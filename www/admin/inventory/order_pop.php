<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("inventory.lib.php");

$Script = "<script language='JavaScript' src='order_pop.js'></Script>";



$db = new Database;
//$db->query("SELECT id,pcode,pname,sellprice,coprice, p.stock,p.sell_ing_cnt , p.surtax_yorn ,regdate, supply_company , inventory_info FROM ".TBL_SHOP_PRODUCT." p  where id = '".$id."'");
$db->query("SELECT gid,gcode,gname,regdate,pi_ix,ci_ix FROM inventory_goods g  where gid = '".$gid."'");
$db->fetch();
$product_info = $db->dt;

if($db->dt[ci_ix] != ""){
	$ci_ix = $db->dt[ci_ix];
}

if($pi_ix == "" && $select_pi_ix != ""){
	$pi_ix = $select_pi_ix;
}else{
	if($pi_ix == "" && $db->dt[pi_ix] != ""){
		//echo $db->dt[inventory_info];
		//exit;
		$select_pi_ix = $db->dt[pi_ix];
		$pi_ix = $db->dt[pi_ix];

	}
}
//print_r($_SESSION["admininfo"]);
$Contents = "
<form name='order_pop' method='post' onsubmit='return CheckFormValue(this)' action='order_pop.act.php' target='act'>
<input type='hidden' name='mode' value=''>
<input type='hidden' name='gid' value=".$gid.">
<input type='hidden' name='pi_ix' id='pi_ix'  value='".($pi_ix)."'>
<input type='hidden' name='gname' value='".$db->dt[gname]."'>
<input type='hidden' name='charger_ix' value='".$_SESSION["admininfo"]["charger_ix"]."'>
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr height=25 align='left'>
				<td style='padding:10px 0px 0px 0px'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title' onclick='CheckFormValue(document.order_pop)'>품목정보</b></td>
			</tr>
			<tr>
				<td style='padding:5px 0px 0px 0px'>

						<table border=0 cellpadding=0 cellspacing=0 width='100%'>
							<tr>
								<td style='width:150px;padding:5px;border:1px solid silver;border-right:0px;' align=center>";


								if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $db->dt[gid], "c"))){
									$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $db->dt[gid], "c");
								}else{
									$img_str = "../image/no_img.gif";
								}
