<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

$db = new Database;
if($act == "insert"){
	$db->sequences = "SHOP_ICON_SEQ";
	$db->query("insert into shop_icon (idx,icon_name,disp,regdate,icon_type) values ('','$icon_name','$disp',NOW(),'$icon_type')");
	$db->query("select idx from shop_icon where idx = LAST_INSERT_ID()");
	$db->fetch();
	$idx = $db->dt[idx];
	$path = $_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"][mall_data_root]."/images/icon/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}
	if ($_FILES["icon_file"]["size"] > 0){
		move_uploaded_file($_FILES["icon_file"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"][mall_data_root]."/images/icon/".$idx.".gif");
	}
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('아이콘이 등록 되었습니다..');parent.location.reload();</script>";
}

if($act == "update"){
	$db->query("update shop_icon set icon_name = '$icon_name', disp='$disp', icon_type = '$icon_type' where idx = '$idx' ");
	$path = $_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"][mall_data_root]."/images/icon/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}
	if ($_FILES["icon_file"]["size"] > 0){
		move_uploaded_file($_FILES["icon_file"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"][mall_data_root]."/images/icon/".$idx.".gif");
	}
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('아이콘이 수정 되었습니다..');parent.location.reload();</script>";
}

if($act == "delete"){
	$db->query("delete from shop_icon where idx = '$idx' ");
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"][mall_data_root]."/images/icon/".$idx.".gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$_SESSION["admin_config"][mall_data_root]."/images/icon/".$idx.".gif");
	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('아이콘이 삭제 되었습니다..');parent.location.reload();</script>";
}



?>