var keycode_Enter       = 13;
var list_limit          = 10;   //15
var list_paginglimit    = 10;

var activeDialogs = new Array();

function activateDialog(dialog) {
    activeDialogs[activeDialogs.length] = dialog;
    
    //bind Enter-Key-pressing event handler to the dialog   
    dialog.keypress(function(event) {
        if(event.keyCode == keycode_Enter)          
            $('[aria-labelledby$='+dialog.attr("id")+']').find(":button:first").click();        
    });
}

/*
//화면이동설정
function set_BaseEvent()
{
    //화면 최상단  ▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
    //ucloud pro 페이지 연결
    $("#btn_top_ucloud_go").unbind("click").bind("click", function(event) {
        window.location.href="http://pro.ucloud.com";
        return false;
    });
    //내정보관리 페이지 연결
    $("#btn_top_myManager").unbind("click").bind("click", function(event) {
        window.location.href="/portal/portal.myinfo.change.html";
        return false;
    });
    //FAQ 페이지 연결
    $("#btn_top_faq").unbind("click").bind("click", function(event) {
        window.location.href="/portal/portal.faq.html";
        return false;
    }); 
    //로그인 페이지 연결
    $("#btn_LoginPage").unbind("click").bind("click", function(event) {
        alert("로그인 페이지");
        return false;
    });
    //화면 최상단 ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲


    
    //LOGO로 메인 페이지 연결
    $("#btn_main_home").unbind("click").bind("click", function(event) { 
        if ($.cookie("memid") != null && $.cookie("memid") != "") {
            alert("로그인후 메인페이지");
        }
        else{
            alert("인덱스페이지");
        }       
        return false;
    });
    //클라우드 컴퓨팅소개
    $("#tab_cloudIntro").unbind("click").bind("click" , function(e){e.preventDefault;
        alert("클라우드 컴퓨팅소개");
        return false;
    });
    //상품안내
    $("#tab_PublicproductIntro").unbind("click").bind("click" , function(e){e.preventDefault;
        alert("상품안내");
        return false;
    });
    //서비스신청
    $("#tab_applyService").unbind("click").bind("click" , function(e){e.preventDefault;
        alert("서비스신청");
        return false;
    });
    //고객센터
    $("#tab_customerCenter_faq").unbind("click").bind("click" , function(e){e.preventDefault;
        alert("고객센터");
        return false;
    });
    //클라우드 콘솔
    $("#btn_CloudManager").unbind("click").bind("click" , function(e){e.preventDefault;
        alert("클라우드 콘솔");
        return false;
    });
    
    
    
    //화면좌측버튼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
    //FAQ
    $("#tab_faq").unbind("click").bind("click" , function(e){e.preventDefault;
        window.location.href="/portal/portal.faq.html";
        return false;
    });
    //공지사항
    $("#tab_notice").unbind("click").bind("click" , function(e){e.preventDefault;
        window.location.href="/portal/portal.notice.html";
        return false;
    });
    //문의하기
    $("#tab_qna").unbind("click").bind("click" , function(e){e.preventDefault;
        window.location.href="/portal/portal.qna.write.html";
        return false
    });
    //문의하기 그림으로 페이지 연결
    $("#qna_customer_btn").unbind("click").bind("click", function(event) {
        window.location.href="/portal/portal.qna.write.html";
        return false;
    });
    //화면좌측버튼▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲
        
}
*/

