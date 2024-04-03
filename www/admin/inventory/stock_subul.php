<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include("inventory.lib.php");
include("../product/goods_input.lib.php");

//auth(8);


$vdate = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()-84600);
$voneweekago = date("Ymd", time()-84600*7);
$vtwoweekago = date("Ymd", time()-84600*14);
$vfourweekago = date("Ymd", time()-84600*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));


/* 
	날짜 검색을 안해도 무조건 1일 부터 현재날짜까지 검색조건으로 되어 있어서 주석처리함 2013-09-13 이학봉
	무조껀 날자 검색 되도록 처리 20131016 Hong
*/
if ($sdate == ""){
	//$sdate = date("Ymd", time()-84600*(date("d")-1));
	$sdate = date("Ymd");
	$edate = date("Ymd");
}

$sdate_yday = date("Ymd",mktime(0,0,0,substr($sdate,4,2),substr($sdate,6,2),substr($sdate,0,4))-60*60*24);


if($_COOKIE[inventory_goods_max_limit]){
	$max = $_COOKIE[inventory_goods_max_limit]; //페이지당 갯수
}else{
	$max = 20;
}


if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;
$db2 = new Database;
$cdb = new Database;

if($gid != ""){
	$fetch=true;
}

if($admininfo[admin_level] == 9){
	if($admininfo[mem_type] == "MD"){
		$a_where = " and g.admin in (".getMySellerList($admininfo[charger_ix]).") ";
	}
}else{
	$a_where = " and g.admin ='".$admininfo[company_id]."' ";
}


if(($subul_type == "date" && $fetch)||$subul_type == ""){

	$g_where = " where g.gid=gu.gid ";
	$hi_where = " and h_type not in ('IW')";
	
	if($gid != ""){
		$sql = "select * from inventory_goods where gid = '$gid' ";
		$db2->query($sql);
		$db2->fetch();
		$gname = $db2->dt[gname];

		$g_where .= " and g.gid = '".$gid."' ";
		$hi_where .= " and hd.gid = '".$gid."' ";
	}

	if($sdate !="" && $edate != ""){
		$hi_where .= " and h.vdate between '$sdate' and '$edate' ";
	}

	if($search_texts ==""){
		$m_where = " where in_amount !='0' OR out_amount  !='0' OR etc_out_amount !='0' ";
	}

	if($ci_ix!=''){
		$hi_where =" and h.ci_ix = '".$ci_ix."' ";
	}

	if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
		//다중검색 시작 2014-04-10 이학봉
		if($search_text != ""){
			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$g_where .= "and ( ";
				$count_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$g_where .= $search_type." = '".trim($search_array[$i])."'";
							$count_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$g_where .= $search_type." = '".trim($search_array[$i])."' or ";
							$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$g_where .= ")";
				$count_where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n
				$search_array = explode("\n",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$g_where .= "and ( ";
				$count_where .= "and ( ";

				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$g_where .= $search_type." = '".trim($search_array[$i])."'";
							$count_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$g_where .= $search_type." = '".trim($search_array[$i])."' or ";
							$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$g_where .= ")";
				$count_where .= ")";
			}else{
				$g_where .= " and ".$search_type." = '".trim($search_text)."'";
				$count_where .= " and ".$search_type." = '".trim($search_text)."'";
			}
		}
	}else{
		if($search_type !="" && $search_text != ""){
			$g_where .= "and ".$search_type." LIKE '%".$search_text."%' ";
		}
	}

	if($_GET["pi_ix"] != ""){
		$hi_where .= " and h.pi_ix = '".$_GET["pi_ix"]."' ";
	}

	if($_GET["company_id"] != ""){
		$hi_where .= " and h.company_id = '".$_GET["company_id"]."' ";
	}

	if($subul_type == ""){
		$group_by = "group by goods.gid, goods.unit ";
		$column=" goods.gid, goods.unit, goods.gname ,goods.cid,goods.barcode";
	}elseif($subul_type == "date"){
		$group_by = "group by goods.gid, goods.unit, history.vdate ";
		$column=" goods.gid, goods.unit, goods.gname, history.vdate ,goods.cid,goods.barcode";
	}else{
		
	}

	$sql = "select * from (
		select 
				$column , 
				ifnull(sum(case when h_div = '1' then amount else 0 end),0) in_amount, 
				ifnull(sum(case when h_div = '2' and h_type = '01' then amount else 0 end),0) out_amount, 
				ifnull(sum(case when h_div = '2' and h_type != '01' then amount else 0 end),0) etc_out_amount
			from  (
				select 
					g.gid,gu.unit,g.gname,g.cid,gu.barcode
				from 
				inventory_goods g ,
				inventory_goods_unit gu
				$g_where $a_where
			) goods
			left join 
			(
				select 
					hd.gid,hd.unit,h_div,h_type,amount,price,vdate
				from 
				inventory_history h,
				inventory_history_detail hd
				where  h.h_ix = hd.h_ix
				$hi_where
			) history
			on (goods.gid = history.gid and goods.unit = history.unit)
			$group_by
		) a
		$m_where
			";
	/*
			left join inventory_place_info ipi on ipi.pi_ix = h.pi_ix
			left join common_member_detail cmd on h.charger_ix = cmd.code
	*/
	//echo nl2br($sql); 	$where	$group_by

	if($mode=="search" || $fetch){
		$db2->query($sql);
		$db2->fetch();
		$total = $db2->total;
	}
	//$total = $db2->dt[total];
	//echo $total;
	 
	 $sql = "select * from (
		select 
				$column , 
				ifnull(sum(case when h_div = '1' then amount else 0 end),0) in_amount, 
				ifnull(sum(case when h_div = '1' then price else 0 end),0) in_price, 
				ifnull(sum(case when h_div = '2' and h_type = '01' then amount else 0 end),0) out_amount, 
				ifnull(sum(case when h_div = '2' and h_type = '01' then price else 0 end),0) out_price,
				ifnull(sum(case when h_div = '2' and h_type != '01' then amount else 0 end),0) etc_out_amount, 
				ifnull(sum(case when h_div = '2' and h_type != '01' then price else 0 end),0) etc_out_price
			from  (
				select 
					g.gid,gu.unit,g.gname,g.cid,gu.barcode
				from 
				inventory_goods g , inventory_goods_unit gu
				$g_where $a_where
			) goods
			left join 
			(
				select 
					hd.gid,hd.unit,h_div,h_type,amount,price,vdate
				from 
				inventory_history h,
				inventory_history_detail hd
				where  h.h_ix = hd.h_ix
				$hi_where
			) history
			on (goods.gid = history.gid and goods.unit = history.unit)
			$group_by
		) a
		$m_where";
		if($mode != "excel"){
			$sql .= "
			LIMIT $start, $max";
		}
		$sql .= "
			";


	//echo nl2br($sql);
	/*
			left join inventory_place_info ipi on ipi.pi_ix = h.pi_ix
			left join common_member_detail cmd on h.charger_ix = cmd.code
	*/

	if($mode=="search" || $mode=="excel"  || $fetch){
		$db->query($sql);
	}

}elseif($subul_type=="customer"||$subul_type=="brand"||$subul_type=="company"){

	$hi_where = " and h_type not in ('IW')";
	$c_where = "";
	
	if($subul_type=="customer"){
		if($detail_view=='1'){
			$group_by ="group by h.ci_ix,hd.gid,hd.unit";
		}else{
			$group_by ="group by h.ci_ix";
		}
	}elseif($subul_type=="brand"){
		if($detail_view=='1'){
			$group_by ="group by g.b_ix,hd.gid,hd.unit";
		}else{
			$group_by ="group by g.b_ix";
		}
	}elseif($subul_type=="company"){
		if($detail_view=='1'){
			$group_by ="group by g.c_ix,hd.gid,hd.unit";
		}else{
			$group_by ="group by g.c_ix";
		}
	}

	if($ci_ix!=''){
		$hi_where =" and h.ci_ix = '".$ci_ix."' ";
	}

	if($sdate !="" && $edate != ""){
		$hi_where .= " and h.vdate between '$sdate' and '$edate' ";
	}
	
	if(is_array($h_div)){
		for($i=0;$i < count($h_div);$i++){
			if($h_div[$i]){
				if($h_div_str == ""){
					$h_div_str .= "'".$h_div[$i]."'";
				}else{
					$h_div_str .= ", '".$h_div[$i]."' ";
				}
			}
		}

		if($h_div_str != ""){
			$hi_where .= "and h.h_div in ($h_div_str) ";
		}
	}else{
		if($h_div){
			$hi_where .= "and h.h_div = '$h_div' ";
		}
	}

	if($search_text != ""){
		if($search_type=="g.gname"){
			$hi_where .= " and ".$search_type." LIKE '%".$search_text."%' ";
		}else{
			$hi_where .= " and ".$search_type." = '".$search_text."' ";
		}
	}

	if($b_ix!=""){
		$hi_where .= " and g.b_ix = '".$b_ix."' ";
	}


	if($_GET["pi_ix"] != ""){
		$hi_where .= " and h.pi_ix = '".$_GET["pi_ix"]."' ";
	}

	if(is_array($sell_type)){
		$c_where .= " and (seller_type like '%1%' or seller_type like '%2%' or seller_type like '%3%' or seller_type like '%4%')";
	}else{
		if($sell_type){
			if($sell_type=='1'){
				$c_where .= " and (seller_type like '%1%' or seller_type like '%2%')";
			}elseif($sell_type=='2'){
				$c_where .= " and (seller_type like '%3%' or seller_type like '%4%')";
			}
		}
	}
	
	/*
	if($business_type !="1"){
		$c_where .= " and business_type in ('F','A') ";
	}
	*/

	if($c_where !=""){
		$hi_where .= " and h.ci_ix in (select company_id from common_company_detail where 1=1 ".$c_where." ) ";
	}
	

	if($subul_type=="customer"){
		$select_sql=",cmd.com_name";
		$left_join_sql=" left join common_company_detail cmd on (history.ci_ix = cmd.company_id)";
	}elseif($subul_type=="company"){
		$select_sql="";
		$left_join_sql="";
	}elseif($subul_type=="brand"){
		$select_sql=",b.brand_name";
		$left_join_sql=" left join shop_brand b on (history.b_ix = b.b_ix)";
	}
	
	if($mode!="etc_excel"){
		$sql="select 
				h.ci_ix,vdate
			from 
			inventory_history h,
			inventory_history_detail hd
			left join inventory_goods g on (hd.gid=g.gid)
			where  h.h_ix = hd.h_ix
			$hi_where
			$group_by  ";
		
		$db2->query($sql);
		$db2->fetch();
		$total = $db2->total;
	}

	$sql="select 
		history.*,cmd2.com_name as basic_com_name
		".$select_sql."
	from (
		select 
			h.ci_ix,vdate,g.gname,unit,hd.gid,hd.standard,g.b_ix,g.company,g.ci_ix as basic_ci_ix,
			ifnull(sum(case when h_div = '1' and h_type != '04' then amount else 0 end),0) as in_amount, 
			ifnull(sum(case when h_div = '1' and h_type != '04' then price else 0 end),0)/ifnull(sum(case when h_div = '1' and h_type != '04' then 1 else 0 end),0) as in_price,
			ifnull(sum(case when h_div = '1' and h_type = '04' then amount else 0 end),0) as return_amount, 
			ifnull(sum(case when h_div = '1' and h_type = '04' then price else 0 end),0)/ifnull(sum(case when h_div = '1' and h_type = '04' then 1 else 0 end),0) as return_price,
			ifnull(sum(case when h_div = '2' then amount else 0 end),0) as out_amount, 
			ifnull(sum(case when h_div = '2' then price else 0 end),0)/ifnull(sum(case when h_div = '1' and h_type != '04' then 1 else 0 end),0) as out_price
		from 
		inventory_history h,
		inventory_history_detail hd
		left join inventory_goods g on (hd.gid=g.gid)
		where  h.h_ix = hd.h_ix
		$hi_where $a_where
		$group_by 
		".($mode!="etc_excel" ? "limit $start,$max" : "")."
	) history
	left join common_company_detail cmd2 on (history.basic_ci_ix = cmd2.company_id)
	".$left_join_sql." ";

	$db->query($sql);

}

