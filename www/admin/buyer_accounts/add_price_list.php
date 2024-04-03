<?
include_once("../class/layout.class");
include("../order/orders.lib.php");

if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));
	$startDate = date("Y-m-d", $before10day);
	$endDate = date("Y-m-d");
}

$db = new Database;

if(!$title_str){
	$title_str  = "추가비용 리스트";
}

if(!$parent_title){
	$parent_title  = "구매자정산관리";
}

$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


if($mode!="search"){
	$orderdate=1;
}

if(!$date_type){
	$date_type="op.regdate";
}

if($orderdate){
	$where .= "and date_format(".$date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
}


if(is_array($pay_status)){
	for($i=0;$i < count($pay_status);$i++){
		if($pay_status[$i]){
			if($pay_status_str == ""){
				$pay_status_str .= "'".$pay_status[$i]."'";
			}else{
				$pay_status_str .= ", '".$pay_status[$i]."' ";
			}
		}
	}

	if($pay_status_str != ""){
		$where .= "and op.pay_status in ($pay_status_str) ";
	}
}else{
	if($pay_status){
		$where .= "and op.pay_status = '$pay_status' ";
	}
}

if(is_array($claim_type)){
	for($i=0;$i < count($claim_type);$i++){
		if($claim_type[$i]){
			if($claim_type_str == ""){
				$claim_type_str .= "'".$claim_type[$i]."'";
			}else{
				$claim_type_str .= ", '".$claim_type[$i]."' ";
			}
		}
	}

	if($claim_type_str != ""){
		$where .= "and op.claim_type in ($claim_type_str) ";
	}
}else{
	if($claim_type){
		$where .= "and op.claim_type = '$claim_type' ";
	}
}

if($search_type && $search_text){
	$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
}

if(is_array($method)){
	for($i=0;$i < count($method);$i++){
		if($method[$i] != ""){
			if($method_str == ""){
				$method_str .= "'".$method[$i]."'";
			}else{
				$method_str .= ", '".$method[$i]."' ";
			}
		}
	}

	if($method_str != ""){
		$where .= "and op.method in ($method_str) ";
	}
}else{
	if($method){
		$where .= "and op.method = '$method' ";
	}
}


$Contents = "
<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "$parent_title > $title_str ")."</td>
	</tr>
