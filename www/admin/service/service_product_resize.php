<?
include("../class/layout.class");
include_once("service.lib.php");

$db = new Database();

$Contents02 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=9 style='padding-bottom:10px;'> ".GetTitleNavigation("서비스이미지리사이징관리", "서비스관리 > 서비스이미지리사이징관리 ")."</td>
	  </tr>
	<tr>
	    <td align='left' colspan=9 style='padding-bottom:3px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 20px;'><img src='../image/title_head.gif' align=absmiddle> <b>이미지리사이징 목록</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	  <form name='imageresize' method='post' action='service_product_resize.act.php' target='act'>
	  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
	    <!--td style='width:50px;' align=center> <input type='checkbox'></td-->
		<td class='s_td' style='width:180px;' align=center> 이미지 종류</td>
	    <td class='e_td' align=center> 사이즈 정보</td>
	  </tr>";
$sql = "select * from service_image_resizeinfo order by idx";
$db->query($sql);

$image_infos[1] = "확대이미지";
$image_infos[2] = "상세이미지";
$image_infos[3] = "리스트이미지";
$image_infos[4] = "리스트작은이미지";
$image_infos[5] = "썸네일이미지";
for($i=0;$i<$db->total;$i++){
	$db->fetch($i);
	$Contents02 .= "

	<tr bgcolor=#ffffff height=50>
		<td class='list_box_td list_bg_gray'> <input type='hidden' name='idx[]' value='".$db->dt[idx]."'> <b>".$image_infos[$db->dt[idx]]."</b>(".$db->dt[width]." / ".$db->dt[height].")</td>
	    <td class='list_box_td point' style='text-align:center;'> 
			<table border=0 align=center>
				<tr>
					<td>	가로 <input type='text' class='textbox number'  name='width[]' size=10 value='".$db->dt[width]."'> px  </td>
					<td style='padding-left:30px;'>세로 <input type='text' class='textbox number' name='height[]' size=10 value='".$db->dt[height]."'> px</td>
				</tr>
			</table>
		</td>
	</tr>";
}
$Contents02 .= "
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr><td colspan=2 align=center style='padding:10px 0px;'> <input type='image' src='../images/".$admininfo["language"]."/b_save.gif'></td></tr>
	  </form>
	  </table>";



$Contents = "<table width='100%' border=0>";

$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";

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

$Contents .= HelpBox("서비스관리", $help_text);

 $Script = "
 <script language='javascript'>

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
$P->Navigation = "HOME > 서비스관리 > 서비스이미지리사이징관리";
$P->NaviTitle = "서비스이미지리사이징관리";
$P->strLeftMenu = service_menu();
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