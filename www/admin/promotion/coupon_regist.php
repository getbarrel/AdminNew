<?
include("../class/layout.class");
include_once("../logstory/class/sharedmemory.class");

$shmop = new Shared("b2c_coupon_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/_shared/";
$shmop->SetFilePath();
$coupon_data = $shmop->getObjectForKey("b2c_coupon_rule");
$coupon_data = unserialize(urldecode($coupon_data));

$db = new Database();

/////////////////////////////////////////////////////////////////////////////////////////////
// csrf Token 발행
$csrfToken = getCsrfToken();
setCsrfTokenInSess($csrfToken);
/////////////////////////////////////////////////////////////////////////////////////////////

$this_menu_code = md5($_SERVER["PHP_SELF"]);

//쿠폰 수정 모드 (복제 후 기존 쿠폰 삭제) 기존 쿠폰을 발급받은 회원이 있을 경우 프로세스 중지 처리
if ($_GET['act'] == 'copy' && $_GET['sub_mode'] == 'modify' && $_GET['publish_ix'] != '') {
    $sql = "select 				
				*
			from 				
				shop_cupon_regist cr 
			where 
				cr.publish_ix = '" . $_GET['publish_ix'] . "'
				";
    $db->query($sql);
    if ($db->total > 0) {
        echo "<script>alert('회원에게 발급된 쿠폰으로 전체 수정 불가능 합니다.');history.back();</script>";
        exit;
    }
}

if (!$publish_ix && $regist_ix) {
    $sql = "Select publish_ix from " . TBL_SHOP_CUPON_REGIST . " cr  where  regist_ix ='$regist_ix' ";
    $db->query($sql);
    $db->fetch();
    $publish_ix = $db->dt[publish_ix];
}

if ($publish_ix) {
    $sql = "Select c.*, cp.*,cmd.name from shop_cupon c, " . TBL_SHOP_CUPON_PUBLISH . " cp left join " . TBL_COMMON_MEMBER_DETAIL . " cmd on cp.mem_ix = cmd.code where c.cupon_ix=cp.cupon_ix and publish_ix ='$publish_ix' ";
    $db->query($sql);
    $db->fetch();

    $cupon_publish_info = $db;
    $mall_ix = $db->dt[mall_ix];

    $cupon_div = $db->dt[cupon_div];
    $cupon_use_div = $db->dt[cupon_use_div];
    $cupon_sale_type = $db->dt[cupon_sale_type];
    $cupon_acnt = $db->dt[cupon_acnt];
    $cupon_sale_value = $db->dt[cupon_sale_value];
    $round_position = $db->dt[round_position];
    $round_type = $db->dt[round_type];
    $haddoffice_rate = $db->dt[haddoffice_rate];
    $seller_rate = $db->dt[seller_rate];

    $publish_min = $db->dt[publish_min];
    $publish_max = $db->dt[publish_max];
    $publish_max_product = $db->dt[publish_max_product];

    $cupon_ix = $db->dt[cupon_ix];
    $use_date_type = $db->dt[use_date_type];
    $publish_condition_price = $db->dt[publish_condition_price];
    $publish_max_price = $db->dt[publish_max_price];
    $use_product_type = $db->dt[use_product_type];
    $mem_ix = $db->dt[mem_ix];
    $publish_date_differ = $db->dt[publish_date_differ];
    $publish_date_type = $db->dt[publish_date_type];
    $regist_date_type = $db->dt[regist_date_type];
    $regist_date_differ = $db->dt[regist_date_differ];
    $publish_limit_price = $db->dt[publish_limit_price];
    if ($act == 'copy') {
        $publish = "insert";
    } else {
        $publish = "update";
    }
    $publish_name = $db->dt[publish_name];
    $publish_desc = $db->dt[publish_desc];
    $disp = $db->dt[disp];
    $is_cs = $db->dt[is_cs];
    $is_use = $db->dt[is_use];
    $issue_type = $db->dt[issue_type];
    $issue_type_detail = $db->dt[issue_type_detail];

    $publish_type = $db->dt[publish_type];
    $cupon_use_sdate = date("Y-m-d H:i:s", $db->dt[cupon_use_sdate]);
    $cupon_use_edate = date("Y-m-d H:i:s", $db->dt[cupon_use_edate]);
    $editdate = $db->dt[editdate];
    $regdate = $db->dt[regdate];

    if ($publish_type == 1) {
        $mem_id = $db->dt[name] . "(" . $db->dt[id] . ")";
    }

    $use_sdate = $db->dt[use_sdate];
    $use_edate = $db->dt[use_edate];
    $buy_point = $db->dt[buy_point];

    $discount_use_yn = $db->dt[discount_use_yn];
    $is_except = $db->dt[is_except];
    $overlap_use_yn = $db->dt[overlap_use_yn];
    $regist_count = $db->dt[regist_count];
    $payment_method = explode("|", $db->dt[payment_method]);
} else {

    $use_date_type = "3";
    $use_product_type = "1";
    $publish = "insert";
    $publish_type = "2";
    $issue_type = "1";
    $publish_condition_price = 0;
    $publish_max_price = 0;
    $publish_limit_price = 0;
    $cupon_sale_type = 1;
    //기본으로 노출 2014-07-15 Hong
    $cupon_use_sdate = date("Y-m-d 00:00:00");
    $cupon_use_edate = date("Y-m-d 23:59:59");
    $use_sdate = date("Y-m-d 00:00:00");
    $use_edate = date("Y-m-d 23:59:59");
}

$Script = "
<style type='text/css'>
  div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:0px;}
  div#drop_relation_product.hover { width:97%;*width:100%;border:5px dashed #aaa; background:#efefef; }
  table.tb {width:100%;cursor:move;}
</style>
<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.core.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' src='/js/ui/jquery.ui.mouse.js'></script>

<script type='text/javascript' src='/admin/js/jquery.event.drag-2.2.js'></script>
<script type='text/javascript' src='/admin/js/jquery.event.drag.live-2.2.js'></script>
<script type='text/javascript' src='/admin/js/jquery.event.drop-2.2.js'></script>
<script type='text/javascript' src='/admin/js/jquery.event.drop.live-2.2.js'></script>
<script type='text/javascript' src='/admin/js/jquery.multisortable.js'></script>

<script type='text/javascript' src='/admin/js/ms_productSearch.js'></script>
<script language='javascript' src='../search.js'></script>
<script language='javascript' src='../include/DateSelect.js'></script>
<script language='javascript' src='cupon.js'></script>
<script type='text/javascript' src='relationAjax.js'></script>
<script  id='dynamic'></script>
<script language=javascript>
";

if ($publish == "update") {
    $Script .= "
	$(document).ready(function(){
		$('form[name=form_cupon] :input').prop('disabled', true);
		$('input[name=publish_name]').prop('disabled', false);
		$('input[name=publish_desc]').prop('disabled', false);
		$('input[name=is_use]').prop('disabled', false);
		$('input[name=discount_use_yn]').prop('disabled', false);
		$('input[name=overlap_use_yn]').prop('disabled', false);
		$('input[name=regist_count]').prop('disabled', false);
		$('input.payment_method').prop('disabled', false);
		$('#disp_area :input').prop('disabled', false);
		$('form[name=form_cupon] :input[type=image]').prop('disabled', false);
		$('form[name=form_cupon] :input[type=hidden]').prop('disabled', false);
        $('#cid0').prop('disabled', false);
        $('#cid1').prop('disabled', false);
        $('#cid2').prop('disabled', false);
        $('#cid3').prop('disabled', false);		
        $('input[name^=category]').prop('disabled', false);
        $('input[name=use_product_type]').prop('disabled', false);
        $('input[name=is_except]').prop('disabled', false);
	});

	";
}

$Script .= "
function MoveSelectBox2(type,level_ix){
	if(type == 'ADD'){
		$('#gp_list option:selected').each(function(){
			$('#select_gp_list').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
			var selected_value = $(this).val();
			$('#gp_list option').each(function(){
				if($(this).val() == selected_value){
					$(this).remove();
				}
			});
		});
	}else{
		$('#select_gp_list option:selected').each(function(){
			$('#gp_list').append('<option value='+$(this).val()+' selected>'+$(this).html()+'</option>');
			var selected_value = $(this).val();
			$('#select_gp_list option').each(function(){
				if($(this).val() == selected_value){
					$(this).remove();
				}
			});
		});
	}

}

