<?
include("../class/layout.class");
//include("./pie.graph.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");



$db = new Database;
$mdb = new Database;
$sms_design = new SMS;

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
		    <td align='left' colspan=6 > ".GetTitleNavigation("매출요약", "매출관리 > 매출요약 ")."</td>
		</tr>
	  <tr height=20><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주문상태별 요약</b></td></tr>
	  <tr>
	  	<td style='padding:5px 5px 0 0'>
	  		".PrintOrderHistory()."
	  	</td>
	  </tr>

	  <tr height=50><td colspan=5 class=small><!--* 해당 통계는 주문상세내용을 기준으로 산정되며 매출액은 주문취소금액 과 입금예정 내역은 제외됩니다.-->
	   ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</td></tr>

	  <tr height=20><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>결제방법별 매출요약</b></td></tr>
	  <tr>
	  	<td style='padding:5px 5px 0 0'>
	  		".PrintOrderMethodSummary()."
	  	</td>
	  </tr>
	  <tr height=50><td colspan=5></td></tr>
	  <tr height=20><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>연령대별 매출요약</b></td></tr>
	  <tr>
	  	<td style='padding:5px 5px 0 0'>
	  		".PrintOrderByAgeSummary()."
	  	</td>
	  </tr>
	  <tr height=50><td colspan=5></td></tr>
	</table>";



$Contents = $Contents01;






$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
$P->Navigation = "매출관리 > 매출요약";
$P->title = "매출요약";
echo $P->PrintLayOut();


