function mouseOnTD(seq, bool){
	//var oTD = eval("document.all.Report" + seq);
	var oTD = document.getElementById("Report" + seq);
	//var len = oTD.length;
	//var len = oTD.getElementsByTagName("td").length;
	var txt="#Report"+seq+">td"
	var len = $(txt).length;
	var borderStyle = "1px solid slategray";

	/*if (bool){
		for(var i =0; i < len ; i++){
			oTD[i].style.borderTop = borderStyle;
			oTD[i].style.borderBottom = borderStyle;
			oTD[i].style.cursor = "default";
		}
		oTD[0].style.borderLeft = borderStyle;
		oTD[0].style.backgroundColor = "";
		oTD[len-1].style.borderRight = borderStyle;
	}else{
		for(var i =0; i < len; i++){
			oTD[i].style.border = "";
		}
		oTD[0].style.backgroundColor = "";
	}*/
	if(bool) {
		for(var i =0; i < len ; i++){
			$(txt)[i].style.cursor = "pointer";
			$(txt)[i].style.backgroundColor = "";
			$(txt)[i].style.borderTop = borderStyle;
			$(txt)[i].style.borderBottom = borderStyle;
			if(i==0) $(txt)[0].style.borderLeft = borderStyle;
			if(i==(len-1)) $(txt)[i].style.borderRight = borderStyle;
		}
	} else {
		for(var i =0; i < len ; i++){
			$(txt)[i].style.cursor = "";
			$(txt)[i].style.backgroundColor = "";
			$(txt)[i].style.border = "";
		}
	}
}

// <-- Show, Hide DIV
var x = 0;
var y = 0;
var snow = 0;
var sw = 0;
var cnt = 0;
var dir = 1;
var offsetx = 0;
var offsety = 0;
var width = 200;
var height = 50;

//document.onmousemove = mouseMove;

function outdp() {
if ( cnt >= 1 ) { sw = 0 };
if ( sw == 0 ) { snow = 0; hideObject(over); }
else { cnt++; }
}

function overdp(obj) {
parm = eval(obj);
over = parm.style;
if (snow == 0) {
	if (dir == 2) { moveTo(over,x+offsetx-(width/2),y+offsety); } // Center
	if (dir == 1) { moveTo(over,x+offsetx,y+offsety); } // Right
	if (dir == 0) { moveTo(over,x-offsetx-width,y+offsety); } // Left
	showObject(over);
	snow = 1;
}
}

function mouseMove(e) {
	x=event.x + document.body.scrollLeft+10
	y=event.y + document.body.scrollTop
	if (x+width-document.body.scrollLeft > document.body.clientWidth) x=x-width-25;
	if (y+height-document.body.scrollTop > document.body.clientHeight) y=y-height;
	
	if (snow) {
		if (dir == 2) { moveTo(over,x+offsetx-(width/2),y+offsety); } // Center
		if (dir == 1) { moveTo(over,x+offsetx,y+offsety); } // Right
		if (dir == 0) { moveTo(over,x-offsetx-width,y+offsety); } // Left
	}
}

function cClick() { hideObject(over); sw=0; }
function layerWrite(txt) { document.all["resisttext"].innerHTML = txt }
function showObject(obj) { obj.visibility = "visible" }
function hideObject(obj) { obj.visibility = "hidden" }
function moveTo(obj,xL,yL) { obj.left = xL; obj.top = yL; }

if (document.all) 
{ 
layerRef='document.all' 
styleRef='.style.' 
} 
else if (document.layers) 
{ 
layerRef='document.layers' 
styleRef='.' 
}

// show DIV
function showLayer(lname) {

eval(layerRef+'["'+lname+'"]'+styleRef+'display="block"');
}

// hide DIV
function hideLayer(lname) {

eval(layerRef+'["'+lname+'"]'+styleRef+'display="none"');
}
// Show, Hide DIV -->