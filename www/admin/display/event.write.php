<?
include("../class/layout.class");

//echo $_SERVER["REMOTE_ADDR"];

$sdb = new Database;
if(!$agent_type){
	$agent_type = "W";
}

$slave_db->query("SELECT * FROM ".TBL_SHOP_EVENT." where event_ix != '$event_ix'");
$etc_events = $slave_db->fetchall();

$slave_db->query("SELECT * FROM ".TBL_SHOP_EVENT." where event_ix= '$event_ix'");
//echo "SELECT * FROM ".TBL_SHOP_EVENT." where event_ix= '$event_ix'";

//$up_dir = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/upfile/";
//echo $up_dir;

if($slave_db->total){
	$slave_db->fetch();
	$event_ix = $slave_db->dt[event_ix];
	$mall_ix = $slave_db->dt[mall_ix];
	$er_ix = $slave_db->dt[er_ix];
	$kind = $slave_db->dt[kind];
	
	$manage_title = $slave_db->dt[manage_title];
	$event_title = $slave_db->dt[event_title];
	$event_keyword = $slave_db->dt[event_keyword];
	
	$event_text = $slave_db->dt[event_text];
	$event_text2 = $slave_db->dt[event_text2];
	$b_img_text = $slave_db->dt[b_img_text];
	$b_img_text2 = $slave_db->dt[b_img_text2];
	$event_width = $slave_db->dt[event_width];
	$event_height = $slave_db->dt[event_height];
	$event_top = $slave_db->dt[event_top];
	$event_left = $slave_db->dt[event_left];
	$md_code = $slave_db->dt[md_code];


	//$place_ix = $slave_db->dt[place_ix];
	

	$event_use_sdate = date("Y-m-d H:i:s",$slave_db->dt[event_use_sdate]);
	$event_use_edate = date("Y-m-d H:i:s",$slave_db->dt[event_use_edate]);
	//	echo $event_use_edate;
	/*
		$event_use_sdate = date("Y-m-d",$slave_db->dt[event_use_sdate]);
		$event_use_stime = date("H",$slave_db->dt[event_use_sdate]);
		$event_use_smin = date("i",$slave_db->dt[event_use_sdate]);

		$event_use_edate = date("Y-m-d",$slave_db->dt[event_use_edate]);
		$event_use_etime = date("H",$slave_db->dt[event_use_edate]);
		$event_use_emin = date("i",$slave_db->dt[event_use_edate]);
	*/

	$send_cond = $slave_db->dt[send_cond];
	$wait = $slave_db->dt[wait];
	$send_duration_H = floor(($slave_db->dt[send_duration] / 60000) / 60);
	$send_duration_M = floor(($slave_db->dt[send_duration] / 60000) % 60);
	$wait_duration_H = floor(($slave_db->dt[wait_duration] / 60000) / 60);
	$wait_duration_M = floor(($slave_db->dt[wait_duration] / 60000) % 60);

	$cid = $slave_db->dt[cid];
	$company_id = $slave_db->dt[company_id];
    $b_ix = $slave_db->dt[b_ix];

	$full = $slave_db->dt[full];
	$disp = $slave_db->dt[disp];

    $sort = $slave_db->dt[sort];

	if($mode == "copy"){
		$act = "insert";
	}else{
		$act = "update";
	}

	if($slave_db->dt[worker_ix] !="" &&   $slave_db->dt[worker_ix] != $_SESSION["admininfo"]["charger_ix"]){

		$sql = "select AES_DECRYPT(UNHEX(cmd.name),'".$slave_db->ase_encrypt_key."') as name from ".TBL_COMMON_MEMBER_DETAIL." cmd where code= '".$slave_db->dt[worker_ix]."' ";
		//echo $sql;
		$sdb->query($sql);
		$sdb->fetch();
		$worker_name = $sdb->dt[name];

		echo "	<script language='javascript'>alert('".$worker_name." 님이 작업중입니다. 작업완료 후 다음 작업을 진행 하실 수 있습니다.');history.back();//self.close()</script>";
		exit;
	}
	
	$place_ix = array();
	$slave_db->query("SELECT place_ix FROM shop_event_place_relation where event_ix= '$event_ix'");
	$place_ix_array = $slave_db->fetchall("object");
	if(count($place_ix_array) > 0){
		foreach($place_ix_array as $p_ix){
			$place_ix[] = $p_ix['place_ix'];
		}
	}
 
	$slave_db->query("SELECT * FROM shop_event_config where event_ix= '$event_ix'");
	$slave_db->fetch();
	$event_type = $slave_db->dt[event_type];
	$lottery_type = $slave_db->dt[lottery_type];
	$use_comment = $slave_db->dt[use_comment];
	$use_comment_category = $slave_db->dt[use_comment_category];
	$probability = $slave_db->dt[probability];
	$lottery_probability = $slave_db->dt[lottery_probability];
	
	$lottery_amount = $slave_db->dt[lottery_amount];
	$lottery_method = $slave_db->dt[lottery_method];
	
	$lottery_date = $slave_db->dt[lottery_date];
	$participation_able_times = $slave_db->dt[participation_able_times];
	$participation_method = $slave_db->dt[participation_method];
	$participation_use_point = $slave_db->dt[participation_use_point];
	$participation_saving_point = $slave_db->dt[participation_saving_point];
	$exposure_rate = $slave_db->dt[exposure_rate];
	$lottery_announce = $slave_db->dt[lottery_announce];
	


	$sql = "update ".TBL_SHOP_EVENT." set worker_ix = '".$_SESSION["admininfo"]["charger_ix"]."' where event_ix= '$event_ix' ";
	//echo $sql;
	$sdb->query($sql);


}else{
	$act = "insert";
	$event_use_sdate = "";
	$event_use_edate = "";
	if($admininfo[admin_level] == 9){
		$disp = "1";
	}else{
		$disp = "9";
	}
	$md_code = "";
	$kind = "E";
  
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
  .productList li { width:100px; }
  .productList li.selected { outline: 1px solid red; }

	.slide-up-down-link { display:block;position:absolute;right:55px;top:13px; }
	.slide-up-down-link .plus { display:none; }
	.slide-up-down-link.closed .minus { display:none; }
	.slide-up-down-link.closed .plus { display:inline; }
	.slide-up-down-all { margin-bottom:10px; }
	.slide-up-down-all .slide-up-down-link { 
		position:static; 
		font-weight:bold;
	}
	.slide-up-down-all .slide-up-down-link:hover { text-decoration:none; }
	.slide-up-down-all .slide-up-down-link .label {
		display: inline-block;
		font-size:18px;
		margin-top:2px;
		vertical-align:top;
		*display:inline;
		*zoom:1;
	}
  </style>
<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n
<script type='text/javascript' src='/admin/js/ui/jquery-ui-1.8.9.custom.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.core.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' src='/admin/js/ui/jquery.ui.mouse.js'></script>
<script type='text/javascript' src='/admin/js/jquery.easing-1.3.js'></script>
<script type='text/javascript' src='/admin/js/jquery.quicksand.js'></script>
<script type='text/javascript' src='/admin/js/jquery.multisortable.js'></script>
<script type='text/javascript' src='/admin/js/ms_productSearch.js?=".rand()."'></script>
<script type='text/javascript' src='../display/relationAjaxForEvent.js'></script>
<Script Language='JavaScript'>

$(document).ready(function () {";
if($agent_type == 'M'){
	//모바일 샵에서 기획전 등록일때 이벤트 등록은 안보이도록 처리 2014.07.16 bgh
	$Script .= "
	$('#event_config_tr').hide();
	$('#event_goods_tr').hide();

	$('#kind2').prop('checked',true);
	";
}
$Script .="
	var copy_text;
	$('#flash_addbtn').click(function(){
		
		
		var newRow = $('#picture_puzzle_table tbody tr:last').clone(true).appendTo('#picture_puzzle_table tfoot');  
		newRow.find('.file_text').text('');
		newRow.find('.ep_link').val('');
		newRow.find('.ep_title').val('');
		newRow.find('.ep_ix').val('');
		newRow.find('#delete_btn').show();
		 
	});

	$('#flash_delbtn').click(function(){
		var len = $('#picture_puzzle_table .clone_tr').length;
		if(len > 1){
			eqIndex--;
			$('#picture_puzzle_table .clone_tr:last').remove();
		}else{
			return false;
		}
	});
	
	var type_btn = $('input[id^=display_type]');
	//alert(type_btn);
	if(type_btn.checked == true)
	{
		$(this).hide();
	}

	//click event
	$('.promotion_type_box').click(function(){
		promotion_type_check_reset();
		var img_tag = $(this).find('img');
		img_tag.attr('src',img_tag.attr('src').replace('.png','_on.png'));
		
		$(this).find('input').attr('checked','checked');
	});

});

function promotion_type_check_reset(){
	//img reset
	$('.promotion_types').find('img').each(function( i, element ){
		$(element).attr('src', $(element).attr('src').replace('_on.png', '.png') );
	})
	//checkbox reset
	$('.promotion_types').find('input').attr('checked','');
}


function SubmitX(frm){
	//alert(iView.document.body.innerHTML);
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	//doToggleText(frm);
	//frm.content.value = iView.document.body.innerHTML;
	//frm.content.value = document.getElementById('iView').contentWindow.document.body.innerHTML; //kbk
	return true;
}




function init(){
	var frm = document.event_frm;
	//Content_Input();
	//Init(frm);
	//alert(1);
	";

//if($agent_type == 'W'){
	$Script .= "
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
		menu_subMenuDelay : 0,
		allowedContent : true,";
	if($admininfo[admin_level]==8 && $act=='update' && $disp !='9' ){
		$Script .= "readOnly : true, ";
	}

	$Script .= "
		toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About','ImageMaps']],
		filebrowserImageUploadUrl : '/admin/ckeditor/upload.php',
		height:500});";


	$Script .= "
	CKEDITOR.replace('event_text2',{
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
		menu_subMenuDelay : 0,
		allowedContent : true,";
	if($admininfo[admin_level]==8 && $act=='update' && $disp !='9' ){
		$Script .= "readOnly : true, ";
	}

	$Script .= "
		toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About','ImageMaps']],
		filebrowserImageUploadUrl : '/admin/ckeditor/upload.php',
		height:500});";

