<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("./inventory.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");



$Script = "<script language='JavaScript' src='./delivery_pop.js'></Script>
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>";


$db = new Database;
$mdb = new Database;
$sdb = new Database;
$idb = new Database;

//$db->query("SELECT id,pcode,pname,sellprice,regdate,coprice , p.supply_company, p.surtax_yorn ,p.stock, p.sell_ing_cnt,p.inventory_info FROM ".TBL_SHOP_PRODUCT." p  where id = '".$id."'");
$db->query("SELECT gid,gcode,gname, g.ci_ix, g.pi_ix, regdate FROM inventory_goods g  where gid = '".$gid."'");
$db->fetch();
$product_info = $db->dt;
//echo $product_info[supply_company];
//exit;
if($product_info[ci_ix] != "" ){
	$ci_ix = $product_info[ci_ix];
}


if($pi_ix == "" && $select_pi_ix != ""){
	$pi_ix = $select_pi_ix;
}else{
	if($pi_ix == "" && $product_info[pi_ix] != ""){
		$select_pi_ix = $product_info[pi_ix];
		//$pi_ix = $product_info[pi_ix];
	}
}



$Contents = "
<form name='output_pop' method='post' action='delivery_pop.act.php' onsubmit=\"return StockSubmit(this)\"><!-- target='act'-->
<input type='hidden' name='act' value='delivery'>
<input type='hidden' name='gid' value=".$gid.">
<input type='hidden' name='pi_ix' id='pi_ix' value=".($pi_ix != "" ? $pi_ix:$select_pi_ix).">
<input type='hidden' name='gname' value=".$product_info[gname].">
<input type='hidden' name='output_owner' value='".$_SESSION["admininfo"]["charger_id"]."'>
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr>
				<td align='left' style='padding:10px 0px 0px 0px' height=25><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title' >상품정보</b></td>
			</tr>
			<tr>
				<td style='padding:10px 0px 0px 0px'>
								<table border=0 cellpadding=0 cellspacing=0 width='100%'>
									<tr>
										<td style='width:150px;padding:0px;border:1px solid silver;border-right:0px;' align=center>";

										if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $product_info[gid], "c"))){
											$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $product_info[gid], "c");
										}else{
											$img_str = "../image/no_img.gif";
										}
$Contents .= "
										<img src='".$img_str."'></td>
										<td width='*' valign=top>
											<table border=0 cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
												<col width='25%' >
												<col width='25%' >
												<col width='25%' >
												<col width='25%' >
												<tr align='left' height='25'>
													<td class='input_box_title'><b>제품명</b></td>
													<td class='input_box_item' colspan=3>&nbsp;".$product_info[gname]."</td>
												</tr>
												<tr align='left' height='25'>
													<td class='input_box_title'> <b>상품코드</b></td>
													<td class='input_box_item'>&nbsp;".$product_info[gid]."</td>
													<td class='input_box_title'><b></b></td>
													<td class='input_box_item' ></td>
													<!--td class='input_box_title'><b>과세여부</b></td>
													<td class='input_box_item' title='".$product_info[surtax_yorn]."' nowrap>
													 ".($product_info[surtax_yorn] == "Y" ? "면세(비과세)":"과세")."
													</td-->
												</tr>
												<!--tr align='left' height='25'>
													<td class='input_box_title'><b>판매가</b></td>
													<td class='input_box_item'>&nbsp;".number_format($product_info[sellprice])." 원</td>
													<td class='input_box_title'><b>공급가</b></td>
													<td class='input_box_item'>&nbsp;".number_format($product_info[coprice])." 원</td>
												</tr>
												<tr align='left' height='25'>
													<td class='input_box_title'><b>상품 재고</b></td>
													<td class='input_box_item point'>&nbsp;".number_format($product_info[stock])." 개</td>
													<td class='input_box_title' nowrap><b>판매진행중 재고</b></td>
													<td class='input_box_item point'>&nbsp;".number_format($product_info[sell_ing_cnt])." 개</td>
												</tr-->
												<tr align='left' height='25'>
													<td class='input_box_title' nowrap>기본 입고처</td>
													<td class='input_box_item'  style='padding:2px 5px;'>
														".SelectSupplyCompany($ci_ix,"ci_ix",($product_info[ci_ix] == "" ? "select":"text"))."
													</td>
													<td class='input_box_title'><b>등록일</b></td>
													<td class='input_box_item' >&nbsp;".substr($product_info[regdate],0,10)."</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
				</td>
			</tr>
			<tr>
				<td align='left' style='padding:10px 0px 5px 0px' height=25><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>재고 및 출고정보</b></td>
			</tr>
			<tr>
					<td align='left' colspan=4 style='padding-bottom:10px;'>
						<div class='tab'>
							<table class='s_org_tab' style='width:100%' border=1>
							<tr>
								<td class='tab'>";

	//$sql = "select pi.pi_ix, place_name from inventory_place_info pi , inventory_product_stockinfo ps where pi.pi_ix = ps.pi_ix and gid = '".$_GET[gid]."' group by pi.pi_ix ";
	$sql = "select distinct (pi.pi_ix), place_name from inventory_place_info pi , inventory_product_stockinfo ps where pi.pi_ix = ps.pi_ix and gid = '".$_GET[gid]."' ";
	//echo $sql;
	$db->query($sql);
	$db->fetch();
	$warehouse_seleted = false;
	if($db->total){
		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$reg_pi_ix[$i] = $db->dt[pi_ix];

			if($pi_ix == $db->dt[pi_ix]){
				$warehouse_seleted = true;
			}
			$Contents .= "
										<table id='tab_01' ".(($pi_ix == $db->dt[pi_ix]) ? "class='on'":"")."  >
										<tr>
											<th class='box_01'></th>
											<td class='box_02 tab_pi_ix' onclick=\"document.location.href='?gid=".$_GET["gid"]."&ci_ix=".$ci_ix."&pi_ix=".$db->dt[pi_ix]."'\">".$db->dt[place_name]."</td>
											<th class='box_03'></th>
										</tr>
										</table>";
		}
	}else{
			$Contents .= "
										<table id='tab_01' class='on' >
										<tr>
											<th class='box_01'></th>
											<td class='box_02 ' >'".$product_info[gname]."' 상품에대한 입고정보가 없습니다.</td>
											<th class='box_03'></th>
										</tr>
										</table>";
	}
