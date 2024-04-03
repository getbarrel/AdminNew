<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");
include("../reseller/reseller.lib.php");
include("../inventory/inventory.lib.php");
include("../../include/cash_manage.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/receipt.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");


include_once("../logstory/class/sharedmemory.class");

$shmop = new Shared("b2c_coupon_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$coupon_data = $shmop->getObjectForKey("b2c_coupon_rule");
$coupon_data = unserialize(urldecode($coupon_data));

$db = new Database;
$mdb = new Database;


// [level1_status] => CC

if($_SESSION["admininfo"]["admin_level"] != 9){
    $and_company_id = " and company_id = '".$_SESSION["admininfo"]["company_id"]."' ";
}else{
    $and_company_id = "";
}

$ADMIN_MESSAGE = $_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")";
$msg=$od_message;
if($od_message)
    $am_update_str=" , admin_message = '".$od_message."' ";
else
    $am_update_str="";

//echo $status." // ".$pre_type." || ";
/*echo "ORDER_STATUS_INCOM_COMPLETE : ".ORDER_STATUS_INCOM_COMPLETE." || ";                               //입금확인(IC)
echo "ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE : ".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE." || ";   // 입금전취소(IB)

echo "ORDER_STATUS_DELIVERY_READY : ".ORDER_STATUS_DELIVERY_READY." || ";   // 배송준비중(DR)
echo "ORDER_STATUS_DELIVERY_ING : ".ORDER_STATUS_DELIVERY_ING." || ";   // 배송중(DI)


echo "ORDER_STATUS_DELIVERY_COMPLETE : ".ORDER_STATUS_DELIVERY_COMPLETE." || ";   // 배송완료(DC)
echo "ORDER_STATUS_CANCEL_APPLY : ".ORDER_STATUS_CANCEL_APPLY." || ";   // 취소요청(CA)
echo "ORDER_STATUS_SOLDOUT_CANCEL : ".ORDER_STATUS_SOLDOUT_CANCEL." || ";   // 품절취소(SO)
echo "ORDER_STATUS_RETURN_APPLY : ".ORDER_STATUS_RETURN_APPLY." || ";   // 반품요청(RA)
echo "ORDER_STATUS_EXCHANGE_APPLY : ".ORDER_STATUS_EXCHANGE_APPLY." || ";   // 교환요청(EA)*/
/*
echo "quick : ".$quick." || ";
echo "deliverycode : ".$deliverycode." || ";
echo "escrow_yn : ".$escrow_yn." || ";
echo "update_kind : ".$update_kind." || ";
echo "update_type : ".$update_type." || ";
echo "act : ".$act." || ";
echo "tno : ".$tno." || ";
*/
// 에스크로 주문일시 
if($escrow_yn == "1345") {
	// 사이트 기본설정
		$statusData["settle_module"]= $settle_module;
	// // 사이트 기본설절
	// KCP기본설정
		/*프로세스 요청의 종류를 구분하는 변수 에스크로 상태변경 페이지의 경우에 반드시 ‘mod_escrow’로 설정*/
		$statusData["req_tx"]		= "mod_escrow";

		/*에스크로 상태 변경 요청의 구분 변수*/
		$statusData["mod_type"]		= "STE1";	// 배송시작
		//$statusData["mod_type"]		= "STE2";	// 즉시취소 (배송 전 취소)
		//$statusData["mod_type"]		= "STE3";	// 정산보류
		//$statusData["mod_type"]		= "STE4";	// 취소 (배송 후 취소)
		//$statusData["mod_type"]		= "STE5";	// 발급계좌해지(가상계좌의 경우에만 사용)
		//$statusData["mod_type"]		= "STE9_A";	// 계좌이체 구매 확인 후 취소
		//$statusData["mod_type"]		= "STE9_AP";// 계좌이체 구매 확인 후 부분취소
		//$statusData["mod_type"]		= "STE9_AR";// 계좌이체 구매 확인 후 환불
		//$statusData["mod_type"]		= "STE9_V";	// 가상계좌 구매 확인 후 환불
		//$statusData["mod_type"]		= "STE9_VP";// 가상계좌 구매 확인 후 부분환불

		/*결제 완료 후 결제 건에 대한 고유한 값 해당 값으로 거래건의 상태를 조회/변경/취소가 가능하니 결과 처리 페이지에서 tno를 반드시 저장해주시기 바랍니다. ※ 거래고유번호 전체로 사용 하시기 바랍니다.(임의의 숫자나 파싱하여 사용 불가)*/
		$statusData["tno"]			= $tno;
	// // KCP기본설정

	// 배송준비중(DR) / 배송중(DI) 일때 KCP 설정
	if($statusData["mod_type"]  == "STE1"){
		/*택배 회사가 해당 배송 건에 대해 발행하는 운송장 번호를 정확히 입력 배송 시에 택배 회사를 이용하지 않는 자가 배송의 경우에는 반드시 “0000” 입력*/
		$statusData["deli_numb"]	= $deliverycode;

		/*에스크로 배송 시작 시에 사용 배송시에 택배회사를 이용하지 않는 자체 배송의 경우에는 반드시 “자가배송” 입력*/
		if($quick == "01") $deli_corp = "우체국택배";
		else if($quick == "05") $deli_corp = "로젠택배";
		else if($quick == "12") $deli_corp = "롯데택배";
		else if($quick == "13") $deli_corp = "한진택배";
		else if($quick == "18") $deli_corp = "CJ택배";
		else if($quick == "40") $deli_corp = "기타";
		$statusData["deli_corp"]	= $deli_corp;

		$statusData["deli_numb"]	= '11111';
		$statusData["deli_corp"] = "CJ택배";
	}
	// // 배송준비중(DR) / 배송중(DI) 일때 KCP 설정

	// 정산보류 KCP 설정 => 해당 설정은 배송된 주문을 취소 요청 > 취소 완료 시 적용 해야할 항목
	if($statusData["mod_type"]  == "STE3"){
		
	}
	// // 정산보류 KCP 설정 => 해당 설정은 배송된 주문을 취소 요청 > 취소 완료 시 적용 해야할 항목

	// 즉시취소(배송 전 취소) / 취소 (배송 후 취소) KCP 설정 => 해당 설정은 주문상세에서 취소요청 > 취소완료 후 환불쪽에서 처리해야하는 부분
	if($statusData["mod_type"]  == "STE2" || $statusData["mod_type"]  == "STE4"){
		/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 고객의 계좌번호 입력하는 변수*/
		$statusData["mod_account"]	= "778899355741111";

		/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 계좌의 계좌주 명 입력하는 변수*/
		$statusData["mod_depositor"]= "에치엠";

		/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 계좌의 은행코드를 입력하는 변수*/
		$statusData["mod_bankcode"]	= "BK02";
	}
	// // 즉시취소(배송 전 취소) KCP 설정 => 해당 설정은 주문상세에서 취소요청 > 취소완료 후 환불쪽에서 처리해야하는 부분

	// 가상계좌 구매 확인 후 환불 KCP 설정 => 해당 설정은 주문상세에서 취소요청 > 취소완료 후 환불쪽에서 처리해야하는 부분
	if($statusData["mod_type"]  == "STE9_V"){
		/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 고객의 계좌번호 입력하는 변수*/
		$statusData["mod_account"]	= "778899355741111";

		/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 계좌의 계좌주 명 입력하는 변수*/
		$statusData["mod_depositor"]= "에치엠";

		/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 계좌의 은행코드를 입력하는 변수*/
		$statusData["mod_bankcode"]	= "BK02";
	}
	// // 가상계좌 구매 확인 후 환불 KCP 설정 => 해당 설정은 주문상세에서 취소요청 > 취소완료 후 환불쪽에서 처리해야하는 부분

	// 가상계좌 구매 확인 후 환불 KCP 설정 => 해당 설정은 주문상세에서 취소요청 > 취소완료 후 환불쪽에서 처리해야하는 부분
	if($statusData["mod_type"]  == "STE9_VP"){
		/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 고객의 계좌번호 입력하는 변수*/
		$statusData["mod_account"]	= "778899355741111";

		/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 계좌의 계좌주 명 입력하는 변수*/
		$statusData["mod_depositor"]= "에치엠";

		/*가상계좌의 경우, 고객이 환불을 받을 때 환불 받을 계좌의 은행코드를 입력하는 변수*/
		$statusData["mod_bankcode"]	= "BK02";
	}
	// // 가상계좌 구매 확인 후 환불 KCP 설정 => 해당 설정은 주문상세에서 취소요청 > 취소완료 후 환불쪽에서 처리해야하는 부분

	//TODO: call PG cancel process(에스크로 상태변경에도 같은 함수 호출)
	//include("./cancelService/cancel.php");
	//$cancel = new cancel();

	//$cancel_data = $cancel->requestStatus($statusData);	
}
//IC // order_edit

if($act=="goodsflow_refund_regist"){
    //구스플로우 반품 프로세스 잡히기 전 구현부터!
    //$od_ix='251';
    if(isUseGoodsflow()) {
        $OAL = new OpenAPI('goodsflow');
        /* useReturnServiceInfo
         *  isUse: true,false
         *  message: 메세지
         *  data: 실제 반품 코드 사용하는 shop_goodsflow_info 정보
         */
        $retusnServiceInfo = $OAL->lib->useReturnServiceInfo($od_ix);
        if($retusnServiceInfo['isUse']){
            /* returnRegist
             *  success: true,false
             *  message: 메세지
             */
            $result = $OAL->lib->returnRegist($od_ix, $retusnServiceInfo['data']);
            print_r($result);
            exit;
        }
    }
    exit;
}

if($act=="goodsflow_refund_cancel"){
    //구스플로우 반품 프로세스 잡히기 전 구현부터!
    //$od_ix='251';
    if(isUseGoodsflow()) {
        $OAL = new OpenAPI('goodsflow');
        /* checkReturnRegist
         *  isUse: true,false
         *  message: 메세지
         *  data: 배송 고유번호 transUniqueCd
         */
        $retusnServiceInfo = $OAL->lib->checkReturnRegist($od_ix);
        if($retusnServiceInfo['isUse']){
            /* returnCancel
             *  success: true,false
             *  message: 메세지
             */
            $result = $OAL->lib->returnCancel($retusnServiceInfo['data']);
            print_r($result);
            exit;
        }
    }
    exit;
}


if($act == "select_status_update"){		//리스트 페이지에서 상태값 변경시


    if($update_kind){
        $status_str=$update_kind."_status";
        $status=$$status_str;

        $delivery_status_str=$update_kind."_delivery_status";
        $delivery_status=$$delivery_status_str;

        $reason_code_str=$update_kind."_reason_code";
        $reason_code=$$reason_code_str;

        $msg_str=$update_kind."_msg";
        $msg=$$msg_str;

        //2014-03-11 HONG 추가
        $due_date_str=$update_kind."_due_date";
        $due_date=$$due_date_str;

        $uc_message_str = $update_kind."_message";
        $uc_message = $$uc_message_str;

        //$sub_delivery_method=$delivery_method;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
        $sub_quick=$quick;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
        $sub_deliverycode=$deliverycode;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
        //$delivery_method="";
        $quick="";
        $deliverycode="";
    }

    //선택한 주문
    if($update_type==2){


        /************* STATUS 변경 START*************/
        if($status == ORDER_STATUS_CANCEL_COMPLETE){//취소 완료;
            if($pre_type==ORDER_STATUS_CANCEL_APPLY||$pre_type==ORDER_STATUS_INCOM_COMPLETE||$pre_type==ORDER_STATUS_DELIVERY_READY||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY||$pre_type==ORDER_STATUS_DEFERRED_PAYMENT||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){//발주취소요청,입금확인,발주확인,(WMS)출고요청,후불(외상),(WMS)포장대기,(WMS)출고대기 리스트에서 넘어왔을때
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else					$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select od.*,o.status as ostatus,o.user_code from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and od.od_ix in (".$od_ix_str.")  $and_company_id ";

                $db->query($sql);
                $order_details = $db->fetchall();

                $update_str="";
                if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                    //$update_str .= " , delivery_status = '', quick = '', invoice_no = '' ";
                }

                for($i=0;$i < count($order_details);$i++){

                    if($order_details[$i][ostatus]==ORDER_STATUS_DEFERRED_PAYMENT){//외상일때 환불 신청 X
                        $update_str2 ="";
                    }else{
                        $update_str2 = " , refund_status='".ORDER_STATUS_REFUND_APPLY."'  , fa_date=NOW() ";
                    }

                    if($pre_type==ORDER_STATUS_INCOM_COMPLETE||$pre_type==ORDER_STATUS_DELIVERY_READY||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY||$pre_type==ORDER_STATUS_DEFERRED_PAYMENT||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                        $STATUS_MESSAGE = "[".fetch_order_status_div('IR','CA',"title",$reason_code)."]".$msg;
                        $update_str2 .= " , claim_fault_type = '".fetch_order_status_div('IR','CA',"type",$reason_code)."' ";

                        if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                            //기본출고 창고로 이동했던걸 다시 본래 보관장소로 재고 이동!

                            $results = inventory_warehouse_move($order_details[$i]["gu_ix"],$order_details[$i]["pcnt"],$order_details[$i]["delivery_basic_ps_ix"],$order_details[$i]["delivery_ps_ix"],$_SESSION["admininfo"]["charger_ix"],$_SESSION["admininfo"]["charger"],"return",$order_details[$i]["oid"]);

                            if($results!="Y"){
                                echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('창고이동에 문제가 발생했습니다. 계속적으로 발생시 관리자에게 문의 바랍니다..');parent.document.location.reload();</script>");
                                exit;
                            }
                        }

                    }else{
                        $STATUS_MESSAGE = $msg;
                    }

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_CANCEL_COMPLETE."'  , cc_date = NOW(), update_date = NOW() $update_str $update_str2 $am_update_str where   od_ix='".$order_details[$i][od_ix]."'   $and_company_id";
                    $db->query($sql);


                    set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);
                    //적립한 마일리지 취소
                    InsertReserveInfo($order_details[$i][user_code],$order_details[$i][oid],$order_details[$i][od_ix],$id,$reserve,'9','2',$etc,'mileage',$_SESSION["admininfo"]);
                    //inventory.lib.php에

                    if(($reason_code!="" && fetch_order_status_div('IR','CA',"type",$reason_code)=="S") || (empty($reason_code) && $order_details[$i]["claim_fault_type"]=="S")){
                        //S:판매자 책임 B:구매자 책임
                        //입금후 취소시 셀러 판매신용점수 차감 (판매자귀책)
                        //셀러판매신용점수 추가 시작 2014-06-15 이학봉
                        InsertPenaltyInfo('2','4',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'입금후취소 판매신용점수 차감',$_SESSION["admininfo"],'cc');
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
                        insertProductPoint('2', POINT_USE_STATE_CC, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '입금후취소 상품점수 차감', $_SESSION["admininfo"], 'cc');
                    }

                    UpdateSellingCnt($order_details[$i]);

                    //후불 외상시 미수금 처리
                    if($order_details[$i][ostatus]==ORDER_STATUS_DEFERRED_PAYMENT){
                        $noaccept_data="";
                        $noaccept_data[oid]=$order_details[$i][oid];
                        $noaccept_data[msg]="<br/>-".date('Ymd')." ".$order_details[$i][pname]." 취소";
                        $noaccept_data[order_cancel_price]=$order_details[$i][ptprice]-$order_details[$i][member_sale_price]-$order_details[$i][use_coupon];
                        setNoacceptOrderCancel($noaccept_data);
                    }

                    //쿠폰 돌려주기!!!
					if($coupon_data[restore_cc2] == "Y"){
						$UseCoupon["oid"]=$order_details[$i][oid];
						$UseCoupon["od_ix"]=$order_details[$i][od_ix];
						$returnCoupon = orderUseCouponReturn($UseCoupon);
					}

                    //제휴사 주문 상태 연동
                    if(function_exists('sellerToolUpdateOrderStatus')){
                        sellerToolUpdateOrderStatus(ORDER_STATUS_CANCEL_COMPLETE,$order_details[$i][od_ix]);
                    }
                }

                $sql="select *,pid as id from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id group by oid";
                $db->query($sql);
                $order_infos = $db->fetchall("object");

                for($i=0;$i < count($order_infos);$i++){
                    //2012-10-09 홍진영
                    $mdb->query("select * from ".TBL_SHOP_ORDER." WHERE oid='".$order_infos[$i][oid]."'");
                    $order="";
                    $order = $mdb->fetch();

                    $mdb->query("select *, pid as id from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$order_infos[$i][oid]."' and od_ix in (".$od_ix_str.") $and_company_id");
                    $order_details="";
                    $order_details = $mdb->fetchall("object");

                    if($pre_type!=ORDER_STATUS_CANCEL_APPLY ){
                        $product_info=array();
                        $product_com_info=array();
                        foreach($order_details as $key => $detail){

                            $product_com_info[$detail[company_id]] = $detail[delivery_type];

                            $product_info[$key] = $detail;
                            $product_info[$key][claim_type]="C";
                            $product_info[$key][claim_group]="99";//클래임그룹임시로 99로 동일!
                            $product_info[$key][claim_fault_type]=fetch_order_status_div('IR','CA',"type",$reason_code);//클래임책임자
                            $product_info[$key][claim_apply_yn]="Y";//요청상품
                            $product_info[$key][claim_apply_cnt]=$detail[pcnt];//요청상품수량
                        }

                        $resulte = clameChangePriceCalculate($product_info);

                        foreach($product_com_info as $company_id => $delivery_type){

                            $sql="select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='".$order_infos[$i][oid]."' ";
                            $db->query($sql);
                            $db->fetch();
                            $claim_group = $db->dt["claim_group"];

                            $sql="update ".TBL_SHOP_ORDER_DETAIL." set claim_group = '".$claim_group."' where oid='".$order_infos[$i][oid]."' and od_ix in (".$od_ix_str.") and company_id='".$company_id."' $and_company_id ";
                            //echo $sql."<br/>";
                            $db->query($sql);

                            $sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','".$order_infos[$i][oid]."','$company_id','$delivery_type','$claim_group','".$resulte[delivery][$company_id][delivery_price]."',NOW())";
                            //echo $sql."<br/>";
                            $db->query($sql);
                        }
                    }else {
                        //ERP를 통해 취소요청이 들어올시 클레임 배송비 처리를 여기서함.
                        $product_info=array();
                        $product_com_info=array();
                        foreach($order_details as $key => $detail){
                            if($detail['need_claim_yn'] == 'Y') {
                                $product_com_info[$detail[company_id]] = $detail[delivery_type];

                                $product_info[$key] = $detail;
                                $product_info[$key][claim_type]="C";
                                $product_info[$key][claim_group]="99";//클래임그룹임시로 99로 동일!
                                $product_info[$key][claim_fault_type]=fetch_order_status_div('IR','CA',"type","DD");//클래임책임자
                                $product_info[$key][claim_apply_yn]="Y";//요청상품
                                $product_info[$key][claim_apply_cnt]=$detail[pcnt];//요청상품수량
                            }
                        }

                        if(count($product_info) > 0) {
                            $resulte = clameChangePriceCalculate($product_info);

                            foreach($product_com_info as $company_id => $delivery_type){

                                $sql="select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='".$order_infos[$i][oid]."' ";
                                $db->query($sql);
                                $db->fetch();
                                $claim_group = $db->dt["claim_group"];

                                $sql="update ".TBL_SHOP_ORDER_DETAIL." set claim_group = '".$claim_group."' where oid='".$order_infos[$i][oid]."' and od_ix in (".$od_ix_str.") and company_id='".$company_id."' $and_company_id ";
                                //echo $sql."<br/>";
                                $db->query($sql);

                                $sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','".$order_infos[$i][oid]."','$company_id','$delivery_type','$claim_group','".$resulte[delivery][$company_id][delivery_price]."',NOW())";
                                //echo $sql."<br/>";
                                $db->query($sql);
                            }
                        }
                    }

                    if($order_details[0][order_from] == 'self'){
                        $mail_info[mem_name] = $order[bname];
                        $mail_info[mem_mail] = $order[bmail];
                        $mail_info[mem_id] = $order[bname];
                        $mail_info[mem_mobile] = $order[bmobile];
                        $mail_info[msg_code]	=	'402'; // MSG 발송코드 402 : 주문취소
                        //sendMessageByStep('order_cancel', $mail_info); //홍차장문의1
                    }
                }
            }
            //exit;
        }elseif($status == ORDER_STATUS_SOLDOUT_CANCEL){

            $od_ix_str="";

            for($j=0;$j < count($od_ix);$j++){
                if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                else					$od_ix_str .= ",'".$od_ix[$j]."'";
            }

            $sql="select od.*,o.status as ostatus,o.user_code from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and od.od_ix in (".$od_ix_str.")  $and_company_id ";

            $db->query($sql);
            $order_details = $db->fetchall();

            $update_str="";


            for($i=0;$i < count($order_details);$i++){

                if($order_details[$i][ostatus]==ORDER_STATUS_DEFERRED_PAYMENT){//외상일때 환불 신청 X
                    $update_str2 ="";
                }else{
                    $update_str2 = " , refund_status='".ORDER_STATUS_REFUND_APPLY."'  , fa_date=NOW() ";
                }

                $STATUS_MESSAGE = $msg;

                $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_SOLDOUT_CANCEL."'  , cc_date = NOW(), update_date = NOW() $update_str $update_str2 $am_update_str where   od_ix='".$order_details[$i][od_ix]."'   $and_company_id";
                $db->query($sql);


                set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);

                UpdateSellingCnt($order_details[$i]);

                //후불 외상시 미수금 처리
                if($order_details[$i][ostatus]==ORDER_STATUS_DEFERRED_PAYMENT){
                    $noaccept_data="";
                    $noaccept_data[oid]=$order_details[$i][oid];
                    $noaccept_data[msg]="<br/>-".date('Ymd')." ".$order_details[$i][pname]." 취소";
                    $noaccept_data[order_cancel_price]=$order_details[$i][ptprice]-$order_details[$i][member_sale_price]-$order_details[$i][use_coupon];
                    setNoacceptOrderCancel($noaccept_data);
                }

                //쿠폰 돌려주기!!!
                if($coupon_data[restore_cc2] == "Y"){
                    $UseCoupon["oid"]=$order_details[$i][oid];
                    $UseCoupon["od_ix"]=$order_details[$i][od_ix];
                    $returnCoupon = orderUseCouponReturn($UseCoupon);
                }

                //제휴사 주문 상태 연동
                if(function_exists('sellerToolUpdateOrderStatus')){
                    sellerToolUpdateOrderStatus(ORDER_STATUS_SOLDOUT_CANCEL,$order_details[$i][od_ix]);
                }
            }

            $sql="select *,pid as id from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id group by oid";
            $db->query($sql);
            $order_infos = $db->fetchall("object");

            for($i=0;$i < count($order_infos);$i++){
                //2012-10-09 홍진영
                $mdb->query("select * from ".TBL_SHOP_ORDER." WHERE oid='".$order_infos[$i][oid]."'");
                $order="";
                $order = $mdb->fetch();

                $mdb->query("select *, pid as id from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$order_infos[$i][oid]."' and od_ix in (".$od_ix_str.") $and_company_id");
                $order_details="";
                $order_details = $mdb->fetchall("object");


                $product_info=array();
                $product_com_info=array();
                foreach($order_details as $key => $detail){

                    $product_com_info[$detail[company_id]] = $detail[delivery_type];

                    $product_info[$key] = $detail;
                    $product_info[$key][claim_type]="C";
                    $product_info[$key][claim_group]="99";//클래임그룹임시로 99로 동일!
                    $product_info[$key][claim_fault_type]=fetch_order_status_div('IR','CA',"type",$reason_code);//클래임책임자
                    $product_info[$key][claim_apply_yn]="Y";//요청상품
                    $product_info[$key][claim_apply_cnt]=$detail[pcnt];//요청상품수량
                }

                $resulte = clameChangePriceCalculate($product_info);

                foreach($product_com_info as $company_id => $delivery_type){

                    $sql="select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='".$order_infos[$i][oid]."' ";
                    $db->query($sql);
                    $db->fetch();
                    $claim_group = $db->dt["claim_group"];

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set claim_group = '".$claim_group."' where oid='".$order_infos[$i][oid]."' and od_ix in (".$od_ix_str.") and company_id='".$company_id."' $and_company_id ";
                    //echo $sql."<br/>";
                    $db->query($sql);

                    $sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','".$order_infos[$i][oid]."','$company_id','$delivery_type','$claim_group','".$resulte[delivery][$company_id][delivery_price]."',NOW())";
                    //echo $sql."<br/>";
                    $db->query($sql);
                }


                if($order_details[0][order_from] == 'self'){
                    $mail_info[mem_name] = $order[bname];
                    $mail_info[mem_mail] = $order[bmail];
                    $mail_info[mem_id] = $order[bname];
                    $mail_info[mem_mobile] = $order[bmobile];
                    $mail_info[msg_code]	=	'402'; // MSG 발송코드 402 : 주문취소
                    $mail_info[pname]	= $order_infos[$i]['pname'];

                    sendMessageByStep('order_soldout_cancel', $mail_info);
                }
            }

        }elseif($status == ORDER_STATUS_CANCEL_APPLY){//취소요청
            if($pre_type==ORDER_STATUS_DELIVERY_READY||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){ //배송준비중,(WMS)출고요청,(WMS)포장대기,(WMS)출고대기 리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else					$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $update_str="";
                if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                    //$update_str .= " , delivery_status = '', quick = '', invoice_no = '' ";
                }

                $update_str .= " , claim_fault_type = '".fetch_order_status_div('IR','CA',"type",$reason_code)."' ";

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();

                for($i=0;$i < count($order_details);$i++){

                    if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                        //기본출고 창고로 이동했던걸 다시 본래 보관장소로 재고 이동!

                        $results = inventory_warehouse_move($order_details[$i]["gu_ix"],$order_details[$i]["pcnt"],$order_details[$i]["delivery_basic_ps_ix"],$order_details[$i]["delivery_ps_ix"],$_SESSION["admininfo"]["charger_ix"],$_SESSION["admininfo"]["charger"],"return",$order_details[$i]["oid"]);

                        if($results!="Y"){
                            echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('창고이동에 문제가 발생했습니다. 계속적으로 발생시 관리자에게 문의 바랍니다..');parent.document.location.reload();</script>");
                            exit;
                        }
                    }

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_CANCEL_APPLY."' ,ca_date = NOW(), update_date = NOW() $update_str $am_update_str
					where  od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    //echo $sql."<br><br>";
                    $db->query($sql);

                    $STATUS_MESSAGE = "[".fetch_order_status_div('IR','CA',"title",$reason_code)."]".$msg;
                    set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);
                }

                $sql="select *,pid as id from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id group by oid";
                $db->query($sql);
                $order_infos = $db->fetchall("object");

                for($i=0;$i < count($order_infos);$i++){
                    //2012-10-09 홍진영
                    $mdb->query("select * from ".TBL_SHOP_ORDER." WHERE oid='".$order_infos[$i][oid]."'");
                    $order="";
                    $order = $mdb->fetch();

                    $mdb->query("select *, pid as id from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$order_infos[$i][oid]."' and od_ix in (".$od_ix_str.") $and_company_id");
                    $order_details="";
                    $order_details = $mdb->fetchall("object");

                    $product_info=array();
                    $product_com_info=array();
                    foreach($order_details as $key => $detail){

                        $product_com_info[$detail[company_id]] = $detail[delivery_type];

                        $product_info[$key] = $detail;
                        $product_info[$key][claim_type]="C";
                        $product_info[$key][claim_group]="99";//클래임그룹임시로 99로 동일!
                        $product_info[$key][claim_fault_type]=fetch_order_status_div('IR','CA',"type",$reason_code);//클래임책임자
                        $product_info[$key][claim_apply_yn]="Y";//요청상품
                        $product_info[$key][claim_apply_cnt]=$detail[pcnt];//요청상품수량
                    }

                    $resulte = clameChangePriceCalculate($product_info);

                    foreach($product_com_info as $company_id => $delivery_type){

                        $sql="select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='".$order_infos[$i][oid]."' ";
                        $db->query($sql);
                        $db->fetch();
                        $claim_group = $db->dt["claim_group"];

                        $sql="update ".TBL_SHOP_ORDER_DETAIL." set claim_group = '".$claim_group."' where oid='".$order_infos[$i][oid]."' and od_ix in (".$od_ix_str.") and company_id='".$company_id."' $and_company_id ";
                        //echo $sql."<br/>";
                        $db->query($sql);

                        $sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','".$order_infos[$i][oid]."','$company_id','$delivery_type','$claim_group','".$resulte[delivery][$company_id][delivery_price]."',NOW())";
                        //echo $sql."<br/>";
                        $db->query($sql);
                    }
                }
            }
        }elseif($status == ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE){//입금전 취소완료

            if($pre_type==ORDER_STATUS_INCOM_READY || $pre_type=="order_edit"){ //입금예정리스트에서 넘어왔을떄

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

                        /*예치금 처리 관련 데이터 JK160804*/
                        $deposit_data = array();

                        $deposit_data['user_code'] = $user_code;
                        $deposit_data['oid'] = $oid[$j];
                        $deposit_data['deposit'] = $_SESSION['order']['deposit_price'];
                        $deposit_data['history_type'] = '3';
                        $deposit_data['etc'] = '주문 취소에 따른 예치금 입금';
                        $deposit_data['use_type'] = 'P';


                        if(function_exists(DepositManagement)){
                            DepositManagement($deposit_data);
                        }
                    }
                    //사용대기중인 예치금 => 사용대기취소 전환 끝

                    for($i=0;$i < count($order_details);$i++){

                        $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'  ,update_date = NOW(), cc_date = NOW() $am_update_str where oid='".$oid[$j]."' and od_ix='".$order_details[$i][od_ix]."' and status = '".ORDER_STATUS_INCOM_READY."' $and_company_id";
                        $db->query($sql);

                        $STATUS_MESSAGE = "[".fetch_order_status_div('IR','CA',"title",$reason_code)."]".$msg;
                        set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);

                        //적립된 마일리지 대기에서 취소, 사용된 적립금은 적립완료!
                        InsertReserveInfo($order_details[$i][user_code],$order_details[$i][oid],$order_details[$i][od_ix],$id,$reserve,'9','2',$etc,'mileage',$_SESSION["admininfo"],'IB');	//마일리지,적립금 통합용 함수 2013-06-19 이학봉
                        //inventory.lib.php
                        UpdateSellingCnt($order_details[$i]);
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
                            $mail_info[msg_code]	=	'402'; // MSG 발송코드 402 : 주문취소
                            sendMessageByStep('order_cancel', $mail_info);
                        }
                    }
					
					if($coupon_data[restore_cc1] == "Y"){
						//쿠폰 돌려주기!!!
						$UseCoupon["oid"]=$oid[$j];
						$returnCoupon = orderUseCouponReturn($UseCoupon);
					}

                    //NEW 마일리지 관리 프로세스 주문을 입금전 취소 했을때 해당 고객이 마일리지를 사용한 고객이라면 사용한 마일리지를 다시 적립 해주는 프로세스 JK160323
                    $sql = "select o.user_code,op.* from ".TBL_SHOP_ORDER." o left join shop_order_payment op on o.oid = op.oid where o.oid = '".$oid[$j]."' and op.method = '13' ";
                    $db->query($sql);
                    if($db->total){
                        $db->fetch();
                        $mileage = $db->dt[payment_price];
                        $message = "고객 주문 취소에 따른 마일리지 환불";
                        $state_type = "add"; //적립 구분
                        InsertMileageInfo($db->dt[user_code],'4',$mileage,$message,$state_type,$oid[$j],$od_ix='',$pid='',$ptprice='',$payprice='');

                        $mileage_data[uid] = $db->dt['user_code'];
                        $mileage_data[type] = 4;
                        $mileage_data[mileage] = $mileage;
                        $mileage_data[message] = '입금전 취소에 따른 마일리지 환불';
                        $mileage_data[state_type] = 'add';
                        $mileage_data[save_type] = 'mileage';
                        $mileage_data[oid] = $oid[$j];
                        InsertMileageInfo($mileage_data);
                    }
                }
            }

        }elseif($status == ORDER_STATUS_DELIVERY_READY){//배송준비중(발주확인)
            if($pre_type==ORDER_STATUS_CANCEL_APPLY || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){ //발주취소요청,(WMS)포장대기,(WMS)출고대기 리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();

                $update_str="";
                if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                    $update_str .= " , delivery_status = '', quick = '', invoice_no = '' ";
                }

                for($i=0;$i < count($order_details);$i++){

                    if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                        //기본출고 창고로 이동했던걸 다시 본래 보관장소로 재고 이동!

                        $results = inventory_warehouse_move($order_details[$i]["gu_ix"],$order_details[$i]["pcnt"],$order_details[$i]["delivery_basic_ps_ix"],$order_details[$i]["delivery_ps_ix"],$_SESSION["admininfo"]["charger_ix"],$_SESSION["admininfo"]["charger"],"return",$order_details[$i]["oid"]);

                        if($results!="Y"){
                            echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('창고이동에 문제가 발생했습니다. 계속적으로 발생시 관리자에게 문의 바랍니다..');parent.document.location.reload();</script>");
                            exit;
                        }
                    }

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_READY."' , update_date = NOW() $update_str $am_update_str
					where  od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    //echo $sql."<br><br>";
                    $db->query($sql);

                    $STATUS_MESSAGE = "[".fetch_order_status_div('CA','CD',"title",$reason_code)."]".$msg;
                    set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);

                    //제휴사 주문 상태 연동
                    if(function_exists('sellerToolUpdateOrderStatus')){
                        sellerToolUpdateOrderStatus(ORDER_STATUS_DELIVERY_READY,$order_details[$i][od_ix],$reason_code);
                    }
                }

                if($pre_type==ORDER_STATUS_CANCEL_APPLY) {
                    //SG Data 취소철회 생성
                    include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/sgdata/sgdata.class.php");
                    $erp = new SgERP('A');
                    $erp->execute($order_details);
                }

            }elseif($pre_type == ORDER_STATUS_INCOM_COMPLETE||$pre_type == ORDER_STATUS_DEFERRED_PAYMENT||$pre_type == ORDER_STATUS_DELIVERY_ING||$pre_type=="order_edit"||$pre_type==ORDER_STATUS_EXCHANGE_READY){ //입금확인,후불(외상),송장입력완료리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                //2014-10-29 고객이 입금확인에서 취소요청이나 취소 완료로 상태 변경할때 관리자가 배송준비중으로 바꾸지 못하게끔 처리하기!!
                if($pre_type == ORDER_STATUS_INCOM_COMPLETE){
                    $RESULT_CHECK=true;
                    $RESULT_SUCCESS=0;

                    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") and status!='".ORDER_STATUS_INCOM_COMPLETE."' $and_company_id ";
                    $db->query($sql);

                    $RESULT_FAIL=$db->total;

                    $where = " and status='".ORDER_STATUS_INCOM_COMPLETE."' ";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $where $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();

                for($i=0;$i < count($order_details);$i++){

                    $RESULT_SUCCESS++;

                    //빠른송장입력일때 송장입력처리!
                    if($pre_type!="order_edit"){
                        //$delivery_method=$sub_delivery_method[$order_details[$i][od_ix]];
                        $delivery_company=$sub_quick[$order_details[$i][od_ix]];
                        $deliverycode=$sub_deliverycode[$order_details[$i][od_ix]];
                    }

                    $update_str = "";

                    //if($delivery_method!="")									$update_str .= " , delivery_method= '".$delivery_method."' ";
                    if($delivery_company!="")									$update_str .= " , quick= '".$delivery_company."' ";
                    if($deliverycode!="")										$update_str .= " , invoice_no= '".$deliverycode."' ";

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_READY."', update_date = NOW(), dr_date = NOW() $update_str $am_update_str
					where  od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    //echo $sql."<br><br>";
                    $db->query($sql);

                    if($pre_type!="order_edit"){
                        $msg = "일괄배송준비중처리";
                    }

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],'',$delivery_company,$deliverycode);

                    if($pre_type == ORDER_STATUS_DELIVERY_ING){
                        UpdateProductCnt_cancel($order_details[$i]);
                    }

                    //제휴사 주문 상태 연동
                    if(function_exists('sellerToolUpdateOrderStatus')){
                        sellerToolUpdateOrderStatus(ORDER_STATUS_DELIVERY_READY,$order_details[$i][od_ix]);
                    }
                }

				if($escrow_yn == "Y") {
					// 사이트 기본설정
						$statusData["settle_module"]= $settle_module;
					// // 사이트 기본설절
					// KCP기본설정
						/*프로세스 요청의 종류를 구분하는 변수 에스크로 상태변경 페이지의 경우에 반드시 ‘mod_escrow’로 설정*/
						$statusData["req_tx"]		= "mod_escrow";

						/*에스크로 상태 변경 요청의 구분 변수*/
						$statusData["mod_type"]		= "STE1";	// 배송시작
						//$statusData["mod_type"]		= "STE2";	// 즉시취소 (배송 전 취소)
						//$statusData["mod_type"]		= "STE3";	// 정산보류
						//$statusData["mod_type"]		= "STE4";	// 취소 (배송 후 취소)
						//$statusData["mod_type"]		= "STE5";	// 발급계좌해지(가상계좌의 경우에만 사용)
						//$statusData["mod_type"]		= "STE9_A";	// 계좌이체 구매 확인 후 취소
						//$statusData["mod_type"]		= "STE9_AP";// 계좌이체 구매 확인 후 부분취소
						//$statusData["mod_type"]		= "STE9_AR";// 계좌이체 구매 확인 후 환불
						//$statusData["mod_type"]		= "STE9_V";	// 가상계좌 구매 확인 후 환불
						//$statusData["mod_type"]		= "STE9_VP";// 가상계좌 구매 확인 후 부분환불

						/*결제 완료 후 결제 건에 대한 고유한 값 해당 값으로 거래건의 상태를 조회/변경/취소가 가능하니 결과 처리 페이지에서 tno를 반드시 저장해주시기 바랍니다. ※ 거래고유번호 전체로 사용 하시기 바랍니다.(임의의 숫자나 파싱하여 사용 불가)*/
						$statusData["tno"]			= $tno;
					// // KCP기본설정

					// 배송준비중(DR) / 배송중(DI) 일때 KCP 설정
					if($statusData["mod_type"]  == "STE1"){
						/*택배 회사가 해당 배송 건에 대해 발행하는 운송장 번호를 정확히 입력 배송 시에 택배 회사를 이용하지 않는 자가 배송의 경우에는 반드시 “0000” 입력*/
						$statusData["deli_numb"]	= $deliverycode;

						/*에스크로 배송 시작 시에 사용 배송시에 택배회사를 이용하지 않는 자체 배송의 경우에는 반드시 “자가배송” 입력*/
						if($quick == "01") $deli_corp = "우체국택배";
						else if($quick == "05") $deli_corp = "로젠택배";
						else if($quick == "12") $deli_corp = "롯데택배";
						else if($quick == "13") $deli_corp = "한진택배";
						else if($quick == "18") $deli_corp = "CJ택배";
						else if($quick == "40") $deli_corp = "기타";
						$statusData["deli_corp"]	= $deli_corp;

						//$statusData["deli_numb"]	= '11111';
						//$statusData["deli_corp"] = "CJ택배";
					}
					// // 배송준비중(DR) / 배송중(DI) 일때 KCP 설정

					//TODO: call PG cancel process(에스크로 상태변경에도 같은 함수 호출)
					include("./cancelService/cancel.php");
					$cancel = new cancel();

					$cancel_data = $cancel->requestStatus($statusData);
				}

                $sql="select oid from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $where $and_company_id group by oid ";
                $db->query($sql);
                $order = $db->fetchall();
                for($i=0;$i < count($order);$i++){
                    $db->query("SELECT max(delivery_box_no)+1 as next_delivery_box_no  FROM ".TBL_SHOP_ORDER." WHERE date_format(order_date, '%Y%m%d') = '".date("Ymd")."' ");
                    $db->fetch();
                    $delivery_box_no = $db->dt[next_delivery_box_no];
                    $db->query("UPDATE ".TBL_SHOP_ORDER." SET delivery_box_no = '".$delivery_box_no."' WHERE oid='".$order[$i][oid]."' and delivery_box_no = '0' ");
                }
            }
        }else if($status == ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY){
            for($j=0;$j < count($oid);$j++){
                $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY."' where oid='".$oid[$j]."' and status = '".ORDER_STATUS_INCOM_COMPLETE."' $add_com_query "; //and status = '".ORDER_STATUS_CANCEL_APPLY."'
                //echo $sql."<br><br>";
                $db->query($sql);

                $update_count += mysql_affected_rows();

                set_order_status($oid[$j],$pid,$status,$status_message,$admin_message,$admininfo[company_id],$quick,$deliverycode);
                /*
                $sql = "insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, pid, status, status_message, company_id,quick,invoice_no, regdate )
                        values
                        ('','".$oid[$j]."','','".ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY."','해외프로세싱중 처리 ','".$admininfo[company_id]."','$quick','$deliverycode',NOW())";
                //echo $sql."<br><br>";
                $db->sequences = "SHOP_ORDER_STATUS_SEQ";
                $db->query($sql);
                */
            }
        }elseif($status == ORDER_STATUS_DELIVERY_DELAY){//배송지연
            if($pre_type==ORDER_STATUS_INCOM_COMPLETE || $pre_type==ORDER_STATUS_CANCEL_APPLY || $pre_type==ORDER_STATUS_DELIVERY_READY || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){ //입금확인,발주취소요청,발주확인,(WMS)출고요청,(WMS)포장대기,(WMS)출고대기 리스트에서 넘어왔을때
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else					$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();

                $update_str="";
                if($due_date!=""){
                    $update_str .= " , due_date = '".str_replace("-","",$due_date)."' ";
                }

                if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                    //$update_str .= " , delivery_status = '', quick = '', invoice_no = '' ";
                }

                for($i=0;$i < count($order_details);$i++){

                    if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                        //기본출고 창고로 이동했던걸 다시 본래 보관장소로 재고 이동!

                        $results = inventory_warehouse_move($order_details[$i]["gu_ix"],$order_details[$i]["pcnt"],$order_details[$i]["delivery_basic_ps_ix"],$order_details[$i]["delivery_ps_ix"],$_SESSION["admininfo"]["charger_ix"],$_SESSION["admininfo"]["charger"],"return",$order_details[$i]["oid"]);

                        if($results!="Y"){
                            echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('창고이동에 문제가 발생했습니다. 계속적으로 발생시 관리자에게 문의 바랍니다..');parent.document.location.reload();</script>");
                            exit;
                        }
                    }

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_DELAY."' , update_date = NOW() $update_str $am_update_str
					where  od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    //echo $sql."<br><br>";
                    $db->query($sql);

                    if($pre_type==ORDER_STATUS_CANCEL_APPLY){
                        $STATUS_MESSAGE = "[".fetch_order_status_div('CA','CD',"title",$reason_code)."]".$msg;
                    }elseif($pre_type==ORDER_STATUS_INCOM_COMPLETE||$pre_type==ORDER_STATUS_DELIVERY_READY||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                        $STATUS_MESSAGE = "[".fetch_order_status_div('DD','DD',"title",$reason_code)."]".$msg;
                    }

                    set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);
                }
            }
        }elseif($status == ORDER_STATUS_INCOM_COMPLETE){//입금확인(IC)

            if($pre_type==ORDER_STATUS_INCOM_READY || $pre_type=="order_edit"){ //입금예정 리스트에서 넘어왔을떄

                for($j=0;$j < count($oid);$j++){

                    $sql="select * from ".TBL_SHOP_ORDER." where oid='".$oid[$j]."'  ";
                    $db->query($sql);
                    $order = $db->fetch();

                    //사용대기중인 예치금 => 사용완료 전환 2014-07-23 이학봉 시작
                    $sql = "select * from shop_order_payment where oid = '".$oid[$j]."' and pay_type ='G' and pay_status = 'IR' and method = '12'";
                    $db->query($sql);
                    $db->fetch();
                    $deposit = $db->dt[payment_price];

                    if($deposit > 0){	//입금예정 중인 주문에서 입금확인시 사용된 예치금 금액이 존재할경우 사용완료 전환해줌 2014-07-23 이학봉
                        InsertDepositInfo('W', '4', '7', $oid[$j], $deposit_ix, $deposit, $order[user_code], '입금확인으로 인한 사용완료처리', $admininfo);
                    }
                    //사용대기중인 예치금 => 사용완료 전환 끝

                    if($order[status] == ORDER_STATUS_INCOM_READY){
                        $sql="update ".TBL_SHOP_ORDER." set status = '".ORDER_STATUS_INCOM_COMPLETE."' where oid='".$oid[$j]."'  ";
                        $db->query($sql);

                        $db->query("select expect_product_price, expect_delivery_price from shop_order_price WHERE oid='".$oid[$j]."' and payment_status='G' ");
                        $db->fetch();

                        $expect_product_price = $db->dt[expect_product_price];
                        $expect_delivery_price = $db->dt[expect_delivery_price];

                        table_order_price_data_creation($oid[$j],'','','G','P',0,$expect_product_price,"관리자 입금완료",0,0,0);
                        if($expect_delivery_price > 0){
                            table_order_price_data_creation($oid[$j],'','','G','D',0,$expect_delivery_price,"관리자 입금완료",0,0,0);
                        }

                        $db->query("update shop_order_payment set pay_status='IC', ic_date=NOW() WHERE oid='".$oid[$j]."' and pay_type = 'G' and pay_status='IR'  ");
                    }

                    if($pre_type=="order_edit"){
                        $where = " and od.od_ix in ('".implode("','",$od_ix)."') ";
                    }else{
                        $where = " and od.status = '".ORDER_STATUS_INCOM_READY."' ";
                    }

                    $sql="select od.*,odd.rmobile,od.pid as id from ".TBL_SHOP_ORDER_DETAIL." od left join shop_order_detail_deliveryinfo odd on (od.odd_ix=odd.odd_ix) where od.oid='".$oid[$j]."' $where $and_company_id";
                    $db->query($sql);
                    $order_details = $db->fetchall();

                    for($i=0;$i < count($order_details);$i++){

                        if(!$order_details[$i][ic_date])		$update_str=" , ic_date=NOW() ";
                        else									$update_str="";

                        $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_INCOM_COMPLETE."' ,update_date = NOW() $update_str $am_update_str where oid='".$oid[$j]."' and od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                        $db->query($sql);

                        //[S] 리셀러 정보 업데이트
                        //resellerOrder($order_details[$i]['od_ix']);
                        //[E] 리셀러 정보 업데이트

                        set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                        if($order_details[$i][hotcon_event_id] && $order_details[$i][hotcon_pcode] && $order[status] == ORDER_STATUS_INCOM_READY){
                            CallHotCon($order[user_code], $oid[$j], $order_details[$i][pid], $order_details[$i][hotcon_event_id], $order_details[$i][hotcon_pcode], $order_details[$i][pcnt], $order_details[$i][rmobile]);
                        }

                        //입금완료시 셀러 판매신용점수 추가
                        //셀러판매신용점수 추가 시작 2014-06-15 이학봉
                        InsertPenaltyInfo('1','1',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'입금완료 판매신용점수 추가',$_SESSION["admininfo"],'ic');
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
                        insertProductPoint('1', POINT_USE_STATE_IC, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '입금후취소 상품점수 추가', $_SESSION["admininfo"], 'ic');

                        if($i==0)		 $goodname=$order_details[$i]["pname"];
                    }

                    //증빙문서 처리!
                    $sql="select * from receipt where order_no='".$oid[$j]."' and receipt_yn='Y' ";
                    $db->query($sql);
                    if($db->total){
                        $receipt = $db->fetch();

                        $sql="select 
							sum(case when method='".ORDER_METHOD_SAVEPRICE."' then payment_price else '0' end) as saveprice,
							sum(case when method!='".ORDER_METHOD_SAVEPRICE."' then payment_price else '0' end) as pgprice
						from 
							shop_order_payment
						where oid='".$oid[$j]."' and pay_status='IC' and pay_type = 'G' and method not in ('".ORDER_METHOD_RESERVE."') and receipt_yn='Y' ";
                        $db->query($sql);
                        $payment = $db->fetch();

                        $cr_price=$payment[pgprice]+$payment[saveprice];
                        $sup_price = round($cr_price/1.1);
                        $tax = $cr_price - $sup_price;

                        $ini_receipt["goodname"]=cut_str($goodname,36) . (count($order_details) > 1 ? ' 외 ' . (count($order_details) - 1) . '건' : '');
                        $ini_receipt["cr_price"]=$cr_price;
                        $ini_receipt["sup_price"]=$sup_price;
                        $ini_receipt["tax"]=$tax;
                        $ini_receipt["buyername"]=$order[bname];
                        $ini_receipt["buyeremail"]=$order[bmail];
                        $ini_receipt["buyertel"]=$order[btel];
                        $ini_receipt["reg_num"]=$receipt[m_number];
                        $ini_receipt["useopt"]=$receipt[m_useopt];

                        $RECEIPT_RESULT = ini_receipt_apply($ini_receipt);

                        if( $RECEIPT_RESULT["result"] == "Y" ){
                            $db->query("update receipt set receipt_yn='C' where order_no ='".$oid[$j]."' ");

                            $db->query("insert into receipt_result(oid,m_rcash_noappl,m_tid,m_payment_price,m_save_price,m_rcr_price,m_rsup_price,m_rtax,m_rsrvc_price,m_ruseopt,regdate)
									values('".$oid[$j]."','".$RECEIPT_RESULT["m_rcash_noappl"]."','".$RECEIPT_RESULT["m_tid"]."','".$RECEIPT_RESULT["m_payment_price"]."','".$payment[saveprice]."','".$payment[pgprice]."','".$RECEIPT_RESULT["m_rsup_price"]."','".$RECEIPT_RESULT["m_rtax"]."','".$RECEIPT_RESULT["m_rsrvc_price"]."','".$RECEIPT_RESULT["m_useopt"]."',NOW())");

                        }elseif( $RECEIPT_RESULT["result"] == "E" ){
                            set_order_status($oid[$j],"IC","현금영수증 발급 실패[".$RECEIPT_RESULT["result_msg"]."]","시스템","");
                        }
                    }

                    if($order[status] == ORDER_STATUS_INCOM_READY){
                        if($order_details[0][order_from] == 'self'){
                            $mail_info[mem_name] = $order[bname];
                            $mail_info[mem_mail] = $order[bmail];
                            $mail_info[mem_id] = $order[bname];
                            $mail_info[mem_mobile] = $order[bmobile];
                            sendMessageByStep('payment_bank_apply', $mail_info);
                        }
                    }
                }

            }elseif($pre_type==ORDER_STATUS_DELIVERY_READY||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY||
                $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){//배송준비중,(WMS)출고예정,(WMS)포장대기,(WMS)출고대기 리스트에서 넘어왔을때
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();

                $update_str="";
                if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                    $update_str .= " , delivery_status = '', quick = '', invoice_no = '' ";
                }

                for($i=0;$i < count($order_details);$i++){

                    if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                        //기본출고 창고로 이동했던걸 다시 본래 보관장소로 재고 이동!

                        $results = inventory_warehouse_move($order_details[$i]["gu_ix"],$order_details[$i]["pcnt"],$order_details[$i]["delivery_basic_ps_ix"],$order_details[$i]["delivery_ps_ix"],$_SESSION["admininfo"]["charger_ix"],$_SESSION["admininfo"]["charger"],"return",$order_details[$i]["oid"]);

                        if($results!="Y"){
                            echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('창고이동에 문제가 발생했습니다. 계속적으로 발생시 관리자에게 문의 바랍니다..');parent.document.location.reload();</script>");
                            exit;
                        }
                    }

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_INCOM_COMPLETE."' , update_date = NOW() $update_str $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);
                }
            }
        }elseif($status == ORDER_STATUS_DELIVERY_COMPLETE){ // 출고완료 --> 배송완료 처리
            if($pre_type==ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE||$pre_type==ORDER_STATUS_DELIVERY_ING||$pre_type=="order_edit"){ //(WMS)출고완료,배송중상품리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select od.*,o.user_code from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and od.od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_COMPLETE."' , dc_date=NOW() , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    //적립 대기 -> 적립 완료
                    InsertReserveInfo($order_details[$i][user_code],$order_details[$i][oid],$order_details[$i][od_ix],$id,$reserve,'1','1',$etc,'mileage',$_SESSION["admininfo"]);

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

                        $message = $order_details[$i]['pname']." 구매시 적립금액";

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
                }
            }elseif($pre_type==ORDER_STATUS_EXCHANGE_APPLY ||$pre_type==ORDER_STATUS_RETURN_APPLY){//교환리스트,반품리스트에서 넘어왔을때(마일리지 X, dc_date X)
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    //2015-11-19 Hong 교환에서 배송완료시 쿠폰 관련 배송 상품에 있는걸 다시 원상보귀시켜 환불금액 정상적으로 노출시기키
                    //2019-12-16 JK 해당 부분으로 인해 주문금액 표기 원주문은 최종 결제 금액에서 쿠폰이 중복할인되는 증상 및 shop_order_detail_discount 에 중복 쿠폰할인 정보로 업데이트 되는 문제로 인해 프로세스 중지 처리
                    //다른 문제가 있는지 지속 관찰 필요
                    /*
                    if($pre_type==ORDER_STATUS_EXCHANGE_APPLY){

                        $sql="select od_ix from shop_order_detail where oid = '".$order_details[$i][oid]."' and claim_delivery_od_ix='".$order_details[$i][od_ix]."' ";
                        $db->query($sql);
                        $db->fetch();

                        $claim_delivery_od_ix = $db->dt['od_ix'];


                        if($claim_delivery_od_ix > 0){
                            $sql="select * from shop_order_detail_discount where oid = '".$order_details[$i][oid]."' and od_ix='".$claim_delivery_od_ix."' and dc_type in ('CP','SCP') ";
                            $db->query($sql);
                            $coupon = $db->fetchall("object");

                            for($z=0;$z<count($coupon);$z++){
                                //기존쿠폰금액 더해주기
                                $sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice+'".$coupon[$z]["dc_price"]."' where od_ix='".$claim_delivery_od_ix."' ";
                                $db->query($sql);

                                $sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice-'".$coupon[$z]["dc_price"]."' where od_ix='".$order_details[$i][od_ix]."' ";
                                $db->query($sql);

                                $sql="update shop_order_detail_discount set od_ix='".$order_details[$i][od_ix]."' where oid = '".$order_details[$i][oid]."' and od_ix='".$claim_delivery_od_ix."' and dc_type ='".$coupon[$z]["dc_type"]."' ";
                                $db->query($sql);
                            }
                        }

                    }
                    */
                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_COMPLETE."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    $sql = "update ".TBL_SHOP_ORDER_DETAIL." set status='".ORDER_STATUS_SETTLE_READY."' where oid = '".$order_details[$i][oid]."' and claim_delivery_od_ix ='".$order_details[$i][od_ix]."' and status='".ORDER_STATUS_EXCHANGE_READY."'";
                    $db->query($sql);
                }
            }
        }elseif($status == ORDER_STATUS_BUY_FINALIZED){ //구매확정
            if($pre_type==ORDER_STATUS_DELIVERY_COMPLETE){ //배송완료상품리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") and status = '".ORDER_STATUS_DELIVERY_COMPLETE."' $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_BUY_FINALIZED."' , update_date = NOW(), bf_date = NOW()  $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    //구매확정시 셀러 판매신용점수 추가
                    //셀러판매신용점수 추가 시작 2014-06-15 이학봉
                    InsertPenaltyInfo('1','3',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'구매확정시 판매신용점수 추가',$_SESSION["admininfo"],'bf');
                    //셀러판매신용점수 추가 끝 2014-06-15 이학봉


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

                    if($reserve_data[mileage_add_setup] == 'C'){
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
                    //끝


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
                    insertProductPoint('1', POINT_USE_STATE_BF, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '구매확정시 상품점수 추가', $_SESSION["admininfo"], 'bf');

                    //[S] 리셀러 정산 입력
                    //include ($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");
                    resellerAccounts($order_details[$i]['od_ix']);
                    //[E] 리셀러 정산 입력
                }

                /*
                $sql="select op.* from ".TBL_SHOP_ORDER_DETAIL." od left join shop_order_payment op on (od.oid=op.oid) where od.od_ix in (".$od_ix_str.") and op.escrow_use='Y' $and_company_id group by od.oid ";
                $db->query($sql);
                $payment = $db->fetchall();
                for($i=0;$i < count($payment);$i++){
                    ///////////////////////////////////////에스크로//////////////////////////////////////승인


                }
                */
            }elseif($pre_type == 'sos_product'){
                

                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }


                $sql = "select od.*,o.user_code from " . TBL_SHOP_ORDER . " o  left join " . TBL_SHOP_ORDER_DETAIL . " od on o.oid = od.oid where od.od_ix in (".$od_ix_str.") and od.status = '".ORDER_STATUS_INCOM_COMPLETE."' $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();

                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_BUY_FINALIZED."' , update_date = NOW(), bf_date = NOW()  $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    //구매확정시 셀러 판매신용점수 추가
                    //셀러판매신용점수 추가 시작 2014-06-15 이학봉
                    InsertPenaltyInfo('1','3',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'구매확정시 판매신용점수 추가',$_SESSION["admininfo"],'bf');
                    //셀러판매신용점수 추가 끝 2014-06-15 이학봉


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

                    if($reserve_data[mileage_add_setup] == 'C'){
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
                    //끝

                    insertProductPoint('1', POINT_USE_STATE_BF, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '구매확정시 상품점수 추가', $_SESSION["admininfo"], 'bf');

                    //[S] 리셀러 정산 입력
                    //include ($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");
                    resellerAccounts($order_details[$i]['od_ix']);
                    //[E] 리셀러 정산 입력

                    //sos 상품의 경우 구매 확정시 재고 차감 필요
                    UpdateProductCnt_complete($order_details[$i]);
                    //판매진행 재고 조정 처리
                    UpdateSellingCnt($order_details[$i]);
                }
            }
        }elseif($status == ORDER_STATUS_EXCHANGE_DENY){ //교환거부
            if($pre_type==ORDER_STATUS_EXCHANGE_APPLY){ //교환리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_EXCHANGE_DENY."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    $STATUS_MESSAGE= "[".fetch_order_status_div('EY','EY',"title",$reason_code)."]".$msg;
                    set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);
                }
            }
        }elseif($status == ORDER_STATUS_EXCHANGE_DEFER){ //교환보류
            if($pre_type==ORDER_STATUS_EXCHANGE_APPLY||$pre_type==ORDER_STATUS_EXCHANGE_ING){ //교환리스트,교환미처리리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_EXCHANGE_DEFER."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    $STATUS_MESSAGE= "[".fetch_order_status_div('EF','EF',"title",$reason_code)."]".$msg;
                    set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);
                }
            }
        }elseif($status == ORDER_STATUS_EXCHANGE_IMPOSSIBLE){ //교환불가
            if($pre_type==ORDER_STATUS_EXCHANGE_APPLY||$pre_type==ORDER_STATUS_EXCHANGE_ING){ //교환리스트,교환미처리리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_EXCHANGE_IMPOSSIBLE."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    $STATUS_MESSAGE= "[".fetch_order_status_div('EM','EM',"title",$reason_code)."]".$msg;
                    set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);
                }
            }
        }elseif($status == ORDER_STATUS_EXCHANGE_ING){ //교환승인
            if($pre_type==ORDER_STATUS_EXCHANGE_APPLY){ //교환리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_EXCHANGE_ING."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    //제휴사 주문 상태 연동
                    if(function_exists('sellerToolUpdateOrderStatus')){
                        sellerToolUpdateOrderStatus(ORDER_STATUS_EXCHANGE_ING,$order_details[$i][od_ix]);
                    }
                }
            }
        }elseif($status == ORDER_STATUS_EXCHANGE_DELIVERY){ //교환상품배송중
            if($pre_type==ORDER_STATUS_EXCHANGE_APPLY){ //교환리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_EXCHANGE_DELIVERY."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);
                }
            }
        }elseif($status == ORDER_STATUS_EXCHANGE_ACCEPT){ //교환회수완료
            if($pre_type==ORDER_STATUS_EXCHANGE_APPLY){ //교환리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $update_str="";

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_EXCHANGE_ACCEPT."' , return_product_state = '".$return_product_state."', update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    if($_SESSION["layout_config"]["mall_use_inventory"] == "Y" && $order_details[$i][stock_use_yn] == "Y"){

//                        if($regist_pi_ix!=""){
                            $sql = "select pi.pi_ix, pi.company_id, pi.place_name, ps.ps_ix, ps.section_name
									from inventory_goods g
									left join inventory_goods_unit gu on g.gid =gu.gid
									left join inventory_place_info pi on pi.pi_ix = '1'
									left join inventory_place_section ps on ps.pi_ix = pi.pi_ix and ps.ps_ix = '1'
									where gu.gu_ix = '".$order_details[$i][gu_ix]."' ";

                            $db->query($sql);

                            if($db->total > 0){

                                $db->fetch();
                                $order_item_info = $db->dt;

                                $sql = "select g.gid, gu.unit, g.standard,
								'".(!empty($detailCnt) ? $detailCnt : $order_details[$i][pcnt])."' as amount ,
								'".$order_details[$i][psprice]."' as price ,
								'".$order_details[$i][pt_dcprice]."' as pt_dcprice ,
								'".$order_item_info[company_id]."' as company_id,
								'".$order_item_info[pi_ix]."' as pi_ix,
								'".$order_item_info[ps_ix]."' as ps_ix
								from inventory_goods g , inventory_goods_unit gu
								where g.gid = gu.gid and gu.gu_ix = '".$order_details[$i][gu_ix]."'";
                                // 출고가격을 어떻게 처리 할지?
                                // 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ...
                                $db->query($sql);
                                $delivery_iteminfo = $db->fetchall();

                                $item_info[pi_ix] = $order_item_info[pi_ix];
                                $item_info[ps_ix] = $order_item_info[ps_ix];
                                $item_info[company_id] = $order_item_info[company_id];
                                $item_info[h_div] = "1"; // 1:입고 2: 출고
                                $item_info[vdate] = date("Ymd");
                                $item_info[ioid] = "1".substr(date("YmdHis"),1)."-".rand(10000, 99999);
                                $item_info[oid] = $order_details[$i][oid];
                                $item_info[msg] = "교환회수완료 - 입고".($msg ? " [".$msg."]" : "");//$_POST["etc"];
                                $item_info[h_type] = '05';//01; 상품매출 04:반품, 05:교환
                                $item_info[charger_name] = $_SESSION[admininfo]["charger"];
                                $item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
                                $item_info[detail] = $delivery_iteminfo;

                                //UpdateGoodsItemStockInfo($item_info, $db);
								UpdateGoodsItemNoStockInfo($item_info, $db);
                            }
//                        }
                    }

                    //UpdateProductCnt_cancel($order_details[$i]);
                }
            }
        }elseif($status == ORDER_STATUS_EXCHANGE_COMPLETE){ //교환확정
            if($pre_type==ORDER_STATUS_EXCHANGE_APPLY||$pre_type==ORDER_STATUS_EXCHANGE_ING){  //교환리스트,교환미처리리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="select * from shop_order_payment where oid='".$order_details[$i][oid]."' and pay_type ='A' and claim_group='".$order_details[$i][claim_group]."' ";
                    $db->query($sql);
                    if($db->total){
                        $sql="update shop_order_claim_delivery set ac_target_yn = 'Y' where oid='".$order_details[$i][oid]."' and claim_group='".$order_details[$i][claim_group]."' $and_company_id";
                        $db->query($sql);
                    }

                    //교환완료시 환불대기인 교환은 환불요청으로~
                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_EXCHANGE_COMPLETE."' , update_date = NOW(),
					refund_status = (case when refund_status='".ORDER_STATUS_REFUND_READY."' then '".ORDER_STATUS_REFUND_APPLY."' else '' end),
					fa_date = (case when refund_status='".ORDER_STATUS_REFUND_READY."' then NOW() else NULL end)
					$am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    if($order_details[$i][claim_fault_type] == 'S'){
                        //교환승인시 셀러판매신용점수 차감
                        //셀러판매신용점수 추가 시작 2014-06-15 이학봉
                        InsertPenaltyInfo('2','5',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'교환확정 판매신용점수 차감',$_SESSION["admininfo"],'ec');
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
                        insertProductPoint('2', POINT_USE_STATE_EC, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '교환확정 상품점수 차감', $_SESSION["admininfo"], 'ec');
                    }

                    //*******교환 프로세스 정한후 처리하기! **********
                    /*
                    $pre_order_info="교환확정-배송준비중처리 [배송업체:".deliveryCompanyList($order_details[$i][quick],"text")."|송장번호:".$order_details[$i][invoice_no]."|배송준비일:".$order_details[$i][di_date]."|배송완료일:".$order_details[$i][dc_date]."]";

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_READY."' , update_date = NOW(), delivery_status='', quick='', invoice_no='', di_date=NULL, dc_date=NULL $am_update_str where  od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    //echo $sql."<br><br>";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    $wmsLotteInterface->deliveryReady($order_details[$i][od_ix]); //롯데택배 WMS 출고예정 테이블 삽입.

                    $sql = "insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, pid, status, status_message,admin_message, company_id,quick,invoice_no, regdate )
                            values
                            ('','".$order_details[$i][oid]."','".$order_details[$i][pid]."','".ORDER_STATUS_DELIVERY_READY."','".$pre_order_info."','".$admininfo[charger]."(".$admininfo[charger_id].")','".$admininfo[company_id]."','$quick','$deliverycode',NOW())";
                    //echo $sql."<br><br>";
                    $db->sequences = "SHOP_ORDER_STATUS_SEQ";
                    $db->query($sql);

                    */
                    //[End] 교환확정으로 변경되면 배송준비중으로 변경한다 with 박과장님 kbk 13/08/06

                }
            }
        }elseif($status == ORDER_STATUS_RETURN_DENY){ //반품거부
            if($pre_type==ORDER_STATUS_RETURN_APPLY){ //반품리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_RETURN_DENY."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    $STATUS_MESSAGE = "[".fetch_order_status_div('RY','RY',"title",$reason_code)."]".$msg;
                    set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);
                }
            }
        }elseif($status == ORDER_STATUS_RETURN_DEFER){ //반품보류
            if($pre_type==ORDER_STATUS_RETURN_APPLY||$pre_type==ORDER_STATUS_RETURN_ING){ //반품리스트,반품미처리리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_RETURN_DEFER."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    $STATUS_MESSAGE = "[".fetch_order_status_div('RF','RF',"title",$reason_code)."]".$msg;
                    set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);
                }
            }
        }elseif($status == ORDER_STATUS_RETURN_IMPOSSIBLE){ //반품불가
            if($pre_type==ORDER_STATUS_RETURN_APPLY||$pre_type==ORDER_STATUS_RETURN_ING){ //반품리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_RETURN_IMPOSSIBLE."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    $STATUS_MESSAGE = "[".fetch_order_status_div('RM','RM',"title",$reason_code)."]".$msg;
                    set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],$reason_code);
                }
            }
        }elseif($status == ORDER_STATUS_RETURN_ING){ //반품승인
            if($pre_type==ORDER_STATUS_RETURN_APPLY){ //반품리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_RETURN_ING."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    //제휴사 주문 상태 연동
                    if(function_exists('sellerToolUpdateOrderStatus')){
                        sellerToolUpdateOrderStatus(ORDER_STATUS_RETURN_ING,$order_details[$i][od_ix]);
                    }
                }
            }
        }elseif($status == ORDER_STATUS_RETURN_DELIVERY){ //반품상품배송중
            if($pre_type==ORDER_STATUS_RETURN_APPLY){ //반품리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_RETURN_DELIVERY."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);
                }
            }
        }elseif($status == ORDER_STATUS_RETURN_ACCEPT){ //반품회수완료
            if($pre_type==ORDER_STATUS_RETURN_APPLY){ //반품리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $and_company_id";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_RETURN_ACCEPT."' , return_product_state = '".$return_product_state."' ,  update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    if($_SESSION["layout_config"]["mall_use_inventory"] == "Y" && $order_details[$i][stock_use_yn] == "Y"){
//                        if($regist_pi_ix!=""){
                            $sql = "select pi.pi_ix, pi.company_id, pi.place_name, ps.ps_ix, ps.section_name
									from inventory_goods g
									left join inventory_goods_unit gu on g.gid =gu.gid
									left join inventory_place_info pi on pi.pi_ix = '1'
									left join inventory_place_section ps on ps.pi_ix = pi.pi_ix and ps.ps_ix = '1'
									where gu.gu_ix = '".$order_details[$i][gu_ix]."' ";

                            $db->query($sql);

                            if($db->total > 0){

                                $db->fetch();
                                $order_item_info = $db->dt;

                                $sql = "select g.gid, gu.unit, g.standard,
								'".(!empty($detailCnt) ? $detailCnt : $order_details[$i][pcnt])."' as amount ,
								'".$order_details[$i][psprice]."' as price ,
								'".$order_details[$i][pt_dcprice]."' as pt_dcprice ,
								'".$order_item_info[company_id]."' as company_id,
								'".$order_item_info[pi_ix]."' as pi_ix,
								'".$order_item_info[ps_ix]."' as ps_ix
								from inventory_goods g , inventory_goods_unit gu
								where g.gid = gu.gid and gu.gu_ix = '".$order_details[$i][gu_ix]."'";
                                // 출고가격을 어떻게 처리 할지?
                                // 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ...
                                $db->query($sql);
                                $delivery_iteminfo = $db->fetchall();

                                $item_info[pi_ix] = $order_item_info[pi_ix];
                                $item_info[ps_ix] = $order_item_info[ps_ix];
                                $item_info[company_id] = $order_item_info[company_id];
                                $item_info[h_div] = "1"; // 1:입고 2: 출고
                                $item_info[vdate] = date("Ymd");
                                $item_info[ioid] = "1".substr(date("YmdHis"),1)."-".rand(10000, 99999);
                                $item_info[oid] = $order_details[$i][oid];
                                $item_info[msg] = "반품회수완료 - 입고".($msg ? " [".$msg."]" : "");//$_POST["etc"];
                                $item_info[h_type] = '05';//01; 상품매출 04:반품, 05:교환
                                $item_info[charger_name] = $_SESSION[admininfo]["charger"];
                                $item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
                                $item_info[detail] = $delivery_iteminfo;

                                //UpdateGoodsItemStockInfo($item_info, $db);
								UpdateGoodsItemNoStockInfo($item_info, $db);
                            }
//                        }
                    }

                    //UpdateProductCnt_cancel($order_details[$i]);
                }
            }
        }elseif($status == ORDER_STATUS_RETURN_COMPLETE){ //반품확정
            if($pre_type==ORDER_STATUS_EXCHANGE_APPLY||$pre_type==ORDER_STATUS_EXCHANGE_ING||$pre_type==ORDER_STATUS_RETURN_APPLY||$pre_type==ORDER_STATUS_RETURN_ING){ //교환리스트,교환미처리리스트,반품리스트,반품미처리리스트에서 넘어왔을떄
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select od.*,o.status as ostatus,o.user_code from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and od.od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall("object");

                for($i=0;$i < count($order_details);$i++){

                    //2015-11-19 Hong 교환에서 반품시 쿠폰 관련 배송 상품에 있는걸 다시 원상보귀시켜 환불금액 정상적으로 노출시기키
                    //2019-12-19 JK 해당 부분으로 인해 주문금액 표기 원주문은 최종 결제 금액에서 쿠폰이 중복할인되는 증상 및 shop_order_detail_discount 에 중복 쿠폰할인 정보로 업데이트 되는 문제로 인해 프로세스 중지 처리
                    //다른 문제가 있는지 지속 관찰 필요
                    /*
                    if($pre_type==ORDER_STATUS_EXCHANGE_APPLY||$pre_type==ORDER_STATUS_EXCHANGE_ING){

                        $sql="select od_ix from shop_order_detail where oid = '".$order_details[$i][oid]."' and claim_delivery_od_ix='".$order_details[$i][od_ix]."' ";
                        $db->query($sql);
                        $db->fetch();

                        $claim_delivery_od_ix = $db->dt['od_ix'];

                        if($claim_delivery_od_ix > 0){
                            $sql="select * from shop_order_detail_discount where oid = '".$order_details[$i][oid]."' and od_ix='".$claim_delivery_od_ix."' and dc_type in ('CP','SCP') ";
                            $db->query($sql);
                            $coupon = $db->fetchall("object");

                            for($z=0;$z<count($coupon);$z++){
                                //기존쿠폰금액 더해주기
                                $sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice+'".$coupon[$z]["dc_price"]."' where od_ix='".$claim_delivery_od_ix."' ";
                                $db->query($sql);

                                $sql="update ".TBL_SHOP_ORDER_DETAIL." set pt_dcprice = pt_dcprice-'".$coupon[$z]["dc_price"]."' where od_ix='".$order_details[$i][od_ix]."' ";
                                $db->query($sql);

                                $sql="update shop_order_detail_discount set od_ix='".$order_details[$i][od_ix]."' where oid = '".$order_details[$i][oid]."' and od_ix='".$claim_delivery_od_ix."' and dc_type ='".$coupon[$z]["dc_type"]."' ";
                                $db->query($sql);
                            }
                        }
                    }
                    */
                    if($order_details[$i][ostatus]==ORDER_STATUS_DEFERRED_PAYMENT){//외상일때 환불 신청 X
                        $update_str ="";
                    }else{

                        $sql="select * from shop_order_payment where oid='".$order_details[$i][oid]."' and pay_type ='A' and claim_group='".$order_details[$i][claim_group]."' ";
                        $db->query($sql);
                        if($db->total){
                            //2014-08-25 HONG 임시 주석처리
                            //$sql="update shop_order_claim_delivery set ac_target_yn = 'Y' where oid='".$order_details[$i][oid]."' and claim_group='".$order_details[$i][claim_group]."' $and_company_id";
                            //$db->query($sql);
                            $update_str = " , refund_status='".ORDER_STATUS_REFUND_APPLY."'  , fa_date=NOW() ";
                        }else{
                            $update_str = " , refund_status='".ORDER_STATUS_REFUND_APPLY."'  , fa_date=NOW() ";
                        }
                    }

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_RETURN_COMPLETE."' , update_date = NOW() $update_str $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    //적립된 적립금 취소
                    InsertReserveInfo($order_details[$i][user_code],$order_details[$i][oid],$order_details[$i][od_ix],$id,$reserve,'9','3',$etc,'mileage',$_SESSION["admininfo"]);	//마일리지,적립금 통합용 함수 2013-06-19 이학봉

                    if($order_details[$i][claim_fault_type] == 'S'){
                        //반품승인시 셀러판매신용점수 차감
                        //셀러판매신용점수 추가 시작 2014-06-15 이학봉
                        InsertPenaltyInfo('2','6',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'반품확정 판매신용점수 차감',$_SESSION["admininfo"],'rc');
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
                        insertProductPoint('2', POINT_USE_STATE_RC, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '반품확정 상품점수 차감', $_SESSION["admininfo"], 'rc');
                    }

                    /* ERP 2차 연동을 위한 처리값 2013-10-20 이학봉 시작 반품일경우 is_erp_seller 를 N dmfh 수정(연동시 사용)*/
                    $sql = "select
								is_erp_link_return
							from
								".TBL_SHOP_ORDER."
							where
								oid = '".$order_details[$i][oid]."'";
                    $db->query($sql);
                    $db->fetch();
                    $is_erp_link_return = $db->dt[is_erp_link_return];

                    if($is_erp_link_return == "N"){
                        $sql="update ".TBL_SHOP_ORDER." set is_erp_link = 'N' where oid='".$order_details[$i][oid]."'";
                        $db->query($sql);
                    }

                    change_erp_link ($status,$order_details[$i][od_ix]);
                    /* ERP 2차 연동을 위한 처리값 2013-10-20 이학봉 끝*/

                    if(function_exists('reseller_incentive_cancel')){
                        reseller_incentive_cancel(); // 해당 함수는 어디에도 생성되어있지 않음.. 왜 여기 들어가있는지 이유를 모르겠슴 그래서 함수 체크하여 일단 대기 중 필요 없으면 지우기 바람 JK0160520
                    }

                    //쿠폰 돌려주기!!!
					if($coupon_data[restore_bf] == "Y"){
						$UseCoupon["oid"]=$order_details[$i][oid];
						$UseCoupon["od_ix"]=$order_details[$i][od_ix];
						$returnCoupon = orderUseCouponReturn($UseCoupon);
					}

                    if(substr($order_details[$i][status],0,1)=='E'){//교환에서 반품으로 넘어올땐 교환배송예정 상품을 UPDATE 하기!
                        $sql = "update ".TBL_SHOP_ORDER_DETAIL." set status='".ORDER_STATUS_SETTLE_READY."' where oid = '".$order_details[$i][oid]."' and claim_delivery_od_ix ='".$order_details[$i][od_ix]."' and status='".ORDER_STATUS_EXCHANGE_READY."'";
                        $db->query($sql);
                    }

                    //제휴사 주문 상태 연동
                    if(function_exists('sellerToolUpdateOrderStatus')){
                        sellerToolUpdateOrderStatus(ORDER_STATUS_RETURN_COMPLETE,$order_details[$i][od_ix]);
                    }
                }
            }

        }else if($status_str == 'level4_status' && $status == DEMANDSHIP){ //주문내역 디멘드쉽 전송

            $RESULT_CHECK == false;
            $RESULT_FAIL == 0;
            $result_msg = "";
            $result_code = "";
            $t_count = 0;
            $s_count = 0;
            $f_count = 0;

            $OAL = new OpenAPI('demandship');
            #$OAL->lib->set_error_type("return");

            $odIxArray = $_POST['od_ix'];

            //echo("<script src='/admin/js/jquery-1.8.3.js'></script>");
            #$addinfo = $_POST['add_info'];

            $sql = "select demandship_service_key, company_id
					  from common_seller_delivery
					 where company_id = '". $_SESSION['admininfo']['company_id'] ."' limit 1";
            $db->query($sql);
            $sellkey = $db->fetch();

            if(empty($sellkey['demandship_service_key']) && count($sellkey['company_id']) > 0){
                echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('토큰이 발급되지 않았습니다. 배송정책 매뉴에서 토큰을 발급하신 후 시도해주세요.');parent.document.location.reload();</script>");
            }else{
                $result = $OAL->lib->registShipments($odIxArray);
                //exit;

                $result_msg .= " [".$result->message."] ";
                $result_code .= " [".$result->resultCode."] ";

                $t_count++;
                //print_r($result->resultCode['status']);
                //exit;
                if($result->resultCode=="success" || $result->resultCode=="200"){
                    $RESULT_SUCCESS++;
                    $s_count++;
                }else{
                    $f_count++;
                    $RESULT_CHECK = true;
                    $RESULT_FAIL++;
                }

                //print_r($result);
                //echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('토큰이 발급되지 않았습니다. 배송정책 매뉴에서 토큰을 발급하신 후 시도해주세요.');parent.document.location.reload();</script>");
                //echo("<script>$('#select_update_loadingbar td',parent.document).html('<img src=\"/admin/images/indicator.gif\" border=\"0\" width=\"32\" height=\"32\" align=\"absmiddle\"> ".$result->message."');</script>");
            }
            //exit;

            if($pre_type==ORDER_UNRECEIVED_CLAIM){ //미수령 신고 상태에서 넘어왔을때
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else					$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();

                $update_str="";
                $claim_str="";
                //$delivery_method=$help_delivery_method;
                $delivery_company=$help_quick;
                $deliverycode="";

                for($i=0;$i < count($help_deliverycode);$i++){
                    if(trim($help_deliverycode[$i])) $deliverycode .= ",".trim($help_deliverycode[$i]);
                }

                //if($delivery_method!="")									$update_str .= " , delivery_method= '".$delivery_method."' ";
                if($delivery_company!="")			$update_str .= " , quick= '".$delivery_company."' ";
                if($deliverycode!="")				$update_str .= " , invoice_no='".substr($deliverycode,1)."' ";

                if($uc_message!="")				$claim_str .= " , return_message='".$uc_message."' ";
                if($status!="")				$claim_str .= " , claim_status='".$status."' ";

                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set update_date = NOW() $update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $sql = "update shop_order_unreceived_claim set update_date = NOW() $update_str $claim_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    if($order_details[$i][delivery_status]){
                        $tmp_status=$order_details[$i][delivery_status];
                    }else{
                        $tmp_status=$order_details[$i][status];
                    }

                    $msg="미수령 신고 접수 철회";

                    set_order_status($order_details[$i][oid],/*$tmp_status*/'',$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"",$delivery_company,substr($deliverycode,1));
                }

                echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('미수령 신고 접수가 철회 되었습니다.');parent.document.location.reload();</script>");
                exit;
            }

        }else if($status == ORDER_UNRECEIVED_CLAIM_COMPLETE){ //미수령 신고철회
            if($pre_type==ORDER_UNRECEIVED_CLAIM){ //미수령 신고 상태에서 넘어왔을때
                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else					$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();

                $update_str="";
                $claim_str="";
                //$delivery_method=$help_delivery_method;
                $delivery_company=$help_quick;
                $deliverycode="";

                for($i=0;$i < count($help_deliverycode);$i++){
                    if(trim($help_deliverycode[$i])) $deliverycode .= ",".trim($help_deliverycode[$i]);
                }

                //if($delivery_method!="")									$update_str .= " , delivery_method= '".$delivery_method."' ";
                if($delivery_company!="")			$update_str .= " , quick= '".$delivery_company."' ";
                if($deliverycode!="")				$update_str .= " , invoice_no='".substr($deliverycode,1)."' ";

                if($uc_message!="")				$claim_str .= " , return_message='".$uc_message."' ";
                if($status!="")				$claim_str .= " , claim_status='".$status."' ";

                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set update_date = NOW() $update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $sql = "update shop_order_unreceived_claim set update_date = NOW() $update_str $claim_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    if($order_details[$i][delivery_status]){
                        $tmp_status=$order_details[$i][delivery_status];
                    }else{
                        $tmp_status=$order_details[$i][status];
                    }

                    $msg="미수령 신고 접수 철회";

                    set_order_status($order_details[$i][oid],/*$tmp_status*/'',$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"",$delivery_company,substr($deliverycode,1));
                }

                echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('미수령 신고 접수가 철회 되었습니다.');parent.document.location.reload();</script>");
                exit;
            }

        }
        /************* STATUS 변경 END*************/

    }elseif($update_type==1){//검색한 주문
        //기능구현 나중에!
    }


    if($RESULT_CHECK){
        if($RESULT_SUCCESS > 0 && $RESULT_FAIL > 0){
            echo("<script>alert('".number_format($RESULT_FAIL)."건 이 상태변경에 실패하였습니다.');</script>");
        }else if($RESULT_FAIL == 0){
            echo("<script>alert('정상적으로 처리 되었습니다.');</script>");
        }else{
            echo("<script>alert('상태변경에 실패하였습니다.');</script>");
        }
    }else{
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상태 변경이 정상적으로  처리 되었습니다.');</script>");
    }

    echo("<script>parent.document.location.reload();</script>");
    exit;

}



