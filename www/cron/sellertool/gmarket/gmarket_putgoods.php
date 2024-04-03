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

$OAL = new OpenAPI('gmarket');

$array_pid2 = array();

$sql = "select pid, if( update_date is null, regist_date, update_date ) sr_date from sellertool_regist_relation where site_code = 'gmarket' and result_code in ('200','500') and pid in (select id from shop_product where is_delete = '1') group by pid ";
$db->query($sql);


if($db->total){
	$pinfo=$db->fetchall("object");
	
	foreach($pinfo as $pi){
		$sql = "select id from shop_product where id = '".$pi[pid]."' and if( editdate = '0000-00-00 00:00:00', regdate, editdate ) > '".$pi[sr_date]."' ";
		//echo $sql;
		$db->query($sql);
		if($db->total){
		array_push($array_pid2,$pi[pid]);
		}
	}
}
//$sql = "select p.id from shop_product p 
//left join sellertool_get_product gp on p.id = gp.pid
//where gp.site_code = 'gmarket' and gp.state = '1'
//and
//		if( p.editdate = '0000-00-00 00:00:00', p.regdate, p.editdate ) > IFNULL((select if( update_date is null, regist_date, update_date ) 
//		from sellertool_regist_relation rr where rr.site_code='gmarket' and rr.pid !='' and rr.pid = CAST(p.id as CHAR(20)) order by srl_ix desc limit 1) ,0)
//order by p.editdate asc 
//
//";
//$sql = "select p.id from shop_product p 
//left join sellertool_get_product gp on p.id = gp.pid
//left join sellertool_not_company nc on p.admin = nc.company_id
//left join sellertool_regist_relation rr on p.id = rr.pid and rr.site_code='gmarket' 
//where gp.site_code = 'gmarket' and gp.state = '1' and (nc.state !='1' or nc.state is null) and p.state not in ('6','7','8')
//and
//		if( p.editdate = '0000-00-00 00:00:00', p.regdate, p.editdate ) > IFNULL(if( rr.update_date is null, rr.regist_date, rr.update_date ),0)";

$sql = "select p.id, if( p.editdate = '0000-00-00 00:00:00', p.regdate, p.editdate ) prd_date from shop_product p 
left join sellertool_get_product gp on p.id = gp.pid
left join sellertool_not_company nc on ( p.admin = nc.company_id and gp.site_code=nc.site_code )

where gp.site_code = 'interpark_api' and gp.state = '1' and (nc.state !='1' or nc.state is null) and p.state not in ('6','7','8')
";
$db2->query($sql);


$pinfo=array();
if($db2->total){
	$pinfo=$db2->fetchall("object");
	foreach($pinfo as $pi){
		$sql = "select if( rr.update_date is null, rr.regist_date, rr.update_date ) sr_date from sellertool_regist_relation rr where rr.pid = '".$pi[id]."' and site_code = 'interpark_api' ";
		$db2->query($sql);

		if($db2->total){
			$db2->fetch();
			if($pi[prd_date] > $db2->dt[sr_date]){
				array_push($array_pid2,$pi[id]);
			}
		}else{
			array_push($array_pid2,$pi[id]);
		}
	}
}

$pinfo = array_unique($array_pid2);

unset($array_pid2);
$array_pid2 = array();
if(count($pinfo) > 0){

	$fp = fopen($_SERVER["DOCUMENT_ROOT"].'/data/entersix_data/_logs/sellertool/gmarket_cron_' . date('Ymd') . '.log', 'a');

	foreach($pinfo as $pid){
		
		//echo $pid."<br>";
		$resulte = $OAL->lib->registGoods($pid,'');
		//echo $pid."|".$resulte->message."<br/>";
		//syslog(1,"SELLERTOOL - " . $pid . "|" . $resulte->message);

		$fh = '---------- START - ['.$db->dt[company_id].']('.count($pinfo).') '. $pid .' : '.$dandom_num.' [' . date('Y-m-d H:i:s') . '] ----------'. chr(13);
		fwrite($fp, $fh);
		
		//프로세스가 중복이 될수 있어서
		if($start_time + (30*60) < time()){
			exit;
		}
		
	}
	
	fclose($fp);
	
}
unset($pinfo);



?>