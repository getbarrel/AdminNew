<?
/**
 * 셀러툴 라이브러리
 *
 * @version 0.44(2012/11/16)
 * @author  scryed@forbiz.co.kr
 * @see     openapi.11st.co.kr
 */

//연동 가능한 제휴사 목록!
$sendUseSiteCode = array('npay', 'halfclub','gmarket','auction','11st','interpark_api','fashionplus','gsshop','cjmall', 'qoo10', 'demandship', 'goodsflow');
$category_table = "shop_category_info";
define(TBL_SELLERTOOL_CATEGORY_INFO, $category_table);
$category_relation_table = 'shop_product_relation';
define(TBL_SELLERTOOL_PRODUCT_RELATION, $category_relation_table);
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");

/**
 * 결제완료내역 가져오기
 *
 * @param {string} site_code 제휴사코드
 * @param {string} startDate 검색 시작일(YYYYMMDDhhmm)
 * @param {string} endDate 검색 종료일(201007210000)
 */

function getOrderList($site_code,$startDate,$endDate){

    $OAL = new OpenAPI($site_code);
/*
    echo "<pre>";
    print_r($OAL);
    echo "</pre>";
*/

    $result = $OAL->lib->getOdrComplete($startDate,$endDate);
    //print_r($result);

    if(count($result) > 0){
        foreach($result as $rt):
            if(!empty($rt["co_od_ix"])){
                $return .= insertOrderInfo($site_code,$rt);
            }
        endforeach;
    }

    return $return;
}

/**
 * 결제완료내역(재조회) 가져오기 -- 인터파크 용
 *
 * @param {string} site_code 제휴사코드
 * @param {string} startDate 검색 시작일(YYYYMMDDhhmm)
 * @param {string} endDate 검색 종료일(201007210000)
 */



function getOrderDelvList($site_code,$startDate,$endDate){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getOdrComplete($startDate,$endDate,'re_order');
    //print_r($result);
    if(count($result) > 0){
        foreach($result as $rt):
            if(!empty($rt["co_od_ix"])){
                $return .= insertOrderInfo($site_code,$rt);
            }

        endforeach;
    }

    return $return;
}

/**
 * 주문 취소 리스트 가져오기
 *
 * @param {string} site_code 제휴사코드
 * @param {string} startDate 검색 시작일(YYYYMMDDhhmm)
 * @param {string} endDate 검색 종료일(201007210000)
 */
function getCancelApplyOrderList($site_code,$startDate,$endDate){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getCancelApplyOdrComplete($startDate,$endDate);
    //print_r($result);
    if(count($result) > 0){
        foreach($result as $rt):
            if(!empty($rt["co_od_ix"])){
                $return .= updateOrdStatus($site_code,ORDER_STATUS_CANCEL_COMPLETE,$rt);
            }
        endforeach;
    }
    return $return;
}

/**
 * 주문 출고대기 취소 리스트 가져오기
 *
 * @param {string} site_code 제휴사코드
 * @param {string} startDate 검색 시작일(YYYYMMDDhhmm)
 * @param {string} endDate 검색 종료일(201007210000)
 */

function getDeliveryCancelApplyOrderList($site_code,$startDate,$endDate){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getDeliveryCancelApplyOdrComplete($startDate,$endDate);
    //prinT_r($result);
    if(count($result) > 0){
        foreach($result as $rt):
            if(!empty($rt["co_od_ix"])){
                $return .= updateOrdStatus($site_code,ORDER_STATUS_CANCEL_APPLY,$rt);
            }
        endforeach;
    }
    return $return;
}

/**
 * 클레임 리스트 가져오기
 *
 * @param {string} site_code 제휴사코드
 * @param {string} startDate 검색 시작일(YYYYMMDDhhmm)
 * @param {string} endDate 검색 종료일(201007210000)
 */
function getClaimOrderList($site_code,$startDate,$endDate){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getClaimOdrComplete($startDate,$endDate);
    //print_r($result);
//exit;
    if(count($result) > 0){
        foreach($result as $rt):
            if(!empty($rt["co_od_ix"])){
                $return .= updateOrdStatus($site_code,$rt[claim_status],$rt);
            }
        endforeach;
    }
    return $return;
}

/**
 * 반품 요청 리스트 가져오기
 *
 * @param {string} site_code 제휴사코드
 * @param {string} startDate 검색 시작일(YYYYMMDDhhmm)
 * @param {string} endDate 검색 종료일(201007210000)
 */
function getReturnApplyOrderList($site_code,$startDate,$endDate){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getReturnApplyOdrComplete($startDate,$endDate);

    if(count($result) > 0){
        foreach($result as $rt):
            if(!empty($rt["co_od_ix"])){
                $return .= updateOrdStatus($site_code,ORDER_STATUS_RETURN_APPLY,$rt);
            }
        endforeach;
    }
    return $return;
}

/**
 * 교환 요청 리스트 가져오기
 *
 * @param {string} site_code 제휴사코드
 * @param {string} startDate 검색 시작일(YYYYMMDDhhmm)
 * @param {string} endDate 검색 종료일(201007210000)
 */
function getExchangeApplyOrderList($site_code,$startDate,$endDate){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getExchangeApplyOdrComplete($startDate,$endDate);

    if(count($result) > 0){
        foreach($result as $rt):
            if(!empty($rt["co_od_ix"])){
                $return .= updateOrdStatus($site_code,ORDER_STATUS_EXCHANGE_APPLY,$rt);
            }
        endforeach;
    }
    return $return;
}

/**
 * 배송결과(배송중, 배송완료, 오류) 수신(goodsfow 용)
 *
 * @param {string} site_code 제휴사코드
 */
function getOrderDelivery($site_code){
    $OAL = new OpenAPI($site_code);
    // 한번통신당 최대 7000건씩 넘겨줌
    $result = $OAL->lib->getOrderDelivery();

    if($result->totalItems > 0){
        $db = new Database;
        $cnt = 0;
        foreach($result->items as $rt):
            $status_data = array();
            $requestArray = array();
            $update_form = "";

            /**
             * 2000 개 이상 데이터 연동시 연동실패하므로
             */
            if ($cnt < 2000) {

                if($rt->dlvStatType == '70') {//배달완료
                    $status_data['status'] = 'DC';
                    $status_message = "(주문상세번호 : " . $rt->transUniqueCode . ") 굿스플로 주문상태 변경 성공[" . $rt->dlvStatType . "]";

                    $status_data['api'] = 'ReceiveTraceResult';

                    $status_data['transUniqueCode'] = $rt->transUniqueCode;
                    $status_data['seq'] = $rt->seq;
                    $status_data['sectionCode'] = $rt->sectionCode;
                    $status_data['logisticsCode'] = $rt->logisticsCode;
                    $status_data['invoiceNo'] = $rt->invoiceNo;
                    $status_data['dlvStatType'] = $rt->dlvStatType;
                    $status_data['procDateTime'] = $rt->procDateTime;
                    $status_data['exceptionCode'] = $rt->exceptionCode;
                    $status_data['exceptionName'] = $rt->exceptionName;
                    $status_data['branchName'] = $rt->branchName;
                    $status_data['branchTel'] = $rt->branchTel;
                    $status_data['employeeName'] = $rt->employeeName;
                    $status_data['employeeTel'] = $rt->employeeTel;
                    $status_data['taker'] = $rt->taker;
                    $status_data['errorCode'] = $rt->errorCode;
                    $status_data['errorName'] = $rt->errorName;
                    $status_data['createDateTime'] = $rt->createDateTime;

                    $status_data[json_data] = json_encode($rt);

                    // response 로그
                    $OAL->lib->set_order_goodsflow_response($status_data);

                    // 우리쪽 데이터가 정상적으로 변경됐는지 확인후 배송결과수신할 request 생성
                    if ($status_data['status'] != '99') {
                        // shop_order_goodsflow_status 데이터 상태변경
                        $OAL->lib->set_order_goodsflow_status($status_data);
                    }

                    // 우리쪽 데이터가 정상적으로 변경됐는지 확인후 배송결과수신할 request 생성
                    $status_chk = $OAL->lib->getConfirmStatus($status_data);

                    // 우리쪽 데이터 상태변경
                    if ($status_chk) {

                        $requestArray['transUniqueCode'] = $rt->transUniqueCode;
                        $requestArray['seq'] = $rt->seq;
                        $requestData['data']['items'][] = $requestArray;
                        
                        $sql = "select od.*,o.user_code from " . TBL_SHOP_ORDER . " o ," . TBL_SHOP_ORDER_DETAIL . " od where o.oid=od.oid  and od.status = '" . ORDER_STATUS_DELIVERY_ING . "' and invoice_no = '" . $rt->invoiceNo . "'";
                        $db->query($sql);
                        $order_details = $db->fetchall("object");
                        for ($i = 0; $i < count($order_details); $i++) {

                            $sql = "update " . TBL_SHOP_ORDER_DETAIL . " set status = '" . ORDER_STATUS_DELIVERY_COMPLETE . "' , dc_date='" . $status_data['procDateTime'] . "' , update_date = NOW() where od_ix='" . $order_details[$i][od_ix] . "'";
                            $db->query($sql);

                            // 상태메시지에 기록 추가
                            set_order_status($order_details[$i]['oid'], ORDER_STATUS_DELIVERY_COMPLETE, $status_message, '굿스플로우', '', $order_details[$i]['od_ix'], $order_details[$i]['pid']);

                            //적립 대기 -> 적립 완료
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
                            //배송완료시 셀러 판매신용점수 추가
                            //셀러판매신용점수 추가 시작 2014-06-15 이학봉
                            InsertPenaltyInfo('1', '2', $order_details[$i][oid], $order_details[$i][od_ix], '', $order_details[$i]["company_id"], '배송완료시 판매신용점수 추가', $_SESSION["admininfo"], 'dc');
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
                            insertProductPoint('1', POINT_USE_STATE_DC, $order_details[$i][oid], $order_details[$i]['od_ix'], '', $order_details[$i]["pid"], '배송완료시 상품점수 추가', $_SESSION["admininfo"], 'dc');
                        }
                    }
                }else {
                    //배송완료처리 아닐때
                    $requestArray['transUniqueCode'] = $rt->transUniqueCode;
                    $requestArray['seq'] = $rt->seq;
                    $requestData['data']['items'][] = $requestArray;
                }
            } else {
                break;
            }

            $cnt++;
        endforeach;

        // 배송결과수신할 데이터가 존재할시 배송결과 수신
        if($cnt > 0){
            $requestData = json_encode($requestData);
            $return = $OAL->lib->sendTraceResultResponse($requestData);
        }
    }

    exit;
}

/**
 * 배송 완료 리스트 가져오기 (수취확인)
 *
 * @param {string} site_code 제휴사코드
 * @param {string} startDate 검색 시작일(YYYYMMDDhhmm)
 * @param {string} endDate 검색 종료일(201007210000)
 */
function getOrderDeliveryComplate($site_code,$nowdate,$startDate='',$endDate=''){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getOrderDeliveryComplate($nowdate,$startDate,$endDate);

    if(count($result) > 0){
        foreach($result as $rt):
            if(!empty($rt["co_od_ix"])){
                $return .= updateOrdStatus($site_code,ORDER_STATUS_DELIVERY_COMPLETE,$rt);
            }
        endforeach;
    }
    return $return;
}


/**
 * 상품 Qna 리스트 가져오기
 *
 * @param {string} site_code 제휴사코드
 * @param {string} startDate 검색 시작일(YYYYMMDDhhmm)
 * @param {string} endDate 검색 종료일(201007210000)
 */
function getProductQnaList($site_code,$startDate,$endDate){
    $OAL = new OpenAPI($site_code);

    if(method_exists($OAL->lib,'getProductQnaList')){
        $result = $OAL->lib->getProductQnaList($startDate,$endDate);
    }
    if(count($result) > 0){
        foreach($result as $rt):
            if(!empty($rt["co_bbs_ix"])){
                $return .= insertProductQna($site_code,$rt);
            }
        endforeach;
    }
    return $return;
}



/**
 * 상품 긴급 메시지 리스트 가져오기
 *
 * @param {string} site_code 제휴사코드
 * @param {string} startDate 검색 시작일(YYYYMMDDhhmm)
 * @param {string} endDate 검색 종료일(201007210000)
 */
function getEmergencyMsgList($site_code,$startDate,$endDate){
    $OAL = new OpenAPI($site_code);

    if(method_exists($OAL->lib,'getEmergencyMsgList')){
        $result = $OAL->lib->getEmergencyMsgList($startDate,$endDate);
    }
    if(count($result) > 0){
        foreach($result as $rt):
            if(!empty($rt["co_bbs_ix"])){
                $return .= insertProductQna($site_code,$rt);
            }
        endforeach;
    }
    return $return;
}


/**
 * 미수령 신고 조회 가져오기
 *
 * @param {string} site_code 제휴사코드
 * @param {string} startDate 검색 시작일(YYYYMMDDhhmm)
 * @param {string} endDate 검색 종료일(201007210000)
 */
function getUnreceivedClaim($site_code,$startDate,$endDate){
    $OAL = new OpenAPI($site_code);

    if(method_exists($OAL->lib,'getUnreceivedClaim')){
        $result = $OAL->lib->getUnreceivedClaim($startDate,$endDate);
    }
    if(count($result) > 0){
        foreach($result as $rt):
            if(!empty($rt["co_bbs_ix"])){
                $return .= insertUnreceivedClaim($site_code,$rt);
            }
        endforeach;
    }
    return $return;
}




/**
 * 반품보류 처리
 *
 * @param {string} site_code 제휴사코드
 */

function setOrderReturnDefer($site_code){
    $OAL = new OpenAPI($site_code);

    if(method_exists($OAL->lib,'setReturnDefer')){

        $db = new Database;
        $time = strtotime('-2 day');
        $sql="select
			*
		from
			shop_order_detail od
		where
			order_from ='".$site_code."'
		and
			ra_date between '".date('Y-m-d 00:00:00',$time)."' and '".date('Y-m-d 23:59:59',$time)."'
		and
			status in (
				'".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_ING."','".ORDER_STATUS_RETURN_DELIVERY."',
				'".ORDER_STATUS_RETURN_DEFER."','".ORDER_STATUS_RETURN_DENY."','".ORDER_STATUS_RETURN_DENY_DEFER."','".ORDER_STATUS_RETURN_IMPOSSIBLE."'
			) ";

        $db->query($sql);
        $order = $db->fetchall("object");
        if(count($order)>0){
            foreach($order as $data){
                $OAL->lib->setReturnDefer($data);
            }
        }
    }

    return;
}


/**
 * 재고 업데이트 (주문 수집 이후 재고 정보 제휴사로 보내기 위함)
 */
function setStockUpdate($site_code,$pid){
    $OAL = new OpenAPI($site_code);

    if(method_exists($OAL->lib,'modifyStock')){
        $result = $OAL->lib->modifyStock($pid);
    }
}


