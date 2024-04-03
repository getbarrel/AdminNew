<?
include("../class/layout.class");

$db = new Database;

//print_r($admininfo);
if($_GET["mall_ix"] == ""){
	$mall_ix = $admininfo[mall_ix];
}

$db->query("SELECT * FROM shop_payment_config where pg_code = 'billgate' and mall_ix = '".$mall_ix."' "); //and mall_ix = '".$admininfo[mall_ix]."'


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
	    <td align='left' colspan=3 > ".GetTitleNavigation("결제모듈(빌게이트)", "상점관리 > 결제모듈(빌게이트)")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>빌게이트 결제정보 </b> <span >신용카드 결제 및 기타결제방식은 반드시 대행업체와 계약을 맺으시기 바랍니다 </span></div>")."</td>
	  </tr>
	  ";
	if($_SESSION["admin_config"][front_multiview] == "Y"){
	$Contents01 .= "
	<tr>
			<td align='left' colspan=3 style='padding-bottom:17px;'>
			    <div class='tab'>
					<table class='s_org_tab' style='width:100%' border=0>
					<tr>
						<td class='tab'>
							<!--table id='tab_01' ".($mall_ix == "" ? "class='on'":"")."  >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mall_ix='\">전체보기</td>
								<th class='box_03'></th>
							</tr>
							</table-->";
//$sql = 	"SELECT * FROM shop_main_div where disp=1   ";

//$db->query($sql);
$site_infos = GetDisplayDivision($payment_config[mall_ix], "array");

for($i=0;($i < 5 && $i < count($site_infos));$i++){
	//$db->fetch($i);
$Contents01 .= "<table id='tab_".($i+2)."' ".($mall_ix == $site_infos[$i][mall_ix] ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?mall_ix=".$site_infos[$i][mall_ix]."'\">".$site_infos[$i][mall_ename]."(".$site_infos[$i][mall_domain].")</td>
								<th class='box_03'></th>
							</tr>
							</table>";
}

$Contents01 .= "
						</td>
						<td class='btn' align=right>";
                     
                        $Contents01.="
						</td>
					</tr>
					</table>
					</div>
			</td>
		</tr>";
}

$Contents01 .= "
	 </table>
	 <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>빌게이트 가맹점 코드 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'><input type=text class='textbox' name='billgate_id' value='".$payment_config[billgate_id]."' style='width:230px;' validation='true' title='가맹점 코드'> <span ><b>빌게이트</b> 에서 발급 받은 코드를 입력해주세요</span></td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>빌게이트 가맹점키 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'><input type=text class='textbox' name='billgate_key' value='".$payment_config[billgate_key]."' style='width:230px;' validation='true' title='가맹점 KEY값'> <span ><b>빌게이트</b> 에서 발급 받은 상점키를 입력해주세요</span></td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'>
			<b>결제취소 비밀번호 <img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			<input type=password class='textbox' name='cancel_pwd' value='".$payment_config["cancel_pwd"]."' style='width:230px;' validation='true' title='가맹점 KEY값'>
			<span ><b>빌게이트</b> 가맹점 관리자의 가맹점 정보메뉴에서 설정한 값을 입력해주세요.</span>
		</td>
	  </tr>
	  <!--tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>스킨타입 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<select name='SkinType' validation='true' title='스킨타입'>
				<option value='' ".($payment_config[SkinType] == "" ? "selected":"").">스킨타입선택</option>
				<option value='blue' ".($payment_config[SkinType] == "blue" ? "selected":"").">BLUE</option>
				<option value='purple' ".($payment_config[SkinType] == "purple" ? "selected":"").">PURPLE</option>
				<option value='red' ".($payment_config[SkinType] == "red" ? "selected":"").">RED</option>
				<option value='green' ".($payment_config[SkinType] == "green" ? "selected":"").">GREEN</option>
			</select>
		</td>
	  </tr-->

	   <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>빌게이트 서비스타입 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'><input type='radio' name='billgate_type' value='test' ".($payment_config[billgate_type] == "test" ? "checked":"").">TEST <input type='radio' name='billgate_type' value='service' ".($payment_config[billgate_type] == "service" ? "checked":"").">SERVICE <span ><b>빌게이트</b> 에서 테스트키이면 TEST, 실제키를 발급받았으면 SERVICE 를 선택하시기 바랍니다.</span><input type='hidden' name='billgate_type_befor' value='".$payment_config[billgate_type]."' /></td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title'> <b>일반할부기간</b> </td>
		<td class='input_box_item' style='padding:5px;'>
		<ul class='paging_area' >
			<li class='front'>
				<input type=text class='textbox' name='billgate_interest_str' value='".$payment_config[billgate_interest_str]."' style='width:300px;'><div ><br>일반결제시에 할부기간은 2~12개월까지 가능합니다.</div>
			</li>
			<li class='back' style='text-align:left'>
			<div style='line-height:140%;'>
				예제)<br>
				(1) 할부기간을 일시불만 가능하도록 사용할 경우 : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0<br>
				(2) 할부기간을 일시불 ~ 12개월까지 사용할 경우 : &nbsp;&nbsp;&nbsp;&nbsp;12<br>
				(3) 할부기간을 일시불 ~ 6개월까지 사용할 경우 : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;6<br>
				</div>
			</li>
		 </ul>
	  </td>
	  </tr>
	  <tr bgcolor=#ffffff height=54>
	    <td class='input_box_title'> <b>무이자할부여부</b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='billgate_nointerest_price' value='".$payment_config[billgate_nointerest_price]."' style='width:130px;'> 원 이상부터는 무이자 할부 적용<br>
	    <div  style='line-height:150%'>
	    ☞ 주의) 빌게이트과 상점과의 별도 계약이 필요합니다.
	    </div>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff height=34>
	    <td class='input_box_title' valign=top style='padding-top:15px;'><b>무이자할부기간</b> </td>
	    <td class='input_box_item' valign=top style='padding:5px 10px;'>
		<ul class='paging_area' >
			<li class='front'>
				<input type=text class='textbox' name='billgate_nointerest_str' style='margin:5px;' value='".$payment_config[billgate_nointerest_str]."'>
				<div  style='line-height:130%'>
				<b>☞ 주의) 빌게이트와 상점과의 별도 계약이 필요합니다.</b><br>
				무이자 할부 서비스는 다음의 카드에만 적용되므로 유의하시기 바랍니다.<br>
				<b>BC, KB(구.국민), 삼성, 엘지, 외환, 현대, 신한, 롯데</b>
				</div><br>
				<table cellpadding=6 cellspacing=1 bgcolor=gray width=700 class='list_table_box'>
				<col width='10%'/>
				<col width='25%' />
				<col width='15%' />
				<col width='10%'/>
				<col width='20%' />
				<col width='15%' />
				<tr bgcolor=#efefef style='font-weight:bold'>
					<td class='s_td'>번호</td>
					<td class='m_td'>카드사명</td>
					<td class='e_td'>코드번호</td>
					<td class='s_td'>번호</td>
					<td class='m_td'>카드사명</td>
					<td class='e_td'>코드번호</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr list_box_td'>1</td>
					<td>비씨</td>
					<td class='ctr point'>01</td>
					<td class='ctr'>2</td>
					<td>국민</td>
					<td class='ctr point'>02</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>3</td>
					<td>외환</td>
					<td class='ctr point'>03</td>
					<td class='ctr'>4</td>
					<td>삼성</td>
					<td class='ctr point'>04</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>5</td>
					<td>신한</td>
					<td class='ctr point'>06</td>
					<td class='ctr'>6</td>
					<td>현대</td>
					<td class='ctr point'>07</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>7</td>
					<td>롯데</td>
					<td class='ctr point'>08</td>
					<td class='ctr'>8</td>
					<td>한미</td>
					<td class='ctr point'>09</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>9</td>
					<td>신세계한미</td>
					<td class='ctr point'>10</td>
					<td class='ctr'>10</td>
					<td>시티</td>
					<td class='ctr point'>11</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>11</td>
					<td>농협</td>
					<td class='ctr point'>12</td>
					<td class='ctr'>12</td>
					<td>수협</td>
					<td class='ctr point'>13</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>13</td>
					<td>평화</td>
					<td class='ctr point'>14</td>
					<td class='ctr'>14</td>
					<td>우리</td>
					<td class='ctr point'>15</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>15</td>
					<td>하나</td>
					<td class='ctr point'>16</td>
					<td class='ctr'>16</td>
					<td>동남(주택)</td>
					<td class='ctr point'>17</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>17</td>
					<td>주택</td>
					<td class='ctr point'>18</td>
					<td class='ctr'>18</td>
					<td>조흥(강원)</td>
					<td class='ctr point'>19</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>19</td>
					<td>축협(농협)</td>
					<td class='ctr point'>20</td>
					<td class='ctr'>20</td>
					<td>광주</td>
					<td class='ctr point'>21</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>21</td>
					<td>전북</td>
					<td class='ctr point'>22</td>
					<td class='ctr'>22</td>
					<td>제주</td>
					<td class='ctr point'>23</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>23</td>
					<td>산은</td>
					<td class='ctr point'>24</td>
					<td class='ctr'>24</td>
					<td>해외비자</td>
					<td class='ctr point'>25</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>25</td>
					<td>해외마스터</td>
					<td class='ctr point'>26</td>
					<td class='ctr'>26</td>
					<td>해외다이너스</td>
					<td class='ctr point'>27</td>
				</tr>
				<tr bgcolor=#ffffff>
					<td class='ctr'>27</td>
					<td>해외AMX</td>
					<td class='ctr point'>28</td>
					<td class='ctr'>28</td>
					<td>해외JCB</td>
					<td class='ctr point'>29</td>
				</tr>
				</table>


			</li>
			<li class='back' style='text-align:left'>
			<div >
				<div  style='line-height:150%'>
					예제)<br>
				<b>(1) 국민카드 특정개월수만 무이자를 하고 싶을경우(2:3:4:5:6개월)</b><br>
				CCKM-2:3:4:5:6<br><br>
				<b>(2) 외환카드 특정개월수만 무이자를 하고 싶을경우 (2:3:4:5:6개월)</b><br>
				CCKE-2:3:4:5:6<br><br>
				<b>(3) 국민,외환카드 특정개월수만 무이자를 하고 싶을경우 샘플(2:3:4:5:6개월)</b><br>
				CCKM-2:3:4:5:6,CCKE-2:3:4:5:6<br><br>
				<b>(4) 무이자 할부기간 설정을 하지 않을 경우에는 0으로 설정</b><br>
				0<br><br>


				☞ 신용카드사에서 별도로 진행하는 무이자할부 이벤트는 본 무이자할부와는 상관이 없습니다. 예로 신한카드에서 2-3개월 무이지할부 이벤트를 실시할 경우 빌게이트 플러그인을 사용하시는 상점에서 고객이 신한카드 3개월 할부결제를 한다면 화면상에는 일반할부처럼 보이지만 할부이자는 청구되지 않습니다.
				</div>
			</li>
		 </ul>


	    </td>
	   </tr>
	  </table>";

