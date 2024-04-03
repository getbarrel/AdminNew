<?
@set_time_limit(0);
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/inventory/inventory.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/cash_manage.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/receipt.lib.php");

//2016-07-04 Hong 메일 세션으로 인해서 선언 필요 
$P = new msLayOut("000000000000000",false);

$db = new Database;
$db2 = new Database;
$mdb = new Database;
$idb = new Database;


//크론으로 체크해야될부분 
/*
	1. 자동배송완료처리	 - ok
	2. 자동주문취소처리
	3. 장바구니자동비움처리
	4. 취소요청자동완료처리
	5. 구매확정일자동처리
*/

$domain = str_replace('www.','',$_SERVER['HTTP_HOST']);
if(substr($domain, 0, 2) == "m.") {
	$domain=substr($domain, 2);
}
$sql = "select * from ".TBL_SHOP_SHOPINFO." where mall_domain = '{$domain}' LIMIT 1";
$db->query($sql);
$db->fetch();
$fetch_shop_info = $db->dt;

$today = date('Y-m-d H:i:s');



/***********************************************************************
//2016-07-04 Hong 추가
//가상계좌 독촉 안내
//
***********************************************************************/
//$ir_startday = date('Y-m-d',strtotime('-3 day'));
//$sql="select op.*, o.bname, o.bmail, o.bname, o.bmobile from shop_order_payment op , shop_order o where op.oid=o.oid and op.regdate between '".$ir_startday." 00:00:00' and '".$ir_startday." 23:59:59' and op.method='".ORDER_METHOD_VBANK."' and op.pay_status='IR' ";
//
//$db->query($sql);
//$orders = $db->fetchall("object");
//
////방어코드... null 일시에 워닝뜸..
//if(!$orders) $orders = array();
//foreach($orders as $order){
//
//	$mail_info[mem_name] = $order[bname];
//	$mail_info[mem_mail] = $order[bmail];
//	$mail_info[mem_id] = $order[bname];
//	$mail_info[mem_mobile] = $order[bmobile];
//	$mail_info[msg_code]	=	'501'; // MSG 발송코드 501 : 가상계좌 독촉
//
//	//sendMessageByStep('order_input_virtual_push', $mail_info);
//}



/***********************************************************************
//2016-07-04 Hong 추가
//배송 지연 안내 메일
//
***********************************************************************/

//$delay_startday = date('Y-m-d',strtotime('-5 day'));
//$sql="select o.oid, o.bname, o.bmail, o.bname, o.bmobile  from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and od.status in ('".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_DELAY."') and od.order_from='self'
//and od.ic_date between '".$delay_startday." 00:00:00' and '".$delay_startday." 23:59:59' group by o.oid ";
//$db->query($sql);
//$orders = $db->fetchall("object");
////방어코드... null 일시에 워닝뜸..
//if(!$orders) $orders = array();
//
//foreach($orders as $order){
//
//	$sql="select * from ".TBL_SHOP_ORDER_DETAIL." od where od.status in ('".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_DELAY."') and od.order_from='self'
//	and od.ic_date between '".$delay_startday." 00:00:00' and '".$delay_startday." 23:59:59' and od.oid='".$order['oid']."' ";
//	$db->query($sql);
//	$order_details = $db->fetchall("object");
//
//
//	$mail_info[mem_name] = $order[bname];
//	$mail_info[mem_mail] = $order[bmail];
//	$mail_info[mem_id] = $order[bname];
//	$mail_info[mem_mobile] = $order[bmobile];
//	$mail_info[msg_code]	=	'500'; // MSG 발송코드 500 : 배송지연
//
//	//sendMessageByStep('delivery_delay_sms', $mail_info);
//}



/***********************************************************************
//
//자동주문취소 (입금이 안될수 자동 주문취소) 프로세서
//
 ***********************************************************************/

$oid = array();
$today = date('Y-m-d' ,strtotime($today));

$sql = "select oid from shop_order where DATE_ADD(order_date,INTERVAL ".$fetch_shop_info['mall_cc_interval']." DAY)  <= '".$today."' and status = '".ORDER_STATUS_INCOM_READY."' ";
//$sql = "select oid from shop_order where DATE_ADD(order_date,INTERVAL  -1 DAY)  <= '".$today."' and status = '".ORDER_STATUS_INCOM_READY."' "; //test

