<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");

$db = new Database;



$Contents =	"


<table cellpadding=0 cellspacing=0 border=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("발주상품", "재고관리 > 발주(사입)관리 > 발주상품")."</td>
	</tr>";

$Contents .=	"
	<tr>
		<td valign=top style='padding-top:33px;'>
		</td>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";


$Contents .= "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
				
				<tr>
					<td align='left' colspan=4 style='padding-bottom:15px;'>
						<div class='tab'>
							<table class='s_org_tab' style='width:100%' border=1>
							<tr>
								<td class='tab'>
									<table id='tab_01' ".($order_list_type == "" ? "class='on'":"")."  >
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='?order_list_type='\">업체별 발주예정 리스트</td>
										<th class='box_03'></th>
									</tr>
									</table>
									<table id='tab_02' ".($order_list_type == "P" ? "class='on'":"").">
									<tr>
										<th class='box_01'></th>
										<td class='box_02' onclick=\"document.location.href='?order_list_type=P'\">담당자별 발주예정 리스트</td>
										<th class='box_03'></th>
									</tr>
									</table>
									";
		$Contents .= "
								</td>
								<td class='btn' style='vertical-align:bottom;padding-bottom:5px;' align=right>
								카트 상품수 : ".number_format(count($db->total))." 개
								</td>
							</tr>
							</table>
							</div>
					</td>
				</tr>
			</table>";


if($order_list_type == "P"){
	$sql = "select distinct odt.ci_ix, ici.customer_name
				from inventory_customer_info ici , inventory_order_detail_tmp odt
				where ici.ci_ix = odt.ci_ix and charger_ix = '".$_SESSION["admininfo"]["charger_ix"]."' ";
}else{
	$sql = "select distinct odt.ci_ix, ici.customer_name
				from inventory_customer_info ici , inventory_order_detail_tmp odt
				where ici.ci_ix = odt.ci_ix and company_id = '".$_SESSION["admininfo"]["company_id"]."'  ";
	
}
$db->query($sql);

if($db->total){
	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
				$Contents .= InventoryOrderCart($order_list_type, $db->dt);
	}
}else{
		$Contents .= InventoryOrderCart($order_list_type, $db->dt);
}


$Contents .= "
		</td>
	</tr>
</table>
</form>
		<IFRAME id=bsframe name=bsframe src='' frameBorder=0 width=0 height=0 scrolling=no ></IFRAME>
		<!--iframe id='act' src='' width=0 height=0></iframe-->
			";


$Script = "<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<script type='text/javascript' src='../js/ui/ui.core.js'></script>
<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script>
<script language='javascript'>
$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});



function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

function unloading(){

	parent.document.getElementById('parent_save_loading').style.zIndex = '-1';
	parent.document.getElementById('loadingbar').innerHTML ='';
	parent.document.getElementById('save_loading').innerHTML ='';
	parent.document.getElementById('save_loading').style.display = 'none';
}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_display','batch_update_category','batch_update_reserve'); //,'batch_update_sms','batch_update_coupon'

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}

function WholeOrder(frm, ci_ix){
	$('.iodt_ix_'+ci_ix).each(function(){		
			$(this).attr('checked','checked');		
	});
	frm.submit();
}


