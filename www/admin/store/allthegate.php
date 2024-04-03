<? 
include("../class/layout.class");



$db = new Database;


$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' limit 1");

$db->fetch();

$phone = explode("-",$db->dt[phone]);
$fax = explode("-",$db->dt[fax]);

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
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=4 > ".GetTitleNavigation("결제모듈(올더게이트)", "상점관리 > 결제모듈(올더게이트)")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>올더게이트 결제정보 </b> <span class=small>신용카드 결제 및 기타결제방식은 반드시 대행업체와 계약을 맺으시기 바랍니다 </span></div>")."</td>
	  </tr>	  	  
	  <tr bgcolor=#ffffff >
	    <td style='width:150px;' ><img src='../image/ico_dot2.gif' align=absmiddle> 올더게이트 가맹점 ID </td><td colspan=3><input type=text class='textbox' name='allthegate_id' value='".$db->dt[allthegate_id]."' style='width:230px;'> <span class=small>예) aegis <b>올더게이트</b> 에서 발급 받은 아이디를 입력해주세요</span></td>
	    
	  </tr>
	  <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>	  
	  <tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> 일반할부기간 </td><td><input type=text class='textbox' name='allthegate_interest_str' value='".$db->dt[allthegate_interest_str]."' style='width:330px;'>
	    <div class=small><br>일반결제시에 할부기간은 2~12개월까지 가능합니다.</div></td>
	    <td align=left colspan=2>
	    <div class=small>
	    	예제) 0:2:3:4:5:6:7:8:9:10:11:12<br>
		(1) 할부기간을 일시불만 가능하도록 사용할 경우<br>
		0<br>
		(2) 할부기간을 일시불 ~ 12개월까지 사용할 경우<br>
		0:2:3:4:5:6:7:8:9:10:11:12<br>
		</div>
	    </td>
	  </tr>
	  <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>
	  <tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> 무이자할부여부 </td>
	    <td colspan=3>
	    <input type=radio  name='allthegate_nointerest_use' id='nointerest_use_n' value='9000400001' ".CompareReturnValue("9000400001",$db->dt[allthegate_nointerest_use],"checked")."><label for='nointerest_use_n'>일반결제</label> 	    
	    <!-- <input type=radio  name='allthegate_nointerest_use' id='nointerest_use_y' value='yes' ".CompareReturnValue("yes",$db->dt[allthegate_nointerest_use],"checked")."><label for='nointerest_use_y'>무이자결제</label> -->
	    <input type=radio  name='allthegate_nointerest_use' id='nointerest_use_n' value='9000400002' ".CompareReturnValue("9000400002",$db->dt[allthegate_nointerest_use],"checked")."><label for='nointerest_use_n'>판매자부담 무이자결제</label> 
	    <br><br>
	    판매자부담 무이자결제 경우 <input type=text class='textbox' name='allthegate_nointerest_price' value='".$db->dt[allthegate_nointerest_price]."' style='width:130px;'> 원 이상부터는 무이자 할부 적용<br>
	    <div class=small style='line-height:150%'>
	    ☞ 주의) 올더게이트와 상점과의 별도 계약이 필요합니다.
	    </div>
	    </td>	    
	  </tr>
	  <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>
	  <tr bgcolor=#ffffff >
	    <td valign=top style='padding-top:15px;'><img src='../image/ico_dot2.gif' align=absmiddle > 무이자할부기간 </td>
	    <td valign=top style='padding-top:10px;'><input type=text class='textbox' name='allthegate_nointerest_str' value='".$db->dt[allthegate_nointerest_str]."' style='width:330px;'>
	    	<div class=small style='line-height:100%'>
	    	<b>☞ 주의) 올더게이트와 상점과의 별도 계약이 필요합니다.</b><br>
		무이자 할부 서비스는 다음의 카드에만 적용되므로 유의하시기 바랍니다.<br>
		<b>BC, KB(구.국민), 삼성, 엘지, 외환, 현대, 신한, 롯데</b>
		</div><br>
		<table cellpadding=4 cellspacing=1 bgcolor=gray width=200>
		<tr bgcolor=#ffffff style='font-weight:bold'><td>코드번호</td><td>카드사명</td></tr>
		<tr bgcolor=#ffffff><td>100</td><td>BC</td></tr>
		<tr bgcolor=#ffffff><td>200</td><td>국민</td></tr>
		<tr bgcolor=#ffffff><td>300</td><td>외환</td></tr>
		<tr bgcolor=#ffffff><td>400</td><td>삼성</td></tr>
		<tr bgcolor=#ffffff><td>500</td><td>LG</td></tr>
		<tr bgcolor=#ffffff><td>600</td><td>신한</td></tr>
		<tr bgcolor=#ffffff><td>800</td><td>현대</td></tr>
		<tr bgcolor=#ffffff><td>900</td><td>롯데</td></tr>
		</table>
	    </td>
	    <td align=left colspan=2 valign=top>
	    <div class=small style='line-height:150%'>
	    	예제)<br>
		<b>(1)  모든 할부거래를 무이자로 하고 싶을때에는 ALL로 설정</b><br>
		ALL<br><br>
		<b>(2) 국민카드 특정개월수만 무이자를 하고 싶을경우(2:3:4:5:6개월)</b><br>
		200-2:3:4:5:6<br><br>
		<b>(3) 외환카드 특정개월수만 무이자를 하고 싶을경우 (2:3:4:5:6개월)</b><br>
		300-2:3:4:5:6<br><br>
		<b>(4) 국민,외환카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월)</b><br>
		200-2:3:4:5:6,300-2:3:4:5:6<br><br>
		<b>(5) 무이자 할부기간 설정을 하지 않을 경우에는 NONE로 설정</b><br>
		NONE<br><br>
		<b>(6) 모든 카드사 특정개월수만 무이자를 하고 싶을 경우 샘플(2:3:6개월)</b><br>
		100-2:3:6,200-2:3:6,300-2:3:6,400-2:3:6,500-2:3:6,600-2:3:6,800-2:3:6,900-2:3:6<br><br>
		
	    ☞ 신용카드사에서 별도로 진행하는 무이자할부 이벤트는 본 무이자할부와는 상관이 없습니다. 예로 신한카드에서 2-3개월 무이지할부 이벤트를 실시할 경우 올더게이트 플러그인을 사용하시는 상점에서 고객이 신한카드 3개월 할부결제를 한다면 화면상에는 일반할부처럼 보이지만 할부이자는 청구되지 않습니다.
	    </div>
	    </td>
	  </tr>
	  <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>
	  </table>";
	  
