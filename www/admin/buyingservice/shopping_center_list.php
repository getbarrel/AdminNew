<?
include("../class/layout.class");
include("buying.lib.php");

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


if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;



$where = " where 1=1  ";

if($ca_ix != ""){
	$where .= " and sc.ca_ix = '".$ca_ix."'  ";
}

if($sdate && $edate && $check_search_date){
	$where .= " and DATE_FORMAT(".$date_type.",'%Y%m%d') between ".str_replace("-","",$sdate)." and ".str_replace("-","",$edate)." ";
}

if($search_text && $search_type){
	if($search_type=="sc_name"){
		$where .= " and (sc_name_korea LIKE '%".$search_text."%' OR sc_name_english LIKE '%".$search_text."%' OR sc_name_chinese LIKE '%".$search_text."%') ";
	}else{
		$where .= " and ".$search_type." LIKE '%".$search_text."%' ";
	}
}

if($disp != ""){
	$where .= " and sc.disp = '".$disp."'  ";
}

if($shopping_center_approach_list){
	$where .= " and ((UNIX_TIMESTAMP(sc_end_date)-UNIX_TIMESTAMP(NOW()))/86400) < 60 ";
}

$db->query("SELECT count(*) as total FROM buyingservice_shopping_center sc left join common_user cu on (sc.sc_charger_ix = cu.code) left join buyingservice_commercial_area ca on (ca.ca_ix=sc.ca_ix) $where ");
$db->fetch();
$total = $db->dt[total];

$sql = "SELECT 
	sc.*,
	ca.ca_country,ca.ca_name_korea,ca.ca_name_english,ca.ca_name_chinese,
	cu.id,AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
FROM buyingservice_shopping_center sc 
left join common_user cu on (sc.sc_charger_ix = cu.code) 
left join common_member_detail cmd on (cu.code = cmd.code)
left join buyingservice_commercial_area ca on (ca.ca_ix=sc.ca_ix)  
$where  limit $start , $max ";
$db->query($sql);

$str_page_bar = page_bar($total, $page,$max, "&ca_country=$ca_country&sc_start_date=$sc_start_date&sc_end_date=$sc_end_date&check_search_date=$check_search_date&date_type=$date_type&search_text=$search_text&search_type=$search_type&disp=$disp&max=$max&sdate=$sdate&edate=$edate","");

