<!--
String.prototype.cutStr = function(length, addText) { 
	var result	= "";
	var strLen	= 0;
	var strSum	= 0;

	// 실제 길이를 추출합니다.
	for(var i=0; i<this.length; i++){
		if( this.charCodeAt(i) < 255)		strLen	+= 1;
		else								strLen	+= 2;
	}

	if(strLen > length){
		// 문자열 길이를 변경합니다.
		for(var i=0; i<this.length; i++){
			if( this.charCodeAt(i) < 255)	strSum	+= 1;
			else							strSum	+= 2;

			if(strSum < length){
				result	+= this.charAt(i);
			}else{
				result	+= "...";
				break;
			}
		}
	}else{
		result = this;
	}

	return result;
}
function getCookie( name ){
	var nameOfCookie = name + "=";
	var x = 0;

	while ( x <= document.cookie.length )
	{
		var y = (x+nameOfCookie.length);
		if ( document.cookie.substring( x, y ) == nameOfCookie ) {
			if ( (endOfCookie=document.cookie.indexOf( ";", y )) == -1 )
				endOfCookie = document.cookie.length;

			return unescape( document.cookie.substring( y, endOfCookie ) );
		}

		x = document.cookie.indexOf( " ", x ) + 1;
		if ( x == 0 )
			break;
	}

	return "";
}



function loadXml(idx){
	var xmlUrl		= "../../test/xmls/test1.xml";
	//var xmlUrl		= "../../test/xmls/test"+idx+".xml";
	//var xmlUrl		= "xmls/test"+idx+".xml";
	//var xmlUrl = "../../data/demo/templet/b2b/layout/header/header_top.htm";
	var aObj		= new AjaxObject;
	
	
	aObj.getHttpRequest(xmlUrl,		"displayContinent", idx);
}

function displayContinent(data, idx){
	var continent	= $("content_"+idx);
	var data		= data['channel'][0];
//alert(continent.id);
	continent.innerHTML = "<img src='http://b2bdev.mallstory.com/admin/images/admin_logo.gif' width=1000 height=30>";
	
	
	/*
	for(var i=0; i<5; i++){
		var oLi			= document.createElement("LI");
		var oA			= document.createElement("A");
		var oText		= document.createElement("TEXT");

		oA.innerHTML	= data['item'][i]['title'].cutStr(45, '...');
		oA.href			= data['item'][i]['link'];
		oText.innerHTML	= "&#8226; ";

		oLi.appendChild(oText);
		oLi.appendChild(oA);

		continent.appendChild(oLi);
	}
	*/
}


addElementIdx = 0;
function addElement(file_path){
	addElementIdx++;
	
	if(!file_path){
		file_path = "aaa.htm";
	}
	
	//personalSort.makeSortItem("1", file_path, "1");
	personalSort.makeSortItem("1", addElementIdx, "1");
}

function layerControl(idx, type){
	if(type == 'exit'){
		personalSort.removeItem(idx);
	}else{
		var obj				= document.getElementById("content_"+idx);
		obj.style.display	= type;

		personalSort.makeCookies();
	}

}
-->