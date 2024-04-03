<?
include("../class/layout.class");
//include("./mail.config.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

$db = new Database;
$sms_design = new SMS;
$db->query("SELECT * FROM shop_sms_box where sms_ix= '$sms_ix'");
$db->fetch();

if($db->total){
	$sms_ix = $db->dt[sms_ix];
	$sms_title = $db->dt[sms_title];
	$sms_group = $db->dt[sms_group];
	$sms_text = $db->dt[sms_text];
	$sms_type = $db->dt[sms_type];
	$sms_code = $db->dt[sms_code];
	$regdate = $db->dt[regdate];
	$disp = $db->dt[disp];
	$act = "update";
}else{
	$act = "insert";
	$mail_use_sdate = "";
	$mail_use_edate = "";
	$disp = "1";
}
$Script = "
<Script Language='JavaScript'>
function SubmitX(frm){
	
	if(frm.sms_code.value==''){
		alert('문자코드를 입력해주세요');
		frm.sms_code.focus();
		return false;
	}else if(frm.sms_title.value==''){
		alert('문자코드를 입력해주세요');
		frm.sms_title.focus();
		return false;
	}else if(frm.sms_text.value==''){
		alert('문자코드를 입력해주세요');
		frm.sms_text.focus();
		return false;
	}else{
		return true;
	}
}

function SendMailCheck(frm){
	if(frm.mt_ix.value == ''){
		alert('타겟군을 선택해주세요');
		return false;
	}

	frm.mail_content.value = iView.document.body.innerHTML;
	return true;
}



function init(){

}


function clearAll(frm){
		for(i=0;i < frm.mh_ix.length;i++){
				frm.mh_ix[i].checked = false;
		}
}
function checkAll(frm){
       	for(i=0;i < frm.mh_ix.length;i++){
				frm.mh_ix[i].checked = true;
		}
}
function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;

	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}



function Selectdelete(frm){
	var check_bool = false;


		for(i=0;i < frm.mh_ix.length;i++){
			if(frm.mh_ix[i].checked){
				check_bool = true	;
			}
		}

		if(!check_bool){
			alert('한명 이상의 회원이 선택되어야 합니다');
			return false;
		}

		return true;

}

function deleteMailHistory(act, mh_ix){
	if(confirm('해당 메일목록을 정말 삭제하시겠습니까? '))
	{
		document.frames['iframe_act'].location.href= 'mail.act.php?act=history_delete&mh_ix='+mh_ix;
		//
	}
}


</Script>";



