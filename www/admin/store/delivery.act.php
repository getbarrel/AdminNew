<?
include("../../class/layout.class");

$db = new Database;
$db2 = new Database;

if($act == 'group_insert'){
	if(checkDupDt_ix($dt_ix)){
		echo("<script>alert('이미 다른 그룹에 등록되어있는 템플릿입니다');</script>");
		echo("<script>parent.document.location.href = 'delivery.php?info_type=".$info_type."';</script>");
		exit;
	}

	$sql = "insert into shop_delivery_group (name, state, regdate) values ('".$name."', '".$state."', NOW())";
	$db->query($sql);

	$db->query("SELECT g_ix FROM shop_delivery_group WHERE g_ix=LAST_INSERT_ID()");
	$db->fetch();
	$g_ix = $db->dt[g_ix];

	$check_duplicate = false;
	foreach($dt_ix as $k){
		if($rep == $k){
			$rep_v = "Y";
		}else{
			$rep_v = "N";
		}

		$sql = "insert into shop_delivery_relation (rep, g_ix, dt_ix, regdate) values ('".$rep_v."', '".$g_ix."', '".$k."', NOW())";
		$db->query($sql);
	}

	echo("<script>parent.document.location.href = 'delivery.php?info_type=".$info_type."';</script>");
	exit;
}

if($act == 'group_update'){
	if(checkDupDt_ix($dt_ix, $g_ix)){
		echo("<script>alert('이미 다른 그룹에 등록되어있는 템플릿입니다');</script>");
		echo("<script>parent.document.location.href = 'delivery.php?info_type=".$info_type."';</script>");
		exit;
	}

	$sql = "update shop_delivery_group set state='".$state."', name='".$name."' where g_ix='".$g_ix."'";
	$db->query($sql);

	//삭제
	$db->query("SELECT * FROM shop_delivery_relation WHERE g_ix='".$g_ix."'");
	$before = $db->fetchall("object");
	
	foreach($before as $k => $v){
		if(! in_array($v[dt_ix], $dt_ix)){
			$sql = "delete from shop_delivery_relation where g_ix='".$g_ix."' and dt_ix='".$v[dt_ix]."'";
			$db->query($sql);
		}
	}

	//수정
	$check_duplicate = false;
	foreach($dt_ix as $k){
		if($rep == $k){
			$rep_v = "Y";
		}else{
			$rep_v = "N";
		}

		$db->query("SELECT g_ix FROM shop_delivery_relation WHERE g_ix='".$g_ix."' and dt_ix='".$k."'");
		$db->fetch();

		if($db->total > 0){
			$sql = "update shop_delivery_relation set rep='".$rep_v."' where g_ix='".$g_ix."' and dt_ix='".$k."'";
			$db->query($sql);
		}else{
			$sql = "insert into shop_delivery_relation (rep, g_ix, dt_ix, regdate) values ('".$rep_v."', '".$g_ix."', '".$k."', NOW())";
			$db->query($sql);
		}
	}

	echo("<script>parent.document.location.href = 'delivery.php?info_type=".$info_type."';</script>");
	exit;
}

if($act == 'group_delete'){
	$sql = "delete from shop_delivery_group where g_ix = '$g_ix'";
	$db->query($sql);

	$sql = "delete from shop_delivery_relation where g_ix = '$g_ix'";
	$db->query($sql);
	echo "Y";
	exit;
}

if($act == "template_update"){	//배송정책 변경

	if(is_array($code_ix)) {
		$delivery_company=implode(",",$code_ix);		//셀러별 택배설정 코드값
	}
	$sql = "select * from common_seller_delivery where company_id = '".$company_id."'";
	$db->query($sql);

	if($db->total){

		$sql = "update common_seller_delivery set
					delivery_policy = '".$delivery_policy."',
					delivery_product_policy = '".$delivery_product_policy."',
					delivery_company='".$delivery_company."',
					delivery_deadline_yn='".$delivery_deadline_yn."',
					delivery_deadline_hour='".$delivery_deadline_hour."',
					delivery_deadline_minute='".$delivery_deadline_minute."',
					goodsflow_return_yn='".$goodsflow_return_yn."',
					goodsflow_policy_type='".$goodsflow_policy_type."'
				where
					company_id = '".$company_id."'
					";
		$db->query($sql);
		echo "<script>alert('정상적으로 변경 되었습니다..');</script>";
	}else{
		$sql = "insert into common_seller_delivery (company_id, delivery_policy, delivery_product_policy, delivery_company) values ('".$company_id."','".$delivery_policy."','".$delivery_product_policy."','".$delivery_company."')";
		$db->query($sql);
		echo "<script>alert('정상적으로 등록 되었습니다..');</script>";
	}

	echo("<script>parent.document.location.reload();</script>");
	exit;
}


