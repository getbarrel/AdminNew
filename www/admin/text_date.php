<?
include("../class/layout.class");

//$sql = "select oid from shop_order where DATE_ADD(order_date,INTERVAL ".$fetch_shop_info[mall_cc_interval]." DAY)  <= '".$today."' and status = '".ORDER_STATUS_INCOM_READY."' ";
// 기간을 설정하여 처리하는 영역에서 주말 제외하는 기능 만들기 JK150519

$startDate = '2015-05-22';
$interval = 2;

$rCnt = 1;
$cCnt = 1;
//$date_all = (getdate(strtotime($startDate)));
//print_r($date_all);
//exit;
//echo DATE_ADD(".$startDate.",INTERVAL ".$interval." DAY);
/*
$this_holidays = array(
	'2015-05-25'
);*/

$db->query("SELECT * FROM `shop_mall_config` where mall_ix = '".$_SESSION["admininfo"][mall_ix]."' and config_name in('holiday_text')  ");
if($db->total){
	for($i=0; $i < $db->total;$i++){
	$db->fetch($i);
		$this_holidays = explode(',',$db->dt[config_value]);
	}
}
//print_r($this_holidays);

for($i=0; $i < $interval+1; $i++){
	$date_all[$i] = getdate(strtotime(date("Y-m-d",strtotime($startDate."+".$i."days"))));
	if($date_all[$i][wday] > 0 || $date_all[$i][wday] < 6 ){
	 $rCnt = $rCnt + 1;
	}
}
echo $rCnt."<br>--------------<br>";
for($i=0; $i < $rCnt; $i++){
	
	
	
	$date_check[$i] = date("Y-m-d",strtotime($startDate."+".$i."days"));
	
	if(in_array($date_check[$i],$this_holidays)){
		$date_all[$i] = (getdate(strtotime($date_check[$i])));
		//echo $date_all[$i][wday];
		if($date_all[$i][wday] != 0 && $date_all[$i][wday] != 6 ){
			$rCnt = $rCnt + 1;
			//echo 1;
		}
		
	}
}
echo "<br>---------<br>".$rCnt."<br>";
echo  date("Y-m-d",strtotime($startDate."+".$rCnt."days"));

exit;

?>