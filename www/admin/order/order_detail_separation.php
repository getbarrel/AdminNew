<?
include("../class/layout.class");


$db = new Database;


$sql = "select * from shop_order_detail where od_ix = '$od_ix'";
//echo $sql;

$db->query($sql);
$sp_order_detail = $db->fetchall();


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr >
			<td align='left' colspan=4> ".GetTitleNavigation("주문상세정보 분리", "상품관리 > 주문상세정보 분리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk>주문상세정보 분리</b></div>")."</td>
	  </tr>
	  </table>
	  <form name='order_detail_separation_form' method='get' action='order_detail_separation.act.php' style='display:inline;' target='iframe_act' >
	<input type='hidden' name='act' value='order_detail_update'>
	<input type='hidden' name='od_ix' value='$od_ix'>
	<input type='hidden' name='total_pcnt' id='total_pcnt' value='".$sp_order_detail[0][pcnt]."'>
	<!--input type='hidden' class='input_pcnt' -->

	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<tr>
		<td colspan=2 height=250 style='vertical-align:top;'>
			
			<table width='100%' border='0' cellpadding='0' cellspacing='0' id='order_detail_table' class='order_detail_table'>
				<tr height='25' bgcolor='#efefef' align=center>
						<td width='*' colspan=2 class='m_td small'><b>상품명</b></td>
						<td width='5%' class='m_td small'><b>수량</b></td>
						<td width='15%' class='m_td small'><b>옵션</b></td>
						<td width='5%' class='m_td small' nowrap><b>전달사항</b></td>
						<td width='10%' class='m_td small'><b>쿠폰사용</b></td>";
if($admininfo[mall_use_multishop]){
$Contents01 .=	"<td width='7%' class='m_td small'><b>공급가</b></td>";
$Contents01 .=	"<td width='11%' class='m_td small'><b>상태</b></td>";
$Contents01 .=	"<td width='5%'  class='m_td small'><b>단가</b></td>
						 <td width='5%' class='m_td small'><b>적립금</b></td>";
}else{
$Contents01 .=	"<td width='10%'  class='m_td small'><b>단가</b></td>
						<td width='10%' class='m_td small'><b>적립금</b></td>";
}

$Contents01 .=	"<td width='8%' class='m_td small'><b>합계</b></td>
						<td width='6%' class='e_td small'><b>관리</b></td>
					</tr>";

	


	$num = 1;

	$sum = 0;
	$arr_sns_ptype=array(4,5,6);
	for($j = 0; $j < count($sp_order_detail); $j++)
	{
		//$db->fetch($j);

		$pname = $sp_order_detail[$j][pname];
		$pcode = $sp_order_detail[$j][pcode];
		$product_type = $sp_order_detail[$j][product_type];
		$count = $sp_order_detail[$j][pcnt];
		$option_div = $sp_order_detail[$j][option_text];
		$option_etc1 = $sp_order_detail[$j][option_etc];
		$msgbyproduct = $sp_order_detail[$j][msgbyproduct];
		$option_price = $sp_order_detail[$j][option_price];
		$price = $sp_order_detail[$j][psprice]+$sp_order_detail[$j][option_price];
		$coprice = $sp_order_detail[$j][coprice];
		$sumptprice = $sumptprice + $sp_order_detail[$j][ptprice];
		$od_admin_message=$sp_order_detail[$j]["admin_message"];


		$reserve = $sp_order_detail[$j][reserve];
		$ptotal = $price * $count;
		$sum += $ptotal;

$Contents01 .= "<tr height='30' align='center'>
							<!--td class=dot-x nowrap><input type=checkbox name='od_ix[]' id='oid' class='od_ix' od_status='".$sp_order_detail[$j][status]."' value='".$sp_order_detail[$j][od_ix]."' checked></td-->
							<td class=dot-x style='padding:3px 0px;'><a href='../product/goods_input.php?id=".$sp_order_detail[$j][pid]."' class='screenshot'  rel='".PrintImage($admin_config[mall_data_root]."/images/product", $sp_order_detail[$j][pid], $LargeImageSize)."'  title='".$LargeImageSize."'><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $sp_order_detail[$j][pid], "c")."'  onerror=\"this.src='".$admin_config[mall_data_root]."/images/noimg_52.gif'\" width=50 height=50 style='margin:5px;'></a></td>
							<td class=dot-x align='left' style='padding:5px 0 5px 0;line-height:130%'>";
