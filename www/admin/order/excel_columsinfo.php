<?

$colums[] = array(value=>'oid',title=>'주문번호');
$colums[] = array(value=>'od_ix',title=>'주문상세번호');
$colums[] = array(value=>'cid_0',title=>'카테고리(대분류)');
$colums[] = array(value=>'cid_1',title=>'카테고리(중분류)');
$colums[] = array(value=>'cid_2',title=>'카테고리(소분류)');
$colums[] = array(value=>'cid_3',title=>'카테고리(세분류)');
$colums[] = array(value=>'pid',title=>'상품시스템코드');
$colums[] = array(value=>'pcode',title=>'상품코드');
$colums[] = array(value=>'pname_1',title=>'상품명');
$colums[] = array(value=>'pname_2',title=>'상품명(옵션포함)');
$colums[] = array(value=>'pname_3',title=>'상품명(옵션/수량)');
$colums[] = array(value=>'add_info',title=>'색상명');
$colums[] = array(value=>'optiontext_1',title=>'상품옵션');
$colums[] = array(value=>'optiontext_2',title=>'상품옵션(추가격미노출)');
$colums[] = array(value=>'pcnt',title=>'주문상품수량');
$colums[] = array(value=>'psprice',title=>'주문상품단가');
$colums[] = array(value=>'order_date',title=>'주문일자');
if($_SESSION["admininfo"]["admin_level"] > 8){
$colums[] = array(value=>'bname',title=>'주문자이름');
}
$colums[] = array(value=>'bname2',title=>'주문자이름(홍*동)');
$colums[] = array(value=>'sex',title=>'주문자성별');
$colums[] = array(value=>'age',title=>'주문자연령');
$colums[] = array(value=>'bmail',title=>'주문자메일');
$colums[] = array(value=>'mem_group',title=>'주문자그룹');
$colums[] = array(value=>'btel',title=>'주문자전화');
$colums[] = array(value=>'bmobile',title=>'주문자핸드폰');
if($_SESSION["admininfo"]["admin_level"] > 8){
	$colums[] = array(value=>'rname',title=>'수취인이름');
}
$colums[] = array(value=>'rname2',title=>'수취인이름(홍*동)');
$colums[] = array(value=>'rtel',title=>'수취인전화');
$colums[] = array(value=>'rmobile',title=>'수취인핸드폰');
$colums[] = array(value=>'zip_new',title=>'신)수취인우편번호(XXXXX)');
$colums[] = array(value=>'zip_1',title=>'구)수취인우편번호(XXXXXX)');
$colums[] = array(value=>'zip_2',title=>'신,구)우편번호(XXX-XXX)');
$colums[] = array(value=>'addr',title=>'수취인주소');
$colums[] = array(value=>'addr_1',title=>'수취인주소1');
$colums[] = array(value=>'addr_2',title=>'수취인주소2');
$colums[] = array(value=>'invoice_no',title=>'송장번호');
$colums[] = array(value=>'msg',title=>'배송메시지');
$colums[] = array(value=>'delivery_pay_method',title=>'배송결제방법');
$colums[] = array(value=>'delivery_method',title=>'배송방법');
$colums[] = array(value=>'quick',title=>'배송업체');
$colums[] = array(value=>'delivery_price',title=>'배송비(+특수지역배송비-할인 포함)');
$colums[] = array(value=>'product_coprice',title=>'공급가(상품별)');
$colums[] = array(value=>'product_dc_price',title=>'할인총금액(상품별)');
$colums[] = array(value=>'product_dc_info',title=>'할인총상세내역(상품별)');
$colums[] = array(value=>'product_dc_coupon',title=>'쿠폰할인(상품별)');
$colums[] = array(value=>'product_dc_premium',title=>'프리미엄할인(상품별)');
$colums[] = array(value=>'product_dc_app',title=>'앱할인(상품별)');
$colums[] = array(value=>'product_dc_admin',title=>'직원할인(상품별)');
$colums[] = array(value=>'product_pt_price',title=>'상품결제금액(상품별)');
$colums[] = array(value=>'status',title=>'주문상태(상품별)');
$colums[] = array(value=>'accounts_status',title=>'정산상태(상품별)');
$colums[] = array(value=>'product_expect_ac_price',title=>'정산예정금액(+)(상품별)');
$colums[] = array(value=>'product_expect_dc_allotment_price',title=>'정산할인부담금액(-)(상품별)');
$colums[] = array(value=>'product_expect_fee_price',title=>'정산수수료(-)(상품별)');
$colums[] = array(value=>'product_ac_price',title=>'실정산금액(상품별)');
$colums[] = array(value=>'ic_date',title=>'입금확인일(결제일)');
$colums[] = array(value=>'di_date',title=>'배송중일자');
$colums[] = array(value=>'dc_date',title=>'배송완료일자');
$colums[] = array(value=>'bf_date',title=>'거래확정일자');
$colums[] = array(value=>'ca_date',title=>'취소요청일자');
$colums[] = array(value=>'fc_date',title=>'환불완료일자');
$colums[] = array(value=>'user_ip',title=>'주문IP주소');


