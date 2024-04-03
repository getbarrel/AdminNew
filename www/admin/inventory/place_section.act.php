<?
include("../../class/database.class");
session_start();


$db = new Database;

if ($act == "value_check_jquery"){
	$strLen = strlen($value);
	if($strLen < '3' || $strLen > '16') {
		echo "130"; //자리수가 지정된만큼 충족이 안된다면 A문자를 출력하고 끝냄
		exit;
	}
	/*
	if (!preg_match("/^[a-z]{1}[0-9a-z_]+$/", $id)) {
		echo "110"; // 유효하지 않은 회원아이디
		exit;
	}
	*/
	/*
	$deny_id = array("admin","administrator","webmaster","master","root","administrator");
	$db->query("select mall_deny_id from oaasys_shopinfo where mall_ix = '".$layout_config[mall_ix]."' and mall_div = '".$layout_config[mall_div]."' ");
	$db->fetch();
	
	$deny_id_add = explode(",",$db->dt[mall_deny_id]);
	$deny_id = array_merge((array)$deny_id,(array)$deny_id_add);
	for($i=0;$i<count($deny_id);$i++){
		if(trim($id) == $deny_id[$i]){
			echo "120"; //가입불가 ID입니다. 다른 ID로 입력해주시기 바랍니다.
			exit;
		}
	}
	*/



	$db->query("SELECT * FROM inventory_place_section WHERE section_name='$value' ");
	
	if ($db->total)
	{
		echo "N"; //등록이 되어있는 [아이디]입니다.등록불가입니다.
	}
	else
	{
		echo "300";
		/*
		$db->query("SELECT * FROM oaasys_company_userinfo WHERE charger_id='$id'");
		if ($db->total){
			echo "N"; //등록이 되어있는 [아이디]입니다.등록불가입니다.
		}else{
			echo "300"; //등록이 가능한 [아이디]입니다.
		}
		*/
	}
}



if ($act == "insert")
{

	$sql = "insert into inventory_place_section (ps_ix,pi_ix, section_name, section_type, disp, width_length, depth_length, height_length, regdate) values('','$pi_ix','$section_name','$section_type','$disp','$width_length','$depth_length','$height_length',NOW())";
	
	$db->sequences = "INVENTORY_PLACE_SECTION_SEQ";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('보관장소가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	$sql = "update inventory_place_section set 
				pi_ix = '$pi_ix', section_name='$section_name', section_type='$section_type', disp='$disp' ,
				width_length='$width_length' , depth_length='$depth_length' , height_length='$height_length' 
			where ps_ix='$ps_ix' ";

	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('보관장소가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){

	if($ps_ix == "3" or $ps_ix == "2"){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('해당 보관장소는 삭제할수 없습니다.');</script>");
		echo("<script>parent.document.location.reload();</script>");
		exit;
	}

	if($ps_ix){

		$sql = "select sum(ips.stock) as cnt
				from
					inventory_history as ih 
					inner join inventory_history_detail as ihd on (ih.h_ix = ihd.h_ix)
					inner join inventory_product_stockinfo as ips on (ihd.gid = ips.gid and ips.unit = '1')
				where
					ips.ps_ix = '".$ps_ix."'";

		$db->query($sql);
		$db->fetch();
		$total = $db->dt[cnt];

		if($total == 0 or $total == NUll){

			$sql = "delete from inventory_place_section where ps_ix='$ps_ix'";
			$db->query($sql);

			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('보관장소가 정상적으로 삭제되었습니다.');</script>");
			echo("<script>parent.document.location.reload();</script>");
		
		}else if($total < 0){
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('보관장소에 미처리 재고가 존재합니다.');</script>");
			echo("<script>parent.document.location.reload();</script>");
		}else if($total > 0){
			echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('보관장소가에 재고가 있습니다.');</script>");
			echo("<script>parent.document.location.reload();</script>");
		}
	}


}



if($act == "exit_order_change"){
//print_r($_POST);
	//print_r($sno);
	//print_r($sort);
	sort($sort);
	//print_r($sort);
	$db = new Database;
	for($i=0;$i < count($sno);$i++){
		//$db->query("UPDATE inventory_place_info SET exit_order='".$sort[$i]."' WHERE pi_ix ='".$sno[$i]."' ");
		if($change_all){
			$db->query("UPDATE inventory_place_section SET exit_order='".($i+1)."' WHERE ps_ix ='".$sno[$i]."' ");
		}else{
			$db->query("UPDATE inventory_place_section SET exit_order='".$sort[$i]."' WHERE ps_ix ='".$sno[$i]."' ");
		}
	}

	echo("<script>parent.document.location.reload();</script>");
	exit;
	
}


if($act == "product_exit_order_change"){
//print_r($_POST);
	//print_r($sno);
	//print_r($sort);
	rsort($sort);
	//print_r($sort);
	$db = new Database;
	$db->debug = true;
	for($i=0;$i < count($sno);$i++){
		//$db->query("UPDATE inventory_place_info SET exit_order='".$sort[$i]."' WHERE pi_ix ='".$sno[$i]."' ");
		$db->query("UPDATE inventory_product_stockinfo SET exit_order='".($i+1)."' WHERE ps_ix ='".$sno[$i]."' and pid = '".$pid."' ");
	}

	//echo("<script>parent.document.location.reload();</script>");

	exit;
	
}

?>