$mstring = "<form name='search_form' method='get' >
<table width='100%' cellpadding=0 cellspacing=0>
	<tr>
	    <td align='left' colspan=6 style='padding-bottom:0px;'> ".GetTitleNavigation("상가리스트", "상가관리 > 상가리스트")."</td>
	</tr>
	<tr >
		<td colspan=2>
			<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
				<col width='150' >
				<col width='*' >
				<col width='150' >
				<col width='*' >
				<tr height=27>
				<td class='search_box_title' bgcolor='#efefef' align=center>
					<select name='date_type'>
						<option value='sc_end_date' ".CompareReturnValue('sc_end_date',$date_type,' selected').">종료일</option>
						<option value='sc_start_date' ".CompareReturnValue('sc_start_date',$date_type,' selected').">시작일</option>
					</select>
					<input type='checkbox' name='check_search_date' id='check_search_date' value='1' onclick='ChangeRegistDate(document.search_form);'".CompareReturnValue("1",$check_search_date,"checked").">
				</td>
				  <td class='search_box_item' align=left style='padding-left:5px;' colspan=3>
					<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
						<col width=70>
						<col width=20>
						<col width=70>
						<col width=*>
						<tr>
							<TD nowrap>
							<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
							</TD>
							<TD align=center> ~ </TD>
							<TD nowrap>
							<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
							</TD>
							<TD style='padding:0px 10px'>
								<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
								<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
								<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
								<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
								<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
								<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
								<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
							</TD>
						</tr>
					</table>
				  </td>
				</tr>
				<tr>
					<td class='input_box_title'>  <b>상가 국가</b>  </td>
					<td class='input_box_item' >
						".getCommercialCountry($ca_country,"select","onchange=\"window.frames['act'].location.href='./shopping_commercial_area.load.php?trigger='+this.value+'&target=ca_ix&form=search_form'\"")." ".getCommercialAreaInfo($ca_country,$ca_ix)."
					</td>
					<td class='input_box_title'> <b>사용여부</b>   </td>
						<td class='input_box_item'>
						<input type='radio' name='disp' id='disp_' value='' ".($disp == ""  ? "checked":"")." ><label for='disp_'>전체</label>
						<input type='radio' name='disp' id='disp_1' value='1' ".($disp == "1"  ? "checked":"")." ><label for='disp_1'>사용</label>
						<input type='radio' name='disp' id='disp_0' value='0' ".($disp == "0"  ? "checked":"")." ><label for='disp_0'>미사용</label>
						</td>
				</tr>
				<tr>
					<td class='input_box_title'>  <b>검색어</b>  </td>
					<td class='input_box_item' valign='top' style='padding-right:5px;padding-top:7px;'>
						<table cellpadding=0 cellspacing=0>
							<tr>
								<td>
									<select name='search_type'  style=\"font-size:12px;height:22px;\">
										<option value=''>전체보기</option>
										<option value='sc_name' ".CompareReturnValue('sc_name',$search_type,' selected').">상가명</option>
										<option value='sc_code' ".CompareReturnValue('sc_code',$search_type,' selected').">상가코드</option>
									</select>
								</td>
								<td style='padding-left:5px;'><INPUT id=search_texts  class='textbox' value='' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'><br>
								<DIV id=popup style='DISPLAY: none; WIDTH: 160px; POSITION: absolute; HEIGHT: 150px; BACKGROUND-COLOR: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
									<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef>
										<tr height=20>
											<td width=100%  style='padding:0 0 0 5'>
												<table width=100% cellpadding=0 cellspacing=0 border=0>
													<tr>
														<td class='p11 ls1'>검색어 자동완성</td>
														<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:hand;padding:0 10 0 0' align=right>닫기</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr height=100% >
											<td valign=top bgColor=#efefef style='padding:0 6 5 6' colspan=2>
												<table width=100% height=100% bgcolor=#ffffff>
													<tr>
														<td valign=top >
														<div style='POSITION: absolute; overflow-y:auto;HEIGHT: 120px;' id='search_data_area'>
															<TABLE id=search_table style='table-layout:fixed;'  width=100% cellSpacing=0 cellPadding=1 bgColor=#ffffff border=0>
															<TBODY id=search_table_body></TBODY>
															</TABLE>
														<div>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									</DIV>
								</td>
								<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'></span></td>
							</tr>
						</table>
					</td>
					<td class='input_box_title'><b>목록갯수</b></td>
					<td class='input_box_item'>
						<select name=max style=\"font-size:12px;height: 20px; width: 50px;\" align=absmiddle>
							<option value='5' ".CompareReturnValue(5,$max).">5</option>
							<option value='10' ".CompareReturnValue(10,$max).">10</option>
							<option value='20' ".CompareReturnValue(20,$max).">20</option>
							<option value='50' ".CompareReturnValue(50,$max).">50</option>
							<option value='100' ".CompareReturnValue(100,$max).">100</option>
						</select> <span class='small'>한페이지에 보여질 갯수를 선택해주세요</span>
					</td>
				</tr>
			</table>
					
		</td>
	</tr>
	<tr >
		<td colspan=8 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>				
	</tr>
