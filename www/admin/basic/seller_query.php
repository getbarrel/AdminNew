<?

	if(!$max){
		$max = 15; //페이지당 갯수
	}
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	//echo substr_count($_SERVER["REQUEST_URI"],"member_black_list");

	if($search_type == "company"){
		if($search_text){
			$sql = "select
					ccr.relation_code 
					from
						common_company_detail as ccd
						inner join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
					where
						ccd.com_name like '%".$search_text."%'";

			$db->query($sql);
			$db->fetch();

			if($db->dt[relation_code]){
			$search_code = $db->dt[relation_code];
			}else{
			$search_code = "a";
			}
		}
		$where .= " and ccr.relation_code like '".$search_code."%' ";
	}else if ($search_type == "ccd.com_name"){
		
		if($search_text){

			if($search_type == "ccd.com_name"){
				$where .= " and ccd.com_name LIKE  '%".$search_text."%' ";
			}else{
				$where .= " and ".$search_type." = '".$search_text."' ";
			}
		}
	} else if($search_type=="company_code") {
		if($search_text){
			$where.=" AND ccd.company_code LIKE '%".$search_text."%' ";
			$count_where .= " AND ccd.company_code like '%".$search_text."%' ";
		}
	}else if($search_type=="personin") {
		if($search_text){
			$where.=" AND ccd.tax_person_name LIKE '%".$search_text."%' ";
			$count_where .= " AND ccd.tax_person_name like '%".$search_text."%' ";
		}
	}else if($search_type=="com_number") {
		if($search_text){
			$where.=" AND ccd.com_number LIKE '%".$search_text."%' ";
			$count_where .= " AND ccd.com_number like '%".$search_text."%' ";
		}
	}else if($search_type=="com_ceo") {
		if($search_text){
			$where.=" AND ccd.com_ceo LIKE '%".$search_text."%' ";
			$count_where .= " AND ccd.com_ceo like '%".$search_text."%' ";
		}
	}else if($search_type=="customer_name") {
		if($search_text){
			$where.=" AND ccd.customer_name LIKE '%".$search_text."%' ";
			$count_where .= " AND ccd.customer_name like '%".$search_text."%' ";
		}
	}else if($search_type=="com_phone") {
		if($search_text){
			$where.=" AND ccd.com_phone LIKE '%".$search_text."%' ";
			$count_where .= " AND ccd.com_phone like '%".$search_text."%' ";
		}
	}else if($search_type!="" && $search_text!=""){
			$where.=" AND ".$search_type." LIKE '%".$search_text."%' ";
			$count_where .= " AND ".$search_type." like '%".$search_text."%' ";
	}


	if($cid2 != "" && false){
		$sql = "select company_id from common_company_relation where relation_code = '".$cid2."' ";
		$db->query($sql);
		$db->fetch();
		$search_comapny_id = $db->dt[company_id];
		$where .= " and ccd.company_id = '".$search_comapny_id."' ";
	}

	if($cid2 != ""){	//사업장검색
		$where .= " and ccr.relation_code like '".$cid2."%' ";
	}

	if($com_person != ""){	//담당자
		$where .= " and ccd.person = '".$com_person."' ";
	}

	if($nationality != ""){	//국내외 구분
		$where .= " and csd.nationality = '".$nationality."' ";
	}

	if($com_div != ""){		//사업자유형
		$where .= " and ccd.com_div = '".$com_div."' ";
	}

	if($seller_level != ""){	//셀러레벨
		$where .= " and csd.seller_level = '".$seller_level."' ";
	}

	if(is_array($seller_type)){	//거래처유형
		if($seller_type[0] !="a"){
			$where .= " and (";
			foreach($seller_type as $key =>$value){
				if($value){
					$where .= "ccd.seller_type like'%".$value."%'";
					if(($key == 0 and count($seller_type) != 1) or $key != count($seller_type)-1){
						$where .= " or ";
					}
				}
			}
			$where .=")";
		}
	}

	if(substr_count($_SERVER["PHP_SELF"],"/inventory/supply_vendor_list.php")>0 || substr_count($_SERVER["PHP_SELF"],"/inventory/sales_vendor_list.php")>0) {//수정 kbk 13/08/09
		if($sdate != "" && $edate != "" && $regdate == "1"){
			if($db->dbms_type == "oracle"){
				$where .= " and  to_char(csd.regdate , 'YYYY-MM-DD') between  ".str_replace("-","",$sdate)." and ".str_replace("-","",$edate)." ";
			}else{
				$where .= " and  date_format(csd.regdate,'%Y-%m-%d') between ".str_replace("-","",$sdate)." and ".str_replace("-","",$edate)." ";
			}
		}
	} else {
		if($_REQUEST['regdate'] == '1'){
			if($sdate != "" && $edate != ""){
				if($db->dbms_type == "oracle"){
					$where .= " and  to_char(ccd.regdate_ , 'YYYY-MM-DD') between  '".$sdate."' and '".$edate."' ";
				}else{
					$where .= " and date_format(ccd.regdate,'%Y-%m-%d') between  '".$sdate."' and '".$edate."' ";
				}
			}
		}
	}
