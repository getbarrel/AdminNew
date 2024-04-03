<?
include("../class/layout.class");

include("../logstory/class/sharedmemory.class");
$shmop = new Shared("delay_order_process_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admininfo"]["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$delay_rule = $shmop->getObjectForKey("delay_order_process_rule");
$delay_rule = unserialize(urldecode($delay_rule));

$sdb = new Database;


if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	$startDate = date("Y-m-d", $before10day);
	$endDate = date("Y-m-d");
}

if($mode!="search"){
	$orderdate=1;
}

if(!$date_type){
	$date_type="om.regdate";
}

if($orderdate){
	$where .= "and date_format(".$date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
}

$Contents = "

<table width='100%' cellpadding=0 cellspacing=0>
<tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("주문상담정보", "주문관리 > 주문상담정보 ")."</td>
</tr>";
if($mmode == "personalization"){ 
}else{
$Contents .= "
<tr>
	<td colspan=4 align=left style='padding-bottom:0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:2px 5px 2px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 주문상담 내역 미처리 실시간 현황</b> </span></b> </div>")."</td>

	<td width='20'> </td>

	<td align=left style='padding-bottom:0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:2px 5px 2px 5px;'><img src='../image/title_head.gif' align=absmiddle><b class=blk> 긴급/지연알림 현황</b> </span></b> </div>")."</td>
 </tr>
<tr>
	<td align='left' colspan=4 style='padding:0px;'> ".MemoSummary1()."</td>
	<td> </td>
	<td align='left' style='padding:0px;'> ".MemoSummary2()."</td>
</tr>";
}
$Contents .= "
</table>

    <table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
		<td style='width:100%;' valign=top colspan=3>
			<table width=100%  border=0>
				<!--tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주문정보 검색하기</b></td></tr-->
				<tr>
					<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
						<form name='searchmember'>
						<input type=hidden name='mode' value='search'>
						<input type=hidden name='mmode' value='".$mmode."'>
						<input type=hidden name='mem_ix' value='".$mem_ix."'>
						<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05' valign=top>

										<table cellpadding=2 cellspacing=1 width='100%' class='search_table_box'>
											<col width='15%' >
											<col width='35%' >
											<col width='15%' >
											<col width='35%' >
											<tr height=30>
												<th class='search_box_title'>판매처 선택 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
												<td class='search_box_item' nowrap colspan='3'>
													<table cellpadding=0 cellspacing=0 width='100%' border='0' >
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<col width='12.5%'>
														<TR height=25>";

												$Contents .= "
															<TD><input type='checkbox' name='order_from[]' id='order_from_self' value='self' ".CompareReturnValue('self',$order_from,' checked')." ><label for='order_from_self'>자체쇼핑몰</label></TD>
															<TD style='display:none;'><input type='checkbox' name='order_from[]' id='order_from_offline' value='offline' ".CompareReturnValue('offline',$order_from,' checked')." ><label for='order_from_offline'>오프라인 영업</label></TD>
															<TD style='display:none;'><input type='checkbox' name='order_from[]' id='order_from_pos' value='pos' ".CompareReturnValue('pos',$order_from,' checked')." ><label for='order_from_pos'>POS</label></TD>";
															$sdb->query("select * from sellertool_site_info where disp='1' ");
															$sell_order_from=$sdb->fetchall();
															if(count($sell_order_from) > 0){

																for($i=0;$i<count($sell_order_from);$i++){

																		if($i==5 || ($i > 5 && $i%8==5)) $Contents .= "</TR><TR>";

																		$Contents .= "<TD style='display:none;'><input type='checkbox' name='order_from[]' id='order_from_".$sell_order_from[$i][site_code]."' value='".$sell_order_from[$i][site_code]."' ".CompareReturnValue($sell_order_from[$i][site_code],$order_from,' checked')." ><label for='order_from_".$sell_order_from[$i][site_code]."'>".$sell_order_from[$i][site_name]."</label></TD>";
																}
															}else{
																$Contents .= "
																<TD></TD>
																<TD></TD>
																<TD></TD>
																<TD></TD>
																<TD></TD>";
															}

										$Contents .= "
														</TR>
													</table>
												</td>
											</tr>
											<tr height=33>
												<th class='search_box_title'>
													<select name='date_type'>
														<option value='om.regdate' ".CompareReturnValue('om.regdate',$date_type,' selected').">상담일자</option>
														<option value='om.order_date' ".CompareReturnValue('om.order_date',$date_type,' selected').">주문일자</option>
													</select>
													<input type='checkbox' name='orderdate' id='orderdate' value='1' onclick='ChangeOrderDate(document.searchmember);' ".CompareReturnValue('1',$orderdate,' checked').">
												</th>
												<td class='search_box_item'  colspan=3>
												".search_date('startDate','endDate',$startDate,$endDate)."
												</td>
											</tr>";
