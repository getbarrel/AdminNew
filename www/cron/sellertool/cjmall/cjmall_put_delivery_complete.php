<?

set_time_limit(9999999);

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");

//새벽 하루에 한번 처리 하기

$db = new Database();

$sql = "select mall_ix,mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
$db->query($sql);
$db->fetch();

$_SESSION['admininfo']['mall_ix'] = $db->dt[mall_ix];
$_SESSION[admininfo][mall_data_root] = $db->dt[mall_data_root];
$_SESSION[admininfo][admin_level] = 9;
$_SESSION[admininfo][language] = 'korea';
$_SESSION[admininfo][mall_type] = $db->dt[mall_type];
$_SESSION[admin_config][mall_data_root] = $db->dt[mall_data_root];

$OAL = new OpenAPI('cjmall');

$data = date('Y-m-d');
//$data = date('Y-m-d',strtotime('-1 day'));

$sql = "select 
			od.od_ix, od.co_oid, od.co_od_ix, odd.rname
		from
			shop_order_detail od left join shop_order_detail_deliveryinfo odd on (od.odd_ix=odd.odd_ix )
		where
			od.order_from='cjmall'
			and od.dc_date between '".$data." 00:00:00' and '".$data." 23:59:59' ";
$db->query($sql);
if($db->total){
	$oinfo = $db->fetchall("object");
	foreach($oinfo as $oi){
		$resulte = $OAL->lib->cjmallDeliveryComplete($oi);

		echo "|||||".$oi['od_ix']."|".$resulte->resultCode."|".$resulte->message."<br/>";
	}
}

?>