$Contents .= "
								<img src='".$img_str."'>
								</td>
								<td width='*' valign=top>
									<table border=0 cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
										<col width='20%' >
										<col width='30%' >
										<col width='20%' >
										<col width='30%' >
										<tr align='left' height='25'>
											<td class='input_box_title'><b>품목명</b></td>
											<td class='input_box_item' colspan=3>&nbsp;".$db->dt[gname]."</td>
										</tr>
										<tr align='left' height='25'>
											<td class='input_box_title'> <b>품목코드</b></td>
											<td class='input_box_item'>&nbsp;".$db->dt[gcode]."</td>
											<td class='input_box_title'> <b></b></td>
											<td class='input_box_item'></td>
											<!--td class='input_box_title'><b>과세여부</b></td>
											<td class='input_box_item' title='".$db->dt[surtax_yorn]."' nowrap>
											 ".($db->dt[surtax_yorn] == "Y" ? "면세(비과세)":"과세")."
											</td-->
										</tr>
										<!--tr align='left' height='25'>
											<td class='input_box_title'><b>판매가</b></td>
											<td class='input_box_item'>&nbsp;".number_format($db->dt[sellprice])." 원</td>
											<td class='input_box_title'><b>공급가</b></td>
											<td class='input_box_item'>&nbsp;".number_format($db->dt[coprice])." 원</td>
										</tr>
										<tr align='left' height='25'>
											<td class='input_box_title'><b>품목 재고</b></td>
											<td class='input_box_item point'>&nbsp;".number_format($db->dt[stock])." 개</td>
											<td class='input_box_title' nowrap><b>판매진행중 재고</b></td>
											<td class='input_box_item point'>&nbsp;".number_format($db->dt[sell_ing_cnt])." 개</td>
										</tr-->
										<tr align='left' height='25'>
											<td class='input_box_title' nowrap> 입고처</td>
											<td class='input_box_item'  style='padding:2px 5px;'>
												".SelectSupplyCompany($ci_ix,"ci_ix",($db->dt[ci_ix] == "" ? "select":"select"))."
											</td>
											<td class='input_box_title'><b>등록일</b></td>
											<td class='input_box_item' >&nbsp;".substr($db->dt[regdate],0,10)."</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>

				</td>
			</tr>

			<!--tr>
				<td align='left' style='padding:5px 0px 0px 0px' height=25><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>보관장소 및 입고처</b></td>
			</tr>
			<tr>
				<td style='padding:5px 0px 0px 0px'>
					<table border='0' width='100%' cellspacing='1' cellpadding='0'>
						<tr>
							<td >
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box'>

									<tr id='default' style='padding-left:10px;'>
										<td class='input_box_title' nowrap> 입고처</td>
										<td class='input_box_item'  style='padding-left:5px;'>
											".SelectSupplyCompany($db->dt[ci_ix],"ci_ix","select")." 
										</td>
									</tr>
									<tr >
										<td class='input_box_title' nowrap> 보관장소</td>
										<td class='input_box_item' style='padding-left:5px; vertical-align:middle'>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr-->
			<tr>
				<td align='left' style='padding:10px 0px 0px 0px' height=25><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>재고 및 발주내용</b></td>
			</tr>
			<tr>
					<td align='left' colspan=4 style='padding-bottom:10px;'>
						<div class='tab'>
							<table class='s_org_tab' style='width:100%' border=1>
							<tr>
								<td class='tab'>";

	//$sql = "select pi.pi_ix, place_name from inventory_place_info pi , inventory_product_stockinfo ps where pi.pi_ix = ps.pi_ix and gid = '".$_GET[gid]."' group by pi.pi_ix ";
	$sql = "select distinct(pi.pi_ix), place_name from inventory_place_info pi , inventory_product_stockinfo ps where pi.pi_ix = ps.pi_ix and gid = '".$_GET[gid]."' ";
	$db->query($sql);
	$db->fetch();

	$warehouse_seleted = false;
	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
		$reg_pi_ix[$i] = $db->dt[pi_ix];
		if($pi_ix == $db->dt[pi_ix]){
			$warehouse_seleted = true;
		}
		$Contents .= "
									<table id='tab_01' ".($pi_ix == $db->dt[pi_ix] ? "class='on'":"")."  >
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='?gid=".$_GET["gid"]."&pi_ix=".$db->dt[pi_ix]."'\">".$db->dt[place_name]."</td>";
		if($pi_ix == $db->dt[pi_ix]){
		$Contents .= "			<td class='box_02' style='padding:4px 3px 0px 3px ;' > 
										
										".SelectSectionInfo($pi_ix,$ps_ix,'ps_ix',"select","true","style='border:1px solid silver;'")." 
										</td>";
		}

		$Contents .= "
										<th class='box_03'></th>
									</tr>
									</table>";
	}

		$Contents .= "
									<table id='tab_02' ".(!$warehouse_seleted ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' style='padding:4px 8px 0px 8px ;;vertical-align:middle;' onclick=\"document.location.href='?gid=".$_GET["gid"]."&pi_ix=all'\">창고전체</td>";
		if(!$warehouse_seleted){
		$Contents .= "
										<td class='box_02' style='padding:4px 3px 0px 3px ;' > 
										".makeSelectBoxPlace($select_pi_ix, 'select_pi_ix',$reg_pi_ix, ($db->total > 0 ? false:true))."			
										".SelectSectionInfo($select_pi_ix,$ps_ix,'ps_ix',"select","true","style='border:1px solid silver;'")." 
										</td>";
		}
		$Contents .= "
										
										<th class='box_03'></th>
									</tr>
									</table>
									";
		$Contents .= "
								</td>
								<td class='btn' style='vertical-align:bottom;padding-bottom:5px;' align=right>
								</td>
							</tr>
							</table>
							</div>
					</td>
				</tr>
			<input type=hidden name=input_size2 id=input_size value=0>
			<tr>
				<td style='padding:5px 0px 0px 0px'>
					<table border=0 cellpadding=0 cellspacing=1 width='100%' class='list_table_box' >
						<col width=*>
						<col width=13%>
						<col width=13%>
						<col width=13%>
						<col width=13%>
						<col width=13%>
						<tr height=30px bgcolor=#e5e5e5>
							<td class='m_td' align=center>단위</td>
							<td class='m_td' align=center>단품코드</td>
							<td class='m_td' align=center>발주 수량</td>
							<td class='m_td' align=center>발주가</td>
							<td class='m_td' align=center>입고기준가</td>
							<td class='m_td' align=center>재고</td>
							<td class='e_td' align=center>안전재고</td>
						</tr>";

						/*
						$sql = "select od.option_stock as stock,od.option_safestock as safestock,o.option_type,od.option_div,od.id,o.option_name ,od.option_code, od.option_coprice, od.opn_ix, od.id as opnd_ix
									from shop_product_options o , shop_product_options_detail od
									where o.opn_ix = od.opn_ix and o.gid = '".$gid."' and od.gid = '".$gid."' and o.option_kind = 'b' group by id ";
						*/
						$sql = "select  g.gid,pi.place_name, ps.section_name,gu.* 
									from inventory_goods g 
									left join inventory_goods_unit gu on g.gid = gu.gid
									left join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
									left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix	
									left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
									where gu.gid = '".$gid."' ";

						$db->query($sql);
						$palce_stock_infos = $db->fetchall();


						for($i=0;$i < count($palce_stock_infos) ;$i++){
							//$db->fetch($i);

							if($palce_stock_infos[$i][gi_ix] != ""){
								$opnd_ix_str = " and gi_ix = '".$palce_stock_infos[$i][gi_ix]."' ";
							}

							if($_GET["pi_ix"] != "" && $_GET["pi_ix"] != "all"){
								$pi_ix_str = " and pi_ix = '".$_GET["pi_ix"]."' ";
							}

							$sql = "select sum(stock) as stock from inventory_product_stockinfo where gid = '".$gid."' $pi_ix_str  $opnd_ix_str group by gid";
							//echo nl2br($sql)."<br><br>";
							$db->query($sql);
							//$db->query("select stock from inventory_product_stockinfo where pid = '".$id."' $pi_ix_str $opn_ix_str  $opnd_ix_str ");


							if($db->total){
								$db->fetch();
								$stock = $db->dt[stock];
								$sum = $sum + $db->dt[stock];
							}else{
								$stock = 0;
							}
							$safe_sum = $safe_sum + $palce_stock_infos[$i][safestock];

							//$sum = $sum + $palce_stock_infos[$i][stock];
							//$safe_sum = $safe_sum + $palce_stock_infos[$i][safestock];


$Contents .= "			<tr bgcolor='#ffffff' height=27>
							<td align=center >".getUnit($palce_stock_infos[$i][unit], "basic_unit","","text")."</td>
							<td align=center >".$palce_stock_infos[$i][item_code]."</td>
							<input type=hidden name='options[".$i."][gi_ix]' value=".$palce_stock_infos[$i][gi_ix].">
							<input type=hidden name='options[".$i."][item_name]' value=".$palce_stock_infos[$i][item_name].">
							<input type=hidden name='options[".$i."][item_code]' value=".$palce_stock_infos[$i][item_code].">

							<td align=center ><input type='text' class='textbox number order_cnt' name='options[".$i."][order_cnt]' id='order_cnt' size=10 onkeyup='sumStock(".$stock.")' value='' validation=false title='발주수량'></td>
							<td align=center ><input type='text' class='textbox number order_price' name='options[".$i."][order_coprice]' id='order_price' size=10 onkeyup='sumStock(".$palce_stock_infos[$i][input_price].")' value='".($palce_stock_infos[$i][input_price] == "0" ? "":$palce_stock_infos[$i][input_price])."' validation=false title='발주금액' ></td>
							<td align=center >".$palce_stock_infos[$i][input_price]."</td>
							<td align=center >".$stock."</td>
							<td align=center >".$palce_stock_infos[$i][safestock]."</td>
						</tr>";

						}