if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
	$Contents01 .= "<a href=\"javascript:PoPWindow('../seller/company.add.php?company_id=".$sp_order_detail[$j][company_id]."&mmode=pop',960,600,'brand')\"><b>".($sp_order_detail[$j][company_name] ? $sp_order_detail[$j][company_name]:"-")."</b></a><br>";
}
if(in_array($product_type,$arr_sns_ptype)){
	$Contents01 .= "<a href=\"/sns/shop/goods_view.php?id=".$sp_order_detail[$j][pid]."\" target=_blank>".$pname."</a>";
} else {
	$Contents01 .= "<a href=\"/shop/goods_view.php?id=".$sp_order_detail[$j][pid]."\" target=_blank>".$pname."</a>";
}

$Contents01 .= "	</td>
							<td class=dot-x nowrap><input type='text' class='textbox input_pcnt' id='input_pcnt' name='pcnt[]' pid='".$sp_order_detail[$j][pid]."' total='".$count."' style='width:30px;' value='".$count."'> 개</td>
							<td class=dot-x align=left style='padding:7px 5px;'>".$option_div."".($option_price != '' ? " + ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($option_price)."".$currency_display[$admin_config["currency_unit"]]["back"]."":"")."</td>
							<td class=dot-x align=center>".$msgbyproduct."</td>
							<td class=dot-x align=center>".number_format($sp_order_detail[$j][use_coupon])." ".($sp_order_detail[$j][use_coupon] > 0 ? "<br><a href=\"javascript:PopSWindow('../display/cupon_publish.php?mmode=pop&regist_ix=".$sp_order_detail[$j][use_coupon_code]."',900,700,'cupon_detail_pop');\" class=blue>쿠폰확인</a>":"")."</td>";
