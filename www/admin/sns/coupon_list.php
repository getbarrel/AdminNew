<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');
$max = "";
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


if($admininfo[admin_level] == 9){
	$where = "where ci.coupon_no Is NOT NULL ";
	if($company_id != "") {
		$where .= " and od.company_id = '".$company_id."'";
	}
}else{
	$where = "where ci.coupon_no Is NOT NULL and od.company_id ='".$admininfo[company_id]."'  ";
}

if(is_array($status)){
	for($i=0;$i < count($status);$i++){


		if($status[$i]){
			if($type_str == ""){
				$type_str .= "'".$status[$i]."'";
			}else{
				$type_str .= ", '".$status[$i]."' ";
			}
		}
	}

	if($type_str != ""){
		$where .= "and ci.status in ($type_str) ";
	}
}else{
	if($status){
		$where .= "and ci.status = '$status' ";
	}

}

if($method != "") {
	$where .= " and o.method = ".$method;
}

if($search_type && $search_text){
	if($search_type == "combi_name"){
		$where .= "and (o.bname LIKE '%".trim($search_text)."%'  or o.rname LIKE '%".trim($search_text)."%' OR o.bank_input_name LIKE '%".trim($search_text)."%') ";
	}else{
		$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
	}
}

$sql = "SELECT ci.ci_ix FROM ".TBL_SNS_COUPON_INFO." ci
	LEFT JOIN ".TBL_SNS_ORDER_DETAIL." od on ci.od_ix = od.od_ix
	LEFT JOIN ".TBL_SNS_ORDER." o on ci.oid = o.oid
	LEFT JOIN ".TBL_SNS_MEMBER." m on ci.code = m.code
	LEFT JOIN ".TBL_SNS_GROUPINFO." mg on m.gp_ix = mg.gp_ix
	$where ";
//echo $sql;
$db->query($sql);


$total = $db->total;

	$sql = "SELECT ci.*, od.pname, o.bname, mg.gp_name, date_format(o.date, '%Y-%m-%d') as regdate FROM ".TBL_SNS_COUPON_INFO." ci
	LEFT JOIN ".TBL_SNS_ORDER_DETAIL." od on ci.od_ix = od.od_ix
	LEFT JOIN ".TBL_SNS_ORDER." o on ci.oid = o.oid
	LEFT JOIN ".TBL_SNS_MEMBER." m on ci.code = m.code
	LEFT JOIN ".TBL_SNS_GROUPINFO." mg on m.gp_ix = mg.gp_ix
	$where LIMIT $start, $max ";
	//echo $sql;
	$db->query($sql);
	$coupon_list = array();
	$coupon_list = $db->fetchall();
	$no = $total-$start;


	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&status=$status&search_type=$search_type&search_text=$search_text", "");

$Contents =	"
<table cellpadding=0 cellspacing=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("발행쿠폰 관리", "소셜커머스 > 발행쿠폰 관리")."</td>
	</tr>

	";

$Contents .=	"
	<form name='search_form' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' >
	<tr>
		<td colspan=2 >
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'><!---mbox04-->
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05 align=center' style='padding:3'>
						<table cellpadding=4 cellspacing=1 border=0 width=100% align='center' class='search_table_box'>
							<col width='15%'>
							<col width='35%'>
							<col width='15%'>
							<col width='35%'>
							<tr height=30>
								<td class='search_box_title'>  쿠폰상태  </td>
								<td class='search_box_item' colspan=3>
								<input type='checkbox' name='status[]' id='status1' value='".SNS_COUPON_STATUS_READY."' ".CompareReturnValue(SNS_COUPON_STATUS_READY, $status, " checked")."><label for='status1'>".getSnsCouponStatus(SNS_COUPON_STATUS_READY)."</label>
								<input type='checkbox' name='status[]' id='status2' value='".SNS_COUPON_STATUS_EXPIRE."' ".CompareReturnValue(SNS_COUPON_STATUS_EXPIRE, $status, " checked")."><label for='status2'>".getSnsCouponStatus(SNS_COUPON_STATUS_EXPIRE)."</label>
								<input type='checkbox' name='status[]' id='status3' value='".SNS_COUPON_STATUS_COMPLETE."' ".CompareReturnValue(SNS_COUPON_STATUS_COMPLETE, $status, " checked")."><label for='status3'>".getSnsCouponStatus(SNS_COUPON_STATUS_COMPLETE)."</label>
								</td>
							</tr>
							<tr height=30>
								<td class='search_box_title'>  검색항목  </td>
								<td class='search_box_item' colspan=3>
								<select name='search_type' style='font-size:11px;'>
									<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자이름+입금자명+수취인명</option>
									<option value='o.bname' ".CompareReturnValue('bname',$search_type,' selected').">주문자이름</option>
									<option value='od.pname' ".CompareReturnValue('pname',$search_type,' selected').">상품이름</option>
									<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
									<option value='o.rname' ".CompareReturnValue('rname',$search_type,' selected').">수취인이름</option>
									<option value='o.bmobile' ".CompareReturnValue('bmobile',$search_type,' selected').">주문자핸드폰</option>
									<option value='o.rmobile' ".CompareReturnValue('rmobile',$search_type,' selected').">수취인핸드폰</option>
								</select>
								<INPUT id=search_texts  class='textbox' value='$search_text' style=' FONT-SIZE: 12px; WIDTH: 160px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
								</td>
							</tr>
							";

							$Contents .=	"
							<tr>
								<td class='search_box_title'>결제방법</td>
								<td class='search_box_item'>
									<select name='method' class='small' style='font-size:12px;'>
										<option value=''>전체보기</option>
										<option value='".ORDER_METHOD_CARD."' ".ReturnStringAfterCompare($method, ORDER_METHOD_CARD, " selected").">카드결제</option>
										<option value='".ORDER_METHOD_PHONE."' ".ReturnStringAfterCompare($method, ORDER_METHOD_PHONE, " selected").">전화결제</option>
										<option value='".ORDER_METHOD_VBANK."' ".ReturnStringAfterCompare($method, ORDER_METHOD_VBANK, " selected").">가상계좌</option>
										<option value='".ORDER_METHOD_ICHE."' ".ReturnStringAfterCompare($method, ORDER_METHOD_ICHE, " selected").">실시간이체</option>
										<option value='".ORDER_METHOD_ASCROW."' ".ReturnStringAfterCompare($method, ORDER_METHOD_ASCROW, " selected").">에스크로</option>
									</select>
								</td>";
