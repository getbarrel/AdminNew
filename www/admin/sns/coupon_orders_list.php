<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
}

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

if(!$update_kind){
	$update_kind = "display";
}

if($max == ""){
	$max = 10; //페이지당 갯수
}else{
	$max = $max;
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;
$db2 = new Database;
$sms_design = new SMS;
/*if($_SESSION["mode"] == "search"){
	$mode = "search";
}*/

switch ($depth){
	case 0:
		$cut_num = 3;
		break;
	case 1:
		$cut_num = 6;
		break;
	case 2:
		$cut_num = 9;
		break;
	case 3:
		$cut_num = 9;
		break;
}

$where = "WHERE od.status <> 'SR' ";
if($admininfo[admin_level] == 9){
	$where .= "AND od.product_type = '5' ";
}else{
	$where .= "AND od.product_type = '5' and od.company_id ='".$admininfo[company_id]."'  ";
}
if($date_type){
	if ($FromYY != "")	$where .= "and date_format(".$date_type.",'%Y%m%d') between $startDate and $endDate ";
}

if(is_array($type)){
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
		$where .= "and od.status in ($type_str) ";
	}
}else{
	if($type){
		$where .= "and od.status = '$type' ";
	}

}
//echo $where;

if($search_type && $search_text){
	if($search_type == "combi_name"){
		$where .= "and (o.bname LIKE '%".trim($search_text)."%'  or o.rname LIKE '%".trim($search_text)."%' OR o.bank_input_name LIKE '%".trim($search_text)."%') ";
	}else{
		$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
	}
}

if($dispathpoint!="") {
	$where.=" AND od.dispathpoint='".$dispathpoint."'";
}

if($company_id != ""){
	$where .= " and od.company_id = '".$company_id."'";
}

/*$sql = "SELECT ci.ci_ix FROM ".TBL_SNS_COUPON_INFO." ci
	LEFT JOIN ".TBL_SNS_ORDER_DETAIL." od on ci.od_ix = od.od_ix
	LEFT JOIN ".TBL_SNS_ORDER." o on ci.oid = o.oid
	LEFT JOIN ".TBL_SNS_MEMBER." m on ci.code = m.code
	LEFT JOIN ".TBL_SNS_GROUPINFO." mg on m.gp_ix = mg.gp_ix
	$where ";*/
$sql = "SELECT ci.ci_ix FROM ".TBL_SNS_COUPON_INFO." ci
	LEFT JOIN ".TBL_SNS_ORDER_DETAIL." od on ci.od_ix = od.od_ix
	LEFT JOIN ".TBL_SNS_ORDER." o on ci.oid = o.oid
	LEFT JOIN ".TBL_COMMON_USER." cu on o.uid = cu.code
	$where ";//수정kbk 11/12/23
//echo $sql;
$db->query($sql);


$total = $db->total;

	/*$sql = "SELECT ci.*, od.status AS od_status,od.pname, od.psprice, od.dispathpoint, o.bname, mg.gp_name, date_format(o.date, '%Y-%m-%d') as regdate, o.method as paymethod, o.status as orderstatus FROM ".TBL_SNS_COUPON_INFO." ci
	LEFT JOIN ".TBL_SNS_ORDER_DETAIL." od on ci.od_ix = od.od_ix
	LEFT JOIN ".TBL_SNS_ORDER." o on ci.oid = o.oid
	LEFT JOIN ".TBL_SNS_MEMBER." m on ci.code = m.code
	LEFT JOIN ".TBL_SNS_GROUPINFO." mg on m.gp_ix = mg.gp_ix
	$where
	ORDER BY o.date desc
	LIMIT $start, $max ";*/
	$sql = "SELECT ci.*, od.status AS od_status,od.pname, od.psprice, od.dispathpoint, o.bname, cu.mem_type, date_format(o.date, '%Y-%m-%d') as regdate, o.method as paymethod, o.status as orderstatus FROM ".TBL_SNS_COUPON_INFO." ci
	LEFT JOIN ".TBL_SNS_ORDER_DETAIL." od on ci.od_ix = od.od_ix
	LEFT JOIN ".TBL_SNS_ORDER." o on ci.oid = o.oid
	LEFT JOIN ".TBL_COMMON_USER." cu on o.uid = cu.code
	$where
	ORDER BY o.date desc
	LIMIT $start, $max ";//수정kbk 11/12/23
	//echo $sql;
	$db->query($sql);
	$order_list = array();
	$order_list = $db->fetchall();

	//$str_page_bar = page_bar($total, $page,$max, "&status=$status&search_type=$search_type&search_text=$search_text");
	if($QUERY_STRING == "nset=$nset&page=$page"){
		$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
	}else{
		$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	}
	$str_page_bar = page_bar($total, $page,$max,$query_string,"");

