<?
//include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
include("bbs.config.php");


$P = new LayOut();
$msb = new MsBoard($bbs_table_name);
$msb->bbs_admin_mode = true;
$msb->MsBoardConfigration($P);
$msb->bbs_template_dir = $bbs_template_dir;  
$msb->bbs_compile_dir  = $bbs_compile_dir;
$msb->site_template_src  = $site_template_src;
$msb->bbs_data_dir = $bbs_data_dir;
/*
$P = new LayOut();
$P->addScript = "";
$P->OnloadFuction = "";
$P->strLeftMenu = $bbs_leftmenu;
$P->PageTitle = $bbs_title;
$P->Navigation = $bbs_navigation;
$P->strContents = $msb->PrintMsBoardModify($bbs_ix, $article_no, $page, $comp);


echo $P->getLayOut();
*/

//$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $msb->PrintMsBoardModify($bbs_ix, $article_no, $page, $comp);
$P->Navigation = "HOME > 상점관리 > 입점업체공지사항";
echo $P->PrintLayOut();
 

?>

