<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

$db = new MySQL;
$db2 = new MySQL;
$db3 = new MySQL;

$unserialize_search_value = unserialize(urldecode($search_searialize_value));
//print_r ($unserialize_search_value);
//print_r($_POST);
//exit;
extract($unserialize_search_value);
//var_dump(extract($unserialize_search_value));
if($_POST["update_kind"]){
	$update_kind = $_POST["update_kind"];
}


$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
if ($FromYY == ""){

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

}

if ($vFromYY == ""){

	$sDate2 = date("Y/m/d", $before10day);
	$eDate2 = date("Y/m/d");

	$startDate2 = date("Ymd", $before10day);
	$endDate2 = date("Ymd");

}else{

	$sDate2 = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate2 = $vToYY."/".$vToMM."/".$vToDD;
	$startDate2 = $vFromYY.$vFromMM.$vFromDD;
	$endDate2 = $vToYY.$vToMM.$vToDD;

}

if ($birYY == ""){

	$sDate3 = date("Y/m/d");
	$eDate3 = date("Y/m/d");

	$startDate3 = date("Ymd");
	$endDate3 = date("Ymd");
}else{

	$sDate3 = $birYY."/".$birMM."/".$birDD;
	$eDate3 = "none";
	$startDate3 = $birYY.$birMM.$birDD;
	$endDate3 = "none";
	$birDate = $birYY.$birMM.$birDD;
}



	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}

include "member_query.php";
/*회원 관련 휴면 TABLE 이 존재하지 않을 경우 기존 회원테이블과 동일하게 생성함 JK*/
$sql = "SHOW TABLES LIKE 'common_user_sleep'";
$db->query($sql);

if(!is_array($db->fetchall())){
	$sql = "CREATE TABLE IF NOT EXISTS common_user_sleep LIKE ".TBL_COMMON_USER."";
	$db->query($sql);
}

$sql = "SHOW TABLES LIKE 'common_member_detail_sleep'";
$db->query($sql);

if(!is_array($db->fetchall())){
	$sql = "CREATE TABLE IF NOT EXISTS common_member_detail_sleep LIKE ".TBL_COMMON_MEMBER_DETAIL."";
	$db->query($sql);
}

/*
$sql = "SHOW TABLES LIKE 'common_company_detail_sleep'";
$db->query($sql);

if(!is_array($db->fetchall())){
	$sql = "CREATE TABLE IF NOT EXISTS common_company_detail_sleep LIKE ".TBL_COMMON_COMPANY_DETAIL."";
	$db->query($sql);
}
*/
//로그 DB 없을때 생성
$sql = "SHOW TABLES LIKE 'common_user_sleep_log'";
$db->query($sql);