//}else{
	

	$Script .= "
	CKEDITOR.replace('b_img_text',{
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
		menu_subMenuDelay : 0,
		allowedContent : true,";
	if($admininfo[admin_level]==8 && $act=='update' && $disp !='9' ){
		$Script .= "readOnly : true, ";
	}

	$Script .= "
		toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About','ImageMaps']],
		filebrowserImageUploadUrl : '/admin/ckeditor/upload.php',
		height:200});";

	$Script .= "
	CKEDITOR.replace('b_img_text2',{
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
		menu_subMenuDelay : 0,
		allowedContent : true,";
	if($admininfo[admin_level]==8 && $act=='update' && $disp !='9' ){
		$Script .= "readOnly : true, ";
	}

	$Script .= "
		toolbar : [['Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','TextColor','BGColor','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Link','Unlink','-','Find','Replace','SelectAll','RemoveFormat','-','Image','Flash','Table','SpecialChar'],'/',['Source','-','ShowBlocks','-','Font','FontSize','Undo','Redo','-','About','ImageMaps']],
		filebrowserImageUploadUrl : '/admin/ckeditor/upload.php',
		height:200});";


//}

$options = '<option value="00000006">이달의 구매혜택</option>';
$Script .= " 
}

function onDropAction(mode, event_ix,pid)
{
	//outTip(img3);
	//alert(1);
	parent.window.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&event_ix='+event_ix+'&pid='+pid;

}

$(window).unload(function() {
	//alert('Handler for .unload() called.');
	//window.frames['act'].location.href ='./event.act.php?act=initialize&event_ix=".$event_ix."';
	$.ajax({
			type: 'GET',
			data: {'act': 'initialize','event_ix':'".$event_ix."'},
			url: '../display/event.act.php',
			dataType: 'html',
			async: false,
			beforeSend: function(){
				//alert('진행중인 이벤트 작업을 종료합니다.');
			},
			success: function(calevents){
				//alert('작업중인 이벤트를 종료합니다.');
				//alert(calevents);
				//$('#row_'+wl_ix).slideRow('up',500);
			}
		});

});

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
	
	
	$('input[name=kind]').click(function (){
	
        if($(this).val() == 'D'){
            $('select[name=er_ix]').find('option').each(function (k,v){ 
                if(v.value == '00000006') { 
                    $(this).remove();                    
                }
            });
            $('select[name=er_ix]').append('".$options."');
        }else{
            $('select[name=er_ix]').find('option').each(function (k,v){ 
                if(v.value == '00000006'){
                    $(this).remove();
                 }                
            });
        }
    });
    //$('#kind1').click();
});

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



</Script>";


$Contents = "
<table width='100%' border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("이벤트/기획전 관리", "전시관리 > 이벤트/기획전 관리 <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_기획전등록(090322)_config.xml',800,517,'manual_view')\"  title='이벤트/기획전 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a>")."</td>
</tr>
 <tr>
	<td align='left' colspan=6 >
	<div style='width:97%;background-color:#fff7da;padding:20px;text-align:center;margin-bottom:4px;'>
	<b style='font-size:17px;'> 기획전 불러오기 : </b>
	<select name='' onchange=\"document.location.href='?mode=copy&event_ix='+this.value\" style='font-size:15px;height:30px;'>";
	$Contents .= "<option value=''>등록된 기획전 목록</option>";
	for($i=0; $i < count($etc_events);$i++){
		$Contents .= "<option value='".$etc_events[$i][event_ix]."' ".($etc_events[$i][event_ix] == $_GET["event_ix"] ? "selected":"").">".($etc_events[$i][agent_type] == "W" ? "[WEB]":"[MOBILE]")." ".$etc_events[$i][event_title]."</option>";
	}
$Contents .= "
	</select>
	</div>
	</td>
</tr>
  <tr>
    <td>

        <form name='event_frm' method='post' onSubmit=\"return SubmitX(this)\" action='../display/event.act.php' style='display:inline;' enctype='multipart/form-data' target='iframe_act'>
		<input type='hidden' name=act value='$act'>
		<input type='hidden' name=event_ix value='$event_ix'>
		<input name='agent_type' type='hidden' value='$agent_type'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td >
                    <table border='0' cellpadding=3 cellspacing=0 width='100%' class='input_table_box'>
						<col width='15%'>
						<col width='35%'>
						<col width='15%'>
						<col width='35%'>";
					if($_SESSION["admin_config"][front_multiview] == "Y"){
					$Contents .= "
					<tr>
						<td class='search_box_title' > 프론트 전시 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
					}
					
					$Contents .= "
						<tr height=27>
                      	<td class='input_box_title'> <b>이벤트/기획전 구분 <img src='".$required3_path."'></b></td>
                        <td class='input_box_item'>";
                    	$Contents .= "
							<input type='radio' name='kind' id='kind1' value='E' ".CompareReturnValue("E",$kind,"checked")." onclick=\"$('#event_config_tr').hide();$('#event_goods_tr').hide();\"> <label for='kind1' >이벤트</label>";
						$Contents .= "
							<input type='radio' name='kind' id='kind2' value='P' ".CompareReturnValue("P",$kind,"checked")." onclick=\"$('#event_config_tr').hide();$('#event_goods_tr').hide();\"><label for='kind2' >기획전</label>
							<!--<input type='radio' name='kind' id='kind3' value='D' ".CompareReturnValue("D",$kind,"checked")." onclick=\"$('#event_config_tr').hide();$('#event_goods_tr').hide();\"><label for='kind3' >DEWYTREE STORY</label>-->
						</td>
						<td class='input_box_title'> <b>전시여부 <img src='".$required3_path."'></b></td>
                        <td class='input_box_item' >";
						if($admininfo[admin_level] == 9){
			$Contents .= "
                            <!--<input type='radio' name='disp' id='disp_9' value='9' ".CompareReturnValue("9",$disp,"checked")."><label for='disp_9' >신청</label>-->
							<input type='hidden' name='pop' value='1'> <input type='radio' name='disp' id='disp_1' value='1' ".CompareReturnValue("1",$disp,"checked")."> <label for='disp_1' >사용</label>
							<input type='radio' name='disp' id='disp_0' value='0' ".CompareReturnValue("0",$disp,"checked")."><label for='disp_0' >미사용</label>";
							if($agent_type == 'M'){
								$Contents .="
								<input type='radio' name='disp' id='disp_2' value='2' ".CompareReturnValue("2",$disp,"checked")."> <label for='disp_2' >미노출</label>
								";
							}
						}else{
			$Contents .= "<input type='radio' name='disp' id='disp_9' value='9' ".($disp == "9" || $disp == "" ? "checked":"")."><label for='disp_9' >신청</label>";
						}
			$Contents .= "
						</td>
						</tr>";

	if($admininfo["admin_level"] == 9 && ($admininfo[mall_type] == "B"  || $admininfo[mall_type] == "O"  || $admininfo[mall_type] == "E")){
$Contents .= "
						<tr height=27>
							<td class='input_box_title'> <b>분류선택</b></td>
							<td class='input_box_item' >
								<table cellpadding=0 cellspacing=0 >
									<tr>
										<td><input type='hidden' name='pop' value='1'>".SelectEventCate($er_ix)."</td>
										<td style='padding-left:3px;'><a href=\"javascript:PoPWindow3('event_category.php?mmode=pop',960,400,'company')\"'><img src='../images/".$admininfo["language"]."/btn_pop_manage.gif' align=absmiddle border=0></a></td>
									</tr>
								</table>
							</td>
							<td class='input_box_title'> 등록(관리)업체 </td>
							<td class='input_box_item' >
							".companyAuthList($company_id , "validation=false title='입점업체' ")."
							</td>

						</tr>";
	}else{
$Contents .= "
						<tr height=27>
							<td class='input_box_title'> <b>분류선택 <img src='".$required3_path."'></b></td>
							<td class='input_box_item' colspan=3>
								<table cellpadding=0 cellspacing=0 >
									<tr>
										<td><input type='hidden' name='pop' value='1'>".SelectEventCate($er_ix)."</td>
										<td style='padding-left:3px;'><a href=\"javascript:PoPWindow3('event_category.php?mmode=pop',960,600,'company')\"'><img src='../images/".$admininfo["language"]."/btn_pop_manage.gif' align=absmiddle border=0></a></td>
									</tr>
								</table>
							</td>
						</tr>";
	}

