<?
$script_time[start] = time();
include("../class/layout.class");
//include("../class/calender.class");
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database; 

$Script = "<script language='javascript' src='shop_main_v3_calender.js'></script>

";

$vdate = date("Ymd", time());
$yesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));

$vdate_str = date("m/d");
$yesterday_str = date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));

if($_SESSION["admininfo"][admin_level] == 9){
	if($_SESSION["admininfo"][mem_type] == "MD"){
		$addWhere = " and od.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).") ";
	}
}else{
	$addWhere = " and od.company_id = '".$_SESSION["admininfo"][company_id]."'";
}

if($odb->dbms_type == "oracle"){

	$sql = "Select /*+ index(od IDX_OD_REGDATE)*/  '".$yesterday_str." 어제 ' ,
	IFNULL(sum(case when status not in ('SR','') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as total_price,
	IFNULL(sum(case when status not in ('SR','') then 1 else 0 end),0) as total_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as incom_ready_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as cancel_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."') then 1 else 0 end),0) as cancel_total_pnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as exchange_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then 1 else 0 end),0) as exchange_cnt,

	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as return_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then 1 else 0 end),0) as return_total_cnt,

	IFNULL(sum(case when status not in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."')  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as real_price,
	IFNULL(sum(case when status in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."')  then 1 else 0 end),0) as real_cnt,
	1 as vieworder
	from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate = to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
	union
	Select /*+ index(od IDX_OD_REGDATE)*/  '".$vdate_str." 오늘 ',
	IFNULL(sum(case when status not in ('SR','') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as total_price,
	IFNULL(sum(case when status not in ('SR','') then 1 else 0 end),0) as total_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as incom_ready_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as cancel_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."') then 1 else 0 end),0) as cancel_total_pnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as exchange_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then 1 else 0 end),0) as exchange_cnt,

	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as return_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then 1 else 0 end),0) as return_total_cnt,

	IFNULL(sum(case when status not in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."')  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as real_price,
	IFNULL(sum(case when status in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."')  then 1 else 0 end),0) as real_cnt,

	2 as vieworder
	from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate =  to_date('".date("Y-m-d")."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
	order by vieworder asc ";
}else{
	/* member_sale_price 없는 임대형들 때문에 임시 주석
	$sql = "Select '".$yesterday_str." 어제 ' ,
	IFNULL(sum(case when status not in ('SR','') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as total_price,
	IFNULL(sum(case when status not in ('SR','') then 1 else 0 end),0) as total_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as incom_ready_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as cancel_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_pnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as exchange_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then 1 else 0 end),0) as exchange_cnt,

	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as return_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then 1 else 0 end),0) as return_total_cnt,

	IFNULL(sum(case when status not in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as real_price,
	IFNULL(sum(case when status in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as real_cnt,
	1 as vieworder
	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".$yesterday."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
	union
	Select '".$vdate_str." 오늘 ',
	IFNULL(sum(case when status not in ('SR','') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as total_price,
	IFNULL(sum(case when status not in ('SR','') then 1 else 0 end),0) as total_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as incom_ready_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as cancel_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_pnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as exchange_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then 1 else 0 end),0) as exchange_cnt,

	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as return_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then 1 else 0 end),0) as return_total_cnt,

	IFNULL(sum(case when status not in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then ptprice-IFNULL(member_sale_price,0)-IFNULL(use_coupon,0) else 0 end),0) as real_price,
	IFNULL(sum(case when status in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as real_cnt,
	2 as vieworder
	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".$vdate."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
	order by vieworder asc ";
	*/
/*
	$sql = "Select '".$yesterday_str." 어제 ' ,
	IFNULL(sum(case when status not in ('SR','') then ptprice-IFNULL(use_coupon,0) else 0 end),0) as total_price,
	IFNULL(sum(case when status not in ('SR','') then 1 else 0 end),0) as total_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then ptprice-IFNULL(use_coupon,0) else 0 end),0) as incom_ready_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."') then ptprice-IFNULL(use_coupon,0) else 0 end),0) as cancel_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."') then 1 else 0 end),0) as cancel_total_pnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice-IFNULL(use_coupon,0) else 0 end),0) as exchange_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then 1 else 0 end),0) as exchange_cnt,

	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then ptprice-IFNULL(use_coupon,0) else 0 end),0) as return_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then 1 else 0 end),0) as return_total_cnt,

	IFNULL(sum(case when status not in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."')  then ptprice-IFNULL(use_coupon,0) else 0 end),0) as real_price,
	IFNULL(sum(case when status in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."')  then 1 else 0 end),0) as real_cnt,
	1 as vieworder
	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".$yesterday."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
	union
	Select '".$vdate_str." 오늘 ',
	IFNULL(sum(case when status not in ('SR','') then ptprice-IFNULL(use_coupon,0) else 0 end),0) as total_price,
	IFNULL(sum(case when status not in ('SR','') then 1 else 0 end),0) as total_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,

	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then ptprice-IFNULL(use_coupon,0) else 0 end),0) as incom_ready_price,
	IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."') then ptprice-IFNULL(use_coupon,0) else 0 end),0) as cancel_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."') then 1 else 0 end),0) as cancel_total_pnt,
	
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice-IFNULL(use_coupon,0) else 0 end),0) as exchange_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then 1 else 0 end),0) as exchange_cnt,

	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then ptprice-IFNULL(use_coupon,0) else 0 end),0) as return_total_price,
	IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."')  then 1 else 0 end),0) as return_total_cnt,

	IFNULL(sum(case when status not in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."')  then ptprice-IFNULL(use_coupon,0) else 0 end),0) as real_price,
	IFNULL(sum(case when status in ('SR','','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."')  then 1 else 0 end),0) as real_cnt,
	2 as vieworder
	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".$vdate."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
	order by vieworder asc ";
	*/
}

//$db->query($sql);
//$datas = $db->getrows();

$datas_title[0] = "구분";
$datas_title[1] = "주문금액";
$datas_title[2] = "입금확인금액";
$datas_title[3] = "입금예정금액";
$datas_title[4] = "주문취소";
$datas_title[5] = "교환요청";
$datas_title[6] = "반품요청";
$datas_title[7] = "실매출액";


//echo (count($datas)-1)/2;
//exit;
$Contents01 = "
<div class='lately_order_history'>
	<h3>최근 주문현황</h3>
	<table cellpadding='0' cellspacing='0' border='0' width='100%' class=''>
	<col width='32%' />
	<col width='34%' />
	<col width='34%' />";

		for($i=0,$j=0;$j<(count($datas)-1)/2;$i++){
			if($i == 0){
				$Contents01 .= "
				<tr>
					<th>".$datas_title[$j]."</th>
					<th>".$datas[$i][0]."</th>
					<th>".$datas[$i][1]."</th>
				</tr>";
			}else{
				$Contents01 .= "
				<tr>
					<td class='x_header'>".$datas_title[$j]."</td>
					<td>".number_format($datas[$i][0])."(".$datas[($i+1)][0].")</td>
					<td>".number_format($datas[$i][1])."(".$datas[($i+1)][1].")</td>
				</tr>";
				$i++;
			}
			$j++;
		}

		$Contents01 .= "
	</table>";
	
	if($_SESSION["admininfo"]["admin_level"]==9){

		//bbs_qna 5:답변완료
		$sql="select count(*) as cnt from bbs_qna where status not in ('5') 
		union
		select count(*) as cnt from shop_product_qna where bbs_re_bool not in ('Y')"; 

		$db->query($sql);
		$db->fetch(0);
		$bbs_qna_cnt = $db->dt[cnt];
		$db->fetch(1);
		$product_qna_cnt = $db->dt[cnt];


		$sql="select count(*) as new_cnt from bbs_qna where status not in ('5') and date_format(regdate , '%Y%m%d') = '".$vdate."' 
		union
		select count(*) as new_cnt from shop_product_qna where bbs_re_bool not in ('Y') and date_format(regdate , '%Y%m%d') = '".$vdate."' "; 

		$db->query($sql);
		$db->fetch(0);
		$bbs_qna_new_cnt = $db->dt[new_cnt];
		$db->fetch(1);
		$product_qna_new_cnt = $db->dt[new_cnt];

		$Contents01 .= "
		<h3>최근 게시물</h3>
		<table cellpadding='0' cellspacing='0' border='0' width='100%' class=''>
		<col width='50%' />
		<col width='50%' />
			<tr>
				<th>게시판</th>
				<th>미처리</th>
			</tr>
			<tr>
				<td class='x_header'>1:1문의</td>
				<td>".number_format($bbs_qna_cnt)." ".($bbs_qna_new_cnt > 0 ? "<img src='./images/icon_new.png' width='16' style='position:relative;top:4px;' />" :"")."</td>
			</tr>
			<tr>
				<td class='x_header'>상품문의</td>
				<td>".number_format($product_qna_cnt)." ".($product_qna_new_cnt > 0 ? "<img src='./images/icon_new.png' width='16' style='position:relative;top:4px;' />" :"")."</td>
			</tr>
		</table>";


		$sql="select count(*) as cnt from common_user where mem_type not in ('A') 
		union
		select count(*) as cnt from common_user where mem_type not in ('A')  and date_format(date , '%Y%m%d') = '".$yesterday."'
		union
		select count(*) as cnt from common_user where mem_type not in ('A')  and date_format(date , '%Y%m%d') = '".$vdate."' ";
		
		$db->query($sql);
		$db->fetch(0);
		$member_total_cnt = $db->dt[cnt];
		$db->fetch(1);
		$member_yesterday_cnt = $db->dt[cnt];
		$db->fetch(2);
		$member_todaty_cnt = $db->dt[cnt];

		$Contents01 .= "
		<h3>회원가입</h3>
		<table cellpadding='0' cellspacing='0' border='0' width='100%' class=''>
		<col width='32%' />
		<col width='34%' />
		<col width='34%' />
			<tr>
				<th>전체회원</th>
				<th>".$yesterday_str." 어제</th>
				<th>".$vdate_str." 오늘</th>
			</tr>
			<tr>
				<td>".number_format($member_total_cnt)."</td>
				<td>".number_format($member_yesterday_cnt)."</td>
				<td>".number_format($member_todaty_cnt)."</td>
			</tr>
		</table>";


		$sql="select count(*) as cnt from shop_product where state='1'
		union
		select count(*) as cnt from shop_product where date_format(regdate , '%Y%m%d') = '".$yesterday."'
		union
		select count(*) as cnt from shop_product where date_format(regdate , '%Y%m%d') = '".$vdate."' ";
		
		$db->query($sql);
		$db->fetch(0);
		$product_total_cnt = $db->dt[cnt];
		$db->fetch(1);
		$product_yesterday_cnt = $db->dt[cnt];
		$db->fetch(2);
		$product_todaty_cnt = $db->dt[cnt];

		$Contents01 .= "
		<h3>판매상품수량</h3>
		<table cellpadding='0' cellspacing='0' border='0' width='100%' class=''>
		<col width='32%' />
		<col width='34%' />
		<col width='34%' />
			<tr>
				<th>전체판매중<br />상품</th>
				<th>".$yesterday_str." 어제<br />(신규상품)</th>
				<th>".$vdate_str." 오늘<br />(신규상품)</th>
			</tr>
			<tr>
				<td>".number_format($product_total_cnt)."</td>
				<td>".number_format($product_yesterday_cnt)."</td>
				<td>".number_format($product_todaty_cnt)."</td>
			</tr>
		</table>";
	}
	$Contents01 .= "
	</div>
	";
$Contents = $Contents01;




	$P = new MobileLayOut();
	$P->addScript = $Script;
	//$P->strLeftMenu = store_menu();
	$P->strContents = $Contents;
	$P->Navigation = "상품리스트";
	$P->TitleBool = false;
	$P->ServiceInfoBool = true;
	echo $P->PrintLayOut();



$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}

?>
