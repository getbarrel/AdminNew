<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

if($act == "update"){

		$cron_goodss = urlencode(serialize($_GET));

		if($goods_act == "cron_goodss_update"){
			$shmop = new Shared("cron_goodss_update");
			$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
			$shmop->SetFilePath();
			$shmop->setObjectForKey($cron_goodss,"cron_goodss_update");
		}else{
			$shmop = new Shared("cron_goodss_reg");
			$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
			$shmop->SetFilePath();
			$shmop->setObjectForKey($cron_goodss,"cron_goodss_reg");
		}

		/*
		if($cron_minutes == "*"){
			$cron_minutes_set_str = "*";
		}else{
			$cron_minutes_set_str = "*\/".$cron_minutes;
		}
		
		if($cron_hours_set == "*"){
			$cron_hours_set_str = "*";
		}else{
			$cron_hours_set_str = "*\/".$cron_hours;
		}
		*/
		
		if($cron_hours_set == "*"){
			$cron_hours_set_str = "*";
		}else{
			$cron_hours_set_str = "*/".$cron_days;
		}

		
		
		
		//$path = $_SERVER["DOCUMENT_ROOT"]."/_logs/";
		/*
		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			//chmod($path,0777);
		}
		*/
		//print_r($_SESSION["layout_config"]);
		//exit;
		if($goods_act == "cron_goodss_update"){
			$write = $cron_minutes." ".$cron_hours ." * * * lynx --dump 'http://".$_SERVER["HTTP_HOST"]."/admin/goodss/goodss.cron.php?goods_act=cron_goodss_update'\n";

			if($mall_id){
				$fp = fopen($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/scheduling/".$mall_id."_goodss_update","w+");
			}else{
				$fp = fopen($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/scheduling/goodss_update","w+");
			}

		}else if($goods_act == "cron_goodss_reg"){
			$write = $cron_minutes." ".$cron_hours ." * * * lynx --dump 'http://".$_SERVER["HTTP_HOST"]."/admin/goodss/goodss.cron.php?goods_act=cron_goodss_reg'\n";
			if($mall_id){
				$fp = fopen($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/scheduling/".$mall_id."_goodss_reg","w+");
			}else{
				$fp = fopen($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/scheduling/goodss_reg","w+");
			}
		}

		
		fwrite($fp,$write);

		echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert('스케줄 정보가 정상적으로 수정 되었습니다.');parent.document.location.reload();</script>";
}



