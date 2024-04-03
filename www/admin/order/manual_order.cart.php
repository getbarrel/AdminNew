<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");

include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] < 9){
	header("Location:/admin/seller/");
}

$db = new Database;
$db1 = new Database;
$db2 = new Database;

if ($act_c == "clear")
{
	if(is_array($ESTIMATE_INTRA)){
		session_unregister("ESTIMATE_INTRA");
		echo "<script>alert('기존세션이 있어서 초기화합니다.');location.href='estimate.intra.php';</script>";
	}
}



if ($ToYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	$before1month = mktime(0, 0, 0, date("m")-1  , 21, date("Y"));
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$startday = 2;
	$lastday = date('t', strtotime($today));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", time()-84600*(date("d")));
	$eDate = date("Y/m/".$lastday);

	$startDate = date("Ymd", time()-84600*(date("d")));
	$endDate = date("Ym".$lastday);
}else{
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());

	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;


	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;

}

	//print_r($ESTIMATE_INTRA);
$vdate = date("Ymd", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()-84600);
$voneweekago = date("Y/m/d", time()-84600*7);
$vtwoweekago = date("Y/m/d", time()-84600*14);
$vfourweekago = date("Y/m/d", time()-84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

if($mode == "et_update"){
	$sql = "select e.*,m.id from
				shop_estimates e left join shop_member m on e.ucode = m.code
				where est_ix = '".$est_ix."' ";

	$db1->query($sql);
	$db1->fetch();
	$est_zip = explode("-", $db1->dt[est_delivery_zip]);
	$est_tel = explode("-", $db1->dt[est_tel]);
	$est_mobile = explode("-", $db1->dt[est_mobile]);
	if($EstimateBool){
		$EstimateBool = false;
		session_register("EstimateBool");
		session_unregister("ESTIMATE_INTRA");
		$sql = "SELECT ed.*,p.coprice
					from shop_estimates_detail ed, shop_product p, shop_companyinfo ci
					WHERE est_ix = '".$est_ix."' and ed.pid = p.id and p.admin = ci.company_id
					ORDER BY ci.company_name desc";

		$db->query($sql);
		$num = 1;
		$sum = 0;
		for($j = 0; $j < $db->total; $j++)
		{
			$db->fetch($j);
			$option = $db->dt[options];
			$options = "";
			if($option) $options = explode("|",$option);
			$id = $db->dt[pid];
			if(is_array($options)){
				$option_serial = md5($id.serialize($options));
			}else{
				$option_serial = md5($id);
			}

			$ESTIMATE_INTRA[$option_serial] = array("pname"=>$db->dt[pname], "pcount"=>$db->dt[pcount], "coprice"=>$db->dt[coprice] ,"sellprice"=>$db->dt[sellprice] ,"id"=>$id, "cid"=>$cid, "pcode"=>$db->dt[pcode], "options"=>$options, "option_serial"=>$option_serial,"totalprice"=>$db->dt[sellprice]*$db->dt[pcount]);
		}
	session_register("ESTIMATE_INTRA");
	}
}

if ($act == "add" )
{
	$option_pric = 0;
	if(is_array($options)){
		$option_serial = md5($id.serialize($options));
		for($o=0; $o<count($options); $o++){
			$sql = "select option_price,option_coprice  from ".TBL_SHOP_PRODUCT."_option a where pid = '$id' and id ='".$options[$o]."' ";
			$db->query($sql);
			$db->fetch();
			$option_price = $db->dt[option_price];
		}
	}else{
		$option_serial = md5($id);
	}
	if($ESTIMATE_INTRA[$option_serial]) {
		$pcount = $ESTIMATE_INTRA[$option_serial][pcount] + $pcount;

		$db->query("SELECT pname, sellprice,  id,reserve,  coprice,  pcode FROM ".TBL_SHOP_PRODUCT." WHERE id='$id'");

		if ($db->total){
			$db->fetch();
			if($option_price > 0) $sellprice = $option_price;
			else $sellprice = $db->dt[sellprice];

			$ESTIMATE_INTRA[$option_serial] = array("pname"=>$db->dt[pname], "pcount"=>$pcount, "coprice"=>$db->dt[coprice] ,"sellprice"=>$sellprice ,"id"=>$id, "cid"=>$cid, "pcode"=>$db->dt[pcode], "options"=>$options, "option_serial"=>$option_serial,"totalprice"=>$sellprice*$pcount);
		}
	} else {
		$db->query("SELECT pname, sellprice,   id,reserve,  coprice,  pcode FROM ".TBL_SHOP_PRODUCT." WHERE id='$id'");

		if ($db->total){
			$db->fetch();
			if($option_price > 0) $sellprice = $option_price;
			else $sellprice = $db->dt[sellprice];

			$ESTIMATE_INTRA[$option_serial] = array("pname"=>$db->dt[pname], "pcount"=>$pcount, "coprice"=>$db->dt[coprice] ,"sellprice"=>$sellprice ,"id"=>$id, "cid"=>$cid, "pcode"=>$db->dt[pcode], "options"=>$options, "option_serial"=>$option_serial,"totalprice"=>$sellprice*$pcount);
		}
	}


	session_register("ESTIMATE_INTRA");

}

if ($act == "del")
{
	unset($ESTIMATE_INTRA[$option_serial]);

	session_register("ESTIMATE_INTRA");
}


$Contents =	"
<table cellpadding=0 cellspacing=0 border=0 width='100%'>
<script  id='dynamic'></script>
	<tr>
		<td align='left' colspan=4> ".GetTitleNavigation("수동주문", "상품관리 > 수동주문")."</td>
	</tr>";

$Contents .=	"
	<tr>
		<td valign=top style='padding-top:33px;'>
		</td>
		<td valign=top style='padding:0px;padding-top:0px;' id=product_list>";

$Contents .= "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
				<tr height=30>
					<td align=left>
					카트 상품수 : ".number_format(count($ESTIMATE_INTRA))." 개
					</td>
					<td align=right>".$str_page_bar."</td>
				</tr>
			</table>
			<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff width=100% align=center class='list_table_box'>

				<col width='*' >
				<col width=8% >
				<col width=10% >
				<col width=10% >
				<col width=10% >
				<col width=7% >
				<col width=10% >
				<col width=10% >
				<tr align=center height=30 style='font-weight:bold;' >
					<td class=s_td>제 품 명</td>
					<td class=m_td>수량</td>
					<td class=m_td>정가</td>
					<td class=m_td>판 매 가</td>
					<td class=m_td>공급가</td>
					<td class=m_td>합계</td>
					<td class=m_td nowrap>공급율</td>
					<td class=e_td>취소</td>
				</tr>";
if($ESTIMATE_INTRA){

	for ( reset($ESTIMATE_INTRA); $key = key($ESTIMATE_INTRA); next($ESTIMATE_INTRA) ){

			$value = pos($ESTIMATE_INTRA);
			$pid = $value[id];
			$pname = $value[pname];
			$pname_str .= $value[pname];
			$pcount    = $value[pcount];
			$options    = $value[options];
			$option_serial    = $value[option_serial];
			$coprice = $value[coprice];
			$sellprice = $value[sellprice];
			$totalprice = $value[totalprice];
			$estimate_totalprice = $estimate_totalprice + $totalprice;
			$coper = $coprice / $sellprice * 100;

			$db->query("SELECT listprice, sellprice,  admin,company, state FROM ".TBL_SHOP_PRODUCT." WHERE id='$pid'");
			$db->fetch();


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $pid, "s"))) {
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $pid, "s");
			}else{
				$img_str = "../image/no_img.gif";
			}

