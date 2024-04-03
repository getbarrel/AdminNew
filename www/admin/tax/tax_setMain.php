<?
	include("../class/layout.class");
 	$db = new Database;

	//include_once $DOCUMENT_ROOT."/admin/tax/test_header.php";

	$id = "forbiz";		// 계정아이디

	$Contents = "
	<script src='tax.js'></script>
	<script src='/admin/js/calendar.js'></script>
	<script>
	$('#tab8_view').attr('style','display:');

	$(document).ready(function(){
		$('#sLogin').submit(function(){
			if($('#pwd').val() == '')
			{
				alert ('패스워드를 입력해주세요');
				$('#pwd').focus();
				return false;
			}
		});
	})
	</script>
	";

	$Contents .= "
	<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
			<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("전송설정", "국세청전송 > 전송설정 ")."</td>
		</tr>
	</table>

	<table width='100%' cellpadding=0 cellspacing=2 border=0 bgcolor='#CCCCCC'>
		<tr>
			<td bgcolor='#FFFFFF'>

				<table width='100%'>
					<tr>
						<td width='50%'>

							<table width='80%' cellpadding=0 cellspacing=1 border=0 bgcolor='#CCCCCC' align='center' style='margin:20px 20px 20px 20px'>
								<tr height='150'>
									<td bgcolor='#FFFFFF'>
									</td>
								</tr>
							</table>

						</td>
						<td width='50%' valign='bottom'>

							<table width='80%' cellpadding=0 cellspacing=1 border=0 bgcolor='#CCCCCC' style='margin:20px 20px 20px 20px'>
								<tr>
									<td bgcolor='#FFFFFF'>

										<form name='sLogin' id='sLogin' method='post' action='tax_setup.php'>
										<input type='hidden' name='id' id='id' value='".$id."'>

										<table>
											<tr height='30'>
												<td width='80' style='padding:10px 10px 10px 10px' align='center'>아이디</td>
												<td style='padding:10px 10px 10px 10px'>".$id."</td>
											</tr>
											<tr height='30'>
												<td style='padding:10px 10px 10px 10px' align='center'>비밀번호</td>
												<td style='padding:10px 10px 10px 10px'><input type='password' name='pwd' id='pwd' size='14'> <input type='submit' value='로그인'> </td>
											</tr>
										</table>
										</form>

									</td>
								</tR>
							</table>

						</td>
					</tr>
					<tr>
						<td style='padding:20px 40px 20px 40px;line-height:20px' colspan='2'>외부로부터 정보를 안전하게 보호하기 위해 비밀번호를 다시 한번 확인합니다.</br>항상 비밀번호는 타인에게 노출되지 않도록 주의해 주세요.</td>
					</tr>

				</table>

			</td>
		</tr>
	</table>
	";

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = tax_menu();
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->Navigation = "세금계산서관리 > 국세청전송 > 전송설정";
	$P->title = "전송설정";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
?>