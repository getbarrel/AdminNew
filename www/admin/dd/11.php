<?
include 'function.php';

  
  //$rate = rateParser('USD','CNY');
 // $ret = getcyn('CNY','KRW');
  //pre( $rate );


$chk1='CNY';
$chk2= 'KRW';

function getCNY($chk1, $chk2) {

	$exchange_url="http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22CNYKRW%22)&format=json&env=store://datatables.org/alltableswithkeys&callback=";


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $exchange_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
	$rt = curl_exec($ch);
	curl_close($ch);

	return json_decode($rt);

}

$db = localDB();


$a = getCNY($chk1, $chk2);
pre($a);
pre($db);

unset($db);

?>