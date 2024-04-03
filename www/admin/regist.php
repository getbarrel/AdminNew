<?include("./include/admin.util.php");?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>몰스토리관리자_메인</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="{title_desc}" />
<meta name="keywords" content="{keyword_desc}" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
<LINK REL='stylesheet' HREF='./v3/include/admin.css' TYPE='text/css'>
<link rel='stylesheet' type='text/css' href='./v3/css/class.css' />
<link rel='stylesheet' type='text/css' href='./v3/css/common.css' />
<!--LINK REL='stylesheet' HREF='./css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='./common/css/design.css' TYPE='text/css'>
<LINK href='./css/facebox2.css' type='text/css' rel='stylesheet'-->
<Script Language='JavaScript'>
</Script>
<style type='text/css'>
	a img {
		border: none;
	} #largeImage {
		position: absolute;
		padding: .5em;
		background: #e3e3e3;
		border: 1px solid;
	}
	.textbox{
		vertical-align:middle;padding:8px 10px;width:165px;font-weight:bold;font-size:16px;
	}
	.input_title{
		vertical-align:middle;padding:8px 10px;width:165px;font-weight:bold;font-size:12px;
	}
</style>
<script language='JavaScript' src='./js/jquery-1.4.js'></Script>
<script language='javascript' src='./js/jquery.blockUI.js'></script>
<script language='JavaScript' src='./js/auto.validation.js'></Script>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" width="100%" >
	<tr>
		<td class="top_menu_area" align="left">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" >
				<col width="*" />
				<col width="23%" />
				<tr>
					<td>
						<div class="left_menu01" > 
							<img src="v3/images/common/logo_img01.gif" alt="몰스토리" title="몰스토리" />
							<img src="v3/images/common/logo_img02.gif" alt="관리자" title="관리자" />
						</div>
					</td>
					<td align="right">
						<div class="top_menu">
							<ul>
								<li >
									<a href="#"><img src="v3/images/btns/login_btn.gif" alt="로그인" title="로그인" /></a>
								</li>
								<li class="top_menu_list01">
									<a href="https://www.mallstory.com/customer/bbs.php?mode=list&board=notice" target=_blank>공지사항 <img src="v3/images/btns/new_icon.gif" alt="" title="" /></a>
								</li>
								<li class="top_menu_list01">ㅣ</li>
								<!--li class="top_menu_list01">
									<a href="#">도움말</a>
								</li>
								<li class="top_menu_list01">ㅣ</li-->
								<li class="top_menu_list01">
									<a href="http://www.mallstory.com" target=_blank>몰스토리</a>
								</li>
							</ul>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td class="topmenu_bg01"></td></tr>
	<tr>
		<td align="center" style='padding-top:20px;'><!--style="background:url(v3/images/common/login_bg.gif) repeat-x;height:588px;"-->
			<div>
				<form name="login_frm" action="" onsubmit="return CheckFormValue(this);" method="POST"> <input type=hidden name="act" value="verify">
				<table cellpadding="0" cellspacing="4" border="0" width="848" >
					<col width="288" />
					<col width="*" />
					<tr bgcolor=#ffffff height=30>
						<td  colspan=2 style='text-align:left;'>
						<?=GetTitleNavigation("셀러 회원가입", "회원가입 > 셀러 회원가입 ","")?>
						</td>
				   </tr>
				   <tr  height=30>
						<td  colspan=2 style='text-align:left;backgorund-color:#efefef;'>
						<div style='padding:5px 5px 5px 5px;'><img src='./image/title_head.gif' align=absmiddle> <b style='color:#000000;'>회원정보</b></div>
						</td>
				   </tr>
				</table>
				<table cellpadding="0" cellspacing="4" border="0" width="848" class='input_table_box'>
					<col width="288" />
					<col width="*" />
					
				   <tr bgcolor=#ffffff height=35>
						<td class='input_box_title'> <b  class='input_title'>이름 </b></td>
						<td class='input_box_item'><input type=text name='name' value='' class='textbox' style="width:165px;"  validation=true title='이름'></td>
				   </tr>
				   <tr bgcolor=#ffffff height=35>
						<td class='input_box_title'> <b class='input_title'>아이디 </b></td>
						<td class='input_box_item'><input type=text name='id' value='' class='textbox' style="width:165px;"  validation=true title='아이디'></td>
				   </tr>
				   <tr bgcolor=#ffffff  height=30>
					<td class='input_box_title'> <b  class='input_title'>이메일 </b></td>
					<td class='input_box_item'><input type=text name='mail' value='' class='textbox'  style='width:300px' title='이메일' validation='true' email='true'></td>
				  </tr>
				  <tr bgcolor=#ffffff  height=30>
					<td class='input_box_title'> <b  class='input_title'>담당자 핸드폰 </b></td>
					<td class='input_box_item'>
						<input type=text name='pcs' value=''  style='width:200px;' class='textbox' validation='true' title='담당자 핸드폰' numeric='true'>
					</td>
				  </tr>
				  <tr bgcolor=#ffffff  height=30>
					<td class='input_box_title'> <b  class='input_title'>패스워드</b></td>
					<td class='input_box_item' nowrap>
						<input type=password name='pw' value='' size=12 style='width:200' class='textbox' >
					</td>
				  </tr>
				  <tr bgcolor=#ffffff  height=30>
					<td class='input_box_title'> <b  class='input_title'>패스워드 확인</b> </td>
					<td class='input_box_item' nowrap><input type=password name='pw_confirm' value='' size=12 class='textbox'  style='width:200px' ></td>
				  </tr>
				  <tr bgcolor=#ffffff height=30>
					<td class='input_box_title'> <b  class='input_title'>담당자전화</b></td>
					<td class='input_box_item'>
						<input type=text name='tel' value=''  style='width:200px;'  class='textbox' >
					</td>
				   </tr>
				</table>
				<table cellpadding="0" cellspacing="4" border="0" width="848" >
					<col width="288" />
					<col width="*" />
					
				   <tr  height=30>
						<td  colspan=2 style='text-align:left;backgorund-color:#efefef;'>
						<div style='padding:25px 5px 10px 5px;'><img src='./image/title_head.gif' align=absmiddle> <b style='color:#000000;'>사업자정보</b></div>
						</td>
				   </tr>
				</table>
				<table cellpadding=0 cellspacing=4 width="848" class='input_table_box'>
				  <colgroup>
					<col width='288' />
					<col width='*' />
				  </colgroup>
				  <tr>
					<td class='input_box_title'><b class='input_title'>사업자번호</b></td>
					<td class='input_box_item'>
						<input type=text name='com_number' value='' class='textbox'  style='width:80px' validation='false' title='사업자번호'>
						<div style='display:inline;padding:2px;' class=small>예) XXX-XX-XXXXX</div>
					</td>
				  </tr>
				  <tr>
					<td class='input_box_title'> <b class='input_title'>기업형태 </b>   </td>
					<td class='input_box_item'>
						<input type='radio' name='com_div' id='com_div_p' value='P' validation=true title='개인' checked><label for='com_div_p'>개인</label> &nbsp;&nbsp;
						<input type='radio' name='com_div' id='com_div_r' value='R' validation=true title='법인' ><label for='com_div_p'>법인</label>
					</td>
				  </tr>
				  <tr>
					<td class='input_box_title'> <b class='input_title'>사업자명 </b></td>
					<td class='input_box_item'>
					<input type=text name='com_name' value='' class='textbox'  style='width:200px' validation='true' title='사업자명'>
					</td>
				  </tr>
				  <tr>
					<td class='input_box_title'> <b class='input_title'>업태</b>   </td>
					<td class='input_box_item'><input type=text name='com_business_status' value='' class='textbox'  style='width:200px' validation='false' title='업태'></td>
				  </tr>
				  <tr>
					<td class='input_box_title'> <b class='input_title'>대표자명 </b>   </td>
					<td class='input_box_item'><input type=text name='com_ceo' value='' class='textbox'  style='width:200px' validation='true' title='대표자명'></td>
				  </tr>
				  <tr>
					<td class='input_box_title' > <b class='input_title'>업종</b>   </td>
					<td class='input_box_item'><input type=text name='com_business_category' value='' class='textbox'  style='width:200px' validation='false' title='업종'></td>
				  </tr>
				  <tr>
					<td class='input_box_title'> <b class='input_title'>대표전화 </b></td>
					<td class='input_box_item'>
						<input type=text class='textbox' name='com_phone' value=''  style='width:200px' validation='true' title='대표전화' com_numeric=true>
					</td>
				  </tr>
				  <tr>
					<td class='input_box_title'> <b class='input_title'>대표팩스</b></td>
					<td class='input_box_item'>
						<input type=text class='textbox' name='com_fax' value=''  style='width:200px'>
					</td>
				  </tr>
					<tr>
					<td class='input_box_title'><b class='input_title'>통신판매업 번호</b></td>
					<td class='input_box_item'><input type=text name='online_business_number' value='' class='textbox'  style='width:200px' validation='false' title='통신판매업 번호'></td>
				  </tr>
				  <tr>
					<td class='input_box_title'> <b class='input_title'>대표이메일 </b></td>
					<td class='input_box_item'><input type=text name='com_email' value='' class='textbox'  style='width:200px' validation='false' title='대표이메일' email=true></td>
				  </tr>
				  <tr>
					<td class='input_box_title'> <b class='input_title'>회사 주소</b>    </td>
					<td class='input_box_item' >
						
						<table border='0' cellpadding='4' cellspacing='0' style='table-layout:fixed;width:100%'>
							<col width='190px'>
							<col width='*'>
							<tr>
								<td height=26>
									<input type='text' class='textbox' name='com_zip' id='com_zip' size='15' maxlength='15' value='' readonly>
								</td>
								<td style='padding:1px 0 0 5px;'>
									<img src='./images/korea/btn_search_address.gif' onclick='zipcode('4');' style='cursor:pointer;'>
								</td>
							</tr>
							<tr>
								<td colspan=2 height=26>
									<input type=text name='com_addr1'  id='com_addr1' value='' size=50 class='textbox'  style='width:75%'>
								</td>
							</tr>
							<tr>
								<td colspan=2 height=26>
									<input type=text name='com_addr2'  id='com_addr2'  value='' size=70 class='textbox'  style='width:450px'> (상세주소)
								</td>
							</tr>
							</table>
						</td>
				  </tr>
				</table>
				<table cellpadding=0 cellspacing=4 width="848" >
				  <tr>
						<td align="center" colspan=2 style='padding:20px;'>
								<input type=image src="v3/images/btns/login_btn01.gif" alt="로그인버튼" title="몰스토리관리자" align="absmiddle" style="vertical-align:middle;margin-left:3px;"/>
						</td>
					</tr>
				  </table>
				</form>
			</div>
		</td>
	</tr>
	<tr>
		<td >
		
		</td>
	</tr>
	<tr>
		<td align="center" style="padding:10px 0;font-family:돋움">
			Copyright ⓒ <strong>Mallstory</strong>. All Rights Reserved.
		</td>
	</tr>