$Contents .="
				<tr height=55>
					<td height='55' style='padding:5px;'>
					<table>
					<tr>
						<td style='height:50px;width:50px;border:1px solid silver;'><a href='goods_view.php?id=$pid'><img src='$img_str' border=0 width=50 align=left ></a></td>
						<td>
						$pname ".($db->dt[state] == "0" ? "<img src ='/data/sigong/templet/basic/sigong_img/btn_soldout.jpg' align='absmiddle'>" :"")."
						";
					for($o=0; $o<count($options); $o++){
					$Contents .= getOptionName($options[$o]);
					}
$Contents .="
						</td>
					</tr>
					</table>


					</td>
					<td height='55' nowrap>
						<div align='center'><input type=text class='textbox' name=quantity id='quantity_".$option_serial."' value='$pcount' size=5 class=input2 style='text-align:right;padding:0 5px 0 0' >  개</div>
					</td>
					<td height='55' align=center>".number_format($db->dt[listprice])."</td>
					<td height='55' align=center><input type=text class='textbox' name='sellprice' id='sellprice_".$option_serial."' value='$sellprice' size=5 class=input2 style='text-align:right;padding:0 5px 0 0;' >

					</td>
					<td height='55' align=center>".number_format($coprice)."</td>
					<td height='55' align=center>".number_format($totalprice)."</td>
					<td height='55' align=center>".number_format($coper)."%</td>
					<td height='55' align=center>";
                    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
                        $Contents .="
					    <a href=\"javascript:num_apply('".$option_serial."','".$pid."');\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' align=absmiddle border=0></a>";
                    }else{
                        $Contents .="
                        <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' align=absmiddle border=0></a>";
                    }
                    if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
                        $Contents .="
					    <a href='manual_order.cart.php?act=del&option_serial=".$option_serial."'><img src='../images/".$admininfo["language"]."/btn_del.gif' border='0' align=absmiddle></a>";
                    }else{
                        $Contents .="
					    <a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btn_del.gif' border='0' align=absmiddle></a>";
                    }
                    $Contents .="
					</td>
				</tr>";
	}
}else{
$Contents .="
				<tr height=50><td colspan=8 align=center>견적상품 내역이  존재 하지 않습니다.</td></tr>
				";

}


