<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-16
 * Time: 오후 6:10
 */

require_once constant("CORE_ROOT") . "/model/mall/shop/forbizShop.class.php";

class forbizOrder extends forbizShop
{
    /**
     * 고객이 주문번호 - 없이 넘어올 경우 주문번호 수정
     * @param $oid
     * @return mixed|string
     */
    public function orderIdRlue($oid)
    {
        $oid = str_replace('-', '', $oid);
        $oid = substr($oid, 0, 12) . '-' . substr($oid, -7);
        return $oid;
    }

    /**
     * 비회원 주문조회 oid 만 리턴
     * @param $oid
     * @param $bname
     * @param $orderPw
     * @return mixed
     */
    public function getNonMemerOrder($oid, $bname, $orderPw)
    {
        $sql = "SELECT oid
            FROM shop_order
            WHERE     oid = '" . $oid . "'
                  AND bname = '" . $bname . "'
                  AND order_pw = '" . $orderPw . "'
                  AND status != 'SR'
                  AND user_code = '' ";
        $this->db->query($sql);
        $this->db->fetch();
        return $this->db->dt;
    }

    /**
     * 비회원 주문비밀번호 암호화
     * @param $orderPw
     * @return string
     */
    public function encryptOrderPassword($orderPw)
    {
        return hash("sha256", $orderPw);
    }
}