$Contents02 = "	  
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>올더게이트 에스크로 </b> <span class=small>신용카드 결제 및 기타결제방식(에스크로 등)은 반드시 대행업체와 계약을 맺으시기 바랍니다 </span></div>")."</td>
	  </tr>	  	 
	  
	  <!-- <tr bgcolor=#ffffff >
	    <td style='width:15%;' ><img src='../image/ico_dot2.gif' align=absmiddle> Escrow 사용여부 </td><td>
	    <input type=radio  name='escrow_use' id='escrow_use_0' value='1' ".CompareReturnValue("1",$db->dt[escrow_use],"checked")."><label for='escrow_use_0'>사용</label> 
	    <input type=radio  name='escrow_use' id='escrow_use_1' value='0' ".CompareReturnValue("0",$db->dt[escrow_use],"checked")."><label for='escrow_use_1'>사용하지 않음</label>
	    </td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>	  
	  <tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> 적용금액 </td><td>
	    <input type=radio  name='escrow_apply' id='escrow_apply_0' value='0'  ".CompareReturnValue("0",$db->dt[escrow_apply],"checked")."><label for='escrow_apply_0'>10만원 이상 결제금액에 대해 적용</label> 
	    <input type=radio  name='escrow_apply' id='escrow_apply_1' value='1'  ".CompareReturnValue("1",$db->dt[escrow_apply],"checked")."><label for='escrow_apply_1'>모든 금액에 대해 적용</label>
	    </td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 class='dot-x'></td></tr> -->

	  <tr bgcolor=#ffffff >
	    <td style='width:15%;'><img src='../image/ico_dot2.gif' align=absmiddle> 결제수단 </td><td>
	    <input type=checkbox  name='escrow_method_bank' id='escrow_method_0' value='1' ".CompareReturnValue("1",$db->dt[escrow_method_bank],"checked")."><label for='escrow_method_0'>계좌이체</label> 
	    <input type=checkbox  name='escrow_method_vbank' id='escrow_method_1' value='1' ".CompareReturnValue("1",$db->dt[escrow_method_vbank],"checked")."><label for='escrow_method_1'>가상계좌</label>
	    <input type=hidden  name='escrow_method_card' value='' >
	    </td>
	    <td align=left colspan=2></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>	  	 
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

$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images//".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
	  
	  
$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='edit_form' action='allthegate.act.php' method='post' onsubmit='return validate(document.edit_form)'><input name='act' type='hidden' value='update'><input name='mall_ix' type='hidden' value='".$db->dt[mall_ix]."'>";
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
$P->Navigation = "HOME > 상점관리 > 결제모듈 설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>