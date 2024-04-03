<?

include($_SERVER["DOCUMENT_ROOT"]."/class/http.class");
include("nate.lib.php");
//define("secretKey","DMVcFz_XmOFSKOq5UBrVdV-Jk_MvdFb3ezVShg");


$OAUTH = new NateAuth;

$OAUTH->GetRequestToken();
echo "request_token : ".$OAUTH->request_token."<br>";
echo "request_token_secret : ".$OAUTH->request_token_secret;

//Redirect to Authorize URL 
//다음 단계인 Nate Login을 위해 페이지 이동
$Authorize_URL = "https://oauth.nate.com/OAuth/Authorize/V1a?oauth_token=".$OAUTH->request_token;
Header("Location: $Authorize_URL");

exit;
$oauth_consumer_key = "b13ca9da99cfe9f74690a4a9ab6c4b9b04d7b433d";
$oauth_consumer_key_secret = "15f90718cab4b8aa1c31c3671d2dc036";
$oauth_timestamp = time();
$oauth_nonce = md5(microtime().mt_rand());
$oauth_version = "1.0";
$oauth_signature_method = "HMAC-SHA1";
$oauth_callback = "https://dev.forbiz.co.kr/admin/work/nate/step2.php";// Your Call Back Page URL
$get_request_token_url = "https://oauth.nate.com/OAuth/GetRequestToken/V1a";

$https = new Https;
$http = new Http;

//네이트온 쪽지 발송

$https->setURL($get_request_token_url);                              //요청 url
$https->setParam("oauth_callback", $oauth_callback);
$https->setParam("oauth_consumer_key", $oauth_consumer_key);
$https->setParam("oauth_nonce", $oauth_nonce);
$https->setParam("oauth_signature_method", $oauth_signature_method);
$https->setParam("oauth_timestamp", $oauth_timestamp);
$https->setParam("oauth_version", $oauth_version);

$i=0;
foreach($https->variable as $key => $val) {	
	if($i == 0){
		 $Query_String .= urlencode_rfc3986(trim($key))."=\"".urlencode_rfc3986(trim($val))."\"";
	}else{
		 $Query_String .= ",".urlencode_rfc3986(trim($key))."=\"".urlencode_rfc3986(trim($val))."\"";
	}
   
  $i++;
}


//Base String 요소들을 rfc3986 으로 encode
$Base_String = urlencode_rfc3986("POST")."&".urlencode_rfc3986($get_request_token_url)."&".urlencode_rfc3986($Query_String);

echo("Base_String=".$Base_String."<br /><br />");

//지금 단계에서는 $oauth_token_secret이 ""
$Key_For_Signing = urlencode_rfc3986($oauth_consumer_key_secret)."&".urlencode_rfc3986($oauth_token_secret);

echo("Key_For_Signing=".$Key_For_Signing."<br /><br />");

//oauth_signature 생성
$oauth_signature=base64_encode(hash_hmac('sha1', $Base_String, $Key_For_Signing, true));

echo("oauth_signature=".$oauth_signature."<br /><br />");

$https->setParam("oauth_signature", $oauth_signature);

$i=0;
//$Authorization_Header  = "Authorization: OAuth ";
foreach($https->variable as $key => $val) {	
	if($i == 0){
		 $Authorization_Header .= urlencode_rfc3986(trim($key))."=\"".urlencode_rfc3986(trim($val))."\"";
	}else{
		 $Authorization_Header .= ",".urlencode_rfc3986(trim($key))."=\"".urlencode_rfc3986(trim($val))."\"";
	}
   
  $i++;
}
echo("Authorization_Header=".$Authorization_Header."<br /><br />");
$send_result = $https->send("Oauth");

echo $send_result;


exit;

$send_memo_url = "https://openapi.nate.com/OApi/RestApiSSL/ON/250060/nateon_SendNote/v1";

$http->setURL($send_memo_url);                              //요청 url
$http->setParam("oauth_consumer_key", $oauth_consumer_key);
$http->setParam("oauth_token", "");
$http->setParam("oauth_signature_method", "HMAC-SHA1");
$http->setParam("oauth_signature", "");
$http->setParam("oauth_timestamp", time());
$http->setParam("oauth_nonce", $oauth_nonce);
$http->setParam("oauth_version", $oauth_version);
$http->setParam("ref", "\"신훈식\"<sigi1074@nate.com>");
$http->setParam("body", "쪽찌 테스트 입니다. ^^");
$http->setParam("confirm", "Y");

