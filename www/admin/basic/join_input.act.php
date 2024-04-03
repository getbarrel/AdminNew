<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/businessLogic.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
@include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");

/**
 * shared memory에서 회원가입 설정 가져오기 12.05.25 bgh  
 */
include_once $_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class";
   
$shmop = new Shared("member_reg_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
if($shmop->getObjectForKey("member_reg_rule")){
    $member_reg_rule = $shmop->getObjectForKey("member_reg_rule");
    $member_reg_rule = unserialize(urldecode($member_reg_rule));
}else{
    $member_reg_rule = "";
}


$bl = new BusinessLogic();


//session_start();

if($regist_type != "mallstory") {

	if($mail_02 == "" || $mail_02 == "etc") {
		$mail = $mail_01."@".$mail_03;
	} else {
		$mail = $mail_01."@".$mail_02;
	}
}

if($add_etc2_1!="") {
	$add_etc2=$add_etc2_1."-".$add_etc2_2."-".$add_etc2_3;
}

$regist_info[com_name]=$com_name;
$regist_info[com_num1]=$com_num1;
$regist_info[com_num2]=$com_num2;
$regist_info[com_num3]=$com_num3;
$regist_info[com_phone1]=$com_phone1;
$regist_info[com_phone2]=$com_phone2;
$regist_info[com_phone3]=$com_phone3;
$regist_info[com_fax1]=$com_fax1;
$regist_info[com_fax2]=$com_fax2;
$regist_info[com_fax3]=$com_fax3;
$regist_info[com_ceo]=$com_ceo;
$regist_info[com_business_status]=$com_business_status;
$regist_info[com_business_category]=$com_business_category;
$regist_info[com_zip1]=$com_zip1;
$regist_info[com_zip2]=$com_zip2;
$regist_info[com_addr1]=$com_addr1;
$regist_info[com_addr2]=$com_addr2;
$regist_info[online_business_number]=$online_business_number;
$regist_info[com_homepage]=$com_homepage;


$regist_info[id]=$id;
$regist_info[name]=$name;
$regist_info[mail]=$mail;
$regist_info[addr1]=$addr1;
$regist_info[addr2]=$addr2;
$regist_info[comp]=$comp;
$regist_info["class"]=$class;
$regist_info[zipcode1]=$zipcode1;
$regist_info[zipcode2]=$zipcode2;
$regist_info[tel1]=$tel1;
$regist_info[tel2]=$tel2;
$regist_info[tel3]=$tel3;
$regist_info[tel_div]=$tel_div;
$regist_info[pcs1]=$pcs1;
$regist_info[pcs2]=$pcs2;
$regist_info[pcs3]=$pcs3;
$regist_info[recom_id]=$recom_id;

$regist_info[czipcode1]=$czipcode1;
$regist_info[caddr1]=$caddr1;
$regist_info[caddr2]=$caddr2;
$regist_info[marriage_yn]=$marriage_yn;

$regist_info[birthday_div]=$birthday_div;
$regist_info[birthday_yyyy]=$birthday1;
$regist_info[birthday_mm]=$birthday2;
$regist_info[birthday_dd]=$birthday3;

$regist_info[add_etc1]=$add_etc1;
$regist_info[add_etc2]=$add_etc2;
$regist_info[add_etc3]=$add_etc3;
$regist_info[add_etc4]=$add_etc4;
$regist_info[add_etc5]=$add_etc5;
$regist_info[add_etc6]=$add_etc6;

/*
$regist_info[f_dica]=$f_dica;
$regist_info[f_dicam]=$f_dicam;
$regist_info[f_editcard]=$f_editcard;
$regist_info[f_software]=$f_software;
$regist_info[f_multimedia]=$f_multimedia;
$regist_info[f_community]=$f_community;
$regist_info[f_education]=$f_education;
$regist_info[f_solution]=$f_solution;
$regist_info[f_help]=$f_help;

*/

session_register("regist_info");

$db = new Database;


if ($act == "idcheck")
{
	$deny_id = array("test","admin", "master", "police", "webmaster", "help", "cancel", "service", "avarta", "regist", "pay", "administrator", "fuck", "sex", "roopy", "center", "email", "monitor", "helpdesk", "doumi","helpdesk", "operate", "operator", "message", "menu", "member", "poll", "point", "communication", "comment", "manager", "management", "plan", "planning", "partner", "board", "notice", "dosirak", "dosirack", "naraadmin", "http", "ftp", "telnet", "administrator", "root", "www", "widget", "trackback", "tag", "spamfilter", "session", "rss", "page", "opage", "module", "layout", "krzip", "integration_search", "install", "importer", "file", "editor", "document", "counter", "autoinstall", "addon");

	$db->query("select mall_deny_id from shop_shopinfo where mall_ix = '".$layout_config[mall_ix]."' and mall_div = '".$layout_config[mall_div]."' ");
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

if ($act == "mailcheck")
{
	$deny_mail = array("admin@forbiz.co.kr","admin@mallstory.com");

	for($i=0;$i<count($deny_mail);$i++){
		if(trim($mail) == $deny_mail[$i]){
			//echo "<script>alert('가입불가 ID입니다. 다른 ID로 입력해주시기 바랍니다.')</script>";
			echo "X";
			exit;
		}
	}

	$total = 0;

	$db->query("SELECT * FROM ".TBL_COMMON_MEMBER_DETAIL." WHERE mail=HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'))");
	$total = $db->total;
	//$db->query("SELECT * FROM ".TBL_SHOP_DROPMEMBER." WHERE id='$mail'");
	//$total = $total + $db->total;

	if ($total)
	{
		//echo("<script>alert('이미 등록된 [아이디]입니다.');</script>");
		//echo("<script>top.join_frm.id.dup_check = false;</script>");
		echo "N";
		exit;
	}
	else
	{
		//echo("<script>alert('사용 가능한 [아이디]입니다.');</script>");
		//echo("<script>top.join_frm.id.dup_check = true;</script>");
		echo "Y";
		exit;
	}
}

if ($act == "regist")
{

	$id    = trim($id);
	$pass  = trim($pass);
	$pass = hash("sha256", $pass);


	$mem_name  = trim($name);
    
    $safeKey = ""; //사용전 초기화
    if($use_ipin == "Y"){
        if($_SESSION[ipin][safeKey] != "" || $_SESSION[ipin][safeKey] != null){
            $mem_name = $_SESSION[ipin][niceNm];
            $safeKey = $_SESSION[ipin][safeKey];
        }else{
            echo("<script>alert('아이핀 세션정보가 없습니다.');location.href='/member/join_agreement.php';</script>");
        }
        
    }else if($use_niceid == "Y"){
        if($_SESSION[niceid][safeKey] != "" || $_SESSION[niceid][safeKey] != null){
            $mem_name = $_SESSION[niceid][name];
            $safeKey = $_SESSION[niceid][safeKey];
            $niceid_di = $_SESSION[niceid][ipin_di];
        }else{
            echo("<script>alert('안심체크 세션정보가 없습니다.');location.href='/member/join_agreement.php';</script>");
        }
    }

	$nick_name  = trim($nick_name);
	//$mail  = trim($mail1."@".$mail2);
	$addr1 = trim($addr1); // 주소는 칼럼이 분리되어 있어서 한줄로 받아도 상관없음
	$addr2 = trim($addr2);
	$comp  = trim($comp);
	$class = trim($class);

	$birthday=$birthday1."-".$birthday2."-".$birthday3;
    /**
     * 우편번호, 전화번호, 한줄로 받을경우 대쉬(-)사용 안하도록 변경.
     */
    if($zipcode2 != "" || $zipcode2 != NULL){
        $zip = "$zipcode1-$zipcode2";
    }else{
        $zip = $zipcode1; 
    }
    
    if($tel2 != "" || $tel2 != NULL){
        $tel   = "$tel1-$tel2-$tel3";    
    }
	else{
	   $tel = $tel1;
	}
    
    if($pcs2 != "" || $pcs2 != NULL){
	   $pcs   = "$pcs1-$pcs2-$pcs3";
    }else{
        $pcs = $pcs1;
    }
    
	$code  = md5(uniqid(rand()));
	$rsl_id = trim($rsl_id);



	/*
	$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id = '$id'");

	if ($db->total)
	{
		echo("<script>alert('[$id]이란 아이디를 그새 누가 등록했네요. T_T');</script>");
		echo("<script>history.back();</script>");
		exit;
	}
	*/


	if($birthday_yyyy) $birthday = $birthday_yyyy.$birthday_mm.$birthday_dd;


	/**
     * 승인타입 설정 12.05.25 bgh
     */
    if(is_array($member_reg_rule)){
        if($member_reg_rule[mall_open_yn] == "Y"){ //회원전용
        	if($member_reg_rule[auth_type] == "A" ){
				$authorized = "Y";// 가입시 자동승인
			}else{
				$authorized = "N";// 가입시 비슷인
			}
        }else{
            $authorized = "Y";// 가입시 자동승인
        }
    }else{//회원가입 관련 관리자 설정 정보가 없을때.
       $authorized = "Y";
    }
    
    /**
     * 이메일 인증 사용시 12.05.29 bgh
     * @param email_activation : 회원가입 폼에서 넘김
     */
	if($member_reg_rule[email_auth]=='Y') { // 개정법안 수정 kbk 13/02/19
		$email_activation = true;
	} else {
		if(empty($email_activation)) $email_activation = false;
	}
    if($email_activation == true){
        $is_id_auth = "N";
    }else{
        $is_id_auth = "Y";
    }
    
	if($regist_type == "company" || $regist_type == "seller"){
		//if($mem_level == ""){
			//$mem_level = "M";
			$gp_ix = "1";
		//}
		$company_id  = md5(uniqid(rand()));
        $activation_code = md5(uniqid(rand()));

		if($db->dbms_type=='oracle'){
			$sql = "INSERT INTO ".TBL_COMMON_USER."
					(code, id, pw, mem_type, date_, visit, last, ip, company_id, authorized, auth, activation_code, is_id_auth,is_pos_link,join_status)
					VALUES
					('$code','$id','".$pass."','C',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id','$authorized',1, '$activation_code', '$is_id_auth','N','I')";
		}else{
			$sql = "INSERT INTO ".TBL_COMMON_USER."
					(code, id, pw, mem_type, date, visit, last, ip, company_id, authorized, auth, activation_code, is_id_auth,is_pos_link,join_status)
					VALUES
					('$code','$id','".$pass."','C',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id','$authorized',1, '$activation_code', '$is_id_auth','N','I')";
		}
		
		$db->query($sql);
        
        
        /**
         * 아이핀 safeKey로 가입할때 12.06.05 bgh
         */
        if($use_ipin == "Y"){
			if($db->dbms_type=='oracle'){
				$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code, ipin_safekey, birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date_, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code','$safeKey','$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
			}else{
				$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code, ipin_safekey, birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code','$safeKey','$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
			}
        /**
         * 안심체크로 가입할때 12.06.12 bgh
         */                
        }else if($use_niceid == "Y"){
			 if($db->dbms_type=='oracle'){
				$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code, niceid_safekey, niceid_di, birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date_, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code','$safeKey','$niceid_di','$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";    
			 }else{
				$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code, niceid_safekey, niceid_di, birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code','$safeKey','$niceid_di','$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";    
			 }
        }else{
			 if($db->dbms_type=='oracle'){
					$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date_, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code',HEX(AES_ENCRYPT('".$db->ase_encrypt_key."')),'$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
			 }else{
					$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code',HEX(AES_ENCRYPT('".$db->ase_encrypt_key."')),'$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
			 }           
        }
		$db->query($sql);


		$sql = "INSERT INTO ".TBL_COMMON_COMPANY_DETAIL."
						(company_id, com_name, com_ceo, com_business_status, com_business_category, online_business_number, com_number, com_phone, com_fax, com_zip, com_addr1, com_addr2, com_homepage)
						VALUES
						('$company_id','$com_name', '$com_ceo', '$com_business_status', '$com_business_category', '$online_business_number', '$com_num1-$com_num2-$com_num3','$com_phone1-$com_phone2-$com_phone3','$com_fax1-$com_fax2-$com_fax3', '$com_zip1-$com_zip2', '$com_addr1', '$com_addr2', '$com_homepage')";

		$db->query($sql);

		if($regist_type == "seller"){
			$sql = "INSERT INTO common_seller_detail SET
						company_id = '$company_id',
						shop_name = '$shop_name',
						md_code = '$md_code',
						team = '$team',
						bank_owner = '$bank_owner',
						bank_name = '$bank_name',
						bank_number = '$bank_number',
						member_category = '$member_category',
						regdate = now()
						$bank_file_str
						$ktp_file_str
						";

			$db->query($sql);
			
			$sql = "INSERT INTO common_seller_delivery SET
						company_id = '$company_id',
						commission = '0',
						delivery_policy = '1',
						delivery_basic_policy = '2',
						delivery_price = '2500',
						delivery_freeprice = '30000',
						delivery_free_policy = '1',
						delivery_product_policy = '3',
						regdate = now()
						";

			$db->query($sql);
		}



    }else if($regist_type == "foreigner"){
        //if($mem_level == ""){
			//$mem_level = "M";
			$gp_ix = "1";
		//}
		$company_id  = md5(uniqid(rand()));
        $activation_code = md5(uniqid(rand()));
	
		if($db->dbms_type=='oracle'){
			$sql = "INSERT INTO ".TBL_COMMON_USER."
					(code, id, pw, mem_type, date_, visit, last, ip, company_id, authorized, auth, activation_code, is_id_auth,is_pos_link,join_status)
					VALUES
					('$code','$id','".$pass."','F',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id','$authorized',1, '$activation_code', '$is_id_auth','N','I')";
		}else{
			$sql = "INSERT INTO ".TBL_COMMON_USER."
					(code, id, pw, mem_type, date, visit, last, ip, company_id, authorized, auth, activation_code, is_id_auth,is_pos_link,join_status)
					VALUES
					('$code','$id','".$pass."','F',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id','$authorized',1, '$activation_code', '$is_id_auth','N','I')";
		}
		$db->query($sql);

		 if($db->dbms_type=='oracle'){
			$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date_, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code',HEX(AES_ENCRYPT('".$db->ase_encrypt_key."')),'$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
		 }else{
			$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code',HEX(AES_ENCRYPT('".$db->ase_encrypt_key."')),'$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
		 }
		$db->query($sql);


		$sql = "INSERT INTO ".TBL_COMMON_COMPANY_DETAIL."
						(company_id, com_name, com_ceo, com_business_status, com_business_category, online_business_number, com_number, com_phone, com_fax, com_zip, com_addr1, com_addr2)
						VALUES
						('$company_id','$com_name', '$com_ceo', '$com_business_status', '$com_business_category', '$online_business_number', '$com_num1-$com_num2-$com_num3','$com_phone1-$com_phone2-$com_phone3','$com_fax1-$com_fax2-$com_fax3', '$com_zip1-$com_zip2', '$com_addr1', '$com_addr2')";

		$db->query($sql);


	}else{
		/*if($mem_level == ""){
			$mem_level = "M";
		}*/
		$company_id  = md5(uniqid(rand()));
        $activation_code = md5(uniqid(rand()));
		$gp_ix = "1";

		
		if($db->dbms_type=='oracle'){
			$sql = "INSERT INTO ".TBL_COMMON_USER."
					(code, id, pw, mem_type, date_, visit, last, ip, company_id, authorized, auth, activation_code, is_id_auth, is_pos_link, join_status)
					VALUES
					('$code','$id','".$pass."','M',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id','$authorized',1, '$activation_code', '$is_id_auth','N','I')";
		}else{
			$sql = "INSERT INTO ".TBL_COMMON_USER."
					(code, id, pw, mem_type, date, visit, last, ip, company_id, authorized, auth, activation_code, is_id_auth, is_pos_link, join_status)
					VALUES
					('$code','$id','".$pass."','M',NOW(),'0',NOW(),'$REMOTE_ADDR','$company_id','$authorized',1, '$activation_code', '$is_id_auth',,'N','I')";
		}

		$db->query($sql);
        /**
         * 아이핀 safeKey로 가입할때 12.06.05 bgh
         */
        if($use_ipin == "Y"){
			 if($db->dbms_type=='oracle'){
				$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code, ipin_safekey, birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date_, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code','$safeKey','$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
			 }else{
				$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code, ipin_safekey, birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code','$safeKey','$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
			 }
        /**
         * 안심체크로 가입할때 12.06.12 bgh
         */                
        }else if($use_niceid == "Y"){
			if($db->dbms_type=='oracle'){
				 $sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code, niceid_safekey, niceid_di, birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date_, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code','$safeKey','$niceid_di','$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";    
			  }else{
					$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code, niceid_safekey, niceid_di, birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code','$safeKey','$niceid_di','$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";    
			  }
        }else{
			if($db->dbms_type=='oracle'){
					$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date_, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code',HEX(AES_ENCRYPT('".$db->ase_encrypt_key."')),'$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
			}else{
					$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code,  birthday, birthday_div, name, mail, zip, addr1, addr2, tel,tel_div, pcs, info, sms, nick_name, job, date, recom_id, gp_ix,  sex_div,add_etc1, add_etc2, add_etc3, add_etc4, add_etc5, add_etc6)
						VALUES
						('$code',HEX(AES_ENCRYPT('".$db->ase_encrypt_key."')),'$birthday','$birthday_div',HEX(AES_ENCRYPT('$mem_name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$zip','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr1','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$addr2','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),'$tel_div',HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),'$info','$sms', '$nick_name', '$job',NOW(),'$recom_id','$gp_ix','$sex_div', '$add_etc1', '$add_etc2', '$add_etc3', '$add_etc4', '$add_etc5', '$add_etc6')";
			}
        }
		$db->query($sql);

		$sql = "INSERT INTO ".TBL_COMMON_COMPANY_DETAIL."
						(company_id, com_name, com_ceo, com_business_status, com_business_category, com_number, com_phone, com_fax, com_zip, com_addr1, com_addr2, com_homepage)
						VALUES
						('$company_id','$com_name', '$com_ceo', '$com_business_status', '$com_business_category', '$com_num1-$com_num2-$com_num3','$com_phone1-$com_phone2-$com_phone3','$com_fax1-$com_fax2-$com_fax3', '$com_zip1-$com_zip2', '$com_addr1', '$com_addr2', '$com_homepage')";

		$db->query($sql);

	}
	
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");


	// ------------------------ 리셀러 START --------------------------

	$shmop = new Shared("reseller_rule");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$reseller_data = $shmop->getObjectForKey("reseller_rule");
	$reseller_data = unserialize(urldecode($reseller_data));

	if($reseller_data[rsl_use] == "y"){	//리셀러 마케팅을 사용 할때
	
		if($_COOKIE[rsl_id]){
			$rsl_id = $_COOKIE[rsl_id];
			$flowin_type = $_COOKIE[flowin_type];
		}else{
			$rsl_id = $recom_id;
			$flowin_type = 3;
		}
		
		$flowin_url=$_COOKIE[flowin_url];

		if($rsl_id){
			
			$sql = "SELECT code as rsl_code FROM ".TBL_COMMON_USER." INNER JOIN ".TBL_COMMON_MEMBER_DETAIL." using (code) WHERE id='$rsl_id'";
			$db->query($sql);
			$db->fetch();

			$rsl_code=$db->dt[rsl_code];

			$db->query("SELECT id reseller_dropmember where rsl_code='".$rsl_code."' ");
			
			if(!$db->total){//탈퇴한 리셀러가 아니라면

				if($rsl_code){  //리셀러 유입자 정보 저장
					$sql = "INSERT INTO reseller_flowin_detail (rsl_code,flowin_code, flowin_url, flowin_type, regdate) VALUES ('$rsl_code','$code','$flowin_url','$flowin_type',NOW()) ";
					$db->query($sql);			
			
					$sql = "select new_incentive_type, new_incentive from reseller_policy where rsl_code ='$rsl_code' "; //리셀러 인센티브 설정 정보 가지고 오기
					$db->query($sql);
					
					$new_incentive_type = $db->dt[new_incentive_type];
					$new_incentive = $db->dt[new_incentive];

					if($new_incentive_type == "2"){
						$sql = "INSERT INTO reseller_incentive (rsl_code,flowin_code, regdate, incentive, incentive_type) VALUES ('$rsl_code','$code',NOW(),'$new_incentive','1') "; 
						$db->query($sql);																						//incentive_type=1 이면 가입으로 인한 인센티브

					}
				}
			}
		}
	}
	//---------------------------- END -------------------------


	/*include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
	$shmop = new Shared("reserve_rule");
	$reserve_data = $shmop->getObjectForKey("reserve_rule");
	$reserve_data = unserialize(urldecode($reserve_data));*/
	
	$shmop = new Shared("reserve_rule");
	//$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$P->Config["mall_data_root"]."/_shared/";
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/_shared/";// 경로를 $p 클래스에서 가져오기에 에러 발생 kbk 12/04/06
	$shmop->SetFilePath();
	$reserve_data = $shmop->getObjectForKey("reserve_rule");
	$reserve_data = unserialize(urldecode($reserve_data));
	if($reserve_data[reserve_use_yn] == "Y" && $reserve_data[join_reserve_rate] > 0){
		$db->query("INSERT INTO ".TBL_SHOP_RESERVE_INFO." (id,uid,oid,pid,ptprice,payprice,reserve,state,etc,regdate,is_pos_link) VALUES ('','$code','','','0','0','".$reserve_data[join_reserve_rate]."','1','회원가입 축하 지급 적립금',NOW(),'N')");
		
	}

	$bl->MemberRegLogic($code,6);


//	$path = $_SERVER["DOCUMENT_ROOT"]."/mailing/mailing_join.php";
//	$email_card_contents_basic = join ('', file ($path));

	$mail_info[mem_name] = $mem_name;
	$mail_info[mem_mail] = $mail;
	$mail_info[mem_id] = $id;
	$mail_info[mem_mobile] = $pcs;

	//$subject = " ".$mem_name." 님, 회원가입을 진심으로 축하드립니다.";
	//SendMail($mail_info, $subject,$email_card_contents_basic);
	
    /**
     *  이메일인증 사용하기 -> 우선 외국인 회원가입폼에서 가져오게 해놨음. 12.5.22 bgh
     */
    //$email_activation = true;
	
    if($email_activation == true){
	   $mail_info[activation_code] = $activation_code;
       sendMessageByStep('member_reg_activation', $mail_info);
	}else{
       sendMessageByStep('member_reg', $mail_info);
    }


    //if (file_exists($_SERVER["DOCUMENT_ROOT"].$P->Config["mall_data_root"]."/_shared/coupon_rule")){
    if (file_exists($_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/_shared/coupon_rule")){// 경로를 $p 클래스에서 가져오기에 에러 발생 kbk 12/04/06
    	// 쿠폰 정책 설정에 따른 쿠폰 지급
    
    	/*$shmop = new Shared("coupon_rule");
    	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$layout_config["mall_data_root"]."/_shared/";
    	$shmop->SetFilePath();
    	$coupon_data = $shmop->getObjectForKey("coupon_rule");
    	$coupon_data = unserialize(urldecode($coupon_data));*/
    	$shmop = new Shared("coupon_rule");
    	//$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$P->Config["mall_data_root"]."/_shared/";
    	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/_shared/";// 경로를 $p 클래스에서 가져오기에 에러 발생 kbk 12/04/06
    	$shmop->SetFilePath();
    	$coupon_data = $shmop->getObjectForKey("coupon_rule");
    
    	$coupon_data = unserialize(urldecode($coupon_data));
    	//echo $coupon_data[coupon_use_yn].":::".$coupon_data[member_publish_ix] ;
    	if($coupon_data[coupon_use_yn] == "Y" && $coupon_data[member_publish_ix]){
    		$sql = "Select publish_ix,use_date_type,publish_date_differ,publish_type,publish_date_type ,regist_date_type, regist_date_differ, use_sdate, use_edate
    				from ".TBL_SHOP_CUPON_PUBLISH."
    				where publish_ix = '".$coupon_data[member_publish_ix]."'";
    		$db->query($sql);
    		$db->fetch();
    		$publish_ix = $db->dt[publish_ix];
    		//print_r($_POST);
    		//echo $db->dt[use_date_type].$db->dt[publish_date_type];
    		//exit;
    		if($db->dt[use_date_type] == 1){
    			if($db->dt[publish_date_type] == 1){
    				$publish_year = date("Y") + $db->dt[publish_date_differ];
    			}else{
    				$publish_year = date("Y");
    			}
    			if($db->dt[publish_date_type] == 2){
    				$publish_month = date("m") + $db->dt[publish_date_differ];
    			}else{
    				$publish_month = date("m");
    			}
    			if($db->dt[publish_date_type] == 3){
    				$publish_day = date("d") + $db->dt[publish_date_differ];
    			}else{
    				$publish_day = date("d");
    			}
    			$use_sdate = time();
    			$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);
    
    			//$event_date = mktime(0,0,0,$event_meonth,$event_day+$order[end_date_differ],$evet_year);
    
    		}else if($db->dt[use_date_type] == 2){
    			if($db->dt[regist_date_type] == 1){
    				$regist_year = date("Y") + $db->dt[regist_date_differ];
    			}else{
    				$regist_year = date("Y");
    			}
    			if($db->dt[regist_date_type] == 2){
    				$regist_month = date("m") + $db->dt[regist_date_differ];
    			}else{
    				$regist_month = date("m");
    			}
    			if($db->dt[regist_date_type] == 3){
    				$regist_day = date("d") + $db->dt[regist_date_differ];
    			}else{
    				$regist_day = date("d");
    			}
    			$use_sdate = time();
    			$use_date_limit = mktime(0,0,0,$regist_month,$regist_day,$regist_year);
    		}else if($db->dt[use_date_type] == 3){
    			$use_sdate = mktime(0,0,0,substr($db->dt[use_sdate],4,2),substr($db->dt[use_sdate],6,2),substr($db->dt[use_sdate],0,4));
    			$use_date_limit = mktime(0,0,0,substr($db->dt[use_edate],4,2),substr($db->dt[use_edate],6,2),substr($db->dt[use_edate],0,4));
    
    		}
    
    		//if($db->dt[publish_type] == "1" || $db->dt[publish_type] == "2"){
    		if($db->dt[publish_type] == "3"){//회원가입전용 쿠폰일 경우만 kbk 12/06/13
    			$use_sdate = date("Ymd",$use_sdate);
    			$use_date_limit = date("Ymd",$use_date_limit);
    
    			$db->query("Select publish_ix from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix' and mem_ix = '".$code."' ");
    
    			if(!$db->total){
    				$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST." (regist_ix,publish_ix,mem_ix,open_yn,use_yn,use_sdate, use_date_limit, regdate)
    						values
    						('','".$coupon_data[member_publish_ix]."','".$code."','1','0','$use_sdate','$use_date_limit',NOW())";
    
    				//echo $sql2;
    
    				$db->query($sql2);
    			}
    		}
    	}
    }
    
    /**
     *  도매몰(루) 가입시 문의게시판에 자동으로 글 하나 생성하기 위한 루틴 시작
     */
    if($_SERVER['HTTP_HOST'] == "lulu.s2.mallstory.com" || $_SERVER['HTTP_HOST'] == "lub2b.co.kr" || $_SERVER['HTTP_HOST'] == "www.lub2b.co.kr"){
        
        $bbs_div = '46'; // 기타문의
        $bbs_subject = "test subject";
        $bbs_name = $mem_name;
        $bbs_email = $mail;
        $bbs_contents = "test";
        $bbs_hidden = "0";
        $is_html = "Y";
        $bbs_etc1 = "1"; //이메일 수신여부
        $bbs_etc2 = $pcs; //휴대폰번호
        $bbs_etc3 = "1"; //문자수신여부
        
        $db->query("select IFNULL(max(bbs_ix),0) as bbs_ix from bbs_qna");
    	if($db->total){
    		$db->fetch();
    		$bbs_ix = $db->dt[bbs_ix] + 1;
    	}else{
    		$bbs_ix = 0;
    	}
        if ($bbs_parent_ix == ""){
    		$bbs_parent_ix = 0;
    	}
    	if ($bbs_ix_level == ""){
    		$bbs_ix_level = 0;
    	}
        
        $sql = "insert into bbs_qna (bbs_ix,bbs_div,sub_bbs_div, mem_ix,bbs_subject,bbs_name,bbs_pass,bbs_email,bbs_contents,bbs_top_ix, bbs_ix_level, bbs_ix_step, bbs_hidden, bbs_file_1,bbs_file_2,bbs_file_3, bbs_etc1,bbs_etc2,bbs_etc3,bbs_etc4,bbs_etc5,is_notice, is_html, ip_addr, status, regdate)
			values
			('','$bbs_div','$sub_bbs_div','$code','$bbs_subject','$bbs_name','$bbs_pass','$bbs_email','$bbs_contents','$bbs_ix','$bbs_ix_level','$bbs_ix_step','$bbs_hidden','$bbs_file_1_name','$bbs_file_2_name','$bbs_file_3_name','$bbs_etc1','$bbs_etc2','$bbs_etc3','$bbs_etc4','$bbs_etc5','$is_notice','$is_html','".$_SERVER["REMOTE_ADDR"]."','$status',NOW())";

    	$db->query($sql);
    }
    /**
     * 도매몰(루) 가입시 문의게시판에 자동으로 글 하나 생성하기 위한 루틴 끝
     */
  
    session_unregister("ipin");
    session_unregister("niceid");
	session_unregister("regist_info");
	//echo("<script>alert('등록해 주셔서 감사합니다.');</script>");
	//echo("<script>location.href = 'join_end2.php';</script>");
	if($mode == "modal")	{
		echo("<script>alert('등록해 주셔서 감사합니다.');</script>");
		echo("<script>top.location.reload();</script>");
	}else if($email_activation == true){
	   echo "
		<html>
		<head></head>
		<body>
			<form name='sForm' method='post' target='_parent' action='join_end2.php?mode=ea'>
				<input type='hidden' name='sname' value='$name' />
				<input type='hidden' name='sid' value='$id' />
                <input type='hidden' name='smail' value='$mail' />
			</form>
		</body>
		<script type='text/javascript'>
			document.sForm.submit();
		</script>
		</html>
		";
    }else{
		echo "
		<html>
		<head></head>
		<body>
			<form name='sForm' method='post' target='_parent' action='join_end2.php'>
				<input type='hidden' name='sname' value='$name' />
				<input type='hidden' name='sid' value='$id' />
			</form>
		</body>
		<script type='text/javascript'>
			document.sForm.submit();
		</script>
		</html>
		";
	}
//	session_unregister("regist_info");
}
?>
