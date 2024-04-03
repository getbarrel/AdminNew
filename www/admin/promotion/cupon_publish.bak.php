<?
include("../class/layout.class");
$db = new Database();

/////////////////////////////////////////////////////////////////////////////////////////////
// csrf Token 발행
$csrfToken = getCsrfToken();
setCsrfTokenInSess($csrfToken);
/////////////////////////////////////////////////////////////////////////////////////////////

$this_menu_code = md5($_SERVER["PHP_SELF"]);
//$before_menu_code = md5(str_replace("/promotion/","/display/",$_SERVER["PHP_SELF"]));
//include("../store/dic.update.php");

if(!$publish_ix && $regist_ix){
	$sql = "Select publish_ix from ".TBL_SHOP_CUPON_REGIST." cr  where  regist_ix ='$regist_ix' ";
	$db->query($sql);
	$db->fetch();
	$publish_ix = $db->dt[publish_ix];
}

if($publish_ix || ($view_type=="mem_group"&&$publish_tmp_ix)){

	if($view_type!="mem_group"){
		if($db->dbms_type == "oracle"){
			$sql = "Select cp.publish_ix, cp.mall_ix, cupon_div, cupon_ix, cupon_no, use_date_type, date_format(use_sdate,'%Y%m%d%H%i%s') as use_sdate, date_format(use_edate,'%Y%m%d%H%i%s') as use_edate, use_product_type, publish_date_differ, publish_date_type, regist_date_differ, regist_date_type, publish_condition_price, publish_type, mem_ix, cp.regdate, cmd.name from ".TBL_SHOP_CUPON_PUBLISH." cp left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cp.mem_ix = cmd.code where  publish_ix ='$publish_ix' ";
			$db->query($sql);
		}else{
			$sql = "Select cp.*,cmd.name from ".TBL_SHOP_CUPON_PUBLISH." cp left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cp.mem_ix = cmd.code where  publish_ix ='$publish_ix' ";
			$db->query($sql);
		}
	}else{
		if($db->dbms_type == "oracle"){
			$sql = "Select cp.publish_tmp_ix, cp.mall_ix, cupon_div, cupon_ix,  use_date_type, date_format(use_sdate,'%Y%m%d%H%i%s') as use_sdate, date_format(use_edate,'%Y%m%d%H%i%s') as use_edate, use_product_type, publish_date_differ, publish_date_type, regist_date_differ, regist_date_type, publish_condition_price, publish_type, mem_ix, cp.regdate, cmd.name from shop_cupon_publish_tmp cp left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cp.mem_ix = cmd.code where  publish_tmp_ix ='$publish_tmp_ix' ";
			$db->query($sql);
		}else{
			$sql = "Select cp.*,cmd.name from shop_cupon_publish_tmp cp left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cp.mem_ix = cmd.code where  publish_tmp_ix ='$publish_tmp_ix' ";
			$db->query($sql);
		}
	}
	//echo nl2br($sql);
	$db->fetch();
//print_r($db->dt);
	$cupon_publish_info = $db;
	$mall_ix = $db->dt[mall_ix];

	$cupon_div = $db->dt[cupon_div];

	$cupon_ix = $db->dt[cupon_ix];
	$use_date_type = $db->dt[use_date_type];
	$publish_condition_price = $db->dt[publish_condition_price];
	$use_product_type = $db->dt[use_product_type];
	$mem_ix = $db->dt[mem_ix];
	$publish_date_differ = $db->dt[publish_date_differ];
	$publish_date_type = $db->dt[publish_date_type];
	$regist_date_type = $db->dt[regist_date_type];
	$regist_date_differ = $db->dt[regist_date_differ];
	$publish_limit_price = $db->dt[publish_limit_price];
	$publish = "publish_update";
	$publish_name = $db->dt[publish_name];
	$disp = $db->dt[disp];
	$is_cs = $db->dt[is_cs];
	$is_use = $db->dt[is_use];
	$issue_type = $db->dt[issue_type];

	$publish_type = $db->dt[publish_type];
	$cupon_use_sdate = date("Y-m-d H:i:s",$db->dt[cupon_use_sdate]);
	//echo $cupon_use_sdate;
	$cupon_use_edate = date("Y-m-d H:i:s",$db->dt[cupon_use_edate]);
	$editdate = $db->dt[editdate];
	$regdate = $db->dt[regdate];

	if($publish_type == 1){
		$mem_id = $db->dt[name]."(".$db->dt[id].")";
	}

//	$sDate = date("Y/m/d");
	//$sDate = substr($db->dt[use_sdate],0,4)."/".substr($db->dt[use_sdate],5,2)."/".substr($db->dt[use_sdate],8,2)."-".substr($db->dt[use_sdate],11,2).":".substr($db->dt[use_sdate],14,2).":".substr($db->dt[use_sdate],17,2);
	//$eDate = substr($db->dt[use_edate],0,4)."/".substr($db->dt[use_edate],5,2)."/".substr($db->dt[use_edate],8,2)."-".substr($db->dt[use_edate],11,2).":".substr($db->dt[use_edate],14,2).":".substr($db->dt[use_edate],17,2);
	//echo $eDate;
	//echo $db->dt[use_sdate].":::".$sDate;
	$use_sdate = $db->dt[use_sdate];
	$use_edate = $db->dt[use_edate];
	$buy_point = $db->dt[buy_point];

	//$startDate = $db->dt[use_sdate];
	//$endDate = $db->dt[use_edate];
}else{

	$use_date_type = "3";
	$use_product_type = "1";
	$publish = "publish";
	$publish_type = "2";
	$issue_type = "1";
	$publish_condition_price = 0;
	$publish_limit_price = 0;

	//기본으로 노출 2014-07-15 Hong
	$cupon_use_sdate = date("Y-m-d 00:00:00");
	$cupon_use_edate = date("Y-m-d 23:59:59");
	$use_sdate = date("Y-m-d 00:00:00");
	$use_edate = date("Y-m-d 23:59:59");

	//$cupon_publish_info[is_include] = 1;

	//$before10day = mktime(date("H"), date("i"), date("s"), date("m")  , date("d")+10, date("Y"));

//	$sDate = date("Y/m/d");
	//$sDate = date("Y/m/d-H:i:s");
	//$eDate = date("Y/m/d-H:i:s", $before10day);

	//$startDate = date("Ymd", $before10day);
	//$endDate = date("Ymd");

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
	
	if(frm.cupon_div.value == ''){
		alert('쿠폰 구분이 선택되지 않았습니다. 쿠폰구분을 선택해주세요');
		//'쿠폰종류가 선택되지 않았습니다. 쿠폰종류를 선택해주세요'
		//frm.cupon_ix.focus();
		return false;
	}

	if(frm.cupon_ix.value == ''){
		alert(language_data['cupon_publish.php']['A'][language]);
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
	var admin_level = '".$_SESSION["admininfo"]["admin_level"]."';

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
 

	obj.append(\"<tr id='num_tr' height=30 class=''><td><input type=hidden name=category[] id='_category' value='\" + ret + \"' style='display:none'><input type=hidden name=depth[] id='_depth' value='\" + $('form[name=form_cupon]').find('input[name=selected_depth]').val() + \"' style='display:none'></td><td></td><td > \"+str.join(\" > \")+\" </td><td align=right style='padding:5px 25px 5px 5px;'><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0 onClick=\\\" $(this).closest('tr').remove();\\\" style='cursor:point;'></td></tr>\");
	 
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
	obj.parent().parent().find('div[class^=display_sub_target_area]').hide();
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
			data: {'act': 'get_coupon', 'return_type': 'json', 'cupon_div':cupon_div, 'csrfToken' : '".$csrfToken."'},
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
				if(cupon_div == 'C'){
					$('input[id^=use_product_type]').attr('disabled', true);
					$('input[id^=use_product_type_1]').attr('disabled', false);
					$('input[id^=use_product_type_1]').attr('checked', true);
				}else{
					$('input[id^=use_product_type]').attr('disabled', false);
				}


			} 
		}); 
}

