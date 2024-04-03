<?php 
/**
 * 몰스토리 뱅크다 테이블 액세스 모델
 * //TODO : REST방식으로 몰스토리 서버에 접근하도록 변경할 것.
 * 
 * @author bgh
 * @date 2013.09.04
 */
require_once $_SERVER["DOCUMENT_ROOT"].'/class/database.class';

class mallstory_m{
	private $db;

	public function __construct(){
		$this->db = new Database();
		$this->db->setDbHost(MALLSTORY_DB_HOST);
		$this->db->setDbName(MALLSTORY_DB_NAME);
		$this->db->setDbUser(MALLSTORY_DB_USER,MALLSTORY_DB_PW);
	}
	
	/**
	 * DB 접속 재설정
	 * mysql클래스의 인스턴스가 분리되지 않는것으로 보여서 다시 생성해서 사용합니다.
	 */
	private function setConnection(){
		$this->db = new Database();
		$this->db->setDbHost(MALLSTORY_DB_HOST);
		$this->db->setDbName(MALLSTORY_DB_NAME);
		$this->db->setDbUser(MALLSTORY_DB_USER,MALLSTORY_DB_PW);
	}
	/**
	 * 이용자 정보
	 * @param String $mallIx
	 * @return array
	 */
	public function userInfo($mallIx){
		$this->setConnection();
		
		$result = null;
		$sql = "SELECT
					*
				FROM
					bankda_member
				WHERE
					mall_ix = '".$mallIx."'
				";
		$this->db->query($sql);
		if($this->db->total){
			$result = $this->db->fetch();
		}
		return $result;
	}
	
	/**
	 * 사용중인 계좌 목록
	 * @param String shopId
	 * @return Array List
	 */
	public function accountList($shopId,$acc_seq=''){
		$this->setConnection();
		
		$result = null;
		$sql = "SELECT
					bmb.seq,
					Bkacctno,
					Bkname,
					bank_code
				FROM
					bankda_member AS bm
				LEFT JOIN
					bankda_member_bank AS bmb
				ON
					bm.seq = bmb.member_seq
				WHERE
					bankda_userid = '".$shopId."'
					AND bUse = 'Y'";
		if(!empty($acc_seq)){
			$sql .= "AND bmb.seq = '".$acc_seq."' ";
			$this->db->query($sql);
			if($this->db->total){
				$result = $this->db->fetch();
			}
		}else{
			$sql .= "ORDER BY bmb.seq DESC";
			$this->db->query($sql);
			if($this->db->total){
				$result = $this->db->fetchall();
			}
		}
		return $result;
	}
	
	/**
	 * 개별 거래내역 + 요약 정보 가져오기
	 * @param String	$shopId		몰아이디	
	 * @param array		$search		검색옵션
	 * @return array	$result["list"], $result["summary"]
	 */
	 public function transactionListByOne($shopId,$Bkid = ''){
		$this->setConnection();
		
		$result = null;
		
		/* 사용중인 계좌 목록 */
		$accountList = $this->accountList($shopId);
		if(empty($accountList)){
			return null;
		}else{
			$account_sql = " AND Bkacctno IN (";
			$count = 0;
			foreach($accountList as $acl):
				if($count == 0){
					$account_sql .= "'".$acl["Bkacctno"]."'";
				}else{
					$account_sql .= ",'".$acl["Bkacctno"]."'";
				}
				$count++;
			endforeach;
			$account_sql .= ")";
		}
		$sql = "SELECT * FROM TBLBANK WHERE Mid = '".$shopId."' and Bkid = '".$Bkid."'";
		$this->db->query($sql);
		if($this->db->total){
			$result = $this->db->fetchall();
		}
		
		return $result;
	 }
	 
