<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
session_start();
$db = new Database;
$tdb = new Database;
$edb = new Database;
$eddb = new Database;
$db->query("SELECT * FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE company_id = '".$company_id."'");

$db->fetch();

$edb->query("select est_ix, est_company, est_title, est_charger, regdate, est_status, est_plan_date, est_delivery_postion, est_etc, case when est_order_method = 1 then '현금' when est_order_method = '2' then '카드' end as est_order_method   from ".TBL_SHOP_ESTIMATES." where est_ix ='$est_ix' ");
$edb->fetch();

$tdb->query("select  sum(totalprice) as estimate_totalprice from ".TBL_SHOP_ESTIMATES_DETAIL." where est_ix ='$est_ix' ");
$tdb->fetch();
$estimate_totalprice = $tdb->dt[estimate_totalprice];


header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=estimate.xls");
header("Content-Description: PHP5 Generated Data");
header( "Content-charset=utf-8" );

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<style>

table td.t {border-top:1px #666666 solid}
table td.b {border-bottom:1px #666666 solid}
table td.l {border-left:1px #666666 solid}
table td.r {border-right:1px #666666 solid}
</style>
</head>

<body>

<table width="650" border="1" cellpadding="3" cellspacing="1" bgcolor="#666666">
	<tr align="center" bgcolor="#FFFFFF">
		<td height="80" colspan="9" class="t b l r" ><strong><font style="font-size:24px">견적서</font></strong></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td width="220" rowspan="5" class="b l r">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td height="80" align="center"><strong><font style="font-size:15px"><?=$edb->dt[est_company]?></font></strong> 귀하</td>
				</tr>
				<tr>
					<td align="center">수신자 : <strong><?=$edb->dt[est_charger]?></strong> 귀하</td>
				</tr>
			</table>
		</td>
		<td width="90" align="center" class="b r" colspan=2>견적번호</td>
		<td width="120" align="center" class="b r" colspan=2>NEO-<?=date("ymd")?></td>
		<td width="90" align="center" class="b r" colspan=2>견적일자</td>
		<td width="130" align="center" class="b r" colspan=2><?=date("Y")?> 년 <?=date("m")?> 월 <?=date("d")?> 일</td>
	</tr>
	<tr>
		<td align="center" bgcolor="#FFFFFF" class="b r">상&nbsp;&nbsp;&nbsp;&nbsp;호</td>
		<td colspan="3" align="center" bgcolor="#FFFFFF" class="b r" colspan=7><?=$db->dt[com_name]?></td>
	</tr>
	<tr>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2>등록번호</td>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2><?=$db->dt[com_number]?></td>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2>대&nbsp;표&nbsp;자</td>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2><?=$db->dt[com_ceo]?></td>
	</tr>
	<tr>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2>업&nbsp;&nbsp;&nbsp;&nbsp;태</td>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2><?=$db->dt[com_business_status]?></td>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2>종&nbsp;&nbsp;&nbsp;&nbsp;목</td>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2><?=$db->dt[com_business_category]?></td>
	</tr>
	<tr>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2>전화번호</td>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2><?=$db->dt[com_phone]?></td>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2>팩스번호</td>
		<td align="center" bgcolor="#FFFFFF" class="b r" colspan=2><?=$db->dt[com_fax]?></td>
	</tr>
	<tr align="center" bgcolor="#FFFFFF" >
		<td colspan="9" class="b r l">아래와 같이 견적하오니 검토 후 회신바랍니다.</td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td colspan="5" class="b r l">견적금액 : &nbsp;&nbsp;&nbsp;&nbsp;<strong>\<?=number_format($estimate_totalprice)?></strong>&nbsp;&nbsp;&nbsp;&nbsp;( <strong><?=price_trans($estimate_totalprice)?></strong> 원정 )</td>
		<td align="center" class="b r" colspan=2>세액처리</td>
		<td align="center" class="b r" colspan=2>부가세 포함</td>
	</tr>
</table><br>

<table width="650" border="1" cellpadding="3" cellspacing="0" bgcolor="#666666" id='estimate2'>
	<tr bgcolor="#FFFFFF">
		<td align="center" class="t b l r">순번</td>
		<td colspan="2" align="center" class="t b  r">품목명</td>
		<td align="center" class="t b r">규격</td>
		<td align="center" class="t b r">단위</td>
		<td align="center" class="t b r">수량</td>
		<td align="center" class="t b r">단가</td>
		<td align="center" class="t b r">세금포함금액</td>
		<td align="center" class="t b r">비고</td>
	</tr>
	<?
	$eddb->query("select  pname, pcount, sellprice, totalprice, etc1 from ".TBL_SHOP_ESTIMATES_DETAIL." where est_ix ='$est_ix' ");
	$eddb->fetch();

	for($i=0;$i < $eddb->total;$i++){
	$eddb->fetch($i);
	?>
	<tr bgcolor="#FFFFFF">
		<td align="center" class="b l r"><?=$i+1?></td>
		<td colspan="2" align="center" class="b r"><?=$eddb->dt[pname]?></td>
		<td align="center" class="b r">&nbsp;</td>
		<td align="center" class="b r">EA</td>
		<td align="center" class="b r"><?=$eddb->dt[pcount]?></td>
		<td align="center" class="b r"><?=number_format($eddb->dt[sellprice],0)?></td>
		<td align="center" class="b r"><?=number_format($eddb->dt[totalprice],0)?></td>
		<td align="center" class="b r"><?=$eddb->dt[etc1]?></td>
	</tr>
	<?
	}
	$aweeknext  = mktime (0,0,0,date("m")  , date("d")+7, date("Y"));
	?>
	<tr bgcolor="#FFFFFF">
		<td colspan="2" align="center" class="b l r">납기일자</td>
		<td colspan="2" align="center" class="b r"><?=$edb->dt[est_plan_date]?></td>
		<td colspan="2" align="center" class="b r">납품장소</td>
		<td colspan="3" align="center" class="b r"><?=$edb->dt[est_delivery_postion]?></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td colspan="2" align="center" class="b l r">유효일자</td>
		<td colspan="2" align="center" class="b r"><?=date("Y년 m월 d일", $aweeknext)?> </td>
		<td colspan="2" align="center" class="b r">결제조건</td>
		<td colspan="3" align="center" class="b r"><?=$edb->dt[est_order_method]?></td>
	</tr>
	<tr bgcolor="#FFFFFF">
		<td colspan="2" align="center" class="b l r">비고</td>
		<td colspan="7" class="b r"><?=$edb->dt[est_etc]?></td>
	</tr>
</table>

</body>
</html>