$Contents = "
<table width='100%' border='0' align='left' >
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("SMS작성", "메일링/SMS > SMS작성 ")."</td>
</tr>
<tr>
    <td>
        <form name='INPUT_FORM' method='post' onSubmit=\"return SubmitX(this)\" action='sms.act.php'>
			<input type='hidden' name=act value='$act'>
			<input type='hidden' name=sms_ix value='$sms_ix'>
			<input type=hidden name=mmode value=$mmode></input>
		<table border='0' cellpadding=3 cellspacing=0 width='100%' class='input_table_box' >
		";
		if($total){
			$Contents .= "
		  <tr height=28>
			<td class='input_box_title' width='20%' nowrap>등록일자</td>
			<td class='input_box_item' style='padding:0px 20px 0px 5px;' colspan=3>
				".$regdate."
			</td>
		  </tr>
		 ";
		}
		$Contents .= "
		  <tr height=28>
			<td class='input_box_title' width='20%' nowrap>발송구분</td>
			<td class='input_box_item' style='padding:0px 20px 0px 5px;'>
				<select name='sms_type'>
					<option value='0'".($sms_type == '0' ? "selected" : '').">마케팅</option>
					<option value='1'".($sms_type == '1' ? "selected" : '').">프로모션</option>
				</select>
			</td>
			<td class='input_box_title' width='20%' nowrap>문자그룹</td>
			<td class='input_box_item' style='padding:0px 20px 0px 5px;'>
				<select name='sms_group' style='width:100px'>
					<option value='A'".($sms_group == 'A' ? "selected" : '').">A</option>
					<option value='B'".($sms_group == 'B' ? "selected" : '').">B</option>
					<option value='C'".($sms_group == 'C' ? "selected" : '').">C</option>
					<option value='D'".($sms_group == 'D' ? "selected" : '').">D</option>
					<option value='E'".($sms_group == 'E' ? "selected" : '').">E</option>
				</select>
			</td>
		  </tr>
		  <tr height=28>
			<td class='input_box_title' width='20%' nowrap>문자코드</td>
			<td class='input_box_item' style='padding:0px 20px 0px 5px;'>
				<input type='text' name='sms_code' value='".$db->dt[sms_code]."' class='textbox' validation=true maxlength='50' style='width:200px;'>
			</td>
			<td class='input_box_title'>사용여부 </td>
			<td class='input_box_item'>
				<input type='hidden' name='pop' value='1'>
					<input type='radio' name='disp' id='disp_1' value='1' ".CompareReturnValue("1",$disp,"checked")."> <label for='disp_1' >사용(O)</label> 
					<input type='radio' name='disp' id='disp_0' value='0' ".CompareReturnValue("0",$disp,"checked")."><label for='disp_0' >사용(X)</label>
		  </tr>
		  <tr height=28>
			<td class='input_box_title' width='20%' nowrap>문자제목</td>
			<td class='input_box_item' colspan='3'style='padding:0px 20px 0px 5px;'><input type='text' name='sms_title' value='".$db->dt[sms_title]."' class='textbox' validation=true maxlength='50' style='width:400px'></td>
		  </tr>
		  <tr bgcolor='#F8F9FA'>
			<td colspan=4>
			 <table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
				<tr>
					<td>
						<table class='box_shadow' style='width:202px;height:120px;' cellpadding=0 cellspacing=0>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05'  valign=top>
									<table cellpadding=0 cellspacing=0><!--CheckSpecialChar(this);-->
									<tr>
										<td style='padding-left:5px;'>
											<textarea style='width:200px;height:300px;background-color:#fff;border:10px solid #efefef;padding:2px;overflow:hidden;' name='sms_text' onkeyup=\"fc_chk_lms(this,80,1000, this.form.sms_text_count);\" >".$db->dt['sms_text']."</textarea>
										</td>
									</tr>
									<tr>
										<td height=20 align=right><b id='msg_type'>SMS</b><input type=text name='sms_text_count' style='display:inline;border:0px;text-align:right' size=3 value=0> byte/<span id='byte'>80byte</span><span id='lms_type'></span></td>
									</tr>
									</table>

								</td>
								<th class='box_06'></th>
							</tr>
							<tr>
								<th class='box_07'></th>
								<td class='box_08'></td>
								<th class='box_09'></th>
							</tr>
						</table>
					</td>
					<td valign=top style='padding:0 0 0 10px'>
						<table cellpadding=0 cellspacing=0 style='width:202px;'>
							<tr height=26>
								<td style='width:200px;height:200px;border:1px solid #ccc;background-color:#efefef'>
									<span style='border-bottom:1px solid #ccc'>자주쓰는 소스설명</span>
								</td>
							</tr>
							<tr height=26>
								<td>
									<input type='checkbox' name='0' value='sms_code' id='sms_code_use'><label for='sms_code_use'><b>분석코드 사용<br />클릭/유입/매출분석사용</b></label></input>
								</td>
							</tr>
							<tr height=26>
								<td>* {}사이트 URL의 별도의 코드가 추가되어 유입/로그인/회원가입/매출액 등 상세 분석이 가능합니다.</td>
							</tr>
							<tr height=26>
								<td>* 사용시 SMS*2건 처리됩니다.</td>
							</tr>
							<tr height=26>
								<td>* 사용시 LMS*3건 처리됩니다.</td>
							</tr>
						</table>
					</td>
					<td>	
					</td>
				</tr>
			  </table>
			</td>
		  </tr>
		</table>
		<table border='0' cellpadding=0 cellspacing=0 width='100%' style='padding-top:3px;'>
			<tr>
				<td align=right>
            	<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle valign='top'>";
  
			if($mmode =='pop'){
				$Contents.="	
					<a href='javascript:self.close();'><img src='../images/".$admininfo["language"]."/b_cancel.gif' align=absmiddle  border=0 valign='top'></a>";
			}else{
				$Contents.="	
					<a href='mail_box.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' align=absmiddle  border=0 valign='top'></a>";
			}
			$Contents.="	
				</td>
			</tr>
		</table>
          ";


$Contents .= "
		</form>
    </td>
  </tr>";
  $Contents .= "
  <tr>
    <td align='left'>";


if($mmode == "pop"){
    $P = new ManagePopLayOut();
    $P->NaviTitle = "SMS작성";
}else{
    $P = new LayOut();
    $P->title = "SMS작성";
	$P->strLeftMenu = campaign_menu();
}
    $P->addScript = $Script;
    $P->Navigation = "메일링/SMS > SMS작성";
    $P->strContents = $Contents;
	$P->OnloadFunction = "init();";//showSubMenuLayer('storeleft');
    echo $P->PrintLayOut();

?>