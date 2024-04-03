<?
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/board.class");

$P = new msLayOut("{cid}");

$tpl = new Template_();
$tpl->template_dir = $P->Config[mall_templet_path];
$tpl->compile_dir  = $DOCUMENT_ROOT.$P->Config[mall_data_root]."/compile_/"; 
$tpl->assign('templet_src',$P->Config[mall_templet_webpath]);
$tpl->assign('product_src',$P->Config[mall_product_imgpath]);
$tpl->assign('images_src',$P->Config[mall_image_path]);

$tpl->define('{cid}',$P->Config[this_templet_path]);

$P->Contents = $tpl->fetch('{cid}');

echo $P->LoadLayOut();
?>
                               