</script>";


$vdate = date("YmdHis", time());
$today = date("Y/m/d-H:i:s", time());
$vyesterday = date("Y/m/d-H:i:s", time()-84600);
$voneweekafter = date("Y/m/d-H:i:s", time()+84600*7);
$vtwoweekafter = date("Y/m/d-H:i:s", time()+84600*14);
$vfourweekafter = date("Y/m/d-H:i:s", time()+84600*28);
$vyesterday = date("Y/m/d-H:i:s",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24);
$voneweekafter = date("Y/m/d-H:i:s",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*7);
$v15after = date("Y/m/d-H:i:s",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*15);
$vfourweekafter = date("Y/m/d-H:i:s",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*28);
$vonemonthafter = date("Y/m/d-H:i:s",mktime(0,0,0,substr($vdate,4,2)+1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthafter = date("Y/m/d-H:i:s",mktime(0,0,0,substr($vdate,4,2)+2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthafter = date("Y/m/d-H:i:s",mktime(0,0,0,substr($vdate,4,2)+3,substr($vdate,6,2)+1,substr($vdate,0,4)));


$Contents .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0'>
  <!-- // 쿠폰정보 -->
  <tr>
	<td align='left' colspan=6> ".GetTitleNavigation("쿠폰 발행", "전시관리 > 쿠폰 발행 ")."</td>
  </tr>
  <!--tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='cupon_publish.php'>쿠폰발행</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='cupon_publish_list.php'>쿠폰발행 목록</a></td>
								<th class='box_03'></th>
							</tr>
							</table>

						</td>
						<td style='width:600px;text-align:right;vertical-align:bottom;padding:0 0 10 0'>

						</td>
					</tr>
					</table>
				</div>
	    </td>
	</tr-->
  <!--tr>
    <td bgcolor='#efefef'><font class='orange16'>쿠폰 발행</font>&nbsp;(여러명일때 아이디는 ',' 또는 ';' 로 구분)</td>
  </tr-->
  <tr>
    <td height='10'></td>
  </tr>
  <tr>
    <td valign='top'>
    	<form name='form_cupon' onsubmit='return CheckCuponInfo(this)' method='post'  action='cupon.act.php' target=''>
  		<input type=hidden name='act' value='".$publish."'>
  		<input type=hidden name='mmode' value='".$mmode."'>
  		<input type=hidden name='publish_ix' value='".$publish_ix."'>
		<input type=hidden name='view_type' value='".$view_type."'>		
		<input type=hidden name='publish_tmp_ix' value='".$publish_tmp_ix."'>
		<input type=hidden name='csrfToken' value='".$csrfToken."'>
		<input type=hidden name='is_include' value='1'>";
if($regdate){
	  $Contents .= "
	  <div style='padding:5px;'>
	  쿠폰 등록일 / 수정일 : ".$regdate." / ".$editdate."
	  </div>";
}
	  $Contents .= "
      <table width='100%' border='0' cellpadding='5' cellspacing='1' class='input_table_box'>
        <col width='15%'>
		<col width='35%'>
		<col width='15%'>
		<col width='35%'>";
		if($_SESSION["admin_config"][front_multiview] == "Y"){
		$Contents .= "
		<tr>
			<td class='input_box_title' > 프론트 전시 구분</td>
			<td class='input_box_item' style='padding-left:5px;' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
		</tr>";
		}
		$Contents .= "
		<tr bgcolor='#ffffff'>
          <td class='input_box_title'>   <b>쿠폰종류</b></td>
          <td class='input_box_item' colspan=3>
				<select name='cupon_div' id='cupon_div' onchange=\"changeCouponDiv($(this), 'cupon_ix')\" validation=true title='쿠폰 구분'>
					<option value=''>쿠폰구분 선택</option>";
					foreach($_COUPON_KIND as $key => $value){
$Contents .= "<option value='".$key."' ".($cupon_div == $key ? "selected":"").">".$value."</option>";
					}
$Contents .= "</select>
		  ".SelectCuponKind($cupon_ix, $cupon_div)."
          	<br><img src='../image/0.gif' width='1' height='1'></td>
			
        </tr>
		<tr bgcolor='#ffffff'>
          <td class='input_box_title'>   <b>쿠폰 발행명</b></td>
          <td class='input_box_item' colspan=3><input type='text' id='publish_name' name='publish_name' value='".$publish_name."' class='textbox' style='height: 18px; width: 370px;'  align='absmiddle'></td>
        </tr>
        <tr bgcolor='#ffffff'>
          <td class='input_box_title'>  <b>사용기간</b></td>
          <td class='input_box_item' style='padding:10px;' colspan=3>
          	<table cellpadding=0 cellspacing=2 border=0 >
				<tr>
					<td valign=middle nowrap>
					<input type='radio' name='use_date_type' id='use_date_type_3' onFocus='this.blur();' align='absmiddle' value=3 ".($use_date_type == 3 ? "checked":"")."><label class='blue' for='use_date_type_3'>사용기간지정</label>&nbsp;
					</td>
					<TD nowrap>
						".search_date('use_sdate','use_edate',$use_sdate,$use_edate,'Y','A')."                        
                    </TD>
			</table>
			<div style='padding:3px 0px 3px 0px'>
				<input type='radio' name='use_date_type' id='use_date_type_1'  onFocus='this.blur();' align='absmiddle' value=1 ".($use_date_type == 1 ? "checked":"").">
				<label class='blue' for='use_date_type_1'>발행일로부터</label>&nbsp;(&nbsp;<input type='text' id='fuse_date1' name='publish_date_differ' value='".($use_date_type == 1 ? $publish_date_differ:"")."' class='textbox' maxlength='3' style='filter: blendTrans(duration=0.5); height: 18px; width: 20px'  onFocus=\"FIn(fuse_date1,'#FFD323',0)\" onFocusOut=\"FOut(fuse_date1,'',0)\"   align='absmiddle'>
				<input type='radio' name='publish_date_type' onFocus='this.blur();' align='absmiddle' value=1 ".($publish_date_type == 1 ? "checked":"").">년&nbsp;
				<input type='radio' name='publish_date_type' onFocus='this.blur();' align='absmiddle' value=2 ".($publish_date_type == 2 ? "checked":"").">개월&nbsp;
				<input type='radio' name='publish_date_type' onFocus='this.blur();' align='absmiddle' value=3 ".($publish_date_type == 3 ? "checked":"").">일&nbsp;)&nbsp;간 사용 가능.<br>
			</div>
          	<input type='radio' name='use_date_type' id='use_date_type_2'  onFocus='this.blur();' align='absmiddle' value=2 ".($use_date_type == 2 ? "checked":"").">
          	<label class='blue' for='use_date_type_2'>발급일로부터</label>&nbsp;(&nbsp;<input type='text' id='fuse_date2' name='regist_date_differ' value='".($use_date_type == 2 ? $regist_date_differ:"")."' class='textbox' maxlength='3' style='filter: blendTrans(duration=0.5); height: 18px; width: 20px' onFocus=\"FIn(fuse_date2,'#FFD323',0)\" onFocusOut=\"FOut(fuse_date2,'',0)\" align='absmiddle'>
          	
			<input type='radio' name='regist_date_type' onFocus='this.blur();' align='absmiddle' value=1 ".($regist_date_type == 1 ? "checked":"").">년&nbsp;
          	<input type='radio' name='regist_date_type' onFocus='this.blur();' align='absmiddle' value=2 ".($regist_date_type == 2 ? "checked":"").">개월&nbsp;
          	<input type='radio' name='regist_date_type' onFocus='this.blur();' align='absmiddle' value=3 ".($regist_date_type == 3 ? "checked":"").">일&nbsp;)&nbsp;간 사용 가능.<br>
          	<div style='padding:3px 0px 3px 0px'>
			<input type='radio' name='use_date_type' id='use_date_type_9'  onFocus='this.blur();' align='absmiddle' value=9 ".($use_date_type == 9 ? "checked":"").">
          	<label class='blue' for='use_date_type_9'>무기한 사용</label>
			</div>
          	</td>
        </tr>
        <tr >
          <td class='input_box_title' rowspan=2>  <b>사용가능상품</b></td>
          <td class='input_box_item' style='line-height:200%;padding:5px;' colspan=3>
          	결제가격이 ".$currency_display[$admin_config["currency_unit"]]["front"]." 
			<input type='text' id='fprice' name='publish_condition_price' value='$publish_condition_price' class='textbox numeric' style='height: 18px; width: 70px; filter: blendTrans(duration=0.5)' onFocus=\"FIn(fprice,'#FFD323',0)\" onFocusOut=\"FOut(fprice,'',0)\"  align='absmiddle'>&nbsp;".$currency_display[$admin_config["currency_unit"]]["back"]."&nbsp;이상인 상품에 사용가능 ".$currency_display[$admin_config["currency_unit"]]["front"]." 
			<input type='text' id='fprice' name='publish_limit_price' value='$publish_limit_price' class='textbox numeric' style='height: 18px; width: 70px; filter: blendTrans(duration=0.5)' onFocus=\"FIn(fprice,'#FFD323',0)\" onFocusOut=\"FOut(fprice,'',0)\"  align='absmiddle'>&nbsp;".$currency_display[$admin_config["currency_unit"]]["back"]."까지 할인 가능<br>
          	(0 원을 입력하시면  가격제한 없음)
			<!--".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B',$publish_condition_price,'publish_condition_price')."-->
          </td>
        </tr>
        <tr bgcolor='#ffffff'>
			  	<td class='input_box_item' style='padding:5px;' colspan=3>
			  	<input type='radio' name='use_product_type' id='use_product_type_1' onclick=\"$('#goods_display_sub_area_B').hide();$('#div_productSearchBox').hide();;$('#relation_category_area').hide();$('#goods_display_sub_area_S').hide();\" onFocus='this.blur();' align='absmiddle' value=1 ".($use_product_type == 1 ? "checked":"")."><label class='blue' for='use_product_type_1'><!--전체 상품에 발급합니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</label><br>
			  	<input type='radio' name='use_product_type' id='use_product_type_4' onclick=\"$('#goods_display_sub_area_B').show();$('#relation_category_area').hide();$('#goods_display_sub_area_S').hide();$('#div_productSearchBox').hide();\" onFocus='this.blur();' align='absmiddle' value=4 ".($use_product_type == 4 ? "checked":"")."><label class='blue' for='use_product_type_4'> 특정 브랜드(에,를) 속한 상품에 발행 합니다. </label><br>"; //".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D', $cupon_publish_info, "is_include")."
/*
$Contents .= "
			  	<div class='doong' id='relation_brand_area' style='".($use_product_type == 4 ? "display:block;":"display:none;")."vertical-align:top'   >";
$Contents .= "
			  		<table cellpadding=3>
			  			<tr>
				  			<td  nowrap> <b>브랜드 선택 </b>&nbsp;&nbsp; </td>
				  			<td>".BrandListSelect($brand, $cid)." <input type='hidden' id='_brand'></td>
							<td><img src='../images/".$admininfo["language"]."/btn_add.gif' align=absmiddle border=0 onclick=\"brandadd()\"></td>
			  			</tr>
			  		</table>
					<br>
					<table width=100% cellpadding=0 cellspacing=0 id=objBrand>
						<col width=1>
						<col width=50>
						<col width=545>
						<col width=*>";
if($use_product_type == 4){
		if($publish_tmp_ix!=""){
			$sql = "Select crb.b_ix from shop_cupon_relation_brand crb
						where publish_tmp_ix = '".$publish_tmp_ix."' ";
		}else{
			$sql = "Select crb.b_ix from shop_cupon_relation_brand crb
						where publish_ix = '".$publish_ix."' ";
		}
		$db->query($sql);

		for($j=0;$j < $db->total;$j++){
			$db->fetch($j);

			$brand_info=GetBrandData($db->dt[b_ix],'');

			$Contents  .= "<tr height=23><td><input type=text name=brand[] id='_brand' value='".$db->dt[b_ix]."' style='display:none'></td><td></td><td>".$brand_info[brand_name]."</td><td><a href='javascript:void(0)' onClick='brand_del(this.parentNode.parentNode)'><img src='../images/i_close.gif' border=0></a></td></tr>";
		}
}
$Contents .= "</table><br><br>

			  	</div>";
				*/
$Contents .= "				
			<div class='goods_auto_area'  id='goods_display_sub_area_B' style='padding:10px 5px 10px 5px;".(($use_product_type == "4") ? "display:block;":"display:none;")."'>
				<table   border='0'  cellpadding=0 cellspacing=0 >								
					<tr>
						<td width='300'>
							<table  border='0' cellpadding=0 cellspacing=0 align='center'>
								<tr align='left'>
									<td width='100'>
										<input type=text class=textbox name='search_text'  id='search_text' style='width:180px;margin-bottom:2px;' value=''>  
									</td>
									<td align='center'>
										<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' onclick=\"SearchInfo('B',$('DIV#goods_display_sub_area_B'), 'brand');\"  style='cursor:pointer;'> 
										<!--img src='../images/btn_select_brand.gif' onclick=\"SearchInfo('B',$('DIV#goods_display_sub_area_B'), 'brand');\"  style='cursor:pointer;'--> 
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
											<select name=\"brand[]\" style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='selected_result_brand' validation=false title='브랜드' multiple>
											";
											//$vip_array = get_vip_member('4');

											if($use_product_type == 4){
												if($publish_tmp_ix!=""){
													$sql = "Select crb.b_ix, b.brand_name  from shop_cupon_relation_brand crb, shop_brand b where  b.b_ix = crb.b_ix and crb.publish_tmp_ix = '".$publish_tmp_ix."' ";
												}else{
													$sql = "Select crb.b_ix, b.brand_name  from shop_cupon_relation_brand crb, shop_brand b where  b.b_ix = crb.b_ix and crb.publish_ix = '".$publish_ix."' ";
												}
												$db->query($sql);
												$selected_brands = $db->fetchall();



											//$sql = "SELECT b.b_ix, b.brand_name FROM shop_brand b, shop_popup_brand_relation pbr where b.b_ix = pbr.b_ix and pbr.popup_ix = '".$popup_ix."'   ";
											//$db->query($sql);



												for($j = 0; $j < count($selected_brands); $j++){
													$Contents .="<option value='".$selected_brands[$j][b_ix]."' ondblclick=\"$(this).remove();\" selected>".$selected_brands[$j][brand_name]."</option>";
												}
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

			  	<input type='radio' name='use_product_type' id='use_product_type_2' onclick=\"$('#goods_display_sub_area_B').hide();document.getElementById('relation_category_area').style.display='block';$('#div_productSearchBox').hide();$('#goods_display_sub_area_S').hide();\" onFocus='this.blur();' align='absmiddle' value=2 ".($use_product_type == 2 ? "checked":"")."><label class='blue' for='use_product_type_2'><!--카테고리에 등록된 상품에 발급합니다(선택한 카테고리 하부 상품에 모두 적용됩니다.)-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."</label><br>

					<div class='doong' id='relation_category_area' style='".($use_product_type == 2 ? "display:block;":"display:none;")."vertical-align:top;min-height:60px;padding-left:20px;'   >
					<table border=0 cellpadding=0 cellspacing=0 style='margin-top:10px;'>
						<tr bgcolor='#ffffff'>
							<td  nowrap> <b>카테고리 선택 </b>&nbsp;&nbsp; </td>
							<td  >
							<input type='hidden' name='selected_cid' value=''>
							<input type='hidden' name='selected_depth' value=''>
							<input type='hidden' id='_category'>
								<table border=0 cellpadding=0 cellspacing=0>
									<tr>
										<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0", " class='cid' onChange=\"loadCategory($(this),'cid1',2)\" title='대분류' ", 0, $cid)." </td>
										<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1", " class='cid' onChange=\"loadCategory($(this),'cid2',2)\" title='중분류'", 1, $cid)." </td>
										<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2", " class='cid' onChange=\"loadCategory($(this),'cid3',2)\" title='소분류'", 2, $cid)." </td>
										<td>".getCategoryList3("세분류", "cid3", " class='cid' onChange=\"loadCategory($(this),'cid_1',2)\" title='세분류'", 3, $cid)."</td>
										<td style='padding-left:10px'><img src='../images/".$admininfo["language"]."/btn_add.gif' align=absmiddle border=0 onclick=\"categoryadd()\"></td>
									</tr>
								</table>";

						$Contents .= "	</td>
						</tr>
					</table> 
					<table width=90% cellpadding=0 cellspacing=0 border=0 id=objCategory style='margin-top:5px;'>
						<col width=1>
						<col width=10>
						<col width=545>
						<col width=*>";
if($use_product_type == 2){
		if($publish_tmp_ix!=""){
			$sql = "Select crc.cid,crc.depth from shop_cupon_relation_category crc, shop_category_info c
						where c.cid = crc.cid and publish_tmp_ix = '".$publish_tmp_ix."'
						order by crc.cpc_ix asc";
		}else{
			$sql = "Select crc.cid,crc.depth from shop_cupon_relation_category crc, shop_category_info c
						where c.cid = crc.cid and publish_ix = '".$publish_ix."'
						order by crc.cpc_ix asc";
		}
		//echo nl2br($sql);
		$db->query($sql);

		for($j=0;$j < $db->total;$j++){
			$db->fetch($j);
			$Contents  .= "<tr height=23><td><input type=text name=category[] id='_category' value='".$db->dt[cid]."' style='display:none'><input type=text name=depth[] value='".$db->dt[depth]."' style='display:none'></td><td></td><td>".getCategoryPathByAdmin($db->dt[cid], 4)."</td><td align=right style='padding:5px 25px 5px 5px;'><a href='javascript:void(0)' onClick='category_del(this.parentNode.parentNode)'><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0></a></td></tr>";
		}
}
$Contents .= "</table><br><br>
					</div>
					
					
					<input type='radio' name='use_product_type' id='use_product_type_5' onclick=\"$('#goods_display_sub_area_B').hide();$('#relation_category_area').hide();$('#div_productSearchBox').hide();$('#goods_display_sub_area_S').show();\" onFocus='this.blur();' align='absmiddle' value=5 ".($use_product_type == 5 ? "checked":"")."><label class='blue' for='use_product_type_5'><!--특정 상품에 발급합니다 (트리에서 상품을 검색후 Drag & Drop 을 통해서 등록합니다)-->특정셀러(에,를) 속한 상품에 발행합니다.</label><br>
					<div class='goods_auto_area'  id='goods_display_sub_area_S' style='padding:10px 5px 10px 5px;".(($use_product_type == "5") ? "display:block;":"display:none;")."'>
						<table   border='0'  cellpadding=0 cellspacing=0 >								
							<tr>
								<td width='300'>
									<table  border='0' cellpadding=0 cellspacing=0 align='center'>
										<tr align='left'>
											<td width='100'>
												<input type=text class=textbox name='search_text'  id='search_text' style='width:180px;margin-bottom:2px;' value=''\"> 
												<!--onclick=ShowModalWindow('./charger_search.php?company_id=3444fde7c7d641abc19d5a26f35a12cc&target=4&amp;code=',600,530,'charger_search')-->
											</td>
											<td align='center'>
												<img src='../v3/images/".$_SESSION["admininfo"]["language"]."/btn_search.gif' onclick=\"SearchInfo('S',$('DIV#goods_display_sub_area_S'), 'seller');\"  style='cursor:pointer;'> 
												<!--img src='../images/btn_select_seller.gif' onclick=\"SearchInfo('S',$('DIV#goods_display_sub_area_S'), 'seller');\"  style='cursor:pointer;'--> 
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
												<select name=\"seller[]\" style='width:300px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='selected_result_seller' validation=false title='셀러' multiple>
												";
												//$vip_array = get_vip_member('4');
												if($use_product_type == 5){
													$sql = "SELECT ccd.company_id, ccd.com_name 
																FROM common_company_detail ccd, shop_cupon_relation_seller crs 
																where ccd.company_id = crs.company_id and  crs.publish_ix = '".$publish_ix."'  ";
													$db->query($sql);
													$selected_sellers = $db->fetchall();


													for($j = 0; $j < count($selected_sellers); $j++){
														$Contents .="<option value='".$selected_sellers[$j][company_id]."' ondblclick=\"$(this).remove();\" selected>".$selected_sellers[$j][com_name]."</option>";
													}
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
					<input type='radio' name='use_product_type' id='use_product_type_3' onclick=\"$('#goods_display_sub_area_B').hide();$('#relation_category_area').hide();ms_productSearch.show_productSearchBox(event,1,'productList_1','clipart','', 'coupon');$('#goods_display_sub_area_S').hide();\" onFocus='this.blur();' align='absmiddle' value=3 ".($use_product_type == 3 ? "checked":"")."><label class='blue' for='use_product_type_3'><!--특정 상품에 발급합니다 (트리에서 상품을 검색후 Drag & Drop 을 통해서 등록합니다)-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F', $db)."</label><br>
					<div style='width:100%;padding:5px;' id='group_product_area_1'>".relationCouponProductList($publish_ix)."</div>
			  	</td>
			  </tr>
			  <tr>
					<td class='input_box_title' nowrap >발행구분</td>
					<td  class='search_box_item' style='padding:10px;' colspan=3><a name='publish_type'></a>
					<div style='padding-bottom:10px;'>";
		 if($view_type=="mem_group"){
			 $publish_type = "4";
			  foreach($_PUBLISH_TYPE as $key => $value){
				//$Contents .= "<input type='radio' name='cupon_use_div' id='cupon_use_div_".$key."' value='".$key."' ".CompareReturnValue($key,$cupon_use_div,"checked")." validation=true title='쿠폰사용'> <label for='cupon_use_div_".$key."' >".$value."</label> ";
				if($key == 4){
					$onclick_str = "$('#display_sub_target_area').show();$('#display_sub_target').show();ChangeDisplaySubTarget($(this),1,'G');";


				$Contents .= "<input type='radio' name='publish_type' id='publish_type_".$key."' onFocus='this.blur();' align='middle' value='".$key."'  ".($publish_type == $key ? "checked":"")." onclick=\"".$onclick_str."\" ><span style='padding-left:2px' class='helpcloud' help_width='280' help_height='25' help_html='".$value["desc"]."'><label for='publish_type_".$key."' class='green'>".$value["text"]."</label>
				</span>";

				}
			  }
		 }else{

		  foreach($_PUBLISH_TYPE as $key => $value){
			//$Contents .= "<input type='radio' name='cupon_use_div' id='cupon_use_div_".$key."' value='".$key."' ".CompareReturnValue($key,$cupon_use_div,"checked")." validation=true title='쿠폰사용'> <label for='cupon_use_div_".$key."' >".$value."</label> ";
			if($key == 2){
				$onclick_str = "$('#display_sub_target_area').hide();$('#display_sub_target').hide();";
			}else if($key == 1){
				$onclick_str = "$('#display_sub_target_area').show();$('#display_sub_target').show();ChangeDisplaySubTarget($(this),1,'M');";
			}else if($key == 3){
				$onclick_str = "$('#display_sub_target_area').hide();$('#display_sub_target').hide();";
			}else if($key == 4){
				$onclick_str = "$('#display_sub_target_area').show();$('#display_sub_target').show();ChangeDisplaySubTarget($(this),1,'G');";
			}else if($key == 5){
				$onclick_str = "$('#display_sub_target_area').hide();$('#display_sub_target').hide();";
			}

			$Contents .= "<input type='radio' name='publish_type' id='publish_type_".$key."' onFocus='this.blur();' align='middle' value='".$key."'  ".($publish_type == $key ? "checked":"")." onclick=\"".$onclick_str."\" ><span style='padding-left:2px' class='helpcloud' help_width='280' help_height='10' help_html='".$value["desc"]."'><label for='publish_type_".$key."' class='green'>".$value["text"]."</label>
</span>";
		  }
/*
$Contents .="
			 <input type='radio' name='publish_type' id='publish_type_2' onFocus='this.blur();' align='middle' value=2  ".($publish_type == 2 ? "checked":"")." onclick=\"$('#display_sub_target_area').hide();$('#display_sub_target').hide();\" ><label for='publish_type_2' class='green'>전체회원(일반 발행)</label>
			<span style='padding-left:2px' class='helpcloud' help_width='280' help_height='25' help_html='상품에 적용되어 고객이 다운받는 쿠폰성격.'><img src='/admin/images/icon_q.gif' align=absmiddle style='margin:2px 0px 0px 0px;' /></span>

			<input type='radio' name='publish_type' id='publish_type_1' onFocus='this.blur();' align='middle' value=1  ".(($publish_type == 1 || $publish_type == "") ? "checked":"")." onclick=\"$('#display_sub_target_area').show();$('#display_sub_target').show();ChangeDisplaySubTarget($(this),1,'M');\" ><label for='publish_type_1' class='green'>고객지정 발행</label>
			<span style='padding-left:2px' class='helpcloud' help_width='245' help_height='25' help_html='관리자가 고객을 지정하여 발행하는 성격.'><img src='/admin/images/icon_q.gif' align=absmiddle style='margin:2px 0px 0px 0px;'/></span>

			<input type='radio' name='publish_type' id='publish_type_3' onFocus='this.blur();' align='middle' value=3  ".(($publish_type == 3 || $publish_type == "") ? "checked":"")." onclick=\"$('#display_sub_target_area').show();$('#display_sub_target').show();ChangeDisplaySubTarget($(this),1,'G');\" ><label for='publish_type_3' class='green'>회원그룹 발행</label>
			<span style='padding-left:2px' class='helpcloud' help_width='245' help_height='25' help_html='관리자가 회원그룹을 지정하여 발행하는 성격.'><img src='/admin/images/icon_q.gif' align=absmiddle style='margin:2px 0px 0px 0px;'/></span>

			<input type='radio' name='publish_type' id='publish_type_4' onFocus='this.blur();' align='middle' value=4 ".($publish_type == 4 ? "checked":"")." onclick=\"$('#display_sub_target_area').hide();$('#display_sub_target').hide();\" ><label for='publish_type_4' class='green'>회원가입시 자동 발급</label>
			<span style='padding-left:2px' class='helpcloud' help_width='230' help_height='30' help_html='회원가입시 자동으로 발행되는 성격.'><img src='/admin/images/icon_q.gif' align=absmiddle style='margin:2px 0px 0px 0px;' /></span>

			<input type='radio' name='publish_type' id='publish_type_5' onFocus='this.blur();' align='middle' value=5 ".($publish_type == 5 ? "checked":"")." onclick=\"$('#display_sub_target_area').hide();$('#display_sub_target').hide();\" ><label for='publish_type_5' class='green'>생일 / 결혼기념일 자동 발급</label>
			<span style='padding-left:2px' class='helpcloud' help_width='230' help_height='30' help_html='생일 / 결혼기념일 자동으로 발행되는 성격.'><img src='/admin/images/icon_q.gif' align=absmiddle style='margin:2px 0px 0px 0px;' /></span>";
*/
 /*
$Contents .="
				<input type='radio' class='textbox' name='publish_type' id='publish_type_a' size=50 value='A' style='border:0px;' ".(($publish_type == "A" || $publish_type == "") ? "checked":"")." onclick=\"$('#display_sub_target_area').hide();$('#display_sub_target').hide();\"/><label for='publish_type_a'>일반반행</label>
				<input type='radio' class='textbox' name='publish_type' id='publish_type_t' size=50 value='T' style='border:0px;' ".($publish_type == "T"  ? "checked":"")." onclick=\"$('#display_sub_target_area').show();$('#display_sub_target').show();\"/><label for='publish_type_t'>고객지정발행</label>

				<select name='display_sub_target' id='display_sub_target' onchange='ChangeDisplaySubTarget($(this), ".($i+1)." , this.value);'  ".($publish_type == "T" ? "style='display:inline;'":"style='display:none;'")." >
				<option value='G' ".(($display_sub_target == "G" || $display_sub_target == "") ? "selected":"").">그룹별</option>
				<option value='M' ".(($display_sub_target == "M") ? "selected":"").">개인별</option>
			  </select>";
*/
		 }
$Contents .="
							<br>
						</div>
						<div id='display_sub_target_area' ".(($publish_type == "1" || $publish_type == "4" || $view_type=="mem_group") ? "style='display:block' ":"style='display:none'")." >
							<div class='display_sub_target_area'  id='display_sub_target_area_G' ".(($publish_type == "4" || $view_type=="mem_group") ? "style='display:block' ":"style='display:none'")." >
								<table   border='0'  cellpadding=0 cellspacing=0 >								
										<tr>
											<td width='300'>
												<table  border='0' cellpadding=0 cellspacing=0 align='center'>
													<tr align='left'>
														<td width='100'>   
															<input type=text class=textbox name='search_text'  id='search_text' style='width:210px;margin-bottom:2px;' value=''>  
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
															<select name='search_result[]' class='search_result' id='search_result_group'  style=' width:320px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' multiple>											
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
															//$vip_array = get_vip_member('4');

															if($publish_type == "4"){
																//기존 sql ###
																$sql = "SELECT gi.gp_ix, gi.gp_name 
																			FROM shop_groupinfo gi, shop_cupon_publish_config cpc 
																			where gi.gp_ix = cpc.r_ix and cpc.publish_type = '".$publish_type."' and publish_ix = '".$publish_ix."'  ";
																/*
																$sql = "SELECT gi.gp_ix, gi.gp_name
																			FROM shop_groupinfo gi, shop_cupon_relation_group scrg
																			WHERE gi.gp_ix = scrg.gp_ix AND scrg.publish_tmp_ix = '".$publish_ix."'";
																*/

																$db->query($sql);
																$selected_groups = $db->fetchall();


																for($j = 0; $j < count($selected_groups); $j++){
																	$Contents .="<option value='".$selected_groups[$j][gp_ix]."' ondblclick=\"$(this).remove();\" selected>".$selected_groups[$j][gp_name]."</option>";
																}
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
							<div class='display_sub_target_area'  id='display_sub_target_area_M'  ".(($publish_type == "1") ? "style='display:block' ":"style='display:none'")." >
								<table   border='0'  cellpadding=0 cellspacing=0 >								
										<tr>
											<td width='300'>
												<table  border='0' cellpadding=0 cellspacing=0 align='center'>
													<tr align='left'>
														<td width='100'>   
															<input type=text class=textbox name='search_text'  id='search_text' style='width:210px;margin-bottom:2px;' value=''>  
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
															//$db->query("select pdr_ix from shop_cupon_publish_config where publish_type = '".$publish_type."' and r_ix = '".$selected_result[$publish_type_text][$j]."' and publish_ix = '".$publish_ix."'  ");
															if($publish_type == "1"){
																$sql = "SELECT cmd.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id as user_id 
																			FROM common_user cu, common_member_detail cmd, shop_cupon_publish_config cpc 
																			where cu.code = cmd.code and cmd.code = cpc.r_ix and cpc.publish_type = '".$publish_type."'  and publish_ix = '".$publish_ix."'  ";
																$db->query($sql);
																$selected_members = $db->fetchall();


																for($j = 0; $j < count($selected_members); $j++){
																	$Contents .="<option value='".$selected_members[$j][code]."' ondblclick=\"$(this).remove();\" selected>".$selected_members[$j][name]."(".$selected_members[$j][user_id].")</option>";
																}
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
/*
$Contents .= "
        <tr bgcolor='#ffffff'>
          <td class='input_box_title'>  <b>발급구분</b></td>
          <td class='input_box_item' style='line-height:120%;padding:5px;'>";
		 if($view_type=="mem_group"){
			 $Contents .= "
			  <input type='radio' name='publish_type' id='publish_type_4' onFocus='this.blur();' align='middle' value=4  ".($publish_type == 4 || $view_type == "mem_group" ? "checked":"")."><label for='publish_type_4' class='green'>회원그룹 발행</label>";
		 }else{

			$Contents .= "
			  <input type='radio' name='publish_type' id='publish_type_1' onFocus='this.blur();' align='middle' value=1  ".($publish_type == 1 ? "checked":"")."><label for='publish_type_1' class='green'>고객지정 발행</label> 
			  <span style='padding-left:2px' class='helpcloud' help_width='245' help_height='30' help_html='관리자가 고객을 지정하여 발행하는 성격.'><img src='/admin/images/icon_q.gif' align=absmiddle style='margin:2px 0px 0px 0px;'/></span> 
			  <input type='radio' name='publish_type' id='publish_type_2' onFocus='this.blur();' align='middle' value=2  ".($publish_type == 2 ? "checked":"")."><label for='publish_type_2' class='green'>일반 발행</label>
			  <span style='padding-left:2px' class='helpcloud' help_width='260' help_height='30' help_html='상품에 적용되어 고객이 다운받는 쿠폰성격.'><img src='/admin/images/icon_q.gif' align=absmiddle style='margin:2px 0px 0px 0px;' /></span>
			  <input type='radio' name='publish_type' id='publish_type_3' onFocus='this.blur();' align='middle' value=3 ".($publish_type == 3 ? "checked":"")."><label for='publish_type_3' class='green'>회원가입 발행</label>
			  <span style='padding-left:2px' class='helpcloud' help_width='230' help_height='30' help_html='회원가입시 자동으로 발행되는 성격.'><img src='/admin/images/icon_q.gif' align=absmiddle style='margin:2px 0px 0px 0px;' /></span>";

		 }
		 $Contents .= "
		<br><br>";

		if($view_type=="mem_group"){
		  $Contents .= "
		  <div id='publish_group_area' style='margin-top:20px;width:100%'>
			<table width='60%' border='0' align='left'>
				<tr align='left'>
					<td width='240' >
						<img src='../images/icon/pop_all.gif' alt='전체선택' title='전체선택' onclick=\"SelectedAll($('#gp_list option'),'selected')\" style='cursor:pointer;'/>
					</td>
				</tr>
				<tr>
					<td width='300'>
						<table width='100%' border='0' align='center'>
							<tr>
								<td colspan='2'>
									<select name='gp_list[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;' class='gp_list' id='gp_list'  multiple>";
										
										$db->query("SELECT g.* FROM shop_cupon_relation_group crg left join ".TBL_SHOP_GROUPINFO." g on (crg.gp_ix=g.gp_ix)  where publish_tmp_ix='".$publish_tmp_ix."' order by g.gp_level asc  ");
										$relation_group_array=$db->fetchall();

										$db->query("SELECT * FROM ".TBL_SHOP_GROUPINFO." where disp='1' order by gp_level asc ");

										for($i=0;$i<$db->total;$i++){
											$db->fetch($i);

											$multiple_select_bool=true;

											for($j=0;$j<count($relation_group_array);$j++){
												if($relation_group_array[$j][gp_ix]==$db->dt[gp_ix]){
													$multiple_select_bool=false;
												}
											}

											if($multiple_select_bool){
												$Contents .="<option value='".$db->dt[gp_ix]."'>[level(".$db->dt[gp_level].")] ".$db->dt[gp_name]."</option>";
											}
										}

								$Contents .="
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td align='center'>
						<div class='float01 email_btns01'>
							<ul>
								<li>
									<a href=\"javascript:MoveSelectBox2('ADD');\"><img src='../images/icon/pop_plus_btn.gif' alt='추가' title='추가' /></a>
								</li>
								<li>
									<a href=\"javascript:MoveSelectBox2('REMOVE');\"><img src='../images/icon/pop_del_btn.gif' alt='삭제' title='삭제' /></a>
								</li>
							</ul>
						</div>
					</td>
					<td>
					<td width='300'>
						<table width='100%' border='0' align='center'>
						<tr>
							<td colspan='2'>
								<select name='select_gp_list[]' style='border:solid 1px #ddd;width:300px;height:148px;font-size:12px;background:#fff;' id='select_gp_list' multiple>";

								for($i = 0; $i<count($relation_group_array); $i++){
									$Contents .="<option value='".$relation_group_array[$i][gp_ix]."'>[level(".$relation_group_array[$i][gp_level].")] ".$relation_group_array[$i][gp_name]."</option>";
								}

								$Contents .="
								</select>
							</td>
						</tr>
						</table>
					</td>
				</tr>
			</table>
		  </div>";
		}else{
			$Contents .= "
          <span class=small><!--지정 발행의 경우 쿠폰 발행후에 사용자를 직접 등록 하시면 됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G')." </span>";
		}
		 $Contents .= "
          </td>
        </tr>";
*/
$Contents .= "
		<tr>
		  <td class='search_box_title' >  <b>발급구분</b></td>
		  <td class='search_box_item' colspan='3'>";
		  foreach($_ISSUE_TYPE as $key => $value){
			$Contents .= "<input type='radio' name='issue_type' id='issue_type_".$key."' class='issue_type' value='".$key."' ".($issue_type == $key ? "checked":"")." validation=true title='".$value[text]."'> <span style='padding-left:2px' class='helpcloud' help_width='380' help_height='10' help_html='".$value["desc"]."'><label for='issue_type_".$key."' >".$value[text]."</label></span> ";
            /*
			if($key=="3"){
				$Contents .= "<input type='text' name='buy_point' value='".$buy_point."' class='textbox numeric' maxlength='4' style='filter: blendTrans(duration=0.5); height: 18px; width: 30px' align='absmiddle'> 개";
			}
            */
		  }
$Contents .= "
				<!--input type='radio' name='issue_type' id='issue_type_1'  align='middle' value='1' ".($issue_type == '1' || $issue_type == '' ? "checked":"")."><label for='issue_type_1' class='green'>즉시발행</label> 
				<input type='radio' name='issue_type' id='issue_type_2'  align='middle' value='2' ".($issue_type == '2' ? "checked":"")."><label for='issue_type_2' class='green'>고객다운로드</label--> 
		  </td>
		  <!--td class='search_box_title' >  <b>C/S 쿠폰여부</b></td>
		  <td class='search_box_item'>
				<input type='checkbox' name='is_cs' id='is_cs_1'  align='middle' value='1' ".($is_cs == '1' ? "checked":"")."><label for='is_cs_1' class='green'>CS쿠폰</label>
		  </td-->
		</tr>
		<tr >
		  <td class='input_box_title' >  <b>노출여부</b></td>
		  <td class='input_box_item'>
				<input type='radio' name='disp' id='disp_1'  align='middle' value='1' ".($disp == '1' || $disp == '' ? "checked":"")."><label for='disp_1' class='green'>노출함</label> 
				<input type='radio' name='disp' id='disp_0'  align='middle' value='0' ".($disp == '0' ? "checked":"")."><label for='disp_0' class='green'>노출안함</label> 
		  </td>
		  <td class='input_box_title' >  <b>사용여부</b></td>
		  <td class='input_box_item'>
				<input type='radio' name='is_use' id='is_use_1'  align='middle' value='1' ".($is_use == '1' || $is_use == '' ? "checked":"")."><label for='is_use_1' class='green'>사용함</label> 
				<input type='radio' name='is_use' id='is_use_0'  align='middle' value='0' ".($is_use == '0' ? "checked":"")."><label for='is_use_0' class='green'>사용안함</label> 
		  </td>
		</tr>
		<tr>
			  <td class='input_box_title' nowrap > <b>노출기간 <!--img src='".$required3_path."'--></b></td>
			  <td class='input_box_item' style='padding:10px;' colspan=3>
				".search_date('cupon_use_sdate','cupon_use_edate',$cupon_use_sdate,$cupon_use_edate,'Y','A')."
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
if($_GET["publish_ix"] == ""){
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>";
	}else{
		$Contents .= "<a href=\"".$auth_write_msg."\"><img  src='../images/".$admininfo["language"]."/b_save.gif' border=0></a>";
	}
}else{
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0>";
	}else{
		$Contents .= "<a href=\"".$auth_update_msg."\"><img  src='../images/".$admininfo["language"]."/b_save.gif' border=0></a>";
	}
}
	$Contents .= "
	</td>
  </tr>
</form>
</table>
  ";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' >생성된 쿠폰을 발행 하는 단계 입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >쿠폰은 전체상품 발행 쿠폰, 카테고리별 상품 발행 쿠폰, 상품별 발행쿠폰 세가지 종류의 쿠폰이 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >쿠폰 발급은 지정발급과 무작위 발급이 있습니다. 지정발급의 경우는 관리자가 직접 사용자를 등록 합니다. </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >쿠폰 발급후 사용기간 변경시 <u>기 발급된 사용자에 대한 사용기간은 변경되지 않습니다.</u> </td></tr>
</table>
";*/
	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .=  HelpBox("쿠폰 발행", $help_text);

if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "init();";
	$P->strLeftMenu = promotion_menu();
	$P->Navigation = "프로모션(마케팅)/전시 > 쿠폰관리 > 쿠폰발행관리";
	$P->title = "쿠폰발행관리";
	$P->NaviTitle = "쿠폰발행관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "init();";
	$P->strLeftMenu = promotion_menu();
	$P->Navigation = "프로모션(마케팅)/전시 > 쿠폰관리 > 쿠폰발행관리";
	$P->title = "쿠폰발행관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


function SelectCuponKind($select_ix, $cupon_div=""){
	$mdb = new Database;
	if($cupon_div){
		$sql = "SELECT * FROM ".TBL_SHOP_CUPON." where cupon_div = '".$cupon_div."' order by cupon_ix asc";
	}else{
	    //쿠폰 구분 선택해야지만 나오도록 수정 처리
		$sql = "SELECT * FROM ".TBL_SHOP_CUPON." where 1=2 order by cupon_ix asc";
	}

	$mdb->query($sql);
	$mstring =  "<select name='cupon_ix' id='cupon_ix' style=\"width: 300px;\" align='middle'><!--behavior: url('../js/selectbox.htc'); -->";

       $mstring .=  "     <option value=''>ㆍ선택ㆍㆍㆍㆍㆍㆍㆍ</option>";


    for($i=0;$i < $mdb->total;$i++){
	 	$mdb->fetch($i);

		if($mdb->dt[cupon_div]=="P"){
			$cupon_div="상품";
		}elseif($mdb->dt[cupon_div]=="D"){
			$cupon_div="배송비";
		}else{
			$cupon_div="-";
		}

	 	if($select_ix == $mdb->dt[cupon_ix]){
	    	$mstring .= "       <option value='".$mdb->dt[cupon_ix]."' selected>".$mdb->dt[cupon_kind]."</option>\n";//ㆍ[".$cupon_div."]
		}else{
			$mstring .= "       <option value='".$mdb->dt[cupon_ix]."'>".$mdb->dt[cupon_kind]."</option>\n";//ㆍ[".$cupon_div."]
		}
	}
    $mstring .= "</select>";

    return $mstring;

}




function relationCouponProductList($publish_ix){
	global $admin_config,$publish_tmp_ix;
	$db = new Database;

	$group_code = 1;
	$disp_type = 'clipart';


	if($publish_tmp_ix!=""){
		$sql = "Select crp.*, p.id, p.pcode, p.shotinfo, p.pname, p.listprice, p.sellprice,  p.wholesale_sellprice, p.wholesale_price,  p.reserve, p.state, p.disp 
					from ".TBL_SHOP_PRODUCT." p, shop_cupon_relation_product crp where p.id = crp.pid and publish_tmp_ix = '".$publish_tmp_ix."' order by crp.vieworder asc ";
	}else{
		$sql = "Select crp.*, p.id, p.pcode, p.shotinfo, p.pname, p.listprice, p.sellprice,  p.wholesale_sellprice, p.wholesale_price,  p.reserve, p.state, p.disp 
					from ".TBL_SHOP_PRODUCT." p, shop_cupon_relation_product crp where p.id = crp.pid and publish_ix = '".$publish_ix."' order by crp.vieworder asc ";
	}
	$db->query($sql);
	$products = $db->fetchall();

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
			}
			//print_r($products);
	}


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
			for($i=0;$i<count($products);$i++){
				$db->fetch($i);
				//$imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $db->dt['id'], 'c');
				//$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$db->dt['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'");'."\n";

				$imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $products[$i]['id'], 'c');
				//$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$products[$i]['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($products[$i]['pname']))).'", "'.addslashes(addslashes(trim($products[$i]['brand_name']))).'", "'.$products[$i]['sellprice'].'");'."\n";

				$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$products[$i]['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($products[$i]['pname']))).'", "'.addslashes(addslashes(trim($products[$i]['brand_name']))).'", "'.$products[$i]['sellprice'].'", "'.$products[$i]['listprice'].'", "'.$products[$i]['reserve'].'", "'.$products[$i]['coprice'].'", "'.$products[$i]['wholesale_price'].'", "'.$products[$i]['wholesale_sellprice'].'", "'.$products[$i]['disp'].'", "'.$products[$i]['state'].'", "'.$products[$i]['dcprice'].'");'."\n";

			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;
}



/*

CREATE TABLE IF NOT EXISTS `shop_cupon_relation_group` (
  `crg_ix` int(8) NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `publish_ix` int(4) NOT NULL COMMENT '쿠폰발행인덱스값',
  `gp_ix` int(4) DEFAULT NULL COMMENT '그룹인덱스값',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`crg_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='회원그룹쿠폰관련그룹' AUTO_INCREMENT=50 ;



CREATE TABLE IF NOT EXISTS `shop_cupon_publish_config` (
  `cpr_ix` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '발행 키값',
  `publish_ix` int(4) unsigned zerofill NOT NULL DEFAULT '0000' COMMENT '발급 키값',
  `publish_type` int(2) NOT NULL DEFAULT '1' COMMENT '발급구분',
  `r_ix` varchar(32) NOT NULL COMMENT '전시매핑키값',
  `vieworder` int(5) NOT NULL DEFAULT '0' COMMENT '노출순서',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '입력 flag',
  `regdate` datetime DEFAULT NULL COMMENT '등록일자',
  PRIMARY KEY (`cpr_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='쿠폰발행 정책 테이블'


*/
?>