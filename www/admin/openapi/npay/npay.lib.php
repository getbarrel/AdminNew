<?php
/**
 * npay 상품 API 라이브러리
 */

require_once 'nhnapi-simplecryptlib.php';
require_once 'npay.config.php';
require_once 'npay.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/admin/openapi/standard.object.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/class/database.class';

class Lib_npay extends Call_npay {

    private $db;
    private $site_code;
    private $userInfo;
    private $secret_key;
    private $api_key;
    private $version;
    private $orderVersion;
    private $result;
    private $error;
    private $tmp_img_array;
    private $mall_data_root;
    private $detaillevel;


    public function __construct($site_code = '') {
        $this->db = new Database ();
        $this->site_code = $site_code;
        $this->userInfo = $this->getUserInfo ();
        $this->secret_key = $this->userInfo ['site_pw'];
        $this->api_key = $this->userInfo ['api_key'];
        $this->version = "2.0";
        $this->orderVersion = "4.1";
        $this->detaillevel = "Full";
        $this->qnaVersion = "1.0";
        $this->detaillevel = "Full";

        if(! empty($_SESSION ['layout_config'] ['mall_data_root'])){
            $this->mall_data_root = $_SESSION ['layout_config'] ['mall_data_root'];
        }elseif(! empty($_SESSION ['admininfo'] ['mall_data_root'])){
            $this->mall_data_root = $_SESSION ['admininfo'] ['mall_data_root'];
        }elseif(! empty($layout_config['mall_data_root'])){
            $this->mall_data_root = $layout_config["mall_data_root"];
        }elseif(! empty($admininfo["mall_data_root"])){
            $this->mall_data_root = $admininfo["mall_data_root"];
        }else{
            $sql = "select mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
            $this->db->query($sql);
            $this->db->fetch();

            $this->mall_data_root = $this->db->dt[mall_data_root];
        }
    }


    public function set_error_type($error_type) {
        $this->error_type = $error_type;
    }


    /**
     * 사용자 정보 가져오기
     */
    public function getUserInfo() {
        $sql = "SELECT * 
				FROM sellertool_site_info 
				WHERE site_code = '" . $this->site_code . "'";
        $this->db->query ( $sql );
        if ($this->db->total) {
            return $this->db->fetch ();
        } else {
            $this->error ['code'] = '1001';
            $this->error ['msg'] = '제휴사 정보가 올바르지 않습니다.(npay)';
            $this->printError ();
        }
    }


    /**
     * error 출력
     * #exit 처리할 에러만
     */
    private function printError() {

        /*
        if($this->error_type=="return"){
            return "[".$this->error ['code']."]".$this->error ['msg'];
        }else{
            echo "<script>alert('" . $this->error ['code'] . " : " . $this->error ['msg'] . "');" . $this->error ['script'] . "</script>";
            exit ();
        }
        */
        return false;
    }


    /**
     * 주문 API연동 기본 메시지 구조체
     * @param {string} $service = 서비스 코드
     * @param {string} $operation = 오퍼레이션 코드
     * @return {xml} API연동 기본 메시지 구조체
     */
    public function getBaseCheckoutRequest($service, $operation){

        // NHNAPISCL 객체생성
        $scl = new NHNAPISCL();

        // 타임스탬프를 포맷에 맞게 생성
        $timestamp = $scl->getTimestamp();

        // hmac-sha256서명생성
        $signature = $scl->generateSign($timestamp . $service . $operation, $this->secret_key);
/*
        // phpinfo();
        echo "<xmp>";
        print_r($signature);
        echo "</xmp>";*/



        $requestXmlBody="
						<base:AccessCredentials>
							<base:AccessLicense>".$this->api_key."</base:AccessLicense>
							<base:Timestamp>".$timestamp."</base:Timestamp>
							<base:Signature>".$signature."</base:Signature>
						</base:AccessCredentials>
						<base:RequestID></base:RequestID>
						<base:DetailLevel>".$this->detaillevel."</base:DetailLevel>
						<base:Version>".$this->orderVersion."</base:Version>
						";

        return $requestXmlBody;
    }


    /**
     * 등록결과 로그에 넣기
     *
     * @param {string} pid 상품코드
     * @param {string} add_info_id 등록옵션 시퀀스
     * @param {string} target_cid 등록된 카테고리아이디
     * @param {string} target_name 등록된 카테고리명
     * @param {string} result 등록후 리턴받은 메시지
     */
    public function submitRegistLog($pid,$add_info_id='',$target_cid='',$target_name='',$result){

        $_message = str_replace ("'", "", htmlspecialchars($result->message));
        $_productNo = $result->productNo;
        $_resultCode = $result->resultCode;

        $sql = "select srl_ix from sellertool_regist_relation where site_code='" . $this->site_code . "' and pid='$pid' order by srl_ix desc limit 0,1";
        $this->db->query($sql);

        if($this->db->total){
            $this->db->fetch();
            $sql = "update sellertool_regist_relation set 
				add_info_id='$add_info_id', 
				target_cid='$target_cid', 
				target_name='$target_name', 
				result_pno='$_productNo', 
				result_msg='$_message', 
				result_code='$_resultCode', 
				update_date=NOW()
			where srl_ix='".$this->db->dt[srl_ix]."' and site_code='" . $this->site_code . "'";
            $this->db->query($sql);
        }else{
            $sql = "insert into sellertool_regist_relation (site_code, pid, add_info_id, target_cid, target_name, result_pno, result_msg, result_code, regist_date)values('" . $this->site_code . "','$pid','$add_info_id','$target_cid','$target_name','$_productNo','$_message','$_resultCode',NOW())";
            $this->db->query($sql);
        }

        $sql = "INSERT INTO 
				sellertool_log (site_code, type, pid, add_info_id, target_cid, target_name, result_pno, result_msg, result_code, regist_date)
				values('" . $this->site_code . "','regist','$pid','$add_info_id','$target_cid','$target_name','$_productNo','$_message','$_resultCode',NOW())";
        $this->db->query($sql);
    }


    /**
     * npay 신규 주문 가져오기
     * 옥션의 주문 아이템은 각 상품별로 주문번호를 발급받게 되며 그룹번호를 공통으로 가진다.
     * 따라서 주문번호를 order_detail 에 넣고 그룹번호를 이용해 쇼핑몰 oid를 맞춘다.
     * getChangedProductOrderList => 입금확인 데이터만 가져온다. PAYED
     * getProductOrderInfoList => 주문상세내역 조회
     * @access public
     */
    function getOdrComplete($startTime, $endTime){


        $order_lists1 = $this->getStorefarmOrderList($startTime."00", $endTime."59", 'PAYED');



        //네이버페이, 스토어팜. 주문수집 전 취소할 경우의 주문을 수집하기 위해 프로세스 추가. 신훈식 170419
        //일부 주문 취소시 입금확인 상태에서도 취소요청으로 상태 변경됨
        $order_lists2 = $this->getStorefarmOrderList($startTime."00", $endTime."59", 'CANCEL_REQUESTED');
        $order_lists = array_merge($order_lists1,$order_lists2);

        $fp = fopen($_SERVER["DOCUMENT_ROOT"].'/data/dewytree_data/_logs/npay/npay_IC_' . date('Ymd') . '.log', 'a');
        $fh .= '---------- START - co_od_ix : '. $data['co_od_ix'] .' [' . date('Y-m-d H:i:s') . '] ----------' . chr(13) . chr(13) . PHP_EOL . PHP_EOL;
        $fh .= print_r($order_lists, true) . chr(13) . chr(13) . PHP_EOL . PHP_EOL;
        fwrite($fp, $fh);
        fclose($fp);

        $key = 0;
        $return = array();
        $OrderIDArray = array();
        if( count($order_lists) > 0 ){
            foreach($order_lists as $order){
                $return[$key]["co_oid"]			=		$order['Order']['OrderID'];//주문번호
                $return[$key]["addr1"]				=		str_replace("'","`",$order['ProductOrder']['ShippingAddress']['BaseAddress']);//수취인 주소
                $return[$key]["addr2"]				=		str_replace("'","`",$order['ProductOrder']['ShippingAddress']['DetailedAddress']);//수취인 주소2
                $return[$key]["zip"]				=		$order['ProductOrder']['ShippingAddress']['ZipCode'];//수취인 우편번호
                $return[$key]["rname"]				=		$order['ProductOrder']['ShippingAddress']['Name'];//수취인
                $return[$key]["rtel"]				=		$order['ProductOrder']['ShippingAddress']['Tel2'];//수취인 전화번호
                $return[$key]["rmobile"]			=		$order['ProductOrder']['ShippingAddress']['Tel1'];//수취인 핸드폰번호
                $return[$key]["msg"]				=		str_replace("'","`",$order['ProductOrder']['ShippingMemo']);//배송 메모
                $return[$key]["customs_clearance_number"]		=		$order['ProductOrder']['IndividualCustomUniqueCode'];//고유통관번호
                $return[$key]["btel"]				=		$order['Order']['OrdererTel2'];//주문자 전화번호
                $return[$key]["bname"]				=		$order['Order']['OrdererName'];//주문자명
                $return[$key]["bmobile"]			=		$order['Order']['OrdererTel1'];//주문자 핸드폰 번호
				$return[$key]['PayLocationType']	=		$order['Order']['PayLocationType'];//주문 구분(PC/모바일)
                $return[$key]["regdate"]			=		date('Y-m-d H:i:s',strtotime($order['Order']['OrderDate']));//주문번호생성일
                if( ! empty($order['Order']['PaymentDate'] )){
                    $return[$key]["ic_date"]			=		date('Y-m-d H:i:s',strtotime($order['Order']['PaymentDate']));//주문결제완료일
                }else{
                    $return[$key]["ic_date"]			=	$return[$key]["regdate"];//주문결제완료일
                }

                $return[$key]["co_od_ix"]			=		$order['ProductOrder']['ProductOrderID'];//주문 순번
                $return[$key]["pid"]				=		$order['ProductOrder']['ProductID'];//상품코드
                $return[$key]["pcnt"]				=		$order['ProductOrder']['Quantity'];//수량
                $return[$key]["psprice"]			=		$order['ProductOrder']['UnitPrice'];//상품 판매가(단품)
                $return[$key]["pt_dcprice"]			=		$order['ProductOrder']['TotalProductAmount'];//상품판매가
                $return[$key]["option_id"]			=		$order['ProductOrder']['OptionCode'];//옵션코드;
                $return[$key]["option_text"]		=		str_replace("'","`",$order['ProductOrder']['ProductOption']);
                $return[$key]["f_option_text"]		=		str_replace("'","`",$order['ProductOrder']['ProductOption']); //옵션 코드를 이용하여 DB 조회값을 넣는 게 아닌 네이버에서 받아온 텍스트 정보를 바로 넣기 위함 JK180712
				$return[$key]["MallManageCode"]		=		$order['ProductOrder']['MallManageCode'];

                //네이버페이, 스토어팜. 주문수집 전 취소할 경우의 주문을 수집하기 위해 프로세스 추가. 신훈식. 170419
                if($order['CancelInfo']['RequestChannel'] != "" && $order['CancelInfo']['CancelReason'] != "") {
                    $return[$key]["status"] = "CA";
                    $return[$key]["ca_date"] = date('Y-m-d H:i:s');//취소일자
                    $return[$key]["co_claim_group"] = $order['ProductOrder']["ProductOrderID"];

                    if($order['CancelInfo']['CancelReason']=="INTENT_CHANGED"){
                        $reason = "구매 의사 취소";
                        $reason_code = "NB";//구매의사없음
                    }elseif($order['CancelInfo']['CancelReason']=="COLOR_AND_SIZE"){
                        $reason = "색상 및 사이즈 변경";
                        $reason_code = "SYS";// 색상 및 사이즈 변경
                    }elseif($order['CancelInfo']['CancelReason']=="PRODUCT_UNSATISFIED"){
                        $reason = "서비스 및 상품 불만족";
                        $reason_code = "ETCS";//기타(판매자책임)
                    }elseif($order['CancelInfo']['CancelReason']=="BROKEN"){
                        $reason = "상품 파손";
                        $reason_code = "PD";//파손/하자
                    }elseif($order['CancelInfo']['CancelReason']=="DELAYED_DELIVERY"){
                        $reason = "배송 지연";
                        $reason_code = "DD";//상품미도착
                    }elseif($order['CancelInfo']['CancelReason']=="DROPPED_DELIVERY"){
                        $reason = "배송 누락";
                        $reason_code = "ETCS";//기타(판매자책임)
                    }elseif($order['CancelInfo']['CancelReason']=="INCORRECT_INFO"){
                        $reason = "상품 정보 상이";
                        $reason_code = "PIE";//상품정보 틀림
                    }elseif($order['CancelInfo']['CancelReason']=="WRONG_ORDER"){
                        $reason = "다른 상품 잘못 주문";
                        $reason_code = "OCF";//사이즈,색상잘못선택
                    }elseif($order['CancelInfo']['CancelReason']=="SOLD_OUT"){
                        $reason = "상품 품절";
                        $reason_code = "ETCS";//기타(판매자책임)
                    }elseif($order['CancelInfo']['CancelReason']=="WRONG_DELIVERY"){
                        $reason = "오배송";
                        $reason_code = "ETCS";//기타(판매자책임)
                    }elseif($order['CancelInfo']['CancelReason']=="WRONG_OPTION"){
                        $reason = "색상 등이 다른 상품을 잘못 배송";
                        $reason_code = "ETCS";//기타(판매자책임)
                    }else {
                        $reason = "이유 확인 불가";
                        $reason_code = "UNDI";
                    }

                    $return[$key]["msg"]				=		"[". $order['CancelInfo']['RequestChannel'] . "] [" . $reason . "] " . ( ! empty($order['CancelInfo']['CancelDetailedReason']) ? $order['CancelInfo']['CancelDetailedReason'] : "시스템 취소요청");
                    $return[$key]["reason_code"]		=		$reason_code;
                }

                if( $OrderIDArray[ $order['Order']['OrderID'] ][ $order['ProductOrder']['PackageNumber'] ] ){
                    $delivery_dcprice = 0;
                }else{
                    if( $order['ProductOrder']['DeliveryFeeAmount'] == '무료' ){
                        $delivery_dcprice = 0;
                    }else{
                        $delivery_dcprice = (int)$order['ProductOrder']['DeliveryFeeAmount'] + (int)$order['ProductOrder']['SectionDeliveryFee'];
                    }
                }

                $return[$key]["delivery_dcprice"]		=		$delivery_dcprice;//총배송비 금액

                $OrderIDArray[ $order['Order']['OrderID'] ][ $order['ProductOrder']['PackageNumber'] ] = 1;
                $key++;
            }
        }

        return $return;
    }


