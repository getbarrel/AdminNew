<?
include("../../class/database.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');
//print_r($_POST);
//exit;


if($admininfo[company_id] == ""){
	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}

//print_r($_POST);
if($search_searialize_value){
	//	echo $search_searialize_value;
	$unserialize_search_value = unserialize(urldecode($search_searialize_value));
	//print_r ($unserialize_search_value);
	//exit;
	extract($unserialize_search_value);
}
$db = new Database;
$db2 = new Database;


if($mode == "search"){

	switch ($depth){
		case 0:
			$cut_num = 3;
			break;
		case 1:
			$cut_num = 6;
			break;
		case 2:
			$cut_num = 9;
			break;
		case 3:
			$cut_num = 9;
			break;
	}
	//$cut_num = $cut_num -3;

	if($admininfo[admin_level] == 9){
		$where = "where p.id Is NOT NULL   ";//and p.id = r.pid
	}else{
		$where = "where p.id Is NOT NULL  and admin ='".$admininfo[company_id]."'  "; //and p.id = r.pid
	}

	if($pid != ""){
		$where = $where."and p.id = '$pid' ";
	}
	if($company_id != ""){
		$where = $where."and p.admin = '".$company_id."' ";

	}
	if($search_text != ""){
		$where = $where."and p.".$search_type." LIKE '%".trim($search_text)."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}

	if($bs_site != ""){
		$where .= " and p.bs_site = '".$bs_site."'";
	}
//echo $state;
	if($state2 != ""){
		//session_register("state");
		$where = $where." and p.state = ".$state2." ";
	}
	if($brand2 != ""){
		//session_register("brand");
		$where .= " and brand = ".$brand2."";
	}

	if($brand_name != ""){
		$where .= " and p.brand_name LIKE '%".trim($brand_name)."%' ";
	}


	if($cid2 != ""){
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}

}

if ($update_kind == "reserve"){

	if($update_type == 2){// 선택회원일때
		if($reserve_type == 1){
			$reserve_str = ", p.reserve = ".$reserve." ";
		}else if($reserve_type == 2){
			$reserve_str = ", p.reserve = p.reserve - ".$reserve." ";
		}else if($reserve_type == 3){
			$reserve_str = ", p.reserve = p.reserve + ".$reserve." ";
		}
		for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',
			if($admininfo[admin_level] == 9){
				
				$sql = "UPDATE ".TBL_SHOP_PRODUCT." p SET reserve_yn = '".$reserve_yn."' ,  editdate = NOW() $reserve_str Where id = '".$select_pid[$i]."' "; // ,state = ".$state." , disp = '".$disp."'

				$db->query ($sql);
			}
		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 적립금 정보변경이 정상적으로 완료되었습니다.');</script>");
		echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");

	}else{// 검색회원일때
			if($reserve_type == 1){
				$reserve_str = ", p.reserve = ".$reserve." ";
			}else if($reserve_type == 2){
				$reserve_str = ", p.reserve = p.reserve - ".$reserve." ";
			}else if($reserve_type == 3){
				$reserve_str = ", p.reserve = p.reserve + ".$reserve." ";
			}
			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			$sql = "update ".TBL_SHOP_PRODUCT." p left join ".TBL_SNS_PRODUCT_RELATION." r on p.id = r.pid set reserve_yn = '".$reserve_yn."' , editdate = NOW() $reserve_str $where  ";
			//echo $sql;
			$db->query($sql);

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전체상품의 적립금 정보변경이 정상적으로 완료되었습니다.');</script>");//parent.document.location.reload();
			//echo("<script>if(parent.document.frames['act'].location != 'about:blank'){parent.select_update_unloading();parent.document.frames['act'].location.reload();}else{parent.document.location.reload();}</script>");//kbk
			echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");

	}

}


if ($update_kind == "display"){

	if($update_type == 2){// 선택회원일때
		for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',
			if($admininfo[admin_level] == 9){
				$sql = "UPDATE ".TBL_SHOP_PRODUCT." p SET state = ".$c_state." , p.disp = ".$c_disp.",  editdate = NOW() Where id = '".$select_pid[$i]."' "; // ,state = ".$state." , disp = '".$disp."'

				$db->query ($sql);
			}
		}
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 판매/진열 상태 정보변경이 정상적으로 완료되었습니다.');</script>");//
		//echo("<script>if(parent.document.frames['act'].location != 'about:blank'){parent.select_update_unloading();parent.document.frames['act'].location.reload();}else{parent.document.location.reload();}</script>");//kbk
		echo("<script>if(top.document.getElementById('act').src != 'about:blank'){top.select_update_unloading();top.location.reload();}else{top.location.reload();}</script>");

	}else{// 검색회원일때

			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			$sql = "update ".TBL_SHOP_PRODUCT." p left join ".TBL_SNS_PRODUCT_RELATION." r on p.id = r.pid set state = ".$c_state." , p.disp = ".$c_disp.",  editdate = NOW() $where  ";
			//echo $sql;
			$db->query($sql);

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전체상품의 판매/진열 상태 정보변경이 정상적으로 완료되었습니다.');</script>");//parent.document.location.reload();
			//echo("<script>if(parent.document.frames['act'].location != 'about:blank'){parent.select_update_unloading();parent.document.frames['act'].location.reload();}else{parent.document.location.reload();}</script>");//kbk
			echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");

	}

}

if ($update_kind == "category"){
	if($update_type == 2){// 선택회원일때

		if($category_change_type == "1"){ // 카테고리 추가일때
			//print_r($select_pid);
			for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',
				if($admininfo[admin_level] == 9){
					$sql = "select * from ".TBL_SNS_PRODUCT_RELATION." Where pid = '".$select_pid[$i]."' "; // ,state = ".$state." , disp = '".$disp."'
					$db->query ($sql);
					if($db->total > 0){
						$basic = 0;
					}else{
						$basic = 1;
					}
					$sql = "insert into ".TBL_SNS_PRODUCT_RELATION." (rid, cid,pid,disp,basic,insert_yn,regdate) values('','$cid2','".$select_pid[$i]."','1','$basic','Y',NOW())";
					//echo $sql;
					$db->query ($sql);
					$sql = "update ".TBL_SHOP_PRODUCT." set reg_category = 'Y' where id = '".$select_pid[$i]."'";
					$db->query ($sql);
				}
			}
		}else if($category_change_type == "2"){ // 카테고리 변경일때(없으면 추가하기)
			for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',
				if($admininfo[admin_level] == 9){
					// 해당 상품의 존재여부 판단
					$sql = "select * from ".TBL_SNS_PRODUCT_RELATION." r Where pid = '".$select_pid[$i]."' and r.basic = '1'  "; // and r.cid = '$c_cid',state = ".$state." , disp = '".$disp."'
					$db->query ($sql);
					if($db->total){
						$sql = "update ".TBL_SNS_PRODUCT_RELATION." r set r.cid = '$cid2', r.basic = '1'  where pid = '".$select_pid[$i]."' and r.basic = '1'  ";
						$db->query ($sql);
					}else{
						$sql = "insert into ".TBL_SNS_PRODUCT_RELATION." (rid,cid,pid,disp,basic,insert_yn,regdate) values('','$cid2','".$select_pid[$i]."','1','1','Y',NOW())";
						$db->query ($sql);
						$sql = "update ".TBL_SHOP_PRODUCT." set reg_category = 'Y' where id = '".$select_pid[$i]."'";
						$db->query ($sql);
					}
				}
			}
		}else if($category_change_type == "3"){ // 기본 카테고리 변경일때 (기본카테고리 제외 삭제)

			for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',
				if($admininfo[admin_level] == 9){
					// 해당 상품의 존재여부 판단
					$sql = "delete r from ".TBL_SNS_PRODUCT_RELATION." r Where pid = '".$select_pid[$i]."' and r.basic != '1'  ";
					$db->query ($sql);

					$sql = "select * from ".TBL_SNS_PRODUCT_RELATION." r Where pid = '".$select_pid[$i]."' and r.basic = '1'  "; // and r.cid = '$c_cid',state = ".$state." , disp = '".$disp."'
					$db->query ($sql);

					if($db->total){
						$sql = "update ".TBL_SNS_PRODUCT_RELATION." r set r.cid = '$cid2', r.basic = '1'  where pid = '".$select_pid[$i]."' and r.basic = '1'  ";
						$db->query ($sql);
					}else{
						$sql = "insert into ".TBL_SNS_PRODUCT_RELATION." (rid,cid,pid,disp,basic,insert_yn,regdate) values('','$cid2','".$select_pid[$i]."','1','1','Y',NOW())";
						$db->query ($sql);
						$sql = "update ".TBL_SHOP_PRODUCT." set reg_category = 'Y' where id = '".$select_pid[$i]."'";
						$db->query ($sql);
					}
				}
			}
		}
		//exit;
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('선택상품의 카테고리 정보변경이 정상적으로 완료되었습니다.');</script>");
		//echo("<script>if(parent.document.frames['act'].location != 'about:blank'){parent.select_update_unloading();parent.document.frames['act'].location.reload();}else{parent.document.location.reload();}</script>");//kbk
		echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	}else{// 검색회원일때
	//echo $category_change_type;
	$db->debug = true;
		if($category_change_type == "1"){ // 카테고리 추가일때

				if($admininfo[admin_level] == 9){

					$sql = "REPLACE INTO ".TBL_SNS_PRODUCT_RELATION."(rid, cid,pid,disp,basic,insert_yn,regdate)
					select case when (r.cid = '$cid2' and r.basic = '1') then r.rid else '' end rid, '$c_cid' as cid, p.id as pid, '1' as disp,
					IF((r.cid = '011006000000000' and r.basic = '1') ,'1','0')  as basic, 'Y' as insert_yn, IFNULL(r.regdate,NOW())
					from ".TBL_SHOP_PRODUCT." p left join  ".TBL_SNS_PRODUCT_RELATION." r on p.id = r.pid and r.basic = 1
					$where  "; // ,state = ".$state." , disp = '".$disp."'

					$db->query ($sql);

					$sql = "
					update ".TBL_SHOP_PRODUCT." p left join  ".TBL_SNS_PRODUCT_RELATION." r on p.id = r.pid and r.basic = 1
					set reg_category = 'Y'
					$where  "; // ,state = ".$state." , disp = '".$disp."'
					$db->query ($sql);

				}
		}else if($category_change_type == "2"){ // 기본 카테고리 변경일때 (없으면 추가)
					if($admininfo[admin_level] == 9){
						$sql = "REPLACE INTO ".TBL_SNS_PRODUCT_RELATION." (rid, cid,pid,disp,basic,insert_yn,regdate)
						select r.rid, '$cid2' as cid , p.id as pid, '1' as disp, IFNULL(r.basic,'1') as basic, 'Y' as insert_yn, IFNULL(r.regdate,NOW())
						from ".TBL_SHOP_PRODUCT." p left join  ".TBL_SNS_PRODUCT_RELATION." r on p.id = r.pid and r.basic = '1'
						$where  "; // ,state = ".$state." , disp = '".$disp."' //IF(r.cid = '', '$c_cid',IFNULL(r.cid,'$c_cid')) as cid
						//echo $sql;
						$db->query ($sql);

						$sql = "
						update ".TBL_SHOP_PRODUCT." p left join  ".TBL_SNS_PRODUCT_RELATION." r on p.id = r.pid and r.basic = '1'
						set reg_category = 'Y'
						$where  "; // ,state = ".$state." , disp = '".$disp."'
						$db->query ($sql);
					}
		}else if($category_change_type == "3"){ // 기본 카테고리 변경일때 (기본카테고리외 삭제)
				if($admininfo[admin_level] == 9){

					$sql = "delete r
					from ".TBL_SHOP_PRODUCT." p left join  ".TBL_SNS_PRODUCT_RELATION." r on p.id = r.pid and r.basic != '1'
					$where  "; // ,state = ".$state." , disp = '".$disp."'
					//echo $sql."<br>";
					//exit;
					$db->query ($sql);

					$sql = "REPLACE INTO ".TBL_SNS_PRODUCT_RELATION." (rid, cid,pid,disp,basic,insert_yn,regdate)
					select r.rid, '$cid2' as cid, p.id as pid, '1' as disp, IFNULL(r.basic,'1') as basic, 'Y' as insert_yn, IFNULL(r.regdate,NOW())
					from ".TBL_SHOP_PRODUCT." p left join  ".TBL_SNS_PRODUCT_RELATION." r on p.id = r.pid and r.basic = '1'
					$where  "; // ,state = ".$state." , disp = '".$disp."'
					//echo $sql."<br>";
					$db->query ($sql);

					$sql = "
					update ".TBL_SHOP_PRODUCT." p left join  ".TBL_SNS_PRODUCT_RELATION." r on p.id = r.pid and r.basic = '1'
					set reg_category = 'Y'
					$where  "; // ,state = ".$state." , disp = '".$disp."'
					//echo $sql."<br>";
					$db->query ($sql);
					exit;
				}
		}
		//exit;
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색상품의 카테고리 정보변경이 정상적으로 완료되었습니다.');</script>");
		//echo("<script>if(parent.document.frames['act'].location != 'about:blank'){parent.select_update_unloading();parent.document.frames['act'].location.reload();}else{parent.document.location.reload();}</script>");//kbk
		echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");
	}

}



