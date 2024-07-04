<?

if($page_type == 'input'){


	/*=================================================새로운 대량상품등록 설정 시작 2014-06-18 이학봉=================================================*/
	/*	2014-08-09 이학봉 추가사항
		1. 카테고리 필수 처리 : 실제 존재하는 카테고리인지 판단후 추가 
			: |  로 구분지어 처음 카테고리가 기본카테고리로 지정. 

			: 수정시도 처음카테고리가 기본으로 되게끔 처리 

		2. 상품코드 : WMS 사용시 상단 품목과 옵션 중 하나만 사용가능 . 
				- 실제 품목코드에 EA 단위정보가 존재하는지를 판단 필요. 

		3. 원산지,제조사 : 필수 처리 
			제조사 : 텍스트 처리 (개별상품등록에서 input text box 로 처리)

		4. 상품 기본옵션 추가 

		5. 엑셀등록 상품 구분 추가 및 등록자 추가 

		6. 품목 단위정보는 EA로 기본 설정
	*/

	$goods_basic_sample[] = array("code"=>"migration_id","title"=>"상품시스템코드","desc"=>"","type"=>"","comment"=>"설명","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"product_type","title"=>"상품구분","desc"=>"","type"=>"","comment"=>"0:일반상품 11:공동구매상품","validation"=>"true","sample"=>"");
	$goods_basic_sample[] = array("code"=>"category","title"=>"카테고리","desc"=>"","type"=>"","comment"=>"첫번째 카테고리가 기본카테고리고 지정됩니다.","validation"=>"true","sample"=>"001001005000000|001001005000000|001001005000000");
	$goods_basic_sample[] = array("code"=>"md_id","title"=>"MD설정","desc"=>"","type"=>"","comment"=>"담당MD","validation"=>"false","sample"=>"ktw9");
	$goods_basic_sample[] = array("code"=>"mall_ix","title"=>"글로벌 노출설정","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"pname","title"=>"상품명(국문)","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"모두의 워킹 나시");
	$goods_basic_sample[] = array("code"=>"english_pname","title"=>"상품명(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"add_info","title"=>"색상명(국문)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"english_add_info","title"=>"색상명(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"pcode","title"=>"상품코드(품목코드)","desc"=>"","type"=>"","comment"=>"재고관리 사용이 Y 이면 필수값입니다!","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"search_keyword","title"=>"검색키워드","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"나시, 티셔츠, 여름나시");
    $goods_basic_sample[] = array("code"=>"english_search_keyword","title"=>"검색키워드(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"preface","title"=>"상품머리말 ","desc"=>"","type"=>"","comment"=>"상품 머리말을 입력해주세요","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"english_preface","title"=>"상품머리말(영문)","desc"=>"","type"=>"","comment"=>"상품 머리말을 입력해주세요","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"c_preface","title"=>"상품머리말 색상코드(HEXA코드)","desc"=>"","type"=>"","comment"=>"상품머리말색상(HEXA 코드)을 입력해 주세요","validation"=>"false","sample"=>"#000000");
    $goods_basic_sample[] = array("code"=>"laundry_cid","title"=>"세탁주의코드","desc"=>"","type"=>"","comment"=>"세탁주의관리 카테고리 코드를 입력하세요.","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"wear_info","title"=>"착장정보","desc"=>"","type"=>"","comment"=>"상품 착장정보를 입력해 주세요","validation"=>"false","sample"=>"착장정보");
    $goods_basic_sample[] = array("code"=>"filter_info","title"=>"필터정보","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"");
    $goods_basic_sample[] = array("code"=>"mandatory_type","title"=>"상품정보고시유형","desc"=>"","type"=>"","comment"=>"35","validation"=>"false","sample"=>"35");
    $goods_basic_sample[] = array("code"=>"mandatory_info","title"=>"상품고시정보","desc"=>"","type"=>"","comment"=>"순서|내용^
	순서1|내용1^
	순서2|내용2","validation"=>"false","sample"=>"01|HP^
	02|올인원^
	03|파빌리온^
	04|알수 없음^
	05|2012/09/10");
    $goods_basic_sample[] = array("code"=>"mandatory_type_global","title"=>"상품정보고시유형(영문)","desc"=>"","type"=>"","comment"=>"35","validation"=>"false","sample"=>"35");
    $goods_basic_sample[] = array("code"=>"mandatory_info_global","title"=>"상품고시정보(영문)","desc"=>"","type"=>"","comment"=>"순서|내용^
	순서1|내용1^
	순서2|내용2","validation"=>"false","sample"=>"01|HP^
	02|올인원^
	03|파빌리온^
	04|알수 없음^
	05|2012/09/10");
    //$goods_basic_sample[] = array("code"=>"shotinfo","title"=>"상품간략설명","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"미국 유명브랜드 상품!");
    //$goods_basic_sample[] = array("code"=>"english_shotinfo","title"=>"상품간략소개(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"og_ix","title"=>"원산지코드","desc"=>"","type"=>"","comment"=>"오프라인 품목코드 를 입력하시면 자동으로 저장됩니다.","validation"=>"false","sample"=>"311");
    $goods_basic_sample[] = array("code"=>"company","title"=>"제조사코드","desc"=>"","type"=>"","comment"=>"오프라인 품목코드 를 입력하시면 자동으로 저장됩니다.","validation"=>"false","sample"=>"121");
    $goods_basic_sample[] = array("code"=>"brand","title"=>"브랜드코드","desc"=>"","type"=>"","comment"=>"오프라인 품목코드 를 입력하시면 자동으로 저장됩니다.","validation"=>"false","sample"=>"2151");
    $goods_basic_sample[] = array("code"=>"surtax_yorn","title"=>"면세여부","desc"=>"","type"=>"","comment"=>"Y:면세,N:과세,P:영세","validation"=>"true","sample"=>"Y");
    $goods_basic_sample[] = array("code"=>"state","title"=>"판매상태","desc"=>"","type"=>"","comment"=>"1:판매중, 0:일시품절, 2:판매중지 7:수정대기, 6:승이내기, 8:승인거부 9:판매금지(셀러는 자동으로 등록신청중으로 등록이 됩니다.)","validation"=>"true","sample"=>"6");
    $goods_basic_sample[] = array("code"=>"disp","title"=>"노출여부","desc"=>"","type"=>"","comment"=>"1:노출함,0:노출안함","validation"=>"true","sample"=>"1");
    $goods_basic_sample[] = array("code"=>"is_sell_date","title"=>"판매기간사용여부","desc"=>"","type"=>"","comment"=>"1:사용 0:미적용","validation"=>"true","sample"=>"1");
    $goods_basic_sample[] = array("code"=>"sell_priod_date","title"=>"판매기간설정","desc"=>"","type"=>"","comment"=>"20140618-201407018","validation"=>"false","sample"=>"1");
    //$goods_basic_sample[] = array("code"=>"coupon_use_yn","title"=>"상품쿠폰 사용여부","desc"=>"","type"=>"","comment"=>"Y:사용함 N:사용안함","validation"=>"true","sample"=>"Y");
    $goods_basic_sample[] = array("code"=>"movie","title"=>"동영상URL","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"www.mallstory.com");
    $goods_basic_sample[] = array("code"=>"coprice","title"=>"공급가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"15000");
    $goods_basic_sample[] = array("code"=>"english_coprice","title"=>"공급가(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"listprice","title"=>"판매가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"25000");
    $goods_basic_sample[] = array("code"=>"english_listprice","title"=>"판매가(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"sellprice","title"=>"할인가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"20000");
    $goods_basic_sample[] = array("code"=>"english_sellprice","title"=>"할인가(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
    //$goods_basic_sample[] = array("code"=>"allow_max_cnt","title"=>"최대판매수량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10");
    //$goods_basic_sample[] = array("code"=>"wholesale_allow_byoneperson_cnt","title"=>"ID당구매수량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10");
    //$goods_basic_sample[] = array("code"=>"allow_order_type","title"=>"한정판매수량사용여부","desc"=>"","type"=>"","comment"=>"1:적용 0:미적용","validation"=>"false","sample"=>"0");
    //$goods_basic_sample[] = array("code"=>"allow_order_cnt_byonesell","title"=>"한정판매수량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"100");
    //$goods_basic_sample[] = array("code"=>"display_option","title"=>"디스플레이옵션","desc"=>"","type"=>"","comment"=>"제조사|대한민국| 해당 정보만 노출^
	//브랜드|나이키| 해당 정보만 노출","validation"=>"false","sample"=>"제조사|대한민국| 해당 정보만 노출^
	//브랜드|나이키| 해당 정보만 노출");
    $goods_basic_sample[] = array("code"=>"option_name","title"=>"옵션명(가격+재고관리)","desc"=>"","type"=>"","comment"=>"옵션명","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"english_option_name","title"=>"옵션명(가격+재고관리)(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"option_div","title"=>"옵션구분","desc"=>"","type"=>"","comment"=>"옵션구분","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"english_option_div","title"=>"옵션구분(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"stock_options_listprice","title"=>"옵션 소매판매가","desc"=>"","type"=>"","comment"=>"옵션 소매판매가","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"english_stock_options_listprice","title"=>"옵션 소매판매가(가격+재고관리)(영문)","desc"=>"","type"=>"","comment"=>"옵션상품의 소매 판매가를 입력해주세요.","validation"=>"false","sample"=>"10000|1000|1000");
    $goods_basic_sample[] = array("code"=>"stock_options_sellprice","title"=>"옵션 소매할인가","desc"=>"","type"=>"","comment"=>"옵션 소매할인가","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"english_stock_options_sellprice","title"=>"옵션 소매할인가(가격+재고관리)(영문)","desc"=>"","type"=>"","comment"=>"옵션상품의 소매 할인가를 입력해주세요.","validation"=>"false","sample"=>"10000|1000|1000");
    $goods_basic_sample[] = array("code"=>"stock_options_soldout","title"=>"옵션품절여부","desc"=>"","type"=>"","comment"=>"품절여부 품절 :1 품절아님 :0","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"stock_options_code","title"=>"옵션품목코드","desc"=>"","type"=>"","comment"=>"옵션품목코드","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"m_basicinfo","title"=>"모바일 상품상세정보","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"english_m_basicinfo","title"=>"모바일 상품상세설명(이미지)(영문)","desc"=>"","type"=>"","comment"=>"상품 상세페이지 이미지 경로를 입력해주세요.","validation"=>"false","sample"=>'<p><img src=\"http://shopdomain.co.kr/data/mallid_data/BatchUploadImages/fortune_120417\"></p>');
    $goods_basic_sample[] = array("code"=>"basicinfo","title"=>"웹 상품상세정보","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"english_basicinfo","title"=>"웹 상품상세설명(이미지)(영문)","desc"=>"","type"=>"","comment"=>"상품 상세페이지 이미지 경로를 입력해주세요.","validation"=>"false","sample"=>'<p><img src=\"http://shopdomain.co.kr/data/mallid_data/BatchUploadImages/fortune_120417\"></p>');
    $goods_basic_sample[] = array("code"=>"mainimg","title"=>"상품이미지","desc"=>"","type"=>"","comment"=>"이미지명의 사이 구분자로 | 사용. 예.image_test1.gif|image_test1.gif|image_test1.gif","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"listNum","title"=>"리스트이미지","desc"=>"","type"=>"","comment"=>"0으로 설정 시 1번 이미지가 상품리스트 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"false","sample"=>"0");
    $goods_basic_sample[] = array("code"=>"overNum","title"=>"마우스오버이미지","desc"=>"","type"=>"","comment"=>"0으로 설정 시 1번 이미지가 상품리스트 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"false","sample"=>"1");
    $goods_basic_sample[] = array("code"=>"slistNum","title"=>"리스트작은이미지","desc"=>"","type"=>"","comment"=>"0으로 설정 시 1번 이미지가 상품리스트 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"false","sample"=>"2");
    $goods_basic_sample[] = array("code"=>"nailNum","title"=>"썸네일이미지","desc"=>"","type"=>"","comment"=>"0으로 설정 시 1번 이미지가 상품리스트 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"false","sample"=>"3");
    $goods_basic_sample[] = array("code"=>"pattNum","title"=>"패턴이미지","desc"=>"","type"=>"","comment"=>"0으로 설정 시 1번 이미지가 상품리스트 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"false","sample"=>"4");
    $goods_basic_sample[] = array("code"=>"marker_left_dn","title"=>"상품마커좌측하단","desc"=>"","type"=>"","comment"=>"0으로 설정 미노출 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"false","sample"=>"0");
    $goods_basic_sample[] = array("code"=>"marker_right_dn","title"=>"상품마커우측하단","desc"=>"","type"=>"","comment"=>"0으로 설정 미노출 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"false","sample"=>"0");

}else if($page_type == 'update'){

	/*=================================================새로운 대량상품 엑셀수정 설정 시작 2014-06-18 이학봉=================================================*/
	$goods_basic_sample[] = array("code"=>"verson","code_group"=>"","title"=>"버전","desc"=>"","type"=>"","comment"=>"설명","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"id","code_group"=>"","title"=>"상품시스템코드","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"product_type","code_group"=>"","title"=>"상품구분","desc"=>"","type"=>"","comment"=>"0:일반상품 11:공동구매상품","validation"=>"true","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"category","code_group"=>"","title"=>"카테고리","desc"=>"","type"=>"","comment"=>"첫번째 카테고리가 기본카테고리고 지정됩니다.","validation"=>"true","sample"=>"001001005000000|001001005000000|001001005000000",width=>25);
	
	$goods_basic_sample[] = array("code"=>"md_id","code_group"=>"","title"=>"MD설정","desc"=>"","type"=>"","comment"=>"담당MD","validation"=>"false","sample"=>"ktw9",width=>25);
	$goods_basic_sample[] = array("code"=>"pname","code_group"=>"","title"=>"상품명","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"모두의 워킹 나시",width=>25);
    $goods_basic_sample[] = array("code"=>"english_pname","code_group"=>"","title"=>"상품명(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"",width=>25);
    $goods_basic_sample[] = array("code"=>"add_info","title"=>"색상명","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"english_add_info","title"=>"색상명(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
    //$goods_basic_sample[] = array("code"=>"gid","title"=>"상품코드(품목코드)","desc"=>"","type"=>"","comment"=>"재고관리 사용이 Y 이면 필수값입니다!","validation"=>"false","sample"=>"201501",width=>25);
	$goods_basic_sample[] = array("code"=>"pcode","code_group"=>"","title"=>"오프라인 상품코드(품목코드)","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"",width=>25);

	$goods_basic_sample[] = array("code"=>"paper_pname","code_group"=>"","title"=>"매입상품명","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"워킹 나시",width=>25);
	$goods_basic_sample[] = array("code"=>"barcode","code_group"=>"","title"=>"바코드","desc"=>"","type"=>"","comment"=>"오프라인 품목코드를 입력하시면 자동으로 저장됩니다.","validation"=>"false","sample"=>"teds15efd",width=>25);
	$goods_basic_sample[] = array("code"=>"shotinfo","code_group"=>"","title"=>"상품간략설명","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"미국 유명브랜드 상품!",width=>25);
	$goods_basic_sample[] = array("code"=>"trade_admin","code_group"=>"","title"=>"매입업체","desc"=>"","type"=>"","comment"=>"자체 상품이 아닌 매입을 통한 상품일 경우 매입처를 별도로 관리하여 효율적으로 관리하실 수 있으며, 매입처가 있는 상ㅍ무에 해당 매입업체 코드를 넣어주세요.","validation"=>"false","sample"=>"7bf64ea003a14c167a3cc69c74ee0489",width=>25);
	$goods_basic_sample[] = array("code"=>"og_ix","code_group"=>"","title"=>"원산지코드","desc"=>"","type"=>"","comment"=>"원산지 코드를 입력해주세요.","validation"=>"true","sample"=>"B001",width=>25);
	$goods_basic_sample[] = array("code"=>"company","code_group"=>"","title"=>"제조사코드","desc"=>"","type"=>"","comment"=>"제조사 텍스트로 입력해주세요","validation"=>"true","sample"=>"HP코리아",width=>25);
	$goods_basic_sample[] = array("code"=>"brand","code_group"=>"","title"=>"브랜드코드","desc"=>"","type"=>"","comment"=>"브랜드를 입력해주세요.","validation"=>"true","sample"=>"AF497835",width=>25);
	$goods_basic_sample[] = array("code"=>"surtax_yorn","code_group"=>"","title"=>"면세여부","desc"=>"","type"=>"","comment"=>"Y:면세,\nN:과세,\nP:영세","validation"=>"true","sample"=>"Y",width=>25);
	$goods_basic_sample[] = array("code"=>"state","code_group"=>"","title"=>"판매상태","desc"=>"","type"=>"","comment"=>"1:판매중, \n0:일시품절, \n2:판매중지 \n7:수정대기, \n6:승이내기, \n8:승인거부 \n9:판매금지(셀러는 자동으로 등록신청중으로 등록이 됩니다.)","validation"=>"true","sample"=>"6",width=>25);
	$goods_basic_sample[] = array("code"=>"disp","code_group"=>"","title"=>"노출여부","desc"=>"","type"=>"","comment"=>"1:노출함,\n0:노출안함","validation"=>"true","sample"=>"1",width=>25);
	$goods_basic_sample[] = array("code"=>"product_weight","code_group"=>"","title"=>"무게","desc"=>"","type"=>"","comment"=>"상품무게 KG","validation"=>"false","sample"=>"10",width=>25);
	$goods_basic_sample[] = array("code"=>"is_sell_date","code_group"=>"","title"=>"판매기간 사용여부","desc"=>"","type"=>"","comment"=>"1:사용 \n0:미적용","validation"=>"true","sample"=>"1",width=>25);
	$goods_basic_sample[] = array("code"=>"sell_priod_date","code_group"=>"","title"=>"판매기간설정","desc"=>"","type"=>"","comment"=>"20140618-201407018","validation"=>"false","sample"=>"1",width=>25);


	$goods_basic_sample[] = array("code"=>"delivery_coupon_yn","code_group"=>"","title"=>"배송쿠폰사용여부","desc"=>"","type"=>"","comment"=>"Y:사용함 \nN:사용안함","validation"=>"true","sample"=>"Y",width=>25);
	$goods_basic_sample[] = array("code"=>"coupon_use_yn","code_group"=>"","title"=>"상품쿠폰 사용여부","desc"=>"","type"=>"","comment"=>"Y:사용함 \nN:사용안함","validation"=>"true","sample"=>"Y",width=>25);
	$goods_basic_sample[] = array("code"=>"search_keyword","code_group"=>"","title"=>"검색키워드","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"나시, 티셔츠, 여름나시",width=>25);

	$goods_basic_sample[] = array("code"=>"stock_use_yn","code_group"=>"","title"=>"재고관리 사용여부","desc"=>"","type"=>"","comment"=>"Y:(WMS)사용,\nN:사용안함,\nQ:빠른재고관리","validation"=>"true","sample"=>"Q",width=>25);

	$goods_basic_sample[] = array("code"=>"safestock","code_group"=>"","title"=>"안전재고","desc"=>"","type"=>"","comment"=>"안전재고","validation"=>"false","sample"=>"200",width=>25);
	$goods_basic_sample[] = array("code"=>"stock","code_group"=>"","title"=>"실재고","desc"=>"","type"=>"","comment"=>"실재고","validation"=>"false","sample"=>"100",width=>25);

	$goods_basic_sample[] = array("code"=>"movie","code_group"=>"","title"=>"동영상 URL","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"www.mallstory.com",width=>25);
	$goods_basic_sample[] = array("code"=>"make_date","code_group"=>"","title"=>"제조일자","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"20120704",width=>25);
	$goods_basic_sample[] = array("code"=>"expiry_date","code_group"=>"","title"=>"유효일","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"20130504",width=>25);

	$goods_basic_sample[] = array("code"=>"is_adult","code_group"=>"","title"=>"19금상품","desc"=>"","type"=>"","comment"=>"0:미적용 1:적용","validation"=>"true","sample"=>"1",width=>25);
	$goods_basic_sample[] = array("code"=>"coprice","code_group"=>"","title"=>"공급가","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"15000",width=>25);
	$goods_basic_sample[] = array("code"=>"wholesale_price","code_group"=>"","title"=>"도매판매가","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"25000",width=>25);
	$goods_basic_sample[] = array("code"=>"wholesale_sellprice","code_group"=>"","title"=>"도매할인가","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"20000",width=>25);
	$goods_basic_sample[] = array("code"=>"listprice","code_group"=>"","title"=>"소매판매가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"25000",width=>25);
	$goods_basic_sample[] = array("code"=>"sellprice","code_group"=>"","title"=>"소매할인가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"20000",width=>25);
	$goods_basic_sample[] = array("code"=>"premiumprice","code_group"=>"","title"=>"프리미엄가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"18000",width=>25);
	$goods_basic_sample[] = array("code"=>"allow_basic_cnt","code_group"=>"","title"=>"기본시작수량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10",width=>25);
	$goods_basic_sample[] = array("code"=>"allow_max_cnt","code_group"=>"","title"=>"최대판매수량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10",width=>25);

	$goods_basic_sample[] = array("code"=>"allow_byoneperson_cnt","code_group"=>"","title"=>"ID당구매수량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10",width=>25);
	$goods_basic_sample[] = array("code"=>"allow_order_type","code_group"=>"","title"=>"한정판매수량 사용여부","desc"=>"","type"=>"","comment"=>"1:적용 0:미적용","validation"=>"false","sample"=>"0",width=>25);
	$goods_basic_sample[] = array("code"=>"allow_order_cnt_byonesell","code_group"=>"","title"=>"한정판매수량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"100",width=>25);

	$goods_basic_sample[] = array("code"=>"delivery_type","code_group"=>"","title"=>"배송타입","desc"=>"","type"=>"","comment"=>"1:통합배송 2:입점업체 배송","validation"=>"true","sample"=>"2",width=>25);
	$goods_basic_sample[] = array("code"=>"one_commission","code_group"=>"","title"=>"개별수수료 사용여부","desc"=>"","type"=>"","comment"=>"N:사용안함 Y:사용","validation"=>"false","sample"=>"N",width=>25);
	$goods_basic_sample[] = array("code"=>"account_type","code_group"=>"","title"=>"정산방식","desc"=>"","type"=>"","comment"=>"1:판매가정산 2:매입가정산 3:미정산","validation"=>"false","sample"=>"1",width=>25);
	$goods_basic_sample[] = array("code"=>"commission","code_group"=>"","title"=>"정산 수수료","desc"=>"","type"=>"","comment"=>"10|20","validation"=>"false","sample"=>"10|20",width=>25);

	$goods_basic_sample[] = array("code"=>"dp_ix","code_group"=>"display_options","color"=>"#D9534F","title"=>"디스플레이옵션키","desc"=>"","type"=>"","comment"=>"10|20","validation"=>"false","sample"=>"10|20",width=>25);
	$goods_basic_sample[] = array("code"=>"display_option","code_group"=>"display_options","color"=>"#D9534F","title"=>"디스플레이옵션","desc"=>"","type"=>"","comment"=>"제조사|대한민국| 해당 정보만 노출^
	브랜드|나이키| 해당 정보만 노출","validation"=>"false","sample"=>"제조사|대한민국| 해당 정보만 노출^
	브랜드|나이키| 해당 정보만 노출",width=>25);

	$goods_basic_sample[] = array("code"=>"vi_ix","code_group"=>"virals","color"=>"#5BC0DE","title"=>"바이럴등록키","desc"=>"","type"=>"","comment"=>"10|20","validation"=>"false","sample"=>"10|20",width=>25);
	$goods_basic_sample[] = array("code"=>"virals","code_group"=>"virals","color"=>"#5BC0DE","title"=>"바이럴 등록","desc"=>"","type"=>"","comment"=>"카페&블로그명|URL|기타설명^
	카페&블로그명1|URL1|기타설명1^
	카페&블로그명2|URL2|기타설명2","validation"=>"false","sample"=>"몰스토리|mallstory.com|운영카페^
	다이소몰|daisomall.co.kr|운영쇼핑몰",width=>25);

	$goods_basic_sample[] = array("code"=>"mandatory_type","code_group"=>"mandatory","color"=>"#1CAF9A","title"=>"상품정보 고시유형","desc"=>"","type"=>"","comment"=>"35","validation"=>"false","sample"=>"35",width=>25);
	$goods_basic_sample[] = array("code"=>"mandatory_info","code_group"=>"mandatory","color"=>"#1CAF9A","title"=>"상품고시정보","desc"=>"","type"=>"","comment"=>"순서|내용^
	순서1|내용1^
	순서2|내용2","validation"=>"false","sample"=>"01|HP^
	02|올인원^
	03|파빌리온^
	04|알수 없음^
	05|2012/09/10",width=>25);
    $goods_basic_sample[] = array("code"=>"pmi_ix","code_group"=>"mandatory","color"=>"#1CAF9A","title"=>"상품고시정보키값","desc"=>"","type"=>"","comment"=>"10|20","validation"=>"false","sample"=>"10|20",width=>25);
    $goods_basic_sample[] = array("code"=>"mandatory_type_global","code_group"=>"mandatory_global","color"=>"#1CAF9A","title"=>"상품정보 고시유형(영문)","desc"=>"","type"=>"","comment"=>"35","validation"=>"false","sample"=>"35",width=>25);
    $goods_basic_sample[] = array("code"=>"mandatory_info_global","code_group"=>"mandatory_global","color"=>"#1CAF9A","title"=>"상품고시정보(영문)","desc"=>"","type"=>"","comment"=>"순서|내용^
	순서1|내용1^
	순서2|내용2","validation"=>"false","sample"=>"01|HP^
	02|올인원^
	03|파빌리온^
	04|알수 없음^
	05|2012/09/10",width=>25);
    $goods_basic_sample[] = array("code"=>"pmi_ix_global","code_group"=>"mandatory_global","color"=>"#1CAF9A","title"=>"상품고시정보키값(영문)","desc"=>"","type"=>"","comment"=>"10|20","validation"=>"false","sample"=>"10|20",width=>25);
	$goods_basic_sample[] = array("code"=>"m_basicinfo","code_group"=>"","title"=>"모바일 상품상세정보","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"basicinfo","code_group"=>"","title"=>"웹 상품상세정보","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"mainimg","code_group"=>"","title"=>"상품이미지","desc"=>"","type"=>"","comment"=>"이미지는 FTP에서  '/www".$admininfo[mall_data_root]."/BatchUploadImages'  경로에 업로드 해주시면 됩니다.","validation"=>"false","sample"=>"",width=>25);

	//기본옵션 시작 
	$goods_basic_sample[] = array("code"=>"basic_opn_ix","code_group"=>"basic_option","color"=>"#428BCA","title"=>"기본옵션키","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"71947^71948^71949",width=>25);
	$goods_basic_sample[] = array("code"=>"basic_option_name","code_group"=>"basic_option","color"=>"#428BCA","title"=>"기본옵션명","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"옵션명1^옵션명2^옵션명3",width=>25);
	$goods_basic_sample[] = array("code"=>"basic_option_use","code_group"=>"basic_option","color"=>"#428BCA","title"=>"기본옵션_사용여부","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"1^1^1",width=>25);
	$goods_basic_sample[] = array("code"=>"basic_option_kind","code_group"=>"basic_option","color"=>"#428BCA","title"=>"옵션종류","desc"=>"","type"=>"","comment"=>"조합옵션(필수) :c1 조합옵션(선택) :c2 독립옵션(필수) :i1 독립옵션(선택) :i2","validation"=>"false","sample"=>"c1^c2^i1",width=>25);
	
	$goods_basic_sample[] = array("code"=>"basic_option_soldout","code_group"=>"basic_option","color"=>"#428BCA","title"=>"품절여부","desc"=>"","type"=>"","comment"=>"품절 : 1 품절아님 : 0","validation"=>"false","sample"=>"0|0|1^1|1|0^0|0|0",width=>25);
	
	$goods_basic_sample[] = array("code"=>"basic_option_div","code_group"=>"basic_option","color"=>"#428BCA","title"=>"기본옵션구분","desc"=>"","type"=>"","comment"=>"옵션구분명","validation"=>"false","sample"=>"옵션구분1_1|옵션구분1_2|옵션구분1_3^옵션구분2_1|옵션구분2_2|옵션구분2_3^옵션구분3_1|옵션구분3_2|옵션구분3_3",width=>25);
	$goods_basic_sample[] = array("code"=>"basic_option_price","code_group"=>"basic_option","color"=>"#428BCA","title"=>"기본옵션추가가격","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"1000|1000|1000^2000|2000|2000^3000|3000|3000",width=>25);
	$goods_basic_sample[] = array("code"=>"basic_opd_ix","code_group"=>"basic_option","color"=>"#428BCA","title"=>"기본옵션_구분키","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"334871|334872|334873^334874|334875|334876^334877|334878|334879",width=>25);

	//가격재고관리 옵션 시작
	$goods_basic_sample[] = array("code"=>"opn_ix","code_group"=>"stock_option","color"=>"#D9534F","title"=>"옵션키","desc"=>"","type"=>"","comment"=>"옵션키","validation"=>"false","sample"=>"227",width=>25);
	$goods_basic_sample[] = array("code"=>"option_name","code_group"=>"stock_option","color"=>"#D9534F","title"=>"옵션명","desc"=>"","type"=>"","comment"=>"옵션명","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"option_div","code_group"=>"stock_option","color"=>"#D9534F","title"=>"옵션구분","desc"=>"","type"=>"","comment"=>"옵션구분","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"stock_options_coprice","code_group"=>"stock_option","color"=>"#D9534F","title"=>"옵션공급가격","desc"=>"","type"=>"","comment"=>"옵션공급가격","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"stock_options_wholesale_listprice","code_group"=>"stock_option","color"=>"#D9534F","title"=>"옵션 도매판매가","desc"=>"","type"=>"","comment"=>"옵션 도매판매가","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"stock_options_wholesale_price","code_group"=>"stock_option","color"=>"#D9534F","title"=>"옵션 도매할인가","desc"=>"","type"=>"","comment"=>"옵션 도매할인가","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"stock_options_listprice","code_group"=>"stock_option","color"=>"#D9534F","title"=>"옵션 소매판매가","desc"=>"","type"=>"","comment"=>"옵션 소매판매가","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"stock_options_sellprice","code_group"=>"stock_option","color"=>"#D9534F","title"=>"옵션 소매할인가","desc"=>"","type"=>"","comment"=>"옵션 소매할인가","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"stock_options_stock","code_group"=>"stock_option","color"=>"#D9534F","title"=>"실재고","desc"=>"","type"=>"","comment"=>"실재고","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"stock_option_soldout","code_group"=>"stock_option","color"=>"#D9534F","title"=>"옵션품절여부","desc"=>"","type"=>"","comment"=>"옵션품절여부","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"stock_options_safestock","code_group"=>"stock_option","color"=>"#D9534F","title"=>"안전재고","desc"=>"","type"=>"","comment"=>"안전재고","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"stock_options_code","code_group"=>"stock_option","color"=>"#D9534F","title"=>"옵션품목코드","desc"=>"","type"=>"","comment"=>"옵션품목코드","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"stock_options_barcode","code_group"=>"stock_option","color"=>"#D9534F","title"=>"옵션바코드","desc"=>"","type"=>"","comment"=>"옵션바코드","validation"=>"false","sample"=>"",width=>25);

	$goods_basic_sample[] = array("code"=>"naver_use","code_group"=>"","title"=>"지식쇼핑","desc"=>"","type"=>"","comment"=>"지식쇼핑","validation"=>"false","sample"=>"",width=>25);
	$goods_basic_sample[] = array("code"=>"daum_use","code_group"=>"","title"=>"다음쇼핑하우","desc"=>"","type"=>"","comment"=>"다음쇼핑하우","validation"=>"false","sample"=>"",width=>25);
    $goods_basic_sample[] = array("code"=>"preface","title"=>"상품머리말 ","desc"=>"","type"=>"","comment"=>"상품 머리말을 입력해주세요","validation"=>"false","sample"=>"");
    $goods_basic_sample[] = array("code"=>"english_preface","title"=>"상품머리말(영문)","desc"=>"","type"=>"","comment"=>"상품 머리말을 입력해주세요","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"laundry_cid","title"=>"세탁주의코드","desc"=>"","type"=>"","comment"=>"세탁주의관리 카테고리 코드를 입력하세요.","validation"=>"false","sample"=>"");
	/*=================================================새로운 대량상품 엑셀수정 설정 끝 2014-06-18 이학봉=================================================*/

}

if(substr($parameter_1,0,1)=='0')$parameter_1 = substr($parameter_1,1,1);

if($act=="get_mandatory_info"){
	$mandatory_infos = str_replace("\"true\"","true",json_encode($goods_mandatory_info[$parameter_1][$parameter_2][$parameter_3]));
	$mandatory_infos = str_replace("\"false\"","false",$mandatory_infos);
	echo $mandatory_infos;
	exit;
}

?>