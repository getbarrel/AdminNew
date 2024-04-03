<?
include("../class/layout.class");
$db = new Database;

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>

			<tr height=30><td class='p11 ls1' style='padding:0 0 0 20px;text-align:left;' > - 예치금 신청 현황을 확인 하실 수 있습니다.</td></tr>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
					<table cellspacing=0 cellpadding=0 width=100% class='list_table_box'>
						<tr bgcolor=#efefef align=center height=28>
							<td class='s_td' width=25%>일자 </td>
							<td class='m_td' width=25%>상태 </td>
							<td class='m_td' width=25% >메모 </td>
							<td class='m_td' width=25% >담당자 </td>
						</tr>";

$max = 10; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

$sql = "select * from shop_deposit_history where oid = '".$oid."'";
$db->query($sql);

if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);
//처리상태 (1:입금대기 2:입금취소 3:입금완료 4:사용완료 5:출금요청 6:출금취소 7:출금확정 8:송금완료)
		switch($db->dt[history_type]){
			case '1':
				$mstate = '입금대기';
				break;
			case '2':
				$mstate = '입금취소';
				break;
			case '3':
				$mstate = '입금완료';
				break;
			case '4':
				$mstate = '사용완료';
				break;
			case '5':
				$mstate = '출금요청';
				break;
			case '6':
				$mstate = '출금취소';
				break;
			case '7':
				$mstate = '출금확정';
				break;
			case '8':
				$mstate = '송금완료';
				break;
		}

		$Contents .= "<tr height=28 align=center>
								<td bgcolor='#fbfbfb'>".$db->dt[regdate]."</td>
								<td style='padding:5px 0 5px 15px;' align=left> ".$mstate." </td>
								<td bgcolor='#fbfbfb'>".$db->dt[etc]."</td>
								<td>".get_member_name($db->dt[uid])."(".get_member_id($db->dt[uid]).")</td>
							</tr>";
	}
		//echo $Contents;
}else{
		$Contents .= "<tr height=60><td colspan=7 align=center>적립금 내용이 없습니다.</td></tr>";

}
$Contents .= "
			<tr height=40><td colspan=7 align=center>".page_bar($total, $page, $max,"&code=$code","")."</td></tr>
			</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>

</TABLE>";

//208ab634bd0cd3e4f7d87f5b44aa3bdc
$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "예치금 신청관리 > 예치금 신청현황";
$P->NaviTitle = "예치금 신청 현황";
$P->title = "예치금 신청현황";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>