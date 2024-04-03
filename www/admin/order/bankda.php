<link rel="stylesheet" href="../css/addservice2.css" type="text/css" />
<link rel="stylesheet" href="../css/common2.css" type="text/css" />
<?
ini_set('include_path', ".:/usr/local/lib/php:".$_SERVER["DOCUMENT_ROOT"]."/include/pear");
include("../class/layout.class");
exit;
$install_path = "../../include/";

if($admininfo[admin_level] < 9){
	header("Location:../admin.php");
}

$db = new Database;
$db->dbcon = mysql_connect("118.217.181.188","forbiz","vhqlwm2011") or $db->error();
mysql_select_db("mallstory_service",$db->dbcon) or $db->error();

$arr_gubun = array("P"=>"개인", "C"=>"법인");

$max = 20;
if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

// 뱅크다 가입자 목록
$sql = "SELECT * FROM bankda_member  WHERE mall_ix = '".$admininfo[mall_ix]."' ";
$db->query($sql);
$db->fetch();
$bankda_member_arr = $db->dt;
$member_seq = $bankda_member_arr[seq];
$member_cnt = $db->total;

if ($member_seq){
	// 뱅크다 계좌 목록
	$sql = "SELECT * FROM bankda_member_bank  WHERE member_seq = ".$member_seq;	
	$db->query($sql);
	$bankda_account_arr = $db->fetchall();
	$member_account_cnt = $db->total;
	if ($member_account_cnt){
		// 뱅크다 거래 목록
		$sql = "SELECT * FROM bankda_member_bank bmb, TBLBANK bl WHERE bmb.member_seq = ".$member_seq." AND bmb.Bkacctno = bl.Bkacctno ORDER BY bl.Bkid DESC ";
		$db->query($sql);
		$list_cnt = $db->total;

		$sql = "SELECT * FROM bankda_member_bank bmb, TBLBANK bl WHERE bmb.member_seq = ".$member_seq." AND bmb.Bkacctno = bl.Bkacctno ORDER BY bl.Bkid DESC limit $start,$max ";
		$db->query($sql);
		$bankda_list_arr = $db->fetchall();	
	}
}

// 뱅크다 거래가능은행 목록 : xml 
$xml_url = "http://www.bankda.com/bankda_requestinfo.xml";
$xml_string = file_get_contents($xml_url);
$xml = simplexml_load_string($xml_string);

$select_bank.= "<select name='sel_bank' class='textbox'><option value=''>==은행선택==</option>";
for ($i=0; $i<count($xml->bank); $i++){
	if (strstr($xml->bank[$i]->requestinfo[1], "일시중지")){
		$select_bank.= "<option value='".$xml->bank[$i][code]."'>".$xml->bank[$i][name]." ".$xml->bank[$i]->requestinfo[1]."</option>";
	}else{
		$select_bank.= "<option value='".$xml->bank[$i][code]."'>".$xml->bank[$i][name]."</option>";
	}
	
}
$select_bank.= "</select>";


$pagestring = page_bar($list_cnt, $page, $max, "","");

