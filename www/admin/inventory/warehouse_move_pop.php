<?
include("../class/layout.class");
include("inventory.lib.php");

$Script = "<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>";

$Script .= "
<script type='text/javascript'>
<!--
$(document).ready(function(){
	$('select#company_id ').find('option:first').remove();
	$('#pi_ix').find('option:first').remove();
	$('#delivery_ps_ix').find('option').remove();
})
//-->
</script>";



$db = new Database;

$sql="SELECT g.gid,gname,g.standard,g.item_account,gu.unit,ips.company_id,ips.pi_ix,ips.ps_ix,ifnull(sum(ips.stock),0) as stock,pi.place_name, ps.section_name
	FROM
	inventory_goods g 
	right join inventory_goods_unit gu  on g.gid =gu.gid
	right join  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
	left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix	
	left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
	where gu.gu_ix='".$gu_ix."' and ips.ps_ix = '".$ps_ix."'";

$db->query($sql);
$db->fetch();

$Contents = "
<form name='input_pop' method='post'  action='./warehouse_move.act.php' onsubmit='return CheckFormValue(this);' target='act'>
<input type='hidden' name='act' value='warehouse_move'>
<input type='hidden' name='mmode' value='pop'>
<input type='hidden' name='gu_ix' value='".$gu_ix."'>
<input type='hidden' name='ps_ix' value='".$db->dt[ps_ix]."'>


<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>

			<!--tr height=25 align='left'>
				<td style='padding:10px 0px 0px 0px'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>상품정보</b></td>
			</tr-->

			<tr>
				<td style='padding:5px 0px 0px 0px'>

						<table border=0 cellpadding=0 cellspacing=0 width='100%'>
							<tr>
								<td width='*' valign=top>
									<table border=0 cellpadding=0 cellspacing=0 width='100%' class='list_table_box'>
										<col width='15%' >
										<col width='*' >
										<col width='15%' >
										<col width='10%' >
										<col width='15%' >
										<tr height='25'>
											<td class='s_td'>품목코드</td>
											<td class='m_td'>품목명</td>
											<td class='m_td'>규격(옵션)</td>
											<td class='m_td'>단위</td>
											<td class='e_td'>품목계정</td>
										</tr>
										<tr height='25'>
											<td align='center'>".$db->dt[gid]."</td>
											<td class='point' align='left'>&nbsp;".$db->dt[gname]."</td>
											<td align='center'>".$db->dt[standard]."</td>
											<td align='center' >".getUnit($db->dt[unit],"","","text")."</td>
											<td align='center'>".getItemAccount($db->dt[item_account],"","","text")."</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>

				</td>
			</tr>

			<tr>
				<td style='padding:5px 0px 0px 0px'>
					<table border=0 cellpadding=0 cellspacing=0 width='100%'>
							<tr>
								<td width='*' valign=top>
									<table border=0 cellpadding=0 cellspacing=0 width='100%' class='list_table_box'>
										<col width='25%' >
										<col width='25%' >
										<col width='20%' >
										<col width='15%' >
										<col width='15%' >
										<tr height='25'>
											<td class='s_td' colspan='3'>현재창고</td>
											<td class='m_td' rowspan='2'>현재고</td>
											<td class='e_td' rowspan='2'>이동수량</td>
										</tr>
										<tr height='25'>
											<td class='s_td'>사업장</td>
											<td class='m_td'>창고</td>
											<td class='m_td'>보관장소</td>
										</tr>
										<tr height='25'>
											<td align='center'>&nbsp;".SelectEstablishment($db->dt[company_id],"company_id","text","","")."</td>
											<td align='center'>&nbsp;".$db->dt[place_name]."</td>
											<td align='center'>&nbsp;".$db->dt[section_name]."</td>
											<td class='point' align='center'>".number_format($db->dt[stock])."</td>
											<td align='center' ><input type='text' name='delivery_cnt' value='".$db->dt[stock]."' validation='true' title='이동수량' class='textbox number' style='width:30px;'/></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
				</td>
			</tr>

			<tr>
				<td style='padding:5px 0px 0px 0px'>
					<table border=0 cellpadding=0 cellspacing=1 width='100%' class='list_table_box' >
						<col width='33%'>
						<col width='33%'>
						<col width='33%'>
						<tr height='25'>
							<td class='s_td' colspan='3'>이동 창고 선택</td>
						</tr>
						<tr height='30px'>
							<td class='s_td' align=center>사업장</td>
							<td class='m_td' align=center>창고</td>
							<td class='e_td' align=center>보관장소</td>
						</tr>
						<tr height='210'><!--selectbox-multiple 은 로드시 스키립트로 옵션 처리했음-->
							<td align=center >".SelectEstablishment("","company_id","select","true","onclick=\"loadPlace(this,'pi_ix','multiple');\"  multiple style='width:200px;height:180px;' ")."</td>
							<td align=center >".SelectInventoryInfo("", "",'pi_ix','select','true', "onclick=\"loadPlaceSection(this,'delivery_ps_ix','multiple')\" multiple style='width:200px;height:180px;' ")."</td>
							<td align=center >".SelectSectionInfo("","",'delivery_ps_ix',"select","true" ," multiple style='width:200px;height:180px;' ")."
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align=center style='padding:20px 0px;'>
					<img src='../images/".$admininfo["language"]."/btn_ok.gif' onclick=\"$('form[name=input_pop]').submit();\" style='cursor:pointer'>
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
$P->Navigation = "재고관리 > 창고(내부)이동 선택";
$P->NaviTitle = "창고(내부)이동 선택";
$P->title = "창고(내부)이동 선택";
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
