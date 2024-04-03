<?php

include 'common.php';

echo '세금계산서 요약정보 및 상태정보 확인 테스트'.chr(10).'처리결과 : ' ;

try {
	$result = $TaxinvoiceService->GetInfo('2208769701',ENumMgtKeyType::SELL,'661');
	echo chr(10);
	echo json_encode($result,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);
?>