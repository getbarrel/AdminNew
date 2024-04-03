<?
include("../class/layout.class");
include("./inventory.lib.php");

$db = new Database;
$db1 = new Database;
$db2 = new Database;
$db3 = new Database;
$odb = new Database;
$tdb = new Database;

//print_r($_POST);

$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' valign=top>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("발주(사입)서 작성하기", "발주(사입)관리 > 발주(사입)서 작성하기 ")."</td>
</tr>
<tr>
	<td colspan='6' height='25' style='padding:0px 0px;'>
		<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>발주상품</b>
	</td>
</tr>
<tr >
	<td width='100%' valign=top style='padding-top:3px;'>

	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center class='list_table_box'>
				<!--col width=5% -->
				<col width=10% >
				<col width='*' >
				<col width=10% >
				<col width=8% >
				<col width=8% >
				<col width=8% >
				<!--col width=8% >
				<col width=7% >
				<col width=6% -->
				<!--col width=13% -->
				<tr align=center height=30 style='font-weight:bold;' >
					<!--td class=s_td><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.listform)'></td-->
					<td class=s_td>입고처</td>
					<td class=m_td>이미지/상품명</td>
					<td class=m_td>옵션명(규격)</td>
					<td class=m_td>발주수량</td>
					<!--td class=m_td>판매가</td-->
					<td class=m_td>구매가(원가)</td>
					<!--td class=m_td>공급가</td-->
					<td class=e_td>합계</td>
					<!--td class=m_td nowrap>공급율</td-->
					<!--td class=e_td>취소</td-->
				</tr>";


if($order_list_type == "P"){
/*
	$sql = "select odt.*,  listprice, coprice, sellprice , r.cid, ici.customer_name, pod.option_price
				from  inventory_order_detail_tmp odt left join shop_product_options_detail pod on odt.pid = pod.pid and odt.opn_ix = pod.opn_ix and odt.opnd_ix = pod.id ,
				shop_product sp left join ".TBL_SHOP_PRODUCT_RELATION." r on sp.id = r.pid and r.basic = '1',
				inventory_customer_info ici
				where ici.ci_ix = odt.ci_ix
				and odt.pid = sp.id and charger_ix = '".$_SESSION["admininfo"]["charger_ix"]."' and odt.order_yn = 'Y'
				order by odt.regdate asc , pid , opnd_ix";
*/
	$sql = "select odt.*, ici.customer_name ,g.cid
					from  inventory_order_detail_tmp odt left join inventory_goods_item gi on odt.gid = gi.gid and odt.gi_ix = gi.gi_ix ,
					inventory_goods g ,
					inventory_customer_info ici
					where ici.ci_ix = odt.ci_ix
					and odt.gid = g.gid and charger_ix = '".$_SESSION["admininfo"]["charger_ix"]."' and odt.order_yn = 'Y'
					order by odt.regdate asc , odt.gid , odt.gi_ix";
}else{
/*
	$sql = "select odt.*,  listprice, coprice, sellprice , r.cid, ici.customer_name, pod.option_price
				from  inventory_order_detail_tmp odt left join shop_product_options_detail pod on odt.pid = pod.pid and odt.opn_ix = pod.opn_ix and odt.opnd_ix = pod.id ,
				shop_product sp left join ".TBL_SHOP_PRODUCT_RELATION." r on sp.id = r.pid and r.basic = '1',
				inventory_customer_info ici
				where ici.ci_ix = odt.ci_ix
				and odt.pid = sp.id and odt.company_id = '".$_SESSION["admininfo"]["company_id"]."' and odt.order_yn = 'Y'
				order by odt.regdate asc , pid , opnd_ix";
*/
	$sql = "select odt.* , ici.customer_name ,g.cid
					from  inventory_order_detail_tmp odt left join inventory_goods_item gi on odt.gid = gi.gid and odt.gi_ix = gi.gi_ix ,
					inventory_goods g ,
					inventory_customer_info ici
					where ici.ci_ix = odt.ci_ix
					and odt.gid = g.gid and odt.company_id = '".$_SESSION["admininfo"]["company_id"]."' and odt.order_yn = 'Y'
					order by odt.regdate asc , odt.gid , odt.gi_ix";
}