    /** 상품 건별 자료 조회 */
    function getOdrCompleteId($ord){

        $order_lists = $this->getProductOrderInfoList($ord);

        $key = 0;
        $return = array();
        $OrderIDArray = array();
        if( count($order_lists) > 0 ){
            foreach($order_lists as $order){
                $return[$key]["co_oid"]			=		$order['Order']['OrderID'];//주문번호
                $return[$key]["addr1"]				=		$order['ProductOrder']['ShippingAddress']['BaseAddress'];//수취인 주소
                $return[$key]["addr2"]				=		$order['ProductOrder']['ShippingAddress']['DetailedAddress'];//수취인 주소2
                $return[$key]["zip"]				=		$order['ProductOrder']['ShippingAddress']['ZipCode'];//수취인 우편번호
                $return[$key]["rname"]				=		$order['ProductOrder']['ShippingAddress']['Name'];//수취인
                $return[$key]["rtel"]				=		$order['ProductOrder']['ShippingAddress']['Tel2'];//수취인 전화번호
                $return[$key]["rmobile"]			=		$order['ProductOrder']['ShippingAddress']['Tel1'];//수취인 핸드폰번호
                $return[$key]["msg"]				=		$order['ProductOrder']['ShippingMemo'];//배송 메모
                $return[$key]["customs_clearance_number"]		=		$order['ProductOrder']['IndividualCustomUniqueCode'];//고유통관번호
                $return[$key]["btel"]				=		$order['Order']['OrdererTel2'];//주문자 전화번호
                $return[$key]["bname"]				=		$order['Order']['OrdererName'];//주문자명
                $return[$key]["bmobile"]			=		$order['Order']['OrdererTel1'];//주문자 핸드폰 번호
                $return[$key]["regdate"]			=		date('Y-m-d H:i:s',strtotime($order['Order']['OrderDate']));//주문번호생성일
                if( ! empty($order['Order']['PaymentDate'] )){
                    $return[$key]["ic_date"]			=		date('Y-m-d H:i:s',strtotime($order['Order']['PaymentDate']));//주문결제완료일
                }else{
                    $return[$key]["ic_date"]			=	$return[$key]["regdate"];//주문결제완료일
                }

                $return[$key]["co_od_ix"]			=		$order['ProductOrder']['ProductOrderID'];//주문 순번
                $return[$key]["pid"]				=		$order['ProductOrder']['ProductID'];//상품코드
                $return[$key]["pcnt"]				=		$order['ProductOrder']['Quantity'];//수량
                $return[$key]["psprice"]			=		$order['ProductOrder']['UnitPrice'];//상품 판매가(단품)
                $return[$key]["pt_dcprice"]			=		$order['ProductOrder']['TotalProductAmount'];//상품판매가
                $return[$key]["option_id"]			=		$order['ProductOrder']['OptionCode'];//옵션코드;
                $return[$key]["option_text"]		=		$order['ProductOrder']['ProductOption'];
                $return[$key]["f_option_text"]		=		$order['ProductOrder']['ProductOption']; //옵션 코드를 이용하여 DB 조회값을 넣는 게 아닌 네이버에서 받아온 텍스트 정보를 바로 넣기 위함 JK180712

                //네이버페이, 스토어팜. 주문수집 전 취소할 경우의 주문을 수집하기 위해 프로세스 추가. 신훈식. 170419
                if($order['CancelInfo']['RequestChannel'] != "" && $order['CancelInfo']['CancelReason'] != "") {
                    $return[$key]["status"] = "CA";
                    $return[$key]["ca_date"] = date('Y-m-d H:i:s');//취소일자
                    $return[$key]["co_claim_group"] = $order['ProductOrder']["ProductOrderID"];

                    if($order['CancelInfo']['CancelReason']=="INTENT_CHANGED"){
                        $reason = "구매 의사 취소";
                        $reason_code = "NB";//구매의사없음
                    }elseif($order['CancelInfo']['CancelReason']=="COLOR_AND_SIZE"){
                        $reason = "색상 및 사이즈 변경";
                        $reason_code = "SYS";// 색상 및 사이즈 변경
                    }elseif($order['CancelInfo']['CancelReason']=="PRODUCT_UNSATISFIED"){
                        $reason = "서비스 및 상품 불만족";
                        $reason_code = "ETCS";//기타(판매자책임)
                    }elseif($order['CancelInfo']['CancelReason']=="BROKEN"){
                        $reason = "상품 파손";
                        $reason_code = "PD";//파손/하자
                    }elseif($order['CancelInfo']['CancelReason']=="DELAYED_DELIVERY"){
                        $reason = "배송 지연";
                        $reason_code = "DD";//상품미도착
                    }elseif($order['CancelInfo']['CancelReason']=="DROPPED_DELIVERY"){
                        $reason = "배송 누락";
                        $reason_code = "ETCS";//기타(판매자책임)
                    }elseif($order['CancelInfo']['CancelReason']=="INCORRECT_INFO"){
                        $reason = "상품 정보 상이";
                        $reason_code = "PIE";//상품정보 틀림
                    }elseif($order['CancelInfo']['CancelReason']=="WRONG_ORDER"){
                        $reason = "다른 상품 잘못 주문";
                        $reason_code = "OCF";//사이즈,색상잘못선택
                    }elseif($order['CancelInfo']['CancelReason']=="SOLD_OUT"){
                        $reason = "상품 품절";
                        $reason_code = "ETCS";//기타(판매자책임)
                    }elseif($order['CancelInfo']['CancelReason']=="WRONG_DELIVERY"){
                        $reason = "오배송";
                        $reason_code = "ETCS";//기타(판매자책임)
                    }elseif($order['CancelInfo']['CancelReason']=="WRONG_OPTION"){
                        $reason = "색상 등이 다른 상품을 잘못 배송";
                        $reason_code = "ETCS";//기타(판매자책임)
                    }else {
                        $reason = "이유 확인 불가";
                        $reason_code = "UNDI";
                    }

                    $return[$key]["msg"]				=		"[". $order['CancelInfo']['RequestChannel'] . "] [" . $reason . "] " . ( ! empty($order['CancelInfo']['CancelDetailedReason']) ? $order['CancelInfo']['CancelDetailedReason'] : "시스템 취소요청");
                    $return[$key]["reason_code"]		=		$reason_code;
                }

                if( $OrderIDArray[ $order['Order']['OrderID'] ][ $order['ProductOrder']['PackageNumber'] ] ){
                    $delivery_dcprice = 0;
                }else{
                    if( $order['ProductOrder']['DeliveryFeeAmount'] == '무료' ){
                        $delivery_dcprice = 0;
                    }else{
                        $delivery_dcprice = (int)$order['ProductOrder']['DeliveryFeeAmount'] + (int)$order['ProductOrder']['SectionDeliveryFee'];
                    }
                }

                $return[$key]["delivery_dcprice"]		=		$delivery_dcprice;//총배송비 금액

                $OrderIDArray[ $order['Order']['OrderID'] ][ $order['ProductOrder']['PackageNumber'] ] = 1;
                $key++;
            }
        }

        return $return;
    }



