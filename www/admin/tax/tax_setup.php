<?
	include("../class/layout.class");
 	$db = new Database;

	include_once $DOCUMENT_ROOT."/admin/tax/test_header.php";
	
	if($s_type == "total" || $s_type == "")			$checked1 = "checked";
	if($s_type == "r_company_number")				$checked2 = "checked";
	if($s_type == "r_company_name")					$checked3 = "checked";

	$Contents = "
	<script src='tax.js'></script>
	<script src='/admin/js/calendar.js'></script>
	<script>
	$('#tab8_view').attr('style','display:');
	$(document).ready(function(){
		$('input[name=\"skind\"]').click(function(){
			var skind_v = $('input[name=\"skind\"]:checked').val();
			if(skind_v == 1)	$('#view1').attr('style','display:');
			else				$('#view1').attr('style','display:none');
		});
	});
	</script>
	";

	$Contents .= "
	<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
			<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("전송설정", "국세청전송 > 전송설정 ")."</td>
		</tr>
	</table>

	<table width='100%' cellpadding=0 cellspacing=1 border=0 bgcolor='#CCCCCC' style='margin:0 0 10px 0'>
		<tr>
			<td bgcolor='#F2F2F2' style='padding:20px 20px 20px 20px' width='100'>전송방법</td>
			<td bgcolor='#FFFFFF' style='line-height:30px;padding:0px 20px 0px 20px'>
				국세청 전송 방법을 설정하세요.</br>
				<input type='radio' name='skind' id='skind' value='1' checked> 자동수정 </br>
				<input type='radio' name='skind' id='skind' value='2'> 수동전송 - <font color='red'>[국세청전송 → 전송하기] 에서 직접 전송하셔야 합니다.</font>
			</td>
		</tr>
	</table>

	<table id='view1' width='100%' cellpadding=0 cellspacing=1 border=0 bgcolor='#CCCCCC'>
		<tr>
			<td bgcolor='#F2F2F2' style='padding:20px 20px 20px 20px' width='100'>마감일자</td>
			<td bgcolor='#FFFFFF' style='line-height:30px;padding:0px 20px 0px 20px'>
				익월 
				<select name='p_day' id='p_day'>
					<option value=''>마감일자</option>
					<option>1</option>
					<option>2</option>
					<option>3</option>
					<option>4</option>
					<option>5</option>
					<option>6</option>
					<option>7</option>
				</select>
				에 월 마감 후, 마감일의 익일 새벽 01시에 자동 전송됩니다.
			</td>
		</tr>
		<tr>
			<td bgcolor='#F2F2F2' style='padding:20px 20px 20px 20px' width='100'>전송옵션</td>
			<td bgcolor='#FFFFFF' style='padding:10px 20px 10px 20px'>
				<span style='line-height:20px;'>
				[발행완료]된 세금계산서를 마감일자에 따라 국세청으로 일괄전송하고 있습니다.</br>
				그 외의 경우는 아래에 설정하신 값에 따라 전송됩니다.</br></br>
				<input type='checkbox' name='' id='' value=''> [미개봉]이거나 [승인요청] 중인 세금계산서는 강제 승인 처리하여 국세청에 전송</br>
				<input type='checkbox' name='' id='' value=''> [취소요청] 중인 세금계산서는 취소요청을 취소하여 [발행완료] 상태로 만든후 국세청에 전송
				</span>
			</td>
		</tr>
	</table>

	<table width='100%' style='margin:10px 0 0 0'>
		<tR>
			<td align='center'><img src='/admin/image/b_save_.gif'></td>
		</tr>
	</table>
	";

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = tax_menu();
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->Navigation = "HOME > 국세청전송 > 전송하기";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
?>