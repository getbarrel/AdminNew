<?
include("../../class/database.class");



$db = new MySQL;

if ($act == "insert")
{

	if($etc == ""){
		$currency_name = $currency_name;
	}else{
		$currency_name = $etc;
	}
	$sql = "insert into global_currency 
				(currency_ix,currency_name,currency_code,currency_unit_front,currency_unit_back,exchange_rate, disp,regdate) 
				values
				('','$currency_name','$currency_code','$currency_unit_front','$currency_unit_back','$disp','$exchange_rate',NOW())";

	// 오라클일때 사용
	$db->sequences = "SHOP_BANKINFO_SEQ";
	$db->query($sql);


	echo("<script currency='javascript' src='../js/message.js.php'></script><script>show_alert2('화폐단위가 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.href='currency.php';</script>");
}


if ($act == "update"){
		
	$sql = "update global_currency set
				currency_name='$currency_name',
				currency_code='$currency_code',
				currency_unit_front='$currency_unit_front',
				currency_unit_back='$currency_unit_back',
				exchange_rate='$exchange_rate',
				disp='$disp' 
				where currency_ix='$currency_ix' ";

	
	$db->query($sql);

	echo("<script currency='javascript' src='../js/message.js.php'></script><script>show_alert2('화폐단위가 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'currency.php';</script>");
}

if ($act == "delete"){
	
	$sql = "delete from global_currency where currency_ix='$currency_ix'";
	$db->query($sql);


	echo("<script currency='javascript' src='../js/message.js.php'></script><script>show_alert2('화폐단위가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.href='currency.php';</script>");
}

?>