$i=0;
foreach($http->variable as $key => $val) {	
	if($i == 0){
		 $parameter .= urlencode_rfc3986(trim($key))."=".urlencode_rfc3986(trim($val));
	}else{
		 $parameter .= "&".urlencode_rfc3986(trim($key))."=".urlencode_rfc3986(trim($val));
	}
   
  $i++;
}

//Base String 요소들을 rfc3986 으로 encode
$Base_String = urlencode_rfc3986("POST")."&".urlencode_rfc3986($get_request_token_url)."&".urlencode_rfc3986($Query_String);

echo("Base_String=".$Base_String."<br /><br />");

//지금 단계에서는 $oauth_token_secret이 ""
$Key_For_Signing = urlencode_rfc3986($oauth_consumer_key_secret)."&".urlencode_rfc3986($oauth_token_secret);

echo("Key_For_Signing=".$Key_For_Signing."<br /><br />");
/*
$StringToSign = "POST\n";
$StringToSign .= strtolower("testbed.icubecloud.com")."\n";
$StringToSign .= "/icube-cc/rest/\n";
$StringToSign .= $parameter;
*/


//echo $parameter."<br>";
//echo base64_encode(hash_hmac("sha1",$StringToSign, $secretKey, true));
$oauth_signature = base64_encode(hash_hmac("sha1",$StringToSign, $secretKey, true));
echo("oauth_signature=".$oauth_signature."<br /><br />");

$http->setParam("oauth_signature", $oauth_signature);


$send_result = $http->send("POST");

echo $send_result;



if($act == "TerminateInstances"){
	
	$secretKey = "DMVcFz_XmOFSKOq5UBrVdV-Jk_MvdFb3ezVShg";
	$http = new Http;
	
	$Requrl = "http://testbed.icubecloud.com:6080/icube-cc/rest/";
	$http->setURL($Requrl);                              //요청 url
	$http->setParam("AccessKeyId", "SZgpSq3NFkmtdB2iaezLsQ");
	$http->setParam("Action", "TerminateInstances");
	$http->setParam("InstanceId", "i-d004a542");//img-9345034b
	
	$http->setParam("SignatureMethod", "HmacSHA1");
	$i=0;
	foreach($http->variable as $key => $val) {	
		if($i == 0){
			 $parameter .= trim($key)."=".urlencode(trim($val));
		}else{
			 $parameter .= "&".trim($key)."=".urlencode(trim($val));
		}
	   
	  $i++;
	}
	
	$StringToSign = "GET\n";
	$StringToSign .= strtolower("testbed.icubecloud.com")."\n";
	$StringToSign .= "/icube-cc/rest/\n";
	$StringToSign .= $parameter;
	$Signature = base64_encode(hash_hmac("sha1",$StringToSign, $secretKey, true));
	$http->setParam("Signature", $Signature);
	
	
	$send_result = $http->send("GET");
	
	return $send_result;
	
}


function urlencode_rfc3986($input) {
	if (is_scalar($input)) {
	    return str_replace(
	      '+',
	      ' ',
	      str_replace('%7E', '~', rawurlencode($input))
	    );
	  } else {
	    return '';
	  }
}


/*
echo "<br><br>\n";
echo "StringToSign:\n".$StringToSign."\n\n";
echo "Signature:\n".$Signature."\n\n";
echo "secretKey:\n".$secretKey."\n\n";

echo "request:".$Requrl."?".$parameter."&Signature=$Signature<br>";
*/

//GET http://testbed.icubecloud.com:6080//icube-cc/rest/?AccessKeyId=SZgpSq3NFkmtdB2iaezLsQ&Action=RunInstances&ImageId=img-9345034b&MinCount=1&SignatureMethod=HmacSHA1&Signature=8qyklMvAc14YKIBhIo1WckAtyGY%3D& HTTP/1.0 Host: testbed.icubecloud.com User-agent: PHP/HTTP_CLASS 
//http://testbed.icubecloud.com:6080/icube-cc/rest/?AccessKeyId=SZgpSq3NFkmtdB2iaezLsQ&Action=RunInstances&ImageId=img-9345034b&MinCount=1&SignatureMethod=HmacSHA1&Signature=8qyklMvAc14YKIBhIo1WckAtyGY%3D
?> 
