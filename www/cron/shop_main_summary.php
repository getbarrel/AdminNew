<?php
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/include/commerce.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/include/util.php");

$slave_db = new Database;
$slave_db->slave_db_setting();


$vdate = date("Ymd", time());
$today = date("Ymd", time());
$firstday = date("Y-m-d", time()-84600*date("w"));
$lastday = date("Y-m-d", time()+84600*(6-date("w")));

//--------------------------------------------------------------------------------------------------------------------------------------------------//

$addWhere .=" and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."','".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') ";

$sql = "Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
1 as vieworder
from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate between '".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))." 00:00:00' and '".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))." 23:59:59' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
union
Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
2 as vieworder
from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate between '".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))." 00:00:00' and '".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))." 23:59:59'  AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
union
Select '".date("m/d")." 오늘 ',
IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
3 as vieworder
from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate between '".date("Y-m-d")." 00:00:00' and '".date("Y-m-d")." 23:59:59' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
union
Select '최근1주',
IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
4 as vieworder
from ".TBL_SHOP_ORDER_DETAIL." od
where od.regdate between '".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))." 00:00:00' and '".date("Y-m-d")." 23:59:59'
AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
union
Select '금주',
IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
5 as vieworder
from ".TBL_SHOP_ORDER_DETAIL." od where 
od.regdate between '".$firstday." 00:00:00' and '".$lastday." 23:59:59'
AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
order by vieworder asc 
";

$slave_db->query($sql);
$main_data['PrintOrderHistory']=$slave_db->getrows();

//--------------------------------------------------------------------------------------------------------------------------------------------------//

$groupbytype="day";
//해당 프로세스에 있는 "day"를 $groupbytype="today" 로 바꿔 라이브에 적용하였으나 추후 생길 문제를 위해 남겨놓음 16 03 30
//union 2, 3번째 절에 o.status not in ('SR') 추가(라이브). 개발은 미추가함.

