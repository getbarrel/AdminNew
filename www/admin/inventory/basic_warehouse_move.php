<?

include("../class/layout.class");
include("./inventory.lib.php");



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
		$cut_num = 9;
		break;
}


$where = "where g.gid is not null  ";


if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
	//다중검색 시작 2014-04-10 이학봉
	if($search_text != ""){
		if(strpos($search_text,",") !== false){
			$search_array = explode(",",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			$count_where .= "and ( ";
			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
			$count_where .= ")";
		}else if(strpos($search_text,"\n") !== false){//\n
			$search_array = explode("\n",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			$count_where .= "and ( ";

			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
			$count_where .= ")";
		}else{
			$where .= " and ".$search_type." = '".trim($search_text)."'";
			$count_where .= " and ".$search_type." = '".trim($search_text)."'";
		}
	}
}else{
	if($search_type !="" && $search_text != ""){
		$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
	}
}

if($cid2 != ""){
	//session_register("cid");
	//session_register("depth");
	$where .= " and g.cid LIKE '".substr($cid2,0,$cut_num)."%'";
}

if($pi_ix != ""){
	$where .= "and ips.pi_ix = '".$pi_ix."' ";
}
/*
if($ps_ix != ""){
	$where .= "and wmd.ps_ix = '".$ps_ix."' ";
}
*/

if($company_id != ""){
	$where .= "and ips.company_id = '".$company_id."' ";
}

if($section_type!=""){
	if(is_array($section_type)){
		for($i=0;$i < count($section_type);$i++){
			if($section_type[$i]){
				if($section_type_str == ""){
					$section_type_str .= "'".$section_type[$i]."'";
				}else{
					$section_type_str .= ", '".$section_type[$i]."' ";
				}
			}
		}

		if($section_type_str != ""){
			$where .= "and ps.section_type in ($section_type_str) ";
		}
	}else{
		$where .= "and ps.section_type = '$section_type' ";
	}
}


$sql = "select count(*) as total from 
		(select  g.gid,g.standard,g.item_account,ifnull(bp.ps_ix,0) as basic_ps_ix ,gu.gu_ix,gu.unit,ips.expiry_date,pi.place_name,  pi.company_id, ps.ps_ix, ps.section_name,sum(ips.stock) as stock
		from inventory_goods g
		inner join inventory_goods_unit as gu on (g.gid = gu.gid)
		inner join inventory_product_stockinfo as ips on (ips.gid = gu.gid and ips.unit = gu.unit)
		left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
		left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
		left join  inventory_goods_basic_place bp on (ips.company_id = bp.company_id and ips.pi_ix = bp.pi_ix and ips.gid = bp.gid and ips.unit = bp.unit)
		$where
		group by ips.gid,ips.unit,ips.ps_ix
		 ) data
		where basic_ps_ix != ps_ix and stock > 0
		 ";//상품 리스트 쿼리와 맞춤 kbk 13/08/08

$db->query($sql);
$db->fetch();
$total = $db->dt[total];

if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
}
$str_page_bar = page_bar($total, $page, $max, $query_string,"");

/*
if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&sprice=$sprice&eprice=$eprice&state2=$state2&disp=$disp&brand_name=$brand_name&cid2=$cid2&depth=$depth&company_id=$company_id&pi_ix=$pi_ix&ps_ix=$ps_ix&move_company_id=$move_company_id&move_pi_ix=$move_pi_ix&move_ps_ix=$move_ps_ix&status=&event=$event&best=$best&sale=$sale&wnew=$wnew&mnew=$mnew&sdate=$sdate&edate=$edate");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype");
	//echo $total.":::".$page."::::".$max."<br>";
}
*/

$Contents =	"
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("입/출고재고 기본창고 이동", "입출고관리 > 입/출고재고 기본창고 이동")."</td>
	</tr>";