function showdlgError_Msg_new(sTitle, sText1, sText2, p_obj) {
    var strDlg          = "";
    var dlgForm         = $("#dialog_service_err");
    var p_width         = p_obj.width || 480;
    var p_zIndex        = p_obj.zIndex || 2000;
    var p_autoOpen      = p_obj.autoOpen || false;
    var p_modal         = p_obj.modal || true;
    var p_resizable     = p_obj.resizable || false;
    var p_closeFunc     = p_obj.closeFunc || "";

    if (typeof p_autoOpen != "boolean") 
        p_autoOpen  = false;
    if (typeof p_modal != "boolean") 
        p_modal = true;
    if (typeof p_resizable != "boolean") 
        p_resizable = false;
    
    activateDialog(dlgForm.dialog({ 
        open: function(event, ui) 
        {$(this).css("padding","0").parent().css("padding","0");},
        width : p_width,
        autoOpen : p_autoOpen,
        modal : p_modal,
        resizable : p_resizable,
        zIndex: p_zIndex,
        close : function(event, ui) {
            if (typeof p_closeFunc == "function") {
                p_closeFunc();
            }
        }
    }));
    
    dlgForm.find("#dlg_service_title").text(sTitle);
    dlgForm.find("#dlg_service_text1").text(sText1);
    dlgForm.find("#dlg_service_text2").text(sText2);

    
    dlgForm.find("#err_btnTopCancel").unbind("click").bind("click", function(event) {
        dlgForm.dialog("close"); 
        return false;
    });
    
    dlgForm.find("#btn_service_err_top_close").unbind("click").bind("click", function(event) {
        dlgForm.dialog("close"); 
        return false;
    });
    
    dlgForm.find("#err_btnCancel").unbind("click").bind("click", function(event) {
        dlgForm.dialog("close"); 
        return false;
    });

    dlgForm.dialog("open");
}


function showdlgError_Msg(sTitle, sText1, sText2, closeFunc) {
    var strDlg = "";
    var dlgForm = $("#dialog_service_err");
    
    activateDialog(dlgForm.dialog({ 
        open: function(event, ui) 
        {$(this).css("padding","0").parent().css("padding","0");},
        width:480,
        autoOpen: false,
        modal: true,
        resizable:false,
        zIndex: 2000,
        close : function(event, ui) {
            if (typeof closeFunc == "function") {
                closeFunc();
            }
        }
    }));
    
    dlgForm.find("#dlg_service_title").text(sTitle);
    dlgForm.find("#dlg_service_text1").html(sText1);
    dlgForm.find("#dlg_service_text2").html(sText2);

    
    dlgForm.find("#err_btnTopCancel").unbind("click").bind("click", function(event) {
        dlgForm.dialog("close"); 
        return false;
    });
    
    dlgForm.find("#btn_service_err_top_close").unbind("click").bind("click", function(event) {
        dlgForm.dialog("close"); 
        return false;
    });
    
    dlgForm.find("#err_btnCancel").unbind("click").bind("click", function(event) {
        dlgForm.dialog("close"); 
        return false;
    });

    dlgForm.dialog("open");

}


//다른페이지에서 넘어오는 get방식 파라미터를 받는다.
function get_param(pramNm) {        
    
    var paramStep = location.href;      //다른페이지에서 넘어온 URL
    
    //주소다음부분부터 받음
    paramStep = paramStep.substring(paramStep.lastIndexOf("?")+1, paramStep.length);
     
     if (paramStep.indexOf(pramNm+"=") == -1) {
     paramStep = false;
     }
     else {
     paramStep = paramStep.split(pramNm+"=")[1];
       if (paramStep.indexOf("&") != -1) {
       paramStep = paramStep.split("&")[0];
       }
       else if (paramStep.indexOf("%26") != -1) {
       paramStep = paramStep.split("%26")[0];
       }   
     }
    return paramStep;
}

//string안에 들어있는 각각의 이름으로 파라미터를 받는다.
//function strGet_param(strparam,pramNm) {      
//   if (strparam.indexOf(pramNm+"=") == -1) {
//       strparam = false;
//   }
//   else {
//       strparam = strparam.split(pramNm+"=")[1];
//     if (strparam.indexOf("&") != -1) {
//         strparam = strparam.split("&")[0];
//     }
//     else if (strparam.indexOf("%26") != -1) {
//         strparam = strparam.split("%26")[0];
//     }   
//   }
//  return strparam;
//}

function trim(val) {
    if(val == null)
        return null;
    return val.replace(/^\s*/, "").replace(/\s*$/, "");
}

