function processForm(e) {
	if (e.preventDefault) e.preventDefault();
	return false;
}

// 주소검색
function search(){
	var index = document.getElementById('IndexWord').value;
	if(index.length != 0) {
			document.getElementById('PageNum').value = 1;
			document.getElementById('zipcode_form').submit();
	}else {
		alert('검색어를 입력하여 주세요');
		return false;
	}
}

// 수정제시어로 검색
function suggest_search(indexWord){
	document.getElementById('IndexWord').value = indexWord;
	document.getElementById('PageNum').value = 1;
	document.getElementById('zipcode_form').submit();
}

// 다음페이지
function nextPage(){
	currentPage = document.getElementById('current_page').innerText;
	totalPage = document.getElementById('end_page').innerText;
	
	if (currentPage < totalPage)
	{
		document.getElementById('PageNum').value = currentPage*1 + 1;				
		document.getElementById('zipcode_form').submit();
	}
}

// 이전페이지
function prevPage(){
	currentPage = document.getElementById('current_page').innerText;

	if(currentPage  > 1){
		document.getElementById('PageNum').value = currentPage*1 -1;			
		document.getElementById('zipcode_form').submit();
	}
}

// 상세주소 입력폼 호출
function detail(type, zipcode, sectionNum, roadAddr1, roadAddr2, jibunAddr){
	document.getElementById('content_result').style.display="none";
	document.getElementById('content_juso').style.display="none";
	document.getElementById('content_detail').style.display='';
	
	if(type == "roadAddr"){
		document.getElementById('road').checked = true;
	}else {
		document.getElementById('jibun').checked = true;
	}

	document.getElementById('road_zipcode').innerHTML = zipcode;
	document.getElementById('road_zipcode').value = zipcode;
	document.getElementById('road_sectionNum').innerHTML = "("+sectionNum+")";
	document.getElementById('road_sectionNum').value = sectionNum;
	document.getElementById('roadAddr1').innerHTML = roadAddr1;
	document.getElementById('roadAddr1').value = roadAddr1;
	document.getElementById('roadAddr2').innerHTML = roadAddr2;
	document.getElementById('roadAddr2').value = roadAddr2;

	document.getElementById('jibun_zipcode').innerHTML = zipcode;
	document.getElementById('jibun_zipcode').value = zipcode;
	document.getElementById('jibun_sectionNum').innerHTML = "("+sectionNum+")";
	document.getElementById('jibun_sectionNum').value = sectionNum;
	document.getElementById('jibunAddr').value = jibunAddr;
	document.getElementById('jibunAddr').innerHTML = jibunAddr;
}

