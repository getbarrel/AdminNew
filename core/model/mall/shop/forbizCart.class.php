<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-12
 * Time: 오후 1:56
 */

require_once constant("CORE_ROOT") . "/model/mall/shop/forbizShop.class.php";

class forbizCart extends forbizShop
{
    /**
     * 비회원일때 사용한 카트 정보를 로그인시 이용할수 있도록 mem_ix update
     * @param $code
     */
    public function updateLoginUserCartData($code)
    {
        $this->db->query("update shop_cart set mem_ix = '" . $code . "' where cart_key = '" . session_id() . "' and mem_ix =''");
    }
}