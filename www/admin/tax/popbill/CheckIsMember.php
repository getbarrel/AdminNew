<?php

include 'common.php';


echo '팝빌 연동회원 가입 여부 확인 테스트'.chr(10).'처리결과 : ' ;


try
{
	$result = $TaxinvoiceService->CheckIsMember('1231212312',$LinkID);
	var_dump($result);
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);

?>