$db->query($sql);
if($db->total){
    $orders = $db->fetchall("object");
    foreach($orders as $order){
        $oid[] = $order[oid];
    }
}


for($j=0;$j < count($oid);$j++){

    //사용대기중인 예치금 => 사용대기취소로 전환 2014-07-23 이학봉 시작
    $sql = "select * from shop_order_payment where oid = '".$oid[$j]."' and pay_type ='G' and pay_status = 'IR' and method = '12'";
    $db->query($sql);
    $db->fetch();
    $deposit = $db->dt[payment_price];

    $sql="select od.*,o.user_code from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and od.oid='".$oid[$j]."' and od.status = '".ORDER_STATUS_INCOM_READY."' $and_company_id ";
    $db->query($sql);
    $order_details = $db->fetchall();

    $user_code = $order_details[0][user_code];

    if($deposit > 0){	//입금예정 중인 주문에서 사용된 예치금 금액이 존재할경우 사용대기 취소로 전환해줌 2014-07-23 이학봉
        InsertDepositInfo('W', '11', '3', $oid[$j], $deposit_ix, $deposit, $user_code, '주문취소로 인한 사용대기취소', $admininfo);
    }
    //사용대기중인 예치금 => 사용대기취소 전환 끝
    $reason_code = 'SYS';//시스템 자동취소
    for($i=0;$i < count($order_details);$i++){

        $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'  ,update_date = NOW(), cc_date = NOW() $am_update_str where oid='".$oid[$j]."' and od_ix='".$order_details[$i][od_ix]."' and status = '".ORDER_STATUS_INCOM_READY."' $and_company_id";
        $db->query($sql);

        //$STATUS_MESSAGE = "[".fetch_order_status_div('IR','CA',"title",$reason_code)."]".$msg;
        set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,"".$fetch_shop_info[mall_cc_interval]."일 경과 시스템 자동입금전취소완료",$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);

        //적립된 마일리지 대기에서 취소, 사용된 적립금은 적립완료!
        InsertReserveInfo($order_details[$i][user_code],$order_details[$i][oid],$order_details[$i][od_ix],$id,$reserve,'9','2',$etc,'mileage',$_SESSION["admininfo"],'IB');	//마일리지,적립금 통합용 함수 2013-06-19 이학봉
        //inventory.lib.php
        UpdateSellingCnt($order_details[$i]);
    }

    //NEW 마일리지 관리 프로세스 주문입금이 되지 않아 자동 취소 했을때 해당 주문의 고객이 마일리지를 사용한 고객이라면 사용한 마일리지를 다시 적립 해주는 프로세스 JK160323
    $sql = "select o.user_code,op.* from ".TBL_SHOP_ORDER." o left join shop_order_payment op on o.oid = op.oid where o.oid = '".$oid[$j]."' and op.method = '13' ";
    $db->query($sql);
    if($db->total){
        $db->fetch();
        $mileage = $db->dt[payment_price];
        $message = "고객 주문 취소에 따른 마일리지 환불";
        $state_type = "add"; //적립 구분

        /*신규 포인트,마일리지 접립 함수 JK 160405*/
        $mileage_data[uid] = $db->dt[user_code];
        $mileage_data[type] = 4;
        $mileage_data[mileage] = $mileage;
        $mileage_data[message] = $message;
        $mileage_data[state_type] = 'add';
        $mileage_data[save_type] = 'mileage';
        $mileage_data[oid] = $oid[$j];

        InsertMileageInfo($mileage_data);
    }

    $sql="update ".TBL_SHOP_ORDER." set status = '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' where oid='".$oid[$j]."' ";
    $db->query($sql);

    $sql="select * from ".TBL_SHOP_ORDER." where oid = '".$oid[$j]."' ";
    $db->query($sql);
    $order_infos = $db->fetchall();

    for($i=0;$i < count($order_infos);$i++){
        //2012-10-09 홍진영
        $mdb->query("select * from ".TBL_SHOP_ORDER." WHERE oid='".$order_infos[$i][oid]."'");
        $order="";
        $order = $mdb->fetch();

        $mdb->query("select *, pid as id from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$order_infos[$i][oid]."' and status= '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' $and_company_id");
        $order_details="";
        $order_details = $mdb->fetchall();

        $db->query("SELECT sum(case when payment_status = 'F'  then -reserve else reserve end) as reserve   from shop_order_price where oid = '".$order_infos[$i][oid]."' ");
        $db->fetch();
        $reserve = $db->dt[reserve];
        if($reserve > 0){
            table_order_price_data_creation($order_infos[$i][oid],'','','F','',0,0,"입금전 취소완료 인한 적립금 환불",$reserve,0,0);
        }

        if($order_details[0][order_from] == 'self'){

            $mail_info[mem_name] = $order[bname];
            $mail_info[mem_mail] = $order[bmail];
            $mail_info[mem_id] = $order[bname];
            $mail_info[mem_mobile] = $order[bmobile];
            $mail_info[msg_code] = '402'; // MSG 발송코드 402 : 주문취소
            $mail_info[order_cancel_date] = date('Y-m-d H:i');

            sendMessageByStep('automatic_order_cancellation', $mail_info);
        }

    }

    //쿠폰 돌려주기!!!
    $UseCoupon["oid"]=$oid[$j];
    $returnCoupon = orderUseCouponReturn($UseCoupon);
}




