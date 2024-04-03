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
		alert(language_data['orders.js']['C'][language]);//배송정보가 정확하지 않습니다. 
	}
}

function zipcode(id,obj_id)
{
	//obj_id 파라미터 추가 2014-04-20 Hong
	var zip = window.open('../member/zipcode.php?zip_type='+id+'&obj_id='+obj_id,'','width=440,height=350,scrollbars=yes,status=no');
}

function return_pop(od_ix)
{
	var zip = window.open('return_reason_pop.php?od_ix='+od_ix,'','width=440,height=350,scrollbars=yes,status=no');
}
/*
function ViewdeliveryCodeInputBox(StatusCode,frm){
	if(StatusCode == "DI" || StatusCode == "TI"){
		document.getElementById("deliverycode").style.display = "inline";
		document.getElementsByName("quick")[0].style.display = "inline";
	}else{
		document.getElementById("deliverycode").style.display = "none";
		document.getElementsByName("quick")[0].style.display = "none";
	}
}
*/

function ViewdeliveryCodeInputBox(StatusCode,Index,admin_level){
	if(admin_level != 9){
		//StatusCode == "DR" || 
		if(StatusCode == "DI" || StatusCode == "TI" || StatusCode == "EG"){
			$('.deliverycode').eq(Index).show();
			$('[name=quick]').eq(Index).show();
			//$('[name=delivery_method]').eq(Index).show();
		}else{
			$('.deliverycode').eq(Index).hide();
			$('[name=quick]').eq(Index).hide();
			//$('[name=delivery_method]').eq(Index).hide();
		}
	}else{
		if(StatusCode == "DR" || StatusCode == "DI" || StatusCode == "EG"){ 
			$('.deliverycode').eq(Index).show();
			$('[name=quick]').eq(Index).show();
			//$('[name=delivery_method]').eq(Index).show();
		}else{
			$('.deliverycode').eq(Index).hide();
			$('[name=quick]').eq(Index).hide();
			//$('[name=delivery_method]').eq(Index).hide();
		}
	}

	if(StatusCode=='IB'){
		$('#reason_code_IB').attr("disabled",true).hide();
		$('.reason_code').attr("disabled",false).show();
	}else{
		$('.reason_code').attr("disabled",true).hide();
	}

}

function ReturnOK(oid){
	
	if(confirm(language_data['orders.js']['A'][language])){//정말로 반품 처리 하시겠습니까?
		document.location.href='orders.act.php?act=stateupdate&oid='+oid;
	}else{
		
	}	
}

function PrintMemberOrder(oid){
	PrintWindow("./orders.print.php?oid="+oid,700,430,'OrderPrint');		
}

function PrintDealingsSheet(oid){
	PrintWindow("./orders.dealings.php?oid="+oid,700,430,'OrderPrint');		
}

function PrintOrderDetail(oid){
	PrintWindow("./orders.view.php?oid="+oid,700,430,'OrderPrint');		
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

function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#startDate').addClass('point_color');
		$('#endDate').addClass('point_color');
	}else{
		$('#startDate').removeClass('point_color');
		$('#endDate').removeClass('point_color');
	}
}


function clearAll2(frm){
	for(i=0;i < frm.oid.length;i++){
			frm.oid[i].checked = false;
	}
}

function checkAll2(frm){
	for(i=0;i < frm.oid.length;i++){
			frm.oid[i].checked = true;
	}
}

