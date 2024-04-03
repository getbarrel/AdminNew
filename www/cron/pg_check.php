<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
//include($_SERVER["DOCUMENT_ROOT"]."/class/http.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");

$db = new Database();
//echo date("H");
/**
* 1. 체크주기 아침 9시부터 24시까지는 3시간동안 주문이 없을경우 알림을 준다.
* 2. 24시 부터 아침 9시까지는 10시간 동안 주문이 없을경우 알림을 준다.
* 3. 특정시간동안 주문이 없는 경우 또한 주문예정금액이 있었을 경우만 알림을 한다.
* 4. 매 10분 마다 체크하여 문제가 있을경우 관련 담당자들에게 SMS 메세지를 전송한다.
**/
if(date("H") > 7 && date("H") < 24){
	$interval = 60;
}else{
	$interval = 30;
}

$sql = "select IFNULL(sum(ptprice),0) as sr_total_price 
			from shop_order_detail  
			where order_from = 'self' and status in ('SR') 
			and regdate between date_sub(NOW(), INTERVAL ".$interval." MINUTE) and NOW() 
			limit 1; ";
//echo nl2br($sql);
$db->query($sql);
$db->fetch();
$sr_total_price = $db->dt[sr_total_price];
//$sr_total_price = 3000;
//$ $sr_total_price."<br>";
//$sql = "select sum(total_price) as total from shop_order  where  vdate='".date("Ymd")."' limit 1; ";
$sql = "select IFNULL(sum(ptprice),0) as total_price 
			from shop_order_detail  
			where order_from = 'self' and status not in ('SR','IR') 
			and regdate between date_sub(NOW(), INTERVAL ".$interval." MINUTE) and NOW() 
			limit 1; ";
//echo nl2br($sql);
$db->query($sql);
$db->fetch();	
//echo $db->dt[total_price];

if($db->total && $db->dt[total_price] > 0){	
	echo "[시스템알림-".$interval."분] 결제금액 : ".number_format($db->dt[total_price]);
	exit;
}else{
	
	if($sr_total_price > 0){
		$sql = "select mall_domain_id, mall_domain_key from shop_shopinfo  where mall_div = 'B'  ";
		$db->query($sql);
		$db->fetch();

		$admininfo[arr_mall_domain_key] = $db->dt[mall_domain_key];
		$admininfo[mall_domain_key] = $db->dt[mall_domain_key];
		$admininfo[mall_domain_id] = $db->dt[mall_domain_id];

		$cominfo = getcominfo();
		$sdb = new Database;
		$s = new SMS();

		//print_r($admininfo);
		$sms_able_count =  $s->getSMSAbleCount($admininfo);
		//echo "aaa".$sms_able_count;
		//exit;
		if($sms_able_count > 0){
				$s->send_phone = $cominfo[com_phone];
				$s->send_name = $cominfo[com_name];

				$mobiles[0] = array("name"=>"신훈식","mobile"=>"010-5203-1074");
				$mobiles[1] = array("name"=>"홍진영","mobile"=>"010-3887-4023");
				/*
				 
				*/

				for($i=0;$i<count($mobiles);$i++){
					//echo $mobiles[$i];
					//list($name, $mem_id, $mobile) = split("[|]",$mobiles[$i],3);

					$s->dest_phone = str_replace("-","",$mobiles[$i]["mobile"]);
					$s->dest_name = $mobiles[$i]["name"];
					$s->msg_body = "[".$admininfo[mall_domain_id]."-시스템알림-".$interval."분] 결제프로세스 확인필요 결제전 금액 : ".number_format($sr_total_price)."원 ";
					echo $s->msg_body;
					$result = $s->sendbyone($admininfo);
					//print_r($result);
				}

				//echo("<script language='javascript'>alert('정상적으로SMS가 발송되었습니다.');</script>");
				//echo("<script language='javascript'>self.close();</script>");
		
		}
	}else{
		echo "[시스템알림]결제 내역이 존재 하지 않습니다. 결제전 금액 : ".number_format($sr_total_price)."원 , 결제 금액 : ".number_format($db->dt[total_price])."원";
	}

	//echo 0;
}

	

?>