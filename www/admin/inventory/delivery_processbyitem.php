<?
include_once("../class/layout.class");
include("../buyingservice/buying.lib.php");
include("../inventory/inventory.lib.php");
//print_r($_SERVER);

if ($vFromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate = $vToYY."/".$vToMM."/".$vToDD;
	$startDate = $vFromYY.$vFromMM.$vFromDD;
	$endDate = $vToYY.$vToMM.$vToDD;
}

$db1 = new Database;
$odb = new Database;
$ddb = new Database;
//$title_str = getOrderStatus($type);
if(!$title_str){
	$title_str  = "매출진행관리";
}

$vdate = date("Ymd", time());
$today = date("Ymd", time());
$firstday = date("Ymd", time()-84600*date("w"));
$lastday = date("Ymd", time()+84600*(6-date("w")));


if($mmode == "print"){
	$max = 10000; //페이지당 갯수
}else{
	if($max=="") $max = 20; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

// 검색 조건 설정 부분
//AND od.product_type NOT IN (".implode(',',$sns_product_type).")
$where = "WHERE od.status <> 'SR'  and od.pcode != '' and od.stock_use_yn = 'Y'  " ;

if ($oid != "")		$where .= "and od.oid = '$oid' ";
if ($bname != "")	$where .= "and bname = '$bname' ";
if ($rname != "")	$where .= "and rname = '$rname' ";
if ($rmobile != "")    $where .= "and rmobile = '$rmobile' ";
if ($bmobile != "")    $where .= "and bmobile = '$bmobile' ";
if($date_type){
	if ($vFromYY != "")	$where .= "and date_format(".$date_type.",'%Y%m%d') between $startDate and $endDate ";
}

if($search_type && $search_text){
	if($search_type == "combi_name"){
		$where .= "and (bname LIKE '%".trim($search_text)."%'  or rname LIKE '%".trim($search_text)."%' or bank_input_name LIKE '%".trim($search_text)."%') ";
	}else{
		$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
	}
}
//print_r($type);

if($delivery_status){
	$where .= " and od.delivery_status = '".$delivery_status."' ";
}

if(empty($type)){
	$type = $fix_type;
}

if(is_array($type)){
	for($i=0;$i < count($type);$i++){
		if($type[$i]){
			if($type_str == ""){
				$type_str .= "'".$type[$i]."'";
			}else{
				$type_str .= ", '".$type[$i]."' ";
			}
		}
	}

	if($type_str != ""){
		$where .= "and od.status in ($type_str) ";
	}
}else{
	if($type){
		$where .= "and od.status = '$type' ";
	}
}

if(is_array($order_from)){
	for($i=0;$i < count($order_from);$i++){
		if($order_from[$i] != ""){
			if($order_from_str == ""){
				$order_from_str .= "'".$order_from[$i]."'";
			}else{
				$order_from_str .= ",'".$order_from[$i]."' ";
			}
		}
	}

	if($order_from_str != ""){
		$where .= "and od.order_from in ($order_from_str) ";
	}
}else{
	if($order_from){
		$where .= "and od.order_from = '$order_from' ";
	}
}

if(is_array($method)){
	for($i=0;$i < count($method);$i++){
		if($method[$i] != ""){
			if($method_str == ""){
				$method_str .= "'".$method[$i]."'";
			}else{
				$method_str .= ", '".$method[$i]."' ";
			}
		}
	}

	if($method_str != ""){
		$where .= "and o.method in ($method_str) ";
	}
}else{
	if($method){
		$where .= "and o.method = '$method' ";
	}
}

if(is_array($payment_agent_type)){
	for($i=0;$i < count($payment_agent_type);$i++){
		if($payment_agent_type[$i] != ""){
			if($payment_agent_type_str == ""){
				$payment_agent_type_str .= "'".$payment_agent_type[$i]."'";
			}else{
				$payment_agent_type_str .= ", '".$payment_agent_type[$i]."' ";
			}
		}
	}

	if($payment_agent_type_str != ""){
		$where .= "and o.payment_agent_type in ($payment_agent_type_str) ";
	}
}else{
	if($payment_agent_type){
		$where .= "and o.payment_agent_type = '$payment_agent_type' ";
	}
}


if(is_array($mem_type)){
	for($i=0;$i < count($mem_type);$i++){
		if($mem_type[$i] != ""){
			if($mem_type_str == ""){
				$mem_type_str .= "'".$mem_type[$i]."'";
			}else{
				$mem_type_str .= ", '".$mem_type[$i]."' ";
			}
		}
	}

	if($mem_type_str != ""){
		$where .= "and o.mem_type in ($mem_type_str) ";
	}
}else{
	if($mem_type){
		$where .= "and o.mem_type = '$mem_type' ";
	}
}

/*
if($mem_type!="") {
	$where.=" AND o.mem_type='".$mem_type."' ";
}
*/

if(is_array($gp_ix)){
	for($i=0;$i < count($gp_ix);$i++){
		if($gp_ix_str == ""){
			$gp_ix_str .= "'".$gp_ix[$i]."'";
		}else{
			$gp_ix_str .= ", '".$gp_ix[$i]."' ";
		}
		/*
		if($gp_ix[$i] != ""){
			$sql="SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix='".$gp_ix[$i]."' ";
			$db1->query($sql);
			$db1->fetch();
			$gp_name=$db1->dt["gp_name"];

			if($gp_name_str == ""){
				$gp_name_str .= "'".$gp_name."'";
			}else{
				$gp_name_str .= ", '".$gp_name."' ";
			}
		}
		*/
	}

	if($gp_ix_str != ""){
		//$where .= "and o.mem_group in ($gp_name_str) ";
		//$detail_where .= "and o.mem_group in ($gp_name_str) ";

		$where .= "and o.gp_ix in ($gp_ix_str) ";
		$detail_where .= "and o.gp_ix in ($gp_ix_str) ";

	}
}else{
	/*
	$sql="SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix='".$gp_ix."' ";
	$db1->query($sql);
	$db1->fetch();
	$gp_name=$db1->dt["gp_name"];

	if($gp_ix){
		$where .= "and o.mem_group = '$gp_name' ";
		$detail_where .= "and o.mem_group = '$gp_name' ";
	}
	*/
	if($gp_ix){
		$where .= "and o.gp_ix = '".$gp_ix."'";
		$detail_where .= "and o.gp_ix = '".$gp_ix."' ";
	}

}


if(is_array($delivery_type)){
	for($i=0;$i < count($delivery_type);$i++){
		if($delivery_type[$i] != ""){
			if($delivery_type_str == ""){
				$delivery_type_str .= "'".$delivery_type[$i]."'";
			}else{
				$delivery_type_str .= ", '".$delivery_type[$i]."' ";
			}
		}
	}

	if($delivery_type_str != ""){
		$where .= "and od.delivery_type in ($delivery_type_str) ";
	}
}else{
	if($delivery_type){
		$where .= "and od.delivery_type = '$delivery_type' ";
	}
}
/*
if($delivery_type!="") {
	$where.=" AND delivery_type='".$delivery_type."' ";
}
*/

if($pcode != ""){
	$where .= " and od.pcode  = '".$pcode."'  ";
}

if($open_timezone != ""){
	$where .= " and sc.open_timezone  = '".$open_timezone."'  ";
}


if($orderby != "" && $ordertype != ""){
	$orderbyString = " order by $orderby $ordertype ";
}else{
	//$orderbyString = " order by diih.regdate desc ";
	//$orderbyString = " order by diih.regdate desc ";
}

	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$where .= " and o.oid = od.oid and  od.company_id = '".$company_id."'";
		}else{
			$where .= " and o.oid = od.oid ";
		}

		if($admininfo[mem_type] == "MD"){
			$where .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}
	}else if($admininfo[admin_level] == 8){
		$where .= " and o.oid = od.oid and od.company_id = '".$admininfo[company_id]."'";
	}


	$sql = "SELECT od.pcode, gu.unit, od.line, od.no, sum(od.pcnt) as pcnt_sum
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
					left join inventory_goods_unit gu on od.pcode = gu.gu_ix 
					right join inventory_goods g on g.gid = gu.gid
					$where 
					GROUP BY od.pcode, gu.unit  
					$orderbyString"; //, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
	//echo nl2br($sql);
	//exit;
	$db1->query($sql);


	$total = $db1->total;



