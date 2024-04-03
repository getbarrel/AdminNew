<?
include_once("../class/layout.class");
include_once("service.lib.php");
//print_r($admin_config);
if ($vFromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate = $vToYY."/".$vToMM."/".$vToDD;
	$startDate = $vFromYY.$vFromMM.$vFromDD;
	$endDate = $vToYY.$vToMM.$vToDD;
}

$db1 = new MySQL;
$odb = new MySQL;
$ddb = new MySQL;
//$title_str = getOrderStatus($type);
if(!$title_str){
	$title_str  = "서비스이용리스트";
}

$vdate = date("Ymd", time());
$today = date("Ymd", time());
$firstday = date("Ymd", time()-84600*date("w"));
$lastday = date("Ymd", time()+84600*(6-date("w")));



$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

// 검색 조건 설정 부분
if($admininfo[admin_level] == 9){
	$where = "WHERE si.si_status <> 'SR' AND si.si_status!='' and s_kind != ''  ";
} else if($admininfo[admin_level] == 8){
	$where = "WHERE si.si_status <> 'SR' AND si.si_status!='' and s_kind != ''  AND s_type='".$admininfo["company_id"]."' ";
}

if($parent_service_code) {
	$where .= "and si.s_kind = '$parent_service_code' ";
	if($service_code) {
		$where .= "and si.s_type = '$service_code' ";
	}
}

if ($bname != "")	$where .= "and si.name = '$bname' ";
if ($pname != "")	$where .= "and si.pname = '$pname' ";
if($date_type){
	if ($vFromYY != "")	$where .= "and date_format(".$date_type.",'%Y%m%d') between $startDate and $endDate ";
}

if($search_type && $search_text){
		if($search_type == "combi_name"){
			$where .= "and (si.name LIKE '%".trim($search_text)."%'  or si.pname LIKE '%".trim($search_text)."%') ";
		}else{
			$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
		}
	}
//print_r($type);
/*if(is_array($type)){
	for($i=0;$i < count($type);$i++){
		if($type[$i]){
			if($type_str == ""){
				$type_str .= "'".$type[$i]."'";
			}else{
				$type_str .= ", '".$type[$i]."' ";
			}
		}
	}

	if($type_str != ""){
		$where .= "and si.si_status in ($type_str) ";
	}
}else{
	if($type){
		$where .= "and si.si_status = '$type' ";
	}
}*/


if(is_array($gp_ix)){
	for($i=0;$i < count($gp_ix);$i++){
		if($gp_ix[$i] != ""){
			if($gp_ix_str == ""){
				$gp_ix_str .= "'".$gp_ix[$i]."'";
			}else{
				$gp_ix_str .= ", '".$gp_ix[$i]."' ";
			}
		}
	}

	if($gp_ix_str != ""){
		$where .= "and cmd.gp_ix in ($gp_ix_str) ";
	}
}else{


	if($gp_ix){
		$where .= "and cmd.gp_ix = '$gp_ix' ";
	}
}


switch($type) {
	case("SI") : $where.=" and si.si_status = '$type' AND UNIX_TIMESTAMP(si.end_date) >= '".time()."' ";
	break;
	case("SS") : $where.=" and si.si_status = 'SI' AND UNIX_TIMESTAMP(si.end_date) < '".time()."' ";
	break;
	case("CC") : $where.=" and si.si_status = '$type' ";
	break;
}

$Contents = "

<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "서비스관리 > $title_str ")."</td>
	</tr>
	<!--tr>
		<td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> $title_str </b></div>")."</td>
	</tr-->
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>";
$Contents .= "
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0><form name='search_frm' method='get' action=''>
				<tr height=25>
					<td colspan=2  align='left'  style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주문정보 검색하기</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 height=160 width='100%' valign=top style='padding-top:5px;'>
						<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05'>
									<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0 >
									<TR>
										<TD bgColor=#ffffff style='padding:0 0 3px 0;height:120px;'>
										<table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
										<col width=15%>
										<col width=35%>
										<col width=15%>
										<col width=35%>";