$db->query($sql);
$order_goods_total = $db->total;
if($db->total){

	for($i=0;$i<$db->total;$i++){
		$db->fetch($i);

			$ci_ix = $db->dt[ci_ix];
			$gid = $db->dt[gid];
			$gname = $db->dt[gname];
			$gname_str .= $db->dt[gname];
			$order_cnt    = $db->dt[order_cnt];
			$order_coprice = $db->dt[order_coprice];
			//$options    = $db->dt[options];
			//$option_serial    = $db->dt[option_serial];
			//$coprice = $db->dt[coprice];
			//$listprice = $db->dt[listprice];
			/*
			if($db->dt[option_price] > 0){
				$sellprice = $db->dt[option_price];
			}else{
				$sellprice = $db->dt[sellprice];
			}
			*/
			$totalprice = $order_cnt*$order_coprice;
			$order_totalprice = $order_totalprice + $totalprice;
			//$coper = $coprice / $sellprice * 100;

			//$db->query("SELECT listprice, sellprice,  admin,company, state FROM ".TBL_SHOP_PRODUCT." WHERE id='$pid'");
			//$db->fetch();


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "c"))) {
				$img_str = InventoryPrintImage($admin_config[mall_data_root]."/images/inventory", $gid, "c");
			}else{
				$img_str = "../image/no_img.gif";
			}

$Contents .="
				<tr height=55>
					<!--td class='list_box_td list_bg_gray'>
						<input type=checkbox class=nonborder id='cgid' name=cgid[] value='".$db->dt[gid]."'><!--input type=hidden class=nonborder id='cgid' name=cgid[] value='".$db->dt[id]."'-->
					</td-->
					<td height='55' align=center>".$db->dt[customer_name]."</td>
					<td height='55' style='padding:5px;' nowrap>
						<table>
							<tr>
								<td><a href='./inventory_goods_input.php?gid=$gid'><img src='$img_str' border=0 width=50 height=50 align=left></a></td>
								<td style='line-height:150%;padding-left:5px;'>
								<span class='small'>".getIventoryCategoryPathByAdmin($db->dt[cid], 4)."</span><br>
								<b>$gname </b>";
/*
								".($db->dt[state] == "0" ? "<img src ='/data/sigong/templet/basic/sigong_img/btn_soldout.jpg' align='absmiddle'>" :"")."

							for($o=0; $o<count($options); $o++){
							$Contents .= getOptionName($options[$o]);
							}
*/
		$Contents .="
								</td>
							</tr>
						</table>


					</td>
					<td height='55' align=center>".$db->dt[item_name]."</td>
					<td height='55' nowrap>
						<div align='center'>$order_cnt<input type=hidden class='textbox' name=order_cnt id='order_cnt_".$option_serial."' value='$order_cnt' size=5 class=input2 style='text-align:right;padding:0 5px 0 0' >  개</div>
					</td>
					<!--td height='55' align=center>".number_format($listprice)."</td-->
					<td height='55' align=center>".$order_coprice."<input type=hidden class='textbox' name='order_coprice' id='order_coprice_".$option_serial."' value='".$order_coprice."' size=5 class=input2 style='text-align:right;padding:0 5px 0 0;' >

					</td>
					<!--td height='55' align=center>".number_format($coprice)."</td-->
					<td height='55' align=center>".number_format($totalprice)."</td>
					<!--td height='55' align=center>".number_format($coper)."%</td-->
					<!--td height='55' align=center style='padding:5px 5px;'>
					<A href=\"javascript:num_apply('".$option_serial."','".$gid."');\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' align=absmiddle border=0></a>
					<a href=\"javascript:deleteOrderInfo('delete', '".$db->dt[iodt_ix]."')\"'><img src='../images/".$admininfo["language"]."/btn_del.gif' border='0' align=absmiddle></a>
					</td-->
				</tr>";
	}
}else{
$Contents .="
				<tr height=50><td colspan=6 align=center>발주상품 내역이  존재 하지 않습니다.</td></tr>
				";

}


