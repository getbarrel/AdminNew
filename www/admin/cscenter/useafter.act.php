<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");


if(!$admininfo){
	echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['common']['C'][language]);</script>");//'로그인 하신후에 사용하실수  있습니다.'
	exit;
}

$db = new Database;


if ($act == "insert"){
	$sql = "insert into shop_product_after (bbs_div, mem_ix,bbs_subject,bbs_name,bbs_id,bbs_contents,bbs_hidden, bbs_file_1,bbs_file_2,bbs_file_3,bbs_file_4,bbs_file_5, pid,pname,brand,oid,company_id,is_best, ip_addr, regdate, valuation_goods, valuation_delivery, option_name)
			values
			('$bbs_div','".$mem_ix."','$bbs_subject','$bbs_name','$bbs_id','$bbs_contents','$bbs_hidden','$bbs_file_1_name','$bbs_file_2_name','$bbs_file_3_name','$bbs_file_4_name','$bbs_file_5_name','$pid','$pname','$brand','$oid','$company_id','$is_best','".$_SERVER["REMOTE_ADDR"]."', NOW(), '$valuation_goods', '$valuation_delivery', '$option_name')";
	$db->query($sql);

	$db->query("Select bbs_ix from shop_product_after where bbs_ix = LAST_INSERT_ID()");
	$db->fetch(0);
	$bbs_ix = (int)$db->dt[bbs_ix];

	$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/product_after";

	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path, 0777);
	}
	
	if(!is_dir($path."/".$bbs_ix)){
		mkdir($path."/".$bbs_ix, 0777);
		chmod($path."/".$bbs_ix, 0777);
	}

	if(is_dir($path."/".$bbs_ix)){
		if ($bbs_file_1_size > 0){
			move_uploaded_file($bbs_file_1, $path."/".$bbs_ix."/".$bbs_file_1_name);
		}

		if ($bbs_file_2_size > 0){
			move_uploaded_file($bbs_file_2, $path."/".$bbs_ix."/".$bbs_file_2_name);
		}

		if ($bbs_file_3_size > 0){
			move_uploaded_file($bbs_file_3, $path."/".$bbs_ix."/".$bbs_file_3_name);
		}

		if ($bbs_file_4_size > 0){
			move_uploaded_file($bbs_file_4, $path."/".$bbs_ix."/".$bbs_file_4_name);
		}

		if ($bbs_file_5_size > 0){
			move_uploaded_file($bbs_file_5, $path."/".$bbs_ix."/".$bbs_file_5_name);
		}
	}
	
	if($mmode == "pop"){
		echo("<script>alert('상품 후기가 정상적으로 등록되었습니다');self.close();opener.document.location.href='./useafter.list.php'</script>");
	}else{
		echo("<script>alert('상품 후기가 정상적으로 등록되었습니다');top.document.location.href='./useafter.list.php'</script>");
	}
	exit;
}

if ($act == "delete"){
	$db->query("delete from shop_product_after where bbs_ix ='$bbs_ix' ");
	$db->query("delete from shop_product_after_comment where bbs_ix ='$bbs_ix' ");

	if($mmode == "pop"){
		echo("<script>alert('정상적으로 삭제 되었습니다.');opener.document.location.reload();self.close();</script>");
	}else{
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>");
	}
	exit;
}

if($act == "update"){

	$sql = "update shop_product_after set is_best = '".$is_best."', bbs_hidden='".$bbs_hidden."' where bbs_ix = '".$bbs_ix."'";
	$db->query($sql);

	echo("<script>alert('상품 후기가 정상적으로 수정되었습니다');self.close();</script>");
	exit;
}

if($act == "update_cmt"){
	$sql = "update shop_product_after_comment set cmt_contents = '".$cmt_contents."' where cmt_ix = '".$cmt_ix."'";
	$db->query($sql);

	echo("<script>top.document.location.href='./useafter.detail.php?bbs_ix=".$bbs_ix."'</script>");
	exit;
}

if($act == "insert_cmt"){
	$sql = "update shop_product_after set bbs_re_cnt = bbs_re_cnt+1 where bbs_ix = '".$bbs_ix."'";
	$db->query($sql);

	$db->query("insert into shop_product_after_comment (bbs_ix, mem_ix, cmt_name, cmt_contents, cmt_ip_addr, regdate) 
												values('".$bbs_ix."','".$mem_ix."','".$cmt_name."','".$cmt_contents."','".$_SERVER['REMOTE_ADDR']."',NOW())");
	echo("<script>top.document.location.href='./useafter.detail.php?bbs_ix=".$bbs_ix."'</script>");
	exit;
}

if($act == "delete_cmt"){
	$sql = "update shop_product_after set bbs_re_cnt = bbs_re_cnt-1 where bbs_ix = '".$bbs_ix."'";
	$db->query($sql);

	$sql = "delete from shop_product_after_comment where cmt_ix = '".$cmt_ix."'";
	$db->query($sql);

	echo("<script>top.document.location.href='./useafter.detail.php?bbs_ix=".$bbs_ix."'</script>");
	exit;
}

?>