/*
		$Contents .= "
									<table id='tab_02' ".(!$warehouse_seleted ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' style='padding:0px 5px;vertical-align:middle;' onclick=\"document.location.href='?id=".$_GET["id"]."&ci_ix=".$ci_ix."&pi_ix='\">창고전체</td>
										<td class='box_02' style='padding:3px 0px 0px 0px ;' > ".makeSelectBoxPlace('select_pi_ix',$reg_pi_ix, ($db->total > 0 ? false:true))."</td>
										<th class='box_03'></th>
									</tr>
									</table>
									";



$sql = "select opn_ix from shop_product_options where pid = '".$id."' and option_kind = 'b' ";
//echo $sql;
$db->query($sql);
*/

		$Contents .= "
								</td>
								<td class='btn' style='vertical-align:bottom;padding-bottom:5px;' align=right>

								<!--input type=checkbox name='move_warehouse' id='move_warehouse'  value='1'><label for='move_warehouse'>창고이전</label-->
								</td>
							</tr>
							</table>
							</div>
					</td>
				</tr>
			<tr>
				<td style='padding:5px 0px 0px 0px'>
					<table border=0 cellpadding=0 cellspacing=0 width='100%' class='list_table_box' >
						<col width=*>
						<col width=11%>
						<col width=10%>
						<col width=11%>
						<col width=11%>
						<col width=11%>
						<col width=10%>
						<!--col width=10%-->
						<col width=11%>
						<tr height=30px bgcolor=#e5e5e5>
							<td class='s_td' align=center >보관장소</td>
							<td class='s_td' align=center nowrap>단위</td>
							<td class='m_td' align=center>규격</td>
							<td class='m_td' align=center>출고수량</td>
							<td class='m_td' align=center>출고가격</td>
							<td class='m_td' align=center>입고기준일</td>
							<td class='m_td' align=center>입고기준가</td>
							<td class='m_td' align=center>창고별재고</td>
							<!--td class='m_td' align=center>전체재고</td-->
							<td class='e_td' align=center nowrap>안전재고</td>
						</tr>";



						
							

							if($pi_ix != ""){
								$pi_ix_str = " and ps.pi_ix = '".$pi_ix."' ";
							}

							$sql = "select section_name, IFNULL(vdate,'-') as vdate , gu.safestock as safestock,gu.unit, gu.gid,gu.buying_price,gu.sellprice, g.standard, g.gcode, IFNULL(stock,0) as stock
										from inventory_goods g 
										left join inventory_goods_unit gu on g.gid = gu.gid 
										left join inventory_product_stockinfo ps on gu.unit = ps.unit and ps.gid = '".$gid."'
										left join inventory_place_section  pse on ps.ps_ix = pse.ps_ix
										where gu.gid = '".$gid."' $pi_ix_str";


							//echo nl2br($sql);
							$db->query($sql);
						if($db->total){
							$palce_stock_infos = $db->fetchall();

							for($i=0;$i < count($palce_stock_infos);$i++){
								//$db->fetch($i);

								if($palce_stock_infos[$i][gi_ix] != ""){
									$gi_ix = " and gi_ix = '".$palce_stock_infos[$i][gi_ix]."' ";
								}


								//$sql = "select sum(stock) as stock from inventory_product_stockinfo ps where gid = '".$gid."' $pi_ix_str $opnd_ix_str group by gid , gi_ix  ";
								//echo nl2br($sql)."<br><br>";
								//$db->query($sql);


								if($db->total){
									$db->fetch();
									$stock = $db->dt[stock];
									$sum = $sum + $db->dt[stock];
								}else{
									$stock = 0;
								}

								/*
								$sql = "select sum(stock) as stock from inventory_product_stockinfo where gid = '".$gid."' $pi_ix_str $opnd_ix_str group by gid , gi_ix  ";
								//echo nl2br($sql)."<br><br>";
								$db->query($sql);


								if($db->total){
									$db->fetch();
									$all_stock = $db->dt[stock];
									$all_stock_sum = $all_stock_sum + $db->dt[stock];
								}else{
									$all_stock = 0;
								}
								*/

								$safe_sum = $safe_sum + $palce_stock_infos[$i][safestock];
								//$sum = $sum + $palce_stock_infos[$i][stock];
								//$safe_sum = $safe_sum + $palce_stock_infos[$i][safestock];

$Contents .= "			<tr bgcolor='#ffffff' height=27>";

$Contents .= "			<td align=center >".$palce_stock_infos[$i][section_name]."</td>
								<td align=center >".getUnit($palce_stock_infos[$i][unit], "unit","","text")."</td>
								<td align=center >".$palce_stock_infos[$i][gcode]."
								

								<input type=hidden name='options[".$i."][standard]' class='standard' value=".$palce_stock_infos[$i][standard].">
								<input type=hidden name='options[".$i."][unit]' value=".$palce_stock_infos[$i][unit].">
								<input type=hidden name='options[".$i."][gcode]' value=".$palce_stock_infos[$i][gcode].">
								<input type=hidden name='options[".$i."][vdate]' value=".$palce_stock_infos[$i][vdate].">
								<input type=hidden name='options[".$i."][ps_ix]' value=".$palce_stock_infos[$i][ps_ix].">
								</td>
								<td align=center ><input type='text' class='textbox number delivery_cnt' name='options[".$i."][delivery_cnt]' id='delivery_cnt_".$palce_stock_infos[$i][gi_ix]."' size=8 onkeyup='sumStock(".$stock.")' value='' validation=false title='출고수량'></td>
								<td align=center ><input type='text' class='textbox number delivery_price' name='options[".$i."][delivery_price]' id='delivery_price_".$palce_stock_infos[$i][gi_ix]."' size=8  value='".($i==0 ? $palce_stock_infos[$i][sellprice] : '')."' validation=false title='출고가격'></td>
								<td align=center >".$palce_stock_infos[$i][vdate]."</td>
								<td align=center >".$palce_stock_infos[$i][buying_price]."</td>
								<td align=center class='point'>".$palce_stock_infos[$i][stock]."<input type='hidden' class='textbox' name='options[".$i."][stock]' id='stock_".$palce_stock_infos[$i][gi_ix]."' size=10 value=".$palce_stock_infos[$i][stock]." style='border:0px;' readonly></td>
								<!--td align=center >".$all_stock."</td-->
								<td align=center >".$palce_stock_infos[$i][safestock]." <input type='hidden' class='textbox' name='options[".$i."][safestock]' id='safestock' size=10 value=".$palce_stock_infos[$i][safestock]." onkeyup='sumStock2()' style='border:0px;' readonly></td>
							</tr>";

							}
$Contents .= "
						<tr bgcolor='#ffffff'>
							<td height=30 align=center colspan=3><b class='blk'>합계</b></td>
							<td align=center><input type=text class='textbox number' name='total_delivery_stock' id=total_delivery_stock size=8 ".(count($palce_stock_infos) != 0 ? "readonly":"")."></td>
							<td align=center ><!--input type=text class='textbox' name='total_safestock' size=8 value=".$safe_sum." ".(count($palce_stock_infos) != 0 ? "readonly":"")."--></td>
							<td align=center ></td>
							<td align=center ></td>
							<td align=center class='point'>".$sum."<input type=hidden class='textbox' name='total_stock' size=8 value=".$sum." style='border:0px;'></td>
							<!--td align=center >".$all_stock_sum."</td-->
							<td align=center style=''></td>
						</tr>";

						}else{
$Contents .= "	<tr height=40><td colspan=9>출고가능한 재고정보가 없습니다.</td></tr>";

						}