function CheckCuponInfo(frm){
	
	if(frm.publish_name.value == ''){
		alert('쿠폰명을 입력해주시기 바랍니다');
		return false;
	}

	if(frm.cupon_div.value == ''){
		alert('쿠폰 구분이 선택되지 않았습니다. 쿠폰구분을 선택해주세요');
		//'쿠폰종류가 선택되지 않았습니다. 쿠폰종류를 선택해주세요'
		//frm.cupon_ix.focus();
		return false;
	}

	if(frm.use_date_type[1].checked){
		if(frm.publish_date_differ.value.length < 1){
			alert(language_data['cupon_publish.php']['B'][language]);//'발행일로부터의 사용기간을 입력해주세요'
			frm.publish_date_differ.focus();
			return false;
		}
	}else if(frm.use_date_type[2].checked){
		if(frm.regist_date_differ.value.length < 1){
			alert(language_data['cupon_publish.php']['C'][language]);//'등록일로부터의 사용기간을 입력해주세요'
			frm.regist_date_differ.focus();
			return false;
		}
	}



	if(frm.publish_condition_price.value.length < 1){
		alert(language_data['cupon_publish.php']['D'][language]);//'결제가격 조건을 입력해 주세요 '
		frm.publish_condition_price.focus();
		return false;
	}

	if(frm.publish_type.value == '1'){
		
		var selected_result_member_cnt = 0;

		$('#selected_result_member option').each(function(){
			$(this).attr('selected', 'selected');
			selected_result_member_cnt++;
		});

		if(selected_result_member_cnt < 1){
			alert('지정발행의 경우 사용자를 선택하셔야 합니다.');
			return false;
		}
	}
	

	if(frm.publish_type.value == '4'){
		var selected_result_group_cnt = 0;

		$('#selected_result_group option').each(function(){
			$(this).attr('selected', 'selected');
			selected_result_group_cnt++;
		});

		if(selected_result_group_cnt < 1){
			alert('그룹이 한개 이상 선택되어야 합니다.');
			return false;
		}
	}

	return true;
}


function init(){
 
}


function categoryadd()
{
	var ret;
	var str = new Array();
	var dupe_bool = false;
	var obj = $('form[name=form_cupon]').find('select[class^=cid]');
	var admin_level = '" . $_SESSION["admininfo"]["admin_level"] . "';

	if(admin_level == 8){
		if($('input[type=radio][name=basic]').length > 0){
			alert('카테고리 입력은 한개만 가능합니다. ');
			return false;
		}
	}

	obj.each(function(index){
		if($(this).find('option:selected').val()){
			str[str.length] =  $(this).find('option:selected').text();
			ret = $(this).find('option:selected').val();
		}
	});

	if (!ret){
		alert(language_data['goods_input.php']['A'][language]);//'카테고리를 선택해주세요'
		return;
	}

	var cate = $('input[name^=display_category]');//document.getElementsByName('display_category[]'); // 호환성 kbk

	cate.each(function(){
		if(ret == $(this).val()){
			dupe_bool = true;
			alert(language_data['goods_input.php']['B'][language]);
			//'이미등록된 카테고리 입니다.'
			return;
		}
	});
	if(dupe_bool){
		return ;
	}

	var obj = $('#objCategory');
 

	obj.append(\"<tr id='num_tr' height=30 class=''><td><input type=hidden name=category[] id='_category' value='\" + ret + \"' style='display:none'><input type=hidden name=depth[] id='_depth' value='\" + $('form[name=form_cupon]').find('input[name=selected_depth]').val() + \"' style='display:none'></td><td></td><td > \"+str.join(\" > \")+\" </td><td align=right style='padding:5px 25px 5px 5px;'><img src='../images/" . $_SESSION["admininfo"]["language"] . "/btc_del.gif' border=0 onClick=\\\" $(this).closest('tr').remove();\\\" style='cursor:point;'></td></tr>\");
	 
}
 
function brandadd()
{
	var ret;

	var obj_key = document.form_cupon.b_ix;
	var obj_value = document.form_cupon.brand_name;

	if (!obj_key.value){
		alert('브랜드를 선택해주세요');
		return;
	}

	var brnad = document.all._brand;
	for(i=1;i < brnad.length;i++){
		if(obj_key.value == brnad[i].value){
			alert('이미 등록된 브랜드 입니다.');
			return;
		}
	}

	var obj = document.getElementById('objBrand');
	var otr_num=obj.getElementsByTagName('TR').length;
	oTr = obj.insertRow(otr_num);
	oTr.id = 'num_tr';
	oTr.height = \"23\";
	oTd = oTr.insertCell(0);
	if(window.addEventListener) oTd.setAttribute('class','table_td_white small');
	else oTd.className = 'table_td_white small';
	oTd.innerHTML = \"<input type=text name=brand[] id='_brand' value='\" + obj_key.value + \"' style='display:none'>\";
	oTd = oTr.insertCell(1);
	if(window.addEventListener) oTd.setAttribute('class','table_td_white small');
	else oTd.className = 'table_td_white small';
	oTd = oTr.insertCell(2);
	oTd.id = \"currPosition\";
	if(window.addEventListener) oTd.setAttribute('class','table_td_white small');
	else oTd.className = 'table_td_white small ';
	oTd.innerHTML = obj_value.value;
	oTd = oTr.insertCell(3);
	if(window.addEventListener) oTd.setAttribute('class','table_td_white small');
	else oTd.className = 'table_td_white small';
	oTd.innerHTML = \" <a href='javascript:void(0)' onClick='brand_del(this.parentNode.parentNode)'><img src='../images/i_close.gif' border=0></a>\";
}

function category_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory');
	obj.deleteRow(idx);
}

function brand_del(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objBrand');
	obj.deleteRow(idx);
}

function loadCategory(obj,target) {
	var trigger = obj.find('option:selected').val();
	var form = obj.closest('form').attr('name');
	var depth = obj.attr('depth');//sel.getAttribute('depth');
	$('form[name=form_cupon]').find('input[name=selected_depth]').val(trigger) ;
	$('form[name=form_cupon]').find('input[name=selected_depth]').val(depth) ;

	$.ajax({ 
			type: 'GET', 
			data: {'return_type': 'json', 'form':form, 'trigger':trigger, 'depth':depth, 'target':target},
			url: '../product/category.load.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(datas){
				$('select[class=cid]').each(function(){
					if(parseInt($(this).attr('depth')) > parseInt(depth)){
						$(this).find('option').not(':first').remove();
					}
				});
				 
				if(datas != null){
					$.each(datas, function(i, data){ 
							$('select[name='+target+']').append(\"<option value='\"+data.cid+\"'>\"+data.cname+\"</option>\");
					});  
				}
			} 
		}); 
 
}
 
function ChangeDisplaySubType(obj, group_code, selected_value){
	obj.parent().parent().find('div[class^=goods_auto_area]').hide();
	$('DIV#goods_display_sub_area_'+selected_value).show();
}

