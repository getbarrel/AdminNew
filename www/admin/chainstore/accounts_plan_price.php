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
		$eDate = date("Y/m/t",strtotime('-1 month'));
		$endDate = date("Ymt",strtotime('-1 month'));
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

/*
$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." WHERE mall_ix='".$admininfo[mall_ix]."' ");
$db->fetch();

$account_priod = $db->dt[account_priod];
*/
$where=" AND od.product_type NOT IN (".implode(',',$sns_product_type).") ";
if ($vFromYY != ""){
	//$where .= "and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate ";
	$where .= "and date_format(od.dc_date,'%Y%m%d') <= $endDate ";
}else{
	//$where .= "and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate ";
	$where .= "and date_format(od.dc_date,'%Y%m%d') <= $endDate ";
}


if($admininfo[admin_level] == 9){


	if($company_id != "") $where .= " and c.company_id='$company_id' ";

	if($admininfo[mem_type] == "MD"){
		$where .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
	}


$sql = "SELECT c.com_name,od.company_id as company_id,bank_name,bank_number,bank_owner ,sum(od.pcnt) as sell_cnt, sum(od.ptprice) as sell_total_ptprice,sum(od.ptprice*(100-od.commission)/100) as sell_total_coprice,
		sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, sum(od.delivery_price) as shipping_price, od.regdate as order_com_date,avg(od.commission) as avg_commission, count(*) as order_cnt
		FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid
		left join ".TBL_COMMON_COMPANY_DETAIL." c on od.company_id = c.company_id
		left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id
		WHERE  od.status = 'DC' and od.company_id is not null  $where group by od.company_id " ; // and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate
//echo nl2br($sql);
		//and o.status = 'DC'
}else if($admininfo[admin_level] == 8){
	$where .= " and c.company_id = '".$admininfo[company_id]."'";

	$sql = "SELECT c.com_name,od.company_id as company_id,bank_name,bank_number,bank_owner ,sum(od.pcnt) as sell_cnt, sum(od.ptprice) as sell_total_ptprice,sum(od.ptprice*(100-od.commission)/100) as sell_total_coprice,
		sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, sum(od.delivery_price) as shipping_price, od.regdate as order_com_date,avg(od.commission) as avg_commission, count(*) as order_cnt
		FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid
		left join ".TBL_COMMON_COMPANY_DETAIL." c on od.company_id = c.company_id
		left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id
		WHERE  od.status = 'DC'  and od.company_id is not null  $where group by od.company_id " ; // and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate
		//and o.status = 'DC'
}

//echo $sql;
$db->query($sql);
if($mode == "excel"){

header( "Content-type: application/vnd.ms-excel" );
header( "Content-Disposition: attachment; filename=account_list_".date("Y-m-d").".xls" );
header( "Content-Description: Generated Data" );

	if($db->total){
		//echo "NO\t업체명\t은행명\t계좌번호\t입금자명\t판매건수\t판매수량\t배송비\t판매총액(할인가기준)\t수수료\t정산금액\n";
		$mstring = "NO\t업체명\t은행명\t계좌번호\t입금자명\t판매건수\t판매수량\t판매총액(할인가기준)\t수수료\t정산금액\n";
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

			$mstring .= ($i+1)."\t".$db->dt[com_name]."\t".$db->dt[bank_name]."\t ".$db->dt[bank_number]." \t".$db->dt[bank_owner]."\t".$db->dt[order_cnt]."\t".$db->dt[sell_cnt]."\t".$db->dt[sell_total_ptprice]."\t".($db->dt[sell_total_ptprice]-$db->dt[sell_total_coprice])."\t".$db->dt[sell_total_coprice]."\n";

		}
	}

	echo iconv("utf-8","CP949",$mstring);
	exit;
}

$Contents = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<!--col width='25%' />
	<col width='25%' />
	<col width='25%' />
	<col width='25%' /-->
	<tr>
		<td align='left' colspan=4>".GetTitleNavigation("정산예정내역", "정산관리 > 정산예정내역")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding:10px 0px 4px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:3px;'><img src='../image/title_head.gif' align=absmiddle><b> 정산정책</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	</table>
	<form name='search_frm' method='get' action='accounts_plan_price.php'><input type='hidden' name='act' value='account_info_update'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	<tr  height=30>
		<td class='search_box_title'>정산기준 만료일자  </td>
		<td class='search_box_item' >
			<table border=0 cellpadding=0 cellspacing=0>
				<!--TD width=200 nowrap>
				<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY></SELECT> 년
				<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
				<TD width=10 align=center> ~ </TD-->
				<TD nowrap>
				<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년
				<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월
				<SELECT name=vToDD></SELECT> 일</TD>
				<td><span class=small><!--만료일 기준 이전 날짜들의 주문상태가 <b>배송완료</b>인 제품들에 대해서 정산 예정 항목이 노출됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span></td>
			</table>
		</td>
	</tr>";
