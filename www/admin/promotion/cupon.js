
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


function LoadValuesAdv(fYear, fMonth, fDay, fHour, fMin, fSec, createdate)
{

    var str1		= new String(createdate);
    var Today;
    var curYear	= eval(Number(str1.substr(0,4)));
    var curMonth	= eval(Number(str1.substr(5,2)));
    var curDate	= eval(Number(str1.substr(8,2)));

    var i;
    var nStep;

    // Year설정
    nStep=0;

    fYear.options[nStep]	= new Option("", "", false, false);

    nStep++;
    if(fYear.name=="birYY") { // 생일에 대한 구분 kbk
        var start_year=curYear-99;
        var current_year=curYear;
    } else {
        var start_year=2000;
        var current_year=curYear+10;
    }
    for(i=start_year; i<=current_year; i++)
    {
        fYear.options[nStep] = new Option(i, i, false, false);
        nStep++;
    }

    fYear.options[0].selected = true;


    // Month 설정
    nStep=0;

    fMonth.options[nStep] = new Option("", "", false, false);

    nStep++;

    for(i=1; i<=12; i++)
    {
        fMonth.options[nStep] = new Option(i, toZero(i), false, false);
        nStep++;
    }

    fMonth.options[0].selected = true;

    // Date 설정
    var LastDay = checkLastDay(curYear, curMonth);
    nStep=0;

    if(fDay != null) {
        fDay.options[nStep] = new Option("", "", false, false);
        nStep++;

        for(i=1; i<=LastDay; i++)
        {
            fDay.options[nStep] = new Option(i, toZero(i), false, false);
            nStep++;
        }
        fDay.options[0].selected			= true;
    }

    // hour 설정
    nStep=0;

    if(fHour != null) {
        fHour.options[nStep] = new Option("", "", false, false);
        nStep++;

        for(i=0; i<=23; i++)
        {
            fHour.options[nStep] = new Option(i, toZero(i), false, false);
            nStep++;
        }
        fHour.options[0].selected			= true;
    }

    // minute 설정
    nStep=0;

    if(fMin != null) {
        fMin.options[nStep] = new Option("", "", false, false);
        nStep++;

        for(i=0; i<=59; i++)
        {
            fMin.options[nStep] = new Option(i, toZero(i), false, false);
            nStep++;
        }
        fMin.options[0].selected			= true;
    }
    // sec 설정
    nStep=0;

    if(fSec != null) {
        fSec.options[nStep] = new Option("", "", false, false);
        nStep++;

        for(i=00; i<=59; i++)
        {
            fSec.options[nStep] = new Option(toZero(i), toZero(i), false, false);
            nStep++;
        }
        fSec.options[0].selected			= true;
    }
}



function onLoad(FromDate, ToDate) {
    var frm = document.form_cupon;



}

$(document).ready(function (){

    //쿠폰 적용 가격에 구분 처리
    $('input[name=cupon_div]').click(function (){
        clickCouponDiv();
    });

    //쿠폰 이미지 선택 처리
    $('#fcupon_img').change(function (e){
        var val = $(this).val();
        var pathpoint = val.lastIndexOf('.');
        var filepoint = val.substring(pathpoint+1,val.length);
        var filetype = filepoint.toLowerCase();

        if(filetype=='jpg' || filetype=='gif' || filetype=='png' || filetype=='jpeg' || filetype=='bmp') {
            //alert(document.getElementById("preview_cupon").src);

            //form_cupon.preview_cupon.src = obj;
            //document.getElementById("preview_cupon").src = form_cupon.preview_cupon.src;
            //alert(form_cupon.preview_cupon.src);
        } else {
            alert(language_data['cupon.js']['A'][language]);//'이미지 파일만 업로드 하실수 있습니다.'
            e.preventDefault();
            $(this).val('');
            return false;
        }

        if(filetype=='bmp') {
            var upload = confirm(language_data['cupon.js']['B'][language]);//'BMP 파일은 웹상에서 사용하기엔 적절한 이미지 포맷이 아닙니다.\n그래도 계속 하시겠습니까.?'
            if(!upload){
                e.preventDefault();
                $(this).val('');
                return false;
            }
        }

        $('#coupon_file_cancel_img').show();
    })

	$('#coupon_file_cancel_img').click(function (){
        $('#fcupon_img').val('');
        $(this).hide();
	});

    if($('input[name=cupon_div]:checked').length > 0){
        clickCouponDiv();
    }
});

