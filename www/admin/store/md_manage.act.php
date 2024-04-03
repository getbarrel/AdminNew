<?
include("../../class/database.class");

session_start();


//print_r($_POST);
//exit;
$db = new Database;

if ($act == "idcheck")
{

	$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id='$id'");

	if ($db->total)
	{
		echo("<script>alert('이미 등록된 [아이디]입니다.');</script>");
		//echo("<script>top.form.id_flag.value = '0';</script>");
		echo("<script>top.edit_form.admin_id.dup_check = false;</script>");
	}
	else
	{
		echo("<script>alert('사용 가능한 [아이디]입니다.');</script>");
		//echo("<script>top.form.id_flag.value = '1';</script>");
		echo("<script>top.edit_form.admin_id.dup_check = true;</script>");
	}
}



if($act == "user_insert"){

	$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE company_id = '".$admininfo[company_id]."' and id = '$id' ");

	if ($db->total)
	{
		echo("<script>alert('[$id] 는 이미 등록된 사용자 입니다.');</script>");
		//echo("<script>history.back();</script>");
		exit;
	}

	$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id = '$id' ");

	if ($db->total)
	{
		echo("<script>alert('[$id] 이미 등록된 아이디 입니다. ');</script>");
		//echo("<script>history.back();</script>");
		exit;
	}

	$id    = trim($id);
	$pw  = trim($pw);
	$name  = trim($md_name);
	$nick_name  = trim($nick_name);
	//$mail  = trim($mail1."@".$mail2);
	$addr1 = trim($addr1);
	$addr2 = trim($addr2);
	$comp  = trim($comp);
	$class = trim($class);
	$birthday=$birthday1."-".$birthday2."-".$birthday3;
	$zip   = "$zipcode1-$zipcode2";
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
					(code, id, pw, mem_type, language, company_id, date_, visit, last, ip, auth)
					VALUES
					('$code','$id','".hash("sha256", $pw)."','MD','".$language_type."','".$admininfo[company_id]."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."','".$auth."')";
	}else{
		$sql = "INSERT INTO ".TBL_COMMON_USER."
					(code, id, pw, mem_type, language, company_id, date, visit, last, ip, auth)
					VALUES
					('$code','$id','".hash("sha256", $pw)."','MD','".$language_type."','".$admininfo[company_id]."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."','".$auth."')";
	}

	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code, name, mail, tel, pcs, date_, recom_id, branch, team, mem_level, gp_ix)
					VALUES
					('$code',AES_ENCRYPT('$name','".$db->ase_encrypt_key."'),AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'),AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'),NOW(),'".$admininfo[charger_id]."','".$branch."','".$team."','".$mem_level."','$gp_ix')";
	}else{
		$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code, name, mail, tel, pcs, date, recom_id, branch, team, mem_level, gp_ix)
					VALUES
					('$code',HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),NOW(),'".$admininfo[charger_id]."','".$branch."','".$team."','".$mem_level."','$gp_ix')";
	}

	$db->query($sql);

	admin_log("C",$id,$company_id);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('MD 정보 가 정상적으로 등록되었습니다.');parent.document.location.reload();</script>");

}