if($admininfo[mall_use_multishop]){
$Contents01 .= "				<td class=dot-x align=center> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($sp_order_detail[$j][coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";
$Contents01 .= "				<td class=dot-x align=center style='line-height:130%'>";
if($sp_order_detail[$j][status]==ORDER_STATUS_EXCHANGE_DELIVERY && $sp_order_detail[$j][order_type]>0) $Contents01 .= "<a href=\"javascript:PopSWindow('/admin/order/orders.view_address.php?oid=".$oid."&od_ix=".$sp_order_detail[$j][od_ix]."&type=".$sp_order_detail[$j][status]."',500,400,'orders_view_address')\">";
$Contents01 .=getOrderStatus($sp_order_detail[$j][status],$sattle_method);
if($sp_order_detail[$j][status]==ORDER_STATUS_EXCHANGE_DELIVERY && $sp_order_detail[$j][order_type]>0) $Contents01 .= "</a>";
if($sp_order_detail[$j][ra_date] && $sp_order_detail[$j][status] == "RA"){
	$Contents01 .= "<br />(".substr($sp_order_detail[$j][ra_date],0,10).")";
}
if($sp_order_detail[$j][ea_date] && $sp_order_detail[$j][status] == "EA"){
	$Contents01 .= "<br />(".substr($sp_order_detail[$j][ea_date],0,10).")";

}
if($sp_order_detail[$j][dc_date] && $sp_order_detail[$j][status] == "DC"){
	$Contents01 .= "<br />(".substr($sp_order_detail[$j][dc_date],0,10).")";
}
if($sp_order_detail[$j][ac_date]){
	$Contents01 .= "<br /><a href=\"/admin/order/accounts_detail.php?ac_ix=".$sp_order_detail[$j][ac_ix]."\" target=_blank>(".substr($sp_order_detail[$j][ac_date],0,4)."-".substr($sp_order_detail[$j][ac_date],4,2)."-".substr($sp_order_detail[$j][ac_date],6,2).")</a> ";
}
if($sp_order_detail[$j][invoice_no] && !($sp_order_detail[$j][status] == "ED" || $sp_order_detail[$j][status] == "RD")){
	$Contents01 .= "<br><a href=\"javascript:searchGoodsFlow('".$sp_order_detail[$j][quick]."', '".str_replace("-","",$sp_order_detail[$j][invoice_no])."')\">".codeName($sp_order_detail[$j][quick])." </a> ";
}

if($sp_order_detail[$j][ra_date] && ($sp_order_detail[$j][status] == "RA")&& $sp_order_detail[$j][return_reason] != ""){

	$Contents01 .= "<br /><a href='javascript:void(0)' onclick=\"return_pop(".$sp_order_detail[$j][od_ix].")\"><img src='../image/btn_return_reason.gif'></a>";

}

if($sp_order_detail[$j][ea_date] && ($sp_order_detail[$j][status] == "EA")&& $sp_order_detail[$j][return_reason] != ""){

	$Contents01 .= "<br /><a href='javascript:void(0)' onclick=\"return_pop(".$sp_order_detail[$j][od_ix].")\"><img src='../images/".$admininfo["language"]."/btn_exchange_reason.gif'></a> <a class='messagebox' messagebox_id='messagebox_".$sp_order_detail[$j][od_ix]."'><img src='../images/".$admininfo["language"]."/btn_exchange_addr.gif'></a>";
	$sql="SELECT * FROM shop_order_detail_deliveryinfo WHERE od_ix='".$sp_order_detail[$j][od_ix]."' ";
	$db1->query($sql);
	$db1->fetch();
			$Contents01 .= "<table id='messagebox_".$sp_order_detail[$j][od_ix]."' style='display:none;' class='messagebox_contents'>
							<tr>
								<td>받는사람 : </td>
								<td>".$db1->dt[rname]."</td>
							</tr>
							<tr>
								<td>받는 전화번호 : </td>
								<td>".$db1->dt[rtel]."</td>
							</tr>
							<tr>
								<td>받는 핸드폰 : </td>
								<td>".$db1->dt[rmobile]."</td>
							</tr>
							<tr>
								<td>받는 주소 : </td>
								<td>[".$db1->dt[zip]."] ".$db1->dt[addr1]."  ".$db1->dt[addr2]."</td>
							</tr>
							<tr>
								<td colspan='2' align='right' style='font-size:13px;font-family:Arial;font-weight:600;color:#A72525;'><span style='cursor:pointer;' class='messagebox_x'>X</span></td>
							</tr>
						</table>";

}

if($sp_order_detail[$j][status] == "ED" || $sp_order_detail[$j][status] == "RD"){
	$Contents01 .= "<br><a href=\"javascript:searchGoodsFlow('".$sp_order_detail[$j][quick]."', '".str_replace("-","",$sp_order_detail[$j][return_invoice_no])."')\">".codeName($sp_order_detail[$j][quick])." </a> ";
}
$Contents01 .="					</td>";
}
$Contents01 .= "				<td class=dot-x> ".$currency_display[$admin_config["currency_unit"]]["front"]." <span id='price' style='display:none'>$price</span> ".number_format($price)."".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
									<td class=dot-x>".number_format($reserve)." P</td>
									<td class=dot-x>".$currency_display[$admin_config["currency_unit"]]["front"]." <span id='ptotal'>".number_format($ptotal)."</span> ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
									<td class=dot-x><img src='../images/".$admininfo["language"]."/btn_add2.gif' id='separation_btn' style='cursor:pointer' align=absmiddle></td>
								</tr>";
	if($od_admin_message!="") {
		$Contents01 .="<tr><td colspan=13 style='padding:5px 0px;background-color:#FAFAFA;'><strong>[관리자 상태변경 메모] : </strong>".$od_admin_message."</td></tr>";
	}
