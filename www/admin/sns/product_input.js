//var pricecheckmode = true;

function setCategory(mode,cname,cid,depth,pid)
{
	//outTip(img3);
	window.frames["act"].location.href='./relation.act.php?mode='+mode+'&cid='+cid+'&pid='+pid; //eval(depth+1) 이값은 depth 가 0 이 없어서 ...
}


function deleteCategory(mode,rid,pid)
{
	window.frames["act"].location.href='./relation.act.php?mode='+mode+'&rid='+rid+'&pid='+pid; 
}
	
function calcurate(frm){	
	
	var rate1 = frm.rate1.value;
	var rate2 = frm.rate2.value;
	
	if(frm.sellprice.value.length < 1)
	{
		alert(language_data['sns_product_input.js']['A'][language]);	//'판매가격이 입력되지 않았습니다.'
		return false;
	}
	
	
	if(frm.rate1.value.length < 1)	{
		alert(language_data['sns_product_input.js']['B'][language]);	//'현금사용시 적립율이 입력되지 않았습니다.'
		return false;
	}else{
		rate1 = rate1/100
	}
	
	if(frm.rate2.value.length < 1)	{
		alert(language_data['sns_product_input.js']['C'][language]);	//'카드사용시 적립율이 입력되지 않았습니다.'
		return false;
	}else{
		rate2 = rate2/100
	}
	
	
	frm.reserve_price.value = Round2(filterNum(frm.sellprice.value) * rate1,1,1);
	return true;
	
}

function filterNum(str) {
re = /^$|,/g;

// "$" and "," 입력 제거

return str.replace(re, "");
}

function calcurate_maginrate(frm){
	
	if(frm.sellprice.value.length < 1)	{
		nsellprice = 0;
	}else{
		nsellprice = filterNum(frm.sellprice.value);
	}
	
	if(frm.coprice.value.length < 1)	{
		ncoprice = 0;
	}else{
		ncoprice = filterNum(frm.coprice.value);
	}
	
	if(nsellprice == 0){
		frm.basic_margin.value = "-" ;
	}else{
		frm.basic_margin.value = round((nsellprice-ncoprice)/nsellprice * 100,1) ;
	}
}


function calcurate_margin(frm){	
	
	var card_pay = frm.card_pay.value;
	
	var basic_margin = frm.basic_margin.value/1;
	var sellprice = filterNum(frm.sellprice.value)/1;
//	pricecheckmode = true;
	var reserve = frm.rate1.value/100;

//alert(sellprice);
	
	if(frm.sellprice.value.length < 1)
	{
		alert(language_data['sns_product_input.js']['A'][language]);	//'판매가격이 입력되지 않았습니다.'
		return false;
	}
	
	
	if(frm.card_pay.value.length < 1)	{
		alert(language_data['sns_product_input.js']['E'][language]);	//'카드수수료가 입력되지 않았습니다.'
		return false;
	}else{
		card_pay = card_pay/100
	}
	
	
	calcurate(frm);
	
	
	
	frm.card_price.value = sellprice*card_pay;
//	frm.reserve.value = sellprice*card_pay;
	frm.nointerest_price.value = sellprice*nointerest_pay;
	if (reserve == 0){
		frm.reserve_price.value = 0;
	}else{
		frm.reserve_price.value = Round2(sellprice*reserve,1,1);
	}
	
/*	if(sellprice >= 200000){
		frm.basic_margin.value  = 2000;
	}else{
		frm.basic_margin.value  = sellprice*0.01;
	}
*/	
	frm.margin.value =  parseInt(frm.card_price.value) + parseInt(frm.nointerest_price.value) + parseInt(frm.reserve_price.value);// + parseInt(frm.basic_margin.value);
//	frm.coprice.value = sellprice - frm.margin.value
	return true;
	
}

function ProductInput(frm,act)
{
	frm.act.value = act;
	//frm.recomm_saveprice.value = filterNum(frm.recomm_saveprice.value);
	//frm.recomm_reserve.value = filterNum(frm.recomm_reserve.value);
	
	frm.sellprice.value = filterNum(frm.sellprice.value);
	frm.coprice.value = filterNum(frm.coprice.value);
	if (frm.pname.value.length < 1){
		alert(language_data['sns_product_input.js']['F'][language]);//'제품명이 입력되지 않았습니다.'
		return false;	
	}
	/*
	if (frm.sellprice.value != frm.bsellprice.value){
		alert(language_data['sns_product_input.js']['G'][language]);//"가격에 대한 정보가 변경되었습니다."
		frm.sellprice.value = FormatNumber3(frm.sellprice.value);
		return false;
	}*/
	
	frm.basicinfo.value = iView.document.body.innerHTML;
	if(frm.coprice.value.length < 1)
	{
		alert(language_data['sns_product_input.js']['H'][language]);	//"공급가격이 입력되지 않았습니다."
		return false;
	}
	/*
	if (frm.shotinfo.value.length < 1){
		alert(language_data['sns_product_input.js']['I'][language]);//'제품소개가 입력되지 않았습니다.'
		return false;	
	}
	*/
	frm.submit();	
}