function ChangeDisplaySubTarget(obj, group_code, selected_value){
	$('div.display_sub_target_area').hide();
	$('DIV#display_sub_target_area_'+selected_value).show();
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

function changeCouponDiv(obj, target){
	var cupon_div = obj.find('option:selected').val(); 
	
	$.ajax({ 
			type: 'GET', 
			data: {'act': 'get_coupon', 'return_type': 'json', 'cupon_div':cupon_div, 'csrfToken' : '" . $csrfToken . "'},
			url: './cupon.act.php',  
			dataType: 'json', 
			async: true, 
			beforeSend: function(){ 
				
			},  
			success: function(datas){
				//alert(datas);
				$('select[name='+target+']').each(function(){
						$(this).find('option').not(':first').remove();
				});
				 
				if(datas != null){
					$.each(datas, function(i, data){ 
							$('select[name='+target+']').append(\"<option value='\"+data.cupon_ix+\"'>\"+data.cupon_kind+\"</option>\");
					});  
				}
//				if(cupon_div == 'C'){
//					$('input[id^=use_product_type]').attr('disabled', true);
//					$('input[id^=use_product_type_1]').attr('disabled', false);
//					$('input[id^=use_product_type_1]').attr('checked', true);
//				}else{
//					$('input[id^=use_product_type]').attr('disabled', false);
//				}


			} 
		}); 
}

function showDiscountType(type){
    var mall_ix = $('select[name=mall_ix] :selected').val();
	if(type == 1){
	    $('.devBenefitArea').show();
		$('.discount_txt').html('할인율(%)');
		$('.discount_txt2').html('%');
		$('.discount_txt3').show();
	
	}else if(type == 2){
	    $('.devBenefitArea').show();
	    var unitText = '할인율(<span class=devUnit>원</span>)';
	    if(mall_ix == '20bd04dac38084b2bafdd6d78cd596b2'){
	        unitText = '할인율(<span class=devUnit>$</span>)';
	    }else{
	        unitText = '할인율(<span class=devUnit>원</span>)';
	    }
		$('.discount_txt').html(unitText);
		$('.discount_txt2').html(unitText);
		$('.discount_txt3').hide();
	}else if(type == 3){
	    $('.devBenefitArea').hide();
	}
}


$(document).ready(function(){
    showDiscountType('$cupon_sale_type');      
    
    $('input[name=cupon_div]:checked').trigger('click');
    
    $('select[name=mall_ix]').on('change',function(){
        var chkMallIx = $(this).val();
        changeUnit(chkMallIx);
    });
    
    var selectMallIx = $('select[name=mall_ix] option:selected').val();
    changeUnit(selectMallIx);
    function changeUnit(chkMallIx){
        if(chkMallIx == '20bd04dac38084b2bafdd6d78cd596b2'){
            $('.devUnit').text('$');
        }else{
            $('.devUnit').text('원');
        }
    }
});
</script>";


$vdate = date("YmdHis", time());
$today = date("Y/m/d-H:i:s", time());
$vyesterday = date("Y/m/d-H:i:s", time() - 84600);
$voneweekafter = date("Y/m/d-H:i:s", time() + 84600 * 7);
$vtwoweekafter = date("Y/m/d-H:i:s", time() + 84600 * 14);
$vfourweekafter = date("Y/m/d-H:i:s", time() + 84600 * 28);
$vyesterday = date("Y/m/d-H:i:s", mktime(0, 0, 0, substr($vdate, 4, 2), substr($vdate, 6, 2), substr($vdate, 0, 4)) + 60 * 60 * 24);
$voneweekafter = date("Y/m/d-H:i:s", mktime(0, 0, 0, substr($vdate, 4, 2), substr($vdate, 6, 2), substr($vdate, 0, 4)) + 60 * 60 * 24 * 7);
$v15after = date("Y/m/d-H:i:s", mktime(0, 0, 0, substr($vdate, 4, 2), substr($vdate, 6, 2), substr($vdate, 0, 4)) + 60 * 60 * 24 * 15);
$vfourweekafter = date("Y/m/d-H:i:s", mktime(0, 0, 0, substr($vdate, 4, 2), substr($vdate, 6, 2), substr($vdate, 0, 4)) + 60 * 60 * 24 * 28);
$vonemonthafter = date("Y/m/d-H:i:s", mktime(0, 0, 0, substr($vdate, 4, 2) + 1, substr($vdate, 6, 2) + 1, substr($vdate, 0, 4)));
$v2monthafter = date("Y/m/d-H:i:s", mktime(0, 0, 0, substr($vdate, 4, 2) + 2, substr($vdate, 6, 2) + 1, substr($vdate, 0, 4)));
$v3monthafter = date("Y/m/d-H:i:s", mktime(0, 0, 0, substr($vdate, 4, 2) + 3, substr($vdate, 6, 2) + 1, substr($vdate, 0, 4)));


$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' valign=top>
	<tr>
		<td align='left'> " . GetTitleNavigation("쿠폰 기본 정보", "프로모션(마케팅)/전시 > 쿠폰관리 > 쿠폰 생성") . "</td>
	</tr>
	<tr>
		<td align='left' style='padding:3px 0px;'>" . colorCirCleBox("#efefef", "100%", "<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b>쿠폰 기본 정보</b></div>") . "</td>
	</tr>
	</table>

	<form name='form_cupon' onsubmit='return CheckCuponInfo(this)' method='post'  action='cupon.act.php' target='act'>
	<input type=hidden name='act' value='" . $publish . "'>
	<input type=hidden name='mmode' value='" . $mmode . "'>
	<input type=hidden name='publish_ix' value='" . $publish_ix . "'>
	<input type=hidden name='sub_mode' value='" . $_GET['sub_mode'] . "'>
	<input type=hidden name='cupon_ix' value='" . $cupon_ix . "'>
	<input type=hidden name='view_type' value='" . $view_type . "'>		
	<input type=hidden name='publish_tmp_ix' value='" . $publish_tmp_ix . "'>
	<input type=hidden name='csrfToken' value='" . $csrfToken . "'>
	<input type=hidden name='is_include' value='1'>

	<table width='100%' cellpadding=3 cellspacing=0 border='0' class='input_table_box'>
	<col width=18%>
	<col width=*>";
if ($_SESSION["admin_config"][front_multiview] == "Y") {
    $Contents .= "
    <tr>
        <td class='search_box_title' > 글로벌 회원 구분</td>
        <td class='search_box_item' >" . GetDisplayDivision($mall_ix, "select") . " </td>
    </tr>";
}
$Contents .= "
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>쿠폰 이름 <img src='" . $required3_path . "'></b></td>
		<td class='input_box_item'><input type='text' id='publish_name' name='publish_name' value='" . $publish_name . "' class='textbox' style='height: 18px; width: 370px;'  align='absmiddle'>
		<span class='blue'>* 고객에게 노출되는 명칭입니다</span></td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>쿠폰 설명</b></td>
		<td class='input_box_item'><input type='text' id='publish_desc' name='publish_desc' value='" . $publish_desc . "' class='textbox' style='height: 18px; width: 370px;'  align='absmiddle'>
		<span class='blue'>* 관리자에게만 노출되는 명칭입니다</span></td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>쿠폰 종류 <img src='" . $required3_path . "'></b></td>
		<td class='input_box_item'>";

$coupon_str = "";

if (empty($cupon_div)) {
    $cupon_div = "G";
}

foreach ($coupon_data[default_coupon_kind] as $key => $value) {
    switch ($value) {
        case 'G' :
            $coupon_str = "상품쿠폰";
            break;
        case 'C' :
            $coupon_str = "장바구니쿠폰";
            break;
        case 'D' :
            $coupon_str = "배송비쿠폰";
            break;
    }

    $Contents .= "<input type='radio' name='cupon_div' id='cupon_div_" . $value . "' value='" . $value . "' " . CompareReturnValue($value, $cupon_div, "checked") . " validation=true title='쿠폰종류' " . ($act == "update" ? "disabled" : "") . "> <label for='cupon_div_" . $value . "' >" . $coupon_str . "</label> ";
}


$Contents .= "
		<span class='blue'>* 쿠폰 기본설정 매뉴의 쿠폰 종류 항목에서 선택한 쿠폰종류가 노출됩니다</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>쿠폰 사용범위 <img src='" . $required3_path . "'></b></td>
		<td class='input_box_item'>
			 <input type='radio' name='cupon_use_div' id='cupon_use_div_a' value='A' " . ($cupon_use_div == "A" || $cupon_use_div == "" ? "checked" : "") . " validation=true title='쿠폰종류'><label for='cupon_use_div_a' >PC + Mobile</label>
			 <input type='radio' name='cupon_use_div' id='cupon_use_div_g' value='G' " . CompareReturnValue("G", $cupon_use_div, "checked") . " validation=true title='쿠폰종류'><label for='cupon_use_div_g' >PC 전용</label>
			 <input type='radio' name='cupon_use_div' id='cupon_use_div_m' value='M' " . CompareReturnValue("M", $cupon_use_div, "checked") . " validation=true title='쿠폰종류'><label for='cupon_use_div_m' >Mobile 전용</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>쿠폰 사용여부 <img src='" . $required3_path . "'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='is_use' id='is_use_1'  align='middle' value='1' " . ($is_use == '1' || $is_use == '' ? "checked" : "") . "><label for='is_use_1' class='green'>사용</label> 
			<input type='radio' name='is_use' id='is_use_0'  align='middle' value='0' " . ($is_use == '0' ? "checked" : "") . "><label for='is_use_0' class='green'>미사용(발급중지)</label> 
			<input type='radio' name='is_use' id='is_use_3'  align='middle' value='3' " . ($is_use == '3' ? "checked" : "") . "><label for='is_use_3' class='green'>사용불가</label> 
			<span class='blue'>* 사용불가 설정 시, 고객이 이미 발급받은 쿠폰이라하더라도 사용할 수 없습니다</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>기획할인 상품에 쿠폰 적용 <img src='" . $required3_path . "'></b></td>
		<td>
			<label><input type='radio' name='discount_use_yn' align='middle' value='Y' " . ($discount_use_yn == 'Y' ? "checked" : "") . ">적용</label> 
			<label><input type='radio' name='discount_use_yn' align='middle' value='N' " . ($discount_use_yn == 'N' || $discount_use_yn == '' ? "checked" : "") . ">적용안함</label> 
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b><span id='overlap_use_yn_cart' style='" . ($cupon_div != 'G' ? "display:none;" : "") . "'>장바구니</span><span id='overlap_use_yn_product' style='" . ($cupon_div != 'C' ? "display:none;" : "") . "'>상품</span> 쿠폰 사용 <img src='" . $required3_path . "'></b></td>
		<td>
		    <label><input type='radio' name='overlap_use_yn' align='middle' value='Y' " . ($overlap_use_yn == 'Y' || $overlap_use_yn == '' ? "checked" : "") . ">적용</label>
			<label><input type='radio' name='overlap_use_yn' align='middle' value='N' " . ($overlap_use_yn == 'N' ? "checked" : "") . ">적용안함</label> 
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>쿠폰 발급 갯수 <img src='" . $required3_path . "'></b></td>
		<td>
			<input type=text class='textbox numeric point_color' validation='true' title='쿠폰 발급 갯수' name='regist_count' maxlength='20' style='width: 70px; filter: blendTrans(duration=0.5)'  align='absmiddle' value='" . (empty($regist_count) ? '1' : $regist_count) . "' /> 개
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>결제수단 설정</b></td>
		<td>";

$payment_method_list = array(ORDER_METHOD_CARD, ORDER_METHOD_VBANK, ORDER_METHOD_ICHE, ORDER_METHOD_PAYCO, ORDER_METHOD_KAKAOPAY, ORDER_METHOD_NPAY, ORDER_METHOD_TOSS);
foreach($payment_method_list as $_method){
    $Contents .= "
    <label>
        <input type='checkbox' class='payment_method' name='payment_method[]' value='".$_method."' " . CompareReturnValue($_method, $payment_method, "checked") . ">".getMethodStatus($_method)."
   </label>";
}

$Contents .= "
		</td>
	</tr>
	</table><br><br>";


$Contents .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' valign=top>
<tr>
	<td align='left'> " . GetTitleNavigation("쿠폰 혜택 설정", "프로모션(마케팅)/전시 > 쿠폰관리 > 쿠폰 혜택 설정") . "</td>
</tr>
<tr>
	<td align='left' style='padding:3px 0px;'>" . colorCirCleBox("#efefef", "100%", "<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b>쿠폰 혜택 설정</b></div>") . "</td>
</tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <tr>
    <td valign='top'>";


if ($regdate) {
    $Contents .= "
	  <div style='padding:5px;'>
	  쿠폰 등록일 / 수정일 : " . $regdate . " / " . $editdate . "
	  </div>";
}
$Contents .= "
      <table width='100%' border='0' cellpadding='5' cellspacing='1' class='input_table_box'>
        <col width='15%'>
		<col width='35%'>
		<col width='15%'>
		<col width='35%'>
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>쿠폰 혜택 <img src='" . $required3_path . "'></b></td>
			<td class='input_box_item' colspan='3' nowrap>
				<div style='padding-top: 5px;'>
                    <input type=radio id='cupon_sale_type1' name='cupon_sale_type' value=1 " . ($cupon_sale_type == "" || $cupon_sale_type == "1" ? "checked" : "") . " " . ($act == "update" ? ("1" != $cupon_sale_type ? "disabled" : "") : "") . "  onclick=\"showDiscountType(1)\"><label for='cupon_sale_type1'>정률할인(%)</label> 
                    <input type=radio id='cupon_sale_type2' name='cupon_sale_type' value=2 " . CompareReturnValue('2', $cupon_sale_type, ' checked') . " " . ($act == "update" ? ("2" != $cupon_sale_type ? "disabled" : "") : "") . "  onclick=\"showDiscountType(2)\"><label for='cupon_sale_type2'>정액할인(<span class='devUnit'>원</span>)</label>
                    <input type=radio id='cupon_sale_type3' name='cupon_sale_type' value=3 " . CompareReturnValue('3', $cupon_sale_type, ' checked') . " " . ($act == "update" ? ("3" != $cupon_sale_type ? "disabled" : "") : "") . "  onclick=\"showDiscountType(3)\"><label for='cupon_sale_type3'>전액할인</label>
                </div></br>

				<table cellpadding=0 cellspacing=0 class='input_table_box devBenefitArea' style='margin: 5px;'>
					<tr bgcolor='#ffffff'>
						<td class='input_box_title' style='padding-right: 15px;'>할인 부담</td>
						<td class='input_box_item'>
							<input type='radio' name='cupon_acnt' value='1' id='cupon_acnt_1' " . ($cupon_acnt == "" || $cupon_acnt == "1" ? "checked" : "") . " onclick=\"$('.seller_rate').hide();$('input[name=cupon_sale_value]').prop('readonly', false);\"><label for='cupon_acnt_1' >본사</label>
							<input type='radio' class='cupon_cart_hide' name='cupon_acnt' value='2' id='cupon_acnt_2' " . ($cupon_acnt == "2" ? "checked" : "") . " onclick=\"$('.seller_rate').show();$('input[name=cupon_sale_value]').prop('readonly', true);\" ><label for='cupon_acnt_2' class='cupon_cart_hide' style='padding-right: 15px;'  >본사 + 셀러 </label>
						</td>
					</tr>
					<tr bgcolor='#ffffff'>
						<td class='input_box_title discount_txt' style='padding-right: 15px;'>할인율(" . ($cupon_sale_type == "" || $cupon_sale_type == 1 ? "%" : "원") . ")</td>
						<td class='input_box_item'>
							<div style='margin-top:10px;padding-right: 10px;'>
								<input type=text class='textbox numeric point_color' validation='true' title='쿠폰적용가격 합계' name='cupon_sale_value' id='cupon_sale_value' maxlength='20' style='width: 70px; filter: blendTrans(duration=0.5)'  align='absmiddle' value='" . $cupon_sale_value . "' " . ($cupon_acnt == "" || $cupon_acnt == "1" ? "" : "readonly") . " /> 
								<span class='discount_txt2'>" . ($cupon_sale_type == "" || $cupon_sale_type == 1 ? "%" : "원") . "</span>

								<div class='seller_rate' style='" . ($cupon_acnt == "2" ? "" : "display:none;") . "float: right;padding-left: 10px;'>
									( 본사부담 : <input type=text class='textbox numeric' validation='true' title='본사부담' name='haddoffice_rate' id='haddoffice_rate' maxlength='10' style='width: 70px; filter: blendTrans(duration=0.5)'  align='absmiddle' value='" . $haddoffice_rate . "' " . ($act == "update" ? "readonly onclick=\"alert('수정하실 수 없는 항목입니다.');\"" : "") . " onkeyup=\"checkCouponPriceRate($(this));\">
									+ 셀러부담 : <input type=text class='textbox numeric' validation='true' title='셀러부담' name='seller_rate' id='seller_rate' maxlength='10' style='width: 70px; filter: blendTrans(duration=0.5)'  align='absmiddle' value='" . $seller_rate . "' " . ($act == "update" ? "readonly onclick=\"alert('수정하실 수 없는 항목입니다.');\"" : "") . " onkeyup=\"checkCouponPriceRate($(this));\"> )
								<div>
							</div>
						</td>
					</tr>
					<tr bgcolor='#ffffff' class='discount_txt3' " . ($cupon_sale_type == "" || $cupon_sale_type == 1 ? "" : "style='display:none;'") . ">
						<td class='input_box_title' style='padding-right: 15px;'>끝 단위 처리</td>
						<td class='input_box_item'>
							<div id='round_type_area_" . ($i + 1) . "' >
								<select name='round_position' id='round_position' > 
									<option value='1' " . ($round_position == 1 ? "selected" : "") . ">일 자리</option>
									<option value='2' " . ($round_position == 2 ? "selected" : "") . ">십 자리</option>
									<option value='3' " . ($round_position == 3 ? "selected" : "") . ">백 자리</option>
									<option value='-2' " . ($round_position == -2 ? "selected" : "") . ">소수점 3자리</option>
									<option value='-1' " . ($round_position == -1 ? "selected" : "") . ">소수점 2자리</option>
									<option value='0' " . ($round_position == 0 ? "selected" : "") . ">소수점 1자리</option>
								</select>
								<select name='round_type' id='round_type'  > 
									<option value='1' " . ($round_type == 1 ? "selected" : "") . ">반올림</option>
									<!--option value='2' " . ($round_type == 2 ? "selected" : "") . ">반내림</option-->
									<option value='3' " . ($round_type == 3 ? "selected" : "") . ">내림</option>
									<option value='4' " . ($round_type == 4 ? "selected" : "") . ">올림</option>
								</select>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor=#ffffff >
			<td class='input_box_title'> <b>쿠폰 혜택 제한 <img src='" . $required3_path . "'></b></td>
			<td class='input_box_item' colspan='3' nowrap>
				<table cellpadding=0 cellspacing=0 class='input_table_box' style='margin: 5px;'>
					<tr bgcolor='#ffffff' class='cupon_cart_hide'>
						<td class='input_box_title change_title' style='padding-right: 15px;'>최소 상품금액</td>
						<td class='input_box_item'>
							<input type='radio' name='publish_min' value='N' id='publish_min_n' " . ($publish_min == "N" || $publish_min == "" ? "checked" : "") . "><label for='publish_min_n' >제한 없음</label>
							<input type='radio' name='publish_min' value='Y' id='publish_min_y' " . ($publish_min == "Y" ? "checked" : "") . "><label for='publish_min_y' style='padding-right: 15px;'>제한 있음 </label> ( <input type='text' id='fprice' name='publish_condition_price' value='" . $publish_condition_price . "' class='textbox numeric' style='height: 18px; width: 70px; filter: blendTrans(duration=0.5)' onFocus=\"FIn(fprice,'#FFD323',0)\" onFocusOut=\"FOut(fprice,'',0)\"  align='absmiddle'> <span class='devUnit'>원</span> 이상 <span class='change_text'>상품</span>에만 적용 가능 )
						</td>
					</tr>					
					<tr bgcolor='#ffffff'>
						<td class='input_box_title change_title2' style='padding-right: 15px;'>최대 상품금액</td>
						<td class='input_box_item'>
							<input type='radio' name='publish_max_product' value='N' id='publish_max_product_n' " . ($publish_max_product == "N" || $publish_max_product == "" ? "checked" : "") . "><label for='publish_max_product_n' >제한 없음</label>
							<input type='radio' name='publish_max_product' value='Y' id='publish_max_product_y' " . ($publish_max_product == "Y" ? "checked" : "") . "><label for='publish_max_product_y' style='padding-right: 15px;'>제한 있음 </label> ( <input type='text' id='fprice' name='publish_max_price' value='" . $publish_max_price . "' class='textbox numeric' style='height: 18px; width: 70px; filter: blendTrans(duration=0.5)' onFocus=\"FIn(fprice,'#FFD323',0)\" onFocusOut=\"FOut(fprice,'',0)\"  align='absmiddle'> <span class='devUnit'>원</span> 미만 <span class='change_text'>상품</span>에만 적용 가능 )
						</td>
					</tr>
					<tr bgcolor='#ffffff'>
						<td class='input_box_title' style='padding-right: 15px;'>최대 할인금액</td>
						<td class='input_box_item'>
							<input type='radio' name='publish_max' value='N' id='publish_max_n' " . ($publish_max == "N" || $publish_max == "" ? "checked" : "") . "><label for='publish_max_n' >제한 없음</label>
							<input type='radio' name='publish_max' value='Y' id='publish_max_y' " . ($publish_max == "Y" ? "checked" : "") . "><label for='publish_max_y' style='padding-right: 15px;'>제한 있음 </label> ( 최대 <input type='text' id='fprice' name='publish_limit_price' value='" . $publish_limit_price . "' class='textbox numeric' style='height: 18px; width: 70px; filter: blendTrans(duration=0.5)' onFocus=\"FIn(fprice,'#FFD323',0)\" onFocusOut=\"FOut(fprice,'',0)\"  align='absmiddle'> <span class='devUnit'>원</span>까지 할인 가능 )
						</td>
					</tr>
				</table>
				<span class='blue'>* 쿠폰 기본설정 메뉴에서 상품금액의 기준을 설정할 수 있습니다</span></br>
				<span class='blue'>* 정액할인인 경우, 최소 상품금액은 할인금액(<span class='devUnit'>원</span>) 이상이어야 합니다</span>
			</td>
		</tr>
		<tr>
		  <td class='search_box_title' >  <b>쿠폰 발급 방식 <img src='" . $required3_path . "'></b></td>
		  <td class='search_box_item' style='padding: 10px;' colspan='3'>";
foreach ($_ISSUE_TYPE as $key => $value) {
    $issue_type_str = "";
    switch ($key) {
        case '4' :
            $issue_type_str = "<select name='issue_type_detail' style='margin-left: 5px;' >
											<option value='1' " . ($issue_type_detail == 1 && $issue_type == 4 ? "selected" : "") . ">회원가입 완료시</option>
											<option value='3' " . ($issue_type_detail == 3 && $issue_type == 4 ? "selected" : "") . ">기념일(생일) 시</option>
											<option value='4' " . ($issue_type_detail == 4 && $issue_type == 4 ? "selected" : "") . ">APP 최초 다운로드 시</option>
										</select>";
            break;
        case '2' :
            $issue_type_str = "<select name='issue_type_detail2' style='margin-left: 5px;' >
											<option value='1' " . ($issue_type_detail == 1 && $issue_type == 2 ? "selected" : "") . ">상품 상세에 쿠폰 노출</option>
											<option value='2' " . ($issue_type_detail == 2 && $issue_type == 2 ? "selected" : "") . ">상품 상세에 쿠폰 미노출</option>
										</select>";
            break;
    }

    $Contents .= "<div style='margin-bottom: 5px;'><input type='radio' name='issue_type' id='issue_type_" . $key . "' class='issue_type' value='" . $key . "' " . ($issue_type == $key ? "checked" : "") . " validation=true title='" . $value[text] . "'><label for='issue_type_" . $key . "' >" . $value[text] . "</label>" . $issue_type_str . " <span class='blue'>* " . $value["desc"] . "</span></div>";
}
$Contents .= "
		  </td>
		</tr>
		  <tr>
				<td class='input_box_title' nowrap ><b>쿠폰 발급 대상 <img src='" . $required3_path . "'></b></td>
				<td  class='search_box_item' style='padding:10px;' colspan=3><a name='publish_type'></a>
				<div style='padding-bottom:10px;'>";

foreach ($_PUBLISH_TYPE as $key => $value) {
    if ($key == 2) {
        $onclick_str = "$('#display_sub_target_area').hide();$('#display_sub_target').hide();";
    } else if ($key == 1) {
        $onclick_str = "$('#display_sub_target_area').show();$('#display_sub_target').show();ChangeDisplaySubTarget($(this),1,'M');";
    } else if ($key == 3) {
        $onclick_str = "$('#display_sub_target_area').hide();$('#display_sub_target').hide();";
    } else if ($key == 4) {
        $onclick_str = "$('#display_sub_target_area').show();$('#display_sub_target').show();ChangeDisplaySubTarget($(this),1,'G');";
    } else if ($key == 5) {
        $onclick_str = "$('#display_sub_target_area').hide();$('#display_sub_target').hide();";
    }

    $Contents .= "<div style='margin-bottom: 5px;'><input type='radio' name='publish_type' id='publish_type_" . $key . "' onFocus='this.blur();' align='middle' value='" . $key . "'  " . ($publish_type == $key ? "checked" : "") . " onclick=\"" . $onclick_str . "\" ><span style='padding-left:2px' class='helpcloud' help_width='280' help_height='10' help_html='" . $value["desc"] . "'><label for='publish_type_" . $key . "' class='green'>" . $value["text"] . "</label></div>
	</span>";
}
$Contents .= "
						<br>
					</div>
					<div id='display_sub_target_area' " . ($publish_type == "1" || $publish_type == "4" ? "style='display:block' " : "style='display:none'") . " >
						<div class='display_sub_target_area'  id='display_sub_target_area_G' " . ($publish_type == "4" ? "style='display:block' " : "style='display:none'") . " >
							<table   border='0'  cellpadding=0 cellspacing=0 >								
									<tr>
										<td width='300'>
											<table  border='0' cellpadding=0 cellspacing=0 align='center'>
												<tr align='left'>
													<td width='100'>   
														<input type=text class=textbox name='search_text'  id='search_text' style='width:210px;margin-bottom:2px;' value=''>  
													</td>
													<td align='center'>
														<img src='../v3/images/" . $_SESSION["admininfo"]["language"] . "/btn_search.gif'  onclick=\"SearchInfo('G',$('DIV#display_sub_target_area_G'), 'group');\"  style='cursor:pointer;'> 
														<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택'  onclick=\"SelectedAll($('DIV#display_sub_target_area_G #search_result_group option'),'selected')\"  style='cursor:pointer;'/>
													</td>
													</tr>
												<tr>
													<td colspan='2' >
														<select name='search_result[]' class='search_result' id='search_result_group'  style=' width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' multiple>";

$sql = "SELECT gi.gp_ix, gi.gp_name 
																	FROM shop_groupinfo gi
																	where disp='1' and use_coupon_yn='Y'";
$db->query($sql);
$basic_groups = $db->fetchall();

for ($j = 0; $j < count($basic_groups); $j++) {
    $Contents .= "<option value='" . $basic_groups[$j][gp_ix] . "' ondblclick=\"$(this).remove();\">" . $basic_groups[$j][gp_name] . "</option>";
}
$Contents .= "
														</select>
													</td>
												</tr>
											</table>
										</td>
										<td align='center' width=80 style='padding-left:40px;text-align:center;'>
											<div class='float01 email_btns01' >
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
if ($publish_type == "4") {
    $sql = "SELECT gi.gp_ix, gi.gp_name 
																		FROM shop_groupinfo gi, shop_cupon_publish_config cpc 
																		where gi.gp_ix = cpc.r_ix and cpc.publish_type = '" . $publish_type . "' and publish_ix = '" . $publish_ix . "'  ";
    $db->query($sql);
    $selected_groups = $db->fetchall();


    for ($j = 0; $j < count($selected_groups); $j++) {
        $Contents .= "<option value='" . $selected_groups[$j][gp_ix] . "' ondblclick=\"$(this).remove();\" selected>" . $selected_groups[$j][gp_name] . "</option>";
    }
}

$Contents .= "
														</select>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
						</div>
						<div class='display_sub_target_area'  id='display_sub_target_area_M'  " . (($publish_type == "1") ? "style='display:block' " : "style='display:none'") . " >
							<table   border='0'  cellpadding=0 cellspacing=0 >								
									<tr>
										<td width='300'>
											<table  border='0' cellpadding=0 cellspacing=0 align='center'>
												<tr align='left'>
													<td width='100'>   
														<input type=text class=textbox name='search_text'  id='search_text' style='width:210px;margin-bottom:2px;' value=''>  
													</td>
													<td align='center'>
														<img src='../v3/images/" . $_SESSION["admininfo"]["language"] . "/btn_search.gif'  onclick=\"SearchInfo('M',$('DIV#display_sub_target_area_M'), 'member');\"  style='cursor:pointer;'> 
														<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택'  onclick=\"SelectedAll($('DIV#display_sub_target_area_M #search_result_member option'),'selected')\"  style='cursor:pointer;'/>
													</td>
													</tr>
												<tr>
													<td colspan='2' >
														<select name='search_result[]' class='search_result' id='search_result_member'  style=' width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' multiple>											
														</select>
													</td>
												</tr>
											</table>
										</td>
										<td align='center' width=80 style='padding-left:40px;text-align:center;'>
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
														<select name='selected_result[member][]' class='selected_result' id='selected_result_member'  style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='' validation=false title='회원' multiple>";

if ($publish_type == "1") {
    $sql = "SELECT cmd.code, AES_DECRYPT(UNHEX(cmd.name),'" . $db->ase_encrypt_key . "') as name, cu.id as user_id 
																		FROM common_user cu, common_member_detail cmd, shop_cupon_publish_config cpc 
																		where cu.code = cmd.code and cmd.code = cpc.r_ix and cpc.publish_type = '" . $publish_type . "'  and publish_ix = '" . $publish_ix . "'  ";
    $db->query($sql);
    $selected_members = $db->fetchall();

    for ($j = 0; $j < count($selected_members); $j++) {
        $Contents .= "<option value='" . $selected_members[$j][code] . "' ondblclick=\"$(this).remove();\" selected>" . $selected_members[$j][name] . "(" . $selected_members[$j][user_id] . ")</option>";
    }
}

$Contents .= "
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

		<tr bgcolor='#ffffff'>
          <td class='input_box_title'>  <b>쿠폰 사용기간 <img src='" . $required3_path . "'></b></td>
          <td class='input_box_item' style='padding:10px;' colspan=3>
          	<table cellpadding=0 cellspacing=2 border=0 >
				<tr>
					<td valign=middle nowrap>
					<input type='radio' name='use_date_type' id='use_date_type_3' onFocus='this.blur();' align='absmiddle' value=3 " . ($use_date_type == 3 ? "checked" : "") . "><label class='blue' for='use_date_type_3'>사용기간지정</label>&nbsp;
					</td>
					<TD nowrap>
						" . search_date('use_sdate', 'use_edate', $use_sdate, $use_edate, 'Y', 'A') . "                        
                    </TD>
			</table>
			<div style='padding:3px 0px 3px 0px'>
				<input type='radio' name='use_date_type' id='use_date_type_1'  onFocus='this.blur();' align='absmiddle' value=1 " . ($use_date_type == 1 ? "checked" : "") . ">
				<label class='blue' for='use_date_type_1'>발행일 기준</label>&nbsp;( 관리자가 발행한 날로부터 <input type='text' id='fuse_date1' name='publish_date_differ' value='" . ($use_date_type == 1 ? $publish_date_differ : "0") . "' class='textbox' maxlength='3' style='filter: blendTrans(duration=0.5); height: 18px; width: 20px'  onFocus=\"FIn(fuse_date1,'#FFD323',0)\" onFocusOut=\"FOut(fuse_date1,'',0)\"   align='absmiddle'>
				<select name='publish_date_type'>
					<option value=3 " . ($publish_date_type == 3 && $use_date_type == 1 ? "selected" : "") . ">일</option>
					<option value=2 " . ($publish_date_type == 2 && $use_date_type == 1 ? "selected" : "") . ">개월</option>
					<option value=1 " . ($publish_date_type == 1 && $use_date_type == 1 ? "selected" : "") . ">년</option>
				</select> 이내 사용 가능)<br>
			</div>
          	<input type='radio' name='use_date_type' id='use_date_type_2'  onFocus='this.blur();' align='absmiddle' value=2 " . ($use_date_type == 2 ? "checked" : "") . ">
          	<label class='blue' for='use_date_type_2'>발급일 기준</label>&nbsp;( 사용자가 발급받은 날로부터 <input type='text' id='fuse_date2' name='regist_date_differ' value='" . ($use_date_type == 2 ? $regist_date_differ : "0") . "' class='textbox' maxlength='3' style='filter: blendTrans(duration=0.5); height: 18px; width: 20px' onFocus=\"FIn(fuse_date2,'#FFD323',0)\" onFocusOut=\"FOut(fuse_date2,'',0)\" align='absmiddle'>
			 <select name='regist_date_type'>
				<option value=3 " . ($regist_date_type == 3 && $use_date_type == 2 ? "selected" : "") . ">일</option>
				<option value=2 " . ($regist_date_type == 2 && $use_date_type == 2 ? "selected" : "") . ">개월</option>
				<option value=1 " . ($regist_date_type == 1 && $use_date_type == 2 ? "selected" : "") . ">년</option>
			</select> 이내 사용 가능)<br>
          	<div style='padding:3px 0px 3px 0px'>
			<input type='radio' name='use_date_type' id='use_date_type_9'  onFocus='this.blur();' align='absmiddle' value=9 " . ($use_date_type == 9 ? "checked" : "") . ">
          	<label class='blue' for='use_date_type_9'>제한 없음(무기한)</label>
			</div>

			</br>
			<div style='background-color: #efefef;padding: 10px;'>
				<img src='../image/emo_3_15.gif' align=absmiddle > <b>쿠폰 사용기간 설정 가이드</b></br>
					<span>-쿠폰 사용기간이란, 쿠폰을 보유한 사용자가 해당 쿠폰을 상품에 적용할 수 있는 기간을 의미합니다. </span></br>
					<span>-발행일이란 관리자가 어드민에서 쿠폰을 생성한 날을 의미하며, 발급일이란 사용자가 쿠폰을 취득(ex. 다운로드, 시리얼번호 등록 등)한 날을 의미합니다. </span></br>
					<span>-관리자 수동 발급이나 특정조건 자동 발행 쿠폰의 경우, 발행일과 발급일은 동일합니다. </span></br>
					<span>-고객 다운로드 쿠폰의 경우, 사용자가 쿠폰을 다운로드 받은 당일이 발급일이 되므로 발행일과 발급일이 상이할 수 있습니다. </span></br>
					<span class='red'>-오프라인 상품권 쿠폰의 경우, 사용자가 시리얼 넘버를 입력하여 등록한 당일이 발급일이 되므로 발행일과 발급일이 상이할 수 있습니다.</span> </br>
			</div>
			</br>
			</td>
        </tr>
        
        
        
        
        
        
        
        
        
        <tr >
          <td class='input_box_title'>  <b>사용가능상품 <img src='" . $required3_path . "'></b></td>
		  <td class='input_box_item' style='padding:5px;' colspan=3>
		  
		  
		  <!-- AREA 1 -->
			  	<input type='radio' name='use_product_type' id='use_product_type_1' 
			  	       onclick=\"$('#goods_display_sub_area_B').hide();$('#div_productSearchBox').hide();$('#relation_category_area').hide();$('#goods_display_sub_area_S').hide();$('#is_except_area').show();\" 
			  	       onFocus='this.blur();' align='absmiddle' value=1 " . ($use_product_type == 1 ? "checked" : "") . ">
			  	<label class='blue' for='use_product_type_1'>전체 상품에 발행 합니다</label>
			  	<br>
		  <!-- AREA 1 -->
		  
		  
		  <!-- AREA 2 -->            	  	
			  	<div  style='display:none'>
			  	<input type='radio' name='use_product_type' id='use_product_type_4' 
			  	       onclick=\"$('#goods_display_sub_area_B').show();
			  	       $('#relation_category_area').hide();
			  	       $('#goods_display_sub_area_S').hide();
			  	       $('#div_productSearchBox').hide();
                       $('#search_text').prop('disabled', false);
                       $('#search_result_brand').prop('disabled', false);
                       $('#selected_result_brand').prop('disabled', false);
			  	       \" 
			  	       onFocus='this.blur();' align='absmiddle' value=4 " . ($use_product_type == 4 ? "checked" : "") . ">
			  	<label class='blue' for='use_product_type_4'> 특정 브랜드(에,를) 속한 상품에 발행 합니다. </label>
			  	<br>
			  	</div>
			    <div class='goods_auto_area'  id='goods_display_sub_area_B' style='padding:10px 5px 10px 5px;" . (($use_product_type == "4") ? "display:block;" : "display:none;") . "'>				
				<table   border='0'  cellpadding=0 cellspacing=0 >								
					<tr>
						<td width='300'>
							<table  border='0' cellpadding=0 cellspacing=0 align='center'>
								<tr align='left'>
									<td width='100'>
										<input type=text class=textbox name='search_text'  id='search_text' style='width:180px;margin-bottom:2px;' value=''>  
									</td>
									<td align='center'>
										<img src='../v3/images/" . $_SESSION["admininfo"]["language"] . "/btn_search.gif' onclick=\"SearchInfo('B',$('DIV#goods_display_sub_area_B'), 'brand');\"  style='cursor:pointer;'> 
										<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('DIV#goods_display_sub_area_B #search_result_brand option'),'selected')\" style='cursor:pointer;'/>
									</td>
									</tr>
								<tr>
									<td colspan='2' >
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
											<select name=\"brand[]\" style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='selected_result_brand' validation=false title='브랜드' multiple>";

if ($use_product_type == 4) {
    if ($publish_tmp_ix != "") {
        $sql = "Select crb.b_ix, b.brand_name  from shop_cupon_relation_brand crb, shop_brand b where  b.b_ix = crb.b_ix and crb.publish_tmp_ix = '" . $publish_tmp_ix . "' ";
    } else {
        $sql = "Select crb.b_ix, b.brand_name  from shop_cupon_relation_brand crb, shop_brand b where  b.b_ix = crb.b_ix and crb.publish_ix = '" . $publish_ix . "' ";
    }
    $db->query($sql);
    $selected_brands = $db->fetchall();

    for ($j = 0; $j < count($selected_brands); $j++) {
        $Contents .= "<option value='" . $selected_brands[$j][b_ix] . "' ondblclick=\"$(this).remove();\" selected>" . $selected_brands[$j][brand_name] . "</option>";
    }
}

$Contents .= "
											</select>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>					
				</div>
                <!-- AREA 2 -->
                
                
                
                <div class='cupon_cart_hide'>
                
                
			  	<input type='radio' name='use_product_type' id='use_product_type_2' 
			  	       onclick=\"
			  	       $('#goods_display_sub_area_B').hide();
			  	       document.getElementById('relation_category_area').style.display='block';
			  	       $('#div_productSearchBox').hide();$('#goods_display_sub_area_S').hide();$('#is_except_area').show();
			  	       \" 
			  	       onFocus='this.blur();' align='absmiddle' value=2 " . ($use_product_type == 2 ? "checked" : "") . ">
			  	<label class='blue' for='use_product_type_2'>카테고리에 등록된 상품에 발행 합니다. (선택한 카테고리 하부 상품에 모두 적용됩니다.)</label>
			  	<br>

                    <!-- AREA 3 -->
					<div class='doong' id='relation_category_area' style='" . ($use_product_type == 2 ? "display:block;" : "display:none;") . "vertical-align:top;min-height:60px;padding-left:20px;'   >
					<table border=0 cellpadding=0 cellspacing=0 style='margin-top:10px;'>
						<tr bgcolor='#ffffff'>
							<td  nowrap> <b>카테고리 선택 </b>&nbsp;&nbsp; </td>
							<td>
							<input type='hidden' name='selected_cid' value=''>
							<input type='hidden' name='selected_depth' value=''>
							<input type='hidden' id='_category'>
								<table border=0 cellpadding=0 cellspacing=0>
									<tr>
										<td style='padding-right:5px;'>" . getCategoryList3("대분류", "cid0", " class='cid' onChange=\"loadCategory($(this),'cid1',2)\" title='대분류' ", 0, $cid, "cid0") . " </td>
										<td style='padding-right:5px;'>" . getCategoryList3("중분류", "cid1", " class='cid' onChange=\"loadCategory($(this),'cid2',2)\" title='중분류'", 1, $cid, "cid1") . " </td>
										<td style='padding-right:5px;'>" . getCategoryList3("소분류", "cid2", " class='cid' onChange=\"loadCategory($(this),'cid3',2)\" title='소분류'", 2, $cid, "cid2") . " </td>
										<td>" . getCategoryList3("세분류", "cid3", " class='cid' onChange=\"loadCategory($(this),'cid_1',2)\" title='세분류'", 3, $cid, "cid3") . "</td>
										<td style='padding-left:10px'><img src='../images/" . $admininfo["language"] . "/btn_add.gif' align=absmiddle border=0 onclick=\"categoryadd()\"></td>
									</tr>
								</table>
							</td>
						</tr>
					</table> 
					
					<table width=90% cellpadding=0 cellspacing=0 border=0 id=objCategory style='margin-top:5px;'>
						<col width=1>
						<col width=10>
						<col width=545>
						<col width=*>";

