<?php 
/**
 * 뱅크다 모델
 * 
 * @author bgh
 * @date 2013.09.04
 */
require_once $_SERVER["DOCUMENT_ROOT"].'/class/database.class';

class bankda_m{
	private $db;

	public function __construct(){
		$this->db = new Database();
	}
	
	/**
	 * DB 접속 재설정
	 * mysql클래스의 인스턴스가 분리되지 않는것으로 보여서 다시 생성해서 사용합니다.
	 */
	private function setConnection(){
		$this->db = new Database();
	}
	/**
	 * 상점정보 가져오기
	 * @return array	shopinfo
	 */
	public function shopInfo(){
		$this->setConnection();
		$result = null;
		$sql = "SELECT
					mall_data_root,
					mall_type, 
					mall_ix,
					mall_ename 
				FROM
					shop_shopinfo 
				WHERE
					mall_div = 'B'
				";
		$this->db->query($sql);
		if($this->db->total){
			$result = $this->db->fetch();
		}
		
		return $result;
	}
	/**
	 * 무통장결제 주문목록
	 * @param array		사용중인 계좌 목록(쇼핑몰에 등록된)
	 * @param array		검색조건
	 */
	public function orderListAll($accountList, $search){
		$this->setConnection();
		$result = null;
		if(!empty($accountList)){
			$acc_list = " AND so.bank IN (";
			$count = 0;
			foreach($accountList as $al):
				$acc_info = $this->bankAccMatch($al['Bkacctno']);
				if($count > 0){
					$acc_list .= "," ;
				}
				$acc_list .= "'".$acc_info['bank_name']." ".$acc_info['bank_number']." ".$acc_info['bank_owner']."'" ;
				
				$count++;
			endforeach;
		}
		$sql = "SELECT 
					so.oid, so.date, so.bank_input_name, so.bank, so.payment_price, so.uid, so.bname,
					sod.status,
					bm.*		
				FROM 
				shop_order as so 
					LEFT JOIN bankda_match as bm 
						ON so.oid = bm.match_oid
					LEFT JOIN shop_order_detail as sod
						ON so.oid = sod.oid 
				WHERE method = '0' ";
		/* 검색 옵션 */
		if(!empty($search)){
			
			/* 주문일 검색 */
			if(!empty($search["sdate"]) && !empty($search["edate"])){
				$search_sql = " AND so.date BETWEEN '".$search['sdate']."235959' AND '".$search['edate']."235959'";
			}
			/* 매칭일 검색 */
			if(!empty($search["mSdate"]) && !empty($search["mEdate"])){
				$search_sql .= " AND bm.match_date BETWEEN '".$search["mSdate"]."' AND '".$search["mEdate"]."235959'";
			}
			
			/* 매칭결과 */
			if(!empty($search["match_result"])){
				switch($search["match_result"]){
					case 'ready':
						$search_sql .= " AND sod.status = 'IR' ";
						break;
					case 'success':
					case 'duplicate':
						$search_sql .= " AND bm.match_result = '".$search["match_result"]."' ";
						break;
					case 'manualSuccess':
						$search_sql .= " AND sod.status != 'IR' ";
						break;
					case 'fail':
						$search_sql .= " AND sod.status = 'IR' AND so.date < '".date('Ymd',strtotime('-7days'))."'";
						break;
				}
				
			}
			/* 계좌 */
			if(!empty($search["Bkacctno"])){
				$acc_info = $this->bankAccMatch($search["Bkacctno"]);
				$search_sql .= " AND so.bank LIKE '%".$acc_info['bank_number']."%' ";
			}
			/* 내역/금액/메모 */
			if(!empty($search["text"])){
				switch($search["search_div"]){
					case "all":
						$search_sql .= " AND (so.bank_input_name LIKE '%".$search["text"]."%'
										  OR so.payment_price LIKE '%".$search["text"]."%'
										  OR so.oid LIKE '%".$search["text"]."%') ";
						break;
					case "name":
						$search_sql .= " AND (so.bank_input_name LIKE '%".$search["text"]."%' 
											or so.bname  LIKE '%".$search["text"]."%') ";
						break;
					case "rname":
						$search_sql .= " AND so.rname  LIKE '%".$search["text"]."%' ";
						break;
					case "price":
						$search_sql .= " AND so.payment_price LIKE '%".$search["text"]."%' ";
						break;
					case "oid":
						$search_sql .= " AND so.oid LIKE '%".$search["text"]."%' ";
						break;
				}
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
		$orderBy_sql = " order by date desc " ;
		
		/* group by  */
		$groupBy_sql = " group by so.oid DESC ";
		
		/* Count Item */
		$count_sql = $sql.$account_sql.$search_sql;
		$this->db->query($count_sql);
		$result['total'] = $this->db->total;
		
		/* Excute LIST sql */
		$list_sql = $sql.$account_sql.$search_sql.$groupBy_sql.$page_sql;
		$this->db->query($list_sql);
		//echo $list_sql;
		if($this->db->total){
			$result["list"] = $this->db->fetchall();
		}
		return $result;
	}
	/**
	 * 뱅크다에 등록된 계좌번호로 쇼핑몰에 등록된 계좌번호 찾기
	 * @param string	뱅크다 계좌번호
	 * @return array	쇼핑몰 계좌정보
	 */
	public function bankAccMatch($accountNo){
		$this->setConnection();
		$sql = "SELECT * 
				FROM shop_bankinfo 
				WHERE REPLACE(bank_number, '-','') = '".$accountNo."'";
		$this->db->query($sql);
		
		if($this->db->total){
			return $this->db->fetch();
		}else{
			return null;
		}
	}
	