/***********************************************************************
//
//	자동배송완료 처리 어라운지용 작업 추가 kbk 13/09/13
//
***********************************************************************/



//공휴일  , 단위
$sql = "SELECT  * FROM shop_mall_config WHERE config_name = 'holiday_text'AND mall_ix = '".$fetch_shop_info['mall_ix']."' ";
$db->query($sql);
$q1 = $db->fetchall("object");
$holiday_text = $q1[0]['config_value'];

//주문 자동 취소일
$sql = "SELECT  * FROM shop_mall_config WHERE config_name = 'mall_dc_interval'AND mall_ix = '".$fetch_shop_info['mall_ix']."' ";
$db->query($sql);
$q2 = $db->fetchall("object");
$mall_dc_interval = $q2[0]['config_value'];

//$order_date = date('Ymd', strtotime('2019-03-15')); //오늘 주문일
$order_date = $today;


$ex_hoilday = explode(',',$holiday_text); //추가 휴일

$i = 0;//무한루프 안빠지기 위한 방어코딩
$mus_date = 0; //휴일이 추가 되는 일자

//영업일 기준으로 일단 형변환해서 날짜를 가지고 있음
$date = date('Ymd' ,strtotime($order_date));
$odate = date('Ymd' ,strtotime($order_date));
//자동 취소일  만큼 무조건 돈다
for($i = 1; $mall_dc_interval > $i; $i++){
    $is_hoily = false;
    //해당일이 토요일 일요일 인지 구분
    if(date('w',strtotime($date)) == 0 || date('w',strtotime($date)) == 6){
        //주말 휴일은 영업일이 아니니 플러스
        //$date = date('Ymd' ,strtotime($date. '+1 day'));
        $is_hoily = true;
    }else{
        //나머지 평일
        //설정 휴일 값 계산
        foreach($ex_hoilday as $key => $val){
            //휴일 값이 존재 * 잘못된 값이 들어오면 0으로 되어서 에러회피 기능 가능
            //휴일 값이 월~금요일 중에 오늘과 동일 하면 하루 플러스
            if(strtotime($val) && strtotime($val) == strtotime($date)) {
                $is_hoily = true;
            }
        }
    }

    //비교날짜 +1
    $date = date('Ymd' ,strtotime($date. '+1 day'));
    $mus_date++;
    if($is_hoily){
        //휴일 추가
        $odate = date('Ymd' ,strtotime($odate. '+1 day'));
    }else{
        //일반 영업일 추가 일반일이니 차감
        if($mall_dc_interval > 0) {
            $odate = date('Ymd', strtotime($odate . '+1 day'));
        }
        $mall_dc_interval--;
    }

    if($i >= 365){
        //무한 루프 방지. 최대 365일을 넘어갈수는 없다.
        break;
    }
}

$mall_dc_interval = $mall_dc_interval +$mus_date;

$sql="select od.*,o.user_code from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and DATE_ADD(od.di_date,INTERVAL ".$mall_dc_interval." DAY)  <= '".$today."' and od.status = '".ORDER_STATUS_DELIVERY_ING."' ";

$db->query($sql);


