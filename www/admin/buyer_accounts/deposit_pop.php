<?
include("../class/layout.class");


$db = new Database;


$Script = "
<script language='JavaScript' >
function DepositReset(){
	var frm = document.forms['deposit'];

	frm.reset();
	frm.act.value = 'reserve_insert';
}


function CheckDeposit(frm){
	if(frm.etc.value.length < 1){
		alert('적립내용을 입력해주세요');
		//frm.etc.focus();
		return false;
	}

	if(frm.deposit.value.length < 1){
		alert('마일리지를 입력해주세요');
		//frm.deposit.focus();
		return false;
	}

	return true;
}

</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>

			<tr height=30><td class='p11 ls1' style='padding:0 0 0 20px;text-align:left;' ></td></tr>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
				<form name='deposit' method='post'  action='deposit_charge.act.php'  onSubmit='return CheckDeposit(this)' target='act'>
				<input type='hidden' name='act' value='deposit_insert'>
				<input type='hidden' name='uid' value='".$code."'>

						<table border='0' width='100%' cellspacing='1' cellpadding='0' class='search_table_box' style='margin-bottom:10px;'>
							<tr height='20' valign='middle' align=center >
								<td align='center' class='search_box_title' style='text-align:center;padding:0px;'><b>회원</b></td>
								<td class='search_box_title' style='text-align:center;padding:0px;'>지급상태</td>
								<td class='search_box_title' style='text-align:center;padding:0px;'>메모</td>
								<td class='search_box_title' style='text-align:center;padding:0px;'>예치금</td>
								<td class='search_box_title' style='text-align:center;padding:0px;'>관리</td>
							</tr>
							<tr height='30' align='center'>
								<td >".get_member_name($code)."</td>
								<td>
									<select name='use_type' class='p11 ls1' id='use_type'>
										<option value='P' ".CompareReturnValue("P",$db->dt[use_type],"selected").">입금</option>
										<option value='W' ".CompareReturnValue("W",$db->dt[use_type],"selected").">출금</option>
									</select>
								</td>
								<td align='center'  ><input type='text' class=textbox name='etc' value='' size=35></td>
								<td><input type='text' class=textbox name='deposit' size=10></td>
								
								<td>
									<input type='image' src='../images/btn/ok.gif' align=absmiddle> 
									<a href='javascript:DepositReset();'><img src='../images/btn/cancel.gif' align=absmiddle border=0></a>
								</td>
							</tr>
						</table>

				</form>
				</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
					<table cellspacing=0 cellpadding=0 width=100% class='list_table_box'>
						<tr bgcolor=#efefef align=center height=28>
							<td class='s_td' width=15%>일자 </td>
							<td class='m_td' width=10%>상태 </td>
							<td class='m_td' width=20% >메모 </td>
							<td class='m_td' width=10% >예치금 </td>
							<td class='e_td' width=15% >담당자 </td>
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

$sql = "select 
			di.de_ix 
		from 
			shop_deposit_info as di
		where 
			di.uid = '$code' 
		$where";
$db->query($sql);

$total = $db->total;


$sql = "select
			*
		from 
			shop_deposit_info as di
		where 
			di.uid = '$code' 
		$where
			order by di.de_ix desc LIMIT $start, $max";
$db->query($sql);


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		switch($db->dt[use_type]){
			case 'P':
				$mstate = '입금';
				break;
			case 'W':
				$mstate = '출금';
				break;
		}
	

		$Contents .= "<tr height=28 align=center>
								<td bgcolor='#fbfbfb'>".$db->dt[regdate]."</td>
								<td > ".$mstate." </td>
								<td bgcolor='#fbfbfb'>".$db->dt[etc]."</td>
								<td>".$db->dt[deposit]."</td>
								<td>".get_member_name($db->dt[uid])."(".get_member_id($db->dt[uid]).")</td>
							</tr>";
	}
		//echo $Contents;
}else{
		$Contents .= "<tr height=60><td colspan=7 align=center>예치금 내용이 없습니다.</td></tr>";

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
$P->Navigation = "예치금관리 > 예치금 내역보기";
$P->NaviTitle = "예치금 보유금액 : <span style='color:#ffffff;font-size:20px;'>".number_format(SearchDeposit($code))."</span> 원";
$P->title = "예치금 내역보기";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>

