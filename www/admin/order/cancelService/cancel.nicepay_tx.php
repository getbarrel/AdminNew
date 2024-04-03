<?php

/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-07-14
 * Time: 오후 4:44
 */
header("Content-Type:text/html; charset=UTF-8;");
include ($_SERVER["DOCUMENT_ROOT"]."/mysql_user/".str_replace("www.","",$_SERVER["HTTP_HOST"]).".php");
require_once $_SERVER["DOCUMENT_ROOT"].'/shop/nicepay_tx/lib/nicepay/web/NicePayWEB.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/shop/nicepay_tx/lib/nicepay/core/Constants.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/shop/nicepay_tx/lib/nicepay/web/NicePayHttpServletRequestWrapper.php';



$logPath = $_SERVER['DOCUMENT_ROOT'] . '/data/' . $mall_id. '_data/_pg/nicepay_tx/cancel';

if (!is_dir($logPath)) {
    mkdir($logPath, 0777);
    chmod($logPath, 0777);
}


class nicepay_tx
{
    private $data;
    private $logPath;
    private $httpRequestWrapper;
    private $nicepayWEB;
    private $result;
    private $mall_id;

    public function __construct(){
        global $mall_id;
        $result = null;
        $this->logPath = $_SERVER['DOCUMENT_ROOT'] . '/data/' . $mall_id. '_data/_pg/nicepay_tx/cancel';
    }

    public function cancelService($data){

        $this->data = $data;

        $httpRequestWrapper = new NicePayHttpServletRequestWrapper($this->data);
        $_REQUEST = $httpRequestWrapper->getHttpRequestMap();
        $nicepayWEB = new NicePayWEB();

        $nicepayWEB->setParam("NICEPAY_LOG_HOME",$this->logPath);             // 로그 디렉토리 설정
        $nicepayWEB->setParam("APP_LOG","1");                           // 이벤트로그 모드 설정(0: DISABLE, 1: ENABLE)
        $nicepayWEB->setParam("EVENT_LOG","1");                         // 어플리케이션로그 모드 설정(0: DISABLE, 1: ENABLE)
        $nicepayWEB->setParam("EncFlag","S");                           // 암호화플래그 설정(N: 평문, S:암호화)
        $nicepayWEB->setParam("SERVICE_MODE", "CL0");                   // 서비스모드 설정(결제 서비스 : PY0 , 취소 서비스 : CL0)
        $nicepayWEB->setParam("CHARSET", "UTF8");                       // 인코딩

        /*
        *******************************************************
        * <취소 결과 필드>
        *******************************************************
        */
        $responseDTO = $nicepayWEB->doService($this->data);

        $this->result["resultCode"]  = $responseDTO->getParameter("ResultCode");        // 결과코드 (취소성공: 2001, 취소성공(LGU 계좌이체):2211)
        $this->result["resultMsg"]   = $responseDTO->getParameterUTF("ResultMsg");      // 결과메시지
        $this->result["cancelAmt"]   = $responseDTO->getParameter("CancelAmt");         // 취소금액
        $this->result["cancelDate"]  = $responseDTO->getParameter("CancelDate");        // 취소일
        $this->result["cancelTime"]  = $responseDTO->getParameter("CancelTime");        // 취소시간
        $this->result["cancelNum"]   = $responseDTO->getParameter("CancelNum");         // 취소번호
        $this->result["payMethod"]   = $responseDTO->getParameter("PayMethod");         // 취소 결제수단
        $this->result["mid"]         = $responseDTO->getParameter("MID");               // 상점 ID
        $this->result["tid"]         = $responseDTO->getParameter("TID");               // 거래아이디 TID

        return $this->result;
    }
}