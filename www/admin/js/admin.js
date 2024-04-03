var mybox;
timer = null; // 타이머
timerW = null; // 타이머
sizeH = 0;    // 최대치 초기화
sizeW = 0;    // 최대치 초기화
speed = 10;   // 갱신속도
 sW = 0;
 

function readCookie(cookiename) 
{
	
	var Found = false;

	//쿠키 값 검사 추가 kbk 12/01/25
	var begin, end;
	var i = 0;
	while(i <= document.cookie.length) {
		begin = i;
		end = begin + cookiename.length;
		if(document.cookie.substring (begin,end) == cookiename) {
			Found = true;
		}
		i++;
	}
	//쿠키 값 검사 추가 kbk 12/01/25
  
	cookiedata = document.cookie; 
	if ( cookiedata.indexOf(cookiename) > 0 ){ 
	Found = true;

	}

	return Found;
} 

function setHeight(size) {
	sizeH = size; //최대치 값 적용
	myObj = document.getElementById("objPopupLayer"); // 개체의 구성 요소 가져옴 
	curHeight = parseInt(myObj.style.height); // 오브젝트의 현재 높이를 구함
	if(curHeight < sizeH) { // 최대치보다 작으면
		//myObj.style.height = ((sizeH - curHeight)/20) + curHeight; // 증가
		myObj.style.height = curHeight + 20; // 증가
		timer = setTimeout("setHeight(sizeH)", speed);
	// speed 만큼의 시간이 지난 후에 다시 함수 호출
	}else{
		clearTimeout(timer); //최대치 이상이면 그만
	}
}

function setWidth(size) {
	sizeW = size; //최대치 값 적용
	myObj = document.getElementById("objPopupLayer"); // 개체의 구성 요소 가져옴 
	curWidth = parseInt(myObj.style.width); // 오브젝트의 현재 높이를 구함
	if(curWidth < sizeW) { // 최대치보다 작으면
		//myObj.style.width = ((sizeW - curWidth)/20) + curWidth; // 증가
		myObj.style.width = curWidth + 20; // 증가
		timerW = setTimeout("setWidth(sizeW)", speed);
	// speed 만큼의 시간이 지난 후에 다시 함수 호출
	}else{
		clearTimeout(timerW); //최대치 이상이면 그만
	}
}


/*** 레이어 팝업창 띄우기 ***/
function popupLayer(s,w,h)
{
	if (!w) w = 600;
	if (!h) h = 400;

	var pixelBorder = 3;
	var titleHeight = 12;
	w += pixelBorder * 2;
	h += pixelBorder * 2 + titleHeight;

	var bodyW = document.body.clientWidth;
	var bodyH = document.body.clientHeight;

	var posX = (bodyW - w) / 2;
	var posY = (bodyH - h) / 2;

	hiddenSelectBox('hidden');

	/*** 백그라운드 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = 0;
		top = 0;
		width = "100%";
		height = document.body.scrollHeight;
		//backgroundColor = "#000000";
		backgroundColor = "#ffffff";
		filter = "Alpha(Opacity=10)";
		opacity = "0.1";
	}
	obj.id = "objPopupLayerBg";
	document.body.appendChild(obj);

	/*** 내용프레임 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";		
		left = posX + document.body.scrollLeft;
		top = posY + document.body.scrollTop;
		width = '100%';	//w;
		height = '100%';	//h;
		backgroundColor = "#FFFFFF";
		//border = "3px solid #000000";
		border = "3px solid orange";
	}
	obj.id = "objPopupLayer";
	document.body.appendChild(obj);
	
	sizeH = h;
	sizeW = w;
	setHeight(sizeH);
	setWidth(sizeW);

	/*** 타이틀바 레이어 ***/
	var bottom = document.createElement("div");
	with (bottom.style){
		position = "absolute";
//		width = w - pixelBorder * 2;
//		height = titleHeight;
		left = 0;
		top = h - titleHeight - pixelBorder * 3;
		padding = "2px 0 0 0";
		textAlign = "right";
		backgroundColor = "gray";
		color = "#ffffff";
		font = "bold 11px tahoma";
	}
	bottom.innerHTML = "<a href='javascript:parent.document.location.reload();closeLayer()' class=white>close</a>&nbsp;&nbsp;&nbsp;";
	obj.appendChild(bottom);

	/*** 아이프레임 ***/
	var ifrm = document.createElement("iframe");
	
	with (ifrm.style){
		width = '100%';//w - 6;
		height = '100%';//h - pixelBorder * 2 - titleHeight - 3;
		overflow = 'hidden';
		//border = "3 solid #000000";
	}
	
	ifrm.frameBorder = 0;
	ifrm.src = s;
	//ifrm.className = "scroll";
	obj.appendChild(ifrm);
//	alert(h+"::"+w);
	
	
}

function closeLayer()
{
	hiddenSelectBox('visible');
	_ID('objPopupLayer').parentNode.removeChild( _ID('objPopupLayer') );
	_ID('objPopupLayerBg').parentNode.removeChild( _ID('objPopupLayerBg') );
}

function _ID(obj){return document.getElementById(obj)}

function hiddenSelectBox(mode)
{
	var obj = document.getElementsByTagName('select');
	for (i=0;i<obj.length;i++){
		obj[i].style.visibility = mode;
	}
}


function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function AddTab(TabNum){
	document.getElementById('tab'+TabNum).innerHTML = "<div style=\"padding-top:10px;padding-left:10px;width:141px;Height:30px;background-image:url('./basic/image/UI_graytab.gif')\">탭이름</div>";	
}

