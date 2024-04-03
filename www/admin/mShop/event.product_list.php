<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;
if(!$agent_type){
	$agent_type = "M";
}


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

function eventDelete(event_ix, erp_ix){
	if(confirm('해당 이벤트 상품을 정말로 삭제하시겠습니까? '))
	{//
		window.frames['act'].location.href= '/admin/display/event.act.php?act=event_product_delete&event_ix='+event_ix+'&erp_ix='+erp_ix;
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
		</tr>
		<tr>
			<td colspan=6>
			<div class='tab' style='width:100%;height:30px;margin:0px;'>
				<table width='100%' class='s_org_tab'>				
				<tr>							
					<td class='tab' >
					<table id='tab_1' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='event.list.php'\">이벤트/기획전 목록</td>
						<th class='box_03'></th>							
					</tr>
					</table>
					<table id='tab_2' class=on>
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
								<option value='group_name' ".CompareReturnValue("group_name",$search_type,"selected")." style='vertical-align:middle;'>이벤트 그룹명</option>
								<option value='event_name' ".CompareReturnValue("event_name",$search_type,"selected")." style='vertical-align:middle;'>이벤트 그룹 이벤트명</option>
								<option value='pname' ".CompareReturnValue("pname",$search_type,"selected")." style='vertical-align:middle;'>상품명</option>								
								<option value='id' ".CompareReturnValue("id",$search_type,"selected")." style='vertical-align:middle;'>상품코드(시스템)</option>
								
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
	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A',$HTTP_HOST,"HTTP_HOST");

//$help_text = HelpBox("이벤트/기획전 관리", $help_text);
$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>이벤트/기획전 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_기획전등록(090322)_config.xml',800,517,'manual_view')\"  title='이벤트/기획전 동영상 메뉴얼입니다' style='cursor:pointer;'><img src='../image/movie_manual.gif' align=absmiddle width=26 height=20 style='position:absolute;top:-1px;'></a></td></tr></table>", $help_text,220);

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
	$P->Navigation = "프로모션/전시 > 이벤트/기획전 > 이벤트/기획전 상품목록";
	$P->title = "이벤트/기획전 상품목록";
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


function PrintEventList(){
	global $db, $mdb, $page, $search_type,$search_text,$disp_yn,$kind,$er_ix;
	global $event_start_sdate,$event_start_edate,$event_end_sdate,$event_end_edate;
	global $auth_delete_msg, $auth_excel_msg, $admininfo, $agent_type;
	global $LargeImageSize;
	

	$where = " where se.event_ix <> '0' ";

	if($admininfo[admin_level] < 9){
		if($admininfo[mall_type] == "B"  || $admininfo[mall_type] == "O"){// 입점형 , 오픈마켓형
			$where .= " and se.company_id = '".$admininfo[company_id]."' ";
		}
	}
	if($agent_type){
		$where .= " and se.agent_type = '".$agent_type."' ";
	}

	if($_GET["md_code"] != ""){
		$where .= " and se.md_code = '".$_GET["md_code"]."' ";
	}

	if($_GET["ori_company_id"] != ""){
		//$where .= " and se.company_id = '".$_GET["ori_company_id"]."' ";
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
	
	$sql = "select count(*) as total
						from ".TBL_SHOP_EVENT." se 
						left join shop_event_relation ser on (se.er_ix=ser.er_ix) 
						left join shop_event_product_group epg on se.event_ix = epg.event_ix
						join shop_event_product_relation epr on se.event_ix = epr.event_ix and epg.group_code = epr.group_code
						left join shop_product p on epr.pid = p.id 
						$where ";

	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];

	//echo $total;

	$max = 20;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	$mString = "
	<table width=100%>
		<tr>
			<td>
			<div style='padding:5px;'>이벤트/기획전 상품등록수 : ".number_format($total)." 개</div>
			</td>
			<td align=right>";


			$mString .= "<a href='?".$_SERVER["QUERY_STRING"]."&mode=excel'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
			
			$mString .= "
			</td>
		</tr>
	</table>";

	$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  class='list_table_box'>";
	$mString .= "
	<col width='4%'>
	".($_SESSION["admin_config"]["front_multiview"] == "Y" ? "<col style='width:7%;'>":"")."
	<col width='*'>
	<col width='20%'>	
	<col width='8%'>
	<col width='20%'>
	<col width='6%'>
	<col width='8%'>
	<col width='6%'>
	<col width='10%'>
	<tr align=center bgcolor=#efefef height='40'>
		<td class=s_td >번호</td>
		".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td'> 프론트전시</td>":"")."
		
		<td class=m_td >이벤트 분류선택/이벤트/기획전 제목</td>
		<td class=m_td >상품그룹명/상품그룹 이벤트명</td>
		<td class=m_td >전시상품코드</td>
		<td class=m_td>전시상품명</td>
		<td class=m_td>판매가</td>
		<td class=m_td>혜택가<br>(최종판매가)</td>";
		 
$mString .= "<td class=m_td>할인율<br>할인금액</td>
		<td class=e_td>관리</td>
		</tr>";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=9 align=center>이벤트 내역이 존재 하지 않습니다.</td></tr>";
		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		$mString .= "<tr bgcolor=#ffffff ><td colspan=5 align=right style='padding:10px 0px;'><a href='event.write.php'><img src='../images/".$admininfo["language"]."/b_eventadd.gif' border=0 ></a></td></tr>";

	}else{
		if($admininfo[admin_level] < 9){
			$sql = "select se.*, epg.*, p.id, epr.erp_ix, epr.pid, p.pname, p.listprice, p.sellprice,  ser.title, pr.cid, 4 as depth
						from ".TBL_SHOP_EVENT." se 
						left join shop_event_relation ser on (se.er_ix=ser.er_ix) 
						left join shop_event_product_group epg on se.event_ix = epg.event_ix
						join shop_event_product_relation epr on se.event_ix = epr.event_ix and epg.group_code = epr.group_code
						join shop_product p on epr.pid = p.id 
						left join shop_product_relation pr on p.id = pr.pid and pr.basic = 1 
						$where
						 
						order by se.regdate desc
						";
			if($_REQUEST["mode"] != "excel"){
			$sql .= "limit $start, $max";
			}
		}else{
			$sql = "select se.*, epg.*, p.id, epr.erp_ix, epr.pid, p.pname, p.listprice, p.sellprice,  ser.title, pr.cid, 4 as depth
						from ".TBL_SHOP_EVENT." se 
						left join shop_event_relation ser on (se.er_ix=ser.er_ix) 
						left join shop_event_product_group epg on se.event_ix = epg.event_ix
						join shop_event_product_relation epr on se.event_ix = epr.event_ix and epg.group_code = epr.group_code
						join shop_product p on epr.pid = p.id 
						left join shop_product_relation pr on p.id = pr.pid and pr.basic = 1 
						$where
						 
						order by se.regdate desc
						";
			if($_REQUEST["mode"] != "excel"){
			$sql .= "limit $start, $max";
			}
		}
	
		//echo nl2br($sql);
		$db->query($sql);
		$event_infos = $db->fetchall();

		if(count($event_infos)){
			$script_times["product_discount_start"] = time();
			for($i=0 ; $i < count($event_infos) ;$i++){
				$_array_pid[] = $event_infos[$i][pid];
				$goods_infos[$event_infos[$i][pid]][pid] = $event_infos[$i][pid];
				$goods_infos[$event_infos[$i][pid]][amount] = 1;//$event_infos[$i][pcount];
				$goods_infos[$event_infos[$i][pid]][cid] = $event_infos[$i][cid];
				$goods_infos[$event_infos[$i][pid]][depth] = $event_infos[$i][depth];
			}
			//print_r($goods_infos);
			$discount_info = DiscountRult($goods_infos, $cid, $depth);
			//print_r($discount_info);
			//exit;
			if(is_array($event_infos))
			{
				
				foreach ($event_infos as $key => $sub_array) {
					$select_ = array("icons_list"=>explode(";",$sub_array[icons]));
					array_insert($sub_array,50,$select_);
					//echo str_pad($sub_array[id], 10, "0", STR_PAD_LEFT)."<br>";
					$discount_item = $discount_info[$sub_array[id]];

					//print_r($discount_item);
					//exit;
					$_dcprice = $sub_array[sellprice];
					if(is_array($discount_item)){						
						foreach($discount_item as $_key => $_item){ 
							if($_item[discount_value_type] == "1"){ // %
								//echo $_item[discount_value]."<br>";
								$_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]);//$_dcprice*(100 - $_item[discount_value])/100;						
							}else if($_item[discount_value_type] == "2"){// 원
								$_dcprice = $_dcprice - $_item[discount_value];
							} 
							$discount_desc[] = $_item;//array("discount_type"=>$_item[discount_type], "haddoffice_value"=>$_item[discount_value], "discount_value"=>$_item[discount_value], 
						}						
					}else{
						unset($_item);
					}
					$_dcprice = array("dcprice"=>$_dcprice);
					array_insert($sub_array,72,$_dcprice);
					$discount_desc = array("discount_desc"=>$discount_desc);
					array_insert($sub_array,73,$discount_desc);
					$discount_value = array("discount_value"=>$_item[discount_value]);
					array_insert($sub_array,74,$discount_value);
					$discount_value_type = array("discount_value_type"=>$_item[discount_value_type]);
					array_insert($sub_array,75,$discount_value_type);

					$event_infos[$key] = $sub_array;
					if($event_infos[$key][uf_valuation] != "") $event_infos[$key][uf_valuation] = round($event_infos[$key][uf_valuation], 0);
					else $event_infos[$key][uf_valuation] = 0;
				}
				//print_r($event_infos);
				//exit;
			}
			//print_r($event_infos);
		}

		if($_REQUEST["mode"] == "excel"){
			
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
		 
			$discount_excel->getActiveSheet(0)->setCellValue('A' . 1, "상품그룹명");//iconv('UTF-8','EUC-KR',"번호")
			$discount_excel->getActiveSheet(0)->setCellValue('B' . 1, "상품그룹 이벤트명");
			$discount_excel->getActiveSheet(0)->setCellValue('C' . 1, "상품그룹 전시여부");
			$discount_excel->getActiveSheet(0)->setCellValue('D' . 1, "전시상품 상품코드");
			$discount_excel->getActiveSheet(0)->setCellValue('E' . 1, "전시상품 상품명");
			
			$discount_excel->getActiveSheet(0)->setCellValue('F' . 1, "전시상품 판매가");
			$discount_excel->getActiveSheet(0)->setCellValue('G' . 1, "저시상품 혜택가(최종판매가)");
			$discount_excel->getActiveSheet(0)->setCellValue('H' . 1, "전시상품할인율");

			if($discount_type == "SP"){
				//$discount_excel->getActiveSheet(0)->setCellValue('M' . 1, "수수료");
			}
			

			$before_pid = "";
		 
			for ($i = 0; $i < count($event_infos); $i++)
			{
				$j="A";
				
				switch($event_infos[$i][disp]){
					case '9':
						$disp = '신청';
					break;
					case '1':
						$disp = '사용';
					break;
					case '0':
						$disp = '미사용';
					break;
				}
				$discount_excel->getActiveSheet()->setCellValue('A' . ($i + 2), $event_infos[$i][group_name]);
				$discount_excel->getActiveSheet()->setCellValue('B' . ($i + 2), $event_infos[$i][event_name]);
				$discount_excel->getActiveSheet()->setCellValue('C' . ($i + 2), $disp);
				$discount_excel->getActiveSheet()->setCellValue('D' . ($i + 2), $event_infos[$i][pid]);
				$discount_excel->getActiveSheet()->setCellValue('E' . ($i + 2), $event_infos[$i][pname]);
				$discount_excel->getActiveSheet()->setCellValue('F' . ($i + 2), $event_infos[$i][sellprice]);
				$discount_excel->getActiveSheet()->setCellValue('G' . ($i + 2), $event_infos[$i][dcprice]);
				$discount_excel->getActiveSheet()->setCellValue('H' . ($i + 2), ($event_infos[$i][discount_value] != "" ? number_format($event_infos[$i][discount_value])." ".($event_infos[$i][discount_value_type] == "1" ? "%":"원"):"") );

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
			
			if(is_excel_csv()){
				header('Content-Type: application/vnd.ms-excel;');//charset=euckr
				header('Content-Disposition: attachment;filename="event_product_list_'.date("Ymd").'.csv"');
				header('Cache-Control: max-age=0');
				//setlocale(LC_CTYPE, 'ko_KR.eucKR');
				//header("Content-charset=euckr");
				//header("Content-Description: PHP5 Generated Data");
				$objWriter = PHPExcel_IOFactory::createWriter($discount_excel, 'CSV');
				$objWriter->setUseBOM(true);
			}else{
				header('Content-Type: application/vnd.ms-excel;');
				header('Content-Disposition: attachment;filename="event_product_list_'.date("Ymd").'.xls"');
				header('Cache-Control: max-age=0');
				$objWriter = PHPExcel_IOFactory::createWriter($discount_excel, 'Excel5');
			}

			$objWriter->save('php://output');

			exit;
		}

		for($i=0;$i < count($event_infos);$i++){
			//$db->fetch($i);
			$no = $total - ($page - 1) * $max - $i;
			//$no = $no + 1;
			 

			if($event_infos[$i][disp] == "1"){
				$disp_str = "사용";
			}else if($event_infos[$i][disp] == "0"){
				$disp_str = "미사용";
			}else if($event_infos[$i][disp] == "9"){
				$disp_str = "신청";
			}else{
				$disp_str = "기타";
			}

			$img_str = PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $event_infos[$i][pid], "s", $event_infos[$i]);

			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td class='list_box_td '>".$no."</td>
			";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$mString .= "
		    <td class='list_box_td list_bg_gray'>".GetDisplayDivision($event_infos[$i][mall_ix], "text")."</td>";
}
	$mString .= "
			
			<td class='list_box_td point' style='text-align:left;padding:5px 5px 5px 10px;font-weight:normal;line-height:150%;'><a href='event.write.php?event_ix=".$event_infos[$i][event_ix]."'>".($event_infos[$i][title] ? $event_infos[$i][title]."<br>":"")."".($event_infos[$i][manage_title] ? "<b>".$event_infos[$i][manage_title]."</b><br>":"").$event_infos[$i][event_title]."</a></td>

			<td class='list_box_td ' style='text-align:left;padding-left:10px;line-height:150%;'>".$event_infos[$i][group_name]."<br>".$event_infos[$i][event_name]."</td>
			<td class='list_box_td '>".$event_infos[$i][pid]."</td>
			<td class='list_box_td ' style='text-align:left;padding:10px;'>
			<table>
				<tr>
					<td style='padding:5px;'>	
					<a href='/shop/goods_view.php?id=".$event_infos[$i][pid]."' target='_blank' class='screenshot'  rel='".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $event_infos[$i][pid], $LargeImageSize, $event_infos[$i])."'><img src='".$img_str."' width=50 height=50></a></td>
					<td><a href='/shop/goods_view.php?id=".$event_infos[$i][pid]."' target='_blank' class='screenshot'  rel='".PrintImage($_SESSION["admin_config"][mall_data_root]."/images/product", $event_infos[$i][pid], $LargeImageSize, $event_infos[$i])."'>".$event_infos[$i][pname]."</a></td>
				</tr>
			</table>
			</td>
			<td class='list_box_td '>".number_format($event_infos[$i][sellprice])."</td>
			<td class='list_box_td '>".number_format($event_infos[$i][dcprice])."</td>
			<td class='list_box_td '>".($event_infos[$i][discount_value] != "" ? number_format($event_infos[$i][discount_value])." ".($event_infos[$i][discount_value_type] == "1" ? "%":"원"):"")." </td>
			";  
			$mString .= "
			</td> 
			<td class='list_box_td list_bg_gray' nowrap>";
			$mString .= "	<!-- 박수철 과장 작업 수정 버튼 추가-->
							<a href='event.write.php?event_ix=".$event_infos[$i][event_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>";
 
		 
			
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$mString .= "<a href=\"JavaScript:eventDelete('".$event_infos[$i][event_ix]."','".$event_infos[$i][erp_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;' alt='삭제' title='삭제'></a>";
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
