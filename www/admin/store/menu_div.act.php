<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{

	if(!$gnb_use_soho) $gnb_use_soho = "N";
	if(!$gnb_use_biz) $gnb_use_biz = "N";
	if(!$gnb_use_openmarket) $gnb_use_openmarket = "N";

	$sql = "insert into admin_menu_div (div_ix,gnb_name, div_name, basic_link, gnb_use_home,gnb_use_soho, gnb_use_biz, gnb_use_wholesale, gnb_use_openmarket, gnb_use_enterprise, disp,vieworder,regdate)
			values
			('','$gnb_name','$div_name','$basic_link','$gnb_use_home','$gnb_use_soho','$gnb_use_biz','$gnb_use_wholesale','$gnb_use_openmarket','$gnb_use_enterprise','$disp','$vieworder',NOW())";
	$db->sequences = "ADMIN_MENU_DIV_SEQ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메뉴분류관리 분류가 정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='menu_div.php?mmode=$mmode';</script>");
}


if ($act == "update"){
	if(!$gnb_use_home) $gnb_use_home = "N";
	if(!$gnb_use_soho) $gnb_use_soho = "N";
	if(!$gnb_use_biz) $gnb_use_biz = "N";
	if(!$gnb_use_openmarket) $gnb_use_openmarket = "N";

	$sql = "update admin_menu_div set
			gnb_name='$gnb_name',div_name='$div_name',basic_link='$basic_link',gnb_use_home = '$gnb_use_home',gnb_use_soho='$gnb_use_soho',gnb_use_biz='$gnb_use_biz', gnb_use_wholesale='$gnb_use_wholesale',
			gnb_use_openmarket='$gnb_use_openmarket',
			gnb_use_enterprise='$gnb_use_enterprise',
			disp='$disp',vieworder='$vieworder'
			where div_ix='$div_ix' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메뉴분류관리 분류가 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'menu_div.php?mmode=$mmode';</script>");
}

if ($act == "delete"){

	$sql = "delete from admin_menu_div where div_ix='$div_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메뉴분류관리 분류가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='menu_div.php?mmode=$mmode';</script>");
}

?>