$Contents .="
				<tr bgcolor=#ffffff height=35 >
					<!--td align='center' class=s_td></td-->
					<td colspan='4' class=m_td><b><font color='#333333'>총합계</font></b></td>
					<td align=center class=m_td colspan='2'><b> <font class='blk' style='font-size:18px;font-family:arial;'>".number_format($order_totalprice)." </font></b><font class='blk'> 원</font></td>
					<!--td class=e_td></td-->
				</tr>
			</table>
			<table cellpadding=0 cellspacing=0 border=0 width=100%>
				<tr>
					<td colspan=4 align=left>
						<table cellpadding=5>
							<tr height=40>
								<td><b><a href='cart.php'>이전</a></b></td>
							</tr>
						</table>
					</td>
					<td colspan=4>
						<table cellpadding=10 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center>
							<tr>
								<td align='right' style='font-size:18px;font-weight:bold;'>
								<!--상품금액  : <span class='blk' style='font-size:18px;font-family:arial;'>".number_format($order_totalprice)."</span> 원 +
								배송비 : <span class='blk' style='font-size:18px;font-family:arial;'>".number_format($total_delivery_price)."</span> 원 = -->
								총 주문금액 : <span class='blk' style='font-size:18px;font-family:arial;'>".number_format($order_totalprice + $total_delivery_price)."</span> 원</td>
							</tr>
						</table><!--f:buttonSection-->
					</td>
				</tr>
			</table><br><br>
";


$Contents .= "

	";

$est_delivery_zip = explode("-", $db1->dt[est_delivery_zip]);
$est_tel = explode("-", $db1->dt[est_tel]);
$est_mobile = explode("-", $db1->dt[est_mobile]);

$vdate = date("Y-m-d", time());
$today = date("Y-m-d", time());
$tommorw = date("Y-m-d", time()+84600);
$vyesterday = date("Y-m-d", time()-84600);
$voneweekago = date("Y-m-d", time()-84600*7);
$vtwoweekago = date("Y-m-d", time()-84600*14);
$vfourweekago = date("Y-m-d", time()-84600*28);
$vyesterday = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