function msgwin(url,mwidth,mheight,obj){
	if (document.all&&window.print)
		return eval('window.showModalDialog(url,obj,"help:0;resizable:1;dialogWidth:'+mwidth+'px;dialogHeight:'+mheight+'px; resizable:no; status:no; help:no; scroll:no;")');
	else
		eval('window.open(url,obj,"width='+mwidth+'px,height='+mheight+'px,resizable=0,scrollbars=no")')
}

function PoPWindow(url,width,height,vname){
    aWindow = window.open(url,vname, 'menubar=no,status=no,toolbar=no,resizable=no,width='+width+',height='+height+',titlebar=no,scrollbars=no,alwaysRaised=yes');
}
function PoPWindow3(url,width,height,vname){
    aWindow = window.open(url,vname, 'menubar=no,status=no,toolbar=no,resizable=no,width='+width+',height='+height+',titlebar=no,scrollbars=yes,alwaysRaised=yes');
}

function PopSWindow(url,width,height,vname){
	
	//2014-09-12 Hong 익스에서 여러창 뛰우기 위해서!
	if(!vname){
		vname='';
	}

	aWindow = window.open(url,'', 'menubar=no,status=no,toolbar=no,resizable=yes,width='+width+',height='+height+',titlebar=no,scrollbars=yes,alwaysRaised=yes');
}

function PopSWindow2(url,width,height,vname){
    aWindow = window.open(url,vname, 'menubar=no,status=no,toolbar=no,resizable=no,width='+width+',height='+height+',titlebar=no,scrollbars=no,alwaysRaised=yes');
}

function ShowModalWindow(url,width,height,vname,reload_bool){
	
	var ua = window.navigator.userAgent;
	if(ua.indexOf('MSIE') > 0 || ua.indexOf('Trident') > 0){
		 aWindow = window.showModalDialog(url,self, 'dialogWidth='+width+'px;dialogHeight='+height+'px;scroll: yes;');
		if(!reload_bool){
			if(typeof aWindow != "undefined" && aWindow) document.location.reload();//showModalDialog를 사용할 경우 부모창 새로 고침을 위해 추가 kbk 13/08/03
		}
	}else{
		aWindow = window.open(url,vname, 'menubar=no,status=no,toolbar=no,resizable=no,width='+width+',height='+height+',titlebar=no,scrollbars=no,alwaysRaised=yes');
	}

   
	return aWindow;
}

function ShowModalWindowWork(url,width,height,vname){
    aWindow = window.showModalDialog(url,window, 'dialogWidth='+width+'px;dialogHeight='+height+'px;scroll: yes;');
	return aWindow;
}

function PrintWindow(url,width,height,vname){
    aWindow = window.open(url,vname, 'menubar=yes,status=no,toolbar=no,resizable=no,width='+width+',height='+height+',titlebar=no,scrollbars=yes,alwaysRaised=yes');
}

function PoPWindow2(url,width,height,vname){
    aWindow = window.open(url,vname, 'menubar=no,status=no,toolbar=no,resizable=no,width='+width+',height='+height+',left=50, top=50,titlebar=no,scrollbars=no,alwaysRaised=yes');
}

function MainPopUpWindow(mypage, w, h, t, l, myname, scroll) {
	
	if(t == 0 && l == 0){
		var winl = (screen.width - w) / 2;
		var wint = (screen.height - h) / 2;
	}else{
		var winl = l;
		var wint = t;
	}
	winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',menubar=no,status=no,toolbar=no,resizable=yes'
	win = window.open(mypage, myname, winprops)
	//if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
}

function full_popup(url) 
{
 params  = 'width='+screen.width;
 params += ', height='+screen.height;
 params += ', top=0, left=0';
 params += ', fullscreen=yes';
 params += ', resizable=yes';
 params += ', scrollbars=yes';
 newwin=window.open(url,'full_popup', params);
 if (window.focus) {newwin.focus()}
 return false;
}

function generate_flash(file_, width_, height_){		
	var mstring="";
	mstring = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="'+width_+'" height="'+height_+'" id="falsh_" align="middle"> \n';
	mstring += '<param name="allowScriptAccess" value="always" /> \n';
	mstring += '<param name="movie" value="'+file_+'" /> \n';
	mstring += '<param name="quality" value="high" /> \n';
	mstring += '<param name="wmode" value="Transparent" /> \n';
	mstring += '<param name="bgcolor" value="#ffffff" /> \n';
	mstring += '<embed src="'+file_+'" quality="high" bgcolor="#ffffff" width="'+width_+'" height="'+height_+'" name="flash_" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" wmode="transparent" /> \n';
	mstring += '</object> \n';
	
	document.write(mstring);
}


function generate_flash2(file_, width_, height_){		
	var mstring="";
	
	mstring = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="'+width_+'" height="'+height_+'" id="falsh_" align="middle"> \n';
	mstring += '<param name="allowScriptAccess" value="always" /> \n';
	mstring += '<param name="movie" value="'+file_+'" /> \n';
	mstring += '<param name="quality" value="high" /> \n';
	mstring += '<param name="wmode" value="Transparent" /> \n';
	mstring += '<param name="bgcolor" value="#ffffff" /> \n';
	mstring += '<embed src="'+file_+'" quality="high" bgcolor="#ffffff" width="'+width_+'" height="'+height_+'" name="flash_" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /> \n';
	mstring += '</object> \n';
	
	return (mstring);
}

