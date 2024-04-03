<?php

include 'common.php';


echo '알림메일 재전송 테스트'.chr(10).'처리결과 : ' ;

try {
	$result = $TaxinvoiceService->SendEmail('1231212312',ENumMgtKeyType::SELL,'123123','receiver@test.com','userid');
	echo '['.$result->code.'] '.$result->message;
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);
?>