<?
include("../class/layout.class");

//print_r($_POST);

if($act == "brand_list"){
	
	$db = new Database;

	/*
	if($act == "cookie_setting"){
		$sql = "update inventory_config Set 
				order_excel_info1 = '".serialize($order_excel_info1)."',
				order_excel_info2='".serialize($order_excel_info2)."',
				order_excel_checked='".serialize($colums)."'
				where company_id = '".$admininfo[company_id]."'";
	}else{
				$sql = "update ".TBL_COMMON_SELLER_DETAIL." Set 
				delivery_excel_info1 = '".serialize($order_excel_info1)."',
				delivery_excel_info2='".serialize($order_excel_info2)."',
				delivery_excel_checked='".serialize($colums)."'
				where company_id = '".$admininfo[company_id]."'";

	}
	*/

	$sql = "REPLACE INTO inventory_config set charger_ix='".$admininfo[charger_ix]."' , conf_name='brand_".$info_type."',conf_val='".serialize($inventory_excel_info)."'  ";

	$db->query($sql);

	$sql = "REPLACE INTO inventory_config set charger_ix='".$admininfo[charger_ix]."' , conf_name='brand_check_".$info_type."',conf_val='".serialize($colums)."'  ";
	//echo $sql;
	$db->query($sql);

	
/*	
	setcookie("_excel_sortlist",serialize($excel_sortlist), time()+3600000,"/",$HTTP_HOST);
	setcookie("_excel_sortlist2",serialize($excel_sortlist2), time()+3600000,"/",$HTTP_HOST);
	setcookie("_colums_checked",serialize($colums), time()+3600000,"/",$HTTP_HOST);
	*/
//	print_r($excel_sortlist);
//	print_r($excel_sortlist2);
//	print_r($colums);
	//echo "주문내역 저장하기 설정정보가 정상적으로 저장되었습니다.";
	if($act == "cookie_setting"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('엑셀정보 설정이 정상적으로 저장되었습니다.');parent.document.location.reload();</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('엑셀정보 설정이 정상적으로 저장되었습니다.');parent.document.location.reload();</script>";
	}
	exit;
}

if($act == "origin_list"){
	
	$db = new Database;

	$sql = "REPLACE INTO inventory_config set charger_ix='".$admininfo[charger_ix]."' , conf_name='origin_".$info_type."',conf_val='".serialize($inventory_excel_info)."'  ";

	$db->query($sql);

	$sql = "REPLACE INTO inventory_config set charger_ix='".$admininfo[charger_ix]."' , conf_name='origin_check_".$info_type."',conf_val='".serialize($colums)."'  ";
	//echo $sql;
	$db->query($sql);

	if($act == "cookie_setting"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('엑셀정보 설정이 정상적으로 저장되었습니다.');parent.document.location.reload();</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('엑셀정보 설정이 정상적으로 저장되었습니다.');parent.document.location.reload();</script>";
	}
	exit;
}

if($act == "company_list"){
	
	$db = new Database;

	$sql = "REPLACE INTO inventory_config set charger_ix='".$admininfo[charger_ix]."' , conf_name='company_".$info_type."',conf_val='".serialize($inventory_excel_info)."'  ";

	$db->query($sql);

	$sql = "REPLACE INTO inventory_config set charger_ix='".$admininfo[charger_ix]."' , conf_name='company_check_".$info_type."',conf_val='".serialize($colums)."'  ";
	//echo $sql;
	$db->query($sql);

	if($act == "cookie_setting"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('엑셀정보 설정이 정상적으로 저장되었습니다.');parent.document.location.reload();</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('엑셀정보 설정이 정상적으로 저장되었습니다.');parent.document.location.reload();</script>";
	}
	exit;
}


if($act == "delete_setting" ){
	$db = new Database;
	
	if($info_type == "list"){
		$type = "brand";
	}else if($info_type == "origin_list"){
		$type = "origin";
	}else if($info_type == "company_list"){
		$type = "company";
	}

	$sql = "delete from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='".$type."_check_".$info_type."'   ";
	//echo $sql;
	$db->query($sql);

	echo "<script language='javascript' src='../js/message.js.php'></script><script>alert(' 정상적으로 삭제되었습니다.');parent.document.location.reload();</script>";

}

if($act == "default_setting"){
	//setcookie("_excel_sortlist","", time()-100,"/",$HTTP_HOST);
	//setcookie("_excel_sortlist2","", time()-100,"/",$HTTP_HOST);
	//setcookie("_colums_checked","", time()-100,"/",$HTTP_HOST);
	
	echo "<script>parent.document.location.reload();</script>";
}



?>