/*
http://daisodev.forbiz.co.kr/admin/basic/seller.list.php?mc_ix=&cid2=C0001&depth=&cid0_1=C0001&cid1_1=&cid2_1=&cid3_1=&com_group=1&department=4&position=1&duty=1&com_person=b923c15c8a42916988b3e2d3809cb1d6&seller_type%5B%5D=1&seller_type%5B%5D=2&seller_type%5B%5D=3&nationality=I&com_div=R&seller_level=1&regdate=1&sdate=2014-07-12&edate=2014-07-12&search_type=com_name&search_text=%EC%A3%BC%29%ED%95%9C%EC%9B%B0%EC%9D%B4%EC%87%BC%ED%95%91&x=66&y=22

*/
if($page_type != 'seller_report'){
	$limit = " LIMIT $start, $max";
}

	//전체 갯수 불러오는 부분
	$sql = "SELECT 
				count(ccd.company_id) as total 
			FROM 
				".TBL_COMMON_COMPANY_DETAIL." as ccd
				inner join ".TBL_COMMON_SELLER_DETAIL." as csd on (ccd.company_id = csd.company_id)
				left join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
				left join common_user as cu on (ccd.company_id = cu.company_id)
				left join common_member_detail as cmd on (cu.code = cmd.code)
			where
				ccd.com_type in ('G','S')
				$where";

	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];

	if($db->dbms_type == "oracle"){

		$sql = "SELECT 
				ccd.*,
				csd.*
			FROM 
				".TBL_COMMON_COMPANY_DETAIL." as ccd
				inner join ".TBL_COMMON_SELLER_DETAIL." as csd on (ccd.company_id = csd.company_id)
				left join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
			where
				ccd.com_type in ('G','S')
				$where
				order by ccd.regdate DESC
				$limit
			";

	}else{
		$sql = "SELECT 
				ccd.*,
				csd.*
			FROM
				".TBL_COMMON_COMPANY_DETAIL." as ccd
				inner join ".TBL_COMMON_SELLER_DETAIL." as csd on (ccd.company_id = csd.company_id)
				left join common_company_relation as ccr on (ccd.company_id = ccr.company_id)
				left join common_user as cu on (ccd.company_id = cu.company_id)
				left join common_member_detail as cmd on (cu.code = cmd.code)
			where
				ccd.com_type in ('G','S')
				$where
				order by ccd.regdate DESC
				$limit";
	}

	$db->query($sql);
	$goods_infos = $db->fetchall();

	if(substr_count($_SERVER["PHP_SELF"],"/inventory/supply_vendor_list.php")>0 || substr_count($_SERVER["PHP_SELF"],"/inventory/sales_vendor_list.php")>0) {//수정 kbk 13/08/09
		if(is_array($seller_type)) {
			for($i=0;$i<count($seller_type);$i++) {
				$add_page_query="&seller_type[]=".$seller_type[$i];
			}
		}
		$str_page_bar = page_bar($total, $page,$max, "&mode=$mode&max=$max&search_type=$search_type&search_text=$search_text&cid0_1=$cid0_1&cid1_1=$cid1_1&cid2_1=$cid2_1&cid3_1=$cid3_1&com_group=$com_group&department=$department&position=$position&duty=$duty&com_person=$com_person&nationality=$nationality&com_div=$com_div&seller_level=$seller_level&regdate=$regdate&sdate=$sdate&edate=$edate".$add_page_query,"view");
	} else {
		$str_page_bar = page_bar($total, $page,$max, "&max=$max&update_kind=$update_kind&search_type=$search_type&search_text=$search_text&region=$region&gp_ix=$gp_ix&age=$age&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&mem_type=$mem_type&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");//gp_level 을 사용하지 않아서 gp_ix 로 바꿈 kbk 13/02/19
	}

