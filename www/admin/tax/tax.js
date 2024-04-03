	$(document).ready(function (){
		$('#sdate').datepicker({
			//changeMonth: true,
			//changeYear: true,
			monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',
		
			onSelect: function(dateText, inst){
				if($('#edate').val() != '' && $('#edate').val() <= dateText){
					$('#edate').val(dateText);
				}else{
					$('#edate').datepicker('setDate','+0d');
				}
			}
		});

		$('#edate').datepicker({
			//changeMonth: true,
			//changeYear: true,
			monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			showMonthAfterYear:true,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			buttonText: '달력',
			onSelect: function(dateText, inst){
			}
		
		});
	});
	//-->
	
	/* 프린트 */
	function this_print(kind, idx)
	{
		if(kind == 1)	var s_url = "print_sales.php";
		else			var s_url = "print_sales.php";
		
		window.open(s_url + "?idx="+idx,"print","width=800,height=800");
	}

	/* 메일재전송 */
	function re_email(email, company, name, idx)
	{
		window.open("./email_re_send.php?email="+email+"&company="+company+"&name="+name+"&idx="+idx,"email_pop","width=450,height=300");
	}

	/* 문서 이력확인 */
	function GetLogs(company, idx)
	{
		window.open("./popbill_getlog.php?idx="+idx,"getlogs","width=550,height=300");
	}

	/* SMS재전송 */
	function re_sms(mobile, company, name)
	{
		window.open("./sms.pop.php?mobile="+mobile+"&company="+company+"&name="+name,"email_pop","width=450,height=500");
	}
	
	function period_input(s,e)
	{
		$("#dateS").val(e);
		$("#dateE").val(s);
	}

	/* 리사이즈 */
	function resize() 
	{ 
		//
		var g_fIsSP2 = false;
		g_fIsSP2 = (window.navigator.userAgent.indexOf("SV1") != -1);
		
		//
		var oBody = document.body;
		var p_height, p_width;   
		
		p_width  = oBody.scrollWidth + (oBody.offsetWidth-oBody.clientWidth)+8;
		p_height =  oBody.scrollHeight + (oBody.offsetHeight-oBody.clientHeight)+8;


		if(g_fIsSP2){     
		// XP SP2 브라우저임..
			self.resizeTo(p_width - 2, p_height + 50); 
		}else{
		//XP SP2 브라우저가 아님.
			self.resizeTo(p_width - 2, p_height + 80); 
		}
	} 
	
	function show_sendview()
	{
		var vv = $(':input:radio[id=send_type]:checked').val();
		if(vv == '1'){ $('#send_view').html('수동전송(원하는 시기에 직접전송) : [국세청 전송설정]메뉴에서 설정된 값'); }
		if(vv == '2'){ $('#send_view').html('공급받는자가 세금계산서를 승인처리하는 즉시 국세청에 신고됩니다.'); }
		if(vv == '3'){ $('#send_view').html('공급받는자의 의사와 관계없이 발생하는 즉시 국세청에 신고됩니다.'); }
	}
	
	function set_comma(n)
	{
/*		var reg = /(^[+-]?\d+)(\d{3})/;
		n += '';
		while(reg.test(n))
		n = n.replace(reg,'$1'+','+'$2');
		
		return n;

		var str = n.value;
		var Re = /[^0-9]/g;
		var ReN = /(-?[0-9]+)([0-9]{3})/;
		str = str.replace(Re,'');              
		while (ReN.test(str)) { 
			str = str.replace(ReN, '$1'+','+'$2'); 
		}
		n.value = str;
*/
	}

	function num_chk(num)
	{
		if(isNaN(num.value))
		{
			alert('숫자만 입력하세요');
			num.value = '';
			num.focus();
			return;
		}
	}

	function del_row(del_cnt)
	{
		$('#Detail'+del_cnt).remove();
	}

	function search_company(kind)
	{
		window.open('./search_company_pop.php?kind='+kind,'company','width=750,height=300');
	}

	function writeType_click(kind)
	{
		var chk_row = add_cnt;
		var write_type = $('input:radio[name=\'WriteType\']:checked').val();	// 작성방법

		for(i=1; i <= chk_row; i++)
		{
			
			if(kind == undefined)
			{
				$('input[name=\'cnt['+i+']\']').attr('class','tb').removeAttr('readonly').val('');
				$('input[name=\'price['+i+']\']').attr('class','tb').removeAttr('readonly').val('');
				$('input[name=\'tax['+i+']\']').attr('class','tb').removeAttr('readonly').val('');
				$('input[name=\'p_price['+i+']\']').attr('class','tb').removeAttr('readonly').val('');
				$('#supply_price').val('');
				$('#tax_price').val('');
				$('#input_total').attr('class','tb_readonly').attr('readonly',true).val('');
				$('#total_price').val('');
			}
			else
			{
				$('input[name=\'cnt['+i+']\']').attr('class','tb').removeAttr('readonly');
				$('input[name=\'price['+i+']\']').attr('class','tb').removeAttr('readonly');
				$('input[name=\'tax['+i+']\']').attr('class','tb').removeAttr('readonly');
				$('input[name=\'p_price['+i+']\']').attr('class','tb').removeAttr('readonly');
				$('#input_total').attr('class','tb_readonly').attr('readonly',true);
			}

			if(write_type == '2')
			{
				$('input[name=\'p_price['+i+']\']').attr('class','tb_readonly').attr('readonly',true);
				$('input[name=\'tax['+i+']\']').attr('class','tb_readonly').attr('readonly',true);
			}
			if(write_type == '3')
			{
				$('input[name=\'cnt['+i+']\']').attr('class','tb_readonly').attr('readonly',true);
				$('input[name=\'price['+i+']\']').attr('class','tb_readonly').attr('readonly',true);
				$('input[name=\'tax['+i+']\']').attr('class','tb_readonly').attr('readonly',true);
			}
			if(write_type == '4')
			{
				$('input[name=\'cnt['+i+']\']').attr('class','tb_readonly').attr('readonly',true);
				$('input[name=\'price['+i+']\']').attr('class','tb_readonly').attr('readonly',true);
				$('input[name=\'tax['+i+']\']').attr('class','tb_readonly').attr('readonly',true);
				$('input[name=\'p_price['+i+']\']').attr('class','tb_readonly').attr('readonly',true);
				$('#input_total').attr('class','tb').removeAttr('readonly');
			}
		}
	}

	function calculator_p()
	{
		var chk_row = add_cnt;
		tax_per = 0;

		tax_per = $('input:radio[name=\'tax_per\']:checked').val();			// 과세형태
		write_type = $('input:radio[name=\'WriteType\']:checked').val();	// 작성방법
		
		for(i=1; i <= chk_row; i++)
		{
			var o_price = $('input[name=\'p_price['+i+']\']').val();
			var q_price = o_price * tax_per;
			var oo_price = oo_price + o_price;
		}
	}

	tpi = 1;
	function totalPrice_input()
	{	
		//alert (tpi +" : "+ add_cnt);
		if(tpi > add_cnt)
		{
			alert ("품목 입력리스트를 추가해주세요.");
			return;
		}
		T_tax = 0;
		T_price = 0;
		T_total = 0;

		var chk_row = add_cnt;
		var tax_per = 1 / 1.1;
		var input_total = $('#input_total').val();
			
		input_total = (input_total)?parseInt(input_total):0;
		var tt_output = Math.floor(input_total);
		var tt2_output = Math.floor(input_total * tax_per);
		
		var tax_v = tt_output - tt2_output;
		if(tt_output - tt2_output != 0)	$('input[name=\'tax['+tpi+']\']').val(tax_v);
		if(tt2_output != 0)				$('input[name=\'p_price['+tpi+']\']').val(tt2_output);

		for(i=1; i <= tpi; i++)
		{
			T_tax = T_tax + tax_v;
			T_price = T_price + tt2_output;
			T_total = T_total + tt_output;
		}
		
		T_tax = (T_tax)?parseInt(T_tax):0;
		T_price = (T_price)?parseInt(T_price):0;
		if(T_tax != 0) $('#tax_price').val(T_tax);
		if(T_price != 0) $('#supply_price').val(T_price);
		$('#total_price').val(T_total);

		tpi++;
		//calculator_ing();
	}
	
	tt_ii = 1;
	function calculator_ing()
	{
		var chk_row = add_cnt;

		tax_per = 0;
		output = 0;
		output_2 = 0;
		t_output = 0;
		t_output_2 = 0;
		cnt = 0;
		price = 0;
		p_price = 0;
		p_tax = 0;

		tax_per = $('input:radio[name=\'tax_per\']:checked').val();			// 과세형태
		write_type = $('input:radio[name=\'WriteType\']:checked').val();	// 작성방법

		if(tax_per == 1)
		{
			if(write_type == 4) tax_per = 1 / 1.1;
			else				tax_per = 0.1;
		}
		if(tax_per == 2)	tax_per = 0;
		tax_per = (tax_per)? tax_per:0;

		if(write_type == '4')
		{
			if(tt_ii > add_cnt)
			{
				alert ("품목 입력리스트를 추가해주세요.");
				return;
			}

			var input_total = $('#input_total').val();
			
			input_total = (input_total)?parseInt(input_total):0;
			var tt_output = Math.floor(input_total);
			var tt2_output = Math.floor(input_total * tax_per);
			
			var tax_v = tt_output - tt2_output;
			if(tt_output - tt2_output != 0)	$('input[name=\'tax['+tt_ii+']\']').val(tax_v);
			if(tt2_output != 0)				$('input[name=\'p_price['+tt_ii+']\']').val(tt2_output);

			if(tax_per != '')
			{
				var tax_v = tt_output - tt2_output;
				if(tt_output - tt2_output != 0)	$('input[name=\'tax['+tt_ii+']\']').val(tax_v);
				if(tt2_output != 0)				$('input[name=\'p_price['+tt_ii+']\']').val(tt2_output);
			}
			else
			{
				if(tt_output > 0)
				{
					$('input[name=\'tax['+tt_ii+']\']').val('0');
					$('input[name=\'p_price['+tt_ii+']\']').val(tt_output);
				}
			}
			if(tt_output > 0) tt_ii++;
		}
		for(i=1; i <= chk_row; i++)
		{
			if(write_type == "1" || write_type == "2")
			{
				cnt = $('input[name=\'cnt['+i+']\']').val();
				cnt = (cnt)? parseInt(cnt):0;
				price = $('input[name=\'price['+i+']\']').val();
				price = (price)? parseInt(price):0;

				output = Math.floor(cnt * price);
				output_2 = Math.floor(output * tax_per);
				t_output = t_output + output;
				t_output_2 = t_output_2 + output_2;

				if(cnt != 0)	$('input[name=\'cnt['+i+']\']').val(cnt);
				if(price != 0)	$('input[name=\'price['+i+']\']').val(price);
				if(output != 0) $('input[name=\'p_price['+i+']\']').val(output);
				if(output_2 != 0) $('input[name=\'tax['+i+']\']').val(output_2);
			}
			else if(write_type == "3")
			{
				p_price =  $('input[name=\'p_price['+i+']\']').val();
				p_price = (p_price)? parseInt(p_price):0;
				output = p_price;
				output_2 = Math.floor(p_price * tax_per);
				t_output = t_output + output;
				t_output_2 = t_output_2 + output_2;

				if(cnt != 0)	$('input[name=\'cnt['+i+']\']').val(cnt);
				if(price != 0)	$('input[name=\'price['+i+']\']').val(price);
				if(output != 0)	$('input[name=\'p_price['+i+']\']').val(output);
				if(output_2 != 0) $('input[name=\'tax['+i+']\']').val(output_2);
			}
			else if(write_type == "4")
			{
				p_tax = $('input[name=\'tax['+i+']\']').val();
				p_price = $('input[name=\'p_price['+i+']\']').val();
				p_tax = (p_tax)?parseInt(p_tax):0;
				p_price = (p_price)?parseInt(p_price):0;
				t_output = t_output + p_price;
				t_output_2 = t_output_2 + p_tax;

				if(tax_per == 0 && p_price > 0)
				{
					$('input[name=\'tax['+i+']\']').val('0');
				}
			}
			
			if(t_output != 0) $('#supply_price').val(t_output);
			if(t_output_2 != 0) $('#tax_price').val(t_output_2);
			if(t_output + t_output_2 != 0) $('#total_price').val(t_output + t_output_2);
		}

		$('#input_total').val("");
		//alert (write_type);
	}
	
	/* 파일첨부 업로드필드 추가 */
	var add_file = 2;
	function add_filearea()
	{
		if(add_file == '4')
		{
			alert ('파일첨부는 3개까지 가능합니다.');
			return;
		}
	//	var add_view = '<span id=\'file'+add_file+'\' style=\'padding:10px 5px 10px 5px\'><input type=\'file\' name=\'DataFile[]\' id=\'DataFile[]\' style=\'width:500px;height:20px\'> <img src=\'./img/add_.gif\' align=\'absbottom\' onclick=\'add_filearea()\' style=\'cursor:hand\'> <img src=\'./img/icon_delete.gif\' align=\'absbottom\' onclick=\'del_filearea('+add_file+')\' style=\'cursor:hand\'></span>';
		var add_view = '<span class=\'file'+add_file+'\' style=\'padding:10px 5px 10px 5px\'><input type=\'file\'  class=\'textbox\' name=\'DataFile[]\' id=\'DataFile[]\' style=\'height:22px; border:1px solid #c5c5c5\'> </span><span class=\'file'+add_file+'\'><img src=\'../images/korea/btn_add_ico.gif\' style=\'cursor:pointer\' align=\'absmiddle\' onclick=\'add_filearea()\'> <img src=\'../images/korea/btn_del.gif\' style=\'cursor:pointer\' align=\'absmiddle\' onclick=\'del_filearea('+add_file+')\'></span><br class=\'file'+add_file+'\' >';
		$('#file_area').append(add_view);

		add_file++;
	}
	
	/* 파일첨부 업로드필드 삭제*/
	function del_filearea(del_no)
	{
		if(add_file == 2)
		{
			return;
		}
		$('.file'+del_no).remove();
		add_file--;
	}

	/* 업로드 파일 삭제 */
	function del_data(idx)
	{
		$("#f_show"+idx).remove();
		PROC.location.href = "./datafile_del.php?idx="+idx;
	}

	/* 메모수정 */
	function memo_modify()
	{
		if($('#memo').val() == "")
		{
			alert ('메모 내용을 입력해주세요.');
			return;
		}

		$('#memo_frm').attr("action","memo_modify_act.php");
		$('#memo_frm').attr("method","post");
		$('#memo_frm').attr("target","PROC");
		$('#memo_frm').submit();
	}
	
	/* 매출,매입,위수탁 정보 등록 */
	function frm_submit(status){
		
		$('#status').val(status);

		if($('#s_company_number').val() == '')
		{
			alert ('공급자 등록번호를 입력해주세요.');
			$('#s_company_number').focus();
			return;
		}
		if($('#s_company_name').val() == '')
		{
			alert ('공급자 상호(업체명)를 입력해주세요.');
			$('#s_company_name').focus();
			return;
		}
		if($('#s_name').val() == '')
		{
			alert ('공급자 성명을 입력해주세요.');
			$('#s_name').focus();
			return;
		}
		if($('#s_address').val() == '')
		{
			alert ('공급자 사업장주소를 입력해주세요.');
			$('#s_address').focus();
			return;
		}
		if($('#s_personin').val() == '')
		{
			alert ('공급자 담당자를 입력해주세요.');
			$('#s_personin').focus();
			return;
		}
		if($('#s_tel').val() == '')
		{
			alert ('공급자 연락처를 입력해주세요.');
			$('#s_tel').focus();
			return;
		}
		if($('#s_email1').val() == '')
		{
			alert ('공급자 이메일 아이디를 입력해주세요.');
			$('#s_email1').focus();
			return;
		}
		if($('#s_email2').val() == '')
		{
			alert ('공급자 이메일 정보를 입력해주세요.');
			$('#s_email2').focus();
			return;
		}

		if($('#r_company_number').val() == '')
		{
			alert ('공급받는자 등록번호를 입력해주세요.');
			$('#r_company_number').focus();
			return;
		}
		if($('#r_company_name').val() == '')
		{
			alert ('공급받는자 상호(업체명)를 입력해주세요.');
			$('#r_company_name').focus();
			return;
		}
		if($('#r_name').val() == '')
		{
			alert ('공급받는자 성명을 입력해주세요.');
			$('#r_name').focus();
			return;
		}
		/*
		if($('#r_address').val() == '')
		{
			alert ('공급받는자 사업장주소를 입력해주세요.');
			$('#r_address').focus();
			return;
		}*/
		if($('#r_personin').val() == '')
		{
			alert ('공급받는자 담당자를 입력해주세요.');
			$('#r_personin').focus();
			return;
		}
		if($('#r_tel').val() == '')
		{
			alert ('공급받는자 연락처를 입력해주세요.');
			$('#r_tel').focus();
			return;
		}
		if($('#r_email1').val() == '')
		{
			alert ('공급받는자 이메일 아이디를 입력해주세요.');
			$('#r_email1').focus();
			return;
		}
		if($('#r_email2').val() == '')
		{
			alert ('공급받는자 이메일 정보를 입력해주세요.');
			$('#r_email2').focus();
			return;
		}
		if($('#total_price').val() == '')
		{
			alert ('품목정보를 정상적으로 작성해 주세요.');
			return;
		}

		$("#frm").submit();
	}
	
	var add_cnt = 4;
	$(document).ready(function(){

		$('#email_com').change(function(){
			$('#s_email2').val($('#email_com').val());
		});

		$('#email_com2').change(function(){
			$('#r_email2').val($('#email_com2').val());
		});

		$('#add_btn').click(function(){
			
			add_cnt++;

			var detail_row = '<tr height=\'35\' id=\'Detail'+add_cnt+'\'>';
			detail_row += '<td colspan=\'1\' align=\'center\' class=\'RBLine\'><input type=\'hidden\' name=\'idx2[]\'><input type=\'text\' class=\'tb\' name=\'t_mon['+add_cnt+']\' id=\'t_mon['+add_cnt+']\' style=\'width:95%;IME-MODE:disabled;text-align:center;\' maxlength=\'2\' valtype=\'NUM\' value=\'\'></td>';
			detail_row += '<td colspan=\'1\' align=\'center\' class=\'RBLine\'><input type=\'text\' class=\'tb\' name=\'t_day['+add_cnt+']\' id=\'t_day['+add_cnt+']\' style=\'width:95%;IME-MODE:disabled;text-align:center;\' maxlength=\'2\' valtype=\'NUM\' value=\'\'></td>';
			detail_row += '<td colspan=\'7\' align=\'center\' class=\'RBLine\' style=\'padding-right:0px;\'><input type=\'text\' class=\'tb\' name=\'product['+add_cnt+']\' id=\'product['+add_cnt+']\' maxlength=\'100\' style=\'width:95%;\' value=\'\'></td>';
			detail_row += '<td colspan=\'3\' align=\'center\' class=\'RBLine\'><input type=\'text\' class=\'tb\' name=\'p_size['+add_cnt+']\' id=\'p_size['+add_cnt+']\' maxlength=\'60\' style=\'width:95%;text-align:right;\' value=\'\'></td>';
			detail_row += '<td colspan=\'3\' align=\'center\' class=\'RBLine\'><input type=\'text\' class=\'tb\' name=\'cnt['+add_cnt+']\' id=\'cnt['+add_cnt+']\' maxlength=\'12\' style=\'width:95%;IME-MODE:disabled;text-align:right;\' valtype=\'NUM\' value=\'\' onkeyup=\'calculator_ing()\'></td>';
			detail_row += '<td colspan=\'3\' align=\'center\' class=\'RBLine\'><input type=\'text\' class=\'tb\' name=\'price['+add_cnt+']\' id=\'price['+add_cnt+']\' maxlength=\'18\' style=\'width:95%;IME-MODE:disabled;text-align:right;\' valtype=\'NUM\' value=\'\' onkeyup=\'calculator_ing()\'></td>';
			detail_row += '<td colspan=\'5\' align=\'center\' class=\'RBLine\'><input type=\'text\' class=\'tb\' name=\'p_price['+add_cnt+']\' id=\'p_price['+add_cnt+']\' maxlength=\'18\' style=\'width:95%;IME-MODE:disabled;text-align:right;\' valtype=\'NUM\' value=\'\' onkeyup=\'calculator_ing()\'></td>';
			detail_row += '<td colspan=\'4\' align=\'center\' class=\'RBLine\'><input type=\'text\' class=\'tb\' name=\'tax['+add_cnt+']\' id=\'tax['+add_cnt+']\' maxlength=\'18\' style=\'width:95%;IME-MODE:disabled;text-align:right;\' valtype=\'NUM\' value=\'\' onkeyup=\'calculator_ing()\'></td>';
			detail_row += '<td colspan=\'6\' align=\'center\' class=\'RBLine\'><input type=\'text\' class=\'tb\' name=\'comment['+add_cnt+']\' id=\'comment['+add_cnt+']\' maxlength=\'100\' style=\'width:95%;\' value=\'\'></td>';
			//detail_row += '<td colspan=\'1\' align=\'center\' class=\'BLine_T\'><img src=\'./img/close.gif\' id=\'DelDetail\' class=\'Buttons\' onclick=\'del_row('+add_cnt+')\'></td>';
			detail_row += '<td colspan=\'1\' align=\'center\' class=\'BLine_T\'><img src=\'../images/korea/btn_del.gif\' onclick=\'del_row('+add_cnt+')\' id=\'DelDetail\' style=\'cursor:pointer\' align=\'absmiddle\'></td>';
			detail_row += '</tr>';
			
			$("tbody[id=taxList]").append(detail_row);
			//$('#DetailBox').append(detail_row);
			
			calculator_ing();
			writeType_click('1');
		});


	});

	/* number_format */
	function number_format (number, decimals, dec_point, thousands_sep) {
    // Formats a number with grouped thousands  
    // 
    // version: 1103.1210
    // discuss at: http://phpjs.org/functions/number_format    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     bugfix by: Michael White (http://getsprink.com)
    // +     bugfix by: Benjamin Lupton
    // +     bugfix by: Allan Jensen (http://www.winternet.no)    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +     bugfix by: Howard Yeend
    // +    revised by: Luke Smith (http://lucassmith.name)
    // +     bugfix by: Diogo Resende
    // +     bugfix by: Rival    // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
    // +   improved by: davook
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +      input by: Jay Klehr
    // +   improved by: Brett Zamir (http://brett-zamir.me)    // +      input by: Amir Habibi (http://www.residence-mixte.com/)
    // +     bugfix by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Theriault
    // +      input by: Amirouche
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)    // *     example 1: number_format(1234.56);
    // *     returns 1: '1,235'
    // *     example 2: number_format(1234.56, 2, ',', ' ');
    // *     returns 2: '1 234,56'
    // *     example 3: number_format(1234.5678, 2, '.', '');    // *     returns 3: '1234.57'
    // *     example 4: number_format(67, 2, ',', '.');
    // *     returns 4: '67,00'
    // *     example 5: number_format(1000);
    // *     returns 5: '1,000'    // *     example 6: number_format(67.311, 2);
    // *     returns 6: '67.31'
    // *     example 7: number_format(1000.55, 1);
    // *     returns 7: '1,000.6'
    // *     example 8: number_format(67000, 5, ',', '.');    // *     returns 8: '67.000,00000'
    // *     example 9: number_format(0.9, 0);
    // *     returns 9: '1'
    // *    example 10: number_format('1.20', 2);
    // *    returns 10: '1.20'    // *    example 11: number_format('1.20', 4);
    // *    returns 11: '1.2000'
    // *    example 12: number_format('1.2000', 3);
    // *    returns 12: '1.200'
    // *    example 13: number_format('1 000,50', 2, '.', ' ');    // *    returns 13: '100 050.00'
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');    }
    return s.join(dec);
}