function viewMenual(config_, width_, height_){
	file_ = "/admin/_manual/movie/controller.swf";
	config_ = "/admin/_manual/movie/"+config_;
	
	mstring = '<object id  ="flashMovie" codeBase ="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" height="'+height_+'" width="'+width_+'" classid  ="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" VIEWASTEXT>';
	mstring += '   <PARAM NAME="_cx"                VALUE="26">';
	mstring += '   <PARAM NAME="_cy"                VALUE="26">';
	mstring += '   <PARAM NAME="FlashVars"          VALUE="csConfigFile='+config_+'">';
	mstring += '   <PARAM NAME="Movie"              VALUE="'+file_+'?csConfigFile='+config_+'">';
	mstring += '   <PARAM NAME="Src"                VALUE="'+file_+'?csConfigFile='+config_+'">';
	mstring += '   <PARAM NAME="WMode"              VALUE="Window">';
	mstring += '   <PARAM NAME="Quality"            VALUE="high">';
	mstring += '   <PARAM NAME="SAlign"             VALUE="">';
	mstring += '   <PARAM NAME="Menu"               VALUE="-1">';
	mstring += '   <PARAM NAME="Base"               VALUE="">';
	mstring += '   <PARAM NAME="AllowScriptAccess"  VALUE="always">';
	mstring += '   <PARAM NAME="DeviceFont"         VALUE="0">';
	mstring += '   <PARAM NAME="EmbedMovie"         VALUE="0">';
	mstring += '   <PARAM NAME="BGColor"            VALUE="#FFFFFF">';
	mstring += '   <PARAM NAME="SWRemote"           VALUE="">';
	mstring += '   <PARAM NAME="MovieData"          VALUE="">';
	mstring += '   <PARAM NAME="SeamlessTabbing"    VALUE="1">';
	mstring += '   <EMBED id          ="EmbedflashMovie"';
	mstring += '          src         ="'+file_+'?csConfigFile='+config_+'"';
	mstring += '          flashvars   ="csConfigFile='+config_+'" ';
	mstring += '          quality     ="high" ';
	mstring += '          bgcolor     ="#FFFFFF"'; 
	mstring += '          width       ="'+width_+'" ';
	mstring += '          height      ="'+height_+'" ';
	mstring += '          type        ="application/x-shockwave-flash" ';
	mstring += '          pluginspace ="http://www.macromedia.com/go/getflashplayer">	';
	mstring += '   </EMBED>';
	mstring += '</OBJECT>';
	
	document.write(mstring);
}
function viewMenual2(config_, width_, height_){
	file_ = "/admin/_manual/movie/controller.swf";
	config_ = "/admin/_manual/movie/"+config_;
	
	mstring = '<object id  ="flashMovie" codeBase ="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" height="'+height_+'" width="'+width_+'" classid  ="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" VIEWASTEXT>';
	mstring += '   <PARAM NAME="_cx"                VALUE="26">';
	mstring += '   <PARAM NAME="_cy"                VALUE="26">';
	mstring += '   <PARAM NAME="FlashVars"          VALUE="csConfigFile='+config_+'">';
	mstring += '   <PARAM NAME="Movie"              VALUE="'+file_+'?csConfigFile='+config_+'">';
	mstring += '   <PARAM NAME="Src"                VALUE="'+file_+'?csConfigFile='+config_+'">';
	mstring += '   <PARAM NAME="WMode"              VALUE="Window">';
	mstring += '   <PARAM NAME="Quality"            VALUE="high">';
	mstring += '   <PARAM NAME="SAlign"             VALUE="">';
	mstring += '   <PARAM NAME="Menu"               VALUE="-1">';
	mstring += '   <PARAM NAME="Base"               VALUE="">';
	mstring += '   <PARAM NAME="AllowScriptAccess"  VALUE="always">';
	mstring += '   <PARAM NAME="DeviceFont"         VALUE="0">';
	mstring += '   <PARAM NAME="EmbedMovie"         VALUE="0">';
	mstring += '   <PARAM NAME="BGColor"            VALUE="#FFFFFF">';
	mstring += '   <PARAM NAME="SWRemote"           VALUE="">';
	mstring += '   <PARAM NAME="MovieData"          VALUE="">';
	mstring += '   <PARAM NAME="SeamlessTabbing"    VALUE="1">';
	mstring += '   <EMBED id          ="EmbedflashMovie"';
	mstring += '          src         ="'+file_+'?csConfigFile='+config_+'"';
	mstring += '          flashvars   ="csConfigFile='+config_+'" ';
	mstring += '          quality     ="high" ';
	mstring += '          bgcolor     ="#FFFFFF"'; 
	mstring += '          width       ="'+width_+'" ';
	mstring += '          height      ="'+height_+'" ';
	mstring += '          type        ="application/x-shockwave-flash" ';
	mstring += '          pluginspace ="http://www.macromedia.com/go/getflashplayer">	';
	mstring += '   </EMBED>';
	mstring += '</OBJECT>';
	
	//document.write(mstring);
	return mstring;
}

function mouseOnTD(seq, bool,txt){
	//var oTD = eval("document.all.Report" + seq);
	//var len = oTD.length;
	var borderStyle = "1px solid gray";
	if (bool){
		/*for(var i =0; i < len ; i++){
			oTD[i].style.borderTop = borderStyle;
			oTD[i].style.borderBottom = borderStyle;
			oTD[i].style.cursor = "default";
			
		}*/
		$("tr[id^="+txt+"]:eq("+seq+")>td").css("borderTop",borderStyle);
		$("tr[id^="+txt+"]:eq("+seq+")>td").css("borderBottom",borderStyle);
		//oTD[seq].style.cursor = "default";
		/*oTD[0].style.borderLeft = borderStyle;
		oTD[0].style.backgroundColor = "";
		oTD[len-1].style.borderRight = borderStyle;*/
		/*$("tr[id^=Report]:eq(0)>td").css("borderTop",borderStyle);
		$("tr[id^=Report]:eq(0)>td").css("backgroundColor","");
		$("tr[id^=Report]:eq("+(len-1)+")>td").css("borderBottom",borderStyle);*/
	}else{
		/*for(var i =0; i < len; i++){
			oTD[i].style.border = "";
		}*/
		$("tr[id^="+txt+"]:eq("+seq+")>td").css("border","");
		//oTD[0].style.backgroundColor = "";
	}
}



