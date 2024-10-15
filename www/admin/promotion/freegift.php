<?
/*
메인프로모션 분류 추가로 인해 이미지명이 중복되어 저장되므로 분류 코드를 이미지명 앞에 붙여서 중복 저장을 막음(기존에 쓰던 방식은 그대로 유지) kbk 13/05/09
*/
include("../class/layout.class");
// 
/*
TRUNCATE TABLE `shop_freegift`; 
TRUNCATE TABLE `shop_freegift_product_group` ;
TRUNCATE TABLE `shop_freegift_display_relation` ;
TRUNCATE TABLE `shop_freegift_product_relation`;
*/
$db = new Database;

$sql = "SELECT * 
			FROM shop_freegift fg  
			where fg.fg_ix ='".$_GET["fg_ix"]."'
			order by fg_use_sdate desc limit 0,1";

$db->query($sql); //AND cid='$cid'
if($db->total){
	$db->fetch();
	$mall_ix = $db->dt[mall_ix]; 

	$fg_ix = $db->dt[fg_ix];
 	$freegift_event_title = $db->dt[freegift_event_title];
	$member_target = $db->dt[member_target]; 
	$freegift_condition = $db->dt[freegift_condition]; 

	$fg_use_sdate = date("Y-m-d H:i:s",$db->dt[fg_use_sdate]);
	$fg_use_edate = date("Y-m-d H:i:s",$db->dt[fg_use_edate]);
	//echo "aaa".$event_priod_edate; 

	$md_mem_ix = $db->dt[md_mem_ix]; 

	$disp = $db->dt[disp];

}else{
	$disp = "1";
	$fg_use_sdate = date("Y-m-d H:i:s");
	$fg_use_edate = date("Y-m-d H:i:s");
	$member_target = "A";
    $freegift_condition = "G";
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
<script type='text/javascript' src='../js/ms_productSearch.js?ver=0.1'></script>
<script language='javascript' src='../search.js'></script>
<Script Language='JavaScript'>
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
	return true;
}




function init(){
	var frm = document.freegift_frm;
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
	       $('.devUnit').text('원');
	       $('.devTypeDisp').attr('disabled',false);
	   } else if(e == '20bd04dac38084b2bafdd6d78cd596b2'){
	       //영문
	       $('.devUnit').text('$');
	       $('.devTypeDisp').attr('disabled',false);
	   }else{
	       //전체
	       $('.devTypeDisp').attr('disabled',true);
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
	$('DIV#goods_auto_area_'+group_code+' DIV#goods_display_sub_area_'+selected_value).show();
}
 
function ChangeDisplaySubTarget(obj, group_code, selected_value){
	obj.parent().parent().find('div[class^=display_sub_target_area]').hide();
	$('DIV#display_sub_target_area_'+selected_value).show();
}


</Script>";



$Contents = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("사은품행사등록", "프로모션(마케팅) > 사은품관리 > 사은품행사등록 ")."</td>
</tr>";
 
