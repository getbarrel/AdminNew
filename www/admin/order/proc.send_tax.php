<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");


$db = new Database;
$tdb = new Database;
$edb = new Database;
$eddb = new Database;




$db->query("SELECT com_name, com_number, com_business_status, com_ceo, com_business_category, com_addr1, com_addr2 FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE com_type = 'A' ");
$db->fetch();

if($db->total){
	$buyer = $db->fetch(0);
}
if($ac_ix != ""){
	$sql = "SELECT c.com_number, c.com_business_status, c.com_ceo, c.com_business_category, c.com_addr1,c.com_addr2, c.com_name, a.company_id ,ac_cnt as sell_cnt, sell_total_price as sell_total_ptprice, ac_price,ac_date, a.taxbill_yn
	FROM ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_SHOP_ACCOUNTS." a
	where c.company_id = '$company_id' and ac_ix = '$ac_ix'   ";
	echo $sql;
}else{
	$sql = "SELECT c.com_number, c.com_business_status, c.com_ceo, c.com_business_category, c.com_addr1, com_addr2,c.com_name, p.admin as company_id ,count(od.pcnt) as sell_cnt, sum(od.ptprice) as sell_total_ptprice,sum(od.ptprice*(100-od.commission)/100) as		sell_total_coprice,
	sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, od.regdate as order_com_date,avg(od.commission) as avg_commission
	FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od, ".TBL_SHOP_ORDER." o, ".TBL_COMMON_COMPANY_DETAIL." c
	where p.id = od.pid and o.oid = od.oid and p.admin = '$company_id' and od.status in ('".ORDER_STATUS_DELIVERY_COMPLETE."')
	and date_format(od.dc_date,'%Y%m%d') <= $endDate and c.company_id = '$company_id' group by admin   ";
	//echo $sql;
}
$db->query($sql);
$db->fetch();
if($db->total){
	$seller = $db->fetch(0);
}

if($ac_ix == ""){
	$ac_price = $seller[sell_total_coprice];//$seller[sell_total_ptprice] - ($seller[sell_total_ptprice] * ($seller[avg_commission]/100));
	$minus = 1.1;
	$total_price_f = round($ac_price / $minus);
	$total_price = str_split(round($total_price_f),1);
	$price_count = count($total_price);
	$gap_price_count = 11 - $price_count;
	$tax_price_f = $ac_price-$total_price_f;//round($total_price_f*0.1);
	$tax_price = str_split(round($tax_price_f),1);
	$tax_count = count($tax_price);
}else{
	$ac_price = $seller[ac_price];
	//echo $ac_price;
	$minus = 1.1;
	$total_price_f = round($ac_price / $minus);
	$total_price = str_split($total_price_f,1);
	$price_count = count($total_price);
	$gap_price_count = 11 - $price_count;
	$tax_price_f = $ac_price-$total_price_f;//round($total_price_f*0.1);
	$tax_price = str_split(round($tax_price_f),1);
	$tax_count = count($tax_price);
}

/*
t_mon = '$t_mon[$z]',
						t_day = '$t_day[$z]',
						product = '$product[$z]',
						p_size = '$p_size[$z]',
						cnt = '$cnt[$z]',
						price = '$price[$z]',
						p_price = '$p_price[$z]',
						tax = '$tax[$z]',
*/
?>