if(!$pre_type){
$Contents .= "

											<tr>
												<th class='search_box_title' >이용상태 : </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0' >
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='*'>
													<TR height=25>
														<TD ><input type='radio' name='type' id='type_A' value='' ".CompareReturnValue("",$type,' checked')." ><label for='type_A'>전체</label></TD>
														<TD ><input type='radio' name='type' id='type_SI' value='SI' ".CompareReturnValue("SI",$type,' checked')." ><label for='type_SI'>사용중</label></TD>
														<TD ><input type='radio' name='type' id='type_SS' value='SS' ".CompareReturnValue("SS",$type,' checked')." ><label for='type_SS'>기간만료</label></TD>
														<TD ><input type='radio' name='type' id='type_CC' value='CC' ".CompareReturnValue("CC",$type,' checked')." ><label for='type_CC'>사용취소</label></TD>
														<td></td>
													</TR>
												</TABLE>
												</td>
											</tr>";
}else if($pre_type == "EA"){
$Contents .= "
											<tr>
												<th class='search_box_title' >이용상태 : </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='*'>
													<TR height=25>
														<TD ><input type='radio' name='type' id='type_A' value='' ".CompareReturnValue("",$type,' checked')." ><label for='type_A'>전체</label></TD>
														<TD ><input type='radio' name='type' id='type_SI' value='SI' ".CompareReturnValue("SI",$type,' checked')." ><label for='type_SI'>사용중</label></TD>
														<TD ><input type='radio' name='type' id='type_SS' value='SS' ".CompareReturnValue("SS",$type,' checked')." ><label for='type_SS'>기간만료</label></TD>
														<TD ><input type='radio' name='type' id='type_CC' value='CC' ".CompareReturnValue("CC",$type,' checked')." ><label for='type_CC'>사용취소</label></TD>
														<td></td>
													</TR>
												</TABLE>
												</td>
											</tr>";
}
$Contents .= "
											<tr height=30>
												<th class='search_box_title' >검색항목 : </th>
												<td class='search_box_item' colspan=3>
													<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<tr>
														<td width='120px'>
														<select name='search_type' style='font-size:11px;'>
															<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">사용자이름+서비스명</option>
															<option value='si.name' ".CompareReturnValue('si.name',$search_type,' selected').">사용자이름</option>
															<option value='pname' ".CompareReturnValue('pname',$search_type,' selected').">상품이름</option>
														</select>
														</td>
														<td width='*'><input type='text' class=textbox name='search_text' size='30' value='$search_text' style=''></td>
														</tr>
														</table>
													</td>
											</tr>
											<tr height=30>
												<th class='search_box_title' >서비스코드 : </th>
												<td class='search_box_item'  colspan=3>
													".getServiceInfoSelect('parent_service_code', '1차 서비스분류',$parent_service_code, $parent_service_code, 1, " onChange=\"loadService(this,'service_code')\" ")."
													".getServiceInfoSelect('service_code', '2차 서비스분류',$parent_service_code, $service_code, 2, "")."
												</td>
											</tr>
											<tr height=30 class='detail_search' >
												<th class='search_box_title' >회원그룹 : </th>
												<td class='search_box_item' colspan=3>
													".makeGroupCheckButton($db1,"gp_ix[]",$gp_ix)."
												</td>
											</tr>
											<tr height=33>
												<th class='search_box_title' >
												<select name='date_type'>
												<option value='si.start_date' ".CompareReturnValue('si.start_date',$date_type,' selected').">시작일자</option>
												<option value='si.end_date' ".CompareReturnValue('si.end_date',$date_type,' selected').">만료일자</option>
												</select>
												<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
												<td class='search_box_item'  colspan=3>
													<table cellpadding=3  cellspacing=1 border=0 bgcolor=#ffffff>
														<tr>
															<TD  nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
															<TD style='padding:0 5px;' align=center> ~ </TD>
															<TD nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>
															<td>";

				$vdate = date("Ymd", time());
				$today = date("Y/m/d", time());
				$vyesterday = date("Y/m/d", time()-84600);
				$voneweekago = date("Y/m/d", time()-84600*7);
				$vtwoweekago = date("Y/m/d", time()-84600*14);
				$vfourweekago = date("Y/m/d", time()-84600*28);
				$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
				$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
				$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
				$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
				$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
				$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
				$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

							$Contents .= "
												<a href=\"javascript:init_date('$today','$today');\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
												<a href=\"javascript:init_date('$vyesterday','$vyesterday');\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
												<a href=\"javascript:init_date('$voneweekago','$today');\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
												<a href=\"javascript:init_date('$v15ago','$today');\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
												<a href=\"javascript:init_date('$vonemonthago','$today');\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
												<a href=\"javascript:init_date('$v2monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
												<a href=\"javascript:init_date('$v3monthago','$today');\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>

															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
										</TD>
									</TR>
									</TABLE>
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
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr></form>
</table>
<form name=listform method=post action='service_usage.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'><!--target='act'-->
<input type='hidden' name='act' value='select_status_update'>
<input type='hidden' name='page' value='$page'>
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
 <tr>";


	/*if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$where .= " and o.oid = od.oid and  od.company_id = '".$company_id."'";
		}else{
			$where .= " and o.oid = od.oid ";
		}

		if($admininfo[mem_type] == "MD"){
			$where .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}
	}else if($admininfo[admin_level] == 8){
		$where .= " and o.oid = od.oid and od.company_id = '".$admininfo[company_id]."'";
	}*/


	$sql = "SELECT count(si.si_ix) as total
					FROM service_info si LEFT JOIN ".TBL_COMMON_USER." c ON c.code=si.code LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON c.code=cmd.code
					$where "; //, ".TBL_SHOP_PRODUCT." p, service_order_detail od
	//echo $sql;
	$db1->query($sql);
	
	$db1->fetch();
	$total = $db1->dt[total];

 $Contents .= "<td colspan=3 align=left><b>전체 주문수 : $total 건</b></td><td colspan=10 align=right>

	<!--a href='excel_out.php?".$QUERY_STRING."' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a-->";

