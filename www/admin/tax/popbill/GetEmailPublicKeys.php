<?php

include 'common.php';


echo 'ASP사업자 유통메일 목록 확인 테스트'.chr(10).'처리결과 : ' ;

try {
	$result = $TaxinvoiceService->GetEmailPublicKeys('1231212312');
	echo chr(10);
	echo json_encode($result,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);
?>