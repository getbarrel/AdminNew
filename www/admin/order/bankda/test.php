<?php 

require_once $_SERVER['DOCUMENT_ROOT'].'/admin/order/bankda/src/bankda.php';



$bankda = new bankda();
$result = $bankda->transactionMatch();
print_r($result);
exit;
//!!!!!!!! callInsertUser !!!!!!!!!!!!!	success
/**
$postValue = array(
	'user_id' => 'shin',
	'user_pw' => 'qwe!@#',
	'user_name' => '신현주',
	'user_tel' => '01022504442',
	'user_email' => 'ccmarketing@forbiz.co.kr',
	'accea' => '1'
);

$result = $bankda->callInsertUser($postValue);
print_r($result);
/**
//!!!!!!!! callDropUser !!!!!!!!!!!!!!!	success
/**
$postValue = array(
		'user_id' => 'blanpain',
		'user_pw' => '4160'
);

$result = $bankda->callDropUser($postValue);
print_r($result);
/**/
/**
//!!!!!!!! addAccount !!!!!!!!!!!!!!!	success
$postValue = array(
		'bkdiv' => 'P',
		'bkcode' => '04',
		'bkacctno' => '26310204011986',
		'bkacctpno_pw' => '4160',
		'Mjumin_1' => '000000',
		'Mjumin_2' => '000000',
		'webid' => 'SCRYED',
		'webpw' => '4160sc'
);
$result = $bankda->callAddAccount($postValue);
print_r($result);
/**/
/**
//!!!!!!!! delete Account !!!!!!!!!!!!!!!	success
$postValue = '110337279377';
$result = $bankda->callDeleteAccount($postValue);
print_r($result);
/**/



echo "//!!!!!!!! bank join info List !!!!!!!!!!!!!!!</br>\n";
$result = $bankda->callBankJoinInfo();
print_r($result);

echo "//!!!!!!!! check callUserList !!!!!!!!!!!!!!!</br>\n";
$result = $bankda->callUserList();
print_r($result);

echo "//!!!!!!!! check callALLAccountInfo !!!!!!!!!!!!!!!</br>\n";
$result = $bankda->callALLAccountInfo();
print_r($result);

echo "//!!!!!!!! check callShopAccountInfo !!!!!!!!!!!!!!!</br>\n";
$result = $bankda->callShopAccountInfo();
print_r($result);
exit;


//goal = find 04
foreach($result as $rt):
	echo $rt['comment'];
endforeach;
//기업 ,개인 구분
//comment값 볼것.



/**

$result2 = $bankda->getAccountBalance();
print_r($result2);
*/


//$result = $bankda->call("dev.forbiz.co.22kr","");

//$bankda->test();
//echo $result;

//print_r($bankda->callPartnerShipInfo());


//$checkDate = date('Ymd',strtotime('-3 day'));
//$startTime = date('YmdHis',strtotime('-13 hours'));
//echo $startTime;
