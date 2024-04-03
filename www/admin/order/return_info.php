<?
include("../class/layout.class");

$db = new Database;

if($type == "return"){
	$title = "반품 배송정보";
	$navigation = "주문관리 > 반품 배송정보";
}else{
	$title = "교환 배송정보";
	$navigation = "주문관리 > 교환 배송정보";
}


$sql = "select * from shop_order_detail_deliveryinfo	where od_ix = '".$od_ix."' ";
//echo $sql;
$db->query($sql);
$db->fetch();

$Contents = "
	<table cellpadding=0 cellspacing=0 border=0 width='100%' align=center style='margin:0px 0 0 0px;background-color:#ffffff;'>
	<tr >
		<td align='left' colspan=2> ".GetTitleNavigation($title, $navigation, false)."</td>
	</tr>
	<tr>
		<td valign=top style='padding:10px;'>
		<table border='0' width='100%' cellspacing='1' cellpadding='2'  class='input_table_box'>
			<col width='30%'>
			<col width='70%'>
			<tr height='23' bgcolor=#ffffff>
				<td class='input_box_title' align='left' >택배사</td>
				<td class='input_box_item'  align='left' >&nbsp;".$db->dt[quick]."</td>
			</tr>
			<tr height='23' bgcolor=#ffffff>
				<td class='input_box_title' align='left' >송장번호</td>
				<td class='input_box_item'  align='left'>&nbsp;".$db->dt[invoice_no]."</td>
			</tr>
		</table>
		</td>

	</tr>
	</table>";


$Script = "
<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
<script type='text/javascript' src='../js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='../js/ui/jquery.ui.core.js'></script>
<script type='text/javascript'>
$(document).ready(function() {
	$('#excel_sortlist').sortable();
	$('#excel_sortlist').disableSelection();
});

function CheckValue(frm){
	var params = $('#excel_sortlist').sortable('toArray');
	$('input[name=order_excel_info1]').val(params);
	//alert(params);
	return true;
}
</script>
<style>
  #excel_sortlist {
      list-style-type:none;
      margin:0;
      padding:0;

   }
   #excel_sortlist li {
     margin:1
     padding:0 0 0 10;
     cursor:move;
     width:445px;
     display:inline;
     border:1px solid #c0c0c0;
   }

   #excel_sortlist2 div {
     margin:1
     padding:0 0 0 10;
     cursor:move;
     width:445px;
     display:inline;
     border:1px solid #c0c0c0;
   }

</style>";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = $navigation;
$P->NaviTitle = $title;
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>