<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include("inventory.lib.php");
//auth(8);
if ($vToYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", time()-84600*(date("d")-1));
	$eDate = date("Y/m/d", time());

	$startDate = date("Ymd", time()-84600*(date("d")-1));
	$endDate = date("Ymd", time());
}else{
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());

	if($view_type == "summary"){
		$sDate = date("Y/m/d", time()-84600*(date("d")-1));
		$eDate = date("Y/m/d", time());
		$startDate = date("Ymd", time()-84600*(date("d")-1));
		$endDate = date("Ymd", time());
	}else{
		$sDate = $vFromYY."/".$vFromMM."/".$vFromDD;
		$eDate = $vToYY."/".$vToMM."/".$vToDD;
		$startDate = $vFromYY.$vFromMM.$vFromDD;
		$endDate = $vToYY.$vToMM.$vToDD;
	}
}

if($max == ""){
	$max = 20; //페이지당 갯수
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

$where = "where iohd.iohd_ix is not null  ";

if($search_text != ""){
	$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
}
if ($vFromYY != "")	$where .= " and date_format(iohd.regdate, '%Y%m%d') between $startDate and $endDate ";

if($ci_ix != ""){
	$where .= "and iohd.ci_ix = '".$ci_ix."' ";
}

if($pi_ix != ""){
	$where .= "and iohd.pi_ix = '".$pi_ix."' ";
}
/*
	$sql = "select count(*) as total
						from inventory_customer_info ici ,
						common_member_detail cmd ,
						inventory_output_history_detail iohd
						left join inventory_place_info ipi on ipi.pi_ix = iohd.pi_ix
						$where  ";
*/


$sql = "select count(*) as total
				from inventory_output_history_detail iohd
				left join inventory_place_info ipi on ipi.pi_ix = iohd.pi_ix
				left join inventory_customer_info ici on ici.ci_ix = iohd.ci_ix and ici.customer_type = 'E'
				left join common_member_detail cmd on iohd.charger_ix = cmd.code
				$where  $orderbyString";

//echo nl2br($sql);
	$db2->query($sql);
	$db2->fetch();
	$total = $db2->dt[total];

//echo $total;

if($view_type == "summary"){
	$page_title = "출고현황";
}else{
	$page_title = "출고리스트";
}
/*
if($db->dbms_type == "oracle"){
	$sql = "select iohd.*, ipi.place_name, ici.customer_name, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name
				from inventory_output_history_detail iohd
				left join inventory_place_info ipi on ipi.pi_ix = iohd.pi_ix
				left join inventory_customer_info ici on ici.ci_ix = iohd.ci_ix and ici.customer_type = 'D'
				left join common_member_detail cmd on iohd.charger_ix = cmd.code
				$where ";
}else{
	$sql = "select iohd.*, ipi.place_name, ici.customer_name, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
				from inventory_output_history_detail iohd
				left join inventory_place_info ipi on ipi.pi_ix = iohd.pi_ix
				left join inventory_customer_info ici on ici.ci_ix = iohd.ci_ix and ici.customer_type = 'D'
				left join common_member_detail cmd on iohd.charger_ix = cmd.code
				$where ";
}
$db->query($sql);
$goods_infos = $db->fetchall();
*/



if($orderby != "" && $ordertype != ""){
	$orderbyString = " order by $orderby $ordertype ";
}else{
	$orderbyString = " order by iohd.regdate desc ";
}


if($db->dbms_type == "oracle"){
	$sql = "select iohd.*, ipi.place_name, ps.section_name, ici.customer_name, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name
				from inventory_output_history_detail iohd
				left join inventory_place_info ipi on ipi.pi_ix = iohd.pi_ix
				left join  inventory_place_section ps on iohd.ps_ix = ps.ps_ix
				left join inventory_customer_info ici on ici.ci_ix = iohd.ci_ix and ici.customer_type = 'D'
				left join common_member_detail cmd on iohd.charger_ix = cmd.code
				$where  $orderbyString
				LIMIT $start, $max
				";
}else{
	$sql = "select iohd.*, ipi.place_name,  ps.section_name, ici.customer_name, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
				from inventory_output_history_detail iohd
				left join inventory_place_info ipi on ipi.pi_ix = iohd.pi_ix
				left join  inventory_place_section ps on iohd.ps_ix = ps.ps_ix
				left join inventory_customer_info ici on ici.ci_ix = iohd.ci_ix and ici.customer_type = 'D'
				left join common_member_detail cmd on iohd.charger_ix = cmd.code
				$where  $orderbyString
				LIMIT $start, $max
				";
}
//echo nl2br($sql);
$db->query($sql);
$stock_output_infos = $db->fetchall();
//echo $sql;


if($mode == "excel"){
	$info_type = "stock_output";
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
	//exit;
	$columsinfo = $colums;

	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$stock_output_excel = new PHPExcel();

	// 속성 정의
	$stock_output_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");
	/*
	$stock_output_excel->getActiveSheet(0)->setCellValue('A' . 1, "번호");
	$stock_output_excel->getActiveSheet(0)->setCellValue('B' . 1, "재고상품아이디");
	$stock_output_excel->getActiveSheet(0)->setCellValue('C' . 1, "재고상품명");
	$stock_output_excel->getActiveSheet(0)->setCellValue('D' . 1, "단품명");
	$stock_output_excel->getActiveSheet(0)->setCellValue('E' . 1, "창고");
	$stock_output_excel->getActiveSheet(0)->setCellValue('F' . 1, "출고유형/비고");
	$stock_output_excel->getActiveSheet(0)->setCellValue('G' . 1, "출고처");
	$stock_output_excel->getActiveSheet(0)->setCellValue('H' . 1, "출고수량");
	$stock_output_excel->getActiveSheet(0)->setCellValue('I' . 1, "출고가격");
	$stock_output_excel->getActiveSheet(0)->setCellValue('J' . 1, "총액");
	$stock_output_excel->getActiveSheet(0)->setCellValue('K' . 1, "작성자");
	$stock_output_excel->getActiveSheet(0)->setCellValue('L' . 1, "출고일");
	*/

	$col = 'A';
	foreach($check_colums as $key => $value){
		$stock_output_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
		$col++;

		//xlsWriteLabel(0,$j,$columsinfo[$value][title]);
		//$j++;
	}

	$before_pid = "";

	for ($i = 0; $i < count($stock_output_infos); $i++)
	{
		
		/*
		$stock_output_excel->getActiveSheet()->setCellValue('A' . ($i + 2), ($i + 1));
		$stock_output_excel->getActiveSheet()->setCellValue('B' . ($i + 2), $stock_output_infos[$i][gid]);
		$stock_output_excel->getActiveSheet()->setCellValue('C' . ($i + 2), $stock_output_infos[$i][gname]);
		$stock_output_excel->getActiveSheet()->setCellValue('D' . ($i + 2), $stock_output_infos[$i][item_name]);
		$stock_output_excel->getActiveSheet()->setCellValue('E' . ($i + 2), $stock_output_infos[$i][place_name]);
		$stock_output_excel->getActiveSheet()->setCellValue('F' . ($i + 2), selectDeliveryType($stock_output_infos[$i][delivery_type],'','O','text')."/".$stock_output_infos[$i][delivery_msg]);
		$stock_output_excel->getActiveSheet()->setCellValue('G' . ($i + 2), $stock_output_infos[$i][customer_name]);
		$stock_output_excel->getActiveSheet()->setCellValue('H' . ($i + 2), number_format($stock_output_infos[$i][delivery_cnt])) ;
		$stock_output_excel->getActiveSheet()->setCellValue('I' . ($i + 2), number_format($stock_output_infos[$i][delivery_price]));
		$stock_output_excel->getActiveSheet()->setCellValue('J' . ($i + 2), number_format($stock_output_infos[$i][delivery_cnt] * $stock_output_infos[$i][delivery_price]));
		$stock_output_excel->getActiveSheet()->setCellValue('K' . ($i + 2), $stock_output_infos[$i][name]);
		$stock_output_excel->getActiveSheet()->setCellValue('L' . ($i + 2), $stock_output_infos[$i][regdate]);
		*/

		$j="A";
		foreach($check_colums as $key => $value){
			if($key == "delivery_type"){
				$value_str = selectDeliveryType($stock_output_infos[$i][delivery_type],'','O','text');		
			}else if($key == "item_account"){
				$value_str = $ITEM_ACCOUNT[$stock_output_infos[$i][item_account]];
			}else{
				$value_str = $stock_output_infos[$i][$value];//$db1->dt[$value];
			}
			$stock_output_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
			$j++;
		}
		$z++;

	}

	// 첫번째 시트 선택
	$stock_output_excel->setActiveSheetIndex(0);

	$col = 'A';
	foreach($check_colums as $key => $value){
		$stock_output_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}
	// 너비조정
	/*
	$stock_output_excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$stock_output_excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$stock_output_excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$stock_output_excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$stock_output_excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$stock_output_excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$stock_output_excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$stock_output_excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$stock_output_excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$stock_output_excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$stock_output_excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$stock_output_excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	*/

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="stock_output.list.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($stock_output_excel, 'Excel5');
	$objWriter->save('php://output');

	exit;
}

if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&view_type=$view_type&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype&view_type=$view_type","");
	//echo $total.":::".$page."::::".$max."<br>";
}

