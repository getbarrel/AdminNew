<?php

include 'common.php';

echo '삭제 테스트'.chr(10).'처리결과 : ' ;

try {
	$result = $TaxinvoiceService->Delete('1231212312',ENumMgtKeyType::SELL,'123123');
	echo '['.$result->code.'] '.$result->message;
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);
?>
