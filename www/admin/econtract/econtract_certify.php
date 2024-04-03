<?php
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, 'http://crosscert.forbiz.co.kr/get_cert_data.php');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'signed_data=' . $_POST['signed_data']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, ture);
	
	$server_output = curl_exec($ch);
	
	curl_close($ch);
?>