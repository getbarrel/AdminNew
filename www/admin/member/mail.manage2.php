<?
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
	doToggleText();
	frm.content.value = iView.document.body.innerHTML;
	return true;
}

function init(){
	Content_Input();
	Init(document.mailconfig);
}

function Content_Input(){
	document.mailconfig.content.value = document.mailconfig.mc_mail_text.value;
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
	$mc_code = $db->dt[mc_code];
	$mc_mail_adminsend_yn = $db->dt[mc_mail_adminsend_yn];
	$mc_mail_usersend_yn = $db->dt[mc_mail_usersend_yn];
	$mc_sms_adminsend_yn = $db->dt[mc_sms_adminsend_yn];
	$mc_sms_usersend_yn = $db->dt[mc_sms_usersend_yn];
	$act = "update";
}else{
	$act = "insert";
	$mc_mail_adminsend_yn = "Y";
	$mc_mail_usersend_yn = "Y";
	$mc_sms_adminsend_yn = "N";
	$mc_sms_usersend_yn = "N";
}

if($mc_code != ""){
	$thisfile = load_template($_SERVER["DOCUMENT_ROOT"]."/mallstory_templete/".SiteUseTemplete($HTTP_HOST)."/ms_mail_".$mc_code.".htm");
}

$Contents ="
		<table width='100%' border='0' cellspacing='0' cellpadding='0' class='input_table_box'>
		<form name=mailconfig action='mail.manage.act.php' method='post' onsubmit='return SubmitX(this);'>
                    <input type='hidden' name=act value='".$act."'>
                    <input type='hidden' name=mc_ix value='".$mc_ix." '>
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
		      <input type=radio name='mc_sms_adminsend_yn' value='N' id='omc_sms_adminsend_yn2' ".CompareReturnValue("N",$mc_sms_adminsend_yn,"checked")."><label for='omc_sms_adminsend_yn2'>발송하지 않음</label>  &nbsp; &nbsp; &nbsp;

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
      </table>";


$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >해당항목에 맞는 정보를 수정해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >{mem_name} , {shop_name} 등 {}(대괄호) 안에 있는 내용은 치환코드로 프로그램에서 해당정보와 맞는 정보로 치환됩니다</td></tr>

</table>
";


//$help_text = HelpBox("게시판 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:-9px;'><table><tr><td valign=bottom><b>메일/SMS 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text,200)."</div>";

$Contents = $Contents.$help_text;

$P = new LayOut;
$P->addScript = "<script language='JavaScript' src='/admin/ckeditor/ckeditor.js'></script>\n$Script";
$P->OnloadFunction = "CKEDITOR.replace('mc_mail_text').config.height = '400px';";//showSubMenuLayer('storeleft');
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
