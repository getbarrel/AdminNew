<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("./inventory.lib.php");


if($_COOKIE[inventory_goods_max_limit]){
	$max = $_COOKIE[inventory_goods_max_limit]; //페이지당 갯수
}else{
	$max = 50;
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if(!$info_type){
	$info_type = "lack";
}

$db = new Database;

if($admininfo[admin_level] == 9){
	$where = "where g.gid Is NOT NULL ";

	if($admininfo[mem_type] == "MD"){
		$where .= " and g.admin in (".getMySellerList($admininfo[charger_ix]).") ";
	}

}else{
	$where = "where g.gid Is NOT NULL and g.admin ='".$admininfo[company_id]."' ";
}


if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
	//다중검색 시작 2014-04-10 이학봉
	if($search_text != ""){
		if(strpos($search_text,",") !== false){
			$search_array = explode(",",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			$count_where .= "and ( ";
			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
			$count_where .= ")";
		}else if(strpos($search_text,"\n") !== false){//\n
			$search_array = explode("\n",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			$count_where .= "and ( ";

			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
			$count_where .= ")";
		}else{
			$where .= " and ".$search_type." = '".trim($search_text)."'";
			$count_where .= " and ".$search_type." = '".trim($search_text)."'";
		}
	}
}else{
	if($search_type !="" && $search_text != ""){
		$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
	}
}

if($company_id != ""){
	$where .= "and pi.company_id = '".$company_id."' ";
}

if($pi_ix != ""){
	$where .= "and pi.pi_ix = '".$pi_ix."' ";
}

if($ps_ix != ""){
	$where .= "and ps.ps_ix = '".$ps_ix."' ";
}

if($item_acccount != ""){
	$where .= "and g.item_acccount = '".$item_acccount."' ";
}

if($is_use !=""){
	$where .= "and g.is_use = '".$is_use."' ";
}

if($item_account !=""){
	$where .= "and g.item_account = '".$item_account."' ";
}

if(is_array($status)){
	for($i=0;$i < count($status);$i++){
		if($status[$i]){
			if($status_str == ""){
				$status_str .= "'".$status[$i]."'";
			}else{
				$status_str .= ", '".$status[$i]."' ";
			}
		}
	}

	if($status_str != ""){
		$where .= "and g.status in ($status_str) ";
	}
}else{
	if($status){
		$where .= "and g.status = '$status' ";
	}
}


switch ($depth){
	case 0:
		$cut_num = 3;
		break;
	case 1:
		$cut_num = 6;
		break;
	case 2:
		$cut_num = 9;
		break;
	case 3:
		$cut_num = 12;
		break;
	case 4:
		$cut_num = 15;
		break;
}

if ($cid2){
	$where .= " and g.cid LIKE '".substr($cid2,0,$cut_num)."%' ";
}

/*
$sql = "select count(*) as total
			from 
			(
				select g.cid,g.gname, g.gcode, g.admin, g.item_account , g.basic_unit, gu.sellprice, g.ci_ix, g.pi_ix, 
				pi.place_name, pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.gu_ix,gu.unit, g.gid,   gu.safestock, gu.sell_ing_cnt, gu.order_cnt, gu.buying_price, gu.barcode, ips.vdate, gu.total_stock, gu.avg_price , 
				(select IFNULL(sum(order_cnt),0) as order_ing_cnt from inventory_order o, inventory_order_detail od where o.ioid = od.ioid and od.gid = g.gid and o.status not in ('CC','OC','DC','WP')   limit 1) as order_ing_cnt 
				from inventory_goods g 
				right join inventory_goods_unit gu  on g.gid =gu.gid
				left join  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
				left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
				left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
				$where
				 group by gu.gid , gu.unit
			 ) data
			 where (stock - sell_ing_cnt + order_ing_cnt - safestock) < 0 ";

$db->query($sql);
$db->fetch();
$total = $db->dt[total];
*/

/*
if($orderby != "" && $ordertype != ""){
	if($orderby=="company_name"){
		$orderbyString2 = " order by $orderby $ordertype ";
	}else{
		$orderbyString = " order by $orderby $ordertype ";
	}
}else{
	$orderbyString = "order by g.regdate desc";
}
*/

$today = date("Ymd", time()-84600*7);
$voneweekago = date("Ymd", time()-84600*7);
$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));

