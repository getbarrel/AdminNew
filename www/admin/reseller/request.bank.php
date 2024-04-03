<?
include("../class/layout.class");
@include ($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");

$db = new Database;

$sql = "SELECT AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name FROM common_member_detail WHERE code = '".$code."'";
$db->query($sql);
$db->fetch();

$Contents = "

<script language='javascript' src='/admin/reseller/reseller.js'></script>

<form method='post' action='reseller.act.php' onsubmit='return requetBank($(this))'>
<input type='hidden' name='act' value='addReseller'/>
<input type='hidden' name='code' value='".$code."'/>
<input type='hidden' name='name' value='".$db->dt[name]."'/>
<input type='hidden' name='rsl_div' value='M'/>

<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
	<colgroup>
		<col width='20%'>
		<col width='*'>
	</colgroup>
	<tbody>
		<tr height='27'>
			<td class='search_box_title'>은행명 <img src='/admin/icon/required3.gif'></td>
			<td class='search_box_item' colspan='3'>
				".getBankInfo()."
			</td>
		</tr>
		<tr height='27'>
			<td class='search_box_title'>예금주 <img src='/admin/icon/required3.gif'></td>
			<td class='search_box_item' colspan='3'>
				<input type='text' class='textbox' name='bank_owner' size='30' value='".$db->dt[name]."' validation='true' title='예금주'>
			</td>
		</tr>
		<tr height='27'>
			<td class='search_box_title'>계좌번호 <img src='/admin/icon/required3.gif'></td>
			<td class='search_box_item' colspan='3'>
				<input type='text' class='textbox' name='bank_number' size='30' value='' validation='true' title='계좌번호'>
			</td>
		</tr>
	</tbody>
</table>
<div style='margin:15px 0;text-align:center;'>
	<input type='image' src='../images/korea/b_save.gif' border='0' style='cursor:pointer;border:0px;' align='absmiddle'>
</div>

</form>

";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "매니저 적용";
$P->NaviTitle = "매니저 적용";
$P->title = "매니저 적용";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>