// 부모 페이지로 우편번호, 주소 전달	
function inputAddr(zip_type,obj_id){
	var roadAddr1;
	var roadAddr2;
	// 도로명주소 선택시.
	if(document.getElementById('road').checked){

		zipcode = document.getElementById('road_sectionNum').value;
		//zipcode = document.getElementById('road_zipcode').value; 우편번호 다섯자리로 바뀜
		roadAddr1 = document.getElementById('roadAddr1').value;
		roadAddr2 = document.getElementById('roadAddr2').value;
		roadAddrDetail = document.getElementById('road_detail').value;
		new_zipcode = document.getElementById('road_sectionNum').value;	
		//zipcode = zipcode.split( '-' );


		if (roadAddrDetail != ''){
			addrResult = roadAddrDetail +" "+roadAddr2;
		}else {
			addrResult = roadAddr2;
		}
	} else {
		zipcode = document.getElementById('jibun_sectionNum').value;
		//zipcode = document.getElementById('jibun_zipcode').value; 우편번호 다섯자리로 바뀜
		roadAddr1 = document.getElementById('jibunAddr').value;
		detail = document.getElementById('jibun_detail').value;
		//zipcode = zipcode.split( '-' );
		new_zipcode = document.getElementById('jibun_sectionNum').value;
		
		addrResult = detail;
		
		
	}

	if(zip_type == 1){
		opener.document.getElementById("zipcode1").value = zipcode;
	//	opener.document.getElementById("new_zipcode").value = new_zipcode; //5자리 우편번호 
		
	//	opener.document.getElementById("zipcode1").value = zipcode[0];
	//	opener.document.getElementById("zipcode2").value = zipcode[1];
		opener.document.getElementById("addr1").value = roadAddr1;		
		opener.document.getElementById("addr2").value=addrResult;
	//	opener.document.getElementById("addr2").focus();	
		$('#zipcode2' , opener.document).val('');

	}else if(zip_type == 2){
		opener.document.getElementById("zipcode1_b").value = zipcode;
	//	opener.document.getElementById("new_zipcode").value = new_zipcode; //5자리 우편번호 

	//	opener.document.getElementById("zipcode1_b").value = zipcode[0];
	//	opener.document.getElementById("zipcode2_b").value = zipcode[1];
		opener.document.getElementById("addr1_b").value    = roadAddr1;		
		opener.document.getElementById("addr2_b").value=addrResult;
		//opener.document.getElementById("addr2_b").focus();	
		$('#zipcode2_b' , opener.document).val('');

	}else if(zip_type == 3){
		opener.document.getElementById("czipcode1").value = zipcode;
	//	opener.document.getElementById("new_zipcode").value = new_zipcode; //5자리 우편번호 
		
	//	opener.document.getElementById("czipcode1").value = zipcode[0];
	//	opener.document.getElementById("czipcode2").value = zipcode[1];
		opener.document.getElementById("caddr1").value = roadAddr1;		
		opener.document.getElementById("caddr2").value=addrResult;
		//opener.document.getElementById("caddr2").focus();	
		$('#czipcode2' , opener.document).val('');

	}else if(zip_type == 4){
		opener.document.getElementById("zip_b_1").value = zipcode;
	//	opener.document.getElementById("new_zipcode").value = new_zipcode; //5자리 우편번호 

	//	opener.document.getElementById("zip_b_1").value = zipcode[0];
	//	opener.document.getElementById("zip_b_2").value = zipcode[1];
		opener.document.getElementById("addr_b_1").value = roadAddr1;		
		opener.document.getElementById("addr_b_2").value=addrResult;
		//opener.document.getElementById("addr_b_2").focus();	
		$('#zip_b_2' , opener.document).val('');
		
		
	}else if(zip_type == 9){
		
		$('#'+obj_id , opener.document).find('#zipcode1').val(zipcode);
		//$('#'+obj_id , opener.document).find('#zipcode2').val(b);
		$('#'+obj_id , opener.document).find('#addr1').val(roadAddr1);
		$('#'+obj_id , opener.document).find('#addr2').val(addrResult);
		$('#'+obj_id , opener.document).find('#zipcode2').val('');

	}else{
		alert('zip_type 이 없습니다.2');	
	}
/*
		if (roadAddrDetail != ''){
			addrResult = roadAddr1 +", "+ roadAddrDetail +" "+roadAddr2;
		} else {
			addrResult = roadAddr1 +" "+roadAddr2;
		}
*/
	// 지번주소 선택시.
	if(typeof(window.opener.GetReigonPrice) == "function" || typeof(window.opener.GetReigonPrice) == "object"){	//도서산간 배송비 추가 함수 2014-01-17 이학봉
		
		window.opener.GetReigonPrice();
	}

	//window.opener.putAddr(zipcode, addrResult);
	self.close();
}
function zipcode(a,b,c,d,zip_type){
		//var obj = opener.document.all;

	if(zip_type == 1){
		opener.document.getElementById("zipcode1").value = a;
		opener.document.getElementById("zipcode2").value = b;
		opener.document.getElementById("addr1").value    = c;		
		opener.document.getElementById("addr2").value='';
		opener.document.getElementById("addr2").focus();	

	}else if(zip_type == 2){
		opener.document.getElementById("zipcode1_b").value = a;
		opener.document.getElementById("zipcode2_b").value = b;
		opener.document.getElementById("addr1_b").value    = c;		
		opener.document.getElementById("addr2_b").value='';
		opener.document.getElementById("addr2_b").focus();	

	}else if(zip_type == 3){
		opener.document.getElementById("czipcode1").value = a;
		opener.document.getElementById("czipcode2").value = b;
		opener.document.getElementById("caddr1").value    = c;		
		opener.document.getElementById("caddr2").value='';
		opener.document.getElementById("caddr2").focus();	

	}else if(zip_type == 4){
		opener.document.getElementById("zip_b_1").value = a;
		opener.document.getElementById("zip_b_2").value = b;
		opener.document.getElementById("addr_b_1").value = c;		
		opener.document.getElementById("addr_b_2").value='';
		opener.document.getElementById("addr_b_2").focus();	
		
	}else{
		alert('zip_type 이 없습니다.');	
	}

	if(typeof(window.opener.GetReigonPrice) == "function" || typeof(window.opener.GetReigonPrice) == "object"){	//도서산간 배송비 추가 함수 2014-01-17 이학봉
		window.opener.GetReigonPrice();
	}
	self.close();
}

