

var fileLoadingImage = "/admin/images/indicator.gif";
var fileBottomNavCloseImage = "/images/admin/closelabel.gif";
var overlayOpacity = 0.0;	// controls transparency of shadow overlay
var animate = true;			// toggles resizing animations
var resizeSpeed = 8.6;		// controls the speed of the image resizing animations (1=slowest and 10=fastest)
var borderSize = 10;		//if you adjust the padding in the CSS, you will need to update this variable
// -----------------------------------------------------------------------------------
//
//	Global Variables
//
var imageArray = new Array;
var activeImage;
if(animate == true){
	overlayDuration = 0.2;	// shadow fade in/out duration
	if(resizeSpeed > 10){ resizeSpeed = 10;}
	if(resizeSpeed < 1){ resizeSpeed = 1;}
	resizeDuration = (11 - resizeSpeed) * 0.15;
} else {
	overlayDuration = 0;
	resizeDuration = 0;
}
// -----------------------------------------------------------------------------------

var opacity_level = 5; // how transparent our overlay bg is

var imgPreloader = new Image(); // create an preloader object
var loadCancelled = false;
var ibox_w_height = 0;
var oldPosition = "divPosition";
var currentPosition = "divPosition";

var titleImage = [
	
];

/*
'<IMG SRC="/images/ps/popup_PS_01.gif" WIDTH="67" HEIGHT="16" ALT="회원찾기" BORDER="0">', // 0
	'<IMG SRC="/images/ps/tit_PS_layer01.gif" WIDTH="109" HEIGHT="16" ALT="아이디중복확인" BORDER="0">', // 1
	'<IMG SRC="/images/ps/tit_PS_layer02.gif" WIDTH="95" HEIGHT="16" ALT="별명중복확인" BORDER="0">', // 2
	'<IMG SRC="/images/ps/tit_PS_layer03.gif" WIDTH="64" HEIGHT="16" ALT="학교검색" BORDER="0">', // 3
	'<IMG SRC="/images/ps/tit_PS_layer13.gif" WIDTH="109" HEIGHT="16" ALT="본인확인 질문답변" BORDER="0">', // 4
	'<IMG SRC="/images/ps/tit_PS_layer14.gif" WIDTH="143" HEIGHT="16" ALT="비밀번호초기화 알림" BORDER="0">', // 5
	'<IMG SRC="/images/ps/tit_PS_layer15.gif" WIDTH="136" HEIGHT="16" ALT="휴대전화 번호 확인" BORDER="0">', // 6
	'<IMG SRC="/images/ps/tit_PS_layer16.gif" WIDTH="123" HEIGHT="16" ALT="이메일 주소 확인" BORDER="0">', // 7
	'<IMG SRC="/images/ps/tit_PS_layer17.gif" WIDTH="180" HEIGHT="16" ALT="SMS 비밀번호초기화 완료" BORDER="0">', // 8
	'<IMG SRC="/images/ps/tit_PS_layer18.gif" WIDTH="200" HEIGHT="16" ALT="이메일 비밀번호초기화 완료" BORDER="0">', // 9
	'<IMG SRC="/images/ps/tit_PS_layer11.gif" WIDTH="66" HEIGHT="16" ALT="주소찾기" BORDER="0">', // 10
	'<IMG SRC="/images/ps/tit_PS_layer12.gif" WIDTH="66" HEIGHT="16" ALT="기관검색" BORDER="0">', // 11 
	'<IMG SRC="/images/ps/tit_PS_layer10.gif" WIDTH="94" HEIGHT="16" ALT="학생그룹관리" BORDER="0">', // 12
	'<img src="/images/ps/stit_PS_attestation.gif" alt="" border="0">'	//13
	*/
