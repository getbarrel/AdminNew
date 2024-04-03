<?
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

if($act == "getLeftmenuData"){

	$trees = getMenuData($leftmenu,"tree");
	//echo print_r($trees);
	echo json_encode($trees);
}


?>