</table>
</form>
<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
	<col width=5%>
	<col width='*'>
	<col width='8%'>
	<col width=14%>
	<col width=6%>
	<col width=14%>
	<col width=8%>
	<col width=6%>
	<col width=13%>
	<col width=8%>
	<col width=8%>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'>순서</td>
		<td class='m_td'>등록일/수정일</td>
		<td class='m_td'>상권국가</td>
		<td class='m_td'>상권명</td>
		<td class='m_td'>상가코드</td>
		<td class='m_td'>상가명</td>
		<td class='m_td'>상가 권한자</td>
		<td class='m_td'>인센티브</td>
		<td class='m_td'>권한일/잔여일</td>
		<td class='m_td'>사용여부</td>
		<td class='e_td'>관리</td>
		</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

		$mstring .="<tr height=32 align=center>
					<td class='list_box_td'>".($i+1)."</td>
					<td class='list_box_td list_bg_gray'>".substr($db->dt[regdate],0,10)." <br/>/ ".substr($db->dt[editdate],0,10)."</td>
					<td class='list_box_td' >".getCommercialCountry($db->dt[ca_country],"text")."</td>
					<td class='list_box_td' style='padding-left:10px; text-align:left;'>".$db->dt[ca_name_korea]." ".($db->dt[ca_name_english] ? "/ ".$db->dt[ca_name_english] :"")." ".($db->dt[ca_name_chinese] ? "/ ".$db->dt[ca_name_chinese] :"")."</td>
					<td class='list_box_td point'>".$db->dt[sc_code]."</td>
					<td class='list_box_td' style='padding-left:10px; text-align:left;'>".$db->dt[sc_name_korea]." ".($db->dt[sc_name_english] ? "/ ".$db->dt[sc_name_english] :"")." ".($db->dt[sc_name_chinese] ? "/ ".$db->dt[sc_name_chinese] :"")."</td>
					<td class='list_box_td list_bg_gray'>".$db->dt[id]." / ".$db->dt[name]."</td>
					<td class='list_box_td '>".$db->dt[sc_incentive]." %</td>
					<td class='list_box_td list_bg_gray'>".$db->dt[sc_start_date]."<br/>~".$db->dt[sc_end_date]." (".round((mktime(0,0,0,substr($db->dt[sc_end_date],5,2),substr($db->dt[sc_end_date],8,2),substr($db->dt[sc_end_date],0,4))-time())/86400)."일)</td>
					<td class='list_box_td '>".($db->dt[disp] == "1" ? "사용함":"사용안함")."</td>
					<td class='list_box_td list_bg_gray' nowrap>";
			$mstring .="
					<!--a href=\"javascript:PoPWindow3('shopping_center.add.php?mmode=pop&sc_ix=".$db->dt[sc_ix]."',900,700,'shopping_center')\"-->
					<a href='shopping_center.add.php?sc_ix=".$db->dt[sc_ix]."'>
						<img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0>
					</a>
					<!--a href='shopping_center.act.php?sc_ix=".$db->dt[sc_ix]."&act=delete'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a-->
					</td>
				</tr>";
	}
}else{
	//$mstring .=	"<table width='100%' cellpadding=0 cellspacing=0>";
	$mstring .= "
				<tr height=50><td colspan=11 align=center style='padding-top:10px;'>등록된 상가정보 관리가 없습니다.</td></tr>
				";
}
	$mstring .=	"</table>";
	$mstring .=	"<table width='100%' cellpadding=0 cellspacing=0>";
	$mstring .= "<tr hegiht=40>
						<td colspan=7>".$str_page_bar."</td>
						<td colspan=3 align=right style='padding-top:10px;'>
							<!--a href=\"javascript:PoPWindow3('shopping_center.add.php?mmode=pop',900,700,'shopping_center')\"-->
							<a href=\"./shopping_center.add.php\">
								<img src='../images/".$admininfo["language"]."/btn_admin_add.gif' border=0>
							</a>
						</td>
					</tr>";

$mstring .="</table><br>";
$Contents = $mstring;

$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >귀사에서 관리하는 상가정보 관리를 등록 관리하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상가정보 관리 정보를 수정하시고자 할때는 수정 버튼 또는 상가정보 관리명을 클릭하시면 수정하실수 있습니다</td></tr>
</table>
";


$help_text = HelpBox("<div style='padding-top:6px;'>상가리스트</div>", $help_text);
$Contents .= $help_text;

$Script = "<script type='text/javascript'>
<!--
	
	$(document).ready(function(){
		ChangeRegistDate(document.search_form);

		$(\"#start_datepicker\").datepicker({
			//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			//showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
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
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력'
		});

	})

	function select_date(FromDate,ToDate,dType) {
		var frm = document.search_form;
		if(frm.check_search_date.checked){
			$('#start_datepicker').val(FromDate);
			$('#end_datepicker').val(ToDate);
		}
	}

	function ChangeRegistDate(frm){
		if(frm.check_search_date.checked){
			frm.sdate.disabled = false;
			frm.edate.disabled = false;
		}else{
			frm.sdate.disabled = true;
			frm.edate.disabled = true;
		}
	}
//-->
</script>";

$P = new LayOut;
$P->addScript = "$Script";
$P->strLeftMenu = buyingservice_menu();
$P->Navigation = "상가관리 > 상가리스트";
$P->title = "상가리스트";
$P->strContents = $Contents;
$P->PrintLayOut();



?>