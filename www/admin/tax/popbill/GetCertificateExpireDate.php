<?php

include 'common.php';

echo '등록공인인증서의 만료일 확인 테스트'.chr(10).'처리결과 : ' ;

try {
	$result = $TaxinvoiceService->GetCertificateExpireDate('1231212312');
	echo $result;
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);
?>