//오늘날짜설정 가져오기
function getToday(){
    var now = "";
    var giYear = "";
    var giMonth = "";
    var giDay = "";

    $.ajax({
        url: "/bizMecaInfo",
        type: "GET",
        data: "command=serverTime&dtFormat=yyyyMMdd",
        dataType: "json",
        async : false,
        success: function(json) {   
            now = json.time;
            giYear = now.substring(0,4);
            giMonth = now.substring(4,6);
            giDay = now.substring(6,8);

        },          
        error: function(XMLHttpResponse) {  
            showdlgError_Msg("통신에러","", "서버와의 연결에 실패했습니다.");
            return;
            //에러처리
        }                           
    });
    return giYear + "년 " + giMonth + "월 " + giDay + "일 ";       

/*
//  현재 날짜 객체를 얻어옴.
    var gdCurDate = new Date();

//  현재 날짜에 날짜 게산.
    gdCurDate.setYear( gdCurDate.getFullYear());
    gdCurDate.setMonth( gdCurDate.getMonth());
    gdCurDate.setDate( gdCurDate.getDate());

    //실제 사용할 연, 월, 일 변수 받기.
    var giYear = gdCurDate.getFullYear();
    var giMonth = gdCurDate.getMonth()+1;
    var giDay = gdCurDate.getDate();
    //월, 일의 자릿수를 2자리로 맞춘다.
    giMonth = "0" + giMonth;
    giMonth = giMonth.substring(giMonth.length-2,giMonth.length);
    giDay   = "0" + giDay;
    giDay   = giDay.substring(giDay.length-2,giDay.length);
    //display 형태 맞추기.
    return giYear + "년 " + giMonth + "월 " + giDay + "일 ";
*/
}

//Validation E-Mail functions - EPC 로그인 이메일형식체크 2010.11.02
function validateEmail(label, field, errMsgField, isOptional) {  
    var isValid = true;
    var errMsg = "";
    var value = field.val();     
    
    var regExp= /[0-9a-zA-Z][_0-9a-zA-Z]*@[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+){1,2}$/;
//    var regExp= /[0-9a-zA-Z][_0-9a-zA-Z]*@[_0-9a-zA-Z]+(\.[_0-9a-zA-Z-]+){1,2}$/;
    if(!value.match(regExp)){
        errMsg = "옳지 않은 이메일 형식입니다.";
        isValid = false;
    }
    
    showError(isValid, field, errMsgField, errMsg); 
    return isValid;
}

function showError(isValid, field, errMsgField, errMsg) {    
    if(isValid) {
        errMsgField.text("").hide();
        field.addClass("text").removeClass("error_text");
    }
    else {
        errMsgField.text(errMsg).show();
        field.removeClass("text").addClass("error_text");   
    }
}
//4 자리를 0으로 채움
function zeroInsert(str){
     var newStr = '';
     if(str.length < 4){
      for(var i=0;i<4-str.length;i++){
       newStr = newStr + '0';
      }
      newStr = newStr + str;
     }
     else{
      newStr = str;
     }
     return newStr;
}