//$Contents01 .="							<tr><td colspan=13 class=dot-x></td></tr>";

		$num++;
	}
$Contents01 = $Contents01."
									</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle><!--btn_inquiry.gif--></td>
	</tr>

	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'>
		
		</td>
	</tr>
	  </table>
	  </form>";


$help_text = "
	<table cellpadding=1 cellspacing=0 class='small' >
		<col width=8>
		<col width=*>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문상세 정보를 분리 할수 있는 기능입니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >필요한 만큼 상품정보를 분리해서 수량을 수정해서 저장 하실 수 있습니다.</td></tr>
		<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >각 정보의 상태는 주문상세정보 분리 후 상태변경 처리하시면 됩니다.</td></tr>
	</table>
	";


$help_text = HelpBox("주문상세정보 분리", $help_text);
$Contents01 = $Contents01.$help_text;

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";

$Contents = $Contents."</table >";

 $Script = "
 <script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target,site_code ) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var obj = sel.name;
		var depth = sel.getAttribute('depth');

		//var depth = sel.depth;//kbk
		
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//document.write('category.load.php?form=' + form + '&obj=' + obj + '&trigger=' + trigger + '&depth='+depth+'&target=' + target +'&site_code='+site_code);
		window.frames['act'].location.href = 'category.load.php?form=' + form + '&obj=' + obj + '&trigger=' + trigger + '&depth='+depth+'&target=' + target +'&site_code='+site_code;

	}

$(function() {
	$('#separation_btn').click(function(){
		OrderDetailSeparation('order_detail_table');
	});
});

function OrderDetailRemove(obj){

}
function OrderDetailSeparation(obj){
	//alert($('.input_pcnt').length);
	if($('#total_pcnt').val() > $('.input_pcnt').length){
		$('#input_pcnt:first').val($('#input_pcnt:first').val()-1);
		var _basic_price = $('span[id^=price]:first').html();
		//alert($('span[id^=ptotal]:first').html().replace(\",\",\"\"));
		$('span[id^=ptotal]:first').html($('span[id^=ptotal]:first').html().replace(\",\",\"\") -_basic_price);

		var objs = $('#'+obj).find('tr:last');

		if(objs.length > 0 ){
			//alert(objs[0].html());
			var obj_table = objs[0].cloneNode(true);
			var target_obj = objs[objs.length-1];	
			//alert(obj_table.html());
		}else{
			
			var obj_table = objs[0].cloneNode(true);
			var target_obj = objs[0];
		}
		
		var newRow = objs.clone(true).appendTo($('#order_detail_table'));  
		newRow.find('img[id^=separation_btn]').attr('src','../images/".$admininfo["language"]."/btn_del.gif');
		var _price = newRow.find('span[id^=price]').html();
		//alert(_price);
		newRow.find('span[id^=ptotal]').html(_price);

		newRow.find('input[id^=input_pcnt]').val(1);
		newRow.find('img[id^=separation_btn]').unbind('click');
		newRow.find('img[id^=separation_btn]').click(function(){
				//alert(1);
				$('#input_pcnt:first').val(parseInt($('#input_pcnt:first').val())+1);
				var _basic_price = parseInt($('span[id^=price]:first').html());
				//alert($('span[id^=ptotal]:first').html().replace(\",\",\"\"));
				$('span[id^=ptotal]:first').html(parseInt($('span[id^=ptotal]:first').html().replace(\",\",\"\")) + _basic_price);

				$(this).parent().parent().remove();
		});
	}else{
		alert('주문상품 분리는 최대 주문 수량 만큼 가능합니다.');
	}
	
}
	</script>
 ";

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = product_menu();
	$P->NaviTitle = "주문상세정보 분리";
	$P->Navigation = "주문관리 > 주문상세정보 분리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = order_menu();
	$P->title = "주문상세정보 분리";
	$P->Navigation = "주문관리 > 주문상세정보 분리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
?>