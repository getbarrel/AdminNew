<?php

include 'common.php';


echo '발행예정 테스트'.chr(10).'처리결과 : ' ;

try {
	$result = $TaxinvoiceService->Send('1231212312',ENumMgtKeyType::SELL,'123123','발행예정 메모','userid');
	echo '['.$result->code.'] '.$result->message;
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);
?>