var box = Class.create();
box.prototype = {
	
	initialize : function()
	{
		
		var strHTML =  '<div id="ibox_w" style="display:none"></div>';
		strHTML +=	   '<div id=\"ibox_wrapper\"  style=\"display:none;padding-top:30px;\">';
		strHTML +=		"<table cellpadding=0 cellspacing=0 style='border:2px solid orange;border-top:0px;'>";
		strHTML +=		"<tr height='4'><td  class='top_orange' colspan=2></td></tr>";
		strHTML +=		"<tr><td>";
		strHTML +=		"<table id=\"ibox_width\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"orange\">";		
		strHTML +=		  '<tr BGCOLOR="#FFFFFF">';
		strHTML +=		    '<td  >';
		strHTML += 			 "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
		strHTML +=		      '<tr>';
		strHTML +=		        '<td COLSPAN="2">';
		strHTML += 			 '<table width="100%" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">';
		strHTML += 			 '<TR BGCOLOR="#efefef" STYLE="padding-top:3px;">';
		strHTML += 			 '<TD id=\"ibox_title\" HEIGHT="25" STYLE="padding-left:20px;"><!--IMG SRC="/images/ps/popup_PS_01.gif" WIDTH="67" HEIGHT="16" ALT="" BORDER="0"--> </TD>';
		strHTML += 			 '<TD ALIGN="right" STYLE="padding-right:20px;"><!--IMG ID="ibox_Close" NAME="ibox_Close" SRC="/images/ps/btn_layertop.gif" WIDTH="10" HEIGHT="10" ALT="" BORDER="0" STYLE="CURSOR:POINTER"--></TD>';
		strHTML +=		      "</tr>";
		strHTML += 			 "</table>";
		strHTML +=		        "</td>";
		strHTML +=		      "</tr>";
		strHTML +=		      "<tr>";
		strHTML +=		        '<td COLSPAN="2" id=\"ibox_content_td\" align=\"left\" style=\"padding-left:0px;padding-right:0px\">';
		strHTML +=	  			  "<div id=\"ibox_content\" style=\"overflow:auto;\">";
		strHTML +=    			  "</div>";
		strHTML +=		        "</td>";
		strHTML +=		      "</tr>";
		strHTML +=		  '<TR><TD COLSPAN="2" HEIGHT="2" BGCOLOR="orange"></TD></TR>';
		strHTML +=		  '<TR><TD COLSPAN="2" HEIGHT="3" BGCOLOR="#efefef"></TD></TR>';
		strHTML +=		  '<TR BGCOLOR="#efefef">';
		strHTML +=		  '<TD  ALIGN="left" STYLE="padding-left:20px;" HEIGHT="30">copyright ⓒ mallstory.com all right reserved. </TD>';
		strHTML +=		  '<TD  ALIGN="right" STYLE="padding-right:20px;" HEIGHT="30">';
		strHTML +=		  '<IMG ID="ibox_Close2" NAME="ibox_Close2" SRC="/admin/image/close.gif" ALT="닫기" BORDER="0" STYLE="cursor:pointer;" >';
		strHTML +=		  "</TD>";
		strHTML +=		  "</TR>";
		strHTML +=		    "</table>";
		strHTML +=		    "</td>";
		strHTML +=		  "</tr>";
		strHTML +=		"</table>";
		strHTML +=		"</td></tr></table>";
		strHTML +=	  "</div>";
		strHTML +=	  "<form id=\"box_form\" name=\"box_form\" method=\"POST\" target=\"ibox_img\" style='display:none;'>";
		strHTML +=	  "</form>";
		
		var docBody = document.getElementsByTagName("body")[0];
		
		new Insertion.Bottom(docBody, strHTML);
		
		Event.observe('ibox_Close2', 'click', function(event){			
			document.location.reload();
			mybox.hideIbox(mybox.btnClose);
			
		}, false);
		Event.observe('ibox_w', 'click', function(event){
			//mybox.hideIbox(mybox.btnClose);
			//alert(event.id);
			alert("먼저 팝업을 닫아주세요.");
		}, false);
	},
	// type : 1 : 이미지
	// type : 2 : #
 	// type : 3 : ajax
	// type : 4 : iframe
	service : function(url, title, divHeight, divWidth, type, input, userFunction, btn, title_str)
	{
		hideSelectBoxes();
		hideFlash();
		this.userFunction = userFunction;
		this.btn = btn;
		this.input = input;
		this.btn = btn;
		this.btnConfirm = null;
		this.btnSave	= null;
		this.btnClose = null;
		this.btnCancel = null;
		this.btnMove	= null;
		var arrayPageSize = getPageSize();
		Element.setWidth($('ibox_w'), arrayPageSize[0]);
		Element.setHeight($('ibox_w'), arrayPageSize[1]);		
		new Effect.Appear($('ibox_w'), { duration: overlayDuration, from: 0.0, to: overlayOpacity });
		
		document.body.style.overflow = "hidden";
		Element.update($('ibox_title'), title_str);
		// 타이틀 입력 부분
		//this.setTitle(title);
		// 타이틀 입력 부분 끝
		if (type > -1 && type < 5)
		{
			
			this.iboxType(type, url, divHeight, divWidth, userFunction)
			
		}
		return ;
	},
	iboxType : function(type, url, divHeight, divWidth, userFunction) {
		var typeFunction;
		var loading = "<table width=\"100%\" height=\"100%\"><tr><td align=\"center\" valign=\"middle\"><img src=\"/admin/images/indicator.gif\">";
		
		loading += "<!--object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" width=\"300\" height=\"50\" id=\"falsh_\" align=\"middle\"> ";
		loading += "<param name=\"allowScriptAccess\" value=\"always\" /> ";
		loading += "<param name=\"movie\" value=\"/admin/images/ing.swf\" /> ";
		loading += "<param name=\"quality\" value=\"high\" /> ";
		loading += "<param name=\"wmode\" value=\"Transparent\" /> ";
		loading += "<param name=\"bgcolor\" value=\"#ffffff\" /> ";
		loading += "<embed src=\"/admin/images/ing.swf\" quality=\"high\" bgcolor=\"#ffffff\" width=\"300\" height=\"50\" name=\"flash_\" align=\"middle\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" /> ";
		loading += "</object--></td></tr></table>";

		Element.update($('ibox_content'),loading);
		switch(type) {
			case 1:
				break;
			case 2:
					typeFunction = function() {
					Element.update($('ibox_content'),url);
					userFunction();
				}
				break;
			case 3:
				break;
			case 4:
				typeFunction = function() {
					
					var strHTML = '<iframe id="ibox_iframe" name="ibox_iframe" frameborder="0" style="width:100%;height:99%;border:0;cursor:hand;margin:0;padding:0;position:absolute;"/>';
					$('ibox_content').innerHTML = strHTML;
					var inputHTML = '';
					
					if (mybox.input != null) {
						mybox.input.each(function(inputData){
							var data = inputData.split("=");
							for (var i = 0 ; i < data.length ; i++) {
								if (data[i].substr(0,1) == " ")
								{
									data[i] = data[i].substr(1, data[i].length);
								}
								if (data[i].substr(data[i].length-1,1) == " ")
								{
									data[i] = data[i].substr(0, data[i].length-1);
								}
							}
							inputHTML +=  '<input type="hidden"';
							inputHTML +=	'id="'+data[0]+'" ';
							inputHTML +=	'name="'+data[0]+'" ';
							inputHTML +=	'value="'+data[1]+'">';
						})
					}
					new Insertion.Top($('box_form'),inputHTML);
					
					$('box_form').action = url;
					$('box_form').target = "ibox_iframe";
					$('box_form').method= "POST";
					$('box_form').submit();
					Element.update($('box_form'),'');
					userFunction();
				}
				break;
		}
		this.showIbox(divHeight, divWidth, typeFunction, true);
		this.clickEvent();
		return;
	},
	setTitle : function(title) {
		var image = '<!--IMG SRC="/images/ps/popup_PS_01.gif" WIDTH="67" HEIGHT="16" ALT="" BORDER="0"-->';
		if( title.isNum())
		{
			if( titleImage)
			{
				if(titleImage.length > title)
				{
					image = titleImage[title];
				}
			}
		}
		else 
		{
			image = title;
		}
		Element.update($('ibox_title'), image);
	},
	showIbox : function(divHeight, divWidth, typeFunction, firstAction) {
		// BackGround 처리 부분
		var arrayPageSize = getPageSize();
		// BackGround 처리 부분 끝
		var arrayPageScroll = getPageScroll();
		var lightboxTop = arrayPageScroll[1] + (arrayPageSize[3] / 10);
		var lightboxLeft = arrayPageScroll[0];
		Element.setTop($('ibox_wrapper'), lightboxTop);
		Element.setLeft($('ibox_wrapper'), lightboxLeft);		
//		new Effect.Appear($('ibox_wrapper'), {duration : 0.0});
		Element.show($('ibox_wrapper'));
		this.widthCurrent = Element.getWidth($('ibox_content'));
		this.heightCurrent = Element.getHeight($('ibox_content'));
		var widthNew = (parseInt(divWidth)  + (borderSize * 2));
		var heightNew = (parseInt(divHeight)  + (borderSize * 2) - 130);
		this.xScale = ( widthNew / this.widthCurrent) * 100;
		this.yScale = ( heightNew / this.heightCurrent) * 100;
		wDiff = this.widthCurrent - widthNew;
		hDiff = this.heightCurrent - heightNew;
		//alert(wDiff);
		if (wDiff >= -20 && wDiff <= 20)
			wDiff = 0;
		var showFunction = function() {
			if (firstAction){
				if (typeFunction != undefined) {
					typeFunction();
				}
			}
		}
		if(!( hDiff == 0)){ new Effect.Scale($('ibox_content'), this.yScale, {scaleX: false, duration: resizeDuration, queue: 'front'}); }
		if(!( wDiff == 0)){
			new Effect.Scale($('ibox_content'), this.xScale, {scaleY: false, delay: resizeDuration, duration: resizeDuration, afterFinish : showFunction});
		} else {
			if (showFunction != undefined) {
					showFunction();
			}
		}
	},
	clickEvent : function()
	{
			Event.observe('ibox_Close', 'click', function(event){
				mybox.hideIbox(mybox.btnClose);
			}, false);
	},
	hideIbox : function(finishFunction) {
		if (finishFunction != null && finishFunction != Prototype.emptyFunction)
		{
			if (finishFunction != Prototype.emptyFunction) {
				if (!finishFunction()) {
						return;
				}
			}
		}
//		Effect.DropOut($('ibox_wrapper'), {beforeStart : Prototype.emptyFunction});
//	Effect.DropOut($('ibox_wrapper'), {afterFinish : function(){$('ibox_wrapper').style.display = 'none'}});
	//	Effect.SlideUp($('ibox_wrapper'));
	//	Effect.Fold($('ibox_wrapper'));
		Element.hide($('ibox_wrapper'));
	//	Effect.Squish($('ibox_wrapper'));
		
		
		new Effect.Fade($('ibox_w'), { duration: overlayDuration});
		showSelectBoxes();
		showFlash();
		if ($('ibox_iframe') != undefined) {
			$('ibox_iframe').src = "";
		}
//		$('ibox_content').innerHTML = "";
//		alert($('ps_calendar'));
		if ($('ps_calendar') != undefined) {
			removeCalendar();
		}
		
		$('ibox_content').innerHTML = "";
		window.setTimeout("document.body.style.overflow = 'auto';$('ibox_content').style.width = '250px';$('ibox_content').style.height = '250px';",4500);
	//	$('ibox_content').style.width = '250px';
	//	$('ibox_content').style.height = '250px';
	}
}
function initbox() {
	mybox = new box();
	
}


