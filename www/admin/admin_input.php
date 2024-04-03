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
<script language='JavaScript' src='/admin/js/jquery-1.4.js'></Script>
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
				<h2><img src="/admin/v3/images/member/admin_join_title2.gif" alt="" /></h2>
				<div class='daiso_admin_input'>
					<h4>사업자 정보 입력</h4>
					<div class='daiso_admin_table'>
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<col width='176'>
							<col width='*'>
							<tr>
								<th>
									<div>
									주요상품군 선택 <span>*</span>
									</div>
								</th>
								<td>
									<div class='daiso_admin_height'>
										<div class='daiso_admin_select'>
											<select name="pcs_div" id="pcs_div_val" style="border:0px;width:100%;" validation="true" title="통신사" numeric="true">
												<option>선택</option>
												<option value="SKT">SKT</option>
												<option value="KT">KT</option>
												<option value="LGT">LGT</option>
											</select>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<th>
									<div>
									주요판매상품<br/>내용 작성 <span>*</span>
									</div>
								</th>
								<td>
									<div class='daiso_admin_height'>
										<textarea name="lastmessage"></textarea>
									</div>
								</td>
							</tr>
							<tr>
								<th>
									<div>
									사업자등록증 <span>*</span>
									</div>
								</th>
								<td>
									<div class='daiso_admin_height' style='height:26px;'>
										<input type="text" id="fileName" class="file_input_textbox app_table_text" style='width:177px; padding:4px; margin:0px;'readonly="readonly">
										<input type="button" value="Search files" class="file_input_button" onClick="file_input_click(this)" />
										<input type="file" class="file_input_hidden" name="receipt_file" validation="true" title="" onchange="javascript:$('.file_input_textbox').eq($('.file_input_hidden').index($(this))).val($(this).val())"  />
									</div>
								</td>
							</tr>
							<tr>
								<th>
									<div>
									기타자료
									</div>
								</th>
								<td>
									<div class='daiso_admin_height' style='height:26px;'>
										<input type="text" id="fileName" class="file_input_textbox app_table_text" style='width:177px; padding:4px; margin:0px;'readonly="readonly">
										<input type="button" value="Search files" class="file_input_button" onClick="file_input_click(this)" />
										<input type="file" class="file_input_hidden" name="receipt_file" validation="true" title="" onchange="javascript:$('.file_input_textbox').eq($('.file_input_hidden').index($(this))).val($(this).val())"  />
									</div>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class='daiso_admin_buttom'>
					<ul>
						<li>
							<a href="/admin/member/admin_join_end.php"><img src="/admin/v3/images/member/admin_join_ok.gif" alt="" /></a>
						</li>
						<li style='margin-left:7px;'>
							<a href="javascript:history.go(-1)"><img src="/admin/v3/images/member/admin_join_no.gif" alt="" /></a>
						</li>
					</ul>
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
<script type="text/javascript">
<!--
	function file_input_click(tg) {
	var idx=$(".file_input_button").index($(tg));
	$(".file_input_hidden").eq(idx).trigger('click');
	}
//-->
</script>
</body>
</html>