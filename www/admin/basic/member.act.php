<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");

$db = new Database;
$db2 = new Database;
$ig_db = new Database;

if ($act == "update"){

	$join_date = trim($_REQUEST[join_date]);		//입사일
	$nationality = trim($_REQUEST[nationality]);		//국내외구분 I - 국내 O - 국외
	$mem_name = trim($_REQUEST[mem_name]);		//이름
	$birthday_div = trim($_REQUEST[birthday_div]);		//음력/양력	YA - 양력 YI - 음력
	$birth일ay = trim($_REQUEST[birthday]);		//생일
	$married = trim($_REQUEST[married]);		//기혼,미혼

	$com_group = trim($_REQUEST[com_group]);		//부서그룹코드
	$department = trim($_REQUEST[department]);		//부서코드
	$position = trim($_REQUEST[position]);		//직위
	$cti_num	= trim($_REQUEST[cti_num]);		//CTI 내선번호
	$duty = trim($_REQUEST[duty]);		//직책

	$tel = trim($_REQUEST[tel_1]."-".$_REQUEST[tel_2]."-".$_REQUEST[tel_3]);		//전화번호
	$com_tel = trim($_REQUEST[com_tel_1]."-".$_REQUEST[com_tel_2]."-".$_REQUEST[com_tel_3]."-".$_REQUEST[com_tel_4]);		//회사전화번호
	$pcs = trim($_REQUEST[pcs_1]."-".$_REQUEST[pcs_2]."-".$_REQUEST[pcs_3]);		//핸드폰번호
	$mail = trim($_REQUEST['mail']);		//메일주소
	$join_devision = trim($_REQUEST[join_devision]);		//입사구분 1. 신입 2. 경력
	$interest = trim($_REQUEST[interest]);		//취미
	$specialty = trim($_REQUEST[specialty]);		//특기
	$work_devision = trim($_REQUEST[work_devision]);		//재직구분
	$zip = trim($_REQUEST[zip]);		//회사 우편코드
	$addr1 = trim($_REQUEST[addr1]);		//주소1
	$addr2 = trim($_REQUEST[addr2]);		//주소2

	$r_zipcode = trim($_REQUEST[r_zipcode]);		//실 주소 우편코드
	$r_addr1 = trim($_REQUEST[r_addr1]);		//실 주소1
	$r_addr2 = trim($_REQUEST[r_addr2]);		//실 주소2

	$bank_name = trim($_REQUEST[bank_name]);		//급여은행명
	$holder_name = trim($_REQUEST[holder_name]);		//예금주명
	$bank_num = trim($_REQUEST[bank_num]);		//계좌번호
	$resign_date = trim($_REQUEST[resign_date]);		//퇴직일

	$resign_msg = trim($_REQUEST[resign_msg]);		//퇴직사유f
	$id = trim($_REQUEST[id]);		//아이디
	$pass = trim($_REQUEST[pw]);		//비밀번호
	$mem_code = trim($_REQUEST[mem_code]);		//사원코드
	$code = trim($_REQUEST[code]);		//회원코드
	$auth = trim($_REQUEST[auth]);		//사용자권한
	$mem_div = trim($_REQUEST[mem_div]);	// 셀러,MD, 기타 설정값
	$language = trim($_REQUEST[language_type]);	// 셀러,MD, 기타 설정값
	$mem_level = trim($_REQUEST[mem_level]);	// MD 레벨

	$otpkey = trim($_REQUEST[otpkey]);	// OTPKEY





if($info_type == "basic"){

	$is_id_auth = "Y";
	if(!$black_list) $black_list = 'N';

	if($befor_black_list != $black_list){
		if($black_list =="Y"){
			$black_list_type ="R";
		}else{
			$black_list_type ="C";
		}
		$sql = "insert into common_blacklist_history(ix,type,code,msg,changer,regdate) values('','$black_list_type','$code','$msg','".$admininfo[charger]."(".$admininfo[charger_id].")"."',NOW()) ";
		$db->sequences = "COMMON_BLACKLIST_HISTORY_SEQ";
		$db->query($sql);
	}

	if($cid2){
		$sql = "select
					cd.company_id,
					cd.com_type
				from
					".TBL_COMMON_COMPANY_RELATION." cr
					inner join ".TBL_COMMON_COMPANY_DETAIL." as cd on (cr.company_id = cd.company_id)
				where
					relation_code ='".$cid2."'";
		$db2->query($sql);
		$db2->fetch();

		$company_id = $db2->dt[company_id];
		$mem_type = $db2->dt[com_type];		//사업장 타입  , 사업장 사원으로등록된 회원 사업장 타입을 따라간다.
	}

	if($change_pass == "1"){
		if($pass){

			////$pass_update_str = " , pw = MD5('$pass') ";
			//$pass_update_str = " , pw = '".hash("sha256", $pass)."' ";
			$pass_update_str = " , pw = '".hash("sha256", md5($pass))."' ";

			//	비밀번호 수정 or 신규 가입시 임시패스워드 발송
			if(trim($mail) != "") {
				$ig_subject = "[임시패스워드]";
				$ig_mail_info[mem_name] = $mem_name;
				$ig_mail_info[mem_mail] = $mail;
				$ig_mail_info[mem_id] = $id;

				$ig_mail_subject = $ig_mail_info[mem_name]." 님, ".$ig_subject;
				$ig_content = "임시패스워드 : ".$pass;
				SendMail($ig_mail_info, $ig_mail_subject, $ig_content,"","","Y");


					$ig_change_pw_history_SQL = "
						INSERT INTO
							ig_change_pw_history
						SET
							code = '".$code."',
							pw_data = '".hash("sha256", md5($pass))."',
							ch_type = '1',
							regDt = '".date("Y-m-d H:i:s")."'
						";
					$db->query($ig_change_pw_history_SQL);
			}
			//	비밀번호 수정 or 신규 가입시 임시패스워드 발송

		}
	}

	$sql = "UPDATE ".TBL_COMMON_USER."  SET
				mem_type = 'A',
				id = '$id',
				language = '$language',
				company_id = '$company_id',
				authorized = '$authorized',
				otpkey = '$otpkey',
				is_pos_link = 'N', 
				join_status = 'U',
				auth = '$auth',
				mem_div = '$mem_div'
				$pass_update_str
			WHERE
				code='$code'";
	$db->query($sql);

	if($db->dbms_type == "oracle"){
			$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL."  SET
			name=AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."'),
			birthday='$birthday',
			birthday_div='$birthday_div',
			mail=AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'),
			addr1=AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."'),
			addr2=AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."'),
			zip=AES_ENCRYPT('$zip','".$db->ase_encrypt_key."'),
			tel=AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),
			com_tel=HEX(AES_ENCRYPT('$com_tel','".$db->ase_encrypt_key."')),
			pcs=AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'),
			mem_code = '$mem_code',
			join_date = '$join_date',
			nationality = '$nationality',
			married = '$married',
			com_group = '$com_group',
			department = '$department',
			position = '$position',
			cti_num	= '$cti_num',
			duty = '$duty',
			work_devision = '$work_devision',
			join_devision = '$join_devision',
			interest = '$interest',
			specialty = '$specialty',
			r_zipcode = '$r_zipcode',
			r_addr1 = '$r_addr1',
			r_addr2 = '$r_addr2',
			bank_name = '$bank_name',
			holder_name = '$holder_name',
			bank_num = '$bank_num',
			resign_date = '$resign_date',
			resign_msg = '$resign_msg',
			worker_message = '$worker_message'
			WHERE code='$code'";

	}else{

	$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL."  SET
				name=HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),
				birthday='$birthday',
				birthday_div='$birthday_div',
				mail=HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),
				addr1=HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),
				addr2=HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),
				zip=HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),
				tel=HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),
				com_tel=HEX(AES_ENCRYPT('$com_tel','".$db->ase_encrypt_key."')),
				pcs=HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),
				mem_code = '$mem_code',
				join_date = '$join_date',
				nationality = '$nationality',
				married = '$married',
				com_group = '$com_group',
				department = '$department',
				position = '$position',
				cti_num	= '$cti_num',
				duty = '$duty',
				work_devision = '$work_devision',
				join_devision = '$join_devision',
				interest = '$interest',
				specialty = '$specialty',
				r_zipcode = '$r_zipcode',
				r_addr1 = '$r_addr1',
				r_addr2 = '$r_addr2',
				bank_name = '$bank_name',
				holder_name = '$holder_name',
				bank_num = '$bank_num',
				resign_date = '$resign_date',
				resign_msg = '$resign_msg',
				worker_message = '$worker_message'
			WHERE
				code='$code'";
	}

	$db->query($sql);

	// 이미지 저장 시작 
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;

	if(!is_dir($path)){
		//mkdir($path, 0777);
		exec("mkdir -m 777 ".$path);	//이미지 폴더 생성
	}

	if ($fine_file_size > 0){
		copy($fine_file,$path."/"."member_".$code.".gif");
		$company_stamp_str = $admin_config[mall_data_root]."/images/stamps/member_".$code.".gif";
	}
	// 이미지 저장 끝


}else if($info_type == "f_info"){	//가족사항
	
	$f_info = $_REQUEST[f_info];
	$code  = $_REQUEST[code];
	for($i = 0; $i<count($f_info); $i++){
		if($f_info[$i][family_ix]){
			$sql = "update common_worker_family set
					f_connection = '".$f_info[$i][f_connection]."',
					f_name = '".$f_info[$i][f_name]."',
					f_brithday = '".$f_info[$i][f_brithday]."',
					f_job = '".$f_info[$i][f_job]."',
					f_contact = '".$f_info[$i][f_contact]."',
					f_note = '".$f_info[$i][f_note]."',
					edit_date = NOW()
				where
					family_ix = '".$f_info[$i][family_ix]."'
			";
			$db->query($sql);
		}else{
			if($f_info[$i][f_connection] or $f_info[$i][f_name] or $f_info[$i][f_brithday] or $f_info[$i][f_job] or $f_info[$i][f_contact] or $f_info[$i][f_note]){
				$sql = "insert into common_worker_family set
						code = '".$code."',
						f_connection = '".$f_info[$i][f_connection]."',
						f_name = '".$f_info[$i][f_name]."',
						f_brithday = '".$f_info[$i][f_brithday]."',
						f_job = '".$f_info[$i][f_job]."',
						f_contact = '".$f_info[$i][f_contact]."',
						f_note = '".$f_info[$i][f_note]."',
						reg_date = NOW()
				";
				//echo "$sql";
				$db->query($sql);
			}
		}
	}


}else if($info_type == "s_info"){	//학력/이력

	$s_info = $_REQUEST[s_info];
	$code  = $_REQUEST[code];
	
	$sql = "select company_id from common_user where code = '".$code."'";
	$db->query($sql);
	$db->fetch();
	$company_id = $db->dt[company_id];

	// 이미지 저장 시작 
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;

	if(!is_dir($path)){
		//mkdir($path, 0777);
		exec("mkdir -m 777 ".$path);	//이미지 폴더 생성
	}

	for($i = 0; $i<count($s_info); $i++){
		if($s_info[$i][school_ix]){
			$sql = "update common_worker_school set
					ac_st_date = '".$s_info[$i][ac_st_date]."',
					ac_end_date = '".$s_info[$i][ac_end_date]."',
					ac_school = '".$s_info[$i][ac_school]."',
					ac_department = '".$s_info[$i][ac_department]."',
					ac_division = '".$s_info[$i][ac_division]."',
					edit_date = NOW()
				where
					school_ix = '".$s_info[$i][school_ix]."'";
			$db->query($sql);
			$school_ix = $s_info[$i][school_ix];

		}else{
			if($s_info[$i][ac_date] or $s_info[$i][ac_school] or $s_info[$i][ac_department] or $s_info[$i][ac_division]){
				$sql = "insert into common_worker_school set
						code = '".$code."',
						school_ix = '".$s_info[$i][school_ix]."',
						ac_st_date = '".$s_info[$i][ac_st_date]."',
						ac_end_date = '".$s_info[$i][ac_end_date]."',
						ac_school = '".$s_info[$i][ac_school]."',
						ac_department = '".$s_info[$i][ac_department]."',
						ac_division = '".$s_info[$i][ac_division]."',
						reg_date = NOW()";
				$db->query($sql);
				$school_ix = $db->insert_id();
			}
		}

		if($school_ix){
			
			//이미지 저장 시작 
			if($_FILES["s_info_".$i][size] > 0){
				$filname= "member_sc_".$i."_".$school_ix."_".$code.".gif";
				$db->query("update common_worker_school set ac_proof_file = '".$filname."' where school_ix = '".$school_ix."' ");

				$copy_file = "s_info_".$i;
				copy($$copy_file,$path."/".$filname);
			}
		}

	}

	$r_info = $_REQUEST[r_info];
	$code  = $_REQUEST[code];
	for($i = 0; $i<count($r_info); $i++){
		if($r_info[$i][resume_ix]){
			$sql = "update common_worker_resume set
					record_st_date = '".$r_info[$i][record_st_date]."',
					record_end_date = '".$r_info[$i][record_end_date]."',
					company_name = '".$r_info[$i][company_name]."',
					department = '".$r_info[$i][department]."',
					duty = '".$r_info[$i][duty]."',
					edit_date = NOW()
				where
					resume_ix = '".$r_info[$i][resume_ix]."'";

			$db->query($sql);
			$resume_ix = $r_info[$i][resume_ix];
		}else{
			if($r_info[$i][record_st_date] or $r_info[$i][company_name] or $r_info[$i][department] or $r_info[$i][duty]){
				$sql = "insert into common_worker_resume set
						code = '".$code."',
						record_st_date = '".$r_info[$i][record_st_date]."',
						record_end_date = '".$r_info[$i][record_end_date]."',
						company_name = '".$r_info[$i][company_name]."',
						department = '".$r_info[$i][department]."',
						duty = '".$r_info[$i][duty]."',
						reg_date = NOW()";

				$db->query($sql);
				$resume_ix = $db->insert_id();
			}
		}

		if($resume_ix){
			//이미지 저장 시작 
			if($_FILES["r_info_".$i][size] > 0){
				$filname= "member_rc_".$i."_".$resume_ix."_".$code.".gif";

				$db->query("update common_worker_resume set record_file = '".$filname."' where resume_ix = '".$resume_ix."'");
				$copy_file = "r_info_".$i;
				copy($$copy_file,$path."/".$filname);
			}
		}
	}

}else if($info_type == "j_info"){	//경력사항

	$p_info = $_REQUEST[p_info];
	$code  = $_REQUEST[code];

	$sql = "select company_id from common_user where code = '".$code."'";
	$db->query($sql);
	$db->fetch();
	$company_id = $db->dt[company_id];

	// 이미지 저장 시작 
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;

	if(!is_dir($path)){
		//mkdir($path, 0777);
		exec("mkdir -m 777 ".$path);	//이미지 폴더 생성
	}

	for($i = 0; $i<count($p_info); $i++){
		if($p_info[$i][project_ix]){
			$sql = "update common_worker_project  set
					project_name = '".$p_info[$i][project_name]."',
					project_st_date = '".$p_info[$i][project_st_date]."',
					project_end_date = '".$p_info[$i][project_end_date]."',
					project_work = '".$p_info[$i][project_work]."',
					project_order = '".$p_info[$i][project_order]."',
					edit_date = NOW()
				where
					project_ix = '".$p_info[$i][project_ix]."'";
			$db->query($sql);
			$project_ix = $p_info[$i][project_ix];

		}else{
			if($p_info[$i][project_name] or $p_info[$i][project_st_date] or $p_info[$i][project_end_date] or $p_info[$i][project_work] or $p_info[$i][project_order]){
				$sql = "insert into common_worker_project  set
						code = '".$code."',
						project_name = '".$p_info[$i][project_name]."',
						project_st_date = '".$p_info[$i][project_st_date]."',
						project_end_date = '".$p_info[$i][project_end_date]."',
						project_work = '".$p_info[$i][project_work]."',
						project_order = '".$p_info[$i][project_order]."',
						reg_date = NOW()";

				$db->query($sql);
				$project_ix = $db->insert_id();
			}
		}


		if($project_ix){

			//이미지 저장 시작 
			if($_FILES["p_info_".$i][size] > 0){
				$filname= "member_pc_".$i."_".$project_ix."_".$code.".gif";

				$db->query("update common_worker_project  set project_file = '".$filname."' where project_ix = '".$project_ix."'");
				$copy_file = "p_info_".$i;
				copy($$copy_file,$path."/".$filname);
			}
		}
	}


}else if($info_type == "z_info"){	//자격/면허

	$z_info = $_REQUEST[z_info];
	$code  = $_REQUEST[code];

	$sql = "select company_id from common_user where code = '".$code."'";
	$db->query($sql);
	$db->fetch();
	$company_id = $db->dt[company_id];

	// 이미지 저장 시작 
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;

	if(!is_dir($path)){
		//mkdir($path, 0777);
		exec("mkdir -m 777 ".$path);	//이미지 폴더 생성
	}

	for($i = 0; $i<count($z_info); $i++){
		if($z_info[$i][certificate_ix]){
			$sql = "update common_worker_certificate  set
					certificate_name = '".$z_info[$i][certificate_name]."',
					cert_st_date = '".$z_info[$i][cert_st_date]."',
					cert_end_date = '".$z_info[$i][cert_end_date]."',
					cert_num = '".$z_info[$i][cert_num]."',
					get_date = '".$z_info[$i][get_date]."',
					edit_date = NOW()
				where
					certificate_ix = '".$z_info[$i][certificate_ix]."'
			";
			//echo "$sql";
			$db->query($sql);
			$certificate_ix = $z_info[$i][certificate_ix];
		}else{
			if($z_info[$i][certificate_name] or $z_info[$i][cert_st_date] or $z_info[$i][cert_end_date] or $z_info[$i][cert_num] or $z_info[$i][get_date]){
				$sql = "insert into common_worker_certificate  set
						code = '".$code."',
						certificate_name = '".$z_info[$i][certificate_name]."',
						cert_st_date = '".$z_info[$i][cert_st_date]."',
						cert_end_date = '".$z_info[$i][cert_end_date]."',
						cert_num = '".$z_info[$i][cert_num]."',
						get_date = '".$z_info[$i][get_date]."',
						reg_date = NOW()
				";
				//echo "$sql";
				$db->query($sql);
				$certificate_ix = $db->insert_id();
			}
		}

		if($certificate_ix){

			//이미지 저장 시작 
			if($_FILES["z_info_".$i][size] > 0){
				$filname= "member_zc_".$i."_".$certificate_ix."_".$code.".gif";

				$db->query("update common_worker_certificate set cert_file = '".$filname."' where certificate_ix = '".$certificate_ix."'");
				$copy_file = "z_info_".$i;
				copy($$copy_file,$path."/".$filname);
			}
		}

	}
}
	

		//	ig_관리자 권환 관련 로그
			if(trim($auth) != "") {
				$ig_code		= $code;								//	회원코드
				$ig_ip			= $_SERVER['REMOTE_ADDR'];				//	ip
				$ig_act_code	= $_SESSION["admininfo"]["charger_ix"];	//	발급자 회원 코드
				$ig_act			= $act;		//	생성/수정/삭제 구분
				$ig_auth		= $auth;		//	권한
				$ig_addinfo1	= "";		//	신청 및 발급 사유
					switch($ig_act) {
						case "insert":
							$ig_addinfo1	= "계정 생성 및 권한 부여";
						break;

						case "update":
							$ig_addinfo1	= "권한 수정";
						break;

						case "delete":
							$ig_addinfo1	= "계정 삭제";
						break;
					}

				$ig_addinfo2	= "";		//	여분필드
				$ig_addinfo3	= "";		//	여분필드
				$ig_regDt		= date("Y-m-d H:i:s");		//	등록/수정 일시

					$ig_auth_history_SQL = "
						INSERT INTO
							ig_auth_history
						SET
							code		= '".$ig_code."',
							ip			= '".$ig_ip."',
							act_code	= '".$ig_act_code."',
							act			= '".$ig_act."',
							auth		= '".$ig_auth."',
							addinfo1	= '".$ig_addinfo1."',
							addinfo2	= '".$ig_addinfo2."',
							addinfo3	= '".$ig_addinfo3."',
							regDt		= '".$ig_regDt."'
					";
					$ig_db->query($ig_auth_history_SQL);
			}
		//	//ig_관리자 권환 관련 로그

	if($url == 'store'){
		echo("<script language='javascript' src='../js/message.js.php'></script>
		<script>show_alert('회원정보가 정상적으로 수정되었습니다.');top.location.href = '../store/admin_manage.php?code=".$code."';</script>");
		echo("<script>parent.location.reload();</script>");
	}else{
		echo("<script language='javascript' src='../js/message.js.php'></script>
		<script>show_alert('회원정보가 정상적으로 수정되었습니다.');top.location.href = 'member.add.php?code=".$code."&mmode=".$mmode."&info_type=".$info_type."';</script>");
		echo("<script>parent.location.reload();</script>");
	}

}

if ($act == "delete")
{
	$db->query("select company_id from ".TBL_COMMON_USER."  WHERE code='$code'");
	$db->fetch();
	$company_id = $db->dt[company_id];


	$db->query("DELETE FROM ".TBL_COMMON_MEMBER_DETAIL."  WHERE code='$code'");
	$db->query("DELETE FROM ".TBL_COMMON_USER."  WHERE code='$code'");
	$db->query("DELETE FROM ".TBL_COMMON_DROPMEMBER."  WHERE code='$code'");

		//	ig_관리자 권환 관련 로그
				$ig_code		= $code;								//	회원코드
				$ig_ip			= $_SERVER['REMOTE_ADDR'];				//	ip
				$ig_act_code	= $_SESSION["admininfo"]["charger_ix"];	//	발급자 회원 코드
				$ig_act			= $act;		//	생성/수정/삭제 구분
				$ig_auth		= $auth;		//	권한
				$ig_addinfo1	= "";		//	신청 및 발급 사유
					switch($ig_act) {
						case "insert":
							$ig_addinfo1	= "계정 생성 및 권한 부여";
						break;

						case "update":
							$ig_addinfo1	= "권한 수정";
						break;

						case "delete":
							$ig_addinfo1	= "계정 삭제";
						break;
					}

				$ig_addinfo2	= "";		//	여분필드
				$ig_addinfo3	= "";		//	여분필드
				$ig_regDt		= date("Y-m-d H:i:s");		//	등록/수정 일시

					$ig_auth_history_SQL = "
						INSERT INTO
							ig_auth_history
						SET
							code		= '".$ig_code."',
							ip			= '".$ig_ip."',
							act_code	= '".$ig_act_code."',
							act			= '".$ig_act."',
							auth		= '".$ig_auth."',
							addinfo1	= '".$ig_addinfo1."',
							addinfo2	= '".$ig_addinfo2."',
							addinfo3	= '".$ig_addinfo3."',
							regDt		= '".$ig_regDt."'
					";
					$ig_db->query($ig_auth_history_SQL);
		//	//ig_관리자 권환 관련 로그


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('회원정보가 정상적으로 삭제되었습니다.','top_reload');</script>");
	exit;
}

if ($act == "mem_talk_insert")
{

	$db->sequences = "SHOP_MEMBER_TALK_HISTORY_SEQ";
	$db->query("insert into ".TBL_SHOP_MEMBER_TALK_HISTORY." (ta_ix,m_code,ta_memo,ta_counselor,regdate) values('','$code','$ta_memo','$ta_counselor',NOW())");

	echo("<script>top.location.href = 'member_view.php?code=".$code."';</script>");
}

if ($act == "mem_talk_delete")
{
	$db->query("DELETE FROM ".TBL_SHOP_MEMBER_TALK_HISTORY."  WHERE ta_ix='$ta_ix'");
	//echo("<script>top.location.href = 'member.php?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."';</script>");
	echo("<script>top.location.href = 'member_view.php?code=".$code."';</script>");
}

if ($act == "member_black_list_n")
{

	$db->query("update ".TBL_COMMON_MEMBER_DETAIL." set black_list='N'  WHERE code='$code'");

	$sql = "insert into common_blacklist_history(ix,type,code,msg,changer,regdate) values('','C','$code','불량고객 리스트에서 해제','".$admininfo[charger]."(".$admininfo[charger_id].")"."',NOW()) ";
	$db->sequences = "COMMON_BLACKLIST_HISTORY_SEQ";
	$db->query($sql);

	echo("<script>top.location.reload();</script>");
}

if($act == "select_member_black_list_n")
{

	for($i=0;$i<count($code);$i++){

		$db->query("update ".TBL_COMMON_MEMBER_DETAIL." set black_list='N'  WHERE code='".$code[$i]."'");

		$sql = "insert into common_blacklist_history(ix,type,code,msg,changer,regdate) values('','C','".$code[$i]."','불량고객 리스트에서 일괄해제','".$admininfo[charger]."(".$admininfo[charger_id].")"."',NOW()) ";
		$db->sequences = "COMMON_BLACKLIST_HISTORY_SEQ";
		$db->query($sql);
	}

	echo("<script>top.location.reload();</script>");
}


if($act == "dropmember_delete")
{
	$db->query("DELETE FROM ".TBL_COMMON_MEMBER_DETAIL."  WHERE code='$code'");
	$db->query("DELETE FROM ".TBL_COMMON_DROPMEMBER."  WHERE code='$code'");
	echo("<script>top.location.href = 'dropmember.php';</script>");
}



//사원등록 시작 2014-06-11 보완작업
if($act == 'insert'){

	if($url == 'store'){
		$use_id = $id;
	}else{
		$use_id = $id;
	}

	$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id='$use_id'");
	$total = $db->total;
	if ($total > 0){
		echo("<script>alert('이미 등록된 [아이디]입니다.');</script>");
		if($url == 'store'){
			echo("<script>top.location.href = '../store/md_manage_list.php';</script>");
		}else{
			echo("<script>top.location.href = 'member.add.php?code=".$code."';</script>");
		}
		exit;
	}

	$join_date = trim($_REQUEST[join_date]);		//입사일
	$nationality = trim($_REQUEST[nationality]);		//국내외구분 I - 내국인 O - 외국인
	$mem_name = trim($_REQUEST[mem_name]);		//이름
	$birthday_div = trim($_REQUEST[birthday_div]);		//음력/양력	1 - 양력 0 - 음력
	$birthday = trim($_REQUEST[birthday]);		//생일
	$married = trim($_REQUEST[married]);		//Y:기혼,N:미혼
	$com_group = trim($_REQUEST[com_group]);		//부서그룹코드
	$department = trim($_REQUEST[department]);		//부서코드
	$position = trim($_REQUEST[position]);		//직위
	$duty = trim($_REQUEST[duty]);		//직책
	$cti_num = trim($_REQUEST[cti_num]);		//CTI 내선번호
	$tel = trim($_REQUEST[tel_1]."-".$_REQUEST[tel_2]."-".$_REQUEST[tel_3]);		//전화번호
	$com_tel = trim($_REQUEST[com_tel_1]."-".$_REQUEST[com_tel_2]."-".$_REQUEST[com_tel_3]."-".$_REQUEST[com_tel_4]);		//회사전화번호
	$pcs = trim($_REQUEST[pcs_1]."-".$_REQUEST[pcs_2]."-".$_REQUEST[pcs_3]);		//핸드폰번호

	$mail = trim($_REQUEST['mail']);		//메일주소
	$join_devision = trim($_REQUEST[join_devision]);		//입사구분 1. 신입 2. 경력
	$interest = trim($_REQUEST[interest]);		//취미
	$specialty = trim($_REQUEST[specialty]);		//특기
	$work_devision = trim($_REQUEST[work_devision]);		//재직구분
	$zip = trim($_REQUEST[zip]);		//회사 우편코드
	$addr1 = trim($_REQUEST[addr1]);		//주소1
	$addr2 = trim($_REQUEST[addr2]);		//주소2

	$r_zipcode = trim($_REQUEST[r_zipcode]);		//실 주소 우편코드
	$r_addr1 = trim($_REQUEST[r_addr1]);		//실 주소1
	$r_addr2 = trim($_REQUEST[r_addr2]);		//실 주소2

	$bank_name = trim($_REQUEST[bank_name]);		//급여은행명
	$holder_name = trim($_REQUEST[holder_name]);		//예금주명
	$bank_num = trim($_REQUEST[bank_num]);		//계좌번호
	$resign_date = trim($_REQUEST[resign_date]);		//퇴직일

	$resign_msg = trim($_REQUEST[resign_msg]);		//퇴직사유
	$id = trim($_REQUEST[id]);		//아이디
	$pass = trim($_REQUEST[pw]);		//비밀번호
	$pw_confirm = trim($_REQUEST[pw_confirm]);		//비밀번호확인
	$mem_code = trim($_REQUEST[mem_code]);		//사원코드
	$auth = trim($_REQUEST[auth]);		//사용자권한
	$mem_div = trim($_REQUEST[mem_div]);	// 셀러,MD, 기타 설정값
	$is_id_auth = "Y";						//사용자계정승인
	//$mem_level = trim($_REQUEST[mem_level]);	//md 레벨
	$language = trim($_REQUEST[language_type]);	// 셀러,MD, 기타 설정값
	$gp_ix = '1';	//직원 1
	$level_ix = '1';	//직원은 일반회원1로
	$mem_type = 'A';	//사원은 직원	M:일반회원 C:사업자회원 A:직원

	if($pass){	//비밀번호 암호화
		//$pass_update_str = hash("sha256", $pass);
		$pass_update_str = hash("sha256", md5($pass)); //cafe24 비밀번호 해시
	}

	$code  = md5(uniqid(rand()));	//회원코드 생성

	if($cid2){
		$sql = "select
					cd.company_id,
					cd.com_type
				from
					".TBL_COMMON_COMPANY_RELATION." cr
					inner join ".TBL_COMMON_COMPANY_DETAIL." as cd on (cr.company_id = cd.company_id)
				where
					relation_code ='".$cid2."'";
		$db->query($sql);
		$db->fetch();
		$company_id = $db->dt[company_id];
	}

	if($db->dbms_type == "oracle"){		// 사원 등록시 mem_type : A(직원-관리자) mem_div : S : 셀러 MD: 담당 MD  기타 : D
		$sql = "INSERT INTO ".TBL_COMMON_USER."
				(code, id, pw, mem_type, mem_div, date_, visit, last, ip, company_id,language, authorized, auth,is_pos_link, join_status,is_id_auth)
				VALUES
				('".$code."','".$id."','".$pass_update_str."','".$mem_type."','".$mem_div."',NOW(),'0',NOW(),'".$REMOTE_ADDR."','".$company_id."','".$language."','".$authorized."','".$auth."','N', 'I','".$is_id_auth."')";
		$db->query($sql);
	}else{
		$sql = "INSERT INTO ".TBL_COMMON_USER."
				(code, id, pw, mem_type, mem_div, date, visit, last, ip, company_id,language, authorized, auth,is_pos_link, join_status,is_id_auth)
				VALUES
				('".$code."','".$id."','".$pass_update_str."','".$mem_type."','".$mem_div."',NOW(),'0',NOW(),'".$REMOTE_ADDR."','".$company_id."','".$language."','".$authorized."','".$auth."','N', 'I', '".$is_id_auth."')";
		$db->query($sql);
	}
	
	if($db->dbms_type == "oracle"){
		$sql_detail = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date_, recom_id, gp_ix,  sex_div,married,nationality,join_devision,interest,specialty,join_date,r_zipcode,r_addr1,r_addr2,work_devision,bank_name,holder_name,bank_num,resign_msg,resign_date,worker_message,com_group,department,duty,position,mem_code,com_tel,mem_level,level_ix)
					VALUES
					('$code',AES_ENCRYPT('".$db->ase_encrypt_key."'),'$birthday','$birthday_div',AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."'),AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'),AES_ENCRYPT('$zip','".$db->ase_encrypt_key."'),AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."'),AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."'),AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),'$tel_div',AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div','$married','$nationality','$join_devision','$interest','$specialty','$join_date','$r_zipcode','$r_addr1','$r_addr2','$work_devision','$bank_name','$holder_name','$bank_num','$resign_msg','$resign_date','$worker_message','$com_group','$department','$duty','$position','$mem_code',HEX(AES_ENCRYPT('$com_tel','".$db->ase_encrypt_key."')),'$mem_level','$level_ix')";
		$db->query($sql_detail);
	}else{
		$sql_detail = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,married,nationality,join_devision,interest,specialty,join_date,r_zipcode,r_addr1,r_addr2,work_devision,bank_name,holder_name,bank_num,resign_msg,resign_date,worker_message,com_group,department,duty,position,cti_num,mem_code,com_tel,mem_level,level_ix)
					VALUES
					('$code','$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div','$married','$nationality','$join_devision','$interest','$specialty','$join_date','$r_zipcode','$r_addr1','$r_addr2','$work_devision','$bank_name','$holder_name','$bank_num','$resign_msg','$resign_date','$worker_message','$com_group','$department','$duty','$position','$cti_num','$mem_code',HEX(AES_ENCRYPT('$com_tel','".$db->ase_encrypt_key."')),'$mem_level','$level_ix')";
		$db->query($sql_detail);
	}



		//	ig_관리자 권환 관련 로그
			if(trim($auth) != "") {
				$ig_code		= $code;								//	회원코드
				$ig_ip			= $_SERVER['REMOTE_ADDR'];				//	ip
				$ig_act_code	= $_SESSION["admininfo"]["charger_ix"];	//	발급자 회원 코드
				$ig_act			= $act;		//	생성/수정/삭제 구분
				$ig_auth		= $auth;		//	권한
				$ig_addinfo1	= "";		//	신청 및 발급 사유
					switch($ig_act) {
						case "insert":
							$ig_addinfo1	= "계정 생성 및 권한 부여";
						break;

						case "update":
							$ig_addinfo1	= "권한 수정";
						break;

						case "delete":
							$ig_addinfo1	= "계정 삭제";
						break;
					}

				$ig_addinfo2	= "";		//	여분필드
				$ig_addinfo3	= "";		//	여분필드
				$ig_regDt		= date("Y-m-d H:i:s");		//	등록/수정 일시

					$ig_auth_history_SQL = "
						INSERT INTO
							ig_auth_history
						SET
							code		= '".$ig_code."',
							ip			= '".$ig_ip."',
							act_code	= '".$ig_act_code."',
							act			= '".$ig_act."',
							auth		= '".$ig_auth."',
							addinfo1	= '".$ig_addinfo1."',
							addinfo2	= '".$ig_addinfo2."',
							addinfo3	= '".$ig_addinfo3."',
							regDt		= '".$ig_regDt."'
					";
					$ig_db->query($ig_auth_history_SQL);
			}
		//	//ig_관리자 권환 관련 로그


	// 이미지 저장 시작 
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;
	if(!is_dir($path)){
		exec("mkdir -m 777 ".$path);	//이미지 폴더 생성
	}

	if ($fine_file_size > 0){
		copy($fine_file,$path."/"."member_".$code.".gif");
		$company_stamp_str = $admin_config[mall_data_root]."/images/stamps/member_".$code.".gif";
	}
	// 이미지 저장 끝



		//	비밀번호 수정 or 신규 가입시 임시패스워드 발송
		if(trim($mail) != "") {
			$ig_subject = "[임시패스워드]";
			$ig_mail_info[mem_name] = $mem_name;
			$ig_mail_info[mem_mail] = $mail;
			$ig_mail_info[mem_id] = $id;

			$ig_mail_subject = $ig_mail_info[mem_name]." 님, ".$ig_subject;
			$ig_content = "임시패스워드 : ".$pass;
			SendMail($ig_mail_info, $ig_mail_subject, $ig_content,"","","Y");

					$ig_change_pw_history_SQL = "
						INSERT INTO
							ig_change_pw_history
						SET
							code = '".$code."',
							pw_data = '".hash("sha256", md5($pass))."',
							ch_type = '1',
							regDt = '".date("Y-m-d H:i:s")."'
						";
					$db->query($ig_change_pw_history_SQL);
		}
		//	비밀번호 수정 or 신규 가입시 임시패스워드 발송



	if($url == 'store'){
		echo("<script language='javascript' src='../js/message.js.php'></script>
		<script>show_alert('MD등록이 정상적으로 처리되었습니다.');top.location.href = '../store/md_manage.php?code=".$code."';</script>");
		//echo("<script>parent.location.reload();</script>");
	}else{
		echo("<script>alert('정상적으로 처리되었습니다.');top.location.href = 'member.add.php?code=".$code."';</script>");
	}

}

