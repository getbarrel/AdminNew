<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

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

$mode	= (empty($_GET['mode']))	?	$_POST['mode']:$_GET['mode'];
$act	= (empty($_GET['act']))	?	$_POST['act']:$_GET['act'];

//print_r();

$P->strLeftMenu = seller_menu();
if($admininfo[admin_level] == 9){
$P->strContents = print_bbs("seller_shop_cs",$mode,$act,NULL,true) ; //$msb->PrintMsBoardList();
}else{
$P->strContents = print_bbs("seller_shop_cs",$mode,$act,NULL,false) ; //$msb->PrintMsBoardList();
}
$P->Navigation = "셀러관리 > 상점 1:1문의";
$P->title = "상점 1:1문의";
$P->prototype_use = true;
echo $P->PrintLayOut();
?>
