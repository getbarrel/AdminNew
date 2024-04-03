<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include("./inventory.lib.php");


if($admininfo[admin_level] < 9){
	//header("Location:/admin/seller/");
}

if(!$title_str){
	$title_str="창고이동현황";
}

if($_COOKIE[inventory_move_max_limit]){
	$max = $_COOKIE[inventory_move_max_limit]; //페이지당 갯수
}else{
	$max = 10;
}


if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;
$db2 = new Database;


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

$where = "where wm.wm_ix is not null  ";

if($search_text != ""){
	$where .= "and ".$search_type." LIKE '%".trim($search_text)."%' ";
}

if($move_company_id != ""){
	$where .= "and wm.move_company_id = '".$move_company_id."' ";
}

if($move_pi_ix != ""){
	$where .= "and wm.move_pi_ix = '".$move_pi_ix."' ";
}

if($move_ps_ix != ""){
	$where .= "and wm.move_ps_ix = '".$move_ps_ix."' ";
}

if($sdate != "" && $edate != ""){
	//$where .= " and  date_format(wm.regdate,'%Y%m%d') between  $sdate and $edate ";
	$where .= " and  replace(".$date_type.",'-','') between  ".str_replace("-","",$sdate)." and ".str_replace("-","",$edate)." ";//수정 kbk 13/08/08
}


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


if(is_array($status)){
	for($i=0;$i < count($status);$i++){
		if($status[$i]){
			if($status_str == ""){
				$status_str .= "'".$status[$i]."'";
			}else{
				$status_str .= ", '".$status[$i]."' ";
			}
		}
	}

	if($status_str != ""){
		$where .= "and wm.status in ($status_str) ";
	}
}else{
	if($status){
		$where .= "and wm.status = '$status' ";
	}
}

if(is_array($h_type)){
	for($i=0;$i < count($h_type);$i++){
		if($h_type[$i]){
			if($h_type_str == ""){
				$h_type_str .= "'".$h_type[$i]."'";
			}else{
				$h_type_str .= ", '".$h_type[$i]."' ";
			}
		}
	}

	if($h_type_str != ""){
		$where .= "and wm.h_type in ($h_type_str) ";
	}
}else{
	if($h_type){
		$where .= "and wm.h_type = '$h_type' ";
	}
}

if($charger_ix != ""){
	$where .= " and wm.apply_charger_ix = '".$charger_ix."' ";
}

if($this_company_id != ""){
	$where .= "and wm.now_company_id = '".$this_company_id."' ";
}

if($pi_ix != ""){
	$where .= "and wmd.pi_ix = '".$pi_ix."' ";
}

if($ps_ix != ""){
	$where .= "and wmd.ps_ix = '".$ps_ix."' ";
}

/* 쿼리 이슈 있어서 주석처리!
		left join inventory_goods_unit gu on wmd.gid=gu.gid and wmd.unit=gu.unit
		left join inventory_goods g on gu.gid=g.gid

*/

$sql = "select count(*) as total from 
		(select  wm.*, pi.place_name as move_place_name, ps.section_name as move_section_name
		from inventory_warehouse_move wm 
		inner join inventory_warehouse_move_detail as wmd on (wm.wm_ix = wmd.wm_ix)
		left join  inventory_place_info pi on wm.move_pi_ix = pi.pi_ix
		left join  inventory_place_section ps on wm.move_ps_ix = ps.ps_ix
		$where    
		 ) data
		 ";//상품 리스트 쿼리와 맞춤 kbk 13/08/08

$db2->query($sql);
$db2->fetch();
$total = $db2->dt[total];