    // 결제내역 조회
    public function getStorefarmOrderList($startTime, $endTime, $statuscode){

        $key = 0;
        $return = array();
        $order_result = $this->getChangedProductOrderList( $startTime, $endTime , $statuscode);

        $order_lists = $order_result->Body->GetChangedProductOrderListResponse->ChangedProductOrderInfoList;

        if( count($order_lists) > 0 ){
            foreach($order_lists as $order_info){
                if($statuscode == 'CANCEL_REJECT'){ //취소 철회 상태 주문 조회시 해당 상태 외에 최근 변경된 주문이 전부 조회됨 170512
                    if($order_info->ClaimStatus == 'CANCEL_REJECT'){
                        $order_details = $this->getProductOrderInfoList($order_info->ProductOrderID);
                    }
                }else if($statuscode == 'RETURN_REJECT'){ //반품 철회 상태 주문 조회시 해당 상태 외에 최근 변경된 주문이 전부 조회됨 170512
                    if($order_info->ClaimStatus == 'RETURN_REJECT'){
                        $order_details = $this->getProductOrderInfoList($order_info->ProductOrderID);
                    }
                }else{
                    $order_details = $this->getProductOrderInfoList($order_info->ProductOrderID);
                }

                if(count($order_details) > 0){
                    foreach($order_details as $order){
                        $return[$key] = $order;
                        $key++;
                    }
                }
            }
        }

        return $return;
    }

    /*
Array
(
    [Order] => Array
        (
            [GeneralPaymentAmount] =>
            [OrderDate] => 2011-12-31T15:00:00.00Z
            [OrderID] => ORDERNO200000001
            [OrdererID] => miser
            [OrdererName] => 구두쇠
            [OrdererTel1] => 010-123-4567
            [PaymentMeans] => 신용카드
            [PayLocationType] => PC
        )

    [ProductOrder] => Array
        (
            [DeliveryFeeAmount] => 2500
            [ExpectedDeliveryMethod] =>
            [OptionPrice] =>
            [PlaceOrderStatus] => NOT_YET
            [ProductID] => 123456
            [ProductName] => 테스트상품1
            [ProductOrderID] => PONO200000000001
            [ProductOption] =>
            [ProductOrderStatus] => PAYED
            [Quantity] => 1
            [SellerProductCode] =>
            [ShippingAddress] => Array
                (
                    [BaseAddress] => 서울특별시 구로구 가마산로 156
                    [DetailedAddress] =>
                    [Name] => 홍길동
                    [Tel1] => 1566-3880
                    [ZipCode] => 08310
                )

            [ShippingFeeType] => 선결제
            [ShippingMemo] =>
            [TotalPaymentAmount] =>
            [TotalProductAmount] =>
            [UnitPrice] => 1000
            [ItemNo] =>
        )

)
*/


    /**
     * npay 주문조회
     * @param {string} $sdate = 조회 시작 일시 YYYY-MM-DDThh:mm:ss
     * @param {string} $edate = 조회 종료 일시 YYYY-MM-DDThh:mm:ss
     * @param {string} $statuscode = 최종 상품 주문 상태 코드 =>
     * PAY_WAITING-입금대기,
     * PAYED-결제완료,
     * DISPATCHED-발송처리,
     * CANCEL_REQUESTED-취소요청,
     * RETURN_REQUESTED-반품요청,
     * EXCHANGE_REQUESTED-교환요청,
     * EXCHANGE_REDELIVERY_READY-교환 재배송 준비,
     * HOLDBACK_REQUESTED-구매확정 보류요청,
     * CANCELED-취소,
     * RETURNED-반품,
     * EXCHANGED-교환,
     * PURCHASE_DECIDED-구매확정
     */
    public function getChangedProductOrderList($sdate = '', $edate = '', $statuscode = ''){
        //GMT 시간으로 변경
        $nsdate = date('c', strtotime($sdate));
        $nedate = date('c', strtotime($edate));

        $service = "MallService41";
        $operation ="GetChangedProductOrderList";

        $requestXmlBody = '
			    	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mall="http://mall.checkout.platform.nhncorp.com/" xmlns:base="http://base.checkout.platform.nhncorp.com/">
			    		<soapenv:Header/>
							<soapenv:Body>
						    	<mall:'.$operation.'Request>';
        $requestXmlBody .= $this->getBaseCheckoutRequest($service, $operation);                                                                 // 기본 구조체
        $requestXmlBody .= 			'<base:InquiryTimeFrom>'.($nsdate ? $nsdate : date("Y-m-d\TH:i:s")).'</base:InquiryTimeFrom>';	    // #필수 조회 시작 일시(해당 시각 포함)

        if($edate){
            $requestXmlBody .= 		'<base:InquiryTimeTo>'.$nedate.'</base:InquiryTimeTo>';	                                                    // #선택 조회 종료 일시(해당 시각 포함하지 않음)
        }

        //$requestXmlBody .= 			'<base:InquiryExtraData>'.$orderNo.'</base:InquiryExtraData>';	                                        // #선택 조회에 사용할 추가 데이터(예: 주문번호)
        if($statuscode){
            $requestXmlBody .= 		'<mall:LastChangedStatusCode>'.$statuscode.'</mall:LastChangedStatusCode>';	                                // #선택 최종 상품 주문 상태 코드
        }

        // $requestXmlBody .= 			'<mall:MallID>'.$this->userInfo['site_id'].'</mall:MallID>';	                                        // #선택 판매자 아이디
        $requestXmlBody .= 			'<mall:MallID></mall:MallID>';	                                                                            // #선택 판매자 아이디
        $requestXmlBody .= 		'</mall:'.$operation.'Request>
							</soapenv:Body>
					</soapenv:Envelope>';

        $targetUrl = NPAY_TARGETURL.$service;
        $action = $this->action = $service."#".$operation;

/*
        echo "<pre>";
        echo $service;
        echo "</pre>";

        echo $action; echo "<br>";
        echo $targetUrl;exit;
*/

        $result = $this->call($targetUrl, $action, $requestXmlBody, __METHOD__ );

        return $result;

        /**
         * xml 에서 오는 데이터
         * [Body] => [GetChangedProductOrderListResponse]
         * [ReturnedDataCount] => 137 주문건수
         * [ChangedProductOrderInfoList][*]
         * 	[LastChangedDate] => 2012-01-01T15:00:00.00Z 최종 변경일
         * 	[LastChangedStatus] => PAY_WAITING 최종 변경상태
         * 	[OrderID] => ORDERNO100000002 그룹상품주문번호
         * 	[ProductOrderID] => PONO100000000004 개별상품주문번호
         * 	[ProductOrderStatus] => PAY_WAITING 주문상태
         */

    }


    public function getExchangeApplyOdrComplete($sdate = '', $edate = '', $statuscode = ''){
        $result = $this->getChangedProductOrderList($sdate, $edate, 'EXCHANGE_REQUESTED');
        return $result;
    }