if($admininfo[admin_level] == 9){

$Contents .= " <a href='service_usage_excel2003.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";

}else if($admininfo[admin_level] == 8){
$Contents .= "<span style='color:red'><!--! 주의 : 입금예정 처리상태일 경우, 상품배송을 하지 마시기 바랍니다. 판매된 상품으로 처리 불가능-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span> ";
$Contents .= "<a href='service_usage_excel2003.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>&nbsp;&nbsp;";
//$Contents .= "<a href='orders.excel.hanjin.php?".$QUERY_STRING."'><img src='../image/btn_delivery_excel_save.gif' border=0 align=absmiddle></a>";
}
$Contents .= "
	</td>
  </tr>
  </table>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='25' >
		<td class='s_td ctr' width='5%'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
		<td width='7%' align='center'  class='m_td' nowrap><font color='#000000' class=small><b>회원그룹</b></font></td>
		<td width='7%' align='center'  class='m_td' nowrap><font color='#000000' class=small><b>사용자명</b></font></td>
		<td width='12%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>서비스종류</b></font></td>
		<td width='*%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>패키지명</b></font></td>
		<td width='9%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>시작일</b></font></td>
		<td width='9%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>만료일</b></font></td>
		<td width='9%' align='center' class='m_td' nowrap><font color='#000000' class=small><b>서비스상태</b></font></td>
		<td width='9%' align='center' class='e_td' nowrap><font color='#000000' class=small><b>관리</b></font></td>
	</tr>

  ";

//print_r($db1->dt);
//echo $db1->total;