$Contents .= "
  <tr>
    <td>

        <form name='freegift_frm' method='POST' onSubmit=\"return CheckFormValue(this)\" action='freegift.act.php' style='display:inline;' enctype='multipart/form-data' target='act'><!--SubmitX-->
		<input type='hidden' name=act value='update'> 
		<input type='hidden' name=fg_ix value='".$fg_ix."'>
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
						<td class='search_box_title' nowrap> <b>사은품 행사명</b></td>
						<td class='search_box_item' colspan=3>
						<input class='textbox' type='text' name='freegift_event_title' value='".$db->dt[freegift_event_title]."' validation=true title='사은품 행사명' maxlength='50' style='width:400px'>
						</td>
					  </tr>
					<tr>
						<td class='input_box_title'  > <b>노출기간 </b></td>
						<td class='input_box_item' style='padding:10px;' colspan=3>
						".search_date('fg_use_sdate','fg_use_edate',$fg_use_sdate,$fg_use_edate,'Y','A')."
						</td> 
					</tr>					
					<tr >
					  <td class='input_box_title' >  <b>매출조건설정</b></td>
					  <td class='input_box_item' colspan=3>";
					  foreach($_FREEGIFT_CONDITION as $key => $value){
						$Contents .= "<input type='radio' name='freegift_condition' id='freegift_condition_".$key."' value='".$key."' ".CompareReturnValue($freegift_condition,$key,"checked")." validation=true title='매출조건 설정'> <label for='freegift_condition_".$key."' >".$value."</label> ";
					  }
					  $Contents .= " 
					  </td>
					</tr>
					<tr>
						<td class='input_box_title' nowrap  >회원조건</td>
						<td colspan='3' class='search_box_item' style='padding:10px;'><a name='member_target'></a>
							<div style='padding-bottom:10px;'>
							    <input type='radio' class='textbox' name='member_target' id='member_target_a' size=50 value='A' style='border:0px;' ".(($member_target == "A" || $member_target == "") ? "checked":"")." onclick=\"$('#display_sub_target_area').hide();$('#display_sub_target').hide();\"/><label for='member_target_a'>전체</label>
								<input type='radio' class='textbox' name='member_target' id='member_target_g' size=50 value='G' style='border:0px;' ".($member_target == "G"  ? "checked":"")." onclick=\"$('#display_sub_target_area').show();$('#display_sub_target').show();ChangeDisplaySubTarget($(this), ".($i+1)." , 'G');\"/><label for='member_target_g'>회원 그룹별</label>
								<input type='radio' class='textbox' name='member_target' id='member_target_m' size=50 value='M' style='border:0px;' ".($member_target == "M"  ? "checked":"")." onclick=\"$('#display_sub_target_area').show();$('#display_sub_target').show();ChangeDisplaySubTarget($(this), ".($i+1)." , 'M');\"/><label for='member_target_m'>개별회원별</label> 
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
																$sql = "SELECT gi.gp_ix, gi.gp_name FROM shop_groupinfo gi, shop_freegift_display_relation fdr 
																			where gi.gp_ix = fdr.r_ix and fdr.member_target = 'G' and fg_ix = '".$fg_ix."'  ";

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
								<div class='display_sub_target_area'  id='display_sub_target_area_M'  ".(($member_target == "M") ? "style='display:block' ":"style='display:none'")." >
									<table   border='0'  cellpadding=0 cellspacing=0 >								
											<tr>
												<td width='300'>
													<table  border='0' cellpadding=0 cellspacing=0 align='center'>
														<tr align='left'>
															<td width='100'>   
																<input type=text class=textbox name='search_text'  id='search_text' style='width:210px;margin-bottom:2px;' value='' \">  
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
																$sql = "SELECT cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , cu.id
																			FROM common_user cu, common_member_detail cmd, shop_freegift_display_relation fdr 
																			where cu.code = cmd.code and cmd.code = fdr.r_ix and fdr.member_target = 'M' and fg_ix = '".$fg_ix."'  ";
																$db->query($sql);
																$selected_members = $db->fetchall();
																

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
						<tr>
							<td class='search_box_title' >  담당 MD</td>
							<td class='search_box_item' >  ".MDSelect($md_mem_ix)."</td>							
							<td class='input_box_title' >  <b>노출여부</b></td>
							<td class='input_box_item'>
								<input type='radio' name='disp' id='disp_1'  align='middle' value='1' ".($disp == '1' || $disp == '' ? "checked":"")."><label for='disp_1' class='green'>노출함</label> 
								<input type='radio' name='disp' id='disp_0'  align='middle' value='0' ".($disp == '0' ? "checked":"")."><label for='disp_0' class='green'>노출안함</label> 
							</td>
						</tr>
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
$sql = "SELECT * FROM shop_freegift_product_group where  fg_ix ='".$fg_ix."'  order by group_code asc ";
//echo $sql;
$gdb->query($sql);//div_code = '".$div_code."'
if($gdb->total || true){
	$group_total = $gdb->total-1;

	for($i=0;($i < $gdb->total || $i < 1);$i++){
	$gdb->fetch($i);

	$fpg_ix = $gdb->dt[fpg_ix];
	$group_name = $gdb->dt[group_name];
	$gift_cnt = $gdb->dt[gift_cnt];
	$sale_condition_s = $gdb->dt[sale_condition_s];
	$sale_condition_e = $gdb->dt[sale_condition_e];
	$event_amount_type = $gdb->dt[event_amount_type];
	$event_amount = $gdb->dt[event_amount];
	$event_amount_type = $gdb->dt[event_amount_type];
	$event_amount_type = $gdb->dt[event_amount_type];
$Contents .= "
                      <div id='group_info_area' class='group_info_area' group_code='".($i+1)."'>
                      <div style='padding:10px 10px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;' id='freegift_group_title'>사은품 <!--그룹  (GROUP ".($i+1).")--></b> <!--a onclick=\"AddGiftGroup('group_info_area')\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle></a> <a class='del_button' onclick=\"del_table('group_info_area',".($i+1).");\" ".($i == 0 ? "style='display:none;'":"")."><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle></a--></div>
                      <table width='100%' border='0' cellpadding='5' cellspacing='1' bgcolor='#E9E9E9' class='search_table_box' id='freegift_group_table'>
					  <col width='15%'>
					  <col width='35%'>
					  <col width='15%'>
					  <col width='35%'>
						<!--tr>
						  <td class='search_box_title'><b>사은품 상품그룹명</b></td>
						  <td class='search_box_item' colspan=3>
						  <input type='text' class='textbox' name='freegift_group[".($i+1)."][group_name]' id='group_name' size=50 value='".$group_name."' validation=true title='상품그룹명'> 상품그룹 이미지 등록을 하지 않은경우 노출됩니다.
						  </td>
						</tr-->
						<tr height=27 id='freegift_condition_g'>
							<td class='input_box_title'> 매출액</td>
							<td class='input_box_item' colspan=3 style='padding:5px;'>								 
								<input type=text class='textbox number devTypeDisp' name='freegift_group[".($i+1)."][sale_condition_s]' id='sale_condition_s' style='width:100px;' value='".$sale_condition_s."'> <span class='devUnit'>원</span> ~ 
								<input type=text class='textbox number devTypeDisp' name='freegift_group[".($i+1)."][sale_condition_e]' id='sale_condition_e' style='width:100px;' value='".$sale_condition_e."'> <span class='devUnit'>원</span>
								<span style='color:blue; font-size:11px'>(매출액 조건 사용 시 프론트 전시구분 전체는 사용 불가 합니다.)</span>
							</td>
						</tr>
						<tr id='freegift_condition_c' style='display:none;'> 
						    <td class='input_box_title'>카테고리포함</td>
						    <td class='input_box_item' colspan=3 style='padding:5px;'>
                                <div id='display_position_area' style=''>
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
																$sql = "SELECT ci.cid, ci.depth, ci.cname FROM shop_category_info ci, shop_freegift_category_relation pdr 
																			where ci.cid = pdr.cid and fg_ix = '".$fg_ix."'  ";
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
						<tr id='freegift_condition_p' style='display:none;'> 
						    <td class='input_box_title'>상품포함</td>
						    <td class='input_box_item' colspan=3 style='padding:5px;'>
                                <div id='goods_manual_area' style='display:block;' class='goods_manual_area_2'>
                                    <div class='filterBar'>
                                        <div class='searchBar'>
                                            <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,'2','productList_2');\">
                                                <img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle>
                                            </a>
                                            <input type='text' class='textbox' name='search_goods' id='search_goods' size='20' value='' onkeyup=\"SearchGoods($(this), '2')\"> 
                                            <img type='image' src='../images/korean/btn_search.gif' style='cursor:pointer;' onclick=\"SearchGoods($(this), '2')\" align='absmiddle'> 
                                            <img src='../images/".$admininfo["language"]."/btc_del.gif' onclick=\"SearchGoodsDelete($(this))\" border='0'  style='cursor:pointer;vertical-align:middle;'>
                                        </div>                                       
                                    </div>
                                    <div class='products_area'>
                                        <div style='width:100%;padding:5px;' id='group_product_area_2' >".relationFreeGiftSelectProductList($fg_ix, '2', "clipart")."</div>
                                        <div style='clear:both;width:100%;'><span class=small>* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.</span></div>
                                    </div>
                                </div>
						    </td>
						</tr>
						<tr height=27>
							<td class='input_box_title'> 지급수량</td>
							<td class='input_box_item' colspan=3 style='padding:5px;'>								 
								<input type=text class='textbox number' name='freegift_group[".($i+1)."][gift_cnt]' id='gift_cnt' style='width:100px;' value='".$gift_cnt."'>  개 
							</td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>전시여부</b></td>
						  <td class='search_box_item' colspan=3>
						  <input type='radio' class='textbox' name='freegift_group[".($i+1)."][is_display]' id='is_display_".($i+1)."_y' id2='is_display_y' size=50 value='Y' checked style='border:0px;' ".($gdb->dt[is_display] == "Y" ? "checked":"")."><label for='is_display_".($i+1)."_y' id2='label_is_display_y'> 전시</label>
						  <input type='radio' class='textbox' name='freegift_group[".($i+1)."][is_display]' id='is_display_".($i+1)."_n' id2='is_display_n' size=50 value='N' style='border:0px;' ".($gdb->dt[is_display] == "N" ? "checked":"")."><label for='is_display_".($i+1)."_n' id2='label_is_display_n'> 전시 하지 않음</label>
						  </td>
						</tr>";

if(false){// 추후 노출
$Contents .= "
						<tr>
						  <td class='search_box_title'><b>상품그룹 이미지</b></td>
						  <td class='search_box_item' style='padding:10px' colspan=3>
						  <input type='file' class='textbox' name='freegift_group[".($i+1)."][group_img]' id='group_img' size=50 value=''> <input type='checkbox' name='freegift_group[".($i+1)."][group_img_del]' id='group_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_img_del_".($i+1)."'>그룹이미지 삭제</label><br>
						  <div style='padding:5px 5px 5px 0px;height:90px;width:90%;overflow:auto' id='group_img_area_".($i+1)."'>";
if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/main/".$fg_ix."_main_group_".($i+1).".gif") && $fg_ix){//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/main/".$fg_ix."_main_group_".($i+1).".gif'>";
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
						  <input type='text' class='textbox' name='freegift_group[".($i+1)."][group_link]' id='group_link_".($i+1)."' size=50 value='".$gdb->dt[group_link]."'>
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>상품그룹 배너 이미지</b></td>
						  <td class='search_box_item' style='padding:10px' colspan=3>
						  <input type='file' class='textbox' name='freegift_group[".($i+1)."][group_banner_img]' id='group_banner_img' size=50 value=''> <input type='checkbox' name='freegift_group[".($i+1)."][group_banner_img_del]' id='group_banner_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_banner_img_del_".($i+1)."'>그룹 배너이미지 삭제</label><br>
						  <div style='padding:5px 5px 5px 0px;height:90px;width:90%;overflow:auto' id='group_banner_img_area_".($i+1)."'>";
if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/main/".$fg_ix."_main_group_banner_".($i+1).".gif")){//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/main/".$fg_ix."_main_group_banner_".($i+1).".gif'>";
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
							".GroupCategoryDisplay($fg_ix, $fpg_ix, ($i+1))."
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
						  <input type='text' class='textbox numeric' name='freegift_group[".($i+1)."][product_cnt]' id='product_cnt_".($i+1)."' size=10 value='".$gdb->dt[product_cnt]."'> 전시타입을 선택하시면 상품 노출갯수가 자동으로 선택됩니다. 
						  </td>
						</tr>";
}

$Contents .= "
						<tr>
						  <td class='search_box_title'><b>전시상품</b><span style='padding-left:2px' class='helpcloud' help_width='300' help_height='30' help_html='자동등록에 경우 사용할 카테고리를 선택하게 되면 상품등록 시 자동으로 신규 상품이 전시됩니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						  <td class='search_box_item' style='padding:10px 10px;' colspan=3><a name='goods_display_type_".($i+1)."'></a>
						   <div style='padding-bottom:10px;'>
							  <!--input type='radio' class='textbox' name='freegift_group[".($i+1)."][goods_display_type]' id='is_display_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').show();$('#goods_auto_area_".($i+1)."').hide();$('#display_auto_sub_type_".($i+1)."').hide();\"><label for='is_display_".($i+1)."_m'>수동등록</label-->
							  
							   
							  <br>
						  </div>
						  <div id='goods_manual_area_".($i+1)."' style='".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "display:block;":"display:none;")."'>
							  
							  <div style='width:100%;padding:5px;' id='group_product_area_".($i+1)."' >".relationFreeGiftProductList($gdb->dt[fg_ix],($gdb->dt[group_code] ? ($i+1):($i+1)), "clipart")."</div>
							  <div style='width:100%;float:left;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
							  </div>
							  <div style='display:block;float:left;margin-top:10px;'>
							  <a href=\"#goods_display_type_".($i+1)."\" id='btn_goods_search_add' onclick=\"ms_productSearch.show_productSearchBox(event,".($i+1).",'productList_".($i+1)."','clipart','77');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a>
							  <input type='text' class='textbox' name='search_goods' id='search_goods' size='20' value='' onkeyup=\"SearchGoods($(this), '".($i+1)."')\"> <img type='image' src='../images/korean/btn_search.gif' style='cursor:pointer;' onclick=\"SearchGoods($(this), '".($i+1)."')\" align='absmiddle'> <img src='../images/".$admininfo["language"]."/btc_del.gif' onclick=\"SearchGoodsDelete($(this))\" border='0'  style='cursor:pointer;vertical-align:middle;'>
							  </div>
						  </div>
						  
						  </td>
						</tr> 
						
						<tr>
						  <td class='search_box_title'><b>제외상품</b></td>
						  <td class='search_box_item' style='padding:10px 10px;' colspan=3>
						  <div style='padding-bottom:10px;'>
							  <!--input type='radio' class='textbox' name='freegift_group[".($i+1)."][goods_display_type]' id='is_display_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').show();$('#goods_auto_area_".($i+1)."').hide();$('#display_auto_sub_type_".($i+1)."').hide();\"><label for='is_display_".($i+1)."_m'>수동등록</label-->
							  
							   
							  <br>
						  </div>

						  <div id='goods_manual_except_area_3' style='".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "display:block;":"display:none;")."'>
							  
							  <div style='width:100%;padding:5px;' id='group_product_except_area_3' >".relationFreeGiftExceptProductList($gdb->dt[fg_ix],'3', "clipart")."</div>
							  <div style='width:100%;float:left;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
							  </div>
							  <div style='display:block;float:left;margin-top:10px;'>
							  <a href=\"#goods_display_except_type_3\" id='btn_goods_search_add' onclick=\"ms_productSearch.show_productSearchBox(event,3,'productList_except_3','clipart','77');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a>
							  <input type='text' class='textbox' name='search_goods' id='search_goods' size='20' value='' onkeyup=\"SearchGoods($(this), '3')\"> <img type='image' src='../images/korean/btn_search.gif' style='cursor:pointer;' onclick=\"SearchGoods($(this), '3')\" align='absmiddle'> <img src='../images/".$admininfo["language"]."/btc_del.gif' onclick=\"SearchGoodsDelete($(this))\" border='0'  style='cursor:pointer;vertical-align:middle;'>
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
   
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:0px;'><table><tr><td valign=middle><b>사은품 행사등록</b></td><td></td></tr></table></div>", $help_text,110)."</div>"; 