if($mode == "etc_excel"){

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

	if($detail_view=='1'){
		$inventory_excel->getActiveSheet(0)->mergeCells('A1:A2');
		$inventory_excel->getActiveSheet(0)->mergeCells('B1:B2');
		$inventory_excel->getActiveSheet(0)->mergeCells('C1:C2');
		$inventory_excel->getActiveSheet(0)->mergeCells('D1:D2');
		$inventory_excel->getActiveSheet(0)->mergeCells('E1:E2');
		$inventory_excel->getActiveSheet(0)->mergeCells('F1:F2');
		$inventory_excel->getActiveSheet(0)->mergeCells('G1:G2');
		$inventory_excel->getActiveSheet(0)->mergeCells('H1:H2');
		$inventory_excel->getActiveSheet(0)->mergeCells('I1:K1');
		$inventory_excel->getActiveSheet(0)->mergeCells('L1:P1');
		$inventory_excel->getActiveSheet(0)->mergeCells('Q1:Q2');

		$inventory_excel->getActiveSheet(0)->setCellValue('A' . 1, "순");
		if($subul_type == "customer"){
			$inventory_excel->getActiveSheet(0)->setCellValue('B' . 1, "거래처명");
		}elseif($subul_type == "brand"){
			$inventory_excel->getActiveSheet(0)->setCellValue('B' . 1, "브랜드명");
		}elseif($subul_type=="company"){
			$inventory_excel->getActiveSheet(0)->setCellValue('B' . 1, "제조사명");
		}
		$inventory_excel->getActiveSheet(0)->setCellValue('C' . 1, "품목코드");
		$inventory_excel->getActiveSheet(0)->setCellValue('D' . 1, "품목명");
		$inventory_excel->getActiveSheet(0)->setCellValue('E' . 1, "주거래처");
		$inventory_excel->getActiveSheet(0)->setCellValue('F' . 1, "단위");
		$inventory_excel->getActiveSheet(0)->setCellValue('G' . 1, "규격");
		$inventory_excel->getActiveSheet(0)->setCellValue('H' . 1, "이월재고");
		$inventory_excel->getActiveSheet(0)->setCellValue('I' . 1, "입고");
		$inventory_excel->getActiveSheet(0)->setCellValue('L' . 1, "출고");
		$inventory_excel->getActiveSheet(0)->setCellValue('Q' . 1, "재고");

		$inventory_excel->getActiveSheet(0)->setCellValue('I' . 2, "수량");
		$inventory_excel->getActiveSheet(0)->setCellValue('J' . 2, "단가");
		$inventory_excel->getActiveSheet(0)->setCellValue('K' . 2, "금액");
		$inventory_excel->getActiveSheet(0)->setCellValue('L' . 2, "수량");
		$inventory_excel->getActiveSheet(0)->setCellValue('M' . 2, "단가");
		$inventory_excel->getActiveSheet(0)->setCellValue('N' . 2, "반품");
		$inventory_excel->getActiveSheet(0)->setCellValue('O' . 2, "단가");
		$inventory_excel->getActiveSheet(0)->setCellValue('P' . 2, "합계");

	}else{

		$inventory_excel->getActiveSheet(0)->mergeCells('A1:A2');
		$inventory_excel->getActiveSheet(0)->mergeCells('B1:B2');
		$inventory_excel->getActiveSheet(0)->mergeCells('C1:E1');
		$inventory_excel->getActiveSheet(0)->mergeCells('F1:J1');

		$inventory_excel->getActiveSheet(0)->setCellValue('A' . 1, "순");
		if($subul_type == "customer"){
			$inventory_excel->getActiveSheet(0)->setCellValue('B' . 1, "거래처명");
		}elseif($subul_type == "brand"){
			$inventory_excel->getActiveSheet(0)->setCellValue('B' . 1, "브랜드명");
		}elseif($subul_type=="company"){
			$inventory_excel->getActiveSheet(0)->setCellValue('B' . 1, "제조사명");
		}
		$inventory_excel->getActiveSheet(0)->setCellValue('C' . 1, "입고");
		$inventory_excel->getActiveSheet(0)->setCellValue('F' . 1, "출고");

		$inventory_excel->getActiveSheet(0)->setCellValue('C' . 2, "수량");
		$inventory_excel->getActiveSheet(0)->setCellValue('D' . 2, "단가");
		$inventory_excel->getActiveSheet(0)->setCellValue('E' . 2, "금액");
		$inventory_excel->getActiveSheet(0)->setCellValue('F' . 2, "수량");
		$inventory_excel->getActiveSheet(0)->setCellValue('G' . 2, "단가");
		$inventory_excel->getActiveSheet(0)->setCellValue('H' . 2, "반품");
		$inventory_excel->getActiveSheet(0)->setCellValue('I' . 2, "단가");
		$inventory_excel->getActiveSheet(0)->setCellValue('J' . 2, "합계");
	}

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$total_in_price = $db->dt[in_amount]*$db->dt[in_price];
		$total_out_price = $db->dt[out_amount]*$db->dt[out_price];
		$total_return_price = $db->dt[return_amount]*$db->dt[return_price];

		$sum_in_amount += $db->dt[in_amount];
		$sum_in_price += $db->dt[in_price];
		$sum_input_total_price += $total_in_price;

		$sum_out_amount += $db->dt[out_amount];
		$sum_out_price += $db->dt[out_price];
		$sum_delivery_total_price += $total_out_price;

		$sum_return_amount += $db->dt[return_amount];
		$sum_return_price += $db->dt[return_price];
		$sum_return_total_price += $total_return_price;

		if($subul_type == "customer"){
			$name = $db->dt[com_name];
		}elseif($subul_type == "brand"){
			$name = $db->dt[brand_name];
		}elseif($subul_type == "company"){
			$name = $db->dt[company];
		}
		
		$basic_stock = get_basic_stock($db->dt[vdate],$db->dt[gid],$db->dt[unit]);

		if($detail_view=='1'){

			$inventory_excel->getActiveSheet()->setCellValue('A' . ($i + 3), $i+1);
			$inventory_excel->getActiveSheet()->setCellValue('B' . ($i + 3), $name);
			$inventory_excel->getActiveSheet()->setCellValue('C' . ($i + 3), $db->dt[gid]);		
			$inventory_excel->getActiveSheet()->setCellValue('E' . ($i + 3), $db->dt[gname]);
			$inventory_excel->getActiveSheet()->setCellValue('E' . ($i + 3), $db->dt[basic_com_name]);
			$inventory_excel->getActiveSheet()->setCellValue('F' . ($i + 3), getUnit($db->dt[unit], "basic_unit","","text"));
			$inventory_excel->getActiveSheet()->setCellValue('G' . ($i + 3), $db->dt[standard]);
			$inventory_excel->getActiveSheet()->setCellValue('H' . ($i + 3), $basic_stock);
			$inventory_excel->getActiveSheet()->setCellValue('I' . ($i + 3), $db->dt[in_amount]);
			$inventory_excel->getActiveSheet()->setCellValue('J' . ($i + 3), $db->dt[in_price]);
			$inventory_excel->getActiveSheet()->setCellValue('K' . ($i + 3), $total_in_price);
			$inventory_excel->getActiveSheet()->setCellValue('L' . ($i + 3), $db->dt[out_amount]);
			$inventory_excel->getActiveSheet()->setCellValue('M' . ($i + 3), $db->dt[out_price]);
			$inventory_excel->getActiveSheet()->setCellValue('N' . ($i + 3), $db->dt[return_amount]);
			$inventory_excel->getActiveSheet()->setCellValue('O' . ($i + 3), $db->dt[return_price]);
			$inventory_excel->getActiveSheet()->setCellValue('P' . ($i + 3), $total_out_price+$total_return_price);
			$inventory_excel->getActiveSheet()->setCellValue('Q' . ($i + 3), $basic_stock + $db->dt[in_amount] - $db->dt[out_amount]);

		}else{

			$inventory_excel->getActiveSheet()->setCellValue('A' . ($i + 3), $i+1);
			$inventory_excel->getActiveSheet()->setCellValue('B' . ($i + 3), $name);
			$inventory_excel->getActiveSheet()->setCellValue('C' . ($i + 3), $db->dt[in_amount]);
			$inventory_excel->getActiveSheet()->setCellValue('D' . ($i + 3), $db->dt[in_price]);
			$inventory_excel->getActiveSheet()->setCellValue('E' . ($i + 3), $total_in_price);
			$inventory_excel->getActiveSheet()->setCellValue('F' . ($i + 3), $db->dt[out_amount]);
			$inventory_excel->getActiveSheet()->setCellValue('G' . ($i + 3), $db->dt[out_price]);
			$inventory_excel->getActiveSheet()->setCellValue('H' . ($i + 3), $db->dt[return_amount]);
			$inventory_excel->getActiveSheet()->setCellValue('I' . ($i + 3), $db->dt[return_price]);
			$inventory_excel->getActiveSheet()->setCellValue('J' . ($i + 3), $total_out_price+$total_return_price);
		}
	}

	// 첫번째 시트 선택
	$inventory_excel->setActiveSheetIndex(0);

	// 너비조정
	$col = 'A';
	
	$inventory_excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$inventory_excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$inventory_excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$inventory_excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
	

	header('Content-Type: application/vnd.ms-excel');

	if($subul_type == "customer"){
		header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","거래처별 현황.csv").'"');
	}elseif($subul_type == "brand"){
		header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","브랜드 현황.csv").'"');
	}elseif($subul_type == "company"){
		header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","제조사별 현황.csv").'"');
	}
	header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'CSV');
	$objWriter->setUseBOM(true);
	$objWriter->save('php://output');

	exit;
}


