<?php
$headers = array("Content-type: application/json;charset=utf-8", "goodsFLOW-Api-Key: 3bdd5171-5b36-4a4e-b5c9-f8d421b842f8");
$template_code = "testCode1";
$time = date('YmdHis');

$return = callCurl('result',$headers,'');
$return_arr = json_decode($return, true);

$return_total = $return_arr['data']['totalItems'];

echo "<pre>";
print_r($return_arr);
echo "</pre>";

function callCurl($type,$headers,$talkdata)
{
    $urlSend = "";
    if ($type == "send") $url = "https://ws1.goodsflow.com/WebApi/MISS/Member/v2/SendTalkOrMsg/forbiz";
    else if ($type == "result") $url = "https://ws1.goodsflow.com/WebApi/MISS/Member/v2/Result/forbiz";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    if ($talkdata) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($talkdata));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $return = curl_exec($ch);
    curl_close($ch);
    return $return;
}
?>