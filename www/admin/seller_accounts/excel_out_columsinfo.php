<?

if($info_type == "accounts_plan"){

	$colums[regdate] = array(value=>'regdate',title=>'주문일자', checked=>'checked');
	$colums[oid] = array(value=>'oid',title=>'주문번호', checked=>'checked');
	$colums[od_ix] = array(value=>'od_ix',title=>'주문상세번호', checked=>'checked');
	$colums[bname] = array(value=>'bname',title=>'주문자명', checked=>'checked');
	$colums[com_name] = array(value=>'com_name',title=>'셀러명', checked=>'checked');
	$colums[account_type] = array(value=>'account_type',title=>'정산방식', checked=>'checked');

	$colums[surtax_yorn] = array(value=>'surtax_yorn',title=>'과세여부', checked=>'checked');

	$colums[pname] = array(value=>'pname',title=>'상품명', checked=>'checked');

	$colums[option_text] = array(value=>'option_text',title=>'옵션', checked=>'checked');

	$colums[pcnt] = array(value=>'pcnt',title=>'수량', checked=>'checked');

	$colums[status] = array(value=>'status',title=>'배송처리상태', checked=>'checked');

	$colums[p_expect_price] = array(value=>'p_expect_price',title=>'정산예정금액(+)', checked=>'checked');

	$colums[p_dc_allotment_price] = array(value=>'p_dc_allotment_price',title=>'할인부담금액(-)', checked=>'checked');

	$colums[p_fee_price] = array(value=>'p_fee_price',title=>'수수료(-)', checked=>'checked');

	$colums[p_ac_price] = array(value=>'p_ac_price',title=>'실정산금액', checked=>'checked');

	$colums[d_expect_price] = array(value=>'d_expect_price',title=>'배송비(+)', checked=>'checked');

	$colums[d_dc_allotment_price] = array(value=>'d_dc_allotment_price',title=>'배송비할인부담금액(-)', checked=>'checked');

	$colums[d_ac_price] = array(value=>'d_ac_price',title=>'배송비실정산금액', checked=>'checked');

	$colums[ac_price] = array(value=>'ac_price',title=>'실정산합계', checked=>'checked');

	$colums[accounts_expect_date] = array(value=>'accounts_expect_date',title=>'정산예정일', checked=>'checked');

	$colums[accounts_status] = array(value=>'accounts_status',title=>'정산상태', checked=>'checked');

	$colums[account_method] = array(value=>'account_method',title=>'정산지급방식', checked=>'checked');


}

?>