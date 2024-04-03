<?

set_time_limit(9999999);

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");

$db = new Database;
$sql = "select mall_ix,mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
$db->query($sql);
$db->fetch();

$admininfo[mall_ix] = $db->dt[mall_ix];
$admininfo[mall_data_root] = $db->dt[mall_data_root];
$admininfo[admin_level] = 9;
$admininfo[language] = 'korea';
$admininfo[mall_type] = $db->dt[mall_type];
$admin_config[mall_data_root] = $db->dt[mall_data_root];

//$startTime = '20160415';
$startTime = date("YmdHi",strtotime("-3 hour"));
$endTime = date("YmdHi");

//GSshop
//취소완료
$OAL = new OpenAPI('gsshop');
$OAL->lib->getCancelApplyOdrRequest($startTime,$endTime);

?>