if($mode == "excel"){

	/*
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
*/
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
	/*
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
		$col++;

		//xlsWriteLabel(0,$j,$columsinfo[$value][title]);
		//$j++;
	}
	*/
	
	$inventory_excel->getActiveSheet(0)->mergeCells('A1:A2');
	$inventory_excel->getActiveSheet(0)->mergeCells('B1:B2');
	$inventory_excel->getActiveSheet(0)->mergeCells('C1:C2');
	$inventory_excel->getActiveSheet(0)->mergeCells('D1:D2');
	$inventory_excel->getActiveSheet(0)->mergeCells('E1:E2');
	$inventory_excel->getActiveSheet(0)->mergeCells('F1:H1');
	$inventory_excel->getActiveSheet(0)->mergeCells('I1:K1');
	$inventory_excel->getActiveSheet(0)->mergeCells('L1:N1');
	$inventory_excel->getActiveSheet(0)->setCellValue('A' . 1, "순");
	$inventory_excel->getActiveSheet(0)->setCellValue('B' . 1, "품목코드");
	$inventory_excel->getActiveSheet(0)->setCellValue('C' . 1, "분류");
	$inventory_excel->getActiveSheet(0)->setCellValue('D' . 1, "품목명");
	$inventory_excel->getActiveSheet(0)->setCellValue('E' . 1, "단위");
	$inventory_excel->getActiveSheet(0)->setCellValue('F' . 1, "입고");
	$inventory_excel->getActiveSheet(0)->setCellValue('I' . 1, "출고");
	$inventory_excel->getActiveSheet(0)->setCellValue('L' . 1, "기타출고");
	$inventory_excel->getActiveSheet(0)->setCellValue('O' . 1, "재고");


	$inventory_excel->getActiveSheet(0)->setCellValue('F' . 2, "수량");
	$inventory_excel->getActiveSheet(0)->setCellValue('G' . 2, "단가");
	$inventory_excel->getActiveSheet(0)->setCellValue('H' . 2, "금액");
	$inventory_excel->getActiveSheet(0)->setCellValue('I' . 2, "수량");
	$inventory_excel->getActiveSheet(0)->setCellValue('J' . 2, "단가");
	$inventory_excel->getActiveSheet(0)->setCellValue('K' . 2, "금액");
	$inventory_excel->getActiveSheet(0)->setCellValue('L' . 2, "수량");
	$inventory_excel->getActiveSheet(0)->setCellValue('M' . 2, "단가");
	$inventory_excel->getActiveSheet(0)->setCellValue('N' . 2, "금액");
	$inventory_excel->getActiveSheet(0)->setCellValue('O' . 2, "수량");
	

	$before_pid = "";
/*
	if($info_type == "warehouse" || $info_type == "category"){
		for ($i = 0; $i < count($goods_infos); $i++)
		{
			$stock_assets_sum += $goods_infos[$i][stock_assets];
			$stock_sum += $goods_infos[$i][stock];
			$stock_assets_total += $goods_infos[$i][stock_assets];
			$order_cnt_sum += $goods_infos[$i][order_cnt];

			
		}
	}
*/	
	for ($i = 0; $i < $db->total; $i++)
	{
		 /*
		$j="A";
		foreach($check_colums as $key => $value){
			if($key == "item_account"){
				$value_str = $ITEM_ACCOUNT[$goods_infos[$i][item_account]];
			}else if($key == "ci_ix"){
				$value_str = strip_tags(SelectSupplyCompany($_SESSION["admininfo"]["company_id"], $goods_infos[$i][ci_ix],'ci_ix','text','false'));
			}else if($key == "pi_ix"){
				$value_str = $goods_infos[$i][place_name]; //strip_tags(SelectSupplyCompany($goods_infos[$i][ci_ix],'ci_ix','text','false'));
			}else if($key == "barcode"){
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
				$value_str =  $goods_infos[$i][stock]-$goods_infos[$i][sell_ing_cnt]+$goods_infos[$i][order_ing_cnt];
			}else if($key == "cname"){
				$value_str = getIventoryCategoryPathByAdmin($goods_infos[$i][cid], 4);
				
			}else{
				$value_str = $goods_infos[$i][$value];//$db1->dt[$value];
			}
			$inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
			$j++;
		}
		$z++;
	*/
		$db->fetch($i);
		$total_in_price = $db->dt[in_amount]*$db->dt[in_price];
		$total_out_price = $db->dt[out_amount]*$db->dt[out_price];
		$total_etc_out_price = $db->dt[etc_out_amount]*$db->dt[etc_out_price];
		$total_stock_price = $db->dt[stock_cnt]  * $db->dt[stock_price];

		//$sum_basic_stock += $db->dt[basic_stock];

		$sum_in_amount += $db->dt[in_amount];
		$sum_in_price += $db->dt[in_price];
		$sum_input_total_price += $total_in_price;

		$sum_out_amount += $db->dt[out_amount];
		$sum_out_price += $db->dt[out_price];
		$sum_delivery_total_price += $total_out_price;

		$sum_etc_out_amount += $db->dt[etc_out_amount];
		$sum_etc_out_price += $db->dt[etc_out_price];
		$sum_etc_delivery_total_price += $total_etc_out_price;

		$sum_stock_cnt += $db->dt[stock_cnt];
		$sum_stock_price += $db->dt[stock_price];
		$sum_stock_total_price += $total_stock_price;

		$now_sum_stock_cnt +=  $db->dt[in_amount] - $db->dt[out_amount] - $db->dt[etc_out_amount];

		
		$stock_amount = ($subul_type == "" ? number_format($db->dt[in_amount] - $db->dt[out_amount] - $db->dt[etc_out_amount]) : number_format($db->dt[in_amount] - $db->dt[out_amount] - $db->dt[etc_out_amount]));

		$inventory_excel->getActiveSheet()->setCellValue('A' . ($i + 3), $i+1);
		$inventory_excel->getActiveSheet()->setCellValue('B' . ($i + 3), $db->dt[gid]);
		$inventory_excel->getActiveSheet()->setCellValue('C' . ($i + 3), $db->dt[gname]);		
		$inventory_excel->getActiveSheet()->setCellValue('E' . ($i + 3), getIventoryCategoryPathByAdmin($db->dt[cid], 4));
		$inventory_excel->getActiveSheet()->setCellValue('E' . ($i + 3), getUnit($db->dt[unit], "basic_unit","","text"));
		$inventory_excel->getActiveSheet()->setCellValue('F' . ($i + 3), $db->dt[in_amount]);
		$inventory_excel->getActiveSheet()->setCellValue('G' . ($i + 3), $db->dt[in_price]);
		$inventory_excel->getActiveSheet()->setCellValue('H' . ($i + 3), $total_in_price);
		$inventory_excel->getActiveSheet()->setCellValue('I' . ($i + 3), $db->dt[out_amount]);
		$inventory_excel->getActiveSheet()->setCellValue('J' . ($i + 3), $db->dt[out_price]);
		$inventory_excel->getActiveSheet()->setCellValue('K' . ($i + 3), $total_out_price);
		$inventory_excel->getActiveSheet()->setCellValue('L' . ($i + 3), $db->dt[etc_out_amount]);
		$inventory_excel->getActiveSheet()->setCellValue('M' . ($i + 3), $db->dt[etc_out_price]);
		$inventory_excel->getActiveSheet()->setCellValue('N' . ($i + 3), $total_etc_out_price);
		$inventory_excel->getActiveSheet()->setCellValue('O' . ($i + 3), $stock_amount);

	}

	// 첫번째 시트 선택
	$inventory_excel->setActiveSheetIndex(0);

	// 너비조정
	$col = 'A';
	/*
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}
	*/
	
	$inventory_excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$inventory_excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$inventory_excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$inventory_excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
	$inventory_excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
	

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","재고수불부_기간별현황.csv").'"');
	header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'CSV');
	$objWriter->setUseBOM(true);
	$objWriter->save('php://output');

	exit;
}
//print_r($_SERVER);