    /**
     * 교환 요청 내역
     *
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
    function getDeliveryExchangeApplyOdrComplete($startTime, $endTime){

        $order_lists = $this->getStorefarmOrderList($startTime."00", $endTime."59", 'EXCHANGE_REQUESTED');

        $key = 0;
        $return = array();
        $OrderIDArray = array();
        if( count($order_lists) > 0 ){
            foreach($order_lists as $order){

                $return[$key]["co_oid"]				=		$order['Order']['OrderID'];//주문번호
                $return[$key]["co_od_ix"]			=		$order['ProductOrder']['ProductOrderID'];//주문 순번
                $return[$key]["pcnt"]				=		$order['ProductOrder']['Quantity'];//수량
                $return[$key]["co_claim_group"]		=		$return[$key]["co_od_ix"];//취소 등록 고유 번호

                $return[$key]["regdate"]			=		date('Y-m-d H:i:s');//취소일자

                if($order['CancelInfo']['CancelReason']=="INTENT_CHANGED"){
                    $reason = "구매 의사 취소";
                    $reason_code = "NB";//구매의사없음
                }elseif($order['CancelInfo']['CancelReason']=="COLOR_AND_SIZE"){
                    $reason = "색상 및 사이즈 변경";
                    $reason_code = "SYS";// 색상 및 사이즈 변경
                }elseif($order['CancelInfo']['CancelReason']=="PRODUCT_UNSATISFIED"){
                    $reason = "서비스 및 상품 불만족";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="BROKEN"){
                    $reason = "상품 파손";
                    $reason_code = "PD";//파손/하자
                }elseif($order['CancelInfo']['CancelReason']=="DELAYED_DELIVERY"){
                    $reason = "배송 지연";
                    $reason_code = "DD";//상품미도착
                }elseif($order['CancelInfo']['CancelReason']=="DROPPED_DELIVERY"){
                    $reason = "배송 누락";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="INCORRECT_INFO"){
                    $reason = "상품 정보 상이";
                    $reason_code = "PIE";//상품정보 틀림
                }elseif($order['CancelInfo']['CancelReason']=="WRONG_ORDER"){
                    $reason = "다른 상품 잘못 주문";
                    $reason_code = "OCF";//사이즈,색상잘못선택
                }elseif($order['CancelInfo']['CancelReason']=="SOLD_OUT"){
                    $reason = "상품 품절";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="WRONG_DELIVERY"){
                    $reason = "오배송";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="WRONG_OPTION"){
                    $reason = "색상 등이 다른 상품을 잘못 배송";
                    $reason_code = "ETCS";//기타(판매자책임)
                }else {
                    $reason = "이유 확인 불가";
                    $reason_code = "UNDI";
                }

                $return[$key]["msg"]				=		"[". $order['CancelInfo']['RequestChannel'] . "] [" . $reason . "] " . ( ! empty($order['CancelInfo']['CancelDetailedReason']) ? $order['CancelInfo']['CancelDetailedReason'] : "시스템 교환요청");
                $return[$key]["reason_code"]		=		$reason_code;

                $key++;
            }
        }

        return $return;
    }


    /**
     * 주문내역 상세조회
     * @param {string} $orderNo = 주문번호
     */
    public function getProductOrderInfoList($orderNo){
        $service = "MallService41";
        $operation ="GetProductOrderInfoList";
        //NHNAPISCL 객체생성
        $scl = new NHNAPISCL();
        //타임스탬프를 포맷에 맞게 생성
        $timestamp = $scl->getTimestamp();
        //리턴데이터 복호화키 생성
        $dec_key = $scl->generateKey($timestamp, $this->secret_key);
        //hmac-sha256서명생성
        $signature = $scl->generateSign($timestamp . $service . $operation, $this->secret_key);

        $requestXmlBody = '
			    	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mall="http://mall.checkout.platform.nhncorp.com/" xmlns:base="http://base.checkout.platform.nhncorp.com/">
			    		<soapenv:Header/>
							<soapenv:Body>
						    	<mall:'.$operation.'Request>';


        /* #필수 기본구조체 */
        $requestXmlBody .= '		<base:AccessCredentials>
										<base:AccessLicense>'.$this->api_key.'</base:AccessLicense>
										<base:Timestamp>'.$timestamp.'</base:Timestamp>
										<base:Signature>'.$signature.'</base:Signature>
									</base:AccessCredentials>
									<base:RequestID></base:RequestID>
									<base:DetailLevel>'.$this->detaillevel.'</base:DetailLevel>
									<base:Version>'.$this->orderVersion.'</base:Version>';

        if(is_array($orderNo)){
            foreach ($orderNo as $seqNo):
                $requestXmlBody .= 		'<mall:ProductOrderIDList>'.$seqNo.'</mall:ProductOrderIDList>';	// #필수 상품 주문 번호
            endforeach;
        }else{
            $requestXmlBody .= 		'<mall:ProductOrderIDList>'.$orderNo.'</mall:ProductOrderIDList>';	// #필수 상품 주문 번호
        }

        $requestXmlBody .= 		'</mall:'.$operation.'Request>
							</soapenv:Body>
					</soapenv:Envelope>';

        $targetUrl = NPAY_TARGETURL.$service;
        $action = $this->action = $service."#".$operation;
        $result = $this->call($targetUrl, $action, $requestXmlBody, __METHOD__ );
        //print_r($result);
        //exit;
        $key = 0;
        $return = array();

        foreach($result->Body->GetProductOrderInfoListResponse->ProductOrderInfoList as $orderInfo):
            //print_r($orderInfo);
            $return[$key]['Order']['GeneralPaymentAmount'] = (string)$orderInfo->Order->GeneralPaymentAmount;
            $return[$key]['Order']['OrderDate'] = (string)$orderInfo->Order->OrderDate;
            $return[$key]['Order']['OrderID'] = (string)$orderInfo->Order->OrderID;
            $return[$key]['Order']['PaymentDate'] = (string)$orderInfo->Order->PaymentDate;
            if( ! empty($orderInfo->Order->OrdererID) ){
                $return[$key]['Order']['OrdererID'] = (string)$scl->decrypt($dec_key, $orderInfo->Order->OrdererID);
            }else{
                $return[$key]['Order']['OrdererID'] = '';
            }
            if( ! empty($orderInfo->Order->OrdererName) ){
                $return[$key]['Order']['OrdererName'] = (string)$scl->decrypt($dec_key, $orderInfo->Order->OrdererName);
            }else{
                $return[$key]['Order']['OrdererName'] = '';
            }
            if( ! empty($orderInfo->Order->OrdererTel1) ){
                $return[$key]['Order']['OrdererTel1'] = (string)$scl->decrypt($dec_key, $orderInfo->Order->OrdererTel1);
            }else{
                $return[$key]['Order']['OrdererTel1'] = '';
            }
            if( ! empty($orderInfo->Order->OrdererTel2) ){
                $return[$key]['Order']['OrdererTel2'] = (string)$scl->decrypt($dec_key, $orderInfo->Order->OrdererTel2);
            }else{
                $return[$key]['Order']['OrdererTel2'] = '';
            }

            $return[$key]['Order']['PaymentMeans'] = (string)$orderInfo->Order->PaymentMeans;
            $return[$key]['Order']['PayLocationType'] = (string)$orderInfo->Order->PayLocationType;

            $return[$key]['ProductOrder']['DeliveryFeeAmount'] = (string)$orderInfo->ProductOrder->DeliveryFeeAmount;
            $return[$key]['ProductOrder']['ExpectedDeliveryMethod'] = (string)$orderInfo->ProductOrder->ExpectedDeliveryMethod;
            $return[$key]['ProductOrder']['OptionPrice'] = (string)$orderInfo->ProductOrder->OptionPrice;
            $return[$key]['ProductOrder']['PlaceOrderStatus'] = (string)$orderInfo->ProductOrder->PlaceOrderStatus;
            $return[$key]['ProductOrder']['ProductID'] = (string)$orderInfo->ProductOrder->ProductID;
            $return[$key]['ProductOrder']['ProductName'] = (string)$orderInfo->ProductOrder->ProductName;
            $return[$key]['ProductOrder']['ProductOrderID'] = (string)$orderInfo->ProductOrder->ProductOrderID;
            $return[$key]['ProductOrder']['ProductOption'] = (string)$orderInfo->ProductOrder->ProductOption;
            $return[$key]['ProductOrder']['ProductOrderID'] = (string)$orderInfo->ProductOrder->ProductOrderID;
            $return[$key]['ProductOrder']['ProductOrderStatus'] = (string)$orderInfo->ProductOrder->ProductOrderStatus;
            $return[$key]['ProductOrder']['Quantity'] = (string)$orderInfo->ProductOrder->Quantity;
            $return[$key]['ProductOrder']['OptionCode'] = (string)$orderInfo->ProductOrder->OptionCode;
            if($orderInfo->ProductOrder->IndividualCustomUniqueCode != "") {
                $return[$key]['ProductOrder']['IndividualCustomUniqueCode'] = (string)$scl->decrypt($dec_key, $orderInfo->ProductOrder->IndividualCustomUniqueCode);
            }


            if( ! empty($orderInfo->ProductOrder->ShippingAddress->BaseAddress) ){
                $return[$key]['ProductOrder']['ShippingAddress']['BaseAddress'] = (string)$scl->decrypt($dec_key, $orderInfo->ProductOrder->ShippingAddress->BaseAddress);
            }else{
                $return[$key]['ProductOrder']['ShippingAddress']['BaseAddress'] = '';
            }
            if( ! empty($orderInfo->ProductOrder->ShippingAddress->DetailedAddress) ){
                $return[$key]['ProductOrder']['ShippingAddress']['DetailedAddress'] = (string)$scl->decrypt($dec_key, $orderInfo->ProductOrder->ShippingAddress->DetailedAddress);
            }else{
                $return[$key]['ProductOrder']['ShippingAddress']['DetailedAddress'] = '';
            }
            if( ! empty($orderInfo->ProductOrder->ShippingAddress->Name) ){
                $return[$key]['ProductOrder']['ShippingAddress']['Name'] = (string)$scl->decrypt($dec_key, $orderInfo->ProductOrder->ShippingAddress->Name);
            }else{
                $return[$key]['ProductOrder']['ShippingAddress']['Name'] = '';
            }
            if( ! empty($orderInfo->ProductOrder->ShippingAddress->Tel1) ){
                $return[$key]['ProductOrder']['ShippingAddress']['Tel1'] = (string)$scl->decrypt($dec_key, $orderInfo->ProductOrder->ShippingAddress->Tel1);
            }else{
                $return[$key]['ProductOrder']['ShippingAddress']['Tel1'] = '';
            }
            if( ! empty($orderInfo->ProductOrder->ShippingAddress->Tel2) ){
                $return[$key]['ProductOrder']['ShippingAddress']['Tel2'] = (string)$scl->decrypt($dec_key, $orderInfo->ProductOrder->ShippingAddress->Tel2);
            }else{
                $return[$key]['ProductOrder']['ShippingAddress']['Tel2'] = '';
            }
            $return[$key]['ProductOrder']['ShippingAddress']['ZipCode'] = (string)$orderInfo->ProductOrder->ShippingAddress->ZipCode;

            $return[$key]['ProductOrder']['SectionDeliveryFee'] = (string)$orderInfo->ProductOrder->SectionDeliveryFee;
            $return[$key]['ProductOrder']['PackageNumber'] = (string)$orderInfo->ProductOrder->PackageNumber;
            $return[$key]['ProductOrder']['ShippingFeeType'] = (string)$orderInfo->ProductOrder->ShippingFeeType;
            $return[$key]['ProductOrder']['ShippingMemo'] = (string)$orderInfo->ProductOrder->ShippingMemo;
            $return[$key]['ProductOrder']['TotalPaymentAmount'] = (string)$orderInfo->ProductOrder->TotalPaymentAmount;
			$return[$key]['ProductOrder']['MallManageCode'] = (string)$orderInfo->ProductOrder->MallManageCode;
            $return[$key]['ProductOrder']['TotalProductAmount'] = (string)$orderInfo->ProductOrder->TotalProductAmount;
            $return[$key]['ProductOrder']['UnitPrice'] = (string)$orderInfo->ProductOrder->UnitPrice;
            $return[$key]['ProductOrder']['ItemNo'] = (string)$orderInfo->ProductOrder->ItemNo;

            $return[$key]['ReturnInfo']['RequestChannel'] = (string)$orderInfo->ReturnInfo->RequestChannel;
            $return[$key]['ReturnInfo']['ReturnReason'] = (string)$orderInfo->ReturnInfo->ReturnReason;
            $return[$key]['ReturnInfo']['ReturnDetailedReason'] = (string)$orderInfo->ReturnInfo->ReturnDetailedReason;

            $return[$key]['CancelInfo']['RequestChannel'] = (string)$orderInfo->CancelInfo->RequestChannel;
            $return[$key]['CancelInfo']['CancelReason'] = (string)$orderInfo->CancelInfo->CancelReason;
            $return[$key]['CancelInfo']['CancelDetailedReason'] = (string)$orderInfo->CancelInfo->CancelDetailedReason;
            $return[$key]['CancelInfo']['EtcFeeDemandAmount'] = (string)$orderInfo->CancelInfo->EtcFeeDemandAmount;

            $key++;

        endforeach;

        return $return;


        /**
         * xml 에서 오는 데이터
         * [Body] => [GetProductOrderInfoListResponse]
         * [ReturnedDataCount] => 1
         * [ProductOrderInfoList][*]
         *
         **[Delivery] => 배송 정보
         *	 [DeliveryMethod] => DELIVERY
         *	 [DeliveryStatus] => 집화
         *	 [SendDate] => 2012-01-01T15:00:00.00Z
         *
         **[CancelInfo] => 주문취소 정보
         *	 [CancelCompletedDate] => 2014-04-24T03:19:59.00Z
         *	 [CancelReason] => PRODUCT_UNSATISFIED
         *	 [ClaimRequestDate] => 2014-04-24T03:19:59.00Z
         *	 [ClaimStatus] => CANCEL_REQUES
         *	 [RequestChannel] => 판매자
         *
         **[ReturnInfo] => 반품정보
         *	 [ClaimRequestDate] => 2014-04-25T01:47:45.00Z
         *	 [ClaimStatus] => RETURN_REQUEST
         *	 [ReturnReason] => INTENT_CHANGED
         *
         **[ExchangeInfo] => 교환정보
         *	 [ClaimRequestDate] => 2012-01-01T15:00:00.00Z
         *	 [ClaimStatus] => EXCHANGE_REDELIVERING
         *	 [ExchangeReason] => ETC
         *	 [RequestChannel] => 구매회원
         *
         **[Order] => 결제정보
         *   [OrderDate] => 2012-01-01T15:00:00.00Z
         *   [OrderID] => ORDERNO900000002
         *   [OrdererID] => VtybcZJX264Cftn1Ws9qNg==
         *   [OrdererName] => +MpikM5Ej5JWoPApiFAB0A==
         *   [OrdererTel1] => q0W06//6FL+0w2dMbTLA/g==
         *   [PaymentMeans] => 신용카드
         *   [IsDeliveryMemoParticularInput] => false
         *   [PayLocationType] => PC
         *
         **	[ProductOrder] => 주문정보
         *   [ClaimStatus] => CANCEL_REQUEST
         *   [ClaimType] => CANCEL
         *   [DeliveryFeeAmount] => 2500
         *   [MallID] => salesman1
         *   [PlaceOrderStatus] => NOT_YET
         *   [ProductClass] => 단일상품
         *   [ProductID] => 123456
         *   [ProductName] => 테스트상품1
         *   [ProductOrderID] => PONO900000000007
         *   [ProductOrderStatus] => PAYED
         *   [Quantity] => 1
         *   [ShippingAddress] => SimpleXMLElement Object
         *    [AddressType] => DOMESTIC
         *    [BaseAddress] => uimvxx8DZQ026ji1uNNXxUIdpWIaFxKtRjd3ywzG4H2M/Tvm1gjdWxqBPt1V+h4l
         *    [DetailedAddress] => e64wHUi36EBPREjxbyIJ1w==
         *    [Name] => RrFBCdceJIYCtJ8SKVjhhQ==
         *    [Tel1] => xMf+YizXFsrnLFdMO3xllw==
         *    [ZipCode] => 463-050
         *   [ShippingDueDate] => 2012-01-04T15:00:00.00Z
         *   [ShippingFeeType] => 선결제
         *   [UnitPrice] => 1000
         *   [SellerBurdenDiscountAmount] => 0
         *   [CommissionPrePayStatus] => GENERAL_PRD
         *   [PaymentCommission] => 0
         *   [SaleCommission] => 0
         *   [ExpectedSettlementAmount] => 0
         *
         */
    }


