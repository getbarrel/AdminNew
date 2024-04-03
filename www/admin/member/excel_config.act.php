<?
include("../class/layout.class");

//print_r($_POST);

if($act == "deposit_info_excel"){	//적립금 사용내역
	$conf_name = "deposit_info_".$info_type;
	$conf_name_check = "check_deposit_info_".$info_type;

}else if($act == "member_list_excel"){	//전체회원리스트 
	$conf_name = "member_list_".$info_type;
	$conf_name_check = "check_member_list_".$info_type;
}

$db = new Database;
$sql = "REPLACE INTO inventory_config set charger_ix='".$admininfo[charger_ix]."' , conf_name='".$conf_name."',conf_val='".serialize($inventory_excel_info)."'  ";
$db->query($sql);

$sql = "REPLACE INTO inventory_config set charger_ix='".$admininfo[charger_ix]."' , conf_name='".$conf_name_check."',conf_val='".serialize($colums)."'  ";
$db->query($sql);

//echo "주문내역 저장하기 설정정보가 정상적으로 저장되었습니다.";
if($act == "cookie_setting"){
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('엑셀정보 설정이 정상적으로 저장되었습니다.');parent.document.location.reload();</script>";
}else{
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('엑셀정보 설정이 정상적으로 저장되었습니다.');parent.document.location.reload();</script>";
}
exit;


if($act == "delete_setting" ){
	$db = new Database;

	$sql = "delete from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='member_edit_".$info_type."'   ";
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
