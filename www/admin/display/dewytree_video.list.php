<?
include("../class/layout.class");

$db = new Database;

$sql = "select count(*) as total from dewytree_video";
$db->query($sql);
$db->fetch();
$total = $db->dt['total'];

$max = 15;

$start = 0;
//$page = 1;

if ($page == '') {
    $start = 0;
    $page = 1;
} else {
    $start = ($page - 1) * $max;
}

$Contents = "
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center style=''>
		<tr>
			<td align='left' > " . GetTitleNavigation("듀이트리 영상 목록", "프로모션/전시 > 프로모션 전시관리 > 듀이트리 영상 목록") . "</td>
		</tr>
		<tr>
			<td align='left' colspan=4 style='padding:3px 0px;'> " . colorCirCleBox("#efefef", "100%", "<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b>듀이트리 영상 목록</b></div>") . "</td>
		 </tr>
		 <tr>
			<td>
			<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
				<col style='width:8%;'>
				<col style='width:25%;'>
				<col style='width:*;'>
				<col style='width:15%;'>
				<col style='width:17%;'>
				<col style='width:15%;'>
			  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
				<td class='s_td'> 번호</td>
				<td class='m_td'> 프로모션명</td>
				<td class='m_td'> 동영상 URL</td>
				<td class='m_td'> 노출여부</td>
				<td class='m_td'> 등록일자</td>
				<td class='e_td'> 관리</td>
			  </tr>";

if ($total == 0) {
    $Contents .= "<tr bgcolor=#ffffff><td height=35 colspan=6 align=center>내역이 존재 하지 않습니다.</td></tr>";
} else {

    $sql = "SELECT * FROM dewytree_video  ORDER BY regdate DESC limit ".$start.", ".$max."";
    $db->query($sql);

    for ($i = 0; $i < $db->total; $i++) {
        $db->fetch($i);

        $no = $total - ($page - 1) * $max - $i;

        if ($db->dt['disp'] == '1') {
            $dispStr = "노출";
        } else {
            $dispStr = "비노출";
        }

        $Contents .= "
						  <tr height=35 align=center >
							<td class='list_box_td list_bg_gray'>" . $no . "</td>
							<td class='list_box_td'>" . $db->dt['title'] . "</td>
							<td class='list_box_td list_bg_gray'>" . $db->dt['video_url'] . "</td>
							<td class='list_box_td'>" . $dispStr . "</td>
							<td class='list_box_td list_bg_gray'>" . $db->dt['regdate'] . "</td>
							<td class='list_box_td'>";
        if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "U")) {
            $Contents .= "<a href=' ./dewytree_video.write.php?dv_ix=" . $db->dt['dv_ix'] . "' ><img  src = '../images/" . $admininfo["language"] . "/btc_modify.gif' border = 0 ></a>";
        } else {
            $Contents .= "<a href=\"" . $auth_update_msg . "\" ><img src = '../images/" . $admininfo["language"] . "/btc_modify.gif' border=0 ></a> ";
        }

        if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "D")) {
            $Contents .= "<a href='JavaScript:dewytreeVideoDelete(" . $db->dt['dv_ix'] . ")'><img  src='../images/" . $admininfo["language"] . "/btc_del.gif' border=0></a>";
        } else {
            $Contents .= "<a href=\"" . $auth_delete_msg . "\" ><img src = '../images/" . $admininfo["language"] . "/btc_del.gif' border=0></a> ";
        }

        $Contents .= "
							</td>
						  </tr>";
    }
}
$Contents .= "</table>";
$Contents .= "<table cellpadding=0 cellspacing=0 border=0 width=100% >
						<tr height=50 bgcolor=#ffffff>
							<td colspan=3 align=left>" . page_bar($total, $page, $max, "&max=$max", "") . "</td>
							<td colspan=3 align=right>";
if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "C")) {
    $Contents .= "<a href='dewytree_video.write.php'><img src='../images/" . $admininfo["language"] . "/b_promotionadd.gif' border=0></a>";
} else {
    $Contents .= "<a href=\"" . $auth_write_msg . "\" ><img src = '../images/" . $admininfo["language"] . "/b_promotionadd.gif' border=0></a> ";
}
$Contents .= "
							</td>
						</tr>";
$Contents .= "</table>
			</td>
		</tr>
	</table>";

$Script = "<script language='javascript'>
function dewytreeVideoDelete(dv_ix){
	if(confirm('영상을 삭제하시겠습니까?'))
	{
		window.frames['act'].location.href= 'dewytree_video.act.php?act=delete&dv_ix='+dv_ix;
	}
}
</script>";

$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션/전시 > 프로모션 전시관리 > 듀이트리 영상 목록";
$P->title = "듀이트리 영상 목록";
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