if($agent_type == "M"  || $agent_type == "mobile"){
$Contents .= "  <tr>
							<td class='search_box_title' >  담당 MD</td>
							<td class='search_box_item'  >  ".MDSelect($md_code)."</td>
							<td class='input_box_title' > 플레이스  </td>
							<td class='input_box_item'  >".getEventPlaceInfo($place_ix,'multiple')." </td>
					   </tr>
					   <!--tr>
							<td class='search_box_title' >  유형</td>
							<td class='search_box_item' colspan='3'  >
								<label><input type='radio' name='send_cond' ".($send_cond == "I" || !$send_cond ? "checked" : "")." onclick='timeCheck(this);' value='I'/>Check In</label>
								&nbsp;
								<label><input type='radio' name='send_cond' ".($send_cond == "S" ? "checked" : "")." time=true onclick='timeCheck(this);' value='S'/>Stay</label>
								&nbsp;
								<input type='text' name='send_duration_H' class='textbox' maxlength='4' value='".($send_duration_H ? $send_duration_H : 0)."' style='width:30px;'/> 시간
								&nbsp;
								<input type='text' name='send_duration_M' class='textbox' maxlength='2' value='".($send_duration_M ? $send_duration_M : 0)."' style='width:15px;'/> 분 경과 후
								&nbsp;
								<label><input type='radio' name='send_cond' ".($send_cond == "O" ? "checked" : "")." onclick='timeCheck(this);' value='O'/>Out</label>
							</td>
						</tr>
						<tr>
							<td class='search_box_title' > 이벤트 노출설정</td>
							<td class='search_box_item' colspan='3'  >
								<label><input type='radio' name='wait' ".($wait == "N" || !$wait ? "checked" : "")." onclick='timeCheck(this);' value='N'/>일회성</label>
								&nbsp;
								<label><input type='radio' name='wait' ".($wait == "Y" ? "checked" : "")." time=true onclick='timeCheck(this);' value='Y'/>지속성</label>
								&nbsp;
								<input type='text' name='wait_duration_H' class='textbox' maxlength='4' value='".($wait_duration_H ? $wait_duration_H : 0)."' style='width:30px;'/> 시간
								&nbsp;
								<input type='text' name='wait_duration_M' class='textbox' maxlength='2' value='".($wait_duration_M ? $wait_duration_M : 0)."' style='width:15px;'/> 분 마다 노출
							</td>
					   </tr--> 
					   ";
}else{
$Contents .= "  <tr>
							<td class='search_box_title' >  담당 MD</td>
							<td class='search_box_item' >  ".MDSelect($md_code)."</td>
							<td class='search_box_title' > 관련 브랜드</td>
							<td class='search_box_item'>".BrandListSelect($b_ix,"")."</td>
							<!--td class='search_box_title' >  매출목표</td>
							<td class='search_box_item'><input class='textbox number' type='text' name='goal_amount' size=15 value='".$goal_amount."' maxlength='25'> 원</td-->
					   </tr>  
					   ";
}
$Contents .= "
					  <!--
					  <tr height=27>
                      	<td class='input_box_title' > 카테고리 선택 </td>
						    <td class='input_box_item'>
						        ".categorySelect($cid)."
						         <span class='small'><br/>* 카테고리를 선택하지 않으시면 원하시는 위치에 배너를 추가하시면됩니다.</span>
						    </td>
						    <td class='input_box_title' > 노출순서 </td>
						    <td class='input_box_item'>
						        <input type=text class='textbox number' name='sort' style='width:50px;' value='".($sort != 999 ? $sort : "")."'>
						        <span class='small'>* 노출순서는 0~999 사이의 숫자로 입력해주세요.</span>
						    </td>
					  </tr>
					  -->
                   <tr height=28>
                     <td class='input_box_title' >
							 <b>이벤트/기획전 제목 <img src='".$required3_path."'></b>
						</td>
                        <td class='input_box_item' colspan=3>
							<input type='text' name='event_title' class='textbox' value='".$event_title."' maxlength='50' style='width:98%' validation='true' title='이벤트/기획전제목'>
						</td>
                      </tr>
						<tr>
							<td class='search_box_title' > 이벤트/기획전 간단설명</td>
							<td class='search_box_item' colspan=3><input type='text' name='manage_title' class='textbox' value='".$manage_title."' maxlength='50' style='width:98%' validation='false' title='이벤트/기획전 간단설명'> </td>
						</tr>
					  <tr height=28>
                        <td class='input_box_title' >
							 <b>이벤트/기획전 검색어 <img src='".$required3_path."'></b>
						</td>
                        <td class='input_box_item' colspan=3>
							<input type='text' name='event_keyword' class='textbox' value='".$event_keyword."' maxlength='50' style='width:98%' validation='true' title='이벤트/기획전 키워드'>
						</td>
                      </tr>
                      <tr height=80 >
						  <td class='input_box_title'> <b>이벤트/기획전 기간 <img src='".$required3_path."'></b></td>
						  <td class='input_box_item' colspan=3> 
						  ".search_date('event_use_sdate','event_use_edate',$event_use_sdate,$event_use_edate,'Y',' ' ,' validation=true title="이벤트/기획전 기간" ')."";
						  /*
$Contents .= "
							<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff >
								<tr>
									<TD width=80px nowrap>
									<input type='text' name='event_use_sdate' class='textbox' value='".$event_use_sdate."' style='height:18px;width:70px;text-align:center;' id='start_datepicker'>
									</td>
									<td align=center width=20px  style='text-align:center;'> 일</td>
									<td nowrap>
									<SELECT name=event_use_stime>";

									for($i=0;$i < 24;$i++){
				$Contents .= "<option value='".$i."' ".($event_use_stime == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 시
									<SELECT name=event_use_smin>";
									for($i=0;$i < 60;$i++){
				$Contents .= "<option value='".$i."' ".($event_use_smin == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 분
									</TD>
									<TD width=30px  align=center> ~ </TD>
									<TD width=80px nowrap>
									<input type='text' name='event_use_edate' class='textbox' value='".$event_use_edate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'>
									</td>
									<td align=center width=20px > 일</td>
									<td nowrap>
									<SELECT name=event_use_etime>";

									for($i=0;$i < 24;$i++){
				$Contents .= "<option value='".$i."' ".($event_use_etime == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 시
									<SELECT name=event_use_emin>";
									for($i=0;$i < 60;$i++){
				$Contents .= "<option value='".$i."' ".($event_use_emin == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 분
									</TD>";
				
							
							$today = date("Y-m-d");
							$vyesterday = date("Y-m-d", time()+86400);
							$voneweekago = date("Y-m-d", time()+86400*7);
							$v15ago = date("Y-m-d", time()+86400*15);
							$vonemonthago = date("Y-m-d",mktime(0,0,0,substr($today,5,2)+1,substr($today,8,2)+1,substr($today,0,4)));
							$v2monthago = date("Y-m-d",mktime(0,0,0,substr($today,5,2)+2,substr($today,8,2)+1,substr($today,0,4)));
							$v3monthago = date("Y-m-d",mktime(0,0,0,substr($today,5,2)+3,substr($today,8,2)+1,substr($today,0,4)));

				$Contents .= "
									<td>
										<div style='padding:7px 10px;'>
											<a href=\"javascript:event_select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
											<a href=\"javascript:event_select_date('$today','$voneweekago',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
											<a href=\"javascript:event_select_date('$today','$v15ago',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
											<a href=\"javascript:event_select_date('$today','$vonemonthago',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
											<a href=\"javascript:event_select_date('$today','$v2monthago',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
											<a href=\"javascript:event_select_date('$today','$v3monthago',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
										</div>
										
										<script type='text/javascript'>
										<!--
											function event_select_date(FromDate,ToDate,dType) {
												//alert($('#event_select_date').attr('disabled'));
												if($('#start_datepicker').attr('disabled') == 'disabled'){
													alert('비활성화 상태에서는 날짜 선택이 불가합니다.');
												}else{
													var frm = document.search_frm;
													$('#start_datepicker').val(FromDate);
													$('#end_datepicker').val(ToDate);
												}
											}
										//-->
										</script>
									</td>
								</tr>
							</table>";
							*/
							$Contents .= "
						  </td>
						</tr>
						<tr>
                        <td class='input_box_title'> 댓글 사용여부</td>
                        <td class='input_box_item' style='padding:5px;' colspan=3>
                            <input type='radio' name='use_comment' id='use_comment_2' value='2' ".("2" == $use_comment || "" == $use_comment  ? "checked":"")."><label for='use_comment_2' >사용안함</label>
                            <input type='radio' name='use_comment' id='use_comment_1' value='1' ".("1" == $use_comment ? "checked":"")."> <label for='use_comment_1' >댓글 게시판 사용</label>
                        </td>
                    </tr>";
							if(false) {
                                $Contents .= "
						<tr height=27>
							<td class='input_box_title'>
								기획전 작은배너이미지<br />
								L08, F02 (215 x 200)
								<br /><br />
								이벤트 작은배너이미지<br />
								(250 x 140)<br />
							</td>
							<td class='input_box_item' colspan=3 style='padding:5px;'>
								<table cellpadding=0 cellspacing=0>
									<tr>
										<td><input type='file' class='textbox' name='s_img'></td>
										<td style='padding:0px 3px;'><input type='checkbox' name='img_del' id='img_del' value='1'></td>
										<td><label for='img_del'>이미지 삭제</label></td>
									</tr>
								</table>";

                                if (file_exists($_SERVER["DOCUMENT_ROOT"] . "" . $admin_config[mall_data_root] . "/images/event/$event_ix/event_banner_" . $event_ix . ".gif")) {
                                    $Contents .= "<br><img src='" . $admin_config[mall_data_root] . "/images/event/$event_ix/event_banner_" . $event_ix . ".gif' align=absmiddle>";
                                }
                                $Contents .= "
							</td>
						</tr>";
                            }
					  if($agent_type == "M"){
					$Contents .= "
						<tr height=27>
							<td class='input_box_title'> 기획전 큰배너이미지<br />F01 (770 x 345) </td>
							<td class='input_box_item' colspan=3 style='padding:5px;'>
								<!--table cellpadding=0 cellspacing=0>
									<tr>
										<td><input type='file' class='textbox' name='b_img'></td>
										<td style='padding:0px 3px;'><input type='checkbox' name='b_img_del' id='b_img_del' value='1'></td>
										<td><label for='img_del'>이미지 삭제</label></td>
									</tr>
								</table-->
							";

if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/b_event_banner_".$event_ix.".gif")){
	$Contents .= "<br><img src='".$admin_config[mall_data_root]."/images/event/$event_ix/b_event_banner_".$event_ix.".gif' align=absmiddle>";
}
$Contents .= "
								<table cellpadding=0 cellspacing=0 width=100%>
									<tr>
										<td height='30' colspan='3' style='padding:10px;'>
											<textarea name='b_img_text' id='b_img_text' style='width:98%;height:100px;display:block' $readonly>".$b_img_text."</textarea>
									  </td>
									</tr>
								</table>
							</td>
						</tr>
						<tr height=27>
							<td class='input_box_title'> 기획전 큰배너이미지2<br />F01 (770 x 345) </td>
							<td class='input_box_item' colspan=3 style='padding:5px;'>
								<table cellpadding=0 cellspacing=0 width=100%>
									<tr>
										<td height='30' colspan='3' style='padding:10px;'>
											<textarea name='b_img_text2' id='b_img_text2' style='width:98%;height:100px;display:block' $readonly>".$b_img_text2."</textarea>
									  </td>
									</tr>
								</table>
							</td>
						</tr>
						
						";
					  }
					  $Contents .= "
						<tr ".($kind == "P" || $kind == "D" || $kind == "E" ? "style='display:none;' ":"")." id='event_config_tr'>
							<td class='input_box_title'> 이벤트 설정 </td>
							<td align='left' colspan=3 style='background-color:#ffffff;padding:10px;'>
								<div class='tab' style='width:100%;height:49px;display:none;'>
								<table width='100%' class='s_org_tab'>				
								<tr>							
									<td class='tab' >
										<table id='tab_1' class='on'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02' >미사용</td>
											<th class='box_03'></th>							
										</tr>
										</table>
										<table id='tab_2' >
										<tr>
											<th class='box_01'></th>
											<td class='box_02' >댓글 이벤트</td>
											<th class='box_03'></th>				
										</tr>
										</table>
										<table id='tab_3' >
										<tr>
											<th class='box_01'></th>
											<td class='box_02' >체험단 이벤트</td>
											<th class='box_03'></th>				
										</tr>
										</table>
										<table id='tab_4' >
										<tr>
											<th class='box_01'></th>
											<td class='box_02' >당첨(즉석)이벤트</td>
											<th class='box_03'></th>				
										</tr>
										</table>
									</td>							
									<td align='right'>
										
									</td>
								</tr>
								</table>										
								</div>
								<div style='padding:0px;'>
								 <table border='0' cellpadding=3 cellspacing=0 width='100%' class='input_table_box'>
									<col width='20%'>
									<col width='30%'>
									<col width='20%'>
									<col width='30%'>
									<tr height=27>
										<td class='input_box_title'> 이벤트 </td>
										<td class='input_box_item' colspan=3 style='padding:5px;'>
											<input type='radio' name='event_type' id='event_type_' value='' ".("" == $event_type ? "checked":"")." onclick='selectEventType(this)'> <label for='event_type_' >미사용</label><br>
											<input type='radio' name='event_type' id='event_type_1' value='1' ".("1" == $event_type ? "checked":"")." onclick='selectEventType(this)'> <label for='event_type_1' >댓글 이벤트 (댓글을 달고 마일리지, 포인트 및 상품등을 추천하는 이벤트)</label><br>
											<!--input type='radio' name='event_type' id='event_type_2' value='2' ".("2" == $event_type ? "checked":"")." onclick='selectEventType(this)'> <label for='event_type_2' >체험단 이벤트 (상품에 응모하여 추첨 및 선착순을 통해 샘플을 제공하는 이벤트)</label><br>
											<input type='radio' name='event_type' id='event_type_3' value='3' ".("3" == $event_type ? "checked":"")." onclick='selectEventType(this)'> <label for='event_type_3' >당첨(즉석) 이벤트 (응모 참여시 바로 당첨 여부를 알 수 있는 이벤트)</label><br>
											<input type='radio' name='event_type' id='event_type_4' value='4' ".("4" == $event_type ? "checked":"")." onclick='selectEventType(this)'> <label for='event_type_4' >응모이벤트 (일정 기간 응모를 통해 추첨 혹은 선착순으로 진행되는 이벤트)</label><br>
											<input type='radio' name='event_type' id='event_type_5' value='5' ".("5" == $event_type ? "checked":"")." onclick='selectEventType(this)'> <label for='event_type_5' >포인트 상품 구매이벤트 (포인트로 상품 혹은 이벤트를 참여할 수 있는 이벤트)</label><br>
											<input type='radio' name='event_type' id='event_type_6' value='6' ".("6" == $event_type ? "checked":"")." onclick='selectEventType(this)'> <label for='event_type_6' >숨은그림 찾기 이벤트 (주어진 숨은그림을 찾는 이벤트)</label><br-->
										</td>
									</tr>
									";
					if($event_ix){
						$Contents .= "
									<tr>
										<td class='input_box_title'> 댓글 분류사용</td>
										<td class='input_box_item' style='padding:5px;' colspan=3>
											<input type='radio' name='use_comment_category' id='use_category_0' value='0' ".(("0" == $use_comment_category || "" == $use_comment_category) ? "checked":"")."> 
											<label for='use_category_0' >미사용</label>
											<input type='radio' name='use_comment_category' id='use_category_1' value='1' ".("1" == $use_comment_category ? "checked":"").">
											<label for='use_category_1' >사용</label>
											<a href=\"javascript:PoPWindow3('event_comment_category.php?event_ix=$event_ix&mmode=pop',960,600,'company')\"'><img src='../images/".$admininfo["language"]."/btn_pop_manage.gif' align=absmiddle border=0></a>
										</td>
									</tr>";
					}else{
						$Contents .= "<tr style='display:none;'><td colspan=4><input type='hidden' name='use_comment_category' id='use_category_0' value='0' checked></td></tr>";
					}
						$Contents .= "
									<tr height=27>
										<td class='input_box_title'> 추첨인원</td>
										<td class='input_box_item' colspan=3 style='padding:5px;'>
											<input type='radio' name='lottery_type' id='lottery_type_1' value='1' ".(("1" == $lottery_type || "" == $lottery_type) ? "checked":"")."> <label for='lottery_type_1' >전체</label>
											<input type='radio' name='lottery_type' id='lottery_type_2' value='2' ".("2" == $lottery_type ? "checked":"")."><label for='lottery_type_2' >추첨(랜덤)</label> (당첨확률 : <input type=text class='textbox numeric' name='lottery_probability' id='lottery_probability' style='width:50px;' value='".$lottery_probability."'> %)
											<input type='radio' name='lottery_type' id='lottery_type_3' value='3' ".("3" == $lottery_type ? "checked":"")."><label for='lottery_type_3' >추첨(관리자 수동선택)</label>
											<input type='radio' name='lottery_type' id='lottery_type_5' value='5' ".("5" == $lottery_type ? "checked":"")."><label for='lottery_type_5' >선착순</label>
											<input type=text class='textbox numeric' name='lottery_amount' id='lottery_amount' style='width:50px;' value='$lottery_amount'> 명
										</td>
									</tr>
									<tr height=27>
										<td class='input_box_title'> 추첨방식</td>
										<td class='input_box_item' colspan=3 style='padding:5px;'>
											<input type='radio' name='lottery_method' id='lottery_method_1' value='1' ".(("1" == $lottery_method || "" == $lottery_method) ? "checked":"")."> <label for='lottery_method_1' >즉시당첨</label>
											<input type='radio' name='lottery_method' id='lottery_method_2' value='2' ".("2" == $lottery_method ? "checked":"")."><label for='lottery_method_2' >추첨일 당첨</label>
											<input type=text class='textbox' name='lottery_date' id='lottery_date' style='width:100px;' value='$lottery_date'>
										</td>
									</tr>
									<tr height=27>
										<td class='input_box_title'> 참여횟수</td>
										<td class='input_box_item' style='padding:5px;'>
											<input type='radio' name='participation_able_times' id='participation_able_times_1' value='1' ".(("1" == $participation_able_times || "" == $participation_able_times) ? "checked":"")."> <label for='participation_able_times_1' >1일 / 1회 (ID) </label>
											<input type='radio' name='participation_able_times' id='participation_able_times_2' value='2' ".("2" == $participation_able_times ? "checked":"")."><label for='participation_able_times_2' >이벤트 기간내 1회 (ID)</label>
										</td>
										<td class='input_box_title'> 참여방식</td>
										<td class='input_box_item' style='padding:5px;'>
											<input type='radio' name='participation_method' id='participation_method_1' value='1' ".(("1" == $participation_method || "" == $participation_method) ? "checked":"")."> <label for='participation_method_1' >참여신청</label>
											<input type='radio' name='participation_method' id='participation_method_2' value='2' ".("2" == $participation_method ? "checked":"")."><label for='participation_method_2' >회원상품선택</label>
										</td>
									</tr>
									<tr height=27>
										<td class='input_box_title'> 포인트 차감</td>
										<td class='input_box_item' style='padding:5px;'>											
											<input type=text class='textbox numeric' name='participation_use_point' id='participation_use_point' style='width:50px;' value='$participation_use_point'> 점 (0점 일경우 차감되지 않습니다.)
										</td>
										<td class='input_box_title'> 포인트 적립</td>
										<td class='input_box_item'  style='padding:5px;'>											
											<input type=text class='textbox numeric' name='participation_saving_point' id='participation_saving_point' style='width:50px;' value='$participation_saving_point'> 점 (0점 일경우 적립 되지 않습니다.)
										</td>
									</tr>
									<tr >
										<td class='input_box_title'> 당첨안내</td>
										<td class='input_box_item'  colspan=3 style='height:120px;' >
										<textarea class='textbox' name='lottery_announce' style='padding:5px;width:97%;height:100px;'>".$lottery_announce."</textarea>
										</td>
									</tr>";
