<?
include("../class/layout.class");

$db = new MySQL;

$db->query("SELECT * FROM shop_search_text where st_ix= '$st_ix'");

if($db->total){
	$db->fetch();
	$act = "update";
	$st_ix = $db->dt[st_ix];
	$mall_ix = $db->dt[mall_ix];
	
	$st_text = $db->dt[st_text];
	$st_type = $db->dt[st_type];
	$st_title = $db->dt[st_title];
	$st_url = $db->dt[st_url];
	$disp = $db->dt[disp];
	$display_position = $db->dt[display_position];
	

	$st_sdate = date("Y-m-d",$db->dt[st_sdate]);
	$st_sdate_stime = date("H",$db->dt[st_sdate]);
	$st_sdate_smin = date("i",$db->dt[st_sdate]);

	$st_edate = date("Y-m-d",$db->dt[st_edate]);
	$st_edate_etime = date("H",$db->dt[st_edate]);
	$st_edate_emin = date("i",$db->dt[st_edate]);

}else{

	$act = "insert";
	$st_sdate = "";
	$st_edate = "";
	$disp = "1";
	$st_type = "1";
	$display_position = 'A';

}


$Script = "
<link rel='stylesheet' type='text/css' href='/admin/v3/css/jquery-ui.css' />
<script type='text/javascript' src='/js/ui/jquery-ui-1.8.9.custom.js'></script>


<Script Language='JavaScript'>

function SubmitX(frm){

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}

	return true;
}

