<?php
include("../class/reportpage.class");
include("../include/referTree.php");

$db = new forbizDatabase;

$Tree ="
<table cellpadding=0 cellspacing=0 width=190>
<tr>
	<td bgcolor='#ffffff'>
		<form>
		<div id=TREE_BAR style=\"margin:5;\">
		".GetTreeNode()."
		</div>
		</form>
	</td>
</tr>
</table>
";

$Contents = $Contents.TitleBar("레퍼러 관리");
$Contents = $Contents."
		<table cellpadding=0 cellspacing=0 border=0>
		<tr>

			<td width=200 valign=top style='border:0px solid #000000'>
			<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8'>
				<tr>
					<td width=200 height=400 valign=top style='overflow:auto;padding:10px;'>
					<div id=TREE_BAR style=\"width:200px;height:420px;padding:5px;overflow:auto;margin:1;background-color:#ffffff\" >
					".$Tree."
					</div>
					</td>
					<td style='padding:10px; text-align:center;'>
					<form name='category_order' method='get' action='categoryorder.php' target='calcufrm'>
						<input type='hidden' name='this_depth' value=''>
						<input type='hidden' name='cid' value=''>
						<input type='hidden' name='mode' value=''>
						<img src='img/up.gif' onclick='order_up(document.category_order)' style='cursor:hand  align:absmiddle'><br><br>
						<img src='../../images/".$admininfo[language]."/btn_arranged_order.gif' onclick='order_up(document.category_order)' style='cursor:hand align:absmiddle'><br><br>
						<img src='img/down.gif' onclick='order_down(document.category_order)' style='cursor:hand  align:absmiddle'>
					</form>
					</td>
				</tr>
			</table>
			</td >

			<td style='padding:10px;padding-right:100px;'>
			</td>
			<td valign=top >

				<form name='thisCategoryform' method='post' action='referer.save.php' enctype='multipart/form-data' target='calcufrm'>
				<input type='hidden' name='mode' value=''>
				<input type='hidden' name='this_depth' value=''>
				<input type='hidden' name='cid' value=''>
				
				<table >
					<tr height=30>
						<td><img src='../../images/dot_org.gif' align='absmiddle' style='position:relative;'><b> 선택된 카테고리</b></td>
					</tr>
					<tr height=30>
						<td><input type='text'  class=textbox name='this_category'> <img src='../../images/".$admininfo[language]."/btc_modify.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"CategorySave(document.thisCategoryform,'modify');\"> <img src='../../images/".$admininfo[language]."/btc_del.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"CategorySave(document.thisCategoryform,'del');\">
						</td>
					</tr>
					<tr height=30>
						<td><input type='text' class=textbox name='this_referer_url' size=30> (search url)</td>
					</tr>
					<tr height=30>
						<td><input type='text' class=textbox name='this_keyword' size=30> (keyword)</td>
					</tr>
					<tr height=30>
						<td><input type='text' class=textbox name='this_parameter' size=30> (parameter) </td>
					</tr>
					<tr height=30 >
						<td ><input type='file' class='textbox' name='category_img' style='padding-bottom:4px;'> (image) </td>
					</tr>
					<tr height=50>
						<td ><div id='catimg'></div></td>
					</tr>
				</table>
				</form>
				<div id=add_subcategory>
				<form name='subCategoryform' method='post' action='referer.save.php' target='calcufrm'>
				<input type='hidden' name='mode' value=''>
				<input type='hidden' name='sub_depth' value=''>
				<input type='hidden' name='cid' value=''>
				<input type='hidden' name='sub_cid' value=''>
				<table>
					<tr height=30>
						<td><img src='../../images/dot_org.gif' align='absmiddle' style='position:relative;'><b> 하부카테고리 추가</b></td>
					</tr>
					<tr height=30>
						<td><input type='text' class=textbox name='sub_category'> <img src='../../images/".$admininfo[language]."/btn_s_ok.gif' border=0 align=absmiddle style='cursor:hand' onclick=\"SubCategorySave(document.subCategoryform,'insert');\">  
						</td>
					</tr>
					<tr height=30>
						<td><input type='text' class=textbox name='sub_referer_url' size=39></td>
					</tr>
				</table>
				</form>
				<br>

				<!--
				카테고리 사용유무<br>
				<input type=radio name='y'>사용 <input type=radio name='n'>미사용<br><br>
				카테고리이미지 추가<br>
				<input type=file name='category_img'><br><br>
				서브이미지 추가<br>
				<input type=file name='sub_img'><br><br>
				-->

			</td>
			<td style='padding-left:30px;'>
			</td>
		</tr>
		</table>
		<iframe name='calcufrm' id='calcufrm' src='' width=0 height=0></iframe>";