/*
//db에 넣을때 필요한 정보들

** ※ psprice 와 pt_dcprice 는 둘중 하나만 값을 넘겨야함 pt_dcprice 가 우선순위 **
** ※ f_option_text 로 넘기면 option_text로 바로 insert됨 **
** ※ b_option_text 는 option_text 뒤에 추가 **

$return[$key]["co_oid"];//주문번호
$return[$key]["addr1"];//수취인 주소1
$return[$key]["addr2"];//수취인 주소2
$return[$key]["zip"];//수취인 우편번호
$return[$key]["rname"];//수취인
$return[$key]["rtel"];//수취인 전화번호
$return[$key]["rmobile"];//수취인 핸드폰번호
$return[$key]["msg"];//배송 메모
$return[$key]["btel"];//주문자 전화번호
$return[$key]["bname"];//주문자명
$return[$key]["bmobile"];//주문자 핸드폰번호
$return[$key]["regdate"];//주문번호생성일
$return[$key]["ic_date"];//주문결제완료일
$return[$key]["co_od_ix"];//주문 순번
$return[$key]["pid"];//상품코드
$return[$key]["option_id"];//옵션코드
$return[$key]["option_text"];//옵션명
$return[$key]["f_option_text"];//옵션명
$return[$key]["b_option_text"];//옵션명
$return[$key]["pcnt"];//수량
$return[$key]["psprice"];//상품 판매가(단품)
$return[$key]["pt_dcprice"];//상품 총판매가
$return[$key]["delivery_dcprice"];//배송비
*/
function insertOrderInfo($site_code,$data){
    global $HEAD_OFFICE_CODE,$admininfo;

    $db = new Database;
    $sdb = new Database;

    $oid = '';
    ///////DEFAULT///////
    $order_from=$site_code;
    $buyer_type="1";//1:소매,2:도매
    $sex="D";
    $status=ORDER_STATUS_INCOM_COMPLETE;

    $payment_agent_type="W";
    $nPaymethod=ORDER_METHOD_BANK;
    ///////DEFAULT///////

    $co_oid = $data["co_oid"];
    $co_oid_2 = $data["co_oid_2"];
    $co_delivery_no = $data["co_delivery_no"];

    $addr1 = $data["addr1"];
    $addr2 = $data["addr2"];
    $rzip = $data["zip"];

    $doro_addr1 = $data["doro_addr1"];
    $doro_addr2 = $data["doro_addr2"];
    $doro_zip = $data["doro_zip"];

    if(empty($addr1) && empty($addr2)){
        $addr1 = $doro_addr1;
        $addr2 = $doro_addr2;
        $rzip = $doro_zip;
    }


    $rname = $data["rname"];
    $rtel = $data["rtel"];
    $rmobile = $data["rmobile"];
    $msg = $data["msg"];
    $btel = $data["btel"];
    $bmobile = $data["bmobile"];
    $bname = $data["bname"];
    $bmail = $data["bmail"];

    if($data["delivery_pay_method"]){
        $delivery_pay_method = $data["delivery_pay_method"];
    }else{
        $delivery_pay_method = "1";
    }

    //$delivery_price = $data["delivery_price"];
    //$product_price = $data["product_price"];
    //$total_price = $data["total_price"];
    //$sum_pcnt = $data["sum_pcnt"];
    $regdate = $data["regdate"];
    if( ! empty($regdate) ){
        $static_date = date('Ymd',strtotime( $regdate ));
        $regdate = "'".$regdate."'";
    }else{
        $static_date = date('Ymd');
        $regdate = "NOW()";
    }
    $ic_date = $data["ic_date"];// 어떻게??
    if( ! empty($ic_date) ){
        $ic_date = "'".$ic_date."'";
    }else{
        $ic_date = "NOW()";
    }
    $co_od_ix = $data["co_od_ix"];
    $pid = $data["pid"];
    $pname = $data["pname"];

    //하프클럽은 재고 관리 사용 안하는 옵션은 1로 연동!
    if($data["option_id"]=="1"){
        $option_id = "";
    }else{
        $option_id = $data["option_id"];
    }

    $option_text = $data["option_text"];
    $f_option_text = $data["f_option_text"];
    $b_option_text = $data["b_option_text"];
    $pcnt = $data["pcnt"];
    $psprice = $data["psprice"];
    $pt_dcprice = $data["pt_dcprice"];
    $delivery_dcprice = $data["delivery_dcprice"];
    //$dcprice = $data["dcprice"];

    $sql="select count(*) as total,oid,od_ix from shop_order_detail where order_from ='".$order_from."' and (co_oid='".$co_oid."' or co_oid = '".$co_oid_2."') and co_od_ix='".$co_od_ix."'";
    $db->query($sql);
    $db->fetch();
    $oid = $db->dt["oid"];
    $od_ix = $db->dt["od_ix"];

    //주문이 없을때
    if($db->dt["total"] == '0'){

        $sql="select
			p.*, csd.account_type,csd.account_info,csd.ac_delivery_type,csd.ac_expect_date,csd.account_method, csd.commission as com_commission,
			(select pr.cid from shop_product_relation pr where pr.pid=p.id order by basic desc limit 0,1) as cid,
			(select com_name from common_company_detail where company_id=p.admin) as company_name,
			(select com_name from common_company_detail where company_id=p.trade_admin) as trade_company_name
		from
			shop_product p left join common_seller_delivery csd on (csd.company_id=p.admin)
		where
			p.id='".$pid."' ";
        $db->query($sql);
        $db->fetch();

        $cid = $db->dt[cid];
        $pcode = $db->dt[pcode];
        $product_type = $db->dt[product_type];
        $stock_use_yn = $db->dt[stock_use_yn];
        if(empty($pname)){
            $pname = $db->dt[pname]." ".$pcode;
        }
        $surtax_yorn = $db->dt[surtax_yorn];

        $brand_code = $db->dt["brand"];
        $brand_name = $db->dt["brand_name"];
        $barcode = $db->dt["barcode"];
        $trade_company = $db->dt["trade_admin"];
        $trade_company_name = $db->dt["trade_company_name"];
        $company_id = $db->dt["admin"];
        $company_name = $db->dt["company_name"];

        $coprice = $db->dt["coprice"];
        $listprice = $db->dt["listprice"];

        $option_price=0;


        if( ! empty($option_id)){

            $sql = "select o.option_div,o.option_size,ot.option_name, ot.option_kind, option_code, o.option_listprice, o.option_price, o.option_coprice, option_barcode
						from shop_product_options_detail o,shop_product_options ot
						where id = '".$option_id."' and o.opn_ix = ot.opn_ix";
            $sdb->query($sql);

            if($sdb->total){
                $sdb->fetch();

                $option_text = "";

                if($sdb->dt[option_kind] == "x2" || $sdb->dt[option_kind] == "s2"){
                    $pname = $pname." - ".$sdb->dt[option_name];
                    $option_text .= $sdb->dt[option_div]." ".$sdb->dt[option_size];
                }else if($sdb->dt[option_kind] != "r"){//옵션이 한개만 등록되는 것을 방지 kbk 12/04/12
                    if($sdb->dt[option_price] > 0 && $sdb->dt[option_kind] != "b"){
                        $option_text .= $sdb->dt[option_name]." : ".$sdb->dt[option_div]." ".$sdb->dt[option_size]."(".number_format($sdb->dt[option_price]).")<br>";
                    }else{
                        $option_text .= $sdb->dt[option_name]." : ".$sdb->dt[option_div]." ".$sdb->dt[option_size]."<br>";
                    }
                }

                if($sdb->dt[option_kind] == "b" || $sdb->dt[option_kind] == "a" || $sdb->dt[option_kind] == "x" || $sdb->dt[option_kind] == "c" || $sdb->dt[option_kind] == "x2" || $sdb->dt[option_kind] == "s2"){
                    $sub_pname = $sdb->dt[option_div]." ".$sdb->dt[option_size];
                    if($sdb->dt[option_kind] == "b"){
                        $option_kind = "";
                    }else{
                        $option_kind = $sdb->dt[option_kind];
                    }
                    $pcode = $sdb->dt[option_code];
                    $coprice = $sdb->dt[option_coprice];
                    if($sdb->dt[option_listprice] == 0){
                        $listprice = $sdb->dt[option_price];
                    }else{
                        $listprice = $sdb->dt[option_listprice];
                    }
                    $barcode = $sdb->dt[option_barcode];
                    if(empty($psprice)){
                        $psprice = $sdb->dt[option_price];
                    }
                }else if($sdb->dt[option_kind] == "s" || $sdb->dt[option_kind] == "p" || $sdb->dt[option_kind] == "c1" || $sdb->dt[option_kind] == "c2" || $sdb->dt[option_kind] == "i1" || $sdb->dt[option_kind] == "i2"){
                    $option_price += $sdb->dt[option_price];
                }
            }else{
                if($psprice > $db->dt["sellprice"]){
                    $option_price = $psprice - $db->dt["sellprice"];
                    $psprice = $db->dt["sellprice"];
                }
            }
        }else{
            if($psprice > $db->dt["sellprice"]){
                $option_price = $psprice - $db->dt["sellprice"];
                $psprice = $db->dt["sellprice"];
            }

            if(strpos($option_text,"^|^")){
                $option_text_ar = explode(';',$option_text);
                $option_text = "";

                for($i=0; $i < count($option_text_ar); $i++){
                    $tmp_options[$i] = explode('^|^',$option_text_ar[$i]);
                }
                for($i=0; $i < count($tmp_options[0]); $i++){
                    $k = 0;
                    for($z=0; $z < count($tmp_options); $z++){

                        if($k > 0){
                            $option_text .= " : ".$tmp_options[$k][$i]."<br>";
                        }else{
                            $option_text .= $tmp_options[$k][$i];
                        }

                        $k ++;
                    }
                }
            }
        }


        if( ! empty($f_option_text)){
            $option_text = $f_option_text;
        }

        if( ! empty($b_option_text)){
            $option_text .= $b_option_text;
        }

        //제휴사에서 넘어온 금액을 기준으로 하지 않는다고 하여 무조건 엔터식스의 현재 판매되고있는 금액을 기준으로 넣어주기 위해 데이터 수정 JK160311
        //2016-03-14 다시 주석 해제
        if($pt_dcprice != ""){
            if(empty($psprice)){
                $psprice = round(($pt_dcprice - ($option_price  * $pcnt))/$pcnt);
            }
            $dcprice = $psprice;
            $ptprice = $pt_dcprice;
        }else{
            $dcprice = $psprice;
            $ptprice = ($dcprice + $option_price) * $pcnt;
            $pt_dcprice = $ptprice;
        }

        $delivery_price = $delivery_dcprice;
        $org_product_price=$pt_dcprice;
        $product_price=$org_product_price;
        $total_price=$product_price+$delivery_dcprice;
        $payment_price=$total_price;

        $tax_price=$payment_price;
        $tax_free_price=0;

        //정산 수수료설정
        if($db->dt[one_commission]=="Y"){
            $one_commission = "Y";
            $commission = $db->dt[commission];
            $commission_msg = "상품개별";
        }else{
            $one_commission = "N";
            $commission = $db->dt[com_commission];
            $commission_msg = "셀러";
        }

        if($company_id==$HEAD_OFFICE_CODE){
            $account_type="3";//정산방식 1:수수료 2:매입(공급가로 정산) 3:미정산
            $account_info="1";//정산 설정1 : 기간별 2:상품별
            $ac_delivery_type=ORDER_STATUS_DELIVERY_ING;//정산기준상태
            $ac_expect_date="3";//정산예정일
            $account_method=ORDER_METHOD_CASH;//정산지급방식 현금:10 예치금 :12
        }else{
            $account_type=$db->dt["account_type"];//정산방식 1:수수료 2:매입(공급가로 정산) 3:미정산
            $account_info=$db->dt["account_info"];//정산 설정1 : 기간별 2:상품별
            $ac_delivery_type=$db->dt["ac_delivery_type"];//정산기준상태
            $ac_expect_date=$db->dt["ac_expect_date"];//정산예정일
            $account_method=$db->dt["account_method"];//정산지급방식 현금:10 예치금 :12
        }


        //배송정책 관련!
        $delivery_type=$db->dt["delivery_type"];//통합배송여부 1:통합배송, 2:입점업체배송
        if($delivery_type=="1"){
            $ori_company_id = $HEAD_OFFICE_CODE;
            $template_company_id = $HEAD_OFFICE_CODE;
        }else{
            $ori_company_id = $company_id;
            $template_company_id = $company_id;
        }

        $sql = "select
					dt.*
				from
					shop_delivery_template as dt
				where
					dt.company_id = '".$template_company_id."' and is_basic_template='1' and product_sell_type='R'
				order by dt.regdate DESC";
        $db->query( $sql );
        $db->fetch();

        $delivery_package=$db->dt["delivery_package"];//개별배송 사용유무 Y:개별배송 N:묶음배송
        $delivery_policy=$db->dt["delivery_policy"];//1:무료배송 2:고정배송비 3:주문결제금액 할인 4:수량별할인 5:출고지별 배송비 6: 상품1개단위 배송비
        $delivery_method=$db->dt["delivery_div"];//배송방법(1:택배,2:화물,3:직배송,4:방문수령)
        $delivery_pay_method=$delivery_pay_method;//배송결제 방법	(1.선불 2. 착불)
        $delivery_addr_use = $db->dt["delivery_addr_use"];//출고지별 배송비 사용 1:사용 0:미사용
        $factory_info_addr_ix = $db->dt["factory_info_addr_ix"];


        //재고관리
        if($stock_use_yn=="Y"){
            if($option_id!=""){
                $sql="select gu.gid,gu.gu_ix from shop_product_options_detail pod , inventory_goods_unit gu where pod.option_code=gu.gu_ix and pod.id='".$option_id."' ";
                $db->query( $sql );
                $db->fetch();
                $gid = $db->dt["gid"];
                $gu_ix = $db->dt["gu_ix"];
                $pcode = $db->dt["gu_ix"];
            }else{
                $sql="select gu.gid,gu.gu_ix from inventory_goods_unit gu where gu.gu_ix='".$pcode."' ";
                $db->query( $sql );
                $db->fetch();
                $gid = $db->dt["gid"];
                $gu_ix = $db->dt["gu_ix"];
            }
        }else{
            $gid="";
            $gu_ix="";
        }

        $sql="select count(*) as total, oid from shop_order_detail where order_from ='".$order_from."' and co_oid='".$co_oid."' ";
        //echo nl2br($sql)."<br><br>";
        $db->query($sql);
        $db->fetch();
        $oid = $db->dt["oid"];
        //echo $db->dt["total"]."<br><br>";
        if($db->dt["total"] == '0'){
            sleep('1');				//아래 rand(10000, 99999)가 중복이 되어서 새로운 주문일경우 1초 쉬엇다 가기
            $oid = make_shop_order_oid();
            //echo nl2br($oid)."<br><br>";
            $sql = "insert into ".TBL_SHOP_ORDER."
			(oid, buyer_type, buserid, bname, sex, btel, bmobile, bmail, order_date, static_date, status, user_ip, user_agent, payment_agent_type, org_delivery_price, delivery_price, org_product_price, product_price, total_price,payment_price)
			values
			('".$oid."','".$buyer_type."','".$buserid."','".$bname."','".$sex."','".$btel."','".$bmobile."','".$bmail."',".$regdate.",'".$static_date."','".$status."','".$_SERVER["REMOTE_ADDR"]."','".$_SERVER["HTTP_USER_AGENT"]."','".$payment_agent_type."','".$delivery_price."','".$delivery_price."','".$org_product_price."','".$product_price."','".$total_price."','".$payment_price."')";
            $db->query($sql);

            table_order_payment_data_creation($oid,'G',$status,$nPaymethod,$tax_price,$tax_free_price,$payment_price,"");
            set_order_status($oid,$status,"제휴사API주문등록","시스템",$od_ix);
            echo $sql.'<br>';
        }else{
            $sql = "UPDATE ".TBL_SHOP_ORDER." SET
				org_delivery_price = org_delivery_price + '".$delivery_price."',
				delivery_price = delivery_price + '".$delivery_price."',
				org_product_price = org_product_price + '".$org_product_price."',
				product_price = product_price + '".$product_price."',
				total_price = total_price + '".$total_price."',
				payment_price = payment_price + '".$payment_price."'
			where oid='".$oid."' ";
            $db->query($sql);

            $sql = "UPDATE shop_order_payment SET
				tax_price = tax_price + '".$tax_price."',
				tax_free_price = tax_free_price + '".$tax_free_price."',
				payment_price = payment_price + '".$payment_price."'
			where oid='".$oid."' ";
            $db->query($sql);
        }

        $sql="select count(*) as total, ode_ix from shop_order_delivery where oid ='".$oid."' and ori_company_id='".$ori_company_id."' ";
        $db->query($sql);
        $db->fetch();
        $ode_ix = $db->dt["ode_ix"];

        if($db->dt["total"] == '0'){

            $sql = "insert into shop_order_delivery (ode_ix,oid,company_id,ori_company_id,delivery_type,delivery_package,delivery_policy,delivery_method,delivery_pay_type,delivery_addr_use,factory_info_addr_ix,pid,delivery_price,delivery_dcprice,regdate) values ('','".$oid."','".$company_id."','".$ori_company_id."','".$delivery_type."','".$delivery_package."','".$delivery_policy."','".$delivery_method."','".$delivery_pay_method."','".$delivery_addr_use."','".$factory_info_addr_ix."','".$pid."','".$delivery_price."','".$delivery_dcprice."',NOW())";
            //	echo $sql;
            $db->query($sql);
            $ode_ix = $db->insert_id();

            table_order_price_data_creation($oid,'',$company_id,'G','D',$delivery_dcprice,$delivery_dcprice,"",0,0,0);
        }else{

            if($site_code == 'gmarket' || $site_code == 'auction'){
                if($delivery_price > 0){
                    $sql="update shop_order_delivery set
						delivery_price = delivery_price + '".$delivery_price."',
						delivery_dcprice = delivery_dcprice + '".$delivery_dcprice."'
					where ode_ix='".$ode_ix."' ";
                    $db->query($sql);
                    table_order_price_data_creation($oid,'',$company_id,'G','D',$delivery_dcprice,$delivery_dcprice,"",0,0,0);
                }
            }

        }

        $sql="select * from shop_order_detail_deliveryinfo where oid='".$oid."' and addr1='".$addr1."' and addr2='".$addr2."' ";
        $db->query($sql);
        if($db->total){
            $db->fetch();
            $odd_ix = $db->dt[odd_ix];
        }else{
            $sql = "insert into shop_order_detail_deliveryinfo (odd_ix,oid,od_ix,order_type,rname,rtel,rmobile,rmail,zip,addr1,addr2,regdate) values('','".$oid."','','1','".$rname."','".$rtel."','".$rmobile."','".$rmail."','".$rzip."','".$addr1."','".$addr2."',NOW())";
            $db->query($sql);

            if($db->dbms_type == "oracle"){
                $odd_ix = $db->last_insert_id;
            }else{
                $odd_ix = $db->insert_id();
            }
        }

        $pcode = str_replace("'","&#39;",$pcode);
        $barcode = str_replace("'","&#39;",$barcode);
        $msg = str_replace("'","&#39;",$msg);

        $sql = "insert into ".TBL_SHOP_ORDER_DETAIL."
		(od_ix,mall_ix,oid,order_from,buyer_type,cid,pid,brand_code,brand_name,pcode,barcode,product_type,pname,sub_pname,gid,gu_ix,trade_company,trade_company_name,option_id,option_text,option_kind,option_price,pcnt,coprice,listprice,psprice,dcprice,ptprice,odd_ix,company_id,company_name,one_commission,commission,commission_msg,surtax_yorn,stock_use_yn,regdate,delivery_type,account_type,delivery_package,account_info,ac_delivery_type,ac_expect_date,account_method,pt_dcprice,delivery_policy,delivery_method,delivery_pay_method,ori_company_id,delivery_addr_use,factory_info_addr_ix,msgbyproduct,status,co_oid,co_od_ix,co_delivery_no,ode_ix,ic_date)
		values
		('','".$admininfo["mall_ix"]."','".$oid."','".$order_from."','".$buyer_type."','".$cid."','".$pid."','".$brand_code."','".$brand_name."','".$pcode."','".$barcode."','".$product_type."','".$pname."','".$sub_pname."','".$gid."','".$gu_ix."','".$trade_company."','".$trade_company_name."','".$option_id."','".$option_text."','".$option_kind."','".$option_price."','".$pcnt."','".$coprice."','".$listprice."','".$psprice."','".$dcprice."','".$ptprice."','".$odd_ix."','".$company_id."','".$company_name."','".$one_commission."','".$commission."','".$commission_msg."','".$surtax_yorn."','".$stock_use_yn."',".$regdate.",'".$delivery_type."','".$account_type."','".$delivery_package."','".$account_info."','".$ac_delivery_type."','".$ac_expect_date."','".$account_method."','".$pt_dcprice."','".$delivery_policy."','".$delivery_method."','".$delivery_pay_method."','".$ori_company_id."','".$delivery_addr_use."','".$factory_info_addr_ix."','".trim($msg)."','".$status."','".$co_oid."','".$co_od_ix."','".$co_delivery_no."','".$ode_ix."',".$ic_date.")";
        $db->sequences = "SHOP_ORDER_DT_SEQ";
        $db->query($sql);
        echo $sql.'<br>';
        if($db->dbms_type == "oracle"){
            $od_ix = $db->last_insert_id;
        }else{
            $od_ix = $db->insert_id();
        }

        table_order_price_data_creation($oid,'','','G','P',$product_price,$product_price,"",0,0,0);

        if ( $stock_use_yn == "Y" ) {

            $pid_array=array();
            $pid_array[]=$pid;

            $sql = "select od.id as opnd_ix ,pid from ".TBL_SHOP_PRODUCT." p inner join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od on (p.id=od.pid) where p.stock_use_yn='Y' and option_code = '".$pcode."' ";
            $db->query($sql);
            if($db->total){
                $option_dt_info = $db->fetchall();
                for($j=0;$j<count($option_dt_info);$j++){

                    $pid_array[]=$option_dt_info[$j][pid];
                    $db->query("update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = option_sell_ing_cnt + '".$pcnt."' where id = '".$option_dt_info[$j][opnd_ix]."' ");
                }
                $pid_array = array_unique($pid_array);
            }

            $db->query("update ".TBL_SHOP_PRODUCT." set sell_ing_cnt = sell_ing_cnt + '".$pcnt."', order_cnt = order_cnt + '".$pcnt."' where id in ('".implode("','",$pid_array)."') ");

            $db->query("update inventory_goods_unit set sell_ing_cnt = sell_ing_cnt + '".$pcnt."', order_cnt = order_cnt + '".$pcnt."' where gu_ix ='$pcode' ");

            //real_lack_stock update
            if($pcode){

                $sql="select real_lack_stock from shop_order_detail  where gu_ix = '".$pcode."' and status in ('IR','IC','DR','DD') and oid !='".$oid."' order by regdate desc limit 0,1";
                $db->query($sql);
                if($db->total){
                    $db->fetch();

                    $item_stock_sum = $db->dt[real_lack_stock];
                }else{
                    $sql = "select sum(ps.stock) as stock
					from inventory_goods_unit gu  left join inventory_product_stockinfo ps on (ps.unit = gu.unit and ps.gid=gu.gid)
					where gu.gu_ix = '".$pcode."' ";
                    $db->query($sql);
                    $db->fetch();

                    $item_stock_sum = $db->dt[stock];
                }

                $sql="select od_ix, pcnt from shop_order_detail  where oid='".$oid."' and gu_ix = '".$pcode."'";
                $db->query($sql);

                if($db->total){
                    $od_info = $db->fetchall("object");

                    $real_lack_stock = $item_stock_sum;

                    for($j=0;$j<count($od_info);$j++){
                        $real_lack_stock -= $od_info[$j][pcnt];
                        $sql="update shop_order_detail set real_lack_stock='".$real_lack_stock."' where od_ix='".$od_info[$j][od_ix]."' ";
                        $db->query($sql);
                    }
                }
            }

        } elseif ( $stock_use_yn == "Q" ) {
            $db->query("update ".TBL_SHOP_PRODUCT." set sell_ing_cnt = sell_ing_cnt + '".$pcnt."', order_cnt = order_cnt + '".$pcnt."' where id ='$pid'");
            $db->query("update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = option_sell_ing_cnt + '".$pcnt."' where pid = '$pid' and id ='$option_id' ");
        } else {
            $db->query("update ".TBL_SHOP_PRODUCT." set order_cnt = order_cnt + '".$pcnt."' where id ='$pid'");
        }

        // 제휴사 주문연동이 종료 될 경우 API 재고 업데이트 위해 추가 JK160218
        if(function_exists('setStockUpdate')){
            setStockUpdate($site_code,$pid);
        }

        // 정상적으로 제휴사 주문이 내부 시스템에 등록 성공 했을 경우 특정 제휴사의 경우 바로 발주 확인 프로세스를 진행 해야 하기 때문에 발주 확인 프로세스 추가
        if($site_code == 'gmarket'){
            if(function_exists('sellerToolUpdateOrderStatus')){
                sellerToolUpdateOrderStatus(ORDER_STATUS_ORDERS_CONFIRM,$od_ix);
            }
        }else if($site_code == '11st' || $site_code == 'auction'){
            //바로 배송 준비중으로 처리하기
            //sellerToolUpdateOrderStatus(ORDER_STATUS_DELIVERY_READY,$od_ix);
        }
    }

    if($site_code == 'gsshop'){
        //주문 수집 완료후 처리 완료 제휴사 리턴 해주기
        getOrderResponse('gsshop',$od_ix);
    }
}