if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
}
$str_page_bar = page_bar($total, $page, $max, $query_string,"");

/*
if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	
	if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
	}else{

		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
	}

	$str_page_bar = page_bar($total, $page, $max, $query_string,"");
}else{
	$str_page_bar = page_bar($total, $page, $max, "&max=$max&info_type=$info_type","");
}

if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&subul_type=$subul_type&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&sdate=$sdate&edate=$edate","");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype","");
	//echo $total.":::".$page."::::".$max."<br>";
}
*/
//if($mmode!="pop" && ($subul_type=="customer" || $subul_type=="date")){
if($mmode!="pop" && ($subul_type=="" || $subul_type=="date")){

	$sql = "select 
					ifnull(sum(case when h_div = '1' and h_type = '01' then amount else 0 end),0) in_sale_amount, 
					ifnull(sum(case when h_div = '1' and h_type = '04' then amount else 0 end),0) in_return_amount, 
					ifnull(sum(case when h_div = '1' and h_type not in ('01','04') then amount else 0 end),0) in_etc_amount,
					ifnull(sum(case when h_div = '2' and h_type = '01' then amount else 0 end),0) out_sale_amount, 
					ifnull(sum(case when h_div = '2' and h_type != '01' then amount else 0 end),0) out_etc_amount
				from 
				inventory_history h,
				inventory_history_detail hd
				where h.h_ix=hd.h_ix and h_type not in ('IW') and h.vdate='".$vdate."'
			";
	$db2->query($sql);
	$db2->fetch();

}
$page_title = "재고수불부";

