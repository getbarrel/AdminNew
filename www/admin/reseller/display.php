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
	$mc_code = $db->dt[mc_code];
	$mc_mail_adminsend_yn = $db->dt[mc_mail_adminsend_yn];
	$mc_mail_usersend_yn = $db->dt[mc_mail_usersend_yn];
	$act = "update";
}else{
	$act = "insert";
	$mc_mail_adminsend_yn = "Y";
	$mc_mail_usersend_yn = "Y";
}

if($mc_code != ""){
	$thisfile = load_template($_SERVER["DOCUMENT_ROOT"]."/mallstory_templete/".SiteUseTemplete($HTTP_HOST)."/ms_mail_".$mc_code.".htm");
}

session_start();
echo 'id :' .$_COOKIE[rsl_id].'</br>';
echo 'type :' .$_COOKIE[flowin_type].'</br>';
echo 'url :' .$_COOKIE[flowin_url].'</br>SESSION :';
print_r($_SESSION);


$Contents ="
	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='input_table_box'>
		<form name=mailconfig action='display.act.php' method='post' onsubmit='return SubmitX(this);'>
		<input type='hidden' name='act' value='".$act."'>
		<input type='hidden' name='mc_ix' value='".$mc_ix." '>
			<col width=20%>
			<col width=*>
			<!--tr>
				<td align='left' colspan=2> ".GetTitleNavigation("리셀러화면설계", "리셀러관리 > 리셀러설정 > 리셀러화면설계 <!--a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a-->")."
				</td>
			</tr-->
	</table>";