if($admininfo[admin_level] == 9){
$Contents .= "<tr height=30>
		<td class='search_box_title'>업체검색  </td>
		<td class='search_box_item'><!--업체명 <input type='text' name='com_name' value=''-->".CompanyList2($company_id,"")."</td>
	</tr>";
}
$Contents .= "
	<!--tr bgcolor=#ffffff height=30>
		<td class='search_box_title'><img src='../image/ico_dot.gif' align=absmiddle>정산수행</td>
		<td class='search_box_item' >
			<table cellpadding=3 cellspacing=0>
				<tr>
					<td><input type=radio name='account_auto' value='1' id='account_auto_1'  ".CompareReturnValue("1",$db->dt[account_auto],"checked")."><label for='account_auto_1'>자동수행</label>
	    			<input type=radio name='account_auto' value='0' id='account_auto_2'  ".CompareReturnValue("0",$db->dt[account_auto],"checked")."><label for='account_auto_2'>수동수행</label></td>
				</tr>
				<tr>
					<td><span class=small><b>외부 호스팅</b>을 받으시는 고객님의 경우 <b>자동수행</b>으로 정산을 원하시는 경우  URL(<u>http://".$HTTP_HOST."/clone/account.php</u>) 을 <b>clone</b> 에 등록해주시기 바랍니다</span></td>
	    		</tr>
			</table>
		</td>
	</tr>
	<tr height=1><td colspan=4 class='dot-x'></td></tr-->
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' >
	<tr bgcolor=#ffffff>
		<td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='cursor:pointer;border:0px;' ></td>
	</tr>
	</form>

	<tr>
		<td align='right' colspan=4><a href='accounts_plan_price_excel2003.php?mode=excel&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></td>
	</tr>
	<tr>
		<td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b> 정산예정내역</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	<!--tr>
		<td align='left' colspan=4><input type='radio' name='view_type' value='SV' onclick=\"document.location.href='accounts_plan.php'\"> 금액간단히보기 <input type='radio' name='view_type' value='DV' onclick=\"document.location.href='accounts_plan_price.php'\" checked> 금액자세히보기</td>
	</tr-->
	<tr bgcolor=#ffffff >
		<td colspan=4 align=right>
			<form name=listform method=post action='accounts.act.php' onsubmit=\"alert(language_data['accounts_plan_price.php']['A'][language]);return false;\" style='display:inline;' target='act'><!--일괄정산 준비중입니다-->
			<input type='hidden' name='act' value='select_accounts_update'>
			<input type='hidden' name='page' value='$page'>
			<input type=hidden name='endDate' value='$endDate'>
			<input type=hidden name='com_name' value='$com_name'>
			<input type=hidden id='company_id' value=''>

			<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
				<tr height=27>
					<td width='3%' align='center' class='s_td'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
					<td width='5%' align='center' class='m_td'><font color='#000000'><b>NO</b></font></td>
					<td width='10%' align='center' class='m_td'><font color='#000000'><b>업체명</b></font></td>
					<!-- <td width='12%' align='center' class='m_td'><font color='#000000'><b>수수료율(평균)</b></font></td> -->
					<td width='*' align='center' class='m_td'><font color='#000000'><b>은행정보</b></font></td>
					<!--td width='10%' align='center' class='m_td'><font color='#000000'><b>계좌번호</b></font></td>
					<td width='5%' align='center' class='m_td'><font color='#000000'><b>입금자명</b></font></td-->
					<td width='5%' align='center' class='m_td' nowrap><font color='#000000'><b>판매건수</b></font></td>
					<td width='5%' align='center' class='m_td' nowrap><font color='#000000'><b>판매수량</b></font></td>

					<td width='13%' align='center' class='m_td' nowrap><font color='#000000'><b>판매총액(할인가기준)</b></font></td>
					<!--td width='10%' align='center' class='m_td'><font color='#000000'><b>할인가</b></font></td-->
					<td width='11%' align='center' class='m_td' nowrap><font color='#000000'><b>수수료</b></font></td>
					";
if($admininfo[admin_level] == 9){
$Contents .= "<td width='11%' align='center' class='m_td'><font color='#000000'><b>정산금액</b></font></td>
							<td width='5%' align='center'  class='m_td' nowrap><font color='#000000'><b>배송비정산</b></font></td>
							<td width='10%' align='center' class='e_td' nowrap><font color='#000000'><b>정산여부</b></font></td>";
}else if($admininfo[admin_level] == 8){
	$Contents .= "<td width='15%' align='center' class='m_td'><font color='#000000'><b>정산금액</b></font></td>
								<td width='5%' align='center'  class='m_td' nowrap><font color='#000000'><b>배송비정산</b></font></td>
								<td width='10%' align='center' class='e_td' nowrap><font color='#000000'><b>정산확인</b></font></td>";
}
$Contents .= "</tr>";



	if($db->total){
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

			$Contents .= "
				<tr onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\" height=30>
					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center'><input type=checkbox name='company_id[]' id='company_id' value='".$db->dt[company_id]."'></td>
					<td class='list_box_td' align='center' nowrap>".($i+1)."</td>
					<td class='list_box_td list_bg_gray'  bgcolor='#EAEAEA' align='center'><a href='accounts_detail.php?company_id=".$db->dt[company_id]."&startDate=$startDate&endDate=$endDate'>".$db->dt[com_name]."</a></td>

					<td class='list_box_td' align='left'><div style='padding-left:3px;'>".$db->dt[bank_name]." ".$db->dt[bank_number]." (".$db->dt[bank_owner].")</div></td>

					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center' nowrap>".$db->dt[order_cnt]." 건</td>
					<td class='list_box_td' align='center' nowrap>".$db->dt[sell_cnt]."개</td>

					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[sell_total_ptprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>

					<td class='list_box_td' align='center'  nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[sell_total_ptprice]-$db->dt[sell_total_coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td point' bgcolor='#EAEAEA' align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[sell_total_coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>
					<td class='list_box_td' bgcolor='#ffffff' align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[shipping_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>";

if($admininfo[admin_level] == 9){
$Contents .= "<td class='list_box_td list_bg_gray' align='center' bgcolor='#EAEAEA'>
							<a href='#' onclick=\"PoPWindow('accounts_taxbill.php?company_id=".$db->dt[company_id]."&startDate=$startDate&endDate=$endDate',680,450,'account_taxbill')\"><img src='../images/".$admininfo["language"]."/btn_taxbill_view.gif' border=0 align=absmiddle></a>
							</td>";
}else if($admininfo[admin_level] == 8){
$Contents .= "<td class='list_box_td list_bg_gray' align='center' bgcolor='#EAEAEA'><a href=\"javascript:Account('".$db->dt[company_id]."','$endDate')\"><img src='../images/".$admininfo["language"]."/btn_account_confirm.gif' align=absmiddle ></a></td>";
}

$Contents .= "
				</tr>";
			$sell_total_ptprice = $sell_total_ptprice + $db->dt[sell_total_ptprice];
			$sell_total_coprice = $sell_total_coprice + $db->dt[sell_total_coprice];
			$sell_total_skpoint = $sell_total_skpoint + $db->dt[sk_point];

		}
		$sell_total_commission = $sell_total_ptprice - $sell_total_coprice;
	$Contents .= "
				<tr bgcolor=#ffffff height=30>
					<td colspan=6 style='padding-right:20px;' class='point'>";
if($admininfo[admin_level] == 9){
	//$Contents .= "<input type=image  src='../image/btn_whole_accounts.gif' border=0 style='cursor:pointer;border:0px;' >";
}
	$Contents .= "합계 :
					</td>

					<td align='center' class='list_bg_gray str' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($sell_total_ptprice)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td align='center' class='str' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($sell_total_commission)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td align='center' class='point' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($sell_total_coprice)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td colspan=2></td>
				</tr>
				<!--tr bgcolor=#ffffff ><td colspan=10 align=right><input type='image' src='../images/".$admininfo["language"]."/btn_account.gif' border=0 style='cursor:pointer;border:0px;' > </td></tr-->
				</form>";

	}else{
		$Contents .= "
				<tr height=50><td colspan=11 align=center>정산 대기내역이 없습니다</td></tr>
				";
	}

	/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배송완료후의 정산기준일자를 선택한 후 검색 합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >정산정책에 따라  정산대기 내역이 입점업체 별로 리스트업 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >업체명을 클릭하시면 정산대기 내역에 대한 상세 내역을 확인하실수 있습니다</td></tr>
	<!--tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >정산대기내역이 확인 되었으면 정산 버튼을 클릭합니다. 정산이 완료된 금액은 나의 통장으로 입금 되게 됩니다</td></tr-->
</table>
";
*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');


$Contents .= "	</table>
	  	</td>
	  </tr>
	  <tr><td colspan=4>".HelpBox("정산정책관리", $help_text)."</td></tr>
	  </table><br>
	  <form name=account_frm method=post action='accounts.act.php' onsubmit=\"alert(language_data['accounts_plan_price.php']['A'][language]);return false;\" target='act'><!--target='act' 일괄정산 준비중입니다-->
			<input type='hidden' name='act' value='account'>
			<input type=hidden name='eDate' value='$endDate'>
			<input type=hidden name='company_id' value=''>
		</form>
	  ";


$Script = "<script lanaguage='javascript'>
function Account(company_id, edate){
	var frm = document.account_frm;

	if(confirm(language_data['accounts_plan_price.php']['B'][language])){//해당 정산내용을 정산확인 처리 하시겠습니까?
		frm.company_id.value = company_id
		frm.submit();
	}
}

</script>";



$P = new LayOut();
$P->addScript = "<script language='javascript' src='account.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "onLoad('$sDate','$eDate');";
$P->strLeftMenu = chainstore_menu();
$P->Navigation = "정산관리 > 정산 예정 내역";
$P->title = "정산 예정 내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>