//위험함 ㅜ 나중에 쿼리 수정해야함
$sql = "select SQL_CALC_FOUND_ROWS data.*,
			ifnull((select sum(pcnt) as last_week_sell_ing_cnt from shop_order_detail od2 where od2.pcode = data.gu_ix and od2.status not in ('SR','RR','IR','IB','CC','RC') and date_format(regdate,'%Y%m%d') between ".$voneweekago." and ".$today." ) ,'0') as last_week_sell_ing_cnt,
			ifnull((select sum(pcnt) as last_month_sell_ing_cnt from shop_order_detail od2 where od2.pcode = data.gu_ix and od2.status not in ('SR','RR','IR','IB','CC','RC') and date_format(regdate,'%Y%m%d') between ".$vonemonthago." and ".$today." ) ,'0') as last_month_sell_ing_cnt
			from 
			(
				select g.cid,g.gname, g.gcode, g.admin, g.item_account , g.basic_unit, gu.sellprice, g.ci_ix, g.pi_ix, 
				pi.place_name, pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.gu_ix,gu.unit, g.gid,   gu.safestock, gu.sell_ing_cnt, gu.order_cnt, gu.buying_price, gu.barcode, ips.vdate, gu.total_stock, gu.avg_price , 
				(select IFNULL(sum(order_cnt),0) as order_ing_cnt from inventory_order o, inventory_order_detail od where o.ioid = od.ioid and od.gid = g.gid and o.status not in ('CC','OC','DC','WP')   limit 1) as order_ing_cnt 
				from inventory_goods g 
				right join inventory_goods_unit gu  on g.gid =gu.gid
				left join  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
				left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
				left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
				$where
				 group by gu.gid , gu.unit
				 $orderbyString 
			 ) data
			 where (stock - sell_ing_cnt + order_ing_cnt - safestock) < 0
			$orderbyString2
			".($mode=='excel' || $mmode=='report'? "":"LIMIT $start, $max")."";

//echo "<br><br>";
//echo nl2br($sql);
//exit;
$db->query($sql);
$goods_infos = $db->fetchall();

$db->query("SELECT FOUND_ROWS() AS total");
$db->fetch();
$total = $db->dt[total];


