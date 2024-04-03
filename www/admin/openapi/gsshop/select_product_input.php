<?

set_time_limit(9999999);

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");

//30분마다!

$db = new Database();
$db2 = new Database();

$sql = "select mall_ix,mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
$db->query($sql);
$db->fetch();

$_SESSION['admininfo']['mall_ix'] = $db->dt[mall_ix];
$_SESSION[admininfo][mall_data_root] = $db->dt[mall_data_root];
$_SESSION[admininfo][admin_level] = 9;
$_SESSION[admininfo][language] = 'korea';
$_SESSION[admininfo][mall_type] = $db->dt[mall_type];
$_SESSION[admin_config][mall_data_root] = $db->dt[mall_data_root];


$dandom_num = rand(0,10000);
$start_time = time();

$OAL = new OpenAPI('gsshop');

$array_pid2 = array();

$max = 100;

if( ! empty($_GET['page']) ){
	$start = $max*$_GET['page'];
}else{
	$start = 0;
}

$last_start = $_GET['last_start'];

//exit;
///////////////

//$sql = "select pid from sellertool_regist_relation where site_code='gsshop' and result_code='500' and result_msg !='상품등록실패 : 상품등록실패 : 상품 등록은 1일 500개 까지만 가능합니다'";
//$sql = "select * from sellertool_get_product where site_code = 'gsshop' and state='1' and pid >= '0000101299' order by pid asc"; // and result_code != 200
//$sql = "select * from sellertool_get_product where site_code='gsshop' and state='1' and sgp_ix > 28182 and pid >= 0000057702 order by pid asc";
//$sql = "select * from sellertool_get_product where site_code='gsshop' and state='1' and pid >= '0000106705' order by pid asc";


//$sql = "select gp.pid from sellertool_get_product gp left join shop_product p on (gp.pid=p.id)
//where gp.site_code='gsshop' and gp.state='1' and p.editdate between '2016-03-11 00:00:00' and '2016-03-14 23:00:00' and gp.pid >= '0000105989' order by pid asc";


$sql = "select gp.pid from sellertool_get_product gp left join shop_product p on (gp.pid=p.id)
where gp.site_code='gsshop' and gp.state='1' and p.state = '1' and p.disp='1' and p.id > '0000096090' order by pid asc limit ".$start.",".$max."";

//$sql = "select * from sellertool_get_product where site_code = 'gsshop' and state='1' and disp='1' order by pid asc";
$db->query($sql);
if($db->total){
	$pinfo=$db->fetchall("object");
	
	foreach($pinfo as $pi){
		array_push($array_pid2,$pi[pid]);
	}
}

$pinfo = array_unique($array_pid2);
///////////////

if(count($pinfo) > 0){

	//$fp = fopen($_SERVER["DOCUMENT_ROOT"].'/data/daiso_data/_logs/sellertool/gmarket_cron_' . date('Ymd') . '.log', 'a');

	foreach($pinfo as $pid){
		
		$pid = zerofill($pid);

		echo "|||||".$pid."|";
		$resulte = $OAL->lib->registGoods($pid,'');
		//$resulte = $OAL->lib->fashionplus_disp($pid,'');
		echo $resulte->resultCode."|".$resulte->message."<br/>";
		
		//exit;
		

		//syslog(1,"SELLERTOOL - " . $pid . "|" . $resulte->message);

		//$fh = '---------- START - ['.$db->dt[company_id].']('.count($pinfo).') '. $pid .' : '.$dandom_num.' [' . date('Y-m-d H:i:s') . '] ----------'. chr(13);
		//fwrite($fp, $fh);
		
		//프로세스가 중복이 될수 있어서
		/*
		if($start_time + (30*60) < time()){
			exit;
		}
		*/
	}
	
	//fclose($fp);
	
}else{
	exit;
}

if($last_start == $start){
	exit;
}

unset($pinfo);

/*
0 30
/admin/openapi/gsshop/select_product_input.php?page=0&last_start=30
31 60
/admin/openapi/gsshop/select_product_input.php?page=31&last_start=60
61 90
/admin/openapi/gsshop/select_product_input.php?page=61&last_start=90
90 113
/admin/openapi/gsshop/select_product_input.php?page=90&last_start=113
*/
echo '<script type="text/javascript">
<!--
	location.href="/admin/openapi/gsshop/select_product_input.php?page='.($page+1).'&last_start='.($last_start).'";
//-->
</script>';


?>