/*
		$help_text = "
		<table>
			<tr>
				<td style='line-height:150%'>
				- 레퍼러 관리란? 각 카테고리별 레퍼러 사이트의 URL을 등록/수정하고, 카테고리 구성을 등록/수정하실 수 있는 메뉴입니다.<br><br>
				* 카테고리<br>
				  &nbsp;- 각 카테고리를 클릭하시면 우측에서 선택된 카테고리에 대한 내용을 확인/수정하실 수 있습니다.<br>
				  &nbsp;- 카테고리 메뉴의 우측에 배열순서 화살표를 클릭하시면 선택된 카테고리의 위치를 변경하실 수 있습니다.<br>
				  &nbsp;- 하위 카테고리 신규등록을 위해서는 상위 카테고리를 반드시 선택하신 뒤 작업을 하셔야 합니다.<br><br>

				* 선택된 카테고리<br>
				  &nbsp;- 카테고리 제목을 변경하신 뒤 수정을 클릭하시면 카테고리 제목이 변경됩니다. <br>&nbsp;&nbsp;삭제를 클릭하시면 해당 카테고리에 포함 된 하위 카테고리 모두가 삭제 됩니다.<br>
				  &nbsp;- Search URL : Search URL을 입력하세요.<br>
				  &nbsp;- Keyword : Keyword를 입력하세요.<br>
				  &nbsp;- Parameter : Parameter를 입력하세요.<br>
				  &nbsp;- 하부 카테고리 추가 : 좌측에서 카테고리를 선택하신 후 하부 카테고리 추가 항목에 추가할 카테고리 명을 입력하신후<br>
				    &nbsp;&nbsp;URL을 입력하신 후 확인을 클릭하시면 신규 카테고리 및 사이트가 등록됩니다.<br>
				</td>
			</tr>
		</table>
		";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


$Contents .= HelpBox("레퍼러 관리", $help_text);
/*
$Script = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>\n<script language='JavaScript' src='referer.js'></script>";
$P = new AdminPage();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
echo $P->AdminFrame();
*/
if ($mode == "iframe"){
    echo "<html>";
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<body>";
    echo "<Script>parent.vdate;parent.ChangeCalenderView($SelectReport);alert(language_data['referer.php']['A'][language]);</Script>";//'통계 관리자 모드에서는 달력을 사용 하실 수 없습니다.'
    echo "</body>";
    echo "</html>";
}else{
    $Script = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>\n<script language='JavaScript' src='referer.js'></script>";
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 관리자모드 > 레퍼러관리";
    $p->title = "레퍼러관리";
    $p->addScript = $Script;
    $p->forbizLeftMenu = Stat_munu('referer.php');
    $p->forbizContents = $Contents;
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>\n<script language='JavaScript' src='referer.js'></script>";
    $p->PrintReportPage();
}
/*


CREATE TABLE ".TBL_LOGSTORY_REFERER_CATEGORYINFO." (
  cid varchar(15) default NULL,
  depth smallint(1) unsigned default NULL,
  vlevel1 int(3) default NULL,
  vlevel2 int(3) default NULL,
  vlevel3 int(3) default NULL,
  vlevel4 int(3) default NULL,
  vlevel5 int(3) default NULL,
  cname varchar(40) default '',
  catimg varchar(100) default NULL,
  subimg varchar(100) default NULL,
  category_use char(1) default NULL,
  regdate date default NULL
) TYPE=MyISAM;
*/
?>