if($mode == "excel"){
	include("excel_out_columsinfo.php");
	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='inventory_excel_".$info_type."' ";

	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='inventory_excel_checked_".$info_type."' ";
	//echo $sql;
	$db->query($sql);
	$db->fetch();
	$stock_report_excel_checked = $db->dt[conf_val];

	$check_colums = unserialize(stripslashes($stock_report_excel_checked));
	//print_r($check_colums);
	//print_r($colums);
	//exit;
	$columsinfo = $colums;

	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$inventory_excel = new PHPExcel();

	// 속성 정의
	$inventory_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");
	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
		$col++;

		//xlsWriteLabel(0,$j,$columsinfo[$value][title]);
		//$j++;
	}
	/*
	$inventory_excel->getActiveSheet(0)->setCellValue('A' . 1, "번호");
	$inventory_excel->getActiveSheet(0)->setCellValue('B' . 1, "재고품목아이디");
	$inventory_excel->getActiveSheet(0)->setCellValue('C' . 1, "카테고리");
	$inventory_excel->getActiveSheet(0)->setCellValue('D' . 1, "품목명");
	$inventory_excel->getActiveSheet(0)->setCellValue('E' . 1, "규격");
	$inventory_excel->getActiveSheet(0)->setCellValue('F' . 1, "단품(규격)코드");
	$inventory_excel->getActiveSheet(0)->setCellValue('G' . 1, "보관장소");
	$inventory_excel->getActiveSheet(0)->setCellValue('H' . 1, "재고");
	$inventory_excel->getActiveSheet(0)->setCellValue('I' . 1, "출고예정재고");
	$inventory_excel->getActiveSheet(0)->setCellValue('J' . 1, "안전재고");
	*/

	$before_pid = "";
	
	for ($i = 0; $i < count($goods_infos); $i++)
	{
		/*
		if(file_exists(InventoryPrintImage($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "m"))){
			$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/product", $goods_infos[$i][id], "m");
		}else{
			$img_str = "../image/no_img.gif";
		}
		*/

		$j="A";
		foreach($check_colums as $key => $value){
			if($key == "item_account"){
				$value_str = $ITEM_ACCOUNT[$goods_infos[$i][item_account]];
			}else if($key == "ci_ix"){
				$value_str = strip_tags(SelectSupplyCompany($_SESSION["admininfo"]["company_id"], $goods_infos[$i][ci_ix],'ci_ix','text','false'));
			}else if($key == "pi_ix"){
				$value_str = $goods_infos[$i][place_name]; //strip_tags(SelectSupplyCompany($goods_infos[$i][ci_ix],'ci_ix','text','false'));
			}else if($key == "item_barcode"){
				$value_str = $goods_infos[$i][barcode]." ";
			}else if($key == "cid"){
				$value_str = $goods_infos[$i][cid]." ";
			}else if($key == "buying_price_share"){
				if($stock_assets_sum > 0){
					$value_str = number_format($goods_infos[$i][stock_assets]/$stock_assets_sum*100,2);
				}else{
					$value_str =  0;
				}
			}else if($key == "stock_share"){
				if($stock_sum > 0){
					$value_str = number_format($goods_infos[$i][stock]/$stock_sum*100,2);
				}else{
					$value_str =  0;
				}
			}else if($key == "order_share"){
				if($order_cnt_sum > 0){
					$value_str = number_format($goods_infos[$i][order_cnt]/$order_cnt_sum*100,2);
				}else{
					$value_str =  0;
				}
			}else if($key == "wantage_stock"){
				$value_str =  $goods_infos[$i][stock]-$goods_infos[$i][sell_ing_cnt]+$goods_infos[$i][order_ing_cnt]-$goods_infos[$i][safestock];
				if( $value_str > 0){
					$value_str = 0;
				}
			}else if($key == "cname"){
				$value_str = getIventoryCategoryPathByAdmin($goods_infos[$i][cid], 4);
				
			}else{
				$value_str = $goods_infos[$i][$value];//$db1->dt[$value];
			}
			$inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
			$j++;
		}
		$z++;
		/*
		$inventory_excel->getActiveSheet()->setCellValue('A' . ($i + 2), ($i + 1));
		$inventory_excel->getActiveSheet()->setCellValue('B' . ($i + 2), $goods_infos[$i][gid]);
		$inventory_excel->getActiveSheet()->setCellValue('C' . ($i + 2), getIventoryCategoryPathByAdmin($goods_infos[$i][cid], 4));
		$inventory_excel->getActiveSheet()->setCellValue('D' . ($i + 2), $goods_infos[$i][gname]);
		$inventory_excel->getActiveSheet()->setCellValue('E' . ($i + 2), $goods_infos[$i][standard]);
		$inventory_excel->getActiveSheet()->setCellValue('F' . ($i + 2), $goods_infos[$i][item_code]);
		$inventory_excel->getActiveSheet()->setCellValue('G' . ($i + 2), $goods_infos[$i][place_name]);
		$inventory_excel->getActiveSheet()->setCellValue('H' . ($i + 2), $goods_infos[$i][stock]);
		$inventory_excel->getActiveSheet()->setCellValue('I' . ($i + 2), $goods_infos[$i][sell_ing_cnt]);
		$inventory_excel->getActiveSheet()->setCellValue('J' . ($i + 2), $goods_infos[$i][safestock]);
		*/

	}

	// 첫번째 시트 선택
	$inventory_excel->setActiveSheetIndex(0);

	// 너비조정
	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}
	/*
	$inventory_excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$inventory_excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$inventory_excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$inventory_excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$inventory_excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$inventory_excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$inventory_excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$inventory_excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$inventory_excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$inventory_excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	//$inventory_excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	//$inventory_excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	*/

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="stock_report_'.$info_type.'.xls"');
	header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'CSV');
	$objWriter->setUseBOM(true);
	$objWriter->save('php://output');

	exit;
}

if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
}
$str_page_bar = page_bar($total, $page, $max, $query_string,"");


