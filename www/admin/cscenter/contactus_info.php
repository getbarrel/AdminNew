<?
include("../class/layout.class");

if(empty($info_type)) {
	$info_type="C";
}

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

$where=" WHERE rtype='".$info_type."'";

//$sql = "SELECT count(*) as total FROM shop_cooperation WHERE DATE_FORMAT(regdate, '%Y%m%d') ";
$sql = "SELECT count(*) as total FROM shop_cooperation $where ";
$db->query($sql);


$db->fetch();
$total = $db->dt[total];



//$sql = "SELECT * from shop_cooperation where DATE_FORMAT(regdate, '%Y%m%d')  order by regdate desc limit $start , $max";
$sql = "SELECT * from shop_cooperation $where order by regdate desc limit $start, $max";
$db->query($sql);


/*
$help_text = "	-  제휴신청 목록입니다. '자세히보기'를 클릭하시면 상세 내용을 보실 수 있습니다. <br>

		";*/

		$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
	    <td align='left' style='padding-bottom:10px;'> ".GetTitleNavigation("제휴문의", "고객센타 > 제휴문의")."</td>
	</tr>
</table>";
$mstring .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_02' ".($info_type == "C" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$mstring .= "<a href='contactus_info.php?info_type=C'>제휴문의</a>";

						$mstring .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($info_type == "M" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$mstring .= "<a href='contactus_info.php?info_type=M'>입점문의</a>";

						$mstring .= "

						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	  </tr>
	  </table>
";
$mstring .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width='40'>
	<col width='*'>
	<col width='150'>
	<col width='150'>
	<col width='100'>
	<col width='130'>
	<col width='90'>
	<col width='140'>
	<col width='90'>
	<tr bgcolor=#efefef align=center height=30 style='font-weight:600;'>
		<td class='s_td'>번호</td>
		<td class='m_td'>제목</td>
		<td class='m_td'>회사명/고객명</td>
		<td class='m_td'>이메일/연락처</td>
		<td class='m_td'>첨부파일</td>
		<td class='m_td'>담당자</td>
		<td class='m_td'>처리상태</td>
		<td class='m_td'>등록일/처리일</td>
		<td class='e_td'>관리</td>
	</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;
		if($db->dt[file_name]) {
			$file_name = '다운로드';
		}else $file_name = '-';
		
		switch($db->dt[status]){
			case 'W':
				$status = '<font color="red">접수중</forn>';
			break;
			case'I':
				$status = '<font color="green">처리중</forn>';
			break;
			case'C':
				$status = '처리완료';
			break;

		}
		$mstring .="<tr height=30 align=center>
					<td class='list_box_td' bgcolor='#efefef'>".$no."</td>
					<td class='list_box_td point' style='text-align:left;padding:  0 0 0 10px;'><a href=\"javascript:PoPWindow('contact_detail.php?ix=".$db->dt[ix]."',800,550,'contact_info')\">".$db->dt[subject]."</td></a>
					<td class='list_box_td' bgcolor='#efefef' >".$db->dt[com_name]."<br>".$db->dt[name]."</td>
					<td class='list_box_td'>".($db->dt[email]?$db->dt[email]:'-')."<br>".($db->dt[tel]?$db->dt[tel]:'-')."</td>
					<td class='list_box_td' bgcolor='#efefef'> <a href='download.php?ix=".$db->dt[ix]."&file_name=".urlencode($db->dt[file_name])."'>".$file_name."</a></td>
					<td class='list_box_td' bgcolor='#efefef' >".($db->dt[md_name]?$db->dt[md_name]:'-')."</td>
					<td class='list_box_td'>".$status."</td>
					<td class='list_box_td'>".$db->dt[regdate]."<br>".($db->dt[complete_date]=='0000-00-00 00:00:00'?'-':$db->dt[complete_date])."</td>
					<td class='list_box_td' bgcolor='#efefef'>
					<a href=\"javascript:PoPWindow('contact_detail.php?ix=".$db->dt[ix]."',800,850,'contact_info')\"><img src='../images/".$admininfo["language"]."/btn_detail_view.gif' align=absmiddle></a>
					</td>
				</tr>";
	}
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;


}else{
	$mstring .= "<tr height=50><td class='list_box_td' colspan=9 align=center style='padding-top:10px;'>".($info_type == "C" ? "제휴문의":"입점문의")."가 없습니다.</td></tr>
				";
}
$mstring .="</table>";
$mstring .= "<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >";
$mstring .="<tr height=40><td  align=right>".page_bar($total, $page, $max,$query_string,"")."</td></tr>";
$mstring .= "<tr><td style='padding-bottom:10px;' colspan=9>".HelpBox("제휴문의 관리", $help_text)."</td></tr>";
$mstring .="</table><br>";

$Contents = $mstring;




$P = new LayOut;
$P->addScript = "";
$P->strLeftMenu = cscenter_menu();
$P->OnloadFunction = "";
$P->Navigation = "고객센타 > 제휴문의";
$P->title = "제휴문의";
$P->strContents = $Contents;
$P->PrintLayOut();




?>