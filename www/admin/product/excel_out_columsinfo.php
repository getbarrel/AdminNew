<?
if($info_type == "edit_history"){
	$colums[edit_date] = array(value=>'edit_date',title=>'정보수정일', checked=>'checked');
	$colums[gp_ix] = array(value=>'gp_ix',title=>'그룹', checked=>'checked');
	$colums[mem_type] = array(value=>'mem_type',title=>'회원구분', checked=>'checked');
	$colums[mem_div] = array(value=>'mem_div',title=>'회원타입', checked=>'checked');
	$colums[name] = array(value=>'name',title=>'이름', checked=>'checked');
	$colums[id] = array(value=>'id',title=>'아이디', checked=>'checked');
	$colums[edit_text] = array(value=>'edit_text',title=>'수정내역', checked=>'checked');
	$colums[chager_name] = array(value=>'chager_name',title=>'수정자명', checked=>'checked');
	$colums[regdate] = array(value=>'regdate',title=>'처리일', checked=>'checked');

}else if($info_type == "list"){
	$colums[regdate] = array(value=>'regdate',title=>'등록일', checked=>'checked');
	$colums[bd_ix] = array(value=>'bd_ix',title=>'브랜드분류', checked=>'checked');
	$colums[brand_code] = array(value=>'brand_code',title=>'브랜드코드', checked=>'checked');
	$colums[cid] = array(value=>'cid',title=>'카테고리', checked=>'checked');
	$colums[brand_name] = array(value=>'brand_name',title=>'브랜드명', checked=>'checked');
	$colums[apply_status] = array(value=>'apply_status',title=>'신청상태', checked=>'checked');
	$colums[pcount] = array(value=>'pcount',title=>'상품수', checked=>'checked');
	$colums[disp] = array(value=>'disp',title=>'사용여부', checked=>'checked');

}else if($info_type == "origin_list"){
	$colums[regdate] = array(value=>'regdate',title=>'등록일', checked=>'checked');
	$colums[od_ix] = array(value=>'od_ix',title=>'원산지분류', checked=>'checked');
	$colums[origin_code] = array(value=>'origin_code',title=>'원산지코드', checked=>'checked');
	$colums[origin_name] = array(value=>'origin_name',title=>'원산지명', checked=>'checked');
	$colums[pcount] = array(value=>'pcount',title=>'상품수', checked=>'checked');
	$colums[disp] = array(value=>'disp',title=>'사용여부', checked=>'checked');
}else if($info_type == "company_list"){
	$colums[regdate] = array(value=>'regdate',title=>'등록일', checked=>'checked');
	$colums[cd_ix] = array(value=>'cd_ix',title=>'제조사분류', checked=>'checked');
	$colums[cp_code] = array(value=>'cp_code',title=>'제조사코드', checked=>'checked');
	$colums[cid] = array(value=>'cid',title=>'카테고리', checked=>'checked');
	$colums[company_name] = array(value=>'origin_name',title=>'제조사명', checked=>'checked');
	$colums[status] = array(value=>'status',title=>'신청상태', checked=>'checked');
	$colums[pcount] = array(value=>'pcount',title=>'상품수', checked=>'checked');
	$colums[disp] = array(value=>'disp',title=>'사용여부', checked=>'checked');
}else{

	$colums[id] = array(value=>'id',title=>'상품 시스템코드', checked=>'checked', is_nessesary=>true, desc=>"시스템 코드는 변경이 불가능하며, 수정시 꼭 필요한 항목입니다. ",width=>20);
	$colums[product_type] = array(value=>'product_type',title=>'상품 구분', checked=>'checked', is_nessesary=>true, desc=>"상품등록시 코드에 따라 구분됩니다. 해당 상품구분을 입력해 주세요\n\n0:일반상품\n12:공동구매상품(최저가딜)\n\n*셀러업체는 0:일반상품만 등록가능합니다.",width=>20);
	$colums[cid] = array(value=>'cid',title=>'카테고리', checked=>'checked', is_nessesary=>true, desc=>"카테고리 코드를 조회 및 엑셀로 다운로도 받으셔서 해당 카테고리를 확인하시고 해서 입력해주세요\n\n카테고리코드\n\n001001005000000|001001005000000\n\n* 다중으로 카테고리 등록이 가명하며 첫번째 카테고리가 마스터 카테고리로 등록됩니다. 만약 실제 코드와 다르면 상품이 등록되지 않습니다",width=>20);
	$colums[md_code] = array(value=>'md_code',title=>'MD 설정', checked=>'checked', is_nessesary=>false, desc=>"개별 담당MD 등록시에만 사용하셔야합니다. MD 아아디를 입력해주세요.",width=>20);

	$colums[pname] = array(value=>'pname',title=>'상품명', checked=>'checked', is_nessesary=>true, desc=>"프론트에 판매시에 노출되는 실제 상품 명을 입력해주세요.\n\n* 상품 코드는 한글, 영문, 숫자를 포함 100 자 이내로 입력해주세요.\n* HTML 코드는 반영되지 않습니다.",width=>20);
	$colums[pcode] = array(value=>'pcode',title=>'상품코드', checked=>'checked', is_nessesary=>false, desc=>"프론트에는 노출되지 않으며, 자체상품관리시에 사용되는 코드를 입력해주세요.\n\n* 상품 코드는 한글, 영문, 숫자를 포함 100 자 이내로 입력해주세요.\n\n* 21_재고관리사용여부  Y : WMS 사용 일 경우에 입력된 품목사용하는 코드를 사용하여 품목 매핑한다.",width=>20);
	$colums[paper_name] = array(value=>'paper_name',title=>'매입상품명', checked=>'checked', is_nessesary=>false, desc=>"",width=>20);
	$colums[barcode] = array(value=>'barcode',title=>'바코드', checked=>'checked', is_nessesary=>false, desc=>"상품에 부여된 바코드를 입력해주세요.\n\n* WMS 품목을 사용하여 상품 등록시 폼목에 등록되어 있는 바코드가 자동으로 입력됩니다.",width=>20);
	$colums[shopinfo] = array(value=>'shopinfo',title=>'상품간략소개', checked=>'checked', is_nessesary=>false, desc=>"프론트 리스트 페이지에 마우스 오버 혹은 프론트에 노출되는 영역으로 상품에 대한 간략 소개할 정보를 입력해주세요",width=>20);
	$colums[trade_admin] = array(value=>'trade_admin',title=>'매입업체코드', checked=>'checked', is_nessesary=>false, desc=>"자체 상품이 아닌 매입을 통한 상품일 경우 매입처를 별도로 관리하여 효율적으로 관리하실 수 있으며, 매입처가 있는 상ㅍ무에 해당 매입업체 코드를 넣어주세요.",width=>20);
	$colums[orgin] = array(value=>'orgin',title=>'원산지', checked=>'checked', is_nessesary=>true, desc=>"원산지 코드를 입력해주세요.",width=>20);
	$colums[company] = array(value=>'company',title=>'제조사', checked=>'checked', is_nessesary=>true, desc=>"제조사 텍스트로 입력해주세요.",width=>20);
	$colums[brand_code] = array(value=>'brand_code',title=>'브랜드코드', checked=>'checked', is_nessesary=>false, desc=>"브랜드를 입력해주세요.",width=>20);
	$colums[surtax_yorn] = array(value=>'surtax_yorn',title=>'면세여부', checked=>'checked', is_nessesary=>true, desc=>"과세/면세 상품을 구분되며 상품등록시 과세/면세를 혼합해서 등록하시면안됩니다. 면세는 Y 또는 과세는 N 으로 입력해주세요.\n\nY:면세 \nN:과세 ",width=>20);
	$colums[state] = array(value=>'state',title=>'판매상태', checked=>'checked', is_nessesary=>true, desc=>"1:판매상태\n0:일시품절\n6:승인대기\n7:수정대기상품",width=>20);
	$colums[disp] = array(value=>'disp',title=>'노출여부', checked=>'checked', is_nessesary=>true, desc=>"1:노출\n0:비노출",width=>20);
	$colums[weight] = array(value=>'weight',title=>'무게', checked=>'checked', is_nessesary=>false, desc=>"상품의 무게(KG)를 입력해주세요.",width=>20);

	$colums[is_sell_date] = array(value=>'is_sell_date',title=>'판매기간 사용여부', checked=>'checked', is_nessesary=>true, desc=>"판매 기간이 없을 경우 0으로 입력하시고 특정 판매기간이 있을 경우 1로 입력해주세요.\n\n0 : 미적용\n1 : 적용",width=>20);
	$colums[sell_priod_sdate] = array(value=>'sell_priod_sdate',title=>'판매시작시간', checked=>'checked', is_nessesary=>false, desc=>"
20140101",width=>20);
	$colums[sell_priod_edate] = array(value=>'sell_priod_edate',title=>'판매종료시간', checked=>'checked', is_nessesary=>false, desc=>"20141231",width=>20);
	$colums[delivery_coupon_yn] = array(value=>'delivery_coupon_yn',title=>'배송쿠폰 사용여부', checked=>'checked', is_nessesary=>true, desc=>"Y 또는 N 으로 입력해주세요.\n\nY : 사용\nN : 사용안함\n\n* 설정 여부에 따라 주문시 배송비 쿠폰을 적용 할 수 있습니다.",width=>20);
	$colums[coupon_use_yn] = array(value=>'coupon_use_yn',title=>'상품쿠폰 사용여부', checked=>'checked', is_nessesary=>true, desc=>"Y 또는 N 으로 입력해주세요.\n\nY : 사용\nN : 사용안함\n\n* 설정 여부에 따라 주문시 상품 쿠폰을 적용 할 수 있습니다",width=>20);
	$colums[search_keyword] = array(value=>'search_keyword',title=>'검색키워드', checked=>'checked', is_nessesary=>false, desc=>"검색 키워드를입력해주세요.\n\n검색어1,검색어2,검색어3\n\n나시,티셔츠,여름나시\n\n* 키워드는 한글,숫자,영문,특수문자 포함 100자 이내로 입력해주세요.\n\n* 상품 검색어에 의해서도 해당상품이 노출될수 있습니다.",width=>20);
	$colums[stock_use_yn] = array(value=>'stock_use_yn',title=>'재고관리 사용여부', checked=>'checked', is_nessesary=>true, desc=>"재고관리 유형을 선택해주세요.\n\nN : 사용안함\nQ : 빠른재고 사용\nY : WMS 사용\n\n* 셀러로 로그인해서 Y : WMS 사용 입력할 경우 자동으로 Q 으로 등록됩니다.",width=>20);

	$colums[safestock] = array(value=>'safestock',title=>'안전재고', checked=>'checked', is_nessesary=>false, desc=>"안전재고 수량을 입력해주세요\n\n안전재고 수량\n\n10 \n\n* 상품을 가격재고옵션에 안전재고를 입력할 경우 자동으로 합산되어 입력됩니다",width=>20);
	$colums[stock] = array(value=>'stock',title=>'실재고', checked=>'checked', is_nessesary=>false, desc=>"실재고 수량을 입력해주세요\n\n실재고 수량\n\n10 \n\n* 상품을 가격재고옵션에 실재고를 입력할 경우 자동으로 합산되어 입력됩니다.",width=>20);
	$colums[movie] = array(value=>'movie',title=>'동영상 URL', checked=>'checked', is_nessesary=>false, desc=>"동영상 URL을 입력해주세요.\n\n동영상URL(NEWS)",width=>20);
	$colums[make_date] = array(value=>'make_date',title=>'재조일자', checked=>'checked', is_nessesary=>false, desc=>"제조일자를 입력해주세요. \n\n 20140101",width=>20);
	$colums[expiry_date] = array(value=>'expiry_date',title=>'유효일', checked=>'checked', is_nessesary=>false, desc=>"",width=>20);
	$colums[is_adult] = array(value=>'is_adult',title=>'19 금 상품여부', checked=>'checked', is_nessesary=>true, desc=>"19세에 따른 상품 노출 여부를 선택해주세요.\n\n1 : 적용(성인용품)\n0 : 미적용\n\n* 적용시 미성년자에게는 상품 이미지가 노출되지 않습니다.",width=>20);

	$colums[coprice] = array(value=>'coprice',title=>'공급가', checked=>'checked', is_nessesary=>false, desc=>"",width=>20);
	$colums[wholesale_price] = array(value=>'wholesale_price',title=>'도매판매가', checked=>'checked', is_nessesary=>true, desc=>"",width=>20);
	$colums[wholesale_sellprice] = array(value=>'wholesale_sellprice',title=>'도매할인가(판매가)', checked=>'checked', is_nessesary=>true, desc=>"",width=>20);

	$colums[listprice] = array(value=>'listprice',title=>'소매가', checked=>'checked', is_nessesary=>true, desc=>"",width=>20);
	$colums[sellprice] = array(value=>'sellprice',title=>'할인가(판매가)', checked=>'checked', is_nessesary=>true, desc=>"",width=>20);

	$colums[delivery_type] = array(value=>'delivery_type',title=>'배송타입', checked=>'checked', is_nessesary=>true, desc=>"",width=>20);
	$colums[delivery_policy] = array(value=>'delivery_policy',title=>'상품별 개별정책 설정', checked=>'checked', is_nessesary=>true, desc=>"",width=>20);
	$colums[one_commission] = array(value=>'one_commission',title=>'개별수수료 사용여부', checked=>'checked', is_nessesary=>true, desc=>"",width=>20);
	$colums[account_type] = array(value=>'account_type',title=>'정산방식', checked=>'checked', is_nessesary=>true, desc=>"",width=>20);
	$colums[commission] = array(value=>'commission',title=>'수수료', checked=>'checked', is_nessesary=>true, desc=>"",width=>20);
	$colums[wholesale_commission] = array(value=>'wholesale_commission',title=>'도매 수수료', checked=>'checked', is_nessesary=>true, desc=>"",width=>20);


	/*
	$colums[gid] = array(value=>'gid',title=>'상품코드', checked=>'checked');
	$colums[gname] = array(value=>'gname',title=>'상품명', checked=>'checked');
	$colums[standard] = array(value=>'standard',title=>'규격', checked=>'checked');
	$colums[unit] = array(value=>'unit',title=>'단위', checked=>'checked');
	$colums[item_account] = array(value=>'item_account',title=>'품목계정', checked=>'checked');
	$colums[basic_unit] = array(value=>'basic_unit',title=>'기본단위', checked=>'checked');
	$colums[place_name] = array(value=>'place_name',title=>'사업장(창고)', checked=>'checked');
	$colums[section_name] = array(value=>'section_name',title=>'보관장소', checked=>'checked');
	$colums[stock] = array(value=>'stock',title=>'현재고', checked=>'checked');
	$colums[sell_ing_cnt] = array(value=>'sell_ing_cnt',title=>'가용재고(-)', checked=>'checked');
	$colums[order_ing_cnt] = array(value=>'order_ing_cnt',title=>'발주미입고', checked=>'checked');
	$colums[safestock] = array(value=>'safestock',title=>'안전재고', checked=>'checked');

	$colums[wantage_stock] = array(value=>'wantage_stock',title=>'부족재고', checked=>'checked');
	$colums[buying_price] = array(value=>'buying_price',title=>'매입가', checked=>'checked');
	$colums[sellprice] = array(value=>'sellprice',title=>'판매가', checked=>'checked');
	$colums[order_cnt] = array(value=>'order_cnt',title=>'판매수량', checked=>'checked');
	$colums[item_barcode] = array(value=>'item_barcode',title=>'바코드', checked=>'checked');
	*/


}

?>