function deleteOrderInfo(act, iodt_ix){
	if(confirm('해당 발주 내역을 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href='cart.act.php?act='+act+'&iodt_ix='+iodt_ix+'&mode=$mode';
	}
}

function num_apply(iodt_ix, pid) {
	var order_cnt = parseInt($('#order_cnt_'+iodt_ix).val()) ;
	var order_coprice = parseInt($('#order_coprice_'+iodt_ix).val()) ;
//
	window.frames['act'].location.href='cart.act.php?act=countadd&iodt_ix='+iodt_ix+'&order_cnt='+order_cnt+'&order_coprice='+order_coprice;
}

function num_p(cart_key, pid) {
	var quantity = parseInt($('#quantity_'+cart_key).val())+1 ;

	window.frames['act'].location.href='countadd.php?PID='+pid+'&act=mod&count='+quantity;
}

function num_m(cart_key, pid) {

	if($('#quantity_'+cart_key).val() > 1) {
		var quantity = parseInt($('#quantity_'+cart_key).val()) -1;
		document.frames['act'].location.href='countadd.php?PID='+pid+'&act=mod&count='+quantity;
	}else {
		$('#quantity_'+cart_key).val(1);
		alert('1개 이상 선택하셔야 합니다    ');
		return;
	}
}
/*
function clearAll(frm){
		for(i=0;i < frm.iodt_ix.length;i++){
				frm.iodt_ix[i].checked = false;
		}
}
function checkAll(frm){
       	for(i=0;i < frm.iodt_ix.length;i++){
				frm.iodt_ix[i].checked = true;
		}
}
function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}
*/

function checkAll(ci_ix){
//alert(ci_ix);
	if($('#all_fix_'+ci_ix).is(':checked')){
			$('.iodt_ix_'+ci_ix).each(function(){
				if($(this).is(':checked')){
					$(this).attr('checked','');
				}else{
					$(this).attr('checked','checked');
				}
			})
	}else{
		$('.iodt_ix_'+ci_ix).each(function(){
			$(this).attr('checked','');
		})
	}

}

</script>
";


//$Contents .= HelpBox("수동주문", $help_text);
$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";
if($view == "innerview"){
	echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'><body>$innerview</body></html>";
	$inner_category_path = getCategoryPathByAdmin($cid2, $depth);
	echo "
	<Script>
	//alert(document.body.innerHTML);
	parent.document.getElementById('product_list').innerHTML = document.body.innerHTML;
	try{
	parent.document.getElementById('select_category_path1').innerHTML=\"".($search_text == "" ? $inner_category_path."(".$total."개)":"<b style='color:red'>'$search_text'</b> 로 검색된 결과 입니다.")."\" ;
	}catch(e){}
	parent.document.search_form.cid2.value ='$cid2';
	parent.document.search_form.depth.value ='$depth';

	</Script>";
}else{
	$Script .= "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>\n
	<!--script Language='JavaScript' src='product_input.js'></script--><!--2011.06.18 없는게 정상 주석처리후 확인필요-->
	<script Language='JavaScript' src='product_list.js'></script>
	<script Language='JavaScript' src='../js/scriptaculous.js' type='text/javascript'></script>
	<script Language='JavaScript' type='text/javascript'>
	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		var depth = sel.getAttribute('depth');
		//alert(depth);
		//dynamic.src = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		//alert(1);
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	function loadChangeCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;
		//var depth = sel.depth;
		var depth = sel.getAttribute('depth');

		//dynamic.src = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		window.frames['act'].location.href = '../product/category.load3.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

	}
	</script>";

	$P = new LayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 발주(사입)요청관리 > 발주예정상품리스트";
	$P->title = "발주예정상품리스트";
	$P->strContents = $Contents;
	$P->jquery_use = false;

	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}



