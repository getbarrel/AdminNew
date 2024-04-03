<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");

//session_start();
$db = new Database;

//결제라인 정보
$db->query( "select adi.disp_name,to_char(aa.approve_date,'YYYY-MM-DD') as approve_date from common_authline_approve aa ,common_authline_detail_info adi where aa.aldt_ix = adi.aldt_ix and aa.ioid = '".$ioid."' order by adi.order_approve asc ");
$common_authline=$db->fetchall();

//발주정보
$db->query("select * from inventory_order io where ioid = '".$ioid."'");
$db->fetch();


$ci_ix = $db->dt[ci_ix];
$limit_priod_s = $db->dt[limit_priod_s];
$limit_priod_e = $db->dt[limit_priod_e];
$total_delivery_price = $db->dt[a_delivery_price]+$db->dt[a_tax]+$db->dt[a_commission];
//입고처 정보
$db->query("SELECT * FROM inventory_customer_info WHERE ci_ix = '".$ci_ix."'");
$db->fetch();

if($db->dt[customer_div]=='8'){//타사입고처
	$db->query("SELECT * FROM inventory_company_detail WHERE ci_ix = '".$ci_ix."'");
	$db->fetch();

	$com_name=$db->dt[com_name];
	$charger=$db->dt[charger];
	$phone=$db->dt[charger_phone1]."-".$db->dt[charger_phone2]."-".$db->dt[charger_phone3];
	$fax=$db->dt[com_fax1]."-".$db->dt[com_fax2]."-".$db->dt[com_fax3];

}elseif($db->dt[customer_div]=='9'){//입점업체
	$company_id=$db->dt[company_id];
	$db->query("SELECT * FROM common_company_detail WHERE company_id = '".$company_id."'");
	$db->fetch();

	$com_name=$db->dt[com_name];
	$charger=$db->dt[represent_name];
	$phone=$db->dt[com_phone];
	$fax=$db->dt[com_fax];
}

//NJOY사업자 정보
$db->query("SELECT * FROM common_company_detail WHERE com_type='A' ");
$db->fetch();

$a_com_name=$db->dt[com_name];
$a_com_number=$db->dt[com_number];
$a_com_ceo=$db->dt[com_ceo];
$a_com_addr=$db->dt[com_addr1]." ".$db->dt[com_addr2];
$a_com_business_type=$db->dt[com_business_category]."/".$db->dt[com_business_status];
$a_com_fax=$db->dt[com_fax];

//발주자 정보
$db->query("SELECT * FROM common_member_detail WHERE code='".$_SESSION[admininfo][charger_ix]."' ");
$db->fetch();

$c_name=$db->dt[name];
$c_tel=$db->dt[tel];
$c_fax=$a_com_fax;

//발주상세 정보
$db->query( "select * from inventory_order_detail od left join inventory_goods g on (od.gid=g.gid) left join inventory_goods_item gi on (od.gi_ix=gi.gi_ix) where ioid = '".$ioid."'");
$order_detail=$db->fetchall();
//print_r($order_detail);


