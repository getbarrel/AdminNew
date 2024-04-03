
	function zipcode(a,b,c,d,type,obj_id,e){
		/*var obj = opener.document.all;
		if(type == "1"){
			obj.zip1.value = a;
			obj.zip2.value = b;
			obj.addr1.value    = c;		
			obj.addr2.focus();	
			self.close();
		}else{
			obj.zipcode1.value = a;
			obj.zipcode2.value = b;
			obj.addr.value    = c;		
			obj.addr.focus();	
			self.close();
		}*/
		//호환성 2011-04-12 kbk

		if(type == "1"){
			/*opener.document.getElementById("zip1").value = a;
			opener.document.getElementById("zip2").value = b;
			opener.document.getElementById("addr1").value    = c;
			opener.document.getElementById("addr2").focus();*/
			if(e != 'Y'){
				opener.document.getElementById("zip1").value = a;
				opener.document.getElementById("zip2").value = b;
				opener.document.getElementById("addr1").value    = e;		
				opener.document.getElementById("addr2").value='';
				opener.document.getElementById("doro_addr").value=c + d;
				opener.document.getElementById("doro_addr1").value=c;
				opener.document.getElementById("doro_addr2").value=d;
				opener.document.getElementById("addr2").focus();	
			}else{
				opener.document.getElementById("zip1").value = a;
				opener.document.getElementById("zip2").value = b;
				opener.document.getElementById("addr1").value    = c;		
				opener.document.getElementById("addr2").value='';
				opener.document.getElementById("doro_addr").value='';
				opener.document.getElementById("doro_addr1").value='';
				opener.document.getElementById("doro_addr2").value='';
				opener.document.getElementById("addr2").focus();	
			}
		}else if(type == "3"){
			opener.document.getElementById("com_zip1").value = a;
			opener.document.getElementById("com_zip2").value = b;
			opener.document.getElementById("com_addr1").value = c;
			opener.document.getElementById("com_addr2").focus();
		}else if(type == "4"){
			opener.document.getElementById("com_zip").value = a+'-'+b;
			opener.document.getElementById("com_addr1").value = c;
			opener.document.getElementById("com_addr2").focus();
		}else if(type == "5"){
			opener.document.getElementById("return_zip1").value = a;
			opener.document.getElementById("return_zip2").value = b;
			opener.document.getElementById("return_addr1").value    = c;
			opener.document.getElementById("return_addr2").focus();
		}else if(type == "6"){	//기초정보 사원등록 사용
			$('#zip', opener.document).val(a+'-'+b);
			$('#addr1', opener.document).val(c);
			$('#addr2', opener.document).focus();
		}else if(type == "7"){	//기초정보 사원등록 사용
			$('#r_zipcode', opener.document).val(a+'-'+b);
			$('#r_addr1', opener.document).val(c);
			$('#r_addr2', opener.document).focus();

		}else if(type == "9"){
			$('#'+obj_id , opener.document).find('#zipcode1').val(a);
			$('#'+obj_id , opener.document).find('#zipcode2').val(b);
			$('#'+obj_id , opener.document).find('#addr1').val(c);
			$('#'+obj_id , opener.document).find('#addr2').focus();
		}else{
			opener.document.getElementById("zipcode1").value = a;
			opener.document.getElementById("zipcode2").value = b;
			opener.document.getElementById("addr1").value    = c;		
			opener.document.getElementById("addr2").focus();	
		}

		self.close();
	}

