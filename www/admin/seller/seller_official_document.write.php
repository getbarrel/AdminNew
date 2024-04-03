<?
include("../class/layout.class");

$db = new Database;
$db->query("SELECT * FROM seller_official_popup where popup_ix= '$popup_ix'");
$db->fetch();

if($db->total){

	$popup_height = 750;
	$popup_top = 100;
	$popup_width = 1050;
	$popup_left = 100;
	$popup_type = 'W';

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
	$popup_use_sdate = $db->dt[popup_use_sdate];
	$popup_use_edate = $db->dt[popup_use_edate];
	$popup_today = $db->dt[popup_today];	
	
	if($mode == "copy"){
		$act = "insert";
		$popup_ix = "";
	}else{
		$act = "update";
	}

	$sDate = date("Y/m/d", mktime(0, 0, 0, substr($db->dt[popup_use_sdate],4,2)  , substr($db->dt[popup_use_sdate],6,2), substr($db->dt[popup_use_sdate],0,4)));
	$eDate = date("Y/m/d",mktime(0, 0, 0, substr($db->dt[popup_use_edate],4,2)  , substr($db->dt[popup_use_edate],6,2), substr($db->dt[popup_use_edate],0,4)));

	$startDate = $popup_use_sdate;
	$endDate = $popup_use_edate;

}else{

	$popup_height = 750;
	$popup_top = 100;
	$popup_width = 1050;
	$popup_left = 100;
	$popup_type = 'W';

	$act = "insert";

	$popup_use_sdate = "";
	$popup_use_edate = "";

	$next10day = mktime(0, 0, 0, date("m")  , date("d")+10, date("Y"));

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d");
	$eDate = date("Y/m/d",$next10day);

	$startDate = date("Ymd");
	$endDate = date("Ymd",$next10day);
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
    CKEDITOR.replace('popup_text',{
        startupFocus : false,height:500
    });
}
</Script>";

/*
if($db->dt[popup_status] == '2'){

	echo '<script>alert(\'진행중인 문서는 수정할 수 없습니다\')
				 self.close()</script>';

} elseif($db->dt[popup_status] == '0'){

	echo '<script>alert(\'진행 완료된 문서는 수정할 수 없습니다\')
				 self.close()</script>';

}
*/

$Contents = "
<table width='100%' border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("셀러 공문서 등록", "셀러설정 관리 > 셀러 공문서 등록")."</td>
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
        <form name='INPUT_FORM' method='post' onSubmit=\"return SubmitX(this)\" enctype='multipart/form-data'  action='seller_official_document.act.php'>
		<input type='hidden' name=act value='$act'>
		<input type='hidden' name=popup_ix value='$popup_ix'>
		<input type='hidden' name=mmode value='$mmode'>
		<input type='hidden' name=popup_position value='$popup_position'>
		<input type='hidden' name=popup_height value='$popup_height'>
		<input type='hidden' name=popup_top value='$popup_top'>
		<input type='hidden' name=popup_width value='$popup_width'>
		<input type='hidden' name=popup_left value='$popup_left'>
		<input type='hidden' name=popup_type value='$popup_type'>
		<input type='hidden' name=popup_div value='$popup_div'>

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
						$Contents .= "
                        <tr>
							<td class='input_box_title' nowrap colspan='2'> <b>문서 제목 <img src='".$required3_path."'></b></td>
							<td class='input_box_item' ><input type='text' class='textbox' name='popup_title' value='".$db->dt[popup_title]."' maxlength='50' style='width:90%' validation='true' title='제목'></td>
							<td class='input_box_title' nowrap>문서 구분<img src='".$required3_path."'></td>
							<td class='input_box_item' >
							  <input type=radio name='popup_div' value='2' id='div_1' ".($db->dt[popup_div] == '2'? 'checked':'')."><label for='div_1'>공문</label>
							  <input type=radio name='popup_div' value='1' id='div_2' ".($db->dt[popup_div] == '1'? 'checked':'')."><label for='div_2'>동의서</label>
							</td>
                        </tr>
					<tr>
						<td class='input_box_title' nowrap colspan='2'> <b>문서 번호</b></td>
						<td class='input_box_item'>".($db->dt[popup_ix] ? "".$db->dt[popup_ix]."":"문서 번호는 자동으로 입력됩니다")."
						</td>
						<td class='input_box_title' nowrap> <b>오늘하루보기 사용 <img src='".$required3_path."'></b></td>
						<td class='input_box_item'>
						<input type='radio' name='popup_today' id='today_1' value='1' ".(($popup_today == "1" || $popup_today == "") ? "checked":"")." validation='true' title='오늘하루보기'> <label for='today_1' >사용</label> <input type='radio' name='popup_today' id='today_0' value='0' ".CompareReturnValue("0",$popup_today,"checked")." validation='true' title='오늘하루보기'><label for='today_0' >미사용</label>
						</td>
					</tr>
					<tr>
						  <td class='input_box_title' nowrap colspan='2'> <b>사용기간 <img src='".$required3_path."'></b></td>
						  <td class='input_box_item' colspan='3'>
							".search_date('popup_use_sdate','popup_use_edate',$popup_use_sdate,$popup_use_edate,'N','D')."
							<input type='checkbox' name='popup_status_end' id='popup_status_end' value='0'>진행 종료
							<!--input type='checkbox' name='popup_status_end' id='popup_status_end' value='2'>진행-->
						</td> 
					 </tr>
 					<tr>
						  <td class='input_box_title' nowrap colspan='2'> <b>첨부 파일 </b></td>
						  <td class='input_box_item' colspan='3'>
							<input type='file' size='30' name='popup_file' id='popup_file'/>현재 첨부된 파일 : ".$db->dt[popup_file]."
						</td> 
					 </tr>
						<tr bgcolor='#F8F9FA'><!--팝업 띄우는 에디터 부분 디스플레이 NONE으로 숨겨둠 2014-04-03 양원석-->
                        <td colspan=5>
							<textarea name='popup_text' id='popup_text'>".$popup_text."</textarea>
                        </td>
                        </tr>
						<tr bgcolor='white'>
						<td colspan=5 align=right style='padding:10px;' bgcolor='white'>";
						$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle> ";
							$Contents .= "<a href='javascript:self.close();'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a>";
						$Contents .= "
						<a href='javascript:self.close();'><input type=\"button\" value=\"미리보기\" onclick=\"if( $('#di_mp_tr_0').is(':visible') ){ $('#di_mp_tr_0').hide();}else{ $('#di_mp_tr_0').show();} \" style='width:100px; height:33px;'></a>
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
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$help_text = HelpBox("셀러 공문서 등록", $help_text);
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
$Script = "<script language='javascript' src='popup.write.js'></script>\n<script type='text/javascript' src='../ckeditor/ckeditor.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";

$P->addScript = $Script;
$P->Navigation = "셀러설정 관리 > 셀러 공문서 등록";
$P->title = "셀러 공문서 등록";
if($popup_position == "A"){
	$P->NaviTitle = "셀러 공문서 등록";
}else{
	$P->NaviTitle = "셀러 공문서 등록";
}
$P->OnloadFunction = "Init(document.INPUT_FORM);MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')";//showSubMenuLayer('storeleft');
$P->strLeftMenu = seller_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();




?>