$Contents =	"
<script  id='dynamic'></script>
<table border=0 cellpadding=0 cellspacing=0 width='100%'>
			<tr>
			    <td align='left' colspan=4 > ".GetTitleNavigation("안전/부족재고현황", "재고관리 > 안전/부족재고현황")."</td>
			</tr>";
if($mmode != "report"){
	$Contents .=	"
			<!--tr>
				<td align='left' colspan=4 style='padding-bottom:15px;'>
					<div class='tab'>
					<table class='s_org_tab'>
						<tr>
							<td class='tab'>
								<table id='tab_00' ".($info_type == "lack" ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='lack_stock.php'\">품목별 안전/부족재고현황</td>
										<th class='box_03'></th>
									</tr>
								</table>
								<table id='tab_01'  ".($info_type == "company"  ? "class='on' ":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='lack_stock.php'\">사입장별 안전/부족재고현황</td>
										<th class='box_03'></th>
									</tr>
								</table>
							</td>
							<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>
							</td>
						</tr>
					</table>
				</div>
				</td>
			</tr-->";
}else{
	$Contents .=	"
			<tr>
				<td align='left' colspan=2 style='padding-bottom:15px;vertical-align:bottom;'>
					<table width='500px' border='0' cellspacing='0' cellpadding='0' >
					<tr>
						<td width='10%' height='31' valign='middle' style='color:#000000;border-bottom:1px solid #efefef;font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-right:20px;' nowrap>
							<img src='../v3/images/common/arrow_icon02.gif' align=absmiddle> 안전/부족제고 현황
						</td>
						<td width='90%' align='right' valign='middle' style='border-bottom:1px solid #efefef;'>
							&nbsp;$navigation
						</td>
					</tr>
					<tr height=30><td colspan=2>현황일자 : ".date("Y.m.d / H:i:s")."</td></tr>
					</table>
				</td>
				<td align='right' colspan=2 style='padding-bottom:15px;width:330px;'>
					<table cellpadding=0 cellspacing=0 border=0 width=330 align=right class='input_table_box'>
							<col width='33%'>
							<col width='33%'>
							<col width='33%'>						
							<tr height='30'>		
								<td class='list_box_td list_bg_gray' style='text-align:center; padding:5px;'><b> </b></td>		
								<td class='list_box_td list_bg_gray' style='text-align:center; padding:5px;'><b> </b></td>		
								<td class='list_box_td list_bg_gray' style='text-align:center; padding:5px;'><b> </b></td>						
							</tr>
							<tr height='60'>		
								<td class='list_box_td' style='text-align:center; padding:5px;'> </td>		
								<td class='list_box_td' style='text-align:center; padding:5px;'> </td>		
								<td class='list_box_td' style='text-align:center; padding:5px;'> </td>
							</tr>
					</table>
				</td>
			</tr>
			";

}

