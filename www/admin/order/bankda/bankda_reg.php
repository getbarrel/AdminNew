<?php 
/**
 * 계좌정보 페이지
 * @date 2013.09.23
 * @author bgh
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/admin/class/layout.class';
require $_SERVER['DOCUMENT_ROOT'].'/admin/order/bankda/src/bankda.php';

$bankda = new bankda();

//print_r($_SESSION["admininfo"]["ADD"]["bankda"]["sm_sdate"]);

$UserInfo = $bankda->getUserInfo();

//print_r($UserInfo);

$db = new Database;



//$postValue = $_GET;

/* 계좌정보 */
//$accountList = $bankda->getAccountList();

/* 공통 메뉴 */
//require "tabMenu.php"; 

$add_service = (array)$_SESSION["admininfo"]["ADD"]["bankda"];
$Contents .= "";

//리스트											
$Contents .= "<table cellpadding='5' border='0' style='margin-top:5px' >
<tr>
	<td height='10'><img src='/admin/images/dot_org.gif'><span style='color:#000000; font-size=11pt; font-weight:bold;'> 이용자 정보</span></td>
</tr>
</table>
<table cellpadding='1' cellspacing='1' border='0' width='100%' class='list_table_box'>
<col width='15%'>
<col width='35%'>
<col width='15%'>
<col width='35%'>
<tr>
	<td class='m_td' style='padding:5px 0;'>이용자 ID</td>
	<td class='search_box_item'>".$bankda_member_arr[bankda_userid]."</td>
	<td class='m_td' style='padding:5px 0;'>이용자 이름</td>
	<td class='search_box_item'>".$UserInfo[bankda_username]."</td>
</tr>
<tr>
	<td class='m_td' style='padding:5px 0;'>서비스 일자</td>
	<td class='search_box_item'>".$add_service["sm_sdate"]." ~ ".$add_service["sm_edate"]."</td>
	<td class='m_td' style='padding:5px 0;'>등록일</td>
	<td class='search_box_item'>".$bankda_member_arr[regdate]."</td>
</tr>
<tr>
	<td class='m_td' style='padding:5px 0;'>만료일</td>
	<td class='search_box_item'>".$bankda_array[sm_edate]."</td>

	<td class='m_td' style='padding:5px 0;'>계좌등록허용갯수</td>
	<td class='search_box_item'>".$bankda_array[service_unit_value]."</td>
</tr>
<tr>
	<td class='m_td' style='padding:5px 0;'>등록계좌수</td>
	<td class='search_box_item'>".$member_account_cnt."</td>
	<td class='m_td' style='padding:5px 0;'>잔여계좌수</td>
	<td class='search_box_item'>".$residual_cnt."</td>
</tr>
";	
//
if ($member_cnt > 0 && false){
	$Contents.="
	<tr>
		<td align='center' colspan='4' height='25'>
			<input type='button' value='[서비스 탈퇴]' onclick='FnUserAdd(3)'>&nbsp;<input type='button' value='[닫기]' onclick='FnUserAdd(0);'>
			<!--input type='button' value='[서비스 등록]' onclick='FnUserAdd(2)'>&nbsp;<input type='button' value='[닫기]' onclick='FnUserAdd(0);'-->
		</td>
	</tr>";
}else{
	/*
	$Contents.="
	<tr>
		<td align='center' colspan='4' height='25'>";
		if(!is_array($bankda_array)){
		$Contents.="
			<input type='button' value='[이용자 가입]' onclick=\"alert('무통장 자동입금확인 서비스 신청 후 사용 가능합니다.')\">&nbsp;<input type='button' value='[닫기]' onclick='FnUserAdd(0);'>";
		}else{
		$Contents.="
			<input type='button' value='[이용자 가입]' onclick='FnUserAdd(1)'>&nbsp;<input type='button' value='[닫기]' onclick='FnUserAdd(0);'>";
		}	
		$Contents.="
		</td>
	</tr>";
	*/
}
$Contents.="
</table><br><br>";

