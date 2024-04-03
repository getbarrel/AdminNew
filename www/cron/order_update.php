<?

set_time_limit(9999999);

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/inventory/inventory.lib.php");

$db = new Database;
$sql = "SELECT id as pid, option_id, COUNT( * )  as sell_ing_cnt
FROM shop_product p
left join shop_order_detail od on p.id = od.pid 
where od.status = 'IC'
GROUP BY pid
LIMIT 0 , 100 ";
$db->query($sql);
$datas = $db->fetchall();

$sql = "update shop_product set stock_use_yn ='Q'  ";
//$db->query($sql);

for($i=0;$i < count($datas);$i++){
	
	
	$sql = "update shop_product_options_detail set  option_sell_ing_cnt = '".$datas[$i][sell_ing_cnt]."' where pid = '".$datas[$i][pid]."' and id = '".$datas[$i][option_id]."' ";
	echo $sql."<br>";
//	$db->query($sql);

	$sql = "update shop_product set stock_use_yn ='Q',  sell_ing_cnt = '".$datas[$i][sell_ing_cnt]."' where id = '".$datas[$i][pid]."' ";
//	$db->query($sql);
}
 