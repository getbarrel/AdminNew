<?

class eximbay
{
    /**
     * 가맹점 아이디
     * @var type
     */
    private $mid;

    /**
     * 가맹점 secretkey
     * @var type
     */
    private $secretKey;
    /**
     * 서비스 도메인
     * @var
     */
    private $serviceDomain;

    public function setConfig($data)
    {
        if ($data['eximbay_service_type'] == 'service') {
            $this->mid = $data['eximbay_mid'];
            $this->secretKey = $data['eximbay_secret_key'];
            $this->serviceDomain = 'https://secureapi.eximbay.com';
        } else {
            $this->mid = '1849705C64';
            $this->secretKey = '289F40E6640124B2628640168C3C5464';
            $this->serviceDomain = 'https://secureapi.test.eximbay.com';
        }
    }

    public function cancelService($data)
    {
        $requestData = array();

        //필수 정보
        $requestData['ver'] = '230'; //연동 버전
        $requestData['txntype'] = 'REFUND'; //거래 타입
        $requestData['charset'] = 'UTF-8'; //기본값은 UTF-8
        $requestData['cur'] = 'USD'; //통화 USD
        $requestData['mid'] = $this->mid; //Eximbay에서 할당한 가맹점 아이디
        $requestData['lang'] = 'EN'; //KR Korean, EN English, CN Chinese (simplified Chinese character), JP Japanese, RU Russian, TH Thai, TW Chinese (Traditional Chinese Characters)

        //취소정보
        $requestData['refundtype'] = $data["real_price"] == $data["cancel_amount"] ? 'F' : 'P'; //“F” : Fully, “P” : Partial
        $requestData['ref'] = $data['oid']; //원 승인 거래 ref
        $requestData['amt'] = $data["real_price"]; //원 승인 거래 금액 (e.g. 1000.50, 9.15)
        $requestData['refundamt'] = $data['cancel_amount']; //환불 요청 금액 원 승인 거래 금액을 초과할 수 없습니다.
        $requestData['balance'] = $data['remain_price']; //“원 승인 금액 – 합(환불금액)”으로 환불 가능 금액 가맹점과 Eximbay간 환불 거래 불일치를 방지하기 위해 사용. 값이 있는 경우만 체크합니다.
        $requestData['transid'] = $data['tid']; //승인 거래의 결제사 거래 아이디
        $requestData['refundid'] = str_replace("-", "", $data['oid']) . time(); //환불 요청에 대한 유일한 값으로 가맹점에서 생성. 모든 요청데이터의 refundid는 Unique 해야 합니다
        $requestData['reason'] = ($data['reason'] ? $data['reason'] : '관리자환불'); //환불사유
//        $requestData['param1'] = ''; //가맹점 정의 파라미터1
//        $requestData['param2'] = ''; //가맹점 정의 파라미터2
//        $requestData['param3'] = ''; //가맹점 정의 파라미터3
        $requestData['fgkey'] = $this->getFgkey($requestData); //검증키

        $response = $this->callApi($this->serviceDomain . '/Gateway/DirectProcessor.krp', $requestData);

        $responseData = new stdClass();
        if ($response['rescode'] == '0000') {
            $responseData->result = true;
        } else {
            $responseData->result = false;
            $responseData->message = $response['resmsg'];
        }
        return $responseData;
    }

    private function getFgkey($data)
    {
        ksort($data);
        $linkBuf = $this->secretKey . "?";
        $linkBufData = array();
        foreach ($data as $key => $val) {
            $linkBufData[] = $key . "=" . $val;
        }
        $linkBuf .= implode('&', $linkBufData);
        return hash("sha256", $linkBuf);
    }

    private function callApi($url, $data)
    {
        /*$ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);

        // Execute post
        $result = curl_exec($ch);

        curl_close($ch);*/

		$cmd = sprintf("curl -d \"%s\" -X POST \"%s\"", http_build_query($data), $url);
        $result = shell_exec($cmd);

        $return = array();
        parse_str($result, $return);

        return $return;
    }
}
