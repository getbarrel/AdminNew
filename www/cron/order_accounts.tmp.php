<?

include("../class/layout.class");
include("../admin/seller_accounts/accounts.lib.php");

$db = new Database;

$sql="select company_id from ".TBL_COMMON_COMPANY_DETAIL." where com_type='A' ";
$db->query($sql);
$db->fetch();
$admin_com_id = $db->dt[company_id];


$_AC_DATE_ = "2014-08-20";
$AC_DATE = "20140831";

if(date("d") < "16"){
	$bool1=true;
}else{
	$bool2=true;
}

$bool1=true;

if($bool1){
	$ac_action=true;
	
	$sql="select
				company_id,ac_term_div
			from
				".TBL_COMMON_SELLER_DELIVERY."
			where
				company_id != '".$admin_com_id."'
			and 
				ac_term_div in ('1','2') ";

	$sql="select
				company_id,ac_term_div
			from
				".TBL_COMMON_SELLER_DELIVERY."
			where
				company_id != '".$admin_com_id."'
				and company_id = 'f5455c9ad13b8cb0fef4650f25e88200'
			and 
				ac_term_div in ('1','2') ";


	$db->query($sql);

	if($db->total){
		$ac_company = $db->fetchall("object");
		foreach($ac_company as $key => $value){
			$company_id_array[$key]=$value[company_id];
			if($value[ac_term_div]=="2"){
				$ac_title[$value[company_id]]=date("m",strtotime('-1 month'))."월 2차";
			}else{
				$ac_title[$value[company_id]]=date("m",strtotime('-1 month'))."월 1차";
			}
		}
	}

}elseif($bool2){
	$ac_action=true;
	
	$sql="select
				company_id,ac_term_div
			from
				".TBL_COMMON_SELLER_DELIVERY."
			where
				company_id != '".$admin_com_id."'
			and 
				ac_term_div in ('2') ";

	$sql="select
				company_id,ac_term_div
			from
				".TBL_COMMON_SELLER_DELIVERY."
			where
				company_id != '".$admin_com_id."'
				and company_id = 'f5455c9ad13b8cb0fef4650f25e88200'
			and 
				ac_term_div in ('2') ";
	$db->query($sql);
	
	if($db->total){
		$ac_company = $db->fetchall("object");
		foreach($ac_company as $key => $value){
			$company_id_array[$key]=$value[company_id];
			$ac_title[$value[company_id]]=date("m")."월 1차";
		}
	}

}else{
	$ac_action=false;
}


