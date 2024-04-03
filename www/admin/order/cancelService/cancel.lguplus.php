<?php

/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-06-21
 * Time: 오후 7:49
 */
class lguplus
{
    private $result;

    public function __construct()
    {
        $result = null;
    }

    /**
     * @param $data
     */
    public function cancelService($data)
    {

        include $_SERVER["DOCUMENT_ROOT"]."/class/pg.class";
        $sattle_module = 'lguplus';
        $pg = new Pg_Call($sattle_module);
        $pg_info = $pg->getPgDataInfo();

        if($pg_info[lguplus_type] == "test"){
            $service_key = $pg_info[lguplus_key]; //가맹점 상점키 설정
            $lguplus_id = $pg_info[lguplus_id];
            $CST_PLATFORM = "test";
        }else{
            $service_key = $pg_info[lguplus_key]; //가맹점 상점키 설정
            $lguplus_id = $pg_info[lguplus_id];
            $CST_PLATFORM = "service";
        }

        $CST_PLATFORM         		= $CST_PLATFORM;       		//LG유플러스 결제 서비스 선택(test:테스트, service:서비스)

        $CST_MID              		= $lguplus_id;           		//상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)

        if($CST_PLATFORM == 'test'){
            $LGD_MID = "t".$CST_MID;
        }else{
            $LGD_MID = $CST_MID;
        }
        $service_id = $LGD_MID;




        $LGD_TID              		= $data["lguplus_tid"];							  		//LG유플러스으로 부터 내려받은 거래번호(LGD_TID)
        $LGD_CANCELAMOUNT     		= $data["cancel_amount"];                          //부분취소 금액
        //$LGD_REMAINAMOUNT     		= $HTTP_POST_VARS["LGD_REMAINAMOUNT"];          //취소전 남은금액

        $LGD_CANCELTAXFREEAMOUNT    = $data["cancel_tax_free_amount"];   //면세대상 부분취소 금액 (과세/면세 혼용상점만 적용)
        $LGD_CANCELREASON     		= $data["reason"];          //취소사유

        $LGD_RFACCOUNTNUM           = $data["bank_number"];	 		//환불계좌 번호(가상계좌 환불인경우만 필수)
        $LGD_RFBANKCODE             = change_bnakcode("".$data["bank_code"]."");	 		//환불계좌 은행코드(가상계좌 환불인경우만 필수)
        $LGD_RFCUSTOMERNAME         = $data["bank_owner"]; 		//환불계좌 예금주(가상계좌 환불인경우만 필수)
        $LGD_RFPHONE                = $data["mobile"];		 		//요청자 연락처(가상계좌 환불인경우만 필수)

        $configPath 				= $_SERVER["DOCUMENT_ROOT"]."/shop/lguplus/lgdacom"; 						 		//LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.



        require_once($_SERVER["DOCUMENT_ROOT"]."/shop/lguplus/lgdacom/XPayClient.php");
        $xpay = &new XPayClient($configPath, $CST_PLATFORM);
        $xpay->Init_TX($LGD_MID);

        $xpay->Set("LGD_TXNAME", "PartialCancel");
        $xpay->Set("LGD_TID", $LGD_TID);
        $xpay->Set("LGD_CANCELAMOUNT", $LGD_CANCELAMOUNT);
        $xpay->Set("LGD_REMAINAMOUNT", $LGD_REMAINAMOUNT);
//    $xpay->Set("LGD_CANCELTAXFREEAMOUNT", $LGD_CANCELTAXFREEAMOUNT);
        $xpay->Set("LGD_CANCELREASON", $LGD_CANCELREASON);
        $xpay->Set("LGD_RFACCOUNTNUM", $LGD_RFACCOUNTNUM);
        $xpay->Set("LGD_RFBANKCODE", $LGD_RFBANKCODE);
        $xpay->Set("LGD_RFCUSTOMERNAME", $LGD_RFCUSTOMERNAME);
        $xpay->Set("LGD_RFPHONE", $LGD_RFPHONE);
        $xpay->Set("LGD_REQREMAIN", "1");


        /*
         * 1. 결제 부분취소 요청 결과처리
         *
         */
        if ($xpay->TX()) {
            //1)결제 부분취소결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)
            //echo "결제 부분취소 요청이 완료되었습니다.  <br>";
            //echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
            //echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";

            $this->result["resultCode"] = $xpay->Response_Code(); // 결과코드 (정상 :0000(취소성공) 그 외 에러)
            //$this->result["resultMsg"] = iconv("EUC-KR" , "UTF-8" , $xpay->Response_Msg()); // 결과메시지
            $this->result["resultMsg"] =  $xpay->Response_Msg(); // 결과메시지

            $keys = $xpay->Response_Names();
            foreach($keys as $name) {
                echo $name . " = " . $xpay->Response($name, 0) . "<br>";

            }
            echo "<p>";

        }else {
            //2)API 요청 실패 화면처리
            //echo "결제 부분취소 요청이 실패하였습니다.  <br>";
            //echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
            //echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";

            $this->result["resultCode"] = $xpay->Response_Code(); // 결과코드 (정상 :0000(취소성공) 그 외 에러)
            $this->result["resultMsg"] = iconv("EUC-KR" , "UTF-8" , $xpay->Response_Msg()); // 결과메시지
        }

        return $this->result;
    }
}

function change_bnakcode($code){
    switch ($code){
        case "su":
            //한국산업은행
            $lg_bank_code = "002";
            break;
        case "ku":
            //기업은행
            $lg_bank_code = "003";
            break;
        case "km":
            //국민은행
            $lg_bank_code = "004";
            break;
        case "yh":
            //외환은행
            $lg_bank_code = "005";
            break;
        case "ss":
            //수협중앙회
            $lg_bank_code = "007";
            break;
        case "nh":
            //농협중앙회
            $lg_bank_code = "011";
            break;
        case "wr":
            //우리은행
            $lg_bank_code = "020";
            break;
        case "sh":
            //신한은행
            $lg_bank_code = "088";
            break;
        case "jh":
            //신한은행(조흥은행)
            $lg_bank_code = "088";
            break;
        case "shjh":
            //신한은행(조흥통합)
            $lg_bank_code = "088";
            break;
        case "sc":
            //SC제일은행
            $lg_bank_code = "023";
            break;
        case "hn":
            //하나은행
            $lg_bank_code = "081";
            break;
        case "hn2":
            //하나은행(서울은행)
            $lg_bank_code = "081";
            break;
        case "hc":
            //한국씨티은행(한미은행)
            $lg_bank_code = "027";
            break;
        case "dk":
            //대구은행
            $lg_bank_code = "031";
            break;
        case "bs":
            //부산은행
            $lg_bank_code = "032";
            break;
        case "kj":
            //광주은행
            $lg_bank_code = "034";
            break;
        case "jj":
            //제주은행
            $lg_bank_code = "035";
            break;
        case "jb":
            //전북은행
            $lg_bank_code = "037";
            break;
        case "kn":
            //경남은행
            $lg_bank_code = "039";
            break;
        case "ct":
            //씨티은행
            $lg_bank_code = "027";
            break;
        case "po":
            //우체국
            $lg_bank_code = "071";
            break;
        case "sk":
            //새마을금고
            $lg_bank_code = "045";
            break;
        case "sn":
            //신협은행
            $lg_bank_code = "048";
            break;

    }
    return $lg_bank_code;
}