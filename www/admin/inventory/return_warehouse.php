<?
include("../class/layout.class");
include("../inventory/inventory.lib.php");

//[S] 스크립트

$Script = "
	<script type='text/javascript'>
		function fixAll2(chk)
		{
			var chk = $(chk).is(':checked');
			if(chk == true){
				$('input[name^=dataArr]').attr('checked', true);
			}
			else{
				$('input[name^=dataArr]').attr('checked', false);
			}
		}

		function formCheck()
		{
			var chk = $('input[name^=dataArr]:checked').length;
			if(chk == 0){
				alert('변경할 품목을 선택해주세요.');
				return false;
			}
		}

		function loadCategory(sel,target) {
			//alert(target);
			var trigger = sel.options[sel.selectedIndex].value;
			var form = sel.form.name;
			var depth = sel.getAttribute('depth');

			//빈값일 경우에는 카테고리 정보 불러오는 파일에서 처리함 kbk 13/08/08
			//if(sel.selectedIndex!=0) {
				window.frames['act'].location.href = 'inventory_category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
			//}

		}
	</script>
";

//[E] 스크립트

$db = new MySQL;

//[S] 데이터 출력

$where = " WHERE t1.pi_ix > 0 ";

if(!empty($search_type) && !empty($search_text))
{
	$where .= " AND " . $search_type . " LIKE '%" . $search_text . "%'";
}

if(!empty($cnt))
{
	if($cnt == "P")
	{
		$where .= " AND t1.pcnt > 0 ";
	}
	else
	{
		$where .= " AND t1.bcnt > 0 ";
	}
}

if (!empty($cid2)){
	switch ($depth){
		case 0: $cut_num = 3; break;
		case 1: $cut_num = 6; break;
		case 2: $cut_num = 9; break;
		case 3: $cut_num = 12; break;
		case 4: $cut_num = 15; break;
	}
	$where .= " and t1.cid LIKE '".substr($cid2,0,$cut_num)."%' ";
}

$sql = "SELECT t1.*
		FROM
			(SELECT
				psi.psi_ix,
				pi.pi_ix,
				pi.place_name,
				g.gid,
				g.cid,
				g.gcode,
				g.gname,
				g.item_account,
				gu.gu_ix,
				gu.unit,
				SUM(gu.sellprice) as sellprice,
					(SELECT com_name
					FROM common_company_detail ccd
					WHERE ccd.company_id = pi.company_id
					LIMIT 1) AS company_name,
					(SELECT SUM(stock)
					FROM inventory_product_stockinfo ips2
					INNER JOIN inventory_place_section ps2
					ON ips2.ps_ix = ps2.ps_ix
					WHERE ips2.pi_ix = pi.pi_ix
					AND ips2.unit = gu.unit
					AND ips2.gid = g.gid
					AND ps2.section_type = 'P'
					LIMIT 1) AS pcnt,
					(SELECT SUM(stock)
					FROM inventory_product_stockinfo ips2
					INNER JOIN inventory_place_section ps2
					ON ips2.ps_ix = ps2.ps_ix
					WHERE ips2.pi_ix = pi.pi_ix
					AND ips2.unit = gu.unit
					AND ips2.gid = g.gid
					AND ps2.section_type = 'B'
					LIMIT 1) AS bcnt
			FROM inventory_place_info pi
			INNER JOIN inventory_product_stockinfo psi
			ON pi.pi_ix = psi.pi_ix
			LEFT JOIN inventory_goods_unit gu
			ON psi.gid = gu.gid AND psi.unit = gu.unit
			LEFT JOIN inventory_goods g
			ON gu.gid = g.gid
			GROUP BY gu.gu_ix, pi.pi_ix) t1
		".$where."
";
$db->query($sql);
$total = $db->total;
$results = $db->fetchall();

$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$max = isset($_REQUEST['max']) ? $_REQUEST['max'] : 10;
$start = $start = ($page - 1) * $max;
$no = $total - ($page - 1) * $max;
$str_page_bar = page_bar($total, $page,$max, "&orderby=$orderby&ordertype=$ordertype".$search_query, "");

if($db->total > 0)
{
	for($i=0; $i<count($results); $i++)
	{
		$arraySum_g[] = $results[$i]["pcnt"];
	}
	for($i=0; $i<count($results); $i++)
	{
		$arraySum_b[] = $results[$i]["bcnt"];
	}

	$goodWarehouses = array_sum($arraySum_g);
	$badWarehouses = array_sum($arraySum_b);
}

