<?
include("../class/layout.class");

$db = new Database;

if($act == "cash_insert"){
	$db->sequences = "SHOP_CASH_INFO_SEQ";
	$db->query("insert into shop_cash_info(c_ix,company_id,ac_ix,cash,status,etc,regdate) values('','$company_id','$ac_ix','$cash','$status','$etc',NOW())");
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('캐쉬정보가 정상적으로 등록되었습니다.');parent.document.location.reload();</script>");
}

if($act == "cash_delete"){
	$db->query("delete from shop_cash_info where c_ix ='$c_ix' and company_id ='$company_id' ");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('캐쉬정보가 정상적으로 삭제되었습니다.');parent.document.location.reload();</script>");
}

?>