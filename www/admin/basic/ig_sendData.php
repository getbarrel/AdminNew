<?php

	//	휴면 / 일반 전환

include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

$db = new MySQL;
$db2 = new MySQL;
$db3 = new MySQL;

	
//		Array
//		(
//			[sendType] => sleep
//			[code_List] => Array
//				(
//					[0] => e595fa84e90d62f3d1ba7c30c398759c
//					[1] => 72f07c889ffd8753da87a07972e4301c
//				)
//		)


		if(count($_POST["code_List"]) > "0" ) {
			if(trim($_POST["sendType"]) != "") {

				$code = $_POST["code_List"];	//	넘어온 회원코드들을 $code 에 담는다.

				switch(trim($_POST["sendType"])) {
					case "sleep":
						//	잠그기

							for($i=0; $i < count($code);$i++){
								if($db->dbms_type == "oracle"){ //오라클 DB 사용시 쿼리 점검 진행 후 사용 바람
									
									$sql = "insert common_user_sleep_log set
												code = '".$code[$i]."',
												id = '(select id from ".TBL_COMMON_USER." where code = '".$code[$i]."' )',
												name = '(select name from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$code[$i]."' )',
												status = 'A',
												message = '사원->잠금회원',
												charger_ix = '".$admininfo[charger_ix]."',
												change_type = 'S',
												regdate = NOW()
										";
									$db->query($sql);	


									$sql = "insert into common_user_sleep select * from ".TBL_COMMON_USER." where code = '".$code[$i]."' and NOT EXISTS (SELECT code FROM common_user_sleep WHERE code='".$code[$i]."') ";

									$db->sequences = "SHOP_RESERVE_INFO_SEQ";
									if($db->query($sql)){
										$sql = "delete from ".TBL_COMMON_USER." where code = '".$code[$i]."' ";
										$db2->query($sql);
									}

									$sql = "insert into common_member_detail_sleep select * from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$code[$i]."' and NOT EXISTS (SELECT code FROM common_member_detail_sleep WHERE code='".$code[$i]."') ";
									$db->sequences = "SHOP_RESERVE_INFO_SEQ";
									if($db->query($sql)){
										$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$code[$i]."' ";
										$db2->query($sql);
									}
									$sql = "insert into common_company_detail_sleep select * from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$code[$i]."' and NOT EXISTS (SELECT code FROM common_company_detail_sleep WHERE code='".$code[$i]."') ";
									$db->sequences = "SHOP_RESERVE_INFO_SEQ";
									if($db->query($sql)){
										$sql = "delete from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$code[$i]."' ";
										$db2->query($sql);
									}
									
								}else{
									
									$transction  = $db->query("SET AUTOCOMMIT=0");
									$transction  = $db->query("BEGIN");
									$transction_ok = true;

									
									

									$sql = "insert common_user_sleep_log set
												code = '".$code[$i]."',
												id = (select id from ".TBL_COMMON_USER." where code = '".$code[$i]."' ),
												name = (select name from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$code[$i]."' ),
												status = 'A',
												message = '사원->잠금회원',
												charger_ix = '".$admininfo[charger_ix]."',
												change_type = 'S',
												regdate = NOW()
										";
									$transction = $db->query($sql);
									if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;


									$sql = "insert into common_user_sleep select * from ".TBL_COMMON_USER." where code = '".$code[$i]."'  and NOT EXISTS (SELECT code FROM common_user_sleep WHERE code='".$code[$i]."')";
									$transction = $db->query($sql);
									if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
								
									$sql = "delete from ".TBL_COMMON_USER." where code = '".$code[$i]."' ";
									$transction = $db->query($sql);
									if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

								
									$sql = "insert into common_member_detail_sleep select * from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$code[$i]."'  and NOT EXISTS (SELECT code FROM common_member_detail_sleep WHERE code='".$code[$i]."')";
									$transction = $db->query($sql);
									if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

									
									$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$code[$i]."' ";
									$transction = $db->query($sql);
									if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

								}
								
							}


							if(!$transction_ok){
								$transction = $db->query("ROLLBACK");
									echo "전환 작업이 실패 하였습니다.";
								exit;
							}else{
								$transction = $db->query("COMMIT");
									echo "sendOK";
								exit;
							}
								
					break;

					case "nosleep":
						//	풀기

							for($i=0; $i < count($code);$i++){
								if($db->dbms_type == "oracle"){ //오라클 DB 사용시 쿼리 점검 진행 후 사용 바람
									
									$sql = "insert common_user_sleep_log set
												code = '".$code[$i]."',
												id = (select id from common_user_sleep where code = '".$code[$i]."' ),
												name = (select name from common_member_detail_sleep where code = '".$code[$i]."' ),
												status = 'A',
												message = '$message',
												charger_ix = '".$admininfo[charger_ix]."',
												change_type = 'M',
												regdate = NOW()
										";
									$db->query($sql);	


									$sql = "insert into ".TBL_COMMON_USER." select * from common_user_sleep where code = '".$code[$i]."' and NOT EXISTS (SELECT code FROM ".TBL_COMMON_USER." WHERE code='".$code[$i]."')";
									$db->sequences = "SHOP_RESERVE_INFO_SEQ";
									if($db->query($sql)){
										$sql = "delete from common_user_sleep where code = '".$code[$i]."' ";
										$db3->query($sql);
									}

									$sql = "insert into ".TBL_COMMON_MEMBER_DETAIL." select * from common_member_detail_sleep where code = '".$code[$i]."' and NOT EXISTS (SELECT code FROM ".TBL_COMMON_MEMBER_DETAIL." WHERE code='".$code[$i]."') ";
									$db->sequences = "SHOP_RESERVE_INFO_SEQ";
									if($db->query($sql)){
										$sql = "delete from common_member_detail_sleep where code = '".$code[$i]."' ";
										$db3->query($sql);
									}
									

									
								}else{
									$transction  = $db->query("SET AUTOCOMMIT=0");
									$transction  = $db->query("BEGIN");
									$transction_ok = true;

									$sql = "insert common_user_sleep_log set
												code = '".$code[$i]."',
												id = (select id from common_user_sleep where code = '".$code[$i]."' ),
												name = (select name from common_member_detail_sleep where code = '".$code[$i]."' ),
												status = 'A',
												message = '$message',
												charger_ix = '".$admininfo[charger_ix]."',
												change_type = 'M',
												regdate = NOW()
										";

									$transction = $db->query($sql);
									if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
									
									$sql = "insert into ".TBL_COMMON_USER." select * from common_user_sleep where code = '".$code[$i]."'  and NOT EXISTS (SELECT code FROM ".TBL_COMMON_USER." WHERE code='".$code[$i]."')";
									$transction = $db->query($sql);
									if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

									
									$sql = "delete from common_user_sleep where code = '".$code[$i]."' ";
									$transction = $db->query($sql);
									if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;


									$sql = "insert into ".TBL_COMMON_MEMBER_DETAIL." select * from common_member_detail_sleep where code = '".$code[$i]."'  and NOT EXISTS (SELECT code FROM ".TBL_COMMON_MEMBER_DETAIL." WHERE code='".$code[$i]."')";
									$transction = $db->query($sql);
									if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
									

									$sql = "delete from common_member_detail_sleep where code = '".$code[$i]."' ";
									$transction = $db->query($sql);
									if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

								}



									//	어드민 로그인 체크 패스워드 초기화
										$ig_admin_loginChk_UPDATE_SQL = "
											UPDATE
												ig_admin_loginChk
											SET
												fail_count = '0',
												fail_step = '0',
												regDt = '".date("Y-m-d H:i:s")."'
											WHERE
												id = (select id from common_user where code = '".$code[$i]."' )
										";
										$db->query($ig_admin_loginChk_UPDATE_SQL);
									//	//어드민 로그인 체크 패스워드 초기화
							}


							if(!$transction_ok){
								$transction = $db->query("ROLLBACK");
									echo "전환 작업이 실패 하였습니다.";
								exit;
							}else{
								$transction = $db->query("COMMIT");
									echo "sendOK";
								exit;
							}

					break;


					case "ig_admin_loginChk":
						//	임시잠금 해제
							if(trim($code[0]) != "") {
								$ig_admin_loginChk_UPDATE_SQL = "
									UPDATE
										ig_admin_loginChk
									SET
										fail_count = '0',
										fail_step = '0',
										regDt = '".date("Y-m-d H:i:s")."'
									WHERE
										id = '".trim($code[0])."'
								";
								$db->query($ig_admin_loginChk_UPDATE_SQL);
								echo "sendOK";
								exit;
							} else {
								echo "정상적인 방법으로 이용바랍니다.";
								exit;
							}
					break;

					default:
						echo "정상적인 방법으로 이용바랍니다.";
						exit;
					break;
				}

			} else {
				echo "선택된 사원이 없습니다.";
				exit;
			}
		} else {
			echo "선택된 사원이 없습니다.";
			exit;
		};



?>