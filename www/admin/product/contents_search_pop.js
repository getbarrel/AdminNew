function CheckSearch(frm){	
	//alert(frm.search_type.value);
	if(frm.search_type.value.length < 1){
		alert('검색타입을 입력해주세요');
		return false;
	}

	if(frm.search_text.value.length < 1){
		alert('검색어를 입력해주세요');
		return false;
	}
}

function clearAll(){
	$('input[id^=ci_ix_]').attr('checked',false);
}

function checkAll(){
	//alert(55);
     $('input[id^=ci_ix_]').attr('checked',true);
	
}
function fixAll(){
	if ($('#all_fix').attr('checked')){
		checkAll();
		//frm.all_fix.checked = true
	}else{
		clearAll();
		//frm.all_fix.checked = false;
	}
}

function MakeAddOption(){
	
	var c_name = '';
	var ci_ix = '';
	var c_coprice = '';
	var c_sellprice = '';
	var coprice = 0;
	var sellprice = 0;
	if($('input.ci_ix:checked').length > 0){
		$('input.ci_ix:checked').each(function(index){
			
			var leng = $('input.ci_ix:checked').length;

			if($(this).attr('checked') == 'checked'){
				var html = '';
				
				c_name = $(this).attr('c_name');
				ci_ix = $(this).val();
				c_coprice = parseInt($(this).attr('c_coprice'));
				c_sellprice = parseInt($(this).attr('c_sellprice'));
				
				coprice = coprice + parseInt(c_coprice);
				sellprice = sellprice + parseInt(c_sellprice);
				if(index == 0){
					$("input[name=pcode]", opener.document).val(ci_ix);
//					opener.$('input[name="pcode"]').val(ci_ix);
//					opener.$('input[name="ci_ix"]').val(ci_ix);
				}
				
//				if(opener.$('#content_no_'+ci_ix+'').val() == undefined){
				if($('#content_no_'+ci_ix+'', opener.document).val() == undefined){
					
					html += '<div id="content_no_'+ci_ix+'"><input type="hidden" name="ci_ix[]" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick="contents_delete('+ci_ix+')" /></div>';
//					opener.$('#contents_goods_area').append(html);
					$("#contents_goods_area", opener.document).append(html);
				}
				
				
			}
		});
		$("input[name=coprice]", opener.document).val(coprice);
		$("input[name=sellprice]", opener.document).val(sellprice);
//		opener.$('input[name="coprice"]').val(coprice);
//		opener.$('input[name="sellprice"]').val(sellprice);
		self.close();
	}else{
		alert('단위 컨텐츠 품목을 선택해주세요');
	}
}

//활동자료 등록 시 사용
function MakeActivityOption(){
	
	var c_name = '';
	var ci_ix = '';
	var c_type = '';
	var c_file_type = '';
	var state = '';


	if($('input.ci_ix:checked').length > 0){
		$('input.ci_ix:checked').each(function(index){
			
			if($(this).attr('checked') == 'checked'){
				var html = '';
				
				c_name = $(this).attr('c_name');
				ci_ix = $(this).val();
				c_type = $(this).attr('c_type');
				c_file_type = $(this).attr('c_file_type');
				state = $(this).attr('state');
				
					
				html += '<tr>';
				html += '	<td class="list_box_td">'+ci_ix+'</td>';
				html += '	<td class="list_box_td">'+c_name+'</td>';
				html += '	<td class="list_box_td">'+c_type+'</td>';
				html += '	<td class="list_box_td">'+c_file_type+'</td>';
				html += '	<td class="list_box_td">'+state+'</td>';
				html += '	<td class="list_box_td"><img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"   onclick=\'contents_delete(this,"activity")\'  /></td>';
				html += '<input type="hidden" name="ci_ix_activity[]" value="'+ci_ix+'" />';
				html += '</tr>';
			
				opener.$('.activity_area').append(html);
				
			}
		});

		opener.$('.activity_box').show();
		self.close();
	}else{
		alert('단위 컨텐츠 품목을 선택해주세요');
	}
}

