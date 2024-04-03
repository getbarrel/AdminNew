function processForm(e) {
	if (e.preventDefault) e.preventDefault();
	return false;
}

// �ּҰ˻�
function search(){
	var index = document.getElementById('IndexWord').value;
	if(index.length != 0) {
			document.getElementById('PageNum').value = 1;
			document.getElementById('zipcode_form').submit();
	}else {
		alert('�˻�� �Է��Ͽ� �ּ���');
		return false;
	}
}

// �������þ�� �˻�
function suggest_search(indexWord){
	document.getElementById('IndexWord').value = indexWord;
	document.getElementById('PageNum').value = 1;
	document.getElementById('zipcode_form').submit();
}

// ����������
function nextPage(){
	currentPage = document.getElementById('current_page').innerText;
	totalPage = document.getElementById('end_page').innerText;
	
	if (currentPage < totalPage)
	{
		document.getElementById('PageNum').value = currentPage*1 + 1;				
		document.getElementById('zipcode_form').submit();
	}
}

// ����������
function prevPage(){
	currentPage = document.getElementById('current_page').innerText;

	if(currentPage  > 1){
		document.getElementById('PageNum').value = currentPage*1 -1;			
		document.getElementById('zipcode_form').submit();
	}
}

// ���ּ� �Է��� ȣ��
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

// �θ� �������� �����ȣ, �ּ� ����	
function inputAddr(zip_type,obj_id){
	var roadAddr1;
	var roadAddr2;
	// ���θ��ּ� ���ý�.
	if(document.getElementById('road').checked){

		zipcode = document.getElementById('road_sectionNum').value;
		//zipcode = document.getElementById('road_zipcode').value; �����ȣ �ټ��ڸ��� �ٲ�
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
		//zipcode = document.getElementById('jibun_zipcode').value; �����ȣ �ټ��ڸ��� �ٲ�
		roadAddr1 = document.getElementById('jibunAddr').value;
		detail = document.getElementById('jibun_detail').value;
		//zipcode = zipcode.split( '-' );
		new_zipcode = document.getElementById('jibun_sectionNum').value;
		
		addrResult = detail;
		
		
	}

	if(zip_type == 1){
		opener.document.getElementById("zipcode1").value = zipcode;
	//	opener.document.getElementById("new_zipcode").value = new_zipcode; //5�ڸ� �����ȣ 
		
	//	opener.document.getElementById("zipcode1").value = zipcode[0];
	//	opener.document.getElementById("zipcode2").value = zipcode[1];
		opener.document.getElementById("addr1").value = roadAddr1;		
		opener.document.getElementById("addr2").value=addrResult;
	//	opener.document.getElementById("addr2").focus();	
		$('#zipcode2' , opener.document).val('');

	}else if(zip_type == 2){
		opener.document.getElementById("zipcode1_b").value = zipcode;
	//	opener.document.getElementById("new_zipcode").value = new_zipcode; //5�ڸ� �����ȣ 

	//	opener.document.getElementById("zipcode1_b").value = zipcode[0];
	//	opener.document.getElementById("zipcode2_b").value = zipcode[1];
		opener.document.getElementById("addr1_b").value    = roadAddr1;		
		opener.document.getElementById("addr2_b").value=addrResult;
		//opener.document.getElementById("addr2_b").focus();	
		$('#zipcode2_b' , opener.document).val('');

	}else if(zip_type == 3){
		opener.document.getElementById("czipcode1").value = zipcode;
	//	opener.document.getElementById("new_zipcode").value = new_zipcode; //5�ڸ� �����ȣ 
		
	//	opener.document.getElementById("czipcode1").value = zipcode[0];
	//	opener.document.getElementById("czipcode2").value = zipcode[1];
		opener.document.getElementById("caddr1").value = roadAddr1;		
		opener.document.getElementById("caddr2").value=addrResult;
		//opener.document.getElementById("caddr2").focus();	
		$('#czipcode2' , opener.document).val('');

	}else if(zip_type == 4){
		opener.document.getElementById("zip_b_1").value = zipcode;
	//	opener.document.getElementById("new_zipcode").value = new_zipcode; //5�ڸ� �����ȣ 

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
		alert('zip_type �� �����ϴ�.2');	
	}
/*
		if (roadAddrDetail != ''){
			addrResult = roadAddr1 +", "+ roadAddrDetail +" "+roadAddr2;
		} else {
			addrResult = roadAddr1 +" "+roadAddr2;
		}
*/
	// �����ּ� ���ý�.
	if(typeof(window.opener.GetReigonPrice) == "function" || typeof(window.opener.GetReigonPrice) == "object"){	//�����갣 ��ۺ� �߰� �Լ� 2014-01-17 ���к�
		
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
		alert('zip_type �� �����ϴ�.');	
	}

	if(typeof(window.opener.GetReigonPrice) == "function" || typeof(window.opener.GetReigonPrice) == "object"){	//�����갣 ��ۺ� �߰� �Լ� 2014-01-17 ���к�
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
				alert('[�õ�]�� �Է����ּ���.');
				document.z.sido.value = '';
				document.z.sido.focus();
				return false;
			}
			var sido_check = document.z.sido.value;
			if (sido_check.match("����")){
				
			}else{
				if(isBlank(document.z.sigugun.value)){
					alert('[�ñ���]�� �Է����ּ���.');
					document.z.sigugun.value = '';
					document.z.sigugun.focus();
					return false;
				}
			}
			
			if(isBlank(document.z.dong.value)){
				alert('[���鵿]�� �Է����ּ���.');
				document.z.dong.value = '';
				document.z.dong.focus();
				return false;
			}
		}else{
			if (isBlank(document.z.sido.value)){
				alert('[�õ�]�� �Է����ּ���.');
				document.z.sido.value = '';
				document.z.sido.focus();
				return false;
			}
			var sido_check = document.z.sido.value;
			if (sido_check.match("����")){
				
			}else{
				if(isBlank(document.z.sigugun.value)){
					alert('[�ñ���]�� �Է����ּ���.');
					document.z.sigugun.value = '';
					document.z.sigugun.focus();
					return false;
				}
			}
			if(isBlank(document.z.nm_doro.value)){
				alert('[���θ�]�� �Է����ּ���.');
				document.z.nm_doro.value = '';
				document.z.nm_doro.focus();
				return false;
			}
		}
	}else{
		
		if (isBlank(document.z.qstr.value))
		{
			alert('[�˻��ּ�]�� �Է����ּ���.');
			document.z.qstr.value = '';
			document.z.qstr.focus();
			return false;
		}
		else
		{
			if (document.z.qstr.value.length < 2)
			{
				alert('[�˻��ּ�]�� 2���̻� �Է����ּ���.');
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
			alert('[�ǹ���ȣ �� �ǹ���]�� �ϳ��̻� �Է� �ʿ�.');
			
				document.z.bldg1.value = '';
				document.z.bldg1.focus();
				return false;
			
		}	*/
	}
	
}