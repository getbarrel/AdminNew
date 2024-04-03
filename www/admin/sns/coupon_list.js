function deleteProduct(act,id, addQuery){	
	if(confirm(language_data['common']['G'][language])){
	//'정말로 삭제하시겠습니까?'
		window.frames['iframe_act'].location.href='./product_list.act.php?act='+act+'&id='+id+addQuery;
		//document.getElementById('iframe_act').src='./product_list.act.php?act='+act+'&id='+id+addQuery;//kbk
	}
}

function clearAll(frm){
		for(i=0;i < frm.cpid.length;i++){
				frm.cpid[i].checked = false;
		}
}
function checkAll(frm){
       	for(i=0;i < frm.cpid.length;i++){
				frm.cpid[i].checked = true;
		}
}
function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}



function coupon_update(ci_ix){
	if($('#coupon_text_'+ci_ix).val() == "")	{
		alert(language_data['sns_coupon_list.js']['A'][language]);//"쿠폰번호를 넣으세요"
		return false;
	}
	$.ajax({
		url : './coupon_list.act.php',
		type : 'POST',
		data : {act:'list_update', coupon_text:$('#coupon_text_'+ci_ix).val(), ci_ix:ci_ix},
		error: function(data,error){// 실패시 실행함수 
			alert(error);}, 
		success: function(transport){
			if(transport == "OK"){
				$('#status_text_'+ci_ix).text("사용완료");
			}else{
				$('#status_text_'+ci_ix).text("수정실패");
				return false;
			}
		}

	});
}
