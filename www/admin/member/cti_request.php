<?php
/* CTI 대기인원 확인 프로세스 2014-07-13 JBG
*  
*/

$url = "http://183.111.154.13:8070/WaitCnt/";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$content = curl_exec($ch);
curl_close($ch);
echo $content;