/**
주문 수집 완료후 제휴사에 성공 여부 정보 리턴
 */
function getOrderResponse($site_code,$od_ix){
    $OAL = new OpenAPI($site_code);
    $OAL->lib->getOdrResponse($od_ix);
}

/**
취소완료 주문후 제휴사에 성공 여부 정보 리턴
 */
function getCancelApplyOrderResponse($site_code,$od_ix){
    $OAL = new OpenAPI($site_code);
    $OAL->lib->getCancelApplyOdrResponse($od_ix);
}

/**
 * 주문번호로 db 상태업데이트
 *
 * @param {string} site_code
 * @param {string} status
 * @param {array} data
 */

function updateOrdStatus($site_code,$status,$data){
    $db = new Database;
    //echo $status;
    //exit;
    switch ($status){
        case ORDER_STATUS_CANCEL_COMPLETE:

            $co_oid = $data[co_oid];
            if( ! empty($co_oid) ){
                $where =" and co_oid = '".$co_oid."' ";
            }
            $co_od_ix = $data[co_od_ix];

            $claim_type = "C";
            $ADMIN_MESSAGE = "제휴사 (실 취소완료시간 : " . $data['regdate'] . ")";
            $reason_code = $data['reason_code'];
            $pcnt = $data['pcnt'];
            $co_claim_group = $data['co_claim_group'];
            $msg = $data['msg'];

            $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and co_claim_group = '".$co_claim_group."' $where ";
            //echo $sql;
            $db->query($sql);

            if(!$db->total){
                if($site_code == 'gmarket'){
                    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix LIKE '".$co_od_ix."%' and co_claim_group = '' and cc_date is null $where"; // 지마켓 추가구성 상품 같이 처리 해야 하기때문에 구분 jk150617
                }else{
                    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and co_claim_group = '' and cc_date is null $where";
                }
                $db->query($sql);

                $order_details = $db->fetchall("object");

                for($i=0;$i < count($order_details);$i++){
                    if($site_code == 'gmarket'){
                        $pcnt = $order_details[$i]["pcnt"];
                    }
                    if($order_details[$i]["pcnt"]!=$pcnt){
                        $od_ix = orderSeparate($order_details[$i]["od_ix"],$pcnt);

                        //다중 취소일때 이슈!!! 임시!
                        $sql="select ( max(co_od_ix) +1 ) as tmp_co_od_ix from shop_order_detail where oid='".$order_details[$i][oid]."' ";
                        $db->query($sql);
                        $db->fetch();
                        $tmp_co_od_ix = $db->dt[tmp_co_od_ix];

                        $sql="update ".TBL_SHOP_ORDER_DETAIL." set co_od_ix='".$tmp_co_od_ix."' where od_ix = '".$order_details[$i][od_ix]."' ";
                        $db->query($sql);

                    }else{
                        $od_ix = $order_details[$i]["od_ix"];
                    }

                    if($order_details[$i]["gu_ix"] !="" && $order_details[$i]["delivery_basic_ps_ix"]!="" && $order_details[$i]["delivery_ps_ix"]!=""){
                        //기본출고 창고로 이동했던걸 다시 본래 보관장소로 재고 이동!
                        $results = inventory_warehouse_move($order_details[$i]["gu_ix"],$pcnt,$order_details[$i]["delivery_basic_ps_ix"],$order_details[$i]["delivery_ps_ix"],$_SESSION["admininfo"]["charger_ix"],"시스템","return",$order_details[$i]["oid"]);
                    }


                    if(($reason_code!="" && fetch_order_status_div('IC','CA',"type",$reason_code)=="S") || (empty($reason_code) && $order_details[$i]["claim_fault_type"]=="S")){
                        //S:판매자 책임 B:구매자 책임
                        //입금후 취소시 셀러 판매신용점수 차감 (판매자귀책)
                        //셀러판매신용점수 추가 시작 2014-06-15 이학봉
                        InsertPenaltyInfo('2','4',$order_details[$i][oid],$od_ix,$penalty,$order_details[$i]["company_id"],'입금후취소 판매신용점수 차감',$_SESSION["admininfo"],'cc');
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
                        insertProductPoint('2', POINT_USE_STATE_CC, $order_details[$i][oid], $od_ix, $point, $order_details[$i]["pid"], '입금후취소 상품점수 차감', $_SESSION["admininfo"], 'cc');
                    }

                    UpdateSellingCnt($order_details[$i]);

                    /*
                    $product_info=array();
                    $product_info[$key] = $order_details[$i];
                    $product_info[$key][claim_type]="C";
                    $product_info[$key][claim_group]="99";//클래임그룹임시로 99로 동일!
                    $product_info[$key][claim_fault_type]=fetch_order_status_div('IC','CA',"type",$reason_code);//클래임책임자
                    $product_info[$key][claim_apply_yn]="Y";//요청상품
                    $product_info[$key][claim_apply_cnt]=$order_details[$i][pcnt];//요청상품수량
                    $resulte = clameChangePriceCalculate($product_info);
                    */

                    $sql="select claim_group from ".TBL_SHOP_ORDER_DETAIL." where oid = '".$order_details[$i][oid]."' and co_claim_group = '".$co_claim_group."' ";
                    $db->query($sql);
                    if($db->total){
                        $db->fetch();
                        $claim_group = $db->dt["claim_group"];
                        $claim_delivery_bool=false;
                    }else{
                        $sql="select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='".$order_details[$i][oid]."' ";
                        $db->query($sql);
                        $db->fetch();
                        $claim_group = $db->dt["claim_group"];
                        $claim_delivery_bool=true;
                    }

                    $STATUS_MESSAGE = "[".fetch_order_status_div('IC','CA',"title",$reason_code)."]".$msg;
                    set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$HEAD_OFFICE_CODE,$od_ix,$order_details[$i][pid],$reason_code);



                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_CANCEL_COMPLETE."', ca_date = NOW(), cc_date = NOW(), refund_status='".ORDER_STATUS_REFUND_APPLY."', fa_date=NOW(), update_date = NOW(), claim_fault_type = '".fetch_order_status_div('IC','CA',"type",$reason_code)."', co_claim_group='".$co_claim_group."', claim_group = '".$claim_group."' where od_ix='".$od_ix."' ";
                    $db->query($sql);

                    if($claim_delivery_bool){
                        $delivery_price=0;
                        $sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','".$order_details[$i][oid]."','".$order_details[$i][company_id]."','".$order_details[$i][delivery_type]."','$claim_group','".$delivery_price."',NOW())";
                        $db->query($sql);
                    }
                }

                if($site_code == 'gsshop'){
                    getCancelApplyOrderResponse('gsshop',$od_ix);
                }
            }

            break;

        case ORDER_STATUS_CANCEL_APPLY:
            $co_oid = $data[co_oid];
            if( ! empty($co_oid) ){
                $where =" and co_oid = '".$co_oid."' ";
            }
            $co_od_ix = $data[co_od_ix];
            $cancel_status = $data['cancel_status']; //취소요청 상태 1: 요청 2: 요청철회 인터파크의 경우 요청 철회 값이 존재하기 때문에 철회시 다시 입금완료 상태로 변경하기 위함 JK160215

            //gmarket 취소요청 상태 ClaimReady : 취소신청 CalimDone : 취소완료 ClaimReject : 취소철회
            if($site_code=='interpark_api' || $site_code=='gmarket'){
                if($cancel_status == 1 || $cancel_status == 'ClaimReady' ){
                    $ADMIN_MESSAGE = "제휴사 (실 취소요청시간 : " . $data['regdate'] . ")";
                }else{
                    $ADMIN_MESSAGE = "제휴사 (실 취소요청철회 시간 : " . $data['regdate'] . ")";
                }
            }else{
                $ADMIN_MESSAGE = "제휴사 (실 취소요청시간 : " . $data['regdate'] . ")";
            }
            $reason_code = $data[reason_code];
            $msg = $data['msg'];
            $pcnt = $data['pcnt'];
            $co_claim_no = $data['co_claim_no'];

            if($site_code=='11st'){
                $co_claim_group = $data['co_claim_group'];
                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and co_claim_group = '".$co_claim_group."' $where";
            }else if($site_code=='interpark_api'){
                if($cancel_status == '1'){
                    $co_claim_group=$data['co_claim_group'];
                    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and co_claim_group = '".$co_claim_group."' $where";

                }else{
                    $co_claim_group=$data['co_claim_group'];
                    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and co_claim_group = '".$co_claim_group."' $where";
                }
            }else{
                $co_claim_group='';
                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and co_claim_group != '".$co_claim_group."' $where";
            }
//echo $reason_code."||".$msg."||".$sql."|||".$cancel_status."<br>";
            $db->query($sql);

            if(!$db->total && ($cancel_status == '1' || $cancel_status =='ClaimReady')){
                if($site_code == 'gmarket'){
                    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix LIKE '".$co_od_ix."%' and ca_date is null $where"; // 지마켓 추가구성 상품 같이 처리 해야 하기때문에 구분 jk150617
                }else{
                    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and ca_date is null $where";
                }

                $db->query($sql);

                if($db->total){
                    $order_details = $db->fetchall("object");

                    for($i=0;$i < count($order_details);$i++){
                        if($site_code == 'gmarket'){
                            $pcnt = $order_details[$i]["pcnt"];
                        }
                        if($order_details[$i]["pcnt"]!=$pcnt){

                            $od_ix = orderSeparate($order_details[$i]["od_ix"],$pcnt);

                            //다중 취소일때 이슈!!! 임시!
                            $sql="select ( max(co_od_ix) +1 ) as tmp_co_od_ix from shop_order_detail where oid='".$order_details[$i][oid]."' ";
                            $db->query($sql);
                            $db->fetch();
                            $tmp_co_od_ix = $db->dt[tmp_co_od_ix];

                            $sql="update ".TBL_SHOP_ORDER_DETAIL." set co_od_ix='".$tmp_co_od_ix."' where od_ix = '".$order_details[$i][od_ix]."' ";
                            $db->query($sql);

                        }else{

                            $od_ix = $order_details[$i]["od_ix"];
                        }

                        if($order_details[$i]["gu_ix"] !="" && $order_details[$i]["delivery_basic_ps_ix"]!="" && $order_details[$i]["delivery_ps_ix"]!=""){

                            //기본출고 창고로 이동했던걸 다시 본래 보관장소로 재고 이동!
                            $results = inventory_warehouse_move($order_details[$i]["gu_ix"],$order_details[$i]["pcnt"],$order_details[$i]["delivery_basic_ps_ix"],$order_details[$i]["delivery_ps_ix"],$_SESSION["admininfo"]["charger_ix"],"시스템","return",$order_details[$i]["oid"]);
                        }

                        $STATUS_MESSAGE = "[".fetch_order_status_div('IC','CA',"title",$reason_code)."]".$msg;
                        set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$HEAD_OFFICE_CODE,$od_ix,$order_details[$i][pid],$reason_code);

                        /*
                        $product_info=array();
                        $product_info[$key] = $order_details[$i];
                        $product_info[$key][claim_type]="C";
                        $product_info[$key][claim_group]="99";//클래임그룹임시로 99로 동일!
                        $product_info[$key][claim_fault_type]=fetch_order_status_div('IC','CA',"type",$reason_code);//클래임책임자
                        $product_info[$key][claim_apply_yn]="Y";//요청상품
                        $product_info[$key][claim_apply_cnt]=$order_details[$i][pcnt];//요청상품수량
                        $resulte = clameChangePriceCalculate($product_info);
                        */

                        $sql="select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='".$order_details[$i][oid]."' ";
                        $db->query($sql);
                        $db->fetch();
                        $claim_group = $db->dt["claim_group"];

                        $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_CANCEL_APPLY."', ca_date = NOW(), update_date = NOW(), claim_fault_type = '".fetch_order_status_div('IC','CA',"type",$reason_code)."', claim_group = '".$claim_group."', co_claim_group='".$co_claim_group."', co_claim_no='".$co_claim_no."' where  od_ix='".$od_ix."' ";
                        $db->query($sql);

                        $delivery_price=0;
                        $sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','".$order_details[$i][oid]."','".$order_details[$i][company_id]."','".$order_details[$i][delivery_type]."','$claim_group','".$delivery_price."',NOW())";
                        $db->query($sql);

                    }
                }else{
                    // ca_date 가 존재 한다는 것은 이미 제휴사에 품절 취소 요청을 진행한 상품으로 지마켓의 경우 해당 조건일때 취소 승인처리를 바로 진행 함
                    if($site_code == 'gmarket'){
                        if(function_exists('sellerToolUpdateOrderStatus')){
                            sellerToolUpdateOrderStatus(ORDER_STATUS_CANCEL_COMPLETE,$order_details[$i][od_ix]);
                        }
                    }
                }
            }else{

                if($cancel_status == '2' || $cancel_status == 'ClaimReject'){
                    $status=ORDER_STATUS_INCOM_COMPLETE;

                    $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and status='".ORDER_STATUS_CANCEL_APPLY."' $where";
                    $db->query($sql);
                    $order_details = $db->fetchall("object");

                    for($i=0;$i < count($order_details);$i++){
                        if($order_details[$i]["pcnt"]!=$pcnt){

                            $od_ix = orderSeparate($order_details[$i]["od_ix"],$pcnt);

                            //다중 취소일때 이슈!!! 임시!
                            $sql="select ( max(co_od_ix) +1 ) as tmp_co_od_ix from shop_order_detail where oid='".$order_details[$i][oid]."' ";
                            $db->query($sql);
                            $db->fetch();
                            $tmp_co_od_ix = $db->dt[tmp_co_od_ix];

                            $sql="update ".TBL_SHOP_ORDER_DETAIL." set co_od_ix='".$tmp_co_od_ix."' where od_ix = '".$order_details[$i][od_ix]."' ";
                            $db->query($sql);

                        }else{

                            $od_ix = $order_details[$i]["od_ix"];
                        }

                        if($order_details[$i]["gu_ix"] !="" && $order_details[$i]["delivery_basic_ps_ix"]!="" && $order_details[$i]["delivery_ps_ix"]!=""){

                            //기본출고 창고로 이동했던걸 다시 본래 보관장소로 재고 이동!
                            $results = inventory_warehouse_move($order_details[$i]["gu_ix"],$order_details[$i]["pcnt"],$order_details[$i]["delivery_basic_ps_ix"],$order_details[$i]["delivery_ps_ix"],$_SESSION["admininfo"]["charger_ix"],"시스템","return",$order_details[$i]["oid"]);
                        }

                        $STATUS_MESSAGE = $msg;
                        set_order_status($order_details[$i][oid],$status,$STATUS_MESSAGE,$ADMIN_MESSAGE,$HEAD_OFFICE_CODE,$od_ix,$order_details[$i][pid],$reason_code);


                        $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_INCOM_COMPLETE."', update_date = NOW(), ca_date = NULL, co_claim_group='' where  od_ix='".$od_ix."' ";
                        $db->query($sql);
                    }
                }
            }

            break;

        case ORDER_STATUS_RETURN_APPLY:
            $co_oid = $data[co_oid];
            if( ! empty($co_oid) ){
                $where =" and co_oid = '".$co_oid."' ";
            }
            $co_od_ix = $data[co_od_ix];
            $reason_code = $data[reason_code];
            $co_claim_group = $data['co_claim_group'];
            $co_claim_no = $data['co_claim_no'];
            $pcnt = $data[pcnt];
            $msg = $data[msg];

            //2015-06-18 Hong 추가 변동 배송비 추가 (추가 결제는 -로 넘겨야함)
            $claim_delivery_price = $data[claim_delivery_price];

            $ADMIN_MESSAGE = "제휴사 (실 반품요청시간 : " . $data['regdate'] . ")";
            $c_type = "B";

            $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and co_claim_group = '".$co_claim_group."' $where ";

            $db->query($sql);

            if(!$db->total){

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and (co_claim_group = '' or co_claim_group is null ) and ra_date is null $where ";

                $db->query($sql);
                $order_details = $db->fetchall("object");

                for($i=0;$i < count($order_details);$i++){
                    if($site_code == 'gmarket'){
                        $pcnt = $order_details[$i]["pcnt"];
                    }
                    if($order_details[$i]["pcnt"]!=$pcnt){
                        $od_ix = orderSeparate($order_details[$i]["od_ix"],$pcnt);

                        //다중 취소일때 이슈!!! 임시!
                        $sql="select ( max(co_od_ix) +1 ) as tmp_co_od_ix from shop_order_detail where oid='".$order_details[$i][oid]."' ";
                        $db->query($sql);
                        $db->fetch();
                        $tmp_co_od_ix = $db->dt[tmp_co_od_ix];

                        $sql="update ".TBL_SHOP_ORDER_DETAIL." set co_od_ix='".$tmp_co_od_ix."' where od_ix = '".$order_details[$i][od_ix]."' ";
                        $db->query($sql);

                    }else{
                        $od_ix = $order_details[$i]["od_ix"];
                    }

                    $sql="select claim_group from ".TBL_SHOP_ORDER_DETAIL." where oid = '".$order_details[$i][oid]."' and co_claim_group = '".$co_claim_group."' ";
                    $db->query($sql);
                    if($db->total){
                        $db->fetch();
                        $claim_group = $db->dt["claim_group"];
                        $claim_delivery_bool=false;
                    }else{
                        $sql="select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='".$order_details[$i][oid]."' ";
                        $db->query($sql);
                        $db->fetch();
                        $claim_group = $db->dt["claim_group"];
                        $claim_delivery_bool=true;
                    }

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_RETURN_APPLY."', update_date = NOW(), ra_date=NOW(), claim_fault_type = '".fetch_order_status_div('DC','RA',"type",$reason_code)."',  claim_group='".$claim_group."', co_claim_group='".$co_claim_group."' ,co_claim_no= '".$co_claim_no."'
					where od_ix='".$od_ix."' ";
                    $db->query($sql);

                    $STATUS_MESSAGE = "[".fetch_order_status_div('DC','RA',"title",$reason_code)."]".$msg;
                    set_order_status($order_details[$i][oid],ORDER_STATUS_RETURN_APPLY,$STATUS_MESSAGE,$ADMIN_MESSAGE,$HEAD_OFFICE_CODE,$od_ix,$order_details[$i][pid],$reason_code,"","",$c_type);

                    if($claim_delivery_bool){
                        if( ! empty($claim_delivery_price)){
                            $delivery_price=$claim_delivery_price;
                        }else{
                            $delivery_price=0;
                        }

                        $sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','".$order_details[$i][oid]."','".$order_details[$i][company_id]."','".$order_details[$i][delivery_type]."','$claim_group','".$delivery_price."',NOW())";
                        $db->query($sql);
                    }
                }
            }

            break;
        case ORDER_STATUS_DELIVERY_COMPLETE:

            $co_oid = $data[co_oid];
            $co_od_ix = $data[co_od_ix];
            $dc_date = $data[dc_date];

            $db->query("select * from ".TBL_SHOP_ORDER_DETAIL." WHERE co_od_ix='".$co_od_ix."' and co_oid = '".$co_oid."' and status = 'DI'");
            $db->fetch();
            $od_data=$db->dt;
            $od_ix = $od_data[od_ix];
            if($od_data['delivery_policy'] !='9'){ // 교환 상품이 아닐때


                $sql="select od.*,o.user_code from ".TBL_SHOP_ORDER." o ,".TBL_SHOP_ORDER_DETAIL." od where o.oid=od.oid and od.od_ix = '".$od_ix."'  ";
                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_COMPLETE."' , dc_date='".$dc_date."' , update_date = NOW() where od_ix='".$order_details[$i][od_ix]."' ";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);


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
                }
            }else{//교환리스트,반품리스트에서 넘어왔을때(마일리지 X, dc_date X)
                $od_ix_str="";

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where od_ix = '".$od_ix."' ";

                $db->query($sql);
                $order_details = $db->fetchall();
                for($i=0;$i < count($order_details);$i++){

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".ORDER_STATUS_DELIVERY_COMPLETE."' , update_date = NOW() where od_ix='".$order_details[$i][od_ix]."' ";
                    $db->query($sql);

                    set_order_status($order_details[$i][oid],$status,$msg,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$order_details[$i][od_ix],$order_details[$i][pid]);

                    $sql = "update ".TBL_SHOP_ORDER_DETAIL." set status='".ORDER_STATUS_SETTLE_READY."' where oid = '".$order_details[$i][oid]."' and claim_delivery_od_ix ='".$order_details[$i][od_ix]."' and status='".ORDER_STATUS_EXCHANGE_READY."'";
                    $db->query($sql);
                }
            }

            break;
        case ORDER_STATUS_EXCHANGE_APPLY:
            $co_oid = $data[co_oid];
            if( ! empty($co_oid) ){
                $where =" and co_oid = '".$co_oid."' ";
            }
            $co_od_ix = $data[co_od_ix];
            $reason_code = $data[reason_code];
            $co_claim_group = $data['co_claim_group'];
            $co_claim_no = $data['co_claim_no'];
            $pcnt = $data[pcnt];
            $msg = $data[msg];

            //send_yn N:아직 상품을 안보냈을때 Y:보냈을떄
            //send_type 1:직접발송,2:지정택배방문요청(셀러업체와 계약된 택배업체 방문수령 수거)
            $send_yn = $data[send_yn];
            $send_type = $data[send_type];
            $ADMIN_MESSAGE = "제휴사 (실 교환요청시간 : " . $data['regdate'] . ")";

            //2015-06-18 Hong 추가 변동 배송비 추가 (추가 결제는 -로 넘겨야함)
            $claim_delivery_price = $data[claim_delivery_price];

            $c_type = "B";
            $refund_status=ORDER_STATUS_REFUND_READY;

            $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and co_claim_group = '".$co_claim_group."' and delivery_policy!='9' $where ";
            $db->query($sql);

            if(!$db->total){

                $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and ea_date is null and delivery_policy!='9' $where ";
                $db->query($sql);
                $order_details = $db->fetchall("object");

                for($i=0;$i < count($order_details);$i++){
                    if($site_code == 'gmarket'){
                        $pcnt = $order_details[$i]["pcnt"];
                    }
                    if($order_details[$i]["pcnt"]!=$pcnt){
//						set_order_status($order_details[$i][oid],ORDER_STATUS_EXCHANGE_APPLY,'부분교환요청으로 인한 연동실패',$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$od_ix,$order_details[$i][pid],$reason_code,"","",$c_type);
//						return false;

                        $od_ix = orderSeparate($order_details[$i]["od_ix"],$pcnt);

                        //다중 교환일때 이슈!!! 임시!
                        $sql="select ( max(co_od_ix) +1 ) as tmp_co_od_ix from shop_order_detail where oid='".$order_details[$i][oid]."' ";
                        $db->query($sql);
                        $db->fetch();
                        $tmp_co_od_ix = $db->dt[tmp_co_od_ix];

                        $sql="update ".TBL_SHOP_ORDER_DETAIL." set co_od_ix='".$tmp_co_od_ix."' where od_ix = '".$order_details[$i][od_ix]."' ";
                        $db->query($sql);

                    }else{
                        $od_ix = $order_details[$i]["od_ix"];
                    }

                    $sql="select claim_group from ".TBL_SHOP_ORDER_DETAIL." where oid = '".$order_details[$i][oid]."' and co_claim_group = '".$co_claim_group."' ";
                    $db->query($sql);
                    if($db->total){
                        $db->fetch();
                        $claim_group = $db->dt["claim_group"];
                        $claim_delivery_bool=false;
                    }else{
                        $sql="select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='".$order_details[$i][oid]."' ";
                        $db->query($sql);
                        $db->fetch();
                        $claim_group = $db->dt["claim_group"];
                        $claim_delivery_bool=true;
                    }

                    $delivery_type=$order_details[$i][delivery_type];//통합배송여부 1:통합배송, 2:입점업체배송
                    $delivery_package=$order_details[$i][delivery_package];//Y:개별배송 N:묶음배송
                    $delivery_policy="9";//1:무료배송 2:고정배송비 3:주문결제금액 할인 4:수량별할인 5:출고지별 배송비 6: 상품1개단위 배송비 9:클레임배송
                    $_delivery_method=$order_details[$i][delivery_method];
                    $delivery_pay_method="1";//배송정책구분값(선불:1, 착불:2)
                    $delivery_addr_use=$order_details[$i][delivery_addr_use];
                    $factory_info_addr_ix=$order_details[$i][factory_info_addr_ix];


                    if($claim_delivery_bool){

                        if( ! empty($claim_delivery_price)){
                            $delivery_price=$claim_delivery_price;
                        }else{
                            $delivery_price=0;
                        }

                        $sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','".$order_details[$i][oid]."','".$order_details[$i][company_id]."','".$order_details[$i][delivery_type]."','$claim_group','".$delivery_price."',NOW())";
                        $db->query($sql);

                        if($send_yn=="N"){//아직 상품을 안보냈을때
                            if($send_type=="2"){//지정택배방문요청(셀러업체와 계약된 택배업체 방문수령 수거)
                                $order_type="4";
                            }else{//직접발송(구매자께서 개별로 배송할 경우)
                                $order_type="3";
                            }

                            $return_zip = $return_zip1."-".$return_zip2;
                            $delivery_method="";
                            $quick="";
                            $deliverycode="";
                            $delivery_pay_type="";
                        }else{//발송했을때
                            $send_type="1";
                            $order_type="3";
                            $return_rname="";
                            $return_rtel="";
                            $return_zip="";
                            $return_addr1="";
                            $return_addr2="";
                            $return_delivery_msg="";
                        }

                        //배송비처리하기!
                        $sql = "insert into shop_order_delivery (ode_ix,oid,company_id,ori_company_id,delivery_type,delivery_package,delivery_policy,delivery_method,delivery_pay_type,delivery_addr_use,factory_info_addr_ix,pid,delivery_price,delivery_dcprice,regdate) values ('','".$order_details[$i][oid]."','".$order_details[$i][company_id]."','".$claim_group."','".$delivery_type."','".$delivery_package."','".$delivery_policy."','".$_delivery_method."','".$delivery_pay_method."','".$delivery_addr_use."','".$factory_info_addr_ix."','','0','0',NOW())"; //".abs($total_apply_delivery_price)."
                        $db->query($sql);
                        $ode_ix = $db->insert_id();

                        $sql="select * from shop_delivery_address where addr_ix in (SELECT dt.exchange_info_addr_ix FROM shop_delivery_template dt WHERE dt.company_id='".$order_details[$i][company_id]."' and exchange_info_addr_ix > 0 ) limit 0,1";
                        $db->query($sql);

                        $db->fetch();
                        $return_zip=$db->dt[zip_code];
                        $return_rname=$db->dt[addr_name];
                        $return_addr1=$db->dt[address_1];
                        $return_addr2=$db->dt[address_2];
                        $return_rtel=$db->dt[addr_phone];


                        //반품정보 입력
                        $sql="insert into shop_order_detail_deliveryinfo (odd_ix,oid,od_ix,order_type,rname,rtel,rmobile,rmail,zip,addr1,addr2,msg,due_date,delivery_method,quick,invoice_no,send_yn,send_type,delivery_pay_type,add_delivery_price,regdate) values('','".$order_details[$i][oid]."','".$od_ix."','".$order_type."','".$return_rname."','".$return_rtel."','".$return_rtel."','','".$return_zip."','".$return_addr1."','".$return_addr2."','".$return_delivery_msg."','','".$delivery_method."','".$quick."','".$deliverycode."','".$send_yn."','".$send_type."','".$delivery_pay_type."','0',NOW())";
                        $db->query($sql);
                        $return_odd_ix = $db->insert_id();

                        $sql="select * from shop_order_detail_deliveryinfo where oid = '".$order_details[$i][oid]."' and order_type = '1' ";
                        $db->query($sql);
                        $db->fetch();

                        $sql="insert into shop_order_detail_deliveryinfo (odd_ix,oid,od_ix,order_type,rname,rtel,rmobile,rmail,zip,addr1,addr2,msg,due_date,delivery_method,quick,invoice_no,send_yn,send_type,add_delivery_price,regdate) values('','".$order_details[$i][oid]."','','2','".$db->dt['rname']."','".$db->dt['rtel']."','".$db->dt['rmobile']."','".$db->dt['rmail']."','".$db->dt['zip']."','".$db->dt['addr1']."','".$db->dt['addr2']."','".$db->dt['msg']."','','','','','','','0',NOW())";
                        $db->query($sql);
                        $delivery_odd_ix = $db->insert_id();
                    }else{
                        $sql="select ode_ix from shop_order_delivery where oid = '".$order_details[$i][oid]."' and ori_company_id = '".$claim_group."' ";
                        $db->query($sql);
                        $db->fetch();
                        $ode_ix = $db->dt['ode_ix'];

                        $sql="select odd_ix from shop_order_detail_deliveryinfo where oid = '".$order_details[$i][oid]."' and order_type = '".$order_type."' ";
                        $db->query($sql);
                        $db->fetch();
                        $return_odd_ix = $db->dt['odd_ix'];

                        $sql="select odd_ix from shop_order_detail_deliveryinfo where oid = '".$order_details[$i][oid]."' and order_type = '2' ";
                        $db->query($sql);
                        $db->fetch();
                        $delivery_odd_ix = $db->dt['odd_ix'];
                    }

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".$status."', update_date = NOW(), ea_date=NOW(), claim_fault_type = '".fetch_order_status_div('DC','RA',"type",$reason_code)."', refund_status='".$refund_status."' , odd_ix='".$return_odd_ix."', claim_group ='".$claim_group."', exchange_delivery_type='".$exchange_delivery_type."',co_claim_group='".$co_claim_group."' ,co_claim_no= '".$co_claim_no."' where  od_ix='".$od_ix."' ";
                    $db->query($sql);


                    $STATUS_MESSAGE = "[".fetch_order_status_div('DC','RA',"title",$reason_code)."]".$msg;
                    set_order_status($order_details[$i][oid],ORDER_STATUS_EXCHANGE_APPLY,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$od_ix,$order_details[$i][pid],$reason_code,"","",$c_type);

                    //동일주문 복사
                    $new_od_ix = orderSeparate($od_ix,"",true);

                    //교환배송상품관련 처리!!!

                    $sql="update ".TBL_SHOP_ORDER_DETAIL." set
							option_id = '',
							option_etc='' ,
							reserve = '',
							status = '".ORDER_STATUS_EXCHANGE_READY."',
							odd_ix='".$delivery_odd_ix."',
							claim_delivery_od_ix='".$od_ix."',
							claim_group ='".$claim_group."',
							ori_company_id='".$claim_group."',
							delivery_policy = '".$delivery_policy."',
							delivery_pay_method='".$delivery_pay_method."',
							delivery_type = '".$delivery_type."',
							delivery_package='".$delivery_package."',
							delivery_method='".$_delivery_method."',
							delivery_addr_use='".$delivery_addr_use."',
							factory_info_addr_ix='".$factory_info_addr_ix."',
							ode_ix='".$ode_ix."',
							delivery_company_id='',
							delivery_pi_ix='',
							delivery_ps_ix='',
							delivery_basic_ps_ix='',
							delivery_status = '',
							refund_status = '',
							quick = '',
							invoice_no = '',
							input_type = '',
							output_type = '',
							claim_fault_type = '',
							is_check_picking = '',
							is_check_delivery = '',
							return_product_state='',
							accounts_status = '',
							ac_ix = '',
							refund_ac_ix = '',
							update_date = NOW(),
							dr_date=NULL,
							di_date=NULL,
							ac_date = NULL,
							dc_date = NULL,
							bf_date=NULL,
							ea_date = NULL,
							ra_date = NULL,
							fa_date = NULL,
							fc_date = NULL,
							due_date = ''
						where  od_ix='".$new_od_ix."' ";

                    $db->query($sql);
                }
            }

            break;
        case ORDER_STATUS_DELIVERY_ING:
            $co_oid = $data[co_oid];
            if( ! empty($co_oid) ){
                $where =" and co_oid = '".$co_oid."' ";
            }
            $co_od_ix = $data[co_od_ix];
            $reason_code = $data[reason_code];
            $co_claim_group = $data['co_claim_group'];
            $co_claim_no = $data['co_claim_no'];
            $msg = $data[msg];

            $sql="select * from ".TBL_SHOP_ORDER_DETAIL." where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and co_claim_group = '".$co_claim_group."' and co_claim_no = '".$co_claim_no."' and status != 'DI' $where ";
            echo $sql;

            $db->query($sql);

            if($db->total){
                $db->fetch();

                $sql="update ".TBL_SHOP_ORDER_DETAIL." set status = '".$status."', update_date = NOW(), ra_date= NULL, co_claim_group = '',co_claim_no = ''    where order_from = '".$site_code."' and co_od_ix = '".$co_od_ix."' and co_claim_group = '".$co_claim_group."' and co_claim_no = '".$co_claim_no."' and status != 'DI' $where ";
                $db->query($sql);


                $STATUS_MESSAGE = $msg;
                set_order_status($db->dt[oid],ORDER_STATUS_DELIVERY_ING,$STATUS_MESSAGE,$ADMIN_MESSAGE,$_SESSION["admininfo"]["company_id"],$db->dt[od_ix],$db->dt[pid],$reason_code,"","",$c_type);
            }

            break;
    }
}

