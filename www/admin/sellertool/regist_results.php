<?
include("../openapi/openapi.lib.php");
include("sellertool.lib.php");

//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");

include("../logstory/class/sharedmemory.class");

require_once('/home/entersix/common.php');
require_once(DOCUMENT_ROOT.'/admin/include/phpexcel/Classes/PHPExcel.php');


if($admininfo[admin_level] < 9){
	header("Location:/admin/seller/");
}

$shmop = new Shared("reserve_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$reserve_data = $shmop->getObjectForKey("reserve_rule");
$reserve_data = unserialize(urldecode($reserve_data));

//print_r($reserve_data);

//echo $update_kind;
if($before_update_kind){
	$update_kind = $before_update_kind;
}

if($_COOKIE["goodsinfo_update_kind"]){
	$update_kind = $_COOKIE["goodsinfo_update_kind"];
}else if(!$update_kind){
	$update_kind = "display";
}

if($max == ""){
	$max = 40; //페이지당 갯수
}else{
	$max = $max;
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;
$db2 = new Database;
/*if($_SESSION["mode"] == "search"){
	$mode = "search";
}*/
$vdate = date("Ymd", time());
$coupon_sdate= date("Y/m/d", time());
$coupon_edate= date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)+1,substr($vdate,6,2)+1,substr($vdate,0,4)));
//TODO:제휴사판매상태/즉시할인여부 쿼리추가하기

if(empty($sdate)){
	$sdate = date("Ymd", strtotime($today."-7day"));
}

if(empty($edate)){
	$edate = date("Ymd");
}

if($orderby != "" && $ordertype != ""){
	$orderbyString = " order by $orderby $ordertype ";
}else{
	$orderbyString = " order by srr.regist_date desc ";
}

