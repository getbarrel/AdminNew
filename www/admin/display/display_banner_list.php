<?
///////////////////////////////////////////////////////////////////
//
// 제목 : 프로모션/전시 > 배너 목록 - 이현우(2013-05-16)
//
///////////////////////////////////////////////////////////////////
include("../class/layout.class");
include_once("../store/md.lib.php");
include_once("../display/display.lib.php");


$db = new Database;
$db2 = new Database;

$banner_div = $_GET["banner_div"];
if (!$banner_div) $banner_div = 1;

// 배너목록 조회
if ($banner_div==1){			// 일반 배너
	$sql = "SELECT s.*, sv.depth, b.* FROM shop_bannerinfo s  INNER JOIN shop_display_banner b ON s.banner_ix = b.banner_ix AND b.banner_div = '1'  LEFT JOIN shop_banner_div sv ON b.div_ix = sv.div_ix ";
}else if ($banner_div==2){	// 플래쉬 배너
	$sql = "SELECT s.*, sv.depth, b.* FROM ".TBL_SHOP_MANAGE_FLASH." s INNER JOIN shop_display_banner b ON s.mf_ix = b.banner_ix AND b.banner_div = '2' LEFT JOIN shop_banner_div sv ON b.div_ix = sv.div_ix  ";
}else if ($banner_div==3){	// 슬라이드 배너
	$sql = "SELECT s.*, sv.depth, b.* FROM ".TBL_SHOP_MANAGE_FLASH." s INNER JOIN shop_display_banner b ON s.mf_ix = b.banner_ix AND b.banner_div = '3' LEFT JOIN shop_banner_div sv ON b.div_ix = sv.div_ix  ";
}
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

 