/**
 * 제휴 주문 상태 업데이트!
 */
function sellerToolUpdateOrderStatus($status, $od_ix, $reason_code='', $goodsflow_chk = false, $quick = '', $invoice_no = ''){
    global $sendUseSiteCode;

    $db = new Database;

    $db->query("select * from ".TBL_SHOP_ORDER_DETAIL." WHERE od_ix='".$od_ix."'");
    $db->fetch();
    $od_data=$db->dt;
    $od_data[reason_code] = $reason_code;

    $RESULT = "success";
    $dupl_chk = 'true';
    if($status == 'DI' && $goodsflow_chk){
        $od_data[origin_order_from] = $od_data[order_from];
        $od_data[order_from] = "goodsflow";

        $od_data[quick] = $quick;
        $od_data[invoice_no] = $invoice_no;

        $db->query("select * from shop_order_goodsflow_status WHERE item_unique_code = '".$od_ix."_".$invoice_no."'");
        $db->fetch();

        if($db->total > 0){
            $dupl_chk = 'false';
        }
    }

    if(!empty($od_data[order_from]) && in_array($od_data[order_from],$sendUseSiteCode)){

        switch ($status){

            case ORDER_STATUS_ORDERS_CONFIRM: //발주 확인 용 (현재 G 마켓 사용)
                $RESULT = sendOrdRepackaging($od_data);
                break;

            case ORDER_STATUS_DELIVERY_READY:
                if($od_data[order_from] == 'gmarket'){ // G 마켓의 경우 주문데이터 수집 직후 바로 발주 연동 처리 하기 때문에 그이후 배송준비중 상태가 될 때 발주 재연동을 진행해야 하기때문에 조건 추가
                    $od_data[re_confirm] = 'Y'; // 발주 재 연동 상태값 추가
                }

                $RESULT = sendOrdRepackaging($od_data);

                break;

            case ORDER_STATUS_DELIVERY_ING:
                if($dupl_chk == 'true') {
                    if($goodsflow_chk){
                        $RESULT = sendOrdReqDelivery($od_data);
                    }else {
                        if ($od_data['delivery_policy'] == '9') { //교환상품일때
                            $RESULT = sendOrdReqExchangeDelivery($od_data);
                        } else {
                            $RESULT = sendOrdReqDelivery($od_data);
                        }
                    }
                }
                break;

            case ORDER_STATUS_RETURN_COMPLETE:
                $od_data[return_yn] = 'Y';
                $RESULT = sendOrdReqReturnComplete($od_data);
                break;

            case ORDER_STATUS_CANCEL_APPLY:
                $RESULT = sendOrdReqCancelApply($od_data);
                break;
            case ORDER_STATUS_CANCEL_COMPLETE:
                if( ( $od_data[order_from]=='auction' || $od_data[order_from]=='fashionplus'  || $od_data[order_from]=='cjmall' ) && empty($od_data['co_claim_group']) && empty($od_data['co_claim_no']) ){
                    $RESULT = sendOrdDenySell($od_data);
                }else{
                    $RESULT = sendOrdReqCancelComplete($od_data);
                }
                break;
            case ORDER_STATUS_EXCHANGE_ING:
                $RESULT = sendOrdExchangeIng($od_data);
                break;
            case ORDER_STATUS_EXCHANGE_ACCEPT:
                $RESULT = sendOrdExchangeAccept($od_data);
                break;

            case ORDER_STATUS_EXCHANGE_DEFER:
            case ORDER_STATUS_EXCHANGE_IMPOSSIBLE:
                $RESULT = sendExchangeDelay($od_data);
                break;

            case ORDER_STATUS_RETURN_ING:
                $od_data[return_yn] = 'Y';
                $RESULT = sendOrdExchangeIng($od_data);
                break;

            case ORDER_STATUS_EXCHANGE_RESERVE:
                $RESULT = sendOrdExchangeReserve($od_data);
                break;

            case ORDER_STATUS_RETURN_RESERVE:
                $RESULT = sendOrdReturnReserve($od_data);
                break;

            case ORDER_UNRECEIVED_CLAIM_COMPLETE:
                $RESULT = RetractUnreceivedClaim($od_data);
                break;

            case ORDER_STATUS_RETURN_DEFER:
                $RESULT = sendOrdReturnDefer($od_data);
                break;
            case 'RETURN_CANCEL':
                $RESULT = sendOrdReturnCancel($od_data);
                break;
            case 'INVOICE_UPDATE':
                $RESULT = sendOrdReqDelivery($od_data);
                break;
        }

    }

    return $RESULT;
}


