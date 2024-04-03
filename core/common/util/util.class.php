<?php

/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-12
 * Time: 오후 5:38
 */
class util
{
    /**
     * php header location
     * @param string $url {이동경로}
     */
    public function phpHeaderLocation($url)
    {
        header("Location:" . $url);
    }

    /**
     * 모바일 체크
     * @return bool
     */
    public function isMobile()
    {
        $is_mobile = false;

        //PC에서도 확인할 수 있도록
        if (strtolower(substr($_SERVER["HTTP_HOST"], 0, 2)) == "m.") {
            $is_mobile = true;

            //m 도메인 접근시 PC버전 사용 세션 제거
            $_SESSION['use_pc_version'] = 'N';
        }

        //PC버전 사용시 FALSE 리턴
        if ($_SESSION['use_pc_version'] == 'Y') {
            return false;
        }

        //agent check
        $mobile_agent_list = array("/android/", "/iphone/", "/mobile/", "/blackberry/", "/windows ce/", "/lg/", "/samsung/", "/sonyericsson/");
        foreach ($mobile_agent_list as $key => $value):
            if (preg_match($value, strtolower($_SERVER["HTTP_USER_AGENT"]))) {
                $is_mobile = true;
            }
        endforeach;

        //webview check
        if ($_SESSION['app_type'] != '') {
            $is_mobile = true;
        }

        return $is_mobile;
    }

    /**
     * 회원 판매 타입
     * @return string R:소매, W:도매
     */
    public function userSellingType()
    {
        $type = "R";
        if ($_SESSION["user"]["selling_type"] == "W") {
            $type = "W";
        }
        return $type;
    }

