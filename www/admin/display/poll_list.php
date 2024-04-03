<?
include("../class/layout.class");


/*
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-30, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");

	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;ss
}
*/

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



$sql = "SELECT count(*) as total FROM shop_poll_group
				WHERE DATE_FORMAT(regdate, '%Y%m%d') ";

$db->query($sql);


$db->fetch();
$total = $db->dt[total];



$sql = "SELECT a.pg_ix,a.g_title,a.regdate, sum(c.result) as result
		from shop_poll_group a left join shop_poll_title b on a.pg_ix = b.pg_ix left join shop_poll_field c on b.pt_ix = c.pt_ix
		group by a.pg_ix order by regdate desc limit $start , $max";

//$sql = "SELECT id,g_title,regdate from shop_poll_group order by regdate desc limit $start , $max";

$db->query($sql);



$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr>
	    <td align='left'> ".GetTitleNavigation("설문관리", "마케팅지원 > 설문관리")."</td>
	</tr>
	<tr>
	    <td align='left' colspan=5 style='padding-bottom:20px;'>
	        <div class='tab'>
	            <table class='s_org_tab'>
	                <tr>
	                    <td class='tab'>
	                        <table id='tab_01' class=on >
	                            <tr>
	                                <th class='box_01'></th>
	                                <td class='box_02' onclick=\"document.location.href='poll_group.php'\">설문 리스트</td>
	                                <th class='box_03'></th>
	                            </tr>
	                        </table>
	                        <table id='tab_02' >
	                            <tr>
	                                <th class='box_01'></th>
	                                <td class='box_02' onclick=\"document.location.href='poll_group.php'\">설문 그룹 만들기</td>
	                                <th class='box_03'></th>
	                            </tr>
	                        </table>
													<table id='tab_03' >
	                            <tr>
	                                <th class='box_01'></th>
	                                <td class='box_02' onclick=\"document.location.href='poll_result.php'\">설문 결과보기</td>
	                                <th class='box_03'></th>
	                            </tr>
	                        </table>
	                    </td>
	                    <td class='btn'>

	                    </td>
	                </tr>
	            </table>
	        </div>
	    </td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box' align='left'>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td' width=5%>번호</td>
		<td class='m_td' width=*>설문</td>
		<td class='m_td' width=10%>참여자</td>
		<td class='m_td' width=15%>등록날자</td>
		<td class='e_td' width=25%>수정/삭제</td>
	</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

		$pg_ix = $db->dt[pg_ix];

		$result = $db->dt[result];

		if($result == ""){
			$result = 0;
		}

		$regdate = $db->dt[regdate];



		$no = $total - ($page - 1) * $max - $i;

		$mstring .="<tr height=27 align=center>
					<td bgcolor=#efefef>".$no."</td>
					<td align='left' style='padding-left:10px;'>".$db->dt[g_title]."</td>
					<td bgcolor=#efefef>".$result."</td>
					<td >".$regdate."</td>
					<td bgcolor=#efefef>
					<a href='poll_group.php?pg_ix=$pg_ix'><img src='../image/btc_modify.gif' border=0></a> <a href='poll.php?pg_ix=$pg_ix'><img src='../image/btc_question_add.gif' border=0></a> <a href=\"javascript:PopSWindow('poll_result.php?mmode=pop&pg_ix=".$pg_ix."',900,600,'cupon_detail_pop');\"  class=blue><img src='../image/btc_result_view.gif' border=0></a> <a href='poll.act.php?act=g_delete&pg_ix=$pg_ix'><img src='../image/btc_del.gif' border=0></a>
					</td>
				</tr>";
	}
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	$mstring .="</table><table width='100%' cellpadding=0 cellspacing=0 border='0'><tr height=40><td colspan=5 align=center>".page_bar($total, $page, $max,$query_string,"")."</td></tr>";

}else{
	$mstring .= "<tr height=50><td colspan=5 align=center style='padding-top:10px;'>작성된 설문이 없습니다.</td></tr>
				<tr height=1><td colspan=5 class='dot-x'></td></tr>";
}
	$mstring .= "<tr height='50'><td colspan='5' align='right'><a href='poll_group.php'><img src='../image/b_poll.gif' border='0'></a></td></tr>";


$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >설문을 추가하신후 질문항목을 작성하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기 정의된 목록에서 수정하시고자 하는 항목의 수정버튼을 클릭합니다</td></tr>

</table>
";


//$help_text = HelpBox("게시판 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:4px;'><table><tr><td valign=bottom><b>설문 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text,200)."</div>";

	$mstring .= "<tr height='50'><td colspan='5' align='right'>$help_text</td></tr>";

$mstring .="</table>";



$Contents = $mstring;




$P = new LayOut;
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
$P->strLeftMenu = display_menu();
$P->OnloadFunction = "onLoad('$sDate', '$eDate');";
$P->Navigation = "마케팅지원 > 설문관리";
$P->title= " 설문관리";
$P->strContents = $Contents;
$P->PrintLayOut();




?>