function isBlank(s)
{
	for (var i = 0; i < s.length; i++)
	{
		var c=s.charAt(i);

		if ((c != ' ') && (c != '\n') && (c != '\t')) return false;
	}
	return true;
}

function check(type)
{
	if(type == 2){
		if(document.z.know.value == 'N'){
		//alert(document.z.sido.value);
			if (isBlank(document.z.sido.value)){
				alert('[시도]를 입력해주세요.');
				document.z.sido.value = '';
				document.z.sido.focus();
				return false;
			}
			var sido_check = document.z.sido.value;
			if (sido_check.match("세종")){
				
			}else{
				if(isBlank(document.z.sigugun.value)){
					alert('[시구군]을 입력해주세요.');
					document.z.sigugun.value = '';
					document.z.sigugun.focus();
					return false;
				}
			}
			
			if(isBlank(document.z.dong.value)){
				alert('[읍면동]을 입력해주세요.');
				document.z.dong.value = '';
				document.z.dong.focus();
				return false;
			}
		}else{
			if (isBlank(document.z.sido.value)){
				alert('[시도]를 입력해주세요.');
				document.z.sido.value = '';
				document.z.sido.focus();
				return false;
			}
			var sido_check = document.z.sido.value;
			if (sido_check.match("세종")){
				
			}else{
				if(isBlank(document.z.sigugun.value)){
					alert('[시구군]을 입력해주세요.');
					document.z.sigugun.value = '';
					document.z.sigugun.focus();
					return false;
				}
			}
			if(isBlank(document.z.nm_doro.value)){
				alert('[도로명]을 입력해주세요.');
				document.z.nm_doro.value = '';
				document.z.nm_doro.focus();
				return false;
			}
		}
	}else{
		
		if (isBlank(document.z.qstr.value))
		{
			alert('[검색주소]를 입력해주세요.');
			document.z.qstr.value = '';
			document.z.qstr.focus();
			return false;
		}
		else
		{
			if (document.z.qstr.value.length < 2)
			{
				alert('[검색주소]를 2자이상 입력해주세요.');
				document.z.qstr.value = '';
				document.z.qstr.focus();
				return false;
			}
			else
			{
				document.z.submit();
			}
		}
		/*if(isBlank(document.z.bldg1.value) && isBlank(document.z.nm_bldg.value)){
			alert('[건물번호 및 건물명]중 하나이상 입력 필요.');
			
				document.z.bldg1.value = '';
				document.z.bldg1.focus();
				return false;
			
		}	*/
	}
	
}