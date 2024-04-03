<?php
include("../class/layout.class");
$domain = str_replace('www.','',$_SERVER['HTTP_HOST']);
if(substr($_SERVER['HTTP_HOST'],0,2) !='m.'){
	$sql = "select * from ".TBL_SHOP_SHOPINFO." where mall_domain = '{$domain}'";
}else{
	$sql = "select * from ".TBL_SHOP_SHOPINFO." where mall_mobile_domain = '{$domain}'";
}
$db->query($sql);
$db->fetch();
$mall_service_type = $db->dt['mall_service_type'];

if($admininfo['language'] == ""){
	$admininfo['language'] = "korean";
}

if($admininfo['mall_type'] == "H"){//E
	include("site_main.php");
}else{
	if(substr($_SERVER["HTTP_HOST"], 0, 2) == "m." || $type == "mobile" || substr_count($_SERVER["HTTP_USER_AGENT"],"Mobile") ){

		//session_start 이슈 있음
		if($_SESSION["admininfo"]["action_agent"]=="app"){
			include("shop_main_v3.php");
		}else{
			//include("shop_main_v3_mobile.php");
			include("shop_main_v3.php");
		}
	}else{
		if(substr_count($_SERVER["PHP_SELF"],"admin")){
			if($_SERVER["HTTP_HOST"]=="dev2.forbiz.co.kr" || $_SERVER["HTTP_HOST"]=="daiso.forbiz.co.kr"){
				include("shop_main_v3_new.php");//박차장님 요청으로 임시로 열어둠 kbk 13/12/19
			}else{
				// 배럴총괄, MD팀장, MD1, MD2, 디자인, CS팀장, 마케팅, 경영지원, 인사, 상품기획팀장, 생산
				$charger_roll_array = array(1, 17, 18, 19, 20, 21, 22, 24, 25, 26, 32, 34);

				if($mall_service_type=='selling'){//shopping
					include("shop_main_selling.php");
				}elseif(isset($_SESSION['admininfo']['charger_roll']) && in_array($_SESSION['admininfo']['charger_roll'], $charger_roll_array) === true) {
					include_once("shop_main_v3.php");//메인파일
				} else {
					include_once("shop_main_v4.php");//메인파일
				}
			}
		}else{
			include("shop_main.php");
		}
	}
}
