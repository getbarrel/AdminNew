<?
include("../class/layout.class");
include_once("../store/md.lib.php");
include("../webedit/webedit.lib.php");
//include("./company.lib.php");
include ("../inventory/inventory.lib.php");

if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$menu_name = "견적서 작성";
}else{
	$menu_name = "견적서 작성";
}

if($info_type == ""){
	$info_type = "basic";
}

// current time

$db = new Database;
$db2 = new Database;
$cdb = new Database;
$pldb = new Database;
$mdb = new Database;

if($est_ix){
	
	$sql = "select
		e.*,
		ed.*,
		eg.*
		from
			shop_estimates as e
			inner join shop_estimates_guide as eg on (e.est_ix = eg.est_ix)
			inner join shop_estimates_detail as ed on (e.est_ix = ed.est_ix)
		where
			e.est_ix = '".$est_ix."'
	";
	$act = 'update';
	
	$db->query($sql);
	$db->fetch();

	$com_zip = explode("-",$db->dt[com_zip]);
	$com_number = explode ("-",$db->dt[com_number]);				//사업자번호
	$corporate_number = explode ("-",$db->dt[corporate_number]);	//법인번호
	$com_phone = explode ("-",$db->dt[com_phone]);					//대표번호
	$com_mobile = explode ("-",$db->dt[com_mobile]);				//대표 핸드폰번호
	$com_fax = explode ("-",$db->dt[com_fax]);						//대표 팩스번호
	$open_date = explode(" ",$db->dt[open_date]);					//설립일
	$tel = explode ("-",$db->dt[btel]);	//의뢰인 전화번호
	$mobile = explode ("-",$db->dt[bmobile]);	//의뢰인 전화번호
	$bzip = explode ("-",$db->dt[bzip]);	//의뢰인 전화번호
	$plan_date = explode (" ",$db->dt[plan_date]);	//의뢰인 전화번호
	$md_code = $db->dt[md_code];
	$event_text = $db->dt[basicinfo];
}else{
	$act = 'insert';
}



$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "견적서 관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 견적정보</b>  </div>")."</td>
	  </tr>
	  </table>";

$Contents01 .= "
	  <table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	  <colgroup>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
	  </colgroup>

	  <tr>
		<td class='input_box_title'> <b>견적서 명 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type=text name='estimate_title' value='".$db->dt[estimate_title]."' class='textbox'  style='width:300px' validation='true' title='견적서 명'>
		</td>
		<td class='input_box_title'>전시유무</td>
			<td class='input_box_item' colspan=3>
				<input type=radio name='disp' id='disp_1' value='1' ".($db->dt[disp] == '1' || $db->dt[disp] == ''?"checked":"")."><label for='disp_1'>전시</label>
				<input type=radio name='disp' id='disp_0' value='0' ".($db->dt[disp] == '0'?"checked":"")."><label for='disp_0'>미 전시</label>
			</td>
	  </tr>
	  <tr>
	    <td class='input_box_title'> <b>전시일자 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type=text  id='open_date' name='open_date' value='".$open_date[0]."' class='textbox'  style='width:100px' validation='false' title='전시일'> - 
		<input type=text  id='plan_date' name='plan_date' value='".$plan_date[0]."' class='textbox'  style='width:100px' validation='false' title='전시만료일'>
		</td>
		<td class='search_box_title' > 프론트 전시 구분</td>
		<td class='search_box_item' colspan=3>".GetDisplayDivision_estimate($mall_ix, "select")." <span class=small>전시 구분을 선택하실경우 해당 사이트에 노출됩니다.</span></td>
	  </tr>
	  <tr height='30' id='move_status_tr'>
	  <!--
			<td class='input_box_title'>회원 분류</td>
			<td class='input_box_item'>
				<input type=radio name='mem_type' id='mem_type_1' value='A' ".($db->dt[gu_status] == 'A' ?"checked":"")."><label for='mem_type_1'>전체회원</label>
				<input type=radio name='mem_type' id='mem_type_2' value='M' ".($db->dt[gu_status] == 'M' || $db->dt[gu_status] == ''?"checked":"")."><label for='mem_type_2'>일반회원</label>
				<input type=radio name='mem_type' id='mem_type_3' value='C' ".($db->dt[gu_status] == 'C'?"checked":"")."><label for='mem_type_3'>사업자 회원</label>
			</td>-->
			<td class='input_box_title'>담당MD</td>
			<td class='input_box_item' colspan='3'>
				".MDSelect($md_code)." 
			</td>
		</tr>
	</table><br>
