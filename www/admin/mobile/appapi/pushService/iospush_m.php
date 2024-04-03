<?php
/**
 * 아이폰 PUSH 모델
 *
 * @author Hong
 * @date 2014.04.30
 * @last upate 2016.03.18
 */
include_once ($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

class iosPush_m{
	var $db;

	public function __construct(){
		$this->db = new Database();
	}

	/**
	 * 전체 회원 단말기 갯수 가져오는것
	 *
	 */
	public function getRegistIdTotal($app_div)
	{
		$sql = "SELECT count(*) as total
				FROM mobile_push_service
				WHERE
					os = 'i'
				AND app_div='".$app_div."'
				AND is_allowable = '1'";

		$this->db->query($sql);

		$result = $this->db->fetch();
		return $result['total'];
	}

	/**
	 * 선택한 회원 단말기 갯수 가져오는것.
	 *
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
					os = 'i'
				AND app_div='".$app_div."'
				AND device_id in ('".$string_code."')
				AND is_allowable = '1'
				";

		$this->db->query($sql);

		$result = $this->db->fetch();
		return $result['total'];
	}

	/**
	 * 검색한 회원 단말기 갯수 가져오는것.
	 *
	 */
	public function getRegistIdTotalUsingSearch($app_div,$query_string)
	{

		//query_string을 파싱해준다.
		//parse_str() 이용하면 변수로 만들어준다.
		parse_str($query_string);
		$is_api = "T";

		include($_SERVER['DOCUMENT_ROOT']."/admin/mShop/push_request_member_query.php");

		$result['total'] = $total;

		return $result['total'];
	}




	/**
	 * 발송대상 키 목록###
	 *
	 * @return {array} key_value list
	 */
	public function getRegistIdList($app_div)
	{

		$return_array = null;
		$sql = "SELECT key_value FROM mobile_push_service mps $branch_join
				WHERE
					os = 'i'
				AND app_div='".$app_div."'
				AND is_allowable = '1' ";

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
	 * 회원별 발송대상 키 목록###
	 *
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
					os = 'i'
				AND app_div='".$app_div."'
				AND user_code in ('".$string_code."')
				AND is_allowable = 1 ";


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
	 *
	 * @return {array} key_value list
	 */
	public function getSearchRegistIdList($app_div,$query_string)
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
	 * 회원별 발송대상 키 목록
	 *
	 * @return {array} key_value list
	 *
	public function getMemberRegistIdList($app_div,$code){
		$return_array = null;
		$sql = "SELECT key_value FROM mobile_push_service WHERE os = 'i' and app_div='".$app_div."' and user_code in ('".implode("','",$code)."') ";

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
	*/

	/**
	 * 등록키 입력
	 *
	 * @param {string} key value
	 */
	public function insertRegistId($data){


		//Device ID가 기준이 된다.
		$sql = "select * from mobile_push_service where app_div='".$data["app_div"]."' and device_id='".$data["device_id"]."'";
		$this->db->query($sql);

		//자료가 없으면
		if(!$this->db->total){
			$sql = "INSERT INTO mobile_push_service (sequence,os,app_div,key_value,device_id,user_code,is_allowable,regdate)
			values('','i','".$data["app_div"]."','".$data["receive_key"]."','".$data["device_id"]."','".$data["user_code"]."','".$data["is_allowable"]."',NOW())";
			$this->sequences = "MOBILE_PUSH_SERVICE_SEQ";
			$this->db->query($sql);
		}else{
			//2014-04-28 Hong 추가
			$this->db->fetch();

            if(!is_null($data["is_allowable"])){
                $sql_is_allowable = " , is_allowable='".$data["is_allowable"]."'";
            }

			//유저코드가 같지 않으면 업데이트
			if($this->db->dt["user_code"] != $data["user_code"]){
				$sql = "UPDATE mobile_push_service SET user_code='".$data["user_code"]."' ".$sql_is_allowable." WHERE device_id='".$data["device_id"]."'";
				$this->db->query($sql);
			}

			//레지스트레이션 아이디가 같지 않으면 업데이트
			$sql = "UPDATE mobile_push_service SET os='i', key_value='".$data["receive_key"]."' ".$sql_is_allowable." WHERE device_id='".$data["device_id"]."'";
				$this->db->query($sql);
		}

		return '';
	}

	public function deleteRegistId($data){
		$sql = "DELETE FROM mobile_push_service where app_div='".$data["app_div"]."' and device_id='".$data["device_id"]."' ";

		$sql = "update mobile_push_service set
				is_allowable = '0'
				WHERE
					app_div='".$data["app_div"]."'
				AND key_value='".$data["receive_key"]."' ";

		$this->db->query($sql);
		return '';
	}

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

	public function selectRegistId($data){

		$sql = "select * from mobile_push_service where app_div='".$data["app_div"]."' and key_value='".$data["receive_key"]."' and is_allowable = '1'";
		$this->db->query($sql);

		if($this->db->total){
			$return="N";
		}else{
			$return="Y";
		}

		return $return;

	}

	//2014-04-28 Hong 추가
	public function updateRegistUserCode($data){
		$sql = "UPDATE mobile_push_service SET user_code='".$data["user_code"]."' WHERE app_div='".$data["app_div"]."' and key_value='".$data["receive_key"]."' ";
		$this->db->query($sql);
		return '';
	}

	/**
	 *	푸시 예약
	 */
	public function insertPushReserve($data)
	{
		$sql = "INSERT INTO mobile_push_reserve
				SET
					b_send = '0',
					reserve_type = 'P',
					reserve_push_os = 'i',
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
}
