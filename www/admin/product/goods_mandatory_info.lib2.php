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

	$goods_basic_sample[] = array("code"=>"verson","title"=>"버전","desc"=>"","type"=>"","comment"=>"설명","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"product_type","title"=>"상품구분","desc"=>"","type"=>"","comment"=>"0:일반상품 11:공동구매상품","validation"=>"true","sample"=>"");
	$goods_basic_sample[] = array("code"=>"category","title"=>"카테고리","desc"=>"","type"=>"","comment"=>"첫번째 카테고리가 기본카테고리고 지정됩니다.","validation"=>"true","sample"=>"001001005000000|001001005000000|001001005000000");
	$goods_basic_sample[] = array("code"=>"md_id","title"=>"MD설정","desc"=>"","type"=>"","comment"=>"담당MD","validation"=>"false","sample"=>"ktw9");
	$goods_basic_sample[] = array("code"=>"pname","title"=>"상품명","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"모두의 워킹 나시");
	//$goods_basic_sample[] = array("code"=>"gid","title"=>"상품코드","desc"=>"","type"=>"","comment"=>"재고관리 사용이 Y 이면 필수값입니다!","validation"=>"false","sample"=>"201501");
	$goods_basic_sample[] = array("code"=>"pcode","title"=>"오프라인 상품코드(품목코드)","desc"=>"","type"=>"","comment"=>"재고관리 사용이 Y 이면 필수값입니다!","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"com_name","title"=>"입점업체명","desc"=>"","type"=>"","comment"=>"입점업체명","validation"=>"false","sample"=>"");

	$goods_basic_sample[] = array("code"=>"paper_pname","title"=>"매입상품명","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"워킹 나시");
	$goods_basic_sample[] = array("code"=>"barcode","title"=>"바코드","desc"=>"","type"=>"","comment"=>"오프라인 품목코드를 입력하시면 자동으로 저장됩니다.","validation"=>"false","sample"=>"teds15efd");
	$goods_basic_sample[] = array("code"=>"shotinfo","title"=>"상품간략설명","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"미국 유명브랜드 상품!");
	//$goods_basic_sample[] = array("code"=>"admin","title"=>"입점업체","desc"=>"","type"=>"","comment"=>"시스템 발급","validation"=>"true","sample"=>"4ee047bf64ea00378a14c167a3cc69c9");
	$goods_basic_sample[] = array("code"=>"trade_admin","title"=>"매입업체","desc"=>"","type"=>"","comment"=>"오프라인 품목코드 를 입력하시면 자동으로 저장됩니다.","validation"=>"false","sample"=>"7bf64ea003a14c167a3cc69c74ee0489");
	$goods_basic_sample[] = array("code"=>"og_ix","title"=>"원산지코드","desc"=>"","type"=>"","comment"=>"오프라인 품목코드 를 입력하시면 자동으로 저장됩니다.","validation"=>"true","sample"=>"311");
	$goods_basic_sample[] = array("code"=>"company","title"=>"제조사코드","desc"=>"","type"=>"","comment"=>"오프라인 품목코드 를 입력하시면 자동으로 저장됩니다.","validation"=>"true","sample"=>"121");
	$goods_basic_sample[] = array("code"=>"brand","title"=>"브랜드코드","desc"=>"","type"=>"","comment"=>"오프라인 품목코드 를 입력하시면 자동으로 저장됩니다.","validation"=>"true","sample"=>"2151");
	$goods_basic_sample[] = array("code"=>"surtax_yorn","title"=>"면세여부","desc"=>"","type"=>"","comment"=>"Y:면세,N:과세,P:영세","validation"=>"true","sample"=>"Y");
	$goods_basic_sample[] = array("code"=>"state","title"=>"판매상태","desc"=>"","type"=>"","comment"=>"1:판매중, 0:일시품절, 2:판매중지 7:수정대기, 6:승이내기, 8:승인거부 9:판매금지(셀러는 자동으로 등록신청중으로 등록이 됩니다.)","validation"=>"true","sample"=>"6");
	$goods_basic_sample[] = array("code"=>"disp","title"=>"노출여부","desc"=>"","type"=>"","comment"=>"1:노출함,0:노출안함","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"product_weight","title"=>"무게","desc"=>"","type"=>"","comment"=>"상품무게 KG","validation"=>"false","sample"=>"10");
	$goods_basic_sample[] = array("code"=>"is_sell_date","title"=>"판매기간 사용여부","desc"=>"","type"=>"","comment"=>"1:사용 0:미적용","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"sell_priod_date","title"=>"판매기간설정","desc"=>"","type"=>"","comment"=>"20140618-201407018","validation"=>"false","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"delivery_coupon_yn","title"=>"배송쿠폰사용여부","desc"=>"","type"=>"","comment"=>"Y:사용함 N:사용안함","validation"=>"true","sample"=>"Y");
	$goods_basic_sample[] = array("code"=>"coupon_use_yn","title"=>"상품쿠폰 사용여부","desc"=>"","type"=>"","comment"=>"Y:사용함 N:사용안함","validation"=>"true","sample"=>"Y");
	$goods_basic_sample[] = array("code"=>"search_keyword","title"=>"검색키워드","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"나시, 티셔츠, 여름나시");
	$goods_basic_sample[] = array("code"=>"stock_use_yn","title"=>"재고관리 사용여부","desc"=>"","type"=>"","comment"=>"Y:(WMS)사용,N:사용안함,Q:빠른재고관리","validation"=>"true","sample"=>"Q");
	$goods_basic_sample[] = array("code"=>"safestock","title"=>"안전재고","desc"=>"","type"=>"","comment"=>"안전재고","validation"=>"false","sample"=>"200");
	$goods_basic_sample[] = array("code"=>"stock","title"=>"실재고","desc"=>"","type"=>"","comment"=>"재고","validation"=>"false","sample"=>"100");

	$goods_basic_sample[] = array("code"=>"movie","title"=>"동영상 URL","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"www.mallstory.com");
	$goods_basic_sample[] = array("code"=>"make_date","title"=>"제조일자","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"20120704");
	$goods_basic_sample[] = array("code"=>"expiry_date","title"=>"유효일","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"20130504");

	$goods_basic_sample[] = array("code"=>"is_adult","title"=>"19금상품","desc"=>"","type"=>"","comment"=>"0:미적용 1:적용","validation"=>"true","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"coprice","title"=>"공급가","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"15000");
	$goods_basic_sample[] = array("code"=>"wholesale_price","title"=>"도매판매가","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"25000");
	$goods_basic_sample[] = array("code"=>"wholesale_sellprice","title"=>"도매할인가","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"20000");
	$goods_basic_sample[] = array("code"=>"listprice","title"=>"소매판매가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"25000");
	$goods_basic_sample[] = array("code"=>"sellprice","title"=>"소매할인가","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"20000");
	$goods_basic_sample[] = array("code"=>"allow_basic_cnt","title"=>"기본시작수량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10");
	$goods_basic_sample[] = array("code"=>"allow_max_cnt","title"=>"최대판매수량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10");
	$goods_basic_sample[] = array("code"=>"wholesale_allow_byoneperson_cnt","title"=>"ID당구매수량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"10");
	$goods_basic_sample[] = array("code"=>"allow_order_type","title"=>"한정판매수량 사용여부","desc"=>"","type"=>"","comment"=>"1:적용 0:미적용","validation"=>"false","sample"=>"0");
	$goods_basic_sample[] = array("code"=>"allow_order_cnt_byonesell","title"=>"한정판매수량","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"100");

	$goods_basic_sample[] = array("code"=>"delivery_type","title"=>"배송타입","desc"=>"","type"=>"","comment"=>"1:통합배송 2:입점업체 배송","validation"=>"true","sample"=>"2");
	$goods_basic_sample[] = array("code"=>"one_commission","title"=>"개별수수료 사용여부","desc"=>"","type"=>"","comment"=>"N:사용안함 Y:사용","validation"=>"false","sample"=>"N");
	$goods_basic_sample[] = array("code"=>"account_type","title"=>"정산방식","desc"=>"","type"=>"","comment"=>"1:판매가정산 2:매입가정산 3:미정산","validation"=>"false","sample"=>"1");
	$goods_basic_sample[] = array("code"=>"commission","title"=>"정산 수수료","desc"=>"","type"=>"","comment"=>"10|20","validation"=>"false","sample"=>"10|20");
	$goods_basic_sample[] = array("code"=>"display_options","title"=>"디스플레이옵션","desc"=>"","type"=>"","comment"=>"제조사|대한민국| 해당 정보만 노출^
	브랜드|나이키| 해당 정보만 노출","validation"=>"false","sample"=>"제조사|대한민국| 해당 정보만 노출^
	브랜드|나이키| 해당 정보만 노출");
	$goods_basic_sample[] = array("code"=>"virals","title"=>"바이럴 등록","desc"=>"","type"=>"","comment"=>"카페&블로그명|URL|기타설명^
	카페&블로그명1|URL1|기타설명1^
	카페&블로그명2|URL2|기타설명2","validation"=>"false","sample"=>"몰스토리|mallstory.com|운영카페^
	다이소몰|daisomall.co.kr|운영쇼핑몰");

	$goods_basic_sample[] = array("code"=>"mandatory_type","title"=>"상품정보 고시유형","desc"=>"","type"=>"","comment"=>"35","validation"=>"false","sample"=>"35");
	$goods_basic_sample[] = array("code"=>"mandatory_info","title"=>"상품고시정보","desc"=>"","type"=>"","comment"=>"순서|내용^
	순서1|내용1^
	순서2|내용2","validation"=>"false","sample"=>"01|HP^
	02|올인원^
	03|파빌리온^
	04|알수 없음^
	05|2012/09/10");


    $goods_basic_sample[] = array("code"=>"mandatory_type_global","title"=>"상품정보 고시유형(영문)","desc"=>"","type"=>"","comment"=>"35","validation"=>"false","sample"=>"35");
    $goods_basic_sample[] = array("code"=>"mandatory_info_global","title"=>"상품고시정보(영문)","desc"=>"","type"=>"","comment"=>"순서|내용^
	순서1|내용1^
	순서2|내용2","validation"=>"false","sample"=>"01|HP^
	02|올인원^
	03|파빌리온^
	04|알수 없음^
	05|2012/09/10");

	$goods_basic_sample[] = array("code"=>"m_basicinfo","title"=>"모바일 상품상세정보","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"basicinfo","title"=>"웹 상품상세정보","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"mainimg","title"=>"상품이미지","desc"=>"","type"=>"","comment"=>"이미지는 FTP에서  '/www".$admininfo[mall_data_root]."/BatchUploadImages'  경로에 업로드 해주시면 됩니다.","validation"=>"false","sample"=>"");

	//기본옵션 시작 
	$goods_basic_sample[] = array("code"=>"basic_option_name","title"=>"기본옵션명","desc"=>"","type"=>"","comment"=>"옵션명1^옵션명2^옵션명3","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"basic_option_kind","title"=>"옵션종류","desc"=>"","type"=>"","comment"=>"조합옵션(필수) :c1 조합옵션(선택) :c2 독립옵션(필수) :i1 독립옵션(선택) :i2","validation"=>"false","sample"=>"c1^c2^i1");
	$goods_basic_sample[] = array("code"=>"basic_option_soldout","title"=>"품절여부","desc"=>"","type"=>"","comment"=>"품절 : 1 품절아님 : 0","validation"=>"false","sample"=>"0|0|1^1|1|0^0|0|0");
	$goods_basic_sample[] = array("code"=>"basic_option_div","title"=>"기본옵션구분","desc"=>"","type"=>"","comment"=>"옵션구분명","validation"=>"false","sample"=>"옵션구분1_1|옵션구분1_2|옵션구분1_3^옵션구분2_1|옵션구분2_2|옵션구분2_3^옵션구분3_1|옵션구분3_2|옵션구분3_3");
	$goods_basic_sample[] = array("code"=>"basic_option_price","title"=>"기본옵션추가가격","desc"=>"","type"=>"","comment"=>"","validation"=>"false","sample"=>"1000|1000|1000^2000|2000|2000^3000|3000|3000");


	//가격재고관리 옵션 시작 
	$goods_basic_sample[] = array("code"=>"option_name","title"=>"옵션명","desc"=>"","type"=>"","comment"=>"옵션명","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"option_div","title"=>"옵션구분","desc"=>"","type"=>"","comment"=>"옵션구분","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"stock_options_coprice","title"=>"옵션공급가격","desc"=>"","type"=>"","comment"=>"옵션구분","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"stock_options_wholesale_listprice","title"=>"옵션 도매판매가","desc"=>"","type"=>"","comment"=>"옵션 도매판매가","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"stock_options_wholesale_price","title"=>"옵션 도매할인가","desc"=>"","type"=>"","comment"=>"옵션 도매할인가","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"stock_options_listprice","title"=>"옵션 소매판매가","desc"=>"","type"=>"","comment"=>"옵션 소매판매가","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"stock_options_sellprice","title"=>"옵션 소매할인가","desc"=>"","type"=>"","comment"=>"옵션 소매할인가","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"stock_options_stock","title"=>"실재고","desc"=>"","type"=>"","comment"=>"실재고","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"stock_options_soldout","title"=>"품절여부","desc"=>"","type"=>"","comment"=>"품절여부 품절 :1 품절아님 :0","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"stock_options_safestock","title"=>"안전재고","desc"=>"","type"=>"","comment"=>"안전재고","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"stock_options_code","title"=>"옵션품목코드","desc"=>"","type"=>"","comment"=>"옵션품목코드","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"stock_options_barcode","title"=>"옵션바코드","desc"=>"","type"=>"","comment"=>"옵션바코드","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"naver_use","title"=>"지식쇼핑","desc"=>"","type"=>"","comment"=>"지식쇼핑","validation"=>"false","sample"=>"");
	$goods_basic_sample[] = array("code"=>"daum_use","title"=>"다음쇼핑하우","desc"=>"","type"=>"","comment"=>"다음쇼핑하우","validation"=>"false","sample"=>"");

	if($_SESSION['admininfo']['charger_id'] == 'forbiz' && false){
		$goods_basic_sample[] = array("code"=>"admin","title"=>"입점업체키","desc"=>"","type"=>"","comment"=>"입점업체키","validation"=>"true","sample"=>"");
	}

	/*=================================================새로운 대량상품등록 설정 끝 2014-06-18 이학봉=================================================*/

}else if($page_type == 'update'){

	/*=================================================새로운 대량상품 엑셀수정 설정 시작 2014-06-18 이학봉=================================================*/
	$goods_basic_sample[] = array("code"=>"id","code_group"=>"","title"=>"상품시스템코드","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"","width"=>"300");
	$goods_basic_sample[] = array("code"=>"product_type","code_group"=>"","title"=>"상품구분","desc"=>"","type"=>"","comment"=>"상품등록시 코드에 따라 구분됩니다. 해당 상품구분을 입력해 주세요. 0 : 일반상품","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"category","code_group"=>"","title"=>"카테고리","desc"=>"","type"=>"","comment"=>"카테고리 코드를 조회 및 엑셀로 다운로도 받으셔서 해당 카테고리를 확인하시고 해서 입력해주세요.

카테고리코드

001001005000000
|001001005000000

* 다중으로 카테고리 등록이 가능하며 첫번째 카테고리가 마스터 카테고리로 등록됩니다. 만약 실제 코드와 다르면 상품이 등록되지 않습니다.","validation"=>"true","sample"=>"001001005000000|001001005000000|001001005000000","width"=>"300");
    $goods_basic_sample[] = array("code"=>"md_id","code_group"=>"","title"=>"MD설정","desc"=>"","type"=>"","comment"=>"개별 담당MD 등록시에만 사용하셔야합니다. MD 아아디를 입력해주세요.

MD 아이디

ktw9

* MD가 별도로 없을경우 입력하지 마세요.","validation"=>"true","sample"=>"ktw9","width"=>"300");
    $goods_basic_sample[] = array("code"=>"disp_global", "code_group"=>"", "title"=>"글로벌 노출설정","desc"=>"","type"=>"","comment"=>"노출하려는 사이트 정보를 입력해주세요.

글로벌 노출 설정

20bd04dac38084b2bafdd6d78cd596b1
|20bd04dac38084b2bafdd6d78cd596b2



* 영문/국문 노출시 키값을 입력해주셔야 합니다.

*. 미입력시 자동으로 전체 노출됩니다.","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"pname","code_group"=>"","title"=>"상품명","desc"=>"","type"=>"","comment"=>"프론트에 판매시에 노출되는 실제 상품 명을 입력해주세요.

상품명

블링블링 반팔

* 상품명은 한글,숫자,영문 포함 100자 내로 입력해주세요.

* HTML 코드는 반영되지 않습니다.","validation"=>"true","sample"=>"모두의 워킹 나시","width"=>"300");
    $goods_basic_sample[] = array("code"=>"english_pname","code_group"=>"","title"=>"상품명(영문)","desc"=>"","type"=>"","comment"=>"프론트에 판매시에 노출되는 실제 상품 명을 입력해주세요.

상품명

summer white

* 상품명은 한글,숫자,영문 포함 100자 내로 입력해주세요.

* HTML 코드는 반영되지 않습니다.

* 미입력시 자동으로 국문상품명이 입력됩니다.","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"add_info","code_group"=>"","title"=>"색상명","desc"=>"","type"=>"","comment"=>"입력되어야하는 정보를 입력해주세요.

색상정보 or 사은품 금액

빨강","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"english_add_info","code_group"=>"","title"=>"색상명(영문)","desc"=>"","type"=>"","comment"=>"입력되어야하는 정보를 입력해주세요.

색상정보 or 사은품 금액

red","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"pcode","code_group"=>"","title"=>"상품코드","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"","width">="300");
    $goods_basic_sample[] = array("code"=>"search_keyword","code_group"=>"","title"=>"검색키워드","desc"=>"","type"=>"","comment"=>"검색 키워드를입력해주세요.

검색어1,검색어2,검색어3

나시,티셔츠,여름나시

* 상품 검색어에 의해서도 해당상품이 노출될수 있습니다.","validation"=>"true","sample"=>"나시, 티셔츠, 여름나시","width"=>"300");
    $goods_basic_sample[] = array("code"=>"english_search_keyword","code_group"=>"","title"=>"검색키워드(영문)","desc"=>"","type"=>"","comment"=>"검색 키워드를입력해주세요.

검색어1,검색어2,검색어3

RED, summer

* 상품 검색어에 의해서도 해당상품이 노출될수 있습니다.","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"preface","code_group"=>"","title"=>"상품 머리말","desc"=>"","type"=>"","comment"=>"상품 머리말을 입력해주세요.","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"english_preface","code_group"=>"","title"=>"상품 머리말(영문)","desc"=>"","type"=>"","comment"=>"상품 머리말을 입력해주세요.(영문)","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"c_preface","title"=>"상품머리말 색상코드(HEXA코드)","desc"=>"","type"=>"","comment"=>"상품머리말색상(HEXA 코드)을 입력해 주세요","validation"=>"true","sample"=>"#000000");
    $goods_basic_sample[] = array("code"=>"laundry_cid","code_group"=>"","title"=>"세탁주의코드","desc"=>"","type"=>"","comment"=>"세탁주의관리 카테고리 코드를 입력하세요.

* 상품관리 > 상품분류관리 > 세탁주의관리에 있는 중분류 코드를 입력하세요.

* 6자리 숫자만 입력하세요.(영문/국문, 특수문자, 띄어쓰기 금지)

 ※ 최초 다운시 '/' 생성되어 반드시 삭제해주시기 바랍니다.

* 미입력시 상품상세 > 세탁주의사항에 노출이 되지 않습니다.
","validation"=>"true","sample"=>"ex) 001001","width"=>"300");
    $goods_basic_sample[] = array("code"=>"wear_info","title"=>"착장정보","desc"=>"","type"=>"","comment"=>"상품 착장정보를 입력해 주세요","validation"=>"true","sample"=>"착장정보");
    $goods_basic_sample[] = array("code"=>"filter_info","code_group"=>"","title"=>"필터정보","desc"=>"","type"=>"","comment"=>"입력되어야하는 필터코드를 입력해주세요.

필터정보

aaa|bbb|ccc

* 구분에 상관없이 코드를 입력해주세요.

*. 상품관리 > 개별상품등록 에 필터에 넣은 코드로 사용해야합니다. 

* 영문과 숫자만 입력 가능합니다.","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"mandatory_type","code_group"=>"mandatory","color"=>"","title"=>"상품정보 고시유형","desc"=>"","type"=>"","comment"=>"상품고시 유형 코드를 입력해주세요.

1 : 의류
2 : 구두/신발
3 : 가방
.
.
.
35 : 기타","validation"=>"true","sample"=>"35","width"=>"300");
    $goods_basic_sample[] = array("code"=>"mandatory_info","code_group"=>"mandatory","color"=>"","title"=>"상품고시정보","desc"=>"","type"=>"","comment"=>"상품정보고시 유형별 입력항목 확인 메뉴에서 조화된 항목의 코드와 내용을 다음과 같은 형식으로 입력해 주세요.

순서|내용^
순서1|내용1^
순서2|내용2^

01|HP^02|올인원^03|파빌리온^
04|알수 없음^05|2012/09/10^
.
.
.
","validation"=>"true","width"=>"300");


    $goods_basic_sample[] = array("code"=>"mandatory_type_global","code_group"=>"mandatory_global","color"=>"","title"=>"상품정보고시유형(영문)","desc"=>"","type"=>"","comment"=>"상품고시 유형 코드를 입력해주세요.

1 : 의류
2 : 구두/신발
3 : 가방
.
.
.
35 : 기타","validation"=>"true","sample"=>"35","width"=>"300");
    $goods_basic_sample[] = array("code"=>"mandatory_info_global","code_group"=>"mandatory_global","color"=>"","title"=>"상품고시정보(영문)","desc"=>"","type"=>"","comment"=>"상품정보고시 유형별 입력항목 확인 메뉴에서 조화된 항목의 코드와 내용을 다음과 같은 형식으로 입력해 주세요.

순서|내용^
순서1|내용1^
순서2|내용2^

01|HP^02|올인원^03|파빌리온^
04|알수 없음^05|2012/09/10^
.
.
.
","validation"=>"true","width"=>"300");
    $goods_basic_sample[] = array("code"=>"shotinfo","code_group"=>"","title"=>"상품간략설명","desc"=>"","type"=>"","comment"=>"프론트 리스트 페이지에 마우스 오버 혹은 프론트에 노출되는 영역으로 상품에 대한 간략 소개할 정보를 입력해주세요.

상품간략 입력

겨울 상품으로 한정 100개 입니다.","validation"=>"true","sample"=>"미국 유명브랜드 상품!","width"=>"300");
    $goods_basic_sample[] = array("code"=>"english_shotinfo","code_group"=>"","title"=>"상품간략소개(영문)","desc"=>"","type"=>"","comment"=>"프론트 리스트 페이지에 마우스 오버 혹은 프론트에 노출되는 영역으로 상품에 대한 간략 소개할 정보를 입력해주세요.

상품간략 입력

good

* 미입력시 자동으로 국문 상품간략소개 정보가 입력됩니다.","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"og_ix","code_group"=>"","title"=>"원산지코드","desc"=>"","type"=>"","comment"=>"원산지 코드를 입력해주세요.

원산지 코드

3

* 정확한 코드를 입력하지 않은 경우 정상적으로 입력되지 않습니다.","validation"=>"true","sample"=>"B001","width"=>"300");
    $goods_basic_sample[] = array("code"=>"company","code_group"=>"","title"=>"제조사코드","desc"=>"","type"=>"","comment"=>"제조사 텍스트로 입력해주세요.

제조사명

HP코리아","validation"=>"true","sample"=>"HP코리아","width"=>"300");
    $goods_basic_sample[] = array("code"=>"brand","code_group"=>"","title"=>"브랜드코드","desc"=>"","type"=>"","comment"=>"브랜드를 입력해주세요.

브랜드코드

AF497835

* 정확한 코드를 입력하지 않은 경우 정상적으로 입력되지 않습니다.","validation"=>"true","sample"=>"AF497835","width"=>"300");
    $goods_basic_sample[] = array("code"=>"surtax_yorn","code_group"=>"","title"=>"면세여부","desc"=>"","type"=>"","comment"=>"과세/면세 상품을 구분되며 상품등록시 과세/면세를 혼합해서 등록하시면안됩니다. 면세는 Y 또는 과세는 N 으로 입력해주세요.

Y : 면세
N : 과세

* 상품 옵션에 면세상품과 과세상품을 한꺼번에 등록하지 마세요.","validation"=>"true","sample"=>"Y","width"=>"300");
    $goods_basic_sample[] = array("code"=>"state","code_group"=>"","title"=>"판매상태","desc"=>"","type"=>"","comment"=>"최초등록시 상품의 상태를 입력해주세요.

1 : 판매중
0 : 일시품절","validation"=>"true","sample"=>"6","width"=>"300");
    $goods_basic_sample[] = array("code"=>"disp","code_group"=>"","title"=>"노출여부","desc"=>"","type"=>"","comment"=>"최초등록시 상품의 노출여부를 입력해주세요.

1 : 노출
0 : 비노출","validation"=>"true","sample"=>"1","width"=>"300");
    $goods_basic_sample[] = array("code"=>"is_sell_date","code_group"=>"","title"=>"판매기간 사용여부","desc"=>"","type"=>"","comment"=>"판매 기간이 없을 경우 0으로 입력하시고 특정 판매기간이 있을 경우 1로 입력해주세요.

0 : 미적용
1 : 적용","validation"=>"true","sample"=>"1","width"=>"300");
    $goods_basic_sample[] = array("code"=>"sell_priod_date","code_group"=>"","title"=>"판매기간설정","desc"=>"","type"=>"","comment"=>"판매될 기간을 입력해주세요.

시작일-종료일

20140101-20141231

* 시작일은 등록시점보다 이전이면 등록되지 않습니다.

* 17_판매기간 사용여부가 1 : 적용 일경우에만 적용됩니다.","validation"=>"true","sample"=>"1","width"=>"300");
    $goods_basic_sample[] = array("code"=>"coupon_use_yn","code_group"=>"","title"=>"상품쿠폰 사용여부","desc"=>"","type"=>"","comment"=>"Y 또는 N 으로 입력해주세요.

Y : 사용
N : 사용안함

* 설정 여부에 따라 주문시 상품 쿠폰을 적용 할 수 있습니다.","validation"=>"true","sample"=>"Y","width"=>"300");

    $goods_basic_sample[] = array("code"=>"movie","code_group"=>"","title"=>"동영상 URL","desc"=>"","type"=>"","comment"=>"동영상 URL을 입력해주세요.

동영상URL(NEWS)

www.mallstory.com","validation"=>"true","sample"=>"www.mallstory.com","width"=>"300");
    $goods_basic_sample[] = array("code"=>"coprice","code_group"=>"","title"=>"공급가","desc"=>"","type"=>"","comment"=>"상품의 공급가를 입력해주세요.

구매단가(원가)

10000

* 정산유형에 따라 자동으로 입력됩니다.","validation"=>"true","sample"=>"15000","width"=>"300");
    $goods_basic_sample[] = array("code"=>"english_coprice","code_group"=>"","title"=>"공급가(영문)","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"listprice","code_group"=>"","title"=>"판매가","desc"=>"","type"=>"","comment"=>"상품의 판매가를 입력해주세요.

판매가

10000","validation"=>"true","sample"=>"25000","width"=>"300");
    $goods_basic_sample[] = array("code"=>"english_listprice","code_group"=>"","title"=>"판매가(영문)","desc"=>"","type"=>"","comment"=>"상품의 판매가를 입력해주세요.

판매가(달러)

10.22

*. 미입력시 자동으로 입력된 공급가(국문) 금액을 환율이 반영된 금액으로 저장됩니다.","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"sellprice","code_group"=>"","title"=>"할인가","desc"=>"","type"=>"","comment"=>"상품의 할인가를 입력해주세요.

할인가

10000","validation"=>"true","sample"=>"20000","width"=>"300");
    $goods_basic_sample[] = array("code"=>"english_sellprice","code_group"=>"","title"=>"할인가(영문)","desc"=>"","type"=>"","comment"=>"상품의 할인가를 입력해주세요.

할인가(달러)

10.22

*. 미입력시 자동으로 입력된 공급가(국문) 금액을 환율이 반영된 금액으로 저장됩니다.","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"allow_max_cnt","code_group"=>"","title"=>"최대판매수량","desc"=>"","type"=>"","comment"=>"1회 최대 구매 가능 수량을 입력해주세요.

최대판매수량

3

* 0 일경우 최대 수량에 상관없이 판매됩니다.","validation"=>"true","sample"=>"10","width"=>"300");
    $goods_basic_sample[] = array("code"=>"wholesale_allow_byoneperson_cnt","title"=>"ID당구매수량","desc"=>"","type"=>"","comment"=>"","validation"=>"true","sample"=>"10");
    $goods_basic_sample[] = array("code"=>"allow_order_type","code_group"=>"","title"=>"한정판매 사용여부","desc"=>"","type"=>"","comment"=>"한정수량판매 적용 여부를 선택해주세요.

0 : 미적용
1 : 적용

* 재고관리 유형에 상관없이 한정수량 판매 설정

* 적용시 재고과 상관없이 한정수량만 판매가 가능합니다.","validation"=>"true","sample"=>"0","width"=>"300");
    $goods_basic_sample[] = array("code"=>"allow_order_cnt_byonesell","code_group"=>"","title"=>"한정판매수량","desc"=>"","type"=>"","comment"=>"재고관리 수량에 관계없이 설정 상품에 판매 상품 수를 제한 할 수 있습니다.

한정수량

10

* 31_한정판매사용여부에 1을 입력시 사용가능하며, 숫자로만 입력 할 수 있습니다.","validation"=>"true","sample"=>"100","width"=>"300");
    $goods_basic_sample[] = array("code"=>"display_option","code_group"=>"display_options","color"=>"","title"=>"디스플레이옵션","desc"=>"","type"=>"","comment"=>"디스플레이옵션에 대해서 정보를 다음과 같은 형식으로 입력해주세요.

추가정보명|추가정보내용|기타설명^
추가정보명1|추가정보내용1|기타설명1^
추가정보명2|추가정보내용2|기타설명2^

제조사|대한민국| 해당 정보만 노출^
브랜드|나이키| 해당 정보만 노출^

* 상품상세페이지 하단에 추가 노출됩니다.","validation"=>"true","sample"=>"제조사|대한민국| 해당 정보만 노출^
	브랜드|나이키| 해당 정보만 노출","width"=>"300");
	$goods_basic_sample[] = array("code"=>"m_basicinfo","code_group"=>"","title"=>"모바일 상품상세정보","desc"=>"","type"=>"","comment"=>"상품 상세페이지 이미지 경로를 입력해주세요.
* 미입력시 PC 상세페이지가 자동으로 적용됩니다.","validation"=>"true","sample"=>"","width"=>"300");
	$goods_basic_sample[] = array("code"=>"english_m_basicinfo","code_group"=>"","title"=>"모바일 상품상세설명(영문)","desc"=>"","type"=>"","comment"=>"상품 상세페이지 이미지 경로를 입력해주세요.
* 미입력시 국문 Mobile 상세페이지가 자동으로 적용됩니다.","validation"=>"true","sample"=>'<p><img src="http://shopdomain.co.kr/data/mallid_data/BatchUploadImages/fortune_120417"></p>',"width"=>"300");
	$goods_basic_sample[] = array("code"=>"basicinfo","code_group"=>"","title"=>"웹 상품상세정보","desc"=>"","type"=>"","comment"=>"카테고리 코드를 조회 및 엑셀로 다운로도 받으셔서 해당 카테고리를 확인하시고 해서 입력해주세요.
카테고리코드
001001005000000
|001001005000000
* 다중으로 카테고리 등록이 가능하며 첫번째 카테고리가 마스터 카테고리로 등록됩니다. 만약 실제 코드와 다르면 상품이 등록되지 않습니다.","validation"=>"true","sample"=>"","width"=>"300");
	$goods_basic_sample[] = array("code"=>"english_basicinfo","code_group"=>"","title"=>"웹 상품상세설명(이미지)(영문)","desc"=>"","type"=>"","comment"=>"상품 상세페이지 이미지 경로를 입력해주세요.
* 미입력시 국문 PC 상세페이지가 자동으로 적용됩니다.","validation"=>"true","sample"=>'<p><img src="http://shopdomain.co.kr/data/mallid_data/BatchUploadImages/fortune_120417"></p>',"width"=>"300");
	$goods_basic_sample[] = array("code"=>"mainimg","code_group"=>"","title"=>"상품이미지","desc"=>"","type"=>"","comment"=>"상품 이미지 파일명을 입력해주세요.
상품이미지명.jpg
fortune_120417_1.jpg","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"listNum","title"=>"리스트이미지","desc"=>"","type"=>"","comment"=>"0으로 설정 시 1번 이미지가 상품리스트 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"true","sample"=>"0");
    $goods_basic_sample[] = array("code"=>"overNum","title"=>"마우스오버이미지","desc"=>"","type"=>"","comment"=>"0으로 설정 시 1번 이미지가 상품리스트 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"true","sample"=>"1");
    $goods_basic_sample[] = array("code"=>"slistNum","title"=>"리스트작은이미지","desc"=>"","type"=>"","comment"=>"0으로 설정 시 1번 이미지가 상품리스트 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"true","sample"=>"2");
    $goods_basic_sample[] = array("code"=>"nailNum","title"=>"썸네일이미지","desc"=>"","type"=>"","comment"=>"0으로 설정 시 1번 이미지가 상품리스트 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"true","sample"=>"3");
    $goods_basic_sample[] = array("code"=>"pattNum","title"=>"패턴이미지","desc"=>"","type"=>"","comment"=>"0으로 설정 시 1번 이미지가 상품리스트 이미지로 등록 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"true","sample"=>"4");
    $goods_basic_sample[] = array("code"=>"marker_left_dn","title"=>"상품마커좌측하단","desc"=>"","type"=>"","comment"=>"0으로 설정 미노출 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"true","sample"=>"0");
    $goods_basic_sample[] = array("code"=>"marker_right_dn","title"=>"상품마커우측하단","desc"=>"","type"=>"","comment"=>"0으로 설정 미노출 됩니다.(빈칸으로 등록시 0으로 저장됩니다.)","validation"=>"true","sample"=>"0");
    $goods_basic_sample[] = array("code"=>"admin_memo","code_group"=>"","title"=>"관리자메모","desc"=>"","type"=>"","comment"=>"관리자 메모를 입력해주세요. ","validation"=>"true","sample"=>"","width"=>"300");
    /*
    $goods_basic_sample[] = array("code"=>"pattern_image","code_group"=>"","title"=>"패턴 이미지","desc"=>"","type"=>"","comment"=>"상품 이미지 파일명을 입력해주세요.
패턴 이미지명.jpg
fortune_120417_1.jpg
* 압축파일에 이미지 압축파일과 함께 등록하는 경우 상품명 앞에 압축파일명/ 를 붙여주세요.
압축파일명/상품이지미명.jpg
alzip/fortune_120417_1.jpg
* 이미지 파일명은 영문으로 작성하하시면 됩니다.","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"addimg","code_group"=>"","title"=>"추가이미지","desc"=>"","type"=>"","comment"=>"상품 이미지 파일명을 입력해주세요.

상품이미지명.jpg

fortune_120417_1.jpg

* 압축파일에 이미지 압축파일과 함께 등록하는 경우 상품명 앞에 압축파일명/ 를 붙여주세요.

압축파일명/상품이지미명.jpg

alzip/fortune_120417_01.jpg
|alzip/fortune_120417_02.jpg
|alzip/fortune_120417_03.jpg
|alzip/fortune_120417_04.jpg","validation"=>"true","sample"=>"","width"=>"300");
    $goods_basic_sample[] = array("code"=>"listimg","code_group"=>"","title"=>"상품 리스트 이미지","desc"=>"","type"=>"","comment"=>"상품 이미지 파일명을 입력해주세요.

상품이미지명.jpg

fortune_120417_1.jpg

* 압축파일에 이미지 압축파일과 함께 등록하는 경우 상품명 앞에 압축파일명/ 를 붙여주세요.

압축파일명/상품이지미명.jpg

alzip/fortune_120417_1.jpg","validation"=>"true","sample"=>"","width"=>"300");
     * */
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