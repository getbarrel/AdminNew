<!--

	function zipcode(id)
	{
		var zip = window.open('zipcode.php?obj='+id,'','width=440,height=200,scrollbars=yes,status=no');
	}

	function showObj(id)
	{
		obj = eval(id+".style");
		obj.display = "block";

		document.lyrstat.opend.value = id;
	}

	function hideObj(id)
	{
		obj = eval(id+".style");
		obj.display = "none";

		document.lyrstat.opend.value = '';
	}

	function swapObj(id)
	{
		obj = eval(id+".style");
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

	function act(act, oid)
	{
		
		if (act == "update")
		{
			var form = eval("document.EDIT_"+oid);

			form.action = 'orders.act.php?act='+act+'&oid='+oid;
			form.submit();
		}

		if (act == "delete")
		{
			if(confirm('정말로 삭제하시겠습니까?'))
			{
				document.frames("act").location.href= 'orders.act.php?act='+act+'&oid='+oid;
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


	function validate(oid)
	{
		var form = eval("document.EDIT_"+oid);

		if (
		     check(form.bname,'주문자이름') &&
			 check(form.rname,'수취인이름') &&
			 check(form.bmail,'주문자메일') &&
			 check(form.rmail,'수취인메일') &&
			 check(form.zipcode1,'우편번호') &&
			 check(form.zipcode2,'우편번호') &&
			 check(form.addr,'배달주소') && 
			 check(form.btel,'주문자전화') &&
			 check(form.rtel,'수취인전화')
		   )
		{
			
			if (form.stats.value == 2 && form.quick.value == 0){
				alert('택배사를 선택해 주세요');
				return false;	
			}else if (form.stats.value == 2 && form.deliverycode.value.length < 1){
				alert('송장 번호를 입력해주세요');
				form.deliverycode.focus();
				return false;	
			}
			act('update', oid);
		}
	}


//-->
