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

$sql = "select mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
$db->query($sql);
$db->fetch();			

$admininfo[mall_data_root] = $db->dt[mall_data_root];
$admininfo[admin_level] = 9;
$admininfo[language] = 'korea';
$admininfo[mall_type] = $db->dt[mall_type];

$admin_config[mall_data_root] = $db->dt[mall_data_root];
if($act == "ralphlauren"){
	$db = new Database;


	$sql = "SELECT * FROM `shop_product` WHERE pcode = 'Array' ";

	$db->query($sql);
	$ralphlauren_infos = $db->fetchall();

	for($a=0;$a < count($ralphlauren_infos);$a++){
		parse_url($ralphlauren_info[$a][bs_goods_url]);
		$sql = "update `shop_product` set pcode = '".$pcode."' where id = '".$ralphlauren_infos[$a][id]."'   ";
		echo $sql."<br><br>";
		//$db->query($sql);
	}
}


if($bs_act == "cron"){
			$db = new Database;


			$sql = "select id, pname,pcode, bs_site, bs_goods_url, buyingservice_coprice, coprice, listprice, sellprice, currency_ix, round_type, round_precision  
						from shop_product  where product_type = 1 and bs_site != 'izabel' order by regdate desc limit 10 ";

			$db->query($sql);
			$goods_infos = $db->fetchall();
			//print_r($goods_infos);
			$bs_act = "bsgoods_auto_reg";
			$act = "cron";
			include "buyingServiceInfo.auto.php";


			//for($i=0;$i < count($goods_infos);$++){

			//}

	
}

