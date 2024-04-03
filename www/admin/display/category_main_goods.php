<?
include("../class/layout.class");
include("./display.lib.php");
include("./category_main.lib.php");

if(!$agent_type){
	$agent_type = "W";
}

$sql = "SELECT cmg_title, cmg.cmg_link, cmg.display_position, cmg.cmg_ix, cmg.div_ix, goods_max, image_width, image_height, cmg_use_sdate , cmg_use_edate, cmg.md_mem_ix, cmg.sales_target, cmg.disp ,cmg.display_cid
			FROM shop_category_main_goods cmg, shop_category_main_div cmd
			where cmg.div_ix = cmd.div_ix and cmg.cmg_ix ='$cmg_ix'
			order by cmg_use_edate desc limit 0,1";

$slave_db->query($sql); //AND display_position='$display_position'
if($slave_db->total){
	$slave_db->fetch();
	$div_ix = $slave_db->dt[div_ix];
	$cmg_ix = $slave_db->dt[cmg_ix];
 	$cmg_title = $slave_db->dt[cmg_title];
	$goods_max = $slave_db->dt[goods_max];
	$image_width = $slave_db->dt[image_width];
	$image_height = $slave_db->dt[image_height];

	$cmg_use_sdate = date("Y-m-d",$slave_db->dt[cmg_use_sdate]);
	//$cmg_use_stime = date("H",$slave_db->dt[cmg_use_sdate]);
	//$cmg_use_smin = date("i",$slave_db->dt[cmg_use_sdate]);

	$cmg_use_edate = date("Y-m-d",$slave_db->dt[cmg_use_edate]);
	//$cmg_use_etime = date("H",$slave_db->dt[cmg_use_edate]);
	//$cmg_use_emin = date("i",$slave_db->dt[cmg_use_edate]);

	$md_mem_ix = $slave_db->dt[md_mem_ix];
	$sales_target = $slave_db->dt[sales_target];

	
	$display_cid = $slave_db->dt[display_cid];
	$display_position = $slave_db->dt[display_position];
	//echo $display_position;
	$disp = $slave_db->dt[disp];
	if($mode == "copy"){
		$act = "insert";
		$cmg_ix = "";
	}else{
		$act = "update";
	}
}else{
	$act = "insert";
}
//print_r($admininfo);
/*
$vdate = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()+84600);
$voneweeklater = date("Ymd", time()+84600*7);
$vtwoweeklater = date("Ymd", time()+84600*14);
$vfourweeklater = date("Ymd", time()+84600*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24);
$voneweeklater = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*7);
$v15later = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*15);
$vfourweeklater = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*28);
$vonemonthlater = date("Ymd",mktime(0,0,0,substr($vdate,4,2)+1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthlater = date("Ymd",mktime(0,0,0,substr($vdate,4,2)+2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthlater = date("Ymd",mktime(0,0,0,substr($vdate,4,2)+3,substr($vdate,6,2)+1,substr($vdate,0,4)));
*/
$Script = "<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<style type='text/css'>
  div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;border:1px solid silver }
  div#drop_relation_product.hover { border:5px dashed #aaa; background:#efefef; }
  table.tb {width:100%;cursor:move;}
</style>
<Script Language='JavaScript'>

