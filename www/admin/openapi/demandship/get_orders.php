<?php
 
include ($_SERVER ["DOCUMENT_ROOT"] . "/class/database.class");
$db = new MySQL ();

if(!$_SESSION["admininfo"]["company_id"]){
    $sql = "select company_id from common_company_detail  where com_type = 'A'  ";
    $db->query($sql);
    $db->fetch();
    $admininfo[company_id] = $db->dt[company_id];

    $sql = "select mall_data_root, mall_type ,mall_domain from shop_shopinfo  where mall_div = 'B'  ";
    $db->query($sql);
    $db->fetch();

    $admininfo[mall_data_root] = $db->dt[mall_data_root];
    $admininfo[admin_level] = 9;
    $admininfo[language] = 'korea';
    $admininfo[mall_type] = $db->dt[mall_type];

    $_SESSION["admin_config"]["mall_data_root"] = $db->dt[mall_data_root];
    if(substr_count(" ".$db->dt[mall_domain],"www") == 0){
        $_SESSION["admin_config"]["mall_domain"] = "www.".$db->dt[mall_domain];
    }else{
        $_SESSION["admin_config"]["mall_domain"] = $db->dt[mall_domain];
    }
}
include ($_SERVER ["DOCUMENT_ROOT"] . "/admin/openapi/openapi.lib.php");

require 'ensogo.config.php';


$api_key = "e06c5933c51bda242cf813f100f8f81a57fdff6feac15cab6e8b5f5dd5a7ef13";
//$company_id = "fae83303cce2d10a33a1021cb44a5502";
$openapi = new OpenAPI($api_key);
$openapi->site_code = "ensogo";
$openapi->lib->debug = true;  
// kbeauty : 9b6daadfa35da63853a84c13400f90bd 

//echo "<img src='http://ecx.images-amazon.com/images/I/21tYT2lWajL.jpg'>";
//exit;

$sql="select distinct add_info_id from sellertool_add_info_meta where meta_key = 'access_token'   limit 1000";//and site_code = 'ensogo'  //and add_info_id = 'a6a23a40c26e34a0e0acf2c258509b3c' 
//echo $sql;
//exit;
$db->query($sql);
$seller_keys = $db->fetchall();
$data[start]=0;
$data[limit]=500;
//$data[startDate] = date("Y-m-d H:i:s");

foreach($seller_keys as $key => $value):
	//echo "seller key : ".$value[add_info_id]."<br>";
	$openapi->lib->seller_key = $value[add_info_id];
	//$openapi->lib->getCouriers($data);
	
	$result = $openapi->lib->getOrder($data);
	$result = json_decode($result, true);
	//$orders = (array)$result;
	print_r($result);
endforeach;


