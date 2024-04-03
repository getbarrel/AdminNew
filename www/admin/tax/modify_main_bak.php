<?
	include("../class/layout.class");
 	$db = new Database;

	//include_once $DOCUMENT_ROOT."/admin/tax/test_header.php";


	# 타이틀
	$menu_title1 = "세금계산서 관리";
	$menu_title2 = "수정발행";

	$Contents = "
	<script>
	$(document).ready(function(){

	});

	function modi_pop(kind,vv)
	{
		if(kind == 1) var go_url = './pop.modify_select.php?tax_no=' + vv;
		if(kind == 2) var go_url = './pop.modify_select.php?publish_type=' + vv;

		window.open(go_url,'md','width=600,height=550');
	}

	function r_click(kk)
	{
		if(kk == 1)
		{
			$('#btn1').attr('style','display:');
			$('#btn2').attr('style','display:none');
			$('#btn3').attr('style','display:none');
		}
		if(kk == 2)
		{
			$('#btn1').attr('style','display:none');
			$('#btn2').attr('style','display:');
			$('#btn3').attr('style','display:none');
		}
		if(kk == 3)
		{
			$('#btn1').attr('style','display:none');
			$('#btn2').attr('style','display:none');
			$('#btn3').attr('style','display:');
		}
	}

	function taxNo_send()
	{
		if($('#taxNo').val() == '')
		{
			alert ('승인번호를 입력해주세요.');
			$('#taxNo').focus();
			return;
		}
		modi_pop('1',$('#taxNo').val());
		return;

		alert ('작업예정 : 해당 수정페이지로 이동');
		return;

		$('#frm').attr('action','');
		$('#frm').attr('method','post');
		$('#frm').submit();
	}

	function go_page()
	{
		var chk_val = $('input:radio[id=\'p_type\']:checked').val();
		modi_pop('2',chk_val);
	}
	</script>
	<LINK REL='stylesheet' HREF='./css/btn.css' TYPE='text/css'>
	";

	$Contents .= "
	<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr >
			<td align='left' colspan=6 > ".GetTitleNavigation("$menu_title2", "$menu_title1 > $menu_title2 ")."</td>
		</tr>
	</table>

	<form id='frm'>
	<table width='100%'>
		<tr>
			<td height='30' colspan='2'><b>Email 주소 착오기재나 미입력하여 발행한 경우(수정세금계산서 발행 대상이 아님)</b></td>
		</tr>
		<tr>
			<td width='20'></td>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width='100%' bgcolor='#CCCCCC'>
					<tr height='50' bgcolor='#FFFFFF'>
						<td width='100' bgcolor='#F2F2F2'></td>
						<td style='padding:5px 5px 5px 5px' bgcolor='#bfffdf'>매출/매입 문서조회에서 해당 문서를 조회, 수정한 후 재발송 하시기 바랍니다.</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height='60' colspan='2'><b>수정발행 문서 검색</b></td>
		</tr>
		<tr>
			<td><input type='radio' name='m_kind' id='m_kind1' onclick='r_click(1)' checked></td>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width='100%' bgcolor='#CCCCCC'>
					<tr height='80' bgcolor='#FFFFFF'>
						<td width='100' bgcolor='#F2F2F2'></td>
						<td style='padding:5px 5px 5px 5px' bgcolor='#bfffdf'>
							<table>
								<tr>
									<td><b>국세청 승인번호를 아는 경우 당초 승인번호 입력 후 작성</b></td>
								</tr>
								<tr>
									<td>수정할 전자세금계산서 승인번호를 입력하여 발행할 수 있습니다.</td>
								</tr>
								<tr height='35'>
									<td>승인번호 <input type='text' name='taxNo' id='taxNo' style='width:200px'> <span id='btn1'><img src='/admin/image/bt_ok.gif' align='absbottom' style='cursor:hand' onclick='taxNo_send()'></span> </td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><input type='radio' name='m_kind' id='m_kind2' onclick='r_click(2)'></td>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width='100%' bgcolor='#CCCCCC'>
					<tr height='80' bgcolor='#FFFFFF'>
						<td width='100' bgcolor='#F2F2F2'></td>
						<td style='padding:5px 5px 5px 5px' bgcolor='#bfffdf'>
							<table>
								<tr>
									<td><b>승인번호를 모르는 경우 목록 조회 후 선택하여 작성</b></td>
								</tr>
								<tr>
									<td>수정할 전자세금계산서를 조회 후 선택하여 작성</td>
								</tr>
								<tr height='35'>
									<td> 당초 발행할 종류 <input type='radio' name='p_type' id='p_type' value='1' checked> 매출 &nbsp; <input type='radio' name='p_type' id='p_type' value='2'> 매입 &nbsp; <input type='radio' name='p_type' id='p_type' value='3'> 위수탁 &nbsp;  <span id='btn2' style='display:none'><img src='/admin/image/bt_ok.gif' align='absbottom' style='cursor:hand' onclick='go_page()'></span> </td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><input type='radio' name='m_kind' id='m_kind3' onclick='r_click(3)'></td>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width='100%' bgcolor='#CCCCCC'>
					<tr height='80' bgcolor='#FFFFFF'>
						<td width='100' bgcolor='#F2F2F2'></td>
						<td style='padding:5px 5px 5px 5px' bgcolor='#bfffdf'>
							<table>
								<tr>
									<td><b>당초 전자발행분이 없는 경우 (직접입력작성)</b></td>
								</tr>
								<tr>
									<td>당초 전자세금계산서 발행분이 없는 경우 직접 입력하여 발행할 수 있습니다.  <span id='btn3' style='display:none'><img src='/admin/image/bt_ok.gif' align='absbottom' style='cursor:hand' onclick='location.href=\"/admin/tax/sales_write.php?tax_type=1\"'></span></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	</form>
	";

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = tax_menu();
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->Navigation = "세금계산서관리 > 수정발행";
	$P->title = "수정발행";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
?>