<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-30
 * Time: 오후 2:51
 */

require_once constant("CORE_ROOT") . "/model/mall/member/forbizMember.class.php";

class forbizSleepMember extends forbizMember
{
    /**
     * 휴면 계정 전환 단계 set
     * @param $step
     */
    public function setReleaseStep($step)
    {
        $_SESSION['sleep']['step'] = $step;
    }

    /**
     * 휴면 계정 전환 단계 get
     * @param $step
     * @return mixed
     */
    public function getReleaseStep()
    {
        return $_SESSION['sleep']['step'];
    }

    /**
     * 휴면 계정 전화 단계 초기화
     */
    public function resetReleaseStep()
    {
        $_SESSION['sleep'] = "";
    }

    /**
     * 휴면 계정 활성화
     * @param $userCode
     * @param $message
     * @return bool
     */
    public function activeMember($userCode, $message)
    {
        $result = true;

        $transction = $this->db->query("SET AUTOCOMMIT=0");
        $transction = $this->db->query("BEGIN");

        $sql = "INSERT common_user_sleep_log SET
				code = '" . $userCode . "',
				id = (SELECT id FROM common_user_sleep WHERE code = '" . $userCode . "' ),
				name = (SELECT name FROM common_member_detail_sleep WHERE code = '" . $userCode . "' ),
				status = 'U',
				message = '" . $message . "',
				charger_ix = '" . $userCode . "',
				change_type = 'M',
				regdate = NOW() ";
        $transction = $this->db->query($sql);
        if (!$transction || mysql_affected_rows() == 0) $result = false;

        $sql = "INSERT INTO common_user
          SELECT * FROM common_user_sleep 
          WHERE code = '" . $userCode . "' 
          AND NOT EXISTS (SELECT code FROM common_user WHERE code='" . $userCode . "')";
        $transction = $this->db->query($sql);
        if (!$transction || mysql_affected_rows() == 0) $result = false;

        $sql = "DELETE FROM common_user_sleep WHERE code = '" . $userCode . "' ";
        $transction = $this->db->query($sql);
        if (!$transction || mysql_affected_rows() == 0) $result = false;

        $sql = "INSERT INTO common_member_detail 
          SELECT * FROM common_member_detail_sleep 
          WHERE code = '" . $userCode . "' 
          AND NOT EXISTS (SELECT code FROM common_member_detail WHERE code='" . $userCode . "')";
        $transction = $this->db->query($sql);
        if (!$transction || mysql_affected_rows() == 0) $result = false;

        $sql = "DELETE FROM common_member_detail_sleep WHERE code = '" . $userCode . "' ";
        $transction = $this->db->query($sql);
        if (!$transction || mysql_affected_rows() == 0) $result = false;

        $sql = "SELECT oid FROM shop_order WHERE user_code = '" . $userCode . "'";
        $this->db->query($sql);

        if ($this->db->total > 0) {
            $order_datas = $this->db->fetchall();
            for ($x = 0; $x < count($order_datas); $x++) {
                $oid = $order_datas[$x]['oid'];

                $sql = "UPDATE shop_order o , separation_shop_order so SET
                          o.btel=so.btel
                          ,o.bmobile=so.bmobile
                          ,o.bmail=so.bmail
                          ,o.bzip=so.bzip
                          ,o.baddr=so.baddr
                        WHERE o.oid = '" . $oid . "'
                        AND o.oid=so.oid";
                $this->db->query($sql);

                $sql = "DELETE FROM separation_shop_order WHERE oid='" . $oid . "'";
                $this->db->query($sql);

                $sql = "UPDATE shop_order_detail_deliveryinfo d, separation_shop_order_deliveryinfo sd SET
                                  d.rname=sd.rname
                                  ,d.rtel=sd.rtel
                                  ,d.rmobile=sd.rmobile
                                  ,d.rmail=sd.rmail
                                  ,d.zip=sd.zip
                                  ,d.addr1=sd.addr1
                                  ,d.addr2=sd.addr2
                                WHERE d.oid = '" . $oid . "'
                                AND d.odd_ix=sd.odd_ix";
                $this->db->query($sql);

                $sql = "DELETE FROM separation_shop_order_deliveryinfo WHERE oid='" . $oid . "'";
                $this->db->query($sql);
            }
        }

        if ($result) {
            $this->db->query("COMMIT");
        } else {
            $this->db->query("ROLLBACK");
        }
        $this->db->query("SET AUTOCOMMIT=1");

        return $result;
    }
}