$vdate = date("Ymd", time());
$today = date("Y-m-d", time());
$vyesterday = date("Y-m-d", time()-84600);
$voneweekago = date("Y-m-d", time()-84600*7);
$vtwoweekago = date("Y-m-d", time()-84600*14);
$vfourweekago = date("Y-m-d", time()-84600*28);
$vyesterday = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

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
<script  id='dynamic'></script>
<table cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("$title_str", "입출고관리 > $title_str")."</td>
	</tr>
	<!--tr>
		<td align='left' colspan=2 style='padding-bottom:12px;'>
			<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_00'  ".($move_status == '' ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='warehouse_move_list.php'\">창고이동 전체리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_01'  ".($move_status == 'MA'  ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='warehouse_move_list.php?move_status=MA'\">이동요청 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_01' ".($move_status == 'MO'  ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='warehouse_move_list.php?move_status=MO'\">이동 출고 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_01' ".($move_status == 'MI'  ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='warehouse_move_list.php?move_status=MI'\">이동중 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_01' ".($move_status == 'MC'  ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='warehouse_move_list.php?move_status=MC'\">이동입고 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
						</td>
						<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr-->
	<tr>
		<td align='left' colspan=2 style='padding-bottom:12px;'>
			<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_00'  ".(basename($_SERVER["PHP_SELF"])== 'warehouse_move_list.php' ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='warehouse_move_list.php'\">요청 건별 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_01'  ".(basename($_SERVER["PHP_SELF"]) == 'warehouse_move_detail_list.php'  ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='warehouse_move_detail_list.php'\">상품별 상세 리스트</td>
									<th class='box_03'></th>
								</tr>
							</table>
						</td>
						<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	";

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
							<tr height=27>
							  <td class='search_box_title'>
									<select name='date_type'  style=\"font-size:12px;height:20px;min-width:90px;\">
										<option value='wm.wm_apply_date' ".CompareReturnValue("wm.wm_apply_date",$date_type).">요청일자</option>
										<option value='wm.wm_delivery_date' ".CompareReturnValue("wm.wm_delivery_date",$date_type).">출고일자</option>
										<option value='wm.wm_entering_date' ".CompareReturnValue("wm.wm_entering_date",$date_type).">입고일자</option>
									</select>
							  </td>
							  <td class='search_box_item' colspan=3 >
								<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
									<col width=100>
									<col width=20>
									<col width=100>
									<col width=*>
									<tr>
										<TD nowrap>
										<input type='text' class='textbox point_color' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:110px;text-align:center;' id='start_datepicker'>
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
							</tr>";
				if($warehouse_list_type == "MI"){
					/*
					$Contents .=	"
							<tr>
								<td class='input_box_title'>품목출고 사업장</td>
								<td class='input_box_item' colspan=3>
									".SelectEstablishment("","this_company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
									".SelectInventoryInfo("", $pi_ix,'pi_ix','select','false', "onChange=\"loadPlaceSection(this,'ps_ix')\"  ")."
									".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","false")." 
								</td>	
							</tr>";
					*/
				}else{
					$Contents .=	"
							<tr>
								<td class='input_box_title'>이동전 창고</td>
								<td class='input_box_item'>
									".SelectEstablishment($this_company_id,"this_company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
									".SelectInventoryInfo($this_company_id, $pi_ix,'pi_ix','select','false', "onChange=\"loadPlaceSection(this,'ps_ix')\"  ")."
									".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","false")." 
								</td>
								<td class='input_box_title'>이동창고</td>
								<td class='input_box_item'>
									".SelectEstablishment($move_company_id,"move_company_id","select","false","onChange=\"loadPlace(this,'move_pi_ix')\" ")."
									".SelectInventoryInfo($move_company_id,$move_pi_ix,'move_pi_ix','select','false', "onChange=\"loadPlaceSection(this,'move_ps_ix')\"  ")."
									".SelectSectionInfo($move_pi_ix,$move_ps_ix,'move_ps_ix',"select","false")." 
								</td>
							</tr>";
				
				}

				$Contents .=	"
							<tr>
								<td class='input_box_title'>창고이동 처리상태</td>
								<td class='input_box_item'>
									".getInventoryStatus("ALL", "status","","checkbox")." 
								</td>	
								<td class='input_box_title'>창고이동유형</td>
								<td class='input_box_item'>
									".selectType('3',"5",$h_type,'h_type','checkbox',false,"")."
								</td>
							</tr>
							<tr>
								<td class='search_box_title'><b>검색어</b>
									<br/>
									<label for='mult_search_use'>(다중검색 체크)</label> <input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
								</td>
								<td class='search_box_item'>
									<table cellpadding=0 cellspacing=0 >
										<tr >
											<td>
											<select name='search_type' id='search_type' style=\"font-size:12px;height:22px;min-width:140px;\">
												<!--option value='g.gcode' ".CompareReturnValue("g.gcode",$search_type).">대표코드</option-->
												<option value='wmd.gid' ".CompareReturnValue("wmd.gid",$search_type).">품목코드</option>
												<option value='wmd.gname' ".CompareReturnValue("wmd.gname",$search_type).">품목명</option>
												<!--option value='gu.barcode' ".CompareReturnValue("gu.barcode",$search_type).">바코드</option-->
												<option value='wm.wm_ix' ".CompareReturnValue("wm.wm_ix",$search_type).">요청번호</option>
											</select>
											</td>
											<td style='padding-left:5px;'>
												<div id='search_text_input_div'>
													<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
												</div>
												<div id='search_text_area_div' style='display:none;'>
													<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
												</div>
											</td>
											<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
										</tr>
									</table>
								</td>
								<td class='input_box_title'>담당자</td>
								<td class='input_box_item'>
									".CompayChargerSearch($_SESSION["admininfo"]["company_id"] ,$charger_ix,"","selectbox")."
								</td>
							</tr>
							<!--창고이동입고에는 필료없어 노출하지 않은<tr>
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
							</tr>-->
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
					<option value='5' ".($_COOKIE[inventory_move_max_limit] == '5'?'selected':'').">5</option>
					<option value='10' ".($_COOKIE[inventory_move_max_limit] == '10'?'selected':'').">10</option>
					<option value='20' ".($_COOKIE[inventory_move_max_limit] == '20'?'selected':'').">20</option>
					<option value='30' ".($_COOKIE[inventory_move_max_limit] == '30'?'selected':'').">30</option>
					<option value='50' ".($_COOKIE[inventory_move_max_limit] == '50'?'selected':'').">50</option>
					</select>
			</span>

			<a href=\"javascript:PoPWindow3('warehouse_move.php?mmode=pop',1000,800,'warehouse_move_apply')\"><img src='../images/".$admininfo["language"]."/btc_warehouse_move.gif' border='0'  style='cursor:pointer;' title=' 창고이동신청'></a>
			";
		/*
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?".$QUERY_STRING."' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$Contents .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= " <a href='stock_report.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
		*/
	$Contents .= "
				</td>
			</tr>";

