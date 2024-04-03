<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/admin/class/layout.class';
//require_once $_SERVER['DOCUMENT_ROOT'].'/admin/order/bankda/src/bankda.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/admin/order/bankda/src/bankda_new.php';

$bankda = new bankda();

$mode = $_GET['mode'];

$tokenData = $bankda->getToken();

if($tokenData['code'] != 200){
    echo "<script> alert('".$tokenData['msg']."') </script>";
    exit;
}


switch($mode){
	case "add":
		$act = "addAccount";
		$bankList = $bankda->callBankJoinInfo();
		break;
	case "mod":
		//계좌정보 수정시, 은행명과 계좌번호는 수정이 되지 않습니다.
		$act = "modifyAccount";
		$acctNo = $_GET['acctNo'];
		$acc_info = $bankda->getAccountList($acctNo);
		break;
	default:
		exit;
}

$Contents .= "
<form name='bankForm' method='post' action='bankda.act.php'>
	<input type='hidden' name='act' value='".$act."'>
	<table cellpadding='1' cellspacing='1' border='0' width='100%' class='list_table_box'>
		<col width='30%'>
		<col width='70%'>
		<tr height='28'>
			<td class='m_td'>사업자구분</td>
			<td class='search_box_item'>
				<input name='bkdiv' id='bkdiv_p' type='radio' value='P' onclick='FnBusinessClick(this.value)' checked>
					<label for='bkdiv_p'>개인</label>
				<input name='bkdiv' id='bkdiv_c' type='radio' value='C' onclick='FnBusinessClick(this.value)'>
					<label for='bkdiv_c'>법인</label>
			</td>
		</tr>
		<tr height='28'>
			<td class='m_td'>은행명</td>
			<td class='search_box_item' align='left'>";
/* 계좌 추가시 은행 목록 불러와서 보여줌 */
if($mode == 'add'){
	/* 개인용 뱅크다 은행 목록 */
	if(!empty($bankList)){
		$Contents .= "
				<select name='bkcode' id='person_bank' class='textbox'>
					<option value=''>은행을 선택하세요.</option>
			";
		foreach($bankList as $bl):
			/* 일시중지 상태 체크 */
			$_condition1 = substr_count($bl->requestinfo[1],"일시중지");
			$_condition2 = substr_count($bl['comment'],"일시중지");
			if($_condition1 > 0){
				$Contents .= "
					<option value = '".$bl['code']."'>".$bl['name'].$bl->requestinfo[1]."</option>";		
			}else if($_condition2 > 0){
				$Contents .= "
					<option value = '".$bl['code']."'>".$bl['name']."(".$bl['comment'].")</option>";
			}else{
				$Contents .= "
					<option value = '".$bl['code']."'>".$bl['name']."</option>";
			}
		endforeach;	
		$Contents .= "
				</select>";
	}
	/* 법인용 뱅크다 은행 목록 */
	if(!empty($bankList)){
		$Contents .= "
				<select name='bkcode2' id='company_bank2' class='textbox2' style='display:none;'>
					<option value=''>은행을 선택하세요.</option>
			";
		foreach($bankList as $bl):
			/* 일시중지 상태 체크 */
			$_condition1 = substr_count($bl->requestinfo[0],"일시중지");
			$_condition2 = substr_count($bl['comment'],"일시중지");
			if($_condition1 > 0){
				$Contents .= "
						<option value = '".$bl['code']."'>".$bl['name'].$bl->requestinfo[0]."</option>";
			}else if($_condition2 > 0){
				$Contents .= "
						<option value = '".$bl['code']."'>".$bl['name']."(".$bl['comment'].")</option>";
			}else{
				$Contents .= "
						<option value = '".$bl['code']."'>".$bl['name']."</option>";
			}
		endforeach;
		$Contents .= "
				</select>";
	}
}else{
	/* 수정시 은행 고정값 */
	$Contents .= "
				<input type='hidden' name='bkcode' value='".$acc_info['bkcode']."' >
				".$acc_info['Bkname'];
}
$Contents .= "
			</td>
		</tr>
		<tr height='28'>
			<td class='m_td'>계좌번호</td>
			<td class='search_box_item'>";
