<?
include("../class/layout.class");

$db = new Database;
$mdb = new Database;

$db->query("SELECT * FROM seller_official_popup where popup_ix= '$popup_ix'");
$db->fetch();

if($db->total){

	$popup_ix = $db->dt[popup_ix];
	$popup_title = $db->dt[popup_title];
	$popup_text = $db->dt[popup_text];
	$popup_div = $db->dt[popup_div];
	$popup_status = $db->dt[popup_status];
	$regdate = $db->dt[regdate];

/*
	if($db->dt[popup_today] == 1){
		$popup_height = $db->dt[popup_height] - 30;
	}else{
		$popup_height = $db->dt[popup_height];
	}
	$popup_top = $db->dt[popup_top];
	$popup_width = $db->dt[popup_width];
	$popup_left = $db->dt[popup_left];
	$popup_type = $db->dt[popup_type];

*/


	if($popup_div == '2'){
		$popup_div_kor = '공문서';
	} else{
		$popup_div_kor = '동의서';
	}

}

$sql = "select
			ccd.com_name, ccd.com_ceo, ccd.com_phone, ccd.com_mobile, ccd.customer_name, ccd.customer_mobile,
			cu.id,
			cu.code,
			csd.charge_code,
			cu.date,
			opr.popup_confirm,
			opr.popup_confirm_date
		from
			common_user as cu 
			inner join common_member_detail as cmd on (cu.code = cmd.code)
			inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
			inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
			left join seller_official_popup_result opr on (opr.charger_ix = cu.code and opr.popup_ix = '".$popup_ix."')
		where
			cu.mem_div = 'S'
			and ccd.com_type = 'S'";
$db->query($sql);

if($mode == "excel"){

	$goods_infos = $db->fetchall("object");

	$colums[com_name] = '셀러명';
	$colums[id] = '셀러ID';
	$colums[com_ceo] = '대표자';
	$colums[com_phone] = '대표전화';
	$colums[com_mobile] = '핸드폰';
	$colums[customer_name] = '담당자';
	$colums[customer_mobile] = '담당자 핸드폰';
	$colums[popup_confirm] = '확인여부';
	$colums[popup_confirm_date] = '확인일자';

	$columsinfo = $colums;

	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$inventory_excel = new PHPExcel();

	// 속성 정의
	$inventory_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("공문/동의서-".date('Y-m-d')."")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");

	$col = 'A';
	foreach($columsinfo as $keys => $values){

		$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$keys]);
		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setWidth(10);
		$col++;
	}

	for ($i = 0; $i < count($goods_infos); $i++){

		if($goods_infos[$i][popup_confirm] == "1"){	//승인여부
			$value_str="동의";
		}elseif($goods_infos[$i][popup_confirm] == "0"){
			$value_str="미동의";
		}elseif($goods_infos[$i][popup_confirm] != "1" || $goods_infos[$i][popup_confirm] != "0"){
			$value_str="미확인";
		}

		$confirm_date = substr($goods_infos[$i][popup_confirm_date], 0, 10);

		if($confirm_date ==  false){
			$confirm_date = '-';
		}

		$inventory_excel->getActiveSheet()->setCellValue("A" . ($z+2), $goods_infos[$i][com_name]);
		$inventory_excel->getActiveSheet()->setCellValue("B" . ($z+2), $goods_infos[$i][id]);
		$inventory_excel->getActiveSheet()->setCellValue("C" . ($z+2), $goods_infos[$i][com_ceo]);
		$inventory_excel->getActiveSheet()->setCellValue("D" . ($z+2), $goods_infos[$i][com_phone]);
		$inventory_excel->getActiveSheet()->setCellValue("E" . ($z+2), $goods_infos[$i][com_mobile]);
		$inventory_excel->getActiveSheet()->setCellValue("F" . ($z+2), $goods_infos[$i][customer_name]);
		$inventory_excel->getActiveSheet()->setCellValue("G" . ($z+2), $goods_infos[$i][customer_mobile]);
		$inventory_excel->getActiveSheet()->setCellValue("H" . ($z+2), $value_str);
		$inventory_excel->getActiveSheet()->setCellValue("I" . ($z+2), $confirm_date);


		unset($history_text);
		$z++;
	}
	


/*
	$col = 'A';
	foreach($columsinfo as $keys => $values){

		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setWidth(10);
		$col++;

	}
*/

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="official_result.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter->save('php://output');

	exit;
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
    CKEDITOR.replace('official_document_contents',{
        startupFocus : false,height:500
    });
}

function ChangeUsedate(thisObj){
	if($(thisObj).is(':checked')){
		$('#use_sdate_start').attr('disabled',false);
		$('#use_sdate_end').attr('disabled',false);
	}else{
		$('#use_sdate_start').val('').attr('disabled',true);
		$('#use_sdate_end').val('').attr('disabled',true);
	}
}