$(function() {
			$(\"#start_datepicker\").datepicker({
			//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			//showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',
			onSelect: function(dateText, inst){
				//alert(dateText);
				if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
					$('#end_datepicker').val(dateText);
				//}else{
					//$('#end_datepicker').datepicker('setDate','+0d');
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

			//$('#end_timepicker').timepicker();
		});



		function select_date(FromDate,ToDate,dType) {
			var frm = document.searchmember;

			$(\"#start_datepicker\").val(FromDate);
			$(\"#end_datepicker\").val(ToDate);
		}

function category_del(group_code, el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory_'+group_code);
	obj.deleteRow(idx);
	var cObj=\$('input[name=basic]');
	if(cObj.length == null){
		//cObj[0].checked = true; // 0이 나오지 null이 나오지 않음 kbk
	}else{
		for(var i=0;i<cObj.length;i++){
			if(cObj[i].checked){
				return true;
				break;
			}else{
				cObj[0].checked = true;
			}
		}
	}
	//cate.splice(idx,1);
}

function SubmitX(frm){
	//alert(iView.document.body.innerHTML);
	//frm.content.value = iView.document.body.innerHTML;

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
}




function init(){
	var frm = document.main_frm;
	Content_Input();
	Init(frm);
	onLoadDate('$sDate','$eDate');
}

function onDropAction(mode, main_ix,pid)
{
	//outTip(img3);
	//alert(1);
	parent.document.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&main_ix='+main_ix+'&pid='+pid;

}

/*
function loadCategory(sel,target) {
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	window.frames['act'].location.href = '/admin/product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
}
*/


function loadCategory(obj,target) {
	
	var trigger = obj.find('option:selected').val();
	var form = obj.closest('form').attr('name');
	var depth = obj.attr('depth');//sel.getAttribute('depth');

	if(trigger == ''){
		if(depth == 0){
			$('#display_cid').val('');
		}else{
			$('#display_cid').val($('.display_cid[depth='+(depth-1)+']').val());
		}
	}else{
		$.ajax({ 
			type: 'GET', 
			data: {'return_type': 'json', 'form':form, 'trigger':trigger, 'depth':depth, 'target':target},
			url: '../product/category.load.php',  
			dataType: 'json', 
			async: true, 
			error: function(){ 
				//alert('error');
			},  
			beforeSend: function(){ 
				
			},  
			success: function(datas){
				$('select[class=display_cid]').each(function(){
					if(parseInt($(this).attr('depth')) > parseInt(depth)){
						$(this).find('option').not(':first').remove();
					}
				});
				 
				if(datas != null){
					$.each(datas, function(i, data){ 
							$('select[name='+target+']').append(\"<option value='\"+data.cid+\"'>\"+data.cname+\"</option>\");
					});  
				}
				$('#display_cid').val(trigger);
			} 
		}); 
	}
 
}



function loadPosition(sel,target) {
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	window.frames['act'].location.href = '/admin/display/category_main_position.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target;
}

$(document).ready(function () { 
	 $('.sortable').sortable();
 
	$('.add_type_choice li').click(function(){
		promotion_type_check_reset();
		var img_tag = $(this).find('img');
		//alert(img_tag.attr('src')+';;;'+img_tag.attr('src').indexOf('_on'));
		if(img_tag.attr('src').indexOf('_on') == -1){
			$(this).find('img').attr('src',img_tag.attr('src').replace('.png','_on.png'));
		}
	});
});

function promotion_type_check_reset(){
	//img reset
	$('.add_type_choice li').find('img').each(function( i, element ){
		$(element).attr('src', $(element).attr('src').replace('_on.png', '.png') );
	})
	//checkbox reset
	$('.promotion_types').find('input').attr('checked','');
}

function CopyDisplayType(jquery_obj, target_id, group_code){

	var newObj = jquery_obj.clone(true).appendTo($('#'+target_id));

	newObj.find('div[class^=control_view]').css('display','');
	newObj.find('input[type^=hidden]').attr('disabled','');
	newObj.find('input[type^=hidden]').attr('disabled',false);
	newObj.find('select[class^=set_cnt]').attr('disabled','');
	newObj.find('select[class^=set_cnt]').attr('disabled',false);
	newObj.css('margin','0 10px 0 0');
	newObj.get(0).onclick='';
	newObj.attr('onclick','');
	if(newObj.find('img').attr('src').indexOf('_on') == -1){
		newObj.find('img').attr('src',newObj.find('img').attr('src').replace('.png','_on.png'));
	}
	newObj.find('img').dblclick(function(){
		$(this).parent().remove();
		DisplayCntCalcurate(group_code);
	});
	
	newObj.find('select[class^=set_cnt]').change(function(){
		DisplayCntCalcurate(group_code);
	});

	
	DisplayCntCalcurate(group_code);
	
	$('#'+target_id).sortable();
}

function DisplayCntCalcurate(group_code){
	var product_cnt = 0;

	$('#display_type_area_'+group_code+' div.control_view').each(function(){
		//alert($(this).find('select[class^=set_cnt]').val()+':::'+$(this).find('select[class^=set_cnt]').attr('dt_goods_num'));
		product_cnt += $(this).find('select[class^=set_cnt]').val() * $(this).find('select[class^=set_cnt]').attr('dt_goods_num');
	});
	

	$('#product_cnt_'+group_code).val(product_cnt);
}


function ChangeDisplaySubType(obj, group_code, selected_value){
	obj.parent().parent().find('div[class^=goods_display_type_area]').hide();
	$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+selected_value).show();
}

/*
function SearchInfo(search_type, obj, group_code){
	//alert(event.code);
	if(search_type == 'B'){
		var act = 'search_brand';
	}else if(search_type == 'S'){
		var act = 'search_seller';
	}else{
		alert('선택된 정보가 올바르지 않습니다. 확인후 다시시도해주세요');
		return;
	}
		//alert(search_type+':::'+act+':::search_text:::'+obj.val());
		$.ajax({ 
			type: 'GET', 
			data: {'act': act, 'search_type':search_type, 'search_text':obj.val()},
			url: '../search.act.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){ 
				//alert(2)
			},  
			success: function(datas){ 
				//alert(datas);
				if(datas!=null){
					$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' #search_result_'+group_code+' option').each(function(){
						$(this).remove();
					});
					$.each(datas, function() {
						 //alert(this['brand_name']);
						 $('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#search_result_'+group_code).append('<option value=\"'+this['value']+'\" ondblclick=\"$(this).remove();\">'+this['text']+'</option>');
						// alert(this.age);
					});
				}
			} 
		}); 
 
}

function MoveSelectBox(search_type, type,group_code){
	//alert(group_code);
	if(type == 'ADD'){
		$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#search_result_'+group_code+' option:selected').each(function(){
			$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#selected_result_'+group_code).append('<option value='+$(this).val()+' ondblclick=\"$(this).remove();\" selected>'+$(this).html()+'</option>');

			var selected_value = $(this).val();

			$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#search_result_'+group_code+' option').each(function(){
				if($(this).val() == selected_value){
					$(this).remove();
				}
			});
		});
	}else{
		$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#selected_result_'+group_code+' option:selected').each(function(){
			$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#search_result_'+group_code).append('<option value='+$(this).val()+' ondblclick=\"$(this).remove();\" selected>'+$(this).html()+'</option>');
			
			var selected_value = $(this).val();
			
			$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+search_type+' SELECT#selected_result_'+group_code+' option').each(function(){
				if($(this).val() == selected_value){
					$(this).remove();
				}
			});
		});
	}
}

*/


function SearchInfo(search_type, obj, group_code){
	//alert(event.code);
	if(search_type == 'C'){
		var act = 'search_category';
	}else if(search_type == 'G'){
		var act = 'search_group';
	}else if(search_type == 'M'){
		var act = 'search_member';
	}else if(search_type == 'S'){
		var act = 'search_seller';
	}else if(search_type == 'B'){
		var act = 'search_brand';
	}else{
		alert('선택된 정보가 올바르지 않습니다. 확인후 다시시도해주세요');
		return;
	}
		//alert(search_type+':::'+act+':::search_text:::'+$(obj).find('input#search_text').val());
		$.ajax({ 
			type: 'GET', 
			data: {'act': act, 'search_type':search_type, 'search_text':$(obj).find('input#search_text').val()},
			url: '../search.act.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){ 
				//alert(2)
			},  
			success: function(datas){ 
				if(datas!=null){
					
					//alert('DIV#'+obj.attr('id')+' #search_result_'+group_code+' option');
					$('DIV#'+obj.attr('id')+' #search_result_'+group_code+' option').each(function(){					
						$(this).remove();
					});
					$.each(datas, function() {
						 //alert('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code);
						 //$('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code).
						 $('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code).append(\"<option value=\"+this['value']+\" ondblclick=\\\"MoveSelectBox( $('DIV#\"+obj.attr('id')+\"'), '\"+search_type+\"','ADD','\"+group_code+\"');\\\" >\"+this['text']+\"</option>\");
						 //append(\"<option value=\"'+this['value']+'\" ondblclick=\\\"MoveSelectBox( $('DIV#\"+obj.attr('id')+\"), '\"+search_type+\"','ADD','category');\\\">'+this['text']+'</option>');
						// alert(this.age);
					});
				}
			} 
		}); 
 
}


function MoveSelectBox(obj, search_type, type,group_code){
	if(type == 'ADD'){
		//alert(obj.attr('id'));
			$('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code+' option:selected').each(function(){
				//alert($(this).html());
				$('DIV#'+obj.attr('id')+' SELECT#selected_result_'+group_code).append(\"<option value=\"+$(this).val()+\" ondblclick=\\\"MoveSelectBox( $('DIV#\"+obj.attr('id')+\"'), '\"+search_type+\"','REMOVE','\"+group_code+\"');\\\" selected>\"+$(this).html()+\"</option>\");
				var selected_value = $(this).val();
				$('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code+' option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}
				});
			});
	}else{
			$('DIV#'+obj.attr('id')+' SELECT#selected_result_'+group_code+' option:selected').each(function(){
				$('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code).append(\"<option value=\"+$(this).val()+\" ondblclick=\\\"MoveSelectBox( $('DIV#\"+obj.attr('id')+\"'), '\"+search_type+\"','REMOVE','\"+group_code+\"');\\\" selected>\"+$(this).html()+\"</option>\");
				var selected_value = $(this).val();
				$('DIV#'+obj.attr('id')+' SELECT#selected_result_'+group_code+' option').each(function(){
					if($(this).val() == selected_value){
						$(this).remove();
					}else{
						$(this).attr('selected', 'selected');
					}
				});
			});
	}
}

function SelectedAll(jquery_obj, selected){
	$(jquery_obj).each(function(){
		$(this).attr('selected', selected);
	});
}

function changeDisplayInfo(jobj){
	if($(jobj).val() == 'P'){
		$(jobj).closest('tr').find('div[id^=goods_manual_choice_]').show();
		$(jobj).closest('tr').find('div[id^=goods_manual_area_]').show();
		$(jobj).closest('tr').find('div[id^=brands_manual_area_]').hide();
	}else if($(jobj).val() == 'B'){
		$(jobj).closest('tr').find('div[id^=goods_manual_choice_]').hide();
		$(jobj).closest('tr').find('div[id^=goods_manual_area_]').hide();
		$(jobj).closest('tr').find('div[id^=brands_manual_area_]').show();
	}
}

function changeBannerInfo(jobj){
	if($(jobj).val() == 'D'){
		$(jobj).closest('tr').find('div[id^=direct_area_]').show();
		$(jobj).closest('tr').find('div[id^=select_area_]').hide();
	}else if($(jobj).val() == 'S'){ 
		$(jobj).closest('tr').find('div[id^=direct_area_]').hide();
		$(jobj).closest('tr').find('div[id^=select_area_]').show();
	}
}
</Script>";


$Contents = "
<script type='text/javascript' src='/admin/js/jquery.multisortable.js'></script>
<script type='text/javascript' src='../js/ms_productSearch.js'></script>
<script type='text/javascript' src='../js/ms_brandSearch.js'></script>
<table width='100%' border='0' align='left' cellspacing='0' cellpadding='0'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("분류별 메인상품 등록", "전시관리 > 분류별 메인상품 등록 ")."</td>
</tr>
  <tr>
    <td>

        <form name='main_frm' method='post' onSubmit=\"return SubmitX(this)\" action='category_main_goods.act.php' style='display:inline;' enctype='multipart/form-data' target='iframe_act'>
		<input type='hidden' name=act value='update'>
		<input type='hidden' name='cid2' value=''>
		<input type='hidden' name=cmg_ix value='".$cmg_ix."'>

        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
			<td style='padding:0px 0px 20px 0px'>
			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05'  valign=top style='padding:0px'>
					<table border='0' cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
					  <col width='20%'>
					  <col width='30%'>
					  <col width='20%'>
					  <col width='30%'>
					  <tr height=28>
						<td class='search_box_title'  nowrap> <b>분류 메인 전시 분류</b></td>
						<td class='search_box_item'>".(($div_ix == "" || true) ? getCategoryMainDiv($div_ix,"selectbox"," onchange=\"loadPosition(this,'display_position')\" "):getCategoryMainDiv($div_ix,"text"))."</td>
						<td class='search_box_title' nowrap> <b>분류 전시 위치</b></td>
						<td class='search_box_item'> 
							".($display_position == "" ? getCategoryMainPosition($div_ix,$display_position,"selectbox"):getCategoryMainPosition($div_ix,$display_position,"selectbox"))."
							<!--".($display_position == "" ? getCategoryMainPositionSelectBox("display_position",$display_position):getCategoryMainPositionSelectBox("display_position",$display_position,"text"))."-->
						</td>
					  </tr>";

$Contents .= "
					  <tr height=28>
						<td class='search_box_title' nowrap> <b>노출 카테고리 선택</b></td>
						<td class='search_box_item' colspan=3>
						<input type=hidden name='display_cid' id='display_cid' value='".$display_cid."' validation=true tit='노출카테고리'>
							<table border=0 cellpadding=0 cellspacing=0>
								<tr>
									<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "class='display_cid' onChange=\"loadCategory($(this),'cid1_1',2)\" title='대분류' ", 0, $display_cid)."</td>
									<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "class='display_cid' onChange=\"loadCategory($(this),'cid2_1',2)\" title='중분류'", 1, $display_cid)."</td>
									<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "class='display_cid' onChange=\"loadCategory($(this),'cid3_1',2)\" title='소분류'", 2, $display_cid)."</td>
									<td>".getCategoryList3("세분류", "cid3_1", "class='display_cid' onChange=\"loadCategory($(this),'cid2',2)\" title='세분류'", 3, $display_cid)."</td>
								</tr>
							</table>
						</td>
					  </tr>";

$Contents .= "
					  <tr height=28>
						<td class='search_box_title' nowrap> <b>분류 메인 전시 제목</b></td>
						<td class='search_box_item'><input class='textbox' type='text' name='cmg_title' value='".$slave_db->dt[cmg_title]."' validation=true title='전시 제목' maxlength='50' style='width:96%'></td>
						<td class='search_box_title' > <b>노출여부</b> </td>
						<td class='search_box_item'  >
						<input type='hidden' name='pop' value='1'> <input type='radio' name='disp' id='disp_1' value='1' ".(($disp == "1" || $disp == "") ? "checked":"")." validation=true title='노출여부'> <label for='disp_1' >노출</label> <input type='radio' name='disp' id='disp_0' value='0' ".CompareReturnValue("0",$disp,"checked")." validation=true title='노출여부'><label for='disp_0' >노출안함</label>
						</td>
					  </tr>
					  <tr style='display:none'>
						  <td class='search_box_title'><b>분류 메인 전시제목 이미지</b></td>
						  <td class='search_box_item' style='padding:10px' colspan=3>
						  <input type='file' class='textbox' name='cmg_title_img' id='cmg_title_img' size=50 value=''> "; 
