<?
include("../class/layout.class");

 
 
$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
if ($use_sdate == "1"){
	if(!$discount_use_sdate_start || !$discount_use_sdate_end){ 
		$discount_use_sdate_start = date("Y-m-d", $before10day);
		$discount_use_sdate_end = date("Y-m-d");
	}
}

if ($use_edate == "1"){
	if(!$discount_use_edate_start || !$discount_use_edate_end){ 
		$discount_use_edate_start = date("Y-m-d", $before10day);
		$discount_use_edate_end = date("Y-m-d");
	}
}

//print_r($_FREEGIFT_CONDITION["B"]);
if(!is_array($member_target)){
	$member_target = array();
}

$Script = "
<link rel='stylesheet' type='text/css' href='/admin/v3/css/jquery-ui.css' />
<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script language='javascript'>

$(function() {
	$(\"#discount_use_sdate_start\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#event_edate').val() != '' && $('#event_edate').val() <= dateText){
			$('#event_edate').val(dateText);
		}
	}

	});

	$(\"#discount_use_sdate_end\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});


	$(\"#discount_use_edate_start\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#event_edate').val() != '' && $('#event_edate').val() <= dateText){
			$('#event_edate').val(dateText);
		}
	}

	});

	$(\"#discount_use_edate_end\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});

function DiscountInfoDelete(dc_ix){
	if(confirm('해당 할인정보를 삭제하시겠습니까?')){
		window.frames['act'].location.href= '/admin/promotion/discount.act.php?act=delete&dc_ix='+dc_ix;
	}
}

function DiscountInfoProductDelete(dc_ix,pid){
	if(confirm('해당 상품을 삭제하시겠습니까?')){
		window.frames['act'].location.href= '/admin/promotion/discount.act.php?act=relation_delete&dc_ix='+dc_ix+'&pid='+pid;
	}
}

function setSelectDate(sdate,edate,date_type) {
	var frm = document.search_events;
	if(date_type == 1){
		$(\"#discount_use_sdate_start\").val(sdate);
		$(\"#discount_use_sdate_end\").val(edate);
	}else{
		$(\"#discount_use_edate_start\").val(sdate);
		$(\"#discount_use_edate_end\").val(edate);
	}
}


function searchUseSdate(frm){
	if(frm.use_sdate.checked){ 
		$('#discount_use_sdate_start').attr('disabled',false);
		$('#discount_use_sdate_end').attr('disabled',false);	 
	}else{
		$('#discount_use_sdate_start').attr('disabled',true);
		$('#discount_use_sdate_end').attr('disabled',true);
	}
}

function searchUseEdate(frm){
	if(frm.use_edate.checked){
		$('#discount_use_edate_start').attr('disabled',false);
		$('#discount_use_edate_end').attr('disabled',false);	 
	}else{
		$('#discount_use_edate_start').attr('disabled',true);
		$('#discount_use_edate_end').attr('disabled',true);
	}
}
 
 

</script>";
if($page_title == ""){
	$page_title = "기획할인등록";
}

$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("$page_title", "프로모션(마케팅) > 상품할인관리 > $page_title")."</td>
		</tr>
		 <tr>
			<td align='left' colspan=4 style='padding-bottom:15px;'>
				<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>
								<table id='tab_01' ".($list_type == '' ? "class='on'":"")." >
								<tr>
									<th class='box_01'></th>
									<td class='box_02'  ><a href='?list_type='>".($discount_type == "M" ?  "모바일" : "기획")."할인 목록</a></td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_02' ".($list_type == 'G' ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' ><a href='?list_type=G'>".($discount_type == "M" ?  "모바일" : "기획")."할인 그룹별 목록</a></td>
									<th class='box_03'></th>
								</tr>
								</table> 
							</td>
						</tr>
						</table>
					</div>
			</td>
		</tr>
		<tr>
			<td>";
		$mstring .= "
		<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:0px'>
				<form name=search_events method='get' ><!--SubmitX(this);'-->
				<input type='hidden' name='list_type' value='".$list_type."'>
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
					<col width='15%'>
					<col width='35%'>
					<col width='15%'>
					<col width='35%'>";
					if($_SESSION["admin_config"][front_multiview] == "Y"){
					$mstring .= "
					<tr>
						<td class='search_box_title' > 프론트 전시 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
					}
					$mstring .= "
					<tr height=30>
					  <td class='search_box_title'>검색조건 </td>
					  <td class='search_box_item'  style='padding-left:5px;' >
						  <select name=search_type>
								<option value='' ".CompareReturnValue("",$search_type,"selected")." style='vertical-align:middle;'>검색조건</option>
								<option value='discount_sale_title' ".CompareReturnValue("discount_sale_title",$search_type,"selected")." style='vertical-align:middle;'>기획할인명</option>";
					if($list_type == "G"){
					$mstring .= "<option value='pname'  ".CompareReturnValue("pname",$search_type,"selected")." >상품명</option>";	
					$mstring .= "<option value='pid'  ".CompareReturnValue("pid",$search_type,"selected")." >상품코드</option>";	
					}
					$mstring .= "	
						  </select>
						  <input type=text name='search_text' class='textbox' value='".$search_text."' style=' width:30%' >
					  </td>
					  <td class='search_box_title'>진행여부 </td>
					  <td class='search_box_item' >
					  <input type=radio name='is_ing' value='' id='is_ing_a'  ".CompareReturnValue("",$is_ing,"checked")."><label for='is_ing_a'>전체</label>
					  <input type=radio name='is_ing' value='1' id='is_ing_1'  ".CompareReturnValue("1",$is_ing,"checked")."><label for='is_ing_1'>진행중</label>
					  <input type=radio name='is_ing' value='0' id='is_ing_0' ".CompareReturnValue("0",$is_ing,"checked")."><label for='is_ing_0'>진행완료</label>
					  </td>
					</tr>
					<tr height=30>
					  <td class='search_box_title'>사용여부 </td>
					  <td class='search_box_item' >
					  <input type=radio name='is_use' value='' id='is_use_a'  ".CompareReturnValue("",$is_use,"checked")."><label for='is_use_a'>전체</label>
					  <input type=radio name='is_use' value='1' id='is_use_y'  ".CompareReturnValue("1",$is_use,"checked")."><label for='is_use_y'>사용</label>
					  <input type=radio name='is_use' value='0' id='is_use_n' ".CompareReturnValue("0",$is_use,"checked")."><label for='is_use_n'>미사용</label>
					  </td>
					  <td class='input_box_title' nowrap  >회원조건</td>
						<td class='search_box_item' >
							    <input type='checkbox' class='textbox' name='member_target[]' id='member_target_a' size=50 value='A' style='border:0px;' ".(in_array("A",$member_target) ? "checked":"")." /><label for='member_target_a'>전체</label>
								<input type='checkbox' class='textbox' name='member_target[]' id='member_target_g' size=50 value='G' style='border:0px;' ".(in_array("G",$member_target)  ? "checked":"")." /><label for='member_target_g'>회원 그룹별</label>
								<!--input type='checkbox' class='textbox' name='member_target[]' id='member_target_m' size=50 value='M' style='border:0px;' ".(in_array("M", $member_target)  ? "checked":"")." /><label for='member_target_m'>개별회원별</label--> 
						 </td>
					</tr>
					<tr bgcolor=#ffffff style='display:none;'>
						<td class='input_box_title'>요일선택 : </td>
						<td class='input_box_item' style='padding:0px;' ".($list_type == 'G' && false ?'':"colspan=3").">";

						foreach($week_name as $key => $value){
							$checked_str = "";
							
							if(is_array($week_no)){
								if(in_array($key, $week_no)){
									$checked_str = "checked";
								}
							}

							$mstring .= "<input type='checkbox' name='week_no[]' id='week_no_".$key."' value='".$key."' ".$checked_str." validation=false title='요일'> <label for='week_no_".$key."' >".$value."</label> ";
						}
							$mstring .= "  
						</td>";

		if($list_type == 'G' && false){
		$mstring .= "
						<td class='search_box_title'>셀러업체 </td>
						<td class='search_box_item' >
							".companyAuthList($company_id , "validation=false title='셀러업체' ")."
						</td>";
		}
		
		$mstring .= "</tr>";

		$mstring .= "
					<tr height=30>
						<td class='search_box_title'>
							<label for='use_sdate'>시작일자</label><input type='checkbox' name='use_sdate' id='use_sdate' value='1' ".CompareReturnValue("1",$use_sdate,"checked")." onclick='searchUseSdate(document.search_events);'>
						</td>
						<td class='search_box_item' colspan='3'>
							".search_date('discount_use_sdate_start','discount_use_sdate_end',$discount_use_sdate_start,$discount_use_sdate_end,'N','D')."	
						</td>
					</tr>
					<tr height=30>
					  <td class='search_box_title'><label for='use_edate'>종료일자</label><input type='checkbox' name='use_edate' id='use_edate' value='1' ".CompareReturnValue("1",$use_edate,"checked")." onclick='searchUseEdate(document.search_events);'></td>
					  <td class='search_box_item'  colspan='3'>
						".search_date('discount_use_edate_start','discount_use_edate_end',$discount_use_edate_start,$discount_use_edate_end,'N')."	
						<!--table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
							<tr>
								<TD width=5% nowrap><input type=text class='textbox' name='discount_use_edate_start' id='discount_use_edate_start' value='$discount_use_edate_start'  style='width:70px;text-align:center;' ".($use_edate ? "":"disabled")."></TD>
								<TD width=1% align=center> ~ </TD>
								<TD width=5% nowrap><input type=text class='textbox' name='discount_use_edate_end' id='discount_use_edate_end' value='$discount_use_edate_end'  style='width:70px;text-align:center;' ".($use_edate ? "":"disabled")."></TD>
								<TD width='*' > 
									<a href=\"javascript:setSelectDate('$today','$today',2);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
									<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',2);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
									<a href=\"javascript:setSelectDate('$voneweekago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
									<a href=\"javascript:setSelectDate('$v15ago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
									<a href=\"javascript:setSelectDate('$vonemonthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
									<a href=\"javascript:setSelectDate('$v2monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
									<a href=\"javascript:setSelectDate('$v3monthago','$today',2);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>

								</TD>
							</tr>
						</table-->
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
		</table>";
		$mstring .= "
			</td>
		</tr>
		<tr >
			<td style='padding:10px 0px;' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
		</tr>
		</form>
		<tr>
			<td>
			".PrintSearchTextList()."
			</td>
		</tr>
		";
$mstring .="</table>";

//$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>$page_title</b></td></tr></table>", $help_text,220);

$Contents = $mstring.$help_text;


$P = new LayOut();
$P->addScript = "".$Script;
$P->OnloadFunction = "";

$P->title = "$page_title";
if($discount_type == "M"){
	$P->strLeftMenu = mshop_menu();
	$P->Navigation = "모바일샾 > 모바일상품할인관리 > $page_title";
}else{
	$P->strLeftMenu = promotion_menu();
	$P->Navigation = "프로모션(마케팅) > 상품할인관리 > $page_title";
}
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintSearchTextList(){
	global $db, $slave_db, $page, $nset, $search_type;
	global $auth_delete_msg, $auth_excel_msg, $admininfo;
	global $week_name, $week_no, $member_target, $discount_type, $page_title;
	global $list_type,$_GET;


	if($discount_type == "SP"){
		$where = " where discount_type = 'SP' ";
	}elseif($discount_type == "M"){
		$where = " where discount_type = 'M' ";
	}else{
		$where = " where discount_type = 'GP' ";
	}
	
	$member_target = $_GET["member_target"];
	if(is_array($member_target)){
		for($i=0;$i < count($member_target);$i++){
			if($member_target[$i] != ""){
				if($member_target_str == ""){
					$member_target_str .= "'".$member_target[$i]."'";
				}else{
					$member_target_str .= ",'".$member_target[$i]."' ";
				}
			}
		}

		if($member_target_str != ""){
			$where .= " AND dc.member_target in (".$member_target_str.") ";
		}
	}else{
		if($member_target){
			$where .= " AND dc.member_target = '".$member_target."' ";
		}else{
			$member_target = array();
		}
	}
/*
	if($_GET["member_target"] != ""){
		$where .= " and dc.member_target =  '".$_GET["member_target"]."' ";
	}
*/
	if(is_array($week_no)){
		foreach($week_no as $key => $value){
			if($week_no_where){
				$week_no_where .= " or dc.week_no_".($value)." =  '1' ";	
			}else{
				$week_no_where .= "dc.week_no_".($value)." =  '1' ";	
			}
		}
		if($week_no_where){
			$where .= " and (".$week_no_where.") "; 
		}
	}


	if($_GET["is_ing"] != ""){
		$having = " having is_ing =  '".$_GET["is_ing"]."' ";
	}

	if($_GET["is_use"] != ""){
		$where .= " and dc.is_use =  '".$_GET["is_use"]."' ";
	}

	if($_GET["search_text"] != "" && $_GET["search_type"] != ""){
		$where .= " and ".$_GET["search_type"]." LIKE  '%".$_GET["search_text"]."%' ";
	}

	if($_GET["company_id"] != ""){		//셀러업체검색
		$where .= " and p.admin =  '".$_GET["company_id"]."' ";
	}

	if($_GET['mall_ix']){
        $where .= " and dc.mall_ix =  '".$_GET["mall_ix"]."' ";
    }
 
	if($_GET["discount_use_sdate_start"] != "" && $_GET["discount_use_sdate_end"] != ""){
//		$unix_timestamp_start_sdate = mktime(0,0,0,substr($_GET["discount_use_sdate_start"],4,2),substr($_GET["discount_use_sdate_start"],6,2),substr($_GET["discount_use_sdate_start"],0,4));
//		$unix_timestamp_start_edate = mktime(23,59,59,substr($_GET["discount_use_sdate_end"],4,2),substr($_GET["discount_use_sdate_end"],6,2),substr($_GET["discount_use_sdate_end"],0,4));
        $unix_timestamp_start_sdate = strtotime($_GET["discount_use_sdate_start"]." 00:00:00");
        $unix_timestamp_start_edate = strtotime($_GET["discount_use_sdate_end"]." 23:59:59");
		$where .= " and  discount_use_sdate between  ".$unix_timestamp_start_sdate." and ".$unix_timestamp_start_edate." ";
	}
 

	if($_GET["discount_use_edate_start"] != "" && $_GET["discount_use_edate_end"] != ""){
//		$unix_timestamp_end_sdate = mktime(0,0,0,substr($_GET["discount_use_edate_start"],4,2),substr($_GET["discount_use_edate_start"],6,2),substr($_GET["discount_use_edate_start"],0,4));
//		$unix_timestamp_end_edate = mktime(0,0,0,substr($_GET["discount_use_edate_end"],4,2),substr($_GET["discount_use_edate_end"],6,2),substr($_GET["discount_use_edate_end"],0,4));
        $unix_timestamp_end_sdate = strtotime($_GET["discount_use_edate_start"]." 00:00:00");
        $unix_timestamp_end_edate = strtotime($_GET["discount_use_edate_end"]." 23:59:59");
		$where .= " and  discount_use_edate between  '".$unix_timestamp_end_sdate."' and '".$unix_timestamp_end_edate."' ";
	}

	if($list_type == "G"){
	$sql = "select dc.*  , case when discount_use_edate >= UNIX_TIMESTAMP(NOW()) then 1 else 0 end as is_ing    
				from shop_discount dc 
				right join shop_discount_product_group dpg on dc.dc_ix = dpg.dc_ix 
				left join shop_discount_product_relation dpr on dc.dc_ix = dpr.dc_ix 
				right join shop_product p on dpr.pid = p.id 
				$where group by dc.dc_ix,dpr.group_code, p.id $having";

	}else{
	$sql = "select dc.*  , case when discount_use_edate >= UNIX_TIMESTAMP(NOW()) then 1 else 0 end as is_ing    from shop_discount dc $where $having";
	}


	//echo nl2br($sql);
	$slave_db->query($sql);
	$total = $slave_db->total;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


		if($list_type == "G"){
		$sql = "select SQL_NO_CACHE dc.* , dpg.dpg_ix, dpg.group_name, p.pname, p.id, p.listprice, p.sellprice, dpg.commission as plan_commission, dpg.sale_rate, dpg.discount_sale_type, dpg.round_position, dpg.round_type, case when discount_use_edate >= UNIX_TIMESTAMP(NOW()) then 1 else 0 end as is_ing    , p.one_commission, p.wholesale_commission, p.commission, csd.econtract_commission, ccd.com_name
				from shop_discount dc 
				right join shop_discount_product_group dpg on dc.dc_ix = dpg.dc_ix 
				left join shop_discount_product_relation dpr on dc.dc_ix = dpr.dc_ix  and dpg.group_code = dpr.group_code
				right join shop_product p on dpr.pid = p.id 
				left join common_company_detail ccd on p.admin = ccd.company_id 
				left join common_seller_delivery csd on p.admin = csd.company_id 
				$where 
				group by dc.dc_ix,dpg.dpg_ix, p.id
				$having
				order by dc.regdate desc , dpg.group_code asc
				".($_GET["mode"]=='excel'  ? "":"LIMIT $start, $max")." ";
		}else{
		$sql = "select SQL_NO_CACHE dc.* , case when discount_use_edate >= UNIX_TIMESTAMP(NOW()) then 1 else 0 end as is_ing   
				from shop_discount dc 
				$where $having
				order by dc.regdate desc 
				".($_GET["mode"]=='excel'  ? "":"LIMIT $start, $max")." ";
		}
		//echo nl2br($sql);
		$slave_db->query($sql);
		$discount_infos = $slave_db->fetchall();
 
if($_GET["mode"] == "excel"){
	
	ini_set('memory_limit','2048M');
	set_time_limit(9999999);

	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$discount_excel = new PHPExcel();

	// 속성 정의
	$discount_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("discount product List")
								 ->setSubject("discount product List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("discount product List");
 
	$discount_excel->getActiveSheet(0)->setCellValue('A' . 1, "번호");//iconv('UTF-8','EUC-KR',"번호")
	$discount_excel->getActiveSheet(0)->setCellValue('B' . 1, "상품시스템코드");
	$discount_excel->getActiveSheet(0)->setCellValue('C' . 1, "상품명");
	$discount_excel->getActiveSheet(0)->setCellValue('D' . 1, "기획할인명");
	$discount_excel->getActiveSheet(0)->setCellValue('E' . 1, "기획할인 할인그룹명");
	
	$discount_excel->getActiveSheet(0)->setCellValue('F' . 1, "판매가");
	$discount_excel->getActiveSheet(0)->setCellValue('G' . 1, "할인가");
	$discount_excel->getActiveSheet(0)->setCellValue('H' . 1, "기획할인가");
	$discount_excel->getActiveSheet(0)->setCellValue('I' . 1, "할인액(본사/셀러)");
	$discount_excel->getActiveSheet(0)->setCellValue('J' . 1, "할인율(본사/셀러)");

	$discount_excel->getActiveSheet(0)->setCellValue('K' . 1, "기획할인 수수료율(%)");
	$discount_excel->getActiveSheet(0)->setCellValue('L' . 1, "자릿수");
	$discount_excel->getActiveSheet(0)->setCellValue('M' . 1, "반올림");
	
	$discount_excel->getActiveSheet(0)->setCellValue('N' . 1, "기획할인 시작일");
	$discount_excel->getActiveSheet(0)->setCellValue('O' . 1, "기획할인 종료일");
	$discount_excel->getActiveSheet(0)->setCellValue('P' . 1, "시간대설정");
	$discount_excel->getActiveSheet(0)->setCellValue('Q' . 1, "적용요일");
	$discount_excel->getActiveSheet(0)->setCellValue('R' . 1, "사용여부");
	$discount_excel->getActiveSheet(0)->setCellValue('S' . 1, "개별수수료 사용유무");
	$discount_excel->getActiveSheet(0)->setCellValue('T' . 1, "도매수수료|소매수수료");
	$discount_excel->getActiveSheet(0)->setCellValue('U' . 1, "업체수수료율");
	$discount_excel->getActiveSheet(0)->setCellValue('V' . 1, "업체명");

	if($discount_type == "SP"){
		//$discount_excel->getActiveSheet(0)->setCellValue('M' . 1, "수수료");
	}
	

	$before_pid = "";
 
	for ($i = 0; $i < count($discount_infos); $i++)
	{
		$j="A";
		
		$discount_excel->getActiveSheet()->setCellValue('A' . ($i + 2), ($i + 1));
		$discount_excel->getActiveSheet()->setCellValue('B' . ($i + 2), $discount_infos[$i][id]);
		$discount_excel->getActiveSheet()->setCellValue('C' . ($i + 2), $discount_infos[$i][pname]);
		$discount_excel->getActiveSheet()->setCellValue('D' . ($i + 2), $discount_infos[$i][discount_sale_title]);
		$discount_excel->getActiveSheet()->setCellValue('E' . ($i + 2), $discount_infos[$i][group_name]);
		
		//CSV 에선 안먹힘 ㅜ
		//$discount_excel->getActiveSheet()->setCellValueExplicit('D' . ($i + 2), $discount_infos[$i][id], PHPExcel_Cell_DataType::TYPE_STRING);

		
		$discount_excel->getActiveSheet()->setCellValue('F' . ($i + 2), $discount_infos[$i][listprice]);
		$discount_excel->getActiveSheet()->setCellValue('G' . ($i + 2), $discount_infos[$i][sellprice]);
		$discount_excel->getActiveSheet()->setCellValue('H' . ($i + 2), ($discount_infos[$i][discount_sale_type] ==1 ? number_format(roundBetter($discount_infos[$i][sellprice] - $discount_infos[$i][sellprice]*$discount_infos[$i][sale_rate]/100,$discount_infos[$i][round_position],$discount_infos[$i][round_type])):number_format(roundBetter($discount_infos[$i][sellprice]-$discount_infos[$i][sale_rate],$discount_infos[$i][round_position],$discount_infos[$i][round_type]))));

		
		$discount_excel->getActiveSheet()->setCellValue('I' . ($i + 2), ($discount_infos[$i][discount_sale_type] ==2 ? $discount_infos[$i][sale_rate]."원":""));
		$discount_excel->getActiveSheet()->setCellValue('J' . ($i + 2), ($discount_infos[$i][discount_sale_type] ==1 ? $discount_infos[$i][sale_rate]."%":""));

		$discount_excel->getActiveSheet()->setCellValue('K' . ($i + 2), $discount_infos[$i][plan_commission]);
		

		if($discount_infos[$i][round_position] == 1){
			$round_position = "일자리";
		}else if($discount_infos[$i][round_position] == 2){
			$round_position = "십자리";
		}else if($discount_infos[$i][round_position] == 3){
			$round_position = "백자리";
		}else{
			$round_position = "";
		}
		$discount_excel->getActiveSheet()->setCellValue('L' . ($i + 2), $round_position);

		if($discount_infos[$i][round_type] == 1){
			$round_type = "반올림";
		}else if($discount_infos[$i][round_type] == 2){
			$round_type = "반내림";
		}else if($discount_infos[$i][round_type] == 3){
			$round_type = "내림";
		}else if($discount_infos[$i][round_type] == 4){
			$round_type = "올림";
		}else{
			$round_type = "";
		}
		$discount_excel->getActiveSheet()->setCellValue('M' . ($i + 2), $round_type);
		

		$discount_excel->getActiveSheet()->setCellValue('N' . ($i + 2), date("Y-m-d",$discount_infos[$i][discount_use_sdate]));
		$discount_excel->getActiveSheet()->setCellValue('O' . ($i + 2), date("Y-m-d",$discount_infos[$i][discount_use_edate]));
		$discount_excel->getActiveSheet()->setCellValue('P' . ($i + 2), ($discount_infos[$i][use_time] == 1 ? "Y":"N"));

		if($discount_infos[$i][week_no_1] == 1){
			$week_str .= "월";
		}
		if($discount_infos[$i][week_no_2] == 1){
			$week_str .= "화";
		}
		if($discount_infos[$i][week_no_3] == 1){
			$week_str .= "수";
		}
		if($discount_infos[$i][week_no_4] == 1){
			$week_str .= "목";
		}
		if($discount_infos[$i][week_no_5] == 1){
			$week_str .= "금";
		}
		if($discount_infos[$i][week_no_6] == 1){
			$week_str .= "토";
		}
		if($discount_infos[$i][week_no_7] == 1){
			$week_str .= "일";
		}
		$discount_excel->getActiveSheet()->setCellValue('Q' . ($i + 2), $week_str);
		
		

		$discount_excel->getActiveSheet()->setCellValue('R' . ($i + 2), ($discount_infos[$i][is_use] == "1" ? "사용":"미사용"));
		$discount_excel->getActiveSheet()->setCellValue('S' . ($i + 2), ($discount_infos[$i][one_commission] == "Y" ? "Y":"N"));
		$discount_excel->getActiveSheet()->setCellValue('T' . ($i + 2), $discount_infos[$i][wholesale_commission]."|".$discount_infos[$i][commission]);
		$discount_excel->getActiveSheet()->setCellValue('U' . ($i + 2), $discount_infos[$i][econtract_commission]);
		$discount_excel->getActiveSheet()->setCellValue('V' . ($i + 2), $discount_infos[$i][com_name]);


		if($discount_type == "SP"){
			//$discount_excel->getActiveSheet()->setCellValue('M' . ($i + 2), $discount_infos[$i][commission]."%");
		}
		unset($week_str);
	}

	// 첫번째 시트 선택
	$discount_excel->setActiveSheetIndex(0);

	 
	$discount_excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$discount_excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$discount_excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$discount_excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
	$discount_excel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true); 
	$discount_excel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true); 
	

	if(is_excel_csv()){
		header('Content-Type: application/vnd.ms-excel;');//charset=euckr
		header('Content-Disposition: attachment;filename="plan_product_'.date("Ymd").'.csv"');
		header('Cache-Control: max-age=0');
		//setlocale(LC_CTYPE, 'ko_KR.eucKR');
		//header("Content-charset=euckr");
		//header("Content-Description: PHP5 Generated Data");
		$objWriter = PHPExcel_IOFactory::createWriter($discount_excel, 'CSV');
		$objWriter->setUseBOM(true);
	}else{
		header('Content-Type: application/vnd.ms-excel;');
		header('Content-Disposition: attachment;filename="plan_product_'.date("Ymd").'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($discount_excel, 'Excel5');
	}

	$objWriter->save('php://output');

	exit;
}


	
	if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
	}
	$str_page_bar = page_bar($total, $page, $max, $query_string,"");


	$mString = "
	<table width=100%>
		<tr>
			<td>
			<div style='padding:5px;'>기획할인 등록수 : ".number_format($total)." 개</div>
			</td>
			<td align=right>";

			if($list_type == "G"){
			$mString .= "<a href='?".$_SERVER["QUERY_STRING"]."&mode=excel'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
			}
			$mString .= "
			</td>
		</tr>
	</table>
	<table cellpadding=0 cellspacing=0 border=0 width=100%  class='list_table_box' style='table-layout:fixed;'>";

	if($list_type == "G"){
	$mString .= "
		<col width='5%'>
		".($_SESSION["admin_config"][front_multiview] == "Y" ? "<col width=10%>":"")."
		<col width='*'>
		<col width='30%'> 
		<col width='6%'>
		<col width='6%'>
		<col width='13%'>
		<col width='6%'>
		<col width='7%'>	
		
		<col width='8%'>
		<tr align=center bgcolor=#efefef height='35'>
			<td class=s_td >번호</td>
			".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td'  > 프론트전시</td>":"")."
			<td class=m_td >기획 할인명 / 할인 그룹명</td>
			<td class=m_td >상품명<!--노출요일--></td>
			<td class='m_td'>".($discount_type == "SP" ? "<b>수수료<br>할인율</b>":"할인율")."</td>
			<td class=m_td >회원조건</td>  
			<td class=m_td>행사기간</td>
			<td class='m_td small'><b>사용여부</b></td>
			<td class=m_td>진행여부</td>
			<td class=e_td>관리</td>
			</tr>";
	}else{
	$mString .= "
		<col width='5%'>
		".($_SESSION["admin_config"][front_multiview] == "Y" ? "<col width=10%>":"")."
		<col width='*'>
		<!--<col width='16%'>-->
		<col width='7%'>
		<col width='15%'>
		<col width='7%'>
		<col width='7%'>
		<col width='7%'>
		<col width='11%'>
		<col width='9%'>
		<tr align=center bgcolor=#efefef height='30'>
			<td class=s_td >번호</td>
			".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td'  > 프론트전시</td>":"")."
			<td class=m_td >기획 할인명</td> 
			<!--<td class=m_td >노출요일</td>-->
			<td class=m_td >회원조건</td>  
			<td class=m_td>행사기간</td>
			<td class=m_td>사용여부</td>
			<td class=m_td>진행여부</td>

			<td class=m_td>상품수량</td>
			<td class=m_td>등록일자</td>
			<td class=e_td>관리</td>
			</tr>";
	}

	if ($total == 0){

	    if($list_type =='G') {
            $mString .= "<tr bgcolor=#ffffff height=70><td colspan=10 align=center>등록(검색)한 " . $page_title . " 이 존재 하지 않습니다.</td></tr>";
        }else{
            $mString .= "<tr bgcolor=#ffffff height=70><td colspan=11 align=center>등록(검색)한 " . $page_title . " 이 존재 하지 않습니다.</td></tr>";
        }
		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		$mString .= "<tr bgcolor=#ffffff ><td colspan=5 align=right style='padding:10px 0px;'>";
		if($discount_type == "SP"){
			$mString .= "<a href='special_discount.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a>";
		}elseif($discount_type == "M"){
			$mString .= "<a href='mobile_discount.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a>";
		}else{
			$mString .= "<a href='discount.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a>";
		}

		$mString .= "</td></tr>";

	}else{
		

		for($i=0;$i < count($discount_infos);$i++){

			$no = $total - ($page - 1) * $max - $i;

			if($discount_infos[$i][member_target] == "A"){
				$member_target_str = "전체";
			}else if($discount_infos[$i][member_target] == "G"){
				$member_target_str = "그룹";
			}else if($discount_infos[$i][member_target] == "M"){
				$member_target_str = "회원";
			}else{

			}

			//수동등록시 상품총수량 2014-08-25 이학봉
			$sql = "select dpr_ix from shop_discount_product_relation where dc_ix = '".$discount_infos[$i][dc_ix]."'";
			$slave_db->query($sql);
			$slave_db->fetch();
			$product_relation_total = $slave_db->total;

			//자동등록시 카테고리별 총 수량
			$sql = "select * from shop_discount_display_relation where dc_ix = '".$discount_infos[$i][dc_ix]."'";
			$slave_db->query($sql);
			$display_array = $slave_db->fetchall();
			
			if(count($display_array) > 0){
				for($k =0;$k<count($display_array);$k++){

					if($display_array[$k][relation_type] == 'C'){
						$dicount_cid = $display_array[$k][r_ix];
						$sql = "select count(pid) as total from shop_product_relation where cid = '".$dicount_cid."' ";
						$slave_db->query($sql);
						$slave_db->fetch();
						$display_total = $slave_db->dt[total];

						$display_relation_total += $display_total;
					}else if($display_array[$k][relation_type] == 'B'){
						$brand_code = $display_array[$k][r_ix];
						$sql = "select count(id) as total from shop_product where brand = '".$brand_code."'";
						$slave_db->query($sql);
						$slave_db->fetch();
						$display_total = $slave_db->dt[total];

						$display_relation_total += $display_total;

					}else if($display_array[$k][relation_type] == 'S'){

						$seller_company_id = $display_array[$k][r_ix];
						$sql = "select count(id) as total from shop_product where admin = '".$seller_company_id."'";
						$slave_db->query($sql);
						$slave_db->fetch();
						$display_total = $slave_db->dt[total];

						$display_relation_total += $display_total;
					
					}
				}
			}
			

			$week_str = "";
			//print_r($discount_infos[$i]);
			 foreach($week_name as $key => $value){
				//exit;  
				/*
				if(is_array($week_no)){
					if($week_str){
						$week_str .= " <span style='color:silver;'>|</span> ";
					}
					if(in_array($discount_infos[$i]["week_no_".$key], $week_no)){
						$week_str .= "<b>".$value."</b>";
					}else{
						$week_str .= "<span style='color:silver;'>".$value."</span>";
					}
				}else{
					*/
					if($week_str){
						$week_str .= " <span style='color:silver;'>|</span> ";
					}
					//echo $key."<br>";
					if($discount_infos[$i]["week_no_".$key]){
						
						$week_str .= "<b>".$value."</b>";
					}else{
						$week_str .= "<span style='color:silver;'>".$value."</span>";
					}
				//}
				   
				
			  }

			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td class='list_box_td list_bg_gray'>".$no."</td>";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$mString .= "
		    <td class='list_box_td'  >".GetDisplayDivision($discount_infos[$i][mall_ix], "text")."</td>";
}
	$mString .= "
			";
		if($list_type == "G"){
		$mString .= "
			<td class='list_box_td' style='text-align:left;padding:10px;line-height:150%;vertical-align:top;'><b>".$discount_infos[$i][discount_sale_title]."</b><br>".$discount_infos[$i][group_name]."</td>
			<td class='list_box_td point' style='overflow:hidden;text-align:left;line-height:150%;padding:5px;' nowrap>
			<table>
				<tr>
					<td>	<img src='".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $discount_infos[$i][id], "s", $discount_infos[$i])."' width=50 height=50 style='margin:5px;'></td>
					<td title='".$discount_infos[$i][pname]."' >
					".$discount_infos[$i][pname]."<br>
					판매가 : ".number_format($discount_infos[$i][listprice])." 원 / 할인가 : ".number_format($discount_infos[$i][sellprice])." 원<br>
					기획할인 : ".($discount_infos[$i][discount_sale_type] ==1 ? number_format(roundBetter($discount_infos[$i][sellprice] - $discount_infos[$i][sellprice]*$discount_infos[$i][sale_rate]/100,$discount_infos[$i][round_position],$discount_infos[$i][round_type])):number_format(roundBetter($discount_infos[$i][sellprice]-$discount_infos[$i][sale_rate],$discount_infos[$i][round_position],$discount_infos[$i][round_type])))."원<br>
					</td>
				</tr>
			</table>
			</td>
			<td class='list_box_td ' style='overflow:hidden;' nowrap>".($discount_type == "SP" ? "".$discount_infos[$i][plan_commission]."% /":"")."".$discount_infos[$i][sale_rate]." ".($discount_infos[$i][discount_sale_type] ==1 ? "%":"원")." <br></td>";
		}else{
		$mString .= "		
			<td class='list_box_td' style='text-align:left;' >".$discount_infos[$i][discount_sale_title]."</td>
			<!--<td class='list_box_td ' nowrap>".$week_str."</td>-->";
		}
		$mString .= "		
			<td class='list_box_td '>".$member_target_str."</td>
			<td class='list_box_td '>".date("Y-m-d",$discount_infos[$i][discount_use_sdate])." ~ ".date("Y-m-d",$discount_infos[$i][discount_use_edate])."</td>
			<td class='list_box_td list_bg_gray'>".($discount_infos[$i][is_use] == "1" ? "사용":"미사용")."</td>
			<td class='list_box_td list_bg_gray'>".($discount_infos[$i][is_ing] == "1" ? "진행중":"진행완료")."</td>";

		if($list_type != "G"){
			$mString .= "
			<td class='list_box_td list_bg_gray'>".($product_relation_total + $display_relation_total)."</td>";
		}
		if($list_type != "G"){
		$mString .= "
			<td class='list_box_td ' style='line-height:140%;'>".$discount_infos[$i][regdate]."</td>";
		}
		$mString .= "
			<td class='list_box_td list_bg_gray' nowrap>";
			if($discount_type == "SP"){
				$mString .= "<a href='special_discount.php?dc_ix=".$discount_infos[$i][dc_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>";
			}elseif($discount_type == "M"){
				$mString .= "<a href='mobile_discount.php?dc_ix=".$discount_infos[$i][dc_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>";
			}else{
				$mString .= "<a href='discount.php?dc_ix=".$discount_infos[$i][dc_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>";
			}
			

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			
			if($list_type=="G"){
				$mString .= "<a href=\"JavaScript:DiscountInfoProductDelete('".$discount_infos[$i][dc_ix]."','".$discount_infos[$i][id]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;' alt='삭제' title='삭제'></a>";
			}else{
				$mString .= "<a href=\"JavaScript:DiscountInfoDelete('".$discount_infos[$i][dc_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;' alt='삭제' title='삭제'></a>";
			}
				

			}else{
			$mString .= "<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;'></a>";
			}
			$mString .= "
			</td>
			</tr>
			";
		
		$product_relation_total = '0';
		$display_relation_total = '0';

		}

		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		$mString .= "<tr bgcolor=#ffffff style='height:50px;'>
					<td colspan=3 align=left>".$str_page_bar."</td>
					<td colspan=2 align=right>";

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
			if($discount_type == "SP"){
				$mString .= "<a href='special_discount.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a>";
			}elseif($discount_type == "M"){
				$mString .= "<a href='mobile_discount.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a>";
			}else{
				$mString .= "<a href='discount.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a>";
			}
		
		}

		$mString .= "
					</td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}
 

?>