    /**
     * 발주처리
     *
     */
    function sendOrdRepackaging($data){

        $return_result = $this->doApproveOperation( $data['co_od_ix'], 'PlaceProductOrder', $data );

        //성공처리
        $return = new resultData();
        if($return_result !== TRUE){
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }else{
            $return->resultCode = 'success';
        }
        return $return;
    }


    /**
     * 발주처리
     *
     */
    function sendOrdReqDelivery($data){

        $nsendDt = date('Y-m-d\TH:i:s', strtotime($data['di_date']));
        $service = "MallService41";
        $operation ="ShipProductOrder";
        $resultResponse = $operation.'Response';

        $requestXmlBody = '
			    	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mall="http://mall.checkout.platform.nhncorp.com/" xmlns:base="http://base.checkout.platform.nhncorp.com/">
			    		<soapenv:Header/>
							<soapenv:Body>
						    	<mall:'.$operation.'Request>';

        $requestXmlBody .= $this->getBaseCheckoutRequest($service, $operation); // 기본 구조체

        $requestXmlBody .= 			'<mall:ProductOrderID>'.$data['co_od_ix'].'</mall:ProductOrderID>';	// #필수 상품 주문 번호
        $requestXmlBody .= 			'<mall:DeliveryMethodCode>DELIVERY</mall:DeliveryMethodCode>';	// #필수 배송 방법
        if($data['quick']){
            $deliveryCompanyCode = $this->getDeliveryCompanyCode($data['quick']);
            $requestXmlBody .= 		'<mall:DeliveryCompanyCode>'.$deliveryCompanyCode.'</mall:DeliveryCompanyCode>';	// #선택 택배사 코드
            $requestXmlBody .= 		'<mall:TrackingNumber>'.$data['invoice_no'].'</mall:TrackingNumber>';	// #선택 송장 번호
        }
        $requestXmlBody .= 			'<mall:DispatchDate>'.$nsendDt.'</mall:DispatchDate>';	// #필수 배송일
        $requestXmlBody .= 		'</mall:'.$operation.'Request>
							</soapenv:Body>
					</soapenv:Envelope>';

        $targetUrl = NPAY_TARGETURL.$service;
        $action = $this->action = $service."#".$operation;
        $result = $this->call($targetUrl, $action, $requestXmlBody, __METHOD__ );

        $fp = fopen($_SERVER["DOCUMENT_ROOT"].'/data/dewytree_data/_logs/npay/npay_ORD_' . date('Ymd') . '.log', 'a');
        $fh .= '---------- START - co_od_ix : '. $data['co_od_ix'] .' [' . date('Y-m-d H:i:s') . '] ----------' . chr(13) . chr(13);
        $fh .= $requestXmlBody . chr(13) . chr(13);
        fwrite($fp, $fh);
        fclose($fp);

        $return = new resultData();

        //성공처리
        if($result->Body->$resultResponse->ResponseType !='SUCCESS' ){
            $error  = $result->Body->ShipProductOrderResponse->Error->Message;
            $error .= $result->Body->ShipProductOrderResponse->Error->Detail;

            $return->message = $error;
            $return->resultCode = 'fail';

        }else{
            $return->resultCode = 'success';
        }

        return $return;
    }


    /** 판매 취소 */
    function setDenySell($data){

        $return_result = $this->doApproveOperation( $data['co_od_ix'], 'CancelSale', $data );

        $fp = fopen($_SERVER["DOCUMENT_ROOT"].'/data/dewytree_data/_logs/npay/npay_CS_' . date('Ymd') . '.log', 'a');
        $fh .= '---------- START - co_od_ix : '. $data['co_od_ix'] .' [' . date('Y-m-d H:i:s') . '] ----------' . chr(13) . chr(13);
        $fh .= print_r($return_result, true) . chr(13) . chr(13);
        $fh .= print_r($data, true) . chr(13) . chr(13);
        fwrite($fp, $fh);
        fclose($fp);

        //성공처리
        $return = new resultData();
        if($return_result !== TRUE){
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }else{
            $return->resultCode = 'success';
        }
        return $return;
    }


    /** 배송 지연 */
    function sendOrdReqDelayOrder($data){

        $return_result = $this->doApproveOperation( $data['co_od_ix'], 'DelayProductOrder', $data );

        $fp = fopen($_SERVER["DOCUMENT_ROOT"].'/data/dewytree_data/_logs/npay/npay_DO_' . date('Ymd') . '.log', 'a');
        $fh .= '---------- START - co_od_ix : '. $data['co_od_ix'] .' [' . date('Y-m-d H:i:s') . '] ----------' . chr(13) . chr(13);
        $fh .= print_r($return_result, true) . chr(13) . chr(13);
        fwrite($fp, $fh);
        fclose($fp);

        //성공처리
        $return = new resultData();
        if($return_result !== TRUE){
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }else{
            $return->resultCode = 'success';
        }
        return $return;
    }


    /** 반품 접수 */
    function RequestReturn($data){

        $return_result = $this->doApproveOperation( $data['co_od_ix'], 'RequestReturn', $data );

        $fp = fopen($_SERVER["DOCUMENT_ROOT"].'/data/dewytree_data/_logs/npay/npay_RD_' . date('Ymd') . '.log', 'a');
        $fh .= '---------- START - co_od_ix : '. $data['co_od_ix'] .' [' . date('Y-m-d H:i:s') . '] ----------' . chr(13) . chr(13);
        $fh .= print_r($return_result, true) . chr(13) . chr(13);
        fwrite($fp, $fh);
        fclose($fp);

        //성공처리
        $return = new resultData();
        if($return_result !== TRUE){
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }else{
            $return->resultCode = 'success';
        }
        return $return;
    }


    /**
     * 주문 교환 재배송 처리
     *
     */
    //sendOrdReqAddReturnPickup
    function sendOrdReqExchangeDelivery($data){

        //성공처리
        $return = new resultData();
        //$return_result = $this->doApproveOperation( $data['co_od_ix'], 'ReleaseReturnHold' ); // 반품보류 해제 후 승인처리

        //if( $return_result === TRUE ){
        $return_result = $this->doApproveOperation( $data['co_od_ix'], 'ReDeliveryExchange', $data );
        if($return_result !== TRUE){
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }else{
            $return->resultCode = 'success';
        }
        /*
        }else{
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }
        */
        return $return;
    }


    /**
     * 주문 교환 수거완료
     *
     */
    //sendOrdReqAddReturnPickup
    function sendOrdReqExchangeCollect($data){

        //성공처리
        $return = new resultData();

        //if( $return_result === TRUE ){
        $return_result = $this->doApproveOperation( $data['co_od_ix'], 'ApproveCollectedExchange', $data );
        if($return_result !== TRUE){
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }else{
            $return->resultCode = 'success';
        }
        /*
        }else{
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }
        */
        return $return;
    }