";




$Contents01 .= "
	  <table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	  <colgroup>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
	  </colgroup>
	  <tr bgcolor='#F8F9FA'>
		<td colspan=4>
		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
			<tr>
			  <td height='30' colspan='3' style='padding:10px;'>
				 <textarea name='event_text' id='event_text' style='width:98%;height:1000px;display:block' $readonly>".$event_text."</textarea>
				<!-- html편집기 메뉴 종료 -->
			</td>
			</tr>
		</table>
		</td>
	  </tr>
	</table><br>";

$Contents01 .="<table width='100%' cellpadding=0 cellspacing=0>
					<tr height=30>
					<td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='vertical-align:middle;'><b style='vertical-align:middle;' class=blk> 상품리스트</b> <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,1,'productList_1','list2','estimate');\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a></div>")."</td>
					</tr>
					</table>";

$Contents01 .="<table border=0 cellpadding=0 cellspacing=0 width='100%'>
				<tr bgcolor='#ffffff'>
					<td style='height:300px;vertical-align:top;' id='group_product_area_1'>
						".relationProductList($est_ix, "clipart",$act)."
						<div style='clear:both;width:100%;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> </span></div>
					</td>
				</tr>
				<tr bgcolor='#F8F9FA'>
				<td colspan=2>";
$Contents01 .= "</td>
				</tr>
			</table><br>";



$ButtonString .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer;'>";

$Contents = "<form name='order_form' action='./estimate.act.php' method='post' onsubmit='return CheckFormValue(this)'style='display:inline;' enctype='multipart/form-data' target=''>";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."
<input name='estimate_div' type='hidden' value='1'>
<input name='estimate_type' type='hidden' value='1'>
<input name='act' type='hidden' value='$act'>
<input name='mmode' type='hidden' value='$mmode'>
<input type='hidden' name='depth' value='$depth'>
<input type='hidden' name='est_ix' value='$est_ix'>
<input name='info_type' type='hidden' value='$info_type'>";

//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents01."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
$Contents = $Contents."<tr><td align=center>".$ButtonString."</td></tr>";
$Contents = $Contents."</table></form><br><br>";


$Script = "<script type='text/javascript' src='../js/ms_productSearch.js'></script>
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.core.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.mouse.js'></script>
<script type='text/javascript' src='../display/relationAjaxForEvent.js'></script>

<script language='javascript' src='../display/event.write.js'></script>\n
<!--script language='JavaScript' src='../webedit/webedit.js'></script>\n
<script language='javascript' src='../include/DateSelect.js'></script>\n-->

<script type='text/javascript' src='../estimate/estimate.js'></script>
<script language='javascript'>

function init(){
	var frm = document.order_form;
	//Content_Input();
	//Init(frm);
	//alert(1);
	CKEDITOR.replace('event_text',{
		docType : '<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">',
		font_defaultLabel : '굴림',
		font_names : '굴림/Gulim;돋움/Dotum;바탕/Batang;궁서/Gungsuh;Arial/Arial;Tahoma/Tahoma;Verdana/Verdana',
		fontSize_defaultLabel : '12px',
		fontSize_sizes : '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;',
		language :'ko',
		resize_enabled : false,
		enterMode : CKEDITOR.ENTER_BR,
		shiftEnterMode : CKEDITOR.ENTER_P,
		startupFocus : false,
		uiColor : '#EEEEEE',
		toolbarCanCollapse : false,
		menu_subMenuDelay : 0,";
if($admininfo[admin_level]==8 && $act=='update' && $disp !='9' ){
$Script .= "
		readOnly : true, ";
}
$Script .= "
		toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About']],
		filebrowserImageUploadUrl : '/admin/ckeditor/upload.php',
		height:500});

	//onLoadDate('$sDate','$eDate');
}