</table>
<div id='loading' style='display:none;border:0px solid red;width:100px;height:100px;padding-top:13px;text-align:center;'><img src='./images/loading_large.gif' border=0></div>
<div id='layerBg' style='border:0px solid gray;'></div>
<script>
//	$.blockUI.defaults.css = {}; 
//	$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} });  
</script>
</body>
</html>
<!--
<html>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title></title>
<style>
TD{font-size:12px;font-family:돋움}
.bg_line {background: url(./image/bg_line.gif) no-repeat left top; }
.bg_color2 {background: url(./image/bg_color.gif) repeat-x left top; }
.bg_main {background: url(./image/bg.gif) no-repeat left top; }
.bg_login {background:url(./image/bg_login.gif) no-repeat center top; }
</style>
<Script Language='JavaScript'>
var language = "<?=$admininfo[language]?>";
function focusIn()
{
    document.login_frm.id.focus();
}
window.onload=focusIn;
</Script>
<script language='JavaScript' src='./js/jquery-1.4.js'></Script>
<script language='javascript' src='./js/jquery.blockUI.js'></script>
<script language='JavaScript' src='./js/auto.validation.js'></Script>
<body topmargin=0 leftmargin=0 class="bg_color2">
<table cellpadding=0 border=0 cellspacing=0 width="100%" height="100%" class="bg_login">
	<tr height=290><td></td></tr>
	<tr>
		<td valign=top align=center style="padding-left:150px;">
			<div style="position:relative;width:300px;hegiht:200px;"  align=center >
			<form name="login_frm" action="" onsubmit="return CheckFormValue(this);" method="POST"> <input type=hidden name="act" value="verify">
			<table cellpadding=0 border=0 cellspacing=0 width="280" height="50" >
				<tr height=24>
					<td width=60>아이디 </td>
					<td width=100><input type=text name="id" value="<?=$_COOKIE['ck_adminSaveID']?>" style='width:120px;border:1px solid silver;ime-mode:disabled ;' onfocus="this.style.border='2px solid orange'" onfocusout="this.style.border='1px solid silver'" validation='true' title='아이디' tabindex=1> </td>
					<td width=78 rowspan=2><input type=image src="./image/btn_login.gif" size=14 tabindex=3> </td>
				</tr>
				<tr height=24>
					<td>비밀번호 </td>
					<td><input type=password name="pw" style='width:120px;border:1px solid silver' onfocus="this.style.border='2px solid orange'" onfocusout="this.style.border='1px solid silver'"  validation='true' title='비밀번호' tabindex=2> </td>
				</tr>
				<tr height="24">
					<td colspan="2"><input type="checkbox" id="chk_saveID" name="chk_saveID" value="Y"<?=($_COOKIE['ck_adminSaveID'])	?	' checked':'';?> /> <label for="chk_saveID">아이디 저장</label></td>
				</tr>
			</table>
			</form>
			</div>
		</td>
	</tr>
	<tr height=200><td></td></tr>
</table>
<div id='loading'></div>
</body>
</html>
-->