if($mmode != "report"){
$Contents .=	"
			<tr>
				<td colspan=4>
					<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
					<input type='hidden' name='mode' value='search'>
					<input type='hidden' name='cid2' value='$cid2'>
					<input type='hidden' name='depth' value='$depth'>
					<input type='hidden' name='info_type' value='$info_type'>
					<!--input type='hidden' name='sprice' value='0' />
					<input type='hidden' name='eprice' value='1000000' /-->
					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:0px'>
								<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
									<col width='150' >
									<col width='*' >
									<col width='150' >
									<col width='*' >
									<tr>
										<td class='input_box_title'>  <b>선택된 품목분류</b>  </td>
										<td class='input_box_item' colspan=3><b id='select_category_path1'>".($search_text == "" ? getIventoryCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>
									</tr>
									<tr>
										<td class='input_box_title'><b>품목분류</b></td>
										<td class='input_box_item' colspan=3>
											<table border=0 cellpadding=0 cellspacing=0>
												<tr>
													<td style='padding-right:5px;'>".getInventoryCategoryList("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
													<td style='padding-right:5px;'>".getInventoryCategoryList("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
													<td style='padding-right:5px;'>".getInventoryCategoryList("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
													<td>".getInventoryCategoryList("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<!--td class='input_box_title'>주거래처</td>
										<td class='input_box_item' >
											".SelectSupplyCompany($ci_ix,'ci_ix','select','false')."
										</td-->
										<td class='input_box_title'>사업장/창고</td>
										<td class='input_box_item'>
											".SelectEstablishment($company_id,"company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
											".SelectInventoryInfo($company_id, $pi_ix,'pi_ix','select','false', "onChange=\"loadPlaceSection(this,'ps_ix')\" ")."
											".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","false" )." 
										</td>	
										<td class='input_box_title'>판매상태</td>
										<td class='input_box_item'>
											<input type=checkbox name='status[]' class=nonborder value='1' id='status_1' validation=false title='사용유무' ".CompareReturnValue("1",$status," checked")."><label for='status_1'>판매중</label>
											<input type=checkbox name='status[]' class=nonborder value='0' id='status_0' validation=false title='사용유무' ".CompareReturnValue("0",$status," checked")."><label for='status_0'>일시품절</label>
											<input type=checkbox name='status[]' class=nonborder value='2' id='status_2' validation=false title='사용유무' ".CompareReturnValue("2",$status," checked")."><label for='status_2'>단종(품절)</label>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>품목계정</td>
										<td class='input_box_item' >
											".getItemAccount($item_account)."
										</td>
										<td class='input_box_title'>품목사용여부</td>
										<td class='input_box_item'>
											<input type=radio name='is_use' class=nonborder value='' id='is_use_A' validation=false title='사용유무' ".($is_use == "" ? "checked":"")."><label for='is_use_A'>전체</label>
											<input type=radio name='is_use' class=nonborder value='Y' id='is_use_Y' validation=false title='사용유무' ".($is_use == "Y" ? "checked":"")."><label for='is_use_Y'>사용</label>
											<input type=radio name='is_use' class=nonborder value='N' id='is_use_N' validation=false title='사용유무' ".($is_use == "N" ? "checked":"")."><label for='is_use_N'>미사용</label>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'>  
											<b>검색어</b>  
											<br/>
											<label for='mult_search_use'>(다중검색 체크)</label> <input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
										</td>
										<td class='input_box_item' valign='middle' style='' colspan='3' >
											<table cellpadding=0 cellspacing=0>
												<tr>
													<td>
														<select name='search_type'  style=\"font-size:12px;height:22px;min-width:140px;\">
															<option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type).">대표코드</option>
															<option value='g.gid' ".CompareReturnValue("g.gid",$search_type).">품목코드</option>
															<option value='g.gname' ".CompareReturnValue("g.gname",$search_type).">품목명</option>
															<option value='gu.gu_ix' ".CompareReturnValue("gu.gu_ix",$search_type).">시스템코드</option>
															<option value='gu.barcode' ".CompareReturnValue("gu.barcode",$search_type).">바코드</option>
														</select>
													</td>
													<td style='padding-left:5px;'>
														<div id='search_text_input_div'>
															<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
														</div>
														<div id='search_text_area_div' style='display:none;'>
															<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
														</div>
													</td>
													<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
												</tr>
											</table>
										</td>
									</tr>
									<!--tr>
										<td class='input_box_title'><b>재고상태</b></td>
										<td class='input_box_item' colspan='3'>
										<input type='radio' name='stock_status' value='whole' id='owhole' ".CompareReturnValue("whole","$stock_status"," checked")."><label for='owhole'>전체</label>
										<input type='radio' name='stock_status' value='soldout' id='osoldout' ".CompareReturnValue("soldout","$stock_status"," checked")."><label for='osoldout'>품절</label>
										<input type='radio' name='stock_status' value='shortage' id='oshortage' ".CompareReturnValue("shortage","$stock_status"," checked")."><label for='oshortage'>부족</label>
										<input type='radio' name='stock_status' value='surplus' id='osurplus' ".CompareReturnValue("surplus","$stock_status"," checked")."><label for='osurplus'>여유</label>
										</td>
									</tr-->
								</table>
							</td>
							<th class='box_06'></th>
						</tr>
						<tr>
							<th class='box_07'></th>
							<td class='box_08'></td>
							<th class='box_09'></th>
						</tr>
						<tr >
							<td colspan=3 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
							
						</tr>
					</table>
					</form>
				</td>
			</tr>
			
			<tr>
			    <td>
				</td>
				<td align='right' colspan=3 style='padding:5px 0 5px 0;'>

				<span style='position:relative;bottom:7px;'>
				목록수 : <select name='max' id='max' style=''>
						<option value='5' ".($_COOKIE[inventory_goods_max_limit] == '5'?'selected':'').">5</option>
						<option value='10' ".($_COOKIE[inventory_goods_max_limit] == '10'?'selected':'').">10</option>
						<option value='20' ".($_COOKIE[inventory_goods_max_limit] == '20'?'selected':'').">20</option>
						<option value='30' ".($_COOKIE[inventory_goods_max_limit] == '30'?'selected':'').">30</option>
						<option value='50' ".($_COOKIE[inventory_goods_max_limit] == '50'?'selected':'').">50</option>
						<option value='100' ".($_COOKIE[inventory_goods_max_limit] == '100'?'selected':'').">100</option>
						<option value='500' ".($_COOKIE[inventory_goods_max_limit] == '500'?'selected':'').">500</option>
						</select>
				</span>

				<a href=\"javascript:PoPWindow3('lack_stock.php?mmode=report&info_type=".$info_type."&".$QUERY_STRING."',970,800,'lack_stock')\"> <img src='../images/".$admininfo["language"]."/btn_report_print.gif'></a>
				<a href='?mmode=pop'> </a> ";
		
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= "
			<a href='excel_config.php?".$QUERY_STRING."' rel='facebox' ><span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></span></a>";
		}else{
			$Contents .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= " <a href='stock_report.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
		
	$Contents .= "
				</td>
			</tr>";

}

