<?
include("../../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/board.class");
include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php"); 
session_start();

$P = new LayOut();

if($board_ename){
	$bbs_table_name= "bbs_".$board_ename;
}else{
	$bbs_table_name= "bbs_b2b_notice";	
}

//$bbs_table_name = "bbs_qna";
$bbs_data_dir = "../../".$admin_config[mall_data_root]."/bbs_data";
$site_template_src = "/data/templet/basic/";	


$bbs_template_dir = "../../../bbs_templet/admin";  
$bbs_compile_dir  = $_SERVER["DOCUMENT_ROOT"]."/".$admin_config[mall_data_root]."/compile_/admin/customer/$bbs_table_name/"; 	


$navi = "홈 > <b style='color:#c733a6'>고객센타</b>";
$title_img = "title_customer_center.gif";
	
?>
