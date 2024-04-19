<?
/*
메인프로모션 분류 추가로 인해 이미지명이 중복되어 저장되므로 분류 코드를 이미지명 앞에 붙여서 중복 저장을 막음(기존에 쓰던 방식은 그대로 유지) kbk 13/05/09
*/

include("../class/layout.class");

/*
TRUNCATE TABLE `shop_discount`; 
TRUNCATE TABLE `shop_discount_product_group` ;
TRUNCATE TABLE `shop_discount_display_relation` ;
TRUNCATE TABLE `shop_discount_product_relation`;
*/

$dc_ix = intval($_GET["dc_ix"]);

$sql = "SELECT * 
			FROM shop_discount dc  
			where dc.dc_ix ='".$dc_ix."'
			order by discount_use_sdate desc limit 0,1";

$slave_db->query($sql); //AND cid='$cid'
if($slave_db->total){
	$slave_db->fetch();
	$act = "update";
	$mall_ix = $slave_db->dt[mall_ix]; 

	$discount_info = $slave_db->dt;
	
	foreach($week_name as $key => $value){
		if($discount_info["week_no_".$key]){
			$week_no[] = $discount_info["week_no_".$key];
		}
	}
	$dc_ix = $slave_db->dt[dc_ix];
 	$discount_sale_title = $slave_db->dt[discount_sale_title];
	$member_target = $slave_db->dt[member_target]; 

	$use_time = $slave_db->dt[use_time]; 
	$headoffice_rate = $slave_db->dt[headoffice_rate]; 
	$seller_rate = $slave_db->dt[seller_rate]; 
	$sale_rate = $slave_db->dt[sale_rate]; 

	$discount_sale_type = $slave_db->dt[discount_sale_type]; 
	$week_no_1 = $slave_db->dt[week_no_1]; 
	$week_no_2 = $slave_db->dt[week_no_2]; 
	$week_no_3 = $slave_db->dt[week_no_3]; 
	$week_no_4 = $slave_db->dt[week_no_4]; 
	$week_no_5 = $slave_db->dt[week_no_5]; 
	$week_no_6 = $slave_db->dt[week_no_6]; 
	$week_no_7 = $slave_db->dt[week_no_7]; 

	$discount_use_sdate = date("Y-m-d H:i:s",$slave_db->dt[discount_use_sdate]);
	$discount_use_edate = date("Y-m-d H:i:s",$slave_db->dt[discount_use_edate]);
	//echo $discount_use_edate;
	//echo "aaa".$event_priod_edate; 

	$md_mem_ix = $slave_db->dt[md_mem_ix]; 

	$is_use = $slave_db->dt[is_use];
	$seller_ix = $slave_db->dt[seller_ix];
}else{
	$is_use = "1";
	$act = "insert";
	$discount_use_sdate = date("Y-m-d H:i:s");
	$discount_use_edate = date("Y-m-d H:i:s");
	$member_target = "A";
	$use_time = "0";
	$discount_sale_type = 1;

	$md_mem_ix = $admininfo[charger_ix]; 
}


$Script = "
<style type='text/css'>
  div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;border:1px solid silver }
  div#drop_relation_product.hover { border:5px dashed #aaa; background:#efefef; }
  table.tb {width:100%;cursor:move;}
</style>
<style>
  .sortable { list-style-type: none; margin: 0; padding: 0; width: 450px; }
  .sortable div { margin: 3px 3px 3px 0; padding: 1px; float: left; width: 140px; height: 160px;  }
  </style>

<script type='text/javascript' src='/admin/js/jquery.multisortable.js'></script>
<script type='text/javascript' src='../js/ms_productSearch.js'></script>

<Script Language='JavaScript'>
/*
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
 

function init(){
	var frm = document.discount_frm;
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

function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	
	var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	//document.write('main_position.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	window.frames['act'].location.href = 'main_position.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
}
*/
$(document).ready(function () {

	
	//click event
	/*
	$('.promotion_type_box').click(function(){
		promotion_type_check_reset();
		var img_tag = $(this).find('img');
		img_tag.attr('src',img_tag.attr('src').replace('.png','_on.png'));
		
		$(this).find('input').attr('checked','checked');
	});
	*/
	 
	 $('.sortable').sortable();

	 /*
	$('.sortable').each(function(){
		alert(1);
		$(this).sortable();
	});
	*/

	$('.add_type_choice li').click(function(){
		promotion_type_check_reset();
		var img_tag = $(this).find('img');
		//alert(img_tag.attr('src')+';;;'+img_tag.attr('src').indexOf('_on'));
		if(img_tag.attr('src').indexOf('_on') == -1){
			$(this).find('img').attr('src',img_tag.attr('src').replace('.png','_on.png'));
		}
	});
	
	$('select[name=mall_ix]').on('change',function(){
	   viewTypeChange($(this).val());
	});
	
	viewTypeChange($('select[name=mall_ix]').val());
	
	function viewTypeChange(e){
	   if(e == '20bd04dac38084b2bafdd6d78cd596b1'){
	       //국문
	       $('#devUnit').text('원');
	       $('.devTypeDisp').attr('disabled',false);
	   } else if(e == '20bd04dac38084b2bafdd6d78cd596b2'){
	       //영문
	       $('#devUnit').text('$');
	       $('.devTypeDisp').attr('disabled',false);
	   }else{
	       //전체
	       $('.devTypeDisp').attr('disabled',true);
	       $('#discount_sale_type_1_1').attr('checked',true);
	   }
	}
	
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
	//alert(jquery_obj.html());
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
	$('DIV#goods_auto_area_'+group_code).show();
	$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+selected_value).show();
}
 