if($act == 'insert'){
	
	$company_id = trim($_REQUEST[company_id]);	//company_id
	$delivery_type = trim($_REQUEST[delivery_type]);	//주소 타입 F 출고지 E 반품 교환지 V 방문수령지
	$mall_ix = trim($_REQUEST[mall_ix]);	// 몰 아이디 
	$addr_name = trim($_REQUEST[addr_name]);	// 출고지명
	$person_name = trim($_REQUEST[person_name]);	// 담당자명
	$addr_phone = trim($_REQUEST[addr_phone_1]."-".$_REQUEST[addr_phone_2]."-".$_REQUEST[addr_phone_3]);	// 전화번호 
	$addr_mobile = trim($_REQUEST[addr_mobile_1]."-".$_REQUEST[addr_mobile_2]."-".$_REQUEST[addr_mobile_3]);	// 전화번호 
	$zip_code = trim($_REQUEST[com_zip]);	// zip 코드

	$com_addr1 = trim($_REQUEST[com_addr1]);	// 주소1
	$com_addr2 = trim($_REQUEST[com_addr2]);	// 주소2

	$basic_addr_use = trim($_REQUEST[basic_addr_use]);	// 기본주소 사용여부
	$code = trim($_REQUEST[code]);	// 코드
	
	if($basic_addr_use == "Y"){
	
		$sql = "
				select
					addr_ix
				from
					shop_delivery_address
				where
					basic_addr_use = 'Y'
					and mall_ix = '$mall_ix'
					and company_id = '$company_id'
					and delivery_type = '$delivery_type'
		";
		$db2->query($sql);
		$db2->fetch();
		$addr_ix = $db2->dt[addr_ix];

		$up_sql = "
			update shop_delivery_address set
				basic_addr_use = 'N'
			where
				addr_ix = '$addr_ix'";

		$db2->query($up_sql);
	}

	$sql = "insert into shop_delivery_address set
				company_id = '$company_id',
				delivery_type = '$delivery_type',
				mall_ix = '$mall_ix',
				addr_name = '$addr_name',
				person_name = '$person_name',
				addr_phone = '$addr_phone',
				addr_mobile = '$addr_mobile',
				zip_code = '$zip_code',
				address_1 = '$com_addr1',
				address_2 = '$com_addr2',
				basic_addr_use = '$basic_addr_use',
				is_delivery_use = '$is_delivery_use',
				code = '$code',
				regdate = NOW()";
	
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'delivery.php?info_type=".$info_type."';</script>");

}