$Contents02 = "
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>빌게이트 에스크로 </b> <span >신용카드 결제 및 기타결제방식(에스크로 등)은 반드시 대행업체와 계약을 맺으시기 바랍니다 </span></div>")."</td>
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
			<!-- 결제수단 선택을 단순 checkbox로 사용하면 act 파일에서 replace가 안되므로 각 결제수단마다 사용함, 사용안함으로 변경 kbk 14/04/10 -->
			계좌이체 [
			<input type=radio  name='escrow_method_bank' id='escrow_method_bank_1' value='1' ".CompareReturnValue("1",$payment_config[escrow_method_bank],"checked")."><label for='escrow_method_bank_1'>사용함</label>
			<input type=radio  name='escrow_method_bank' id='escrow_method_bank_0' value='0' ".CompareReturnValue("0",$payment_config[escrow_method_bank],"checked")."><label for='escrow_method_bank_0'>사용안함</label> ]
			&nbsp;&nbsp;&nbsp;가상계좌 [
			<input type=radio  name='escrow_method_vbank' id='escrow_method_vbank_1' value='1' ".CompareReturnValue("1",$payment_config[escrow_method_vbank],"checked")."><label for='escrow_method_vbank_1'>사용함</label>
			<input type=radio  name='escrow_method_vbank' id='escrow_method_vbank_0' value='0' ".CompareReturnValue("0",$payment_config[escrow_method_vbank],"checked")."><label for='escrow_method_vbank_0'>사용안함</label> ]
			<input type=hidden  name='escrow_method_card' value='' >
	    </td>
	  </tr>
	  </table>
	  ";

$ContentsDesc01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td ><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;' ><b> PG사와 계약을 맺은 이후에는 메일로 받으신 실제 ID 를 넣으시면 됩니다.</b> </td>
</tr>
</table>
";

$ContentsDesc02 = "
<table cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td ><img src='../image/emo_3_15.gif' align=absmiddle ></td>
	<td align=left style='padding:10px;padding-bottom:2px;' ><b> 전자상거래소비자보호법 및 시행령 개정에 따라 2006년 4월1일부터 10만원 이상 현금 결제시 의무 시행됩니다.</b> </td>
</tr>
<tr>
	<td ></td>
	<td align=left style='padding:2px;line-height:150%' >
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

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='edit_form' action='billgate.act.php' method='post' onsubmit='return CheckFormValue(this)' target='iframe_act'>
<input name='act' type='hidden' value='update'>
<input name='mall_ix' type='hidden' value='".$mall_ix."'>
<input name='pg_code' type='hidden' value='billgate'>";
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
$P->Navigation = "상점관리 > 결제관련 > 결제모듈(빌게이트)";
$P->title = "결제모듈(빌게이트)";
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