	/**
	수동매칭할 주문 정보 가져오기
	*/
	public function orderMatch($oid){
		$this->setConnection();
		$result = null;
		$result['count'] = 0;
		
		$sql = "SELECT 
					distinct so.oid,
					sod.status,
					'order' as type
				FROM 
					shop_order as so 
				LEFT JOIN
					shop_order_detail as sod
				ON 
					so.oid = sod.oid
				WHERE
					so.method = 0
				AND 
					sod.status = 'IR'
				AND
					so.oid = '".$oid."'
				";
				
		$this->db->query($sql);
		
		if($this->db->total > 0){
			$result['count'] = $this->db->total;
			$result['result'] = $this->db->fetchall();
		}else{
			$sql = "SELECT
						distinct oid,
						state as status,
						'save' as type
					FROM
						shop_saveprice_info
					WHERE
						state = '0'
					AND
						method = '0'
					AND
						use_div = 'SB'
					AND 
						bank_input_name = '".$depositInfo['Bkjukyo']."'
					AND
						saveprice = '".$depositInfo['Bkinput']."'
					AND 
						REPLACE(bank_number,'-','') LIKE '%".$depositInfo['Bkacctno']."%'
				";
			//	echo $sql."<br>";
			$this->db->query($sql);
			
			$result['count'] = $this->db->total;
			$result['result'] = $this->db->fetchall();
		}
		
		return $result;
	}
	
	
	
