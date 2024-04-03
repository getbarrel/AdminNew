<? include("../admin_header.php");?>
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
				<form name='login_frm' action='./member.act.php' method='post' onsubmit='return CheckFormValue(this);' target='act'>
				<input type='hidden' name='act' value='seller_join'>
					<table cellspacing="0" cellpadding="0" border="0" >
						<col width='86'>
						<col width='200'>
						<col width='86'>
						<tr>
							<th>
								<img src="/admin/v3/images/common/daiso_admin_id.png" alt="" />
							</th>
							<td>
								<input type="text" name="id" id="id" value="" tabindex="1" style="vertical-align:middle;font-weight:bold;font-size:16px; width:194px;  padding:4px 0px 3px; padding-left:4px;" align="absmiddle" validation="true" title='아이디'>
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
								<input type="password" name="pw" id="pw" value="" tabindex="1" style="vertical-align:middle;font-weight:bold;font-size:16px; width:194px; padding:4px 0px 3px; padding-left:4px;" align="absmiddle"  validation="true" title="비밀번호">
							</td>
						</tr>
					</table>
				</form>
				</div>
			</div>
		</div>
	</div>
	<iframe name="act" id="act" style="display:none;"></iframe>
<? include("../admin_copyright.php");?>