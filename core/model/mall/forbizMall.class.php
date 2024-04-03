<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-02-26
 * Time: 오후 5:18
 */

require_once constant("CORE_ROOT") . "/model/forbizModel.class.php";

class forbizMall extends forbizModel
{
    private static $mallIx;

    /**
     * get mall ix
     * @return mixed
     */
    protected function getMallIx()
    {
        if (empty(self::$mallIx)) {
            self::$mallIx = $this->_getMallIx();
        }
        return self::$mallIx;
    }

    /**
     * 디비에서 mall ix 정보 가지고 오기
     * @return mixed
     */
    private function _getMallIx()
    {
        $domain = $this->_getDomain();
        $sql = "SELECT mall_ix FROM shop_shopinfo WHERE ";

        if ($this->_checkMobileDomain($domain)) {
            $sql .= "mall_mobile_domain = '" . $domain . "' ";
        } else {
            $sql .= "mall_domain = '" . $domain . "'";
        }
        $this->db->query($sql);
        $this->db->fetch();
        return $this->db->dt['mall_ix'];
    }

    /**
     * 도메인 get
     * @return mixed
     */
    private function _getDomain()
    {
        return str_replace('www.', '', $_SERVER['HTTP_HOST']);
    }

    /**
     * 모바일 도메인 체크
     * @param $domain
     * @return bool
     */
    private function _checkMobileDomain($domain)
    {
        return (substr($domain, 0, 2) == "m." ? true : false);
    }
}
