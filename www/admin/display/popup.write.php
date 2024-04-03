<?
include("../class/layout.class");

$db = new Database;
$db->query("SELECT * FROM ".TBL_SHOP_POPUP." where popup_ix= '$popup_ix'");
$db->fetch();

if($db->total){
	$popup_ix = $db->dt[popup_ix];
	$popup_position = $db->dt[popup_position];
	$user_appoint = $db->dt[user_appoint];
	
	$popup_title = $db->dt[popup_title];
	$popup_text = $db->dt[popup_text];
	$popup_width = $db->dt[popup_width];
	if($db->dt[popup_today] == 1){
		$popup_height = $db->dt[popup_height] - 30;
	}else{
		$popup_height = $db->dt[popup_height];
	}
	$popup_top = $db->dt[popup_top];
	$popup_left = $db->dt[popup_left];
	$popup_use_sdate = $db->dt[popup_use_sdate];
	$popup_use_edate = $db->dt[popup_use_edate];
	$popup_today = $db->dt[popup_today];
	$disp = $db->dt[disp];
	$popup_type = $db->dt[popup_type];
	$display_position = $db->dt[display_position];
    $display_url = $db->dt[display_url];
	$display_target = $db->dt[display_target];
	$display_sub_target = $db->dt[display_sub_target];
	$popup_display_type = $db->dt[popup_display_type];
	if($popup_position == "A"){
		$is_use_templet = 1;
	}else{
		$is_use_templet = $db->dt[is_use_templet];
	}

	$display_title_type = $db->dt[display_title_type];
	$display_title_img = $db->dt[display_title_img];
	$display_title_text = $db->dt[display_title_text];
	

	$product_cnt = $db->dt[product_cnt];
	$goods_display_type = $db->dt[goods_display_type];
	$display_auto_sub_type = $db->dt[display_auto_sub_type];
	$display_order_type = $db->dt[display_order_type];
	$recent_priod = $db->dt[recent_priod];
	$mobile_type = unserialize($db->dt[mobile_type]);
    $mall_ix = $db->dt['mall_ix'];

	if($mode == "copy"){
		$act = "insert";
		$popup_ix = "";
	}else{
		$act = "update";
	}

//	$sDate = date("Y/m/d", mktime(0, 0, 0, substr($db->dt[popup_use_sdate],4,2)  , substr($db->dt[popup_use_sdate],6,2), substr($db->dt[popup_use_sdate],0,4)));
//	$eDate = date("Y/m/d",mktime(0, 0, 0, substr($db->dt[popup_use_edate],4,2)  , substr($db->dt[popup_use_edate],6,2), substr($db->dt[popup_use_edate],0,4)));

//	$startDate = $popup_use_sdate;
//	$endDate = $popup_use_edate;

}else{
	$act = "insert";
	
	if($popup_position == ""){
		if($agent_type){
			$popup_position = "M";
		}else{
			$popup_position = "F";
		}
	}

    $next10day = mktime(0, 0, 0, date("m")  , date("d")+10, date("Y"));

	$popup_use_sdate = date("Y-m-d 00:00:00");
	$popup_use_edate = date("Y-m-d 23:59:59",$next10day);
	$is_use_templet = 1;
	$disp = "1";

//	$sDate = date("Y/m/d");
//	$sDate = date("Y/m/d");
//	$eDate = date("Y/m/d",$next10day);

}


$Script = "
<link rel='stylesheet' href='../colorpicker/farbtastic.css' type='text/css'>
<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.core.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.mouse.js'></script>
<script type='text/javascript' src='../colorpicker/farbtastic.js'></script>
<script type='text/javascript' src='../js/ms_productSearch.js'></script>
<Script Language='JavaScript'>

var eqIndex = '$clon_no';
$(document).ready(function () {
	var copy_text;
	$('#flash_addbtn').click(function(){	
		
		var newRow = $('#move_banner_table tbody tr:last').clone(true).appendTo('#move_banner_table tfoot');  
		newRow.find('.file_text').text('');
		newRow.find('.bd_link').val('');
		newRow.find('.bd_title').val('');
		newRow.find('.pbd_ix').val('');
		newRow.find('#delete_btn').show();
		
		/*
		eqIndex++;
		copy_text = $('#move_banner_table tbody:first').html();
		$(copy_text).clone().appendTo('#move_banner_table tfoot');
		$('.file_text:eq('+eqIndex+')').text('');
		$('.bd_link:eq('+eqIndex+')').val('');
		$('.bd_title:eq('+eqIndex+')').val('');
		$('.banner_ix:eq('+eqIndex+')').val('');
		*/
	});

	$('#flash_delbtn').click(function(){
		var len = $('#move_banner_table .clone_tr').length;
		if(len > 1){
			eqIndex--;
			$('#move_banner_table .clone_tr:last').remove();
		}else{
			return false;
		}
	});
	
	$('.popup_types li').find('img').click(function(){
		$(this).parent().find('label').trigger('click');
	});
});

function CopyBanner(banner_cnt){
	var banner_total_length = $('#move_banner_table .clone_tr').length;
	for(i=0;i < banner_cnt && banner_cnt > $('#move_banner_table .clone_tr').length ; i++){
		
		var newRow = $('#move_banner_table tbody tr[class=clone_tr]:last').clone(true).appendTo('#move_banner_table');  
		//alert(newRow.html());
		newRow.find('#pbd_ix').attr('name','popup_banner['+(banner_total_length+i)+'][pbd_ix]');
		newRow.find('#banner_b').attr('name','popup_banner['+(banner_total_length+i)+'][b]');
		newRow.find('#banner_s').attr('name','popup_banner['+(banner_total_length+i)+'][s]');
		newRow.find('#banner_link').attr('name','popup_banner['+(banner_total_length+i)+'][link]');
		newRow.find('#banner_title').attr('name','popup_banner['+(banner_total_length+i)+'][title]');
		newRow.find('#bd_file_b_area').html('');
	
		newRow.find('.file_text').text('');
		newRow.find('.bd_link').val('');
		newRow.find('.bd_title').val('');
		newRow.find('.pbd_ix').val('');
		newRow.find('#delete_btn').show();
		var total_cnt = $('#move_banner_table .clone_tr').length;
		newRow.find('#banner_number').html(total_cnt);
	}
	var total_cnt = $('#move_banner_table .clone_tr').length;
	//alert(total_cnt);
	for(i=banner_cnt;i < total_cnt;i++){
		$('#move_banner_table .clone_tr:last').remove();
	}
}

function RemoveBanner(){
	var len = $('#move_banner_table .clone_tr').length;
	if(len > 1){
		eqIndex--;
		$('#move_banner_table .clone_tr:last').remove();
	}else{
		return false;
	}
}



function ChangeDisplaySubType(obj, group_code, selected_value){
	obj.parent().parent().find('div[class^=goods_auto_area]').hide();
	$('DIV#goods_display_sub_area_'+selected_value).show();
}

function ChangeDisplaySubTarget(obj, group_code, selected_value){
	obj.parent().parent().find('div[class^=display_sub_target_area]').hide();
	$('DIV#display_sub_target_area_'+selected_value).show();
}

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
				//alert(datas);
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
		}); 
 
}