if ($update_kind == "stock"){

	$db = new Database;
echo "<script language='JavaScript' src='/admin/js/prototype.js'></Script>\n
<script language='javascript' src='../_language/language.php'></script>
	<script language='javascript'>
	var goods_detail_link = new Array();";

	if($update_type == 2){// 선택회원일때
		$bs_i=0;
		for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',
			if($admininfo[admin_level] == 9){
				$sql = "select bs_goods_url, bs_site from ".TBL_SHOP_PRODUCT." Where id = '".$select_pid[$i]."' "; // ,state = ".$state." , disp = '".$disp."'
				$db->query ($sql);
				//echo $sql;

				for($j=0;$j < $db->total; $j++){
					//echo "goods_detail_link[".$bs_i."] = '".str_replace("&amp;","&",$goods_detail_unique_links[$bs_i])."';\n";
					$db->fetch($j);
					echo "goods_detail_link[".$bs_i."] = new Array();\n";
					echo "goods_detail_link[".$bs_i."][0] = '".str_replace("&amp;","&",$db->dt[bs_site])."';\n";
					echo "goods_detail_link[".$bs_i."][1] = '".str_replace("&amp;","&",$db->dt[bs_goods_url])."';\n";
					$bs_i++;
				}

			}
		}
		//echo("<script>alert('선택상품의 판매/진열 상태 정보변경이 적상적으로 완료되었습니다.');parent.document.location.reload();</script>");
	}else{// 검색회원일때

			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			$sql = "select bs_goods_url, bs_site from ".TBL_SHOP_PRODUCT." p right join ".TBL_SNS_PRODUCT_RELATION." r on p.id = r.pid and r.basic = 1  $where  ";
			//echo $sql;
			$db->query($sql);

			for($bs_i=0;$bs_i < $db->total;$bs_i++){
				//echo "goods_detail_link[".$bs_i."] = '".str_replace("&amp;","&",$goods_detail_unique_links[$bs_i])."';\n";
				$db->fetch($bs_i);
				echo "goods_detail_link[".$bs_i."] = new Array();\n";
				echo "goods_detail_link[".$bs_i."][0] = '".str_replace("&amp;","&",$db->dt[bs_site])."';\n";
				echo "goods_detail_link[".$bs_i."][1] = '".str_replace("&amp;","&",$db->dt[bs_goods_url])."';\n";
			}

		//	echo("<script>alert('전체상품의 판매/진열 상태 정보변경이 적상적으로 완료되었습니다.');parent.document.location.reload();</script>");
	}
	//print_r($goods_detail_unique_links);
	//exit;




	echo "
	//alert(goods_detail_link.length);
	//for(i=0;i < goods_detail_link.length ;i++){//&& i < 10
	var bs_i = 0;
	buyingservice_goods_reg();

	function buyingservice_goods_reg(){
		//alert(bs_i+'::'+goods_detail_link[bs_i]);
		new Ajax.Request('product_bsgoods.act.php',
		{
			method: 'POST',
			asynchronous: true,
			parameters: 'bs_act=bsgoods_one_update&sc_state=$sc_state&sc_disp=$sc_disp&bs_site='+goods_detail_link[bs_i][0]+'&goods_detail_link='+goods_detail_link[bs_i][1],
			//encoding: 'UTF-8',
			onComplete: function(transport){
				try{
					//alert('등록완료 : '+transport.responseText);
					parent.document.getElementById('select_update_loadingbar').innerHTML = \"<table><tr><td><img src='/admin/images/indicator.gif' border=0 width=32 height=32 align=absmiddle> </td><td>[\"+transport.responseText+\"] <br>\"+goods_detail_link[bs_i][1]+\"</td></tr></table> \";
					//parent.document.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview';
				//	alert(goods_detail_link.length +'>'+ bs_i+'='+(goods_detail_link.length > bs_i));
					bs_i++;
					if(goods_detail_link.length > bs_i){
					//alert(goods_detail_link.length+':::'+bs_i);
						//if(parent.document.search_form.search_status[0].checked){

							setTimeout(\"buyingservice_goods_reg()\",900);

						//}else{
						//	parent.unloading();
						//}
					}else{
						//parent.document.search_form.cid2.value ='$cid2';
						//parent.document.search_form.depth.value ='$depth';
						//getBuyingServiceInfoNextPage();
						alert(language_data['sns_goods_batch.act.php']['A'][language]+transport.responseText);
						parent.select_update_unloading();
						//'재고확인 처리가 완료되었습니다.'
					}
				}catch(e){
					alert(e.message);
				}

			}
		});
	}
	//}
	</script>
	";

/*
	$search_str =  "$PageParamName=".$eval_value;
	$replace_str = "$PageParamName=".(($this_pagenum+1)*$page_size);
	$next_list_url = str_replace($search_str,$replace_str,$list_url);

	echo "
	<script language='javascript'>
	function getBuyingServiceInfoNextPage(){
		parent.unloading();
		if(parent.document.search_form.search_status[0].checked){
			if(parseInt(parent.document.search_form.end.value) >= parseInt(parent.document.search_form.this_pagenum.value)){
				if(parent.document.search_form.this_pagenum.value == '-'){
					parent.document.search_form.this_pagenum.value = 0;
				}else{
					parent.document.search_form.this_pagenum.value = parseInt(parent.document.search_form.this_pagenum.value)+1;
					parent.document.search_form.this_url.value = '$next_list_url';
				}
				//alert(1);
				parent.document.frames['act'].location.href='product_bsgoods.php?bsmode=reg&view=innerview';
				//alert(2);
				parent.checkSearchFrom(parent.document.search_form,'get_goods');
				//alert(3);
			}
		}
	}
	</script>";
*/

}