$Script.="
<SCRIPT LANGUAGE='JavaScript'>
<!--
	function FnBankWrite(){
		var f = document.bankForm;
		if (f.sel_bank.value == ''){
			alert('은행을 선택해 주세요.');
			f.sel_bank.focus();
			return;
		}
		if (f.Bkacctno.value == ''){
			alert('계좌번호를 입력해 주세요.');
			f.Bkacctno.focus();
			return;
		}
		if (f.Bkjukyo.value == ''){
			alert('예금주를 입력해 주세요.');
			f.Bkjukyo.focus();
			return;
		}
		if (f.BkinternetId.value == ''){
			alert('인터넷뱅킹 ID를 입력해 주세요.');
			f.BkinternetId.focus();
			return;
		}
		if (f.BkinternetPw.value == ''){
			alert('인터넷뱅킹 비밀번호를 입력해 주세요.');
			f.BkinternetPw.focus();
			return;
		}
		if (f.business_gubun[0].checked == false && f.business_gubun[1].checked == false){
			alert('사업자구분은 선택해 주세요.');
			f.business_gubun[0].focus();
		}

		var bkdiv;
		if (f.business_gubun[0].checked == true){
			bkdiv = 'P';
		}else if (f.business_gubun[1].checked == true){
			bkdiv = 'C';
		}
		f.submit();			
	}

	// 사업자구분 선택
	function FnBusinessClick(val){
		var f = document.bankForm;
		if (f.business_gubun[0].checked == true){
			document.getElementById('idbusiness_num_1').style.display = '';
			document.getElementById('idbusiness_num_2').style.display = 'none';
		}else{
			document.getElementById('idbusiness_num_1').style.display = 'none';
			document.getElementById('idbusiness_num_2').style.display = '';
		}
	}

	// 사업자구분 선택 (수정폼)
	function FnBusinessClick_editmode(val){
		var f = document.bankUpdForm;
		if (f.business_gubun[0].checked == true){
			document.getElementById('idbusiness_num_1_editmode').style.display = '';
			document.getElementById('idbusiness_num_2_editmode').style.display = 'none';
		}else{
			document.getElementById('idbusiness_num_1_editmode').style.display = 'none';
			document.getElementById('idbusiness_num_2_editmode').style.display = '';
		}
	}

	// 뱅크다 이용자 등록 레이어
	function FnUserAdd(flag){
		if (flag==1){
			document.getElementById('div_useradd').style.display = '';
		}else{
			document.getElementById('div_useradd').style.display = 'none';
		}
	}

	// 뱅크다 이용자 등록 처리
	function FnUserAddWrite(){
		var f = document.addForm;
		if (f.user_id.value == ''){
			alert('이용자 ID를 입력해 주세요.');
			f.user_id.focus();
			return;
		}
		if (f.user_pw.value == ''){
			alert('이용자 비밀번호를 입력해 주세요.');
			f.user_pw.focus();
			return;
		}
		if (f.user_name.value == ''){
			alert('이용자이름(업체명)을 입력해 주세요.');
			f.user_name.focus();
			return;
		}
		if (f.user_tel.value == ''){
			alert('전화번호를 입력해 주세요.');
			f.user_tel.focus();
			return;
		}
		if (f.user_email.value == ''){
			alert('메일주소를 입력해 주세요.');
			f.user_email.focus();
			return;
		}
		f.submit();
	}
 
	// 입금확인처리
	function FnOrderStatusUpd(no, bkname, bkinput, bkdate, bkjukyo){
		var f = document.updForm;
		f.no.value = no;
		f.bkname.value = bkname;
		f.bkinput.value = bkinput;
		f.bkdate.value = bkdate;
		f.bkjukyo.value = bkjukyo;
		f.submit();
	}
	
	// 계좌삭제
	function FnBankDel(Bkacctno){
		var f = document.updForm;
		f.act.value = 'bankDel';
		f.Bkacctno.value = Bkacctno;
		f.submit();
	}

	// 이용자삭제
	function FnUserDel(userid,userpw){
		var f = document.userForm;
		f.user_id.value = userid;
		f.user_pw.value = userpw;
		f.submit();
	}

	// 계좌수정 레이어
	function FnBankMod(member_bank_seq, Bkacctno){
		if (document.getElementById('div_bankupd').style.display == 'none'){
			document.getElementById('div_bankupd').style.display = '';
		}else{
			document.getElementById('div_bankupd').style.display = 'none';
		}
		
		var f = document.bankUpdForm;
		f.Bkacctno.value = Bkacctno;
		f.member_bank_seq.value = member_bank_seq;		
	}

	// 계좌수정처리
	function FnBankUpdSubmit(){
		var f = document.bankUpdForm;		
		if (f.sel_bank.value == ''){
			alert('은행을 선택해 주세요.');
			f.sel_bank.focus();
			return;
		}
		if (f.Bkacctno.value == ''){
			alert('계좌번호를 입력해 주세요.');
			f.Bkacctno.focus();
			return;
		}
		if (f.Bkjukyo.value == ''){
			alert('예금주를 입력해 주세요.');
			f.Bkjukyo.focus();
			return;
		}
		if (f.BkinternetId.value == ''){
			alert('인터넷뱅킹 ID를 입력해 주세요.');
			f.BkinternetId.focus();
			return;
		}
		if (f.BkinternetPw.value == ''){
			alert('인터넷뱅킹 비밀번호를 입력해 주세요.');
			f.BkinternetPw.focus();
			return;
		}
		if (f.business_gubun[0].checked == false && f.business_gubun[1].checked == false){
			alert('사업자구분은 선택해 주세요.');
			f.business_gubun[0].focus();
		}

		var bkdiv;
		if (f.business_gubun[0].checked == true){
			bkdiv = 'P';
		}else if (f.business_gubun[1].checked == true){
			bkdiv = 'C';
		}
		f.submit();			
		f.submit();

	}
