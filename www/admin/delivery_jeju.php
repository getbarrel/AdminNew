<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("./class/layout.class");



if($max == ""){
	$max = 20; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;

$db->query("select count(*) as total from ".TBL_SHOP_ADD_DELIVERY_AREA);
$db->fetch();

$total = $db->dt[total];


$sql = "select * from ".TBL_SHOP_ADD_DELIVERY_AREA." order by ix limit $start, $max";
//echo $sql;
$db->query($sql);

$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&buying_status=$buying_status&sdate=$sdate&edate=$edate&input_id=$input_id&select_name=$select_name","");

$Script = "
<script language='JavaScript' >

function CheckSearch(frm){
	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');
		return false;
	}
}

function SelectBrand(c_ix, company_name){

	$('#".$input_id."',opener.document).val(c_ix);
	$('#".$select_name."',opener.document).val(company_name);
	self.close();
}

function setAddDeliveryPrice(ix) {
		var price = $('#addPrice_'+ix).val();
        
	  	$.ajax({ 
				type: 'POST', 
				data: {'return_type': 'json', 'ix':ix, 'price':price, 'act':'change'},
				url: './delivery_jeju.act.php',  
				dataType: 'json', 
				async: true, 
				beforeSend: function(){ 
					
				},  
				success: function(datas){
					alert(datas.msg)
				}
					
		}); 
	}
</Script>";

$Contents = "
<form name='delivery_add_price' method='post'>
<input type='hidden' name='act' value='change'>
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("제주/도서산간 지역 확인", "제주/도서산간 지역 확인", false)."</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr height=30>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
		<td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> </td>
	</tr>
	<tr><form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' ><input type=hidden name='act' value='send_mail' >
		<td colspan=2 style='padding: 0 5px 0 5px' width=100% valign=top>
		<table width=100% class='list_table_box'>
		<tr height='28' bgcolor='#ffffff'>
			<td width='20%' align='center' class='m_td'><font color='#000000'><b>우편번호</b></font></td>
			<td width='*' align='center' class=m_td><font color='#000000'><b>주소</b></font></td>
			<td width='20%' align='center' class=m_td><font color='#000000'><b>추가배송비</b></font></td>
		  </tr>";


if($db->total){
	for($i=0;$i  < $db->total; $i++){
		$db->fetch($i);

		$Contents .= "<tr height=25 style='text-align:center;cursor:pointer;' >
								<td class='list_box_td '>".$db->dt['zip']."</td>
								<td class='list_box_td list_bg_gray' >".$db->dt['addr']."</td>
								<td class='list_box_td' >
									<input type='text' size='3' name='addPrice[".$db->dt['ix']."]' id='addPrice_".$db->dt['ix']."' value='".$db->dt['price']."'>
									<input type='button' onclick='setAddDeliveryPrice(".$db->dt['ix'].")' value='저장'>
								</td>
								</tr>";
	}
}


$Contents .= "
		</table>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:0 10px 0 10px' colspan=2>
		</td>
	</tr>
	<tr>
		<td align=center style='padding:10px 0 0 0' colspan=2>
			".$str_page_bar."
		</td>
	</tr></form>
</TABLE>
</form>
";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "추가지역 배송비 등록";
$P->NaviTitle = "추가지역 배송비 등록";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>