$Contents .=	"
	<tr>
		<td valign=top >";

$Contents .= "
		</td>
		<form name=listform method=post  onsubmit='return SelectUpdate(this)'  target='iframe_act' style='display:inline;'><!--onsubmit='return CheckDelete(this)' -->
		<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
		<input type='hidden' id='pid' value=''>
		<input type='hidden' name='act' value='update'>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

//$innerview = "<ul class='total_cnt_area' style='width:100%;'>
//					<li class='back'>".$str_page_bar."</li>
//				  </ul>";
$innerview = "
			<table cellpadding=3 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='15px'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='*'>
				<col width='35px'>
				<col width='8%'>
				<col width='50px'>
				<col width='50px'>
				<col width='50px'>
				<col width='70px'>
				<col width='7%'>
				<col width='7%'>
				<col width='7%'>
				<col width='70px'>
				<col width='60px'>
				<tr bgcolor='#cccccc' align=center height=30>
					<td class=s_td rowspan=2><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td rowspan=2 nowrap>요청번호</td>
					<td class=m_td rowspan=2 nowrap>요청일</td>
					<td class=m_td rowspan=2 nowrap>담당자</td>
					<td class=m_td rowspan=2 >품목코드</td>
					<td class=m_td rowspan=2 >품목</td>			
					<td class=m_td rowspan=2 nowrap>단위</td>
					<td class=m_td rowspan=2 nowrap>규격</td>
					<td class=m_td rowspan=2 nowrap>요청<br/>수량</td>
					<td class=m_td rowspan=2 nowrap>실출고<br/>수량</td>
					<td class=m_td rowspan=2 nowrap>실입고<br/>수량</td>
					<td class=m_td rowspan=2 style='line-height:120%;' nowrap>출고일</td>
					<td class=m_td colspan=3 nowrap>이동전 / 이동후위치</td>
					<td class=m_td rowspan=2 style='line-height:120%;' nowrap>입고일</td>
					<td class=e_td rowspan=2 nowrap>처리상태</td>
				</tr>
				<tr align=center height=30>
					<td class=m_td nowrap>사업장</td>
					<td class=m_td nowrap>창고</td>
					<td class=m_td nowrap>보관장소</td>	
				</tr>
				";