$Contents =	"
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("쿠폰상품 주문관리", "소셜커머스 > 쿠폰상품 주문관리")."</td>
	</tr>

	";

$Contents .=	"
	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
	<input type='hidden' name='mode' value='search' />
	<tr height=150>
		<td colspan=2 >
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:0px'>
						<table cellpadding=3 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>
							<tr height=30>
								<td class='search_box_title'>  처리상태  </td>
								<td class='search_box_item' colspan=3>
									<table cellpadding=0 cellspacing=0 width='100%' border='0'>
										<TR>
											<TD width='100'><input type='checkbox' name='type[]' id='type_ir' value='".ORDER_STATUS_INCOM_READY."' ".CompareReturnValue(ORDER_STATUS_INCOM_READY,$type,' checked')." ><label for='type_ir'>".getOrderStatus(ORDER_STATUS_INCOM_READY)."</label></TD>
											<TD width='100'><input type='checkbox' name='type[]' id='type_ic' value='".ORDER_STATUS_INCOM_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_INCOM_COMPLETE,$type,' checked')." ><label for='type_ic'>".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</label></TD>
											<TD width='100'><input type='checkbox' name='type[]' id='type_oc' value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$type,' checked')." ><label for='type_oc'>".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</label></TD>
											<TD width='100'><input type='checkbox' name='type[]' id='type_or' value='".ORDER_STATUS_DELIVERY_READY."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_READY,$type,' checked')." ><label for='type_or'>".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</label></TD>
											<TD width='100'><input type='checkbox' name='type[]' id='type_ob' value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$type,' checked')." ><label for='type_ob'>".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</label></TD>
										</TR>
										<TR>

											<TD><input type='checkbox' name='type[]' id='type_ea' value='".ORDER_STATUS_EXCHANGE_APPLY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_APPLY,$type,' checked')." ><label for='type_ea'>".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</label></TD>
											<TD><input type='checkbox' name='type[]' id='type_r1' value='".ORDER_STATUS_RETURN_APPLY."' ".CompareReturnValue(ORDER_STATUS_RETURN_APPLY,$type,' checked')." ><label for='type_r1'>".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</label></TD>
											<TD><input type='checkbox' name='type[]'  id='type_r2' value='".ORDER_STATUS_RETURN_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_RETURN_COMPLETE,$type,' checked')." ><label for='type_r2'>".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</label></TD>
											<TD><input type='checkbox' name='type[]' id='type_ca' value='".ORDER_STATUS_CANCEL_APPLY."' ".CompareReturnValue(ORDER_STATUS_CANCEL_APPLY,$type,' checked')."><label for='type_ca'>".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</label></TD>
											<TD><input type='checkbox' name='type[]'  id='type_xx' value='".ORDER_STATUS_CANCEL_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_CANCEL_COMPLETE,$type,' checked')." ><label for='type_xx'>".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</label></TD>
										</TR>
										<TR>

											<TD><input type='checkbox' name='type[]' id='type_ei' value='".ORDER_STATUS_EXCHANGE_ING."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_ING,$type,' checked')." ><label for='type_ei'>".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</label></TD>
											<TD><input type='checkbox' name='type[]' id='type_ed' value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_DELIVERY,$type,' checked')." ><label for='type_r1'>".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</label></TD>
											<TD><input type='checkbox' name='type[]'  id='type_ec' value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_EXCHANGE_COMPLETE,$type,' checked')." ><label for='type_r2'>".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</label></TD>

										</TR>
									</TABLE>
								</td>
							</tr>
							<tr height=30>
								<td class='search_box_title'>  검색항목  </td>
								<td class='search_box_item' colspan=3>
								<table cellpadding=0 cellspacing=0>
									<tr>
										<td>
										<select name='search_type'>
											<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자이름+입금자명+수취인명</option>
											<option value='bname' ".CompareReturnValue('bname',$search_type,' selected').">주문자이름</option>
											<option value='pname' ".CompareReturnValue('pname',$search_type,' selected').">상품이름</option>
											<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
											<option value='rname' ".CompareReturnValue('rname',$search_type,' selected').">수취인이름</option>
											<option value='bmobile' ".CompareReturnValue('bmobile',$search_type,' selected').">주문자핸드폰</option>
											<option value='rmobile' ".CompareReturnValue('rmobile',$search_type,' selected').">수취인핸드폰</option>
										</select>
										</td>
										<td style='padding:0px 2px'>
										<INPUT id=search_texts  class='textbox' value='".$search_text."' style='padding:0px;WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
										</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td class='search_box_title' >
									<table cellpadding=0 cellspacing=0>
									<tr>
										<td class='search_box_title' style='padding:0px; margin:0px; align:left;'>
										<select name='date_type'>
											<option value='o.date' ".CompareReturnValue('o.date',$date_type,' selected').">주문일자</option>
											<option value='o.bank_input_date' ".CompareReturnValue('o.bank_input_date',$date_type,' selected').">입금일자</option>
											<!--option value='date'>취소일자</option-->
										</select>
										</td>
										<td class='search_box_title' style='padding:0px; margin:0px; align:left;'>
										<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_form);' ".CompareReturnValue('1',$orderdate,' checked').">
										</td>
									</tr>
									</table>
								</td>
								<td class='search_box_item' colspan='3'>
									<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>
										<col width=190>
										<col width=40>
										<col width=190>
										<col width=*>
										<tr>
											<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY style='width:56px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM style='width:43px;'></SELECT> 월 <SELECT name=FromDD style='width:43px;'></SELECT> 일 </TD>
											<TD width=14 align=center>~</TD>
											<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY style='width:56px;'></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM style='width:43px;'></SELECT> 월 <SELECT name=ToDD style='width:43px;'></SELECT> 일</TD>
											<td style='padding-left:10px;'>
												<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
												<a href=\"javascript:select_date('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
												<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
												<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
												<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
												<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
												<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							";
							$Contents .=	"
							<tr>
								<td class='search_box_title'>배송형태</td>
								<td class='search_box_item'>
								<input type='radio' name='dispathpoint'  id='disp_' value='' ".CompareReturnValue($dispathpoint, "", " checked")."><label for='disp_'>전체</label>
								<input type='radio' name='dispathpoint'  id='disp_O' value='O' ".CompareReturnValue($dispathpoint, "O", " checked")."><label for='disp_O'>즉시배송</label>
								<input type='radio' name='dispathpoint'  id='disp_A' value='A' ".CompareReturnValue($dispathpoint, "A", " checked")."><label for='disp_A'>일괄배송</label>
								</td>
								<td width='15%' class='search_box_title'>입점업체</td>
								<td width='35%' class='search_box_item'>
									".CompanyList2($company_id,"")."
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
		</td>
	</tr>
	<tr >
		<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
		</form>
	</tr>";

