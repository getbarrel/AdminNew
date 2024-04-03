
    // *********************************************************************


	function validate(frm)
	{
		/*
		if(frm.pass1.value.length < 5 || frm.pass2.value.length < 5){
			alert('패스워드 길이가 작습니다.');
			return false;
		}
		*/
		
		if(frm.pass1.value != frm.pass2.value){
			alert('새로입력된 패스워드가 정확하지 않습니다.');
			return false;
		}

	}
	
	
	function checkPermit(obj){		
		var select_obj = eval("document.all."+obj.id);
		var check00 = select_obj[0].checked;
		
		for(i=0;i<select_obj.length;i++){
			select_obj[i].checked = check00;
		}		
	}
	
	function checkPermit2(obj){		
		var select_obj = eval("document.all."+obj.id);
		var check00 = select_obj[0].checked;
		
		//alert(obj.checked+"::::"+check00);
		if(obj.checked == true || check00){
			for(i=0;i<select_obj.length;i++){
				if(select_obj[i].basic_checked == 'true'){
					select_obj[i].checked = true;
				}
			}
		}

		$("."+$(obj).attr('class')).each(function () {
			if($(obj).attr('checked')){
				$(this).attr('checked',true);
			}else{
				$(this).attr('checked',false);
			}
		});
   
		
	}

	function domainKey(frm){
		if(frm.checked == true){
			document.getElementById('mall_domain_key').disabled = false;
			document.getElementById('mall_domain_key2').disabled = true;
		}else{
			document.getElementById('mall_domain_key').disabled = true;
			document.getElementById('mall_domain_key2').disabled = false;
		}
	}