if($mode=="add"){
	$Contents .= "<input type='text' class='textbox' name='bkacctno' size=40>
				  <span style='margin-left:10px;' class='small blu'>- 는 빼고 입력해 주세요.</span>";
}else{
	$Contents .= "<input type='hidden' class='textbox' name='bkacctno' value='".$acc_info['Bkacctno']."' size=40>
					".$acc_info['Bkacctno'];
}
$Contents .= "
			</td>
		</tr>
		<tr height='28'>
			<td class='m_td'>계좌비밀번호</td>
			<td class='search_box_item'>
				<input type='password' class='textbox' name='bkacctpno_pw'>
		</td>
		</tr>
		<tr height='28'>
			<td class='m_td'>인터넷뱅킹 ID</td>
			<td class='search_box_item'>
				<input type='text' class='textbox' name='webid'>
				<span style='margin-left:10px;' class='small blu'>- 신한은행 등 간편조회ID,PW 가 있는 금융사는 해당정보를 입력해 주세요.</span>
			</td>
		</tr>
		<tr height='28'>
			<td class='m_td'>인터넷뱅킹 비밀번호</td>
			<td class='search_box_item'>
				<input type='password' class='textbox' name='webpw'>
			</td>
		</tr>
		<tr height='28'>
			<td class='e_td' width='300'>등록정보</td>
			<td class='search_box_item' width='300'>
				<span id='idbusiness_num_1'>
					주민번호 앞6자리 : 
					<input type='text' class='textbox' name='Mjumin_1' size='7' maxlength='6'>
					<input type='hidden' class='textbox' name='Mjumin_2' size='7' maxlength='6' value='0000000'>
				</span>
				<span id='idbusiness_num_2' style='display:none;'>
					사업자번호 : 
					<input type='text' class='textbox' name='Bjumin_1' size='4' maxlength='3'> - 
					<input type='password' class='textbox' name='Bjumin_2' size='3' maxlength='2'> - 
					<input type='password' class='textbox' name='Bjumin_3' size='6' maxlength='5'>
				</span>
			</td>
		</tr>				
	</table>
<br>
<span style='margin-left:10px;' class='small blu'>- 비밀번호, 주민번호 등의 개인정보는 쇼핑몰 서버에 저장하지 않으며, 입금자동확인 서비스 대행업체인 '뱅크다' 업체서버에 암호화되서 저장됩니다.
</span>
";
$Script = "
<script>
	function accountFrmChk(){
		var f = document.bankForm;
		if (f.bkcode.value == ''){
			alert('은행을 선택해 주세요.');
			f.bkcode.focus();
			return;
		}
		if (f.bkacctno.value == ''){
			alert('계좌번호를 입력해 주세요.');
			f.bkacctno.focus();
			return;
		}
//		if (f.webid.value == ''){
//			alert('인터넷뱅킹 ID를 입력해 주세요.');
//			f.webid.focus();
//			return;
//		}
//		if (f.webpw.value == ''){
//			alert('인터넷뱅킹 비밀번호를 입력해 주세요.');
//			f.webpw.focus();
//			return;
//		}
		if (f.bkdiv[0].checked == false && f.bkdiv[1].checked == false){
			alert('사업자구분은 선택해 주세요.');
			f.bkdiv[0].focus();
		}
		f.submit();			
	}

	// 사업자구분 선택
	function FnBusinessClick(val){
		var f = document.bankForm;
		if (f.bkdiv[0].checked == true){
			//jumin or code
			$('#idbusiness_num_1').css('display','');
			$('#idbusiness_num_2').css('display','none');
		
			//bank
			$('#person_bank').css('display','');
			$('#person_bank').attr('disable',false);
			$('#company_bank').css('display','none');
			$('#company_bank').attr('disable',true);
			$('input[name=Mjumin_1]').val('');
			$('input[name=Bjumin_1]').val(123456);
			$('input[name=Bjumin_2]').val(12);
			$('input[name=Bjumin_3]').val(12345);
		}else{
			//jumin or code
			$('#idbusiness_num_1').css('display','none');
			$('#idbusiness_num_2').css('display','');
	
			//bank
			/*
			$('#person_bank').css('display','none');
			$('#company_bank').css('display','');
			$('#company_bank').attr('disable',false);
			*/
			$('input[name=Mjumin_1]').val(123456);
			$('input[name=Bjumin_1]').val('');
			$('input[name=Bjumin_2]').val('');
			$('input[name=Bjumin_3]').val('');
		}
	}
</script>";

$Contents.="
	<table width='90%' cellpadding=0 cellspacing=0 border='0' align='left' style='float:left;'>
	 	<tr>
			<td align='center' style='padding:10px 0px;' >
				<a href='javascript:accountFrmChk();'>
					<img src='../../images/".$admininfo["language"]."/btn_reg.gif' align=absmiddle >
				</a>
			</td>
		</tr>
	</table>	
</form>";

/* laout print */
$P = new ManagePopLayOut();
$P->Navigation = "계좌추가";
$P->NaviTitle = "계좌추가";
$P->addScript = $Script;
$P->strContents = $Contents;
echo $P->PrintLayOut();