//popup
//팝업 Active Dialog
function setApplyServiceStep2Dialog(gubun){
    //removeDialogs();
    var dlg = $("#popupZipcodeSearch");
    var key = gubun;
    var dongList;
    var addressFull = "";   //주소 전체 //[우편번호]주소
    
    //우편번호검색
    activateDialog(dlg.dialog({ 
        open: function(event, ui) {$(this).css("padding","0").parent().css("padding","0");},
        width:480,
        autoOpen: false,
        modal: true,
        resizable:false,
        zIndex: 2000
    }));
    
    
    dlg.dialog("open");
    
    $("#photo0").val("");
    $("#address").val("");
    $("#address_bunji").val("");
    $("#address_detail").val("");
//  $("#popupZipcodeSearch").find("#zipResult").empty();
    
    $("#popupZipcodeSearch #btnZipcodeSearchClose").unbind("click").bind("click", function(event) {
        $("#popupZipcodeSearch").dialog("close");
        return false;
    });
    $("#zipResult").unbind("change").bind("change", function (event) {
        var sel_val     = $("#zipResult").val();
        var len         = dongList.length;
        var result_val;
        
        for (var i = 0; i < len; i++) {
            if (sel_val == dongList[i].DONG_CD)
            {
                result_val  = dongList[i];
                break;
            }
        }
        
//      if (result_val.DONG_ADDR == null)
//          result_val.DONG_ADDR    = "";
            $("#address").val( result_val.DONG_ADDR || "" );
            $("#address_dong").val( result_val.DONG_CD || "" );
            addressFull = result_val.DONG_ADDR;
            
    });
    
    $("#popupZipcodeSearch #btnZipcodeSearch").unbind("click").bind("click", function(event) {
        
        var sDong = trim($("#photo0").val());
        
        if (sDong == null || sDong == "") {
            showdlgError_Msg("주소입력", "주소 ('동' 이름)을 입력하여 주십시오.", "");
            return false;           
        }
        
        var params = { command : "getZipAddrsForISV" , dongName : encodeURIComponent(sDong)};
        $.ajax({
            url: WebROOT + "bizMecaInfo",
            type: "POST",
            data: params,
            dataType: "json",
            success: function(data) {
                
                if(data.response.result == 0){
                    
                    var zipResult   = document.getElementById("zipResult");
                    zipResult.options.length    = 1;
                    dongList        = data.response.data;
                    
                    if (!dongList.length > 0 ) {            
                        showdlgError_Msg("우편번호", "", "해당지역의 우편번호가 존재하지 않습니다.");
                        return ;
                    }
                    // EPC 2010.11.02 시작 
                    for (var i = 0; i < dongList.length; i++) {             
                        
                        try {
                            zipResult.add(new Option(dongList[i].DONG_ADDR, dongList[i].DONG_CD ), null);
                        } catch (e) {
                            zipResult.add(new Option(dongList[i].DONG_ADDR, dongList[i].DONG_CD ));
                        }

//                      $("#zipResult").after(newRow);
//                      tab_list = "";
                        /*
                        var Content = $("#popupZipcodeSearch").find("#zipResult").clone(true);
                        Content.attr("id", "zipResult_"+data.response.data[i].ZIP);
                        Content.attr("dongCd",data.response.data[i].DONG_CD);
                        Content.data("address", data.response.data[i].DONG_ADDR);
                        
                        Content.find("#step2_addr_result").text(data.response.data[i].DONG_ADDR);
//                      Content.find("#step2_zipcode_result").text(data.response.data[i].ZIP);
                        $("#popupZipcodeSearch").find("#zipResult").appendTo(Content.show());
                        */
                    }
                    $("#zipResult").focus();
                    
                    return true;
                }
                else if(data.response.result == 1){
                    $("#photo0").val("");
                    $("#address").val("");
                    $("#address_bunji").val("");
                    $("#address_detail").val("");
                    showdlgError_Msg("필수 요소 부족", "주소를", "모두 써주세요");
                    return false;
                }
                else if(data.response.result == 90){
                    $("#photo0").val("");
                    $("#address").val("");
                    $("#address_bunji").val("");
                    $("#address_detail").val("");
                    showdlgError_Msg("", "요청하신 주소와  ", "일치하는 결과가 없습니다.");
                    return false;
                }
                else{
                    showdlgError_Msg("오류","오류","");
                }

            },
            error: function(xhr, desc, error) {
                //showdlgError_Msg("로그인 오류", "서버로 부터 오류 메세지가 도착했습니다.", "아이디 혹은 비밀번호가 틀립니다. 다시 확인하시고 이용해주십시요.");
            }
        });
        return false;
    });
    
    $("#popupZipcodeSearch #btnZipcodeSearchOk").unbind("click").bind("click", function(event) {
//      var target = $(event.target).parent().parent();
//      var targetId = target.attr("id");
//      targetId = targetId.substring(10, targetId.length);
//      var zipCode1 = targetId.substring(0,3);
//      var zipCode2 = targetId.substring(3,6);
//      var addIndex = target.data("address").indexOf("]");     //번지와 주소를 분리하기 위한 인덱스값
//      var sAddress = target.data("address").substring(addIndex);
//      
//      TEMPDONG_CD  = target.attr("dongCd");
        
//      var addrIndex = $("#address_bunji").val().indexOf("-");
//      var address01 = $("#address_bunji").val().substring(0,addIndex);
//      var address02 = $("#address_bunji").val().substring(addIndex);
        

        var bunji = "";
        var ho = "";
        
        var addrBunji = $("#address_bunji").val();
        if(addrBunji.indexOf("-")== -1){
            bunji = addrBunji;
        }
        else{
            var temp = addrBunji.split("-");
            var len = temp.length;
            
             if (len > 2)  {
                showInstancesdlgError_Msg("번지", "", "형식에 맞지 않습니다.");
              return false;
             }
             else {
              bunji = temp[0];
              ho  = temp[1];
             }
        }
        
        if(bunji.length > 4 || ho.length > 4 ){
            showInstancesdlgError_Msg("번지", "번지를 확인해 주세요", "형식에 맞지 않습니다.");
              return false;
        }
        
        if( addressFull=="" || bunji==""
            || $("#address_detail").val()=="" ){
            
            showInstancesdlgError_Msg("필수 요소 부족", " ", "칸을 정확히 채워주세요.");
            return false;
        }
        
        
        if(isNaN(bunji)){       //번지에 숫자만 입력 가능하게 막아놓음
            showInstancesdlgError_Msg("번지", "번지에는", "숫자만 입력이 가능합니다.");
            return false;
        }
        if(isNaN(ho)){      //번지에 숫자만 입력 가능하게 막아놓음
            showInstancesdlgError_Msg("번지", "번지에는", "숫자만 입력이 가능합니다.");
            return false;
        }
    
        var addr_idx        = addressFull.indexOf("]");
        var zip_idx         = addressFull.indexOf("-");
        var addr            = addressFull.substring(addr_idx+1);
        var addr_zip1       = addressFull.substring(1,zip_idx);
        var addr_zip2       = addressFull.substring(zip_idx+1,addr_idx);
        
        //가입정보 우편번호
        if( key == "joinInfo" ){
            $("#step2_zip01").val(addr_zip1);   //우편번호1
            $("#step2_zip02").val(addr_zip2);   //우편번호2
            $("#step2_addr1").val(addr);    //주소 
            $("#step2_addr2").val(addrBunji);   //번지
            $("#addrHo").val(ho);   //호
            $("#step2_addr3").val($("#address_detail").val());  //상세주소
            $("#step2_dongCD").val($("#address_dong").val());   //동코드
            
        }
        //납부정보 우편번호
        else if( key == "payInfo" ){
            $("#payerZip01").val(addr_zip1);
            $("#payerZip02").val(addr_zip2);
            $("#payerFixAddr").val(addr);
            $("#payerAddrBunji").val(addrBunji);    //번지
            $("#payerAddrho").val(ho);  //호
            $("#payerDtlAddr").val($("#address_detail").val()); //상세주소
            $("#payer_dongCD").val($("#address_dong").val());   //동코드
            
        }//수정
        else if( key == "updateInfo" ){
            $("#Zip01").val(addr_zip1);
            $("#Zip02").val(addr_zip2);
            $("#payerAddr").val(addr);
            $("#payerAddrNo").val(addrBunji);   //번지
            $("#payerAddrHo").val(ho);  //호
            $("#payerAddrRef").val($("#address_detail").val()); //상세주소
            $("#payerDongCD").val($("#address_dong").val());    //동코드
        }       
        
        $("#popupZipcodeSearch").dialog("close");
        return false;
    });
}