    /**
     * 주문 취소 완료 내역
     *
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
    function getCancelApplyOdrComplete($startTime, $endTime){

        $order_lists = $this->getStorefarmOrderList($startTime."00", $endTime."59", 'CANCELED');

        $key = 0;
        $return = array();
        $OrderIDArray = array();
        if( count($order_lists) > 0 ){
            foreach($order_lists as $order){

                $return[$key]["co_oid"]				=		$order['Order']['OrderID'];//주문번호
                $return[$key]["co_od_ix"]			=		$order['ProductOrder']['ProductOrderID'];//주문 순번
                $return[$key]["pcnt"]				=		$order['ProductOrder']['Quantity'];//수량
                $return[$key]["co_claim_group"]		=		$return[$key]["co_od_ix"];//취소 등록 고유 번호
                $return[$key]["EtcFeeDemandAmount"]	=		$order['CancelInfo']["EtcFeeDemandAmount"];//추가결제비용
                $return[$key]["regdate"]			=		date('Y-m-d H:i:s');//취소일자

                if($order['CancelInfo']['CancelReason']=="INTENT_CHANGED"){
                    $reason = "구매 의사 취소";
                    $reason_code = "NB";//구매의사없음
                }elseif($order['CancelInfo']['CancelReason']=="COLOR_AND_SIZE"){
                    $reason = "색상 및 사이즈 변경";
                    $reason_code = "SYS";// 색상 및 사이즈 변경
                }elseif($order['CancelInfo']['CancelReason']=="PRODUCT_UNSATISFIED"){
                    $reason = "서비스 및 상품 불만족";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="BROKEN"){
                    $reason = "상품 파손";
                    $reason_code = "PD";//파손/하자
                }elseif($order['CancelInfo']['CancelReason']=="DELAYED_DELIVERY"){
                    $reason = "배송 지연";
                    $reason_code = "DD";//상품미도착
                }elseif($order['CancelInfo']['CancelReason']=="DROPPED_DELIVERY"){
                    $reason = "배송 누락";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="INCORRECT_INFO"){
                    $reason = "상품 정보 상이";
                    $reason_code = "PIE";//상품정보 틀림
                }elseif($order['CancelInfo']['CancelReason']=="WRONG_ORDER"){
                    $reason = "다른 상품 잘못 주문";
                    $reason_code = "OCF";//사이즈,색상잘못선택
                }elseif($order['CancelInfo']['CancelReason']=="SOLD_OUT"){
                    $reason = "상품 품절";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="WRONG_DELIVERY"){
                    $reason = "오배송";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="WRONG_OPTION"){
                    $reason = "색상 등이 다른 상품을 잘못 배송";
                    $reason_code = "ETCS";//기타(판매자책임)
                }else {
                    $reason = "이유 확인 불가";
                    $reason_code = "UNDI";
                }

                $return[$key]["msg"]				=		"[". $order['CancelInfo']['RequestChannel'] . "] [" . $reason . "] " . ( ! empty($order['CancelInfo']['CancelDetailedReason']) ? $order['CancelInfo']['CancelDetailedReason'] : "시스템 취소요청");
                $return[$key]["reason_code"]		=		$reason_code;
                $return[$key]["reason"]		        =		$reason;

                $key++;
            }
        }

        return $return;
    }


    /**
     * 주문 취소 요청 내역
     *
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
    function getDeliveryCancelApplyOdrComplete($startTime, $endTime){

        $order_lists = $this->getStorefarmOrderList($startTime."00", $endTime."59", 'CANCEL_REQUESTED');

        $key = 0;
        $return = array();
        $OrderIDArray = array();
        if( count($order_lists) > 0 ){
            foreach($order_lists as $order){

                $return[$key]["co_oid"]				=		$order['Order']['OrderID'];//주문번호
                $return[$key]["co_od_ix"]			=		$order['ProductOrder']['ProductOrderID'];//주문 순번
                $return[$key]["pcnt"]				=		$order['ProductOrder']['Quantity'];//수량
                $return[$key]["co_claim_group"]		=		$return[$key]["co_od_ix"];//취소 등록 고유 번호

                $return[$key]["regdate"]			=		date('Y-m-d H:i:s');//취소일자

                if($order['CancelInfo']['CancelReason']=="INTENT_CHANGED"){
                    $reason = "구매 의사 취소";
                    $reason_code = "NB";//구매의사없음
                }elseif($order['CancelInfo']['CancelReason']=="COLOR_AND_SIZE"){
                    $reason = "색상 및 사이즈 변경";
                    $reason_code = "SYS";// 색상 및 사이즈 변경
                }elseif($order['CancelInfo']['CancelReason']=="PRODUCT_UNSATISFIED"){
                    $reason = "서비스 및 상품 불만족";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="BROKEN"){
                    $reason = "상품 파손";
                    $reason_code = "PD";//파손/하자
                }elseif($order['CancelInfo']['CancelReason']=="DELAYED_DELIVERY"){
                    $reason = "배송 지연";
                    $reason_code = "DD";//상품미도착
                }elseif($order['CancelInfo']['CancelReason']=="DROPPED_DELIVERY"){
                    $reason = "배송 누락";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="INCORRECT_INFO"){
                    $reason = "상품 정보 상이";
                    $reason_code = "PIE";//상품정보 틀림
                }elseif($order['CancelInfo']['CancelReason']=="WRONG_ORDER"){
                    $reason = "다른 상품 잘못 주문";
                    $reason_code = "OCF";//사이즈,색상잘못선택
                }elseif($order['CancelInfo']['CancelReason']=="SOLD_OUT"){
                    $reason = "상품 품절";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="WRONG_DELIVERY"){
                    $reason = "오배송";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['CancelInfo']['CancelReason']=="WRONG_OPTION"){
                    $reason = "색상 등이 다른 상품을 잘못 배송";
                    $reason_code = "ETCS";//기타(판매자책임)
                }else {
                    $reason = "이유 확인 불가";
                    $reason_code = "UNDI";
                }

                $return[$key]["msg"]				=		"[". $order['CancelInfo']['RequestChannel'] . "] [" . $reason . "] " . ( ! empty($order['CancelInfo']['CancelDetailedReason']) ? $order['CancelInfo']['CancelDetailedReason'] : "시스템 취소요청");
                $return[$key]["reason_code"]		=		$reason_code;
                $return[$key]["reason"]		        =		$reason;

                $key++;
            }
        }

        return $return;
    }


    /**
     * 주문 취소 철회 내역
     *
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
    function getCancelRejectRequestOdr($startTime, $endTime){

        $order_lists = $this->getStorefarmOrderList($startTime."00", $endTime."59", 'CANCEL_REJECT');

        $fp = fopen($_SERVER["DOCUMENT_ROOT"].'/data/dewytree_data/_logs/npay/npay_CRJ_' . date('Ymd') . '.log', 'a');
        $fh .= '---------- START - co_od_ix : '. $data['co_od_ix'] .' [' . date('Y-m-d H:i:s') . '] ----------' . chr(13) . chr(13);
        $fh .= print_r($order_lists, true) . chr(13) . chr(13);
        fwrite($fp, $fh);
        fclose($fp);

        $key = 0;
        $return = array();
        $OrderIDArray = array();
        if( count($order_lists) > 0 ){
            foreach($order_lists as $order){
                $return[$key]["co_oid"]				=		$order['Order']['OrderID'];//주문번호
                $return[$key]["co_od_ix"]			=		$order['ProductOrder']['ProductOrderID'];//주문 순번
                $return[$key]["regdate"]			=		date('Y-m-d H:i:s');//취소일자

                $key++;
            }
        }

        return $return;
    }


    /**
     * 주문 반품 철회 내역
     *
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
    function getReturnRejectRequestOdr($startTime, $endTime){

        $order_lists = $this->getStorefarmOrderList($startTime."00", $endTime."59", 'RETURN_REJECT');

        $fp = fopen($_SERVER["DOCUMENT_ROOT"].'/data/dewytree_data/_logs/npay/npay_RRJ_' . date('Ymd') . '.log', 'a');
        $fh .= '---------- START - co_od_ix : '. $data['co_od_ix'] .' [' . date('Y-m-d H:i:s') . '] ----------' . chr(13) . chr(13);
        $fh .= print_r($order_lists, true) . chr(13) . chr(13);
        fwrite($fp, $fh);
        fclose($fp);

        $key = 0;
        $return = array();
        $OrderIDArray = array();
        if( count($order_lists) > 0 ){
            foreach($order_lists as $order){
                $return[$key]["co_oid"]				=		$order['Order']['OrderID'];//주문번호
                $return[$key]["co_od_ix"]			=		$order['ProductOrder']['ProductOrderID'];//주문 순번
                $return[$key]["regdate"]			=		date('Y-m-d H:i:s');//취소일자

                $key++;
            }
        }

        return $return;
    }


    /**
     * 주문 출고 취소 승인 요청
     *
     */
    function sendOrdReqCancelComplete($data){

        $return_result = $this->doApproveOperation( $data['co_od_ix'], 'ApproveCancelApplication', $data['EtcFee'] );

        //성공처리
        $return = new resultData();
        if($return_result !== TRUE){
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }else{
            $return->resultCode = 'success';
        }
        return $return;
    }


