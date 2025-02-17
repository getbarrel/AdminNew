<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
//ini_set('memory_limit', -1);
include("../class/layout.class");
include("inventory.lib.php");
//auth(8);
$script_time[start] = time();

if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));
	$startDate = date("Y-m-d", $before10day);
	$endDate = date("Y-m-d");
}

if($max == ""){
	$max = 50; //페이지당 갯수
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

if($h_div){
	$where = "where h.h_div = '".$h_div."' and h_type != 'IW' and hd.is_delete != 1 ";
}else{
	$where = "where  h_type != 'IW' ";
}

if($search_text != ""){
	$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
}

if($ci_ix != ""){
	$where .= "and h.ci_ix = '".$ci_ix."' ";
}
if($h_type != ""){
	$where .= "and h.h_type = '".$h_type."' ";
}


if($pi_ix != ""){
	$where .= "and h.pi_ix = '".$pi_ix."' ";
}

if($ps_ix != ""){
	$where .= "and h.ps_ix = '".$ps_ix."' ";
}

if ($regdate != "")	$where .= " and h.vdate between '".str_replace("-","",$startDate)."' and '".str_replace("-","",$endDate)."' ";

$sql = "select count(*) as total
			from inventory_history h
			left join inventory_history_detail hd on h.h_ix = hd.h_ix
			left join inventory_place_info ipi on ipi.pi_ix = h.pi_ix
			left join  inventory_place_section ps on h.ps_ix = ps.ps_ix
			$where ";

//echo nl2br($sql);
//exit;
$script_time[count_start] = time();
$db2->query($sql);
$script_time[count_end] = time();
$db2->fetch();
$total = $db2->dt[total];
//echo $total;

//echo $sql;


if($mode == "excel"){ 
	if($total > '50000'){
		echo("<script>alert('50000건 이상의 데이타는 다운받을수 없습니다.');history.go(-1);</script>");
		exit;
	}
}

$sql = "select hd.*,  h.*, ipi.place_name, ps.section_name,
			g.item_account,
			g.basic_unit,
			hd.amount as delivery_cnt,
			hd.price as delivery_price,
			h.charger_name as name,
			h.h_type as delivery_type,
			h.msg as delivery_msg
			";
		if($mode == "excel"){
			if($info_type == "delivery"){
				//$sql .= ", od.order_from, od.delivery_method, od.quick,od.invoice_no,od2.delivery_price ";
			}
		}
		$sql .= "
		from inventory_history h
		left join inventory_history_detail hd on h.h_ix = hd.h_ix
		left join inventory_place_info ipi on ipi.pi_ix = h.pi_ix
		left join  inventory_place_section ps on h.ps_ix = ps.ps_ix
		left join common_member_detail cmd on h.charger_ix = cmd.code
		left join inventory_goods as g on (hd.gid = g.gid and hd.unit = g.basic_unit)";
		
$sql .= " $where $orderbyString";
		if($mode != "excel"){
			$sql .=" LIMIT $start, $max ";			
		}

$script_time[query_start] = time();
$db->query($sql);
$script_time[query_end] = time();
$goods_infos = $db->fetchall("object");
//print_r($goods_infos);
//exit;


if($mode == "excel"){

	//echo nl2br($sql);
	//exit;

	if($total > '50000'){
		echo("<script>alert('50,000건 이상의 데이타는 메모리 제한으로 다운받을수 없습니다. 필요시 시스템 관리자에게 문의하시기 바랍니다');history.go(-1);</script>");
		exit;
	}

	ini_set('memory_limit','2048M');
	set_time_limit(9999999);

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
/*
	$inventory_excel->getActiveSheet(0)->setCellValue('A' . 1, "순번");
	$inventory_excel->getActiveSheet(0)->setCellValue('B' . 1, "재고품목아이디");
	$inventory_excel->getActiveSheet(0)->setCellValue('C' . 1, "재고품목명");
	$inventory_excel->getActiveSheet(0)->setCellValue('D' . 1, "단품명");
	$inventory_excel->getActiveSheet(0)->setCellValue('E' . 1, "매입처");
	$inventory_excel->getActiveSheet(0)->setCellValue('F' . 1, "사업장/창고");
	$inventory_excel->getActiveSheet(0)->setCellValue('G' . 1, "".$sub_title."수량");
	$inventory_excel->getActiveSheet(0)->setCellValue('H' . 1, "실매입가");
	$inventory_excel->getActiveSheet(0)->setCellValue('I' . 1, "총액");
	$inventory_excel->getActiveSheet(0)->setCellValue('J' . 1, "".$sub_title."유형/비고");
	$inventory_excel->getActiveSheet(0)->setCellValue('K' . 1, "작성자");
	$inventory_excel->getActiveSheet(0)->setCellValue('L' . 1, "".$sub_title."일");
	*/

	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]."(".$columsinfo[$value][value].")");
		$col++;
	}

	$before_pid = "";

	for ($i = 0; $i < count($goods_infos); $i++)
	{
		//$sql .= ", od.order_from, od.delivery_method, od.quick,od.invoice_no,od2.delivery_price ";
		$sql = "select
					od.order_from, od.delivery_method, od.quick,od.invoice_no,od2.delivery_price
				from
					inventory_goods_unit as gu 
					left join shop_order_detail od on (od.oid='".$goods_infos[$i][oid]."' and od.gu_ix=gu.gu_ix)
					left join shop_order_delivery od2  on (od2.oid=od.oid and od2.company_id=od.company_id)
				where
					gu.gid = '".$goods_infos[$i][gid]."'
					and gu.unit = '".$goods_infos[$i][unit]."'";
		$db->query($sql);
		$db->fetch();

		/*$sql .= " left join inventory_goods_unit gu on (gu.gid=hd.gid and gu.unit=hd.unit)
								left join shop_order_detail od on (od.oid=h.oid and od.pcode=gu.gu_ix)
								left join shop_order_delivery od2  on (od2.oid=od.oid and od2.company_id=od.company_id) ";*/
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
			}else if($key == "unit"){
				$value_str = getUnit($goods_infos[$i][unit], "basic_unit","","text")." ";
			}else if($key == "delivery_type"){
				$value_str = selectDeliveryType($type, $h_div, $goods_infos[$i][h_type],'','1','text');
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
			}else if($key == "msg"){
				$value_str =  $goods_infos[$i][msg]." ".$goods_infos[$i][oid];
			}else if($key == "delivery_msg"){
				$value_str =  $goods_infos[$i][delivery_msg]." ".$goods_infos[$i][oid];
			}else if($key == "cname"){
				$value_str = getIventoryCategoryPathByAdmin($goods_infos[$i][cid], 4);
			}else if($key == "regdate"){
				$value_str = substr($goods_infos[$i][vdate],0,4)."-".substr($goods_infos[$i][vdate],4,2)."-".substr($goods_infos[$i][vdate],6,2);
			}else if($key == "unit"||$key == "basic_unit"){
				$value_str = $ITEM_UNIT[$goods_infos[$i][$key]];
			}else{

				if($info_type == "delivery" || $info_type == "warehousing"){
					if($key == "order_from"){
						$value_str = getOrderFromName($db->dt[order_from]);
					}else if($key == "delivery_method"){
						$value_str = DeliveryMethod("", $db->dt[delivery_method],"","text");
					}else if($key == "quick"){
						$value_str = deliveryCompanyList($db->dt[quick],"text"); //strip_tags(SelectSupplyCompany($goods_infos[$i][ci_ix],'ci_ix','text','false'));invoice_no
					}else if($key == "invoice_no"){
						$value_str = $db->dt[invoice_no]; 
					}else if($key == "delivery_price"){
						$value_str = $db->dt[delivery_price]; 
					}else{
						$value_str = $goods_infos[$i][$value];
					}
				}else{
					$value_str = $goods_infos[$i][$value];
				}
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

	/*
	for ($i = 0; $i < count($goods_infos); $i++)
	{

		$inventory_excel->getActiveSheet()->setCellValue('A' . ($i + 2), ($i + 1));
		$inventory_excel->getActiveSheet()->setCellValue('B' . ($i + 2), $goods_infos[$i][gid]);
		$inventory_excel->getActiveSheet()->setCellValue('C' . ($i + 2), $goods_infos[$i][gname]);
		$inventory_excel->getActiveSheet()->setCellValue('D' . ($i + 2), $goods_infos[$i][item_name]);
		$inventory_excel->getActiveSheet()->setCellValue('E' . ($i + 2), $goods_infos[$i][customer_name]);
		$inventory_excel->getActiveSheet()->setCellValue('F' . ($i + 2), $goods_infos[$i][place_name]);
		$inventory_excel->getActiveSheet()->setCellValue('G' . ($i + 2), number_format($goods_infos[$i][input_cnt]));
		$inventory_excel->getActiveSheet()->setCellValue('H' . ($i + 2), number_format($goods_infos[$i][input_price])) ;
		$inventory_excel->getActiveSheet()->setCellValue('I' . ($i + 2), number_format($goods_infos[$i][input_cnt] * $goods_infos[$i][input_price]));
		$inventory_excel->getActiveSheet()->setCellValue('J' . ($i + 2), selectDeliveryType($goods_infos[$i][input_type],'','I','text')."/".$goods_infos[$i][input_msg]);
		$inventory_excel->getActiveSheet()->setCellValue('K' . ($i + 2), $goods_infos[$i][charger]);
		$inventory_excel->getActiveSheet()->setCellValue('L' . ($i + 2), $goods_infos[$i][regdate]);


	}
	*/

	// 첫번째 시트 선택
	$inventory_excel->setActiveSheetIndex(0);

	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}

	// 너비조정
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
	$inventory_excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$inventory_excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	

	
	header('Content-Type: application/vnd.ms-excel');
	if($h_div == 1){
		header('Content-Disposition: attachment;filename="stock_input_hisotry_'.date("YmdHis").'.xls"');
	}else{
		header('Content-Disposition: attachment;filename="stock_output_history_'.date("YmdHis").'.xls"');
	}
	header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'CSV');
	$objWriter->setUseBOM(true);