$Contents .="
				<tr bgcolor=#ffffff height=35 >
					<td align='center' class=s_td><b><font color='#333333'>총합계</font></b></td>
					<td colspan='5' class=m_td></td>
					<td align=center class=m_td><b> <font color='FF4E00'>".number_format($estimate_totalprice)." </font></b><font color='FF4E00'> 원</font></td>
					<td class=e_td></td>
				</tr>

				<tr>
					<td colspan=8 align=right>
						<table cellpadding=5>
							<tr height=40>
								<!--td>
                                    <b>
                                        <img src='../images/".$admininfo["language"]."/bts_select_order.gif' align=absmiddle border=0>
                                    </b>
                                </td-->";
                            if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
                                $Contents .="
								<td>
                                    <b>
                                        <a href='manual_order.infoinput.php'><img src='../images/".$admininfo["language"]."/bts_all_order.gif' align=absmiddle border=0></a>
                                    </b>
                                </td>
								<td>
                                    <b>
                                        <a href='manual_order.php'><img src='../images/".$admininfo["language"]."/bts_shopping_ing.gif' align=absmiddle border=0></a>
                                    </b>
                                </td>";
                            }else{
                                $Contents .="
								<td>
                                    <b>
                                        <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/bts_all_order.gif' align=absmiddle border=0></a>
                                    </b>
                                </td>
								<td>
                                    <b>
                                        <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/bts_shopping_ing.gif' align=absmiddle border=0></a>
                                    </b>
                                </td>";
                            }    
                                $Contents .="
							</tr>
						</table>
					</td>
				</tr>
			</table><br><br>
		</td>
	</tr>
</table>
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



function deleteEstimate(act, est_ix){
	window.frames['act'].location.href='estimate.act.php?act='+act+'&est_ix='+est_ix+'&mode=$mode';
}

function num_apply(cart_key, pid) {
	var quantity = parseInt($('#quantity_'+cart_key).val()) ;
	var sellprice = parseInt($('#sellprice_'+cart_key).val()) ;
	//alert('#sellprice_'+cart_key);
	//document.write('manual_order.countadd.php?cart_key='+cart_key+'&PID='+pid+'&act=mod&count='+quantity+'&sellprice='+sellprice+'&mode=$mode&est_ix=$est_ix');
	window.frames['act'].location.href='manual_order.countadd.php?cart_key='+cart_key+'&PID='+pid+'&act=mod&count='+quantity+'&sellprice='+sellprice+'&mode=$mode&est_ix=$est_ix';
}

function num_p(cart_key, pid) {
	var quantity = parseInt($('#quantity_'+cart_key).val())+1 ;

	window.frames['act'].location.href='manual_order.countadd.php?PID='+pid+'&act=mod&count='+quantity;
}

function num_m(cart_key, pid) {

	if($('#quantity_'+cart_key).val() > 1) {
		var quantity = parseInt($('#quantity_'+cart_key).val()) -1;
		document.frames['act'].location.href='manual_order.countadd.php?PID='+pid+'&act=mod&count='+quantity;
	}else {
		$('#quantity_'+cart_key).val(1);
		alert('1개 이상 선택하셔야 합니다    ');
		return;
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
	$P->strLeftMenu = order_menu();
	$P->addScript = $Script;
	$P->Navigation = "주문관리 > 수동주문 > 수동주문카트";
	$P->title = "수동주문카트";
	$P->strContents = $Contents;
	$P->jquery_use = false;

	if ($category_load == "yes"){
		$P->OnLoadFunction = "zoomBox(event,this,200,400,0,90,img3)";
	}
	$P->PrintLayOut();
}
?>