if($mode == "search"){
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
	$where = " AND p.product_type NOT IN (".implode(',',$sns_product_type).") ";
	if($search_text != ""){
		$where .= "and p.".$search_type." LIKE '%".trim($search_text)."%' ";
	}

	if($sprice && $eprice){
		$where .= "and sellprice between $sprice and $eprice ";
	}

	if($status_where){
		$where .= " and ($status_where) ";
	}
	if($brand != ""){
		$where .= " and brand = ".$brand."";
	}

	if($brand_name != ""){
		$where .= " and p.brand_name LIKE '%".$brand_name."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}
    if($result_code != ""){
        $where .= " and srr.result_code = ".$result_code;
    }

	if($site_code != ""){
		$where = $where." and site_code = '".$site_code."' ";
	}

	if($state2 != ""){
		$where .= " and state = ".$state2."";
	}


	if($cid2 != ""){
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}

	if($sdate != "" && $edate != ""){
		//$where .= " and  date_format(p.regdate,'%Y%m%d') between  $sdate and $edate ";
		//$where .= " and  p.regdate between  '".substr($sdate, 0, 4)."-".substr($sdate, 4, 2)."-".substr($sdate, 6, 2)." 00:00:00' and '".substr($edate, 0, 4)."-".substr($edate, 4, 2)."-".substr($edate, 6, 2)." 23:59:59' ";
		$where .= " and srr.regist_date between '".substr($sdate, 0, 4)."-".substr($sdate, 4, 2)."-".substr($sdate, 6, 2)." 00:00:00' and '".substr($edate, 0, 4)."-".substr($edate, 4, 2)."-".substr($edate, 6, 2)." 23:59:59' ";
	}
	if($admininfo[admin_level] == 9)
	{
		if($company_id != ""){
			$addWhere = "and admin ='".$company_id."'";
		}else{
			unset($addWhere);
		}
		$sql = "SELECT  p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name, p.stock,
		p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate, p.reserve, p.reserve_rate,p.reserve_yn,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, srr.site_code, srr.result_pno, srr.result_code, srr.result_msg, srr.regist_date, srr.srl_ix
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c , sellertool_regist_relation srr
		where p.id = srr.pid and c.company_id = p.admin and p.id = r.pid and r.basic = 1 $addWhere $where
		$orderbyString";
	}
	else
	{
		$sql = "SELECT  p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid,  p.search_keyword,state, p.brand_name, p.stock,
		p.company, p.pcode, p.coprice, p.listprice,p.disp, p.editdate, p.reserve, p.reserve_rate,p.reserve_yn,
		case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, srr.site_code, srr.result_pno, srr.result_code, srr.result_msg, srr.regist_date, srr.srl_ix
		FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c , sellertool_regist_relation srr
		where p.id = srr.pid and c.company_id = p.admin and p.id = r.pid and r.basic = 1  and admin ='".$admininfo[company_id]."' $where
		$orderbyString";
	}

	if($act!='excel_save')
		$sql .= " LIMIT $start, $max";

// 	echo "<!-- #sql={$sql} -->";
	$db->query($sql);

	if($act=='excel_save')
	{
		ini_set('memory_limit',  -1 );

		$phpexcel = new PHPExcel();
		$phpexcel->getProperties()->setCreator("(주)엔터식스");
		$phpexcel->getProperties()->setLastModifiedBy("(주)엔터식스");
		$phpexcel->getProperties()->setTitle("제휴사 연동결과 _" . str_replace('-', '', $sdate) . "-" . str_replace('-', '', $edate));
		$phpexcel->getProperties()->setSubject("제휴사 연동결과 _" . str_replace('-', '', $sdate) . "-" . str_replace('-', '', $edate));
		$phpexcel->getProperties()->setDescription("제휴사 연동결과 _" . str_replace('-', '', $sdate) . "-" . str_replace('-', '', $edate));
		$phpexcel->getProperties()->setKeywords("(주)엔터식스");
		$phpexcel->getProperties()->setCategory("(주)엔터식스");


		$phpexcel->setActiveSheetIndex(0);

		$phpexcel_sheet = &$phpexcel->getActiveSheet();

		$phpexcel_sheet->getColumnDimension('A')->setWidth(30);
		$phpexcel_sheet->getColumnDimension('B')->setWidth(57.14);
		$phpexcel_sheet->getColumnDimension('C')->setWidth(97.86);
		$phpexcel_sheet->getColumnDimension('D')->setWidth(9.57);
		$phpexcel_sheet->getColumnDimension('E')->setWidth(7.43);
		$phpexcel_sheet->getColumnDimension('F')->setWidth(16.86);
		$phpexcel_sheet->getColumnDimension('G')->setWidth(38.29);
		$phpexcel_sheet->getColumnDimension('H')->setWidth(14.86);
		$phpexcel_sheet->getColumnDimension('I')->setWidth(10);
		$phpexcel_sheet->getColumnDimension('J')->setWidth(14.86);
		$phpexcel_sheet->getColumnDimension('K')->setWidth(14.86);
		$phpexcel_sheet->getColumnDimension('L')->setWidth(13.29);
		$phpexcel_sheet->getColumnDimension('M')->setWidth(17.29);
		$phpexcel_sheet->getColumnDimension('N')->setWidth(17.29);
		$phpexcel_sheet->getColumnDimension('O')->setWidth(12.43);
		$phpexcel_sheet->getColumnDimension('P')->setWidth(22.14);
		$phpexcel_sheet->getColumnDimension('Q')->setWidth(14.86);
		$phpexcel_sheet->getColumnDimension('R')->setWidth(53);


		$phpexcel_sheet->setCellValue('A1', '품번');
		$phpexcel_sheet->setCellValue('B1', '카테고리');
		$phpexcel_sheet->setCellValue('C1', '상품명');
		$phpexcel_sheet->setCellValue('D1', '처리상태');
		$phpexcel_sheet->setCellValue('E1', '구분');
		$phpexcel_sheet->setCellValue('F1', '상품시스템코드');
		$phpexcel_sheet->setCellValue('G1', '셀러');
		$phpexcel_sheet->setCellValue('H1', '자사 판매상태');
		$phpexcel_sheet->setCellValue('I1', '자사 진열');
		$phpexcel_sheet->setCellValue('J1', '자사 소비자가');
		$phpexcel_sheet->setCellValue('K1', '자사 판매가');
		$phpexcel_sheet->setCellValue('L1', '제휴사');
		$phpexcel_sheet->setCellValue('M1', '제휴사 상품코드');
		$phpexcel_sheet->setCellValue('N1', '제휴사 판매상태');
		$phpexcel_sheet->setCellValue('O1', '제휴사 진열');
		$phpexcel_sheet->setCellValue('P1', '제휴사 즉시할인여부');
		$phpexcel_sheet->setCellValue('Q1', '제휴사 판매가');
		$phpexcel_sheet->setCellValue('R1', '결과메세지');

		$phpexcel_style = $phpexcel_sheet->getStyle('A1:R1');
// 		$phpexcel_style->getFont()->setBold(true);

		$phpexcel_style->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$phpexcel_style->getFill()->getStartColor()->setRGB('009C65');
// 		$phpexcel_style->getFill()->getStartColor()->setARGB('3459bb');

// 		$phpexcel_style->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		for($i=0; $i<$db->total; ++$i)
		{
			$db->fetch($i);

			$col_no = (string)($i+2);


			$phpexcel_sheet->getCell("A{$col_no}")->setValueExplicit($db->dt[pcode]); // '품번'
			$phpexcel_sheet->getCell("B{$col_no}")->setValueExplicit(getCategoryPathByAdmin($db->dt[cid], 4)); // '카테고리'
			$phpexcel_sheet->getCell("C{$col_no}")->setValueExplicit($db->dt[pname]); // '상품명');

			switch($db->dt[result_code])	// '처리상태'
			{
				case 200: $phpexcel_sheet->getCell("D{$col_no}")->setValueExplicit('성공'); break;
				case 500: $phpexcel_sheet->getCell("D{$col_no}")->setValueExplicit('오류'); break;
				case 2001: $phpexcel_sheet->getCell("D{$col_no}")->setValueExplicit('실패'); break;
				default: $phpexcel_sheet->getCell("D{$col_no}")->setValueExplicit('??'); break;
			}

			$phpexcel_sheet->getCell("E{$col_no}")->setValueExplicit('쇼핑몰'); // '구분'
			$phpexcel_sheet->getCell("F{$col_no}")->setValueExplicit($db->dt[id]); // '상품시스템코드'
			$phpexcel_sheet->getCell("G{$col_no}")->setValueExplicit($db->dt[com_name]); // '셀러'

			switch($db->dt[state])
			{
				case SALE_STATE_NO_STOCK: $phpexcel_sheet->getCell("H{$col_no}")->setValueExplicit('일시품절'); break;
				case SALE_STATE_RUNNING: $phpexcel_sheet->getCell("H{$col_no}")->setValueExplicit('판매중'); break;
				case SALE_STATE_PAUSED: $phpexcel_sheet->getCell("H{$col_no}")->setValueExplicit('판매중지'); break;
				case SALE_STATE_NEED_APPROVAL: $phpexcel_sheet->getCell("H{$col_no}")->setValueExplicit('승인대기'); break;
				case SALE_STATE_NEED_CORRECTION: $phpexcel_sheet->getCell("H{$col_no}")->setValueExplicit('수정요청'); break;
				case SALE_STATE_STOPPED: $phpexcel_sheet->getCell("H{$col_no}")->setValueExplicit('판매금지'); break;
				default: $phpexcel_sheet->getCell("H{$col_no}")->setValueExplicit($db->dt[state]); break;
			}

			switch($db->dt[disp])
			{
				case 0: $phpexcel_sheet->getCell("I{$col_no}")->setValueExplicit('진열안함'); break;
				case 1: $phpexcel_sheet->getCell("I{$col_no}")->setValueExplicit('진열함'); break;
			}

			$phpexcel_sheet->getCell("J{$col_no}")->setValueExplicit($db->dt[listprice]); // '자사 소비자가'
			$phpexcel_sheet->getCell("K{$col_no}")->setValueExplicit($db->dt[sellprice]); // '자사 판매가'
			$phpexcel_sheet->getCell("L{$col_no}")->setValueExplicit($db->dt[site_code]); // '제휴사'
			$phpexcel_sheet->getCell("M{$col_no}")->setValueExplicit($db->dt[result_pno]);// '제휴 상품코드

			if($db->dt[target_display]=='onsale')
				$phpexcel_sheet->getCell("N{$col_no}")->setValueExplicit('판매중'); // '제휴사 판매상태'
			else
				$phpexcel_sheet->getCell("N{$col_no}")->setValueExplicit('판매중지'); // '제휴사 판매상태'

			$phpexcel_sheet->getCell("O{$col_no}")->setValueExplicit('N/A'); // '제휴사 진열'

			if($db->dt[target_coupon_yn]=='Y')
				$phpexcel_sheet->getCell("P{$col_no}")->setValueExplicit('설정함'); // '제휴사 즉시할인여부'
			else
				$phpexcel_sheet->getCell("P{$col_no}")->setValueExplicit('설정안함'); // '제휴사 즉시할인여부'

			$phpexcel_sheet->getCell("Q{$col_no}")->setValueExplicit($db->dt[target_price]); // '제휴사 판매가'
			$phpexcel_sheet->getCell("R{$col_no}")->setValueExplicit($db->dt[result_msg]);	// '결과 메세지'
		}

		// $this->header("content-type: application/octet-stream");
		header("content-type: application/vnd.ms-excel");
		header("content-disposition: attachment; filename=\"result_list_" . date("Ymd", strtotime($sdate)) . "~" . date("Ymd", strtotime($edate)) . ".xls\"");
		header("cache-control: no-cache");
		header("cache-control: no-store");

		$phpexcel_writer = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
		$phpexcel_writer->save('php://output');

		exit;
	}

}else{

	if ($cid2 == ""){
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "and admin ='".$company_id."'";
			}else{
				unset($addWhere);
			}
			//$tmp_sql = "create temporary table ".TBL_LOGSTORY_BYPAGE."_tmp ENGINE = MEMORY select vdate, pageid, ncnt, nduration from ".TBL_SHOP_PRODUCT_RELATION." where vdate = '$vdate' ";

			$sql = "SELECT  p.id as id, p.pname, p.brand, p.sellprice, p.regdate, p.vieworder,c.com_name, r.cid, p.search_keyword,state, p.brand_name, p.stock,
			p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate, p.reserve, p.reserve_rate, p.reserve_yn,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, srr.*
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c, sellertool_regist_relation srr
			where p.id = srr.pid and c.company_id = p.admin and p.id = r.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).") and r.basic = 1 $where $addWhere $orderbyString LIMIT $start, $max";
			//echo $sql;
			if(!empty($_SERVER["QUERY_STRING"])){
				$db->query($sql);
			}
		}else{
			$sql = "SELECT  p.id as id ,p.brand, p.pname, p.sellprice, p.regdate, p.vieworder,c.com_name, r.cid, p.search_keyword,state, p.brand_name, p.stock,
			p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate, p.reserve, p.reserve_rate, p.reserve_yn,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, srr.*
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c, sellertool_regist_relation srr
			where p.id = srr.pid and c.company_id = p.admin and p.id = r.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).") and r.basic = 1 and admin ='".$admininfo[company_id]."' $where $orderbyString LIMIT $start, $max";


			//echo $sql;
			if(!empty($_SERVER["QUERY_STRING"])){
				$db->query($sql);
			}
		}

		//echo $sql;
	}else{
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

		if($admininfo[admin_level] == 9)
		{
			$sql = "SELECT p.id as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,c.com_name, r.cid, p.search_keyword,state, p.brand, p.brand_name, p.stock,
				p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate,  p.reserve_rate,p.reserve_yn,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, srr.*
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c , sellertool_regist_relation srr
				where p.id = srr.pid and c.company_id = p.admin and p.id = r.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).") and r.cid = '".$cid2."' $where $orderbyString LIMIT $start, $max";

			if(!empty($_SERVER["QUERY_STRING"]))
				$db->query($sql);

		}
		else
		{
			$sql = "SELECT p.id as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder,c.com_name,  r.cid, p.search_keyword,state, p.brand, p.brand_name, p.stock,
				p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate,  p.reserve_rate,p.reserve_yn,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp, srr.*
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c , sellertool_regist_relation srr
				where p.id = srr.pid and c.company_id = p.admin and p.id = r.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).") and r.cid = '".$cid2."' and admin ='".$admininfo[company_id]."' $where $orderbyString LIMIT $start, $max";

			if(!empty($_SERVER["QUERY_STRING"]))
				$db->query($sql);
		}
	}
}

