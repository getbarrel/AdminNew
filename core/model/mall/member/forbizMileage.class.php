<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-22
 * Time: 오후 7:54
 */

require_once constant("CORE_ROOT") . "/model/mall/member/forbizMember.class.php";

class forbizMileage extends forbizMember
{
    protected $type;
    protected $rule;

    /**
     * 회원 가입 지급
     * @param $userCode
     * @param $message
     */
    public function giveJoin($userCode, $message)
    {
        $this->_setRule();
        if ($this->rule['join_mileage_rate'] > 0) {
            $this->_add($userCode, $this->rule['join_mileage_rate'], '2', $message);
        }
    }

    /**
     * set rule
     */
    protected function _setRule()
    {
        if (empty($this->rule)) {
            require_once constant('CORE_ROOT') . "/common/di.class.php";
            $diClass = new di('util', 'shardMemory');
            $mileageRuleCode = $this->_getRuleCode($diClass->util->userSellingType());
            $this->type = str_replace("", "_mileage_rule", $mileageRuleCode);
            $this->rule = $diClass->shardMemory->getData($mileageRuleCode);
        }
    }

    /**
     * 정책 코드 get
     * @param $sellingType
     * @return string
     */
    protected function _getRuleCode($sellingType)
    {
        $ruleCode = "b2c_mileage_rule";
        if ($sellingType == 'W') {
            $ruleCode = "b2b_mileage_rule";
        }
        return $ruleCode;
    }

    /**
     * add
     * type 1 : 주문에 의한 적립, 2 : 회원가입에 의한 적립  3 : 수동적립, 4 : 취소적립, 5 : 배송비 적립 ,6 : 게시판 글 작성, 7 :기타
     * payprice = pt_dcprice
     * @param $mileageData
     */
    protected function _add($userCode, $mileage, $type, $message
        , $orderId = '', $orderDetailIx = '', $productId = '', $ptprice = '', $payprice = '')
    {
        if (empty($userCode) || !($mileage > 0)) {
            return;
        }

        if (!($this->rule['mileage_info_use'] == "Y" || $this->rule['mileage_info_use'] == "P")) {
            return;
        }

        //적립 완료는 * 회원가입 , 배송완료 or 구매확정 , 취소에 의한 재적립 또는 관리자에 의한 수동 적립일때 사용 따라서 state 값이 1 로 접수되면 무조건 마일리지 적립 테이블에 기록
        //마일리지 적립 범위가 본사 상품만 사용이며, 주문에 의한 적립 타입일 경우 아래 조건을 통해 충족 여부를 판단 한다.
        if ($this->rule['mileage_use_yn'] == 'N' && !empty($productId)) {
            $sql = "SELECT id FROM 
                    shop_product p, common_company_detail c 
                WHERE p.admin = c.company_id AND c.com_type='A' AND p.id = '" . $productId . "'";
            $this->db->query($sql);
            //적립하고자 하는 상품이 본사 상품이 아닐경우 마일리지 정책에 따라 프로세스를 종료 시킨다.
            if (!($this->db->total > 0)) {
                return;
            }
        }

        //이미 동일한 주문건의 적립이 존재 할경우 프로세스 진행 하지 않고 return
        if (!empty($orderId) && !empty($orderDetailIx)) {
            $sql = "SELECT * FROM shop_add_mileage WHERE oid = '" . $orderId . "' AND od_ix = '" . $orderDetailIx . "'";
            $this->db->query($sql);
            if ($this->db->total > 0) {
                return;
            }
        }

        //적립 당시 소멸 예정일에 따른 소멸 예정 일자 등록
        $extinctionDate = mktime(date("h"), date("i"), date("s"), date("m") + $this->rule['cancel_month'], date("d"), date("Y") + $this->rule['cancel_year']);

        $sql = "INSERT INTO shop_add_mileage 
				(uid,add_type,oid,od_ix,pid,am_mileage,am_state,reserve_type,auto_cancel,message,date,regdate,extinction_date) 
			    VALUES ('" . $userCode . "','" . $type . "','" . $orderId . "','" . $orderDetailIx . "','" . $productId . "','" . $mileage . "','1','" . $this->type . "','N','" . $message . "',NOW(),NOW(),'" . date('Y-m-d', $extinctionDate) . "') ";
        $this->db->query($sql);

        $addTypeIx = $this->db->insert_id();

        $newTotalMileage = $this->_log($userCode, 'add', $addTypeIx, $mileage, '1', $message, $orderId, $orderDetailIx, $productId, $ptprice, $payprice);

        $sql = "UPDATE common_user SET mileage = '" . $newTotalMileage . "' WHERE code = '" . $userCode . "'";
        $this->db->query($sql);

        //취소에 의한 적립 상태로 들어올 경우에는 사용된 마일리지를 추가 했기때문에 기존에 차감 마일리지에도 값을 추가 해줘야 함
        if ($type == '4') {
            $sql = "SELECT um_ix FROM shop_use_mileage WHERE oid = '" . $orderId . "'"; //고객이 주문시 사용한 마일리지 정보를 가져온다
            $this->db->query($sql);
            $this->db->fetch();
            $umIx = $this->db->dt['um_ix'];

            // 차감 테이블에 고객이 사용한 마일리지에 대한 Key 값존재 여부 확인 및 차감된 마일리지 합계 확인
            $sql = "select * from shop_remove_mileage where um_ix = '" . $umIx . "' and rm_state = '1' group by um_ix";
            $this->db->query($sql);
            if ($this->db->total) {
                $rmData = $this->db->fetchall();
                foreach ($rmData as $data) {
                    $this->_remove($data['am_ix'], $data['um_ix'], $data['rm_mileage'], '주문취소에 따른 차감데이터 복구', $userCode, 2);
                }
            }
        }
    }

