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
	//newRow.find("input[id^=architecture_code]").attr("name","architecture["+(total_rows)+"][architecture_code]");
	//alert(objs.length+":::"+target_obj.outerHTML);
	//alert(obj_table.parentElement);
	/*
	var obj_table_text = obj_table.outerHTML;
	obj_table_text = obj_table_text.replace(/style=\"display:none;\"/g," "); // 크롬 kbk
	obj_table_text = obj_table_text.replace(/style=\"display:\snone;\"/g," "); // 파폭 kbk
	obj_table_text = obj_table_text.replace(/style=\"DISPLAY:\snone\"/g," "); // 익스 kbk
	//obj_table_text = obj_table_text.replace("disabled","");
	obj_table_text = obj_table_text.replace(/disabled=\"disabled\"/g," "); // 크롬,파폭 kbk
	obj_table_text = obj_table_text.replace(/disabled/g," "); // 익스 kbk
	*/
	//obj_table_text = obj_table_text.replace("validation=false","validation=true");
	//alert(obj_table_text);
	//target_obj.insertAdjacentHTML("afterEnd",obj_table_text);
	//obj_table_text.insertAfter($("#region_name_table_add"))
}
//테이블 전체 삭제 문제로 스크립트 수정 2012.01.13
/*
function insertInputBox(obj){
	//var objs = eval("document.all."+obj);
	var objs=$("table."+obj);
	//var objs = document.getElmentById(obj);
	if(objs.length > 0 ){
		//alert(objs[0]);
		var obj_table = objs[0].cloneNode(true);
		var target_obj = objs[objs.length-1];	
	}else{
		
		var obj_table = objs[0].cloneNode(true);
		var target_obj = objs[0];
	}

	//alert(objs.length+":::"+target_obj.outerHTML);
	//alert(obj_table.parentElement);
	var obj_table_text = obj_table.outerHTML;
	obj_table_text = obj_table_text.replace(/style=\"display:none;\"/g," "); // 크롬 kbk
	obj_table_text = obj_table_text.replace(/style=\"display:\snone;\"/g," "); // 파폭 kbk
	obj_table_text = obj_table_text.replace(/style=\"DISPLAY:\snone\"/g," "); // 익스 kbk
	//obj_table_text = obj_table_text.replace("disabled","");
	obj_table_text = obj_table_text.replace(/disabled=\"disabled\"/g," "); // 크롬,파폭 kbk
	obj_table_text = obj_table_text.replace(/disabled/g," "); // 익스 kbk
	//obj_table_text = obj_table_text.replace("validation=false","validation=true");
	//alert(obj_table_text);
	target_obj.insertAdjacentHTML("afterEnd",obj_table_text);
}
*/
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

$(function (){
	$('select[name=seller_cid]').change(function (){
		var value = $(this).val();
		$.ajax({
			type:"POST",
			url:"../member/member.act.php",
			dataType:"html",
			data:{
				act:'selectMD',
				cid : value},
			success: function(msg){
				if(msg != "N"){
					$('input[name=md_code]').val(msg);
				}
			}
		});
	});
});
//-->