$orderbyString = " order by wm.wm_apply_date desc, wm.wm_ix desc ";

/*
		left join inventory_goods_unit gu on wmd.gid=gu.gid and wmd.unit=gu.unit
		left join inventory_goods g on gu.gid=g.gid

*/
$sql = "select data.*,
		(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.now_company_id   limit 1) as now_company_name,
		(select place_name as place_name from inventory_place_info pi where pi.pi_ix = data.now_pi_ix  limit 1) as now_place_name,
		(select section_name as section_name from inventory_place_section ps where ps.ps_ix = data.now_ps_ix   limit 1) as now_section_name,
		(select com_name as company_name from common_company_detail ccd where ccd.company_id = data.move_company_id   limit 1) as move_company_name
		from 
		(select wm.*, wmd_ix,wmd.gid,wmd.gname,wmd.unit,wmd.standard,apply_cnt,delivery_cnt,entering_cnt, pi.place_name as move_place_name, ps.section_name as move_section_name
		from inventory_warehouse_move wm 
		inner join inventory_warehouse_move_detail wmd on (wm.wm_ix = wmd.wm_ix)
		left join  inventory_place_info pi on wm.move_pi_ix = pi.pi_ix
		left join  inventory_place_section ps on wm.move_ps_ix = ps.ps_ix
		$where    
		 $orderbyString 
		LIMIT $start, $max
		 ) data
		 group by wm_ix,wmd_ix
		 
		 ";//select  wm.*, pi.place_name as move_place_name, ps.section_name as move_section_name 이 쿼리에 distinct 가 arounz에는 추가되어있었음 kbk 13/08/08

//echo nl2br($sql);

$db->query($sql);
$goods_infos = $db->fetchall();