*/
	if(is_excel_csv()){
		header('Content-Type: application/vnd.ms-excel');
		if($h_div == 1){
			header('Content-Disposition: attachment;filename="stock_input_hisotry_'.date("YmdHis").'.csv"');
		}else{
			header('Content-Disposition: attachment;filename="stock_output_history_'.date("YmdHis").'.csv"');
		}
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'CSV');
		$objWriter->setUseBOM(true);
	}else{
		header('Content-Type: application/vnd.ms-excel');
		if($h_div == 1){
			header('Content-Disposition: attachment;filename="stock_input_hisotry_'.date("YmdHis").'.xls"');
		}else{
			header('Content-Disposition: attachment;filename="stock_output_history_'.date("YmdHis").'.xls"');
		}
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	}


	$objWriter->save('php://output');

	exit;
}


if($_SERVER[QUERY_STRING] == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$_SERVER[QUERY_STRING]) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER[QUERY_STRING]) ;
}

$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			 <tr>
			    <td align='left' colspan=4 > ".GetTitleNavigation("".$sub_title."내역", "재고관리 > $title_str")."</td>
			</tr>

			 <form name='search_frm' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' ><!--target='act'><input type='hidden' name='view' value='innerview'-->
			 <input type='hidden' name='mode' value='search'>
			 <input type='hidden' name='cid' value='$cid'>
			 <input type='hidden' name='depth' value='$depth'>
			 <input type='hidden' name='view_type' value='$view_type'>
				<td colspan=2 >
					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:10'>
								<table cellpadding=0 cellspacing=0 width=100% class='input_table_box'>
									<col width=15%>
									<col width=35%>
									<col width=15%>
									<col width=35%>
									<tr>
										<td class='input_box_title'>   <label for='regdate'>".$sub_title."일 </label>
										<input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeDate(document.search_frm);' ".CompareReturnValue("1",$regdate,"checked")."></td>
										<td class='input_box_item' colspan=3>
											".search_date('startDate','endDate',$startDate,$endDate)."
										</td>
									</tr>
									<tr>
										<td class='input_box_title'> ".$sub_title."처</td>
										<td class='input_box_item'>
										".SelectSupplyCompany($ci_ix,"ci_ix","select", "false",$h_div)."
										</td>
										<td class='input_box_title'> 목록갯수</td>
										<td class='input_box_item'>
										<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle><!-- onchange=\"document.frames['act'].location.href='".$HTTP_URL."?cid=$cid&depth=$depth&view=innerview&max='+this.value\"-->
										<option value='5' ".CompareReturnValue(5,$max).">5</option>
										<option value='10' ".CompareReturnValue(10,$max).">10</option>
										<option value='15' ".CompareReturnValue(15,$max).">15</option>
										<option value='20' ".CompareReturnValue(20,$max).">20</option>
										<option value='50' ".CompareReturnValue(50,$max).">50</option>
										<option value='100' ".CompareReturnValue(100,$max).">100</option>
										</select> <span >한페이지에 보여질 갯수를 선택해주세요</span>
										</td>
									</tr>";
