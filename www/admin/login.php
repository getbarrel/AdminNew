<?
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
include("../class/database.class");
include("./include/design.tmp.php");
include("./include/admin.util.php");


//session_start();
if($act == "verify"){
	//session_start();

	if (TRIM($id) != "" && TRIM($pw) != "")
	{

		if(!shop_auth($_POST)){
			echo "<script>document.location.href='auth.php'</script>";
			exit;
		}

		con_log("login",$id,$admininfo[company_name]);
		$db = new Database;
		$db->query("select cu.*, cmd.department, cmd.position from common_user cu, common_member_detail cmd where cu.code = cmd.code and cu.code = '".$admininfo[charger_ix]."' ");
		if($db->total){
			$db->fetch();
			$admininfo[department] = $db->dt[department];
			$admininfo[position] = $db->dt[position];

			if($db->dt["authorized"]=="X") {
				echo "<script type='text/javascript'>
					alert('관리자 승인 후 로그인 가능합니다.');
					window.history.back();
				</script>";
				exit;
			}
			//exit;
			if($_POST['chk_saveID'] == 'Y')	{					
				setcookie('ck_adminSaveID', $id, time() + (86400 * 30), '/admin/');
			}	else	{
				setcookie('ck_adminSaveID', '', time() - 86400);
			}

			//print_r($admininfo);
			//exit;
			
			//$admininfo[charger_roll] = $db->dt[charger_roll];
			
			$sql = "select * from service_ing where mem_ix = '".$admininfo[charger_ix]."' and service_div = 'APP' and solution_div = 'WORK' ";
			//echo $sql;
			//exit;
			$db->query($sql);
			//echo $db->total;
			//exit;
			if($db->total && substr_count($_SERVER["HTTP_HOST"],"unimind.kr") > 0){
				//$admininfo[charger_ix] = $db->dt[charger_ix];
				
				$admininfo[use_work] = 1;

			
				$sql = "SELECT * FROM work_userinfo WHERE charger_ix = '".$admininfo[charger_ix]."'  ";
				//echo $sql;
				$db->query($sql);

				if($db->total){
					$db->fetch();

					$admininfo[master] = $db->dt[master];
				}
				//print_r($admininfo);
				//exit;

				session_register("admininfo");
				header("Location:./work/");
				//echo("<script>location.href = './work/';</script>");
				exit;
			}else{
				if($admininfo[charger_roll] == "" || $admininfo[charger_roll] == 0){
					echo("<script>alert('권한이 할당되지 않았습니다. 계속 문제가 될경우는 운영자에게 문의해주시기 바랍니다.^^');document.location.href='./'</script>");
					exit;
				}
			}

		}
		if($admininfo[admin_level] == 9){
			if($admininfo[mem_type] == "A"){
				//echo("<script>location.href = './store/main.php';</script>");
				header("Location:./store/main.php");
			}else if($admininfo[mem_type] == "MD"){
				echo("<script>location.href = './seller/';</script>");
			}else if($admininfo[mem_type] == "CS"){
				echo("<script>location.href = './chainstore/';</script>");
			}
			exit;
			
		}else if($admininfo[admin_level] == 8){
			if($admininfo[mem_type] == "S"){
				echo("<script>location.href = './seller/';</script>");
			}else if($admininfo[mem_type] == "MD"){
				echo("<script>location.href = './seller/';</script>");
			}else if($admininfo[mem_type] == "CS"){
				echo("<script>location.href = './chainstore/';</script>");
			}
		}

	}
	else
	{
		$error = "아이디와 비밀번호를 확인후 다시 시도해 주세요";
		echo("<script>alert('$error');</script>");
	}
}else if($act== "logout"){

	con_log("logout",$admininfo[charger_id],$admininfo[company_name]);

	session_unregister("admininfo");
	session_unregister("admin_config");
	echo("<script>location.href = 'admin.php';</script>");	
	exit;
}else{
	if($admininfo[admin_level] == 9){
		if($admininfo[mem_type] == "A"){
			//echo("<script>location.href = './store/main.php';</script>");
			header("Location:./store/main.php");
		}else if($admininfo[mem_type] == "MD"){
			echo("<script>location.href = './seller/';</script>");
		}else if($admininfo[mem_type] == "CS"){
			echo("<script>location.href = './chainstore/';</script>");
		}
		exit;
		
	}else if($admininfo[admin_level] == 8){
		if($admininfo[mem_type] == "S"){
			echo("<script>location.href = './seller/';</script>");
		}else if($admininfo[mem_type] == "MD"){
			echo("<script>location.href = './seller/';</script>");
		}else if($admininfo[mem_type] == "CS"){
			echo("<script>location.href = './chainstore/';</script>");
		}
	}
}


if(substr_count($_SERVER["HTTP_HOST"],"unimind.kr")){
	include("admin_unimind.php");
}else if(substr($_SERVER["HTTP_HOST"], 0, 2) == "m." || $type == "mobile" || substr_count($_SERVER["HTTP_USER_AGENT"],"Mobile") ){
	include("admin_mobile.php");
}else if(substr_count($_SERVER["PHP_SELF"],"admin")){
	include("admin_v3.php");

}else{
	include("admin_v2.php");
}


?>