//-->
</SCRIPT>
";

$Contents="

<div id='contents' style='padding:10px;'>
<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_04' ".(($list_type == "income_list" || $list_type == "") ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02 blk' onclick=\"document.location.href='?list_type=income_list'\">계좌 입금목록</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_01' ".(($list_type == "userinfo") ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02 blk' onclick=\"document.location.href='?list_type=userinfo'\">이용자정보</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_02' ".(($list_type == "reg_account_infos") ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02 blk' onclick=\"document.location.href='?list_type=reg_account_infos'\">등록계좌 목록</td>
									<th class='box_03'></th>
								</tr>
							</table>
							<table id='tab_03' ".($list_type == "reg_account" ? "class='on'":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02 blk' onclick=\"document.location.href='?list_type=reg_account'\">계좌 신규등록</td>
									<th class='box_03'></th>
								</tr>
							</table>
							
						</td>
					</tr>
				</table>
			</div>";
//if($list_type == "useradd"){
$Contents .= " 
<div id='div_useradd' style='display:none; position:absolute; top:100px; left:400px; z-index:99; border:3px solid #000000; background:#FFFFFF'>
	<form name='addForm' action='bankda.act.php' method='post'>
	<input type='hidden' name='accea' value='1'>
	<input type='hidden' name='act' value='user_add'>
		<table cellpadding='1' cellspacing='1' border='0' width='500' class='list_table_box'>
		<col width='35%'>
		<col width='65%'>
		<tr height='28'>
			<td class='m_td'>이용자 ID</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_id'></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>이용자 비밀번호</td>
			<td class='search_box_item'><input type='password' class='textbox' name='user_pw'></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>이용자이름(업체명)</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_name'></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>전화번호</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_tel'></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>이메일주소</td>
			<td class='search_box_item'><input type='text' class='textbox' name='user_email'></td>
		</tr>
		</table>
		<table width='500' cellpadding=0 cellspacing=0 border='0' align='left' style='float:left;'>
		<tr><td align='center' style='padding:10px 0px;' ><a href='#;' onclick='FnUserAddWrite()'><img src='../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></a></td></tr>
		</table>
	</form>
</div>";
//}
if($list_type == "reg_account_infos"){
$Contents .= " 
<!-- 계좌수정 레이어 -->
<div id='div_bankupd' style='display:none; position:absolute; top:100px; left:500px; width:800px; height:400px; z-index:99; border:3px solid #000000; background:#FFFFFF'>
<table cellpadding='10' border='0' style='margin-top:10px' >
<tr>
	<td height='30'><img src='/admin/images/dot_org.gif'><span style='color:#000000; font-size=11pt; font-weight:bold;'> 계좌 수정</span></td>
</tr>
</table>
<form name='bankUpdForm' method='post' action='bankda.act.php'>
<input type='hidden' name='act' value='bankUpd'>
<input type='hidden' name='member_seq' value='".$member_seq."'>
<input type='hidden' name='member_bank_seq' value=''>
<table cellpadding='1' cellspacing='1' border='0' width='70%' class='list_table_box'>
<col width='30%'>
<col width='70%'>
<tr height='28'>
	<td class='m_td'>은행명</td>
	<td class='search_box_item' align='left'>".$select_bank."</td>
</tr>
<tr height='28'>
	<td class='m_td'>계좌번호</td>
	<td class='search_box_item'><input type='text' class='textbox' name='Bkacctno' size=40><span style='margin-left:10px;' class='small blu'>- 는 빼고 입력해 주세요.</span></td>
</tr>
<tr height='28'>
	<td class='m_td'>예금주</td>
	<td class='search_box_item'><input type='text' class='textbox' name='Bkjukyo'></td>
</tr>
<tr height='28'>
	<td class='m_td'>계좌비밀번호</td>
	<td class='search_box_item'><input type='password' class='textbox' name='Bkpass'></td>
</tr>
<tr height='28'>
	<td class='m_td'>인터넷뱅킹 ID</td>
	<td class='search_box_item'><input type='text' class='textbox' name='BkinternetId'><span style='margin-left:10px;' class='small blu'>- 신한은행 등 간편조회ID,PW 가 있는 금융사는 해당정보를 입력해 주세요.</span>
	</td>
</tr>
<tr height='28'>
	<td class='m_td'>인터넷뱅킹 비밀번호</td>
	<td class='search_box_item'><input type='password' class='textbox' name='BkinternetPw'></td>
</tr>
<tr height='28'>
	<td class='m_td'>사업자구분</td>
	<td class='search_box_item'>".makeRadioTag($arr_gubun, "business_gubun", false, "onclick='FnBusinessClick_editmode(this.value)'", false)."</td>
</tr>
<tr height='28'>
	<td class='e_td' width='300'>등록정보</td>
	<td class='search_box_item' width='300'>
	<span id='idbusiness_num_1_editmode'>
	주민번호 : <input type='text' class='textbox' name='jumin1' size='7' maxlength='6'> - <input type='password' class='textbox' name='jumin2' size='8' maxlength='7'>
	</span>
	<span id='idbusiness_num_2_editmode' style='display:none;'>
	사업자번호 : <input type='text' class='textbox' name='business_num1' size='4' maxlength='3'> - <input type='password' class='textbox' name='business_num2' size='3' maxlength='2'> - <input type='password' class='textbox' name='business_num3' size='6' maxlength='5'>
	</span>
	</td>
</tr>				
</table>
<br><span style='margin-left:10px;' class='small blu'>- 비밀번호, 주민번호 등의 개인정보는 쇼핑몰 서버에 저장하지 않으며, 입금자동확인 서비스 대행업체인 '뱅크다' 업체서버에 암호화되서 저장됩니다.</span>
<table width='500' cellpadding=0 cellspacing=0 border='0' align='left' style='float:left;'>
	<tr><td align='center' style='padding:10px 0px;' ><a href='#;' onclick='FnBankUpdSubmit()'><img src='../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></a></td></tr>
	</table>
</form>
</div>";
}
if($list_type == "userinfo"){
		$Contents .= " 

		<table cellpadding='10' border='0' style='margin-top:10px' >
		<tr>
			<td height='30'><img src='/admin/images/dot_org.gif'><span style='color:#000000; font-size=11pt; font-weight:bold;'> 이용자 정보</span></td>
		</tr>
		</table>
		<table cellpadding='1' cellspacing='1' border='0' width='30%' class='list_table_box'>
		<col width='30%'>
		<col width='50%'>
		<col width='20%'>
		<tr>
			<td class='m_td' style='padding:5px 0;'>이용자 ID</td>
			<td class='search_box_item'>".$bankda_member_arr[bankda_userid]."</td>
		</tr>
		<tr>
			<td class='m_td' style='padding:5px 0;'>등록일</td>
			<td class='search_box_item'>".$bankda_member_arr[regdate]."</td>
		</tr>
		";	
		if ($member_cnt > 0){
			$Contents.="
			<tr>
				<td align='center' colspan='2' height='25'>등록완료 <input type='button' value='이용자 삭제' onclick=\"FnUserDel('".$bankda_member_arr[bankda_userid]."','".$bankda_member_arr[bankda_userpw]."')\" style='background:#eeeeee'></td>
			</tr>";
		}else{
			$Contents.="
			<tr>
				<td align='center' colspan='2' height='25'><input type='button' value='[이용자 가입]' onclick='FnUserAdd(1)'>&nbsp;<input type='button' value='[닫기]' onclick='FnUserAdd(0);'></td>
			</tr>";
		}
		$Contents.="
		</table>";
}
if($list_type == "reg_account_infos"){
		$Contents .= " 
		<br>
		<table cellpadding='10' border='0' style='margin-top:10px' >
		<tr>
			<td height='30'><img src='/admin/images/dot_org.gif'><span style='color:#000000; font-size=11pt; font-weight:bold;'> 등록계좌 목록</span></td>
		</tr>
		</table>
		<table cellpadding='1' cellspacing='1' border='0' width='70%' class='list_table_box'>
		<col width='5%'>
		<col width='15%'>
		<col width='20%'>
		<col width='20%'>
		<col width='10%'>
		<col width='10%'>
		<tr>
			<td class='s_td' style='padding:5px 0;'>번호</td>	
			<td class='m_td'>은행명</td>
			<td class='m_td'>계좌번호</td>
			<td class='m_td'>신청일</td>	
			<td class='e_td'>계좌삭제</td>
		</tr>
		";
			for ($i=0; $i<$member_account_cnt; $i++){
				for ($j=0; $j<count($xml->bank); $j++){
					if ($xml->bank[$j][code] == $bankda_account_arr[$i]['bank_code']){
						$bank_name = $xml->bank[$j][name];
					}
				}
				$Contents.="
				<tr height='30'>
					<td class='list_box_td' style='padding:5px 0;'>".($i+1)."</td>						
					<td class='list_box_td'>".$bank_name."</td>
					<td class='list_box_td'>".$bankda_account_arr[$i]['Bkacctno']."&nbsp;<input type='button' value='계좌수정' onclick=\"FnBankMod('".$bankda_account_arr[$i]['seq']."', '".$bankda_account_arr[$i]['Bkacctno']."')\" style='background:#eeeeee'> </td>
					<td class='list_box_td'>".$bankda_account_arr[$i]['regdate']."</td>
					<td class='list_box_td'><input type='button' value='계좌삭제' onclick=\"FnBankDel('".$bankda_account_arr[$i]['Bkacctno']."')\" style='background:#eeeeee'></td>			
				</tr>";
			}
			if ($member_account_cnt == 0){
				$Contents.="
				<tr>
					<td align='center' colspan='6' height='25'>내역이 없습니다. </td>
				</tr>";
			}
		$Contents.="
		</table>";
}
if($list_type == "reg_account"){
		$Contents .= " 
		<br>
		<table cellpadding='10' border='0' style='margin-top:10px' >
		<tr>
			<td height='30'><img src='/admin/images/dot_org.gif'><span style='color:#000000; font-size=11pt; font-weight:bold;'> 계좌 신규등록</span></td>
		</tr>
		</table>
		<form name='bankForm' method='post' action='bankda.act.php'>
		<input type='hidden' name='act' value='insert'>
		<input type='hidden' name='member_seq' value='".$member_seq."'>
		<table cellpadding='1' cellspacing='1' border='0' width='70%' class='list_table_box'>
		<col width='30%'>
		<col width='70%'>
		<tr height='28'>
			<td class='m_td'>은행명</td>
			<td class='search_box_item' align='left'>".$select_bank."</td>
		</tr>
		<tr height='28'>
			<td class='m_td'>계좌번호</td>
			<td class='search_box_item'><input type='text' class='textbox' name='Bkacctno' size=40><span style='margin-left:10px;' class='small blu'>- 는 빼고 입력해 주세요.</span></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>예금주</td>
			<td class='search_box_item'><input type='text' class='textbox' name='Bkjukyo'></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>계좌비밀번호</td>
			<td class='search_box_item'><input type='password' class='textbox' name='Bkpass'></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>인터넷뱅킹 ID</td>
			<td class='search_box_item'><input type='text' class='textbox' name='BkinternetId'><span style='margin-left:10px;' class='small blu'>- 신한은행 등 간편조회ID,PW 가 있는 금융사는 해당정보를 입력해 주세요.</span>
			</td>
		</tr>
		<tr height='28'>
			<td class='m_td'>인터넷뱅킹 비밀번호</td>
			<td class='search_box_item'><input type='password' class='textbox' name='BkinternetPw'></td>
		</tr>
		<tr height='28'>
			<td class='m_td'>사업자구분</td>
			<td class='search_box_item'>".makeRadioTag($arr_gubun, "business_gubun", false, "onclick='FnBusinessClick(this.value)'", false)."</td>
		</tr>
		<tr height='28'>
			<td class='e_td' width='300'>등록정보</td>
			<td class='search_box_item' width='300'>
			<span id='idbusiness_num_1'>
			주민번호 : <input type='text' class='textbox' name='jumin1' size='7' maxlength='6'> - <input type='password' class='textbox' name='jumin2' size='8' maxlength='7'>
			</span>
			<span id='idbusiness_num_2' style='display:none;'>
			사업자번호 : <input type='text' class='textbox' name='business_num1' size='4' maxlength='3'> - <input type='password' class='textbox' name='business_num2' size='3' maxlength='2'> - <input type='password' class='textbox' name='business_num3' size='6' maxlength='5'>
			</span>
			</td>
		</tr>				
		</table>
		<br><span style='margin-left:10px;' class='small blu'>- 비밀번호, 주민번호 등의 개인정보는 쇼핑몰 서버에 저장하지 않으며, 입금자동확인 서비스 대행업체인 '뱅크다' 업체서버에 암호화되서 저장됩니다.</span>
		";


		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
			$Contents.= "<table width='90%' cellpadding=0 cellspacing=0 border='0' align='left' style='float:left;'>";
			$Contents.= " <tr><td align='center' style='padding:10px 0px;' ><a href='#;' onclick='FnBankWrite()'><img src='../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle ></a></td></tr>";
			$Contents.= "</table>";
		}
		$Contents.="
		</form>";
}
if($list_type == "income_list" || $list_type == ""){

			$Contents.="<br><br><br>
			<table cellpadding='10' border='0' style='float:left;' align='left' width='100%'>
			<tr>
				<td height='30' align='left'><img src='/admin/images/dot_org.gif'><span style='color:#000000; font-size=11pt; font-weight:bold;'> 계좌 입금목록</span></td>
			</tr>
			</table>
			<table cellpadding='1' cellspacing='1' border='0' width='100%' class='list_table_box' align='left' style=' float:left;'>
			<col width='50'>
			<col width='100'>
			<col width='100'>
			<col width='100'>
			<col width='100'>
			<col width='100'>
			<col width='100'>
			<col width='100'>
			<col width='100'>
			<tr>
				<td class='s_td' style='padding:5px 0;'>번호</td>
				<td class='m_td'>거래일자</td>
				<td class='m_td'>입금자명</td>
				<td class='m_td'>은행명</td>
				<td class='m_td'>계좌번호</td>
				<td class='m_td'>내용</td>
				<td class='m_td'>입금액</td>
				<td class='m_td'>출금액</td>
				<td class='m_td'>잔액</td>	
				<td class='e_td'>주문상태변경</td>
			</tr>
			";
			for ($i=0; $i<count($bankda_list_arr); $i++){
				$no = $list_cnt - $i;
				//$bank_date = $bankda_list_arr[$i][Bkxferdatetime];
				$bank_date = $bankda_list_arr[$i][Bkdate];
				//$bank_date = getDateList($bank_date);

				$oid = $bankda_list_arr[$i][oid];


				if (!$oid)	 $btn_status = "<input type='button' value='입금확인처리' onclick=\"FnOrderStatusUpd(".$bankda_list_arr[$i][Bkid].", '".$bankda_list_arr[$i][Bkname]." ".$bankda_list_arr[$i][Bkacctno]."', ".$bankda_list_arr[$i][Bkinput].", '".$bankda_list_arr[$i][regdate]."', '".$bankda_list_arr[$i][Bkjukyo]."')\" style='background:#eeeeee'>";
				if ($oid)	 $btn_status = "<a href='/admin/order/orders.read.php?oid=".$oid."' target='_blank'><u>".$oid."</u></a>";

				
				
				$Contents.="
				<tr align='center' height='25'>
					<td >".$no."</td>		
					<td >".$bank_date."</td>
					<td >".$bankda_list_arr[$i][Bkjukyo]."</td>
					<td >".$bankda_list_arr[$i][Bkname]."</td>
					<td >".$bankda_list_arr[$i][Bkacctno]."</td>
					<td >".$bankda_list_arr[$i][Bkcontent]."</td>
					<td >".number_format($bankda_list_arr[$i][Bkinput])."</td>
					<td >".number_format($bankda_list_arr[$i][Bkoutput])."</td>
					<td >".number_format($bankda_list_arr[$i][Bkjango])."</td>		
					<td >".$btn_status."</td>		
				</tr>
				";
			}
			if ($list_cnt == 0){
				$Contents.="
				<tr>
					<td align='center' colspan='10' height='25'>내역이 없습니다. </td>
				</tr>

				";
			}else{
				$Contents.="
				<tr>
					<td align='center' colspan='10' height='25'>
					<ul class='paging_area' >
						<li class='front'>".$pagestring."</li>
						<li class='back'></li>
					  </ul>
					</td>
				</tr>	
				";
			}

			$Contents.="</table><br>
			<form name='updForm' method='post' action='bankda.act.php'>
			<input type='hidden' name='act' value='statusUpd'>
			<input type='hidden' name='no'>
			<input type='hidden' name='bkname'>
			<input type='hidden' name='bkinput'>
			<input type='hidden' name='bkdate'>
			<input type='hidden' name='bkjukyo'>
			<input type='hidden' name='Bkacctno'>
			</form>

			<!-- 이용자삭제 임시용 -->
			<form name='userForm' method='post' action='bankda.act.php'>
			<input type='hidden' name='act' value='userDel'>
			<input type='hidden' name='user_id' value=''>
			<input type='hidden' name='user_pw' value=''>
			</form>

			<!-- 삭제 임시용 -->
			<!--
			<form name='delForm' method='post' action='bankda.act.php'>
			<input type='hidden' name='act' value='bankDel'>
			<input type='hidden' name='testmode' value='Y'>
			<input type='hidden' name='bankda_userid' value='baekop'>
			<input type='hidden' name='bankda_userpw' value='baekop00'>
			<input type='hidden' name='Bkacctno' value='110387892586'>
			<input type='button' value='계좌삭제' onclick=\"document.delForm.submit();\" style='background:#eeeeee'>
			</form>
			-->
			</div>
			";
}
/*
<Script>
FnUserDel('hidejj','1212');
</script>
*/