<form name="tax">
<input type="hidden" name="publish_type" value="2"><!-- 1.매출 2.매입 3.위수탁 -->
<input type="hidden" name="tax_type" value="1"><!-- 1.세금계산서 2.계산서 -->
<input type="hidden" name="numbering_k" value=""><!-- 책번호 권 -->
<input type="hidden" name="numbering_h" value=""><!-- 책번호 호 -->
<input type="hidden" name="numbering" value=""><!-- 일련번호 -->
<input type="hidden" name="s_company_number" value="<?=$seller[com_number]?>"><!-- 공급자 등록번호 -->
<input type="hidden" name="s_company_j" value=""><!-- 공급자 종사업장 -->
<input type="hidden" name="s_company_name" value="<?=$seller[com_name]?>"><!-- 공급자 상호 -->
<input type="hidden" name="s_name" value="<?=$seller[com_ceo]?>"><!-- 공급자 성명 -->
<input type="hidden" name="s_address" value="<?=$seller[com_addr1]." ".$seller[com_addr2]?>"><!-- 공급자 사업장 주소 -->
<input type="hidden" name="s_state" value="<?=$seller[com_business_status]?>"><!-- 공급자업태 -->
<input type="hidden" name="s_items" value="<?=$seller[com_business_category]?>"><!-- 공급자종목 -->
<input type="hidden" name="s_personin" value=""><!-- 공급자 담당자 -->
<input type="hidden" name="s_tel" value=""><!-- 공급자 연락처 -->
<input type="hidden" name="s_email" value=""><!-- 공급자 이메일 -->
<input type="hidden" name="r_company_number" value="<?=$buyer[com_name]?>"><!-- 받는자 등록번호 -->
<input type="hidden" name="r_company_j" value=""><!-- 받는자 종사업장 -->
<input type="hidden" name="r_company_name" value="<?=$buyer[com_name]?>"><!-- 받는자 상호 -->
<input type="hidden" name="r_name" value="<?=$buyer[com_ceo]?>"><!-- 받는자 성명 -->
<input type="hidden" name="r_address" value="<?=$buyer[com_addr1]." ".$buyer[com_addr2]?>"><!-- 받는자 사업장주소 -->
<input type="hidden" name="r_state" value="<?=$buyer[com_business_status]?>"><!-- 받는자 업태 -->
<input type="hidden" name="r_items" value="<?=$buyer[com_business_category]?>"><!-- 받는자 종목 -->
<input type="hidden" name="r_personin" value=""><!-- 받는자 담당자 -->
<input type="hidden" name="r_tel" value=""><!-- 받는자 연락처 -->
<input type="hidden" name="r_email" value=""><!-- 받는자 이메일 -->
<input type="hidden" name="company_j" value=""><!-- 수탁자 종사업장  -->
<input type="hidden" name="tax_per" value="1"><!-- 과세형태 -->
<input type="hidden" name="marking" value=""><!-- 비고 -->
<input type="hidden" name="supply_price" value="<?=$total_price_f?>"><!-- 공급가액 -->
<input type="hidden" name="tax_price" value="<?=$tax_price_f?>"><!-- 세액 -->
<input type="hidden" name="total_price" value="<?=$seller[sell_total_ptprice]?>"><!-- 합계금액 -->
<input type="hidden" name="cash" value=""><!-- 현금 -->
<input type="hidden" name="cheque" value=""><!-- 수표 -->
<input type="hidden" name="pro_note" value=""><!-- 어음 -->
<input type="hidden" name="outstanding" value=""><!-- 외상미수금 -->
<input type="hidden" name="claim_kind" value="1"><!-- 청구/영수구분 -->
<input type="hidden" name="signdate" value=""><!-- 작성일 -->
<input type="hidden" name="send_type" value="1"><!-- 국세청전송방법 1.기본 2.승인후전송 3.즉시전송-->
<input type="hidden" name="status" value="2"><!-- 승인요청 status 변경 (1.발행 2.임시발행 3.발행취소 4.승인요청 5.승인거부 6.승인취소) -->

<input type="hidden" name="signdate" value="<?=date('Y-m-d')?>">


<input type="hidden" name="t_mon[1]" value="<?=substr($seller[ac_date],4,2)?>">
<input type="hidden" name="t_day[1]"  value="<?=substr($seller[ac_date],6,2)?>">
<input type="hidden" name="product[1]" value="<?=substr($seller[ac_date],4,2)?>월 상품판매 정산금액">
<input type="hidden" name="p_size[1]" value="">
<input type="hidden" name="cnt[1]" value="">
<input type="hidden" name="price[1]" value="">
<input type="hidden" name="p_price[1]" value="<?=$total_price_f?>">
<input type="hidden" name="tax[1]" value="<?=$tax_price_f?>">
</form>
<script>
var frm = document.tax;
frm.action = "/admin/tax/sales_write_act.php";
frm.method = "post";
frm.submit();
</script>