if($act == "select_delivery_status_update"){		//리스트페이지에서 출고상태값 변경시

    if($update_kind){
        $status_str=$update_kind."_status";
        $status=$$status_str;

        $delivery_status_str=$update_kind."_delivery_status";
        $delivery_status=$$delivery_status_str;

        $reason_code_str=$update_kind."_reason_code";
        $reason_code=$$reason_code_str;

        $msg_str=$update_kind."_msg";
        $msg=$$msg_str;

        //$sub_delivery_method=$delivery_method;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
        $sub_quick=$quick;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
        $sub_deliverycode=$deliverycode;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
        //$delivery_method="";
        $quick="";
        $deliverycode="";

    }

    if($update_type==2){ //선택한 주문

        /************* DELIVERY_STATUS 변경 START*************/
        if($delivery_status == ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){//출고요청
            if($pre_type==ORDER_STATUS_DELIVERY_READY || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){ //배송준비중,(WMS)출고요청확정,(WMS)포장대기,(WMS)출고대기 넘어왔을떄

                if(is_array($od_ix)){
                    $od_ix_str="";

                    for($j=0;$j < count($od_ix);$j++){
                        if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                        else						$od_ix_str .= ",'".$od_ix[$j]."'";
                    }
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();

                for($i=0;$i < count($order_details);$i++){

                    if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){
                        //기본출고 창고로 이동했던걸 다시 본래 보관장소로 재고 이동!

                        $results = inventory_warehouse_move($order_details[$i]["gu_ix"],$order_details[$i]["pcnt"],$order_details[$i]["delivery_basic_ps_ix"],$order_details[$i]["delivery_ps_ix"],$_SESSION["admininfo"]["charger_ix"],$_SESSION["admininfo"]["charger"],"return",$order_details[$i]["oid"]);

                        if($results!="Y"){
                            echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('창고이동에 문제가 발생했습니다. 계속적으로 발생시 관리자에게 문의 바랍니다..');parent.document.location.reload();</script>");
                            exit;
                        }
                    }

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_READY."' , delivery_status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."'  ,update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$delivery_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);
                }
            }
        }elseif($delivery_status == ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM){//출고요청확정
            if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){ //(WMS)출고요청 리스트에서 넘어왔을떄

                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();

                for($i=0;$i < count($order_details);$i++){

                    $update_str = "";

                    if($sub_delivery_info[$order_details[$i][od_ix]]!=""){
                        list($delivery_company_id,$delivery_pi_ix,$delivery_ps_ix) = explode("|",$sub_delivery_info[$order_details[$i][od_ix]]);

                        if($delivery_company_id!="")								$update_str .= " , delivery_company_id= '".$delivery_company_id."' ";
                        if($delivery_pi_ix!="")										$update_str .= " , delivery_pi_ix= '".$delivery_pi_ix."' ";
                        if($delivery_ps_ix!="")										$update_str .= " , delivery_ps_ix= '".$delivery_ps_ix."' ";

                        $sql="select ps.ps_ix from inventory_place_section ps where ps.section_type='D' and ps.pi_ix='".$delivery_pi_ix."'   ";
                        $db->query($sql);
                        $db->fetch();
                        $delivery_basic_ps_ix = $db->dt[ps_ix];

                        $update_str .= " , delivery_basic_ps_ix= '".$delivery_basic_ps_ix."' ";
                    }

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_READY."' , delivery_status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM."' , update_date = NOW() $update_str $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$delivery_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"",$delivery_company,$deliverycode);
                }
            }
        }elseif($delivery_status == ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING){//포장대기
            if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_CONFIRM||$pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY){ //(WMS)출고요청,(WMS)출고요청확정,(WMS)출고대기 리스트에서 넘어왔을떄

                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();

                for($i=0;$i < count($order_details);$i++){

                    $update_str = "";

                    if($sub_delivery_info[$order_details[$i][od_ix]]!=""){
                        list($delivery_company_id,$delivery_pi_ix,$delivery_ps_ix) = explode("|",$sub_delivery_info[$order_details[$i][od_ix]]);

                        if($delivery_company_id!="")								$update_str .= " , delivery_company_id= '".$delivery_company_id."' ";
                        if($delivery_pi_ix!="")										$update_str .= " , delivery_pi_ix= '".$delivery_pi_ix."' ";
                        if($delivery_ps_ix!="")										$update_str .= " , delivery_ps_ix= '".$delivery_ps_ix."' ";

                        $sql="select ps.ps_ix from inventory_place_section ps where ps.section_type='D' and ps.pi_ix='".$delivery_pi_ix."'";
                        $db->query($sql);
                        $db->fetch();
                        $delivery_basic_ps_ix = $db->dt[ps_ix];

                        $update_str .= " , delivery_basic_ps_ix= '".$delivery_basic_ps_ix."' ";

                    }else{
                        $delivery_basic_ps_ix=$order_details[$i]["delivery_basic_ps_ix"];
                        $delivery_ps_ix=$order_details[$i]["delivery_ps_ix"];
                    }

                    //기본출고 창고로 재고 이동!
                    /*
                    gu_ix  : 단위코드
                    pcnt : 주문수량
                    delivery_ps_ix : 현재 보관장소
                    delivery_basic_ps_ix : 보내야할 보관장소

                    */
                    $results = inventory_warehouse_move($order_details[$i]["gu_ix"],$order_details[$i]["pcnt"],$delivery_ps_ix,$delivery_basic_ps_ix,$_SESSION["admininfo"]["charger_ix"],$_SESSION["admininfo"]["charger"],"return",$order_details[$i]["oid"]);

                    if($results=="Y"){

                        //$delivery_method=$sub_delivery_method[$order_details[$i][od_ix]];
                        $delivery_company=$sub_quick[$order_details[$i][od_ix]];
                        $deliverycode=$sub_deliverycode[$order_details[$i][od_ix]];

                        //if($delivery_method!="")									$update_str .= " , delivery_method= '".$delivery_method."' ";
                        if($delivery_company!="")									$update_str .= " , quick= '".$delivery_company."' ";
                        if($deliverycode!="")										$update_str .= " , invoice_no= '".$deliverycode."' ";

                        $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_READY."' , delivery_status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING."' , update_date = NOW() $update_str $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                        $db->query($sql);

                        set_order_status($order_details[$i][oid],$delivery_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"",$delivery_company,$deliverycode);
                    }else{
                        echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('창고이동에 문제가 발생했습니다. 계속적으로 발생시 관리자에게 문의 바랍니다..');parent.document.location.reload();</script>");
                        exit;
                    }
                }
            }
        }elseif($delivery_status == ORDER_STATUS_WAREHOUSE_DELIVERY_READY){//출고대기
            if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type == ORDER_STATUS_DELIVERY_ING){ //(WMS)포장대기, 배송중리스트

                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else					$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();

                for($i=0;$i < count($order_details);$i++){

                    $update_str = "";

                    if($delete_invoice_yn=="Y"){
                        $update_str .= " , quick= '' ,invoice_no= '' ";//delivery_method= '' ,
                    }else{
                        if($delete_invoice_yn !="N"){
                            //$delivery_method=$sub_delivery_method[$order_details[$i][od_ix]];
                            $delivery_company=$sub_quick[$order_details[$i][od_ix]];
                            $deliverycode=$sub_deliverycode[$order_details[$i][od_ix]];

                            //if($delivery_method!="")									$update_str .= " , delivery_method= '".$delivery_method."' ";
                            if($delivery_company!="")									$update_str .= " , quick= '".$delivery_company."' ";
                            if($deliverycode!="")										$update_str .= " , invoice_no= '".$deliverycode."' ";
                        }
                    }

                    if($pre_type == ORDER_STATUS_DELIVERY_ING){
                        if($order_details[$i][delivery_status]!=""){
                            $update_str .= " , delivery_status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."'  ";
                            //재고 출고취소
                            inventory_cancel_output_stock($order_details[$i]);
                        }
                    }else{
                        $update_str .= " , delivery_status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."'  ";
                    }


                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_READY."' ,update_date = NOW() $update_str $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    if($delete_invoice_yn=="Y")									$msg = "일괄출고대기(송장번호삭제)";

                    set_order_status($order_details[$i][oid],$delivery_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"",$delivery_company,$deliverycode);
                }
            }
        }elseif($delivery_status == 'WDACC'){//출고요청취소
            if($pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY){ //(WMS)출고요청

                $od_ix_str="";

                for($j=0;$j < count($od_ix);$j++){
                    if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                    else						$od_ix_str .= ",'".$od_ix[$j]."'";
                }

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
                $db->query($sql);
                $order_details = $db->fetchall();

                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set delivery_status = ''  ,update_date = NOW() where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$delivery_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);
                }
            }
        }elseif($delivery_status == 'CUSTOMIZING'){//DPS 연동
            //포장대기 프로세스와 비슷함!

            $ORDER_FROM_ARRAY["self"]="자체쇼핑몰";
            $ORDER_FROM_ARRAY["offline"]="오프라인 영업";
            $ORDER_FROM_ARRAY["pos"]="POS";

            $db->query("select site_name,site_code from sellertool_site_info ");
            if($db->total){
                $sellertool=$db->fetchall("object");
                foreach($sellertool as $st){
                    $ORDER_FROM_ARRAY[$st["site_code"]]=$st["site_name"];
                }
            }

            $od_ix_str="";

            for($j=0;$j < count($od_ix);$j++){
                if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
                else						$od_ix_str .= ",'".$od_ix[$j]."'";
            }

            $dps_db = new Database;
            $dps_db->set_change_db("daiso_dps");
            $date = date('Y-m-d');
            $sql = "select max(send_count) as send_count from shop_dps_info where regdate between '".$date." 00:00:00' and '".$date." 23:59:59'";
            $dps_db->query($sql);
            $dps_db->fetch();
            $send_count = ($dps_db->dt[send_count] + 1);

            $sql="select 
					o.*,
					od.*,
					odd.*,
					g.cid,g.gname, g.gcode, g.admin, pi.company_id, gu.unit,g.gid, g.standard,
					(select ifnull(sum(ipss.stock),0) as stock from inventory_product_stockinfo as ipss where ipss.gid = g.gid and ipss.unit = gu.unit and ipss.company_id = od.delivery_company_id and ipss.pi_ix = od.delivery_pi_ix and ipss.ps_ix = od.delivery_ps_ix) as stock,
					ps.section_name,ps.ps_ix,od.od_ix
				from 
					shop_order as o 
					inner join shop_order_detail as od on (o.oid = od.oid)
					inner join shop_order_detail_deliveryinfo as odd on (od.odd_ix = odd.odd_ix)
					left join inventory_goods_unit as gu on (od.gu_ix = gu.gu_ix)
					left join inventory_goods as g on (gu.gid = g.gid)
					left join inventory_product_stockinfo ips on (ips.gid=g.gid and ips.unit=gu.unit and ips.company_id=od.delivery_company_id and ips.pi_ix=od.delivery_pi_ix and ips.ps_ix=od.delivery_ps_ix)
					left join common_company_detail ccd on (ccd.company_id = ips.company_id)
					left join inventory_place_info pi on (pi.pi_ix = ips.pi_ix)
					left join inventory_place_section ps on (ps.ps_ix = ips.ps_ix)
				where 
					od.od_ix in (".$od_ix_str.")";

            $db = new Database;
            $db->query($sql);
            $order_details = $db->fetchall();

            for($i=0;$i < count($order_details);$i++){

                if($order_details[$i][gu_ix] != ""){
                    $db = new Database;

                    $order_from = $ORDER_FROM_ARRAY[$order_details[$i][order_from]];

                    $sql = "select pi.company_id, ps.pi_ix, ps.ps_ix from inventory_place_info pi , inventory_place_section ps where pi.pi_ix = ps.pi_ix and ps.ps_ix = '".$order_details[$i][delivery_ps_ix]."' ";
                    $db->query($sql);
                    $db->fetch();
                    $pi_ix = $db->dt[pi_ix];
                    $now_company_id = $db->dt[company_id];
                    $now_pi_ix = $db->dt[pi_ix];
                    $now_ps_ix = $db->dt[ps_ix];

                    $sql = "select pi.company_id, ps.pi_ix, ps.ps_ix from inventory_place_info pi , inventory_place_section ps where pi.pi_ix = ps.pi_ix and ps.ps_ix = '".$order_details[$i][delivery_basic_ps_ix]."' ";
                    $db->query($sql);
                    $db->fetch();
                    $move_company_id = $db->dt[company_id];
                    $move_pi_ix = $db->dt[pi_ix];
                    $move_ps_ix = $db->dt[ps_ix];

                    $param_details[oid] = $order_details[$i][oid]."-".$order_details[$i][ode_ix];							//주문번호
                    $param_details[ori_oid] = $order_details[$i][oid];	//원주문번호
                    $param_details[od_ix] = $order_details[$i][od_ix];					//주문상세
                    $param_details[order_from] = $order_from;	//판매처
                    $param_details[gcode] = $order_details[$i][gcode];							//품목대표코드
                    $param_details[gid] = $order_details[$i][gid];							//품목코드
                    $param_details[unit] = $order_details[$i][unit];							//품목단위코드
                    $param_details[pname] = str_replace("'","",$order_details[$i][pname]);				//상품명
                    $param_details[gname] = str_replace("'","",$order_details[$i][gname]." ".$order_details[$i][standard]);				//품목명 + 품목규격
                    $param_details[standard] = $order_details[$i][standard];			//품목규격
                    $param_details[options] = $order_details[$i][option_text];		//옵션 구분: 옵션명
                    $param_details[stock] = $order_details[$i][stock];						//출고 요청시 총 재고수량
                    $param_details[pcnt] = $order_details[$i][pcnt];						//상품주문수량
                    $param_details[account_price] = '0';																			//정산금액
                    $param_details[delivery_price] = '신용';																		//배송비
                    $param_details[delivery_company] = $order_details[$i][delivery_company];				//배송업체 (DPS용)
                    $param_details[delivery_code] = $order_details[$i][delivery_code];							//배송업체 코드 (DPS용)
                    $param_details[delivery_msg] = str_replace("'","",$order_details[$i][msg]);							//배송업체 코드 (DPS용)
                    $param_details[rname] = $order_details[$i][rname];													//수취인명
                    $param_details[rtel] = $order_details[$i][rtel];															//수취인전화번호
                    $param_details[rname] = str_replace("'","",$order_details[$i][rname]);													//수취인명
                    $param_details[rmobile] = $order_details[$i][rmobile];												//수취인 핸드폰번호
                    $param_details[rzipcode] = $order_details[$i][zip];													//수취인 우편번호
                    $param_details[raddr] = str_replace("'","",$order_details[$i][addr1]." ".$order_details[$i][addr2]);		//수취인 주소
                    $param_details[order_date] = $order_details[$i][order_date];									//주문일자
                    $param_details[da_date] = $order_details[$i][da_date];											//출고요청일
                    $param_details[dc_date] = $order_details[$i][dc_date];											//출고일자
                    $param_details[df_date] = $order_details[$i][df_date];												//출고대응일
                    $param_details[dps_send_date] = $order_details[$i][dps_send_date];					//DPS수신일
                    $param_details[dps_status] = $order_details[$i][dps_status];									//DPS 상태값
                    $param_details[dps_is_use] = $order_details[$i][dps_is_use];									//DPS 상태값 변경
                    $param_details[pid] = $order_details[$i][pid];									//상품시스템코드
                    $param_details[user_code] = $order_details[$i][user_code];						//주문자 회원 코드

                    $param_details[ps_ix] = $order_details[$i][section_name];								//로케이션명(보관장소명)
                    $param_details[now_company_id] = $now_company_id;			//출고보관장소키
                    $param_details[now_pi_ix] = $now_pi_ix;						//출고보관장소키
                    $param_details[now_ps_ix] = $now_ps_ix;						//출고보관장소키

                    $param_details[move_company_id] = $move_company_id;			//출고보관장소키
                    $param_details[move_pi_ix] = $move_pi_ix;					//출고보관장소키
                    $param_details[move_ps_ix] = $move_ps_ix;					//출고보관장소키

                    $param_details[charger] = $admininfo[charger];									//DPS전송 처리 관리자명
                    $param_details[charger_ix] = $admininfo[charger_ix];							//DPS전송 처리 관리자 코드

                    $param_details[dcprice] = $order_details[$i][dcprice];									//DPS전송 처리 관리자명
                    $param_details[pt_dcprice] = $order_details[$i][pt_dcprice];							//DPS전송 처리 관리자 코드

                    InsertDpsDelivery($param_details,$send_count);

                    $db = new Database;
                    $sql = "update shop_order_detail set dps_status = 'D".$send_count."' where od_ix = '".$order_details[$i][od_ix]."' and dps_status = ''";
                    $db->query($sql);
                }

            }

        }

    }elseif($update_type==1){//검색한 주문
        //기능구현 나중에!
    }

    /************* DELIVERY_STATUS 변경 END*************/

    echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상태 변경이 정상적으로  처리 되었습니다.');parent.document.location.reload();</script>");
    exit;
}

