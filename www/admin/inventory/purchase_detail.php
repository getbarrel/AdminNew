<?
if($view_type=="email"){
	include_once("../../class/database.class");
}else{
	include_once("../class/layout.class");
}
include_once("inventory.lib.php");


$db = new Database;

$db->query("SELECT * FROM inventory_order where ioid = '".$ioid."'");
$io_info = $db->fetch("object");

$db->query("SELECT * FROM inventory_order_detail where ioid = '".$ioid."'");
$iod_info = $db->fetchall("object");

$db->query("SELECT * FROM common_company_detail where com_type='A' ");
$com_info = $db->fetch("object");

$db->query("SELECT * FROM common_company_detail where company_id='".$io_info[ci_ix]."' ");
$ci_info = $db->fetch("object");

$Contents = "
<table cellspacing=0 cellpadding=0 width='750' align=center border=0 >
	<tr>
		<td>
			<!--타이틀-->
			<table border=0 cellpadding=0 cellspacing=0 width='100%' style='border-collapse: separate; border-spacing: 1px; background: #c5c5c5; border: 1px;'>
				<col width='25%' >
				<col width='50%' >
				<col width='12%' >
				<col width='13%' >
				<tr align='left' >
					<td style='background: #ffffff; text-align: center; height: 60px;' rowspan='3'>
						로고 자동 연동
					</td>
					<td style='background: #ffffff; text-align: center; height: 60px; font-size: 25px; font-weight:bold;' rowspan='3'>
						주 문 발 주 서
					</td>
					<td style='background: #ffffff; text-align: center;'>
						문서분류
					</td>
					<td style='background: #ffffff; text-align: center;'>
						
					</td>
				</tr>
				<tr>
					<td style='background: #ffffff; text-align: center;'>
						작성자
					</td>
					<td style='background: #ffffff; text-align: center;'>
						".$io_info[charger]."
					</td>
				</tr>
				<tr>
					<td style='background: #ffffff; text-align: center;'>
						작성일
					</td>
					<td style='background: #ffffff; text-align: center;'>
						".substr($io_info[regdate],0,10)."
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height='30'></td></tr>
	<tr>
		<td>
			<!--수주처 및 발주처 정보-->
			<table border=0 cellpadding=0 cellspacing=0 width='100%' style='border-collapse: separate; border-spacing: 1px; background: #c5c5c5; border: 1px;'>
				<col width='6%'>
				<col width='6%'>
				<col width='32%'>
				<col width='6%'>
				<col width='13%'>
				<col width='37%'>
				<tr align='left'>
					<td style='background: #f2f2f2; text-align: center; height: 20px; font-weight: bold;' colspan='2'>
						발주번호
					</td>
					<td style='background: #ffffff; text-align: center; height: 20px;'>
						".$io_info[ioid]."
					</td>
					<td style='background: #f2f2f2; text-align: center; font-weight: bold;' rowspan='5'>
						발<br/><br/>주<br/><br/>처
					</td>
					<td style='background: #ffffff; text-align: center;'>
						등록번호
					</td>
					<td style='background: #ffffff; text-align: left; padding-left: 10px;'>
						".$com_info[com_number]."
					</td>
				</tr>
				<tr>
					<td style='background: #f2f2f2; text-align: center; height: 20px; font-weight: bold;' rowspan='4'>
						수<br/><br/>주<br/><br/>처
					</td>
					<td style='background: #ffffff; text-align: center;' rowspan='4' colspan='2'>
						<table cellspacing=0 cellpadding=0 width='90%' border=0 style='margin-left:10px;'>
							<tr>
								<td style='background: #ffffff; text-align: right; height: 40px;'>
									<u>".$ci_info[com_name]."</u> 귀하
								</td>
							</tr>
							<tr>
								<td style='background: #ffffff; text-align: left; height: 15px;'>
									전화 번호 : ".$ci_info[com_phone]."
								</td>
							</tr>
							<tr>
								<td style='background: #ffffff; text-align: left; height: 15px;'>
									팩스 번호 : ".$ci_info[com_fax]."
								</td>
							</tr>
							<tr>
								<td style='background: #ffffff; text-align: left; height: 15px;'>
									담당자 <u> &nbsp;".$ci_info[customer_name]."</u> 귀하
								</td>
							</tr>
						</table>
					</td>
					<td style='background: #ffffff; text-align: center; height: 20px;'>
						상 호
					</td>
					<td style='background: #ffffff; text-align: left; padding-left: 10px;'>
						".$com_info[com_name]."
					</td>
				</tr>
				<tr>
					<td style='background: #ffffff; text-align: center; height: 40px;'>
						사업장주소
					</td>
					<td style='background: #ffffff; text-align: left; padding-left: 10px;'>
						[".$com_info[com_zip]."] ".$com_info[com_addr1]."<br/> ".$com_info[com_addr2]."
					</td>
				</tr>
				<tr>
					<td style='background: #ffffff; text-align: center; height: 20px;'>
						업종/업태
					</td>
					<td style='background: #ffffff; text-align: left; padding-left: 10px;'>
						".$com_info[com_business_category]." / ".$com_info[com_business_status]."
					</td>
				</tr>
				<tr>
					<td style='background: #ffffff; text-align: center; height: 20px;'>
						연락처
					</td>
					<td style='background: #ffffff; text-align: left; padding-left: 10px;'>
						".$com_info[com_phone]."
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height='30'></td></tr>
	<tr>
		<td>
			<!--발주품목 정보-->
			<table border=0 cellpadding=0 cellspacing=1 width='100%' style='border-collapse: separate; border-spacing: 1px; background: #c5c5c5; border: 1px;' >
				<col width=5%>
				<col width=*>
				<col width=15%>
				<col width=7%>
				<col width=7%>
				<col width=12%>
				<col width=12%>
				<col width=12%>
				<tr height='20px' bgcolor='#f2f2f2'>
					<td style='font-weight: bold; text-align: center;'>순번</td>
					<td style='font-weight: bold; text-align: center;'>품목코드</td>
					<td style='font-weight: bold; text-align: center;'>품명</td>
					<td style='font-weight: bold; text-align: center;'>규격</td>
					<td style='font-weight: bold; text-align: center;'>단위</td>
					<td style='font-weight: bold; text-align: center;'>수량</td>
					<!--<td style='font-weight: bold; text-align: center;'>객단가</td>-->
					<td style='font-weight: bold; text-align: center;'>공급가</td>
					<td style='font-weight: bold; text-align: center;'>세액</td>
					<td style='font-weight: bold; text-align: center;'>금액</td>
				</tr>";

				for($i=0;$i < count($iod_info) ;$i++){

                    /*
                    $db->query("select * from inventory_goods_unit where gid = '".$iod_info[$i][gid]."' order by gu_ix ASC");
                    $igu_info = $db->fetchall("object");
                    */

					$Contents .= "
					<tr bgcolor='#ffffff' height=27>
						<td align=center>".($i+1)."</td>
						<td align='left' style='padding-left:3px;'>".$iod_info[$i][gid]."</td>
						<td align='left' style='padding-left:3px;'>".$iod_info[$i][gname]."</td>
						<td align='left' style='padding-left:3px;'>".$iod_info[$i][standard]."</td>
						<td align=center>".getUnit($iod_info[$i][unit], "","","text")."</td>
						<td align=center>".number_format($iod_info[$i][cnt])."</td>
						<!--<td align=center>".number_format($igu_info[0][buying_price])."</td>-->
						<td align=center>".number_format($iod_info[$i][coprice])."</td>
						<td align=center>".number_format($iod_info[$i][tax_price])."</td>
						<td align=center>".number_format($iod_info[$i][buying_price])."</td>
					</tr>";

					$sum_cnt+=$iod_info[$i][cnt];
                    $sum_buying_price_unit += $igu_info[0][buying_price];
					$sum_coprice+=$iod_info[$i][coprice];
					$sum_tax_price+=$iod_info[$i][tax_price];
					$sum_buying_price+=$iod_info[$i][buying_price];
				}

				$Contents .= "
				<tr bgcolor='#ffffff'>
					<td height=30 align=center colspan=5 style='font-weight: bold;'>합계 (+배송비 : ".number_format($io_info[delivery_price]).")</td>
					<td align=center>".number_format($sum_cnt)."</td>
					<!--<td align=center>".number_format($sum_buying_price_unit)."</td>-->
					<td align=center>".number_format($sum_coprice)."</td>
					<td align=center>".number_format($sum_tax_price)."</td>
					<td align=center>".number_format($sum_buying_price)."</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height='30'></td></tr>
	<tr>
		<td>
			<!--기타 정보-->
			<table border=0 cellpadding=0 cellspacing=0 width='100%' style='border-collapse: separate; border-spacing: 1px; background: #c5c5c5; border: 1px;'>
				<col width='20%'>
				<col width='*'>
				<tr align='left'>
					<td style='background: #f2f2f2; text-align: center; height: 20px; font-weight: bold;'>
						납기일자
					</td>
					<td style='background: #ffffff; height: 20px; padding-left:10px;'>
						".$io_info[limit_date]."
					</td>
				</tr>
				<tr align='left'>
					<td style='background: #f2f2f2; text-align: center; height: 20px; font-weight: bold;' rowspan='2'>
						납품장소
					</td>
					<td style='background: #ffffff; height: 20px; padding-left:10px;'>
						".$io_info[delivery_name]."
					</td>
				</tr>
				<tr align='left'>
					<td style='background: #ffffff; height: 20px; padding-left:10px;'>
						[".$io_info[delivery_zip]."] ".$io_info[delivery_addr1]." ".$io_info[delivery_addr2]."
					</td>
				</tr>
				<tr align='left'>
					<td style='background: #f2f2f2; text-align: center; height: 20px; font-weight: bold;'>
						기타사항
					</td>
					<td style='background: #ffffff; height: 20px; padding-left:10px;'>
						".$io_info[msg]."
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height='30'></td></tr>
	<tr>
		<td style='text-align: center; font-size: 16px; font-weight: bold;'>
			위와 같이 발주하오니 계약조건을 준수하여 납품하여 주시기 바랍니다.
		</td>
	</tr>
