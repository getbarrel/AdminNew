<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");

$db = new Database;

if ($act == "goodss_order"){

	ini_set('include_path', ".:/usr/local/lib/php:".$_SERVER["DOCUMENT_ROOT"]."/include/pear");
	$install_path = "../../include/";
	include("SOAP/Client.php");


	$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

	//$payment_info = $_POST;
	//print_r($_POST);
	//exit;
	$sql = "select * from ".TBL_SHOP_ORDER."  where oid='".$oid."' ";
	//echo $sql;
	$db->query($sql);
	$db->fetch(0,"object");
	$goodss_order[order_info] = (array)$db->dt;

	//print_r($admininfo);
	//print_r($sellerInfo);
	//exit;

	$sql = "select * from ".TBL_SHOP_ORDER_DETAIL." od WHERE oid='".$oid."'";
	//echo $sql;
	$db->query($sql);
	$db->fetch(0,"object");
	$goodss_order[orders_detail_info] = (array)$db->dt;

	//print_r($goodss_order);
	//exit;
	$result = (array)$soapclient->call("GoodsOrderReg",$params = array("goodss_order"=> $goodss_order),	$options);
	//echo $co_goodsinfo;
	print_r($result);
	exit;

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('도매 주문이 정상적으로 처리 되었습니다.');self.close();</script>";


}

?>