//lynx --dump 'http://selina.s1.mallstory.com/admin/product/buyingServiceInfo.cron.php?act=new_goods_reg&get_bs_site=gap'
//lynx --dump 'http://selina.s1.mallstory.com/admin/product/buyingServiceInfo.cron.php?act=new_goods_reg&get_bs_site=oldnavy'
//http://dev.forbiz.co.kr/admin/product/buyingServiceInfo.cron.php?act=new_goods_reg
//http://dev.forbiz.co.kr/admin/product/buyingServiceInfo.cron.php?act=new_goods_reg http://www.ralphlauren.com/family/index.jsp?categoryId=1767594
if($bs_act == "new_goods_reg"){
			$db = new Database;
			if($get_bs_site == ""){
				$get_bs_site = "gap";
			}
			$sql = "select cr.* , case when depth = 1 then bs_ix  else parent_bs_ix end as group_order 
						from shop_buyingservice_site cr  where site_code in ('".$get_bs_site."') 
						order by  group_order asc ,depth asc , vieworder asc ";

			//echo $sql."<br>";
			//exit;
			$db->query($sql);
			$bs_siteinfos = $db->fetchall();
			//print_r($bs_siteinfos);
			for($a=0;$a < count($bs_siteinfos);$a++){
				$sql = "select * from shop_buyingservice_url_info bsui where bs_site = '".$bs_siteinfos[$a][site_code]."' and disp=1 and cid != '' order by cid asc  ";
				//echo $sql."<br>";

				$db->query($sql);
				$bs_list_urls = $db->fetchall();

				for($b=0;$b < count($bs_list_urls);$b++){
					$bs_act = "get_goods";
					$bs_site = $bs_list_urls[$b][bs_site];
					if(substr_count($bs_list_urls[$b][bs_list_url],"gap.com")){
						$list_url = str_replace("&pageID=1","",$bs_list_urls[$b][bs_list_url]);
					}else{
						$list_url = $bs_list_urls[$b][bs_list_url];
					}
					$new_goods_reg_list_url = $list_url;
					//syslog(LOG_INFO, "new_goods_reg_list_url : ".$new_goods_reg_list_url." \r");
					$currency_ix = $bs_list_urls[$b][currency_ix];
					$cid2= $bs_list_urls[$b][cid];
					echo "<b>".$list_url."</b><br>";
					//echo $bs_list_urls[$b][cid];
					//exit;
					
//echo $sql."<br>";
//				exit;
					include "product_bsgoods.act.php";
					//print_r($goods_detail_unique_links);
					//syslog(LOG_INFO, "상품정보 : ".print_r($goods_detail_unique_links,true)." \r");
					$sql = "select date_format(edate,'%Y%m%d') as edate from shop_buyingservice_autoupdate_history where cid = '".$cid2."' and date_format(edate,'%Y%m%d') = '".date("Ymd")."'  ";
						
					
					$db->query($sql);
					$db->fetch();
					//echo $db->dt[edate];
					if(!$db->total){// && $db->dt[edate] == "00000000"
						$cname = getCategoryPathByAdmin($cid2,1);
						$sql = "insert into shop_buyingservice_autoupdate_history set 
								cid='".$cid2."',
								cname='".$cname."',
								bs_site='".$get_bs_site."',
								autoupdate_type='new_goods_reg',
								bs_list_url='".$list_url."',
								bs_list_url_md5='".$bs_list_url_md5."',
								orgin_category_info='".$orgin_category_info."',
								sdate=NOW(),
								goods_update_cnt = '".count($goods_detail_unique_links)."', 
								regdate=NOW()";
						$db->query($sql);
						$db->query("SELECT bsah_ix  FROM shop_buyingservice_autoupdate_history WHERE bsah_ix =LAST_INSERT_ID()");
						$db->fetch();
						$bsah_ix  = $db->dt[0];
					}

					if(is_array($goods_detail_unique_links)){

							

							//for($c=0;$c < count($goods_detail_unique_links);$c++){
							$c = 0;
							foreach($goods_detail_unique_links as $key => $value){
								$bs_act = "bsgoods_one_reg";
								$goods_detail_link = $value;
								//echo $b."==".$c."==".$goods_detail_link ." &nbsp;&nbsp;&nbsp;";
								include "product_bsgoods.act.php";
								unset($option);
								unset($options);
								unset($option2);
								unset($pid);
								unset($pcode);
								if($c == 2){
									//exit;
								}
								$c++;

								//exit;
							}					
					}

					$sql = "update shop_buyingservice_autoupdate_history set 								
								goods_update_complete_cnt = '$goods_reg_complete_cnt', 
								edate = NOW() 
								WHERE bsah_ix = '".$bsah_ix."'";
					$db->query($sql);
					$db->fetch();

					unset($goods_reg_complete_cnt);
					unset($goods_detail_unique_links);
					unset($goods_detail_links);
					$goods_detail_unique_links = "";
					$goods_detail_links = "";
				}
				//exit;
				//print_r($bs_list_urls);

			}
}
//bodenusa_MA368-GRY
// 이미지 카피 에러 bodenusa_MJ079-CRN

