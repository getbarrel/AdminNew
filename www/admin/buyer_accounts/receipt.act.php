<?
include_once("../class/layout.class");

$db = new Database;

if($act == "receipt_initialization"){

	$sql = "update  receipt  set receipt_yn = 'Y' where order_no = '".$oid."' ";
	$db->query($sql);	

	$sql = "delete from  receipt_result  where oid = '".$oid."' ";
	$db->query($sql);	

	echo "<script>alert('정상적으로 처리되었습니다.');top.location.reload();</script>";
	exit;
}


if($act == "receipt_cancel"){

	$sql = "update  shop_order_payment  set receipt_yn = 'N' where oid = '".$oid."' and receipt_yn = 'Y'";
	$db->query($sql);

	$sql = "delete from  receipt  where order_no = '".$oid."' ";
	$db->query($sql);

	echo "<script>alert('정상적으로 처리되었습니다.');top.location.reload();</script>";
	exit;
}

?>