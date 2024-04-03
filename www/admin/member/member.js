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

function validate(code){
	var form = eval("document.EDIT_"+code);
	if (
	//     check(form.id,'아이디') &&
		check(form.name,'이름') &&
		//check(form.jumin1,'주민번호 앞자리') &&
		//check(form.jumin2,'주민번호 뒷자리') &&
		// regCheck(code) &&
		check(form.mail,'이메일')
		//check(form.zip1,'우편번호') &&
		//check(form.zip2,'우편번호') &&
		//check(form.addr1,'집주소') &&
		//check(form.addr2,'세부주소')
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

		if (form.pass.value != form.again.value){
			alert('[비밀번호]와 [비번확인]이 일치하지 않습니다.');

			form.pass.value  = '';
			form.again.value = '';
			form.pass.focus();
		}else{
			act('update', code);
		}
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
/*
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
*/

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

function onLoad(FromDate, ToDate,FromDate2, ToDate2,ToDate3) {
	var frm = document.searchmember;

	LoadValues(frm.FromYY, frm.FromMM, frm.FromDD, FromDate);
	LoadValues(frm.ToYY, frm.ToMM, frm.ToDD, ToDate);
	if(FromDate2) {
		LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate2);
		LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate2);
	}
	if(ToDate3) {
		//LoadValues(frm.birYY, frm.birMM, frm.birDD, ToDate3);
	}

	init_date(FromDate,ToDate,FromDate2, ToDate2,ToDate3);

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
	//input_check_num();
}

function zipcode(type) {
	var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=500,height=350,scrollbars=yes,status=no');
}

$(function() {
	var join_frm=document.EDIT_;
	$('#user_id').keyup(function(){
		var PT_idtype =/^[a-zA-Z]{1}[a-zA-Z0-9_]+$/;
		//var PT_idtype =/^[a-z0-9_-]{4,12}$/;
		if(join_frm.id.value.length < 4 || join_frm.id.value.length > 16 ){
			$("#idCheckText").css("color","#FF5A00").html('* 아이디는 4~16자리의 영문, 숫자와 특수기호(_)만 사용가능.');
			join_frm.id.focus();
			return false;
		}
		if(join_frm.id.value != ""){
			if(!PT_idtype.test(join_frm.id.value)){
				$("#idCheckText").css("color","#FF5A00").html('* 아이디는 4~16자리의 영문, 숫자와 특수기호(_)만 사용가능.');
				join_frm.id.focus();
				return false;
			}
			$.ajax({
				url: 'member.act.php',
				type: 'get',
				dataType: "html",
				data: ({act: "idcheck",
						id: $('#user_id').val()
				}),
				success: function(result){
				//alert(result);
					//alert(join_frm.id.value);
					if(result == "Y"){
						$("#idCheckText").css("color","#00B050").html("* 사용가능한 아이디 입니다.");
						$('#id_flag').val('Y');
						$('#id_check_value').val(join_frm.id.value);//kbk
						$('#user_id').attr("dup_check","true");//kbk
					}else if(result == "X"){
						$("#idCheckText").css("color","#FF5A00").html("* 가입불가 ID입니다. 다른 ID로 입력해주시기 바랍니다.");
						$('#id_flag').val("");
						$('#id_check_value').val("");//kbk
						$('#user_id').attr("dup_check","false");//kbk
						return false;
					}else if(result=="N"){
						$("#idCheckText").css("color","#FF5A00").html("* 사용할수 없는 아이디 입니다.");
						$('#id_flag').val("");
						$('#id_check_value').val("");//kbk
						$('#user_id').attr("dup_check","false");//kbk
						return false;
					} else {
						//alert(result);
						return false;
					}
				}

			});
		}else{
			join_frm.id.focus();
			$("#idCheckText").html("* 아이디가 비어있습니다.");
			return false;
		}
	});
});

function select_mem_type(){

	var form = document.EDIT_;
	var sel = form.mem_type.value;

	if(sel == 'M'){
		document.location.href = './member_info.php?act=insert&mem_type=M';
	}else if(sel == 'C'){
		document.location.href = './member_info.php?act=insert&mem_type=C';
	}else if(sel == 'F'){
		document.location.href = './member_info.php?act=insert&mem_type=F';
	}else if(sel == 'S'){
		document.location.href = './member_info.php?act=insert&mem_type=S';
	}else{
		document.location.href = './member_info.php?act=insert&mem_type=C';
	}
}

function submit_refund(fm){
	if(CheckFormValue(fm)) {
		if(fm.mode.value=="insert") var con_text="계좌를 추가하시겠습니까?";
		else var con_text="계좌를 수정하시겠습니까?";
		if(confirm(con_text)) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function edit_bank(bank_ix,bank_code,bank_owner,bank_number,use_yn) {
	var fm=document.bank_info_form;
	fm.mode.value="update";
	fm.bank_ix.value=bank_ix;
	fm.bank_code.value=bank_code;
	var o_idx=fm.bank_code.selectedIndex;
	fm.bank_owner.value=bank_owner;
	fm.bank_number.value=bank_number;
	if(use_yn=="Y") {
		$("input[name=use_yn]").eq(0).attr("checked",true);
	} else {
		$("input[name=use_yn]").eq(1).attr("checked",true);
	}
}

function del_bank(bank_ix,code){

	if(confirm("해당 계좌를 삭제하시겠습니까?")) {
		window.frames["iframe_act"].location.href="/mypage/refund_account.act.php?mode=delete&bank_ix="+bank_ix+"&ucode="+code+"&admin_type=Y";
	}
}
//-->
$(document).ready(function (){

    $('input[name=mult_search_use]').click(function (){
        var value = $(this).attr('checked');

        if(value == 'checked'){
            $('#search_text_input_div').css('display','none');
            $('#search_text_area_div').css('display','');

            $('#search_text_input_div').find("input").attr('disabled',true);
            $('#search_text_area_div').find("input").attr('disabled',false);

            $('#search_text_area').attr('disabled',false);
            $('#search_texts').attr('disabled',true);


        }else{
            $('#search_text_input_div').css('display','');
            $('#search_text_area_div').css('display','none');

            $('#search_text_area').attr('disabled',true);
            $('#search_texts').attr('disabled',false);

            $('#search_text_input_div').find("input").attr('disabled',false);
            $('#search_text_area_div').find("input").attr('disabled',true);

        }
    });

    var mult_search_use = $('input[name=mult_search_use]:checked').val();

    if(mult_search_use == '1'){
        $('#search_text_input_div').css('display','none');
        $('#search_text_area_div').css('display','');

        $('#search_text_area').attr('disabled',false);
        $('#search_texts').attr('disabled',true);

    }else{
        $('#search_text_input_div').css('display','');
        $('#search_text_area_div').css('display','none');

        $('#search_text_area').attr('disabled',true);
        $('#search_texts').attr('disabled',false);

    }



});