if($mode == "excel"){

	$sql = "SELECT sum(od.coprice*od.pcnt) as total_buying_fee, count(od.pid) as total_pcnt
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join inventory_goods_unit gu on od.pcode = gu.gu_ix right join inventory_goods g on g.gid = gu.gid
					$where ";

	//echo nl2br($sql);
	$db1->query($sql);
	$db1->fetch();
	$total_buying_fee = $db1->dt[total_buying_fee];
	$total_pcnt = $db1->dt[total_pcnt];
	
	if($list_type == "item" || $list_type == ""){
	$sql = "select data.* , ps2.section_name from 
					(SELECT o.oid, od.pcode, gu.barcode, g.gid, gu.unit, g.gname, g.standard, od.company_name, od.com_phone, od.status, od.represent_name, od.represent_mobile, sum(od.pcnt) as pcnt_sum, count(od.pid) as group_cnt
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
					left join inventory_goods_unit gu on od.pcode = gu.gu_ix 
					left join inventory_goods g on gu.gid = g.gid
					$where
					GROUP BY od.pcode
					LIMIT $start, $max ) data 
					left join inventory_product_stockinfo ps on data.gid = ps.gid and data.unit = ps.unit
					left join inventory_place_section ps2 on ps.ps_ix = ps2.ps_ix and ps2.section_type not in ('D','S')
					GROUP BY data.pcode
					order by   ps2.section_name asc
				 ";
	}else{
	$sql = "SELECT o.oid, od.pcode, gu.barcode, g.gid, gu.unit, g.gname, g.standard, od.company_name, od.com_phone, od.status, od.represent_name, od.represent_mobile, sum(od.pcnt) as pcnt_sum,
				count(od.pid) as group_cnt
			FROM 
				".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
				left join inventory_goods_unit gu on od.pcode = gu.gu_ix 
				left join inventory_goods g on gu.gid = g.gid
			
			$where
				GROUP BY od.pcode
				$orderbyString
				LIMIT $start, $max ";
	}
	
	$db1->query($sql);
	$_order_info = $db1->fetchall();



include '../include/phpexcel/Classes/PHPExcel.php';
PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
	
	date_default_timezone_set('Asia/Seoul');
	
	$accounts_plan_priceXL = new PHPExcel();
	
	// 속성 정의
	$accounts_plan_priceXL->getProperties()->setCreator("CKCO& 어라운지")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle(iconv("UTF-8","CP949","품목종합 리스트"))
								 ->setSubject(iconv("UTF-8","CP949","리스트"))
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory(iconv("UTF-8","CP949","리스트"));
	$col = 'A';

	$start=1;

if($list_type == "item" || $list_type == ""){
	$accounts_plan_priceXL->getActiveSheet(0)->mergeCells('A'.($start+1).':A'.($start+2));
	$accounts_plan_priceXL->getActiveSheet(0)->mergeCells('B'.($start+1).':B'.($start+2));
	$accounts_plan_priceXL->getActiveSheet(0)->mergeCells('C'.($start+1).':C'.($start+2));
	$accounts_plan_priceXL->getActiveSheet(0)->mergeCells('D'.($start+1).':D'.($start+2));
	$accounts_plan_priceXL->getActiveSheet(0)->mergeCells('E'.($start+1).':E'.($start+2));
	$accounts_plan_priceXL->getActiveSheet(0)->mergeCells('F'.($start+1).':F'.($start+2));
	$accounts_plan_priceXL->getActiveSheet(0)->mergeCells('G'.($start+1).':G'.($start+2));
	$accounts_plan_priceXL->getActiveSheet(0)->mergeCells('H'.($start+1).':M'.($start+1));
	
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('A' . ($start+1), "품목코드");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('B' . ($start+1), "바코드");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('C' . ($start+1), "품목명");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('D' . ($start+1), "단위");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('E' . ($start+1), "규격(옵션)");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('F' . ($start+1), "주문수량");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('G' . ($start+1), "재고");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('H' . ($start+1), "사업장/보관장소");

	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('H' . ($start+2), "사업장");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('I' . ($start+2), "창고");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('J' . ($start+2), "보관장소");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('K' . ($start+2), "유통기간");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('L' . ($start+2), "현재고");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('M' . ($start+2), "창고이동수량");

}else{

	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('A' . ($start+1), "주문번호");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('B' . ($start+1), "주문자명");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('C' . ($start+1), "주문수량");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('D' . ($start+1), "품목코드");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('E' . ($start+1), "바코드");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('F' . ($start+1), "품목명");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('G' . ($start+1), "단위");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('H' . ($start+1), "규격(옵션)");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('I' . ($start+1), "주문수량");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('J' . ($start+1), "사업장");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('K' . ($start+1), "창고");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('L' . ($start+1), "보관장소");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('M' . ($start+1), "유통기간");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('M' . ($start+1), "현재고");

}

	$add_row = 0;
	for ($i = 0; $i < count($_order_info); $i++){
		//$_order_info = $db1->fetch($i);

		if($admininfo[admin_level] == 9){
			if($admininfo[mem_type] == "MD"){
				$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}
	
			if(is_array($type)){

				if($type_str != ""){
					$addWhere = "and od.status in ($type_str) ";
				}
			}else{
				if($type){
					$addWhere = "and od.status = '$type' ";
				}
			}

			if($delivery_status == ""){
					
			}else if($delivery_status != ""){
				 $delivery_status_str = " and od.delivery_status = '".$delivery_status."' ";
			}

				if($list_type == "item_member"){

					$sql = "SELECT o.oid, od.od_ix,od.pname, od.option_text, od.regdate, od.psprice, od.coprice, sum(od.pcnt) as pcnt, od.ptprice,od.commission, user_code,
							o.delivery_price as pay_delivery_price,od.delivery_type, com_name,od.pid,  bname, mem_group,
							 od.status, od.delivery_status,  total_price, UNIX_TIMESTAMP(order_date) AS date, od.company_name, od.com_phone, od.represent_mobile, od.company_id, od.quick, od.invoice_no,
							gu.gid, gu.unit,  g.gname, g.pi_ix, g.ps_ix, gu.gu_ix
							FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, 
							inventory_goods_unit gu 
							right join inventory_goods g on g.gid = gu.gid  and gu.gu_ix = '".$_order_info[$i][pcode]."' 					
							where o.oid = od.oid and od.pcode = gu.gu_ix 
							and od.pcode = '".$_order_info[$i][pcode]."' 
							and od.stock_use_yn = 'Y'  ".$delivery_status_str."
							$addWhere $detail_where 						
							group by od.pcode , od.oid
							ORDER BY company_id DESC, od.status DESC"; 
				
				}else{

					$sql = "SELECT od.od_ix, 	gu.gid, gu.gu_ix, gu.unit,  g.gname, pi.place_name, ps.section_name,ps.section_type, (case when ps.section_type = 'D' and ips.stock < 0 then 0 else ips.stock end) as stock,			ips.pi_ix, ips.ps_ix, 
							'".$_order_info[$i][status]."' as status, ips.company_id,'".$_order_info[$i][oid]."' as oid, ips.company_id,
							(select com_name as company_name from common_company_detail ccd where ccd.company_id = ips.company_id   limit 1) as ips_company_name
							FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, 
							inventory_goods_unit gu 
							right join inventory_goods g on g.gid = gu.gid  and gu.gu_ix = '".$_order_info[$i][pcode]."' 
							left join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit  
							left join  inventory_place_info pi on pi.pi_ix = ips.pi_ix
							left join  inventory_place_section ps on ps.pi_ix = ips.pi_ix and  ps.ps_ix = ips.ps_ix 
							where o.oid = od.oid and  gu.gu_ix = '".$_order_info[$i][pcode]."' 		
							and ((ps.section_type != 'D' ) or (ps.section_type = 'D' ))
							$delivery_status_str
							group by gu.gu_ix, ips.pi_ix, ips.ps_ix
							order by ips.exit_order, pi.exit_order, ps.exit_order 
							 "; 
				}

		}else if($admininfo[admin_level] == 8){

			if(is_array($type)){

				if($type_str != ""){
					$addWhere = "and od.status in ($type_str) ";
				}
			}else{
				if($type){
					$addWhere = "and od.status = '$type' ";
				}
			} 

			$sql = "SELECT o.oid, od.od_ix,od.pname, od.option_text, od.regdate, od.psprice, od.coprice, od.pcnt, od.ptprice,od.commission, user_code,company_id,
						o.delivery_price as pay_delivery_price,od.delivery_type, com_name,od.pid,  bname, mem_group,
						 od.status, od.delivery_status,  total_price, UNIX_TIMESTAMP(order_date) AS date, od.company_name, od.com_phone, od.represent_mobile, od.company_id,od.quick, od.invoice_no,
						gu.gid, gu.unit,  g.gname, pi.place_name, ps.section_name, ips.stock, ips.pi_ix, ips.ps_ix, 
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							
							group by company_id  limit 1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type,
						(select com_name as company_name from common_company_detail ccd where ccd.company_id = ips.company_id   limit 1) as ips_company_name
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, 
						inventory_goods_unit gu 
						right join inventory_goods g on g.gid = gu.gid  and gu.gu_ix = '".$_order_info[$i][pcode]."' 
						left join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
						left join  inventory_place_info pi on pi.pi_ix = ips.pi_ix
						left join  inventory_place_section ps on ps.pi_ix = ips.pi_ix and  ps.ps_ix = ips.ps_ix
						where o.oid = od.oid and od.pcode = gu.gu_ix and od.pcode = '".$_order_info[$i][pcode]."'  and od.company_id ='".$admininfo[company_id]."' 
						$addWhere $detail_where 
						ORDER BY ips.company_id DESC, od.status DESC
						 "; 
		}

		$ddb->query($sql);
		$order_detail_infos = $ddb->fetchall();

		$od_count = $ddb->total;

if($list_type == "item" || $list_type == ""){

		$accounts_plan_priceXL->getActiveSheet()->mergeCells('A' . ($i + $start + 3 + $add_row).':A'.($i + $start + 3 + $od_count - 1 + $add_row));
		$accounts_plan_priceXL->getActiveSheet()->mergeCells('B' . ($i + $start + 3 + $add_row).':B'.($i + $start + 3 + $od_count - 1 + $add_row));
		$accounts_plan_priceXL->getActiveSheet()->mergeCells('C' . ($i + $start + 3 + $add_row).':C'.($i + $start + 3 + $od_count - 1 + $add_row));
		$accounts_plan_priceXL->getActiveSheet()->mergeCells('D' . ($i + $start + 3 + $add_row).':D'.($i + $start + 3 + $od_count - 1 + $add_row));
		$accounts_plan_priceXL->getActiveSheet()->mergeCells('E' . ($i + $start + 3 + $add_row).':E'.($i + $start + 3 + $od_count - 1 + $add_row));
		$accounts_plan_priceXL->getActiveSheet()->mergeCells('F' . ($i + $start + 3 + $add_row).':F'.($i + $start + 3 + $od_count - 1 + $add_row));
		$accounts_plan_priceXL->getActiveSheet()->mergeCells('G' . ($i + $start + 3 + $add_row).':G'.($i + $start + 3 + $od_count - 1 + $add_row));
		

		$accounts_plan_priceXL->getActiveSheet()->setCellValue('A' . ($i + $start + 3 + $add_row), $_order_info[$i][gid]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('B' . ($i + $start + 3 + $add_row), " ".$_order_info[$i][barcode]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('C' . ($i + $start + 3 + $add_row), $_order_info[$i][gname]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('D' . ($i + $start + 3 + $add_row), getUnit($_order_info[$i][unit], "basic_unit","","text"));
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('E' . ($i + $start + 3 + $add_row), $_order_info[$i][standard]);
		$this_pcnt_sum = $_order_info[$i][pcnt_sum];

		for($j=0;$j < count($order_detail_infos);$j++){

			if($list_type == "" || $list_type == "item"){
				if($b_pcode != $_order_info[$i][pcode] && $b_pcode != ""){			
					$Contents = str_replace("{stock_sum}",number_format($pcode_stock_sum)." ", $Contents);
					unset($pcode_stock_sum);
					$pcode_stock_sum += $order_detail_infos[$j][stock];
				}else{
					$pcode_stock_sum += $order_detail_infos[$j][stock];
				}
			}

			$accounts_plan_priceXL->getActiveSheet()->setCellValue('H' . ($i + $start + 3 + $j + $add_row), $order_detail_infos[$j][ips_company_name]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('I' . ($i + $start + 3 + $j + $add_row), $order_detail_infos[$j][place_name]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('J' . ($i + $start + 3 + $j + $add_row), $order_detail_infos[$j][section_name]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('K' . ($i + $start + 3 + $j + $add_row), $order_detail_infos[$j][expiry_date]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('L' . ($i + $start + 3 + $j + $add_row), $order_detail_infos[$j][stock]);

			if($this_pcnt_sum < $order_detail_infos[$j][stock]){

				if($order_detail_infos[$j][section_type]=='D' || $this_pcnt_sum <= 0){

					$accounts_plan_priceXL->getActiveSheet()->setCellValue('M' . ($i + $start + 3 + $j + $add_row), $this_pcnt_sum);
				}else{

					$accounts_plan_priceXL->getActiveSheet()->setCellValue('M' . ($i + $start + 3 + $j + $add_row), $this_pcnt_sum);
				}

				$this_pcnt_sum = 0;

			}else{

				if($order_detail_infos[$j][section_type]=='D' || $order_detail_infos[$j][stock] <= 0){

					$accounts_plan_priceXL->getActiveSheet()->setCellValue('M' . ($i + $start + 3 + $j + $add_row), ($order_detail_infos[$j][stock] < 0 ? '0':$order_detail_infos[$j][stock]));
				}else{

					$accounts_plan_priceXL->getActiveSheet()->setCellValue('M' . ($i + $start + 3 + $j + $add_row), $order_detail_infos[$j][stock]);

				}
				if($order_detail_infos[$j][stock] > 0){

					$this_pcnt_sum = $this_pcnt_sum - $order_detail_infos[$j][stock];

				}
			}
		}
		

		$accounts_plan_priceXL->getActiveSheet()->setCellValue('F' . ($i + $start + 3 + $add_row), $_order_info[$i][pcnt_sum]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('G' . ($i + $start + 3 + $add_row), $pcode_stock_sum);
	
		if($od_count > 1){
			$add_row += $od_count - 1;
		}

		unset($pcode_stock_sum);

}else{
	for($j=0;$j < count($order_detail_infos);$j++){

		$accounts_plan_priceXL->getActiveSheet()->setCellValue('A' . ($i + $start + 2), $_order_info[$i][oid]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('B' . ($i + $start + 2), $order_detail_infos[$j][bname]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('C' . ($i + $start + 2), $_order_info[$i][pcnt_sum]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('D' . ($i + $start + 2), $_order_info[$i][gid]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('E' . ($i + $start + 2), " ".$_order_info[$i][barcode]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('F' . ($i + $start + 2), $_order_info[$i][gname]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('G' . ($i + $start + 2), getUnit($_order_info[$i][unit], "basic_unit","","text"));
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('H' . ($i + $start + 2), $_order_info[$i][standard]);
		$accounts_plan_priceXL->getActiveSheet()->setCellValue('I' . ($i + $start + 2), $_order_info[$i][pcnt_sum]);

		$sql = "select g.cid,g.gname, g.gcode, g.admin, (gu.buying_price*ips.stock) as buying_price, gu.sellprice , g.item_account , g.basic_unit, g.ci_ix, g.pi_ix, 
					pi.place_name, ps.ps_ix, ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit, g.gid, gu.safestock, gu.sell_ing_cnt, ips.expiry_date ,
					 (select com_name as company_name from common_company_detail ccd where ccd.company_id = pi.company_id limit 1) as ips_company_name
					from inventory_goods g 
					left join inventory_goods_unit gu on g.gid =gu.gid 
					left join inventory_place_info pi on pi.pi_ix = g.pi_ix and pi.pi_ix = '".$order_detail_infos[$j][pi_ix]."'
					left join inventory_place_section ps on pi.pi_ix = ps.pi_ix and  ps.pi_ix = g.pi_ix and ps.section_type = 'D'
					left join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit and ips.pi_ix = ps.pi_ix and ips.ps_ix = ps.ps_ix
					where gu.gu_ix = '".$order_detail_infos[$j][gu_ix]."' 
					";

		$ddb->query($sql);
		$ddb->fetch();
		$warehouse_info = $ddb->dt;
				
		if($b_pcode != $_order_info[$i][pcode] ){
			if(!($order_detail_infos[$j][pi_ix] && $order_detail_infos[$j][ps_ix])){
				$accounts_plan_priceXL->getActiveSheet()->setCellValue('J' . ($i + $start + 2), '품목의 기본창고가 지정되야 합니다.');
			}else{
				$accounts_plan_priceXL->getActiveSheet()->setCellValue('J' . ($i + $start + 2), $warehouse_info[ips_company_name]);
				$accounts_plan_priceXL->getActiveSheet()->setCellValue('K' . ($i + $start + 2), $warehouse_info[place_name]);
				$accounts_plan_priceXL->getActiveSheet()->setCellValue('L' . ($i + $start + 2), $warehouse_info[section_name]);
				$accounts_plan_priceXL->getActiveSheet()->setCellValue('M' . ($i + $start + 2), $warehouse_info[stock]);
			}
		}
	}
}

	}
	//exit;
	
	$Contents = str_replace("{stock_sum}",number_format($pcode_stock_sum)." ", $Contents);

	// 첫번째 시트 선택
	$accounts_plan_priceXL->setActiveSheetIndex(0);

	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('A')->setWidth(13);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	//$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('J')->setWidth(15);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('K')->setWidth(10);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('L')->setWidth(10);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('M')->setWidth(15);


	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="salesbyproduct.xls"');
	header('Cache-Control: max-age=0');

	$styleArray = array(
		'borders' => array(
			'allborders' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);

if($list_type == "item" || $list_type == ""){

	$accounts_plan_priceXL->getActiveSheet()->getStyle('A'.($start+1).':M'.($i+$start+2 + $add_row))->applyFromArray($styleArray);
	$accounts_plan_priceXL->getActiveSheet()->getStyle('A'.$start.':M'.($i+$start+2 ))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$accounts_plan_priceXL->getActiveSheet()->getStyle('A'.($start).':M'.($start+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true);
	$accounts_plan_priceXL->getActiveSheet()->getStyle('A'.$start.':M'.($i+$start+2))->getFont()->setSize(10)->setName('돋움');
	$accounts_plan_priceXL->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	unset($styleArray);

}else{

	$accounts_plan_priceXL->getActiveSheet()->getStyle('A'.($start+1).':M'.($i+$start+1))->applyFromArray($styleArray);
	$accounts_plan_priceXL->getActiveSheet()->getStyle('A'.($start).':M'.($start+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setWrapText(true);
	$accounts_plan_priceXL->getActiveSheet()->getStyle('A'.$start.':M'.($i+$start+2))->getFont()->setSize(10)->setName('돋움');
	$accounts_plan_priceXL->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

}
	
	$objWriter = PHPExcel_IOFactory::createWriter($accounts_plan_priceXL, 'Excel5');
	$objWriter->save('php://output');
	exit;

}else{

	$sql = "SELECT sum(od.coprice*od.pcnt) as total_buying_fee, count(od.pid) as total_pcnt
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join inventory_goods_unit gu on od.pcode = gu.gu_ix right join inventory_goods g on g.gid = gu.gid
					$where ";

	//echo nl2br($sql);
	$db1->query($sql);
	$db1->fetch();
	$total_buying_fee = $db1->dt[total_buying_fee];
	$total_pcnt = $db1->dt[total_pcnt];
	
	if($list_type == "item" || $list_type == ""){
	$sql = "select data.* , ps2.section_name from 
					(SELECT o.oid, od.pcode, gu.barcode, g.gid, gu.unit, g.gname, g.standard, od.company_name, od.com_phone, od.status, od.represent_name, od.represent_mobile, sum(od.pcnt) as pcnt_sum, count(od.pid) as group_cnt
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
					left join inventory_goods_unit gu on od.pcode = gu.gu_ix 
					left join inventory_goods g on gu.gid = g.gid
					$where
					GROUP BY od.pcode
					LIMIT $start, $max ) data 
					left join inventory_product_stockinfo ps on data.gid = ps.gid and data.unit = ps.unit
					left join inventory_place_section ps2 on ps.ps_ix = ps2.ps_ix and ps2.section_type not in ('D','S')
					GROUP BY data.pcode
					order by   ps2.section_name asc
				 ";
	}else{
	$sql = "SELECT o.oid, od.pcode, od.order_from, gu.barcode, g.gid, gu.unit, g.gname, g.standard, od.company_name, od.com_phone, od.status, od.represent_name, od.represent_mobile, sum(od.pcnt) as pcnt_sum, count(od.pid) as group_cnt
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od 
					left join inventory_goods_unit gu on od.pcode = gu.gu_ix 
					left join inventory_goods g on gu.gid = g.gid
					$where
					GROUP BY od.pcode
					$orderbyString
					LIMIT $start, $max ";
	}
	

	//echo nl2br($sql);
	$db1->query($sql);
	$_order_info = $db1->fetchall();
	//print_r($_order_info);
}


if($mmode == "print" || $mmode == "order_sms"){
	//include("./ordersbysc.read.php");
	//exit;
}

if(($list_type == "" || $list_type == "item")){
	$print_title = "출고지시서(품목 종합리스트)";
}else if($list_type == "item_member"){
	$print_title = "출고지시서(품목/회원별 리스트)";
}else if($list_type == "order"){
	$print_title = "출고지시서(주문 리스트)";
}

$Contents = "

<div id='print_area'>
<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("$title_str", "배송관리 > $title_str ")."</td>
	</tr>";
if($mmode != "print"){
	if($pre_type == ORDER_STATUS_DELIVERY_READY ){
$Contents .= "
	<tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'>
			<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							";
		if($delivery_status != "WDR"){
							$Contents .= "
							<table id='tab_02'  ".(($list_type == "" || $list_type == "item") ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?list_type=item'\">품목종합 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>";
		}
$Contents .= "
							<table id='tab_03' ".($list_type == "item_member" ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?list_type=item_member'\">품목/회원별 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_02' ".($list_type == "order" ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?list_type=order'\">주문별 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
							

						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>";

	}else if($pre_type == ORDER_STATUS_WAREHOUSING_STANDYBY || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
$Contents .= "
	<tr>
		<td align='left' colspan=4 style='padding-bottom:20px;'>
			<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02 blk' onclick=\"document.location.href='buying_ing.php?'\">주문별 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_02' ".($list_type == "sc" || $list_type == "" ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02 blk' onclick=\"document.location.href='?list_type=sc'\">품목별 주문 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_03' ".($list_type == "scc" ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02 blk' onclick=\"document.location.href='?list_type=scc&gp_ix%5B%5D=8&gp_ix%5B%5D=7&gp_ix%5B%5D=4&gp_ix%5B%5D=3&gp_ix%5B%5D=2&gp_ix%5B%5D=1'\">사업자 주문리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_04' ".($list_type == "vb" ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02 blk' onclick=\"document.location.href='?list_type=vb&gp_ix%5B%5D=9&gp_ix%5B%5D=6&gp_ix%5B%5D=5'\">VB회원 주문 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>";
	}

$Contents .= "
	<!--tr>
		<td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> $title_str </b></div>")."</td>
	</tr-->
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td colspan=2>
			".OrderSummary($pre_type, $title_str)."
		</td>
	</tr>
	<tr>";

$Contents .= "
		<td style='width:75%;' colspan=2 valign=top>
		<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
					<input type='hidden' name='list_type' value='$list_type'>
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
										
										<td class='input_box_title'>사업장/창고</td>
										<td class='input_box_item' >
											".SelectEstablishment($company_id,"company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
											".SelectInventoryInfo($company_id, $pi_ix,'pi_ix','select','false', "onChange=\"loadPlaceSection(this,'ps_ix')\" ")."
											".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","false" )." 
										</td>
										<td class='input_box_title'>품목계정</td>
										<td class='input_box_item' >
											".getItemAccount($item_account)."
										</td>
									</tr>
									<!--tr>
										<td class='input_box_title'>주거래처</td>
										<td class='input_box_item' >
											".SelectSupplyCompany($ci_ix,'ci_ix','select','false')."
										</td-->
										<!--td class='input_box_title'>품목사용여부</td>
										<td class='input_box_item'>
											<input type=radio name=disp class=nonborder value='' id='disp_' validation=false title='사용유무' ".($disp == "" ? "checked":"")."><label for='disp_'>전체</label>
											<input type=radio name=disp class=nonborder value=1 id='disp_1' validation=false title='사용유무' ".($disp == "1" ? "checked":"")."><label for='disp_1'>사용</label>
											<input type=radio name=disp class=nonborder value=0 id='disp_0' validation=false title='사용유무' ".($disp == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
										</td>
									</tr-->
									<tr>
										<td class='input_box_title'>  <b>검색어</b>  </td>
										<td class='input_box_item' valign='top' style='padding-right:5px;padding-top:7px;' >
											<table cellpadding=0 cellspacing=0>
												<tr>
													<td><select name='search_type'  style=\"font-size:12px;height:22px;min-width:140px;\">
																	<option value='g.gid' ".CompareReturnValue("g.gid",$search_type).">품목코드</option>
																	<!--option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type).">대표코드</option-->
																	<option value='g.gname' ".CompareReturnValue("g.gname",$search_type).">품목명</option>
																</select>
																</td>
													<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox' value='".$search_text."' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 200px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
													<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
														<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef>
															<tr height=20>
																<td width=100%  style='padding:0 0 0 5'>
																	<table width=100% cellpadding=0 cellspacing=0 border=0>
																		<tr>
																			<td class='p11 ls1'>검색어 자동완성</td>
																			<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:hand;padding:0 10 0 0' align=right>닫기</td>
																		</tr>
																	</table>
																</td>
															</tr>
															<tr height=100% >
																<td valign=top bgColor=#efefef style='padding:0 6 5 6' colspan=2>
																	<table width=100% height=100% bgcolor=#ffffff>
																		<tr>
																			<td valign=top >
																			<div style='POSITION: absolute; overflow-y:auto;HEIGHT: 120px;' id='search_data_area'>
																				<TABLE id=search_table style='table-layout:fixed;'  width=100% cellSpacing=0 cellPadding=1 bgColor=#ffffff border=0>
																				<TBODY id=search_table_body></TBODY>
																				</TABLE>
																			<div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
														</DIV>
													</td>
													<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
												</tr>
											</table>
										</td>
										<td class='input_box_title'><b>목록갯수</b></td>
										<td class='input_box_item'>
											<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
												<option value='5' ".CompareReturnValue(5,$max).">5</option>
												<option value='10' ".CompareReturnValue(10,$max).">10</option>
												<option value='20' ".CompareReturnValue(20,$max).">20</option>
												<option value='50' ".CompareReturnValue(50,$max).">50</option>
												<option value='100' ".CompareReturnValue(100,$max).">100</option>
											</select> <span class='small'>한페이지에 보여질 갯수를 선택해주세요</span>
										</td>
									</tr>";
									if($list_type=="item_member"){
									$Contents .= "
									<tr>
										<th class='search_box_title'>판매처 선택 </th>
										<td class='search_box_item' nowrap colspan='3'>
											<table cellpadding=0 cellspacing=0 width='100%' border='0' >
												<col width='15%'>
												<col width='15%'>
												<col width='15%'>
												<col width='15%'>
												<col width='15%'>
												<col width='15%'>
												<TR height=25>";

										$Contents .= "
													<TD><input type='checkbox' name='order_from[]' id='order_from_self' value='self' ".CompareReturnValue('self',$order_from,' checked')." ><label for='order_from_self'>자체쇼핑몰</label></TD>
													<TD><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." ><label for='order_from_offline'>오프라인 영업</label></TD>
													<TD><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." ><label for='order_from_pos'>POS</label></TD>";
													$db->query("select * from sellertool_site_info where disp='1' ");
													$sell_order_from=$db->fetchall();
													if(count($sell_order_from) > 0){

														for($i=0;$i<count($sell_order_from);$i++){
																$Contents .= "<TD><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label></TD>";
														}
													}else{
													$Contents .= "
													<TD></TD>
													<TD></TD>
													<TD></TD>";
													}
									
								$Contents .= "
												</TR>
											</table>
										</td>
									</tr>";
								}
								$Contents .= "
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
	</tr>";
}else{
$Contents .= "
	<tr>
		<td>
			<table cellpadding='0' cellspacing='0' border='0' class='print_area'  style='display:none;' width='100%'>
					<tr>
						<td height='50' align='center'>
							<b class='print_area_big_font' >".$print_title." </b>
						</td>
					</tr> 
					<tr>
						<td height='30' align='right'>
							".date("Y 년 m 월 d 일")."
						</td>
					</tr> 
				</table>
		</td>
	</tr>";
}
$Contents .= "
</table>

<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
 <tr>";

//exit;
$Contents .= "<td colspan=3 align=left>";
if($mmode != "print"){
	//$Contents .= "<!--b>전체 주문수 : $total 건</b--> 총 품목수 : <b>".number_format($total)."</b>  , 총 사입금액 : <b>".number_format($total_buying_fee)."</b> ";
}
$Contents .= "
				</td>
				<td colspan=5 align=right> ";
if($mmode != "print"){
	$Contents .= "			
				<!--a href='?mmode=print&".$QUERY_STRING."&pre_type=".$pre_type."' --><a href=\"javascript:PopSWindow('?mmode=print&".$QUERY_STRING."&pre_type=".$pre_type."',1150,700,'member_info')\" ><img src='../images/".$admininfo["language"]."/btn_print.gif'  border=0 style='cursor:pointer;' /></a>
				";
	if($pre_type == ORDER_STATUS_DELIVERY_READY){
		//$Contents .= "<a href='excel_config.php?info_type=delivery_item' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif' ></a>";
	}

	if($admininfo[admin_level] == 9){
		if($pre_type == ORDER_STATUS_DELIVERY_READY || $pre_type == ORDER_STATUS_WAREHOUSING_STANDYBY || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE ){
			$Contents .= " <a href='?mode=excel&".str_replace("mode=search&","",$QUERY_STRING)."&pre_type=".$pre_type."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
		}
	}else if($admininfo[admin_level] == 8){
		if($pre_type == ORDER_STATUS_DELIVERY_READY){
			$Contents .= "<span style='color:red'><!--! 주의 : 입금예정 처리상태일 경우, 상품배송을 하지 마시기 바랍니다. 판매된 상품으로 처리 불가능-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
			$Contents .= "<a href='?mode=excel&".str_replace("mode=search&","",$QUERY_STRING)."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
		}
	$Contents .= "<a href='?mode=excel&".str_replace("mode=search&","",$QUERY_STRING)."'><img src='../image/btn_delivery_excel_save.gif' border=0 align=absmiddle></a>";
	}
}
$Contents .= "
  </td>
  </tr>
  </table>
  <!--form name='list_frm'   method=post -->
  <form name=listform method=post onsubmit='return CheckStatusUpdate(this)' action='../order/orders.goods_list.act.php' target='act' ><!--target='act'-->
<input type='hidden' name='act' value='delivery_update'>
<input type='hidden' name='pre_type' value='$pre_type'>
<input type='hidden' name='list_type' value='item_member'>
<input type='hidden' id='oid' value=''>
<input type='hidden' id='od_ix' value=''>

  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='25' >";
	
	$Contents .= "";
	
	
if($list_type == "item_member"){
	if($mmode != "print"){
	$Contents .= "<td class='s_td' width='5%' rowspan=2><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)' checked></td>";	
	}

	$Contents .= "<td width=25% align='center' class=m_td colspan=3>주문정보</td>";
	if($pre_type == ORDER_STATUS_DELIVERY_READY){
		if($mmode != "print"){
		$Contents .= "
		<td width='8%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>관리</b></font></td>";
		}
	}else if($pre_type == ORDER_STATUS_DELIVERY_ING || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
		$Contents .= "
			<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>처리상태</b></font></td>
			<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>택배사</b></font></td>
			<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>송장번호/위치추적</b></font></td>
			<td width='10%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>관리</b></font></td>";
	}else{
		$Contents .= "
			<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>처리상태</b></font></td>
			<td width='10%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>관리</b></font></td>";
	}

	$Contents .= "
		<td width='6%' align='center' class='m_td' rowspan=2><font color='#000000'><b>".OrderByLink("품목코드", "g.gname", $ordertype)."</b></font></td>
		<td width='7%' align='center' class='m_td' rowspan=2><font color='#000000'><b>".OrderByLink("바코드", "gu.barcode", $ordertype)."</b></font></td>
		<td width='15%' align='center' class='m_td' rowspan=2><font color='#000000'><b>".OrderByLink("품목명", "gcode", $ordertype)."</b></font></td>
		<td width='5%' align='center' class='m_td' rowspan=2><font color='#000000'><b>단위</b></font></td>
		<td width='7%' align='center' class='m_td' rowspan=2><font color='#000000'><b>규격(옵션)</b></font></td>
		<td width='5%' align='center' class='m_td' rowspan=2><font color='#000000'><b>주문수량</b></font></td>
		<td width=25% align='center' class=m_td colspan=5>출고창고</td>";

}else{
	if($mmode != "print"){
	$Contents .= "<td class='s_td' width='5%' rowspan=2><input type=checkbox  name='all_fix' onclick='fixAll3(document.listform)' checked></td>";
	}

	$Contents .= "
		<td width='6%' align='center' class='m_td' rowspan=2><font color='#000000'><b>".OrderByLink("품목코드", "g.gname", $ordertype)."</b></font></td>
		<td width='7%' align='center' class='m_td' rowspan=2><font color='#000000'><b>".OrderByLink("바코드", "gu.barcode", $ordertype)."</b></font></td>
		<td width='15%' align='center' class='m_td' rowspan=2><font color='#000000'><b>".OrderByLink("품목명", "gcode", $ordertype)."</b></font></td>
		<td width='5%' align='center' class='m_td' rowspan=2><font color='#000000'><b>단위</b></font></td>
		<td width='7%' align='center' class='m_td' rowspan=2><font color='#000000'><b>규격(옵션)</b></font></td>
		<td width='5%' align='center' class='m_td' rowspan=2><font color='#000000'><b>주문수량</b></font></td>";

	if($list_type != "item_member"){
		$Contents .= "<td width='5%' align='center' class='m_td' rowspan=2><font color='#000000'><b>재고</b></font></td>";
	}
	$Contents .= "<td width=45% align='center' class=m_td ".($mmode == "print" ? "colspan=7":"colspan=6").">사업장/보관장소</td>";

	if($pre_type == ORDER_STATUS_DELIVERY_READY){
		if($mmode != "print"){
		$Contents .= "
		<td width='8%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>관리</b></font></td>";
		}
	}else if($pre_type == ORDER_STATUS_DELIVERY_ING || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
		$Contents .= "
			<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>처리상태</b></font></td>
			<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>택배사</b></font></td>
			<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>송장번호/위치추적</b></font></td>
			<td width='10%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>관리</b></font></td>";
	}else{
		$Contents .= "
			<td width='7%' align='center' class='m_td' rowspan=2 nowrap><font color='#000000' ><b>처리상태</b></font></td>
			<td width='10%' align='center' class='e_td' rowspan=2 nowrap><font color='#000000' ><b>관리</b></font></td>";
	}

	

} 
	

	
$Contents .= "
	</tr>";
if($list_type == "item_member"){
$Contents .= "
	<tr align=center height=30>
		<td class=m_td width='100px'>주문번호/판매처</td>
		<td class=m_td width='120px'>주문자명<br />받는사람명</td>
		<td class=m_td width='60px'>주문수량</td>	
		<td class=m_td width='80px'>사업장</td>
		<td class=m_td width='90px'>창고</td>
		<td class=m_td width='110px'>보관장소</td>	
		<td class=m_td width='80px' nowrap>유통기간</td>	
		<td class=m_td width='80px' nowrap>현재고</td>	
		
		
	</tr>
  ";
}else{
$Contents .= "
	<tr align=center height=30>
		<td class=m_td width='80px'>사업장</td>
		<td class=m_td width='90px'>창고</td>
		<td class=m_td width='110px'>보관장소</td>	
		<td class=m_td width='80px' >유통기간</td>	
		<td class=m_td width='80px'>현재고</td>
		<td class=m_td width='80px'>".($mmode == "print" ? "권장<br>창고이동수량":"창고이동수량")."</td>	";
		if($mmode == "print"){
		$Contents .= "<td class=m_td width='80px'>실<br>창고이동수량</td>";
		}
$Contents .= "
	</tr>
  ";
}


if($db1->total){
	for ($i = 0; $i < count($_order_info); $i++)
	{
		$db1->fetch($i);

		if($admininfo[admin_level] == 9){
			if($admininfo[mem_type] == "MD"){
				$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}
	
			if(is_array($type)){

				if($type_str != ""){
					$addWhere = "and od.status in ($type_str) ";
				}
			}else{
				if($type){
					$addWhere = "and od.status = '$type' ";
				}
			}

			if($delivery_status == ""){
					
			}else if($delivery_status != ""){
				 $delivery_status_str = " and od.delivery_status = '".$delivery_status."' ";
			}

			if($list_type == "item_member"){

				
				
			$sql = "SELECT o.oid, od.od_ix,od.pname, od.option_text, od.regdate, od.psprice, od.coprice, sum(od.pcnt) as pcnt, od.ptprice,od.commission, user_code,
						o.delivery_price as pay_delivery_price,od.delivery_type, com_name,od.pid,  bname, mem_group,
						 od.status, od.delivery_status,  total_price, UNIX_TIMESTAMP(order_date) AS date, od.company_name, od.com_phone, od.represent_mobile, od.company_id, od.quick, od.invoice_no,od.order_from,
						gu.gid, gu.unit,  g.gname, g.pi_ix, g.ps_ix, gu.gu_ix
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, 
						inventory_goods_unit gu 
						right join inventory_goods g on g.gid = gu.gid  and gu.gu_ix = '".$_order_info[$i][pcode]."' 					
						where o.oid = od.oid and od.pcode = gu.gu_ix 
						and od.pcode = '".$_order_info[$i][pcode]."' 
						and od.stock_use_yn = 'Y'  ".$delivery_status_str."
						$addWhere $detail_where 						
						group by od.pcode , od.oid
						ORDER BY company_id DESC, od.status DESC"; 
						//and od.product_type NOT IN (".implode(',',$sns_product_type).") 
				
				}else{

				$sql = "SELECT od.od_ix, 	gu.gid, gu.gu_ix, gu.unit,  g.gname, pi.place_name, ps.section_name,ps.section_type, (case when ps.section_type = 'D' and ips.stock < 0 then 0 else ips.stock end) as stock, ips.pi_ix, ips.ps_ix, 
						'".$_order_info[$i][status]."' as status, ips.company_id,'".$_order_info[$i][oid]."' as oid, ips.company_id,
						(select com_name as company_name from common_company_detail ccd where ccd.company_id = ips.company_id   limit 1) as ips_company_name
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, 
						inventory_goods_unit gu 
						right join inventory_goods g on g.gid = gu.gid  and gu.gu_ix = '".$_order_info[$i][pcode]."' 
						left join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit  
						left join  inventory_place_info pi on pi.pi_ix = ips.pi_ix
						left join  inventory_place_section ps on ps.pi_ix = ips.pi_ix and  ps.ps_ix = ips.ps_ix 
						where o.oid = od.oid and  gu.gu_ix = '".$_order_info[$i][pcode]."' 		
						and ((ps.section_type != 'D' ) or (ps.section_type = 'D' ))
						$delivery_status_str
						group by gu.gu_ix, ips.pi_ix, ips.ps_ix
						order by ips.exit_order, pi.exit_order, ps.exit_order 
						 "; 
				}
						//ORDER BY company_id DESC group by od.pcode, ips.pi_ix, ips.ps_ix
			
		if($_order_info[$i][oid] == "201009151408-7669"){
			//echo $sql;
		}
		//echo nl2br($sql);

		/*서브쿼리 삭제 아무것도 없을때 에러남 서브쿼리 부분에서 */
		//,			(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,
		//				(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id) as company_total

		}else if($admininfo[admin_level] == 8){
			if(is_array($type)){


				if($type_str != ""){
					$addWhere = "and od.status in ($type_str) ";
				}
			}else{
				if($type){
					$addWhere = "and od.status = '$type' ";
				}
			} 

			$sql = "SELECT o.oid, od.od_ix,od.pname, od.option_text, od.regdate, od.psprice, od.coprice, od.pcnt, od.ptprice,od.commission, user_code,company_id,
						o.delivery_price as pay_delivery_price,od.delivery_type, com_name,od.pid,  bname, mem_group,
						 od.status, od.delivery_status,  total_price, UNIX_TIMESTAMP(order_date) AS date, od.company_name, od.com_phone, od.represent_mobile, od.company_id,od.quick, od.invoice_no,od.order_from,
						gu.gid, gu.unit,  g.gname, pi.place_name, ps.section_name, ips.stock, ips.pi_ix, ips.ps_ix, 
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							
							group by company_id  limit 1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type,
						(select com_name as company_name from common_company_detail ccd where ccd.company_id = ips.company_id   limit 1) as ips_company_name
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od, 
						inventory_goods_unit gu 
						right join inventory_goods g on g.gid = gu.gid  and gu.gu_ix = '".$_order_info[$i][pcode]."' 
						left join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
						left join  inventory_place_info pi on pi.pi_ix = ips.pi_ix
						left join  inventory_place_section ps on ps.pi_ix = ips.pi_ix and  ps.ps_ix = ips.ps_ix
						where o.oid = od.oid and od.pcode = gu.gu_ix and od.pcode = '".$_order_info[$i][pcode]."'  and od.company_id ='".$admininfo[company_id]."' 
						$addWhere $detail_where 
						ORDER BY ips.company_id DESC, od.status DESC
						 "; //ORDER BY company_id DESC
						 //and product_type NOT IN (".implode(',',$sns_product_type).")

		//,(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id) as company_total
		}

		$ddb->query($sql);
		$order_detail_infos = $ddb->fetchall();
		//echo $sql;

		$od_count = $ddb->total;

		$status = getOrderStatus($_order_info[$i][status]);

		//$psum = number_format($ddb->dt[total_price]);

	$bcompany_id = '';
	for($j=0;$j < count($order_detail_infos);$j++){
		//$ddb->fetch($j);
		$one_status = getOrderStatus($ddb->dt[status],$ddb->dt[method])."<input type='hidden' id='od_status_".str_replace("-","",$order_detail_infos[$j][oid])."' value='".$ddb->dt[status]."'>";

if($list_type == "" || $list_type == "item"){
		if($b_pcode != $_order_info[$i][pcode] && $b_pcode != ""){			
			$Contents = str_replace("{stock_sum}",number_format($pcode_stock_sum)." ", $Contents);
			unset($pcode_stock_sum);
			$pcode_stock_sum += $order_detail_infos[$j][stock];
		}else{
			$pcode_stock_sum += $order_detail_infos[$j][stock];
		}
}

		$Contents .= "<tr height=28>";

if($list_type == "item_member"){
		if($mmode != "print"){
		$Contents .= "<td class='list_box_td list_bg_gray' align=center title='".$od_count."'  > 
								<input type=checkbox name='od_ix[]' id='od_ix' class='od_ix_".$order_detail_infos[$j][oid]."' value='".$order_detail_infos[$j][od_ix]."' title='".$ddb->dt[od_ix]."' checked>
							</td>";
		}
		$Contents .="<td class='list_box_td ' align=center nowrap><a href=\"../order/orders.read.php?oid=".$order_detail_infos[$j][oid]."&pid=".$order_detail_infos[$j][pid]."\" style='color:#007DB7;font-weight:bold;' class='small'>".$order_detail_infos[$j][oid]."</a><br/>".getOrderFromName($order_detail_infos[$j][order_from])."</td>";
		$Contents .="<td class='list_box_td point' align=center style='line-height:140%;padding:3px;' nowrap>
								".($order_detail_infos[$j][user_code] != "" ? "<a href=\"javascript:PopSWindow('../member/member_view.php?code=".$order_detail_infos[$j][user_code]."',950,500,'member_info')\" >".Black_list_check($order_detail_infos[$j][user_code],$order_detail_infos[$j][bname])."</a>":$order_detail_infos[$j][bname])."<span class='small'>(".$order_detail_infos[$j][mem_group].")</span><br />".$order_detail_infos[$j][rname]."<br>
							</td>";
		$Contents .="<td class='list_box_td point' align=center >".$order_detail_infos[$j][pcnt]."</td>";
		$Contents .="{Manage_Contents}";
}else if($list_type == "item" || $list_type == ""){
	if($mmode != "print"){
		$Contents .= "<td class='list_box_td list_bg_gray' align=center title='".$od_count."' > 
								<input type=checkbox name='gu_ix[]' id='gu_ix' class='gu_ix_".$order_detail_infos[$j][oid]."' value='".$order_detail_infos[$j][gu_ix]."' title='".$order_detail_infos[$j][gu_ix]."' alt='".$order_detail_infos[$j][gu_ix]."' ".(!($order_detail_infos[$j][pi_ix] && $order_detail_infos[$j][ps_ix]) || $order_detail_infos[$j][section_type]=='D' ?"disabled":"checked")."  >
							</td>";
	}
}


		if($b_pcode != $_order_info[$i][pcode] ){
			$this_pcnt_sum = $_order_info[$i][pcnt_sum];

			if(file_exists($DOCUMENT_ROOT.InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $_order_info[$i][gid], "c"))){
				$inventoryimg = "<img src='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $_order_info[$i][gid], "c")."' style='border:1px solid silver' align=absmiddle>";
			}else{
				$inventoryimg = "<img src='../image/no_img.gif' style='border:1px solid silver' align=absmiddle >";
			}
		
		$Contents .= "<td class='list_box_td ' align=center  rowspan='".$od_count."' >".$_order_info[$i][gid]."</td>";
		if($_order_info[$i][barcode]){
			//$barcode_str = "<img src='/include/barcode/php-barcode-0.4/barcode.php?code=00".$_order_info[$i][barcode]."&encoding=EAN&scale=1&mode=png' style='margin:10px 0px 10px 0px;width:90px;' />";
		}
		$Contents .= "<td class='list_box_td ' align=center  rowspan='".$od_count."' >".$_order_info[$i][barcode]."</td>";
		$Contents .= "<td class='list_box_td list_bg_gray' align=center rowspan='".$od_count."' style='text-align:left;padding:10px 10px 10px 10px;'>
								<table cellpadding=0 cellspacing=0>
									<tr>
										<td width='40' align=center style='padding:0px 2px;'>".$inventoryimg."</td>
										<td  class='list_box_td' style='vertical-align:top;text-align:left; padding-top:5px;line-height:150%;'>
											<a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$_order_info[$i][gid]."',1000,800,'item_info')\">".$_order_info[$i][gname]."</a>
										</td>
									</tr>
								</table>
						</td>";
		$Contents .= "<td class='list_box_td' align=center rowspan='".$od_count."'>".getUnit($_order_info[$i][unit], "basic_unit","","text")."</td>";
		$Contents .= "<td class='list_box_td list_bg_gray' align=center rowspan='".$od_count."'>".$_order_info[$i][standard]."</td>";
		$Contents .= "<td class='list_box_td' align=center rowspan='".$od_count."'>".$_order_info[$i][pcnt_sum]."</td>";
			if($list_type != "item_member"){
				$Contents .= "<td class='list_box_td list_bg_gray' align=center rowspan='".$od_count."' style='line-height:140%;padding:5px;' nowrap>{stock_sum}</td>";
			}
		}
		

if($list_type == "item_member"){
	
		$sql = "select g.cid,g.gname, g.gcode, g.admin, (gu.buying_price*ips.stock) as buying_price, gu.sellprice , g.item_account , g.basic_unit, g.ci_ix, g.pi_ix, 
					pi.place_name, ps.ps_ix, ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.unit, g.gid, gu.safestock, gu.sell_ing_cnt, ips.expiry_date ,
					 (select com_name as company_name from common_company_detail ccd where ccd.company_id = pi.company_id limit 1) as ips_company_name
					from inventory_goods g 
					left join inventory_goods_unit gu on g.gid =gu.gid 
					left join inventory_place_info pi on pi.pi_ix = g.pi_ix and pi.pi_ix = '".$order_detail_infos[$j][pi_ix]."'
					left join inventory_place_section ps on pi.pi_ix = ps.pi_ix and  ps.pi_ix = g.pi_ix and ps.section_type = 'D'
					left join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit and ips.pi_ix = ps.pi_ix and ips.ps_ix = ps.ps_ix
					where gu.gu_ix = '".$order_detail_infos[$j][gu_ix]."' 
					";

	//echo nl2br($sql);
					//echo $_order_info[$i][gid];
		if($order_detail_infos[$j][gid] == "920000008"){
			//echo nl2br($sql);
		}
		//echo nl2br($sql);

		$ddb->query($sql);
		$ddb->fetch();
		$warehouse_info = $ddb->dt;

				
		if($b_pcode != $_order_info[$i][pcode] ){
			if(!($order_detail_infos[$j][pi_ix] && $order_detail_infos[$j][ps_ix])){
				$Contents .="<td class='list_box_td ' align=center colspan=6 rowspan='".$od_count."' style='line-height:140%;'>품목의 기본창고가 지정되야 합니다.<br><a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$_order_info[$i][gid]."',1000,800,'item_info')\"><b style='color:red;'>품목 기본창고지정</b></a></td>";
			}else{
					$Contents .="<td class='list_box_td ' align=center rowspan='".$od_count."'>".$warehouse_info[ips_company_name]."</td>";
					$Contents .="<td class='list_box_td point' align=center rowspan='".$od_count."'>".$warehouse_info[place_name]." </td>";
					$Contents .="<td class='list_box_td point' align=center rowspan='".$od_count."'>".$warehouse_info[section_name]."</td>";
					//$Contents .="<td class='list_box_td point' align=center rowspan='".$od_count."'>".getUnit($warehouse_info[unit], "basic_unit","","text")."</td>";
					$Contents .="<td class='list_box_td point' align=center rowspan='".$od_count."'>".$warehouse_info[expiry_date]."</td>";
					$Contents .="<td class='list_box_td point' align=center rowspan='".$od_count."'>".$warehouse_info[stock]."</td>";
			}
		}
		
		
}else{

		if(!($order_detail_infos[$j][pi_ix] && $order_detail_infos[$j][ps_ix])){
			$Contents .="<td class='list_box_td ' align=center colspan=7 style='line-height:140%;'>입고된 내역이 없습니다.</td>";
		}else{

			$Contents .="<td class='list_box_td ' align=center >".$order_detail_infos[$j][ips_company_name]."</td>";
			$Contents .="<td class='list_box_td point' align=center >".$order_detail_infos[$j][place_name]."</td>";
			$Contents .="<td class='list_box_td point' align=center >".$order_detail_infos[$j][section_name]."</td>";
			//$Contents .="<td class='list_box_td point' align=center >".getUnit($order_detail_infos[$j][unit], "basic_unit","","text")."</td>";
			$Contents .="<td class='list_box_td point' align=center >".$order_detail_infos[$j][expiry_date]."</td>";
			$Contents .="<td class='list_box_td point' align=center >".$order_detail_infos[$j][stock]."</td>";

			if($this_pcnt_sum < $order_detail_infos[$j][stock]){
				if($order_detail_infos[$j][section_type]=='D' || $this_pcnt_sum <= 0){
					$Contents .="<td class='list_box_td point' align=center >".$this_pcnt_sum."</td>";
				}else{
					$Contents .="<td class='list_box_td point' align=center ><input type='textbox' class='delivery_cnt textbox number' size=4 name='delivery_cnt[".$order_detail_infos[$j][gu_ix]."][]' gu_ix='".$order_detail_infos[$j][gu_ix]."' ps_ix='".$order_detail_infos[$j][ps_ix]."' id='delivery_cnt' value='".$this_pcnt_sum."'></td>";
				}
				$this_pcnt_sum = 0;
			}else{
				if($order_detail_infos[$j][section_type]=='D' || $order_detail_infos[$j][stock] <= 0){
					$Contents .="<td class='list_box_td point' align=center >".($order_detail_infos[$j][stock] < 0 ? '0':$order_detail_infos[$j][stock])."</td>";
				}else{
					$Contents .="<td class='list_box_td point' align=center >
								<input type='textbox' class='delivery_cnt textbox number' size=4 name='delivery_cnt[".$order_detail_infos[$j][gu_ix]."][]' gu_ix='".$order_detail_infos[$j][gu_ix]."' ps_ix='".$order_detail_infos[$j][ps_ix]."' id='delivery_cnt'  value='".$order_detail_infos[$j][stock]."'>
								</td>";
				}
				if($order_detail_infos[$j][stock] > 0){
					$this_pcnt_sum = $this_pcnt_sum - $order_detail_infos[$j][stock];
				}
			}
		}

		if($mmode == "print"){
		$Contents .="<td class='list_box_td' align=center > </td>";
		}

		$Contents .="{Manage_Contents}";
} 
			$Manage_Contents = "";
			if($pre_type == ORDER_STATUS_DELIVERY_ING || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
				$Manage_Contents .= "
					<td align='center' class='list_box_td' nowrap>".deliveryCompanyList($order_detail_infos[$j][quick],"text")."</td>
					<td align='center' class='list_box_td' nowrap><a href=\"javascript:searchGoodsFlow('".$order_detail_infos[$j][quick]."', '".str_replace("-","",$order_detail_infos[$j][invoice_no])."')\">".$order_detail_infos[$j][invoice_no]."</a></td>";
			}elseif($pre_type == ORDER_STATUS_DELIVERY_READY && $delivery_status == "WDR"){
				$Manage_Contents .=  "
					<td  class='list_box_td' align='center' style='padding:10px 10px;line-height:140%;'>
						<div style='display:inline;' id='change_status_frm_".md5($order_detail_infos[$j][oid])."_".$order_detail_infos[$j][company_id]."'>
							<table>
							<tr>
								<td>".DeliveryMethod("delivery_method[".$order_detail_infos[$j][od_ix]."]","","onchange=\"/*if(this.value=='TE'){ $('select[name^=quick]').filter('[name*=".$order_detail_infos[$j][od_ix]."]').val(13)}else{ $('select[name^=quick]').filter('[name*=".$order_detail_infos[$j][od_ix]."]').val('')}*/\"","select")."</td>
								<td>".deliveryCompanyList2("quick[".$order_detail_infos[$j][od_ix]."]","SelectbySeller","",$order_detail_infos[$j][company_id])."</td>
							</tr>
							<tr>
								<td><input type='text' name='deliverycode[".$order_detail_infos[$j][od_ix]."]' id='deliverycode'  class=textbox   size=15 value='".$db2->dt[deliverycode]."' validation=true title='송장번호'></td>
								<td><img type=image src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='cursor:pointer;margin:1px 0px;cursor:hand;' onclick=\"SelectDeliveryIng('', '', '', '".$order_detail_infos[$j][od_ix]."')\"></td>
							</tr>   
							</table>
						</div>
					</td>";
			}
			//onclick=\"orderStatusUpdate('".$order_detail_infos[$j][oid]."', 'delivery_update', '$pre_type', 'change_status_frm_".md5($order_detail_infos[$j][oid])."_".$order_detail_infos[$j][company_id]."')\"

			if(($pre_type == ORDER_STATUS_DELIVERY_READY && $delivery_status != "WDR" || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE || $pre_type == ORDER_STATUS_WAREHOUSING_STANDYBY) && $mmode != "print"){
					
					if( $b_gu_ix != $order_detail_infos[$j][gu_ix]){

						$Manage_Contents .= "<td  class='list_box_td' align='center' style='padding:10px 10px;line-height:140%;' rowspan='".$od_count."' >";//".($list_type != "item_member" ? "rowspan='".$od_count."'":"")." 
							/*
							if($pre_type == ORDER_STATUS_DELIVERY_READY && $delivery_status == "WDR"){
								$Manage_Contents .=  "<div style='display:inline;' id='change_status_frm_".md5($order_detail_infos[$j][oid])."_".$order_detail_infos[$j][company_id]."'>
											<table>
											<tr>
												<td>".DeliveryMethod("delivery_method[".$order_detail_infos[$j][od_ix]."]","","","select")."</td>
												<td>".deliveryCompanyList2("quick[".$order_detail_infos[$j][od_ix]."]","SelectbySeller","",$order_detail_infos[$j][company_id])."</td>
											</tr>
											<tr>
												<td><input type='text' name='deliverycode[".$order_detail_infos[$j][od_ix]."]' id='deliverycode'  class=textbox   size=15 value='".$db2->dt[deliverycode]."' validation=true title='송장번호'></td>
												<td><img type=image src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='cursor:pointer;margin:1px 0px;cursor:hand;' onclick=\"orderStatusUpdate('".$order_detail_infos[$j][oid]."', 'delivery_update', '$pre_type', 'change_status_frm_".md5($order_detail_infos[$j][oid])."_".$order_detail_infos[$j][company_id]."')\"></td>
											</tr>   
											</table>
											<!--/form--></div>";

							}else 
							*/
							if($pre_type == ORDER_STATUS_DELIVERY_READY){
								if($list_type == "item_member"){
									if($order_detail_infos[$j][delivery_status] != "WDR"){
										$Manage_Contents.="<a href=\"javascript:ChangeWarehouseStatus('".$order_detail_infos[$j][oid]."','".$order_detail_infos[$j][od_ix]."','WDR')\"><img src='../v3/images/".$admininfo["language"]."/btn_warehouse_delivery_ready.gif'></a>";
									}
									$Manage_Contents.="
									<!--a href=\"javascript:PoPWindow3('../inventory/order_pop.php?gid=".$goods_infos[$i][gid]."',750,700,'input_pop')\"><!--출고대기--><img src='../images/".$admininfo["language"]."/bts_order.gif'></a-->";
								}else{
									if(!($order_detail_infos[$j][pi_ix] && $order_detail_infos[$j][ps_ix])){
										$Manage_Contents.="<a href=\"javascript:alert('출고창고 이동은 기본창고 지정후 하실수 있습니다. 해당 품목의 기본창고를 지정해주세요');\"><img src='../v3/images/".$admininfo["language"]."/btn_devliery_warehouse_move.gif'></a><br><a href=\"javascript:PoPWindow3('../inventory/order_pop.php?gid=".$goods_infos[$i][gid]."',750,700,'input_pop')\"><!--출고창고이동--><img src='../images/".$admininfo["language"]."/bts_order.gif'></a>".$order_detail_infos[$j][delivery_status];
									}else{
										$Manage_Contents.="<a href=\"javascript:ChangeDeliveryProcess('".$order_detail_infos[$j][gu_ix]."','devliery_warehouse_move', true)\"><img src='../v3/images/".$admininfo["language"]."/btn_devliery_warehouse_move.gif'></a><br><a href=\"javascript:PoPWindow3('../inventory/order_pop.php?gid=".$goods_infos[$i][gid]."',750,700,'input_pop')\"><!--출고창고이동--><img src='../images/".$admininfo["language"]."/bts_order.gif'></a>".$order_detail_infos[$j][delivery_status];
									}
								}
								/*
							}else if($pre_type == ORDER_STATUS_WAREHOUSING_STANDYBY){
									$Manage_Contents .=  "<form name='change_status_frm_".$_order_info[$i][pcode]."' method=post action='orders.goods_list.act.php' target='act'><!-- target='act'-->
												<input type='hidden' name='oid' value='".$order_detail_infos[$j][oid]."'>
												<input type='hidden' name='company_id' value='".$order_detail_infos[$j][company_id]."'>
												<input type='hidden' name='act' value='buying_update_bysc'>
												<input type='hidden' name='od_ix_str' value=''>
												<input type='hidden' name='pre_type' value='$pre_type'>
												<input type='hidden' name='change_status' value=''>
											<table border=0 style='text-align:center;' align=center>
											<tr>
											<td  >";
											if($admininfo[admin_level] ==  9){
												$Manage_Contents .=  "<img src='../images/".$admininfo["language"]."/btn_soldout_cancel.gif' align=absmiddle onclick=\"orderBuyingIngSCUpdate(document.change_status_frm_".$_order_info[$i][pcode].", '".ORDER_STATUS_SOLDOUT_CANCEL."')\" style='cursor:pointer;'>";
											}
											$Manage_Contents .= "</td>
											</tr>
											<tr>
											<td  >";
											if($admininfo[admin_level] ==  9){
												$Manage_Contents .=  "<img src='../images/".$admininfo["language"]."/btn_buying_complete.gif' align=absmiddle onclick=\"orderBuyingIngSCUpdate(document.change_status_frm_".$_order_info[$i][pcode].", '".ORDER_STATUS_BUYING_COMPLETE."')\" style='cursor:pointer;'>";
											}
											$Manage_Contents .= "</td>
											</tr>
											<tr>
												<td><a href=\"orders.edit.php?oid=".$order_detail_infos[$j][oid]."&pid=".$order_detail_infos[$j][pid]."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='margin:1px 0px;cursor:hand;'></a></td>
											</tr>
											<tr>
												<td><a href=\"javascript:orderSendSMS('".$order_detail_infos[$j][company_id]."','".$pre_type."')\"><img src='../images/".$admininfo["language"]."/btn_sms_orderinfo.gif' align=absmiddle  style='margin:1px 0px;cursor:hand;'></a></td>
											</tr>
											
											</table>
											</form>";
											*/
							}else if($pre_type == ORDER_STATUS_DELIVERY_ING){
									$Manage_Contents .=  "<img src='../images/".$admininfo["language"]."/btn_delivery_complete.gif' align=absmiddle onclick=\"ChangeStatus('status_update', '".$order_detail_infos[$j][oid]."', '".$order_detail_infos[$j][od_ix]."','".$pre_type."','".ORDER_STATUS_DELIVERY_COMPLETE."')\" style='cursor:hand;'>";

							}else if($pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
										if($order_detail_infos[$j][oid] != $b_oid){
											//$Manage_Contents .=  "<td  class='list_box_td' align='center' style='padding:10px 0px;' ".($order_detail_infos[$j][oid] != $b_oid ? "rowspan='".($od_count)."'":"")." nowrap>";
											$Manage_Contents .=  "<a href=\"orders.edit.php?oid=".$order_detail_infos[$j][oid]."&pid=".$order_detail_infos[$j][pid]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle  style='cursor:hand;'></a>";
											//$Manage_Contents .= "</td>";
										}

							}else{
									if($order_detail_infos[$j][oid] != $b_oid){
										$Manage_Contents .= "<td ".($order_detail_infos[$j][oid] != $b_oid ? "rowspan='".($od_count)."'":"")." class='list_box_td' align='center'  nowrap>";

										if($pre_type == "CA"){
											$Manage_Contents .=  "<img src='../images/".$admininfo["language"]."/btn_cancel_confirm.gif' align=absmiddle onclick=\"ChangeStatus('status_update', '".$order_detail_infos[$j][oid]."', '".$pre_type."')\" style='cursor:hand;'>";

										}else{

										}
									}
							}

						$Manage_Contents .= "</td>";
					}
			}

			$Contents = str_replace("{Manage_Contents}",$Manage_Contents,$Contents);
	 
		$Contents .= "</tr>";

		
	


/*
		if(($od_count-1) == $j && $admininfo[admin_level] == 9){
			$Contents .= "<tr >
							<td class='list_box_td' style='background-color:#efefef;height:30px;font-weight:bold;padding:0 0 0 10px' class=blue colspan=4>
							 <span class='small blue'>".getDeliveryPrice2($order_detail_infos[$j][oid])."</span>
							</td>
							<td class='list_box_td'  style='background-color:#efefef;height:30px;font-weight:bold;'  colspan=3>
							<span>현금영수증 : ".$receipt_y."</span>
							</td>
							<td class='list_box_td'  style='background-color:#efefef;height:30px;font-weight:bold;'  colspan=5 align='right'>
							<b style='color:red;'>결제금액 : ".number_format($_order_info[$i][payment_price])." 원 ".$use_reserve_price."&nbsp;</b>
							</td>
						</tr>";
		}
*/
	
		$b_pcode = $_order_info[$i][pcode];
		$b_unit = $_order_info[$i][unit];
		$b_line = $_order_info[$i][line];
		$b_noe = $_order_info[$i][no];

		$coprice_sum += $order_detail_infos[$j][coprice];
		$pcnt_sum += $order_detail_infos[$j][pcnt];
		$stock_sum += $order_detail_infos[$j][stock];
		$buying_fee_sum += $order_detail_infos[$j][coprice]*$order_detail_infos[$j][pcnt];
		
		$coprice_sum_sum += $order_detail_infos[$j][coprice]*$order_detail_infos[$j][pcnt];

		$b_oid = $_order_info[$i][oid];
		$b_status = $order_detail_infos[$j][status];
		$b_gu_ix = $order_detail_infos[$j][gu_ix]; 

		$bcompany_id = $order_detail_infos[$j][company_id];
		$total_pcnt_sum += $order_detail_infos[$j][pcnt];
		//$buying_fee_sum += $order_detail_infos[$j][coprice]*$order_detail_infos[$j][pcnt];

		

	}
	
	//$Contents .= "<tr height=3><td colspan=10 bgcolor='#DDDDDD'></td></tr>";
	}
	$Contents = str_replace("{total_pcnt_sum}",number_format($total_pcnt_sum)." 건", $Contents);
	$Contents = str_replace("{buying_fee_sum}",number_format($buying_fee_sum)." 원", $Contents);
	$Contents = str_replace("{stock_sum}",number_format($pcode_stock_sum)." ", $Contents);
}else{
	$Contents = str_replace("{total_pcnt_sum}",number_format($total_pcnt_sum)." 건", $Contents);
	$Contents = str_replace("{buying_fee_sum}",number_format($buying_fee_sum)." 원", $Contents);
$Contents .= "<tr height=50><td colspan=17 align=center>조회된 결과가 없습니다.</td></tr>
			";
}




if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
	</table>
	 <table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
	  <tr>
		<td colspan=10 align=left valign=middle style='font-weight:bold' nowrap>";

$Contents .= "

    </td>
  </tr>";
if($mmode != "print"){
$Contents .= "
  <tr height=40>
    <td colspan='10' align='center'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
  </tr>";
}
$Contents .= "
</table>
</div>
";


if($pre_type==ORDER_STATUS_DELIVERY_READY){

	if($list_type == "item_member" && $delivery_status=='WDA'){
		$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한품목 전체</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind_level0' value='level0' checked><label for='update_kind_level0'>출고대기 상태변경</label><!--onclick=\"ChangeUpdateForm('help_text_level0');\" -->
		<!--input type='radio' name='update_kind' id='update_kind_level1' value='level1' onclick=\"ChangeUpdateForm('help_text_level1');\" ><label for='update_kind_level1'>배송처리상태변경</label-->
	</nobr>";

		$help_text = "
		<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>출고처리 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 출고처리 상태의 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>
		<div id='help_text_level0'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
			<col width=170>
			<col width=*>
			<tr id='ht_level0_status'>
				<td class='input_box_title'> <b>출고처리상태</b></td>
				<td class='input_box_item'> ";
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."'  checked><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."'  >".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_READY)."</label> ";
				//onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."')\"
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_WDACC' value='WDACC' ><label for='level0_update_delivery_status_WDACC'  >출고요청 취소</label> ";
				//onclick=\"HelpTextChangeStatus('level0','WDACC')\" 
			$help_text .= "
				</td>
			</tr>
		</table>
		</div>";

		 $help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>";

	}elseif($list_type == "item_member" && $delivery_status=='WDR'){
		$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한품목 전체</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind_level0' value='level0' checked><label for='update_kind_level0'>출고대기 상태변경</label><!--onclick=\"ChangeUpdateForm('help_text_level0');\" -->
		<!--input type='radio' name='update_kind' id='update_kind_level1' value='level1' onclick=\"ChangeUpdateForm('help_text_level1');\" ><label for='update_kind_level1'>배송처리상태변경</label-->
	</nobr>";

		$help_text = "
		<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>출고처리 상태 변경</b> <span class=small style='color:gray'>변경하고자 하는 출고처리 상태의 주문서를 선택하시고 저장 버튼을 클릭해주세요.</span></div>
		<div id='help_text_level0'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
			<col width=170>
			<col width=*>
			<tr id='ht_level0_delivery_status'>
				<td class='input_box_title'> <b>출고처리상태</b></td>
				<td class='input_box_item'> ";
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."' checked><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE."'  >".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_COMPLETE)."</label>";
			$help_text .= "
				</td>
			</tr>
			<tr id='ht_level0_status'>
				<td class='input_box_title'> <b>배송처리상태</b></td>
				<td class='input_box_item'> ";
				$help_text .= "<input type='radio' name='level0_status' id='level0_update_status_".ORDER_STATUS_DELIVERY_ING."' value='".ORDER_STATUS_DELIVERY_ING."' checked><label for='level0_update_status_".ORDER_STATUS_DELIVERY_ING."'  >".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label>";
			$help_text .= "
				</td>
			</tr>
		</table>
		</div>";

		 $help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>";
	
	}else{
		$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한품목 전체</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind_warehouse_move' value='warehouse_move' checked><label for='update_kind_warehouse_move'>출고창고이동</label><!--onclick=\"ChangeUpdateForm('help_text_level0');\" -->
		<!--input type='radio' name='update_kind' id='update_kind_level1' value='level1' onclick=\"ChangeUpdateForm('help_text_level1');\" ><label for='update_kind_level1'>배송처리상태변경</label-->
	</nobr>";

		$help_text = "
		<div style='padding:4px 0;'><img src='../images/dot_org.gif'> <b>출고창고 이동</b> <span class=small style='color:gray'>현재 품목의 출고 창고로 재고이동을 합니다.</span></div>
		<div id='help_text_level0'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
			<col width=170>
			<col width=*>
			<tr id='ht_level0_status'>
				<td class='input_box_title'> <b>출고창고 이동</b></td>
				<td class='input_box_item'> 선택하신 품목의 출고창고로 재고를 이동합니다. ";
				/*
			if($pre_type==ORDER_STATUS_DELIVERY_READY){
				$help_text .= "<input type='radio' name='level0_delivery_status' id='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' value='".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' onclick=\"HelpTextChangeStatus('level0','".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."')\" checked><label for='level0_update_delivery_status_".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."'  >".getOrderStatus(ORDER_STATUS_WAREHOUSE_DELIVERY_READY)."</label>";
			}
			*/
			
			$help_text .= "
				</td>
			</tr>
		</table>
		</div>";

		 $help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50>
            <td colspan=4 align=center>";
            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                $help_text .= "
                <img  src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' onclick=\"ChangeDeliveryProcessAll();\">";
            }else{
                $help_text .= "
                <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
            }
            $help_text .= "
            </td>
        </tr>
	</table>";

	}
	
	if($mmode != "print"){
	$Contents .= HelpBox($help_title, $help_text,250);
	}

}
 
 $Contents .=	"</form>
 <object id='factory' style='display:none' viewastext classid='clsid:1663ed61-23eb-11d2-b92f-008048fdd814'
codebase='http://".$_SERVER["HTTP_HOST"]."/admin/order/scriptx/smsx.cab#Version=7,1,0,60'>
</object>";

$Script = "<script language='javascript'>


	var initBody ;

	function beforePrint() {
		initBody = document.body.innerHTML; document.body.innerHTML = document.getElementById('print_area').innerHTML;
		//alert(document.body.innerHTML);
		$('.not_print_area').hide();
		$('.print_area').show();
		$('*').css('font-size','10px');
		$('b.middle_title').css('font-size','12px');
		$('b.print_area_big_font').css('font-size','18px');
	}

	function afterPrint() {
		document.body.innerHTML = initBody;
	}

	function printArea() {";

	if($mmode == "print"){
		$Script .= "	window.focus(); window.print();";
	}else{
		$Script .= "	window.print();";
	}
	$Script .= "
	}

	window.onbeforeprint = beforePrint;
	window.onafterprint = afterPrint;";

	if($mmode == "print"){
	$Script .= "
	$(document).ready(function() {
		printArea();
		//printPage();
	});";
	}

	$Script .= "

//스크립트 X 주문을 일정량 넘어서면 치명적인 오류 발생으로 인해 사용 중단
function printPage() {
	//alert(1);

	factory.printing.header = ''; // Header에 들어갈 문장
	factory.printing.footer = ''; // Footer에 들어갈 문장
	factory.printing.portrait = false // true 면 세로인쇄, false 면 가로인쇄
	factory.printing.leftMargin = 0.2 // 왼쪽 여백 사이즈
	factory.printing.topMargin = 0.2 // 위 여백 사이즈
	factory.printing.rightMargin = 0.2 // 오른쪽 여백 사이즈
	factory.printing.bottomMargin = 0.2 // 아래 여백 사이즈
	factory.printing.preview();
	factory.printing.Print(false,window) // 출력하기

}



function ChangeDeliveryProcessAll(){
	$('INPUT[id=gu_ix]:checked').each(function(){//enabled
		//alert($(this).val());
		ChangeDeliveryProcess($(this).val(), 'warehouse_move', false)
	});

	alert('출고창고 이동이 정상적으로 처리 되었습니다.');
	document.location.reload();
}

function ChangeDeliveryProcess(gu_ix, status, reload_bool){
	var get_str = '';
	$('INPUT[name^=delivery_cnt]').each(function(){
		if($(this).attr('gu_ix') == gu_ix){
			if(get_str == ''){
				get_str = 'warehouse_delivery['+$(this).attr('ps_ix')+']='+$(this).val();
			}else{
				get_str += '&warehouse_delivery['+$(this).attr('ps_ix')+']='+$(this).val();
			}
		}
	});
	//alert(get_str);
	get_str += '&gu_ix='+gu_ix;
	//document.write(get_str);
	
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'warehouse_move'},
		url: './delivery_ready.act.php?'+get_str,  
		dataType: 'html', 
		async: false, 
		beforeSend: function(){ 
				//alert(11);
		},  
		success: function(data){ 			
			//alert(data);
			if(reload_bool){
				alert(data);
				document.location.reload();
			}
		} 
	}); 
}

function ChangeWarehouseStatus(oid, od_ix, delivery_status){	
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'delivery_status_update','oid':oid,'od_ix':od_ix,'delivery_status':delivery_status},
		url: './delivery_ready.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
				
				
		},  
		success: function(data){ 
			alert(data);
			document.location.reload();
		} 
	}); 
}


function SelectDeliveryIng(product_type,oid_value,set_group_value,od_ix_value){

	frm=document.listform;
	if(product_type==99){
		for(i=0;i < frm.od_ix.length;i++){
			//alert(frm.od_ix[i].value +':::'+od_ix_value+':::'+frm.od_ix[i].getAttribute('oid') +':::'+oid_value +':::'+frm.od_ix[i].getAttribute('set_group')+':::'+set_group_value);
			if(frm.od_ix[i].getAttribute('oid')==oid_value && frm.od_ix[i].getAttribute('set_group')== set_group_value){
				frm.od_ix[i].checked=true;
			}else{
				frm.od_ix[i].checked=false;
			}
		}
	}else{
		for(i=0;i < frm.od_ix.length;i++){
			if(frm.od_ix[i].value==od_ix_value){
				frm.od_ix[i].checked=true;
			}else{
				frm.od_ix[i].checked=false;
			}
		}
	}

	if(CheckStatusUpdate(frm)){
		frm.submit();
	}
}

function CheckStatusUpdate(frm){

	var checked_bool = false;
	var other_seller_bool = false;
	var select_oid ='';
	var select_cnt =0;
	var pre_type = frm.pre_type.value;
	var level ='';

	$('[name=update_kind]').each(function(){
		if($(this).prop('checked')==true){
			level = $(this).val();
		}
	});

	var status_str = level+'_status';
	var status ='';
	var delivery_status_str = level+'_delivery_status';
	var delivery_status ='';
	var reason_code_str = level+'_reason_code';


	$('[name='+status_str+']').each(function(){
		if($(this).prop('checked')==true){
			status = $(this).val();
		}
	});

	$('[name='+delivery_status_str+']').each(function(){
		if($(this).prop('checked')==true){
			delivery_status = $(this).val();
		}
	});

	if(status.length < 1 && delivery_status.length < 1){
		alert('처리상태값을 선택해주세요');
		return false;
	}
	
	if(frm.update_type.value==2){// 선택한 주문일때
		
		for(i=0;i < frm.od_ix.length;i++){
			if(frm.od_ix[i].checked){
				checked_bool = true;
				if(status=='DI'){
					if(frm.od_ix[i].value){
						if($('[name=\"delivery_method['+frm.od_ix[i].value+']\"]').val().length < 1){
							$('[name=\"delivery_method['+frm.od_ix[i].value+']\"]').focus();
							alert('배송타입을 선택해야 합니다.');
							return false;
						}
						if($('[name=\"delivery_method['+frm.od_ix[i].value+']\"]').val()=='tekbae' && $('[name=\"quick['+frm.od_ix[i].value+']\"]').val().length < 1){
							$('[name=\"quick['+frm.od_ix[i].value+']\"]').focus();
							alert('배송업체을 선택해야 합니다.');
							return false;
						}
						if($('[name=\"delivery_method['+frm.od_ix[i].value+']\"]').val()=='tekbae' && $('[name=\"deliverycode['+frm.od_ix[i].value+']\"]').val().length < 1){
							$('[name=\"deliverycode['+frm.od_ix[i].value+']\"]').focus();
							alert('송장번호를 입력해야 합니다.');
							return false;
						}
					}
				}
			}
		}
		

		if(!checked_bool){
			alert(language_data['orders.goods_list.js']['G'][language]);//상태변경하실 주문을 한개이상 선택하셔야 합니다.
			return false;
		}else{
			if(status=='DI'){//배송중은 delivery_update 쪽에서 모두 처리
				frm.act.value='delivery_update';
			}else if(delivery_status=='WDR'||delivery_status=='WDACC'){
				frm.act.value='select_delivery_status_update';
			}
			if(confirm('선택하신 상태로 처리 하시겠습니까?')){
				return true;
			}else{
				return false;
			}
		}
		return false;
	}
}

function clearAll(frm){
		for(i=0;i < frm.od_ix.length;i++){
				frm.od_ix[i].checked = false;
		}
}

function checkAll(frm){ 
       	for(i=0;i < frm.od_ix.length;i++){
				frm.od_ix[i].checked = true;
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



function orderStatusUpdate(oid, act, pre_type, form_id)  {
	
	var od_ix_str = '';
	$('.od_ix_'+oid+':checked').each(function(){
		if(od_ix_str == ''){
			od_ix_str = $(this).val();
		}else{
			od_ix_str += ','+ $(this).val();
		}		
	});
	//alert(od_ix_str);
	if(od_ix_str == ''){
		alert(language_data['orders.goods_list.js']['W'][language]);//'배송완료 처리할 상품을 선택해주세요'
		return false;
	}

	var delivery_company = $('#'+form_id).find('#delivery_company').val();
	if(delivery_company == ''){
		alert(language_data['orders.goods_list.js']['T'][language]);//배송 업체를 선택해주세요
		$('#'+form_id).find('#delivery_company').focus();
		return false;
	}

	var deliverycode = $('#'+form_id).find('#deliverycode').val();
	if(deliverycode.length < 1){
		alert(language_data['orders.goods_list.js']['U'][language]);//송장번호를 입력해주세요
		$('#'+form_id).find('#deliverycode').focus();
		return false;
	}

	var f=document.createElement('form');
	f.name=form_id;
	f.method='post';
	f.action='../order/orders.goods_list.act.php';
	f.target='act';

	var i=document.createElement('input');
	i.type='hidden';
	i.name='oid';
	i.value=oid;
	f.insertBefore(i);

	var i2=document.createElement('input');
	i2.type='hidden';
	i2.name='act';
	i2.value=act;
	f.insertBefore(i2);

	var i3=document.createElement('input');
	i3.type='hidden';
	i3.name='pre_type';
	i3.value=pre_type;
	f.insertBefore(i3);

	var i4=document.createElement('input');
	i4.type='hidden';
	i4.name='od_ix_str';
	i4.value=od_ix_str;
	f.insertBefore(i4);

	var i5=document.createElement('input');
	i5.type='hidden';
	i5.name='delivery_company';
	i5.value=delivery_company;
	f.insertBefore(i5);

	var i6=document.createElement('input');
	i6.type='hidden';
	i6.name='deliverycode';
	i6.value=deliverycode;
	f.insertBefore(i6);





	 // f.submit();

 
	if(confirm(language_data['orders.goods_list.js']['V'][language])){//'선택된 주문상품을 배송처리 하시겠습니까?'
		return true;
	}else{
		return false;
	}
}


//gu_ix용
function clearAll3(frm){
	for(i=0;i < frm.gu_ix.length;i++){
			frm.gu_ix[i].checked = false;
	}
}

function checkAll3(frm){
	for(i=0;i < frm.gu_ix.length;i++){
			frm.gu_ix[i].checked = true;
	}
}

function fixAll3(frm){
	if (!frm.all_fix.checked){
		clearAll3(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll3(frm);
		frm.all_fix.checked = true;
	}
}
function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var depth = sel.getAttribute('depth');

	if(sel.selectedIndex!=0) {
		window.frames['act'].location.href = 'inventory_category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}

}

</script>";

if($mmode == "print"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	if(($list_type == "" || $list_type == "item")){
		$P->Navigation = "출고관리 > 출고지시서 (품목 종합리스트)";
		$P->NaviTitle = "출고지시서(품목 종합리스트)";
	}else if($list_type == "item_member"){
		$P->Navigation = "출고관리 > 출고지시서 (품목/회원별 리스트)";
		$P->NaviTitle = "출고지시서(품목/회원별 리스트)";
	}else if($list_type == "order"){
		$P->Navigation = "출고관리 > 출고지시서 (주문 리스트)";
		$P->NaviTitle = "출고지시서(주문 리스트)";
	}
	
	//$P->layout_display = false;
	echo $P->PrintLayOut();
}else{


	$P = new LayOut();
	$P->strLeftMenu = inventory_menu();
	$P->OnloadFunction = "";//MenuHidden(false);onLoad('$sDate','$eDate');ChangeOrderDate(document.search_frm);
	$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>\n$Script\n";
	$P->Navigation = "출고관리 > $title_str";
	$P->title = $title_str;
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function OrderSummary($status, $title){
	$mdb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));

	if($status == "IR"){
	$sql = "select '구분      ','입금예정 ', '입금예정금액','입금확인건수(카드결제 미포함)', '입금확인금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."') then ptprice else '0' end),0) as incom_ready_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_COMPLETE."')  then ptprice else '0' end),0) as incom_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end),0) as incom_ready_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."') then ptprice else '0' end),0) as incom_ready_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end),0) as incom_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_COMPLETE."')  then ptprice else '0' end),0) as incom_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "CA"){
	$sql = "select '구분      ','취소신청 ', '취소신청금액','취소완료건수', '취소완료금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."'  then 1 else '0' end),0) as cancel_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."') then ptprice else '0' end),0) as cancel_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."'  then 1 else '0' end),0) as cancel_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_COMPLETE."')  then ptprice else '0' end),0) as cancel_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."'  then 1 else '0' end),0) as cancel_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."') then ptprice else '0' end),0) as cancel_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."'  then 1 else '0' end),0) as cancel_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_COMPLETE."')  then ptprice else '0' end),0) as cancel_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "EA"){
	$sql = "select '구분      ','교환신청 ', '교환신청금액','교환완료(회수완료)건수', '교환완료(회수완료)금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."'  then 1 else '0' end),0) as exchange_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice else '0' end),0) as exchange_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_COMPLETE."'  then 1 else '0' end),0) as exchange_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_COMPLETE."')  then ptprice else '0' end),0) as exchange_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."'  then 1 else '0' end),0) as exchange_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_APPLY."') then ptprice else '0' end),0) as exchange_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_COMPLETE."'  then 1 else '0' end),0) as exchange_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_EXCHANGE_COMPLETE."')  then ptprice else '0' end),0) as exchange_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "RA"){
	$sql = "select '구분      ','반품신청 ', '반품신청금액','반품완료건수', '반품완료금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."'  then 1 else '0' end),0) as return_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."') then ptprice else '0' end),0) as return_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_COMPLETE."'  then 1 else '0' end),0) as return_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_COMPLETE."')  then ptprice else '0' end),0) as return_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."'  then 1 else '0' end),0) as return_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."') then ptprice else '0' end),0) as return_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_COMPLETE."'  then 1 else '0' end),0) as return_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_COMPLETE."')  then ptprice else '0' end),0) as return_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else if($status == "FA"){
	$sql = "select '구분      ','환불대기 ', '환불예정금액','환불완료건수', '환불완료금액 '
			union
			Select '".date("m/d")." 금일 ',
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."'  then 1 else '0' end),0) as refund_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_APPLY."') then ptprice else '0' end),0) as refund_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."'  then 1 else '0' end),0) as refund_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_COMPLETE."')  then ptprice else '0' end),0) as refund_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'
			union
			Select '최근1개월',
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."'  then 1 else '0' end),0) as refund_apply_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_APPLY."') then ptprice else '0' end),0) as refund_apply_price,
			IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."'  then 1 else '0' end),0) as refund_complete_cnt,
			IFNULL(sum(case when status in ('".ORDER_STATUS_REFUND_COMPLETE."')  then ptprice else '0' end),0) as refund_complete_price
			from ".TBL_SHOP_ORDER_DETAIL." where regdate between '".date("Y-m-d 00:00:00",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' and '".date("Y-m-d 23:59:59")."' ";
	}else{
		return;
	}

	$mdb->query($sql);
	$datas = $mdb->fetchall();
	//$datas = $mdb->getrows();

	$mstring = "<table width=100%  border=0><form name='search_frm' method='get' action=''>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'>";
