<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("inventory.lib.php");

$Script = "<script language='JavaScript' src='input_pop.js'></Script>";



$db = new Database;

$db->query("SELECT id,pcode,pname,sellprice,coprice, p.supply_company, p.inventory_info, p.stock,p.sell_ing_cnt, p.surtax_yorn, regdate FROM ".TBL_SHOP_PRODUCT." p  where id = '".$id."'");
$db->fetch();
$product_info = $db->dt;
if($db->dt[supply_company] != "" && $db->dt[supply_company]>0){
	$ci_ix = $db->dt[supply_company];
}

if($pi_ix == "" && $select_pi_ix != ""){
	$pi_ix = $select_pi_ix;	
}else{
	if($pi_ix == "" && $db->dt[inventory_info] != ""){
		$select_pi_ix = $db->dt[inventory_info];
	}
}

$Contents = "
<form name='input_pop' method='post'  action='input_pop.act.php' target='act'>
<input type='hidden' name='mode' value=''>
<input type='hidden' name='pid' value=".$id.">
<input type='hidden' name='pi_ix' id='pi_ix'  value='".($pi_ix != "" ? $pi_ix:$select_pi_ix)."'>
<input type='hidden' name='pname' value='".$db->dt[pname]."'>
<input type='hidden' name='input_owner' value='".$_SESSION["admininfo"]["charger_id"]."'>
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>			
			<tr height=25 align='left'>
				<td style='padding:10px 0px 0px 0px'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>상품정보</b></td>
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
												".SelectSupplyCompany($ci_ix,"ci_ix",($db->dt[supply_company] == "0" ? "select":"text"))." 
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
											".SelectSupplyCompany($ci_ix,"ci_ix")."
										</td>
									</tr>
									<tr >
										<td class='input_box_title' nowrap> 보관장소</td>
										<td class='input_box_item' style='padding-left:5px; vertical-align:middle'>".makeSelectBoxPlace($idb,'inventory_place_info','','pi_ix','등록된보관장소 가 없습니다.')."
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>	 
				</td>
			</tr-->
			
			<tr>
				<td align='left' style='padding:10px 0px 0px 0px' height=25><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>재고 및 입고내용</b></td>
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
									<table id='tab_01' ".(($pi_ix == $db->dt[pi_ix]) ? "class='on'":"")."  >
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='?id=".$_GET["id"]."&ci_ix=".$ci_ix."&pi_ix=".$db->dt[pi_ix]."'\">".$db->dt[place_name]."</td>
										<th class='box_03'></th>
									</tr>
									</table>";
	}

		$Contents .= "
									<table id='tab_02' ".(!$warehouse_seleted ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' style='padding:4px 8px 0px 8px ;;vertical-align:middle;' onclick=\"document.location.href='?id=".$_GET["id"]."&pi_ix='\">창고전체</td>
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
			<tr>
				<td style='padding:5px 0px 0px 0px'>
					<table border=0 cellpadding=0 cellspacing=1 width='100%' class='list_table_box' >
						<col width=15%>
						<col width=*>
						<col width=15%>
						<col width=11%>
						<col width=11%>
						<col width=11%>
						<col width=11%>
						<col width=11%>
						<tr height=30px bgcolor=#e5e5e5>
							<td class='s_td' align=center >옵션이름</td>
							<td class='m_td' align=center>옵션명(규격)</td>
							<td class='m_td' align=center>옵션코드</td>
							<td class='m_td' align=center>입고수량</td>
							<td class='m_td' align=center>입고단가</td>
							<td class='m_td' align=center>입고기준가</td>
							<td class='m_td' align=center>재고</td>
							<td class='e_td' align=center>안전재고</td>
						</tr>";


						$sql = "select opn_ix from shop_product_options where pid = '".$id."' and option_kind = 'b' ";
						//echo $sql;
						$db->query($sql);
						if($db->total){
							$sql = "select ot.option_stock as stock,ot.option_safestock as safestock,o.option_type,ot.option_div,ot.id,o.option_name , ot.option_coprice, ot.option_code, ot.opn_ix, ot.id as opnd_ix
										from shop_product_options o , shop_product_options_detail ot 
										where o.opn_ix = ot.opn_ix and o.pid = '".$id."' and ot.pid = '".$id."' and o.option_kind = 'b' group by id ";

							$db->query($sql);
							$palce_stock_infos = $db->fetchall();

							for($i=0;$i<count($palce_stock_infos);$i++){
								//$db->fetch($i);

								if($palce_stock_infos[$i][opn_ix] != ""){
									$opn_ix_str = " and opn_ix = '".$palce_stock_infos[$i][opn_ix]."' ";
								}

								if($palce_stock_infos[$i][opnd_ix] != ""){
									$opnd_ix_str = " and opnd_ix = '".$palce_stock_infos[$i][opnd_ix]."' ";
								}

								if($pi_ix != ""){
									$pi_ix_str = " and pi_ix = '".$pi_ix."' ";
								}

								$sql = "select vdate, sum(stock) as stock from inventory_product_stockinfo where pid = '".$id."' $pi_ix_str $opn_ix_str  $opnd_ix_str group by vdate, pid , opnd_ix  ";
								//echo nl2br($sql)."<br><br>";
								$db->query($sql);
								

								if($db->total){
									

								for($j=0;$j < $db->total;$j++){
									$db->fetch($j);
									$stock = $db->dt[stock];
									$sum = $sum + $db->dt[stock];

									$safe_sum = $safe_sum + $palce_stock_infos[$i][safestock];
									//$sum = $sum + $palce_stock_infos[$i][stock];
									//$safe_sum = $safe_sum + $palce_stock_infos[$i][safestock];

	$Contents .= "			<tr bgcolor='#ffffff' height=27>";

									if($i ==0){
	$Contents .= "			<td rowspan='".count($palce_stock_infos)."' align=center >".$palce_stock_infos[$i][option_name]."</td>";
									}

	$Contents .= "			<td align=center >".$palce_stock_infos[$i][option_div]."</td>
									<td align=center >".$palce_stock_infos[$i][option_code]."
									
									<input type=hidden name='options[".$i."][opn_ix]' value=".$palce_stock_infos[$i][opn_ix].">
									<input type=hidden name='options[".$i."][opnd_ix]' class='opnd_ix' value=".$palce_stock_infos[$i][opnd_ix].">
									<input type=hidden name='options[".$i."][option_div]' value=".$palce_stock_infos[$i][option_div].">
									</td>
									<td align=center ><input type='text' class='textbox number input_cnt' name='options[".$i."][input_cnt]' id='input_cnt_".$palce_stock_infos[$i][opnd_ix]."' size=8 onkeyup='sumStock(".$stock.")' value='' validation=false title='입고수량'></td>
									<td align=center ><input type='text' class='textbox number input_price' name='options[".$i."][input_price]' id='input_price_".$palce_stock_infos[$i][opnd_ix]."' size=8  value='' validation=false title='입고단가'></td>
									<td align=center >".$palce_stock_infos[$i][option_coprice]."</td>
									<td align=center >".$stock."<input type='hidden' class='textbox stock' name='options[".$i."][stock]' id='stock_".$palce_stock_infos[$i][opnd_ix]."' size=8 value=".$stock." style='border:0px;' readonly></td>
									<td align=center >".$palce_stock_infos[$i][safestock]." <input type='hidden' class='textbox' name='safestock[]' id='safestock' size=8 value=".$palce_stock_infos[$i][safestock]." onkeyup='sumStock2()' style='border:0px;' readonly></td>
								</tr>";
								}

									
								}else{
									$stock = 0;
								}


							}
$Contents .= "			
						<tr bgcolor='#ffffff'>
							<td height=30 align=center colspan=3><b>합계</b></td>
							<td align=center><input type=text class='textbox number' name='total_inputstock' id=total_inputstock size=8 ".(count($palce_stock_infos) != 0 ? "readonly":"")."></td>
							<td align=center style=''></td>
							<td align=center style=''></td>
							<td align=center >".$sum."<input type=hidden class='textbox' name='total_stock' size=8 value=".$sum." style='border:0px;'></td>
							<td align=center ><!--input type=text class='textbox' name='total_safestock' size=8 value=".$safe_sum." ".(count($palce_stock_infos) != 0 ? "readonly":"")."--></td>

						</tr>";

						}else{
							$db->query("select ips.stock,sp.safestock, coprice from shop_product sp left join inventory_product_stockinfo ips on sp.id = ips.pid where id = '".$id."'");
							$db->fetch();

$Contents .= "			
						<tr height=30px bgcolor='#ffffff'>
							<td width=100px align=center style='' colspan=3>".$product_info[pname]."</td>
							<td style='' align=center><input type=text class='textbox number' name='input_cnt' id=input_cnt size=8 validation=true title='수량'></td>
							<td align=center style=''><input type=text class='textbox number' name='input_price' id=input_price size=8 validation=true title='입고가격'></td>
							<td align=center >".number_format($db->dt[coprice])."</td>
							<td align=center style=''>".number_format($db->dt[stock])."<input type=hidden class='textbox number' name='total_stock' size=8 value=".$db->dt[stock]."></td>
							<td align=center style=''>".number_format($db->dt[safestock])."</td>
							
						</tr>";

						}