</table>
";

$Script="
<script type='text/javascript'>
<!--
function change_act(status){

	if(status=='ORC'){
		str='발주예정취소';
	}else{
		str='발주확정';
	}

	if(confirm(str+'로 변경하시겠습니까?')){
		$('input[type=hidden][name=u_status]').val(status);
		$('form[name=order_pop]').submit();
	}
}
//-->
</script>
";

if($view_type=="update"){
	$Contents .= "
	<form name='order_pop' method='post' onsubmit='return CheckFormValue(this)' action='purchase.act.php' target='act'>
	<input type='hidden' name='act' value='status_update'>
	<input type='hidden' name='u_status' value=''>
	<input type='hidden' name='mmode' value='pop'>
	<input type='hidden' name='ioid[]' value='".$ioid."'>

	<table cellspacing=0 cellpadding=0 width='750' align=center border=0 style='margin-top:20px;' >
		<tr>
			<td align='center'>
				<input type='button' value='발주예정취소' onclick=\"change_act('ORC')\" /> <input type='button' value='발주확정' onclick=\"change_act('OC')\" />
			</td>
		</tr>
	</table>
	</form>";
}

if($view_type=="email"){
	echo $Contents;
}else{
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 발주상세내용";
	$P->NaviTitle = "발주상세내용";
	$P->title = "발주상세내용";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
?>