$sql = "select data.vdate, sum(data.order_sum) as order_sum,sum(data.order_amount_sum) as order_amount_sum, sum(order_sale_sum) as order_sale_sum, sum(order_coprice_sum) as order_coprice_sum, 
sum(sale_cnt) as sale_cnt, 
sum(sale_all_cnt) as sale_all_cnt, 
sum(sale_all_sum) as sale_all_sum, sum(coprice_all_sum) as coprice_all_sum, 
sum(cancel_sale_cnt) as cancel_sale_cnt, sum(cancel_sale_sum) as cancel_sale_sum, sum(cancel_coprice_sum) as cancel_coprice_sum, 
sum(return_sale_cnt) as return_sale_cnt, sum(return_sale_sum) as return_sale_sum, sum(return_coprice_sum) as return_coprice_sum, 
sum(whole_delivery_cnt) as whole_delivery_cnt, sum(whole_delivery_sum) as whole_delivery_sum, sum(return_delivery_cnt) as return_delivery_cnt, sum(return_delivery_sum) as return_delivery_sum
from (
	Select ";
	
	if($groupbytype=="dashboard_week"){
		$sql .= "date_format(od.regdate,'%U')";
	}else if($groupbytype=="dashboard_month"){
		$sql .= "date_format(od.regdate,'%Y%m')" ;
	}else{
		$sql .= "date_format(od.regdate,'%Y%m%d')" ;
	}

	$sql .= " as vdate ,
	'0' as order_sum,
	sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else '0' end) as order_amount_sum,
	sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (od.pt_dcprice)  else '0' end) as order_sale_sum,
	sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as order_coprice_sum,
	0 as sale_cnt,
	sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else '0' end) as sale_all_cnt,
	sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as sale_all_sum,
	sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as coprice_all_sum,

	sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else '0' end) as cancel_sale_cnt,
	sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as cancel_sale_sum,
	sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as cancel_coprice_sum,

	sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else '0' end) as return_sale_cnt,
	sum(case when od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as return_sale_sum,
	sum(case when od.status IN ('".implode("','",$return_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as return_coprice_sum, 
	'0' as whole_delivery_cnt,
	'0' as whole_delivery_sum,
	'0' as return_delivery_cnt,
	'0' as return_delivery_sum
	from  shop_order_detail od";
	if($groupbytype=="today"){
		$sql .= " where od.regdate between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
	}else if($groupbytype=="dashboard_today"){
		$sql .= " where od.regdate between '".datestrReturn($vyesterday)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
	}else if($groupbytype=="dashboard_week"){
		$sql .= " where od.regdate between '".datestrReturn($voneweekago)." 00:00:00' and '".datestrReturn($vweekenddate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
	}else if($groupbytype=="dashboard_month"){
		$sql .= " where od.regdate between '".date("Y-m-01",strtotime("-1 month"))." 00:00:00' and '".substr(datestrReturn($vdate),0,7)."-".date("t")." 23:59:59'
		AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
	}else{
		$sql .= " where od.regdate between '".substr(datestrReturn($vdate),0,7)."-01 00:00:00' and '".substr(datestrReturn($vdate),0,7)."-".date("t")." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
	}
	if($groupbytype=="dashboard_week"){
		$sql .= " group by date_format(od.regdate,'%U')";
	}else if($groupbytype=="dashboard_month"){
		$sql .= " group by date_format(od.regdate,'%Y%m')";
	}else{
		$sql .= " group by date_format(od.regdate,'%Y%m%d')";
	}
	$sql .= "
	union 
	select ";
	
	if($groupbytype=="dashboard_week"){
		$sql .= "date_format(o.order_date,'%U')";
	}else if($groupbytype=="dashboard_month"){
		$sql .= "date_format(o.order_date,'%Y%m')" ;
	}else{
		$sql .= "date_format(o.order_date,'%Y%m%d')" ;
	}

	$sql .= " as vdate , 
	'0' as order_sum,
	'0' as order_amount_sum,
	'0' as order_sale_sum,
	'0' as order_coprice_sum,
	0 as sale_cnt,
	'0' as sale_all_cnt,
	'0' as sale_all_sum,
	'0' as coprice_all_sum,

	'0' as cancel_sale_cnt,
	'0' as cancel_sale_sum,
	'0' as cancel_coprice_sum,

	'0' as return_sale_cnt,
	'0' as return_sale_sum,
	'0' as return_coprice_sum, 
	sum(case when oph.price_div = 'D' and payment_status = 'G'  then 1 else '0' end) as whole_delivery_cnt,
	sum(case when oph.price_div = 'D' and payment_status = 'G'  then oph.expect_price else '0' end) as whole_delivery_sum,
	sum(case when oph.price_div = 'D' and payment_status = 'F'  then 1 else '0' end) as return_delivery_cnt,
	sum(case when oph.price_div = 'D' and payment_status = 'F'  then oph.expect_price else '0' end) as return_delivery_sum
	from 
	shop_order o, shop_order_price_history oph";
	if($groupbytype=="today"){
		$sql .= " where o.oid = oph.oid and o.order_date between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_today"){
		$sql .= " where o.oid = oph.oid and o.order_date between '".datestrReturn($vyesterday)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_week"){
		$sql .= " where o.oid = oph.oid and o.order_date between '".datestrReturn($voneweekago)." 00:00:00' and '".datestrReturn($vweekenddate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_month"){
		$sql .= " where o.oid = oph.oid and o.order_date between '".date("Y-m-01",strtotime("-1 month"))." 00:00:00' and '".substr(datestrReturn($vdate),0,7)."-".date("t")." 23:59:59' ";
	}else{
		$sql .= " where o.oid = oph.oid and static_date LIKE '".substr($vdate,0,6)."%' ";
	}
if($groupbytype=="day" || $groupbytype=="dashboard_today"){
	$sql .= "group by static_date ";
}else if($groupbytype=="dashboard_week"){
	$sql .= " group by date_format(o.order_date,'%U')";
}else if($groupbytype=="dashboard_month"){
	$sql .= " group by date_format(o.order_date,'%Y%m')";
}
	$sql .= "
	union 
	select 
	";
	
	if($groupbytype=="dashboard_week"){
		$sql .= "date_format(o.order_date,'%U')";
	}else if($groupbytype=="dashboard_month"){
		$sql .= "date_format(o.order_date,'%Y%m')";
	}else{
		$sql .= "static_date";
	}

	$sql .= " as vdate ,  
	count(*) as order_sum,
	'0' as order_amount_sum,
	'0' as order_sale_sum,
	'0' as order_coprice_sum,
	sum(case when o.status not in ('".implode("','",$all_sale_status)."')  then 1 else 0 end) as sale_cnt,
	'0' as sale_all_cnt,
	'0' as sale_all_sum,
	'0' as coprice_all_sum,

	'0' as cancel_sale_cnt,
	'0' as cancel_sale_sum,
	'0' as cancel_coprice_sum,

	'0' as return_sale_cnt,
	'0' as return_sale_sum,
	'0' as return_coprice_sum, 
	0 as whole_delivery_cnt,
	0 as whole_delivery_sum,
	0 as return_delivery_cnt,
	0 as return_delivery_sum
	from 
	shop_order o ";


	if($groupbytype=="today"){
		$sql .= " where o.order_date between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_today"){
		$sql .= " where o.order_date between '".datestrReturn($vyesterday)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_week"){
		$sql .= " where o.order_date between '".datestrReturn($voneweekago)." 00:00:00' and '".datestrReturn($vweekenddate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_month"){
		$sql .= " where o.order_date between '".date("Y-m-01",strtotime("-1 month"))." 00:00:00' and '".substr(datestrReturn($vdate),0,7)."-".date("t")." 23:59:59' ";
	}else{
		$sql .= " where static_date LIKE '".substr($vdate,0,6)."%' ";
	}
if($groupbytype=="day" || $groupbytype=="dashboard_today"){
	$sql .= "group by static_date ";
}else if($groupbytype=="dashboard_week"){
	$sql .= " group by date_format(o.order_date,'%U')";
}else if($groupbytype=="dashboard_month"){
	$sql .= " group by date_format(o.order_date,'%Y%m')";
}
	$sql .= "
) data
group by vdate ";

$slave_db->query($sql);
$main_data['salesByDateReportTable']['today']=$slave_db->fetchall("object");

//--------------------------------------------------------------------------------------------------------------------------------------------------//

$groupbytype="month";

$sql = "select data.vdate, sum(data.order_sum) as order_sum,sum(data.order_amount_sum) as order_amount_sum, sum(order_sale_sum) as order_sale_sum, sum(order_coprice_sum) as order_coprice_sum, 
sum(sale_cnt) as sale_cnt, 
sum(sale_all_cnt) as sale_all_cnt, 
sum(sale_all_sum) as sale_all_sum, sum(coprice_all_sum) as coprice_all_sum, 
sum(cancel_sale_cnt) as cancel_sale_cnt, sum(cancel_sale_sum) as cancel_sale_sum, sum(cancel_coprice_sum) as cancel_coprice_sum, 
sum(return_sale_cnt) as return_sale_cnt, sum(return_sale_sum) as return_sale_sum, sum(return_coprice_sum) as return_coprice_sum, 
sum(whole_delivery_cnt) as whole_delivery_cnt, sum(whole_delivery_sum) as whole_delivery_sum, sum(return_delivery_cnt) as return_delivery_cnt, sum(return_delivery_sum) as return_delivery_sum
from (
	Select ";
	
	if($groupbytype=="dashboard_week"){
		$sql .= "date_format(od.regdate,'%U')";
	}else if($groupbytype=="dashboard_month"){
		$sql .= "date_format(od.regdate,'%Y%m')" ;
	}else{
		$sql .= "date_format(od.regdate,'%Y%m%d')" ;
	}

	$sql .= " as vdate ,
	'0' as order_sum,
	sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else '0' end) as order_amount_sum,
	sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (od.pt_dcprice)  else '0' end) as order_sale_sum,
	sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as order_coprice_sum,
	0 as sale_cnt,
	sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else '0' end) as sale_all_cnt,
	sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as sale_all_sum,
	sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as coprice_all_sum,

	sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else '0' end) as cancel_sale_cnt,
	sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as cancel_sale_sum,
	sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as cancel_coprice_sum,

	sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else '0' end) as return_sale_cnt,
	sum(case when od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as return_sale_sum,
	sum(case when od.status IN ('".implode("','",$return_status)."')  then (case when od.commission > 0 then od.pt_dcprice*(100-od.commission)/100 else od.coprice*od.pcnt end) else '0' end) as return_coprice_sum, 
	'0' as whole_delivery_cnt,
	'0' as whole_delivery_sum,
	'0' as return_delivery_cnt,
	'0' as return_delivery_sum
	from  shop_order_detail od";
	if($groupbytype=="today"){
		$sql .= " where od.regdate between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
	}else if($groupbytype=="dashboard_today"){
		$sql .= " where od.regdate between '".datestrReturn($vyesterday)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
	}else if($groupbytype=="dashboard_week"){
		$sql .= " where od.regdate between '".datestrReturn($voneweekago)." 00:00:00' and '".datestrReturn($vweekenddate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
	}else if($groupbytype=="dashboard_month"){
		$sql .= " where od.regdate between '".date("Y-m-01",strtotime("-1 month"))." 00:00:00' and '".substr(datestrReturn($vdate),0,7)."-".date("t")." 23:59:59'
		AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
	}else{
		$sql .= " where od.regdate between '".substr(datestrReturn($vdate),0,7)."-01 00:00:00' and '".substr(datestrReturn($vdate),0,7)."-".date("t")." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
	}
	if($groupbytype=="dashboard_week"){
		$sql .= " group by date_format(od.regdate,'%U')";
	}else if($groupbytype=="dashboard_month"){
		$sql .= " group by date_format(od.regdate,'%Y%m')";
	}else{
		$sql .= " group by date_format(od.regdate,'%Y%m%d')";
	}
	$sql .= "
	union 
	select ";
	
	if($groupbytype=="dashboard_week"){
		$sql .= "date_format(o.order_date,'%U')";
	}else if($groupbytype=="dashboard_month"){
		$sql .= "date_format(o.order_date,'%Y%m')" ;
	}else{
		$sql .= "date_format(o.order_date,'%Y%m%d')" ;
	}

	$sql .= " as vdate , 
	'0' as order_sum,
	'0' as order_amount_sum,
	'0' as order_sale_sum,
	'0' as order_coprice_sum,
	0 as sale_cnt,
	'0' as sale_all_cnt,
	'0' as sale_all_sum,
	'0' as coprice_all_sum,

	'0' as cancel_sale_cnt,
	'0' as cancel_sale_sum,
	'0' as cancel_coprice_sum,

	'0' as return_sale_cnt,
	'0' as return_sale_sum,
	'0' as return_coprice_sum, 
	sum(case when oph.price_div = 'D' and payment_status = 'G'  then 1 else '0' end) as whole_delivery_cnt,
	sum(case when oph.price_div = 'D' and payment_status = 'G'  then oph.expect_price else '0' end) as whole_delivery_sum,
	sum(case when oph.price_div = 'D' and payment_status = 'F'  then 1 else '0' end) as return_delivery_cnt,
	sum(case when oph.price_div = 'D' and payment_status = 'F'  then oph.expect_price else '0' end) as return_delivery_sum
	from 
	shop_order o, shop_order_price_history oph";
	if($groupbytype=="today"){
		$sql .= " where o.oid = oph.oid and o.order_date between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_today"){
		$sql .= " where o.oid = oph.oid and o.order_date between '".datestrReturn($vyesterday)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_week"){
		$sql .= " where o.oid = oph.oid and o.order_date between '".datestrReturn($voneweekago)." 00:00:00' and '".datestrReturn($vweekenddate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_month"){
		$sql .= " where o.oid = oph.oid and o.order_date between '".date("Y-m-01",strtotime("-1 month"))." 00:00:00' and '".substr(datestrReturn($vdate),0,7)."-".date("t")." 23:59:59' ";
	}else{
		$sql .= " where o.oid = oph.oid and static_date LIKE '".substr($vdate,0,6)."%' ";
	}
if($groupbytype=="day" || $groupbytype=="dashboard_today"){
	$sql .= "group by static_date ";
}else if($groupbytype=="dashboard_week"){
	$sql .= " group by date_format(o.order_date,'%U')";
}else if($groupbytype=="dashboard_month"){
	$sql .= " group by date_format(o.order_date,'%Y%m')";
}
	$sql .= "
	union 
	select 
	";
	
	if($groupbytype=="dashboard_week"){
		$sql .= "date_format(o.order_date,'%U')";
	}else if($groupbytype=="dashboard_month"){
		$sql .= "date_format(o.order_date,'%Y%m')";
	}else{
		$sql .= "static_date";
	}

	$sql .= " as vdate ,  
	count(*) as order_sum,
	'0' as order_amount_sum,
	'0' as order_sale_sum,
	'0' as order_coprice_sum,
	sum(case when o.status not in ('".implode("','",$all_sale_status)."')  then 1 else 0 end) as sale_cnt,
	'0' as sale_all_cnt,
	'0' as sale_all_sum,
	'0' as coprice_all_sum,

	'0' as cancel_sale_cnt,
	'0' as cancel_sale_sum,
	'0' as cancel_coprice_sum,

	'0' as return_sale_cnt,
	'0' as return_sale_sum,
	'0' as return_coprice_sum, 
	0 as whole_delivery_cnt,
	0 as whole_delivery_sum,
	0 as return_delivery_cnt,
	0 as return_delivery_sum
	from 
	shop_order o ";
	if($groupbytype=="today"){
		$sql .= " where o.order_date between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_today"){
		$sql .= " where o.order_date between '".datestrReturn($vyesterday)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_week"){
		$sql .= " where o.order_date between '".datestrReturn($voneweekago)." 00:00:00' and '".datestrReturn($vweekenddate)." 23:59:59' ";
	}else if($groupbytype=="dashboard_month"){
		$sql .= " where o.order_date between '".date("Y-m-01",strtotime("-1 month"))." 00:00:00' and '".substr(datestrReturn($vdate),0,7)."-".date("t")." 23:59:59' ";
	}else{
		$sql .= " where static_date LIKE '".substr($vdate,0,6)."%' ";
	}
if($groupbytype=="day" || $groupbytype=="dashboard_today"){
	$sql .= "group by static_date ";
}else if($groupbytype=="dashboard_week"){
	$sql .= " group by date_format(o.order_date,'%U')";
}else if($groupbytype=="dashboard_month"){
	$sql .= " group by date_format(o.order_date,'%Y%m')";
}
	$sql .= "
) data
group by vdate ";

$slave_db->query($sql);
$main_data['salesByDateReportTable']['month']=$slave_db->fetchall("object");

//--------------------------------------------------------------------------------------------------------------------------------------------------//

$groupbytype="today";

$sql = "Select date_format(od.regdate,'%Y%m%d') as vdate ,
sum(case when order_from = 'self' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as self_sale_all_sum,
sum(case when order_from = 'self' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as self_cancel_sale_sum,
sum(case when order_from = 'self' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as self_return_sale_sum,

sum(case when order_from = 'offline' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as offline_sale_all_sum,
sum(case when order_from = 'offline' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as offline_cancel_sale_sum,
sum(case when order_from = 'offline' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as offline_return_sale_sum,

sum(case when order_from = 'pos' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as pos_sale_all_sum,
sum(case when order_from = 'pos' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as pos_cancel_sale_sum,
sum(case when order_from = 'pos' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as pos_return_sale_sum,

sum(case when od.status not in ('".implode("','",$not_real_sale_status)."')  then (od.pt_dcprice)  else '0' end) as sale_sum
from  shop_order_detail od";
if($groupbytype=="today"){
	$sql .= " where od.regdate between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
}else{
	$sql .= " where od.regdate between '".datestrReturn($vdate,"month_f")." 00:00:00' and '".datestrReturn($vdate,"month_l")." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
}

if($groupbytype=="day"){
	$sql .="group by date_format(od.regdate,'%Y%m%d')";
}

$slave_db->query($sql);
$main_data['salesByDateFromReportTable']['today']=$slave_db->fetchall("object");

//--------------------------------------------------------------------------------------------------------------------------------------------------//

$groupbytype="month";

$sql = "Select date_format(od.regdate,'%Y%m%d') as vdate ,
sum(case when order_from = 'self' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as self_sale_all_sum,
sum(case when order_from = 'self' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as self_cancel_sale_sum,
sum(case when order_from = 'self' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as self_return_sale_sum,

sum(case when order_from = 'offline' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as offline_sale_all_sum,
sum(case when order_from = 'offline' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as offline_cancel_sale_sum,
sum(case when order_from = 'offline' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as offline_return_sale_sum,

sum(case when order_from = 'pos' and od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else '0' end) as pos_sale_all_sum,
sum(case when order_from = 'pos' and od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else '0' end) as pos_cancel_sale_sum,
sum(case when order_from = 'pos' and od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else '0' end) as pos_return_sale_sum,

sum(case when od.status not in ('".implode("','",$not_real_sale_status)."')  then (od.pt_dcprice)  else '0' end) as sale_sum
from  shop_order_detail od";
if($groupbytype=="today"){
	$sql .= " where od.regdate between '".datestrReturn($vdate)." 00:00:00' and '".datestrReturn($vdate)." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
}else{
	$sql .= " where od.regdate between '".datestrReturn($vdate,"month_f")." 00:00:00' and '".datestrReturn($vdate,"month_l")." 23:59:59' AND od.status NOT IN ('".implode("','",$non_sale_status)."') ";
}

if($groupbytype=="day"){
	$sql .="group by date_format(od.regdate,'%Y%m%d')";
}

$slave_db->query($sql);
$main_data['salesByDateFromReportTable']['month']=$slave_db->fetchall("object");


//--------------------------------------------------------------------------------------------------------------------------------------------------//

$today = date("Ymd", time());
$Ymd1 = date("Ymd",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-1,substr($today,0,4)));
$Ymd2 = date("Ymd",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-2,substr($today,0,4)));

$today_ = date("Y-m-d", time());
$Ymd1_ = date("Y-m-d",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-1,substr($today,0,4)));
$Ymd2_ = date("Y-m-d",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-2,substr($today,0,4)));


$sql = "select day_str,
		(m_b2c_total + m_b2b_total + m_ect_total) as m_total, 
		concat(m_b2c_total , ' / ' , m_b2b_total , ' / ' , m_ect_total) as m,
		j_total,
		concat(j_b2c_total , ' / ' , j_b2b_total , ' / ' , j_ect_total) as j,
		(select IFNULL(sum(1),0) as d_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' ) as d_total,
		concat((select IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as d_b2c_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' ) , ' / ' , (select IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as d_b2b_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' ) , ' / ' , (select IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as d_ect_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' )) as d,
		1 as vieworder
	from 
	(
		select 
			data.*,
			IFNULL((select count(*) from common_user u where date <= '".$Ymd1_." 23:59:59' and mem_type in ('M') ),0) as m_b2c_total,
			IFNULL((select count(*) from common_user u where date <= '".$Ymd1_." 23:59:59' and mem_type in ('C') ),0) as m_b2b_total,
			IFNULL((select count(*) from common_user u where date <= '".$Ymd1_." 23:59:59' and mem_type not in ('M','C')) ,0) as m_ect_total
		from
		(
				Select '".date("m/d",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-2,substr($today,0,4)))."      ' as day_str ,
				IFNULL(sum(1),0) as m_total,
				
				IFNULL(sum(1),0) as j_total,
				IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as j_b2c_total,
				IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as j_b2b_total,
				IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as j_ect_total
				from ".TBL_COMMON_USER." where date between '".$Ymd1_." 00:00:00' and '".$Ymd1_." 23:59:59'
			) data
		) data2
	union
	select  day_str,
		(m_b2c_total + m_b2b_total + m_ect_total) as m_total, 
		concat(m_b2c_total , ' / ' , m_b2b_total , ' / ' , m_ect_total) as m,
		j_total,
		concat(j_b2c_total , ' / ' , j_b2b_total , ' / ' , j_ect_total) as j,
		(select IFNULL(sum(1),0) as d_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' ) as d_total,
		concat((select IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as d_b2c_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' ) , ' / ' , (select IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as d_b2b_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' ) , ' / ' , (select IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as d_ect_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' )) as d,
		2 as vieworder
	from 
	(
		Select '".date("m/d",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-1,substr($today,0,4)))."      ' as day_str,
		IFNULL(sum(1),0) as m_total,
		IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as m_b2c_total,
		IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as m_b2b_total,
		IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as m_ect_total,

		IFNULL(sum(1),0) as j_total,
		IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as j_b2c_total,
		IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as j_b2b_total,
		IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as j_ect_total
		from ".TBL_COMMON_USER." where date between '".$Ymd2_." 00:00:00' and '".$Ymd2_." 23:59:59'
	) data
	union
	select day_str,
		(m_b2c_total + m_b2b_total + m_ect_total) as m_total, 
		concat(m_b2c_total , ' / ' , m_b2b_total , ' / ' , m_ect_total) as m,
		j_total,
		concat(j_b2c_total , ' / ' , j_b2b_total , ' / ' , j_ect_total) as j,
		(select IFNULL(sum(1),0) as d_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' ) as d_total,
		concat((select IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as d_b2c_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' ) , ' / ' , (select IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as d_b2b_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' ) , ' / ' , (select IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as d_ect_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' )) as d,
		3 as vieworder
	from 
	(
		Select '".date("m/d")." 오늘 ' as day_str,
		IFNULL(sum(1),0) as m_total,
		IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as m_b2c_total,
		IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as m_b2b_total,
		IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as m_ect_total,
		IFNULL(sum(1),0) as j_total,
		IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as j_b2c_total,
		IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as j_b2b_total,
		IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as j_ect_total
		from ".TBL_COMMON_USER." where date between '".$today_." 00:00:00' and '".$today_." 23:59:59' 
	) data
	order by vieworder asc ";

$slave_db->query($sql);
$main_data['PrintMemberTable']=$slave_db->getrows();

//--------------------------------------------------------------------------------------------------------------------------------------------------//

$data = urlencode(serialize($main_data));
$shmop = new Shared("shop_main_summary");

if(empty($_SESSION["layout_config"]["mall_data_root"])){
	$sql = "select mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
	$db->query($sql);
	$db->fetch();			

	$mall_data_root = $db->dt[mall_data_root];
}else{
	$mall_data_root = $_SESSION["layout_config"]["mall_data_root"];
}

$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$mall_data_root."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey($data,"shop_main_summary");


function datestrReturn($date,$type="day"){
	if($type=="month"){
		$return = substr($date,0,4)."-".substr($date,4,2);
	}elseif($type=="month_f"){
		$return = substr($date,0,4)."-".substr($date,4,2)."-01";
	}elseif($type=="month_l"){
		$return = substr($date,0,4)."-".substr($date,4,2)."-".date("t");
	}else{
		$return = substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2);
	}
	return $return;
}

?>