if($bs_act == "goods_update"){
			$db = new Database;
			if($get_bs_site == ""){
				$get_bs_site = "gap";
			}
			//$sql = "select id, pcode, bs_goods_url, bs_site, length(pcode) as pcode_length  from shop_product sp  where bs_site = 'bodenusa' and length(pcode) <= 14 order by  regdate desc limit 10 ";
			if($get_bs_site == "jcrew" && false){
			
			$sql = "select id, pcode, bs_goods_url, bs_site, length(pcode) as pcode_length,  editdate  
						from shop_product sp  where bs_site = 'gap' 
						and date_format(editdate, '%Y%m%d%H%i%s') < '".date("Ymd", time()+86400)."000000'  	
						order by  editdate asc limit 100"; //
			/*
			$sql = "SELECT id, pcode, bs_goods_url, bs_site, length(pcode) as pcode_length, date_format(editdate, '%Y%m%d%H%i%s') as editdate  
					FROM shop_product p right join shop_product_relation r on p.id = r.pid , common_company_detail c 
					where c.company_id = p.admin and p.product_type = 1 
					and p.bs_site = 'ralphlauren' and state = 1 
					and r.cid LIKE '033001014%' 
					group by p.id 
					order by p.regdate desc  ";
			*/
			}else{
			 //state == 1인 상품 (판매중) 만 업데이트 하도록 수정 12-11-15 bgh(대표님 지시)
			$sql = "select id, pcode, bs_goods_url, bs_site, length(pcode) as pcode_length, editdate  
						from shop_product sp  where bs_site = '".$get_bs_site."' and state = '1' 
						and date_format(editdate, '%Y%m%d%H%i%s') < '".date("Ymd")."000000'  						
						order by  editdate asc limit 100";
						//, date_format(editdate, '%Y%m%d%H%i%s') as editdate  
			}
						//and state != 0
			/*
			$sql = "select id, pcode, bs_goods_url, bs_site, length(pcode) as pcode_length, date_format(editdate, '%Y%m%d%H%i%s') as editdate  
						from shop_product sp  
						where bs_site = 'jcrew' and sp.id in ('0000023567','0000023566','0000023565','0000023564','0000023563','0000023562') order by  regdate desc limit 10 ";
			
			*/
			//echo nl2br($sql)."<br>";
			//exit;
			$db->query($sql);
			$bs_goods_orgin = $db->fetchall();
			//print_r($bs_goods_orgin);
			//echo count($bs_goods_orgin)."<br>";


			//syslog(LOG_INFO, "전체 상품갯수 : ".count($bs_goods_orgin)." \r");
			$mstring = "<table>";
			$mstring .= "<tr>
									<td>순</td>
									<td><b>상품 수정일자</b></td>
									<td><b>상품 URL</b></td>
									<td><b>기존 상품코드</b></td>
									<td><b>URL 상품코드</b></td>
									<td>상품코드길이</td>
									</tr>";
			for($a=0;$a < count($bs_goods_orgin);$a++){
				$bs_act = "bsgoods_one_update";
				//$bs_act = "bsgoods_pcode_update";
				$pid = $bs_goods_orgin[$a][id];
				$bs_site = $bs_goods_orgin[$a][bs_site];
				$goods_detail_link = $bs_goods_orgin[$a][bs_goods_url];
				
				$__bs_url = split("[/]",$goods_detail_link);
				$_pcode = split("~",$__bs_url[count($__bs_url)-2]);
				//$pcode = $__bs_url[count($__bs_url)-2];
				$url_pcode = $bs_site."_".$_pcode[1];
				//if($url_pcode != $bs_goods_orgin[$a][pcode]){
				$mstring .= "<tr>
									<td>".$a."</td>
									<td><b>".$bs_goods_orgin[$a][editdate]."</b></td>
									<td>".$goods_detail_link  ."</td>
									<td><b>".$bs_goods_orgin[$a][pcode]."</b></td>
									<td><b>".$url_pcode."</b></td>
									<td>".$bs_goods_orgin[$a][pcode_length]." <br></td>
									</tr>";
				

				include "product_bsgoods.act.php";
				//}
				unset($option);
				unset($options);
				unset($option2);
				unset($pid);
				unset($pcode);
				unset($price);
				
			}
			$mstring .= "</table>";
			//echo $mstring;
}





