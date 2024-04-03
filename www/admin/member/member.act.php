<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include("../basic/company.lib.php");

	include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");

$db = new Database;
$db2 = new Database;

/////////////////////////////////////////////////////////////////////////////////////////////
$refererParts = explode('/', $_SERVER['HTTP_REFERER']);


function getNewPassword($password, $userId){

    $makeshop_id = 'dewytree1';
    //$new_pass = hash("sha512", md5($password) . $makeshop_id . $userId);
    $new_pass = hash('sha256', md5($password));

    return $new_pass;
}



if($refererParts[3] != 'admin'){
    echo "<script>
			 alert('비정상적인 접근입니다.');
		  </script>";
    exit;
}
/////////////////////////////////////////////////////////////////////////////////////////////
if ($act == "update"){

	$name  = trim($name);
    $first_name  = trim($first_name);
    $last_name  = trim($last_name);
    $first_kana  = trim($first_kana);
    $last_kana  = trim($last_kana);

	$pass  = trim($pass);
	$mail  = trim($mail);
	$comp  = trim($comp);
	$class = trim($class);
	$addr1 = trim($addr1);
	$addr2 = trim($addr2);
	if($zip2 != "")
		$zip   = "$zip1-$zip2";
	else
		$zip = $zip1;

	if($tel2 != "")
		$tel   = "$tel1-$tel2-$tel3";
	else
		$tel = $tel1;

	if($pcs2 != "")
		$pcs   = "$pcs1-$pcs2-$pcs3";
	else
		$pcs = $pcs1;

	if($mail_auth == "Y"){
		$is_id_auth = "Y";
	}else{
		$is_id_auth = "N";
	}

	$com_number = "$com_num1-$com_num2-$com_num3";
	$com_phone = "$com_phone1-$com_phone2-$com_phone3";
	$com_mobile = "$com_mobile1-$com_mobile2-$com_mobile3";
	$corporate_number = "$corporate_number_1-$corporate_number_2-$corporate_number_3";
	$com_fax = "$com_fax1-$com_fax2-$com_fax3";
	$com_zip   = "$com_zip";
    $com_homepage = "$com_homepage";
	$com_email = "$com_email";
	$birthday = $birthday_yyyy."-".$birthday_mm."-".$birthday_dd;


		if($change_pass == "1"){
				if($pass){

					// 패스워드변경1
					$new_pass = getNewPassword($pass, $user_id_hide);
					$pass_update_str = " , pw = '".$new_pass."' ";
				}
		}


	if($info_type == "member"){

		$sql="select field from shop_join_info where disp = 'Y' and field like 'add_etc%' order by vieworder";
		$db2->query($sql);
		$add_fetch=$db2->fetchall();
		$cnt_add_fetch=count($add_fetch);
		$add_where_add_fetch="";
		for($i=0;$i<$cnt_add_fetch;$i++) {
			$add_where_add_fetch.=",".$add_fetch[$i]["field"]." ='".${$add_fetch[$i]["field"]}."' ";
		}// 추가되는 항목에 대해서만 값을 입력함 kbk

		//회원정보 수정 히스토리 쌓기 2013-12-02 이학봉
		$compare_value[0] = array("input_name"=>"name", "column_name"=>"name", "name_text"=>"회원명");
		$compare_value[1] = array("input_name"=>"gp_ix", "column_name"=>"gp_ix", "name_text"=>"회원그룹");
		$compare_value[2] = array("input_name"=>"mem_type", "column_name"=>"mem_type", "name_text"=>"회원구분");
		$compare_value[3] = array("input_name"=>"level_ix", "column_name"=>"level_ix", "name_text"=>"회원레벨");
		$compare_value[4] = array("input_name"=>"level_msg", "column_name"=>"level_msg", "name_text"=>"회원레벨 메세지");
		$compare_value[5] = array("input_name"=>"sex_div", "column_name"=>"sex_div", "name_text"=>"성별");
		$compare_value[6] = array("input_name"=>"mem_card", "column_name"=>"mem_card", "name_text"=>"회원카드번호");
		$compare_value[7] = array("input_name"=>"birthday_yyyy", "column_name"=>"birthday", "name_text"=>"생일");
		$compare_value[8] = array("input_name"=>"birthday_div", "column_name"=>"birthday_div", "name_text"=>"음력/양력");
		$compare_value[9] = array("input_name"=>"pcs1", "column_name"=>"pcs", "name_text"=>"휴대폰번호");
		$compare_value[10] = array("input_name"=>"sms", "column_name"=>"sms", "name_text"=>"문자메세지수신여부");
		$compare_value[11] = array("input_name"=>"tel1", "column_name"=>"tel", "name_text"=>"전화번호");
		$compare_value[12] = array("input_name"=>"mail", "column_name"=>"mail", "name_text"=>"메일");
		$compare_value[13] = array("input_name"=>"info", "column_name"=>"info", "name_text"=>"메일수신여부");
		$compare_value[14] = array("input_name"=>"zip1", "column_name"=>"zip", "name_text"=>"우편번호");
		$compare_value[15] = array("input_name"=>"addr1", "column_name"=>"addr1", "name_text"=>"주소1");
		$compare_value[16] = array("input_name"=>"addr2", "column_name"=>"addr2", "name_text"=>"주소2");
		$compare_value[17] = array("input_name"=>"authorized", "column_name"=>"authorized", "name_text"=>"승인여부");
		$compare_value[18] = array("input_name"=>"nick_name", "column_name"=>"nick_name", "name_text"=>"닉네임");
		$compare_value[19] = array("input_name"=>"job", "column_name"=>"job", "name_text"=>"직업");
		$compare_value[19] = array("input_name"=>"pass", "column_name"=>"pw", "name_text"=>"비밀번호");

		//20160407
		$compare_value[19] = array("input_name"=>"customs_clearance_number", "column_name"=>"customs_clearance_number", "name_text"=>"통관고유번호");

		$compare_value[20] = array("input_name"=>"collection", "column_name"=>"collection", "name_text"=>"개인정보 수신여부");

	
		$sql = "select
					cu.authorized,
					cu.mem_type,
					cu.mem_div,
					cu.pw,
					cmd.gp_ix,
					cmd.level_ix,
					cmd.level_msg,
					cmd.mem_card,
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
					cmd.job,
					cmd.nick_name,
					replace(cmd.birthday,'-','') as birthday,
					if(cmd.nationality = 'I','0','1') as nationality,
					cmd.sex_div,
					AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
					cmd.info,
					cmd.collection, 
					replace(AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."'),'-','') as zip,
					AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1,
					AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2,
					cmd.pcs_div,
					replace(AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."'),'-','') as pcs,
					cmd.sms,
					replace(AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."'),'-','') as tel,
					cmd.birthday_div,
					cmd.voucher_div,
					replace(cmd.voucher_num_div,'-','') as voucher_num_div,
					replace(cmd.voucher_phone,'-','') as voucher_phone,
					cmd.phone_voucher_name,
					replace(cmd.expense_num,'-','') as expense_num
				from
					common_user as cu 
					inner join common_member_detail as cmd on (cu.code = cmd.code)
				where
					cu.code = '".$code."'";

		$db->query($sql);
		$db->fetch();
		$db_value = $db->dt;

		for($i=0;$i<count($compare_value);$i++){
            $db_value[$compare_value[$i][column_name]] = addslashes($db_value[$compare_value[$i][column_name]]);
			if($compare_value[$i][input_name] == 'zip1'){
				if($_POST[zip1].$_POST[zip2] != $db_value[$compare_value[$i][column_name]]){

					member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[zip1]."@".$_POST[zip2],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);

				}
			}else if($compare_value[$i][input_name] == 'pcs1'){
				if($_POST[pcs1].$_POST[pcs2].$_POST[pcs3] != $db_value[$compare_value[$i][column_name]]){

					member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[pcs1].$_POST[pcs2].$_POST[pcs3],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);

				}
			}else if($compare_value[$i][input_name] == 'tel1'){
				if($_POST[tel1].$_POST[tel2].$_POST[tel3] != $db_value[$compare_value[$i][column_name]]){

					member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[tel1].$_POST[tel2].$_POST[tel3],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);

				}
			}else if($compare_value[$i][input_name] == 'birthday_yyyy'){
				if($_POST[birthday_yyyy].$_POST[birthday_mm].$_POST[birthday_dd] != $db_value[$compare_value[$i][column_name]]){

					member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[tel1].$_POST[tel2].$_POST[tel3],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);

				}
			}else if($compare_value[$i][input_name] == 'pass'){
				if($_POST[pass]){

				    // 패스워드 변경
                    $makeshop_id = 'dewytree1';
                    $new_pass = hash("sha512", md5($_POST[pass]) . $makeshop_id . $user_id_hide);

					if($new_pass != $db_value[$compare_value[$i][column_name]]){
						member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$new_pass,$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);
					}
				}
			}else{
				if($_POST[$compare_value[$i][input_name]] != $db_value[$compare_value[$i][column_name]]){

					member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[$compare_value[$i][input_name]],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);
				}
			}
		}

		if($change_pass == "1"){
			if($pass){
                $new_pass = getNewPassword($pass, $user_id_hide);
                $pass_update_str = " , pw = '".$new_pass."' ";

				//	비밀번호 수정 or 신규 가입시 임시패스워드 발송
				if(trim($mail) != "") {


							$ig_content = "
								<link rel='stylesheet' href='http://dev.forbiz.co.kr/admin/tax/css/VAT_invoice.css' type='text/css' />
								<SCRIPT language=JavaScript src='js/png.js'></SCRIPT>
								<style type='text/css'>
								#warp	{float:left;width:600px;margin-left:50px;}
								#header	{margin:0px 0px 5px 5px;}
								/*컨텐츠*/
								/*타이틀영역*/
								#contents	{float:left;width:100%;}
								.contentsarea	{border-left:solid 1px #c3d6de;border-right:solid 1px #c3d6de;float:left;598px;}
								#contents	.warpLine	{font-size:1px;line-height:0px;height:4px;}
								#contents	.title_area	{float:left;width:598px;background:#f7fafb;}
								#contents	.title_area		ul		{float:left;width:560px;padding:14px 0 18px 18px;}
								#contents	.title_area		ul	li	{float:left;font-size:22px;font-weight:bold;color:#404040;}
								#contents	.title_area		ul	li	span	{color:#0268c1;}
								.gap01	{margin:5px 0px 0px 10px;}

								/*언더라인영역*/
								.under_line	{background:url(http://dev.forbiz.co.kr/admin/tax/mail_img/dotted_line.gif) repeat-x;height:1px; width:598px;clear:both;font-size:1px;line-height:0px;padding:0;margin:0px;}

								/*세금계산서 편지 */
								.letter	{}
								.letter		ul	{width:560px;margin:17px 0 0 20px;}
								.letter		ul	li	{line-height:150%;color:#6f6f6f;}
								.letter		ul	li	strong.approval	{color:#0268c1;text-decoration:underline;font-weight:bold;}
								.letter		ul	li	strong.refusal	{color:#cf0000;text-decoration:underline;font-weight:bold;}
								.letter_title	{font-size:14px;font-weight:bold;}
								.btns	{text-align:center;padding:28px 0;}

								/*세금계산서 정보*/
								.supplier_area	{float:left;width:598px;}	
								.supplier_area	ul	{float:left;width:560px;margin-left:20px;display:inline;margin-bottom:50px;}
								.supplier_area	ul	li	{float:left;}
								h2	{background:url(http://dev.forbiz.co.kr/admin/tax/mail_img/blue_point.gif) no-repeat 0 center;margin:10px 0px 5px 0px; }
								h2	strong	{margin-left:10px;}
								.box01	{}
								.box01	td	{border-bottom:solid 1px #e8e8e8;}
								.topLine	td	{border-top:solid 1px #e8e8e8;}
								.box01	td	div	{padding:7px 0;margin-left:10px;background:url(http://dev.forbiz.co.kr/admin/tax/mail_img/point.gif) no-repeat 0 center;}
								.box01	td	div	span	{margin-left:5px;}
								.box01	td	strong	{margin-left:10px;}
								.email	a{text-decoration:underline;color:#0268c1;}
								.title	{background:#f6f6f6;}
								.tbox01	{clear:both;width:560px;margin-left:20px;margin-bottom:20px;}

								/*세금계산서 설명*/
								.ulbox01	{background:#f6f6f6;padding:20px 0;border-top:solid 2px #e8e8e8;border-bottom:solid 2px #e8e8e8;}
								.ulbox01_1	{margin-left:30px;margin-bottom:5px;}
								.ulbox01_1	strong	{margin-left:10px;}
								.ulbox01_1	span	{margin-left:25px;}

								.point_inle	{background:url(http://dev.forbiz.co.kr/admin/tax/mail_img/dotted_line.gif) repeat-x;width:100%;height:1px;}


								/*풋터영역*/
								#footer	{clear:both;width:100%;}
								.footerarea	{padding:20px 0;}
								.footerarea	ul	{margin-left:20px;}
								.footerarea	ul	li	{line-height:140%;}
								.footeline	{background:url(http://dev.forbiz.co.kr/admin/tax/mail_img/footer_line.gif) repeat-x; height:3px;width:100%;margin-bottom:20px;}

								</style>
								";


						$mail_sql = "SELECT * FROM shop_mailsend_config WHERE mc_ix = '0105'";
						$db->query($mail_sql);
						$db->fetch();
						


					//$ig_subject = $db->dt[mc_mail_title];
					$ig_subject = "배럴 임시패스워드";
					$ig_mail_info[mem_name] = $name;
					$ig_mail_info[mem_mail] = $mail;
					$ig_mail_info[mem_id] = $user_id_hide;

					$ig_mail_subject = $ig_mail_info[mem_name]." 님, ".$ig_subject;
					//$ig_content = "임시패스워드 : ".$pass;
					$ig_content .= $db->dt[mc_mail_text];
					$ig_content = str_replace("{ig_new_pw}",$pass,$ig_content);
					$ig_content = str_replace("{mallName}","배럴",$ig_content);
					$ig_content = str_replace("{mallDomain}","https://www.getbarrel.com:",$ig_content);

					SendMail($ig_mail_info, $ig_mail_subject, $ig_content,"","","Y");


						$ig_change_pw_history_SQL = "
							INSERT INTO
								ig_change_pw_history
							SET
								code = '".$code."',
								pw_data = '".$new_pass."',
								ch_type = '1',
								regDt = '".date("Y-m-d H:i:s")."'
							";
						$db->query($ig_change_pw_history_SQL);
				}
				//	비밀번호 수정 or 신규 가입시 임시패스워드 발송

			}
		}

		//회원정보 수정 히스토리 쌓기 2013-12-02 이학봉

		$sql = "UPDATE ".TBL_COMMON_USER."  SET
				mem_type = '$mem_type' ,
				authorized = '$authorized' $pass_update_str , 
				is_id_auth='$is_id_auth',
				is_pos_link = 'N', join_status = 'U'
				WHERE code='$code'";

		$db->query($sql);

		if($mem_type == "M"){

			$sql = "UPDATE ".TBL_COMMON_USER."  SET
							company_id = '',
							is_pos_link = 'N',
							join_status = 'U'
							WHERE code='$code'";
			$db->query($sql);

		}

		if($db->dbms_type == "oracle"){
				$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL."  SET
							name=HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),
							birthday='$birthday',
							birthday_div='$birthday_div',
							mail=AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'),
							addr1=AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."'),
							addr2=AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."'),
							zip=AES_ENCRYPT('$zip','".$db->ase_encrypt_key."'),
							doro_addr1=AES_ENCRYPT('$doro_addr1','".$db->ase_encrypt_key."'),
							doro_addr2=AES_ENCRYPT('$doro_addr2','".$db->ase_encrypt_key."'),
							doro_zip=AES_ENCRYPT('$zip','".$db->ase_encrypt_key."'),
							tel=AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),
							pcs=AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'),
							resign_msg=HEX(AES_ENCRYPT('$level_msg','".$db->ase_encrypt_key."')),
							mem_card=HEX(AES_ENCRYPT('$mem_card','".$db->ase_encrypt_key."')),
							level_ix='$level_ix',
							level_msg='$level_msg',
							info='$info',
							sms='$sms',
							collection='$collection',
							gp_ix='$gp_ix',
							sex_div = '$sex_div',
							nick_name = '$nick_name',
							job='$job',
							customs_clearance_number='$customs_clearance_number'
							$add_where_add_fetch
						WHERE code='$code'";
		}else{
				$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL."  SET
							name=HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),
							first_name=HEX(AES_ENCRYPT('$first_name','".$db->ase_encrypt_key."')),
							last_name=HEX(AES_ENCRYPT('$last_name','".$db->ase_encrypt_key."')),
							first_kana=HEX(AES_ENCRYPT('$first_kana','".$db->ase_encrypt_key."')),
							last_kana=HEX(AES_ENCRYPT('$last_kana','".$db->ase_encrypt_key."')),
							birthday='$birthday',
							birthday_div='$birthday_div',
							mail=HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),
							addr1=HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),
							addr2=HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),
							zip=HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),
							doro_addr1=HEX(AES_ENCRYPT('$doro_addr1','".$db->ase_encrypt_key."')),
							doro_addr2=HEX(AES_ENCRYPT('$doro_addr2','".$db->ase_encrypt_key."')),
							doro_zip=HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),
							tel=HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),
							pcs=HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),
							resign_msg=HEX(AES_ENCRYPT('$level_msg','".$db->ase_encrypt_key."')),
							mem_card=HEX(AES_ENCRYPT('$mem_card','".$db->ase_encrypt_key."')),
							level_ix='$level_ix',
							level_msg='$level_msg',
							info='$info',
							sms='$sms',
							collection='$collection',
							gp_ix='$gp_ix',
							sex_div = '$sex_div',
							nick_name = '$nick_name',
							job='$job',
							di = md5('".str_replace('-', '', $pcs)."'),
							customs_clearance_number='$customs_clearance_number'
							$add_where_add_fetch
						WHERE code='$code'";

		}

		$db->query($sql);

	}else if($info_type == "c_member"){	//사업자 회원정보 추가후 거래처랑 매핑 해주는 기능 2014-07-08 이학봉

		$db->query("select mem_type from ".TBL_COMMON_USER."  WHERE code='$code'");
		$db->fetch();
		$mem_type = $db->dt[mem_type];

		//회원정보 수정 히스토리 쌓기 2013-12-02 이학봉
		$compare_value[0] = array("input_name"=>"company_id", "column_name"=>"company_id", "name_text"=>"사업장변경");

		$sql = "select
					cu.company_id
				from
					common_user as cu 
				where
					cu.code = '".$code."'";

		$db->query($sql);
		$db->fetch();
		$db_value = $db->dt;

		for($i=0;$i<count($compare_value);$i++){

			if($compare_value[$i][input_name] == 'com_phone1'){
				if($_POST[com_phone1].$_POST[com_phone2].$_POST[com_phone3] != $db_value[$compare_value[$i][column_name]]){

					member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[com_phone1].$_POST[com_phone2].$_POST[com_phone3],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);
				}
			}else if($compare_value[$i][input_name] == 'com_mobile1'){
				if($_POST[com_mobile1].$_POST[com_mobile2].$_POST[com_mobile3] != $db_value[$compare_value[$i][column_name]]){

					member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[com_mobile1].$_POST[com_mobile2].$_POST[com_mobile3],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);
				}
			}else if($compare_value[$i][input_name] == 'com_num1'){
				if($_POST[com_num1].$_POST[com_num2].$_POST[com_num3] != $db_value[$compare_value[$i][column_name]]){

					member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[com_num1].$_POST[com_num2].$_POST[com_num3],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);
				}
			}else if($compare_value[$i][input_name] == 'corporate_number_1'){
				if($_POST[corporate_number_1].$_POST[corporate_number_2].$_POST[corporate_number_3] != $db_value[$compare_value[$i][column_name]]){

					member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[corporate_number_1].$_POST[corporate_number_2].$_POST[corporate_number_3],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);
				}
			}else{
				if($_POST[$compare_value[$i][input_name]] != $db_value[$compare_value[$i][column_name]]){

					member_edit_history($code,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[$compare_value[$i][input_name]],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger],$reg_url);
				}
			}
		}


		//회원정보 수정 히스토리 쌓기 2013-12-02 이학봉
		if($mem_type == 'C'){

			if($company_id){

				$sql = "UPDATE ".TBL_COMMON_USER."  SET
							company_id = '$company_id',
							is_pos_link = 'N',
							join_status = 'U'
							WHERE code='$code'";
				$db->query($sql);

			}else{

				$company_id  = md5(uniqid(rand()));

				$sql = "insert into ".TBL_COMMON_COMPANY_DETAIL."  SET
						company_id='$company_id',
						com_name='$com_name',
						com_div = '$com_div',
						com_type = 'G',
						com_ceo='$com_ceo',
						seller_type = '1',
						com_number='$com_number',
						online_business_number='$online_business_number',
						com_phone='$com_phone',
						com_mobile = '$com_mobile',
						com_email = '$com_email',
						corporate_number = '$corporate_number',
						com_homepage = '$com_homepage',
						com_fax='$com_fax',
						com_business_status = '$com_business_status',
						com_business_category = '$com_business_category',
						com_zip = '$com_zip',
						com_addr1 = '$com_addr1',
						com_addr2 = '$com_addr2',
						seller_auth = 'Y',
						loan_price = '0',
						inputtype = 'A',
						is_erp_link = 'N'";

				$db->query($sql);
				$company_ix = $db->insert_id();
				$db->query("update ".TBL_COMMON_COMPANY_DETAIL." set custseq = '".$company_ix."' where company_ix = '".$company_ix."'");

				$seller_sql = "insert into ".TBL_COMMON_SELLER_DETAIL." set
								company_id = '".$company_id."',
								shop_name = '".$com_name."',
								seller_level = '3',
								seller_date = NOW(),
								seller_division = '1',
								nationality = 'I',
								deposit_price = '0',
								authorized = 'Y',
								regdate =NOW()
							";

				$db->query($seller_sql);
				
				//트리코드 생성 시작 
				$sql = "select
							relation_code
						from
							common_company_relation
						where
							company_id = '".$_SESSION[admininfo][company_id]."'";	
				$db2->query($sql);
				$db2->fetch();
				$re_relation_code = $db2->dt[relation_code];

				$seq	= check_seq($re_relation_code,$depth);
				$new_code = check_relation($re_relation_code,$depth);
				//트리코드 생성 끝

				$sql_relation = "
						insert into 
							".TBL_COMMON_COMPANY_RELATION." set
						company_id = '".$company_id."',
						relation_code = '".$new_code."',
						seq = '".$seq."',
						reg_date = NOW();
				";
				
				$db->query($sql_relation);

				$sql = "UPDATE ".TBL_COMMON_USER."  SET
							company_id = '$company_id',
							is_pos_link = 'N',
							join_status = 'U'
						WHERE code='$code'";
				$db->query($sql);
			}
		}

		$sql="select field from shop_join_info where disp = 'Y' and field like 'add_etc%' order by vieworder";
		$db2->query($sql);
		$add_fetch=$db2->fetchall();
		$cnt_add_fetch=count($add_fetch);
		$add_where_add_fetch="";
		for($i=0;$i<$cnt_add_fetch;$i++) {
			$add_where_add_fetch.=",".$add_fetch[$i]["field"]." ='".${$add_fetch[$i]["field"]}."' ";
		}// 추가되는 항목에 대해서만 값을 입력함 kbk

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/".$company_id;
		
		if(!is_dir($path)){
			exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
		}

		// 이미지 저장 시작 
		$file_type = substr(strrchr($business_file_name, '.'), 1); 
		$file_name = "business_file_".$company_id.".".$file_type;
		if ($business_file_size > 0){
			copy($business_file,$path."/"."business_file_".$company_id.".".$file_type);
		}

		$sql = "select 
					*
				from
					common_company_file
				where
					sheet_name = 'business_file'
					and company_id = '".$company_id."'";
		$db->query($sql);
		$db->fetch();
		$db->total;

		if($db->total == 0){
			$sql = "insert into common_company_file set
						company_id = '$company_id',
						seq = '1',
						sheet_name = 'business_file',
						sheet_value = '$file_name',
						reg_date = NOW();";
			$db->query($sql);
		}
		// 이미지 저장 끝

	}else if($info_type == "file_member"){

		$voucher_phone = $voucher_phone1."-".$voucher_phone2."-".$voucher_phone3;		//현금영수증 핸드폰번호
		$voucher_card = $voucher_card1."-".$voucher_card2."-".$voucher_card3."-".$voucher_card4;		// 현금영수증 카드번호
		$expense_num = $expense_num1."-".$expense_num2."-".$expense_num3;		//지출증빙번호

		$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL."  SET
					voucher_div = '$voucher_div',
					phone_voucher_name = '$phone_voucher_name',
					voucher_num_div = '$voucher_num_div',
					card_voucher_name = '$card_voucher_name',
					voucher_phone = '$voucher_phone',
					voucher_card = '$voucher_card',
					expense_num = '$expense_num',
					certificate_yn = '$certificate_yn'
				WHERE code='$code'";

		$db->query($sql);

	}else if($info_type == "delivery_info"){

	}else if($info_type == "return_bank"){

		$bank_ix=trim($bank_ix);
		$bank_code=trim($bank_code);

		$bank_name=$arr_banks_name[$bank_code];//constants.php 에 $arr_banks_name 있음 kbk 13/07/05
		$bank_number=trim($bank_number);
		$bank_owner=trim($bank_owner);
	/*
	1. 기본 : - 해당 정보가 기본일경우 업데이트
			: 1.기본으로 수정시 : 
	*/
		//기본환불통장 체크 2014-04-30 이학봉
		$sql = "select bank_ix, ucode, bank_code, use_yn, is_basic, regdate, editdate,
					AES_DECRYPT(UNHEX(bank_name),'".$db->ase_encrypt_key."') as bank_name,
					AES_DECRYPT(UNHEX(bank_number),'".$db->ase_encrypt_key."') as bank_number,
					AES_DECRYPT(UNHEX(bank_owner),'".$db->ase_encrypt_key."') as bank_owner 
				from shop_user_bankinfo where bank_ix='".$bank_ix."'";
		$db->query($sql);
		$db->fetch();

		if($db->dt[is_basic] == '1'){
			if($is_basic == '1'){
				$where = " , is_basic = '".$is_basic."' ";
				//기존 데이타 변함없음
			}else{
				$sql = "select bank_ix, ucode, bank_code, use_yn, is_basic, regdate, editdate,
							AES_DECRYPT(UNHEX(bank_name),'".$db->ase_encrypt_key."') as bank_name,
							AES_DECRYPT(UNHEX(bank_number),'".$db->ase_encrypt_key."') as bank_number,
							AES_DECRYPT(UNHEX(bank_owner),'".$db->ase_encrypt_key."') as bank_owner 
						from shop_user_bankinfo where bank_ix != '".$bank_ix."'";
				$db->query($sql);
				$db->fetch();

				if($db->total > 0){	//해당 정보외 다른 데이타가 잇을경우
					$sql = "update shop_user_bankinfo set is_basic = '1' where bank_ix = (select bank_ix from (select bank_ix from shop_user_bankinfo as u1 where u1.is_basic = '0'  and u1.ucode='".$ucode."' order by u1.regdate DESC limit 0,1) as t )";
					$db->query($sql);	//기본 -> 일반으로 바뀔경우 가장 최근에 등록된 일반정보가 기본으로 등록됨
				}else{
					$is_basic = '0';
					echo "<script>alert('기본환불통장은 필수 입니다.');</script>";
				}
			}
		}else{
			if($is_basic == '1'){	//일반 -> 기본으로 수정할 경우  기본을 일반으로 수정하고 해당 정보를 기본으로 등록한다.
				$sql = "update shop_user_bankinfo set is_basic = '0' where is_basic = '1'";
				$db->query($sql);

				$where = " , is_basic = '".$is_basic."' ";
			}else{					//일반에서 일반으로수정시 변함없음
				$where = " , is_basic = '".$is_basic."' ";
				//기존 데이타 변함없음
			}
		}
		//기본환불통장 체크 2014-04-30 이학봉

		$sql="UPDATE shop_user_bankinfo SET 
					bank_code='".$bank_code."', 
					bank_number=HEX(AES_ENCRYPT('".$bank_number."','".$db->ase_encrypt_key."')), 
					bank_name=HEX(AES_ENCRYPT('".$bank_name."','".$db->ase_encrypt_key."')), 
					bank_owner=HEX(AES_ENCRYPT('".$bank_owner."','".$db->ase_encrypt_key."')),
					use_yn='".$use_yn."', 
					editdate=NOW() $where
				WHERE
					bank_ix='".$bank_ix."' AND ucode='".$ucode."'  ";
		$db->query($sql);

		echo "<script>
			alert('계좌정보가 수정되었습니다.');
			top.location.reload();location.href='about:blank';
		</script>";
		exit;
	}else if($info_type == 'my_pet'){
        for($i=0; $i<count($pet_name); $i++){
            if($pet_birth_yyyy[$i] != '' && $pet_birth_mm[$i] != '' && $pet_birth_dd[$i] != ''){
                $pet_birth = $pet_birth_yyyy[$i].'-'.$pet_birth_mm[$i].'-'.$pet_birth_dd[$i];
            }

            for($j=0; $j<count(${"pet_gender_".$i}); $j++){
                $pet_gender = ${"pet_gender_".$i}[$j];
            }

            $sql = "update ".TBL_COMMON_PET."
                       set pet_name = '".$pet_name[$i]."'
                         , pet_group = '".$pet_group[$i]."'
                         , pet_option = '".$pet_option[$i]."'
                         , pet_birth = '".$pet_birth."'
                         , pet_weight = '".$pet_weight[$i]."'
                         , pet_reg_length = '".$pet_reg_length[$i]."'
                         , pet_back_length = '".$pet_back_length[$i]."'
                         , pet_gender = '".$pet_gender."'
                     where pet_id = '".$pet_id[$i]."'
                    ";

            $db->query($sql);
        }

        echo "<script>alert('수정되었습니다.');</script>";
	}

	echo("<script>parent.location.reload();</script>");


}else if ($act == "delete"){

	$db->query("select company_id,mileage from ".TBL_COMMON_USER."  WHERE code='$code'");
	$db->fetch();
    $company_id = $db->dt[company_id];
    $mileage = $db->dt[mileage];

	$db->query("DELETE FROM ".TBL_COMMON_MEMBER_DETAIL."  WHERE code='$code'");
	$db->query("DELETE FROM ".TBL_COMMON_USER."  WHERE code='$code'");
    $db->query("DELETE FROM ".TBL_COMMON_DROPMEMBER."  WHERE code='$code'");
    $db->query("DELETE FROM sns_info  WHERE uid='$code'");

	if($mileage > 0){
        $mileage_data[uid] = $code;
        $mileage_data[type] = 6;
        $mileage_data[mileage] = abs($mileage);
        $mileage_data[message] = "회원 삭제에 따른 사용처리";
        $mileage_data[state_type] = "use";
        $mileage_data[save_type] = 'mileage';
        InsertMileageInfo($mileage_data);
    }


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('회원정보가 정상적으로 삭제되었습니다.');top.location.href = 'member.php?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."';</script>");



}else if($act == "mem_talk_insert"){

	$db->sequences = "SHOP_MEMBER_TALK_HISTORY_SEQ";
	$sql = "insert into ".TBL_SHOP_MEMBER_TALK_HISTORY." (ta_ix,ucode,ta_memo,ta_counselor,regdate,tc_ix) values('','$code','$ta_memo','$ta_counselor',NOW(),'$tc_ix')";
	$db->query($sql);

	echo("<script>top.location.href = 'member_view.php?code=".$code."';</script>");


}else if($act == "mem_talk_delete"){

	$db->query("DELETE FROM ".TBL_SHOP_MEMBER_TALK_HISTORY."  WHERE ta_ix='$ta_ix'");
	echo("<script>top.location.href = 'member_view.php?code=".$code."';</script>");



}else if($act == "reserve_insert"){
    //************적립금 *************

	if($state == "2"){
		$use_state = $use_state_cancel;
	}else{
		$use_state = $use_state_add;
	}

	InsertReserveInfo($uid,$oid,$od_ix,$id,$reserve,$state,$use_state,$etc,'mileage',$admininfo);

	switch ($state) {
		case '1' : 
			$type = '7';  //적립완료 (수동적립 - 관리자)
			$state_type = 'add';
			break;
		case '2' : 
			$type = '2'; //사용내역 (수동사용 - 관리자)
			$state_type = 'use';
			break;
		default : 
			$type = '';  //사용안함
			break;
	}

	if(!empty($type)){
	
		$mileage_data[uid] = $uid;
		$mileage_data[type] = $type;
		$mileage_data[mileage] = abs($reserve);
		$mileage_data[message] = $etc;
		$mileage_data[state_type] = $state_type;
		$mileage_data[save_type] = 'mileage';
		InsertMileageInfo($mileage_data);
	}

	echo("<script>top.location.href = 'reserve.pop.php?code=$uid&reserve_id=$reserve_id';</script>");


}else if($act == "reserve_update"){

	if($state == "2"){
		$use_state = $use_state_cancel;
	}else{
		$use_state = $use_state_add;
	}
	if($state == '9' || $state == '1'){
		InsertReserveInfo($uid,$oid,$od_ix,$id,$reserve,$state,$use_state,$etc,'mileage',$admininfo);
		echo("<script>top.location.href = 'reserve.pop.php?code=$uid&reserve_id=$reserve_id';</script>");
	}else{
		echo("<script>alert('이미 처리된 마일리지는 수정하실수 없습니다.');history.go(-1);</script>");
	}


}else if($act == "update_state"){

	$sql = "select
				reserve_id
			from
				".TBL_SHOP_RESERVE_INFO." 
			where
				id = '".$id."'";
	$db->query($sql);
	$db->fetch();
	$reserve_id = $db->dt[reserve_id];
	$sql = "update shop_reserve set state='$state',use_state='$use_state' where reserve_id = '$reserve_id' ";

	$sql = "update ".TBL_SHOP_RESERVE_INFO." set state='$state',use_state='$use_state' where id = '$id' ";
	$db->query($sql);

	echo("<script>top.location.reload();</script>");


}else if($act == "reserve_delete"){

	$sql = "select
				reserve_id
			from
				".TBL_SHOP_RESERVE_INFO." 
			where
				id = '".$id."'";
	$db->query($sql);
	$db->fetch();
	$reserve_id = $db->dt[reserve_id];
	$db->query("DELETE FROM shop_reserve  WHERE reserve_id='$reserve_id'");
	$db->query("DELETE FROM ".TBL_SHOP_RESERVE_INFO."  WHERE id='$id'");

	echo("<script>top.location.reload();</script>");


}else if($act == "reserve_select_delete"){
		
	$sql = "select reserve_id
			  from ".TBL_SHOP_RESERVE_INFO." 
			 where id = '".$id."'";
	$db->query($sql);
	$db->fetch();
	$reserve_id = $db->dt[reserve_id];

	for($i=0;$i<count($rid);$i++){
		$db->query("DELETE FROM shop_reserve  WHERE reserve_id='$reserve_id'");
		$db->query("DELETE FROM ".TBL_SHOP_RESERVE_INFO."  WHERE id='".$rid[$i]."'");
	}

	echo("<script>top.location.reload();</script>");


}else if($act == "point_insert"){
	//************포인트 *************
    if($state == "2"){
		$use_state = $use_state_cancel;
	}else{
		$use_state = $use_state_add;
	}

	InsertReserveInfo($uid,$oid,$od_ix,$id,$reserve,$state,$use_state,$etc,'point',$admininfo);

	switch ($state) {
		case '1' : 
			$type = '7';  //적립완료 (수동적립 - 관리자)
			$state_type = 'add';
			break;
		case '2' : 
			$type = '2'; //사용내역 (수동사용 - 관리자)
			$state_type = 'use';
			break;
		default : 
			$type = '';  //사용안함
			break;
	}
	if(!empty($type)){
	
		$mileage_data[uid] = $uid;
		$mileage_data[type] = $type;
		$mileage_data[mileage] = abs($reserve);
		$mileage_data[message] = $etc;
		$mileage_data[state_type] = $state_type;
		$mileage_data[save_type] = 'point';
		InsertMileageInfo($mileage_data);


	}

	echo("<script>history.go(-1);</script>");
	echo("<script>top.location.href = 'point.pop.php?code=$uid&reserve_id=$reserve_id';</script>");

}else if($act == "point_update"){

	if($state == "2"){
		$use_state = $use_state_cancel;
	}else{
		$use_state = $use_state_add;
	}

	if($state == '9' || $state == '1'){
		InsertReserveInfo($uid,$oid,$od_ix,$id,$reserve,$state,$use_state,$etc,'point',$admininfo);
	}else{
		echo("<script>alert('이미 처리된 마일리지는 수정하실수 없습니다.');history.go(-1);</script>");
	}
	echo("<script>top.location.href = 'point.pop.php?code=$uid';</script>");

}else if($act == "point_delete"){

	$sql = "select
				reserve_id
			from
				".TBL_SHOP_POINT_INFO." 
			where
				id = '".$id."'";
	$db->query($sql);
	$db->fetch();
	$reserve_id = $db->dt[reserve_id];
	$db->query("DELETE FROM shop_point  WHERE reserve_id='$reserve_id'");
	$db->query("DELETE FROM ".TBL_SHOP_POINT_INFO."  WHERE id='$id'");

	echo("<script>top.location.href = 'point.pop.php?code=$uid';</script>");


}else if($act == "point_select_delete"){
		$sql = "select
				reserve_id
			from
				".TBL_SHOP_POINT_INFO." 
			where
				id = '".$id."'";
		$db->query($sql);
		$db->fetch();
		$reserve_id = $db->dt[reserve_id];
	
		for($i=0;$i<count($rid);$i++){
			$db->query("DELETE FROM shop_point  WHERE reserve_id='$reserve_id'");
			$db->query("DELETE FROM ".TBL_SHOP_POINT_INFO."  WHERE id='".$rid[$i]."'");
		}

	echo("<script>top.location.reload();</script>");


}else if($act == "member_black_list_n"){

	$db->query("update ".TBL_COMMON_MEMBER_DETAIL." set level_ix='1'  WHERE code='$code'");

	$sql = "insert into common_blacklist_history(ix,type,code,msg,changer,regdate) values('','C','$code','불량고객 리스트에서 해제','".$admininfo[charger]."(".$admininfo[charger_id].")"."',NOW()) ";
	$db->sequences = "COMMON_BLACKLIST_HISTORY_SEQ";
	$db->query($sql);

	echo("<script>top.location.reload();</script>");


}else if($act == "select_member_black_list_n"){

	for($i=0;$i<count($code);$i++){
		
		$db->query("update ".TBL_COMMON_MEMBER_DETAIL." set level_ix='1'  WHERE code='".$code[$i]."'");

		$sql = "insert into common_blacklist_history(ix,type,code,msg,changer,regdate) values('','C','".$code[$i]."','불량고객 리스트에서 일괄해제','".$admininfo[charger]."(".$admininfo[charger_id].")"."',NOW()) ";
		$db->sequences = "COMMON_BLACKLIST_HISTORY_SEQ";
		$db->query($sql);
	}

	echo("<script>top.location.reload();</script>");


}else if($act == 'delete_member_vip'){

	if(count($code) > 0){
		for($i=0;$i<count($code);$i++){
			$sql = "update common_member_detail set level_ix = '1' where code = '".$code[$i]."'";
			$db->query($sql);
		}

		echo("<script>top.location.reload();</script>");
	}else{
		echo("<script>alert('선택한 회원이 없습니다.');top.location.reload();</script>");
	}

}else if($act == "dropmember_delete"){

	$db->query("DELETE FROM ".TBL_COMMON_MEMBER_DETAIL."  WHERE code='$code'");

	$db->query("DELETE FROM ".TBL_COMMON_DROPMEMBER."  WHERE code='$code'");

		//	삭제한 회원의 주문건의 개인정보 업데이트
		//	btel(주문자전화번호), bmobile(주문자모바일번호), bmail(주문자메일), bzip(주문자우편번호), baddr(주문자주소)
			if(trim($code) != "") {
				$ig_dropmember_delete_SQL = "
					UPDATE
						shop_order AS O LEFT JOIN shop_order_detail_deliveryinfo AS ODD ON O.oid = ODD.oid
					SET
						O.btel = '',
						O.bmobile = '',
						O.bmail = '',
						O.bzip = '',
						O.baddr = '',
						ODD.rname = '',
						ODD.rtel = '',
						ODD.rmobile = '',
						ODD.rmail = '',
						ODD.zip = '',
						ODD.addr1 = '',
						ODD.addr2 = ''
					WHERE
						O.user_code = '".$code."'
					";
				$db->query($ig_dropmember_delete_SQL);
			}
		//	//삭제한 회원의 주문건의 개인정보 업데이트

	echo("<script>top.location.href = 'dropmember.php';</script>");


}else if($act == 'insert'){	//사용자등록
	
	if($info_type == 'member'){
		$sql = "select * from common_user where id = '".$id."'";
		$db->query($sql);
		$db->fetch();

		if($db->total > 0){
			echo("<script>alert('이미 등록된 아이디 입니다.');</script>");
			exit;
		}
		
		$mem_name = trim($name);
		$pass  = trim($pass);
		$mail  = trim($mail);
		
		$zip = $zip1."-".$zip2;
		$addr1 = trim($addr1);
		$addr2 = trim($addr2);
		$tel = $tel1."-".$tel2."-".$tel3;
		$pcs = $pcs1."-".$pcs2."-".$pcs3;

		$is_id_auth = "Y";
		$mem_div = 'D';	//사업자나 일반회원은 회원구분이 전부 D로 설정 2014-07-08 이학봉
		$birthday = $birthday_yyyy."-".$birthday_mm."-".$birthday_dd;

		if($pass){

            $new_pass = getNewPassword($pass, $user_id_hide);
            $pass_update_str = $new_pass;
		}

		$code		= md5(uniqid(rand()));
		//$company_id	= md5(uniqid(rand()));

		if($db->dbms_type == "oracle"){

		$sql = "INSERT INTO ".TBL_COMMON_USER."
				(code, id, pw, mem_type, mem_div, date_, visit, last, ip, company_id, authorized, auth,is_pos_link, join_status, language, request_info, request_yn, request_date)
				VALUES
				('$code','$id','".$pass_update_str."','$mem_type','$mem_div',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id','$authorized',1,'N', 'I', 'korean','$mem_type','$authorized', NOW())";
		}else{

		$sql = "INSERT INTO ".TBL_COMMON_USER."
				(code, id, pw, mem_type, mem_div, date, visit, last, ip, company_id, authorized, auth,is_pos_link, join_status, language, request_info, request_yn, request_date)
				VALUES
				('$code','$id','".$pass_update_str."','$mem_type','$mem_div',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id','$authorized',1,'N', 'I', 'korean','$mem_type','$authorized', NOW())";
		}

		$db->query($sql);

		if($db->dbms_type == "oracle"){
			$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date_, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6,level_ix, level_msg)
					VALUES
					('$code','$birthday','$birthday_div',AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."'),AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'),AES_ENCRYPT('$zip','".$db->ase_encrypt_key."'),AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."'),AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."'),AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),'$tel_div',AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6', '$level_ix', '$level_msg')";
		}else{
			$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code, birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6,level_ix, level_msg)
					VALUES
					('$code','$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6', '$level_ix', '$level_msg')";
		}
		$db->query($sql);

		$reserve_data = GetReserveRate();

		if($reserve_data[join_mileage_rate] > 0){
			//////////////// 마일리지 적립 시작///////////////////////
			InsertReserveInfo($code,$oid,$od_ix,$r_id,$reserve_data[join_mileage_rate],'1','5',$name."님 회원가입 축하 지급 적립금",'mileage',$admininfo);	
			//마일리지,적립금 통합용 함수 2013-06-19 이학봉

			
			/*신규 포인트,마일리지 접립 함수 JK 160405*/
			$mileage_data[uid] = $code;
			$mileage_data[type] = 2;
			$mileage_data[mileage] = $reserve_data[join_mileage_rate];
			$mileage_data[message] = $name."님 회원가입 축하 지급 적립금";
			$mileage_data[state_type] = 'add';
			$mileage_data[save_type] = 'mileage';
			InsertMileageInfo($mileage_data);
		}

		echo("<script>alert('정상적으로 등록 되었습니다.');top.location.href = 'member_info.php?code=".$code."&info_type=".$info_type."';</script>");

	}else if($info_type == 'return_bank'){	//환불계좌정보 추가

		$bank_code=trim($bank_code);

		$bank_name=$arr_banks_name[$bank_code];//constants.php 에 $arr_banks_name 있음 kbk 13/07/05
		$bank_number=trim($bank_number);
		$bank_owner=trim($bank_owner);
		
		//기본환불통장 체크 2014-04-30 이학봉
		$sql = "select bank_ix, ucode, bank_code, use_yn, is_basic, regdate, editdate,
					AES_DECRYPT(UNHEX(bank_name),'".$db->ase_encrypt_key."') as bank_name,
					AES_DECRYPT(UNHEX(bank_number),'".$db->ase_encrypt_key."') as bank_number,
					AES_DECRYPT(UNHEX(bank_owner),'".$db->ase_encrypt_key."') as bank_owner 
				from shop_user_bankinfo where ucode = '".$ucode."' and is_basic = '1'";
		$db->query($sql);
		$db->fetch();

		if($db->total > 0){
			if($is_basic == '1'){	//기본이 존재하는데 기본으로 입력할경우
				
				$sql = "update shop_user_bankinfo set
							is_basic = '0' 
						where
							bank_ix = (select bank_ix from (select bank_ix from shop_user_bankinfo as u1 where u1.is_basic = '1'  and u1.ucode='".$ucode."') as t)";

				$db->query($sql);	//기본이 있는데 기본으로 등록될경우 기존 기본은 일반으로 수정되고 새로운 기본으로 등록됨
				
				//echo "<script>alert('기본환불 통장이 존재하기에 일반환불 통장으로 등록됩니다.');</script>";
			}
		}else{
			if($is_basic == '1'){	//기본이 없는데 기본으로등록될경우 변함없음
				$is_basic = '1';
			}else{					//기본이 없는데 일반으로 등록될경우 기본으로 등록됨
				echo "<script>alert('기본환불 통장이 필수이기에 기본환불 통장으로 등록됩니다.');</script>";
				$is_basic = '1';
			}
		}
		//기본환불통장 체크 2014-04-30 이학봉

		$sql="INSERT INTO shop_user_bankinfo (ucode, bank_code, bank_name, bank_number, bank_owner, use_yn, regdate,is_basic) VALUES ('".$ucode."','".$bank_code."',HEX(AES_ENCRYPT('".$bank_name."','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('".$bank_number."','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('".$bank_owner."','".$db->ase_encrypt_key."')),'".$use_yn."',NOW(),'".$is_basic."')";
		$db->query($sql);

		if($admin_type=="Y") {
			$js_locatoin="top.location.href='/admin/member/member_info.php?info_type=return_bank&mmode=pop&code=$ucode';";
		} else {
			$js_locatoin="top.location.href='/mypage/refund_account.php';";
		}
		echo "<script>
			alert('계좌정보가 추가되었습니다.');
			".$js_locatoin."
		</script>";
		exit;
	}

}else if($act == 'insert_false'){
    /**
     * 회원 수동 등록
     *
     */

	$mem_name  = trim($name);
	$pass  = trim($pass);
	$mail  = trim($mail);
	$comp  = trim($comp);
	$class = trim($class);
	$addr1 = trim($addr1);
	$addr2 = trim($addr2);

	if($zip2 != "")
		$zip = "$zip1-$zip2";
	else
		$zip = $zip1;

	if($tel2 != "")
		$tel = "$tel1-$tel2-$tel3";
	else
		$tel = $tel1;

	if($pcs2 != "")
		$pcs = "$pcs1-$pcs2-$pcs3";
	else
		$pcs = $pcs1;


    $is_id_auth = "Y";

	if($mem_type == "M"){
		$mem_div = 'B';
	}else if($meme_type == "C" || $mem_type == "S"){
		$mem_div = 'C';
	}else{
		$mem_div = 'B';
	}

	$com_number = "$com_num1-$com_num2-$com_num3";
	$com_phone = "$com_phone1-$com_phone2-$com_phone3";
	$com_fax = "$com_fax1-$com_fax2-$com_fax3";
	$com_zip   = "$com_zip1-$com_zip2";
	$com_homepage = "$com_homepage";

	$birthday = $birthday_yyyy."-".$birthday_mm."-".$birthday_dd;

	if($pass){
	    // 패스워드 변경
        $new_pass = getNewPassword($pass, $id);
        $pass_update_str = " , pw = '".$new_pass."' ";
	}

	$code  = md5(uniqid(rand()));
	$company_id  = md5(uniqid(rand()));

	if($db->dbms_type == "oracle"){
		$sql = "INSERT INTO ".TBL_COMMON_USER."
						(code, id, pw, mem_type, mem_div, date_, visit, last, ip, company_id, authorized, auth,is_pos_link, join_status)
						VALUES
						('$code','$id','".$pass_update_str."','$mem_type','$mem_div',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id','$authorized',1,'N', 'I')";
	}else{
		$sql = "INSERT INTO ".TBL_COMMON_USER."
						(code, id, pw, mem_type, mem_div, date, visit, last, ip, company_id, authorized, auth,is_pos_link, join_status)
						VALUES
						('$code','$id','".$pass_update_str."','$mem_type','$mem_div',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id','$authorized',1,'N', 'I')";
	}
	$db->query($sql);
	if($db->dbms_type == "oracle"){
	   $sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date_, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code',AES_ENCRYPT('".$db->ase_encrypt_key."'),'$birthday','$birthday_div',AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."'),AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'),AES_ENCRYPT('$zip','".$db->ase_encrypt_key."'),AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."'),AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."'),AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),'$tel_div',AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
	}else{
	    $sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code',HEX(AES_ENCRYPT('".$db->ase_encrypt_key."')),'$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
	}
    $db->query($sql);
    $sql = "INSERT INTO ".TBL_COMMON_COMPANY_DETAIL."
						(company_id, com_name, com_ceo, com_business_status, com_business_category, com_number, com_phone,com_mobile, com_fax, com_zip, com_addr1, com_addr2, com_homepage)
						VALUES
						('$company_id','$com_name', '$com_ceo', '$com_business_status', '$com_business_category', '$com_num1-$com_num2-$com_num3','$com_phone1-$com_phone2-$com_phone3','$com_mobile1-$com_mobile2-$com_mobile3','$com_fax1-$com_fax2-$com_fax3', '$com_zip1-$com_zip2', '$com_addr1', '$com_addr2', '$com_homepage')";

	$db->query($sql);

    include_once $_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class";
    $shmop = new Shared("reserve_rule");
	//$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$P->Config["mall_data_root"]."/_shared/";
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/_shared/";// 경로를 $p 클래스에서 가져오기에 에러 발생 kbk 12/04/06
	$shmop->SetFilePath();
	$reserve_data = $shmop->getObjectForKey("reserve_rule");
	$reserve_data = unserialize(urldecode($reserve_data));

	if($reserve_data[reserve_use_yn] == "Y" && $reserve_data[join_reserve_rate] > 0){
		$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate) VALUES ('','$code','','','0','0','".$reserve_data[join_reserve_rate]."','1','회원가입 축하 지급 적립금',NOW())");
	}

	echo("<script>alert('정상적으로 등록 되었습니다.');location.href = 'member_info.php?code=".$code."';</script>");



}else if($act == "idcheck"){

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

	$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id='$id' and mem_type in ('M','F','C','S','A','MD') ");
	$total = $db->total;
	$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id='$id' ");
	$total = $total + $db->total;

	if ($total)
	{
		echo "N";
	}else{
		echo "Y";
	}



//셀러업체 가입 프로세스 시작
}else if($act == 'seller_join'){

    // 패스워드 변경
    $new_pass = getNewPassword($pw, $id);
    $pass_check = " and (pw = MD5('".$pw."') or pw = '".hash("sha256", $pw)."' or pw = '".$new_pass."' ) ";


	if($db->dbms_type == "oracle"){
		$sql = "SELECT
					ccd.*,
					cu.*,
					AES128_CRYPTO.decrypt(cmd.name,'".$db->ase_encrypt_key."') as name,
					cmd.mem_level
				FROM
					".TBL_COMMON_USER." cu
					inner join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
					inner join ".TBL_COMMON_COMPANY_DETAIL." ccd on cu.company_id = ccd.company_id
				WHERE
					cu.id=TRIM('".$id."')
					$pass_check
					and ccd.com_type not in ('S','A')
					and cu.mem_div !='S'
					and cu.auth != '4'";
	}else{
		$sql = "SELECT
					ccd.*, cu.*,
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
					cmd.mem_level
				FROM
					".TBL_COMMON_USER." cu
					inner join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
					inner join ".TBL_COMMON_COMPANY_DETAIL." ccd on cu.company_id = ccd.company_id
				WHERE
					cu.id=TRIM('".$id."')
					$pass_check
					and ccd.com_type not in ('S','A')
					and cu.mem_div !='S'
					and cu.auth != '4'";
	}

	$db->query($sql);
	$db->fetch();

	if ($db->total > 0 && TRIM($id) != "" && TRIM($pw) != ""){

		echo("<script>parent.document.location.href='./admin_input.php?company_id=".$db->dt[company_id]."&code=".$db->dt[code]."'</script>");
		exit;

	}else{
		
		$sql = "select 
					cu.*,
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
				from
					".TBL_COMMON_USER." cu
					inner join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code
				where
					cu.id=TRIM('".$id."')
					$pass_check
					";
		$db->query($sql);
		$db->fetch();

		if($db->total < '1'){
			$error = "아이디 및 비밀번호를 확인해주세요.";	//확인클릭시 사업자회원가입페이지로 이동(일반회원)
			echo("<script>alert('$error');parent.document.location.href='/admin/member/admin_join.php'</script>");
			exit;
		}else if($db->dt[mem_type] == 'M'){
			$error = "사업자회원만 신청가능합니다.\n 사업자전환을 원하는 회원은 '마이페이지 > 회원전환'에서 전환신청을 해주셔야 합니다.\n 이동하시겠습니까?";	
			//프론토 로그인페이지로 이동
			echo("<script>
				if(confirm('사업자회원만 신청가능합니다. 사업자전환을 원하는 회원은 \'마이페이지 > 회원전환\'에서 전환신청을 해주셔야 합니다. 이동하시겠습니까?')){
					parent.document.location.href='http://$_SERVER[HTTP_HOST]/member/join_agreement.php?join_type=C';
				}else{
					parent.document.location.href='../admin.php';
				}
				</script>");
			exit;
		}else if($db->dt[auth] == '0' && $db->dt[mem_div] == 'S'){
			$error = $db->dt[name]."님 신규협력사 신청을 하셨습니다. 현재 검토 처리중입니다.";	//셀러 승인처리중일경우 auth= 0
			echo("<script>alert('$error');parent.document.location.href='../admin.php'</script>");
			exit;
		}else if($db->dt[request_yn] != 'Y'){
			$error = $db->dt[name]."님 사업자회원 미승인 상태입니다.";	//사업자회원 미승인 상태
			echo("<script>alert('$error');parent.document.location.href='../admin.php'</script>");
			exit;
		}else{
			echo("<script>alert('이미 처리되었습니다.');parent.document.location.href='../admin.php'</script>");
			exit;
		}
	}



}else if($act == "seller_join_input"){

	/* 입점업체 가입 프로세스 
		1. 사업자 회원이고 셀러승인 거부된 회원만 요청가능
		2. 요청시 기존 사업자 정보와 회원정보는 변경이 없고 셀러권한과 회원타입만 변경
	*/

	//두번클릭시 중복가입 방지 처리 시작 2014-06-24 이학봉
	$sql = "select * from common_company_detail where company_id = '".$company_id."'";
	$db->query($sql);
	$db->fetch();
	if($db->dt[com_type] == 'S'){
		echo("<script>alert('이미 셀러가입 하였습니다.');parent.document.location.href='./admin_join.php'</script>");
		exit;
	}
	//두번클릭시 중복가입 방지 처리 끝 2014-06-24 이학봉
	
	if($_FILES[business_file][size] > 0){

	}else{
		echo("<script>alert('사업자등록 파일 오류입니다. 새로운 파일을 등록해주시기 바랍니다.');parent.document.location.href='./admin_join.php'</script>");
		exit;
	}

	$member_reg_rule = GetSharedInfo_daiso("member_reg_rule");

	if($member_reg_rule[seller_auth_type] == "A" ){
		$authorized = "Y";				//가입시 자동승인
		$mem_div = 'S';					//셀러회원
		$com_type = "S";				//거래처 타입 S : 셀러
		$seller_type = '1|2';			//국내매출/국내매입
		$seller_auth = 'Y';				//기초정보 거래처 승인여부
		$request_info = 'S';			//셀러회원으로 요청
		$auth = '4';					//셀러권한부여

	}else{
		$authorized = "Y";				//가입시 수동승인
		$com_type = "S";				//거래처 타입 G : 일반 사업자
		$mem_div = 'S';					//기타구분
		$seller_type = '1|2';			//국내매출/국내매입
		$seller_auth = 'Y';				//기초정보 거래처 승인여부
		$request_info = 'S';			//사업자회원으로 요청
		$auth = '0';					//셀러권한부여
	}

	//1. 사업자정보 변경
	$sql = "update common_company_detail set 
				com_type = '".$com_type."',
				seller_type = '".$seller_type."',
				seller_auth = '".$seller_auth."'
			where
				company_id = '".$company_id."'";
	$db->query($sql);

	//2. 회원정보 변경
	$sql = "update common_user set 
				mem_div = '".$mem_div."',
				auth='".$auth."',
				authorized = '".$authorized."',
				request_info = '".$request_info."'
			where
				code = '".$code."'";
	$db->query($sql);
	//request_yn = '".$request_yn."'
	$sql = "select * from common_seller_detail where company_id = '".$company_id."'";
	$db->query($sql);
	$db->fetch();
	$charge_code = $db->dt[charge_code];

	if(!$charge_code){
		$seller_detail_where = " , charge_code = '".$code."' ";
	}
	
	$sql = "update common_seller_detail set
				seller_cid = '".$seller_cid."',
				seller_msg = '".$seller_msg."',
				md_code = '".$md_code."',
				minishop_templet = 'basic',
				minishop_use = '0'
				$seller_detail_where
			where
				company_id = '".$company_id."'";
	$db->query($sql);

	//셀러기본정책 추가 (정산방식 관련) 2014-06-10 이학봉
	$seller_config = GetSharedInfo_daiso('basic_seller_setup');	//셀러별 기본 수수료 설정 가져오기 (2014-04-07 이학봉)

	$account_info = $seller_config[account_info];
	$ac_delivery_type = $seller_config[ac_delivery_type];
	$ac_expect_date = $seller_config[ac_expect_date];
	$ac_term_div = $seller_config[ac_term_div];
	$ac_term_date1 = $seller_config[ac_term_date1];
	$ac_term_date2 = $seller_config[ac_term_date2];
	$account_type = $seller_config[account_type];
	$account_method = $seller_config[account_method];		//정산 지급 방신 10: 현금 12: 예치금 
	$wholesale_commission = $seller_config[wholesale_commission];
	$commission = $seller_config[commission];
	$seller_grant_use = $seller_config[seller_grant_use];
	$grant_setup_price = $seller_config[grant_setup_price];
	$ac_grant_price = $seller_config[ac_grant_price];
	$account_div = $seller_config[account_div];

	$sql = "select
				count(company_id) as cnt
			from
				common_seller_delivery
			where
				company_id = '".$company_id."'";
	$db->query($sql);
	$db->fetch();
	$delivery_cnt = $db->dt[cnt];

	if($delivery_cnt > 0 ){

		$sql = "update common_seller_delivery set
					account_info = '".$account_info."',
					ac_delivery_type = '".$ac_delivery_type."',
					ac_expect_date = '".$ac_expect_date."',
					ac_term_div = '".$ac_term_div."',
					ac_term_date1 = '".$ac_term_date1."',
					ac_term_date2 = '".$ac_term_date2."',
					account_type = '".$account_type."',
					wholesale_commission = '".$wholesale_commission."',
					commission = '".$commission."',
					seller_grant_use = '".$seller_grant_use."',
					grant_setup_price = '".$grant_setup_price."',
					ac_grant_price = '".$ac_grant_price."',
					account_div = '".$account_div."'
				where
					company_id = '".$company_id."'";
		$db->query($sql);

	}else{

		$sql = "insert into common_seller_delivery set
					company_id = '".$company_id."',
					account_info = '".$account_info."',
					ac_delivery_type = '".$ac_delivery_type."',
					ac_expect_date = '".$ac_expect_date."',
					ac_term_div = '".$ac_term_div."',
					ac_term_date1 = '".$ac_term_date1."',
					ac_term_date2 = '".$ac_term_date2."',
					account_type = '".$account_type."',
					wholesale_commission = '".$wholesale_commission."',
					commission = '".$commission."',
					seller_grant_use = '".$seller_grant_use."',
					grant_setup_price = '".$grant_setup_price."',
					ac_grant_price = '".$ac_grant_price."',
					account_div = '".$account_div."',
					delivery_product_policy = '1'";
		$db->query($sql);
	}
	// 입점업체별 기본 설정 추가 관리 끝 


	//이미지 저장 시작
	$path = $_SERVER["DOCUMENT_ROOT"]."/data/daiso_data/images/basic/".$company_id;
	if(!is_dir($path)){
		exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
	}

	//사업자등록번호 파일
	$file_type = substr(strrchr($business_file_name, '.'), 1);
	//$file_name = "business_file_".$company_id.".".$file_type;
	$file_name = "business_file_".$company_id.".jpg";


	if($_FILES[business_file][size] > 0){
		//copy($business_file,$path."/"."business_file_".$company_id.".".$file_type);
		copy($business_file,$path."/"."business_file_".$company_id.".jpg");
		$sql = "select 
				*
			from
				common_company_file
			where
				sheet_name = 'business_file'
				and company_id = '".$company_id."'";
		$db->query($sql);
		$db->fetch();

		if($db->total > 0){
			$sql = "update common_company_file set
						sheet_value = '$file_name'
					where
						company_id = '".$company_id."'
						and sheet_name = 'business_file'";
			$db->query($sql);

		}else{
			$sql = "insert into common_company_file set
						company_id = '$company_id',
						seq = '1',
						sheet_name = 'business_file',
						sheet_value = '$file_name',
						reg_date = NOW();";
			$db->query($sql);
		}
	}

	//기타 파일
	$file_type = substr(strrchr($other_file_name, '.'), 1); 
	//$file_name = "other_file_".$company_id.".".$file_type;
	$file_name = "other_file_".$company_id.".jpg";
	if($other_file_size > 0){
		//copy($other_file,$path."/"."other_file_".$company_id.".".$file_type);
		copy($other_file,$path."/"."other_file_".$company_id.".jpg");

		$sql = "select 
				*
			from
				common_company_file
			where
				sheet_name = 'other_file'
				and company_id = '".$company_id."'";
		$db->query($sql);
		$db->fetch();
		if($db->total == 0){
			$sql = "insert into common_company_file set
						company_id = '$company_id',
						seq = '1',
						sheet_name = 'other_file',
						sheet_value = '$file_name',
						reg_date = NOW();";
			$db->query($sql);
		}
	}

	echo("<script>alert('셀러가입이 정상적으로 처리되었습니다. ');parent.document.location.href='./admin_join_end.php'</script>");
	exit;




}else if($act == "selectMD"){
	
	if($cid){
		$sql = "select * from shop_category_auth where cid = '".$cid."' and auth_use = '1' limit 0,1";
		$db->query($sql);
		$db->fetch();

		if($db->total > '0'){
			echo $db->dt[access_user];
		}else{
			echo "N";
		}
	}else{
		echo "N";
	}
}

?>