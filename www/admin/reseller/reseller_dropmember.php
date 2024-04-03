<?
include("../class/layout.class");


$db = new Database;

$Script ="
<script language='JavaScript' >

function DeleteDropmember(rsl_code){
	if(confirm('정말로 삭제하시겠습니까?')){
		window.frames['iframe_act'].location.href='reseller_dropmember.act.php?act=dropmember_delete&rsl_code='+rsl_code;
		//document.getElementById('iframe_act').src='reseller_dropmember.act.php?act=dropmember_delete&rsl_code='+rsl_code;//kbk
	}
}

</Script>";

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("리셀러탈퇴회원관리", "리셀러관리 > 회원관리 > 리셀러탈퇴회원관리 ")."</td>
	  </tr>";

$Contents01 .= "
	  </table>";


$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
		<td colspan=7 style='padding-bottom:10px;'>

			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top >
					<form name='frm' method='post'>
					<table width='100%' border='0' cellspacing='0' cellpadding='0' class='search_table_box'>
					<tr height=30>
						<td width='15%' class='search_box_title' >이름</td>
						<td class='search_box_item'>
							<input type='text' class='textbox' name='search_name' value='$search_name'>
							<input type='image' src='../images/".$admininfo["language"]."/btn_search.gif' align='absmiddle'>
						</td>
					</tr>
					</form>
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

		</td>
	  </tr>
	</table>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	  <tr bgcolor=#efefef align=center height=28>
			<td class='s_td' width=5%><font color='#000000'><b>번호</b></font></td>
			<td class='m_td' width=10%><font color='#000000'><b>이름</b></font></td>
			<td class='m_td' width=10%><font color='#000000'><b>ID</b></font></td>
			<td class='m_td' width=15%><font color='#000000'><b>이메일</b></font></td>
			<td class='m_td' width=*><font color='#000000'><b>남긴말</b></font></td>
			<td class='m_td' width=10% ><font color='#000000'><b>탈퇴일</b></font></td>
			<td class='e_td' width=15% ><font color='#000000'><b>관리</b></font></td>
		</tr>";


$max = 20; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}
$where=" ";
if($search_name!="") {
	$where.=" where name like '%$search_name%' ";
}

//탈퇴시 member 테이블에서 회원데이터 삭제를 위해 아래쿼리 수정
$db->query("select count(*) as total from reseller_dropmember $where");
$db->fetch();
$total = $db->dt[total];

//탈퇴시 member테이블에서는 회원데이터 삭제함으로 ... 아래쿼리 수정
$db->query("select * from reseller_dropmember $where order by dropdate desc limit $start, $max");

if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;

		$Contents02 .= "<tr height=30 align=center>
				<td class='list_box_td' bgcolor='#fbfbfb'>".$no."</td>
				<td class='list_box_td point' >".$db->dt[name]."</td>
				<td class='list_box_td' >".$db->dt[id]."</td>
				<td class='list_box_td' >".$db->dt[email]."</td>
				<td class='list_box_td list_bg_gray' style='padding:10px;text-align:left;' >".cut_str($db->dt[message],60)."</td>
				<td class='list_box_td'>".$db->dt[dropdate]."</td>
				<td class='list_box_td' >
					<a href=\"javascript:DeleteDropmember('".$db->dt[rsl_code]."')\">
						<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0>
					</a>
					<a href=\"javascript:PopSWindow('reseller_dropmember_view.php?rsl_code=".$db->dt[rsl_code]."',500,300,'member_info')\">
						<img src='../images/".$admininfo["language"]."/btn_dropmember.gif' border=0>
					</a>
				</td>
			</tr>";

	}
}else{
		$Contents02 .= "
			<tr height=60><td colspan=7 align=center>탈퇴 내역이 없습니다.</td></tr>
			";

}
$Contents02 .= "</table>";
$Contents02 .= "<table width='100%' border='0' cellpadding='0' cellspacing='0'>";
$Contents02 .= "<tr height=40><td colspan=7 align=center>".page_bar($total, $page, $max,"&code=$code","")."</td></tr>";
$Contents02 .= "</table>";



$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>$Contents01<br></td></tr>";

$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >탈퇴한 고객들이 작성한 내역입니다. </td></tr>
</table>
";
*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("리셀러탈퇴회원관리", $help_text);




$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = reseller_menu();
$P->Navigation = "리셀러관리 > 회원관리 > 리셀러탈퇴회원관리";
$P->title = "리셀러탈퇴회원관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();




?>