//[E] 데이터 출력

$Contents = "
<div style='padding:10px 15px;background:#efefef;'><img src='../image/title_head.gif' align='absmiddle'> <b>보관장소 리스트</b></div>

<table width='100%' cellpadding='0' cellspacing='0' style='margin-top:15px;'>
	<tbody>
		<tr>
			<td colspan='2'>
				<table cellpadding='0' cellspacing='1' border='0' width='100%' align='center' class='input_table_box'>
					<colgroup><col width='150'>
						<col width='33%'>
						<col width='33%'>
						<col width='33%'>
					</colgroup>
					<tbody>
						<tr>
							<td class='input_box_title' style='text-align:center;'>품목수</td>
							<td class='input_box_title' style='text-align:center;'>양호 보유현황</td>
							<td class='input_box_title' style='text-align:center;'>불량 보유현황</td>
						</tr>
						<tr>
							<td class='input_box_item' style='text-align:center;'>".$total."</td>
							<td class='input_box_item' style='text-align:center;'>".$goodWarehouses."</td>
							<td class='input_box_item' style='text-align:center;'>".$badWarehouses."</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<form name='search_form' method='get' action=''>
<input type='hidden' name='cid2' value='$cid2'>
<input type='hidden' name='depth' value='$depth'>

	<table width='100%' cellpadding='0' cellspacing='0' style='margin-top:35px;'>
		<tbody>
			<tr>
				<td colspan='2'>
					<table cellpadding='0' cellspacing='1' border='0' width='100%' align='center' class='input_table_box'>
						<colgroup>
							<col width='12%'>
							<col width='50%'>
							<col width='12%'>
							<col width='26%'>
						</colgroup>
						<tbody>
							<tr>
								<td class='input_box_title'>품목분류</td>
								<td class='input_box_item' colspan='3'>
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
								<td class='input_box_title'>검색</td>
								<td class='input_box_item'>
									<select name='search_type' style='min-width:140px;width:140px;'>
										<option value='t1.gid' ".($search_type == "t1.gid" ? "selected" : "").">대표코드</option>
										<option value='t1.gcode' ".($search_type == "t1.gcode" ? "selected" : "").">품목코드</option>
										<option value='t1.gu_ix' ".($search_type == "t1.gu_ix" ? "selected" : "").">품목 단위코드</option>
									</select> 
									<input type='text' class='textbox' name='search_text' size='30' value='".$search_text."' style='border: 1px solid rgb(204, 204, 204);'>						
								</td>
								<td class='input_box_title'>상태</td>
								<td class='input_box_item'>
									<label><input type='radio' name='cnt' value='' ".(!$cnt ? "checked" : "")."/>전체</label>
									&nbsp;
									<label><input type='radio' name='cnt' value='P' ".($cnt == "P" ? "checked" : "")."/>양호</label>
									&nbsp;
									<label><input type='radio' name='cnt' value='B' ".($cnt == "B" ? "checked" : "")."/>불량</label>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan='8' align='center' style='padding:10px 0px;'><input type='image' src='../images/korea/bt_search.gif' border='0' align='absmiddle'><!--btn_inquiry.gif--></td>
			</tr>
		</tbody>
	</table>

</form>

