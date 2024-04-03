<?php

/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-04-02
 * Time: 오후 7:04
 */
class kakaoAlimTalk
{

    private $url;                    //요청 url
    private $yn;                    //알림톡 사용 여부
    private $smsLimitbytes;            //sms 최대 bytes
    private $requestData;            //요청 데이터

    private $memberCode;            //(*)회원사코드 - 굿스플로에서 지정한 회 원사코드
    private $apiKey;                //(*)발급받은 API key

    //private $uniqueCode;			//(*)고객사용번호 - 굿스플로 전송단 위
    private $sectionCode;            //(*)요청에 대한 구분 코드 없으면 회원사 코드로 등록됨
    //private $recipientNo;			//(*)받는분 핸드폰번호
    private $talkType;                //(*)톡 구분코드 - A: 알림톡 F: 친구톡
    private $talkTemplateCode;    //(*)카카오톡 템플릿코드 - 알림톡(A)인 경우 필수 친구톡(F)인 경우 사용 안됨
    //private $talkContent ;		//(*)메시지 내용
    //private $buttonName;			//알림톡 버튼명
    //private $buttonUrl;			//알림톡 버튼 연결 URL
    //private $msgType;				//(*)문자 구분코드 - SMS: SMS 문자 LMS: LMS 문자
    //private $msgContent;			//(*)메시지 내용
    private $msgCallback;            //(*)발신번호

    function __construct($yn, $memberCode, $apiKey, $talkTemplateCode, $sectionCode, $msgCallback)
    {
        $this->talkType = "A";
        $this->smsLimitbytes = 80;
        $this->yn = $yn;
        $this->memberCode = $memberCode;
        $this->apiKey = $apiKey;
        $this->talkTemplateCode = $talkTemplateCode;
        $this->sectionCode = $sectionCode;
        $this->msgCallback = $msgCallback;
        //$this->msgCallback = "07044371788";
    }

    public function send($datas)
    {

        if ($this->yn != 'Y') {
            return $this->_result("0001", "카카오 알림톡 사용안함");
        }

        if ($this->memberCode == '') {
            return $this->_result("0002", "카카오 알림톡 memberCode 설정 필요");
        }

        if ($this->apiKey == '') {
            return $this->_result("0003", "카카오 알림톡 apiKey 설정 필요");
        }

        if ($this->talkTemplateCode == '') {
            return $this->_result("0004", "카카오 알림톡 템플릿 설정안함");
        }

        $this->url = "https://ws1.goodsflow.com/WebApi/MISS/Member/v2/SendTalkOrMsg/" . $this->memberCode;
        $this->requestData = array(
            "data" => array(
                "items" => array(),
            ),
            "context" => date('YmdHis')
        );

        foreach ($datas as $data) {
            array_push($this->requestData['data']['items'],
                array(
                    "uniqueCode" => uniqid(),
                    "sectionCode" => $this->sectionCode,
                    "recipientNo" => $data['recipientNo'],
                    "talkType" => $this->talkType,
                    "talkTemplateCode" => $this->talkTemplateCode,
                    "talkContent" => $data['msgContent'],
                    "buttonName" => "",
                    "buttonUrl" => "",
                    "msgType" => $this->_getMsgType($data['msgContent']),
                    "msgContent" => $data['msgContent'],
                    "msgCallback" => $this->msgCallback,
                )
            );
        }

        $res = $this->_request();
        if ($res['success']) {
            return $this->_result();
        } else {
            return $this->_result("9999", "카카오톡 API Error");
        }
    }

    public function sendStructure($recipientNo, $msgContent)
    {
        return array("recipientNo" => str_replace("-", "", $recipientNo), "msgContent" => $msgContent);
    }

    private function _getMsgType($content)
    {
        if (mb_strwidth($content, 'UTF-8') > $this->smsLimitbytes) {
            return 'LMS';
        } else {
            return 'SMS';
        }
    }

    private function _request()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json;charset=utf-8", "goodsFLOW-Api-Key: " . $this->apiKey));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        if ($this->requestData) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->requestData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($ch);
        curl_close($ch);
        return json_decode($return, true);
    }

    private function _result($code = "0000", $msg = "성공")
    {
        return array('code' => $code, 'msg' => $msg);
    }
}