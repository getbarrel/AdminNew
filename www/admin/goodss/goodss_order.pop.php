<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");

$db = new Database;

$sql = "select *
			from ".TBL_SHOP_ORDER_DETAIL." 
			where od_ix = '".$od_ix."'";

//echo $sql;
$db->query($sql);
$db->fetch();

$Contents = "<!--img src='../images/0.gif' width='880' height='1'-->

		<table cellpadding=0 cellspacing=0 border=0 width='100%' align=center style='margin:0px 0 0 0px;background-color:#ffffff;'>	
		<tr >
			<td align='left' colspan=2> ".GetTitleNavigation("도매 주문하기", "주문관리 > 도매 주문하기", false)."</td>
		</tr>
		<tr >
			<td align='left' colspan=2> 선택하신 주문정보를 도매주문 처리합니다. </td>
		</tr>
		<tr>
			<td valign=top style='padding:10px 0px;min-height:100px;'>	
			<form name=goodss_order method=post action='../goodss/goodss_orders.act.php' ><!--target='iframe_act'-->
			<input type='hidden' name='act' value='goodss_order'>
			<input type='hidden' name='oid' value='".$_GET["oid"]."'>
			<table border='0' width='100%' cellspacing='1' cellpadding='2'  class='search_table_box'>
				<col width='40'>
				<col width='*'>
				<col width='150'>
				<col width='90'>
				<tr height='25' >
					<td class='s_td ctr' width='5%'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
					<td align='center' class='m_td' ><b>제품정보</b></td>
					<td align='center' class='m_td'><b>상품금액</b></td>
					<td align='center' class='m_td'><b>수량</b></td>
				</tr>";
for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
$Contents .= "
				<tr height='50' bgcolor=#ffffff>
					<td class='list_box_td list_bg_gray' bgcolor='#efefef' align=center>
						<input type=checkbox class=nonborder id='cpid' name=cpid[] value='".$db->dt[od_ix]."' ".($_GET["od_ix"] == $db->dt[od_ix] ? "checked":"").">
					</td>
					<td class='list_box_td' align='center' style='padding:5px;text-align:center;'>
						<table>
							<tr>
								<td><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "c")."'  width=50 height=50></td>
								<td style='line-height:130%;'><b>".$db->dt[pname]."</b><br>".$db->dt[option_text]."</td>
							</tr>
						</table>
					</td>
					<td class='list_box_td' >".number_format($db->dt[psprice])." 원</td>
					<td class='list_box_td' >".$db->dt[pcnt]." 개</td>
				</tr>";
}
$Contents .= "
				
			</table>
			
			</td>
		</tr>
		<tr >
			<td align='center' colspan=2 style='padding:20px 0px;'>
				<img src='../images/".$admininfo["language"]."/btn_goodss_order.gif' border=0 align=absmiddle onclick=\"GoodssOrder('goodss_order','".$_GET["oid"]."')\" style='cursor:hand;'> 
				<img src='../images/".$admininfo["language"]."/btn_goodss_cart.gif' border=0 align=absmiddle onclick=\"GoodssOrder('goodss_cart','".$_GET["oid"]."')\" style='cursor:hand;'></td>
		</tr>
		</table>
		</form>

		";



$Script = "
<script language='JavaScript' src='../js/jquery-1.4.js'></Script>
<script type='text/javascript' src='../js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='../js/ui/jquery.ui.core.js'></script>
<script type='text/javascript'>
function GoodssOrder(act, oid){
	var frm = document.goodss_order;

		if(confirm('해당 주문을 도매주문 처리 하시겠습니까?')){
			frm.act.value= act;
			frm.submit();
			//document.location.href= '../goodss/goodss_orders.act.php?act='+act+'&oid='+oid;
			//window.frames['iframe_act'].location.href= '../goodss/goodss_orders.act.php?act='+act+'&oid='+oid;
		}
	
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
$P->Navigation = "주문관리 > 도매 주문하기";
$P->NaviTitle = "도매 주문하기";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>