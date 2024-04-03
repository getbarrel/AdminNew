<?
include("../class/layout.class");



$db = new MySQL;

if ($act == "insert")
{

	if($is_use=="1"){
		$sql="update global_customs set is_use='0' where is_use='1'";
		$db->query($sql);
	}

	$sql = "insert into global_customs(customs_ix,shipping_transport_costs,domestic_delivery_costs,bond_warehouse_costs,port_costs,storage_costs,is_use,regdate)				
				values
				('','$shipping_transport_costs','$domestic_delivery_costs','$bond_warehouse_costs','$port_costs','$storage_costs','$is_use',NOW())
				";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('통관시 비용가 정상적으로 등록되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){
	
	if($is_use=="1"){
		$sql="update global_customs set is_use='0' where is_use='1'";
		$db->query($sql);
	}

	$sql = "update global_customs set				
				shipping_transport_costs='".$shipping_transport_costs."',
				domestic_delivery_costs='".$domestic_delivery_costs."',
				bond_warehouse_costs='".$bond_warehouse_costs."',
				port_costs='".$port_costs."',
				storage_costs='".$storage_costs."',
				is_use='".$is_use."'
				where customs_ix='".$customs_ix."' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('통관시 비용가 정상적으로 수정되었습니다.');</script>");
	//echo("<script>location.href = 'addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){

	$sql = "delete from global_customs where customs_ix='$customs_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert2('통관시 비용가 정상적으로 삭제되었습니다.');</script>");
	//echo("<script>document.location.href='addressbook_group.php?mmode=$mmode';</script>");
	echo("<script>parent.document.location.reload();</script>");
}

 
?>