if($mode == "search"){

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
	}


	if($admininfo[admin_level] == 9){
		$where = "where p.id = srr.pid and p.id = r.pid and r.basic = 1 AND p.product_type NOT IN (".implode(',',$sns_product_type).")  ";
	}else{
		$where = "where p.id = srr.pid and p.id = r.pid and r.basic = 1 AND p.product_type NOT IN (".implode(',',$sns_product_type).") and admin ='".$admininfo[company_id]."'  ";
	}

	if($pid != ""){
		$where = $where."and p.id = $pid ";
	}
	if($company_id != ""){
		$where = $where."and p.admin = '".$company_id."' ";

	}
	if($search_text != ""){
		$where = $where."and p.".$search_type." LIKE '%".trim($search_text)."%' ";
	}

	if($disp != ""){
		$where .= " and p.disp = ".$disp;
	}
    if($result_code != ""){
        $where .= " and srr.result_code = ".$result_code;
    }

	if($site_code != ""){
		$where = $where." and site_code = '".$site_code."' ";
	}

//echo $where;
	if($state2 != ""){
		//session_register("state");
		$where = $where." and p.state = ".$state2." ";
	}
	if($brand != ""){
		//session_register("brand");
		$where .= " and brand = ".$brand."";
	}

	if($brand_name != ""){
		$where .= " and p.brand_name LIKE '%".trim($brand_name)."%' ";
	}

	if($cid2 != ""){
		//session_register("cid");
		//session_register("depth");
		$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
	}else{
		$where .= "";
	}

	if($sdate != "" && $edate != ""){
		$where .= " and srr.regist_date between '".substr($sdate, 0, 4)."-".substr($sdate, 4, 2)."-".substr($sdate, 6, 2)." 00:00:00' and '".substr($edate, 0, 4)."-".substr($edate, 4, 2)."-".substr($edate, 6, 2)." 23:59:59' ";
		//$where .= " and  p.regdate between  '".substr($sdate, 0, 4)."-".substr($sdate, 4, 2)."-".substr($sdate, 6, 2)." 00:00:00' and '".substr($edate, 0, 4)."-".substr($edate, 4, 2)."-".substr($edate, 6, 2)." 23:59:59' ";
//		$where .= " and  date_format(p.regdate,'%Y%m%d') between  $sdate and $edate ";
	}

	$sql = "SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r , sellertool_regist_relation srr $where  ";
	//echo $sql;
	$db2->query($sql);

}else{

	if ($cid2 == ""){
		if($admininfo[admin_level] == 9){
			$addWhere = "Where p.id = srr.pid and p.id = r.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).")  ";
			if($company_id != ""){
				$addWhere .= " and admin ='".$company_id."'";
			}

			$sql = "SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r , sellertool_regist_relation srr   $addWhere ";

			if(!empty($_SERVER["QUERY_STRING"])){
				$db2->query($sql);
			}
		}else{
			$sql = "SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r , sellertool_regist_relation srr   where  p.id = r.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).") and admin ='".$admininfo[company_id]." '";

			if(!empty($_SERVER["QUERY_STRING"])){
				$db2->query($sql);
			}
		}


	}else{
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
		}
		if($admininfo[admin_level] == 9){
			$sql = "SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r , sellertool_regist_relation srr   where p.id = r.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).") and r.basic = 1 and r.cid LIKE '".substr($cid2,0,$cut_num)."%' ";

			if(!empty($_SERVER["QUERY_STRING"])){
				$db2->query($sql);
			}

		}else{
			$sql = "SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r , sellertool_regist_relation srr  where p.id = r.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).") and r.basic = 1 and r.cid LIKE '".substr($cid2,0,$cut_num)."%' and admin ='".$admininfo[company_id]."' ";

			if(!empty($_SERVER["QUERY_STRING"])){
				$db2->query($sql);
			}
		}

	}
}