if(count($goods_infos) == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=20 align=center> 등록된 창고이동 목록이 없습니다. <!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."--></td></tr>";

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

//".PrintStockByOptionToOrder($goods_infos[$i])."
	$innerview .= "<tr bgcolor='#ffffff' align=center>";
	
	if($b_wm_ix !=$goods_infos[$i][wm_ix]){
		$rowspan_cnt =0;
		foreach($goods_infos as $info){
			if($goods_infos[$i][wm_ix] == $info[wm_ix]){
				$rowspan_cnt ++;
			}
		}

		$innerview .= "
						<td class='list_box_td list_bg_gray' align=center rowspan='".($rowspan_cnt * 2)."' ><input type=checkbox class=nonborder id='cpid' name='select_pid[]' value='".$goods_infos[$i][wm_ix]."'></td>
						<td class='list_box_td ' align=center rowspan='".($rowspan_cnt * 2)."'>".$goods_infos[$i][wm_ix]." <br/><img src='../images/".$admininfo["language"]."/btn_letter_request.gif' onclick=\"PoPWindow3('../inventory/warehouse_move.php?mmode=pop&wm_ix=".$goods_infos[$i][wm_ix]."',1100,700,'warehouse_move')\" style='cursor:pointer;' /></td>
						<td class='list_box_td ' align=center nowrap rowspan='".($rowspan_cnt * 2)."'> ".$goods_infos[$i][wm_apply_date]."</td>
						<td bgcolor=#ffffff rowspan='".($rowspan_cnt * 2)."'>".$goods_infos[$i][apply_charger_name]."</td>";
	}

	$innerview .= "
						<td bgcolor=#ffffff rowspan=2>".$goods_infos[$i][gid]."</td>
						<td class='list_box_td point' rowspan=2>
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
					<td bgcolor=#ffffff rowspan=2>".getUnit($goods_infos[$i][unit], "basic_unit","","text")."</td>
					<td bgcolor=#ffffff rowspan=2>".$goods_infos[$i][standard]."</td>
					<td bgcolor=#ffffff rowspan=2>".number_format($goods_infos[$i][apply_cnt])."</td>
					<td bgcolor=#ffffff rowspan=2>".number_format($goods_infos[$i][delivery_cnt])."</td>
					<td bgcolor=#ffffff rowspan=2>".number_format($goods_infos[$i][entering_cnt])."</td>
					
					<td bgcolor=#ffffff rowspan=2>".substr($goods_infos[$i][wm_delivery_date],0,4)."-".substr($goods_infos[$i][wm_delivery_date],4,2)."-".substr($goods_infos[$i][wm_delivery_date],6,2)."</td>
					<td bgcolor=#ffffff height='35'>".$goods_infos[$i][now_company_name]."</td>
					<td bgcolor=#ffffff >".$goods_infos[$i][now_place_name]."</td>
					<td bgcolor=#ffffff >".$goods_infos[$i][now_section_name]."</td>
					<td bgcolor=#ffffff rowspan=2 nowrap>".substr($goods_infos[$i][wm_entering_date],0,4)."-".substr($goods_infos[$i][wm_entering_date],4,2)."-".substr($goods_infos[$i][wm_entering_date],6,2)."</td>
					
					
					<td bgcolor=#ffffff rowspan=2 nowrap>".getInventoryStatus($goods_infos[$i][status],"","","text")." </td>";

$innerview .= "
				</tr>
				<tr>
					<td bgcolor=#ffffff height='35'>".$goods_infos[$i][move_company_name]."</td>
					<td bgcolor=#ffffff >".$goods_infos[$i][move_place_name]."</td>
					<td bgcolor=#ffffff >".$goods_infos[$i][move_section_name]."</td>
				</tr>";

		$b_wm_ix = $goods_infos[$i][wm_ix];
	}
}
	$innerview .= "</table>
				<table width='100%'>
				<tr height=30>
					<td width=210>

					</td>
					<td align=right>".$str_page_bar."</td>
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


$Script = "<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<script type='text/javascript' src='../js/ui/ui.core.js'></script>
<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script>
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
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
			$.cookie('inventory_move_max_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
			document.location.reload();
		});
	});

	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var depth = sel.getAttribute('depth');

		if(sel.selectedIndex!=0) {
			window.frames['act'].location.href = 'inventory_category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		}
	}
	</script>";
	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->strLeftMenu = inventory_menu();
		$P->addScript = $Script;
		$P->Navigation = "재고관리 > 입출고관리 > $title_str ";
		$P->NaviTitle = "$title_str ";
		$P->strContents = $Contents;
		$P->jquery_use = false;

		$P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->strLeftMenu = inventory_menu();
		$P->addScript = $Script;
		$P->Navigation = "재고관리 > 입출고관리 > $title_str ";
		$P->title = "$title_str";
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