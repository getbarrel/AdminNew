//사용방법 onclick="tab_imgch(해당번호, 총갯수, 이미지명숫자뒤로 삭제, 이미지경로폴더명까지, 아이디값번호빼고, 이미지종류)"
//예제 <img src="{templet_src}/mail_img/tab_menu3.jpg" id="menu03" style="cursor:pointer;"  onclick="tab_imgch(3, 5, 'tab_menu', '{templet_src}/mail_img/', 'menu0', 'jpg')">
function tab_imgch(no, total, idname) {
	for(i=1; i<=total; i++){
		if(i == no) {
			$("#"+idname+i+"o").css("display", "block");
			$("#"+idname+i+"f").css("display", "none");
		} else {
			$("#"+idname+i+"o").css("display", "none");
			$("#"+idname+i+"f").css("display", "block");
		}
	}
}

function PopUpWindow(mypage, w, h, myname, scroll) {
	
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',menubar=no,status=no,toolbar=no,resizable=no'
	win = window.open(mypage, myname, winprops);
	if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
}


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

//밸리데이션 체크
function CheckForm(_element){
	var language = "korean";
	var len = _element.title.length;
	var PT_idtype =/^[a-zA-Z]{1}[a-zA-Z0-9_]+$/;
	//var PT_pwtype =/^[a-zA-Z0-9_]+$/;
	var PT_pwtype =/^(?=([a-zA-Z]+[0-9]+[a-zA-Z0-9]*|[0-9]+[a-zA-Z]+[a-zA-Z0-9]*)).{4,20}$/;
	var chk1 = /^[a-z\d]{6,12}$/i;
	var chk2 = /[a-z]/i;
	var chk3 = /\d/;
	var PT_number =/^[0-9]+$/;　　　　　　　　　　　// 숫자만 사용가
	var PT_com_number =/^[0-9-]+$/;　　　　　　　　　　　// 숫자,-만 사용가
	var PT_alpabet =/^[a-zA-Z\s]+$/;　　　　　　　　   // 영문,띄어쓰기만 사용가
	var PT_company_name =/^[a-zA-Z0-9-\s]+$/;　　　　　　　　   // 영문,숫자,-,띄어쓰기만 사용가
	var PT_korean =/^[가-힣]+$/;　　　　　　　　　　// 한글만 사용가
	var PT_char =/^[가-힣a-zA-Z]+$/;　　　　　　 // 한글,영문만 사용가
	var PT_K_E_N =/^[가-힣a-zA-Z0-9]+$/;　　　　  // 한글,영문,숫자만 사용가
	var PT_K_N =/^[가-힣0-9]+$/;　　　　　　　　// 한글,숫자만 사용가
	var PT_K =/[가-힣]/;　　　　　　　　　　　// 한글포함
	//var PT_email = /[a-z0-9_]{2,}@[a-z0-9-]{2,}\.[a-z0-9]{2,}/i;  // 이메일
	var PT_email_host = /^[a-z0-9-]{2,}\.[a-z0-9]{2,}/i;  // 이메일 kbk
	var PT_email = /^([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)(\.[0-9a-zA-Z_-]+){1,2}$/;
	var PT_regno = /\d{6}(\-|)[1-4]\d{6}$/;
	var PT_file_image = /^[gif|jpg|png|GIF|JPG|PNG]+$/;
	var PT_defance_file_type = /^[php|inc|html|htm|phtml|php3|js|PHP|INC|HTML|HTM|PHTML|PHP3|JS]+$/;
	var PT_file_zip = /^[zip|ZIP]+$/;
	var PT_th_won = /^[0]+$/;
	
	//alert($(_element).attr("validation"));
	
	//alert(_element.type);
	switch (_element.type){
		case "text":
		case "textarea":
		case "password":
		{
			//alert($(_element).attr("name")+" :::"+$(_element).attr("validation"));
			
			if(eval($(_element).attr("validation"))){
				if(_element.value.length < 1){
					/*if(language == "english"){
						alert(" Please enter english characters or numbers for the first letter of '"+_element.title+ "'  ");
					}else{
						alert("'"+_element.title+ "' 첫글자는 영문, 영문과 숫자만 입력하실수 있습니다");
					}*/
					if($(_element).attr("name")!="bbs_contents") {//에디터를 사용하는 게시판 내용 kbk 11/12/28
						if(language == "english"){
							alert(" Please enter a '"+_element.title+ "'  ");					
						}else if(language == "korean"){
							alert("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 입력해주세요");
						}else if(language == "indonesian"){
							alert("Silakan masukkan '"+_element.title+ "'  ");
						}
						_element.focus();
						return false;
					}
				}

				if($(_element).attr("size_min") != "" && $(_element).attr("size_max") != ""){
					if (($(_element).attr("size_min") > _element.value.length) || ($(_element).attr("size_max") < _element.value.length)){
						if(language == "english"){
							alert("'"+_element.title+ "' is available not less than "+$(_element).attr("size_max")+" and not more than "+$(_element).attr("size_min")+" words. ");
						}else if(language == "korean"){
							alert(_element.title + "는 "+$(_element).attr("size_min")+"자이상 "+$(_element).attr("size_max")+"자 이하만 가능 합니다.");
						}else{
							alert("'"+_element.title + "' hanya kurang dari "+$(_element).attr("size_min")+" - "+$(_element).attr("size_max")+" karakter.");
						}
						_element.focus();
						return false;
					}
				}else if($(_element).attr("size_min") != ""){
					if ($(_element).attr("size_min") > _element.value.length){
						if(language == "english"){
							alert("'"+_element.title + "' is available at least  "+$(_element).attr("size_min")+" words ");
						}else if(language == "korean"){
							alert(_element.title + "는 최소 "+$(_element).attr("size_min")+" 자 이상 가능 합니다.");
						}else{
							alert("Panjang '"+_element.title + "' minimum "+$(_element).attr("size_min")+" karakter.");
						}
						_element.focus();
						return false;
					}
				}else if($(_element).attr("size_max") != ""){
					if ($(_element).attr("size_max") < _element.value.length){
						if(language == "english"){
							alert("'"+_element.title + "' is available under "+$(_element).attr("size_max")+" words ");
						}else if(language == "korean"){
							alert(_element.title + "는 최대 "+$(_element).attr("size_max")+"자 이하 가능 합니다.");
						}else{
							alert("Maksimum '"+_element.title + "' "+$(_element).attr("size_max")+" karakter atau kurang.");
						}
						_element.focus();
						return false;
					}
				}
				
				if($(_element).attr("idtype")){

					if (!PT_idtype.test(_element.value)){
						if(language == "english"){
							alert(" Please enter english characters for the first letter of '"+_element.title+ "'  ");
						}else if(language == "korean"){
							alert("'"+_element.title+ "' 첫글자는 영문만 입력하실수 있습니다");
						}else{
							alert("Huruf pertama '"+_element.title+ "' sebaiknya kombinasi antara huruf dan angka.");
						}
						_element.focus();
						return false;
					} else {
						if(_element.value.length < 4 || _element.value.length > 16) {
							if(language == "english"){
								alert('Within 4 to 16 digits, please enter lowercase letters and numbers in combination.');
							}else if(language == "korean"){
								alert("'"+_element.title+ '는 최소 4글자 이상, 최대 16글자 이하로 영문과 숫자 조합 이어야만 합니다.');
							}else{
								alert('Silakan masukkan 4-16 karakter berupa kombinasi huruf kecil dan angka.');
							}
							return false;
						}
					}
				}

				/*if($(_element).attr("pwtype")){
					if(!(chk1.test(_element.value) && chk2.test(_element.value) && chk3.test(_element.value))){
						
						if(language == "english"){
							//alert(" Please enter '"+_element.title+ "' olny in English or numbers ");
							alert('6~12 digit lowercase letters, number use only.');
						}else if(language == "korean"){
							alert("'"+_element.title+ "' 는 영문과 숫자 조합 이어야만 합니다");
						}
						_element.focus();
						return false;
					}
				}*/
				
				if($(_element).attr("pwtype")){
					if(!PT_pwtype.test(_element.value)) {
						
						if(language == "english"){
							//alert(" Please enter '"+_element.title+ "' olny in English or numbers ");
							alert('Within 4 to 20 digits, please enter lowercase letters and numbers in combination.');
						}else if(language == "korean"){
							alert("'"+_element.title+ "' 는 4~20 자리의 영문과 숫자 조합이어야만 합니다");
						}else{
							alert("'"+_element.title+ "' sebaiknya kombinasi antara huruf dan angka bahasa Inggris.");
						}
						_element.focus();
						return false;
					}
				}
				
				if($(_element).attr("sizecheck") == "equal"){
					//alert(_element.value.length + ":::"+ _element.lengthlimit);
					if (_element.value.length != $(_element).attr("lengthlimit")){
						if(language == "english"){
							alert(" Entered '"+_element.title+ "' incorrectly, Please check and try again! ");
						}else if(language == "korean"){
							alert("'"+_element.title+ "' 는 길이가 잘못입력되었습니다. 확인후 다시 시도해주세요");
						}else{
							alert("Panjang '"+_element.title+ "' yang dimasukkan salah. Silakan coba lagi setelah memeriksa.");
						}
						_element.focus();
						return false;
					}
				}

				if($(_element).attr("ssnum")){
					
					if(!ChkJumin(_element.form.jumin1.value, _element.form.jumin2.value)){
						if(language == "korean"){
							alert('주민등록번호가 정확하지 않습니다. 확인후 다시 시도해주세요. ');
						}else{
							alert('Nomor jaminan sosial tidak akurat. Silakan coba lagi setelah memeriksa. ');
						}

						_element.focus();
						return false;				
					}
				}

				if($(_element).attr("numeric")){
					if (!PT_number.test(_element.value)){
						if(language == "english"){
							alert(" Please enter '"+_element.title+ "' in numbers. ");
						}else if(language == "korean"){
							alert("'"+_element.title+ "' 는 숫자만 입력하실수 있습니다. 확인후 다시  입력해주세요");
						}else{
							alert("'"+_element.title+ "' yang dimasukkan hanya angka. Silakan coba lagi setelah memeriksa.");
						}
						
						_element.focus();
						return false;
					}
				}

				if($(_element).attr("com_numeric")){
					if (!PT_com_number.test(_element.value)){
						if(language == "english"){
							alert(" Please enter '"+_element.title+ "' in numbers and `-`. ");
						}else if(language == "korean"){
							alert("'"+_element.title+ "' 는 숫자와 `-` 만 입력하실수 있습니다. 확인후 다시  입력해주세요");
						}else{
							alert("'"+_element.title+ "' yang dimasukkan hanya angka dan `-`. Silakan coba lagi setelah memeriksa.");
						}
						
						_element.focus();
						return false;
					}
				}
				
				if($(_element).attr("th_check")){
						
					if(_element.value.length < 4){
						//alert("'"+_element.title+ "' 는 천원단위로 입력해주세요");
						if(language == "korean"){
							alert("'"+_element.title+ "' 는 천원단위로 입력해주세요");
						}else{
							alert("'"+_element.title+ "' Silakan masukkan 1,000 won.");
						}

						return false;
					}
					var th_number = _element.value.length - 3;
					var th_number2 = _element.value.substr(th_number,3);
					for(var j=0;j<th_number2.length;j++){
						if (!PT_th_won.test(th_number2.charAt([j]))){
							//alert("'"+_element.title+ "' 는 천원단위로 입력해주세요");
							if(language == "korean"){
								alert("'"+_element.title+ "' 는 천원단위로 입력해주세요");
							}else{
								alert("'"+_element.title+ "' Silakan masukkan 1,000 won.");
							}
							_element.focus();
							return false;
						}
					}
				}
		
				if($(_element).attr("korean")){
					if (!PT_korean.test(_element.value)){
						if(language == "english"){
							alert("  Please enter '"+_element.title+ "'  only in Korean. ");
						}else if(language == "korean"){
							//alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  한글만 입력해주세요 ");
							alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  한글만 입력해주세요 ");
						}else{
							alert(" Silakan masukkan '"+_element.title+ "' huruf Korea saja.");
						}
						_element.focus();
						return false;
					}
				}

				if($(_element).attr("alpabet")){
					if (!PT_alpabet.test(_element.value)){
						if(language == "english"){
							alert("  Please enter '"+_element.title+ "'  only in Alpabet. ");
						}else if(language == "korean"){
							//alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  영문만 입력해주세요 ");
							alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  영문만 입력해주세요 ");
						}else{
							alert(" Silakan masukkan '"+_element.title+ "' huruf Inggris saja.");
						}
						_element.focus();
						return false;
					}
				}

				if($(_element).attr("company_name")){
					if (!PT_company_name.test(_element.value)){
						if(language == "english"){
							alert("  Please enter '"+_element.title+ "'  only in Alpabet and dash. ");
						}else if(language == "korean"){
							//alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  영문,-만 입력해주세요 ");
							alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  영문과 ,`-` 만 입력해주세요 ");
						}else{
							alert(" Silakan masukkan '"+_element.title+ "' huruf Inggris dan `-` saja.");
						}
						_element.focus();
						return false;
					}
				}
		
				
				
				if($(_element).attr("phrase")){
					if (!PT_char.test(_element.value)){
						//alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  한글,영문만 입력해주세요 ");
						if(language == "english"){
							alert("  Please enter '"+_element.title+ "'  only in Korean and Alpabet. ");
						}else if(language == "korean"){
							alert("'"+_element.title+ "'"+checkLastChar(_element.title.substring(len-1,len))+"  한글,영문만 입력해주세요 ");
						}else{
							alert(" Silakan masukkan '"+_element.title+ "' huruf Korea dan Inggris saja.");
						}
						//alert("Silakan masukkan huruf Korea dan Inggris saja.");
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
						}else if(language == "korean"){
							alert('['+compare_A.title+']와 ['+compare_B.title+']이 일치하지 않습니다. ');
						}else{
							alert('['+compare_A.title+'] dan ['+compare_B.title+'] tidak cocok. ');
						}
						_element.focus();
						return false;				
					}
				}
		
				if($(_element).attr("email_host")){
					if (!PT_email_host.test(_element.value)){
						if(language == "english"){
							alert('Invalid E-mail format. Check and retry please');
						}else if(language == "korean"){
							alert('이메일 형식이 아닙니다. 확인후 다시 시도해주세요');
						}else{
							alert('Ini bukan alamat email. Silakan coba lagi setelah memeriksa.');
						}
						_element.focus();
						return false;
					}
				}

				if($(_element).attr("email")){
					if (!PT_email.test(_element.value)){
						if(language == "english"){
							alert('Invalid E-mail format. Check and retry please');
						}else if(language == "korean"){
							alert('이메일 형식이 아닙니다. 확인후 다시 시도해주세요');
						}else{
							alert('Ini bukan alamat email. Silakan coba lagi setelah memeriksa.');
						}
						_element.focus();
						return false;
					}
				}
		
				if($(_element).attr("duplicate")){
					//alert($(_element).attr("duplicate"));
					if (!eval($(_element).attr("dup_check"))){
						if(language == "english"){
							alert("This '"+_element.title+"' is already being used.");
						}else if(language == "korean"){
							alert("이미 사용중인 "+_element.title+" 입니다.");
						}else{
							alert("'"+_element.title+"' sudah digunakan.");
						}
						_element.focus();
						return false;
					}
				}

				if($(_element).attr("name")=="bbs_contents") {//게시판 쓰기 에디터용 검사 kbk 11/12/07
					var objStrip = new RegExp();
					objStrip = /[<][^>]*[>]/gi;
					var bbs_val=$(_element).val();
					var bbs_con_text=bbs_val.replace(objStrip, "");

					var objStrip2 = new RegExp();
					objStrip2 = /&nbsp;/gi;
					bbs_con_text=bbs_con_text.replace(objStrip2, "");
					bbs_con_text=bbs_con_text.trim();
					if(bbs_con_text.length<1) {
						alert("'"+_element.title+ "'을 입력해주세요.");
						return false;
					}
				}
				
				
			}
			return true;
			break;
		}
		
		case "select-one":
		{
			//alert(_element[_element.selectedIndex].value+":::");
			if(eval($(_element).attr("validation"))){
				//if(_element.selectedIndex==0){
				if(_element.value==""){//kbk
					if(language == "english"){
						alert("Please select a '"+_element.title+ "' ");
					}else if(language == "korean"){
						alert("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 선택해주세요");
					}else{
						alert("Silakan pilih '"+_element.title+ "'");
					}
					//_element.focus(); // jquery 효과때문에 포커스 인식을 못함 kbk
					return false;
				}
			}					
			return true;
			break;
		}
		
		case "checkbox": 
		{
			if(eval($(_element).attr("validation"))){
				if(!_element.checked){
					if(language == "english"){
						alert("Please select a '"+_element.title+ "' ");
					}else if(language == "korean"){
						alert("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 선택해주세요");
					}else{
						alert("Silakan pilih '"+_element.title+ "'");
					}
					//_element.focus(); // jquery 효과때문에 포커스 인식을 못함 kbk
					return false;
				}
			}					
			return true;
			break;
		}
		case "radio":
		{
			//alert(_element.name+" :::"+$(_element).attr("validation"));
			//alert($(_element).attr("validation"));
			if(eval($(_element).attr("validation"))){
				//alert(_element.form.getAttribute("name"));
				var cobj = eval("document.forms['"+_element.form.getAttribute("name")+"']."+_element.name);
				
				if(cobj.length) {
					for(j=0;j < cobj.length;j++){
						if(cobj[j].checked){
							return true;								
							break;	
						}
					}
				} else {// 라디오 버튼이 한개만 있을 때 kbk 12/04/06
					if(cobj.checked){
						return true;								
						break;
					}
				}
				
					if(language == "english"){
						alert("Please select a '"+_element.title+ "' ");
					}else if(language == "korean"){
						alert("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 선택해주세요");
					}else{
						alert("Silakan pilih '"+_element.title+ "'");
					}
					//_element.focus();
					return false;
					break;
			}else{
				return true;
				break;
			}
		}
		case "file":
		{
			if(eval($(_element).attr("validation"))){
				if(_element.value.length < 1){
					if(language == "english"){
						alert("Please select a '"+_element.title+ "' ");
					}else if(language == "korean"){
						alert("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 선택해주세요");
					}else{
						alert("Silakan pilih '"+_element.title+ "'");
					}
					_element.focus();
					return false;
				}
				
				if(_element.value.length > 0){
					var defancefiletype  = _element.value.substring(_element.value.length-3);
					if (PT_defance_file_type.test(defancefiletype)){
						if(language == "english"){
							alert('This file type is not available to register. Please try again after checking.');
						}else if(language == "korean"){
							alert('등록가능한 파일타입이 아닙니다. 확인후 다시 시도해주세요');
						}else{
							alert('Dokumen ini tidak dapat didaftarkan. Silakan coba lagi setelah memeriksa.');
						}
						_element.focus();
						return false;
					}
				}
				
				if($(_element).attr("filetype") == "image"){
					var filetype  = _element.value.substring(_element.value.length-3); 	 
		
					if (!PT_file_image.test(filetype)){
						if(language == "english"){
							alert('This file type is not available to register. Please try again after checking');
						}else if(language == "korean"){
							alert('등록가능한 파일타입이 아닙니다. 확인후 다시 시도해주세요');
						}else{
							alert('Dokumen ini tidak dapat didaftarkan. Silakan coba lagi setelah memeriksa.');
						}
						_element.focus();
						return false;
					}
		
				}
		
		
				if($(_element).attr("filetype") == "zip"){
					var filetype  = _element.value.substring(_element.value.length-3); 	 
		
					if (!PT_file_zip.test(filetype)){
						if(language == "english"){
							alert('This file type is not available to register. Please try again after checking');
						}else if(language == "korean"){
							alert('등록가능한 파일타입이 아닙니다. 확인후 다시 시도해주세요');
						}else{
							alert('Dokumen ini tidak dapat didaftarkan. Silakan coba lagi setelah memeriksa.');
						}
						_element.focus();
						return false;
					}
		
				}
			} else {
				if(_element.value.length > 0){
					var defancefiletype  = _element.value.substring(_element.value.length-3);
					if (PT_defance_file_type.test(defancefiletype)){
						if(language == "english"){
							alert('This file type is not available to register. Please try again after checking.');
						}else if(language == "korean"){
							alert('등록가능한 파일타입이 아닙니다. 확인후 다시 시도해주세요');
						}else{
							alert('Dokumen ini tidak dapat didaftarkan. Silakan coba lagi setelah memeriksa.');
						}
						_element.focus();
						return false;
					}

					if($(_element).attr("filetype") == "image"){
						var filetype  = _element.value.substring(_element.value.length-3); 	 
			
						if (!PT_file_image.test(filetype)){
							if(language == "english"){
								alert('This file type is not available to register. Please try again after checking');
							}else if(language == "korean"){
								alert('등록가능한 파일타입이 아닙니다. 확인후 다시 시도해주세요');
							}else{
								alert('Dokumen ini tidak dapat didaftarkan. Silakan coba lagi setelah memeriksa.');
							}
							_element.focus();
							return false;
						}
			
					}

					if($(_element).attr("filetype") == "zip"){
						var filetype  = _element.value.substring(_element.value.length-3); 	 
			
						if (!PT_file_zip.test(filetype)){
							if(language == "english"){
								alert('This file type is not available to register. Please try again after checking');
							}else if(language == "korean"){
								alert('등록가능한 파일타입이 아닙니다. 확인후 다시 시도해주세요');
							}else{
								alert('Dokumen ini tidak dapat didaftarkan. Silakan coba lagi setelah memeriksa.');
							}
							_element.focus();
							return false;
						}
			
					}
				}
			}
			return true;
			break;
		}

		case "hidden":
		{
			//alert($(_element).attr("name"));
			if(eval($(_element).attr("validation"))){
				if(_element.value.length < 1){
					if(_element.name=="id_flag") {
						alert("'"+_element.title+ "' ");
					}else if(language == "korean"){
					    alert("'"+_element.title+ "'을 입력해주세요.");
						//alert("'"+_element.title+ "' "+checkLastChar(_element.title.substring(len-1,len))+" 설문이 완료되지 않았습니다.");
					}else{
						alert("'"+_element.title+ "' Survey belum selesai.");
					}
					//_element.focus();
					return false;
				}
			}
			//alert($(_element).attr("name"));
			return true;
			break;
		}
		default:
		{
			//alert(1);
			return true;
			break;
		}
			//alert('여기오나');
			//return false;
	}
		
}