function ChangeDisplaySubTarget(obj, group_code, selected_value){
	obj.parent().parent().find('div[class^=display_sub_target_area]').hide();
	$('DIV#display_sub_target_area_'+selected_value).show();
}


</Script>";

if($page_title == ""){
	$page_title = "기획할인등록";
}

$Contents = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation($page_title, "프로모션(마케팅) > 상품할인관리 > ".$page_title)."</td>
</tr>";
 
$Contents .= "
  <tr>
    <td>

        <form name='discount_frm' method='POST' onSubmit=\"return CheckFormValue(this)\" action='../promotion/discount.act.php' style='display:inline;' enctype='multipart/form-data' target='iframe_act'><!--SubmitX-->
		<input type='hidden' name=act value='".$act."'> 
		<input type='hidden' name=dc_ix value='".$dc_ix."'>
		<input type='hidden' name=discount_type value='".$discount_type."'>		
					<table border='0' cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
					  <col width='15%'>
					  <col width='35%'>
					  <col width='15%'>
					  <col width='35%'>";
					if($_SESSION["admin_config"][front_multiview] == "Y"){
					$Contents .= "
					<tr height=28>
						<td class='search_box_title' > 프론트 전시 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." <span class=small>전시 구분을 선택하실경우 해당 사이트에 노출됩니다.</span></td>
					</tr>";
					} 