function copyImageCheckAll(){
	var copy_image = document.all.copy_img;
	var all_bool = document.all.copy_allimg.checked
	for(i=0;i < copy_image.length;i++){
		if(all_bool){
			copy_image[i].checked = true;	
		}else{
			copy_image[i].checked = false;	
		}
	}
}

function copyAddImageCheckAll(){
	var add_copy_img = document.all.add_copy_img;
	var all_bool = document.all.add_copy_allimg.checked
	for(i=0;i < add_copy_img.length;i++){
		if(all_bool){
			add_copy_img[i].checked = true;	
		}else{
			add_copy_img[i].checked = false;	
		}
	}
}


function ChnageImg(vsize,vid, img_path)
{
	document.getElementById('viewimg').innerHTML = "<img src='"+img_path+"/"+vsize+"_"+vid+".gif' id=chimg>";
	//document.getElementById('viewimg').innerHTML = "<img src='"+img_path+"/"+vsize+"_"+vid+".gif' id=chimg>\n\n<input type=file name='"+vsize+"img' size=30 style='font-size:8pt'>";
}

function AddImageView(img_path)
{
	document.getElementById('add_image_view').innerHTML = "<img src='"+img_path+"' id=addimg valign=middle>";
	//document.getElementById('viewimg').innerHTML = "<img src='"+img_path+"/"+vsize+"_"+vid+".gif' id=chimg>\n\n<input type=file name='"+vsize+"img' size=30 style='font-size:8pt'>";
}

function AddImageAct(frm,act)
{
	frm.act.value = act;
	frm.submit();
}

function AddOption(frm){
	frm.submit();
}

function deleteAddimage(act,id,pid)
{
	window.frames["act"].location.href='./img.add.php?act='+act+'&id='+id+'&pid='+pid; 
}

function showTabContents(vid, tab_id){
	var area = new Array('addimginputarea','displayinfo_area','OptionArea','categoryarea','AddOptionArea2','machumarea','relation_product_area');
	var tab = new Array('tab_01','tab_02','tab_03','tab_04','tab_05','tab_06','tab_07');
	
	for(var i=0; i<area.length; ++i){
		
		if(area[i]==vid){
			document.getElementById(vid).style.display = "block";			
			document.getElementById(tab_id).className = "on";
		}else{			
			document.getElementById(area[i]).style.display = "none";
			document.getElementById(tab[i]).className = "";
		}
	}
}

function showPriceTabContents(vid, tab_id){
	var area = new Array('price_info','detail_price_info');
	var tab = new Array('p_tab_01','p_tab_02');
	
	for(var i=0; i<area.length; ++i){
		
		if(area[i]==vid){
			document.getElementById(vid).style.display = "block";			
			document.getElementById(tab_id).className = "on";
		}else{			
			document.getElementById(area[i]).style.display = "none";
			document.getElementById(tab[i]).className = "";
		}
	}
}


function dpCheckOptionData(frm){
	if(frm.dp_title.value.length < 1){
		alert(language_data['sns_product_input.js']['J'][language]);	//'옵션구분값을 입력해주세요'
		frm.option_div.focus();
		return false;
	}
	
	if(frm.dp_desc.value.length < 1){
		alert(language_data['sns_product_input.js']['K'][language]);	//'옵션별 비회원가를 입력해주세요'
		frm.option_price.focus();
		return false;
	}
	
	return true;
}