$db2->fetch();
$total = $db2->dt[total];

if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&sprice=$sprice&eprice=$eprice&state2=$state2&disp=$disp&brand_name=$brand_name&cid2=$cid2&depth=$depth&company_id=$company_id&event=$event&best=$best&sale=$sale&wnew=$wnew&mnew=$mnew");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype");
	//echo $total.":::".$page."::::".$max."<br>";
}



$Contents =	"
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("제휴사 연동결과", "상품관리 > 제휴사 연동결과")."</td>
	</tr>

	";

$Contents .=	"
	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<input type='hidden' name='sprice' value='0' />
	<input type='hidden' name='eprice' value='1000000' />
	<tr height=150>
		<td colspan=2  >
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:1px'>
						<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>
							<tr>
								<td class='search_box_title'><b>카테고리선택</b></td>
								<td class='search_box_item' colspan=3>
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
											<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
											<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
											<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
										</tr>
									</table>
								</td>
							</tr>
							";
							if($admininfo[mall_type] != "F" && $admininfo[admin_level] == 9){
							$Contents .=	"
							<tr>
								<td class='search_box_title'><b>입점업체</b></td>
								<td class='search_box_item'>".CompanyList2($company_id,"")."</td>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item'>".BrandListSelect($brand, $cid)."</td>
							</tr>
							";
							}else{
							$Contents .=	"
							<tr>
								<td class='search_box_title'><b>브랜드</b></td>
								<td class='search_box_item' colspan=3>".BrandListSelect($brand, $cid)."</td>
							</tr>
							";
							}
								$Contents .=	"
							<tr>
								<td class='search_box_title'><b>진열</b></td>
								<td class='search_box_item'>
								<input type='radio' name='disp'  id='disp_' value='' ".ReturnStringAfterCompare($disp, "", " checked")."><label for='disp_'>전체</label>
								<input type='radio' name='disp'  id='disp_1' value='1' ".ReturnStringAfterCompare($disp, "1", " checked")."><label for='disp_1'>노출함</label>
								<input type='radio' name='disp'  id='disp_0' value='0' ".ReturnStringAfterCompare($disp, "0", " checked")."><label for='disp_0'>노출안함</label>
								</td>
								<td class='search_box_title'><b>쇼핑몰 판매상태</b></td>
								<td class='search_box_item'>
									<select name='state2' style='font-size:12px;'>
										<option value=''>상태값선택</option>
										<option value='1' ".ReturnStringAfterCompare($state2, "1", " selected").">판매중</option>
										<option value='0' ".ReturnStringAfterCompare($state2, "0", " selected").">일시품절</option>
										<option value='6' ".ReturnStringAfterCompare($state2, "6", " selected").">등록신청중</option>
										<option value='7' ".ReturnStringAfterCompare($state2, "7", " selected").">수정신청중</option>
										<option value='8' ".ReturnStringAfterCompare($state2, "8", " selected").">승인거부</option>
									</select>
								</td>
							</tr>
                            <tr>
                                <td class='search_box_title'><b>즉시할인여부</b></td>
                                <td class='search_box_item'>
                                    <input type='radio' name='coupon_yn'  id='coupon' value='' ".ReturnStringAfterCompare($coupon_yn, "", " checked")."><label for='coupon'>전체</label>
                                    <input type='radio' name='coupon_yn' id='coupon_y' value='' ".ReturnStringAfterCompare($coupon_yn,"Y"," checked")."><label for='coupon_y'>설정함</label>
                                    <input type='radio' name='coupon_yn' id='coupon_n' value='' ".ReturnStringAfterCompare($coupon_yn,"N"," checked")."><label for='coupon_n'>설정안함</label>
                                </td>
                                <td class='search_box_title'><b>제휴사 판매상태</b></td>
								<td class='search_box_item'>
									<select name='target_display' style='font-size:12px;'>
										<option value=''>상태값선택</option>
										<option value='onsale' ".ReturnStringAfterCompare($target_display, "onsale", " selected").">판매중</option>
										<option value='stop' ".ReturnStringAfterCompare($target_display, "stop", " selected").">판매중지</option>
									</select>
								</td>
                            </tr>
							<tr>
								<td class='search_box_title'><b>검색어</b></td>
								<td class='search_box_item' style='padding-right:5px;margin-top:3px;'>
									<table cellpadding=0 cellspacing=0 >
										<tr >
											<td>
											<select name='search_type'  style=\"font-size:12px;height:20px;\">
												<option value='pname'>상품명</option>
												<option value='pcode'>상품코드</option>
												<option value='id'>상품코드(key)</option>
											</select>
											</td>
											<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox1' value='".$search_text."' onclick='findNames();'  clickbool='false' style='height:16px;FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
											<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
												<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef>
													<tr height=20>
														<td width=100%  style='padding:0 0 0 5'>
															<table width=100% cellpadding=0 cellspacing=0 border=0>
																<tr>
																	<td class='p11 ls1'>검색어 자동완성</td>
																	<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:pointer;padding:0 10 0 0' align=right>닫기</td>
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
								<td class='search_box_title'><b>목록갯수</b></td>
								<td class='search_box_item'><select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
								<option value='5' ".CompareReturnValue(5,$max).">5</option>
								<option value='10' ".CompareReturnValue(10,$max).">10</option>
								<option value='20' ".CompareReturnValue(20,$max).">20</option>
								<option value='50' ".CompareReturnValue(50,$max).">50</option>
								<option value='100' ".CompareReturnValue(100,$max).">100</option>
								</select> <span class='small'><!--한페이지에 보여질 갯수를 선택해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
								</td>
							</tr>
							<tr height=27>
							  <td class='search_box_title'><b>연동 일자</b></td>
							  <td class='search_box_item' colspan=3 >
								<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
									<col width=70>
									<col width=20>
									<col width=70>
									<col width=*>
									<tr>
										<TD nowrap>
										<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
										<!--SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
										<SELECT name=FromDD></SELECT> 일 -->
										</TD>
										<TD align=center> ~ </TD>
										<TD nowrap>
										<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
										<!--SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
										<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
										<SELECT name=ToDD></SELECT> 일 -->
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
                                <td class='search_box_title'><b>연동 처리상태</b></td>
								<td class='search_box_item' >
                                    <select name=result_code style=\"font-size:12px;height: 20px;\" align=absmiddle>
    								    <option value='' >전체 </option>
										<option value='200' ".CompareReturnValue(200,$result_code).">성공 </option>
    								    <option value='500' ".CompareReturnValue(500,$result_code).">에러 </option>
										<option value='2001' ".CompareReturnValue(2001,$result_code).">실패 </option>
								    </select>
								</td>
								<td class='input_box_title'>제휴사 선택</td>
								<td class='input_box_item' >
									<table border=0 cellpadding=0 cellspacing=0>
										<tr>
											<td style='padding-right:5px;'>
												".getSellerToolSiteInfo($site_code)."
											</td>
										</tr>
									</table>
								</td>
                            </tr>
						</table>
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
			</table>
		</td>
	</tr>
	<tr >
		<td colspan=2 align=center style='padding:10px 0px'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
		</form>
	</tr>";