function PrintOrderByAgeSummary(){
	global $admininfo,$sns_product_type;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));
	$YYYY = date("Y", time());

	if($admininfo[admin_level] == 9 ){//&& $admininfo[mem_type] != "MD"
		if($odb->dbms_type == "oracle"){
			$sql = "
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 10 and 19 then total_price else 0 end),0) as total_price_10,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 20 and 29 then total_price else 0 end),0) as total_price_20,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 30 and 39 then total_price else 0 end),0) as total_price_30,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 40 and 49 then total_price else 0 end),0) as total_price_40,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 50 and 59 then total_price else 0 end),0) as total_price_50,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid_ = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and	date_format(o.date_ ,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 10 and 19 then total_price else 0 end),0) as total_price_10,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 20 and 29 then total_price else 0 end),0) as total_price_20,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 30 and 39 then total_price else 0 end),0) as total_price_30,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 40 and 49 then total_price else 0 end),0) as total_price_40,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 50 and 59 then total_price else 0 end),0) as total_price_50,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid_ = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(o.date_ ,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '".date("m/d")." 오늘',
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 10 and 19 then total_price else 0 end),0) as total_price_10,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 20 and 29 then total_price else 0 end),0) as total_price_20,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 30 and 39 then total_price else 0 end),0) as total_price_30,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 40 and 49 then total_price else 0 end),0) as total_price_40,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 50 and 59 then total_price else 0 end),0) as total_price_50,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid_ = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(o.date_ ,'%Y%m%d') =  '".date("Ymd")."'
					union
					Select '최근1주',
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 10 and 19 then total_price else 0 end),0) as total_price_10,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 20 and 29 then total_price else 0 end),0) as total_price_20,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 30 and 39 then total_price else 0 end),0) as total_price_30,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 40 and 49 then total_price else 0 end),0) as total_price_40,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 50 and 59 then total_price else 0 end),0) as total_price_50,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid_ = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(o.date_ ,'%Y%m%d') between '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."' and '".date("Ymd")."'
				 	union
					Select '금주',
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 10 and 19 then total_price else 0 end),0) as total_price_10,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 20 and 29 then total_price else 0 end),0) as total_price_20,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 30 and 39 then total_price else 0 end),0) as total_price_30,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 40 and 49 then total_price else 0 end),0) as total_price_40,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 50 and 59 then total_price else 0 end),0) as total_price_50,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid_ = m.code and date_format(o.date_ ,'%Y%m%d') between '".$firstday."' and '".$lastday."'
				 	union
					Select '최근1개월',
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 10 and 19 then total_price else 0 end),0) as total_price_10,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 20 and 29 then total_price else 0 end),0) as total_price_20,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 30 and 39 then total_price else 0 end),0) as total_price_30,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 40 and 49 then total_price else 0 end),0) as total_price_40,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 50 and 59 then total_price else 0 end),0) as total_price_50,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid_ = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(o.date_ ,'%Y%m%d') between '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Ymd")."'
				 	union
					Select '전체',
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 10 and 19 then total_price else 0 end),0) as total_price_10,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 20 and 29 then total_price else 0 end),0) as total_price_20,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 30 and 39 then total_price else 0 end),0) as total_price_30,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 40 and 49 then total_price else 0 end),0) as total_price_40,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 50 and 59 then total_price else 0 end),0) as total_price_50,
					IFNULL(sum(case when to_number(TO_CHAR(sysdate,'YYYY'),'9999') - to_number(substr(replace(m.birthday,'-',''),1,4),'9999') between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid_ = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') ";
		}else{
			$sql = "
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 10 and 19 then total_price else 0 end),0) as 10_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 20 and 29 then total_price else 0 end),0) as 20_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 30 and 39 then total_price else 0 end),0) as 30_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 40 and 49 then total_price else 0 end),0) as 40_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 50 and 59 then total_price else 0 end),0) as 50_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and	date_format(o.date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 10 and 19 then total_price else 0 end),0) as 10_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 20 and 29 then total_price else 0 end),0) as 20_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 30 and 39 then total_price else 0 end),0) as 30_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 40 and 49 then total_price else 0 end),0) as 40_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 50 and 59 then total_price else 0 end),0) as 50_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(o.date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '".date("m/d")." 오늘',
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 10 and 19 then total_price else 0 end),0) as 10_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 20 and 29 then total_price else 0 end),0) as 20_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 30 and 39 then total_price else 0 end),0) as 30_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 40 and 49 then total_price else 0 end),0) as 40_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 50 and 59 then total_price else 0 end),0) as 50_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(o.date,'%Y%m%d') =  '".date("Ymd")."'
					union
					Select '최근1주',
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 10 and 19 then total_price else 0 end),0) as 10_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 20 and 29 then total_price else 0 end),0) as 20_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 30 and 39 then total_price else 0 end),0) as 30_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 40 and 49 then total_price else 0 end),0) as 40_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 50 and 59 then total_price else 0 end),0) as 50_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(o.date,'%Y%m%d') between ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))." and ".date("Ymd")."
				 	union
					Select '금주',
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 10 and 19 then total_price else 0 end),0) as 10_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 20 and 29 then total_price else 0 end),0) as 20_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 30 and 39 then total_price else 0 end),0) as 30_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 40 and 49 then total_price else 0 end),0) as 40_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 50 and 59 then total_price else 0 end),0) as 50_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid = m.code and date_format(o.date,'%Y%m%d') between '".$firstday."' and $lastday
				 	union
					Select '최근1개월',
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 10 and 19 then total_price else 0 end),0) as 10_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 20 and 29 then total_price else 0 end),0) as 20_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 30 and 39 then total_price else 0 end),0) as 30_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 40 and 49 then total_price else 0 end),0) as 40_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 50 and 59 then total_price else 0 end),0) as 50_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(o.date,'%Y%m%d') between ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))." and ".date("Ymd")."
				 	union
					Select '전체',
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 10 and 19 then total_price else 0 end),0) as 10_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 20 and 29 then total_price else 0 end),0) as 20_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 30 and 39 then total_price else 0 end),0) as 30_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 40 and 49 then total_price else 0 end),0) as 40_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 50 and 59 then total_price else 0 end),0) as 50_total_price,
					IFNULL(sum(case when date_format(now(),'%Y')-left(m.birthday,4) between 60 and 200 then total_price else 0 end),0) as etc_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_COMMON_MEMBER_DETAIL." m
				 	where o.uid = m.code and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') ";
		}
		//echo $sql;
		//exit;
		/*
		$sql = "Select
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then total_price else 0 end) as today_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') between '".$firstday."' and $lastday then total_price else 0 end) as thisweek_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_total_price,
					sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_cancel_total_price,
					sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end) as ready_cnt,
					sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end) as order_end_cnt,
					sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end) as thismonth_return_total_cnt
				 	from ".TBL_SHOP_ORDER."  ";
				 	*/
	//echo $sql;
	}else if($admininfo[admin_level] == 8 || $admininfo[mem_type] == "MD"){

		if($admininfo[mem_type] == "MD"){
			$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}else{
			$addWhere = " and od.company_id = '".$admininfo[company_id]."' ";
		}

		$sql = "
					select '기간      ','매출액(원) ', '주문건수','입금예정', '입금확인','배송준비/배송중', '교환 ','주문취소'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
						IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid $addWhere
				 	and od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
						IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid $addWhere
				 	and od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '".date("m/d")." 오늘 ',
						IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid $addWhere
				 	and od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') =  '".date("Ymd")."'
					union
					Select '최근1주',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid $addWhere
				 	and od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))." and ".date("Ymd")." between date_format(date,'%Y%m%d')
				 	union
					Select '금주',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid $addWhere
				 	and od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') between '".$firstday."' and '$lastday'
				 	union
					Select '최근1개월',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid $addWhere
				 	and od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Ymd")."' between date_format(date,'%Y%m%d')
				 	union
					Select '전체',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid $addWhere
				 	and od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')";

	/*
		$sql = "Select
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then od.ptprice else 0 end) as today_total_price,
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') between '".$firstday."' and $lastday then od.ptprice else 0 end) as thisweek_total_price,
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then od.ptprice else 0 end) as thismonth_total_price,
					sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_cancel_total_price,
					sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end) as ready_cnt,
					sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end) as order_end_cnt,
					sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end) as thismonth_return_total_cnt
				 	FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id $addWhere ";
	*/
		//echo $sql;
	}




	$odb->query($sql);

	$datas = $odb->getrows();

	$datas_title[0] = "연령대별";
	$datas_title[1] = "10 대";
	$datas_title[2] = "20 대";
	$datas_title[3] = "30 대";
	$datas_title[4] = "40 대";
	$datas_title[5] = "50 대";
	$datas_title[6] = "기타";

	$mstring = "
	<table border='0' cellspacing='1' cellpadding='5' width='100%'>
      <tr>
        <td bgcolor='#F8F9FA'>
			<table cellpadding=0 cellspacing=1 bgcolor=#c0c0c0 width='100%' class='list_table_box'>
				<col width='*' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
		";
	for($i=0;$i<count($datas)-1;$i++){
		//print_r($datas);
			if($i == 0){
				$mstring .= "<tr bgcolor=#ffffff align=center height=30><td class='s_td'>".$datas_title[$i]."</td><td class='m_td'>".$datas[$i][0]."</td><td class='m_td'>".$datas[$i][1]."</td><td class='m_td'>".$datas[$i][2]."</td><td class='m_td'>".$datas[$i][3]."</td><td class='m_td'>".$datas[$i][4]."</td><td class='m_td'>".$datas[$i][5]."</td><td class='e_td'>".$datas[$i][6]."</td></tr>";
			}else{
				$mstring .= "<tr bgcolor=#ffffff height=25 align=right>
				<td class='list_box_td list_bg_gray blk' align=left style='padding:0px 0 0 10px;'><b> ".$datas_title[$i]."</b></td>
				<td class='list_box_td'>".number_format($datas[$i][0])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][1])."</td>
				<td class='list_box_td point'>".number_format($datas[$i][2])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][3])."</td>
				<td class='list_box_td'>".number_format($datas[$i][4])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][5])."</td>
				<td class='list_box_td'>".number_format($datas[$i][6])."</td>
				</tr>";
			}
	}
	$mstring .= "</table>";
	$mstring .= "</td></tr></table>";

	return $mstring;


}