if($bs_act == "goods_update2"){
			$db = new Database;
			if($get_bs_site == ""){
				$get_bs_site = "gap";
			}
			//$sql = "select id, pcode, bs_goods_url, bs_site, length(pcode) as pcode_length  from shop_product sp  where bs_site = 'bodenusa' and length(pcode) <= 14 order by  regdate desc limit 10 ";
			
			$sql = "select substring(ci.cid,1,9) as cid, count(*) as product_cnt from shop_category_info ci , shop_product_relation pr , shop_product sp
						where ci.cid = pr.cid  and pr.pid = sp.id and sp.bs_site = '".$get_bs_site."' 
						group by substring(ci.cid,1,9)
						order by substring(ci.cid,1,9) asc, product_cnt desc ";
			//echo nl2br($sql);
			//exit;

			$db->query($sql);
			$bs_productreg_category_infos = $db->fetchall();

			for($_z=0; $_z < count($bs_productreg_category_infos);$_z++){
				$cid = $bs_productreg_category_infos[$_z]["cid"].str_repeat("0",6);
				$sql = "select date_format(edate,'%Y%m%d') as edate from shop_buyingservice_autoupdate_history where cid = '".$cid."' and date_format(edate,'%Y%m%d') = '".date("Ymd")."'  ";
				//echo $sql;
				
				$db->query($sql);
				$db->fetch();
				//echo $db->dt[edate];
				if(!$db->total){// && $db->dt[edate] == "00000000"
					$cname = getCategoryPathByAdmin($cid,1);
					$sql = "insert into shop_buyingservice_autoupdate_history set 
							cid='".$cid."',
							cname='".$cname."',
							bs_site='".$get_bs_site."',
							autoupdate_type='goods_update2',
							bs_list_url='".$bs_list_url."',
							bs_list_url_md5='".$bs_list_url_md5."',
							orgin_category_info='".$orgin_category_info."',
							sdate=NOW(),
							goods_update_cnt = '".$bs_productreg_category_infos[$_z]["product_cnt"]."', 
							regdate=NOW()";

					//echo $sql;
					$db->query($sql);
					$db->query("SELECT bsah_ix  FROM shop_buyingservice_autoupdate_history WHERE bsah_ix =LAST_INSERT_ID()");
					$db->fetch();
					$bsah_ix  = $db->dt[0];
				
				
					$sql = "select id, pcode, bs_goods_url, bs_site, length(pcode) as pcode_length, date_format(editdate, '%Y%m%d%H%i%s') as editdate  
								from shop_product sp, shop_product_relation pr  
								where pr.pid = sp.id and sp.bs_site = '".$get_bs_site."' and pr.cid LIKE '".substr($cid,0,9)."%'
								and date_format(editdate, '%Y%m%d%H%i%s') < '".date("Ymd")."000000'  		
								order by  editdate asc ";
					
								//and state != 0
				
					//echo nl2br($sql)."<br>";
					//exit;
					$db->query($sql);
					$bs_goods_orgin = $db->fetchall();
					//print_r($bs_goods_orgin);
					//echo count($bs_goods_orgin)."<br>";
					//syslog(LOG_INFO, "전체 상품갯수 : ".count($bs_goods_orgin)." \r");
					$mstring = "<table>";
					$mstring .= "<tr>
											<td>순</td>
											<td><b>상품 수정일자</b></td>
											<td><b>상품 URL</b></td>
											<td><b>기존 상품코드</b></td>
											<td><b>URL 상품코드</b></td>
											<td>상품코드길이</td>
											</tr>";
					for($a=0;$a < count($bs_goods_orgin);$a++){
						$bs_act = "bsgoods_one_update";
						//$bs_act = "bsgoods_pcode_update";
						$pid = $bs_goods_orgin[$a][id];
						$bs_site = $bs_goods_orgin[$a][bs_site];
						$goods_detail_link = $bs_goods_orgin[$a][bs_goods_url];
						
						$__bs_url = split("[/]",$goods_detail_link);
						$_pcode = split("~",$__bs_url[count($__bs_url)-2]);
						//$pcode = $__bs_url[count($__bs_url)-2];
						$url_pcode = $bs_site."_".$_pcode[1];


						//if($url_pcode != $bs_goods_orgin[$a][pcode]){
						$mstring .= "<tr>
											<td>".$a."</td>
											<td><b>".$bs_goods_orgin[$a][editdate]."</b></td>
											<td>".$goods_detail_link  ."</td>
											<td><b>".$bs_goods_orgin[$a][pcode]."</b></td>
											<td><b>".$url_pcode."</b></td>
											<td>".$bs_goods_orgin[$a][pcode_length]." <br></td>
											</tr>";
						include "product_bsgoods.act.php";
						//}
						unset($option);
						unset($options);
						unset($option2);
						unset($pid);
						unset($pcode);
						unset($price);
						
					}
					$mstring .= "</table>";
					$sql = "update shop_buyingservice_autoupdate_history set 								
								goods_update_complete_cnt = '$goods_update_complete_cnt', 
								goods_update_soldout_cnt = '$goods_update_soldout_cnt', 
								edate = NOW() 
								WHERE bsah_ix = '".$bsah_ix."'";
					$db->query($sql);
					$db->fetch();

					unset($goods_update_complete_cnt);
					unset($goods_update_soldout_cnt);
				}
			}
			//echo $mstring;
}
