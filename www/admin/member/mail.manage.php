<?php
include("../class/layout.class");

//print_r($_SESSION);

$Script = "
<Script Language='JavaScript'>

function SubmitX(frm){
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	return true;
}

function testSendMail(mcIx){
    if($('#mainName').val() == ''){
        alert('전송할 이메일 주소를 입력해 주세요.');
		return false;
	}
    
    if (emailCheck($('#mainName').val())) {
		
	} else {
		alert('유효하지 않은 이메일 주소입니다.');
        return false;
	}
    
    $.ajax({
		type: 'POST',
		data: {'update_kind': 'testemail', 'mc_ix': mcIx, 'mail_addrs': $('#mainName').val()},
		url: './member_batch.act.php',
		dataType: 'json',
		async: true,
		error: function(e) {
            alert('메일 전송 요청이 완료 되었습니다.');
			return false;
            //console.log(e); 
        },
		success: function(d) {
			//console.log(d);
            alert('메일 전송 요청이 완료 되었습니다.');
			return false;
		}
	});
    
}

function emailCheck(email_address){     
	email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
	if(!email_regex.test(email_address)){ 
		return false; 
	}else{
		return true;
	}
}

</Script>
";
$db = new Database;
$db->query("SELECT * FROM ".TBL_SHOP_MAILSEND_CONFIG." where mc_ix= '$mc_ix'");
$db->fetch();

if($db->total){
	$mc_ix = $db->dt[mc_ix];
	$mc_title = $db->dt[mc_title];
	$mc_mail_title = $db->dt[mc_mail_title];
	$mc_mail_text = $db->dt[mc_mail_text];
	$mc_sms_text = $db->dt[mc_sms_text];
    $disp = $db->dt['disp'];
	if($db->dbms_type == "oracle"){
		$mc_mail_text = str_replace("\\",'',$mc_mail_text);
		$mc_sms_text = str_replace("\\",'',$mc_sms_text);
	}

	$mc_code = $db->dt[mc_code];
	$mc_mail_adminsend_yn = $db->dt[mc_mail_adminsend_yn];
	$mc_mail_usersend_yn = $db->dt[mc_mail_usersend_yn];
	$mc_sms_adminsend_yn = $db->dt[mc_sms_adminsend_yn];
	$mc_sms_usersend_yn = $db->dt[mc_sms_usersend_yn];
    $kakao_alim_talk_template_code = $db->dt[kakao_alim_talk_template_code];
	$kakao_alim_talk_btn_code = $db->dt[kakao_alim_talk_btn_code];
    
	$act = "update";
}else{
	$act = "insert";
	$disp = 1;
	$mc_mail_adminsend_yn = "Y";
	$mc_mail_usersend_yn = "Y";
	$mc_sms_adminsend_yn = "N";
	$mc_sms_usersend_yn = "N";
}

if($mc_code != ""){
	$thisfile = load_template($_SERVER["DOCUMENT_ROOT"]."/mallstory_templete/".SiteUseTemplete($HTTP_HOST)."/ms_mail_".$mc_code.".htm");
}