if($_SERVER["REMOTE_ADDR"] == "175.121.188.179" || true){
$Contents .= "
									<tr bgcolor=#ffffff id='event_picturepuzzles_area'  ".("6" == $event_type ? "":"style='display:none;'").">
										<td class='input_box_title' style='vertical-align:top;padding-top:20px;'> <b>숨은그림찾기 이미지 </b> <img src='../images/".$admininfo["language"]."/btn_add.gif' alt='옵션추가' id='flash_addbtn'   align=absmiddle style='cursor:pointer;'> </td>
										<td class='input_box_item' colspan=3 style='padding:5px 15px;'>";

		$Contents .= "
											<table cellpadding=0 cellspacing=0 border='0' align='left' id='picture_puzzles_table' >
											<tr>
												<td><b>노출률 : </b><input type='text' class='textbox numeric' name='exposure_rate' id='exposure_rate' style='width:50px;' value='".$exposure_rate."'> % (노출률은 1~100까지 숫자로 입력해주시기 바랍니다.)</td>
												<td align=left></td>
											</tr>
											";
$slave_db->query("SELECT * FROM shop_event_picturepuzzle  where event_ix = '".$event_ix."' order by ep_ix ASC ");//order by 가 regdate 로 되어 있던 것을 고침 kbk 13/02/15
	if($slave_db->total){
		$picturepuzzles = $slave_db->fetchall();
	}
$clon_no = 0;
//if(is_array($picturepuzzles)){
	//foreach($picturepuzzles as $_key=>$_value){
	for($i=0;($i < count($picturepuzzles) || $i <  1);$i++){
/*
		if($_key == 0) {
		$Contents .= "<tbody>";
		} else if($_key == 1){
		$Contents .= "<tfoot>";
		}
		*/
		$Contents .= "
				  <tr bgcolor=#ffffff  class='clone_tr'>
					<td height='25' style='padding:10px 0; solid #d3d3d3;' >

					<input type=hidden name='event_picturepuzzles[".$i."][ep_ix]' id='ep_ix' class='ep_ix' value='".$picturepuzzles[$i][ep_ix]."' style='width:230px;' validation=false>
					 첨부파일 : <input type=file class='textbox' name='event_picturepuzzles[".$i."][ep_file]' id='ep_file' class='ep_file'  style='width:255px;'     title='파일'> <span class='file_text helpcloud'  help_width='200' help_height='30' help_html=\"선택 해제후 저장하시면 해당이미지가 삭제되게 됩니다.\"><b id='ep_file_text'>".$picturepuzzles[$i][ep_file]."</b>
					 <input type='checkbox' name='event_picturepuzzles[".$i."][nondelete]' id='nondelete' value='1' checked><label for='non_delete_".$picturepuzzles[$i][ep_ix]."'>업로드된 파일유지</label></span><br><br>
					
					 설 명 : <input type=text class='textbox ep_title' name='event_picturepuzzles[".$i."][ep_title]' value='".$picturepuzzles[$i][ep_title]."' id='ep_title' class='ep_title' style='width:130px;'  title='설명'>
					 &nbsp;&nbsp;&nbsp;&nbsp;링 크 : <input type=text class='textbox ep_link' name='event_picturepuzzles[".$i."][ep_link]' id='ep_link' class='ep_link' value='".$picturepuzzles[$i][ep_link]."' style='width:248px;'  title='링크'>
					</td>
					";
					if($picturepuzzles[$i][ep_file] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/".$picturepuzzles[$i][event_ix]."/puzzle/".$picturepuzzles[$i][ep_file])){
					//	exit;
					$image_info = getimagesize ($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/event/".$picturepuzzles[$i][event_ix]."/puzzle/".$picturepuzzles[$i][ep_file]);
					$Contents .= "<td style='padding:5px;'>
						<img src='".$admin_config[mall_data_root]."/images/event/".$picturepuzzles[$i][event_ix]."/puzzle/".$picturepuzzles[$i][ep_file]."' id='picturepuzzle_img' style='vertical-align:middle;margin:3px;'  ".($_COOKIE[banner_image_view] == 1 ? "":"height=50")." class='helpcloud' help_width='".($image_info[0]+20)."' help_height='".($image_info[1])."' help_html=\"<img src='".$admin_config[mall_data_root]."/images/event/".$picturepuzzles[$i][event_ix]."/puzzle/".$picturepuzzles[$i][ep_file]."' >\" style='cursor:pointer;'>
						</td>";
					}

		$Contents .= "
					
					<td style='vertical-align:middle;padding:10px;'>
					<img src='../images/".$admininfo["language"]."/btn_del.gif' alt='이미지삭제' id='delete_btn'  onclick=\"delete_puzzle_img($(this).parent().parent())\" ".($i == 0 ? "style='display:none;' ":"").">
					</td>
				  </tr>
				  ";

		if($i == 0) {
//		$Contents .= "</tbody>";
		} else {
			$clon_no++;
		}
	}
//}

$Contents .="
										
								</table>";
}
$Contents .= "
										</td>
									</tr>
									</table>
								</div>
							</td>
						</tr>
						<tr ".($kind == "P" || $kind == "D" || $kind == "E" ? "style='display:none;' ":"")." id='event_goods_tr'>
							<td class='input_box_title'> 이벤트 상품 <a onclick=\"AddGift('event_gift_table');\" ><img src='../images/".$admininfo["language"]."/btn_add.gif' border=0 style='margin:2px 0 3px 5px; vertical-align:middle;'></a> </td>
							<td align='left' colspan=3 style='background-color:#ffffff;padding:10px;'>
								<table cellpadding=0 cellspacing=0 width='100%' class='list_table_box' id='event_gift_table'>
									<col width=8%>
									<col width=*>
									<col width=15%>
									<col width=20%>
									<col width=15%>
									<col width=15%>
									<tr align=center height=27>
										<td class='s_td' >순위</td>
										<td class='m_td' > 상품명 </td>
										<td class='m_td' > 상품종류</td>			
										<td class='m_td' > 코드/포인트</td>
										<td class='m_td' > 당첨인원</td>
										<td class='e_td'> 포인트  차감</td>
									</tr>";
$i=0;
$slave_db->query("SELECT * FROM shop_event_gift where event_ix= '$event_ix'");

for($i=0;($i < $slave_db->total || $i < 1);$i++){
$slave_db->fetch($i);
$Contents .= "
									<tr align=center height=30 depth=1>
										<td class='input_box_title' style='padding:0px;text-align:center;' >
											<div id='event_rank'>".($i+1)."</div>
										</td>
										<td class='list_box_item' style='padding:0px;text-align:center;'>
											<input type=hidden  name='event_gift[".$i."][eg_ix]' id='eg_ix' value='".$slave_db->dt[eg_ix]."'>
											<input type=hidden  name='event_gift[".$i."][ranking]' id='ranking' value='".($slave_db->dt[ranking] == "0" ? ($i+1):$slave_db->dt[ranking])."'>
											<input type=text class='textbox point_color' name='event_gift[".$i."][gift_name]' id='gift_name' style='width:90%;' value='".$slave_db->dt[gift_name]."'>
										</td>
										<td class='list_box_item' >
											<select name='event_gift[".$i."][gift_type]' id='gift_code' style='width:90%;' >
											<option value=''>상품타입</option>
											<option value='1' ".($slave_db->dt[gift_type] == "1" ? "selected":"").">마일리지</option>
											<option value='2' ".($slave_db->dt[gift_type] == "2" ? "selected":"").">상품</option>
											<option value='3' ".($slave_db->dt[gift_type] == "3" ? "selected":"").">큐피콘</option>
											<option value='4' ".($slave_db->dt[gift_type] == "4" ? "selected":"").">쿠폰</option>
											<option value='5' ".($slave_db->dt[gift_type] == "5" ? "selected":"").">기타</option>
											</select>
										</td>
										<td class='list_box_item' >
											<input type=text class='textbox point_color number' name='event_gift[".$i."][gift_code]' id='gift_code' style='width:90%;' value='".$slave_db->dt[gift_code]."'>
										</td>
										<td class='list_box_item' >
											<input type=text class='textbox number' name='event_gift[".$i."][gift_amount]' id='gift_amount' style='width:70px;' value='".$slave_db->dt[gift_amount]."'>
										</td>
										<td class='list_box_item' >
											<input type=text class='textbox number' name='event_gift[".$i."][use_point]' id='gift_use_point' style='width:70px;' value='".$slave_db->dt[use_point]."'>
											<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;margin:0px 0px 4px 3px' ondblclick=\"if($('#event_gift_table').find('tr[depth^=1]').length > 1){\$(this).parent().parent().remove();}else{ $(this).parent().parent().find('input[type=text]').val(''); }\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
										</td>
									</tr>";
}
$Contents .= "
								</table>
							</td>
						</tr>
						<tr bgcolor='#F8F9FA'>
                        	<td colspan=4>
								<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>";
if($agent_type == 'W'){
	$Contents .= "			
			                        <tr>
										<td height='30' colspan='3' style='padding:10px;'>
										    <div>PC 노출</div>
											<textarea name='event_text' id='event_text' style='width:98%;height:1000px;display:block' $readonly>".$event_text."</textarea>
										</td>
            						</tr>
            						<tr>
										<td height='30' colspan='3' style='padding:10px;'>
										    <div>MOBILE 노출</div>
											<textarea name='event_text2' id='event_text2' style='width:98%;height:1000px;display:block' $readonly>".$event_text2."</textarea>
										</td>
            						</tr>
            						<tr>
										<td height='30' colspan='3' style='padding:10px;'>
										    <div>당첨자 PC 노출</div>
											<textarea name='b_img_text' id='b_img_text' style='width:98%;height:1000px;display:block' $readonly>".$b_img_text."</textarea>
										</td>
            						</tr>
            						<tr>
										<td height='30' colspan='3' style='padding:10px;'>
										    <div>당첨자 MOBILE 노출</div>
											<textarea name='b_img_text2' id='b_img_text2' style='width:98%;height:1000px;display:block' $readonly>".$b_img_text2."</textarea>
										</td>
            						</tr>
            						";
}

$Contents .= "
                    <tr id='productArea' ".($kind != 'P' ? "style='display:none;'" : "").">
                      <td  colspan='4' style='padding:10px;' id='group_area_parent'>
						<div class='slide-up-down-all'><a href='javascript:slideUpDownAll();' class='slide-up-down-link'>
							<span class='plus'><img src='/admin/images/btn_group_all_open.png' alt='Plus'></span>
							<span class='minus'><img src='/admin/images/btn_group_all_close.png' alt='Minus'></span>
							<span class='label'>전체닫기/열기</a>
						</a></div>
					  ";
$gdb = new Database;
//$gdb->query("SELECT * FROM shop_event_product_group where event_ix= '$event_ix'");
$gdb->query("SELECT * FROM shop_event_product_group where event_ix= '$event_ix' and event_ix != '' order by group_code asc");//수현대리 수정 kbk 12/03/13
if($gdb->total || true){
	$group_total = $gdb->total;
	$total = 5;

	for($i=0;($i < $gdb->total || $i == 0);$i++){

	$gdb->fetch($i);

	$epg_ix = $gdb->dt[epg_ix];
	if($gdb->dt[group_code]){
		$group_no = $gdb->dt[group_code];
	}else{
		$group_no = $i+1;
	}

	$Contents .= "	<div id='group_info_area".$i."' data-id='group_info_area".$i."' class='group_info_area_wrapper' group_code='".$group_no."'>
                      <div class='group_info_header' style='position:relative;padding:10px 10px;width:100%;'><img src='/admin/images/dot_org.gif' > <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>상품그룹  (GROUP ".$group_no.")</b> <a onclick=\"add_table()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle style='position:relative;top:-2px;cursor:pointer;'></a> ".($i == 0 ? "<!--삭제버튼-->":"<a onClick=\"del_table('group_info_area".$i."','".$group_no."');\"><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle style='position:relative;top:-2px;'></a>").
						"<a href='javascript:void(0);' class='slide-up-down-link'>
							<span class='plus'><img src='/admin/images/btn_group_close.png' alt='Plus'></span>
							<span class='minus'><img src='/admin/images/btn_group_open.png' alt='Minus'></span>
					  </a>".
						 "<a style='position:absolute;top:13px;right:24px;display:block;' href='javascript:void(0);' class='drag-link'>
							<img src='/admin/images/btn_group_move.png' alt='Drag'>
							</a>
							<input type='hidden'  name='epg_ix[" . $group_no . "]' value='" . $epg_ix . "'>
							<input type='hidden' class='input-order' name='group_order[" . $group_no . "]' value='" . $group_no . "'>

					  </div>
                      <table width='100%' border='0' cellpadding='10' cellspacing='1' class='input_table_box'>
						<col width='140'>
						<col width='*'>
						<tr>
						  <td class='input_box_title'> <b>상품그룹명</b></td>
						  <td class='input_box_item'>
						  <input type='text' class='textbox' name='group_name[".$group_no."]' id='group_name_".$group_no."' size=50 value='".$gdb->dt[group_name]."'>
						  </td>
						</tr>";
					  if($agent_type != "M"){
					$Contents .= "
						<tr>
						  <td class='input_box_title'> <b>상품 그룹 이벤트명</b></td>
						  <td class='input_box_item'>
						  <input type='text' class='textbox' name='event_name[".$group_no."]' id='event_name_".$group_no."' size=50 value='".$gdb->dt[event_name]."'>
						  </td>
						</tr>";
					  }
					$Contents .= "
						<tr>
						  <td class='input_box_title'> <b>상품그룹 전시여부</b></td>
						  <td class='input_box_item'>
						  <input type='radio' class='textbox' name='use_yn[".$group_no."]' id='use_".$group_no."_y' size=50 value='Y' style='border:0px;' ".($gdb->dt[use_yn] == "Y" ? "checked":"")."><label for='use_".$group_no."_y'>전시</label>
						  <input type='radio' class='textbox' name='use_yn[".$group_no."]' id='use_".$group_no."_n' size=50 value='N' style='border:0px;' ".($gdb->dt[use_yn] == "N" ? "checked":"")."><label for='use_".$group_no."_n'>전시 하지 않음</label>
						  </td>
						</tr>";
					  if($agent_type != "M"){
					$Contents .= "
						<tr>
						  <td class='input_box_title'> <b>상품그룹 이미지(PC)</b></td>
						  <td class='input_box_item' style='padding:10px 5px;'>
						  <input type='file' class='textbox' name='group_img[".$group_no."]' id='group_img' size=50 value=''>
						  <input type='checkbox' name='group_img_chk[".$group_no."]' value='y'>삭제
						  <br>
						  <div style='padding:5px; width:1000px; height:900px; overflow:auto;' id='group_img_area_".$group_no."'>";
if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/event_group_".$group_no.".gif")){
	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/event/$event_ix/event_group_".$group_no.".gif'>";
}

$Contents .= "			   </div><br>
						  <span class=small><!--* 이미지 등록을 하지 않을경우 상품그룹명이 노출됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
						  </td>
						</tr>";
					$Contents .= "
						<tr>
						  <td class='input_box_title'> <b>상품그룹 이미지(Mobile)</b></td>
						  <td class='input_box_item' style='padding:10px 5px;'>
						  <input type='file' class='textbox' name='group_img_m[".$group_no."]' id='group_img_m' size=50 value=''>
						  <input type='checkbox' name='group_img_m_chk[".$group_no."]' value='y'>삭제
						  <br>
						  <div style='padding:5px; width:1000px; height:900px; overflow:auto;' id='group_img_m_area_".$group_no."'>";
if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/m_event_group_".$group_no.".gif")){
	$Contents .= "<img src='".$admin_config[mall_data_root]."/images/event/$event_ix/m_event_group_".$group_no.".gif'>";
}

$Contents .= "			   </div><br>
						  <span class=small><!--* 이미지 등록을 하지 않을경우 상품그룹명이 노출됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
						  </td>
						</tr>";
					  }
					$Contents .= "
						<tr style='display:none'>
						  <td class='search_box_title'><b>전시타입</b></td>
						  <td class='search_box_item promotion_types'   style='padding:10px 10px;' colspan=3><a name='display_type_area_".$group_no."'></a>
							<div id='display_type_area_".$group_no."' class=sortable style='width:100%;float:left;'>
							".GroupCategoryDisplay($mg_ix, $epg_ix, $group_no)."
							</div> ";

//$Contents .= SelectFileList2($DOCUMENT_ROOT.$admin_config[mall_data_root]."/module/main_templet/")."

$Contents .= "			
								<div class='add_type_box'>
									<p class='add_type' style='padding-top:15px;'><a href=\"#display_type_area_".$group_no."\" onclick=\"$('#add_type_choice_".$group_no."').slideToggle();\"><img src='/admin/images/protype_select.gif' alt='전시타입선택' title='전시타입선택' /></a></p>
									<div class='add_type_choice' id='add_type_choice_".$group_no."' style='display:none;'>
										".DisplayTemplet($group_no)."										 
									</div>
								</div>
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>상품노출갯수</b><span style='padding-left:2px' class='helpcloud' help_width='390' help_height='30' help_html='전시타입 배열 설정에 따라 노출갯수의 수를 지정해 주셔야 합니다<br />ex)5배열의 전시타입일 경우 30, 35등 5단위 노출갯수 지정'><img src='/admin/images/icon_q.gif' /></span></td>
						  <td class='search_box_item' colspan=3>
						  <input type='text' class='textbox numeric' name='product_cnt[".$group_no."]' id='product_cnt_".$group_no."' size=10 value='".$gdb->dt[product_cnt]."'> 전시타입을 선택하시면 상품 노출갯수가 자동으로 선택됩니다. 
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>전시상품</b></td>
						  <td class='input_box_item' style='padding:10px 5px;'>
						  <div style='padding-bottom:10px;'>
							  <input type='hidden' class='textbox' name='goods_display_type[".$group_no."]' id='use_".$group_no."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".$group_no."').toggle();$('#goods_auto_area_".$group_no."').toggle();\"><!--label for='use_".$group_no."_m'>수동등록</label-->
							  <!--input type='radio' class='textbox' name='goods_display_type[".$group_no."]' id='use_".$group_no."_a' size=50 value='A' style='border:0px;' ".($gdb->dt[goods_display_type] == "A" ? "checked":"")." onclick=\"$('#goods_manual_area_".$group_no."').toggle();$('#goods_auto_area_".$group_no."').toggle();\"><label for='use_".$group_no."_a'>자동등록</label><br-->
						  </div>
						  <div id='goods_manual_area_".$group_no."' style='display:block;' class='goods_manual_area'>
							  <div class='filterBar'>
								  <div class='searchBar'>
									 <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,".$group_no.",'productList_".$group_no."');\">
										<img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle>
									</a>
									<input type='text' class='textbox' name='search_goods' id='search_goods' size='20' value='' onkeyup=\"SearchGoods($(this), '".$group_no."')\"> 
									<img type='image' src='../images/korean/btn_search.gif' style='cursor:pointer;' onclick=\"SearchGoods($(this), '".$group_no."')\" align='absmiddle'> 
									<img src='../images/".$admininfo["language"]."/btc_del.gif' onclick=\"SearchGoodsDelete($(this))\" border='0'  style='cursor:pointer;vertical-align:middle;'>
								  </div>
								  <div class='sortingFilter'>
									<select>
										<option value=0>선택해 주세요</option>
										<option value='vieworder' selected>구매수순</option>
										<option value='lowestPrice'>최저가</option>
										<option value='highestPrice'>최고가</option>
										<option value='viewcnt'>클릭수순</option>
										<option value='regdate'>최근등록수순</option>
									</select>
								  </div>
							  </div>
							  <div class='products_area'>
								  <div style='width:100%;padding:5px;' id='group_product_area_".$group_no."' >".relationEventGroupProductList($event_ix, $group_no, "clipart")."</div>
								  <div style='clear:both;width:100%;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span></div>
							  </div>
						  </div>
						 ";

