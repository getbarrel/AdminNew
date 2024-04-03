<?
include("../../class/database.class");

session_start();

$db = new Database;

//print_r($_POST);

if($act == "user_insert"){
	
	$db->debug = true;
	$db->query("select * from ".TBL_COMMON_USER."  where company_id='".trim($company_id)."' and id='".trim($id)."' ");
	
	if($db->total){
		echo "<script language='javascript'>alert('$charger_id 아이디는 이미 사용중입니다.');</script>";
		exit;
	}
	
	$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id = '$id' ");

	if ($db->total)
	{
		echo("<script>alert('[$id] 이미 등록된 아이디 입니다. ');</script>");
		//echo("<script>history.back();</script>");
		exit;
	}
	
	
	$tel = $tel1."-".$tel2."-".$tel3;
	$pcs = $pcs1."-".$pcs2."-".$pcs3;

	if(!isset($use_work)){
		$use_work = "1";
	}

	if(!isset($department)){
		$department = "0";
	}

	if(!isset($position)){
		$position = "0";
	}
	
	//$company_id  = md5(uniqid(rand()));
	$code  = md5(uniqid(rand()));


	$sql = "INSERT INTO ".TBL_COMMON_USER."
						(code, id, pw, mem_type, date, visit, last, ip, company_id, auth)
						VALUES
						('$code','$id','".hash("sha256", $pw)."','A',NOW(),'0',NOW(),'$REMOTE_ADDR','".$admininfo[company_id]."',1)";

	$db->query($sql);


	$sql = "insert into ".TBL_COMMON_MEMBER_DETAIL." SET					
			code = '".$code."' ,
			name = HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')) ,
			mail=HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')), 
			tel=HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),
			pcs=HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),  
			department = '$department' , position = '$position' 
			"; 

	
	//echo $sql;
	//exit	;
	$db->query($sql);

	//$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE code=LAST_INSERT_ID()");
	//$db->fetch();
	//$code = $db->dt[code];

	$sql = "insert into service_ing(si_ix,service_div,solution_div,mem_ix,sp_name,sm_sdate,sm_edate,si_status,set_status,regdate)		
				values
				('','APP','WORK','".$code."','업무관리','".date("Y-m-d")."',NULL,'SI','SC',NOW())";

	$db->query($sql);


	admin_log("C",$charger_id,$company_id);

	if($google_mail != ""){
		$sql = "insert into work_userinfo(charger_ix,google_mail,google_pass,regdate) values('$charger_ix','$google_mail','$google_pass',NOW())";
		$db->query($sql);
	}

	if ($_FILES["profile_img"][size] > 0){
		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/";
		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}

		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/profile/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}



		$path .= $wr_ix."/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}

		if ($_FILES["profile_img"][size] > 0){
			move_uploaded_file($_FILES[report_file][tmp_name], $path."/".iconv('UTF-8','EUC-KR',"profile_".$code.".jpg")); //$_FILES[profile_img][name]
			chmod($path."/".iconv('UTF-8','EUC-KR',$_FILES[profile_img][name]),0777);
		}
	}
	
	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('사용자가 정상적으로 등록되었습니다.');parent.document.location.reload();</script>");
	exit;
	/*
	if($charger_level == 9){
		echo("<script>location.href = 'admin_manage.php';</script>");
	}else{
		echo("<script>location.href = 'company_user.php?company_id=$company_id';</script>");
	}
	*/
}