if($mode == "excel"){
	include("excel_out_columsinfo.php");

	if($info_type == "seller_list"){
		$conf_name = "seller_list_".$info_type;
		$conf_name_check = "check_seller_list_".$info_type;

	}

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='".$conf_name."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='".$conf_name_check."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel_checked = $db->dt[conf_val];

	$check_colums = unserialize(stripslashes($stock_report_excel_checked));

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
	if(is_array($check_colums) && count($check_colums) > 0 ) {
        foreach ($check_colums as $key => $value) {
            $inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
            $col++;
        }

        $before_pid = "";

        if ($info_type == "warehouse" || $info_type == "category") {
            for ($i = 0; $i < count($goods_infos); $i++) {
                $stock_assets_sum += $goods_infos[$i][stock_assets];
                $stock_sum += $goods_infos[$i][stock];
                $stock_assets_total += $goods_infos[$i][stock_assets];
                $order_cnt_sum += $goods_infos[$i][order_cnt];
            }
        }

        for ($i = 0; $i < count($goods_infos); $i++) {
            $j = "A";
            foreach ($check_colums as $key => $value) {
                if ($key == "seller_date") {
                    if ($goods_infos[$i][seller_date]) {
                        $value_array = explode(" ", $goods_infos[$i][seller_date]);
                        $value_str = $value_array[0];
                    }
                } else if ($key == "seller_type") {

                    $seller_array = unserialize($goods_infos[$i][seller_type]);

                    switch ($seller_array[sales_vendor]) {
                        case "1":
                            $value_str = "매출";
                            break;
                    }
                    switch ($seller_array[supply_vendor]) {
                        case "2":
                            $value_str .= " / 매입";
                            break;
                    }

                } else if ($key == "com_div") {
                    switch ($goods_infos[$i][com_div]) {
                        case "P":
                            $value_str = "개인(일반사업자)";
                            break;
                        case "R":
                            $value_str = "법인";
                            break;
                        case "S":
                            $value_str = "간이과세자";
                            break;
                        case "E":
                            $value_str = "면세과세자";
                            break;
                        case "I":
                            $value_str = "수출입업자";
                            break;
                    }
                } else if ($key == "seller_level") {
                    switch ($goods_infos[$i][seller_level]) {
                        case "1":
                            $value_str = "우호";
                            break;
                        case "2":
                            $value_str = "양호";
                            break;
                        case "3":
                            $value_str = "보통";
                            break;
                        case "4":
                            $value_str = "위험";
                            break;
                        case "5":
                            $value_str = "블랙리스트";
                            break;
                    }
                } else if ($key == "loan_price") {
                    $value_str = number_format($goods_infos[$i][loan_price]) . " 원";
                } else if ($key == "deposit_price") {
                    $value_str = number_format($goods_infos[$i][deposit_price]) . " 원";
                } else {
                    $value_str = $goods_infos[$i][$value];//$db1->dt[$value];
                    //echo "$key"." ::: "."$value_str";
                }

                $inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
                $j++;
            }
            $z++;

        }

        // 첫번째 시트 선택
        $inventory_excel->setActiveSheetIndex(0);

        // 너비조정
        $col = 'A';
        foreach ($check_colums as $key => $value) {
            $inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
    }else{
        $inventory_excel->getActiveSheet(0)->setCellValue($col . "1", "엑셀 다운로드 형식 설정이 안되어 있습니다. 엑셀 설정하기를 클릭하여 저장 후 다시 엑셀 저장 시도해주세요.");
        $inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="basic_excel_'.$info_type.'.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter->save('php://output');

	exit;
}


?>
