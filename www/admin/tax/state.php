<?
	include("../class/layout.class");
 	$db = new Database;

	//include_once $DOCUMENT_ROOT."/admin/tax/test_header.php";


	$Contents = "
	<script src='tax.js'></script>
	<script src='/admin/js/calendar.js'></script>
	<script>
	$(document).ready(function(){
	});
	</script>
	";

	$Contents .= "
	<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr>
			<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("통계", "세금계산서관리 > 통계 ")."</td>
		</tr>
	</table>

	<table width='100%'>
		<tr>
			<td width='30%'><input type='button' value='바로작성하기'></td>
			<td align='center'>
				<table>
					<tr>
						<td><<</td>
						<td width='90'>2010년 11월</td>
						<td>>></td>
					</tr>
				</table>
			</td>
			<td width='30%'></td>
		</tr>
	</table>

	<table width='100%' cellpadding=0 cellspacing=1 border=0 bgcolor='#CCCCCC' style='margin:0 0 10px 0'>
		<tr bgcolor='#F2F2F2' height='30'>
			<td rowspan='5'>
				국세청 전송 설정 상태
				수동전송
			</td>
			<td colspan='2'></td>
			<td colspan='2'></td>
			<td colspan='2'></td>
		</tr>
		<tr bgcolor='#FFFFFF' height='25'>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr bgcolor='#FFFFFF' height='25'>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr bgcolor='#FFFFFF' height='25'>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr bgcolor='#FFFFFF' height='25'>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</table>
	";

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = tax_menu();
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->Navigation = "세금계산서관리 > 통계";
	$P->title = "통계";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
?>