$Contents .=	"
	<tr>
		<td valign=top style='padding-top:0px;'>";

$Contents .= "
		</td>
		<form name=list_frm method=post action='coupon_orders_list.act.php' onsubmit='return couponSubmit(this)' target='act'><!--onsubmit='return CheckDelete(this)' -->
		<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
		<input type='hidden' name='act' value='update'>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0 style='margin-bottom:5px;'>
				<tr height=20>
					<td align=left>
					총주문 : ".number_format($total)." 건
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
			</table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='5%'>
				<col width='13%'>
				<col width='9%'>
				<col width='*'>
				<col width='9%'>
				<col width='9%'>
				<col width='9%'>
				<col width='8%'>
				<tr bgcolor='#cccccc' align=center>
					<td class=s_td height='40'><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.list_frm)'></td>
					<td class=m_td style='line-height:140%;'>주문일자<br>주문번호</td>
					<td class=m_td style='line-height:140%;'>주문자명<br>회원등급</td>
					<td class=m_td style='line-height:140%;'>제품명<br>쿠폰번호</td>
					<td class=m_td style='line-height:140%;'>결제방법<br>입금현황</td>
					<td class=m_td style='line-height:140%;'>상품금액<br>수수료</td>
					<td class=m_td style='line-height:140%;'>발급형태<br>발급현황</td>
					<td class=e_td>관리</td>
				</tr>";