if($mmode != "personalization"){
$Contents .= "
											<tr height=33>
												<th class='search_box_title'>
													고객상태
												</th>
												<td class='search_box_item'>
													<input type=radio name='user_mood_state' id='user_mood_state_' value='' ".CompareReturnValue('',$user_mood_state,' checked')."><label class='helpcloud' help_width='70' help_height='15' help_html='매우불만' for='user_mood_state_'> 전체</label> 
													<input type=radio name='user_mood_state' id='user_mood_state_5' value='5' ".CompareReturnValue('5',$user_mood_state,' checked')."><label class='helpcloud' help_width='45' help_height='15' help_html='기쁨' for='user_mood_state_5'> <img src='../images/icon/mood_state_5.png' align='absmiddle' /></label> 
													<input type=radio name='user_mood_state' id='user_mood_state_4' value='4' ".CompareReturnValue('4',$user_mood_state,' checked')."><label class='helpcloud' help_width='45' help_height='15' help_html='양호' for='user_mood_state_4'> <img src='../images/icon/mood_state_4.png' align='absmiddle' /></label> 
													<input type=radio name='user_mood_state' id='user_mood_state_3' value='3' ".CompareReturnValue('3',$user_mood_state,' checked')."><label class='helpcloud' help_width='45' help_height='15' help_html='보통' for='user_mood_state_3'> <img src='../images/icon/mood_state_3.png' align='absmiddle' /></label> 
													<input type=radio name='user_mood_state' id='user_mood_state_2' value='2' ".CompareReturnValue('2',$user_mood_state,' checked')."><label class='helpcloud' help_width='45' help_height='15' help_html='불만' for='user_mood_state_2'> <img src='../images/icon/mood_state_2.png' align='absmiddle' /></label> 
													<input type=radio name='user_mood_state' id='user_mood_state_1' value='1' ".CompareReturnValue('1',$user_mood_state,' checked')."><label class='helpcloud' help_width='70' help_height='15' help_html='매우불만' for='user_mood_state_1'> <img src='../images/icon/mood_state_1.png' align='absmiddle' /></label>
												</td>
												<th class='search_box_title' >콜처리유형 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
												<td class='search_box_item'>";
													//$memo_call_type_array 는 constants.php 에 있음
													foreach($memo_call_type_array as $key => $val){
														$Contents .= "<input type='checkbox' name='call_type[]' id='call_type_".$key."' value='".$key."' ".CompareReturnValue($key,$call_type,' checked')." ><label for='call_type_".$key."'>".$val."</label>&nbsp;&nbsp;";
													}
												$Contents .= "
												</td>
											</tr>";
}
$Contents .= "
											<tr height=27>
												<th class='search_box_title' >분류 </th>
												<td class='search_box_item'>";
												
												$sql = "SELECT div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
																FROM ".TBL_BBS_MANAGE_DIV."
																where bm_ix = '1' and div_depth = 1
																group by div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
																order by view_order asc, div_depth asc,div_ix asc ";
												$sdb->query($sql);
												$bbs_divs = $sdb->fetchall();
												
												$Contents .= "
												<select name='bbs_div' onChange=\"bbsloadCategory(this,'sub_bbs_div',1)\">
													<option value=''>분류선택</option>";
										for($d=0;$d<count($bbs_divs);$d++){
											$Contents .= "<option value=".$bbs_divs[$d][div_ix]." ".( $bbs_divs[$d][div_ix] == $_GET["bbs_div"] ? "selected" : "").">".$bbs_divs[$d][div_name]."</option>";
										}
												$Contents .= "
												</select>";

												if($bbs_div){
													$sql = "SELECT div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
																	FROM ".TBL_BBS_MANAGE_DIV."
																	where bm_ix = '1' and div_depth = 2 and parent_div_ix = '$bbs_div'
																	group by div_ix,bm_ix,parent_div_ix,div_name,div_depth,view_order,disp,regdate
																	order by view_order asc, div_depth asc,div_ix asc ";
													$sdb->query($sql);
													$sub_bbs_divs = $sdb->fetchall();
												}

												$Contents .= "
												<span id='sub_cate_table' ".(count($sub_bbs_divs)>0 ? "" :"style='display:none;'").">
													<select name='sub_bbs_div'>
														<option value=''>서브분류선택</option>";
											for($d=0;$d<count($sub_bbs_divs);$d++){
												$Contents .= "<option value=".$sub_bbs_divs[$d][div_ix]." ".( $sub_bbs_divs[$d][div_ix] == $_GET["sub_bbs_div"] ? "selected" : "").">".$sub_bbs_divs[$d][div_name]."</option>";
											}
						$Contents .= "
													</select>
												</span>

												</td>
												<th class='search_box_title' >처리상태 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
												<td class='search_box_item'>";
													//$memo_state_array 는 constants.php 에 있음
													foreach($memo_state_array as $key => $val){
														$Contents .= "<input type='checkbox' name='memo_state[]' id='memo_state_".$key."' value='".$key."' ".CompareReturnValue($key,$memo_state,' checked')." ><label for='memo_state_".$key."'>".$val."</label>&nbsp;&nbsp;";
													}
												$Contents .= "
												</td>
											</tr>
											<tr height=27>
												<th class='search_box_title' >조건검색 </th>
												<td class='search_box_item'>
													<select name=charger_search_type>
														<option value='counselor' ".CompareReturnValue("counselor",$charger_search_type,"selected").">상담자</option>
														<option value='charger' ".CompareReturnValue("charger",$charger_search_type,"selected").">담당자</option>
													</select>
													<input type=text name='charger_search_text' class='textbox' value='".$charger_search_text."' style='width:30%; vertical-align:top; ' >
												</td>
												<th class='search_box_title' >긴급/지연 <input type='checkbox' onclick=\"linecheck($(this));\" /></th>
												<td class='search_box_item'>
													<input type='checkbox' name='alarm[]' id='alarm_d' value='D' ".CompareReturnValue("D",$alarm,' checked')." ><label for='alarm_d'>긴급</label>&nbsp;&nbsp;
													<input type='checkbox' name='alarm[]' id='alarm_w' value='W' ".CompareReturnValue("W",$alarm,' checked')." ><label for='alarm_w'>지연(지연알림설정)</label>
												</td>
											</tr>
											<tr height=27>
												<th class='search_box_title' >조건검색 </th>
												<td class='search_box_item' colspan='3'>
													<select name=search_type>";
