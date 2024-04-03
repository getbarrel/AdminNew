<?
include("../class/layout.class");

if ($vToYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", time()-84600*(date("d")-1));
	$eDate = date("Y/m/d", time()+84600*(31-date("d")));

	$startDate = date("Ymd", time()-84600*(date("d")-1));
	$endDate = date("Ymd", time()+84600*(31-date("d")));

	//if($admininfo[admin_level] == 8){
		$eDate = "2009/01/31";
		$endDate = "20090131";
	//}
}else{
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());

	$sDate = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate = $vToYY."/".$vToMM."/".$vToDD;
	$startDate = $vFromYY.$vFromMM.$vFromDD;
	$endDate = $vToYY.$vToMM.$vToDD;
}



$db = new Database;


$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." WHERE mall_ix='".$admininfo[mall_ix]."' ");
$db->fetch();

$account_priod = $db->dt[account_priod];

$where .= "and date_format(od.dc_date,'%Y%m%d') <= $endDate ";



if($admininfo[admin_level] == 9){

	if($company_name != "") $where .= " and ccd.com_name  LIKE '%$company_name%'";


		$sql = "create temporary table shop_delivery_account_tmp ENGINE = MEMORY
			select o.oid , od.company_id ,  ccd.com_name as company_name,bank_name,bank_number,bank_owner,o.delivery_price  , od.one_delivery_price ,  delivery_free_price,  sum(od.ptprice) as sum_ptprice, count(*) as cnt
		 from shop_order o, shop_order_detail od , ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd 
		where o.delivery_price != 0 and o.dac_ix is null and o.oid = od.oid and od.company_id = ccd.company_id and csd.company_id = ccd.company_id
		and od.delivery_type = 'CD' and od.status = 'AC' $where
		group by o.oid, od.company_id
		having  sum(od.ptprice) < delivery_free_price
		order by od.company_id asc, date desc";
		//echo $sql;
		$db->query($sql);
		$sql = "select  oid , company_id ,  company_name,bank_name,bank_number,bank_owner,delivery_price  , delivery_free_price,  sum(one_delivery_price)as one_delivery_price ,   sum(sum_ptprice) as sum_ptprice, count(cnt) as cnt
						from shop_delivery_account_tmp
						group by company_id ";

}else if($admininfo[admin_level] == 8){
	$where .= " and c.company_id = '".$admininfo[company_id]."'";


	$sql = "create temporary table shop_delivery_account_tmp ENGINE = MEMORY
			select o.oid , od.company_id ,  ccd.com_name as company_name,bank_name,bank_number,bank_owner,o.delivery_price  , od.one_delivery_price ,  delivery_free_price,  sum(od.ptprice) as sum_ptprice, count(*) as cnt
		 from shop_order o, shop_order_detail od , ".TBL_COMMON_SELLER_DELIVERY." csd, ".TBL_COMMON_COMPANY_DETAIL." ccd
		where o.delivery_price != 0 and o.dac_ix is null and o.oid = od.oid and od.company_id = ccd.company_id and csd.company_id = ccd.company_id
		and od.delivery_type = 'CD' and od.status = 'AC'  $where
		group by o.oid, od.company_id
		having  sum(od.ptprice) < delivery_free_price
		order by od.company_id asc, date desc";
		$db->query($sql);
		$sql = "select  oid , company_id ,  company_name,bank_name,bank_number,bank_owner,delivery_price  , delivery_free_price,  sum(one_delivery_price)as one_delivery_price ,   sum(sum_ptprice) as sum_ptprice, count(cnt) as cnt
						from shop_delivery_account_tmp
						group by company_id ";
}

//echo $sql;
$db->query($sql);
if($mode == "excel"){

header( "Content-type: application/vnd.ms-excel" );
header( "Content-Disposition: attachment; filename=account_delivery_".date("Y-m-d").".xls" );
header( "Content-Description: Generated Data" );

	if($db->total){
		//echo "NO\t업체명\t은행명\t계좌번호\t입금자명\t판매건수\t판매수량\t배송비\t판매총액(할인가기준)\t수수료\t정산금액\n";
		echo "NO\t업체명\t은행명\t계좌번호\t입금자명\t상품수\t주문총액\t배송료\n";
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

			echo ($i+1)."\t".$db->dt[company_name]."\t".$db->dt[bank_name]."\t".$db->dt[bank_number]."\t".$db->dt[bank_owner]."\t".$db->dt[cnt]."\t".$db->dt[sum_ptprice]."\t".($db->dt[one_delivery_price])."\n";

		}
	}
	exit;
}