$Contents .=	"
	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
	<input type='hidden' name='mode' value='search'>
	<input type='hidden' name='cid2' value='$cid2'>
	<input type='hidden' name='depth' value='$depth'>
	<!--input type='hidden' name='sprice' value='0' />
	<input type='hidden' name='eprice' value='1000000' /-->
	<tr >
		<td colspan=2 >
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:0px'>
						<table cellpadding=0 cellspacing=0 border=0 width=100% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>
							<tr>
								<td class='input_box_title'>  <b>선택된 카테고리</b>  </td>
								<td class='input_box_item' colspan=3><b id='select_category_path1'>".($search_text == "" ? getIventoryCategoryPathByAdmin($cid2, $depth)."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."</b></div></td>	
							</tr>
							<tr> 
								<td class='search_box_title'><b>품목분류</b></td>
								<td class='search_box_item' colspan=3>
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
								<td class='input_box_title'>사업장/창고분류</td>
								<td class='input_box_item' >
									".SelectEstablishment($company_id,"company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
									".SelectInventoryInfo($company_id, $pi_ix,'pi_ix','select','false', "")."
								</td>
								<td class='input_box_title'>창고 유형</td>
								<td class='input_box_item' >
									<input type='checkbox' name='section_type[]' value='S' id='section_type_s' ".CompareReturnValue('S',$section_type,' checked')." /><label for='section_type_s'>입고보관장소</label>
									<input type='checkbox' name='section_type[]' value='D' id='sell_type_d' ".CompareReturnValue('D',$section_type,' checked')." /><label for='sell_type_d'>출고보관장소</label>
									<input type='checkbox' name='section_type[]' value='G' id='sell_type_g' ".CompareReturnValue('G',$section_type,' checked')." /><label for='sell_type_g'>기타 로케이션</label>
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>검색어</b>
									<br/>
									<label for='mult_search_use'>(다중검색 체크)</label> <input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
								</td>

								<td class='search_box_item' colspan=3>
									<table cellpadding=0 cellspacing=0 >
										<tr >
											<td>
											<select name='search_type' id='search_type' style=\"font-size:12px;height:22px;min-width:140px;\">
												<option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type).">대표코드</option>
												<option value='g.gid' ".CompareReturnValue("g.gid",$search_type).">품목코드</option>
												<option value='g.gname' ".CompareReturnValue("g.gname",$search_type).">품목명</option>
												<option value='gu.gu_ix' ".CompareReturnValue("gu.gu_ix",$search_type).">시스템코드</option>
												<option value='gu.barcode' ".CompareReturnValue("gu.barcode",$search_type).">바코드</option>
											</select>
											</td>
											<td style='padding-left:5px;' >
												<div id='search_text_input_div'>
													<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
												</div>
												<div id='search_text_area_div' style='display:none;'>
													<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
												</div>
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
	</tr>
	<tr>
		<td align='right' colspan=4 style='padding:5px 0 5px 0;'>
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
		

	/*
	$Contents .= "
			<a href=\"javascript:PoPWindow3('warehouse_move.php?mmode=pop',1000,800,'warehouse_move_apply')\"><img src='../images/".$admininfo["language"]."/btc_warehouse_move.gif' border='0'  style='cursor:pointer;' title=' 창고이동신청'></a>
			<a href='?mmode=pop'> </a> ";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		$Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
		<a href='excel_config.php?".$QUERY_STRING."' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
	}else{
		$Contents .= "
		<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
	}
	*/

	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
		$Contents .= " <a href='basic_warehouse_move.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}else{
		$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}
	

$Contents .= "
			</td>
		</tr>";

$Contents .=	"
	<tr>
		<td valign=top >";

$Contents .= "
		</td>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>
			<form name=listform method=post action='warehouse_move.act.php'  onsubmit='return CheckGoods(this)'  target='act' style='display:inline;'>
			<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
			<input type='hidden' name='act' value='select_warehouse_move'>
			<input type='hidden' id='gu_ix' value=''>";

