<?

if($info_type == "deposit_info"){

	$colums[regdate] = array(value=>'regdate',title=>'처리일', checked=>'checked');
	$colums[use_type] = array(value=>'use_type',title=>'입/출금 구분', checked=>'checked');
	$colums[state] = array(value=>'state',title=>'처리상태', checked=>'checked');
	$colums[use_state] = array(value=>'use_state',title=>'타입', checked=>'checked');
	$colums[deposit] = array(value=>'deposit',title=>'입/출금 금액', checked=>'checked');
	$colums[use_deposit] = array(value=>'use_deposit',title=>'총누적금액', checked=>'checked');
	$colums[user_name] = array(value=>'user_name',title=>'회원명', checked=>'checked');
	$colums[user_id] = array(value=>'user_id',title=>'아이디', checked=>'checked');
	$colums[etc] = array(value=>'etc',title=>'입/출금 상세내역', checked=>'checked');
	$colums[charger_name] = array(value=>'charger_name',title=>'처리담당자', checked=>'checked');
	$colums[charger_id] = array(value=>'charger_id',title=>'담당자 아이디', checked=>'checked');

}else if($info_type == "deposit_use"){

	$colums[name] = array(value=>'name',title=>'회원명', checked=>'checked');
	$colums[id] = array(value=>'id',title=>'아이디', checked=>'checked');
	$colums[gp_name] = array(value=>'gp_name',title=>'회원그룹', checked=>'checked');
	$colums[total_deposit] = array(value=>'total_deposit',title=>'입금금액', checked=>'checked');
	$colums[total_use_deposit] = array(value=>'total_use_deposit',title=>'사용완료 금액', checked=>'checked');
	$colums[total_withdraw_deposit] = array(value=>'total_withdraw_deposit',title=>'출금금액', checked=>'checked');
	$colums[deposit] = array(value=>'deposit',title=>'보유예치금액', checked=>'checked');
	$colums[ranking] = array(value=>'ranking',title=>'보유순위', checked=>'checked');

}if($info_type == "deposit_informal"){
	
	$colums[edit_date] = array(value=>'edit_date',title=>'일자', checked=>'checked');
	$colums[wait_deposit] = array(value=>'wait_deposit',title=>'입금대기', checked=>'checked');
	$colums[cancel_deposit] = array(value=>'cancel_deposit',title=>'입금취소', checked=>'checked');
	$colums[complete_deposit] = array(value=>'complete_deposit',title=>'입금완료', checked=>'checked');
	$colums[use_deposit] = array(value=>'use_deposit',title=>'사용완료', checked=>'checked');
	$colums[request_deposit] = array(value=>'request_deposit',title=>'출금요청', checked=>'checked');
	$colums[request_cancel_deposit] = array(value=>'request_cancel_deposit',title=>'출금취소', checked=>'checked');
	$colums[confirm_deposit] = array(value=>'confirm_deposit',title=>'출금확정', checked=>'checked');
	$colums[withdrawl_deposit] = array(value=>'withdrawl_deposit',title=>'출금완료', checked=>'checked');
	$colums[total_deposit] = array(value=>'total_deposit',title=>'입출금합계', checked=>'checked');

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