$Contents .= "
<form  name='form' method='post' onsubmit='return CheckFormValue(this)' action='./cart.act.php' target='act'>
<input type=hidden name=act value='insert'>
<!--input type=hidden name='myreserve_price' value='$total_reserve'>
<input type=hidden name='total_cart_price' value='$order_totalprice'>
<input type=hidden name='order_goods_total' value='$order_goods_total'-->
<input type='hidden' id='code' value=''>
	<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center height='300px'>
		<col width='50%'>
		<col width='50%'>

		<tr>
			<td colspan='2' height='25' style='padding:0px 0px;'>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>발주내역저장</b>
			</td>
		</tr>
		<tr>
			<td colspan='2' style='padding:5px 0px;'>
				<table cellpadding=0 cellspacing=0 border=0 width=100% align=center class='input_table_box'>
						<col width='15%'>
						<col width='35%'>
						<col width='15%'>
						<col width='35%'>
						<!--tr height='25'>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사전 현지 운송료</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><input type='text' name='b_delivery_price' class='textbox' value='' style='height:20px;width:200px;' id='b_delivery_price'></td>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사후 현지 운송료</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><input type='text' name='a_delivery_price' class='textbox' value='' style='height:20px;width:200px;' id='a_delivery_price' onkeyup='sum_order_totalprice();'></td>
						</tr>
						<tr height='25'>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사전 현지 세금</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><input type='text' name='b_tax' class='textbox' value='' style='height:20px;width:200px;' id='b_tax'></td>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사후 현지 세금</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><input type='text' name='a_tax' class='textbox' value='' style='height:20px;width:200px;' id='a_tax' onkeyup='sum_order_totalprice();'></td>
						</tr>
						<tr height='25'>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사전 수수료</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><input type='text' name='b_commission' class='textbox' value='' style='height:20px;width:200px;' id='b_commission'></td>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>사후 수수료</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><input type='text' name='a_commission' class='textbox' value='' style='height:20px;width:200px;' id='a_commission' onkeyup='sum_order_totalprice();'></td>
						</tr-->
						<tr height='25'>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>발주상품 총 금액</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'>
							<input type=hidden  name='order_totalprice' value='$order_totalprice'/>
							<span id='order_totalprice'>".$order_totalprice."</span> 원</td>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>기타현지비용/수수료</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><span id='etc_price'>0</span> 원</td>
							<!--td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>배송료</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><span>".number_format($total_delivery_price)."원</span></td-->
						</tr>
						<tr height='25'>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>최종 결제 금액</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><input type=text class='textbox' name='order_pttotalprice' id='order_pttotalprice' style='width:200px' value='$order_totalprice'/> 원</td>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>납기일</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'>
								<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100%>
									<col width=70>
									<col width=*>
									<tr>
										<TD nowrap>
											<input type='text' name='limit_priod_s' class='textbox' value='' style='height:20px;width:70px;text-align:center;' id='start_datepicker' validation='true' title='납기일'> ~
											<input type='text' name='limit_priod_e' class='textbox' value='' style='height:20px;width:70px;text-align:center;' id='end_datepicker' validation='true' title='납기일'>
										</TD>
									</tr>
								</table>
							</td>
						</tr>
						<tr height='25'>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>작성자</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><span>".$admininfo[charger]."</span></td>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>결제라인</b></td>
							<td class='list_box_td' style='text-align:left; padding:3px 10px;'>
								".makeSelectBoxAuthorizationLine($al_ix, "al_ix",$reg_al_ix)."
							</td>
						</tr>
						<tr height='25'>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;' nowrap><b>발주업체명(입고처)</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><b>".SelectSupplyCompany($ci_ix,"ci_ix", "text")."</b></td>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>업체담당자</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;'><input type=text class='textbox' name='incom_company_charger' id=incom_company_charger style='width:200px'></td>
						</tr>
						<tr height='25'>
							<td class='list_box_td list_bg_gray' style='text-align:left; padding:10px;'><b>비고</b></td>
							<td class='list_box_td' style='text-align:left; padding:10px;' colspan=3><input type=text class='textbox' name='etc' id=order_etc style='width:700px'></td>
						</tr>
				</table>";

$Contents .= "

			</td>
		</tr>

		<tr height=40>
			<td colspan='2' style='padding:10px;' align=center>";
			if($order_goods_total == 0){
				$Contents .= " <img  src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"alert('발주하시고자 하는 상품이 한개 이상 선택되어야 합니다.\/n 발주예정상품에서 발주하시고자 하는 상품을 선택해주세요 ');\">";
			}else{
				$Contents .= " <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;'>";
			}
			$Contents .= "
			</td>
		</tr>
	</table>
</form>
	";

$Contents .= "
	</td>
</tr>
</table>

		";

$Script = "
<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<!--script type='text/javascript' src='../js/ui/ui.core.js'></script-->


<script language='JavaScript' >
function sum_order_totalprice(){
	//b_delivery_price = Number($('#b_delivery_price').val());
	a_delivery_price = Number($('#a_delivery_price').val());
	//b_tax = Number($('#b_tax').val());
	a_tax = Number($('#a_tax').val());
	//b_commission = Number($('#b_commission').val());
	a_commission = Number($('#a_commission').val());

	order_totalprice = Number($('#order_totalprice').text());

	etc_price = a_delivery_price+a_tax+a_commission;

	order_pttotalprice = order_totalprice+etc_price;

	$('#etc_price').text(etc_price)
	$('#order_pttotalprice').val(order_pttotalprice)
}