if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/title_img.jpg") && $cmg_ix){	
					$Contents .= "
						  <input type='checkbox' name='cmg_title_img_del' id='cmg_title_img_del' size=50 value='Y' style='vertical-align:middle;'><label for='cmg_title_img_del'>메인전시제목 이미지 삭제</label><br>";
}
					$Contents .= "
						  <div style='padding:5px 5px 5px 0px;height:50px;width:90%;overflow:auto' id='cate_mg_img_area'>";
if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/title_img.jpg") && $cmg_ix){//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/title_img.jpg'>"; 
}

$Contents .= "		</div><br>
						  <span class=small>* 이미지 등록을 하지 않을경우 메인전시제목이 노출됩니다. </span>
						  </td>
					  </tr>
					  <tr height=28>
						<td class='search_box_title' nowrap> <b>분류 메인 전시 분류 링크</b></td>
						<td class='search_box_item' colspan=3>
						<input class='textbox' type='text' name='cmg_link' value='".$slave_db->dt[cmg_link]."' validation=false title='메인전시 분류 링크' maxlength='50' style='width:400px'>
						</td>
					  </tr>
					 <tr height=27>
						  <td class='search_box_title' > <b>노출일자</b></td>
						  <td class='search_box_item'  colspan=3>
						  ".search_date('cmg_use_sdate','cmg_use_edate',$cmg_use_sdate,$cmg_use_edate,'Y',"","validation=true title='노출일자' ")."";
 
$Contents .= "
						  </td>
						</tr>
						<!--tr height=27>
							<td class='search_box_title' nowrap> <b>상품목록갯수</b></td>
							<td class='search_box_item' colspan=3>
							한페이지에 <input class='textbox number' type='text' name='goods_max' size=5 value='".$slave_db->dt[goods_max]."' maxlength='50' > 개의 상품을 노출합니다
							<div class=small style='display:inline;'>입력되지 않으면 기본 상품 노출갯수 15개로 노출됩니다.</div>
							</td>
						</tr>
						<tr height=27>
							<td class='search_box_title' nowrap> <b>상품이미지 사이즈</b></td>
							<td class='search_box_item' colspan=3 style='padding:5px;'>
								가로 : <input class='textbox number' type='text' name='image_width' size=5 value='".$image_width."' maxlength='50' >
								세로 : <input class='textbox number' type='text' name='image_height' size=5 value='".$image_height."' maxlength='50' ><br>
								<div class=small style='padding:4px 0px '>
								정보가 입력되지 않으면 기본 이미지 사이즈가 노출됩니다. <br>
								한쪽 사이즈만 입력되면 입력되지 않은 부분은 비율적으로 표시되게 됩니다.<br>
								</div>
								</td>
						</tr-->
						<tr>
							<td class='search_box_title' >  담당 MD</td>
							<td class='search_box_item'>  ".MDSelect($md_mem_ix)." </td>
							<td class='search_box_title' >  매출목표</td>
							<td class='search_box_item'><input class='textbox number' type='text' name='sales_target' size=15 value='".$sales_target."' maxlength='25'> 원</td>
						</tr>
					</table>
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
			</table>
			</td>
		  </tr>
		  <tr>
            <td bgcolor='#ffffff'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td >
                    <table border='0' cellpadding=0 cellspacing=0 width='100%'>


                    <tr>
                      <td  colspan='4' style='padding:0px;'>";
$gdb = new Database;
$sql = "SELECT * FROM shop_category_main_product_group where cmg_ix ='".$cmg_ix."' order by group_code asc ";
//echo $sql;
$gdb->query($sql);
//if($gdb->total){

	if($gdb->total)	$group_total = $gdb->total;
	else					$group_total =1;

	for($i=0;($i < $gdb->total || $i < 1);$i++){
	$gdb->fetch($i);
	$cmpg_ix = $gdb->dt[cmpg_ix];

$Contents .= "
                      <div id='group_info_area".$i."' group_code='".($i+1)."'>
                      <div style='padding:5px 5px'>
						<img src='/admin/images/dot_org.gif' align=absmiddle> <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>분류 메인상품그룹  (GROUP ".($i+1).")</b> <a onclick=\"add_table('cate_main')\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle style='cursor:pointer;'></a> ".($i == 0 ? "<!--삭제버튼-->":"<a onclick=\"del_table('group_info_area".$i."',".($i+1).");\"><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle style='cursor:pointer;'></a>")."</div>
                      <table width='100%' border='0' cellpadding='5' cellspacing='1' class='input_table_box'>
						<col width='20%'>
						<col width='30%'>
						<col width='20%'>
						<col width='30%'>
						<tr >
						  <td class='input_box_title'> <b>상품그룹명</b></td>
						  <td class='input_box_item' style='padding:5px 10px;' colspan=3>
						  <input type='text' class='textbox point_color' name='group_name[".($i+1)."]' id='group_name_".($i+1)."' size=50 value='".$gdb->dt[group_name]."' validation=true title='상품그룹명'> 상품그룹 이미지 등록을 하지 않은경우 노출됩니다.
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>전시여부</b></td>
						  <td class='input_box_item' style='padding:5px 10px;' >
						  <input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_y' size=50 value='Y' style='border:0px;' ".(($gdb->dt[use_yn] == "Y" || $gdb->dt[use_yn] == "") ? "checked":"")."><label for='use_".($i+1)."_y'> 전시</label>
						  <input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_n' size=50 value='N' style='border:0px;' ".($gdb->dt[use_yn] == "N" ? "checked":"")."><label for='use_".($i+1)."_n'> 전시 하지 않음</label>
						  </td>
						  <td class='input_box_title'  > <b>전시노출 갯수</b></td>
						  <td class='input_box_item' style='padding:5px 10px;' >
						  <input type='text' class='textbox point_color numeric' name='product_cnt[".($i+1)."]' id='product_cnt_".($i+1)."' size=10 value='".$gdb->dt[product_cnt]."' validation=true title='전시노출 갯수'> 개
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>기본 노출여부</b></td>
						  <td class='input_box_item' style='padding:5px 10px;' colspan=3>
						  <div>
						  <input type='radio' class='textbox' name='basic_display_yn[".($i+1)."]' id='basic_display_yn_".($i+1)."_y' size=50 value='Y' style='border:0px;' ".(($gdb->dt[basic_display_yn] == "Y" || $gdb->dt[basic_display_yn] == "") ? "checked":"")."><label for='basic_display_yn_".($i+1)."_y'> 노출</label>
						  <input type='radio' class='textbox' name='basic_display_yn[".($i+1)."]' id='basic_display_yn_".($i+1)."_n' size=50 value='N' style='border:0px;' ".($gdb->dt[basic_display_yn] == "N" ? "checked":"")."><label for='basic_display_yn_".($i+1)."_n'> 노출 하지 않음</label>
						  </div>
						  <div style='float:left;padding:5px 5px;'>예) 탭구성과 같은 전시형태의 경우 첫번째 그룹만 기본 노출을 하고 나머지 구성은 기본노출 하지 않음으로 설정후 스크립트 형태로 처리함.</div>
						  </td>
						</tr>
						<tr style='display:none'>
						  <td class='input_box_title'> <b>상품그룹 이미지</b></td>
						  <td class='input_box_item' style='padding:10px 10px;' colspan=3>
						  <table width=100% cellspacing=3 cellpadding=3  >
							<col width='50%'>
							<col width='50%'>
							<tr height=30>
								<td class=s_td >
									<div >기본이미지</div>
								</td>
								<td class=e_td>
									<div>마우스 오버이미지</div>
								</td>
							</tr>
							<tr >
								<td style='padding:20px;border:1px solid #efefef;'>
									<input type='file' class='textbox' name='group_img[".($i+1)."]' id='group_img' size=50 value=''> <input type='checkbox' name='group_img_del[".($i+1)."]' id='group_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_img_del_".($i+1)."'>그룹이미지 삭제</label><br>
						  <div style='padding:15px 5px 5px 0px;width:90%;overflow:auto' id='group_img_area_".($i+1)."'>";
if(file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/cate_mg_".($i+1).".jpg") && $cmg_ix){//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/cate_mg_".($i+1).".jpg'>";
}

$Contents .= "						</div><br>
								</td>
								<td style='padding:20px;border:1px solid #efefef;'>
<input type='file' class='textbox' name='group_over_img[".($i+1)."]' id='group_over_img' size=50 value=''> <input type='checkbox' name='group_over_img_del[".($i+1)."]' id='group_over_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_over_img_del_".($i+1)."'>그룹이미지 삭제</label><br>
						  <div style='padding:15px 5px 5px 0px;width:90%;overflow:auto' id='group_over_img_area_".($i+1)."'>";
if(file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/cate_mg_over_".($i+1).".jpg") && $cmg_ix){//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/cate_mg_over_".($i+1).".jpg'>";
}

$Contents .= "						</div><br>
								</td>
							</tr>
							</table>";
