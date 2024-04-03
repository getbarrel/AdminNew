<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include("./inventory.lib.php");

include("../logstory/class/sharedmemory.class");
//include_once("../class/layout.class");

$title_str = "창고이동현황";
//$warehouse_list_type = "MI";
//print_r($_SESSION["admininfo"]);
/*
if($_SESSION["admininfo"]["com_type"] == "BR"){
	$this_company_id = $_SESSION["admininfo"]["company_id"];
	echo $this_company_id;
}
*/
include("warehouse_move_list_base.php");
//include("orders.goods_list.php");



?>