if(is_array($order_list)){
	foreach($order_list as $_key=>$_val)
	{
		$coupon_print = substr($_val['coupon_no'], 0, 15);

		$innerview .= "<tr bgcolor='#ffffff' height='25'>
					<td class='list_box_td'  rowspan=2><input type=checkbox class=nonborder id='ci_ix' name='ci_ix[]' value='".$_val[ci_ix]."'></td>
					<td class='list_box_td list_bg_gray' >".$_val['regdate']."</td>
					<td class='list_box_td' >".$_val['bname']."</td>
					<td class='list_box_td point' style='padding:4px 5px;text-align:left;'>".$_val['pname']."</td>
					<td class='list_box_td' >".getMethodStatus($_val['paymethod'])."</td>
					<td class='list_box_td list_bg_gray' >".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($_val['psprice'])."".$currency_display[$admin_config["currency_unit"]]["back"]."</td>

					<td class='list_box_td' >".($_val['dispathpoint'] == "O" ? "즉시발행" : "일괄발행")."</td>
					<td class='list_box_td list_bg_gray'  rowspan=2 nowrap>";
					if(checkMenuAuth(md5("/admin/order/orders.edit.php"),"U") && checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$innerview .= "<a href='../order/orders.edit.php?oid=".$_val['oid']."&pid=".$_val['pid']."' target='_blank')'><img src='../images/".$admininfo["language"]."/btc_modify.gif'></a>";
					}else{
					$innerview .= "<a href=\"".$auth_update_msg."\" ><img src='../images/".$admininfo["language"]."/btc_modify.gif'></a>";
					}
					$innerview .= "

					</td>
				</tr>
				<tr bgcolor='#ffffff' height='25'>
					<td class='list_box_td list_bg_gray'  style='border-top:1px solid #ffffff'>".$_val['oid']."</td>
					<!--td class='list_box_td'  style='border-top:1px solid #efefef'>".($_val['gp_name'] == "" ? "-":$_val['gp_name'])."</td-->
					<td class='list_box_td'  style='border-top:1px solid #efefef'>".($_val['mem_type'] == "" ? "-":getMemType($_val['mem_type']))."</td><!-- 수정kbk 11/12/23 -->
					<td class='list_box_td point'  style='border-top:1px solid #ffffff;text-align:left;'><b>".$coupon_print."****</b></td>
					<td class='list_box_td'  style='border-top:1px solid #efefef'>".getOrderStatus($_val['od_status'])."</td>
					<td class='list_box_td list_bg_gray'  style='border-top:1px solid #ffffff'>".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($_val['psprice'])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td'  style='border-top:1px solid #efefef' nowrap><span id='status_text_".$_val[ci_ix]."'>".getSnsCouponStatus($_val['status'])."</span></td>
				</tr>";
	$no--;
	}
} else {
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=8 align=center> 등록된 제품이 없습니다.</td></tr>";
}
	$innerview .= "</table>
				<table width='100%'>
				<tr height=30>
					<td width=210>

					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
				<tr height=30><td colspan=2 align=right></td></tr>
				</table>

				";