if($ac_action){

	$tmp_where="
	where od.product_type NOT IN (".implode(',',$sns_product_type).") 
	and od.account_info = '1'
	and od.account_type !='3' 
	and DATE_FORMAT(DATE_ADD(case od.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date else od.dc_date end,INTERVAL od.ac_expect_date DAY),'%Y%m%d') < '".$AC_DATE."' ";

	$where = $tmp_where."
	and od.company_id != '".$admin_com_id."'
	and od.company_id in ('".implode("','",$company_id_array)."') ";

	$sub_where="
	and odr.product_type NOT IN (".implode(',',$sns_product_type).") 
	and odr.company_id != '".$admin_com_id."'
	and odr.account_info = '1'
	and odr.account_type !='3'
	and  DATE_FORMAT(DATE_ADD(case odr.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then odr.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then odr.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then odr.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then odr.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then odr.bf_date else odr.dc_date end,INTERVAL odr.ac_expect_date DAY),'%Y%m%d') < '".$AC_DATE."'";

	$sql = "select
			company_id, account_type, surtax_yorn, account_info, account_method,
			sum(case when refund_bool='Y' then ifnull(-p_sell_price,'0') else ifnull(p_sell_price,'0') end) as p_sell_price,
			sum(case when refund_bool='Y' then ifnull(-p_expect_price,'0') else ifnull(p_expect_price,'0') end) as p_expect_price,
			sum(case when refund_bool='Y' then ifnull(-p_dc_allotment_price,'0') else ifnull(p_dc_allotment_price,'0') end) as p_dc_allotment_price,
			sum(case when refund_bool='Y' then ifnull(-p_fee_price,'0') else ifnull(p_fee_price,'0') end) as p_fee_price,

			sum(case when refund_bool='Y' then ifnull(-d_expect_price,'0') else ifnull(d_expect_price,'0') end) as d_expect_price,
			sum(case when refund_bool='Y' then ifnull(-d_dc_allotment_price,'0') else ifnull(d_dc_allotment_price,'0') end) as d_dc_allotment_price
		from
		(
			select
				od.company_id, od.account_type, od.surtax_yorn, od.account_info, od.account_method,

				'N' as refund_bool,

				od.ptprice as p_sell_price,

				case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end as p_expect_price,
				
				(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) as p_dc_allotment_price,

				case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end as p_fee_price,

				(
					case when 
						od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." ".$sub_where.")
					then
						(
							odv.delivery_price
						) 
					else
						'0' 
					end

				) as d_expect_price,
				
				(
					case when 
						od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." ".$sub_where.")
					then
						(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.ode_ix=odv.ode_ix and dc.dc_type in ('DCP','DE')) 
					else
						'0' 
					end

				) as d_dc_allotment_price
		
			from
				".TBL_SHOP_ORDER_DETAIL." od
			left join
				".TBL_SHOP_ORDER." o on o.oid = od.oid
			left join shop_order_delivery odv on (
				odv.oid=od.oid
				and odv.ode_ix = od.ode_ix
				and odv.delivery_type != '1'
				and odv.delivery_pay_type = '1'
				and odv.ac_ix = '0'
			)
			".$where."
			and
			(
				
				".$AC_NORMARL_QUERY."
				
			)
			
			UNION ALL

			select
				od.company_id, od.account_type, od.surtax_yorn, od.account_info, od.account_method,

				'Y' as refund_bool,

				od.ptprice as p_sell_price,

				case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end as p_expect_price,
				
				(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) as p_dc_allotment_price,

				case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end as p_fee_price,

				(
					case when 
						od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.claim_group = od.claim_group and ".str_replace("od.","odr.",$AC_REFUND_QUERY)." ".$sub_where.")
					then
						(
							ocd.delivery_price
						) 
					else
						'0' 
					end

				) as d_expect_price,
				
				'0' as d_dc_allotment_price
		
			from
				".TBL_SHOP_ORDER_DETAIL." od
			left join
				".TBL_SHOP_ORDER." o on o.oid = od.oid
			left join
					shop_order_claim_delivery ocd on (
				ocd.oid=od.oid
				and ocd.company_id=od.company_id
				and ocd.claim_group=od.claim_group
				and ocd.ac_ix='0' 
				and ocd.ac_target_yn='Y' 
				and ocd.delivery_type != '1'
			)
			".$where."
			and
			(
				".$AC_REFUND_QUERY."
				
			)
			and
				DATE_FORMAT(od.fc_date,'%Y%m%d') < '".$AC_DATE."'

		) o
		group by o.company_id, o.account_type, o.surtax_yorn ";

	//echo $sql;
	//exit;

	$db->query($sql);
	if($db->total){

		$ac_list = $db->fetchall("object");
		foreach($ac_list as $list){

			$ac_info=array();
			$ac_info[company_id]=$list[company_id];
			$ac_info[ac_date]=$_AC_DATE_;
			$ac_info[ac_title]=$ac_title[$list[company_id]];
			$ac_info[ac_info]=$list[account_info];//정산 설정1 : 기간별 2:상품별
			$ac_info[ac_type]=$list[account_type];//정산방식 1:수수료 2:매입(공급가로 정산) 3:미정산
			$ac_info[account_method]=$list[account_method];//정산지급방식
			$ac_info[surtax_yorn]=$list[surtax_yorn];
			//실매출액 = 상품 판매가 - 할인부담율
			$ac_info[p_sell_price]=($list[p_sell_price] - $list[p_dc_allotment_price]);
			$ac_info[p_expect_price]=$list[p_expect_price];
			$ac_info[p_fee_price]=$list[p_fee_price];
			$ac_info[p_dc_allotment_price]=$list[p_dc_allotment_price];
			$ac_info[p_ac_price]=($ac_info[p_expect_price] - $ac_info[p_dc_allotment_price]) - $ac_info[p_fee_price];
			$ac_info[d_sell_price]=$list[d_expect_price];
			$ac_info[d_expect_price]=$list[d_expect_price];
			$ac_info[d_dc_allotment_price]=$list[d_dc_allotment_price];
			$ac_info[d_ac_price]=$ac_info[d_expect_price] - $ac_info[d_dc_allotment_price];
			$ac_info[ac_price]=$ac_info[p_ac_price]+$ac_info[d_ac_price];
			$ac_info[status]=ORDER_STATUS_ACCOUNT_READY;

			$ac_ix = shop_accounts_insert($ac_info);
			
			//정상 주문 업데이트
			$sql="update ".TBL_SHOP_ORDER_DETAIL." od set ac_ix = '".$ac_ix."' , accounts_status = '".ORDER_STATUS_ACCOUNT_READY."',  ac_date = '".$AC_DATE."'
				".$tmp_where."
				and
					od.company_id = '".$list[company_id]."'
				and
					od.account_type = '".$list[account_type]."'
				and
					od.surtax_yorn = '".$list[surtax_yorn]."'
				and
					".$AC_NORMARL_QUERY." ";
			$db->query($sql);

			//반품 주문 업데이트
			$sql="update ".TBL_SHOP_ORDER_DETAIL." od set refund_ac_ix = '".$ac_ix."' , ac_date = '".$AC_DATE."'
				".$tmp_where."
				and
					od.company_id = '".$list[company_id]."'
				and
					od.account_type = '".$list[account_type]."'
				and
					od.surtax_yorn = '".$list[surtax_yorn]."'
				and
					".$AC_REFUND_QUERY."
				and
					DATE_FORMAT(od.fc_date,'%Y%m%d') < '".$AC_DATE."'
				";
			$db->query($sql);

			//배송비 업데이트
			$sql="UPDATE 
					shop_order_delivery odr 
				INNER JOIN
					".TBL_SHOP_ORDER_DETAIL." od
				ON 
					(odr.oid=od.oid and odr.ode_ix=od.ode_ix and odr.delivery_type != '1' and odr.delivery_pay_type = '1' and odr.ac_ix = '0')
				SET 
					odr.ac_ix = '".$ac_ix."'
				WHERE 
					od.ac_ix='".$ac_ix."'
				 ";

			$db->query($sql);

			//클래임배송비 업데이트
			$sql="UPDATE 
					shop_order_claim_delivery ocd 
				INNER JOIN
					".TBL_SHOP_ORDER_DETAIL." od
				ON 
					(ocd.oid=od.oid and ocd.company_id=od.company_id and ocd.claim_group=od.claim_group and ocd.ac_ix='0' and ocd.ac_target_yn='Y' and ocd.delivery_type != '1')
				SET 
					ocd.ac_ix = '".$ac_ix."'
				WHERE 
					od.refund_ac_ix='".$ac_ix."' 
				";

			$db->query($sql);
		}
	}
}