if($_SESSION["admininfo"]["admin_level"] > 8){
	$colums[] = array(value=>'company_name',title=>'업체명');
	$colums[] = array(value=>'md_name',title=>'담당MD');
	$colums[] = array(value=>'gid',title=>'품목코드');
	$colums[] = array(value=>'buserid',title=>'주문자ID');
	$colums[] = array(value=>'order_total_price',title=>'주문총금액(상품할인+배송비할인)');
	$colums[] = array(value=>'order_payment_price',title=>'주문결제금액(주문총금액-적립금)');
	$colums[] = array(value=>'use_reserve',title=>'적립금');
//	$colums[] = array(value=>'use_saveprice',title=>'예치금');
	$colums[] = array(value=>'method',title=>'결제방법');
	$colums[] = array(value=>'order_status',title=>'결제상태');
	$colums[] = array(value=>'delivery_status',title=>'출고상태(상품별)');
	$colums[] = array(value=>'refund_status',title=>'환불상태(상품별)');
	$colums[] = array(value=>'claim_apply_user',title=>'클레임신청자');
	$colums[] = array(value=>'claim_apply_msg',title=>'클레임사유');
	$colums[] = array(value=>'claim_data_channel',title=>'클레임채널');
//	$colums[] = array(value=>'charger_ix',title=>'수동주문담당자');
	$colums[] = array(value=>'order_from',title=>'판매처(제휴사명)');
	$colums[] = array(value=>'rfid',title=>'기여사이트');
	$colums[] = array(value=>'refund_bank',title=>'환불은행명');
	$colums[] = array(value=>'refund_bank_account',title=>'환불계좌번호');
    $colums[] = array(value=>'refund_bank_owner',title=>'환불계좌주명');
    $colums[] = array(value=>'tid',title=>'네이버주문번호(승인번호)');
	
	
//	$colums[] = array(value=>'co_oid',title=>'제휴사(연동)주문번호');
//	$colums[] = array(value=>'co_od_ix',title=>'제휴사(연동)주문상세번호');
//	$colums[] = array(value=>'co_delivery_no',title=>'제휴사(연동)배송번호');
//	$colums[] = array(value=>'co_product_price',title=>'제휴사(연동)정산상품금액');
//	$colums[] = array(value=>'co_delivery_price',title=>'제휴사(연동)정산배송비');
//	$colums[] = array(value=>'namsa_basic_section',title=>'남사물류센터 기본로케이션');
//	$colums[] = array(value=>'wemakeprice_options',title=>'위메프옵션(입력용)');
//	$colums[] = array(value=>'tmon_options',title=>'티몬옵션(입력용)');
}

$colums[] = array(value=>'choice_gift_order',title=>'구매금액별 사은품 선택여부');
$colums[] = array(value=>'choice_gift_prd',title=>'상품별 사은품 선택여부');
$colums[] = array(value=>'gift_type',title=>'사은품유형');
$colums[] = array(value=>'country',title=>'국가');
$colums[] = array(value=>'city',title=>'도시');
$colums[] = array(value=>'state',title=>'시도');

$essential_colums[] = array('pname_1');
$essential_colums[] = array('pcnt');
$essential_colums[] = array('bname');
//$essential_colums[] = array('btel','bmobile');
$essential_colums[] = array('rname');
$essential_colums[] = array('rtel','rmobile');
$essential_colums[] = array('zip_1','zip_2');
$essential_colums[] = array('addr','addr_1','addr_2');
$essential_colums[] = array('co_oid');


//외부몰 연동 추가
//$colums[] = array(value=>'exmall_tr_co',title=>'고객코드');
//$colums[] = array(value=>'exmall_exch_cd',title=>'환종');
//$colums[] = array(value=>'exmall_so_fg',title=>'거래구분');
//$colums[] = array(value=>'exmall_vat_fg',title=>'과세구분');
//$colums[] = array(value=>'exmall_umat_fg',title=>'단가구분');
//$colums[] = array(value=>'exmall_mgmt_cd',title=>'관리구분코드');
//$colums[] = array(value=>'exmall_due_dt',title=>'납기일');
//$colums[] = array(value=>'exmall_shipreq_dt',title=>'출하예정일');
//사방넷 연동 추가
//$colums[] = array(value=>'sbmall_od_cd',title=>'사방넷주문번호');
//$colums[] = array(value=>'sbmall_pr_cd',title=>'사방넷상품코드');
//$colums[] = array(value=>'sbmall_be_dt',title=>'배송희망일(공백)');

?>