$Contents = $Contents.$innerview;
$cominfo = getcominfo();

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small'>
	<col width=8>
	<col width=*>
	<tr>
		<td valign=top><img src='/admin/image/icon_list.gif'></td><td class='small'>검색을 통하여 쿠폰번호를 매칭하여 사용된 쿠폰상태로 변경 합니다. </td>
	</tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$Contents .= HelpBox("SNS 배송상품 발행관리", $help_text);
$Contents.= "
			</td>
			</tr>
		</table>
			";
/*$help_text = "
<table cellpadding=0 cellspacing=0 class='small'>
	<col width=8>
	<col width=*>
	<tr>
		<td valign=top><img src='/admin/image/icon_list.gif'></td><td class='small'>검색을 통하여 쿠폰번호를 매칭하여 사용된 쿠폰상태로 변경 합니다. </td>
	</tr>
</table>
";*/

$Script = "
	<script language='javascript' src='../include/DateSelect.js'></script>\n
	<script Language='JavaScript' src='./goods_orders_list.js'></script>\n

";


$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
	$Script .= "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script>";

$Script.="
<script language='javascript'>

function couponSubmit(frm){
	if(frm.update_type.value == 1){
		if(frm.search_searialize_value.value.length < 1){
			alert(language_data['sns_coupon_orders_list.php']['A'][language]);//'적용대상중 [검색회원전체]는  검색후 사용 가능합니다. 확인후 다시 시도해주세요'
			return false;
		}

	}else if(frm.update_type.value == 2){
		var ci_ix_checked_bool = false;
		var ci_ix=document.getElementsByName('ci_ix[]');
		var ci_ix_len=ci_ix.length;
		for(i=0;i < ci_ix_len;i++){
			if(ci_ix[i].checked){
				ci_ix_checked_bool = true;
			}
		}
		if(!ci_ix_checked_bool){
			alert(language_data['sns_coupon_orders_list.php']['B'][language]);//'선택된 수신자가 없습니다. 변경/발송 하시고자 하는 수신자를 선택하신 후 저장/보내기 버튼을 클릭해주세요'
			return false;
		}
	}


	if(frm.sms_text.value.length < 1){
		alert(language_data['sns_coupon_orders_list.php']['C'][language]);//'SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요'
		frm.sms_text.focus();
		return false;
	}
	if(frm.update_type.value == 1){
		if(confirm(language_data['sns_coupon_orders_list.php']['D'][language])){return true;}else{return false;}//'검색회원 전체에게 SMS 발송을 하시겠습니까?'
	} else {
		if(confirm(language_data['sns_coupon_orders_list.php']['E'][language])){return true;}else{return false;}//'선택한 회원에게 SMS 발송을 하시겠습니까?'
	}
}

function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
	}else{
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
	}
}

function init(){

	var frm = document.search_form;
	onLoad('$sDate','$eDate');";

if($orderdate != "1"){
$Script .= "
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;";
}

$Script .= "
}

$(document).ready(function () {
	//fc_chk_byte(document.list_frm.sms_text,80, document.list_frm.sms_text_count);
});
</script>";

	$P = new LayOut();
	$P->strLeftMenu = sns_menu();
	$P->OnloadFunction = "init();";
	$P->addScript = $Script;
	$P->Navigation = "소셜커머스 > 소셜상품 주문관리 > 쿠폰상품 주문관리";
	$P->title = "쿠폰상품 주문관리";
	$P->strContents = $Contents;
	$P->jquery_use = false;

	$P->PrintLayOut();
?>