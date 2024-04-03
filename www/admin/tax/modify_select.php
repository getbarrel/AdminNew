<?
	include("../class/layout.class");
 	$db = new Database;
	
	
	$Contents = "";
	$Contents .= "
	<table cellpadding=0 cellspacing=0 border=0 width='100%' style='margin-bottom:20px;'>
		<tr>
			<td align='left' colspan=6 > <img src='../images/".$admininfo[language]."/step_modify02.gif' align='absmiddle'></td>
		</tr>
	</table>
	<TABLE cellSpacing=0 class='' cellPadding=0 width='100%'>
		<input type='hidden' name='tax_no' id='tax_no' value='".$idx."'>
		<input type='hidden' name='publish_type' id='publish_type' value='".$publish_type."'>
		<input type='hidden' name='tax_type' id='tax_type' value='".$tax_type."'>
		<tr>
			<td>
				<TABLE cellSpacing=0 class='list_table_box' cellPadding=0 width='100%' height='80px' align=center border=0 style='text-align:center; margin:10px 0;'>
					<col width = '5%' />
					<col width = '10%' />
					<col width = '*' />
					<col width = '10%' />
					<tr>
						<td align='center'><input type='radio' name='kind' id='kind1' value='1' checked></td>
						
						<td>기재사항<br>착오정정</td>
						<td style='text-align:left; padding-left:15px;'><b>기재사항 착오로 잘못 발행한 경우</b><br>당초 취소분 1장(자동발행), 수정분1장(직접입력) <b>총 2장</b> 수정세금계산서 발행</td>
						
						<td ><label for='kind1'><img src='../images/".$admininfo[language]."/btn_select.gif' align='absmiddle'></label></td>
								
					</tr>
				</table>
				<TABLE cellSpacing=0 class='list_table_box' cellPadding=0 width='100%' height='80px' align=center border=0 style='text-align:center; margin:10px 0;'>
					<col width = '5%' />
					<col width = '10%' />
					<col width = '*' />
					<col width = '10%' />
					<tr>
						<td align='center'><input type='radio' name='kind' id='kind6' value='6'></td>
						
						<td >착오에 의한<br>이중발급</td>
						<td style='text-align:left; padding-left:15px;'><b>착오로 이중 발급한 경우, 면세 등 발급대상이 아닌 거래 등에 대하여 발급한 경우</b><br>당초 발급한 (세금)계산서의 내용대로 부(-)의 수정(세금)계산서<b>1장</b></td>
						<td ><label for='kind6'><img src='../images/".$admininfo[language]."/btn_select.gif' align='absmiddle'></label></td>		
					</tr>
				</table>
				<TABLE cellSpacing=0 class='list_table_box' cellPadding=0 width='100%' height='80px' align=center border=0 style='text-align:center; margin:10px 0;'>
					<col width = '5%' />
					<col width = '10%' />
					<col width = '*' />
					<col width = '10%' />
					<tr>
						<td align='center'><input type='radio' name='kind' id='kind2' value='2'></td>
						
						<td >공급가액<br>변동</td>
						<td style='text-align:left; padding-left:15px;'><b>당초 발행한 금액의 <font color='red'>증감</font>이 발행한 경우</b><br><font color='red'>증감시킬</font> 금액에 대해 정(+) 또는 부(-)의 수정세금계산서 <b>1장</b></td>
						<td ><label for='kind2'><img src='../images/".$admininfo[language]."/btn_select.gif' align='absmiddle'></label></td>		
					</tr>
				</table>
				<TABLE cellSpacing=0 class='list_table_box' cellPadding=0 width='100%' height='80px' align=center border=0 style='text-align:center; margin:10px 0;'>
					<col width = '5%' />
					<col width = '10%' />
					<col width = '*' />
					<col width = '10%' />
					<tr>
						<td align='center'><input type='radio' name='kind' id='kind3' value='3'></td>
						
						<td >계약의<br>해제</td>
						<td style='text-align:left; padding-left:15px;'><b>당초 발행금액이 취소된 경우</b><br>당초 공급금액에 대한 부(-)의 수정세금계산서 <b>1장</b> 발행</td>
						<td ><label for='kind3'><img src='../images/".$admininfo[language]."/btn_select.gif' align='absmiddle'></label></td>
					</tr>
				</table>
				<TABLE cellSpacing=0 class='list_table_box' cellPadding=0 width='100%' height='80px' align=center border=0 style='text-align:center; margin:10px 0;'>
					<col width = '5%' />
					<col width = '10%' />
					<col width = '*' />
					<col width = '10%' />
					<tr>
						<td align='center'><input type='radio' name='kind' id='kind4' value='4'></td>
						
						<td >환입</td>
						<td style='text-align:left; padding-left:15px;'><b>반품등으로 당초 발행금액의 일부가 취소된 경우</b><br><font color='red'>반품된 금액만큼만</font> 부(-)의 수정세금계산서 <b>1장</b> 발행</td>
						<td ><label for='kind4'><img src='../images/".$admininfo[language]."/btn_select.gif' align='absmiddle'></label></td>
					</tr>
				</table>
				<!--TABLE cellSpacing=0 class='list_table_box' cellPadding=0 width='100%' height='80px' align=center border=0 style='text-align:center; margin:10px 0;'>
					<col width = '5%' />
					<col width = '10%' />
					<col width = '*' />
					<col width = '10%' />
					<tr>
						<td align='center'><input type='radio' name='kind' id='kind5' value='5'></td>
						
						<td>내국신용장<br>등사후개설</td>
						<td style='text-align:left; padding-left:15px;'><b>내국신용장 등이 사후 개설된 경우</b><br>개설된 금액만큼 부(-1)로 1장, 영세율로 1장 총 <b>2장</b> 수정세금계산서 발행</td>
						<td ><label for='kind5'><img src='../images/".$admininfo[language]."/btn_select.gif' align='absmiddle'></label></td>
					</tr>
				</table-->
			</td>
		</tr>
		<tr>
			<td align='center'>
				<img src='../images/".$admininfo[language]."/btn_modify_select.gif' align='absmiddle' onclick=\"modify_select('".$idx."');\">
				<a href='modify_main.php'><img src='../images/".$admininfo[language]."/btn_prevpage.gif' align='absmiddle'></a>
			</td>
		</tr>
	</table>
	
	";
$Script="
<script type='text/javascript'>

	function modify_select(idx){

		var kind = $('input:radio[name=kind]:checked').val();
		var publish_type = $('#publish_type').val();
		var tax_type = $('#tax_type').val();
		
		var go_url = '/admin/tax/tax_mdfy.php?idx=' + idx + '&m_kind=' + kind + '&publish_type=' + publish_type + '&tax_type=' + tax_type;
		//alert(go_url);
		location.href = go_url;
	}
</script>
";
/*
function frm_submit()
{
	var kind = $('input[name=\"kind\"]:checked').val();
	var publish_type = $("#publish_type").val();

	if($('#tax_no').val() != '')
	{
		var tax_no = '&tax_no=' + $("#tax_no").val();
	}
	
	var go_url = "/admin/tax/tax_mdfy.php?publish_typ=" + publish_type + "&mKind=" + kind;
	if(tax_no) go_url += tax_no;
	opener.location.href = go_url;
}
</script>";
*/
	
	
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = tax_menu();
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->Navigation = "세금계산서관리 > 수정발행 > 수정발행 사유선택";
	$P->title = "수정발행";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
?>