$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){

		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+3d');
		}
	}

	});


	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});


});




function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#priod\").val(FromDate);
}

function num_apply(cart_key, gid) {
	var quantity = parseInt($('#quantity_'+cart_key).val()) ;
	var sellprice = parseInt($('#sellprice_'+cart_key).val()) ;
	//alert('#sellprice_'+cart_key);
	//document.write('countadd.php?cart_key='+cart_key+'&gid='+gid+'&act=mod&count='+quantity+'&sellprice='+sellprice+'&mode=$mode&est_ix=$est_ix');
	window.frames['act'].location.href='countadd.php?cart_key='+cart_key+'&gid='+gid+'&act=mod&count='+quantity+'&sellprice='+sellprice+'&mode=$mode&est_ix=$est_ix';
}

function receiptChoice(clickType){

	if(clickType == '1'){
		document.getElementById('receipt_result1').style.display = 'none';
		document.getElementById('receipt_result2').style.display = '';
		document.getElementById('receipt_result3').style.display = '';
		document.getElementById('receipt_result1_1').style.display = '';
		document.getElementById('com_num_info').style.display = '';
			document.getElementById('receipt_result_non').style.display = 'none';
	}

	if(clickType == '2'){
		//alert(document.form.payment_div[0].checked);
			document.getElementById('receipt_result1').style.display = 'none';
			document.getElementById('receipt_result2').style.display = 'none';
			document.getElementById('receipt_result3').style.display = 'none';
			document.getElementById('receipt_result1_1').style.display = '';
			document.getElementById('com_num_info').style.display = '';
			document.getElementById('receipt_result_non').style.display = 'none';

		$('.valid').each(function(){
			$(this).attr('validation','false');
		})
	}

	if(clickType == '3'){

		document.getElementById('receipt_result1').style.display = 'none';
		document.getElementById('receipt_result2').style.display = 'none';
		document.getElementById('receipt_result3').style.display = 'none';
		document.getElementById('receipt_result1_1').style.display = 'none';
		document.getElementById('com_num_info').style.display = 'none';
		document.getElementById('receipt_result_non').style.display = '';
		$('.valid').each(function(){
			$(this).attr('validation','false');
		})
	}
}

function isEQ()	{

		var form = document.form;

		if (form.same[0].checked)
		{
			form.name_b.value = form.name_a.value;
			//form.mail_b.value = form.mail_a.value;
			form.zipcode1_b.value = form.zipcode1.value;
			form.zipcode2_b.value = form.zipcode2.value;
			form.addr1_b.value = form.addr1.value;
			form.addr2_b.value = form.addr2.value;
			form.tel1_b.value = form.tel1_a.value;
			form.tel2_b.value = form.tel2_a.value;
			form.tel3_b.value = form.tel3_a.value;
			form.pcs1_b.value = form.pcs1_a.value;
			form.pcs2_b.value = form.pcs2_a.value;
			form.pcs3_b.value = form.pcs3_a.value;

		}
		else
		{
			form.name_b.value = '';
			//form.mail_b.value = '';
			form.zipcode1_b.value = '';
			form.zipcode2_b.value = '';
			form.addr1_b.value = '';
			form.addr2_b.value = '';
			form.tel1_b.value = '';
			form.tel2_b.value = '';
			form.tel3_b.value = '';
			form.pcs1_b.value = '';
			form.pcs2_b.value = '';
			form.pcs3_b.value = '';
		}
	}

