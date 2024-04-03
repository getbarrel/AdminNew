<?php

/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-07-14
 * Time: 오후 6:50
 */
require($_SERVER["DOCUMENT_ROOT"]."/shop/inipay_standard/libs/INILib.php");
include ($_SERVER["DOCUMENT_ROOT"]."/mysql_user/".str_replace("www.","",$_SERVER["HTTP_HOST"]).".php");
include $_SERVER['DOCUMENT_ROOT']."/class/orders.class";

$logPath = $_SERVER['DOCUMENT_ROOT'] . '/data/' . $mall_id. '_data/_pg/inipay_standard';

if (!is_dir($logPath)) {
    mkdir($logPath, 0777);
    chmod($logPath, 0777);
}

$CancelLogPath = $_SERVER['DOCUMENT_ROOT'] . '/data/' . $mall_id. '_data/_pg/inipay_standard/cancel';

if (!is_dir($CancelLogPath)) {
    mkdir($CancelLogPath, 0777);
    chmod($CancelLogPath, 0777);
}


class inipay_standard
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
        $this->logPath = $_SERVER['DOCUMENT_ROOT'] . '/data/' . $mall_id. '_data/_pg/inipay_standard/cancel';
        $this->HomePath = $_SERVER['DOCUMENT_ROOT'] . '/shop/inipay_standard';
    }

    public function cancelService($data){

        $orders = new OrderProcess();
        $pg_info = $orders->getPginfo('inipay_standard');

        if($pg_info['service_type'] == 'service' ){
            $mid = $pg_info['mid'];
        }else{
            $mid = "INIpayTest";  // 가맹점 ID(가맹점 수정후 고정)
        }

        /* * *************************************
 * 2. INIpay41 클래스의 인스턴스 생성 *
 * ************************************* */
        $inipay = new INIpay50;

        /* * *******************
         * 3. 취소 정보 설정 *
         * ******************* */
        $inipay->SetField("inipayhome", $this->HomePath); // 이니페이 홈디렉터리(상점수정 필요)
        $inipay->SetField("type", "cancel");                            // 고정 (절대 수정 불가)
        $inipay->SetField("debug", "true");                             // 로그모드("true"로 설정하면 상세로그가 생성됨.)
        $inipay->SetField("mid", $mid);                                 // 상점아이디
        /* * ************************************************************************************************
         * admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
         * 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
         * 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
         * 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
         * ************************************************************************************************ */
        $inipay->SetField("admin", "1111");
        $inipay->SetField("tid", $data['TID']);                                 // 취소할 거래의 거래아이디
        $inipay->SetField("cancelmsg", $data['CancelMsg']);                           // 취소사유

        /* * **************
         * 4. 취소 요청 *
         * ************** */
        $inipay->startAction();

        /* * **************************************************************
         * 5. 취소 결과                                           	*
         *                                                        	*
         * 결과코드 : $inipay->getResult('ResultCode') ("00"이면 취소 성공)  	*
         * 결과내용 : $inipay->getResult('ResultMsg') (취소결과에 대한 설명) 	*
         * 취소날짜 : $inipay->getResult('CancelDate') (YYYYMMDD)          	*
         * 취소시각 : $inipay->getResult('CancelTime') (HHMMSS)            	*
         * 현금영수증 취소 승인번호 : $inipay->getResult('CSHR_CancelNum')    *
         * (현금영수증 발급 취소시에만 리턴됨)                          *
         * ************************************************************** */
        $this->result["resultCode"]  = $inipay->getResult('ResultCode');
        $this->result["resultMsg"]   = $inipay->getResult('ResultMsg');
        $this->result["cancelDate"]  = $inipay->getResult('CancelDate');
        $this->result["cancelTime"]  = $inipay->getResult('CancelTime');
        $this->result["CancelNum"]  = $inipay->getResult('CSHR_CancelNum');


        return $this->result;
    }
}