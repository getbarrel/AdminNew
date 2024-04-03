<?

class naverpayPg
{

    /**
     * 파트너 ID
     * @var type
     */
    private $partnerId;

    /**
     * 클라이언트 ID
     * @var type
     */
    private $clientId;

    /**
     * 클라이언트 시크릿 키
     * @var type
     */
    private $clientSecret;

    /**
     * 모드 development or production
     * @var
     */
    private $mode;

    /**
     * API 도메인
     * @var
     */
    private $apiDomain;

    public function setConfig($data)
    {
        $this->partnerId = $data['naverpay_pg_partner_id'];
        $this->clientId = $data['naverpay_pg_client_id'];
        $this->clientSecret = $data['naverpay_pg_client_secret'];

        if ($data['naverpay_pg_service_type'] == 'service') {
            $this->mode = 'production';
            $this->apiDomain = 'https://apis.naver.com';
        } else {
            $this->mode = 'development';
            $this->apiDomain = 'https://dev.apis.naver.com';
        }
    }

    public function cancelService($data)
    {
        $requestData = array(
            'paymentId' => $data['tid']
        , 'merchantPayKey' => $data['oid']
        , 'cancelAmount' => $data['cancel_amount']
        , 'cancelReason' => ($data['reason'] ? $data['reason'] : '관리자환불')
        , 'cancelRequester' => '2' // 취소 요청자(1: 구매자, 2: 가맹점 관리자) 구분이 애매한 경우 가맹점 관리자로 입력합니다
        , 'taxScopeAmount' => $data['cancel_tax_amount'] //과세 대상 금액
        , 'taxExScopeAmount' => $data['cancel_tax_free_amount'] //면세 대상 금액
        , 'doCompareRest' => 1
        , 'expectedRestAmount' => ($data['remain_price'] - $data['cancel_amount']) // 이번 취소가 수행되고 난 후에 남을 가맹점의 예상 금액 , 옵션 파라미터인 doCompareRest값이 1일 때에만 동작합니다 Ex) 결제금액 1000원 중 200원을 취소하고 싶을 때 => expectedRestAmount =800원, cancelAmount=200원으로 요청
        );

        $response = $this->callApi($this->apiDomain . '/' . $this->partnerId . '/naverpay/payments/v1/cancel', $requestData);

        $responseData = new stdClass();
        if ($response->code == 'Success') {
            $responseData->result = true;
        } else {
            $responseData->result = false;
            $responseData->message = $response->message;
        }
        return $responseData;
    }

    private function callApi($url, $data)
    {

        // Eos 서버 업데이트 관련 수정 요청 포비즈
		/*$headers = array(
            'X-Naver-Client-Id:'.$this->clientId,
            'X-Naver-Client-Secret:'.$this->clientSecret
        );

        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
//        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Execute post
        $result = curl_exec($ch);*/
		$cmd = sprintf("curl -H \"%s\" -H \"%s\" -d \"%s\" -X POST \"%s\"", 'X-Naver-Client-Id:'.$this->clientId, 'X-Naver-Client-Secret:'.$this->clientSecret, http_build_query($data), $url);
        $result = shell_exec($cmd);

		// Eos 서버 업데이트 관련 수정 요청 포비즈

        $fp = fopen($_SERVER["DOCUMENT_ROOT"] . $_SESSION["layout_config"]["mall_data_root"] . "/_logs/payment/naverpayPg/cancelRequest_".date('Ymd').".txt", "a+");
        fwrite($fp, $result."\n");
        fclose($fp);

        //curl_close($ch);

        return json_decode($result);
    }
}
