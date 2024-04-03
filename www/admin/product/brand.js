
	
	
	function addPCBanner(type){
		var type2 = type.toUpperCase();
		var bannerRow = "</br><ul name='"+type+"BannerRow'>";
				bannerRow += "<li><a href='javascript:void(0)' onclick='removePCBanner($(this))'>삭제</a>";
				bannerRow += "<li><input type='hidden' name='modiBanner"+type2+"[]' value=''>첨부파일<input type=file class='textbox' title='이미지' name='banner"+type2+"Image[]' size=15 style='font-size:8pt;'></li>";
				bannerRow += "<li>링크<input type=text class='textbox' title='링크' name='link"+type2+"[]' size=15 style='font-size:8pt;'></li>";
				bannerRow += "<li>노출순서<input type=text class='textbox' title='노출순서' name='displayOrder"+type2+"[]' size=15 style='font-size:8pt;'></li>";
			bannerRow += "</ul>";

		$(bannerRow).appendTo('#'+type+'BannerView');
	}

	function removePCBanner(elem){
		elem.parent().parent().remove();
	}

	function BrandInput(frm,mode)
	{
		
		frm.mode.value = mode;
		//frm.brandimg.style.display="block";
		//frm.top_design.value = iView.document.body.innerHTML;
		//frm.top_design.value = document.getElementById("iView").contentWindow.document.body.innerHTML;//kbk
		return CheckFormValue(frm);
		//frm.submit();
	}

	function BrandInput_lsit(frm,mode)
	{
		
		//frm.mode.value = mode;
		//frm.brandimg.style.display="block";
		//frm.top_design.value = iView.document.body.innerHTML;
		//frm.top_design.value = document.getElementById("iView").contentWindow.document.body.innerHTML;//kbk
		return CheckFormValue(frm);
		//frm.submit();
	}

	function BrandSubmit(frm,mode)
	{
		frm.mode.value = mode;
		frm.brand_name_division.value= hangul_to_jaso(frm.brand.value);
		//frm.brandimg.style.display="block";
		//frm.top_design.value = iView.document.body.innerHTML;
		//frm.top_design.value = document.getElementById("iView").contentWindow.document.body.innerHTML;//kbk
		if(CheckFormValue(frm)){
			frm.submit();		
		}
		
	}

	function CompanySubmit(frm,mode)
	{
		frm.mode.value = mode;
		frm.company_name_division.value= hangul_to_jaso(frm.company_name.value);
		//frm.brandimg.style.display="block";
		//frm.top_design.value = iView.document.body.innerHTML;
		//frm.top_design.value = document.getElementById("iView").contentWindow.document.body.innerHTML;//kbk
		if(CheckFormValue(frm)){
			frm.submit();		
		}
		
	}

	function ViewBrandImage(b_ix)
	{
		window.frames["extand"].location.href="brand.act.php?mode=change&b_ix="+b_ix;
		//document.getElementById("extand").src="brand.act.php?mode=change&b_ix="+b_ix;//kbk
		
	}

	function categoryadd()
	{
		var ret;
		var str = new Array();
		var obj = document.brandform.cid;
		for (i=0;i<obj.length;i++){
			if (obj[i].value){
				str[str.length] = obj[i][obj[i].selectedIndex].text;
				ret = obj[i].value;
			}
		}
		if (!ret){
			alert(language_data['goods_input.php']['A'][language]);//'카테고리를 선택해주세요'
			return;
		}
		//var cate = document.all._category;
		var cate=document.getElementsByName('category[]'); // 호환성 kbk
		//alert(cate.length);

		//if(is_array([cate])){
			//alert(cate.length);
			for(i=0;i < cate.length;i++){
				//alert(ret +'=='+ cate[i].value);
				//alert(cate[i].value);
				if(ret == cate[i].value){
					alert(language_data['goods_input.php']['B'][language]);
					//'이미등록된 카테고리 입니다.'
					return;
				}
			}
		//}

		//cate.unshift(ret);
		var obj = document.getElementById('objCategory');
		//oTr = obj.insertRow();
		oTr = obj.insertRow(-1); // 크롬과 파폭에서는 td의 생성이 반대로 됨 -1 인자를 넣어주면 순서대로 형성됨 2011-04-07 kbk
		oTr.id = 'num_tr';
		oTr.height = '30px';
		//oTr.className = 'dot_xx';
		if(window.addEventListener) oTr.setAttribute('class','');
		else oTr.className = '';
		oTd = oTr.insertCell(-1);
		//oTd.className = '';
		if(window.addEventListener) oTd.setAttribute('class','');
		else oTd.className = '';
		oTd.innerHTML = "<input type=text name=category[] id='_category' value='" + ret + "' style='display:none'>";
		oTd = oTr.insertCell(-1);
		//oTd.className = '';
		if(window.addEventListener) oTd.setAttribute('class','');
		else oTd.className = '';
		if(oTr.rowIndex == 0){
			oTd.innerHTML = "<input type=radio name=basic value='"+ ret + "' checked>";
		}else{
			oTd.innerHTML = "<input type=radio name=basic value='"+ ret + "'>";
		}
		oTd = oTr.insertCell(-1);
		//oTd.id = "currPosition";
		if(window.addEventListener) oTd.setAttribute('id','currPosition');
		else oTd.id = 'currPosition';
		//oTd.className = '';
		if(window.addEventListener) oTd.setAttribute('class','');
		else oTd.className = '';
		oTd.innerHTML = str.join(" > ");
		oTd = oTr.insertCell(-1);
		//oTd.className = '';
		if(window.addEventListener) oTd.setAttribute('class','');
		else oTd.className = '';
		oTd.innerHTML = " <a href='javascript:void(0)' onClick='category_del(this.parentNode.parentNode)'><img src='../images/"+language+"/btc_del.gif' border=0></a>";


	}

	function category_del(el)
	{
		idx = el.rowIndex;
		var obj = document.getElementById('objCategory');
		obj.deleteRow(idx);
		var cObj=$('input[name=basic]');
		var cObj_num=0;
		if(cObj.length == null){
			//cObj[0].checked = true; // 0이 나오지 null이 나오지 않음 kbk
		}else{
			for(var i=0;i<cObj.length;i++){
				if(cObj[i].checked){
					cObj_num++;
				}
			}
			if(cObj_num==0) {
				cObj[0].checked = true;
			}
		}
		//cate.splice(idx,1);
	}


	function hangul_to_jaso(text){
	//초성(19자) ㄱ ㄲ ㄴ ㄷ ㄸ ㄹ ㅁ ㅂ ㅃ ㅅ ㅆ ㅇ ㅈ ㅉ ㅊ ㅋ ㅌ ㅍ ㅎ 
		 var ChoSeong = new Array (0x3131, 0x3132, 0x3134, 0x3137, 0x3138,
					 0x3139, 0x3141, 0x3142, 0x3143, 0x3145, 0x3146, 0x3147, 0x3148,
					 0x3149, 0x314a, 0x314b, 0x314c, 0x314d, 0x314e );
			   //중성(21자) ㅏ ㅐ ㅑ ㅒ ㅓ ㅔ ㅕ ㅖ ㅗ ㅘ(9) ㅙ(10) ㅚ(11) ㅛ ㅜ ㅝ(14) ㅞ(15) ㅟ(16) ㅠ ㅡ ㅢ(19) ㅣ 
		   var JungSeong = new Array (0x314f, 0x3150, 0x3151, 0x3152, 0x3153,
					   0x3154, 0x3155, 0x3156, 0x3157, 0x3158, 0x3159, 0x315a, 0x315b,
					  0x315c, 0x315d, 0x315e, 0x315f, 0x3160, 0x3161, 0x3162, 0x3163 );
			  //종성(28자) <없음> ㄱ ㄲ ㄳ(3) ㄴ ㄵ(5) ㄶ(6) ㄷ ㄹ ㄺ(9) ㄻ(10) ㄼ(11) ㄽ(12) ㄾ(13) ㄿ(14) ㅀ(15) ㅁ ㅂ ㅄ(18) ㅅ ㅆ ㅇ ㅈ ㅊ ㅋ ㅌ ㅍ ㅎ 
		  var JongSeong = new Array (0x0000, 0x3131, 0x3132, 0x3133, 0x3134,
					  0x3135, 0x3136, 0x3137, 0x3139, 0x313a, 0x313b, 0x313c, 0x313d,
					  0x313e, 0x313f, 0x3140, 0x3141, 0x3142, 0x3144, 0x3145, 0x3146,
					  0x3147, 0x3148, 0x314a, 0x314b, 0x314c, 0x314d, 0x314e );
		  var chars = new Array()
		  var v = new Array();
		  for (var i = 0; i < text.length; i++){ 
			  chars[i] = text.charCodeAt(i); 
			  //// "AC00:가" ~ "D7A3:힣" 에 속한 글자면 분해         
			  if (chars[i] >= 0xAC00 && chars[i] <= 0xD7A3) {
				  var i1, i2, i3; 
				   
				  i3 = chars[i] - 0xAC00; 
				  i1 = i3 / (21 * 28);            
				  i3 = i3 % (21 * 28);        
					   
				  i2 = i3 / 28; 
				  i3 = i3 % 28;             
					   
				  v.push(String.fromCharCode(ChoSeong[parseInt(i1)])); 
	   
				  //복모음 분리 
				  switch(parseInt(i2)){ 
					  case 9 : v.push('ㅗㅏ'); break; 
					  case 10 : v.push('ㅗㅐ'); break;  
					  case 11 : v.push('ㅗㅣ'); break; 
					  case 14 : v.push('ㅜㅓ'); break; 
					  case 15 : v.push('ㅜㅔ'); break; 
					  case 16 : v.push('ㅜㅣ'); break; 
					  case 19 : v.push('ㅡㅣ'); break;                
					   
					  default : v.push(String.fromCharCode(JungSeong[parseInt(i2)]));
				  }             
				   
				  // c가 0이 아니면, 즉 받침이 있으면 
				  if (i3 != 0x0000) {                        
					  //복자음 분리 
					  switch(parseInt(i3)){ 
						  case 3 : v.push('ㄱㅅ'); break; 
						  case 5 : v.push('ㄴㅈ'); break;  
						  case 6 : v.push('ㄴㅎ'); break; 
						  case 9 : v.push('ㄹㄱ'); break; 
						  case 10 : v.push('ㄹㅁ'); break; 
						  case 11 : v.push('ㄹㅂ'); break; 
						  case 12 : v.push('ㄹㅅ'); break; 
						  case 13 : v.push('ㄹㅌ'); break; 
						  case 14 : v.push('ㄹㅍ'); break; 
						  case 15 : v.push('ㄹㅎ'); break; 
						  case 18 : v.push('ㅂㅅ'); break; 
						   
						  default : v.push(String.fromCharCode(JongSeong[parseInt(i3)])); 
					  } 
				  } 
				   
			  }else { 
				  v.push(String.fromCharCode(chars[i] )); 
			  } 
		  } 
		   
		  var return_str = v.join('');
		  return return_str; 
	}


	 
	function deleteBrandInfo(mode, b_ix){
		if(confirm('해당 브랜드 정보를 정말로 삭제하시겠습니까?')){
			window.frames['act'].location.href = './brand.act.php?mode=delete&b_ix='+b_ix;
		}
	}

	function loadCategory(sel,target) {
		//alert(target);
		var trigger = sel.options[sel.selectedIndex].value;	
		var form = sel.form.name;
		//var depth = sel.depth;
		//var depth = sel.getAttribute("depth");//kbk
		var depth = $("select[name="+sel.name+"]").attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		//if(depth == 2){
		//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
		//}
		//alert(target);
		//dynamic.src = 'category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;//kbk
		
		window.frames["act"].location.href = 'category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;

		//document.getElementById("act").src = 'category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		
	}

	function loadBrandInfo(sel,target) {//brand.php 에 있던 것을 brand_list.php 에서 같이 사용하기 위해 여기로 옮겨옴 kbk 13/07/01

		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;

		//var depth = sel.getAttribute('depth');
	//	document.write('brand.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2');
		window.frames['act'].location.href = './brand.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

	}

	function loadBrandInfo2(sel) {//2차 분류 선택 시 kbk 13/07/02
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form;

		if(trigger!="") {
			form.bd_ix2.value=trigger;
		} else {
			form.bd_ix2.value=form.parent_bd_ix.value;
		}
	}

	 function checkBrandCode(obj,target_obj) {

		$.ajax({ 
				type: 'GET', 
				data: {'act': 'checkBrandCode', 'brand_code':obj.val()},
				url: '../product/brand.act.php',  
				dataType: 'json', 
				async: true, 
				beforeSend: function(){ 
					
				},
				success: function(result){

					if(result.bool){
						$('#check_brand_code').val(1);
					}else{
						$('#check_brand_code').val(0);
					}
					target_obj.html(result.message);
				} 
			}); 
	 
	}

	 function checkcompanyCode(obj,target_obj) {

		$.ajax({ 
				type: 'GET', 
				data: {'act': 'checkcompanyCode', 'cp_code':obj.val()},
				url: '../product/company.act.php',  
				dataType: 'json', 
				async: true, 
				beforeSend: function(){ 
					
				},  
				success: function(result){
					//alert(result);
					if(result.bool){
						$('#check_company_code').val(1);
					}else{
						$('#check_company_code').val(0);
					}
					target_obj.html(result.message);
				} 
			}); 
	}