if($mmode != "personalization"){ 
$Contents .= "
														<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자이름+주문자ID</option>";
}
$Contents .= "
														<option value='om.oid' ".CompareReturnValue("om.oid",$search_type,"selected").">주문번호</option>
														<option value='memo' ".CompareReturnValue("memo",$search_type,"selected").">상담내용</option>
													</select>
													<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:30%; vertical-align:top; ' >
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
						</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr >
		<td colspan=3 align=center style='padding:10px; '>
			<img src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='cursor:pointer;' onclick=\"$('form[name=searchmember]').submit();\" >
		</td>
	</tr>
	
	<tr >
		<td colspan=3 align=center style='padding-top:10px;' id='design_history_area'>
			".PrintOrderMemo()."
		</td>
	</tr>
	</table>



";
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문상담내역 목록입니다. </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >원하는 주문상담내역을 클릭하신다음 주문내역을 확인하실수 있습니다</td></tr>
</table>
";
*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$help_text = HelpBox("주문상담내역", $help_text);
$Contents .= $help_text;


$Contents = $Contents."
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

$Script = "
<script language='javascript' >

function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#start_datepicker').addClass('point_color');
		$('#end_datepicker').addClass('point_color');
	}else{
		$('#start_datepicker').removeClass('point_color');
		$('#end_datepicker').removeClass('point_color');
	}
}