/**
 * 발송 처리
 */

function sendOrdReqDelivery($od_data){
    global $HEAD_OFFICE_CODE;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $data[oid] = $oid;
    $data[od_ix] = $od_ix;
    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[pid] = $od_data[pid];
    $data[od_ix] = $od_data[od_ix];
    $data[company_id] = $od_data[company_id];
    $data[co_delivery_no] = $od_data[co_delivery_no];

    if($od_data[option_id]=="0"){
        $data[option_id] = "1";
    }else{
        $data[option_id] = $od_data[option_id];
    }

    $data[option_text] = $od_data[option_text];
    $data[pcnt] = $od_data[pcnt];

    $data[quick] = $od_data[quick];

    $data[di_date] = $od_data[di_date];

    $tmp_invoice_no = explode(",",$od_data[invoice_no]);
    $data[invoice_no] = $tmp_invoice_no[0];

    $status = ORDER_STATUS_DELIVERY_ING;
    $admin_message="시스템";

    $RESULT = "success";

    if(!empty($site_code)){
        $OAL = new OpenAPI($site_code);

        if(method_exists($OAL->lib,'sendOrdReqDelivery')){
            $result = $OAL->lib->sendOrdReqDelivery($data);

            $RESULT = $result->resultCode;
            if($result->resultCode=="success" || $result->resultCode=="200"){
                $status_message = "연동 성공[s]";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                $RESULT = "success";
            }else{
                $status_message = "연동 실패[s][".$result->message."]";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                $RESULT = "fail";
            }
        }
    }

    return $RESULT;
}

/**
 * 배송준비중 처리
 */

function sendOrdRepackaging($od_data){
    global $HEAD_OFFICE_CODE;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[co_delivery_no] = $od_data[co_delivery_no];

    $data[ic_date] =  $od_data[ic_date];
    $data[due_date] =  $od_data[due_date];
    $data[re_confirm] = $od_data[re_confirm];
    $pre_status = $od_data[status];

    $status = ORDER_STATUS_DELIVERY_READY;
    $admin_message="시스템";

    if(!empty($site_code)){
        //추가구성상품이 없는경우 null처리

        if($addPrdYn == 'N'){
            $addPrdNo = 'null';
        }

        $OAL = new OpenAPI($site_code);

        if($pre_status == ORDER_STATUS_CANCEL_APPLY){

            $data[ClaimConfirm] = 'false';
            switch ($od_data[reason_code]){
                case 'MCC':
                    $data[Comment] = '주문제작 취소불가';
                    break;

                case 'NCP':
                    $data[Comment] = '취소불가상품(상품페이지참조)';
                    break;

                case 'DR':
                    $data[Comment] = '포장완료/배송대기';
                    break;

                case 'ETC':
                    $data[Comment] = '기타';
                    break;
            }

            if(method_exists($OAL->lib,'getConfirmOrderCancel')){
                $result = $OAL->lib->getConfirmOrderCancel($data);
                $RESULT = $result->resultCode;

                if($result->resultCode=="success" || $result->resultCode=="200"){
                    $status_message = "연동 성공";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }else{
                    $status_message = "연동 실패[".$result->message."]";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }
            }
        }else{

            if(method_exists($OAL->lib,'sendOrdRepackaging')){
                $result = $OAL->lib->sendOrdRepackaging($data);
                $RESULT = $result->resultCode;

                if($result->resultCode=="success" || $result->resultCode=="200"){
                    $status_message = "연동 성공";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }else{
                    $status_message = "연동 실패[".$result->message."]";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }
            }
        }
    }else{
        $RESULT = "success";
    }

    return $RESULT;
}

/**
 * 반품확인 처리
 */

