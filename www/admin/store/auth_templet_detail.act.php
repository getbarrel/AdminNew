<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;
//echo $_POST["all_menu"]."<br>" ;
//echo $add_auth_templet_ix."<br>";
//print_r($_POST[set_menu_info]);
//echo count($_POST[set_menu_info]);
//exit;
//print_r($_POST);
if ($_POST["act"] == "updates"){

	//$db->debug = true;
	//for($i=0,$i=0;$i < count($_POST[set_menu_info]);$i++){
	for($i=0,$x=0;$x < count($_POST[set_menu_info]);$i++){
		//echo $i."::".$x."::".$_POST[set_menu_info][$i][menu_code];
		
		if($_POST[set_menu_info][$i][menu_code]){
	//echo "aaa<br>";		
			if($_POST[set_menu_info][$i][auth_read] == ""){
				$auth_read = "N";
			}else{
				$auth_read = "Y";
			}

			if($_POST[set_menu_info][$i][auth_write_update] == ""){
				$auth_write_update = "N";
			}else{
				$auth_write_update = "Y";
			}
			if($_POST[set_menu_info][$i][auth_delete] == ""){
				$auth_delete = "N";
			}else{
				$auth_delete = "Y";
			}

			if($_POST[set_menu_info][$i][auth_excel] == ""){
				$auth_excel = "N";
			}else{
				$auth_excel = "Y";
			}
			if($_POST["all_menu"] && $add_auth_templet_ix == ""){
				$sql = "select * from admin_auth_templet_detail where menu_code = '".$_POST[set_menu_info][$i][menu_code]."'  ";
			}else{
				$sql = "select * from admin_auth_templet_detail where menu_code = '".$_POST[set_menu_info][$i][menu_code]."' and auth_templet_ix = '".$auth_templet_ix."' ";
			}
			//echo $sql;
			$db->query($sql);			

			if($db->total){
				$update_infos = $db->fetchall();
				for($j=0,$j=0;$j < count($update_infos);$j++){
					if($_POST["all_menu"]){
						$auth_templet_ix = $update_infos[$j][auth_templet_ix];
					}
					if($db->total){
						$sql = "update admin_auth_templet_detail set 
									menu_code = '".$_POST[set_menu_info][$i][menu_code]."', 
									auth_read = '".$auth_read."', 
									auth_write_update = '".$auth_write_update."',
									auth_excel = '".$auth_excel."',
									auth_delete = '".$auth_delete."' 
									where menu_code = '".$_POST[set_menu_info][$i][menu_code]."' and auth_templet_ix = '".$auth_templet_ix."' "; //auth_excel
						 
						$db->query($sql);
					}else{
						$sql = "insert into admin_auth_templet_detail
									(menu_code,auth_templet_ix,auth_read, auth_write_update, auth_delete, auth_excel, regdate)	
									values
									('".$_POST[set_menu_info][$i][menu_code]."', '$auth_templet_ix','$auth_read','$auth_write_update','$auth_delete','$auth_excel',NOW()) ";
						//echo $sql;
						$db->query($sql);
					}
				}
			}else{
				$sql = "select * from admin_auth_templet_detail where menu_code = '".$_POST[set_menu_info][$i][menu_code]."' and auth_templet_ix = '".$auth_templet_ix."' ";
				$db->query($sql);

				if($db->total){
					$sql = "update admin_auth_templet_detail set 
								menu_code = '".$_POST[set_menu_info][$i][menu_code]."', 
								auth_read = '".$auth_read."', 
								auth_write_update = '".$auth_write_update."',
								auth_excel = '".$auth_excel."',
								auth_delete = '".$auth_delete."' 
								where menu_code = '".$_POST[set_menu_info][$i][menu_code]."' and auth_templet_ix = '".$auth_templet_ix."' "; //auth_excel
					 
					$db->query($sql);
				}else{
					$sql = "insert into admin_auth_templet_detail
								(menu_code,auth_templet_ix,auth_read, auth_write_update, auth_delete, auth_excel, regdate)	
								values
								('".$_POST[set_menu_info][$i][menu_code]."', '".$auth_templet_ix."','$auth_read','$auth_write_update','$auth_delete','$auth_excel',NOW()) ";
					$db->query($sql);
				}
			}

			if($_POST["all_menu"] && $add_auth_templet_ix){
				$sql = "select * from admin_auth_templet_detail where menu_code = '".$_POST[set_menu_info][$i][menu_code]."' and auth_templet_ix = '".$add_auth_templet_ix."' ";
				$db->query($sql);

				if($db->total){
					$sql = "update admin_auth_templet_detail set 
								menu_code = '".$_POST[set_menu_info][$i][menu_code]."', 
								auth_read = '".$auth_read."', 
								auth_write_update = '".$auth_write_update."',
								auth_excel = '".$auth_excel."',
								auth_delete = '".$auth_delete."' 
								where menu_code = '".$_POST[set_menu_info][$i][menu_code]."' and auth_templet_ix = '".$add_auth_templet_ix."' "; //auth_excel
					 
					$db->query($sql);
				}else{
					$sql = "insert into admin_auth_templet_detail
								(menu_code,auth_templet_ix,auth_read, auth_write_update, auth_delete, auth_excel, regdate)	
								values
								('".$_POST[set_menu_info][$i][menu_code]."', '".$add_auth_templet_ix."','$auth_read','$auth_write_update','$auth_delete','$auth_excel',NOW()) ";
					$db->query($sql);
				}
			}

			//add_auth_templet_ix
			//echo $sql."<br>";
		}
		if($_POST[set_menu_info][$i]){
			$x++;
		}
	}
	
	//exit;
	echo "<script language='javascript' src='../js/message.js.php'></script><Script Language='JavaScript'>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</Script>";	
	//Header("Location: referer.php");
	
}

//echo "<br>maxlevel:".getMaxlevel($cid,$sub_depth);
?>