$Contents = "
<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	<tr>
		<td align='left' colspan=4>".GetTitleNavigation("배송비 정산예정내역", "주문관리 > 배송비 정산예정내역")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4>".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 15;'><img src='../image/title_head.gif' align=absmiddle><b> 정산정책</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	<form name='search_frm' method='get' action=''><input type='hidden' name='act' value='account_info_update'>
	<tr>
		<td width='20%'><img src='../image/ico_dot.gif' align=absmiddle> 정산기준 만료일자  </td>
		<td width='80%' colspan=3>
			<table border=0 cellpadding=0 cellspacing=0>
				<!--TD width=200 nowrap>
				<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY></SELECT> 년
				<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD-->
				<TD width=10 align=center> ~ </TD>
				<TD width=200 nowrap>
				<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년
				<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월
				<SELECT name=vToDD></SELECT> 일</TD>
				<td><span class=small>만료일 기준 이전 날짜들의 주문상태가 <b>배송완료</b>인 제품들에 대해서 정산 예정 항목이 노출됩니다</span></td>
			</table>
		</td>
	</tr>
	<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>";
if($admininfo[admin_level] == 9){
$Contents .= "
	<tr>
		<td width='20%'><img src='../image/ico_dot.gif' align=absmiddle> 업체검색  </td>
		<td width='80%' colspan=3>업체명 <input type='text' name='company_name' value=''></td>
	</tr>
	<tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>";
}
$Contents .= "
	<tr bgcolor=#ffffff>
		<td colspan=4 align=right><input type='image' src='../image/bt_search.gif' border=0 style='cursor:hand;border:0px;' ></td>
	</tr>
	</form>
	<tr height=50><td colspan=4></td></tr>
	<tr>
		<td align='right' colspan=4><a href='?mode=excel&".$QUERY_STRING."'><img src='../image/btn_excel_save.gif' border=0></a></td>
	</tr>
	<tr>
		<td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 15;'><img src='../image/title_head.gif' align=absmiddle><b> 정산예정내역</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	<!--tr>
		<td align='left' colspan=4><input type='radio' name='view_type' value='SV' onclick=\"document.location.href='accounts_plan.php'\"> 금액간단히보기 <input type='radio' name='view_type' value='DV' onclick=\"document.location.href='accounts_plan_price.php'\" checked> 금액자세히보기</td>
	</tr-->
	<tr bgcolor=#ffffff >
		<td colspan=4 align=right>
			<form name=listform method=post action='accounts_delivery.act.php' onsubmit=\"alert('일괄정산 준비중입니다');return false;\" target='act'>
			<input type='hidden' name='act' value='select_accounts_update'>
			<input type='hidden' name='page' value='$page'>
			<input type=hidden name='endDate' value='$endDate'>
			<input type=hidden name='company_name' value='$company_name'>
			<input type=hidden id='company_id' value=''>

			<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center'>
				<tr>
					<td width='5%' class='s_td'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
					<td width='5%' align='center' class='m_td small'><font color='#000000'><b>NO</b></font></td>
					<td width='20%' align='center' class='m_td small'><font color='#000000'><b>업체명</b></font></td>
					<td width='30%' align='center' class='m_td small'><font color='#000000'><b>은행정보</b></font></td>
					<td width='5%' align='center' class='m_td small' nowrap><font color='#000000'><b>상품수</b></font></td>
					<td width='10%' align='center' class='m_td small' nowrap><font color='#000000'><b>주문총액</b></font></td>

					";
