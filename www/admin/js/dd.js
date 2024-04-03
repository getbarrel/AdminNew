/*******************************************************************************************
	drag and drop 자바스크립트 v0.050517.3

	작성일 : 2005. 5. 17.
	작성자 : hooriza (http://hooriza.com/)

	사용법 :

		<SCRIPT language="javascript" src="dd.js"></SCRIPT>
		<SCRIPT language="javascript" src="mozInnerHTML.js"></SCRIPT> 포함

		* mozInnerHTML.js 의 출처는 http://webfx.eae.net/dhtml/mozInnerHTML/mozInnerHtml.html
          모질라에서 제대로 동작하려면 mozInnerHTML.js 을 사용해야 함.

		드래그할 객체에 dragable="true" 속성 추가
		드랍을 받는 객체에 dropzone="true" 속성 추가

		드랍을 받는 객체에 필요에 따라
		ondragover(드래그 객체가 드랍 객체 위로 올라왔을때),
		ondragout(드래그 객체가 드랍 객체 밖으로 나갔을때),
		ondrop(드래그 객체가 드랍 되었을때)
		속성을 지정할 수 있음

		ondrop 으로 지정된 함수 내에서는 arguments[0] 을 사용
		해서 드래그 된 객체를 참조할 수 있음

	예  제 :
		<SPAN dragable>드래그 할 꺼</SPAN>
		<TABLE dropzone border=1

			ondragover="this.style.backgroundColor='green';"
			ondragout="this.style.backgroundColor='yellow';"
			ondrop="alert('드랍된 객체 -> '+arguments[0]+'\n'+arguments[0].outerHTML);"
		><TR><TD>여기에 드랍하세요</TD></TR></TABLE>

	IE 6.0 과 FF 1.0.3, Opera 8.01.7583 에서 테스트했습니다
*******************************************************************************************/

var browser;

if		(navigator.userAgent.indexOf("Gecko") > -1)		browser = "GECKO";
else if	(navigator.userAgent.indexOf("Opera") > -1)		browser = "OPERA";
else if	(navigator.userAgent.indexOf("MSIE") > -1)		browser = "MSIE";

var DRAG_KEYWORD = "dragable";
var DROP_KEYWORD = "dropzone";

var currentDragObj = null;
var currentDropZone = null;
var currentDragXPos = 0;
var currentDragYPos = 0;

var arrayDropZone = new Array();
var virtualDragObj = null; 
var attachStatus = true;

// ie 에뮬레이션 시작
function convertEventValues(e)
{
	if (browser != "MSIE") // not MSIE
	{
		var evt = new Array();

		evt.clientX = e.clientX;
		evt.clientY = e.clientY;

		evt.screenX = e.screenX;
		evt.screenY = e.screenY;

		evt.srcElement = e.target;
		evt.type = e.type;
		
		if (evt.type == "mousedown" || evt.type == "mouseup")
		{
			switch (e.button)
			{
			case 0:
				evt.button = 1;
				break;
			
			case 1:
				evt.button = 4;
				break;
			}
		}

		return evt;
	}

	return e;
}

/*if (window.attachEvent == undefined)
{
	//Window.prototype.attachEvent = HTMLDocument.prototype.attachEvent = HTMLElement.prototype.attachEvent =
	Window.prototype.attachEvent = HTMLDocument.prototype.attachEvent = HTMLElement.prototype.attachEvent;

	function(eventtype, func)
	{
		eventtype = eventtype.substr(2);
		this.addEventListener(eventtype, func, false);
	}
}*/
// ie 에뮬레이션 끝

function initializeDropZones()
{
	var alls = document.body.getElementsByTagName("div");

	for (var i = 0; i < alls.length; i++)
	{
		if (alls[i].getAttribute(DROP_KEYWORD) != null)
			arrayDropZone.push(alls[i]);
	}
}

function getOffsetLeft(obj)
{
	var retval = document.body.clientLeft ? document.body.clientLeft : 0;

	while (obj)
	{
		retval += obj.offsetLeft;
		obj = obj.offsetParent;
	}

	return retval;
}

function getOffsetTop(obj)
{
	var retval = document.body.clientTop ? document.body.clientTop : 0;

	while (obj)
	{
		retval += obj.offsetTop;
		obj = obj.offsetParent;
	}

	return retval;
}

function isDropZoneOver(xp, yp, obj)
{
	var _left = getOffsetLeft(obj);
	var _top = getOffsetTop(obj);

	var _right = _left + obj.offsetWidth;
	var _bottom = _top + obj.offsetHeight;

	return (xp >= _left && xp <= _right && yp >= _top && yp <= _bottom);
}

function findParentByAttribute(obj, attname)
{
	if (!obj) return null;

	if (!obj.getAttribute(attname))
		return findParentByAttribute(obj.parentElement, attname);
	
	return obj;
}

function convertStringToFunction(obj, attname)
{
	var func = obj.getAttribute(attname);
	
	if (typeof(func) == "string" && func.indexOf("function()") == -1){
		eval("obj." + attname + " = function() { " + func + " }");
	}
	
}

function cancelDrag()
{
	currentDragObj = null;
}

function sendMessageToDropZone(dropobj, isover)
{
	if (dropobj && currentDragObj)
	{
		convertStringToFunction(dropobj, isover ? "ondragover" : "ondragout");

		if (isover && dropobj.ondragover)	dropobj.ondragover();
		else if (dropobj.ondragout)			dropobj.ondragout();

		currentDropZone = (isover ? dropobj : null);
	}
}

