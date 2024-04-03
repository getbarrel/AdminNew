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

//$basic_date = date("YmdHms",strtotime("-5 minutes"));
//$startTime = strtotime("+1 hours",date("YmdHms",strtotime("-5 minutes")));
//$endTime = date($basic_date);

$basic_date = date("YmdHis",strtotime("-5 minutes"));
$startTime = date("YmdHis",strtotime($basic_date." -24 hours"));
$endTime = date($basic_date);


//주문 정보 조회시 실패하게 되면 재조회가 안되기 때문에 그럴경우는 해당 파일이 실행되야 함 24시간을 기준으로 하루에 두번씩 돌아주면 되지 않을까 생각됨 JK160219
getOrderDelvList('interpark_api',$startTime,$endTime);


//sellerToolUpdateOrderStatus(ORDER_STATUS_DELIVERY_ING,'400187');
//getCancelApplyOrderList('auction',$startTime,$endTime);
//sellerToolUpdateOrderStatus(ORDER_STATUS_CANCEL_COMPLETE,'400175');
//getDeliveryCancelApplyOrderList('auction',$startTime,$endTime);
//getReturnApplyOrderList('auction',$startTime,$endTime);
//sellerToolUpdateOrderStatus(ORDER_STATUS_RETURN_COMPLETE,'400175');
//getExchangeApplyOrderList('auction',$startTime,$endTime);

?>