function showInstancesdlgError_Msg(sTitle, sText1, sText2, closeFunc) {
    var strDlg = "";
    var dlgForm = $("#dialog_Instancesservice_Msg");
    
    dlgForm.dialog({ 
        close : function(event, ui) {
            if (typeof closeFunc == "function") {
                closeFunc();
            }
        }
    });
    
    
    dlgForm.dialog("open");
    
    dlgForm.find("#dlg_Instancesservice_Msg_title").text(sTitle);
    dlgForm.find("#dlg_Instancesservice_text1").html(sText1);
    dlgForm.find("#dlg_Instancesservice_text2").html(sText2);

    
    dlgForm.find("#btn_Instancesservice_Msg_top_close").unbind("click").bind("click", function(e) {
        e.preventDefault();
        dlgForm.dialog("close"); 
        return false;
    });
            
    dlgForm.find("#btn_Instances_MsgOK").unbind("click").bind("click", function(e) {
        e.preventDefault();
        dlgForm.dialog("close"); 
        return false;
    });
    
}

function validateString(label, field, errMsgField, isOptional) {  
    var isValid = true;
    var errMsg = "";
    var value = field.val();     
    if (isOptional!=true && (value == null || value.length == 0)) {  //required field   
        errMsg = label + " 을(를) 입력하셔야 합니다. ";      
        isValid = false;        
    }   
    else if (value!=null && value.length >= 255) {      
        errMsg = label + " 은(는) 최대 255자 이상 입력하실수 없습니다.";       
        isValid = false;        
    }   
    else if(isOptional!=true ) {
        if((value != null || value.length > 0))
        {
            if(value.indexOf('"')!=-1)
            {
                errMsg = "쌍따옴표는 사용할수 없습니다.";
                isValid = false;
            }
        }
    }
    showError(isValid, field, errMsgField, errMsg); 
    return isValid;
}

