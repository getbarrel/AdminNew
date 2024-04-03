<?php


	$ig_isSecure = false;
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		$ig_isSecure = true;
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
		$ig_isSecure = true;
	}

	if($ig_isSecure === false) {
		//	https 리다이렉트
		//echo "NO1";
		header("Location:https://" . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI']);
		exit;
	}





		//	세션 ip 고정
		if($_SESSION["admininfo"][ch_my_ip] != $_SERVER["REMOTE_ADDR"]) {
			echo"
				<script>alert('접속 IP변경이 확인되었습니다. 재 로그인 해주세요.');
				location.href='/admin/admin.php?act=logout';</script>
				";
			exit;
		}




define("SOLUTION_VERSION","4.0.1");

define('CONFIG_ROOT', dirname(__file__));
define('MALL_ROOT', str_replace('/config', '/www', dirname(__file__)));
define('CORE_ROOT', str_replace('/config', '/core', dirname(__file__)));

//ENVIRONMENT (development, testing, production)
if (substr_count($_SERVER['HTTP_HOST'], 'forbiz') > 0) {
    define('ENVIRONMENT', 'development');
} else {
    define('ENVIRONMENT', 'production');
}

//이미지 서버 도메인
if (constant('ENVIRONMENT') == 'production'){
    define("IMAGE_SERVER_DOMAIN","");
}else{
    define("IMAGE_SERVER_DOMAIN","");
}




	//	오류메시지 숨김 ISMS 정보누출
        ini_set("memory_limit" , -1);
        error_reporting(0);