<?php
set_time_limit(99999);
ini_set('memory_limit',-1);

include("../class/layout.class");
include ("../include/phpexcel/Classes/PHPExcel.php");
PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

//include ("Spreadsheet/Excel/Writer.php");
$db = new Database;

$oet_ix = $_GET['oet_ix'];
$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

if($oet_ix == '1'){

	//주문 건별 정산 쿼리

	$sql= "select 
		'타입','주문번호','주문상세번호','상품코드','상품명','옵션명','옵션코드','품목코드','주문수량','공급가','판매가','상품가격(*수량)','쿠폰사용금액','쿠폰본사부담','쿠폰셀러부담',
		'할인금액(쿠폰제외)','할인본사부담금액','할인셀러부담금액','정산방식','수수료','수수료방식','수수료금액','배송비','주문일자','주문자','주문자 ID','수령인명','송장번호','주문상태','입금(환불)일자',
		'배송일자','거래완료일','업체명','결제수단'

		union all

		select '입금' as type, od.oid,od.od_ix,od.pid,od.pname,od.option_text,od.option_id,od.gid,od.pcnt,od.coprice,od.psprice,od.ptprice,
		ifnull((select sum(dc_price) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type in ('CP','SCP')),'0') as coupon_use_price,
		ifnull((select sum(dc_price_admin) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type in ('CP','SCP')),'0') as coupon_admin_price,
		ifnull((select sum(dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type in ('CP','SCP')),'0') as coupon_seller_price,

		ifnull((select sum(dc_price) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type not in ('CP','SCP')),'0') as sale_use_price,
		ifnull((select sum(dc_price_admin) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type not in ('CP','SCP')),'0') as sale_admin_price,
		ifnull((select sum(dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type not in ('CP','SCP')),'0') as sale_seller_price,

		(case when account_type='3' then '미정산' when account_type='2' then '매입(공급가정산)' else '수수료' end) as account_type,
		od.commission, 
		od.commission_msg,

		case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') 
		
		from shop_order_detail_discount dc 
		where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end as commission_price,

		(case when od.od_ix = (select max(odr.od_ix) 
		from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and od.status not in ('SR','ER') and od.ic_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59'  )
		then
			((case when odv.delivery_pay_type='2' then '0' else odv.delivery_price end)) 
		else
			'0'
		end) as delivery_price,
		od.regdate, o.bname, o.buserid, odd.rname, od.invoice_no, od.status, od.ic_date, od.di_date, od.bf_date, od.company_name,

		(select group_concat(op.method SEPARATOR '|') from shop_order_payment op where op.oid=od.oid) as method

		from 
		shop_order o 
		left join shop_order_detail od on (o.oid=od.oid)
		left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix)
		left join shop_order_delivery odv on (odv.oid = od.oid and odv.ode_ix=od.ode_ix)
		where 
		od.status not in ('SR','ER') and od.ic_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' 

		AND 
			od.delivery_policy !='9'

		union all

		select '환불' as type, od.oid,od.od_ix,od.pid,od.pname,od.option_text,od.option_id,od.gid,concat('-',od.pcnt) as pcnt,(od.coprice * -1) as coprice, (od.psprice * -1) as psprice,concat('-',od.ptprice) as ptprice,

		(ifnull((select sum(dc_price) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type in ('CP','SCP')),'0') * -1) as coupon_use_price,
		(ifnull((select sum(dc_price_admin) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type in ('CP','SCP')),'0') * -1) as coupon_admin_price,
		(ifnull((select sum(dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type in ('CP','SCP')),'0') * -1) as coupon_seller_price,

		(ifnull((select sum(dc_price) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type not in ('CP','SCP')),'0') * -1) as sale_use_price,
		(ifnull((select sum(dc_price_admin) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type not in ('CP','SCP')),'0') * -1) as sale_admin_price,
		(ifnull((select sum(dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type not in ('CP','SCP')),'0') * -1) as sale_seller_price,

		(case when account_type='3' then '미정산' when account_type='2' then '매입(공급가정산)' else '수수료' end) as account_type,
		od.commission, od.commission_msg,

		(case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end * -1) as commission_price,

		((case when 
			od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.company_id = od.company_id and odr.claim_group = od.claim_group and od.status not in ('SR','EC') 
			and od.fc_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' )
		then
			(odv.delivery_price) 
		else
			'0'
		end) * -1)  as delivery_price,
		od.regdate, o.bname, o.buserid, odd.rname, od.invoice_no, od.refund_status, od.fc_date, od.di_date, od.bf_date, od.company_name,
		(select group_concat(op.method SEPARATOR '|') from shop_order_payment op where op.oid=od.oid) as method

		from 
		shop_order o 
		left join shop_order_detail od on (o.oid=od.oid)
		left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix)
		left join shop_order_claim_delivery odv on (odv.oid = od.oid and odv.company_id=od.company_id and odv.claim_group=od.claim_group and odv.ac_target_yn='Y')
		where 
		od.status not in ('SR','EC') and od.fc_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59'
		and od.refund_status = 'FC'";

} elseif($oet_ix == '2'){


	// 주문별 정산 쿼리

	$sql = "select '주문번호','결제수단','과세-공급가액','과세-세액','면세-금액','실결제금액','쿠폰금액','장바구니쿠폰금액','증빙', '결제상태' ,'입금,환불일','주문자명','주문자ID','TID' ,'판매처' ,'M:모바일,W:웹결제' , '결제정보' ,'적립금사용금액' ,'결제배송료','환불배송료'

	union all

	select op.oid, GROUP_CONCAT(op.method SEPARATOR '|') as method,

	 sum((case when op.pay_type='F' then -(op.tax_price-ROUND(op.tax_price/1.1)) else (op.tax_price-ROUND(op.tax_price/1.1)) end)) as p_co_price,

	 sum((case when op.pay_type='F' then -ROUND(op.tax_price/1.1) else ROUND(op.tax_price/1.1) end)) as p_tax_price ,

	 sum((case when op.pay_type='F' then -op.tax_free_price else op.tax_free_price end)) as tax_free_price , 

	 sum((case when op.pay_type='F' then -op.payment_price else op.payment_price end)) as payment_price , 

	(select sum(dc_price) as dc_price from shop_order_detail_discount where oid=o.oid and dc_type in ('CP','SCP') ) as coupon_price,

	sum((case when op.method='14' then (case when op.pay_type='F' then -op.payment_price else op.payment_price end) else '0' end)) as cart_coupon_price, 

	(case when taxsheet_yn='Y' then '세금계산서' when receipt_yn='Y' then '현금영수증&증빙' else '미발급' end) as receipt,

	op.pay_status, op.ic_date, o.bname, o.buserid, op.tid,

	(select ssi.site_name from shop_order_detail od left join sellertool_site_info ssi on (od.order_from=ssi.site_code) where od.oid=o.oid limit 0,1) as order_from,

	o.payment_agent_type , op.vb_info,

	(select payment_price from shop_order_payment op2 where op2.oid=o.oid and op2.method='13' and op2.pay_type='G') as use_reserve_price,


	(select sum(ifnull(delivery_dcprice,'0')) from shop_order_delivery odd where odd.oid=o.oid) as delivery_price,

	(select sum(ifnull(delivery_price,'0') * -1) from shop_order_claim_delivery odv where odv.oid=o.oid and odv.ac_target_yn='Y' ) as re_delivery_price


	from shop_order_payment op , shop_order o where op.oid = o.oid and op.ic_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59'

	group by op.oid";

} elseif($oet_ix == '3'){


	// MD 요청

	$sql = "select 
			'담당MD','카테고리시스템코드','카테고리(대분류)','카테고리(중분류)','카테고리(소분류)','주문일자','주문번호','셀러명','정산방식','상품시스템코드',
			'상품명','옵션','수량','정산예정금액','할인부담금액','수수료','실정산금액','실정산배송비','실정산합계','판매처(제휴사명)'

			union all

			select 
				cu.id,od.cid,
				(select ci1.cname from shop_category_info ci1 where ( ci1.cid = concat(left(od.cid,3),'000000000000') and ci1.depth='0' ) limit 0,1) as cname1,
				(select ci2.cname from shop_category_info ci2 where ( ci2.cid = concat(left(od.cid,6),'000000000') and ci2.depth='1' ) limit 0,1) as cname2,
				(select ci3.cname from shop_category_info ci3 where ( ci3.cid = concat(left(od.cid,9),'000000') and ci3.depth='2' ) limit 0,1) as cname3,
				od.regdate,od.oid,od.company_name,(case when od.account_type='2' then '공급가' when od.account_type='3' then '미정산' else '수수료' end) as account_type,
				od.pid,od.pname,od.option_text,od.pcnt,od.product_expect_ac_price,od.product_expect_dc_allotment_price,od.product_expect_fee_price, (od.product_expect_ac_price-od.product_expect_dc_allotment_price-od.product_expect_fee_price) as ac_product_price, od.ac_delivery_price,
				(od.product_expect_ac_price-od.product_expect_dc_allotment_price-od.product_expect_fee_price-od.ac_delivery_price) as ac_price,
				IFNULL(ssi.site_name, od.order_from) as order_from
			from (
				select 
					od.order_from,od.od_ix,od.ode_ix,od.status,od.ic_date,od.cid,od.regdate,od.md_code,od.oid,od.company_name,od.account_type,od.pid,od.pname,od.option_text,od.pcnt,
					(case when od.account_type='3' or od.refund_status='FC' then '0' else (case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end) end) as product_expect_ac_price,
					IFNULL((case when od.account_type='3' or od.refund_status='FC' then '0' else (select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) end),'0') as product_expect_dc_allotment_price,
					IFNULL((case when od.account_type='3' or od.refund_status='FC' then '0' else (case when od.account_type in ('1','') then ((od.ptprice - (select IFNULL(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end) end),'0') as product_expect_fee_price,
					(case when 
						od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and od.status not in ('SR','ER') 
						and od.ic_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' )
					then
						(
							(case when odv.delivery_pay_type='2' then '0' else odv.delivery_price end)
						) 
					else
						'0'
					end) as ac_delivery_price
				from 
					shop_order_detail od 
					left join shop_order_delivery odv on (odv.oid = od.oid and odv.ode_ix=od.ode_ix)
				where 
					od.status not in ('SR','ER') and od.ic_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59'
					AND 
						od.delivery_policy !='9'
			) od left join common_user cu on (cu.code = od.md_code)
			left join sellertool_site_info ssi on (ssi.site_code=od.order_from)


			union all


			select 
				cu.id,od.cid,
				(select ci1.cname from shop_category_info ci1 where ( ci1.cid = concat(left(od.cid,3),'000000000000') and ci1.depth='0' ) limit 0,1) as cname1,
				(select ci2.cname from shop_category_info ci2 where ( ci2.cid = concat(left(od.cid,6),'000000000') and ci2.depth='1' ) limit 0,1) as cname2,
				(select ci3.cname from shop_category_info ci3 where ( ci3.cid = concat(left(od.cid,9),'000000') and ci3.depth='2' ) limit 0,1) as cname3,
				od.regdate,od.oid,od.company_name,(case when od.account_type='2' then '공급가' when od.account_type='3' then '미정산' else '수수료' end) as account_type,
				od.pid,od.pname,od.option_text,od.pcnt,od.product_expect_ac_price,od.product_expect_dc_allotment_price,od.product_expect_fee_price, (od.product_expect_ac_price-od.product_expect_dc_allotment_price-od.product_expect_fee_price) as ac_product_price, od.ac_delivery_price,
				(od.product_expect_ac_price-od.product_expect_dc_allotment_price-od.product_expect_fee_price-od.ac_delivery_price) as ac_price,
				IFNULL(ssi.site_name, od.order_from) as order_from
			from (
				select 
					od.order_from,od.od_ix,od.ode_ix,od.status,od.ic_date,od.cid,od.regdate,od.md_code,od.oid,od.company_name,od.account_type,od.pid,od.pname,od.option_text,(od.pcnt * -1) as pcnt,
					((case when od.account_type='3' or od.refund_status='FC' then '0' else (case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end) end) * -1) as product_expect_ac_price,
					(IFNULL((case when od.account_type='3' or od.refund_status='FC' then '0' else (select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) end),'0') * -1) as product_expect_dc_allotment_price,
					(IFNULL((case when od.account_type='3' or od.refund_status='FC' then '0' else (case when od.account_type in ('1','') then ((od.ptprice - (select IFNULL(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end) end),'0') * -1) as product_expect_fee_price,
					((case when 
						od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.company_id = od.company_id and odr.claim_group = od.claim_group and od.status not in ('SR','EC') and od.fc_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59' )
					then
						(
							odv.delivery_price
						) 
					else
						'0'
					end) * -1) as ac_delivery_price
				from 
					shop_order_detail od 
					left join shop_order_claim_delivery odv on (odv.oid = od.oid and odv.company_id=od.company_id and odv.claim_group=od.claim_group and odv.ac_target_yn='Y')
				where 
					od.status not in ('SR','EC') and od.fc_date between '".$startDate." 00:00:00' and '".$endDate." 23:59:59'
					and od.refund_status = 'FC'
			) od left join common_user cu on (cu.code = od.md_code)
			left join sellertool_site_info ssi on (ssi.site_code=od.order_from)";
}

$db->query($sql);
$orders=$db->fetchall("object");

/*
if($oet_ix == '1'){
	$file_name = $startDate.'~'.$endDate.' 주문건별.xls';
} elseif ($oet_ix == '2') {
	$file_name = $startDate.'~'.$endDate.' 주문별.xls';
} elseif ($oet_ix == '3') {
	$file_name = $startDate.'~'.$endDate.' MD.xls';
}
*/
/*
혹시나 open_base_dir 에러 나시는 분은 
$workbook->setTempDir('/home/tmp'); 
*/
/*
$workbook = new Spreadsheet_Excel_Writer();
$workbook->setVersion(8); 
$worksheet =& $workbook->addWorksheet();
$worksheet->setInputEncoding('utf-8'); 

$workbook->send($file_name); 

foreach($orders as $i => $data){
	if($i != 0){

		if( ! empty($data["주문상태"]))
			$data["주문상태"] = strip_tags(getOrderStatus($data["주문상태"]));

		if( ! empty($data["결제상태"]))
			$data["결제상태"] = strip_tags(getOrderStatus($data["결제상태"]));
		
		if( ! empty($data["결제수단"]))
			$data["결제수단"] = getMethodStatus($data["결제수단"]);
	}
	
	$j=0;
	foreach($data as $val){
		$worksheet->write($i, $j, strip_tags($val));
		$j++;
	}
}

// Let's send the file 
$workbook->close(); 
*/

foreach($orders as $i => $data){
    if($i != 0){

        if( ! empty($data["주문상태"]))
            $data["주문상태"] = strip_tags(getOrderStatus($data["주문상태"]));

        if( ! empty($data["결제상태"]))
            $data["결제상태"] = strip_tags(getOrderStatus($data["결제상태"]));

        if( ! empty($data["결제수단"]))
            $data["결제수단"] = getMethodStatus($data["결제수단"]);
    }
    $orders2[] = $data;
}

$ordersXL = new PHPExcel();

//$sheet = $ordersXL->getActiveSheet();
//$sheet->getDefaultStyle()->getFont()->setName('맑은 고딕')->setSize(10);

$ordersXL->getActiveSheet()->fromArray($orders2, NULL, 'A1');
$ordersXL->getActiveSheet()->getStyle('A1:AH1')->getFont()->setBold(true);

unset($orders);

	header("Content-Type: text/html; charset=utf-8");
	header("Content-Encoding: utf-8");
	header('Cache-Control: max-age=0');
	header('Content-Type: application/vnd.ms-excel');

if($oet_ix == '1'){
	header('Content-Disposition: attachment;filename="'.$startDate.'~'.$endDate.' '.iconv("UTF-8","CP949","주문건별").'.csv"');
} elseif ($oet_ix == '2') {
	header('Content-Disposition: attachment;filename="'.$startDate.'~'.$endDate.' '.iconv("UTF-8","CP949","주문별").'.csv"');
} elseif ($oet_ix == '3') {
	header('Content-Disposition: attachment;filename="'.$startDate.'~'.$endDate.' '.iconv("UTF-8","CP949","MD").'.csv"');
}


//$objWriter = PHPExcel_IOFactory::createWriter($ordersXL, 'Excel5');
$objWriter = PHPExcel_IOFactory::createWriter($ordersXL, 'CSV');
$objWriter->setUseBOM(true);
$objWriter->save('php://output');

?>