if($page_type == 'warehousing'){
$Contents .=	"
									<tr>
										<td class='input_box_title'> 사업장/창고</td>
										<td class='input_box_item'>
										 ".SelectEstablishment($et_company_id,"et_company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
										".SelectInventoryInfo($et_company_id, $pi_ix,'pi_ix','select','false', "onChange=\"loadPlaceSection(this,'ps_ix')\"  ")."
										".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","false")." 
										</td>
										<td class='input_box_title'> ".$sub_title." 구분</td>
										<td class='input_box_item'>
											<input type='radio' name='h_div' value='' id='h_div_' ".($_GET["h_div"] == "" ? "checked":"")."><label for='h_div_'>전체</label>
											<input type='radio' name='h_div' value='1' id='h_div_1' ".($_GET["h_div"] == "1" ? "checked":"")."><label for='h_div_1'>입고</label>
											<input type='radio' name='h_div' value='2' id='h_div_2' ".($_GET["h_div"] == "2" ? "checked":"")."><label for='h_div_2'>출고</label>
										</td>

									</tr>";
}else{
$Contents .=	"
									<tr>
										<td class='input_box_title'> 사업장/창고</td>
										<td class='input_box_item' colspan=3>
										 ".SelectEstablishment($et_company_id,"et_company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
										".SelectInventoryInfo($et_company_id, $pi_ix,'pi_ix','select','false', "onChange=\"loadPlaceSection(this,'ps_ix')\"  ")."
										".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","false")." 
										</td>
									</tr>";
}
$Contents .=	"
									<tr>
										<td class='input_box_title'> 검색어  </td>
										<td class='input_box_item' style='padding-right:5px;padding-top:2px;'>
											<table cellpadding=0 cellspacing=0>
											<tr>
												<td>
													<select name='search_type' id='search_type'  style=\"font-size:12px;height:22px;min-width:140px;\" validation=false title='검색어'>
														<option value='hd.gid' ".CompareReturnValue("hd.gid",$search_type).">품목코드</option>
														<!--option value='gcode' ".CompareReturnValue("gcode",$search_type).">대표코드</option-->
														<option value='hd.gname' ".CompareReturnValue("hd.gname",$search_type).">품목명</option>
														<option value='h.oid' ".CompareReturnValue("h.oid",$search_type).">주문번호</option>
													</select>
												</td>
												<td style='padding-left:3px;'>
												<!--onkeyup='findNames();' onclick='findNames();' onFocusOut='clearNames()' -->
												<INPUT id=search_texts class='textbox' value='".$search_text."'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 180px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text autocomplete='off' validation=false  title='검색어'><br>
												<DIV id=popup style='display: none; width: 268px; POSITION: absolute; height: 150px; backgorund-color: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
												<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef style='width: 368px;'>

													<tr height=100% >
														<td valign=top bgColor=#efefef style='' colspan=2>
															<table cellpadding=0 cellspacing=0 width=100% height=100% style='margin:0 0px 5px 0px;height: 120px;' bgcolor=#ffffff>
																<tr>
																	<td valign=top >
																	<div style='POSITION: absolute; overflow-y:auto;height: 120px;' id='search_data_area'>
																		<TABLE id=search_table style='table-layout:fixed;width:100%;' cellSpacing=0 cellPadding=0 bgColor=#ffffff border=0>
																		<TBODY id=search_table_body></TBODY>
																		</TABLE>
																	<div>
																	</td>
																</tr>
															</table>

														</td>
													</tr>
													<tr height=24>
														<td width=100%  style='padding:0 0 0 5px'>
														<table width=100% cellpadding=0 cellspacing=0 border=0>
														<tr>
															<td class='p11 ls1'>검색어 자동완성</td>
															<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:hand;padding:0 10px 0 0' align=right>닫기</td>
														</tr>
														</table>
														</td>
													</tr>
												</table>
												</DIV>
												</td>
												<td colspan=2 style='padding-left:5px;'><!--span >* 품목명의 일부를 입력하시면 자동검색됩니다. 2자 이상 입력해주세요</span--></td>
											</tr>
											</table>
										</td>
										<td class='input_box_title'>".$sub_title." 유형  </td>
										<td class='input_box_item' >";

								if($page_type == 'stocked'){
										$Contents .= "".selectDeliveryType('1',$type_div,$h_type,'h_type',"select","false")."";//$h_type 추가 kbk 13/08/07
								}else if($page_type == 'delivery'){
										$Contents .= "".selectDeliveryType('2',$type_div,$h_type,'h_type',"select","false")."";//$h_type 추가 kbk 13/08/07
								}
	//global $id;

				$Contents .= "
										</td>							
									</tr>
									<!--tr hegiht=1><td colspan=4  class='td_underline'></td></tr-->

								</table>
							</td>
							<th class='box_06'></th>
						</tr>
						<tr>
							<th class='box_07'></th>
							<td class='box_08'></td>
							<th class='box_09'></th>
						</tr>
						<tr height=10><td  colspan=3 align=center style='padding:10px 0px 0px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle> <!--btn_inquiry.gif--></td></tr>
					</table>
					</form>
				</td>
			</tr>
			
			
			<tr>
			<td valign=top >";

$Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_list>
			<!--form ><input type=hidden name='mode' value='search'>

			<table width='100%'>
				<tr height=25 align=center>
					<td>품목코드</td>
					<td>제품명</td>
					<td>가격</td>
					<td>검색</td>
				</tr>
				<tr height=25 align=center>
					<td><input type=text name=pid value='$pid' size=8></td>
					<td><input type=text name=pname value='$pname' size=30></td>
					<td nowrap><input type=text name=from_price value='$from_price' size=10> ~ <input type=text name=to_price value='$to_price' size=10></td>
					<td><input type=submit value='search'></td>
					<td><input type=button value='전체보기' onclick=\"document.location.href='/admin/product_list.php'\"></td>
				</tr>
			</table>";

$Contents .=	"
			</form-->";
$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>

			<tr height=30>
				<td align=left>

				<!--b >품목명</b>
				<a href='?cid=$cid&depth=0&orderby=gname&ordertype=asc&view_type=$view_type'><img src='../image/orderby_desc.gif' border=0 align=top alt='가나다순' title='가나다순'></a>
				<a href='?cid=$cid&depth=0&orderby=gname&ordertype=desc&view_type=$view_type'><img src='../image/orderby_asc.gif' border=0 align=top alt='가나다역순' title='가나다역순'></a>
				<b >최종".$sub_title."일</b>
				<a href='?cid=$cid&depth=0&orderby=regdate&ordertype=desc&view_type=$view_type'><img src='../image/orderby_desc.gif' border=0 align=top alt='최근등록순' title='최근등록순'></a>
				<a href='?cid=$cid&depth=0&orderby=regdate&ordertype=asc&view_type=$view_type'><img src='../image/orderby_asc.gif' border=0 align=top alt='등록순' title='등록순'></a-->

				</td>
				<td align=right>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		$innerview .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
		<a href='excel_config.php?info_type=".$page_type."&".$QUERY_STRING."' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
	}else{
		$innerview .= "
		<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
	}

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	$excel_q_string=str_replace("mode=search","",$_SERVER["QUERY_STRING"]);
	if($total > 50000){
		$innerview .= " <a href=\"javascript:alert('50,000건 이상의 데이타는 메모리 제한으로 다운받을수 없습니다. 필요시 시스템 관리자에게 문의하시기 바랍니다');\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}else{
		$innerview .= " <a href='".$_SERVER["PHP_SELF"]."?mode=excel&info_type=".$page_type.$excel_q_string."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}
}else{
$innerview .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}
$innerview .= "
				</td>
				<td align=right></td></tr></table>
			<table cellpadding=2 cellspacing=0 width=100% class='list_table_box'>
				<col  width='4%' >
				<col  width='6%' >
				<col  width='*'>
				<col  width='6%' >
				<col  width='4%' >
				<col  width='6%' >
				<col  width='6%' >
				<col  width='6%' >
				<col  width='6%' >
				<col  width='5%' >
				<col  width='6%' >
				<col  width='6%' >
				<col  width='6%' >
				<col  width='10%'>
			<tr bgcolor='#cccccc' height=30 align=center>
				<td class=s_td rowspan=2>순번</td>
				<td class=m_td  rowspan=2>".$sub_title."번호</td>
				<td class=m_td  rowspan=2>품목코드/".OrderByLink("품목명", "gname", $ordertype)."</td>
				<td class=m_td  rowspan=2>규격</td>
				<td class=m_td  rowspan=2>단위</td>
				<td class=m_td  rowspan=2>".$sub_title."처</td>
				<td class=m_td colspan=3 nowrap>사업장/창고</td>
				<td class=m_td  rowspan=2 nowrap>".$sub_title."유형</td>
				<td class=m_td  rowspan=2 nowrap>".$sub_title."단가<br/>(수량)/합계</td>
				<td class=m_td  rowspan=2>작성자</td>
				<td class=m_td  rowspan=2>".OrderByLink("".$sub_title."일", "h.vdate", $ordertype)."</td>
				<td class=e_td   rowspan=2 nowrap>비고</td>
			</tr>
			<tr align=center height=30>
				<td class=m_td nowrap>사업장</td>
				<td class=m_td nowrap>창고</td>
				<td class=m_td nowrap>보관장소</td>	
			</tr>";