$order_details = $db->fetchall("object");
for($i=0;$i < count($order_details);$i++){

	$sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_COMPLETE."' , dc_date=NOW() , update_date = NOW() where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
	$db->query($sql);

	echo "자동배송완료 " . $order_details[$i][oid] ." ::: ". $order_details[$i][od_ix]."<br/>";
	set_order_status($order_details[$i][oid],ORDER_STATUS_DELIVERY_COMPLETE,$msg,"".$fetch_shop_info[mall_dc_interval]."일 경과 시스템 자동배송완료",$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

	//적립 대기 -> 적립 완료
	if(!empty($order_details[$i][user_code])){
		InsertReserveInfo($order_details[$i][user_code],$order_details[$i][oid],$order_details[$i][od_ix],$id,$order_details[$i][reserve],'1','1',$etc,'mileage',$_SESSION["admininfo"]);

		//New 마일리지 시스템 JK160323
		$sql = "select mg.selling_type from ".TBL_COMMON_MEMBER_DETAIL." cmd left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix where cmd.code = '".$order_details[$i][user_code]."' ";
		$db->query($sql);
		$db->fetch();
		
		if($db->dt[selling_type] == 'R'){
			$Shared_file = "b2c_mileage_rule";	//마일리지 설정값 파일명
			$com_type = 'b2c';
		}else if($db->dt[selling_type] == 'W'){
			$Shared_file = "b2b_mileage_rule";
			$com_type = 'b2b';
		}else{
			$Shared_file = "b2c_mileage_rule";	//마일리지 설정값 파일명
			$com_type = 'b2c';
		}

		$reserve_data = getBasicSellerSetup($Shared_file);

		if($reserve_data[mileage_add_setup] == 'S'){
			$state_type = 'add';
			$message = $order_details[$i]['pname']." 구매시 적립금액";

			/*신규 포인트,마일리지 접립 함수 JK 160405*/
			$mileage_data[uid] = $order_details[$i][user_code];
			$mileage_data[type] = 1;
			$mileage_data[mileage] = $order_details[$i][reserve];
			$mileage_data[message] = $message;
			$mileage_data[state_type] = 'add';
			$mileage_data[save_type] = 'mileage';
			$mileage_data[oid] = $order_details[$i][oid];
			$mileage_data[od_ix] = $order_details[$i][od_ix];
			$mileage_data[pid] = $order_details[$i][pid];
			$mileage_data[ptprice] = $order_details[$i][ptprice];
			$mileage_data[payprice] = $order_details[$i][pt_dcprice];
			InsertMileageInfo($mileage_data);


		}
		//끝
	}
	//배송완료시 셀러 판매신용점수 추가
	//셀러판매신용점수 추가 시작 2014-06-15 이학봉	
	InsertPenaltyInfo('1','2',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'배송완료시 판매신용점수 추가',$_SESSION["admininfo"],'dc');
	//셀러판매신용점수 추가 끝 2014-06-15 이학봉

	/*				
	define("POINT_USE_STATE_IC","1"); // 입금완료
	define("POINT_USE_STATE_DC","2"); // 배송완료
	define("POINT_USE_STATE_BF","3"); // 구매확정
	define("POINT_USE_STATE_CC","4"); // 입금후 취소
	define("POINT_USE_STATE_EC","5"); // 교환확정
	define("POINT_USE_STATE_RC","6"); // 반품확정
	define("POINT_USE_STATE_DD","7"); // 입금완료후 발송지연
	define("POINT_USE_STATE_DDA","8"); // 입금완료후 추가 발송지연 
	define("POINT_USE_STATE_ETC","9"); // 기타
	*/
	insertProductPoint('1', POINT_USE_STATE_DC, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '배송완료시 상품점수 추가', $_SESSION["admininfo"], 'dc');

	//reseller_incentive_incom();
	

	//New 마일리지 적립 프로세스 자동 배송완료 시 마일리지 정책 확인 후 배송완료 일때 적입 프로세스 인경우에 해당 작업 처리 위함 JK160323

	if(!empty($order_details[$i][user_code])){
		$sql = "select mg.selling_type from ".TBL_COMMON_MEMBER_DETAIL." cmd left join ".TBL_SHOP_GROUPINFO." mg on cmd.gp_ix = mg.gp_ix where cmd.code = '".$order_details[$i][user_code]."' ";
		$db->query($sql);
		$db->fetch();
		if($db->dt[selling_type] == 'R'){
			$Shared_file = "b2c_mileage_rule";	//마일리지 설정값 파일명
			$com_type = 'b2c';
		}else if($db->dt[selling_type] == 'W'){
			$Shared_file = "b2b_mileage_rule";
			$com_type = 'b2b';
		}else{
			$Shared_file = "b2c_mileage_rule";	//마일리지 설정값 파일명
			$com_type = 'b2c';
		}

		$reserve_data = getBasicSellerSetup($Shared_file);

		if($reserve_data[mileage_add_setup] == 'S'){
			$state_type = 'add';
			$message = $order_details[$i]['pname']." 구매시 적립금액";
            /*신규 포인트,마일리지 접립 함수 JK 160405*/
            $mileage_data['uid'] = $order_details[$i]['user_code'];
            $mileage_data['type'] = 1;
            $mileage_data['mileage'] = $order_details[$i]['reserve'];
            $mileage_data['message'] = $message;
            $mileage_data['state_type'] = $state_type;
            $mileage_data['save_type'] = 'mileage';
            $mileage_data['oid'] = $order_details[$i]['oid'];
            $mileage_data['od_ix'] = $order_details[$i]['od_ix'];
            $mileage_data['pid'] = $order_details[$i]['pid'];
            $mileage_data['ptprice'] = $order_details[$i]['ptprice'];
            $mileage_data['payprice'] = $order_details[$i]['pt_dcprice'];
            InsertMileageInfo($mileage_data);

		}
	}

}



/***********************************************************************
//
//목적 달성된 개인정보에 대한 파기 정책-주문 상담 내역
//
 ***********************************************************************/

$db->query("SELECT config_name, config_value FROM shop_mall_privacy_setting where mall_ix = '".$_SESSION["layout_config"]["mall_ix"]."'  and config_name in ('achievement_purpose_destruction_order_memo_yn','achievement_purpose_destruction_order_memo_year') ");
$configs = $db->fetchall();
for ($i=0;$i<count($configs);$i++){
    $$configs[$i]['config_name'] = $configs[$i]['config_value'];
}
if($achievement_purpose_destruction_order_memo_yn =="Y"){
    if($achievement_purpose_destruction_order_memo_year!="" && $achievement_purpose_destruction_order_memo_year!="0" && $achievement_purpose_destruction_order_memo_year > 0){
        $sql="delete from shop_order_memo where order_date <= DATE_ADD(NOW(),INTERVAL -".$achievement_purpose_destruction_order_memo_year." YEAR) ";
        $db->query($sql);
    }
}

/***********************************************************************
//
//목적 달성된 개인정보에 대한 파기 정책-주문 내역
//
 ***********************************************************************/

$db->query("SELECT config_name, config_value FROM shop_mall_privacy_setting where mall_ix = '".$_SESSION["layout_config"]["mall_ix"]."'  and config_name in ('achievement_purpose_destruction_order_yn','achievement_purpose_destruction_order_year') ");
$configs = $db->fetchall();
for ($i=0;$i<count($configs);$i++){
    $$configs[$i]['config_name'] = $configs[$i]['config_value'];
}
if($achievement_purpose_destruction_order_yn =="Y"){
    if($achievement_purpose_destruction_order_year!="" && $achievement_purpose_destruction_order_year!="0" && $achievement_purpose_destruction_order_year > 0){
        $sql="select oid from shop_order where order_date <= DATE_ADD(NOW(),INTERVAL -".$achievement_purpose_destruction_order_year." YEAR) ";
        $db->query($sql);
        $orders = $db->fetchall("object");
        for ($i=0;$i<count($orders);$i++){
            $sql="delete from separation_shop_order where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from separation_shop_order_deliveryinfo where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_claim_delivery where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_delivery where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_detail where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_detail_deliveryinfo where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_detail_discount where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_goodsflow_response where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_goodsflow_status where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_memo where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_payment where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_price where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_price_history where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_session where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_siteinfo where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);
            $sql="delete from shop_order_unreceived_claim where oid='".$orders[$i]['oid']."' ";
            $db->query($sql);

            set_order_status($orders[$i]['oid'],'DEL', $achievement_purpose_destruction_order_year."년 이상 지난 주문 삭제","system","");
        }
    }
}

?>