$Contents .=	"
	<tr>
		<td valign=top style='padding-top:33px;'>";

$Contents .= "
		</td>
		<form name=listform method=post action='regist_results.act.php' onsubmit='return SelectUpdate(this)' target='act'><!--onsubmit='return CheckDelete(this)' -->
		<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
		<input type='hidden' id='pid' value=''>
        <input type='hidden' name='act' value='$update_kind'>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0 >
				<tr height=30>
					<td align=left>
					상품수 : ".number_format($total)." 개
					</td>
					<td align=right>".$str_page_bar."</td>
					<td align=right>". ($mode=="search" ? "<a href='".$_SERVER[PHP_SELF]."?act=excel_save&best=$best&b_ix=$b_ix&brand_name=$brand_name&cid2=$cid2&cid0_1=$cid0_1&cid1_1=$cid1_1&cid2_1=$cid2_1&cid3_1=$cid3_1&company_id=$company_id&coupon_yn=$coupon_yn&depth=$depth&disp=$disp&eprice=$eprice&event=$event&max=$max&mnew=$mnew&mode=$mode&orderby=$orderby&ordertype=$ordertype&result_code=$result_code&sale=$sale&search_text=$search_text&search_type=$search_type&site_code=$site_code&sprice=$sprice&state2=$state2&target_display=$target_display&view=innerview&wnew=$wnew&sdate=$sdate&edate=$edate'><img align='absmiddle' src='../images/korea/btn_excel_save.gif' border=0></a>" : "") . "</td>
				</tr>
			</table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='3%'>
				<!--col width='10%'-->
				<col width='*'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='10%'>
				<col width='10%'>
				<col width='10%'>
				<col width='7%'>
				<col width='7%'>
				<tr bgcolor='#cccccc' align=center>
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<!--td class=m_td>상품코드</td>
					<td class=m_td>이미지</td-->
					<td class=m_td >상품정보</td>
					<td class=m_td nowrap>처리상태/메세지</td>
                    <td class=m_td>구분/제휴사</td>
					<td class=m_td nowrap>상품코드</td>
					<td class=m_td>판매상태</td>
					<td class=m_td>진열</td>
					<!--td class=m_td>공급가</td>
					<td class=m_td>적립금</td-->
					<td class=m_td>소비자가/즉시할인여부</td>
					<td class=m_td>판매가</td>
					<td class=m_td>재고</td>
					<td class=e_td>관리</td>
				</tr>
                ";