/*
$Contents .= "
						  <input type='file' class='textbox' name='group_img[".($i+1)."]' id='group_img' size=50 value=''> 
						  <div><input type='checkbox' name='group_img_del[".($i+1)."]' id='group_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_img_del_".($i+1)."'>그룹이미지 삭제</label></div><br>
						  <div style='padding:5px 5px 5px 0px;height:80px;width:600px;overflow:auto' id='group_img_area_".($i+1)."'>";
if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/cate_mg_".$cmg_ix."_".($i+1).".gif") && $cmg_ix){
	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/category_main/cate_mg_".$cmg_ix."_".($i+1).".gif'>";
}
*/
$Contents .= "						</div><br>
						  <div class=small><!--* 이미지 등록을 하지 않을경우 상품그룹명이 노출됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</div>
						  </td>
						</tr>
						<tr style='display:none'>
						  <td class='input_box_title'> <b>상품그룹이미지링크</b></td>
						  <td class='input_box_item' style='padding:5px 10px;' colspan=3>
						  <input type='text' class='textbox' name='group_link[".($i+1)."]' id='group_link_".($i+1)."' size=100 value='".$gdb->dt[group_link]."'>
						  </td>
						</tr>
						<tr height=100 style='display:none'>
						  <td class='search_box_title'>
						  <b>상품그룹 배너 이미지</b>
						   
						  <!--select name='banner_type[".($i+1)."]' id='banner_type_".($i+1)."' onchange=\"changeBannerInfo($(this));\" style='margin-top:5px;'>
								<option value='D' ".(($gdb->dt[banner_type] == "D" || $gdb->dt[banner_type] == "") ? "selected":"").">직접입력</option>
								<option value='S' ".($gdb->dt[banner_type] == "S" ? "selected":"").">입력배너 선택</option>
							</select-->
						  </td>
						  <td class='search_box_item' style='padding:10px' colspan=3>
						  <div id='direct_area_".($i+1)."'>
						  <input type='file' class='textbox' name='group_banner_img[".($i+1)."]' id='group_banner_img' size=50 value=''> <input type='checkbox' name='group_banner_img_del[".($i+1)."]' id='group_banner_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_banner_img_del_".($i+1)."'>그룹 배너이미지 삭제</label><br>
						  <div style='padding:15px 5px 5px 0px;width:90%;overflow:auto' id='group_banner_img_area_".($i+1)."'>";
if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/main_group_banner_".($i+1).".jpg")){//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/main_group_banner_".($i+1).".jpg'>";
}

$Contents .= "		</div>
							<br><span class=small><!--* 이미지 등록을 하지 않을경우 상품그룹명이 노출됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
							</div>
							<div id='select_area_".($i+1)."' style='display:none;'>
							<a href=\"javascript:PoPWindow3('banner.php?mmode=pop',1100,800,'banner_select')\">배너선택</a>
							</div>
						  </td>
						</tr>
						<tr style='display:none'>
						  <td class='search_box_title'><b>전시타입</b></td>
						  <td class='search_box_item promotion_types'   style='padding:10px 10px;' colspan=3><a name='display_type_area_".($i+1)."'></a>
							<div id='display_type_area_".($i+1)."' class=sortable style='width:100%;float:left;height:150px;'>
							".GroupCategoryDisplay($cmg_ix, $cmpg_ix, ($i+1))."
							</div> ";

//$Contents .= SelectFileList2($DOCUMENT_ROOT.$admin_config[mall_data_root]."/module/main_templet/")."

