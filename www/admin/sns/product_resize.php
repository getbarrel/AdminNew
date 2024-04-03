<?
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

$db = new Database();

$Contents02 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='50' />
	<col width='80' />
	<col width='*' />
	<tr >
		<td align='left' colspan=3 style='padding-bottom:10px;'> ".GetTitleNavigation("이미지리사이징관리", "상품관리 > 이미지리사이징관리 ")."</td>
	</tr>
	<tr>
	    <td align='left' colspan=3> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 20px;'><img src='../image/title_head.gif' align=absmiddle> <b>이미지리사이징 목록</b></div>")."</td>
	  </tr>
	  <tr height=10><td colspan=3 ></td></tr>
	  <form name='imageresize' method='post' action='product_resize.act.php' target='act'>
	  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
	    <td style='width:50px;' align=center> <input type='checkbox'></td>
		<td style='width:80px;' align=center> 가로/세로</td>
	    <td  align=center> 수정</td>
	  </tr>";
$sql = "select * from ".TBL_SNS_IMAGE_RESIZEINFO." order by idx";
$db->query($sql);
for($i=0;$i<$db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "

	<tr bgcolor=#ffffff height=50>
		<td align=center> <input type='hidden' name='idx[]' value='".$db->dt[idx]."'></td>
		<td align=center> ".$db->dt[width]."/".$db->dt[height]."</td>
	    <td align=center> 가로 <input type='text' class=textbox name='width[]' size=7 value='".$db->dt[width]."' style='height:22px;text-align=right;'> 세로 <input type='text' class=textbox name='height[]' size=7 value='".$db->dt[height]."' style='height:22px;text-align=right;'></td>
	</tr>
	<tr height=1><td colspan=3 class='dot-x'></td></tr>	 ";
}
$Contents02 .= "
	<tr><td colspan=3 align=center style='padding:20px 0px;'> <input type='image' src='../images/".$admininfo["language"]."/b_save.gif'></td></tr>
	  </form>
	  </table>";



$Contents = "<table width='100%' border=0>";

$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table ><iframe name='act' src='' width=0 height=0></iframe>";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배너를 등록하신후 치환함수를 이용해 디자인에 적용하실 수 있습니다</td></tr>

</table>

";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("배너관리", $help_text);

 $Script = "
 <script language='javascript'>

	 function deleteBanner(act, banner_ix){
		if(confirm(language_data['sns_product_resize.php']['A'][language])){
		//'배너를 정말로 삭제하시겠습니까?'
			document.location.href = 'banner.act.php?act='+act+'&banner_ix='+banner_ix;
		}
	 }
	function clearAll(frm){
		for(i=0;i < frm.id.length;i++){
				frm.id[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.id.length;i++){
				frm.id[i].checked = true;
		}
}
function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}
 </script>
 ";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "상품관리 > 이미지리사이징관리";
$P->NaviTitle = "이미지리사이징관리";
$P->strLeftMenu = product_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();




/*

create table shop_bannerinfo (
banner_ix int(4) unsigned not null auto_increment  ,
banner_name varchar(20) null default null,
banner_link varchar(255)  null default null,
banner_target varchar(20) null default null,
banner_desc varchar(255)  null default null,
regdate datetime not null,
primary key(banner_ix));
*/
?>