function bbsloadCategory(sel,target, depth) {

	var trigger = sel.options[sel.selectedIndex].value;	// 첫번째 selectbox의 선택된 텍스트
	var form = sel.form.name;
	window.frames['iframe_act'].location.href='/bbs/category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

}

function memoDelete(oid, om_ix){
	if(confirm(language_data['orders_memo.php']['A'][language])){//해당 상담내역을 정말로 삭제 하시겠습니까?
		window.frames['iframe_act'].location.href='orders_memo.act.php?act=memo_delete&oid='+oid+'&om_ix='+om_ix;
		//document.getElementById('iframe_act').src='orders_memo.act.php?act=memo_delete&oid='+oid+'&om_ix='+om_ix;//kbk
	}

}

</script>
";

if($mmode == "personalization"){
	$P = new ManagePopLayOut();
	$P->addScript = "<script language='javascript' src='orders.js'></script>\n".$Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = order_menu();
	$P->Navigation = "주문관리 > 주문상담내역";
	$P->title =  "주문상담내역";
	$P->NaviTitle =  "주문상담내역";
	$P->strContents =  $Contents;
	$P->layout_display = false;
	$P->view_type = "personalization";
	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->strLeftMenu = order_menu();
	$P->OnloadFunction = "";
	$P->addScript = "<script language='javascript' src='orders.js'></script>\n".$Script;
	$P->Navigation = "주문관리 > 주문상담내역";
	$P->title = "주문상담내역";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}