$Contents .= "				<div style='padding:0px 0px;display:none;' id='goods_auto_area_".$group_no."'>
							<a href=\"javascript:PoPWindow3('category_select.php?mmode=pop&group_code=".$group_no."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
							<table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
								<col width=100%>
								<tr>
									<td>";
										if($id != ""){
											$Contents .= PrintRelation($id);
										}else{
										$Contents .= "<table cellpadding=0 cellspacing=0 id='objCategory_".$group_no."' >
														<col width=5>
														<col width=30>
														<col width=*>
														<col width=100>
													  </table>";
										}
					$Contents .= "	</td>
								</tr>
								<tr><td>자동등록기능은 준비중입니다.</td></tr>
							</table>
							</div>";

$Contents .= "

						  </td>
						</tr>
					  </table><br><br>
					  </div>";
	}
}
$Contents .= "
                      </td>
                    </tr>
                    <tr>
						<td colspan=3 align=right style='padding:10px;'><table width=100%  border=0>
						<col width= '*' >
						<col width= '100' >
						<col width= '100' >
						<col width= '100' >
									<tr>";
							if($gdb->dt[event_ix]!="") {
								$Contents .= "<td align='left'><a href='/event/goods_event.php?event_ix=".$gdb->dt[event_ix]."' target='_blank' ><img src='../images/".$admininfo["language"]."/btn_promotion_preview.gif' align=absmiddle></a></td> ";
							}
							if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
								$Contents .= "
										<td><input type='checkbox' name='delete_cache' id='delete_cache' value='Y'><label for='delete_cache'>캐쉬삭제하기</label></td>
										<td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle></td>
										<td><a href='event.list.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a></td>
									 ";
							}

						$Contents .= "</tr>
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
            </td>
          </tr>
        </table>
        </form>
    </td>
  </tr>

  ";
  /*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >작업시에는 표시하지 않음으로 선택후 작업하시기 바랍니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >등록된 <u>이벤트는 </u> 사용으로 되어 있는 이벤트만 <a href='/event/promotion_list.php' target='_blank'>http://$HTTP_HOST/event/promotion_list.php</a> 에서 확인 하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기간이 만료된 이벤트는 자동으로 노출이 종료됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기획전 미리보기는 변경된 내용을 저장하신 후 사용하셔야 합니다.</td></tr>
</table>
";*/
	//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

