<?
//print_r($_POST);
//exit;
include("../../class/database.class");
$db = new Database;
if($act == "insert"){

	$place_tel = $place_phone1."-".$place_phone2."-".$place_phone3;
	$place_fax = $place_fax1."-".$place_fax2."-".$place_fax3;

	$sql = "select IFNULL(max(exit_order),0) as exit_order from inventory_place_info ";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		$exit_order = $db->dt[exit_order];
	}else{
		$exit_order = '1';
	}
	$sql = "insert into inventory_place_info 
				(place_type, place_name,place_tel,place_fax,place_msg, return_position, disp, exit_order,regdate) 
				values
				('$place_type','$place_name','$place_tel','$place_fax','$place_msg','$return_position','$disp','$exit_order',NOW())";

	$db->query($sql);

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('보관장소 등록이 완료 되었습니다.');parent.document.location.href='place_list.php'</script>";
}

if($act == "delete"){
	$db->query("delete from inventory_place_info where pi_ix = '".$pi_ix."'");
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('보관장소가 정상적으로 삭제 되었습니다.');parent.document.location.href='place_list.php'</script>";
}

if($act == "update"){
	$place_tel = $place_phone1."-".$place_phone2."-".$place_phone3;
	$place_fax = $place_fax1."-".$place_fax2."-".$place_fax3;


	$sql = "update inventory_place_info set 
				place_type = '$place_type',place_name = '$place_name', place_tel = '$place_tel', place_fax='$place_fax',	place_msg='$place_msg',	return_position='$return_position',	disp='$disp'
				where pi_ix = '$pi_ix'";
	$db->query($sql);
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('보관장소 수정이 완료 되었습니다.');parent.document.location.href='place_list.php'</script>";
}
?>