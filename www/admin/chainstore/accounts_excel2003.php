<?php
	include("../class/layout.class");
	include '../include/phpexcel/Classes/PHPExcel.php';

//	error_reporting(E_ALL);
	
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
	date_default_timezone_set('Asia/Seoul');
	
	$db1 = new Database;
	$odb = new Database;
	
	
	if ($vToYY == ""){
		$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
		$vdate = date("Ymd", time());
		$today = date("Ymd", time());
	
		$sDate = date("Y/m/d", time()-84600*(date("d")-1));
		$eDate = date("Y/m/d", time()+84600*(31-date("d")));
	
		$startDate = date("Ymd", time()-84600*(date("d")-1));
		$endDate = date("Ymd", time()+84600*(31-date("d")));
	}else{
		$vdate = date("Ymd", time());
		$today = date("Ymd", time());
	
		$sDate = $vFromYY."/".$vFromMM."/".$vFromDD;
		$eDate = $vToYY."/".$vToMM."/".$vToDD;
		$startDate = $vFromYY.$vFromMM.$vFromDD;
		$endDate = $vToYY.$vToMM.$vToDD;
	}
	
	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}


	if ($vFromYY != "")	$where .= "and a.ac_date between $startDate and $endDate ";

	if($status){
		$where .= " and a.status = '".$status."'";
	}

	if($mode != "excel"){
		$limit_str = "  LIMIT $start, $max ";
	}

	if($admininfo[admin_level] == 9){
			if($company_id != ""){
				$company_id_str = " and c.company_id = '$company_id'";
			}

			if($acc_view_type == "detail" || $acc_view_type == "report"){
				$sql = "SELECT a.company_id FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_SHOP_ORDER_DETAIL." od
								where a.company_id = c.company_id and a.ac_ix = od.ac_ix
								$where $company_id_str ";
				$db1->query($sql);
				$total = $db1->total;

				$sql = "SELECT a.*, c.com_name, csd.bank_name, csd.bank_number, csd.bank_owner , od.oid, od.pid, od.pname, od.option1, od.option_text, od.option_etc,
								od.pcnt, od.coprice, od.psprice, od.ptprice, od.ptprice*(100-od.commission)/100 as account_price, od.delivery_price,
								od.commission, od.surtax_yorn, o.bname, o.method, o.receipt_y, o.mem_group, o.use_reserve_price ,  o.use_cupon_price
								FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_COMMON_SELLER_DETAIL." csd , ".TBL_SHOP_ORDER_DETAIL." od,  ".TBL_SHOP_ORDER." o
								where a.company_id = c.company_id and c.company_id = csd.company_id and a.ac_ix = od.ac_ix and o.oid = od.oid $company_id_str $where  order by o.oid asc $limit_str ";
				$db1->query($sql);
			}else{
				$sql = "SELECT a.company_id FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c where a.company_id = c.company_id $where $company_id_str ";
				$db1->query($sql);
				$total = $db1->total;

				$sql = "SELECT a.*, c.com_name, csd.bank_name, csd.bank_number, csd.bank_owner
								FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_COMMON_SELLER_DETAIL." csd
								where a.company_id = c.company_id and c.company_id = csd.company_id $company_id_str $where $limit_str ";
//								echo $sql;
				$db1->query($sql);
			}


	}else if($admininfo[admin_level] == 8){
		if($acc_view_type == "detail"  || $acc_view_type == "report"){
				$sql = "SELECT a.company_id FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_SHOP_ORDER_DETAIL." od
								where a.company_id = c.company_id and a.ac_ix = od.ac_ix
								$where $company_id_str ";
				$db1->query($sql);
				$total = $db1->total;

				$sql = "SELECT a.*, c.com_name, csd.bank_name, csd.bank_number, csd.bank_owner , od.oid,  od.pid,  od.pname, od.option1, od.option_text, od.option_etc,
								od.pcnt, od.coprice, od.psprice, od.ptprice, od.ptprice*(100-od.commission)/100 as account_price, od.delivery_price, od.commission, od.surtax_yorn,
								o.bname, o.method, o.receipt_y, o.mem_group, o.use_reserve_price ,  o.use_cupon_price
								FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_COMMON_SELLER_DETAIL." csd  , ".TBL_SHOP_ORDER_DETAIL." od,  ".TBL_SHOP_ORDER." o
								where a.company_id = c.company_id and c.company_id = csd.company_id and a.ac_ix = od.ac_ix and c.company_id = '".$admininfo[company_id]."' and o.oid = od.oid $where
								order by o.oid asc $limit_str ";
				$db1->query($sql);
		}else{
			$sql = "SELECT a.company_id FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c where  a.company_id = c.company_id and c.company_id = '".$admininfo[company_id]."' $where ";
			$db1->query($sql);
			$total = $db1->total;

			$sql = "SELECT a.*, c.com_name, csd.bank_name, csd.bank_number, csd.bank_owner
					FROM ".TBL_SHOP_ACCOUNTS." a, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_COMMON_SELLER_DETAIL." csd
					where a.company_id = c.company_id and c.company_id = csd.company_id and c.company_id = '".$admininfo[company_id]."' $where $limit_str ";
			$db1->query($sql);
		}
	}
	
	if($mode == "excel"){
	
		$accounts_XL = new PHPExcel();
		
		// 속성 정의
		$accounts_XL->getProperties()->setCreator("포비즈 코리아")
									 ->setLastModifiedBy("Mallstory.com")
									 ->setTitle("accounts")
									 ->setSubject("accounts")
									 ->setDescription("generated by forbiz korea")
									 ->setKeywords("mallstory")
									 ->setCategory("accounts");
		
		$HeaderRow = 1;
		
		if($db1->total){
			//echo "NO\t업체명\t은행명\t계좌번호\t입금자명\t정산일자\t판매건수\t배송비\t판매총액(할인가기준)\t수수료\t정산금액\t정산상태\n";
			
			//// 타이틀을 정한다.
			if($acc_view_type == "detail"){
				$accounts_XL->getActiveSheet(0)->setCellValue('A' . 1, "번호");
				$accounts_XL->getActiveSheet(0)->setCellValue('B' . 1, "업체");
				$accounts_XL->getActiveSheet(0)->setCellValue('C' . 1, "정산일");
				$accounts_XL->getActiveSheet(0)->setCellValue('D' . 1, "주문번호");
				$accounts_XL->getActiveSheet(0)->setCellValue('E' . 1, "제품명");
				$accounts_XL->getActiveSheet(0)->setCellValue('F' . 1, "옵션");
				$accounts_XL->getActiveSheet(0)->setCellValue('G' . 1, "비고");
				$accounts_XL->getActiveSheet(0)->setCellValue('H' . 1, "수량");
				$accounts_XL->getActiveSheet(0)->setCellValue('I' . 1, "단가");
				$accounts_XL->getActiveSheet(0)->setCellValue('J' . 1, "판매액");
				$accounts_XL->getActiveSheet(0)->setCellValue('K' . 1, "수수료율(%)");
				$accounts_XL->getActiveSheet(0)->setCellValue('L' . 1, "면세여부");
				$accounts_XL->getActiveSheet(0)->setCellValue('M' . 1, "정산금액");
				$accounts_XL->getActiveSheet(0)->setCellValue('N' . 1, "정산금액(공급가)");
				$accounts_XL->getActiveSheet(0)->setCellValue('O' . 1, "정산금액(부가세)");
				$accounts_XL->getActiveSheet(0)->setCellValue('P' . 1, "배송비정산금액");
				$accounts_XL->getActiveSheet(0)->setCellValue('Q' . 1, "정산상태");
				
			}else if($acc_view_type == "report"){
				$accounts_XL->getActiveSheet()->mergeCells('A1:A2');
				$accounts_XL->getActiveSheet()->mergeCells('B1:B2');
				$accounts_XL->getActiveSheet()->mergeCells('C1:C2');
				$accounts_XL->getActiveSheet()->mergeCells('D1:D2');
				$accounts_XL->getActiveSheet()->mergeCells('E1:E2');
				$accounts_XL->getActiveSheet()->mergeCells('F1:F2');
				$accounts_XL->getActiveSheet()->mergeCells('G1:G2');
				$accounts_XL->getActiveSheet()->mergeCells('H1:H2');
				$accounts_XL->getActiveSheet()->mergeCells('I1:L1');
				$accounts_XL->getActiveSheet()->mergeCells('M1:O1');
				$accounts_XL->getActiveSheet()->mergeCells('P1:R1');
				$accounts_XL->getActiveSheet()->mergeCells('S1:U1');
	
				$accounts_XL->getActiveSheet()->mergeCells('V1:V2');
				$accounts_XL->getActiveSheet()->mergeCells('W1:W2');
				$accounts_XL->getActiveSheet()->mergeCells('X1:X2');
				$accounts_XL->getActiveSheet()->mergeCells('Y1:Y2');
				$accounts_XL->getActiveSheet()->mergeCells('Z1:Z2');
				$accounts_XL->getActiveSheet()->mergeCells('AA1:AA2');
				$accounts_XL->getActiveSheet()->mergeCells('AB1:AB2');
				$accounts_XL->getActiveSheet()->mergeCells('AC1:AC2');
				$accounts_XL->getActiveSheet()->mergeCells('AD1:AD2');
				$accounts_XL->getActiveSheet()->mergeCells('AE1:AE2');
				
				//NO	업체명	정산일	주문번호	제품명	옵션	비고	수량
				$accounts_XL->getActiveSheet(0)->setCellValue('A' . 1, "번호");
				$accounts_XL->getActiveSheet(0)->setCellValue('B' . 1, "업체명");
				$accounts_XL->getActiveSheet(0)->setCellValue('C' . 1, "정산일");
				$accounts_XL->getActiveSheet(0)->setCellValue('D' . 1, "주문번호");
				$accounts_XL->getActiveSheet(0)->setCellValue('E' . 1, "제품명");
				$accounts_XL->getActiveSheet(0)->setCellValue('F' . 1, "옵션");
				$accounts_XL->getActiveSheet(0)->setCellValue('G' . 1, "비고");
				$accounts_XL->getActiveSheet(0)->setCellValue('H' . 1, "수량");
	
				//상품매출		단가	공급가	부가세	합계
				$accounts_XL->getActiveSheet(0)->setCellValue('I' . 1, "상품매출");
				$accounts_XL->getActiveSheet(0)->setCellValue('I' . 2, "단가");
				$accounts_XL->getActiveSheet(0)->setCellValue('J' . 2, "공급가");
				$accounts_XL->getActiveSheet(0)->setCellValue('K' . 2, "부가세");
				$accounts_XL->getActiveSheet(0)->setCellValue('L' . 2, "합계");
	
				//배송비정산금액	공급가	부가세	합계
				$accounts_XL->getActiveSheet(0)->setCellValue('M' . 1, "배송비정산금액");
				$accounts_XL->getActiveSheet(0)->setCellValue('M' . 2, "공급가");
				$accounts_XL->getActiveSheet(0)->setCellValue('N' . 2, "공급가");
				$accounts_XL->getActiveSheet(0)->setCellValue('O' . 2, "합계");
				
				//합계			공급가	부가세	합계
				$accounts_XL->getActiveSheet(0)->setCellValue('P' . 1, "합계");
				$accounts_XL->getActiveSheet(0)->setCellValue('P' . 2, "공급가");
				$accounts_XL->getActiveSheet(0)->setCellValue('Q' . 2, "부가세");
				$accounts_XL->getActiveSheet(0)->setCellValue('R' . 2, "합계");
				
				//매출원가		공급가	부가세	합계
				$accounts_XL->getActiveSheet(0)->setCellValue('S' . 1, "매출원가");
				$accounts_XL->getActiveSheet(0)->setCellValue('S' . 2, "공급가");
				$accounts_XL->getActiveSheet(0)->setCellValue('T' . 2, "부가세");
				$accounts_XL->getActiveSheet(0)->setCellValue('U' . 2, "합계");
				
				//수수료율(%)	수수료	쿠폰용금액	적립금사용금액	면세여부	정산상태	증빙	결제방법	회원명	회원등급
				$accounts_XL->getActiveSheet(0)->setCellValue('V' . 1, "수수료율(%)");
				$accounts_XL->getActiveSheet(0)->setCellValue('W' . 1, "수수료");
				$accounts_XL->getActiveSheet(0)->setCellValue('X' . 1, "쿠폰용금액");
				$accounts_XL->getActiveSheet(0)->setCellValue('Y' . 1, "적립금사용금액");
				$accounts_XL->getActiveSheet(0)->setCellValue('Z' . 1, "면세여부");
				$accounts_XL->getActiveSheet(0)->setCellValue('AA' . 1, "정산상태");
				$accounts_XL->getActiveSheet(0)->setCellValue('AB' . 1, "증빙");
				$accounts_XL->getActiveSheet(0)->setCellValue('AC' . 1, "결제방법");
				$accounts_XL->getActiveSheet(0)->setCellValue('AD' . 1, "회원명");
				$accounts_XL->getActiveSheet(0)->setCellValue('AE' . 1, "회원등급");
				
				$HeaderRow = 2;
				
			}else{
				$accounts_XL->getActiveSheet(0)->setCellValue('A' . 1, "번호");
				$accounts_XL->getActiveSheet(0)->setCellValue('B' . 1, "업체명");
				$accounts_XL->getActiveSheet(0)->setCellValue('C' . 1, "정산일");
				$accounts_XL->getActiveSheet(0)->setCellValue('D' . 1, "은행명");
				$accounts_XL->getActiveSheet(0)->setCellValue('E' . 1, "계좌번호");
				$accounts_XL->getActiveSheet(0)->setCellValue('F' . 1, "입금자명");
				$accounts_XL->getActiveSheet(0)->setCellValue('G' . 1, "정산일자");
				$accounts_XL->getActiveSheet(0)->setCellValue('H' . 1, "판매건수");
				$accounts_XL->getActiveSheet(0)->setCellValue('I' . 1, "판매총액(할인가기준)");
				$accounts_XL->getActiveSheet(0)->setCellValue('J' . 1, "수수료");
				$accounts_XL->getActiveSheet(0)->setCellValue('K' . 1, "정산금액");
				$accounts_XL->getActiveSheet(0)->setCellValue('L' . 1, "배송비정산금액");
				$accounts_XL->getActiveSheet(0)->setCellValue('M' . 1, "정산상태");
			}
			
			for ($i = 0; $i < $db1->total; $i++){
				$db1->fetch($i);
	
				if ($db1->dt[method] == ORDER_METHOD_CARD)
				{
					if($db1->dt[bank] == ""){
						$method = "카드결제";
					}else{
						$method = $db1->dt[bank];
					}
				}elseif($db1->dt[method] == ORDER_METHOD_BANK){
					$method = "계좌입금";
				}elseif($db1->dt[method] == ORDER_METHOD_PHONE){
					$method = "전화결제";
				}elseif($db1->dt[method] == ORDER_METHOD_AFTER){
					$method = "후불결제";
				}elseif($db1->dt[method] == ORDER_METHOD_VBANK){
					$method = "가상계좌";
				}elseif($db1->dt[method] == ORDER_METHOD_ICHE){
					$method = "실시간계좌이체";
				}elseif($db1->dt[method] == ORDER_METHOD_ASCROW){
					$method = "가상계좌[에스크로]";
				}
	
				if($db1->dt[receipt_y] == "Y"){
					$receipt_str = "발행";
				}else{
					$receipt_str = "미발행";
				}
				
				if($acc_view_type == "detail"){
					$accounts_XL->getActiveSheet(0)->setCellValue('A' . ($i+$HeaderRow), $i+1);
					$accounts_XL->getActiveSheet(0)->setCellValue('B' . ($i+$HeaderRow), $db1->dt[com_name]);
					$accounts_XL->getActiveSheet(0)->setCellValue('C' . ($i+$HeaderRow), $db1->dt[ac_date]);
					$accounts_XL->getActiveSheet(0)->setCellValue('D' . ($i+$HeaderRow), $db1->dt[oid]);
					$accounts_XL->getActiveSheet(0)->setCellValue('E' . ($i+$HeaderRow), strip_tags($db1->dt[pname]));
					$accounts_XL->getActiveSheet(0)->setCellValue('F' . ($i+$HeaderRow), strip_tags($db1->dt[option_text]));
					$accounts_XL->getActiveSheet(0)->setCellValue('G' . ($i+$HeaderRow), $db1->dt[option_etc1]);
					$accounts_XL->getActiveSheet(0)->setCellValue('H' . ($i+$HeaderRow), $db1->dt[pcnt]);
					$accounts_XL->getActiveSheet(0)->setCellValue('I' . ($i+$HeaderRow), $db1->dt[psprice]);
					$accounts_XL->getActiveSheet(0)->setCellValue('J' . ($i+$HeaderRow), $db1->dt[ptprice]);
					$accounts_XL->getActiveSheet(0)->setCellValue('K' . ($i+$HeaderRow), $db1->dt[commission]);
					$accounts_XL->getActiveSheet(0)->setCellValue('L' . ($i+$HeaderRow), $db1->dt[ptprice]-$db1->dt[account_price]);
					$accounts_XL->getActiveSheet(0)->setCellValue('M' . ($i+$HeaderRow), $db1->dt[surtax_yorn] == "Y" ? "면세":"과세");
					$accounts_XL->getActiveSheet(0)->setCellValue('N' . ($i+$HeaderRow), $db1->dt[account_price]);
					$accounts_XL->getActiveSheet(0)->setCellValue('O' . ($i+$HeaderRow), $db1->dt[surtax_yorn] == "Y" ? $db1->dt[account_price]:round($db1->dt[account_price]/1.1));
					$accounts_XL->getActiveSheet(0)->setCellValue('P' . ($i+$HeaderRow), $db1->dt[delivery_price]);
					$accounts_XL->getActiveSheet(0)->setCellValue('Q' . ($i+$HeaderRow), $db1->dt[account_price]);
					$accounts_XL->getActiveSheet(0)->setCellValue('R' . ($i+$HeaderRow), $db1->dt[status] == "AR" ? "정산대기":"정산완료");
	
				}else if($acc_view_type == "report"){
					//NO	업체명	정산일	주문번호	제품명	옵션	비고	수량
					$accounts_XL->getActiveSheet(0)->setCellValue('A' . ($i+$HeaderRow), $i+1);
					$accounts_XL->getActiveSheet(0)->setCellValue('B' . ($i+$HeaderRow), $db1->dt[com_name]);
					$accounts_XL->getActiveSheet(0)->setCellValue('C' . ($i+$HeaderRow), $db1->dt[ac_date]);
					$accounts_XL->getActiveSheet(0)->setCellValue('D' . ($i+$HeaderRow), $db1->dt[oid]);
					$accounts_XL->getActiveSheet(0)->setCellValue('E' . ($i+$HeaderRow), strip_tags($db1->dt[pname]));
					$accounts_XL->getActiveSheet(0)->setCellValue('F' . ($i+$HeaderRow), strip_tags($db1->dt[option_text]));
					$accounts_XL->getActiveSheet(0)->setCellValue('G' . ($i+$HeaderRow), $db1->dt[option_etc1]);
					$accounts_XL->getActiveSheet(0)->setCellValue('H' . ($i+$HeaderRow), $db1->dt[pcnt]);
	
					//상품매출		단가	공급가	부가세	합계
					$accounts_XL->getActiveSheet(0)->setCellValue('I' . ($i+$HeaderRow), $db1->dt[psprice]);
					$accounts_XL->getActiveSheet(0)->setCellValue('J' . ($i+$HeaderRow), ($db1->dt[surtax_yorn] == "Y" ? $db1->dt[psprice]:round($db1->dt[psprice]/1.1)));
					$accounts_XL->getActiveSheet(0)->setCellValue('K' . ($i+$HeaderRow), ($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[psprice]-round($db1->dt[psprice]/1.1)));
					$accounts_XL->getActiveSheet(0)->setCellValue('L' . ($i+$HeaderRow), $db1->dt[ptprice]);
	
					//배송비정산금액	공급가	부가세	합계
					$accounts_XL->getActiveSheet(0)->setCellValue('M' . ($i+$HeaderRow), (round($db1->dt[delivery_price]/1.1)));
					$accounts_XL->getActiveSheet(0)->setCellValue('N' . ($i+$HeaderRow), ($db1->dt[delivery_price]-round($db1->dt[delivery_price]/1.1)));
					$accounts_XL->getActiveSheet(0)->setCellValue('O' . ($i+$HeaderRow), $db1->dt[delivery_price]);
					
					//합계			공급가	부가세	합계
					$accounts_XL->getActiveSheet(0)->setCellValue('P' . ($i+$HeaderRow), (($db1->dt[surtax_yorn] == "Y" ? $db1->dt[ptprice]:round($db1->dt[ptprice]/1.1))+(round($db1->dt[delivery_price]/1.1))));
					$accounts_XL->getActiveSheet(0)->setCellValue('Q' . ($i+$HeaderRow), (($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[ptprice]-round($db1->dt[ptprice]/1.1))+($db1->dt[delivery_price]-round($db1->dt[delivery_price]/1.1))));
					$accounts_XL->getActiveSheet(0)->setCellValue('R' . ($i+$HeaderRow), intval($db1->dt[ptprice]+$db1->dt[delivery_price]));
					
					//매출원가		공급가	부가세	합계
					$accounts_XL->getActiveSheet(0)->setCellValue('S' . ($i+$HeaderRow), (($db1->dt[surtax_yorn] == "Y" ? $db1->dt[account_price]:round($db1->dt[account_price]/1.1))+(round($db1->dt[delivery_price]/1.1))));
					$accounts_XL->getActiveSheet(0)->setCellValue('T' . ($i+$HeaderRow), (($db1->dt[surtax_yorn] == "Y" ? "0":$db1->dt[account_price]-round($db1->dt[account_price]/1.1))+($db1->dt[delivery_price]-round($db1->dt[delivery_price]/1.1))));
					$accounts_XL->getActiveSheet(0)->setCellValue('U' . ($i+$HeaderRow), intval($db1->dt[account_price]+$db1->dt[delivery_price]));
					
					//수수료율(%)	수수료	쿠폰용금액	적립금사용금액	면세여부	정산상태	증빙	결제방법	회원명	회원등급
					$accounts_XL->getActiveSheet(0)->setCellValue('V' . ($i+$HeaderRow), ($db1->dt[commission]));
					$accounts_XL->getActiveSheet(0)->setCellValue('W' . ($i+$HeaderRow), ($db1->dt[ptprice]-$db1->dt[account_price]));
					$accounts_XL->getActiveSheet(0)->setCellValue('X' . ($i+$HeaderRow), ($boid != $db1->dt[oid] ? $db1->dt[use_cupon_price]:"0"));
					$accounts_XL->getActiveSheet(0)->setCellValue('Y' . ($i+$HeaderRow), ($boid != $db1->dt[oid] ? $db1->dt[use_reserve_price]:"0"));
					$accounts_XL->getActiveSheet(0)->setCellValue('Z' . ($i+$HeaderRow), ($db1->dt[surtax_yorn] == "Y" ? "면세":"과세"));
					$accounts_XL->getActiveSheet(0)->setCellValue('AA' . ($i+$HeaderRow), ($db1->dt[status] == "AR" ? "정산대기":"정산완료"));
					$accounts_XL->getActiveSheet(0)->setCellValue('AB' . ($i+$HeaderRow), $receipt_str);
					$accounts_XL->getActiveSheet(0)->setCellValue('AC' . ($i+$HeaderRow), $method);
					$accounts_XL->getActiveSheet(0)->setCellValue('AD' . ($i+$HeaderRow), $db1->dt[bname]);
					$accounts_XL->getActiveSheet(0)->setCellValue('AE' . ($i+$HeaderRow), $db1->dt[mem_group]);
	
				}else{
					$accounts_XL->getActiveSheet(0)->setCellValue('A' . ($i+$HeaderRow), ($i+1));
					$accounts_XL->getActiveSheet(0)->setCellValue('B' . ($i+$HeaderRow), $db1->dt[com_name]);
					$accounts_XL->getActiveSheet(0)->setCellValue('C' . ($i+$HeaderRow), $db1->dt[ac_date]);
					$accounts_XL->getActiveSheet(0)->setCellValue('D' . ($i+$HeaderRow), $db1->dt[bank_name]);
					$accounts_XL->getActiveSheet(0)->setCellValue('E' . ($i+$HeaderRow), $db1->dt[bank_number]);
					$accounts_XL->getActiveSheet(0)->setCellValue('F' . ($i+$HeaderRow), $db1->dt[bank_owner]);
					$accounts_XL->getActiveSheet(0)->setCellValue('G' . ($i+$HeaderRow), $db1->dt[ac_date]);
					$accounts_XL->getActiveSheet(0)->setCellValue('H' . ($i+$HeaderRow), $db1->dt[ac_cnt]);
					$accounts_XL->getActiveSheet(0)->setCellValue('I' . ($i+$HeaderRow), $db1->dt[sell_total_price]);
					$accounts_XL->getActiveSheet(0)->setCellValue('J' . ($i+$HeaderRow), ($db1->dt[sell_total_price]-$db1->dt[ac_price]));
					$accounts_XL->getActiveSheet(0)->setCellValue('K' . ($i+$HeaderRow), ($db1->dt[ac_price]));
					$accounts_XL->getActiveSheet(0)->setCellValue('L' . ($i+$HeaderRow), $db1->dt[shipping_fee]);
					$accounts_XL->getActiveSheet(0)->setCellValue('M' . ($i+$HeaderRow), ($db1->dt[taxbill_yn] != "Y" ? "정산완료":"정산승인대기"));
					
					// 시트이름등록
					$accounts_XL->getActiveSheet()->setTitle('업체별 정산 요약');
					

					//$memberXL->getActiveSheet()->getColumnDimension('B')->setWidth(15);									
				}
	
				$boid = $db1->dt[oid];
			}
		}
	}

	// 첫번째 시트 선택
	$accounts_XL->setActiveSheetIndex(0);
	
	//모두 AUTOSIZE
	$accounts_XL->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('AC')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('AD')->setAutoSize(true);
	$accounts_XL->getActiveSheet()->getColumnDimension('AE')->setAutoSize(true);
	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="accounts.xls"');
	header('Cache-Control: max-age=0');


	$objWriter = PHPExcel_IOFactory::createWriter($accounts_XL, 'Excel5');
	$objWriter->save('php://output');

	exit;
	
		/*
		if($acc_view_type == "report"){
			$mstring .= "</table>";
		}
	
		echo iconv("utf-8","CP949",$mstring);
		*/
//		exit;
//	}
	
	