$Contents .= "			
								<div class='add_type_box'>
									<p class='add_type' style='padding-top:15px;'><a href=\"#display_type_area_".($i+1)."\" onclick=\"$('#add_type_choice_".($i+1)."').slideToggle();\"><img src='/admin/images/protype_select.gif' alt='전시타입선택' title='전시타입선택' /></a></p>
									<div class='add_type_choice' id='add_type_choice_".($i+1)."' style='display:none;'>
										".DisplayTemplet($i+1)."										 
									</div>
								</div>
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'>
							<b>전시정보</b>
							<div class='hidden' style='display:none;'>
								<span style='padding-left:2px' class='helpcloud' help_width='300' help_height='30' help_html='자동등록에 경우 사용할 카테고리를 선택하게 되면 상품등록 시 자동으로 신규 상품이 전시됩니다.'><img src='/admin/images/icon_q.gif' align=absmiddle /></span>
								<select name='display_info_type[".($i+1)."]' id='display_info_type_".($i+1)."' onchange=\"changeDisplayInfo($(this));\">
									<option value='P' ".(($gdb->dt[display_info_type] == "P" || $gdb->dt[display_info_type] == "") ? "selected":"").">상품</option>
									<option value='B' ".($gdb->dt[display_info_type] == "B" ? "selected":"").">브랜드</option>
								</select>
							</div>
						  </td>
						  <td class='search_box_item' style='padding:10px 10px;' colspan=3><a name='goods_display_type_".($i+1)."'></a>
						   <div id='goods_manual_choice_".($i+1)."' style='padding-bottom:10px;".($gdb->dt[display_info_type] == "P" || $gdb->dt[display_info_type] == "" ? "":"display:none; ")."' >
							  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').show();$('#goods_auto_area_".($i+1)."').hide();$('#goods_display_sub_type_".($i+1)."').hide();\"><label for='use_".($i+1)."_m'>수동등록</label>
							  
							  <div class='hidden' style='display:none;'>
								  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_a' size=50 value='A' style='border:0px;' ".($gdb->dt[goods_display_type] == "A" ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').hide();$('#goods_auto_area_".($i+1)."').show();$('#goods_display_sub_type_".($i+1)."').show();\"><label for='use_".($i+1)."_a'>자동등록</label>
							  </div>
								  <select name='goods_display_sub_type[".($i+1)."]' id='goods_display_sub_type_".($i+1)."' onchange='ChangeDisplaySubType($(this), ".($i+1)." , this.value);'  ".($gdb->dt[goods_display_type] == "A" ? "style='display:inline;'":"style='display:none;'")." >
									<option value='C' ".(($gdb->dt[goods_display_sub_type] == "C" || $gdb->dt[goods_display_sub_type] == "") ? "selected":"").">상품카테고리</option>
									<option value='B' ".(($gdb->dt[goods_display_sub_type] == "B") ? "selected":"").">브랜드</option>
									<option value='S' ".(($gdb->dt[goods_display_sub_type] == "S") ? "selected":"").">셀러</option>
									<!--option value='P' ".(($gdb->dt[goods_display_sub_type] == "P") ? "selected":"").">개인화</option-->
								  </select>
							  <br>
						  </div>
						  <div id='goods_manual_area_".($i+1)."' style='".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") && ($gdb->dt[display_info_type] == "P" || $gdb->dt[display_info_type] == "") ? "display:block;":"display:none;")."'>
							  
							  <div style='width:100%;padding:5px;' id='group_product_area_".($i+1)."' >".relationCategoryMainProductList($cmg_ix, ($gdb->dt[group_code] ? $gdb->dt[group_code]:1), "clipart")."</div>
							  
							  <div style='width:100%;float:left;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
							  </div>
							  <div style='display:block;float:left;margin-top:10px;'>
							  <a href=\"#goods_display_type_".($i+1)."\" onclick=\"ms_productSearch.show_productSearchBox(event,".($i+1).",'productList_".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a>
							  <input type='text' class='textbox' name='search_goods' id='search_goods' size='20' value='' onkeyup=\"SearchGoods($(this), '".($i+1)."')\"> <img type='image' src='../images/korea/btn_search.gif' style='cursor:pointer;' onclick=\"SearchGoods($(this), '".($i+1)."')\" align='absmiddle'> <img src='../images/".$admininfo["language"]."/btc_del.gif' onclick=\"SearchGoodsDelete($(this))\" border='0'  style='cursor:pointer;vertical-align:middle;'> 
							  </div>
							 
						  </div>
						   <div id='brands_manual_area_".($i+1)."' ".($gdb->dt[display_info_type] == "B" ? "":"style='display:none;' ").">
							  <a href=\"javascript:\" onclick=\"ms_brandSearch.show_productSearchBox(event,".($i+1).",'brandList_".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_brand_search_add.gif' border=0 align=absmiddle></a><br>
							  <div style='width:100%;padding:5px;' id='group_brand_area_".($i+1)."' >".relationCategoryMainBrandList($cmg_ix, ($gdb->dt[group_code] ? $gdb->dt[group_code]:1), "clipart")."</div>
							  <div style='width:100%;float:left;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
							  </div>
						  </div>
						  <div style='padding:0px 0px;".($gdb->dt[goods_display_type] == "A" ? "display:block;":"display:none;")."' id='goods_auto_area_".($i+1)."'>
							<div class='goods_display_type_area'  id='goods_display_sub_area_C' style='".(($gdb->dt[goods_display_sub_type] == "C" || $gdb->dt[goods_display_sub_type] == "") ? "display:block;":"display:none;")."'>
							<table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
								<col width=100%>
								<tr>
									<td style='padding-top:5px;'>";

											$Contents .= PrintCategoryRelation($cmg_ix, ($i+1));

					$Contents .= "	</td>
								</tr>
								<tr><td style='padding-bottom:5px;'>카테고리 선택하기를 클릭해서 자동 노출하고자 하는 카테고리를 지정 하실 수 있습니다.</td></tr>
							</table><br>
							<a href=\"javascript:PoPWindow3('category_select.php?mmode=pop&group_code=".($i+1)."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_select_category.gif' border=0 align=absmiddle></a> 
							</div>
							<div class='goods_display_type_area'  id='goods_display_sub_area_B' style='".(($gdb->dt[goods_display_sub_type] == "B") ? "display:block;":"display:none;")."'>
							<table   border='0'  cellpadding=0 cellspacing=0 >
								<tr>
									<td width='300' height='30'>* 더블클릭으로 추가/삭제가 가능합니다.</td>
								</tr>
								<tr>
									<td width='300'>
										<table  border='0' cellpadding=0 cellspacing=0 align='center'>
										<tr align='left'>
											<td width='100'>
												<input type=text class=textbox name='search_text'  id='search_text' style='width:150px;margin-bottom:2px;' value='' onkeyup=\"SearchInfo('B',$('DIV#goods_auto_area_".($i+1)." DIV#goods_display_sub_area_B'), ".($i+1).");\">  
											</td>
											<td align='center'>
												<img src='../images/btn_select_brand.gif' onclick=\"SearchInfo('B',$('DIV#goods_auto_area_".($i+1)." DIV#goods_display_sub_area_B'), ".($i+1).");\"  style='cursor:pointer;'> 
												<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('DIV#goods_display_sub_area_B #search_result_".($i+1)." option'),'selected')\" style='cursor:pointer;'/>
											</td>
											</tr>
										<tr>
											<td colspan='2' >
												<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='search_result'>
												</div-->
												<select name='search_result[".($i+1)."]' style=' width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' class='search_result' id='search_result_".($i+1)."'  multiple>											
												</select>
											</td>
										</tr>
										</table>
									</td>
									<!--td align='center' width=80>
										<div class='float01 email_btns01'>
											<ul>
												<li>
													<a href=\"javascript:MoveSelectBox('B','ADD',".($i+1).");\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
												</li>
												<li>
													<a href=\"javascript:MoveSelectBox('B','REMOVE',".($i+1).");\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
												</li>
											</ul>
										</div>
									</td-->
									<td>
									<td width='300' style='vertical-align:bottom;'>
										<table width='100%' border='0' align='center'>
										<tr>
											<td colspan='2' >
												<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='selected_result'>
												</div-->
												<select name=\"selected_result[".($i+1)."][brand][]\" style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='selected_result_".($i+1)."' validation=false title='브랜드' multiple>
												";
												//$vip_array = get_vip_member('4');
												$sql = "SELECT b.b_ix, b.brand_name FROM shop_brand b, shop_category_main_brand_relation cmbr 
															where b.b_ix = cmbr.b_ix 
															and cmbr.cmg_ix = '".$cmg_ix."' and cmbr.group_code = '".($i+1)."' 
															and relation_type = 'M' ";
												$slave_db->query($sql);
												$selected_brands = $slave_db->fetchall();
												

												for($j = 0; $j < count($selected_brands); $j++){
													$Contents .="<option value='".$selected_brands[$j][b_ix]."' ondblclick=\"$(this).remove();\" selected>".$selected_brands[$j][brand_name]."</option>";
												}
												$Contents .="
												</select>
											</td>
										</tr>
										</table>
									</td>
								</tr>
								</table>
								
							</div>
							<div class='goods_display_type_area'  id='goods_display_sub_area_S' style='".(($gdb->dt[goods_display_sub_type] == "S") ? "display:block;":"display:none;")."'>
								<table   border='0'  cellpadding=0 cellspacing=0 >	
								<tr>
									<td width='300' height='30'>* 더블클릭으로 추가/삭제가 가능합니다.</td>
								</tr>							
								<tr>
									<td width='300'>
										<table  border='0' cellpadding=0 cellspacing=0 align='center'>
										<tr align='left'>
											<td width='100'>
												<input type=text class=textbox name='search_text'  id='search_text' style='width:150px;margin-bottom:2px;' value='' onkeyup=\"SearchInfo('S',$('DIV#goods_auto_area_".($i+1)." DIV#goods_display_sub_area_S'), ".($i+1).");\"> 
												<!--onclick=ShowModalWindow('./charger_search.php?company_id=3444fde7c7d641abc19d5a26f35a12cc&target=4&amp;code=',600,530,'charger_search')-->
											</td>
											<td align='center'>
												<img src='../images/btn_select_seller.gif' onclick=\"SearchInfo('S',$('DIV#goods_auto_area_".($i+1)." DIV#goods_display_sub_area_S'), ".($i+1).");\"  style='cursor:pointer;'> 
												<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('DIV#goods_display_sub_area_S #search_result_".($i+1)." option'),'selected')\" style='cursor:pointer;'/>
											</td>
											</tr>
										<tr>
											<td colspan='2' >
												<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='search_result'>
												</div-->
												<select name='search_result[".($i+1)."]' style=' width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' class='search_result' id='search_result_".($i+1)."'  multiple>											
												</select>
											</td>
										</tr>
										</table>
									</td>
									<!--td align='center' width=80>
										<div class='float01 email_btns01'>
											<ul>
												<li>
													<a href=\"javascript:MoveSelectBox('S','ADD',".($i+1).");\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
												</li>
												<li>
													<a href=\"javascript:MoveSelectBox('S','REMOVE',".($i+1).");\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
												</li>
											</ul>
										</div>
									</td-->
									<td>
									<td width='300' style='vertical-align:bottom;'>
										<table width='100%' border='0' align='center'>
										<tr>
											<td colspan='2' >
												<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='selected_result'>
												</div-->
												<select name=\"selected_result[".($i+1)."][seller][]\" style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='selected_result_".($i+1)."' validation=false title='브랜드' multiple>
												";
												//$vip_array = get_vip_member('4');
												$sql = "SELECT ccd.company_id, ccd.com_name 
															FROM common_company_detail ccd, shop_category_main_seller_relation cmsr 
															where ccd.company_id = cmsr.company_id and cmsr.cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."'  ";
												$slave_db->query($sql);
												$selected_sellers = $slave_db->fetchall();
												

												for($j = 0; $j < count($selected_sellers); $j++){
													$Contents .="<option value='".$selected_sellers[$j][company_id]."' ondblclick=\"$(this).remove();\" selected>".$selected_sellers[$j][com_name]."</option>";
												}
												$Contents .="
												</select>
											</td>
										</tr>
										</table>
									</td>
								</tr>
								</table>
								<!--a href=\"javascript:ShowModalWindow('../code_search.php?search_type=brand&group_code=".($i+1)."',600,600,'code_search')\" style='cursor:pointer;'><img src='/admin/images/btn_select_seller.gif' alt='셀러선택' title='셀러선택' align='absmiddle' /></a--> 
							</div>
							<div   id='goods_display_sub_area_P' style='display:none;margin-top:10px;padding:5px 10px 5px 10px;border:1px solid silver;line-height:140%;' >
							<b>개인화 서비스</b><br>
							<input type='radio' name='personal_service' id='personal_service_1' value=''><label for='personal_service_1'>방문한 키워드와 동일한 키워드로 방문한 고객들이 많이 본 상품을 노출합니다.</label><br>
							<input type='radio' name='personal_service' id='personal_service_2' value=''><label for='personal_service_2'>방문한 키워드와 동일한 키워드로 방문한 고객들이 많이 구매한 상품을 노출합니다.</label><br>

							<!--a href='#'><img src='/admin/images/btn_personalization.gif' alt='개인화' title='개인화' align='absmiddle' /></a-->
							</div>
							
							
							
							<div style='padding:10px 0px 5px 0px;'>
							선택한 카테고리 내의 상품을
							<select name='display_auto_type[".($i+1)."]'>
								<option value='order_cnt' ".($gdb->dt[display_auto_type] == "order_cnt" ? "selected":"").">구매수순</option>
								<option value='view_cnt' ".($gdb->dt[display_auto_type] == "view_cnt" ? "selected":"").">클릭수순</option>
								<option value='sellprice' ".($gdb->dt[display_auto_type] == "sellprice" ? "selected":"").">최저가순</option>
								<option value='regdate' ".($gdb->dt[display_auto_type] == "regdate" ? "selected":"").">최근등록순</option>
								<option value='wish_cnt' ".($gdb->dt[display_auto_type] == "wish_cnt" ? "selected":"").">찜한순</option>
								<option value='after_score' ".($gdb->dt[display_auto_type] == "after_score" ? "selected":"").">후기순위</option>
							</select>
							으로 노출 하며 <span class='red'>최근 
							
							<select name='display_auto_priod[".($i+1)."]'>
								<option value='1' ".($gdb->dt[display_auto_priod] == "1" ? "selected":"").">1일</option>
								<option value='7' ".($gdb->dt[display_auto_priod] == "7" ? "selected":"").">7일</option>
								<option value='10' ".($gdb->dt[display_auto_priod] == "10" ? "selected":"").">10일</option>
								<option value='15' ".($gdb->dt[display_auto_priod] == "15" ? "selected":"").">15일</option>
								<option value='30' ".($gdb->dt[display_auto_priod] == "30" ? "selected":"").">30일</option>
							</select>

							<!--input type='text' class='textbox' name='display_auto_priod[".($i+1)."]' id='display_auto_priod_".($i+1)."' size=10 value='".$gdb->dt[display_auto_priod]."'-->
							
							일 기준</span>으로 합니다.
							</div>
							</div>
						  </td>
						</tr>";
						/*
$Contents .="
						<tr >
						  <td class='input_box_title'> <b>전시상품</b></td>
						  <td class='input_box_item' style='padding:5px 10px;' colspan=3>
						   <div style='padding-bottom:10px;'>
							  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').show();$('#goods_auto_area_".($i+1)."').hide();\"><label for='use_".($i+1)."_m'>수동등록</label>
							  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_a' size=50 value='A' style='border:0px;' ".($gdb->dt[goods_display_type] == "A" ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').hide();$('#goods_auto_area_".($i+1)."').show();\"><label for='use_".($i+1)."_a'>자동등록</label><br>
						  </div>
						  <div id='goods_manual_area_".($i+1)."' style='".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "display:block;":"display:none;")."'>
							  <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,".($i+1).",'productList_".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
							  <div style='width:100%;padding:5px;' id='group_product_area_".($i+1)."' >".relationCategoryMainProductList($cmg_ix, ($gdb->dt[group_code] ? $gdb->dt[group_code]:1), "clipart")."</div>
							  <div style='width:100%;float:left;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
							  </div> 
						  </div>
						  <div style='padding:0px 0px;".($gdb->dt[goods_display_type] == "A" ? "display:block;":"display:none;")."' id='goods_auto_area_".($i+1)."'>
							<a href=\"javascript:PoPWindow3('category_select.php?mmode=pop&group_code=".($i+1)."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
							<table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
								<col width=100%>
								<tr>
									<td style='padding-top:5px;'>";

											$Contents .= PrintCategoryRelation($cmg_ix, ($i+1));

					$Contents .= "	</td>
								</tr>
								<tr><td style='padding-bottom:5px;'>분류 선택하기를 클릭해서 자동 노출하고자 하는 분류를 지정 하실 수 있습니다.</td></tr>
							</table>
							<div style='padding:5px 0px;'>
							선택한 분류 내의 상품을
							<select name='display_auto_type[".($i+1)."]'>
							<option value='order_cnt' ".($gdb->dt[display_auto_type] == "order_cnt" ? "selected":"").">구매수순</option>
							<option value='view_cnt' ".($gdb->dt[display_auto_type] == "view_cnt" ? "selected":"").">클릭수순</option>
							<option value='sellprice' ".($gdb->dt[display_auto_type] == "sellprice" ? "selected":"").">최저가순</option>
							<option value='regdate' ".($gdb->dt[display_auto_type] == "regdate" ? "selected":"").">최근등록순</option>
							<option value='wish_cnt' ".($gdb->dt[display_auto_type] == "wish_cnt" ? "selected":"").">찜한순</option>
							<option value='after_score' ".($gdb->dt[display_auto_type] == "after_score" ? "selected":"").">후기순위</option>
							</select>
							으로 노출 합니다.
							</div>
							</div>
						  </td>
						</tr>";
*/
$Contents .="
						<tr >
						  <td class='input_box_title'> <b>담당 MD</b></td>
						  <td class='input_box_item'> ".MDSelect($gdb->dt[md_mem_ix], "md_mem_ix[".($i+1)."]", "md_mem_name[".($i+1)."]",($i+1))."";
						// $Contents .= "".makeMDSelectBox($slave_db,'arr_md_id['.($i+1).']',$gdb->dt[md_id],'')."";
						$Contents .= "
						  </td>
						  <td class='input_box_title'> <b>매출목표</b></td>
						  <td class='input_box_item'>
						  <input type='text' class='textbox number' name='group_sales_target[".($i+1)."]' id='group_sales_target_".($i+1)."' size=15 value='".$gdb->dt[group_sales_target]."'> 원
						  </td>
						</tr>
					  </table><br><br>
					  </div>";
	}
 
