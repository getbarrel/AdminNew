//eyesys dhtml  (c)eyecon 2002 [ eyecon@webteam.ro ]
//visit www.webteam.ro for great scripts and tutorials

var ie5=window.createPopup

if (ie5)
//document.getElementById(DSpreadSheet1').oncontextmenu=init;
var eyesys="";
var sysmen = "";
var preitem="";
var strContextMenu = "" ;
var SelectedID = "";

function init2(vid){	
	SelectedID = vid;
	mx=event.clientX;
	my=event.clientY;
	menx=window.screenLeft+mx;
	meny=window.screenTop+my-20;
	sysmen=window.createPopup();
	sysmen.document.write(eyesys);
	
	sysmen.show(menx,meny,eyesys_width,document.getElementById('men').offsetHeight-29);
	
	return false;
};

function eyesys_init(){
	if (ie5){
		eyesys+= "<style type='text/css'>.gnbSubMenu{font-size:11px;font-family:굴림};.textul{position:absolute;top:0px;color:"+eyesys_titletext+";writing-mode:	tb-rl;padding-top:10px;z-Index:10;width:100%;height:100%;font: bold 12px sans-serif}.gradientul{position:relative;top:0px;left:0px;width:100%;background-color:"+eyesys_titlecol2+";height:100%;z-Index:9;FILTER: alpha( style=1,opacity=0,finishOpacity=100,startX=100,finishX=100,startY=0,finishY=100)}.contra{background-color:"+eyesys_titlecol1+";border:0px inset "+eyesys_bg+";height:98%;width:18px;z-Index:8;top:0px;left:0px;margin:2px;position:absolute;}.men{position:absolute;top:0px;left:0px;padding-left:0px;background-color:"+eyesys_bg+";border:0px outset "+eyesys_bg+";z-Index:1;}.men a{margin:1px;cursor:default;padding-bottom:4px;padding-left:1px;padding-right:1px;padding-top:3px;text-decoration:none;height:100%;width:100%;color:"+eyesys_cl+";font:normal 12px sans-serif;}.men a:hover{background:"+eyesys_bgov+";color:"+eyesys_clov+";} BODY{overflow:hidden;border:0px;padding:0px;margin:0px;}.ico{border:none;float:left;}</style><div id='men2' class='men'>";
	}
};

function eyesys_item(txt,ico,lnk){
	if (ie5){
		if(!ico)ico='s.gif';
		preitem+=("<a href='#' onmousedown=\""+lnk+"\" ondragstart='return false;'>"+txt+"</a>")
	
	}
};

function eyesys_close(){
	if (ie5){	
	generateGNBLayerContextMenu('storeleft', subMenus_storeleft);
	
	eyesys+= strContextMenu.replace('\n','') ; //preitem; 
	eyesys+=("</div>");
	document.write("<div id='men' ondragstart='return false;' style='width:"+eyesys_width+";border:1px solid #000000'></div>");
	
	document.getElementById('men').innerHTML=strContextMenu
	}
}
