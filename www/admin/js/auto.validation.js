
function checkLastChar(vchar){
	b=vchar.charCodeAt(0);

	hanTable=new Array();
	hanTable[0]='ㄱㄲㄴㄷㄸㄹㅁㅂㅃㅅㅆㅇㅈㅉㅊㅋㅌㅍㅎ'; // 19 초성
	hanTable[1]='ㅏㅐㅑㅒㅓㅔㅕㅖㅗㅘㅙㅚㅛㅜㅝㅞㅟㅠㅡㅢㅣ'; //21 중성
	hanTable[2]=' ㄱㄲㄳㄴㄵㄶㄷㄹㄺㄻㄼㄽㄾㄿㅀㅁㅂㅄㅅㅆㅇㅈㅊㅋㅌㅍㅎ'; //28 종성

	hcode=b-0xAC00;


	//hanTable='ㄱㄲㄳㄴㄵㄶㄷㄸㄹㄺㄻㄼㄽㄾㄿㅀㅁㅂㅃㅄㅅㅆㅇㅈㅉㅊㅋㅌㅍㅎㅏㅐㅑㅒㅓㅔㅕㅖㅗㅘㅙㅚㅛㅜㅝㅞㅟㅠㅡㅢㅣ ';

	cho=new Array();
	cho[0]=parseInt(hcode / 588); //초성
	hcode2=hcode % 588;

	cho[1]=parseInt(hcode2 / 28); //중성
	cho[2]=hcode2 % 28; //종성 ㄱ,,,ㄴ 

	m=new Array();

	//보고픔님과 더마린님의 조언&게시물 참고

	//초성 
	m[0]=Math.floor((b-0xAC00)/(21*28)); 
	//중성
	m[1]=Math.floor(((b-0xAC00)%(21*28))/28); 
	//종성
	m[2]=(b-0xAC00)%28;

	if(m[2]){
		return "을";
	}else{
		return "를";
	}
}