function PrintOrderMethodSummary(){
	global $admininfo,$sns_product_type;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));


	if($admininfo[admin_level] == 9){
		if($admininfo[mem_type] == "MD"){
			$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}

		if($odb->dbms_type == "oracle"){
			$sql = "
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and	date_format(date_,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date_,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '".date("m/d")." 오늘 ',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date_,'%Y%m%d') =  '".date("Ymd")."'
					union
					Select '최근1주',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date_,'%Y%m%d') between ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))." and ".date("Ymd")."
				 	union
					Select '금주',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER." where date_format(date_,'%Y%m%d') between '".$firstday."' and $lastday
				 	union
					Select '최근1개월',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date_,'%Y%m%d') between ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))." and ".date("Ymd")."
				 	union
					Select '전체',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') ";
		}else{
			$sql = "
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and	date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '".date("m/d")." 오늘 ',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') =  '".date("Ymd")."'
					union
					Select '최근1주',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') between ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))." and ".date("Ymd")."
				 	union
					Select '금주',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER." where date_format(date,'%Y%m%d') between '".$firstday."' and $lastday
				 	union
					Select '최근1개월',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') between ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))." and ".date("Ymd")."
				 	union
					Select '전체',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_PHONE."') then total_price else 0 end),0) as phone_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_VBANK."') then total_price else 0 end),0) as vbank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_ICHE."') then total_price else 0 end),0) as iche_total_price
				 	from ".TBL_SHOP_ORDER."
				 	where status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') ";
		}
		//echo $sql;
		//exit;
		/*
		$sql = "Select
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then total_price else 0 end) as today_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') between '".$firstday."' and $lastday then total_price else 0 end) as thisweek_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_total_price,
					sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_cancel_total_price,
					sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end) as ready_cnt,
					sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end) as order_end_cnt,
					sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end) as thismonth_return_total_cnt
				 	from ".TBL_SHOP_ORDER."  ";
				 	*/
	//echo $sql;
	}else if($admininfo[admin_level] == 8){

		$sql = "
					select '기간      ','매출액(원) ', '주문건수','입금예정', '입금확인','배송준비/배송중', '교환 ','주문취소'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
						IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and p.admin = '".$admininfo[company_id]."'
				 	and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."'
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
						IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and p.admin = '".$admininfo[company_id]."'
				 	and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '".date("m/d")." 오늘 ',
						IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and p.admin = '".$admininfo[company_id]."'
				 	and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') =  '".date("Ymd")."'
					union
					Select '최근1주',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and p.admin = '".$admininfo[company_id]."'
				 	and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))." and ".date("Ymd")." between date_format(date,'%Y%m%d')
				 	union
					Select '금주',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and p.admin = '".$admininfo[company_id]."'
				 	and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and date_format(date,'%Y%m%d') between '".$firstday."' and $lastday
				 	union
					Select '최근1개월',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and p.admin = '".$admininfo[company_id]."'
				 	and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')
				 	and ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))." and ".date("Ymd")." between date_format(date,'%Y%m%d')
				 	union
					Select '전체',
					IFNULL(sum(case when method in ('".ORDER_METHOD_BANK."') then total_price else 0 end),0) as bank_total_price,
					IFNULL(sum(case when method in ('".ORDER_METHOD_CARD."') then total_price else 0 end),0) as card_total_price
				 	from ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and p.admin = '".$admininfo[company_id]."'
				 	and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."')";
	/*
		$sql = "Select
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then od.ptprice else 0 end) as today_total_price,
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m%d') between '".$firstday."' and $lastday then od.ptprice else 0 end) as thisweek_total_price,
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then od.ptprice else 0 end) as thismonth_total_price,
					sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_cancel_total_price,
					sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end) as ready_cnt,
					sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end) as order_end_cnt,
					sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end) as thismonth_return_total_cnt
				 	FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' ";
	*/
		//echo $sql;
	}


	$odb->query($sql);
	$datas = $odb->getrows();

	$datas_title[0] = "기간";
	$datas_title[1] = "무통장입금";
	$datas_title[2] = "카드매출";
	$datas_title[3] = "전화결제";
	$datas_title[4] = "가상계좌";
	$datas_title[5] = "실시간계좌이체";

	$mstring = "
	<table border='0' cellspacing='1' cellpadding='5' width='100%'>
      <tr>
        <td bgcolor='#F8F9FA'>
			<table cellpadding=0 cellspacing=1 width=100% bgcolor=#c0c0c0 class='list_table_box'>
				<col width='*' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
		";
	for($i=0;$i<count($datas)-1;$i++){
		//print_r($datas);
			if($i == 0){
				$mstring .= "<tr bgcolor=#ffffff align=center height=30><td class='s_td'>".$datas_title[$i]."</td><td class='m_td'>".$datas[$i][0]."</td><td class='m_td'>".$datas[$i][1]."</td><td class='m_td'>".$datas[$i][2]."</td><td class='m_td'>".$datas[$i][3]."</td><td class='m_td'>".$datas[$i][4]."</td><td class='m_td'>".$datas[$i][5]."</td><td class='e_td'>".$datas[$i][6]."</td></tr>";
			}else{
				$mstring .= "<tr bgcolor=#ffffff height=25 align=right>
				<td class='list_box_td list_bg_gray blk' style='padding:0px 0 0 10px;text-align:left;'><b>".$datas_title[$i]."</b></td>
				<td class='list_box_td'>".number_format($datas[$i][0])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][1])."</td>
				<td class='list_box_td point'>".number_format($datas[$i][2])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][3])."</td>
				<td class='list_box_td'>".number_format($datas[$i][4])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][5])."</td>
				<td class='list_box_td'>".number_format($datas[$i][6])."</td>
				</tr>";
			}
	}
	$mstring .= "</table>";
	$mstring .= "</td></tr></table>";

	return $mstring;


}