function PrintOrderMemo(){
	global $admininfo, $page, $nset, $QUERY_STRING,$search_type,$search_text,$auth_delete_msg,$memo_state_array,$memo_call_type_array,$startDate,$endDate;
	global $order_from,$date_type,$charger_search_type,$charger_search_text,$memo_state,$alarm,$delay_rule,$bbs_div,$sub_bbs_div,$user_mood_state,$call_type;
	global $mmode, $mem_ix,$orderdate;

	$mdb = new Database;
	$sdb = new Database;

	$where = " where om.om_ix != 0 ";

	if($mmode == "personalization"){
		$where .= " and om.ucode = '".$mem_ix."' ";
	}

	if($user_mood_state!=""){
		$where .= " and om.user_mood_state = '$user_mood_state' ";
	}

	if($sub_bbs_div!=""){
		$where .= " and om.memo_div = '$sub_bbs_div' ";
	}elseif($bbs_div!=""){
		$where .= " and ( om.memo_div in (select div_ix from bbs_manage_div where parent_div_ix = '$bbs_div') OR om.memo_div ='".$bbs_div."' ) ";
	}

	if($charger_search_type != "" && $charger_search_text != ""){
		$where .= " and $charger_search_type LIKE '%$charger_search_text%' ";
	}
	
	if($search_type && $search_text){
		if($search_type == "combi_name"){
			$where .= "and (bname LIKE '%".trim($search_text)."%' or buserid LIKE '%".trim($search_text)."%') ";
		}else{
			$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
		}
	}

	if(is_array($order_from)){
		for($i=0;$i < count($order_from);$i++){
			if($order_from[$i] != ""){
				if($order_from_str == ""){
					$order_from_str .= "'".$order_from[$i]."'";
				}else{
					$order_from_str .= ",'".$order_from[$i]."' ";
				}
			}
		}

		if($order_from_str != ""){
			$where .= "and om.order_from in ($order_from_str) ";
		}
	}else{
		if($order_from){
			$where .= "and om.order_from = '$order_from' ";
		}
	}
	
	if(is_array($alarm)){
		for($i=0;$i < count($alarm);$i++){
			if($alarm[$i] == "D"){
				$where .= " and om.urgency_yn = 'Y' ";
			}elseif($alarm[$i] == "W" && $delay_rule["omr_omc_yn"]=="Y"){
				$where .= " and DATE_ADD(om.regdate, INTERVAL '".$delay_rule["omr_omc_day"]."' DAY) < NOW() ";
			}
		}
	}else{
		if($alarm == "D"){
			$where .= " and om.urgency_yn = 'Y' ";
		}elseif($alarm == "W" && $delay_rule["omr_omc_yn"]=="Y"){
			$where .= " and DATE_ADD(om.regdate, INTERVAL '".$delay_rule["omr_omc_day"]."' DAY) < NOW() ";
		}
	}

	if(is_array($memo_state)){
		for($i=0;$i < count($memo_state);$i++){
			if($memo_state[$i] != ""){
				if($memo_state_str == ""){
					$memo_state_str .= "'".$memo_state[$i]."'";
				}else{
					$memo_state_str .= ",'".$memo_state[$i]."' ";
				}
			}
		}

		if($memo_state_str != ""){
			$where .= "and om.memo_state in ($memo_state_str) ";
		}
	}else{
		if($memo_state){
			$where .= "and om.memo_state = '$memo_state' ";
		}
	}

	if(is_array($call_type)){
		for($i=0;$i < count($call_type);$i++){
			if($call_type[$i] != ""){
				if($call_type_str == ""){
					$call_type_str .= "'".$call_type[$i]."'";
				}else{
					$call_type_str .= ",'".$call_type[$i]."' ";
				}
			}
		}

		if($call_type_str != ""){
			$where .= "and om.call_type in ($call_type_str) ";
		}
	}else{
		if($call_type){
			$where .= "and om.call_type = '$call_type' ";
		}
	}

	if($date_type==""){
		$date_type = "om.regdate";
	}

	if($orderdate && $startDate && $endDate){
		$where .= " and  date_format($date_type, '%Y%m%d') between '".str_replace('-','',$startDate)."' and '".str_replace('-','',$endDate)."' ";
	}


	$sql = "select 
				count(*) as total 
			from 
				shop_order_memo om 
			left join 
				bbs_manage_div d on (om.memo_div=d.div_ix)
			left join
				shop_order o on (om.oid=o.oid)
			$where ";

	//echo nl2br($sql);
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];


	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>
				<col width='3%'>
				<col width='10%'>
				<col width='10%'>
				<col width='*'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='4%'>";
if($mmode != "personalization"){ 
	$mString .= "
				<col width='5%'>";
}
$mString .= "
				<tr align=center bgcolor=#efefef height=25>
					<td class='s_td' rowspan='2'>알림</td>
					<td class='m_td' >판매처</td>
					<td class='m_td' rowspan='2'>고객상태</td>
					<td class=m_td rowspan='2'>상담내용</td>
					<td class='m_td'>접수자</td>
					<td class='m_td'>접수일</td>
					<td class=m_td rowspan='2'>콜처리유형</td>
					<td class=m_td rowspan='2'>전화응대필요</td>
					<td class=m_td rowspan='2'>총 처리시간</td>";
