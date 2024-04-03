<?
if($info_type == "warehouse"){
	$colums[pi_ix] = array(value=>'pi_ix',title=>'사업장(창고) 코드', checked=>'checked');
	$colums[place_name] = array(value=>'place_name',title=>'사업장', checked=>'checked');
	$colums[ps_ix] = array(value=>'ps_ix',title=>'보관장소', checked=>'checked');
	$colums[section_name] = array(value=>'section_name',title=>'보관장소명', checked=>'checked');
	$colums[stock] = array(value=>'stock',title=>'현재고', checked=>'checked');
	$colums[buying_price] = array(value=>'buying_price',title=>'매입가', checked=>'checked');
	$colums[buying_price_share] = array(value=>'buying_price_share',title=>'매입가점유율', checked=>'checked');
	$colums[stock_share] = array(value=>'stock_share',title=>'현재고 점유율', checked=>'checked');
	$colums[order_cnt] = array(value=>'order_cnt',title=>'총판매수량', checked=>'checked');
	$colums[order_share] = array(value=>'order_share',title=>'총판매수량 점유율', checked=>'checked');
}else if($info_type == "category"){
	$colums[cid] = array(value=>'cid',title=>'품목분류 코드', checked=>'checked');
	$colums[cname] = array(value=>'cname',title=>'품목분류명', checked=>'checked');
	$colums[stock] = array(value=>'stock',title=>'현재고', checked=>'checked');
	$colums[buying_price] = array(value=>'buying_price',title=>'매입가', checked=>'checked');
	$colums[buying_price_share] = array(value=>'buying_price_share',title=>'매입가점유율', checked=>'checked');
	$colums[stock_share] = array(value=>'stock_share',title=>'현재고 점유율', checked=>'checked');
	$colums[order_cnt] = array(value=>'order_cnt',title=>'총판매수량', checked=>'checked');
	$colums[order_share] = array(value=>'order_share',title=>'총판매수량 점유율', checked=>'checked');
}else if($info_type == "stocked"){
	$colums[gid] = array(value=>'gid',title=>'품목코드', checked=>'checked');
	$colums[gname] = array(value=>'gname',title=>'품목명', checked=>'checked');
	$colums[standard] = array(value=>'standard',title=>'규격', checked=>'checked');
	$colums[unit] = array(value=>'unit',title=>'단위', checked=>'checked');
	$colums[item_account] = array(value=>'item_account',title=>'품목계정', checked=>'checked');
	$colums[basic_unit] = array(value=>'basic_unit',title=>'기본단위', checked=>'checked');
	$colums[place_name] = array(value=>'place_name',title=>'사업장(창고)', checked=>'checked');
	$colums[section_name] = array(value=>'section_name',title=>'보관장소', checked=>'checked');

	$colums[vdate] = array(value=>'vdate',title=>'입고일', checked=>'checked');
	$colums[delivery_type] = array(value=>'delivery_type',title=>'입고유형', checked=>'checked');
	$colums[customer_name] = array(value=>'customer_name',title=>'입고처', checked=>'checked');
	$colums[delivery_cnt] = array(value=>'delivery_cnt',title=>'입고수량', checked=>'checked');
	$colums[delivery_price] = array(value=>'delivery_price',title=>'입고가격', checked=>'checked');
	$colums[name] = array(value=>'name',title=>'작성자', checked=>'checked');
	$colums[regdate] = array(value=>'regdate',title=>'입고일자', checked=>'checked');
	$colums[delivery_msg] = array(value=>'delivery_msg',title=>'입고메세지', checked=>'checked');
}else if($info_type == "warehousing"){
	$colums[gid] = array(value=>'gid',title=>'품목코드', checked=>'checked');
	$colums[gname] = array(value=>'gname',title=>'품목명', checked=>'checked');
	$colums[standard] = array(value=>'standard',title=>'규격', checked=>'checked');
	$colums[unit] = array(value=>'unit',title=>'단위', checked=>'checked');
	$colums[item_account] = array(value=>'item_account',title=>'품목계정', checked=>'checked');
	$colums[basic_unit] = array(value=>'basic_unit',title=>'기본단위', checked=>'checked');
	$colums[place_name] = array(value=>'place_name',title=>'사업장(창고)', checked=>'checked');
	$colums[section_name] = array(value=>'section_name',title=>'보관장소', checked=>'checked');

	$colums[vdate] = array(value=>'vdate',title=>'입/출고일', checked=>'checked');
	$colums[delivery_type] = array(value=>'delivery_type',title=>'입/출고유형', checked=>'checked');
	$colums[customer_name] = array(value=>'customer_name',title=>'입/출고처', checked=>'checked');
	$colums[delivery_cnt] = array(value=>'delivery_cnt',title=>'입/출고수량', checked=>'checked');
	$colums[delivery_price] = array(value=>'delivery_price',title=>'입/출고가격', checked=>'checked');
	$colums[name] = array(value=>'name',title=>'작성자', checked=>'checked');
	$colums[regdate] = array(value=>'regdate',title=>'입/출고일자', checked=>'checked');
	$colums[delivery_msg] = array(value=>'delivery_msg',title=>'입/출고메세지', checked=>'checked');
}else if($info_type == "stock_output" || $info_type == "delivery"){
	$colums[gid] = array(value=>'gid',title=>'품목코드', checked=>'checked');
	$colums[gname] = array(value=>'gname',title=>'품목명', checked=>'checked');
	$colums[standard] = array(value=>'standard',title=>'규격', checked=>'checked');
	$colums[unit] = array(value=>'unit',title=>'단위', checked=>'checked');
	//$colums[item_account] = array(value=>'item_account',title=>'품목계정', checked=>'checked');
	//$colums[basic_unit] = array(value=>'basic_unit',title=>'기본단위', checked=>'checked');
	$colums[place_name] = array(value=>'place_name',title=>'사업장(창고)', checked=>'checked');
	$colums[section_name] = array(value=>'section_name',title=>'보관장소', checked=>'checked');

	//$colums[vdate] = array(value=>'vdate',title=>'입고일', checked=>'checked');
	$colums[h_type] = array(value=>'h_type',title=>'출고유형', checked=>'checked');
	$colums[customer_name] = array(value=>'customer_name',title=>'출고처', checked=>'checked');
	$colums[amount] = array(value=>'amount',title=>'출고수량', checked=>'checked');
	$colums[price] = array(value=>'price',title=>'출고가격', checked=>'checked');
	$colums[charger_name] = array(value=>'charger_name',title=>'작성자', checked=>'checked');
	$colums[regdate] = array(value=>'regdate',title=>'출고일자', checked=>'checked');
	$colums[msg] = array(value=>'msg',title=>'출고메세지', checked=>'checked');

	$colums[order_from] = array(value=>'order_from',title=>'판매처', checked=>'');
	$colums[delivery_method] = array(value=>'delivery_method',title=>'배송방식', checked=>'');
	$colums[quick] = array(value=>'quick',title=>'배송업체', checked=>'');
	$colums[invoice_no] = array(value=>'invoice_no',title=>'송장번호', checked=>'');
	$colums[delivery_price] = array(value=>'delivery_price',title=>'배송비', checked=>'');


	/*
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
}else if($info_type == "delivery_item"){

	$colums[gid] = array(value=>'gid',title=>'품목코드', checked=>'checked');
	$colums[gname] = array(value=>'gname',title=>'품목명', checked=>'checked');
	$colums[standard] = array(value=>'standard',title=>'규격', checked=>'checked');
	
	$colums[item_account] = array(value=>'item_account',title=>'품목계정', checked=>'checked');
	$colums[amount] = array(value=>'amount',title=>'수량', checked=>'checked');
	$colums[company_name] = array(value=>'company_name',title=>'사업장', checked=>'checked');
	$colums[place_name] = array(value=>'place_name',title=>'창고', checked=>'checked');
	$colums[section_name] = array(value=>'section_name',title=>'보관장소', checked=>'checked');
	$colums[unit] = array(value=>'unit',title=>'단위', checked=>'checked');
	$colums[expiry_date] = array(value=>'expiry_date',title=>'유효기간', checked=>'checked');
	$colums[stock] = array(value=>'stock',title=>'현재고', checked=>'checked');
	//$colums[sell_ing_cnt] = array(value=>'sell_ing_cnt',title=>'가용재고(-)', checked=>'checked');
	//$colums[order_ing_cnt] = array(value=>'order_ing_cnt',title=>'발주미입고', checked=>'checked');
	//$colums[safestock] = array(value=>'safestock',title=>'안전재고', checked=>'checked');

	/*
	$colums[wantage_stock] = array(value=>'wantage_stock',title=>'부족재고', checked=>'checked');
	$colums[buying_price] = array(value=>'buying_price',title=>'매입가', checked=>'checked');
	$colums[sellprice] = array(value=>'sellprice',title=>'판매가', checked=>'checked');
	$colums[order_cnt] = array(value=>'order_cnt',title=>'판매수량', checked=>'checked');
	$colums[item_barcode] = array(value=>'item_barcode',title=>'바코드', checked=>'checked');
	*/
}elseif($info_type == 'purchase_ready'){
	$colums[ioid] = array(value=>'ioid',title=>'발주번호', checked=>'checked');
	$colums[regdate] = array(value=>'regdate',title=>'청구확정일', checked=>'checked');
	$colums[ci_name] = array(value=>'ci_name',title=>'매입처', checked=>'checked');
	
	$colums[gcode] = array(value=>'gcode',title=>'품목대표코드', checked=>'checked');
	$colums[gid] = array(value=>'gid',title=>'품목코드', checked=>'checked');
	$colums[gname] = array(value=>'gname',title=>'품목명', checked=>'checked');
	$colums[goods_cnt] = array(value=>'goods_cnt',title=>'요청총수량', checked=>'checked');
	$colums[goods_price] = array(value=>'goods_price',title=>'품목금액합계', checked=>'checked');
	$colums[delivery_price] = array(value=>'delivery_price',title=>'배송비', checked=>'checked');
	$colums[total_price] = array(value=>'total_price',title=>'총합계', checked=>'checked');
	$colums[delivery_name] = array(value=>'delivery_name',title=>'납품처명', checked=>'checked');
	$colums[status] = array(value=>'status',title=>'처리상태', checked=>'checked');
	$colums[charger] = array(value=>'charger',title=>'업체담당', checked=>'checked');

	$colums[unit] = array(value=>'unit',title=>'단위', checked=>'checked');
	$colums[stock] = array(value=>'stock',title=>'현재고', checked=>'checked');
	$colums[order_basic_unit] = array(value=>'order_basic_unit',title=>'매입단위', checked=>'checked');
	$colums[change_amount] = array(value=>'change_amount',title=>'환산수량', checked=>'checked');

}elseif($info_type == 'purchase_apply_complete'){
	$colums[ioid] = array(value=>'ioid',title=>'발주번호', checked=>'checked');
	$colums[regdate] = array(value=>'regdate',title=>'청구확정일', checked=>'checked');
	$colums[ci_name] = array(value=>'ci_name',title=>'매입처', checked=>'checked');
	
	$colums[gcode] = array(value=>'gcode',title=>'품목대표코드', checked=>'checked');
	$colums[gid] = array(value=>'gid',title=>'품목코드', checked=>'checked');
	$colums[gname] = array(value=>'gname',title=>'품목명', checked=>'checked');
	$colums[goods_cnt] = array(value=>'goods_cnt',title=>'요청총수량', checked=>'checked');
	$colums[goods_price] = array(value=>'goods_price',title=>'품목금액합계', checked=>'checked');
	$colums[delivery_price] = array(value=>'delivery_price',title=>'배송비', checked=>'checked');
	$colums[total_price] = array(value=>'total_price',title=>'총합계', checked=>'checked');
	$colums[delivery_name] = array(value=>'delivery_name',title=>'납품처명', checked=>'checked');
	$colums[status] = array(value=>'status',title=>'처리상태', checked=>'checked');
	$colums[charger] = array(value=>'charger',title=>'업체담당', checked=>'checked');

	$colums[unit] = array(value=>'unit',title=>'단위', checked=>'checked');
	$colums[stock] = array(value=>'stock',title=>'현재고', checked=>'checked');
	$colums[order_basic_unit] = array(value=>'order_basic_unit',title=>'매입단위', checked=>'checked');
	$colums[change_amount] = array(value=>'change_amount',title=>'환산수량', checked=>'checked');

}elseif($info_type == 'purchase_complete'){
	$colums[ioid] = array(value=>'ioid',title=>'발주번호', checked=>'checked');
	$colums[regdate] = array(value=>'regdate',title=>'청구확정일', checked=>'checked');
	$colums[ci_name] = array(value=>'ci_name',title=>'매입처', checked=>'checked');
	$colums[gcode] = array(value=>'gcode',title=>'품목대표코드', checked=>'checked');
	$colums[gid] = array(value=>'gid',title=>'품목코드', checked=>'checked');
	$colums[gname] = array(value=>'gname',title=>'품목명', checked=>'checked');
	$colums[goods_cnt] = array(value=>'goods_cnt',title=>'요청총수량', checked=>'checked');
	$colums[total_price] = array(value=>'total_price',title=>'총합계', checked=>'checked');
	$colums[delivery_name] = array(value=>'delivery_name',title=>'납품처명', checked=>'checked');
	$colums[status] = array(value=>'status',title=>'처리상태', checked=>'checked');
	$colums[charger] = array(value=>'charger',title=>'업체담당', checked=>'checked');

	$colums[unit] = array(value=>'unit',title=>'단위', checked=>'checked');
	$colums[stock] = array(value=>'stock',title=>'현재고', checked=>'checked');
	$colums[order_basic_unit] = array(value=>'order_basic_unit',title=>'매입단위', checked=>'checked');
	$colums[change_amount] = array(value=>'change_amount',title=>'환산수량', checked=>'checked');

}else{
	$colums[gid] = array(value=>'gid',title=>'품목코드', checked=>'checked');
	$colums[gname] = array(value=>'gname',title=>'품목명', checked=>'checked');
	$colums[standard] = array(value=>'standard',title=>'규격', checked=>'checked');
	$colums[unit] = array(value=>'unit',title=>'단위', checked=>'checked');
	$colums[item_account] = array(value=>'item_account',title=>'품목계정', checked=>'checked');
	$colums[basic_unit] = array(value=>'basic_unit',title=>'기본단위', checked=>'checked');
	$colums[place_name] = array(value=>'place_name',title=>'사업장(창고)', checked=>'checked');
	$colums[section_name] = array(value=>'section_name',title=>'보관장소', checked=>'checked');
	$colums[stock] = array(value=>'stock',title=>'현재고', checked=>'checked');
	$colums[sell_ing_cnt] = array(value=>'sell_ing_cnt',title=>'진행재고(-)', checked=>'checked');
	$colums[order_ing_cnt] = array(value=>'order_ing_cnt',title=>'발주미입고(+)', checked=>'checked');
	$colums[safestock] = array(value=>'safestock',title=>'안전재고(-)', checked=>'checked');

	$colums[wantage_stock] = array(value=>'wantage_stock',title=>'부족재고', checked=>'checked');
	$colums[buying_price] = array(value=>'buying_price',title=>'매입가', checked=>'checked');
	$colums[sellprice] = array(value=>'sellprice',title=>'판매가', checked=>'checked');
	$colums[order_cnt] = array(value=>'order_cnt',title=>'판매수량', checked=>'checked');
	$colums[item_barcode] = array(value=>'item_barcode',title=>'바코드', checked=>'checked');
	$colums[id] = array(value=>'id',title=>'시스템코드', checked=>'checked');
	$colums[color] = array(value=>'color',title=>'칼라', checked=>'checked');
    $colums[size] = array(value=>'size',title=>'사이즈', checked=>'checked');
    $colums[soldout_text] = array(value=>'soldout_text',title=>'품절표시', checked=>'checked');

/*
	$colums[pid] = array(value=>'pid',title=>'품목코드', checked=>'checked');
	$colums[pname] = array(value=>'pname',title=>'품목명', checked=>'checked');
	$colums[company_id] = array(value=>'company_id',title=>'회사코드', checked=>'checked');
	$colums[company_name] = array(value=>'company_name',title=>'회사명', checked=>'checked');
	$colums[optiontext] = array(value=>'optiontext',title=>'품목옵션', checked=>'checked');
	$colums[pcnt] = array(value=>'pcnt',title=>'품목수량', checked=>'checked');
	$colums[coprice] = array(value=>'coprice',title=>'공급가', checked=>'checked');
	$colums[psprice] = array(value=>'psprice',title=>'판매가', checked=>'checked');
	$colums[ptprice] = array(value=>'ptprice',title=>'판매총액', checked=>'checked');
	$colums[reserve] = array(value=>'reserve',title=>'적립금', checked=>'checked');
	$colums[invoiceno] = array(value=>'invoice_no',title=>'송장번호',checked=>'checked');
	$colums[quick] = array(value=>'quick',title=>'택배사',checked=>'checked');
	$colums[deliveryprice] = array(value=>'deliveryprice',title=>'배송비', checked=>'checked');
	$colums[deliverypaytype] = array(value=>'deliverypaytype',title=>'배송비결제', checked=>'checked');
	$colums[deliverypayuse] = array(value=>'deliverypayuse',title=>'배송비부담', checked=>'checked');
	*/
}
/*
$colums2[pid] = array(value=>'pid',title=>'품목코드', checked=>'checked');
$colums2[pname] = array(value=>'pname',title=>'품목명', checked=>'checked');
$colums2[optiontext] = array(value=>'optiontext',title=>'품목옵션', checked=>'checked');
$colums2[pcnt] = array(value=>'pcnt',title=>'품목수량', checked=>'checked');
$colums2[coprice] = array(value=>'coprice',title=>'공급가', checked=>'checked');
$colums2[psprice] = array(value=>'psprice',title=>'판매가', checked=>'checked');
$colums2[ptprice] = array(value=>'ptprice',title=>'판매총액', checked=>'checked');
$colums2[reserve] = array(value=>'reserve',title=>'적립금', checked=>'checked');
$colums2[invoiceno] = array(value=>'invoice_no',title=>'송장번호',checked=>'checked');
$colums2[quick] = array(value=>'quick',title=>'택배사',checked=>'checked');
$colums2[deliveryprice] = array(value=>'deliveryprice',title=>'배송비', checked=>'checked');
$colums2[deliverypaytype] = array(value=>'deliverypaytype',title=>'배송비결제', checked=>'checked');
$colums2[deliverypayuse] = array(value=>'deliverypayuse',title=>'배송비부담', checked=>'checked');
*/
?>