/////////////////////////////////////////////////////////////////////

if($act == 'invoce_update'){//선택배송타입/송장번호 변경

    //$sub_delivery_method=$delivery_method;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
    $sub_quick=$quick;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
    $sub_deliverycode=$deliverycode;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
    $delivery_method="";
    $quick="";
    $deliverycode="";

    $od_ix_str="";

    for($j=0;$j < count($od_ix);$j++){
        if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
        else					$od_ix_str .= ",'".$od_ix[$j]."'";
    }

    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
    $db->query($sql);
    $order_details = $db->fetchall();

    for($i=0;$i < count($order_details);$i++){

        $update_str="";

        //$delivery_method=$sub_delivery_method[$order_details[$i][od_ix]];
        $delivery_company=$sub_quick[$order_details[$i][od_ix]];
        $deliverycode=$sub_deliverycode[$order_details[$i][od_ix]];

        //if($delivery_method!="")									$update_str .= " , delivery_method= '".$delivery_method."' ";
        if($delivery_company!="")									$update_str .= " , quick= '".$delivery_company."' ";
        if($deliverycode!="")										$update_str .= " , invoice_no= '".$deliverycode."' ";


        $sql="update ".TBL_SHOP_ORDER_DETAIL." set update_date = NOW() $update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
        $db->query($sql);

        if($order_details[$i][delivery_status]){
            $tmp_status=$order_details[$i][delivery_status];
        }else{
            $tmp_status=$order_details[$i][status];
        }

        $msg="개별 배송타입/송장번호 변경";

        set_order_status($order_details[$i][oid],$tmp_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"",$delivery_company,$deliverycode);

        if($order_details[$i][status] == ORDER_STATUS_DELIVERY_ING) {
            //굿스플로에 속한 택배사인지 체크
            if(chkDelivery('goodsflow', $order_details[$i]['quick'])) {
                sellerToolUpdateOrderStatus("INVOICE_UPDATE", $order_details[$i]['od_ix']);
            }
        }
    }

    echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('개별 배송타입/송장번호 변경이 정상적으로 처리 되었습니다.','parent_reload');</script>");
    exit;
}elseif($act=="select_invoce_update"){//일괄배송타입/송장번호 변경

    $od_ix_str="";

    for($j=0;$j < count($od_ix);$j++){
        if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
        else					$od_ix_str .= ",'".$od_ix[$j]."'";
    }

    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
    $db->query($sql);
    $order_details = $db->fetchall();

    $update_str="";

    //$delivery_method=$help_delivery_method;
    $delivery_company=$help_quick;
    $deliverycode="";

    for($i=0;$i < count($help_deliverycode);$i++){
        if(trim($help_deliverycode[$i])) $deliverycode .= ",".trim($help_deliverycode[$i]);
    }

    //if($delivery_method!="")									$update_str .= " , delivery_method= '".$delivery_method."' ";
    if($delivery_company!="")									$update_str .= " , quick= '".$delivery_company."' ";
    if($deliverycode!="")											$update_str .= " , invoice_no='".substr($deliverycode,1)."' ";

    for($i=0;$i < count($order_details);$i++){

        $sql="update ".TBL_SHOP_ORDER_DETAIL." set update_date = NOW() $update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
        $db->query($sql);

        if($order_details[$i][delivery_status]){
            $tmp_status=$order_details[$i][delivery_status];
        }else{
            $tmp_status=$order_details[$i][status];
        }

        $msg="일괄 배송타입/송장번호 변경";

        set_order_status($order_details[$i][oid],$tmp_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"",$delivery_company,substr($deliverycode,1));
        if($order_details[$i][status] == ORDER_STATUS_DELIVERY_ING) {
            //굿스플로에 속한 택배사인지 체크
            if(chkDelivery('goodsflow', $order_details[$i]['quick'])) {
                sellerToolUpdateOrderStatus("INVOICE_UPDATE", $order_details[$i]['od_ix']);
            }
        }
    }

    echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('일괄 배송타입/송장번호 변경이 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>");
    exit;
}elseif($act == 'invoce_add'){//송장번호 추가

    $od_ix_str="";

    for($j=0;$j < count($od_ix);$j++){
        if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
        else					$od_ix_str .= ",'".$od_ix[$j]."'";
    }

    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
    $db->query($sql);
    $order_details = $db->fetchall();

    $update_str="";
    $deliverycode="";

    for($i=0;$i < count($help_deliverycode);$i++){
        if(trim($help_deliverycode[$i])) $deliverycode .= ",".trim($help_deliverycode[$i]);
    }

    if($deliverycode!="")					$update_str .= " , invoice_no=(case when ifnull(invoice_no,'') !='' then CONCAT(invoice_no,'".$deliverycode."') else '".substr($deliverycode,1)."' end)";

    for($i=0;$i < count($order_details);$i++){

        $sql="update ".TBL_SHOP_ORDER_DETAIL." set update_date = NOW() $update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
        $db->query($sql);

        if($order_details[$i][delivery_status]){
            $tmp_status=$order_details[$i][delivery_status];
        }else{
            $tmp_status=$order_details[$i][status];
        }

        $msg="송장번호 추가";

        set_order_status($order_details[$i][oid],$tmp_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"","",substr($deliverycode,1));
    }

    echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('송장번호 추가가 정상적으로 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>");
    exit;
}elseif($act == 'invoce_delete'){//배송타입/송장번호 삭제

    $od_ix_str="";

    for($j=0;$j < count($od_ix);$j++){
        if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
        else					$od_ix_str .= ",'".$od_ix[$j]."'";
    }

    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.")  $and_company_id ";
    $db->query($sql);
    $order_details = $db->fetchall();

    $update_str = " , quick= '' ,invoice_no= '' ";

    for($i=0;$i < count($order_details);$i++){

        $sql="update ".TBL_SHOP_ORDER_DETAIL." set update_date = NOW() $update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
        $db->query($sql);

        if($order_details[$i][delivery_status]){
            $tmp_status=$order_details[$i][delivery_status];
        }else{
            $tmp_status=$order_details[$i][status];
        }

        $msg="배송타입/송장번호 삭제";

        set_order_status($order_details[$i][oid],$tmp_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],"","","");
    }

    echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배송타입/송장번호 삭제가 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>");
    exit;
}


if($act == "status_update"){		//주문상세에서 상태값 변경시

    if($change_status == ORDER_STATUS_INCOM_COMPLETE){ //입금예정인 항목 --> 입금 확인처리 입점업체는 권한 없음!!!
        if($admininfo[admin_level]==9){

            $sql="select status , user_code , bname, bmail, btel from ".TBL_SHOP_ORDER." where oid='".$oid."'  ";
            $db->query($sql);
            $order = $db->fetch();

            //사용대기중인 예치금 => 사용완료 전환 2014-07-23 이학봉 시작
            $sql = "select * from shop_order_payment where oid = '".$oid."' and pay_type ='G' and pay_status = 'IR' and method = '12'";
            $db->query($sql);
            $db->fetch();
            $deposit = $db->dt[payment_price];

            if($deposit > 0){	//입금예정 중인 주문에서 입금확인시 사용된 예치금 금액이 존재할경우 사용완료 전환해줌 2014-07-23 이학봉
                InsertDepositInfo('W', '4', '7', $oid, $deposit_ix, $deposit, $order[user_code], '입금확인으로 인한 사용완료처리', $admininfo);
            }
            //사용대기중인 예치금 => 사용완료 전환 끝

            $sql="update ".TBL_SHOP_ORDER." set status = '".ORDER_STATUS_INCOM_COMPLETE."' where oid='".$oid."'  ";
            $db->query($sql);

            $db->query("select expect_product_price, expect_delivery_price from shop_order_price WHERE oid='".$oid."' and payment_status='G' ");
            $db->fetch();

            $expect_product_price = $db->dt[expect_product_price];
            $expect_delivery_price = $db->dt[expect_delivery_price];

            //입금확인 처리시 payment 받은 금액 입력 업데이트
            table_order_price_data_creation($oid,'','','G','P',0,$expect_product_price,"관리자 입금완료",0,0,0);
            if($expect_delivery_price > 0){
                table_order_price_data_creation($oid,'','','G','D',0,$expect_delivery_price,"관리자 입금완료",0,0,0);
            }

            $db->query("update shop_order_payment set pay_status='IC' WHERE oid='".$oid."' and pay_type = 'G' and pay_status='IR'  ");

            $sql="select od.*,odd.rmobile from ".TBL_SHOP_ORDER_DETAIL." od left join shop_order_detail_deliveryinfo odd on (od.odd_ix=odd.odd_ix) where od.oid='".$oid."' and status = '".ORDER_STATUS_INCOM_READY."'";
            $db->query($sql);
            $order_details = $db->fetchall();
            for($i=0;$i < count($order_details);$i++){
                $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_INCOM_COMPLETE."'  , update_date = NOW() , ic_date=NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' ";
                $db->query($sql);

                set_order_status($order_details[$i][oid],$change_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                if($order_details[$i][hotcon_event_id] && $order_details[$i][hotcon_pcode] && $order[status] == ORDER_STATUS_INCOM_READY){
                    CallHotCon($order[user_code], $order_details[$i][oid], $order_details[$i][pid], $order_details[$i][hotcon_event_id], $order_details[$i][hotcon_pcode], $order_details[$i][pcnt], $order_details[$i][rmobile]);
                }

                //입금확인시 페널티 적립
                //셀러판매신용점수 추가 시작 2014-06-15 이학봉
                InsertPenaltyInfo('1','1',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'입금완료 판매신용점수 적립',$_SESSION["admininfo"],'ic');
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
                insertProductPoint('1', POINT_USE_STATE_IC, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '입금완료 상품점수 적립', $_SESSION["admininfo"], 'ic');

                if($i==0)		 $goodname=$order_details[$i]["pname"];
            }

            //증빙문서 처리!
            $sql="select * from receipt where order_no='".$oid."' and receipt_yn='Y' ";
            $db->query($sql);
            if($db->total){
                $receipt = $db->fetch();

                $sql="select 
					sum(case when method='".ORDER_METHOD_SAVEPRICE."' then payment_price else '0' end) as saveprice,
					sum(case when method!='".ORDER_METHOD_SAVEPRICE."' then payment_price else '0' end) as pgprice
				from 
					shop_order_payment
				where oid='".$oid."' and pay_status='IC' and pay_type = 'G' and method not in ('".ORDER_METHOD_RESERVE."') and receipt_yn='Y' ";
                $db->query($sql);
                $payment = $db->fetch();

                $cr_price=$payment[pgprice]+$payment[saveprice];
                $sup_price = round($cr_price/1.1);
                $tax = $cr_price - $sup_price;

                $ini_receipt["goodname"]=cut_str($goodname,36) . (count($order_details) > 1 ? ' 외 ' . (count($order_details) - 1) . '건' : '');
                $ini_receipt["cr_price"]=$cr_price;
                $ini_receipt["sup_price"]=$sup_price;
                $ini_receipt["tax"]=$tax;
                $ini_receipt["buyername"]=$order[bname];
                $ini_receipt["buyeremail"]=$order[bmail];
                $ini_receipt["buyertel"]=$order[btel];
                $ini_receipt["reg_num"]=$receipt[m_number];
                $ini_receipt["useopt"]=$receipt[m_useopt];

                $RECEIPT_RESULT = ini_receipt_apply($ini_receipt);

                if( $RECEIPT_RESULT["result"] == "Y" ){
                    $db->query("update receipt set receipt_yn='C' where order_no ='".$oid."' ");

                    $db->query("insert into receipt_result(oid,m_rcash_noappl,m_tid,m_payment_price,m_save_price,m_rcr_price,m_rsup_price,m_rtax,m_rsrvc_price,m_ruseopt,regdate)
							values('".$oid."','".$RECEIPT_RESULT["m_rcash_noappl"]."','".$RECEIPT_RESULT["m_tid"]."','".$RECEIPT_RESULT["m_payment_price"]."','".$payment[saveprice]."','".$payment[pgprice]."','".$RECEIPT_RESULT["m_rsup_price"]."','".$RECEIPT_RESULT["m_rtax"]."','".$RECEIPT_RESULT["m_rsrvc_price"]."','".$RECEIPT_RESULT["m_useopt"]."',NOW())");

                }elseif( $RECEIPT_RESULT["result"] == "E" ){
                    set_order_status($oid,"IC","현금영수증 발급 실패[".$RECEIPT_RESULT["result_msg"]."]","시스템","");
                }
            }

            if($order[status] == ORDER_STATUS_INCOM_READY){
                if($order_details[0][order_from] == 'self'){
                    $mail_info[mem_name] = $order[bname];
                    $mail_info[mem_mail] = $order[bmail];
                    $mail_info[mem_id] = $order[bname];
                    $mail_info[mem_mobile] = $order[bmobile];
                    sendMessageByStep('payment_bank_apply', $mail_info);
                }
            }

            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입금확인 처리 되었습니다.');parent.document.location.reload();</script>");
        }else{
            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('입점업체는 수정하실수 없습니다.');parent.document.location.reload();</script>");
        }
    }else if($change_status == ORDER_STATUS_CANCEL_COMPLETE){ // 발주취소요청인 항목 --> 취소완료 처리

        $sql="select od.*, od.pid as id, o.status as ostatus from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and od.od_ix = '".$od_ix."'  $and_company_id ";
        $db->query($sql);
        $order_details = $db->fetchall();

        for($i=0;$i < count($order_details);$i++){

            if($order_details[$i][ostatus]==ORDER_STATUS_DEFERRED_PAYMENT){//외상일때 환불 신청 X
                $update_str ="";
            }else{
                $update_str = " , refund_status='".ORDER_STATUS_REFUND_APPLY."'  , fa_date=NOW() ";
            }

            $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_CANCEL_COMPLETE."'  , cc_date = NOW()  , update_date = NOW() $update_str $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
            $db->query($sql);

            set_order_status($order_details[$i][oid],$change_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

            //적립된 적립금 취소
            //////////////// 마일리지 적립 시작///////////////////////
            $sql = "select user_code from ".TBL_SHOP_ORDER." where oid = '".$order_details[$i][oid]."'";
            $db->query($sql);
            $db->fetch();
            $user_code = $db->dt[user_code];
            InsertReserveInfo($user_code,$order_details[$i][oid],$order_details[$i][od_ix],$id,$reserve,'9','2',$etc,'mileage',$_SESSION["admininfo"]);	//마일리지,적립금 통합용 함수 2013-06-19 이학봉
            //////////////// 마일리지 적립 끝///////////////////////

            //입금후 취소시 셀러페널티 차감
            //셀러판매신용점수 추가 시작 2014-06-15 이학봉
            InsertPenaltyInfo('2','4',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'입금후취소 판매신용점수 차감',$_SESSION["admininfo"],'cc');
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
            insertProductPoint('2', POINT_USE_STATE_CC, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '입금후취소 상품점수 차감', $_SESSION["admininfo"], 'cc');

            UpdateSellingCnt($order_details[$i]);//inventory.lib.php에

            //후불 외상시 미수금 처리
            if($order_details[$i][ostatus]==ORDER_STATUS_DEFERRED_PAYMENT){
                $noaccept_data="";
                $noaccept_data[oid]=$order_details[$i][oid];
                $noaccept_data[msg]="<br/>-".date('Ymd')." ".$order_details[$i][pname]." 취소";
                $noaccept_data[order_cancel_price]=$order_details[$i][ptprice]-$order_details[$i][member_sale_price]-$order_details[$i][use_coupon];
                setNoacceptOrderCancel($noaccept_data);
            }

            //쿠폰 돌려주기!!!
			if($coupon_data[restore_cc2] == "Y"){
				$UseCoupon["oid"]=$order_details[$i][oid];
				$UseCoupon["od_ix"]=$order_details[$i][od_ix];
				$returnCoupon = orderUseCouponReturn($UseCoupon);
			}

            //제휴사 주문 상태 연동
            if(function_exists('sellerToolUpdateOrderStatus')){
                sellerToolUpdateOrderStatus(ORDER_STATUS_CANCEL_COMPLETE,$order_details[$i][od_ix]);
            }
        }

        //2012-10-09 홍진영
        $mdb->query("select * from ".TBL_SHOP_ORDER." WHERE oid='".$oid."'");
        $order = $mdb->fetch();

        if($order_details[0][order_from] == 'self'){
            $mail_info[mem_name] = $order[bname];
            $mail_info[mem_mail] = $order[bmail];
            $mail_info[mem_id] = $order[bname];
            $mail_info[mem_mobile] = $order[bmobile];
            $mail_info[msg_code]	=	'402';//MSG 코드 402 : 주문취소
            sendMessageByStep('order_cancel', $mail_info);
        }

        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 취소승인 처리 되었습니다.');parent.document.location.reload();</script>");
    }else if($change_status == ORDER_STATUS_DELIVERY_COMPLETE){ // 출고완료 --> 배송완료 처리


        $sql="select od.*,o.user_code from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and od.od_ix='".$od_ix."'  $and_company_id ";
        $db->query($sql);
        $order_details = $db->fetchall();
        for($i=0;$i < count($order_details);$i++){

            $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_COMPLETE."' , dc_date=NOW() , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
            $db->query($sql);

            set_order_status($order_details[$i][oid],$change_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

            //적립 대기를 적립 완료
            InsertReserveInfo($order_details[$i][user_code],$order_details[$i][oid],$order_details[$i][od_ix],$id,$reserve,'1','1',$etc,'mileage',$_SESSION["admininfo"]);	//마일리지,적립금 통합용 함수 2013-06-19 이학봉

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
                $message = $order_details[$i]['pname']." 구매시 적립금액";

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

            //배송완료시 셀러페널티 적립
            //셀러판매신용점수 추가 시작 2014-06-15 이학봉
            InsertPenaltyInfo('1','2',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'배송완료 판매신용점수 추가',$_SESSION["admininfo"],'dc');
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
            insertProductPoint('1', POINT_USE_STATE_DC, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '배송완료 상품점수 추가', $_SESSION["admininfo"], 'dc');

        }

        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 배송완료 처리 되었습니다.');parent.document.location.reload();</script>");
    }else if($change_status == ORDER_STATUS_BUY_FINALIZED){ // 배송완료 --> 구매확정처리

        $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix='".$od_ix."' $and_company_id";
        $db->query($sql);
        $order_details = $db->fetchall();
        for($i=0;$i < count($order_details);$i++){

            $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_BUY_FINALIZED."' , update_date = NOW(), bf_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
            $db->query($sql);

            set_order_status($order_details[$i][oid],$change_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

            //구매확정시 셀러페널티 추가
            //셀러판매신용점수 추가 시작 2014-06-15 이학봉
            InsertPenaltyInfo('1','3',$order_details[$i][oid],$order_details[$i][od_ix],$penalty,$order_details[$i]["company_id"],'구매확정 판매신용점수 추가',$_SESSION["admininfo"],'bf');
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
            insertProductPoint('1', POINT_USE_STATE_BF, $order_details[$i][oid], $order_details[$i]['od_ix'], $point, $order_details[$i]["pid"], '구매확정 상품점수 추가', $_SESSION["admininfo"], 'bf');

            //[S] 리셀러 정산 입력
            //include ($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");
            resellerAccounts($order_details[$i]['od_ix']);
            //[E] 리셀러 정산 입력
        }

        /*
        $sql="select op.* from ".TBL_SHOP_ORDER_DETAIL." od left join shop_order_payment op on (od.oid=op.oid) where od.od_ix='".$od_ix."' and op.escrow_use='Y' $and_company_id group by od.oid ";
        $db->query($sql);
        $payment = $db->fetchall();
        for($i=0;$i < count($payment);$i++){
            ///////////////////////////////////////에스크로//////////////////////////////////////승인


        }
        */


        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 구매확정 처리 되었습니다.');parent.document.location.reload();</script>");
    }else if($change_status == ORDER_STATUS_EXCHANGE_ING){ // 교환요청 --> 교환승인

        $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix='".$od_ix."' $and_company_id";
        $db->query($sql);
        $order_details = $db->fetchall();
        for($i=0;$i < count($order_details);$i++){

            $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_EXCHANGE_ING."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
            $db->query($sql);

            set_order_status($order_details[$i][oid],$change_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);
        }

        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 교환승인 처리 되었습니다.');parent.document.location.reload();</script>");
    }else if($change_status == ORDER_STATUS_RETURN_ING){ // 반품요청 --> 반품승인

        $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix='".$od_ix."' $and_company_id";
        $db->query($sql);
        $order_details = $db->fetchall();
        for($i=0;$i < count($order_details);$i++){

            $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_RETURN_ING."' , update_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
            $db->query($sql);

            set_order_status($order_details[$i][oid],$change_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

        }

        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 반품승인 처리 되었습니다.');parent.document.location.reload();</script>");
    }elseif($change_status == ORDER_STATUS_DELIVERY_READY){//배송준비중

        $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix.") and status='".ORDER_STATUS_INCOM_COMPLETE."'  $and_company_id ";
        $db->query($sql);
        $order_details = $db->fetchall();

        for($i=0;$i < count($order_details);$i++){

            $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_READY."' , update_date = NOW(), dr_date= NOW()  $am_update_str
			where  od_ix='".$order_details[$i][od_ix]."' $and_company_id";
            $db->query($sql);

            set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid],'','','');

            //제휴사 주문 상태 연동
            if(function_exists('sellerToolUpdateOrderStatus')){
                sellerToolUpdateOrderStatus(ORDER_STATUS_DELIVERY_READY,$order_details[$i][od_ix]);
            }

            echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 배송준비중 처리 되었습니다.');parent.document.location.reload();</script>");
        }
    }else if($change_status == ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY){ //입금확인 --> 해외프로세싱중 처리

        if($od_ix!=""){
            $where = " and od_ix='".$od_ix."' ";
        }

        $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where oid='".$oid."' and status = '".ORDER_STATUS_INCOM_COMPLETE."' $where $add_com_query ";
        $db->query($sql);
        $order_details = $db->fetchall();
        for($i=0;$i < count($order_details);$i++){

            $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY."' where od_ix='".$order_details[$i][od_ix]."' ";
            $db->query($sql);
            //echo nl2br($sql);

            set_order_status($order_details[$i][oid],$order_details[$i][pid],$change_status,$status_message,$admin_message,$admininfo[company_id],$quick,$deliverycode);

            /*
            $sql = "insert into ".TBL_SHOP_ORDER_STATUS." (os_ix, oid, pid, status, status_message, company_id,quick,invoice_no, regdate )
                    values
                    ('','".$order_details[$i][oid]."','".$order_details[$i][pid]."','".ORDER_STATUS_OVERSEA_WAREHOUSE_DELIVERY_READY."','해외프로세싱중 처리 완료','".$admininfo[company_id]."','$quick','$deliverycode',NOW())";
            //echo nl2br($sql);
            $db->sequences = "SHOP_ORDER_STATUS_SEQ";
            $db->query($sql);
            */
        }
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 해외프로세싱중 처리 되었습니다.');parent.document.location.reload();</script>");

    }
    exit;
}

if($act == "delivery_update"){

    /**
    작업일 : 2013 년 07월 17 일
    작업자 : 신훈식
    작업내용 :
    1. inventory/delivery_work_ing.php 페이지에서 빠른송장처리를 위해서 넘어오는 od_ix_str 이 있는경우는 $od_ix 값이 있을경우는 다시 $od_ix_str 값을 구성한다.[신훈식]
     **/
    //print_r($_POST);
    //exit;

    if(is_array($od_ix)){
        $od_ix_str="";
        for($j=0;$j < count($od_ix);$j++){
            if($j==0)				$od_ix_str .= "'".$od_ix[$j]."'";
            else						$od_ix_str .= ",'".$od_ix[$j]."'";
        }
    }

    if($pre_type==ORDER_STATUS_CANCEL_APPLY){ //발주취소요청리스트에서 넘어왔을떄
        $status_str = $update_kind."_status";
        $status=$$status_str;
    }elseif($pre_type == ORDER_STATUS_INCOM_COMPLETE || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY || $pre_type=="WMS_".ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING || $pre_type==ORDER_STATUS_DELIVERY_ING || ($pre_type==ORDER_STATUS_DELIVERY_READY && $list_type=="item_member") || $pre_type==ORDER_STATUS_DELIVERY_COMPLETE){//(WMS)출고대기,(WMS)포장대기,송장입력완료,inventory/delivery_work_ing.php  페이지 [품목/회원별 리스트],배송완료 에서 넘어왔을떄
        $status_str=$update_kind."_status";
        $status=$$status_str;
        $delivery_status_str=$update_kind."_delivery_status";
        $delivery_status=$$delivery_status_str;
        //$sub_delivery_method=$delivery_method;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
        $sub_quick=$quick;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
        $sub_deliverycode=$deliverycode;//넘어온 변수를 다른 변수에 담아서 사용(이미 선언된 변수라서)
        //$delivery_method="";
        $quick="";
        $deliverycode="";
    }elseif($pre_type==ORDER_STATUS_DELIVERY_READY && $list_type=="order"){
        /*
        inventory/delivery_work_ing.php  페이지 [주문별 리스트] 에서 빠른송장입력 형태로 넘어 왔을때의 프로세스
        */
        $status = ORDER_STATUS_DELIVERY_ING;
        $delivery_status = ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE;
    }

    if($pre_type=="order_edit"){
        //송장번호 수정때문에
        $od_where = " and status in ('".ORDER_STATUS_DELIVERY_DELAY."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') ";
    }else{
        $od_where = " and status='".ORDER_STATUS_DELIVERY_READY."' ";
    }

    //$db->debug = true;
    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".$od_ix_str.") $od_where $and_company_id ";
    $db->query($sql);
    $order_details = $db->fetchall();

    $status_alert['order_cnt'] = count($order_details);
    $status_alert['success_cnt'] = 0;
    $sucess_od_ix=array();
    for($i=0;$i < count($order_details);$i++){

        // 굿스플로 연동
        $result = true;

        if(isUseGoodsflow()) {
            // 어드민 각페이지에서 택배사코드와 송장번호가 다르게 넘어온다.
            if ($pre_type == ORDER_STATUS_INCOM_COMPLETE || $pre_type == "WMS_" . ORDER_STATUS_WAREHOUSE_DELIVERY_READY || $pre_type == "WMS_" . ORDER_STATUS_WAREHOUSE_DELIVERY_PICKING) {    // 빠른송장 입력, 출고검수완료 페이지에서 넘어올때
                $goodsflow_quick = $sub_quick[$order_details[$i][od_ix]];
                $goodsflow_invoice_no = $sub_deliverycode[$order_details[$i][od_ix]];
            } else if ($pre_type == ORDER_STATUS_DELIVERY_ING) {                // 송장입력 완료 페이지에서 넘어올때
                $goodsflow_quick = $order_details[$i][quick];
                $goodsflow_invoice_no = $order_details[$i][invoice_no];
            } else {                                                                            // 주문정보 수정 페이지에서 넘어올때
                if ($quick == '' || $deliverycode == '') {
                    $goodsflow_quick = $order_details[$i][quick];
                    $goodsflow_invoice_no = $order_details[$i][invoice_no];
                } else {
                    $goodsflow_quick = $quick;
                    $goodsflow_invoice_no = $deliverycode;
                }
            }

            //프론트에서 체크하지만 서버단에서 1번 더 체크.
            if(empty($goodsflow_quick)){
                echo("<script>alert('배송업체를 선택해주세요.');</script>");
                exit;
            }else if(empty($goodsflow_invoice_no)) {
                echo("<script>alert('송장번호를 입력해주세요.');</script>");
                exit;
            }

            if ($order_details[$i][status] == ORDER_STATUS_DELIVERY_READY && $status == ORDER_STATUS_DELIVERY_ING && $goodsflow_quick != '40') {
                //굿스플로에 속한 택배사인지 체크
                if(chkDelivery('goodsflow', $goodsflow_quick)) {
                    if (function_exists('sellerToolUpdateOrderStatus')) {
                        $goodsflow_result = sellerToolUpdateOrderStatus(ORDER_STATUS_DELIVERY_ING, $order_details[$i][od_ix], '', true, $goodsflow_quick, $goodsflow_invoice_no);

                        if ($goodsflow_result != 'success') {
                            $result = false;
                        }
                    }
                }
            }
        }

        if($result) {
            $status_alert['success_cnt']++;
            $sucess_od_ix[] = $order_details[$i][od_ix];
            if ($pre_type == ORDER_STATUS_INCOM_COMPLETE || $pre_type == "WMS_" . ORDER_STATUS_WAREHOUSE_DELIVERY_READY || ($pre_type == ORDER_STATUS_DELIVERY_READY && $list_type == "item_member")) {//출고대기,inventory/delivery_work_ing.php  페이지 [품목/회원별 리스트]에서 넘어왔을떄
                //$delivery_method=$sub_delivery_method[$order_details[$i][od_ix]];
                $delivery_company = $sub_quick[$order_details[$i][od_ix]];
                $deliverycode = $sub_deliverycode[$order_details[$i][od_ix]];
            } elseif ($pre_type == "order_edit") {
                $delivery_company = $quick;
                if ($order_details[$i][delivery_status] != "") $delivery_status = ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE;
                else                                                        $delivery_status = "";
            }

            $sql = "select status, pid, gu_ix, stock_use_yn from " . TBL_SHOP_ORDER_DETAIL . " where od_ix='" . $order_details[$i][od_ix] . "'  ";
            $db->query($sql);
            $db->fetch();
            $pid = $db->dt[pid];
            $gu_ix = $db->dt[gu_ix];
            $stock_use_yn = $db->dt[stock_use_yn];
            $order_detail_info = $db->dt;

            $update_str = "";

            if ($delivery_status != "") $update_str .= " , delivery_status= '" . $delivery_status . "' ";
            //if($delivery_method!="")										$update_str .= " , delivery_method= '".$delivery_method."' ";
            if ($delivery_company != "") $update_str .= " , quick= '" . $delivery_company . "' ";
            if ($deliverycode != "") $update_str .= " , invoice_no= '" . $deliverycode . "' ";
            if ($status != "") $update_str .= " , status= '" . $status . "' ";


            if ($status == ORDER_STATUS_DELIVERY_ING && $pre_type != ORDER_STATUS_DELIVERY_COMPLETE && $order_details[$i][di_date] == "")//배송완료에서 배송중상태 변경시에는 업데이트 X
                $update_str .= " , di_date = NOW() ";

            $sql = "update " . TBL_SHOP_ORDER_DETAIL . " set
			update_date = NOW()
			$update_str
			$am_update_str
			where od_ix='" . $order_details[$i][od_ix] . "'
			$and_company_id";
            $db->query($sql);
            //echo nl2br($sql)."<br><br>";


            //판매신용점수차후에 처리!!
            //페널티자동지급 1: 상품 품절 2: 배송 지연 3: 환불지연 4: 반품 지연 5: 교환 지연
            //AutoPenaltyInput("2",$order_detail_info);


            set_order_status($order_details[$i][oid], $status, $msg, $ADMIN_MESSAGE, $_SESSION["admininfo"]["company_id"], $order_details[$i][od_ix], $order_details[$i][pid], "", $delivery_company, $deliverycode);

            if ($delivery_status) {
                set_order_status($order_details[$i][oid], $delivery_status, $msg, $ADMIN_MESSAGE, $_SESSION["admininfo"]["company_id"], $order_details[$i][od_ix], $order_details[$i][pid]);
            }

            if ($order_details[$i][status] == ORDER_STATUS_DELIVERY_READY) {

                //제휴사 주문 상태 연동
                $sellertool_result = "success";
                if (function_exists('sellerToolUpdateOrderStatus') && $order_details[0][order_from] != 'self') {
                    $sellertool_result = sellerToolUpdateOrderStatus(ORDER_STATUS_DELIVERY_ING, $order_details[$i][od_ix]);
                }

                if ($sellertool_result != "success") {
                    //실패시 다시 전 상태로
                    $sql = "update " . TBL_SHOP_ORDER_DETAIL . " set
				update_date = NOW()
				,status= '" . $order_details[$i][status] . "'
				,delivery_status= '" . $order_details[$i][delivery_status] . "'
				where od_ix='" . $order_details[$i][od_ix] . "'
				$and_company_id";
                    //echo $sql;
                    $db->query($sql);
                } else {
                    // 없어서 추가 2012-09-21 홍진영
                    if ($status == ORDER_STATUS_AIR_TRANSPORT_ING || ($status == ORDER_STATUS_DELIVERY_ING && $pre_type != ORDER_STATUS_DELIVERY_COMPLETE)) {//배송완료에서 배송중상태 변경시에는 재고 X
                        if ($_SESSION["layout_config"]["mall_use_inventory"] == "Y" && $order_details[$i][stock_use_yn] == "Y") {
                            if ($status == ORDER_STATUS_AIR_TRANSPORT_ING || ($status == ORDER_STATUS_DELIVERY_ING && ($order_details[$i][product_type] != '1' && $order_details[$i][product_type] != '2'))) {  //해외 물류는 항공 배송중에서 재고차감
                                //if ($order_details[$i]["delivery_basic_ps_ix"]) { //조건 제거

                                    $sql = "select pi.pi_ix, pi.company_id, pi.place_name, ps.ps_ix, ps.section_name
										from	inventory_place_section ps
										left join inventory_place_info pi on pi.pi_ix = ps.pi_ix
										where ps.ps_ix = '1'";
                                    //" . $order_details[$i]["delivery_basic_ps_ix"] . " 1로 고정 홍차장

                                    $db->query($sql);
                                    $db->fetch();
                                    $order_item_info = $db->dt;

                                    $sql = "select g.gid, gu.unit, g.standard,
								'" . $order_details[$i][pcnt] . "' as amount ,
								'" . $order_details[$i][psprice] . "' as price ,
								'" . $order_details[$i][pt_dcprice] . "' as pt_dcprice ,
								'" . $order_item_info[company_id] . "' as company_id,
								'" . $order_item_info[pi_ix] . "' as pi_ix,
								'" . $order_item_info[ps_ix] . "' as ps_ix
								from inventory_goods g , inventory_goods_unit gu
								where g.gid = gu.gid and gu.gu_ix = '" . $gu_ix . "'";
                                    // 출고가격을 어떻게 처리 할지?
                                    // 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ...
                                    $db->query($sql);
                                    $delivery_iteminfo = $db->fetchall();

                                    $item_info[pi_ix] = $order_item_info[pi_ix];
                                    $item_info[ps_ix] = $order_item_info[ps_ix];
                                    $item_info[company_id] = $order_item_info[company_id];
                                    $item_info[h_div] = "2"; // 2: 출고
                                    $item_info[vdate] = date("Ymd");
                                    //$item_info[ci_ix] = $_POST["ci_ix"];
                                    $item_info[oid] = $order_details[$i][oid];
                                    $item_info[msg] = "상품판매 - 출고";//$_POST["etc"];
                                    $item_info[h_type] = '01';//01; 상품매출
                                    $item_info[charger_name] = $_SESSION[admininfo]["charger"];
                                    $item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
                                    $item_info[detail] = $delivery_iteminfo;

                                    UpdateGoodsItemStockInfo($item_info, $db);
                                //}
                            }
                        }

                        UpdateProductCnt_complete($order_details[$i]);
                    }
                }
            }
        }
    }


    //배송중일 때 메일이 발송되도록 추가 kbk 13/11/08
    if($status == ORDER_STATUS_DELIVERY_ING && $pre_type != ORDER_STATUS_DELIVERY_COMPLETE && count($sucess_od_ix) > 0){

        //푸쉬 보내기

        if(file_exists($_SERVER["DOCUMENT_ROOT"]."/admin/mobile/appapi/pushService/push.ini.php")
            && file_exists($_SERVER["DOCUMENT_ROOT"]."/admin/mobile/appapi/pushService/pushService.class")){

            include_once($_SERVER["DOCUMENT_ROOT"]."/admin/mobile/appapi/pushService/push.ini.php");
            include_once($_SERVER["DOCUMENT_ROOT"]."/admin/mobile/appapi/pushService/pushService.class");
            $push = new pushService($ios_pem,$android_apikey);
            $icon = $push->ios;
            $acon = $push->android;

            $sql="select DISTINCT oid, odd_ix from ".TBL_SHOP_ORDER_DETAIL." where od_ix in (".implode(',',$sucess_od_ix).") and status='".ORDER_STATUS_DELIVERY_ING."' $and_company_id";
            $db->query($sql);
            $order_infos = $db->fetchall("object");

            for($i=0;$i < count($order_infos);$i++){

                $qry = "select o.user_code,
                                od.invoice_no, 
                                od.pname, 
                                od.quick, 
                                odd.oid, 
                                odd.rname, 
                                odd.rtel, 
                                odd.rmobile, 
                                concat(odd.addr1,' ',odd.addr2) as addr, 
                                odd.msg, 
                                odd.zip, 
                                date_format(o.order_date,'%Y-%m-%d') as order_date, 
                                o.bname, 
                                o.bmail, 
                                o.bmobile, 
                                o.payment_price,
                                od.claim_delivery_od_ix,
                                od.claim_group 
                          from shop_order_detail_deliveryinfo odd 
                          left join shop_order o on (odd.oid=o.oid) 
                          left join shop_order_detail od on (o.oid=od.oid) 
                          WHERE odd.oid='".$order_infos[$i][oid]."' 
                            and odd.odd_ix='".$order_infos[$i][odd_ix]."' 
                            and od.od_ix in (".implode(',',$sucess_od_ix).") 
                            and od.status='".ORDER_STATUS_DELIVERY_ING."' ";
                $db->query($qry);
                $order = "";
                $order = $db->fetch();

                $db->query("select *, pid as id from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$order_infos[$i][oid]."' and status= '".ORDER_STATUS_DELIVERY_ING."' AND od_ix IN (".implode(',',$sucess_od_ix).") and product_type not in ('77') $and_company_id");
                $order_details="";
                $order_details = $db->fetchall("object");


/*
                echo "<xmp>";
                print_r($order_details);
                echo "</xmp>";
                exit;
                */


                if($order_details[0][order_from] == 'self'){

                    $mail_info[mem_name] = $order[bname];
                    $mail_info[mem_mail] = $order[bmail];
                    $mail_info[mem_id] = $order[bname];
                    $mail_info[mem_mobile] = $order[bmobile];
                    $mail_info[addr1] = $order[addr];
                    $mail_info[rtel] = $order[rtel];
                    $mail_info[msg] = $order[msg];
                    $mail_info[zip] = str_replace("-", "", $order[zip]);
                    $mail_info[payment_price] = $order[payment_price];
                    $mail_info[invoice] = $order[invoice_no];

                    $http_type = (!empty($_SERVER['HTTPS'])) ? 'https://' : 'http://';
                    $mail_info[domain] = $http_type . $_SERVER['HTTP_HOST'];

                    //$mail_info[pname] = substr($order[pname],0,20);
                    $mail_info[pname] = mb_substr($order[pname],0,15,"utf-8").(count($order_details)>1 ? " 외 ".(count($order_details)-1)."건" : "");
                    $mail_info[quick] = deliveryCompany($order[quick]);
                    $mail_info[msg_code]	=	'0301'; // MSG 발송코드 0301 : 상품발송

                    if($order[claim_group] > 0 && $order[claim_delivery_od_ix] != 0) {
                        //교환건체크
                        $mail_info['sendType'] = 'E';
                        $mail_info['processIds'] = implode(',',$sucess_od_ix);
                    }

                   // sendMessageByStep('admin_ms_email_good_send_sucess', $mail_info);
                }
            }
        }

        if($mail_info['sendType'] == 'E') {
            //교환발송건
            sendMessageByStep('admin_ms_email_exchange_send_sucess', $mail_info);
        }else {
            //첫주문발송건
            sendMessageByStep('admin_ms_email_good_send_sucess', $mail_info);
        }
        //푸쉬 보내기
    }

    if($status_alert['order_cnt'] == $status_alert['success_cnt']) {
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리 되었습니다.');parent.document.location.reload();</script>");
    }else{
        echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상태변경에 실패한 주문건이 있습니다.');parent.document.location.reload();</script>");
    }
    exit;
}

if($act == "confirmation_transform"){
	$sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix='".$od_ix."' $and_company_id";
	$db->query($sql);
	$order_details = $db->fetchall();

	$change_status = ORDER_STATUS_BUY_FINALIZED;

	for($i=0;$i < count($order_details);$i++){
		$sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_BUY_FINALIZED."' , refund_status = null, update_date = NOW(), bf_date = NOW() $am_update_str where od_ix='".$order_details[$i][od_ix]."' $and_company_id";
		$db->query($sql);
	
		set_order_status($order_details[$i][oid],$change_status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 구매확정 처리 되었습니다.');parent.document.location.reload();</script>");

	exit;
}

/*if($act == "complete_transform"){
	$sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix='".$od_ix."' $and_company_id";
	echo "B : ".$sql;
	$db->query($sql);
	$order_details = $db->fetchall();
	exit;
}*/
?>