$Contents ="
<form name=mailconfig action='mail.manage.act.php' method='post' onsubmit='return SubmitX(this);'>
<input type='hidden' name=act value='".$act."'>
<input type='hidden' name=mc_ix value='".$mc_ix."'>
<input type='hidden' name=disp value='".$disp."'>
		<table width='100%' border='0' cellspacing='0' cellpadding='0' class='input_table_box'>

		    <col width=20%>
		    <col width=*>
		    <!--tr>
					<td align='left' colspan=2> ".GetTitleNavigation("메일/SMS 작성", "마케팅지원 > 메일/SMS 작성 <!--a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a-->")."</td>
		    </tr-->
		    <tr>
		      <td class='input_box_title'> <b>메일코드 <img src='".$required3_path."'></b></td>
		      <td class='input_box_item' >
				<table width='100%' border='0' cellspacing='0' cellpadding='0'>

					<tr>
						<td width='340px'>
						  <input type=text name='mc_code' value='".$mc_code."' size='50' validation='true' title='메일코드'> &nbsp;&nbsp;&nbsp;
						  </td>
						  <td class='input_box_item'>
						  <span><img src='../image/emo_3_15.gif' align=absmiddle>  영문으로 반드시 입력해주세요 메일 템플릿 생성시 사용됩니다.</span>
						</td>
					</tr>
				</table>
			 </td>
		    </tr>
		    <tr>
		      <td class='input_box_title'> <b>메일/SMS 관리제목 <img src='".$required3_path."'></b></td>
		      <td class='input_box_item'>
				<table width='100%' border='0' cellspacing='0' cellpadding='0'>
					<tr>
						<td width='340px'>
						  <input type=text name='mc_title' value='".$mc_title."' size='50' validation='true' title='메일/SMS 관리제목'> &nbsp;&nbsp;&nbsp;
						  </td>
						  <td class='input_box_item' style='align:left;'>
						  <span><img src='../image/emo_3_15.gif' align=absmiddle> 메일 관리 항목에 표시되는 제목입니다.</span>
				  		</td>
					</tr>
				</table>
		      </td>
		    </tr>
		     <tr>
		      <td class='input_box_title'> <b>메일제목 <img src='".$required3_path."'></b></td>
		      <td class='input_box_item'>
				<table width='100%' border='0' cellspacing='0' cellpadding='0'>
					<col width:50% />
					<col width:50% />
					<tr>
						<td width='340px'>
						  <input type=text name='mc_mail_title' value='".$mc_mail_title."' size='50' validation='true' title='메일제목'> &nbsp;&nbsp;&nbsp;
						  </td>
						  <td class='input_box_item'>
						  <span class=small><img src='../image/emo_3_15.gif' align=absmiddle> 실제 보내지는 메일의 제목으로 쓰여집니다.</span>
						</td>
					</tr>
				</table>
		      </td>
		    </tr>
		    <tr>
		      <td class='input_box_title'> 사용자 메일 발송여부 </td>
		      <td class='input_box_item'>
		      <input type=radio name='mc_mail_usersend_yn' value='Y' id='omc_mail_usersend_yn1'  ".CompareReturnValue("Y",$mc_mail_usersend_yn,"checked")."><label for='omc_mail_usersend_yn1'>발송</label>&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type=radio name='mc_mail_usersend_yn' value='N' id='omc_mail_usersend_yn2' ".CompareReturnValue("N",$mc_mail_usersend_yn,"checked")."><label for='omc_mail_usersend_yn2'>발송하지 않음</label> &nbsp; &nbsp; &nbsp;
		      </td>
		    </tr>
			 <tr>
		      <td class='input_box_title'> 사용자 SMS 발송여부 </td>
		      <td class='input_box_item'>
		       <input type=radio name='mc_sms_usersend_yn' value='Y' id='omc_sms_usersend_yn1'  ".CompareReturnValue("Y",$mc_sms_usersend_yn,"checked")."><label for='omc_sms_usersend_yn1'>발송</label>&nbsp;&nbsp;&nbsp;&nbsp;
		       <input type=radio name='mc_sms_usersend_yn' value='K' id='omc_sms_usersend_yn3' ".CompareReturnValue("K",$mc_sms_usersend_yn,"checked")."><label for='omc_sms_usersend_yn3'>카카오 알림톡 발송</label>&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type=radio name='mc_sms_usersend_yn' value='N' id='omc_sms_usersend_yn2' ".CompareReturnValue("N",$mc_sms_usersend_yn,"checked")."><label for='omc_sms_usersend_yn2'>발송하지 않음</label>
		      </td>
		    </tr>
		    <tr>
		      <td class='input_box_title'> 관리자 메일 발송여부 </td>
		      <td class='input_box_item'>
		      <input type=radio name='mc_mail_adminsend_yn' value='Y' id='omc_mail_adminsend_yn1'  ".CompareReturnValue("Y",$mc_mail_adminsend_yn,"checked")."><label for='omc_mail_adminsend_yn1'>발송</label>&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type=radio name='mc_mail_adminsend_yn' value='N' id='omc_mail_adminsend_yn2' ".CompareReturnValue("N",$mc_mail_adminsend_yn,"checked")."><label for='omc_mail_adminsend_yn2'>발송하지 않음</label> &nbsp; &nbsp; &nbsp;

		      </td>
		    </tr>
			<tr>
		      <td class='input_box_title'> 관리자 SMS 발송여부 </td>
		      <td class='input_box_item'>
		       <input type=radio name='mc_sms_adminsend_yn' value='Y' id='omc_sms_adminsend_yn1'  ".CompareReturnValue("Y",$mc_sms_adminsend_yn,"checked")."><label for='omc_sms_adminsend_yn1'>발송</label>&nbsp;&nbsp;&nbsp;&nbsp;
		       <input type=radio name='mc_sms_adminsend_yn' value='K' id='omc_sms_adminsend_yn3'  ".CompareReturnValue("K",$mc_sms_adminsend_yn,"checked")."><label for='omc_sms_adminsend_yn3'>카카오 알림톡 발송</label>&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type=radio name='mc_sms_adminsend_yn' value='N' id='omc_sms_adminsend_yn2' ".CompareReturnValue("N",$mc_sms_adminsend_yn,"checked")."><label for='omc_sms_adminsend_yn2'>발송하지 않음</label>  &nbsp; &nbsp; &nbsp;

		      </td>
		    </tr>
			<tr>
		      <td class='input_box_title'> 카카오 알림톡 사용시 탬플릿 코드 </td>
		      <td class='input_box_item'>
				<input type=text name='kakao_alim_talk_template_code' value='".$kakao_alim_talk_template_code."' size='50' validation='false' title='카카오 알림톡 탬플릿 코드'>

		      </td>
			 </tr>
			 <tr>
		      <td class='input_box_title'> 카카오 알림톡 사용시 버튼 코드 </td>
		      <td class='input_box_item'>
				<input type=text name='kakao_alim_talk_btn_code' value='".$kakao_alim_talk_btn_code."' size='150' validation='false' title='카카오 알림톡 버튼 코드'>

		      </td>
			 </tr>
			 <tr>
		      <td class='input_box_title'> 테스트메일보내기 </td>
		      <td class='input_box_item'>
				<input type='text' size='50px;' id='mainName'><a href=javascript:testSendMail('$mc_ix')> 메일전송</a>

		      </td>
			 </tr>
			 
			 
			 <td>
        		
        	</td>
			 
		</table>
		<table width='100%' border='0' cellspacing='0' cellpadding='0' class='input_table_box' style='margin-top:3px;'>
		    </tr>
		    <tr height=30 align=center bgcolor='#efefef' >
		    	<td class='input_box_title'>SMS 내용</td>
		    	<td class='input_box_title'>Mail 내용</td>
		    </tr>
			<tr>
        	<td bgcolor='#ffffff' align=center valign=top>
				<table cellpadding=0 cellspacing=0>
				<tr>
					<td height=57><!--img src='../image/emo_3_15.gif' align=absmiddle--> ".$mc_title."</td>
				</tr>
				<tr>
					<td>
						<table width='238' height='326' border='0' cellpadding='0' cellspacing='0' background='../image/sms_box.gif' style='background-repeat:no-repeat;'>
						<tr>
							<td height='100px'>&nbsp;</td>
						</tr>
						<tr>
							<td valign='top' style='padding-left:45px;padding-right:95px; ' >
								<textarea  name='mc_sms_text' style='color:#ffffff;font-size:11px;border:0px;width:150;height:120px;overflow:auto;background-color:transparent;' >".$mc_sms_text."</textarea>
							</td>
						</tr>
						<tr>
							<td height='19'>&nbsp;</td>
						</tr>
						</table>
					</td>
				</tr>
				</table>
        	</td>
			<td colspan='3' bgcolor='#ffffff' style='padding:0px;'>
				<textarea name='mc_mail_text' id='mc_mail_text'>".$mc_mail_text."</textarea>
          </td>
        </tr>
		 </table>
		<table cellpadding=0 cellspacing=0 width=100%;>
        <tr height=60>
			<td bgcolor='#ffffff' align=right style='padding:0px;'>
				<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle valign='top'>
				<a href='mail.manage.list2.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle valign='top'></a>
			</td>
		</tr>
      </table>
	 </form>";


