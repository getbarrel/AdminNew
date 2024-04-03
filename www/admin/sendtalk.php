<?php
$headers = array("Content-type: application/json;charset=utf-8", "goodsFLOW-Api-Key: 3bdd5171-5b36-4a4e-b5c9-f8d421b842f8");
$template_code = "testCode1";
$time = date('YmdHis');
$talkdata = array(
    "data" => array(
        "items" => array(
            "0" => array(
                "uniqueCode" => "test1".$time,
                "sectionCode" => "test2".$time,
                "recipientNo" => "01024052768",
                "talkType" => "A",
                "talkTemplateCode" => $template_code,
                "talkContent" => "[배송 시작 안내]
안녕하세요. 고객명 고객님!
고객님께서 주문하신 상품이 발송되었습니다.
 
-주문번호 : 주문번호
-상품명 : 상품명
-택배사명 : 택배사명
-운송장번호 : 01011112222333
 
감사합니다.",
                "buttonName" => "배송조회하기",
                "buttonUrl" => "http://enter40.forbiz.co.kr/admin/sendtalk_check.php",
                "msgType" => "LMS",
                "msgContent" => "구매하신 물품이 건영택배로 발송되었습니다.",
                "msgCallback" => "07044371788",
            ),
        ),
    ),
    "context" => $time
);

echo "<pre>";
print_r(json_encode($talkdata));
echo "</pre> <br>";

$return = callCurl('send',$headers,$talkdata);

echo "<pre>";
print_r($return);
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