/*
	for ($i = 0; $i < $db1->total; $i++)
	{
		$db1->fetch($i);
*/
		if($admininfo[admin_level] == 9){
			/*if($admininfo[mem_type] == "MD"){
				$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}*/
			$sql = "SELECT si.*, UNIX_TIMESTAMP(si.end_date) AS unix_end_date , gp.gp_name
						FROM service_info si 
						LEFT JOIN ".TBL_COMMON_USER." c ON si.code=c.code 
						LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON c.code=cmd.code
						LEFT JOIN ".TBL_SHOP_GROUPINFO." gp on cmd.gp_ix = gp.gp_ix
						$where 
						ORDER BY si.regdate DESC 
						limit $start, $max ";

			//echo $sql;
		/*서브쿼리 삭제 아무것도 없을때 에러남 서브쿼리 부분에서 */
		//,			(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,
		//				(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id) as company_total

		}else if($admininfo[admin_level] == 8){
			$sql = "SELECT si.*, UNIX_TIMESTAMP(si.end_date) AS unix_end_date , gp.gp_name
						FROM service_info si 
						LEFT JOIN ".TBL_COMMON_USER." c ON si.code=c.code 
						LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON c.code=cmd.code
						LEFT JOIN ".TBL_SHOP_GROUPINFO." gp on cmd.gp_ix = gp.gp_ix
						$where 
						ORDER BY si.regdate DESC 
						limit $start, $max ";

		//,(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as delivery_totalprice,(select company_total from shop_order_delivery where o.oid = oid and od.company_id = company_id) as company_total
		}
		$ddb->query($sql);
		$od_count = $ddb->total;

if($ddb->total){		

	$bcompany_id = '';
	for($j=0;$j < $ddb->total;$j++){
		$ddb->fetch($j);
		$delete = "<a href=\"javascript:act('delete','".$ddb->dt[si_ix]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle></a>";

		if($ddb->dt[use_reserve_price]>0) {
			$use_reserve_price="<span style='font-weight:100;'>마일리지 사용: ".$currency_display[$admin_config["currency_unit"]]["front"]." ".$ddb->dt[use_reserve_price]." ".$currency_display[$admin_config["currency_unit"]]["back"]."</span>";
		} else {
			$use_reserve_price="";
		}

		switch($ddb->dt[si_status]) {
			case "SI" : $si_status="사용중";
			break;
			case "CC" : $si_status="사용취소";
			break;
		}

		$now_time=time();
		if($now_time>$ddb->dt["unix_end_date"]) {
			$si_status="사용만료";
		}

		$one_status = $si_status."<input type='hidden' id='si_status_".$ddb->dt["si_ix"]."' value='".$ddb->dt[si_status]."'>";

		if($ddb->dt[gift] != ""){
			$od_count_plus = 0;
		}else{
			$od_count_plus = 0;
		}
		//$Contents .= "<tr ".($ddb->dt[oid] != $b_oid  ? "style='background-color:#efefef'":"")." height=28 >";// kbk
		$Contents .= "<tr height=28 >";
		$Contents .= "<td  nowrap align='center'><input type=checkbox name='si_ix[]' id='si_ix_".$ddb->dt["si_ix"]."' value='".$ddb->dt["si_ix"]."' ><input type=hidden name='bstatus[".$ddb->dt["si_ix"]."]' value='".$ddb->dt[si_status]."'><input type='hidden' id='od_status_".$ddb->dt["si_ix"]."'></td>";
		$Contents .= "<td style='line-height:140%;color:#007DB7;font-weight:bold;' align=center >".$ddb->dt[gp_name]."</td>";
		$Contents .= "<td class='point' style='line-height:140%' align=center>";
		
		$Contents .= "
		<a href=\"javascript:PopSWindow('../member/member_view.php?code=".$ddb->dt[code]."',950,500,'member_info')\" >".$ddb->dt["name"]."</a></td>";

		$Contents .= "<td style='line-height:140%;color:#007DB7;font-weight:bold;' align=center >".print_service_code_name($ddb->dt[s_kind])."</td>";
		$Contents .= "
						<td class='dot-x point' style='padding-left:10px;'>
							<TABLE cellpadding='0' cellspacing='0' border='0' width='100%'>
								<col width='60' />
								<col width='5' />
								<col width='*' />
								<TR>
									<TD><a href='service_usage.edit.php?si_ix=".$ddb->dt[si_ix]."' target='blank'><img src='".getServiceProductImage($ddb->dt["s_type"])."'  width=50></a></TD>
									<td width='5'></td>
									<TD class=small style='line-height:140%'><a href='service_usage.edit.php?si_ix=".$ddb->dt[si_ix]."' target='blank'>".$ddb->dt[pname]."</a></TD>
								</TR>
							</TABLE>
						</td>
						<td  align='center'  nowrap>".substr($ddb->dt[start_date],0,10)."</td>
						<td  align=center>".substr($ddb->dt[end_date],0,10)."</td>
						<td  align=center>".$si_status."</td>";
				
		
			$Contents .= "<td  align='center'  nowrap>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .= "<a href=\"service_usage.edit.php?si_ix=".$ddb->dt[si_ix]."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle style='margin:3px;'><!--btc_modify.gif--></a> ";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle style='margin:3px;'><!--btc_modify.gif--></a> ";
			}
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents .= $delete;
			}

			$Contents .= "</td>";
		$Contents .= "</tr>";

		//$bcompany_id = $ddb->dt[company_id];
	}
	//$Contents .= "<tr height=3><td colspan=10 bgcolor='#DDDDDD'></td></tr>";