$Contents .= "
			<tr>
			<td  colspan=4 valign=top style='padding:0px;padding-top:0px;' id=product_stock>
			";

$innerview = "
			<form name='stockfrm' method=post action='product_stock.act.php' target='act'>
			<input type='hidden' name='act' value='update'>
			<input type='hidden' name='cid' value='$cid'>
			<input type='hidden' name='depth' value='$depth'>
			<input type='hidden' name='info_type' value='$info_type'>
			<input type='hidden' id='gu_ix' value=''>
			<table cellpadding=3 cellspacing=0  width='100%' class='list_table_box'>
			<col width='5%'>
			<col width='5%'>
			<col width='*%'>
			<col width='7%'>
			<col width='7%'>
			<col width='8%'>
			<col width='6%'>
			<col width='7%'>
			<col width='7%'>
			<col width='6%'>
			<col width='6%'>
			<col width='6%'>
			<col width='6%'>
			<col width='6%'>
			<tr align=center height=30>
				<td class=s_td rowspan='2' nowrap>".($mmode == "report" ? "순번":"<input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.stockfrm)'>")."</td>
				<td class=m_td rowspan='2'>품목코드</td>
				<td class=m_td rowspan='2' nowrap>이미지/품목명</td>				
				<td class=m_td rowspan='2'>품목계정</td>
				<td class=m_td rowspan='2' nowrap>단위</td>
				<td class=m_td rowspan='2' nowrap>규격</td>
				<td class=m_td rowspan='2' nowrap>전주<br/>판매수량</td>
				<td class=m_td rowspan='2' nowrap>최근한달<br/>판매수량</td>
				<td class=m_td  colspan='5'>재고현황</td>
				<td class=e_td style='".($mmode == "report" ? "display:none;":"")."padding:0px 3px;'  rowspan='2' nowrap>구매<br/>요청수량</td>
			</tr>
			
			<tr align=center height=30>
				<td class=m_td nowrap>재고</td>
				<td class=m_td nowrap>진행재고(-)</td>
				<td class=m_td nowrap>발주미입고(+)</td>
				<td class=m_td nowrap>안전재고(-)</td>
				<td class=m_td nowrap>부족재고</td>
			</tr>
			";