$innerview = "
			<table cellpadding=3 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='15px'>
				<col width='9%'>
				<col width='*'>
				<col width='9%'>
				<col width='6%'>
				<col width='7%'>
				<col width='6%'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<tr bgcolor='#cccccc' align=center height=30>
					<td class=s_td rowspan=2><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td rowspan=2 nowrap>대표코드<br/>/품목코드</td>
					<td class=m_td rowspan=2 nowrap>품목명</td>
					<td class=m_td rowspan=2 >규격(옵션)</td>
					<td class=m_td rowspan=2 nowrap>단위</td>
					<td class=m_td rowspan=2 nowrap>유통기한</td>
					<td class=e_td rowspan=2 nowrap>현재고</td>
					<td class=m_td colspan=3 nowrap>사업장/창고</td>
					<td class=m_td colspan=2 nowrap>기본창고</td>
				</tr>
				<tr align=center height=30>
					<td class=m_td nowrap>사업장</td>
					<td class=m_td nowrap>창고</td>
					<td class=m_td nowrap>보관장소</td>
					<td class=m_td nowrap>창고</td>
					<td class=m_td nowrap>보관장소</td>
				</tr>
				";

	$sql = "select data.*, (select com_name as company_name from common_company_detail ccd where ccd.company_id = data.company_id limit 1) as company_name,
			pi.place_name as  basic_place_name , ps.section_name as basic_section_name
		from
		(
			select  g.gid,g.gcode,g.standard,g.gname,g.item_account,ifnull(bp.pi_ix,0) as basic_pi_ix ,ifnull(bp.ps_ix,0) as basic_ps_ix ,gu.gu_ix,gu.unit,ips.expiry_date,pi.place_name, pi.company_id, ps.ps_ix, ps.section_name,sum(ips.stock) as stock
			from inventory_goods g
			inner join inventory_goods_unit as gu on (g.gid = gu.gid)
			inner join inventory_product_stockinfo as ips on (ips.gid = gu.gid and ips.unit = gu.unit)
			left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix
			left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
			left join  inventory_goods_basic_place bp on (ips.company_id = bp.company_id and ips.pi_ix = bp.pi_ix and ips.gid = bp.gid and ips.unit = bp.unit)
			$where
			group by ips.gid,ips.unit,ips.ps_ix
		) data
		left join  inventory_place_info pi on data.basic_pi_ix = pi.pi_ix
		left join  inventory_place_section ps on data.basic_ps_ix = ps.ps_ix

		where data.basic_ps_ix != data.ps_ix and stock > 0

		LIMIT $start, $max
	 ";

$db->query($sql);


if($mode == "excel"){

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
	
	$inventory_excel->getActiveSheet(0)->mergeCells('A1:A2');
	$inventory_excel->getActiveSheet(0)->mergeCells('B1:B2');
	$inventory_excel->getActiveSheet(0)->mergeCells('C1:C2');
	$inventory_excel->getActiveSheet(0)->mergeCells('D1:D2');
	$inventory_excel->getActiveSheet(0)->mergeCells('E1:E2');
	$inventory_excel->getActiveSheet(0)->mergeCells('F1:F2');
	$inventory_excel->getActiveSheet(0)->mergeCells('G1:G2');
	$inventory_excel->getActiveSheet(0)->mergeCells('H1:J1');
	$inventory_excel->getActiveSheet(0)->mergeCells('K1:L1');

	$inventory_excel->getActiveSheet(0)->setCellValue('A' . 1, "순");
	$inventory_excel->getActiveSheet(0)->setCellValue('B' . 1, "대표코드");
	$inventory_excel->getActiveSheet(0)->setCellValue('C' . 1, "품목코드");
	$inventory_excel->getActiveSheet(0)->setCellValue('D' . 1, "품목명");
	$inventory_excel->getActiveSheet(0)->setCellValue('E' . 1, "단위");
	$inventory_excel->getActiveSheet(0)->setCellValue('F' . 1, "유통기간");
	$inventory_excel->getActiveSheet(0)->setCellValue('G' . 1, "현재고");
	$inventory_excel->getActiveSheet(0)->setCellValue('H' . 1, "사업장/창고");
	$inventory_excel->getActiveSheet(0)->setCellValue('K' . 1, "기본창고");

	$inventory_excel->getActiveSheet(0)->setCellValue('H' . 2, "사업장");
	$inventory_excel->getActiveSheet(0)->setCellValue('I' . 2, "창고");
	$inventory_excel->getActiveSheet(0)->setCellValue('J' . 2, "보관장소");
	$inventory_excel->getActiveSheet(0)->setCellValue('K' . 2, "창고");
	$inventory_excel->getActiveSheet(0)->setCellValue('L' . 2, "보관장소");

	
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$inventory_excel->getActiveSheet()->setCellValue('A' . ($i + 3), $i+1);
		$inventory_excel->getActiveSheet()->setCellValue('B' . ($i + 3), $db->dt[gcode]);
		$inventory_excel->getActiveSheet()->setCellValue('C' . ($i + 3), $db->dt[gid]);
		$inventory_excel->getActiveSheet()->setCellValue('D' . ($i + 3), $db->dt[gname]);		
		$inventory_excel->getActiveSheet()->setCellValue('E' . ($i + 3), getUnit($db->dt[unit], "basic_unit","","text"));
		$inventory_excel->getActiveSheet()->setCellValue('F' . ($i + 3), $db->dt[expiry_date]);
		$inventory_excel->getActiveSheet()->setCellValue('G' . ($i + 3), $db->dt[stock]);
		$inventory_excel->getActiveSheet()->setCellValue('H' . ($i + 3), $db->dt[company_name]);
		$inventory_excel->getActiveSheet()->setCellValue('I' . ($i + 3), $db->dt[place_name]);
		$inventory_excel->getActiveSheet()->setCellValue('J' . ($i + 3), $db->dt[section_name]);

		if($db->dt[basic_ps_ix]=="0"){
			$inventory_excel->getActiveSheet()->mergeCells('K'.($i + 3).':L'.($i + 3));
			$inventory_excel->getActiveSheet()->setCellValue('K' . ($i + 3), "기본창고 지정안됨");
		}else{
			$inventory_excel->getActiveSheet()->setCellValue('K' . ($i + 3), $db->dt[basic_place_name]);
			$inventory_excel->getActiveSheet()->setCellValue('L' . ($i + 3), $db->dt[basic_section_name]);
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
	

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","기본창고이동.xls").'"');
	header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'CSV');
	$objWriter->setUseBOM(true);
	$objWriter->save('php://output');

	exit;
}

