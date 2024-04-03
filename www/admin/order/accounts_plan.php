<?
include("../class/layout.class");

if($admininfo[admin_level] != 9){
	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['accounts_plan.php']['A'][language]);history.back();</script>";//권한이 없습니다.
}

$db = new Database;

// 현재는 사용하지 않는것으로 판단
$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." WHERE mall_ix='".$admininfo[mall_ix]."' ");
$db->fetch();

$account_priod = $db->dt[account_priod];


$Contents = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=4 > ".GetTitleNavigation("정산 정책 관리", "주문관리 > 정산 정책 관리")."</td>
	  </tr>";

 if($admininfo[admin_level] == 9){

	$Contents .= "
	  <tr>
	    <td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 15;'><img src='../image/title_head.gif' align=absmiddle><b> 정산정책</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	  </tr><form name='delivery_form' action='accounts.act.php' method='post' target='act'><input type='hidden' name='act' value='account_info_update'>

	  <tr bgcolor=#ffffff>
	    <td width='20%'><img src='../image/ico_dot.gif' align=absmiddle> 정산주기   </td>
	    <td width='80%'  colspan=3>
	    	<table cellpadding=0 cellspacing=0>
		    	<tr><td> 	<b>배송완료</b>인 주문을 배송완료후  <input type=text class='textbox' name='account_priod' value='".$db->dt[account_priod]."' size=5> 일이 경과한 상품에 한해서 정산을 수행합니다</td></tr>
		    	<tr height=25><td>※<span class=small> <font color=#5B5B5B> 기간이 <b>0</b> 인경우 현재 배송완료 상태인 모든 주문에 대해 정산됩니다.</font></span></td></tr>
		    	<!--tr><td>용달 :</td><td><input type=text class='textbox' name='basic_send_cost_truck' value='".$db->dt[basic_send_cost_truck]."' size=15><span class=small> <font color=#5B5B5B>사용하지 않는 경우는 공백으로 두시면 됩니다.</font></span></td></tr-->
	    	</table>

	    </td>
	  </tr>
	  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr>
	  <!--tr bgcolor=#ffffff height=30>
	    <td><img src='../image/ico_dot.gif' align=absmiddle> 정산수행    </td>
	    <td colspan=3>
	    	<table cellpadding=3 cellspacing=0>
		    	<tr>
		    		<td>
		    		<input type=radio name='account_auto' value='1' id='account_auto_1'  ".CompareReturnValue("1",$db->dt[account_auto],"checked")."><label for='account_auto_1'>자동수행</label>
	    			<input type=radio name='account_auto' value='0' id='account_auto_2'  ".CompareReturnValue("0",$db->dt[account_auto],"checked")."><label for='account_auto_2'>수동수행</label>
	    			</td>
	    		</tr>
		    	<tr>
		    		<td>
		    		<span class=small><b>외부 호스팅</b>을 받으시는 고객님의 경우 <b>자동수행</b>으로 정산을 원하시는 경우  URL(<u>http://".$HTTP_HOST."/clone/account.php</u>) 을 <b>clone</b> 에 등록해주시기 바랍니다</span>
	    			</td>
	    		</tr>
	    	</table>

	    </td>
	  </tr>
	  <tr height=1><td colspan=4 background='../image/dot.gif'></td></tr-->
	  <tr bgcolor=#ffffff ><td colspan=4 align=right><input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr></form>
	  <tr height=50><td colspan=4 ></td></tr>";
	}

$Contents .= "
	  <tr>
	    <td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 15;'><img src='../image/title_head.gif' align=absmiddle><b> 정산대기 내역 미리보기</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	  	<td colspan=4 align=right>
			<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center'>
				<tr height='25' >
					<!--td class='s_td'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td-->
					<td width='15%' align='center' class='s_td'><font color='#000000'><b>업체명</b></font></td>
					<td width='15%' align='center' class='m_td'><font color='#000000'><b>주문완료일</b></font></td>
					<td width='10%' align='center'  class='m_td' nowrap><font color='#000000'><b>정산진행상태</b></font></td>
					<td width='10%' align='center' class='m_td' nowrap><font color='#000000'><b>판매건수</b></font></td>
					<td width='20%' align='center' class='m_td'><font color='#000000'><b>판매금액(공급가기준)</b></font></td>
					<td width='10%' align='right' class='m_td' nowrap><font color='#000000'><b>선불배송건수</b></font></td>
					<td width='10%' align='center' class='m_td' nowrap><font color='#000000'><b>선불배송비</b></font></td>
					<td width='15%' align='center' class='e_td'><font color='#000000'><b>정산금액</b></font></td>
				</tr>";
	if($account_priod){
		$account_priod_str = " and NOW() > DATE_FORMAT(DATE_ADD(os.regdate, interval ".$account_priod." day),'%Y-%m-%d 23:59:59') ";
	}

	if($admininfo[admin_level] == 9){
		$sql = "SELECT c.com_name, p.admin as company_id ,count(od.oid) as sell_cnt, sum(od.coprice) as sell_total_coprice,
			sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, sum(o.delivery_price) as shipping_price, os.regdate as order_com_date
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od, ".TBL_SHOP_ORDER." o, ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_SHOP_ORDER_STATUS." os
			where p.id = od.pid and o.oid = od.oid and p.admin = c.company_id and od.status in ('DC') and o.oid = os.oid and o.os_ix = os.os_ix
			$account_priod_str  group by admin   ";
	}else if($admininfo[admin_level] == 8){
		$sql = "SELECT c.com_name, p.admin as company_id ,count(od.oid) as sell_cnt, sum(od.coprice) as sell_total_coprice,
			sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, sum(o.delivery_price) as shipping_price, os.regdate as order_com_date
			FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od, ".TBL_SHOP_ORDER." o, ".TBL_COMMON_COMPANY_DETAIL." c , ".TBL_SHOP_ORDER_STATUS." os
			where p.id = od.pid and o.oid = od.oid and p.admin = '".$admininfo[company_id]."' and od.status in ('DC') and o.oid = os.oid and o.os_ix = os.os_ix and c.company_id = '".$admininfo[company_id]."'
			$account_priod_str  group by admin   ";
	}
	//echo $sql;
	$db->query($sql);

	if($db->total){
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

			$Contents = $Contents."
				<tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
					<!--td nowrap><input type=checkbox name='oid[]' id='oid' value='".$db->dt[oid]."'></td-->
					<td align='center'  nowrap>".$db->dt[company_name]."</td>
					<td align='center'  nowrap><a href='accounts_detail.php?company_id=".$db->dt[company_id]."&oid=".$db->dt[oid]."'>".$db->dt[order_com_date]." </a></td>
					<td align='center' nowrap>정산대기</td>
					<td align='center'  nowrap>".$db->dt[sell_cnt]."</td>
					<td align='center' style='padding-left:10px' nowrap>".number_format($db->dt[sell_total_coprice])." 원</td>
					<td align='right' >".number_format($db->dt[pre_shipping_cnt])." </td>
					<td align='center' >".number_format($db->dt[shipping_price])." </td>
					<td align='center'  nowrap>
						".number_format($db->dt[sell_total_coprice]+$db->dt[shipping_price])." 원
					</td>
				</tr>
				<tr height=1><td colspan=9 background='../image/dot.gif'></td></tr>";
		}

	$Contents .= "<form name='delivery_form' action='accounts.act.php' method='post' target=act><input type='hidden' name='act' value='account'>
				<tr bgcolor=#ffffff ><td colspan=9 align=right><input type='image' src='../image/btn_account.gif' border=0 style='cursor:hand;border:0px;' > </td></tr>
				</form>";

	}else{
		$Contents .= "
				<tr height=50><td colspan=9 align=center>정산 대기내역이 없습니다</td></tr>
				<tr height=1><td colspan=9 background='../image/dot.gif'></td></tr>";
	}
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배송완료후의 정산주기를 선택한후 정산 정책을 저장합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >정산정책이 저장되면 정산대기 내역이 입점업체 별로 리스트업 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문완료일을 클릭하시면 정산대기 내역에 대한 상세 내역을 확인하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >정산대기내역이 확인 되었으면 정산 버튼을 클릭합니다. 정산이 완료된 금액은 나의 통장으로 입금 되게 됩니다</td></tr>
</table>
";




$Contents .= "	</table>
	  	</td>
	  </tr>
	  <tr><td colspan=4>".HelpBox("정산정책관리", $help_text)."</td></tr>
	  </table><br>

	  ";





$Script = "<script language='javascript' src='delivery.js'></script>\n<script language='JavaScript' src='/js/XMLHttp.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->OnloadFunction = "";
$P->strLeftMenu = order_menu();
$P->Navigation = "HOME > 주문관리 > 정산 정책 관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>