$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			 <tr>
			    <td align='left' colspan=4 style='padding-bottom:10px;'> ".GetTitleNavigation("$page_title", "재고관리 > $page_title")."</td>
			</tr>

			 <form name='search_frm' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' ><!--target='act'><input type='hidden' name='view' value='innerview'-->
			 <input type='hidden' name='mode' value='search'>
			 <input type='hidden' name='cid' value='$cid'>
			 <input type='hidden' name='depth' value='$depth'>
			 <input type='hidden' name='view_type' value='$view_type'>
				<td colspan=2>
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


$Contents .=	"				<col width=20%>
									<col width=30%>
									<col width=20%>
									<col width=30%>
									<tr>
										<td class='input_box_title'> <label for='regdate'>출고일자 </label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeIncomeDate(document.search_frm);' ".CompareReturnValue("1",$regdate,"checked")."> </td>
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
									</tr>
									<tr>
										<td class='input_box_title'> 출고처</td>
										<td class='input_box_item'>
										".SelectBoxSellCustomer("ci_ix", $ci_ix, "false")."

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
										</select> <span class='small'>한페이지에 보여질 갯수를 선택해주세요</span>
										</td>
									</tr>
									<tr>
										<td class='input_box_title'> 창고</td>
										<td class='input_box_item'>
										 ".makeSelectBoxTargetPlace($pi_ix, 'pi_ix',"","false")."
										</td>
										<td class='input_box_title'> </td>
										<td class='input_box_item'>

										</td>

									</tr>
									<tr>
										<td class='input_box_title'> 검색어  </td>
										<td class='input_box_item'  colspan=3 align=left  style='padding-right:5px;padding-top:2px;'>
											<table cellpadding=0 cellspacing=0>
											<tr>
												<td><select name='search_type' id='search_type'  style=\"font-size:12px;height:22px;\"><option value='gname'>상품명</option><option value='gcode'>상품코드</option></select></td>
												<td style='padding-left:3px;'>
												<INPUT id=search_texts onkeyup='findNames();' class='textbox' value='' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 260px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text autocomplete='off' validation=false  title='검색어'><!--onFocusOut='clearNames()'--><br>

												<DIV id=popup style='display: none; width: 268px; POSITION: absolute; height: 150px; backgorund-color: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
												<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef style='width: 268px;'>
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
												</table>
												</DIV>
												</td>
												<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'>* 상품명의 일부를 입력하시면 자동검색됩니다. 2자 이상 입력해주세요</span></td>
											</tr>
											</table>
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
					</table>

				</td>
			</tr>
			<tr ><td  colspan=2 align=center style='padding-top:20px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle> <!--btn_inquiry.gif--></td></td>
			</form>
			<tr>
			<td valign=top >";

$Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_list>
			<!--form ><input type=hidden name='mode' value='search'>

			<table width='100%'>
				<tr height=25 align=center>
					<td>상품코드</td>
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
				<!--b>상품명</b>
				<a href='?cid=$cid&depth=0&orderby=gname&ordertype=asc&view_type=$view_type'><img src='../image/orderby_desc.gif' border=0 align=top alt='가나다순' title='가나다순'></a>
				<a href='?cid=$cid&depth=0&orderby=gname&ordertype=desc&view_type=$view_type'><img src='../image/orderby_asc.gif' border=0 align=top alt='가나다역순' title='가나다역순'></a>
				<b>출고날짜</b>
				<a href='?cid=$cid&depth=0&orderby=date&ordertype=desc&view_type=$view_type'><img src='../image/orderby_desc.gif' border=0 align=top alt='최근등록순' title='최근등록순'></a>
				<a href='?cid=$cid&depth=0&orderby=date&ordertype=asc&view_type=$view_type'><img src='../image/orderby_asc.gif' border=0 align=top alt='등록순' title='등록순'></a-->

				</td>
				<td align=right>";
				 
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$innerview .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?info_type=stock_output&".$QUERY_STRING."' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$innerview .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
$innerview .= "
				".($view_type == "list" ? "<a href='stock_output.list.php?".str_replace("view_type=list", "mode=excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>" : "")."";
}else{
$innerview .= "
				".($view_type == "list" ? "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>" : "")."";
}
$innerview .= "
				</td>
				<!--td align=right>".$str_page_bar."</td--></tr></table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
			<tr bgcolor='#cccccc' height=30 align=center>
				<td width='5%' class=s_td>번호</td>
				<td width='*' class='m_td' >상품명</td>
				<td width='8%' class='m_td'>규격</td>
				<td width='8%' class=m_td>창고</td>
				<td width='7%' class=m_td>입고일</td>
				<td width='8%' class=m_td>출고유형</td>
				<td width='7%' class=m_td>출고처</td>
				<td width='5%' class=m_td>단위</td>
				<td width='5%' class=m_td>출고수량</td>
				<td width='7%' class=m_td>출고가격</td>
				<td width='7%' class=m_td>작성자</td>
				<td width='12%' class=m_td>출고일</td>
				<td width='9%' class=e_td>비고</td>

			</tr>";


