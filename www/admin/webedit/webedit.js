var viewMode = 1;
var font_name = '' ;

function Init(frm)
{
	var iStype;
   
 
	var _iView = document.getElementById("iView").contentWindow;
	_iView.document.designMode = 'On';
 	_iView.document.open();
 
	iStype ="<html xmlns='http://www.w3.org/1999/xhtml'>";//추가 kbk
	iStype += "<head>";//추가 kbk
	iStype +="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
	iStype += "<style>";
	iStype += "P {margin-top:0px;margin-bottom:0px;line-height:140%;};";
	iStype += "body {font-size:14pt;font-family:돋움;line-height:140%;};";//추가 kbk
	iStype += ".bottom_line{border-bottom:1px solid gray;font-family:돋움;font-size:12px;}";
	iStype += ".sheet_title{font-family:굴림체;font-size:12px;}";
	iStype += ".sheet_contents{font-family:굴림체;font-size:12px;}";
	iStype += ".sheet_text{font-family:굴림체;font-size:12px;}";
	iStype += "</style></head><body></body></html>";

   _iView.document.write(iStype) ;
   if( frm.content.value.length > 0 )
   {
   		//alert(frm.content.value.replace(/\{mall_data_root\}/g,frm.mall_data_root.value));
      _iView.document.write(frm.content.value ) ;		
   }
   else
   {
      _iView.document.write("<p>&nbsp;</p>");
   }
   _iView.document.close();// 브라우저가 계속 로드중으로 뜨는 현상을 해결하기 위해 씀 2011-04-06 kbk

	//alert(_iView.document.getElementsByTagName('style'));
   //주석 kbk	_iView.document.body.style.fontSize = "14pt";
   //주석 kbk	_iView.document.body.style.fontFamily = "굴림";
}

function selOn(ctrl)
{
   ctrl.style.borderColor = '#000000';
   ctrl.style.backgroundColor = '#B5BED6';
   ctrl.style.cursor = 'pointer';
}

function selOff(ctrl)
{
   ctrl.style.borderColor = '#D6D3CE';  
   ctrl.style.backgroundColor = '#D6D3CE';
}

function selDown(ctrl)
{
   ctrl.style.backgroundColor = '#8492B5';
}

function selUp(ctrl)
{
   ctrl.style.backgroundColor = '#B5BED6';
}

function doBold()
{
	var _iView = document.getElementById("iView").contentWindow;
   _iView.document.execCommand('bold', false, null);
   _iView.focus() ;
}

function doItalic()
{
	var _iView = document.getElementById("iView").contentWindow;
   _iView.document.execCommand('italic', false, null);
   _iView.focus() ;
}

function doUnderline()
{
	var _iView = document.getElementById("iView").contentWindow;
   _iView.document.execCommand('underline', false, null);
   _iView.focus() ;
}

function doLeft()
{
	var _iView = document.getElementById("iView").contentWindow;
   _iView.document.execCommand('justifyleft', false, null);
   _iView.focus() ;
}

function doCenter()
{
	var _iView = document.getElementById("iView").contentWindow;
   _iView.document.execCommand('justifycenter', false, null);
   _iView.focus() ;
}

function doRight()
{
	var _iView = document.getElementById("iView").contentWindow;
   _iView.document.execCommand('justifyright', false, null);
   _iView.focus() ;
}

function doOrdList()
{
	var _iView = document.getElementById("iView").contentWindow;
   _iView.document.execCommand('insertorderedlist', false, null);
   _iView.focus() ;
}

function doBulList()
{
	var _iView = document.getElementById("iView").contentWindow;
   _iView.document.execCommand('insertunorderedlist', false, null);
   _iView.focus() ;
}

function doRule()
{
	var _iView = document.getElementById("iView").contentWindow;
   _iView.document.execCommand('inserthorizontalrule', false, null);
   _iView.focus() ;
}

// 테이블 삽입
function doTable()
{
   window.open('/admin/webedit/table.php', 'doTable', 'width=354,height=286,resizeable=yes,scrollbars=no,left=200, top=200');
}

// 링크걸기
function doLink()
{
   window.open('/admin/webedit/link.php', 'doLink', 'width=378,height=116,resizeable=yes,scrollbars=no,left=200, top=200');
}

// 멀티미디어 링크
function doMultilink(){
   window.open('/admin/webedit/multimedia.php', 'doMultilink', 'width=378,height=116,resizeable=yes,scrollbars=no,left=200, top=200');
}

// 이미지 삽입
function doImage()
{
   window.open('/admin/webedit/image.php', 'doImage', 'width=407,height=332,left=200, top=200');
}

// 글자색
function doForcol( type )
{
   window.open('/admin/webedit/font_color.php', 'doForcol', 'toolbar=no,scrollbars=no,menubar=no,width=210,height=350,left=200, top=200') ;
}

// 글자배경색
function doBgcol()
{
   window.open('/admin/webedit/font_bg.php', 'doBgcol', 'toolbar=no,scrollbars=no,menubar=no,width=210,height=360,left=200, top=200') ;
   
}

// 폰트타입
function doFont()
{
   window.open('/admin/webedit/font_type.php', 'doImage', 'width=215, height=150,left=200, top=200');
}

// 폰트사이즈
function doSize(fSize)
{
   window.open('/admin/webedit/font_size.php', 'doImage', 'width=300, height=230,left=200, top=200');
}

function doHead(hType)
{
	var _iView = document.getElementById("iView").contentWindow;
   if(hType != '')
   {
      _iView.document.execCommand('FormatBlock', false, hType); 
      doFont( font_name );
   }
   
   _iView.focus() ;
}
function doToggleView()
{  
	var _iView = document.getElementById("iView").contentWindow;
   if(viewMode == 1)
   {
      iHTML = _iView.document.body.innerHTML;
      _iView.document.body.innerText = iHTML;
      
      // Hide all controls
      document.getElementById("tblCtrls").style.display = 'none';
      _iView.focus();
      
      viewMode = 2;
   }
   else
   {
      iText = _iView.document.body.innerText;
      _iView.document.body.innerHTML = iText;
      
      // Show all controls
      document.getElementById("tblCtrls").style.display = 'inline';
      _iView.focus();
      
      viewMode = 1;
   }
   
   _iView.focus() ;
}

function doToggle(){
	if(frm.toggle.checked == true) doToggleHtml();
	else doToggleText();
}

function doToggleHtml(frm)
{  
	var _iView = document.getElementById("iView").contentWindow;
	if(viewMode != 2){
		iHTML = _iView.document.body.innerHTML;
		_iView.document.body.innerText = iHTML;
		// Hide all controls
		document.getElementById("tblCtrls").style.display = 'none';
		_iView.focus();      
		viewMode = 2;
	}   
   _iView.focus() ;
}

function doToggleText(frm)
{  
	var _iView = document.getElementById("iView").contentWindow;
	if(viewMode != 1){
		iText = _iView.document.body.innerText;
		_iView.document.body.innerHTML = iText;
		// Show all controls
		document.getElementById("tblCtrls").style.display = '';
		_iView.focus();
		viewMode = 1;
	}  
   _iView.focus() ;
}