<?
include("../../class/database.class");

if($act == "exit_order_change"){
//print_r($_POST);
	//print_r($sno);
	//print_r($sort);
	sort($sort);
	//print_r($sort);
	$db = new Database;
	for($i=0;$i < count($sno);$i++){
		//$db->query("UPDATE inventory_place_info SET exit_order='".$sort[$i]."' WHERE pi_ix ='".$sno[$i]."' ");
		$db->query("UPDATE inventory_place_info SET exit_order='".($i+1)."' WHERE pi_ix ='".$sno[$i]."' ");
	}

	echo("<script>parent.document.location.reload();</script>");

	exit;
	
}


if($act == "product_exit_order_change"){
//print_r($_POST);
	//print_r($sno);
	//print_r($sort);
	sort($sort);
	//print_r($sort);
	$db = new Database;
	$db->debug = true;
	for($i=0;$i < count($sno);$i++){
		//$db->query("UPDATE inventory_place_info SET exit_order='".$sort[$i]."' WHERE pi_ix ='".$sno[$i]."' ");
		$db->query("UPDATE inventory_product_stockinfo SET exit_order='".($i+1)."' WHERE pi_ix ='".$sno[$i]."' and pid = '".$pid."' ");
	}

	//echo("<script>parent.document.location.reload();</script>");

	exit;
	
}

?>