function sendOrdReqReturnComplete($od_data){
    global $HEAD_OFFICE_CODE;
    $db = new Database;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[co_delivery_no] = $od_data[co_delivery_no];
    $data[return_product_state] = $od_data[return_product_state];
    $data[co_claim_group] = $od_data[co_claim_group];
    $data[co_claim_no] = $od_data[co_claim_no];

    $data[quick] = $od_data[quick];
    $data[di_date] = $od_data[di_date];

    $tmp_invoice_no = explode(",",$od_data[invoice_no]);
    $data[invoice_no] = $tmp_invoice_no[0];

    if($od_data[return_yn] == 'Y'){
        $sql = "SELECT * FROM shop_order_detail_deliveryinfo where oid = '".$oid."' and order_type= '1' ";
    }else{
        $sql = "SELECT * FROM shop_order_detail_deliveryinfo where oid = '".$oid."' and order_type= '2' ";
    }
    $db->query($sql);
    $db->fetch();

    $data[rname] = $db->dt[rname];
    $data[rtel] = $db->dt[rtel];
    $data[rmobile] = $db->dt[rmobile];
    $data[zip] = $db->dt[zip];
    $data[addr1] = $db->dt[addr1];
    $data[addr2] = $db->dt[addr2];
    $data[clmCrtTp] = '02';

    $status = ORDER_STATUS_RETURN_COMPLETE;
    $admin_message="시스템";

    if(!empty($site_code)){
        //추가구성상품이 없는경우 null처리

        if($addPrdYn == 'N'){
            $addPrdNo = 'null';
        }

        $OAL = new OpenAPI($site_code);

        if($site_code == 'gmarket'){
            //지마켓의 경우 환불유보 해제 승인 다음날 자동 환불 처리 함
            if(method_exists($OAL->lib,'sendOrdReserveRefund')){
                $result = $OAL->lib->sendOrdReserveRefund($data);

                $RESULT = $result->resultCode;

                if($result->resultCode=="success" || $result->resultCode=="200"){
                    $status_message = "연동 성공[반품유보해제]";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);

                }else{
                    $status_message = "연동 실패[반품유보해제][".$result->message."]";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }
            }
        }else{
            if(method_exists($OAL->lib,'sendOrdReqReturnComplete')){
                $result = $OAL->lib->sendOrdReqReturnComplete($data);

                $RESULT = $result->resultCode;

                if($result->resultCode=="success" || $result->resultCode=="200"){
                    $status_message = "연동 성공";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }else{
                    $status_message = "연동 실패[".$result->message."]";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }
            }
        }
    }else{
        $RESULT = "success";
    }

    return $RESULT;
}

/**
 * 반품 유보 처리
 *
 */
function sendOrdReturnReserve($od_data){
    global $HEAD_OFFICE_CODE;

    $db = new Database;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $status = $od_data[status];

    $data[od_ix] = $od_data[od_ix];
    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[co_delivery_no] = $od_data[co_delivery_no];
    $data[pid] = $od_data[pid];
    $data[company_id] = $od_data[company_id];
    $data[co_claim_group] = $od_data[co_claim_group];
    $data[co_claim_no] = $od_data[co_claim_no];

    // 지마켓의 경우 수거 정보등록 (수거지시) 이후 교환 및 환불 유보 처리를 해야 하기 때문에 다음 프로세스 추가
    $OAL = new OpenAPI($site_code);
    if(method_exists($OAL->lib,'sendOrdReserveRefund')){
        $result = $OAL->lib->sendOrdReserveRefund($data);

        $RESULT = $result->resultCode;

        if($result->resultCode=="success" || $result->resultCode=="200"){
            $status_message = "연동 성공[반품유보]";
            set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);

        }else{
            $status_message = "연동 실패[반품유보][".$result->message."]";
            set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
        }
    }

}

/**
 * 반품 보류 처리
 *
 */
function sendOrdReturnDefer($od_data){
    global $HEAD_OFFICE_CODE;

    $db = new Database;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $status = $od_data[status];

    $data[od_ix] = $od_data[od_ix];
    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[co_delivery_no] = $od_data[co_delivery_no];
    $data[pid] = $od_data[pid];
    $data[company_id] = $od_data[company_id];
    $data[co_claim_group] = $od_data[co_claim_group];
    $data[co_claim_no] = $od_data[co_claim_no];

    $sql = "SELECT reason_code, status_message FROM shop_order_status where oid = '".$oid."' and od_ix= '".$od_ix."' and status='".ORDER_STATUS_RETURN_DEFER."' order by regdate desc limit 1 ";
    $db->query($sql);
    $db->fetch();

    $data[reason_code] = $db->dt[reason_code];
    $data[status_message] = $db->dt[status_message];

    $OAL = new OpenAPI($site_code);
    if(method_exists($OAL->lib,'sendOrdReturnDefer')){
        $result = $OAL->lib->sendOrdReturnDefer($data);

        $RESULT = $result->resultCode;

        if($result->resultCode=="success" || $result->resultCode=="200"){
            $status_message = "연동 성공[반품보류]";
            set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);

        }else{
            $status_message = "연동 실패[반품보류][".$result->message."]";
            set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
        }
    }

}

/**
 * 반품 취소 처리
 *
 */
function sendOrdReturnCancel($od_data){
    global $HEAD_OFFICE_CODE;

    $db = new Database;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $status = $od_data[status];

    $data[od_ix] = $od_data[od_ix];
    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[co_delivery_no] = $od_data[co_delivery_no];
    $data[pid] = $od_data[pid];
    $data[company_id] = $od_data[company_id];
    $data[co_claim_group] = $od_data[co_claim_group];
    $data[co_claim_no] = $od_data[co_claim_no];


    $OAL = new OpenAPI($site_code);
    if(method_exists($OAL->lib,'sendOrdReturnCancel')){
        $result = $OAL->lib->sendOrdReturnCancel($data);

        $RESULT = $result->resultCode;

        if($result->resultCode=="success" || $result->resultCode=="200"){
            $status_message = "연동 성공[반품취소]";
            set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);

        }else{
            $status_message = "연동 실패[반품취소][".$result->message."]";
            set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
        }
    }
}

/**
 * 교환 승인 처리 (물품 회수 요청 단계)
 */
function sendOrdExchangeIng($od_data){
    global $HEAD_OFFICE_CODE;

    $db = new Database;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $status = $od_data[status];

    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[co_delivery_no] = $od_data[co_delivery_no];
    $data[pid] = $od_data[pid];
    $data[company_id] = $od_data[company_id];
    $data[co_claim_group] = $od_data[co_claim_group];
    $data[co_claim_no] = $od_data[co_claim_no];

    $data[quick] = $od_data[quick];

    $data[di_date] = $od_data[di_date];

    $tmp_invoice_no = explode(",",$od_data[invoice_no]);
    $data[invoice_no] = $tmp_invoice_no[0];

    if($od_data[return_yn] == 'Y'){
        $sql = "SELECT * FROM shop_order_detail_deliveryinfo where oid = '".$oid."' and order_type= '1' ";
    }else{
        $sql = "SELECT * FROM shop_order_detail_deliveryinfo where oid = '".$oid."' and order_type= '2' ";
    }
    $db->query($sql);
    $db->fetch();

    $data[rname] = $db->dt[rname];
    $data[rtel] = $db->dt[rtel];
    $data[rmobile] = $db->dt[rmobile];
    $data[zip] = $db->dt[zip];
    $data[addr1] = $db->dt[addr1];
    $data[addr2] = $db->dt[addr2];

    if(!empty($site_code)){

        //추가구성상품이 없는경우 null처리
        if($addPrdYn == 'N'){
            $addPrdNo = 'null';
        }

        $OAL = new OpenAPI($site_code);
        if(method_exists($OAL->lib,'sendOrdReqAddReturnPickup')){
            $result = $OAL->lib->sendOrdReqAddReturnPickup($data);

            $RESULT = $result->resultCode;

            if($result->resultCode=="success" || $result->resultCode=="200"){
                $status_message = "연동 성공";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);

                //교환,반품 수거지시 이후 유보 설정 진행 JK160331
                if($site_code == 'gmarket'){
                    if($od_data[return_yn] == 'Y'){
                        if(function_exists('sellerToolUpdateOrderStatus')){
                            sellerToolUpdateOrderStatus(ORDER_STATUS_RETURN_RESERVE,$od_ix,'true');
                        }
                    }else{
                        if(function_exists('sellerToolUpdateOrderStatus')){
                            sellerToolUpdateOrderStatus(ORDER_STATUS_EXCHANGE_RESERVE,$od_ix,'true');
                        }
                    }
                }
            }else{
                $status_message = "연동 실패[".$result->message."]";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
            }
        }

    }else{
        $RESULT = "success";
    }
}

/**
 * 교환 유보 처리
 */

function sendOrdExchangeReserve($od_data){
    global $HEAD_OFFICE_CODE;

    $db = new Database;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $status = $od_data[status];

    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[co_delivery_no] = $od_data[co_delivery_no];
    $data[pid] = $od_data[pid];
    $data[company_id] = $od_data[company_id];
    $data[co_claim_group] = $od_data[co_claim_group];
    $data[co_claim_no] = $od_data[co_claim_no];

    $data[resrve_type] = $od_data[reason_code];

    // 지마켓의 경우 수거 정보등록 (수거지시) 이후 교환 및 환불 유보 처리를 해야 하기 때문에 다음 프로세스 추가
    if(method_exists($OAL->lib,'sendOrdReserveExchange')){
        $result = $OAL->lib->sendOrdReserveExchange($data);

        $RESULT = $result->resultCode;

        if($result->resultCode=="success" || $result->resultCode=="200"){
            $status_message = "연동 성공[교환유보]";
            set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);

        }else{
            $status_message = "연동 실패[교환유보][".$result->message."]";
            set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
        }
    }
}

/**
 * 교환 지연 예고
 */

function sendExchangeDelay($od_data){
    global $HEAD_OFFICE_CODE;

    $db = new Database;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $data[oid] = $od_data[oid];
    $data[od_ix] = $od_data[od_ix];
    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[co_delivery_no] = $od_data[co_delivery_no];
    $data[pid] = $od_data[pid];
    $data[company_id] = $od_data[company_id];
    $data[co_claim_group] = $od_data[co_claim_group];
    $data[co_claim_no] = $od_data[co_claim_no];

    if(!empty($site_code)){

        //추가구성상품이 없는경우 null처리
        if($addPrdYn == 'N'){
            $addPrdNo = 'null';
        }

        $OAL = new OpenAPI($site_code);
        if(method_exists($OAL->lib,'doExchangeDelay')){
            $result = $OAL->lib->doExchangeDelay($data);

            $RESULT = $result->resultCode;

            if($result->resultCode=="success" || $result->resultCode=="200"){
                $status_message = "연동 성공";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
            }else{
                $status_message = "연동 실패[".$result->message."]";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
            }
        }
    }else{
        $RESULT = "success";
    }
}

/**
 * 교환상품 회수완료 처리
 */

function sendOrdExchangeAccept($od_data){
    global $HEAD_OFFICE_CODE;

    $db = new Database;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[co_delivery_no] = $od_data[co_delivery_no];
    $data[pid] = $od_data[pid];
    $data[company_id] = $od_data[company_id];
    $data[co_claim_group] = $od_data[co_claim_group];
    $data[co_claim_no] = $od_data[co_claim_no];

    $data[quick] = $od_data[quick];

    $data[di_date] = $od_data[di_date];

    $tmp_invoice_no = explode(",",$od_data[invoice_no]);
    $data[invoice_no] = $tmp_invoice_no[0];
    $data[clmCrtTp] = '03';

    $sql = "SELECT * FROM shop_order_detail_deliveryinfo where oid = '".$oid."' and order_type= '4' ";

    $db->query($sql);
    $db->fetch();

    $data[rname] = $db->dt[rname];
    $data[rtel] = $db->dt[rtel];
    $data[rmobile] = $db->dt[rmobile];
    $data[zip] = $db->dt[zip];
    $data[addr1] = $db->dt[addr1];
    $data[addr2] = $db->dt[addr2];

    if(!empty($site_code)){

        if($site_code =='interpark_api'){//인터파크만 처리되어 있음 (게다가 인터파크는 반품과 교환을 같이 쓰고있어 이슈 있음 다른제휴사는 반품처리 되고 있었음)
            //추가구성상품이 없는경우 null처리
            if($addPrdYn == 'N'){
                $addPrdNo = 'null';
            }

            $OAL = new OpenAPI($site_code);
            if(method_exists($OAL->lib,'sendOrdReqReturnComplete')){
                $result = $OAL->lib->sendOrdReqReturnComplete($data);

                $RESULT = $result->resultCode;

                if($result->resultCode=="success" || $result->resultCode=="200"){
                    $status_message = "연동 성공";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }else{
                    $status_message = "연동 실패[".$result->message."]";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }
            }
        }else{
            $RESULT = "success";
        }
    }else{
        $RESULT = "success";
    }
}

/**
 * 교환상품 재발송 처리
 */

function sendOrdReqExchangeDelivery($od_data){
    global $HEAD_OFFICE_CODE;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $data[oid] = $oid;
    $data[od_ix] = $od_ix;
    $data[claim_delivery_od_ix] = $od_data[claim_delivery_od_ix];

    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[pid] = $od_data[pid];
    $data[company_id] = $od_data[company_id];
    $data[co_delivery_no] = $od_data[co_delivery_no];
    $data[co_claim_group] = $od_data[co_claim_group];
    $data[co_claim_no] = $od_data[co_claim_no];

    if($od_data[option_id]=="0"){
        $data[option_id] = "1";
    }else{
        $data[option_id] = $od_data[option_id];
    }

    $data[option_text] = $od_data[option_text];
    $data[pcnt] = $od_data[pcnt];

    $data[quick] = $od_data[quick];

    $data[di_date] = $od_data[di_date];

    $tmp_invoice_no = explode(",",$od_data[invoice_no]);
    $data[invoice_no] = $tmp_invoice_no[0];

    $status = ORDER_STATUS_DELIVERY_ING;
    $admin_message="시스템";

    if(!empty($site_code)){
        //추가구성상품이 없는경우 null처리

        if($addPrdYn == 'N'){
            $addPrdNo = 'null';
        }

        $OAL = new OpenAPI($site_code);

        if($site_code =='gmarket'){//g마켓의 경우 교환상품 재배송 시 유보 해제 프로세스를 진행 해야 함
            if(method_exists($OAL->lib,'sendOrdReserveExchange')){
                $result = $OAL->lib->sendOrdReserveExchange($data);

                $RESULT = $result->resultCode;

                if($result->resultCode=="success" || $result->resultCode=="200"){
                    $status_message = "연동 성공[교환유보해제]";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);

                }else{
                    $status_message = "연동 실패[교환유보해제][".$result->message."]";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }
            }
        }



        if(method_exists($OAL->lib,'sendOrdReqExchangeDelivery')){
            $result = $OAL->lib->sendOrdReqExchangeDelivery($data);

            $RESULT = $result->resultCode;

            if($result->resultCode=="success" || $result->resultCode=="200"){
                $status_message = "연동 성공";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
            }else{
                $status_message = "연동 실패[".$result->message."]";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
            }
        }
    }else{
        $RESULT = "success";
    }

    return $RESULT;
}

/**
 * 품절에 의한 취소 요청 처리
 */

function sendOrdReqCancelApply($od_data){
    global $HEAD_OFFICE_CODE;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[co_delivery_no] = $od_data[co_delivery_no];
    $data[co_claim_group] = $od_data[co_claim_group];

    $status = ORDER_STATUS_CANCEL_APPLY;
    $admin_message="시스템";

    if(!empty($site_code)){
        //추가구성상품이 없는경우 null처리

        if($addPrdYn == 'N'){
            $addPrdNo = 'null';
        }

        $OAL = new OpenAPI($site_code);
        if(method_exists($OAL->lib,'sendOrdReqCancelApply')){
            $result = $OAL->lib->sendOrdReqCancelApply($data);
            $RESULT = $result->resultCode;

            if($result->resultCode=="success" || $result->resultCode=="200"){
                $status_message = "연동 성공[API 품절취소]";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
            }else{
                $status_message = "연동 실패[".$result->message."]";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
            }
        }
    }else{
        $RESULT = "success";
    }

    return $RESULT;
}

/**
 * 판매취소/거부 처리
 */

function sendOrdDenySell($od_data){
    global $HEAD_OFFICE_CODE;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[pid] = $od_data[pid];
    $data[oid] = $od_data[oid];
    $data[od_ix] = $od_data[od_ix];

    $status = ORDER_STATUS_CANCEL_COMPLETE;
    $admin_message="시스템";

    if(!empty($site_code)){
        $OAL = new OpenAPI($site_code);
        if(method_exists($OAL->lib,'setDenySell')){
            $result = $OAL->lib->setDenySell($data);
            $RESULT = $result->resultCode;

            if($result->resultCode=="success" || $result->resultCode=="200"){
                $status_message = "연동 성공";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
            }else{
                $status_message = "연동 실패[".$result->message."]";
                set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
            }
        }
    }else{
        $RESULT = "success";
    }

    return $RESULT;
}

