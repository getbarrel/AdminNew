<?

set_time_limit(9999999);

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/inventory/inventory.lib.php");

$db = new Database;
$sql = "select mall_ix,mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
$db->query($sql);
$db->fetch();

$_SESSION['admininfo']['mall_ix'] = $db->dt[mall_ix];
$_SESSION[admininfo][mall_data_root] = $db->dt[mall_data_root];
$_SESSION[admininfo][admin_level] = 9;
$_SESSION[admininfo][language] = 'korea';
$_SESSION[admininfo][mall_type] = $db->dt[mall_type];
$_SESSION[admin_config][mall_data_root] = $db->dt[mall_data_root];

$startTime = date("Y-m-d",strtotime("-7 days"));
$endTime = date("Y-m-d",strtotime("+1 days"));


getProductQnaList('gmarket',$startTime,$endTime);


?>