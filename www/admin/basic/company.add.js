<!--
function validate(frm)
{
	return true;
}

function DeleteCompany(company_id){
	
	if(confirm(language_data["company.add.js"]["A"][language])){//'거래처를 정말로 삭제하시겠습니까?'
		document.location.href='company.act.php?act=delete&company_id='+company_id
	}
}

function DeleteCompanySeller(company_id){
	
	if(confirm(language_data["company.add.js"]["A"][language])){//'거래처를 정말로 삭제하시겠습니까?'
		document.location.href='seller.act.php?act=delete&company_id='+company_id
	}
}

function download_img(file_name){
	
window.frames['iframe_act'].location.href = 'download.php?file_name='+file_name;

}

function RecommendCompany(company_id, val){
	if(val == "Y") {
		if(confirm(language_data["company.add.js"]["B"][language])){//거래처를 정말로 추천하시겠습니까?
			act.document.location.href='company.act.php?act=recommend&company_id='+company_id+"&recomm="+val
		}
	} else {
		if(confirm(language_data["company.add.js"]["C"][language])){//거래처를 정말로 추천취소하시겠습니까?
			act.document.location.href='company.act.php?act=recommend&company_id='+company_id+"&recomm="+val
		}
	}
}

function checkPermit(obj){		
	var select_obj = eval("document.all."+obj.id);
	var check00 = select_obj[0].checked;
	
	for(i=0;i<select_obj.length;i++){
		select_obj[i].checked = check00;
	}		
}

function checkPermit2(obj){		
	var select_obj = eval("document.all."+obj.id);
	var check00 = select_obj[0].checked;
	
	if(obj.checked == true || check00){
		for(i=0;i<select_obj.length;i++){
			if(select_obj[i].basic_checked == 'true'){
				select_obj[i].checked = true;
			}
		}
	}else{
		
	}
}
	
function deliveryTypeView(type){
	if(type == "1"){
		document.getElementById('policy_input').style.display = "none";
		document.getElementById('price_input').style.display = "none";
		document.getElementById('free_input').style.display = "none";
		document.getElementById('product_input').style.display = "none";
		document.getElementById('price_text').style.display = "";
		document.getElementById('policy_text').style.display = "";
		document.getElementById('free_text').style.display = "";
		document.getElementById('product_text').style.display = "";
		document.getElementById('delivery_freeprice').validation = false;
		document.getElementById('delivery_price').validation = false;
	}else{
		document.getElementById('policy_input').style.display = "";
		document.getElementById('price_input').style.display = "";
		document.getElementById('free_input').style.display = "";
		document.getElementById('product_input').style.display = "";
		document.getElementById('price_text').style.display = "none";
		document.getElementById('policy_text').style.display = "none";
		document.getElementById('free_text').style.display = "none";
		document.getElementById('product_text').style.display = "none";
		document.getElementById('delivery_freeprice').validation = true;
		document.getElementById('delivery_price').validation = true;
	}
}


$(document).ready(function() {   
	$("#delete").click(function(){
		if($("#report_table tr").size() > 5) $("#report_table tr").last().remove();
	});

	$("#add").click(function(){
		var copy = $("#report_table tr").last().clone();
		 copy.find("select,input[type='text']").val("");
		 copy.find("select,input[type='hidden']").val("");
	  $("#report_table").append(copy);
	});
	
	$("#table_close").live("click",function() {

		if($("#report_table tr").size() > 9) $(this).parents('#add_table').remove();
	});
});

$(document).ready(function(){

	$("input[name='com_type']").click(function(){
		var value = $(this).val();
		if(value == 'BR'){
			$("tr.br").show();
			$("tr.po").hide();
			$('tr.br').prop('disabled', false); 
			$('tr.po').prop('disabled', true); 
			$('#is_wharehouse_1').attr('checked',true);
		}
		if(value == 'BP'){
			$("tr.po").show();
			$("tr.br").hide();
			$('tr.po').prop('disabled', false); 
			$('tr.br').prop('disabled', true);
			$('#is_wharehouse_11').attr('checked',true);
		}
		if(value == 'BO'){
			$("tr.po").show();
			$("tr.br").hide();
			$('tr.po').prop('disabled', false);
			$('tr.br').prop('disabled', true); 
			$('#is_wharehouse_11').attr('checked',true);
		}
	});
});