<form name='listform' action='return_warehouse.act.php' onsubmit='return formCheck()' method='POST' target='act'>
	
	<table width='100%' cellpadding='0' cellspacing='0' style='margin-top:35px;'>
		<tbody>
			<tr>
				<td colspan='2'>
					
					<table width='100%' cellpadding='3' cellspacing='0' border='0'>
						<colgroup>
							<col width='10%'>
							<col width='70%'>
							<col width='13%'>
							<col width='7%'>
						</colgroup>
						<tbody>
							<tr>
								<td></td>
								<td align='left' height='30'></td>
								<td align='right'>
									목록수 : 
									<select name='max' onchange=\"location.href='?max='+this.value\">
										<option value='5' ".($max == 5 ? "selected" : "").">5</option>
										<option value='10' ".($max == 10 ? "selected" : "").">10</option>
										<option value='20' ".($max == 20 ? "selected" : "").">20</option>
										<option value='30' ".($max == 30 ? "selected" : "").">30</option>
										<option value='50' ".($max == 50 ? "selected" : "").">50</option>
										<option value='100' ".($max == 100 ? "selected" : "").">100</option>
										<option value='500' ".($max == 500 ? "selected" : "").">500</option>
										<option value='1000' ".($max == 1000 ? "selected" : "").">1000</option>
										<option value='2000' >2000</option>
									</select>
								</td>
								<td align='right'>
									<a href='return_warehouse.excel.php?".$_SERVER["QUERY_STRING"]."'><img src='../images/korea/btn_excel_save.gif' /></a>
								</td>
							</tr>
						</tbody>
					</table>

					<table cellpadding='0' cellspacing='1' border='0' width='100%' align='center' class='input_table_box'>
						<colgroup>
							<col width='3%'>
							<col width='12%'>
							<col width='6%'>
							<col width='*'>
							<col width='14%'>
							<col width='6%'>
							<col width='11%'>
							<col width='11%'>
							<col width='5%'>
							<col width='5%'>
							<col width='8%'>
						</colgroup>
						<tbody>
							<tr>
								<td class='s_td' rowspan=2>
									<input type='checkbox' name='all_fix' onclick='fixAll2(this)'>
								</td>
								<td class='m_td' rowspan=2>대표코드<br/>/품목코드</td>
								<td class='m_td' rowspan=2>품목<br/>단위코드</td>
								<td class='m_td' rowspan=2>이미지/품목명</td>
								<td class='m_td' rowspan=2>품목계정</td>
								<td class='m_td' rowspan=2>단위</td>
								<td class='m_td' rowspan=2>사업장</td>
								<td class='m_td' rowspan=2>창고/창고키</td>
								<td class='m_td' colspan=2 style='padding:3px 0;'>재고현황</td>
								<td class='e_td' rowspan=2>재고자산</td>
							</tr>
							<tr>
								<td class='m_td' style='padding:3px 0;'>양호</td>
								<td class='m_td'>불량</td>
							</tr>
";

if($total > 0)
{
	$results = array_slice($results, $start, $max);

	foreach($results as $key => $val)
	{
		$Contents .= "
			<tr>
				<td class='list_box_td' style='text-align:center;'>
					<input type='checkbox' name='dataArr[]' value='".$val["gu_ix"]."|".$val["pi_ix"]."|".$val["pcnt"]."|".$val["bcnt"]."' />
				</td>
				<td class='list_box_td' style='text-align:center;'>
					".$val["gid"]."<br/>".$val["gcode"]."
				</td>
				<td class='list_box_td'>
					".$val["gu_ix"]."
				</td>
				<td class='list_box_td point' style='text-align:center;'>
					".$val["gname"]."
				</td>
				<td class='list_box_td' style='text-align:center;'>
					".$ITEM_ACCOUNT[$val["item_account"]]."
				</td>
				<td class='list_box_td' style='text-align:center;'>
					".getUnit($val["unit"], "basic_unit","","text")."
				</td>
				<td class='list_box_td' style='text-align:center;'>
					".$val["company_name"]."
				</td>
				<td class='list_box_td' style='text-align:center;padding:8px;'>
					".$val["place_name"]."<br/>(".$val["pi_ix"].")
				</td>
				<td class='list_box_td' style='text-align:center;'>
					".number_format($val["pcnt"])."
					<input type='hidden' name='stockGood' value='".$val["pcnt"]."' />
				</td>
				<td class='list_box_td' style='text-align:center;'>
					".number_format($val["bcnt"])."
					<input type='hidden' name='stockBad' value='".$val["bcnt"]."' />
				</td>
				<td class='list_box_td' style='text-align:center;'>
					".number_format($val["sellprice"])."
				</td>
			</tr>
		";
	}
}
else
{
	$Contents .= "
		<tr>
			<td class='list_box_td' colspan=11 style='padding:30px;'>등록된 데이터가 없습니다.</td>
		</tr>
	";
}

