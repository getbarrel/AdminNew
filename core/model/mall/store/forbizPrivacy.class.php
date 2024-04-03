<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-12
 * Time: 오전 11:37
 */

require_once constant("CORE_ROOT") . "/model/mall/store/forbizStore.class.php";

class forbizPrivacy extends forbizStore
{
    /**
     * 비밀번호 변경 안내 기간 체크
     * @param $userChangePwDate
     * @param $userJoinDate
     * @return bool
     */
    public function isChangePassword($userChangePwDate, $userJoinDate)
    {
        if ($_SESSION['privacy_config']['change_pw_info'] == 'Y'
            && $_SESSION['privacy_config']['change_pw_day'] != ''
            && $_SESSION['privacy_config']['change_pw_continue_day'] != ''
        ) {
            if (!empty($userChangePwDate)) {
                $changePwDate = date('Y-m-d', strtotime($userChangePwDate));
            } else {
                $changePwDate = date('Y-m-d', strtotime($userJoinDate));
            }

            $changeCkeckDate = date("Y-m-d", strtotime($changePwDate . "+" . $_SESSION['privacy_config']['change_pw_day'] . "days"));

            if (strtotime($changeCkeckDate) < strtotime('now')) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 비밀번호 변경 안내 세션 생성
     * @param $boolean
     */
    public function setChangePasswordSession($boolean)
    {
        $_SESSION['user']['changeAccessPassword'] = $boolean;
    }
}