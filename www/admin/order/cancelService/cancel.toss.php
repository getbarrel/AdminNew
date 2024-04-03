<?

/**
 * nicepay 취소 모듈
 *
 * @author bgh
 * @date 2013.07.10
 */
class toss
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = 'https://pay.toss.im/api';
    }

    public function cancelService($rdata)
    {
        $data = array();

        $data['apiKey'] = $rdata['toss_api_key']; // 가맹점 key (필수)
        $data['payToken'] = $rdata['tid']; // 토스 결제 토큰 (필수)
//        $data['refundNo'] = ''; // 환불 번호 (미입력 시 자동 생성되며 환불 완료 응답에서 확인 가능합니다. 매회 요청마다 유니크한 값을 사용하시길 권장드립니다.)
        $data['reason'] = ($rdata['reason'] ? $rdata['reason'] : '관리자환불'); // 환불 사유
        $data['amount'] = $rdata['cancel_amount']; // 환불할 금액 (필수)
        $data['amountTaxFree'] = $rdata['cancel_tax_free_amount']; // 환불할 금액 중 비과세금액 (필수)
//        $data['amountTaxable'] = 0; // 환불할 금액 중 과세금액
//        $data['amountVat'] = 0; // 환불할 금액 중 부가세
//        $data['amountServiceFee'] = 0; // 환불할 금액 중 봉사료

        $response = $this->callApi($this->apiUrl . '/v2/refunds', json_encode($data));
        $responseData = new stdClass();
        if ($response->code == '0') {
            $responseData->result = true;
        } else {
            $responseData->result = false;
            $responseData->message = $response->msg;
        }
        return $responseData;
    }

    private function callApi($url, $data)
    {
        /*$ch = curl_init();
        // Set the url, number of POST vars, POST data

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($ch, CURLOPT_SSLVERSION, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        // Execute post
        $result = curl_exec($ch);

        curl_close($ch);*/
		$data = str_replace("\"","\'",$data);
		$data = str_replace("'","\"",$data);

		$cmd = sprintf("curl -H \"%s\" -d \"%s\" -X POST \"%s\"", 'Content-Type: application/json', $data, $url);
        $result = shell_exec($cmd);

        return json_decode($result);
    }
}