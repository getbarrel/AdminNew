function FnUserAdd(flag){
	if (flag==1){
		document.getElementById('div_useradd').style.display = '';
	}else if(flag == 2){
		document.getElementById('div_serviceadd').style.display = '';
	}else if(flag == 3){
		document.getElementById('div_servicedrop').style.display = '';
	}else{
		document.getElementById('div_useradd').style.display = 'none';
		document.getElementById('div_serviceadd').style.display = 'none';
	}
}
// 뱅크다 이용자 등록 처리
function FnUserAddWrite(){
	var f = document.addForm;
	if (f.user_id.value == ''){
		alert('이용자 ID를 입력해 주세요.');
		f.user_id.focus();
		return;
	}
	if (f.user_pw.value == ''){
		alert('이용자 비밀번호를 입력해 주세요.');
		f.user_pw.focus();
		return;
	}
	if (f.user_name.value == ''){
		alert('이용자이름(업체명)을 입력해 주세요.');
		f.user_name.focus();
		return;
	}
	if (f.user_tel.value == ''){
		alert('전화번호를 입력해 주세요.');
		f.user_tel.focus();
		return;
	}
	if (f.user_email.value == ''){
		alert('메일주소를 입력해 주세요.');
		f.user_email.focus();
		return;
	}
	f.submit();
}

$(document).ready(function() {
	var first_memocloud;
	$('.memocloud').click(function(e){
		
		if(first_memocloud){
			first_memocloud.remove();
		}
		first_memocloud=MemoView($(this),e);
	});
	
})

function MemoView(obj,e){
		
		var is_addwrap = obj.find('input#idx_ix').val();

		var help_html = obj.attr('help_html_'+is_addwrap);
		var help_width = "200";
		var help_height = "100";
		
		var gepX = obj.width();
		var gepY = 0;
		
		var offset = obj.offset();
		var offsetX = offset.left;
		var offsetY = offset.top;
				
		var html ;
		html = "<table border=0 id='memotext' class='memotext' style='position: absolute;width:"+help_width+"px;height:"+help_height+"px' cellpadding=0 cellspacing=0>";
		html += "<col width=4><col width=*><col width=13><col width=*><col width=4>";
		html += "<tr>";
		html += "	<th class='box_01'></th>";
		html += "	<td class='box_02' colspan=3></td>";
		html += "	<th class='box_03'></th>";
		html += "</tr>";
		html += "<tr>";
		html += "	<th class='box_04'></th>";
		html += "	<td class='box_05 ' colspan=3 style='height:"+help_height+"px;line-height:130%;padding:5px;'>	";
		html += "	<textarea style='background-color:#fff7da;' name='help_html' id='help_html'>"+ help_html +" </textarea>";
		html += "	<input type='button' name='add_html' value='저장' onclick=\"Add_Html($('#help_html').val(),'"+is_addwrap+"')\">";
		html += "	</td>";
		html += "	<th class='box_06'></th>";
		html += "</tr>";
		html += "<tr>";
		html += "	<th class='box_07'></th>";
		html += "	<td class='box_08'></td>";
		html += "	<td style='background-color:#ffffff'></td>";
		html += "	<td class='box_08'></td>";
		html += "	<th class='box_09'></th>";
		html += "</tr>";
		html += "<tr>";
		html += "	<th ></th>";
		html += "	<td ></td>";
		html += "	<td class='box_10'></td>";
		html += "	<td ></td>";
		html += "	<th ></th>";
		html += "</tr>";
		html += "</table>";

		return $(html).css('top',offsetY + gepY).css('left',offsetX + gepX).appendTo('body');
		//return $(html).css('top', e.pageY + offsetY).css('left', e.pageX + offsetX).appendTo('body');
	
		/*
		$('.memocloud').mousemove(function(e){
			//alert(offsetX);
			$('#memocloud').css('top', e.pageY + offsetY).css('left', e.pageX + offsetX);
			//alert($('#helpcloud').attr('height'));
			
		})
		*/
		
}
function Add_Html(MemoValue,bkid){
	
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'memo_update','memo_text': MemoValue,'bkid': bkid},
		url: 'bankda.act.php',  
		dataType: 'html', 
		cache:true,
		error: function(data,error){// 실패시 실행함수
			alert(111);
		},
		success: function(transport){
			alert('입력완료');
			location.reload();
			
		}
	});
	
}
