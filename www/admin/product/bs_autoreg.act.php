<?
include_once("../../class/database.class");

if($bs_act == "new_goods_reg"){
	$db = new Database;

	$sql = "select date_format(edate,'%Y%m%d') as edate from shop_buyingservice_autoupdate_history where autoupdate_type = '".$bs_site."' and date_format(sdate,'%Y%m%d') = '".date("Ymd")."' and sdate < edate ";
	
			
	$db->query($sql);
	$db->fetch();

	if(!$db->total){
	//	echo ("lynx --dump 'http://".$_SERVER["HTTP_HOST"]."/admin/product/buyingServiceInfo.cron.php?bs_act=new_goods_reg&get_bs_site=".$bs_site."'"); 
	//exit;
		$result = shell_exec("lynx --dump 'http://".$_SERVER["HTTP_HOST"]."/admin/product/buyingServiceInfo.cron.php?bs_act=new_goods_reg&get_bs_site=".$bs_site."'"); 
		echo "성공적으로 요청되었습니다.";
		echo $result;
		
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('신상품 등록이 이미 진행중입니다.');parent.document.location.reload();</script>";
	}
}