Event.observe(window, 'load', initbox, false);


//
// getPageScroll()
// Returns array with x,y page scroll values.
// Core code from - quirksmode.com
//
function getPageScroll(){

	var xScroll, yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
		xScroll = self.pageXOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
		xScroll = document.documentElement.scrollLeft;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
		xScroll = document.body.scrollLeft;
	}

	arrayPageScroll = new Array(xScroll,yScroll)
	return arrayPageScroll;
}


// -----------------------------------------------------------------------------------
//
// getPageSize()
// Returns array with page width, height and window width, height
// Core code from - quirksmode.com
// Edit for Firefox by pHaez
//
function getPageSize(){

	var xScroll, yScroll;

	if (window.innerHeight && window.scrollMaxY) {
		xScroll = window.innerWidth + window.scrollMaxX;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}

	var windowWidth, windowHeight;

	if (self.innerHeight) {	// all except Explorer
		if(document.documentElement.clientWidth){
			windowWidth = document.documentElement.clientWidth;
		} else {
			windowWidth = self.innerWidth;
		}
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}

	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else {
		pageHeight = yScroll;
	}

//	console.log("xScroll " + xScroll)
//	console.log("windowWidth " + windowWidth)

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){
		pageWidth = xScroll;
	} else {
		pageWidth = windowWidth;
	}