//$help_text = HelpBox("이벤트/기획전  관리", $help_text);
//$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td><b>이벤트/기획전 관리</b></td><td><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_기획전등록(090322)_config.xml',800,517,'manual_view')\"  title='이벤트/기획전 동영상 메뉴얼입니다' style='cursor:pointer;'><img src='../image/movie_manual.gif' align=absmiddle align=absmiddle width=26 height=20 style='position:absolute;top:-1px;'></a></td></tr></table>", $help_text,160);

$Contents .= "
  <tr>
    <td align='left' style='padding-bottom:200px;'>
    $help_text

    </td>
  </tr>";

$Contents .= "
	</table>


<form name='lyrstat'><input type='hidden' name='opend' value=''></form>
<Script Language='JavaScript'>
$(document).ready(function () {
	var copy_text;
	$('#flash_addbtn').click(function(){
		// alert($('#picture_puzzles_table tbody tr:last').clone(true).html());
		var option_target_obj = $('#picture_puzzles_table tr');
		var total_rows = option_target_obj.length;
		//alert(total_rows);
		var newRow = $('#picture_puzzles_table tbody tr:first').clone(true).appendTo('#picture_puzzles_table');  

		newRow.find('input[id^=ep_ix ]').attr('name','event_picturepuzzles['+(total_rows)+'][ep_ix]');
		newRow.find('input[id^=ep_file ]').attr('name','event_picturepuzzles['+(total_rows)+'][ep_file]');
		newRow.find('input[id^=ep_link ]').attr('name','event_picturepuzzles['+(total_rows)+'][ep_link]');
		newRow.find('input[id^=ep_title ]').attr('name','event_picturepuzzles['+(total_rows)+'][ep_title]');
		newRow.find('input[id^=file_text ]').attr('name','event_picturepuzzles['+(total_rows)+'][file_text]');
		newRow.find('input[id^=nondelete ]').attr('name','event_picturepuzzles['+(total_rows)+'][nondelete]');
		

		newRow.find('input[id^=ep_ix ]').val('');
		newRow.find('input[id^=ep_link ]').val('');
		newRow.find('input[id^=ep_title ]').val('');
		newRow.find('input[id^=file_text ]').val('');
		newRow.find('img[id^=picturepuzzle_img ]').remove();
		
		newRow.find('b[id^=ep_file_text]').text('');

		/*
		
		newRow.find('.ep_link').val('');
		newRow.find('.ep_title').val('');
		newRow.find('.ep_ix').val('');
		*/
		newRow.find('#delete_btn').show();
		 
	});

	$('#flash_delbtn').click(function(){
		var len = $('#picture_puzzles_table .clone_tr').length;
		if(len > 1){
			eqIndex--;
			$('#picture_puzzles_table .clone_tr:last').remove();
		}else{
			return false;
		}
	});
	
	sortGroup(true);
	createProducts();
	
	$('input[name=kind]').click(function(){
	   if($(this).val() == 'E'){
	       $('#productArea').hide();
	   } else {
	       $('#productArea').show();
	   }
	});
	
});

