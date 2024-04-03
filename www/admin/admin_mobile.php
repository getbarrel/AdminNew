<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>몰스토리관리자_메인</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="{title_desc}" />
<meta name="keywords" content="{keyword_desc}" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" />
<link rel='stylesheet' type='text/css' href='./v3/css/class.css' />
<link rel='stylesheet' type='text/css' href='./v3/css/common.css' />
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
							<!--ul>
								<li >
									<a href="#"><img src="v3/images/btns/login_btn.gif" alt="로그인" title="로그인" /></a>
								</li>
								<li class="top_menu_list01">
									<a href="https://www.mallstory.com/customer/bbs.php?mode=list&board=notice" target=_blank>공지사항 <img src="v3/images/btns/new_icon.gif" alt="" title="" /></a>
								</li>
								<li class="top_menu_list01">ㅣ</li>
								
								<li class="top_menu_list01">
									<a href="http://www.mallstory.com" target=_blank>몰스토리</a>
								</li>
							</ul-->
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td class="topmenu_bg01"></td></tr>
	<tr>
		<td align="center" style="background:url(v3/images/common/login_bg.gif) repeat-x;">
			<div style='margin:20px;'>
				<table cellpadding="0" cellspacing="0" border="0" width="100%" >
					<tr>
						<td align="left">
							<div class="">
							<form name="login_frm" action="" onsubmit="return CheckFormValue(this);" method="POST"> <input type=hidden name="act" value="verify">
								<h1 style="border-bottom:solid 2px #f16700;padding-bottom:15px;"><img src="v3/images/common/login_title01.gif" alt="몰스토리관리자" title="몰스토리관리자" /></h1>
								<ul class="login_box" style="padding:10px 0 0 0;">
									<li>
										<input type="text" name="id" value="<?=$_COOKIE['ck_adminSaveID']?>" tabindex=1 style="vertical-align:middle;padding:8px 10px;width:165px;font-weight:bold;font-size:16px;" align="absmiddle" /> 
										<input type="checkbox" id="chk_saveID" name="chk_saveID" value="Y"<?=($_COOKIE['ck_adminSaveID'])	?	' checked':'';?> style="vertical-align:middle;" align="absmiddle"/><label for="chk_saveID" style="vertical-align:middle;font-weight:bold;letter-spacing:-1px;">아이디 저장</label>
									</li>
									<li>
										<input type=password name="pw" value='' tabindex=2 style="vertical-align:middle;padding:8px 10px;width:165px;font-weight:bold;font-size:16px;" /> <input type=image src="v3/images/btns/login_btn02.gif" alt="로그인버튼" title="몰스토리관리자" align="absmiddle" style="vertical-align:middle;margin-left:3px;"/>
									</li>
									<li style="padding:9px 0;">
										<a href="http://www.mallstory.com/member/join_agreement.php"><strong style='color:#000000'>회원가입</strong></a> ㅣ <a href="https://www.mallstory.com/member/search_idpw.php" style='color:#000000'>아이디/비밀번호찾기</a>
									</li>
								</ul>
								<ul class="callcenter_box">
									<li>
										<img src="v3/images/common/login_title03.gif" alt="고객센터" title="고객센터" />
									</li>
									<li style='font-family:돋움'>
										09:00~18:00 (토요일,일요일,공휴일 휴무)
									</li>
									<li style="padding-top:7px;">
										<a href="http://www.mallstory.com/customer/"><img src="v3/images/btns/on_line_ask_btn.gif" alt="온라인문의" title="온라인문의" align="absmiddle" style="vertical-align:middle;"/>
										<a href="https://www.mallstory.com/customer/bbs.php?board=faq"><img src="v3/images/btns/faq_btn.gif" alt="FAQ" title="FAQ" align="absmiddle" style="vertical-align:middle;"/>
									</li>
								</ul>
								</form>
							</div>
						</td>
						<!--td align="right" valign="bottom"><img src="v3/images/common/login_img01.jpg" title="" alt="" style="width:517px;height:403px;"/></td-->
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td align="center" style="padding:10px 0;font-family:돋움">
			Copyright ⓒ <strong>Mallstory</strong>. All Rights Reserved.
		</td>
	</tr>
</table>
</body>
</html>