function isEQ_sc()	{

	var form1 = document.form;
	var form2 = document.sc_info;

	if (form.same_sc[0].checked)
	{
		form.sc_damdang.value = form.name_a.value;
		form.sc_mail.value = form.mail_a.value;
		form.sc_tel1.value = form.tel1_a.value;
		form.sc_tel2.value = form.tel2_a.value;
		form.sc_tel3.value = form.tel3_a.value;
		form.sc_pcs1.value = form.pcs1_a.value;
		form.sc_pcs2.value = form.pcs2_a.value;
		form.sc_pcs3.value = form.pcs3_a.value;

	}
	else
	{
		form.sc_damdang.value = form2.sc_damdang.value;
		form.sc_mail.value = form2.sc_mail.value;
		form.sc_tel1.value = form2.sc_damdang_tel1.value;
		form.sc_tel2.value = form2.sc_damdang_tel2.value;
		form.sc_tel3.value = form2.sc_damdang_tel3.value;
		form.sc_pcs1.value = form2.sc_damdang_pcs1.value;
		form.sc_pcs2.value = form2.sc_damdang_pcs2.value;
		form.sc_pcs3.value = form2.sc_damdang_pcs3.value;
	}
}

function idsearch() {
	var zip = window.open('./searchuser.php','','width=440,height=400,scrollbars=yes,status=no');
}

function input_text(){
	if($('#msg2').attr('rel') == 'first'){
		$('#msg2').val('');
		$('#msg2').attr('rel','');
	}
}

</Script>
";

if($view == "innerview"){

	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>".EstimateApplyList($cid,$depth)."</body></html>";

	echo "
	<Script>
	parent.document.getElementById('estimate_product_list').innerHTML = document.body.innerHTML;
	</Script>";
}else{
	$P = new LayOut;
	$P->addScript = $Script;
	$P->OnloadFunction = "";//MenuHidden(false);
	$P->prototype_use = false;
	$P->jquery_use = true;
	$P->strLeftMenu = inventory_menu();
	$P->strContents = $Contents;
	$P->Navigation = "재고관리 > 발주(사입)요청관리 > 발주요청(발주서)";
	$P->title = "발주요청(발주서)";
	$P->PrintLayOut();
}
function getCompanyCartAdmin($company_id,$delivery_company, $cart_key){
	global $user;
	$where = " cart_key = '$cart_key'";
	if($delivery_company == "MI"){
		$delivery_company_where = " and (c.delivery_company ='MI' or c.delivery_company = '') ";
	}else{
		$delivery_company_where = " and c.delivery_company = '$delivery_company' ";
	}
	$mdb = new Database;
	$admin_delievery_policy = getTopDeliveryPolicy($mdb);

	$sql = "select c.*,
			p.delivery_package,
			if(p.delivery_policy =1,
				(select if(delivery_policy = 1,'".$admin_delievery_policy[delivery_price]."',delivery_price) from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '$company_id' )
			,delivery_price) as delivery_price,
			(select if(delivery_policy = 1,'".$admin_delievery_policy[delivery_basic_policy]."',delivery_basic_policy) from ".TBL_COMMON_SELLER_DELIVERY." where company_id = '".$company_id."') as delivery_basic_policy
			from shop_cart c,shop_product p
			where $where and c.id = p.id and company_id = '".$company_id."'
			and c.delivery_company='$delivery_company'
			order by c.regdate desc ";//정렬이 delivery_price 인 것을 regdate 로 바꿈 kbk 11.10.10

	$mdb->query($sql);
	return $mdb->fetchall();
}
function giftRelation($total_price){
	global $db;

	$sql = "select * from shop_product where $total_price >= startprice and $total_price < endprice and product_type = '6' limit 4";
	$db->query($sql);

	$gift_product = $db->fetchall();

	return $gift_product;
}

function getScName($sc_code){
	global $db;

	$db->query("select sc_nm from shop_comm_sc where sc_code = '$sc_code' ");
	$db->fetch();

	return $db->dt[sc_nm];
}

/*

create table inventory_order (
loid varchar(20) not null,
order_charger varchar(255) null default null,
limit_priod varchar(10) null default null,
ci_ix varchar(255) null default null,
incom_company_charger varchar(255) null default null,
total_price int(10) null default 0,
total_add_price int(10) null default 0,
status varchar(2) default null ,
etc varchar(255) default null ,
regdate datetime not null,
primary key(loid));

*/

?>

