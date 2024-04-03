<?
include("../class/layout.class");

$db = new Database;


$db->query("select com_name from ".TBL_COMMON_COMPANY_DETAIL." where company_id = '$company_id' ");

if($db->total){
	$db->fetch();
	$user_name = $db->dt[com_name];
}else{
	$user_name = "미지정";
}





$Script ="
<script language='JavaScript' >
function CashReset(){
	var frm = document.forms['cash'];

	frm.reset();
	frm.act.value = 'cash_insert';
}

function DeleteCash(c_ix, company_id){
	if(confirm(language_data['cash.php']['A'][language])){//적립금 정보를 정말로 삭제하시겠습니까?
		window.frames['iframe_act'].location.href='cash.act.php?act=cash_delete&c_ix='+c_ix+'&company_id='+company_id;
	}
}

function UpdateCash(c_ix, etc, cash, status){
	var frm = document.forms['cash'];

	frm.c_ix.value = c_ix;
	frm.etc.value = etc;
	frm.cash.value = cash;

	//frm.status[frm.status.selectedIndex].selected = true;
	for(i=0;i<frm.status.length;i++){
		if(frm.status[i].value == status){
			frm.status[i].selected = true;
		}
	}
	frm.act.value = 'cash_update';
}

function CheckCash(frm){

	if(frm.company_id.selectedIndex == 0){
		alert(language_data['cash.php']['B'][language]);
		//frm.etc.focus();
		return false;
	}

	if(frm.etc.value.length < 1){
		alert(language_data['cash.php']['C'][language]);	//내용을 입력해주세요
		//frm.etc.focus();
		return false;
	}

	if(frm.cash.value.length < 1){
		alert(language_data['cash.php']['D'][language]);	//캐쉬를 입력해주세요
		//frm.cash.focus();
		return false;
	}

	return true;
}
</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr >
		<td align='left' colspan=2> ".GetTitleNavigation("캐쉬 관리", "주문관리 > 캐쉬 관리 ")."</td>
	</tr>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr>
				<td align=center style='padding: 10px 0 10px 0'>
				<form name='cash' method='post'  action='cash.act.php'  onSubmit='return CheckCash(this)' target='iframe_act'>
				<input type='hidden' name='act' value='cash_insert'>
				<input type='hidden' name='c_ix' value=''>
					<table class='box_shadow' style='width:100%;' cellpadding=0 cellspacing=0>
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05' align=right style='padding:0px'>
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='search_table_box'>
									<tr height='28' valign='middle' align=center bgcolor='#efefef'>
										<td align='center' class='s_td'><b>업체</b></td>
										<td class='m_td'>내용</td>
										<td class='m_td'>마일리지</td>
										<td class='m_td'>상태</td>
										<td class='e_td'>관리</td>
									</tr>
									<tr height='30' align='center'>
										<td class='list_bg_gray'>".CompanyList($company_id,"","")."</td>
										<td align='center'  ><input type='text' class='textbox'  name='etc' value='' style='width:450px;' size=55></td>
										<td class='list_bg_gray'><input type='text' class='textbox' name='cash' size=10></td>
										<td>
											<select name='status' class='p11 ls1' >
												<option value=0>판매대금적립</option>
												<option value=1>캐쉬출금</option>
											</select>
										</td>
										<td class='list_bg_gray'>";
                                        if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                                            $Contents.="
                                            <input type='image' src='../images/".$admininfo["language"]."/btn_s_ok.gif' align=absmiddle> 
                                            <a href='javascript:CashReset();'><img src='../images/".$admininfo["language"]."/btn_s_cancle.gif' align=absmiddle border=0></a></td>";
                                        }else{
                                            $Contents.="
                                            <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_s_ok.gif' align=absmiddle></a>
                                            <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_s_cancle.gif' align=absmiddle border=0></a></td>";
                                        }
                                        $Contents.=" 
									</tr>
								</table>
							</td>
							<th class='box_06'></th>
						</tr>
						<tr>
							<th class='box_07'></th>
							<td class='box_08'></td>
							<th class='box_09'></th>
						</tr>
						</table>
				</form>
				</td>
			</tr>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
					<table cellspacing=0 cellpadding=0 width=100% class='list_table_box'>
						<col width=15%>
						<col width=15% >
						<col width=*>
						<col width=10% >
						<col width=10% >
						<col width=15% >
						<col width=10% >
						<tr bgcolor=#efefef align=center height=28>
							<td class='s_td'>날짜 </td>
							<td class='m_td'>업체명 </td>
							<td class='m_td'>내용 </td>
							<td class='m_td'>캐쉬 </td>
							<td class='m_td'>상태 </td>
							<td class='m_td'>정산코드 </td>
							<td class='e_td' >관리 </td>
						</tr>";