if($admininfo[admin_level] == 8){
$Contents .= "<td width='10%' align='center'  class='m_td small' nowrap><font color='#000000'><b>배송비</b></font></td>
							<td width='5%' align='center' class='e_td small' nowrap><font color='#000000'><b>정산여부</b></font></td>";
}else if($admininfo[admin_level] == 9){
	$Contents .= "<td width='10%' align='center'  class='e_td small' nowrap><font color='#000000'><b>배송비</b></font></td>";
}
$Contents .= "</tr>";



	if($db->total){
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

			$Contents .= "
				<tr onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\" height=30>
					<td bgcolor='#EAEAEA' nowrap><input type=checkbox name='company_id[]' id='company_id' value='".$db->dt[company_id]."'></td>
					<td align='center' nowrap>".($i+1)."</td>
					<td bgcolor='#EAEAEA' nowrap><a href='accounts_delivery_detail.php?company_id=".$db->dt[company_id]."&startDate=$startDate&endDate=$endDate'>".$db->dt[company_name]."</a></td>
					<td align='left' nowrap>".$db->dt[bank_name]." ".$db->dt[bank_number]." (".$db->dt[bank_owner].")</td>
					<td bgcolor='#EAEAEA' align='center' nowrap>".number_format($db->dt[cnt])." 개</td>
					<td  align='right' style='padding-right:10px;' nowrap>".number_format($db->dt[sum_ptprice])." 원</td>
					<td bgcolor='#EAEAEA' style='padding-right:10px;' align='right'  nowrap><b>".number_format($db->dt[one_delivery_price])." 원</b></td>
					";
if($admininfo[admin_level] == 8){
$Contents .= "<td align='center'><a href='#' onclick=\"AccountDelivery('".$db->dt[oid]."','".$db->dt[company_id]."','$endDate')\">정산확인</a></td>";
}

$Contents .= "
				</tr>
				<tr height=1><td colspan=8 background='../image/dot.gif'></td></tr>";
			$total_delivery_price = $total_delivery_price + $db->dt[one_delivery_price];

			$sell_total_ptprice = $sell_total_ptprice + $db->dt[sum_ptprice];

		}
		$sell_total_commission = $sell_total_ptprice - $sell_total_coprice;
	$Contents .= "
				<tr bgcolor=#ffffff height=30>
					<td colspan=3>";
if($admininfo[admin_level] == 9){
	$Contents .= "<input type=image  src='../image/btn_whole_accounts.gif' border=0 style='cursor:hand;border:0px;' >";
}
	$Contents .= "
					</td>


					<td></td>
					<td align='center'>합계 :</td>
					<td align='center' nowrap>".number_format($sell_total_ptprice)."원</td>
					<td align='center' nowrap>".number_format($total_delivery_price)."원</td>


				</tr>
				</tr>
				<!--tr bgcolor=#ffffff ><td colspan=10 align=right><input type='image' src='../image/btn_account.gif' border=0 style='cursor:hand;border:0px;' > </td></tr-->
				</form>";

	}else{
		$Contents .= "
				<tr height=50><td colspan=10 align=center>정산 대기내역이 없습니다</td></tr>
				<tr height=1><td colspan=10 background='../image/dot.gif'></td></tr>";
	}
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배송완료후의 정산기준일자를 선택한 후 검색 합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배송비 정산 항목을 확인후 맞으면 확인버튼을 클릭해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >업체명을 클릭하시면 배송비 정산대기 내역에 대한 상세 내역을 확인하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배송비 정산대기내역이 확인 되었으면 정산확인 버튼을 클릭합니다. 정산이 완료된 금액은 나의 통장으로 입금 되게 됩니다</td></tr>
</table>
";




$Contents .= "	</table>
	  	</td>
	  </tr>
	  <tr><td colspan=4>".HelpBox("정산정책관리", $help_text)."</td></tr>
	  </table><br>
	  <form name=account_delivery_frm method=post action='accounts_delivery.act.php' onsubmit=\"alert('일괄정산 준비중입니다');return false;\" ><!--target='act'-->
			<input type='hidden' name='act' value='accounts_delivery'>
			<input type=hidden name='eDate' value='$endDate'>
			<input type=hidden name='company_name' value='$company_name'>
			<input type=hidden name='company_id' value=''>
			<input type=hidden name='oid' value=''>
		</form>
	  ";


$Script = "<script lanaguage='javascript'>
function AccountDelivery(oid,company_id, edate){
	var frm = document.account_delivery_frm;

	if(confirm('배송비 정산을 하시겠습니까?')){
		frm.company_id.value = company_id
		frm.oid.value = oid;
		frm.submit();
	}
}

</script>";



$P = new LayOut();
$P->addScript = "<script language='javascript' src='account.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "onLoad('$sDate','$eDate');";
$P->strLeftMenu = order_menu();
$P->Navigation = "HOME > 주문관리 > 배송비 정산 예정 내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>