if(!is_array($db->fetchall())){
	$sql = "CREATE TABLE common_user_sleep_log (
			  `sl_ix` int(10) unsigned zerofill NOT NULL auto_increment COMMENT '로그키값',
			  `code` varchar(32) NOT NULL COMMENT '회원코드',
			  `id` varchar(20) default NULL COMMENT '아이디',
			  `name` varchar(200) default NULL COMMENT '회원명',
			  `status` varchar(10) default NULL COMMENT '변경상태(자동, 수동 등)',
			  `message` varchar(255) default NULL COMMENT '변경사유',
			  `charger_ix` varchar(32) default NULL COMMENT '관리자코드',
			  `change_type` enum('S','M') default NULL COMMENT '전환타입(S 휴면으로 전환, M 회원으로 재전환)',
			  `regdate` datetime default NULL COMMENT '등록일',
			  PRIMARY KEY  (`sl_ix`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
			";
	$db->query($sql);
}
/*끝*/
if($act == 'move_sleep'){


	if($update_type == 2){// 선택회원일때
		for($i=1; $i < count($code);$i++){
			if($db->dbms_type == "oracle"){ //오라클 DB 사용시 쿼리 점검 진행 후 사용 바람
				
				$sql = "insert common_user_sleep_log set
							code = '".$code[$i]."',
							id = '(select id from ".TBL_COMMON_USER." where code = '".$code[$i]."' )',
							name = '(select name from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$code[$i]."' )',
							status = 'A',
							message = '일반회원->휴면회원',
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
							message = '일반회원->휴면회원',
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



                $sql = "SELECT oid FROM shop_order WHERE user_code = '".$code[$i]."'";
                $db2->query($sql);

                if($db2->total > 0) {
                    $order_datas = $db2->fetchall("object");
                    for($x = 0; $x < count($order_datas); $x++) {
                        $oid = $order_datas[$x]['oid'];

                        // shop_order - separation_shop_order
                        $sql = "INSERT INTO separation_shop_order (oid, btel, bmobile, bmail, bzip, baddr, regdate) 
                            SELECT oid, btel, bmobile, bmail, bzip, baddr, NOW() FROM shop_order WHERE oid = '" . $oid . "'";
                        $db2->query($sql);

                        $sql = "SELECT * FROM separation_shop_order WHERE oid = '" . $oid . "'";
                        $db2->query($sql);
                        if ($db2->total > 0) {
                            $sql = "UPDATE shop_order SET
                          btel=''
                          ,bmobile=''
                          ,bmail=''
                          ,bzip=''
                          ,baddr=''
                        WHERE oid = '" . $oid . "'";
                            $db2->query($sql);
                        }

                        // shop_order_detail_deliveryinfo - separation_shop_order_deliveryinfo
                        $sql = "SELECT odd_ix FROM shop_order_detail_deliveryinfo WHERE oid = '" . $oid . "'";
                        $db2->query($sql);
                        if ($db2->total > 0) {
                            $order_detail_deliveryinfo_datas = $db2->fetchall("object");

                            for ($y = 0; $y < count($order_detail_deliveryinfo_datas); $y++) {
                                $order_detail_deliveryinfo = $order_detail_deliveryinfo_datas[$y];
                                $sql = "INSERT INTO separation_shop_order_deliveryinfo (odd_ix, oid, od_ix, rname, rtel, rmobile, rmail, zip, addr1, addr2, regdate) 
                                  SELECT odd_ix, oid, od_ix, rname, rtel, rmobile, rmail, zip, addr1, addr2, NOW() FROM shop_order_detail_deliveryinfo WHERE odd_ix = '" . $order_detail_deliveryinfo[odd_ix] . "'";
                                $db2->query($sql);

                                $sql = "SELECT * FROM separation_shop_order_deliveryinfo WHERE odd_ix = '" . $order_detail_deliveryinfo[odd_ix] . "'";
                                $db2->query($sql);
                                if ($db2->total > 0) {
                                    $sql = "UPDATE shop_order_detail_deliveryinfo SET
                                  rname='휴면회원'
                                  ,rtel=''
                                  ,rmobile=''
                                  ,rmail=''
                                  ,zip=''
                                  ,addr1=''
                                  ,addr2=''
                                WHERE odd_ix = '" . $order_detail_deliveryinfo[odd_ix] . "'";
                                    $db2->query($sql);
                                }
                            }
                        }
                    }
                }
				/*
				$sql = "insert into common_company_detail_sleep select * from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$code[$i]."'  and NOT EXISTS (SELECT code FROM common_company_detail_sleep WHERE code='".$code[$i]."')";
				$transction = $db->query($sql);
				if(!$transction ) $transction_ok = false;

				
				$sql = "delete from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$code[$i]."' ";
				$transction = $db->query($sql);
				if(!$transction ) $transction_ok = false;
				*/
			}
			
		}

		if(!$transction_ok){
			$transction = $db->query("ROLLBACK");
			echo("<script>alert('전환 작업이 실패 하였습니다.');parent.document.location.reload();</script>");
			exit;
		}else{
			$transction = $db->query("COMMIT");
			echo("<script>alert('선택회원 전체 휴면회원 전환 완료.');parent.document.location.reload();</script>");
			exit;
		}
		
		
	}else{// 검색회원일때

			//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
			if($db->dbms_type == "oracle"){ //오라클 DB 사용시 쿼리 점검 진행 후 사용 바람
				
				$sql = "SELECT cu.id, cu.code, cmd.name from ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code $where ";
				$db->query($sql);

				for($z=0; $z < $db->total; $z++){
					$db->fetch($z);
					
					$sql = "insert common_user_sleep_log set
							code = '".$db->dt[code]."',
							id = '".$db->dt[id]."',
							name = '".$db->dt[name]."',
							status = 'A',
							message = '일반회원->휴면회원',
							charger_ix = '".$admininfo[charger_ix]."',
							change_type = 'S',
							regdate = NOW()
					";
					$db2->query($sql);	

					$sql = "insert into common_user_sleep select * from ".TBL_COMMON_USER." where code = '".$db->dt[code]."' and NOT EXISTS (SELECT code FROM common_user_sleep WHERE code='".$db->dt[code]."')";
					if($db2->query($sql)){
						$sql = "delete from ".TBL_COMMON_USER." where code = '".$db->dt[code]."' ";
						$db3->query($sql);
					}

					$sql = "insert into common_member_detail_sleep select * from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$db->dt[code]."' and NOT EXISTS (SELECT code FROM common_member_detail_sleep WHERE code='".$db->dt[code]."')";
					if($db2->query($sql)){
						$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$db->dt[code]."' ";
						$db3->query($sql);
					}
				/*	
					$sql = "insert into common_company_detail_sleep select * from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$db->dt[code]."' and NOT EXISTS (SELECT code FROM common_company_detail_sleep WHERE code='".$db->dt[code]."')";
					if($db2->query($sql)){
						$sql = "delete from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$db->dt[code]."' ";
						$db3->query($sql);
					}
				*/

				}
				
				

			}else{


                /*
                $where = ' WHERE 1=1 ';
			    if(isset($code[0])){
                    $inMember = array();
                    foreach($code as $val){
                        if($val) array_push($inMember, $val);

                    }
                    $inMember =  implode( '","',$inMember);
                    $where .= 'AND cu.code in ("'.$inMember.'")';
                }*/


				$sql = "SELECT 
                          cu.id, cu.code, cmd.name 
                        from 
                          ".TBL_COMMON_USER." cu 
                        inner join 
                            ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code 
                        LEFT JOIN shop_groupinfo AS mg ON (cmd.gp_ix = mg.gp_ix)
                        LEFT JOIN common_company_detail AS ccd ON (
                            ccd.company_id = cu.company_id
                        )
                        where 1
                        $where ";
				$db->query($sql);

				if($db->total){

					$s=0;
					$f=0;
					$transction  = $db2->query("SET AUTOCOMMIT=0");
					$transction  = $db2->query("BEGIN");
					$transction_ok = true;
					$db2->transction = true;

					for($z=0; $z < $db->total; $z++){
						$db->fetch($z);

						$sql = "insert common_user_sleep_log set
								code = '".$db->dt[code]."',
								id = '".$db->dt[id]."',
								name = '".$db->dt[name]."',
								status = 'A',
								message = '일반회원->휴면회원',
								charger_ix = '".$admininfo[charger_ix]."',
								change_type = 'S',
								regdate = NOW()
						";
						$transction = $db2->query($sql);
						if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
						//$db2->debug=true;

						$sql = "insert into common_user_sleep select * from ".TBL_COMMON_USER." where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM common_user_sleep WHERE code='".$db->dt[code]."')";

						$transction = $db2->query($sql);
						if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

						$sql = "delete from ".TBL_COMMON_USER." where code = '".$db->dt[code]."' ";
						$transction = $db2->query($sql);
						if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

						$sql = "insert into common_member_detail_sleep select * from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM common_member_detail_sleep WHERE code='".$db->dt[code]."')";
						$transction = $db2->query($sql);
						if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

						$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$db->dt[code]."' ";
						$transction = $db2->query($sql);
						if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
						/*
						$sql = "insert into common_company_detail_sleep select * from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM common_company_detail_sleep WHERE code='".$db->dt[code]."')";
						$transction = $db2->query($sql);
						if(!$transction ) $transction_ok = false;

						$sql = "delete from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$db->dt[code]."' ";
						$transction = $db2->query($sql);
						if(!$transction ) $transction_ok = false;
						*/


                        $sql = "SELECT oid FROM shop_order WHERE user_code = '".$db->dt[code]."'";
                        $db2->query($sql);

                        if($db2->total > 0) {
                            $order_datas = $db2->fetchall("object");
                            for($x = 0; $x < count($order_datas); $x++) {
                                $oid = $order_datas[$x]['oid'];

                                // shop_order - separation_shop_order
                                $sql = "SELECT * FROM separation_shop_order WHERE oid = '" . $oid . "'";
                                $db2->query($sql);
                                if (!$db2->total) {
                                    $sql = "INSERT INTO separation_shop_order (oid, btel, bmobile, bmail, bzip, baddr, regdate) 
                                    SELECT oid, btel, bmobile, bmail, bzip, baddr, NOW() FROM shop_order WHERE oid = '" . $oid . "'";
                                    $db2->query($sql);
                                }
                                $sql = "SELECT * FROM separation_shop_order WHERE oid = '" . $oid . "'";
                                $db2->query($sql);
                                if ($db2->total > 0) {
                                    $sql = "UPDATE shop_order SET
                                      btel=''
                                      ,bmobile=''
                                      ,bmail=''
                                      ,bzip=''
                                      ,baddr=''
                                    WHERE oid = '" . $oid . "'";
                                    $db2->query($sql);
                                }

                                // shop_order_detail_deliveryinfo - separation_shop_order_deliveryinfo
                                $sql = "SELECT odd_ix FROM shop_order_detail_deliveryinfo WHERE oid = '" . $oid . "'";
                                $db2->query($sql);
                                if ($db2->total > 0) {
                                    $order_detail_deliveryinfo_datas = $db2->fetchall("object");

                                    for ($y = 0; $y < count($order_detail_deliveryinfo_datas); $y++) {
                                        $order_detail_deliveryinfo = $order_detail_deliveryinfo_datas[$y];
                                        $sql = "SELECT * FROM separation_shop_order_deliveryinfo WHERE odd_ix = '" . $order_detail_deliveryinfo[odd_ix] . "'";
                                        $db2->query($sql);
                                        if (!$db2->total) {
                                            $sql = "INSERT INTO separation_shop_order_deliveryinfo (odd_ix, oid, od_ix, rname, rtel, rmobile, rmail, zip, addr1, addr2, regdate) 
                                            SELECT odd_ix, oid, od_ix, rname, rtel, rmobile, rmail, zip, addr1, addr2, NOW() FROM shop_order_detail_deliveryinfo WHERE odd_ix = '" . $order_detail_deliveryinfo[odd_ix] . "'";
                                            $db2->query($sql);
                                        }
                                        $sql = "SELECT * FROM separation_shop_order_deliveryinfo WHERE odd_ix = '" . $order_detail_deliveryinfo[odd_ix] . "'";
                                        $db2->query($sql);
                                        if ($db2->total > 0) {
                                            $sql = "UPDATE shop_order_detail_deliveryinfo SET
                                  rname='휴면회원'
                                  ,rtel=''
                                  ,rmobile=''
                                  ,rmail=''
                                  ,zip=''
                                  ,addr1=''
                                  ,addr2=''
                                WHERE odd_ix = '" . $order_detail_deliveryinfo[odd_ix] . "'";
                                            $db2->query($sql);
                                        }
                                    }
                                }
                            }
                        }
						
						

						if(!$transction_ok){
							$transction = $db2->query("ROLLBACK");
							$f++;
							
						}else{
							//exit;
							$transction = $db2->query("COMMIT");
							$s++;
						}
					}
					
					echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색 대상중 전환성공".$s."건 전환실패 ".$f."건 처리완료.');parent.document.location.reload();</script>");
					exit;	
					
					
				}else{
					echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전환 대상이 존재하지 않습니다..');parent.document.location.reload();</script>");
					exit;
				}
			}
				
	}
}