</Script>";


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center >
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("셀러 공문서 동의", "셀러설정 관리 > 셀러 공문/동의서 결과")."</td>
		</tr>
		<tr>
			<td>";
		$mstring .= "
		<form name=searchmember method='get' ><!--SubmitX(this);'-->
		<input type=hidden name='popup_ix' value='".$popup_ix."' >
		<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:0px'>
				
				<br>
				[".$popup_title."] - [".$popup_div_kor."]에 대한 현황
				<br>
				<br>
				<br>
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>				
					<col width='15%'>
					<col width='*'>
					";

					$mstring .= "
					<tr >
					  <td class='search_box_title'>조건검색 </td>
					  <td class='search_box_item'>
						  <select name=search_type>
								<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">셀러명</option>
								<option value='com_ceo' ".CompareReturnValue("com_ceo",$search_type,"selected").">대표자명</option>
								<option value='customer_name' ".CompareReturnValue("customer_name",$search_type,"selected").">담당자 명</option>
								<option value='customer_mobile' ".CompareReturnValue("customer_mobile",$search_type,"selected").">담당자 핸드폰</option>
								<option value='com_mobile' ".CompareReturnValue("com_mobile",$search_type,"selected").">핸드폰</option>
						  </select>
						  <input type=text name='search_text' class='textbox' value='".$search_text."' style='width:50%; vertical-align:top;' >
					  </td>
					</tr>
					<tr>
					  <td class='search_box_title'>확인 여부 </td>
					  <td class='search_box_item'>
						  <input type=radio name='popup_confirm' value='1' id='confirm_y'  ".CompareReturnValue("1",$popup_confirm,"checked")."><label for='confirm_y'>O(확인/동의)</label>
						  <input type=radio name='popup_confirm' value='0' id='confirm_n'  ".CompareReturnValue("0",$popup_confirm,"checked")."><label for='confirm_n'>X(미동의)</label>
						  <input type=radio name='popup_confirm' value='3' id='confirm_not'  ".CompareReturnValue("3",$popup_confirm,"checked")."><label for='confirm_not'>미확인</label>
					  </td>
					</tr>
					";

 
		$mstring .= "
					<tr height=27>
					  <td class='search_box_title'>확인일자
						 <input type=checkbox name='is_use_date' value='1' id='is_use_date' onclick='ChangeUsedate(this);' ".CompareReturnValue("1",$is_use_date,"checked").">
					  </td>
					  </td>
					  <td class='search_box_item'>
					   ".search_date('use_sdate_start','use_sdate_end',$use_sdate_start,$use_sdate_end,'N','D')."";
					$mstring .= "
					  </td>
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
			<tr height=60>
				<td style='padding:10px 0px;' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
			</tr>
		</table>
		</form>";
		$mstring .= "
			</td>
		</tr>
		<tr>
			<td>
			".PrintPopupList($popup_ix)."
			</td>
		</tr>";





		/*
      <div id='TG_INPUT' style='position: relative;'>
        <form name='INPUT_FORM' method='post' onSubmit=\"return SubmitX(this)\" enctype='multipart/form-data'  action='popup.act.php'>
		<input type='hidden' name=act value='$act'>
		*/


$Contents = $mstring;



	$P = new ManagePopLayOut();

$Script = "<script language='javascript' src='popup.write.js'></script>\n<script type='text/javascript' src='../ckeditor/ckeditor.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";

$P->addScript = $Script;
$P->title = "셀러 공문/동의서 결과";
if($popup_position == "A"){
	$P->NaviTitle = "셀러 공문/동의서 결과";
}else{
	$P->NaviTitle = "셀러 공문/동의서 결과";
}
$P->OnloadFunction = "Init(document.INPUT_FORM);MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')";//showSubMenuLayer('storeleft');
$P->strContents = $Contents;
echo $P->PrintLayOut();


