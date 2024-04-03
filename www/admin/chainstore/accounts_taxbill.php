<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");


$db = new Database;
$tdb = new Database;
$edb = new Database;
$eddb = new Database;




$db->query("SELECT com_name, com_number, com_business_status, com_ceo, com_business_category, com_addr1, com_addr2 FROM ".TBL_COMMON_COMPANY_DETAIL." ccd  WHERE com_type = 'A' ");
$db->fetch();

if($db->total){
	$buyer = $db->fetch(0);
}
if($ac_ix != ""){
	$sql = "SELECT c.com_number, c.com_business_status, c.com_ceo, c.com_business_category, c.com_addr1, com_addr2 ,c.com_name, a.company_id ,ac_cnt as sell_cnt, sell_total_price as sell_total_ptprice, ac_price,ac_date, a.taxbill_yn
	FROM ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_SHOP_ACCOUNTS." a
	where c.company_id = '$company_id' and ac_ix = '$ac_ix'   ";
	//echo $sql;
}else{
	$sql = "SELECT c.com_number, c.com_business_status, c.com_ceo, c.com_business_category, c.com_addr1, com_addr2,c.com_name, p.admin as company_id ,count(od.pcnt) as sell_cnt, sum(od.ptprice) as sell_total_ptprice,sum(od.ptprice*(100-od.commission)/100) as		sell_total_coprice,
	sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, od.regdate as order_com_date,avg(od.commission) as avg_commission
	FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od, ".TBL_SHOP_ORDER." o, ".TBL_COMMON_COMPANY_DETAIL." c
	where p.id = od.pid and o.oid = od.oid and p.admin = '$company_id' and od.status in ('".ORDER_STATUS_DELIVERY_COMPLETE."')
	and date_format(od.dc_date,'%Y%m%d') <= $endDate and c.company_id = '$company_id' AND p.product_type NOT IN ('4','5','6') group by admin   ";
	//echo $sql;
}
$db->query($sql);
$db->fetch();
if($db->total){
	$seller = $db->fetch(0);
}
if($mode == "excel"){
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=estimate.xls");
	header( "Content-charset=euc-kr" );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>세금계산서</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='pragma' content='no-cache'>

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
INPUT.provider {color:red; border:0px;text-align:center}
INPUT.receiver {color:blue; border:0px;text-align:center}

</style>
</head>
<?
}else{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>세금계산서</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='pragma' content='no-cache'>
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
INPUT.provider {color:red; border:0px;text-align:center}
INPUT.receiver {color:blue; border:0px;text-align:center}

</style>
<Script Language="javascript">

var initBody ;
function beforePrint() {
	initBody = document.body.innerHTML; document.body.innerHTML = printarea.innerHTML;
}
function afterPrint() {
	document.body.innerHTML = initBody;
}
function printArea() {
	window.print();
}
window.onbeforeprint = beforePrint; window.onafterprint = afterPrint;

function EstimateReset(frm){

	frm.reset();
	frm.act.value="update";
	frm.est_ix.value='<?=est_ix?>';
}

function printTaxBill(obj){
	obj.style.display = 'none';
<?
/*
if($seller[taxbill_yn] != "Y"){
?>
	document.taxbill.action = 'accounts.act.php';
	//document.taxbill.target = 'act';
	document.taxbill.submit();
<?
}
*/
?>
	print();
}


function sendmailTaxBill(oid){

	if(confirm('정말로 메일을 발송하시겠습니까? 발송하시게 되면 메일과 함께 세금계산서가 엑셀파일로 고객님께 전송되게 됩니다.')){
		document.location.href='orders.act.php?act=send_mail&oid='+oid;
	}
}

</Script>
</head>
<?
}
?>
<body topmargin=5 >
<table width="640" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td style='padding-bottom:7px;'>
	<div style="overflow:auto;" id='printarea'>
	<table width="640" border="0" cellspacing="0" cellpadding="0">
		<?
		if($_SESSION[admininfo][admin_level] == 8){
		?>
		<tr>
			<td style='padding-bottom:7px;'>
		<?TaxBill("provider","공급자");?>
			</td>
		</tr>

			<div style="position:absolute;">
		<div style="position:relative;top:120px;left:600px">
			<img src='http://www.thezoom.co.kr/data/demo/images/stamp_d02b37324dd0b08f6bc0f3847673e7d5.gif'>
		</div>
		</div>

		<?
		}else if($_SESSION[admininfo][admin_level] == 9){
		?>
		<!--tr><td height=1 style='border-bottom:1px dotted gray'>&nbsp;</td></tr-->
		<tr>
			<td style='padding-top:20px;'>
		<?TaxBill("receiver","공급받는자");?>
			</td>
		</tr>
		<div style="position:absolute;">
		<div style="position:relative;top:120px;left:600px">
			<img src='http://www.thezoom.co.kr/data/demo/images/stamp_d02b37324dd0b08f6bc0f3847673e7d5.gif'>
		</div>
		</div>
		<?
		}
		?>
	</table>
	</div>
	</td>
