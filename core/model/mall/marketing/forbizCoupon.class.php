<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-23
 * Time: 오전 11:04
 */
require_once constant("CORE_ROOT") . "/model/mall/marketing/forbizMarketing.class.php";

class forbizCoupon extends forbizMarketing
{
    protected $rule;
    protected $unlimitedDate = '9999-12-31 23:59:59'; //무기한

    /**
     * 회원 가입 지급
     * @param $userCode
     * @param $agentType W:PC, M:모바일
     */
    public function giveJoin($userCode, $agentType)
    {
        $this->_setRule();

        $publishIxList = array();
        if ($this->rule['member_coupon_use_yn'] == "Y" && count($this->rule['member_publish_ix']) > 0 && $agentType == 'W') {
            $publishIxList = $this->rule['member_publish_ix'];
        } elseif ($this->rule['mobile_member_coupon_use_yn'] == "Y" && count($this->rule['mobile_member_publish_ix']) > 0 && $agentType == 'M') {
            $publishIxList = $this->rule['mobile_member_publish_ix'];
        }

        if (count($publishIxList) > 0) {
            foreach ($publishIxList as $list) {
                if (!$this->_checkRegist($list['publish_ix'], $userCode)) {
                    $useDate = $this->_getPeriodOfUseDate($list['publish_ix']);
                    $this->_regist($list['publish_ix'], $userCode, $useDate['useStartDate'], $useDate['useLimitDate']);
                }
            }
        }
    }

    /**
     * set rule
     */
    protected function _setRule()
    {
        if (empty($this->rule)) {
            require_once constant('CORE_ROOT') . "/common/di.class.php";
            $diClass = new di('shardMemory');
            $this->rule = $diClass->shardMemory->getData('b2c_coupon_rule');
        }
    }

    /**
     * get 사용 일자
     * @param $publishIx
     * @return array
     */
    protected function _getPeriodOfUseDate($publishIx)
    {
        $sql = "SELECT publish_ix,
                   use_date_type,
                   publish_date_differ,
                   publish_type,
                   publish_date_type,
                   regist_date_type,
                   regist_date_differ,
                   use_sdate,
                   use_edate,
                   regdate
            FROM shop_cupon_publish
            WHERE publish_ix = '" . $publishIx . "'";
        $this->db->query($sql);
        $this->db->fetch();
        $publishData = $this->db->dt;

        $nowDate = date('Y-m-d 00:00:00');

        switch ($publishData['use_date_type']) {
            case '1': //발행일
                if ($publishData['publish_date_type'] == 1) $addDateType = 'year';
                else if ($publishData['publish_date_type'] == 2) $addDateType = 'month';
                else if ($publishData['publish_date_type'] == 3) $addDateType = 'day';
                else $addDateType = '';
                $useStartDate = $nowDate;
                $useLimitDate = $this->_getMakeDate($publishData['regdate'], $addDateType, $publishData['publish_date_differ']);
                break;
            case '2': //발급일
                if ($publishData['regist_date_type'] == 1) $addDateType = 'year';
                else if ($publishData['regist_date_type'] == 2) $addDateType = 'month';
                else if ($publishData['regist_date_type'] == 3) $addDateType = 'day';
                else $addDateType = '';
                $useStartDate = $nowDate;
                $useLimitDate = $this->_getMakeDate($nowDate, $addDateType, $publishData['regist_date_differ']);
                break;
            case '3': //사용기간 지정
                $useStartDate = $publishData['use_sdate'];
                $useLimitDate = $publishData['use_edate'];
                break;
            case '9': //무기한
                $useStartDate = $nowDate;
                $useLimitDate = $this->unlimitedDate;
                break;
        }
        return array('useStartDate' => $useStartDate, 'useLimitDate' => $useLimitDate);
    }

    /**
     * make date
     * @param $standardDate
     * @param $addDateType
     * @param $number
     * @return false|string
     */
    protected function _getMakeDate($standardDate, $addDateType, $number)
    {
        return date('Y-m-d 00:00:00', strtotime($standardDate . ' + ' . $number . ' ' . $addDateType));
    }

    /**
     * 쿠폰 발급 체크
     * @param $publishIx
     * @param $userCode
     * @return bool
     */
    protected function _checkRegist($publishIx, $userCode)
    {
        $sql = "SELECT publish_ix FROM shop_cupon_regist WHERE publish_ix='" . $publishIx . "' AND mem_ix = '" . $userCode . "'";
        $this->db->query($sql);
        return ($this->db->total > 0 ? true : false);
    }

    /**
     * 회원 쿠폰 발급
     * @param $publishIx
     * @param $userCode
     * @param $useStartDate
     * @param $useLimitDate
     */
    protected function _regist($publishIx, $userCode, $useStartDate, $useLimitDate)
    {
        $sql = "INSERT INTO shop_cupon_regist (publish_ix, mem_ix, use_sdate, use_date_limit, regdate)
            VALUES ('" . $publishIx . "','" . $userCode . "','" . $useStartDate . "','" . $useLimitDate . "',NOW())";
        $this->db->query($sql);
    }
}