$Contents .= "			
						<tr height=30px bgcolor='#ffffff'>
							<td colspan=8 align=center style=''><b>입고내용</b></td>
						</tr>
						<tr bgcolor='#ffffff'>
							<td colspan=8 style='padding:5px;'><textarea name='input_msg' style='width:94%;' height=50px></textarea></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style='padding:20px 0px;'>
					<img src='../images/".$admininfo["language"]."/btn_ok.gif' onclick=\"StockSubmit(document.input_pop,'insert')\" style='cursor:pointer'> 
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
$P->Navigation = "재고관리 > 입고하기";
$P->NaviTitle = "입고하기";
$P->title = "입고하기";
$P->strContents = $Contents;
$P->OnloadFunction = "";
echo $P->PrintLayOut();

/*
function makeSelectBoxPlace($sdb,$table,$where,$select_name,$msg){
	global $id,$pi_ix;
	$db = new Database;
	$sql = "SELECT * FROM ".$table." where disp = 'Y'  ";
	//echo $sql;
	$sdb->query($sql);

	$mstring = "<select id='$select_name' name='$select_name'  ".($pi_ix == "" ? "":"disabled")." validation='true' title='보관장소' style='width:200px;'>";
	$mstring .= "<option value=''>보관장소를 선택해 주세요</option>";

		if($sdb->total){
			for($i=0;$i < $sdb->total;$i++){
				$sdb->fetch($i);
				$mstring .= "<option value='".$sdb->dt[pi_ix]."' ".($sdb->dt[pi_ix] == $pi_ix ? "selected":"").">".$sdb->dt[place_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>".$msg."</option>";
		}
		$mstring .= "</select> ".($pi_ix == "" ? "":"<input type='checkbox' name='place_change' value='Y' style='border:0px' onclick=\"inventoryChange(this,'".$pi_ix."')\"> 보관장소 변경<span id='insert_place_info'><input type='hidden' name='$select_name' value='".$pi_ix."'></span>")." ";

	return $mstring;
}
*/

?>
