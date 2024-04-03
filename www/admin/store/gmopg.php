<? 
include("../class/layout.class");



$db = new Database;


$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' limit 1");

$db->fetch();

$phone = explode("-",$db->dt[phone]);
$fax = explode("-",$db->dt[fax]);

//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);
	  

$Contents01 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	<col width='150' />
	<col width='400' />
	<col width='*' />
	  <tr>
	    <td align='left' colspan=3 > ".GetTitleNavigation("결제모듈(LGDACOM)", "상점관리 > 결제모듈(LGDACOM)")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=3> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> <b>LG DACOM 결제정보 </b> <span class=small>신용카드 결제 및 기타결제방식은 반드시 대행업체와 계약을 맺으시기 바랍니다 </span></div>")."</td>
	  </tr>";

$Contents01 .= "
	  <tr bgcolor=#ffffff >
	    <td style='width:190px;' >
			<img src='../image/ico_dot2.gif' align=absmiddle> <b>GMO PG 사이트 ID 
			<img src='".$required_path."'></b>
		</td>
		<td colspan=2>
			<input type=text class='textbox' name='gmopg_siteid' value='".$db->dt[gmopg_siteid]."' style='width:230px;' validation='true' title='사이트 ID'> 
			<span class=small><b>GMO PG</b> 에서 발급 받은 사이트 아이디를 입력해주세요</span>
		</td>
	  </tr>
	  <tr hegiht=1><td colspan=3 class='dot-x'></td></tr>

	  <tr bgcolor=#ffffff >
	    <td style='width:190px;' >
			<img src='../image/ico_dot2.gif' align=absmiddle> <b>GMO PG 사이트 PW 
			<img src='".$required_path."'></b>
		</td>
		<td colspan=2>
			<input type=text class='textbox' name='gmopg_sitepw' value='".$db->dt[gmopg_sitepw]."' style='width:230px;' validation='true' title='사이트 PW'> 
			<span class=small><b>GMO PG</b> 에서 발급 받은 사이트 비밀번호를 입력해주세요</span>
		</td>
	  </tr>
	  <tr hegiht=1><td colspan=3 class='dot-x'></td></tr>";

$Contents01 .= "	  
	  <tr bgcolor=#ffffff >
	    <td style='width:190px;' >
			<img src='../image/ico_dot2.gif' align=absmiddle> <b>GMO PG SHOP ID 
			<img src='".$required_path."'></b>
		</td>
		<td colspan=2>
			<input type=text class='textbox' name='gmopg_shopid' value='".$db->dt[gmopg_shopid]."' style='width:230px;' validation='true' title='SHOP ID'> 
			<span class=small><b>GMO PG</b> 에서 발급 받은 SHOP 아이디를 입력해주세요</span>
		</td>
	  </tr>
	  <tr hegiht=1><td colspan=3 class='dot-x'></td></tr>

	  <tr bgcolor=#ffffff >
	    <td style='width:190px;' >
			<img src='../image/ico_dot2.gif' align=absmiddle> <b>GMO PG SHOP PW 
			<img src='".$required_path."'></b>
		</td>
		<td colspan=2>
			<input type=text class='textbox' name='gmopg_shoppw' value='".$db->dt[gmopg_shoppw]."' style='width:230px;' validation='true' title='SHOP PW'> 
			<span class=small><b>GMO PG</b> 에서 발급 받은 SHOP 비밀번호를 입력해주세요</span>
		</td>
	  </tr>
	  <tr hegiht=1><td colspan=3 class='dot-x'></td></tr>";


$Contents01 .= "
	  </tr>
	  <tr hegiht=1><td colspan=3 class='dot-x'></td></tr>
	  <tr bgcolor=#ffffff >
	    <td><img src='../image/ico_dot2.gif' align=absmiddle> 일반할부기간 </td><td><input type=text class='textbox' name='gmopg_interest_str' value='".$db->dt[gmopg_interest_str]."' style='width:330px;'>
	    <div class=small><br>일반결제시에 할부기간은 2~12개월까지 가능합니다.</div></td>
	    <td align=left >
	    <div class=small> 
	    	예제) 0:2:3:4:5:6:7:8:9:10:11:12<br>
		(1) 할부기간을 일시불만 가능하도록 사용할 경우<br>
		0<br>
		(2) 할부기간을 일시불 ~ 12개월까지 사용할 경우<br>
		0:2:3:4:5:6:7:8:9:10:11:12<br>
		</div>
	    </td>
	  </tr>
	  <tr hegiht=1><td colspan=3 class='dot-x'></td></tr>
	  <tr>
		<td><img src='../image/ico_dot2.gif' align=absmiddle>사용할 서비스</td>
		<td>
		<!-- 마루찌뻬이멘토 공통은 거래 조회만 -->
			<select id='gmopg_service_module' name='gmopg_service_module'>
				<option value='tran' selected='selected'>신용 카드 거래 관련</option>
				<option value='mcard'>회원 카드 관련</option>
				<option value='1'>모바일 Suica 거래 관련</option>
				<option value='2'>모바일 Edy 거래 관련</option>
				<option value='3'>편의점 거래 관련</option>
				<option value='4'>Pay-easy 거래 관련</option>
				<option value='5'>PayPal 거래 관련</option>
				<option value='6'>WebMoney 거래 관련</option>
				<option value='7'>au 간단 결제 거래 관련</option>
				<option value='8'>도코모 휴대폰 지불 관련</option>
				<option value='9'>도코모 계속 결제 관련</option>
				<option value='10'>소프트 뱅크 휴대폰 결제 관련</option>
				<option value='11'>시문 관련</option>
				<option value='12'>au 계속 관련</option>
				<option value='13'>사기 방지 (ReDShield) 관련</option>
				<option value='14'>Magstripe 거래 관련</option>
				<option value='15'>JcbPreca 거래 관련</option>
				<option value='16'>Netcash 거래 관련</option>
				<option value='17'>RakutenId 거래 관련</option>
				<option value='18'>Mcp 거래 관련</option>
				<option value='19'>Linepay 거래 관련</option>
				<option value='20'>Unionpay 거래 관련</option>
				<option value='21'>SbContinuance 거래 관련</option>
				<option value='22'>Virtualaccount 거래 관련</option>
				<option value='23'>Recruit 거래 관련</option>
				<option value='24'>RecruitContinuance 거래 관련</option>
				<option value='25'>Recurring 거래 관련</option>
				<option value='26'>Brandtoken 거래 관련</option>
				<option value='27'>Bankaccount 거래 관련</option>
				<option value='28'>Paid 거래 관련</option>
				<option value='29'>Maillink 거래 관련</option>
			</select>
			<select id='gmopg_service_module_detail' name='gmopg_service_module_detail'>
				<option value='tran|EntryTran'>거래등록</option>
				<option value='tran|ExecTran'>결제실행</option>
				<option value='tran|EntryExecTran' selected='selected'>거래등록+결제실행</option>
				<option value='tran|AlterTran'>거래변경</option>
				<option value='tran|ChangeTran'>금액변경</option>
			</select>
		</td>
		<td>현재는 \"신용카드 거래 관련\" -> \"거래 등록 + 결제 실행\"만 가능합니다.</td>
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


$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=3 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
	  
	  
$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='edit_form' action='gmopg.act.php' method='post' onsubmit='return CheckFormValue(this)'><input name='act' type='hidden' value='update'><input name='mall_ix' type='hidden' value='".$db->dt[mall_ix]."'>";
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
$P->Navigation = "HOME > 상점관리 > 결제모듈(LGDACOM)";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>