function CheckOptionData(frm){
	var option_kind = frm.option_kind.value
	if(option_kind == 'b' || option_kind == 'p'){
		if(frm.opn_ix.value == ""){
			alert(language_data['sns_product_input.js']['L'][language]);	//'옵션이름을 선택해주세요'
			frm.option_div.focus();
			return false;
		}
		
		if(frm.option_div.value.length < 1){
			alert(language_data['sns_product_input.js']['J'][language]);	//'옵션구분값을 입력해주세요'
			frm.option_div.focus();
			return false;
		}
		
		if(frm.option_price.value.length < 1){
			alert(language_data['sns_product_input.js']['K'][language]);	//'옵션별 비회원가를 입력해주세요'
			frm.option_price.focus();
			return false;
		}
		
		if(frm.option_m_price.value.length < 1){
			alert(language_data['sns_product_input.js']['M'][language]);	//'옵션별 회원가를 입력해주세요'
			frm.option_m_price.focus();
			return false;
		}
		
		if(frm.option_d_price.value.length < 1){
			alert(language_data['sns_product_input.js']['N'][language]);	//'옵션별 딜러가를 입력해주세요'
			frm.option_d_price.focus();
			return false;
		}
		
		if(frm.option_a_price.value.length < 1){
			alert(language_data['sns_product_input.js']['O'][language]);	//'옵션별 대리점가를 입력해주세요'
			frm.option_a_price.focus();
			return false;
		}
	}else if(option_kind == 's'){
		if(frm.opn_ix.value == ""){
			alert(language_data['sns_product_input.js']['L'][language]);	//'옵션이름을 선택해주세요'
			frm.option_div.focus();
			return false;
		}
		
		if(frm.option_div.value.length < 1){
			alert(language_data['sns_product_input.js']['J'][language]);	//'옵션구분값을 입력해주세요'
			frm.option_div.focus();
			return false;
		}
	}else{
		alert(language_data['sns_product_input.js']['L'][language]);//'옵션이름을 선택해주세요'
		return false;	
	}
	
	return true;
}

function deleteOption(act,id,pid, opn_ix){
	window.frames["act"].location.href='./option.act.php?act='+act+'&id='+id+'&pid='+pid+'&opn_ix='+opn_ix; 
}

function deleteDisplayOption(act,dp_ix,pid){
	window.frames["act"].location.href='./display_option.act.php?act='+act+'&dp_ix='+dp_ix+'&pid='+pid; 
}

function UpdateOption(option_id, option_div, option_price,option_m_price,option_d_price,option_a_price, option_stock, option_safestock, option_etc1){	
	var frm = document.forms["optionform"];
	frm.act.value ='update';
	
	frm.option_id.value = option_id;
	//frm.option_name.value = option_name;
	frm.option_div.value = option_div;
	frm.option_price.value = option_price;
	frm.option_m_price.value = option_m_price;
	frm.option_d_price.value = option_d_price;
	frm.option_a_price.value = option_a_price;
	frm.option_stock.value = option_stock;
	frm.option_safestock.value = option_safestock;
	frm.option_etc1.value = option_etc1;
	
	//document.frames["act"].location.href='./option.act.php?act='+act+'&id='+id+'&pid='+pid; 
}

function UpdateDisplayOption(dp_ix, dp_title, dp_desc){	
	var frm = document.forms["dispoptionform"];
	frm.act.value ='update';
	
	frm.dp_ix.value = dp_ix;	
	frm.dp_title.value = dp_title;
	frm.dp_desc.value = dp_desc;
	
}

function FormatNumber2_old(num){
        // 만든이:김인현(jasmint@netsgo.com)
        // ie5.5이상에서 사용할것
        fl=""
        if(isNaN(num)) { alert(language_data['sns_product_input.js']['P'][language]);return 0}//"문자는 사용할 수 없습니다."
        if(num==0) return num
        
        if(num<0){ 
                num=num*(-1)
                fl="-"
        }else{
                num=num*1 //처음 입력값이 0부터 시작할때 이것을 제거한다.
        }
        num = new String(num)
        temp=new Array()
        co=3
        num_len=num.length
        while (num_len>0){
                num_len=num_len-co
                if(num_len<0){co=num_len+co;num_len=0}
                temp.unshift(num.substr(num_len,co))
        }
        return fl+temp.join(",")
}

function FormatNumber2(num){
        // 만든이:김인현(jasmint@netsgo.com)
        fl=""
        if(isNaN(num)) { alert(language_data['sns_product_input.js']['P'][language]);return 0}//"문자는 사용할 수 없습니다."
        if(num==0) return num
        
        if(num<0){ 
                num=num*(-1)
                fl="-"
        }else{
                num=num*1 //처음 입력값이 0부터 시작할때 이것을 제거한다.
        }
        num = new String(num)
        temp=""
        co=3
        num_len=num.length
        while (num_len>0){
                num_len=num_len-co
                if(num_len<0){co=num_len+co;num_len=0}
                temp=","+num.substr(num_len,co)+temp
        }
        return fl+temp.substr(1)
}

