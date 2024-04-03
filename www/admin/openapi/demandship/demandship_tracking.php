<?php
	include("../../class/layout.class");
	include_once("demandship.config.php");

	$db = new Database;
	$db2 = new Database;

	$sql = "select 
			demandship_service_key, company_id
			from
				common_seller_delivery
			where
				company_id = '". $_SESSION['admininfo']['company_id'] ."' limit 1";
	$db->query($sql);
	$sellkey = $db->fetch();

	if(!empty($sellkey['demandship_service_key']) && count($sellkey['company_id']) > 0){
		$header = array();
		$header[] = "Content-Type: application/json";
		$header[] = "Authorization: Bearer ".$sellkey['demandship_service_key'];
		$header[] = "Accept: application/json";
		$header = implode("' -H '", $header);

		$actionUrl = DEMANDSHIP_URL . "/api/v1/tracking/" . $_GET['invoice_no'];

		$command = "curl -H '".$header."' " . $actionUrl;
		$response = shell_exec($command);

		$res_arr = json_decode($response, true);

		echo '고유번호 : ';
		echo $res_arr['data']['shipment'];
		echo '<br>';
		echo '송장번호 : ';
		echo $res_arr['data']['trackingNumber'];
		echo '<br>';
		echo '배송상세내역 : ';

		echo "<ul style='list-style:none'>";
		if(count($res_arr['data']['updates']) > 0){
			foreach($res_arr['data']['updates'] as $key => $val){
				/*
				echo '<pre>';
				echo print_r($val[]);
				echo '</pre>';
				echo '<br>';
				*/

				echo "<li>";
				echo 'description : ';
				echo $val['description'];
				echo "</li>";

				echo "<li>";
				echo 'location : ';
				echo $val['location'];
				echo "</li>";

				echo "<li style='padding-bottom:18px'>";
				echo 'date : ';
				echo $val['date'] . $val['time'];
				echo "</li>";
			}
			}
		echo '</ul>';

	}
?>