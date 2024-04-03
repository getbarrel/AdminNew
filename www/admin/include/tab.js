var lastTab = null;
//activated when pressing on the tab
function tabToggle(tabID){


	if ((lastTab != tabID) && (lastTab != null)){
		lastTab.style.zIndex = "1";
		lastTab.style.top = "3";
		//lastTab.style.backgroundImage='url(/manage/basic/image/UI_graytab.gif)';
		lastTab.style.fontWeight='bold';
		lastTab.style.paddingTop='2px';
    }
	if (tabID.style.zIndex == "3"){
      	tabID.style.zIndex = "11";
	  	tabID.style.top = "2";
		//tabID.style.backgroundImage='url(/manage/basic/image/UI_bluetab.gif)';
		tabID.style.fontWeight='bold';
		tabID.style.paddingTop='4px';
	}else {
		
		tabID.style.zIndex = 11; 		
		tabID.style.top = "2";		
		//tabID.style.backgroundImage='url(/manage/basic/image/UI_bluetab.gif)';
		tabID.style.fontWeight='bold';
		tabID.style.paddingTop='4px';		
		//document.getElementById(tabID.id+"_arrow").src="/img/bule_arrow.gif";		
   		lastTab = tabID;
    }
}

function tabToggle2(tabID){


	if ((lastTab != tabID) && (lastTab != null)){
		lastTab.style.zIndex = "1";
		lastTab.style.top = "3";
		//lastTab.style.backgroundImage='url(/manage/basic/image/UI_graytab.gif)';
		lastTab.style.fontWeight='normal';
		lastTab.style.paddingTop='2px';
    	}
	if (tabID.style.zIndex == "3")
		{
		
      	tabID.style.zIndex = "3";
	  	tabID.style.top = "0";
		//tabID.style.backgroundImage='url(/manage/basic/image/UI_bluetab.gif)';
		tabID.style.fontWeight='bold';
		tabID.style.paddingTop='4px';
		}
	else 
		{
		
		tabID.style.zIndex = "3"; 
		tabID.style.top = "0";
		//tabID.style.backgroundImage='url(/manage/basic/image/UI_bluetab.gif)';
		tabID.style.fontWeight='bold';
		tabID.style.paddingTop='4px';
   		lastTab = tabID;
    	}
}

function showLayers(num) {
	var divleft=new Array(0,25,40,10,20,40,10,40,290,90);
  for (i=1; i<10; i++) {   
    st = 'menu'+i;
    if (navigator.appName == 'Netscape' && document.layers != null) {
      theObj = document.layers[st];
      if (i==num) { if (theObj) theObj.visibility = 'show'; 
      	theObj.top=20;
      }
      else { if (theObj) theObj.visibility = 'hide'; }
    } else if (document.all != null) {
      theObj = document.all[st];
      
      if (i==num) { 
		if (theObj) { if((document.body.offsetWidth-780)>0) theObj.style.left=(divleft[i]+(document.body.offsetWidth-1000)/2);else theObj.style.left=divleft[i]; theObj.style.visibility = 'visible'; }
      }
      else { if (theObj) theObj.style.visibility = 'hidden'; }
  	}
  }
}
function getCookie(sName)
{
	var aCookie = document.cookie.split("; ");
	for (var i=0; i < aCookie.length; i++)
	{
		var aCrumb = aCookie[i].split("=");
		if (sName == aCrumb[0])
		return unescape(aCrumb[1]);
	}
	return null;
}

 //alert(top.boolhistory);
 //  alert(top.nhistory);
 //  alert(top.beforePage);