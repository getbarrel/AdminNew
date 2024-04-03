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

$edb->query("select est_ix, est_company, est_title, est_charger, regdate, est_status, est_plan_date, est_delivery_postion, est_etc, case when est_order_method = 1 then '현금' when est_order_method = '2' then '카드' end as est_order_method   from ".TBL_SHOP_ESTIMATES." where est_ix ='$est_ix' ");
$edb->fetch();

$tdb->query("select  sum(totalprice) as estimate_totalprice from ".TBL_SHOP_ESTIMATES_DETAIL." where est_ix ='$est_ix' ");
$tdb->fetch();
$estimate_totalprice = $tdb->dt[estimate_totalprice];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>세금계산서</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<LINK REL='stylesheet' HREF='../include/admin.css' TYPE='text/css'>
<style>
table.provider td {color:red;background-color:#ffffff}
table.provider td.t {border-top:1px red solid}
table.provider td.b {border-bottom:1px red solid}
table.provider td.l {border-left:1px red solid}
table.provider td.r {border-right:1px red solid}
input.estimate {width:100%;}

table.receiver td {color:blue;background-color:#ffffff}
table.receiver td.t {border-top:1px blue solid}
table.receiver td.b {border-bottom:1px blue solid}
table.receiver td.l {border-left:1px blue solid}
table.receiver td.r {border-right:1px blue solid}


</style>
<Script Language="javascript">
function EstimateReset(frm){
	
	frm.reset();
	frm.act.value="update";
	frm.est_ix.value='<?=est_ix?>';
}
</Script>
</head>

<body>
<table width="640" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td style='padding-bottom:7px;'>	
<?TaxBill("provider","공급자");?>
	</td>
</tr>
<tr><td height=1 style='border-bottom:1px dotted gray'>&nbsp;</td></tr>
<tr>
	<td style='padding-top:20px;'>
<?//TaxBill("receiver","공급받는자");?>
	</td>
</tr>
</table>
</body>
</html>


<?
function TaxBill($type="provider",$type_text=""){
	global $db, $tdb,$edb,$eddb, $est_ix;
?>
<table width="640" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td>
		<table class="<?=$type?>" width="650" border="0" cellpadding="3" cellspacing="0" bgcolor="red">
			<col width=25>
			<col width=65>
			<col width=100>
			<col width=25>
			<col width=100>
			<col width=25>
			<col width=65>
			<col width=100>
			<col width=25>
			<col width=100>
			<tr align="center" bgcolor="#FFFFFF"> 
				<td height="60" colspan="6" class="t b l"><strong><font style="font-size:24px">세금계산서</font> (<?=$type_text?> 보관용)</strong></td>
				<td colspan="4" class="t b" style='padding:0px;padding-left:40px;'>
					<table width="100%" height=100% border="0" cellspacing="0" cellpadding="0">
						<tr> 							
							<td width=33% align="center" bgcolor="#FFFFFF" class="b r l">책&nbsp;&nbsp;번&nbsp;&nbsp;호</td>
							<td width=33% colspan="3" align="center" bgcolor="#FFFFFF" class="b r">권</td>
							<td width=33% colspan="3" align="center" bgcolor="#FFFFFF" class="b r">호</td>
						</tr>
						<tr> 
							<td align="center" bgcolor="#FFFFFF" class="l r">일 련 번 호</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr> 
				<td width=25 height="120" align="center" class="b l r" rowspan=4>공<br>급<br>자</td>
				<td align="center" class="b r">등록번호</td>
				<td colspan=3 align="center" class="b r"><?=$db->dt[com_number]?></td>
				<td width="25" rowspan="4" class="b r">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr> 
							<td height="80" align="center">공<br>급<br>자<br>받<br>는<br>자</td>
						</tr>						
					</table>
				</td>
				<td align="center" class="b r">등록번호</td>
				<td colspan=3 align="center" class="b r"><?=date("ymd")?></td>	
			</tr>						
			<tr> 
				<td align="center" bgcolor="#FFFFFF" class="b r">상&nbsp;&nbsp;&nbsp;&nbsp;호<br>(법인명)</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_name]?></td>
				<td align="center" bgcolor="#FFFFFF" class="b r">성<br>명</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_ceo]?></td>
				<td align="center" bgcolor="#FFFFFF" class="b r">상&nbsp;&nbsp;&nbsp;&nbsp;호<br>(법인명)</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_name]?></td>
				<td align="center" bgcolor="#FFFFFF" class="b r">성<br>명</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_ceo]?></td>	
				<!--td width="90" align="center" class="b r">성명</td>
				<td width="130" align="center" class="b r"><?=date("Y")?> 년 <?=date("m")?> 월 <?=date("d")?> 일</td-->			
			</tr>
			<tr>
				<td align="center" class="b r">사업장주소</td>
				<td colspan=3 align="center" class="b r"><?=$db->dt[com_addr1]." ".$db->dt[com_addr2]?>&nbsp;</td>
				<td align="center" class="b r">사업장주소</td>
				<td colspan=3 align="center" class="b r"><?=$db->dt[com_addr1]." ".$db->dt[com_addr2]?>&nbsp;</td>	
			</tr>
			<tr>
				<td align="center" bgcolor="#FFFFFF" class="b r">업&nbsp;&nbsp;&nbsp;&nbsp;태</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_business_status]?></td>
				<td align="center" bgcolor="#FFFFFF" class="b r">종<br>목</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_business_category]?></td>
				<td align="center" bgcolor="#FFFFFF" class="b r">업&nbsp;&nbsp;&nbsp;&nbsp;태</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_business_status]?></td>
				<td align="center" bgcolor="#FFFFFF" class="b r">종<br>목</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$db->dt[com_business_category]?></td>
			</tr>
			<tr bgcolor="#FFFFFF"> 
				<td colspan="2" align="center" class="b r l">작&nbsp;&nbsp;&nbsp;성</td>
				<td colspan="4"  align="center" class="b r">공&nbsp;&nbsp;급&nbsp;&nbsp;가&nbsp;&nbsp;액</td>
				<td colspan="2" align="center" class="b r">세 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;액</td>
				<td colspan="2" align="center" class="b r">비&nbsp;&nbsp;&nbsp;&nbsp;고</td>
			</tr>
			<tr bgcolor="#FFFFFF" height=60> 
				<td colspan="2" align="center" class="b r l" style="padding:0px;">
					<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
						<tr height="50%">
							<td align="center" bgcolor="#FFFFFF" class="b r">년</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">월</td>
							<td align="center" bgcolor="#FFFFFF" class="b">일</td>
						</tr>
						<tr height="50%">
							<td align="center" bgcolor="#FFFFFF" class="r"><?=date("Y")?>&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td colspan="4"  align="center" class="b r" style="padding:0px;">
					<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
						<tr height="50%">
							<td align="center" bgcolor="#FFFFFF" class="b r">공란수</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">백</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">십</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">억</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">천</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">백</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">십</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">만</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">천</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">백</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">십</td>
							<td align="center" bgcolor="#FFFFFF" class="b">일</td>
						</tr>
						<tr height="50%">
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>							
							<td align="center" bgcolor="#FFFFFF" class="">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td colspan="2" align="center" class="b r" style="padding:0px;">
					<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
						<tr height="50%">
							<td align="center" bgcolor="#FFFFFF" class="b r">십</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">억</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">천</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">백</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">십</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">만</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">천</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">백</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">십</td>
							<td align="center" bgcolor="#FFFFFF" class="b">일</td>
						</tr>
						<tr height="50%">
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>							
							<td align="center" bgcolor="#FFFFFF" class="">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td colspan="2" align="center" bgcolor="#FFFFFF" class="b r">&nbsp;</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td><form name='estimate_edit_form' action='estimate.act.php' method='post'>
		<input type='hidden' name='act' value='update'>
		<input type='hidden' name='est_ix' value='<?=$est_ix?>'>
		<table class="<?=$type?>"  width="650" border="0" cellpadding="3" cellspacing="0" >
			<tr bgcolor="#FFFFFF"> 
				<td width=2% align="center" class="b l r" >월</td>
				<td width=2% align="center" class="b r" >일</td>
				<td width=40% colspan="2" align="center" class="b  r">품목 및 규격</td>								
				<td width=10% align="center" class="b r">수량</td>
				<td width=10% align="center" class="b r">단가</td>
				<td width=10% align="center" class="b r">공급가액</td>
				<td width=10% align="center" class="b r">세&nbsp;&nbsp;&nbsp;&nbsp;액</td>
				<td width=15% align="center" class="b r">비고</td>
			</tr>
			<?
			$eddb->query("select  estd_ix, pname, pcount, sellprice, totalprice, etc1 from ".TBL_SHOP_ESTIMATES_DETAIL." where est_ix ='$est_ix' ");
			$eddb->fetch();
			
			for($i=0;$i < $eddb->total;$i++){
			$eddb->fetch($i);
			?>
			<tr bgcolor="#FFFFFF"> 
				<td align="center" class="b l r">&nbsp;<input type='hidden' name='estd_ix[]' value='<?=$eddb->dt[estd_ix]?>'></td>
				<td align="center" class="b r">&nbsp;</td>
				<td colspan="2" align="center" class="b r"><input type='text' class='estimate' name='pname_<?=$eddb->dt[estd_ix]?>' value='<?=$eddb->dt[pname]?>'></td>
				<td align="center" class="b r"><input type='text' class='estimate' name='pcount_<?=$eddb->dt[estd_ix]?>' value='<?=$eddb->dt[pcount]?>'></td>
				<td align="center" class="b r"><input type='text' class='estimate' name='sellprice_<?=$eddb->dt[estd_ix]?>' value='<?=number_format($eddb->dt[sellprice],0)?>'></td>
				<td align="center" class="b r"><input type='text' class='estimate' name='totalprice_<?=$eddb->dt[estd_ix]?>' value='<?=number_format($eddb->dt[totalprice],0)?>'></td>
				<td align="center" class="b r"><input type='text' class='estimate' name='totalprice_<?=$eddb->dt[estd_ix]?>' value='<?=number_format($eddb->dt[totalprice]*0.1,0)?>'></td>
				<td align="center" class="b r"><input type='text' class='estimate' name='etc1_<?=$eddb->dt[estd_ix]?>' value='<?=$eddb->dt[etc1]?>'></td>
			</tr>
			<?
			}
			$aweeknext  = mktime (0,0,0,date("m")  , date("d")+7, date("Y"));
			?>
			<tr bgcolor="#FFFFFF" height=60 > 
				<td colspan="9" align="center" class="b l r" style="padding:0px;">
					<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
						<tr height="50%">
							<td align="center" bgcolor="#FFFFFF" class="b r">합계금액</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">현금</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">수표</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">어음</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">외상미수금</td>
							<td align="center" bgcolor="#FFFFFF" class="" width=130 rowspan=2>이 금액을 <b>영수</b>함</td>
						</tr>
						<tr height="50%">
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr></form>			
		</table>	
	</td>
</tr>
</table>	
<?	
}
?>