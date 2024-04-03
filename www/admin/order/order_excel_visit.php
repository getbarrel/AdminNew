<?php
	include("../class/layout.class");
	include("../order/excel_out_columsinfo.php");
	include("../include/phpexcel/Classes/PHPExcel.php");
	
	//error_reporting(E_ALL);
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
	
	date_default_timezone_set('Asia/Seoul');
	
	$db1 = new MySQL;
	$odb = new MySQL;
	if($excel_type == "delivery"){
		$sql = "select delivery_excel_info1 as order_excel_info1, delivery_excel_info2 as order_excel_info2, delivery_excel_checked as order_excel_checked 
				from ".TBL_COMMON_SELLER_DETAIL."	
				where company_id = '".$admininfo[company_id]."'";
	}else{
		$sql = "select order_excel_info1, order_excel_info2, order_excel_checked 
				from ".TBL_COMMON_SELLER_DETAIL."	
				where company_id = '".$admininfo[company_id]."'";
	}
	
	$db1->query($sql);
	$db1->fetch();
	
	$check_colums = unserialize(stripslashes($db1->dt[order_excel_checked]));
	$columsinfo = $colums;
	
	
	$str_colums = implode(",", $check_colums);

	

	$where = "WHERE od.status <> '' and od.status !='SR' AND od.product_type NOT IN (".implode(',',$sns_product_type).") ";
	

	$cb_pop_view=true;
	$bm_pop_view=true;
	if($admininfo[mem_type]=='BM' && $admininfo[mem_level]=='11'){//지사장
		$company_id=$admininfo[company_id];
		$branch_name=$admininfo[company_name];
		$cb_ix=GetCB_IX($company_id);
		$cb_pop_view=false;
	}elseif(($admininfo[mem_type]=='BM' && $admininfo[mem_level]=='14') || ($admininfo[mem_type]=='BM' && $admininfo[mem_level]=='13') ){//BM,팀장
		$company_id=$admininfo[company_id];
		$branch_name=$admininfo[company_name];
		$bm_code=$admininfo[charger_ix];
		$bm_name=$admininfo[charger];
		$cb_pop_view=false;
		$bm_pop_view=false;
	}

	if($admininfo[mem_type]=='BM' && $admininfo[mem_level]=='11'){//지사장

		$where .= " and o.cb_ix = '".$cb_ix."' ";

		if($bm_code!=""){
			$where .= " and o.bm_code = '".$bm_code."' ";
		}
	}elseif(($admininfo[mem_type]=='BM' && $admininfo[mem_level]=='14') || ($admininfo[mem_type]=='BM' && $admininfo[mem_level]=='13')){//BM
		$where .= " and o.bm_code = '".$bm_code."' ";
	}else{
		if($bm_code!=""){
			$where .= " and o.bm_code = '".$bm_code."' ";
		}elseif($cb_ix!=""){
			$where .= " and o.cb_ix = '".$cb_ix."' ";
		}
	}

	if($search_type != "" && $search_text != ""){
		if($search_type == "combi_name"){
			$where .= "and (bname LIKE '%".trim($search_text)."%'  or rname LIKE '%".trim($search_text)."%' or bank_input_name LIKE '%".trim($search_text)."%') ";
		}else{
			$where .= "and $search_type like '%$search_text%'";
		}
	}

	if ($vFromYY != "")	{
		$startDate = $vFromYY.$vFromMM.$vFromDD;
		$endDate = $vToYY.$vToMM.$vToDD;

		$where .= "and date_format(date,'%Y%m%d') between $startDate and $endDate ";
	}

	if(is_array($type)){ //

		for($i=0;$i < count($type);$i++){

			if($type_str == ""){
				$type_str .= "'".$type[$i]."'";
			}else{
				$type_str .= ",'".$type[$i]."'";
			}

		}

		if($type_str){
			$where .= "and od.status in ($type_str) ";
		}
	}else{

		if($type){
			$where .= "and od.status = '$type'";
		}

	}

	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$where .= " and o.oid = od.oid and o.oid = odv.oid and od.company_id = odv.company_id and od.company_id = '".$company_id."'";//od.pid = p.id and
		}else{
			$where .= " and o.oid = od.oid and o.oid = odv.oid and od.company_id = odv.company_id  ";
		}
	}else if($admininfo[admin_level] == 8){
		$where .= " and o.oid = od.oid and o.oid = odv.oid and od.company_id = odv.company_id and od.company_id = '".$admininfo[company_id]."'"; // od.pid = p.id and
	}

	if($excel_type == "delivery"){
		$sql = "select oid from (SELECT od.oid, od.pid, od.regdate 
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_delivery odv $where ) ood 
				left join ".TBL_SHOP_PRODUCT." p on ood.pid = p.id 
				WHERE p.product_type NOT IN (".implode(',',$sns_product_type).") group by oid ";
	}else{
		$sql = "select oid from (SELECT od.oid, od.pid, od.regdate 
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_delivery odv $where ) ood 
				left join ".TBL_SHOP_PRODUCT." p on ood.pid = p.id 
				WHERE p.product_type NOT IN (".implode(',',$sns_product_type).") ";

	}
	$db1->query($sql);		//,
	//echo $sql;

	$total = $db1->total;
	
	if($excel_type == "delivery"){
		/*
		$sql = "select  p.pcode, ood.*  from
				(SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, o.rname, tid,  method,rmail,bmail, total_price, payment_price, date, od.pname, addr, zip, msg, rtel, rmobile,
						od.pid, od.company_name, od.company_id, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,  od.quick, od.invoice_no as invoiceno , od.dc_date,
						(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as deliveryprice,
						(select delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id) as deliverypaytype
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_delivery odv $where ) ood 
				left join ".TBL_SHOP_PRODUCT." p on ood.pid = p.id AND p.product_type NOT IN (".implode(',',$sns_product_type).") 
				group by oid , company_id ORDER BY date DESC "; //
		*/
			/*$sql = "select  p.pcode, ood.*  from
				(SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, o.rname, tid,  method,rmail,bmail, total_price, payment_price, date, od.pname, addr, zip, msg, rtel, rmobile,
						od.od_ix,od.pid, od.company_name, od.company_id, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,  od.quick, od.invoice_no as invoiceno , od.dc_date,
						odv.delivery_price, odv.delivery_pay_type
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_delivery odv $where ) ood 
				left join ".TBL_SHOP_PRODUCT." p on ood.pid = p.id AND p.product_type NOT IN (".implode(',',$sns_product_type).") 
				group by oid , company_id,ood.od_ix ORDER BY date DESC ";*/

				$sql = "SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, o.rname, tid,  method,rmail,bmail, total_price, payment_price, date, od.pname, addr, zip, msg, rtel, rmobile,
						od.od_ix,od.pid, od.company_name, od.company_id, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,  od.quick, od.invoice_no as invoiceno , od.dc_date,
						odv.delivery_price, odv.delivery_pay_type,
				(select  p.pcode FROM ".TBL_SHOP_PRODUCT." p WHERE p.id=od.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).")) AS pcode
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_delivery odv $where 
				group by oid , company_id,od.od_ix ORDER BY date DESC ";//쿼리 부하가 의심되어 수정함 kbk 12/05/22
	}else{
		/*
		$sql = "select  p.pcode, ood.*  from
				(SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, o.rname, tid,  method,rmail,bmail, total_price, payment_price, date, od.pname, addr, zip, msg, rtel, rmobile,
						od.pid, od.company_name, od.company_id, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,  od.quick, od.invoice_no as invoiceno , od.dc_date,
						(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as deliveryprice,
						(select delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id) as deliverypaytype
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od $where ) ood 
				left join ".TBL_SHOP_PRODUCT." p on ood.pid = p.id AND p.product_type NOT IN (".implode(',',$sns_product_type).") 
				ORDER BY date DESC "; //
		*/
		if($e_type!="") {//주문 이외 리스트용 kbk 12/02/28
			if($e_type==ORDER_STATUS_EXCHANGE_APPLY) {
				/*$sql = "select  p.pcode, ood.*  from
					(SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, odvd.rname, tid,  method,odvd.rmail,bmail, total_price, payment_price, o.date,odvd.date AS ex_date, od.pname, CONCAT(odvd.addr1,' ',odvd.addr2) AS addr, odvd.zip, o.msg, odvd.rtel, odvd.rmobile,
							od.od_ix,od.pid, od.company_name, od.company_id, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,  od.quick, od.invoice_no as invoiceno , od.dc_date,
							odv.delivery_price, odv.delivery_pay_type
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_delivery odv, shop_order_detail_deliveryinfo odvd $where and od.od_ix=odvd.od_ix ) ood 
					left join ".TBL_SHOP_PRODUCT." p on ood.pid = p.id AND p.product_type NOT IN (".implode(',',$sns_product_type).") 
					group by oid, ood.pid, ood.od_ix ORDER BY date DESC ";*/
				$sql = "SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, odvd.rname, tid, o.smart_yn, method,odvd.rmail,bmail, total_price, payment_price, o.date,odvd.date AS ex_date, od.pname, CONCAT(odvd.addr1,' ',odvd.addr2) AS addr, odvd.zip, o.msg, odvd.rtel, odvd.rmobile,
							od.od_ix,od.pid, od.company_name, od.company_id, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,  od.quick, od.invoice_no as invoiceno , od.dc_date,
							odv.delivery_price, odv.delivery_pay_type,
					(select  AES_DECRYPT(UNHEX(cmd2.name),'".$db->ase_encrypt_key."') as name from common_member_detail cmd2 where o.bm_code = cmd2.code) as bm_name,
					(select  branch_name from common_branch_detail cbd2 where o.cb_ix = cbd2.cb_ix) as branch_name,
					(select  p.pcode FROM ".TBL_SHOP_PRODUCT." p WHERE p.id=od.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).")) AS pcode
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_delivery odv, shop_order_detail_deliveryinfo odvd $where and od.od_ix=odvd.od_ix 
					group by oid, od.pid, od.od_ix ORDER BY date DESC ";//쿼리 부하가 의심되어 수정함 kbk 12/05/22
			} else {
				if($e_type==ORDER_STATUS_RETURN_APPLY) $add_select="od.ra_date AS rt_date,";
				else $add_select="";
				/*$sql = "select  p.pcode, ood.*  from
					(SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, o.rname, tid,  method,o.rmail,bmail, total_price, payment_price,o.date, ".$add_select." od.pname, o.addr, o.zip, o.msg, o.rtel, o.rmobile,
							od.od_ix,od.pid, od.company_name, od.company_id, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,  od.quick, od.invoice_no as invoiceno , od.dc_date,
							odv.delivery_price, odv.delivery_pay_type
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_delivery odv $where ) ood 
					left join ".TBL_SHOP_PRODUCT." p on ood.pid = p.id AND p.product_type NOT IN (".implode(',',$sns_product_type).") 
					group by oid, ood.pid, ood.od_ix ORDER BY date DESC ";*/
				$sql = "SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, o.rname, tid, o.smart_yn, method,o.rmail,bmail, total_price, payment_price,o.date, ".$add_select." od.pname, o.addr, o.zip, o.msg, o.rtel, o.rmobile,
							od.od_ix,od.pid, od.company_name, od.company_id, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,  od.quick, od.invoice_no as invoiceno , od.dc_date,
							odv.delivery_price, odv.delivery_pay_type,
					(select  AES_DECRYPT(UNHEX(cmd2.name),'".$db->ase_encrypt_key."') as name from common_member_detail cmd2 where o.bm_code = cmd2.code) as bm_name,
					(select  branch_name from common_branch_detail cbd2 where o.cb_ix = cbd2.cb_ix) as branch_name,
					(select  p.pcode FROM ".TBL_SHOP_PRODUCT." p WHERE p.id=od.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).")) AS pcode
					FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_delivery odv $where
					group by oid, od.pid, od.od_ix ORDER BY date DESC ";//쿼리 부하가 의심되어 수정함 kbk 12/05/22
			}
		} else {
			/*$sql = "select  p.pcode, ood.*  from
				(SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, o.rname, tid,  method,rmail,bmail, total_price, payment_price, date, od.pname, addr, zip, msg, rtel, rmobile,
						od.od_ix,od.pid, od.company_name, od.company_id, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,  od.quick, od.invoice_no as invoiceno , od.dc_date,
						odv.delivery_price, odv.delivery_pay_type
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_delivery odv $where ) ood 
				left join ".TBL_SHOP_PRODUCT." p on ood.pid = p.id AND p.product_type NOT IN (".implode(',',$sns_product_type).") 
				group by oid, ood.pid, ood.od_ix ORDER BY date DESC "; */
			$sql = "SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, o.rname, tid, o.smart_yn, method,rmail,bmail, total_price, payment_price, date, od.pname, addr, zip, msg, rtel, rmobile,
						od.od_ix,od.pid, od.company_name, od.company_id, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,  od.quick, od.invoice_no as invoiceno , od.dc_date,
						odv.delivery_price, odv.delivery_pay_type,
				(select  AES_DECRYPT(UNHEX(cmd2.name),'".$db->ase_encrypt_key."') as name from common_member_detail cmd2 where o.bm_code = cmd2.code) as bm_name,
				(select  branch_name from common_branch_detail cbd2 where o.cb_ix = cbd2.cb_ix) as branch_name,
				(select  p.pcode FROM ".TBL_SHOP_PRODUCT." p WHERE p.id=od.pid AND p.product_type NOT IN (".implode(',',$sns_product_type).")) AS pcode
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od , shop_order_delivery odv $where
				group by oid, od.pid, od.od_ix ORDER BY date DESC ";//쿼리 부하가 의심되어 수정함 kbk 12/05/22
		}
	}
		

	//echo nl2br($sql);
	//exit;
	$db1->query($sql);
	
	
	$ordersXL = new PHPExcel();
	
	// 속성 정의
	
	$ordersXL->getProperties()->setCreator("포비즈 코리아")
							 ->setLastModifiedBy("Mallstory.com")
							 ->setTitle("orders List")
							 ->setSubject("orders List")
							 ->setDescription("generated by forbiz korea")
							 ->setKeywords("mallstory")
							 ->setCategory("orders List");
	
	
	if($db1->total){
		$j=0;
	
		// 헤더찍기
		$col = 'A';
		foreach($check_colums as $key => $value){
			$ordersXL->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
			$col++;

			//xlsWriteLabel(0,$j,$columsinfo[$value][title]);
			//$j++;
		}
		if($e_type!="") {//주문 이외의 리스트용 kbk 12/02/28
			if($e_type==ORDER_STATUS_EXCHANGE_APPLY) $ordersXL->getActiveSheet(0)->setCellValue($col . "1", "교환신청일자");
			else if($e_type==ORDER_STATUS_RETURN_APPLY) $ordersXL->getActiveSheet(0)->setCellValue($col . "1", "반품신청일자");
		}
	
		//$mstring_line = "주문번호\t사업자명\t상품코드\t상품명\t과세/면세\t옵션\t주문일\t회원그룹\t주문자명\t연락처1\t연락처2\t받는자\t우편번호\t수취인주소\t연락처1\t연락처2\t판매가\t공급가\t수량\t배송료\t포장비\t상태\t증빙서\t배송완료일\t택배사명\t송장번호\t메모\n";
		
		for ($i=0,$z=0; $i < $db1->total; $i++)
		{
			
			$db1->fetch($i);
	
			//for($x=0;$x < $db1->dt[pcnt];$x++,$z++){//주문수량대로 분리하지않고 합침 kbk 12/06/28
				
				$j="A";
				
				$status = getOrderStatus($db1->dt[status]);
	
				if ($db1->dt[method] == "1")
				{
					if($db1->dt[bank] == ""){
						$method = "카드결제";
					}else{
						$method = $db1->dt[bank];
					}
				}elseif($db1->dt[method] == "0"){
					$method = "계좌입금";
				}elseif($db1->dt[method] == "2"){
					$method = "전화결제";
				}elseif($db1->dt[method] == "4"){// kbk 12/01/10
					$method = "가상계좌";
				}elseif($db1->dt[method] == "5"){// kbk 12/01/10
					$method = "계좌이체";
				}
				if($db1->dt[surtax_yorn] == "Y"){
					$surtax_yorn = "면세";
				}else{
					$surtax_yorn = "과세";
				}

	
				$psum = number_format($db1->dt[total_price]);
	
	
	
				if($db1->dt[receipt_y] == "Y"){
					$receipt_y = "발행";
				}else{
					$receipt_y = "미발행";
				}
	
	
				
				foreach($check_colums as $key => $value){
					//echo $value;
					if($value == "status"){
						$value_str = strip_tags(getOrderStatus($db1->dt[$value]));
					}else if($value == "quick"){
						$value_str = deliveryCompanyList($db1->dt[$value],"excel_text");
					}else if($value == "method"){
						if ($db1->dt[$value] == "1")
						{
							/*if($db1->dt[$value] == ""){
								$value_str = "카드결제";
							}*/// kbk 12/01/10
							$value_str = "카드결제";
						}elseif($db1->dt[$value] == "0"){
							$value_str = "계좌입금";
						}elseif($db1->dt[$value] == "2"){
							$value_str = "전화결제";
						}elseif($db1->dt[$value] == "4"){// kbk 12/01/10
							$value_str = "가상계좌";
						}elseif($db1->dt[$value] == "5"){// kbk 12/01/10
							$value_str = "계좌이체";
						}
					}else if($value == "deliverypaytype"){
						if($db1->dt[$value] == "1"){
							$value_str = "선불";
						}elseif($db1->dt[$value] == "2"){
							$value_str = "착불";
						}else{
							$value_str = "무료";
						}
					}else if($value == "deliverypayuse"){
						if($db1->dt[deliverypaytype] == "1" || $db1->dt[deliverypaytype] == "2"){
							$value_str = "구매자";
						}else{
							$value_str = "판매자";
						}
					}else if($value == "smart_yn"){
						if($db1->dt[smart_yn] == 'Y'){
							$value_str = "스마트 배송";
						}else{
							$value_str = "일반 택배 배송";
						}
					}else{
						if($value == "pcnt"){
							$pcnt = $db1->dt[$value];
							$value_str = $db1->dt[$value];//주문수량대로 분리하지않고 합침 kbk 12/06/28
						}else{
							if($value == "optiontext"){
								$value_str = str_replace(array("color :","COLOR :"),"",$db1->dt[$value]);
								$value_str = str_replace(array("size :","SIZE :","?","\n\r","\n","="),"",$value_str);
								
								
								$value_str = strip_tags($value_str);
								//$value_str = substr($value_str,1,2);
								//echo $value_str."\n";
							}else{
								
								$value_str = $db1->dt[$value];
							}
						}
					}
					//if(is_numeric($value_str) && $value != "invoiceno"){
						//xlsWriteNumber(($z+1),$j,$value_str);
					//}else{
						//xlsWriteLabel(($z+1),$j,$value_str);
						
						//echo("  : " . $j .($z + 1). " " . $value_str);
						//$ordersXL->getActiveSheet()->getColumnDimension($z)->setAutoSize(true);
					//}
					//$value_str = str_replace(array("★"),"",$value_str);
					//echo $value_str . " ";
					$ordersXL->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
					$j++;
				}
				if($e_type!="") {//주문 이외의 리스트용 kbk 12/02/28
					if($e_type==ORDER_STATUS_EXCHANGE_APPLY) $ordersXL->getActiveSheet()->setCellValue($j . ($z + 2), $db1->dt["ex_date"]);
					else if($e_type==ORDER_STATUS_RETURN_APPLY) $ordersXL->getActiveSheet()->setCellValue($j . ($z + 2), $db1->dt["rt_date"]);
				}
				$z++;//주문수량대로 분리하지않고 합침 kbk 12/06/28
				
			//}//주문수량대로 분리하지않고 합침 kbk 12/06/28
		}
	}
//exit;
	$ordersXL->getActiveSheet()->setTitle('매출진행관리');
	$ordersXL->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.iconv("UTF-8","CP949","방문요청목록").'_'.date("Ymd").'.xls"');
		header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($ordersXL, 'Excel5');
	$objWriter->save('php://output');