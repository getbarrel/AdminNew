<?
include("../class/layout.class");

$db = new MySQL;
$mdb = new MySQL;
$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
if ($use_sdate == "1"){
	if(!$fg_use_sdate_start || !$fg_use_sdate_end){ 
		$fg_use_sdate_start = date("Ymd", $before10day);
		$fg_use_sdate_end = date("Ymd");	
	}
}

if ($use_edate == "1"){
	if(!$fg_use_edate_start || !$fg_use_edate_end){ 
		$fg_use_edate_start = date("Ymd", $before10day);
		$fg_use_edate_end = date("Ymd");	
	}
}

//print_r($_FREEGIFT_CONDITION["B"]);

$Script = "
<link rel='stylesheet' type='text/css' href='/admin/v3/css/jquery-ui.css' />
<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script language='javascript'>

$(function() {
	$(\"#fg_use_sdate_start\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#event_edate').val() != '' && $('#event_edate').val() <= dateText){
			$('#event_edate').val(dateText);
		}
	}

	});

	$(\"#fg_use_sdate_end\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});


	$(\"#fg_use_edate_start\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#event_edate').val() != '' && $('#event_edate').val() <= dateText){
			$('#event_edate').val(dateText);
		}
	}

	});

	$(\"#fg_use_edate_end\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});

function FreeGiftDelete(fg_ix){
	if(confirm('해당 사은품행사를 삭제하시겠습니까?')){
		window.frames['act'].location.href= 'freegift.act.php?act=delete&fg_ix='+fg_ix;
	}
}


function setSelectDate(sdate,edate,date_type) {
	var frm = document.search_events;
	if(date_type == 1){
		$(\"#fg_use_sdate_start\").val(sdate);
		$(\"#fg_use_sdate_end\").val(edate);
	}else{
		$(\"#fg_use_edate_start\").val(sdate);
		$(\"#fg_use_edate_end\").val(edate);
	}
}


function searchUseSdate(frm){
	if(frm.use_sdate.checked){ 
		$('#fg_use_sdate_start').attr('disabled',false);
		$('#fg_use_sdate_end').attr('disabled',false);	 
	}else{
		$('#fg_use_sdate_start').attr('disabled',true);
		$('#fg_use_sdate_end').attr('disabled',true);
	}
}

function searchUseEdate(frm){
	if(frm.use_edate.checked){
		$('#fg_use_edate_start').attr('disabled',false);
		$('#fg_use_edate_end').attr('disabled',false);	 
	}else{
		$('#fg_use_edate_start').attr('disabled',true);
		$('#fg_use_edate_end').attr('disabled',true);
	}
}
 
 

</script>";


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("사은품행사 리스트", "프로모션(마케팅) > 사은품관리 > 사은품행사 리스트")."</td>
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
								<option value='freegift_event_title' ".CompareReturnValue("freegift_event_title",$search_type,"selected")." style='vertical-align:middle;'>사은품 행사명</option> 
						  </select>
						  <input type=text name='search_text' class='textbox' value='".$search_text."' style='height:22px;width:30%' >
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
					  <td class='search_box_item' colspan='3'>
					  <input type=radio name='disp' value='' id='disp_a'  ".CompareReturnValue("",$disp,"checked")."><label for='disp_a'>전체</label>
					  <input type=radio name='disp' value='1' id='disp_y'  ".CompareReturnValue("1",$disp,"checked")."><label for='disp_y'>사용</label>
					  <input type=radio name='disp' value='0' id='disp_n' ".CompareReturnValue("0",$disp,"checked")."><label for='disp_n'>미사용</label>
					  </td>
					</tr>
					<tr >
					  <td class='input_box_title' >  <b>매출조건설정</b></td>
					  <td class='input_box_item' colspan=3>";
					  $mstring .= "<input type='radio' name='freegift_condition' id='freegift_condition_' value='' ".CompareReturnValue("",$freegift_condition,"checked")." validation=true title='매출조건 설정'> <label for='freegift_condition_' >전체</label> ";
					  foreach($_FREEGIFT_CONDITION as $key => $value){
						$mstring .= "<input type='radio' name='freegift_condition' id='freegift_condition_".$key."' value='".$key."' ".CompareReturnValue($key,$freegift_condition,"checked")." validation=true title='매출조건 설정'> <label for='freegift_condition_".$key."' >".$value."</label> ";
					  }
					  $mstring .= " 
					  </td>
					</tr>
					<tr>
						<td class='input_box_title' nowrap  >회원조건</td>
						<td colspan='3' class='search_box_item' >
							    <input type='checkbox' class='textbox' name='member_target[]' id='member_target_a' size=50 value='A' style='border:0px;' ".CompareReturnValue('A',$member_target,"checked")." /><label for='member_target_a'>전체</label>
								<input type='checkbox' class='textbox' name='member_target[]' id='member_target_g' size=50 value='G' style='border:0px;' ".CompareReturnValue('G',$member_target,"checked")." /><label for='member_target_g'>회원 그룹별</label>
								<input type='checkbox' class='textbox' name='member_target[]' id='member_target_m' size=50 value='M' style='border:0px;' ".CompareReturnValue('M',$member_target,"checked")." /><label for='member_target_m'>개별회원별</label> 
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
								<TD width=5% nowrap><input type=text class='textbox' name='fg_use_sdate_start' id='fg_use_sdate_start' value='$fg_use_sdate_start' style='width:70px;text-align:center;' ".($use_sdate ? "":"disabled")."></TD>
								<TD width=1% align=center> ~ </TD>
								<TD width=5% nowrap><input type=text class='textbox' name='fg_use_sdate_end' id='fg_use_sdate_end' value='$fg_use_sdate_end'  style='width:70px;text-align:center;' ".($use_sdate ? "":"disabled")."></TD>
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
								<TD width=5% nowrap><input type=text class='textbox' name='fg_use_edate_start' id='fg_use_edate_start' value='$fg_use_edate_start'  style='width:70px;text-align:center;' ".($use_edate ? "":"disabled")."></TD>
								<TD width=1% align=center> ~ </TD>
								<TD width=5% nowrap><input type=text class='textbox' name='fg_use_edate_end' id='fg_use_edate_end' value='$fg_use_edate_end'  style='width:70px;text-align:center;' ".($use_edate ? "":"disabled")."></TD>
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

$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>사은품행사 리스트</b></td></tr></table>", $help_text,220);

$Contents = $mstring.$help_text;


$P = new LayOut();
$P->addScript = "".$Script;
$P->OnloadFunction = "";
$P->Navigation = "프로모션(마케팅) > 사은품관리 > 사은품행사 리스트";
$P->title = "사은품행사 리스트";
$P->strLeftMenu = promotion_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintSearchTextList(){
	global $db, $mdb, $page, $nset, $search_type;
	global $auth_delete_msg, $auth_excel_msg, $admininfo;
	global $_FREEGIFT_CONDITION;


	$where = " where fg_ix <> '0' ";
	


	if($_GET["disp"] != ""){
		$where .= " and fg.disp =  '".$_GET["disp"]."' ";
	}

	if($_GET["search_text"] != "" && $_GET["search_type"] != ""){
		$where .= " and ".$_GET["search_type"]." LIKE  '%".$_GET["search_text"]."%' ";
	}
 
	if($_GET["fg_use_sdate_start"] != "" && $_GET["fg_use_sdate_end"] != ""){
		$unix_timestamp_start_sdate = mktime(0,0,0,substr($_GET["fg_use_sdate_start"],4,2),substr($_GET["fg_use_sdate_start"],6,2),substr($_GET["fg_use_sdate_start"],0,4));
		$unix_timestamp_start_edate = mktime(23,59,59,substr($_GET["fg_use_sdate_end"],4,2),substr($_GET["fg_use_sdate_end"],6,2),substr($_GET["fg_use_sdate_end"],0,4));

		$where .= " and  fg_use_sdate between  ".$unix_timestamp_start_sdate." and ".$unix_timestamp_start_edate." ";
	}
 

	if($_GET["fg_use_edate_start"] != "" && $_GET["fg_use_edate_end"] != ""){
		$unix_timestamp_end_sdate = mktime(0,0,0,substr($_GET["fg_use_edate_start"],4,2),substr($_GET["fg_use_edate_start"],6,2),substr($_GET["fg_use_edate_start"],0,4));
		$unix_timestamp_end_edate = mktime(0,0,0,substr($_GET["fg_use_edate_end"],4,2),substr($_GET["fg_use_edate_end"],6,2),substr($_GET["fg_use_edate_end"],0,4));

		$where .= " and  fg_use_edate between  ".$unix_timestamp_end_sdate." and ".$unix_timestamp_end_edate." ";
	}

	if($_GET['is_ing'] == '1'){
        $where .= " and fg_use_edate >= UNIX_TIMESTAMP(NOW())";
    }else if($_GET['is_ing'] == '0'){
        $where .= " and fg_use_edate < UNIX_TIMESTAMP(NOW())";
    }


    $member_target = $_GET['member_target'];
    $member_target_str = "";
    if(is_array($member_target)){
        for($i = 0; $i < count($member_target); $i++){
            if($member_target[$i]){
                if($member_target_str == ""){
                    $member_target_str .= "'".$member_target[$i]."'";
                }else{
                    $member_target_str .= ", '".$member_target[$i]."' ";
                }
            }
        }

        if($member_target_str != ""){
            $where .= "and member_target in ($member_target_str) ";
        }
    }else{
        if($member_target){
            $where .= "and member_target = '$member_target' ";
        }
    }


    $sql = "select fg.* from shop_freegift fg $where";

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

	if($_SERVER["QUERY_STRING"] == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$_SERVER["QUERY_STRING"]) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER["QUERY_STRING"]) ;
	}
	$str_page_bar = page_bar($total, $page, $max, $query_string,"");


	$mString = "<table cellpadding=0 cellspacing=0 border=0 width=100%  class='list_table_box'>";
	$mString .= "
	<col width='5%'>
	".($_SESSION["admin_config"][front_multiview] == "Y" ? "<col width=10%>":"")."
	<col width='*'>
	<col width='7%'>
	<col width='7%'>
	<col width='15%'>
	<col width='7%'>
	<col width='15%'>	
	<col width='10%'>
	<col width='10%'>
	<tr align=center bgcolor=#efefef height='30'>
		<td class=s_td >번호</td>
		".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td'  > 프론트전시</td>":"")."
		<td class=m_td >사은품 행사명</td>
		<td class=m_td >매출조건</td>
		<td class=m_td >회원조건</td>  
		<td class=m_td>행사기간</td>
		<td class=m_td>노출여부</td>
		<td class=m_td>진행여부</td>
		<td class=m_td>등록일자</td>
		<td class=e_td>관리</td>
		</tr>";


	if ($total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=10 align=center>등록한 사은품 행사가 존재 하지 않습니다.</td></tr>";
		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%  >";
		$mString .= "<tr bgcolor=#ffffff ><td colspan=5 align=right style='padding:10px 0px;'><a href='freegift.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a></td></tr>";

	}else{

		$sql = "select fg.* , case when fg_use_edate >= UNIX_TIMESTAMP(NOW()) then 1 else 0 end as is_end   from shop_freegift fg $where order by fg.regdate desc limit $start, $max";

		$db->query($sql);
		$fg_infos = $db->fetchall();

		for($i=0;$i < count($fg_infos);$i++){

			$no = $total - ($page - 1) * $max - $i;

			if($fg_infos[$i][member_target] == "A"){
				$member_target_str = "전체";
			}else if($fg_infos[$i][member_target] == "G"){
				$member_target_str = "그룹";
			}else if($fg_infos[$i][member_target] == "M"){
				$member_target_str = "회원";
			}else{

			}

			

			$mString = $mString."<tr height=30 bgcolor=#ffffff align=center>
			<td class='list_box_td list_bg_gray'>".$no."</td>";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$mString .= "
		    <td class='list_box_td'  >".GetDisplayDivision($fg_infos[$i][mall_ix], "text")."</td>";
}
	$mString .= "
			<td class='list_box_td '>".$fg_infos[$i][freegift_event_title]."</td>
			<td class='list_box_td '>".$_FREEGIFT_CONDITION[$fg_infos[$i][freegift_condition]]."</td>
			<td class='list_box_td '>".$member_target_str."</td>
			
			
			
			<td class='list_box_td '>".date("Y-m-d",$fg_infos[$i][fg_use_sdate])." ~ ".date("Y-m-d",$fg_infos[$i][fg_use_edate])."</td>
			<td class='list_box_td list_bg_gray'>".($fg_infos[$i][disp] == "1" ? "사용":"미사용")."</td>
			<td class='list_box_td list_bg_gray'>".($fg_infos[$i][is_end] == "1" ? "진행중":"진행완료")."</td>
			
			
			<td class='list_box_td ' style='line-height:140%;'>".$fg_infos[$i][regdate]."</td>
			<td class='list_box_td list_bg_gray' nowrap>";

			$mString .= "<a href='freegift.php?fg_ix=".$fg_infos[$i][fg_ix]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정'></a>";

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
			$mString .= "<a href=\"JavaScript:FreeGiftDelete('".$fg_infos[$i][fg_ix]."')\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle style='margin:0px 5px;' alt='삭제' title='삭제'></a>";
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
		$mString .= "<a href='freegift.php'><img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0 ></a>";
		}

		$mString .= "
					</td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}

/*
CREATE TABLE IF NOT EXISTS `shop_freegift` (
  `fg_ix` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `freegift_event_title` varchar(255) DEFAULT NULL COMMENT '텍스트명',
  `fg_use_sdate` int(11) DEFAULT NULL COMMENT '노출시작시간',
  `st_edate` int(11) DEFAULT NULL COMMENT '노출끝시간',
  `st_type` char(1) DEFAULT NULL COMMENT '1:TEXT,2:IMG',
  `st_title` varchar(100) DEFAULT NULL COMMENT '타이틀',
  `st_url` varchar(255) DEFAULT NULL COMMENT '링크',
  `disp` char(1) DEFAULT NULL COMMENT '사용여부',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`fg_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='사은품행사 리스트' AUTO_INCREMENT=1 ;


*/

?>