function CheckForm(_element){
		var len = _element.title.length;
		var PT_idtype =/^[a-zA-Z]{1}[a-zA-Z0-9_]+$/;　// 첫글자 영문, 영,숫,_문 사용가
		var PT_pwtype =/^[a-zA-Z0-9~!@\#$%^&*\()\-=+_.'\"]+$/;　// 첫글자 영문, 영,숫,_문 사용가[]/
		var PT_number =/^[0-9]+$/;　　　　　　　　　　　// 숫자만 사용가
		var PT_com_number =/^[0-9-]+$/;　　　　　　　　　　　// 숫자,-만 사용가
		var PT_alpabet =/^[a-zA-Z]+$/;　　　　　　　　   // 영문만 사용가
		var PT_URL =/^[a-zA-Z0-9.\\/_-]+$/;　　　　　　　　   // 영문만 사용가
		var PT_dbtable =/^[a-zA-Z0-9._]+$/;　　　　　　　　   // 테이블 스타일 사용가
		var PT_korean =/^[가-]+$/;　　　　　　　　　　// 한글만 사용가
//		var PT_char =/^[가-a-zA-Z]+$/;　　　　　　 // 한글,영문만 사용가
//		var PT_K_E_N =/^[가-a-zA-Z0-9]+$/;　　　　  // 한글,영문,숫자만 사용가
//		var PT_K_N =/^[가-0-9]+$/;　　　　　　　　// 한글,숫자만 사용가
		var PT_K =/[가-]/;　　　　　　　　　　　// 한글포함
		//var PT_email = /[a-z0-9]{2,}@[a-z0-9-]{2,}\.[a-z0-9]{2,}/i;  // 이메일
		var PT_email = /^([./0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)(\.[0-9a-zA-Z_-]+){1,2}$/;	//메일에 . 특수부호가 있는데 허용이 안되서 ./ 추가 2013-10-10 이학봉
		var PT_regno = /\d{6}(\-|)[1-4]\d{6}$/;
		var PT_file_image = /^[gif|jpg|png|GIF|JPG|PNG]+$/;
		var PT_defance_file_type = /^[.php|.inc|.html|.htm|.phtml|.php3|.js|.PHP|.INC|.HTML|.HTM|.PHTML|.PHP3|.JS]+$/;
		var PT_file_zip = /^[zip|ZIP]+$/;
		var PT_th_won = /^[0]+$/;

		//alert(_element.title);
		if(eval(_element.getAttribute("validation")) || _element.value.length > 0){
			if($(_element).attr("idtype")){
				if (!PT_idtype.test(_element.value)){
					if(language == "english"){
						alert(" Please Enter English Characters Or Numbers For The First Letter of '"+_element.title+ "'  ");
					}else{
						alert("'"+_element.title+ "' 첫글자는 영문, 영문과 숫자만 입력하실수 있습니다");
					}
					
					_element.focus();
					return false;
				}
			}
			
			if($(_element).attr("pwtype")){
				if (!PT_pwtype.test(_element.value)){
					if(language == "english"){
						alert(" Please enter '"+_element.title+ "' olny in English or numbers ");
					}else{
						alert("'"+_element.title+ "'영문, 영문과 숫자만 입력하실수 있습니다");
					}
					_element.focus();
					return false;
				}
			}
			

			if($(_element).attr("sizecheck") == "equal"){
				//alert(_element.value.length + ":::"+ _element.lengthlimit);
				if (_element.value.length != _element.lengthlimit){
					if(language == "english"){
						alert(" Entered '"+_element.title+ "' incorrectly, Please check and try again! ");
					}else{
						alert("'"+_element.title+ "' 는 길이가 잘못입력되었습니다. 확인후 다시 시도해주세요");
					}
					_element.focus();
					return false;
				}
			}

			if($(_element).attr("numeric")){
				if (!PT_number.test(_element.value)){
					if(language == "english"){
						alert(" Please enter '"+_element.title+ "' in numbers. ");
					}else{
						alert("'"+_element.title+ "' 는 숫자형식으로 입력해주세요");
					}
					_element.focus();
					return false;
				}
			}

			if($(_element).attr("com_numeric")){
				if (!PT_com_number.test(_element.value)){
					if(language == "english"){
						alert(" Please enter '"+_element.title+ "' in numbers and `-`. ");
					}else if(language == "korea"){
						alert("'"+_element.title+ "' 는 숫자와 `-` 만 입력하실수 있습니다. 확인후 다시  입력해주세요");
					}else{
						alert("'"+_element.title+ "' yang dimasukkan hanya angka dan `-`. Silakan coba lagi setelah memeriksa.");
					}
					
					_element.focus();
					return false;
				}
			}
			
			if($(_element).attr("texttype")){
				if (!PT_K_E_N.test(_element.value)){
					if(language == "english"){
						alert(" Please enter '"+_element.title+ "' in numbers, English or Korean. ");
					}else{
						alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  한글,영문,숫자만 입력해주세요 ");
					}
					_element.focus();
					return false;
				}
			}
			
			if($(_element).attr("korean")){
				if (!PT_korean.test(_element.value)){
					if(language == "english"){
						alert("  Please enter '"+_element.title+ "'  only in Korean. ");
					}else{
						alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  한글만 입력해주세요 ");
					}
					_element.focus();
					return false;
				}
			}
		
			if($(_element).attr("urltype")){
				if (!PT_URL.test(_element.value)){
					if(language == "english"){
						alert("  Please enter '"+_element.title+ "' as URL format. Available only '-', '_', '/' for special character. ");
					}else{
						alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  URL 형식으로 입력해주세요. 특수문자는 '-', '_', '/' 만 가능합니다 ");
					}
					_element.focus();
					return false;
				}
			}
			
			if($(_element).attr("dbtable")){
				if (!PT_dbtable.test(_element.value)){
					if(language == "english"){
						alert("Please enter '"+_element.title+ "' as DB table format. Available Only  '-', '_', '/'for special character. Blanks cannot be used. ");
					}else{
						alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  DB TABLE 형식으로 입력해주세요. 특수문자는 영문,숫자, '_' 만 가능합니다. 공백은 사용 하실수 없습니다. ");
					}
					_element.focus();
					return false;
				}
			}
			
			
			if($(_element).attr("ssnum")){
				//alert(_element.form.ssnum1.value+":::"+_element.form.ssnum2.value);
				if(!ChkJumin(_element.form.jumin1.value, _element.form.jumin2.value)){
					if(language == "english"){
						alert("'Social security number' is incorrect. Please try again. ");
					}else{
						alert('주민등록번호가 정확하지 않습니다. 확인후 다시 시도해주세요. ');
					}
					_element.focus();
					return false;				
				}
			}

			if($(_element).attr("compare")){
				compare_A = document.getElementById("compare_a");
				compare_B = document.getElementById("compare_b");
				if(compare_A.value != compare_B.value){
					if(language == "english"){
						alert("The ["+compare_A.title+"] and ["+compare_B.title+"] you typed do not match. ");
					}else{
						alert('['+compare_A.title+']와 ['+compare_B.title+']이 일치하지 않습니다. ');
					}
					_element.focus();
					return false;				
				}
			}

			if($(_element).attr("email")){
				if (!PT_email.test(_element.value)){
					if(language == "english"){
						alert("'It is not e-mail format. Please check and try again! ");
					}else{
						alert('이메일 형식이 아닙니다. 확인후 다시 시도해주세요');
					}
					_element.focus();
					return false;
				}
			}

			if($(_element).attr("duplicate")){
				//alert($(_element).attr("dup_check"));
				if (!eval($(_element).attr("dup_check"))){
					if(language == "english"){
						alert("Please check duplicate '"+_element.title+ "'  first. ");
					}else{
						alert(_element.title+" 중복확인 을 먼저 해주세요");
					}
					_element.focus();
					return false;
				}
			}

			if($(_element).attr("size_min") != "" && $(_element).attr("size_max") != ""){
				if (($(_element).attr("size_min") > _element.value.length) || ($(_element).attr("size_max") < _element.value.length)){
					if(language == "english"){
						alert("'"+_element.title+ "' is available not less than "+$(_element).attr("size_max")+" and not more than "+$(_element).attr("size_min")+" words. ");
					}else{
						alert(_element.title + "는 "+$(_element).attr("size_min")+"자이상 "+$(_element).attr("size_max")+"자 이하만 가능 합니다.");
					}
					_element.focus();
					return false;
				}
			}else if($(_element).attr("size_min") != ""){
				if ($(_element).attr("size_min") > _element.value.length){
					if(language == "english"){
						alert(""+_element.title + " is available at least  "+$(_element).attr("size_min")+" words ");
					}else{
						alert("최소 "+_element.title + "는 "+$(_element).attr("size_min")+"자이상 가능 합니다.");
					}
					_element.focus();
					return false;
				}
			}else if($(_element).attr("size_max") != ""){
				if ($(_element).attr("size_max") < _element.value.length){
					if(language == "english"){
						alert(""+_element.title + " is available under "+$(_element).attr("size_max")+" words ");
					}else{
						alert("최대 "+_element.title + "는 "+$(_element).attr("size_max")+"자이하 가능 합니다.");
					}
					_element.focus();
					return false;
				}
			}
		}
	
			switch (_element.type){
				case "text":
				case "password":
				case "textarea":
					//alert(_element.name+" :::"+_element.validation);
					if(eval(_element.getAttribute("validation"))){
						if(_element.value.length < 1){
							if(language == "english"){
								alert(" Please enter a '"+_element.title+ "'  ");
							}else{
								//ViewAlertBox("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 입력해주세요");
								alert("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 입력해주세요");

							}
							_element.focus();
							return false;
						}
					}
					return true;
					break;
				case "select-one":
					//alert(_element[_element.selectedIndex].value+":::");
					if(eval(_element.getAttribute("validation"))){
						if(_element.selectedIndex==0){
							if(language == "english"){
								alert("Please select a '"+_element.title+ "' ");
							}else{
								alert("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 선택해주세요");
							}
							_element.focus();
							return false;
						}
					}					
					return true;
					break;
				//20131016 Hong 추가
				case "select-multiple":
					if(eval(_element.getAttribute("validation"))){
						if($(_element).val()==null || $(_element).val()==''){
							if(language == "english"){
								alert("Please select a '"+_element.title+ "' ");
							}else{
								alert("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 선택해주세요");
							}
							_element.focus();
							return false;
						}
					}					
					return true;
					break;
				case "checkbox": 
				case "radio":
					//alert(_element.name+" :::"+_element.validation);
					if(eval(_element.getAttribute("validation"))){
						if($(_element).attr("multi_check")){
							var cobj = $(".multi-check");
						}else {
                            //var cobj = eval("document.forms['"+_element.form.name+"']."+_element.name);
                            var cobj = $("input[name=" + _element.name + "]");
                        }

						if(cobj.length < 1){
							if(cobj[0].attr('checked','checked')){
								return true;
								break;
							}
						}else{
							var checked_bool = false;
							cobj.each(function(){
								//alert($(this).val());
								//alert($(this).val()+"::"+$(this).attr('checked'));
								if($(this).attr('checked') == "checked"){
									checked_bool = true;
									return true;
								}
							});

							if(checked_bool){
								return checked_bool;
							}
							/*
							 for(j=0;j < cobj.length;j++){
								 if(cobj[j].attr('checked') == "checked"){
									 return true;
									 break;
								 }
							 }
							 */
						}

						if(language == "english"){
							alert("Please select a '"+_element.title+ "' ");
						}else{
							alert("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 선택해주세요");
						}
						_element.focus();
						return false;
						break;
					}else{
						return true;
						break;
					}
				case "file":
					if(eval(_element.getAttribute("validation"))){
						if(_element.value.length < 1){
							if(language == "english"){
								alert("Please select a '"+_element.title+ "' ");
							}else{
								alert("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 선택해주세요");
							}

							_element.focus();
							return false;
						}

						if(_element.value.length > 0){
							var defancefiletype  = _element.value.substring(_element.value.length-4);
							//alert(defancefiletype);
							if (PT_defance_file_type.test(defancefiletype)){
								alert('등록가능한 파일타입이 아닙니다. 확인후 다시 시도해주세요');
								_element.focus();
								return false;
							}
						}
						
						if($(_element).attr("filetype") == "image"){
							var filetype  = _element.value.substring(_element.value.length-3); 	 
				
							if (!PT_file_image.test(filetype)){
								alert('등록가능한 파일타입이 아닙니다. 확인후 다시 시도해주세요');
								_element.focus();
								return false;
							}
				
						}
				
				
						if($(_element).attr("filetype") == "zip"){
							var filetype  = _element.value.substring(_element.value.length-3); 	 
				
							if (!PT_file_zip.test(filetype)){
								alert('등록가능한 파일타입이 아닙니다. 확인후 다시 시도해주세요');
								_element.focus();
								return false;
							}
				
						}
					}
					return true;
					break;
				default:
					return true;
					break;
					//alert('여기오나');
					//return false;
			}
		
}

function CheckFormValue(frm){		
	//if(confirm('저장하시겠습니까?')){
		for(i=0;i < frm.elements.length;i++){
			if(!CheckForm(frm.elements[i])){
				return false;
			}
		}

		$('#submit_btn').css('display','none');
		$.blockUI.defaults.css = {};
		$.blockUI({ message: $('#loading'), css: {left:'40%', top:'40%',  width: '10px' , height: '10px' ,padding:  '10px'} });
		setTimeout($.unblockUI, 2000);
		return true;
	//}else{
	//	return false;
	//}
}


function ChkJumin(str_jumin1,str_jumin2) {  
    errfound = false;  
 //   var str_jumin1; 
 //   var str_jumin2; 
    var checkImg='';  
    

    var i3=0  
    for (var i=0;i<str_jumin1.length;i++) {  
        var ch1 = str_jumin1.substring(i,i+1);  
        if (ch1<'0' || ch1>'9') { i3=i3+1 }  
    }  
    if ((str_jumin1 == '') || ( i3 != 0 )) {  
        return false; 
    }  

    var i4=0  
    for (var i=0;i<str_jumin2.length;i++) {  
        var ch1 = str_jumin2.substring(i,i+1);  
        if (ch1<'0' || ch1>'9') { i4=i4+1 }  
    }  
    if ((str_jumin2 == '') || ( i4 != 0 )) { 
      return false; 
    }  

    if(str_jumin1.substring(0,1) < 2) {  
        return false; 
    }  

    if(str_jumin2.substring(0,1) > 2) {  
        return false; 
    }  

    if((str_jumin1.length > 7) || (str_jumin2.length > 8)) {  
        return false; 
    }  

    if ((str_jumin1 == '72') || ( str_jumin2 == '18')) {  
      return false; 
    }  
      
    var f1=str_jumin1.substring(0,1)  
    var f2=str_jumin1.substring(1,2)  
    var f3=str_jumin1.substring(2,3)  
    var f4=str_jumin1.substring(3,4)  
    var f5=str_jumin1.substring(4,5)  
    var f6=str_jumin1.substring(5,6)  
    var hap=f1*2+f2*3+f3*4+f4*5+f5*6+f6*7  
    var l1=str_jumin2.substring(0,1)  
    var l2=str_jumin2.substring(1,2)  
    var l3=str_jumin2.substring(2,3)  
    var l4=str_jumin2.substring(3,4)  
    var l5=str_jumin2.substring(4,5)  
    var l6=str_jumin2.substring(5,6)  
    var l7=str_jumin2.substring(6,7)  
    hap=hap+l1*8+l2*9+l3*2+l4*3+l5*4+l6*5  
    hap=hap%11  
    hap=11-hap  
    hap=hap%10  
    if (hap != l7) {  
      return false; 
    }      
        
    var i9=0  

    if (!errfound)  
        return true; 
}


function idCheck(edit_form)
{
	var id = edit_form.admin_id;

	if (!isAlNum(id,'아이디'))
	{
		return false;
	}
	else if (id.value.length > 20 || id.value.length < 4)
	{
		alert('[아이디]는 4자이상 20자이하만 가능합니다.');
		id.value = '';
		id.focus();

		return false;
	}
	else
	{
		document.frames("act").location.href = 'store/company.act.php?act=idcheck&id='+id.value;
	}
}

function isAlNum(s, str)
{
	var OKstr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	for (i = 0; i < s.value.length; i++)
	{
		var c = s.value.charAt(i);

		if (OKstr.indexOf(c) == -1)
		{
			alert('['+str+'] 값은 알파벳과 숫자만 가능합니다.');
			s.value = '';
			s.focus();
			return false; break;
		}
	}
	return true;
}

function blockLoading(){		

	$.blockUI.defaults.css = {}; 
	$.blockUI({ message: $('#loading'), css: {left:'40%', top:'40%',  width: '10px' , height: '10px' ,padding:  '10px'} });  
	setTimeout($.unblockUI, 3000); 
	return true;
}

function unblockLoadingBox(){
	setTimeout($.unblockUI, 100); 
}

function unblockLoading(){
	setTimeout($.unblockUI, 2000); 
}

function completeLoading(msg){
	$.blockUI({ message: msg });  
}