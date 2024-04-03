  function getGNBLinkColor(moduleID)
  {
  	 return "#F48E00";
  	/*
    if (moduleID == "www") return "#33AABF";
    if (moduleID == "hompy") return "#5EBAE1";
    if (moduleID == "storeleft") return "#5EBAE1";
    if (moduleID == "club") return "#F48E00";
    if (moduleID == "design") return "#DB713E";
    if (moduleID == "order") return "#A2B00E";
    if (moduleID == "sattlement") return "#7F7FD4";
    if (moduleID == "member") return "#5B8ABA";
    if (moduleID == "zeronine") return "#5EBAE1";
    if (moduleID == "marketting") return "#F48E00";
    if (moduleID == "static") return "#A2B00E";
    if (moduleID == "log") return "#7F7FD4";
    if (moduleID == "estimate") return "#33AABF";
    if (moduleID == "database") return "#5B8ABA";
    */
   
  }

  function getGNBLayerColor(moduleID)
  {
  	return "#FCE7C8";
  	
    if (moduleID == "store") return "#E1F0F6";
    if (moduleID == "storeleft") return "#E1F0F6";
    if (moduleID == "club") return "#FCE7C8";
    if (moduleID == "design") return "#F4E2D1";
    if (moduleID == "order") return "#E9EDC6";
    if (moduleID == "sattlement") return "#E6E5F6";
    if (moduleID == "member") return "#DFE7F2";
    if (moduleID == "zeronine") return "#E1F0F6";
    if (moduleID == "marketting") return "#FCE7C8";
    if (moduleID == "static") return "#E9EDC6";
    if (moduleID == "log") return "#E6E5F6";
    if (moduleID == "estimate") return "#E1F0F6";
    if (moduleID == "database") return "#DFE7F2";
	if (moduleID == "inventory") return "#DFE7F2";
  }

  function getGNBLayerHighlightColor(moduleID)
  {
  	return "#FF9900";
    if (moduleID == "store") return "#87C4E2";
    if (moduleID == "storeleft") return "#87C4E2";
    if (moduleID == "club") return "#FF9900";
    if (moduleID == "design") return "#E77D4A";
    if (moduleID == "order") return "#B6C422";
    if (moduleID == "sattlement") return "#918BE3";
    if (moduleID == "member") return "#6C9BCB";
    if (moduleID == "zeronine") return "#87C4E2";
    if (moduleID == "marketting") return "#FF9900";
    if (moduleID == "static") return "#B6C422";
    if (moduleID == "log") return "#918BE3";
    if (moduleID == "estimate") return "#87C4E2";
    if (moduleID == "database") return "#6C9BCB";
	if (moduleID == "inventory") return "#6C9BCB";
  }

  function generateGNBLayerMenu(moduleID, subMenu, menuID, strOption)
  {
  	//alert(subMenu);
    if (subMenu[0].substr(0, 7) == "http://")
      var href = "javascript:sayNavigate('"+subMenu[0].replace(/'/g, '\\\'')+"');"
    else
      var href = subMenu[0];
    
    if (subMenu[2] == "normal")
    	if(subMenu[3]){
      		return "<TD "+strOption+" ONMOUSEOVER=\"javascript:highlightLayer('"+moduleID+"', '"+menuID+"')\" ONMOUSEOUT=\"javascript:restoreLayer('"+moduleID+"', '"+menuID+"')\" STYLE=\"cursor:pointer\" CLASS=gnbSubMenu>&nbsp;<SPAN ID=text_"+menuID+" ONMOUSEDOWN=\""+href+"\" >" + subMenu[1] + "</SPAN> " + subMenu[3] + "&nbsp;&nbsp;</TD>";
    	}else{
    		return "<TD "+strOption+" ONMOUSEOVER=\"javascript:highlightLayer('"+moduleID+"', '"+menuID+"')\" ONMOUSEOUT=\"javascript:restoreLayer('"+moduleID+"', '"+menuID+"')\" ONMOUSEDOWN=\""+href+"\"  STYLE=\"cursor:pointer\" CLASS=gnbSubMenu>&nbsp;<SPAN ID=text_"+menuID+" >" + subMenu[1] + "</SPAN>&nbsp;&nbsp;</TD>";
    	}
    else if (subMenu[2] == "submenu")      
      return "<TD "+strOption+" ONMOUSEOVER=\"javascript:highlightLayer('"+moduleID+"', '"+menuID+"')\" ONMOUSEOUT=\"javascript:restoreLayer('"+moduleID+"', '"+menuID+"')\" ONMOUSEDOWN=\""+href+"\" STYLE=\"cursor:pointer;padding-left:10px;\" CLASS=gnbSubMenu>&nbsp;<SPAN ID=text_"+menuID+">" + subMenu[1] + "</SPAN>&nbsp;&nbsp;</TD>";
    else if (subMenu[2] == "special")
      return "<TD "+strOption+" ONMOUSEOVER=\"javascript:highlightLayer('"+moduleID+"', '"+menuID+"');document.all.arrow_"+menuID+".color='#FFFFFF';\" ONMOUSEOUT=\"javascript:restoreLayer('"+moduleID+"', '"+menuID+"');document.all.arrow_"+menuID+".color='#BFBFBF';\" ONMOUSEDOWN=\""+href+"\" STYLE=\"cursor:pointer\" CLASS=gnbSubMenu>&nbsp;<SPAN ID=text_"+menuID+"><FONT ID=arrow_"+menuID+" COLOR=#BFBFBF style='font-size:8pt;'>▶</FONT>" + subMenu[1] + "</SPAN>&nbsp;&nbsp;</TD>";
    else if (subMenu[2] == "topmenu")
      return "<TD "+strOption+" ONMOUSEOVER=\"javascript:highlightLayer('"+moduleID+"', '"+menuID+"');document.all.arrow_"+menuID+".color='#FFFFFF';\" ONMOUSEOUT=\"javascript:restoreLayer('"+moduleID+"', '"+menuID+"');document.all.arrow_"+menuID+".color='#BFBFBF';\" ONMOUSEDOWN=\""+href+"\" CLASS=gnbSubMenu>&nbsp;<SPAN ID=text_"+menuID+" style='font-weight:bold'><FONT ID=arrow_"+menuID+" COLOR=#BFBFBF style='font-size:8pt;'>▶</FONT>" + subMenu[1] + "</SPAN>&nbsp;&nbsp;</TD>";
    else
      return "<TD "+strOption+" ONMOUSEOVER=\"javascript:highlightLayer('"+moduleID+"', '"+menuID+"')\" ONMOUSEOUT=\"javascript:restoreLayer('"+moduleID+"', '"+menuID+"');document.all.text_"+menuID+".style.color='#646464';\" ONMOUSEDOWN=\""+href+"\" STYLE=\"cursor:pointer; color:#646464;\" CLASS=gnbSubMenu ALIGN=right>&nbsp;<SPAN ID=text_"+menuID+"><b>more <FONT SIZE=-2>&gt;</FONT></b></SPAN>&nbsp;&nbsp;</TD>";
  }
  
  
  function generateGNBLayerMenuContextMenu(moduleID, subMenu, menuID, strOption)
  {
  	
    if (subMenu[0].substr(0, 7) == "http://")
      var href = "javascript:sayNavigate('"+subMenu[0].replace(/'/g, '\\\'')+"');"
    else
      var href = subMenu[0];
    
    if (subMenu[2] == "normal")
      return "<TD "+strOption+" ONMOUSEOVER=\"javascript:parent.highlightLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEOUT=\"javascript:parent.restoreLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEDOWN=\""+href+"\" STYLE=\"cursor:pointer\" CLASS=gnbSubMenu>&nbsp;<SPAN ID=text_"+menuID+">" + subMenu[1] + "</SPAN>&nbsp;&nbsp;</TD>";
    else if (subMenu[2] == "submenu")      
      return "<TD "+strOption+" ONMOUSEOVER=\"javascript:parent.highlightLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEOUT=\"javascript:parent.restoreLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEDOWN=\""+href+"\" STYLE=\"cursor:pointer;padding-left:10px;\" CLASS=gnbSubMenu>&nbsp; <SPAN ID=text_"+menuID+">" + subMenu[1] + "</SPAN>&nbsp;&nbsp;</TD>";
    else if (subMenu[2] == "special")
      return "<TD "+strOption+" ONMOUSEOVER=\"javascript:parent.highlightLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEOUT=\"javascript:parent.restoreLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEDOWN=\""+href+"\" STYLE=\"cursor:pointer\" CLASS=gnbSubMenu>&nbsp;<SPAN ID=text_"+menuID+"><FONT ID=arrow_"+menuID+" COLOR=#BFBFBF style='font-size:8pt;'>▶</FONT>" + subMenu[1] + "</SPAN>&nbsp;&nbsp;</TD>";
    else if (subMenu[2] == "topmenu")
      return "<TD "+strOption+" ONMOUSEOVER=\"javascript:parent.highlightLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEOUT=\"javascript:parent.restoreLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEDOWN=\""+href+"\" CLASS=gnbSubMenu>&nbsp;<SPAN ID=text_"+menuID+" style='font-weight:bold'><FONT ID=arrow_"+menuID+" COLOR=#BFBFBF style='font-size:8pt;'>▶</FONT>" + subMenu[1] + "</SPAN>&nbsp;&nbsp;</TD>";
    else if (subMenu[2] == "onlymenu")
      return "<TD "+strOption+" ONMOUSEOVER=\"javascript:parent.highlightLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEOUT=\"javascript:parent.restoreLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEDOWN=\""+href+"\" CLASS=gnbSubMenu>&nbsp;<SPAN ID=text_"+menuID+" style='font-weight:bold'>" + subMenu[1] + "</SPAN>&nbsp;&nbsp;</TD>";
    else
      return "<TD "+strOption+" ONMOUSEOVER=\"javascript:parent.highlightLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEOUT=\"javascript:parent.restoreLayer2('"+moduleID+"', '"+menuID+"')\" ONMOUSEDOWN=\""+href+"\" STYLE=\"cursor:pointer; color:#646464;\" CLASS=gnbSubMenu ALIGN=right>&nbsp;<SPAN ID=text_"+menuID+"><b>more <FONT SIZE=-2>&gt;</FONT></b></SPAN>&nbsp;&nbsp;</TD>";
  }

  function generateGNBLayerTop(moduleID, subMenu, bgcolor, menuID)
  {
  	
    return (
"<TR VALIGN=bottom HEIGHT=1>"+
  "<TD ROWSPAN=2 BGCOLOR=#717171></TD>"+
  "<TD ID="+menuID+" BGCOLOR="+bgcolor+" ROWSPAN=2 COLSPAN=3></TD>"+
  generateGNBLayerMenu(moduleID, subMenu, menuID, 'ID='+menuID+' BGCOLOR='+bgcolor+' ROWSPAN=2')+
  "<TD ID="+menuID+" BGCOLOR="+bgcolor+" ROWSPAN=2 COLSPAN=2></TD>"+
  "<TD ROWSPAN=2 BGCOLOR=#717171></TD>"+
  "<TD BGCOLOR=#000000></TD>"+
"</TR>"+

"<TR BGCOLOR="+bgcolor+" VALIGN=bottom HEIGHT=17>"+
  "<TD BGCOLOR=#000000></TD>"+
"</TR>");
  }
  
  function generateGNBLayerTopContextMenu(moduleID, subMenu, bgcolor, menuID)
  {
    return (


"<TR VALIGN=bottom HEIGHT=1>"+
  "<TD ROWSPAN=2 BGCOLOR=#717171></TD>"+
  "<TD ID="+menuID+" BGCOLOR="+bgcolor+" ROWSPAN=2 COLSPAN=3></TD>"+
  generateGNBLayerMenuContextMenu(moduleID, subMenu, menuID, 'ID='+menuID+' BGCOLOR='+bgcolor+' ROWSPAN=2')+
  "<TD ID="+menuID+" BGCOLOR="+bgcolor+" ROWSPAN=2 COLSPAN=2></TD>"+
  "<TD ROWSPAN=2 BGCOLOR=#717171></TD>"+
  "<TD></TD>"+
"</TR>"+

"<TR BGCOLOR="+bgcolor+" VALIGN=bottom HEIGHT=17>"+
  "<TD BGCOLOR=#000000></TD>"+
"</TR>");
  }
  
  function generateGNBLayerTopContextMenu(moduleID, subMenu, bgcolor, menuID)
  {
    return (
"<TR VALIGN=bottom HEIGHT=1>"+
  "<TD ROWSPAN=2 BGCOLOR=#717171></TD>"+
  "<TD ID="+menuID+" BGCOLOR="+bgcolor+" ROWSPAN=2 COLSPAN=3></TD>"+
  generateGNBLayerMenuContextMenu(moduleID, subMenu, menuID, 'ID='+menuID+' BGCOLOR='+bgcolor+' ROWSPAN=2')+
  "<TD ID="+menuID+" BGCOLOR="+bgcolor+" ROWSPAN=2 COLSPAN=2></TD>"+
  "<TD ROWSPAN=2 BGCOLOR=#717171></TD>"+
  "<TD></TD>"+
"</TR>"+

"<TR BGCOLOR="+bgcolor+" VALIGN=bottom HEIGHT=17>"+
  "<TD BGCOLOR=#000000></TD>"+
"</TR>" 

);
  }

  function generateGNBLayerCommon(moduleID, subMenu, bgcolor, menuID)
  {
  	
    return (
"<TR ID="+menuID+" BGCOLOR="+bgcolor+" VALIGN=bottom HEIGHT=18>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD COLSPAN=3></TD>"+
  generateGNBLayerMenu(moduleID, subMenu, menuID, '')+
  "<TD COLSPAN=2></TD>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD BGCOLOR=#000000></TD>"+
"</TR>");
  }
  
  function generateGNBLayerCommonContextMenu(moduleID, subMenu, bgcolor, menuID)
  {
    return (
"<TR ID="+menuID+" BGCOLOR="+bgcolor+" VALIGN=bottom HEIGHT=18>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD COLSPAN=3></TD>"+
  generateGNBLayerMenuContextMenu(moduleID, subMenu, menuID, '')+
  "<TD COLSPAN=2></TD>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD BGCOLOR=#000000></TD>"+
"</TR>");
  }

  function generateGNBLayerBottom(moduleID, subMenu, bgcolor, menuID)
  {
    return (
"<TR ID="+menuID+" BGCOLOR="+bgcolor+" VALIGN=bottom HEIGHT=16>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD></TD>"+
  "<TD ROWSPAN=2></TD>"+
  "<TD ROWSPAN=3></TD>"+
  generateGNBLayerMenu(moduleID, subMenu, menuID, 'ROWSPAN=3')+
  "<TD ROWSPAN=2></TD>"+
  "<TD></TD>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD ROWSPAN=2 BGCOLOR=#000000></TD>"+
"</TR>"+

"<TR HEIGHT=1>"+
  "<TD ROWSPAN=2></TD>"+
  "<TD ROWSPAN=2 BGCOLOR=#717171></TD>"+
  "<TD ROWSPAN=2 BGCOLOR=#717171></TD>"+
  "<TD ROWSPAN=2 BGCOLOR=#000000></TD>"+
"</TR>"+

"<TR HEIGHT=1>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD></TD>"+
"</TR>");
  }
  
  function generateGNBLayerBottomContextMenu(moduleID, subMenu, bgcolor, menuID)
  {
    return (
"<TR ID="+menuID+" BGCOLOR="+bgcolor+" VALIGN=bottom HEIGHT=16>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD></TD>"+
  "<TD ROWSPAN=2></TD>"+
  "<TD ROWSPAN=3></TD>"+
  generateGNBLayerMenuContextMenu(moduleID, subMenu, menuID, 'ROWSPAN=3')+
  "<TD ROWSPAN=2></TD>"+
  "<TD></TD>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD ROWSPAN=2 BGCOLOR=#000000></TD>"+
"</TR>"+

"<TR HEIGHT=1>"+
  "<TD ROWSPAN=2></TD>"+
  "<TD ROWSPAN=2 BGCOLOR=#717171></TD>"+
  "<TD ROWSPAN=2 BGCOLOR=#717171></TD>"+
  "<TD ROWSPAN=2 BGCOLOR=#000000></TD>"+
"</TR>"+

"<TR HEIGHT=1>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD></TD>"+
"</TR>");
  }
  
  function generateGNBLayerLine()
  {
    return (
"<TR BGCOLOR=#D3D3D3 HEIGHT=1>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD COLSPAN=6></TD>"+
  "<TD BGCOLOR=#717171></TD>"+
  "<TD BGCOLOR=#000000></TD>"+
"</TR>");
  }

  function generateGNBLayer(moduleID, subMenus)
  {
  	//alert(moduleID);
    var bgcolor = getGNBLayerColor(moduleID);
	var mstrMenu;
	//onpropertychange=\"selectbox_hidden('gnb_layer_"+moduleID+"')\"
    mstrMenu = (
"<DIV ID=gnb_layer_"+moduleID+"   ONMOUSEOVER=\"javascript:showSubMenuLayer('"+moduleID+"')\" ONMOUSEOUT=\"javascript:hideSubMenuLayer('"+moduleID+"');\" STYLE=\"position:absolute; display:none; z-index:1;\">"+
"<div style='position:absolute;z-index:2;top:-7px;left:30px;' ><img src='/admin/images/navi/menu_arrow.gif' ></div>"+
"<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 STYLE=\"position:relative\">"+
  "<COL WIDTH=1>"+
  "<COL WIDTH=1>"+
  "<COL WIDTH=1>"+
  "<COL WIDTH=1>"+
  "<COL>"+
  "<COL WIDTH=1>"+
  "<COL WIDTH=1>"+
  "<COL WIDTH=1>"+
  "<COL WIDTH=1>");
  
  mstrMenu += (
 "<TR height=1>"+
  "	<TD colSpan=3></TD>"+
  "	<TD bgColor=#a3a3a3 colSpan=3></TD>"+
  "	<TD colSpan=3></TD>"+
  "</TR>"+
  "<TR height=1>"+
  "	<TD colSpan=2></TD>"+
  "	<TD bgColor=#717171></TD>"+
  "	<TD bgColor=#ffffff colSpan=2></TD>"+
  "	<TD bgColor=#000000 colSpan=3></TD>"+
  "	<TD></TD>"+
  "</TR>"+
  "<TR height=1>"+
  "	<TD></TD>"+
  "	<TD bgColor=#717171></TD>"+
  "	<TD bgColor=#e1f0f6></TD>"+
  "	<TD bgColor=#ffffff colspan=3></TD>"+
  "	<TD bgColor=#717171 ></TD>"+
  "	<TD bgColor=#717171 ></TD>"+
  "	<TD bgColor=#000000 ></TD>"+
  "	<TD></TD>"+
  "</TR>"

  );
  //mstrMenu = generateGNBLayerContextMenu(moduleID, subMenus);

    var isSpecial = false;
    var bgcolor = '#FFFFFF';
    var i = 0;
    
    //alert(subMenus.length);
    //for (key in subMenus)
    for (key = 0 ;key < subMenus.length;key++)
    {
    	
   try{
      /*if (subMenus[key][2] != 'normal')
        bgcolor = "#F8F8F3";
      else 
      */	
      if (i % 2 == 0)
        bgcolor = getGNBLayerColor(moduleID);
      else
        bgcolor = "#FFFFFF";

	
      if (subMenus[key][2] != 'normal' && !isSpecial)
      {
        generateGNBLayerLine();
        isSpecial = true;
      }


      if (key == subMenus.length - 1)
        mstrMenu += generateGNBLayerBottom(moduleID, subMenus[key], bgcolor, "gnb_layer_"+moduleID+"_"+key);
      else if (key == 0)
        mstrMenu += generateGNBLayerTop(moduleID, subMenus[key], bgcolor, "gnb_layer_"+moduleID+"_"+key);
      else
        mstrMenu += generateGNBLayerCommon(moduleID, subMenus[key], bgcolor, "gnb_layer_"+moduleID+"_"+key);
        
        i++;
     }catch(e){}
    }

    mstrMenu += (
  "<TR HEIGHT=1>"+
    "<TD COLSPAN=2></TD>"+
    "<TD BGCOLOR=#000000></TD>"+
    "<TD COLSPAN=2 BGCOLOR=#717171></TD>"+
    "<TD COLSPAN=3 BGCOLOR=#000000></TD>"+
    "<TD></TD>"+
  "</TR>"+

  "<TR HEIGHT=1>"+
    "<TD COLSPAN=4></TD>"+
    "<TD COLSPAN=2 BGCOLOR=#000000></TD>"+
    "<TD COLSPAN=3></TD>"+
  "</TR>"+

"</TABLE>"+
"</DIV>");

document.writeln (mstrMenu);

  }
  
  
  function generateGNBLayerContextMenu(moduleID, subMenus)
  {
    var bgcolor = getGNBLayerColor(moduleID);

    strContextMenu = (
"<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 STYLE=\"position:relative\">"+
  "<TR height=1>"+
  "	<TD colSpan=3></TD>"+
  "	<TD bgColor=#717171 colSpan=3></TD>"+
  "	<TD colSpan=3></TD>"+
  "</TR>"+
  "<TR height=1>"+
  "	<TD colSpan=2></TD>"+
  "	<TD bgColor=#717171></TD>"+
  "	<TD bgColor=#e1f0f6 colSpan=2></TD>"+
  "	<TD bgColor=#000000 colSpan=3></TD>"+
  "	<TD></TD>"+
  "</TR>"+
  "<TR height=1>"+
  "	<TD></TD>"+
  "	<TD bgColor=#717171></TD>"+
  "	<TD bgColor=#e1f0f6></TD>"+
  "	<TD bgColor=#e1f0f6 colspan=3></TD>"+
  "	<TD bgColor=#717171 ></TD>"+
  "	<TD bgColor=#717171 ></TD>"+
  "	<TD bgColor=#ffffff ></TD>"+
  "	<TD></TD>"+
  "</TR>"


  );

    var isSpecial = false;
    var bgcolor = '#FFFFFF';

    for (key in subMenus)
    {
      /*if (subMenus[key][2] != 'normal')
        bgcolor = "#F8F8F3";
      else 
      */	
      if (key % 2 == 0)
        bgcolor = getGNBLayerColor(moduleID);
      else
        bgcolor = "#FFFFFF";

		
      if (subMenus[key][2] != 'normal' && !isSpecial)
      {
        generateGNBLayerLine();
        isSpecial = true;
      }

      if (key == subMenus.length - 1)
       strContextMenu += generateGNBLayerBottomContextMenu(moduleID, subMenus[key], bgcolor, "gnb_layer_"+moduleID+"_"+key);
      else if (key == 0)
       strContextMenu += generateGNBLayerTopContextMenu(moduleID, subMenus[key], bgcolor, "gnb_layer_"+moduleID+"_"+key);
      else
       strContextMenu += generateGNBLayerCommonContextMenu(moduleID, subMenus[key], bgcolor, "gnb_layer_"+moduleID+"_"+key);
    }

    strContextMenu += (
  "<TR HEIGHT=1>"+
    "<TD COLSPAN=2></TD>"+
    "<TD BGCOLOR=#000000></TD>"+
    "<TD COLSPAN=2 BGCOLOR=#717171></TD>"+
    "<TD COLSPAN=3 BGCOLOR=#000000></TD>"+
    "<TD></TD>"+
  "</TR>"+
  "<TR HEIGHT=1>"+
    "<TD COLSPAN=4></TD>"+
    "<TD COLSPAN=2 BGCOLOR=#000000></TD>"+
    "<TD COLSPAN=3></TD>"+
  "</TR>"+
"</TABLE>"
	);
  }

  var originalLayerBGColor;
  var originalLayerFontColor;

  function highlightLayer(moduleID, menuID)
  {
  	
    //showSubMenuLayer(moduleID);
    originalLayerBGColor = document.getElementById(menuID).bgColor;
    var colMenu = document.getElementsByName(menuID);
    for (var i=0; i<colMenu.length; i++)
      colMenu[i].bgColor = getGNBLayerHighlightColor(moduleID);
    document.getElementById("text_"+menuID).style.color = "#FFFFFF";
  }

  function highlightLayer2(moduleID, menuID){
	
    //showSubMenuLayer(moduleID);
    originalLayerBGColor = sysmen.document.getElementById(menuID).bgColor;
    var colMenu = sysmen.document.getElementsByName(menuID);
    for (var i=0; i<colMenu.length; i++)
      colMenu[i].bgColor = getGNBLayerHighlightColor(moduleID);
    sysmen.document.getElementById("text_"+menuID).style.color = "#FFFFFF";
  }
  
  function restoreLayer(moduleID, menuID)
  {
    //hideSubMenuLayer(moduleID);
    
    var colMenu = document.getElementsByName(menuID);
    for (var i=0; i<colMenu.length; i++)
      colMenu[i].bgColor = originalLayerBGColor;
    document.getElementById("text_"+menuID).style.color = "#000000";
  }
  
  function restoreLayer2(moduleID, menuID)
  {
    //hideSubMenuLayer(moduleID);
    var colMenu = sysmen.document.getElementsByName(menuID);
    for (var i=0; i<colMenu.length; i++)
      colMenu[i].bgColor = originalLayerBGColor;
    sysmen.document.getElementById("text_"+menuID).style.color = "#000000";
  }

  var originalMenuColor;
  var originalMenuFontWeight;
  var currentShewModuleID = '';

  function showSubMenuLayer(moduleID)
  {
  	if(document.getElementById("searchZipArea")){  		
		document.getElementById("searchZipArea").innerHTML = "지역별 배송을 설정하고자 하는 지역명을 검색해주세요...";
	}
    if (currentShewModuleID == moduleID)
      return;

    if (currentShewModuleID != '')
      hideSubMenuLayer(currentShewModuleID);


    currentShewModuleID = moduleID;
	//alert(moduleID);
    /*
	var tg = $("gnb_layer_"+moduleID);
    var link = $("gnb_link_"+moduleID);
    var linkText = $("gnb_link_text_"+moduleID);
	*/
	var tg = $("#gnb_layer_"+moduleID);
	var link = $("#gnb_link_"+moduleID);
    var linkText = $("#gnb_link_text_"+moduleID);

/*
    originalMenuColor = linkText.style.color;
    originalMenuFontWeight = linkText.style.fontWeight;
    linkText.style.color = getGNBLinkColor(moduleID);
    linkText.style.fontWeight = "bold";
*/
	originalMenuColor = linkText.css('color');
    originalMenuFontWeight = linkText.css('fontWeight');
    linkText.css('color',getGNBLinkColor(moduleID));
    linkText.css('fontWeight','bold');


    if (tg != null)
    {
      //tg.style.top = link.offsetTop + link.offsetHeight-10; // posTop --> top 으로 수정 , posLeft --> left 로 수정
      //tg.style.left = link.offsetLeft-10;   // 2007.03.14 일 신훈식 추가
	  var offset = link.offset();
	  //alert(offset.top+" "+ link.attr('offsetHeight'));
	 
	  if(window.addEventListener) { // 브라우저 호환을 위해 수정 kbk
		 tg.css('top',(offset.top + link.attr('offsetHeight')-10)+"px");
		 tg.css('left',(offset.left-10)+"px");
	  } else {
		 tg.css('top',(offset.top + link.attr('offsetHeight')-10));
		 tg.css('left',offset.left-10);
	  }
      tg.show();
	  //tg.slideDown();	
	 // alert(tg.html());
      for (var i=0; i<objectForClipping.length; i++)
      {
        var frameName = objectForClipping[i];
        var tableName = oTableForClipping[i];    
        var oFrame = document.getElementById(frameName);
        var oTable = document.getElementById(tableName); 

        if( 'undefined' != typeof(tableName) && '' != tableName )    
        {    
			
           var oldFrameOffsetTop = oFrame.offsetTop;    
           var oldFrameOffsetLeft = oFrame.offsetLeft;    
           var frameOffsetTop = oFrame.offsetTop + parseInt(oTable.style.top);    
           var frameOffsetLeft = oFrame.offsetLeft + parseInt(oTable.style.left);    
           var frameOffsetBottom = frameOffsetTop + oFrame.offsetHeight;    
           var frameOffsetRight = frameOffsetLeft + oFrame.offsetWidth;    
        }    
        else    
        {  
          oFrame.style.position = 'relative';

          var frameOffsetTop = oFrame.offsetTop;
          var frameOffsetLeft = oFrame.offsetLeft;
          var frameOffsetBottom = oFrame.offsetTop + oFrame.offsetHeight;
          var frameOffsetRight = oFrame.offsetLeft + oFrame.offsetWidth;
        }

        var layerOffsetTop = tg.offsetTop;
        var layerOffsetBottom = tg.offsetTop + tg.offsetHeight;
        var layerOffsetLeft = tg.offsetLeft;
        var layerOffsetRight = tg.offsetLeft + tg.offsetWidth;

        if (frameOffsetTop <= layerOffsetBottom &&
            (layerOffsetLeft >= frameOffsetLeft && layerOffsetLeft <= frameOffsetRight ||
             frameOffsetLeft >= layerOffsetLeft && frameOffsetLeft <= layerOffsetRight))
        {
          oFrame.style.position = 'absolute';

          if( 'undefined' != typeof(tableName) && '' != tableName )    
          {    
            oFrame.style.top = oldFrameOffsetTop;    
            oFrame.style.left = oldFrameOffsetLeft;    
          }    
          else    
          {    
            oFrame.style.top = frameOffsetTop;
            oFrame.style.left = frameOffsetLeft;
          }
          oFrame.style.clip = "rect(" + (layerOffsetBottom - frameOffsetTop + 1) + " auto auto auto)";
        }
      }
    }
  }

  function hideSubMenuLayer(moduleID, force)
  {
    if (currentShewModuleID != moduleID)
      return;

    var tg = document.getElementById("gnb_layer_" + moduleID);
    var linkText = document.getElementById("gnb_link_text_"+moduleID);

    currentShewModuleID = '';

    linkText.style.color = originalMenuColor?originalMenuColor:"#000000";
    linkText.style.fontWeight = originalMenuFontWeight?originalMenuFontWeight:"normal";

    if (tg != null)
    {
      tg.style.display = "none";

      for (var i=0; i<objectForClipping.length; i++)
      {
        var frameName = objectForClipping[i];
        var tableName = oTableForClipping[i]; 
        var oFrame = document.getElementById(frameName);
        var oTable = document.getElementById(tableName); 

        oFrame.style.position = 'relative';
        oFrame.style.top = 0;
        oFrame.style.left = 0;
        oFrame.style.clip = "rect(auto)";
      }
    }
    selectbox_visible();
  }

  function showSubMenuLayer2(moduleID)
  {
    if (currentShewModuleID == moduleID)
      return;

    if (currentShewModuleID != '')
      hideSubMenuLayer(currentShewModuleID);

    currentShewModuleID = moduleID;

    var linkText = document.getElementById("gnb_link_text_"+moduleID);

    originalMenuColor = linkText.style.color;
    originalMenuFontWeight = linkText.style.fontWeight;
    linkText.style.color = getGNBLinkColor(moduleID);
    linkText.style.fontWeight = "bold";
  }

  function hideSubMenuLayer2(moduleID)
  {
    if (currentShewModuleID != moduleID)
      return;

    var linkText = document.getElementById("gnb_link_text_"+moduleID);

    currentShewModuleID = '';

    linkText.style.color = originalMenuColor?originalMenuColor:"#000000";
    linkText.style.fontWeight = originalMenuFontWeight?originalMenuFontWeight:"normal";
  }

  var objectForClipping = new Array();
  var oTableForClipping = new Array();

  function addIFrameForClipping(frameName, tableName)
  {
    var count = objectForClipping.length;
    objectForClipping[count] = frameName;
    oTableForClipping[count] = tableName;
  }


  function lightup(imgName)
  {
    if (document.images)
    {
      imgOn=eval(imgName + "on.src");
      document[imgName].src= imgOn;
    }
  }

  function turnoff(imgName)
  {
    if (document.images)
    {
      imgOff=eval(imgName + "off.src");
      document[imgName].src= imgOff;
    }
  }
  
  
  
  

