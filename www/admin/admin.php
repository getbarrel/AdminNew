<?php

$ig_isSecure = false;

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
	$ig_isSecure = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
	$ig_isSecure = true;
}

if($ig_isSecure === false) {
	//	https 리다이렉트
	//echo "NO1";
	header("Location:https://" . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI']);
	exit;
}

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
include_once("../class/database.class");//디비, 로그인
include_once("./include/design.tmp.php");
include_once("./include/admin.util.php");
include_once("./otp/class/GoogleAuthenticator.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/privacy_setting.php");

$domain = str_replace('www.','',$_SERVER['HTTP_HOST']);
if(substr($_SERVER['HTTP_HOST'],0,2) !='m.'){
	$sql = "select * from ".TBL_SHOP_SHOPINFO." where mall_domain = '{$domain}'";
} else {
	$sql = "select * from ".TBL_SHOP_SHOPINFO." where mall_mobile_domain = '{$domain}'";
}

$db->query($sql);
$db->fetch();
$mall_service_type = $db->dt['mall_service_type'];

if($mall_service_type == 'shoping') {
	$logo_img_title = "omnichannel";
} else if($mall_service_type == 'selling') {
	$logo_img_title = "gst";
} else {
	$logo_img_title = "omnichannel";
}

//관리자 접근제어
$adminAccessIp = array(
    '220.75.187.234'
);

$sql = "select
			*
		from
			shop_mall_config
		where
			mall_ix = '20bd04dac38084b2bafdd6d78cd596b1'
			and config_value is not null
			and config_name = 'admin_access_ip'
		";

$db->query($sql);
$db->fetch();
$admin_ip = $db->dt[config_value];

$admin_access_ip = explode(',',$admin_ip);
$adminAccessIp = array_merge($admin_access_ip,$adminAccessIp);
$adminAccessIp = array_unique($adminAccessIp);

if(!in_array($_SERVER['REMOTE_ADDR'],$adminAccessIp)) {
    $db = new MySQL;
    $sql = "select            
			  config_value as front_url
			from
			  shop_mall_config
		    where
			mall_ix = '20bd04dac38084b2bafdd6d78cd596b1'
			and config_name = 'front_url'";

    $db->query($sql);
    $db->fetch();
    $front_url = $db->dt['front_url'];

    if($front_url) {
        echo "<script type='text/javascript'>					
					location.href='" . $front_url . "';
				</script>";
        exit;
    }
}

if($act== "cert") {	// 2차 인증 항목 추가


	$ga = new PHPGangsta_GoogleAuthenticator();
	$oneCode = $ga->getCode($otpkey);

	if($otpNumber == $oneCode) {
		$taeng_admin_auth_secret_UPDATE_SQL = "
			UPDATE
				common_user
			SET
				otpkey = '".$otpkey."'
			WHERE
				id = '".trim($id)."'
		";
		$db->query($taeng_admin_auth_secret_UPDATE_SQL);

		//	-------------	ig_ 다중로그인 방지 (다른기기)	-------------
		$memcache = new Memcache;
		$memcache->connect(MEMCACHE_IP, MEMCACHE_PORT);

		$Slabs = $memcache->getExtendedStats('slabs'); 

		foreach ($Slabs as $server => $slabs) { 
			foreach ($slabs as $slab_id => $slabMeta) { 
				if(is_numeric($slab_id)) { 
					$cache_dump = $memcache->getExtendedStats('cachedump', (int)$slab_id); 
					foreach ($cache_dump as $server => $entries) { 
						if ($entries) { 
							foreach ($entries as $entry => $detail) { 
								if($memcache->get($entry)) { 
									//$value[] = array($entry=>$memcache->get($entry)); 
									$ig_parse = @explode("admininfo|", $memcache->get($entry));
									$ig_parse_s1 = @str_replace("admininfo", "", $ig_parse[1]);

									$ig_chk_DATA = @unserialize($ig_parse_s1);
										//print_r($ig_chk_DATA);
										//echo "<br>";
										//echo $ig_chk_DATA["admin_id"];
										//echo "<hr>";
									if(trim($ig_chk_DATA["admin_id"]) == trim($id)) {
										echo"
											<script>alert('이미 로그인중인 계정입니다.');
											document.location.href='./'
											</script>
											";
										exit;
									}
								}
							}
						}
					}
				}
			}
		}	
		//print_r($value);
		//	//-------------	ig_ 다중로그인 방지 (다른기기)	-------------

		//	wel 어드민 로그인 비밀번호 입력 오류 시 정책
		//	여기서 하는 이유는 포비즈에서 shop_auth 함수가 들어있는 /www/include/config.php 파일은 라이센스 관련해서 확인못하게 해서 여기서 로그인관련 처리해야함.

		//	임시잠겼을 경우 처리
		$ig_admin_loginChk_SELECT_SQL = "
			SELECT
				*
			FROM
				ig_admin_loginChk
			WHERE
				id = '".trim($id)."'
		";
		$db->query($ig_admin_loginChk_SELECT_SQL);
		$db->fetch();

		switch(trim($db->dt[fail_step])) {
			case "1":
				if(date("Y-m-d H:i:s") <= date("Y-m-d H:i:s",strtotime ("+10 minutes", strtotime($db->dt[regDt]))) ) {
					echo"
						<script>alert('10분후 재시도 가능합니다.');
						document.location.href='./'</script>
						";
					exit;
				}
			break;

			case "2":
				if(date("Y-m-d H:i:s") <= date("Y-m-d H:i:s",strtotime ("+20 minutes", strtotime($db->dt[regDt]))) ) {
					echo"
						<script>alert('20분후 재시도 가능합니다.');
						document.location.href='./'</script>
						";
					exit;
				}
			break;

			case "3":
				if(date("Y-m-d H:i:s") <= date("Y-m-d H:i:s",strtotime ("+30 minutes", strtotime($db->dt[regDt]))) ) {
					echo"
						<script>alert('30분후 재시도 가능합니다.');
						document.location.href='./'</script>
						";
					exit;
				}
			break;

			case "4":
				if(date("Y-m-d H:i:s") <= date("Y-m-d H:i:s",strtotime ("+50 minutes", strtotime($db->dt[regDt]))) ) {
					echo"
						<script>alert('50분후 재시도 가능합니다.');
						document.location.href='./'</script>
						";
					exit;
				}
			break;

			case "5":
					echo"
						<script>alert('잠금계정으로 전환된 계정입니다. 관리자에게 문의하세요.');
						document.location.href='./'</script>
						";
					exit;
			break;
		}
		//	//임시잠겼을 경우 처리

		$ig_loginChk_SQL = "
			select
				CU.code,
				CU.id,
				CU.pw,
				CU.fail_count
			from
				common_user AS CU left join common_company_detail AS CCD ON CU.company_id = CCD.company_id
			where
				CU.mem_type = 'A'
				AND CCD.com_type in ('S','A','G','M','CS','BP','BO','BR')
				AND CU.id = '".trim($id)."'
				AND CU.pw = '".trim(hash("sha256", md5($pw)))."'
		";

		$db->query($ig_loginChk_SQL);
		$db->fetch();

		if(trim($db->dt[code]) != "") {
			//	로그인 성공시 값 초기화
			$ig_admin_loginChk_UPDATE_SQL = "
				UPDATE
					ig_admin_loginChk
				SET
					fail_count = '0',
					fail_step = '0',
					regDt = '".date("Y-m-d H:i:s")."'
				WHERE
					id = '".trim($id)."'
			";
			$db->query($ig_admin_loginChk_UPDATE_SQL);
		} else {
			$ig_loginChk_SQL = "
				select
					CU.code,
					CU.id,
					CU.pw,
					CU.fail_count
				from
					common_user AS CU left join common_company_detail AS CCD ON CU.company_id = CCD.company_id
				where
					CU.mem_type = 'A'
					AND CCD.com_type in ('S','A','G','M','CS','BP','BO','BR')
					AND CU.id = '".trim($id)."'
			";
			$db->query($ig_loginChk_SQL);
			$db->fetch();

			$ig_code = trim($db->dt[code]);

			if(trim($db->dt[code]) != "") {
				//	실패시 기록
				$ig_admin_loginChk_SQL = "
					INSERT INTO
						ig_admin_loginChk
					SET
						id = '".trim($id)."',
						fail_count = 1,
						regDt = '".date("Y-m-d H:i:s")."'
					ON DUPLICATE KEY UPDATE
						fail_count = fail_count+1
				";
				$db->query($ig_admin_loginChk_SQL);

				$ig_admin_loginChk_SELECT_SQL = "
					SELECT
						*
					FROM
						ig_admin_loginChk
					WHERE
						id = '".trim($id)."'
				";
				$db->query($ig_admin_loginChk_SELECT_SQL);
				$db->fetch();

				if(trim($db->dt[fail_count]) >= "10") {
					//	5차(완전 잠금)

					//	실패시 기록
					$ig_admin_loginChk_UPDATE_SQL = "
						UPDATE
							ig_admin_loginChk
						SET
							fail_step = '5',
							regDt = '".date("Y-m-d H:i:s")."'
						WHERE
							id = '".trim($id)."'
					";
					$db->query($ig_admin_loginChk_UPDATE_SQL);

					if($db->dbms_type == "oracle") { //오라클 DB 사용시 쿼리 점검 진행 후 사용 바람
						$sql = "insert common_user_sleep_log set
								code = '".$ig_code."',
								id = '(select id from ".TBL_COMMON_USER." where code = '".$ig_code."' )',
								name = '(select name from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$ig_code."' )',
								status = 'A',
								message = '사원->잠금회원(비밀번호 분실)',
								charger_ix = '".$admininfo[charger_ix]."',
								change_type = 'S',
								regdate = NOW()
								";
						$db->query($sql);	

						$sql = "insert into common_user_sleep select * from ".TBL_COMMON_USER." where code = '".$ig_code."' and NOT EXISTS (SELECT code FROM common_user_sleep WHERE code='".$ig_code."') ";

						$db->sequences = "SHOP_RESERVE_INFO_SEQ";
						if($db->query($sql)){
							$sql = "delete from ".TBL_COMMON_USER." where code = '".$ig_code."' ";
							$db2->query($sql);
						}

						$sql = "insert into common_member_detail_sleep select * from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$ig_code."' and NOT EXISTS (SELECT code FROM common_member_detail_sleep WHERE code='".$ig_code."') ";
						$db->sequences = "SHOP_RESERVE_INFO_SEQ";
						if($db->query($sql)){
							$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$ig_code."' ";
							$db2->query($sql);
						}
						$sql = "insert into common_company_detail_sleep select * from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$ig_code."' and NOT EXISTS (SELECT code FROM common_company_detail_sleep WHERE code='".$ig_code."') ";
						$db->sequences = "SHOP_RESERVE_INFO_SEQ";
						if($db->query($sql)){
							$sql = "delete from ".TBL_COMMON_COMPANY_DETAIL." where code = '".$ig_code."' ";
							$db2->query($sql);
						}
					} else {
						$transction  = $db->query("SET AUTOCOMMIT=0");
						$transction  = $db->query("BEGIN");
						$transction_ok = true;

						$sql = "insert common_user_sleep_log set
								code = '".$ig_code."',
								id = (select id from ".TBL_COMMON_USER." where code = '".$ig_code."' ),
								name = (select name from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$ig_code."' ),
								status = 'A',
								message = '사원->잠금회원(비밀번호 분실)',
								charger_ix = '".$admininfo[charger_ix]."',
								change_type = 'S',
								regdate = NOW()
								";
						$transction = $db->query($sql);
						if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;

						$sql = "insert into common_user_sleep select * from ".TBL_COMMON_USER." where code = '".$ig_code."'  and NOT EXISTS (SELECT code FROM common_user_sleep WHERE code='".$ig_code."')";
						$transction = $db->query($sql);
						if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
					
						$sql = "delete from ".TBL_COMMON_USER." where code = '".$ig_code."' ";
						$transction = $db->query($sql);
						if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
					
						$sql = "insert into common_member_detail_sleep select * from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$ig_code."'  and NOT EXISTS (SELECT code FROM common_member_detail_sleep WHERE code='".$ig_code."')";
						$transction = $db->query($sql);
						if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
						
						$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$ig_code."' ";
						$transction = $db->query($sql);
						
						if(!$transction || mysql_affected_rows() == 0) $transction_ok = false;
					}

					if(!$transction_ok) {
						$transction = $db->query("ROLLBACK");
							echo"
								<script>alert('관리자에게 문의하세요.');
								document.location.href='./'</script>
								";
							exit;
					} else {
						$transction = $db->query("COMMIT");
							echo"
								<script>alert('지속적인 실패로 잠금계정으로 전환되었습니다.');
								document.location.href='./'</script>
								";
							exit;
					}
				} else if(trim($db->dt[fail_count]) >= "9") {
					//	4차(9회 50분)
						//	실패시 기록
						$ig_admin_loginChk_UPDATE_SQL = "
							UPDATE
								ig_admin_loginChk
							SET
								fail_step = '4',
								regDt = '".date("Y-m-d H:i:s")."'
							WHERE
								id = '".trim($id)."'
						";
						$db->query($ig_admin_loginChk_UPDATE_SQL);

					echo"
						<script>alert('지속된 실패 9회로 50분후 재시도 가능합니다.');
						document.location.href='./'</script>
						";
					exit;
				} else if(trim($db->dt[fail_count]) >= "8") {
					//	3차(8회 30분)
						$ig_admin_loginChk_UPDATE_SQL = "
							UPDATE
								ig_admin_loginChk
							SET
								fail_step = '3',
								regDt = '".date("Y-m-d H:i:s")."'
							WHERE
								id = '".trim($id)."'
						";
						$db->query($ig_admin_loginChk_UPDATE_SQL);

					echo"
						<script>alert('지속된 실패 8회로 30분후 재시도 가능합니다.');
						document.location.href='./'</script>
						";
					exit;
				} else if(trim($db->dt[fail_count]) >= "7") {
					//	2차(7회 20분)
						$ig_admin_loginChk_UPDATE_SQL = "
							UPDATE
								ig_admin_loginChk
							SET
								fail_step = '2',
								regDt = '".date("Y-m-d H:i:s")."'
							WHERE
								id = '".trim($id)."'
						";
						$db->query($ig_admin_loginChk_UPDATE_SQL);

					echo"
						<script>alert('지속된 실패 7회로 20분후 재시도 가능합니다.');
						document.location.href='./'</script>
						";
					exit;
				} else if(trim($db->dt[fail_count]) >= "6") {
					//	1차(6회 10분)
						$ig_admin_loginChk_UPDATE_SQL = "
							UPDATE
								ig_admin_loginChk
							SET
								fail_step = '1',
								regDt = '".date("Y-m-d H:i:s")."'
							WHERE
								id = '".trim($id)."'
						";
						$db->query($ig_admin_loginChk_UPDATE_SQL);

					echo"
						<script>alert('지속된 실패 6회로 10분후 재시도 가능합니다.');
						document.location.href='./'</script>
						";
					exit;
				} else {
					echo"
						<script>alert('아이디와 비밀번호를 확인후 다시 시도해 주세요.');
						document.location.href='./'</script>
						";
					exit;
				}
				//	//실패시 기록
			} else {
				//	아예 없는 계정.
				echo"
					<script>alert('아이디와 비밀번호를 확인후 다시 시도해 주세요.');
					document.location.href='./'</script>
					";
				exit;
			}
		}
		//	//wel 어드민 로그인 비밀번호 입력 오류 시 정책

		if(!shop_auth($_POST)) {
			echo "<script>document.location.href='auth.php'</script>";
			exit;
		}

		con_log("login",$id,$_SESSION["admininfo"][company_name]);
		$db = new Database;
		$db->query("select cu.*, cmd.department, cmd.position from common_user cu, common_member_detail cmd where cu.code = cmd.code and cu.code = '".$_SESSION["admininfo"][charger_ix]."' ");

		if($db->total) {
			$db->fetch();

			$_SESSION["admininfo"][department] = $db->dt[department];
			$_SESSION["admininfo"][position] = $db->dt[position];
			$_SESSION["admininfo"][userAuth] = $db->dt[auth];
			
			/*개인정보 보호관련 관리자 영역 데이터 처리 JK160303*/
			if($_SESSION['privacy_config']['change_pw_info'] == 'Y' && $_SESSION['privacy_config']['change_admin_pw_day'] > 0 && $_SESSION['admininfo']['charger_id'] !='forbiz') {//비밀번호 변경안내 설정이 사용함이면서, 관리자 비밀번호 변경기간이 0보다 클때
				//로그인 하려는 회원의 비밀번호 변경 일자를 가져온다. 변경일이 없는 경우 가입일로 대체
				if($db->dt['change_pw_date']) {
					$change_pw_date = date('Y-m-d',strtotime($db->dt['change_pw_date']));
				} else {
					$change_pw_date = date('Y-m-d',strtotime($db->dt['date']));
				}

				$change_ckeck_date = date("Y-m-d",strtotime($change_pw_date."+".$_SESSION['privacy_config']['change_admin_pw_day']."days"));
				$now_date = date("Y-m-d");
				if(strtotime($change_ckeck_date) < strtotime($now_date)) {
					$_SESSION["admininfo"][change_access_pw] = true;
				} else {
					$_SESSION["admininfo"][change_access_pw] = false;
				}
			}
			if($_SESSION['privacy_config']['admin_access_yn'] == 'Y' && $_SESSION['admininfo']['charger_id'] != 'forbiz') { //관리자 접근 허용 설정 사용함일때 
				$admin_access_ip = array();
				$admin_access_ip = explode(',',$_SESSION['privacy_config']['admin_access_ip']);
				
				if(!in_array($_SERVER["REMOTE_ADDR"],$admin_access_ip)) {
					$_SESSION["admininfo"][admin_access] = true;
				} else {
					$_SESSION["admininfo"][admin_access] = false;
				}
			}
			/*끝*/

			if($db->dt["authorized"]=="X" || $db->dt["authorized"]=="N" || $db->dt["auth"]=="0") {	 //$db->dt["auth"]=="0"	셀러권한 부여(회원승인관리에서 셀러승인시 권한부여)
				session_unregister("admininfo");
				echo "<script type='text/javascript'>
						alert('관리자 승인 후 로그인 가능합니다.');
						location.href='/admin/admin.php';
					</script>";
				exit;
			}//관리자 승인대기일 경우도 막아줌, admininfo 세션이 살아있어서 로그인이 되는 것을 막음 kbk 12/05/21

			if($_POST['chk_saveID'] == 'Y')	{					
				setcookie('ck_adminSaveID', $id, time() + (86400 * 30), '/admin/');
			} else {
				setcookie('ck_adminSaveID', '', time() - 86400);
			}

			if($_SESSION["admininfo"][mall_type] != "O" || true) {
				$sql = "select * from service_ing where mem_ix = '".$_SESSION["admininfo"][charger_ix]."' and service_div = 'APP' and solution_div = 'WORK' ";
				$db->query($sql);

				if($db->total  || substr_count($_SERVER["HTTP_HOST"],"unimind.kr")) {
					$_SESSION["admininfo"][use_work] = 1;

					$sql = "SELECT * FROM work_userinfo WHERE charger_ix = '".$_SESSION["admininfo"][charger_ix]."'  ";
					$db->query($sql);

					if($db->total) {
						$db->fetch();
						$_SESSION["admininfo"][master] = $db->dt[master];
					}

					$_SESSION["admininfo"][charger_roll] = 1;
					header("Location:./work/");
					exit;
				}
			} else {
				if($_SESSION["admininfo"][charger_roll] == "" || $_SESSION["admininfo"][charger_roll] == 0) {
					echo("<script>alert('권한이 할당되지 않았습니다. 계속 문제가 될경우는 운영자에게 문의해주시기 바랍니다.^^');document.location.href='./'</script>");
					exit;
				}
			}
		}

		//  ig 마지막 변경된 패스워드가 임시패스워드 인지 체크
		$ig_change_pw_history_chk_SQL = "
			SELECT
				*
			FROM
				ig_change_pw_history
			WHERE
				code = '".$_SESSION["admininfo"][charger_ix]."'
			ORDER BY
				h_pw_ix DESC
			LIMIT 0, 1
			";
		$db->query($ig_change_pw_history_chk_SQL);
		$db->fetch();

		if(trim($db->dt[ch_type]) == "1") {
			//	마지막 변경된 비번이 임시패스워드일 경우.
				$_SESSION["admininfo"][ch_type] = trim($db->dt[ch_type]);
		} else {
			//	집적 수정한 경우.
				$_SESSION["admininfo"][ch_type] = "0";
		}

		//  //ig 마지막 변경된 패스워드가 임시패스워드 인지 체크

		//	ig 세션 고정 (ip가 변경되면 로그아웃 시켜버려야함
		$_SESSION["admininfo"][ch_my_ip] = $_SERVER["REMOTE_ADDR"];
		//	//ig 세션 고정 (ip가 변경되면 로그아웃 시켜버려야함

		if($_SESSION["admininfo"][admin_level] == 9) {
			setcookie("UploadPath", $_SESSION["admininfo"][mall_data_root]."/BatchUploadImages", time()+3600);
			setcookie("UploadServer", "http://".$_SERVER["HTTP_HOST"]."/android/handle_upload.php", time()+3600);

			/********* 도메인이 여러개인 경우 mall_domain_key 를 전부 불러오기 [S]*********///kbk 13/03/13
			$dbm=new Database;		// new MySQL -> new Database : 이경원(2013-04-24)

			$dbm->query("SELECT mall_domain_key FROM ".TBL_SHOP_SHOPINFO." ");
			$domain_key_fetch=$dbm->fetchall();
			$arr_domain_key="";
			for($i=0;$i<count($domain_key_fetch);$i++) {
				if($i==0) $arr_domain_key=$domain_key_fetch[$i]["mall_domain_key"];
				else $arr_domain_key.=",".$domain_key_fetch[$i]["mall_domain_key"];
			}
			$_SESSION["admininfo"][arr_mall_domain_key]=$arr_domain_key;
			/********* 도메인이 여러개인 경우 mall_domain_key 를 전부 불러오기 [E]*********/

			if($_SESSION["admininfo"][mem_type] == "A") {
				if(substr_count($_SERVER["HTTP_HOST"],"unimind.kr") || substr_count($_SERVER["HTTP_HOST"],"unimind")) {
					header("Location:./work/");
				} else {
					$sql = 	"Select distinct am.menu_div, amd.div_name, amd.gnb_name, amd.basic_link
						from admin_menus am left join admin_auth_templet_detail aatd on am.menu_code = aatd.menu_code
						and auth_templet_ix = '".$_SESSION["admininfo"][charger_roll]."'
						and am.disp_auth = 'Y' , admin_menu_div amd
						where amd.div_name = am.menu_div and amd.gnb_use_home = 'Y' and disp_auth = 'Y' and auth_read = 'Y'
						and am.use_home = 'Y' $inventory_str
						order by amd.vieworder asc ";	
					$db->query($sql);
					if($db->total) {
						$db->fetch();
						header("Location:".$db->dt[basic_link]);
					} else {
						header("Location:./store/main.php");
					}
				}
			} else if($_SESSION["admininfo"][mem_type] == "MD") {
				echo("<script>location.href = './seller/';</script>");
			} else if($_SESSION["admininfo"][mem_type] == "CS") {
				echo("<script>location.href = './chainstore/';</script>");
			} else if($_SESSION["admininfo"][mem_type] == "C") {
				echo("<script>location.href = './seller/';</script>");
			} else {
				echo("<script>location.href = './basic/index.php';</script>");
			}
			exit;
		} else if($_SESSION["admininfo"][admin_level] == 8) {

			if($_SESSION["admininfo"][mem_type] == "C") {
				echo("<script>location.href = './seller/';</script>");
			} else if($_SESSION["admininfo"][mem_type] == "MD") {
				echo("<script>location.href = './seller/';</script>");
			} else if($_SESSION["admininfo"][mem_type] == "CS") {
				echo("<script>location.href = './chainstore/';</script>");
			} else {
				echo("<script>location.href = './basic/index.php';</script>");
			}
		}
		$_SESSION["admininfo"][language] = "korae";
	} else {
		$error = "2차 인증 번호가 일치하지 않습니다.";
		echo("<script>alert('$error');</script>");
	}
} else if($act == "verify") {
	if (TRIM($id) != "" && TRIM($pw) != "")	{
		// taeng 1차 인증 후 2차인증 시크릿 키값이 있는지 확인
		$teang_admin_cert_loginChk_SQL = "
			SELECT
				otpkey
			FROM
				common_user
			WHERE
				id = '".trim($id)."'
		";
		$db->query($teang_admin_cert_loginChk_SQL);
		$db->fetch();

		$secret = trim($db->dt[otpkey]);

		// taeng OTP 시크릿키 생성
		$secretChk = 0;
		if($secret == "") {
			$ga = new PHPGangsta_GoogleAuthenticator();

			$secret = $ga->createSecret(); // 시크릿키 생성
			$qrCodeUrl = $ga->getQRCodeGoogleUrl($id, $secret);

			$secretChk = 1;
		} 
		// // taeng OTP 시크릿키 생성

		include("admin_v4.php");

/* 2차인증관련		
		// 1차 인증 여기까지
		// taeng 1차 인증 후 2차인증 시크릿 키값이 있는지 확인
		$teang_admin_cert_loginChk_SQL = "
			SELECT
				otpkey
			FROM
				common_user
			WHERE
				id = '".trim($id)."' AND
				otpkey = ''
		";
		$db->query($teang_admin_cert_loginChk_SQL);
		$db->fetch();

		$secret = trim($db->dt[otpkey]);
		// // taeng 1차 인증 후 2차인증 시크릿 키값이 있는지 확인

		// taeng OTP 시크릿키 생성
		$secretChk = 0;
		if($secret == "") {
			$ga = new PHPGangsta_GoogleAuthenticator();

			//$secret = $ga->createSecret(); // 시크릿키 생성
			$secret="YITTJVES4XHSR2BL";
			$qrCodeUrl = $ga->getQRCodeGoogleUrl($id, $secret);

			$secretChk = 1;
		} 
		// // taeng OTP 시크릿키 생성

		include("admin_v4.php");
		//2차 인증 후 아래항목 처리
*/
		
	} else {
		$error = "아이디와 비밀번호를 확인후 다시 시도해 주세요";
		echo("<script>alert('$error');</script>");
	}
} else if($act== "logout") {
	con_log("logout",$_SESSION["admininfo"][charger_id],$_SESSION["admininfo"][company_name]);

	session_unregister("admininfo");
	session_unregister("admin_config");
	session_unregister("menu_check");
	echo("<script>location.href = 'admin.php';</script>");	
	exit;
} else if($act== "autologout") {
	$charger_id = $_REQUEST['charger_id'];
	$company_name = $_REQUEST['company_name'];

	echo $charger_id." // ".$company_name;

//https://sslstgadmin.getbarrel.com/admin/admin.php?act=autologout&charger_id=hmpartner1&company_name=%EC%A3%BC%EC%8B%9D%ED%9A%8C%EC%82%AC%20%EB%B0%B0%EB%9F%B4
	con_log("logout",$charger_id,$company_name);

	session_unregister("admininfo");
	session_unregister("admin_config");
	session_unregister("menu_check");
	echo("<script>location.href = 'admin.php';</script>");	
	exit;
} else {
	//2014-11-06 Hong 다이소 임시처리 카페24관리자는 일정한 url로 보내기! id 는 box_cafe24 차후 삭제해도됨
	if($_SESSION["admininfo"]["charger_ix"]=="53cac063a2cda1988c4d0529fe3796ab") {
		echo("<script>location.href = './order/cafe24_order_list.php';</script>");
		exit;
	}

	if($_SESSION["admininfo"][admin_level] == 9) {
		if($_SESSION["admininfo"][mem_type] == "A") {
			//echo("<script>location.href = './store/main.php';</script>");
			if(substr_count($_SERVER["HTTP_HOST"],"unimind.kr") || substr_count($_SERVER["HTTP_HOST"],"unimind")) {
				header("Location:./work/");
			} else {
				header("Location:./store/main.php");
			}
		} else if($_SESSION["admininfo"][mem_type] == "MD") {
			echo("<script>location.href = './seller/';</script>");
		} else if($_SESSION["admininfo"][mem_type] == "CS") {
			echo("<script>location.href = './chainstore/';</script>");
		} else if($_SESSION["admininfo"][mem_div] == "S") {
			echo("<script>location.href = './seller/';</script>");
		} else {
			echo("<script>location.href = './store/main.php';</script>");
		}
		exit;
		
	} else if($_SESSION["admininfo"][admin_level] == 8) {
		if($_SESSION["admininfo"][mem_div] == "S"){
			echo("<script>location.href = './seller/';</script>");
		} else if($_SESSION["admininfo"][mem_type] == "MD") {
			echo("<script>location.href = './seller/';</script>");
		} else if($_SESSION["admininfo"][mem_type] == "CS") {
			echo("<script>location.href = './chainstore/';</script>");
		} else {
			echo("<script>location.href = './store/main.php';</script>");
		}
	}
}

if(substr_count($_SERVER["PHP_SELF"],"admin")) {
	preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches); 
	$lang = $matches[1];

	if($lang == "ko-KR") {
		include("admin_v3.php");
	} else {
		include("admin_v3.php");
	}
} else {
	include("admin_v2.php");
}