// ========================================================================
if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=11 align=center> <등록된 상품이 없습니다.> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</td></tr>";



}else{
    $_list = $db->fetchAll();
    $i = 0;
    foreach($_list as $_lt):
        $list[$i] = $_lt[srl_ix];
        $i++;
    endforeach;
    $list = json_encode($list);
    //if(!empty($list)){
    //print_r($list);
    //}
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$db->dt[id].".gif")){
		//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s"))) {
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s");
		//}else{
		//	$img_str = "../image/no_img.gif";
		//}

	$innerview .= "<tr bgcolor='#ffffff' height=31>
						<td class='list_box_td list_bg_gray' rowspan=2><input type=checkbox class=nonborder id='cpid' name='select_pid[]' value='".$db->dt[srl_ix]."'></td>
						<td class='list_box_td point' style='line-height:140%;padding:5px;' rowspan=2 >
							<table cellpadding=0 cellspacing=0 border=0 width=100% >
								<col width='60px'>
								<col width='*'>
								<tr>
									<td>
									<a href='../product/goods_input.php?id=".$db->dt[id]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], $LargeImageSize)."'  ><img src='".$img_str."' width=50 height=50 style='border:1px solid #efefef'></a>
									</td>
									<td align=left style='font-weight:normal'>
										<".($db->dt[pcode] != "" ? "상품코드 : <b>".$db->dt[pcode]."</b>":"")."><br/> ".getCategoryPathByAdmin($db->dt[cid], 4)."<br>
										<a href='../product/goods_input.php?id=".$db->dt[id]."&mode=$mode&nset=$nset&page=$page&cid2=$cid2&depth=$depth&company_id=$company_id&brand2=$brand2&max=$max&state2=$state2&disp=$disp&search_type=$search_type&search_text=".trim($search_text)."&onew=$onew&best=$best&sale=$sale&event=$event&wnew=$wnew&mnew=$mnew' target='_blank'><b> ".($db->dt[brand_name] ? "[".$db->dt[brand_name]."]":"")." ".$db->dt[pname]."</b></a>";
		if($db->dt[result_code] != "200"){
										$innerview .= "<br><br><b style='color:red;'>".$db->dt[result_msg]."</b>";
										$sql = "select * from sellertool_regist_fail where site_code='".$site_code."' and pid = '".$db->dt[id]."' ";
										$db2->query($sql);
										if($db2->total){
											$db2->fetch();
											$innerview .= "<br><br><input type='button' value='전문보기' onclick=\"window.open('".$db2->dt[api_url].$db2->dt[data_url]."', '_blank')\">
											<input type='button' value='XML데이터보기' onclick=\"window.open('".str_replace('&dataUrl=','',$db2->dt[data_url])."', '_blank')\">
											<input type='button' value='삭제' onclick=\"fail_data_delete('".$db->dt[site_code]."','".$db->dt[id]."')\" />";
										}
		}
		$innerview .= "
									</td>
								</tr>
							</table>
					</td>
					<td align=center class='small' rowspan=2>";

						if($db->dt[result_code] == "200"){
							$innerview .= "성공";
						}else{
							$innerview .= "에러";

						}


$innerview .= "</td>
                    <td class='list_box_td ' nowrap>
					쇼핑몰
					</td>
					<td class='list_box_td ' nowrap>
					".$db->dt[id]."
					</td>


					<td class='list_box_td ' >";
						if($db->dt[state] == 1){
							$innerview .= "판매중";

						}else if($db->dt[state] == 6){
							$innerview .= "등록신청중";
						}else if($db->dt[state] == 7){
							$innerview .= "수정신청중";
						}else if($db->dt[state] == 8){
							$innerview .= "승인거부";
						}else if($db->dt[state] == 0){
							$innerview .= "일시품절중";
						}

$innerview .= "					</td>
					<td align=center class='small'>";

						if($db->dt[disp] == 1){
							$innerview .= "진열함";
						}else if($db->dt[disp] == 0){
							$innerview .= "진열안함";
						}

$innerview .= "</td>";

/*
$innerview .= "
					<td class='list_box_td list_bg_gray'>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td ' style='line-height:150%;'>";
if($db->dt[reserve_yn] == "Y"){
	$innerview .= "		<b>개별적용</b><br>";
}else{
	$innerview .= "		<b>전체정책</b><br>";
}
if ($db->dt[reserve_yn] == "Y"){
	$innerview .= "		".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[reserve])." ".$currency_display[$admin_config["currency_unit"]]["back"]."";
}else{
		$innerview .= "		".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[sellprice]*$reserve_data[goods_reserve_rate] /100)." ".$currency_display[$admin_config["currency_unit"]]["back"]."";
	}

$innerview .= "
					</td>";
*/

if($db->dt[target_display]=='onsale'){
	$target_display = '판매중';
}else{
	$target_display = '판매중지';
}


if($db->dt[target_coupon_yn] == 'Y'){
    $target_coupon_yn = '설정함';
}else{
    $target_coupon_yn = '설정안함';
}

$innerview .= "
					<td class='list_box_td ' nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[listprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td ' style='padding:0px 5px;' nowrap>
					".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."
					</td>
					<td class='list_box_td' nowrap>".$db->dt[stock]."</td>
					<td class='list_box_td' nowrap>
						<table cellspaing=0 cellpadding=0 style='margin:0; padding:0;' width='100%'>
							<tr >
								<td><a href='/shop/goods_view.php?cid=".$db->dt[cid]."&id=".$db->dt[id]."&depth=3&b_ix=".$db->dt[brand]."' target='_blank'>
								<img src='../images/".$admininfo["language"]."/btn_preview.gif'></a></td>
							</tr>
						</table>
					</td>
				</tr>
                <tr height=31>
                    <td align=center class='list_bg_gray'>".$db->dt[site_code]."</td>
                    <td align=center class='list_bg_gray'>".$db->dt[result_pno]."</td>
                    <td align=center class='list_bg_gray'>
						";
						if($site_code == 'gmarket' && $db->dt[result_code] == '200' ){
							$innerview .= "
							<span id='TradeStatus_".$db->dt[result_pno]."'></span>
							<input type='button' onclick=\"display_check('".$db->dt[site_code]."','".$db->dt[result_pno]."')\" value='상태확인' />";
							
						}else{
							$innerview .= "
							".$target_display."
							";
						}
					$innerview .="
					</td>
                    <td align=center class='list_bg_gray'>N/A</td>
                    <td align=center class='list_bg_gray'>".$target_coupon_yn."</td>
                    <td align=center class='list_bg_gray' id='target_price_".$db->dt[result_pno]."'>".number_format($db->dt[target_price])." 원</td>
					<td align=center class='list_bg_gray' id='stock_".$db->dt[result_pno]."'>-</td>
                    <td align=center class='list_box_td list_bg_gray'>
                        <table cellspaing=0 cellpadding=0 style='margin:0; padding:0;' width='100%'>
							<tr>";
                        if($db->dt[result_code] == '200'){
								if($site_code == 'gmarket'){
									$link_domain = "http://item.gmarket.co.kr/detailview/Item.asp?goodscode=";
								}else if($site_code == '11st'){
									$link_domain = "http://www.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=";
								}else if($site_code == 'interpark_api'){
									$link_domain = "http://www.interpark.com/product/MallDisplay.do?_method=Detail&sc.prdNo=";
								}else{
									$link_domain = "";
								}
								$innerview .="
								<td>";
									if($link_domain){
									$innerview .="
                                    <a href='".$link_domain.$db->dt[result_pno]."' target='_blank'>";
									}else{
									$innerview .="
									<a href='javascript:void(0)' onclick=\"alert('링크가 존재하지 않습니다.')\">	";	
									}
									$innerview .="
								    <img src='../images/".$admininfo["language"]."/btn_preview.gif'></a>
                                </td>";
                        }
                        if($db->dt[result_code] == '500' || $db->dt[result_code] == '2001'){
                            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                                $innerview .=
                                "<td>
                                    <img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 onclick=\"delete_log(".$db->dt[srl_ix].");\">
                                </td>";
                            }else{
                                $innerview .=
                                "<td>
                                    <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='cursor:pointer' border=0 ></a>
                                </td>";
                            }
                        }
    $innerview .=
							"</tr>

						</table>
                    </td>
                </tr>";
	}
}
	$innerview .= "</table>
				<table width='100%'>
				<tr height=30>
					<td width=210 id='updateStatus_btn'>
                        <a href='javascript:' onclick='updateStatus();'>현재 페이지 상품상태 업데이트</a>
					</td>
					<td align=right>".$str_page_bar."</td>
					<td align=right>". ($mode=="search" ? "<a href='".$_SERVER[PHP_SELF]."?act=excel_save&best=$best&b_ix=$b_ix&brand_name=$brand_name&cid2=$cid2&cid0_1=$cid0_1&cid1_1=$cid1_1&cid2_1=$cid2_1&cid3_1=$cid3_1&company_id=$company_id&coupon_yn=$coupon_yn&depth=$depth&disp=$disp&eprice=$eprice&event=$event&max=$max&mnew=$mnew&mode=$mode&orderby=$orderby&ordertype=$ordertype&result_code=$result_code&sale=$sale&search_text=$search_text&search_type=$search_type&site_code=$site_code&sprice=$sprice&state2=$state2&target_display=$target_display&view=innerview&wnew=$wnew&sdate=$sdate&edate=$edate'><img align='absmiddle' src='../images/korea/btn_excel_save.gif' border=0></a>" : "") . "</td>
				</tr>
				<tr height=30><td colspan=2 align=right></td></tr>
				</table>

				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>
		<!--iframe id='act' src='' width=0 height=0></iframe-->
			";

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small'>
	<col width=8>
	<col width=*>
	<tr>
		<td valign=top><img src='/admin/image/icon_list.gif'></td><td class='small'>승인을 하시고자 하는 상품을 선택하신후 일괄정보 수정을 하실 수 있습니다. </td>
	</tr>
	<tr>
		<td valign=top><img src='/admin/image/icon_list.gif'></td><td class='small'>승인여부 와 진열여부를 선택하신후 <img src='../image/bt_all_modify.gif' align=absmiddle> 버튼을 하실 수 있습니다</td>
	</tr>