function moveVirtualDragObj(xp, yp)
{
	var clipx, clipy;

	var maxy = document.body.scrollHeight;
	var maxx = document.body.scrollWidth;

	if (maxy < document.body.clientHeight) maxy = document.body.clientHeight;
	if (maxx < document.body.clientWidth) maxx = document.body.clientWidth;

	virtualDragObj.style.width = "auto";
	virtualDragObj.style.height = "auto";

	clipx = xp + virtualDragObj.offsetWidth - maxx;
	clipy = yp + virtualDragObj.offsetHeight - maxy;

	if (clipx < 0) clipx = 0;
	if (clipy < 0) clipy = 0;

	var vwidth = virtualDragObj.offsetWidth - clipx;
	var vheight = virtualDragObj.offsetHeight - clipy;

	if (vwidth < 0 || vheight < 0)
	{
		virtualDragObj.style.display = "none";
	}
	else
	{
		with (virtualDragObj.style)
		{
			width = vwidth;
			height = vheight;

			left = xp;
			top = yp;

			display = "inline";
			filter = "alpha(opacity=50)";
		}
	}
}

function debug(str)
{
	var output = document.getElementById("output");

	output.innerHTML = str;
}


function relationOnMouseOut(){
	
	try{
		if(!attachStatus){
			
			document.detachEvent("onmousedown", onMouseDown);
			document.detachEvent("onmousemove", onMouseMove);
			document.detachEvent("onmouseup", onMouseUp);	
			/*
			document.getElementById('relation_product_area').attachEvent("onmousedown", onMouseDown);
			document.getElementById('relation_product_area').attachEvent("onmousemove", onMouseMove);
			document.getElementById('relation_product_area').attachEvent("onmouseup", onMouseUp);
			*/
			document.getElementById('reg_product').innerHTML = "<table width=100% height=100%><tr><td align=center>좌측카테고리를 선택해주세요</td></tr></table>";
			attachStatus = true;
		}
	}catch(e){}
	
}
// eventListener

function onLoad()
{

	initializeDropZones();

	virtualDragObj = document.createElement("NOBR");

	with (virtualDragObj.style)
	{
		position = "absolute";
		display = "none";
		border = "0px";
		zIndex = 99999999;
	}

	document.body.appendChild(virtualDragObj);

	document.onmousedown = function() { return false; }
	
	if(attachStatus){
		
		document.attachEvent("onmousedown", onMouseDown);
		document.attachEvent("onmousemove", onMouseMove);
		document.attachEvent("onmouseup", onMouseUp);
		/*
		document.getElementById('relation_product_area').attachEvent("onmousedown", onMouseDown);
		document.getElementById('relation_product_area').attachEvent("onmousemove", onMouseMove);
		document.getElementById('relation_product_area').attachEvent("onmouseup", onMouseUp);
		*/
		attachStatus = false;
	}
	
}

function onMouseDown(e)
{
	e = convertEventValues(e);
	
	if (virtualDragObj.style.display != "none" || e.button != 1) return;

	var obj = e.srcElement;
	var dragobj = findParentByAttribute(obj, DRAG_KEYWORD);

	if (currentDragObj = dragobj)
	{
		var xp = e.clientX + document.body.scrollLeft;
		var yp = e.clientY + document.body.scrollTop + document.getElementById('reg_product').scrollTop;
		//alert(currentDragObj.scrollTop);
		//alert(e.clientX+"::::"+e.clientY);
		window.status = e.clientX+"::::"+e.clientY;
		virtualDragObj.innerHTML = currentDragObj.outerHTML;

		currentDragXPos = xp - getOffsetLeft(currentDragObj) + (document.body.clientLeft ? document.body.clientLeft : 0);
		currentDragYPos = yp - getOffsetTop(currentDragObj) ;//+ (document.body.clientTop ? document.body.clientTop : 0);
		
		onMouseMove(e);

		with (virtualDragObj.style)
		{
			display = "inline";
			overflow = "hidden";

			width = "auto";
			height = "auto";
		}

	}
}

function onMouseMove(e)
{
	
	e = convertEventValues(e);

	// alert(e.clientX);

	var xp = e.clientX + document.body.scrollLeft;
	var yp = e.clientY + document.body.scrollTop;
//alert(document.body.scrollTop);
	var dropzone = null;
	var minzindex = -1;

	if (browser == "MSIE" && e.button != 1) return onMouseUp();

	if (currentDragObj)
		moveVirtualDragObj(xp - currentDragXPos, yp - currentDragYPos);

	for (var i = 0; i < arrayDropZone.length; i++)
	{
		if (isDropZoneOver(xp, yp, arrayDropZone[i]))
		{
			var zindex = arrayDropZone[i].style.zIndex;

			if (zindex >= minzindex)
			{
				dropzone = arrayDropZone[i];
				minzindex = zindex;
			}
		}
	}

	if (dropzone != currentDropZone)
		sendMessageToDropZone(currentDropZone, false);

	if (dropzone)
		sendMessageToDropZone(dropzone, true);
}

function onMouseUp(e)
{
	e = convertEventValues(e);

	if (currentDropZone)
	{
		var dropzone = currentDropZone;
		
		sendMessageToDropZone(currentDropZone, false);
		
		if (currentDragObj && e.button == 1)
		{
			convertStringToFunction(dropzone, "ondrop");
			if (dropzone.ondrop) dropzone.ondrop(currentDragObj);
		}

		currentDragObj = null;
	}

	if (virtualDragObj)
	{
		virtualDragObj.innerHTML = "";
		virtualDragObj.style.display = "none";
	}
}

//window.attachEvent('onload', onLoad);

 //document.attachEvent("onselectstart", function() { return false; } );
