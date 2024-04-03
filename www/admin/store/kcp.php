<?
include("../class/layout.class");

$db = new Database;


if ($_GET["mall_ix"] == "") {
    $mall_ix = $admininfo[mall_ix];
}

$db->query("SELECT * FROM shop_payment_config where pg_code = 'kcp' and mall_ix = '" . $mall_ix . "' "); //and mall_ix = '".$admininfo[mall_ix]."'


if ($db->total) {

    for ($i = 0; $i < $db->total; $i++) {
        $db->fetch($i);
        $payment_config[$db->dt[config_name]] = $db->dt[config_value];

    }
}


//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);

$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 계좌 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' >
	<col width='20%' />
	<col width='*' />
	  <tr>
	    <td align='left' colspan=3 > " . GetTitleNavigation("결제모듈(KCP)", "상점관리 > 결제모듈(KCP)") . "</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> " . colorCirCleBox("#efefef", "100%", "<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>KCP 결제정보 </b> <span class=small>신용카드 결제 및 기타결제방식은 반드시 대행업체와 계약을 맺으시기 바랍니다 </span></div>") . "</td>
	  </tr>
	 </table>
	 <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>KCP 가맹점 코드 <img src='" . $required3_path . "'></b></td>
		<td class='input_box_item'><input type=text class='textbox' name='site_cd' value='" . $payment_config[site_cd] . "' style='width:230px;' validation='true' title='가맹점 코드'> <span class=small><b>KCP</b> 에서 발급 받은 코드를 입력해주세요</span></td>
	  </tr>
	   <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>KCP 가맹점키 <img src='" . $required3_path . "'></b></td>
		<td class='input_box_item'><input type=text class='textbox' name='site_key' value='" . $payment_config[site_key] . "' style='width:230px;' validation='true' title='가맹점 KEY값'> <span class=small><b>KCP</b> 에서 발급 받은 상점키를 입력해주세요</span></td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>KCP 노출 상점명 <img src='" . $required3_path . "'></b></td>
		<td class='input_box_item'><input type=text class='textbox' name='site_name' value='" . $payment_config[site_name] . "' style='width:230px;' validation='true' title='KCP 노출 상점명'> <span class=small><b>KCP</b> 영문으로 입력해 주세요.</span></td>
	  </tr>
	   <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>KCP 서비스타입 <img src='" . $required3_path . "'></b></td>
		<td class='input_box_item'><input type='radio' name='service_type' value='test' " . ($payment_config[service_type] == "test" ? "checked" : "") . ">TEST <input type='radio' name='service_type' value='service' " . ($payment_config[service_type] == "service" ? "checked" : "") . ">SERVICE <span class=small><b>KCP</b> 에서 테스트키이면 TEST, 실제키를 발급받았으면 SERVICE 를 선택하시기 바랍니다.</span></td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>KCP 복합과세 사용 여부 <img src='" . $required3_path . "'></b></td>
		<td class='input_box_item'><input type='radio' name='tax_use_yn' value='N' " . ($payment_config[tax_use_yn] == "N" ? "checked" : "") . ">미사용 <input type='radio' name='tax_use_yn' value='Y' " . ($payment_config[tax_use_yn] == "Y" ? "checked" : "") . ">사용</td>
	  </tr>
	  </table>";

$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td ><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small><b> PG사와 계약을 맺은 이후에는 메일로 받으신 실제 ID 를 넣으시면 됩니다.</b> </td>
</tr>
</table>
";

$ContentsDesc02 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td ><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;padding-bottom:2px;' class=small><b> 전자상거래소비자보호법 및 시행령 개정에 따라 2006년 4월1일부터 10만원 이상 현금 결제시 의무 시행됩니다.</b> </td>
</tr>
<tr>
	<td ></td>
	<td align=left style='padding:2px;line-height:150%' class=small>
	- 에스크로 사용범위 및 사용금액에 대한것은 신청한 PG사나 은행에 따라 다를 수 있으므로 협의를 하셔야 합니다. <br>
	- 에스크로 결제를 선택했을 경우 할증하는 방식으로 고객에게 수수료를 총액 대비로 부담 시킬 수 있으며 이 기능을 적용하시는 경우에는 <span style='color:red'>추후 법적인 문제</span>가  발생할 수 있기 때문에, 아주 신중하게 적용해 주셔야 합니다! <br>
	-또한 각 PG 사나 은행의 에스크로 수수료이하로 설정을 해주셔야 합니다.
	</td>
</tr>
</table>
";
if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "U")) {
    $ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff><td colspan=4 align=center><input type='image' src='../images/" . $admininfo["language"] . "/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<table width='100%' border=0>";
$Contents = $Contents . "<form name='edit_form' action='kcp.act.php' method='post' onsubmit='return CheckFormValue(this)'><input name='act' type='hidden' value='update'><input name='mall_ix' type='hidden' value='" . $mall_ix . "'>";
$Contents = $Contents . "<tr><td>" . $Contents01 . "</td></tr>";
$Contents = $Contents . "<tr><td>" . $ContentsDesc01 . "</td></tr>";
$Contents = $Contents . "<tr><td>" . $Contents02 . "</td></tr>";
$Contents = $Contents . "<tr><td>" . $ContentsDesc02 . "</td></tr>";
$Contents = $Contents . "<tr height=30><td></td></tr>";
$Contents = $Contents . "<tr><td>";
$Contents = $Contents . $ButtonString;
$Contents = $Contents . "</td></tr></form>";
$Contents = $Contents . "</table >";


$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 결제관련 > 결제모듈(KCP)";
$P->title = "결제모듈(KCP)";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>