if($admininfo[admin_level] == 9){
							$Contents .=	"
								<td class='search_box_title'>공급업체</td>
								<td class='search_box_item'>
									".CompanyList($company_id,"","")."
								</td>";
} else {
							$Contents .=	"
								<td class='search_box_title'></td>
								<td class='search_box_item'></td>";
}
							$Contents .=	"
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
		<td colspan=2 align=center style='padding:10px 0px'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
		</form>
	</tr>";

$Contents .=	"
	<tr>
		<td valign=top style='padding-top:0px;'>";

$Contents .= "
		</td>
		<form name=listform method=post action='goods_batch.act.php' onsubmit='return SelectUpdate(this)' target='iframe_act2' ><!--onsubmit='return CheckDelete(this)' -->
		<input type='hidden' name='search_searialize_value' value='".($_GET[mode] == "search" ? urlencode(serialize($_GET)):"")."'>
		<input type='hidden' name='act' value='update'>
		<input type='hidden' name='search_act_total' value='$total'>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
				<tr height=30>
					<td align=left>
					발행쿠폰수 : ".number_format($total)." 개
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
			</table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
				<col width='4%'>
				<col width='5%'>
				<col width='15%'>
				<col width='9%'>
				<col width='*'>
				<col width='19%'>
				<col width='9%'>
				<col width='10%'>
				<tr bgcolor='#cccccc' align=center>
					<td class=s_td height='30'><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td>
					<td class=m_td>순번</td>
					<td class=m_td style='line-height:140%;'>주문일자<br>주문번호</td>
					<td class=m_td style='line-height:140%;'>주문자명<br>회원등급</td>
					<td class=m_td>제품명</td>
					<td class=m_td>쿠폰번호</td>
					<td class=m_td>상태</td>
					<td class=e_td>관리</td>
				</tr>";




if(is_array($coupon_list)){
	foreach($coupon_list as $_key=>$_val)
	{
		$coupon_Arr = explode("-", $_val['coupon_no']);
		$coupon_print = substr($_val['coupon_no'], 0, 15);

		$innerview .= "<tr bgcolor='#ffffff' height='25'>
					<td class='list_box_td list_bg_gray'  rowspan=2><input type=checkbox class=nonborder id='ci_ix' name='ci_ix[]' value='".$_val[ci_ix]."'></td>
					<td class='list_box_td'  rowspan=2>".$no."</td>
					<td class='list_box_td list_bg_gray' >".$_val['regdate']."</td>
					<td class='list_box_td' >".$_val['bname']."</td>
					<td class='list_box_td list_bg_gray' align=left rowspan=2 style='padding:0px 10px'>".$_val['pname']."</td>
					<td class='list_box_td'  rowspan=2>".$coupon_print."
					<input type='text' name='coupon_text[".$_val[ci_ix]."]' id='coupon_text_".$_val[ci_ix]."' maxlength='4' value='".($_val['status'] == SNS_COUPON_STATUS_COMPLETE || $admininfo[admin_level] == 9 ? $coupon_Arr[3] : "")."' style='width:30px;'>
					</td>
					<td class='list_box_td list_bg_gray'  rowspan=2 nowrap><span id='status_text_".$_val[ci_ix]."'>".getSnsCouponStatus($_val['status'])."</span></td>
					<td class='list_box_td'  rowspan=2 nowrap>
					<a href='javascript:void(0);' onclick=\"PoPWindow('./coupon_pop.php?ci_ix=".$_val[ci_ix]."','550','400','coupon_pop')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' align=absmiddle></a>
					<a href='javascript:void(0);' onclick='coupon_update(\"".$_val[ci_ix]."\")'><img src='../images/".$admininfo["language"]."/btn_use_complete.gif' align=absmiddle></a>
					</td>
				</tr>
				<tr bgcolor='#ffffff' height='25'>
					<td class='list_box_td list_bg_gray'  style='border-top:1px solid #ffffff'>".$_val['oid']."</td>
					<td class='list_box_td'  style='border-top:1px solid #efefef'>".($_val['gp_name'] == "" ? "-":$_val['gp_name'])."</td>

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

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>
			";
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

$Contents .= HelpBox("소셜커머스 발행쿠폰관리", $help_text);

$Script .= "
	<script Language='JavaScript' src='./coupon_list.js'></script>\n
";


$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
	$Script .= "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script>";

	$P = new LayOut();
	$P->strLeftMenu = sns_menu();
	$P->addScript = $Script;
	$P->Navigation = "소셜커머스 > 소셜상품 주문관리 > 쿠폰상품 발행관리";
	$P->title = "쿠폰상품 발행관리";
	$P->strContents = $Contents;
	$P->jquery_use = false;

	$P->PrintLayOut();
?>