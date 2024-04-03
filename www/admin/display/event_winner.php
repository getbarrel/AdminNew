<?
include("../class/layout.class");

//print_r($admininfo);
$db = new Database;



$Contents = "
<script language='javascript' src='"."tglib_orders.php?page=$page&ctgr=$ctgr&qstr=$qstr"."'></script>
<table width='100%' cellpadding=0 cellspacing=0>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("이벤트당첨자리스트", "프로모션/전시 > 이벤트당첨자리스트")."</td>
</tr>
<tr>
	<td align='left' colspan=4 style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:3px;'><img src='../image/title_head.gif' align=absmiddle><b> 당첨자정보검색</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
</tr>
</table>";



$Contents .= "

	<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='30' align='center'>
			<td width='25%' align='center' class='m_td'><font color='#000000'><b>당첨자 회원코드</b></font></td>
			<td width='25%' align='center' class='m_td'><font color='#000000'><b>당첨자 아이디</b></font></td>
			<td width='25%' align='center' class='m_td'><font color='#000000'><b>당첨자 이름</b></font></td>
			<!--td width='20%' align='center' class='m_td'><font color='#000000'><b>제목</b></font></td>
			<td width='*' align='center' class='m_td'><font color='#000000'><b>내용</b></font></td-->
			<td width='25%' align='center' class='m_td'><font color='#000000'><b>당첨일</b></font></td>
		</tr>";




$sql = "SELECT ew.* FROM shop_event_winner ew where ew.event_code='".$event_code."' and ew.sub_idx='".$sub_idx."' ";
$db->query($sql);

if($db->total){
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

	$Contents .= "
	  <tr height='28' align='center'>
		<td class='list_box_td list_bg_gray'>".$db->dt[mem_ix]."</td>
		<td class='list_box_td '>".$db->dt[mem_id]."</td>
		<td class='list_box_td list_bg_gray' >".$db->dt[mem_name]."</td>
		<!--td class='list_box_td ' >".$db->dt[title]."</td>
		<td class='list_box_td list_bg_gray' >".$db->dt[description]."</td-->
		<td class='list_box_td ' >".$db->dt[regdate]."</td>
		</tr>";
	}
}else{
	$Contents .= "<tr height=50><td colspan=7 align=center>조회된 결과가 없습니다.</td></tr>";
}
$Contents .= "
</table>
";



$P = new LayOut();
$P->strLeftMenu = display_menu();
$P->OnloadFunction = "";
$P->addScript = "$Script";
$P->Navigation = "프로모션/전시 > 이벤트당첨자리스트";
$P->title = "이벤트당첨자리스트";
$P->strContents = $Contents;


echo $P->PrintLayOut();

?>
