<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;
$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
if ($use_sdate == "1"){
	if(!$st_start_sdate || !$st_start_edate){ 
		$st_start_sdate = date("Ymd", $before10day);
		$st_start_edate = date("Ymd");	
	}
}

if ($use_edate == "1"){
	if(!$st_end_sdate || !$st_end_edate){ 
		$st_end_sdate = date("Ymd", $before10day);
		$st_end_edate = date("Ymd");	
	}
}

$Script = "<script language='javascript'>

$(function() {
	$(\"#st_start_sdate\").datepicker({
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

	$(\"#st_start_edate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});


	$(\"#st_end_sdate\").datepicker({
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

	$(\"#st_end_edate\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});

function stDelete(st_ix){
	if(confirm('해당 검색어를 삭제하시겠습니까?'))
		window.frames['act'].location.href= 'search_text.act.php?act=delete&st_ix='+st_ix;
	}
}


function setSelectDate(sdate,edate,date_type) {
	var frm = document.search_events;
	if(date_type == 1){
		$(\"#st_start_sdate\").val(sdate);
		$(\"#st_start_edate\").val(edate);
	}else{
		$(\"#st_end_sdate\").val(sdate);
		$(\"#st_end_edate\").val(edate);
	}
}


function searchUseSdate(frm){
	if(frm.use_sdate.checked){ 
		$('#st_start_sdate').attr('disabled',false);
		$('#st_start_edate').attr('disabled',false);	 
	}else{
		$('#st_start_sdate').attr('disabled',true);
		$('#st_start_edate').attr('disabled',true);
	}
}

function searchUseEdate(frm){
	if(frm.use_edate.checked){
		$('#st_end_sdate').attr('disabled',false);
		$('#st_end_edate').attr('disabled',false);	 
	}else{
		$('#st_end_sdate').attr('disabled',true);
		$('#st_end_edate').attr('disabled',true);
	}
}
 
  
</script>";


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("검색어관리", "전시관리 > 검색어관리")."</td>
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
					<tr height=30>
					  <td class='search_box_title'>텍스트명 </td>
					  <td class='search_box_item'  style='padding-left:5px;' colspan='3'>
						  <!--select name=search_type>
								<option value='st_text' ".CompareReturnValue("st_text",$search_type,"selected")." style='vertical-align:middle;'>텍스트명</option>
						  </select-->
						  <input type=text name='search_text' class='textbox' value='".$search_text."' style='width:30% ; vertical-align:top;' >
					  </td>
					</tr>
					<tr height=30>
					  <td class='search_box_title'>사용여부 </td>
					  <td class='search_box_item' >
					  <input type=radio name='disp' value='' id='disp_a'  ".CompareReturnValue("",$disp,"checked")."><label for='disp_a'>전체</label>
					  <input type=radio name='disp' value='1' id='disp_y'  ".CompareReturnValue("1",$disp,"checked")."><label for='disp_y'>사용</label>
					  <input type=radio name='disp' value='0' id='disp_n' ".CompareReturnValue("0",$disp,"checked")."><label for='disp_n'>미사용</label>
					  </td>
					  <td class='search_box_title'>노출 타입</td>
					  <td class='search_box_item' >
					  <input type=radio name='st_type' value='' id='st_type_a'  ".CompareReturnValue("",$st_type,"checked")."><label for='st_type_a'>전체</label>
					  <input type=radio name='st_type' value='1' id='st_type_1'  ".CompareReturnValue("1",$st_type,"checked")."><label for='st_type_1'>텍스트</label>
					  <input type=radio name='st_type' value='2' id='st_type_2'  ".CompareReturnValue("2",$st_type,"checked")."><label for='st_type_2'>이미지</label>
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
						
						<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100% >
							<tr>
								<TD width=7% nowrap><input type=text class='textbox' name='st_start_sdate' id='st_start_sdate' value='$st_start_sdate' style='width:70px;text-align:center;' ".($use_sdate ? "":"disabled")."></TD>
								<TD width=2% align=center> ~ </TD>
								<TD width=7% nowrap><input type=text class='textbox' name='st_start_edate' id='st_start_edate' value='$st_start_edate'  style='width:70px;text-align:center;' ".($use_sdate ? "":"disabled")."></TD>
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
						</table>

					  </td>
					</tr>
					<tr height=30>
					  <td class='search_box_title'><label for='use_edate'>종료일자</label><input type='checkbox' name='use_edate' id='use_edate' value='1' ".CompareReturnValue("1",$use_edate,"checked")." onclick='searchUseEdate(document.search_events);'></td>
					  <td class='search_box_item'  colspan='3'>
						<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
							<tr>
								<TD width=7% nowrap><input type=text class='textbox' name='st_end_sdate' id='st_end_sdate' value='$st_end_sdate'  style='width:70px;text-align:center;' ".($use_edate ? "":"disabled")."></TD>
								<TD width=2% align=center> ~ </TD>
								<TD width=7% nowrap><input type=text class='textbox' name='st_end_edate' id='st_end_edate' value='$st_end_edate'  style='width:70px;text-align:center;' ".($use_edate ? "":"disabled")."></TD>
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

$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>검색어관리</b></td></tr></table>", $help_text,220);

$Contents = $mstring.$help_text;


$P = new LayOut();
$P->addScript = "".$Script;
$P->OnloadFunction = "";
$P->Navigation = "프로모션/전시 > 검색어관리";
$P->title = "검색어관리";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintSearchTextList(){
	global $db, $mdb, $page, $search_type;
	global $auth_delete_msg, $auth_excel_msg, $admininfo;


	$where = " where st_ix <> '0' ";
	

	if($_GET["st_type"] != ""){
		$where .= " and st.st_type =  '".$_GET["st_type"]."' ";
	}

	if($_GET["disp"] != ""){
		$where .= " and st.disp =  '".$_GET["disp"]."' ";
	}

	if($_GET["search_text"] != ""){
		$where .= " and st.st_text LIKE  '%".$_GET["search_text"]."%' ";
	}
 
	if($_GET["st_start_sdate"] != "" && $_GET["st_start_edate"] != ""){
		$unix_timestamp_start_sdate = mktime(0,0,0,substr($_GET["st_start_sdate"],4,2),substr($_GET["st_start_sdate"],6,2),substr($_GET["st_start_sdate"],0,4));
		$unix_timestamp_start_edate = mktime(0,0,0,substr($_GET["st_start_edate"],4,2),substr($_GET["st_start_edate"],6,2),substr($_GET["st_start_edate"],0,4));

		$where .= " and  st_sdate between  ".$unix_timestamp_start_sdate." and ".$unix_timestamp_start_edate." ";
	}
 

	if($_GET["st_end_sdate"] != "" && $_GET["st_end_edate"] != ""){
		$unix_timestamp_end_sdate = mktime(0,0,0,substr($_GET["st_end_sdate"],4,2),substr($_GET["st_end_sdate"],6,2),substr($_GET["st_end_sdate"],0,4));
		$unix_timestamp_end_edate = mktime(0,0,0,substr($_GET["st_end_edate"],4,2),substr($_GET["st_end_edate"],6,2),substr($_GET["st_end_edate"],0,4));

		$where .= " and  st_edate between  ".$unix_timestamp_end_sdate." and ".$unix_timestamp_end_edate." ";
	}


	$sql = "select st.* from shop_search_text st $where";

	//echo $sql;
	$mdb->query($sql);
	$total = $mdb->total;

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	
	if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
	}
	$str_page_bar = page_bar($total, $page, $max, $query_string,"");


	$mString = "<table cellpadding=0 cellspacing=0 border=0 width=100%  class='list_table_box'>";
	$mString .= "
	<col width='5%'>
	<col width='*'>
	<col width='10%'>
	<col width='20%'>
	<col width='10%'>
	<col width='15%'>
	<col width='15%'>
	<tr align=center bgcolor=#efefef height='30'>
		<td class=s_td >번호</td>
		<td class=m_td >텍스트명</td>
		<td class=m_td >조회수</td>
		<td class=m_td>기간</td>
		<td class=m_td>사용여부</td>
		<td class=m_td>등록일자</td>
		<td class=e_td>관리</td>
		</tr>";


	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=8 align=center>등록한 검색어가 존재 하지 않습니다.</td></tr>";
		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		$mString .= "<tr bgcolor=#ffffff ><td colspan=5 align=right style='padding:10px 0px;'><a href='search_text.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a></td></tr>";

	}else{

		$sql = "select st.*,sk.searchcnt from shop_search_text st left join shop_search_keyword sk on (st.st_text=sk.keyword and sk.disp='1') $where order by st.regdate desc limit $start, $max";

		$db->query($sql);
		$st_infos = $db->fetchall();

		for($i=0;$i < count($st_infos);$i++){

			$no = $total - ($page - 1) * $max - $i;

			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td class='list_box_td list_bg_gray'>".$no."</td>
			<td class='list_box_td '>".$st_infos[$i][st_text]."</td>
			<td class='list_box_td point'>".number_format($st_infos[$i][searchcnt])."</td>
			<td class='list_box_td '>".date("Y-m-d",$st_infos[$i][st_sdate])." ~ ".date("Y-m-d",$st_infos[$i][st_edate])."</td>
			<td class='list_box_td list_bg_gray'>".($st_infos[$i][disp] == "1" ? "사용":"미사용")."</td>
			<td class='list_box_td ' style='line-height:140%;'>".$st_infos[$i][regdate]."</td>
			<td class='list_box_td list_bg_gray' nowrap>";

			$mString .= "<a href='search_text.php?st_ix=".$st_infos[$i][st_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>";

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$mString .= "<a href=\"JavaScript:stDelete('".$st_infos[$i][st_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;' alt='삭제' title='삭제'></a>";
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
					<td colspan=3 align=left>".$str_page_bar."</td>
					<td colspan=2 align=right>";

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		$mString .= "<a href='search_text.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a>";
		}

		$mString .= "
					</td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}

/*
CREATE TABLE IF NOT EXISTS `shop_search_text` (
  `st_ix` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `st_text` varchar(255) DEFAULT NULL COMMENT '텍스트명',
  `st_sdate` int(11) DEFAULT NULL COMMENT '노출시작시간',
  `st_edate` int(11) DEFAULT NULL COMMENT '노출끝시간',
  `st_type` char(1) DEFAULT NULL COMMENT '1:TEXT,2:IMG',
  `st_title` varchar(100) DEFAULT NULL COMMENT '타이틀',
  `st_url` varchar(255) DEFAULT NULL COMMENT '링크',
  `disp` char(1) DEFAULT NULL COMMENT '사용여부',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`st_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='검색어관리' AUTO_INCREMENT=1 ;


*/

?>