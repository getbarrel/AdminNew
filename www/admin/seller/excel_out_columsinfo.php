<?
if($info_type == "seller_user_list"){
	$colums[com_name] = array(value=>'com_name',title=>'업체명', checked=>'checked');
	$colums[charge_code] = array(value=>'charge_code',title=>'대표여부', checked=>'checked');
	$colums[com_ceo] = array(value=>'com_ceo',title=>'대표자명', checked=>'checked');
	$colums[name] = array(value=>'name',title=>'셀러명', checked=>'checked');
	$colums[id] = array(value=>'id',title=>'셀러 아이디', checked=>'');
	$colums[pcs] = array(value=>'pcs',title=>'연락처', checked=>'');
	$colums[mail] = array(value=>'mail',title=>'이메일', checked=>'');
	$colums[auth] = array(value=>'auth',title=>'승인', checked=>'');
	$colums[date] = array(value=>'date',title=>'승인일', checked=>'');
}else if($info_type == "member_resign"){
	$colums[mem_code] = array(value=>'mem_code',title=>'사원코드', checked=>'checked');
	$colums[join_date] = array(value=>'join_date',title=>'입사일', checked=>'checked');
	$colums[name] = array(value=>'name',title=>'이름', checked=>'checked');
	$colums[com_name] = array(value=>'section_name',title=>'근무사업장', checked=>'checked');
	$colums[com_group] = array(value=>'com_group',title=>'부서그룹', checked=>'checked');
	$colums[department] = array(value=>'department',title=>'부서', checked=>'checked');
	$colums[position] = array(value=>'position',title=>'직위', checked=>'checked');
	$colums[duty] = array(value=>'duty',title=>'직책', checked=>'checked');
	$colums[pcs] = array(value=>'pcs',title=>'연락처', checked=>'checked');
	$colums[mail] = array(value=>'mail',title=>'이메일', checked=>'checked');
}else if($info_type == "member_lump"){
	$colums[mem_code] = array(value=>'mem_code',title=>'사원코드', checked=>'checked');
	$colums[join_date] = array(value=>'join_date',title=>'입사일', checked=>'checked');
	$colums[name] = array(value=>'name',title=>'이름', checked=>'checked');
	$colums[com_name] = array(value=>'section_name',title=>'근무사업장', checked=>'checked');
	$colums[com_group] = array(value=>'com_group',title=>'부서그룹', checked=>'checked');
	$colums[department] = array(value=>'department',title=>'부서', checked=>'checked');
	$colums[position] = array(value=>'position',title=>'직위', checked=>'checked');
	$colums[duty] = array(value=>'duty',title=>'직책', checked=>'checked');
	$colums[pcs] = array(value=>'pcs',title=>'연락처', checked=>'checked');
	$colums[mail] = array(value=>'mail',title=>'이메일', checked=>'checked');
}else if($info_type == "seller_list"){
	$colums[seller_level] = array(value=>'seller_level',title=>'거래처등급', checked=>'checked');
	$colums[seller_date] = array(value=>'seller_date',title=>'거래시작일', checked=>'checked');
	$colums[company_code] = array(value=>'company_code',title=>'업체코드', checked=>'checked');
	$colums[seller_type] = array(value=>'seller_type',title=>'거래처유형', checked=>'checked');
	$colums[com_name] = array(value=>'com_name',title=>'사업자명', checked=>'checked');
	$colums[com_div] = array(value=>'com_div',title=>'사업자유형', checked=>'checked');
	$colums[nationality] = array(value=>'nationality',title=>'국내외구분', checked=>'checked');
	$colums[com_phone] = array(value=>'com_phone',title=>'대표전화', checked=>'checked');
	$colums[com_email] = array(value=>'com_email',title=>'대표이메일1', checked=>'checked');
	$colums[customer_phone] = array(value=>'customer_phone',title=>'담당자전화번호', checked=>'checked');
	$colums[customer_mobile] = array(value=>'customer_mobile',title=>'담당자핸드폰번호', checked=>'checked');
	$colums[loan_price] = array(value=>'loan_price',title=>'여신한도', checked=>'checked');
	$colums[deposit_price] = array(value=>'deposit_price',title=>'보증금', checked=>'checked');
}else if($info_type == "company_list"){
	//$colums[name] = array(value=>'name',title=>'회원명', checked=>'checked');
	//$colums[id] = array(value=>'id',title=>'아이디', checked=>'checked');
	$colums[com_ceo] = array(value=>'com_ceo',title=>'대표자명', checked=>'checked');
	$colums[com_phone] = array(value=>'com_phone',title=>'대표전화', checked=>'checked');
	$colums[com_email] = array(value=>'com_email',title=>'대표이메일', checked=>'checked');
	$colums[seller_cid] = array(value=>'seller_cid',title=>'주요상품군', checked=>'checked');
	//$colums[seller_level] = array(value=>'seller_level',title=>'거래처등급', checked=>'checked');
	//$colums[seller_date] = array(value=>'seller_date',title=>'거래시작일', checked=>'checked');
	//$colums[company_code] = array(value=>'company_code',title=>'업체코드', checked=>'checked');
	$colums[seller_type] = array(value=>'seller_type',title=>'거래처유형', checked=>'checked');
	$colums[com_name] = array(value=>'com_name',title=>'사업자명', checked=>'checked');
	$colums[com_div] = array(value=>'com_div',title=>'사업자유형', checked=>'checked');
	$colums[nationality] = array(value=>'nationality',title=>'국내외구분', checked=>'checked');
	$colums[customer_phone] = array(value=>'customer_phone',title=>'담당자전화번호', checked=>'checked');
	$colums[customer_mobile] = array(value=>'customer_mobile',title=>'담당자핸드폰번호', checked=>'checked');
	$colums[loan_price] = array(value=>'loan_price',title=>'여신한도', checked=>'checked');
	$colums[deposit_price] = array(value=>'deposit_price',title=>'보증금금', checked=>'checked');
	$colums[seller_auth] = array(value=>'seller_auth',title=>'승인여부', checked=>'checked');
	$colums[authorized_date] = array(value=>'authorized_date',title=>'승인일', checked=>'checked');
	$colums[minishop_use] = array(value=>'minishop_use',title=>'미니샵 사용여부', checked=>'checked');
	$colums[goods_total] = array(value=>'goods_total',title=>'상품수', checked=>'checked');
	$colums[reg_et_ix] = array(value=>'reg_et_ix',title=>'전자계약서 발급여부', checked=>'checked');
	$colums[contract_title] = array(value=>'contract_title',title=>'전자계약서명', checked=>'checked');
	$colums[econtract_commission] = array(value=>'econtract_commission',title=>'전자계약서 수수료', checked=>'checked');
}else{
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


}

?>