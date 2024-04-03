$(document).ready(function(){
		$('.c_type').click(function(){
			var view_id = $(this).val();
			$('.c_file_type').hide();
			$('.c_file_type input').attr('disabled',true);
			$('.'+view_id).show();
			$('.'+view_id+' input').attr('disabled',false);
		});

		$('.up_file_type').click(function(){
			var file_type = $(this).val();
			
			if(file_type == 'F'){
				$('.type_file').show();
				$('.type_url').hide();
				$('.type_sauce').hide();

				$('.data_info_file').attr('disabled',false);
				$('.data_info_url').attr('disabled',true);
				$('.data_info_sauce').attr('disabled',true);

				$('.data_info_file').css('validation',true);
				$('.data_info_url').css('validation',false);
				$('.data_info_sauce').css('validation',false);

			}else if(file_type == 'U'){
				$('.type_file').hide();
				$('.type_url').show();
				$('.type_sauce').hide();

				$('.data_info_file').attr('disabled',true);
				$('.data_info_url').attr('disabled',false);
				$('.data_info_sauce').attr('disabled',true);

				$('.data_info_file').css('validation',false);
				$('.data_info_url').css('validation',true);
				$('.data_info_sauce').css('validation',false);
			}else if(file_type == 'S'){
				$('.type_file').hide();
				$('.type_url').hide();
				$('.type_sauce').show();

				$('.data_info_file').attr('disabled',true);
				$('.data_info_url').attr('disabled',true);
				$('.data_info_sauce').attr('disabled',false);
				
				$('.data_info_file').css('validation',false);
				$('.data_info_url').css('validation',false);
				$('.data_info_sauce').css('validation',true);
			}
		});

		
});

function tmp_file_upload(){
	var up_file = $('#up_file')[0].files[0];
	console.log(up_file)
	var formData = new FormData();
	var file_area_num = $('#file_area_num').val();

	formData.append('act', 'tmp_file_upload');
	formData.append('up_file', up_file);

//		console.log(up_file.name)
	
	if(up_file == undefined){
		alert('파일을 선택해 주세요.');
		return false;
	}

	$.ajax({
		url: './contents_goods_input.act.php',
		data: formData,
		processData: false,
		contentType: false,
		type: 'POST',
		success: function(result){
			if(result == 0){
//				$('.file_area').append('<div class=\'floatL file_'+file_area_num+'\'><span class=\'vm Pgap_L10\'>'+up_file.name+'</span><img src=\'../images/btn_x.gif\' class=\'vm\' style=\'cursor:pointer;margin:0px 3px;\' onclick=\'delete_tmp_img(\"file_'+file_area_num+'\",\"'+up_file.name+'\")\' ><input type=\'hidden\' class=\'data_info_file\' name=\'data_info[]\' value=\"'+up_file.name+'\" /></div>');

				$(".file_area").append("<div class='tmp_data floatL file_"+file_area_num+"'><span class='vm Pgap_L10'>"+up_file.name+"</span><img src='../images/btn_x.gif' class='vm' style='cursor:pointer;margin:0px 3px;' onclick=\"delete_tmp_img('file_"+file_area_num+"','"+up_file.name+"')\" ><input type='hidden' class='data_info_file' name='data_info[]' value='"+up_file.name+"' /></div>");
				$('#file_area_num').val(parseInt(file_area_num)+1);
			}else if(result == 1){
				alert('동일한 파일명이 존재 합니다. 변경 후 재등록 바랍니다.');
			}else{
				alert('등록 실패');	
			}
		}
	});

}

function delete_tmp_img(img_area,img_name){
	var formData = new FormData();

	formData.append('act', 'tmp_file_delete');
	formData.append('delete_file', img_name);

	$.ajax({
		url: './contents_goods_input.act.php',
		data: formData,
		processData: false,
		contentType: false,
		type: 'POST',
		success: function(result){
			$('.'+img_area).remove();
		}
	});
}

function delete_data_img(img_area,img_name,ci_ix){
	var formData = new FormData();

	formData.append('act', 'file_delete');
	formData.append('delete_file', img_name);
	formData.append('cd_data', img_area);
	formData.append('ci_ix', ci_ix);

	$.ajax({
		url: './contents_goods_input.act.php',
		data: formData,
		processData: false,
		contentType: false,
		type: 'POST',
		success: function(result){
			if(result == 0){
				$('.'+img_area).remove();
			}else{
				alert('삭제실패');
			}
		}
	});
}

function delete_tmp_all(){
	if(confirm('임시저장 파일을 삭제 하시겠습니까?')){
		var formData = new FormData();

		formData.append('act', 'tmp_file_delete_all');

		$.ajax({
			url: './contents_goods_input.act.php',
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function(result){
				$('.tmp_data').remove(); 
				alert('임시파일이 삭제 되었습니다.');
			}
		});
	}
}
function SubmitX(frm){
	if(!CheckFormValue(frm)){
		return false;
	}else{
		return true;
	}
}

function contents_goods_history(ci_ix){
	var formData = new FormData();

	formData.append('act', 'history_view');
	formData.append('ci_ix', ci_ix);

	$.ajax({
		url: './contents_goods_input.act.php',
		data: formData,
		processData: false,
		contentType: false,
		type: 'POST',
		success: function(result){
			
		}
	});
}