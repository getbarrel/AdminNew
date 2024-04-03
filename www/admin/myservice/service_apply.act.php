<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");

$db = new Database;

if ($act == "service_apply"){

	ini_set('include_path', ".:/usr/local/lib/php:".$_SERVER["DOCUMENT_ROOT"]."/include/pear");
	$install_path = "../../include/";
	include("SOAP/Client.php");


	$soapclient = new SOAP_Client("http://www.mallstory.com/admin/service/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

	//$payment_info = $_POST;
	//print_r($_POST);
	//exit;

	$result = (array)$soapclient->call("ServiceApply",$params = array("mall_domain_key"=> $admininfo["mall_domain_key"],"payment_info"=> $_POST,"mall_domain"=> $_SERVER["HTTP_HOST"]),	$options);
	//echo $co_goodsinfo;
	//print_r($result);


	$result = (array)$soapclient->call("getMyServiceInfo",$params = array("mall_domain"=> $_SERVER["HTTP_HOST"],"company_id"=> $admininfo[mall_domain_id], "mall_domain_key"=> $admininfo["mall_domain_key"]),	$options);
	//echo $co_goodsinfo;
	//print_r($result);
	$result = urlencode(serialize($result));
	$shmop = new Shared("myservice_info");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$shmop->setObjectForKey($result,"myservice_info");


	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('서비스가 정상적으로 신청 되었습니다.');self.close();</script>";

	// 넘어온 정보를 웹서비스를 통해서 서비스 신청 처리함
	// 주말 해야 할일
	// - my service 기초 잡기
	// 모바일 관리자 샘플 페이지 만들기
	// 디자인 구성 부분 템플릿 분리해내기
	// 쇼핑몰 로그인 내역 보는 화면 만들기
	// 한미포스트 배송 시스템 복사해서 현재 시스템에 만들어 넣기
	// SMS 발송시 예약 시스템 만들기
}

?>