$Contents .= "

					  <tr height=28>
						<td class='search_box_title' nowrap> <b>기획할인명</b></td>
						<td class='search_box_item' colspan=3>
						<input class='textbox' type='text' name='discount_sale_title' value='".$slave_db->dt[discount_sale_title]."' validation=true title='기획할인명' maxlength='50' style='width:400px'>
						</td>
					  </tr>
					<tr>
						<td class='input_box_title'  > <b>노출기간 </b></td>
						<td class='input_box_item' style='padding:10px;' colspan=3>
						".search_date('discount_use_sdate','discount_use_edate',$discount_use_sdate,$discount_use_edate,'Y','A')."
						</td> 
					</tr>
					<tr bgcolor=#ffffff style='display:none'>
						<td class='input_box_title'>요일선택 : </td>
						<td class='input_box_item' style='padding:5px;' colspan=3>";

						  foreach($week_name as $key => $value){
							  $checked_str = "checked";
							  
							  //if(is_array($week_no)){
								if($slave_db->dt["week_no_".$key] == 1){
									$checked_str = "checked";
								}
							  //}
							   
							$Contents .= "<input type='checkbox' class='week_no' name='week_no[".$key."]' id='week_no_".$key."' value='1' ".$checked_str." validation=false title='요일'> <label for='week_no_".$key."' >".$value."</label> ";
						  }
							 $Contents .= "  
						</td>
					</tr>
					<tr height=28>
						<td class='search_box_title' nowrap > <b>타임세일 설정</b></td>
						<td class='search_box_item' >
                            <input type='radio' name='use_time' id='use_time_0' value='0' ".CompareReturnValue("0",$use_time,"checked")." validation=true title='타임세일 설정 사용여부'> <label for='use_time_0' >미사용</label>
                            <input type='radio' name='use_time' id='use_time_1' value='1' ".CompareReturnValue("1",$use_time,"checked")." validation=true title='타임세일 설정 사용여부'> <label for='use_time_1' >사용</label>
						</td>
						<td class='input_box_title' >  <b>사용여부</b></td>
					    <td class='input_box_item' style='padding:3px;' colspan='3'>
							<input type='radio' name='is_use' id='is_use_1'  align='middle' value='1' ".($is_use == '1' || $is_use == '' ? "checked":"")."><label for='is_use_1' class='green'>사용함</label> 
							<input type='radio' name='is_use' id='is_use_0'  align='middle' value='0' ".($is_use == '0' ? "checked":"")."><label for='is_use_0' class='green'>미사용</label> 
					  </td>
					  </tr>
					  <tr>
							<td class='input_box_title' >  담당 MD</td>
							<td class='input_box_item' colspan='3'>  ".MDSelect($md_mem_ix)."</td>			
							<!--
							<td class='input_box_title' nowrap> <b>셀러업체 </b></td>
							<td class='input_box_item'>
							".companyAuthList($seller_ix , "validation=false title='셀러업체' ")."
							</td>
							-->
						</tr>
						<tr style='display:none;'>
						<td class='input_box_title' nowrap  >회원조건</td>
						<td colspan='3' class='search_box_item' style='padding:10px;'><a name='member_target'></a>
							<div style='padding-bottom:10px;'>
							    <input type='radio' class='textbox' name='member_target' id='member_target_a' size=50 value='A' style='border:0px;' ".(($member_target == "A" || $member_target == "") ? "checked":"")." onclick=\"$('#display_sub_target_area').hide();$('#display_sub_target').hide();\"/><label for='member_target_a'>회원전체</label>
								<input type='radio' class='textbox' name='member_target' id='member_target_g' size=50 value='G' style='border:0px;' ".($member_target == "G"  ? "checked":"")." onclick=\"$('#display_sub_target_area').show();$('#display_sub_target').show();ChangeDisplaySubTarget($(this), ".($i+1)." , 'G');\"/><label for='member_target_g'>회원 그룹별</label>
								<!--input type='radio' class='textbox' name='member_target' id='member_target_m' size=50 value='M' style='border:0px;' ".($member_target == "M"  ? "checked":"")." onclick=\"$('#display_sub_target_area').show();$('#display_sub_target').show();ChangeDisplaySubTarget($(this), ".($i+1)." , 'M');\"/><label for='member_target_m'>개별회원별</label--> 
								<br>
							</div>
						    <div id='display_sub_target_area' ".(($member_target == "A" || $member_target == "") ? "style='display:none' ":"style='display:block'")." >
								<div class='display_sub_target_area'  id='display_sub_target_area_G' ".(($member_target == "G") ? "style='display:block' ":"style='display:none'")." >
									<table   border='0'  cellpadding=0 cellspacing=0 >								
											<tr>
												<td width='300'>
													<table  border='0' cellpadding=0 cellspacing=0 align='center'>
														<tr align='left'>
															<td width='100'>   
																<input type=text class=textbox name='search_text'  id='search_text' style='width:210px;margin-bottom:2px;' value='' >  
															</td>
															<td align='center'>
																<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif'  onclick=\"SearchInfo('G',$('DIV#display_sub_target_area_G'), 'group');\"  style='cursor:pointer;'> 
																<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택'  onclick=\"SelectedAll($('DIV#display_sub_target_area_G #search_result_group option'),'selected')\"  style='cursor:pointer;'/>
															</td>
															</tr>
														<tr>
															<td colspan='2' > 
																<select name='search_result[]' class='search_result' id='search_result_group'  style=' width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' multiple>											
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
																<select name='selected_result[group][]' class='selected_result' id='selected_result_group'  style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='' validation=false title='회원그룹' multiple>
																";
																//$vip_array = get_vip_member('4');
																$sql = "SELECT gi.gp_ix, gi.gp_name FROM shop_groupinfo gi, shop_discount_display_relation fdr 
																			where gi.gp_ix = fdr.r_ix and fdr.relation_type = 'G' and dc_ix = '".$dc_ix."'  ";

																$slave_db->query($sql);
																$selected_groups = $slave_db->fetchall();
																

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
								<div class='display_sub_target_area'  id='display_sub_target_area_M'  ".(($member_target == "M") ? "style='display:block' ":"style='display:none'")." >
									<table   border='0'  cellpadding=0 cellspacing=0 >								
											<tr>
												<td width='300'>
													<table  border='0' cellpadding=0 cellspacing=0 align='center'>
														<tr align='left'>
															<td width='100'>   
																<input type=text class=textbox name='search_text'  id='search_text' style='width:210px;margin-bottom:2px;' value='' >  
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
																<select name='search_result[]' class='search_result' id='search_result_member'  style=' width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' multiple>											
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
																<select name='selected_result[member][]' class='selected_result' id='selected_result_member'  style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='' validation=false title='회원' multiple>
																";
																//$vip_array = get_vip_member('4');
																$sql = "SELECT cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$slave_db->ase_encrypt_key."') as name , cu.id
																			FROM common_user cu, common_member_detail cmd, shop_discount_display_relation fdr 
																			where cu.code = cmd.code and cmd.code = fdr.r_ix and fdr.relation_type = 'M' and dc_ix = '".$dc_ix."'  ";
																$slave_db->query($sql);
																$selected_members = $slave_db->fetchall();
																

																for($j = 0; $j < count($selected_members); $j++){
																	$Contents .="<option value='".$selected_members[$j][code]."' ondblclick=\"$(this).remove();\" selected>".$selected_members[$j][name]."(".$selected_members[$j][id].")</option>";
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
					    ";
 
$Contents .= " 
						
					</table> 
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td bgcolor='#ffffff'>
                    <table border='0' cellpadding=3 cellspacing=0 width='100%'>


                    <tr>
                      <td  colspan='4' style='padding:0px;' id='group_infos'>";
$gdb = new Database;
$sql = "SELECT * FROM shop_discount_product_group where  dc_ix ='".$dc_ix."'  order by group_code asc ";
//echo $sql;
$gdb->query($sql);//div_code = '".$div_code."'
if($gdb->total || true){
	$group_total = $gdb->total-1;

	for($i=0;($i < $gdb->total || $i < 1);$i++){
	$gdb->fetch($i);

	$dpg_ix = $gdb->dt[dpg_ix];
	$group_name = $gdb->dt[group_name];
	$headoffice_rate = $gdb->dt[headoffice_rate];
	$seller_rate = $gdb->dt[seller_rate];
	$sale_rate = $gdb->dt[sale_rate];
	$discount_sale_type = $gdb->dt[discount_sale_type];
	if(!$discount_sale_type){
		$discount_sale_type = 1;
	}
	$commission = $gdb->dt[commission];
	$round_position = $gdb->dt[round_position];
	$round_type = $gdb->dt[round_type];
	$event_amount_type = $gdb->dt[event_amount_type];
$Contents .= "
                      <div id='group_info_area' class='group_info_area' group_code='".($i+1)."'>
                      <div style='padding:10px 10px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;' id='discount_group_title'>할인 상품그룹 (GROUP ".($i+1).")</b> <!--<a onclick=\"AddDiscountGroup('group_info_area')\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle></a> <a onclick=\"$(this).closest('DIV#group_info_area').remove();\" id='group_del' ".($i == 0 ? "style='display:none;'":"")."><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle></a>--></div>
                      <table width='100%' border='0' cellpadding='5' cellspacing='1' bgcolor='#E9E9E9' class='search_table_box' id='discount_group_table'>
					  <col width='15%'>
					  <col width='35%'>
					  <col width='15%'>
					  <col width='35%'>
						<tr>
						  <td class='search_box_title'><b>기획할인 상품그룹명</b></td>
						  <td class='search_box_item' colspan=3>
						  <input type='text' class='textbox' name='discount_group[".($i+1)."][group_name]' id='group_name' size=50 value='".$group_name."' validation=true title='상품그룹명'> 
						  </td>
						</tr>";
						if($discount_type == "SP" && false){
						$Contents .= "
						<tr>
						  <td class='search_box_title'><b>수수료</b></td>
						  <td class='search_box_item' colspan=3>
						  <input type='text' class='textbox integer numeric' name='discount_group[".($i+1)."][commission]' id='commission' size=10 value='".$commission."' validation=true title='수수료'> %
						  </td>
						</tr>";
						}
						$Contents .= "
						<tr height=30>
						  <td class='input_box_title'  > 할인율</td>
						  <td class='input_box_item'  style='padding-left:10px;' colspan=3 nowrap>
								
								
								<div style='display:inline;' >
								본사 : <input type=text class='textbox integer numeric' validation='true' title='본사부담율' name='discount_group[".($i+1)."][headoffice_rate]' id='headoffice_rate' maxlength='10' style='width: 50px; filter: blendTrans(duration=0.5)'  align='absmiddle' value='".$headoffice_rate."' onkeyup=\"changeCommission($(this));\">";
						if($discount_type == "SP" && false){
						$Contents .= " + 
								셀러 : <input type=text class='textbox numeric' validation='true' title='셀러부담율' name='discount_group[".($i+1)."][seller_rate]' id='seller_rate' maxlength='10' style='width: 50px; filter: blendTrans(duration=0.5)'  align='absmiddle' value='".$seller_rate."' onkeyup=\"changeCommission($(this));\" disabled>
								</div>
								= 
								전체합계 : 
								<input type=text class='textbox numeric' validation='true' title='전체할인율' name='discount_group[".($i+1)."][sale_rate]' id='sale_rate' maxlength='20' style='width: 50px; filter: blendTrans(duration=0.5)'  align='absmiddle' value='".$sale_rate."' readonly>";
						}
						$Contents .= "								
								<input type=radio class='devTypeDisp' id='discount_sale_type_".($i+1)."_2' name='discount_group[".($i+1)."][discount_sale_type]' value=2 ".CompareReturnValue('2', $discount_sale_type, ' checked')." onclick=\" $(this).closest('td').find('div[id^=round_type_area_]').hide();\"><label for='discount_sale_type_".($i+1)."_2'><span id='devUnit'>원</span></label>
								<input type=radio id='discount_sale_type_".($i+1)."_1' name='discount_group[".($i+1)."][discount_sale_type]' value=1 ".($discount_sale_type == "1" ? ' checked':"")." onclick=\" $(this).closest('td').find('div[id^=round_type_area_]').css('display','inline')\"><label for='discount_sale_type_".($i+1)."_1'>%</label> 
								<span style='color:blue; font-size: 11px;'>(금액 할인은 프론트 전시 구분 전체 영역에서 사용할 수 없습니다.)</span>
								<div id='round_type_area_".($i+1)."'  ".($discount_sale_type == "1" || $discount_sale_type == "" ? "style='display:inline;'":"style='display:none;'").">
								<select name='discount_group[".($i+1)."][round_position]' id='round_position' style='display:None;'> 
									<option value='1' ".($round_position == 1 ? "selected":"").">일 자리</option>
									<option value='2' ".($round_position == 2 ? "selected":"").">십 자리</option>
									<option value='3' ".($round_position == 3 ? "selected":"").">백 자리</option>
								</select>
								<select name='discount_group[".($i+1)."][round_type]' id='round_type' style='display:None;' > 
									<!--option value='1' ".($round_type == 1 ? "selected":"").">반올림</option-->
									<!--option value='2' ".($round_type == 2 ? "selected":"").">반내림</option-->
									<option value='3' ".($round_type == 3 ? "selected":"").">내림</option>
									<option value='4' ".($round_type == 4 ? "selected":"").">올림</option>
								</select>
								</div>
						  </td>
						</tr>
						
						<tr>
						  <td class='search_box_title'><b>전시여부</b></td>
						  <td class='search_box_item' colspan=3>
						  <input type='radio' class='textbox' name='discount_group[".($i+1)."][is_display]' id='is_display_".($i+1)."_y' id2='is_display_y' size=50 value='Y' style='border:0px;' ".($gdb->dt[is_display] == "Y" || $act == "insert" ? "checked":"")."><label for='is_display_".($i+1)."_y' id2='label_is_display_y'> 전시</label>
						  <input type='radio' class='textbox' name='discount_group[".($i+1)."][is_display]' id='is_display_".($i+1)."_n' id2='is_display_n' size=50 value='N' style='border:0px;' ".($gdb->dt[is_display] == "N" ? "checked":"")."><label for='is_display_".($i+1)."_n' id2='label_is_display_n'> 전시 하지 않음</label>
						  </td>
						</tr>";

if(false){// 추후 노출
$Contents .= "
						<tr>
						  <td class='search_box_title'><b>상품그룹 이미지</b></td>
						  <td class='search_box_item' style='padding:10px' colspan=3>
						  <input type='file' class='textbox' name='discount_group[".($i+1)."][group_img]' id='group_img' size=50 value=''> <input type='checkbox' name='discount_group[".($i+1)."][group_img_del]' id='group_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_img_del_".($i+1)."'>그룹이미지 삭제</label><br>
						  <div style='padding:5px 5px 5px 0px;height:90px;width:90%;overflow:auto' id='group_img_area_".($i+1)."'>";
if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/main/".$dc_ix."_main_group_".($i+1).".gif") && $dc_ix){//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/main/".$dc_ix."_main_group_".($i+1).".gif'>";
} else {
	//if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/main/main_group_".($i+1).".gif")){
	//	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/main/main_group_".($i+1).".gif'>";
	//}
}

$Contents .= "						</div><br>
						  <span class=small><!--* 이미지 등록을 하지 않을경우 상품그룹명이 노출됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>상품그룹이미지링크</b></td>
						  <td class='search_box_item' colspan=3>
						  <input type='text' class='textbox' name='discount_group[".($i+1)."][group_link]' id='group_link_".($i+1)."' size=50 value='".$gdb->dt[group_link]."'>
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>상품그룹 배너 이미지</b></td>
						  <td class='search_box_item' style='padding:10px' colspan=3>
						  <input type='file' class='textbox' name='discount_group[".($i+1)."][group_banner_img]' id='group_banner_img' size=50 value=''> <input type='checkbox' name='discount_group[".($i+1)."][group_banner_img_del]' id='group_banner_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_banner_img_del_".($i+1)."'>그룹 배너이미지 삭제</label><br>
						  <div style='padding:5px 5px 5px 0px;height:90px;width:90%;overflow:auto' id='group_banner_img_area_".($i+1)."'>";
if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/main/".$dc_ix."_main_group_banner_".($i+1).".gif")){//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/main/".$dc_ix."_main_group_banner_".($i+1).".gif'>";
} else {
	//if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/main/main_group_banner_".($i+1).".gif")){
	//	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/main/main_group_banner_".($i+1).".gif'>";
	//}
}

$Contents .= "		</div><br>
						  <span class=small><!--* 이미지 등록을 하지 않을경우 상품그룹명이 노출됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
						  </td>
						</tr>
						
						<tr>
						  <td class='search_box_title'><b>전시타입</b></td>
						  <td class='search_box_item promotion_types'   style='padding:10px 10px;' colspan=3><a name='display_type_area_".($i+1)."'></a>
							<div id='display_type_area_".($i+1)."' class=sortable style='width:100%;float:left;'>
							".GroupCategoryDisplay($dc_ix, $dpg_ix, ($i+1))."
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
						  <td class='search_box_title'><b>상품노출갯수</b><span style='padding-left:2px' class='helpcloud' help_width='390' help_height='30' help_html='전시타입 배열 설정에 따라 노출갯수의 수를 지정해 주셔야 합니다<br />ex)5배열의 전시타입일 경우 30, 35등 5단위 노출갯수 지정'><img src='/admin/images/icon_q.gif' /></span></td>
						  <td class='search_box_item' colspan=3>
						  <input type='text' class='textbox numeric' name='discount_group[".($i+1)."][product_cnt]' id='product_cnt_".($i+1)."' size=10 value='".$gdb->dt[product_cnt]."'> 전시타입을 선택하시면 상품 노출갯수가 자동으로 선택됩니다. 
						  </td>
						</tr>";
}
        if($discount_type == "SP") {
            $Contents .= "<input type='hidden' name='discount_group[" . ($i + 1) . "][coupon_use_yn]' value='Y' />";
        } else {
            $Contents .= "         <tr>
						  <td class='search_box_title'><b>쿠폰 사용 여부</b></td>
						  <td class='search_box_item' colspan=3>
						  <label><input type='radio' class='textbox' name='discount_group[" . ($i + 1) . "][coupon_use_yn]' value='Y' style='border:0px;' " . ($gdb->dt[coupon_use_yn] == "Y" ? "checked" : "") . "> 사용</label>
						  <label><input type='radio' class='textbox' name='discount_group[" . ($i + 1) . "][coupon_use_yn]' value='N' style='border:0px;' " . ($gdb->dt[coupon_use_yn] == "N" || $act == "insert" ? "checked" : "") . "> 사용안함</label>
						  </td>
						</tr>";
        }

        $Contents .= " <tr>
						  <td class='search_box_title'><b>할인상품</b></td>
						  <td class='search_box_item' style='padding:10px 10px;' colspan=3><a name='goods_display_type_".($i+1)."'></a>
						   <div style='padding-bottom:10px;'>
						    
						       <label><input type='radio' class='textbox' name='discount_group[".($i+1)."][goods_display_type]' value='A' style='border:0px;' ".($gdb->dt[goods_display_type] == "A" || $gdb->dt[goods_display_type] == "" ? "checked":"")." onclick=\"$(this).closest('td').find('[id^=goods_auto_area_]').hide();$(this).closest('td').find('[id^=goods_manual_area_]').hide();\">전체 상품</label>

							  <label><input type='radio' class='textbox' name='discount_group[".($i+1)."][goods_display_type]' value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M") ? "checked":"")." onclick=\"$(this).closest('td').find('[id^=goods_auto_area_]').hide();$(this).closest('td').find('[id^=goods_manual_area_]').show();\">특정상품</label>

							  <label><input type='radio' class='textbox' name='discount_group[".($i+1)."][goods_display_type]' value='ME' style='border:0px;' ".(($gdb->dt[goods_display_type] == "ME") ? "checked":"")." onclick=\"$(this).closest('td').find('[id^=goods_auto_area_]').hide();$(this).closest('td').find('[id^=goods_manual_area_]').show();\">제외상품</label>

							  <label><input type='radio' class='textbox' name='discount_group[".($i+1)."][goods_display_type]' value='C' style='border:0px;' ".($gdb->dt[goods_display_type] == "C" ? "checked":"")." onclick=\"$(this).closest('td').find('[id^=goods_auto_area_]').show();$(this).closest('td').find('[id^=goods_manual_area_]').hide();\">상품분류</label>

							  <label><input type='radio' class='textbox' name='discount_group[".($i+1)."][goods_display_type]' value='CE' style='border:0px;' ".($gdb->dt[goods_display_type] == "CE" ? "checked":"")." onclick=\"$(this).closest('td').find('[id^=goods_auto_area_]').show();$(this).closest('td').find('[id^=goods_manual_area_]').hide();\">상품분류 제외</label>
						  </div>";

$Contents .= "
						  <div id='goods_manual_area_".($i+1)."' style='".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "ME") ? "display:block;":"display:none;")."'>
							  
							  <div style='width:100%;padding:5px;' id='group_product_area_".($i+1)."' >".relationEventGroupProductList($gdb->dt[dc_ix],($gdb->dt[group_code] ? $gdb->dt[group_code]:($i+1)), "clipart")."</div>
							  <div style='width:100%;float:left;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
							  </div>
							  <div style='display:block;float:left;margin-top:10px;'>
							  <!--#goods_display_type_".($i+1)."--><a href=\"#\" id='btn_goods_search_add' onclick=\"ms_productSearch.show_productSearchBox(event,".($i+1).",'productList_".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a>
							  <input type='text' class='textbox' name='search_goods' id='search_goods' size='20' value='' onkeyup=\"SearchGoods($(this), '".($i+1)."')\"> <img type='image' src='../images/korea/btn_search.gif' style='cursor:pointer;' onclick=\"SearchGoods($(this), '".($i+1)."')\" align='absmiddle'> <img src='../images/".$admininfo["language"]."/btc_del.gif' onclick=\"SearchGoodsDelete($(this))\" border='0'  style='cursor:pointer;vertical-align:middle;'>
							  </div>
						  </div>
						   <div style='padding:0px 0px;".($gdb->dt[goods_display_type] == "C"  || $gdb->dt[goods_display_type] == "CE" ? "display:block;":"display:none;")."' id='goods_auto_area_".($i+1)."'>";

	$Contents .= "	<div class='goods_display_type_area'  id='goods_display_sub_area_C'>
										<table   border='0'  cellpadding=0 cellspacing=0 >								
											<tr>
												<td width='300'>
													<table  border='0' cellpadding=0 cellspacing=0 align='center'>
														<tr align='left'>
															<td width='100'>   
																<input type=text class=textbox name='search_text'  id='search_text' style='width:260px;margin-bottom:2px;' value=''>  
															</td>
															<td align='center' >
																<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' id=btn_search_info onclick=\"SearchInfo('C',$(this).closest('#goods_display_sub_area_C'), '".($i+1)."');\"  style='cursor:pointer;'> 
																<img src='../images/icon/pop_all.gif' id='btn_select_all' alt='전체선택' title='전체선택'  onclick=\"SelectedAll($('DIV#goods_display_sub_area_C #search_result_".($i+1)." option'),'selected')\"  style='cursor:pointer;'/>
															</td>
															</tr>
														<tr>
															<td colspan='2' > 
																<select name='search_result[]' class='search_result' uid='search_result' id='search_result_".($i+1)."'  style=' width:370px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' ondblclick=\"MoveSelectBox($('DIV#goods_auto_area_".($i+1)." DIV#goods_display_sub_area_C'), 'C','ADD',".($i+1).");\" multiple>											
																</select>
															</td>
														</tr>
													</table>
												</td>
												<td align='center' width=80>
													<div>
														<ul>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#goods_display_sub_area_C'), 'C','ADD','".($i+1)."');\" id='btn_selectbox_add'><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
															</li>
															<li>
																<a href=\"javascript:MoveSelectBox($('DIV#goods_display_sub_area_C'), 'C','REMOVE','".($i+1)."');\" id='btn_selectbox_remove'><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
															</li>
														</ul>
													</div>
												</td>
												<td width='300' style='vertical-align:bottom;'>
													<table width='100%' border='0' align='center'>
														<tr>
															<td colspan='2' > 
																<select name='discount_group[".($i+1)."][category][]' class='selected_result' uid='selected_result'  id='selected_result_".($i+1)."'  style='width:350px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='' validation=false title='카테고리' multiple>
																";
																//$vip_array = get_vip_member('4');
																$sql = "SELECT ci.cid, ci.depth, ci.cname FROM shop_category_info ci, shop_discount_display_relation ddr 
																			where ci.cid = ddr.r_ix and ddr.relation_type = 'C' and dc_ix = '".$dc_ix."' and ddr.group_code = '".($i+1)."'  ";
																$slave_db->query($sql);
																$selected_categorys = $slave_db->fetchall();
																

																for($j = 0; $j < count($selected_categorys); $j++){
																	$Contents .="<option value='".$selected_categorys[$j][cid]."' ondblclick=\"$(this).remove();\" selected>".strip_tags(getCategoryPath($selected_categorys[$j][cid],$selected_categorys[$j][depth]))."</option>";
																	//ondblclick=\"MoveSelectBox($('DIV#display_position_area'), 'C','REMOVE','".($i+1)."');\" 
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
					  </table><br><br>
					  </div>";

	unset($gdb->dt);
	}
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
  </tr>

  ";
   
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


//$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:0px;'><table><tr><td valign=middle><b>사은품 행사등록</b></td><td></td></tr></table></div>", $help_text,110)."</div>";

$Contents .= "
  <tr>
    <td align='left' style='padding-bottom:100px;'>
    $help_text

    </td>
  </tr>";

$Contents .= "
	</table>


 
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

//my_init('$group_total');
</Script>";



$Script = "<!--script language='JavaScript' src='../js/scriptaculous.js'></script>\n
<script language='JavaScript' src='../js/dd.js'></script>\n
<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->\n
<script language='javascript' src='../display/event.write.js'></script>\n
<script language='JavaScript' src='../webedit/webedit.js'></script -->\n
<script language='JavaScript' src='../promotion/discount.js'></script>
<script language='javascript' src='../search.js'></script>
$Script";
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	if($discount_type == "M"){
		$P->strLeftMenu = mshop_menu();
		$P->Navigation = "모바일샾 > 모바일상품할인관리 > $page_title";
	}else{
		$P->strLeftMenu = promotion_menu();
		$P->Navigation = "프로모션(마케팅) > 상품할인관리 > $page_title";
	}
	$P->title =  $page_title;
	$P->NaviTitle =  $page_title;
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->title = $page_title;
	$P->OnloadFunction = "";//showSubMenuLayer('storeleft');
	if($discount_type == "M"){
		$P->strLeftMenu = mshop_menu();
		$P->Navigation = "모바일샾 > 모바일상품할인관리 > $page_title";
	}else{
		$P->strLeftMenu = promotion_menu();
		$P->Navigation = "프로모션(마케팅) > 상품할인관리 > $page_title";
	}
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


function relationEventGroupProductList($dc_ix, $group_code, $disp_type=""){
	global $start,$page, $orderby, $admin_config, $dprid, $page_title,$slave_db;

	$max = 1000;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT COUNT(*) FROM ".TBL_SHOP_PRODUCT." p, shop_discount_product_relation dpr where p.id = dpr.pid and dc_ix = '".$dc_ix."' and group_code = '".$group_code."' and p.disp = 1";
	$slave_db->query($sql);
	$slave_db->fetch();
	$total = $slave_db->dt[0];

	$sql = "SELECT p.id, p.pcode, p.shotinfo, p.pname, p.listprice,  p.sellprice,  p.coprice, p.reserve, dpr_ix, dpr.vieworder, dpr.group_code, p.brand_name, p.wholesale_price,p.wholesale_sellprice, p.state, p.disp, r.cid, c.depth
					FROM ".TBL_SHOP_PRODUCT." p
					right join ".TBL_SHOP_PRODUCT_RELATION." r on p.id = r.pid and r.basic = '1'
					right join ".TBL_SHOP_CATEGORY_INFO." c	 on r.cid = c.cid 
					right join shop_discount_product_relation dpr on p.id = dpr.pid
					
					where  dc_ix != '' and dc_ix = '".$dc_ix."' and group_code = '".$group_code."' 
					order by dpr.vieworder asc limit $start,$max";
					//and p.disp = 1

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
			$mString .= '<script id="setproduct">'."\n";
			$mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
			for($i=0;$i<count($products);$i++){
				//$slave_db->fetch($i);
				$imgPath = PrintImage($admin_config['mall_data_root'].'/images/addimgNew', $products[$i]['id'], 'slist');
				 
				$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$products[$i]['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($products[$i]['pname']))).'", "'.addslashes(addslashes(trim($products[$i]['brand_name']))).'", "'.$products[$i]['sellprice'].'", "'.$products[$i]['listprice'].'", "'.$products[$i]['reserve'].'", "'.$products[$i]['coprice'].'", "'.$products[$i]['wholesale_price'].'", "'.$products[$i]['wholesale_sellprice'].'", "'.$products[$i]['disp'].'", "'.$products[$i]['state'].'", "'.$products[$i]['dcprice'].'");'."\n";
			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;
}

function DisplayTemplet($group_code){
	global $slave_db ,$admininfo;

	$sql = "select * 
				from shop_display_templetinfo dt 
				where disp = 1
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

function GroupCategoryDisplay($dc_ix, $dpg_ix, $group_code){
	global $slave_db ,$admininfo;

	$sql = "select mgd.* , dt.dt_ix, dt.dt_name, dt.dt_goods_num
				from shop_main_group_display mgd 
				left join shop_display_templetinfo dt on  mgd.display_type = dt.dt_ix
				where mgd.dpg_ix = '".$dpg_ix."' 
				order by mgd.vieworder asc 
				 ";

	//echo $sql."<br><br>";
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
									<input type=hidden name='discount_group[".($group_code)."][display_type][mgd_ix][]' id='mgd_ix' value='".$slave_db->dt[mgd_ix]."'>
									<input type='hidden' class='textbox' name='discount_group[".($group_code)."][display_type][type][]' id='type' value='".$slave_db->dt[dt_ix]."'  style='border:0px;'  ><!-- id='display_type_".($group_code)."_0' --><label for='display_type_".($group_code)."_0'>".$slave_db->dt[dt_name]."(".$slave_db->dt[dt_goods_num]."EA)</label>
									<select name='discount_group[".($group_code)."][display_type][set_cnt][]' id='set_cnt' class=set_cnt  dt_goods_num='".$slave_db->dt[dt_goods_num]."' onchange=\"DisplayCntCalcurate('$group_code');\">";
										for($j=0;$j < 10;$j++){
											$mString .= "<option value='".($j+1)."' ".($slave_db->dt[set_cnt] == ($j+1) ? "selected":"").">".($j+1)."</option>";
										}
										$mString .= "		
									</select>
									</div>
								</div>";
		}
		
	} 

	return $mString;
}



function PrintCategoryRelation($group_code,$dc_ix){
	global $slave_db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth, r.mcr_ix, r.regdate  
				from shop_main_category_relation r, ".TBL_SHOP_CATEGORY_INFO." c 
				where group_code = '".$group_code."' 
				and c.cid = r.cid and dc_ix='".$dc_ix."'";

	//echo $sql."<br><br>";
	$slave_db->query($sql);




	if ($slave_db->total == 0){
		$mString .= "<table cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."' >
								<col width=5>
								<col width=*>
								<col width=100>
							  </table>";
	}else{
		$i=0;
		$mString = "<table width=100% border=0 cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."'>";
		for($i=0;$i<$slave_db->total;$i++){
			$slave_db->fetch($i);
			$parent_cname = GetParentCategory($slave_db->dt[cid],$slave_db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='discount_group[".$group_code."][category][]' id='_category' value='".$slave_db->dt[cid]."' style='display:none'></td>
				<td class='table_td_white small ' width='*'>".($parent_cname != "" ? $parent_cname." > ":"").$slave_db->dt[cname]."</td>
				<td class='table_td_white' width='100' align=right><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(".$group_code.",this.parentNode.parentNode)' style='cursor:pointer;' /></td>
				</tr>";
		}
		$mString .= "</table>";
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";


	return $mString;
}

/*
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
*/
 
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
					FROM ".TBL_SHOP_PRODUCT." p, shop_discount_product_relation fpr
					where p.id = fpr.pid and p.disp = 1   ";
	$slave_db->query($sql);
	$total = $slave_db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, fpr_ix
					FROM ".TBL_SHOP_PRODUCT." p, shop_discount_product_relation fpr
					where p.id = fpr.pid and p.disp = 1 order by fpr.vieworder limit $start,$max";
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

	global $start,$page, $orderby, $admin_config, $fprid,$slave_db;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve
					FROM ".TBL_SHOP_PRODUCT." p, shop_discount_product_relation fpr
					where p.id = fpr.pid and p.disp = 1   ";
	$slave_db->query($sql);
	$total = $slave_db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, fpr_ix, fpr.vieworder, fpr.group_code
					FROM ".TBL_SHOP_PRODUCT." p, shop_discount_product_relation fpr
					where p.id = fpr.pid and p.disp = 1 order by fpr.vieworder asc limit $start,$max";
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
CREATE TABLE IF NOT EXISTS `shop_discount` (
  `dc_ix` int(6) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `mall_ix` varchar(32) DEFAULT NULL COMMENT '프론트전시구분값',
  `discount_sale_title` varchar(255) DEFAULT NULL COMMENT '사은품 행사명',
  `discount_use_sdate` int(10) DEFAULT NULL COMMENT '시작일',
  `discount_use_edate` int(10) DEFAULT NULL COMMENT '종료일',
  `week_no_1` int(2) DEFAULT 0 COMMENT '적용요일(월요일)',
  `week_no_2` int(2) DEFAULT 0 COMMENT '적용요일(화요일)',
  `week_no_3` int(2) DEFAULT 0 COMMENT '적용요일(수요일)',
  `week_no_4` int(2) DEFAULT 0 COMMENT '적용요일(목요일)',
  `week_no_5` int(2) DEFAULT 0 COMMENT '적용요일(금요일)',
  `week_no_6` int(2) DEFAULT 0 COMMENT '적용요일(토요일)',
  `week_no_7` int(2) DEFAULT 0 COMMENT '적용요일(일요일)',
  `use_time` varchar(1) DEFAULT NULL COMMENT '시간대설정 사용여부(미사용:0, 사용:1)',
  `start_time` int(2) DEFAULT 0 COMMENT '적용시작시간',
  `start_min` int(2) DEFAULT 0 COMMENT '적용시작분',
  `end_time` int(2) DEFAULT 0 COMMENT '적용종료시간',
  `end_min` int(2) DEFAULT 0 COMMENT '적용종료분',
  `member_target` int(10) DEFAULT NULL COMMENT '회원조건',
  `md_mem_ix` varchar(32) NOT NULL COMMENT '총괄MD 코드',
  `seller_ix` varchar(32) NOT NULL COMMENT '셀러업체',
  `disp` char(1) DEFAULT NULL COMMENT '사용여부',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`dc_ix`),
  KEY `mg_use_sdate_ix` (`discount_use_sdate`),
  KEY `mg_use_edate_ix` (`discount_use_edate`),
  KEY `disp_ix` (`disp`),
  KEY `editdate_ix` (`editdate`),
  KEY `regdate_ix` (`regdate`),
  KEY `SHOP_DISCOUNT_MALL_IX` (`mall_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='할인정보 관리' ;

CREATE TABLE IF NOT EXISTS `shop_discount_display_relation` (
  `ddr_ix` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '키값',
  `dc_ix` int(4) unsigned zerofill NOT NULL DEFAULT '0000' COMMENT '할인정보 키값',
  `relation_type` enum('A','G','M') NOT NULL DEFAULT 'A' COMMENT '회원조건 구부값',
  `r_ix` varchar(32) NOT NULL COMMENT '회원조건 키값',
  `vieworder` int(5) NOT NULL DEFAULT '0' COMMENT '노출순서',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '입력 flag',
  `regdate` datetime DEFAULT NULL COMMENT '등록일자',
  PRIMARY KEY (`ddr_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='할인정보 회원조건 정보'


CREATE TABLE IF NOT EXISTS `shop_discount_product_relation` (
  `dpr_ix` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `pid` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000' COMMENT '상품아이디',
  `dc_ix` int(6) NOT NULL COMMENT '할인정보 코드',
  `group_code` int(2) NOT NULL DEFAULT '1' COMMENT '그룹코드',
  `vieworder` int(5) NOT NULL DEFAULT '0' COMMENT '정렬순서',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '업데이트 구분값',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`dpr_ix`),
  KEY `dc_ix_pid` (`dc_ix`,`pid`),
  KEY `group_code` (`group_code`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='할인정보 상품정보 ';



CREATE TABLE `shop_discount_product_group` (
  `dpg_ix` int(8) unsigned zerofill NOT NULL auto_increment COMMENT '키값',
  `dc_ix` int(6) NOT NULL COMMENT '할인정보 코드',
  `group_name` varchar(100) NOT NULL default '' COMMENT '할인 상품그룹명',
  `group_code` int(2) NOT NULL default '0' COMMENT '그룹코드',
  `headoffice_rate` int(10) default NULL COMMENT '본사부담',
  `seller_rate` int(10) default NULL COMMENT '셀러부담',
  `sale_rate` int(10) default NULL COMMENT '전체 할인률',
  `goods_display_type` int(10) default NULL COMMENT '상품전시타입',  
  `insert_yn` enum('Y','N') default 'Y' COMMENT '등록 flag',
  `is_display` enum('Y','N') default 'Y' COMMENT '전시여부',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `regdate` datetime default NULL COMMENT '등록일자',
  PRIMARY KEY  (`dpg_ix`)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COMMENT='할인 그룹정보'


 

*/
?>