	/**
	 * 거래내역 + 요약 정보 가져오기
	 * @param String	$shopId		몰아이디	
	 * @param array		$search		검색옵션
	 * @return array	$result["list"], $result["summary"]
	 */
	public function transactionList($shopId,$search = ''){
		$this->setConnection();
		
		$result = null;
		
		/* 사용중인 계좌 목록 */
		$accountList = $this->accountList($shopId);
		if(empty($accountList)){
			return null;
		}else{
			$account_sql = " AND Bkacctno IN (";
			$count = 0;
			foreach($accountList as $acl):
				if($count == 0){
					$account_sql .= "'".$acl["Bkacctno"]."'";
				}else{
					$account_sql .= ",'".$acl["Bkacctno"]."'";
				}
				$count++;
			endforeach;
			$account_sql .= ")";
		}
		$sql = "SELECT * FROM TBLBANK WHERE Mid = '".$shopId."' ";
		
		/* 검색 옵션 */
		if(!empty($search)){
			
			/* 날짜 검색 */
			if(!empty($search["sdate"]) && !empty($search["edate"])){
				$search_sql = " AND Bkxferdatetime BETWEEN '".$search["sdate"]."' AND '".$search["edate"]."235959'";
			}
			
			/* 입금일 */
			if(!empty($search["input_sdate"]) && !empty($search["input_edate"])){
				
				$input_sdate = str_replace('-','',$search["input_sdate"]);
				$input_edate = str_replace('-','',$search["input_edate"]);
				
				$search_sql = " AND Bkdate BETWEEN '".$input_sdate."' AND '".$input_edate."'";
			}
			
			/* 매칭일 */
			if(!empty($search["matching_sdate"]) && !empty($search["matching_edate"])){
				$search_sql = " AND matching_date BETWEEN '".$search["matching_sdate"]." 00:00:00' AND '".$search["matching_edate"]." 23:59:59'";
			}
			
			/* 구분(전체,입금,출금 */
			if(!empty($search["div"])){
				switch($search["div"]){
					case "input":
						$search_sql .= " AND Bkoutput = 0 ";
						break;
					case "output":
						$search_sql .= " AND Bkinput = 0 ";
						break;
					default:
						break;
				}
			}
			/* 메모 */
			if(!empty($search["bank_memo"])){
				$search_sql .= " AND memo LIKE '%".$search["bank_memo"]."%' ";
			}
			/* 계좌 */
			if(!empty($search["Bkacctno"])){
				$search_sql .= " AND Bkacctno = '".$search["Bkacctno"]."' ";
			}
			/* 내역/금액/메모 */
			if(!empty($search["text"])){
				$search_sql .= " AND Bkcontent LIKE '%".$search["text"]."%' 
						  OR Bkinput LIKE '%".$search["text"]."%' 
						  OR Bkjukyo LIKE '%".$search["text"]."%' ";
			}
			/* 입금자명 */
			if(!empty($search["bank_name"])){
				$search_sql .= " AND Bkjukyo LIKE '%".$search["bank_name"]."%' ";
			}
			/* 입금금액 */
			if(!empty($search["bank_price"])){
				$search_sql .= " AND Bkinput = '".$search["bank_price"]."' ";
			}
			/* 주문번호 */
			if(!empty($search["bank_oid"])){
				$search_sql .= " AND oid = '".$search["bank_oid"]."' ";
			}
						
		}
		/* Pagenation */
		$max = 10;
		$page = 1;
		$start = 0;
		if(!empty($search["max"])){
			$max = $search["max"];
		}
		if(!empty($search["page"])){
			$page = $search["page"];
			$start = ($page - 1) * $max;
		}
		$end = $start + $max;
		$page_sql = " LIMIT ".$start.",".$max;
		
		/* order by index DESC */
		$orderBy_sql = " order by Bkid desc " ;
		
		/* Count Item */
		$count_sql = $sql.$account_sql.$search_sql;
		$this->db->query($count_sql);
		$result['total'] = $this->db->total;
		
		/* Excute LIST sql */
		$list_sql = $sql.$account_sql.$search_sql.$orderBy_sql.$page_sql;
		$this->db->query($list_sql);
		//echo $list_sql;
		if($this->db->total){
			$result["list"] = $this->db->fetchall();
			
		}
		
		/**
		 *  SUMMARY 
		 */
		$summary_sql = "SELECT 
							Bkname,
							Bkacctno,
							SUM(Bkinput) AS input,
							(SUM(Bkoutput) * -1) AS output,
							(SUM(Bkinput) + (SUM(Bkoutput) * -1)) AS total
						FROM TBLBANK
						WHERE  Mid = '".$shopId."' ";
		$groupBy_sql = " GROUP BY Bkacctno";
		
		/* Excute SUMMARY sql */ 
		$sql = $summary_sql.$account_sql.$search_sql.$groupBy_sql;
		$this->db->query($sql);
		if($this->db->total){
			$result["summary"] = $this->db->fetchall();
		}
		
		return $result;
	}
	