$goods_infos = $db->fetchall();

if(count($goods_infos) == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=12 align=center> 기본창고로 이동할 목록이 없습니다. </td></tr>";
}else{

	for ($i = 0; $i < count($goods_infos); $i++)
	{
		$db->fetch($i);

		//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$goods_infos[$i][id].".gif")){
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))) {
			$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c");
		}else{
			$img_str = "../image/no_img.gif";
		}

	$innerview .= "<tr bgcolor='#ffffff' align=center height=27>
						<td class='list_box_td list_bg_gray' align=center>
							<!--input type=checkbox class=nonborder id='gu_ix' name='gu_ix[]' value='".$goods_infos[$i][gu_ix]."' ps_ix='".$goods_infos[$i][ps_ix]."' delivery_ps_ix='".$goods_infos[$i][basic_ps_ix]."' delivery_cnt='".$goods_infos[$i][stock]."' ".($goods_infos[$i][basic_ps_ix] =="0" ? "disabled" :"")." /-->
							<input type=checkbox class='nonborder check_gu_ix' id='gu_ix' name='wmlistinfo[".$i."][gu_ix]' value='".$goods_infos[$i][gu_ix]."' ".($goods_infos[$i][basic_ps_ix] =="0" ? "disabled" :"")." />
							<input type=hidden name='wmlistinfo[".$i."][ps_ix]' value='".$goods_infos[$i][ps_ix]."' ".($goods_infos[$i][basic_ps_ix] =="0" ? "disabled" :"")." />
							<input type=hidden name='wmlistinfo[".$i."][delivery_ps_ix]' value='".$goods_infos[$i][basic_ps_ix]."'  ".($goods_infos[$i][basic_ps_ix] =="0" ? "disabled" :"")." />
							<input type=hidden name='wmlistinfo[".$i."][delivery_cnt]' value='".$goods_infos[$i][stock]."' ".($goods_infos[$i][basic_ps_ix] =="0" ? "disabled" :"")." />
						</td>
						<td class='list_box_td ' align=center>".$goods_infos[$i][gcode]."<br/>".$goods_infos[$i][gid]."</td>
						<td class='list_box_td point' >
							<table cellpadding=0 cellspacing=0>
								<tr>";
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "c"))) {
		$innerview .= "
									<td bgcolor='#ffffff' align=center style='padding:3px 3px' >
										<a href='../inventory/inventory_goods_input.php?gid=".$goods_infos[$i][gid]."' class='screenshot'  rel='".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $goods_infos[$i][gid], "basic")."'><img src='".$img_str."' width=30 height=30 style='border:1px solid #efefef'></a>
									</td>";
							
		}
		$innerview .= "
									<td bgcolor='#ffffff' align=left style='font-weight:normal;line-height:140%;'>
									<a href=\"javascript:PoPWindow3('../inventory/inventory_goods_input.php?mmode=pop&gid=".$goods_infos[$i][gid]."',970,800,'goods_info')\"><b>".$goods_infos[$i][gname]."</b></a>
									</td>
								</tr>
							</table>
						</td>
					<td bgcolor=#ffffff >".$goods_infos[$i][standard]."</td>
					<td bgcolor=#ffffff>".getUnit($goods_infos[$i][unit], "basic_unit","","text")."</td>
					<!--td bgcolor=#ffffff >".getItemAccount($goods_infos[$i][item_account],"","text")."</td-->
					<td bgcolor=#ffffff >".$goods_infos[$i][expiry_date]."</td>
					<td bgcolor=#ffffff>".number_format($goods_infos[$i][stock])."</td>
					<td bgcolor=#ffffff>".$goods_infos[$i][company_name]."</td>
					<td bgcolor=#ffffff >".$goods_infos[$i][place_name]."</td>
					<td bgcolor=#ffffff >".$goods_infos[$i][section_name]."</td>";
					if($goods_infos[$i][basic_ps_ix]=="0"){
						
						$innerview .= "<td colspan='2'><span class='red'>기본창고 지정안됨</span></td>";
					}else{
						$innerview .= "
						<td bgcolor=#ffffff >".$goods_infos[$i][basic_place_name]."</td>
						<td bgcolor=#ffffff >".$goods_infos[$i][basic_section_name]."</td>";
					}