$Contents .= "
                      </td>
                    </tr>

                    <tr><td colspan=3 align=right style='padding:10px;'>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents .= "<table>
									<tr>
										<td><input type='checkbox' name='delete_cache' id='delete_cache' value='Y'><label for='delete_cache'>캐쉬삭제하기</label></td>
										<td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' align=absmiddle border=0></td>
									</tr>
								</table>";
}
$Contents .= "
					<!--a href='main.list.php'><img src='../image/b_cancel.gif' align=absmiddle  border=0></a--></td></tr>
                  </table>

                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </form>
    </td>
  </tr>";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

//$help_text = HelpBox("이벤트/기획전  관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:0px;'><table><tr><td valign=middle><b>분류 메인전시관리</b></td><td></td></tr></table></div>", $help_text,110)."</div>";//<!--a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_기획전등록(090322)_config.xml',800,517,'manual_view')\"  title='이벤트/기획전 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a-->

$Contents .= "
  <tr>
    <td align='left' style='padding-bottom:100px;'>
    $help_text

    </td>
  </tr>";

$Contents .= "
	</table>
<form name='lyrstat'><input type='hidden' name='opend' value=''></form>";



$Script = "<!--script language='JavaScript' src='../js/scriptaculous.js'></script>\n
<script language='JavaScript' src='../js/dd.js'></script>\n
<script language='JavaScript' src='../js/mozInnerHTML.js'></script>\n-->
<script language='javascript' src='../display/event.write.js'></script>\n
<!--script language='JavaScript' src='../webedit/webedit.js'></script>\n-->
<Script Language='JavaScript'>
//init();
$(document).ready(function() {\n
	my_init('".$group_total."');\n
});\n
</Script>\n
$Script";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	$P->strLeftMenu = display_menu();
	$P->Navigation = "프로모션/전시 > 분류 전시관리 > 분류별 메인 전시상품 등록";
	$P->title = "분류별 메인 전시상품 등록";
	$P->NaviTitle = "분류별 메인 전시상품 등록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = "프로모션/전시 > 분류 전시관리 > 분류별 메인 전시상품 등록";
	$P->title = "분류별 메인 전시상품 등록";
	$P->OnloadFunction = "";//showSubMenuLayer('storeleft');
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}