if ($use_product_type == 2) {
    if ($publish_tmp_ix != "") {
        $sql = "Select crc.cid,crc.depth from shop_cupon_relation_category crc, shop_category_info c
						where c.cid = crc.cid and publish_tmp_ix = '" . $publish_tmp_ix . "'
						order by crc.cpc_ix asc";
    } else {
        $sql = "Select crc.cid,crc.depth from shop_cupon_relation_category crc, shop_category_info c
						where c.cid = crc.cid and publish_ix = '" . $publish_ix . "'
						order by crc.cpc_ix asc";
    }
    $db->query($sql);

    for ($j = 0; $j < $db->total; $j++) {
        $db->fetch($j);
        $Contents .= "<tr height=23><td><input type=text name=category[] id='_category' value='" . $db->dt[cid] . "' style='display:none'><input type=text name=depth[] value='" . $db->dt[depth] . "' style='display:none'></td><td></td><td>" . getCategoryPathByAdmin($db->dt[cid], 4) . "</td><td align=right style='padding:5px 25px 5px 5px;'><a href='javascript:void(0)' onClick='category_del(this.parentNode.parentNode)'><img src='../images/" . $_SESSION["admininfo"]["language"] . "/btc_del.gif' border=0></a></td></tr>";
    }
}


$Contents .= "</table>
                <br><br>
			  </div>
			<!-- AREA 3 -->	
					
					
			<!-- AREA 4 -->
			<div  style='display:none'>		
					<input type='radio' name='use_product_type' id='use_product_type_5' 
					       onclick=\"$('#goods_display_sub_area_B').hide();
					       $('#relation_category_area').hide();
					       $('#div_productSearchBox').hide();
					       $('#goods_display_sub_area_S').show();
					       \" 
					       onFocus='this.blur();' align='absmiddle' value=5 " . ($use_product_type == 5 ? "checked" : "") . ">
                   <label class='blue' for='use_product_type_5'>특정셀러(에,를) 속한 상품에 발행합니다.</label>
                   </div>
					<div class='goods_auto_area'  id='goods_display_sub_area_S' style='padding:10px 5px 10px 5px;" . (($use_product_type == "5") ? "display:block;" : "display:none;") . "'>
						<table border='0' cellpadding=0 cellspacing=0 >								
							<tr>
								<td width='300'>
									<table  border='0' cellpadding=0 cellspacing=0 align='center'>
										<tr align='left'>
											<td width='100'>
												<input type=text class=textbox name='search_text'  id='search_text' style='width:180px;margin-bottom:2px;' value=''\"> 
											</td>
											<td align='center'>
												<img src='../v3/images/" . $_SESSION["admininfo"]["language"] . "/btn_search.gif' onclick=\"SearchInfo('S',$('DIV#goods_display_sub_area_S'), 'seller');\"  style='cursor:pointer;'> 
												<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('DIV#goods_display_sub_area_S #search_result_seller option'),'selected')\" style='cursor:pointer;'/>
											</td>
										</tr>
										<tr>
											<td colspan='2' >
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
												<select name=\"seller[]\" style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='selected_result_seller' validation=false title='셀러' multiple>";
