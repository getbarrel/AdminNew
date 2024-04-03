<?
include('../class/layout.class');

$db = new Database;

$Script = "
<script>
function zipcode_pop(zip_type) {
	var zip = window.open('/popup/zipcode.php?zip_type='+zip_type,'','width=600,height=465,scrollbars=yes,status=no');
}
function check_value(fm) {
	if(!CheckFormValue(fm)) {
		return false;
	}
	var PT_number =/^[0-9]+$/;
	if(!PT_number.test(fm.mobile1.value)) {
		alert('전화번호는 숫자만 입력해 주세요.');
		return false;
	}
	if(!PT_number.test(fm.mobile2.value)) {
		alert('전화번호는 숫자만 입력해 주세요.');
		return false;
	}
	if(!PT_number.test(fm.mobile3.value)) {
		alert('전화번호는 숫자만 입력해 주세요.');
		return false;
	}
}
</script>";

$sql = "SELECT * FROM shop_shipping_address WHERE ix = '".$ix."' ";
$db->query($sql);
$db->fetch();

list ($tel1,$tel2,$tel3) = explode('-',$db->dt[tel]);
list ($mobile1,$mobile2,$mobile3) = explode('-',$db->dt[mobile]);
list ($zipcode1,$zipcode2) = explode('-',$db->dt[zipcode]);

$Contents = "

<form name='cooperation' method='POST' action='/mypage/addressbook.act.php' onsubmit='return check_value(this);' target=''>
<input type='hidden' name='act' value='".$act."'>
<input type='hidden' name='code' value='".$code."'>
<input type='hidden' name='ix' value='".$ix."'>
	<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
		<col width='140' />
		<col width='*' />
		<tr>
			<td class='input_box_title'>
				배송지 명<span style='color:red; font-size:11px; font-weight:normal; margin-left:5px;'>(*)</span>
			</td>
			<td class='input_box_item'>
				<input type='text' id='shipping_name' name='shipping_name' value='".$db->dt[shipping_name]."' style='width:180px;' class='textbox' validation='true' title='배송지 명' />
				<input type='checkbox' id='default_yn' name='default_yn' value='Y'  align='absmiddle' ".($db->dt[default_yn]=='Y' ? "checked" : "")."/> <label for='default_yn' style='vertical-align:middle'>기본 배송지로 지정</label>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'>
				수취인 명<span style='color:red; font-size:11px; font-weight:normal; margin-left:5px;'>(*)</span>
			</td>
			<td class='input_box_item'>
				<input type='text' id='recipient' name='recipient' value='".$db->dt[recipient]."' style='width:180px;' class='textbox' validation='true' title='수취인 명' />
			</td>
		</tr>
		<tr>
			<td class='input_box_title'>
				전화번호
			</td>
			<td class='input_box_item'>
				<input type='text' id='tel1' name='tel1' value='".$tel1."' style='width:64px;' class='textbox' title='전화번호' maxlength='3' numeric='true' /> -
				<input type='text' id='tel2' name='tel2' value='".$tel2."' style='width:64px;' class='textbox' title='전화번호' maxlength='4' numeric='true' /> -
				<input type='text' id='tel3' name='tel3' value='".$tel3."' style='width:64px;' class='textbox' title='전화번호' maxlength='4' numeric='true' />
			</td>
		</tr>
		<tr>
			<td class='input_box_title'>
				핸드폰 번호<span style='color:red; font-size:11px; font-weight:normal; margin-left:5px;'>(*)</span>
			</td>
			<td class='input_box_item'>
				<input type='text' id='mobile1' name='mobile1' value='".$mobile1."' maxlength='3' style='width:64px;' class='textbox' title='>핸드폰 번호' validation='true' numeric='true'/> -
				<input type='text' id='mobile2' name='mobile2' value='".$mobile2."' maxlength='4' style='width:64px;' class='textbox' title='>핸드폰 번호' validation='true' numeric='true'/> - 
				<input type='text' id='mobile3' name='mobile3' value='".$mobile3."' maxlength='4' style='width:64px;' class='textbox' title='>핸드폰 번호' validation='true' numeric='true'/>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'>
				우편번호<span style='color:red; font-size:11px; font-weight:normal; margin-left:5px;'>(*)</span>
			</td>
			<td class='input_box_item'>
				<div class=''>
					<input type='text' id='zipcode1' name='zipcode1' value='".$db->dt[zipcode]."' style='width:64px;' class='textbox' validation='true' title='우편번호' readonly/> 
					<img src='../images/".$_SESSION["admininfo"]["language"]."/btn_search_address.gif' title='우편번호' align='absmiddle' onClick='zipcode_pop(1)' style='cursor:pointer;vertical-align:top;'>
				</div>
			</td>
		</tr>
		<tr>
			<td class='input_box_title' rowspan='2'>
				주소<span style='color:red; font-size:11px; font-weight:normal; margin-left:5px;'>(*)</span>
			</td>
			<td class='input_box_item'>
				<input type='text' id='addr1' name='address1' value='".$db->dt[address1]."' style='width:360px;' class='textbox' validation='true' title='상세주소' readonly />
			</td>
		</tr>
		<tr>
			<td class='input_box_item'>
				<input type='text' id='addr2' name='address2' value='".$db->dt[address2]."' style='width:360px;' class='textbox' validation='true' title='상세주소' />
			</td>
		</tr>
	</table>
	<table border='0' width='100%' cellspacing='0' cellpadding='0' style='margin-top:30px;'>
		<tr>
			<td align='center'>
				<input type='image' src='../images/".$_SESSION["admininfo"]["language"]."/b_save.gif' title='확인' alt='확인' align='absmiddle'>
				<a href='javascript:void(0);' onclick='window.close();'><img src='../images/".$_SESSION["admininfo"]["language"]."/b_cancel.gif' title='' align='absmiddle'></a>
			</td>
		</tr>
	</table>
</form>
<div class='align_right  Pgap_H5' style='Margin-right:5px;'>
	<a href='javascript:window.close();'><img src='/data/arounz_data/templet/stylestory/images/close_btn01.gif' title='' align='absmiddle'></a>
</div>
";

$Script .= "
<script language='javascript'>


</script>";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "배송지관리";
$P->NaviTitle = "배송지관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>