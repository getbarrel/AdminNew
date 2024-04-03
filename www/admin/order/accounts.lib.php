<?
include("../class/layout.class");


function get_acctoun_plan($admininfo,$currency_display,$admin_config,$where,$company_id){

	$db = new Database;
//echo $company_id."<br>";
	$sql = "SELECT c.com_name,od.company_id as company_id,bank_name,bank_number,bank_owner ,sum(od.pcnt) as sell_cnt, sum(od.ptprice) as sell_total_ptprice,sum(od.ptprice*(100-od.commission)/100) as sell_total_coprice,
				sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, sum(od.delivery_price) as shipping_price, od.regdate as order_com_date,avg(od.commission) as avg_commission, count(*) as order_cnt
				FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid
				left join ".TBL_COMMON_COMPANY_DETAIL." c on od.company_id = c.company_id
				left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id

				WHERE  od.status = 'DC' and od.company_id is not null  $where
				and od.company_id = '".$company_id."'
				";
	$db->query($sql);
	//echo "<pre>";
	//echo "$sql"."<br>";
	if($db->total){
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

			$Contents .= "
				<tr onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\" height=30>
					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center'><input type=checkbox name='company_id[]' id='company_id' value='".$db->dt[company_id]."'></td>
					<td class='list_box_td' align='center' nowrap>".($i+1)."</td>
					<td class='list_box_td list_bg_gray'  bgcolor='#EAEAEA' align='center'><a href='accounts_detail.php?company_id=".$db->dt[company_id]."&startDate=$startDate&endDate=$endDate'>".$db->dt[com_name]."123</a></td>
					
					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center' nowrap>입점</td>
					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center' nowrap>중개</td>
					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center' nowrap>면세</td>
					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center' nowrap>개인</td>

					<td class='list_box_td' align='left'><div style='padding-left:3px;'>".number_format($db->dt[sell_total_ptprice])."</div></td>

					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center' nowrap>".number_format($db->dt[sell_total_ptprice])."</td>
					<td class='list_box_td' align='center' nowrap>".$db->dt[sell_cnt]."개</td>

					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[sell_total_ptprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>

					<td class='list_box_td' align='center'  nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[sell_total_ptprice]-$db->dt[sell_total_coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td point' bgcolor='#EAEAEA' align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[sell_total_coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>
					<td class='list_box_td' bgcolor='#ffffff' align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[shipping_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>";

					if($admininfo[admin_level] == 9){
					$Contents .= "<td class='list_box_td list_bg_gray' align='center' bgcolor='#EAEAEA'>
									<a href='#' onclick=\"PoPWindow('accounts_taxbill.php?company_id=".$db->dt[company_id]."&startDate=$startDate&endDate=$endDate',680,450,'account_taxbill')\"><img src='../images/".$admininfo["language"]."/btn_taxbill_view.gif' border=0 align=absmiddle></a>
									<a href=\"javascript:Account('".$db->dt[company_id]."','$endDate')\"><img src='../images/".$admininfo["language"]."/btn_account_confirm.gif' align=absmiddle ></a>
									</td>";
					}else if($admininfo[admin_level] == 8){
					$Contents .= "<td class='list_box_td list_bg_gray' align='center' bgcolor='#EAEAEA'><a href=\"javascript:Account('".$db->dt[company_id]."','$endDate')\"><img src='../images/".$admininfo["language"]."/btn_account_confirm.gif' align=absmiddle ></a></td>";
					}

			$Contents .= "</tr>";
		}
	}
	return $Contents;
}

?>