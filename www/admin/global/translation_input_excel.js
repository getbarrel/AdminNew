function deleteProduct(act,id){	
	if(confirm(language_data['product_input_excel.js']['A'][language])){//'정말로 삭제하시겠습니까?'
		window.frames['iframe_act'].location.href='./product_input_excel.act.php?act='+act+'&id='+id;
		//document.getElementById('iframe_act').src='./product_input_excel.act.php?act='+act+'&id='+id;
	}
}

function CheckDelete(frm){
//	alert(document.getElementById('cpid').length);
//	alert(frm.cpid.length);
	for(i=0;i < frm.cpid.length;i++){
		if(frm.cpid[i].checked){
			if(confirm(language_data['product_input_excel.js']['A'][language])){//'정말로 삭제하시겠습니까?'
				return true;
			} else {
				return false;
			}
		}
	}
	alert(language_data['product_input_excel.js']['B'][language]);//'삭제하실 제품을 한개이상 선택하셔야 합니다.'
	return false;
}

function setCategory(cname,cid,depth,pid){
	//alert(1);
	window.frames['act'].location.href='./product_input_excel.php?cid='+cid+'&depth='+depth+'&view=innerview';
	//document.frames["act"].location.href='./product_input_excel.php?cid='+cid+'&depth='+depth+'&view=innerview';
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

var TranslationUploadExcelReg_i = 0;
var trans_key = new Array();

function TranslationUploadExcelReg(){
	$('.upload_excel_infos').each(function(i){
		trans_key[i] = $(this).val();
	});

	TranslationUploadExcelRegAjax(trans_key.length,TranslationUploadExcelReg_i);
}

function TranslationUploadExcelRegAjax(total_no,now_no){
	//alert(now_no);
	$.ajax({ 
		type: 'GET', 
		data: {'act': 'single_data_reg', 'trans_key':trans_key[now_no]},
		url: './translation_excel_input.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
			$('#status_message_'+trans_key[now_no]).html('상품등록 진행중...');
			$('#status_message_'+trans_key[now_no]).html("상품등록 진행중...<img src='/admin/images/indicator.gif' border=0 width=20 height=20 align=absmiddle> ")
		},  
		success: function(data){ 
			//	alert(1);
			TranslationUploadExcelReg_i++;
			
			try{
				if(total_no > now_no){
					$('#status_message_'+trans_key[now_no]).html(data);
					TranslationUploadExcelRegAjax(total_no,TranslationUploadExcelReg_i);
				}
			}catch(e){
				alert(e.message);
			}

		} ,
		error:function(x, o, e){
			alert(x.status + " : "+ o +" : "+e);
		}
	});

}
 
 function makeLanguageFile(trans_type){
 	if(confirm('해당언어 정보를 정말로 재생성 하시겠습니까? 이미 생성되어 있던 파일은 초기화 되게 됩니다.')){
 		window.frames['act'].location.href='./translation_excel_input.act.php?act=make_language_file&trans_type='+trans_type;
		 
 	}
}


 function ChangeTempletLanguage(trans_type){
	 if(confirm('백업진행하신겁니까? 꼭 백업후 진행바랍니다.')){
 		if(confirm('해당 언어 정보로 템플릿을 변역하시겠습니까?')){
 			window.location.href='./translation_excel_input.act.php?act=changetempletlanguage&trans_type='+trans_type;//frames['act'].
		}
 	}
}