	/**
	 * 입금내역으로 매칭되는 주문 가져오기
	 * @param array		입금정보
	 * @return array	매칭된 주문정보
	 */
	public function orderCheck($depositInfo){
		$this->setConnection();
		$result = null;
		$result['count'] = 0;
		
		$sql = "SELECT 
					distinct so.oid,
					sod.status,
					so.uid,
					'order' as type
				FROM 
					shop_order as so 
				LEFT JOIN
					shop_order_detail as sod
				ON 
					so.oid = sod.oid
				WHERE
					so.method = 0
				AND 
					sod.status = 'IR'
				AND
					(so.bank_input_name = '".$depositInfo['Bkjukyo']."' or  so.bname = '".$depositInfo['Bkjukyo']."')
				AND
					so.payment_price = '".$depositInfo['Bkinput']."'
				AND 
					REPLACE(so.bank,'-','') LIKE '%".$depositInfo['Bkacctno']."%'
				";
				
	//	echo $sql;		
		$this->db->query($sql);
		
		
		if($this->db->total > 0){
			$result['count'] = $this->db->total;
			$result['result'] = $this->db->fetchall();
			//$result['count'] = $sql;
		}else{
			$sql = "SELECT distinct oid, state as status, uid, 'save' as type, state FROM shop_saveprice_info WHERE state in ('0','1') AND method = '0' AND use_div = 'SB' AND bank_input_name = '".$depositInfo['Bkjukyo']."' AND saveprice = '".$depositInfo['Bkinput']."' AND REPLACE(bank_number,'-','') LIKE '%".$depositInfo['Bkacctno']."%'
					
				";
				//echo $sql."<br>";
				//exit;
			syslog(LOG_INFO,'sql : ' . $sql);
			$this->db->query($sql);
			
			$result['count'] = $this->db->total;
			$result['result'] = $this->db->fetchall();
			//$result['count'] = $sql;
				/*
				$order_sql = str_replace("'IR'","'IC'", $order_sql);
				$this->db->query($order_sql);
				if($this->db->total > 0){
					$result['count'] = 0;
					$result['result'][state] = 1;
					$result['result'] = $this->db->fetchall();
				}else{

				}
				*/
		}
		
		return $result;
	}
	/**
	 * 입금완료처리
	 * @param array		orderInfo
	 * @param array		뱅크다 입금정보
	 * @return boolean	T/F
	 */
	public function orderUpdate($orderInfo,$depositInfo){
		//print_r($orderInfo);
		//exit;
		$this->setConnection();
		
		if($orderInfo['type'] == 'order'){

		    if($_SESSION['admininfo']){
                $sql = "insert into bankda_match_self (oid, regdate) values ('".$orderInfo["oid"]."', NOW())";
                $this->db->query($sql);
            }
			
			$sql = "UPDATE
						shop_order
					SET
						status = 'IC'
					WHERE
						oid = '".$orderInfo["oid"]."'
					AND
						status = 'IR'
						";
		 
			if($this->db->query($sql)){
				$sql = "UPDATE
							shop_order_detail
						SET
							status = 'IC',
							ic_date = NOW()
						WHERE
							oid = '".$orderInfo['oid']."'
						AND
							status = 'IR'
						";
			
				if($this->db->query($sql)){
					$this->statusLog($orderInfo,$depositInfo);
					$this->Sendby_SMS($orderInfo);
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else if ($orderInfo['type'] == 'save'){
			$sql = "update 
						shop_saveprice_info 
					set 
						state = '1' , recent_editdate = NOW() 
					where 
						oid = '".$orderInfo['oid']."' 
					AND
						state = '0'
					";
			if($this->db->query($sql)){
				//$this->statusLog($orderInfo,$depositInfo); 예치금 충전이므로 상태로그 필요 없슴
				$this->Sendby_SMS($orderInfo); 
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * 입금완료 처리 후 SMS 전송
     * @param  array $orderInfo
	 * 
	 */
	public function Sendby_SMS($orderInfo){
		include_once($_SERVER['DOCUMENT_ROOT']."/include/global_util.php");
		
		$this->setConnection();
		if(!empty($orderInfo['uid'])){
			$sql = "select 
						m.id, 
						AES_DECRYPT(UNHEX(cmd.name),'".$this->db->ase_encrypt_key."') as name, 
						AES_DECRYPT(UNHEX(cmd.pcs),'".$this->db->ase_encrypt_key."') as pcs, 
						AES_DECRYPT(UNHEX(cmd.mail),'".$this->db->ase_encrypt_key."') as mail
					from 
						".TBL_COMMON_USER." m 
					LEFT JOIN 
						".TBL_COMMON_MEMBER_DETAIL." cmd ON m.code=cmd.code 
					WHERE m.code = '".$orderInfo['uid']."'";

			$this->db->query($sql);
			$member = $this->db->fetch();
			
			$mail_info[mem_name] = $member['name'];
			$mail_info[mem_mail] = $member['mail'];
			$mail_info[mem_id] = $member['id'];
			$mail_info[mem_mobile] = str_replace('-','',$member['pcs']);
			
			if($orderInfo['type'] == 'order'){
				sendMessageByStep('order_sucess_price', $mail_info);
			}else if($orderInfo['type'] == 'save'){
				sendMessageByStep('send_save_price', $mail_info);
			}else{
				return false;
			}
		}
	}
	 
	
	/**
	 * 매칭정보 테이블에 입력
	 * @param 2차 array 	주문정보 2차원 배열
	 * @param array 	입금정보
	 * @return boolean	T/F
	 */
	public function insertMatch($orderInfo, $depositInfo,$result){
		$this->setConnection();
		if(!empty($orderInfo)){
			if(count($orderInfo) == 1){
				if(!empty($orderInfo[0]['oid'])){
					$sql = "INSERT INTO
									bankda_match
								SET
									match_oid = '".$orderInfo[0]['oid']."',
									match_result = '".$result."',
									match_bkid = '".$depositInfo['Bkid']."',
									match_type = '".$orderInfo[0]['type']."',
									match_date = NOW()
								";
					if(!$this->db->query($sql)){
						return false;
					}
				}
			}else{
				foreach($orderInfo as $oi):
					if(!empty($oi['oid'])){
						$sql = "INSERT INTO
									bankda_match
								SET
									match_oid = '".$oi['oid']."',
									match_result = '".$result."',
									match_bkid = '".$depositInfo['Bkid']."',
									match_type = '".$oi['type']."',
									match_date = NOW()
								";
						if(!$this->db->query($sql)){
							return false;
						}
					}
				endforeach;
			}
		}
		return true;
	}

	//170822 pde 동명이인 주문 매칭된(duplicate) 이후 수동 매칭할때 success로 업데이트하고 동명이인 매칭된 다른 데이터는 삭제.
	//업데이트 전 관련 bankda_match 데이터는 bankda_match_log에 전부 insert함.
	public function insertMatchDuplicate($orderInfo, $depositInfo,$result){
		$this->setConnection();
		if(!empty($orderInfo)){
			if(count($orderInfo) == 1){
				if(!empty($orderInfo[0]['oid'])){

					$sql = "select * from bankda_match where match_oid = '".$orderInfo[0]['oid']."'";
					$this->db->query($sql);

					if($this->db->total > 0){
						$this->db->fetch();

						if($this->db->dt[match_result] == 'duplicate'){
							$sql = "insert into bankda_match_log select * from bankda_match m where m.match_bkid = '".$depositInfo['Bkid']."'";
							$this->db->query($sql);

							$sql = "DELETE from bankda_match where match_bkid = '".$depositInfo['Bkid']."' and match_oid != '".$orderInfo[0]['oid']."' and match_result = 'duplicate'";
							$this->db->query($sql);

							$sql = "UPDATE
											bankda_match
										SET
											match_result = '".$result."',
											match_bkid = '".$depositInfo['Bkid']."',
											match_type = '".$orderInfo[0]['type']."',
											match_date = NOW()
										WHERE 
											match_oid = '".$orderInfo[0]['oid']."' and match_bkid = '".$depositInfo['Bkid']."'
										";
						}
					}else{
						$sql = "INSERT INTO
										bankda_match
									SET
										match_oid = '".$orderInfo[0]['oid']."',
										match_result = '".$result."',
										match_bkid = '".$depositInfo['Bkid']."',
										match_type = '".$orderInfo[0]['type']."',
										match_date = NOW()
									";
					}

					if(!$this->db->query($sql)){
						return false;
					}
				}
			}else{
				foreach($orderInfo as $oi):
					if(!empty($oi['oid'])){

						$sql = "select * from bankda_match where match_oid = '".$oi['oid']."'";
						$this->db->query($sql);

						if($this->db->total > 0){
							$this->db->fetch();

							if($this->db->dt[match_result] == 'duplicate'){
								$sql = "insert into bankda_match_log select * from bankda_match m where m.match_bkid = '".$depositInfo['Bkid']."'";
								$this->db->query($sql);

								$sql = "DELETE from bankda_match where match_bkid = '".$depositInfo['Bkid']."' and match_oid != '".$oi['oid']."' and match_result = 'duplicate'";
								$this->db->query($sql);

								$sql = "UPDATE
												bankda_match
											SET
												match_result = '".$result."',
												match_bkid = '".$depositInfo['Bkid']."',
												match_type = '".$oi['type']."',
												match_date = NOW()
											WHERE 
												match_oid = '".$oi['oid']."'
											";
							}
						}else{
							$sql = "INSERT INTO
										bankda_match
									SET
										match_oid = '".$oi['oid']."',
										match_result = '".$result."',
										match_bkid = '".$depositInfo['Bkid']."',
										match_type = '".$oi['type']."',
										match_date = NOW()
									";
						}

						if(!$this->db->query($sql)){
							return false;
						}
					}
				endforeach;
			}
		}
		return true;
	}

	/**
	 * 주문로그 남기기
	 * 한주문에 한번만 남김(입점업체별로 볼 필요 X)
	 * @param array $orderInfo
	 * @param array		뱅크다 입금정보
	 */
	public function statusLog($orderInfo,$depositInfo){
		$this->setConnection();
		if($depositInfo[checkmethod] == "mobile"){
			$message = "모바일 자동 입금확인(".$depositInfo['Bkjukyo']." : ".number_format($depositInfo['Bkinput'])."원 ".$depositInfo['Bkcontent'].")";
		}else{
				$message = "뱅크다 입금확인(".$depositInfo['Bkname']."-".$depositInfo['Bkjukyo']." : ".number_format($depositInfo['Bkinput'])."원 ".$depositInfo['Bkcontent'].")";
		}
		$sql = "INSERT INTO 
					shop_order_status 
				SET
					oid = '".$orderInfo['oid']."',
					status = 'IC',
					status_message = '".$message."',
					regdate = NOW()
				";
		$this->db->query($sql);
	}
	
	/**
	 * 회원정보 가져오기
	 * @param string	$ucode
	 */
	public function memberInfoByCode($ucode){
		$this->setConnection();
		$sql = "SELECT id FROM common_user WHERE code = '".$ucode."'";
		$this->db->query($sql);
		if($this->db->total){
			return $this->db->fetch();
		}else{
			return null;
		}
		
	}
	/**
	 * - unused
	 * 주문 상세정보 가져오기
	 * @param string 주문번호
	 * @return array orderDetail
	 */
	public function orderDetail($oid){
		$this->setConnection();
		$result = null;
		$sql = "SELECT
					*
				FROM
					shop_order_detail
				WHERE 
					oid = '".$oid."'
				";
		$this->db->query($sql);
		if($this->db->total){
			$result = $this->db->fetchall();
		}
		return $result;
	}
	
}