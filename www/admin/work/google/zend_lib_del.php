<?php
// 클래스 로드
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear:$DOCUMENT_ROOT/admin/work/");
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
Zend_Loader::loadClass('Zend_Http_Client');

// 서비스에 연결
$gcal = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
$user = "username@gmail.com";
$pass = "pass";
$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $gcal);
$gcal = new Zend_Gdata_Calendar($client);    

// 이벤트 인출
// 이벤트 삭제
try {          
  $event = $gcal->getCalendarEventEntry('http://www.google.com/calendar/
   feeds/default/private/full/xxxxxxx');
  $event->delete();
} catch (Zend_Gdata_App_Exception $e) {
  echo "Error: " . $e->getResponse();
}        
echo 'Event successfully deleted!';  
?>