	/**
	 * 개별 업데이트 처리대상 
	 * 1시간전 부터의 입금 내역
	 * @return array list
	 */
	public function checkListByOne($shopId,$bkid){
		$this->setConnection();
		
		$result = null;
		
		$sql = "SELECT
					*
				FROM
					TBLBANK
				WHERE
						Bkoutput = 0
					AND
						oid IS NULL
					AND
						is_duplicated = 'N'
					AND
						maching_cnt = 0
					AND
						Mid = '".$shopId."'
					AND
						Bkid = '".$bkid."' 
				";
		$this->db->query($sql);
		
		if($this->db->total){
			$result = $this->db->fetchall();
		}
		return $result;
	}

	/**
	 * 개별 업데이트 처리대상 (동명이인일 경우) 170822 추가
	 * 1시간전 부터의 입금 내역
	 * @return array list
	 */
	public function checkListByOneDuplicate($shopId,$bkid){
		$this->setConnection();
		
		$result = null;
		
		$sql = "SELECT
					*
				FROM
					TBLBANK
				WHERE
						Bkoutput = 0
					AND
						oid IS NULL
					AND
						is_duplicated = 'Y'
					AND
						maching_cnt = 0
					AND
						Mid = '".$shopId."'
					AND
						Bkid = '".$bkid."' 
				";
		$this->db->query($sql);
		
		if($this->db->total){
			$result = $this->db->fetchall();
		}
		return $result;
	}
	/**
	 * 업데이트 처리대상 리스트
	 * 1시간전 부터의 입금 내역
	 * @return array list
	 */
	public function checkList($shopId){
		$this->setConnection();
		
		$result = null;
		$startTime = date('YmdHis',strtotime('-1 hours'));
		$startTime = date('YmdHis',strtotime('-10 days'));
		$sql = "SELECT
					*
				FROM
					TBLBANK
				WHERE
						Bkoutput = 0
					AND
						oid IS NULL
					AND
						is_duplicated = 'N'
					AND
						maching_cnt = 0
					AND
						Mid = '".$shopId."'
					AND
						Bkxferdatetime > '".$startTime."' 
				";
		//syslog(1,print_r("SELEC	* FROM TBLBANK WHERE Bkoutput = 0 AND oid IS NULL AND is_duplicated = 'N' AND maching_cnt = 0 AND Mid = '".$shopId."' AND Bkxferdatetime > '".$startTime."'",true));
		$this->db->query($sql);
		if($this->db->total){
			$result = $this->db->fetchall();
		}
		return $result;
	}
	/**
	 * 메모 업데이트 처리
	 */
	public function MemoUpdate($userId,$Bkid,$memo_text){
		$this->setConnection();
		
		$sql = "UPDATE 
					TBLBANK
				SET
					memo = '".$memo_text."'
				WHERE
						Bkid  = '".$Bkid."'
					AND
						Mid = '".$userId."'
				";
		if($this->db->query($sql)){
			return true;
		}else{
			return false;
		}
	}/**
	 * 거래내역 업데이트 처리
	 * @param string 이용자 아이디
	 * @param string 뱅크다 일련번호
	 * @param string 주문번호
	 * @return boolean T/F
	 */
	public function transactionMatch($userId,$Bkcode,$oid){
		$this->setConnection();
		
		$sql = "UPDATE 
					TBLBANK
				SET
					oid = '".$oid."',
					maching_cnt = '1',
					matching_date = NOW(),
					recent_editdate = NOW()
				WHERE
						Bkcode = '".$Bkcode."'
					AND
						Mid = '".$userId."'
				";
		if($this->db->query($sql)){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 동명이인처리
	 * @param array		뱅크다 입금정보
	 */
	public function duplicateCheck($depositInfo){
		$this->setConnection();
		
		$sql = "UPDATE
					TBLBANK
				SET
					is_duplicated = 'Y'
				WHERE
					Bkcode = '".$depositInfo['Bkcode']."'
				";
		if($this->db->query($sql)){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 사용중인 계좌 잔액 정보
	 * @param String $shopId
	 * @return array list
	 */
	public function accountBalance($shopId){
		$this->setConnection();
		
		$accountList = $this->accountList($shopId);
		$result = array();
		if(!empty($accountList)){
			foreach($accountList as $al):		
				$sql = "SELECT 
							Bkname,Bkacctno,Bkjango 
						FROM 
							TBLBANK 
						WHERE 
							Bkacctno = '".$al["Bkacctno"]."' 
						ORDER BY Bkid DESC 
						LIMIT 1";
				$this->db->query($sql);
				if($this->db->total){
					array_push($result,$this->db->fetch());
				}
			endforeach;
		}
		return $result;
	}
	/**
	 * 이용자 추가
	 * @param array 사용자 정보
	 */
	public function addUser($userInfo){
		$this->setConnection();
		
		$sql = "INSERT INTO bankda_member (seq, mall_ix, mall_div, mall_domain, bankda_userid, bankda_username, bankda_userpw, regdate) VALUES";
		$sql.=" ('', '".$userInfo[mall_ix]."', '".$userInfo[mall_div]."', '".$userInfo[mall_domain]."', '".$userInfo[user_id]."', '".$userInfo[user_name]."', '".$userInfo[user_pw]."', now()) ";
		$this->db->query($sql);
		return true;
	}
	/**
	 * 이용자 삭제
	 * @param array 사용자 정보
	 */
	public function dropUser($userInfo){
		$this->setConnection();
		if(!empty($userInfo['user_id'])){
			$sql = "DELETE FROM
						bankda_member
					WHERE
						bankda_userid = '".$userInfo['user_id']."'";
			if($this->db->query($sql)){
				return true;
			}			
		}
		return false;
	}
	
	/**
	 * 계좌추가
	 * @param array 계좌정보
	 */
	public function addAccount($accInfo){
		$this->setConnection();
		$sql = "SELECT * FROM bankda_member
				WHERE bankda_userid = '".$accInfo['user_id']."'";
		$this->db->query($sql);
		if($this->db->total){
			$member_info = $this->db->fetch();
			$sql = "INSERT INTO
						bankda_member_bank
					SET
						member_seq = '".$member_info['seq']."',
						Bkacctno = '".$accInfo['bkacctno']."',
						Bkname = '".$accInfo['bkname']."',
						bank_code = '".$accInfo['bkcode']."',
						bUse = 'Y',
						regdate = NOW()
					";
			if($this->db->query($sql)){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 계좌삭제
	 * @param string	계좌번호 or action구분('drop')
	 * @param array		탈퇴회원 정보
	 */
	public function deleteAccount($bkacctno,$dropInfo=''){
		$this->setConnection();
		if(!empty($bkacctno)){
			switch($bkacctno){
				case 'drop':
					$member_info = getMemberSeq($dropInfo['user_id']);
					if(!empty($member_info['seq'])){
						$sql = "SELECT bkacctno FROM bankda_member_bank WHERE member_seq ='".$member_info['seq']."'";
						$this->db->query($sql);
						if($this->db->total){
							$accList = $this->db->fetchAll();
							foreach($accList as $al):
								$this->deleteTransactionList($al['bkacctno']);
							endforeach;
						}
						$sql = "DELETE FROM
									bankda_member_bank
								WHERE
									member_seq ='".$member_info['seq']."'";
					}
					break;
				default:
					$this->deleteTransactionList($bkacctno);
					$sql = "DELETE FROM
							bankda_member_bank
						WHERE
							bkacctno='".$bkacctno."'";
					break;
			}
			
			if($this->db->query($sql)){
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 거래내역 삭제
	 * 
	 * @param string $accNo
	 */
	private function deleteTransactionList($accNo){
		$this->setConnection();
		$sql = "DELETE FROM TBLBANK WHERE Bkacctno = '".$accNo."'";
		if($this->db->query($sql)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 이용자 seq 가져오기
	 */
	private function getMemberSeq($shopId){
		$this->setConnection();
		$sql = "SELECT * FROM bankda_member
				WHERE
					bankda_userid = '".$shopId."'";
		$this->db->query($sql);
		if($this->db->total){
			return $this->db->fetch();
		}else{
			return null;
		}
	}
	

}