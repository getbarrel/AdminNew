<?
include_once("../class/layout.class");
include("../buyingservice/buying.lib.php");
//print_r($admin_config);
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



$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

// 검색 조건 설정 부분
$where = "WHERE od.status <> 'SR' AND od.product_type NOT IN (".implode(',',$sns_product_type).") and od.sc_ix != '' ";

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
		$where .= "and o.delivery_type in ($delivery_type_str) ";
	}
}else{
	if($delivery_type){
		$where .= "and o.delivery_type = '$delivery_type' ";
	}
}
/*
if($delivery_type!="") {
	$where.=" AND delivery_type='".$delivery_type."' ";
}
*/

if($sc_ix != ""){
	$where .= " and od.sc_ix  = '".$sc_ix."'  ";
}

if($floor != ""){
	$where .= " and od.floor  = '".$floor."'  ";
}

if($line != ""){
	$where .= " and od.line  = '".$line."'  ";
}

if($no != ""){
	$where .= " and od.no  = '".$no."'  ";
}

if($open_timezone != ""){
	$where .= " and sc.open_timezone  = '".$open_timezone."'  ";
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


	$sql = "SELECT od.sc_ix, od.floor, od.line, od.no
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join buyingservice_shopping_center sc on od.sc_ix = sc.sc_ix
					$where 
					GROUP BY od.sc_ix, od.floor, od.line, od.no  "; //, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
	//echo nl2br($sql);
	//exit;
	$db1->query($sql);


	$total = $db1->total;



if($mode == "excel"){
	$sql = "SELECT od.sc_ix, od.floor, od.line, od.no, od.company_name, od.com_phone, od.represent_name, od.represent_mobile, count(od.pid) as group_cnt
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join buyingservice_shopping_center sc on od.sc_ix = sc.sc_ix
					$where
					GROUP BY od.sc_ix, od.floor, od.line, od.no 
					ORDER BY od.sc_ix, od.floor, od.line, od.no 
					";

	//echo nl2br($sql);
	//exit;
	$db1->query($sql);
	$_order_info = $db1->fetchall();

PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
	
	date_default_timezone_set('Asia/Seoul');
	
	$accounts_plan_priceXL = new PHPExcel();
	
	// 속성 정의
	$accounts_plan_priceXL->getProperties()->setCreator("몰스토리")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle(iconv("UTF-8","CP949","업체별_사입리스트"))
								 ->setSubject(iconv("UTF-8","CP949","업체별_사입리스트"))
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory(iconv("UTF-8","CP949","업체별_사입리스트"));

								 
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('A' . 1, "번호");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('B' . 1, "상가");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('C' . 1, "층");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('D' . 1, "라인");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('E' . 1, "호수");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('F' . 1, "상호");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('G' . 1, "매전전화번호");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('H' . 1, "장기명");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('I' . 1, "옵션");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('J' . 1, "공급가");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('K' . 1, "수량");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('L' . 1, "합계");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('M' . 1, "입고완료");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('N' . 1, "품절취소");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('O' . 1, "입고대기");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('P' . 1, "교환");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('Q' . 1, "반품");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('R' . 1, "처리상태");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('S' . 1, "주문일자");
	$accounts_plan_priceXL->getActiveSheet(0)->setCellValue('T' . 1, "주문자/받는사람");
	
	$k = 0;
	for ($i = 0; $i < count($_order_info); $i++){
		//$db1->fetch($i);

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
			$sql = "SELECT o.oid, od.od_ix,od.pname,od.paper_pname, od.option_text, od.regdate, od.psprice, od.option_price, od.coprice, od.pcnt, od.ptprice,od.commission, uid,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,o.delivery_type, com_name,od.pid, rname, bname, mem_group,o.use_reserve_price, od.com_phone,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							and product_type NOT IN (".implode(',',$sns_product_type).")
							group by company_id  limit 1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and od.sc_ix = '".$_order_info[$i][sc_ix]."' and od.floor = '".$_order_info[$i][floor]."' and od.line = '".$_order_info[$i][line]."' and od.no = '".$_order_info[$i][no]."'  and od.product_type NOT IN (".implode(',',$sns_product_type).")
						$addWhere $detail_where ORDER BY company_id DESC, od.status DESC
						 "; //ORDER BY company_id DESC


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

			$sql = "SELECT o.oid, od.od_ix,od.pname,od.paper_pname, od.option_text, od.regdate, od.psprice, od.option_price, od.coprice, od.pcnt, od.ptprice,od.commission, uid,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,o.delivery_type, com_name,od.pid, rname, bname, mem_group,o.use_reserve_price, od.com_phone,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							and product_type NOT IN (".implode(',',$sns_product_type).")
							group by company_id  limit 1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and od.sc_ix = '".$_order_info[$i][sc_ix]."' and od.floor = '".$_order_info[$i][floor]."' and od.line = '".$_order_info[$i][line]."' and od.no = '".$_order_info[$i][no]."'  and od.company_id ='".$admininfo[company_id]."' and od.product_type NOT IN (".implode(',',$sns_product_type).")
						$addWhere $detail_where ORDER BY company_id DESC, od.status DESC
						 "; 
		}

		$ddb->query($sql);

		for($j=0;$j < $ddb->total;$j++){
			$ddb->fetch($j);

			if($b_sc_ix != $_order_info[$i][sc_ix] || $b_floor != $_order_info[$i][floor] || $b_line != $_order_info[$i][line] || $b_noe != $_order_info[$i][no]){
				
					if($i != 0){

						$accounts_plan_priceXL->getActiveSheet()->setCellValue('A' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('B' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('C' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('D' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('E' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('F' . ($k + 2) ,"");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('G' . ($k + 2) ,"");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('H' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('I' . ($k + 2), "합계");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('J' . ($k + 2), $coprice_sum);
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('K' . ($k + 2), $pcnt_sum);
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('L' . ($k + 2), $coprice_sum_sum);
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('M' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('N' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('O' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('P' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('Q' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('R' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('S' . ($k + 2), "");
						$accounts_plan_priceXL->getActiveSheet()->setCellValue('T' . ($k + 2), "");
						$coprice_sum = 0;
						$pcnt_sum = 0;
						$coprice_sum_sum = 0;
						$k++;
					}
				
			}

			$accounts_plan_priceXL->getActiveSheet()->setCellValue('A' . ($k + 2), ($k+1));
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('B' . ($k + 2), $_shopping_center_info[$_order_info[$i][sc_ix]]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('C' . ($k + 2), $_floor_info[$_order_info[$i][floor]]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('D' . ($k + 2), $_line_info_english[$_order_info[$i][line]].$_line_info_korea[$_order_info[$i][line]]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('E' . ($k + 2), $_order_info[$i][no]." 호");
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('F' . ($k + 2) ,$ddb->dt[company_name]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('G' . ($k + 2) ,$ddb->dt[com_phone]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('H' . ($k + 2), $ddb->dt[pname]."(장기명 :".$ddb->dt[paper_pname].")");
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('I' . ($k + 2), str_replace("<br>"," ",$ddb->dt[option_text]));
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('J' . ($k + 2), $ddb->dt[coprice]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('K' . ($k + 2), $ddb->dt[pcnt]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('L' . ($k + 2), $ddb->dt[coprice]*$ddb->dt[pcnt]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('M' . ($k + 2), "");
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('N' . ($k + 2), "");
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('O' . ($k + 2), "");
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('P' . ($k + 2), "");
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('Q' . ($k + 2), "");

			$accounts_plan_priceXL->getActiveSheet()->setCellValue('R' . ($k + 2), strip_tags(getOrderStatus($ddb->dt[status])));
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('S' . ($k + 2), $ddb->dt[regdate]);
			$accounts_plan_priceXL->getActiveSheet()->setCellValue('T' . ($k + 2), $ddb->dt[bname]."/".$ddb->dt[rname]);

			$coprice_sum += $ddb->dt[coprice];
			$pcnt_sum += intval($ddb->dt[pcnt]);
			$coprice_sum_sum += intval($ddb->dt[coprice]*$ddb->dt[pcnt]);

			

			$b_sc_ix = $_order_info[$i][sc_ix];
			$b_floor = $_order_info[$i][floor];
			$b_line = $_order_info[$i][line];
			$b_noe = $_order_info[$i][no];

			$k++;
		}

					$accounts_plan_priceXL->getActiveSheet()->setCellValue('A' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('B' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('C' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('D' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('E' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('F' . ($k + 2) ,"");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('G' . ($k + 2) ,"");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('H' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('I' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('J' . ($k + 2), $coprice_sum);
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('K' . ($k + 2), $pcnt_sum);
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('L' . ($k + 2), $coprice_sum_sum);
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('M' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('N' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('O' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('P' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('Q' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('R' . ($k + 2), "");
					$accounts_plan_priceXL->getActiveSheet()->setCellValue('S' . ($k + 2), "");
					//$coprice_sum = 0;
					//$pcnt_sum = 0;
				
			
		
	}
	
	$accounts_plan_priceXL->getActiveSheet()->setTitle('상가별 사입내역');
	
	// 첫번째 시트 선택
	$accounts_plan_priceXL->setActiveSheetIndex(0);
	
	// 너비조정
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
	$accounts_plan_priceXL->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.iconv("UTF-8","CP949","업체별_사입리스트").'_'.date("Ymd").'.xls"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($accounts_plan_priceXL, 'Excel5');
	$objWriter->save('php://output');
	exit;

}else{

	$sql = "SELECT sum(od.coprice*od.pcnt) as total_buying_fee, count(od.pid) as total_pcnt
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join buyingservice_shopping_center sc on od.sc_ix = sc.sc_ix
					$where";

	//echo nl2br($sql);
	$db1->query($sql);
	$db1->fetch();
	$total_buying_fee = $db1->dt[total_buying_fee];
	$total_pcnt = $db1->dt[total_pcnt];
	

	$sql = "SELECT od.sc_ix, od.floor, od.line, od.no, od.company_name, od.com_phone, od.represent_name, od.represent_mobile, count(od.pid) as group_cnt
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od left join buyingservice_shopping_center sc on od.sc_ix = sc.sc_ix
					$where
					GROUP BY od.sc_ix, od.floor, od.line, od.no 
					ORDER BY od.sc_ix, od.floor, od.line, od.no 
					LIMIT $start, $max";

	//echo nl2br($sql);
	$db1->query($sql);
	$_order_info = $db1->fetchall();
	//print_r($_order_info);
}

if($mmode == "print" || $mmode == "order_sms"){
	include("./ordersbysc.read.php");
	exit;
}


$Contents = "

<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("$title_str", "배송관리 > $title_str ")."</td>
	</tr>";
if($pre_type == ORDER_STATUS_DELIVERY_READY){
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
									<td class='box_02' onclick=\"document.location.href='invoice_input_excel.php'\">일괄송장입력</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_02' class='on'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='delivery_ready.php'\">배송준비중 상품목록</td>
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
									<td class='box_02 blk' onclick=\"document.location.href='?list_type=sc'\">업체별 주문 리스트</td>
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
if(false){
$Contents .= "
		<!--td style='width:25%;' valign=top>
			<table width=100% border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>최근 주문현황</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 height=150 width='270px' valign=top style='padding-top:5px;'>".PrintOrderSummary3()."</td>
				</tr>
			</table>
		</td-->";
}
$Contents .= "
		<td style='width:75%;' colspan=2 valign=top>
		<form name='search_frm' method='get' action=''>
		<input type=hidden name=list_type value='".$_GET["list_type"]."'>
			<table width=100%  border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>주문정보 검색하기</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
						
						<TABLE cellSpacing=0 cellPadding=3 style='width:100%;' align=center border=0 class='search_table_box'>
								<col width=15%>
								<col width=35%>
								<col width=15%>
								<col width=35%>
								<tr height=30>
									<th class='search_box_title'>검색항목 : </th>
									<td class='search_box_item' colspan=3>
										<table cellpadding='3' cellspacing='0' border='0' width='100%'>
										<col width='170px'>
										<col width='*'>
										<tr>
											<td >
											<select name='search_type' style='font-size:12px;'>
												<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자이름+입금자명+수취인명</option>
												<option value='bname' ".CompareReturnValue('bname',$search_type,' selected').">주문자이름</option>
												<option value='pname' ".CompareReturnValue('pname',$search_type,' selected').">상품이름</option>
												<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
												<option value='rname' ".CompareReturnValue('rname',$search_type,' selected').">수취인이름</option>
												<option value='bmobile' ".CompareReturnValue('bmobile',$search_type,' selected').">주문자핸드폰</option>
												<option value='rmobile' ".CompareReturnValue('rmobile',$search_type,' selected').">수취인핸드폰</option>
												<option value='deliverycode' ".CompareReturnValue('deliverycode',$search_type,' selected').">송장번호</option>
											</select>
											</td>
											<td ><input type='text' class=textbox name='search_text' size='30' value='$search_text' style=''></td>
											</tr>
											</table>
										</td>
								</tr>
								<tr height=27>
									<th class='search_box_title''>
									<!--label for='visitdate'>주문일자</label-->
									<select name='date_type'>
									<option value='o.date' ".CompareReturnValue('o.date',$date_type,' selected').">주문일자</option>
									<option value='o.bank_input_date' ".CompareReturnValue('o.bank_input_date',$date_type,' selected').">입금일자</option>
									<!--option value='date'>취소일자</option-->
									</select>
									<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
									<td class='search_box_item' colspan=3>
										<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff>
											<tr>
												<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
												<TD style='padding:0 5px;' align=center> ~ </TD>
												<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>
												<td>";

	$vdate = date("Ymd", time());
	$today = date("Y/m/d", time());
	$vyesterday = date("Y/m/d", time()-84600);
	$voneweekago = date("Y/m/d", time()-84600*7);
	$vtwoweekago = date("Y/m/d", time()-84600*14);
	$vfourweekago = date("Y/m/d", time()-84600*28);
	$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
	$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
	$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
	$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
	$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
	$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
	$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

				$Contents .= "
									<a href=\"javascript:init_date('$today','$today');\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
									<a href=\"javascript:init_date('$vyesterday','$vyesterday');\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
									<a href=\"javascript:init_date('$voneweekago','$today');\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
									<a href=\"javascript:init_date('$v15ago','$today');\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
									<a href=\"javascript:init_date('$vonemonthago','$today');\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
									<a href=\"javascript:init_date('$v2monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
									<a href=\"javascript:init_date('$v3monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>

												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr height=30>
									<th class='search_box_title'>결제방법 : </th>
									<td class='search_box_item' colspan=3>
									<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$method,' checked')." ><label for='method_".ORDER_METHOD_BANK."'>".getMethodStatus(ORDER_METHOD_BANK)."</label>
									<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$method,' checked')." ><label for='method_".ORDER_METHOD_CARD."'>".getMethodStatus(ORDER_METHOD_CARD)."</label>
									<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$method,' checked')." ><label for='method_".ORDER_METHOD_VBANK."'>".getMethodStatus(ORDER_METHOD_VBANK)."</label>

									<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$method,' checked')." ><label for='method_".ORDER_METHOD_ICHE."'>".getMethodStatus(ORDER_METHOD_ICHE)."</label>

									<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$method,' checked')." ><label for='method_".ORDER_METHOD_PHONE."'>".getMethodStatus(ORDER_METHOD_PHONE)."</label-->
									<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$method,' checked')." ><label for='method_".ORDER_METHOD_SAVEPRICE."'>".getMethodStatus(ORDER_METHOD_SAVEPRICE)."</label>
									</td>
								</tr>
								<tr height=30>
									<th class='search_box_title'>결제형태 : </th>
									<td class='search_box_item' ".(($admininfo[mall_type] == "F" || $admininfo[admin_level] == 8) ? "colspan=3":"").">
										<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_W' value='W' ".CompareReturnValue("W",$payment_agent_type,' checked')."><label for='payment_agent_type_W'>일반(웹)결제</label>
										<input type='checkbox' name='payment_agent_type[]' id='payment_agent_type_M' value='M' ".CompareReturnValue("M",$payment_agent_type,' checked')."><label for='payment_agent_type_M'>모바일결제</label>
									</td>";
								if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
								$Contents .= "
									<th class='search_box_title'>업체명 : </th>
									<td class='search_box_item'>".CompanyList($company_id,"","")."</td>";
								}
								$Contents .= "
								</tr>
							";
if(!$pre_type){
$Contents .= "
								<tr>
									<th class='search_box_title'>처리상태 : </th>
									<td class='search_box_item' colspan=3>
									<table cellpadding=0 cellspacing=0 width='100%' border='0'>
										<col width='20%'>
										<col width='20%'>
										<col width='20%'>
										<col width='20%'>
										<col width='20%'>
										<TR>
											<TD ><input type='checkbox' name='type[]' id='type_ir' value='".ORDER_STATUS_INCOM_READY."' ".CompareReturnValue(ORDER_STATUS_INCOM_READY,$type,' checked')." ><label for='type_ir'>".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label></TD>
											<TD ><input type='checkbox' name='type[]' id='type_ic' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$type,' checked')." ><label for='type_ic'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label></TD>
											<TD ><input type='checkbox' name='type[]' id='type_oc' value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$type,' checked')." ><label for='type_oc'>".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label></TD>
											<TD ><input type='checkbox' name='type[]' id='type_or' value='".ORDER_STATUS_DELIVERY_READY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_READY,$type,' checked')." ><label for='type_or'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label></TD>
											<TD ><input type='checkbox' name='type[]' id='type_ob' value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$type,' checked')." ><label for='type_ob'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label></TD>
										</TR>
										<TR>
											<TD><input type='checkbox' name='type[]' id='type_r1' value='".ORDER_STATUS_RETURN_APPLY."' ".CompareReturnValue(ORDER_STATUS_RETURN_APPLY,$type,' checked')." ><label for='type_r1'>".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</label></TD>
											<TD><input type='checkbox' name='type[]'  id='type_r2' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." ><label for='type_r2'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label></TD>
											<TD><input type='checkbox' name='type[]' id='type_ca' value='".ORDER_STATUS_CANCEL_APPLY."' ".CompareReturnValue(ORDER_STATUS_CANCEL_APPLY,$type,' checked')."><label for='type_ca'>".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</label></TD>
											<TD><input type='checkbox' name='type[]'  id='type_xx' value='".ORDER_STATUS_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_CANCEL_COMPLETE,$type,' checked')." ><label for='type_xx'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label></TD>
										</TR>
										<TR>
											<TD><input type='checkbox' name='type[]' id='type_ob' value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$type,' checked')." ><label for='type_ob'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</label></TD>
											<TD><input type='checkbox' name='type[]' id='type_ei' value='".ORDER_STATUS_EXCHANGE_ING."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ING,$type,' checked')." ><label for='type_ei'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label></TD>
											<TD ><input type='checkbox' name='type[]' id='type_ed' value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DELIVERY,$type,' checked')." ><label for='type_r1'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</label></TD>
											<TD><input type='checkbox' name='type[]'  id='type_ec' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_COMPLETE,$type,' checked')." ><label for='type_r2'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label></TD>

										</TR>
									</TABLE>
									</td>
								</tr>";
}else if($pre_type == "EA"){
$Contents .= "
								<tr>
									<th class='search_box_title'>처리상태 : </th>
									<td class='search_box_item' colspan=3>
									<table cellpadding=0 cellspacing=0 width='100%' border='0'>
										<col width='20%'>
										<col width='20%'>
										<col width='20%'>
										<col width='20%'>
										<col width='20%'>

										<TR height=30>
											<TD>
												<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_APPLY."' value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$type,' checked')." >
												<label for='type_".ORDER_STATUS_EXCHANGE_APPLY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</label>
											</TD>
											<TD>
												<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_ING."' value='".ORDER_STATUS_EXCHANGE_ING."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ING,$type,' checked')." >
												<label for='type_".ORDER_STATUS_EXCHANGE_ING."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label></TD>
											<TD >
												<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_EXCHANGE_DELIVERY."' value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DELIVERY,$type,' checked')." >
												<label for='type_".ORDER_STATUS_EXCHANGE_DELIVERY."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</label></TD>
											<TD>
												<input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_EXCHANGE_COMPLETE."' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_COMPLETE,$type,' checked')." >
												<label for='type_".ORDER_STATUS_EXCHANGE_COMPLETE."'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label></TD>
											<td></td>
										</TR>
									</TABLE>
									</td>
								</tr>";
}else if($pre_type == "RA"){
$Contents .= "
								<tr>
									<th class='search_box_title'>처리상태 : </th>
									<td class='search_box_item' colspan=3>
									<table cellpadding=0 cellspacing=0 width='100%' border='0'>
										<col width='20%'>
										<col width='20%'>
										<col width='20%'>
										<col width='20%'>
										<col width='20%'>

										<TR height=30>
											<TD>
												<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_APPLY."' value='".ORDER_STATUS_RETURN_APPLY."' ".CompareReturnValue(ORDER_STATUS_RETURN_APPLY,$type,' checked')." >
												<label for='type_".ORDER_STATUS_RETURN_APPLY."'>".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</label>
											</TD>
											<TD>
												<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_ING."' value='".ORDER_STATUS_RETURN_ING."' ".CompareReturnValue(ORDER_STATUS_RETURN_ING,$type,' checked')." >
												<label for='type_".ORDER_STATUS_RETURN_ING."'>".getOrderStatus(ORDER_STATUS_RETURN_ING)."</label></TD>
											<TD >
												<input type='checkbox' name='type[]' id='type_".ORDER_STATUS_RETURN_DELIVERY."' value='".ORDER_STATUS_RETURN_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_RETURN_DELIVERY,$type,' checked')." >
												<label for='type_".ORDER_STATUS_RETURN_DELIVERY."'>".getOrderStatus(ORDER_STATUS_RETURN_DELIVERY)."</label></TD>
											<TD>
												<input type='checkbox' name='type[]'  id='type_".ORDER_STATUS_RETURN_COMPLETE."' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." >
												<label for='type_".ORDER_STATUS_RETURN_COMPLETE."'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label></TD>
											<td></td>

										</TR>
									</TABLE>
									</td>
								</tr>";
}
$Contents .= "				<tr height=30 class='detail_search' style='".($_COOKIE["detail_search"] == 1 ? "display:block;":"display:none;")."'>
									<th class='search_box_title' >회원구분 : </th>
									<td class='search_box_item' colspan=3>
										<!--input type=checkbox name='mem_type[]' value='' id='mem_type_'  ".CompareReturnValue("",$mem_type,"checked")."><label for='mem_type_'>모두</label-->
										<input type='checkbox' name='mem_type[]' id='mem_type_d' value='D' ".CompareReturnValue("D",$mem_type,' checked')." /> <label for='mem_type_d'>내국인</label>
										<input type='checkbox' name='mem_type[]' id='mem_type_p' value='P' ".CompareReturnValue("P",$mem_type,' checked')." /> <label for='mem_type_p'>해외거주자</label>
										<input type='checkbox' name='mem_type[]' id='mem_type_f' value='F' ".CompareReturnValue("F",$mem_type,' checked')." /> <label for='mem_type_f'>외국인</label>
									</td>
								</tr>";



$Contents .= "			<tr height=30 class='detail_search' style='".($_COOKIE["detail_search"] == 1 ? "display:block;":"display:none;")."'>
									<th class='search_box_title' >배송구분 : </th>
									<td class='search_box_item' colspan='3'>
										<!--input type=checkbox name='delivery_type[]' value='' id='delivery_type_'  ".CompareReturnValue("",$delivery_type,"checked")."><label for='mem_type_'>모두</label-->
										<input type='checkbox' name='delivery_type[]' id='delivery_type_c' value='C' ".CompareReturnValue("C",$delivery_type,' checked')." /> <label for='delivery_type_c'>고객배송대행</label>
										<input type='checkbox' name='delivery_type[]' id='delivery_type_a' value='A' ".CompareReturnValue("A",$delivery_type,' checked')." /> <label for='delivery_type_a'>완사입배송</label>
										<input type='checkbox' name='delivery_type[]' id='delivery_type_d' value='D' ".CompareReturnValue("D",$delivery_type,' checked')." /> <label for='delivery_type_d'>즉시배송</label>
									</td>
								</tr>
								<tr bgcolor=#ffffff class='detail_search' style='".($_COOKIE["detail_search"] == 1 ? "display:block;":"display:none;")."'>
									<td class='input_box_title'> 상가검색 : </td>
									<td class='input_box_item' >
									<table>
										<tr>
											<td>".getShoppingCenter($_GET[sc_ix], "select"," onchange=\"loadShoppingCenterInfo(this, 'floor')\" validation='false' ")."</td>
											<td>".getShoppingCenterFloorInfo($_GET[sc_ix], $_GET[floor], "select")."</td>
											<td>".getShoppingCenterLineInfo($_GET[sc_ix], $_GET[line], "select")."</td>
											<td>".getShoppingCenterNoInfo($_GET[sc_ix], $_GET[no], "select")."</td>
										</tr>
									</table>
									</td>
									<td class='input_box_title'> <b>오픈시간대 </b></td>
									<td class='input_box_item'>
										<input type=radio name='open_timezone' id='open_timezone_all' value='' validation='true' title='전체' ".CompareReturnValue("",$open_timezone," checked")." > <label for='open_timezone_all'>전체</label> 
										<input type=radio name='open_timezone' id='open_timezone_d' value='D' validation='true' title='오픈시간대' ".CompareReturnValue("D",$open_timezone," checked")." > <label for='open_timezone_d'>낮시장</label> 
										<input type=radio name='open_timezone' id='open_timezone_n' value='N' validation='true' title='오픈시간대' ".CompareReturnValue("N",$open_timezone," checked")."> <label for='open_timezone_n'>밤시장</label> 
										<input type=radio name='open_timezone' id='open_timezone_e' value='E' validation='true' title='오픈시간대' ".CompareReturnValue("E",$open_timezone," checked")."> <label for='open_timezone_e'>기타</label></td>
								</tr>
						</TABLE>
					</td>
				</tr>
				<tr><td style='text-align:right' ><a href='#' onclick=\"DetailSearchView();\" id='detail_search_text'>".($_COOKIE["detail_search"] == 1 ? "상세검색 숨김":"상세검색 보기")."</a></td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr></form>
</table>

<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
 <tr>";

//exit;
 $Contents .= "<td colspan=3 align=left><!--b>전체 주문수 : $total 건</b--> 총 매장수 : <b>".number_format($total)."</b> , 총 상품수 : <b>".number_format($total_pcnt)."</b> , 총 사입금액 : <b>".number_format($total_buying_fee)."</b> </td>
				<td colspan=5 align=right>  
				<a href='?mmode=print&".$QUERY_STRING."&pre_type=".$pre_type."' ><img src='../images/".$admininfo["language"]."/btn_print.gif'  border=0 style='cursor:pointer;' /></a>
				";
if($pre_type == ORDER_STATUS_DELIVERY_READY){
	$Contents .= "<a href='excel_out.php?excel_type=delivery' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif' ></a>";
}

if($admininfo[admin_level] == 9){
	if($pre_type == ORDER_STATUS_DELIVERY_READY || $pre_type == ORDER_STATUS_WAREHOUSING_STANDYBY || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE ){
		$Contents .= " <a href='?mode=excel&".$QUERY_STRING."&pre_type=".$pre_type."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
	}
}else if($admininfo[admin_level] == 8){
	if($pre_type == ORDER_STATUS_DELIVERY_READY){
		$Contents .= "<span style='color:red'><!--! 주의 : 입금예정 처리상태일 경우, 상품배송을 하지 마시기 바랍니다. 판매된 상품으로 처리 불가능-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
		$Contents .= "<a href='?mode=excel&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
	}
$Contents .= "<a href='?mode=excel&".$QUERY_STRING."'><img src='../image/btn_delivery_excel_save.gif' border=0 align=absmiddle></a>";
}
$Contents .= "
  </td>
  </tr>
  </table>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='25' >";
	if($pre_type != ORDER_STATUS_EXCHANGE_APPLY){
	//$Contents .= "<td class='s_td' width='5%'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>";
	}
	$Contents .= "
		<td width='5%' align='center' class='m_td'><font color='#000000'><b>상가</b></font></td>
		<td width='5%' align='center' class='m_td'><font color='#000000'><b>층</b></font></td>
		<td width='5%' align='center' class='m_td'><font color='#000000'><b>라인</b></font></td>
		<td width='5%' align='center' class='m_td'><font color='#000000'><b>호수</b></font></td>
		<td width='5%' align='center' class='m_td'><font color='#000000'><b>상호</b></font></td>
		<td width='14%' align='center' class='s_td'><font color='#000000' ><b>주문일자/주문번호</b></font></td>
		<td width='7%' align='center'  class='m_td' nowrap><font color='#000000' ><b>주문자명/받는사람명</b></font></td>
		<td width='*' align='center' class='m_td' nowrap><font color='#000000' ><b>제품명</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>공급가</b></font></td>
		<td width='4%' align='center' class='m_td' nowrap><font color='#000000' ><b>수량</b></font></td>
		<td width='4%' align='center' class='m_td' nowrap><font color='#000000' ><b>사입금액</b></font></td>
		<!--td width='6%' align='center' class='m_td' nowrap><font color='#000000' ><b>주문금액</b></font></td-->";
if($pre_type == ORDER_STATUS_DELIVERY_READY){
	$Contents .= "
		<td width='7%' align='center' class='e_td' nowrap><font color='#000000' ><b>처리상태</b></font></td>";
}else if($pre_type == ORDER_STATUS_DELIVERY_ING || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
	$Contents .= "
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>처리상태</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>택배사</b></font></td>
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>송장번호/위치추적</b></font></td>
		<td width='10%' align='center' class='e_td' nowrap><font color='#000000' ><b>관리</b></font></td>";
}else{
	$Contents .= "
		<td width='7%' align='center' class='m_td' nowrap><font color='#000000' ><b>처리상태</b></font></td>
		<td width='10%' align='center' class='e_td' nowrap><font color='#000000' ><b>관리</b></font></td>";
}
$Contents .= "
	</tr>

  ";



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
			$sql = "SELECT o.oid, o.delivery_box_no, od.od_ix,od.pname, od.option_text, od.regdate, od.psprice, od.coprice, od.pcnt, od.ptprice,od.commission, uid,od.delivery_price,
						o.delivery_price as pay_delivery_price,o.delivery_type, com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.com_phone, od.represent_mobile, od.company_id, od.quick, od.invoice_no,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							and product_type NOT IN (".implode(',',$sns_product_type).")
							group by company_id  limit 1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and od.sc_ix = '".$_order_info[$i][sc_ix]."' and od.floor = '".$_order_info[$i][floor]."' and od.line = '".$_order_info[$i][line]."' and od.no = '".$_order_info[$i][no]."'  and od.product_type NOT IN (".implode(',',$sns_product_type).") 
						$addWhere $detail_where ORDER BY company_id DESC, od.status DESC
						 "; //ORDER BY company_id DESC
			
		if($_order_info[$i][oid] == "201009151408-7669"){
			//echo $sql;
		}
		if($i == 0){
			//echo nl2br($sql);
		//	exit;
		}else{
			//echo nl2br($sql)."<br><br>";
		}

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
			/*
			$sql = "SELECT o.oid, od.od_ix, od.pname, od.regdate, od.psprice, od.pcnt, od.ptprice,od.commission, uid,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no,
						(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_totalprice,
						(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as company_total,
						(select delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and o.oid = '".$_order_info[$i][oid]."' and od.company_id ='".$admininfo[company_id]."' AND od.product_type NOT IN (".implode(',',$sns_product_type).")
						$addWhere
						 "; //ORDER BY company_id DESC
			*/
			$sql = "SELECT o.oid, o.delivery_box_no, od.od_ix,od.pname, od.option_text, od.regdate, od.psprice, od.coprice, od.pcnt, od.ptprice,od.commission, uid,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price,o.delivery_type, com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,
						tid, od.status, method, total_price, UNIX_TIMESTAMP(date) AS date,receipt_y, od.company_name, od.com_phone, od.represent_mobile, od.company_id,od.quick, od.invoice_no,
						(select IFNULL(delivery_price,'') as delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id limit 1) as delivery_totalprice,
						(select count(*) as company_total from ".TBL_SHOP_ORDER_DETAIL."
							where o.oid = oid and od.company_id = company_id
							and od.status = status
							and product_type NOT IN (".implode(',',$sns_product_type).")
							group by company_id  limit 1) as company_total,
						(select IFNULL(delivery_pay_type,'') as delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id  limit 1) as delivery_pay_type
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and od.sc_ix = '".$_order_info[$i][sc_ix]."' and od.floor = '".$_order_info[$i][floor]."' and od.line = '".$_order_info[$i][line]."' and od.no = '".$_order_info[$i][no]."'  and od.company_id ='".$admininfo[company_id]."' and od.product_type NOT IN (".implode(',',$sns_product_type).")
						$addWhere $detail_where ORDER BY company_id DESC, od.status DESC
						 "; //ORDER BY company_id DESC

		//,(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id) as company_total
		}

		$ddb->query($sql);
		//echo $sql;

		$od_count = $ddb->total;

		$status = getOrderStatus($_order_info[$i][status]);

		$psum = number_format($ddb->dt[total_price]);

	$bcompany_id = '';
	for($j=0;$j < $ddb->total;$j++){
		$ddb->fetch($j);

		if ($ddb->dt[method] == ORDER_METHOD_CARD)
		{
			if($ddb->dt[bank] == ""){
				$method = "카드결제";
			}else{
				$method = $_order_info[$i][bank];
			}
			$receipt_y = "카드결제";
		}elseif($ddb->dt[method] == ORDER_METHOD_BANK){
			$method = "무통장입금";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}elseif($ddb->dt[method] == ORDER_METHOD_PHONE){
			$method = "전화결제";
		}elseif($ddb->dt[method] == ORDER_METHOD_AFTER){
			$method = "후불결제";
		}elseif($ddb->dt[method] == ORDER_METHOD_VBANK){
			$method = "가상계좌";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}elseif($ddb->dt[method] == ORDER_METHOD_ICHE){
			$method = "실시간계좌이체";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}elseif($ddb->dt[method] == ORDER_METHOD_ASCROW){
			$method = "가상계좌[에스크로]";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}elseif($ddb->dt[method] == ORDER_METHOD_SAVEPRICE){
			$method = "예치금결제";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}elseif($ddb->dt[method] == ORDER_METHOD_NOPAY){
			$method = "무료결제";
			if($ddb->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else if($ddb->dt[receipt_y] == "N"){
				$receipt_y = "미발행";
			}
		}

		if($ddb->dt[delivery_pay_type] == "1"){
			$delivery_pay_type = "선불";
		}elseif($ddb->dt[delivery_pay_type] == "2"){
			$delivery_pay_type = "착불";
		}else{
			$delivery_pay_type = "무료";
		}

		if($ddb->dt[use_reserve_price]>0) {
			$use_reserve_price="<span style='font-weight:100;'>적립금 사용: ".$currency_display[$admin_config["currency_unit"]]["front"]."".$ddb->dt[use_reserve_price]." ".$currency_display[$admin_config["currency_unit"]]["back"]."</span>";
		} else {
			$use_reserve_price="";
		}

		$one_status = getOrderStatus($ddb->dt[status],$ddb->dt[method])."<input type='hidden' id='od_status_".str_replace("-","",$ddb->dt[oid])."' value='".$ddb->dt[status]."'>";

		if($ddb->dt[gift] != ""){
			$od_count_plus = 0;
		}else{
			$od_count_plus = 0;
		}

		switch($ddb->dt["delivery_type"]) {
			case "C" : $delivery_type_txt="<font color='blue'>고객배송대행</font>";
			break;
			case "A" : $delivery_type_txt="완사입배송";
			break;
			case "D" : $delivery_type_txt="<font color='red'>즉시배송</font>";
			break;
			default : $delivery_type_txt="완사입배송";
			break;
		}


		//$Contents .= "<tr ".($ddb->dt[oid] != $b_oid  ? "style='background-color:#efefef'":"")." height=28 >";// kbk
		if($b_sc_ix != $_order_info[$i][sc_ix] || $b_floor != $_order_info[$i][floor] || $b_line != $_order_info[$i][line] || $b_noe != $_order_info[$i][no]){
			if($i != 0){
				$Contents .= "<tr height=28 ><td colspan=8></td><td align=center> ".number_format($coprice_sum)." 원</td><td align=center> ".number_format($pcnt_sum)." 건</td><td align=center> ".number_format($buying_fee_sum)." 원</td><td colspan=2></td></tr>";
				$coprice_sum = 0;
				$pcnt_sum = 0;
				$buying_fee_sum = 0;
			}
		}

		$Contents .= "<tr height=28 >";
		if($b_sc_ix != $_order_info[$i][sc_ix] || $b_floor != $_order_info[$i][floor] || $b_line != $_order_info[$i][line] || $b_noe != $_order_info[$i][no]){

		$Contents .= "<td class='list_box_td list_bg_gray' align=center title='".$_order_info[$i][group_cnt]."' rowspan='".$_order_info[$i][group_cnt]."'> ".$_shopping_center_info[$_order_info[$i][sc_ix]]."</td>";
		$Contents .= "<td class='list_box_td' align=center rowspan='".$_order_info[$i][group_cnt]."'>".$_floor_info[$_order_info[$i][floor]]."</td>";
		$Contents .= "<td class='list_box_td list_bg_gray' align=center rowspan='".$_order_info[$i][group_cnt]."'>".$_line_info_english[$_order_info[$i][line]]."".$_line_info_korea[$_order_info[$i][line]]."</td>";
		$Contents .= "<td class='list_box_td' align=center rowspan='".$_order_info[$i][group_cnt]."'>".$_order_info[$i][no]." 호</td>";
		$Contents .= "<td class='list_box_td list_bg_gray' align=center rowspan='".$_order_info[$i][group_cnt]."' style='line-height:140%;padding:5px;' nowrap>".($ddb->dt[company_name] ? $ddb->dt[company_name]:"-")."<br>".$ddb->dt[com_phone]."<br><a href='?company_id=".$ddb->dt[company_id]."&mmode=print&".$QUERY_STRING."' ><img src='../images/".$admininfo["language"]."/btn_print.gif' align=absmiddle border=0 style='cursor:pointer;' /></a></td>";
		}
		//$od_ix_str .= "<input type='hidden' name='od_ix[]' id='od_ix' value='".$ddb->dt[od_ix]."'  >";
		//if($ddb->dt[oid] != $b_oid){

			/*
			if($pre_type != ORDER_STATUS_EXCHANGE_APPLY){
			$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count+$od_count_plus)."'":"")." class='list_box_td' nowrap align='center'>
						  <input type=checkbox name='oid[]' id='oid' value='".$ddb->dt[oid]."' ".($ddb->dt[status] == "AC" ? "disabled":"")." >
						  <input type=hidden name='bstatus[".$ddb->dt[oid]."]' value='".$ddb->dt[status]."'>
						  <input type='hidden' id='od_status_".str_replace("-","",$ddb->dt[oid])."'>
						  </td>";
			}
			*/
			//$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count)."'":"")." class='list_box_td' align=center></td>";
			$Contents .= "<td  class='list_box_td' style='line-height:140%' align=center>
						  ".$ddb->dt[regdate]."<br>
						  <a href=\"orders.read.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\" style='color:#007DB7;font-weight:bold;' class='small'>".$ddb->dt[oid]."</a>".($ddb->dt[delivery_box_no] ? "<b style='color:red;'>-".$ddb->dt[delivery_box_no]."</b>":"")."<br>".$delivery_type_txt."
						  </td>";
			$Contents .= "<td style='line-height:140%' align=center class='list_box_td list_bg_gray'>
						  ".($ddb->dt[uid] != "" ? "<a href=\"javascript:PopSWindow('../member/member_view.php?code=".$ddb->dt[uid]."',950,500,'member_info')\" >".$ddb->dt[bname]."</a>":$ddb->dt[bname])."<span class='small'>(".$ddb->dt[mem_group].")</span> <br> 수취인 : ".$ddb->dt[rname]."<br>
						  </td>";
		//}
			$Contents .= "<td class='list_box_td' style='padding-left:10px'>
							<TABLE>
								<TR>
									<TD>
									<a href='../product/goods_input.php?id=".$ddb->dt[pid]."' target=_blank><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $ddb->dt[pid], "c")."'  width=50></a>
									</TD>
									<td width='5'></td>
									<TD style='line-height:140%;text-align:left;' nowrap>";
			if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
				$Contents .= "<a href=\"javascript:PopSWindow('../seller/company.add.php?company_id=".$ddb->dt[company_id]."&mmode=pop',960,600,'brand')\"><b >".($ddb->dt[company_name] ? $ddb->dt[company_name]:"-")."</b></a><br>";
			}
			$Contents .= cut_str($ddb->dt[pname],30)."<b><br>".$ddb->dt[option_text]."</b><!--b>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ddb->dt[psprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </b-->
									</TD>
								</TR>
							</TABLE>
						</td>
						<td class='list_box_td point' align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ddb->dt[coprice])."".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
						<td class='list_box_td point' align=center>".number_format($ddb->dt[pcnt])."</td>
						<td class='list_box_td point' align=center>".number_format($ddb->dt[coprice]*$ddb->dt[pcnt])."</td>
						<!--td class='list_box_td list_bg_gray' align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ddb->dt[ptprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
						</td-->
						";
	

			$Contents .="<td class='list_box_td point' align='center' nowrap>".$one_status;
			if($pre_type == ORDER_STATUS_INCOM_COMPLETE  || $pre_type == ORDER_STATUS_WAREHOUSING_STANDYBY){
				if($list_type=="vb" || $list_type=="scc" || $list_type=="sc") $checked_text="";
				else $checked_text="checked";
				$Contents .="<input type=checkbox name='od_ix[]' class='od_ix_".$ddb->dt[company_id]."' value='".$ddb->dt[od_ix]."' ".$checked_text."> ";
			}
			$Contents .="</td>";

if($pre_type == ORDER_STATUS_DELIVERY_ING || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
	$Contents .= "
		<td align='center' class='list_box_td' nowrap>".deliveryCompanyList($ddb->dt[quick],"text")."</td>
		<td align='center' class='list_box_td' nowrap><a href=\"javascript:searchGoodsFlow('".$ddb->dt[quick]."', '".str_replace("-","",$ddb->dt[invoice_no])."')\">".$ddb->dt[invoice_no]."</a></td>";
}
if($pre_type == ORDER_STATUS_INCOM_COMPLETE || $pre_type == ORDER_STATUS_DELIVERY_ING || $pre_type == ORDER_STATUS_DELIVERY_COMPLETE || $pre_type == ORDER_STATUS_WAREHOUSING_STANDYBY){
		if($bcompany_id != $ddb->dt[company_id] || $b_status != $ddb->dt[status]){
			$Contents .= "<td  class='list_box_td' align='center' style='padding:10px 0px;' rowspan='".$_order_info[$i][group_cnt]."' nowrap>";
				if($pre_type == ORDER_STATUS_INCOM_COMPLETE){
					$Contents .=  "<form name=change_status_frm_".md5($ddb->dt[oid])."_".$ddb->dt[company_id]." method=post action='orders.goods_list.act.php' onSubmit='return orderStatusUpdate(this)'  target='act'>
									<input type='hidden' name='oid' value='".$ddb->dt[oid]."'>
									<input type='hidden' name='oid' value='".md5($ddb->dt[oid])."'>
									<input type='hidden' name='act' value='delivery_update'>
									<input type='hidden' name='od_ix_str' value=''>
									<input type='hidden' name='pre_type' value='$pre_type'>
								<table>
								<tr>
									<td>".deliveryCompanyList("","SelectbySeller","",$ddb->dt[company_id])."</td>
									<td><input type='text' name='deliverycode' class=textbox   size=15 value='".$db2->dt[deliverycode]."' validation=true title='송장번호'></td>
									<td><input type=image src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='margin:1px 0px;cursor:hand;'></td>
								</tr>
								</table>
								</form>";


								/*
								if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
									$Contents .= "<a href=\"orders.edit.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:3px;'><!--btc_modify.gif--></a> ";
								}else{
									$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:3px;'><!--btc_modify.gif--></a> ";
								}
								*/
				}else if($pre_type == ORDER_STATUS_WAREHOUSING_STANDYBY){
						$Contents .=  "<form name='change_status_frm_".$_order_info[$i][sc_ix]."_".$_order_info[$i][floor]."_".$_order_info[$i][line]."_".$_order_info[$i][no]."' method=post action='orders.goods_list.act.php' target='act'><!-- target='act'-->
									<input type='hidden' name='oid' value='".$ddb->dt[oid]."'>
									<input type='hidden' name='company_id' value='".$ddb->dt[company_id]."'>
									<input type='hidden' name='act' value='buying_update_bysc'>
									<input type='hidden' name='od_ix_str' value=''>
									<input type='hidden' name='pre_type' value='$pre_type'>
									<input type='hidden' name='change_status' value=''>
								<table border=0 style='text-align:center;' align=center>
								<tr>
								<td  >";
								if($admininfo[admin_level] ==  9){
									$Contents .=  "<img src='../images/".$admininfo["language"]."/btn_soldout_cancel.gif' align=absmiddle onclick=\"orderBuyingIngSCUpdate(document.change_status_frm_".$_order_info[$i][sc_ix]."_".$_order_info[$i][floor]."_".$_order_info[$i][line]."_".$_order_info[$i][no].", '".ORDER_STATUS_SOLDOUT_CANCEL."')\" style='cursor:pointer;'>";
								}
								$Contents .= "</td>
								</tr>
								<tr>
								<td  >";
								if($admininfo[admin_level] ==  9){
									$Contents .=  "<img src='../images/".$admininfo["language"]."/btn_buying_complete.gif' align=absmiddle onclick=\"orderBuyingIngSCUpdate(document.change_status_frm_".$_order_info[$i][sc_ix]."_".$_order_info[$i][floor]."_".$_order_info[$i][line]."_".$_order_info[$i][no].", '".ORDER_STATUS_BUYING_COMPLETE."')\" style='cursor:pointer;'>";
								}
								$Contents .= "</td>
								</tr>
								<tr>
									<td><a href=\"orders.edit.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' align=absmiddle  style='margin:1px 0px;cursor:hand;'></a></td>
								</tr>
								<tr>
									<td><a href=\"javascript:orderSendSMS('".$ddb->dt[company_id]."','".$pre_type."')\"><img src='../images/".$admininfo["language"]."/btn_sms_orderinfo.gif' align=absmiddle  style='margin:1px 0px;cursor:hand;'></a></td>
								</tr>
								
								</table>
								</form>";
				}else if($pre_type == ORDER_STATUS_DELIVERY_ING){
						$Contents .=  "<img src='../images/".$admininfo["language"]."/btn_delivery_complete.gif' align=absmiddle onclick=\"ChangeStatus('status_update', '".$ddb->dt[oid]."', '".$ddb->dt[od_ix]."','".$pre_type."','".ORDER_STATUS_DELIVERY_COMPLETE."')\" style='cursor:hand;'>";

				}else if($pre_type == ORDER_STATUS_DELIVERY_COMPLETE){
							if($ddb->dt[oid] != $b_oid){
								//$Contents .=  "<td  class='list_box_td' align='center' style='padding:10px 0px;' ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count)."'":"")." nowrap>";
								$Contents .=  "<a href=\"orders.edit.php?oid=".$ddb->dt[oid]."&pid=".$ddb->dt[pid]."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle  style='cursor:hand;'></a>";
								//$Contents .= "</td>";
							}

				}else{
						if($ddb->dt[oid] != $b_oid){
							$Contents .= "<td ".($ddb->dt[oid] != $b_oid ? "rowspan='".($od_count)."'":"")." class='list_box_td' align='center'  nowrap>
											<!--img src='../images/".$admininfo["language"]."/btn_taxbill_view.gif' align=absmiddle onclick=\"PoPWindow('taxbill.php?uid=".$ddb->dt[uid]."&oid=".$ddb->dt[oid]."',680,800,'sendsms')\" style='cursor:hand;'-->";

							if($pre_type == "CA"){
								$Contents .=  "<img src='../images/".$admininfo["language"]."/btn_cancel_confirm.gif' align=absmiddle onclick=\"ChangeStatus('status_update', '".$ddb->dt[oid]."', '".$pre_type."')\" style='cursor:hand;'>";

							}else{

							}
				}
			}

			$Contents .= "</td>";
		}


}
		$Contents .= "</tr>";




/*
		if(($od_count-1) == $j && $admininfo[admin_level] == 9){
			$Contents .= "<tr >
							<td class='list_box_td' style='background-color:#efefef;height:30px;font-weight:bold;padding:0 0 0 10px' class=blue colspan=4>
							 <span class='small blue'>".getDeliveryPrice2($ddb->dt[oid])."</span>
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
		$b_sc_ix = $_order_info[$i][sc_ix];
		$b_floor = $_order_info[$i][floor];
		$b_line = $_order_info[$i][line];
		$b_noe = $_order_info[$i][no];

		$coprice_sum += $ddb->dt[coprice];
		$pcnt_sum += $ddb->dt[pcnt];
		$buying_fee_sum += $ddb->dt[coprice]*$ddb->dt[pcnt];
		
		$coprice_sum_sum += $ddb->dt[coprice]*$ddb->dt[pcnt];

		$b_oid = $ddb->dt[oid];
		$b_status = $ddb->dt[status];
		$bcompany_id = $ddb->dt[company_id];
		$total_pcnt_sum += $ddb->dt[pcnt];
		//$buying_fee_sum += $ddb->dt[coprice]*$ddb->dt[pcnt];
	}
	
	//$Contents .= "<tr height=3><td colspan=10 bgcolor='#DDDDDD'></td></tr>";
	}
	$Contents = str_replace("{total_pcnt_sum}",number_format($total_pcnt_sum)." 건", $Contents);
	$Contents = str_replace("{buying_fee_sum}",number_format($buying_fee_sum)." 원", $Contents);
}else{
	$Contents = str_replace("{total_pcnt_sum}",number_format($total_pcnt_sum)." 건", $Contents);
	$Contents = str_replace("{buying_fee_sum}",number_format($buying_fee_sum)." 원", $Contents);
$Contents .= "<tr height=50><td colspan=13 align=center>조회된 결과가 없습니다.</td></tr>
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
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){

if($pre_type == ""){
	$Contents .= "선택된 항목을 ";
	if($admininfo[admin_level] == 9){

	$Contents .= "
			<select name='status' onchange=\"if(this.value == '".ORDER_STATUS_DELIVERY_ING."'){document.getElementById('invoice').style.display = 'inline'}else{document.getElementById('invoice').style.display = 'none'}\">
					<option value='".ORDER_STATUS_INCOM_READY."' >".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option>
					<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
					<!--option value='".ORDER_STATUS_ADVANCE_ING."' >".getOrderStatus(ORDER_STATUS_ADVANCE_ING)."</option-->
					<option value='".ORDER_STATUS_DELIVERY_READY."' >".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option>
					<!--option value='".ORDER_STATUS_DELIVERY_ING."' >".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option-->
					<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' >".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
					<!--option value='".ORDER_STATUS_EXCHANGE_APPLY."' >".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</option>
					<option value='".ORDER_STATUS_RETURN_APPLY."' >".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</option>
					<option value='".ORDER_STATUS_RETURN_COMPLETE."' >".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</option-->
					<option value='".ORDER_STATUS_CANCEL_COMPLETE."' >".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>
				</select>";
	}else if($admininfo[admin_level] == 8){
	$Contents .= "<select name='status' onchange=\"if(this.value == '".ORDER_STATUS_DELIVERY_ING."'){document.getElementById('invoice').style.display = 'inline'}else{document.getElementById('invoice').style.display = 'none'}\">
					<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
					<!--option value='".ORDER_STATUS_DELIVERY_READY."' >".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option-->
				</select>";
	}
	$Contents .= "로 상태변경
	<div id='invoice' style='display:none'>
		".deliveryCompanyList($db3->dt[quick],"select")." <div id='deliverycode' style='display:inline'><input type='text' name='deliverycode'   size=15 value='".$db3->dt[invoice_no]."'> <!--* 좌측에 송장번호를 입력해주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." </div>
	</div>
	<input type=image src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle>";

}else if($pre_type == "IR"){
	$Contents .= "선택된 항목을 ";
	$Contents .= "<select name='status' onchange=\"if(this.value == '".ORDER_STATUS_DELIVERY_ING."'){document.getElementById('invoice').style.display = 'inline'}else{document.getElementById('invoice').style.display = 'none'}\">
					<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
					<option value='".ORDER_STATUS_CANCEL_COMPLETE."' >".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>
				</select>";
	$Contents .= " 로 상태변경

	<input type=image src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle>";
}else if($pre_type == "CA"){
	$Contents .= "선택된 항목을 일괄 취소완료로 상태변경 <input type=hidden name='status' value='".ORDER_STATUS_CANCEL_COMPLETE."'> <input type=image src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle> ";
}

}
$Contents .= "

    </td>
  </tr>
  <tr height=40>
    <td colspan='10' align='center'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
  </tr>
</table>
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문번호를 클릭하시면 주문에 대한 상세 정보를 보실수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문상태를 변경하시려면 수정버튼을 누르세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문상태를 빠르게 변경하시려면 변경하시고자 하는 주문 선택후 아래 변경하고자 하는 상태를 선택하신후 수정버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>주문총액</b>은 <u>배송비 미포함 금액</u>입니다.</td></tr>
</table>
";*/
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

//$Contents .= HelpBox("빠른송장입력", $help_text);
$P = new LayOut();
$P->strLeftMenu = order_menu();
$P->OnloadFunction = "onLoad('$sDate','$eDate');ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='orders.goods_list.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n";
$P->Navigation = "배송관리 > $title_str";
$P->title = $title_str;
$P->strContents = $Contents;
echo $P->PrintLayOut();


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