/*
							$sql = "select sum(stock) as stock from inventory_product_stockinfo where pid = '".$id."' $opn_ix_str  $opnd_ix_str group by pid , opnd_ix  ";
							//echo nl2br($sql)."<br><br>";
							$db->query($sql);


							if($db->total){
								$db->fetch();
								$all_stock = $db->dt[stock];
								$all_stock_sum = $all_stock_sum + $db->dt[stock];
							}else{
								$all_stock = 0;
							}

							if($pi_ix != ""){
								$pi_ix_str = " and pi_ix = '".$pi_ix."' ";
							}

							$db->query("select vdate, ips.stock,sp.safestock, sp.coprice from shop_product sp left join inventory_product_stockinfo ips on sp.id = ips.pid where id = '".$id."' $pi_ix_str "); //group by vdate
							$row_cnt = $db->total;



							for($i=0;$i <$db->total;$i++){
								$db->fetch($i);


$Contents .= "
						<tr height=30px bgcolor='#ffffff'>";
if($i==0){
$Contents .= "		<td width=100px align=center style='' rowspan='".$row_cnt."' colspan=3>".$product_info[pname]."</td>";
}
$Contents .= "		<td style='' align=center><input type=text class='textbox number' name='delivery_cnt' id=delivery_cnt size=8 validation=true title='출고 수량'></td>
							<td align=center style=''><input type=text class='textbox number' name='delivery_price' id=delivery_price size=8 value='' validation=true title='출고 가격'></td>
							<td align=center >".$db->dt[vdate]."</td>
							<td align=center >".number_format($db->dt[coprice])."</td>
							<td align=center style=''>".number_format($db->dt[stock])." <input type=hidden class='textbox number' name='stock' id='stock' size=10 value=".$db->dt[stock]."></td>";
if($i==0){
$Contents .= "		<td align=center style='' rowspan='".$row_cnt."'>".number_format($all_stock)." </td>";
}
$Contents .= "		<td align=center style=''>".number_format($db->dt[safestock])." </td>
						</tr>";
							}

						}
*/
$Contents .= "

					</table>
				</td>
			</tr>

			<!--tr>
				<td align='left' style='padding:10px 0px 0px 0px' height=25><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>출고정보</b></td>
			</tr-->
			<tr>
				<td style='padding:10px 0px 0px 0px'>
					<table border='0' width='100%' cellspacing='1' cellpadding='0' class='input_table_box'>
						<col width='20%'>
						<col width='30%'>
						<col width='20%'>
						<col width='30%'>
						<tr >
							<td class='input_box_title' nowrap>출고유형</td>
							<td class='input_box_item point' >
							".selectDeliveryType($dt_ix, "delivery_type",'O')."
							</td>
							<td class='input_box_title default' id='default'>출고처</td>
							<td class='input_box_item default' id='default'>
								".SelectBoxSellCustomer("d_ci_ix","")."&nbsp;&nbsp;
							</td>
							<td class='input_box_title move_warehouse_area' id='move_warehouse_area' style='display:none;'>이동창고</td>
							<td class='input_box_item move_warehouse_area' id='move_warehouse_area' style='display:none;'>
								<!--".makeSelectBoxTargetPlace($pi_ix, 'move_pi_ix')." -->
								".SelectInventoryInfo($pi_ix,'move_pi_ix','select','false', "onChange=\"loadPlaceSection(this,'move_ps_ix')\"  ")."
								".SelectSectionInfo($pi_ix,$move_ps_ix,'move_ps_ix',"select","true")." 
							</td>
						</tr>";