function fixAll2(frm){
	if (!frm.all_fix.checked){
		clearAll2(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll2(frm);
		frm.all_fix.checked = true;
	}
}


function clearAll(frm){
		for(i=0;i < frm.od_ix.length;i++){
				frm.od_ix[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.od_ix.length;i++){
				frm.od_ix[i].checked = true;
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



function listAction(frm){
		
	PoPWindow('../sms.pop.php',450,300,'sendsms');
	frm.action = '../sms.pop.php';
	frm.target = 'sendsms';
	frm.submit();
}


function CheckStatusUpdate(frm){
	var checked_bool = false;
	var other_seller_bool = false;
	if(frm.status.value == 'DI'){
		alert(language_data['orders.js']['D'][language]);//배송중 상태의 경우는 주문정보수정 페이지에서 택배사및 송장번호를 입력하신후 수정하시기 바랍니다
		return false;
	}
	if(frm.status.value == "IR"){
		alert(language_data['orders.js']['E'][language]);
		return false;
	}
	for(i=0;i < frm.oid.length;i++){	
		if(frm.oid[i].checked){
			var od_status = eval("document.all.od_status_"+frm.oid[i].value.replace("-",""));
			checked_bool = true	;			
			for(j=1;j < od_status.length;j++){
				if(frm.status.value == od_status[j].value){
					alert(language_data['orders.js']['F'][language]);//현재상태와 변경을 원하시는 상태가 같은 주문이 한개이상 있으면 상태 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.
					return false;
				}else if(frm.status.value == "IC"){
					if(od_status[j].value != 'IR'){	
						alert(language_data['orders.js']['G'][language]);//입금예정 상태가 아닌 주문이 한개이상 포함되어 있으면  입금 확인으로 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.
						return false;
					}
				}else if(frm.status.value == "DR"){
					if(od_status[j].value != 'IC'){	
						alert(language_data['orders.js']['H'][language]);//입금확인 상태가 아닌 주문이 한개이상 포함되어 있으면  배송준비중 으로 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.
						return false;
					}					
				}else if(frm.status.value == "OR"){
					if(od_status[j].value != 'IC'){	
						alert('입금확인 상태가 아닌 주문이 한개이상 포함되어 있으면 해외프로세싱중 으로 변경이 불가능 합니다. \n상태관리가 잘못된 경우는 송장입력 버튼을 클릭하시고 주문상담내역에 정보를 특시사항을 남기신후 상태변경을 하시기 바랍니다.');
						return false;
					}
				}
			}
			
		}
	}
	
	if(!checked_bool){
		alert(language_data['orders.js']['J'][language]);//상태변경하실 주문을 한개이상 선택하셔야 합니다
		return false;
	}else{
		return true;
	}
}



function act(act, oid)
{
	
	if (act == "update")
	{
		var form = eval("document.EDIT_"+oid);

		form.action = 'orders.act.php?act='+act+'&oid='+oid;
		form.submit();
	}

	if (act == "delete")
	{
		if(confirm(language_data['orders.js']['I'][language])){//카테고리를 선택해주세요
			window.frames["iframe_act"].location.href= 'orders.act.php?act='+act+'&oid='+oid;
		}
	}
}

function order_delete(act, oid){
	if (act == "delete")
	{
		if(confirm(language_data['orders.js']['B'][language])){//[카드결제]의 경우는 승인취소후 삭제해주세요. 해당 주문을  정말로 삭제하시겠습니까?
			window.frames["iframe_act"].location.href= 'orders.act.php?act='+act+'&oid='+oid;
		}
	}
}

function orderStatusUpdate(frm,admin_level){
	

	if(AdminTimesecond > 180){
		alert("화면을 유지한지 3분 이상이 지났습니다. 새로고침후 다시 이용해주세요.");
		return false;
	}

	if(frm.status.value=="DI"){
		frm.act.value = "delivery_update";
	}else{
		frm.act.value = "select_status_update";
	}

	var od_ix_str = "";
	if(frm.status.value.length < 1){
		alert(language_data['orders.js']['N'][language]);//'상태정보를 선택해주세요'
		return false;
	}

	var equal_cnt = 0;
	var not_equal_cnt = 0;
	var di_cnt = 0;
	var not_di_cnt = 0;
	var ca_not_cnt = 0;
	var er_not_cnt = 0;
	var company_id="";
	var ea_check="";
	var apply_all="N";

	$(frm).find('.od_ix:checked').each(function(i){
		if(i==0)		od_ix_str = $(this).val();
		else			od_ix_str = od_ix_str +"|"+$(this).val();

		if($(this).attr("od_status") == frm.status.value)			equal_cnt++;
		else														not_equal_cnt++;

		if($(this).attr("od_status") == "DI")							di_cnt++;
		else															not_di_cnt++;

		if($(this).attr("od_status") != "IC" && $(this).attr("od_status") != "DR")
			ca_not_cnt++;

		if($(this).attr("od_status") == "IC" || $(this).attr("od_status") == "DR")
			er_not_cnt++;

		company_id = $(this).attr("company_id");
		ea_check = $(this).attr("ea_check");
	});

	if(equal_cnt > 0 && !frm.status_info_change.checked){
		alert('변경하시고자 하는 상태와 현재상태가 같은 주문이 있습니다. 확인후 다시 시도해주세요');
		return false;
	}

	if(equal_cnt == 0 && not_equal_cnt == 0){
		alert('변경하시고자 하는 주문정보가 하나이상 선택되어야 합니다.');
		return false;
	}

	if(frm.status.value == "CA"  && ca_not_cnt > 0){
		alert('취소요청은 입금확인 과 배송준비중일때만 가능합니다.');
		return false;
	}

	if(frm.status.value == "EA" || frm.status.value == "RA" || frm.status.value == "CA"){

		if(frm.status.value == "EA")		status_text="교환";
		else if(frm.status.value == "CA")	status_text="취소";
		else								status_text="반품";
		
		if(frm.status.value == "EA"){

			//교환시 같은 업체만 선택 가능하도록!
			if($(frm).find('.od_ix:checked').length != $(frm).find(".od_ix[ea_check='"+ea_check+"']:checked").length){
				alert(status_text+'요청은 같은배송정책의 상품끼리만 가능합니다.');
				return false;
			}

		}else{
			
			//전체 취소가 아닐떄!
			if( !($(frm).find('.od_ix:checked').length == $(frm).find('.od_ix').length && frm.status.value == "CA")){
				//반품,취소는 같은 업체만 선택 가능하도록!
				if($(frm).find('.od_ix:checked').length != $(frm).find(".od_ix[company_id='"+company_id+"']:checked").length){
					alert(status_text+'요청은 같은업체의 상품끼리만 가능합니다.');
					return false;
				}
			}else{
				apply_all="Y";
			}
		}
		
		if(frm.status.value == "EA" || frm.status.value == "RA"){
			if(er_not_cnt > 0){
				alert(status_text+'요청은 배송후에 가능합니다.');
				return false;
			}
		}
	}

	if($(frm).find('.order_status').val()=='IR'){//입금예정일시 전체 선택해야만 가능!
		if(frm.status.value == "IC"){
			if((equal_cnt + not_equal_cnt) != equal_cnt){
				alert('입금 확인은 전체주문을 선택 하셔야 합니다.');
				return false;
			}
		}
	}

	if(frm.status.value == "DC"){
		if(di_cnt == 0 && not_di_cnt == 0){
			alert('변경하시고자 하는 주문정보가 하나이상 선택되어야 합니다.');
			return false;
		}

		if(not_di_cnt > 0){
			alert('배송중인 상품만 배송완료 처리 가능합니다.');
			return false;
		}
	}

	
	if(admin_level!=9){
		if(frm.status.value == "DI"){
			if(frm.quick.value == ''){
				alert(language_data['orders.js']['K'][language]);//배송 업체를 선택해주세요
				frm.quick.focus();
				return false;
			}
			if(frm.deliverycode.value.length < 1){
				alert(language_data['orders.js']['L'][language]);//송장번호를 입력해주세요
				frm.deliverycode.focus();
				return false;
			}
		}
	}

	
	/*
	if(frm.status.value == "ED" || frm.status.value == "RD"){ //교환거부 반품거부 상태값 바꾸어야함(제휴사연동값이 중복이라서!!!)
		if(frm.status_message.value == ''){
			alert('거부사유를 입력해주세요.');//배송 업체를 선택해주세요
			frm.status_message.focus();
			return false;
		}
	}*/

	if(frm.status.value=='CA'||frm.status.value=='EA'||frm.status.value=='RA'){
		if(frm.status.value=='CA')		WindowHeight = 500;
		else							WindowHeight = 800;
		ShowModalWindow('claim_apply.php?apply_status='+frm.status.value+'&apply_all='+apply_all+'&od_ix_str='+od_ix_str,1000,WindowHeight,'claim_apply');
		return false;
	}
	

	if(confirm(language_data['orders.js']['M'][language].replace('_STATUS_',frm.status.options[frm.status.selectedIndex].text))){ 
		return true;
	}else{
		return false;
	}
}

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
	dateFormat: 'yy.mm.dd',
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
	dateFormat: 'yy.mm.dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	/*$("input[name='od_ix[]']").click(function(){
		if($(this).is(":checked")){
			if($(this).attr("set_group").length > 0){
				set_group = $(this).attr("set_group");
				$("input[name='od_ix[]']").each(function(){
					if($(this).attr("set_group").length > 0){
						if($(this).attr("set_group")==set_group){
							$(this).prop("checked","checked");
						}
					}
				});
			}
		}else{
			if($(this).attr("set_group").length > 0){
				set_group = $(this).attr("set_group");
				$("input[name='od_ix[]']").each(function(){
					if($(this).attr("set_group").length > 0){
						if($(this).attr("set_group")==set_group){
							$(this).prop("checked","");
						}
					}
				});
			}
		}
	});*///세트 상품 묶어서 체크되는 기능 주석 처리 kbk 13/08/06

});


