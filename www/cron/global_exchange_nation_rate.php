<?
include_once("../class/database.class");

$db = new database();

$usd = get_currency("USD", "KRW");

$sql = "update common_exchange_rate set is_use='0' where is_use='1'";
$db->query($sql);

$sql = "	insert into common_exchange_rate (er_ix,usd,jpy,cny,eur,is_use,regdate) values ('','$usd','$jpy','$cny','$eur','1',NOW())";
$db->sequences = "COMMON_EXCHANGE_RATE_SEQ";
$db->query($sql);




function get_currency($from_currency, $to_currency)
{
	$url = 'http://download.finance.yahoo.com/d/quotes.csv?s='.$from_currency.$to_currency.'=X&f=sl1d1t1c1ohgv&e=.csv';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$currency_csv = curl_exec($ch);
	curl_close ($ch);

	$csv_data = explode(',', $currency_csv);
	$currency_value = -1;
	if(sizeof($csv_data) == 9 && isset($csv_data[1]))
	{
		$currency_value = (float)$csv_data[1];
		$currency_value = number_format($currency_value,2, '.', '');

		// FIXME: Do Something
	}
	unset($csv_data); unset($currency_csv);
	return $currency_value;
}

?>