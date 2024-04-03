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

$admininfo[mall_ix] = $db->dt[mall_ix];
$admininfo[mall_data_root] = $db->dt[mall_data_root];
$admininfo[admin_level] = 9;
$admininfo[language] = 'korea';
$admininfo[mall_type] = $db->dt[mall_type];
$admin_config[mall_data_root] = $db->dt[mall_data_root];

$startTime = date("Y-m-d",strtotime("-7 days"));
$endTime = date("Y-m-d",strtotime("+1 days"));
$nowdate = date("Y-m-d");
//30분마다
//$startTime = "201409250000";
//$endTime = "201409252300";


getOrderList('gmarket',$startTime,$endTime); //주문내역확인

//getCancelApplyOrderList('gmarket',$startTime,$endTime);
getReturnApplyOrderList('gmarket',$startTime,$endTime);//반품요청내역
getDeliveryCancelApplyOrderList('gmarket',$startTime,$endTime); //취소요청내역 확인
//getExchangeApplyOrderList('gmarket',$startTime,$endTime); //교환요청내역 확인 
getOrderDeliveryComplate('gmarket',$nowdate); //배송완료내역 확인 
?>