    /**
     * use
     * @param $userCode
     * @param $mileage
     * @param $type
     * @param $message
     * @param string $orderId
     */
    protected function _use($userCode, $mileage, $type, $message, $orderId = '')
    {

        if (empty($userCode) || !($mileage > 0)) {
            return;
        }

        if (!($this->rule['mileage_info_use'] == "Y" || $this->rule['mileage_info_use'] == "P")) {
            return;
        }

        $sql = "INSERT INTO shop_use_mileage 
				(uid,use_type,oid,um_mileage,um_state,message,date,regdate) 
				VALUES ('" . $userCode . "','" . $type . "','" . $orderId . "','" . $mileage . "','1','" . $message . "',NOW(),NOW()) ";
        $this->db->query($sql);

        $useTypeIx = $this->db->insert_id();

        $newTotalMileage = $this->_log($userCode, 'add', $useTypeIx, $mileage, '1', $message, $orderId);

        $sql = "UPDATE common_user SET mileage = '" . $newTotalMileage . "' WHERE code = '" . $userCode . "'";
        $this->db->query($sql);

        $rmMessage = "마일리지 사용에 따른 순차적 차감";

        //적립 완료된 마일리지 중 차감 대상이 되는 마일리지 정보 가져오기
        $sql = "SELECT 
                IFNULL(SUM(CASE WHEN rm_state = '2' THEN -rm_mileage ELSE rm_mileage END),0) AS remove_mileage,
                am_ix
            FROM 
                shop_remove_mileage WHERE uid = '" . $userCode . "' GROUP BY am_ix ORDER BY am_ix ASC";
        $this->db->query($sql);
        if ($this->db->total) {
            $removeData = $this->db->fetchall();
            $balanceMileage = 0;
            for ($i = 0; $i < count($removeData); $i++) {
                $sql = "SELECT am_mileage FROM shop_add_mileage WHERE am_ix = '" . $removeData[$i]['am_ix'] . "' AND uid = '" . $userCode . "' ";
                $this->db->query($sql);
                $this->db->fetch();

                $amMileage = $this->db->dt['am_mileage'];

                if ($amMileage > ($removeData[$i]['remove_mileage'] + $mileage)) {
                    $rmMileage = $mileage;
                    $amIx = $removeData[$i]['am_ix'];
                    break;
                } else if ($amMileage == ($removeData[$i]['remove_mileage'] + $mileage)) {
                    $rmMileage = $mileage;
                    $amIx = $removeData[$i]['am_ix'];
                    break;
                } else if ($amMileage < ($removeData[$i]['remove_mileage'] + $mileage)) {
                    $rmMileage = $amMileage - $removeData[$i]['remove_mileage'];
                    $amIx = $removeData[$i]['am_ix'];

                    $balanceMileage = abs($rmMileage - $mileage);
                    break;
                }
            }

            if ($balanceMileage == 0) {
                $this->_remove($amIx, $useTypeIx, $rmMileage, $rmMessage, $userCode, 1);
            }
            if ($balanceMileage > 0) {
                $sql = "SELECT * FROM shop_add_mileage WHERE am_ix > '" . $amIx . "' AND uid = '" . $userCode . "' ORDER BY am_ix ASC";
                $this->db->query($sql);
                $result = $this->db->fetchall();

                foreach ($result as $data) {
                    if ($data['am_mileage'] >= $balanceMileage) {
                        $rmMileage = $balanceMileage;
                        $amIx = $data['am_ix'];
                        $this->_remove($amIx, $useTypeIx, $rmMileage, $rmMessage, $userCode, 1);
                        break;
                    } else {
                        $rmMileage = $data['am_mileage'];
                        $amIx = $data['am_ix'];
                        $this->_remove($amIx, $useTypeIx, $rmMileage, $rmMessage, $userCode, 1);
                        $balanceMileage = $balanceMileage - $data['am_mileage'];
                    }
                }
            }
        } else {
            $balanceMileage = $mileage;
            $sql = "SELECT * FROM shop_add_mileage WHERE uid = '" . $userCode . "' ORDER BY am_ix ASC";
            $this->db->query($sql);
            $result = $this->db->fetchall();

            foreach ($result as $data) {
                if ($data['am_mileage'] >= $balanceMileage) {
                    $rmMileage = $balanceMileage;
                    $amIx = $data['am_ix'];
                    $this->_remove($amIx, $useTypeIx, $rmMileage, $rmMessage, $userCode, 1);
                    break;
                } else {
                    $rmMileage = $data['am_mileage'];
                    $amIx = $data['am_ix'];
                    $this->_remove($amIx, $useTypeIx, $rmMileage, $rmMessage, $userCode, 1);
                    $balanceMileage = $balanceMileage - $data['am_mileage'];
                }
            }
        }
    }