</tr>
<tr height=30>
	<td align=center>
		<img src="../images/<?=$admininfo["language"]?>/btn_print.gif" border=0 onclick="printTaxBill(this);" style="cursor:pointer;">
		<!--img src="../image/btn_email.gif" border=0 onclick="sendmailTaxBill('<?=$oid?>')" style="cursor:hand;"-->
	</td>
</tr></form>
</table>
</body>
</html>


<?
function TaxBill($type="provider",$type_text=""){
	global $seller, $buyer, $ptprice,$edb,$eddb, $oid,$ac_ix;
	$code = rand(100000,999999);
	//echo $code;
//print_r($buyer);
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
					<table width="100%" height=66 border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width=33% align="center" bgcolor="#FFFFFF" class="b r l">책&nbsp;&nbsp;번&nbsp;&nbsp;호</td>
							<td width=33% colspan="3" align="center" bgcolor="#FFFFFF" class="b r">권</td>
							<td width=33% colspan="3" align="center" bgcolor="#FFFFFF" class="b r">호</td>
						</tr>
						<tr>
							<td align="center" bgcolor="#FFFFFF" class="l r">일 련 번 호</td>
							<td align="center" bgcolor="#FFFFFF" class="r"><?=substr($code,0,1)?></td>
							<td align="center" bgcolor="#FFFFFF" class="r"><?=substr($code,1,1)?></td>
							<td align="center" bgcolor="#FFFFFF" class="r"><?=substr($code,2,1)?></td>
							<td align="center" bgcolor="#FFFFFF" class="r"><?=substr($code,3,1)?></td>
							<td align="center" bgcolor="#FFFFFF" class="r"><?=substr($code,4,1)?></td>
							<td align="center" bgcolor="#FFFFFF" class="r"><?=substr($code,5,1)?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width=25 height="120" align="center" class="b l r" rowspan=4>공<br>급<br>자</td>
				<td align="center" class="b r">등록번호</td>
				<td colspan=3 align="center" class="b r"><?=$seller[com_number]?></td>
				<td width="25" rowspan="4" class="b r">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td height="80" align="center">공<br>급<br>자<br>받<br>는<br>자</td>
						</tr>
					</table>
				</td>
				<td align="center" class="b r">등록번호</td>
				<td colspan=3 align="center" class="b r"><?=$buyer[com_number]?>&nbsp;</td>
			</tr>
			<tr>
				<td align="center" bgcolor="#FFFFFF" class="b r">상&nbsp;&nbsp;&nbsp;&nbsp;호<br>(법인명)</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$seller[com_name]?></td>
				<td align="center" bgcolor="#FFFFFF" class="b r">성<br>명</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$seller[com_ceo]?></td>
				<td align="center" bgcolor="#FFFFFF" class="b r">상&nbsp;&nbsp;&nbsp;&nbsp;호<br>(법인명)</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$buyer[com_name]?>&nbsp;</td>
				<td align="center" bgcolor="#FFFFFF" class="b r">성<br>명</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$buyer[com_ceo]?>&nbsp;</td>

				<!--td width="90" align="center" class="b r">성명</td>
				<td width="130" align="center" class="b r"><?=date("Y")?> 년 <?=date("m")?> 월 <?=date("d")?> 일</td-->
			</tr>
			<tr>
				<td align="center" class="b r">사업장주소</td>
				<td colspan=3 align="center" class="b r"><?=$seller[com_addr1]." ".$seller[com_addr2]?>&nbsp;</td>
				<td align="center" class="b r">사업장주소</td>
				<td colspan=3 align="center" class="b r"><?=$buyer[com_addr1]." ".$seller[com_addr2]?>&nbsp;</td>
			</tr>
			<tr>
				<td align="center" bgcolor="#FFFFFF" class="b r">업&nbsp;&nbsp;&nbsp;&nbsp;태</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$seller[com_business_status]?>&nbsp;</td>
				<td align="center" bgcolor="#FFFFFF" class="b r">종<br>목</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$seller[com_business_category]?>&nbsp;</td>
				<td align="center" bgcolor="#FFFFFF" class="b r">업&nbsp;&nbsp;&nbsp;&nbsp;태</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$buyer[com_business_status]?>&nbsp;</td>
				<td align="center" bgcolor="#FFFFFF" class="b r">종<br>목</td>
				<td align="center" bgcolor="#FFFFFF" class="b r"><?=$buyer[com_business_category]?>&nbsp;</td>
			</tr>
			<tr bgcolor="#FFFFFF">
				<td colspan="2" align="center" class="b r l">작&nbsp;&nbsp;&nbsp;성</td>
				<td colspan="4"  align="center" class="b r">공&nbsp;&nbsp;급&nbsp;&nbsp;가&nbsp;&nbsp;액</td>
				<td colspan="2" align="center" class="b r">세 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;액</td>
				<td colspan="2" align="center" class="b r">비&nbsp;&nbsp;&nbsp;&nbsp;고</td>
			</tr>
			<?
				if(!$ac_ix){
					$edate = $_GET["endDate"];
				}else{
					$edate = $seller[ac_date];
				}

			//echo $_GET["endDate"];
			?>
			<tr bgcolor="#FFFFFF" height=60>
				<td colspan="2" align="center" class="b r l" style="padding:0px;">
					<table width="100%" height="59" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td align="center" bgcolor="#FFFFFF" class="b r">년</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">월</td>
							<td align="center" bgcolor="#FFFFFF" class="b">일</td>
						</tr>
						<!--tr height="50%">
							<td align="center" bgcolor="#FFFFFF" class="r">2008&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;12</td>
							<td align="center" bgcolor="#FFFFFF" class="">&nbsp;31</td>
						</tr-->

						<tr>
							<td align="center" bgcolor="#FFFFFF" class="r"><?=substr($edate,0,4)?>&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=substr($edate,4,2)?></td>
							<td align="center" bgcolor="#FFFFFF" class="">&nbsp;<?=substr($edate,6,2)?></td>
						</tr>

					</table>
				</td>
				<td colspan="4"  align="center" class="b r" style="padding:0px;">
					<table width="100%" height="59" border="0" cellspacing="0" cellpadding="0">
						<tr>
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
						<?

					//echo $total_price_f;
					if($ac_ix == ""){
						$ac_price = $seller[sell_total_coprice];//$seller[sell_total_ptprice] - ($seller[sell_total_ptprice] * ($seller[avg_commission]/100));
						$minus = 1.1;
						$total_price_f = round($ac_price / $minus);
						$total_price = str_split(round($total_price_f),1);
						$price_count = count($total_price);
						$gap_price_count = 11 - $price_count;
						$tax_price_f = $ac_price-$total_price_f;//round($total_price_f*0.1);
						$tax_price = str_split(round($tax_price_f),1);
						$tax_count = count($tax_price);
					}else{

						$ac_price = $seller[ac_price];
						//echo $ac_price;
						$minus = 1.1;
						$total_price_f = round($ac_price / $minus);
						$total_price = str_split($total_price_f,1);
						$price_count = count($total_price);
						$gap_price_count = 11 - $price_count;
						$tax_price_f = $ac_price-$total_price_f;//round($total_price_f*0.1);
						$tax_price = str_split(round($tax_price_f),1);
						$tax_count = count($tax_price);
					}

						//echo $price_count;
						//print_r($total_price);
						?>
						<tr>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$gap_price_count?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$total_price[$price_count-11]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$total_price[$price_count-10]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$total_price[$price_count-9]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$total_price[$price_count-8]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$total_price[$price_count-7]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$total_price[$price_count-6]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$total_price[$price_count-5]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$total_price[$price_count-4]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$total_price[$price_count-3]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$total_price[$price_count-2]?></td>
							<td align="center" bgcolor="#FFFFFF" class="">&nbsp;<?=$total_price[$price_count-1]?></td>
						</tr>
					</table>
				</td>
				<td colspan="2" align="center" class="b r" style="padding:0px;">
					<table width="100%" height="59" border="0" cellspacing="0" cellpadding="0">
						<tr>
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
						<tr>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$tax_price[$tax_count-10]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$tax_price[$tax_count-9]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$tax_price[$tax_count-8]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$tax_price[$tax_count-7]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$tax_price[$tax_count-6]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$tax_price[$tax_count-5]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$tax_price[$tax_count-4]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$tax_price[$tax_count-3]?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=$tax_price[$tax_count-2]?></td>
							<td align="center" bgcolor="#FFFFFF">&nbsp;<?=$tax_price[$tax_count-1]?></td>
						</tr>
					</table>
				</td>
				<td colspan="2" align="center" bgcolor="#FFFFFF" class="b r">&nbsp;</td>
			</tr>
		</table>

	</td>
</tr>
<tr>
	<td><form name='taxbill' action='' method='post'>
		<input type='hidden' name='act' value='account'>
		<input type='hidden' name='company_id' value='<?=$seller[company_id]?>'>
		<input type='hidden' name='month' value='<?=substr($_GET["endDate"],4,2)?>'>
		<input type='hidden' name='shipping_free' value='<?=$seller[shipping_price]?>'>
		<input type='hidden' name='sDate' value='<?=$_GET["startDate"]?>'>
		<input type='hidden' name='eDate' value='<?=$edate?>'><?//=$_GET["endDate"]?>
		<input type='hidden' name='f_ac_ix' value='<?=$_GET["ac_ix"]?>'>
		<IFRAME id=act name=act src="" frameBorder=0 width=0 height=0 scrolling=no style="display:none;"></IFRAME>
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

			<!--tr bgcolor="#FFFFFF">
				<td align="center" class="b l r">&nbsp;12</td>
				<td align="center" class="b r">&nbsp;31</td>
				<td colspan="2" align="left" class="b r">12월 상품판매 정산금액</td-->

				<td align="center" class="b l r">&nbsp;<?=substr($edate,4,2)?></td>
				<td align="center" class="b r">&nbsp;<?=substr($edate,6,2)?></td>
				<td colspan="2" align="left" class="b r"><?=substr($edate,4,2)?>월 상품판매 정산금액</td>

				<td align="center" class="b r">&nbsp;</td>
				<td align="center" class="b r">&nbsp;</td>
				<td align="center" class="b r"><input type='text' class='<?=$type?>' name='ac_price' size=12 value='<?=number_format($total_price_f)?>'></td>
				<td align="center" class="b r"><input type='text' class='<?=$type?>' name='ac_per_price' size=10 value='<?=number_format($tax_price_f,0)?>'></td>
				<td align="center" class="b r">&nbsp;</td>
			</tr>
			<?

			$aweeknext  = mktime (0,0,0,date("m")  , date("d")+7, date("Y"));
			?>
			<tr bgcolor="#FFFFFF" height=60 >
				<td colspan="9" align="center" class="b l r" style="padding:0px;">
					<table width="100%" height="59" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td align="center" bgcolor="#FFFFFF" class="b r">합계금액</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">현금</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">수표</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">어음</td>
							<td align="center" bgcolor="#FFFFFF" class="b r">외상미수금</td>
							<td align="center" bgcolor="#FFFFFF" class="" width=130 rowspan=2>이 금액을 <b>영수</b>함</td>
						</tr>
						<tr>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;<?=number_format($total_price_f+$tax_price_f,0)?></td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
							<td align="center" bgcolor="#FFFFFF" class="r">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
<?
}
?>