<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include("./inventory.lib.php");

include("../logstory/class/sharedmemory.class");
//include_once("../class/layout.class");
$move_status = 'MI';
$fix_type = array('MI');
for($i=0;$i < count($fix_type);$i++){
	if($type_param == ""){
		$type_param = "type%5B%5D=".$fix_type[$i];
	}else{
		$type_param .= "&type%5B%5D=".$fix_type[$i];
	}
}
$title_str = "창고이동입고";
$warehouse_list_type = "outside_move";
//print_r($_SESSION["admininfo"]);
if($_SESSION["admininfo"]["com_type"] == "BC"){
	$move_company_id = $_SESSION["admininfo"]["company_id"];
	//echo $this_company_id;
}

include("warehouse_move_list_base.php");
//include("orders.goods_list.php");



?>