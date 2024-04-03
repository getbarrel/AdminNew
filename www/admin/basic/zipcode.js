
	function zipcode(a,b,c,d,type){
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
		//È£È¯¼º 2011-04-12 kbk
		if(type == "1"){
			//opener.document.getElementById("zip1").value = a;
			//opener.document.getElementById("zip2").value = b;
			opener.document.getElementById("zip").value = a+'-'+b;
			opener.document.getElementById("addr1").value    = c;		
			opener.document.getElementById("addr2").focus();
			self.close();
		}else if(type == "3"){
			opener.document.getElementById("com_zip1").value = a;
			opener.document.getElementById("com_zip2").value = b;
			opener.document.getElementById("com_addr1").value    = c;		
			opener.document.getElementById("com_addr2").focus();
			self.close();
		}else if(type == "4"){
			opener.document.getElementById("r_zipcode").value = a+'-'+b;
			opener.document.getElementById("r_addr1").value    = c;		
			opener.document.getElementById("r_addr2").focus();
			self.close();
		}else{
			opener.document.getElementById("zipcode1").value = a;
			opener.document.getElementById("zipcode2").value = b;
			opener.document.getElementById("addr").value    = c;		
			opener.document.getElementById("addr").focus();	
			self.close();
		}
	}

