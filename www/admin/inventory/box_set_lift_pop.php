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
	$('#delivery_ps_ix').find('option:first').remove();
})

function change_unit_cnt (obj,change_amount,stock){
	var val = parseInt(obj.val());
	var _change_amount = parseInt(change_amount);
	var _stock = parseInt(stock);

	if(val > _stock){
		obj.val(_stock);
		$('input[name=unit_lift_cnt]').val(_stock*_change_amount);
		$('#unit_lift_cnt').text(_stock*_change_amount);
	}else{
		$('input[name=unit_lift_cnt]').val(val*_change_amount);
		$('#unit_lift_cnt').text(val*_change_amount);
	}
}

//-->
</script>";



$db = new Database;

$sql="SELECT g.gid,gname,g.standard,g.item_account,gu.unit,gu.change_amount,ips.company_id,ips.pi_ix,ips.ps_ix,ifnull(sum(ips.stock),0) as stock,pi.place_name, ps.section_name,
	bp.company_id as basic_company_id ,bp.pi_ix as basic_pi_ix, bp.ps_ix as basic_ps_ix
	FROM
	inventory_goods g 
	right join inventory_goods_unit gu  on g.gid =gu.gid
	right join  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
	left join  inventory_goods_basic_place bp on (ips.company_id = bp.company_id and ips.pi_ix = bp.pi_ix and ips.gid = bp.gid and ips.unit = bp.unit)
	left join  inventory_place_info pi on ips.pi_ix = pi.pi_ix	
	left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
	where gu.gu_ix='".$gu_ix."' and ips.ps_ix = '".$ps_ix."'";

$db->query($sql);
$db->fetch();

$Contents = "
<form name='input_pop' method='post'  action='./box_set_lift.act.php' onsubmit='return CheckFormValue(this);' target='act'>
<input type='hidden' name='act' value='box_set_lift'>
<input type='hidden' name='mmode' value='pop'>
<input type='hidden' name='gu_ix' value='".$gu_ix."'>
<input type='hidden' name='gid' value='".$db->dt[gid]."'>
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
									<table border=0 cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
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
											<td class='input_box_item'>&nbsp;".$db->dt[gid]."</td>
											<td class='input_box_item'>&nbsp;".$db->dt[gname]."</td>
											<td class='input_box_item'>&nbsp;".$db->dt[standard]."</td>
											<td class='input_box_item' >&nbsp;".getUnit($db->dt[unit],"","","text")."</td>
											<td class='input_box_item'>&nbsp;".getItemAccount($db->dt[item_account],"","","text")."</td>
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
									<table border=0 cellpadding=0 cellspacing=0 width='100%' class='input_table_box'>
										<col width='18%' >
										<col width='18%' >
										<col width='18%' >
										<col width='15%' >
										<col width='15%' >
										<col width='15%' >
										<tr height='25'>
											<td class='s_td' colspan='3'>현재창고</td>
											<td class='m_td' rowspan='2'>단위/낱개수량</td>
											<td class='m_td' rowspan='2'>현재고/낱개수량</td>
											<td class='e_td' rowspan='2'>해재수량/낱개수량</td>
										</tr>
										<tr height='25'>
											<td class='s_td'>사업장</td>
											<td class='m_td'>창고</td>
											<td class='m_td'>보관장소</td>
										</tr>
										<tr height='25'>
											<td class='input_box_item'>&nbsp;".SelectEstablishment($db->dt[company_id],"company_id","text","","")."</td>
											<td class='input_box_item'>&nbsp;".$db->dt[place_name]."</td>
											<td class='input_box_item'>&nbsp;".$db->dt[section_name]."</td>
											<td class='input_box_item' style='text-align:center;' >".getUnit($db->dt[unit],"","","text")." / ".number_format($db->dt[change_amount])."</td>
											<td class='input_box_item' style='text-align:center;' >".number_format($db->dt[stock])." / ".number_format($db->dt[change_amount]*$db->dt[stock])."</td>
											<td class='input_box_item' style='text-align:center;' ><input type='text' name='lift_cnt' value='".$db->dt[stock]."' onkeyup=\"change_unit_cnt($(this),'".$db->dt[change_amount]."','".$db->dt[stock]."');\" validation='true' title='이동수량' class='number' style='width:30px;'/> / <span id='unit_lift_cnt'>".($db->dt[change_amount]*$db->dt[stock])."</span><input type='hidden' name='unit_lift_cnt' value='".($db->dt[change_amount]*$db->dt[stock])."' /></td>
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
							<td align=center >".SelectEstablishment($db->dt[basic_company_id],"company_id","select","true","onclick=\"loadPlace(this,'pi_ix','multiple');\"  multiple style='width:200px;height:200px;' ")."</td>
							<td align=center >".SelectInventoryInfo($db->dt[basic_company_id], $db->dt[basic_pi_ix],'pi_ix','select','true', "onclick=\"loadPlaceSection(this,'delivery_ps_ix','multiple')\" multiple style='width:200px;height:200px;' ")."</td>
							<td align=center >".SelectSectionInfo($db->dt[basic_pi_ix],$db->dt[basic_ps_ix],'delivery_ps_ix',"select","true" ," multiple style='width:200px;height:200px;' ")."
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
$P->Navigation = "재고관리 > Box/Set 해제";
$P->NaviTitle = "Box/Set 해제";
$P->title = "Box/Set 해제";
$P->strContents = $Contents;
$P->OnloadFunction = "";
echo $P->PrintLayOut();


?>