$Script = "
<script type='text/javascript' src='display.js'></script>
<script language='javascript'>
function FnBannerDel(banner_ix, banner_div){
	if(confirm(language_data['banner.php']['A'][language]))
	{
		//'해당 배너를  정말로 삭제하시겠습니까? '
		window.frames['act'].location.href= 'display_banner.act.php?act=delete&banner_ix='+banner_ix+'&banner_div='+banner_div;
		//document.getElementById('act').src= 'category_main_goods.act.php?act=delete&cmg_ix='+cmg_ix;
	}
}
function FnBannerFlashDel(banner_ix, banner_div){
	if(confirm(language_data['banner.php']['A'][language]))
	{
		//'해당 배너를  정말로 삭제하시겠습니까? '
		window.frames['act'].location.href= 'display_banner_flash.act.php?act=delete&mf_ix='+banner_ix+'&banner_div='+banner_div;
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


// 배너 등록폼
function FnDisplayWrite(banner_div){
	var url;
	if (banner_div==1){
		url = 'display_banner_write.php?banner_div=".$banner_div."';
	}else if (banner_div==2 || banner_div==3){
		url = 'display_banner_write_flash.php?banner_div=".$banner_div."';
	}
	location.href = url;
}

// 선택한 전시 일괄삭제
function FnDeleteAll(){
}



// 일반배너 수정
function FnBannerMod(banner_ix){
	location.href = 'display_banner_write.php?banner_div=".$banner_div."&banner_ix='+banner_ix;
}
// 플래시배너 수정
function FnFlashBannerMod(mf_ix){
	location.href = 'display_banner_write_flash.php?banner_div=".$banner_div."&mf_ix='+mf_ix;
}
 
</script>";

$tabmenu_class1 = (strstr($_SERVER["SCRIPT_FILENAME"],"display_banner_list.php")) ? " class='on' " : "";
$tabmenu_class2 = (strstr($_SERVER["SCRIPT_FILENAME"],"display_report.php")) ? " class='on' " : "";

// 전시검색폼
$mstring ="
<iframe name='iframe_act_thispage' id='iframe_act_thispage' width=600 height=300 frameborder=0 style='display:none'></iframe>
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
							<td class='box_02' onclick=\"document.location.href='display_banner_list.php?display_div=".$display_div."'\">전시관리</td>
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
						<a href='#;' onclick='FnDisplayWrite(".$banner_div.")'><img src='../images/".$admininfo["language"]."/btn_banner_write.gif' align=absmiddle ></a>&nbsp;
						<a href='#;'onclick='location.href=\"display_banner_div.php?banner_div=".$banner_div."\"'><img src='../images/".$admininfo["language"]."/btn_disp_write.gif' align=absmiddle ></a>&nbsp;</a>
					</td>
				</tr>
				</table>										
				</div>					
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
					<col width=10%>
					<col width=90%>
					<tr>
						<td class='search_box_title' >  검색어</td>
						<td class='search_box_item'><input type='text' class='textbox' name='srch_text' value='".$srch_text."'></td>
					</tr>
					";
if ($display_div == "2" || $display_div >= "6"){ 
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
						<td class='search_box_title' >  배너분류</td>
						<td class='search_box_item' >".makeBannerDivSelectBox($db,  'srch_div', $parent_div_ix, 0,  '1차분류', 'onChange="loadBannerDivix(this.form,\'div_ix\', this.value)"')."&nbsp;".makeBannerDivSelectBox($db, 'div_ix', $div_ix, 1,  $display_name = '2차분류', $onchange='')."</td>
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

$mstring.="
					<tr>
						<td class='search_box_title'>기간</td>
						  <td class='search_box_item'  colspan=1>
							<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
								<col width=35>
								<col width=35>
								<col width=20>
								<col width=35>
								<col width=35>
								<col width=*>
								<tr>
									<td  nowrap>
									<input type='text' name='cmg_use_sdate' class='textbox' value='".$cmg_use_sdate."' style='height:18px;width:70px;text-align:center;'  id='start_datepicker'> 일
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
									<input type='text' name='cmg_use_edate' class='textbox' value='".$cmg_use_edate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'> 일
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
		<col width=100>
		<col width=100>
		<tr height='30'>
			<td><b>전체 : ".$db->total."개</b></td>
			<td></td>
		</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
		<col width=35>
		<col width=40>
		<col width=130>
		<col width=100>
		<col width=170>
		<col width=60>
		<col width=60>
		<col width=100>
		<col width=70>
		<col width=100>
		<col width=100>
		<col width=100>

		<tr height='28' bgcolor='#ffffff'>
			<td align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		    <td align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>배너분류</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>배너명</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>치환함수</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>담당MD</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>전시유무</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>노출기간</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>진행상태</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>등록일</b></font></td>
			<td align='center' class='m_td'><font color='#000000'><b>수정일</b></font></td>						
			<td align='center' class='m_td'><font color='#000000'><b>관리</b></font></td>
		</tr>
		";
//echo $db->total;
if (!$db->total){
		$mstring .= "<tr bgcolor=#ffffff><td height=70 colspan=12 align=center>배너전시 내역이 존재 하지 않습니다.</td></tr>";
}else{
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;
		$banner_ix			= $db->dt[banner_ix];
		$disp_title			= $db->dt[banner_name];
		$md_id				= $db->dt[md_id];		
		$disp					= $db->dt[disp];
		$regdate			= $db->dt[regdate];
		$moddate			= $db->dt[mod_date];
		$sdate				= $db->dt[sdate];
		$edate				= $db->dt[edate];
		$cid					= $db->dt[cid];
		$depth				= $db->dt[depth];
		$list_div_ix			= $db->dt[div_ix];
		$banner_width	= $db->dt[banner_width];
		$banner_height	= $db->dt[banner_height];

		
		$disp_status = ($disp == "1") ? "전시" : "미전시";
		if ($sdate && $edate){
			$show_date = getDateList($sdate)."~<BR>".getDateList($edate);
		}

		if ($banner_div == 1){			
			$mod_btn		= "<a href='#;' onclick='FnBannerMod(".$banner_ix.")'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"\"></a>";
			$del_btn  = "<a href='#;' onclick='FnBannerDel(".$banner_ix.", ".$banner_div.")'><img src='../images/".$admininfo["language"]."/btc_del.gif' align=absmiddle ></a>";
		}else if ($banner_div == 2 || $banner_div==3){
			$banner_ix			= $db->dt[mf_ix];
			$disp_title			= $db->dt[mf_name];
			$mod_btn		= "<a href='#;' onclick='FnFlashBannerMod(".$banner_ix.")'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"\"></a>";
			$del_btn  = "<a href='#;' onclick='FnBannerFlashDel(".$banner_ix.", ".$banner_div.")'><img src='../images/".$admininfo["language"]."/btc_del.gif' align=absmiddle ></a>";
		}
		
		if ($cid){
			$gubun = getCategoryPath($cid, $depth);
		}

		if (!$banner_width) $banner_width = "가로크기";
		if (!$banner_height) $banner_height = "세로크기";

		if ($list_div_ix){
			$func_name = "{=getDisplayBanner(".$banner_div.", ".$banner_ix.", ".$banner_width.", ".$banner_height.", ".$list_div_ix.")}";
		}else{
			$func_name = "{=getDisplayBanner(".$banner_div.", ".$banner_ix.", ".$banner_width.", ".$banner_height.",'')}";
		}

		$mstring.="
		<tr align='center' height='28' bgcolor='#ffffff'>
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$banner_ix."'></td>
			<td class='list_box_td'>".$no."</td>
			<td class='list_box_td'>".getBannerDivInfo($db,$list_div_ix)."</td>
			<td class='list_box_td'>".$disp_title."</td>
			<td class='list_box_td'>".$func_name."</td>
			<td class='list_box_td'>".getNameForCode($db, $md_id)."</td>
			<td class='list_box_td'>".$disp_status."</td>			
			<td class='list_box_td'>".$show_date."</td>
			<td class='list_box_td'>".getStatusForDate($sdate,$edate)."</td>
			<td class='list_box_td'>".$regdate."</td>
			<td class='list_box_td'>".$moddate."</td>			
			<td class='list_box_td'>".$mod_btn."&nbsp;".$del_btn."</td>
		</tr>
		";
	}
}
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&display_div=$display_div&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");
$mstring.="
		</table>
		<table width=100%>
		<tr>
			<td><b>선택한 항목을 </b><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle onClick=\"FnDeleteAll()\"></td>
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
$help_text = HelpBox($__arr_display_div_banner[$banner_div]." 전시관리", $help_text);

$Contents = $mstring.$help_text;
$Contents .= "<div style='height:120px;'></div>";

//echo $_SERVER["SCRIPT_FILENAME"];
$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션/전시 > 배너관리 > ".$__arr_display_div_banner[$banner_div];
$P->title = "프로모션/전시 > 배너관리 > ".$__arr_display_div_banner[$banner_div];
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>