$Contents .= "
  <tr>
    <td align='left' style='padding-bottom:100px;'>
    $help_text

    </td>
  </tr>";

$Contents .= "
	</table>


<form name='lyrstat'><input type='hidden' name='opend' value=''></form>
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



$Script = "<script language='JavaScript' src='freegift.js'></script>
<script type='text/javascript' src='/admin/js/ms_productSearch.js?=".rand()."'></script>
$Script";
$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션(마케팅) > 사은품관리 > 사은품행사등록";
$P->title = "사은품행사등록";
$P->OnloadFunction = "";//showSubMenuLayer('storeleft');
$P->strLeftMenu = promotion_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();


function DisplayTemplet($group_code){
	global $db ,$admininfo;

	$sql = "select * 
				from shop_display_templetinfo dt 
				where disp = 1
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
			$mString .= "	<li  >
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

function GroupCategoryDisplay($fg_ix, $fpg_ix, $group_code){
	global $db ,$admininfo;

	$sql = "select mgd.* , dt.dt_ix, dt.dt_name, dt.dt_goods_num
				from shop_main_group_display mgd 
				left join shop_display_templetinfo dt on  mgd.display_type = dt.dt_ix
				where mgd.fpg_ix = '".$fpg_ix."' 
				order by mgd.vieworder asc 
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
									<input type=hidden name='freegift_group[".($group_code)."][display_type][mgd_ix][]' id='mgd_ix' value='".$db->dt[mgd_ix]."'>
									<input type='hidden' class='textbox' name='freegift_group[".($group_code)."][display_type][type][]' id='type' value='".$db->dt[dt_ix]."'  style='border:0px;'  ><!-- id='display_type_".($group_code)."_0' --><label for='display_type_".($group_code)."_0'>".$db->dt[dt_name]."(".$db->dt[dt_goods_num]."EA)</label>
									<select name='freegift_group[".($group_code)."][display_type][set_cnt][]' id='set_cnt' class=set_cnt  dt_goods_num='".$db->dt[dt_goods_num]."' onchange=\"DisplayCntCalcurate('$group_code');\">";
										for($j=0;$j < 10;$j++){
											$mString .= "<option value='".($j+1)."' ".($db->dt[set_cnt] == ($j+1) ? "selected":"").">".($j+1)."</option>";
										}
										$mString .= "		
									</select>
									</div>
								</div>";
		}
		
	} 

	return $mString;
}



