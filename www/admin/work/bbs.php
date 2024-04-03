<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.work.class");
include("work.lib.php");
session_start();

$P = new LayOut();

/*
$msb = new MsBoard($bbs_table_name);
$msb->bbs_admin_mode = true;
$msb->MsBoardConfigration($P);
$msb->bbs_template_dir = $bbs_template_dir;
$msb->bbs_compile_dir  = $bbs_compile_dir;
$msb->site_template_src  = $site_template_src;
$msb->bbs_data_dir = $bbs_data_dir;
*/
//print_r($admininfo);
$mode	= (empty($_GET['mode']))	?	$_POST['mode']:$_GET['mode'];
$act	= (empty($_GET['act']))	?	$_POST['act']:$_GET['act'];

$P->addScript = "<script type='text/javascript' src='work.js'></script>";
$P->strLeftMenu = work_menu();
$P->strContents = print_bbs("work_notice",$mode,$act,NULL,($admininfo[master] == "Y" ? true:false)) ; //$msb->PrintMsBoardList();
$P->Navigation = "업무관리 > 공지사항";
$P->title = "공지사항";
$P->prototype_use = true;
$P->footer_menu = footMenu()."".footAddContents();
echo $P->PrintLayOut();
?>