if ($use_product_type == 5) {
    $sql = "SELECT ccd.company_id, ccd.com_name 
																FROM common_company_detail ccd, shop_cupon_relation_seller crs 
																where ccd.company_id = crs.company_id and  crs.publish_ix = '" . $publish_ix . "'  ";
    $db->query($sql);
    $selected_sellers = $db->fetchall();

    for ($j = 0; $j < count($selected_sellers); $j++) {
        $Contents .= "<option value='" . $selected_sellers[$j][company_id] . "' ondblclick=\"$(this).remove();\" selected>" . $selected_sellers[$j][com_name] . "</option>";
    }
}
$Contents .= "
												</select>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
			<!-- AREA 4 -->
					
					
			<!-- AREA 5 -->
					<input type='radio' name='use_product_type' id='use_product_type_3' 
					       onclick=\"$('#goods_display_sub_area_B').hide();$('#relation_category_area').hide();ms_productSearch.show_productSearchBox(event,1,'productList_1','clipart','', 'coupon');$('#goods_display_sub_area_S').hide();$('#is_except_area').hide();$('#is_except').prop('checked',false);\" 
					       onFocus='this.blur();' align='absmiddle' value=3 " . ($use_product_type == 3 ? "checked" : "") . ">
					<label class='blue' for='use_product_type_3'>특정 상품에 발행 합니다. (트리에서 상품을 검색 후 드래그앤드롭 을 사용해 등록합니다)</label>
					<br>
			<!-- AREA 5 -->
			";

