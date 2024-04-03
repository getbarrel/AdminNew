<?php 


include ($_SERVER ["DOCUMENT_ROOT"] . "/admin/openapi/gsshop/gsshop.class.php");
include ($_SERVER ["DOCUMENT_ROOT"] . "/class/layout.class");
require 'gsshop.config.php';

$db = new Database();
$call = new Call_gsshop();

$requestXmlBody = '';

$result = $call->call ( CJMALL_URL , $requestXmlBody );

print_r($result);