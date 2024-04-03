<?php

/**
 * storefame API Call 클래스
 * @author hjy
 * @date 2016.05.20
 */
class Call_npay
{
    protected $serverUrl;
    protected $actionUrl;

    /**
     * CALL
     * @param string $serverUrl
     * @param string $actionUrl
     * @param string $postData
     * @return DomDocument
     */
    protected function call($serverUrl = '', $actionUrl = '', $postData = NULL, $function_name)
    {
        $this->serverUrl = $serverUrl;
        $this->actionUrl = $actionUrl;


        try {
            $this->logWrite(
                '[request : ' . $request_number . '] time : ' . date('Y-m-d H:i:s') . "\n[URL] " . $this->serverUrl . "  actionUrl : " . $this->actionUrl,
                $postData,
                $function_name
            );


            exit;


            $headers = $this->buildAuctionHeaders(strlen($postData));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->serverUrl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $response = curl_exec($ch);
            curl_close($ch);
            //print_r($response);
            //exit;
            $responseDoc = new DomDocument ();
            $responseDoc->loadXML($response);
            $responseDoc->formatOutput = TRUE; //log사용위해 출력포맷 설정

            /* Soap result를 simplexml Object로 변환 */
            $xmlString = $responseDoc->saveXML();
            $xmlObj = simplexml_load_string(str_replace(array('n:', 'n1:', 'soapenv:'), '', $xmlString));


            $this->logWrite(
                '[result : ' . $request_number . '] time : ' . date('Y-m-d H:i:s'),
                $xmlString,
                $function_name
            );

            return $xmlObj;
        } catch (Exception $e) {
            echo $e->getMessages();
        }
    }


    private function buildAuctionHeaders($postDataLength)
    {
        $headers = array(
            "Content-Type: text/xml; charset=utf-8",
            "Content-Length: $postDataLength",
            "SOAPAction: $this->actionUrl"
        );

        return $headers;
    }


    /**
     * 객체의 attribute, array 를 array로 변환
     *
     * @param $object
     * @return $array
     */
    protected function myObject2Array($object)
    {
        $return = array();
        foreach ($object as $key => $val):
            $item = array();
            //attribute
            foreach ($val->attributes() as $attr_key => $attr_val):
                $item[$attr_key] = (string)$attr_val;
            endforeach;

            //object
            foreach ($val as $obj_key => $obj_val):
                $item[$obj_key] = $this->myObject2Array($val->$obj_key); //recursive call
            endforeach;

            if (!empty($item)) {
                array_push($return, $item);
            }
        endforeach;
        return $return;
    }

    /**
     * 파일 로그 남기기
     *
     * @param $cmt
     * @param $data
     */
    protected function logWrite($cmt, $data, $function_name)
    {
        $file = fopen($_SERVER["DOCUMENT_ROOT"].'/data/dewytree_data/_logs/npay/call_history_' . date('Ymd') . '.txt', 'a');
        $text = '------------------------------------------------------------------------------------' . "\n";
        $text .= '[Call] ' . $function_name . "\n";
        $text .= $cmt . "\n";
        $text .= $data . "\n";

        fwrite($file, $text);
        fclose($file);
        unset($text);
    }


}