if ($use_product_type == 6) {
    $Contents .= "
            <!-- AREA 6 -->
					<input type='radio' name='use_product_type' id='use_product_type_6' 
					       onclick=\"$('#goods_display_sub_area_B').hide();$('#relation_category_area').hide();ms_productSearch.show_productSearchBox(event,1,'productList_1','clipart','', 'coupon');$('#goods_display_sub_area_S').hide();\" 
					       onFocus='this.blur();' align='absmiddle' value=6 " . ($use_product_type == 6 ? "checked" : "") . ">
					<label class='blue' for='use_product_type_6'>전체 상품, 일부 상품 제외 발행 합니다. (트리에서 상품을 검색 후 드래그앤드롭 을 사용해 등록합니다)</label>
					<br>
					<div style='width:100%;padding:5px;' id='group_product_area_1'>" . relationCouponProductList($publish_ix) . "</div>
			 <!-- AREA 6 -->	
            ";
} else {
    $Contents .= "		
			<!-- AREA 6 -->
                 <br>
			        <div id='is_except_area'> 
					( <input type='checkbox' name='is_except' id='is_except' align='absmiddle' value=1 " . ($is_except == 1 ? "checked" : "") . ">
					<label onclick=\"ms_productSearch.show_productSearchBox(event,1,'productList_1','clipart','', 'coupon');\" class='blue'>전체 상품, 일부 상품 제외 발행 합니다. (트리에서 상품을 검색 후 드래그앤드롭 을 사용해 등록합니다)</label> )
					</div>
					<div style='width:100%;padding:5px;' id='group_product_area_1'>" . relationCouponProductList($publish_ix) . "</div>
			 <!-- AREA 6 -->							
					  
					</div>
            ";
}
$Contents .= "
			  	</td>
		</tr>
		<tr>
			  <td class='input_box_title' nowrap > <b>쿠폰 발급기간 <img src='" . $required3_path . "'></b></td>
			  <td class='input_box_item' style='padding:10px;' colspan=3 id='disp_area'>
				<table>
					<tr>
						<td><input type='radio' name='disp' id='disp_1'  align='middle' value='1' " . ($disp == '1' || $disp == '' ? "checked" : "") . ">
						    <label for='disp_1' class='green'>발급일</label>
						</td>
						<td>" . search_date('cupon_use_sdate', 'cupon_use_edate', $cupon_use_sdate, $cupon_use_edate, 'Y', 'A') . "</td>
					<tr>
					<tr>
					    <td colspan='3'><input type='radio' name='disp' id='disp_0'  align='middle' value='0' " . ($disp == '0' ? "checked" : "") . ">
					                    <label for='disp_0' class='green'>미발급</label> </br>
					                    <span class='red'>* 미발급 설정 시 고객 프론트에 노출되지 않기 때문에, 고객 다운로드 쿠폰의 경우 고객이 다운로드 할 수 없습니다</span></td>
					<tr>
				</table>
			</td> 
		</tr>
      </table>
    </td>
  </tr>
  <tr>
    <td valign='top'>
      <span id='cupon_send_type_random' style='display: none; width: 100%; filter: blendTrans(Duration=1.5)'>
      <table width='100%' border='0' cellpadding='0' cellspacing='0'>
        <tr>
          <td></td>
        </tr>
      </table>
      </span>
    </td>
  </tr>
  <tr>
    <td height='20'></td>
  </tr>
  <tr>
    <td align='center'>";


if ($_GET["publish_ix"] == "") {
    if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "C")) {
        $Contents .= "<input type=image src='../images/" . $admininfo["language"] . "/b_save.gif' border=0>";
    } else {
        $Contents .= "<a href=\"" . $auth_write_msg . "\"><img  src='../images/" . $admininfo["language"] . "/b_save.gif' border=0></a>";
    }
} else {
    if (checkMenuAuth(md5($_SERVER["PHP_SELF"]), "U")) {
        $Contents .= "<input type=image src='../images/" . $admininfo["language"] . "/b_save.gif' border=0>";
    } else {
        $Contents .= "<a href=\"" . $auth_update_msg . "\"><img  src='../images/" . $admininfo["language"] . "/b_save.gif' border=0></a>";
    }
}
$Contents .= "
	</td>
  </tr>