$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			 <tr>
			    <td align='left' colspan=2 style='padding-bottom:10px;'> ".GetTitleNavigation("$page_title", "재고관리 > $page_title")."</td>
			</tr>
			<tr>
				<td align='left' colspan=2 style='padding-bottom:14px;'>
					<div class='tab'>
						<table class='s_org_tab'>
							<tr>
								<td class='tab'>
									<table id='tab_00'  ".($subul_type == "" ? "class='on'":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='?subul_type='\">기간별 현황</td>
											<th class='box_03'></th>
										</tr>
									</table>
									<table id='tab_01'  ".($subul_type == "date" ? "class='on'":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='?subul_type=date'\">날짜별 품목현황</td>
											<th class='box_03'></th>
										</tr>
									</table>
									<table id='tab_01'  ".($subul_type == "customer" ? "class='on'":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='?subul_type=customer'\">거래처별 현황</td>
											<th class='box_03'></th>
										</tr>
									</table>
									<table id='tab_01'  ".($subul_type == "brand" ? "class='on'":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='?subul_type=brand'\">브랜드 현황</td>
											<th class='box_03'></th>
										</tr>
									</table>
									<table id='tab_01'  ".($subul_type == "company" ? "class='on'":"").">
										<tr>
											<th class='box_01'></th>
											<td class='box_02' onclick=\"document.location.href='?subul_type=company'\">제조사별 현황</td>
											<th class='box_03'></th>
										</tr>
									</table>
								</td>
								<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>";
									$Contents .= "
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>";

			if($mmode!="pop" && ($subul_type=="" || $subul_type=="date")){
				$Contents .="
				<tr>
					<td colspan=2 align=left style='padding:10px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:2px 5px 2px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 오늘 입/출고 현황</b> </span></b> </div>")."</td>
				 </tr>
				<tr>
					<td colspan=2 style='padding-bottom:30px;'>
						<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
						<col width=12.5%>
						<col width=12.5%>
						<col width=12.5%>
						<col width=12.5%>
						<col width=12.5%>
						<col width=12.5%>
						<col width=12.5%>
						<col width=12.5%>
						<tr height=25>
							<td class='s_td' colspan='4'>입고현황</td>
							<td class='m_td' colspan='3'>출고 현황</td>
							<td class='e_td'>Total 재고 현황</td>
						</tr>
						<tr height=25>
							<td class='s_td'>상품매입</td>
							<td class='m_td'>반품입고</td>
							<td class='m_td'>기타입고</td>
							<td class='m_td'>합계</td>
							<td class='m_td'>판매출고</td>
							<td class='m_td'>기타출고</td>
							<td class='m_td'>합계</td>
							<td class='e_td'>총수량</td>
						</tr>
						<tr height=25>
							<td align='center'>".number_format($db2->dt[in_sale_amount])."</td>
							<td align='center'>".number_format($db2->dt[in_return_amount])."</td>
							<td align='center'>".number_format($db2->dt[in_etc_amount])."</td>
							<td align='center'>".number_format($db2->dt[in_sale_amount]+$db2->dt[in_return_amount]+$db2->dt[in_etc_amount])."</td>
							<td align='center'>".number_format($db2->dt[out_sale_amount])."</td>
							<td align='center'>".number_format($db2->dt[out_etc_amount])."</td>
							<td align='center'>".number_format($db2->dt[out_sale_amount]+$db2->dt[out_etc_amount])."</td>
							<td align='center'>".number_format($db2->dt[in_sale_amount]+$db2->dt[in_return_amount]+$db2->dt[in_etc_amount]-$db2->dt[out_etc_amount]-$db2->dt[out_etc_amount])."</td>
						</tr>
						</table>
					</td>
				</tr>";
			}

			$Contents .="
			<tr>
				<td colspan=2>
				<form name='search_frm' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' ><!--target='act'><input type='hidden' name='view' value='innerview'-->
				 <input type='hidden' name='mode' value='search'>
				 <input type='hidden' name='gid' value='".$gid."'>
				 <input type='hidden' name='subul_type' value='$subul_type'>
					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:10'>
								<table cellpadding=0 cellspacing=0 border=0 width=100% class='input_table_box' >";

$Contents .=	"				<col width=15%>
								<col width=35%>
								<col width=15%>
								<col width=35%>
									<!--tr>
										<td class='input_box_title'> <label for='regdate'>출고일자 </label> </td>
										<td class='input_box_item' colspan=3>
											<table border=0 cellpadding=0 cellspacing=0>
												<TD nowrap>
												<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY></SELECT> 년
												<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
												<TD width=10 align=center> ~ </TD>
												<TD nowrap>
												<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년
												<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월
												<SELECT name=vToDD></SELECT> 일</TD>
											</table>
										</td>
									</tr-->
									<tr height=27>
									  <td class='search_box_title'><b>".($subul_type == "customer" ? "거래일자":"등록일자")."</b></td>
									  <td class='search_box_item' colspan=3 >
										<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
											<col width=100>
											<col width=20>
											<col width=100>
											<col width=*>
											<tr>
												<TD nowrap>
												<input type='text' class='textbox point_color' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:100px;text-align:center;' id='start_datepicker'>
												</TD>
												<TD align=center> ~ </TD>
												<TD nowrap>
												<input type='text' class='textbox point_color' name='edate' class='textbox' value='".$edate."' style='height:20px;width:100px;text-align:center;' id='end_datepicker'>
												</TD>
												<TD style='padding:0px 10px'>
													<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
													<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
													<a href=\"javascript:setSelectDate('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
													<a href=\"javascript:setSelectDate('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
													<a href=\"javascript:setSelectDate('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
													<a href=\"javascript:setSelectDate('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
													<a href=\"javascript:setSelectDate('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
												</TD>
											</tr>
										</table>
									  </td>
									</tr>
									<tr>
										<td class='input_box_title'> 사업장/창고</td>
										<td class='input_box_item' ".($subul_type=="customer" ? "" : "colspan='3'").">
										".SelectEstablishment($company_id,"company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
										".SelectInventoryInfo($company_id, $pi_ix,'pi_ix','select','false')."
										</td>";
										if($subul_type=="customer"){
											$Contents .=	"
											<td class='input_box_title'> 거래처 유형  </td>
											<td class='input_box_item' align=left  style='padding-right:5px;padding-top:2px;'>
												<input type='checkbox' name='sell_type[]' value='1' id='sell_type_1' ".CompareReturnValue('1',$sell_type,' checked')." /><label for='sell_type_1'>매입</label> <input type='checkbox' name='sell_type[]' value='3' id='sell_type_3' ".CompareReturnValue('3',$sell_type,' checked')." /><label for='sell_type_3'>매출</label>&nbsp;&nbsp;&nbsp; <!--input type='checkbox' name='business_type' value='1' id='business_type_1' ".CompareReturnValue('1',$business_type,' checked')." /><label for='business_type_1'>온라인등록업체 포함</label-->
											</td>";
										}
									$Contents .=	"
									</tr>
									<tr>
										<td class='input_box_title'> 거래처 검색 </td>
										<td class='input_box_item' colspan='3'>
											".SelectSupplyCompany($ci_ix,"ci_ix","select", "false", '','search_company')."
										</td>
									</tr>
									
									";

if($subul_type=="customer"||$subul_type=="brand"||$subul_type=="company"){

							$Contents .=	"
									<tr>";

									if($subul_type=="customer"){
										$Contents .=	"
											
											<td class='input_box_title'> 입출고 유형  </td>
											<td class='input_box_item' align=left  style='padding-right:5px;padding-top:2px;' colspan='3'>
												<input type='checkbox' name='h_div[]' value='1' id='h_div_1' ".CompareReturnValue('1',$h_div,' checked')."/><label for='h_div_1'>입고</label> <input type='checkbox' name='h_div[]' value='2' id='h_div_2' ".CompareReturnValue('2',$h_div,' checked')." /><label for='h_div_2'>출고</label>
											</td>
										";
									}

									if($subul_type=="brand"){
										$Contents .=	"
										<td class='input_box_title'> 브래드 검색  </td>
										<td class='input_box_item' colspan='3' align=left  style='padding-right:5px;padding-top:2px;'>
											".BrandListSelect($brand,'')."
										</td>";
									}

									if($subul_type=="company"){
										$Contents .=	"
										<td class='input_box_title'> 제조사 검색  </td>
										<td class='input_box_item' colspan='3' align=left  style='padding-right:5px;padding-top:2px;'>
											".MakerList($company,'')."
										</td>";
									}
							$Contents .=	"
									</tr>";
}

