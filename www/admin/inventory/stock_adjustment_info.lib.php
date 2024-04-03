<?
if($page_type == 'input'){

	$goods_basic_sample[] = array("code"=>"verson","title"=>"버전","desc"=>"","type"=>"","comment"=>"설명","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"stock_type","title"=>"재고조정타입","desc"=>"재고조정타입","type"=>"","comment"=>"재고조정타입을 입력해주세요.1:입고조정 2:출고조정 3:기초조정","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"gid","title"=>"품목코드","desc"=>"","type"=>"","comment"=>"품목코드","validation"=>"true","sample"=>"3606700");
	$goods_basic_sample[] = array("code"=>"vdate","title"=>"입고일","desc"=>"","type"=>"","comment"=>"입고일을 입력해주세요.","validation"=>"true","sample"=>"2014-10-10");

	$goods_basic_sample[] = array("code"=>"charger_id","title"=>"담당자아이디","desc"=>"","type"=>"","comment"=>"입고 담당자 아이디를 입력해주세요","validation"=>"true","sample"=>"ktw100");

	$goods_basic_sample[] = array("code"=>"regist_company_id","title"=>"조정창고(사업장)","desc"=>"","type"=>"","comment"=>"입고창고 키값 
	(주)한웰이쇼핑 : 362ed8ee1cba4cc34f80aa5529d2fbcd ","validation"=>"true","sample"=>"362ed8ee1cba4cc34f80aa5529d2fbcd");

	$goods_basic_sample[] = array("code"=>"regist_pi_ix","title"=>"조정창고(창고)","desc"=>"","type"=>"","comment"=>"남사물류센터 : 1
기흥물류 : 2
본사샘플 : 3","validation"=>"true","sample"=>"1");

	$goods_basic_sample[] = array("code"=>"regist_ps_ix","title"=>"조정창고(보관장소)","desc"=>"","type"=>"","comment"=>"조정보관장소 키를 넣으주세요.","validation"=>"false","sample"=>"1");

	$goods_basic_sample[] = array("code"=>"ci_ix","title"=>"매입처코드","desc"=>"","type"=>"","comment"=>"주매입처의 상품 코드를 넣어주세요.","validation"=>"true","sample"=>"31bc0b29a9f9f57ac0694a226bb374de");

	$goods_basic_sample[] = array("code"=>"h_type","title"=>"조정유형","desc"=>"","type"=>"","comment"=>"01 : 상품매입
09 : 폐기(손/망실)
FC : 기초조정","validation"=>"true","sample"=>"01");

	$goods_basic_sample[] = array("code"=>"gname","title"=>"품목명","desc"=>"","type"=>"","comment"=>"품목명을 입력해주세요.","validation"=>"true","sample"=>"블링블링 반팔티");


	$goods_basic_sample[] = array("code"=>"standard","title"=>"규격","desc"=>"","type"=>"","comment"=>"규격(옵션)을 입력해주세요.","validation"=>"false","sample"=>"색상");


	$goods_basic_sample[] = array("code"=>"item_account","title"=>"품목계정","desc"=>"","type"=>"","comment"=>"1 : 원재료
2 : 부재료
3 : 반제품
4 : 완제품(상품)
5 : 용역
6 : 저장품
7 : 가상품목","validation"=>"true","sample"=>"1");

	$goods_basic_sample[] = array("code"=>"unit","title"=>"매입단위","desc"=>"","type"=>"","comment"=>"1 : EA
2 : Kg
3 : m2
4 : Roll
5 : BOX
6 : Pack
7 : 생산단위
8 : 식","validation"=>"true","sample"=>"1");

	$goods_basic_sample[] = array("code"=>"surtax_div","title"=>"부가세적용","desc"=>"","type"=>"","comment"=>"1: 부과세 포함
2 : 부과세 별도
3 : 영세율 적용
4 : 면세율 적용
5 : 부가세 없음","validation"=>"false","sample"=>"1");

	$goods_basic_sample[] = array("code"=>"expiry_date","title"=>"유효기간","desc"=>"","type"=>"","comment"=>"2014-10-10","validation"=>"true","sample"=>"2014-10-10");

	$goods_basic_sample[] = array("code"=>"amount","title"=>"입/출고 수량","desc"=>"","type"=>"","comment"=>"입/출고 할 수량을 입력해주세요.","validation"=>"true","sample"=>"10");


	$goods_basic_sample[] = array("code"=>"price","title"=>"매입가","desc"=>"","type"=>"","comment"=>"* 숫자로 유요한 일을 입력해주세요.","validation"=>"true","sample"=>"180");

	$goods_basic_sample[] = array("code"=>"etc","title"=>"비고","desc"=>"","type"=>"","comment"=>"입구 참고정보를 입력해주세요.","validation"=>"false","sample"=>"비고내용 입력");

}else if($page_type == 'update'){

	
}
?>