<link rel='stylesheet' type='text/css' href='../v3/css/common.css' />
<script language='JavaScript' src='../js/jquery-1.7.1.min.js'></Script>
<script language='JavaScript' src='../js/jquery.imagetick.min.js'></Script>
<div class="service_qna">
	<div style="margin:0 10px;">
		<h1><img src="./images/service_qna_h1.png" alt="서비스문의 및 신고" width="60%" /></h1>
		<div class="radio_btns">
			&nbsp;<input type="radio" name="inquiry" id="inquiry" class="btn_radio" checked /><label for="inquiry" style="margin-right:30px;"><img src="./images/inquiry.png" alt="문의하기" width="46" /></label>
			<input type="radio" name="report" id="report"  class="btn_radio" /><label for="report"><img src="./images/report.png" alt="신고하기" width="46" /></label>
		</div>
		<table cellspacing="" cellpadding="0" border="0" class="service_qna_table" width="100%">
		<col width="25%" />
		<col width="75%" />
			<tr><td colspan="2" height="10"></td></tr>
			<tr>
				<th><b>성명</b>&nbsp;<img src='./images/img_checked.png' width='11' align="absmiddle" /></th>
				<td><input type='text' name='' /></td>
			</tr>
			<tr>
				<th><b>이메일</b></th>
				<td><input type='text' name='' /></td>
			</tr>
			<tr>
				<th><b>연락처</b>&nbsp;<img src='./images/img_checked.png' width='11' align="absmiddle" /></th>
				<td><input type='text' name='' /></td>
			</tr>
			<tr>
				<th valign="top" style="padding-top:5px;"><b>내용</b></th>
				<td><textarea></textarea></td>
			</tr>
			<tr><td colspan="2" height="10"></td></tr>
		</table>
		<p class="btn_service_qna"><input type="image" src="./images/btn_service_qna.png" /></p>
		<input type="image" src="./images/delete_check.png" alt="닫기" class="facebox_close" onclick="$(document).trigger('close.facebox');" />
	</div>
</div>

<script type="text/javascript">
<!--
	$("input.btn_radio").imageTick({
		tick_image_path: "./images/radio_bt_on.png",
		no_tick_image_path: "./images/radio_bt.png",
		image_tick_class: "btn_radio"
	});
//-->
</script>