function clickCouponDiv(){

    var val = $('input[name=cupon_div]:checked').val();

    switch(val){
        //장바구니 쿠폰

        case 'C':
            $('#seller_rate').val(0).attr('readonly',true).addClass('point_color');
            $('#cart_coupon_text').show();
            $('#delivery_coupon_text').hide()
            //$('#cupon_sale_type1').attr('disabled',false);
            $('#cupon_sale_type3').attr('disabled',true);
            if($('#cupon_sale_type3').is(':checked') == true){
                $('#cupon_sale_type1').trigger('click');
            }
            $('input[name=overlap_use_yn]').attr('disabled',false);
            $('input[name=discount_use_yn]').attr('disabled',false);
            $('input[name=use_product_type]').attr('disabled',false);
            $('input[name=is_except]').attr('disabled',false);


            $('#cupon_acnt_1').trigger('click');
            $('#cupon_acnt_2').attr('disabled',true);

            $('.cupon_cart_hide').show();

            $('.change_title').text('최소 상품 합계 금액');
            $('.change_title2').text('최대 상품 합계 금액');
            $('.change_text').text('상품 합계 금액 ');

            $('#overlap_use_yn_cart').hide();
            $('#overlap_use_yn_product').show();

            break;
        case 'D':
            $('#seller_rate').val(0).attr('readonly',true).addClass('point_color');
            $('#cupon_sale_type3').attr('disabled',false);
            $('input[name=overlap_use_yn]').attr('disabled',true);
            $('input[name=discount_use_yn]').attr('disabled',true);
            $('input[name=use_product_type]').attr('disabled',true);
            $('input[name=is_except]').attr('disabled',true);

            $('#cupon_acnt_1').trigger('click');
            $('#cupon_acnt_2').attr('disabled',true);

            $('.cupon_cart_hide').show();

            $('.change_title').text('최소 상품 합계 금액');
            $('.change_title2').text('최대 상품 합계 금액');
            $('.change_text').text('상품 합계 금액 ');

            $('#overlap_use_yn_cart').hide();
            $('#overlap_use_yn_product').show();
            //$('#cupon_sale_type1').attr('disabled',true);
            //$('#cupon_sale_type2').trigger('click');
            //$('#cart_coupon_text').hide();
            //$('#delivery_coupon_text').show();
            break;
        default:
            $('#seller_rate').attr('readonly',false).removeClass('point_color');
            $('#cart_coupon_text').hide();
            $('#delivery_coupon_text').hide();
            // $('#cupon_sale_type1').attr('disabled',false);
            $('#cupon_sale_type3').attr('disabled',true);
            if($('#cupon_sale_type3').is(':checked') == true){
                $('#cupon_sale_type1').trigger('click');
            }
            $('input[name=overlap_use_yn]').attr('disabled',false);
            $('input[name=discount_use_yn]').attr('disabled',false);
            $('input[name=use_product_type]').attr('disabled',false);
            $('input[name=is_except]').attr('disabled',false);

            $('#cupon_acnt_2').attr('disabled',false);

            $('.cupon_cart_hide').show();
            $('.change_title').text('최소 상품금액');
            $('.change_title2').text('최대 상품금액');
            $('.change_text').text('상품');

            $('#overlap_use_yn_cart').show();
            $('#overlap_use_yn_product').hide();
            break;
    }

    //금액 계산 처리
    checkCouponPriceRate();
}

function checkCouponPriceRate(jquery_obj){
    //alert(($('#cupon_sale_value').val() == ""));
    if($('#cupon_sale_value').val() == ""){
        //$(jquery_obj).val("");
        //$('#cupon_sale_value_validation_text').html('쿠폰 적용가격을 입력하신후 할인 부담율을 입력하세요');
        //return false;
    }
    if($('#seller_rate').val()){
        var seller_rate = $('#seller_rate').val();
    }else{
        $('#seller_rate').val(0);
        var seller_rate = 0;
    }

    if($('#haddoffice_rate').val()){
        var haddoffice_rate = $('#haddoffice_rate').val();
    }else{
        $('#haddoffice_rate').val(0);
        var haddoffice_rate = 0;
    }
    $('#cupon_sale_value').val(parseInt(seller_rate) + parseInt(haddoffice_rate));
	/*
	 if(jquery_obj.attr('id') == "haddoffice_rate"){
	 //$('#seller_rate').val($('#cupon_sale_value').val()-$('#haddoffice_rate').val());
	 $('#cupon_sale_value').val($('#seller_rate').val() + $('#haddoffice_rate').val());
	 }else if(jquery_obj.attr('id') == "seller_rate"){
	 $('#haddoffice_rate').val($('#cupon_sale_value').val()-$('#seller_rate').val());
	 }

	 if(parseInt($('#haddoffice_rate').val()) < 0){
	 $('#haddoffice_rate').val(0);
	 $('#seller_rate').val($('#cupon_sale_value').val());
	 $('#cupon_sale_value_validation_text').html('할인 부담비율의 합이 쿠폰 적용가격을 초과할수 없습니다.');
	 setTimeout(function(){$('#cupon_sale_value_validation_text').html('')},1000);
	 }
	 if(parseInt($('#seller_rate').val()) < 0 ){
	 $('#haddoffice_rate').val($('#cupon_sale_value').val());
	 $('#seller_rate').val(0);
	 $('#cupon_sale_value_validation_text').html('할인 부담비율의 합이 쿠폰 적용가격을 초과할수 없습니다.');
	 setTimeout(function(){$('#cupon_sale_value_validation_text').html('')},1000);
	 }
	 if(parseInt($('#cupon_sale_value').val()) < (parseInt($('#haddoffice_rate').val()) + parseInt($('#seller_rate').val())) ){
	 $('#haddoffice_rate').val("");
	 $('#seller_rate').val("");
	 $('#cupon_sale_value_validation_text').html('할인 부담비율의 합이 쿠폰 적용가격을 초과할수 없습니다.');
	 return false;
	 }
	 */

}