$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >해당항목에 맞는 정보를 수정해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >
	    [프론트 전용 템플릿 태그]<br/>
	    --------------------------------------------------------------
	    <ul>
            <li>{mallDomain} : 웹사이트 주소 (http://sample.com)</li>
            <li>{templateImagePath} : 웹사이트 assets image URL</li>
            <li>{dataImagePath} : 이미지 서버 Data image URL</li>
            <li>{mallName} : 쇼핑몰 이름</li>
            <li>{comName} : 회사이름</li>
            <li>{comCeo} : 대표자명</li>
            <li>{comAddr1} : 회사주소1</li>
            <li>{comAddr2} : 회사주소2</li>
            <li>{comEmail} : 회사 이메일</li>
            <li>{comNumber} : 사업자 번호</li>
            <li>{comCsPhone} : 고객센타 연락처</li>
            <li>{comOnlineBusinessNumber} : </li>
            <li>{comOfficerName} : 담당자 명</li>
        </ul>
        --------------------------------------------------------------
    </td></tr>

</table>
";


//$help_text = HelpBox("게시판 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:-9px;'><table><tr><td valign=bottom><b>메일/SMS 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text,200)."</div>";

$Contents = $Contents.$help_text;

$P = new LayOut;
$P->addScript = "<script language='JavaScript' src='/admin/ckeditor/ckeditor.js'></script>\n$Script";
//$P->OnloadFunction = "CKEDITOR.replace('mc_mail_text').config.height = '400px';";//showSubMenuLayer('storeleft');
$P->OnloadFunction = "
	CKEDITOR.replace('mc_mail_text',{
		height:'500px',
		toolbar: [
			[ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ],
			[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
			[ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ],
			[ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ],
			'/',
			[ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ],
			[ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ],
			[ 'Link', 'Unlink', 'Anchor' ],
			[ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ],
			'/',
			[ 'Styles', 'Format', 'Font', 'FontSize' ],
			[ 'TextColor', 'BGColor' ],
			[ 'Maximize', 'ShowBlocks' ],
			[ '-' ],
			[ 'About' ]
		]
	});
";
$P->strLeftMenu = member_menu();
switch($mc_ix) {
	case ("0001") : $page_title_txt="회원가입완료";
	break;
	case ("0002") : $page_title_txt="회원탈퇴";
	break;
	case ("0003") : $page_title_txt="비밀번호찾기";
	break;
	case ("0004") : $page_title_txt="주문완료(무통장)";
	break;
	case ("0005") : $page_title_txt="주문완료(카드)";
	break;
	case ("0009") : $page_title_txt="주문완료(가상계좌)";
	break;
	case ("0010") : $page_title_txt="주문완료(계좌이체)";
	break;
	case ("0006") : $page_title_txt="상품발송";
	break;
	case ("0007") : $page_title_txt="주문취소";
	break;
	case ("0017") : $page_title_txt="소셜무료쿠폰발송";
	break;
	case ("0021") : $page_title_txt="1:1상담 답변";
	break;
	default : $page_title_txt="자동메일/SMS 작성";
	break;
}
$P->Navigation = "회원관리 > 자동메일/SMS 설정 > ".$page_title_txt;
$P->title = $page_title_txt;
$P->strContents = $Contents;
$P->PrintLayOut();
?>