function PrintPopupList($popup_ix){
	global $db, $mdb, $page, $search_type,$search_text,$disp_yn, $popup_confirm;
	global $use_sdate_start,$use_edate_end,$use_sdate_end;
	global $auth_delete_msg, $admininfo;
	global $popup_position , $agent_type;

	$max = 20;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$where = "";

	if($popup_confirm == "1"){
		$where .= " and opr.popup_confirm =  '1' ";
	}else if($popup_confirm == "0"){
		$where .= " and opr.popup_confirm = '0' ";
	}elseif($popup_confirm == "3"){
		$where .= " and opr.popup_confirm is null";
	}

	if($search_type != "" && $search_text != ""){
		$where .= " and $search_type LIKE  '%$search_text%' ";
	}

	//$startDate = $FromYY.$FromMM.$FromDD;
	//$endDate = $ToYY.$ToMM.$ToDD;


	if($use_sdate_start != "" && $use_sdate_end != ""){
		$where .= " and date_format(popup_confirm_date, '%Y-%m-%d') between  '$use_sdate_start' and '$use_sdate_end' ";
	}

	//$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	//$vendDate = $vToYY.$vToMM.$vToDD;

	$sql = "select
				ccd.*,
				cu.id,
				cu.code,
				csd.charge_code,
				cu.date
			from
				common_user as cu 
				inner join common_member_detail as cmd on (cu.code = cmd.code)
				inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
				inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
				left join seller_official_popup_result opr on (opr.charger_ix = cu.code and opr.popup_ix = '".$popup_ix."')
			where
				cu.mem_div = 'S'
				and ccd.com_type = 'S'
				$where";
//left join seller_official_popup_result opr on (opr.charger_ix = cu.code and opr.popup_ix = '".$popup_ix."')
/*
	$sql = "select cd.com_name, cd.com_ceo, cd.com_phone, cd.com_mobile, cd.customer_name, cd.customer_mobile, opr.popup_confirm, opr.popup_confirm_date, opr.seller_id from common_company_detail cd
					left join seller_official_popup_result opr on (opr.charger_ix = cd.company_id)
					$where";
					*/
	//echo nl2br($sql);
	$mdb->query($sql);
	$total = $mdb->total;

	$mString = "<a href='./seller_official_document.result.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align=right style='padding-bottom:4px'></a>
			<table cellpadding=4 cellspacing=0 border=0 width=100% class='list_table_box'>
					<tr align=center bgcolor=#efefef height=30>
					<td class=s_td width=5%>번호</td>
					<td class=s_td width='10%'>셀러명</td>
					<td class=m_td width='10%'>셀러ID</td>
					<td class=m_td width='10%'>대표자</td>
					<td class=m_td width='10%'>대표전화</td>
					<td class=m_td width='10%'>핸드폰</td>
					<td class=m_td width='10%'>담당자</td>
					<td class=e_td width='10%'>담당자 핸드폰</td>
					<td class=m_td width='5%'>확인여부</td>
					<td class=e_td width='15%'>확인일자</td>
					</tr>";
	if ($mdb->total == 0){
		$mString .= "<tr bgcolor=#ffffff height=70><td colspan=10 align=center>팝업 내역이 존재 하지 않습니다.</td></tr>
							</table>";
		$mString .= "<table width=100%><tr bgcolor=#ffffff ><td colspan=5 style='padding:5px 0px;' align=right>
			</td></tr></table>";
		$mString .= " ";
	}else{

	$sql = "select
				ccd.*,
				cu.id,
				cu.code,
				csd.charge_code,
				cu.date
			from
				common_user as cu 
				inner join common_member_detail as cmd on (cu.code = cmd.code)
				inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
				inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
				left join seller_official_popup_result opr on (opr.charger_ix = cu.code and opr.popup_ix = '".$popup_ix."')
			where
				cu.mem_div = 'S'
				and ccd.com_type = 'S'
				$where
				LIMIT $start,$max";
//left join seller_official_popup_result opr on (opr.charger_ix = cu.code and opr.popup_ix = '".$popup_ix."')
		$db->query($sql);

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			$sql="select * from seller_official_popup_result where charger_ix='".$db->dt[code]."' and popup_ix='".$popup_ix."'";
			$mdb->query($sql);
			$mdb->fetch();
			$mdb->dt;

			$no = $total - ($page - 1) * $max - $i;
			//$no = $no + 1;

			$mString = $mString."<tr height=30 >
			<td class='list_box_td '>".$no."</td>
			<td class='list_box_td' style='text-align:left;padding-left:15px;'>".$db->dt[com_name]."</td>
			<td class='list_box_td'>".$db->dt[id]."</td>
			<td class='list_box_td'>".$db->dt[com_ceo]."</td>
			<td class='list_box_td'>".$db->dt[com_phone]."</td>
			<td class='list_box_td' nowrap>".$db->dt[com_mobile]."</td>
			<td class='list_box_td'>".$db->dt[customer_name]."</td>
			<td class='list_box_td'>".$db->dt[customer_mobile]."</td>
			<td class='list_box_td'>";
			
			if($mdb->dt[popup_confirm] == '1'){
				$mString .= "동의";
			} elseif($mdb->dt[popup_confirm] == '0'){
				$mString .= "미동의";
			} elseif($mdb->dt[popup_confirm]!="1" || $mdb->dt[popup_confirm]!="0"){
				$mString .= "미확인";
			}
				
			$mString .= "</td>
			<td class='list_box_td'>".$mdb->dt[popup_confirm_date]."</td>
			</tr>
			";
		}
		$mString .= "</table>
					<table cellpadding=0 cellspacing=0 border=0 width=100% >
					<tr height=50 bgcolor=#ffffff>
					<td colspan=5 align=right>".page_bar($total, $page, $max,  "&max=$max&search_type=$search_type&search_text=$search_text&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD&disp_yn=$disp_yn","")."</td>
				</tr>";
				$mString .= "</table>";
	}


	

	return $mString;
}

?>