function change_st_type(){

	var st_type_val = $('input[name=st_type]:checked').val();

	if(st_type_val==1){ //텍스트
		$('#title_tr_text').show();
		$('#title_tr_img').hide();

		$('[name=st_title]').attr('validation','true');
		$('[name=st_img]').attr('validation','false');
		$('[name=img_st_url]').attr('validation','false');
	}else{ //이미지
		$('#title_tr_text').hide();
		$('#title_tr_img').show();
		
		if($('[name=act]').val()=='update'){
			$('[name=st_img]').attr('validation','false');
		}else{
			$('[name=st_img]').attr('validation','true');
		}

		$('[name=st_title]').attr('validation','false');
		
		$('[name=img_st_url]').attr('validation','true');
	}

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


$(document).ready(function () {

	change_st_type();

	$(\"#start_datepicker\").datepicker({
		//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		//showMonthAfterYear:true,
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
				$('#end_datepicker').val(dateText);
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

});


</Script>";


$Contents = "
<table width='100%' border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("검색어 등록", "검색어관리 < 검색어 등록")."</td>
</tr>
  <tr>
    <td>
        <form name='st_frm' method='post' onSubmit=\"return SubmitX(this)\" action='search_text.act.php' style='display:inline;' enctype='multipart/form-data' target='act'>
		<input type='hidden' name=act value='$act'>
		<input type='hidden' name=st_ix value='$st_ix'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td >
                    <table border='0' cellpadding=3 cellspacing=0 width='100%' class='input_table_box'>
						<col width='20%'>
						<col width='30%'>
						<col width='20%'>
						<col width='30%'>";
						if($_SESSION["admin_config"][front_multiview] == "Y"){
						$Contents .= "
						<tr>
							<td class='input_box_title' > 프론트 전시 구분</td>
							<td class='input_box_item' style='padding-left:10px;' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
						</tr>";
						}
						$Contents .= "
					  
                      <tr height=28>
                        <td class='input_box_title' ><b>검색어 관리명 <img src='".$required3_path."'></b></td>
                        <td class='input_box_item' colspan=3 style='padding:10px;'>
							<input type='text' name='st_text' class='textbox' value='".$st_text."' maxlength='80' style='width:98%' validation='true' title='검색어 관리명'>
						</td>
                      </tr>";
/*
$Contents .= "
                      <tr height=27 >
						  <td class='input_box_title'> <b>노출기간 <img src='".$required3_path."'></b></td>
						  <td class='input_box_item' colspan=3 style='padding:10px;'>
							<table cellpadding=0 cellspacing=0 border=0 bgcolor=#ffffff >
								<tr>
									<TD width=80px nowrap>
									<input type='text' name='st_sdate' class='textbox' value='".$st_sdate."' style='height:18px;width:70px;text-align:center;' id='start_datepicker'>
									</td>
									<td align=center width=20px  style='text-align:center;'> 일</td>
									<td nowrap>
									<SELECT name='st_sdate_stime'>";

									for($i=0;$i < 24;$i++){
				$Contents .= "<option value='".$i."' ".($st_sdate_stime == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 시
									<SELECT name='st_sdate_smin'>";
									for($i=0;$i < 60;$i++){
				$Contents .= "<option value='".$i."' ".($st_sdate_smin == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 분
									</TD>
									<TD width=30px  align=center> ~ </TD>
									<TD width=80px nowrap>
									<input type='text' name='st_edate' class='textbox' value='".$st_edate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'>
									</td>
									<td align=center width=20px > 일</td>
									<td nowrap>
									<SELECT name='st_edate_etime'>";

									for($i=0;$i < 24;$i++){
				$Contents .= "<option value='".$i."' ".($st_edate_etime == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 시
									<SELECT name='st_edate_emin'>";
									for($i=0;$i < 60;$i++){
				$Contents .= "<option value='".$i."' ".($st_edate_emin == $i ? "selected":"").">".$i."</option>";
									}
				$Contents .= "
									</SELECT> 분
									</TD>
								</tr>
							</table>
						  </td>
						</tr>";
*/
$Contents .= "	<tr>
							  <td class='input_box_title' nowrap > <b>노출기간 <!--img src='".$required3_path."'--></b></td>
							  <td class='input_box_item' style='padding:10px;' colspan=3>
								".search_date('st_sdate','st_edate',$st_sdate,$st_edate,'Y','A')."
							</td> 
						</tr>
						<tr height=27>
							<td class='input_box_title'> <b>노출 타입<img src='".$required3_path."'></b></td>
							<td class='input_box_item'>
								<input type='radio' name='st_type' id='st_type_1' value='1' ".CompareReturnValue("1",$st_type,"checked")." onclick=\"change_st_type()\"> <label for='st_type_1' >텍스트</label>
								<input type='radio' name='st_type' id='st_type_2' value='2' ".CompareReturnValue("2",$st_type,"checked")." onclick=\"change_st_type()\"><label for='st_type_2' >이미지</label>
							</td>
							<td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b></td>
							<td class='input_box_item' >
								<input type='hidden' name='pop' value='1'> <input type='radio' name='disp' id='disp_1' value='1' ".CompareReturnValue("1",$disp,"checked")."> <label for='disp_1' >사용</label>
								<input type='radio' name='disp' id='disp_0' value='0' ".CompareReturnValue("0",$disp,"checked")."><label for='disp_0' >미사용</label>
							</td>
						  </tr>
						<tr height=27 id='title_tr_text'  ".($st_type == "1" || $st_type == "" ? "style='display:table-row;'":"display:none;").">
							<td class='input_box_title'>노출 문구</td>
							<td class='input_box_item' colspan=3 style='padding:10px;'>
							<table cellpadding=0 cellspacing=0 width='100%'>
								<tr height=30>
									<td width='50px'>텍스트</td>
									<td width='10px'> : </td>
									<td width='*'><input type='text' name='st_title' class='textbox' value='".$st_title."' maxlength='30' style='width:50%' validation='true' title='타이틀'></td>
								</tr>
								<tr height=30>
									<td>링 크</td>
									<td > : </td>
									<td>
										<input type='text' name='text_st_url' class='textbox' value='".$st_url."' maxlength='100' style='width:70%' validation='false' title='링크'>
										<br/><div style='padding-top:5px;'> ex> http://urlname.com/index.php <- http:// 포함된 풀URL 입력</div>
									</td>
								</tr>
							</table>
							</td>
						</tr>
						<tr height=27 id='title_tr_img' ".($st_type == "2" ? "style='display:table-row;'":"display:none;").">
							<td class='input_box_title'> 노출이미지</td>
							<td class='input_box_item' colspan=3 style='padding:10px;'>
							<table cellpadding=0 cellspacing=0 width='100%'>
								<tr height=30>
									<td width='50px'>이미지</td>
									<td width='10px'> : </td>
									<td width='*'><input type='file' class='textbox' name='st_img' validation='true' title='이미지'></td>
									<td align='left'>";

										if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/search/$st_ix/".$st_ix.".gif")){
											$Contents .= "<img src='".$admin_config[mall_data_root]."/images/search/$st_ix/".$st_ix.".gif' align=absmiddle >";
										}

										$Contents .= "
									</td>
								</tr>
								<tr height=30>
									<td>링 크</td>
									<td > : </td>
									<td>
										<input type='text' name='img_st_url' class='textbox' value='".$st_url."' maxlength='30' style='width:70%' validation='true' title='링크'>
										<br/><div style='padding-top:5px;'> ex> http://urlname.com/index.php <- http:// 포함된 풀URL 입력</div>
									</td>
									<td></td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap  >노출위치 설정</td>
							<td colspan='3' class='search_box_item' style='padding:10px;'> 
								<div style='padding-bottom:10px;'>
									<input type='radio' class='textbox' name='display_position' id='display_position_a' size=50 value='A' style='border:0px;' ".(($display_position == "A" || $display_position == "") ? "checked":"")." onclick=\"$('#display_position_area').hide();\"/><label for='display_position_a'>전체</label>
									<input type='radio' class='textbox' name='display_position' id='display_position_c' size=50 value='C' style='border:0px;' ".($display_position == "C"  ? "checked":"")." onclick=\"$('#display_position_area').show();\"/><label for='display_position_c'>카테고리별  </label>
									<br>
								</div>
								<div id='display_position_area' ".($display_position == "C" ? "style='display:block' ":"style='display:none'").">
									<div id='display_position_area_C' >
										<table   border='0'  cellpadding=0 cellspacing=0 >								
											<tr>
												<td width='300'>
													<table  border='0' cellpadding=0 cellspacing=0 align='center'>
														<tr align='left'>
															<td width='100'>   
																<input type=text class=textbox name='search_text'  id='search_text' style='width:310px;margin-bottom:2px;' value='' onkeyup=\"SearchInfo('C',$('DIV#display_position_area_C'), 'category');\">  
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
																<select name='search_result[]' class='search_result' id='search_result_category'  style=' width:420px;height:148px;font-size:12px;background:#fff;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' multiple>											
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
																<select name='selected_result[category][]' class='selected_result' id='selected_result_category'  style='width:400px;height:148px;font-size:12px;background:#fff;padding:5px;border-radius:0px;box-shadow:inset 0px 0px 0px 0px #e6e6e6;' id='' validation=false title='카테고리' multiple>
																";
																//$vip_array = get_vip_member('4');
																$sql = "SELECT ci.cid, ci.depth,  ci.cname FROM shop_category_info ci, shop_search_category_relation  scr 
																			where ci.cid = scr.cid and st_ix = '".$st_ix."'  ";
																$db->query($sql);
																$selected_categorys = $db->fetchall();
																

																for($j = 0; $j < count($selected_categorys); $j++){
																	$Contents .="<option value='".$selected_categorys[$j][cid]."' ondblclick=\"MoveSelectBox($('DIV#display_position_area'), 'C','REMOVE','category');\" selected>".strip_tags(getCategoryPath($selected_categorys[$j][cid],$selected_categorys[$j][depth])) ."</option>";
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
                    </table>
                  </td>
                </tr>
				
				<tr>
					<td colspan=3 align=right style='padding-top:10px;' class='input_box_item'>
						<table width=100%  border=0>
							<col width= '*' >
							<col width= '100' >
							<col width= '100' >
							<col width= '100' >
							<tr>
								<td></td>
								<td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle></td>
								<td><a href='search_text_list.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a></td>
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

	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td><b>검색어 등록</b></td></tr></table>", $help_text,160);

$Contents .= "
  <tr>
    <td align='left' style='padding-bottom:200px;'>
		".$help_text."
    </td>
  </tr>";

$Contents .= "
	</table>";

$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션(마케팅) > 검색어 등록";
$P->title = "검색어 등록";
$P->OnloadFunction = "";
$P->strLeftMenu = promotion_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>