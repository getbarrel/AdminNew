<?php

include 'common.php';


echo '발급 단가 확인 테스트'.chr(10).'처리결과 : ' ;

try {
	$result = $TaxinvoiceService->GetUnitCost('1231212312');
	echo $result;
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);
?>
