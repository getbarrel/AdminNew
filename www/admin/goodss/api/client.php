<?php		
		ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
		$install_path = "../include/";
		include("SOAP/Client.php");

		$soapclient = new SOAP_Client("http://b2bdev.mallstory.com/admin/cogoods/");
		// server.php 의 namespace 와 일치해야함
		$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

		## 한글 인자의 경우 에러가 나므로 인코딩함.
		
		// 타겟정보는 array('타켓 이름' , '타겟 이메일')
		$targetInfo[0] = array(urlencode("신훈식"), "tech@forbiz.co.kr");
		$targetInfo[1] = array(urlencode("신훈식1"), "sigi1074@naver.com");
		$targetInfo[2] = array(urlencode("신훈식2"), "sigi1074@nate.com");
		
		$mailInfo[0] = NULL; // 메일내용 형식 (HTML, TXT)
		$mailInfo[1] = urlencode("tech@forbiz.co.kr"); // 보내는 사람 메일
		$mailInfo[2] = urlencode("포비즈"); // 보내는 사람 이름
		
		$mailInfo[3] = urlencode("제목");  // 메일제목
		$mailInfo[4] = urlencode("메일내용\n test \n <b>굵게</b>"); // 메일내용
		$mailInfo[5] = "TXT"; // 메일내용 형식 (HTML, TXT)
		
		$soapclient->call("sendMails",$params = array("targetInfo"=> $targetInfo,"mailInfo"=> $mailInfo),	$options);
	
?>