if($mmode != "personalization"){ 
	$mString .= "
					<td class=e_td rowspan='2'>관리</td>";
}
	$mString .= "
				</tr>
				<tr align=center bgcolor=#efefef height=25>
					<td class='m_td'>상담분류</td>
					<td class='m_td'>처리담당자</td>
					<td class=m_td>처리상태</td>
				</tr>";

	if ($total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=9 align=center> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</td></tr>";
	}else{

		if($mdb->dbms_type == "oracle"){
			$sql = "select 
				om.*, o.bname, o.buserid,
				case when d.div_depth = 2 then (select d2.div_name from bbs_manage_div d2 where d2.div_ix = d.parent_div_ix)||'>'||d.div_name else d.div_name end as div_name,d.parent_div_ix,d.div_depth
			from
				shop_order_memo om left join bbs_manage_div d on (om.memo_div=d.div_ix) left join shop_order o on (om.oid=o.oid)
			$where 
			order by om.regdate desc   
			limit $start, $max";
		}else{
			$sql = "select 
				om.*, o.bname, o.buserid,
				case when d.div_depth = 2 then concat((select d2.div_name from bbs_manage_div d2 where d2.div_ix = d.parent_div_ix),'>',d.div_name) else d.div_name end as div_name,d.parent_div_ix,d.div_depth
			from
				shop_order_memo om left join bbs_manage_div d on (om.memo_div=d.div_ix) left join shop_order o on (om.oid=o.oid)
			$where 
			order by om.regdate desc   
			limit $start, $max";
		}
		//echo nl2br($sql);
		$mdb->query($sql);

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			
			$reg_datetime=explode(" ",$mdb->dt[regdate]);
			$reg_date=explode("-",$reg_datetime[0]);
			$reg_time=explode(":",$reg_datetime[1]);
			$reg_mktime=mktime($reg_time[0],$reg_time[1],$reg_time[2],$reg_date[1],$reg_date[2],$reg_date[0]);
			
			$complete_datetime=explode(" ",$mdb->dt[complete_date]);
			$complete_date=explode("-",$complete_datetime[0]);
			$complete_time=explode(":",$complete_datetime[1]);
			$complete_mktime=mktime($complete_time[0],$complete_time[1],$complete_time[2],$complete_date[1],$complete_date[2],$complete_date[0]);

			if($mdb->dt[urgency_yn]=="Y")
				$alarm_img_str="<img src='../images/icon/alarm_danger.gif'>";
			elseif($delay_rule["omr_omc_yn"]=="Y"&&($delay_rule["omr_omc_day"]!="" && $reg_mktime < (time()-(86400*$delay_rule["omr_omc_day"]))))
				$alarm_img_str="<img src='../images/icon/alarm_warning.gif'>";
			else										
				$alarm_img_str="";
			
			if($mdb->dt[call_action_yn]=="Y")
				$call_action="필요(".($mdb->dt[call_action_state]=="1"?"완료":"대기중").")".($mdb->dt[call_action_date]!="" ? "<br/>".$mdb->dt[call_action_date]:"").($mdb->dt[call_action_time]!="" ? "<br/>".$mdb->dt[call_action_time]:"");
			else
				$call_action="불필요";
			
			$sql="select 
						'counselor' as mem_type,
						ccd.mem_code,
						scd.dp_name,
						cd.duty_name,
						AES_DECRYPT(UNHEX(ccd.com_tel),'".$sdb->ase_encrypt_key."') as com_tel,
						AES_DECRYPT(UNHEX(ccd.mail),'".$sdb->ase_encrypt_key."') as mail
					from
						common_member_detail ccd 
						left join ".TBL_SHOP_COMPANY_DEPARTMENT." scd on (ccd.department = scd.dp_ix) 
						left join ".TBL_SHOP_COMPANY_DUTY." cd on (ccd.duty = cd.cu_ix) 
					where ccd.code='".$mdb->dt[counselor_ix]."' and code!=''
					union
					select 
						'charger' as mem_type,
						ccd.mem_code,
						scd.dp_name,
						cd.duty_name,
						AES_DECRYPT(UNHEX(ccd.com_tel),'".$sdb->ase_encrypt_key."') as com_tel,
						AES_DECRYPT(UNHEX(ccd.mail),'".$sdb->ase_encrypt_key."') as mail
					from
						common_member_detail ccd 
						left join ".TBL_SHOP_COMPANY_DEPARTMENT." scd on (ccd.department = scd.dp_ix) 
						left join ".TBL_SHOP_COMPANY_DUTY." cd on (ccd.duty = cd.cu_ix) 
					where ccd.code='".$mdb->dt[charger_ix]."' and code!='' ";
			$sdb->query($sql);
			
			$counselor_info=$sdb->fetchall("object");
			
			$counselor_bool=false;
			$charger_bool=false;
			if(is_array($counselor_info)){
				foreach($counselor_info as $ci){
					$com_tel=explode("-",$mdb->dt[com_tel]);

					if($ci["mem_type"]=="counselor"){
						$counselor_str="<span style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='직원(코드) : ".$mdb->dt[counselor]." (".$ci["mem_code"].") <br/>부서 : ".$mdb->dt[dp_name]."<br/>직책 : ".$mdb->dt[duty_name]." <br/>회사내선 : ".$com_tel[3]." <br/>이메일 : ".$mdb->dt["mail"]."' />".$mdb->dt[counselor]."</span>";
						$counselor_bool=true;
					}
	
					if($ci["mem_type"]=="charger"){
						$charger_str="<span style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='직원(코드) : ".$mdb->dt[charger]." (".$ci["mem_code"].") <br/>부서 : ".$mdb->dt[dp_name]."<br/>직책 : ".$mdb->dt[duty_name]." <br/>회사내선 : ".$com_tel[3]." <br/>이메일 : ".$mdb->dt["mail"]."' />".$mdb->dt[charger]."</span>";
						$charger_bool=true;
					}

				}
			}
			
			if(!$counselor_bool)		$counselor_str=$mdb->dt[counselor];
			if(!$charger_bool)			$charger_str=$mdb->dt[charger];

			$mString .= "<tr height=35 bgcolor=#ffffff align=center>
			<td class='list_box_td' rowspan='2'>".$alarm_img_str."</td>
			<td class='list_box_td list_bg_gray'>".getOrderFromName($mdb->dt[order_from])."</td>
			<td class='list_box_td' rowspan='2'>".wel_masking_seLen($mdb->dt[bname],1,1)."".( $mdb->dt[buserid] ? "<br/>(<span class='small'>".$mdb->dt[buserid]."</span>)" : "")." <img src='../images/icon/mood_state_".$mdb->dt[user_mood_state].".png' align='absmiddle'/></td>
			<td class='list_box_td point' align=left  style='padding:10px;font-weight:normal;' rowspan='2'>
				<table width=100% >
					<tr>
						<td align=left class='point'> <a href='orders.edit.php?oid=".$mdb->dt[oid]."#order_memo' target='_blank'>".$mdb->dt[oid]."</a> </td>
					</tr>
					<tr>
						<td align=left class='point' style='font-weight:normal;line-height:140%;'>".nl2br($mdb->dt[memo])."</td>
					</tr>
				</table>
			</td>
			<td class='list_box_td'>".$counselor_str."</td>
			<td class='list_box_td'>".str_replace(" ","<br/>",$mdb->dt[regdate])."</td>
			<td class='list_box_td' rowspan='2'>".$memo_call_type_array[$mdb->dt[call_type]]."</td>
			<td class='list_box_td' rowspan='2'>".$call_action."</td>
			<td class='list_box_td list_bg_gray' rowspan='2'>".($mdb->dt[memo_state]=="4"? round(($complete_mktime-$reg_mktime)/86400,1)."일" : "-")."</td>";
			if($mmode != "personalization"){ 
				$mString .= "
				<td class='list_box_td' rowspan='2'>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$mString.="
					<a href=\"JavaScript:memoDelete('".$mdb->dt[oid]."','".$mdb->dt[om_ix]."')\" ><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}else{
					$mString.="
					<a href=\"".$auth_delete_msg."\"><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}
				$mString.="
				</td>";
			}

			$mString.="
			</tr>
			";

			$mString .= "
			<tr height=35 bgcolor=#ffffff align=center>
				<td class='list_box_td list_bg_gray'>".$mdb->dt[div_name]."</td>
				<td class='list_box_td'>".$charger_str."</td>
				<td class='list_box_td'>".$memo_state_array[$mdb->dt[memo_state]]."<br/>".str_replace(" ","<br/>",$mdb->dt[memo_state_change_date])."</td>
			</tr>
			";

		}

		//$mString .= "<tr bgcolor=#ffffff height=40><td colspan=8 align=left><a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../image/bt_all_del.gif' border=0 align=absmiddle ></a></td></tr>";
	}

	if($QUERY_STRING == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	}

	$mString .= "</table>
					<table width=100% >
						<tr height=40>
							<td align=right class='point'>".page_bar($total, $page, $max,$query_string,"")."</td>
						</tr>
					</table>";

	return $mString;
}