if($subul_type == "date"){
	$Contents .=	"			<tr>
										<td class='input_box_title'> 품목선택  </td>
										<td class='input_box_item'  colspan=3 align=left  style='padding-right:5px;padding-top:2px;'>";
										if($gid){
											$Contents .=	"<b>".$gname."</b> <a href=\"javascript:ShowModalWindow('./inventory_search.php?type=subul',900,480,'inventory_search')\"><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align='absmiddle' style='cursor:pointer;'></a>";
										}else{
											$Contents .=	"<a href=\"javascript:ShowModalWindow('./inventory_search.php?type=subul',900,480,'inventory_search')\"><img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align='absmiddle' style='cursor:pointer;'></a>";
										}
										$Contents .=	"
										</td>
									</tr>";
}elseif($subul_type != "customer"){
	$Contents .=	"			<tr>
										<td class='input_box_title'> 검색어  
											<br/>
											<label for='mult_search_use'>(다중검색 체크)</label> <input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
											</td>
										<td class='input_box_item'  colspan=3 align=left  style='padding-right:5px;padding-top:2px;'>
											<table cellpadding=0 cellspacing=0>
											<tr>
												<td>
													<select name='search_type' id='search_type' style=\"font-size:12px;height:22px;min-width:140px;\">
														<option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type).">대표코드</option>
														<option value='g.gid' ".CompareReturnValue("g.gid",$search_type).">품목코드</option>
														<option value='g.gname' ".CompareReturnValue("g.gname",$search_type).">품목명</option>
														<option value='gu.gu_ix' ".CompareReturnValue("gu.gu_ix",$search_type).">시스템코드</option>
														<option value='gu.barcode' ".CompareReturnValue("gu.barcode",$search_type).">바코드</option>
													</select>
												</td>
												<td style='padding-left:3px;'>
													<div id='search_text_input_div'>
														<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
													</div>
													<div id='search_text_area_div' style='display:none;'>
														<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
													</div>
												</td>
												<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'>* 상품명의 일부를 입력하시면 자동검색됩니다. 2자 이상 입력해주세요</span></td>
											</tr>
											</table>
										</td>
									</tr>";
}
	$Contents .=	"
								</table>
							</td>
							<th class='box_06'></th>
						</tr>
						<tr ><td  colspan=2 align=center style='padding-top:20px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle> <!--btn_inquiry.gif--></td></tr>
						<tr>
							<th class='box_07'></th>
							<td class='box_08'></td>
							<th class='box_09'></th>
						</tr>
					</table>
					</form>

				</td>
			</tr>
			<tr>
				<td align=right style='padding:5px 0px;'>
				<table width=100%>
					<tr>
						<td>
							목록수 : ".number_format($total)." 개";
					if($subul_type=="customer"||$subul_type=="brand"||$subul_type=="company"){
						$innerview .= "&nbsp;&nbsp;<input type='checkbox' name='detail_view' id='detail_view' onclick=\"".($detail_view == 1 ? "location.href='?".str_replace('&detail_view=1','&detail_view=0',$_SERVER["QUERY_STRING"])."'":"location.href='?".str_replace('&detail_view=0','',$_SERVER["QUERY_STRING"]).(strlen($_SERVER["QUERY_STRING"]) > 0 ? "&detail_view=1" : "detail_view=1")."'")."\" ".($detail_view == 1 ? "checked":"")." ><label for='detail_view'> 상세리스트보기</label>";
					}
					$innerview .= "
						</td>
						<td align=right>
						
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

						";
if($subul_type==""||$subul_type=="date"){
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		$innerview .= "<a href='?mode=excel&".str_replace("mode=".$mode, "mode=excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}else{
		$innerview .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}
}else{
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		$innerview .= "<a href='?mode=etc_excel&".str_replace("mode=".$mode, "mode=etc_excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}else{
		$innerview .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}
}

$innerview .= "	</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
			<td valign=top >";

/*
$innerview .= "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
			<tr height=30>
				<!--td align=left>
				<b>상품명</b>
				<a href='?cid=$cid&depth=0&orderby=pname&ordertype=asc'><img src='../image/orderby_desc.gif' border=0 align=top alt='가나다순' title='가나다순'></a>
				<a href='?cid=$cid&depth=0&orderby=pname&ordertype=desc'><img src='../image/orderby_asc.gif' border=0 align=top alt='가나다역순' title='가나다역순'></a>
				<b>출고날짜</b>
				<a href='?cid=$cid&depth=0&orderby=date&ordertype=desc'><img src='../image/orderby_desc.gif' border=0 align=top alt='최근등록순' title='최근등록순'></a>
				<a href='?cid=$cid&depth=0&orderby=date&ordertype=asc'><img src='../image/orderby_asc.gif' border=0 align=top alt='등록순' title='등록순'></a>
				</td-->
			</tr>
			</table>";
*/

