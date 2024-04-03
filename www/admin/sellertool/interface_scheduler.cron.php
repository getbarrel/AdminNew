<?
include_once("../../class/database.class");
//include_once("../include/admin.util.php");
//include_once "../class/Snoopy.class.php";
//print_r($_SESSION);
//exit;



if(!function_exists('getCategoryPathByAdmin')){
		function getCategoryPathByAdmin($cid, $depth='-1'){
			global $user;
			$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
			if($cid == ""){
				return "전체";
			}
			$mdb = new Database;

			if($depth == '0'){
				$sql = "select * from ".$tb." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
			}else if($depth == '1'){
				$sql = "select * from ".$tb." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
			}else if($depth == '2'){
				$sql = "select * from ".$tb." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
			}else if($depth == '3'){
				$sql = "select * from ".$tb." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
			}else if($depth == '4'){
				$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
			}else{
				$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
				return "전체";
			}
			//echo $sql."<br>";
			$mdb->query($sql);

			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);

				if($i == 0){
					$mstring .= $mdb->dt[cname];
				}else{
					$mstring .= " > ".$mdb->dt[cname];
				}
			}
			return $mstring;
		}
}



$db = new Database;

$sql = "select company_id from common_company_detail  where com_type = 'A'  ";
$db->query($sql);
$db->fetch();	
$admininfo[company_id] = $db->dt[company_id];

$sql = "select mall_data_root, mall_type, mall_ix,mall_ename from shop_shopinfo where mall_div = 'B'  ";
$db->query($sql);
$db->fetch();			

$admininfo[mall_data_root] = $db->dt[mall_data_root];
$admininfo[admin_level] = 9;
$admininfo[language] = 'korea';
$admininfo[mall_type] = $db->dt[mall_type];
$admininfo[mall_ix] = $db->dt[mall_ix];
$admin_config[mall_data_root] = $db->dt[mall_data_root];

$mall_ename = $db->dt[mall_ename];
 
if($goods_act == "cron_sellertool_update"){

			$db = new Database;
			
			$sql = "select distinct pid, site_code, update_date  
						from sellertool_regist_relation srrs  where result_code = '200' 	
						order by  update_date asc limit 1000 "; // 공유상품만 재고 업데이트 
						//, date_format(editdate, '%Y%m%d%H%i%s') as editdate  
			
						//and state != 0
			/*
			$sql = "select id, pcode, bs_goods_url, bs_site, length(pcode) as pcode_length, date_format(editdate, '%Y%m%d%H%i%s') as editdate  
						from shop_product sp  
						where bs_site = 'jcrew' and sp.id in ('0000023567','0000023566','0000023565','0000023564','0000023563','0000023562') order by  regdate desc limit 10 ";
			
			*/
			
			
			$db->query($sql);
			$reg_goodss_infos = $db->fetchall();
			//echo count($reg_goodss_infos);
			//exit;
			//echo count($reg_goodss_infos)."<br>";
			//syslog(LOG_INFO, "전체 상품갯수 : ".count($reg_goodss_infos)." \r");
			$mstring = "<table>";
			$mstring .= "<tr>
									<td>순</td>
									<td><b>상품 수정일자</b></td>
									<td><b>상품코드</b></td>
									<td><b>사이트코드</b></td>
									</tr>";
			$act = "cron_regist";
			for($a=0;$a < count($reg_goodss_infos);$a++){
				
				
				$pid = $reg_goodss_infos[$a][pid];
				$site_code = $reg_goodss_infos[$a][site_code];
				
				
				$mstring .= "<tr>
									<td>".$a."</td>
									<td><b>".$reg_goodss_infos[$a][update_date]."</b></td>
									<td><b>".$reg_goodss_infos[$a][pid]."</b></td>
									<td><b>".$site_code."</b></td>
									</tr>";
				
				include $_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.goods.regist.to.market.act.php";
				//include $_SERVER["DOCUMENT_ROOT"]."/admin/goodss/goods_list.act.php";
				//}
				unset($goodss_pid);
				unset($option);
				unset($options);
				unset($option2);
				unset($pid);
				unset($pcode);
				unset($price);
				
			}
			$mstring .= "</table>";
			echo $mstring;
}


if($goods_act == "cron_sellertool_reg"){

	$db = new Database;
	//sclr.* , ci.cid, ci.cname , ci.depth 
	$sql = "select r.pid
			from sellertool_category_linked_relation sclr
			left join shop_category_info ci on sclr.origin_cid = ci.cid and sclr.site_code = '".$site_code."'
			left join ".TBL_SHOP_PRODUCT_RELATION." r on sclr.origin_cid = r.cid and r.basic = 1 
			left join ".TBL_SHOP_PRODUCT." p on r.pid = p.id and p.state = 1 and p.disp = 1
			where sclr.site_code = '".$site_code."' and p.state = 1 and p.disp = 1
			order by rel_date desc  ";
			//where ci.disp = '1'


	$db->query($sql); 
	$category_setting_infos = $db->fetchall();
	//echo count($category_setting_infos);
	//print_r($category_setting_infos);
	//exit;

	define_syslog_variables();
	openlog("phplog", LOG_PID , LOG_LOCAL0);
	//syslog(LOG_INFO, $mall_ename.'제휴사 자동상품등록 START');
	$act = 'cron_regist';
	for($a=0;$a < count($category_setting_infos);$a++){
		
		//$site_code = $category_setting_infos[$a][site_code];
		//$cid2 = $category_setting_infos[$a][origin_cid];
		$pid = $category_setting_infos[$a][pid];

		//if($a > 2){
		//	exit;
		//}
		
/*		
		$update_type = 1;
		
		$search_rules[mode]= "cron";
		$search_rules[goodss_cid]= $category_setting_infos[$a][goodss_cid];
		$search_rules[company_id]= $category_setting_infos[$a][goodss_company_id];
		
		$category_setting_info[gcs_ix] = $category_setting_infos[$a][gcs_ix];
		$category_setting_info[cid] = $category_setting_infos[$a][cid];
		$category_setting_info[margin_caculation_type] = $category_setting_infos[$a][margin_caculation_type];
		$category_setting_info[margin] = $category_setting_infos[$a][margin];
		$category_setting_info[usable_round] = $category_setting_infos[$a][usable_round];
		$category_setting_info[round_type] = $category_setting_infos[$a][round_type];
		$category_setting_info[round_precision] = $category_setting_infos[$a][round_precision];
*/

		include $_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.goods.regist.to.market.act.php";
	}

	//syslog(LOG_INFO, $mall_ename.'제휴사 자동상품등록 END');
	closelog();
}

/*

			echo "<script type=\"text/javascript\">alert('123다.')</script>";
exit;

Array
(
    [mode] => search
    [goodss_cid] => 000001000000000
    [goodss_depth] => 1
    [co_type] => 
    [co_goods] => 
    [cid0_1] => 000000000000000
    [cid1_1] => 000001000000000
    [goodss_cid_1] => 
    [cid3_1] => 
    [company_id] => 77672e1aa7a81494e0dc63834f0b02c7
    [brand_name] => 
    [disp] => 
    [state2] => 
    [search_type] => 
    [search_text] => 
    [max] => 10
    [x] => 39
    [y] => 17
)

*/
