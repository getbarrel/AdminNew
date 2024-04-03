<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

$Script = "<script language='javascript'>
function eventDelete(md_ix){
	if(confirm(language_data['hot_stuff.php']['A'][language]))
	{//'해당 메인추천상품관리상품을 정말로 삭제하시겠습니까? 삭제하시면 관련된 모든 이미지가 삭제됩니다.'
		document.frames('act').location.href= 'hot.act.php?act=delete&md_ix='+md_ix;
	}


}
</script>";


$mstring ="<form name=poll action='board.manage.act.php'><input type=hidden name=act value=insert>
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("메인추천상품관리 관리", "마케팅지원 > 메인추천상품관리 관리 ")."</td>
		</tr>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:15px;'>
			    <div class='tab'>
					<table class='s_org_tab' style='width:100%'>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".($div_ix == "" ? "class='on'":"")."  >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?div_ix='\">전체보기</td>
								<th class='box_03'></th>
							</tr>
							</table>";
$sql = 	"SELECT * FROM shop_recommend_div where disp=1 ";

$db->query($sql);
for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
$mstring .= "<table id='tab_".($i+2)."' ".($div_ix == $db->dt[div_ix] ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?div_ix=".$db->dt[div_ix]."'\">".$db->dt[div_name]."</td>
								<th class='box_03'></th>
							</tr>
							</table>";
}
$mstring .= "
						</td>
						<td align=right>
							<input type='button' name='hot_stuff' value='메인 추천상품 분류' onClick='location.href=\"hot_stuff_category.php\";' style='height:20px;width:120px;color:black;font-weight:600;cursor:pointer;' />
						</td>
					</tr>
					</table>
					</div>
			</td>
		</tr>
		<tr>
			<td>
			".PrintEventList()."
			</td>
		</tr>
		</form>";
$mstring .="</table>";


$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>메인추천상품관리 추가</b>를 원하시면 이벤트 추가버튼을 클릭해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기간이 만료된 메인추천상품관리는 자동으로 노출이 종료됩니다</td></tr>
</table>
";


$help_text = HelpBox("메인추천상품관리 관리", $help_text);

$Contents = $mstring.$help_text;


$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "HOME > 마케팅지원 > 메인추천상품관리 관리";
$P->strLeftMenu = marketting_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintEventList(){
	global $db, $mdb, $div_ix,$nset,$page;

	if($div_ix){
		$sql = "select count(*) from shop_recommend r , shop_recommend_div rd where r.div_ix = rd.div_ix and rd.disp = 1 and r.div_ix = '$div_ix'";
	}else{
		$sql = "select count(*) from shop_recommend r, shop_recommend_div rd where r.div_ix = rd.div_ix and rd.disp = 1 ";
	}
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[0];

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=0 cellspacing=0 border=0 width=100% bgcolor=silver>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25>
		<td class=s_td width='15%'>분류</td>
		<td class=m_td width='30%'>메인추천상품관리 제목</td>
		<td class=m_td width='15%'>표시</td>
		<td class=m_td width='*'>노출기간</td>
		<td class=e_td width='15%'>관리</td>
		</tr>";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=5 align=center>메인추천상품관리 내역이 존재 하지 않습니다.</td></tr>";
		//$mString .= "<tr height=1 bgcolor=silver><td colspan=5></td></tr>";
		$mString .= "<tr height=1><td colspan=5 class='td_underline'></td></tr>";
		$mString .= "<tr bgcolor=#ffffff ><td colspan=5 align=right><a href='hot.write.php'><img src='../images/b_eventadd.gif' border=0 ></a></td></tr>";

	}else{

		if($div_ix){
			$sql = "select * from shop_recommend r, shop_recommend_div rd where r.div_ix = rd.div_ix and rd.disp = 1 and r.div_ix = '$div_ix' order by  r.regdate desc limit $start , $max";
		}else{
			$sql = "select * from shop_recommend r , shop_recommend_div rd where r.div_ix = rd.div_ix and rd.disp = 1 order by  r.regdate desc limit $start , $max";
		}
		$db->query($sql);
		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			$mString = $mString."<tr bgcolor=#ffffff align=center>
			<td height=30>".$db->dt[div_name]."</td>
			<td bgcolor='#efefef' align=left style='padding-left:10px;'><a href='hot.write.php?md_ix=".$db->dt[md_ix]."'>".$db->dt[md_title]."</a></td>
			<td >".($db->dt[disp] == "1" ? "표시":"표시하지 않음")."</td>
			<td bgcolor='#efefef'>".ChangeDate($db->dt[md_use_sdate])." ~ ".ChangeDate($db->dt[md_use_edate])."</td>
			<td align=center>
				<a href=\"JavaScript:eventDelete('".$db->dt[md_ix]."')\"><img  src='../image/btc_del.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=5 class='td_underline'></td></tr>
			";
		}
		//$mString .= "<tr height=1 bgcolor=silver><td colspan=5></td></tr>";
		$mString .= "<tr height=50 bgcolor=#ffffff>
					<td colspan=3 align=left>".page_bar($total, $page, $max,  "&max=$max","")."</td>
					<td colspan=2 align=right><a href='hot.write.php'><img src='../image/b_mainrecommadd.gif' border=0 ></a></td>
				</tr>";
	}


	$mString .= "</table>";

	return $mString;
}


?>