function MemoSummary1(){

	$mdb = new Database;

	$vdate = date("Y-m-d", time());
	$today = date("Y-m-d", time());
	$firstday = date("Y-m-d", time()-84600*date("w"));
	$lastday = date("Y-m-d", time()+84600*(6-date("w")));

	$sql = "Select 
			IFNULL(sum(case when memo_state != '4' then 1 else 0 end),0) as no_complete_cnt,
			IFNULL(sum(case when memo_state = '1'  then 1 else 0 end),0) as receipt_cnt,
			IFNULL(sum(case when memo_state = '2'  then 1 else 0 end),0) as ing_cnt,
			IFNULL(sum(case when memo_state = '3'  then 1 else 0 end),0) as delay_cnt
			from shop_order_memo ";

	$mdb->query($sql);
	$mdb->fetch();
	$data = $mdb->dt;

	$mstring = "<table width=100% cellpadding=0 cellspacing=0  border=0 > 
				
				<tr>
					<td align='left'  width='100%' valign=top style='padding-top:5px;'>
					<table cellpadding=0 cellspacing=1 width='100%' border='0' bgcolor=silver class='list_table_box'>
						<col width='25%'>
						<col width='25%'>
						<col width='25%'>
						<col width='25%'>
						<tr height=30  bgcolor='#ffffff'>
							<th bgcolor='#efefef' >미처리합계 </th>
							<th bgcolor='#efefef' >접수완료 </th>
							<th bgcolor='#efefef' >처리중</th>
							<th bgcolor='#efefef' >처리지연</th>
						</tr>
						<tr height=30  bgcolor='#ffffff' align=center>
							<td align='center' bgcolor='#efefef' style='font-weight:bold;'>".number_format($data[no_complete_cnt])."</td>
							<td align='center'>".number_format($data[receipt_cnt])."</td>
							<td align='center'>".number_format($data[ing_cnt])."</td>
							<td align='center'>".number_format($data[delay_cnt])."</td>
						</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td style='padding:5px 0px;text-align:right;'>.</td>
				</tr>
			</table>";
	return $mstring;
}