if(count($goods_infos) == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan='14' align=center> 해당되는  품목이 없습니다.</td></tr>";
}else{

	$before_pid = "";
	
	for ($i = 0; $i < count($goods_infos); $i++)
	{

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
			$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c");
		}else{
			$img_str = "../image/no_img.gif";
		}

		$no = $total - ($page - 1) * $max - $i;
		$wantage_stock = $goods_infos[$i][stock]-$goods_infos[$i][sell_ing_cnt]+$goods_infos[$i][order_ing_cnt]-$goods_infos[$i][safestock];
		if( $wantage_stock > 0){
			$wantage_stock = 0;
		}

		$innerview .= "<tr bgcolor='#ffffff' height=26 align=center>
						<td class='list_box_td list_bg_gray' align=center>
							".($mmode == "report" ? $no :"<input type=checkbox class=nonborder id='gu_ix' name='gu_ix[]' value='".$goods_infos[$i][gu_ix]."'>")."
						</td>
						<td bgcolor=#ffffff style='padding:0px 3px;' >".$goods_infos[$i][gid]."</td>
						<td class='list_box_td point' nowrap>
							<table cellpadding=0 cellspacing=0>
								<tr>";
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))){
		$innerview .= "
									<td bgcolor='#ffffff' align=center style='padding:5px 3px' >
										<a href='../inventory/inventory_goods_input.php?gid=".$goods_infos[$i][gid]."' class='screenshot'  rel='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "basic")."'><img src='".$img_str."' width=30 height=30 style='border:1px solid #efefef'></a>
									</td>";
		}
		$innerview .= "
									<td bgcolor='#ffffff' align=left style='font-weight:normal;line-height:140%;'>
									<a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$goods_infos[$i][gid]."',970,800,'item_info')\"><b>".$goods_infos[$i][gname]."</b></a>
									</td>
								</tr>
							</table>
						</td>
					
					
					<td bgcolor=#ffffff style='padding:0px 3px;' nowrap>".$ITEM_ACCOUNT[$goods_infos[$i][item_account]]."</td>
					<td bgcolor=#ffffff nowrap>".getUnit($goods_infos[$i][unit], "basic_unit","","text")."</td>
					<td bgcolor=#ffffff nowrap>".$goods_infos[$i][standard]."</td>
					<td bgcolor=#ffffff nowrap>".number_format($goods_infos[$i][last_week_sell_ing_cnt])."</td>
					<td bgcolor=#ffffff nowrap>".number_format($goods_infos[$i][last_month_sell_ing_cnt])."</td>
					<td class='list_box_td point'>".number_format($goods_infos[$i][stock])."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][sell_ing_cnt]."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][order_ing_cnt]."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][safestock]."</td>
					<td bgcolor=#ffffff>".number_format($wantage_stock)."</td>
					<td bgcolor=#ffffff style='".($mmode == "report" ? "display:none;":"")."padding:0px 5px;' nowrap>
						준비중
					</td>
				</tr>";

	}

}
	$innerview .= "</table>";

if($mmode != "report"){
	$innerview .= "
				<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=40><td></td>
					<td align=right nowrap>".$str_page_bar."</td></tr>
				</table>";
}else{
	$innerview .= "<br><br><br>";
}
	$innerview .= "			
				</form>";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
			";



$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>각 품목별 및 옵션별로 안전/부족재고현황을 보실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >옵션 항목의 재고가 부족, 품절일 경우도 리스트에 각 상태에 따라 출력되게 됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >재고 상태 검색시 카테고리에 등록되어 있지 않은 품목은 나오지 않습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td>재고자산은 원가법으로 산출하였으며, 현재 이동평균법의 재고원가를 산출됩니다. 약간의 오차가 발생할 수 있으며, 회계용으로 사용하지 마시고 참고용으로 사용하세요.</td></tr>
</table>
";

$Contents .= HelpBox("품목별 안전/부족재고현황", $help_text);



$Contents .="<object id='factory' style='display:none' viewastext classid='clsid:1663ed61-23eb-11d2-b92f-008048fdd814'
codebase='http://".$_SERVER["HTTP_HOST"]."/admin/order/scriptx/smsx.cab#Version=7,1,0,60'>
</object>";

