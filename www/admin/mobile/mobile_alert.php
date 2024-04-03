<link rel='stylesheet' type='text/css' href='../v3/css/common.css' />
<script type="text/javascript">
<!--
	//버튼 삭제
	$("#facebox").find("a.close").hide();
//-->
</script>
<div class="goods_input_alert" style="width:290px;">
	<ul class="goods_input_alert_text">
		<?if($msg_type=="page_ready"){?>
			<li>준비중입니다.</li>
		<?}elseif($msg_type=="no_product_update"){?>
			<li>상품상세내역 혹은 수정은 <br/>웹 관리자에서만 가능합니다.</li>
		<?}else{?>

		<?}?>
		<li><input type="image" src="./images/confirm_y.png" width="30%" onclick="$(document).trigger('close.facebox');" /></li>
	</ul>
	<input type="image" src="./images/delete_check.png" alt="닫기" class="facebox_close" onclick="$(document).trigger('close.facebox');" />
</div>

<!--div class="goods_input_alert">
	<ul class="goods_input_alert_text">
		<li>
			필수항목이 모두 입력되지 않아<br />
			상품등록이 불가능합니다.<br />
			입력항목으로 이동합니다.<br />
		</li>
		<li><input type="image" src="./images/confirm_y.png" width="30%" /></li>
	</ul>
	<input type="image" src="./images/delete_check.png" alt="닫기" class="facebox_close" onclick="$(document).trigger('close.facebox');" />
</div>
<div class="goods_input_alert02">
	<ul class="goods_input_alert_text">
		<li>
			상품이 정상 등록되었습니다.<br />
			메인페이지로 이동합니다.<br />
		</li>
		<li><input type="image" src="./images/confirm_y.png" width="20%" /></li>
	</ul>
	<input type="image" src="./images/delete_check.png" alt="닫기" class="facebox_close" onclick="$(document).trigger('close.facebox');" />
</div>

<div class="goods_input_alert02">
	<ul class="goods_input_alert_text">
		<li>
			로그아웃을 하시겠습니까?<br />
			(현재 ID scpar***)<br />
		</li>
		<li><input type="image" src="./images/confirm_y.png" width="20%" /></li>
	</ul>
	<input type="image" src="./images/delete_check.png" alt="닫기" class="facebox_close" onclick="$(document).trigger('close.facebox');" />
</div>

<div class="goods_input_alert02">
	<ul class="goods_input_alert_text">
		<li>
			현재 로그인 상태가 아닙니다.<br />
		</li>
		<li><input type="image" src="./images/confirm_ok.png" width="20%" /></li>
	</ul>
	<input type="image" src="./images/delete_check.png" alt="닫기" class="facebox_close" onclick="$(document).trigger('close.facebox');" />
</div-->
