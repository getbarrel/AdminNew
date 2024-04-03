<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-12
 * Time: 오후 3:57
 */

require_once constant("CORE_ROOT") . "/model/mall/app/forbizApp.class.php";

class forbizPush extends forbizApp
{
    /**
     * deviceId 로 회원맵핑
     * @param $code
     * @param $deviceId
     */
    public function updateUserPushServiceByDeviceId($code, $deviceId)
    {
        $sql = "update mobile_push_service SET user_code = '" . $code . "' WHERE device_id = '" . $deviceId . "'";
        $this->db->query($sql);
    }
}
