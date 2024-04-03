<?php

include 'common.php';

echo '세금계산서 요약정보 대량 확인 테스트'.chr(10).'처리결과 : ' ;

$MgtKeyList = array(
		'123123',
		'123123123',
		'123123123123',
		'1',
		'2'
);
try {
	$result = $TaxinvoiceService->GetInfos('1231212312',ENumMgtKeyType::SELL,$MgtKeyList);
	echo chr(10);
	echo json_encode($result,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);
?>