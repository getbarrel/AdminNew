<?
/*
상품명에 cut_str 걸려있는거 다 제거함 kbk 13/08/06
*/


include_once("../class/layout.class");
include_once("../sellertool/sellertool.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("../campaign/mail.config.php");
include("../order/orders.lib.php");

include('../dd/config.php');


$DB = new AMysql();


$start = 1;
$max = 10;

$sql ="
SELECT * FROM con_log $WHERE ORDER BY log_date DESC LIMIT $start, $max
";

$rows = $DB->rows( $sql );

$pot['rows'] = $rows;
$pot['abc'] = '123';

$Contents = getTpl('tpl/index', $pot, true);


if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";
	$P->addScript = "<script language='javascript' src='../order/orders.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n".$Script;
	$P->strLeftMenu = order_menu();
	$P->Navigation = "주문관리 > 주문리스트";
	$P->title = "주문리스트";
    $P->NaviTitle = "주문리스트"; 
	$P->strContents = $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";

	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	if($view_type == "sellertool"){
		$P->strLeftMenu = sellertool_menu();
	}else if($view_type == "offline_order"){
		$P->strLeftMenu = offline_order_menu();
	}else if($view_type == "pos_order"){
		$P->strLeftMenu = pos_order_menu();
	}else if($view_type == "sc_order"){
		$P->strLeftMenu = sns_menu();
	}else if($view_type == "inventory"){
		$P->strLeftMenu = inventory_menu();
	}else{
		$P->strLeftMenu = order_menu();
	}
	//$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";
	//$P->addScript = "<script language='javascript' src='../order/orders.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n".$Script;
	$P->Navigation = "주문관리 > TMall 리스트";
	$P->title = "Tmall List";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

?>
