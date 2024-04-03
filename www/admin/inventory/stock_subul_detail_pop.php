<?
include("../class/layout.class");
include("inventory.lib.php");

$Script = "<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>";
/*
$Script .= "
<script type='text/javascript'>
<!--
$(document).ready(function(){
	$('select#company_id ').find('option:first').remove();
	$('#pi_ix').find('option:first').remove();
	$('#delivery_ps_ix').find('option:first').remove();
})
	
//-->
</script>";
*/

$where =" and h_type not in ('IW') and h.vdate between '$sdate' and '$edate'";

if($ci_ix!=""){
	$title="거래처별";
	$where .=" and h.ci_ix='".$ci_ix."' ";
}elseif($b_ix!=""){
	$title="브랜드별";
	$where .=" and g.b_ix='".$b_ix."' ";
}elseif($maker!=""){
	$title="제조사별";
	$where .=" and g.maker='".$maker."' ";
}

if($gid!=""){
	$where .=" and hd.gid='".$gid."' ";
}

if($unit!=""){
	$where .=" and hd.unit='".$unit."' ";
}

$db = new Database;

$sql="select 
			vdate,hd.gid,g.gname, hd.standard,h_type,h_div,unit,price,
			ifnull(case when h_div = '1' then amount else -amount end,0) as amount
		from 
		inventory_history h,
		inventory_history_detail hd
		left join inventory_goods g on (hd.gid=g.gid)
		where  h.h_ix = hd.h_ix
		$where";

$db->query($sql);
$db->fetch();

$Contents = "
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
										<col width='5%' >
										<col width='10%' >
										<col width='15%' >
										<col width='*' >
										<col width='10%' >
										<col width='10%' >
										<col width='10%' >
										<col width='10%' >
										<col width='10%' >
										<tr height='25'>
											<td class='s_td' rowspan='2'>순번</td>
											<td class='m_td' rowspan='2'>일자</td>
											<td class='m_td' rowspan='2'>품목코드</td>
											<td class='m_td' rowspan='2'>품목명</td>
											<td class='m_td' rowspan='2'>단위/규격</td>
											<td class='m_td' rowspan='2'>입/출고유형</td>
											<td class='e_td' colspan='3'>입/출고수량</td>
										</tr>
										<tr height='25'>
											<td class='m_td'>수량</td>
											<td class='m_td'>단가</td>
											<td class='e_td'>합계</td>
										</tr>";
						
						if($db->total){
							for($i=0;$i<$db->total;$i++){
							$db->fetch($i);
							$Contents .= "
											<tr height='25' >
												<td class='list_box_td' >".($i+1)."</td>
												<td class='list_box_td list_bg_gray'>".substr($db->dt[vdate],0,4)."-".substr($db->dt[vdate],4,2)."-".substr($db->dt[vdate],6,2)."</td>
												<td class='list_box_td'>".$db->dt[gid]."</td>
												<td class='list_box_td'>".$db->dt[gname]."</td>
												<td class='list_box_td'>".getUnit($db->dt[unit],"","","text").($db->dt[standard] ? "/".$db->dt[standard] : "")."</td>
												<td class='list_box_td'>".selectDeliveryType($db->dt[h_div],"",$db->dt[h_type],"","text")."</td>
												<td class='list_box_td' >".number_format($db->dt[amount])."</td>
												<td class='list_box_td'>".number_format($db->dt[price])."</td>
												<td class='list_box_td'>".number_format($db->dt[amount]*$db->dt[price])."</td>
											</tr>";
							}
						}else{
							$Contents .= "
											<tr height='50'>
												<td class='list_box_td' align='center' colspan='9'>조회된 내역이 없습니다.</td>
											</tr>";
						}
						$Contents .= "
									</table>
								</td>
							</tr>
						</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</TABLE>";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "재고관리 > 상세입출고내역";
$P->NaviTitle = $title." 상세입출고내역";
$P->title = $title." 상세입출고내역";
$P->strContents = $Contents;
$P->OnloadFunction = "";
echo $P->PrintLayOut();


?>
