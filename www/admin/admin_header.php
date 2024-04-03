<?
include_once($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");
?>

<!---------------------------------------------------------------------------다이소-------------------------------------------------------------------------------------->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>몰스토리관리자_메인</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="{title_desc}" />
<meta name="keywords" content="{keyword_desc}" />
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<LINK REL='stylesheet' HREF='/admin/v3/include/admin.css?".rand()."' TYPE='text/css'>
<link rel='stylesheet' type='text/css' href='/admin/v3/css/class.css' />
<link rel='stylesheet' type='text/css' href='/admin/v3/css/common.css' />
<!--LINK REL='stylesheet' HREF='./css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='./common/css/design.css' TYPE='text/css'>
<LINK href='./css/facebox2.css' type='text/css' rel='stylesheet'-->
<Script Language='JavaScript'>
var language = "<?=$admininfo[language]?>";
function focusIn()
{
	//document.login_frm.id.focus();
	$('input[name=id]').focus();
}
window.onload=focusIn;
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
</style>
<script language='JavaScript' src='/admin/js/jquery-1.4.js'></Script>
<script language='javascript' src='/admin/js/jquery.blockUI.js'></script>
<script language='JavaScript' src='/admin/js/auto.validation.js'></Script>

</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" width="100%" >
	<tr>
		<td class="top_menu_area2" align="left">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" >
				<col width="*" />
				<col width="23%" />
				<tr>
					<td>
						<div class="left_menu01" >
							<!--img src="v3/images/common/logo_img01.gif" alt="몰스토리" title="몰스토리" align=absmiddle  />
							<img src="v3/images/common/logo_img02.gif" alt="관리자" title="관리자" align=absmiddle / back-->
							<img src="/admin/v3/images/common/daiso_admin_title02.png" alt="다이소몰셀러오피스" title="다이소몰셀러오피스" align='absmiddle' />
						</div>
					</td>
					<td align="right">
						<div class="top_menu">
							<ul>
								<li>
									<!--a href="#"><img src="v3/images/btns/login_btn.gif" alt="로그인" title="로그인" /></a-->
									<a href="/admin/admin.php"><img src="/admin/v3/images/common/daiso_admin_login.png" alt="로그인" title="로그인" align='absmiddle' style='margin-top:2px;'/></a>
								</li>
								<li class="top_menu_list01">
									<a href="https://www.mallstory.com/customer/bbs.php?mode=list&board=notice" target=_blank>공지사항 <!--img src="v3/images/btns/new_icon.gif" alt="" title="" /></a back--><img src="/admin/v3/images/common/daiso_admin_notice.png" alt="" title="" /></a>
								</li>
								<li class="top_menu_list01">ㅣ</li>
								<!--li class="top_menu_list01">
									<a href="#">도움말</a>
								</li>
								<li class="top_menu_list01">ㅣ</li-->
								<li class="top_menu_list01">
									<!--a href="http://www.mallstory.com" target=_blank>몰스토리</a back-->
									<a href="http://daiso.forbiz.co.kr/" target=_blank>다이소몰 바로가기</a>
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
		<td align="center" style="height:714px; border-bottom:1px solid #cccccc; background:#fff url('/admin/v3/images/common/daiso_admin_background.png') 0 0 repeat-x;" valign='top'>