    /**
     * log
     * @param $userCode
     * @param $mileage
     * @param $state
     * @param $message
     * @param string $orderId
     * @param string $orderDetailIx
     * @param string $productId
     * @param string $ptprice
     * @param string $payprice
     * @return int
     */
    protected function _log($userCode, $type, $typeIx, $mileage, $state, $message
        , $orderId = '', $orderDetailIx = '', $productId = '', $ptprice = '', $payprice = '')
    {
        $sql = "SELECT total_mileage FROM shop_mileage_log WHERE uid = '" . $userCode . "' ORDER BY ml_ix DESC LIMIT 1";
        $this->db->query($sql);
        $this->db->fetch();

        $totalMileage = $this->db->dt['total_mileage'];

        if (empty($totalMileage)) {
            $totalMileage = 0;
        }

        $newTotalMileage = $totalMileage + $mileage;

        $sql = "INSERT INTO shop_mileage_log 
                (uid,log_type,type_ix,oid,od_ix,pid,ptprice,payprice,ml_mileage,total_mileage,ml_state,message,date,regdate) 
              VALUES ('" . $userCode . "','" . $type . "','" . $typeIx . "','" . $orderId . "','" . $orderDetailIx . "','" . $productId . "','" . $ptprice . "','" . $payprice . "','" . $mileage . "','" . $newTotalMileage . "','" . $state . "','" . $message . "',NOW(),NOW()) ";
        $this->db->query($sql);

        return $newTotalMileage;
    }

    /**
     * remove
     * @param $amIx
     * @param $useTypeIx
     * @param $mileage
     * @param $message
     * @param $userCode
     * @param $state
     */
    protected function _remove($amIx, $useTypeIx, $mileage, $message, $userCode, $state)
    {
        $sql = "INSERT INTO shop_remove_mileage
				(am_ix,um_ix,rm_mileage,message,uid,rm_state,date,regdate) 
			VALUES 
				('" . $amIx . "','" . $useTypeIx . "','" . $mileage . "','" . $message . "','" . $userCode . "','" . $state . "',NOW(),NOW()) ";
        $this->db->query($sql);
    }
}