//	console.log("pageWidth " + pageWidth)

	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight)
	return arrayPageSize;
}

// -----------------------------------------------------------------------------------

// ---------------------------------------------------

function showSelectBoxes(){
	var selects = document.getElementsByTagName("select");
	for (i = 0; i != selects.length; i++) {
//		selects[i].style.visibility = "visible";
		Element.show(selects[i]);
	}
}

// ---------------------------------------------------

function hideSelectBoxes(){
	var selects = document.getElementsByTagName("select");
	for (i = 0; i != selects.length; i++) {
//		selects[i].style.visibility = "hidden";
		Element.hide(selects[i]);
	}
}

// ---------------------------------------------------

function showFlash(){
	var flashObjects = document.getElementsByTagName("object");
	for (i = 0; i < flashObjects.length; i++) {
		flashObjects[i].style.visibility = "visible";
	}
	var flashEmbeds = document.getElementsByTagName("embed");
	for (i = 0; i < flashEmbeds.length; i++) {
		flashEmbeds[i].style.visibility = "visible";
	}
}

// ---------------------------------------------------

function hideFlash(){
	var flashObjects = document.getElementsByTagName("object");
	for (i = 0; i < flashObjects.length; i++) {
		flashObjects[i].style.visibility = "hidden";
	}

	var flashEmbeds = document.getElementsByTagName("embed");
	for (i = 0; i < flashEmbeds.length; i++) {
		flashEmbeds[i].style.visibility = "hidden";
	}

}

Object.extend(Element, {
	getWidth: function(element) {
	   	element = $(element);
	   	return element.offsetWidth;
	},
	getTop: function(element) {
		element = $(element);
	   	return element.offsetTop;
	},
	getLeft: function(element) {
		element = $(element);
	   	return element.offsetLeft;
	},
	setWidth: function(element,w) {
	   	element = $(element);
    	element.style.width = w +"px";
	},
	setHeight: function(element,h) {
   		element = $(element);
    	element.style.height = h +"px";
	},
	setTop: function(element,t) {
	   	element = $(element);
    	element.style.top = t +"px";
	},
	setLeft: function(element,l) {
	   	element = $(element);
    	element.style.left = l +"px";
	},
	setSrc: function(element,src) {
    	element = $(element);
    	element.src = src;
	},
	setHref: function(element,href) {
    	element = $(element);
    	element.href = href;
	},
	setInnerHTML: function(element,content) {
		element = $(element);
		element.innerHTML = content;
	}
});
