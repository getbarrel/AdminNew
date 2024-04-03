<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

//print_r($_POST[]);
//exit;
if ($act == "updates"){
	
	for($i=0;$i < count($_POST[menu_info]);$i++){
		if($_POST[menu_info][$i][menu_code]){

			if($_POST[menu_info][$i][use_home] == ""){
				$use_home = "N";
			}else{
				$use_home = "Y";
			}

			if($_POST[menu_info][$i][use_soho] == ""){
				$use_soho = "N";
			}else{
				$use_soho = "Y";
			}

			if($_POST[menu_info][$i][use_business] == ""){
				$use_business = "N";
			}else{
				$use_business = "Y";
			}

			if($_POST[menu_info][$i][use_wholesale] == ""){
				$use_wholesale = "N";
			}else{
				$use_wholesale = "Y";
			}

			if($_POST[menu_info][$i][use_openmarket] == ""){
				$use_openmarket = "N";
			}else{
				$use_openmarket = "Y";
			}

			if($_POST[menu_info][$i][use_enterprise] == ""){
				$use_enterprise = "N";
			}else{
				$use_enterprise = "Y";
			}


			if($_POST[menu_info][$i][disp_auth] == ""){
				$disp_auth = "N";
			}else{
				$disp_auth = "Y";
			}

			$sql = "select * from admin_menus where menu_code = '".$_POST[menu_info][$i][menu_code]."' and view_order = '".$_POST[menu_info][$i][b_view_order]."' ";
			$db->query($sql);
			if($db->total){
				$sql = "update admin_menus set 
						menu_name = '".$_POST[menu_info][$i][menu_name]."', menu_div = '".$_POST[menu_info][$i][menu_div]."', use_home = '".$use_home."', use_soho = '".$use_soho."', use_business = '".$use_business."', use_wholesale = '".$use_wholesale."', use_openmarket = '".$use_openmarket."', use_enterprise = '".$use_enterprise."',
						view_order = '".$_POST[menu_info][$i][view_order]."',menu_path = '".$_POST[menu_info][$i][menu_path]."',menu_link = '".$_POST[menu_info][$i][menu_link]."',
						menu_param = '".$_POST[menu_info][$i][menu_param]."',disp_auth = '".$disp_auth."' 
						where menu_code = '".$_POST[menu_info][$i][menu_code]."' and view_order = '".$_POST[menu_info][$i][b_view_order]."' "; //and view_order = '".$_POST[menu_info][$i][view_order]."'
				//echo nl2br($sql);

				$db->query($sql);
			}
			//echo "menu_copy :".$_POST[menu_info][$i][menu_copy]."<br><br>";
			if($_POST[menu_info][$i][menu_copy] == "Y"){
				$sql = "insert into admin_menus(menu_code,menu_div, use_home, use_soho, use_business, use_wholesale, use_openmarket, use_enterprise, disp_auth, menu_name,menu_path,menu_link,menu_param, view_order, regdate) 
						values
						('".$_POST[menu_info][$i][menu_code]."','".$_POST[menu_info][$i][menu_div]."','".$use_home."','".$use_soho."','".$use_business."','".$use_wholesale."','".$use_openmarket."', '".$use_enterprise."', '".$disp_auth."','".$_POST[menu_info][$i][menu_name]."','".$_POST[menu_info][$i][menu_path]."','".$_POST[menu_info][$i][menu_link]."','".$_POST[menu_info][$i][menu_param]."','".($_POST[menu_info][$i][view_order]+1)."',NOW())";
				//echo nl2br($sql);

				$db->query($sql);
			}
		}
	}
	
	
	echo "<Script Language='JavaScript'>parent.document.location.reload();</Script>";	
	//Header("Location: referer.php");
	
}

if($act == "delete"){
	$sql = "delete from admin_menus where menu_code = '$menu_code'";
	$db->query($sql);

	$sql = "delete from admin_auth_templet_detail where menu_code = '$menu_code'";
	$db->query($sql);

	echo "<script language='javascript' src='../js/message.js.php'></script><Script Language='JavaScript'>show_alert('정상적으로 삭제되었습니다.');parent.document.location.reload();</Script>";	
}
//echo "<br>maxlevel:".getMaxlevel($cid,$sub_depth);
?>