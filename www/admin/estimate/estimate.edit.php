<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
session_start();
$db = new Database;
$tdb = new Database;
$edb = new Database;
$eddb = new Database;
$db->query("SELECT * FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE company_id = '".$admininfo[company_id]."'");

$db->fetch();

$edb->query("select * from ".TBL_SHOP_ESTIMATES." where est_ix ='$est_ix' ");
$edb->fetch();

$tdb->query("select  sum(totalprice) as estimate_totalprice from ".TBL_SHOP_ESTIMATES_DETAIL." where est_ix ='$est_ix' ");

$tdb->fetch();
$estimate_totalprice = $tdb->dt[estimate_totalprice];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>견적서</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<LINK REL='stylesheet' HREF='../include/admin.css' TYPE='text/css'>
<style>

table td.t {border-top:1px #666666 solid}
table td.b {border-bottom:1px #666666 solid}
table td.l {border-left:1px #666666 solid}
table td.r {border-right:1px #666666 solid}
input.estimate {width:100%;}
</style>
<Script Language="javascript">
function EstimateReset(frm){
	
	frm.reset();
	frm.act.value="update";
	frm.est_ix.value='<?=est_ix?>';
}

function sendmailEstimate(est_ix){
	
	if(confirm('정말로 메일을 발송하시겠습니까? 발송하시게 되면 메일과 함께 견적서가 엑셀파일로 고객님께 전송되게 됩니다.')){
		document.location.href='estimate.act.php?act=send_mail&est_ix='+est_ix;
	}
}


function changeEstimateinfo(id){
	var frm = document.estimate_edit_form;
	var pcount = eval('frm.pcount_'+id+'.value');
	if(pcount != "")
		pcount = parseInt(pcount);	
		
	var sellprice = eval('frm.sellprice_'+id+'.value');
	if(sellprice != "")
		sellprice = parseInt(sellprice);	
		
	var totalprice = eval('frm.totalprice_'+id);
	var tax = eval('frm.tax_'+id);
	
	
	totalprice.value =sellprice * pcount;
	
	tax.value =parseInt(sellprice * pcount *0.1);
}
</Script>
</head>

<body>
<table width="650" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td>
		<table width="650" border="0" cellpadding="3" cellspacing="0" bgcolor="#666666">
			<tr align="center" bgcolor="#FFFFFF"> 
				<td height="80" colspan="5" class="t b l r"><strong><font style="font-size:24px">견적서</font></strong></td>
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
				<td width="90" align="center" class="b r">견적번호</td>
				<td width="120" align="center" class="b r">NEO-<?=date("ymd")?></td>
				<td width="90" align="center" class="b r">견적일자</td>
				<td width="130" align="center" class="b r"><?=date("Y")?> 년 <?=date("m")?> 월 <?=date("d")?> 일</td>
			</tr>
			<tr> 
				<td align="center" bgcolor="#FFFFFF" class="b r">상&nbsp;&nbsp;&nbsp;&nbsp;호</td>
				<td colspan="3" align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_name]?></td>
			</tr>
			<tr> 
				<td align="center" bgcolor="#FFFFFF" class="b r">등록번호</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_number]?></td>
				<td align="center" bgcolor="#FFFFFF" class="b r">대&nbsp;표&nbsp;자</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_ceo]?></td>
			</tr>
			<tr>
				<td align="center" bgcolor="#FFFFFF" class="b r">업&nbsp;&nbsp;&nbsp;&nbsp;태</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_business_status]?></td>
				<td align="center" bgcolor="#FFFFFF" class="b r">종&nbsp;&nbsp;&nbsp;&nbsp;목</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_business_category]?></td>
			</tr>
			<tr> 
				<td align="center" bgcolor="#FFFFFF" class="b r">전화번호</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_phone]?></td>
				<td align="center" bgcolor="#FFFFFF" class="b r">팩스번호</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_fax]?></td>
			</tr>
			<tr align="center" bgcolor="#FFFFFF" > 
				<td colspan="5" class="b r l">아래와 같이 견적하오니 검토 후 회신바랍니다.</td>
			</tr>
			<tr bgcolor="#FFFFFF"> 
				<td colspan="3" class="b r l">견적금액 : &nbsp;&nbsp;&nbsp;&nbsp;<strong>\<?=number_format($estimate_totalprice*1.1)?></strong>&nbsp;&nbsp;&nbsp;&nbsp;( <strong><?=price_trans($estimate_totalprice*1.1)?></strong> 원정 )</td>
				<td align="center" class="b r">세액처리</td>
				<td align="center" class="b r">부가세 포함</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td height="3"></td>
