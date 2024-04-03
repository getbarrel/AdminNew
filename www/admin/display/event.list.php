<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;
if(!$agent_type){
	$agent_type = "W";
}

//2016-05-16 Hong 추가
$sql = "CREATE TABLE IF NOT EXISTS `shop_event_place_relation` (
  `epr_ix` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `event_ix` int(10) unsigned NOT NULL COMMENT '이벤트인덱스값',
  `place_ix` int(8) unsigned NOT NULL COMMENT '플레이스 아이디',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '수정시구분값',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`epr_ix`),
  KEY `event_ix` (`event_ix`),
  KEY `place_ix` (`place_ix`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='이벤트/기획전 관련 플레이스' AUTO_INCREMENT=1 ";
$db->query($sql);

$before10day = mktime(0, 0, 0, date("m")  , date("d")-10, date("Y"));
$after10day = mktime(0, 0, 0, date("m")  , date("d")+10, date("Y"));
if ($use_sdate == "1"){
	if(!$event_start_sdate || !$event_start_edate){ 
		$event_start_sdate = date("Ymd", $before10day);
		$event_start_edate = date("Ymd");	
	}
}
if ($use_edate == "1"){
	if(!$event_end_sdate || !$event_end_edate){ 
		$event_end_sdate = date("Ymd");
		$event_end_edate = date("Ymd", $after10day);	
	}
}
$Script = "<script language='javascript'>

$(function() {
	$(\"#event_start_sdate\").datepicker({
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

	$(\"#event_start_edate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});


	$(\"#event_end_sdate\").datepicker({
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

	$(\"#event_end_edate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});

function eventDelete(event_ix){
	if(confirm(language_data['event.list.php']['A'][language]))
	{//'해당 이벤트를 정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'
		window.frames['act'].location.href= '/admin/display/event.act.php?act=delete&event_ix='+event_ix;
		//document.getElementById('act').src= 'event.act.php?act=delete&event_ix='+event_ix;
	}
}


function setSelectDate(sdate,edate,date_type) {
	var frm = document.search_events;
	if(date_type == 1){
		$(\"#event_start_sdate\").val(sdate);
		$(\"#event_start_edate\").val(edate);
	}else{
		$(\"#event_end_sdate\").val(sdate);
		$(\"#event_end_edate\").val(edate);
	}
}


function searchUseSdate(frm){
	if(frm.use_sdate.checked){ 
		$('#event_start_sdate').attr('disabled',false);
		$('#event_start_edate').attr('disabled',false);	 
	}else{
		$('#event_start_sdate').attr('disabled',true);
		$('#event_start_edate').attr('disabled',true);
	}
}

function searchUseEdate(frm){
	if(frm.use_edate.checked){
		$('#event_end_sdate').attr('disabled',false);
		$('#event_end_edate').attr('disabled',false);	 
	}else{
		$('#event_end_sdate').attr('disabled',true);
		$('#event_end_edate').attr('disabled',true);
	}
}
 
 
function init(){

	var frm = document.searchmember; ";

if($use_sdate != "1"){
$Script .= "
	$('#event_start_sdate').attr('disabled',true);
	$('#event_start_edate').attr('disabled',true);
	";
}
if($use_edate != "1"){
$Script .= "
	$('#event_end_sdate').attr('disabled',true);
	$('#event_end_edate').attr('disabled',true);";
}
$Script .= "
}
 
  
</script>";

if($disp_yn=="") {
	$disp_yn="all";
}

$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("이벤트/기획전 관리", "전시관리 > 이벤트/기획전 관리 <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=".urlencode("몰스토리동영상메뉴얼_기획전등록(090322)_config.xml")."',800,517,'manual_view')\"  title='이벤트/기획전 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a>")."</td>
		</tr>";
if($_SESSION['admininfo']['admin_level'] == 9){
$mstring .="
		<tr>
			<td colspan=6>
			<div class='tab' style='width:100%;height:30px;margin:0px;'>
				<table width='100%' class='s_org_tab'>				
				<tr>							
					<td class='tab' >
					<table id='tab_1' class=on>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='event.list.php'\">이벤트/기획전 목록</td>
						<th class='box_03'></th>							
					</tr>
					</table>
					<table id='tab_2' ".$tabmenu_class2.">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='event.product_list.php'\">이벤트/기획전 상품목록</td>
						<th class='box_03'></th>				
					</tr>
					</table>
					</td>
				</tr>
				</table>										
			</div>	
			</td>
		</tr>";
}
$mstring .="
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
				<td class='box_05'  valign=top style='padding:5px'>
				<form name=search_events method='get' ><!--SubmitX(this);'-->
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
					<col width='15%'>
					<col width='35%'>
					<col width='15%'>
					<col width='35%'>
					";
					if($_SESSION["admin_config"][front_multiview] == "Y"){
					$mstring .= "
					<tr>
						<td class='search_box_title' > 프론트 전시 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
					}
					$mstring .= "
					<tr height=30>
					  <td class='search_box_title'>조건검색 </td>
					  <td class='search_box_item'  style='padding-left:5px;' colspan='3'>
						  <select name=search_type>
								<option value='event_title' ".CompareReturnValue("event_title",$search_type,"selected")." style='vertical-align:middle;'>이벤트/기획전 제목</option>
								<option value='manage_title' ".CompareReturnValue("manage_title",$search_type,"selected")." style='vertical-align:middle;'>이벤트/기획전 관리제목</option>
								<option value='event_keyword' ".CompareReturnValue("event_keyword",$search_type,"selected")." style='vertical-align:middle;'>이벤트/기획전 검색어</option>
								
						  </select>
						  <input type=text name='search_text' class='textbox' value='".$search_text."' style='width:30% ; vertical-align:top;' >
					  </td>
					</tr>
					<tr height=30>
					  <td class='search_box_title'>전시여부 </td>
					  <td class='search_box_item' >
					  <input type=radio name='disp_yn' value='all' id='disp_a'  ".CompareReturnValue("all",$disp_yn,"checked")."><label for='disp_a'>전체</label>
					  <input type=radio name='disp_yn' value='1' id='disp_y'  ".CompareReturnValue("1",$disp_yn,"checked")."><label for='disp_y'>사용</label>
					  <input type=radio name='disp_yn' value='0' id='disp_n' ".CompareReturnValue("0",$disp_yn,"checked")."><label for='disp_n'>미사용</label>
					  <input type=radio name='disp_yn' value='9' id='disp_a' ".CompareReturnValue("9",$disp_yn,"checked")."><label for='disp_a'>신청</label>
					  </td>
					  <td class='search_box_title'>이벤트/기획전 구분</td>
					  <td class='search_box_item' >
					  <input type=radio name='kind' value='' id='kind_a'  ".CompareReturnValue("",$kind,"checked")."><label for='kind_a'>전체</label>
					  <input type=radio name='kind' value='E' id='kind_e'  ".CompareReturnValue("E",$kind,"checked")."><label for='kind_e'>이벤트</label>
					  <input type=radio name='kind' value='P' id='kind_p'  ".CompareReturnValue("P",$kind,"checked")."><label for='kind_p'>기획전</label>
					  
					  </td>
					</tr>
					<tr height=30>
						<td class='search_box_title'>분류선택 </td>
					  <td class='search_box_item' >
							".SelectEventCate($er_ix)."
					  </td>
					  <td class='input_box_title' > 카테고리 선택 </td>
						<td class='input_box_item' >".categorySelect($category_choice)."</td>
					</tr>
					<tr height=30>
						<td class='search_box_title' >  담당 MD</td>
						<td class='search_box_item'>  ".MDSelect($md_code)."</td>
					    <td class='input_box_title'> 등록(관리)업체 </td>
						<td class='input_box_item' >
						".companyAuthList($company_id , "validation=false title='입점업체' ")."
						</td>
					</tr>
					";

		$vdate = date("Ymd", time());
		$today = date("Ymd", time());
		$vyesterday = date("Ymd", time()-84600);
		$voneweekago = date("Ymd", time()-84600*7);
		$vtwoweekago = date("Ymd", time()-84600*14);
		$vfourweekago = date("Ymd", time()-84600*28);
		$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
		$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
		$v15ago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
		$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
		$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
		$v2monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
		$v3monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

		 $mstring .= "
					<tr height=30>
						<td class='search_box_title'>
						<label for='use_sdate'>시작일자</label><input type='checkbox' name='use_sdate' id='use_sdate' value='1' ".CompareReturnValue("1",$use_sdate,"checked")." onclick='searchUseSdate(document.search_events);'>
						</td>
						<td class='search_box_item' colspan='3'>
							".search_date('event_start_sdate','event_start_edate',$event_start_sdate,$event_start_edate,'N','D')."	";
/*
							$mstring .= "
							<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100% >
								<tr>
									<TD width=7% nowrap><input type=text class='textbox' name='event_start_sdate' id='event_start_sdate' value='$event_start_sdate' style='width:70px;text-align:center;' ".($use_sdate ? "":"disabled")."></TD>
									<TD width=2% align=center> ~ </TD>
									<TD width=7% nowrap><input type=text class='textbox' name='event_start_edate' id='event_start_edate' value='$event_start_edate'  style='width:70px;text-align:center;' ".($use_sdate ? "":"disabled")."></TD>
									<TD width='*'> 
										<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
										<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
										<a href=\"javascript:setSelectDate('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
										<a href=\"javascript:setSelectDate('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
										<a href=\"javascript:setSelectDate('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
										<a href=\"javascript:setSelectDate('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
										<a href=\"javascript:setSelectDate('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
									</TD>
								</tr>
							</table>";
*/
							$mstring .= "
						</td>
					</tr>
					<tr height=30>
						<td class='search_box_title'><label for='use_edate'>종료일자</label><input type='checkbox' name='use_edate' id='use_edate' value='1' ".CompareReturnValue("1",$use_edate,"checked")." onclick='searchUseEdate(document.search_events);'></td>
						<td class='search_box_item'  colspan='3'>
						".search_date('event_end_sdate','event_end_edate',$event_end_sdate,$event_end_edate,'N','D')."";
/*
$mstring .= "
							<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
								<tr>
									<TD width=7% nowrap><input type=text class='textbox' name='event_end_sdate' id='event_end_sdate' value='$event_end_sdate'  style='width:70px;text-align:center;' ".($use_edate ? "":"disabled")."></TD>
									<TD width=2% align=center> ~ </TD>
									<TD width=7% nowrap><input type=text class='textbox' name='event_end_edate' id='event_end_edate' value='$event_end_edate'  style='width:70px;text-align:center;' ".($use_edate ? "":"disabled")."></TD>
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
							</table>";
*/
$mstring .= "
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
			".PrintEventList()."
			</td>
		</tr>
		";
$mstring .="</table>";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>이벤트 추가</b>를 원하시면 이벤트 추가버튼을 클릭해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 <u>이벤트는 </u> 사용으로 되어 있는 이벤트만 <a href='/event/promotion_list.php' target='_blank'>http://$HTTP_HOST/event/promotion_list.php</a> 에서 확인 하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기간이 만료된 이벤트는 자동으로 노출이 종료됩니다</td></tr>
</table>
";*/
	//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A',$HTTP_HOST,"HTTP_HOST");

//$help_text = HelpBox("이벤트/기획전 관리", $help_text);
//$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>이벤트/기획전 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_기획전등록(090322)_config.xml',800,517,'manual_view')\"  title='이벤트/기획전 동영상 메뉴얼입니다' style='cursor:pointer;'><img src='../image/movie_manual.gif' align=absmiddle width=26 height=20 style='position:absolute;top:-1px;'></a></td></tr></table>", $help_text,220);

$Contents = $mstring.$help_text;

if($agent_type == "M"  || $agent_type == "mobile"){
	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = $navigation;
	$P->title = $title;
	$P->strLeftMenu = mshop_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = "".$Script;
	$P->OnloadFunction = "init();";
	$P->Navigation = "프로모션/전시 > 이벤트/기획전 > 이벤트/기획전 목록";
	$P->title = "이벤트/기획전 목록";
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


function PrintEventList(){
	global $db, $mdb, $page, $search_type,$search_text,$disp_yn,$kind,$er_ix,$mall_ix;
	global $event_start_sdate,$event_start_edate,$event_end_sdate,$event_end_edate;
	global $auth_delete_msg, $auth_excel_msg, $admininfo, $agent_type;
	

	$where = " where se.event_ix <> '0' ";

	if($admininfo[admin_level] < 9){
		//if($admininfo[mall_type] == "B"  || $admininfo[mall_type] == "O"){// 입점형 , 오픈마켓형
			$where .= " and se.company_id = '".$admininfo[company_id]."' ";
		//}
	}
	if($agent_type){
		$where .= " and se.agent_type = '".$agent_type."' ";
	}

	if($_GET["md_code"] != ""){
		$where .= " and se.md_code = '".$_GET["md_code"]."' ";
	}

	if($_GET["company_id"] != ""){
		$where .= " and se.company_id = '".$_GET["company_id"]."' ";
	}

	if($_GET["category_choice"] != ""){
		$where .= " and se.cid = '".$_GET["category_choice"]."' ";
	}

	if($disp_yn == "1"){
		$where .= " and se.disp =  '1' ";
	}else if($disp_yn == "0"){
		$where .= " and se.disp = '0' ";
	}

	if($search_type != "" && $search_text != ""){
		$where .= " and $search_type LIKE  '%$search_text%' ";
	}

	if($event_start_sdate != "" && $event_start_edate != ""){
		$event_start_sdate_where = mktime(0,0,0,substr($event_start_sdate,5,2),substr($event_start_sdate,8,2),substr($event_start_sdate,0,4));
		$event_start_edate_where = mktime(0,0,0,substr($event_start_edate,5,2),substr($event_start_edate,8,2),substr($event_start_edate,0,4));
		$where .= " and  event_use_sdate between  $event_start_sdate_where and $event_start_edate_where ";
	}

	if($event_end_sdate != "" && $event_end_edate != ""){
		$event_end_sdate_where = mktime(0,0,0,substr($event_end_sdate,5,2),substr($event_end_sdate,8,2),substr($event_end_sdate,0,4));
		$event_end_edate_where = mktime(0,0,0,substr($event_end_edate,5,2),substr($event_end_edate,8,2),substr($event_end_edate,0,4));
		$where .= " and  event_use_edate between  $event_end_sdate_where and $event_end_edate_where ";
	}

	if($kind!=""){
		$where .= " and se.kind ='".$kind."' ";
	}

	if($er_ix!=""){
		$where .= " and se.er_ix ='".$er_ix."' ";
	}

	if($mall_ix!=""){
		$where .= " and se.mall_ix ='".$mall_ix."' ";
	}
	
	$sql = "select se.* from ".TBL_SHOP_EVENT." se left join shop_event_relation ser on (se.er_ix=ser.er_ix) $where";
//echo nl2br($sql);
	$mdb->query($sql);
	$total = $mdb->total;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$mString = "<table cellpadding=0 cellspacing=0 border=0 width=100%  class='list_table_box'>";
	$mString .= "
	<col width='4%'>
	".($_SESSION["admin_config"]["front_multiview"] == "Y" ? "<col style='width:7%;'>":"")."
	<col width='8%'>
	<col width='*'>
	<col width='7%'>
	<col width='14%'>
	<col width='5%'>
	<col width='5%'>
	<col width='5%'>
	<col width='7%'>
	<col width='14%'>
	<tr align=center bgcolor=#efefef height='30'>
		<td class=s_td >번호</td>
		".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td'> 프론트전시</td>":"")."
		<td class=m_td >분류선택</td>
		<td class=m_td >이벤트/기획전 제목</td>
		<td class=m_td >등록상품수</td>
		<td class=m_td>사용기간</td>
		<td class=m_td>참여자</td>
		<td class=m_td>사용</td>";
		if($admininfo[admin_level] == 9){
			if($admininfo[mall_type] == "B"  || $admininfo[mall_type] == "O"){// 입점형 , 오픈마켓형
$mString .= "
		<td class=m_td>신청업체</td>";
			}
		}
$mString .= "<td class=m_td>작업자</td>
		<td class=m_td>등록일</td>
		<td class=e_td>관리</td>
		</tr>";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=11 align=center>이벤트 내역이 존재 하지 않습니다.</td></tr>";
		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		$mString .= "<tr bgcolor=#ffffff ><td colspan=5 align=right style='padding:10px 0px;'><a href='event.write.php'><img src='../images/".$admininfo["language"]."/b_eventadd.gif' border=0 ></a></td></tr>";

	}else{
		
		/*
		if($admininfo[admin_level] < 9){
			
			$sql = "select se.*, ser.title, ccd.com_name , sum(case when ea_ix is null then 0 else 1 end) as applicant_cnt, sum(case when pid is null then 0 else 1 end) as product_cnt
						from ".TBL_SHOP_EVENT." se 
						left join shop_event_product_relation epr on se.event_ix = epr.event_ix
						right join shop_event_relation ser on (se.er_ix=ser.er_ix)
						right join common_company_detail ccd on se.company_id = ccd.company_id
						left join shop_event_applicant ea on se.event_ix = ea.event_ix
						$where
						group by se.event_ix
						order by se.regdate desc
						limit $start, $max";
		}else{

			$sql = "select se.* , ser.title, sum(case when ea_ix is null then 0 else 1 end) as applicant_cnt, sum(case when pid is null then 0 else 1 end) as product_cnt
						from ".TBL_SHOP_EVENT." se						
						left join shop_event_relation ser on (se.er_ix=ser.er_ix) 
						left join shop_event_product_relation epr on se.event_ix = epr.event_ix
						left join shop_event_applicant ea on se.event_ix = ea.event_ix
						$where  
						group by se.event_ix
						order by se.regdate desc 
						limit $start, $max";
		}
		*/

		$sql = "select 
					se.*, ccd.com_name, 
					(select count(*) from shop_event_applicant ea where se.event_ix = ea.event_ix ) as applicant_cnt,
					(select count(*) from shop_event_product_relation epr where se.event_ix = epr.event_ix ) as product_cnt
				from 
				(
					select se.*, ser.title, sec.use_comment
					from ".TBL_SHOP_EVENT." se 
					left join shop_event_relation ser on (se.er_ix=ser.er_ix)
					left join shop_event_config sec on (se.event_ix=sec.event_ix)
					$where
					order by se.regdate desc
					limit $start, $max
				) se
				left join common_company_detail ccd on se.company_id = ccd.company_id
				";
		
		//echo nl2br($sql);
		$db->query($sql);
		$event_infos = $db->fetchall("object");

		for($i=0;$i < count($event_infos);$i++){
			//$db->fetch($i);
			$no = $total - ($page - 1) * $max - $i;
			//$no = $no + 1;
			$sql = "select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from ".TBL_COMMON_MEMBER_DETAIL." cmd where code= '".$event_infos[$i][worker_ix]."' ";
			//echo $sql;
			$db->query($sql);
			$db->fetch();
			$worker_name = $db->dt[name];

			if($event_infos[$i][disp] == "1"){
				$disp_str = "사용";
			}else if($event_infos[$i][disp] == "0"){
				$disp_str = "미사용";
			}else if($event_infos[$i][disp] == "9"){
				$disp_str = "신청";
			}else{
				$disp_str = "기타";
			}

			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td class='list_box_td '>".$no."</td>
			";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$mString .= "
		    <td class='list_box_td list_bg_gray'>".GetDisplayDivision($event_infos[$i][mall_ix], "text")."</td>";
}
	$mString .= "
			<td class='list_box_td '>".$event_infos[$i][title]."</td>
			<td class='list_box_td point' style='text-align:left;padding:5px 5px 5px 10px;font-weight:normal;line-height:150%;'>
			<a href='event.write.php?event_ix=".$event_infos[$i][event_ix]."'>
			".($event_infos[$i][manage_title] ? "<b>".$event_infos[$i][manage_title]."</b><br>":"")."</a>
			<a href='event.write.php?event_ix=".$event_infos[$i][event_ix]."'>".$event_infos[$i][event_title]."</a><br>
			단축URL : http://".str_replace("www.","",$_SERVER["HTTP_HOST"])."/link/e.php?ix=".$event_infos[$i][event_ix]."
			</td>
			<td class='list_box_td '>".$event_infos[$i][product_cnt]."</td>
			<td class='list_box_td '>".date("Y.m.d",$event_infos[$i][event_use_sdate])." ~ ".date("Y.m.d",$event_infos[$i][event_use_edate])."</td>
			<td class='list_box_td list_bg_gray'><a href=\"javascript:PoPWindow3('event_applicant.php?mmode=pop&event_ix=".$event_infos[$i][event_ix]."',1000,800,'inventory_goods_info')\"  >".($event_infos[$i][applicant_cnt])."</a></td>
			<td class='list_box_td'>".($disp_str)."</td>
			";
		if($admininfo[admin_level] == 9){
			if($admininfo[mall_type] == "B"  || $admininfo[mall_type] == "O"){// 입점형 , 오픈마켓형
$mString .= "<td class='list_box_td list_bg_gray'>".($event_infos[$i][com_name] == "" ? "본사":$event_infos[$i][com_name])."</td>";
			}
		}
$mString .= "<td class='list_box_td list_bg_gray ' style='line-height:140%;'>
			".($event_infos[$i][worker_ix] == "" ? "없음":$worker_name)."";
			if($_SESSION["admininfo"]["admin_level"] == 9 && $event_infos[$i][worker_ix] !="" &&  $event_infos[$i][worker_ix] != $_SESSION["admininfo"]["charger_ix"] ){
				$mString .= "	<br><a href='#' onclick=\"if(confirm('진행중인 작업을 강제로 종료하시겠습니까?')){window.frames['iframe_act'].location.href ='../display/event.act.php?act=initialize&event_ix=".$event_infos[$i][event_ix]."';}\" style='color:red;'>작업강제 종료</a>";
			}
			$mString .= "
			</td>
			<td class='list_box_td '>".str_replace("-",".",substr($event_infos[$i][regdate],0,10))."</td>
			<td class='list_box_td list_bg_gray' nowrap>";
			$mString .= "	<!-- 박수철 과장 작업 수정 버튼 추가-->
							<a href='event.write.php?event_ix=".$event_infos[$i][event_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>";
if($agent_type == 'W' && $event_infos[$i][use_comment]=='1'){
	/*
			$mString .= "
							<a href=\"javascript:PoPWindow3('event_applicant.php?mmode=pop&event_ix=".$event_infos[$i][event_ix]."',1000,800,'inventory_goods_info')\"  ><img src='../images/".$admininfo["language"]."/btn_result.gif' border=0 align=absmiddle alt='결과보기' title='결과보기'></a>";
	*/

    $mString .= "
							<a href=\"javascript:PoPWindow3('event_comment.php?mmode=pop&event_ix=".$event_infos[$i][event_ix]."',1000,800,'event_comment')\"  ><img src='../images/".$admininfo["language"]."/btn_result.gif' border=0 align=absmiddle alt='댓글목록' title='댓글목록'></a>";
}
			/*
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
				$mString .= "
							<a href='event_goods_excel.php?event_ix=".$db->dt[event_ix]."' target='iframe_act'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align=absmiddle alt='엑셀로저장' title='엑셀로 저장' ></a>";
			}else{
				$mString .= "
							<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align=absmiddle ></a>";
			}
			*/

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$mString .= "<a href=\"JavaScript:eventDelete('".$event_infos[$i][event_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;' alt='삭제' title='삭제'></a>";
			}else{
			$mString .= "<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;'></a>";
			}
			$mString .= "
			</td>
			</tr>
			";
		}
		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		$mString .= "<tr bgcolor=#ffffff style='height:50px;'>
					<td colspan=3 align=left>
					".page_bar($total, $page, $max,  "&max=$max&search_type=$search_type&search_text=$search_text&event_start_sdate=$event_start_sdate&event_start_edate=$event_start_edate&event_end_sdate=$event_end_sdate&event_end_edate=$event_end_edate&disp_yn=$disp_yn","")."</td>
					<td colspan=2 align=right>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		$mString .= "<a href='event.write.php'><img src='../images/".$admininfo["language"]."/b_eventadd.gif' border=0 ></a>";
		}
		$mString .= "
					</td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}

function SelectEventCate($category){
	$db = new Database;

	$sql = "SELECT * FROM shop_event_relation ORDER BY regdate ";
	$db->query($sql);
	$cateArr = $db->fetchall();

	$mstring =  "<select name='er_ix'>";
	$mstring .=  "<option value=''>선택하세요.</option>";
	if(is_array($cateArr)){
		foreach($cateArr as $_KEY=>$_VALUE) {
			$mstring .= "<option value='".$_VALUE[er_ix]."' ".($_VALUE[er_ix] == $category ? " selected ":"").">".$_VALUE[title]."</option>";
		}
	}
	$mstring .=  "</select>";

	return $mstring;
}
?>