//$Contents = "<div style=height:1000px;'></div>";

if($admininfo[mall_type] == "H"){
	$Contents = str_replace("쇼핑몰","사이트",$Contents);
}

$P = new LayOut();
$P->addScript = $Script;
if($view_type == "sellertool"){
	$P->strLeftMenu = sellertool_menu();
}else{
	$P->strLeftMenu = order_menu();
}
$P->Navigation = "주문관리 > 무통장입금 자동확인";
$P->title = "무통장입금 자동확인";
$P->strContents = $Contents;
echo $P->PrintLayOut();

// radio tag 생성
if(!function_exists("makeRadioTag")){
	function makeRadioTag($array, $name, $select_value=false, $onclick=false, $key_is_value = false){
		if (is_array($array)){
			foreach ($array as $key => $val){
				if ($key_is_value == "Y"){
					if ($select_value == $val)		$str.="<input name='".$name."' type='radio' value='".$val."' checked ".$onclick.">&nbsp;".$val;
					else									$str.="<input name='".$name."' type='radio' value='".$val."' ".$onclick.">&nbsp;".$val;
				}else{
					if ($select_value == $key)		$str.="<input name='".$name."' type='radio' value='".$key."' checked ".$onclick.">&nbsp;".$val;
					else									$str.="<input name='".$name."' type='radio' value='".$key."' ".$onclick.">&nbsp;".$val;
				}
				$str.="&nbsp;";
			}		
			return $str;
		}
	}
}
// 12자리 날짜,시간,분 목록표시용 처리
if(!function_exists("getDateList")){
	function getDateList($date){
		$date = substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)." ".substr($date,8,2).":".substr($date,10,2);
		return $date;
	}
}
?>