$Contents .= "
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<table width='100%'>
		<tbody>
			<tr height='30'>
				<td width='210'></td>
				<td align='right'>
					<div id='page_area' style='margin-top:15px;'>
						".$str_page_bar."
					</div>
				</td>
			</tr>
			<tr height='30'>
				<td colspan='2' align='right'></td>
			</tr>
		</tbody>
	</table>

	<table width='100%' border='0'>
		<tbody>
			<tr>
				<td colspan='3' nowrap=''>
					<div style='z-index:0;vertical-align:bottom;position:relative;top:12px;left:10px;width:250px;height:15px;font-weight:bold;padding:0px 10px 0px 55px;background-color:#ffffff' class='help_title' nowrap=''> 
						<nobr>
							<select name='update_type'>
								<!--option value='1'>검색한주문 전체에게</option-->
								<option value='2'>선택한주문 전체에게</option>
							</select>
							<input type='radio' name='update_kind' id='update_kind_level0' value='level0' onclick='ChangeUpdateForm('help_text_level0');' checked=''><label for='update_kind_level0'>재고이동</label>
						</nobr>
					</div>
				</td>
			</tr>
			<tr height='2'><td class='help_col' colspan='3'></td></tr>
			<tr height=''>
				<td width='1' class='help_row1' style='padding-bottom:5px;'></td>
				<td class='top p10 lh160' style='padding-bottom:5px;'>
					<table width='100%' cellpadding='0' cellspacing='0' style='margin-top:15px;'>
						<tbody>
							<tr>
								<td colspan='2'>
									<table cellpadding='0' cellspacing='1' border='0' width='100%' align='center' class='input_table_box'>
										<colgroup>
											<col width='15%'>
											<col width='*'>
										</colgroup>
										<tbody>
											<tr>
												<td class='input_box_title'>재고이동</td>
												<td class='input_box_item'>
													<label><input type='radio' name='moveType' value='B' checked='checked' />기본창고로 이동</label>
													&nbsp;&nbsp;
													<label><input type='radio' name='moveType' value='GB' />양호->불량</label>
													&nbsp;&nbsp;
													<label><input type='radio' name='moveType' value='BG' />불량->양호</label>
													&nbsp;&nbsp;
													<label><input type='radio' name='moveType' value='BR' />불량->반품</label>
													&nbsp;&nbsp;
													<label><input type='radio' name='moveType' value='BD' />불량->폐기(손망실)</label>
													&nbsp;&nbsp;&nbsp;
													<strong>* 기본창고 이동은 양호로 되어있는 품목만 이동 됩니다.</strong>
												</td>
											</tr>
										</tbody>
									</table>

									<div style='margin-top:15px;text-align:center;'>
										<input type='image' src='../images/korea/b_save.gif' border='0' style='cursor:pointer;border:0px;'>
									</div>

									</form>

									<form name='listform' action='return_warehouse.act.php' onsubmit='return formCheck()' enctype='multipart/form-data' method='POST' target='act'>
									<input type='hidden' name='act' value='excelUpload'>

									<div style='margin-top:20px;padding:5px 10px;background:#efefef;'><img src='../image/title_head.gif' align='absmiddle'> <b>엑셀 업로드</b></div>

									<table cellpadding='0' cellspacing='1' border='0' width='100%' align='center' class='input_table_box' style='margin-top:8px;'>
										<colgroup>
											<col width='15%'>
											<col width='*'>
										</colgroup>
										<tbody>
											<tr>
												<td class='input_box_title'>재고이동</td>
												<td class='input_box_item'>
													<label><input type='radio' name='moveType' value='B' checked='checked' />기본창고로 이동</label>
													&nbsp;&nbsp;
													<label><input type='radio' name='moveType' value='GB' />양호->불량</label>
													&nbsp;&nbsp;
													<label><input type='radio' name='moveType' value='BG' />불량->양호</label>
													&nbsp;&nbsp;
													<label><input type='radio' name='moveType' value='BR' />불량->반품</label>
													&nbsp;&nbsp;
													<label><input type='radio' name='moveType' value='BD' />불량->폐기(손망실)</label>
													&nbsp;&nbsp;&nbsp;
												</td>
											</tr>
											<tr>
												<td class='input_box_title'>샘플파일</td>
												<td class='input_box_item'>
													<a href='warehouse_sample.xls'><span class='btnSmall'>샘플파일 다운로드</span></a>
												</td>
											</tr>
											<tr>
												<td class='input_box_title'>엑셀파일 업로드</td>
												<td class='input_box_item'>
													<input type='file' name='excel_file' />
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td width='1' class='help_row2' style='padding:0;'></td>
			</tr>
			<tr>
				<td width='1' class='help_row1'></td>
				<td align='center' style='padding:10px 0 20px;'><input type='image' src='../images/korea/b_save.gif' border='0' style='cursor:pointer;border:0px;'></td>
				<td width='1' class='help_row2'></td>
			</tr>
			<tr height='2'>
				<td class='help_col' colspan='3'></td>
			</tr>
		</tbody>
	</table>

</form>
";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = inventory_menu();
$P->Navigation = "반품창고 현황";
$P->title = "반품창고 현황";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>