function PrintCategoryRelation($group_code,$fg_ix){
	global $db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth, r.mcr_ix, r.regdate  
				from shop_main_category_relation r, ".TBL_SHOP_CATEGORY_INFO." c 
				where group_code = '".$group_code."' 
				and c.cid = r.cid and fg_ix='".$fg_ix."'";

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


function relationFreeGiftProductList($fg_ix, $group_code, $disp_type=""){
	global $start,$page, $orderby, $admin_config, $fprid;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$sql = "SELECT COUNT(*) FROM ".TBL_SHOP_PRODUCT." p, shop_freegift_product_relation fpr where p.id = fpr.pid and fg_ix = '$fg_ix' and group_code = '$group_code' and p.disp = 1";
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[0];

	$sql = "SELECT 
              p.product_type, p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, p.disp, p.state ,p.one_commission, p.product_type, i.gid ,
              if(p.is_sell_date = '1',p.sell_priod_sdate <= NOW() and p.sell_priod_edate >= NOW(),'1=1') as sell_date
            FROM 
              ".TBL_SHOP_PRODUCT." p 
            left join 
                inventory_goods_unit i on p.pcode=i.gu_ix, shop_freegift_product_relation gp 
            where 
                p.id = gp.pid
            and 
                gp.fg_ix = '".$fg_ix."' and gp.group_code = '".$group_code."' order by gp.vieworder asc limit $start,$max";
    $db->query($sql);

	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){

            $addQaDir = "";
            if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
                $addQaDir = "/QA";
            }

            $mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script id="setproduct">'."\n";
			$mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				if($db->dt['sell_date'] == '1'){
                    $disp = $db->dt[disp];
                }else{
				    $disp = 0;
                }
                $mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$db->dt['id'].'", "'.PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $db->dt['id'], "slist").'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'","", "", "", "", "", "'.$disp.'", "'.$db->dt[state].'","","","","","'.$db->dt[one_commission].'","'.$db->dt[product_type].'","'.$db->dt[gid].'");'."\n";
			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;
}

