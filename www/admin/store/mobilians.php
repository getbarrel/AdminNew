<?
include("../class/layout.class");

$db = new Database;

//print_r($admininfo);

$db->query("SELECT * FROM shop_payment_config where pg_code = 'mobilians' and mall_ix = '".$admininfo[mall_ix]."' "); //and mall_ix = '".$admininfo[mall_ix]."'


if($db->total){

	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
		$payment_config[$db->dt[config_name]] = $db->dt[config_value];
		//$phone = explode("-",$db->dt[phone]);
		//$fax = explode("-",$db->dt[fax]);
	}
}

//print_r($payment_config);

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
	    <td align='left' colspan=3 > ".GetTitleNavigation("결제모듈(Mobilians)", "상점관리 > 결제모듈(Mobilians)")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>Mobilians 결제정보 </b> <span class=small>신용카드 결제 및 기타결제방식은 반드시 대행업체와 계약을 맺으시기 바랍니다 </span></div>")."</td>
	  </tr>
	 </table>
	 <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>Mobilians 상점 아이디 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'><input type=text class='textbox' name='MxID' value='".$payment_config[MxID]."' style='width:230px;' validation='true' title='상점 아이디'> <span class=small><b>Mobilians</b> 에서 발급 받은 상점 아이디을 입력해주세요</span></td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>스킨타입 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<select name='SkinType' validation='true' title='스킨타입'>
				<option value='' ".($payment_config[SkinType] == "" ? "selected":"").">스킨타입선택</option>
				<option value='00' ".($payment_config[SkinType] == "00" ? "selected":"").">BLUE</option>
				<option value='01' ".($payment_config[SkinType] == "01" ? "selected":"").">GRAY</option>
				<option value='02' ".($payment_config[SkinType] == "02" ? "selected":"").">GREEN</option>
				<option value='03' ".($payment_config[SkinType] == "03" ? "selected":"").">RED</option>
				<option value='04' ".($payment_config[SkinType] == "04" ? "selected":"").">YELLOW</option>
			</select>
		</td>
	  </tr>
	   <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>Mobilians 서비스타입 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'><input type='radio' name='mobilians_type' value='test' ".($payment_config[mobilians_type] == "test" ? "checked":"").">TEST <input type='radio' name='mobilians_type' value='service' ".($payment_config[mobilians_type] == "service" ? "checked":"").">SERVICE <span class=small><b>Mobilians</b> 에서 테스트키이면 TEST, 실제키를 발급받았으면 SERVICE 를 선택하시기 바랍니다.</span><input type='hidden' name='mobilians_type_befor' value='".$payment_config[mobilians_type]."' /></td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>일반할부기간</b> </td>
		<td class='input_box_item' style='padding:5px;'>
		<ul class='paging_area' >
			<li class='front'>
				<input type=text class='textbox' name='mobilians_interest_str' value='".$payment_config[mobilians_interest_str]."' style='width:300px;'><div class=small><br>일반결제시에 할부기간은 2~12개월까지 가능합니다.</div>
			</li>
			<li class='back' style='text-align:left'>
			<div style='line-height:140%;'>
				예제)<br>
				(1) 할부기간을 일시불만 가능하도록 사용할 경우<br /> -> 0 <br />
				(2) 할부기간을 일시불 ~ 12개월까지 사용할 경우<br /> -> 2:3  (2, 3개월만 가능) <br />
				(3) 할부기간을 일시불 ~ 6개월까지 사용할 경우<br /> -> 0:2:3:4:5:6:7:8:9:10:11:12 (일시불~12개월까지 가능)
				</div>
			</li>
		 </ul>
	  </td>
	  </tr>
	  <tr bgcolor=#ffffff height=54>
	    <td class='input_box_title'> <b>무이자할부여부</b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='mobilians_nointerest_price' value='".$payment_config[mobilians_nointerest_price]."' style='width:130px;'> 원 이상부터는 무이자 할부 적용<br>
	    <div class=small style='line-height:150%'>
	    ☞ 주의) Mobilians과 상점과의 별도 계약이 필요합니다.
	    </div>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title' valign=top style='padding-top:15px;'><b>무이자할부기간</b> </td>
	    <td class='input_box_item' valign=top style='padding:5px 10px;'>
		<ul class='paging_area' >
			<li class='front'>
				<input type=text class='textbox' name='mobilians_nointerest_str' value='".$payment_config[mobilians_nointerest_str]."'>
				<div class=small style='line-height:100%'>
				<b>☞ 주의) Mobilians와 상점과의 별도 계약이 필요합니다.</b><br>
				무이자 할부 서비스는 다음의 카드에만 적용되므로 유의하시기 바랍니다.<br>
				</div><br>
			</li>
			<li class='back' style='text-align:left'>
			<div >
				<div class=small style='line-height:150%'>
				예제)<br>
				(1) 무이자할부기간을 일시불만 가능하도록 사용할 경우<br /> -> 0 <br />
				(2) 무이자할부기간을 일시불 ~ 12개월까지 사용할 경우<br /> -> 2:3  (2, 3개월만 가능) <br />
				(3) 무이자할부기간을 일시불 ~ 6개월까지 사용할 경우<br /> -> 0:2:3:4:5:6:7:8:9:10:11:12 (일시불~12개월까지 가능)<br />
				</div>
			</li>
		 </ul>

	    </td>
	   </tr>
	  </table>";

