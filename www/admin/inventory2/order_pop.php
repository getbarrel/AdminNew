<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("inventory.lib.php");

$Script = "<script language='JavaScript' src='order_pop.js'></Script>";



$db = new Database;
$db->query("SELECT id,pcode,pname,sellprice,coprice, p.stock,p.sell_ing_cnt , p.surtax_yorn ,regdate, supply_company , inventory_info FROM ".TBL_SHOP_PRODUCT." p  where id = '".$id."'");
$db->fetch();
$product_info = $db->dt;

if($db->dt[supply_company] != ""){
	$ci_ix = $db->dt[supply_company];
}

if($pi_ix == "" && $select_pi_ix != ""){
	$pi_ix = $select_pi_ix;	
}else{
	if($pi_ix == "" && $db->dt[inventory_info] != ""){
		//echo $db->dt[inventory_info];
		//exit;
		$select_pi_ix = $db->dt[inventory_info];
		$pi_ix = $db->dt[inventory_info];

	}
}
//print_r($_SESSION["admininfo"]);
$Contents = "
<form name='order_pop' method='post' onsubmit='return CheckFormValue(this)' action='order_pop.act.php' target='act'>
<input type='hidden' name='mode' value=''>
<input type='hidden' name='pid' value=".$id.">
<input type='hidden' name='pi_ix' id='pi_ix'  value='".($pi_ix)."'>
<input type='hidden' name='pname' value='".$db->dt[pname]."'>
<input type='hidden' name='surtax_yorn' value='".$db->dt[surtax_yorn]."'>
<input type='hidden' name='charger_ix' value='".$_SESSION["admininfo"]["charger_ix"]."'>
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>			
			<tr height=25 align='left'>
				<td style='padding:10px 0px 0px 0px'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title' onclick='CheckFormValue(document.order_pop)'>상품정보</b></td>
			</tr>
			<tr>
				<td style='padding:5px 0px 0px 0px'>
					
						<table border=0 cellpadding=0 cellspacing=0 width='100%'>
							<tr>
								<td style='width:150px;padding:5px;border:1px solid silver;border-right:0px;' align=center>";


								if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s"))){
									$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "s");
								}else{
									$img_str = "../image/no_img.gif";
								}
