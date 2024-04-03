<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

if($act == "update"){

		$cron_goodss = urlencode(serialize($_GET));
		$shmop = new Shared("cron_group_cupon");
		$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
		$shmop->SetFilePath();
		$shmop->setObjectForKey($cron_goodss,"cron_group_cupon");

		/*
		$write = "* * ".$day." * * root lynx --dump 'http://".$_SERVER["HTTP_HOST"]."/admin/cron/group_cupon_give.cron.php?act=cron'\n";

		$fp = fopen($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/scheduling/group_cupon_give","w+");
		fwrite($fp,$write);
		*/

		echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert('스케줄 정보가 정상적으로 수정 되었습니다.');parent.document.location.reload();</script>";
}