if($orderby != "" && $ordertype != ""){
	$orderbyString = " order by $orderby $ordertype ";
}else{
	//$orderbyString = " order by hd.regdate desc ";
	//$orderbyString = " order by h.vdate desc ";
	$orderbyString = " order by h.regdate desc ";	//입출고 날짜 순서로 DESC 처리함 2014-07-28 이학봉 DPS에서
}

if($db->dbms_type == "oracle"){
	$sql = "select h.*, hd.*,  ipi.place_name, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name
			from inventory_history h
			left join inventory_history_detail hd on h.h_ix = hd.h_ix
			left join inventory_place_info ipi on ipi.pi_ix = h.pi_ix
			left join common_member_detail cmd on h.charger_ix = cmd.code
			$where $orderbyString
			LIMIT $start, $max";
}else{
	$sql = "select h.*, hd.*,  ipi.place_name, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
			from inventory_history h
			left join inventory_history_detail hd on h.h_ix = hd.h_ix
			left join inventory_place_info ipi on ipi.pi_ix = h.pi_ix
			left join common_member_detail cmd on h.charger_ix = cmd.code
			$where $orderbyString
			LIMIT $start, $max";
}
//echo nl2br($sql);
$db->query($sql);

//echo $sql;



if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=16 align=center> 등록된 $title_str이 없습니다.</td></tr>";
}else{


	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		if($db->dt[customer_name] != ""){
			$customer_name = $db->dt[customer_name];
		}

		/*
		$sql = "select count(oid) as total from shop_order where oid = '".$db->dt[oid]."'";
		$cdb->query($sql);
		$cdb->fetch();
		$oid_total = $cdb->dt[total];
		if($cdb->dt[total] > 0){
			$inventory_oid = $db->dt[oid];
		}else{
			$time_oid = explode("-",$db->dt[oid]);
			
			$inventory_oid =substr($time_oid[0],0,4)."-".substr($time_oid[0],4,2)."-".substr($time_oid[0],6,2)." ".substr($time_oid[1],0,2).":".substr($time_oid[1],2,2).":".substr($time_oid[1],4,2);
		}
		*/

		if($h_div=="1"){
			$inventory_oid=$db->dt[ioid];
		}elseif($h_div=="2"){
			$inventory_oid="<a href='/admin/order/orders.edit.php?oid=".$db->dt[oid]."' target='_blank'>".$db->dt[oid]."</a>";
		}else{
			if($db->dt[h_div] == "1"){
				$inventory_oid=$db->dt[ioid];
			}else if($db->dt[h_div] == "2"){
				$inventory_oid="<a href='/admin/order/orders.edit.php?oid=".$db->dt[oid]."' target='_blank'>".$db->dt[oid]."</a>";
			}
		}

		$innerview .= "<tr height=25>
					<td class='list_box_td list_bg_gray'>".$no."</td>
					<td class='list_box_td ' >".$inventory_oid."</td>
					<td class='list_box_td point' >
					<table>
						<tr>";
		if(file_exists(InventoryPrintImage($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/inventory", $db->dt[gid], "c"))){
			$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $db->dt[gid], "c");
			$innerview .= "
							<td width='50' align=center style='padding:0px 5px;'><img src='".$img_str."' width=30 height=30 style='border:1px solid #eaeaea' align=absmiddle></td>";
							
		}
		$innerview .= "
							<td  class='list_box_td'style='text-align:left; padding-right:5px;line-height:150%;'>
							<span class='small'>".$db->dt[gid]."</span><br>
							<!--a href=\"javascript:PoPWindow3('stock_input_detail.php?idx=".$db->dt[o_ix]."&gid=".$db->dt[gid]."&company_code=".$db->dt[inventory_info]."',820,800,'input_detail_pop')\"--><b>".$db->dt[gname]."</b><!--/a-->
							</td>
						</tr>
					</table>
					</td>
					<td class='list_box_td list_bg_gray'>".$db->dt[standard]."</td>
					<td class='list_box_td ' >".getUnit($db->dt[unit], "basic_unit","","text")."</td>
					<td class='list_box_td ' style='padding:5px 5px;'>".$customer_name."</td>
					<td class='list_box_td list_bg_gray' >".$db->dt[com_name]."</td>
					<td class='list_box_td list_bg_gray' >".$db->dt[place_name]."</td>
					<td class='list_box_td list_bg_gray' >".$db->dt[section_name]."</td>
					<td class='list_box_td ' nowrap>";
					if($db->dt[h_div] == "1"){
						$innerview .= "<b>입고</b><br>";
					}else if($db->dt[h_div] == "2"){
						$innerview .= "<b>출고</b><br>";
					}
					$innerview .= "".selectDeliveryType($type, $h_div, $db->dt[h_type],'','1','text')."
					</td>
					<td class='list_box_td list_bg_gray' >".number_format($db->dt[price])."(".number_format($db->dt[amount]).")<br/>".number_format($db->dt[ptprice])."</td>
					<td class='list_box_td ' >".$db->dt[charger_name]."</td>
					<td class='list_box_td list_bg_gray' nowrap>".substr($db->dt[vdate],0,4)."-".substr($db->dt[vdate],4,2)."-".substr($db->dt[vdate],6,2)."</td>
					<td class='list_box_td ' style='padding:3px;line-height:130%;'>".$db->dt[msg]."</td>
					</tr>
					";
			$sum_amount +=	$db->dt[amount]; 
			$sum_price += $db->dt[price];
			$sum_ptprice += $db->dt[ptprice];
	}		

	
	if($h_div == "1" || $h_div=="2"){
		$innerview .= "<tr height=30>
					<td class='list_box_td list_bg_gray' colspan=3><b>합계</b></td>
					<td class='list_box_td ' colspan=3></td>
					<td class='list_box_td ' colspan='3'>수량 : ".$sum_amount."</td>
					<td class='list_box_td ' colspan='3'>단가 : ".number_format($sum_price)."</td>
					<td class='list_box_td ' colspan='2'>최종가 : ".number_format($sum_ptprice)."</td>
					</tr>
					";
	}

}
	$innerview .= "</table>
				<table width='100%'><tr height=30><td align=right>".page_bar($total, $page, $max,$query_string."","")."</td></tr></table>

				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<iframe name='act' src='' width=0 height=0></iframe>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >리스트에서 기본적인 정보를 수정하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >개별정보를 수정후 <img src='../image/btc_modify.gif' align=absmiddle> 버튼를 클릭하시면 해당 제품만을 수정하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >리스트의 여러제품을 수정후 <img src='../image/bt_all_modify.gif' align=absmiddle> 버튼를 클릭하시면 해당 리스트에 보여지는 전체 제품을 수정하실수 있습니다</td></tr>
</table>
";

//$Contents .= HelpBox("품목 리스트", $help_text);



$Script = "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
<script src='stock_input.list.js' type='text/javascript'></script>
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
<script type='text/javascript'>

$(document).ready(function(){
	ChangeDate(document.search_frm);
});

function ChangeDate(frm){
	if(frm.regdate.checked){
		$('#startDate').addClass('point_color');
		$('#endDate').addClass('point_color');
	}else{
		$('#startDate').removeClass('point_color');
		$('#endDate').removeClass('point_color');
	}
}

</script>";


	$P = new LayOut();
	$P->strLeftMenu = inventory_menu();
	$P->OnloadFunction = "";
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 입출고관리 > $title_str";
	$P->title = "$title_str ";
	$P->strContents = $Contents;
	$P->PrintLayOut();


?>