if($act == "user_update"){
	
	
	$tel = $tel1."-".$tel2."-".$tel3;
	$pcs = $pcs1."-".$pcs2."-".$pcs3;
	
	
	if($change_pass){
		$pass_str = ", pw='".hash("sha256", $pw)."'";	
	}
		
	if(trim($id) != trim($b_id)){
		$db->query("select * from ".TBL_COMMON_USER."  where company_id='".trim($company_id)."' and id='".trim($id)."' ");
		
		if($db->total){
			echo "<script language='javascript'>alert('$charger_id 아이디는 이미 사용중입니다.');</script>";
			exit;
		}
		
		$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id = '$id' ");

		if ($db->total)
		{
			echo("<script>alert('[$id] 이미 등록된 아이디 입니다. ');</script>");
			//echo("<script>history.back();</script>");
			exit;
		}
	}
		
	if(!isset($use_work)){
		$use_work = "0";
	}

	if(!isset($department)){
		$department = "0";
	}

	if(!isset($position)){
		$position = "0";
	}

	if($auth != ""){
		$auth_str = " , auth = '$auth'  ";
	}

	$sql = "UPDATE ".TBL_COMMON_USER." SET					
			id='$id' , authorized = '$authorized' $auth_str  $pass_str
			WHERE code='$charger_ix'"; // 이름에 대한 수정을 없앰 kbk
	//echo $sql;
	//exit;
	$db->query($sql);


	$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL." SET					
			name = HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')) ,
			mail=HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')), 
			tel=HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),
			pcs=HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),  
			department = '$department' , position = '$position' , sex_div = '$sex_div' 
			WHERE code='$charger_ix'"; // 이름에 대한 수정을 없앰 kbk

			
	//echo $sql;
	//exit	;
	$db->query($sql);
	//echo $sql;
	//exit	;
	$db->query($sql);
	
	if($google_mail != "" || $master != ""){

		$db->query("SELECT * FROM work_userinfo WHERE charger_ix='$charger_ix'");
		$db->fetch();

		if($db->total){
			$db->query("update work_userinfo set master='$master',google_mail='$google_mail',google_pass='$google_pass',google_sync_yn='$google_sync_yn', google_sync_time = '$google_sync_time' where charger_ix='$charger_ix' ");
			$db->fetch();
		}else{
			
			$sql = "insert into work_userinfo(charger_ix,master,google_mail,google_pass,google_sync_yn,google_sync_time,regdate) values('$charger_ix','$master','$google_mail','$google_pass','$google_sync_yn','$google_sync_time',NOW())";
			$db->query($sql);
		
		}
	}
	
	
	if($charger_level == 9){
		if($admininfo[admin_id] == $charger_id){
			//$admininfo[permit]  = $tmp_permit;
			//session_register("admininfo");
		}
		//echo("<script>location.href = 'admin_manage.php';</script>");
	//}else{
		//echo("<script>location.href = 'company_user.php?company_id=$company_id&charger_id=$charger_id';</script>");
	}

	if ($_FILES["profile_img"][size] > 0){
		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/";
		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}

		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/profile/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}



		$path .= $wr_ix."/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}

		if ($_FILES["profile_img"][size] > 0){
			move_uploaded_file($_FILES[profile_img][tmp_name], $path."/".iconv('UTF-8','EUC-KR',"profile_".$charger_ix.".jpg")); //$_FILES[profile_img][name]
			chmod($path."/".iconv('UTF-8','EUC-KR',$_FILES[profile_img][name]),0777);
		}
	}
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('사용자가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}

if($act == "user_delete"){
	
	$sql = "delete from work_userinfo where charger_ix = '$charger_ix' ";
	//echo $sql."<br>";
	$db->query($sql);

	
	$sql = "delete from ".TBL_COMMON_USER." where company_id ='$company_id' and code = '$charger_ix'";
	//echo $sql."<br>";
	$db->query($sql);

	$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where  code = '$charger_ix'";
	//echo $sql."<br>";
	$db->query($sql);
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('사용자 정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
	//echo("<script>location.href = 'company_list.php';</script>");
}


function admin_log($crud_div,$id,$company_id)
{
	global $admininfo;

	$mdb = new Database;

	$sql = "select ccd.com_name, AES_DECRYPT(UNHEX(cmd.name),'".$mdb->ase_encrypt_key."') as name  
			from common_user cu, common_member_detail cmd ,  common_company_detail ccd  
			where cu.code = cmd.code and cu.company_id = ccd.company_id 
			and cu.company_id = '$company_id' 
			and cu.id = '$id'";

	$mdb->query($sql);

	$mdb->fetch();


	$sql = "insert into admin_log(accept_com_name,accept_m_name,admin_id,admin_name,crud_div,ip,regdate) values('".$mdb->dt[com_name]."','".$mdb->dt[name]."','".$admininfo['charger_id']."','".$admininfo['charger']."','$crud_div','".$_SERVER["REMOTE_ADDR"]."',NOW())";

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