//주민번호 체크 함수
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
	if(str_jumin2.substring(0,1) == "1" || str_jumin2.substring(0,1) == "2"){
		if(str_jumin1.substring(0,1) < 2) {  
			return false; 
		} 
	}else if(str_jumin2.substring(0,1) == "3" || str_jumin2.substring(0,1) == "4"){
		var today = new Date();
		var year = today.getFullYear()+'';
		if(str_jumin1.substring(0,2) > year.substring(2,4)){
			return false;
		}
	}
    if(str_jumin2.substring(0,1) > 4) {  
        return false; 
    }  
	
    if((str_jumin1.length > 7) || (str_jumin2.length > 8)) {  
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
//밸리데이션 체크
function CheckFormValue(frm){
	for(i=0;i < frm.elements.length;i++){
		
		if(frm.elements[i].tagName != "OBJECT"){
			if(!CheckForm(frm.elements[i])){
				return false;
			}
		}
	}
	
	return true;
}

function categoryList(){
	var obj = document.getElementById('category_list');
	var obj2 = document.getElementById('brand_list');
	if(obj.style.display == 'none'){
		obj.style.display = "block";
	}else{
		obj.style.display = "none";
	}
	if(obj2.style.display == 'block') {
		obj2.style.display = 'none';
	}
}

function brandList(templet_dir) {
	var obj = document.getElementById('brand_list');
	var obj2 = document.getElementById('category_list');
	if(obj.style.display =='none') {
		obj.style.display = 'block';
		
		new Ajax.Request(templet_dir+'/brands.xml',
		{
			method: 'POST',
			parameters: '',
			onComplete: function(transport){
				
					var xmlDoc = new ActiveXObject('Msxml2.DOMDocument');
					xmlDoc.async = false;
					//alert(transport.responseText);
					xmlDoc.loadXML(transport.responseText);
					
					var err = xmlDoc.parseError;
					
					if (err.errorCode != 0)
						throw new Error('XML 문서 해석 실패 - ' + err.reason);
					
					var xsl = new ActiveXObject('Microsoft.XMLDOM');
					xsl.async = false;
					xsl.load(templet_dir+'/brands.xsl');
					//alert(3);
					//alert(xmlDoc.transformNode(xsl));
					document.getElementById('brand_list').innerHTML = xmlDoc.transformNode(xsl);
					
					var err = xmlDoc.parseError;
					if (err.errorCode != 0)
						throw new Error('XSL 문서 해석 실패 - ' + err.reason);
				}
		})
	}else{
		obj.style.display = 'none';
	}
	if(obj2.style.display == 'block') {
		obj2.style.display = 'none';
	}
}

function FormatNumber2(num){
      
        fl=""
        if(isNaN(num)) { /*alert("문자는 사용할 수 없습니다.");*/return 0}
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

function FormatNumber(num){
        num=new String(num)
        num=num.replace(/,/gi,"")
      //  pricecheckmode = false;
        
        return FormatNumber2(num)
}

		//콤마표현 없는 정수만입력
function onlyEditableNumber(obj){
 var str = obj.value;
 str = new String(str);
 var Re = /[^0-9]/g;  
 str = str.replace(Re,''); 
 obj.value = str;
}


this.screenshotPreview = function(){	
	/* CONFIG */
		
		xOffset = 10;
		yOffset = 30;
		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result
		
	/* END CONFIG */
	$("a.screenshot").hover(function(e){
		this.t = this.title;
		this.title = "";	
		var c = (this.t != "") ? "<br/>" + this.t : "";
		$("body").append("<p id='screenshot'><img src='"+ this.rel +"' alt='url preview' />"+ c +"</p>");								 
		$("#screenshot")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");						
    },
	function(){
		this.title = this.t;	
		$("#screenshot").remove();
    });	
	$("a.screenshot").mousemove(function(e){
		$("#screenshot")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});			
};
this.tooltip = function(){	
	/* CONFIG */		
		xOffset = 10;
		yOffset = 20;		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result		
	/* END CONFIG */		
	$("a.tooltip").hover(function(e){											  
		this.t = this.title;
		this.title = "";									  
		$("body").append("<p id='tooltip'>"+ this.t +"</p>");
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");		
    },
	function(){
		this.title = this.t;		
		$("#tooltip").remove();
    });	
	$("a.tooltip").mousemove(function(e){
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});			
};

// starting the script on page load
$(document).ready(function(){
	//
	if($("#screenshot")) screenshotPreview();
	if($("#tooltip")) tooltip();
	
	//체크박스 체크와 헤제 jquery스크립트
	$("input[name='all_chk']").click(function(){
		var frm=$(this).val();
		if($(this).attr("checked")){
			$("."+frm).attr("checked","checked");
			//alert($(this).val()+' is checked');
		}else{
			$("."+frm).attr("checked","");
		}
	});

});
$(document).ready(function(){
	$("#cateall").click(function(){
		if($(".service").html() == ""){
			$.ajax({
				url : '/category/top_cate.php',
				type : 'GET',
				data : {act:'123'},
				dataType: 'html',
				cache:true,
				error: function(data,error){// 실패시 실행함수 
					alert(error);},
				success: function(transport){
				//alert(transport),
					$(".service").css("display", "block"),
					$(".service").html(transport);
				}
			});
		}
		$('.warpbox').toggle();
	});
	$("#privacy_open").click(function(){
		if($(".Modal_box1").html() == ""){
			$.ajax({
				url : '/popup/modal.php',
				type : 'GET',
				data : {company_id:$("#privacy_open").attr("rel")},
				dataType: 'html',
				cache:true,
				error: function(data,error){// 실패시 실행함수 
					alert(error);},
				success: function(transport){
				//alert(transport),
					$(".Modal_box1").css("display", "block"),
					$(".Modal_box1").html(transport);
				}
			});
		}
		$('.Modal_box1').toggle();
	});
});
	function change_option_div1(){
		$.ajax({
			url : '/shop/option_price.php',
			type : 'POST',
			data : {
			act:'CHoption_div1',
			option_add_name:$("#option_add_name").val(),
			sellprice:$("#sellprice").val(),
			addprice:$("#option_add_name option:selected").attr("addprice"),
			pid:$("#id").val()
			},
			dataType: 'html',
			cache:true,
			error: function(data,error){// 실패시 실행함수 
				alert(error);},
			success: function(transport){
			alert(transport);
				$("#sellprice").val(transport);
				$("#sellprice_area").html("Rp. "+FormatNumber(transport));
			}
		});
	}
	function change_option_div2(){
		$.ajax({
			url : '/shop/option_price.php',
			type : 'POST',
			data : {
			act:'CHoption_div2',
			option_year:$("#option_year").val(),
			sellprice:$("#sellprice").val(),
			saleprice:$("#option_year option:selected").attr("saleprice"),
			pid:$("#id").val()
			},
			dataType: 'html',
			cache:true,
			error: function(data,error){// 실패시 실행함수 
				alert(error);},
			success: function(transport){
				alert(transport);
				$("#sellprice").val(transport),
				$("#sellprice_area").html("Rp. "+FormatNumber(transport));
			}
		});
	}
	function change_option_div(){
		var totalprice;
		var basicyear = 1;
		var use_month = 1;
		var sellprice = $("#sellprice").val();
		var option_basic_term = $("#option_basic_term").val();
		var basic_price = $("#sellprice").attr("basic_price");

		//기간(약정기간)
		if($("#option_year option:selected").val()) var option_year = $("#option_year option:selected").val();
		else var option_year = 0;
		//부가서비스명칭
		if($("#option_add_name option:selected").val()) var option_add_name = $("#option_add_name option:selected").val();
		else var option_add_name = 0;
		//부가서비스코드
		if($("#option_add_name option:selected").attr("addcode")) var addcode = $("#option_add_name option:selected").attr("addcode");
		else var addcode = "";
		//부가서비스 추가금액
		if($("#option_add_name option:selected").attr("addprice")) var addprice = $("#option_add_name option:selected").attr("addprice");
		else var addprice = 0;
		//약정기간 할인요율
		if($("#option_year option:selected").attr("saleprice")) var saleprice = $("#option_year option:selected").attr("saleprice");
		else var saleprice = 0;
		//인원 값
		if($("#option_add_cnt_nomal").val()) {
			var option_add_cnt_nomal = $("#option_add_cnt_nomal").val();
			if(option_add_cnt_nomal <= 0){
				alert("유저는 0이하의 숫자가 들어갈 수 없습니다.");
				$("#option_add_cnt_nomal").val(1);
				option_add_cnt_nomal = 1;
			}
		}else{ 
			var option_add_cnt_nomal = 0;
		}
		//유저 값
		if($("#option_add_cnt").val()) {
			var option_add_cnt = $("#option_add_cnt").val();
			var optionno = $("#option_add_cnt").attr("optionno");
			var optionnoprice = $("#option_add_cnt").attr("optionnoprice");
			//유저추가시에 연산
			var addmemberper = parseInt(option_add_cnt) / parseInt(optionno);
			addmemberper = round(addmemberper, 0);
			noprice = addmemberper * optionnoprice;
		} else { 
			var noprice = 0;
		}
		//용량 값
		if($("#option_add_disk").val()) {
			var option_add_disk = $("#option_add_disk").val();
			var addbite = $("#option_add_disk").attr("addbite");
			var addbiteprice = $("#option_add_disk").attr("addbiteprice");
			//용량추가시에 연산
			var addbiteper = option_add_disk / addbite;
			addbiteper = round(addbiteper, 0);
			biteper = addbiteper * addbiteprice;
		} else { 
			var biteper = 0;
		}
		
		//기본적인 기간을 가져와서 연단위인지 월단위인지 파악하여 연산될 기간을 설정
		if(option_year > 0) {
			if(option_basic_term == 1) basicyear = option_year * 12;
			else if (option_basic_term == 2) basicyear = option_year;
			use_month = option_year * 12;
		} else {
			use_month = basicyear;
		}
		
		//추가될 금액을 계산
		var plusprice = parseInt(basic_price) + parseInt(addprice) + parseInt(noprice) + parseInt(biteper);
		if(basicyear > 0) plusprice = plusprice * basicyear;
		var totalsaleprice = plusprice * (saleprice * 0.01); //약정기간에 따른 할인율을 구한다
		totalprice = parseInt(plusprice) - parseInt(totalsaleprice); //약정기간에 따른 할인율을 총금액에서 차감한다.
		if(option_add_cnt_nomal > 0) totalprice = totalprice * option_add_cnt_nomal; //일반인원수이면 인원수를 총금액에서 곱해준다
		$("#use_month").val(use_month), //사용기간을 수정한다
		$("#option_add_text1").val(addcode), //부가서비스코드를 변경,입력한다
		$("#sellprice").val(totalprice), //결제금액을 수정한다
		$("#sellprice_area").html("Rp. "+FormatNumber(totalprice)); //결제금액을 보여준다
	}

// round 함수 ( val = 값, precision= 소숫점 자릿수) 
function round(val,precision) { 
  val = val * Math.pow(10,precision); 
  val = Math.ceil(val); 
  return val/Math.pow(10,precision); 
} 

function allpopupClose(){
	$('.warpbox').css("display", "none");
}
function check_key() {
 var char_ASCII = event.keyCode;
  //숫자
 if (char_ASCII >= 48 && char_ASCII <= 57 )
   return 1;
 //영어
 else if ((char_ASCII>=65 && char_ASCII<=90) || (char_ASCII>=97 && char_ASCII<=122))
    return 2;
 //특수기호
 else if ((char_ASCII>=33 && char_ASCII<=47) || (char_ASCII>=58 && char_ASCII<=64) 
   || (char_ASCII>=91 && char_ASCII<=96) || (char_ASCII>=123 && char_ASCII<=126))
    return 4;
 //한글
 else if ((char_ASCII >= 12592) || (char_ASCII <= 12687))
    return 3;
 else 
    return 0;
}

//텍스트 박스에 숫자만 입력할수 있도록

function numberKey() {

 if(check_key() != 1 ) {
  event.returnValue = false;   
  $("#number_value").css("color","#FF5A00").text("숫자만 입력할 수 있습니다.");
  return;
 } else {
  $("#number_value").css("color","#FF5A00").text("");
  return;
 }
}

function potal_loginurl(id, pw, domain){
	f = document.createElement('form');
	f.name = 'frmLogin';
	f.id = 'frmLogin';
	f.method    = 'post'; 
	f.action    = domain;

	i0          = document.createElement('input');
	i0.type     = 'hidden';
	i0.name     = 'forbiz_mem_id';
	i0.id     = 'forbiz_mem_id';
	i0.value    = id;
	f.appendChild(i0);

	i1          = document.createElement('input');
	i1.type     = 'hidden';
	i1.name     = 'forbiz_pw';
	i1.id     = 'forbiz_pw';
	i1.value    = pw;
	f.appendChild(i1);
	
	document.body.appendChild(f);
	setTimeout('f.submit()');
}

function potal_loginurl2(id, pw, domain){
	f = document.createElement('form');
	f.name = 'frmLogin';
	f.id = 'frmLogin';
	f.method    = 'post'; 
	f.action    = domain;

	i0          = document.createElement('input');
	i0.type     = 'hidden';
	i0.name     = 'loginid';
	i0.id     = 'loginid';
	i0.value    = id;
	f.appendChild(i0);

	i1          = document.createElement('input');
	i1.type     = 'hidden';
	i1.name     = 'loginpw';
	i1.id     = 'loginpw';
	i1.value    = pw;
	f.appendChild(i1);
	
	document.body.appendChild(f);
	setTimeout('f.submit()');
}

function input_text(){
	if($("#msg2").attr("rel") == "first"){
		$("#msg2").val("");
		$("#msg2").attr("rel","");
	}
}

function alert_img(){
	write('<div id=alertImg style="position:relative;width:676;height:270;overflow:hidden"><img src=/images/order_non_popup.jpg onclick=CloseBtn(\"alertImg\")><div>')
}

function CloseBtn(id){
	$("#alertImg").css("display","none");
}

// 자바스크립트로 PHP의 number_format 흉내를 냄
// 숫자에 , 를 출력
function number_format(n) {
  var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
  n = String(n);                    // 숫자를 문자열로 변환

  while (reg.test(n))
    n = n.replace(reg, '$1' + ',' + '$2');

  return n;
}

/** 메인 상품 시작 **/

function productScroll(obj, w)
{
	speed = (arguments[2])	?	arguments[2]:1000;
	if(w > 0)	{
		$('div#'+obj).find('div.good_names:first-child').before($('div#'+obj).find('div.good_names:last-child').clone());
		$('div#'+obj).find('div.good_names:last-child').remove();
		$('div#'+obj).css('marginLeft','-210px');
		w = 0;

	}
	$('div#'+obj).animate({marginLeft:w+'px'}, speed, null, function()	{
		if(w < 0)	{
			$(this).find('div.good_names:first-child').clone().appendTo($(this));
			$(this).find('div.good_names:first-child').remove();
			$(this).css('marginLeft',0);
		}	else	{

		}
	});
}

function main_scroll_width() {
	/*if(document.getElementById("main_scroll_width")) {
		var obj=document.getElementById("main_scroll_width");
		//obj.style.width=parseInt(obj.offsetWidth)-80+"px";
		var ww=parseInt(obj.offsetWidth)/210;
		//alert(ww);
		obj.style.width=210*parseInt(ww)+"px";
	}*///슬라이드 박스의 길이를 자동으로 잡아주는 용도로 만들었으나 필요없다고 판단 주석처리함 kbk 12/01/17
}

function select_addr_data(iden, target, value) {
	$.ajax({
		url : '/member/address.act.php',
		type : 'POST',
		data : {
		act:'address_select',
		address:$("#"+iden+" option:selected").val(),
		value:value
		},
		dataType: 'html',
		cache:true,
		error: function(data,error){// 실패시 실행함수 
			alert(error);},
		success: function(transport){
		//alert(transport);
			$("#"+target).html(transport);
		}
	});
}

/** 메인 상품 끝 **/

function view_brand_menu(n) {
	var b_obj=document.getElementById("h_menu_brand_box");
	if(n) {
		//b_obj.style.display="block";
		$("#h_menu_brand_box").fadeIn(300);
	} else {
		//b_obj.style.display="none";
		$("#h_menu_brand_box").fadeIn(10,function () {
			$(this).fadeOut(300);
		});
	}
}

$(document).ready(function(){
	main_scroll_width();
	$("#h_menu_brand_img").click(function() {
		view_brand_menu(1);
	});
});

function page_move(obj) {
 if(!CheckFormValue(obj)){
  return false;
 }
 
 if($("#page").val() > $("#total_page").val()) {
  alert('페이지를 찾을 수 없습니다.');
  return false;
 }
}

function imageResize(ImgClass, WidthValue, HeightValue){
 $("img."+ImgClass).each(function(){
  if($(this).width() > $(this).height()) {
   if($(this).width() > WidthValue){
    $(this).width(WidthValue);
   }
   $(this).css("margin-top", (parseInt(HeightValue) - parseInt($(this).height()))/2+"px");
  } else {
   if($(this).height() > HeightValue){
    $(this).height(HeightValue);
   }
  }
 });
}
function loadCategory(sel,target) {
	//alert(sel.options[sel.selectedIndex].value);
	var trigger = sel.options[sel.selectedIndex].value;	
	var form = sel.form.name;
	
	var depth = $("select[name="+sel.name+"]").attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
	
	window.frames["act"].location.href = '/admin/product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	
}

function selectCategory(sel,depth) {
	//alert(2);
	var form = sel.form;
	
	form.cid2.value=sel.options[sel.selectedIndex].value;
	form.depth.value=depth;

}
function BrandInput(frm,mode)
{
	//alert(mode);
	frm.mode.value = mode;
	//frm.brandimg.style.display="block";
	frm.top_design.value = iView.document.body.innerHTML;
	return CheckFormValue(frm);
	//frm.submit();
}

function priceBoxChange(type){
    if(type == 'free'){
        $("#price_box").attr('disabled',true);
        $("#price_box").val("");
    }else{
        $("#price_box").attr('disabled',false);
    }
    
}
function map_view(id){
    window.open('/lecture/map_large.php?pid='+ id,'','width=1021,height=721,scrollbars=no,status=no');
}
function map_view_list(type){
    window.open('/lecture/map_large.php?type='+type,'','width=1021,height=721,scrollbars=no,status=no');
}
function map_view_adv(){
    window.open('/lecture/map_large_adv.php','','width=1021,height=721,scrollbars=no,status=no');
}
function frmButtonHandler(frm,condition){
    for(i=0;i<frm.length;i++){
        if(frm[i].type == "button" || frm[i].type == "submit" || frm[i].type == "image"){
            if(condition == "disabled"){
                frm[i].disabled = true;
            }else{
                frm[i].disabled = false;
            }
        }
    }
}