function DisplayTemplet($group_code){
	global $slave_db ,$admininfo, $agent_type;

	$sql = "select * 
				from shop_display_templetinfo dt 
				where disp = 1 and agent_type = '".$agent_type."'
				order by dt_ix asc 
				 ";

	//echo $sql."<br><br>";
	$slave_db->query($sql);




	if ($slave_db->total == 0){
		$mString .= " ";
	}else{
		$i=0;
		$mString .= "<ul>";
		for($i=0;$i<$slave_db->total;$i++){
			$slave_db->fetch($i);
			//$parent_cname = GetParentCategory($slave_db->dt[cid],$slave_db->dt[depth]);
			$mString .= "	<li  >
									<div onclick=\"CopyDisplayType($(this), 'display_type_area_".($group_code)."', ".($group_code).");\"  style='display:inline-block;text-align:center;width:138px;margin:0px 0 0 0px;'>
										<img src='../images/".$_SESSION["admininfo"]["language"]."/P_type_".$slave_db->dt[dt_ix].".png' align=center ><br>
										<div class='control_view' style='padding-top:3px;display:none;'>
										<input type='hidden' class='textbox' name='display_type[".($group_code)."][type][]' id='display_type_".($group_code)."_0' value='".$slave_db->dt[dt_ix]."'  style='border:0px;' disabled=true><label for='display_type_".($group_code)."_0'>".$slave_db->dt[dt_name]."(".$slave_db->dt[dt_goods_num]."EA)</label>
										<select name='display_type[".($group_code)."][set_cnt][]' class=set_cnt dt_goods_num='".$slave_db->dt[dt_goods_num]."' disabled>";
										for($j=0;$j < 10;$j++){
											$mString .= "<option value='".($j+1)."' ".(($j+1) == 0  ? "selected":"").">".($j+1)."</option>";
										}
										$mString .= "				
										</select>
										</div>
									</div>
									</li>
											";
			}
			$mString .= "</ul>";
		
		
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";


	return $mString;
}

function GroupCategoryDisplay($cmg_ix, $cmpg_ix, $group_code){
	global $slave_db ,$admininfo;

	$sql = "select cmgd.* , dt.dt_ix, dt.dt_name, dt.dt_goods_num
				from shop_category_main_group_display cmgd 
				left join shop_display_templetinfo dt on  cmgd.display_type = dt.dt_ix
				where cmgd.cmpg_ix = '".$cmpg_ix."' 
				order by cmgd.vieworder asc 
				 ";

	//echo nl2br($sql)."<br><br>";
	$slave_db->query($sql);
 

	if ($slave_db->total == 0){
		$mString .= " ";
	}else{
		$i=0;
		
		for($i=0;$i<$slave_db->total;$i++){
			$slave_db->fetch($i);
			//$parent_cname = GetParentCategory($slave_db->dt[cid],$slave_db->dt[depth]);
			$mString .= "<div  ondblclick=\"$(this).remove();DisplayCntCalcurate('$group_code');\"  style='display:inline-block;text-align:center;width:138px;margin:0px 10px 0 0px;'>
									<img src='../images/".$_SESSION["admininfo"]["language"]."/P_type_".$slave_db->dt[dt_ix]."_on.png' align=center ><br>
									<div class='control_view' style='padding-top:3px;display:block;'>
									<input type=hidden name='display_type[".($group_code)."][egd_ix][]' value='".$slave_db->dt[egd_ix]."'>
									<input type='hidden' class='textbox' name='display_type[".($group_code)."][type][]' id='display_type_".($group_code)."_0' value='".$slave_db->dt[dt_ix]."'  style='border:0px;'  ><label for='display_type_".($group_code)."_0'>".$slave_db->dt[dt_name]."(".$slave_db->dt[dt_goods_num]."EA)</label>
									<select name='display_type[".($group_code)."][set_cnt][]' class=set_cnt  dt_goods_num='".$slave_db->dt[dt_goods_num]."' onchange=\"DisplayCntCalcurate('$group_code');\">";
										for($j=0;$j < 10;$j++){
											$mString .= "<option value='".($j+1)."' ".($slave_db->dt[set_cnt] == ($j+1) ? "selected":"").">".($j+1)."</option>";
										}
										$mString .= "		
									</select>
									</div>
								</div>";
		}
		
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";


	return $mString;
}

function PrintCategoryRelation($cmg_ix, $group_code){
	global $slave_db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth, r.cmcr_ix, r.regdate  from shop_category_main_category_relation r, ".TBL_SHOP_CATEGORY_INFO." c where group_code = '".$group_code."' and c.cid = r.cid and cmg_ix = '".$cmg_ix."'";

	//echo $sql."<br><br>";
	$slave_db->query($sql);




	if ($slave_db->total == 0){
		$mString .= "<table width=100% cellpadding=0 cellspacing=0 border=0  id='objCategory_".($group_code)."' >
						<col width=5>
						<col width=*>
						<col width=100>
					  </table>";
	}else{
		$i=0;
		$mString = "<table width=100% cellpadding=0 cellspacing=0 border=0 id='objCategory_".($group_code)."'>
				";
		for($i=0;$i<$slave_db->total;$i++){
			$slave_db->fetch($i);
			$parent_cname = GetParentCategory($slave_db->dt[cid],$slave_db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='category[".$group_code."][]' id='_category' value='".$slave_db->dt[cid]."' style='display:none'></td>
				<!--td class='table_td_white small' width='50'><input type='radio' name='basic[".$group_code."]' value='".$slave_db->dt[cid]."' ".($slave_db->dt[basic] == 1 ? "checked":"")."></td-->
				<td class='table_td_white small ' width='*'>".($parent_cname != "" ? $parent_cname." > ":"").$slave_db->dt[cname]."</td>
				<td class='table_td_white' width='50' align=center><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(".$group_code.",this.parentNode.parentNode)' style='cursor:pointer;' /></td>
				</tr>";
		}
		$mString .= "</table>";
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";


	return $mString;
}


function FileList2 ( $path , $maxdepth = -1 , $mode = "FULL" , $d = 0 ){
global $page_name;
   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }
   $dirlist = array () ;
   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
   if(!is_dir($path)){return false;};
   if ( $handle = opendir ( $path ) )
   {

       while ( false !== ( $file = readdir ( $handle ) ) )
       {
           if ( $file != '.' && $file != '..' )
           {
               $only_file = $file;
               $file = $path . $file ;
               if ( ! is_dir ( $file ) || $mode == "FULL"){
               		if(is_dir ( $file )){
               			//$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               		}else{
               			if($page_name == $only_file){
               				$mstring .=  "<option value='".$only_file ."' selected>".$only_file ."</option>";
               			}else{
               				$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               			}
               		}

               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
                   $mstring .= FileList2 ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;
                  $mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
               }
           }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $mstring ) ;
}

function SelectFileList2($path){
	global $DOCUMENT_ROOT, $mod, $SubID, $mmode;
	if($path == ""){
		$path = $_SERVER["DOCUMENT_ROOT"]."/data/sample/templet/basic";
	}

	$mstring =  "<select name='page_name' onchange=\"document.location.href='design.mod.php?SubID=$SubID&mod=$mod&page_name='+this.value+'&mmode=$mmode'\">";
	if(FileList2($path, 0, "FULL")){
		$mstring .= FileList2($path, 0, "FULL");
	}else{
		$mstring .= "<option>파일이 존재하지않습니다.</option>";
	}
	$mstring .=  "</select>";

	return $mstring;
}


function relationCategoryMainBrandList($cmg_ix, $group_code, $disp_type=""){
	global $start,$page, $orderby, $admin_config, $erpid,$slave_db;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT count(*) as total 
					FROM shop_brand mb 					
					LEFT JOIN shop_category_main_brand_relation br ON mb.b_ix=br.b_ix 
					where mb.b_ix IS NOT NULL and br.cmg_ix='".$cmg_ix."' and group_code = '".$group_code."' and relation_type = 'M'
					 ";


	$slave_db->query($sql);
	$slave_db->fetch();
	$total = $slave_db->dt[0];


	$sql = "SELECT mb.* 
					FROM shop_brand mb 					
					LEFT JOIN shop_category_main_brand_relation br ON mb.b_ix=br.b_ix 
					where mb.b_ix IS NOT NULL  and br.cmg_ix='".$cmg_ix."' and group_code = '".$group_code."' and relation_type = 'M'
					order by vieworder asc
					LIMIT $start, $max ";

	//echo nl2br($sql);
	$slave_db->query($sql);

	if ($slave_db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="brandList_'.$group_code.'" name="brandList" class="brandList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){
			$mString = '<ul id="brandList_'.$group_code.'" name="brandList" class="brandList"></ul>'."\n";
			$mString .= '<script>'."\n";
			$mString .= 'ms_brandSearch.groupCode = '.$group_code.";\n";
			
			for($i=0;$i<$slave_db->total;$i++){
				$slave_db->fetch($i);

				//$imgPath = $admin_config['mall_data_root'].'/images/shopimg/shop_logo_'.$slave_db->dt['company_id'].'.gif';
				$imgPath = $admin_config['mall_data_root'].'/images/brand/'.$slave_db->dt[b_ix].'/brand_'.$slave_db->dt[b_ix].'.gif' ;

				$mString .= 'ms_brandSearch._setBrand("brandList_'.$group_code.'", "M", "'.$slave_db->dt['b_ix'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($slave_db->dt['brand_name']))).'", "'.addslashes(addslashes(trim($slave_db->dt['brand_name']))).'", "'.$slave_db->dt['brand_name'].'");'."\n";
			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;
}


function relationCategoryMainProductList($cmg_ix, $group_code, $disp_type=""){
	global $start,$page, $orderby, $admin_config, $erpid ,$product_image_column_str,$image_hosting_type,$slave_db;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$sql = "SELECT COUNT(*) as total
			FROM ".TBL_SHOP_PRODUCT." p, shop_category_main_product_relation cmpr, shop_category_main_product_group cmpg
			where cmpg.cmg_ix ='".$cmg_ix."'
			and cmpg.cmg_ix = cmpr.cmg_ix
			and p.id = cmpr.pid
			and cmpr.group_code = '".$group_code."'
			and cmpr.group_code = cmpg.group_code
			";//and p.disp = 1

	$slave_db->query($sql);
	$slave_db->fetch();
	$total = $slave_db->dt[total];

	$sql = "SELECT p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.listprice, p.reserve, p.coprice, p.wholesale_price,p.wholesale_sellprice, p.state, p.disp, cmpr_ix, cmpr.vieworder, cmpr.group_code, p.brand_name ".$product_image_column_str."
					FROM ".TBL_SHOP_PRODUCT." p, shop_category_main_product_relation cmpr, shop_category_main_product_group cmpg
					where cmpg.cmg_ix ='".$cmg_ix."'
					and cmpg.cmg_ix = cmpr.cmg_ix
					and p.id = cmpr.pid
					and cmpr.group_code = '".$group_code."'
					and cmpr.group_code = cmpg.group_code					
					order by cmpr.vieworder asc "; //and p.disp = 1 //limit $start,$max
	$slave_db->query($sql);
	$products = $slave_db->fetchall();

	if(count($products)){
			$script_times["product_discount_start"] = time();
			for($i=0 ; $i < count($products) ;$i++){
				$_array_pid[] = $products[$i][id];
				$goods_infos[$products[$i][id]][pid] = $products[$i][id];
				$goods_infos[$products[$i][id]][amount] = $products[$i][pcount];
				$goods_infos[$products[$i][id]][cid] = $products[$i][cid];
				$goods_infos[$products[$i][id]][depth] = $products[$i][depth];
			}
//print_r($goods_infos);
			$discount_info = DiscountRult($goods_infos, $cid, $depth);
			//print_r($discount_info);
			//exit;
			if(is_array($products))
			{
				foreach ($products as $key => $sub_array) {
					$select_ = array("icons_list"=>explode(";",$sub_array[icons]));
					array_insert($sub_array,50,$select_);
					//echo str_pad($sub_array[id], 10, "0", STR_PAD_LEFT)."<br>";
					$discount_item = $discount_info[$sub_array[id]];
					//print_r($discount_item);
					$_dcprice = $sub_array[sellprice];
					if(is_array($discount_item)){						
						foreach($discount_item as $_key => $_item){ 
							if($_item[discount_value_type] == "1"){ // %
								//echo $_item[discount_value]."<br>";
								$_dcprice = roundBetter($_dcprice*(100 - $_item[discount_value])/100, $_item[round_position], $_item[round_type]);//$_dcprice*(100 - $_item[discount_value])/100;						
							}else if($_item[discount_value_type] == "2"){// 원
								$_dcprice = $_dcprice - $_item[discount_value];
							} 
							$discount_desc[] = $_item;//array("discount_type"=>$_item[discount_type], "haddoffice_value"=>$_item[discount_value], "discount_value"=>$_item[discount_value], 
						}						
					}
					$_dcprice = array("dcprice"=>$_dcprice);
					array_insert($sub_array,72,$_dcprice);
					$discount_desc = array("discount_desc"=>$discount_desc);
					array_insert($sub_array,73,$discount_desc);
					$products[$key] = $sub_array;
					if($products[$key][uf_valuation] != "") $products[$key][uf_valuation] = round($products[$key][uf_valuation], 0);
					else $products[$key][uf_valuation] = 0;
				}
				//print_r($products);
				//exit;
			}
			//print_r($products);
	}

	if(count($products) == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script id="productListScript_'.$group_code.'">'."\n";
			$mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
			//pPrice, listprice, reserve,coprice,wholesale_price,wholesale_sellprice, disp, state)
			for($i=0;$i<count($products);$i++){
				//$slave_db->fetch($i);

				//$imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $slave_db->dt['id'], 's',$slave_db->dt);
				$imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $products[$i]['id'], 's',$products[$i]);

				//$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$slave_db->dt['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($slave_db->dt['pname']))).'", "'.addslashes(addslashes(trim($slave_db->dt['brand_name']))).'", "'.$slave_db->dt['sellprice'].'", "'.$slave_db->dt['listprice'].'", "'.$slave_db->dt['reserve'].'", "'.$slave_db->dt['coprice'].'", "'.$slave_db->dt['wholesale_price'].'", "'.$slave_db->dt['wholesale_sellprice'].'", "'.$slave_db->dt['disp'].'", "'.$slave_db->dt['state'].'");'."\n";

				$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$products[$i]['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($products[$i]['pname']))).'", "'.addslashes(addslashes(trim($products[$i]['brand_name']))).'", "'.$products[$i]['sellprice'].'", "'.$products[$i]['listprice'].'", "'.$products[$i]['reserve'].'", "'.$products[$i]['coprice'].'", "'.$products[$i]['wholesale_price'].'", "'.$products[$i]['wholesale_sellprice'].'", "'.$products[$i]['disp'].'", "'.$products[$i]['state'].'", "'.$products[$i]['dcprice'].'", "'.$products[$i]['vieworder'].'", "'.$products[$i]['view_cnt'].'", "'.$products[$i]['regdate'].'");'."\n";
			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;
}

function relationProductList2(){

	global $start,$page, $orderby, $admin_config,$slave_db;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT distinct p.id,p.pname, p.sellprice, p.reserve
					FROM ".TBL_SHOP_PRODUCT." p, shop_category_main_product_relation cmpr
					where p.id = cmpr.pid and p.disp = 1   ";
	$slave_db->query($sql);
	$total = $slave_db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, cmpr_ix
					FROM ".TBL_SHOP_PRODUCT." p, shop_category_main_product_relation cmpr
					where p.id = cmpr.pid and p.disp = 1 order by cmpr.vieworder limit $start,$max";
	$slave_db->query($sql);




	if ($slave_db->total){

		$mString = "<div id='sortlist'>";

		$i=0;
		for($i=0;$i<$slave_db->total;$i++){
			$slave_db->fetch($i);
			$mString .= "<img src='".$admin_config[mall_data_root]."/images/product/c_".$slave_db->dt[id].".gif' id='image_".$slave_db->dt[id]."' title='".cut_str($slave_db->dt[pname],30)."' ondblclick=\"document.frames['act'].location.href='relation.category.act.php?mode=delete&pid=".$pid."&rp_ix=".$slave_db->dt[rp_ix]."'\"width=50 height=50 style='border:1px solid silver' vpace=2 hspace=2>";
		}
	}
	$mString .= "</div>";

	return $mString;

}

function relationProductList($disp_type=""){

	global $start,$page, $orderby, $admin_config, $erpid,$slave_db;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve
					FROM ".TBL_SHOP_PRODUCT." p, shop_category_main_product_relation cmpr
					where p.id = cmpr.pid and p.disp = 1   ";
	$slave_db->query($sql);
	$total = $slave_db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, cmpr_ix, cmpr.vieworder, cmpr.group_code
					FROM ".TBL_SHOP_PRODUCT." p, shop_category_main_product_relation cmpr
					where p.id = cmpr.pid and p.disp = 1 order by cmpr.vieworder asc limit $start,$max";
	$slave_db->query($sql);



	if ($slave_db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'><!--등록된 이벤트/기획전 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>이벤트/기획전 상품으로 등록됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')." </td></tr>";
		$mString .= "</table>";
	}else{
//		$mString = "<ul id='sortlist' >";

		$i=0;
		if($disp_type == "clipart"){

			for($i=0;$i<$slave_db->total;$i++){
				$slave_db->fetch($i);
				$mString .= "<div id='seleted_tb_".$slave_db->dt[id]."' style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'>\n";
				$mString .= "<table id='seleted_tb_".$slave_db->dt[id]."' cellpadding=0 cellspacing=0 border=0 style='display:inline;'>\n";
				$mString .= "<tr>\n";
				$mString .= "<td style='display:none;'></td>\n";
				$mString .= "<td><img src='".$admin_config[mall_data_root]."/images/product/c_".$slave_db->dt[id].".gif' ></td>\n";
				$mString .= "<td style='display:none;'>".$slave_db->dt[pname]."</td>\n";
				$mString .= "<td style='display:none;'><input type='hidden' name='rpid[".$slave_db->dt[group_code]."][]' value='"+spid+"'></td>\n";
				$mString .= "</tr>\n";
				$mString .= "</table>\n";
				$mString .= "</div>\n";
			}
		}else{
	  	$mString .= "<!--li id='image_".$slave_db->dt[id]."' -->
							<table width=100% cellpadding=0 cellspacing=0 id=tb_relation_product class=tb border=0 >
							<col width='60'>
							<col width='*'>
							<col width='60'>";

			for($i=0;$i<$slave_db->total;$i++){
				$slave_db->fetch($i);
				//ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);$('tb_relation_product').deleteRow(this.rowIndex);	\"
				$mString .= "<tr height=27 bgcolor=#ffffff onclick=\"spoit(this)\" ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);\" style='background: url(../images/dot.gif) repeat-x left bottom; '>
							<td class=table_td_white align=center style='padding:5px;'>
								<div style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'><img src='".$admin_config[mall_data_root]."/images/product/c_".$slave_db->dt[id].".gif'></div>
							</td>
							<td class=table_td_white>".cut_str($slave_db->dt[pname],30)."<br>".number_format($slave_db->dt[sellprice])."</td>
							<td><input type='hidden' name='rpid[]' value='".$slave_db->dt[id]."'></td>
							</tr>
							";
				//$mString .= "</li>";
			}
			$mString .= "</table>";

		}
	}

	//$mString = $mString."</ul>";

	return $mString;

}


/*

CREATE TABLE `shop_category_main_brand_relation` (
  `cmbr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `b_ix` int(6) unsigned zerofill NOT NULL default '000000',
  `cmg_ix` int(10) NOT NULL COMMENT '메인전시 분류코드',
  `group_code` int(2) NOT NULL default '1',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`cmbr_ix`)
) TYPE=MyISAM COMMENT='메인상품전시관리_노출브랜드'

CREATE TABLE `shop_category_main_seller_relation` (
  `cmsr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `company_id` varchar(32) NOT NULL ,
  `mg_ix` int(10) NOT NULL COMMENT '메인전시 분류코드',
  `group_code` int(2) NOT NULL default '1',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`cmsr_ix`)
) TYPE=MyISAM COMMENT='메인상품전시관리_노출셀러'


CREATE TABLE `shop_category_main_group_display` (
  `cmgd_ix` int(8) unsigned NOT NULL auto_increment,
  `cmg_ix` int(8) unsigned ,
  `display_type` int(2) unsigned zerofill  default '01',
  `set_cnt` int(2) default '1',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`cmgd_ix`)
) TYPE=MyISAM COMMENT='카테고리 메인 상품전시관리_그룹별 전시타입'


CREATE TABLE `shop_category_main_product_group` (
  `mpg_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `group_name` varchar(100) NOT NULL default '',
  `group_code` int(2) NOT NULL default '0',
  `display_type` int(2) default '1',
  `insert_yn` enum('Y','N') default 'Y',
  `use_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`mpg_ix`)
) TYPE=MyISAM COMMENT='메인상품전시관리_그룹'



CREATE TABLE `shop_category_main_product_relation` (
  `cmpr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `pid` int(6) unsigned zerofill NOT NULL default '000000',
  `group_code` int(2) NOT NULL default '1',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`cmpr_ix`)
) TYPE=MyISAM COMMENT='메인상품전시관리_상품'


CREATE TABLE `shop_category_main_category_relation` (
  `cmcr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `cid` int(6) unsigned zerofill NOT NULL default '000000',
  `group_code` int(2) NOT NULL default '1',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`cmcr_ix`)
) TYPE=MyISAM COMMENT='메인상품전시관리_노출분류'


ALTER TABLE `shop_category_main_product_group`  ADD `basic_display_yn` ENUM('Y','N') NOT NULL DEFAULT 'Y' COMMENT '기본전시여부' AFTER `use_yn`



CREATE TABLE IF NOT EXISTS `shop_category_main_brand_relation` (
  `cmbr_ix` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `cmg_ix` int(6) NOT NULL COMMENT '전시관리인덱스값',
  `pid` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000' COMMENT '상품아이디',
  `group_code` int(2) NOT NULL DEFAULT '1' COMMENT '그룹코드',
  `vieworder` int(5) NOT NULL DEFAULT '0' COMMENT '노출순서',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '수정시구분값',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`cmbr_ix`),
  KEY `group_code` (`group_code`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='카테고리 메인상품 전시관리_브랜드 ';



*/
?>
