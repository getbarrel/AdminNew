<?
include("../class/layout.class");

$Script = "
<script language='JavaScript' >

</Script>
";

$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("카페24 TEST", "카페24 TEST")."</td>
</tr>
<tr>
	<td colspan=2 width='100%' valign=top style='padding-top:3px;'>

		<iframe name='manual_order_frame' id='manual_order_frame' width='1300px' height='1000px' frameborder='0' src='http://box.daisomall.co.kr/customer/index.php?page=box_guide'></iframe>

	</td>
</tr>
</table>
";



$P = new LayOut;
$P->addScript = $Script;
$P->OnloadFunction = "";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
$P->Navigation = "주문관리 > 카페24 TEST";
$P->title = "카페24 TEST";
$P->PrintLayOut();


?>