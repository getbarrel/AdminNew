<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>몰스토리관리자_메인</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="{title_desc}" />
<meta name="keywords" content="{keyword_desc}" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
<LINK REL='stylesheet' HREF='/admin/v3/include/admin.css?".rand()."' TYPE='text/css'>
<link rel='stylesheet' type='text/css' href='/admin/v3/css/class.css' />
<link rel='stylesheet' type='text/css' href='/admin/v3/css/common.css' />
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td class="top_menu_area2" align="left">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<col width="*">
				<col width="23%">
				<tr>
					<td>
						<div class="left_menu01">
							<!--img src="v3/images/common/logo_img01.gif" alt="몰스토리" title="몰스토리" align=absmiddle  />
							<img src="/admin/v3/images/common/logo_img02.gif" alt="관리자" title="관리자" align=absmiddle / back-->
							<img src="/admin/v3/images/common/daiso_admin_title.png" alt="다이소몰셀러오피스" title="다이소몰셀러오피스" align="absmiddle">
						</div>
					</td>
					<td align="right">
						<div class="top_menu">
							<ul>
								<li>
									<!--a href="#"><img src="/admin/v3/images/btns/login_btn.gif" alt="로그인" title="로그인" /></a-->
									<a href="#"><img src="/admin/v3/images/common/daiso_admin_login.png" alt="로그인" title="로그인" align="absmiddle" style="margin-top:2px;"></a>
								</li>
								<li class="top_menu_list01">
									<a href="https://www.mallstory.com/customer/bbs.php?mode=list&amp;board=notice" target="_blank">공지사항 <!--img src="v3/images/btns/new_icon.gif" alt="" title="" /></a back--><img src="/admin/v3/images/common/daiso_admin_notice.png" alt="" title=""></a>
								</li>
								<li class="top_menu_list01">ㅣ</li>
								<!--li class="top_menu_list01">
									<a href="#">도움말</a>
								</li>
								<li class="top_menu_list01">ㅣ</li-->
								<li class="top_menu_list01">
									<!--a href="http://www.mallstory.com" target=_blank>몰스토리</a back-->
									<a href="http://daiso.forbiz.co.kr/" target="_blank">다이소몰 바로가기</a>
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
		<td align="center" style="height:714px; border-bottom:1px solid #cccccc; background:#fff url('/admin/v3/images/common/daiso_admin_background.png') 0 0 repeat-x;" valign="top">
			<div class="daiso_admin_margin">
				<h2><img src="/admin/v3/images/member/admin_join_title.gif" alt="" /></h2>
				<div class='admin_join_text'>
					<h3>다이소몰 협력사 가입을 원하시는 파트너사는 먼저 다이소몰 ‘사업자 회원’으로 가입을 해주셔야 합니다.</h3>
					<span class='admin_join_stext'>가입 후 협력사 요청을 작성해주시면, 기본 자료를 토대로 심사 후 해당 담당자가 승인처리 및<br /> 연락을 드리겠습니다. 감사합니다.</span>
					<span class='admin_join_stext2'>(협력사의 해당 자료가 있을 경우 첨부해주시면 빠른처리가 가능합니다.)</span>
					<a href="/member/join_agreement.php?join_type=C"><img src="/admin/v3/images/member/admin_join_buttom.gif" alt="사업자 회원가입" /></a>
				</div>
				<div class='id_pw_input'>
					<h4>신규 협력사 신청<span >(사업자회원 가입자만 신청할 수 있습니다.)</span></h4>
					<div class='id_pw_inputwrap'>
						<div class='id_pw_inputdiv'>
						<form name='seller_join' action='../login.php' method='post' onsubmit='return SubmitX(this);'>
						<input type='hidden' name=act value='".$act."'>
							<table cellspacing="0" cellpadding="0" border="0" >
								<col width='86'>
								<col width='200'>
								<col width='86'>
								<tr>
									<th>
										<img src="/admin/v3/images/common/daiso_admin_id.png" alt="" />
									</th>
									<td>
										<input type="text" name="id" value="" tabindex="1" style="vertical-align:middle;font-weight:bold;font-size:16px; width:194px;  padding:4px 0px 3px; padding-left:4px;" align="absmiddle">
									</td>
									<td rowspan='3' style='padding-left:7px;'>
										<input type=image src="/admin/v3/images/common/daiso_admin_login2.png" alt="로그인버튼" title="몰스토리관리자" align="absmiddle" style="vertical-align:middle;"/>
									<td>
								</tr>
								<tr>
									<td colspan='2' height='7px'></td>
								</tr>
								<tr>
									<th>
										<img src="/admin/v3/images/common/daiso_admin_pw.png" alt="" />
									</th>
									<td>
										<input type="text" name="pw" value="" tabindex="1" style="vertical-align:middle;font-weight:bold;font-size:16px; width:194px; padding:4px 0px 3px; padding-left:4px;" align="absmiddle">
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
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