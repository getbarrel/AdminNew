<?
///////////////////////////////////////////////////////////////////
//
// 제목 : 전시관리 > 결과분석 - 이현우(2013-05-16)
//
///////////////////////////////////////////////////////////////////
include("../class/layout.class");
include_once("../store/md.lib.php");
include_once("../display/display.lib.php");

$db = new Database;
$db2 = new Database;

$display_div = $_GET["display_div"];
$sel_listcnt = $_GET["sel_listcnt"];
if (!$display_div) $display_div = 1;

if (!$sel_listcnt) $sel_listcnt = 20;

// 전시목록 조회
$sql = "SELECT * FROM shop_display where display_div='$display_div' LIMIT ".$sel_listcnt;
$db->query($sql);
$total = $db->total;

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

 

$Script = "<script language='javascript'>
function eventDelete(cmg_ix){
	if(confirm(language_data['category_main.list.php']['A'][language]))
	{//'해당 프로모션  정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'
		window.frames['act'].location.href= 'category_main_goods.act.php?act=delete&cmg_ix='+cmg_ix;//kbk
		//document.getElementById('act').src= 'category_main_goods.act.php?act=delete&cmg_ix='+cmg_ix;
	}


}

 function loadCategory(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;

	var depth = $('select[name='+sel.name+']').attr('depth');
	//alert('category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}

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
				//}else{
					//$('#end_datepicker').datepicker('setDate','+0d');
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



function select_date(FromDate,ToDate,dType) {
	var frm = document.serchform;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}


// 전시 등폭폼
function FnDisplayWrite(){
	location.href = 'display_write.php?display_div=".$display_div."';
}

// 선택한 전시 일괄삭제
function FnDeleteAll(){
}



// 전시수정
function FnDisplayMod(display_ix){
	location.href = 'display_write.php?display_div=".$display_div."&display_ix='+display_ix;
}
</script>";

$tabmenu_class1 = (strstr($_SERVER["SCRIPT_FILENAME"],"display_list.php")) ? " class='on' " : "";
$tabmenu_class2 = (strstr($_SERVER["SCRIPT_FILENAME"],"display_report.php")) ? " class='on' " : "";

// 전시검색폼
$mstring ="
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<form name=serchform >
	<input type='hidden' name='cid2' value=''>
	<input type='hidden' name='depth' value=''>		
		<tr>
			<td align='left' style='padding-bottom:10px;'>								
				<div class='tab' style='width:100%;height:38px;margin:0px;'>
				<table width='100%' class='s_org_tab'>
				<tr>							
					<td class='tab' width='75'>
						<table id='tab_1' ".$tabmenu_class1.">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='display_list.php?display_div=".$display_div."'\">전시관리</td>
							<th class='box_03'></th>							
						</tr>
						</table>
					</td>
					<td class='tab' width='75'>
						<table id='tab_2' ".$tabmenu_class2.">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='display_report.php?display_div=".$display_div."'\">결과분석</td>
							<th class='box_03'></th>				
						</tr>
						</table>
					</td>							
					<td align='right'>
						<a href='category_main_div.php'><input type='button' value='상품전시추가(이미지제작 필요)' onclick='FnDisplayWrite()'></a>&nbsp;
						<a href='category_main_div.php'><input type='button' value='분류수정' onclick='location.href=\"display_div.php?display_div=".$display_div."\"'></a>
					</td>
				</tr>
				</table>										
				</div>					
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
					<col width=20%>
					<col width=80%>
					<tr>
						<td class='search_box_title' >  검색어</td>
						<td class='search_box_item'><input type='text' class='textbox' name='srch_text' value='".$srch_text."'></td>
					</tr>
					<tr>
						<td class='search_box_title' >  전시분류</td>
						<td class='search_box_item'>".makeDisplayDivSelectBox($db2, $display_div, 'srch_div', $srch_div_ix, 0,  '1차분류', 'onChange="loadDivix(this.form,\'srch_div2\', this.value)"')."&nbsp;".makeDisplayDivSelectBox($db, $display_div, 'srch_div2', $srch_div_ix2, 1,  $display_name = '2차분류', $onchange='')."
						</td>
					</tr>
					<tr>
						<td class='search_box_title' >  진행상태</td>
						<td class='search_box_item'>".makeRadioTag($arr_display_status, "srch_status")."</td>
					</tr>
					<tr>
						<td class='search_box_title' >  전시유무</td>
						<td class='search_box_item'>".makeRadioTag($arr_display_disp, "srch_disp")."</td>
					</tr>
					";
if ($display_div == "2"){
	$mstring.="
					<tr>
						<td class='search_box_title' >  카테고리선택</td>
						<td class='search_box_item'>
							<table border=0 cellpadding=0 cellspacing=0>
								<tr>
									<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
									<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
									<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
									<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid2)."</td>
								</tr>
							</table>
						</td>
					</tr>
					";
}
$mstring.="
					<tr>
						<td class='search_box_title'>기간</td>
						  <td class='search_box_item'  colspan=3>
							<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
								<col width=35>
								<col width=35>
								<col width=20>
								<col width=35>
								<col width=35>
								<col width=*>
								<tr>
									<td nowrap>
									<input type='text' name='cmg_use_sdate' class='textbox' value='".$cmg_use_sdate."' style='height:18px;width:70px;text-align:center;' id='start_datepicker'>
									</td>
									<td nowrap>
									<SELECT name=FromHH>";
													for($i=0;$i < 24;$i++){
									$mstring.= "<option value='".$i."' ".($FromHH == $i ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 시
													<SELECT name=FromMI>";
													for($i=0;$i < 60;$i++){
									$mstring.= "<option value='".$i."' ".($FromMI == $i ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 분
																	</td>
																	<td align=center> ~ </td>
																	<td nowrap>
																	<input type='text' name='cmg_use_edate' class='textbox' value='".$cmg_use_edate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'>
																	</td>
																	<td nowrap>
																	<SELECT name=ToHH>";
													for($i=0;$i < 24;$i++){
									$mstring.= "<option value='".$i."' ".($ToHH == $i ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 시
													<SELECT name=ToMI>";
													for($i=0;$i < 60;$i++){
									$mstring.= "<option value='".$i."' ".($ToMI == $i ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 분
									</td>
									<td style='padding:0px 10px'>
										<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
										<a href=\"javascript:select_date('$today','$voneweeklater',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
										<a href=\"javascript:select_date('$today','$v15later',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
										<a href=\"javascript:select_date('$today','$vonemonthlater',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
										<a href=\"javascript:select_date('$today','$v2monthlater',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
										<a href=\"javascript:select_date('$today','$v3monthlater',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
									</td>
								</tr>
							</table>
						  </td>
					</tr>
					<tr>
						<td class='search_box_title' >  담당 MD</td>
						<td class='input_box_item'>
						 ".makeMDSelectBox($db2, 'srch_md',$md_name)."
						</td>
					</tr>
					<tr>
						<td class='search_box_title' >  목록수</td>
						<td class='input_box_item'>
						<select name='sel_listcnt'>
							<option value='20'>20</option>
							<option value='30'>30</option>
							<option value='40'>40</option>
							<option value='50'>50</option>	
						</select>
						</td>
				</table>
			</td>
		</tr>
		<tr>
			<td style='padding:10px 0px;' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
		</tr>
		</form>";
$mstring .="</table>";

// 전시목록
$mstring.="
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<col width=100>
		<col width=*>		
		<col width=100>
		<tr height='30'>
			<td><b>전체 : ".$db->total."개</b></td>
			<td></td>
			<td></td>
		</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
		<col width=35>
		<col width=50>
		<col width=100>
		<col width=150>
		<col width=100>
		<col width=100>
		<col width=100>
		<col width=100>
		<col width=100>
		<col width=100>
		<col width=100>
		<tr height='28' bgcolor='#ffffff'>
			<td align='center' class=s_td rowspan='2'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		    <td align='center' class='m_td' rowspan='2'><font color='#000000'><b>번호</b></font></td>
			<td align='center' class='m_td' rowspan='2'><font color='#000000'><b>전시분류</b></font></td>
			<td align='center' class='m_td' rowspan='2'><font color='#000000'><b>전시명</b></font></td>
			<td align='center' class='m_td' rowspan='2'><font color='#000000'><b>담당MD</b></font></td>			
			<td align='center' class='m_td' rowspan='2'><font color='#000000'><b>노출기간</b></font></td>
			<td align='center' class='m_td' rowspan='2'><font color='#000000'><b>진행상태</b></font></td>
			<td align='center' class='m_td' rowspan='2'><font color='#000000'><b>등록일</b></font></td>
			<td align='center' class='m_td' colspan='8'><font color='#000000'><b>결과분석</b></font></td>
			<td align='center' class='m_td' rowspan='2'><font color='#000000'><b>관리</b></font></td>
		</tr>
		<tr height='28'>		
			<td align='center' class='m_td'><font color='#000000'><b>클릭수</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>구매수</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>구매전환율</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>구매확정율</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>매출액</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>구매확정<br>매출액</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>목표매출액</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>목표달성율</b></font></td>									

		</tr>
		";
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;
		$display_ix			= $db->dt[display_ix];
		$list_display_div	= $db->dt[display_div];
		$disp_title			= $db->dt[disp_title];
		$md_id				= $db->dt[md_id];				
		$regdate			= $db->dt[regdate];		
		$sdate				= $db->dt[sdate];
		$edate				= $db->dt[edate];

		$click_cnt				= $db->dt[click_cnt];
		$order_cnt				= $db->dt[order_cnt];
		$order_change_per	= $db->dt[order_change_per];
		$order_fix_cnt			= $db->dt[order_fix_cnt];
		$order_fix_per			= $db->dt[order_fix_per];
		$order_fix_amount	= $db->dt[order_fix_amount];
		$goal_amount			= $db->dt[goal_amount];
		$goal_per				= $db->dt[goal_per];
		

		$show_date = getDateList($sdate)."~<BR>".getDateList($edate);
		$mod_btn		= "상세확인";

		$mstring.="
		<tr align='center' height='28' bgcolor='#ffffff'>
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$display_ix."'></td>
			<td class='list_box_td'>".$no."</td>
			<td class='list_box_td'>".getDisplayDivInfo($db,$list_display_div)."</td>
			<td class='list_box_td'>".$disp_title."</td>
			<td class='list_box_td'>".getNameForCode($db, $md_id)."</td>			
			<td class='list_box_td'>".$show_date."</td>
			<td class='list_box_td'>".getStatusForDate($sdate,$edate)."</td>
			<td class='list_box_td'>".$regdate."</td>	
			<td class='list_box_td'>".$click_cnt."</td>
			<td class='list_box_td'>".$order_cnt."</td>
			<td class='list_box_td'>".$order_change_per."</td>
			<td class='list_box_td'>".$order_fix_cnt."</td>
			<td class='list_box_td'>".$order_fix_per."</td>
			<td class='list_box_td'>".$order_fix_amount."</td>
			<td class='list_box_td'>".$goal_amount."</td>
			<td class='list_box_td'>".$goal_per."</td>
			
			<td class='list_box_td'>".$mod_btn."</td>
		</tr>
		";
	}
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&display_div=$display_div&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");
$mstring.="
		</table>
		<table width=100%>
		<tr>
			<td><b>선택한 항목을 </b>인쇄</td>
		</tr>
		</table>
		<table width=100%>
		<tr>
			<td><div style='width:100%;text-align:right;padding:5px 0px;'>".$str_page_bar."</div></td>
		</tr>
		</table>
";
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>프로모션 상품 추가</b>를 원하시면 이벤트 추가버튼을 클릭해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기간이 만료된 프로모션 상품은 자동으로 노출이 종료됩니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');
$help_text = HelpBox($__arr_display_div[$display_div]." 전시관리", $help_text);

$Contents = $mstring.$help_text;
$Contents .= "<div style='height:120px;'></div>";

//echo $_SERVER["SCRIPT_FILENAME"];
$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션/전시 > 결과분석 (".$__arr_display_div[$display_div].")";
$P->title = "프로모션/전시 > 결과분석 (".$__arr_display_div[$display_div].")";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>