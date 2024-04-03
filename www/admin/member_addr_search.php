<?

include("./class/layout.class");

$db = new Database;
$mdb = new Database;


$select_title = "배송지";


$sql = "SELECT * FROM shop_shipping_address WHERE mem_ix = '".$code."' ";
$db->query($sql);
$db->fetch();


$Script = "
<script language='JavaScript' >


function SelectCompanyAddr(recipient,tel,mobile,zipcode,address1,address2)
{

	$('#bname',opener.document).val(recipient);
	$('#addr1',opener.document).val(address1);
	$('#addr2',opener.document).val(address2);

	var phone = tel.split('-');	//주문자 전화번호
	var pcs = mobile.split('-');	//주문자 휴대폰전화번호
	var zip = zipcode.split('-');	//주문자 배송지 우편코드

	$('#bmember_phone1',opener.document).val(phone[0]);
	$('#bmember_phone2',opener.document).val(phone[1]);
	$('#bmember_phone3',opener.document).val(phone[2]);

	$('#bmember_pcs1',opener.document).val(pcs[0]);
	$('#bmember_pcs2',opener.document).val(pcs[1]);
	$('#bmember_pcs3',opener.document).val(pcs[2]);

	$('#zip1',opener.document).val(zip[0]);
	$('#zip2',opener.document).val(zip[1]);
	
	self.close();
}

function changeBgColor(obj,ix){

	var objTop = obj.parentNode.parentNode;	
	for(j=0;j < objTop.rows.length;j++){
		$(objTop.rows[j]).find('td').each(function(){
			$(this).css('background-color','');	
		});
	}
	$(obj).find('td').css('background-color','#f9ded1');
	var onclick_str = 'PopSWindow(\'/admin/member/addressbook_add_pop.php?act=update&code=".$code."&ix=' + ix + '\',730,476,\'addressbook_add_pop\');';
	$('#btn_modify').attr(\"onclick\",onclick_str);
	$('#btn_modify > img').show();

}

</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<tr>
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% align='right' valign=top>

		<a href='javascript:void(0);' onclick=\"PopSWindow('/admin/member/addressbook_add_pop.php?act=insert&code=".$code."',730,476,'addressbook_add_pop');\"><img src='/admin/images/".$admininfo["language"]."/btn_add.gif' title='' align='' style='margin-bottom:10px;' /></a>
		
		<a href='javascript:void(0);' id='btn_modify'  onclick=\"PopSWindow('/admin/member/addressbook_add_pop.php?act=insert&code=".$code."',730,476,'addressbook_add_pop');\"><img src='/admin/images/".$admininfo["language"]."/bts_modify.gif' title='' align=''  style='margin-left:2px;margin-bottom:10px; display:none;' /></a>

		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>
			<td width='20%' align='center' class='m_td'><font color='#000000'><b>배송지명</b></font></td>
			<td width='*' align='center' class=m_td><font color='#000000'><b>수취인명</b></font></td>
			<td width='30%' align='center' class=m_td><font color='#000000'><b>배송지주소</b></font></td>
			<td width='20%' align='center' class=m_td><font color='#000000'><b>전화번호</b></font></td>
			<td width='20%' align='center' class=m_td><font color='#000000'><b>핸드폰번호</b></font></td>
		  </tr>";


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		$Contents .= "<tr height=35 style='text-align:center;cursor:pointer;' onclick=\"if($(this).children().css('background-color').replace(/\s/g,'')=='rgb(249,222,209)'){SelectCompanyAddr('".$db->dt[recipient]."','".$db->dt[tel]."','".$db->dt[mobile]."','".$db->dt[zipcode]."','".$db->dt[address1]."','".$db->dt[address2]."');}else{changeBgColor(this,'".$db->dt[ix]."')}\" >
				<td class='list_box_td '>".$db->dt[shipping_name]."</td>
				<td class='list_box_td point' >".$db->dt[recipient]."</td>
				<td class='list_box_td ' >".$db->dt[address1]."<br>".$db->dt[address2]."</td>
				<td class='list_box_td list_bg_gray' >".$db->dt[tel]."</td>
				<td class='list_box_td ' >".$db->dt[mobile]."</td>
			</tr>";
		}
}else{
	$Contents .= "<tr height=50><td colspan='5' align='center'>저장된 배송지 정보가 없습니다.</td></tr>";
}


$Contents .= "
		</table>
		</td>
	</tr>
</TABLE>
";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "".$select_title."검색";
$P->NaviTitle = "".$select_title."검색";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>





