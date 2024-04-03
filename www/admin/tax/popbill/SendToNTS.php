<?php

include 'common.php';


echo '국세청 전송 요청 테스트'.chr(10).'처리결과 : ' ;

try {
	$result = $TaxinvoiceService->SendToNTS('1231212312',ENumMgtKeyType::SELL,'123123','userid');
	echo '['.$result->code.'] '.$result->message;
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);
?>