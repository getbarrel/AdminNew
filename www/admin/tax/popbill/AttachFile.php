<?php

include 'common.php';

echo '파일첨부 테스트'.chr(10).'현재 폴더의 uploadtest.jpg파일이 첨부됩니다.'.chr(10).'처리결과 : ' ;

//파일 첨부
try {
	//해당 파일에 대한 읽기 권한이 설정되어 있어야 합니다.
	$filepath = './uploadtest.jpg';
	$result = $TaxinvoiceService->AttachFile('1231212312',EnumMgtKeyType::SELL,'123123',$filepath,'userid');
	echo $result->message;
}
catch(PopbillException $pe) {
	echo $pe->getMessage();
}

echo chr(10);

?>