if($act == 'move_member'){
	if($update_type == 2){// 선택회원일때
		for($i=1; $i <count($code);$i++){
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
				
				/*
				$sql = "insert into ".TBL_COMMON_COMPANY_DETAIL." select * from common_company_detail_sleep where code = '".$code[$i]."' and NOT EXISTS (SELECT code FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE code='".$code[$i]."') ";
				$db->sequences = "SHOP_RESERVE_INFO_SEQ";
				if($db->query($sql)){
					$sql = "delete from common_company_detail_sleep where code = '".$code[$i]."' ";
					$db3->query($sql);
				}
				*/
				
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
				
				/*
				$sql = "insert into ".TBL_COMMON_COMPANY_DETAIL." select * from common_company_detail_sleep where code = '".$code[$i]."'  and NOT EXISTS (SELECT code FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE code='".$code[$i]."')";
				$transction = $db->query($sql);
				if(!$transction ) $transction_ok = false;


				
				$sql = "delete from common_company_detail_sleep where code = '".$code[$i]."' ";
				$transction = $db->query($sql);
				if(!$transction ) $transction_ok = false;
				*/

                $sql = "SELECT oid FROM shop_order WHERE user_code = '".$code[$i]."'";
                $db2->query($sql);

                if($db2->total > 0) {
                    $order_datas = $db2->fetchall("object");
                    for($x = 0; $x < count($order_datas); $x++) {
                        $oid = $order_datas[$x]['oid'];

                        $sql = "UPDATE shop_order o , separation_shop_order so SET
                          o.btel=so.btel
                          ,o.bmobile=so.bmobile
                          ,o.bmail=so.bmail
                          ,o.bzip=so.bzip
                          ,o.baddr=so.baddr
                        WHERE o.oid = '" . $oid . "'
                        and o.oid=so.oid";
                        $db2->query($sql);

                        $sql="delete from separation_shop_order where oid='". $oid ."'";
                        $db2->query($sql);

                        $sql = "UPDATE shop_order_detail_deliveryinfo d, separation_shop_order_deliveryinfo sd SET
                                  d.rname=sd.rname
                                  ,d.rtel=sd.rtel
                                  ,d.rmobile=sd.rmobile
                                  ,d.rmail=sd.rmail
                                  ,d.zip=sd.zip
                                  ,d.addr1=sd.addr1
                                  ,d.addr2=sd.addr2
                                WHERE d.oid = '" . $oid . "'
                                and d.odd_ix=sd.odd_ix";
                        $db2->query($sql);

                        $sql="delete from separation_shop_order_deliveryinfo where oid='". $oid ."'";
                        $db2->query($sql);
                    }
                }
			}
			
		}
		if(!$transction_ok){
			//echo 1;
			//exit;
			$transction = $db2->query("ROLLBACK");
			echo("<script>alert('전환 작업이 실패 하였습니다.');parent.document.location.reload();</script>");
		}else{
			//exit;
			$transction = $db2->query("COMMIT");
			echo("<script>alert('선택회원 전체 일반회원 전환 완료.');parent.document.location.reload();</script>");
		}
		
		exit;
	}else{// 검색회원일때

		//$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$uid','','','','','$reserve','$state','$etc',NOW())");
		if($db->dbms_type == "oracle"){ //오라클 DB 사용시 쿼리 점검 진행 후 사용 바람
			
			$sql = "SELECT cu.id, cu.code, cmd.name from common_user_sleep cu left join common_member_detail_sleep cmd on cu.code = cmd.code $where ";
			$db->query($sql);

			for($z=0; $z < $db->total; $z++){
				$db->fetch($z);

				$sql = "insert common_user_sleep_log set
						code = '".$db->dt[code]."',
						id = '".$db->dt[id]."',
						name = '".$db->dt[name]."',
						status = 'A',
						message = '$message',
						charger_ix = '".$admininfo[charger_ix]."',
						change_type = 'M',
						regdate = NOW()
				";
				$db2->query($sql);	

				$sql = "insert into ".TBL_COMMON_USER." select * from common_user_sleep where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM ".TBL_COMMON_USER." WHERE code='".$db->dt[code]."')";
				if($db2->query($sql)){
					$sql = "delete from common_user_sleep where code = '".$db->dt[code]."' ";
					$db3->query($sql);
				}

				$sql = "insert into ".TBL_COMMON_MEMBER_DETAIL." select * from common_member_detail_sleep where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM ".TBL_COMMON_MEMBER_DETAIL." WHERE code='".$db->dt[code]."')";
				if($db2->query($sql)){
					$sql = "delete from common_member_detail_sleep where code = '".$db->dt[code]."' ";
					$db3->query($sql);
				}

                $sql = "SELECT oid FROM shop_order WHERE user_code = '".$db->dt[code]."'";
                $db2->query($sql);

                if($db2->total > 0) {
                    $order_datas = $db2->fetchall("object");
                    for($x = 0; $x < count($order_datas); $x++) {
                        $oid = $order_datas[$x]['oid'];

                        $sql = "UPDATE shop_order o , separation_shop_order so SET
                          o.btel=so.btel
                          ,o.bmobile=so.bmobile
                          ,o.bmail=so.bmail
                          ,o.bzip=so.bzip
                          ,o.baddr=so.baddr
                        WHERE o.oid = '" . $oid . "'
                        and o.oid=so.oid";
                        $db2->query($sql);

                        $sql="delete from separation_shop_order  where oid='". $oid ."'";
                        $db2->query($sql);

                        $sql = "UPDATE shop_order_detail_deliveryinfo d, separation_shop_order_deliveryinfo sd SET
                                  d.rname=sd.rname
                                  ,d.rtel=sd.rtel
                                  ,d.rmobile=sd.rmobile
                                  ,d.rmail=sd.rmail
                                  ,d.zip=sd.zip
                                  ,d.addr1=sd.addr1
                                  ,d.addr2=sd.addr2
                                WHERE d.oid = '" . $oid . "'
                                and d.odd_ix=sd.odd_ix";
                        $db2->query($sql);

                        $sql="delete from separation_shop_order_deliveryinfo  where oid='". $oid ."'";
                        $db2->query($sql);
                    }
                }
				
				/*
				$sql = "insert into ".TBL_COMMON_COMPANY_DETAIL." select * from common_company_detail_sleep where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE code='".$db->dt[code]."')";
				if($db2->query($sql)){
					$sql = "delete from common_company_detail_sleep where code = '".$db->dt[code]."' ";
					$db3->query($sql);
				}
				*/

			}
			
			

		}else{
			

			$sql = "SELECT cu.id, cu.code, cmd.name from common_user_sleep cu left join common_member_detail_sleep cmd on cu.code = cmd.code 
                    where 1 $where ";

			$db->query($sql);

			if($db->total){
				
				$transction  = $db2->query("SET AUTOCOMMIT=0");
				$transction  = $db2->query("BEGIN");
				$transction_ok = true;
				$db2->transction = true;
				
				$s=0;
				$f=0;

				for($z=0; $z < $db->total; $z++){
					$db->fetch($z);

					$sql = "insert common_user_sleep_log set
							code = '".$db->dt[code]."',
							id = '".$db->dt[id]."',
							name = '".$db->dt[name]."',
							status = 'A',
							message = '$message',
							charger_ix = '".$admininfo[charger_ix]."',
							change_type = 'M',
							regdate = NOW()
					";
					$transction = $db2->query($sql);
					if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

					$sql = "insert into ".TBL_COMMON_USER." select * from common_user_sleep where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM ".TBL_COMMON_USER." WHERE code='".$db->dt[code]."')";
					$transction = $db2->query($sql);
					if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;


					
					$sql = "delete from common_user_sleep where code = '".$db->dt[code]."' ";
					$transction = $db2->query($sql);
					if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;



					$sql = "insert into ".TBL_COMMON_MEMBER_DETAIL." select * from common_member_detail_sleep where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM ".TBL_COMMON_MEMBER_DETAIL." WHERE code='".$db->dt[code]."')";
					$transction = $db2->query($sql);
					if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;


					
					$sql = "delete from common_member_detail_sleep where code = '".$db->dt[code]."' ";
					$transction = $db2->query($sql);
					if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

					/*
					$sql = "insert into ".TBL_COMMON_COMPANY_DETAIL." select * from common_company_detail_sleep where code = '".$db->dt[code]."'  and NOT EXISTS (SELECT code FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE code='".$db->dt[code]."')";
					$transction = $db2->query($sql);
					if(!$transction ) $transction_ok = false;


					
					$sql = "delete from common_company_detail_sleep where code = '".$db->dt[code]."' ";
					$transction = $db2->query($sql);
					if(!$transction) $transction_ok = false;
					*/
					
					if(!$transction_ok){
						$transction = $db2->query("ROLLBACK");
						$f++;
						
					}else{
						//exit;
						$transction = $db2->query("COMMIT");
						$s++;
					}

				}
				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색 대상중 전환성공".$s."건 전환실패 ".$f."건 처리완료.');parent.document.location.reload();</script>");
				exit;	
				/*if(!$transction_ok){
					//echo 1;
					//exit;
					$transction = $db2->query("ROLLBACK");
					echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전환 작업이 실패 하였습니다.');parent.document.location.reload();</script>");
				}else{
					//exit;
					$transction = $db2->query("COMMIT");
					echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('검색회원 전체 일반회원으로 전환 되었습니다.');parent.document.location.reload();</script>");
				}*/
			
			}else{
				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('전환대상이 없습니다.');parent.document.location.reload();</script>");
			}
			
			exit;
		}
	}
}
?>