function MemoSummary2(){
	global $delay_rule;

	$mdb = new Database;
	


	$sql = "Select 
		IFNULL(sum(case when memo_state != '4' and urgency_yn = 'Y' then 1 else 0 end),0) as urgency_cnt,
		IFNULL(sum(case when memo_state != '4' and DATE_ADD(regdate, INTERVAL '".$delay_rule["omr_omc_day"]."' DAY) < NOW() then 1 else 0 end),0) as delay_cnt
		from shop_order_memo ";
	$mdb->query($sql);
	$mdb->fetch();
	$data = $mdb->dt;

	$mstring = "<table width=100% cellpadding=0 cellspacing=0  border=0> 
				
				<tr>
					<td align='left'  width='100%' valign=top style='padding-top:5px;'>
					<table cellpadding=0 cellspacing=1 width='100%' border='0' bgcolor=silver class='list_table_box'>
						<col width='50%'>
						<col width='50%'>
						<tr height=30  bgcolor='#ffffff'>
							<th bgcolor='#efefef' >긴급처리건 <img src='../images/icon/alarm_danger.gif'></th>
							<th bgcolor='#efefef' >지연건 <img src='../images/icon/alarm_warning.gif'></th>
						</tr>
						<tr height=30  bgcolor='#ffffff' align=center>
							<td align='center' style='font-weight:bold;'>".number_format($data[urgency_cnt])."</td>
							<td align='center'>".($delay_rule["omr_omc_yn"]=="Y" ? number_format($data[delay_cnt]) : "0")."</td>
						</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td style='padding:5px 0px;text-align:right;'>.</td>
				</tr>
			</table>";
	return $mstring;
}

?>
