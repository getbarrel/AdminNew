<?
include("../../class/database.class");
include("inventory.lib.php");

$db = new Database;

if($act == "insert"){

	$place_tel = $place_phone_1."-".$place_phone_2."-".$place_phone_3;
	$place_fax = $place_fax_1."-".$place_fax_2."-".$place_fax_3;

	$sql = "select IFNULL(max(exit_order),0)+1 as exit_order from inventory_place_info ";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		$exit_order = $db->dt[exit_order];
	}else{
		$exit_order = '1';
	}

	//$place_zip=$place_zip1."-".$place_zip2;
	if($place_zip2 != "" || $place_zip2 != NULL){
		$place_zip = "$place_zip1-$place_zip2";
	}else{
		$place_zip = $place_zip1;
	}
	$sql = "insert into inventory_place_info
				(pi_ix,place_type, place_name,place_position,place_zip,place_addr1,place_addr2,place_tel,place_fax,place_msg, return_position, disp, exit_order,regdate,company_id,com_person,online_place_yn)
				values
				('','$place_type','$place_name','$place_position','$place_zip','$place_addr1','$place_addr2','$place_tel','$place_fax','$place_msg','$return_position','$disp','$exit_order',NOW(),'$company_id','$com_person','$online_place_yn')";
	$db->sequences = "INVENTORY_PLACE_INFO_SEQ";
	$db->query($sql);

	$db->query("SELECT pi_ix FROM inventory_place_info WHERE pi_ix=LAST_INSERT_ID()");
	$db->fetch();
	$pi_ix = $db->dt[pi_ix];

	$sql = "insert into inventory_place_section (ps_ix,pi_ix, section_name, section_type, disp, is_basic, regdate) values('','$pi_ix','입고 보관장소','S','1','1',NOW())";	
	$db->sequences = "INVENTORY_PLACE_SECTION_SEQ";
	$db->query($sql);

	$sql = "insert into inventory_place_section (ps_ix,pi_ix, section_name, section_type, disp, is_basic, regdate) values('','$pi_ix','출고 보관장소','D','1','1',NOW())";	
	$db->sequences = "INVENTORY_PLACE_SECTION_SEQ";
	$db->query($sql);

	//[S] 양호, 불량 보관장소 추가

	$sql = "insert into inventory_place_section (ps_ix,pi_ix, section_name, section_type, disp, is_basic, regdate) values('','$pi_ix','반품 보관장소 (양호)','P','1','1',NOW())";	
	$db->sequences = "INVENTORY_PLACE_SECTION_SEQ";
	$db->query($sql);

	$sql = "insert into inventory_place_section (ps_ix,pi_ix, section_name, section_type, disp, is_basic, regdate) values('','$pi_ix','반품 보관장소 (불량)','B','1','1',NOW())";	
	$db->sequences = "INVENTORY_PLACE_SECTION_SEQ";
	$db->query($sql);

	// 쉐어드메모리 데이터 로드
	$warehouse_data = sharedControll("warehouse_data");

	//[S] 데이터 저장
	if(empty($warehouse_data)){
		$datas = array(
			"regist_company_id" => $company_id,
			"regist_pi_ix" => $pi_ix,
			"regdate" => date("Y-m-d H:i:s")
		);
		sharedControll("warehouse_data", "insert", $datas); // shared 명, act, 저장 데이터
	}
	//[E] 데이터 저장

	//[E] 양호, 불량 보관장소 추가

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('창고 등록이 완료 되었습니다.');parent.document.location.href='place_list.php'</script>";
}

if($act == "delete"){
	$db->query("delete from inventory_place_info where pi_ix = '".$pi_ix."' ");
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('창고가 정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>";
}

if($act == "update"){
	$place_tel = $place_tel_1."-".$place_tel_2."-".$place_tel_3;
	$place_fax = $place_fax_1."-".$place_fax_2."-".$place_fax_3;
	//$place_zip=$place_zip1."-".$place_zip2;
	if($place_zip2 != "" || $place_zip2 != NULL){
		$place_zip = "$place_zip1-$place_zip2";
	}else{
		$place_zip = $place_zip1;
	}

	$sql = "update inventory_place_info set
				place_type = '$place_type',place_name = '$place_name',place_position = '$place_position', place_tel = '$place_tel', place_fax='$place_fax',	place_msg='$place_msg',	return_position='$return_position',	disp='$disp',company_id = '$company_id', com_person = '$com_person', online_place_yn = '$online_place_yn', place_zip = '$place_zip', place_addr1 = '$place_addr1', place_addr2 = '$place_addr2'
				where pi_ix = '$pi_ix'";

	$db->query($sql);

	$db->query("SELECT pi_ix FROM inventory_place_section WHERE pi_ix='".$pi_ix."' and section_type = 'S'  ");
	if(!$db->total){
		$sql = "insert into inventory_place_section (ps_ix,pi_ix, section_name, section_type, disp,  is_basic, regdate) values('','$pi_ix','입고 보관장소','S','1','1',NOW())";	
		$db->sequences = "INVENTORY_PLACE_SECTION_SEQ";
		$db->query($sql);
	}

	$db->query("SELECT pi_ix FROM inventory_place_section WHERE pi_ix='".$pi_ix."' and section_type = 'D'  ");
	if(!$db->total){
		$sql = "insert into inventory_place_section (ps_ix,pi_ix, section_name, section_type, disp, is_basic,regdate) values('','$pi_ix','출고 보관장소','D','1','1',NOW())";	
		$db->sequences = "INVENTORY_PLACE_SECTION_SEQ";
		$db->query($sql);
	}


	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('창고 수정이 완료 되었습니다.');parent.document.location.href='place_list.php'</script>";
}
?>