if($status == "IR"){
		$mstring .= "<img src='../images/dot_org.gif' align=absmiddle> <b>최근 입금예정 현황</b>";
}else if($status == "CA"){
		$mstring .= "<img src='../images/dot_org.gif' align=absmiddle> <b>최근 취소신청 현황</b>";
}else if($status == "EA"){
		$mstring .= "<img src='../images/dot_org.gif' align=absmiddle> <b>최근 교환신청 현황</b>";
}else if($status == "RA"){
		$mstring .= "<img src='../images/dot_org.gif' align=absmiddle> <b>최근 반품신청 현황</b>";
}else if($status == "FA"){
		$mstring .= "<img src='../images/dot_org.gif' align=absmiddle> <b>최근 환불 현황</b>";
}
	$mstring .= "	</td>
				</tr>
				<tr>
					<td align='left' colspan=2 height=100 width='100%' valign=top style='padding-top:5px;'>
					<table cellpadding=3 cellspacing=1 width='100%' border='0' bgcolor=silver>
						<col width='20%'>
						<col width='20%'>
						<col width='20%'>
						<col width='20%'>
						<col width='20%'>
						";
				for($i=0;$i<count($datas);$i++){
				if($i == 0){
					$mstring .= "
						<tr height=30 ".($i == 0 ? "bgcolor=#efefef  align='center'":"bgcolor=#ffffff  align='right'").">
							<th bgcolor='#efefef' align='center'>".$datas[$i][0]." </th>
							<td style='padding-right:15px;'> ".$datas[$i][1]."</td>
							<td style='padding-right:15px;'> ".$datas[$i][2]."</td>
							<td style='padding-right:15px;'> ".$datas[$i][3]." </td>
							<td style='padding-right:15px;'> ".$datas[$i][4]." </td>
						</tr>";
				}else{
					$mstring .= "
						<tr height=30 ".($i == 0 ? "bgcolor=#efefef  align='center'":"bgcolor=#ffffff  align='right'").">
							<th bgcolor='#efefef' align='center'>".$datas[$i][0]." </th>
							<td style='padding-right:15px;'> ".number_format($datas[$i][1])." 건</td>
							<td style='padding-right:15px;'> ".number_format($datas[$i][2])." 원</td>
							<td style='padding-right:15px;'> ".number_format($datas[$i][3])." 건</td>
							<td style='padding-right:15px;'> ".number_format($datas[$i][4])." 원</td>
						</tr>";
				}
				}
				$mstring .= "
						<!--tr bgcolor=#ffffff height=30 align='right'>
							<th bgcolor='#efefef' align='center'>최근 30일 </th>
							<td style='padding-right:15px;'>15건</td>
							<td style='padding-right:15px;'>3400,000 원</td>
							<td style='padding-right:15px;'> - </td>
							<td style='padding-right:15px;'> - </td>
						</tr-->
					</table>
					</td>
				</tr>
				<tr>
					<td style='padding:5px 0px;text-align:right;'>* 위 통계는 주문일 기준으로 작성 됩니다.</td>
				</tr>
			</table>";
	return $mstring;
}
?>