if(count($stock_output_infos) == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=13 align=center> 출고된 내역이 없습니다.</td></tr>";
}else{
	for ($i = 0; $i < count($stock_output_infos); $i++)
	{
		//$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		if(file_exists(InventoryPrintImage($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/inventory", $stock_output_infos[$i][gid], "c"))){
			$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $stock_output_infos[$i][gid], "c");
		}else{
			$img_str = "../image/no_img.gif";
		}

		$sum_delivery_cnt += $stock_output_infos[$i][delivery_cnt];

		$db2->query("select cid from inventory_goods where gid = '".$stock_output_infos[$i][gid]."' ");
		$db2->fetch();

		$innerview .= "<tr height=20>
					<td class='list_box_td list_bg_gray'>".$no."</td>
					<td class='list_box_td point' style='padding:2px 2px;' >
						
						<table  >
								<tr>";
		if(file_exists(InventoryPrintImage($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/inventory", $stock_output_infos[$i][gid], "c"))){
		$innerview .= "
									<td bgcolor='#ffffff' align=center style='padding:3px 3px' >
										<a href='../inventory/inventory_goods_input.php?gid=".$stock_output_infos[$i][gid]."' class='screenshot'  rel='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $stock_output_infos[$i][gid], "basic")."'><img src='".$img_str."' width=30 height=30 style='border:1px solid #efefef'></a>
									</td>";
							
		}
		$innerview .= "
									<td bgcolor='#ffffff' align=left style='font-weight:normal;line-height:140%;'>
									<a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$stock_output_infos[$i][gid]."',970,800,'goods_info')\"><b>".$stock_output_infos[$i][gname]."</b></a>
									</td>
								</tr>
							</table>
					</td>
					<td class='list_box_td list_bg_gray'> ".$stock_output_infos[$i][item_name]."</td>
					<td class='list_box_td'>".$stock_output_infos[$i][place_name]."</td>
					<td class='list_box_td list_bg_gray'>".$stock_output_infos[$i][vdate]."</td>
					<td class='list_box_td'>".selectDeliveryType($stock_output_infos[$i][delivery_type],'','O','text')."</td>
					<td class='list_box_td list_bg_gray' style='padding:0px 5px;' nowrap>".$stock_output_infos[$i][customer_name]."</td>
					<td class='list_box_td ' >".getUnit($stock_output_infos[$i][unit], "basic_unit","","text")."</td>
					<td class='list_box_td'>".$stock_output_infos[$i][delivery_cnt]."</td>
					<td class='list_box_td list_bg_gray'>".$stock_output_infos[$i][delivery_price]."</td>
					<td class='list_box_td'>".$stock_output_infos[$i][name]."</td>
					<td class='list_box_td list_bg_gray' nowrap>".$stock_output_infos[$i][regdate]."</td>
					<td class='list_box_td'>".$stock_output_infos[$i][delivery_msg]."</td>";

	}

	if($view_type == "summary"){
	$innerview .= "<tr height=30>
					<td class='list_box_td list_bg_gray' colspan=7><b>합계</b></td>
					<td class='list_box_td ' >".$sum_delivery_cnt."</td>
					<td class='list_box_td ' colspan=4></td>
					</tr>
					";
	}

}
	$innerview .= "</table>
				<table width='100%'><tr height=30><td align=right>".$str_page_bar."</td></tr></table>

				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<iframe name='act' src='' width=0 height=0></iframe>
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

//$Contents .= HelpBox("상품 리스트", $help_text);


$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";


	$Script = "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>
	<!-- 스크립트 에러 발생으로 주석처리함 kbk -->
	<script src='stock_output.list.js' type='text/javascript'></script>
	<script type='text/javascript'>
function onLoad(FromDate, ToDate) {
	var frm = document.search_frm;


	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);";


if($regdate != "1"){
$Script .= "
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;	";
}
$Script .= "
	init_date(FromDate,ToDate);

}
</script>
";
	$P = new LayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 입출고관리 > $page_title";
	$P->title = "$page_title";
	$P->strContents = $Contents;
	$P->OnloadFunction = "onLoad('$sDate','$eDate');";//"ChangeOrderDate(document.search_frm);";
	$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
	$P->PrintLayOut();





?>