if($act=="excal"){


header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=inventory_order_".$ioid.".xls");
header("Content-Description: PHP5 Generated Data");
header("Content-charset=utf-8" );


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
<head>
<title> javascript </title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<script type="text/javascript">
//<![CDATA[

//]]>
</script>
</head>

<body>

<table cellspacing="0" cellpadding="0" border="0">
	<col width="27" />
	<col width="53" />
	<col width="97" />
	<col width="100" />
	<col width="41" />
	<col width="59" />
	<col width="85" />
	<col width="40" />
	<col width="45" />
	<col width="55" />
	<col width="30" />
	<col width="30" />
	<col width="55" />
	<tr>
		<td valign="center" align="center" style="font-size:40px;font-weight:bold;border-left:0px;border-top:0px;border-bottom:0px" colspan="6" rowspan="3">
			정 상 발 주 서
		</td>
		<td  style="font-size:12px; text-align:center;border:1px solid black;">
			<?=$common_authline[0][disp_name]?>
		</td>
		<td style="font-size:12px; text-align:center;border:1px solid black;" colspan="2" >
			<?=$common_authline[1][disp_name]?>
		</td>
		<td style="font-size:12px; text-align:center;border:1px solid black;" colspan="2" >
			<?=$common_authline[2][disp_name]?>
		</td>
		<td style="font-size:12px; text-align:center;border:1px solid black;" colspan="2">
			<?=$common_authline[3][disp_name]?>
		</td>
	</tr>
	<tr>
		<td style="font-size:12px; text-align:center;border:1px solid black;">
			<?=$common_authline[0][approve_date]?>
		</td>
		<td style="font-size:12px; text-align:center;border:1px solid black;" colspan="2">
			<?=$common_authline[1][approve_date]?>
		</td>
		<td style="font-size:12px; text-align:center;border:1px solid black;" colspan="2">
			<?=$common_authline[2][approve_date]?>
		</td>
		<td style="font-size:12px; text-align:center;border:1px solid black;" colspan="2" >
			<?=$common_authline[3][approve_date]?>
		</td>
	</tr>
	<tr>
		<td height="47" style="border:1px solid black;"></td>
		<td colspan="2" style="border:1px solid black;"></td>
		<td colspan="2" style="border:1px solid black;"></td>
		<td colspan="2" style="border:1px solid black;"></td>
	</tr>
	<tr height="5">
		<td colspan="13"></td>
	</tr>

	<tr>
		<td style="height:20px; font-size:13px; text-align:center; border:1px solid black;" colspan="3" >납품회사</td>
		<td style="font-size:13px;border-right:1px solid #000000;border-top:1px solid #000000;" colspan="3" >NO : <?=$ioid?></td>
		<td style="font-size:13px;border-top:1px solid #000000;border-right:1px solid #000000;" colspan="7">발주회사 : <?=$a_com_name?></td>
	</tr>
	<tr>
		<td style="height:16px; font-size:12px;border-right:1px solid #000000;border-left:1px solid #000000;" colspan="3" >회사명 : <?=$com_name?></td>
		<td style="font-size:12px;border-right:1px solid #000000;" colspan="3">DATE : <?=date("Y-m-d")?></td>
		<td style="font-size:12px;border-right:1px solid #000000;" colspan="7">사업자번호 : <?=$a_com_number?></td>
	</tr>
	<tr>
		<td style="height:16px; font-size:12px;border-right:1px solid #000000;border-left:1px solid #000000;" colspan="3" >담당자 : <?=$charger?></td>
		<td style="font-size:12px;border-right:1px solid #000000;" colspan="3">담당 : <?=$c_name?></td>
		<td style="font-size:12px;border-right:1px solid #000000;" colspan="7">대표자 : <?=$a_com_ceo?></td>
	</tr>
	<tr>
		<td style="height:16px; font-size:12px;border-right:1px solid #000000;border-left:1px solid #000000;" colspan="3" >T E L : <?=$phone?></td>
		<td style="font-size:12px;border-right:1px solid #000000;" colspan="3">TEL : <?=$c_tel?></td>
		<td style="font-size:12px;border-right:1px solid #000000;" colspan="7">사업장주소 : <?=$a_com_addr?></td>
	</tr>
	<tr>
		<td style="height:16px; font-size:12px;border-right:1px solid #000000;border-left:1px solid #000000;border-bottom:1px solid #000000;" colspan="3" >F A X : <?=$fax?></td>
		<td style="font-size:12px;border-right:1px solid #000000;border-bottom:1px solid #000000;" colspan="3">FAX : <?=$c_fax?></td>
		<td style="font-size:12px;border-bottom:1px solid #000000;border-right:1px solid #000000;" colspan="7">업종/업태 : <?=$a_com_business_type?></td>
	</tr>
	<tr height="20">
		<td colspan="13"></td>
	</tr>
	<tr>
		<td colspan="13" style="height:25px;font-size:12px;height:25px;font-weight:bold;">
			1. 다음 물품에 대하여 아래와 같이 납품하여 주시기 바랍니다.
		</td>
	</tr>
	<tr>
		<td style="height:17px;font-size:12px;" colspan="5">납기일 : <?=$limit_priod_s?> ~ <?=$limit_priod_e?></td>
		<td style="font-size:12px;" colspan="8">현지배송료($): <?=$total_delivery_price?></td>
	</tr>
	<tr>
		<td colspan="13" style="height:17px;font-size:12px;">
			(통화 단위는 달러($),Tax는 제품가격에 포함, 구매수수료 및 현지 운송비용 발생시에는 Invoice에 별도 기재 바랍니다.
		</td>
	</tr>
	<tr>
		<td style="height:20px; font-size:12px; text-align:center;border:1px solid #000000;" >순번</td>
		<td style="font-size:12px; text-align:center;border:1px solid #000000;">상품코드</td>
		<td style="font-size:12px; text-align:center;border:1px solid #000000;" colspan="2">상품명</td>
		<td style="font-size:12px; text-align:center;border:1px solid #000000;">style</td>
		<td style="font-size:12px; text-align:center;border:1px solid #000000;">단품코드</td>
		<td style="font-size:12px; text-align:center;border:1px solid #000000;" colspan="2">단품명</td>
		<td style="font-size:12px; text-align:center;border:1px solid #000000;">단가</td>
		<td style="font-size:12px; text-align:center;border:1px solid #000000;">발주수량</td>
		<td style="font-size:12px; text-align:center;border:1px solid #000000;" colspan="2">금액</td>
		<td style="font-size:12px; text-align:center;border:1px solid #000000;">입고수량</td>
	</tr>

	<?
	if(count($order_detail) <= 10)	$count = 10;
	else							$count = count($order_detail);
	for($i=0;$i < $count;$i++){
	?>
	<tr>
		<td align="center" style="height:40px;font-size:12px;border:1px solid #000000;"><?=($i+1)?></td>
		<td align="center" style="font-size:12px;border:1px solid #000000;"><?=$order_detail[$i][gid]?></td>
		<td align="center" style="font-size:12px;border:1px solid #000000;" colspan="2"><?=$order_detail[$i][gname]?></td>
		<td align="center" style="font-size:12px;border:1px solid #000000;"></td>
		<td align="center" style="font-size:12px;border:1px solid #000000;"><?=$order_detail[$i][gi_ix]?></td>
		<td align="left" style="font-size:12px;border:1px solid #000000;" colspan="2"><?=$order_detail[$i][item_name]?></td>
		<td align="right" style="font-size:12px;border:1px solid #000000;"><?=$order_detail[$i][order_coprice]?></td>
		<td align="right" style="font-size:12px;border:1px solid #000000;"><?=$order_detail[$i][order_cnt]?></td>
		<td align="right" style="font-size:12px;border:1px solid #000000;" colspan="2"><?=($order_detail[$i][order_coprice]*$order_detail[$i][order_cnt])?></td>
		<td align="right" style="font-size:12px;border:1px solid #000000;"><?=$order_detail[$i][incom_cnt]?></td>
	</tr>
	<?
	$total_order_cnt += $order_detail[$i][order_cnt];
	$total_order_price += $order_detail[$i][order_coprice]*$order_detail[$i][order_cnt];
	$total_incom_cnt += $order_detail[$i][incom_cnt];
	}
	?>
	<tr>
		<td colspan="8" style="border:1px solid #000000;">&nbsp;</td>
		<td align="center"style="font-size:12px;border:1px solid #000000;">합계</td>
		<td align="right"style="font-size:12px;border:1px solid #000000;"><?=$total_order_cnt?></td>
		<td align="right"style="font-size:12px;border:1px solid #000000;" colspan="2"><?=$total_order_price?></td>
		<td align="right"style="font-size:12px;border:1px solid #000000;"><?=$total_incom_cnt?></td>
	</tr>
	<tr height="5">
		<td colspan="13"></td>
	</tr>
	<tr>
		<td rowspan="2" colspan="2" style="height:25px; font-weight:bold; font-size:12px;">2. 확인자 :</td>
		<td colspan="11" style="height:20px;font-size:12px;border-bottom:1px solid #000000;">창고입고확인 - </td>
	</tr>
	<tr>
		<td colspan="11" style=" height:20px;font-size:12px;border-bottom:1px solid #000000;">
			운영재고확정 -
		</td>
	</tr>
	<tr>
		<td colspan="13" style="height:25px;font-weight:bold;font-size:12px; padding:5px 0 0 0 ;">
			3. 별첨서류(필요시) :
		</td>
	</tr>
	<tr height="15">
		<td colspan="13"></td>
	</tr>
	<tr>
		<td colspan="13" style="height:25px;font-weight:bold;font-size:12px; border:1px solid black;">
			4. 납품시 기재사항
		</td>
	</tr>
	<tr>
		<td colspan="6" style="font-size:12px; height:20px; border-left:1px solid black;">
			입고일 :
		</td>
		<td colspan="7" style="font-size:12px; border-right:1px solid black;">
			업체 인계자 서명 :
		</td>
	</tr>
	<tr>
		<td colspan="6" style="font-size:12px; height:20px; border-left:1px solid black;border-bottom:1px solid black;">
			납기일 :
		</td>
		<td colspan="7" style="font-size:12px; border-right:1px solid black;border-bottom:1px solid black;">
			물류창고 인수자 서명 :
		</td>
	</tr>
	<tr height="5">
		<td colspan="13"></td>
	</tr>
	<tr>
		<td colspan="13" style="height:25px;font-weight:bold;font-size:12px; border:1px solid black;">
			5. 참고사항
		</td>
	</tr>
	<tr>
		<td colspan="13" style="font-size:12px;border:1px solid black;">
			&nbsp;- 입고지 : Incheon Nam-gu Juan 4(sa)-dong&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;228-6 2nd flr 201<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 납품 : 납품 지연시 당사의 담당 MD와 납품처에 즉시 연락바랍니다.<br />
			&nbsp;- 입고통지 : 최소 입고 2시간 전 물류창고에 입고예정 수량 및 도착예정시각을 통지바랍니다.<br />
			&nbsp;- 입고절차 : 입고상품이 발주서상의 단품별로 구분되지 않는 경우는 입고하실 수 없습니다.<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;입고 시 반드시 물류창고 인수자와 상호 검수 후 본 발주서상에 인수자의 서명을 받으시기 바랍니다.<br />
			&nbsp;- 제품불량 : 납품회사는 하자상품 발견 통보 즉시 이를 회수하며, 하자상품에 대한 대금을 100% 반환하여야 합니다.<br />
			&nbsp;- 대금결제 서류 : 대금결제를 위해서는 본 입고증을 운영 담당자에게, Invoice는 발주 담당자에게 보내주십시오.<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(발주서상 발주 담당자 및 물류창고 인수자 서명 필수)<br />
			&nbsp;- 검수 : 본 입고증에 물류창고 인수자의 서명이 있으면 검수 완료된 것으로 간주합니다.<br />
			&nbsp;- 발주서의 대표자 직인은 생략되어 있습니다.
		</td>
	</tr>

</table>
</body>
</html>

<?

}elseif($act=="pdf"){


}


?>