// Internet Explorer에서 셀렉트박스와 레이어가 겹칠시 레이어가 셀렉트 박스 뒤로 숨는 현상을 해결하는 함수 
    // 레이어가 셀렉트 박스를 침범하면 셀렉트 박스를 hidden 시킴 
    // 사용법 : 
    // <div id=LayerID style="display:none; position:absolute;" onpropertychange="selectbox_hidden('LayerID')"> 
    function selectbox_hidden(layer_id) 
    { 
        var ly = eval　(layer_id); 

        // 레이어 좌표 
        var ly_left  = ly.offsetLeft; 
        var ly_top    = ly.offsetTop; 
        var ly_right  = ly.offsetLeft + ly.offsetWidth; 
        var ly_bottom = ly.offsetTop + ly.offsetHeight; 

        // 셀렉트박스의 좌표 
        var el; 

        for (i=0; i<document.forms.length; i++) { 
            for (k=0; k<document.forms[i].length; k++) { 
                el = document.forms[i].elements[k];    
                if (el.type == "select-one") { 
                    var el_left = el_top = 0; 
                    var obj = el; 
                    if (obj.offsetParent) { 
                        while (obj.offsetParent) { 
                            el_left += obj.offsetLeft; 
                            el_top  += obj.offsetTop; 
                            obj = obj.offsetParent; 
                        } 
                    } 
                    el_left  += el.clientLeft; 
                    el_top    += el.clientTop; 
                    el_right  = el_left + el.clientWidth; 
                    el_bottom = el_top + el.clientHeight; 

                    // 좌표를 따져 레이어가 셀렉트 박스를 침범했으면 셀렉트 박스를 hidden 시킴 
                    if ( (el_left >= ly_left && el_top >= ly_top && el_left <= ly_right && el_top <= ly_bottom) || 
                        (el_right >= ly_left && el_right <= ly_right && el_top >= ly_top && el_top <= ly_bottom) || 
                        (el_left >= ly_left && el_bottom >= ly_top && el_right <= ly_right && el_bottom <= ly_bottom) || 
                        (el_left >= ly_left && el_left <= ly_right && el_bottom >= ly_top && el_bottom <= ly_bottom) ) 
                        el.style.visibility = 'hidden'; 
                } 
            } 
        } 
    } 

    // 감추어진 셀렉트 박스를 모두 보이게 함 
    function selectbox_visible() 
    { 
        for (i=0; i<document.forms.length; i++) { 
            for (k=0; k<document.forms[i].length; k++) { 
                el = document.forms[i].elements[k];    
                if (el.type == "select-one" && el.style.visibility == 'hidden') 
                    el.style.visibility = 'visible'; 
            } 
        } 
    } 