/*

----------------------------------------------------------------------------------
-- 주문 건별 정산 쿼리

'할인가(단가-쿠폰할인 제외)',
od.dcprice,


select '타입','주문번호','주문상세번호','상품코드','상품명','옵션명','옵션코드','품목코드','주문수량','공급가','판매가','상품가격(*수량)','쿠폰사용금액','쿠폰본사부담','쿠폰셀러부담'
,'할인금액(쿠폰제외)','할인본사부담금액','할인셀러부담금액','정산방식','수수료','수수료방식','수수료금액','배송비','주문일자','주문자','주문자 ID','수령인명','송장번호','주문상태','입금(환불)일자'
,'배송일자','거래완료일','업체명','결제방법'

union all

select '입금' as type, od.oid,od.od_ix,od.pid,od.pname,od.option_text,od.option_id,od.gid,od.pcnt,od.coprice,od.psprice,od.ptprice,
ifnull((select sum(dc_price) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type in ('CP','SCP')),'0') as coupon_use_price,
ifnull((select sum(dc_price_admin) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type in ('CP','SCP')),'0') as coupon_admin_price,
ifnull((select sum(dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type in ('CP','SCP')),'0') as coupon_seller_price,

ifnull((select sum(dc_price) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type not in ('CP','SCP')),'0') as sale_use_price,
ifnull((select sum(dc_price_admin) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type not in ('CP','SCP')),'0') as sale_admin_price,
ifnull((select sum(dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc_type not in ('CP','SCP')),'0') as sale_seller_price,

(case when account_type='3' then '미정산' when account_type='2' then '매입(공급가정산)' else '수수료' end) as account_type,
od.commission, od.commission_msg,

case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end as commission_price,

(case when 
	od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and od.status not in ('SR','ER') and date_format(od.ic_date,'%Y%m%d') between '20150701' and '20150731' )
then
	(
		(case when odv.delivery_pay_type='2' then '0' else odv.delivery_price end)
	) 
else
	'0'
end) as delivery_price,
od.regdate, o.bname, o.buserid, odd.rname, od.invoice_no, od.status, od.ic_date, od.di_date, od.bf_date, od.company_name,

(select group_concat(op.method) from shop_order_payment op where op.oid=od.oid) as method


from 
shop_order o 
left join shop_order_detail od on (o.oid=od.oid)
left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix)
left join shop_order_delivery odv on (odv.oid = od.oid and odv.ode_ix=od.ode_ix)
where 
od.status not in ('SR','ER') and date_format(od.ic_date,'%Y%m%d') between '20150701' and '20150731'

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
	od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.company_id = od.company_id and odr.claim_group = od.claim_group and od.status not in ('SR','EC') and date_format(od.fc_date,'%Y%m%d') between '20150701' and '20150731' )
then
	(
		odv.delivery_price
	) 
else
	'0'
end) * -1)  as delivery_price,
od.regdate, o.bname, o.buserid, odd.rname, od.invoice_no, od.refund_status, od.fc_date, od.di_date, od.bf_date, od.company_name,
(select group_concat(op.method) from shop_order_payment op where op.oid=od.oid) as method

from 
shop_order o 
left join shop_order_detail od on (o.oid=od.oid)
left join shop_order_detail_deliveryinfo odd on (odd.odd_ix=od.odd_ix)
left join shop_order_claim_delivery odv on (odv.oid = od.oid and odv.company_id=od.company_id and odv.claim_group=od.claim_group and odv.ac_target_yn='Y')
where 
od.status not in ('SR','EC') and date_format(od.fc_date,'%Y%m%d') between '20150701' and '20150731'
and od.refund_status = 'FC'


----------------------------------------------------------------------------------
-- 주문별 정산 쿼리


select '주문번호','결제수단','과세-공급가액','과세-세액','면세-금액','실결제금액','쿠폰금액','장바구니쿠폰금액','증빙', '결제상태(IR:입금예정,IC:입금완료)' ,'입금,환불일','주문자명','주문자ID','TID' ,'판매처' ,'M:모바일,W:웹결제' , '결제정보' ,'적립금사용금액' ,'결제배송료','환불배송료'

union all

select 

op.oid,

GROUP_CONCAT(case when op.method='0' then '무통장입금' when op.method='1' then '카드' when op.method='10' then '현금' when op.method='12' then '예치금' when op.method='13' then '적립금' when op.method='2' then '소액결제' when op.method='4' then '가상계좌' when op.method='5' then '실시간계좌' when op.method='8' then '무료결제' when op.method='14' then '장바구니쿠폰' else op.method end) as method,

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


from shop_order_payment op , shop_order o where op.oid = o.oid and date_format(op.ic_date,'%Y%m%d') between '20150701' and '20150731'

group by op.oid


결제 코드관련 입니다.

"0"			//무통장
"1"			//카드
"2"			//휴대폰결제		
"4"			//가상계좌
"5"			//실시간계좌이체
"6"			//모바일결제
"8"			//무료결제 추가
"9"			//에스크로
"10"			//현금
"12"			//예치금
"13"			//적립금


주문상태관련 코드


"IR"//입금예정
"IC"//입금확인


//취소
"CA"//취고요청
"IB"//입금전취소완료
"CC"//취소완료
"CD"//취소거부
"CI" //취소처리중

//환불
"FA"//환불요청
"FC"//환불완료


//배송
"DR"//배송준비중
"DD"//배송지연
"DI"//배송중
"DC"//배송완료
"BF"//구매확정 -> 거래완료

//교환
"EA"//교환요청
"EY"//교환거부
"EI"//교환승인
"ER"//교환예정(교환배송상품발송예정상태)
"ED"//교환상품배송중
"ET"//교환회수완료
"EF"//교환보류
"EM"//교환불가
"EC"//교환반품확정
"EN"//교환신청취소
"EG"//교환재배송중

//반품
"RA"//반품요청
"RI"//반품승인
"RD"//반품상품배송중
"RC"//반품확정
"RT"//반품회수완료
"RN" //반품취소
"RF" //반품보류
"RY" //반품거부
"RM"//반품불가


*/


/*


----------------------------------------------------------------------------------
-- MD 요청



select '담당MD','카테고리시스템코드','카테고리(대분류)','카테고리(중분류)','카테고리(소분류)','주문일자','주문번호','셀러명','정산방식','상품시스템코드','상품명','옵션','수량','정산예정금액','할인부담금액','수수료','실정산금액','실정산배송비','실정산합계','판매처(제휴사명)'

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
			od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and od.status not in ('SR','ER') and date_format(od.ic_date,'%Y%m%d') between '20150701' and '20150731' )
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
		od.status not in ('SR','ER') and date_format(od.ic_date,'%Y%m%d') between '20150701' and '20150731'
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
			od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.company_id = od.company_id and odr.claim_group = od.claim_group and od.status not in ('SR','EC') and date_format(od.fc_date,'%Y%m%d') between '20150701' and '20150731' )
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
		od.status not in ('SR','EC') and date_format(od.fc_date,'%Y%m%d') between '20150701' and '20150731'
		and od.refund_status = 'FC'
) od left join common_user cu on (cu.code = od.md_code)
left join sellertool_site_info ssi on (ssi.site_code=od.order_from)

*/
?>