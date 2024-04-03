<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2017-06-22
 * Time: 오후 5:20
 */

include("../class/layout.class");



$db = new Database;


if($_GET["mall_ix"] == ""){
    $mall_ix = $admininfo[mall_ix];
}

$db->query("SELECT * FROM shop_payment_config where pg_code = 'inipay_standard' and mall_ix = '".$mall_ix."' "); //and mall_ix = '".$admininfo[mall_ix]."'


if($db->total){

    for($i=0;$i < $db->total;$i++){
        $db->fetch($i);
        $payment_config[$db->dt[config_name]] = $db->dt[config_value];

    }
}



$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;'>
		<img src='../image/emo_3_15.gif' align=absmiddle>  거래은행 및 계좌 정보는 결제시 이용됩니다. 정확하게 입력해주세요
	</td>
</tr>
</table>
";

$Contents01 = "<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'  >
	  <col width='20%'/>
	  <col width='*'/>  
	 <tr height=40>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>이니시스 결제정보 </b> <span class=small>신용카드 결제 및 기타결제방식은 반드시 대행업체와 계약을 맺으시기 바랍니다 </span></div>")."</td>
	  </tr>
	  </table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='20%'/>
	  <col width='*'/>  
	   <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>서비스타입 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='service_type' id='service_type_t' value='test' ".($payment_config[service_type] == "test" ? "checked":"")."> <label for='service_type_t'>TEST</label> 
			<input type='radio' name='service_type' id='service_type_s' value='service' ".($payment_config[service_type] == "service" ? "checked":"")."><label for='service_type_s'>SERVICE</label> 
			<span style='size:10px; color:blue;'>[테스트키이면 TEST, 실제키를 발급받았으면 SERVICE 를 선택하시기 바랍니다.]</span>
			</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td style='width:150px;' class='input_box_title'> INIPay ID </td>
		<td class='input_box_item'><input type=text class='textbox' name='mid' value='".$payment_config[mid]."' style='width:230px;'> <span class=small><b>inicis</b> 에서 발급 받은 아이디를 입력해주세요</span>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td style='width:150px;' class='input_box_title'> INIPay signkey </td>
		<td class='input_box_item'><input type=text class='textbox' name='service_key' value='".$payment_config[service_key]."' style='width:230px;'> <span class=small><b>inicis</b> 에서 발급 받은 사인키를 입력해주세요</span>
	  </tr>
	  
	  </table>";

$Contents02 = "	  
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'  >
	  <col width='15%'/>
	  <col width='60%'/>
	  <col width='35%'/>
	  <col width='5%'/>
	  <tr height=40>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>이니시스 에스크로 </b> <span class=small>신용카드 결제 및 기타결제방식은 반드시 대행업체와 계약을 맺으시기 바랍니다 </span></div>")."</td>
	  </tr>	  	  
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='20%'/>
	  <col width='*'/>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> Escrow 사용여부 </td>
		<td class='input_box_item'>
	    <input type=radio  name='escrow_use' id='escrow_use_0' value='1' ".CompareReturnValue("1",$payment_config[escrow_use],"checked")."><label for='escrow_use_0'>사용</label> 
	    <input type=radio  name='escrow_use' id='escrow_use_1' value='0' ".CompareReturnValue("0",$payment_config[escrow_use],"checked")."><label for='escrow_use_1'>사용하지 않음</label>
	    </td>
	  </tr>
	  
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> Escrow ID </td>
		<td class='input_box_item'><input type=text class='textbox' name='escrow_mid' value='".$payment_config[escrow_mid]."' style='width:230px;'> <span class=small><b>inicis</b> 에서 발급 받은 아이디를 입력해주세요</span></td>
	  </tr>
	 
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 결제수단 </td>
		<td class='input_box_item'>
	    <input type=checkbox  name='escrow_method_iche' id='escrow_method_5' value='5' ".CompareReturnValue("5",$payment_config[escrow_method_bank],"checked")."><label for='escrow_method_5'>계좌이체</label> 
	    <input type=checkbox  name='escrow_method_vbank' id='escrow_method_4' value='4' ".CompareReturnValue("4",$payment_config[escrow_method_vbank],"checked")."><label for='escrow_method_4'>가상계좌</label>
	    <input type=checkbox  name='escrow_method_card' id='escrow_method_1' value='1' ".CompareReturnValue("1",$payment_config[escrow_method_card],"checked")."><label for='escrow_method_1'>신용카드</label>
	    </td>
	  </tr>
	  	  	 
	  </table>
	  ";

$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td ><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' class=small><b> PG사와 계약을 맺은 이후에는 메일로 받으신 실제 ID, Key를 넣으시면 됩니다.</b> </td>
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

$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='edit_form' action='inipay_standard.act.php' method='post' onsubmit='return validate(document.edit_form)' target='act'>
<input name='act' type='hidden' value='update'>
<input name='mall_ix' type='hidden' value='".$mall_ix."'>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc02."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr></form>";
$Contents = $Contents."</table >";




$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "HOME > 상점관리 > 결제모듈(이니페이 스텐다드)";
$P->title = "결제모듈(이니페이 스텐다드)";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>