<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-04-02
 * Time: 오후 2:10
 */

require_once constant("CORE_ROOT") . "/model/mall/store/forbizStore.class.php";

class forbizStoreInfo extends forbizStore
{
    protected $shopInfoTableKeys = array('mall_data_root', 'mall_domain', 'mall_deny_id');

    /**
     * mall config get
     * @return array
     */
    public function getConfig()
    {
        $argList = func_get_args();
        $returnData = array();
        $shopInfoTableKeyList = array();
        $mallConfigTableKeyList = array();
        foreach ($argList as $configName) {
            if (in_array($configName, $this->shopInfoTableKeys)) {
                $shopInfoTableKeyList[] = $configName;
            } else {
                $mallConfigTableKeyList[] = $configName;
            }
        }

        if (count($shopInfoTableKeyList) > 0) {
            $returnData = array_merge($returnData, $this->_getShopInfo($shopInfoTableKeyList));
        }

        if (count($mallConfigTableKeyList) > 0) {
            $returnData = array_merge($returnData, $this->_getMallConfig($mallConfigTableKeyList));
        }

        return $returnData;
    }

    /**
     * 몰 관리자 정보
     * @return mixed
     */
    public function getCompanyInfo()
    {
        $sql = "SELECT ccd.com_name,
                ccd.com_zip,
                ccd.com_addr1,
                ccd.com_addr2,
                ccd.com_phone,
                ccd.com_mobile,
                ccd.com_ceo,
                ccd.com_email,
                ccd.com_number,
                ccd.online_business_number,
                ccd.officer_name,
                ccd.officer_email,
                ccd.cs_phone
            FROM common_company_detail ccd
            WHERE ccd.com_type = 'A' ";
        $this->db->query($sql);
        $this->db->fetch();

        return $this->db->dt;
    }

    /**
     * 등록 되어 있는 라이센스 리스트 get
     * @return array
     */
    public function getLicenseList()
    {
        $sql = "SELECT mall_domain_key FROM shop_shopinfo";
        $this->db->query($sql);
        $list = $this->db->fetchall();

        $result = array();
        for ($i = 0; $i < count($list); $i++) {
            $result[] = $list[$i]["mall_domain_key"];
        }
        return $result;
    }

    /**
     * shop_info 테이블에서 get
     * @param $keyList
     * @return array
     */
    protected function _getShopInfo($keyList)
    {
        $sql = "SELECT " . implode(",", $keyList) . " FROM shop_shopinfo WHERE mall_ix = '" . $this->getMallIx() . "' LIMIT 1";
        $this->db->query($sql);
        $this->db->fetch();

        $data = array();
        if (count($keyList) > 0) {
            foreach ($keyList as $key) {
                $data[$key] = $this->db->dt[$key];
            }
        }
        return $data;
    }

    /**
     * mall_config 테이블에서 get
     * @param $keyList
     * @return array
     */
    protected function _getMallConfig($keyList)
    {
        $sql = "SELECT config_name, config_value 
              FROM shop_mall_config 
              WHERE mall_ix = '" . $this->getMallIx() . "' 
                AND config_name IN ('" . implode("','", $keyList) . "') ";
        $this->db->query($sql);
        $list = $this->db->fetchall();
        $data = array();
        if (count($list) > 0) {
            foreach ($list as $value) {
                $data[$value['config_name']] = $value['config_value'];
            }
        }
        return $data;
    }
}