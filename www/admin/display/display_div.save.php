<?
include("../../class/database.class");
/////////////////////////////////
//  
//  제목 : 전시관리 > 분류 저장
//
/////////////////////////////////

session_start();

$db = new Database;

// 파라미터
$display_div	= $_POST["display_div"];
$mode			= $_POST["mode"];
$this_depth	= $_POST["this_depth"];
$div_name		= $_POST["div_name"];
$disp				= $_POST["disp"];
$menu_disp	= $_POST["menu_disp"];
$sub_depth	= $_POST["sub_depth"];
$parent_div_ix = $_POST["parent_div_ix"];
if (!$menu_disp) $menu_disp = "0";

if ($div_name == ""){
	msg("분류명은 필수사항 입니다.");
	exit;
}

if($mode == "update"){
	
	$sql = "SELECT * FROM ".TBL_SHOP_DISPLAY_DIV." WHERE div_ix=$div_ix ";
	$db->query($sql);
	if ($db->total){
		$db->fetch();	
	}
	
	$sql = "UPDATE ".TBL_SHOP_DISPLAY_DIV." SET 
					div_name ='".$div_name."' 
					,disp ='".$disp."' 
					,menu_disp ='".$menu_disp."' 
				WHERE div_ix = '".$div_ix."' ";
			 
	$db->query($sql);
	if (!$db->result){
		msg($__err_db);
		exit;
	}
	goUrlParent("display_div.php?display_div=".$display_div."&div_ix=".$div_ix);
}

if($mode == "insert"){
	if (!$parent_div_ix) $parent_div_ix = 0;
	if (!$sub_depth) $sub_depth = 0;

	$sql = "INSERT INTO ".TBL_SHOP_DISPLAY_DIV;
	$sql.="	(div_ix, display_div, div_name, disp, menu_disp, regdate, depth, parent_div_ix) values ('',  '$display_div', '$div_name', '$disp', '$menu_disp', now(), $sub_depth, $parent_div_ix) ";
	
	$db->query($sql);
	if (!$db->result){
		msg($__err_db);
		exit;
	}
	goUrlParent("display_div.php?display_div=".$display_div."&div_ix=".$db->last_insert_id);
}