function relationFreeGiftExceptProductList($fg_ix, $group_code, $disp_type=""){
    global $start,$page, $orderby, $admin_config, $fprid;

    $max = 105;

    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    $db = new Database;

    $sql = "SELECT COUNT(*) FROM ".TBL_SHOP_PRODUCT." p, shop_freegift_select_product_relation fpr where p.id = fpr.pid and fg_ix = '$fg_ix' and group_code = '$group_code' and p.disp = 1";
    $db->query($sql);
    $db->fetch();
    $total = $db->dt[0];

    $sql = "SELECT 
              p.product_type, p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, p.disp, p.state ,p.one_commission, p.product_type, i.gid ,
              if(p.is_sell_date = '1',p.sell_priod_sdate <= NOW() and p.sell_priod_edate >= NOW(),'1=1') as sell_date
            FROM 
              ".TBL_SHOP_PRODUCT." p 
            left join 
                inventory_goods_unit i on p.pcode=i.gu_ix, shop_freegift_select_product_relation gp 
            where 
                p.id = gp.pid
            and 
                gp.fg_ix = '".$fg_ix."' and gp.group_code = '".$group_code."' order by gp.vieworder asc limit $start,$max";
    $db->query($sql);

    if ($db->total == 0){
        if($disp_type == "clipart"){
            $mString = '<ul id="productList_except_'.$group_code.'" name="productList" class="productList"></ul>';
        }
    }else{
        $i=0;
        if($disp_type == "clipart"){

            $addQaDir = "";
            if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
                $addQaDir = "/QA";
            }

            $mString = '<ul id="productList_except_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
            $mString .= '<script id="setproduct">'."\n";
            $mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
            for($i=0;$i<$db->total;$i++){
                $db->fetch($i);
                if($db->dt['sell_date'] == '1'){
                    $disp = $db->dt[disp];
                }else{
                    $disp = 0;
                }
                $mString .= 'ms_productSearch._setProduct("productList_except_'.$group_code.'", "M", "'.$db->dt['id'].'", "'.PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $db->dt['id'], "slist").'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'","", "", "", "", "", "'.$disp.'", "'.$db->dt[state].'","","","","","'.$db->dt[one_commission].'","'.$db->dt[product_type].'","'.$db->dt[gid].'");'."\n";
            }
            $mString .= '</script>'."\n";
        }
    }
    return $mString;
}