    /**
     * sms 및 email 과리자 설정 기준으로 보내기
     * @param $mcCode
     * @param $email
     * @param $mobile
     * @param array $sendData
     * @param string $language
     * @param array $emailCc
     * @param array $mobileCc
     */
    public function sendMessage($mcCode, $email, $mobile, $sendData = array(), $language = 'korean', $emailCc = array())
    {
        $result = array();
        $result['eamil'] = true;
        $result['sms'] = true;

        require_once constant("MALL_ROOT") . "/model/store/message.class.php";
        $messageClass = new message();

        $messageConfig = $messageClass->getConfigByMcCode($mcCode);

        require_once constant("MALL_ROOT") . "/model/store/storeInfo.class.php";
        $storeInfoClass = new storeInfo();
        
        //몰 config 정보
        $storeData = $storeInfoClass->getConfig('mall_data_root', 'mall_name', 'mall_domain'
            , 'kakao_alim_talk_yn', 'kakao_alim_talk_memberCode', 'kakao_alim_talk_apiKey');

        //몰 업체 정보
        $storeCompanyData = $storeInfoClass->getCompanyInfo();

        //공통 치환
        require_once constant('CORE_ROOT') . "/common/di.class.php";
        $diClass = new di('protocol');

        if (!is_array($sendData)) {
            $sendData = array();
        }

        $sendData['MALL_DOMAIN'] = $diClass->protocol->getProtocol() . $storeData['mall_domain'];
        $sendData['MALL_NAME'] = $storeData['mall_name'];
        $sendData['MALL_DATA_PATH'] = (constant('IMAGE_SERVER_DOMAIN') != '' ? constant('IMAGE_SERVER_DOMAIN') : $sendData['MALL_URL']) . $storeData['mall_data_root'];
        $sendData['COM_NAME'] = $storeCompanyData['com_name'];
        $sendData['COM_CEO'] = $storeCompanyData['com_ceo'];
        $sendData['COM_ADDR1'] = $storeCompanyData['com_addr1'];
        $sendData['COM_ADDR2'] = $storeCompanyData['com_addr2'];
        $sendData['COM_EMAIL'] = $storeCompanyData['com_email'];
        $sendData['COM_NUMBER'] = $storeCompanyData['com_number'];
        $sendData['ONLINE_BUSINESS_NUMBER'] = $storeCompanyData['online_business_number'];
        $sendData['OFFICER_NAME'] = $storeCompanyData['officer_name'];
        $sendData['OFFICER_EMAIL'] = $storeCompanyData['officer_email'];
        $sendData['CS_PHONE'] = $storeCompanyData['cs_phone'];

        //email
        if (($messageConfig['mc_mail_usersend_yn'] == "Y" || $messageConfig['mc_mail_adminsend_yn'] == "Y") && !empty($email)) {

            $includeLanguagePath = constant("MALL_ROOT") . $storeData['mall_data_root'] . "/_language/" . $language . "/common.php";
            if (file_exists($includeLanguagePath)) {
                include_once $includeLanguagePath;
            }

            require_once(constant("CORE_ROOT") . "/common/library/extension/template_/Template_.class.php");

            $tpl = new Template_();
            $tpl->template_dir = constant("MALL_ROOT") . "" . $storeData['mall_data_root'] . "/email_templet";
            $tpl->compile_dir = constant("MALL_ROOT") . $storeData['mall_data_root'] . "/compile_/email_templet";

            $tpl->define('mail', "ms_mail_" . $mcCode . ".htm");

            if (is_array($sendData) && count($sendData)) {
                $tpl->assign($sendData);
            }

            $emailBody = $tpl->fetch('mail');
            $emailTitle = $messageConfig['mc_mail_title'];
            $from = $storeCompanyData['com_email'];

            if (is_array($sendData) && count($sendData) > 0) {
                foreach ($sendData as $key => $value) {
                    $emailTitle = str_replace('{' . $key . '}', $value, $emailTitle);
                }
            }

            $recipients = array_merge(array(), $emailCc);
            if ($messageConfig['mc_mail_usersend_yn'] == "Y") $recipients[] = $email;
            if ($messageConfig['mc_mail_adminsend_yn'] == "Y") $recipients[] = $from;

            require_once(constant("CORE_ROOT") . "/common/message/email.class.php");
            $emailClass = new email();
            $result['eamil'] = $emailClass->send($recipients, $email, $from, $emailTitle, $emailBody);
        }

        $smsBody = $messageConfig['mc_sms_text'];
        if (is_array($sendData) && count($sendData) > 0) {
            foreach ($sendData as $key => $value) {
                $smsBody = str_replace('{' . $key . '}', $value, $smsBody);
            }
        }

        $sendPhone = $storeCompanyData['com_phone'];

        if (($messageConfig['mc_sms_usersend_yn'] == "Y" && !empty($mobile))
            || ($messageConfig['mc_sms_adminsend_yn'] == "Y" && !empty($sendPhone))
        ) { //sms
            require_once(constant("CORE_ROOT") . "/common/message/sms.class.php");

            //몰 라이센스 정보
            $licenseList = $storeInfoClass->getLicenseList();

            //sms 보내기
            $smsClass = new sms($licenseList);
            $smsClass->send_phone = $sendPhone;
            $smsClass->send_name = $storeCompanyData['com_name'];
            $smsClass->msg_code = $sendData['msgCode'];
            $smsClass->dest_name = $sendData['userName'];
            $smsClass->msg_body = $smsBody;

            if ($messageConfig['mc_sms_usersend_yn'] == "Y" && !empty($mobile)) {
                $smsClass->dest_phone = str_replace("-", "", $mobile);
                $result['sms'] = $smsClass->send();
            }

            if ($messageConfig['mc_sms_adminsend_yn'] == "Y" && !empty($sendPhone)) {
                $smsClass->dest_phone = str_replace("-", "", $sendPhone);
                $smsClass->send();
            }
        }

        if (($messageConfig['mc_sms_usersend_yn'] == "K" && !empty($mobile))
            || ($messageConfig['mc_sms_adminsend_yn'] == "K" && !empty($sendPhone))
        ) { //kakaoAlimTalk
            require_once(constant("CORE_ROOT") . "/common/message/kakaoAlimTalk.class.php");

            $kakaoAlimTalkClass = new KakaoAlimTalk($storeData['kakao_alim_talk_yn'], $storeData['kakao_alim_talk_memberCode']
                , $storeData['kakao_alim_talk_apiKey'], $messageConfig['kakao_alim_talk_template_code'], $sendData['msgCode'], $sendPhone);

            $datas = array();
            if ($messageConfig['mc_sms_usersend_yn'] == "K" && !empty($mobile)) {
                array_push($datas, $kakaoAlimTalkClass->sendStructure($mobile, $smsBody));
            }
            if ($messageConfig['mc_sms_adminsend_yn'] == "K" && !empty($sendPhone)) {
                array_push($datas, $kakaoAlimTalkClass->sendStructure($sendPhone, $smsBody));
            }

            if (count($datas) > 0) {
                $res = $kakaoAlimTalkClass->send($datas);
                if ($res['code'] != '0000' && $result['sms'] == true) {
                    $result['sms'] = false;
                }
            }
        }

        return $result;
    }
}