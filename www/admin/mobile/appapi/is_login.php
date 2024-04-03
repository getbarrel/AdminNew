<?php
include $_SERVER['DOCUMENT_ROOT'].'/class/layout.class';

//syslog(LOG_INFO,'is_login session id : '.session_id());

$result = false;
$msg = 'not login';
if(isset($_SESSION['user'])){
	if(! empty($_SESSION['user']['code'])){
		$result = true;
		$msg = 'login';
	}
}

print_r(array('result'=>$result,'msg'=>$msg));
