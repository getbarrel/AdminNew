
function account(frm){
	if(confirm('정말로 선택한 상태로 처리 하시겠습니까?')){
		return true;
	}else{
		return false;
	}
}

/*
$(function() {
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

});
*/

function ChangeOrderDate(frm){
	if(frm.check_search_date.checked){
		$('#startDate').addClass('point_color');
		$('#endDate').addClass('point_color');
	}else{
		$('#startDate').removeClass('point_color');
		$('#endDate').removeClass('point_color');
	}
}

/*
function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;
	$("#start_datepicker").val(FromDate);
	$("#end_datepicker").val(ToDate);
}

function ChangeRegistDate(frm){
	if(frm.check_search_date.checked){
		frm.sdate.disabled = false;
		frm.edate.disabled = false;
	}else{
		frm.sdate.disabled = true;
		frm.edate.disabled = true;
	}
}
*/

var ContentsboxTop = 0;
var ContentsboxTitleHeight = 0;

$(document).ready(function() {
	ContentsboxTitleHeight=$('#scroll_title').css('height');
	$('#scroll_list').css('margin-top',ContentsboxTitleHeight);

	$('#scroll_div').scroll(function() {

			 ContentsboxTop=$('#scroll_title').position().top; 
			
			if(ContentsboxTop < 0){
				$('#scroll_title').css('margin-top',-ContentsboxTop+'px'); 
			}else if(ContentsboxTop > 0){
				$('#scroll_title').css('margin-top',ContentsboxTop+'px'); 
			}else{
				$('#scroll_title').css('margin-top','0px'); 
			}
	});
});



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


function clearAll2(frm){
	for(i=0;i < frm.ac_ix.length;i++){
			frm.ac_ix[i].checked = false;
	}
}

function checkAll2(frm){
	for(i=0;i < frm.ac_ix.length;i++){
			frm.ac_ix[i].checked = true;
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


function clearAll3(frm){
	for(i=0;i < frm.ar_ix.length;i++){
			frm.ar_ix[i].checked = false;
	}
}

function checkAll3(frm){
	for(i=0;i < frm.ar_ix.length;i++){
			frm.ar_ix[i].checked = true;
	}
}

function fixAll3(frm){
	if (!frm.all_fix3.checked){
		clearAll3(frm);
		frm.all_fix3.checked = false;
	}else{
		checkAll3(frm);
		frm.all_fix3.checked = true;
	}
}


function FormatNumber2(num){
        // 만든이:김인현(jasmint@netsgo.com)
        fl=""
        if(isNaN(num)) { alert(language_data['product_input.js']['O'][language]);return 0}
        if(num==0) return num
        
        if(num<0){ 
                num=num*(-1)
                fl="-"
        }else{
                num=num*1 //처음 입력값이 0부터 시작할때 이것을 제거한다.
        }
        num = new String(num)
        temp=""
        co=3
        num_len=num.length
        while (num_len>0){
                num_len=num_len-co
                if(num_len<0){co=num_len+co;num_len=0}
                temp=","+num.substr(num_len,co)+temp
        }
        return fl+temp.substr(1)
}

function FormatNumber3(num){
        num=new String(num)
        num=num.replace(/,/gi,"")
      //  pricecheckmode = false;
        
        return FormatNumber2(num)
}