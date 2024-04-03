<?
include("../class/layout.class");



$db = new Database;


$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' limit 1");

$db->fetch();

$phone = explode("-",$db->dt[phone]);
$fax = explode("-",$db->dt[fax]);

//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' >
	<col width='20%' />
	<col width='*' />
	  <tr>
	    <td align='left' colspan=3 > ".GetTitleNavigation("결제모듈(플러그앤페이)", "상점관리 > 결제모듈(플러그앤페이)")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>PNP 결제정보 </b> <span class=small>신용카드 결제 및 기타결제방식은 반드시 대행업체와 계약을 맺으시기 바랍니다 </span></div>")."</td>
	  </tr>
	 </table>
	 <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>Publisher Name<img src='".$required3_path."'></b></td>
		<td class='input_box_item'><input type=text class='textbox' name='plugnpay_publisher_name' value='".$db->dt[plugnpay_publisher_name]."' style='width:230px;' validation='true' title='Publisher Name'> <span class=small><b>플러그앤페이</b> 에서 발급 받은 아이디를 입력해주세요</span><input type='hidden' name='plugnpay_publisher_name_befor' value='".$db->dt[plugnpay_publisher_name]."' /></td>
	   </tr>
	</table>
	  ";

$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td ><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small><b> PG사와 계약을 맺은 이후에는 메일로 받으신 실제 ID 를 넣으시면 됩니다.</b> </td>
</tr>
</table>
";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='edit_form' action='plugnpay.act.php' method='post' onsubmit='return CheckFormValue(this)'><input name='act' type='hidden' value='update'><input name='mall_ix' type='hidden' value='".$db->dt[mall_ix]."'>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."</td></tr>";
//$Contents = $Contents."<tr><td>".$ContentsDesc02."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr></form>";
$Contents = $Contents."</table >";




$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 결제관련 > 결제모듈(플러그앤페이)";
$P->title = "결제모듈(플러그앤페이)";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>