</table>
";
*/
$help_text = "
<div style='z-index:-1;position:absolute;' id='select_update_parent_save_loading'>
<div style='width:700px;height:200px;display:block;position:relative;z-index:10;text-align:center;' id='select_update_save_loading'></div>
</div>
<div id='batch_update_display' ".($update_kind == "display" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0 4px 0'><img src='../images/dot_org.gif'> <b>판매 상태 변경</b> <span class=small style='color:gray'><!--변경하시고자 하는 판매/진열 상태를 선택후 저장 버튼을 클릭해주세요--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span></div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>판매상태 </b></td>
		<td class='input_box_item'>
		<input type='radio' name='display' id='c_disp_0' value='stop'  ".(($disp == "stop" || $disp == "") ? "checked":"")."><label for='c_disp_0'>판매중지</label>
		<input type='radio' name='display' id='c_disp_1' value='restart'  ".($disp == "restart" ? "checked":"")."><label for='c_disp_1'>판매중지 해제</label>
		</td>
	</tr>
	</table>";
if(checkMenuAuth(md5("/admin/sellertool/regist_results.php"),"U") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table>";
}else{
	$help_text .= "<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>";
}
$help_text .= "

</div>
<div id='batch_update_price' ".($update_kind == "price" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0px 4px 0px'><img src='../images/dot_org.gif'> <b>판매가격 수정</b> <span class=small style='color:gray'><!--변경하시고자 하는 적립금정보를 선택 후 저장 버튼을 클릭해 주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I')."</span></div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>판매가격</b></td>
		<td class='input_box_item'>
		<table cellpadding=3 cellspacing=0>
			<tr>
				<td > 기존 판매가격을
				<input type=text class='textbox1' name=editValue size=13 style='text-align:right' onkeypress='onlyEditableNumber(this)' onkeyup='onlyEditableNumber(this)' value='$editValue'>
				<select id='' name='editType'>
					<option value='won'>원</option>
					<option value='percent'>%</option>
				</select>
                <select id='' name='editKind'>
					<option value='up'>인상</option>
					<option value='down'>인하</option>
				</select>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
    <ul class=''>
        <li>판매가는 10원 단위로, 최대 10억 원 미만으로 입력 가능합니다.</li>
        <li>판매가 정보 수정 시, 최대 50% 인상/80% 인하까지 수정하실 수 있습니다.</li>
        <li>서비스이용료는 카테고리/판매가에 따라 다르게 적용될 수 있습니다.</li>
        <li><span class=''>11번가와 협의하여 11번가의 쿠폰 또는 서비스이용료 조정이 적용된 상품의 경우, 판매자 기본즉시할인이 적용된 가격이 상향 되면 조정된 내용이 강제로 종료 됩니다.</span></li>
    </ul>
";
if(checkMenuAuth(md5("/admin/sellertool/regist_results.php"),"U") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table>";
}else{
	$help_text .= "<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>";
}
$help_text .= "
</div>

<div id='batch_update_coupon' ".($update_kind == "coupon" || $update_kind == "" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4px 0px 4px 0px'><img src='../images/dot_org.gif'> <b>판매가격 수정</b> <span class=small style='color:gray'><!--변경하시고자 하는 적립금정보를 선택 후 저장 버튼을 클릭해 주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'I')."</span></div>
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width=160>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>즉시할인</b></td>
		<td class='input_box_item'>
		<table cellpadding=3 cellspacing=0>
			<tr>
    			<td>
    				<div class=''>
    				<label class=''><input type='radio' class='radio' value='N' name='cuponcheck' id='dscAmtUseYn' onClick='changeDscAmtUseYn(this.value);' checked><span class=''>설정안함</span></label>
    				<label class=''><input type='radio' class='radio' value='Y' name='cuponcheck' id='dscAmtUseYn' onClick='changeDscAmtUseYn(this.value);'><span class=''>설정함</span></label>
                    <br/>
    				<label class=''>판매가에서 <input type='text' class='ipt' name='dscAmtPercnt' id='dscAmtPercnt' style='width:50px;' maxlength='10'>
    				<select class='' name='cupnDscMthdCd' id='cupnDscMthdCd'>
    					<option value='01'>원</option>
    	                <option value='02'>%</option>
    				</select> 할인</label><br/>
    				<label class=''><input type='checkbox' class='' name='cupnUseLmtDyYn' id='cupnUseLmtDyYn' value='Y' onClick='checkCupnUseLmtDy();'><span class=''>할인기간 설정</span></label>
    				<label><input type='text' class='' name='cupnIssStartDy' id='cupnIssStartDy' style='width:62px;' value='' readOnly>~</label>
    				<label><input type='text' class='' name='cupnIssEndDy' id='cupnIssEndDy' style='width:62px;' value='' ></label>

    				</div>
    			</td>

			</tr>
			</table>
		</td>
	</tr>
	</table>
    <ul class=''>
        <li>설정되어 있는 즉시할인을 중단하시려면 ‘설정안함’을 선택하시면 됩니다.</li>
        <li>기본즉시할인은 판매자가 부담하는 할인으로 할인금액/할인기간을 설정하실 수 있습니다.</li>
        <li><span class=''>11번가와 협의하여 11번가의 쿠폰 또는 서비스이용료 조정이 적용된 상품의 경우, 판매자 기본즉시할인이 적용된 가격이 상향 되면 조정된 내용이 강제로 종료 됩니다.</span></li>
    </ul>
