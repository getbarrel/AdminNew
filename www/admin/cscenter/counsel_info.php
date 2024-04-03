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
$db2 = new Database;



$sql = "SELECT count(*) as total FROM bbs_qna WHERE DATE_FORMAT(regdate, '%Y%m%d') "; 

$db->query($sql);


$db->fetch();

$total = $db->dt[total];



$sql = "SELECT * from bbs_qna where DATE_FORMAT(regdate, '%Y%m%d')  order by regdate desc limit $start , $max";

$db->query($sql);





$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<col width = '5%' >
	<col width = '*' >
	<col width = '10%' >
	<col width = '10%' >
	<col width = '10%' >
	<col width = '10%' >
	<tr>
	    <td align='left' colspan=9 style='padding-bottom:10px;'> ".GetTitleNavigation("1:1맞춤상담내역 관리", "고객센타 > 1:1맞춤상담내역 관리")."</td>
	</tr>
	
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td' >번호</td>
		<td class='m_td' >제목</td>
		<td class='m_td' >고객명</td>

		<td class='m_td'>분류</td>
		<!--td class='m_td' >첨부파일</td-->
		
		<td class='m_td'>등록일</td>
		<td class='e_td' >자세히 보기</td>
		</tr>";

if($db->total){
	for($i=0; $i < $db->total; $i++){
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;
		$db2->query("select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from  ".TBL_COMMON_MEMBER_DETAIL." cmd   where code='".$db->dt[mem_ix]."'");
		$db2->fetch();
		$name = $db2->dt[name];
		if($db->dt[bbs_etc1] == '1') {
			$type = '결제문의';
		}else if($db->dt[bbs_etc1] == '2') {
			$type = '교환문의';
		}else{
			$type = '기타문의';
		}
			
		$mstring .="<tr height=27 align=center>
					<td bgcolor='#efefef'>".$no."</td>
					<td align='left' style='padding: 0 0 0 10px;'><a href=\"javascript:PoPWindow('counsel_detail.php?bbs_ix=".$db->dt[bbs_ix]."',800,550,'contact_info')\">".$db->dt[bbs_subject]."</a></td>
					<td bgcolor='#efefef'>".$name."</td>
					<td>".$type."</td>
					<td bgcolor='#efefef'>".$db->dt[regdate]."</td>
					<td>
					<a href=\"javascript:PoPWindow('counsel_detail.php?bbs_ix=".$db->dt[bbs_ix]."',800,550,'contact_info')\"><img src='../images/".$admininfo["language"]."/btn_detail_view.gif' align=absmiddle></a>
					</td>
				</tr>
				<tr height=1 bgcolor='#efefef'><td colspan=9 class='dot-x'></td></tr>";
	}
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
	$mstring .="<tr height=40><td colspan=9 align=center>".page_bar($total, $page, $max,$query_string,"")."</td></tr>";
	
}else{
	$mstring .= "<tr height=50><td colspan=9 align=center style='padding-top:10px;'>1:1맞춤상담 내역이 없습니다.</td></tr>
				<tr height=1><td colspan=9 class='dot-x'></td></tr>";
}

//	$mstring .= "<tr height=40><td colspan=9 align=right style='padding-top:10px;'><a href='company.add.php'><img src='../image/b_companyadd.gif' border=0></a></td></tr>";

$mstring .="</table>";
$Contents = $mstring;




$P = new LayOut;
$P->addScript = "";
$P->strLeftMenu = cscenter_menu();
$P->OnloadFunction = "";
$P->Navigation = "HOME > 고객센타> 1:1맞춤상담 내역";
$P->strContents = $Contents;
$P->PrintLayOut();




?>