function FormatNumber3(num){
        num=new String(num)
        num=num.replace(/,/gi,"")
      //  pricecheckmode = false;
        
        return FormatNumber2(num)
}

function num_check() {
        // ie에서만 작동
        var keyCode = event.keyCode
        if ((keyCode < 48 || keyCode > 57) && keyCode != 8){
                alert(language_data['sns_product_input.js']['P'][language]+"["+keyCode+"]")//"문자는 사용할 수 없습니다."
                event.returnValue=false
        }
        
}

function round(num,ja) {
        ja=Math.pow(10,ja)
        return Math.round(num * ja) / ja;
}

function Round2(Num, Position , Base){
//Num = 반올림할 수
//Position = 반올림할 자릿수(정수로만)
//Base = i 이면 소숫점위의 자릿수에서, f 이면 소숫점아래의 자릿수에서 반올림

	if(Position == 0){ 
	        //1이면 소숫점1 자리에서 반올림
	return Math.round(Num); 
	}else if(Position > 0){
	                var cipher = '1';
	                for(var i=0; i < Position; i++ )
	                                cipher = cipher + '0';
	
	                var no = Number(cipher);
	
	                if(Base=="F"){
	                                //소숫점아래에서 반올림                        
	                                return Math.round(Num * no) / no;
	                }else{
	                                //소숫점위에서 반올림.                        
	                                return Math.round(Num / no) * no;
	                }
	 }else{
	                alert(language_data['sns_product_input.js']['Q'][language]);//"자릿수는 정수로만 구분합니다."
	                return false;
	 }

}


function commaSplit(srcNumber) {
	var txtNumber = '' + srcNumber;
	if (isNaN(txtNumber) || txtNumber == "") {
		//alert("숫자만 입력 하세요");
		return 0;
	}
	else {
		var rxSplit = new RegExp('([0-9])([0-9][0-9][0-9][,.])');
		var arrNumber = txtNumber.split('.');
		arrNumber[0] += '.';
		do {
			arrNumber[0] = arrNumber[0].replace(rxSplit, '$1,$2');
		} while (rxSplit.test(arrNumber[0]));
		
		if (arrNumber.length > 1) {
			return arrNumber.join('');
		}else {
			return arrNumber[0].split('.')[0];
		}
	}
}


function SampleProductInsert(){
	var frm = document.forms['product_input'];
	frm.pname.value = "sample product";
	frm.pcode.value = "pdc00001";
	frm.company.value = "(주) 몰스토리";
	frm.sellprice.value = "10000";
	frm.prd_member_price.value = "9000";
	frm.prd_dealer_price.value = "8000";
	frm.prd_agent_price.value = "7000";
	frm.coprice.value = "5000";
	
}

function copyPrice(frm, copy_price, step){
	
	if(step == 1){
		if(copy_price == ""){
			alert(language_data['sns_product_input.js']['R'][language]);	//'구매단가(공급가)를 입력후 복사 버튼을 클릭해주세요'
			return false;
		}
		frm.listprice.value = copy_price;
		frm.sellprice.value = copy_price;
		/*frm.prd_member_price.value = copy_price;
		frm.prd_dealer_price.value = copy_price;
		frm.prd_agent_price.value = copy_price;*/		
	}else if(step == 2){
		if(copy_price == ""){
			alert(language_data['sns_product_input.js']['S'][language]);	//'정가를 입력후 복사 버튼을 클릭해주세요'
			return false;
		}
		frm.sellprice.value = copy_price;
		/*frm.prd_member_price.value = copy_price;
		frm.prd_dealer_price.value = copy_price;
		frm.prd_agent_price.value = copy_price;*/	
	}
	/*else if(step == 3){
		if(copy_price == ""){
			alert(language_data['sns_product_input.js']['T'][language]);	//'회원가를 입력후 복사 버튼을 클릭해주세요'
			return false;
		}
		frm.prd_member_price.value = copy_price;
		frm.prd_dealer_price.value = copy_price;
		frm.prd_agent_price.value = copy_price;	
	}else if(step == 4){
		if(copy_price == ""){
			alert(language_data['sns_product_input.js']['U'][language]);	//'딜러가를 입력후 복사 버튼을 클릭해주세요'
			return false;
		}
		frm.prd_agent_price.value = copy_price;	
	}*/
}