function PrintOrderHistory(){
	global $admininfo,$sns_product_type;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));


	if($admininfo[admin_level] == 9){
		if($admininfo[mem_type] == "MD"){
			$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}

		$sql = "
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '".date("m/d")." 오늘 ',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".date("Ymd")."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '최근1주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') between ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))." and ".date("Ymd")." AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select '금주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') between '".$firstday."' and $lastday AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select '최근1개월',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') between ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))." and ".date("Ymd")." AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select '전체',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." od WHERE od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere ";
		//echo $sql;
		//exit;
		/*
		$sql = "Select
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then total_price else 0 end) as today_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(regdate,'%Y%m%d') between '".$firstday."' and $lastday then total_price else 0 end) as thisweek_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_total_price,
					sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_cancel_total_price,
					sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end) as ready_cnt,
					sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end) as order_end_cnt,
					sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end) as thismonth_return_total_cnt
				 	from ".TBL_SHOP_ORDER_DETAIL."  ";
				 	*/
	//echo $sql;
	}else if($admininfo[admin_level] == 8){
		$sql = "
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_cnt
				 	from ".TBL_SHOP_ORDER_DETAIL." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and date_format(od.regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."' AND od.product_type NOT IN (".implode(',',$sns_product_type).")
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and date_format(od.regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."' AND od.product_type NOT IN (".implode(',',$sns_product_type).")
					union
					Select '".date("m/d")." 오늘 ',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and date_format(od.regdate,'%Y%m%d') =  '".date("Ymd")."' AND od.product_type NOT IN (".implode(',',$sns_product_type).")
					union
					Select '최근1주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and ".date("Ymd")."' and ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))." between date_format(od.regdate,'%Y%m%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).")
				 	union
					Select '금주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and date_format(od.regdate,'%Y%m%d') between '".$firstday."' and $lastday AND od.product_type NOT IN (".implode(',',$sns_product_type).")
				 	union
					Select '최근1개월',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and date_format(od.regdate,'%Y%m%d') between ".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))." and ".date("Ymd")." AND od.product_type NOT IN (".implode(',',$sns_product_type).")
				 	union
					Select '전체',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." o, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid AND od.product_type NOT IN (".implode(',',$sns_product_type).") ";
	/*
		$sql = "Select
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then od.ptprice else 0 end) as today_total_price,
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(regdate,'%Y%m%d') between '".$firstday."' and $lastday then od.ptprice else 0 end) as thisweek_total_price,
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then od.ptprice else 0 end) as thismonth_total_price,
					sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_cancel_total_price,
					sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end) as ready_cnt,
					sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end) as order_end_cnt,
					sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end) as thismonth_return_total_cnt
				 	FROM ".TBL_SHOP_ORDER_DETAIL." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' ";
	*/
		//echo $sql;
	}


	$odb->query($sql);
	$datas = $odb->getrows();

	$datas_title[0] = "기간";
	$datas_title[1] = "매출액(원)";
	$datas_title[2] = "주문건수(건)";
	$datas_title[3] = "입금예정(건)";
	$datas_title[4] = "입금확인(건)";
	$datas_title[5] = "배송준비/배송중(건)";
	$datas_title[6] = "교환(건)";
	$datas_title[7] = "주문취소(건)";

	$mstring = "
	<table border='0' cellspacing='1' cellpadding='5' width='100%'>
      <tr>
        <td bgcolor='#F8F9FA'>
			<table cellpadding=0 cellspacing=1 width=100% bgcolor=#c0c0c0 class='list_table_box'>
				<col width='*' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
		";
	for($i=0;$i<count($datas)-1;$i++){
		//print_r($datas);
			if($i == 0){
				$mstring .= "<tr bgcolor=#ffffff align=center height=30><td class='s_td'>".$datas_title[$i]."</td><td class='m_td'>".$datas[$i][0]."</td><td class='m_td'>".$datas[$i][1]."</td><td class='m_td'>".$datas[$i][2]."</td><td class='m_td'>".$datas[$i][3]."</td><td class='m_td'>".$datas[$i][4]."</td><td class='m_td'>".$datas[$i][5]."</td><td class='e_td'>".$datas[$i][6]."</td>";
			}else{
				$mstring .= "<tr bgcolor=#ffffff height=25 align=right>
				<td class='list_box_td list_bg_gray blk' style='padding:0px 0 0 10px;text-align:left;' ><b>".$datas_title[$i]."</b></td>
				<td class='list_box_td'>".number_format($datas[$i][0])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][1])."</td>
				<td class='list_box_td point'>".number_format($datas[$i][2])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][3])."</td>
				<td class='list_box_td'>".number_format($datas[$i][4])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][5])."</td>
				<td class='list_box_td'>".number_format($datas[$i][6])."</td>
				</tr>";
			}
	}
	$mstring .= "</table>";
	$mstring .= "</td></tr></table>";

	return $mstring;


}


?>