if ($act == "select_delete"){

	if($update_type == 2){// 선택회원일때
		$bs_i=0;
		for($i=0;$i<count($select_pid);$i++){//disp='".$_POST["disp".$select_pid[$i]]."',
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$select_pid[$i].".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$select_pid[$i].".gif");
			}
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$select_pid[$i].".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$select_pid[$i].".gif");
			}
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$select_pid[$i].".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$select_pid[$i].".gif");
			}
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/mm_".$select_pid[$i].".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/mm_".$select_pid[$i].".gif");
			}
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$select_pid[$i].".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$select_pid[$i].".gif");
			}
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$select_pid[$i].".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$select_pid[$i].".gif");
			}
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$select_pid[$i].".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$select_pid[$i].".gif");
			}

			$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='".$select_pid[$i]."'");
			$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='".$select_pid[$i]."'");
			$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='".$select_pid[$i]."'");
			$db->query("DELETE FROM ".TBL_SNS_PRODUCT_RELATION." WHERE pid='".$select_pid[$i]."'");
			$db->query("DELETE FROM shop_product_buyingservice_priceinfo WHERE pid='".$select_pid[$i]."'");
			//$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE id='".$cpid[$i]."'");



			if($select_pid[$i] && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$select_pid[$i])){
				rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$select_pid[$i]);
			}
		}
		//echo("<script>alert('선택상품의 판매/진열 상태 정보변경이 적상적으로 완료되었습니다.');parent.document.location.reload();</script>");
	}else{// 검색회원일때

			$sql = "select id from shop_product p   $where  ";
			//echo $sql;
			$db->query($sql);

			for($bs_i=0;$bs_i < $db->total;$bs_i++){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$bs_i[$i].".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$bs_i[$i].".gif");
				}
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$bs_i[$i].".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$bs_i[$i].".gif");
				}
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$bs_i[$i].".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$bs_i[$i].".gif");
				}
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$bs_i[$i].".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$bs_i[$i].".gif");
				}
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$bs_i[$i].".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$bs_i[$i].".gif");
				}
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$bs_i[$i].".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$bs_i[$i].".gif");
				}

				$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='".$bs_i[$i]."'");
				$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='".$bs_i[$i]."'");
				$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='".$bs_i[$i]."'");
				$db->query("DELETE FROM ".TBL_SNS_PRODUCT_RELATION." WHERE pid='".$bs_i[$i]."'");
				$db->query("DELETE FROM shop_product_buyingservice_priceinfo WHERE pid='".$bs_i[$i]."'");
				//$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE id='".$cpid[$i]."'");



				if($bs_i[$i] && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$bs_i[$i])){
					rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$bs_i[$i]);
				}
			}

		//	echo("<script>alert('전체상품의 판매/진열 상태 정보변경이 적상적으로 완료되었습니다.');parent.document.location.reload();</script>");
	}
	/*for($i=0;$i<count($cpid);$i++){

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$cpid[$i].".gif");
		}

		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='".$cpid[$i]."'");
		$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='".$cpid[$i]."'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='".$cpid[$i]."'");
		$db->query("DELETE FROM ".TBL_SNS_PRODUCT_RELATION." WHERE pid='".$cpid[$i]."'");
		$db->query("DELETE FROM shop_product_buyingservice_priceinfo WHERE pid='".$cpid[$i]."'");
		//$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE id='".$cpid[$i]."'");



		if($cpid[$i] && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$cpid[$i])){
			rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$cpid[$i]);
		}


	}*/

		//echo "<script language='javascript'>parent.document.location.reload();</script>";
		//echo("<script>if(parent.document.frames['act'].location != 'about:blank'){parent.select_update_unloading();parent.document.frames['act'].location.reload();}else{parent.document.location.reload();}</script>");//kbk
		echo("<script>if(parent.document.getElementById('act').src != 'about:blank'){parent.select_update_unloading();parent.location.reload();}else{parent.location.reload();}</script>");


	//header("Location:../product_list.php");
}


?>