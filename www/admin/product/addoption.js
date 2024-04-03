function move(direct)
{
	
		for (i=album.options.selectedIndex;i<album.options.length;i++){
			if (selR.options[i].selected==true){
				selR.options.remove(i--);
			}
		}
	
}

function addoption_delete(frm){
		if(frm.album.options.length <= 1){
			alert(language_data['addoption.js']['A'][language]);
			//'앨범이 한개일경우는 지울수 없습니다.'
		}else{
		
			for (i=0;i<frm.album.options.length;i++){
				if (frm.album.options[i].selected==true){
					frm.album.options.remove(i);
				}
			}
		
			frm.abm_name.value = "";
			frm.abm_open.checked = false;
		}
}

function Length(str){
	return(str.length+(escape(str)+"%u").match(/%u/g).length-1);
}

function CheckStringLength(str){
	var frm = document.addoption_input;
	frm.aname_length.value = Length(str);
	if(Length(str) >= 19){		
	//alert(Length(str));
		if(event.keyCode != 8 && event.keyCode != 46){
			event.returnValue=false;
			alert(language_data['addoption.js']['B'][language]);
			//'앨범이름은 한글 8자 영문 16자 까지 가능합니다.'
			return false;
		}
	}
}


function AlbumChkByte(objname,maxlength) 
{ 
  var objstr = objname.value; // 입력된 문자열을 담을 변수 
  var objstrlen = objstr.length; // 전체길이 

  // 변수초기화 
  var maxlen = maxlength; // 제한할 글자수 최대크기 
  var i = 0; // for문에 사용 
  var bytesize = 0; // 바이트크기 
  var strlen = 0; // 입력된 문자열의 크기
  var onechar = ""; // char단위로 추출시 필요한 변수 
  var objstr2 = ""; // 허용된 글자수까지만 포함한 최종문자열

  // 입력된 문자열의 총바이트수 구하기
  for(i=0; i< objstrlen; i++) 
  { 
    // 한글자추출 
   onechar = objstr.charAt(i); 
   
  if (escape(onechar).length > 4)
  { 
   bytesize += 2;     // 한글이면 2를 더한다. 
  } 
  else
  {  
     bytesize++;      // 그밗의 경우는 1을 더한다.
  } 
   
  if(bytesize <= maxlen) 
  {   // 전체 크기가 maxlen를 넘지않으면 
   strlen = i + 1;     // 1씩 증가
  }
  }

  // 총바이트수가 허용된 문자열의 최대값을 초과하면 
  if(bytesize > maxlen) 
  { 
    alert(language_data['addoption.js']['C'][language]); 
	//"앨범이름은 한글 8자 영문 16자 까지 가능합니다. \n초과된 내용은 자동으로 삭제 됩니다."
    objstr2 = objstr.substr(0, strlen); 
    objname.value = objstr2; 
  } 

  objname.focus(); 
} 


function ChangeAlbumName(frm, abm_obj){
	
	//alert(Length(abm_obj.value));
	
	if(frm.abm_open.checked)
		abm_open_value = 0
	else
		abm_open_value = 1
		
	frm.album.options[frm.album.selectedIndex].text = abm_obj.value;
	frm.album.options[frm.album.selectedIndex].value = getValue(frm.album.options[frm.album.selectedIndex],0)+"|"+abm_obj.value+"|"+abm_open_value
	
	
}

function AddAlbum(frm){
	if(frm.abm_open.checked)
		abm_open_value = 0
	else
		abm_open_value = 1
		
	var newAlbum = document.createElement("option");
	newAlbum.value = "-|새앨범|"+abm_open_value;
	newAlbum.innerText = "새앨범";
	
	var obj = frm.album.options.appendChild(newAlbum);
	obj.selected = true;
	frm.abm_name.value = "새앨범";
	frm.abm_open.checked = false;
	
	//new_sort(frm.album);
}

function SelectList(obj){

	document.addoption_input.abm_name.value=obj.options[obj.selectedIndex].text
	if(getValue(obj.options[obj.selectedIndex],2) == 0){
		document.addoption_input.abm_open.checked = true;
	}else{
		document.addoption_input.abm_open.checked = false;
	}

}

function OnClick_Up(objLst)
{
	var index = objLst.selectedIndex;
	if (index <= 0) 
		return;

	swap_option(objLst, index-1);
	objLst.options[index-1].selected = true;
}

function OnClick_Dn(objLst){

	var index = objLst.selectedIndex;

	if (index >= objLst.options.length-1)
	return;

	swap_option(objLst, index+1);	
	objLst.options[index+1].selected = true;
}

function swap_option(target, index)
 {
	var index_1 = target.selectedIndex;
	var value0 = getValue(target, 0);
	var value1 = getValue(target, 1);
	var value2 = getValue(target, 2);
	
	target.options[target.selectedIndex].selected = false;
	target.options[index].selected = true;

	var index_2 = index;
	var value0_ = getValue(target, 0);
	var value1_ = getValue(target, 1);
	var value2_ = getValue(target, 2);
	
	var strValue_1 = value0+"|"+value1+"|"+value2;
	var strValue_2 = value0_+"|"+value1_+"|"+value2_;
	target.options[index_1].value = strValue_2;
	target.options[index_1].text = value1_;
	target.options[index_2].value = strValue_1;
	target.options[index_2].text = value1;
 }

function new_sort(objLst)
{
   var index = objLst.options.length;
	
   while(index>1)
   {
   	index = index -1;
   	var strValue_1 = objLst.options[index].value;
   	var strName_1 = objLst.options[index].text;
   	var strValue_2 = objLst.options[index-1].value;
   	var strName_2 = objLst.options[index-1].text;
   	change_option(strName_2, strValue_2, objLst, index);
   	change_option(strName_1, strValue_1, objLst, index-1);
   }
}
 
function change_option(text, value, target, index)
{
   target.options[index].text = text;
   target.options[index].value = value;
}



function getValue(obj,param) {

	var arrayValue = obj.value.split("|");
	
	for(i=0;i<arrayValue.length;i++){
		if(i==param){
			return arrayValue[i];
		}
	}
}

function AllSelect(){
	var frm = document.addoption_input;
	
	for(i=0;i<frm.album.length;i++){
		frm.album.options[i].selected = true;
	}
	
	frm.submit();
	//return true;

}

function CheckValue(frm){
	frm.album.multiple = true;
	
	if(frm.album.multiple){
		setTimeout("AllSelect()",1000);		
	}	
	//return true;

}