// <-- Fade In, Out
var tInC=null;
var tIdC=null;
var tIdCOn = new Array(0,0,0,0,0,0,0); 
var tIdCOff = new Array(1,1,1,1,1,1,1);


function FIn(obj, col, idNum) {
 if(tInC != obj && tInC != null && tIdCOn[idNum] == 0) FOut(tInC,tIdC); 
 if(tIdCOn[idNum] == 0) {
  tIdCOn[idNum] = 1;
  tIdCOff[idNum] = 0;
  tInC=obj;
  tIdC=idNum;
  changeColor(obj, col);
 }
}
function FOut(obj, col, idNum) {
 if(tIdCOff[idNum] == 0) {
  tIdCOff[idNum] = 1;
  tIdCOn[idNum] = 0;
  changeColor(obj, col);
 }
}


function changeColor(obj, col) {
 obj.filters.blendTrans.apply(); 
 obj.style.backgroundColor= col;
 obj.filters.blendTrans.play(); 
}
// Fade In, Out -->


// <-- Display Sub
var previd = null;
  function displaySub(subID)
  {
    if (previd != null){
      if (previd != subID){
        previd.style.display = "none";
      }
    }
    if (subID.style.display == "none"){
      subID.filters.blendTrans.Apply();
      subID.style.display = '';
      subID.filters.blendTrans.Play()
    }
      else{
        subID.filters.blendTrans.Apply();
        subID.style.display = 'none';
        subID.filters.blendTrans.Play()
      }
    previd = subID;
  }

  function displaySub1(subID)
  {
    if (previd != null){
      if (previd != subID){
        previd.style.display = "none";
      }
    }
    if (subID.style.display == "none"){
      subID.filters.blendTrans.Apply();
      subID.style.display = '';
      subID.filters.blendTrans.Play()
    }
      else{
        subID.filters.blendTrans.Apply();
        subID.style.display = 'none';
        subID.filters.blendTrans.Play()
      }
    previd = subID;
  }

  function displaySub2(subID)
  {
    if (previd != null){
      if (previd != subID){
        previd.style.display = "none";
      }
    }
    if (subID.style.display == "none"){
      subID.filters.blendTrans.Apply();
      subID.style.display = '';
      subID.filters.blendTrans.Play()
    }
    previd = subID;
  }

 function displaySub3(subID) {
  if (subID.style.display == 'none'){
    subID.filters.blendTrans.Apply();
    subID.style.display = '';
    subID.filters.blendTrans.Play()
    }
    else {
      subID.filters.blendTrans.Apply();
      subID.style.display = 'none';
      subID.filters.blendTrans.Play()
      }
    previd = subID;
  }

  function displaySubw(subID)
  {
    if (previd != null){
      if (previd != subID){
        previd.style.display = "none";
      }
    }
    if (subID.style.display == "none"){
      subID.filters.blendTrans.Apply();
      subID.style.display = '';
      subID.filters.blendTrans.Play()
    }
      else{
        subID.filters.blendTrans.Apply();
        subID.style.display = 'none';
        subID.filters.blendTrans.Play()
      }
    previd = subID;
  }
// Display Sub -->



