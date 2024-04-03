<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-20
 * Time: 오후 9:46
 */

require_once constant("CORE_ROOT") . "/model/mall/store/forbizStore.class.php";

class forbizPolicy extends forbizStore
{
    /**
     * 최근 약관 데이터 가지고 오기
     * @return array
     */
    public function getRecencyInfo()
    {
        $argList = func_get_args();
        if (!(count($argList) > 0)) {
            return array();
        }

        $sql = "SELECT spi.pi_ix,
               spi.pi_code,
               spi.pi_contents,
               spi.regdate
        FROM shop_policy_info spi,
             (SELECT pi_code, max(startdate) startdate
              FROM shop_policy_info
              WHERE     startdate < now()
                    AND pi_code IN ('" . implode("', '", $argList) . "')
                    AND disp = 'Y'
              GROUP BY pi_code) subspi
        WHERE spi.pi_code = subspi.pi_code AND spi.startdate = subspi.startdate
        ORDER BY pi_code";
        $this->db->query($sql);

        $data = array();
        foreach ($this->db->fetchall() as $key => $val) {
            $data[$val['pi_code']]['ix'] = $val['pi_ix'];

            $val['pi_contents'] = str_replace("[DATE]", date("Y년 [m월 d일]", strtotime($val['regdate'])), $val['pi_contents']);

            $data[$val['pi_code']]['contents'] = stripslashes($val['pi_contents']);
        }

        return $data;
    }

    /**
     * 회원 약관 동의 로그
     * @param $piIx
     * @param $userCode
     * @param $userId
     * @param $name
     * @param $memType
     */
    public function insertAgreementHistory($piIx, $userCode, $userId, $name, $memType)
    {
        $sql = "INSERT INTO shop_agreement_history
            (user_code, user_id, user_name, user_type, user_ip, pi_ix, regdate)
            VALUES
            ('" . $userCode . "','" . $userId . "',HEX(AES_ENCRYPT('" . $name . "','" . $this->db->ase_encrypt_key . "'))
            ,'" . $memType . "','" . $_SERVER['REMOTE_ADDR'] . "','" . $piIx . "',now())";
        $this->db->query($sql);
    }
}