function relationFreeGiftSelectProductList($fg_ix, $group_code, $disp_type=""){
    global $start,$page, $orderby, $admin_config, $fprid;

    $max = 500;

    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    $db = new Database;

    $sql = "SELECT COUNT(*) FROM ".TBL_SHOP_PRODUCT." p, shop_freegift_select_product_relation fpr where p.id = fpr.pid and fg_ix = '$fg_ix' and group_code = '$group_code' and p.disp = 1";
    $db->query($sql);
    $db->fetch();
    $total = $db->dt[0];

    $sql = "SELECT p.product_type, p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, p.disp, p.state ,p.one_commission, p.product_type, i.gid  FROM ".TBL_SHOP_PRODUCT." p left join inventory_goods_unit i on p.pcode=i.gu_ix, shop_freegift_select_product_relation gp where p.id = gp.pid
     and gp.fg_ix = '".$fg_ix."' and gp.group_code = '".$group_code."' order by gp.vieworder asc limit $start,$max";
    $db->query($sql);

	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){

            $addQaDir = "";
            if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
                $addQaDir = "/QA";
            }

            $mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script id="setproduct">'."\n";
			$mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
                $mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$db->dt['id'].'", "'.PrintImage($admin_config[mall_data_root]."/images/addimgNew".$addQaDir, $db->dt['id'], "slist").'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'","", "", "", "", "", "'.$db->dt[disp].'", "'.$db->dt[state].'","","","","","'.$db->dt[one_commission].'","'.$db->dt[product_type].'","'.$db->dt[gid].'");'."\n";
			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;
}

