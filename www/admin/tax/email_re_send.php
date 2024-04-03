<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("../class/layout.class");

$Script = "
<script language='JavaScript' >

function CheckEmail(frm){
	if(frm.email.value.length < 1){
		alert('이메일을 입력해주세요');
		return false;
	}
}

</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr height=30><td class='p11 ls1' style='padding:0 0 0 5px;'  align='left' > - <b>".$company." ".$name."</b> 님에게 메일을 재전송 합니다.</td></tr>
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
				<form name='search_frm' method='get'  action='./popbill_send_etc.php' target='' onSubmit='return CheckEmail(this)'>
				<input type='hidden' name='act' value='send_mail'>
				<input type='hidden' name='idx' value='".$idx."'>
				
					<table class='box_shadow' style='width:100%;' cellpadding=0 cellspacing=0>
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05' align=right style='padding: 0 20 0 20'>
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box'>
									<col width='170'>
									<col width='*'>
									<tr height='40' valign='middle'>
										<td class='input_box_item' style='padding-left:15px;'>
											<input type='text' class='textbox' name='email' size='60' value='".$email."'>
										</td>
									</tr>
								</table>
							</td>
							<th class='box_06'></th>
						</tr>
						<tr>
							<th class='box_07'></th>
							<td class='box_08' style='padding-top:14px;'>메일주소를 변경하시면 변경한 주소로 메일이 재전송 됩니다.</td>
							<th class='box_09'></th>
						</tr>
						</table>
						<table style='margin-top:20px;'>
							<tr>
								<td>
									<input type='image' src='../images/".$admininfo[language]."/btn_send_mail_01.png' style='cursor:pointer' align='absmiddle'>
								</td>
							</tr>
						</table>
				</form>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</TABLE>
";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "이메일 재전송";
$P->NaviTitle = "이메일 재전송";
$P->strContents = $Contents;
echo $P->PrintLayOut();

//print_r($script_times);

?>