</form>
</table>
  ";

//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

//$Contents .=  HelpBox("쿠폰 발행", $help_text);

if ($mmode == "pop") {

    $P = new ManagePopLayOut();
    $P->addScript = $Script;
    $P->OnloadFunction = "init();";
    $P->strLeftMenu = promotion_menu();
    $P->Navigation = "프로모션(마케팅)/전시 > 쿠폰관리 > 쿠폰 생성";
    $P->title = "쿠폰 생성";
    $P->NaviTitle = "쿠폰 생성";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
} else {
    $P = new LayOut();
    $P->addScript = $Script;
    $P->OnloadFunction = "init();";
    $P->strLeftMenu = promotion_menu();
    $P->Navigation = "프로모션(마케팅)/전시 > 쿠폰관리 > 쿠폰 생성";
    $P->title = "쿠폰 생성";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}


function SelectCuponKind($select_ix, $cupon_div = "")
{
    $mdb = new Database;
    if ($cupon_div) {
        $sql = "SELECT * FROM " . TBL_SHOP_CUPON . " where cupon_div = '" . $cupon_div . "' order by cupon_ix asc";
    } else {
        //쿠폰 구분 선택해야지만 나오도록 수정 처리
        $sql = "SELECT * FROM " . TBL_SHOP_CUPON . " where 1=2 order by cupon_ix asc";
    }

    $mdb->query($sql);
    $mstring = "<select name='cupon_ix' id='cupon_ix' style=\"width: 300px;\" align='middle'><!--behavior: url('../js/selectbox.htc'); -->";

    $mstring .= "     <option value=''>ㆍ선택ㆍㆍㆍㆍㆍㆍㆍ</option>";


    for ($i = 0; $i < $mdb->total; $i++) {
        $mdb->fetch($i);

        if ($mdb->dt[cupon_div] == "P") {
            $cupon_div = "상품";
        } elseif ($mdb->dt[cupon_div] == "D") {
            $cupon_div = "배송비";
        } else {
            $cupon_div = "-";
        }

        if ($select_ix == $mdb->dt[cupon_ix]) {
            $mstring .= "       <option value='" . $mdb->dt[cupon_ix] . "' selected>" . $mdb->dt[cupon_kind] . "</option>\n";//ㆍ[".$cupon_div."]
        } else {
            $mstring .= "       <option value='" . $mdb->dt[cupon_ix] . "'>" . $mdb->dt[cupon_kind] . "</option>\n";//ㆍ[".$cupon_div."]
        }
    }
    $mstring .= "</select>";

    return $mstring;

}