//	}
}else{
$Contents .= "<tr height=50><td colspan=9 align=center>조회된 결과가 없습니다.</td></tr>
		";
}

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
	</tabel>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
  <tr height=40>
    <td align=left valign=middle style='font-weight:bold' nowrap>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){


	$Contents .= "선택된 항목을 ";
	if($admininfo[admin_level] == 9){

	$Contents .= "
			<select name='status' onchange=\"if(this.value == '".ORDER_STATUS_DELIVERY_ING."'){document.getElementById('invoice').style.display = 'inline'}else{document.getElementById('invoice').style.display = 'none'}\">
					<option value='SI' >사용중</option>
					<option value='CC' >사용취소</option>
				</select>";
	}else if($admininfo[admin_level] == 8){
	$Contents .= "<select name='status' onchange=\"if(this.value == '".ORDER_STATUS_DELIVERY_ING."'){document.getElementById('invoice').style.display = 'inline'}else{document.getElementById('invoice').style.display = 'none'}\">
					<option value='SI' >사용중</option>
				</select>";
	}
	$Contents .= "로 상태변경
	<input type=image src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle>";

}
$Contents .= "

    </td>
  </tr>
  <tr height=40>
    <td align='center'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
  </tr>
</table>
</form>
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문번호를 클릭하시면 주문에 대한 상세 정보를 보실수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문상태를 변경하시려면 수정버튼을 누르세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문상태를 빠르게 변경하시려면 변경하시고자 하는 주문 선택후 아래 변경하고자 하는 상태를 선택하신후 수정버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>주문총액</b>은 <u>배송비 미포함 금액</u>입니다.</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Script = "
<script language='javascript' >
function loadService(sel,target) {
	
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	
	var depth = sel.getAttribute('depth');
//	document.write('service_div.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
	window.frames['iframe_act'].location.href = 'service_div.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';
	

}
</script>
";

$Contents .= HelpBox("서비스이용리스트", $help_text);
$P = new LayOut();
$P->OnloadFunction = "onLoad('$sDate','$eDate');ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='service_usage.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->strLeftMenu = service_menu();
$P->Navigation = "서비스관리 > 서비스이용리스트";
$P->title = "서비스이용리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function getServiceProductImage($s_code) {
	global $admin_config;
	$mdb=new MySQL;
	
	$sql="SELECT sp.id FROM service_division sd LEFT JOIN service_product sp ON sd.service_code=sp.service_code WHERE sd.service_code='".$s_code."' ";
	$mdb->query($sql);

	if($mdb->total) {
		$mdb->fetch();
		$img_txt=PrintImage($admin_config[mall_data_root]."/images/service_product", $mdb->dt[id], "c");
	} else {
		$img_txt="";
	}
	return $img_txt;
	
}
?>