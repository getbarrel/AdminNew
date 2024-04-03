<?php
set_time_limit(99999);
ini_set('memory_limit', '-1');
/**
 * 아이폰 PUSH 서비스(GCM) 컨트롤러
 *
 * @author Hong
 * @date 2014.04.30
 * @last update 2016.03.18
 */
include($_SERVER["DOCUMENT_ROOT"] . "/admin/mobile/appapi/pushService/iospush_m.php");

class iosPush
{
    var $model;
    var $apiKey;
    var $senderId;

    public function __construct($senderId = "", $apiKey = "")
    {

        $this->model = new iosPush_m();

        $this->senderId = $senderId;
        $this->apiKey = $apiKey;

    }

    /**
     *    push 메시지 발송
     */
    public function requestPush($data)
    {

        if ($data[update_type] == "1") {
            //검색한 회원 보낼 디바이스 총 합
            $total = $this->model->getRegistIdTotalUsingSearch($data["app_div"], $data[query_string]);
        } elseif ($data[update_type] == "2") {
            //선택한 회원 보낼 디바이스 총 합
            $total = $this->model->getRegistIdTotalUsingCode($data["app_div"], $data[str_code_list]);
        } else {
            //가장 기본 전체 보낼 디바이스 총 합
            $total = $this->model->getRegistIdTotal($data["app_div"]);
        }

        $start = 0;
        $max = 500;
        $round = (int)($total / $max) + 1;

        //선택한 회원은 최대 100명이 되지 않기 때문에
        //round = 1 로 설정 후  한번만 돌린다.
        if ($data[update_type] == "2") {
            $round = 1;
        }

        $success_cnt = 0;
        $fail_cnt = 0;

        if(empty($data['type']))    $data['type'] = 'i';

        //로그를 남긴다.
        $sequence = $this->model->insertSendLog($data);


        for ($i = 0; $i < $round; $i++) {
            $start = ($i * $max) + 1;
            if ($i == 0)
                $start = 0;
            $end = ($i + 1) * $max;


            if ($data[update_type] == "1") {
                //검색한 회원 보내기
                $registrationIDs = $this->model->getSearchRegistIdList($data["app_div"], $data[query_string], $start, $max);
            } elseif ($data[update_type] == "2") {
                //선택한 회원 보내기
                $registrationIDs = $this->model->getMemberRegistIdList($data["app_div"], $data[str_code_list]);
            } else {
                //가장 기본 전체 보내기
                $registrationIDs = $this->model->getRegistIdList($data["app_div"], $start, $max);
            }

            // Message to be sent
            if (!empty($data["msg"])) {
                $message = $data["msg"];
            } else {
                echo "message 값은 필수 입력입니다.";
                return false;
            }

            $desc = "";
            if(!empty($data['description'])) {
                $desc = $data["description"];
            }

            // Set POST variables
            $url = 'https://fcm.googleapis.com/fcm/send';

            $datafields = array();
            $datafields['sequence'] = $sequence;
            $datafields['description'] = $desc;
            $datafields['title'] = $data['push_title'];
            $datafields['text'] = $message;
            $datafields['sound'] = 'default';
            $datafields['image'] = 'Notification Image';

            if($data['contents_type'] == 'noti_img') {
                $datafields['imageURL'] = $data['contents'];
                $imagefield = true;
            }else {
                $imagefield = false;
            }

            if(!empty($data['link'])) {
                $datafields['openURL'] = $data['link'];
            }

            $fields = array(
                'registration_ids'  => $registrationIDs,
                'priority'          => 'high',
                'mutable_content'   => $imagefield,
                'notification'      => $datafields,
                'data'              => $datafields
            );

            $headers = array(
                'Authorization: key=' . $this->senderId,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, $url );
            curl_setopt($ch,CURLOPT_POST, true );
            curl_setopt($ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);

            $data['contents'] = $message;

            $result = json_decode($result);

            $success_cnt += (int)$result->success;
            $fail_cnt += (int)$result->failure;

            curl_close($ch);
        }

        $data['result'] = 'success : ' . $success_cnt . ' / fail : ' . $fail_cnt;

        $this->model->updateSendLog($sequence, $data);

        if ($success_cnt > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *    2014-04-28 Hong 회원별 push 메시지 발송
     *  -> 위 requestPush() 메소드 안에서 다 처리한다.
     */
    public function requestPushMember($data)
    {

        //선택한 회원 보내기
        $registrationIDs = $this->getMemberRegistIdList($data["app_div"],$data["code"]);

        // Message to be sent
        if (!empty($data["contents"])) {
            $message = $data["contents"];
        } else {
            echo "message 값은 필수 입력입니다.";
            return false;
        }

        $desc = "";
        if(!empty($data['description'])) {
            $desc = $data["description"];
        }

        //로그를 남긴다.
        $sequence = $this->model->insertSendLog($data);

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $datafields = array();
        $datafields['sequence'] = $sequence;
        $datafields['description'] = $desc;
        $datafields['title'] = $data['push_title'];
        $datafields['text'] = $message;
        $datafields['sound'] = 'default';
        $datafields['image'] = 'Notification Image';

        if($data['contents_type'] == 'noti_img') {
            $datafields['imageURL'] = $data['contents'];
            $imagefield = true;
        }else {
            $imagefield = false;
        }

        if(!empty($data['link'])) {
            $datafields['openURL'] = $data['link'];
        }

        $fields = array(
            'registration_ids'  => $registrationIDs,
            'priority'          => 'high',
            'mutable_content'   => $imagefield,
            'notification'      => $datafields,
            'data'              => $datafields
        );

        $headers = array(
            'Authorization: key=' . $this->senderId,
            'Content-Type: application/json'
        );

        // Open connection
        if(count($registrationIDs) > 0) {


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Execute post
            $result = curl_exec($ch);

            $data['contents'] = $message;

            $result = json_decode($result);

            $success_cnt = (int)$result->success;
            $fail_cnt    = (int)$result->failure;

            curl_close($ch);
        }else{
            $result = "99";
        }

        $data['result'] = 'success : ' . $success_cnt . ' / fail : '.$fail_cnt;

        $this->model->updateSendLog($sequence,$data);

        return $result;
    }


    /**
     * 푸시를 예약하기 위해 DB에 저장한다. 20151207
     * @author pyw
     * @param array $data
     *
     * @access public
     * @return boolean
     */
    public function requestPushReserve($data)
    {
        $data[send_push_reserve] = date("Y-m-d H:i:s", strtotime($data[send_time_sms] . " " . $data[send_time_hour] . ":" . $data[send_time_minite] . ":00"));
        $result = $this->model->insertPushReserve($data);
        return $result;
    }


    /**
     *    registId 등록
     */
    public function setRegistId($data)
    {
        $result = $this->model->insertRegistId($data);
        return $result;
    }

    public function deleteRegistId($data)
    {
        $result = $this->model->deleteRegistId($data);
        return $result;
    }

    /**
     *    등록 되어 있는지 여부
     */
    public function getSelectId($data)
    {
        $result = $this->model->selectRegistId($data);
        return $result;
    }

    //2014-04-28 Hong 추가 등록된 registId 회원코드 업데이트
    public function updateRegistUserCode($app_div)
    {
        $result = null;
        $result = $this->model->updateRegistUserCode($app_div);

        return $result;
    }

    //2014-04-28 Hong 추가 등록된 registId 회원코드 업데이트
    public function getMemberRegistIdList($app_div, $code)
    {
        $result = null;
        $result = $this->model->getMemberRegistIdList($app_div, $code);

        return $result;
    }

    /**
     * registIdList 가져오기
     */
    private function getRegistIdList($app_div)
    {
        $result = null;
        $result = $this->model->getRegistIdList($app_div);

        return $result;
    }


}