$innerview .= "
				</tr>";
	}
}
	$innerview .= "</table>
				<table width='100%'>
				<tr height=30>
					<td width=210>
						<b>선택한 품목을 <!--img src='../images/".$admininfo["language"]."/btn_basic_warehouse_move.gif' onclick=\"select_basic_warehouse_move()\" style='cursor:pointer;' align='absmiddle'/--> <input type='image' src='../images/".$admininfo["language"]."/btn_basic_warehouse_move.gif' style='cursor:pointer;' align='absmiddle'/> 하기 </b>
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
				<tr height=30><td colspan=2 align=right></td></tr>
				</table>
			</form>
				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>
		<!--iframe id='act' src='' width=0 height=0></iframe-->
			";


$Script = "<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
<script language='javascript'>


function clearAll(frm){
	for(i=0;i < frm.gu_ix.length;i++){
			frm.gu_ix[i].checked = false;
	}
}

function checkAll(frm){
	for(i=0;i < frm.gu_ix.length;i++){
			frm.gu_ix[i].checked = true;
	}
}

function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}


function unloading(){

	parent.document.getElementById('parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('loadingbar').innerHTML ='';
	parent.document.getElementById('save_loading').innerHTML ='';
	parent.document.getElementById('save_loading').style.display = 'none';
}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_display','batch_update_category','batch_update_reserve'); //,'batch_update_sms','batch_update_coupon'

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}


</script>
";