if($subul_type=="" || $subul_type=="date"){

	$innerview .= "
				<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<tr bgcolor='#cccccc' height=30 align=center>
				".($subul_type == "date" ? "<td width='7%' class=m_td rowspan=2>날짜</td>":"")."
				  <td width='7%' class=m_td rowspan=2>품목코드</td>
				  <td width='*' class=m_td rowspan=2>이미지/품목명</td>
				  <td width='5%' class=m_td rowspan=2>이월재고</td>
				  <td width='15%' class=m_td colspan=3>입고</td>
				  <td width='15%' class=m_td colspan=3>출고</td>
				  <td width='15%' class=m_td colspan=3>기타출고</td>
				  <td width='5%' class=m_td colspan=1 >재고</td>
				</tr>
				<tr>
				  <td width='5%' class=m_td height=25px>수량</td>
				  <td width='5%' class=m_td>단가</td>
				  <td width='5%' class=m_td>금액</td>
				  <td width='5%' class=m_td>수량</td>
				  <td width='5%' class=m_td>단가</td>
				  <td width='5%' class=m_td>금액</td>
				  <td width='5%' class=m_td>수량</td>
				  <td width='5%' class=m_td>단가</td>
				  <td width='5%' class=m_td>금액</td>
				  <td width='5%' class=m_td >수량</td>
				  <!--<td width='5%' class=m_td ".($subul_type == "date" ?  "style='display:none'" : "" ).">단가</td>
				  <td width='5%' class=m_td ".($subul_type == "date" ?  "style='display:none'" : "" ).">금액</td>-->
			  </tr>";

	//echo $sql;

	if($total == 0){
		if($subul_type=="date"){
			if($mode=="search"){
				$innerview .= "<tr bgcolor=#ffffff height=50><td colspan='14' align=center> 재고 수불 내역이 없습니다.</td></tr>";
			}else{
				$innerview .= "<tr bgcolor=#ffffff height=50><td colspan='14' align=center> 단품을 먼저 선택해 주시기 바랍니다.</td></tr>";
			}
		}else{
			if($mode=="search"){
				$innerview .= "<tr bgcolor=#ffffff height=50><td colspan='13' align=center> 재고 수불 내역이 없습니다.</td></tr>";
			}else{
				$innerview .= "<tr bgcolor=#ffffff height=50><td colspan='13' align=center> 원하시는 품목을 검색해주세요.</td></tr>";
			}
		}
	}else{
		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);
			$no = $total - ($page - 1) * $max - $i;

			if(file_exists(InventoryPrintImage($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/inventory", $db->dt[gid], "c"))){
				$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $db->dt[gid], "c");
			}else{
				$img_str = "../image/no_img.gif";
			}

			$total_in_price = $db->dt[in_amount]*$db->dt[in_price];
			$total_out_price = $db->dt[out_amount]*$db->dt[out_price];
			$total_etc_out_price = $db->dt[etc_out_amount]*$db->dt[etc_out_price];
			$total_stock_price = $db->dt[stock_cnt]  * $db->dt[stock_price];
			
			$basic_stock = get_basic_stock($db->dt[vdate],$db->dt[gid],$db->dt[unit]);

			$sum_basic_stock += $basic_stock;

			$sum_in_amount += $db->dt[in_amount];
			$sum_in_price += $db->dt[in_price];
			$sum_input_total_price += $total_in_price;

			$sum_out_amount += $db->dt[out_amount];
			$sum_out_price += $db->dt[out_price];
			$sum_delivery_total_price += $total_out_price;

			$sum_etc_out_amount += $db->dt[etc_out_amount];
			$sum_etc_out_price += $db->dt[etc_out_price];
			$sum_etc_delivery_total_price += $total_etc_out_price;

			$sum_stock_cnt += $db->dt[stock_cnt];
			$sum_stock_price += $db->dt[stock_price];
			$sum_stock_total_price += $total_stock_price;

			$now_sum_stock_cnt +=  $db->dt[in_amount] - $db->dt[out_amount] - $db->dt[etc_out_amount];

			$innerview .= "
					<tr height=70>
						".($subul_type == "date" ? "<td class='list_box_td list_bg_gray'>".substr($db->dt[vdate],0,4).".".substr($db->dt[vdate],4,2).".".substr($db->dt[vdate],6,2)."</td>":"")."
						<td class='list_box_td '>".$db->dt[gid]." ".($subul_type=="" && $db->dt[barcode] ? "<br/>(".$db->dt[barcode].")" : "" )."</td>
						<td class='list_box_td point' style='padding:5px 5px;' nowrap>
						<table>
							<tr>
								<td width='50' align=center style='padding:0px 10px;'><img src='".$img_str."' width=50 height=50 style='border:1px solid #eaeaea' align=absmiddle></td>
								<td  class='list_box_td'style='text-align:left; padding-left:10px;line-height:150%;'>
								<span class='small'>".getIventoryCategoryPathByAdmin($db->dt[cid], 4)."</span><br>
								<a href=\"javascript:PoPWindow('./stock_subul.php?mmode=pop&subul_type=date&gid=".$db->dt[gid]."&sdate=".$sdate."&edate=".$edate."',1100,980,'subul');\">".$db->dt[gname]."</a><br>
								".getUnit($db->dt[unit], "basic_unit","","text")."
								</td>
							</tr>
						</table>
						</td>
						<td class='list_box_td'>".number_format($basic_stock)."</td>
						<td class='list_box_td list_bg_gray'>".number_format($db->dt[in_amount])."</td>
						<td class='list_box_td' style='padding:0px 5px;' nowrap>".number_format($db->dt[in_price])."</td>
						<td class='list_box_td list_bg_gray'>".number_format($total_in_price)."</td>
						<td class='list_box_td'>".number_format($db->dt[out_amount])."</td>
						<td class='list_box_td list_bg_gray '>".number_format($db->dt[out_price])."</td>
						<td class='list_box_td '>".number_format($total_out_price)."</td>
						<td class='list_box_td list_bg_gray'>".number_format($db->dt[etc_out_amount])."</td>
						<td class='list_box_td '>".number_format($db->dt[etc_out_price])."</td>
						<td class='list_box_td list_bg_gray'>".number_format($total_etc_out_price)."</td>
						<td class='list_box_td '>".($subul_type == "" ? number_format($basic_stock + $db->dt[in_amount] - $db->dt[out_amount] - $db->dt[etc_out_amount]) : number_format($db->dt[in_amount] - $db->dt[out_amount] - $db->dt[etc_out_amount]))."</td>
						<td class='list_box_td list_bg_gray' ".($subul_type == "date" ?  "style='display:none'" : "style='display:none'" ).">".number_format($db->dt[stock_price])."</td>
						<td class='list_box_td ' ".($subul_type == "date" ?  "style='display:none'" : "style='display:none'" ).">".number_format($total_stock_price)."</td>
					</tr>";
						

		}

		$innerview .= "<tr height=30>
						<td class='list_box_td list_bg_gray' colspan=".($subul_type == "date" ? "3'":"2")."><b>합계</b></td>
						<td class='list_box_td '>".number_format($sum_basic_stock)."</td>
						<td class='list_box_td list_bg_gray'>".number_format($sum_in_amount)."</td>
						<td class='list_box_td ' >".number_format($sum_in_price)."</td>
						<td class='list_box_td list_bg_gray' >".number_format($sum_input_total_price)."</td>
						<td class='list_box_td  ' >".number_format($sum_out_amount)."</td>
						<td class='list_box_td list_bg_gray' >".number_format($sum_out_price)."</td>
						<td class='list_box_td  ' >".number_format($sum_delivery_total_price)."</td>
						<td class='list_box_td  list_bg_gray' >".number_format($sum_etc_out_amount)."</td>
						<td class='list_box_td  ' >".number_format($sum_etc_out_price)."</td>
						<td class='list_box_td  list_bg_gray' >".number_format($sum_etc_delivery_total_price)."</td>
						<td class='list_box_td'  >".($subul_type == "" ? number_format($now_sum_stock_cnt) : number_format($now_sum_stock_cnt))."</td>
						<td class='list_box_td  list_bg_gray' ".($subul_type == "date" ?  "style='display:none'" : "style='display:none'" ).">".number_format($sum_stock_price)."</td>
						<td class='list_box_td' ".($subul_type == "date" ?  "style='display:none'" : "style='display:none'" ).">".number_format($sum_stock_total_price)."</td>
						</tr>";

	}

	$innerview .= "</table>";

}else{

	$innerview .= "
				<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<tr bgcolor='#cccccc' height=25 align=center>";
				if($subul_type == "customer"){
					$innerview .= "<td width='13%' class=m_td rowspan=2>거래처명</td>";
				}elseif($subul_type == "brand"){
					$innerview .= "<td width='13%' class=m_td rowspan=2>브랜드명</td>";
				}elseif($subul_type=="company"){
					$innerview .= "<td width='13%' class=m_td rowspan=2>제조사명</td>";
				}

				if($detail_view=='1'){
					$innerview .= "<td width='13%' class=m_td rowspan=2>품목코드<br/>/품목명</td>";
					$innerview .= "<td width='8%' class=m_td rowspan=2>주거래처</td>";
					$innerview .= "<td width='8%' class=m_td rowspan=2>단위/규격</td>";
					$innerview .= "<td width='5%' class=m_td rowspan=2>이월재고</td>";
				}
				$innerview .= "
				  <td  class=m_td colspan=3>입고</td>
				  <td  class=m_td colspan=5>출고</td>";

				if($detail_view=='1'){
					$innerview .= "<td width='5%' class=m_td rowspan=2>재고</td>";
				}

				$innerview .= "
				  <td width='4%' class=m_td rowspan=2>상세<br/>보기</td>
				</tr>
				<tr height=25>
				  <td width='5%' class=m_td>수량</td>
				  <td width='6%' class=m_td>단가</td>
				  <td width='6%' class=m_td>금액</td>
				  <td width='5%' class=m_td>수량</td>
				  <td width='6%' class=m_td>단가</td>
				  <td width='5%' class=m_td>반품(+)</td>
				  <td width='6%' class=m_td>단가</td>
				  <td width='6%' class=m_td>합계</td>
			  </tr> ";

	//echo $sql;

	if($db->total == 0){
		$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=".($detail_view == "1" ? "15'":"10")." align=center> 재고 수불 내역이 없습니다.</td></tr>";
	}else{
		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);
			$no = $total - ($page - 1) * $max - $i;

			if(file_exists(InventoryPrintImage($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/inventory", $db->dt[gid], "c"))){
				$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $db->dt[gid], "c");
			}else{
				$img_str = "../image/no_img.gif";
			}
			
			
			$total_in_price = $db->dt[in_amount]*$db->dt[in_price];
			$total_out_price = $db->dt[out_amount]*$db->dt[out_price];
			$total_return_price = $db->dt[return_amount]*$db->dt[return_price];


			$sum_in_amount += $db->dt[in_amount];
			$sum_in_price += $db->dt[in_price];
			$sum_input_total_price += $total_in_price;

			$sum_out_amount += $db->dt[out_amount];
			$sum_out_price += $db->dt[out_price];
			$sum_delivery_total_price += $total_out_price;

			$sum_return_amount += $db->dt[return_amount];
			$sum_return_price += $db->dt[return_price];
			$sum_return_total_price += $total_return_price;
			
			if($subul_type == "customer"){
				$innerview .= "<tr height=70>
						<td class='list_box_td point' nowrap>".$db->dt[com_name]."</td>";
			}elseif($subul_type == "brand"){
				$innerview .= "<tr height=70>
						<td class='list_box_td point' nowrap>".$db->dt[brand_name]."</td>";
			}elseif($subul_type == "company"){
				$innerview .= "<tr height=70>
						<td class='list_box_td point' nowrap>".$db->dt[company]."</td>";
			}
			
			if($detail_view=='1'){
				$basic_stock = get_basic_stock($db->dt[vdate],$db->dt[gid],$db->dt[unit]);

				$innerview .= "
						<td class='list_box_td' style='text-align:left'><b>".$db->dt[gid]."</b><br/>".$db->dt[gname]."</td>
						<td class='list_box_td'>".$db->dt[basic_com_name]."</td>
						<td class='list_box_td list_bg_gray' style='text-align:left'>".getUnit($db->dt[unit],"","","text").($db->dt[standard] ? "/".$db->dt[standard] : "")."</td>
						<td class='list_box_td'>".number_format($basic_stock)."</td>";
			}

			$innerview .= "
						<td class='list_box_td'>".number_format($db->dt[in_amount])."</td>
						<td class='list_box_td list_bg_gray'>".number_format($db->dt[in_price])."</td>
						<td class='list_box_td' style='padding:0px 5px;' nowrap>".number_format($total_in_price)."</td>
						<td class='list_box_td list_bg_gray'>".number_format($db->dt[out_amount])."</td>
						<td class='list_box_td'>".number_format($db->dt[out_price])."</td>
						<td class='list_box_td list_bg_gray'>".number_format($db->dt[return_amount])."</td>
						<td class='list_box_td '>".number_format($db->dt[return_price])."</td>
						<td class='list_box_td list_bg_gray'>".number_format($total_out_price+$total_return_price)."</td>";

					if($detail_view=='1'){
						$innerview .= "
						<td class='list_box_td'>".number_format($basic_stock + $db->dt[in_amount] - $db->dt[out_amount])."</td>";
					}

					$innerview .= "
						<td class='list_box_td '><img src='../images/".$_SESSION['admininfo']['language']."/btn_search.gif' ";

			if($subul_type == "customer"){
				if($detail_view=='1'){
					$innerview .= "onclick=\"PopSWindow('./stock_subul_detail_pop.php?ci_ix=".$db->dt[ci_ix]."&sdate=".$sdate."&edate=".$edate."&gid=".$db->dt[gid]."&unit=".$db->dt[unit]."',900,300,'stock_subul_detail_pop')\"";
				}else{
					$innerview .= "onclick=\"PopSWindow('./stock_subul_detail_pop.php?ci_ix=".$db->dt[ci_ix]."&sdate=".$sdate."&edate=".$edate."',900,300,'stock_subul_detail_pop')\"";
				}
			}elseif($subul_type == "brand"){
				if($detail_view=='1'){
					$innerview .= "onclick=\"PopSWindow('./stock_subul_detail_pop.php?b_ix=".$db->dt[b_ix]."&sdate=".$sdate."&edate=".$edate."&gid=".$db->dt[gid]."&unit=".$db->dt[unit]."',900,300,'stock_subul_detail_pop')\"";
				}else{
					$innerview .= "onclick=\"PopSWindow('./stock_subul_detail_pop.php?b_ix=".$db->dt[b_ix]."&sdate=".$sdate."&edate=".$edate."',900,300,'stock_subul_detail_pop')\"";
				}
			}elseif($subul_type == "company"){
				if($detail_view=='1'){
					$innerview .= "onclick=\"PopSWindow('./stock_subul_detail_pop.php?maker=".$db->dt[maker]."&sdate=".$sdate."&edate=".$edate."&gid=".$db->dt[gid]."&unit=".$db->dt[unit]."',900,300,'stock_subul_detail_pop')\"";
				}else{
					$innerview .= "onclick=\"PopSWindow('./stock_subul_detail_pop.php?maker=".$db->dt[maker]."&sdate=".$sdate."&edate=".$edate."',900,300,'stock_subul_detail_pop')\"";
				}
			}


			$innerview .= " style='cursor:pointer' /></td>
					</tr>";

		}
		
		if($detail_view!='1'){
			$innerview .= "<tr height=30>
						<td class='list_box_td list_bg_gray' colspan=".($detail_view == "1" ? "3'":"1")."><b>합계</b></td>
						<td class='list_box_td '>".number_format($sum_in_amount)."</td>
						<td class='list_box_td list_bg_gray'>".number_format($sum_in_price)."</td>
						<td class='list_box_td ' >".number_format($sum_input_total_price)."</td>
						<td class='list_box_td list_bg_gray' >".number_format($sum_out_amount)."</td>
						<td class='list_box_td  ' >".number_format($sum_out_price)."</td>
						<td class='list_box_td list_bg_gray' >".number_format($sum_return_amount)."</td>
						<td class='list_box_td  ' >".number_format($sum_return_price)."</td>
						<td class='list_box_td  list_bg_gray' >".number_format($sum_delivery_total_price+$sum_return_total_price)."</td>
						<td class='list_box_td  ' >-</td>";

						$innerview .= "
						</tr>";
		}
	}

	$innerview .= "</table>";

}

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
			";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >리스트에서 기본적인 정보를 수정하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >개별정보를 수정후 <img src='../image/btc_modify.gif' align=absmiddle> 버튼를 클릭하시면 해당 제품만을 수정하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >리스트의 여러제품을 수정후 <img src='../image/bt_all_modify.gif' align=absmiddle> 버튼를 클릭하시면 해당 리스트에 보여지는 전체 제품을 수정하실수 있습니다</td></tr>
