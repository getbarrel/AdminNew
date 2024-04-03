<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

if($act == "update"){

		//print_r($_GET);

		if($goods_act == "cron_sellertool_update"){
			$shmop = new Shared("cron_sellertool_update");
			$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
			$shmop->SetFilePath();
			$cron_sellertools = $shmop->getObjectForKey("cron_sellertool_update");
		}else{
			$shmop = new Shared("cron_sellertool_reg");
			$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
			$shmop->SetFilePath();
			$cron_sellertools = $shmop->getObjectForKey("cron_sellertool_reg");
		}
		$sellertool_schedule = unserialize(urldecode($cron_sellertools));


		$sellertool_schedule[$_GET["site_code"]]["site_code"] = $_GET["site_code"];
		$sellertool_schedule[$_GET["site_code"]]["cron_minutes_set"] = $_GET["cron_minutes_set"];		
		$sellertool_schedule[$_GET["site_code"]]["cron_hours_set"] = $_GET["cron_hours_set"];

		$sellertool_schedule[$_GET["site_code"]]["cron_minutes"] = $_GET["cron_minutes"];		
		$sellertool_schedule[$_GET["site_code"]]["cron_hours"] = $_GET["cron_hours"];

		$sellertool_schedule[$_GET["site_code"]]["cron_days_set"] = $_GET["cron_days_set"];
		$sellertool_schedule[$_GET["site_code"]]["cron_weekdays_set"] = $_GET["cron_weekdays_set"];
		$sellertool_schedule[$_GET["site_code"]]["cron_months_set"] = $_GET["cron_months_set"];

		//print_r($sellertool_schedule);
		//exit;

		$cron_sellertool = urlencode(serialize($sellertool_schedule));

		if($goods_act == "cron_sellertool_update"){
			$shmop = new Shared("cron_sellertool_update");
			$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
			$shmop->SetFilePath();
			$shmop->setObjectForKey($cron_sellertool,"cron_sellertool_update");
		}else{
			$shmop = new Shared("cron_sellertool_reg");
			$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
			$shmop->SetFilePath();
			$shmop->setObjectForKey($cron_sellertool,"cron_sellertool_reg");
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
		if(false){
			if($goods_act == "cron_sellertool_update"){
				$write = $cron_minutes." ".$cron_hours ." * * * lynx --dump 'http://".$_SERVER["HTTP_HOST"]."/admin/sellertool/interface_scheduler.cron.php?goods_act=cron_sellertool_update'\n";

				if($mall_id){
					$fp = fopen($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/scheduling/".$mall_id."_sellertool_update","w+");
				}else{
					$fp = fopen($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/scheduling/sellertool_update","w+");
				}

			}else if($goods_act == "cron_sellertool_reg"){
				$write = $cron_minutes." ".$cron_hours ." * * * lynx --dump 'http://".$_SERVER["HTTP_HOST"]."/admin/sellertool/interface_scheduler.cron.php?goods_act=cron_sellertool_reg'\n";
				if($mall_id){
					$fp = fopen($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/scheduling/".$mall_id."_sellertool_reg","w+");
				}else{
					$fp = fopen($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/scheduling/sellertool_reg","w+");
				}
			}
		

		
		fwrite($fp,$write);
		}

		echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert('스케줄 정보가 정상적으로 수정 되었습니다.');parent.document.location.reload();</script>";
}