// 로그아웃
function logoutldap() {

    $.ajax({
        url: WebROOT + "plogout",
        type: "POST",
        async: false,
        dataType: "json",
        success: function(data) {
            if(data.response.result == 0){
                //EPC COOKIES 2010.11.02 시작
                $.cookie('svcid', null, {path : WebROOT});
                $.cookie('memsq', null, {path : WebROOT});
                $.cookie('memid', null, {path : WebROOT});
                $.cookie('memnm', null, {path : WebROOT});
                $.cookie('memauthcd', null, {path : WebROOT});
                $.cookie('rcntloindttm', null, {path : WebROOT});
                $.cookie('emailauthyn', null, {path : WebROOT});
                $.cookie('activeyn', null, {path : WebROOT});
                $.cookie('cscreateyn', null, {path : WebROOT});
                //EPC COOKIES 2010.11.02 끝
                
                $.cookie('cprole', null, {path : WebROOT});
                $.cookie('cloudpid', null, {path : WebROOT});
                $.cookie('ktid', null, {path : WebROOT});
                $.cookie('companyid', null, {path : WebROOT});
                $.cookie('cloudname', null, {path : WebROOT});

                $.cookie('ktusername', null, {path : WebROOT});
                $.cookie('kthname', null, {path : WebROOT});
                $.cookie('cloudpid', null, {path : WebROOT});
                $.cookie('cprole', null, {path : WebROOT});
                $.cookie('service_name', null, {path : WebROOT});
                $.cookie('cs_account', null, {path : WebROOT});
                $.cookie('cs_pwd', null, {path : WebROOT});

                $.cookie('svcid_S1820', null, {path : WebROOT});
                $.cookie('svcid_S1821', null, {path : WebROOT});
                $.cookie('svcid_S1822', null, {path : WebROOT});
                $.cookie('svcid_S1823', null, {path : WebROOT});
                $.cookie('svcid_S1824', null, {path : WebROOT});
                
                g_mem_id = null;
                g_mem_nm = null;
                g_mem_auth_cd = null;       //권한 코드
                g_rcnt_login_dttm = null;   //최종 로그인일시
                g_email_auth_yn = null;     //가입용 이메일 인증 여부
                g_active_yn = null;         //활성여부
                g_cs_create_yn = null;      //Cloud Stack ID 생성완료 여부(서비스 신청 여부)
                
                logoutcloud();
                
                document.location.href  = WebROOT + "index.html";
                return true;
            }
            else {
                showdlgError_Msg("로그아웃 오류", "세션 삭제중 오류가 발생했습니다.", "잠시후 다시 시도해 주세요.");
            }
        },
        error: function(xhr, desc, error) {
            showdlgError_Msg("로그인 오류", "로그아웃 호출시 오류가 발생했습니다.", "잠시후 다시 시도해 주세요.");
        }
    });



}

// 로그아웃
function logoutcloud() {
    
    g_mySession = null;
    g_username = null;  
    g_account = null;
    g_domainid = null;  
    g_timezoneoffset = null;
    g_timezone = null;
    
    $.cookie('JSESSIONID', null, {path : WebROOT});
    $.cookie('username', null, {path : WebROOT});
    $.cookie('account', null, {path : WebROOT});
    $.cookie('domainid', null, {path : WebROOT});
    $.cookie('role', null, {path : WebROOT});
    $.cookie('networktype', null, {path : WebROOT}); 
    $.cookie('timezoneoffset', null, {path : WebROOT});
    $.cookie('timezone', null, {path : WebROOT});
}

