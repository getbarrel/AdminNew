<?
if($page_type == 'input'){
/*=================================================새로운 대량품목등록 설정 시작 2014-06-18 이학봉=================================================*/
	$goods_basic_sample[] = array("code"=>"verson","title"=>"버전","desc"=>"","type"=>"","comment"=>"설명","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"selected_cid","title"=>"품목분류","desc"=>"","type"=>"","comment"=>"품목분류 001001005000","validation"=>"false","sample"=>"001001005000");
	$goods_basic_sample[] = array("code"=>"item_account","title"=>"품목계정","desc"=>"","type"=>"","comment"=>"품목 계정을 선택하여 입력해주세요.","validation"=>"true","sample"=>"1 : 원재료
	2 : 부재료
	3 : 반제품
	4 : 완제품(상품)
	5 : 용역
	6 : 저장품
	7 : 가상품목");
	$goods_basic_sample[] = array("code"=>"is_use","title"=>"사용여부","desc"=>"","type"=>"","comment"=>"Y 또는 N 으로 입력해주세요.","validation"=>"true","sample"=>"Y");
	$goods_basic_sample[] = array("code"=>"gcode","title"=>"대표코드","desc"=>"","type"=>"","comment"=>"* 대표코드는 숫자,영문을 포함 30자 내로 입력해주세요.","validation"=>"true","sample"=>"567100");
	$goods_basic_sample[] = array("code"=>"status","title"=>"판매상태","desc"=>"","type"=>"","comment"=>"* 필드가 없는 경우 품목이 등록 되지 않습니다.","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"gname","title"=>"품목명","desc"=>"","type"=>"","comment"=>"* 품목명은 한글,숫자,영문, 특수문자 포함 30자 내로 입력해주세요.","validation"=>"true","sample"=>"블링블링 반팔티 ");
	$goods_basic_sample[] = array("code"=>"model","title"=>"모델명","desc"=>"","type"=>"","comment"=>"* 모델명은 한글,숫자,영문, 특수문자 포함 30자 내로 입력해주세요.","validation"=>"false","sample"=>"블링블링 반팔티");
	$goods_basic_sample[] = array("code"=>"basic_unit","title"=>"기본단위","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"surtax_div","title"=>"부가세적용","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"admin_type","title"=>"품목구분","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"A");
	$goods_basic_sample[] = array("code"=>"ci_ix","title"=>"주매입처","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"31bc0b29a9f9f57ac0694a226bb374de");
	$goods_basic_sample[] = array("code"=>"og_ix","title"=>"원산지","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"B001");
	$goods_basic_sample[] = array("code"=>"c_ix","title"=>"제조사","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"C00231341");

	$goods_basic_sample[] = array("code"=>"b_ix","title"=>"브랜드","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"6");
	$goods_basic_sample[] = array("code"=>"available_priod","title"=>"유효기간","desc"=>"","type"=>"","comment"=>"* 숫자로 유요한 일을 입력해주세요.","validation"=>"false","sample"=>"180");
	$goods_basic_sample[] = array("code"=>"material","title"=>"소재/재질","desc"=>"","type"=>"","comment"=>"소재/재질","validation"=>"false","sample"=>"합성섬유");
	$goods_basic_sample[] = array("code"=>"glevel","title"=>"품목등급","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"A++");
	$goods_basic_sample[] = array("code"=>"kc_mark","title"=>"KC 인증여부","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"Y");
	$goods_basic_sample[] = array("code"=>"hc_code","title"=>"HC 코드","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"AAF1381");
	$goods_basic_sample[] = array("code"=>"bs_goods_url","title"=>"품목구매URL","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"www.mallstory.com");
	$goods_basic_sample[] = array("code"=>"search_keyword","title"=>"검색키워드","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"나시, 티셔츠, 여름나시");
	$goods_basic_sample[] = array("code"=>"allimg","title"=>"품목이미지","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"bimg_text","title"=>"이미지URL","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"www.mallstory.com");
	$goods_basic_sample[] = array("code"=>"leadtime","title"=>"LEAD TIME","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"100");
	$goods_basic_sample[] = array("code"=>"available_amountperday","title"=>"일별생산량/구매가능량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"100");
	$goods_basic_sample[] = array("code"=>"valuation","title"=>"재고평가","desc"=>"","type"=>"","comment"=>"
	1 : 이동식 평균법
	2 : 선입선출법
	3 : 후입선출법
	4 : 평행","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"lotno","title"=>"생산라인번호","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"12000");
	$goods_basic_sample[] = array("code"=>"unit","title"=>"단위정보","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"1|3|5|2");
	$goods_basic_sample[] = array("code"=>"change_amount","title"=>"단위당 수량(환산)","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"1.0|2|2.3|12|3");
	$goods_basic_sample[] = array("code"=>"buying_price","title"=>"기본매입가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"10000|100|1000");
	$goods_basic_sample[] = array("code"=>"wholesale_price","title"=>"기본도매가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"10000|100|1000");
	$goods_basic_sample[] = array("code"=>"sellprice","title"=>"기본소매가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"10000|100|1000");
	$goods_basic_sample[] = array("code"=>"weight","title"=>"무게","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10|10|10");
	$goods_basic_sample[] = array("code"=>"length_info","title"=>"부피","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10");
	$goods_basic_sample[] = array("code"=>"standard_name","title"=>"구격(옵션)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10");
	$goods_basic_sample[] = array("code"=>"standard_gid","title"=>"품목코드","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"113120");
	$goods_basic_sample[] = array("code"=>"standard_barcode","title"=>"바코드","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"100");
	$goods_basic_sample[] = array("code"=>"standard_etc","title"=>"비고","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"비고내요 입력");
	$goods_basic_sample[] = array("code"=>"p_no","title"=>"번호","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"company_id","title"=>"입력업체","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"1");

	/*=================================================새로운 대량품목등록 설정 끝 2014-06-18 이학봉=================================================*/

}else if($page_type == 'update'){

	$goods_basic_sample[] = array("code"=>"verson","title"=>"버전","desc"=>"","type"=>"","comment"=>"설명","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"gid","title"=>"품목코드","desc"=>"","type"=>"","comment"=>"품목코드 360611","validation"=>"true","sample"=>"360611");
	$goods_basic_sample[] = array("code"=>"selected_cid","title"=>"품목분류","desc"=>"","type"=>"","comment"=>"품목분류 001001005000","validation"=>"false","sample"=>"001001005000");
	$goods_basic_sample[] = array("code"=>"item_account","title"=>"품목계정","desc"=>"","type"=>"","comment"=>"품목 계정을 선택하여 입력해주세요.","validation"=>"true","sample"=>"1 : 원재료
	2 : 부재료
	3 : 반제품
	4 : 완제품(상품)
	5 : 용역
	6 : 저장품
	7 : 가상품목");
	$goods_basic_sample[] = array("code"=>"is_use","title"=>"사용여부","desc"=>"","type"=>"","comment"=>"Y 또는 N 으로 입력해주세요.","validation"=>"true","sample"=>"Y");

	$goods_basic_sample[] = array("code"=>"gcode","title"=>"대표코드","desc"=>"","type"=>"","comment"=>"* 대표코드는 숫자,영문을 포함 30자 내로 입력해주세요.","validation"=>"true","sample"=>"567100");

	$goods_basic_sample[] = array("code"=>"status","title"=>"판매상태","desc"=>"","type"=>"","comment"=>"* 필드가 없는 경우 품목이 등록 되지 않습니다.","validation"=>"true","sample"=>"1");

	$goods_basic_sample[] = array("code"=>"gname","title"=>"품목명","desc"=>"","type"=>"","comment"=>"* 품목명은 한글,숫자,영문, 특수문자 포함 30자 내로 입력해주세요.","validation"=>"true","sample"=>"블링블링 반팔티 ");

	$goods_basic_sample[] = array("code"=>"model","title"=>"모델명","desc"=>"","type"=>"","comment"=>"* 모델명은 한글,숫자,영문, 특수문자 포함 30자 내로 입력해주세요.","validation"=>"false","sample"=>"블링블링 반팔티");

	$goods_basic_sample[] = array("code"=>"basic_unit","title"=>"기본단위","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"order_basic_unit","title"=>"매입단위","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"surtax_div","title"=>"부가세적용","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"admin_type","title"=>"품목구분","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"A");

	$goods_basic_sample[] = array("code"=>"ci_ix","title"=>"주매입처","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"31bc0b29a9f9f57ac0694a226bb374de");
	$goods_basic_sample[] = array("code"=>"og_ix","title"=>"원산지","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"c_ix","title"=>"제조사","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10");
	$goods_basic_sample[] = array("code"=>"b_ix","title"=>"브랜드","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"6");

	$goods_basic_sample[] = array("code"=>"available_priod","title"=>"유효기간","desc"=>"","type"=>"","comment"=>"* 숫자로 유요한 일을 입력해주세요.","validation"=>"false","sample"=>"180");
	$goods_basic_sample[] = array("code"=>"material","title"=>"소재/재질","desc"=>"","type"=>"","comment"=>"소재/재질","validation"=>"false","sample"=>"합성섬유");

	$goods_basic_sample[] = array("code"=>"glevel","title"=>"품목등급","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"A++");
	$goods_basic_sample[] = array("code"=>"kc_mark","title"=>"KC 인증여부","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"Y");
	$goods_basic_sample[] = array("code"=>"hc_code","title"=>"HC 코드","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"AAF1381");

	$goods_basic_sample[] = array("code"=>"bs_goods_url","title"=>"품목구매URL","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"www.mallstory.com");
	$goods_basic_sample[] = array("code"=>"search_keyword","title"=>"검색키워드","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"나시, 티셔츠, 여름나시");

	$goods_basic_sample[] = array("code"=>"leadtime","title"=>"LEAD TIME","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"100");
	$goods_basic_sample[] = array("code"=>"available_amountperday","title"=>"일별생산량/구매가능량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"100");
	$goods_basic_sample[] = array("code"=>"valuation","title"=>"재고평가","desc"=>"","type"=>"","comment"=>"
	1 : 이동식 평균법
	2 : 선입선출법
	3 : 후입선출법
	4 : 평행","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"lotno","title"=>"생산라인번호","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"12000");
	$goods_basic_sample[] = array("code"=>"unit","title"=>"단위정보","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"1|3|5|2");
	$goods_basic_sample[] = array("code"=>"change_amount","title"=>"단위당 수량(환산)","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"1.0|2|2.3|12|3");
	$goods_basic_sample[] = array("code"=>"buying_price","title"=>"기본매입가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"10000|100|1000");
	$goods_basic_sample[] = array("code"=>"wholesale_price","title"=>"기본도매가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"10000|100|1000");
	$goods_basic_sample[] = array("code"=>"sellprice","title"=>"기본소매가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"10000|100|1000");
	$goods_basic_sample[] = array("code"=>"weight","title"=>"무게","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10|10|10");
	$goods_basic_sample[] = array("code"=>"length_info","title"=>"부피","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10");
	$goods_basic_sample[] = array("code"=>"standard_name","title"=>"구격(옵션)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10");
	$goods_basic_sample[] = array("code"=>"standard_gid","title"=>"품목코드","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"113120");
	$goods_basic_sample[] = array("code"=>"standard_barcode","title"=>"바코드","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"100");
	$goods_basic_sample[] = array("code"=>"standard_etc","title"=>"비고","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"비고내용 입력");
	$goods_basic_sample[] = array("code"=>"company_id","title"=>"재고관리업체","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"allimg","title"=>"품목이미지","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"bimg_text","title"=>"이미지URL","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"www.mallstory.com");
}
?>