/**
 * 출고대기 주문취소 승인 처리
 */

function sendOrdReqCancelComplete($od_data){
    global $HEAD_OFFICE_CODE;

    $site_code = $od_data[order_from];
    $oid = $od_data[oid];
    $od_ix = $od_data[od_ix];
    $pid = $od_data[pid];

    $data[co_oid] = $od_data[co_oid];
    $data[co_od_ix] = $od_data[co_od_ix];
    $data[co_delivery_no] = $od_data[co_delivery_no];
    $data[co_claim_group] = $od_data[co_claim_group];

    $status = ORDER_STATUS_CANCEL_COMPLETE;
    $admin_message="시스템";

    if(!empty($site_code)){
        //추가구성상품이 없는경우 null처리

        if($addPrdYn == 'N'){
            $addPrdNo = 'null';
        }

        $OAL = new OpenAPI($site_code);
        if($site_code == 'gmarket'){
            if(method_exists($OAL->lib,'getConfirmOrderCancel')){
                $result = $OAL->lib->getConfirmOrderCancel($data);
                $RESULT = $result->resultCode;

                if($result->resultCode=="success" || $result->resultCode=="200"){
                    $status_message = "연동 성공";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }else{
                    $status_message = "연동 실패[".$result->message."]";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }
            }

        }else{
            if(method_exists($OAL->lib,'sendOrdReqCancelComplete')){
                $result = $OAL->lib->sendOrdReqCancelComplete($data);
                $RESULT = $result->resultCode;

                if($result->resultCode=="success" || $result->resultCode=="200"){
                    $status_message = "연동 성공";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }else{
                    $status_message = "연동 실패[".$result->message."]";
                    set_order_status($oid,$status,$status_message,$admin_message,$HEAD_OFFICE_CODE,$od_ix,$pid);
                }
            }
        }
    }else{
        $RESULT = "success";
    }

    return $RESULT;
}

/**
 * 상품Qna 등록
 */

function insertProductQna($site_code,$data){
    global $HEAD_OFFICE_CODE,$admininfo;

    $bbs_div = $data['bbs_div'];
    $co_bbs_ix = $data['co_bbs_ix'];
    $co_pid = $data['co_pid'];
    $bbs_subject = $data['bbs_subject'];
    $bbs_contents = $data['bbs_contents'];
    $bbs_name = $data['bbs_name'];
    $pname = $data['pname'];
    $pid = $data['pid'];
    $company_id = $data['company_id'];

    $co_od_ix = $data['co_od_ix'];
    $oid = $data['oid'];
    $msg_type = $data['msg_type'];

    if( ! empty($data['regdate']) ){
        $regdate = "'".$data['regdate']."'";
    }else{
        $regdate = "NOW()";
    }

    $db = new Database;

    $sql="select count(*) as total from shop_product_qna where site_code ='".$site_code."' and co_bbs_ix='".$co_bbs_ix."'";
    $db->query($sql);
    $db->fetch();

    //상품 Qna 없을때
    if($db->dt["total"] == '0'){
        $sql="insert into shop_product_qna(bbs_ix,bbs_div,pid,pname,company_id,ucode,bbs_subject,bbs_name,bbs_id,bbs_pass,bbs_hidden,bbs_email,bbs_contents,bbs_response_title,bbs_response,bbs_re_date,bbs_hit,bbs_re_bool,bbs_re_cnt,response_id,response_date,regdate,old_uid,site_code,co_bbs_ix,co_pid,co_od_ix,oid,msg_type) values('','$bbs_div','$pid','$pname','$company_id','$ucode','$bbs_subject','$bbs_name','$bbs_id','$bbs_pass','$bbs_hidden','$bbs_email','$bbs_contents','$bbs_response_title','$bbs_response','$bbs_re_date','$bbs_hit','$bbs_re_bool','$bbs_re_cnt','$response_id','$response_date',$regdate,'$old_uid','$site_code','$co_bbs_ix','$co_pid','$co_od_ix','$oid','$msg_type')";
        $db->query($sql);
        $db->fetch();
    }
}

/**
 * 제휴 상품 Qna 답변 처리!
 */
function sellerToolAnswerProductQna($bbs_ix){
    global $sendUseSiteCode;

    $db = new Database;

    $db->query("select * from ".TBL_SHOP_PRODUCT_QNA." WHERE bbs_ix='".$bbs_ix."'");
    $db->fetch();
    $qna_data=$db->dt;

    if(!empty($qna_data[site_code]) && in_array($qna_data[site_code],$sendUseSiteCode) && $qna_data[bbs_re_bool]=='Y'){

        if($qna_data[msg_type] == 'B'){

            $RESULT = sendAnswerProductQna($qna_data);

            //차후 로직 변경 ㅜ
            if($RESULT=="response_fail"){
                $RESULT="";
                $RESULT = sendAnswerProductQna($qna_data);
            }

            if($RESULT=="response_fail"){
                $RESULT="";
                $RESULT = sendAnswerProductQna($qna_data);
            }
        }else if($qna_data[msg_type] == 'E'){
            $RESULT = sendAnswerEmergencyMsg($qna_data);

            //차후 로직 변경 ㅜ
            if($RESULT=="response_fail"){
                $RESULT="";
                $RESULT = sendAnswerEmergencyMsg($qna_data);
            }

            if($RESULT=="response_fail"){
                $RESULT="";
                $RESULT = sendAnswerEmergencyMsg($qna_data);
            }
        }
    }
}


/**
 *  제휴 상품 Qna 답변 처리!

 */

function sendAnswerProductQna($qna_data){
    global $HEAD_OFFICE_CODE;

    $site_code = $qna_data[site_code];

    $data[bbs_ix] = $qna_data[bbs_ix];
    $data[co_bbs_ix] = $qna_data[co_bbs_ix];
    $data[co_pid] = $qna_data[co_pid];
    $data[co_od_ix] = $qna_data[co_od_ix];
    $data[bbs_subject] = $qna_data[bbs_subject];
    $data[bbs_contents] = $qna_data[bbs_contents];
    $data[response_date] = $qna_data[response_date];
    $data[bbs_response_title] = $qna_data[bbs_response_title];
    $data[bbs_response] = $qna_data[bbs_response];
    $data[bbs_re_bool] = $qna_data[bbs_re_bool];

    if(!empty($site_code)){
        $OAL = new OpenAPI($site_code);
        if(method_exists($OAL->lib,'sendAnswerProductQna')){
            $result = $OAL->lib->sendAnswerProductQna($data);
            $RESULT = $result->resultCode;
        }
    }else{
        $RESULT = "success";
    }

    return $RESULT;
}


/**
 *  제휴 상품 긴급메시지 답변 처리!

 */

function sendAnswerEmergencyMsg($qna_data){
    global $HEAD_OFFICE_CODE;

    $site_code = $qna_data[site_code];

    $data[bbs_ix] = $qna_data[bbs_ix];
    $data[co_bbs_ix] = $qna_data[co_bbs_ix];
    $data[co_pid] = $qna_data[co_pid];
    $data[co_od_ix] = $qna_data[co_od_ix];
    $data[bbs_subject] = $qna_data[bbs_subject];
    $data[bbs_contents] = $qna_data[bbs_contents];
    $data[response_date] = $qna_data[response_date];
    $data[bbs_response_title] = $qna_data[bbs_response_title];
    $data[bbs_response] = $qna_data[bbs_response];
    $data[bbs_re_bool] = $qna_data[bbs_re_bool];

    if(!empty($site_code)){
        $OAL = new OpenAPI($site_code);
        if(method_exists($OAL->lib,'sendAnswerEmergencyMsg')){
            $result = $OAL->lib->sendAnswerEmergencyMsg($data);
            $RESULT = $result->resultCode;
        }
    }else{
        $RESULT = "success";
    }

    return $RESULT;
}

/**
 * 제휴사 미수령 신고 데이터 가져오기
 *
 */

function insertUnreceivedClaim($site_code,$data){
    global $HEAD_OFFICE_CODE,$admininfo;

    $db = new Database;
    $sdb = new Database;

    $co_oid = $data["co_oid"];
    $co_od_ix = $data["co_od_ix"];
    $claim_status = $data["claim_status"];
    $claim_type = $data["claim_type"];
    $claim_message = $data["claim_message"];
    $claim_date = $data["claim_date"];
    $quick_name = $data["quick_name"];
    $invoice_no = $data["invoice_no"];
    $dc_date = $data["dc_date"];
    $return_message = $data["return_message"];

    $sql = "select * from shop_order_unreceived_claim where co_oid = '".$co_oid."' and co_od_ix = '".$co_od_ix."' and site_code = '".$site_code."' and claim_status = '".$claim_status."' ";
    $db->query($sql);

    if(empty($db->total)){

        $sql = "select * from ".TBL_SHOP_ORDER_DETAIL." where co_oid = '".$co_oid."' and co_od_ix = '".$co_od_ix."' and order_form = '".$site_code."' ";
        $db2->query($sql);
        $db2->fetch();

        $oid = $db2->dt[oid];
        $od_ix = $db2->dt[od_ix];
        $pid = $db2->dt[pid];
        $company_id = $db->dt[company_id];
        if(empty($dc_date)){
            $dc_date = $db2->dt[dc_date];
        }

        $sql = "inster into shop_order_unreceived_claim 
					(oid,od_ix,pid,company_id,claim_status,claim_type,claim_message,dc_date,claim_date,site_code,co_oid,co_od_ix) 
				values 
					('".$oid."','".$od_ix."','".$pid."','".$company_id."','".$claim_status."','".$claim_type."','".$claim_message."','".$dc_date."','".$claim_date."','".$site_code."','".$co_oid."','".$co_od_ix."')";
        $db2->query($sql);
    }

}

/**
 * 미수령 신고 철회
 **/

function RetractUnreceivedClaim($retract_data){
    global $HEAD_OFFICE_CODE;

    $site_code = $retract_data['site_code'];




    if(!empty($site_code)){
        $OAL = new OpenAPI($site_code);
        if(method_exists($OAL->lib,'sendCancelUnreceivedClaim')){
            $result = $OAL->lib->sendCancelUnreceivedClaim($retract_data);
            $RESULT = $result->resultCode;
        }
    }else{
        $RESULT = "success";
    }

    return $RESULT;
}


/**
 * pcode로 상품정보 가져오기
 *
 * @param {string} pcode 판매자상품코드
 * @return {array} shop_product_info 상품정보
 */

/*
function getPrdByPcode($pcode){
    $db = new Database;
    $sql = "select * from shop_product where pcode = '".$pcode."'";
    $db->query($sql);
    if($db->total){
        $shop_product_info = $db->fetch();

        //TODO:test data 실서버 적용시 제거 업체를 (주)스타일스토리로 표시
        $shop_product_info[admin] = "3444fde7c7d641abc19d5a26f35a12cc";

        $sql = "select com_name from common_company_detail where company_id = '".$shop_product_info[admin]."'";
        $db->query($sql);
        if($db->total){
            $result = $db->fetch();
            $shop_product_info[com_name] = $result[com_name];
        }

    }else{
        $shop_product_info = NULL;
    }
    //TODO:test data
    $shop_product_info[admin] = "3444fde7c7d641abc19d5a26f35a12cc";
    $shop_product_info[com_name] = "스타일스토리";

    return $shop_product_info;
}
*/

/**
 * 주문 할인정보 가져오기
 *
 * @param {string} oid 주문번호
 */
/*
function getDiscountTotal($oid){
    $db = new Database;
    $sql = "select * from sellertool_order_info where order_id = '".$oid."'";
    $db->query($sql);
    if($db->total){
        $result = $db->fetchAll();
        foreach($result as $rt):
            $seller_discount += $rt[sellerDscPrc];
            $market_discount += $rt[tmallDscPrc];
        endforeach;
        $return[seller_discount] = $seller_discount;
        $return[market_discount] = $market_discount;


    }
    if($seller_discount == 0 && $market_discount == 0){
        return NULL;
    }else{
        return $return;
    }

}
*/

/**
 * 부분 발송 처리
 */
/*
function sendOrdReqPartDelivery($site_code, $sendDt, $dlvMthdCd, $dlvEtprsCd = 'null', $invcNo = 'null', $dlvNo, $ordNo, $ordPrdSeq){
   //테스트 필요
   return NULL;

   if(!empty($site_code)){
       $OAL = new OpenAPI($site_code);
       $result = $OAL->lib->sendOrdReqPartDelivery($sendDt, $dlvMthdCd, $dlvEtprsCd, $invcNo, $dlvNo, $ordNo, $ordPrdSeq);
   }else{
       $result = NULL;
   }

   return $result;
}
*/

/**
 * 판매불가 처리
 */
/*
function sendReqRejectOrder(){

}
*/


//****************************************************************** 주문 관련 함수 END ************************************************************************************************//

/**
 * 카테고리 가져오기
 */

function getLinkCategory($site_code, $category_text ="기본카테고리 선택", $object_name="cid", $onchange_handler="", $depth=0, $cid="")
{


    $OAL = new OpenAPI($site_code);
    if($depth == 0){
        $category_infos = $OAL->lib->getSubCategory();
    }else{
        if($cid){
            $category_infos = $OAL->lib->getSubCategory($cid);
        }
    }
    //print_r($category_infos);
    //exit;

    //echo $depth."<br>";



    if (count($category_infos) > 0){
        $mstring = "<select name='$object_name' class='sellertool_category' depth='$depth' $onchange_handler style='width:140px;font-size:12px;'>\n";
        $mstring = $mstring."<option value=''>$category_text</option>\n";
        for($i=0; $i < count($category_infos); $i++){
            $category_info = (array)$category_infos[$i];

            if(substr($cid,0,($category_info[depth]+1)*3) == substr($category_info[disp_no],0,($category_info[depth]+1)*3)){

                $mstring = $mstring."<option value='".$category_info[disp_no]."' selected>".$category_info[disp_name]."</option>\n";

            }else{
                $mstring = $mstring."<option value='".$category_info[disp_no]."' >".$category_info[disp_name]."</option>\n";
            }
        }
    }else{
        $mstring = "<select name='$object_name' class='sellertool_category' depth='".$depth."' $onchange_handler validation=false  style='width:140px;font-size:12px;'>\n";
        $mstring = $mstring."<option value=''> $category_text</option>\n";
    }

    $mstring = $mstring."</Select>\n";

    return $mstring;
}

function getLinkItemType($site_code, $category_text ="기본품목분류 선택", $object_name="cid", $onchange_handler="", $depth=0, $cid="")
{


    $OAL = new OpenAPI($site_code);
    if($depth == 0){
        $category_infos = $OAL->lib->getSubItemType();
    }else{
        if($cid){
            $category_infos = $OAL->lib->getSubItemType($cid);
        }
    }
    //print_r($category_infos);
    //exit;

    //echo $depth."<br>";



    if (count($category_infos) > 0){
        $mstring = "<select name='$object_name' class='sellertool_category' depth='$depth' $onchange_handler style='width:140px;font-size:12px;'>\n";
        $mstring = $mstring."<option value=''>$category_text</option>\n";
        for($i=0; $i < count($category_infos); $i++){
            $category_info = (array)$category_infos[$i];

            if(substr($cid,0,($category_info[depth]+1)*3) == substr($category_info[disp_no],0,($category_info[depth]+1)*3)){

                $mstring = $mstring."<option value='".$category_info[disp_no]."' selected>".$category_info[disp_name]."</option>\n";

            }else{
                $mstring = $mstring."<option value='".$category_info[disp_no]."' >".$category_info[disp_name]."</option>\n";
            }
        }
    }else{
        $mstring = "<select name='$object_name' class='sellertool_category' depth='".$depth."' $onchange_handler validation=false  style='width:140px;font-size:12px;'>\n";
        $mstring = $mstring."<option value=''> $category_text</option>\n";
    }

    $mstring = $mstring."</Select>\n";

    return $mstring;
}

/**
 * 등록 로그 삭제
 * 수동 삭제 or 재시도 후 삭제 처리관련
 *
 * @param {int}srl_ix 로그 인덱스 값
 */