function InventoryOrderCart($order_list_type, $supply_company_info){
	global $admin_config, $admininfo;
$mdb = new Database;

if($order_list_type == "P"){

	$sql = "select odt.*,  listprice, coprice, sellprice , r.cid, ici.customer_name, pod.option_price
				from  inventory_order_detail_tmp odt left join shop_product_options_detail pod on odt.pid = pod.pid and odt.opn_ix = pod.opn_ix and odt.opnd_ix = pod.id ,
				shop_product sp 
				left join ".TBL_SHOP_PRODUCT_RELATION." r on sp.id = r.pid and r.basic = '1',			
				inventory_customer_info ici 
				where ici.ci_ix = odt.ci_ix and odt.ci_ix = '".$supply_company_info[ci_ix]."' 
				and odt.pid = sp.id and charger_ix = '".$_SESSION["admininfo"]["charger_ix"]."' 
				order by odt.regdate asc , pid , opnd_ix";
}else{
	$sql = "select odt.*,  listprice, coprice, sellprice , r.cid, ici.customer_name, pod.option_price
				from  inventory_order_detail_tmp odt left join shop_product_options_detail pod on odt.pid = pod.pid and odt.opn_ix = pod.opn_ix and odt.opnd_ix = pod.id ,
				shop_product sp 
				left join ".TBL_SHOP_PRODUCT_RELATION." r on sp.id = r.pid and r.basic = '1',		
				inventory_customer_info ici 
				where ici.ci_ix = odt.ci_ix and odt.ci_ix = '".$supply_company_info[ci_ix]."' 
				and odt.pid = sp.id and company_id = '".$_SESSION["admininfo"]["company_id"]."' 
				order by odt.regdate asc , pid , opnd_ix";
}
$mdb->query($sql);

$mstring = "<form name='listform_".md5($supply_company_info[ci_ix])."' method=post action='cart.act.php' onsubmit='return SelectUpdate2(this)'><!-- target='act'--><!--onsubmit='return CheckDelete(this)' target='act'-->
<input type='hidden' name='act' value='select_order'>
<input type='hidden' id='pid' value=''>
<input type='hidden' name='order_list_type' id='order_list_type' value='".$order_list_type."'>
<input type='hidden' name='ci_ix' id='ci_ix' value='".$supply_company_info[ci_ix]."'>
<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% >
<tr>
	<td colspan='2' height='25' style='padding:0px 0px;'>
		<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>".$supply_company_info[customer_name]." 발주내역 </b>
	</td>
</tr>
</table>
<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center class='list_table_box'>
				<col width=5% >
				<col width=8% >
				<col width='*' >
				<col width=10% >
				<col width=8% >
				<col width=8% >
				<col width=8% >
				<col width=8% >
				<col width=7% >
				<col width=6% >
				<col width=13% >
				<tr align=center height=30 style='font-weight:bold;' >
					<td class=s_td><input type=checkbox class=nonborder name='all_fix' id='all_fix_".md5($supply_company_info[ci_ix])."'  onclick=\"checkAll('".md5($supply_company_info[ci_ix])."')\"></td>
					<td class=m_td>입고처</td>
					<td class=m_td>이미지/상품명</td>
					<td class=m_td>과세여부</td>
					<td class=m_td>옵션명(규격)</td>
					
					<td class=m_td>발주요청수량</td>
					<td class=m_td>판매가</td>
					<td class=m_td>구매가(원가)</td>
					<!--td class=m_td>공급가</td-->
					<td class=m_td>합계</td>
					<!--td class=m_td nowrap>공급율</td-->
					<td class=e_td>관리</td>
				</tr>";



if($mdb->total){

	for($i=0;$i<$mdb->total;$i++){
		$mdb->fetch($i);

			$pid = $mdb->dt[pid];
			$pname = $mdb->dt[pname];
			$pname_str .= $mdb->dt[pname];
			$order_cnt    = $mdb->dt[order_cnt];
			$options    = $mdb->dt[options];
			$option_serial    = $mdb->dt[option_serial];
			$order_coprice = $mdb->dt[order_coprice];
			$listprice = $mdb->dt[listprice];
			if($mdb->dt[option_price] > 0){
				$sellprice = $mdb->dt[option_price];
			}else{
				$sellprice = $mdb->dt[sellprice];
			}
			$totalprice = $order_cnt*$order_coprice;
			$order_totalprice = $order_totalprice + $totalprice;
			$coper = $order_coprice / $sellprice * 100;

			//$mdb->query("SELECT listprice, sellprice,  admin,company, state FROM ".TBL_SHOP_PRODUCT." WHERE id='$pid'");
			//$mdb->fetch();


			if(file_exists($_SERVER["DOCUMENT_ROOT"].PrintImage($admin_config[mall_data_root]."/images/product", $pid, "s"))) {
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $pid, "s");
			}else{
				$img_str = "../image/no_img.gif";
			}

$mstring .="
				<tr height=55>
					<td class='list_box_td list_bg_gray'>
						<input type=checkbox  id='iodt_ix' class='iodt_ix_".md5($supply_company_info[ci_ix])."' name=iodt_ix[] value='".$mdb->dt[iodt_ix]."'>
					</td>
					<td height='55' align=center>".$mdb->dt[customer_name]."</td>
					<td height='55' style='padding:5px;' nowrap>
						<table>
							<tr>
								<td><a href='goods_view.php?id=$pid'><img src='$img_str' border=0 width=50 height=50 align=left></a></td>
								<td style='line-height:150%;padding:5px 5px;' >
								<span class='small'>".getCategoryPathByAdmin($mdb->dt[cid], 4)."</span><br>
								<b>$pname </b>
								".($mdb->dt[state] == "0" ? "<img src ='/data/sigong/templet/basic/sigong_img/btn_soldout.jpg' align='absmiddle'>" :"")."
								";
							for($o=0; $o<count($options); $o++){
							$mstring .= getOptionName($options[$o]);
							}
		$mstring .="
								</td>
							</tr>
						</table>


					</td>
					<td class='list_box_td' title='".$mdb->dt[surtax_yorn]."' nowrap>
					 ".($mdb->dt[surtax_yorn] == "Y" ? "면세(비과세)":"과세")."
					</td>
					<td height='55' align=center>".$mdb->dt[option_name]."</td>
					<td height='55' nowrap>
						<div align='center'><input type=text class='textbox' name=order_cnt id='order_cnt_".$mdb->dt[iodt_ix]."' value='$order_cnt' size=5 class=input2 style='text-align:right;padding:0 5px 0 0' >  개</div>
					</td>
					<td height='55' align=center>".number_format($sellprice)."</td>
					<td height='55' align=center><input type=text class='textbox' name='order_coprice' id='order_coprice_".$mdb->dt[iodt_ix]."' value='".$order_coprice."' size=5 class=input2 style='text-align:right;padding:0 5px 0 0;' >

					</td>
					<!--td height='55' align=center>".number_format($order_coprice)."</td-->
					<td height='55' align=center>".number_format($totalprice)."</td>
					<!--td height='55' align=center>".number_format($coper)."%</td-->
					<td height='55' align=center style='padding:5px 5px;'>
					<A href=\"javascript:num_apply('".$mdb->dt[iodt_ix]."','".$pid."');\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' align=absmiddle border=0></a>
					<a href=\"javascript:deleteOrderInfo('delete', '".$mdb->dt[iodt_ix]."')\"'><img src='../images/".$admininfo["language"]."/btn_del.gif' border='0' align=absmiddle></a>
					</td>
				</tr>";
	}
}else{
$mstring .="
				<tr height=50><td colspan=10 align=center>발주예정 상품내역이  존재 하지 않습니다.</td></tr>
				";

}


$mstring .="
				<tr bgcolor=#ffffff height=35 >
					<td align='center' class=s_td></td>
					<td colspan='7' class=m_td><b><font color='#333333'>총합계</font></b></td>
					<td align=center class=m_td colspan='1'><b> <font color='FF4E00'>".number_format($order_totalprice)." </font></b><font color='FF4E00'> 원</font></td>
					<td class=e_td></td>
				</tr>
				<tr>
					<td colspan=10 align=right>
						<table cellpadding=3>
							<tr height=40>
								<td><input type=image src='../images/".$admininfo["language"]."/btn_select_order.gif' align=absmiddle border=0></td>
								<td><b><a href=\"javascript:WholeOrder(document.listform_".md5($supply_company_info[ci_ix]).",'".md5($supply_company_info[ci_ix])."');\"><img src='../images/".$admininfo["language"]."/btn_whole_order.gif' border='0' align=absmiddle> </a></b></td>
								<td><b><a href='order.php'><img src='../images/".$admininfo["language"]."/btn_add_goods.gif' border='0' align=absmiddle></a></b></td>
							</tr>
						</table>
					</td>
				</tr>
			</table><br><br>
			</form>";


return $mstring;

}
?>