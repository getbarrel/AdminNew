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

var UploadExcelGoodsReg_i = 0;
var p_no = new Array();

function UploadExcelGoodsReg(page_type){
	$('.upload_excel_infos').each(function(i){
		p_no[i] = $(this).val();
	});
	

	var check_array = new Array();
	$('input[name^=update_check_]:checked').each(function (){
		
		var value = $(this).val();
		var value_name = $(this).attr('name');
		//alert(value_name);
		check_array.push('{'+value_name+' : '+value+'}');
		//alert(value_name);
	});

	//var json = eval(check_array);
	//var json = eval("(" + check_array + ")");
	//var check_data = JSON.stringify(check_array);
	
	//대량수정시 체크박스 선택된건만 수정됨 2014-08-18 이학봉
	var check_data = $('input[name^=update_check_]:checked').serializeArray();

	UploadExcelGoodsRegAjax(p_no.length,UploadExcelGoodsReg_i,page_type,check_data);
}

function replaceToUpper(key, value) {
    return value.toString().toUpperCase();
}

function UploadExcelGoodsRegAjax(total_no,now_no,page_type,check_data){

	$.ajax({ 
		type: 'GET', 
		data: {'act': 'single_goods_reg', 'p_no':p_no[now_no], 'page_type':page_type, 'check_data':check_data},
		url: './product_update_excel.act.php',  
		dataType: 'html', 
		async: true, 
		beforeSend: function(){ 
			//$('#status_message_'+p_no[now_no]).html('상품등록 진행중...');
			$('#status_message_'+p_no[now_no]).html("상품등록 진행중...<img src='/admin/images/indicator.gif' border=0 width=20 height=20 align=absmiddle> ")
		},
		success: function(data){ 
			UploadExcelGoodsReg_i++;
			//alert(data);
			try{
				if(total_no > now_no){
					$('#status_message_'+p_no[now_no]).html(data);
					UploadExcelGoodsRegAjax(total_no,UploadExcelGoodsReg_i,page_type,check_data);
				}else{
					//alert(total_no +">"+ now_no);
					if(confirm('등록완료되었습니다. 등록이 실패한 상품정보를 엑셀로 다운받으시겠습니까?')){
						location.href='./product_update_excel.act.php?act=bad_goods_info_excel&page_type='+page_type;
						
					}
					/*
					if(confirm('등록완료되었습니다. 새로고침 하시겠습니까?')){
						parent.document.location.reload();
					}else{
						
					}
					*/
				
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


$(document).ready(function (){

	
	$('#check_all').click(function (){
	
		var value = $(this).attr('checked');

		if(value == 'checked'){
			$('input[name^=update_check_]').attr('checked','checked');
		
		}else{
			$('input[name^=update_check_]').attr('checked',false);
		}
	
	});

});