//컨텐츠를 단일로 등록할때만 사용 
function MakeAddSingle(ci_id,file_type){

	var c_name = '';
	var ci_ix = '';
	var c_coprice = '';
	var c_sellprice = '';
	var coprice = 0;
	var sellprice = 0;
	var html = '';

	c_name = $('#'+ci_id).attr('c_name');

	ci_ix = $('#'+ci_id).val();
	c_coprice = parseInt($('#'+ci_id).attr('c_coprice'));
	c_sellprice = parseInt($('#'+ci_id).attr('c_sellprice'));
	coprice = coprice + parseInt(c_coprice);
	sellprice = sellprice + parseInt(c_sellprice);

	if(opener.$('input[name="pcode"]').val() != undefined){
		opener.$('input[name="pcode"]').val(ci_ix);
	}
	
	if(file_type == 'plan'){
		
		opener.$('.content_area').remove();
		if(opener.$('#content_no_'+ci_ix+'').val() == undefined){
			html += '<div class="content_area" id="content_no_'+ci_ix+'"><input type="hidden" name="ci_ix" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick=\'contents_delete('+ci_ix+',"plan")\' /></div>';
			opener.$('#contents_goods_area').append(html);
		}
	}else if(file_type == 'plan_all'){
		
		opener.$('.content_all_area').remove();
		if(opener.$('#content_all_no_'+ci_ix+'').val() == undefined){
			html += '<div class="content_all_area" id="content_all_no_'+ci_ix+'"><input type="hidden" name="ci_ix_all" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick=\'contents_delete('+ci_ix+',"plan_all")\' /></div>';
			opener.$('#contents_all_goods_area').append(html);
		}
	}else if(file_type == 'coach'){
		opener.$('.content_coach_area').remove();
		if(opener.$('#content_coach_no_'+ci_ix+'').val() == undefined){
			html += '<div class="content_coach_area" id="content_coach_no_'+ci_ix+'"><input type="hidden" name="ci_ix_coach" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick=\'contents_delete('+ci_ix+',"coach")\' /></div>';
			opener.$('#contents_coach_goods_area').append(html);
		}
	}else if(file_type == 'area'){
		opener.$('.participation_goods_area').remove();
		if(opener.$('#participation_area_no_'+ci_ix+'').val() == undefined){
			html += '<div class="participation_goods_area" id="participation_area_no_'+ci_ix+'"><input type="hidden" name="ci_ix_area" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick=\'contents_delete('+ci_ix+',"area")\' /></div>';
			opener.$('#participation_goods_area').append(html);
		}
	}else if(file_type == 'detail'){
		opener.$('.participation_goods_detail').remove();
		if(opener.$('#participation_detail_no_'+ci_ix+'').val() == undefined){
			html += '<div class="participation_goods_detail" id="participation_detail_no_'+ci_ix+'"><input type="hidden" name="ci_ix_detail" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick=\'contents_delete('+ci_ix+',"detail")\' /></div>';
			opener.$('#participation_goods_detail').append(html);
		}
	}else if(file_type == 'eduplan'){
		opener.$('.participation_goods_eduplan').remove();
		if(opener.$('#participation_eduplan_no_'+ci_ix+'').val() == undefined){
			html += '<div class="participation_goods_eduplan" id="participation_eduplan_no_'+ci_ix+'"><input type="hidden" name="ci_ix_eduplan" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick=\'contents_delete('+ci_ix+',"eduplan")\' /></div>';
			opener.$('#participation_goods_eduplan').append(html);
		}
	}else if(file_type == 'multi_1'){		
		opener.$('.content_1_area').remove();
		if(opener.$('#content_no_1_'+ci_ix+'').val() == undefined){
			html += '<div class="content_1_area" id="content_no_1_'+ci_ix+'"><input type="hidden" name="ci_ix_movie[]" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick=\'contents_delete('+ci_ix+',"multi_1")\' /></div>';
			opener.$('#contents_goods_area_1').append(html);
		}
	}else if(file_type == 'multi_2'){		
		opener.$('.content_2_area').remove();
		if(opener.$('#content_no_2_'+ci_ix+'').val() == undefined){
			html += '<div class="content_2_area" id="content_no_2_'+ci_ix+'"><input type="hidden" name="ci_ix_movie[]" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick=\'contents_delete('+ci_ix+',"multi_2")\' /></div>';
			opener.$('#contents_goods_area_2').append(html);
		}
	}else if(file_type == 'multi_3'){
		opener.$('.content_3_area').remove();
		if(opener.$('#content_no_3_'+ci_ix+'').val() == undefined){
			html += '<div class="content_3_area" id="content_no_3_'+ci_ix+'"><input type="hidden" name="ci_ix_movie[]" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick=\'contents_delete('+ci_ix+',"multi_3")\' /></div>';
			opener.$('#contents_goods_area_3').append(html);
		}
	}else if(file_type == 'multi_4'){		
		opener.$('.content_4_area').remove();
		if(opener.$('#content_4_no_'+ci_ix+'').val() == undefined){
			html += '<div class="content_4_area" id="content_no_4_'+ci_ix+'"><input type="hidden" name="ci_ix_movie[]" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick=\'contents_delete('+ci_ix+',"multi_4")\' /></div>';
			opener.$('#contents_goods_area_4').append(html);
		}
	}else if(file_type == 'multi_5'){		
		opener.$('.content_5_area').remove();
		if(opener.$('#content_no_5_'+ci_ix+'').val() == undefined){
			html += '<div class="content_5_area" id="content_no_5_'+ci_ix+'"><input type="hidden" name="ci_ix_movie[]" value="'+ci_ix+'" /><input type="text" value="'+c_name+'"  disabled/> <img src="../images/btn_x.gif" alt="해당 옵션 삭제" title="해당 옵션 삭제" style="vertical-align: middle;"  onclick=\'contents_delete('+ci_ix+',"multi_5")\' /></div>';
			opener.$('#contents_goods_area_5').append(html);
		}
	}
	
		
	if(opener.$('input[name="coprice"]').val() != undefined){
		opener.$('input[name="coprice"]').val(coprice);
		opener.$('input[name="sellprice"]').val(sellprice);
	}


	self.close();
	
}

