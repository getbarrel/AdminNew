<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

// /bbs/bbs.php 에 print_bbs 선언되어 있음

$P = new LayOut();
$db=new Database();
/*
$msb = new MsBoard($bbs_table_name);
$msb->bbs_admin_mode = true;
$msb->MsBoardConfigration($P);
$msb->bbs_template_dir = $bbs_template_dir;
$msb->bbs_compile_dir  = $bbs_compile_dir;
$msb->site_template_src  = $site_template_src;
$msb->bbs_data_dir = $bbs_data_dir;
*/

$db->query("SELECT board_name FROM bbs_manage_config where board_ename= '".$_GET["board"]."'");
$db->fetch();
$board_name = $db->dt[board_name];

if($mmode == "pop"){	//게시판 게시글을 클릭시 팝업으로 창띄우기 2014-10-08 이학봉
	$P = new ManagePopLayOut();
	$P->Navigation = "게시판관리 > 게시판관리(리스트)";
	$P->title = $board_name;
	$P->strContents = print_bbs($_GET["board"],$_GET["mode"], $_GET["act"],NULL, true); //$msb->PrintMsBoardList();
	echo $P->PrintLayOut();
}else{
	$P->strLeftMenu = bbsmanage_menu();
	$P->strContents = print_bbs($_GET["board"],$_GET["mode"], $_GET["act"],NULL, true); //$msb->PrintMsBoardList();
	$P->Navigation = "게시판관리 > 게시판관리(리스트)";
	$P->title = $board_name;
	echo $P->PrintLayOut();
}
?>
