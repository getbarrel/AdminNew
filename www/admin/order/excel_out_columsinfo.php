<?
if($excel_type == "delivery"){
	$colums[oid] = array(value=>'oid',title=>'주문번호', checked=>'checked');
	$colums[od_ix] = array(value=>'od_ix',title=>'주문상세번호', checked=>'checked');
	$colums[delivery_method] = array(value=>'delivery_method',title=>'배송방식',checked=>'checked');
	$colums[quick] = array(value=>'quick',title=>'택배사',checked=>'checked');
	$colums[invoiceno] = array(value=>'invoice_no',title=>'송장번호',checked=>'checked');
	$colums[pname] = array(value=>'pname',title=>'상품명', checked=>'checked');
	$colums[optiontext] = array(value=>'optiontext',title=>'상품옵션', checked=>'checked');
	$colums[pcnt] = array(value=>'pcnt',title=>'상품수량', checked=>'checked');
	$colums[bname] = array(value=>'bname',title=>'주문자이름', checked=>'checked');
	$colums[rname] = array(value=>'rname',title=>'수취인이름', checked=>'checked');
	$colums[rmail] = array(value=>'rmail',title=>'수취인메일', checked=>'checked');
	$colums[rtel] = array(value=>'rtel',title=>'수취인전화', checked=>'checked');
	$colums[rmobile] = array(value=>'rmobile',title=>'수취인핸드폰', checked=>'checked');
	$colums[zip] = array(value=>'zip',title=>'우편번호', checked=>'checked');
	$colums[addr] = array(value=>'addr',title=>'배달주소', checked=>'checked');
	$colums[msg] = array(value=>'msg',title=>'배송메시지', checked=>'checked');

	//$colums[date] = array(value=>'date',title=>'주문일자', checked=>'checked');
	//$colums[company_id] = array(value=>'company_id',title=>'회사코드', checked=>'checked');
	//$colums[company_name] = array(value=>'company_name',title=>'회사명', checked=>'checked');


/*
	$colums[bname] = array(value=>'bname',title=>'주문자이름', checked=>'checked');
	$colums[btel] = array(value=>'btel',title=>'주문자전화', checked=>'checked');
	$colums[bmobile] = array(value=>'bmobile',title=>'주문자핸드폰', checked=>'checked');
	$colums[bmail] = array(value=>'bmail',title=>'주문자메일', checked=>'checked');
	$colums[rname] = array(value=>'rname',title=>'수취인이름', checked=>'checked');
	$colums[rmail] = array(value=>'rmail',title=>'수취인메일', checked=>'');
	$colums[rtel] = array(value=>'rtel',title=>'수취인전화', checked=>'');
	$colums[rmobile] = array(value=>'rmobile',title=>'수취인핸드폰', checked=>'');
	$colums[zip] = array(value=>'zip',title=>'우편번호', checked=>'');
	$colums[addr] = array(value=>'addr',title=>'배달주소', checked=>'');

	$colums[status] = array(value=>'status',title=>'주문상태', checked=>'');
	$colums[method] = array(value=>'method',title=>'결제방법', checked=>'');
	$colums[msg] = array(value=>'msg',title=>'배송메시지', checked=>'');

	$colums[pid] = array(value=>'pid',title=>'상품코드', checked=>'');
	$colums[pname] = array(value=>'pname',title=>'상품명', checked=>'checked');
	
	$colums[optiontext] = array(value=>'optiontext',title=>'상품옵션', checked=>'checked');
	$colums[pcnt] = array(value=>'pcnt',title=>'상품수량', checked=>'checked');
	$colums[coprice] = array(value=>'coprice',title=>'공급가', checked=>'');
	$colums[psprice] = array(value=>'psprice',title=>'판매가', checked=>'');
	$colums[ptprice] = array(value=>'ptprice',title=>'판매총액', checked=>'');
	$colums[reserve] = array(value=>'reserve',title=>'적립금', checked=>'');
	
	$colums[deliveryprice] = array(value=>'deliveryprice',title=>'배송비', checked=>'');
	$colums[deliverypaytype] = array(value=>'deliverypaytype',title=>'배송비결제', checked=>'');
	$colums[deliverypayuse] = array(value=>'deliverypayuse',title=>'배송비부담', checked=>'');
*/
}else{
	$colums[oid] = array(value=>'oid',title=>'주문번호', checked=>'checked');
	$colums[od_ix] = array(value=>'od_ix',title=>'주문상세번호', checked=>'checked');
	$colums[date] = array(value=>'date',title=>'주문일자', checked=>'checked');
	$colums[bname] = array(value=>'bname',title=>'주문자이름', checked=>'checked');
	$colums[btel] = array(value=>'btel',title=>'주문자전화', checked=>'checked');
	$colums[bmobile] = array(value=>'bmobile',title=>'주문자핸드폰', checked=>'checked');
	$colums[bmail] = array(value=>'bmail',title=>'주문자메일', checked=>'checked');
	$colums[rname] = array(value=>'rname',title=>'수취인이름', checked=>'checked');
	$colums[rmail] = array(value=>'rmail',title=>'수취인메일', checked=>'');
	$colums[rtel] = array(value=>'rtel',title=>'수취인전화', checked=>'');
	$colums[rmobile] = array(value=>'rmobile',title=>'수취인핸드폰', checked=>'');
	$colums[zip] = array(value=>'zip',title=>'우편번호', checked=>'');
	$colums[addr] = array(value=>'addr',title=>'배달주소', checked=>'');
	$colums[status] = array(value=>'status',title=>'주문처리상태', checked=>'');
	$colums[delivery_status] = array(value=>'delivery_status',title=>'출고처리상태', checked=>'');
	$colums[method] = array(value=>'method',title=>'결제방법', checked=>'');
	$colums[msg] = array(value=>'msg',title=>'배송메시지', checked=>'');
	$colums[cid] = array(value=>'cid',title=>'카테고리', checked=>'');
	$colums[pid] = array(value=>'pid',title=>'상품코드', checked=>'');
	$colums[pname] = array(value=>'pname',title=>'상품명', checked=>'checked');
	$colums[gid] = array(value=>'gid',title=>'품목코드', checked=>'');
	$colums[gname] = array(value=>'gname',title=>'재고상품명', checked=>'checked');
	$colums[company_id] = array(value=>'company_id',title=>'회사코드', checked=>'checked');
	$colums[company_name] = array(value=>'company_name',title=>'회사명', checked=>'checked');
	$colums[optiontext] = array(value=>'optiontext',title=>'상품옵션', checked=>'checked');
	$colums[pcnt] = array(value=>'pcnt',title=>'상품수량', checked=>'checked');
	$colums[coprice] = array(value=>'coprice',title=>'공급가', checked=>'');
	$colums[psprice] = array(value=>'psprice',title=>'판매가', checked=>'');
	$colums[ptprice] = array(value=>'ptprice',title=>'판매총액', checked=>'');
	$colums[reserve] = array(value=>'reserve',title=>'적립금', checked=>'');
	$colums[use_coupon] = array(value=>'use_coupon',title=>'쿠폰사용금액', checked=>'');
	$colums[delivery_method] = array(value=>'delivery_method',title=>'배송방식',checked=>'');
	$colums[quick] = array(value=>'quick',title=>'택배사',checked=>'');
	$colums[invoiceno] = array(value=>'invoice_no',title=>'송장번호',checked=>'');
	$colums[deliveryprice] = array(value=>'deliveryprice',title=>'배송비', checked=>'');
	$colums[deliverypaytype] = array(value=>'deliverypaytype',title=>'배송비결제', checked=>'');
	$colums[deliverypayuse] = array(value=>'deliverypayuse',title=>'배송비부담', checked=>'');
	$colums[dc_date] = array(value=>'dc_date',title=>'배송완료일자', checked=>'checked');
	$colums[is_erp_link] = array(value=>'is_erp_link',title=>'ERP매출', checked=>'checked');
	$colums[is_erp_link_return] = array(value=>'is_erp_link_return',title=>'ERP반품', checked=>'checked');
	$colums[erp_link_date] = array(value=>'erp_link_date',title=>'ERP반영일', checked=>'checked');
}
/*
$colums2[pid] = array(value=>'pid',title=>'상품코드', checked=>'');
$colums2[pname] = array(value=>'pname',title=>'상품명', checked=>'checked');
$colums2[optiontext] = array(value=>'optiontext',title=>'상품옵션', checked=>'checked');
$colums2[pcnt] = array(value=>'pcnt',title=>'상품수량', checked=>'checked');
$colums2[coprice] = array(value=>'coprice',title=>'공급가', checked=>'');
$colums2[psprice] = array(value=>'psprice',title=>'판매가', checked=>'');
$colums2[ptprice] = array(value=>'ptprice',title=>'판매총액', checked=>'');
$colums2[reserve] = array(value=>'reserve',title=>'적립금', checked=>'');
$colums2[invoiceno] = array(value=>'invoice_no',title=>'송장번호',checked=>'');
$colums2[quick] = array(value=>'quick',title=>'택배사',checked=>'');
$colums2[deliveryprice] = array(value=>'deliveryprice',title=>'배송비', checked=>'');
$colums2[deliverypaytype] = array(value=>'deliverypaytype',title=>'배송비결제', checked=>'');
$colums2[deliverypayuse] = array(value=>'deliverypayuse',title=>'배송비부담', checked=>'');
*/
?>