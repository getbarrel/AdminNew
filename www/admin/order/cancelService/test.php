<?
include("./cancel.php");

$cancel = new cancel();

$data["cancel_amount"] = "1000";
$data["oid"] = "201307042332-6662";
$data["cancel_msg"] = "부분취소 테스트요청";

$result = $cancel->requestCancel($data);

//echo "111";
print_r($result);
//print_r($cancel->test());