function delete_reg_log($srl_ix){

    $db = new Database();
    $db2 = new Database();

    $sql = "select * from sellertool_regist_relation where srl_ix = '".$srl_ix."' ";
    $db->query($sql);
    $db->fetch();
    $site_code = $db->dt[site_code];
    $pid = $db->dt[pid];
    if($site_code == 'interpark_api'){

        $sql = "select * from sellertool_regist_fail where pid = '".$pid."' and site_code = '".$site_code."'";
        $db->query($sql);
        if($db->total){
            for($i=0; $i < $db->total; $i++){
                $db->fetch($i);

                if($site_code == 'interpark_api'){
                    $xml_data = str_replace('&dataUrl=http://'.$_SERVER[HTTP_HOST].'','',$db->dt[data_url]);
                    $xml_data = $_SERVER["DOCUMENT_ROOT"].$xml_data;
                    unlink($xml_data);

                }
                $sql = "delete from sellertool_regist_fail where sf_ix = '".$db->dt[sf_ix]."'";
                $db2->query($sql);

            }
        }
    }

    $db->query("delete from sellertool_regist_relation where srl_ix = '".$srl_ix."'");
    return true;
}

/**
 * 단일상품조회-> 페이지 업데이트시 사용함
 *
 * 빠름빠름빠름
 *
 * 쿠폰적용여부필드가 없음. 버그인지 뭔지 모르겠는데 답변안줌 11번가
 *
 * @param {string} site_code 제휴사코드
 * @param {int} targetCode 제휴사 상품코드
 */
function getProductInfo($site_code,$targetCode){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getProductInfo($targetCode);
    //print_r($result);
    $return = updateProductStatus($result,$targetCode);
    return $return;
}

/**
 * 다중상품조회
 *
 * 느림 리미트 작게줘도 느림. 안씀
 */
function getProductsInfo($site_code,$condition){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getProductsInfo($condition);
}

/**
 * 제휴사 상품상태 db업데이트
 *
 * @param {obj} $data 상품조회결과값
 */
function updateProductStatus($data,$targetCode){

    if($data->prdNo == $targetCode){
        $db = new Database;
        if($data->selStatCd == 103){
            $target_display = 'onsale';
        }else if($data->selStatCd == 105){
            $target_display = 'stop';
        }
        if($data->cuponcheck == 'Y'){
            $coupon_yn = 'Y';
        }else{
            $coupon_yn = 'N';
        }
        $sql = "update sellertool_regist_relation set target_price = '".$data->selPrc."', target_display = '".$target_display."', target_coupon_yn = '".$coupon_yn."' where result_pno = '".$targetCode."'";
        $db->query($sql);
        return "success";
    }else{
        //error
        return "fail";
    }


}
/**
 * 제휴사 사이트코드 얻기
 * - 연동결과테이블에서 seq값으로
 *
 * @param {int} seq 연동결과테이블시퀀스
 * @return {string} 제휴사코드
 */
function getSiteCodeBySeq($seq){
    $db = new Database;
    $sql = "select site_code from sellertool_regist_relation where srl_ix = '".$seq."'";
    $db->query($sql);

    if($db->total){
        $result = $db->fetch();

        return $result[site_code];
    }else{
        //TODO:에러처리
    }
}

/**
 * 제휴사 상품코드 얻기
 * - 연동결과테이블에서 seq값으로
 *
 * @param {int} seq 연동결과테이블시퀀스
 * @return {string} 제휴사 상품코드
 */
function getProductCodeBySeq($seq){
    $db = new Database;
    $sql = "select result_pno from sellertool_regist_relation where srl_ix = '".$seq."'";
    $db->query($sql);

    if($db->total){
        $result = $db->fetch();

        return $result[result_pno];
    }else{
        //TODO:에러처리
    }
}

/**
 * 제휴사 상품가격 얻기
 * - 연동결과테이블에서 seq값으로
 *
 * @param {int} seq 연동결과테이블시퀀스
 * @param {int} 제휴사 상품가격
 */
function getPriceBySeq($seq){
    $db = new Database;
    $sql = "select target_price from sellertool_regist_relation where srl_ix = '".$seq."'";
    $db->query($sql);

    if($db->total){
        $result = $db->fetch();

        return $result[target_price];
    }else{
        //TODO:에러처리
    }
}
/**
 * 상품가격수정
 *
 * @param {string} site_code
 * @param {string} targetCode 11번가 상품코드
 * @param {int} newPrice 수정된 가격
 */
function editPrice($site_code, $targetCode, $newPrice){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->editPrice($targetCode,$newPrice);

    if($result->resultCode == 'success' || $result->resultCode=="200"){
        $db = new Database;
        $db->query("update sellertool_regist_relation set target_price = '".$newPrice."' where result_pno = '".$targetCode."'");
    }

    return $result;
}

/**
 * 상품가격수정 + 즉시할인 수정
 *
 * @param {string} site_code
 * @param {string} targetCode 11번가 상품코드
 * @param {int} newPrice 수정된 가격
 * @param {array} coupon 쿠폰정보배열
 */
function editPriceCoupon($site_code, $targetCode, $newPrice, $coupon){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->editPriceCoupon($targetCode,$newPrice,$coupon);

    return $result;
}

/**
 * 판매중지
 *
 * @param {string} site_code
 * @param {string} targetCode 11번가 상품코드
 */
function stopDisplay($site_code, $targetCode){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->stopDisplay($targetCode);
    if($result->resultCode == 'success' || $result->resultCode=="200"){
        $db = new Database;
        $db->query("update sellertool_regist_relation set target_display = 'stop' where result_pno = '".$targetCode."'");
    }
    return $result;
}

/**
 * 판매중지 해제
 *
 * @param {string} site_code
 * @param {string} targetCode 11번가 상품코드
 */
function restartDisplay($site_code, $targetCode){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->restartDisplay($targetCode);
    if($result->resultCode == 'success' || $result->resultCode=="200"){
        $db = new Database;
        $db->query("update sellertool_regist_relation set target_display = 'onsale' where result_pno = '".$targetCode."'");
    }
    return $result;

}


/**
 * 우편번호 조회
 *
 * @param {string} site_code
 * @param {string} search 검색어
 */
function getZipCode($site_code, $search){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getZipCode($search);

    return (array)$result;

}

/**
 * 출고지 주소 등록
 *
 * @param {string} site_code
 * @param {array} data
 */
function registOutAddress($site_code, $data){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->registOutAddress($data);

    return $result;
}

/**
 * 출고지 주소 수정
 *
 * @param {string} site_code
 * @param {array} data
 */
function updateOutAddress($site_code, $data){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->updateOutAddress($data);

    return $result;
}

/**
 * 출고지 주소 상세조회
 *
 * @param {string} site_code
 * @return {array} list
 */
function getOutAddressInfoList($site_code){
    if(!$site_code) return ;
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getOutAddress();

    $list = "";
    $key = 0;
    foreach($result as $rt):
        $subResult = $OAL->lib->getOutAddressInfo($rt->addrSeq);
        $list[$key] = (array)$subResult;

        //11번가의 경우 상세주소에서 우편번호로 연결된 주소는 안주기때문에 따로 추가함
        if($site_code == '11st'){
            $list[$key][addr] = str_replace($list[$key][dtlsAddr],"",$rt->addr);
        }
        $key++;
    endforeach;

    return $list;
}


/**
 * 출고지 주소 조회 셀렉트박스 리턴
 * 12.09.03 bgh
 *
 * @param {string} site_code
 * @return {string} selectbox html


 */
function getOutAddress($site_code){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getOutAddress();

    $return_str = "<select name=addr_seq_out>";
    foreach($result as $rt):
        $return_str .= "<option value='".$rt->addrSeq."'>".$rt->addr."</option>";
    endforeach;
    $return_str .="</select>";

    return $return_str;
    /*
    getOutAddress return obj

    [addr] => 경기 고양시 일산동구 설문동 796-9  이앤드피
    [addrNm] => 설문동
    [addrSeq] => 4
    [gnrlTlphnNo] => 031-901-2741
    [memNo] => 12244407
    [prtblTlphnNo] => 010-2484-8284
    [rcvrNm] => 이앤드피
    */
}

/**
 * 반품/교환지 주소 등록
 *
 * @param {array} data
 */
function registInAddress($site_code, $data){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->registInAddress($data);

    return $result;
}

/**
 * 반품/교환지 주소 수정
 *
 * @param {array} data
 */
function updateInAddress($site_code, $data){
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->updateInAddress($data);

    return $result;
}

/**
 * 반품/교환지 주소 상세조회
 *
 * @param {string} site_code
 * @return {array} list
 */
function getInAddressInfoList($site_code){
    if(!$site_code) return ;
    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getInAddress();

    $list = "";
    $key = 0;
    foreach($result as $rt):
        $subResult = $OAL->lib->getInAddressInfo($rt->addrSeq);
        $list[$key] = (array)$subResult;

        //11번가의 경우 상세주소에서 우편번호로 연결된 주소는 안주기때문에 따로 추가함
        if($site_code == '11st'){
            $list[$key][addr] = str_replace($list[$key][dtlsAddr],"",$rt->addr);
        }
        $key++;
    endforeach;

    return $list;
}
/**
 * 반품 / 교환지 주소 조회 셀렉트박스 리턴
 * 12.09.03 bgh
 *
 * @param {string} site_code
 * @return {string} selectbox html
 */
function getInAddress($site_code){

    $OAL = new OpenAPI($site_code);
    $result = $OAL->lib->getInAddress();

    $return_str = "<select name=addr_seq_in>";
    foreach($result as $rt):
        $return_str .= "<option value='".$rt->addrSeq."'>".$rt->addr."</option>";
    endforeach;
    $return_str .="</select>";

    return $return_str;
    /*
    getInAddress return obj

    [addr] => 경기 고양시 일산동구 설문동 796-9  이앤드피
    [addrNm] => 설문동
    [addrSeq] => 4
    [gnrlTlphnNo] => 031-901-2741
    [memNo] => 12244407
    [prtblTlphnNo] => 010-2484-8284
    [rcvrNm] => 이앤드피
    */
}



function getSellerToolSiteInfo($site_code="", $property="", $return_type = "selectbox", $subWhere = ""){
    global $admininfo;
    $mdb = new Database;

    if($return_type == "text"){
        $sql = 	"SELECT si.* FROM sellertool_site_info si
					where disp = 1 and site_code = '".$site_code."' ".$subWhere."
					";

        $mdb->query($sql);
        $mdb->fetch();

        $mstring = $mdb->dt[site_name]."(".$mdb->dt[site_domain].") <input type='hidden' name='site_code' value='".$site_code."'>";
    }else if($return_type == "selectbox" || $return_type == ""){
        $sql = 	"SELECT si.* FROM sellertool_site_info si
					where disp = 1 ".$subWhere."
					group by si_ix order by vieworder asc ";

        $mdb->query($sql);

        $mstring = "<select name='site_code' id='site_code' ".$property."	>";
        $mstring .= "<option value=''>쇼핑몰(오픈마켓)</option>";
        if($mdb->total){


            for($i=0;$i < $mdb->total;$i++){
                $mdb->fetch($i);
                if($mdb->dt[site_code] == $site_code){
                    $mstring .= "<option value='".$mdb->dt[site_code]."' selected>".$mdb->dt[site_name]." (".$mdb->dt[site_domain].")</option>";
                }else{
                    $mstring .= "<option value='".$mdb->dt[site_code]."'>".$mdb->dt[site_name]." (".$mdb->dt[site_domain].")</option>";
                }
            }

        }
        $mstring .= "</select>";
    }

    return $mstring;
}


function getSellerToolSiteKeyInfo($si_ix="", $property="", $return_type = "selectbox", $subWhere = ""){
    global $admininfo;
    $mdb = new Database;

    if($return_type == "text"){
        $sql = 	"SELECT si.* FROM sellertool_site_info si
					where disp = 1 and si_ix = '".$si_ix."' ".$subWhere."
					";

        $mdb->query($sql);
        $mdb->fetch();

        $mstring = $mdb->dt[site_name]."(".$mdb->dt[site_domain].") <input type='hidden' name='si_ix' value='".$si_ix."'>";
    }else if($return_type == "selectbox" || $return_type == ""){
        $sql = 	"SELECT si.* FROM sellertool_site_info si
					where disp = 1 ".$subWhere."
					group by si_ix order by vieworder asc ";

        $mdb->query($sql);

        $mstring = "<select name='si_ix' id='si_ix' ".$property."	>";
        $mstring .= "<option value=''>쇼핑몰(오픈마켓)</option>";
        if($mdb->total){


            for($i=0;$i < $mdb->total;$i++){
                $mdb->fetch($i);
                if($mdb->dt[si_ix] == $si_ix){
                    $mstring .= "<option value='".$mdb->dt[si_ix]."' selected>".$mdb->dt[site_name]." (".$mdb->dt[site_domain].")</option>";
                }else{
                    $mstring .= "<option value='".$mdb->dt[si_ix]."'>".$mdb->dt[site_name]." (".$mdb->dt[site_domain].")</option>";
                }
            }

        }
        $mstring .= "</select>";
    }

    return $mstring;
}


function getSellerToolAddInfo($site_code, $selected="",$property=""){
    $mdb = new Database;

    $sql = "SELECT * from sellertool_site_add_info where disp = 'Y' and site_code = '".$site_code."' ";

    $mdb->query($sql);

    $mstring = "<select name='add_info' id='add_info' ".$property."	>";
    $mstring .= "<option value=''>등록옵션 선택</option>";
    if($mdb->total){
        for($i=0;$i < $mdb->total;$i++){
            $mdb->fetch($i);
            if($mdb->dt[site_code] == $selected){
                $mstring .= "<option value='".$mdb->dt[ssai_ix]."' selected>".$mdb->dt[add_info_name]." (".$mdb->dt[site_code].")</option>";
            }else{
                $mstring .= "<option value='".$mdb->dt[ssai_ix]."'>".$mdb->dt[add_info_name]." (".$mdb->dt[site_code].")</option>";
            }
        }
    }
    $mstring .= "</select>";

    return $mstring;
}

function getSellerToolProductDiv($site_code='11st', $selected = "", $property = ""){
    $mdb = new Database;
    $sql = "SELECT * from sellertool_product_div where depth = '0' and site_code = '".$site_code."' ";
    $mdb->query($sql);

    $mstring = "<select name='product_div_code' id='product_div_code' ".$property." >";
    $mstring .= "<option value=''>상품 유형선택</option>";
    if($mdb->total){
        $result = $mdb->fetchAll();
        foreach($result as $rt):
            if($rt[product_div_code]==$selected){
                $mstring .= "<option value='".$rt[product_div_code]."' selected>".$rt[product_div_name]."</option>";
            }else{
                $mstring .= "<option value='".$rt[product_div_code]."'>".$rt[product_div_name]."</option>";
            }
        endforeach;

    }
    $mstring .= "</select>";

    return $mstring;
}

function getSellertoolReceivedCategoryPathByAdmin($cid, $site_code){

    $return = array();

    $cinfo = getSellertoolReceivedCategory($cid,$site_code);

    $cname_ary[ $cinfo['depth'] ] = $cinfo['disp_name'];

    for( $i = $cinfo['depth']; $i > -1 ; $i-- ){
        $cinfo = getSellertoolReceivedCategory( $cinfo['parent_no'] , $cinfo['site_code'] );

        if( ! empty($cinfo['disp_name']) ){
            $cname_ary[ $cinfo['depth'] ] = $cinfo['disp_name'];
        }
    }

    ksort($cname_ary);
    //print_r($cname_ary);
    return implode(" > ",$cname_ary);
}


function getSellertoolReceivedCategory($cid, $site_code){

    $mdb = new Database;

    $sql = "select * from sellertool_received_category where disp_no = '".$cid."' and site_code='".$site_code."' ";
    $mdb->query($sql);
    $mdb->fetch();
    return $mdb->dt;
}


function getSellertoolReceivedItemTypePathByAdmin($cid, $site_code){

    $return = array();

    $cinfo = getSellertoolReceivedItemType($cid,$site_code);

    $cname_ary[ $cinfo['depth'] ] = $cinfo['disp_name'];

    for( $i = $cinfo['depth']; $i > -1 ; $i-- ){
        $cinfo = getSellertoolReceivedItemType( $cinfo['parent_no'] , $cinfo['site_code'] );

        if( ! empty($cinfo['disp_name']) ){
            $cname_ary[ $cinfo['depth'] ] = $cinfo['disp_name'];
        }
    }

    ksort($cname_ary);
    //print_r($cname_ary);
    return implode(" > ",$cname_ary);
}


function getSellertoolReceivedItemType($cid, $site_code){

    $mdb = new Database;

    $sql = "select * from sellertool_received_itemtype where disp_no = '".$cid."' and site_code='".$site_code."' ";
    $mdb->query($sql);
    $mdb->fetch();
    return $mdb->dt;
}


?>