</table>
";

$Contents .= "
<table width=100%>
<tr>
	<td align=right>
	".$str_page_bar."
	<!--img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\" /-->
	<!--div style='cursor:pointer' onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\">회원수동등록</div-->
	</td>
</tr>
</table>";
//$Contents .= HelpBox("상품 리스트", $help_text);





$Script = "
<script type='text/javascript'>

$(document).ready(function(){

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
});


$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});

function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}


function onLoad(FromDate, ToDate) {
	var frm = document.search_frm;


//	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
//	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);";

/*
if($regdate != "1"){
$Script .= "
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;	";
}*/
$Script .= "
	init_date(FromDate,ToDate);

}
</script>
";


if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
 
	$P->strLeftMenu = inventory_menu();
	$P->strContents = $Contents;
	$P->Navigation = "재고관리 > 집계표 > $page_title";
	$P->title = "$page_title";
	$P->NaviTitle = "재고품목 등록/수정";
	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 집계표 > $page_title";
	$P->title = "$page_title";
	$P->strContents = $Contents;
	//$P->OnloadFunction = "onLoad('$sDate','$eDate');";//"ChangeOrderDate(document.search_frm);";
	$P->addScript = "<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>\n$Script";
	$P->PrintLayOut();
}


function get_basic_stock($vdate,$gid,$unit){
	global $cdb;

	if($_GET['pi_ix']!=""){
		$where .= " and pi_ix = '".$_GET['pi_ix']."'";
	}
	
	if($_GET["company_id"] != ""){
		$where .= " and company_id = '".$_GET["company_id"]."' ";
	}
	
	if($vdate != ""){
		$where .= " and vdate=date_format(date_add('".$vdate."', INTERVAL -1 DAY),'%Y%m%d') ";
	}

	$sql="select 
		ifnull(sum(stock_cnt),0) as basic_stock
	from 
		inventory_product_stockinfo_bydate
	where 
		gid='".$gid."' and unit='".$unit."' 
		$where";
	$cdb->query($sql);
	$cdb->fetch();
	return $cdb->dt[basic_stock];
}
?>