if($act == "user_update"){

	admin_log("U",$id,$company_id);


	//$tel = $tel1."-".$tel2."-".$tel3;
	//$pcs = $pcs1."-".$pcs2."-".$pcs3;

	if($change_pass){
		$update_pass_str = ", pw = '".hash("sha256", $pw)."'";
	}

	if(trim($id) != trim($b_id)){
		$db->query("select * from ".TBL_COMMON_USER."  where company_id='".trim($admininfo[company_id])."' and id='".trim($id)."' ");

		if($db->total){
			echo "<script language='javascript'>alert('$charger_id 아이디는 이미 사용중입니다.');</script>";
			exit;
		}

		$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id = '$id' ");

		if ($db->total)
		{
			echo("<script>alert('[$id] 이미 등록된 아이디 입니다. ');</script>");
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
			id='$id' , language = '".$language_type."',authorized = '$authorized', auth = '$auth' $update_pass_str
			WHERE code='$code'"; // 이름에 대한 수정을 없앰 kbk

	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL." SET
			mail=AES_ENCRYPT('$mail','".$db->ase_encrypt_key."'), tel=AES_ENCRYPT('$tel','".$db->ase_encrypt_key."'),pcs=AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."'), name = AES_ENCRYPT('$md_name','".$db->ase_encrypt_key."'), branch = '$branch', team = '$team', mem_level = '$mem_level'
			WHERE code='$code'"; // 이름에 대한 수정을 없앰 kbk , shs 2011.07.17 , department = '$department' , position = '$position'
	}else{
		$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL." SET
			mail=HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')), tel=HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),pcs=HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')), name = HEX(AES_ENCRYPT('$md_name','".$db->ase_encrypt_key."')), branch = '$branch', team = '$team', mem_level = '$mem_level'
			WHERE code='$code'"; // 이름에 대한 수정을 없앰 kbk , shs 2011.07.17 , department = '$department' , position = '$position'
	}

	//echo $sql;
	//exit	;
	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('MD 정보 가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}

if($act == "user_delete"){

	admin_log("D",$id,$admininfo[company_id]);

	$db->query("SELECT code, company_id FROM ".TBL_COMMON_USER." WHERE company_id = '".$admininfo[company_id]."' and code = '$code' ");
	$db->fetch();
	$code = $db->dt[code];

	$sql = "delete from ".TBL_COMMON_USER." where company_id ='".$admininfo[company_id]."' and id = '$code'";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('MD 정보 사용자 정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
	//echo("<script>location.href = 'company_list.php';</script>");
}

if($act == "admin_log")
{
	admin_log("R",$charger_id,$admininfo[company_id]);
}

function admin_log($crud_div,$id,$company_id)
{
	global $admininfo;

	$mdb = new Database;

	if($mdb->dbms_type == "oracle"){
		$mdb->query("select ccd.com_name, AES_DECRYPT(cmd.name,'".$mdb->ase_encrypt_key."') as name from common_user cu, common_member_detail cmd ,  common_company_detail ccd  where cu.code = cmd.code and cu.company_id = ccd.company_id and cu.company_id = '$company_id' and cu.id = '$id'");
	}else{
		$mdb->query("select ccd.com_name, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name from common_user cu, common_member_detail cmd ,  common_company_detail ccd  where cu.code = cmd.code and cu.company_id = ccd.company_id and cu.company_id = '$company_id' and cu.id = '$id'");
	}
	$mdb->fetch();


	$sql = "insert into admin_log(log_ix,accept_com_name,accept_m_name,admin_id,admin_name,crud_div,ip,regdate) values('','".$mdb->dt[com_name]."','".$mdb->dt[name]."','".$admininfo['charger_id']."','".$admininfo['charger']."','$crud_div','".$_SERVER["REMOTE_ADDR"]."',NOW())";
	$mdb->sequences = "ADMIN_LOG_SEQ";
	$mdb->query($sql);


}


function rmdirr($target,$verbose=false)
// removes a directory and everything within it
{
$exceptions=array('.','..');
if (!$sourcedir=@opendir($target))
   {
   if ($verbose)
       echo '<strong>Couldn&#146;t open '.$target."</strong><br />\n";
   return false;
   }
while(false!==($sibling=readdir($sourcedir)))
   {
   if(!in_array($sibling,$exceptions))
       {
       $object=str_replace('//','/',$target.'/'.$sibling);
       if($verbose)
           echo 'Processing: <strong>'.$object."</strong><br />\n";
       if(is_dir($object))
           rmdirr($object);
       if(is_file($object))
           {
           $result=@unlink($object);
           if ($verbose&&$result)
               echo "File has been removed<br />\n";
           if ($verbose&&(!$result))
               echo "<strong>Couldn&#146;t remove file</strong>";
           }
       }
   }
closedir($sourcedir);
if($result=@rmdir($target))
   {
   if ($verbose)
       echo "Target directory has been removed<br />\n";
   return true;
   }
if ($verbose)
   echo "<strong>Couldn&#146;t remove target directory</strong>";
return false;
}
?>
