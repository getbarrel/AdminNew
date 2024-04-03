<?
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
$db = new Database;
$mdb = new Database;
$idb = new Database;
$today = date('Y-m-d H:i:s');

//[Start] mall_ix 가져오는 프로세스 추가 kbk 13/12/13
$domain = str_replace('www.','',$_SERVER['HTTP_HOST']);
if(substr($domain, 0, 2) == "m.") {
	$domain=substr($domain, 2);
}
$sql = "select mall_ix from ".TBL_SHOP_SHOPINFO." where mall_domain = '{$domain}' LIMIT 1";
$db->query($sql);
$db->fetch();
$mall_ix=$db->dt["mall_ix"];
//[End] mall_ix 가져오는 프로세스 추가 kbk 13/12/13

///////////////////장바구니 비움기간등 새로 추가 shop_mall_config 에 추가되고 가져옴///////////////////////////////2013-05-03 이학봉

$sql = "
		select
			*
		from
			shop_mall_config
		where
			mall_ix = '".$mall_ix."'
			and config_value is not null
";//mall_ix 를 $admininfo[mall_ix]로 가져오던 것을 $mall_ix 로 변경 kbk 13/12/13

$db->query($sql);
$payment_array = $db->fetchall();

$i = 0;
while(count($payment_array)> $i){
	
	if($payment_array[$i][config_name] == "cart_delete_day")	$cart_delete_day = $payment_array[$i][config_value];
	if($payment_array[$i][config_name] == "cancel_auto_day")	$cancel_auto_day = $payment_array[$i][config_value];
	if($payment_array[$i][config_name] == "check_order_day")	$check_order_day = $payment_array[$i][config_value];
	if($payment_array[$i][config_name] == "seller_account_status")	$seller_account_status = $payment_array[$i][config_value];
	if($payment_array[$i][config_name] == "product_prohibition_text")	$product_prohibition_text = $payment_array[$i][config_value];
	if($payment_array[$i][config_name] == "wholesale_retail_use")	$wholesale_retail_use = $payment_array[$i][config_value];
	if($payment_array[$i][config_name] == "seller_account_day")	$seller_account_day = $payment_array[$i][config_value];

	if(strstr($payment_array[$i][config_name],"sheet_name")){
		//$deail_array[$payment_array[$i][config_name]][sheet_value] = $payment_array[$i][sheet_value];
		//$deail_array[$payment_array[$i][config_name]][text] = $payment_array[$i][text];
	}
	
$i++;

}

/////////////////////////////////////////////////////////////////////////////

// 장바구니 비움  몇일 이상된 장바구니 상품 비움 시작/////////

$sql = "select cart_ix from shop_cart where DATE_ADD(regdate,INTERVAL ".$cart_delete_day." DAY)  <= '".$today."' order by regdate desc ";
$mdb->query($sql);

for($i=0;$i<$mdb->total;$i++){
	$mdb->fetch($i);
	$sql = "delete from shop_cart where	cart_ix = '".$mdb->dt[cart_ix]."'";
	$idb->query($sql);
	
	$sql = "delete from shop_cart_options where		cart_ix = '".$mdb->dt[cart_ix]."'";
	$idb->query($sql);
}


// 장바구니 비움  몇일 이상된 장바구니 상품 비움  끝/////////



// 취소요청 자동완료기간  몇일 이상된 취소요청 주문을 자동 '취소완료' 처리상태로 변경되는 기간 시작/////////ORDER_STATUS_CANCEL_APPLY--->ORDER_STATUS_CANCEL_COMPLETE

/* 정책적으로 검토 해야함 HONG 2014-08-05

$sql = "select od_ix from shop_order_detail where DATE_ADD(regdate,INTERVAL ".$cancel_auto_day." DAY)  <= '".$today."' and status = '".ORDER_STATUS_CANCEL_APPLY."' "; 
//echo "$sql";exit;
//제휴사 주문때문에 자체주문만 처리하도록 수정 12.10.19 bgh
$mdb->query($sql);

for($i=0;$i<$mdb->total;$i++){
	$mdb->fetch($i);
	$sql = "update shop_order_detail set status = '".ORDER_STATUS_CANCEL_COMPLETE."' where od_ix = '".$mdb->dt[od_ix]."' ";
	$idb->query($sql);

}
*/

// 장바구니 비움  몇일 이상된 장바구니 상품 비움  끝/////////


?>