$Contents .= "
								<img src='".$img_str."'>
								</td>
								<td width='*' valign=top>
									<table border=0 cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
										<col width='25%' >
										<col width='25%' >
										<col width='25%' >
										<col width='25%' >
										<tr align='left' height='25'>
											<td class='input_box_title'><b>제품명</b></td>
											<td class='input_box_item' colspan=3>&nbsp;".$db->dt[pname]."</td>
										</tr>
										<tr align='left' height='25'>
											<td class='input_box_title'> <b>상품코드</b></td>
											<td class='input_box_item'>&nbsp;".$db->dt[pcode]."</td>
											<td class='input_box_title'><b>과세여부</b></td>
											<td class='input_box_item' title='".$db->dt[surtax_yorn]."' nowrap>
											 ".($db->dt[surtax_yorn] == "Y" ? "면세(비과세)":"과세")."
											</td>
										</tr>
										<tr align='left' height='25'>
											<td class='input_box_title'><b>판매가</b></td>
											<td class='input_box_item'>&nbsp;".number_format($db->dt[sellprice])." 원</td>
											<td class='input_box_title'><b>공급가</b></td>
											<td class='input_box_item'>&nbsp;".number_format($db->dt[coprice])." 원</td>
										</tr>
										<tr align='left' height='25'>
											<td class='input_box_title'><b>상품 재고</b></td>
											<td class='input_box_item point'>&nbsp;".number_format($db->dt[stock])." 개</td>
											<td class='input_box_title' nowrap><b>판매진행중 재고</b></td>
											<td class='input_box_item point'>&nbsp;".number_format($db->dt[sell_ing_cnt])." 개</td>
										</tr>
										<tr align='left' height='25'>											
											<td class='input_box_title' nowrap> 입고처</td>
											<td class='input_box_item'  style='padding:2px 5px;'>
												".SelectSupplyCompany($ci_ix,"ci_ix",($db->dt[supply_company] == "" ? "select":"text"))." 
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
											".SelectSupplyCompany($db->dt[supply_company],"ci_ix")."
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
	$sql = "select pi.pi_ix, place_name from inventory_place_info pi , inventory_product_stockinfo ps where pi.pi_ix = ps.pi_ix and pid = '".$_GET[id]."' group by pi_ix ";
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
										<td class='box_02' onclick=\"document.location.href='?id=".$_GET["id"]."&pi_ix=".$db->dt[pi_ix]."'\">".$db->dt[place_name]."</td>
										<th class='box_03'></th>
									</tr>
									</table>";
	}

		$Contents .= "
									<table id='tab_02' ".(!$warehouse_seleted ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' style='padding:4px 8px 0px 8px ;;vertical-align:middle;' onclick=\"document.location.href='?id=".$_GET["id"]."&pi_ix=all'\">창고전체</td>
										<td class='box_02' style='padding:4px 3px 0px 3px ;' > ".makeSelectBoxPlace($select_pi_ix, 'select_pi_ix',$reg_pi_ix, ($db->total > 0 ? false:true))."</td>
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
						<col width=13%>
						<col width=*>
						<col width=13%>
						<col width=13%>
						<col width=13%>
						<col width=13%>
						<col width=13%>
						<tr height=30px bgcolor=#e5e5e5>
							<td class='s_td' align=center >옵션이름</td>
							<td class='m_td' align=center>옵션명(규격)</td>
							<td class='m_td' align=center>옵션코드</td>
							<td class='m_td' align=center>발주 수량</td>
							<td class='m_td' align=center>발주가</td>
							<td class='m_td' align=center>입고기준가</td>
							<td class='m_td' align=center>재고</td>
							<td class='e_td' align=center>안전재고</td>
						</tr>";


						$sql = "select opn_ix from shop_product_options where pid = '".$id."' and option_kind = 'b' ";
						//echo $sql;
						$db->query($sql);
						if($db->total){
							$sql = "select od.option_stock as stock,od.option_safestock as safestock,o.option_type,od.option_div,od.id,o.option_name ,od.option_code, od.option_coprice, od.opn_ix, od.id as opnd_ix
										from shop_product_options o , shop_product_options_detail od 
										where o.opn_ix = od.opn_ix and o.pid = '".$id."' and od.pid = '".$id."' and o.option_kind = 'b' group by id ";

							$db->query($sql);
							$palce_stock_infos = $db->fetchall();


							for($i=0;$i < count($palce_stock_infos) ;$i++){
								//$db->fetch($i);

								if($palce_stock_infos[$i][opn_ix] != ""){
									$opn_ix_str = " and opn_ix = '".$palce_stock_infos[$i][opn_ix]."' ";
								}

								if($palce_stock_infos[$i][opnd_ix] != ""){
									$opnd_ix_str = " and opnd_ix = '".$palce_stock_infos[$i][opnd_ix]."' ";
								}

								if($_GET["pi_ix"] != "" && $_GET["pi_ix"] != "all"){
									$pi_ix_str = " and pi_ix = '".$_GET["pi_ix"]."' ";
								}

								$sql = "select sum(stock) as stock from inventory_product_stockinfo where pid = '".$id."' $pi_ix_str $opn_ix_str  $opnd_ix_str group by pid , opnd_ix  ";
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
								

$Contents .= "			<tr bgcolor='#ffffff' height=27>";

								if($i ==0){
$Contents .= "			<td rowspan='".count($palce_stock_infos)."' align=center >".$palce_stock_infos[$i][option_name]."</td>";
								}

$Contents .= "			<td align=center >".$palce_stock_infos[$i][option_div]."</td>
								<td align=center >".$palce_stock_infos[$i][option_code]."</td>
								<!--input type=hidden name='opn_ix[]' value=".$palce_stock_infos[$i][opn_ix].">
								<input type=hidden name='opnd_ix[]' value=".$palce_stock_infos[$i][opnd_ix].">
								<input type=hidden name='option_name[]' value=".$palce_stock_infos[$i][option_div]."-->
								
								<input type=hidden name='options[".$i."][opn_ix]' value=".$palce_stock_infos[$i][opn_ix].">
								<input type=hidden name='options[".$i."][opnd_ix]' value=".$palce_stock_infos[$i][opnd_ix].">
								<input type=hidden name='options[".$i."][option_name]' value=".$palce_stock_infos[$i][option_div].">

								<!--input type=hidden name='oid[]' value=".$palce_stock_infos[$i][id]."-->
								<td align=center ><input type='text' class='textbox number order_cnt' name='options[".$i."][order_cnt]' id='order_cnt' size=10 onkeyup='sumStock(".$stock.")' value='' validation=false title='발주수량'></td>
								<td align=center ><input type='text' class='textbox number order_price' name='options[".$i."][order_coprice]' id='order_price' size=10 onkeyup='sumStock(".$palce_stock_infos[$i][option_coprice].")' value='".($palce_stock_infos[$i][option_coprice] == "0" ? "":$palce_stock_infos[$i][option_coprice])."' validation=false title='발주금액' ></td>
								<td align=center >".$palce_stock_infos[$i][option_coprice]."</td>
								<td align=center >".$stock."</td>
								<td align=center >".$palce_stock_infos[$i][safestock]."</td>
							</tr>";

							}
$Contents .= "			
						<tr bgcolor='#ffffff'>
							<td height=30 align=center colspan=3><b>합계</b></td>
							<td align=center><input type=text class='textbox number' name='total_order_stock' value=''  id=total_inputstock size=10 ".(count($palce_stock_infos) != 0 ? "readonly":"")."></td>
							<td align=center><!--input type=text class='textbox' name='total_inputstock' value=''  id=total_inputstock size=10 ".(count($palce_stock_infos) != 0 ? "readonly":"")."--></td>
							<td align=center >-</td>
							<td align=center >".$sum."</td>
							<td align=center >-</td>

						</tr>";

						}else{
							$db->query("select stock,safestock, coprice from shop_product where id = '".$id."'");
							$db->fetch();

$Contents .= "			
						<tr height=30px bgcolor='#ffffff'>
							<td width=100px align=center style='' colspan=3>".$product_info[pname]."</td>
							<td style='' align=center><input type=text class='textbox number' name='order_cnt' id=order_cnt value='' size=10 validation=true title='발주수량'></td>
							<td align=center style=''><input type=text class='textbox number' name='order_coprice' size=10 value='' title='발주가'></td>
							<td align=center >".$db->dt[coprice]."</td>
							<td align=center >".$db->dt[stock]."</td>
							<td align=center >".$db->dt[safestock]."</td>
						</tr>";

						}
$Contents .= "			
						<tr height=30px bgcolor='#ffffff'>
							<td colspan=3 align=center style=''><b >발주 내용</b></td>
						
							<td colspan=5 style='padding:5px;'><textarea name='input_msg' style='width:94%;padding:5px;' height=50px></textarea></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style='padding:20px 0px;'>
					<img src='../images/".$admininfo["language"]."/btn_ok.gif' onclick=\"StockSubmit(document.order_pop,'insert')\" style='cursor:pointer'> 
					<img src='../images/".$admininfo["language"]."/btn_close.gif' onclick='self.close()' style='cursor:pointer'>
				</td>
			</tr>
		</table>
		</td>
	</tr>

</TABLE>
</form>";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "재고관리 > 발주하기";
$P->NaviTitle = "발주하기";
$P->title = "발주하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>