function relationProductList2(){

	global $start,$page, $orderby, $admin_config;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$sql = "SELECT distinct p.id,p.pname, p.sellprice, p.reserve
					FROM ".TBL_SHOP_PRODUCT." p, shop_freegift_product_relation fpr
					where p.id = fpr.pid and p.disp = 1   ";
	$db->query($sql);
	$total = $db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, fpr_ix
					FROM ".TBL_SHOP_PRODUCT." p, shop_freegift_product_relation fpr
					where p.id = fpr.pid and p.disp = 1 order by fpr.vieworder limit $start,$max";
	$db->query($sql);




	if ($db->total){

		$mString = "<div id='sortlist'>";

		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$mString .= "<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' id='image_".$db->dt[id]."' title='".cut_str($db->dt[pname],30)."' ondblclick=\"document.frames['act'].location.href='relation.category.act.php?mode=delete&pid=".$pid."&rp_ix=".$db->dt[rp_ix]."'\"width=50 height=50 style='border:1px solid silver' vpace=2 hspace=2>";
		}
	}
	$mString .= "</div>";

	return $mString;

}

function relationProductList($disp_type=""){

	global $start,$page, $orderby, $admin_config, $fprid;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve
					FROM ".TBL_SHOP_PRODUCT." p, shop_freegift_product_relation fpr
					where p.id = fpr.pid and p.disp = 1   ";
	$db->query($sql);
	$total = $db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, fpr_ix, fpr.vieworder, fpr.group_code
					FROM ".TBL_SHOP_PRODUCT." p, shop_freegift_product_relation fpr
					where p.id = fpr.pid and p.disp = 1 order by fpr.vieworder asc limit $start,$max";
	$db->query($sql);



	if ($db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'><!--등록된 이벤트/기획전 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>이벤트/기획전 상품으로 등록됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')." </td></tr>";
		$mString .= "</table>";
	}else{
//		$mString = "<ul id='sortlist' >";

		$i=0;
		if($disp_type == "clipart"){

			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				$mString .= "<div id='seleted_tb_".$db->dt[id]."' style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'>\n";
				$mString .= "<table id='seleted_tb_".$db->dt[id]."' cellpadding=0 cellspacing=0 border=0 style='display:inline;'>\n";
				$mString .= "<tr>\n";
				$mString .= "<td style='display:none;'></td>\n";
				$mString .= "<td><img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' ></td>\n";
				$mString .= "<td style='display:none;'>".$db->dt[pname]."</td>\n";
				$mString .= "<td style='display:none;'><input type='hidden' name='rpid[".$db->dt[group_code]."][]' value='"+spid+"'></td>\n";
				$mString .= "</tr>\n";
				$mString .= "</table>\n";
				$mString .= "</div>\n";
			}
		}else{
	  	$mString .= "<!--li id='image_".$db->dt[id]."' -->
							<table width=100% cellpadding=0 cellspacing=0 id=tb_relation_product class=tb border=0 >
							<col width='60'>
							<col width='*'>
							<col width='60'>";

			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				//ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);$('tb_relation_product').deleteRow(this.rowIndex);	\"
				$mString .= "<tr height=27 bgcolor=#ffffff onclick=\"spoit(this)\" ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);\" style='background: url(../images/dot.gif) repeat-x left bottom; '>
							<td class=table_td_white align=center style='padding:5px;'>
								<div style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'><img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif'></div>
							</td>
							<td class=table_td_white>".cut_str($db->dt[pname],30)."<br>".number_format($db->dt[sellprice])."</td>
							<td><input type='hidden' name='rpid[]' value='".$db->dt[id]."'></td>
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
CREATE TABLE IF NOT EXISTS `shop_freegift` (
  `fg_ix` int(6) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `mall_ix` varchar(32) DEFAULT NULL COMMENT '프론트전시구분값',
  `fg_title` varchar(255) DEFAULT NULL COMMENT '사은품 행사명',
  `fg_use_sdate` int(10) DEFAULT NULL COMMENT '시작일',
  `fg_use_edate` int(10) DEFAULT NULL COMMENT '종료일',
  `freegift_condition` varchar(2) DEFAULT NULL COMMENT '매출조건설정',
  `member_target` int(10) DEFAULT NULL COMMENT '회원조건',
  `md_mem_ix` varchar(32) NOT NULL COMMENT '총괄MD 코드',
  `disp` char(1) DEFAULT NULL COMMENT '사용여부',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`fg_ix`),
  KEY `mg_use_sdate_ix` (`fg_use_sdate`),
  KEY `mg_use_edate_ix` (`fg_use_edate`),
  KEY `disp_ix` (`disp`),
  KEY `regdate_ix` (`regdate`),
  KEY `SHOP_FREEGIFT_MALL_IX` (`mall_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='사은품 행사관리 정보' ;

CREATE TABLE IF NOT EXISTS `shop_freegift_display_relation` (
  `fdr_ix` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '키값',
  `fg_ix` int(4) unsigned zerofill NOT NULL DEFAULT '0000' COMMENT '사은행사 키값',
  `member_target` enum('A','G','M') NOT NULL DEFAULT 'A' COMMENT '회원조건 구부값',
  `r_ix` varchar(32) NOT NULL COMMENT '회원조건 키값',
  `vieworder` int(5) NOT NULL DEFAULT '0' COMMENT '노출순서',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '입력 flag',
  `regdate` datetime DEFAULT NULL COMMENT '등록일자',
  PRIMARY KEY (`fdr_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='사은행사 회원조건 정보'


CREATE TABLE IF NOT EXISTS `shop_freegift_product_relation` (
  `fpr_ix` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `pid` int(10) unsigned zerofill NOT NULL DEFAULT '0000000000' COMMENT '상품아이디',
  `fg_ix` int(6) NOT NULL COMMENT '메인전시정보 코드',
  `group_code` int(2) NOT NULL DEFAULT '1' COMMENT '그룹코드',
  `vieworder` int(5) NOT NULL DEFAULT '0' COMMENT '정렬순서',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '업데이트 구분값',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`fpr_ix`),
  KEY `group_code` (`group_code`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='사은품행사 상품정보 ';



CREATE TABLE `shop_freegift_product_group` (
  `fpg_ix` int(8) unsigned zerofill NOT NULL auto_increment COMMENT '키값',
  `group_name` varchar(100) NOT NULL default '' COMMENT '사은품 상품그룹명',
  `group_code` int(2) NOT NULL default '0' COMMENT '그룹코드',
  `sale_condition_s` int(10) default NULL COMMENT '매출조건 시작',
  `sale_condition_e` int(10) default NULL COMMENT '매출조건 끝',
  `event_amount_type` enum('1','2') default '1' comment '수량제한타입(1:무제한, 2:선착순)',
  `event_amount` int(10) default NULL COMMENT '선착순 인원',  
  `insert_yn` enum('Y','N') default 'Y' COMMENT '등록 flag',
  `is_display` enum('Y','N') default 'Y' COMMENT '전시여부',
  `editdate` datetime DEFAULT NULL COMMENT '수정일',
  `regdate` datetime default NULL COMMENT '등록일자',
  PRIMARY KEY  (`fpg_ix`)
) TYPE=MyISAM COMMENT='사은품행사 그룹정보'


 

*/
?>