    /**
     * 주문 반품 요청
     *
     */
    function getReturnApplyOdrComplete($startTime, $endTime){

        $order_lists = $this->getStorefarmOrderList($startTime."00", $endTime."59", 'RETURN_REQUESTED');

        $key = 0;
        $return = array();
        $OrderIDArray = array();
        if( count($order_lists) > 0 ){
            foreach($order_lists as $order){

                $return[$key]["co_oid"]				=		$order['Order']['OrderID'];//주문번호
                $return[$key]["co_od_ix"]			=		$order['ProductOrder']['ProductOrderID'];//주문 순번
                $return[$key]["pcnt"]				=		$order['ProductOrder']['Quantity'];//수량
                $return[$key]["co_claim_group"]		=		$return[$key]["co_od_ix"];//취소 등록 고유 번호
                $return[$key]["regdate"]			=		date('Y-m-d H:i:s');//취소일자

                if($order['ReturnInfo']['ReturnReason']=="INTENT_CHANGED"){
                    $reason = "구매 의사 취소";
                    $reason_code = "NB";//구매의사없음
                }elseif($order['ReturnInfo']['ReturnReason']=="COLOR_AND_SIZE"){
                    $reason = "색상 및 사이즈 변경";
                    $reason_code = "SYS";// 색상 및 사이즈 변경
                }elseif($order['ReturnInfo']['ReturnReason']=="PRODUCT_UNSATISFIED"){
                    $reason = "서비스 및 상품 불만족";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['ReturnInfo']['ReturnReason']=="BROKEN"){
                    $reason = "상품 파손";
                    $reason_code = "PD";//파손/하자
                }elseif($order['ReturnInfo']['ReturnReason']=="DELAYED_DELIVERY"){
                    $reason = "배송 지연";
                    $reason_code = "DD";//상품미도착
                }elseif($order['ReturnInfo']['ReturnReason']=="DROPPED_DELIVERY"){
                    $reason = "배송 누락";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['ReturnInfo']['ReturnReason']=="INCORRECT_INFO"){
                    $reason = "상품 정보 상이";
                    $reason_code = "PIE";//상품정보 틀림
                }elseif($order['ReturnInfo']['ReturnReason']=="WRONG_ORDER"){
                    $reason = "다른 상품 잘못 주문";
                    $reason_code = "OCF";//사이즈,색상잘못선택
                }elseif($order['ReturnInfo']['ReturnReason']=="SOLD_OUT"){
                    $reason = "상품 품절";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['ReturnInfo']['ReturnReason']=="WRONG_DELIVERY"){
                    $reason = "오배송";
                    $reason_code = "ETCS";//기타(판매자책임)
                }elseif($order['ReturnInfo']['ReturnReason']=="WRONG_OPTION"){
                    $reason = "색상 등이 다른 상품을 잘못 배송";
                    $reason_code = "ETCS";//기타(판매자책임)
                }else{
                    $reason ="이유 확인 불가";
                    $reason_code = "UNDI";
                }

                $return[$key]["msg"]				=		"[". $order['ReturnInfo']['RequestChannel'] . "] " . $reason . " " . ( ! empty($order['ReturnInfo']['ReturnDetailedReason']) ? $order['ReturnInfo']['ReturnDetailedReason'] : "시스템 반품요청");
                $return[$key]["reason_code"]		=		$reason_code;

                $key++;
            }
        }

        return $return;
    }


    /**
     * 반품 승인
     */
    function sendOrdReqReturnComplete($data){ //반품확정시

        //성공처리
        $return = new resultData();
        //$return_result = $this->doApproveOperation( $data['co_od_ix'], 'ReleaseReturnHold' ); // 반품보류 해제 후 승인처리

        //if( $return_result === TRUE ){
        $return_result = $this->doApproveOperation( $data['co_od_ix'], 'ApproveReturnApplication' );
        if($return_result !== TRUE){
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }else{
            $return->resultCode = 'success';
        }
        /*
        }else{
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }
        */
        return $return;
    }


    /**
     * 주문 반품,교환 승인
     *
     */
    function sendOrdReqAddReturnPickup($data){

        //성공처리
        $return = new resultData();
        //$return_result = $this->doApproveOperation( $data['co_od_ix'], 'ReleaseReturnHold' ); // 반품보류 해제 후 승인처리

        //if( $return_result === TRUE ){
        $return_result = $this->doApproveOperation( $data['co_od_ix'], 'ApproveReturnApplication' );
        if($return_result !== TRUE){
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }else{
            $return->resultCode = 'success';
        }
        /*
        }else{
            $return->message = $return_result;
            $return->resultCode = 'fail';
        }
        */
        return $return;
    }


    /**
     * 상태변경 공통함수
     * @param {string} $orderNo = 주문번호
     * @param {string} $operation = 오퍼레이션 => 발주처리(PlaceProductOrder), 취소승인(ApproveCancelApplication), 반품승인(ApproveReturnApplication), 교환수거완료(ApproveCollectedExchange), 반품보류 해제(ReleaseReturnHold), 교환보류 해제(ReleaseExchangeHold)
     */
    public function doApproveOperation($orderNo, $operation, $orderData=""){

        $service = "MallService41";
        $requestXmlBody = '
			    	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mall="http://mall.checkout.platform.nhncorp.com/" xmlns:base="http://base.checkout.platform.nhncorp.com/">
			    		<soapenv:Header/>
							<soapenv:Body>
						    	<mall:'.$operation.'Request>';

        $requestXmlBody .= $this->getBaseCheckoutRequest($service, $operation); // 기본 구조체

        $requestXmlBody .= 			'<mall:ProductOrderID>'.$orderNo.'</mall:ProductOrderID>';	// #필수 상품 주문

        if($operation == 'PlaceProductOrder'){
            $requestXmlBody .= 			'<mall:CheckReceiverAddressChanged>true</mall:CheckReceiverAddressChanged>'; // #선택 배송지 정보 수정 여부 확인. 주문 시 구매자가 입력한 배송지 정보가 관리자/판매자/구매자에 의해 수정된 이력이 있는지 여부를 응답에 포함한다.
        }

        if($operation == 'ApproveCancelApplication' || $operation == 'ApproveReturnApplication'){
            if($orderData == ""){
                $orderData = "0";
            }
            $requestXmlBody .= 			'<mall:EtcFeeDemandAmount>'.$orderData.'</mall:EtcFeeDemandAmount>';	// #필수 기타 비용 청구액
        }

        $requestXmlBody .= $this->getAddRequest($operation, $orderData); // 추가 구조체

        $requestXmlBody .= 		'</mall:'.$operation.'Request>
							</soapenv:Body>
					</soapenv:Envelope>';


        $targetUrl = NPAY_TARGETURL.$service;
        $action = $this->action = $service."#".$operation;
        $result = $this->call($targetUrl, $action, $requestXmlBody, __METHOD__ );

        $resultResponse = $operation.'Response';
        if($result->Body->$resultResponse->ResponseType !='SUCCESS' ){
            $error  = $result->Body->$resultResponse->Error->Message;
            $error .= $result->Body->$resultResponse->Error->Detail;

            $fp = fopen($_SERVER["DOCUMENT_ROOT"].'/data/dewytree_data/_logs/npay/npay_ERROR_' . date('Ymd') . '.log', 'a');
            $fh .= '---------- Error MSG -- oid : '. $orderNo .' [' . date('Y-m-d H:i:s') . '] Request : '.$operation.' ----------' . chr(13) . chr(13);
            $fh .= print_r($error, true) . chr(13) . chr(13);
            fwrite($fp, $fh);
            fclose($fp);

            return $error;
        }

        if($operation == 'PlaceProductOrder'){
            //주소 변경 체크
            if( $result->Body->$resultResponse->IsReceiverAddressChanged == "true" ){
                $order_details = $this->getProductOrderInfoList($orderNo);
                foreach($order_details as $od){
                    $sql = "update shop_order_detail_deliveryinfo odd, shop_order_detail od set odd.zip='".$od['ProductOrder']['ShippingAddress']['ZipCode']."', odd.addr1='".$od['ProductOrder']['ShippingAddress']['BaseAddress']."', odd.addr2='".$od['ProductOrder']['ShippingAddress']['DetailedAddress']."'
							where odd.oid=od.oid and odd.odd_ix=od.odd_ix and od.order_from='" . $this->site_code . "' and od.co_oid='" . $orderData['co_oid'] . "' and co_od_ix ='" . $orderData['co_od_ix'] . "' ";
                    $this->db->query ( $sql );
                }
            }
        }

        return TRUE;
        /**
         * xml 에서 오는 데이터
         * [Body] => [ApproveCancelApplicationResponse]
         *
         ** 성공
         * 	[ResponseType] => SUCCESS
         * 	[Timestamp] => 2014-04-24T04:29:10.00Z
         *
         ** 실패
         * 	[ResponseType] => ERROR
         * 	 [Error]
         * 	  [Code] => unknown
         * 	  [Message] => 취소  요청 상태가 아닙니다.
         * 	  [Detail] => Transaction ID: 95158783653B8DD54A817712AF9E1A334E2B
         * 	[Timestamp] => 2014-04-24T05:40:22.92Z
         */
    }


