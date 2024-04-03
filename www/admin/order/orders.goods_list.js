function OrderViewType(){	
	
		if($('#order_view_type').attr('checked') == true || $('#order_view_type').attr('checked') == "checked"){		
			//alert($('#order_view_type').attr('checked'));
			$.cookie('order_view_type', '1', {expires:1,domain:document.domain, path:'/', secure:0});
		}else{		
			$.cookie('order_view_type', '0', {expires:1,domain:document.domain, path:'/', secure:0});
		}
		document.location.reload();
}

function searchGoodsFlow(delivery_company, invoice_no){
	//document.write('searchGoodsFlow.php?act=search&delivery_company='+delivery_company+'&invoice_no='+invoice_no);
	if(delivery_company != "" && invoice_no != ""){
		
		var f    = document.createElement('form');
		window.frames['iframe_act'].location.href = '/mypage/searchGoodsFlow.php?act=search&delivery_company='+delivery_company+'&invoice_no='+invoice_no;
	}else{
		alert(language_data['orders.goods_list.js']['A'][language]);// 배송정보가 정확하지 않습니다. 
	}
}

function zipcode(id)
{
	var zip = window.open('../member/zipcode.php?type='+id,'','width=440,height=350,scrollbars=yes,status=no');
}

function return_pop(od_ix)
{
	var zip = window.open('return_reason_pop.php?od_ix='+od_ix,'','width=440,height=350,scrollbars=yes,status=no');
}

function ViewdeliveryCodeInputBox(StatusCode,frm){
	
	
	if(StatusCode == "DI"){
		
		document.getElementById("deliverycode").style.display = "inline";
		document.getElementsByName("quick")[0].style.display = "inline";
	}else{
		document.getElementById("deliverycode").style.display = "none";
		
		document.getElementsByName("quick")[0].style.display = "none";
	}
	
	
}


function init_date(FromDate,ToDate) {
	var frm = document.search_frm;
	
	
	for(i=0; i<frm.vFromYY.length; i++) {
		if(frm.vFromYY.options[i].value == FromDate.substring(0,4))
			frm.vFromYY.options[i].selected=true
	}
	for(i=0; i<frm.vFromMM.length; i++) {
		if(frm.vFromMM.options[i].value == FromDate.substring(5,7))
			frm.vFromMM.options[i].selected=true
	}
	for(i=0; i<frm.vFromDD.length; i++) {
		if(frm.vFromDD.options[i].value == FromDate.substring(8,10))
			frm.vFromDD.options[i].selected=true
	}
	
	
	for(i=0; i<frm.vToYY.length; i++) {
		if(frm.vToYY.options[i].value == ToDate.substring(0,4))
			frm.vToYY.options[i].selected=true
	}
	for(i=0; i<frm.vToMM.length; i++) {
		if(frm.vToMM.options[i].value == ToDate.substring(5,7))
			frm.vToMM.options[i].selected=true
	}
	for(i=0; i<frm.vToDD.length; i++) {
		if(frm.vToDD.options[i].value == ToDate.substring(8,10))
			frm.vToDD.options[i].selected=true
	}
	
}


function onLoad(FromDate, ToDate, frm) {
	if(!frm){
		var frm = document.search_frm;
	}
	
	
	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);
	
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;	
	
	init_date(FromDate,ToDate);
	
}



function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#start_datepicker').addClass('point_color');
		$('#end_datepicker').addClass('point_color');
	}else{
		$('#start_datepicker').removeClass('point_color');
		$('#end_datepicker').removeClass('point_color');
	}
}
/*
function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		frm.vFromYY.disabled = false;
		frm.vFromMM.disabled = false;
		frm.vFromDD.disabled = false;
		frm.vToYY.disabled = false;
		frm.vToMM.disabled = false;
		frm.vToDD.disabled = false;
	}else{
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;		
	}
}
*/


