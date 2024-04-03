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



$sql = "SELECT count(*) as total
FROM shop_idea 
WHERE DATE_FORMAT(regdate, '%Y%m%d') "; 

$db->query($sql);


$db->fetch();
$total = $db->dt[total];



$sql = "SELECT * from shop_idea where DATE_FORMAT(regdate, '%Y%m%d')  order by regdate desc limit $start , $max";

$db->query($sql);


$help_text = "	-  상품/아이디어제안 목록입니다. '자세히보기'를 클릭하시면 상세 내용을 보실 수 있습니다. <br>
		
		";


$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<col width=50>
	<col width=*>
	<col width=100>
	<col width=120>
	<col width=120>
	<col width=80>
	<tr>
	    <td align='left' colspan=9 style='padding-bottom:10px;'> ".GetTitleNavigation("상품/아이디어제안 관리", "마케팅지원 > 상품/아이디어제안 관리")."</td>
	</tr>
	
	<tr bgcolor=#ffffff align=center height=27>
		<td class='s_td'>번호</td>
		<td class='m_td'>제목</td>
		<td class='m_td'>고객명</td>
		<td class='m_td'>분류</td>
		<td class='m_td'>등록일</td>
		<td class='e_td'>자세히 보기</td>
		</tr>";

if($db->total){
	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;
		if($db->dt[idea_choice] == '1') {
			$idea_choice = '상품제안';
		}else $idea_choice = '아이디어 공모';
			
		$mstring .="<tr height=27 bgcolor=#ffffff align=center>
					<td bgcolor=#efefef>".$no."</td>
					<td align=left style='padding-left:10px;'>".$db->dt[subject]."</td>
					<td bgcolor=#efefef>".$db->dt[name]."</td>
					<td>".$idea_choice."</td>
					<td bgcolor=#efefef>".$db->dt[regdate]."</td>
					<td>
					<a href=\"javascript:PoPWindow('idea_detail.php?ix=".$db->dt[ix]."',800,550,'contact_info')\">자세히보기</a>
					</td>
				</tr>
				<tr height=1><td colspan=9 class='dot-x'></td></tr>";
	}
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	$mstring .="<tr height=40><td colspan=9 align=center>".page_bar($total, $page, $max,$query_string,"")."</td></tr>";
	
}else{
	$mstring .= "<tr height=50><td colspan=9 align=center style='padding-top:10px;'>상품제안 & 아이디어 공모가 없습니다.</td></tr>
				<tr height=1><td colspan=9 class='dot-x'></td></tr>";
}

//	$mstring .= "<tr height=40><td colspan=9 align=right style='padding-top:10px;'><a href='company.add.php'><img src='../image/b_companyadd.gif' border=0></a></td></tr>";
$mstring .= "<tr><td style='padding-bottom:10px;' colspan=7>".HelpBox("상품/아이디어제안 관리", $help_text)."</td></tr>";
$mstring .="</table>";
$Contents = $mstring;




$P = new LayOut;
$P->addScript = "";
$P->strLeftMenu = marketting_menu();
$P->OnloadFunction = "";
$P->Navigation = "HOME > 마케팅지원 > 상품제안&아이디어 공모";
$P->strContents = $Contents;
$P->PrintLayOut();




?>