$Contents .= "
						<tr bgcolor='#ffffff'>
							<td height=30 align=center colspan=2><b>합계</b></td>
							<td align=center><input type=text class='textbox number' name='total_order_stock' value='' name='total_inputstock' id=total_inputstock size=10 ".(count($palce_stock_infos) != 0 ? "readonly":"")."></td>
							<td align=center><!--input type=text class='textbox' name='total_inputstock' value=''  id=total_inputstock size=10 ".(count($palce_stock_infos) != 0 ? "readonly":"")."--></td>
							<td align=center >-</td>
							<td align=center >".$sum."</td>
							<td align=center >-</td>

						</tr>";

$Contents .= "
						<tr height=30px bgcolor='#ffffff'>
							<td colspan=2 align=center style=''><b >발주 내용</b></td>

							<td colspan=5 style='padding:5px;'><textarea name='input_msg' style='width:94%;padding:5px;' height=50px></textarea></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align=center style='padding:20px 0px;'>
					<img src='../images/".$admininfo["language"]."/btn_ok.gif' onclick=\"StockSubmit(document.order_pop,'insert')\" style='cursor:pointer'>
					<img src='../images/".$admininfo["language"]."/btn_close.gif' onclick='self.close()' style='cursor:pointer'>
				</td>
			</tr>
		</table>
		</td>
	</tr>

</TABLE>
</form>";


$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >발주후 입고될 입고창고를 지정하여 발주를 합니다. </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td > </td></tr>
</table>
";
//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= HelpBox("발주하기", $help_text, 60);


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "재고관리 > 발주하기";
$P->NaviTitle = "발주하기";
$P->title = "발주하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>
