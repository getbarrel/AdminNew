<?
include("bbs.config.php");
session_start();

$P = new LayOut();
$msb = new MsBoard($bbs_table_name);
$msb->bbs_admin_mode = false;
$msb->MsBoardConfigration($P);
$msb->bbs_template_dir = $bbs_template_dir;  
$msb->bbs_compile_dir  = $bbs_compile_dir;
$msb->site_template_src  = $site_template_src;
$msb->bbs_data_dir = $bbs_data_dir;


$P->strLeftMenu = store_menu();
$P->strContents = $msb->PrintMsBoardRead($article_no, $bbs_ix, $page);
$P->Navigation = "HOME > 마케팅지원 > 게시판관리(읽기)";
echo $P->PrintLayOut();

?>