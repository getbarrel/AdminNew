<?
include_once("../class/database.class");
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

$sql = "select mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
$db->query($sql);
$db->fetch();			

$admininfo[mall_data_root] = $db->dt[mall_data_root];
$admininfo[admin_level] = 9;
$admininfo[language] = 'korea';
$admininfo[mall_type] = $db->dt[mall_type];

$admin_config[mall_data_root] = $db->dt[mall_data_root];


 
if($goods_act == "cron_goodss_update"){
			$db = new Database;
			
			$sql = "select id, pcode, co_pid, editdate  
						from shop_product sp  where co_goods = '2' 	
						order by  editdate asc limit 1000 "; // 공유상품만 재고 업데이트 
						//, date_format(editdate, '%Y%m%d%H%i%s') as editdate  
			
						//and state != 0
			/*
			$sql = "select id, pcode, bs_goods_url, bs_site, length(pcode) as pcode_length, date_format(editdate, '%Y%m%d%H%i%s') as editdate  
						from shop_product sp  
						where bs_site = 'jcrew' and sp.id in ('0000023567','0000023566','0000023565','0000023564','0000023563','0000023562') order by  regdate desc limit 10 ";
			
			*/
			
			
			$db->query($sql);
			$reg_goodss_infos = $db->fetchall();
			//print_r($reg_goodss_infos);
			//echo count($reg_goodss_infos)."<br>";
			//syslog(LOG_INFO, "전체 상품갯수 : ".count($reg_goodss_infos)." \r");
			$mstring = "<table>";
			$mstring .= "<tr>
									<td>순</td>
									<td><b>상품 수정일자</b></td>
									<td><b>상품 URL</b></td>
									<td><b>기존 상품코드</b></td>
									<td><b>URL 상품코드</b></td>
									<td>상품코드길이</td>
									</tr>";
			for($a=0;$a < count($reg_goodss_infos);$a++){
				$act = "b2b_goodss_stock_check_one";
				
				$goodss_pid = $reg_goodss_infos[$a][co_pid];
				
				$mstring .= "<tr>
									<td>".$a."</td>
									<td><b>".$reg_goodss_infos[$a][editdate]."</b></td>
									<td>".$goods_detail_link  ."</td>
									<td><b>".$reg_goodss_infos[$a][pcode]."</b></td>
									<td><b>".$url_pcode."</b></td>
									<td>".$reg_goodss_infos[$a][pcode_length]." <br></td>
									</tr>";
				

				include $_SERVER["DOCUMENT_ROOT"]."/admin/goodss/goods_list.act.php";
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


if($goods_act == "cron_goodss_reg"){
	$db = new Database;
	$sql = "select gcs.* , ci.cid, ci.cname , ci.depth 
			from goodss_category_setting gcs
			left join shop_category_info ci on gcs.cid = ci.cid  
			where gcs.disp = '1'
			order by regdate desc  ";


	$db->query($sql); 
	$category_setting_infos = $db->fetchall();
	//print_r($category_setting_infos);
	//exit;
	for($a=0;$a < count($category_setting_infos);$a++){
		$act = "b2b_goods_reg";
		$update_type = 1;
		$search_rules[goodss_cid]= $category_setting_infos[$a][goodss_cid];
		$search_rules[company_id]= $category_setting_infos[$a][goodss_company_id];

		include $_SERVER["DOCUMENT_ROOT"]."/admin/goodss/server_goods_list.act.php";
	}

}

/*
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
