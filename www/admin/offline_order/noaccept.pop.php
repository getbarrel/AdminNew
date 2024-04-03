<?
include("../class/layout.class");

if($max == ""){
	$max = 10; //페이지당 갯수
}
if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;
$mdb = new Database;

if($oid!=""){
	$where = " and ni.oid='".$oid."'";
}


$sql = " select ni.*,cd.com_name from shop_noaccept_info ni left join common_company_detail cd on (ni.company_id=cd.company_id)
where ni.company_id='".$company_id."' and ni.status ='1' and cancel_update_yn ='N' $where ";

//echo $sql;
$db->query($sql);
$db->total();
//$db->fetch();
//echo $db->total();

$Script = "
<script language='JavaScript' >
	function CheckConfirm(){
		if(confirm('입력하신 정보로 미수금처리 하시겠습니까?')){
			return true;
		}else{
			return false;
		}
	}
</Script>";

$Contents = "
<form name='nprice' method='post' action='noaccept.act.php' onsubmit='return CheckConfirm();' target='act'>
<input type=hidden name='act' value='noaccept_withdraw' >
<input type=hidden name='company_id' value='".$company_id."' >
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("미수금관리", "미수금관리", false)."</td>
			</tr>
			<!--tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - 찾으실 ".$select_title."명 또는 ".$select_title."코드을 입력하세요.</td></tr-->
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				</td>
			</tr>
			<tr>
				<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top >
				<div style='overflow-y:scroll;height:300px;'>
				<table width=100% class='list_table_box'>
					<tr height='28' bgcolor='#ffffff'>
						<td width='10%' align='center' class='m_td'><font color='#000000'><b>주문일</b></font></td>
						<td width='*' align='center' class=m_td><font color='#000000'><b>주문번호</b></font></td>
						<td width='13%' align='center' class=m_td><font color='#000000'><b>거래처코드</b></font></td>
						<td width='13%' align='center' class=m_td><font color='#000000'><b>거래처명</b></font></td>
						<td width='13%' align='center' class=m_td><font color='#000000'><b>미수금</b></font></td>
						<td width='13%' align='center' class=m_td><font color='#000000'><b>잔여미수금액</b></font></td>
						<td width='13%' align='center' class=m_td><font color='#000000'><b>미수금입금금액</b></font></td>
					</tr>";


if($db->total){
	for($i=0;$i < $db->total; $i++){
		$db->fetch($i);

		$Contents .= "<tr height=25 style='text-align:center;'>
								<td class='list_box_td '>".substr($db->dt[regdate],0,10)."</td>
								<td class='list_box_td list_bg_gray' >".$db->dt[oid]."</td>
								<td class='list_box_td ' >".$db->dt[company_id]."</td>
								<td class='list_box_td list_bg_gray'>".$db->dt[com_name]."</td>
								<td class='list_box_td ' >".number_format($db->dt[price])." 원</td>
								<td class='list_box_td point' >".number_format($db->dt[price] - $db->dt[cancel_price])." 원</td>
								<td class='list_box_td ' ><input type='text' class='number' name='input_price[".$db->dt[oid]."]' value='".($db->dt[price] - $db->dt[cancel_price])."' /></td>
							</tr>";
	}

}else{
	$Contents .= "<tr height=50 style='text-align:center;'>
								<td class='list_box_td ' colspan='7'>미수금 보유 내역이 없습니다.</td>
							</tr>";
}


$Contents .= "
			</table>
			</div>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10px 0 0 0' colspan=2>
			<input type='image' src='../images/".$_SESSION["admininfo"]["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' \"> 
		</td>
	</tr>
</TABLE>
</form>
";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "미수금관리";
$P->NaviTitle = "미수금관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>