$(function() {
	
	$(\"#lottery_date\").datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			//alert(dateText);
			//if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			//	$('#end_datepicker').val(dateText);
			//}else{
				//$('#end_datepicker').datepicker('setDate','+0d');
			//}
		}
	});

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


function selectEventType(obj){
	if(obj.value == 6){
		$('#event_picturepuzzles_area'	).show();
	}else{
		$('#event_picturepuzzles_area'	).hide();
	}
}

function delete_puzzle_img(tg) {//이미지 정보 삭제 kbk 13/07/11
	var ep_ix=$(tg).find('.ep_ix').eq(0).val();
	var event_ix='".$event_ix."';
	if(ep_ix!='') {
		$.ajax({
			type: 'GET',
			data:
				{'act': 'del_puzzle_img','ep_ix':ep_ix,'event_ix':event_ix},
			url: '/admin/display/event.act.php',
			dataType: 'html',
			async: false,
			beforeSend: function(){
			},
			success: function(datas){
				if(datas == null){
					alert('정보를 찾을 수 없습니다.');
				}else{
					if(datas=='Y') {
						$(tg).remove();
						alert('해당 이미지 정보를 삭제하였습니다.');
					} else {
						alert('요청한 동작을 실행하지 못하였습니다.');
					}
				}
			}

		});
	} else { 
		$(tg).remove();
	}
}

init();
my_init($group_total);
</Script>";



$Script = "<script language='javascript' src='../search.js'></script>
<!--script language='JavaScript' src='../js/scriptaculous.js'></script>\n
<script language='JavaScript' src='../js/dd.js'></script>\n
<script language='JavaScript' src='../js/mozInnerHTML.js'></script-->\n
<script language='javascript' src='../display/event.write.js'></script>\n
<!--script language='JavaScript' src='../webedit/webedit.js'></script-->\n
<!--script language='javascript' src='../include/DateSelect.js'></script-->\n
$Script";

if($agent_type == "M"  || $agent_type == "mobile"){
	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = $navigation;
	$P->title = $title;
	$P->strLeftMenu = mshop_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->Navigation = "프로모션/전시 > 이벤트/기획전 > 이벤트/기획전 등록";
	$P->title = "이벤트/기획전 등록";
	$P->OnloadFunction = "";//"MM_preloadImages('../webedit/images/wtool1_1.gif','../webedit/images/wtool2_1.gif','../webedit/images/wtool3_1.gif','../webedit/images/wtool4_1.gif','../webedit/images/wtool5_1.gif','../webedit/images/wtool6_1.gif','../webedit/images/wtool7_1.gif','../webedit/images/wtool8_1.gif','../webedit/images/wtool9_1.gif','../webedit/images/wtool11_1.gif','../webedit/images/wtool13_1.gif','../webedit/images/wtool10_1.gif','../webedit/images/wtool12_1.gif','../webedit/images/wtool14_1.gif','../webedit/images/bt_html_1.gif','../webedit/images/bt_source_1.gif')";//showSubMenuLayer('storeleft');
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}

function DisplayTemplet($group_code){
	global $slave_db ,$admininfo, $agent_type;

	$sql = "select * 
				from shop_display_templetinfo dt 
				where disp = 1 
				and agent_type = '".$agent_type."'
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

function GroupCategoryDisplay($mg_ix, $epg_ix, $group_code){
	global $slave_db ,$admininfo;

	$sql = "select egd.* , dt.dt_ix, dt.dt_name, dt.dt_goods_num
				from shop_event_group_display egd 
				left join shop_display_templetinfo dt on  egd.display_type = dt.dt_ix
				where egd.epg_ix = '".$epg_ix."' 
				order by egd.vieworder asc 
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

function SelectEventCate($category){
	global $agent_type,$slave_db;

	if($agent_type){
		$where = " where  agent_type = '$agent_type' ";
	}
	$sql = "SELECT * FROM shop_event_relation $where ORDER BY regdate ";
	$slave_db->query($sql);
	$cateArr = $slave_db->fetchall();

	$mstring =  "<select name='er_ix'>";
	$mstring .=  "<option value=''>선택하세요.</option>";
	if(is_array($cateArr)){
		foreach($cateArr as $_KEY=>$_VALUE) {
			$mstring .= "<option value='".$_VALUE[er_ix]."' ".($_VALUE[er_ix] == $category ? " selected ":"").">".$_VALUE[title]."</option>";
		}
	}
	$mstring .=  "</select>";

	return $mstring;
}
//SelectEventCate('123');

function relationEventGroupProductList($event_ix, $group_code, $disp_type=""){

	global $start,$page, $orderby, $admin_config, $erpid,$slave_db;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT p.id,p.pname, p.sellprice,  p.reserve, event_ix , p.brand_name
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and group_code = '$group_code'    "; //and p.disp = 1
	$slave_db->query($sql);
	$total = $slave_db->total;
//p.id, p.pcode, p.shotinfo, p.pname, p.listprice, p.sellprice, p.wholesale_sellprice, p.wholesale_price,   p.reserve, p.state, p.disp, ppr.vieworder, ppr.group_code, p.brand_name	
	$sql = "SELECT p.id, p.pcode, p.shotinfo, p.pname, p.listprice, p.sellprice,  p.wholesale_sellprice, p.wholesale_price,  p.reserve, p.view_cnt, p.regdate, p.state, p.disp, event_ix, erp_ix, erp.vieworder, erp.group_code, p.brand_name
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and group_code = '$group_code' order by erp.vieworder asc "; //and p.disp = 1 limit $start,$max
	//echo $sql."<br><br>";
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

	if(count($products) == 0){
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
                //$slave_db->fetch($i);
                $imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $products[$i]['id'], 'c');
                //$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$products[$i]['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($products[$i]['pname']))).'", "'.addslashes(addslashes(trim($products[$i]['brand_name']))).'", "'.$products[$i]['sellprice'].'", "'.$products[$i]['disp'].'", "'.$products[$i]['state'].'");'."\n";


                $mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$products[$i]['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($products[$i]['pname']))).'", "'.addslashes(addslashes(trim($products[$i]['brand_name']))).'", "'.$products[$i]['sellprice'].'", "'.$products[$i]['listprice'].'", "'.$products[$i]['reserve'].'", "'.$products[$i]['coprice'].'", "'.$products[$i]['wholesale_price'].'", "'.$products[$i]['wholesale_sellprice'].'", "'.$products[$i]['disp'].'", "'.$products[$i]['state'].'", "'.$products[$i]['dcprice'].'", "'.$products[$i]['vieworder'].'", "'.$products[$i]['view_cnt'].'", "'.$products[$i]['regdate'].'");'."\n";

            }
            $mString .= '</script>'."\n";
		}
	}
	return $mString;

}

