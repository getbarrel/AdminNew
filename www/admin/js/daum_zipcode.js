/**
 * Created by moon on 2019-03-12.
 */
/*DAUM 도로명 주소 시스템 스크립트*/
/*** 도로명 주소와 지번주소를 같이 입력할 경우 사용 ***/
function zipcode_daum_together(zip_type) {
    new daum.Postcode({
        oncomplete: function(data) {
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
            var extraRoadAddr = ''; // 도로명 조합형 주소 변수

            // 법정동명이 있을 경우 추가한다.
            if(data.bname !== ''){
                extraRoadAddr += data.bname;
            }
            // 건물명이 있을 경우 추가한다.
            if(data.buildingName !== ''){
                extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
            // 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
            if(extraRoadAddr !== ''){
                extraRoadAddr = ' (' + extraRoadAddr + ')';
            }
            // 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
            if(fullRoadAddr !== ''){
                fullRoadAddr += extraRoadAddr;
            }

            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            document.getElementById("zipcode1").value = data.postcode1;
            document.getElementById("zipcode2").value = data.postcode2;
            document.getElementById("new_addr1").value = fullRoadAddr;
            document.getElementById("addr1").value = data.jibunAddress;

            // 사용자가 '선택 안함'을 클릭한 경우, 예상 주소라는 표시를 해준다.
            if(data.autoRoadAddress) {
                //예상되는 도로명 주소에 조합형 주소를 추가한다.
                var expRoadAddr = data.autoRoadAddress + extraRoadAddr;
                document.getElementById("guide").innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';

            } else if(data.autoJibunAddress) {
                var expJibunAddr = data.autoJibunAddress;
                document.getElementById("guide").innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';

            } else {
                document.getElementById("guide").innerHTML = '';
            }
        }
    }).open();
}

/*** 선택한 주소만 입력할 경우 사용 ***/
function zipcode_daum_select(zip_type,obj_id) {
    new daum.Postcode({
        oncomplete: function(data) {
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 각 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var fullAddr = ''; // 최종 주소 변수
            var extraAddr = ''; // 조합형 주소 변수

            // 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
            if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                fullAddr = data.roadAddress;

            } else { // 사용자가 지번 주소를 선택했을 경우(J)
                fullAddr = data.jibunAddress;
            }

            // 사용자가 선택한 주소가 도로명 타입일때 조합한다.
            if(data.userSelectedType === 'R'){
                //법정동명이 있을 경우 추가한다.
                if(data.bname !== ''){
                    extraAddr += data.bname;
                }
                // 건물명이 있을 경우 추가한다.
                if(data.buildingName !== ''){
                    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
            }

            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            /*if(zip_type == 1){
             document.getElementById("zipcode1").value = data.postcode1;
             document.getElementById("zipcode2").value = data.postcode2;
             document.getElementById("addr1").value = fullAddr;

             // 커서를 상세주소 필드로 이동한다.
             document.getElementById("addr2").focus();
             }*/

            if(zip_type == 1){
                //document.getElementById("zipcode1").value = data.postcode;
                //document.getElementById("new_zipcode").value = data.zonecode; //5자리로 변경될 때 사용

                document.getElementById("zipcode1").value = data.postcode1;
                document.getElementById("zipcode2").value = data.postcode2;
                document.getElementById("addr1").value = fullAddr;
                document.getElementById("addr2").value = '';
                document.getElementById("addr2").focus();

            }else if(zip_type == 2){
                //document.getElementById("zipcode1_b").value = data.postcode;
                //document.getElementById("zipcode1_b").value = data.zonecode; //5자리로 변경될 때 사용

                document.getElementById("zipcode1_b").value = data.postcode1;
                document.getElementById("zipcode2_b").value = data.postcode2;
                document.getElementById("addr1_b").value = fullAddr;
                document.getElementById("addr2_b").value = '';
                document.getElementById("addr2_b").focus();

            }else if(zip_type == 3){
                //document.getElementById("czipcode1").value = data.postcode;
                //document.getElementById("czipcode1").value = data.zonecode; //5자리로 변경될 때 사용

                document.getElementById("czipcode1").value = data.postcode1;
                document.getElementById("czipcode2").value = data.postcode2;
                document.getElementById("caddr1").value = fullAddr;
                document.getElementById("caddr2").value = '';
                document.getElementById("caddr2").focus();

            }else if(zip_type == 4){
                //document.getElementById("zip_b_1").value = data.postcode;
                //document.getElementById("zip_b_1").value = data.zonecode; //5자리로 변경될 때 사용

                document.getElementById("zip_b_1").value = data.postcode1;
                document.getElementById("zip_b_2").value = data.postcode2;
                document.getElementById("addr_b_1").value = fullAddr;
                document.getElementById("addr_b_2").value = '';
                document.getElementById("addr_b_2").focus();

            }else{
                alert('zip_type 이 없습니다.');
            }
        }
    }).open();
}


function zipcode_daum_layer(zip_type,obj_id) {

    var element_wrap = document.getElementById('wrap');
    // 현재 scroll 위치를 저장해놓는다.
    var currentScroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);
    new daum.Postcode({
        oncomplete: function(data) {
            // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 각 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var fullAddr = data.address; // 최종 주소 변수
            var extraAddr = ''; // 조합형 주소 변수

            // 기본 주소가 도로명 타입일때 조합한다.
            if(data.addressType === 'R'){
                //법정동명이 있을 경우 추가한다.
                if(data.bname !== ''){
                    extraAddr += data.bname;
                }
                // 건물명이 있을 경우 추가한다.
                if(data.buildingName !== ''){
                    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
            }
            if(zip_type == 1){
                opener.document.getElementById("zipcode1").value = data.zonecode;
                opener.document.getElementById("addr1").value = fullAddr;
                opener.document.getElementById("addr2").value= '';
                opener.document.getElementById("addr2").focus();
                $('#zipcode2' , opener.document).val('');

            }else if(zip_type == 2){
                opener.document.getElementById("zipcode1_b").value = data.zonecode;

                opener.document.getElementById("addr1_b").value    = fullAddr;
                opener.document.getElementById("addr2_b").value='';
                opener.document.getElementById("addr2_b").focus();
                $('#zipcode2_b' , opener.document).val('');

            }else if(zip_type == 3){
                opener.document.getElementById("czipcode1").value = data.zonecode;
                opener.document.getElementById("caddr1").value = fullAddr;
                opener.document.getElementById("caddr2").value='';
                opener.document.getElementById("caddr2").focus();
                $('#czipcode2' , opener.document).val('');

            }else if(zip_type == 4){
                opener.document.getElementById("zip_b_1").value = data.zonecode;

                opener.document.getElementById("addr_b_1").value = fullAddr;
                opener.document.getElementById("addr_b_2").value='';
                opener.document.getElementById("addr_b_2").focus();
                $('#zip_b_2' , opener.document).val('');

            }else if(zip_type == 5){
                opener.document.getElementById("return_zip1").value = data.zonecode;

                opener.document.getElementById("return_addr1").value = fullAddr;
                opener.document.getElementById("return_addr2").value='';
                //opener.document.getElementById("addr_b_2").focus();
                $('#return_zip2' , opener.document).val('');

            }else if(zip_type == 9){

                $('#'+obj_id , opener.document).find('#zipcode1').val(data.zonecode);
                $('#'+obj_id , opener.document).find('#addr1').val(fullAddr);
                $('#'+obj_id , opener.document).find('#addr2').val('');
                $('#'+obj_id , opener.document).find('#zipcode2').val('');

            }else{
                alert('zip_type 이 없습니다.');
            }

            if(typeof(window.opener.GetReigonPrice) == "function" || typeof(window.opener.GetReigonPrice) == "object"){	//도서산간 배송비 추가 함수 2014-01-17 이학봉

                window.opener.GetReigonPrice();
            }

            //window.opener.putAddr(zipcode, addrResult);
            self.close();
            // iframe을 넣은 element를 안보이게 한다.
            // (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
            //element_wrap.style.display = 'none';

            // 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
            //document.body.scrollTop = currentScroll;
        },
        // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
        onresize : function(size) {
            element_wrap.style.height = size.height+'px';
        },
        width : '100%',
        height : '100%'
    }).embed(element_wrap);

    // iframe을 넣은 element를 보이게 한다.
    element_wrap.style.display = 'block';
}