//$Contents .= HelpBox("발주(사입)작성", $help_text);

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
	<!--script Language='JavaScript' src='product_input.js'></script--><!--2011.06.18 없는게 정상 주석처리후 확인필요-->
	<script Language='JavaScript' src='product_list.js'></script>
	<script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script>
	<script Language='JavaScript' type='text/javascript'>

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

	function CheckGoods (){
		var g_total = 0;
		g_total = $('.check_gu_ix:checked').length;

		if(g_total ==0){
			alert('하나이상 선택하셔야 합니다.');
			return false;
		}else{
			return true;
		}
	}

	/*
	function select_basic_warehouse_move (){
		var g_total = 0;
		g_total = $('input[name^=gu_ix]:checked').length;

		if(g_total ==0){
			alert('하나이상 선택하셔야 합니다.');
			return false;
		}else{
			if(confirm('선택하신 품목을 기본창고로 이동하시겠습니까 ?')){

				$('input[name^=gu_ix]:checked').each(function(){//enabled
					//alert($(this).val()+':'+$(this).attr('delivery_cnt')+':'+$(this).attr('ps_ix')+':'+$(this).attr('delivery_ps_ix'));
					warehouse_move($(this).val(),$(this).attr('delivery_cnt'),$(this).attr('ps_ix'),$(this).attr('delivery_ps_ix'));
				});

				alert('정상적으로 완료 되었습니다.');
				document.location.reload();
			}
		}
	}
	
	function warehouse_move (gu_ix,delivery_cnt,ps_ix,delivery_ps_ix) {
			$.ajax({ 
				type: 'GET', 
				data: {'act': 'warehouse_move','gu_ix':gu_ix,'delivery_cnt':delivery_cnt,'ps_ix':ps_ix,'delivery_ps_ix':delivery_ps_ix},
				url: './warehouse_move.act.php',  
				dataType: 'html', 
				async: false, 
				beforeSend: function(){ 
						//alert(11);
				},  
				success: function(data){ 			
					//alert(data);
					//document.location.reload();
				} 
			});
	}
	*/
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var depth = sel.getAttribute('depth');

		//if(sel.selectedIndex!=0) {
			window.frames['act'].location.href = 'inventory_category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//}
	}
	</script>";

	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->strLeftMenu = inventory_menu();
		$P->addScript = $Script;
		$P->Navigation = "재고관리 > 입출고관리 > 입/출고재고 기본창고 이동";
		$P->NaviTitle = "입/출고재고 기본창고 이동";
		$P->strContents = $Contents;
		$P->jquery_use = false;

		$P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->strLeftMenu = inventory_menu();
		$P->addScript = $Script;
		$P->Navigation = "재고관리 > 입출고관리 > 입/출고재고 기본창고 이동";
		$P->title = "입/출고재고 기본창고 이동";
		$P->strContents = $Contents;
		$P->jquery_use = false;

		$P->PrintLayOut();
	}
}



/*
CREATE TABLE IF NOT EXISTS `inventory_wh_move` (
  `whm_ix` int(10) NOT NULL AUTOINCREMENT COMMENT '요청번호',
  `apply_charger` varchar(255) DEFAULT NULL COMMENT '요청담당자',
  `apply_date` varchar(10) DEFAULT NULL COMMENT '요청일자',
  `status` varchar(2) DEFAULT NULL COMMENT '상태',
  `etc` varchar(255) DEFAULT NULL COMMENT '기타필드',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  `charger_ix` varchar(32) NOT NULL COMMENT '작성자',
  `al_ix` int(10) NOT NULL COMMENT '결제라인',
  PRIMARY KEY (`whm_ix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='창고이동내역';












CREATE TABLE IF NOT EXISTS `inventory_wh_move` (
  `whm_ix` int(10) NOT NULL AUTOINCREMENT COMMENT '요청번호',
  `apply_charger` varchar(255) DEFAULT NULL COMMENT '요청담당자',
  `apply_date` varchar(10) DEFAULT NULL COMMENT '요청일자',
  `limit_priod_e` varchar(10) NOT NULL COMMENT '납기일(종료일)',
  `ci_ix` varchar(255) DEFAULT NULL COMMENT '입고처키',
  `incom_company_charger` varchar(255) DEFAULT NULL COMMENT '업체담당자',
  `b_delivery_price` int(8) DEFAULT '0' COMMENT '사전 현지 운송료',
  `a_delivery_price` int(8) DEFAULT '0' COMMENT '사후 현지 운송료',
  `b_tax` int(8) DEFAULT '0' COMMENT '사전 현지 세금',
  `a_tax` int(8) DEFAULT '0' COMMENT '사후 현지 세금',
  `b_commission` int(8) DEFAULT '0' COMMENT '사전 수수료',
  `a_commission` int(8) DEFAULT '0' COMMENT '사후 수수료',
  `total_price` int(10) DEFAULT '0' COMMENT '발주품목 총 금액',
  `total_add_price` int(10) DEFAULT '0' COMMENT '발주품목 총 추가금액',
  `pttotal_price` int(10) DEFAULT '0' COMMENT '최종결제금액',
  `status` varchar(2) DEFAULT NULL COMMENT '상태',
  `etc` varchar(255) DEFAULT NULL COMMENT '기타필드',
  `real_input_file` varchar(255) DEFAULT NULL COMMENT '실입고증',
  `charger_ix` varchar(32) NOT NULL COMMENT '발주담당자',
  `al_ix` int(10) NOT NULL COMMENT '결제라인',
  `regdate` datetime NOT NULL COMMENT '둥ㅀㄱ알',
  PRIMARY KEY (`ioid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='발주내역';


*/
?>