$max = 15; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

if($company_id){
	$sql = "select c_ix from ".TBL_SHOP_CASH_INFO." where company_id = '$company_id' ";
}else{
	$sql = "select c_ix from ".TBL_SHOP_CASH_INFO."  ";
}
$db->query($sql);
$total = $db->total;

if($company_id){
	$db->query("select c.*, ci.com_name, DATE_FORMAT(c.regdate, '%Y.%m.%d %H:%i:%s') as disp_regdate from ".TBL_SHOP_CASH_INFO." c , ".TBL_COMMON_COMPANY_DETAIL." ci where c.company_id = ci.company_id and company_id = '$company_id' order by c.regdate desc LIMIT $start, $max");
}else{
	$db->query("select c.*, ci.com_name, DATE_FORMAT(c.regdate, '%Y.%m.%d %H:%i:%s') as disp_regdate from ".TBL_SHOP_CASH_INFO." c, ".TBL_COMMON_COMPANY_DETAIL." ci where c.company_id = ci.company_id order by c.regdate desc LIMIT $start, $max");
}


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		if($db->dt[status]==0){
			$mstatus = "판매대금적립";
		}else if($db->dt[status]==1){
			$mstatus = "캐쉬출금";
		}

		$Contents .= "<tr height=28 align=center>
				<td class='list_box_td list_bg_gray' >".$db->dt[disp_regdate]."</td>
				<td class='list_box_td' >".$db->dt[com_name]."</td>
				<td class='list_box_td list_bg_gray' ><!--a href=\"javascript:UpdateCash('".$db->dt[c_ix]."','".$db->dt[etc]."', '".$db->dt[cash]."','".$db->dt[status]."')\"-->".$db->dt[etc]."<!--/a--></td>
				<td class='list_box_td point' >".number_format($db->dt[cash])."</td>
				<td class='list_box_td list_bg_gray' >".$mstatus."</td>
				<td class='list_box_td' >".($db->dt[ac_ix] ? "ACC_".$db->dt[ac_ix]:"-")."</td>";
                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                    $Contents.="
    				<td class='list_box_td list_bg_gray' ><a href=\"javascript:DeleteCash('".$db->dt[c_ix]."', '".$db->dt[company_id]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>";
                }else{
                    $Contents.="
    				<td><a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a></td>";
                }
            $Contents.="
			</tr>";
	}
}else{
$Contents .= "
			<tr height=60><td colspan=7 align=center>캐쉬 관리 목록이 없습니다.</td></tr>
			";

}
$Contents .= "</table>
				<table cellspacing=0 cellpadding=0 width=100%'>
";
$Contents .= "
						<tr height=40><td colspan=7 align=center style='border-spacing:0px;'>".page_bar($total, $page, $max,"&company_id=$company_id","")."</td></tr>
					</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>

</TABLE>";


/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >캐쉬 내용을 수정/ 추가 하실수 있습니다. </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >시스템에서 정산된 목록이 입력됩니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >입점업체에게 판매대금을 입금한후 출금 처리를 하셔야 합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >내역을 삭제하시면 총 cash 금액에 변화가 되므로 삭제시 주의하셔야 합니다</td></tr>
</table>
";
*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("캐쉬 관리", $help_text);




$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = order_menu();
$P->Navigation = "주문관리 > 캐쉬 관리";
$P->title = "캐쉬 관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();
?>

