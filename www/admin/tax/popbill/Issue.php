<?php

include 'common.php';


echo '발행 테스트'.chr(10).'처리결과 : ' ;

try {
	/*
	* 발행을 위해서는 공인인증서가 등록되어 있어야 합니다.
	* 세금계산서의 발행은 공급자또는 수탁자에 의해서 이루어 지며, Issue함수의 parameter는 다음과 같다.
	* $CorpNum 		=> 행위자 사업자번호
	* $MgtKeyType 	=> 관리번호 종류, SELL(매출), BUY(매입), TRUSTEE(수탁) 중 1개 
	* $MgtKey 		=>  파트너 관리번호
	* $Memo 		=> 발행시 메모
	* $EmailSubject => 발행시 전달되는 메일의 제목을 변경하고자 할때 기재, 미기재시 기본 제목으로 전송.
	* $ForceIssue 	=> 지연발행건 강제발행여부. 해당 세금계산서의 가산세가 예상될 경우 발행되지 않고 오류를 반환한다.
	*					만약강제로 발행하고자 할 경우 true로 호출하면 발생가능하다.
	* $userid 		=> 팝빌 회원아이디
	*/
	$result = $TaxinvoiceService->Issue('1231212312',ENumMgtKeyType::SELL,'123123','발행 메모',null,false,'userid');
	echo '['.$result->code.'] '.$result->message;
}
catch(PopbillException $pe) {
	echo '['.$pe->getCode().'] '.$pe->getMessage();
}
echo chr(10);
?>