if ($act == "idcheck")
{
	$deny_id = array("test","admin", "master", "police", "webmaster", "help", "cancel", "service", "avarta", "regist", "pay", "administrator", "fuck", "sex", "roopy", "center", "email", "monitor", "helpdesk", "doumi","helpdesk", "operate", "operator", "message", "menu", "member", "poll", "point", "communication", "comment", "manager", "management", "plan", "planning", "partner", "board", "notice", "dosirak", "dosirack", "naraadmin", "http", "ftp", "telnet", "administrator", "root", "www", "widget", "trackback", "tag", "spamfilter", "session", "rss", "page", "opage", "module", "layout", "krzip", "integration_search", "install", "importer", "file", "editor", "document", "counter", "autoinstall", "addon");

	$db->query("select mall_deny_id from shop_shopinfo where mall_ix = '".$admin_config[mall_ix]."' and mall_div = '".$admin_config[mall_div]."' ");
	$db->fetch();

	$deny_id_add = explode(",",$db->dt[mall_deny_id]);
	$deny_id = array_merge((array)$deny_id,(array)$deny_id_add);
	for($i=0;$i<count($deny_id);$i++){
		if(trim($id) == $deny_id[$i]){
			//echo "<script>alert('가입불가 ID입니다. 다른 ID로 입력해주시기 바랍니다.')</script>";
			echo "X";
			exit;
		}
	}

	$total = 0;

	$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id='$id' and mem_type in ('M','C','F','S','A','MD','CS','HO','BR','BP','BO') ");
	$total = $db->total;
	$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id='$id' ");
	$total = $total + $db->total;

	if ($total)
	{
		//echo("<script>alert('이미 등록된 [아이디]입니다.');</script>");
		//echo("<script>top.join_frm.id.dup_check = false;</script>");
		echo "N";
	}
	else
	{
		//echo("<script>alert('사용 가능한 [아이디]입니다.');</script>");
		//echo("<script>top.join_frm.id.dup_check = true;</script>");
		echo "Y";
	}
}

?>
