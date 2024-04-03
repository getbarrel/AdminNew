<?

/**
 * payco 취소 모듈
 *
 * @author hong
 * @date 2015.10.14
 */
class payco
{

    private $result;

    public function __construct()
    {
        $result = null;
    }

    public function cancelService($data)
    {

        //2015-11-02 Hong payco_util.php 에서 글로벌 변수가 안넘어가서 추가적으로 선언
        GLOBAL $URL_cancel;
        $pg_info = $data['pgInfo'];

        include($_SERVER["DOCUMENT_ROOT"] . "/shop/payco/payco_config.php");

        //---------------------------------------------------------------------------------
        // 가맹점 주문 번호로 상품 불러오기
        // DB에 연결해서 가맹점 주문 번호로 해당 상품 목록을 불러옵니다.
        //---------------------------------------------------------------------------------

        $cancelType = strtoupper($data["cancelType"]);                    // 취소 Type 받기 - ALL 또는 PART
        $orderCertifyKey = $data["orderCertifyKey"];                            // 주문완료통보시 내려받은 인증값
        $sellerOrderProductReferenceKey = $data["sellerOrderProductReferenceKey"];            // 가맹점 주문 상품 연동 키 ( PART 취소 시 )
        $cancelTotalAmt = $data["cancelTotalAmt"];                            // 총 주문 금액
        $cancelAmt = $data["cancelAmt"];                                // 취소 상품 금액 ( PART 취소 시 )
        $requestMemo = $data["requestMemo"];                                // 취소처리 요청메모

        $orderNo = $data["orderNo"];                                    // 주문번호
        $totalCancelTaxfreeAmt = $data["totalCancelTaxfreeAmt"];                    // 총 취소할 면세금액
        $totalCancelTaxableAmt = $data["totalCancelTaxableAmt"];                    // 총 취소할 과세금액
        $totalCancelVatAmt = $data["totalCancelVatAmt"];                        // 총 취소할 부가세
        $totalCancelPossibleAmt = $data["totalCancelPossibleAmt"];                    // 총 취소가능금액(현재기준): 취소가능금액 검증(취소요청 전 취소할수있는 총금액)
        $cancelDetailContent = $data["cancelDetailContent"];                        // 취소사유


        //-----------------------------------------------------------------------------
        // (로그) 호출 시점과 호출값을 파일에 기록합니다.
        //-----------------------------------------------------------------------------
        Write_Log("payco_cancel.php is Called - cancelType : $cancelType , sellerOrderProductReferenceKey : $sellerOrderProductReferenceKey, cancelTotalAmt : $cancelTotalAmt, cancelAmt : $cancelAmt , requestMemo : $requestMemo , orderNo : $orderNo, totalCancelTaxfreeAmt : $totalCancelTaxfreeAmt, totalCancelTaxableAmt : $totalCancelTaxableAmt, totalCancelVatAmt : $totalCancelVatAmt, totalCancelPossibleAmt : $totalCancelPossibleAmt, orderCertifyKey : $orderCertifyKey  , cancelDetailContent : $cancelDetailContent");

        //---------------------------------------------------------------------------------------------------------------------
        // orderNo, cancelTotalAmt 값이 없으면 로그를 기록한 뒤 JSON 형태로 오류를 돌려주고 API를 종료합니다.
        //---------------------------------------------------------------------------------------------------------------------
        if ($orderNo == "") {
            $this->result["message"] = "주문번호가 전달되지 않았습니다.";
            $this->result["code"] = 9999;
            return $this->result;
        }
        if ($cancelTotalAmt == "") {
            $this->result["message"] = "총 주문금액이 전달되지 않았습니다.";
            $this->result["code"] = 9999;
            return $this->result;
        }

        //----------------------------------------------------------------------------------
        // 상품정보 변수 선언 및 초기화
        //----------------------------------------------------------------------------------
        //2015-11-02 Hong payco_constants.php 에서 include 했기 때문에 Global 선언을 하면 변수값이 사라져서 주석처리
        //Global $cpId, $productId;

        //-----------------------------------------------------------------------------------
        // 취소 내역을 담을 JSON OBJECT를 선언합니다.
        //-----------------------------------------------------------------------------------
        $cancelOrder = array();

        //-----------------------------------------------------------------------------------
        // 전체 취소 = "ALL", 부분취소 = "PART"
        //------------------------------------------------------------------------------------
        if ($cancelType == "ALL") {
            //---------------------------------------------------------------------------------
            // 파라메터로 값을 받을 경우 필요가 없는 부분이며
            // 주문 키값으로만 DB에서 데이터를 불러와야 한다면 이 부분에서 작업하세요.
            //---------------------------------------------------------------------------------

        } else if ($cancelType == "PART") {
            //-----------------------------------------------------------------------------------------------------------------------
            // sellerOrderProductReferenceKey, cancelAmt 값이 없으면 로그를 기록한 뒤 JSON 형태로 오류를 돌려주고 API를 종료합니다.
            //-----------------------------------------------------------------------------------------------------------------------
            if ($sellerOrderProductReferenceKey == "") {
                $this->result["message"] = "취소주문연동키 값이 전달되지 않았습니다.";
                $this->result["code"] = 9999;
                return $this->result;
            }
            if ($cancelAmt == "") {
                $this->result["message"] = "취소상품 금액이 전달되지 않았습니다.";
                $this->result["code"] = 9999;
                return $this->result;
            }

            //---------------------------------------------------------------------------------
            // 주문상품 데이터 불러오기
            // 파라메터로 값을 받을 경우 받은 값으로만 작업을 하면 됩니다.
            // 주문 키값으로만 DB에서 취소 상품 데이터를 불러와야 한다면 이 부분에서 작업하세요.
            //---------------------------------------------------------------------------------
            $orderProducts = array();

            //---------------------------------------------------------------------------------
            // 취소 상품값으로 읽은 변수들로 Json String 을 작성합니다.
            //---------------------------------------------------------------------------------
            $orderProduct = array();
            $orderProduct["cpId"] = $cpId;                            // 상점 ID , payco_config.php 에 설정
            $orderProduct["productId"] = $productId;                        // 상품 ID , payco_config.php 에 설정
            $orderProduct["productAmt"] = $cancelAmt;                        // 취소 상품 금액 ( 파라메터로 넘겨 받은 금액 - 필요서 DB에서 불러와 대입 )
            $orderProduct["sellerOrderProductReferenceKey"] = $sellerOrderProductReferenceKey;    // 취소 상품 연동 키 ( 파라메터로 넘겨 받은 값 - 필요서 DB에서 불러와 대입 )
            $orderProduct["cancelDetailContent"] = urlencode($cancelDetailContent);    // 취소 상세 사유
            array_push($orderProducts, $orderProduct);


        } else {
            //---------------------------------------------------------------------------------
            // 취소타입이 잘못되었음. ( ALL과 PART 가 아닐경우 )
            //---------------------------------------------------------------------------------
            $this->result["message"] = "취소 요청 타입이 잘못되었습니다.";
            $this->result["code"] = 9999;
            return $this->result;
        }

        //---------------------------------------------------------------------------------
        // 설정한 주문정보 변수들로 Json String 을 작성합니다.
        //---------------------------------------------------------------------------------

        $cancelOrder["sellerKey"] = $sellerKey;                            //가맹점 코드. payco_config.php 에 설정
        $cancelOrder["orderCertifyKey"] = $orderCertifyKey;                        //주문완료통보시 내려받은 인증값
        $cancelOrder["requestMemo"] = urlencode($requestMemo);                //취소처리 요청메모
        $cancelOrder["cancelTotalAmt"] = $cancelTotalAmt;                        //주문서의 총 금액을 입력합니다. (전체취소, 부분취소 전부다)
        $cancelOrder["orderProducts"] = $orderProducts;                        //위에서 작성한 상품목록과 배송비상품을 입력

        $cancelOrder["orderNo"] = $orderNo;                                // 주문번호
        $cancelOrder["totalCancelTaxfreeAmt"] = $totalCancelTaxfreeAmt;                // 총 취소할 면세금액
        $cancelOrder["totalCancelTaxableAmt"] = $totalCancelTaxableAmt;                // 총 취소할 과세금액
        $cancelOrder["totalCancelVatAmt"] = $totalCancelVatAmt;                    // 총 취소할 부가세
        $cancelOrder["totalCancelPossibleAmt"] = $totalCancelPossibleAmt;                // 총 취소가능금액(현재기준): 취소가능금액 검증
        //---------------------------------------------------------------------------------
        // 주문 결제 취소 가능 여부 API 호출 ( JSON 데이터로 호출 )
        //---------------------------------------------------------------------------------
        //$Result_json = payco_cancel(urldecode(stripslashes(json_encode($cancelOrder))));
        //$Result = json_decode($Result_json, true);
		$Result = payco_cancel(urldecode(stripslashes(json_encode($cancelOrder))));

        /*
		if ($Result['code'] == '0') {
            if ($Result['result']['totalCancelPaymentAmt'] == $data["cancelAmt"]) {
                $this->result["code"] = "0";
            } else {
                $this->result["code"] = $Result['code'];  // 결과 코드
            }
        } else {
            $this->result["code"] = "9999";  // 결과 코드
        }

        $this->result["message"] = $Result['message']; // 결과 메시지
        return $this->result;
		*/
		if ($Result->code == '0') {
            if ($Result->result->totalCancelPaymentAmt == $data["cancelAmt"]) {
                $this->result["code"] = "0";
            } else {
                $this->result["code"] = $Result->code;  // 결과 코드
            }
        } else {
            $this->result["code"] = "9999";  // 결과 코드
        }

        $this->result["message"] = $Result->message; // 결과 메시지
        return $this->result;
    }
}