function relationCouponProductList($publish_ix)
{
    global $admin_config, $publish_tmp_ix;
    $db = new Database;

    $group_code = 1;
    $disp_type = 'clipart';


    if ($publish_tmp_ix != "") {
        $sql = "Select crp.*, p.id, p.pcode, p.shotinfo, p.pname, p.listprice, p.sellprice,  p.wholesale_sellprice, p.wholesale_price,  p.reserve, p.state, p.disp 
					from " . TBL_SHOP_PRODUCT . " p, shop_cupon_relation_product crp where p.id = crp.pid and publish_tmp_ix = '" . $publish_tmp_ix . "' order by crp.vieworder asc ";
    } else {
        $sql = "Select crp.*, p.id, p.pcode, p.shotinfo, p.pname, p.listprice, p.sellprice,  p.wholesale_sellprice, p.wholesale_price,  p.reserve, p.state, p.disp 
					from " . TBL_SHOP_PRODUCT . " p, shop_cupon_relation_product crp where p.id = crp.pid and publish_ix = '" . $publish_ix . "' order by crp.vieworder asc ";
    }
    $db->query($sql);
    $products = $db->fetchall();

    if (count($products)) {
        $script_times["product_discount_start"] = time();
        for ($i = 0; $i < count($products); $i++) {
            $_array_pid[] = $products[$i][id];
            $goods_infos[$products[$i][id]][pid] = $products[$i][id];
            $goods_infos[$products[$i][id]][amount] = $products[$i][pcount];
            $goods_infos[$products[$i][id]][cid] = $products[$i][cid];
            $goods_infos[$products[$i][id]][depth] = $products[$i][depth];
        }

        //print_r($goods_infos);
        $discount_info = DiscountRult($goods_infos, $cid, $depth);
        //print_r($discount_info);

        if (is_array($products)) {
            foreach ($products as $key => $sub_array) {
                $select_ = array("icons_list" => explode(";", $sub_array[icons]));
                array_insert($sub_array, 50, $select_);
                //echo str_pad($sub_array[id], 10, "0", STR_PAD_LEFT)."<br>";
                $discount_item = $discount_info[$sub_array[id]];
                //print_r($discount_item);
                $_dcprice = $sub_array[sellprice];
                if (is_array($discount_item)) {
                    foreach ($discount_item as $_key => $_item) {
                        if ($_item[discount_value_type] == "1") { // %
                            //echo $_item[discount_value]."<br>";
                            $_dcprice = roundBetter($_dcprice * (100 - $_item[discount_value]) / 100, $_item[round_position], $_item[round_type]);//$_dcprice*(100 - $_item[discount_value])/100;
                        } else if ($_item[discount_value_type] == "2") {// 원
                            $_dcprice = $_dcprice - $_item[discount_value];
                        }
                        $discount_desc[] = $_item;//array("discount_type"=>$_item[discount_type], "haddoffice_value"=>$_item[discount_value], "discount_value"=>$_item[discount_value],
                    }
                }
                $_dcprice = array("dcprice" => $_dcprice);
                array_insert($sub_array, 72, $_dcprice);
                $discount_desc = array("discount_desc" => $discount_desc);
                array_insert($sub_array, 73, $discount_desc);
                $products[$key] = $sub_array;
                if ($products[$key][uf_valuation] != "") $products[$key][uf_valuation] = round($products[$key][uf_valuation], 0);
                else $products[$key][uf_valuation] = 0;
            }
            //print_r($products);
        }
        //print_r($products);
    }


    if ($db->total == 0) {
        if ($disp_type == "clipart") {
            $mString = '<ul id="productList_' . $group_code . '" name="productList" class="productList"></ul>';
        }
    } else {
        $i = 0;
        if ($disp_type == "clipart") {
            $mString = '<ul id="productList_' . $group_code . '" name="productList" class="productList"></ul>' . "\n";
            $mString .= '<script>' . "\n";
            $mString .= 'ms_productSearch.groupCode = ' . $group_code . ";\n";
            for ($i = 0; $i < count($products); $i++) {
                $db->fetch($i);
                $imgPath = PrintImage($admin_config['mall_data_root'] . '/images/product', $products[$i]['id'], 'c');
                $mString .= 'ms_productSearch._setProduct("productList_' . $group_code . '", "M", "' . $products[$i]['id'] . '", "' . $imgPath . '", "' . addslashes(addslashes(trim($products[$i]['pname']))) . '", "' . addslashes(addslashes(trim($products[$i]['brand_name']))) . '", "' . $products[$i]['sellprice'] . '", "' . $products[$i]['listprice'] . '", "' . $products[$i]['reserve'] . '", "' . $products[$i]['coprice'] . '", "' . $products[$i]['wholesale_price'] . '", "' . $products[$i]['wholesale_sellprice'] . '", "' . $products[$i]['disp'] . '", "' . $products[$i]['state'] . '", "' . $products[$i]['dcprice'] . '");' . "\n";

            }
            $mString .= '</script>' . "\n";
        }
    }
    return $mString;
}

?>