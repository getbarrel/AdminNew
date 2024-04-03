<?
include("../class/layout.class");


$db = new Database;

$where = "";
if($_SESSION['admininfo']['admin_level'] < 9){
    $where .= " and company_id ='".$_SESSION['admininfo']['company_id']."' ";
}


if($page_type=="add"){

	$db->query("select ac.*,ccd.com_name from shop_accounts ac left join common_company_detail ccd on (ccd.company_id=ac.company_id) where ac.ac_ix='".$ac_ix."' ".str_replace("company_id","ac.company_id",$where)." ");
	$db->fetch();
	$ac_info=$db->dt;

	if($ac_info[surtax_yorn]=='N'){
		$surtax_yorn="과세";
	}elseif($ac_info[surtax_yorn]=='Y'){
		$surtax_yorn="면세";
	}elseif($ac_info[surtax_yorn]=='P'){
		$surtax_yorn="영세";
	}else{
		$surtax_yorn="-";
	}
}

$Script = "
<script language='JavaScript' >

</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>";

		if($page_type=="add"){
			$Contents .= "
			<tr>
				<td align=center style='padding: 0 10 0 10'>
				<form name='deposit_frm' method='post'  action='accounts.act.php'  onSubmit='return CheckFormValue(this)' target='act'>
				<input type='hidden' name='act' value='add_price_insert'>
				<input type='hidden' name='ac_ix' value='".$ac_info[ac_ix]."'>
				<input type='hidden' name='company_id' value='".$ac_info[company_id]."'>

						<table border='0' width='100%' cellspacing='1' cellpadding='0'  class='list_table_box' style='margin-bottom:10px;'>
							<col width=15%>
							<col width=10%>
							<col width=10%>
							<col width=10%>
							<col width=13%>
							<col width=*>
							<col width=13%>
							<tr height='20' valign='middle' align=center>
								<td class='s_td'><b>정산번호/업체명</b></td>
								<td class='s_td'><b>과세여부</b></td>
								<td class='m_td'>정산타입</td>
								<td class='m_td'>처리상태</td>
								<td class='m_td'>금액</td>
								<td class='m_td'>내용</td>
								<td class='e_td'>관리</td>
							</tr>
							<tr height='30' align='center'>
								<td class='list_box_td'><b class='blue'>".$ac_info[ac_ix]."</b> / ".$ac_info[com_name]."</td>
								<td class='list_box_td'>".$surtax_yorn."</td>
								<td class='list_box_td'>
									<select name='app_type' id='app_type'>
										<option value='P' selected>상품</option>
										<option value='D'>배송비</option>
										<option value='C'>수수료</option>
									</select>
								</td>
								<td class='list_box_td' >
									<select name='app_state' id='app_state'>
										<option value='1' selected>증감</option>
										<option value='2'>차감</option>
									</select>
								</td>
								<td class='list_box_td' >
									<input type='text' class='textbox numeric' name='app_price' size=10 validation='true' title='금액'>
								</td>
								<td class='list_box_td' >
									<input type='text' class=textbox name='app_msg' value='' style='width:80%;' validation='true' title='내용'>
								</td>
								<td class='list_box_td' >
									<input type='image' src='../images/btn/ok.gif' align=absmiddle>
									<a href='javascript:ReserveReset();'><img src='../images/btn/cancel.gif' align=absmiddle border=0></a>
								</td>
							</tr>
						</table>
				</form>
				</td>
			</tr>";
		}

			$Contents .= "
			<tr>
				<td align=center style='padding: 0 10 0 10'>
					<table cellspacing=0 cellpadding=0 width=100% class='list_table_box'>
						<col width='15%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='*'>
						<tr bgcolor=#efefef align=center height=28>
							<td class='s_td'>처리일자</td>
							<td class='m_td'>정산타입</td>
							<td class='m_td'>처리상태 </td>
							<td class='m_td'>금액</td>
							<td class='m_td'>내용 </td>
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

if(!empty($ar_ix)){

	$db->query("select * from shop_accounts_add_price where ac_ix in ( select ac_ix from shop_accounts where ar_ix ='".$ar_ix."' )  ".$where." ");

	$total = $db->total;

	$sql = "select
				*
			from 
				shop_accounts_add_price
			where
				ac_ix in ( select ac_ix from shop_accounts where ar_ix ='".$ar_ix."' )
				".$where."
			order by 
				regdate DESC
			LIMIT $start, $max";
	$db->query($sql);
	$data_array = $db->fetchall("object");

}else{
	
	$db->query("select * from shop_accounts_add_price where ac_ix = '$ac_ix' ".$where." ");

	$total = $db->total;

	$sql = "select
				*
			from 
				shop_accounts_add_price
			where
				ac_ix = '$ac_ix'
				 ".$where."
			order by 
				regdate DESC
			LIMIT $start, $max";
	$db->query($sql);
	$data_array = $db->fetchall("object");

}

if(count($data_array) > 0){
	for($i=0; $i< count($data_array); $i++){

		switch($data_array[$i][app_type]){
			case 'P':
				$app_type = '상품';
				break;
			case 'D':
				$app_type = '배송비';
				break;
			case 'C':
				$app_type = '수수료';
				break;
		}

		switch($data_array[$i][app_state]){
			case '1':
				$app_state = "<span class='blue'>증감</span>";
				break;
			case '2':
				$app_state = "<span class='red'>차감</span>";
				break;
		}

					$Contents .= "
					<tr height=28 align=center>
						<td bgcolor='#fff'>".$data_array[$i][regdate]."</td>
						<td bgcolor='#fbfbfb'>".$app_type."</td>
						<td bgcolor='#fff'>".$app_state."</td>
						<td bgcolor='#fbfbfb'>".$data_array[$i][app_price]."</td>
						<td style='padding:5px 0 5px 15px' align=left>".$data_array[$i][app_msg]."</td>
					</tr>";
	}

}else{
		$Contents .= "<tr height=60><td colspan=5 align=center>추가정산 내용이 없습니다.</td></tr>";

}
$Contents .= "
			</table>
			<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
			<tr>
				<td align='right' style='padding:10px 0' >
					".page_bar($total, $page, $max,"&ac_ix=$ac_ix&ar_ix=$ar_ix&page_type=$page_type","")."
				</td>
			</tr>
			<table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</TABLE>";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "판매자정산관리 > 추가정산관리";
$P->NaviTitle = "추가정산관리";
$P->title = "추가정산관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>

