<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");

$db = new Database;


if ($act == "service_apply"){

	ini_set('include_path', ".:/usr/local/lib/php:".$_SERVER["DOCUMENT_ROOT"]."/include/pear");
	$install_path = "../../include/";
	include("SOAP/Client.php");


	$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

	//$payment_info = $_POST;
	//print_r($_POST);
	//exit;
	$sql = "select * from ".TBL_COMMON_USER." cu where cu.company_id = '".$admininfo[company_id]."' and code = '".$admininfo[charger_ix]."' ";
	//echo $sql;
	$db->query($sql);
	$db->fetch(0,"object");
	$sellerInfo[user] = (array)$db->dt;

	//print_r($admininfo);
	//print_r($sellerInfo);
	//exit;

	$sql = "select cmd.code,birthday,birthday_div,
				AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
				AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, 
				AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,
				AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
				AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,
				AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,tel_div,
				AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
				info,sms,nick_name,job,cmd.date,cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6 
				from ".TBL_COMMON_MEMBER_DETAIL." cmd 
				where code = '".$admininfo[charger_ix]."' ";
	$db->query($sql);
	$db->fetch(0,"object");
	$sellerInfo[member_detail] = (array)$db->dt;

	$sql = "select * from ".TBL_COMMON_COMPANY_DETAIL." ccd where ccd.company_id = '".$admininfo[company_id]."' ";
	$db->query($sql);
	$db->fetch(0,"object");
	$sellerInfo[company_detail] = (array)$db->dt;

	//print_r($sellerInfo);
	//exit;
//판매자기 때문에 셀러 정보에 해당하는건 넘기지 않는다.
/*
	$sql = "select csd.* ,'".$_SERVER["HTTP_HOST"]."' as shop_url from ".TBL_COMMON_SELLER_DETAIL." csd where csd.company_id = '".$admininfo[company_id]."' ";
	$db->query($sql);
	$db->fetch(0,"object");
	$sellerInfo[seller_detail] = (array)$db->dt;


	$sql = "select * from ".TBL_COMMON_SELLER_DELIVERY." csd where csd.company_id = '".$admininfo[company_id]."' ";
	$db->query($sql);
	$db->fetch(0,"object");
	$sellerInfo[seller_delivery] = (array)$db->dt;
*/
	$result = (array)$soapclient->call("ServiceApply",$params = array("mall_domain_key"=> $admininfo["mall_domain_key"],"sellerInfo"=> $sellerInfo,"payment_info"=> $_POST,"mall_domain"=> $_SERVER["HTTP_HOST"]),	$options);
	//echo $co_goodsinfo;
	//print_r($result);
	//exit;

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