<!--

	function showObj(id)
	{
		//obj = eval(id+".style");
		obj=document.getElementById(id+".style");
		obj.display = "block";

		document.lyrstat.opend.value = id;
	}

	function hideObj(id)
	{
		//obj = eval(id+".style");
		obj=document.getElementById(id+".style");
		obj.display = "none";

		document.lyrstat.opend.value = '';
	}

	function swapObj(id)
	{
		//obj = eval(id+".style");
		obj=document.getElementById(id+".style");
		stats = obj.display;

		if (stats == "none")
		{
			if (document.lyrstat.opend.value)
				hideObj(document.lyrstat.opend.value);

			showObj(id);
		}
		else
		{
			hideObj(id);
		}
	}

	function act(act, code)
	{
		//alert(1);
		if (act == "update")
		{
			var form = eval("document.EDIT_"+code);

			form.action = 'member.act.php?act='+act+'&code='+code;
			form.submit();
		}

		if (act == "delete"){

			//'정말로 삭제하시겠습니까?'
			if(confirm(language_data['common']['G'][language]))
			{
				window.frames["iframe_act"].location.href= 'member.act.php?act='+act+'&code='+code;
				//document.getElementById("act").src= 'member.act.php?act='+act+'&code='+code;
			}
		}
		
		if (act == "mem_talk_delete")
		{
			//'정말로 삭제하시겠습니까?'
			if(confirm(language_data['common']['G'][language]))
			{
				window.frames["iframe_act"].location.href= 'member.act.php?act='+act+'&ta_ix='+code;
				//document.getElementById("act").src= 'member.act.php?act='+act+'&ta_ix='+code;
			}
		}
	}

    // 입력내용 체크 *************************************************************
	function isBlank(s)
	{
		for (var i = 0; i < s.length; i++)
		{
			var c=s.charAt(i);

			if ((c != ' ') && (c != '\n') && (c != '\t')) return false;
		}
		return true;
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

	function isNum(s, str)
	{
		var OKstr = "0123456789";

		for (i = 0; i < s.value.length; i++)
		{
			var c = s.value.charAt(i);

			if (OKstr.indexOf(c) == -1)
			{
				alert('['+str+'] 값은 숫자만 가능합니다');
				s.value = '';
				s.focus();
				return false; break;
			}
		}
		return true;
	}

	function regCheck(code)
	{
		var form = eval("document.EDIT_"+code);

		var chk = 0;

		for (var i = 0; i <= 5; i++)
		{
			chk = chk + ((i%8+2) * parseInt(form.jumin1.value.substring(i,i+1)));
		}
		for (var i = 6; i <= 11 ; i++)
		{
			chk = chk + ((i%8+2) * parseInt(form.jumin2.value.substring(i-6,i-5)));
		}

		chk = (11 - (chk % 11)) % 10;

		if (chk != form.jumin2.value.substring(6,7))
		{
			alert('유효하지 않은 [주민번호] 입니다.');
			form.jumin1.value = '';
			form.jumin2.value = '';
			form.jumin1.focus();

			return false;
		}
		return true;
	}

	function idCheck()
	{
		var id = document.form.id;

		if (!isAlNum(id,'아이디'))
		{
			return false;
		}
		else if (id.value.length > 20 || id.value.length < 4)
		{
			alert('[아이디]는 4자이상 20자이하만 가능합니다.');
			document.form.id.value = '';
			document.form.id.focus();

			return false;
		}
		else
		{
			window.frames["act"].location.href = 'regist.act.php?act=idcheck&id='+id.value;
			//document.getElementById("act").src = 'regist.act.php?act=idcheck&id='+id.value;
		}
	}

	function check(element, string)
	{
		if (isBlank(element.value))
		{
			alert('['+string+']을 입력해주세요.');
			element.value = '';
			element.focus();
			return false;
		}
		return true;
	}

    // *********************************************************************


	function validate(code)
	{
		var form = eval("document.EDIT_"+code);
		if (
		//     check(form.id,'아이디') &&
		     check(form.name,'이름') &&
			 //check(form.jumin1,'주민번호 앞자리') &&
			 //check(form.jumin2,'주민번호 뒷자리') &&
			// regCheck(code) &&
			 check(form.mail,'이메일') &&
			 check(form.zip1,'우편번호') &&
			 check(form.zip2,'우편번호') &&
			 check(form.addr1,'집주소') && 
			 check(form.addr2,'세부주소') 
		   )
		{
			/*
			&&
			 check(form.tel2,'자택전화') &&
			 check(form.tel3,'자택전화') &&
			 isNum(form.tel2,'자택전화') &&
			 isNum(form.tel3,'자택전화') &&			 
			 check(form.comp,'회사명')			 
			 */
			 
			if (form.pass.value != form.again.value)
			{
				alert('[비밀번호]와 [비번확인]이 일치하지 않습니다.');
				
				form.pass.value  = '';
				form.again.value = '';
				form.pass.focus();
			}
//			else if (form.id_flag.value == '0')
//			{
//				alert('아이디 [중복확인]을 먼저 해주세요.');
//			}
			else
			{
				act('update', code);
			}
		}
	}


function init_date2(FromDate,ToDate,FromDate2,ToDate2,ToDate3) {
	var frm = document.searchmember;

	for(i=0; i<frm.FromYY2.length; i++) {
		if(frm.FromYY2.options[i].value == FromDate.substring(0,4))
			frm.FromYY2.options[i].selected=true
	}
	for(i=0; i<frm.FromMM2.length; i++) {
		if(frm.FromMM2.options[i].value == FromDate.substring(5,7))
			frm.FromMM2.options[i].selected=true
	}
	for(i=0; i<frm.FromDD2.length; i++) {
		if(frm.FromDD2.options[i].value == FromDate.substring(8,10))
			frm.FromDD2.options[i].selected=true
	}
	
	for(i=0; i<frm.ToYY2.length; i++) {
		if(frm.ToYY2.options[i].value == ToDate.substring(0,4))
			frm.ToYY2.options[i].selected=true
	}
	for(i=0; i<frm.ToMM2.length; i++) {
		if(frm.ToMM2.options[i].value == ToDate.substring(5,7))
			frm.ToMM2.options[i].selected=true
	}
	for(i=0; i<frm.ToDD2.length; i++) {
		if(frm.ToDD2.options[i].value == ToDate.substring(8,10))
			frm.ToDD2.options[i].selected=true
	}
}

function init_date(FromDate,ToDate,FromDate2,ToDate2,ToDate3) {
	var frm = document.searchmember;

	for(i=0; i<frm.FromYY.length; i++) {
		if(frm.FromYY.options[i].value == FromDate.substring(0,4))
			frm.FromYY.options[i].selected=true
	}
	for(i=0; i<frm.FromMM.length; i++) {
		if(frm.FromMM.options[i].value == FromDate.substring(5,7))
			frm.FromMM.options[i].selected=true
	}
	for(i=0; i<frm.FromDD.length; i++) {
		if(frm.FromDD.options[i].value == FromDate.substring(8,10))
			frm.FromDD.options[i].selected=true
	}
	
	
	for(i=0; i<frm.ToYY.length; i++) {
		if(frm.ToYY.options[i].value == ToDate.substring(0,4))
			frm.ToYY.options[i].selected=true
	}
	for(i=0; i<frm.ToMM.length; i++) {
		if(frm.ToMM.options[i].value == ToDate.substring(5,7))
			frm.ToMM.options[i].selected=true
	}
	for(i=0; i<frm.ToDD.length; i++) {
		if(frm.ToDD.options[i].value == ToDate.substring(8,10))
			frm.ToDD.options[i].selected=true
	}
	

	if(FromDate2) {
		for(i=0; i<frm.vFromYY.length; i++) {
			if(frm.vFromYY.options[i].value == FromDate2.substring(0,4))
				frm.vFromYY.options[i].selected=true
		}
		for(i=0; i<frm.vFromMM.length; i++) {
			if(frm.vFromMM.options[i].value == FromDate2.substring(5,7))
				frm.vFromMM.options[i].selected=true
		}
		for(i=0; i<frm.vFromDD.length; i++) {
			if(frm.vFromDD.options[i].value == FromDate2.substring(8,10))
				frm.vFromDD.options[i].selected=true
		}
		
		
		for(i=0; i<frm.vToYY.length; i++) {
			if(frm.vToYY.options[i].value == ToDate2.substring(0,4))
				frm.vToYY.options[i].selected=true
		}
		for(i=0; i<frm.vToMM.length; i++) {
			if(frm.vToMM.options[i].value == ToDate2.substring(5,7))
				frm.vToMM.options[i].selected=true
		}
		for(i=0; i<frm.vToDD.length; i++) {
			if(frm.vToDD.options[i].value == ToDate2.substring(8,10))
				frm.vToDD.options[i].selected=true
		}
	}

	if(ToDate3) {

		for(i=0; i<frm.birYY.length; i++) {
			if(frm.birYY.options[i].value == ToDate3.substring(0,4))
				frm.birYY.options[i].selected=true
		}
		for(i=0; i<frm.birMM.length; i++) {
			if(frm.birMM.options[i].value == ToDate3.substring(5,7))
				frm.birMM.options[i].selected=true
		}
		for(i=0; i<frm.birDD.length; i++) {
			if(frm.birDD.options[i].value == ToDate3.substring(8,10))
				frm.birDD.options[i].selected=true
		}
	}
	
	
}



function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;
	
	if(dType == 1){
		for(i=0; i<frm.FromYY.length; i++) {
			if(frm.FromYY.options[i].value == FromDate.substring(0,4))
				frm.FromYY.options[i].selected=true
		}
		for(i=0; i<frm.FromMM.length; i++) {
			if(frm.FromMM.options[i].value == FromDate.substring(5,7))
				frm.FromMM.options[i].selected=true
		}
		for(i=0; i<frm.FromDD.length; i++) {
			if(frm.FromDD.options[i].value == FromDate.substring(8,10))
				frm.FromDD.options[i].selected=true
		}
		
		
		for(i=0; i<frm.ToYY.length; i++) {
			if(frm.ToYY.options[i].value == ToDate.substring(0,4))
				frm.ToYY.options[i].selected=true
		}
		for(i=0; i<frm.ToMM.length; i++) {
			if(frm.ToMM.options[i].value == ToDate.substring(5,7))
				frm.ToMM.options[i].selected=true
		}
		for(i=0; i<frm.ToDD.length; i++) {
			if(frm.ToDD.options[i].value == ToDate.substring(8,10))
				frm.ToDD.options[i].selected=true
		}
	}else{
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
	
}

function select_date2(FromDate,ToDate,dType) {
	var frm = document.searchmember;
	
	for(i=0; i<frm.FromYY2.length; i++) {
			if(frm.FromYY2.options[i].value == FromDate.substring(0,4))
				frm.FromYY2.options[i].selected=true
		}
		for(i=0; i<frm.FromMM2.length; i++) {
			if(frm.FromMM2.options[i].value == FromDate.substring(5,7))
				frm.FromMM2.options[i].selected=true
		}
		for(i=0; i<frm.FromDD2.length; i++) {
			if(frm.FromDD2.options[i].value == FromDate.substring(8,10))
				frm.FromDD2.options[i].selected=true
		}
		
		
		for(i=0; i<frm.ToYY2.length; i++) {
			if(frm.ToYY2.options[i].value == ToDate.substring(0,4))
				frm.ToYY2.options[i].selected=true
		}
		for(i=0; i<frm.ToMM2.length; i++) {
			if(frm.ToMM2.options[i].value == ToDate.substring(5,7))
				frm.ToMM2.options[i].selected=true
		}
		for(i=0; i<frm.ToDD2.length; i++) {
			if(frm.ToDD2.options[i].value == ToDate.substring(8,10))
				frm.ToDD2.options[i].selected=true
		}
}


function onLoad(FromDate, ToDate,FromDate2, ToDate2,ToDate3) {
	var frm = document.searchmember;
	
	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
	if(FromDate2) {
		LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate2);
		LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate2);
	}
	if(ToDate3) {
		LoadValues(frm.birYY, frm.birMM, frm.birDD, ToDate3);
	}

	init_date(FromDate,ToDate,FromDate2, ToDate2,ToDate3);
	
}

function onLoad2(FromDate, ToDate,FromDate2, ToDate2,ToDate3) {
	var frm = document.searchmember;
	
	LoadValues(frm.FromYY2, frm.FromMM2, frm.FromDD2, FromDate);
	LoadValues(frm.ToYY2, frm.ToMM2, frm.ToDD2, ToDate);

	init_date2(FromDate,ToDate,FromDate2, ToDate2,ToDate3);
	
}


function listAction(frm){
		
	PoPWindow('../sms.pop.php',450,370,'sendsms');
	frm.action = '../sms.pop.php';
	frm.target = 'sendsms';
	frm.submit();
}



function clearAll(frm){
		for(i=0;i < frm.code.length;i++){
				frm.code[i].checked = false;
		}
}

function checkAll(frm){
       	for(i=0;i < frm.code.length;i++){
				frm.code[i].checked = true;
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
	input_check_num();
}

function zipcode(type) {
	var zip = window.open('zipcode.php?type='+type,'','width=440,height=350,scrollbars=yes,status=no');
}
//-->
