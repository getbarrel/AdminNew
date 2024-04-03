<?
include("../class/layout.class");




$db = new Database;

$db->query("select * from ".TBL_SHOP_HTML_LIBRARY." ");
$db->fetch();

$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' >
	<tr>
	    <td align='left' colspan=6 > ".GetTitleNavigation("HTML 라이브러리", "디자인관리 > <a onclick=\"PoPWindow('../design/html_library.write.php',650,500,'html_lib')\" >HTML 라이브러리</a> ")."</td>
	</tr>
	<!--tr height=20><td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table width=100% style='padding:5px;'><tr><td><img src='../image/title_head.gif' align=absmiddle><b> 쇼핑몰 디자인 관리 :HTML 디자인 템플릿</b></td><td align=right style='padding:0 20 0 0'><a onclick=\"PoPWindow('../design/html_library.write.php',650,500,'html_lib')\" >html lib 저장하기</a></td></tr></table>")."</td></tr-->
	<tr height=100> 
	<td width='100%' colspan='2' valign=top style='padding:20px 10px 0px 10px;'>
	<table cellpadding=0 cellspacing=0 width=100% >
	<col width='500px'>
	<col width='*'>
	";

for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
$Contents .="
	<tr >
		<td  height=30 style='padding:0 20px 0 10px'  nowrap>
			<b><a onclick=\"PoPWindow('../design/html_library.write.php?hl_ix=".$db->dt[hl_ix]."',650,500,'html_lib')\" >".$db->dt[hl_name]."&nbsp;</a></b> : ".$db->dt[hl_desc]."
		</td>
		<td rowspan=2>
<textarea onkeydown=\"textarea_useTab( this, event );\" name='html_code' wrap='off'  cols=10 style='width:95%;height:150px;'>
".$db->dt[html_code]."
</textarea>
		</td>
	</tr>
	<tr>
		<td style='padding:0 20px 0 0' >".$db->dt[html_code]."</td>
	</tr>
	<tr><td colspan=2 height=30></td></tr>
	";
}
$Contents .="
	</table>				
	</td>	
</tr>
<tr> 
	<td bgcolor='#ffffff' height='100' colspan='2' >	
	</td>
</tr>	
</table>
		
                  ";
                  
$P = new LayOut;
$P->addScript = "<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>\n$Script";
//$LO->OnloadFunction = "PageLoad();";//showSubMenuLayer('storeleft');
$P->title = "";
$P->strLeftMenu = design_menu();
$P->strContents = $Contents;
$P->Navigation = "디자인관리 > 디자인관리 > HTML 라이브러리";
$P->title = "HTML 라이브러리";
$P->PrintLayOut();


?>