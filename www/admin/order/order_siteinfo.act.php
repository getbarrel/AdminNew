<?
include("../../class/database.class");

session_start();

$db = new Database;

if ($act == "update"){
	
	$sql = "update shop_order_siteinfo set
				orgin_oid='".$orgin_oid."',
				orgin_tracking_no='".$orgin_tracking_no."'
				where  oid='".$oid."' and od_ix='".$od_ix."'";
	
	$db->query($sql);
    
   if($change_status){

	$sql = "update shop_order_detail set status='".$change_status."' where  oid='".$oid."' and od_ix='".$od_ix."'";
	$db->query($sql);

	   $sql = "insert into ".TBL_SHOP_ORDER_STATUS." 
				(os_ix, oid, pid, status, status_message, admin_message, company_id,quick,invoice_no, regdate ) 
				values 
				('','".$oid."','".$pid."','".$change_status."',' 사이트 정보 변경 후 상태변경 orgin_oid : ".$orgin_oid." , orgin_tracking_no : ".$orgin_tracking_no." ','','".$admininfo[company_id]."','','',NOW()) ";
		$db->sequences = "SHOP_ORDER_STATUS_SEQ";
		$db->query($sql);
   }
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");

}

?>