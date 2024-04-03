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

	if(empty($sellkey['demandship_service_key']) && count($sellkey['company_id']) > 0){

		$autorization_code = $_GET['code'];
		$actionUrl = DEMANDSHIP_URL . "/oauth/token";

		$command = "curl --data 'grant_type=authorization_code&client_id=". constant('CLIENT_ID') ."&client_secret=". constant(
		"CLIENT_SECRET") ."&redirect_uri=". constant("CALLBACK_URL") ."' --data-urlencode 'code=".$autorization_code."' " . $actionUrl;
		$response = shell_exec($command);
		$arr_res = json_decode($response, true);

		if(strlen($response) > 0){
			$sql = "update common_seller_delivery set
						demandship_service_key = '".$arr_res['access_token']."'
					where
						company_id = '". $_SESSION['admininfo']['company_id'] ."' limit 1";
			$db->query($sql);

			echo "<script>alert('서비스키가 인증되었습니다.');opener.location.reload();window.close();</script>";
		}else{
			echo "<script>alert('오류');window.close();</script>";
		}
	}else{
		echo "<script>alert('이미 인증이 되어 있습니다.');window.close();</script>";
	}

?>
