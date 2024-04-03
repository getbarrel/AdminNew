<? 
include("../class/layout.class");

$db = new Database;

//print_r($admininfo);
if($_GET["mall_ix"] == ""){
	$mall_ix = $admininfo[mall_ix];
}

$db->query("SELECT * FROM shop_payment_config where pg_code = 'inicis_alipay' and mall_ix = '".$mall_ix."' "); //and mall_ix = '".$admininfo[mall_ix]."'


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
	  <col width='*'/>  ";
	  if($_SESSION["admin_config"][front_multiview] == "Y"){
		$Contents01 .= "
		<tr>
			<td class='search_box_title' > 프론트 전시 구분</td>
			<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
		</tr>";
		}
		$Contents01 .= "
	   <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>서비스타입 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='service_type' value='test' ".($payment_config[service_type] == "test" ? "checked":"").">TEST 
			<input type='radio' name='service_type' value='service' ".($payment_config[service_type] == "service" ? "checked":"").">SERVICE 
			<span style='size:10px; color:blue;'>[테스트키이면 TEST, 실제키를 발급받았으면 SERVICE 를 선택하시기 바랍니다.]</span>
			<input type='hidden' name='service_type_befor' value='".$payment_config[service_type]."' /></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td style='width:150px;' class='input_box_title'> inicis_alipay ID </td>
		<td class='input_box_item'><input type=text class='textbox' name='mid' value='".$payment_config[mid]."' style='width:230px;'> <span class=small><b>inicis</b> 에서 발급 받은 아이디를 입력해주세요</span><input type='hidden' name='inipay_mid_befor' value='".$payment_config[mid]."' /></td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td style='width:150px;' class='input_box_title'> inicis_alipay signkey </td>
		<td class='input_box_item'><input type=text class='textbox' name='pay_key' value='".$payment_config[pay_key]."' style='width:230px;'> <span class=small><b>inicis</b> 에서 발급 받은 사인키를 입력해주세요</span>
	  </tr>
	  <tr bgcolor=#ffffff height=60>
	    <td class='input_box_title'> 일반할부기간 </td>
		<td class='input_box_item'><input type=text class='textbox' name='interest_str' value='".$payment_config[interest_str]."' style='width:800px;'>
	    <div class=small>ex) 선택:일시불:3개월:4개월:5개월:6개월:7개월:8개월:9개월:10개월:11개월:12개월</div></td>
	  </tr>
	   
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 무이자할부여부 </td>
		<td class='input_box_item'>
	    <input type=radio  name='nointerest_use' id='nointerest_use_n' value='no' ".CompareReturnValue("no",$payment_config[nointerest_use],"checked")."><label for='nointerest_use_n'>일반결제</label> 
	    <input type=radio  name='nointerest_use' id='nointerest_use_y' value='yes' ".CompareReturnValue("yes",$payment_config[nointerest_use],"checked")."><label for='nointerest_use_y'>무이자결제</label>
	    </td>
	  </tr>
	  
	  <tr bgcolor=#ffffff height=120>
	    <td class='input_box_title'> 무이자할부기간 </td>
	    <td class='input_box_item'>
	    <textarea class='textbox' name='nointerest_str' style='width:800px;height:60px;padding:3px;'>".$payment_config[nointerest_str]."</textarea> 
	    <div class=small style='padding:10px 10px 10px 0px;'>ex) 01-선택:일시불:3개월:4개월,03-선택:일시불:3개월:4개월,04-선택:일시불:3개월:4개월,06-선택:일시불:3개월:4개월,011-선택:일시불:3개월:4개월,12-선택:일시불:3개월:4개월</div></td>
	  </tr>
	  
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 카드사코드정보 </td>
	    <td class='input_box_item'> 01-외환, 03-롯데, 04-현대, 06-국민, 11-BC, 12-삼성, 13-LG, 14-신한, 21-해외비자, 22-해외마스터, 23-JCB, 24-해외아멕스, 25-해외다이너스, 41-농협 </td>
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
	    <td class='input_box_title'> Escrow Key File mcert.pem</td>
		<td class='input_box_item'><input type=file class='textbox' name='inipay_file1' value='' style='width:230px;'> <span class=small><b>mcert.pem </b>파일을 입력해주세요</span></td>
	  </tr>
	  
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> Escrow Key File mpriv.pem</td>
		<td class='input_box_item'><input type=file class='textbox' name='inipay_file2' value='' style='width:230px;'> <span class=small><b>mpriv.pem</b> 파일을 입력해주세요</span></td>
	  </tr>
	  
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> Escrow Key File keypass.enc</td>
		<td class='input_box_item'><input type=file class='textbox' name='inipay_file3' value='' style='width:230px;'> <span class=small><b>keypass.enc</b> 파일을 입력해주세요</span></td>
	  </tr>
	  
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 적용금액 </td>
		<td class='input_box_item'>
	    <input type=radio  name='escrow_apply' id='escrow_apply_0' value='0'  ".CompareReturnValue("0",$payment_config[escrow_apply],"checked")."><label for='escrow_apply_0'>5만원 이상 결제금액에 대해 적용</label> 
	    <input type=radio  name='escrow_apply' id='escrow_apply_1' value='1'  ".CompareReturnValue("1",$payment_config[escrow_apply],"checked")."><label for='escrow_apply_1'>모든 금액에 대해 적용</label>
	    </td>
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
$Contents = $Contents."<form name='edit_form' action='inicis_alipay.act.php' method='post' onsubmit='return validate(document.edit_form)' target='act'><input name='act' type='hidden' value='update'>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc02."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr></form>";
$Contents = $Contents."</table >";

 
	

$Script = "<script language='javascript' src='basicinfo.js'></script>

<script language='javascript'>
	$(document).ready(function(){
		$('select[name=\"mall_ix\"]').change(function(){
			var link =  document.location.href;
			location.href=link+'&mall_ix='+$(this).val();

		});
	});

</script>
";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "HOME > 상점관리 > 결제모듈(이니시스_알리페이)";
$P->title = "결제모듈(이니시스_알리페이)";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>