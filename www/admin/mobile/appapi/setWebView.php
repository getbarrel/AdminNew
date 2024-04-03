<?php
include $_SERVER['DOCUMENT_ROOT'].'/class/layout.class';

//syslog(LOG_INFO,'set webview session id : '.session_id());


$_SESSION["is_webview"] = true;

$result = true;
$msg = 'set webview success';



print_r(array('result'=>$result,'msg'=>$msg));