$Contents02 = "
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>Mobilians 에스크로 </b> <span class=small>신용카드 결제 및 기타결제방식(에스크로 등)은 반드시 대행업체와 계약을 맺으시기 바랍니다 </span></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='20%' />
	  <col width='*' />
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title' style='width:15%;' > Escrow 사용여부 </td><td>
	    <input type=radio  name='escrow_use' id='escrow_use_0' value='1' ".CompareReturnValue("1",$payment_config[escrow_use],"checked")."><label for='escrow_use_0'>사용</label>
	    <input type=radio  name='escrow_use' id='escrow_use_1' value='0' ".CompareReturnValue("0",$payment_config[escrow_use],"checked")."><label for='escrow_use_1'>사용하지 않음</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title' > 적용금액 </td><td>
			<input type=radio  name='escrow_apply' id='escrow_apply_0' value='0'  ".CompareReturnValue("0",$payment_config[escrow_apply],"checked")."><label for='escrow_apply_0'>5만원 이상 결제금액에 대해 적용</label>
			<input type=radio  name='escrow_apply' id='escrow_apply_1' value='1'  ".CompareReturnValue("1",$payment_config[escrow_apply],"checked")."><label for='escrow_apply_1'>모든 금액에 대해 적용</label>
	    </td>
	  </tr>

	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title' > 결제수단 </td>
		<td class='input_box_item' >
			<input type=checkbox  name='escrow_method_bank' id='escrow_method_0' value='1' ".CompareReturnValue("1",$payment_config[escrow_method_bank],"checked")."><label for='escrow_method_0'>계좌이체</label>
			<input type=checkbox  name='escrow_method_vbank' id='escrow_method_1' value='1' ".CompareReturnValue("1",$payment_config[escrow_method_vbank],"checked")."><label for='escrow_method_1'>가상계좌</label>
			<!--input type=hidden  name='escrow_method_card' value='' -->
	    </td>
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
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<form name='edit_form' action='payment.act.php' method='post' onsubmit='return CheckFormValue(this)' target='iframe_act'><input name='act' type='hidden' value='update'><input name='mall_ix' type='hidden' value='".$admininfo[mall_ix]."'><input name='pg_code' type='hidden' value='mobilians'>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc02."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table ></form>";




$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 결제관련 > 결제모듈(Mobilians)";
$P->title = "결제모듈(Mobilians)";
$P->strContents = $Contents;
echo $P->PrintLayOut();

/*
create table shop_payment_config (
pg_code varchar(50) not null ,
config_name varchar(255) null default null,
config_value varchar(255) null default null,
primary key(pg_code, config_name));
*/
?>