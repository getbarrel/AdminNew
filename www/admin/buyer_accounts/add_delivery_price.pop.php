<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");


$Script = "
<script type='text/javascript'>
<!--

//-->
</script>

";

if(empty($odd_ix)){
	echo " 잘못된 접근입니다.";
	exit;
}

$Contents = "<form name='add_delivery_price_frm' method='get' action='add_delivery_price.act.php' onsubmit='return CheckFormValue(this)' target='iframe_act'>
<input type='hidden' name='act' value='payment_yn_update'>
<input type='hidden' name='odd_ix' value='".$odd_ix."'>

		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>
			<tr>
				<td align=center style='padding: 0 10 0 10'>
						<table border='0' width='100%' cellspacing='1' cellpadding='0' >
							<tr>
								<td >
									<table border='0' width='100%' cellspacing='1' cellpadding='3' class='input_table_box' style='table-layout:fixed;' >
										<col width='30%'>
										<col width='70%'>
										<tr>
											<td class='input_box_title'> 추가배송비 결제상태 </td>
											<td class='input_box_item' >
												<input type='radio' name='payment_yn' id='payment_yn_y' value='Y' checked><label for='payment_yn_y'>입금완료</label>
												<input type='radio' name='payment_yn' id='payment_yn_l' value='L' ><label for='payment_yn_l'>배송비 손실</label>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
			</td>
  		</tr>
		<tr>
			<td colspan=2 align=center style='padding:10px 0px;'>
			<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle>
			</td>
		</tr>
  		</table>
</form>";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "주문관리 > 추가배송비 결제상태변경하기";
$P->NaviTitle = "추가배송비 결제상태변경하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();