";
if(checkMenuAuth(md5("/admin/sellertool/regist_results.php"),"U") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
	</table>";
}else{
	$help_text .= "<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>";
}
$help_text .= "
</div>
";
//TODO:검색한상품 전체 기능 수정하기
$select = "
<select name='update_type' >
					<option value='2' selected>선택한 상품 전체에</option>
					<!--option value='1' >검색한 상품 전체에</option-->

				</select>
				<input type='radio' name='update_kind' id='update_kind_display' value='display' ".CompareReturnValue("display",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_display');\"><label for='update_kind_display'>판매 상태 일괄 변경</label>
				<input type='radio' name='update_kind' id='update_kind_price' value='price' ".CompareReturnValue("price",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_price');\"><label for='update_kind_price'>판매 가격 수정</label>
                <input type='radio' name='update_kind' id='update_kind_coupon' value='coupon' ".CompareReturnValue("coupon",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_coupon');\"><label for='update_kind_coupon'>즉시할인 설정</label>
				";

$Contents .= "".HelpBox($select, $help_text,600)."</form>";

$Script = "
<script language='javascript'>

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

function unloading(){

	parent.document.getElementById('parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('loadingbar').innerHTML ='';
	parent.document.getElementById('save_loading').innerHTML ='';
	parent.document.getElementById('save_loading').style.display = 'none';
}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_display','batch_update_price','batch_update_coupon'); //,'batch_update_sms','batch_update_coupon'
    var frm = document.listform;
	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
            if(selected_id == 'batch_update_coupon'){
                changeDscAmtUseYn('N');
            }
            frm.act.value = selected_id.replace('batch_update_','');
			$.cookie('goodsinfo_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}

function changeDscAmtUseYn(value){
	var frm = document.listform;

	if(value == 'Y') {
		document.getElementById('dscAmtPercnt').disabled = false;
		document.getElementById('cupnDscMthdCd').disabled = false;
		document.getElementById('cupnUseLmtDyYn').disabled = false;
		document.getElementById('cupnIssStartDy').disabled = false;
		document.getElementById('cupnIssEndDy').disabled = false;
	} else {
		document.getElementById('dscAmtPercnt').disabled = true;
		document.getElementById('cupnDscMthdCd').disabled = true;
		document.getElementById('cupnUseLmtDyYn').disabled = true;
		document.getElementById('cupnIssStartDy').disabled = true;
		document.getElementById('cupnIssEndDy').disabled = true;
	}

	frm.dscAmtPercnt.value = '';
	frm.cupnIssStartDy.value = '';
	frm.cupnIssEndDy.value = '';
	document.getElementById('cupnDscMthdCd').options[0].selected = true;
	document.getElementById('cupnUseLmtDyYn').checked = false;

}
//쿠폰지급기간 설정 시 Default 값
var dfltCupnIssStartDy	= '".$coupon_sdate."';
var dfltCupnIssEndDy = '".$coupon_edate."';
//즉시할인쿠폰 지급기간 설정
function checkCupnUseLmtDy()
{
    var frm = document.listform;
	if(frm.cupnUseLmtDyYn.checked){
		frm.cupnIssStartDy.value = dfltCupnIssStartDy;
		frm.cupnIssEndDy.value = dfltCupnIssEndDy;
	}else{
		frm.cupnIssStartDy.value='';
		frm.cupnIssEndDy.value='';
	}
}


function updateStatus(){
    var list = ".$list.";

    var act = 'updateStatus';

    $.ajax({
        url : 'regist_results.act.php',
        type : 'POST',
        data : {act:act,list:list},
        dataType: 'html',
        //cache:true,
        error: function(data,error){// 실패시 실행
            alert(error);},
        success: function(transport){
            if(transport == 'success'){
                alert('업데이트 완료');
                location.reload();
            }else{
                alert('업데이트 실패');
            }
        }
    });
}
//log삭제
function delete_log(srl_ix){
    var act = 'delete_log';
    var idx = srl_ix;

    $.ajax({
        url : 'regist_results.act.php',
        type : 'POST',
        data : {act:act,srl_ix:idx},
        dataType : 'html',
        error : function(data,error){
            alert(error);},
        success : function(transport){
            if(transport == 'success'){
                alert('삭제 완료');
                location.reload();
            }else{
                alert('삭제 실패');
            }
        }
    });
}


function fail_data_delete(site_code,pid){
	if(confirm('전문데이터를 삭제하시겠습니까?')){
		$.ajax({
			url : 'regist_results.act.php',
			type : 'POST',
			data : {act:'fail_data_delete',pid:pid, site_code:site_code},
			dataType : 'html',
			error : function(data,error){
				alert(error);},
			success : function(transport){
				console.log(transport)
				if(transport == 'success'){
					alert('삭제 완료');
					location.reload();
				}else{
					alert('삭제 실패');
				}
			}
		});
	}
}

function display_check(site_code, sell_id){
	//if(confirm('상태정보를 가져오시겠습니까?')){
		$.ajax({
			url : 'regist_results.act.php',
			type : 'POST',
			data : {act:'display_check',sell_id:sell_id, site_code:site_code},
			dataType : 'json',
			error : function(data,error){
				alert(error);},
			success : function(transport){
			
				if(transport != ''){
					var TradeStatus;
					if(transport.TradeStatus == 'Standby'){
						TradeStatus = '거래대기';
					}else if(transport.TradeStatus == 'Active'){
						TradeStatus = '거래가능';
					}else if(transport.TradeStatus == 'Suspended'){
						TradeStatus = '거래중지';
					}else if(transport.TradeStatus == 'Discontinued'){
						TradeStatus = '거래폐지';
					}else if(transport.TradeStatus == 'Restricted'){
						TradeStatus = '제한상품';
					}
					$('#target_price_'+transport.GmktItemNo).html(transport.SellPrice);
					$('#stock_'+transport.GmktItemNo).html(transport.StockQty);
					$('#TradeStatus_'+transport.GmktItemNo).html(TradeStatus);
					//alert('완료');
					//location.reload();
				}else{
					alert('실패');
				}
			}
		});
	//}
}
	
</script>

";


//$Contents .= HelpBox("제휴사 연동결과", $help_text);
$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid2, $depth);
	echo "
	<Script>
	//alert(document.body.innerHTML);
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	try{
	parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."\" ;
	}catch(e){}
	parent.document.search_form.cid2.value ='$cid2';
	parent.document.search_form.depth.value ='$depth';

	</Script>";
}else{
	$Script .= "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<script Language='JavaScript' src='sellertool.js'></script>
	<!--script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script-->
	<script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//alert(depth);
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//alert(1);
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	function loadChangeCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;
		//var depth = sel.getAttribute('depth');
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)

		//dynamic.src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		window.frames['act'].location.href = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	</script>";

	$P = new LayOut();
	$P->strLeftMenu = sellertool_menu();
	$P->addScript = $Script;
	$P->Navigation = "제휴사 연동 > 제휴사 연동결과";
	$P->title = "제휴사 연동결과";
	$P->strContents = $Contents;
	$P->jquery_use = false;

	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}
?>