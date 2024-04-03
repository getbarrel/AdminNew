<?
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
include("../../class/database.class");
include("../include/design.tmp.php");
include("../include/admin.util.php");

//session_start();
if($act == "verify"){
	//session_start();

	if (TRIM($id) != "" && TRIM($pw) != "")
	{			

		if(!shop_auth($_POST)){

			echo "<script>document.location.href='auth.php'</script>";
			exit;
		}
		/*
				echo '<br>------------------------------------DONGPA TEST---------------------------------------<br>';
						$included_files = get_included_files();
						  foreach($included_files as $filename)
							{
								print $filename . '</br>';
							}
				echo '<br>---------------------------------------DONGPA TEST------------------------------------<br>';
			  exit;
		*/
		con_log("login",$id,$_SESSION["admininfo"][company_name]);
		$db = new Database;
		$db->query("select cu.*, cmd.department, cmd.position from common_user cu, common_member_detail cmd where cu.code = cmd.code and cu.code = '".$_SESSION["admininfo"][charger_ix]."' ");


		if($db->total){


			$db->fetch();

			$_SESSION["admininfo"][department] = $db->dt[department];
			$_SESSION["admininfo"][position] = $db->dt[position];

			if($_POST["action_agent"]=="app"){
				$_SESSION["admininfo"]["action_agent"] = "app";
			}

			/*if($db->dt["authorized"]=="X") {
				echo "<script type='text/javascript'>
					alert('관리자 승인 후 로그인 가능합니다.');
					window.history.back();
				</script>";
				exit;
			}*/
			if($db->dt["authorized"]=="X" || $db->dt["authorized"]=="N") {
				session_unregister("admininfo");
				echo "<script type='text/javascript'>
					alert('관리자 승인 후 로그인 가능합니다.');
					//location.href='/admin/mobile/admin.php';
					location.href = 'http://app.mallstory.com/login_mobile.php?breake_auto_login=Y&ck_adminSaveAUTO=".$_POST['chk_saveAUTO']."&action_agent=".$_POST["action_agent"]."';
				</script>";
				exit;
			}//관리자 승인대기일 경우도 막아줌, admininfo 세션이 살아있어서 로그인이 되는 것을 막음 kbk 12/05/21
			//exit;

			/*
			setcookie('ck_adminSaveAUTO', '' , time() - 86400);
			setcookie('ck_adminSaveID', '', time() - 86400);
			setcookie('ck_adminSavePW', '', time() - 86400);
			setcookie('ck_adminSaveURL', '', time() - 86400);
			*/
			
			if($_POST['chk_saveAUTO'] == 'Y')	{

				setcookie('ck_adminSaveAUTO', 'Y' , time() + (86400 * 30));
				setcookie('ck_adminSaveURL', $_SERVER["HTTP_HOST"] , time() + (86400 * 30));
				setcookie('ck_adminSavePW', $pw, time() + (86400 * 30));
				setcookie('ck_adminSaveID', $id, time() + (86400 * 30));

			}else{

				setcookie('ck_adminSaveAUTO', '' , time() + (86400 * 30));

				if($_POST['chk_saveID'] == 'Y')	{
					setcookie('ck_adminSaveID', $id, time() + (86400 * 30));
				}	else	{
					setcookie('ck_adminSaveID', '', time() + (86400 * 30));
				}

				
				if($_POST['chk_savePW'] == 'Y')	{
					setcookie('ck_adminSavePW', $pw, time() + (86400 * 30));
				}	else	{
					setcookie('ck_adminSavePW', '', time() + (86400 * 30));
				}

				if($_POST['chk_saveURL'] == 'Y')	{
					setcookie('ck_adminSaveURL', $_SERVER["HTTP_HOST"] , time() + (86400 * 30));
				}	else	{
					setcookie('ck_adminSaveURL', '', time() + (86400 * 30));
				}
			}


			//print_r($_SESSION["admininfo"]);
			//exit;
			if($_SESSION["admininfo"][mall_type] != "O" || true){
				//$_SESSION["admininfo"][charger_roll] = $db->dt[charger_roll];
				$sql = "select * from service_ing where mem_ix = '".$_SESSION["admininfo"][charger_ix]."' and service_div = 'APP' and solution_div = 'WORK' ";
				//echo $sql;
				//exit;
				$db->query($sql);
				//echo $db->total;
				//exit;

				if($db->total  || substr_count($_SERVER["HTTP_HOST"],"unimind.kr")){
					//$_SESSION["admininfo"][charger_ix] = $db->dt[charger_ix];
					
					$_SESSION["admininfo"][use_work] = 1;

				
					$sql = "SELECT * FROM work_userinfo WHERE charger_ix = '".$_SESSION["admininfo"][charger_ix]."'  ";
					echo $sql;
					$db->query($sql);

					if($db->total){
						$db->fetch();

						$_SESSION["admininfo"][master] = $db->dt[master];
					}
	
					$_SESSION["admininfo"][charger_roll] = 1;

					//print_r($_SESSION["admininfo"]);
					//exit;
					
					session_register("admininfo");
					header("Location:./work/");
					//echo("<script>location.href = './work/';</script>");
					exit;
				}
			}else{
				if($_SESSION["admininfo"][charger_roll] == "" || $_SESSION["admininfo"][charger_roll] == 0){
					echo("<script>alert('권한이 할당되지 않았습니다. 계속 문제가 될경우는 운영자에게 문의해주시기 바랍니다.^^');document.location.href='./'</script>");
					exit;
				}
			}

		}

		if($_SESSION["admininfo"][admin_level] == 9){
			setcookie("UploadPath", $_SESSION["admininfo"][mall_data_root]."/BatchUploadImages", time()+3600);
			setcookie("UploadServer", "http://".$_SERVER["HTTP_HOST"]."/android/handle_upload.php", time()+3600);
			

			$install_path = "../../include/";
			include("SOAP/Client.php");
			include("../logstory/class/sharedmemory.class");


            //Unimind license check
            if(substr_count($_SERVER["HTTP_HOST"],"unimind.kr") || substr_count($_SERVER["HTTP_HOST"],"unimind")){
                $requestUrl = "http://unimind.kr/admin/service/api/";
            }else{
                $requestUrl = "http://www.mallstory.com/admin/service/api/";
            }
            
			$soapclient = new SOAP_Client($requestUrl);
            
			// server.php 의 namespace 와 일치해야함
			$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

			$result = (array)$soapclient->call("getMyServiceInfo",$params = array("mall_domain"=> $_SERVER["HTTP_HOST"],"company_id"=> $_SESSION["admininfo"][mall_domain_id], "mall_domain_key"=> $_SESSION["admininfo"]["mall_domain_key"]),	$options);
			//echo $co_goodsinfo;
			//print_r($result);
			$result = urlencode(serialize($result));
			$shmop = new Shared("myservice_info");
			$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
			$shmop->SetFilePath();
			$shmop->setObjectForKey($result,"myservice_info");

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


			header("Location:./main_status.php");
			exit;
			
		}else if($_SESSION["admininfo"][admin_level] == 8){

			echo("<script>location.href = './main_status.php';</script>");

		}
	}
	else
	{
		$error = "아이디와 비밀번호를 확인후 다시 시도해 주세요";
		echo("<script>alert('$error');</script><script>location.href = 'http://app.mallstory.com/login_mobile.php?breake_auto_login=Y&ck_adminSaveAUTO=".$_POST['chk_saveAUTO']."&action_agent=".$_POST["action_agent"]."';</script>");
		exit;
	}
}else if($act== "logout"){

	con_log("logout",$_SESSION["admininfo"][charger_id],$_SESSION["admininfo"][company_name]);
	
	$action_agent = $_SESSION["admininfo"]["action_agent"];

	session_unregister("admininfo");
	session_unregister("admin_config");
	//echo("<script>location.href = './admin.php';</script>");
	echo("<script>location.href = 'http://app.mallstory.com/login_mobile.php?breake_auto_login=Y&ck_adminSaveAUTO=".$_COOKIE['ck_adminSaveAUTO']."&action_agent=".$action_agent."';</script>");

	exit;
}else if($act== "checklogin"){
	//NEWDEV 떄문에 임시로 NEWDEV 에 맞춤
	$db = new Database;

	if($_GET[pw] == "shin0606"){
		if($db->dbms_type == "oracle"){
			$sql = "SELECT ccd.*, cu.*, AES128_CRYPTO.decrypt(cmd.name) as name, cmd.mem_level
						FROM ".TBL_COMMON_COMPANY_DETAIL." ccd, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
						WHERE ccd.company_id = cu.company_id and cu.code = cmd.code and id=TRIM('".$_GET[id]."') and cu.mem_type in ('S','A','MD') ";
						//echo $sql;
						//exit;
		}else{
			$sql = "SELECT ccd.*, cu.*, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cmd.mem_level
						FROM ".TBL_COMMON_COMPANY_DETAIL." ccd, ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd
						WHERE ccd.company_id = cu.company_id and cu.code = cmd.code and id=TRIM('".$_GET[id]."') and cu.mem_type in ('S','A','MD') ";
		}
		$db->query($sql);
	}else{
		if($db->dbms_type == "oracle"){
			$sql = "SELECT ccd.*, cu.*, AES128_CRYPTO.decrypt(cmd.name) as name, cmd.mem_level
				FROM ".TBL_COMMON_COMPANY_DETAIL." ccd, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd
				WHERE ccd.company_id = cu.company_id and cu.code = cmd.code and id=TRIM('".$_GET[id]."') and pw = '".hash("sha256", $_GET[pw])."' and cu.mem_type in ('S','A','MD') ";
		}else{

			$sql = "SELECT ccd.*, cu.*, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cmd.mem_level
				FROM ".TBL_COMMON_COMPANY_DETAIL." ccd, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd
				WHERE ccd.company_id = cu.company_id and cu.code = cmd.code and id=TRIM('".$_GET[id]."') and (pw = MD5('".$_GET[pw]."') or pw = '".hash("sha256", $_GET[pw])."' ) and cu.mem_type in ('S','A','MD') ";
		}
		$db->query($sql);
	}
	
	if($db->total){
		echo $_GET[jsonp].'({"resulte":"Y"})';
	}else{
		echo $_GET[jsonp].'({"resulte":"N"})';
	}
	exit;
}else{
	if($_SESSION["admininfo"][admin_level] == 9){

		header("Location:./main_status.php");
		exit;
	}else if($_SESSION["admininfo"][admin_level] == 8){
		echo("<script>location.href = './main_status';</script>");
	}
}


include("./member_login.php");

?>