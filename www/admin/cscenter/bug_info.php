<?
include("../class/layout.class");



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

$db = new Database;



//$sql = "SELECT count(*) as total FROM shop_bug WHERE DATE_FORMAT(regdate, '%Y%m%d') ";
$sql = "SELECT count(*) as total FROM shop_bug ";
$db->query($sql);


$db->fetch();
$total = $db->dt[total];



//$sql = "SELECT * from shop_bug where DATE_FORMAT(regdate, '%Y%m%d')  order by regdate desc limit $start , $max";
$sql = "SELECT * from shop_bug order by regdate desc limit $start, $max";

$db->query($sql);

/*
$help_text = "	-  버그신고 목록입니다. '자세히보기'를 클릭하시면 상세 내용을 보실 수 있습니다. <br>

		";*/
	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr>
	    <td align='left' style='padding-bottom:10px;'> ".GetTitleNavigation("버그신고", "고객센타 > 버그신고")."</td>
	</tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width=70>
	<col width=*>
	<col width=100>
	<col width=140>
	<col width=120>
	<tr bgcolor=#efefef align=center height=30 style='font-weight:600;'>
		<td class='s_td'>번호</td>
		<td class='m_td'>제목</td>
		<td class='m_td'>고객명</td>
		<td class='m_td'>등록일</td>
		<td class='e_td'>자세히 보기</td>
		</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;
		if($db->dt[file_name]) {
			$file_name = '다운로드';
		}else $file_name = '-';

		$mstring .="<tr height=30 bgcolor=#ffffff align=center>
					<td class='list_box_td' bgcolor=#efefef>".$no."</td>
					<td class='list_box_td point' style='text-align:left;padding:0 0 0 10px'>".$db->dt[subject]."</td>
					<td class='list_box_td' bgcolor=#efefef>".$db->dt[name]."</td>
					<td class='list_box_td'>".$db->dt[regdate]."</td>
					<td class='list_box_td' bgcolor=#efefef>
					<a href=\"javascript:PoPWindow('bug_detail.php?ix=".$db->dt[ix]."',800,550,'contact_info')\"><img src='../images/".$admininfo["language"]."/btn_detail_view.gif' align=absmiddle></a>
					</td>
				</tr>";
	}
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;


}else{
	$mstring .= "<tr height=50><td class='list_box_td' colspan=5 align=center style='padding-top:10px;'>버그신고가 없습니다.</td></tr>";
}

$mstring .="</table>";
$mstring .="<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >";
$mstring .="<tr height=40><td align=right>".page_bar($total, $page, $max,$query_string,"")."</td></tr>";
$mstring .= "<tr><td style='padding-bottom:10px;' >".HelpBox("버그신고 관리", $help_text)."</td></tr>";
$mstring .="</table>";
$Contents = $mstring;




$P = new LayOut;
$P->addScript = "";
$P->strLeftMenu = cscenter_menu();
$P->OnloadFunction = "";
$P->Navigation = "고객센타 > 버그신고";
$P->title = "버그신고";
$P->strContents = $Contents;
$P->PrintLayOut();




?>