</tr>
<tr>
	<td><form name='estimate_edit_form' action='estimate.act.php' method='post'>
		<input type='hidden' name='act' value='update'>
		<input type='hidden' name='est_ix' value='<?=$est_ix?>'>
		<table width="650" border="0" cellpadding="3" cellspacing="0" >
			<tr bgcolor="#FFFFFF"> 
				<td width=5% align="center" class="t b l r" >순번</td>
				<td width=40% colspan="2" align="center" class="t b  r">품목명</td>
				<td width=5% align="center" class="t b r">규격</td>
				<td width=5% align="center" class="t b r">단위</td>
				<td width=10% align="center" class="t b r">수량</td>
				<td width=10% align="center" class="t b r">단가</td>
				<td width=10% align="center" class="t b r" nowrap>VAT</td>
				<td width=15% align="center" class="t b r">비고</td>
			</tr>
			<?
			$eddb->query("select  estd_ix, pname, pcount, sellprice, totalprice, etc1 from ".TBL_SHOP_ESTIMATES_DETAIL." where est_ix ='$est_ix' ");
			$eddb->fetch();
			
			for($i=0;$i < $eddb->total;$i++){
			$eddb->fetch($i);
			?>
			<tr bgcolor="#FFFFFF"> 
				<td align="center" class="b l r"><?=$i+1?><input type='hidden' name='estd_ix[]' value='<?=$eddb->dt[estd_ix]?>'></td>
				<td colspan="2" align="center" class="b r"><input type='text' class='estimate' name='pname_<?=$eddb->dt[estd_ix]?>' value='<?=$eddb->dt[pname]?>'></td>
				<td align="center" class="b r">&nbsp;</td>
				<td align="center" class="b r">EA</td>
				<td align="center" class="b r"><input type='text' class='estimate' onkeyup="changeEstimateinfo('<?=$eddb->dt[estd_ix]?>');"  name='pcount_<?=$eddb->dt[estd_ix]?>' value='<?=$eddb->dt[pcount]?>'></td>
				<td align="center" class="b r"><input type='text' class='estimate' onkeyup="changeEstimateinfo('<?=$eddb->dt[estd_ix]?>');" name='sellprice_<?=$eddb->dt[estd_ix]?>' style='text-align:right;' value='<?=$eddb->dt[sellprice]?>'></td>
				<td align="center" class="b r">
					<input type='text' class='estimate' name='totalprice_<?=$eddb->dt[estd_ix]?>'  style='text-align:right;border:0px;display:none;' value='<?=$eddb->dt[totalprice]?>'>
					<input type='text' class='estimate' name='tax_<?=$eddb->dt[estd_ix]?>'  style='text-align:right;border:0px;' value='<?=$eddb->dt[totalprice]*0.1?>' readonly>
				</td>
				<td align="center" class="b r"><input type='text' class='estimate' name='etc1_<?=$eddb->dt[estd_ix]?>' value='<?=$eddb->dt[etc1]?>'></td>
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
				<td colspan="7" class="b r"><?=$edb->dt[est_etc]?>&nbsp;</td>
			</tr>
			<tr height=50 align=center>
				<td colspan=9>
					<input type=image src='../images/btn/ok.gif' aligb=absmiddle border=0>
					<a href="javascript:EstimateReset(document.forms.estimate_edit_form);"><img src='../images/btn/cancel.gif' aligb=absmiddle border=0></a>
					<a href="estimate.view.php?est_ix=<?=$est_ix?>"><img src='../image/btn_print.gif' aligb=absmiddle border=0></a>
					<a href="javascript:sendmailEstimate('<?=$est_ix?>')"><img src='../image/btn_email.gif' aligb=absmiddle border=0></a>
					
				</td>	
			</tr>
		</table>
	</form>
	</td>
</tr>
</table>
</body>
</html>