function loadCategory(sel,target,select_type){	//기존 방식과 달리 ajax 로 처리 2014-06-12 이학봉
/*
select_type =	member : 사원관리용 (사원관리는 본사와 지사, 사업소등 거래처만 노출) 
				company : 거래처등록용 (거래처 등록용은 모든 거래처 노출 가능)

*/

	var relation_code = $('select[name='+sel+']').val();
	var target_depth = $('select[name='+target+']').attr('depth');
	
	/*
	if(relation_code == ''){
		$('select[name^=cid]').each(function (){
			$(this).empty();
			$(this).append("<option value='' selected>선택</option>");
		});
	}*/

	$('input[name=cid2]').val(relation_code);

	$.ajax({
	    url : '../basic/basic_relation.load.php',
	    type : 'GET',
	    data : {relation_code:relation_code,
				target_depth:target_depth,
				mode:'select_company',
				select_type : select_type,
				access_type : 'G'
				},
	    dataType: 'json',
	    error: function(data,error){// 실패시 실행함수 
	        alert(error);},
	    success: function(args){
			if(args != null){
				$('select[name='+target+']').empty();
				$('select[name='+target+']').append("<option value='' selected>선택</option>");
				$.each(args, function(index, entry){
					$('select[name='+target+']').append("<option value='"+entry.relation_code+"' >"+entry.com_name+"</option>");
				});
			}else{
				$('select[name='+target+']').empty();
				$('select[name='+target+']').append("<option value='' selected>선택</option>");
			}
        }
    });

	//window.frames['act'].location.href = '../basic/basic_relation.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target+'&type=' + type;

}

function loadDepartment(sel_name,target_name){	//기존 방식과 달리 ajax 로 처리 2014-06-12 이학봉

	var group_ix = $('select[name='+sel_name+']').val();				//본부코드
	
	$.ajax({
	    url : '../basic/basic_relation.load.php',
	    type : 'GET',
	    data : {group_ix:group_ix,
				mode:'select_department'
				},
	    dataType: 'json',
	    error: function(data,error){// 실패시 실행함수 
	        alert(error);},
	    success: function(args){
			if(args != null){
				$('select[name='+target_name+']').empty();
				$('select[name='+target_name+']').append("<option value='' selected>부서선택</option>");
				$.each(args, function(index, entry){
					$('select[name='+target_name+']').append("<option value='"+entry.dp_ix+"' >"+entry.dp_name+"</option>");
				});
			}else{
				$('select[name='+target_name+']').empty();
				$('select[name='+target_name+']').append("<option value='' selected>부서</option>");
			}
        }
    });

}



function insertInputBox(obj){
	//var objs = eval("document.all."+obj);
	var objs=$("table."+obj).find("tr");
	//alert(objs.find("tr").html());
	//var objs = document.getElmentById(obj);
	if(objs.length > 0 ){
		//alert(objs[0]);
		var obj_table = objs[0].cloneNode(true);
		var target_obj = objs[objs.length-1];	
	}else{
		
		var obj_table = objs[0].cloneNode(true);
		var target_obj = objs[0];
	}
	var newRow = objs.clone(true).appendTo($("#region_name_table_add"));  
	
}

function del_table(txt,evt) {
	var pObj=document.getElementById(txt);
	var tg=evt.target?evt.target:evt.srcElement;
	var tg_p=tg.parentNode;
	while(tg_p.parentNode) {
		tg_p=tg_p.parentNode;
		if(tg_p.tagName=="TABLE") {
			break;
		}
	}
	//alert(tg_p.tagName);
	pObj.removeChild(tg_p);
}

function ch_banknum(tg) {//kbk 12/02/15
	var PT_com_number =/^[0-9-]+$/;　　　　　　　　　　　// 숫자,-만 사용가
	if(tg.value.length>0) {
		if(!PT_com_number.test(tg.value)) {
			if(language == "english"){
				alert(" Please enter '"+tg.title+ "' in numbers and `-`. ");
			}else if(language == "korea"){
				alert("'"+tg.title+ "' 는 숫자와 `-` 만 입력하실수 있습니다. 확인후 다시  입력해주세요");
			}
			tg.value="";
		}
	}
}

function bak_page(){

	if(confirm('수정하신 내용이 저장되지 않습니다. 이동하시겠습니까?')){
		history.go(-1);
	}else{
		return false;
	}
}

//-->