function MoveSelectBox(obj, search_type, type,group_code){
	if(type == 'ADD'){
		//alert(obj.attr('id'));
			$('DIV#'+obj.attr('id')+' SELECT#search_result_'+group_code+' option:selected').each(function(){
				//alert($(this).html());
				$('DIV#'+obj.attr('id')+' SELECT#selected_result_'+group_code).append(\"<option value=\"+$(this).val()+\" ondblclick=\\\"MoveSelectBox( $('DIV#\"+obj.attr('id')+\"'), '\"+search_type+\"','ADD','\"+group_code+\"');\\\" selected>\"+$(this).html()+\"</option>\");
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



function CopyDisplayType(jquery_obj, target_id, group_code){
	//alert(jquery_obj.html());
	var newObj = jquery_obj.clone(true).appendTo($('#'+target_id));

	newObj.find('div[class^=control_view]').css('display','');
	newObj.find('input[type^=hidden]').attr('disabled','');
	newObj.find('input[type^=hidden]').attr('disabled',false);
	newObj.find('select[class^=set_cnt]').attr('disabled','');
	newObj.find('select[class^=set_cnt]').attr('disabled',false);
	newObj.css('margin','0 10px 0 0');
	//alert(1);
	newObj.get(0).onclick='';
	//alert(2);
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
	

	$('#product_cnt').val(product_cnt);
}

	
function setcolorpicker(div_id,input_id){
	
	$('#'+div_id).farbtastic('#'+input_id);		//색상표선택
	$('#'+div_id).css('display','');

}

function department_del(dp_ix){
	$('#department_row_'+dp_ix).remove();
}

function person_del(code){
	$('#row_'+code).remove();
}


function SubmitX(frm){

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	//alert(iView.document.body.innerHTML);
	doToggleText(frm);
	//frm.content.value = iView.document.body.innerHTML;
	frm.content.value = document.getElementById('iView').contentWindow.document.body.innerHTML; //kbk
	return true;
}




function init(){
	var frm = document.INPUT_FORM;
	Content_Input();
	Init(frm);
	//onLoad('$sDate','$eDate');
}
</Script>";

if($pid){
	$db->query("select mem_ix, pname from ".TBL_SHOP_CART." where id = '".$pid."' and mem_ix != '' ");
	$mem_ixs = $db->fetchall("object");
	//$aaa = $db->getrows();

	for($i=0;$i < count($mem_ixs);$i++){
		$code[$i] = $mem_ixs[$i][mem_ix];
		if($i == 0){
			$pname = $mem_ixs[$i][pname];
		}
	}
	//print_r($code);
	//$msg = $db->dt[pname].($db->dt[option_text] ? '-'.strip_tags($db->dt[option_text]) : '' ).'이 배송지연되고 있습니다.';
	$text = "'<b>".$pname."</b>' (를)을 장바구니에 담은 고객 목록입니다.";
	$style_class = 'point_color';
	//echo $msg;
}else{
	//$text = "- 찾으실 아이디 또는 이름을 입력하세요.";
}



$Contents = "
<table width='100%' border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("팝업관리", "전시관리 > 팝업관리 ")."</td>
</tr>";
if($pid){
$Contents .= "
 <tr>
	<td align='left' colspan=6 class='".$style_class."' style='height:30px;padding:10px;'> ".$text."</td>
</tr>";
}
$Contents .= "
  <tr>
    <td>
      <div id='TG_INPUT' style='position: relative;'>
        <form name='INPUT_FORM' method='post' onSubmit=\"return SubmitX(this)\" enctype='multipart/form-data'  action='popup.act.php'>
		<input type='hidden' name=act value='$act'>
		<input type='hidden' name=popup_ix value='$popup_ix'>
		<input type='hidden' name=mmode value='$mmode'>
		<input type='hidden' name=popup_position value='$popup_position'>
		
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td bgcolor='#ffffff'>
                    <table cellpadding=3 cellspacing=0 width='100%' class='input_table_box'>
						<col width='10%' />
						<col width='10%' />
						<col width='*' />
						<col width='15%' />
						<col width='30%' />";
						if($_SESSION["admin_config"][front_multiview] == "Y"){
						$Contents .= "
						<tr>
							<td class='input_box_title' colspan='2'> 프론트 전시 구분</td>
							<td class='input_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
						</tr>";
						}
						$Contents .= "
                        <tr>
							<td class='input_box_title' nowrap colspan='2'> <b>팝업명 <img src='".$required3_path."'></b></td>
							<td class='input_box_item' ><input type='text' class='textbox' name='popup_title' value='".$db->dt[popup_title]."' maxlength='50' style='width:90%' validation='true' title='제목'></td>
							<td class='input_box_title' nowrap>팝업 코드(시스템코드)</td>
							<td class='input_box_item' >".($db->dt[popup_ix] ? $db->dt[popup_ix]:"팝업등록시 자동노출됩니다.")."<!--input type='text' class='textbox' name='popup_ix' id='' value='' style='width:70%'  readonly/--></td>
                        </tr>";

if($popup_position != "A"){
	$Contents .= "
						<tr>
							<td class='input_box_title' nowrap colspan='2'>노출위치 설정</td>
							<td colspan='3' class='search_box_item' style='padding:10px;'> 
								<div style='padding-bottom:10px;'>
									<input type='radio' class='textbox' name='display_position' id='display_position_m' size=50 value='M' style='border:0px;' ".(($display_position == "M" || $display_position == "") ? "checked":"")." onclick=\"$('#display_position_area,#display_position_url').hide();\"/><label for='display_position_m'>메인</label>";
						if($agent_type != "M"){
							$Contents .= "
									<input type='radio' class='textbox' name='display_position' id='display_position_c' size=50 value='C' style='border:0px;' ".($display_position == "C"  ? "checked":"")." onclick=\"$('#display_position_area').show();$('#display_position_url').hide();\"/><label for='display_position_c'>카테고리별 </label>
								";
						}

                        $Contents .= "<input type='radio' class='textbox' name='display_position' id='display_position_e' size=50 value='E' style='border:0px;' ".($display_position == "E"  ? "checked":"")." onclick=\"$('#display_position_area').hide();$('#display_position_url').show();\"/><label for='display_position_e'>URL 직접입력 </label>";

							$Contents .= "
									<br>
								</div>
								<div id='display_position_url' style='display:".($display_position == "E" ?  "block": "none" )."' >
								    <input type='text' class='textbox' name='display_url' style='width:500px;' value='".$display_url."' />
								    <br/><br/>
								    <span class=\"small\">
								        * 팝업을 뛰우고 싶은 url을 도메인 제외하고 입력해주시기 바랍니다.
								        <br/>* /event/event_list.php (이벤트 리스트)
								        <br/>* /event/goods_event.php?event_ix=3 (특정 이벤트 페이지)
                                </span>
                            </div>
								<div id='display_position_area' style='display:".($display_position == "C" ? "block":"none")."'>
									<div id='display_position_area_C' >
										<table   border='0'  cellpadding=0 cellspacing=0 >								
											<tr>
												<td width='300'>
													<table  border='0' cellpadding=0 cellspacing=0 align='center'>
														<tr align='left'>
															<td width='100'>   
																<input type=text class=textbox name='search_text'  id='search_text' style='width:260px;margin-bottom:2px;' value='' onkeyup=\"SearchInfo('C',$('DIV#display_position_area_C'), 'category');\">  
															</td>
															<td align='center' >
																<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif'  onclick=\"SearchInfo('C',$('DIV#display_position_area_C'), 'category');\"  style='cursor:pointer;'> 
																<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택'  onclick=\"SelectedAll($('DIV#display_position_area_C #search_result_category option'),'selected')\"  style='cursor:pointer;'/>
															</td>
															</tr>
														<tr>
															<td colspan='2' >
																<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='search_result'>
																</div-->
																<select name='search_result[]' class='search_result' id='search_result_category'  style=' width:370px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' multiple>											
																</select>
															</td>
														</tr>
													</table>
												</td>
												<td align='center' width=80>
													<div class='float01 email_btns01'>
														<ul>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#display_position_area_C'), 'C','ADD','category');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
															</li>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#display_position_area_C'), 'C','REMOVE','category');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
															</li>
														</ul>
													</div>
												</td>
												<td width='300' style='vertical-align:bottom;'>
													<table width='100%' border='0' align='center'>
														<tr>
															<td colspan='2' > 
																<select name='selected_result[category][]' class='selected_result' id='selected_result_category'  style='width:350px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='' validation=false title='카테고리' multiple>
																";
																//$vip_array = get_vip_member('4');
																$sql = "SELECT ci.cid, ci.depth, ci.cname FROM shop_category_info ci, shop_popup_display_relation pdr 
																			where ci.cid = pdr.r_ix and pdr.pdr_div = 'P' and pdr.pdr_sub_div = 'C' and popup_ix = '".$popup_ix."'  ";
																$db->query($sql);
																$selected_categorys = $db->fetchall();
																

																for($j = 0; $j < count($selected_categorys); $j++){
																	$Contents .="<option value='".$selected_categorys[$j][cid]."' ondblclick=\"MoveSelectBox($('DIV#display_position_area'), 'C','REMOVE','category');\" selected>".strip_tags(getCategoryPath($selected_categorys[$j][cid],$selected_categorys[$j][depth]))."</option>";
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
								</div>
							</td>
						</tr>
					   <tr>
						<td class='input_box_title' nowrap colspan='2'>노출회원 설정</td>
						<td colspan='3' class='search_box_item' style='padding:10px;'><a name='display_target'></a>
							<div style='padding-bottom:10px;'>
							    <input type='radio' class='textbox' name='display_target' id='display_target_a' size=50 value='A' style='border:0px;' ".(($display_target == "A" || $display_target == "") ? "checked":"")." onclick=\"$('#display_sub_target_area').hide();$('#display_sub_target').hide();\"/><label for='display_target_a'>전체</label>
									<input type='radio' class='textbox' name='display_target' id='display_target_t' size=50 value='T' style='border:0px;' ".($display_target == "T"  ? "checked":"")." onclick=\"$('#display_sub_target_area').show();$('#display_sub_target').show();\"/><label for='display_target_t'>개별</label>
								<select name='display_sub_target' id='display_sub_target' onchange='ChangeDisplaySubTarget($(this), ".($i+1)." , this.value);'  ".($display_target == "T" ? "style='display:inline;'":"style='display:none;'")." >
								<option value='G' ".(($display_sub_target == "G" || $display_sub_target == "") ? "selected":"").">그룹별</option>
								<option value='M' ".(($display_sub_target == "M") ? "selected":"").">개인별</option>
							  </select>
								<br>
							</div>
						    <div id='display_sub_target_area' ".(($display_target == "A" || $display_target == "") ? "style='display:none' ":"style='display:block'")." >
								<div class='display_sub_target_area'  id='display_sub_target_area_G' ".(($display_sub_target == "G" || $display_sub_target == "") ? "style='display:block' ":"style='display:none'")." >
									<table   border='0'  cellpadding=0 cellspacing=0 >								
											<tr>
												<td width='300'>
													<table  border='0' cellpadding=0 cellspacing=0 align='center'>
														<tr align='left'>
															<td width='100'>   
																<input type=text class=textbox name='search_text'  id='search_text' style='width:260px;margin-bottom:2px;' value='' onkeyup=\"SearchInfo('G',$('DIV#display_sub_target_area_G'), 'group');\">  
															</td>
															<td align='center'>
																<!--img src='../images/btn_select_brand.gif'  onclick=\"SearchInfo('G',$('DIV#display_sub_target_area_G'), 'group');\"  style='cursor:pointer;' /--> 
																<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif'  onclick=\"SearchInfo('G',$('DIV#display_sub_target_area_G'), 'group');\"  style='cursor:pointer;'> 
																<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택'  onclick=\"SelectedAll($('DIV#display_sub_target_area_G #search_result_group option'),'selected')\"  style='cursor:pointer;'/>
															</td>
															</tr>
														<tr>
															<td colspan='2' >
																<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='search_result'>
																</div-->
																<select name='search_result[]' class='search_result' id='search_result_group'  style=' width:370px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' multiple>											
																</select>
															</td>
														</tr>
													</table>
												</td>
												<td align='center' width=80>
													<div class='float01 email_btns01'>
														<ul>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#display_sub_target_area_G'), 'G','ADD','group');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
															</li>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#display_sub_target_area_G'), 'G','REMOVE','group');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
															</li>
														</ul>
													</div>
												</td>
												<td width='300' style='vertical-align:bottom;'>
													<table width='100%' border='0' align='center'>
														<tr>
															<td colspan='2' > 
																<select name='selected_result[group][]' class='selected_result' id='selected_result_group'  style='width:350px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='' validation=false title='회원그룹' multiple>
																";
																//$vip_array = get_vip_member('4');
																$sql = "SELECT gi.gp_ix, gi.gp_name FROM shop_groupinfo gi, shop_popup_display_relation pdr 
																			where gi.gp_ix = pdr.r_ix and pdr.pdr_div = 'T' and pdr.pdr_sub_div = 'G' and popup_ix = '".$popup_ix."'  ";

																$db->query($sql);
																$selected_groups = $db->fetchall();
																

																for($j = 0; $j < count($selected_groups); $j++){
																	$Contents .="<option value='".$selected_groups[$j][gp_ix]."' ondblclick=\"$(this).remove();\" selected>".$selected_groups[$j][gp_name]."</option>";
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
								<div class='display_sub_target_area'  id='display_sub_target_area_M'  ".(($display_sub_target == "M") ? "style='display:block' ":"style='display:none'")." >
									<table   border='0'  cellpadding=0 cellspacing=0 >								
											<tr>
												<td width='300'>
													<table  border='0' cellpadding=0 cellspacing=0 align='center'>
														<tr align='left'>
															<td width='100'>   
																<input type=text class=textbox name='search_text'  id='search_text' style='width:260px;margin-bottom:2px;' value='' onkeyup=\"SearchInfo('M',$('DIV#display_sub_target_area_M'), 'member');\">  
															</td>
															<td align='center'>
																<!--img src='../images/btn_select_brand.gif'  onclick=\"SearchInfo('M',$('DIV#display_sub_target_area_M'), 'member');\"  style='cursor:pointer;' /-->
																<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif'  onclick=\"SearchInfo('M',$('DIV#display_sub_target_area_M'), 'member');\"  style='cursor:pointer;'> 
																<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택'  onclick=\"SelectedAll($('DIV#display_sub_target_area_M #search_result_member option'),'selected')\"  style='cursor:pointer;'/>
															</td>
															</tr>
														<tr>
															<td colspan='2' >
																<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='search_result'>
																</div-->
																<select name='search_result[]' class='search_result' id='search_result_member'  style=' width:370px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' multiple>											
																</select>
															</td>
														</tr>
													</table>
												</td>
												<td align='center' width=80>
													<div class='float01 email_btns01'>
														<ul>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#display_sub_target_area_M'), 'M','ADD','member');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
															</li>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#display_sub_target_area_M'), 'M','REMOVE','member');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
															</li>
														</ul>
													</div>
												</td>
												<td width='300' style='vertical-align:bottom;'>
													<table width='100%' border='0' align='center'>
														<tr>
															<td colspan='2' > 
																<select name='selected_result[member][]' class='selected_result' id='selected_result_member'  style='width:350px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='' validation=false title='회원' multiple>
																";
																//$vip_array = get_vip_member('4');
																$sql = "SELECT cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name FROM common_member_detail cmd, shop_popup_display_relation pdr 
																			where cmd.code = pdr.r_ix and pdr.pdr_div = 'T' and pdr.pdr_sub_div = 'M' and popup_ix = '".$popup_ix."'  ";
																$db->query($sql);
																$selected_members = $db->fetchall();
																

																for($j = 0; $j < count($selected_members); $j++){
																	$Contents .="<option value='".$selected_members[$j][code]."' ondblclick=\"$(this).remove();\" selected>".$selected_members[$j][name]."</option>";
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
							</div>
						  </td>
					  </tr>";
}

	$Contents .= "
                      <tr>
						  <td class='input_box_title' nowrap colspan='2'> <b>사용기간 <img src='".$required3_path."'></b></td>
						  <td class='input_box_item' colspan='3'>
							".search_date('popup_use_sdate','popup_use_edate',$popup_use_sdate,$popup_use_edate,'Y',' ',' validation="true" title="사용기간" ')."
						</td> 
					</tr>
					";
		if($agent_type != "M"){
			$Contents .= "
                    <tr>
                      	<td class='input_box_title' nowrap colspan='2'> <b>사이즈 <img src='".$required3_path."'></b></td>
                        <td class='input_box_item' colspan='3'>
                        <input type='hidden' name='pop' value='1'>
                        	<table cellpadding=0>
                        	<tr>
                        		<td width=90>가로(넓이) : </td>
								<td width=80><input type='text' class='textbox'  name='popup_width' value='".$popup_width."' size=10 validation='true' title='가로(넓이)'></td>
								<td width=20> px </td>
                        		<td width=100 style='padding-left:20px'>세로(높이) : </td>
								<td width=80><input type='text' class='textbox' name='popup_height' value='".$popup_height."' size=10 validation='true' title='세로(넓이)'></td>
								<td width=20>  px </td>
                        	</tr>
                        	</table>
						</td>
					</tr>
					<tr>
                      	<td class='input_box_title' nowrap colspan='2'> <b>팝업창 위치 <img src='".$required3_path."'></b></td>
                        <td class='input_box_item' colspan='3'>
                        <input type='hidden' name='pop' value='1'>
                        	<table cellpadding=0>
                        	<tr>
                        		<td width=90>상단부터(top) : </td>
								<td width=80><input type='text' class='textbox' name='popup_top' value='".$popup_top."' size=10 validation='true' title='상단'> </td>
								<td width=20>px </td>
                        		<td width=100 style='padding-left:20px'>좌측부터(left) : </td>
								<td width=80><input type='text' class='textbox' name='popup_left' value='".$popup_left."' size=10 validation='true' title='좌측'></td>
								<td width=20>  px </td>
                        	</tr>
                        	</table>
						</td>
                    </tr>
			";
		}
		$Contents .= "
					<tr>
						<td class='input_box_title' nowrap colspan='2'> <b>노출 유무 <img src='".$required3_path."'></b></td>
						<td class='input_box_item'>
						   <input type='hidden' name='pop' value='1'> <input type='radio' name='disp' id='disp_1' value='1' ".CompareReturnValue("1",$disp,"checked")."> <label for='disp_1' >표시</label> <input type='radio' name='disp' id='disp_0' value='0' ".CompareReturnValue("0",$disp,"checked")."><label for='disp_0' >표시하지 않음</label>
						</td>
						<td class='input_box_title' nowrap> <b>오늘하루보기 사용 <img src='".$required3_path."'></b></td>
						<td class='input_box_item'>
						<input type='radio' name='popup_today' id='today_1' value='1' ".(($popup_today == "1" || $popup_today == "") ? "checked":"")." validation='true' title='오늘하루보기'> <label for='today_1' >사용</label> <input type='radio' name='popup_today' id='today_0' value='0' ".CompareReturnValue("0",$popup_today,"checked")." validation='true' title='오늘하루보기'><label for='today_0' >미사용</label>
						</td>
					</tr>";
if($popup_position == "A"){
	$Contents .= "
					<tr>
						<td class='input_box_title' nowrap colspan='2'> <b>팝업Type 선택 <img src='".$required3_path."'></b></td>
                        <td class='input_box_item' colspan=3>
                        <input type='hidden' name='pop' value='1'> 
							<input type='radio' name='popup_type' id='popup_type_1' value='L' ".(($popup_type == "L" || $popup_type == "") ? "checked":"")."> <label for='popup_type_1' validation='true' title='팝업Type선택'>레이어</label>
							<input type='radio' name='popup_type' id='popup_type_0' value='W' ".CompareReturnValue("W",$popup_type,"checked")." validation='true' title='팝업Type선택'><label for='popup_type_0' >윈도우창</label> 
							
							<input type='hidden' name='is_use_templet' id='user_appoint' value='1'  >
							</td> 
						 
					</tr>";
}else{
	$Contents .= "
					<tr>
						<td class='input_box_title' nowrap colspan='2'> <b>팝업Type 선택 <img src='".$required3_path."'></b></td>
                        <td class='input_box_item' colspan='3'>
                        <input type='hidden' name='pop' value='1'> 
							<input type='radio' name='popup_type' id='popup_type_1' value='L' ".(($popup_type == "L" || $popup_type == "") ? "checked":"")."> <label for='popup_type_1' validation='true' title='팝업Type선택'>레이어</label> 
							";
	if($agent_type != "M"){
		$Contents .= "
							<input type='radio' name='popup_type' id='popup_type_0' value='W' ".CompareReturnValue("W",$popup_type,"checked")." validation='true' title='팝업Type선택'><label for='popup_type_0' >윈도우창</label> 
							</td> 
					";
			}


	$Contents .= "
                            <input type='hidden' name='is_use_templet' id='user_appoint' value='1' >";
                         if(false) {
                             $Contents .= "
                                <td class='input_box_title' nowrap> <b>팝업 템플릿 사용 여부<img src='" . $required3_path . "'></b></td>
                                <td class='input_box_item'>
							    <input type='radio' name='is_use_templet' id='user_appoint' value='1'  validation='true' title='팝업 템플릿 사용' " . (($is_use_templet == 1 || $is_use_templet == '') ? "checked" : "") . " onclick=\"$('.pop_templet').hide();$('#dhtml_editor_area').show();\"> <label for='user_appoint' >사용자 지정</label> 
							";
                             if ($agent_type != "M") {
                                 $Contents .= "
							        <div style='display:none'><input type='radio' name='is_use_templet' id='templet_appoint' value='0' validation='true' title='팝업 템플릿 사용' " . (($is_use_templet == '0') ? "checked" : "") . " onclick=\"$('.pop_templet').show();$('#dhtml_editor_area').hide();\"><label for='templet_appoint' style='color:red;'>템플릿 사용</label></div>
						            ";
                             }
                             $Contents .= "
						        </td>";
                         }
    $Contents .= "
					</tr>";
}
if($agent_type == "M"){
				$Contents .= "
					<tr>
						<td class='input_box_title' nowrap colspan='2'> <b>노출구분 </b></td>
                        <td class='input_box_item' colspan=3>
							<input type='radio' name='mobile_type[]' id='mobile_type_1' value='A' ".CompareReturnValue("A",$mobile_type,"checked")."> <label for='mobile_type_1'>APP_전체팝업</label>
							<input type='radio' name='mobile_type[]' id='mobile_type_2' value='M' ".CompareReturnValue("M",$mobile_type,"checked")."> <label for='mobile_type_2' >APP_모달팝업</label> 
							<input type='radio' name='mobile_type[]' id='mobile_type_0' value='W' ".CompareReturnValue("W",$mobile_type,"checked")."> <label for='mobile_type_0' >Web_m</label> 
							<!--span style='color:blue; font-size:11px; margin-left:20px;'>(모두 선택하지 않을 경우 두 곳 모두 노출 됨)</span-->
						</td> 
						 
					</tr>";
}
$Contents .= "
					<!--/table>
					<table cellpadding=3 cellspacing=0 width='100%' class='input_table_box' >
						<col width='10%' />
						<col width='10%' />
						<col width='*' />
						<col width='15%' />
						<col width='30%' /-->
					<tr class='pop_templet' ".(($is_use_templet == '1') ? "style='display:none'":"").">
						<td class='input_box_title' nowrap rowspan='2'>	<b>전시 타입 <img src='".$required3_path."'></b></td>
						<td class='input_box_title' nowrap style='width:100px;'>고정형</td>
						<td colspan='3' class='popup_types'>
							<div>
								<ul>
									<li>
										<img src='../images/popup/pop_type01.png' align='absmiddle' />
										<input type='radio' name='popup_display_type' id='pop_type01' onclick='CopyBanner(1)' value=1 ".(($popup_display_type == 1 || $popup_display_type == "") ? "checked":"")." /> <label for='pop_type01'>type-1</label>
									</li>
									<li>
										<img src='../images/popup/pop_type02.png' align='absmiddle' />
										<input type='radio' name='popup_display_type' id='pop_type02' onclick='CopyBanner(2)' value=2 ".($popup_display_type == 2 ? "checked":"")." /> <label for='pop_type02'>type-2</label>
									</li>
									<li>
										<img src='../images/popup/pop_type03.png' align='absmiddle' />
										<input type='radio' name='popup_display_type' id='pop_type03' onclick='CopyBanner(3)' value=3 ".($popup_display_type == 3 ? "checked":"")."  /> <label for='pop_type03'>type-3</label>
									</li>
									<li>
										<img src='../images/popup/pop_type04.png' align='absmiddle' />
										<input type='radio' name='popup_display_type' id='pop_type04' onclick='CopyBanner(2)' value=4 ".($popup_display_type == 4 ? "checked":"")."  /> <label for='pop_type04'>type-4</label>
									</li>
									<li>
										<img src='../images/popup/pop_type05.png' align='absmiddle' />
										<input type='radio' name='popup_display_type' id='pop_type05' onclick='CopyBanner(4)' value=5 ".($popup_display_type == 5 ? "checked":"")."  /> <label for='pop_type05'>type-5</label>
									</li>
									<li>
										<img src='../images/popup/pop_type06.png' align='absmiddle' />
										<input type='radio' name='popup_display_type' id='pop_type06' onclick='CopyBanner(3)' value=6 ".($popup_display_type == 6 ? "checked":"")." /> <label for='pop_type06'>type-6</label>
									</li>
								</ul>
							</div>
						</td>
					</tr>
					<tr class='pop_templet' ".(($is_use_templet == '1') ? "style='display:none'":"").">
						<td class='input_box_title' nowrap style='width:100px;'>슬라이드 형</td>
						<td colspan='3' class='popup_types'>
							<div>
								<ul>
									<li>
										<img src='../images/popup/pop_type07.png' align='absmiddle' />
										<input type='radio' name='popup_display_type' id='pop_type07' onclick='CopyBanner(3)' value=7 ".($popup_display_type == 7 ? "checked":"")."  /> <label for='pop_type07'>type-7</label>
									</li>
									<li>
										<img src='../images/popup/pop_type08.png' align='absmiddle' />
										<input type='radio' name='popup_display_type' id='pop_type08' onclick='CopyBanner(2)' value=8 ".($popup_display_type == 8 ? "checked":"")." /> <label for='pop_type08'>type-8</label>
									</li>
									<li>
										<img src='../images/popup/pop_type09.png' align='absmiddle' />
										<input type='radio' name='popup_display_type' id='pop_type09' onclick='CopyBanner(3)' value=9 ".($popup_display_type == 9 ? "checked":"")."  /> <label for='pop_type09'>type-9</label>
									</li>
									<li>
										<img src='../images/popup/pop_type10.png' align='absmiddle'  />
										<input type='radio' name='popup_display_type' id='pop_type10' onclick='CopyBanner(3)' value=10 ".($popup_display_type == 10 ? "checked":"")."  /> <label for='pop_type10'>type-10</label>
									</li>
									<li>
										<img src='../images/popup/pop_type11.png' align='absmiddle' />
										<input type='radio' name='popup_display_type' id='pop_type11' onclick='CopyBanner(4)' value=11 ".($popup_display_type == 11 ? "checked":"")."  /> <label for='pop_type11'>type-11</label>
									</li>
									<li>
										<label for='pop_type12'><img src='../images/popup/pop_type12.png' align='absmiddle'  /></label>
										<input type='radio' name='popup_display_type' id='pop_type12' onclick='CopyBanner(4)' value=12 ".($popup_display_type == 12 ? "checked":"")."  /> <label for='pop_type12'>type-12</label>
									</li>
									<li>
										<img src='../images/popup/pop_type13.png' align='absmiddle'  />
										<input type='radio' name='popup_display_type' id='pop_type13' onclick='CopyBanner(6)' value=13 ".($popup_display_type == 13 ? "checked":"")."  /> <label for='pop_type13'>type-13</label>
									</li>
									<li>
										<img src='../images/popup/pop_type14.png' align='absmiddle' />
										<input type='radio' name='popup_display_type' id='pop_type14' onclick='CopyBanner(6)'  value=14 ".($popup_display_type == 14 ? "checked":"")."  /> <label for='pop_type14'>type-14</label>
									</li>
								</ul>
							</div>
						</td>
					  </tr>
					  <tr class='pop_templet' ".(($is_use_templet == '1') ? "style='display:none'":"").">
						<td class='input_box_title' nowrap colspan='2'>팝업 설정 </td>
						<td colspan='3' class='show_popup_type' style='padding:10px;'>
							
			
							<table cellpadding=0 cellspacing=0 border='0' align='left' id='move_banner_table' width='100%'>
								<col width=4%>
								<col width=38%>
								<col width=10%>
								<col width=38%>
								<col width=10%>
							";
						//$mfdArr = array();
						$db->query("SELECT * FROM shop_popup_bannerinfo_detail  where popup_ix = '".$popup_ix."' order by pbd_ix ASC ");//order by 가 regdate 로 되어 있던 것을 고침 kbk 13/02/15
						if($db->total){
							$banner_details = $db->fetchall();
						}
					$clon_no = 0;
					if(is_array($banner_details) || true){
						//foreach($banner_details as $_key=>$_value){
						for($j=0 ; ($j < count($banner_details) || $j == 0); $j++){
	
						 
							$Contents .= "
									  <tr bgcolor=#ffffff  class='clone_tr'>
										<td colspan=4 style='font-size:16px;width:50px;font-weight:bold;' align=center >
											<table cellpadding=0 cellspacing=0 width=100%>
											 <tr bgcolor=#ffffff  >
												<td id='banner_number' style='font-size:16px;width:50px;font-weight:bold;' align=center rowspan=2>".($clon_no+1)."</td>
												<td height='25' style='padding:10px 0; solid #d3d3d3;' >
													<input type=hidden name='popup_banner[".$clon_no."][pbd_ix]' id='pbd_ix' class='pbd_ix' value='".$banner_details[$j][pbd_ix]."' style='width:230px;' validation=false>
													첨부파일(대) : 
													<input type=file class='textbox' name='popup_banner[".$clon_no."][b]' id='banner_b' style='width:50%;'  title='파일' value=''> 
													<span class='file_text helpcloud'  help_width='200' help_height='30' help_html=\"선택 해제후 저장하시면 해당이미지가 삭제되게 됩니다.\">
														<b>".$banner_details[$j][popup_banner_big]."</b>
														<input type='checkbox' name='nondelete[".$banner_details[$j][pbd_ix]."]' id='non_delete_".$banner_details[$j][pbd_ix]."' value='1' checked />
														<label for='non_delete_".$banner_details[$j][pbd_ix]."'> 파일유지</label>
													</span>
												</td>";
												
							$Contents .= "<td style='padding:5px;text-align:center;' id='bd_file_b_area' rowspan=2>";
												if($banner_details[$j][bd_file_b] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/".$banner_details[$j][popup_ix]."/".$banner_details[$j][bd_file_b])){
												//	exit;
													$image_info = getimagesize ($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/popup/".$banner_details[$j][popup_ix]."/".$banner_details[$j][bd_file_b]);
													$Contents .= "<img src='".$admin_config[mall_data_root]."/images/popup/".$banner_details[$j][popup_ix]."/".$banner_details[$j][bd_file_b]."' style='vertical-align:middle;margin:3px;'  ".($_COOKIE[banner_image_view] == 1 ? "":"height=50")." class='helpcloud' help_width='".($image_info[0]+20)."' help_height='".($image_info[1])."' help_html=\"<img src='".$admin_config[mall_data_root]."/images/popup/".$banner_details[$j][popup_ix]."/".$banner_details[$j][bd_file_b]."' >\" style='cursor:pointer;'>";
												}
												$Contents .= "</td>";
												
										$Contents .= "
												<td>
													첨부파일(소) : 
													<input type=file class='textbox' name='popup_banner[".$clon_no."][s]' id='banner_s'  style='width:50%;' title='파일'> 
													<span class='file_text helpcloud'  help_width='200' help_height='30' help_html=\"선택 해제후 저장하시면 해당이미지가 삭제되게 됩니다.\">
														<b>".$banner_details[$j][popup_banner_big]."</b>
														<input type='checkbox' name='nondelete[".$banner_details[$j][pbd_ix]."]' id='non_delete_".$banner_details[$j][pbd_ix]."' value='1' checked />
														<label for='non_delete_".$banner_details[$j][pbd_ix]."'> 파일유지</label>
													</span>
													
												</td>
												";

												$Contents .= "<td style='padding:5px;text-align:center;' id='bd_file_s_area' rowspan=2>";
												if($banner_details[$j][bd_file_s] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/".$banner_details[$j][popup_ix]."/".$banner_details[$j][bd_file_s])){
												//	exit;
													$image_info = getimagesize ($DOCUMENT_ROOT.$admin_config[mall_data_root]."/images/popup/".$banner_details[$j][popup_ix]."/".$banner_details[$j][bd_file_s]);
													$Contents .= "<img src='".$admin_config[mall_data_root]."/images/popup/".$banner_details[$j][popup_ix]."/".$banner_details[$j][bd_file_s]."' style='vertical-align:middle;margin:3px;'  ".($_COOKIE[banner_image_view] == 1 ? "":"height=50")." class='helpcloud' help_width='".($image_info[0]+20)."' help_height='".($image_info[1])."' help_html=\"<img src='".$admin_config[mall_data_root]."/images/popup/".$banner_details[$j][popup_ix]."/".$banner_details[$j][bd_file_s]."' >\" style='cursor:pointer;'>";
												}
												$Contents .= "</td>";
										

									$Contents .= "
												
												<td style='vertical-align:middle;padding:10px;'>&nbsp;</td>
											  </tr>
											  <tr>
												<td>
												링&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;크 : 
													<input type=text class='textbox bd_link' name='popup_banner[".$clon_no."][link]' id='banner_link'  class='bd_link' value='".$banner_details[$j][bd_link]."' style='width:80%;'  title='링크'>
												</td>
												<td>
												타 이 틀 : 
													<input type=text class='textbox bd_title' name='popup_banner[".$clon_no."][title]' id='banner_title'  value='".$banner_details[$j][bd_title]."' id='bd_title' class='bd_title' style='width:80%;' title='타이틀' />
												</td>
											  </tr>
											</table>
										</td>
									  </tr>
									  ";
							 
							$clon_no++;
						}
					}  
					$Contents .= " 
							</table>
						</td>
					  </tr>";
if(false){
$Contents .= " 
					  <tr >	
						<td class='input_box_title' nowrap colspan='2'>전시 타입명 </td>
						<td colspan='3' class='search_box_item' >
							<div class='type_name'>
								<input type='radio' name='display_title_type' id='display_title_type_txt' value='T' ".(($display_title_type == "T" || $display_title_type == "") ? "checked":"")." /> 
								<label for='display_title_type_txt'>텍스트</label> <input type='text' name='display_title_text' id='display_title_text' value='".$display_title_text."' />
								<input type='radio' name='display_title_type' id='display_title_type_img' value='I' ".($display_title_type == "I" ? "checked":"")." /> 
								<label for='display_title_type_img'>이미지</label> <input type='file' name='display_title_img' id='' style='border:1px solid #ccc;' validation=false title='전시타입명 이미지'/>
								
								
							</div>
						</td>
					  </tr>
					  <tr>
						<td class='input_box_title' nowrap colspan='2'>전시 타입</td>
						<td colspan='3' class='popup_goods' style='padding:10px 10px;'>
							<div id='display_type_area_1' class=sortable style='width:100%;float:left;'>
								 ".GroupCategoryDisplay($popup_ix)."
							</div>
							<div class='add_type_box'>
								<p class='add_type' style='padding:15px 0 0 10px;'><a href=\"#display_type_area_2\" onclick=\"$('#add_type_choice_2').slideToggle();\">
									<img src='/admin/images/protype_select.gif' alt='전시타입선택' title='전시타입선택' /></a>
								</p>
								<div class='add_type_choice' id='add_type_choice_2' style='display:none;'>
									".DisplayTemplet($i+1)."										 
								</div>
							</div>
						</td>
					  </tr>
					  <tr>
						<td class='input_box_title' nowrap colspan='2'>전시 갯수</td>
						<td class='search_box_item' colspan=3>
							<input type='text' class='textbox numeric' name='product_cnt' id='product_cnt' size=10 value='".$product_cnt."'> 전시타입을 선택하시면 상품 노출갯수가 자동으로 선택됩니다. 
						</td>
					  </tr>
					  <tr>
						<td class='input_box_title' nowrap colspan='2'>전시상품</td>
						<td colspan='3' class='search_box_item' style='padding:10px;'><a name='goods_display_type_1'></a>
							<div style='padding-bottom:10px;'>
							  <input type='radio' class='textbox' name='goods_display_type' id='use_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($goods_display_type == "M" || $goods_display_type == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').show();$('#goods_auto_area').hide();$('#display_auto_sub_type_".($i+1)."').hide();\"><label for='use_".($i+1)."_m'>수동등록</label>
							  <input type='radio' class='textbox' name='goods_display_type' id='use_".($i+1)."_a' size=50 value='A' style='border:0px;' ".($goods_display_type == "A" ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').hide();$('#goods_auto_area').show();$('#display_auto_sub_type_".($i+1)."').show();\"><label for='use_".($i+1)."_a'>자동등록</label>
							  <select name='display_auto_sub_type' id='display_auto_sub_type_".($i+1)."' onchange='ChangeDisplaySubType($(this), ".($i+1)." , this.value);'  ".($goods_display_type == "A" ? "style='display:inline;'":"style='display:none;'")." >
								<option value='C' ".(($display_auto_sub_type == "C" || $display_auto_sub_type == "") ? "selected":"").">상품카테고리</option>
								<option value='B' ".(($display_auto_sub_type == "B") ? "selected":"").">브랜드</option>
								<option value='S' ".(($display_auto_sub_type == "S") ? "selected":"").">셀러</option>
								<!--option value='P' ".(($display_auto_sub_type == "P") ? "selected":"").">개인화</option-->
							  </select>
							   
							  <br>
							</div>
							<div id='goods_manual_area_".($i+1)."' style='".(($goods_display_type == "M" || $goods_display_type == "") ? "display:block;":"display:none;")."'>
								<div style='width:100%;padding:5px;' id='group_product_area_".($i+1)."' >
									".relationPopupProductList($popup_ix,($gdb->dt[group_code] ? $gdb->dt[group_code]:($i+1)), "clipart")."</div>
								<div style='width:100%;float:left;'>
									<span class=small>상품 이미지를 드레그앤드롭으로 노출 순서를 좌.우로 조정 할 수 있습니다. <br />더블클릭으로 상품별 개별 삭제가 가능합니다.".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
								</div>
								<div style='display:block;float:left;margin-top:10px;'>
									 <a href=\"#goods_display_type_".($i+1)."\" onclick=\"ms_productSearch.show_productSearchBox(event,".($i+1).",'productList_".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a>
								</div>
					        </div>
						    <div style='padding:0px 0px;".($goods_display_type == "A" ? "display:block;":"display:none;")."' id='goods_auto_area'>
								<div class='goods_auto_area'  id='goods_display_sub_area_C' style='".(($display_auto_sub_type == "C" || $display_auto_sub_type == "") ? "display:block;":"display:none;")."'>
									<table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
										<col width=100%>
										<tr>
											<td style='padding-top:5px;'>";

											$Contents .= PrintCategoryRelation(($i+1),$popup_ix);

					$Contents .= "	</td>
										</tr>
										<tr>
											<td style='padding-bottom:5px;'>카테고리 선택하기를 클릭해서 자동 노출하고자 하는 카테고리를 지정 하실 수 있습니다.</td>
										</tr>
									</table><br>
									<a href=\"javascript:PoPWindow3('category_select.php?mmode=pop&group_code=".($i+1)."',660,300,'category_select')\"'>
										<img src='../images/".$admininfo["language"]."/btn_select_category.gif' border=0 align=absmiddle>
									</a> 
								</div>
								<div class='goods_auto_area'  id='goods_display_sub_area_B' style='".(($display_auto_sub_type == "B") ? "display:block;":"display:none;")."'>
									<table   border='0'  cellpadding=0 cellspacing=0 >								
										<tr>
											<td width='300'>
												<table  border='0' cellpadding=0 cellspacing=0 align='center'>
													<tr align='left'>
														<td width='100'>
															<input type=text class=textbox name='search_text'  id='search_text' style='width:150px;margin-bottom:2px;' value='' onkeyup=\"SearchInfo('B',$('DIV#goods_display_sub_area_B'), 'brand');\">  
														</td>
														<td align='center'>
															<img src='../images/btn_select_brand.gif' onclick=\"SearchInfo('B',$('DIV#goods_display_sub_area_B'), 'brand');\"  style='cursor:pointer;'> 
															<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('DIV#goods_display_sub_area_B #search_result_brand option'),'selected')\" style='cursor:pointer;'/>
														</td>
														</tr>
													<tr>
														<td colspan='2' >
															<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='search_result'>
															</div-->
															<select name='search_result[brand]' style=' width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' class='search_result' id='search_result_brand'  multiple>											
															</select>
														</td>
													</tr>
													</table>
												</td>
												<td align='center' width=80>
													<div class='float01 email_btns01'>
														<ul>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#goods_display_sub_area_B'), 'B','ADD','brand');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
															</li>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#goods_display_sub_area_B'), 'B','REMOVE','brand');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
															</li>
														</ul>
													</div>
												</td>
												<td width='300' style='vertical-align:bottom;'>
													<table width='100%' border='0' align='center'>
														<tr>
															<td colspan='2' >
																<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='selected_result'>
																</div-->
																<select name=\"selected_result[brand][]\" style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='selected_result_brand' validation=false title='브랜드' multiple>
																";
																//$vip_array = get_vip_member('4');
																$sql = "SELECT b.b_ix, b.brand_name FROM shop_brand b, shop_popup_brand_relation pbr where b.b_ix = pbr.b_ix and pbr.popup_ix = '".$popup_ix."'   ";
																$db->query($sql);
																$selected_brands = $db->fetchall();
																

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
									<div class='goods_auto_area'  id='goods_display_sub_area_S' style='".(($display_auto_sub_type == "S") ? "display:block;":"display:none;")."'>
										<table   border='0'  cellpadding=0 cellspacing=0 >								
											<tr>
												<td width='300'>
													<table  border='0' cellpadding=0 cellspacing=0 align='center'>
														<tr align='left'>
															<td width='100'>
																<input type=text class=textbox name='search_text'  id='search_text' style='width:150px;margin-bottom:2px;' value='' onkeyup=\"SearchInfo('S',$('DIV#goods_display_sub_area_S'), 'seller');\"> 
																<!--onclick=ShowModalWindow('./charger_search.php?company_id=3444fde7c7d641abc19d5a26f35a12cc&target=4&amp;code=',600,530,'charger_search')-->
															</td>
															<td align='center'>
																<img src='../images/btn_select_seller.gif' onclick=\"SearchInfo('S',$('DIV#goods_display_sub_area_S'), 'seller');\"  style='cursor:pointer;'> 
																<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('DIV#goods_display_sub_area_S #search_result_seller option'),'selected')\" style='cursor:pointer;'/>
															</td>
														</tr>
														<tr>
															<td colspan='2' >
																<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='search_result'>
																</div-->
																<select name='search_result[seller]' style=' width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' class='search_result' id='search_result_seller'  multiple>											
																</select>
															</td>
														</tr>
													</table>
												</td>
												<td align='center' width=80>
													<div class='float01 email_btns01'>
														<ul>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#goods_display_sub_area_S'), 'S','ADD','seller');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
															</li>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#goods_display_sub_area_S'),'S','REMOVE','seller');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
															</li>
														</ul>
													</div>
												</td>
												<td width='300' style='vertical-align:bottom;'>
													<table width='100%' border='0' align='center'>
														<tr>
															<td colspan='2' >
																<!--div style=' width:320px;height:148px;font-size:12px;background:#fff;border:1px solid silver;' id='selected_result'>
																</div-->
																<select name=\"selected_result[seller][]\" style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='selected_result_seller' validation=false title='셀러' multiple>
																";
																//$vip_array = get_vip_member('4');
																$sql = "SELECT ccd.company_id, ccd.com_name 
																			FROM common_company_detail ccd, shop_popup_seller_relation pcr 
																			where ccd.company_id = pcr.company_id and pcr.popup_ix = '".$popup_ix."'   ";
																$db->query($sql);
																$selected_sellers = $db->fetchall();
																

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
									<select name='display_order_type'>
										<option value='order_cnt' ".($display_order_type == "order_cnt" ? "selected":"").">구매수순</option>
										<option value='view_cnt' ".($display_order_type == "view_cnt" ? "selected":"").">클릭수순</option>
										<option value='sellprice' ".($display_order_type == "sellprice" ? "selected":"").">최저가순</option>
										<option value='regdate' ".($display_order_type == "regdate" ? "selected":"").">최근등록순</option>
										<option value='wish_cnt' ".($display_order_type == "wish_cnt" ? "selected":"").">찜한순</option>
										<option value='after_score' ".($display_order_type == "after_score" ? "selected":"").">후기순위</option>
									</select>
									으로 노출 하며 최근 <input type='text' class='textbox' name='recent_priod' id='display_auto_priod_".($i+1)."' size=10 value='".$recent_priod."'> 일 기준으로 합니다.
								</div>
							</div>
						  </td>
						</tr>";
}
$Contents .= " 
                        <tr bgcolor='#F8F9FA'><!--팝업 띄우는 에디터 부분 디스플레이 NONE으로 숨겨둠 2014-04-03 양원석-->
                        <td colspan=5>
							 <table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
								<tr  id='dhtml_editor_area' ".(($is_use_templet == '0') ? "style='display:none'":"").">
								  <td height='30' colspan='3' style='padding:10px;'>
										  <table id='tblCtrls' width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
											<tr>
											  <td bgcolor='F5F6F5' colspan='2' >
												 <table width='100%' border='0' cellspacing='0' cellpadding='0'>
												  <tr>
													<td width='18%' height='56'>
															<table width='100%' height='56' border='0' align='center' cellpadding='0' cellspacing='0'>
														<tr align='center' valign='bottom'>
														  <td height='26'><a href='javascript:doBold();' onMouseOver=\"MM_swapImage('editImage1','','../webedit/image/wtool1_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool1.gif' name='editImage1' width='19' height='18' border='0' id='editImage1'></a></td>
														  <td><a href='javascript:doItalic();' onMouseOver=\"MM_swapImage('editImage2','','../webedit/image/wtool2_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool2.gif' name='editImage2' width='19' height='18' border='0' id='editImage2'></a></td>
														  <td><a href='javascript:doUnderline();' onMouseOver=\"MM_swapImage('editImage3','','../webedit/image/wtool3_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool3.gif' name='editImage3' width='19' height='18' border='0' id='editImage3'></a></td>
														</tr>
														<tr>
														  <td height='3' colspan='3'></td>
														</tr>
														<tr align='center' valign='top'>
														  <td height='27'><a href='javascript:doLeft();' onMouseOver=\"MM_swapImage('editImage8','','../webedit/image/wtool8_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool8.gif' name='editImage8' width='19' height='18' border='0' id='editImage8'></a></td>
														  <td><a href='javascript:doCenter();' onMouseOver=\"MM_swapImage('editImage9','','../webedit/image/wtool9_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool9.gif' name='editImage9' width='19' height='18' border='0' id='editImage9'></a></td>
														  <td><a href='javascript:doRight();' onMouseOver=\"MM_swapImage('editImage10','','../webedit/image/wtool10_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool10.gif' name='editImage10' width='19' height='18' border='0' id='editImage10'></a></td>
														</tr>
													  </table>
														 </td>
													<td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
													<td width='19%'>
															<table width='100%' border='0' cellspacing='0' cellpadding='0'>
														<tr>
														  <td width='100%' height='27' align='center' valign='bottom'><a href='javascript:doFont();' onMouseOver=\"MM_swapImage('editImage4','','../webedit/image/wtool4_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool4.gif' name='editImage4' width='84' height='22' border='0' id='editImage4'></a></td>
														</tr>
														<tr>
														  <td height='2'></td>
														</tr>
														<tr>
														  <td height='27' align='center' valign='top'><a href='javascript:doSize();' onMouseOver=\"MM_swapImage('editImage11','','../webedit/image/wtool11_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool11.gif' name='editImage11' width='84' height='22' border='0' id='editImage11'></a></td>
														</tr>
													  </table>
														 </td>
													<td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
													<td width='20%'>
															<table width='100%' border='0' cellspacing='0' cellpadding='0'>
														<tr>
														  <td height='27' align='center' valign='bottom'><a href='javascript:doForcol();' onMouseOver=\"MM_swapImage('editImage5','','../webedit/image/wtool5_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool5.gif' name='editImage5' width='95' height='22' border='0' id='editImage5'></a></td>
														</tr>
														<tr>
														  <td height='2'></td>
														</tr>
														<tr>
														  <td height='27' align='center' valign='top'><a href='javascript:doBgcol();' onMouseOver=\"MM_swapImage('editImage12','','../webedit/image/wtool12_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool12.gif' name='editImage12' width='95' height='22' border='0' id='editImage12'></a></td>
														</tr>
													  </table>
														 </td>
													<td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
													<td width='18%'>
															<table width='100%' border='0' cellspacing='0' cellpadding='0'>
														<tr>
														  <td height='27' align='center' valign='bottom'><a href='javascript:doImage();' onMouseOver=\"MM_swapImage('editImage6','','../webedit/image/wtool6_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool6.gif' name='editImage6' width='73' height='22' border='0' id='editImage6'></a></td>
														</tr>
														<tr>
														  <td height='2'></td>
														</tr>
														<tr>
														  <td height='27' align='center' valign='top'><a href='javascript:doTable();' onMouseOver=\"MM_swapImage('editImage13','','../webedit/image/wtool13_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool13.gif' name='editImage13' width='73' height='22' border='0' id='editImage13'></a></td>
														</tr>
													  </table>
														 </td>
													<td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
													<td width='25%'>
															<table width='100%' border='0' cellspacing='0' cellpadding='0'>
														<tr>
														  <td height='27' align='center' valign='bottom'><a href='javascript:doLink();' onMouseOver=\"MM_swapImage('editImage7','','../webedit/image/wtool7_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool7.gif' name='editImage7' width='74' height='22' border='0' id='editImage7'></a></td>
														</tr>
														<tr>
														  <td height='2'></td>
														</tr>
														<tr>
														  <td height='27' align='center' valign='top'><a href='javascript:doMultilink();' onMouseOver=\"MM_swapImage('editImage14','','../webedit/image/wtool14_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool14.gif' name='editImage14' width='111' height='22' border='0' id='editImage14'></a></td>
														</tr>
													  </table>
														 </td>
												  </tr>
												</table>
												 </td>
											</tr>
											</table>
											<table width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
											<tr>
												<td colspan='3' >
												  <input type='hidden' name='content' value=''>
												  <input type='hidden' name='text' value=''>
												  <iframe align='right' id='iView' frameborder=0 style='width: 100%; height:310px;border:1px solid silver' scrolling='YES' hspace='0' vspace='0'></iframe>
												  <textarea name='popup_text'  style='display:none'>".$popup_text."</textarea>
												  <!-- html편집기 메뉴 종료 -->
												</td>
											</tr>
											<tr>
											<td width='120' height='25' align='center' ></td>
											<td colspan='2' align='right'>&nbsp;
											<a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
											<a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
											</td>
										</tr>
									  </table>
								  </td>
								</tr>
								<tr>
								  <td bgcolor='D0D0D0' height='1' colspan='4'></td>
								</tr>
								<tr><td colspan=3 align=right style='padding:10px;'>";
								if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
								$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle> ";
								}else{}
								if($mmode == "pop"){
									$Contents .= "<a href='javascript:self.close();'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a>";
								}else{
									$Contents .= "<a href='../display/popup.list.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a>";
								}
								$Contents .= "
									</td>
								</tr>
							  </table>
                        </td>
                        </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </form>
      </div>
    </td>
  </tr>";
 /*
 $help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >팝업은 팝업의 사이즈 및 팝업창 위치 사용기간 등을 설정하실수 있습니다 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 <u>팝업는 </u> 사용으로 되어 있는 팝업만 메인에서 자동으로 노출됩니다.  </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >작업을 하실때는 표시여부를 <u>표시하지 않음</u>으로 설정한후 작업이 완료되면 다시 표시로 변경하시면 메인에 노출되게 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >작업하신 파일을 노출하기전 미리 확인하시길 원하시면 <b>팝업 미리보기</b> 버튼을 클릭하시면 확인하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>기간이 만료된 팝업</u>는 <u>자동으로 노출이 종료</u>됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>일반 팝업 태그참고</b></td></tr>
	<tr><td valign=top></td><td class='small' >&lt;A href=\"javascript:opener.document.location.href='링크하고자 하는 페이지 URL';self.close(); \" &gt;  ---> 링크페이지로 이동후 팝업 닫힘 스크립트</td></tr>
	<tr><td valign=top></td><td class='small' >&lt;A onclick=\"self.close();\" href=\"링크하고자 하는 페이지 URL'\" target=\"_blank\" &gt;  ---> 새창으로 페이지 연후 팝업 닫힘 스크립트</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>레이어 팝업 태그참고</b></td></tr>
	<tr><td valign=top></td><td class='small' >&lt;A href=\"javascript:parent.document.location.href='링크하고자 하는 페이지 URL'; \" &gt;  ---> 링크페이지로 이동 스크립트</td></tr>
	<tr><td valign=top></td><td class='small' >&lt;A onclick=\"closeWin();\" href=\"링크하고자 하는 페이지 URL'\" target=\"_blank\" &gt;  ---> 새창으로 페이지 연후 팝업 닫힘 스크립트</td></tr>
</table>
";*/
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

//$help_text = HelpBox("팝업 관리", $help_text);
$Contents .= "
  <tr>
    <td align='left'>

  $help_text

    </td>
  </tr>
</table>

<form name='lyrstat'><input type='hidden' name='opend' value=''></form>
<Script Language='JavaScript'>
init()
</Script>";



if($_GET["mmode"] == "pop"){
	$P = new ManagePopLayOut();
}else{
	$P = new LayOut();
}
$Script = "<script language='javascript' src='popup.write.js'></script>\n<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";

if($agent_type == "M"){
	$P->addScript = $Script;
	$P->OnloadFunction = "Init(document.INPUT_FORM);MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')";//showSubMenuLayer('storeleft');
	$P->Navigation = "모바일샵 > 모바일 팝업관리 > 팝업추가";
	$P->title = "모바일 팝업관리";
	$P->strLeftMenu = mshop_menu();
	$P->strContents = $Contents;

}else{
	$P->addScript = $Script;
	$P->Navigation = "프로모션/전시 > 팝업관리 > 팝업추가";
	$P->title = "팝업추가";
	if($popup_position == "A"){
		$P->NaviTitle = "관리자 팝업추가";
	}else{
		$P->NaviTitle = "팝업추가";
	}
	$P->OnloadFunction = "Init(document.INPUT_FORM);MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')";//showSubMenuLayer('storeleft');
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
}
echo $P->PrintLayOut();

function DisplayTemplet($group_code=1){
	global $db ,$admininfo;

	$sql = "select * 
				from shop_display_templetinfo dt 
				where disp = 1 and dt_div = 'p'
				order by dt_ix asc 
				 ";

	//echo $sql."<br><br>";
	$db->query($sql);




	if ($db->total == 0){
		$mString .= " ";
	}else{
		$i=0;
		$mString .= "<ul>";
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			//$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "	<li>
									<div onclick=\"CopyDisplayType($(this), 'display_type_area_".($group_code)."', ".($group_code).");\"  style='display:inline-block;text-align:center;width:138px;margin:0px 0 0 0px;'>
										<img src='../images/".$_SESSION["admininfo"]["language"]."/P_type_".$db->dt[dt_ix].".png' align=center ><br>
										<div class='control_view' style='padding-top:3px;display:none;'>
										<input type='hidden' class='textbox' name='display_type[".($group_code)."][type][]' id='display_type_".($group_code)."_0' value='".$db->dt[dt_ix]."'  style='border:0px;' disabled=true><label for='display_type_".($group_code)."_0'>".$db->dt[dt_name]."(".$db->dt[dt_goods_num]."EA)</label>
										<select name='display_type[".($group_code)."][set_cnt][]' class=set_cnt dt_goods_num='".$db->dt[dt_goods_num]."' disabled>";
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



function GroupCategoryDisplay($popup_ix, $group_code=1){
	global $db ,$admininfo;

	$sql = "select pd.* , dt.dt_ix, dt.dt_name, dt.dt_goods_num
				from shop_popup_display pd 
				left join shop_display_templetinfo dt on  pd.display_type = dt.dt_ix and dt_div = 'p'
				where pd.popup_ix = '".$popup_ix."' 
				order by pd.vieworder asc 
				 ";

	//echo $sql."<br><br>";
	$db->query($sql);




	if ($db->total == 0){
		$mString .= " ";
	}else{
		$i=0;
		
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			//$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<div  ondblclick=\"$(this).remove();DisplayCntCalcurate('$group_code');\"  style='display:inline-block;text-align:center;width:138px;margin:0px 10px 0 0px;'>
									<img src='../images/".$_SESSION["admininfo"]["language"]."/P_type_".$db->dt[dt_ix]."_on.png' align=center ><br>
									<div class='control_view' style='padding-top:3px;display:block;'>
									<input type=hidden name='display_type[".($group_code)."][pd_ix][]' value='".$db->dt[pd_ix]."'>
									<input type='hidden' class='textbox' name='display_type[".($group_code)."][type][]' id='display_type_".($group_code)."_0' value='".$db->dt[dt_ix]."'  style='border:0px;'  ><label for='display_type_".($group_code)."_0'>".$db->dt[dt_name]."(".$db->dt[dt_goods_num]."EA)</label>
									<select name='display_type[".($group_code)."][set_cnt][]' class=set_cnt  dt_goods_num='".$db->dt[dt_goods_num]."' onchange=\"DisplayCntCalcurate('$group_code');\">";
										for($j=0;$j < 10;$j++){
											$mString .= "<option value='".($j+1)."' ".($db->dt[set_cnt] == ($j+1) ? "selected":"").">".($j+1)."</option>";
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


function relationPopupProductList($popup_ix, $group_code, $disp_type=""){
	global $start,$page, $orderby, $admin_config, $pprid;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$sql = "SELECT COUNT(*) FROM ".TBL_SHOP_PRODUCT." p, shop_popup_product_relation ppr where p.id = ppr.pid and popup_ix = '$popup_ix'   and p.disp = 1";
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[0];

	$sql = "SELECT p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, ppr_ix, ppr.vieworder,   p.brand_name
					FROM ".TBL_SHOP_PRODUCT." p, shop_popup_product_relation ppr
					where p.id = ppr.pid and popup_ix != '' and popup_ix = '$popup_ix'  and p.disp = 1
					order by ppr.vieworder asc limit $start,$max";
	//echo nl2br($sql);
	$db->query($sql);

	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script>'."\n";
			$mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				$imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $db->dt['id'], 'c');
				$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$db->dt['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'");'."\n";
			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;
}

function PrintCategoryRelation($group_code,$popup_ix){
	global $db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth, r.pcr_ix, r.regdate  
				from shop_popup_category_relation r, ".TBL_SHOP_CATEGORY_INFO." c 
				where c.cid = r.cid and popup_ix='".$popup_ix."'";

	//echo $sql."<br><br>";
	$db->query($sql);




	if ($db->total == 0){
		$mString .= "<table cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."' >
								<col width=5>
								<col width=*>
								<col width=100>
							  </table>";
	}else{
		$i=0;
		$mString = "<table width=100% border=0 cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."'>";
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='category[".$group_code."][]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<!--td class='table_td_white small' width='50'><input type='radio' name='basic[".$group_code."]' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."></td-->
				<td class='table_td_white small ' width='*'>".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</td>
				<td class='table_td_white' width='100' align=right><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(".$group_code.",this.parentNode.parentNode)' style='cursor:pointer;' /></td>
				</tr>";
		}
		$mString .= "</table>";
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";


	return $mString;


}
?>