if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid, $depth);
	echo "
	<Script>
	parent.document.getElementById('product_stock').innerHTML = document.body.innerHTML;
	parent.document.getElementById('select_category_path1').innerHTML='".$inner_category3_path."';
	</Script>";
}else{
	$Script = "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<!--script Language='JavaScript' src='../include/zoom.js'></script-->\n
	<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
	<script Language='JavaScript' type='text/javascript'>
	
	$(document).ready(function (){

	//다중검색어 시작 2014-04-10 이학봉

		$('input[name=mult_search_use]').click(function (){
			var value = $(this).attr('checked');

			if(value == 'checked'){
				$('#search_text_input_div').css('display','none');
				$('#search_text_area_div').css('display','');
				
				$('#search_text_area').attr('disabled',false);
				$('#search_texts').attr('disabled',true);
			}else{
				$('#search_text_input_div').css('display','');
				$('#search_text_area_div').css('display','none');

				$('#search_text_area').attr('disabled',true);
				$('#search_texts').attr('disabled',false);
			}
		});

		var mult_search_use = $('input[name=mult_search_use]:checked').val();
			
		if(mult_search_use == '1'){
			$('#search_text_input_div').css('display','none');
			$('#search_text_area_div').css('display','');

			$('#search_text_area').attr('disabled',false);
			$('#search_texts').attr('disabled',true);
		}else{
			$('#search_text_input_div').css('display','');
			$('#search_text_area_div').css('display','none');

			$('#search_text_area').attr('disabled',true);
			$('#search_texts').attr('disabled',false);
		}
		

		$('#max').change(function(){
			var value= $(this).val();
			$.cookie('inventory_goods_max_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
			document.location.reload();
		});

	//다중검색어 끝 2014-04-10 이학봉

	});

	function clearAll(frm){
		for(i=0;i < frm.gu_ix.length;i++){
				frm.gu_ix[i].checked = false;
		}
	}

	function checkAll(frm){
		for(i=0;i < frm.gu_ix.length;i++){
				frm.gu_ix[i].checked = true;
		}
	}

	function fixAll(frm){
		if (!frm.all_fix.checked){
			clearAll(frm);
			frm.all_fix.checked = false;
				
		}else{
			checkAll(frm);
			frm.all_fix.checked = true;
		}
	}

	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var depth = sel.getAttribute('depth');

		//빈값일 경우에는 카테고리 정보 불러오는 파일에서 처리함 kbk 13/08/08
		//if(sel.selectedIndex!=0) {
			window.frames['act'].location.href = 'inventory_category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//}

	}

	function reloadView(){
	
		if($('#view_shotage_goods').attr('checked') == true || $('#view_shotage_goods').attr('checked') == 'checked'){		
			$.cookie('view_shotage_goods', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('view_shotage_goods', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
		
		document.location.reload();
	
	}

	
	var initBody ;

	function beforePrint() {
		initBody = document.body.innerHTML; document.body.innerHTML = document.getElementById('print_area').innerHTML;
		//alert(document.body.innerHTML);
	}

	function afterPrint() {
		document.body.innerHTML = initBody;
	}

	function printArea() {";

	if($mmode == "report"){
		$Script .= "	window.focus(); window.print();";
	}else{
		$Script .= "	window.print();";
	}
	$Script .= "
	}

	window.onbeforeprint = beforePrint;
	window.onafterprint = afterPrint;";

	if($mmode == "report"){
	$Script .= "
	$(document).ready(function() {
		printArea();
		//printPage();
	});";
	}

	$Script .= "

	function printPage() {
		//alert(1);

		factory.printing.header = ''; // Header에 들어갈 문장
		factory.printing.footer = ''; // Footer에 들어갈 문장
		factory.printing.portrait = true // true 면 세로인쇄, false 면 가로인쇄
		factory.printing.leftMargin = 0.2 // 왼쪽 여백 사이즈
		factory.printing.topMargin = 0.2 // 위 여백 사이즈
		factory.printing.rightMargin = 0.2 // 오른쪽 여백 사이즈
		factory.printing.bottomMargin = 0.2 // 아래 여백 사이즈
		factory.printing.Print(false,window) // 출력하기

	}

	</script>";

	if($mmode == "pop" || $mmode == "report"){
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->Navigation = "재고관리 > 안전/부족재고현황";
		$P->NaviTitle = "안전/부족재고현황";
		$P->title = "안전/부족재고현황";
		$P->strContents = $Contents;
		$P->OnloadFunction = "";
		$P->layout_display = false;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->strLeftMenu = inventory_menu();
		$P->addScript = $Script;
		$P->Navigation = "재고관리 > 안전/부족재고현황";
		$P->title = "안전/부족재고현황";
		$P->strContents = $Contents;



		$P->PrintLayOut();
	}
}

?>