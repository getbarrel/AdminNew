<?php

	include("../class/layout.class");
	include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');
	include '../include/phpexcel/Classes/PHPExcel.php';
	
	//error_reporting(E_ALL);
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
	
	date_default_timezone_set('Asia/Seoul');
	
	$db = new Database;
	if($orderby != "" && $ordertype != ""){
		$orderbyString = " group by p.id   order by $orderby $ordertype ";
	}else{
		$orderbyString = " group by p.id   order by r.regdate desc ";
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
		$where = "";
		if($search_text != ""){
			$where .= "and p.".$search_type." LIKE '%".trim($search_text)."%' ";
		}
	
		if($sprice && $eprice){
			$where .= "and sellprice between $sprice and $eprice ";
		}
	
		if($status_where){
			$where .= " and ($status_where) ";
		}
		if($brand2 != ""){
			$where .= " and brand = ".$brand2."";
		}
	
		if($brand_name != ""){
			$where .= " and brand_name LIKE '%".trim($brand_name)."%' ";
		}
	
		if($disp != ""){
			$where .= " and p.disp = ".$disp;
		}
	
		if($one_commission){
			$where .= " and p.one_commission = '".$one_commission."'";
		}
	
	
	
		if($state2 != ""){
			$where .= " and state = ".$state2."";
		}
	
	
		if($cid2 != ""){
			$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
		}else{
			$where .= "";
		}
		if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$addWhere = "and admin ='".$company_id."'";
			}else{
				unset($addWhere);
			}
			$sql = "SELECT distinct p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name, csd.delivery_price,  p.basicinfo,
			p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate,  case when p.one_commission = 'Y' then p.commission else csd.commission end as commission , p.etc2, p.one_commission, p.commission as goods_commission , csd.commission as company_commission,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp
			FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
			where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid  $addWhere $where $orderbyString $limit_string";
			//echo $sql;
			$db->query($sql);
		}else{
			$sql = "SELECT distinct p.id, p.pname,p.brand, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name, csd.delivery_price,  p.basicinfo,
			p.company, p.pcode, p.coprice, p.listprice,p.disp, p.editdate, p.reserve, p.reserve_rate, case when p.one_commission = 'Y' then p.commission else csd.commission end as commission, p.etc2, p.one_commission, p.commission as goods_commission , csd.commission as company_commission,
			case when vieworder = 0 then 100000 else vieworder end as vieworder2
			FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
			where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid  and admin ='".$admininfo[company_id]."' $where $orderbyString $limit_string";
	
	
			$db->query($sql);
		}
		//echo $sql;
	}else{
	
		if ($cid2 == ""){
			if($admininfo[admin_level] == 9){
				if($company_id != ""){
					$addWhere = "and admin ='".$company_id."'";
				}else{
					unset($addWhere);
				}
				//$tmp_sql = "create temporary table ".TBL_LOGSTORY_BYPAGE."_tmp ENGINE = MEMORY select vdate, pageid, ncnt, nduration from ".TBL_SHOP_PRODUCT_RELATION." where vdate = '$vdate' ";
	
				$sql = "SELECT distinct (p.id) as id, p.pname, p.brand,p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name, c.delivery_price,  p.basicinfo,
				p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate,case when p.one_commission = 'Y' then p.commission else c.commission end as commission, p.etc2,p.one_commission, p.commission as goods_commission , c.commission as company_commission,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp
				FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
				where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid $where $addWhere $orderbyString $limit_string";
				//echo $sql;
				$db->query($sql);
			}else{
				$sql = "SELECT distinct (p.id) as id ,p.brand, p.pname, p.sellprice, p.regdate,p.vieworder,c.com_name,  r.cid, p.stock,  p.search_keyword,state, p.brand_name,  c.delivery_price,  p.basicinfo,
				p.company, p.pcode, p.coprice, p.listprice,  p.disp, p.editdate, p.reserve, p.reserve_rate, case when p.one_commission = 'Y' then p.commission else c.commission end as commission, p.etc2,p.one_commission, p.commission as goods_commission , c.commission as company_commission,
				case when vieworder = 0 then 100000 else vieworder end as vieworder2
				FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c,  ".TBL_COMMON_SELLER_DELIVERY." csd
				where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid and admin ='".$admininfo[company_id]."' $where $orderbyString $limit_string";
	
	
				//echo $sql;
				$db->query($sql);
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
	
			if($admininfo[admin_level] == 9){
				$sql = "SELECT distinct (p.id) as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder, r.cid, p.stock, p.search_keyword,state, p.brand, b.brand_name,  p.size, p.natural_item,  c.delivery_price,  p.delivery_type, p.basicinfo,
					p.company, p.pcode, p.coprice, p.listprice, p.disp, p.editdate,  p.reserve_rate,  case when p.one_commission = 'Y' then p.commission else c.commission end as commission, p.etc2, p.one_commission, p.commission as goods_commission , c.commission as company_commission,
					case when vieworder = 0 then 100000 else vieworder end as vieworder2,p.disp
					FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_SNS_BRAND." b,  ".TBL_COMMON_SELLER_DELIVERY." csd
					where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid and p.brand = b.b_ix and r.cid = '".$cid2."' $where $orderbyString $limit_string";
	
				//echo $sql;
	
				$db->query($sql);
			}else{
				$sql = "SELECT distinct (p.id) as id, p.pname, p.sellprice, p.regdate,c.com_name,p.vieworder, r.cid, p.stock, p.search_keyword,state, p.brand, b.brand_name,  p.size, p.natural_item,  c.delivery_price, p.delivery_type, p.basicinfo,
					p.company, p.pcode, p.coprice, p.listprice,   p.disp, p.editdate,  p.reserve_rate, case when p.one_commission = 'Y' then p.commission else c.commission end as commission, p.etc2, p.one_commission, p.commission as goods_commission , c.commission as company_commission,
					case when vieworder = 0 then 100000 else vieworder end as vieworder2
					FROM ".TBL_SNS_PRODUCT." p, ".TBL_SNS_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_SNS_BRAND." b,  ".TBL_COMMON_SELLER_DELIVERY." csd
					where c.company_id = p.admin and c.company_id = csd.company_id and p.id = r.pid and p.brand = b.b_ix and r.cid = '".$cid2."' and admin ='".$admininfo[company_id]."' $where $orderbyString $limit_string";
	
					//echo $sql;
					$db->query($sql);
	
					//echo "test".$db->total;
	
			}
		}
	}
	
	$productListXL = new PHPExcel();
	
	// 속성 정의
	$productListXL->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("Product List")
								 ->setSubject("Product List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("Product List");
								 
	// 데이터 등록
	$productListXL->getActiveSheet(0)->setCellValue('A' . 1, "상품명");
	$productListXL->getActiveSheet(0)->setCellValue('B' . 1, "상품코드");
	$productListXL->getActiveSheet(0)->setCellValue('C' . 1, "업체명");
	$productListXL->getActiveSheet(0)->setCellValue('D' . 1, "원산지");
	$productListXL->getActiveSheet(0)->setCellValue('E' . 1, "브랜드");
	$productListXL->getActiveSheet(0)->setCellValue('F' . 1, "재고량");
	$productListXL->getActiveSheet(0)->setCellValue('G' . 1, "유사검색어");
	$productListXL->getActiveSheet(0)->setCellValue('H' . 1, "옵션1");
	$productListXL->getActiveSheet(0)->setCellValue('I' . 1, "공급가");
	$productListXL->getActiveSheet(0)->setCellValue('J' . 1, "정가");
	$productListXL->getActiveSheet(0)->setCellValue('K' . 1, "판매가(할인가)");
	$productListXL->getActiveSheet(0)->setCellValue('L' . 1, "수수료");
	$productListXL->getActiveSheet(0)->setCellValue('M' . 1, "배송선택");
	$productListXL->getActiveSheet(0)->setCellValue('N' . 1, "배송료");
	$productListXL->getActiveSheet(0)->setCellValue('O' . 1, "기본이미지");
	$productListXL->getActiveSheet(0)->setCellValue('P' . 1, "상세정보");
	
	//------ row 1 data ------ 
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		
		$productListXL->getActiveSheet()->setCellValue('A' . ($i + 2), $db->dt[pname]);
		$productListXL->getActiveSheet()->setCellValue('B' . ($i + 2), $db->dt[pcode]);
		$productListXL->getActiveSheet()->setCellValue('C' . ($i + 2), $db->dt[com_name]);
		$productListXL->getActiveSheet()->setCellValue('D' . ($i + 2), $db->dt[company]);
		$productListXL->getActiveSheet()->setCellValue('E' . ($i + 2), $db->dt[brand]);
		$productListXL->getActiveSheet()->setCellValue('F' . ($i + 2), $db->dt[stock]);
		$productListXL->getActiveSheet()->setCellValue('G' . ($i + 2), $db->dt[search_keyword]);
		$productListXL->getActiveSheet()->setCellValue('H' . ($i + 2), $db->dt[option_text]);
		$productListXL->getActiveSheet()->setCellValue('I' . ($i + 2), $db->dt[coprice]);
		$productListXL->getActiveSheet()->setCellValue('J' . ($i + 2), $db->dt[listprice]);
		$productListXL->getActiveSheet()->setCellValue('K' . ($i + 2), $db->dt[sellprice]);
		$productListXL->getActiveSheet()->setCellValue('L' . ($i + 2), $db->dt[one_commission]);
		$productListXL->getActiveSheet()->setCellValue('M' . ($i + 2), $db->dt[delivery_type]);
		$productListXL->getActiveSheet()->setCellValue('N' . ($i + 2), $db->dt[delivery_price]);
		$productListXL->getActiveSheet()->setCellValue('O' . ($i + 2), "http://".$HTTP_HOST.$admin_config[mall_data_root]."/images/product/b_".$db->dt[id].".gif");
		$productListXL->getActiveSheet()->setCellValue('P' . ($i + 2), $db->dt[basicinfo]);
		
	}
	
	// 시트이름등록
	$productListXL->getActiveSheet()->setTitle('상품리스트');
	
	// 첫번째 시트 선택
	$productListXL->setActiveSheetIndex(0);
	$productListXL->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$productListXL->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="product_list.xls"');
	header('Cache-Control: max-age=0');


	$objWriter = PHPExcel_IOFactory::createWriter($productListXL, 'Excel5');
	$objWriter->save('php://output');

	exit;