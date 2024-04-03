<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
//print_r($_SESSION);

	$div_type_str = "주 문 서";


header('Pragma: public');
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
header('Content-Type: text/plain; charset=UTF-8');
header('Content-Transfer-Encoding: binary');
header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
header("Content-type: application/x-msexcel");                    // This should work for the rest
header('Content-Disposition: attachment; filename='.iconv("utf-8","CP949",$div_type_str).'.xls');

?>


<div class="es"><html>
<title><?=$div_type_str?></title>
<style>
td		{padding:5px 0;!important;text-align:center;}
.es		{!important; padding:10px 0;}
.esInner	{border:1px solid #000 !important;}	
.black	{background:black;}
div p	{padding:10px 0;}
</style>
<body topmargin=0 leftmargin=0 ><!--onload="Init(document.send_mail);"-->
<?
$db = new Database;
$db2 = new Database;
$db3 = new Database;
$mdb = new Database;
$mdb2 = new Database;

$sql = "select * from inventory_order where ioid = '".$ioid."' ";
$db->query($sql);
$db->fetch();
$ioid = $db->dt[ioid];
$order_charger = $db->dt[order_charger];
$limit_priod = $db->dt[limit_priod];
$ci_ix = $db->dt[ci_ix];
$incom_company_charger  = $db->dt[incom_company_charger];
$total_price = $db->dt[total_price];
$total_add_price = $db->dt[total_add_price];
$delivery_price = $db->dt[delivery_price];
$charger_ix = $db->dt[charger_ix];



$sql = "select * from inventory_order_detail where ioid = '".$ioid."' ";
$mdb->query($sql);
$mdb->fetch();

$pid = $mdb->dt[pid];
$ci_ix = $mdb->dt[ci_ix];
//echo $sql;

$sql = "select *, AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel, AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs from common_member_detail where code = '".$charger_ix."'" ;
$db2->query($sql);
$db2->fetch();
$charger_phone = $db2->dt[tel];
$charger_mobile = $db2->dt[pcs];

$sql = "select ci.*, cd.bank_owner, cd.bank_name, cd.bank_number from inventory_customer_info ci 
					left join inventory_company_detail cd on ci.ci_ix = cd.ci_ix
					where ci.ci_ix = '".$ci_ix."' ";
$mdb2->query($sql);
$mdb2->fetch();

if ($mdb2->dt[how_to_order] == "F" || $mdb2->dt[how_to_order] == ""){
	$how_to_order = "FAX";
} else if ($mdb2->dt[how_to_order] == "E"){
	$how_to_order = "E-mail";
}else if ($mdb2->dt[how_to_order] == "W"){
	$how_to_order = "WEB";
}

$customer_name = $mdb2->dt[customer_name];
$customer_phone = $mdb2->dt[customer_phone];
$customer_fax = $mdb2->dt[customer_fax];
/**
$sql ="select * from common_company_detail 
				where ";
*/
?>					
<table class="es" border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
	<tr>
		<td height="1" width="90"></td>
		<td height="1" width="65"></td>
		<td height="1" width="45"></td>
		<td height="1" width="40"></td>
		<td height="1" width="45"></td>
		<td height="1" width="55"></td>
		<td height="1" width="55"></td>
		<td height="1" width="75"></td>
		<td height="1" width="78"></td>
		<td height="1" width="80"></td>
	
	</tr>
	<tr>
		<td colspan="10" align="center" style="height:42px;font-size:25px;padding:5px 0px;"><?=$div_type_str?></td>
	</tr>
	<tr>
		<td colspan="">일련번호</td>
		<td colspan="4"><?=$ioid?> </td>
		<td align="right"colspan="5" rowspan="2">
			
		</td>
	</tr>
	<tr>
		<td colspan="">수신</td>
		<td colspan="4"><?=$customer_name?> </td>
	</tr>
	<tr>
		<td colspan="" height="22">담당</td>
		<td colspan="4" height="22"><?=$incom_company_charger?> </td>
		<td colspan="2" height="22">사업자번호</td>
		<td colspan="3" height="22"><?=$_SESSION["shopcfg"]["biz_no"]?></td>
	</tr>
	<tr>
		<td colspan="" height="22">TEL</td>
		<td colspan="4" height="22"><?=$customer_phone?> </td>
		<td colspan="2" height="22">회사명/대표</td>
		<td colspan="3" height="22"><?=$_SESSION["shopcfg"]["com_name"]?> / <?=$_SESSION["shopcfg"]["ceo"]?></td>
	</tr>
	<tr>
		<td colspan="" height="22">FAX</td>
		<td colspan="4" height="22"><?=$customer_fax?> </td>
		<td colspan="2" height="22">배송지</td>
		<td colspan="3" height="22"><?=$_SESSION["shopcfg"]["company_address"]?> <br/><?=$_SESSION["shopcfg"]["phone"]?> </td>
	</tr>
	<tr>
	
		<td colspan="" height="22">발주방법</td>
		<td colspan="4" height="22"><?=$how_to_order?> </td>
		<td colspan="2" height="22">담당</td>
		<td colspan="3" height="22"><?=$order_charger?></td>
	</tr>
	<tr>
		<td colspan="" height="22">입금계좌/은행</td>
		<td colspan="4" height="22"><?=$bank_name?>/<?=$bank_number?><br>예금주 : <?=$bank_owner?> </td>
		<td colspan="2" height="22">연락처</td>
		<td colspan="3" height="22"><?=$charger_phone?>/<?=$charger_mobile?></td>
		
	</tr>
	<tr>
		<td colspan="5" height="22">납기일자 : <?=$limit_priod?> 일</td>
		<td colspan="2" height="22"></td>
		<td colspan="3" height="22"></td>
		
	</tr>
	
	<tr>
		<td colspan="10" align="center">
			금액 : <?//changePrice($payment_price)?> <?=number_format(($total_price))?> (VAT 포함), 
			배송비 : <?//changePrice($payment_price)?> <?=number_format(($delivery_price))?>
		</td>
	</tr>


	<tr>
		<th height="34" >품목코드</th>
		<th colspan="5" height="34">품목명</th>
		<th height="34">수량</th>
		<th colspan="" height="34">단가</th>
		<th colspan="" height="34">공급가액</th>
		<th height="34">비고</th>
	</tr>
	<? 
	$sum = 0;
	for($i=0;$i<$mdb->total;$i++){
		$mdb->fetch($i);
	?>
	<tr>
		<td height="34"><?=($mdb->dt[pid])?></td>
		<td  colspan="5" height="34" ><?=$mdb->dt[pname]?><? if($mdb->dt[option_name] != "")?>(<?=str_replace(array("<br>")," ",$mdb->dt[option_name])?>)</td>
		<td height="34"><?=$mdb->dt[order_cnt]?></td>
		<td colspan="" height="34"><?=number_format($mdb->dt[order_coprice])?></td>
		<td colspan="" height="34"><?=number_format(($mdb->dt[order_coprice])*($mdb->dt[order_cnt]))?></td>
		<td colspan="" height="34"></td>
		
	</tr>
	<?
		$sum = $sum + $db->dt[ptprice];
	}
	?>
	
	<!--tr>
		<td colspan="2" height="26">상품 합계금액</td>
		<td></td>
		<td colspan="2"></td>
		<td colspan="2"> </td>
		<td colspan="2"><?=number_format($sum)?></td>
		<td></td>
	</tr>
	
	<tr>
		<td colspan="2"height="26">할인금액</td>
		<td></td>
		<td colspan="2"></td>
		<td colspan="2"></td>
		<td colspan="2" align="right"><?=number_format($use_reserve_price+$use_cupon_price)?></td>
		<td></td>
	</tr>
	
	<tr>
		<td colspan="2" height="26">총 견적 합계금액</td>
		<td></td>
		<td colspan="2"></td>
		<td colspan="2"></td>
		<td colspan="2"><?=number_format($sum-($use_reserve_price+$use_cupon_price))?></td>
		<td></td>
	</tr-->
	<tr>
		<td style="align:left;" colspan="10"><u><strong>※물품 발송시 반드시 거래 명세서 넣어 주시기 바랍니다.</strong></u><br><br>
		* 세금계산서 발행해 주세요. (전자세금계산서 발행 시 <?=$_SESSION["shopcfg"]["email"]?>)<br> 전화 :<?=$_SESSION["shopcfg"]["phone"]?>/ 팩스: <?=$_SESSION["shopcfg"]["fax"]?>
		</td>
	</tr>
</table>
<?
function changePrice($totalprice){

$num1 = array("일","이","삼","사","오","육","칠","팔","구");
$num2 = array("첫","두","세","네","다섯","여섯","일곱","여덟","아홉");
$num3 = array("번째","십","백","천","만","십","천","억");

$len = strlen($totalprice);
$temp = sprintf("%d",$totalprice);
$temp = strrev($temp);
for($i=$len;$i>=0;$i--){
	$num=intval($temp[$i]);
	if($num != 0){
		if($i==0)
			$mstring .= $num2[intval($temp[$i])-1].$num3[$i]." ";
		else
			$mstring .= $num1[intval($temp[$i])-1].$num3[$i]." ";
	}
}
return $mstring;
}
?>