<?
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>발행쿠폰 상세정보</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='pragma' content='no-cache'>

<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
*/

$Script = "
<script language='JavaScript' >
function formSubmit(obj){
	if(obj.lastcoupon.value == '') {
		alert(language_data['sns_coupon_pop.php']['A'][language]);//'쿠폰번호 뒷자리를 입력해주세요'
		return false;
	}
}
</Script>";


$db = new Database;

$where = " WHERE ci.ci_ix = '".$ci_ix."'";
$sql = "SELECT ci.*, od.pname, o.bname, mg.gp_name, date_format(o.date, '%Y-%m-%d') as regdate FROM ".TBL_SNS_COUPON_INFO." ci
	LEFT JOIN ".TBL_SNS_ORDER_DETAIL." od on ci.od_ix = od.od_ix
	LEFT JOIN ".TBL_SNS_ORDER." o on ci.oid = o.oid
	LEFT JOIN ".TBL_SNS_MEMBER." m on ci.code = m.code
	LEFT JOIN ".TBL_SNS_GROUPINFO." mg on m.gp_ix = mg.gp_ix
	$where ";
//echo $sql;
$db->query($sql);
$db->fetch();

$coupon_Arr = explode("-", $db->dt['coupon_no']);
$coupon_print = substr($db->dt['coupon_no'], 0, 15);

if($db->dt[status] == SNS_COUPON_STATUS_COMPLETE) $lastcoupon = $coupon_Arr[3];

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr height=40>
		<td align='left' colspan=2> ".GetTitleNavigation("쿠폰상태 변경", "소셜커머스 > 쿠폰상태 변경", false)."</td>
	</tr>
</TABLE>
<form name='coupon' method='POST' action='./coupon_list.act.php' onsubmit='return formSubmit(this)' >
<input type='hidden' name='act' value='pop_update'>
<input type='hidden' name='ci_ix' value='".$ci_ix."'>
<input type='hidden' name='coupon_print' value='".$coupon_print."'>
<div class='mallstory t_no' style='width:97%;'>
	<div id='member_info_view' style='width:100%;'>
		<table border='0' width='100%' cellspacing='0' cellpadding='0' style='border:5px solid #F8F9FA'>
			<tr>
				<td >
					<table border='0' width='100%' cellspacing='0' cellpadding='0' class='line_color'>
						<tr>
							<td class=leftmenu align='left' width='20%' style='height:18px;font-weight:bold'> 주문일자</td>
							<td bgcolor='#ffffff' align='left' width='30%'>&nbsp;".$db->dt[regdate]."</td>
							<td class=leftmenu align='left'  width='20%'> 주문번호</td>
							<td bgcolor='#ffffff' align='left' width='30%'>&nbsp;".$db->dt[oid]."</td>
						</tr>
						<tr>
							<td class=leftmenu align='left' width='20%' style='height:18px;font-weight:bold'> 주문자명</td>
							<td bgcolor='#ffffff' align='left' width='30%'>&nbsp;".$db->dt[bname]."</td>
							<td class=leftmenu align='left'  width='20%'> 회원등급</td>
							<td bgcolor='#ffffff' align='left' width='30%'>&nbsp;".$db->dt[gp_name]."</td>
						</tr>
						<tr>
							<td class=leftmenu align='left' width='20%' style='height:18px;font-weight:bold'> 제품명</td>
							<td bgcolor='#ffffff' align='left' width='80%' colspan='3'>&nbsp;".$db->dt[pname]."</td>
						</tr>
						<tr>
							<td class=leftmenu align='left' width='20%' style='height:18px;font-weight:bold'> 쿠폰번호</td>
							<td bgcolor='#ffffff' align='left' width='80%' colspan='3'>&nbsp;".$coupon_print." <input type='text' class=textbox name='lastcoupon' maxlength='4' value='".$lastcoupon."' style='width:50px;' validation='true' title='쿠폰번호'></td>
						</tr>
						<tr>
							<td class=leftmenu align='left' width='20%' style='height:18px;font-weight:bold'> 상태</td>
							<td bgcolor='#ffffff' align='left' width='80%' colspan='3'>&nbsp;
							<input type='radio' class='null' name='status' value='".SNS_COUPON_STATUS_READY ."' ".($db->dt[status] == SNS_COUPON_STATUS_READY ? "checked":"")."> 사용대기
							<input type='radio' class='null' name='status' value='".SNS_COUPON_STATUS_EXPIRE ."' ".($db->dt[status] == SNS_COUPON_STATUS_EXPIRE ? "checked":"")."> 기간만료
							<input type='radio' class='null' name='status' value='".SNS_COUPON_STATUS_COMPLETE ."' ".($db->dt[status] == SNS_COUPON_STATUS_COMPLETE ?  "checked":"")."> 사용완료
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div style='width:97%; text-align:center;'>
		<table width='100%' border='0'>
			<tr>
				<td align='left'>

				</td>
				<td align='right'>
					<input type='image' src='../images/".$admininfo["language"]."/bts_modify.gif' style='border:0px' border=0>
					<img src='../images/".$admininfo["language"]."/btn_close.gif' border=0 onClick='self.close();' style='cursor:pointer;'>
				</td>
			</tr>
		</table>
	</div>
</div>
</form>";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "소셜커머스 > 소셜상품 주문관리 > 쿠폰상품 발행관리";
$P->title = "쿠폰상품 발행관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>