function FormatNumber2(num){
        // 만든이:김인현(jasmint@netsgo.com)
        fl=""
        if(isNaN(num)) { /*alert("문자는 사용할 수 없습니다.");*/return 0}
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

function FormatNumber(num){
        num=new String(num)
        num=num.replace(/,/gi,"")
      //  pricecheckmode = false;
        
        return FormatNumber2(num)
}


//콤마표현 없는 정수만입력
function onlyNumber(obj){
 var str = obj.value;
 str = new String(str);
 var Re = /[^0-9|-|.]/g;
 str = str.replace(Re,''); 
 obj.value = str;
}

function onlyNumberByRefund(obj){
    var str = obj.value;
    str = new String(str);
    var Re = /[^0-9\-.]/g;
    str = str.replace(Re,'');
    obj.value = str;
}


/*
' ------------------------------------------------------------------
' Function    : fc_chk_byte(aro_name)
' Description : 입력한 글자수를 체크
' Argument    : Object Name(글자수를 제한할 컨트롤)
' Return      : 
' ------------------------------------------------------------------
*/
function fc_chk_byte(aro_name,ari_max, view_length)
{

   var ls_str     = aro_name.value; // 이벤트가 일어난 컨트롤의 value 값
   var li_str_len = ls_str.length;  // 전체길이

   // 변수초기화
   var li_max      = ari_max; // 제한할 글자수 크기
   var i           = 0;  // for문에 사용
   var li_byte     = 0;  // 한글일경우는 2 그밗에는 1을 더함
   var li_len      = 0;  // substring하기 위해서 사용
   var ls_one_char = ""; // 한글자씩 검사한다
   var ls_str2     = ""; // 글자수를 초과하면 제한할수 글자전까지만 보여준다.

   for(i=0; i< li_str_len; i++)
   {
      // 한글자추출
      ls_one_char = ls_str.charAt(i);

      // 한글이면 2를 더한다.
      if (escape(ls_one_char).length > 4)
      {
         li_byte += 2;
      }
      // 그밗의 경우는 1을 더한다.
      else
      {
         li_byte++;
      }

      // 전체 크기가 li_max를 넘지않으면
      if(li_byte <= li_max)
      {
         li_len = i + 1;
      }
   }
   
   // 전체길이를 초과하면
   if(li_byte > li_max)
   {
      alert( li_max + " byte 를 초과 입력할수 없습니다. \n초과된 내용은 자동으로 삭제 됩니다. ");
      ls_str2 = ls_str.substr(0, li_len);
      
      aro_name.value = ls_str2;
      //alert(aro_name.focusbool);
   }else{
   	view_length.value = li_byte;
   }
   aro_name.focus();   
}

function fc_chk_lms(aro_name,ari_min,ari_max, view_length, type)
{

   var ls_str     = aro_name.value; // 이벤트가 일어난 컨트롤의 value 값
   var li_str_len = ls_str.length;  // 전체길이

   // 변수초기화
   var li_min	   = ari_min; // SMS 제한할 글자수 크기
   var li_max      = ari_max; // LMS 제한할 글자수 크기
   var i           = 0;  // for문에 사용
   var li_byte     = 0;  // 한글일경우는 2 그밗에는 1을 더함
   var li_len      = 0;  // substring하기 위해서 사용
   var ls_one_char = ""; // 한글자씩 검사한다
   var ls_str2     = ""; // 글자수를 초과하면 제한할수 글자전까지만 보여준다.
   var text_array  = new Array();
   var text_array0 = "";
   var text_array1 = "";
   var text_array2 = "";

   var text_array_lan = "0";
   for(i=0; i< li_str_len; i++)
   {
      // 한글자추출
      ls_one_char = ls_str.charAt(i);

      // 한글이면 2를 더한다.
      if (escape(ls_one_char).length > 4)
      {
         li_byte += 2;
      }
      // 그밗의 경우는 1을 더한다.
      else
      {
         li_byte++;
      }

      // 전체 크기가 li_max를 넘지않으면
      if(li_byte <= li_max)
      {
         li_len = i + 1;
      }

	  if(li_byte < 75){
		text_array0 = text_array0 + ls_one_char;
		//console.log(text_array);
		//text_array_lan += 1;
	  }else if(li_byte > 74 && li_byte < 145){
		text_array1 = text_array1 + ls_one_char;
		
		//console.log(text_array);
	  }else if(li_byte > 144 && li_byte < 220){
		text_array2 = text_array2 + ls_one_char;
		//console.log(text_array);
	  }
   }
   
	$('#sms_text_array').val(''+text_array0+'^|^'+text_array1+'^|^'+text_array2+'');
   // SMS  길이를 초과하면
   if(li_byte > li_min)
   {	
	   
     if(type == 'sms'){
		  $("#lms_title").show();
		  document.getElementById("byte").innerHTML ="1000byte";
		  document.getElementById("msg_type").innerHTML ="LMS";
		  document.getElementById("select_sms_type").innerHTML ="<strong style='color:red;'>타입 선택</strong> : <input type='radio' name='select_sms_type' value='SMS'>SMS <input type='radio' name='select_sms_type' value='LMS' checked>LMS";
		  document.getElementById("lms_type").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='3'>";
	  }else if(type =='mms'){
		  document.getElementById("mms_byte").innerHTML ="1000byte";
		  document.getElementById("mms_msg_type").innerHTML ="LMS";
		  document.getElementById("select_sms_type").innerHTML ="<strong style='color:red;'>타입 선택</strong> : <input type='radio' name='select_sms_type' value='SMS'>SMS <input type='radio' name='select_sms_type' value='LMS' checked>LMS";
		  document.getElementById("mms_lms_type").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='3'>";
	  }else if(type =='nmb'){
		  document.getElementById("nmb_byte").innerHTML ="1000byte";
		  document.getElementById("nmb_msg_type").innerHTML ="LMS";
		  document.getElementById("select_sms_type").innerHTML ="<strong style='color:red;'>타입 선택</strong> : <input type='radio' name='select_sms_type' value='SMS'>SMS <input type='radio' name='select_sms_type' value='LMS' checked>LMS";
		  document.getElementById("nmb_lms_type").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='3'>";
	  }
   
   }

   if(li_byte < li_min)
   {
	  
	   if(type == 'sms'){
		   $("#lms_title").hide();
		  document.getElementById("byte").innerHTML ="80byte";
		  document.getElementById("msg_type").innerHTML ="SMS";
		  //document.getElementById("select_sms_type").innerHTML ="타입 선택 : <input type='radio' name='select_sms_type' value='SMS' checked>SMS <input type='radio' name='select_sms_type' value='LMS' >LMS";
		  document.getElementById("select_sms_type").innerHTML ="";
		  document.getElementById("lms_type").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='1'>";
	  }else if(type =='mms'){
		  document.getElementById("mms_byte").innerHTML ="80byte";
		  document.getElementById("mms_msg_type").innerHTML ="SMS";
		  //document.getElementById("select_sms_type").innerHTML ="타입 선택 : <input type='radio' name='select_sms_type' value='SMS' checked>SMS <input type='radio' name='select_sms_type' value='LMS' >LMS";
		  document.getElementById("select_sms_type").innerHTML ="";
		  document.getElementById("mms_lms_type").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='1'>";
	  }else if(type =='nmb'){
		  document.getElementById("nmb_byte").innerHTML ="80byte";
		  document.getElementById("nmb_msg_type").innerHTML ="SMS";
		  //document.getElementById("select_sms_type").innerHTML ="타입 선택 : <input type='radio' name='select_sms_type' value='SMS' checked>SMS <input type='radio' name='select_sms_type' value='LMS' >LMS";
		  document.getElementById("select_sms_type").innerHTML ="";
		  document.getElementById("nmb_lms_type").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='1'>";
	  }
   
   }
   
   if(li_byte > li_max)
   {
      alert( li_max + " byte 를 초과 입력할수 없습니다. \n초과된 내용은 자동으로 삭제 됩니다. ");
      ls_str2 = ls_str.substr(0, li_len);
      
      aro_name.value = ls_str2;
      //alert(aro_name.focusbool);
   }else{
   	view_length.value = li_byte;
   }
   aro_name.focus();   
}


$(document).ready(function(){
	$('body').on({
		click : function(e){

			var check_select_sms = $('input[type=radio][name=select_sms_type]:checked').val();
			//alert(check_select_sms);
			var check_max_byte = $('input[type=text][name=sms_text_count]').val();
			if(check_select_sms == 'SMS' && check_max_byte > 219){
				alert('SMS 타입 전송은 220byte 를 넘을 수 없습니다.');
				e.preventDefault();
			}
			
		}
	},'input[type=radio][name=select_sms_type]')
});


function fc_chk_lms1(aro_name,ari_min,ari_max, view_length)
{

   var ls_str     = aro_name.value; // 이벤트가 일어난 컨트롤의 value 값
   var li_str_len = ls_str.length;  // 전체길이

   // 변수초기화
   var li_min	   = ari_min; // SMS 제한할 글자수 크기
   var li_max      = ari_max; // LMS 제한할 글자수 크기
   var i           = 0;  // for문에 사용
   var li_byte     = 0;  // 한글일경우는 2 그밗에는 1을 더함
   var li_len      = 0;  // substring하기 위해서 사용
   var ls_one_char = ""; // 한글자씩 검사한다
   var ls_str2     = ""; // 글자수를 초과하면 제한할수 글자전까지만 보여준다.

   for(i=0; i< li_str_len; i++)
   {
      // 한글자추출
      ls_one_char = ls_str.charAt(i);

      // 한글이면 2를 더한다.
      if (escape(ls_one_char).length > 4)
      {
         li_byte += 2;
      }
      // 그밗의 경우는 1을 더한다.
      else
      {
         li_byte++;
      }

      // 전체 크기가 li_max를 넘지않으면
      if(li_byte <= li_max)
      {
         li_len = i + 1;
      }
   }
   
   // SMS  길이를 초과하면
   if(li_byte > li_min)
   {
      //alert( li_min + " byte 를 초과되면 LMS로 전환됩니다");
	  document.getElementById("byte1").innerHTML ="1000byte";
	  document.getElementById("msg_type1").innerHTML ="LMS";
	  document.getElementById("lms_type1").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='3'>";
   
   }

   if(li_byte < li_min)
   {
	  document.getElementById("byte1").innerHTML ="80byte";
	  document.getElementById("msg_type1").innerHTML ="SMS";
	  document.getElementById("lms_type1").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='1'>";
   
   }
   
   if(li_byte > li_max)
   {
      alert( li_max + " byte 를 초과 입력할수 없습니다. \n초과된 내용은 자동으로 삭제 됩니다. ");
      ls_str2 = ls_str.substr(0, li_len);
      
      aro_name.value = ls_str2;
      //alert(aro_name.focusbool);
   }else{
   	view_length.value = li_byte;
   }
   aro_name.focus();   
}

function fc_chk_lms2(aro_name,ari_min,ari_max, view_length)
{

   var ls_str     = aro_name.value; // 이벤트가 일어난 컨트롤의 value 값
   var li_str_len = ls_str.length;  // 전체길이

   // 변수초기화
   var li_min	   = ari_min; // SMS 제한할 글자수 크기
   var li_max      = ari_max; // LMS 제한할 글자수 크기
   var i           = 0;  // for문에 사용
   var li_byte     = 0;  // 한글일경우는 2 그밗에는 1을 더함
   var li_len      = 0;  // substring하기 위해서 사용
   var ls_one_char = ""; // 한글자씩 검사한다
   var ls_str2     = ""; // 글자수를 초과하면 제한할수 글자전까지만 보여준다.

   for(i=0; i< li_str_len; i++)
   {
      // 한글자추출
      ls_one_char = ls_str.charAt(i);

      // 한글이면 2를 더한다.
      if (escape(ls_one_char).length > 4)
      {
         li_byte += 2;
      }
      // 그밗의 경우는 1을 더한다.
      else
      {
         li_byte++;
      }

      // 전체 크기가 li_max를 넘지않으면
      if(li_byte <= li_max)
      {
         li_len = i + 1;
      }
   }
   
   // SMS  길이를 초과하면
   if(li_byte > li_min)
   {
      //alert( li_min + " byte 를 초과되면 LMS로 전환됩니다");
	  document.getElementById("byte2").innerHTML ="1000byte";
	  document.getElementById("msg_type2").innerHTML ="LMS";
	  document.getElementById("lms_type2").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='3'>";
   
   }

   if(li_byte < li_min)
   {
	  document.getElementById("byte2").innerHTML ="80byte";
	  document.getElementById("msg_type2").innerHTML ="SMS";
	  document.getElementById("lms_type2").innerHTML ="<input type='hidden' name='send_type' style='width:100px' value='1'>";
   
   }
   
   if(li_byte > li_max)
   {
      alert( li_max + " byte 를 초과 입력할수 없습니다. \n초과된 내용은 자동으로 삭제 됩니다. ");
      ls_str2 = ls_str.substr(0, li_len);
      
      aro_name.value = ls_str2;
      //alert(aro_name.focusbool);
   }else{
   	view_length.value = li_byte;
   }
   aro_name.focus();   
}

/*
' ------------------------------------------------------------------
' Function    : fc_chk2()
' Description : Enter키를 못치게한다.
' Argument    : 
' Return      : 
' ------------------------------------------------------------------
*/
function fc_chk2()
{
   if(event.keyCode == 13)
      event.returnValue=false;
}


function CheckSpecialChar(obj){
	
	/*
	if (!((event.keyCode >= 48 && event.keyCode <= 90) || (event.keyCode >= 37 && event.keyCode <= 40) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 46 )){		//|| event.keyCode == 16 
		alert("특수문자는 입력할 수 없습니다.["+event.keyCode+"]");
		event.returnValue=false;
		obj.focus();
	}
	*/
	
	var strSpecial = "`~!@#$%^&*()_+|\;\\/:=-<>[]{},.'\""; 
	var expression = obj.value;

	for(i=0;i<expression.length;i++){
		//for(j=0;j<strSpecial.length;j++){
			//if(expression.charAt(i) == strSpecial.charAt(j)){
			if (strSpecial.indexOf(expression.charAt(i)) != -1) {
				//return false;   // 특수문자가 있으면.. false값을 돌려보냅니다.
				alert('특수문자는 입력하실수 없습니다.');
				obj.value = expression.substr(0,expression.length-1)
			}
		//}
	}
}




function inFocus1(i) {
	(i).style.border='2px solid orange';
}

function outFocus1(i) {
	(i).style.border='1px solid #cccccc';
}

function LogininFocus(i) {
	(i).style.border='3px solid orange';
	(i).style.height='22px';
}

function LoginoutFocus(i) {
	(i).style.border='1px solid #cccccc';
	(i).style.height='22px';
}

function inArray( needle, haystack )
{
	for ( i = 0; i < haystack.length; i++ )
		if ( haystack[i] == needle ) return true;
	return false;
}

/* 브라우저별 이벤트 처리*/
function addEvent(obj, evType, fn){
	if (obj.addEventListener) {
		obj.addEventListener(evType, fn, false);
		return true;
	} else if (obj.attachEvent) {
		var r = obj.attachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}

function delEvent(obj, evType, fn){
	if (obj.removeEventListener) {
		obj.removeEventListener(evType, fn, false);
		return true;
	} else if (obj.detachEvent) {
		var r = obj.detachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}

function getTargetElement(evt)
{
	if ( evt.srcElement ) return target_Element = evt.srcElement; // 익스
	else return target_Element = evt.target; // 익스외
}

/*** 포커스 테두리 넣기 ***/
function linecss(){
	var obj = document.getElementsByTagName('input');
	var obj_txa = document.getElementsByTagName('textarea');
	for( e =0; e < obj.length; e++ ){
		var type = obj[e].getAttribute('type');
		if( type == 'text' || type == 'password' || type == 'file'){
			var isClsnm = false;
			
			var clsnm = obj[e].className.toString().split(' ');
			//alert("'"+obj[e].className+"'");
			for (c = 0; c < clsnm.length; c++){
				if (inArray(clsnm[c], Array('lline', 'line', 'rline', 'cline', 'loginline','textbox'))) isClsnm = true;
			}
			if (isClsnm === true){
				addEvent(obj[e], 'focus', function(e) { inFocus1(getTargetElement(e)); });
				addEvent(obj[e], 'blur', function(e) { outFocus1(getTargetElement(e)); });
			}

			//obj[e].onfocus = function(){
			//	this.select();
			//}
		}

	}
	
	for( t =0; t < obj_txa.length; t++ ){
		var clsnm = obj_txa[t].className.toString().split(' ');
		
		if (inArray("tline", clsnm)){
			//alert(clsnm);
			addEvent(obj_txa[t], 'focus', function(e) { inFocus1(getTargetElement(e)); });
			addEvent(obj_txa[t], 'blur', function(e) { outFocus1(getTargetElement(e)); });
		}
	}
}





function LayerShow(obj_id){
	//$.blockUI.defaults.css = {}; 
	if(screen.height < 1000){
		$.blockUI({ message: $('#'+obj_id), css: { backgroundColor:'transparent', textAlign: 'center', width: '0px' , height: '0px' ,padding:  '0px', border: '0px ', top:'5%', left:'25%'} }); 
	}else{
		$.blockUI({ message: $('#'+obj_id), css: { backgroundColor:'transparent', textAlign: 'center', width: '0px' , height: '0px' ,padding:  '0px', border: '0px ', top:'25%', left:'25%'} }); 
	}
}

function LayerClose(){
	$.unblockUI(); 
}

function gnb_toggle(){
	//alert($('.global_navigation_menu').css('display'));
	if($('.global_navigation_menu').css('display') == "block" || $('.global_navigation_menu').css('display') == "table-row"){
		$('.global_navigation_menu').hide();
		$('#unimind_logo_main').hide();
		$('#unimind_logo_sub').show();
		$('#unimind_menu_controal_text').text('상단메뉴열기');
		$('#unimind_leftmenu').css('top','-28px');
		$.cookie('global_navigation_menu', '1', {expires:1,domain:document.domain, path:'/', secure:0});
	}else{
		$('.global_navigation_menu').show();
		$('#unimind_logo_main').show();
		$('#unimind_logo_sub').hide();
		$('#unimind_menu_controal_text').text('상단메뉴닫기');
		$('#unimind_leftmenu').css('top','');
		$.cookie('global_navigation_menu', '0', {expires:1,domain:document.domain, path:'/', secure:0});

	}
	
}


//콤마표현 없는 정수만입력
function onlyEditableNumber(obj){
 var str = obj.value;
 var new_str = '';
 str = new String(str);
 //alert(str);
 var Re = /[^0-9]/g;  
 new_str = str.replace(Re,''); 
 
	if(new_str){
		obj.value = parseInt(new_str);
	}else if(str){
		obj.value = new_str;
	}
}

$(document).ready(function(){
	$('.top_menu_img').parent().css({'height':'53px'});

	var slideProduct = setInterval('productScroll("div_slideProduct", -93)', 3000);
		$('#div_slideProductBox').hover(function()	{
			
			clearInterval(slideProduct);
		}, function()
		{
			slideProduct = setInterval('productScroll("div_slideProduct", -93)', 3000);
		});

	var slideProduct2 = setInterval('productScroll2("div_slideProduct2", -186)', 3000);
		$('#div_slideProductBox2').hover(function()	{
			
			clearInterval(slideProduct2);
		}, function()
		{
			slideProduct2 = setInterval('productScroll2("div_slideProduct2", -186)', 3000);
		});
});

function productScroll(obj, w){
	
	speed = (arguments[2])	?	arguments[2]:1000;
	if(w > 0)	{
		$('ul#'+obj).find('li.good_names:first-child').before($('ul#'+obj).find('li.good_names:last-child').clone());
		$('ul#'+obj).find('li.good_names:last-child').remove();
		$('ul#'+obj).css('marginLeft','-93px');
		w = 0;
		
	}
	$('ul#'+obj).animate({marginLeft:w+'px'}, speed, null, function()	{
		if(w < 0)	{
			$(this).find('li.good_names:first-child').clone().appendTo($(this));
			$(this).find('li.good_names:first-child').remove();
			$(this).css('marginLeft',0);
		}	else	{

		}
	});
	
}

function productScroll2(obj, w){
	
	speed = (arguments[2])	?	arguments[2]:1000;
	if(w > 0)	{
		$('ul#'+obj).find('li.good_names:first-child').before($('ul#'+obj).find('li.good_names:last-child').clone());
		$('ul#'+obj).find('li.good_names:last-child').remove();
		$('ul#'+obj).css('marginLeft','-93px');
		w = 0;
		
	}
	$('ul#'+obj).animate({marginLeft:w+'px'}, speed, null, function()	{
		if(w < 0)	{
			$(this).find('li.good_names:first-child').clone().appendTo($(this));
			$(this).find('li.good_names:first-child').remove();
			$(this).css('marginLeft',0);
		}	else	{

		}
	});
	
}



function MenuTreeView(target_obj, menu_name){

	$.ajax({ 
		type: 'GET', 
		data: 
			{'act': 'getLeftmenuData', 'leftmenu':menu_name},  
		url: '/admin/v3/include/get_menu_data.php',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 
			 //alert(1);
		},  
		error:function(request,status,error){
			alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
		},
		success: function(tree_datas){
				//document.write(tree_datas);
				treeData = tree_datas;
				
		} 
	}); 
	$("#btnExpandAll").click(function(){
		
		$('#'+target_obj).dynatree("getRoot").visit(function(node){
			node.expand(true);
		});
		 return false;
    });

	//autoCollapse: true, 다른노드가 열리면 자동으로 열려 있던 노드가 닫힘
	$('#'+target_obj).dynatree({
			checkbox: false,
			selectMode: 1,
			children: treeData,			
			autoFocus: false,
			persist: true,
			  
			onQueryExpand: null, 
			onSelect: function(select, node) {
				//alert("onSelect");
			},
			onClick: function(node, event) {
				//alert("onClick :"+ node.data.href);
				try{
				$.blockUI.defaults.css = {}; 
				$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} }); 
				}catch(e){document.location.href=node.data.href;}

				if(node.data.href){
					document.location.href=node.data.href;
					//window.open(node.data.href, node.data.target);
				}
			},
			onDblClick: function(node, event) {
				node.toggleSelect();
			},
			onKeydown: function(node, event) {
				//alert("onKeydown");
				if( event.which == 32 ) {
					node.toggleSelect();
					return false;
				}
			},
			onActivate: function(node){
				
				//alert("onActivate :"+ node.data.href);
				try{
					$.blockUI.defaults.css = {}; 
					$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} }); 
				}catch(e){document.location.href=node.data.href;}

				if(node.data.href){
					document.location.href=node.data.href;
					//window.open(node.data.href, node.data.target);
				}
				
			},
			onQuerySelect: function(flag, node){
				
				//alert("onQuerySelect : "+node.data.href);
				try{
				$.blockUI.defaults.css = {}; 
				$.blockUI({ message: $('#loading'), css: { width: '100px' , height: '100px' ,padding:  '10px'} }); 
				}catch(e){document.location.href=node.data.href;}

				if(node.data.href){
					document.location.href=node.data.href;
					//window.open(node.data.href, node.data.target);
				}
			},
				 
			onPostInit: function(isReloading, isError) {
			 //alert("onPostInit");
			},

			cookieId: 'dynatree-'+target_obj,
			idPrefix: 'dynatree-'+target_obj+'-'
		});

}

function substitudeCheck(obj){
	// 소수점 rateFloor 자리까지만 입력 가능하도록 처리

	var rateFloor = 10; // 입력가능 자릿수 10:소수점 1자리, 100:소수점 2자리
	var rateMax = 100; // 최대 입력가능
	var rate = $(obj).val();
	var rateAfter = Math.floor(rate * rateFloor) / rateFloor;
	var rateSplit = rate.split(".");
	if(rateSplit.length > 1){
		if(rateSplit[1].length > 1){
			$(obj).val(rateAfter);
		}
	}
	if(parseInt(rate) > rateMax){
		alert(rateMax+"% 까지만 입력 가능합니다.");
		$(obj).val(rateMax);
	}
}