function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.search_frm;

	$('#start_datepicker').val(FromDate);
	$('#end_datepicker').val(ToDate);
}

$(document).ready(function (){
	$("#start_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$("#end_datepicker").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});


	$("input[name='od_ix[]']").click(function(){
		if($(this).is(":checked")){
			if($(this).attr("oid")){
				oid = $(this).attr("oid");
				if($(this).attr("set_group").length > 0){
					set_group = $(this).attr("set_group");
					$("input[name='od_ix[]']").each(function(){
						if($(this).attr("set_group").length > 0){
							if($(this).attr("set_group")==set_group && $(this).attr("oid")==oid){
								$(this).prop("checked","checked");
							}
						}
					});
				}
			}
		}else{
			if($(this).attr("oid")){
				oid = $(this).attr("oid");
				if($(this).attr("set_group").length > 0){
					set_group = $(this).attr("set_group");
					$("input[name='od_ix[]']").each(function(){
						if($(this).attr("set_group").length > 0){
							if($(this).attr("set_group")==set_group && $(this).attr("oid")==oid){
								$(this).prop("checked","");
							}
						}
					});
				}
			}
		}
	});

	$('input[name=mult_search_use]').click(function (){
        var value = $(this).attr('checked');

        if(value == 'checked'){
            $('#search_text_input_div').css('display','none');
            $('#search_text_area_div').css('display','');

            $('#search_text_input_div').find("input").css('disabled',true);
            $('#search_text_area_div').find("input").attr('disabled',false);

            $('#search_text_area').attr('disabled',false);
            $('#search_texts').attr('disabled',true);

            //다중검색 일때는 combi 모드로 검색 시 검색 품질에 문제가 발생하여 제공하지 않음
            $('select[name=search_type] option[value^=combi]').hide();
            $('select[name=search_type] option:eq(0)').prop("selected",true);
            // $('select[name=search_type] option[value=combi_name]').hide();
            // $('select[name=search_type] option[value=combi_email]').hide();
            // $('select[name=search_type] option[value=combi_tel]').hide();
            // $('select[name=search_type] option[value=combi_mobile]').hide();

        }else{
            $('#search_text_input_div').css('display','');
            $('#search_text_area_div').css('display','none');

            $('#search_text_area').attr('disabled',true);
            $('#search_texts').attr('disabled',false);

            $('#search_text_input_div').find("input").css('disabled',false);
            $('#search_text_area_div').find("input").attr('disabled',true);

            $('select[name=search_type] option[value^=combi]').show();
            $('select[name=search_type] option:eq(0)').prop("selected",true);
        }
    });

	var mult_search_use = $('input[name=mult_search_use]:checked').val();

    if(mult_search_use == '1'){
        $('#search_text_input_div').css('display','none');
        $('#search_text_area_div').css('display','');

        $('#search_text_area').attr('disabled',false);
        $('#search_texts').attr('disabled',true);

        $('select[name=search_type] option[value^=combi]').hide();
        // $('select[name=search_type] option:eq(0)').prop("selected",true);
    }else{
        $('#search_text_input_div').css('display','');
        $('#search_text_area_div').css('display','none');

        $('#search_text_area').attr('disabled',true);
        $('#search_texts').attr('disabled',false);

        $('select[name=search_type] option[value^=combi]').show();
        // $('select[name=search_type] option:eq(0)').prop("selected",true);
    }

});




function clearAll(frm){
	for(i=0;i < frm.oid.length;i++){
			frm.oid[i].checked = false;
	}
}

function checkAll(frm){
	for(i=0;i < frm.oid.length;i++){
			frm.oid[i].checked = true;
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

//od_ix 용 생성
function clearAll2(frm){
	for(i=0;i < frm.od_ix.length;i++){
			frm.od_ix[i].checked = false;
	}
}

function checkAll2(frm){
	for(i=0;i < frm.od_ix.length;i++){
			frm.od_ix[i].checked = true;
	}
}

function fixAll2(frm){
	if (!frm.all_fix2.checked){
		clearAll2(frm);
		frm.all_fix2.checked = false;
	}else{
		checkAll2(frm);
		frm.all_fix2.checked = true;
	}
}


//oid 및 od_ix 홉합용
function clearAllOdix(oid){
	$('input[type=checkbox]').each(function(){
		if($(this).attr("oid")==oid){
			$(this).prop("checked","");
		}
	});
}

function checkAllOdix(oid){
	$('input[type=checkbox]').each(function(){
		if($(this).attr("oid")==oid){
			$(this).prop('checked','checked');
		}
	});	
}

function fixAllOdix(obj){
if (!obj.checked){
		clearAllOdix(obj.value);
		obj.checked = false;
	}else{
		checkAllOdix(obj.value);
		obj.checked = true;
	}
}


function clearAllOid(frm){
	for(i=0;i < frm.oid.length;i++){
			frm.oid[i].checked = false;
			clearAllOdix(frm.oid[i].value);
	}
}

function checkAllOid(frm){
	for(i=0;i < frm.oid.length;i++){
			frm.oid[i].checked = true;
			checkAllOdix(frm.oid[i].value);
	}
}

function fixAllOid(frm){
	if (!frm.all_fix_oid.checked){
		clearAllOid(frm);
		frm.all_fix_oid.checked = false;
	}else{
		checkAllOid(frm);
		frm.all_fix_oid.checked = true;
	}
}

function listAction(frm){
		
	PoPWindow('../sms.pop.php',450,300,'sendsms');
	frm.action = '../sms.pop.php';
	frm.target = 'sendsms';
	frm.submit();
}

function SetDeliveryInfoCopy(type,obj,product_type,oid_value,set_group_value){
	var od_ix_array = new Array();
	frm=document.listform;
	obj_value = obj.val();
	if(product_type==99){
		j=0;
		for(i=0;i < frm.od_ix.length;i++){
			//alert(frm.od_ix[i].value +':::'+od_ix_value+':::'+frm.od_ix[i].getAttribute("oid") +':::'+oid_value +':::'+frm.od_ix[i].getAttribute("set_group")+':::'+set_group_value);
			if(frm.od_ix[i].getAttribute("oid")==oid_value && frm.od_ix[i].getAttribute("set_group")== set_group_value){
				od_ix_array[j] = frm.od_ix[i].value;
				j++;
			}
		}

		for(i=0;i < od_ix_array.length;i++){
			$("[name='"+type+"["+od_ix_array[i]+"]']").val(obj_value);
		}
	}
}

function help_deliverycode_copy(obj_class_str){
	$('.'+obj_class_str+':first').clone(true).insertAfter($('.'+obj_class_str+':last')).find("input").val('');
}

function help_deliverycode_delete(obj){
	if($(".help_deliverycode_area").length > 1){
		obj.closest(".help_deliverycode_area").remove();
	}else{
		obj.closest(".help_deliverycode_area").find("input").val('');
	}
}

function SelectDeliveryIng(product_type,oid_value,set_group_value,od_ix_value){

	frm=document.listform;
	if(product_type==99){
		for(i=0;i < frm.od_ix.length;i++){
			//alert(frm.od_ix[i].value +':::'+od_ix_value+':::'+frm.od_ix[i].getAttribute("oid") +':::'+oid_value +':::'+frm.od_ix[i].getAttribute("set_group")+':::'+set_group_value);
			if(frm.od_ix[i].getAttribute("oid")==oid_value && frm.od_ix[i].getAttribute("set_group")== set_group_value){
				frm.od_ix[i].checked=true;
			}else{
				frm.od_ix[i].checked=false;
			}
		}
	}else{
		for(i=0;i < frm.od_ix.length;i++){
			if(frm.od_ix[i].value==od_ix_value){
				frm.od_ix[i].checked=true;
			}else{
				frm.od_ix[i].checked=false;
			}
		}
	}
	
	if(CheckStatusUpdate(frm)){
		frm.act.value='invoce_update';
		frm.submit();
	}
}

function CheckStatusUpdate(frm){

	

	var checked_bool = false;
	var other_seller_bool = false;
	var select_oid ="";
	var oid_str ="";
	var select_cnt =0;
	var pre_type = frm.pre_type.value;
	var level ="";


	$('[name=update_kind]').each(function(){
		if($(this).prop('checked')==true){
			level = $(this).val();
		}
	});

	var status_str = level+"_status";
	var status ="";
	var delivery_status_str = level+"_delivery_status";
	var delivery_status ="";
	var reason_code_str = level+"_reason_code";


	$('[name='+status_str+']').each(function(){
		if($(this).prop('checked')==true){
			status = $(this).val();
		}
	});

	$('[name='+delivery_status_str+']').each(function(){
		if($(this).prop('checked')==true){
			delivery_status = $(this).val();
		}
	});

	//빠른송장입력, 포장대기, 송장번호관련작업 일때
	if((pre_type=='IC' && status=='DI') || pre_type=='WMS_WDP' || delivery_status=='invoce_update' || delivery_status=='select_invoce_update' || delivery_status=='invoce_add' || delivery_status=='invoce_delete'){
		if(AdminTimesecond > 180){
			alert("화면을 유지한지 3분 이상이 지났습니다. 새로고침후 다시 이용해주세요.");
			return false;
		}
	}else{
		if(AdminTimesecond > 180){
			alert("화면을 유지한지 3분 이상이 지났습니다. 새로고침후 다시 이용해주세요.");
			return false;
		}
	}

	if(status.length < 1 && delivery_status.length < 1){
		alert('처리상태값을 선택해주세요');
		return false;
	}
	if(frm.update_type.value==2){// 선택한 주문일때

		if(pre_type=='CA'){//배송취소요청리스트
			if(status=='DR'||status=='DD'){//배송준비중,배송지연
				if($('[name='+reason_code_str+']').val().length < 1){
					$('[name='+reason_code_str+']').focus();
					alert('사유를 선택해주세요');
					return false;
				}
			}else if(status=='DI'){//배송중

				/*
				if(frm.delivery_method.value.length < 1){
					frm.delivery_method.focus();
					alert('배송방법을 선택해주세요');
					return false;
				}
				*/

				if(frm.delivery_company.value.length < 1){
					alert('배송업체을 선택해주세요');
					frm.delivery_company.focus();
					return false;
				}
				if(frm.deliverycode.value.length < 1){
					alert('송장번호를 입력해주세요.');
					frm.deliverycode.focus();
					return false;
				}
			}
		}else if(pre_type=='IR'){//입금예정리스트
			if(status=='IB'){
				if($('[name='+reason_code_str+']').val().length < 1){
					$('[name='+reason_code_str+']').focus();
					alert('사유를 선택해주세요');
					return false;
				}
			}
		}else if(pre_type=='IC'){//입금확인리스트
			if(status=='CC'||status=='DD'){
				if($('[name='+reason_code_str+']:enabled').val().length < 1){
					$('[name='+reason_code_str+']:enabled').focus();
					alert('사유를 선택해주세요');
					return false;
				}
			}
		}else if(pre_type=='DR'||pre_type=='WDA'){//발주확인,출고요청리스트
			if(status=='DD'||status=='CC'){
				if($('[name='+reason_code_str+']:enabled').val().length < 1){
					$('[name='+reason_code_str+']:enabled').focus();
					alert('사유를 선택해주세요');
					return false;
				}
			}
		}else if(pre_type=='EA'||pre_type=='EI'){//교환리스트,교환미처리리스트
			if(status=='EY'||status=='EF'||status=='EM'){
				if($('[name='+reason_code_str+']:enabled').val().length < 1){
					$('[name='+reason_code_str+']:enabled').focus();
					alert('사유를 선택해주세요');
					return false;
				}
			}else if(status=='ET'){//교환회수완료
				if($("input[type=checkbox][name='od_ix[]'][stock_use_yn=Y]:checked").length){
					if($('#regist_pi_ix').val()==""){
						alert('회수상품중에 재고관리 상품이 있습니다. 회수 창고를 선택해주세요.');
						return false;
					}
				}
			}
		}else if(pre_type=='RA'||pre_type=='RI'){//반품리스트,반품미처리리스트
			if(status=='RY'||status=='RF'||status=='RM'){
				if($('[name='+reason_code_str+']:enabled').val().length < 1){
					$('[name='+reason_code_str+']:enabled').focus();
					alert('사유를 선택해주세요');
					return false;
				}
			}else if(status=='RT'){//반품회수완료
				if($("input[type=checkbox][name='od_ix[]'][stock_use_yn=Y]:checked").length){
					if($('#regist_pi_ix').val()==""){
						alert('회수상품중에 재고관리 상품이 있습니다. 회수 창고를 선택해주세요.');
						return false;
					}
				}
			}
		}


		if(pre_type=='IR'){
			for(i=0;i < frm.oid.length;i++){
				if(frm.oid[i].checked){
					checked_bool = true;
				}
			}
		}else{
			for(i=0;i < frm.od_ix.length;i++){
				if(frm.od_ix[i].checked){
					checked_bool = true;
					if((pre_type=='CA' && status=='DI') || status=='provider_print' || status=='buyer_print' || status=='combo_print' || status=='noprice_print'||status=='picking_print'){
						if($(frm.od_ix[i]).attr('oid')){
							if(select_oid != $(frm.od_ix[i]).attr('oid')){
								select_oid = $(frm.od_ix[i]).attr('oid');
								if(select_cnt==0)		oid_str += $(frm.od_ix[i]).attr('oid');
								else					oid_str += ','+$(frm.od_ix[i]).attr('oid');
								select_cnt++;
							}
						}
					}else if(pre_type=='WDR'||pre_type=='IC'){//출고대기,빠른송장입력
						//||(pre_type=='WDR'&&delivery_status=='WDR')
						if(status=='DR'||status=='DI'){
							if(frm.od_ix[i].value){
								/*
								if($("[name='delivery_method["+frm.od_ix[i].value+"]']").val().length < 1){
									$("[name='delivery_method["+frm.od_ix[i].value+"]']").focus();
									alert('배송타입을 선택해야 합니다.');
									return false;
								}
								*/

								//$("[name='delivery_method["+frm.od_ix[i].value+"]']").val()=='tekbae' && 
								if($("[name='quick["+frm.od_ix[i].value+"]']").val().length < 1){
									$("[name='quick["+frm.od_ix[i].value+"]']").focus();
									alert('배송업체을 선택해야 합니다.');
									return false;
								}

								//$("[name='delivery_method["+frm.od_ix[i].value+"]']").val()=='tekbae' &&
								if( $("[name='deliverycode["+frm.od_ix[i].value+"]']").val().length < 1){
									$("[name='deliverycode["+frm.od_ix[i].value+"]']").focus();
									alert('송장번호를 입력해야 합니다.');
									return false;
								}
							}
						}
					}
				}
			}
		}

		if(status=='DI' && select_cnt > 1){
			alert('배송중은 같은주문의 상품에 한에서 한주문만 배송중 처리가 가능합니다.');
			return false;
		}

		if(!checked_bool){
			alert('상태변경하실 주문을 한개이상 선택하셔야 합니다.');//
			return false;
		}else{
			//||(pre_type=='WDR' && delivery_status=='WDR')
			if(status=='DI'){//배송중은 delivery_update 쪽에서 모두 처리
				frm.act.value='delivery_update';
			}else if(delivery_status=='WDA' || delivery_status=='WDO' || delivery_status=='WDP' || delivery_status=='WDR'|| delivery_status=='WDACC' || delivery_status=='CUSTOMIZING'){
				frm.act.value='select_delivery_status_update';
			}else if(delivery_status=='invoce_update'||delivery_status=='select_invoce_update'|| delivery_status=='invoce_add' || delivery_status=='invoce_delete'){
				frm.act.value=delivery_status;
			}else{
				frm.act.value='select_status_update';
			}

			if(status=='provider_print'||status=='buyer_print'||status=='combo_print'||status=='noprice_print'||status=='picking_print'){//공급자용,구매자용
				PopSWindow('../order/orders.read.php?mmode=print&print_type='+status+'&oid='+oid_str,950,500,'orders_print');
				//window.open('../order/orders.read.php?mmode=print&print_type='+status+'&oid='+oid_str,'newwindow','height=1000,width=1200,scrollbars=yes');
			}else{
				if(confirm('선택하신 상태로 처리 하시겠습니까?')){
					return true;
				}else{
					return false;
				}
			}
		}
		return false;
	}
}



function order_delete(act, oid){
	if (act == "delete")
	{
		if(confirm(language_data['orders.goods_list.js']['H'][language])){//[카드결제]의 경우는 승인취소후 삭제해주세요. 해당 주문을  정말로 삭제하시겠습니까?
			window.frames["act"].location.href= 'orders.act.php?act='+act+'&oid='+oid;
		}
	}
}

function ChangeStatus(act, oid, od_ix, this_status, change_status){
		//alert(act+":::"+oid+":::"+od_ix+":::"+this_status+":::"+change_status);
		if(change_status == "IC"){
			if(confirm('해당 주문을 입금확인 처리 하시겠습니까?')){//'해당 주문을 입금확인 처리 하시겠습니까?'
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "DR"){
			if(confirm('해당 주문상품을 배송준비중처리 하시겠습니까?')){//'해당 주문상품을 배송준비중처리 하시겠습니까?'
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "DC"){
			if(confirm('해당 주문상품을 배송완료 처리 하시겠습니까?')){//해당 주문상품을 배송완료 처리 하시겠습니까?
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "CC"){
			if(confirm('해당 주문상품을 취소승인을 하시겠습니까?')){//해당 주문상품을 취소승인을 하시겠습니까?
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "EI"){
			if(confirm('해당 주문상품을 교환승인을 하시겠습니까?')){//해당 주문상품을 교환승인을 하시겠습니까?
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "EC"){
			if(confirm('해당 주문상품을 교환배송완료처리 하시겠습니까?')){//language_data['orders.goods_list.js']['N'][language]
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "RI"){
			if(confirm('해당 주문상품을 반품승인처리 하시겠습니까?')){//해당 주문상품을 반품승인처리 하시겠습니까?
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "RC"){
			if(confirm('해당 주문상품을 반품회수완료처리 하시겠습니까?')){//해당 주문상품을 반품회수완료처리 하시겠습니까?
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "FA"){
			if(confirm('해당 주문상품을 환불신청 처리 하시겠습니까?')){//해당 주문상품을 환불신청 처리 하시겠습니까?
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "FC"){
			if(confirm('해당 주문상품을 환불완료 처리 하시겠습니까?')){//해당 주문상품을 환불완료 처리 하시겠습니까?
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "OR"){
			if(confirm('해당 주문상품을 해외프로세싱중 처리 하시겠습니까?')){
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "TR"){
			if(confirm('해당 주문상품을 항공배송준비중 처리 하시겠습니까?')){
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "SO"){
			if(confirm('해당 주문상품을 품절취소 처리 하시겠습니까?')){
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "EF"){//여기서부터 체크
			if(confirm('해당 주문상품을 교환보류 처리 하시겠습니까?')){
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "ET"){
			if(confirm('해당 주문상품을 교환회수완료 처리 하시겠습니까?')){
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "RF"){
			if(confirm('해당 주문상품을 반품보류 처리 하시겠습니까?')){
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "DI"){
			if(confirm('해당 주문상품을 배송중 처리 하시겠습니까?')){
				//조건 걸어주기
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "WDR"){
			if(confirm('해당 주문상품을 출고대기 처리 하시겠습니까?')){
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}else if(change_status == "BF"){
			if(confirm('해당 주문상품을 구매확정 처리 하시겠습니까?')){
				window.frames["act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
			}
		}
}


function orderStatusUpdate(frm){
	//alert($(".od_ix_"+frm.oid.value).serializeArray());
	//var f    = document.createElement('form');
	var od_ix_str = '';
	$(".od_ix_"+frm.oid.value+":checked").each(function(){
		if(od_ix_str == ""){
			od_ix_str = $(this).val();
		}else{
			od_ix_str += ","+ $(this).val();
		}
		
	});
	frm.od_ix_str.value = od_ix_str;
	if(frm.od_ix_str.value == ''){
		alert(language_data['orders.goods_list.js']['W'][language]);//'배송완료 처리할 상품을 선택해주세요'
		return false;
	}

	if(frm.delivery_method.value == ''){
		alert('배송 방법을 선택해주세요.');
		frm.delivery_method.focus();
		return false;
	}

	if(frm.delivery_company.value == ''){
		alert(language_data['orders.goods_list.js']['T'][language]);//배송 업체를 선택해주세요
		frm.delivery_company.focus();
		return false;
	}

	if(frm.deliverycode.value.length < 1){
		alert(language_data['orders.goods_list.js']['U'][language]);//송장번호를 입력해주세요
		frm.deliverycode.focus();
		return false;
	}

	if(confirm(language_data['orders.goods_list.js']['V'][language])){//'선택된 주문상품을 배송처리 하시겠습니까?'
		return true;
	}else{
		return false;
	}
}

function ChangeUpdateForm(selected_id){
	var area = new Array('help_text_level0','help_text_level1','help_text_level2','help_text_level3','help_text_level4');

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			//document.getElementById(selected_id).style.display = 'block';
			$('#'+selected_id).show();
		}else{
			//document.getElementById(area[i]).style.display = 'none';
			$('#'+area[i]).hide();
		}
	}
}

function linecheck(obj){
	if(obj.is(':checked')){
		obj.parent().next().find('input[type=checkbox]').prop('checked',true);
	}else{
		obj.parent().next().find('input[type=checkbox]').prop('checked',false)
	}
}

function initCheck(chk){
	var num = $("input[name^=od_ix]:checked").length;
	var newRows = "";

	if(num == 1){
		$("#detailCnt").html("");
		var detailCnt = $("input[name^=od_ix]:checked").parent().parent().find(".detailCnt").html();
		var mdCheck = "";
		for(i=1; i<=detailCnt; i++){
			if(i == detailCnt){
				mdCheck = "selected";
			}
			newRows += "<option value='"+i+"' "+mdCheck+" >"+i+"</option>";
		}
		$("#detailCnt").html(newRows);
		$("#detailCnt").attr("disabled", false);
		$("#detailCnt").show();
	}else{
		$("#detailCnt").hide();
		$("#detailCnt").attr("disabled", true);
	}
}

function btnConfirmation(oid, od_ix){
	if(confirm('구매확정으로 전환하시겠습니까?\n(처리상태만 변경됩니다.)')){
		window.frames["act"].location.href= '../order/orders.goods_list.act.php?act=confirmation_transform&oid='+oid+'&od_ix='+od_ix;
	}
}

/*function btnComplete(oid, od_ix){
	if(confirm('환불완료로 전환하시겠습니까?\n(처리상태만 변경됩니다.)')){
		window.frames["act"].location.href= '../order/orders.goods_list.act.php?act=complete_transform&oid='+oid+'&od_ix='+od_ix;
	}
}*/