function ChangeStatus(act, oid, od_ix, this_status, change_status){
	//alert(act+":::"+oid+":::"+od_ix+":::"+this_status+":::"+change_status);
	if(change_status == "IC"){
		if(confirm(language_data['orders.goods_list.js']['I'][language])){//'해당 주문을 입금확인 처리 하시겠습니까?'
			window.frames["iframe_act"].location.href= '../order/orders.goods_list.act.php?act='+act+'&oid='+oid+'&od_ix='+od_ix+'&this_status='+this_status+'&change_status='+change_status;
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

function orders_memo_submit(){
	
	if($('select[name=call_type]').val().length == 0){
		alert('콜 처리유형을 선택해주세요.');
		return false;
	}

	if($('select[name=bbs_div]').val().length == 0){
		alert('상담 분류를 선택해주세요.');
		return false;
	}else{
		return true;
	}
}

function SelectDelete(frm){
	if(confirm('정말로 삭제 하시겠습니까?')){
		frm.action='../order/orders.act.php';
		frm.mmode.value='select_delete';
		frm.submit();
	}
}

function cid_del(code){
    $('#row_'+code).remove();
}

//세트상품일 경우 동시 체크
function checkSet(obj){
	var group = obj.attr('set_group');
	var checked = obj.prop('checked');

	if(group != '') {
		$('.od_ix').each(function() {
			var diff = $(this).attr('set_group');
			if(diff == group) {
				$(this).prop('checked', checked);
			}
		});
    }
}

$(document).ready(function (){

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

//다중검색어 끝 2014-04-10 이학봉

});