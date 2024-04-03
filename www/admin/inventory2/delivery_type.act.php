<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{

	$sql = "select * from  inventory_delivery_type where delivery_type_code = '$delivery_type_code' ";
	$db->query($sql);
	if($db->total){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('출고형태코드 정보가 이미 등록되어 있습니다. 확인후 다른 코드로 입력해주세요');</script>");
	}else{
		$sql = "insert into inventory_delivery_type (delivery_type,delivery_type_code,disp,regdate) values('$delivery_type','$delivery_type_code','$disp',NOW())";
		$db->query($sql);
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('출고형태 정보가 정상적으로 등록되었습니다.');</script>");
		echo("<script>parent.document.location.href='delivery_type.php?mmode=$mmode';</script>");
	}
}


if ($act == "update"){
	
	$sql = "select * from  inventory_delivery_type where delivery_type_code = '$delivery_type_code' and dt_ix NOT IN ('$dt_ix') ";
	$db->query($sql);
	if($db->total){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('출고형태코드 정보가 이미 등록되어 있습니다. 확인후 다른 코드로 입력해주세요');</script>");
	}else{

		$sql = "update inventory_delivery_type set delivery_type='$delivery_type',delivery_type_code='$delivery_type_code',disp='$disp' where dt_ix='$dt_ix' ";
		
		$db->query($sql);

		

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('출고형태 정보가 정상적으로 수정되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'delivery_type.php?mmode=$mmode';</script>");
	}
}

if ($act == "delete"){
	
	$sql = "delete from inventory_delivery_type where dt_ix='$dt_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('출고형태 정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.href='delivery_type.php?mmode=$mmode';</script>");
}

?>
