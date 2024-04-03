<?
include("../../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("./company.lib.php");

$db = new Database;

if ($act == "insert")
{
	if($info_type == "basic"){
		
		$relation_code = trim($_REQUEST[cid2]);	// 선택한 사업소 코드
		$company_group = trim($_REQUEST[company_group]);
		$group_code = trim($_REQUEST[group_code]);
		$group_name = trim($_REQUEST[group_name]);
		$seq = trim($_REQUEST[seq]);
		$disp = trim($_REQUEST[disp]);
		$company_id = $admininfo['company_id'];

		if($company_group == "D"){
			if(!$department_group){
				//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('비정상적인 본부 코드입니다. 다시 입력해 주십시오.');</script>");
				echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&info_type=".$info_type."';</script>");
			}
		}
		if($company_group == "C"){
		$sql = "
				insert into 
					".TBL_SHOP_COMPANY_GROUP."  set
				company_id = '".$company_id."',
				group_code = '".$group_code."',
				company_group = '".$company_group."',
				group_name = '".$group_name."',
				seq = '".$seq."',
				disp = '".$disp."',
				reg_date = NOW();
		";
		
		}else {
			$sql = "
				insert into 
					".TBL_SHOP_COMPANY_DEPARTMENT."  set
				company_id = '".$company_id."',
				group_ix = '".$department_group."',
				group_code = '".$group_code."',
				company_group = '".$company_group."',
				dp_name = '".$group_name."',
				seq = '".$seq."',
				disp = '".$disp."',
				regdate = NOW();
				";
		}
		$db->query($sql);
		$insert_id = $db->insert_id();
			
			if($insert_id){
				//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
				if($url == "store"){
					echo("<script>parent.document.location.href = '../store/department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
				}else{
					echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
				}
			}else{
				//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되지 않았습니다..');</script>");
				echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
			}
		
	}else if($info_type == "post_info"){
			$sql = "
				insert into ".TBL_SHOP_COMPANY_POSITION." set
					ps_name = '".$ps_name."',
					seq = '".$seq."',
					disp = '".$disp."',
					regdate = NOW();
			";
			$db->query($sql);
			$insert_id = $db->insert_id();

			if($insert_id){
				//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
				if($url == "store"){
					echo("<script>parent.document.location.href = '../store/department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
				}else{
					echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
				}
			}else{
				//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되지 않았습니다..');</script>");
				echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
				
			}
	}else if($info_type == "position_info"){

			$sql = "
				insert into ".TBL_SHOP_COMPANY_DUTY." set
					duty_name = '".$duty_name."',
					seq = '".$seq."',
					disp = '".$disp."',
					reg_date = NOW();
			";
			$db->query($sql);
			$insert_id = $db->insert_id();

			if($insert_id){
				//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
				if($url == "store"){
					echo("<script>parent.document.location.href = '../store/department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
				}else{
					echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
				}
			}else{
				//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되지 않았습니다..');</script>");
				echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
			}

	}

}

if ($act == "update")
{	

	if($info_type == "basic"){

		if($company_group == "C"){
		$sql = "update 
					".TBL_SHOP_COMPANY_GROUP."  set
				company_group = '".$company_group."',
				group_name = '".$group_name."',
				seq = '".$seq."',
				disp = '".$disp."',
				edit_date = NOW()
				where
					group_ix = '".$group_ix."'
		";
		}else {
			$sql = "update
					".TBL_SHOP_COMPANY_DEPARTMENT."  set
				group_ix = '".$department_group."',
				dp_name = '".$group_name."',
				seq = '".$seq."',
				disp = '".$disp."',
				edit_date = NOW()
				where
					dp_ix = '".$dp_ix."'
				";
		}

		$db->query($sql);

		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되지 않았습니다..');</script>");
		if($url == "store"){
			echo("<script>parent.document.location.href = '../store/department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
		}else{
			echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
		}

	}else if($info_type == "post_info"){

		if($ps_ix){
			$sql = "update ".TBL_SHOP_COMPANY_POSITION."
					set
						ps_name = '".$ps_name."',
						seq = '".$seq."',
						disp = '".$disp."',
						edit_date = NOW()
					where
						ps_ix = '".$ps_ix."'
			";

			$db->query($sql);

			if($url == "store"){
					echo("<script>parent.document.location.href = '../store/department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
				}else{
					echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
				}
		}
			
	}else if($info_type == "position_info"){
	
		if($cu_ix){
			$sql = "update ".TBL_SHOP_COMPANY_DUTY."
					set
						duty_name = '".$duty_name."',
						seq = '".$seq."',
						disp = '".$disp."',
						edit_date = NOW()
						where
						cu_ix = '".$cu_ix."'
			";

			$db->query($sql);
			
			if($url == "store"){
					echo("<script>parent.document.location.href = '../store/department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
				}else{
					echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
				}
		}

	}

	if($admininfo[admin_level] == 9){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
	}else if($admininfo[admin_level] == 8){
		//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$admininfo[company_id]."';</script>");
	}

}

if($act == "select_update"){

	foreach($code as $key =>$value){
		
		if($key == "group"){
			foreach($value as $cnt =>$ix){
				$db->query("update ".TBL_SHOP_COMPANY_GROUP." set disp = '".$use_disp."' where group_ix = '".$ix."'");
			}
		}
		if($key == "department"){
			foreach($value as $cnt =>$ix){
				$db->query("update ".TBL_SHOP_COMPANY_DEPARTMENT." set disp = '".$use_disp."' where dp_ix = '".$ix."'");
			}
		}
		if($key == "position"){
			foreach($value as $cnt =>$ix){
				$db->query("update ".TBL_SHOP_COMPANY_POSITION." set disp = '".$use_disp."' where ps_ix = '".$ix."'");
			}
		}
		if($key == "duty"){
			foreach($value as $cnt =>$ix){
				$db->query("update ".TBL_SHOP_COMPANY_DUTY." set disp = '".$use_disp."' where cu_ix = '".$ix."'");
			}
		}
	}

	//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'department.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
	
	
}

if ($act == "recommend")
{
	$sql = "UPDATE ".TBL_COMMON_COMPANY_DETAIL." SET
			recommend='$recomm'
			WHERE company_id='$company_id'"; // 이름에 대한 수정을 없앰 kbk
			// 회원과 회사 정보는 1:다 관계 이므로 code 값을 company_id 로 변경

	$db->query($sql);

	//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
	echo("<script>top.document.location.reload();</script>");
}


if($act == "delete"){

	if($group_type == "C"){
		$sql ="select
				count(*) as cnt
			from
				".TBL_SHOP_COMPANY_DEPARTMENT."
				where
					group_ix = '".$group_ix."'
		";

		$db->query($sql);
		$db->fetch();
		$department_cnt = $db->dt[cnt];

	if($department_cnt > 0){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('하위 부서가 존재합니다.');</script>");
		echo("<script>parent.document.location.href = 'department.add.php?info_type=".$info_type."';</script>");
		exit;
	}{
		$sql = "delete from ".TBL_SHOP_COMPANY_GROUP." where group_ix ='$group_ix'";
		//echo $sql;
		$db->query($sql);
	}
	}else if($group_type == "D"){
		$sql = "delete from ".TBL_SHOP_COMPANY_DEPARTMENT." where dp_ix ='$group_ix'";
		//echo $sql;
		$db->query($sql);
	}else if($group_type == "P"){
		$sql = "delete from ".TBL_SHOP_COMPANY_POSITION." where ps_ix ='$ps_ix'";
		//echo $sql;
		$db->query($sql);
	}else if($group_type == "T"){
		$sql = "delete from ".TBL_SHOP_COMPANY_DUTY." where cu_ix ='$cu_ix'";
		//echo $sql;
		$db->query($sql);
	}

	//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');</script>");
	echo("<script>location.href = 'department.add.php?info_type=".$info_type."&mmode=".$mmode."&';</script>");
	echo $delivery_price;

}


if($act == "user_insert"){

	//$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE company_id = '$company_id' and id = '$id' ");
	$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id = '$id' ");//입점업체별로 동일 아이디가 생성될 수 있으므로 company_id를 조건절에서 뺌(by 김수현대리) kbk 12/02/08

	if ($db->total)
	{
		echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$id] ' + language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 사용자 입니다.
		//echo("<script>history.back();</script>");
		exit;
	}

	$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id = '$id' ");

	if ($db->total)
	{
		echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$id] '+ language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 아이디 입니다.
		//echo("<script>history.back();</script>");
		exit;
	}

	$id    = trim($id);
	$pw  = trim($pw);
	$name  = trim($name);
	$nick_name  = trim($nick_name);
	//$mail  = trim($mail1."@".$mail2);
	$addr1 = trim($addr1);
	$addr2 = trim($addr2);
	$comp  = trim($comp);
	$class = trim($class);
	$birthday=$birthday1."-".$birthday2."-".$birthday3;
	$zip   = "$zipcode1-$zipcode2";
	$tel = trim($tel);
	$pcs = trim($pcs);
	//$tel   = "$tel1-$tel2-$tel3";
	//$pcs   = "$pcs1-$pcs2-$pcs3";
	$code  = md5(uniqid(rand()));



	if(!isset($department)){
		$department = "0";
	}

	if(!isset($position)){
		$position = "0";
	}

	$gp_ix = "1";
	if($db->dbms_type == "oracle"){
		$sql = "INSERT INTO ".TBL_COMMON_USER."
						(code, id, pw, mem_type, language, company_id, date_, visit, last, ip, auth,authorized)
						VALUES
						('$code','$id','".hash("sha256", $pw)."','S','".$language_type."','".$company_id."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."', '".$auth."','".$authorized."')";
	}else{
		$sql = "INSERT INTO ".TBL_COMMON_USER."
						(code, id, pw, mem_type, language, company_id, date, visit, last, ip, auth,authorized)
						VALUES
						('$code','$id','".hash("sha256", $pw)."','S','".$language_type."','".$company_id."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."', '".$auth."','".$authorized."')";
	}
	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code, name, mail, tel, pcs, date_, recom_id, gp_ix)
					VALUES
					('$code',AES_ENCRYPT('$name','".$db->ase_encrypt_key."'),AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'),AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'),NOW(),'".$admininfo[charger_id]."','$gp_ix')";
	}else{
		$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
						(code, name, mail, tel, pcs, date, recom_id, gp_ix)
						VALUES
						('$code',HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),NOW(),'".$admininfo[charger_id]."','$gp_ix')";
	}

	$db->query($sql);

	admin_log("C",$id,$company_id);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('셀러가 정상적으로 등록되었습니다.');parent.document.location.reload();</script>");

}

if($act == "user_update"){

	admin_log("U",$b_id,$company_id);


	$tel = trim($tel);
	$pcs = trim($pcs);

	if($change_pass){
		$update_pass_str = ", pw= '".hash("sha256", $pw)."'";
	}

	if(trim($charger_id) != trim($bcharger_id)){
		$db->query("select * from ".TBL_COMMON_USER."  where company_id='".trim($company_id)."' and id='".trim($id)."' ");

		if($db->total){
			//echo "<script language='javascript'>alert('$charger_id 아이디는 이미 사용중입니다.');</script>";
			echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$charger_id] ' + language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 사용자 입니다.
			exit;
		}

		$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id = '$id' ");

		if ($db->total)
		{
			//echo("<script>alert('[$id] 이미 등록된 아이디 입니다. ');</script>");
			echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$id] ' + language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 아이디 입니다.
			//echo("<script>history.back();</script>");
			exit;
		}
	}


	if(!isset($department)){
		$department = "0";
	}

	if(!isset($position)){
		$position = "0";
	}

	$sql = "UPDATE ".TBL_COMMON_USER." SET
			id='$id' , language = '$language_type',authorized = '$authorized', auth = '$auth' $update_pass_str
			WHERE code='$code'";

	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL." SET
				mail= AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'), tel=AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),pcs=AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'), name = AES_ENCRYPT('$name','".$db->ase_encrypt_key."') , department = '$department' , position = '$position'
				WHERE code='$code'";
	}else{
		$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL." SET
				mail= HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')), tel=HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),pcs=HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')), name = HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')) , department = '$department' , position = '$position'
				WHERE code='$code'";
	}

	//echo $sql;
	//exit	;
	$db->query($sql);

	//변경정보와 로그인 아이디가 같으면 랭귀지 변경정보를 세션에 반영한다.
	if($admininfo[charger_ix] == $code){
		$admininfo["language"] = $language_type;
	}


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('관리자가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}

if($act == "user_delete"){

	admin_log("D",$id,$company_id);

	$db->query("SELECT code, company_id FROM ".TBL_COMMON_USER." WHERE company_id = '$company_id' and code = '$code' ");
	$db->fetch();
	$code = $db->dt[code];

	$sql = "delete from ".TBL_COMMON_USER." where company_id ='$company_id' and code = '$code'";

	$db->query($sql);

	$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where  code = '$code'";

	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('입점업체 사용자 정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
	//echo("<script>location.href = 'company_list.php';</script>");
}

if($act == "admin_log")
{
	admin_log("R",$charger_id,$company_id);
}

function admin_log($crud_div,$id,$company_id)
{
	global $admininfo;

	$mdb = new Database;


	if($mdb->dbms_type == "oracle"){
		$sql = "select ccd.com_name, AES_DECRYPT(cmd.name,'".$mdb->ase_encrypt_key."') as name
			from common_user cu, common_member_detail cmd ,  common_company_detail ccd
			where cu.code = cmd.code and cu.company_id = ccd.company_id
			and cu.company_id = '$company_id'
			and cu.id = '$id'";
			//echo $sql;
	}else{
		$sql = "select ccd.com_name, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name
			from common_user cu, common_member_detail cmd ,  common_company_detail ccd
			where cu.code = cmd.code and cu.company_id = ccd.company_id
			and cu.company_id = '$company_id'
			and cu.id = '$id'";
	}

	$mdb->query($sql);
	$mdb->fetch();


	$sql = "insert into admin_log(log_ix,accept_com_name,accept_m_name,admin_id,admin_name,crud_div,ip,regdate) values('','".$mdb->dt[com_name]."','".$mdb->dt[name]."','".$admininfo['charger_id']."','".$admininfo['charger']."','$crud_div','".$_SERVER["REMOTE_ADDR"]."',NOW())";
	$mdb->sequences = "ADMIN_LOG_SEQ";
	$mdb->query($sql);

}


function checkMyService($service_type,$solution_type, $service_use_value = ""){
	$service_mall_type = array("H","F","R","B");
	if(!in_array($_SESSION["admininfo"]["mall_type"],$service_mall_type)){
		//return true;
	}
	include_once($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
	$shmop = new Shared("myservice_info");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$myservice_info = $shmop->getObjectForKey("myservice_info");
	$myservice_info = unserialize(urldecode($myservice_info));
	//echo $solution_type;
	//$__service_info = eval("\$myservice_info->".$service_type);
	$_service_info = (array)$myservice_info[$service_type];
	$myservice_info = (array)$_service_info[$solution_type];
	//print_r($myservice_info);
	//return $myservice_info->$service_type->$solution_type->si_status;
	//echo $myservice_info[si_status];
	//echo $myservice_info[service_unit_value];
	if($myservice_info[si_status] == "SI" && $myservice_info[sm_edate] >= date("Y-m-d")){
		if($service_use_value != ""){
			//echo $myservice_info[service_unit_value];
			if($myservice_info[service_unit_value] > $service_use_value){
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
	}else{
		return false;
	}
	//return $myservice_info;
}

?>
