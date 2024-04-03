var maxW,maxH,fromX,fromY,toX,toY,adjX,adjY,zBox,zStep=0,zLink,zNew,imgID;
var n = (document.layers) ? 1:0;
var ie = (document.all) ? 1:0;

function overTip(boxObj) {
	if (ie) {
		boxObj.style.visibility = "visible";
		boxObj.filters.item(0).stop();
	}
}

function outTip(boxObj) {
	if (ie) {
		boxObj.style.visibility = "visible";
		boxObj.filters.item(0).transition = 12;
		boxObj.filters.item(0).apply();
		boxObj.style.visibility = "hidden";
		boxObj.filters.item(0).play();
	}
}

function zoomBox(evt,zlink,maxw,maxh,tox,toy,imgid)
{
	if (arguments.length > 2) zNew=1;

	scrollH = (window.pageYOffset!=null) ? window.pageYOffset : document.body.scrollTop;
	maxW = maxw ? maxw : window.innerWidth ? innerWidth :document.body.clientWidth;
	maxH = maxh ? maxh : window.innerHeight ? innerHeight : document.body.clientHeight;
	toX = tox ? tox : 0;
	toY = (toy ? toy : 0) + scrollH;
	fromX = evt.pageX ? evt.pageX : evt.clientX;
	fromY = (evt.pageY ? evt.pageY : evt.clientY) + (document.all ? scrollH : 0);
	adjX = toX + evt.screenX - fromX;
	adjY= toY + evt.screenY - fromY;

	if (document.createElement && document.body.appendChild && !zBox) 
	{
		zBox=document.createElement("div");
		zBox.style.position="absolute";
		document.body.appendChild(zBox);
	}
	else if (document.all && !zBox) 
	{
		document.all[document.all.length-1].outerHTML+='<div id="zBoxDiv" style="position:absolute"></div>';
		zBox=document.all.zBoxDiv;
	}
	else if (document.layers && !zBox) 
	{
		zBox=new Layer(maxW);
		zBox.style=zBox;
	}
	
	zLink=zlink;
	imgID=imgid;
	
	doZoom();
}

function doZoom() 
{
	zStep+=1;
	zPct=(10-zStep)/10

	if (document.layers)
	{
		zBox.moveTo(toX+zPct*(fromX-toX),toY+zPct*(fromY-toY));
		zBox.document.open();
		zBox.document.write("<table width='"+maxW*(1-zPct)+"' height="+maxH*(1-zPct)+" border=2 cellspacing=0><tr><td><img src='data/15/hma304_1.jpg'></td></tr></table>");
		zBox.document.close();
	} 
	else 
	{
		zBox.style.border="1px solid #000000"; // zoom 두께 및 색상
		zBox.style.left=toX+zPct*(fromX-toX);
		zBox.style.top=toY+zPct*(fromY-toY);
		zBox.style.width=maxW*(1-zPct);
		zBox.style.height=maxH*(1-zPct);
		zBox.style.zIndex=150;
	}

	zBox.style.visibility="visible";

	if (zStep < 10) setTimeout("doZoom("+fromX+","+fromY+","+toX+","+toY+")",30);	// 10단계 루프
	else 
	{
		zBox.style.visibility='hidden';
		zStep=0;
		if (zLink && !zNew) 
		{
			overTip(imgID);
			//location.href=zLink.href;
		}
		else if (zLink && zNew)
		{			
			overTip(imgID);
			//var w=window.open(zLink.href,'','width='+maxW+',height='+maxH+',left='+adjX+',top='+adjY+',scrollbars,resizable');
			zNew=null;
		}
	}
}

function gotClick(arg) 
{
	evt = arg ? arg : event;
	evtFrom = evt.target ? evt.target : evt.srcElement;

	if (evtFrom.parentNode) evtFrom = evtFrom.parentNode.href ? evtFrom.parentNode : evtFrom;

	if (evtFrom.href && !evtFrom.onclick) 
	{
		zoomBox(evt,evtFrom);
		return false;
	} 
	else 
	{
		if (document.routeEvent) document.routeEvent(evt);
		if (evtFrom.href) return false;
	}
}

function getClicks() 
{
	if (document.layers) document.captureEvents(Event.CLICK);
	document.onclick=gotClick;
}

//window.onload=getClicks;