    /**
     * 주문 API연동 추가 XML
     */
    public function getAddRequest($operation, $datas){
        if($operation == 'CancelSale'){
            //PRODUCT_UNSATISFIED 서비스 및 상품 불만족
            //DELAYED_DELIVERY 배송 지연
            //SOLD_OUT 상품 품절

            //log 쌓기 180118
            $fp = fopen($_SERVER["DOCUMENT_ROOT"].'/data/dewytree_data/_logs/npay/npay_CS_' . date('Ymd') . '.log', 'a');
            $fh .= '---------- START - Reason Code : '. $datas[reason_code] .' [' . date('Y-m-d H:i:s') . '] ----------' . chr(13) . chr(13);
            $fh .= print_r($datas, true) . chr(13) . chr(13);
            fwrite($fp, $fh);
            fclose($fp);

            $requestXmlBody="
							<mall:CancelReasonCode>DELAYED_DELIVERY</mall:CancelReasonCode>
							";
        }else if($operation == 'DelayProductOrder'){
            //RESERVED_DISPATCH 예약 발송
            switch($datas[reason_code]){
                case 'STS' : $reason_code = 'PRODUCT_PREPARE'; //상품 준비 중
                    break;
                case 'BA' : $reason_code = 'CUSTOMER_REQUEST'; //고객 요청
                    break;
                case 'OMI' : $reason_code = 'CUSTOM_BUILD'; //주문 제작
                    break;
                default : $reason_code = 'ETC';
                    break;
            }

            if(empty($datas[status_message])){
                $datas[status_message] = "판매자에게 문의 부탁드립니다";
            }

            //date("Y-m-d\TH:i:s",strtotime("2016-11-28 12:00:00"))
            $requestXmlBody="
							<mall:DispatchDueDate>".date("Y-m-d\TH:i:s", strtotime($datas[due_date]." 23:59:59"))."</mall:DispatchDueDate>
							<mall:DispatchDelayReasonCode>".$reason_code."</mall:DispatchDelayReasonCode>
							<mall:DispatchDelayDetailReason>".$datas[status_message]."</mall:DispatchDelayDetailReason>
							";
        }else if($operation == 'RejectReturn'){
            $requestXmlBody="
							<mall:RejectDetailContent>".$datas[status_message]."</mall:RejectDetailContent>
							";
        }else if($operation == 'WithholdReturn'){
            //EXTRAFEEE 추가 비용 청구
            //RETURN_DELIVERYFEE_AND_EXTRAFEEE 반품 배송비 + 추가 비용 청구
            //RETURN_PRODUCT_NOT_DELIVERED 반품 상품 미입고

            switch($datas[reason_code]){
                case 'NRDP' : $reason_code = 'RETURN_DELIVERYFEE'; //반품 배송비 청구
                    break;
                case 'NRA' : $reason_code = 'RETURN_PRODUCT_NOT_DELIVERED'; //반품 상품 미입고
                    break;
                default : $reason_code = 'ETC'; //기타 사유
                    break;
            }

            $requestXmlBody="
							<mall:ReturnHoldCode>".$reason_code."</mall:ReturnHoldCode>
							<mall:ReturnHoldDetailContent>".$datas[status_message]."</mall:ReturnHoldDetailContent>
							";
            //<mall:EtcFeeDemandAmount></mall:EtcFeeDemandAmount> 기타 반품 비용
        }else if($operation == 'ReDeliveryExchange'){
            $requestXmlBody="
							<mall:ReDeliveryMethodCode>DELIVERY</mall:ReDeliveryMethodCode>
							<mall:ReDeliveryCompanyCode>CJGLS</mall:ReDeliveryCompanyCode>
							<mall:ReDeliveryTrackingNumber>12121212</mall:ReDeliveryTrackingNumber>
							";
        }else if($operation == 'RejectExchange'){
            $requestXmlBody="
							<mall:RejectDetailContent>TEST RejectExchange</mall:RejectDetailContent>
							";
        }else if($operation == 'WithholdExchange'){
            //EXCHANGE_EXTRAFEE 추가 교환 비용 청구
            //EXCHANGE_PRODUCT_READY 교환 상품 준비 중
            //EXCHANGE_HOLDBACK 교환 구매 확정 보류
            //RESERVED_DISPATCH 예약 발송

            switch($datas[reason_code]){
                case 'NRA' : $reason_code = 'EXCHANGE_PRODUCT_NOT_DELIVERED'; //교환 상품 미입고
                    break;
                case 'NRDP' : $reason_code = 'EXCHANGE_DELIVERYFEE'; //교환 배송비 청구
                    break;
                default : $reason_code = 'ETC';
                    break;
            }

            $requestXmlBody="
							<mall:ExchangeHoldCode>".$reason_code."</mall:ExchangeHoldCode>
							<mall:ExchangeHoldDetailContent>".$datas[status_message]."</mall:ExchangeHoldDetailContent>
							";
        }else if($operation == 'RejectExchange'){
            $requestXmlBody="
							<mall:RejectDetailContent>".$datas[status_message]."</mall:RejectDetailContent>
							";
        }else if($operation == 'RequestReturn'){

            switch($datas[reason_code]){
                case 'COLOR_AND_SIZE' : $reason_code = 'PRODUCT_PREPARE'; //사이즈,색상잘못선택
                    break;
                case 'BROKEN' : $reason_code = 'CUSTOMER_REQUEST'; //배송상품 파손/하자
                    break;
                case 'WRONG_DELIVERY' : $reason_code = 'CUSTOM_BUILD'; //배송상품 오배송
                    break;
                case 'DROPPED_DELIVERY' : $reason_code = 'CUSTOM_BUILD'; //상품미도착
                    break;
                case 'INCORRECT_INFO' : $reason_code = 'CUSTOM_BUILD'; //상품정보 틀림
                    break;
                default : $reason_code = 'ETC';
                    break;
            }

            $requestXmlBody="
							<mall:ReturnReasonCode>".$reason_code."</mall:ReturnReasonCode>
							<mall:CollectDeliveryMethodCode>RETURN_INDIVIDUAL</mall:CollectDeliveryMethodCode>
							<mall:CollectDeliveryCompanyCode></mall:CollectDeliveryCompanyCode>
							<mall:CollectTrackingNumber></mall:CollectTrackingNumber>
							";
        }

        return $requestXmlBody;
    }


    /**
     * 상품 후기 연동
     *
     * @param {string} startTime = 검색 시작일 YYYYMMDDhhmm(201007210000)
     * @param {string} endTime = 검색 종료일 YYYYMMDDhhmm(201007210000)
     */
    function getReviewList($startTime, $endTime){

        $nsdate = date('Y-m-d\TH:i:s', strtotime($startTime."00") - (3600 * 9));
        $nedate = date('Y-m-d\TH:i:s', strtotime($endTime."59") - (3600 * 9));

        $service = "MallService41";
        $operation ="GetPurchaseReviewList";
        $resultResponse = $operation.'Response';

        $requestXmlBody = '
			    	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:mall="http://mall.checkout.platform.nhncorp.com/" xmlns:base="http://base.checkout.platform.nhncorp.com/">
			    		<soapenv:Header/>
							<soapenv:Body>
						    	<mall:'.$operation.'Request>';

        $requestXmlBody .= $this->getBaseCheckoutRequest($service, $operation); // 기본 구조체

        $requestXmlBody .= 			'<base:InquiryTimeFrom>'.$nsdate.'</base:InquiryTimeFrom>';	// 조회 일자
        $requestXmlBody .= 			'<base:InquiryTimeTo>'.$nedate.'</base:InquiryTimeTo>';	// 조회 일자
        //$requestXmlBody .= 			'<mall:InquiryExtraData></mall:InquiryExtraData>';	//상품 주문 번호
        //$requestXmlBody .= 			'<mall:MallID>'.$this->userInfo['site_id'].'</mall:MallID>';	// #필수 가맹점 ID
        $requestXmlBody .= 			'<mall:PurchaseReviewClassType>PREMIUM</mall:PurchaseReviewClassType>';	// #필수 조회할 구매평 타입 GENERAL PREMIUM

        $requestXmlBody .= 		'</mall:'.$operation.'Request>
							</soapenv:Body>
					</soapenv:Envelope>';

        $targetUrl = NPAY_TARGETURL.$service;
        $action = $this->action = $service."#".$operation;
        $result = $this->call($targetUrl, $action, $requestXmlBody, __METHOD__ );

        $review_list = $result->Body->$resultResponse->PurchaseReviewList;

        $return = array();
        $i = 0;
        if( count($review_list) > 0 ){
            foreach($review_list as $review){

                if( (string)$review->PurchaseReviewScore > 16 ){
                    $return[$i]['valuation'] = 5;
                }elseif( (string)$review->PurchaseReviewScore > 12 ){
                    $return[$i]['valuation'] = 4;
                }elseif( (string)$review->PurchaseReviewScore > 8 ){
                    $return[$i]['valuation'] = 3;
                }elseif( (string)$review->PurchaseReviewScore > 5 ){
                    $return[$i]['valuation'] = 2;
                }else{
                    $return[$i]['valuation'] = 1;
                }

                $return[$i]['co_bbs_ix'] = (string)$review->PurchaseReviewId;
                $return[$i]['pid'] = (string)$review->ProductID;
                $return[$i]['pname'] = (string)$review->ProductName;
                $return[$i]['subject'] = (string)$review->Title;
                $return[$i]['contents'] = (string)$review->Content;
                $i++;
            }
        }

        return $return;

    }


    /**
     * 택배사 코드 목록
     * @param {string} $dlvEtprsCd = 쇼핑몰 택배사 코드
     * @return {string} $dlvComCode = 샵N 택배사 코드
     */
    public function getDeliveryCompanyCode($dlvEtprsCd) {
        switch ($dlvEtprsCd) {

            /*
            01	우체국택배
            02	아주택배
            03	옐로우캡
            04	삼성택배
            05	로젠택배
            06	대한통운
            07	고려택배
            08	트라넷
            10	KGB택배
            11	훼미리택배
            12	현대택배
            13	한진택배
            15	동부택배
            16	로엑스택배
            17	사가와익
            18	CJ택배
            19	하나로택배
            21	경동택배
            22	대신택배
            23	일양로지스
            24	건영택배
            25	천일택배
            26	합동택배
            27	호남택배
            40	기타
            41	KG로지스(구,동부택배)
            42	CVSnet(편의점택배)
            43	gtx로지스
            */

            case '01':	//	우체국택배
                $result = "EPOST";
                break;
            case '03':	//	옐로우캡
                $result = "YELLOW";
                break;
            case '05':	//	로젠택배
                $result = "KGB";
                break;
            case '06':	//	대한통운
                $result = "KOREXG";
                break;
            case '10':	//	KGB택배
                $result = "KGBLS";
                break;
            case '12':	//	현대택배
                $result = "HYUNDAI";
                break;
            case '13':	//	한진택배
                $result = "HANJIN";
                break;
            case '15':	//	동부익스프레스택배
                $result = "DONGBU";
                break;
            case '17':	//	SC로지스(사가와익스프레스택배)
                $result = "SAGAWA";
                break;
            case '18':	//	CJGLS
                $result = "CJGLS";
                break;
            case '19':	//	하나로택배
                $result = "HANARO";
                break;
            case '25':	//	천일택배
                $result = "CHUNIL";
                break;
            case '26':	//	합동택배
                $result = "HDEXP";
                break;
            case '42':	//	편의점택배
                $result = "CVSNET";
                break;
            case '21':	//	경동택배
                $result = "KDEXP";
                break;
            case '23':	//	일양로지스
                $result = "ILYANG";
                break;
            case '22':	//	대신택배
                $result = "DAESIN";
                break;
            case '52':	//	우체국 EMS 배송조회
                $result = "EMS";
                break;
            case '43':	//	FEDEX
                $result = "FEDEX";
                break;
            case '44':	//	DHL
                $result = "DHL";
                break;
            case '45':	//	UPS
                $result = "UPS";
                break;
            case '46':	//	i-parcel
                $result = "IPARCEL";
                break;
            case '47':	//	DPD(UK)
                $result = "DPD(UK)";
                break;
            case '48':	//	PostNL
                $result = "PostNL";
                break;
            case '49':	//	범한판토스
                $result = "PANTOS";
                break;
            case '50':	//	ACI Express
                $result = "ACIEXPRESS";
                break;
            case '51':	//	kglogis
                $result = "DONGBU";
                break;
            case '53':	//	KGB택배
                $result = "KGB";
                break;
            case '54':	//	SLX
                $result = "SLX";
                break;
            case '55':	//	순풍택배
                $result = "SFexpress";
                break;
            case '56': // AliExpressStandardShipping EMS (npay 연동시 AliExpressStandardShipping -> EMS 로 넣어달라는 고객사요청으로 추가 (26767))
                $result = "EMS";
                break;
            case '57': // 차이나 EMS (npay 연동시 차이나EMS -> EMS 로 넣어달라는 고객사요청으로 추가 (26767))
                $result = "EMS";
                break;
            default :	// 기타택배
                $result = "CH1";
                break;
        }

        return $result;
    }
}