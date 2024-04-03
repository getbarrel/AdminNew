<?php
/**
 * 안드로이드 PUSH 모델
 *
 * @author bgh
 * @date 2013.07.19
 * @update date 2015.11.11 pyw
 */
include_once ($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

class androidPush_m{
	var $db;

	public function __construct()
	{
		$this->db = new Database();
	}

	/**
	 * 단말기 총 수
	 */
	public function getRegistIdTotal($app_div)
	{
		$sql = "SELECT count(*) as total
				FROM mobile_push_service
				WHERE
					os = 'a'
				AND app_div='".$app_div."' 
				AND is_allowable = '1'";

		$this->db->query($sql);

		$result = $this->db->fetch();
		return $result['total'];
	}


	/**
	 * 선택한 회원 단말기 갯수 가져오는것.
	 */
	public function getRegistIdTotalUsingCode($app_div,$code)
	{

		//explode 했다 implode 하는 이유는 IN QUERY를 쓰기 위해서
		//기본 $code는 ex,ex,ex, 상태로 되어있음 이 상태를
		// ex','ex','ex',' 상태로

		$array_code =  explode(",",$code);
		$string_code = implode("','",$array_code);


		$sql = "SELECT count(*) as total
				FROM mobile_push_service
				WHERE
					os = 'a'
				AND app_div='".$app_div."'
				AND user_code in ('".$string_code."')
				AND is_allowable = '1';
				";

		$this->db->query($sql);

		$result = $this->db->fetch();
		return $result['total'];
	}

	/**
	 * 검색한 회원 단말기 갯수 가져오는것.
	 */
	public function getRegistIdTotalUsingSearch($app_div,$query_string)
	{

		//query_string을 파싱해준다.
		//parse_str() 이용하면 변수로 만들어준다.
		parse_str($query_string);
		$is_api = "T";

		include($_SERVER['DOCUMENT_ROOT']."/admin/mShop/push_request_member_query.php");

		return $total;
	}


	/**
	 * 발송대상 키 목록
	 * @return {array} key_value list
	 */
	public function getRegistIdList($app_div, $start, $max)
	{
		$return_array = null;
		$sql = "SELECT key_value
				FROM mobile_push_service
				WHERE
					os = 'a'
				AND app_div='".$app_div."' 
				AND is_allowable = '1' 
				LIMIT ".$start.", ".$max;


		$this->db->query($sql);
		if($this->db->total){
			$result = $this->db->fetchAll();
			$return_array = array();
			foreach($result as $rt):
				array_push($return_array, $rt["key_value"]);
			endforeach;
		}

		return $return_array;
	}

	/**
	 * 회원별 발송대상 키 목록
	 * @return {array} key_value list
	 */
	public function getMemberRegistIdList($app_div,$code)
	{

		//explode 했다 implode 하는 이유는 IN QUERY를 쓰기 위해서
		//기본 $code는 ex,ex,ex, 상태로 되어있음 이 상태를
		// ex','ex','ex',' 상태로
		$array_code =  explode(",",$code);
		$string_code = implode("','",$array_code);

		$return_array = null;
		$sql = "SELECT key_value
				FROM mobile_push_service
				WHERE
					os = 'a'
				AND app_div='".$app_div."'
				AND device_id in ('".$string_code."')
				AND is_allowable = '1'";

		$this->db->query($sql);
		if($this->db->total){
			$result = $this->db->fetchAll();
			$return_array = array();
			foreach($result as $rt):
				array_push($return_array, $rt["key_value"]);
			endforeach;
		}
		return $return_array;
	}

	/**
	 * 검색한 회원 발송대상 키 목록
	 * @return {array} key_value list
	 */
	public function getSearchRegistIdList($app_div,$query_string, $push_start, $push_max)
	{

		//query_string을 파싱해준다.
		//parse_str() 이용하면 변수로 만들어준다.
		parse_str($query_string);
		$is_api = "T";

		include($_SERVER['DOCUMENT_ROOT']."/admin/mShop/push_request_member_query.php");

		$this->db->query($sql);

		if($this->db->total){
			$result = $this->db->fetchAll();
			$return_array = array();
			foreach($result as $rt):
				array_push($return_array, $rt["key_value"]);
			endforeach;
		}
		return $return_array;
	}



	/**
	 * 등록키 입력
	 * @param {array} $data key value
	 *
	 * @access public
	 * @return array
	 */
	public function insertRegistId($data)
	{

		$result = array('result'=>false,'code'=>300,'msg'=>'input validation fail');

		//Device Id가 기준이 된다.
		$sql = "SELECT *
				FROM mobile_push_service
				WHERE
					app_div='".$data['app_div']."'
				AND device_id='".$data['device_id']."'";
		$this->db->query($sql);

		// Device_Id 가 존재하지 않으면 insert
		if(! $this->db->total){
			$sql = "INSERT INTO mobile_push_service (
							sequence,
							os,
							app_div,
							key_value,
							device_id,
							user_code,
							is_allowable,
							regdate
							) values (
							'',
							'a',
							'".$data['app_div']."',
							'".$data['receive_key']."',
							'".$data['device_id']."',
							'".$data['user_code']."',
							'".$data["is_allowable"]."',
							NOW()
							)";

			if($this->db->query($sql)){
				$result = array('result'=>true,'code'=>200,'msg'=>'insert complete');
			}
		}else{
			//Device_Id가 존재하면 정보 업데이트
			$this->db->fetch();

            if(!is_null($data["is_allowable"])){
                $sql_is_allowable = " , is_allowable='".$data["is_allowable"]."'";
            }

			//유저코드가 다르면 수정해준다.  $data['user_code'] 데이터가 있으면
			if($this->db->dt[user_code] != $data['user_code'] && $data['user_code'] != ''){
				$set_string = ", user_code = '".$data['user_code']."'";
			}

			//RegistrationId는 늘 업데이트 해준다.
			$sql = "UPDATE mobile_push_service SET
						key_value = '".$data['receive_key']."'
						$set_string 
						$sql_is_allowable 
					WHERE device_id = '".$data['device_id']."'";

			if($this->db->query($sql)){
				$result = array('result'=>true,'code'=>200,'msg'=>'update complete');
			}
		}

		return $result;
	}

	/**
	 * 등록키 삭제
	 * @param {array} $data key value
	 *
	 * @access public
	 * @return array
	 */
	public function deleteRegistId($data){
		$result = array('result'=>false,'code'=>300,'msg'=>'input validation fail');
		$sql = "DELETE
				FROM mobile_push_service
				WHERE
					app_div='".$data["app_div"]."'
				AND key_value='".$data["receive_key"]."' ";
		if($this->db->query($sql)){
			$result = array('result'=>true,'code'=>200,'msg'=>'delete complete');
		}

		return $result;
	}

	/**
	 * 발송내역 저장
	 * @param array $data
	 *
	 * @access public
	 */
	public function insertSendLog($input){
		$sql = "INSERT INTO mobile_push_log
				SET
					title = '".$input['title']."',
					push_title = '".$input['push_title']."',
					contents = '".$input['contents']."',
					contents_type = '".$input['type']."',
					link = '".$input['link']."',
					app_div = '".$input['app_div']."',
					serialize_data = '".urlencode(serialize($input))."',
					regdate = NOW()
				";
		//
		$this->db->query($sql);
		return $this->db->insert_id();
	}

	public function updateSendLog($sequence,$input){
		$sql = "UPDATE mobile_push_log
				SET
					result = '".$input['result']."'
				WHERE
					sequence = '".$sequence."'
				";
		//result = '".$input['result']."',
		$this->db->query($sql);
	}

	/**
	 * 푸시 예약을 위해서.
	 *
	 */
	public function insertPushReserve($data)
	{
		$sql = "INSERT INTO mobile_push_reserve
				SET
					b_send = '0',
					reserve_type = 'P',
					reserve_push_os = 'a',
					reserve_time = '".$data[send_push_reserve]."',
					reserve_data = '".urlencode(serialize($data))."',
					regdate = NOW()
				";

		if ($this->db->query($sql)) {
			$result = array('result'=>true,'code'=>200,'msg'=>'Success Insert Push Reserve');
		}
		else {
			$result = array('result'=>false,'code'=>401,'msg'=>'Fail Insert Push Reserve');
		}

		return $result;

	}

    //2014-04-28 Hong 추가
    public function updateRegistUserCode($data){
        $sql = "UPDATE mobile_push_service SET user_code='".$data["user_code"]."' WHERE app_div='".$data["app_div"]."' and key_value='".$data["receive_key"]."' ";
        $this->db->query($sql);
        return '';
    }

}
