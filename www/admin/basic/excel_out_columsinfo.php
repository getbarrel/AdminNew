<?
if($info_type == "member_list"){
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
	$colums[com_email] = array(value=>'com_email',title=>'대표이메일', checked=>'checked');
	$colums[customer_phone] = array(value=>'customer_phone',title=>'담당자전화번호', checked=>'checked');
	$colums[customer_mobile] = array(value=>'customer_mobile',title=>'담당자핸드폰번호', checked=>'checked');
	$colums[loan_price] = array(value=>'loan_price',title=>'여신한도', checked=>'checked');
	$colums[deposit_price] = array(value=>'deposit_price',title=>'보증금', checked=>'checked');
}else if($info_type == "company_list"){
	$colums[regdate] = array(value=>'regdate',title=>'등록일', checked=>'checked');
	$colums[company_code] = array(value=>'company_code',title=>'회사코드', checked=>'checked');
	$colums[com_type] = array(value=>'com_type',title=>'사업자유형', checked=>'checked');
	$colums[com_name] = array(value=>'com_name',title=>'회사명', checked=>'checked');
	$colums[com_ceo] = array(value=>'com_ceo',title=>'대표자명', checked=>'checked');
	$colums[com_div] = array(value=>'com_div',title=>'사업자유형', checked=>'checked');
	$colums[com_number] = array(value=>'com_number',title=>'사업자번호', checked=>'checked');
	$colums[nationality] = array(value=>'nationality',title=>'국내외구분', checked=>'checked');
	$colums[member_count] = array(value=>'member_count',title=>'사원수', checked=>'checked');
	$colums[com_phone] = array(value=>'com_phone',title=>'대표전화', checked=>'checked');
	$colums[com_email] = array(value=>'com_email',title=>'대표이메일', checked=>'checked');
	$colums[com_fax] = array(value=>'com_fax',title=>'대표팩스', checked=>'checked');
	$colums[com_zip] = array(value=>'com_zip',title=>'우편번호', checked=>'checked');
	$colums[com_addr1] = array(value=>'com_addr1',title=>'주소', checked=>'checked');
	$colums[com_addr2] = array(value=>'com_addr2',title=>'상세주소', checked=>'checked');
	$colums[seller_auth] = array(value=>'seller_auth',title=>'승인상태', checked=>'checked');

}else{
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


}

?>