if ($act == "update"){

	if($info_type == 'seller_setup'){

		foreach ($_POST as $key => $val) {
			if($key != "act" && $key != "mall_ix" && $key != "x" && $key != "y"){ 
				if($db->dbms_type=='oracle'){
					
					$sql = "delete shop_mall_config where 
							mall_ix = '".$_POST[mall_ix]."' and
							config_name ='".$key."' and
							config_value ='".$val."'  ";
					$db->query($sql);
					$sql ="INSERT INTO shop_mall_config (mall_ix,config_name,config_value) values ('".$_POST[mall_ix]."','".$key."','".$val."')";
					
				}else{

						$sql = "replace into shop_mall_config set
									config_name = '".$key."',
									config_value = '".$val."',
									mall_ix = '".$_POST[mall_ix]."'
									";
				}
				$db->query($sql);
			}
		}
		$account_info = $_REQUEST[account_info];
		$ac_delivery_type = $_REQUEST[ac_delivery_type];
		$ac_expect_date = $_REQUEST[ac_expect_date];
		$ac_term_div = $_REQUEST[ac_term_div];
		$ac_term_date1 = $_REQUEST[ac_term_date1];
		$ac_term_date2 = $_REQUEST[ac_term_date2];

		$account_type = $_REQUEST[account_type];
		$account_method = $_REQUEST[account_method];
		$wholesale_commission = $_REQUEST[wholesale_commission];
		$commission = $_REQUEST[commission];

		$seller_grant_use = $_REQUEST[seller_grant_use];
		$grant_setup_price = $_REQUEST[grant_setup_price];
		$ac_grant_price = $_REQUEST[ac_grant_price];
		$account_div = $_REQUEST[account_div];

		$sql = "select count(company_id) as cnt from common_seller_delivery where company_id = '".$company_id."'";
		$db->query($sql);
		$db->fetch();
		$cnt = $db->dt[cnt];

		if($cnt > 0){	//update
			$sql = "update common_seller_delivery set
						account_info = '".$account_info."',
						ac_delivery_type = '".$ac_delivery_type."',
						ac_expect_date = '".$ac_expect_date."',
						ac_term_div = '".$ac_term_div."',
						ac_term_date1 = '".$ac_term_date1."',
						ac_term_date2 = '".$ac_term_date2."',
						account_type = '".$account_type."',
						account_method = '".$account_method."',
						wholesale_commission = '".$wholesale_commission."',
						commission = '".$commission."',
						seller_grant_use = '".$seller_grant_use."',
						grant_setup_price = '".$grant_setup_price."',
						ac_grant_price = '".$ac_grant_price."',
						account_div = '".$account_div."',
						et_ix = '".$et_ix."',
						econtract_commission = '".$electron_contract_commission."'
					where
						company_id = '".$company_id."'";
			$db->query($sql);
		
		}else{
			$sql = "insert into common_seller_delivery set
						company_id = '".$company_id."',
						account_info = '".$account_info."',
						ac_delivery_type = '".$ac_delivery_type."',
						ac_expect_date = '".$ac_expect_date."',
						ac_term_div = '".$ac_term_div."',
						ac_term_date1 = '".$ac_term_date1."',
						ac_term_date2 = '".$ac_term_date2."',
						account_type = '".$account_type."',
						account_method = '".$account_method."',
						wholesale_commission = '".$wholesale_commission."',
						commission = '".$commission."',
						seller_grant_use = '".$seller_grant_use."',
						grant_setup_price = '".$grant_setup_price."',
						ac_grant_price = '".$ac_grant_price."',
						account_div = '".$account_div."',
						et_ix = '".$et_ix."',
						econtract_commission = '".$electron_contract_commission."'";

			$db->query($sql);
		}

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('수수료 설정이 정상적으로 처리되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'delivery.php?info_type=".$info_type."';</script>");
	
	}else{
		$company_id = trim($_REQUEST[company_id]);	//company_id
		$delivery_type = trim($_REQUEST[delivery_type]);	//주소 타입 F 출고지 E 반품 교환지 V 방문수령지
		$mall_ix = trim($_REQUEST[mall_ix]);	// 몰 아이디 
		$addr_name = trim($_REQUEST[addr_name]);	// 출고지명
		$person_name = trim($_REQUEST[person_name]);	// 담당자명
		$addr_phone = trim($_REQUEST[addr_phone_1]."-".$_REQUEST[addr_phone_2]."-".$_REQUEST[addr_phone_3]);	// 전화번호 
		$addr_mobile = trim($_REQUEST[addr_mobile_1]."-".$_REQUEST[addr_mobile_2]."-".$_REQUEST[addr_mobile_3]);	// 전화번호 
		$zip_code = trim($_REQUEST[com_zip]);	// zip 코드

		$com_addr1 = trim($_REQUEST[com_addr1]);	// 주소1
		$com_addr2 = trim($_REQUEST[com_addr2]);	// 주소2

		$basic_addr_use = trim($_REQUEST[basic_addr_use]);	// 기본주소 사용여부
		$code = trim($_REQUEST[code]);	// 코드
		
		if($basic_addr_use == "Y"){
		
			$sql = "select
						addr_ix
					from
						shop_delivery_address
					where
						basic_addr_use = 'Y'
						and mall_ix = '$mall_ix'
						and company_id = '$company_id'
						and delivery_type = '$delivery_type'
			";
			$db2->query($sql);
			$db2->fetch();
			$addr_ix = $db2->dt[addr_ix];

			$up_sql = "
				update shop_delivery_address set
					basic_addr_use = 'N'
				where
					addr_ix = '$addr_ix'
			";

			//echo "$up_sql";exit;
			$db2->query($up_sql);
		}

		$sql = "update shop_delivery_address set
					delivery_type = '$delivery_type',
					addr_name = '$addr_name',
					person_name = '$person_name',
					addr_phone = '$addr_phone',
					addr_mobile = '$addr_mobile',
					zip_code = '$zip_code',
					address_1 = '$com_addr1',
					address_2 = '$com_addr2',
					basic_addr_use = '$basic_addr_use',
					is_delivery_use = '$is_delivery_use',
					code = '$code',
					editdate = NOW()
				where
					addr_ix = '".$_REQUEST[addr_ix]."'
		";
		
		$db->query($sql);

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'delivery.php?info_type=".$info_type."';</script>");
	
	}

}


if($act == "delivery_update"){
		$sql = "update ".TBL_SHOP_CODE." set
						code_name = '$code_name' , code_etc1 = '$code_etc1' ,code_etc3 = '$code_etc3' , code_etc4 = '$code_etc4' ,disp = '$disp'
						where code_gubun='02' and code_ix = '".$code_ix."' ";
		//echo $sql;
		$db->query($sql);

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();window.close();//parent.mybox.hideIbox(parent.mybox.btnClose);</script>");
}

if($act == "delivery_insert"){
		//$sql = "insert ".TBL_SHOP_CODE." set code_name = '$code_name' , code_etc1 = '$code_etc1' , disp = '$disp' where code_gubun='02'  ";
		$sql = "select max(CEILING(code_ix))+1 as code_ix from ".TBL_SHOP_CODE." where code_gubun=2  ";
		//echo $sql;
		$db->query($sql);
		$db->fetch();
		$code_ix = $db->dt[code_ix];
		//echo $code_ix;
		//exit;
		$sql = "insert into shop_code (code_gubun,code_ix,code_name,code_etc1,code_etc2,code_etc3,code_etc4,disp)
						values
						('02','$code_ix','$code_name','$code_etc1','$code_etc2','$code_etc3','$code_etc4','$disp')";
		//echo $sql;
		//exit;
		$db->query($sql);

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력되었습니다.');parent.window.close();//parent.mybox.hideIbox(parent.mybox.btnClose);</script>");
}

if($act == "delivery_delete"){
		$sql = "delete from ".TBL_SHOP_CODE." where code_gubun='02' and code_ix = '".$code_ix."' ";
		//echo $sql;
		$db->query($sql);

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');parent.opener.document.location.reload();parent.window.close();//parent.mybox.hideIbox(parent.mybox.btnClose);</script>");
}

if($act == "delete"){
	
	$sql = "delete from shop_delivery_address where  addr_ix = '$addr_ix'";

	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'delivery.php?info_type=".$info_type."';</script>");
}


function returnImagePath($str){
	$IMG = split(" ",$str);

	for($i=0;$i<count($IMG);$i++){
		//echo substr_count($IMG[$i],"src");
			if(substr_count($IMG[$i],"src=") > 0){
				$mstring = str_replace("src=","",$IMG[$i]);
				return str_replace("\"","",$mstring);
			}
	}
}

function ClearText($str){
	return str_replace(">","",$str);
}


function returnFileName($filestr){
	$strfile = split("/",$filestr);

	return str_replace("%20","",$strfile[count($strfile)-1]);
	//return count($strfile);

}

function rmdirr($target,$verbose=false)
// removes a directory and everything within it
{
$exceptions=array('.','..');
if (!$sourcedir=@opendir($target))
   {
   if ($verbose)
       echo '<strong>Couldn&#146;t open '.$target."</strong><br />\n";
   return false;
   }
while(false!==($sibling=readdir($sourcedir)))
   {
   if(!in_array($sibling,$exceptions))
       {
       $object=str_replace('//','/',$target.'/'.$sibling);
       if($verbose)
           echo 'Processing: <strong>'.$object."</strong><br />\n";
       if(is_dir($object))
           rmdirr($object);
       if(is_file($object))
           {
           $result=@unlink($object);
           if ($verbose&&$result)
               echo "File has been removed<br />\n";
           if ($verbose&&(!$result))
               echo "<strong>Couldn&#146;t remove file</strong>";
           }
       }
   }
closedir($sourcedir);
if($result=@rmdir($target))
   {
   if ($verbose)
       echo "Target directory has been removed<br />\n";
   return true;
   }
if ($verbose)
   echo "<strong>Couldn&#146;t remove target directory</strong>";
return false;
}

function checkDupDt_ix($dt_ix, $g_ix=""){
	global $db;

	if(count($dt_ix) > 0){
		foreach($dt_ix as $k => $v){
			$db->query("SELECT g_ix FROM shop_delivery_relation WHERE ".(! empty($g_ix) ? "g_ix != '".$g_ix."' and " : "")."dt_ix='".$v."'");
			$db->fetch();
			if($db->total > 0){
				return true;
			}
		}
	}

	return false;
}
?>