/*
$Contents .= "
						<!--tr>
							<td class='input_box_title' nowrap>출고상품 옵션별수량</td>
							<td class='input_box_item' colspan=3>
								".makeSelectBox2("opnd_ix","등록된 옵션이 없습니다.")."
							</td>
						</tr-->";
*/
$Contents .= "
						<tr height=70>
							<td class='input_box_title' nowrap>비고</td>
							<td class='input_box_item' style='text-align:left;padding:5px;' colspan=3><textarea name='delivery_msg' style='width:98%;height:50px;padding:3px;'></textarea></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align=center style='padding:10px'>
					<table>
						<tr>
							<td><input type=image src='../images/".$admininfo["language"]."/btn_ok.gif' style='cursor:pointer'> </td>
							<td><img src='../images/".$admininfo["language"]."/btn_close.gif' onclick='self.close()' style='cursor:pointer'></td>
						</tr>
					</table>
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
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >창고이동(보관장소 변경)를 선택하시면 이동창고를 선택 할수 있도록 노출됩니다. </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >출고할경우 보관장소와 입고일자를 기준으로 출고하셔야 합니다. </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >창고정보는 해당상품이 입고되어 있는 창고 정보만 노출되게 됩니다. </td></tr>
</table>
";
//$help_text =  getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= HelpBox("출고하기", $help_text, 60);


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "재고관리 > 출고하기";
$P->NaviTitle = "출고하기";
$P->title = "출고하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();