$Contents ="
	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='input_table_box'>
		<tr>
			<td class='input_box_title'> <b>이미지 설명 <img src='".$required3_path."'></b></td>
			<td class='input_box_item' >
				<table width='100%' border='0' cellspacing='0' cellpadding='0'>
					<tr>
						<td width='340px'>
							<input type=text name='mc_code' value='".$mc_code."' size='50' validation='true' title='이미지 설명'> &nbsp;&nbsp;&nbsp;
						</td>
						<td class='input_box_item'>
							<span><img src='../image/emo_3_15.gif' align=absmiddle> 이미지의 설명 문구를 쓰시면 됩니다.</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td height='30' colspan='2'>
				<table id='tblCtrls' width='100%' border='0' cellspacing='0' cellpadding='0' align='center'>
				<tr>
					<td bgcolor='F5F6F5'>
						<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						<tr>
										<td width='18%' height='56'>
										 <table width='100%' height='56' border='0' align='center' cellpadding='0' cellspacing='0'>
											<tr align='center' valign='bottom'>
											  <td height='26'><a href='javascript:doBold();' onMouseOver=\"MM_swapImage('editImage1','','../images/".$admininfo["language"]."/webedit/wtool1_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool1.gif' name='editImage1' width='19' height='18' border='0' id='editImage1'></a></td>
											  <td><a href='javascript:doItalic();' onMouseOver=\"MM_swapImage('editImage2','','../images/".$admininfo["language"]."/webedit/wtool2_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool2.gif' name='editImage2' width='19' height='18' border='0' id='editImage2'></a></td>
											  <td><a href='javascript:doUnderline();' onMouseOver=\"MM_swapImage('editImage3','','../images/".$admininfo["language"]."/webedit/wtool3_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool3.gif' name='editImage3' width='19' height='18' border='0' id='editImage3'></a></td>
											</tr>
											<tr>
											  <td height='3' colspan='3'></td>
											</tr>
											<tr align='center' valign='top'>
											  <td height='27'><a href='javascript:doLeft();' onMouseOver=\"MM_swapImage('editImage8','','../images/".$admininfo["language"]."/webedit/wtool8_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool8.gif' name='editImage8' width='19' height='18' border='0' id='editImage8'></a></td>
											  <td><a href='javascript:doCenter();' onMouseOver=\"MM_swapImage('editImage9','','../images/".$admininfo["language"]."/webedit/wtool9_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool9.gif' name='editImage9' width='19' height='18' border='0' id='editImage9'></a></td>
											  <td><a href='javascript:doRight();' onMouseOver=\"MM_swapImage('editImage10','','../images/".$admininfo["language"]."/webedit/wtool10_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool10.gif' name='editImage10' width='19' height='18' border='0' id='editImage10'></a></td>
											</tr>
										 </table>
										</td>
										<td width='2'><img src='../images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
										<td width='19%'>
										  <table width='100%' border='0' cellspacing='0' cellpadding='0'>
											<tr>
											  <td width='100%' height='27' align='center' valign='bottom'><a href='javascript:doFont();' onMouseOver=\"MM_swapImage('editImage4','','../images/".$admininfo["language"]."/webedit/wtool4_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool4.gif' name='editImage4' width='84' height='22' border='0' id='editImage4'></a></td>
											</tr>
											<tr>
											  <td height='2'></td>
											</tr>
											<tr>
											  <td height='27' align='center' valign='top'><a href='javascript:doSize();' onMouseOver=\"MM_swapImage('editImage11','','../images/".$admininfo["language"]."/webedit/wtool11_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool11.gif' name='editImage11' width='84' height='22' border='0' id='editImage11'></a></td>
											</tr>
										  </table>
											 </td>
										<td width='2'><img src='../images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
										<td width='20%'>
										  <table width='100%' border='0' cellspacing='0' cellpadding='0'>
											<tr>
											  <td height='27' align='center' valign='bottom'><a href='javascript:doForcol();' onMouseOver=\"MM_swapImage('editImage5','','../images/".$admininfo["language"]."/webedit/wtool5_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool5.gif' name='editImage5' width='95' height='22' border='0' id='editImage5'></a></td>
											</tr>
											<tr>
											  <td height='2'></td>
											</tr>
											<tr>
											  <td height='27' align='center' valign='top'><a href='javascript:doBgcol();' onMouseOver=\"MM_swapImage('editImage12','','../images/".$admininfo["language"]."/webedit/wtool12_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool12.gif' name='editImage12' width='95' height='22' border='0' id='editImage12'></a></td>
											</tr>
										  </table>
											 </td>
										<td width='2'><img src='../images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
										<td width='18%'>
										  <table width='100%' border='0' cellspacing='0' cellpadding='0'>
											<tr>
											  <td height='27' align='center' valign='bottom'><a href='javascript:doImage();' onMouseOver=\"MM_swapImage('editImage6','','../images/".$admininfo["language"]."/webedit/wtool6_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool6.gif' name='editImage6' width='73' height='22' border='0' id='editImage6'></a></td>
											</tr>
											<tr>
											  <td height='2'></td>
											</tr>
											<tr>
											  <td height='27' align='center' valign='top'><a href='javascript:doTable();' onMouseOver=\"MM_swapImage('editImage13','','../images/".$admininfo["language"]."/webedit/wtool13_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool13.gif' name='editImage13' width='73' height='22' border='0' id='editImage13'></a></td>
											</tr>
										  </table>
											 </td>
										<td width='2'><img src='../images/".$admininfo["language"]."/webedit/bar.gif' width='2' height='39' align='absmiddle'></td>
										<td width='25%'>
										  <table width='100%' border='0' cellspacing='0' cellpadding='0'>
											<tr>
											  <td height='27' align='center' valign='bottom'><a href='javascript:doLink();' onMouseOver=\"MM_swapImage('editImage7','','../images/".$admininfo["language"]."/webedit/wtool7_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool7.gif' name='editImage7' width='74' height='22' border='0' id='editImage7'></a></td>
											</tr>
											<tr>
											  <td height='2'></td>
											</tr>
											<tr>
											  <td height='27' align='center' valign='top'><a href='javascript:doMultilink();' onMouseOver=\"MM_swapImage('editImage14','','../images/".$admininfo["language"]."/webedit/wtool14_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../images/".$admininfo["language"]."/webedit/wtool14.gif' name='editImage14' width='111' height='22' border='0' id='editImage14'></a></td>
											</tr>
										  </table>
										</td>
									  </tr>
									 </table>
								  </td>
								</tr>
							  </table>
							<table width='100%' border='0' cellspacing='3' cellpadding='0' >
								<tr>
								<td  bgcolor='#ffffff'>
									<textarea name=\"basicinfo\"  style='display:none' >".$basicinfo."</textarea>
									<input type='hidden' name='content' value=''>
									<iframe align='right' id='iView' style='width:100%; height:510px;' scrolling='YES' hspace='0' vspace='0'></iframe>
								</td>
								</tr>
							</table>
							  <!-- html편집기 메뉴 종료 -->
					  </td>
					</tr>
		</table>
		<table cellpadding=0 cellspacing=0 width=100%;>
        <tr>
	       <td bgcolor='#ffffff' align=right>
			  <a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
		      <a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
          </td>
        </tr>
        <tr height=60>
			<td bgcolor='#ffffff' align=right style='padding:0px;'>
				<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle valign='top'>
				<a href='#'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle valign='top'></a>
			</td>
		</tr>
        <textarea name='mall_companyinfo'  style='display:none'>".$mc_mail_text."</textarea></form>
      </table>";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >해당항목에 맞는 정보를 수정해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >{mem_name} , {shop_name} 등 {}(대괄호) 안에 있는 내용은 치환코드로 프로그램에서 해당정보와 맞는 정보로 치환됩니다</td></tr>

</table>
";


$help_text = HelpBox("게시판 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:-9px;'><table><tr><td valign=bottom><b>이메일 설정하기</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_메일SMS리스트관리(090322)_config.xml',800,517,'manual_view')\"  title='메일/SMS 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a></td></tr></table></div>", $help_text,200)."</div>";
*/
$Contents = $Contents.$help_text;

$P = new LayOut;
$P->addScript = "<script language='JavaScript' src='../webedit/webedit.js'></script>\n$Script";
$P->OnloadFunction = "init();MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')";//showSubMenuLayer('storeleft');
$P->strLeftMenu =  reseller_menu();
$P->Navigation = "리셀러관리 > 리셀러설정 > 리셀러화면설계";
$P->title = "리셀러화면설계";
$P->strContents = $Contents;
$P->PrintLayOut();

?>
