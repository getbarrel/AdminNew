<?
include("../class/layout.class");

$Script = "
<Script Language='JavaScript'>
function SubmitX(frm){
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	doToggleText();
	//frm.content.value = iView.document.body.innerHTML;
	frm.content.value = document.getElementById('iView').contentWindow.document.body.innerHTML; //kbk
	return true;
}

function init(){
	Content_Input();
	Init(document.mailconfig);
}

function Content_Input(){
	document.mailconfig.content.value = document.mailconfig.mall_companyinfo.value;
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
	$thisfile = load_template($_SERVER["DOCUMENT_ROOT"]."/shop_templete/".SiteUseTemplete($HTTP_HOST)."/ms_mail_".$mc_code.".htm");
}

$Contents ="
		<table width='100%' border='0' cellspacing='0' cellpadding='0' style='table-layout:fixed;'>
		<form name=mailconfig action='mail.manage.act.php' method='post' onsubmit='return SubmitX(this);'>
                    <input type='hidden' name=act value='".$act."'>
                    <input type='hidden' name=mc_ix value='".$mc_ix." '>
		    <col width='22%'>
		    <col width='*'>
		    <col width='20%'>
		    <col width='20%'>
		    <tr>
					<td align='left' colspan=4 style='padding:0 0 10px 0;'> ".GetTitleNavigation("메일/SMS 작성", "마케팅지원 > 메일/SMS 작성 <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a>")."</td>
		    </tr>
		    <tr>
		      <td height=27 bgcolor='#efefef' align=left style='padding:0 0 0 10px'><img src='../image/ico_dot.gif' align=absmiddle> <b>메일코드 <img src='".$required3_path."'></b></td>
		      <td align=left style='padding-left:4px;'>
		      <input type=text name='mc_code' value='".$mc_code."' style='width:90%' validation='true' title='메일코드'>
		      </td>
		      <td colspan=2>
		      <span class=small><img src='../image/emo_3_15.gif' align=absmiddle>  영문으로 반드시 입력해주세요 메일 템플릿 생성시 사용됩니다.</span>
		      </td>
		    </tr>
		    <tr height=1><td colspan=4 class=td_underline></td></tr>
		    <tr >
		      <td height=27 bgcolor='#efefef' align=left style='padding:0 0 0 10px'><img src='../image/ico_dot.gif' align=absmiddle> <b>메일/SMS 관리제목 <img src='".$required3_path."'></b></td>
		      <td align=left style='padding-left:4px;'>
		      <input type=text name='mc_title' value='".$mc_title."' style='width:90%' validation='true' title='메일/SMS 관리제목'>
		      </td>
		      <td colspan=2>
		      <span class=small><img src='../image/emo_3_15.gif' align=absmiddle> 메일 관리 항목에 표시되는 제목입니다.</span>
		      </td>
		    </tr>
		    <tr height=1><td colspan=4 class=td_underline></td></tr>
		     <tr>
		      <td height=27 bgcolor='#efefef' align=left style='padding:0 0 0 10px'><img src='../image/ico_dot.gif' align=absmiddle> <b>메일제목 <img src='".$required3_path."'></b></td>
		      <td align=left style='padding-left:4px;'>
		      <input type=text name='mc_mail_title' value='".$mc_mail_title."' style='width:90%' validation='true' title='메일제목'>
		      </td>
		      <td colspan=2>
		      <span class=small><img src='../image/emo_3_15.gif' align=absmiddle> 실제 보내지는 메일의 제목으로 쓰여집니다.</span>
		      </td>
		    </tr>
			 <tr hegiht=1><td colspan=4 class=td_underline></td></tr>
		    <tr>
		      <td height=27 bgcolor='#efefef' align=left style='padding:0 0 0 10px'><img src='../image/ico_dot.gif' align=absmiddle> 사용자 메일 발송여부 </td>
		      <td align=left colspan=3>
		      <input type=radio name='mc_mail_usersend_yn' value='Y' id='omc_mail_usersend_yn1'  ".CompareReturnValue("Y",$mc_mail_usersend_yn,"checked")."><label for='omc_mail_usersend_yn1'>발송</label>&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type=radio name='mc_mail_usersend_yn' value='N' id='omc_mail_usersend_yn2' ".CompareReturnValue("N",$mc_mail_usersend_yn,"checked")."><label for='omc_mail_usersend_yn2'>발송하지 않음</label> &nbsp; &nbsp; &nbsp;
		      </td>
		    </tr>
			<tr hegiht=1><td colspan=4 class=td_underline></td></tr>
			 <tr>
		      <td height=27 bgcolor='#efefef' align=left style='padding:0 0 0 10px'><img src='../image/ico_dot.gif' align=absmiddle> 사용자 SMS 발송여부 </td>
		      <td align=left colspan=3>
		       <input type=radio name='mc_sms_usersend_yn' value='Y' id='omc_sms_usersend_yn1'  ".CompareReturnValue("Y",$mc_sms_usersend_yn,"checked")."><label for='omc_sms_usersend_yn1'>발송</label>&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type=radio name='mc_sms_usersend_yn' value='N' id='omc_sms_usersend_yn2' ".CompareReturnValue("N",$mc_sms_usersend_yn,"checked")."><label for='omc_sms_usersend_yn2'>발송하지 않음</label>
		      </td>
		    </tr>
		    <tr height=1><td colspan=4 class=td_underline></td></tr>
		    <tr>
		      <td height=27 bgcolor='#efefef' align=left style='padding:0 0 0 10px'><img src='../image/ico_dot.gif' align=absmiddle> 관리자 메일 발송여부 </td>
		      <td align=left  colspan=3>
		      <input type=radio name='mc_mail_adminsend_yn' value='Y' id='omc_mail_adminsend_yn1'  ".CompareReturnValue("Y",$mc_mail_adminsend_yn,"checked")."><label for='omc_mail_adminsend_yn1'>발송</label>&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type=radio name='mc_mail_adminsend_yn' value='N' id='omc_mail_adminsend_yn2' ".CompareReturnValue("N",$mc_mail_adminsend_yn,"checked")."><label for='omc_mail_adminsend_yn2'>발송하지 않음</label> &nbsp; &nbsp; &nbsp;

		      </td>
		    </tr>
		    <tr hegiht=1><td colspan=4 class=td_underline></td></tr>
			<tr>
		      <td height=27 bgcolor='#efefef' align=left style='padding:0 0 0 10px'><img src='../image/ico_dot.gif' align=absmiddle> 관리자 SMS 발송여부 </td>
		      <td align=left  colspan=3>
		       <input type=radio name='mc_sms_adminsend_yn' value='Y' id='omc_sms_adminsend_yn1'  ".CompareReturnValue("Y",$mc_sms_adminsend_yn,"checked")."><label for='omc_sms_adminsend_yn1'>발송</label>&nbsp;&nbsp;&nbsp;&nbsp;
		      <input type=radio name='mc_sms_adminsend_yn' value='N' id='omc_sms_adminsend_yn2' ".CompareReturnValue("N",$mc_sms_adminsend_yn,"checked")."><label for='omc_sms_adminsend_yn2'>발송하지 않음</label>  &nbsp; &nbsp; &nbsp;

		      </td>
		    </tr>
			<tr hegiht=1><td colspan=4 class=td_underline></td></tr>

		    <tr><td height=20 colspan=4 ></td></tr>
		    <tr align=center bgcolor='#efefef' >
		    	<td height=30 >SMS 내용</td>
		    	<td colspan=3>Mail 내용</td>
		    </tr>
		    <tr hegiht=1><td colspan=4 class=td_underline></td></tr>
        <tr>
        	<td bgcolor='#ffffff' align=center valign=top>
        	<table cellpadding=0 cellspacing=0 border='0' width='100%'>
        	<tr><td height=57 width='100%' align='left' style='padding:0px 5px;'><!--img src='../image/emo_3_15.gif' align=absmiddle--> ".$mc_title."</td></tr>
        	<tr><td>

          <table width='100%' height='326' border='0' cellpadding='0' cellspacing='0' style='background:url(../image/sms_box.gif) no-repeat center;'>
              <tr>
                <td height='120'>&nbsp;</td>
              </tr>
              <tr>
                <td valign='top' style='padding-left:8px;' align='center'>

                  <textarea  name='mc_sms_text' style='color:#ffffff;font-size:11px;border:0px;width:120px;height:130px;overflow:auto;background-color:transparent;' >".$mc_sms_text."</textarea>
                  </td>
              </tr>
              <tr>
                <td height='19'>&nbsp;</td>
              </tr>
            </table>
        	 </td></tr>
        	</table>
        	</td>
          <td height='30' colspan='3'>
      <table id='tblCtrls' width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
        <tr>
          <td bgcolor='F5F6F5'>
			 <table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td width='18%' height='56'>
					 	<table width='100%' height='56' border='0' align='center' cellpadding='0' cellspacing='0'>
                    <tr align='center' valign='bottom'>
                      <td height='26'><a href='javascript:doBold();' onMouseOver=\"MM_swapImage('editImage1','','../webedit/image/wtool1_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool1.gif' name='editImage1' width='19' height='18' border='0' id='editImage1'></a></td>
                      <td><a href='javascript:doItalic();' onMouseOver=\"MM_swapImage('editImage2','','../webedit/image/wtool2_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool2.gif' name='editImage2' width='19' height='18' border='0' id='editImage2'></a></td>
                      <td><a href='javascript:doUnderline();' onMouseOver=\"MM_swapImage('editImage3','','../webedit/image/wtool3_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool3.gif' name='editImage3' width='19' height='18' border='0' id='editImage3'></a></td>
                    </tr>
                    <tr>
                      <td height='3' colspan='3'></td>
                    </tr>
                    <tr align='center' valign='top'>
                      <td height='27'><a href='javascript:doLeft();' onMouseOver=\"MM_swapImage('editImage8','','../webedit/image/wtool8_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool8.gif' name='editImage8' width='19' height='18' border='0' id='editImage8'></a></td>
                      <td><a href='javascript:doCenter();' onMouseOver=\"MM_swapImage('editImage9','','../webedit/image/wtool9_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool9.gif' name='editImage9' width='19' height='18' border='0' id='editImage9'></a></td>
                      <td><a href='javascript:doRight();' onMouseOver=\"MM_swapImage('editImage10','','../webedit/image/wtool10_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool10.gif' name='editImage10' width='19' height='18' border='0' id='editImage10'></a></td>
                    </tr>
                  </table>
					 </td>
                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
                <td width='19%'>
					 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                      <td width='100%' height='27' align='center' valign='bottom'><a href='javascript:doFont();' onMouseOver=\"MM_swapImage('editImage4','','../webedit/image/wtool4_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool4.gif' name='editImage4' width='84' height='22' border='0' id='editImage4'></a></td>
                    </tr>
                    <tr>
                      <td height='2'></td>
                    </tr>
                    <tr>
                      <td height='27' align='center' valign='top'><a href='javascript:doSize();' onMouseOver=\"MM_swapImage('editImage11','','../webedit/image/wtool11_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool11.gif' name='editImage11' width='84' height='22' border='0' id='editImage11'></a></td>
                    </tr>
                  </table>
					 </td>
                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
                <td width='20%'>
					 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                      <td height='27' align='center' valign='bottom'><a href='javascript:doForcol();' onMouseOver=\"MM_swapImage('editImage5','','../webedit/image/wtool5_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool5.gif' name='editImage5' width='95' height='22' border='0' id='editImage5'></a></td>
                    </tr>
                    <tr>
                      <td height='2'></td>
                    </tr>
                    <tr>
                      <td height='27' align='center' valign='top'><a href='javascript:doBgcol();' onMouseOver=\"MM_swapImage('editImage12','','../webedit/image/wtool12_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool12.gif' name='editImage12' width='95' height='22' border='0' id='editImage12'></a></td>
                    </tr>
                  </table>
					 </td>
                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
                <td width='18%'>
					 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                      <td height='27' align='center' valign='bottom'><a href='javascript:doImage();' onMouseOver=\"MM_swapImage('editImage6','','../webedit/image/wtool6_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool6.gif' name='editImage6' width='73' height='22' border='0' id='editImage6'></a></td>
                    </tr>
                    <tr>
                      <td height='2'></td>
                    </tr>
                    <tr>
                      <td height='27' align='center' valign='top'><a href='javascript:doTable();' onMouseOver=\"MM_swapImage('editImage13','','../webedit/image/wtool13_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool13.gif' name='editImage13' width='73' height='22' border='0' id='editImage13'></a></td>
                    </tr>
                  </table>
					 </td>
                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
                <td width='*'>
					 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                      <td height='27' align='center' valign='bottom'><a href='javascript:doLink();' onMouseOver=\"MM_swapImage('editImage7','','../webedit/image/wtool7_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool7.gif' name='editImage7' width='74' height='22' border='0' id='editImage7'></a></td>
                    </tr>
                    <tr>
                      <td height='2'></td>
                    </tr>
                    <tr>
                      <td height='27' align='center' valign='top'><a href='javascript:doMultilink();' onMouseOver=\"MM_swapImage('editImage14','','../webedit/image/wtool14_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool14.gif' name='editImage14' width='111' height='22' border='0' id='editImage14'></a></td>
                    </tr>
                  </table>
					 </td>
              </tr>
            </table>
			 </td>
        </tr>
      </table>
      <input type='hidden' name='content' value=''>
      <iframe align='right' id='iView' style='width: 100%; height:410px;' scrolling='YES' hspace='0' vspace='0'></iframe>
      <!-- html편집기 메뉴 종료 -->
          </td>
        </tr>
        <tr>
          <td height='25' align='center' bgcolor='#F0F0F0'></td>
	       <td colspan='3' align='right'>
      <a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
		      <a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
          </td>
        </tr>
        <tr>
          <td bgcolor='#D0D0D0' height='1' colspan='4' style='font-size:1px;padding:0px;margin:0px;line-height:0px;'></td>
        </tr>
        <tr><td height=60 colspan=4 align=right style='padding:0px;'><input type=image src='../image/b_save.gif' border=0> <a href='mail.manage.list.php'><img src='../image/b_cancel.gif' border=0></a></td></tr>
        <textarea name='mall_companyinfo'  style='display:none'>".$mc_mail_text."</textarea></form>
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
$help_text =HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td valign=top><b>메일/SMS 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다' style='cursor:pointer;'><img src='../image/movie_manual.gif' align=absmiddle style='position:absolute;top:-1px;' borer='0'></a></td></tr></table>", $help_text,200);

$Contents = $Contents.$help_text;

$P = new LayOut;
$P->addScript = "<script language='JavaScript' src='../webedit/webedit.js'></script>\n$Script";
$P->OnloadFunction = "init();MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')";//showSubMenuLayer('storeleft');
$P->strLeftMenu = marketting_menu();
$P->Navigation = "HOME > 마케팅지원 > 메일/SMS 관리";
$P->strContents = $Contents;
$P->PrintLayOut();

?>