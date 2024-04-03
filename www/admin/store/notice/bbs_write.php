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
$P = new msLayOut();
//$P->addScript = PrintEvent(2,"pop");
$P->Contents = $msb->PrintMsBoardWrite();
$P->shop_left =CommunityLeftMenu();
echo $P->LoadLayOut();
*/


//$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $msb->PrintMsBoardWrite();
$P->Navigation = "HOME > 상점관리 > 입점업체공지사항";
echo $P->PrintLayOut();

?>