function zipcode(type) {
	var zip = window.open('../member/zipcode.php?type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function idsearch() {
		var zip = window.open('../order/manual_order.searchuser.php?page_type=estimates','','width=440,height=400,scrollbars=yes,status=no');
	}

function ChangeAddress(obj){
	if(obj.checked){
		document.getElementById('input_address_area').style.display = '';
	}else{
		document.getElementById('input_address_area').style.display = 'none';
	}
}

function loadRegion(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/region.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadBranch(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/branch.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadTeam(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = '../store/team.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadSellerManager(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;
	var depth = sel.getAttribute('depth');
	window.frames['act'].location.href = '../store/sellermanager.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadperson(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	var key = sel.getAttribute('name');

	window.frames['act'].location.href = './person.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&key='+key;

}

function del(name,company_id){

    var select = confirm('삭제하시겠습니까?');

    if(select){
        $.ajax({
    			url: 'company.act.php',
    			type: 'get',
    			dataType: 'html',
    			data: {del : name, company_id : company_id, act : 'image_del'},
    			success: function(result){
    			    document.location.reload();
    			}
    	});
    }
    else{
        return false;
    }

}


$(document).ready(function() {

	$('#open_date').datepicker({
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: 'Kalender',
	});

	$('#plan_date').datepicker({
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: 'Kalender',
	});


	init()
});

function calcurate_maginrate(product_id){

	

		var dcprice = $('#dcprice_'+product_id).val();		//에누리액
		
		var unit_price;									//에누리단가
		var dc_unit_price;								//에누리견적가 단가
		var dc_tax;									//에누리견적가 세액
		var total_price;								//에누리견적가 공급가
		var discount_rate;								//할인율
		var amount = $('#amount_'+product_id).val();
		var sellprice;
	
			var sellprice  = $('#sellprice_'+product_id).val();	//에누리단가
			unit_price = sellprice - dcprice;
		
			$('#unit_price_'+product_id).val(unit_price);				//에누리단가
			$('#td_unit_price_'+product_id).html(FormatNumber(unit_price));				//에누리단가

			dc_unit_price = Math.round(unit_price/11*10*amount);		//에누리견적가 - 단가		td_dc_unit_price_0000002507

			$('#dc_unit_price_'+product_id).val(dc_unit_price);	
			$('#td_dc_unit_price_'+product_id).html(FormatNumber(dc_unit_price));	

			dc_tax = Math.floor(unit_price/11*amount);					//에누리견적가 - 세액
			$('#dc_tax_'+product_id).val(dc_tax);
			$('#td_dc_tax_'+product_id).html(FormatNumber(dc_tax));	
			
			total_price = unit_price * amount;							//에누리 견적가 - 공급가
			$('#total_price_'+product_id).val(total_price);
			$('#td_total_price_'+product_id).html(FormatNumber(total_price));
			
																			//할인률
			discount_rate = unit_price/sellprice*100;
			$('#discount_rate_'+product_id).val(Math.round(discount_rate));
			$('#td_discount_rate_'+product_id).html(Math.round(discount_rate)+'%');
			

	
}


function product_option(product_id){

	var product_id;
	var sell_type = $('#sell_type').val();
	var option_id = $('select[id=opn_ix_'+product_id+']').children('option:selected').val();
	console.log('./estimate.act.php?pid='+product_id+'&option_id='+option_id+'&act=search_productinfo&type='+sell_type);
	if(product_id){
	
		$.ajax({
		    url : './estimate.act.php',
		    type : 'get',
		    data : {pid:product_id,
					option_id:option_id,
					act:'search_productinfo',
					type:sell_type
					},
		    dataType: 'json',
		    error: function(data,error){// 실패시 실행함수 
		        alert(error);
			},
		    success: function(data){
				var listprice = data['listprice'];
				var sellprice = data['sellprice'];
				var coprice = data['coprice'];
				var unit_price ;
				var dcprice = $('#dcprice_'+product_id).val();
					
					$('#td_coprice_'+product_id).html(FormatNumber(coprice));
					$('#coprice_'+product_id).val(coprice);

					$('#td_listprice_'+product_id).html(FormatNumber(listprice));
					$('#listprice_'+product_id).val(listprice);

					$('#td_sellprice_'+product_id).html(FormatNumber(sellprice));
					$('#sellprice_'+product_id).val(sellprice);

					$('#dcprice_'+product_id).val('0');

					$('#td_unitprice_'+product_id).html(FormatNumber(sellprice));
					$('#unitprice_'+product_id).val(sellprice);

					$('#amount_'+product_id).val('1');
					unit_price = sellprice;	//에누리단가
					$('#unit_price_'+product_id).val(unit_price);				//에누리단가
					$('#td_unit_price_'+product_id).html(FormatNumber(unit_price));			//에누리단가
		
					dc_unit_price = Math.round(unit_price/11*10);
					$('#dc_unit_price_'+product_id).val(dc_unit_price);
					$('#td_dc_unit_price_'+product_id).html(FormatNumber(dc_unit_price));	

					dc_tax = Math.floor(unit_price/11);
					$('#dc_tax_'+product_id).val(dc_tax);
					$('#td_dc_tax_'+product_id).html(FormatNumber(dc_tax));	
					
					total_price = unit_price;
					$('#total_price_'+product_id).val(total_price);
					$('#td_total_price_'+product_id).html(FormatNumber(total_price));
					
					discount_rate = unit_price/sellprice*100;
					$('#discount_rate_'+product_id).val(Math.round(discount_rate));
					$('#td_discount_rate_'+product_id).html(Math.round(discount_rate)+'%');

            }
        });
	}
}

</script>

";

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = estimate_menu();
	$P->strContents = $Contents;
	$P->Navigation = "견적서 관리 > 견적서 관리 > $menu_name";
	$P->NaviTitle = " $menu_name";
	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = estimate_menu();
	$P->strContents = $Contents;
	$P->Navigation = "견적서 관리 > 견적서 관리 > $menu_name";
	$P->title = "$menu_name";
	echo $P->PrintLayOut();
}



function relationProductList($est_ix, $disp_type="",$act ='insert'){

	global $start,$page, $orderby, $admin_config, $erpid, $pldb;

	$max = 105;
	$group_code = 1;
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;
	$sql = "select
				count(*) as total
			from
				".TBL_SHOP_ESTIMATES." as e
				inner join ".TBL_SHOP_ESTIMATES_DETAIL." as ed on (e.est_ix = ed.est_ix)
			where
				e.est_ix = '".$est_ix."'";

	$db->query($sql);
	$total = $db->total;
	
	$sql = "select
				ed.*,
				p.coprice,
				ed.pname as product_name
			from
				".TBL_SHOP_ESTIMATES." as e
				inner join ".TBL_SHOP_ESTIMATES_DETAIL." as ed on (e.est_ix = ed.est_ix)
				inner join ".TBL_SHOP_PRODUCT." as p on (ed.pid = p.id)
			where
				e.est_ix = '".$est_ix."'
				order by ed.estd_ix ASC
		";

	$db->query($sql);

	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<table width="100%" id="productList_1" name="productList" class="list_table_box" >
								<tr height="25">
									<td class="s_td" width="15%" rowspan="2">상품명</td>
									<td class="m_td" width="10%" rowspan="2">옵션</td>
									<td class="m_td" width="7%" rowspan="2">매입가</td>
									<td class="m_td" width="7%" rowspan="2">정가</td>
									<td class="m_td" width="7%" rowspan="2">판매가(할인가)</td>
									<td class="m_td" width="7%" rowspan="2">에누리액</td>
									<td class="m_td" width="7%" rowspan="2">에누리단가</td>
									<td class="m_td"  width="7%"rowspan="2">수량</td>
									<td class="m_td" width="20%" colspan="3">에누리견적가</td>
									<td class="e_td"width="7%" rowspan="2">할인율%</td>
								</tr>
								<tr height="25">
									<td class="s_td"  width="5%">단가</td>
									<td class="m_td" width="5%">세액</td>
									<td class="e_td" width="5%">공급가</td>
								</tr>
								<tr id="non_result_area" height=50><td colspan=13 style="text-align:center">견적상품을 선택해주세요</td></tr>
							</table>';
		}
	}else{

		if($disp_type == "clipart"){
			$mString = '<table width="100%" id="productList_1" name="productList" class="list_table_box" >
								<tr height="25">
									<td class="s_td" width="15%" rowspan="2">상품명</td>
									<td class="m_td" width="10%" rowspan="2">옵션</td>
									<td class="m_td" width="7%" rowspan="2">매입가</td>
									<td class="m_td" width="7%" rowspan="2">정가</td>
									<td class="m_td" width="7%" rowspan="2">판매가(할인가)</td>
									<td class="m_td" width="7%" rowspan="2">에누리액</td>
									<td class="m_td" width="7%" rowspan="2">에누리단가</td>
									<td class="m_td"  width="7%"rowspan="2">수량</td>
									<td class="m_td" width="20%" colspan="3">에누리견적가</td>
									<td class="e_td"width="7%" rowspan="2">할인율%</td>
								</tr>
								<tr height="25">
									<td class="s_td"  width="5%">단가</td>
									<td class="m_td" width="5%">세액</td>
									<td class="e_td" width="5%">공급가</td>
								</tr>
							';

			for($i=0;$i<$db->total;$i++){

				$db->fetch($i);
				
				$unit_price = $db->dt[sellprice] - $db->dt[discountprice];
				$mString .= "
						<tr align='center' height='27'>
							<td id='li_productList_1_".$db->dt[pid]."'>".$db->dt[product_name]."
							<input type='hidden' name='goods_infos[".$db->dt[pid]."][listPid]' class='listPid' value='".$db->dt[pid]."'>
							<input type='hidden' class='pName' id='pName_1_".$db->dt[pid]."' value='".$db->dt[product_name]."'>
							<input type='hidden' name='rpid[1][]' value='".$db->dt[pid]."'>
							<input type='hidden' id='brandName_1_".$db->dt[pid]."' value=''>
							<input type='hidden' id='pName_1_".$db->dt[pid]."' value='".$db->dt[product_name]."'>
							<input type='hidden' id='pPrice_1_".$db->dt[pid]."' value='".$db->dt[sellprice]."'>
							<input type='hidden' id='coprice_1_".$db->dt[pid]."' value='".$db->dt[coprice]."'>
							<input type='hidden' id='wholesale_price_1_".$db->dt[pid]."' value='".$db->dt[wholesale_price]."'>
							<input type='hidden' id='wholesale_sellprice_1_".$db->dt[pid]."' value='".$db->dt[wholesale_sellprice]."'></td>
							<td id='td_opn_ix_".$db->dt[pid]."'>
								<select name='goods_infos[".$db->dt[pid]."][opn_ix]' id='opn_ix_".$db->dt[pid]."' onchange=product_option('".$db->dt[pid]."');  style='width:200px;'>";
							$sql = "select pod.id,pod.option_div from shop_product_options as po inner join shop_product_options_detail as pod on (po.opn_ix = pod.opn_ix) where po.pid='".$db->dt[pid]."' and po.option_kind = 'b' order by pod.id ASC";
					
							$pldb->query($sql);
							$option_detail = $pldb->fetchall();
							if(count($option_detail) > 0){
								for($j=0;$j<count($option_detail);$j++){
								
									$mString .= "<option value='".$option_detail[$j][id]."' ".($db->dt[opn_ix] == $option_detail[$j][id]? "selected":"").">".$option_detail[$j][option_div]."</option>";
								}
							}else{
								$mString .= "<option value=''>가격+재고관리 옵션 없음</option>";
							}
							
					$mString .= "
								</select>
							</td>
							<td id='td_coprice_".$db->dt[pid]."'>".number_format($db->dt[coprice])."</td><input type='hidden' class='textbox number' name='goods_infos[".$db->dt[pid]."][coprice]' id='coprice_".$db->dt[pid]."' value='".$db->dt[coprice]."' style='width:30px;'>

							<td id='td_listprice_".$db->dt[pid]."'>".number_format($db->dt[listprice])."</td><input type='hidden' class='textbox number' name='goods_infos[".$db->dt[pid]."][listprice]' id='listprice_".$db->dt[pid]."' value='".$db->dt[listprice]."' style='width:80px;'>

							<td id='td_sellprice_".$db->dt[pid]."'>".number_format($db->dt[sellprice])."</td><input type='hidden' class='textbox number' name='goods_infos[".$db->dt[pid]."][sellprice]' id='sellprice_".$db->dt[pid]."' value='".$db->dt[sellprice]."' style='width:80px;'>

							<td id='td_dcprice_".$db->dt[pid]."'><input type='text' class='textbox number' name='goods_infos[".$db->dt[pid]."][dcprice]' id='dcprice_".$db->dt[pid]."' onkeyup=calcurate_maginrate('".$db->dt[pid]."') value='".$db->dt[discountprice]."' style='width:60px;'></td>

							<td id='unit_price_".$db->dt[pid]."'>".number_format($unit_price)."</td><input type='hidden' class='textbox number' name='goods_infos[".$db->dt[pid]."][unit_price]' id='unit_price_".$db->dt[pid]."' value='".$unit_price."' style='width:60px;' readonly=''>

							<td id='td_amount_".$db->dt[pid]."'><input type='text' class='textbox number' name='goods_infos[".$db->dt[pid]."][amount]' id='amount_".$db->dt[pid]."' onkeyup=calcurate_maginrate('".$db->dt[pid]."') value='".$db->dt[pcount]."' style='width:30px;'></td>

							<td id='td_dc_unit_price_".$db->dt[pid]."'>".number_format(round($db->dt[sellprice]/11*10*$db->dt[pcount]))."</td>
							<input type='hidden' class='textbox number' name='goods_infos[".$db->dt[pid]."][dc_unit_price]' id='dc_unit_price_".$db->dt[pid]."' value='".round($db->dt[sellprice]/11*10*$db->dt[pcount])."' style='width:30px;'>

							<td id='td_dc_tax_".$db->dt[pid]."'>".number_format(round($db->dt[sellprice]/11*$db->dt[pcount]))."</td>
							<input type='hidden' class='textbox number' name='goods_infos[".$db->dt[pid]."][dc_tax]' id='dc_tax_".$db->dt[pid]."' value='".round($db->dt[sellprice]/11*$db->dt[pcount])."' style='width:30px;'>

							<td id='td_total_price_".$db->dt[pid]."'>".number_format($db->dt[totalprice])."</td>
							<input type='hidden' class='textbox number' name='goods_infos[".$db->dt[pid]."][total_price]' id='total_price_".$db->dt[pid]."' value='".$db->dt[totalprice]."' style='width:30px;'>

							<td id='td_discount_rate_".$db->dt[pid]."'>".$db->dt[rate]." %</td>
							<input type='hidden' class='textbox number' name='goods_infos[".$db->dt[pid]."][discount_rate]' id='discount_rate_".$db->dt[pid]."' value='".$db->dt[rate]."' style='width:30px;'>
						</tr>";
			}

			$mString .= "</table>";
		}
	}


	return $mString;

}

?>