</table>
<form name='search_frm' method='get' action=''>
<input type='hidden' name='mode' value='search' />
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>주문정보 검색하기</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
						<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05'>
									<TABLE cellSpacing=0 cellPadding=3 style='width:100%;' align=center border=0 class='search_table_box'>
											<col width=15%>
											<col width=35%>
											<col width=15%>
											<col width=35%>
											<tr>
												<th class='search_box_title'>추가비용 결제상태 </th>
												<td class='search_box_item' colspan='3'>
													<table cellpadding=0 cellspacing=0 width='100%' border='0' >
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<TR height=25>
															<TD>
																<input type='checkbox' name='pay_status[]'  id='pay_status".ORDER_STATUS_INCOM_READY."' value='".ORDER_STATUS_INCOM_READY."' ".CompareReturnValue(ORDER_STATUS_INCOM_READY,$pay_status,' checked')." ><label for='pay_status".ORDER_STATUS_INCOM_READY."'>".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label>
															</TD>
															<TD>
																<input type='checkbox' name='pay_status[]'  id='pay_status".ORDER_STATUS_INCOM_COMPLETE."' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$pay_status,' checked')." ><label for='pay_status".ORDER_STATUS_INCOM_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label>
															</TD>
															<TD>
																<input type='checkbox' name='pay_status[]'  id='pay_status".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' value='".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE,$pay_status,' checked')." ><label for='pay_status".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE)."</label>
															</TD>
															<TD>
																<input type='checkbox' name='pay_status[]'  id='pay_status".ORDER_STATUS_CANCEL_COMPLETE."' value='".ORDER_STATUS_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_CANCEL_COMPLETE,$pay_status,' checked')." ><label for='pay_status".ORDER_STATUS_CANCEL_COMPLETE."'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label>
															</TD>
															<TD>
																<input type='checkbox' name='pay_status[]'  id='pay_status".ORDER_STATUS_LOSS."' value='".ORDER_STATUS_LOSS."' ".CompareReturnValue(ORDER_STATUS_LOSS,$pay_status,' checked')." ><label for='pay_status".ORDER_STATUS_LOSS."'>".getOrderStatus(ORDER_STATUS_LOSS)."</label>
															</TD>
															<TD></TD>
														</TR>
													</TABLE>
												</td>
											</tr>
											<tr>
												<th class='search_box_title'>클래임타입 </th>
												<td class='search_box_item' colspan=3>
												<table cellpadding=0 cellspacing=0 width='100%' border='0'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<col width='15%'>
													<TR height=30>
														<TD >
															<input type='checkbox' name='claim_type[]' id='claim_type_c' value='C' ".CompareReturnValue("C",$claim_type,' checked')." ><label for='claim_type_c'>취소</label>
														</TD>
														<TD >
															<input type='checkbox' name='claim_type[]' id='claim_type_r' value='R' ".CompareReturnValue("R",$claim_type,' checked')." ><label for='claim_type_r'>반품</label>
														</TD>
														<TD>
															<input type='checkbox' name='claim_type[]' id='claim_type_e' value='E' ".CompareReturnValue("E",$claim_type,' checked')." ><label for='claim_type_e'>교환</label>
														</TD>
														<TD></TD>
														<TD></TD>
														<TD></TD>
													</TR>
												</TABLE>
												</td>
											</tr>
											<tr height=33>
												<th class='search_box_title'>
													<select name='date_type'>
														<option value='op.regdate' ".CompareReturnValue('op.regdate',$date_type,' selected').">추가비용요청일</option>
														<option value='op.ic_date' ".CompareReturnValue('op.ic_date',$date_type,' selected').">추가비용입금일</option>
														<option value='".($db->dbms_type == "oracle" ? 'o.date_' : 'o.date' )."' ".CompareReturnValue(($db->dbms_type == "oracle" ? 'o.date_' : 'o.date' ),$date_type,' selected').">주문일자</option>
													</select>
													<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
												<td class='search_box_item' colspan=3>
													".search_date('startDate','endDate',$startDate,$endDate)."
												</td>
											</tr>
											<tr height=30>
												<th class='search_box_title'>검색항목 </th>
												<td class='search_box_item'>
													<table cellpadding='3' cellspacing='0' border='0' width='100%'>
													<col width='90px'>
													<col width='*'>
													<tr>
														<td>
														<select name='search_type' style='font-size:12px;'>
															<option value='op.oid' ".CompareReturnValue('op.oid',$search_type,' selected').">주문번호</option>
															<option value='o.bname' ".CompareReturnValue('o.bname',$search_type,' selected').">주문자명</option>
														</select>
														</td>
														<td ><input type='text' class=textbox name='search_text' size='30' value='$search_text' style=''></td>
														</tr>
														</table>
													</td>
												<th class='search_box_title'>추가비용 결제방법 </th>
												<td class='search_box_item'>
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_BANK."' value='".ORDER_METHOD_BANK."' ".CompareReturnValue(ORDER_METHOD_BANK,$method,' checked')." ><label for='method_".ORDER_METHOD_BANK."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_BANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_BANK.".gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CARD."' value='".ORDER_METHOD_CARD."' ".CompareReturnValue(ORDER_METHOD_CARD,$method,' checked')." ><label for='method_".ORDER_METHOD_CARD."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CARD)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CARD.".gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_VBANK."' value='".ORDER_METHOD_VBANK."' ".CompareReturnValue(ORDER_METHOD_VBANK,$method,' checked')." ><label for='method_".ORDER_METHOD_VBANK."' class='helpcloud' help_width='70' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_VBANK)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_VBANK.".gif' align='absmiddle'></label>&nbsp;
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_ICHE."' value='".ORDER_METHOD_ICHE."' ".CompareReturnValue(ORDER_METHOD_ICHE,$method,' checked')." ><label for='method_".ORDER_METHOD_ICHE."' class='helpcloud' help_width='110' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_ICHE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_ICHE.".gif' align='absmiddle'></label>&nbsp;
													<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_PHONE."' value='".ORDER_METHOD_PHONE."' ".CompareReturnValue(ORDER_METHOD_PHONE,$method,' checked')." ><label for='method_".ORDER_METHOD_PHONE."' class='helpcloud' help_width='80' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_PHONE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_PHONE.".gif' align='absmiddle'></label>&nbsp;-->
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$method,' checked')." ><label for='method_".ORDER_METHOD_CASH."' class='helpcloud' help_width='50' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_CASH)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_CASH.".gif' align='absmiddle'></label>
													<!--input type='checkbox' name='method[]' id='method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$method,' checked')." ><label for='method_".ORDER_METHOD_SAVEPRICE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_SAVEPRICE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_SAVEPRICE.".gif' align='absmiddle'></label>&nbsp;-->
													<input type='checkbox' name='method[]' id='method_".ORDER_METHOD_RESERVE."' value='".ORDER_METHOD_RESERVE."' ".CompareReturnValue(ORDER_METHOD_RESERVE,$method,' checked')." ><label for='method_".ORDER_METHOD_RESERVE."' class='helpcloud' help_width='55' help_height='15' help_html='".getMethodStatus(ORDER_METHOD_RESERVE)."'><img src='../images/".$admininfo[language]."/s_order_method_".ORDER_METHOD_RESERVE.".gif' align='absmiddle'></label>
												</td>
											</tr>
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
	</tr>