if ($member_cnt == 0){
$Contents.="
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0>
				<tr height=25>
					<td ><img src='../../images/dot_org.gif' align=absmiddle> <b class=blk>사용자 등록</b></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<div id='div_useradd' style='display:block; background:#FFFFFF'>
	<form name='addForm' action='bankda.act.php' method='post' target='act'>
	<input type='hidden' name='accea' value='1'>
	<input type='hidden' name='mall_ix' value='".$admininfo[mall_ix]."'>
	<input type='hidden' name='mall_div' value='".$admininfo[mall_div]."'>
	<input type='hidden' name='mall_domain' value='".$admin_config[mall_domain]."'>
	<input type='hidden' id='act' name='act' value='addUser'>
		<table cellpadding='1' cellspacing='1' border='0' width='100%' class='list_table_box'>
		<col width='15%'>
		<col width='*'>
		<tr height='28'>
			<td class='m_td'>이용자 ID</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_id' style='width:50%' value='".$UserInfo[bankda_userid]."'></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>이용자 비밀번호</td>
			<td class='search_box_item'><input type='password' class='textbox' name='user_pw' style='width:50%' value='".$UserInfo[bankda_userpw]."'></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>이용자이름(업체명)</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_name' style='width:50%' value='".$UserInfo[bankda_username]."'></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>전화번호</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_tel' style='width:50%' value='".$UserInfo[bankda_usertel]."'></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>이메일주소</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_email' style='width:50%' value='".$UserInfo[bankda_useremail]."'></td>
		</tr>
		</table>";

$Contents.="
		<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
		<tr><td align='center' style='padding:10px 0px;' ><a href='#;' onclick='FnUserAddWrite()'><img src='../../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></a></td></tr>
		</table>";

$Contents.="
	</form>
</div><br>";
}
$help_text ="
<table width='100%' border='0' cellpadding='3' cellspacing='0'  style=''>
	<tr height='120' >
		<td width='5%' align='left' style='margin-top:10px;'>
			<b style='margin-left:10px;'>
				[필독] 계좌등록하기 전 주의 사항!!
			</b>
			<p style='padding-left:10px;'>
				> 법인계좌 조회는 간편조회 서비스.개인계좌의 조회는 기본적으로 인터넷뱅킹ID,PW를 통해 조회합니다.</br>
				&nbsp;&nbsp;단. 일부은행의 경우. 부득이하게 다른정보를 요구할 수 있습니다.</br> 
				> 계좌번호 및 은행을 변경하시려면 계좌 삭제후 다시 등록해주시기 바랍니다.</br> 
				> 계좌 신규/수정시 DB에 반영되는 시간은 은행별로 상이하나 평균 5분 정도 기다려 주시기 바랍니다.
			</p>
		</td>
	</tr>
</table>
";

$Contents .= HelpBox("무통장 입금확인 서비스", $help_text);

/*
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0>
				<tr height=25>
					<td ><img src='../../images/dot_org.gif' align=absmiddle> <b class=blk>서비스 신청</b></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<div id='div_serviceadd' style='display:block;background:#FFFFFF'>
	<form name='addForm2' action='bankda.act.php' method='post' target='act'>
	<input type='hidden' id='act' name='act' value='addService'>
		<table cellpadding='1' cellspacing='1' border='0' width='100%' class='list_table_box'>
		<col width='35%'>
		<col width='65%'>
		<tr height='28'>
			<td class='m_td'>신청계좌수</td>
			<td class='search_box_item'><input type='text' class='textbox' name='req_accea' value='".$bankda_array[service_unit_value]."' readonly ></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>신청기간(월)</td>
			<td class='search_box_item'><input type='text' class='textbox' name='req_month' value='".$bankda_array[priod]."' readonly ></td>
		</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0'   >
		<tr><td align='center' style='padding:10px 0px;' ><input type='image' src='../../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></td></tr>
		</table>
	</form>
</div>
*/
/*
$Contents.="
<div id='div_servicedrop' style='display:block;background:#FFFFFF'>
	<form name='addForm3' action='bankda.act.php' method='post' target='act'>
	<input type='hidden' id='act' name='act' value='dropUser'>
		<table cellpadding='1' cellspacing='1' border='0' width='100%' class='list_table_box'>
		<col width='35%'>
		<col width='65%'>
		<tr height='28'>
			<td class='m_td'>서비스계정</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_id' value='".$UserInfo[bankda_userid]."' readonly ></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>계정비밀번호</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_pw' value='".$UserInfo[bankda_userpw]."' readonly ></td>
		</tr>
		</table>
		<table width='100%' cellpadding=0 cellspacing=0 border='0'   >
		<tr><td align='center' style='padding:10px 0px;' ><input type='image' src='../../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></td></tr>
		</table>
	</form>
</div>
";
*/

$Script = "
<script language='javascript' src='../../include/DateSelect.js'></script>
<script language='javascript' src='bankda.js'></script>
<script>
function deleteAccount(bkacctno){
	var select= confirm(bkacctno + ' : 해당 계좌를 정말로 삭제하시겠습니까?');
	if(select){
		eventDetail=window.open('','act','');
    	eventDetail.location='/admin/order/bankda/bankda.act.php?act=deleteAccount&bkacctno=' + bkacctno;
	}
}
</script>
";
$P = new LayOut();
$P->addScript = $Script;
$P->OnloadFunction = "";
$P->Navigation = "주문관리 > 무통장 자동입금확인";
$P->title = "계좌정보";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();