// 클라우드 콘솔
function loginCloudStock(str1, str2) {
    var array1 = [];
    var username = encodeURIComponent(str1);
    array1.push("&username="+username);

    /*var password = $.md5(encodeURIComponent(str2));
    array1.push("&password="+password);*/
    str2 = "EPC_USER";
    //str2 = "ROOT";

    if (str2 == "ROOT") array1.push("&domain="+encodeURIComponent("/"));
    else  array1.push("&domain="+encodeURIComponent(str2));
    $.ajax({
        url: "/gwapi",
        type: "GET",
        data: "command=login"+array1.join("")+"&response=json",
        dataType: "json",
        async : false,
        success: function(json) {   
        
            var mySession      = $.cookie('JSESSIONID');
            var role           = json.loginresponse.type;
            var username       = json.loginresponse.username;
            var account        = json.loginresponse.account;
            var domainid       = json.loginresponse.domainid;
            var timezone       = json.loginresponse.timezone;
            var timezoneoffset = json.loginresponse.timezoneoffset;
            
            var networkType= null;
            var hypervisorType= null;
            var directattachnetworkgroupsenabled= null;
            var directAttachedUntaggedEnabled= null;
            
            if (json.loginresponse.networktype != null)
                networkType = json.loginresponse.networktype;
    
            if (json.loginresponse.hypervisortype != null)
                hypervisorType = json.loginresponse.hypervisortype;
    
            if (json.loginresponse.directattachnetworkgroupsenabled != null)
                directattachnetworkgroupsenabled = json.loginresponse.directattachnetworkgroupsenabled;
    
            if (json.loginresponse.directattacheduntaggedenabled != null)
                directAttachedUntaggedEnabled = json.loginresponse.directattacheduntaggedenabled;
    
            $.cookie('networktype', networkType,{ expires: 0, path : WebROOT});
            $.cookie('hypervisortype', hypervisorType,{ expires: 0, path : WebROOT});
            $.cookie('username', username,{ expires: 0, path : WebROOT});   
            $.cookie('account', account,{ expires: 0, path : WebROOT}); 
            $.cookie('domainid', domainid,{ expires: 0, path : WebROOT});
            $.cookie('role', role,{ expires: 0, path : WebROOT});
            $.cookie('timezoneoffset', timezoneoffset,{ expires: 0, path : WebROOT});  
            $.cookie('timezone', timezone,{ expires: 0, path : WebROOT});  
            $.cookie('directattachnetworkgroupsenabled', directattachnetworkgroupsenabled,{ expires: 0, path : WebROOT}); 
            $.cookie('directattacheduntaggedenabled', directAttachedUntaggedEnabled,{ expires: 0, path : WebROOT});
            
            document.location.href="/console/console.iaas.home.html";
            
        },          
        error: function(XMLHttpResponse) {  
            showdlgError_Msg("클라우드cs 인증 실패","클라우드cs 인증중에 에러가 발생하였습니다.", "잠시 후 다시 시도해 주시기바랍니다.");
            return;
            //에러처리
        }                           
    });
    
}


function calculate_byte( sTargetStr ) {
    var sTmpStr, sTmpChar;
    var nOriginLen = 0;
    var nStrLength = 0;

    sTmpStr = new String(sTargetStr);
    nOriginLen = sTmpStr.length;

    for ( var i=0 ; i < nOriginLen ; i++ ) {
        sTmpChar = sTmpStr.charAt(i);

        if (escape(sTmpChar).length > 4) {
            nStrLength += 2;
        }else if (sTmpChar!='\r') {
            nStrLength ++;
        }
    }
    return nStrLength;
}

function Cut_Str( sTargetStr , nMaxLen ) {
    var sTmpStr, sTmpChar, sDestStr;
    var nOriginLen = 0;
    var nStrLength = 0;
    var sDestStr = "";
    sTmpStr = new String(sTargetStr);
    nOriginLen = sTmpStr.length;

    for ( var i=0 ; i < nOriginLen ; i++ ) {
        sTmpChar = sTmpStr.charAt(i);

        if (escape(sTmpChar).length > 4) {
            nStrLength = nStrLength + 2;
        } else if (sTmpChar!='\r') {
            nStrLength ++;
        }

        if (nStrLength <= nMaxLen) {
            sDestStr = sDestStr + sTmpChar;
        } else {
            break;
        }
    }
    return sDestStr;
}


function shownotice_Msg() {
    var strDlg = "";
    var dlgForm = $(".popupNotice");
    activateDialog($(".popupNotice").dialog({ 
        open: function(event, ui) 
        {$(this).css("padding","0").parent().css("padding","0");},
        width:693,
        autoOpen: false,
        modal: true,
        resizable:false,
        zIndex: 2000
    }));
    
    dlgForm.dialog("open");
    
    dlgForm.find("#btnnoticeOK").unbind("click").bind("click", function(event) {
        dlgForm.dialog("close"); 
        return false;
    });
}