</table>
</form>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
 <tr height=30>";
	
	
	$sql="SELECT * FROM shop_order_payment op , ".TBL_SHOP_ORDER." o WHERE op.oid=o.oid and op.pay_type='A' $where";
	$db->query($sql);
	$total = $db->total;
	
	
	$sql="SELECT op.*,o.order_date, o.user_code as user_id, o.bname, o.mem_group, o.buserid, o.bmobile FROM shop_order_payment op , ".TBL_SHOP_ORDER." o WHERE op.oid=o.oid and op.pay_type='A' $where ORDER BY op.regdate DESC LIMIT $start, $max";
	$db->query($sql);
	

 $Contents .= "<td colspan=3 align=left><b class=blk>전체 : ".$total." 건</b></td>
			<td colspan=9 align=right >
			</td>
		</tr>
  </table>";

	$Contents .= "
	  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='25' >
			<td width='8%' align='center'  class='m_td' nowrap><b>요청일</b></td>
			<td width='15%' align='center' class='m_td'><b>주문일자/주문번호</b></td>
			<td width='7%' align='center'  class='m_td' nowrap><b>주문자명</b></td>
			<td width='6%' align='center' class='m_td' nowrap><b>클래임타입</b></td>
			<td width='10%' align='center' class='m_td' nowrap><b>결제방법</b></td>
			<td width='10%' align='center' class='m_td' nowrap><b>결제상태</b></td>
			<td width='15%' align='center' class='m_td' nowrap><b>비고</b></td>
			<td width='6%' align='center' class='m_td' nowrap><b>추가비용</b></td>
			<td width='5%' align='center' class='e_td' nowrap><b>관리</b></td>
		</tr>";

	if($db->total){
		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);


			if($db->dt[claim_type]=="C"){
				$claim_type="취소";
			}elseif($db->dt[claim_type]=="R"){
				$claim_type="반품";
			}elseif($db->dt[claim_type]=="E"){
				$claim_type="교환";
			}else{
				$claim_type = $db->dt[claim_type];
			}
			
			if($db->dt[pay_status]==ORDER_STATUS_INCOM_COMPLETE){
				$pay_status="(".$db->dt[ic_date].")";
			}else{
				$pay_status="";
			}

			$pay_status.=" <img src='../images/".$admininfo["language"]."/btn_modify.gif' align=absmiddle onclick=\"ShowModalWindow('./add_price.pop.php?opay_ix=".$db->dt[opay_ix]."',700,300,'add_price_pop');\"  style='cursor:pointer;'>";


			$u_etc_info=get_order_user_info($db->dt[user_id]);

			$b_mem_info = "<b style='cursor:pointer' class='helpcloud' help_width='230' help_height='100' help_html='주문자 <br/>ID/성명 : ".$db->dt[buserid]."/".$db->dt[bname]."<br/>핸드폰 : ".$db->dt[bmobile]." <br/>회원그룹 : ".$db->dt[mem_group]." <br/>최근로그인 : ".$u_etc_info["user_last"]." <br/>최근주문(30일) : ".$u_etc_info["user_order_cnt"]."건' />".Black_list_check($db->dt[user_id],$db->dt[bname])."</b> ".($db->dt[user_id] ? "<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' align=absmiddle onclick=\"PopSWindow('../member/member_view.php?code=".$db->dt[user_id]."',950,500,'member_info')\"  style='cursor:pointer;'>" : "");

			$Contents .= "<tr height=28 >";
				$Contents .= "<td  class='list_box_td ' style='line-height:140%' align=center>".substr($db->dt[regdate],0,10)."</td>";
				$Contents .= "<td class='list_box_td point' style='line-height:140%' align=center>".$db->dt[order_date]."<br><a href=\"../order/orders.read.php?oid=".$db->dt[oid]."&pid=".$db->dt[pid]."\" style='color:#007DB7;font-weight:bold;' class='small'>".$db->dt[oid]."</a></td>";
				$Contents .= "<td style='line-height:140%' align=center class='list_box_td'>".$b_mem_info."</td>";
				$Contents .= "<td class='list_box_td ' align='center' nowrap>".$claim_type."</td>
							<td class='list_box_td' align='center'  nowrap>".getMethodStatus($db->dt[method])."</td>
							<td class='list_box_td' align='center'  nowrap>".getOrderStatus($db->dt[pay_status]).$pay_status."</td>
							<td class='list_box_td' align='center'  nowrap>".$db->dt[memo]."</td>";
		$Contents .= "<td class='list_box_td' align='center'  nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[payment_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
							<td class='list_box_td' align='center'>";
					if($admininfo[admin_level] ==  9){
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						  $Contents .= "<a href=\"../order/orders.edit.php?oid=".$db->dt[oid]."&pid=".$db->dt[pid]."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a>";
						}else{
						   $Contents .=  "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_invoice.gif' border=0 align=absmiddle style='margin:2px;'></a>";
						}
					}
		$Contents .= "</td>
							</tr>";
		}

	}else{
	$Contents .= "<tr height=50><td colspan='9' align=center>조회된 결과가 없습니다.</td></tr>
				";
	}
	$Contents .= "
	  </table>";



if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
  <tr height=40>
    <td colspan='12' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
  </tr>
</table>
";

$P = new LayOut();

$P->strLeftMenu = buyer_accounts_menu();
$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='../order/orders.goods_list.js'></script>\n";
$P->Navigation = "구매자정산관리 > $title_str ";
$P->title = $title_str;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>