function relationProductList2($event_ix){

	global $start,$page, $orderby, $admin_config,$slave_db;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, event_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and p.disp = 1   ";
	$slave_db->query($sql);
	$total = $slave_db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, event_ix, erp_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix'  and p.disp = 1 order by erp.vieworder limit $start,$max";
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

function relationProductList($event_ix, $disp_type=""){

	global $start,$page, $orderby, $admin_config, $erpid,$slave_db;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, event_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and p.disp = 1   ";
	$slave_db->query($sql);
	$total = $slave_db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, event_ix, erp_ix, erp.vieworder, erp.group_code
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix'  and p.disp = 1 order by erp.vieworder asc limit $start,$max";
	$slave_db->query($sql);




	if ($slave_db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'><!--등록된 이벤트/기획전 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>이벤트/기획전 상품으로 등록됩니다. --> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')."</td></tr>";
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
							<td><input type='hidden' name='rpid[]' value='".$slave_db->dt[id]."'><!--a href='relation.category.act.php?mode=delete&event_ix=".$event_ix."&erp_ix=".$slave_db->dt[erp_ix]."'  target=act><img src='../image/btc_del.gif'></a--></td>
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


function relationProductList_backup($event_ix){

	global $start,$page, $orderby, $admin_config, $erpid,$slave_db;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve, event_ix
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix' and p.disp = 1   ";
	$slave_db->query($sql);
	$total = $slave_db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, event_ix, erp_ix, erp.vieworder
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_EVENT_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.event_ix = '$event_ix'  and p.disp = 1 order by erp.vieworder asc limit $start,$max";
	$slave_db->query($sql);




	if ($slave_db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'><!--등록된 이벤트/기획전 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>이벤트/기획전 상품으로 등록됩니다. -->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G')."</td></tr>";
	}else{
		$mString = "<ul id='sortlist'>";

		$i=0;



		for($i=0;$i<$slave_db->total;$i++){
			$slave_db->fetch($i);

			$mString .= "<li id='image_".$slave_db->dt[id]."' >
						<table width=99% border=0 >
						<col width='60'>
						<col width='*'>
						<col width='60'>
						<tr height=27 bgcolor=#ffffff >
						<td class=table_td_white align=center style='padding:5px;'>
							<img src='".$admin_config[mall_data_root]."/images/product/c_".$slave_db->dt[id].".gif'>
						</td>
						<td class=table_td_white>".cut_str($slave_db->dt[pname],30)."</td>
						<td><a href='relation.category.act.php?mode=delete&event_ix=".$event_ix."&erp_ix=".$slave_db->dt[erp_ix]."'  target=act><img src='../images/".$admininfo["language"]."/btc_del.gif'></a></td>
						</tr><tr height=1><td colspan=5 class=td_underline></td></tr>
						</table></li>";
		}
	}

	$mString = $mString."</ul>";

	return $mString;

}

/*
CREATE TABLE `shop_event_product_relation` (
  `erid` int(10) unsigned zerofill NOT NULL auto_increment,
  `event_ix` int(4) unsigned zerofill NOT NULL default '',
  `pid` int(6) unsigned zerofill default NULL,
  `disp` char(1) default '1',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`erid`)
) TYPE=MyISAM COMMENT='이벤트/기획전 상품등록';

CREATE TABLE `shop_event_product_relation` (
  `erp_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `pid` int(6) unsigned zerofill NOT NULL default '000000',
  `event_ix` int(4) unsigned zerofill NOT NULL default '',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`erp_ix`)
) TYPE=MyISAM COMMENT='이벤트/기획전 상품등록' ;

// 김수현 110916 추가
ALTER TABLE `shop_event` ADD `er_ix` INT( 8 ) UNSIGNED ZEROFILL NULL COMMENT '이벤트기획전 카테고리' AFTER `cid` ;

ALTER TABLE `shop_event` ADD `kind` ENUM( 'E', 'P' ) NOT NULL DEFAULT 'E' COMMENT '이벤트/기획전 구분' AFTER `er_ix`;

CREATE TABLE IF NOT EXISTS `shop_event_relation` (
  `er_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `title` varchar(150) default NULL COMMENT '카테고리 명',
  `file` varchar(150) default NULL COMMENT '파일명',
  `use_yn` enum('Y','N') default 'Y' COMMENT '사용유무',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`er_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='이벤트/기획전 카테고리'

  



CREATE TABLE IF NOT EXISTS `shop_event_config` (
  `event_ix` int(8) unsigned zerofill NOT NULL,
  `event_type` varchar(1) default '1' COMMENT '이벤트 타입',
  `lottery_type` varchar(1) default '1' COMMENT '추첨타입',
  `lottery_amount` int(5) default 0 COMMENT '선착순 인원',
  `lottery_method` varchar(1) default '1' COMMENT '추첨방식',
  `lottery_date` varchar(10) default NULL COMMENT '추첨일자',
  `participation_able_times` varchar(1) default '1' COMMENT '참여횟수',
  `participation_method` varchar(1) default '1' COMMENT '참여횟수',
  `participation_use_point` int(3) default 0 COMMENT '포인트 차감',
  `regdate` datetime default NULL COMMENT '등록일자',
  PRIMARY KEY  (`event_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='이벤트/기획전 이벤트 설정'


CREATE TABLE IF NOT EXISTS `shop_event_gift` (
  `eg_ix` int(10) unsigned  NOT NULL auto_increment,
  `event_ix` int(8) unsigned  NOT NULL,
  `gift_name` varchar(50) default '1' COMMENT '상품이름',
  `gift_type` varchar(1) default '1' COMMENT '상품종류',
  `gift_code` int(5) default 0 COMMENT '상품코드',
  `gift_amount` int(10) default '1' COMMENT '당첨인원',
  `use_point` varchar(10) default NULL COMMENT '포인트 차감',
  `insert_yn` varchar(1) default 'Y' COMMENT '등록여부',
  `regdate` datetime default NULL COMMENT '등록일자',
  PRIMARY KEY  (`eg_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='이벤트/기획전 이벤트 상품'



CREATE TABLE IF NOT EXISTS `shop_event_picturepuzzle` (
  `ep_ix` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `event_ix` int(10) unsigned NOT NULL COMMENT '이벤트 키값',
  `ep_title` varchar(150) DEFAULT NULL COMMENT '이미지타이틀',
  `ep_link` varchar(255) DEFAULT NULL COMMENT '이미지링크',
  `ep_file` varchar(255) DEFAULT NULL COMMENT '이미지파일',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  `tmp_update` enum('0','1') NOT NULL DEFAULT '0' COMMENT '수정시구분값',
  PRIMARY KEY (`ep_ix`),
  KEY `event_ix` (`event_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='이벤트 숨은그림찾기 정보' 

CREATE TABLE IF NOT EXISTS `shop_event_picturepuzzle_find` (
  `ep_ix` int(10) unsigned NOT NULL COMMENT '인덱스',
  `event_ix` int(10) unsigned NOT NULL COMMENT '이벤트 코드값',
  `mem_ix` varchar(32) DEFAULT NULL COMMENT '회원코드값',
  PRIMARY KEY (`ep_ix`,`event_ix`,`mem_ix`),
  KEY `event_ix` (`event_ix`)
}



CREATE TABLE `shop_event_group_display` (
  `egd_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `epg_ix` int(8) unsigned ,
  `display_type` int(2) default '1',
  `set_cnt` int(2) default '1',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`egd_ix`)
) TYPE=MyISAM COMMENT='이벤트 상품전시관리_그룹별 전시타입'



CREATE TABLE IF NOT EXISTS `shop_event_comment` (
  `ec_ix` int(10) NOT NULL AUTO_INCREMENT COMMENT '이벤트 콤멘트 키값',
  `event_ix` int(10) DEFAULT NULL COMMENT '이벤트 코드값', 
  `mem_ix` varchar(32) DEFAULT NULL COMMENT '회원코드값',
  `comment` mediumtext COMMENT '콤멘트 내용',
  `recommend` unsigned int(10) default null COMMENT '추천수',
  `recommend_user` mediumtext COMMENT '추천인', 
  `regdate` datetime DEFAULT NULL COMMENT '등록일자',
  PRIMARY KEY (`ec_ix`),
  KEY `mem_ix_ix` (`mem_ix`),
  KEY `regdate_ix` (`regdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8



CREATE TABLE `shop_event_brand_relation` (
  `ebr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `b_ix` int(6) unsigned zerofill NOT NULL default '000000',
  `group_code` int(2) NOT NULL default '1',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`ebr_ix`)
) TYPE=MyISAM COMMENT='이벤트 상품전시관리_노출브랜드'

CREATE TABLE `shop_event_seller_relation` (
  `esr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `company_id` varchar(32) NOT NULL ,
  `group_code` int(2) NOT NULL default '1',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`esr_ix`)
) TYPE=MyISAM COMMENT='이벤트 상품전시관리_노출셀러'

*/
?>