<?php

	include("../class/layout.class");
	include("category.lib.php");
	include("design.common.php");
	
	if($page_name == ""){
		$page_name = "ms_index.htm";
	}
	
	
	
	if($admin_config[mall_page_type] == "P"){
		$templet_path = $admin_config[mall_data_root]."/templet/".$admin_config[selected_templete];
		$templet_file_path = $templet_path."/".$mod."/".$page_name;
		$selected_skin = $admin_config[selected_templete];
		$unique_file_path = "templet/".$admin_config[selected_templete]."/".$mod."/".$page_name;
	}else if($admin_config[mall_page_type] == "M"){
		$templet_path = $admin_config[mall_data_root]."/mobile_templet/".$admin_config[selected_templete];
		$templet_file_path = $templet_path."/".$mod."/".$page_name;
		$selected_skin = $admin_config[selected_templete];
		$unique_file_path = "mobile_templet/".$admin_config[selected_templete]."/".$mod."/".$page_name;
	}else if($admin_config[mall_page_type] == "MI"){
		$templet_path = $admin_config[mall_data_root]."/minishop_templet/".$admin_config[selected_templete];
		$templet_file_path = $templet_path."/".$mod."/".$page_name;
		$selected_skin = $admin_config[selected_templete];
		$unique_file_path = "minishop_templet/".$admin_config[selected_templete]."/".$mod."/".$page_name;
	}
	
	if($design_act == "recovery"){
		$db = new Database;
		$db->query("select page_contents from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix='$mall_ix' and page_ix = '$page_ix' ");
		$db->fetch();
		$thisfile = $db->dt[page_contents];
		$thisfile_convert = eregi_replace("{templet_src}",$templet_path, $thisfile);
		$thisfile_convert = eregi_replace("@_templet_path",$templet_path, $thisfile_convert);
		$thisfile_convert = str_replace("</textarea>","&lt;/textarea&gt;", $thisfile_convert);
		$thisfile_convert = str_replace("<textarea","&lt;textarea", $thisfile_convert);
		$thisfile_convert = str_replace("</form>","&lt;/form&gt;", $thisfile_convert);
		$thisfile_convert = str_replace("<form","&lt;form", $thisfile_convert);
		$thisfile_convert = str_replace("<FORM","&lt;form", $thisfile_convert);
		$thisfile_convert = str_replace("</FORM>","&lt;/form&gt;", $thisfile_convert);
	}else{
		$thisfile = load_template($DOCUMENT_ROOT.$templet_file_path);
		$thisfile_convert = eregi_replace("{templet_src}",$templet_path, $thisfile);
		$thisfile_convert = eregi_replace("@_templet_path",$templet_path, $thisfile_convert);
		$thisfile_convert = str_replace("</textarea>","&lt;/textarea&gt;", $thisfile_convert);
		$thisfile_convert = str_replace("<textarea","&lt;textarea", $thisfile_convert);
		$thisfile_convert = str_replace("</form>","&lt;/form&gt;", $thisfile_convert);
		$thisfile_convert = str_replace("<form","&lt;form", $thisfile_convert);
		$thisfile_convert = str_replace("</FORM>","&lt;/FORM&gt;", $thisfile_convert);
		$thisfile_convert = str_replace("<FORM","&lt;FORM", $thisfile_convert);
	
		$thisfile = str_replace("</textarea>","&lt;/textarea&gt;", $thisfile);
		$thisfile = str_replace("<textarea","&lt;textarea", $thisfile);
		$thisfile = str_replace("</TEXTAREA>","&lt;/textarea&gt;", $thisfile);
		$thisfile = str_replace("<TEXTAREA","&lt;textarea", $thisfile);
		$thisfile = str_replace("</FORM>","&lt;/FORM&gt;", $thisfile);
		$thisfile = str_replace("<FORM","&lt;FORM", $thisfile);
	}
	
	
	
	
	
	
	$Script = "
	<Script Language='JavaScript' src='../js/XMLHttp.js'></Script>
	<Script Language='JavaScript' src='design.js'></Script>
	<Script Language='JavaScript'>
	function ToolBarCmd() {
		var EditBox = document.forms['info_input'].page_contents;
		EditBox.focus();
		var argv = ToolBarCmd.arguments;
	
		if (argv[1]==null) {
			EditBox.document.execCommand(argv[0]);
		}
		else {
			EditBox.document.execCommand(argv[0], false, argv[1]);
		}
	
		EditBox.focus();
		return false;
	}
	
	function SubmitX(frm){
			var pc_obj = document.getElementById('page_contents');
			pc_obj.readOnly = true;
	
			document.getElementById('parent_save_loading').style.zIndex = '1';
			with (document.getElementById('save_loading').style){
				width = '100%';
				height = '300px';
				backgroundColor = '#ffffff';
				filter = 'Alpha(Opacity=50)';
				opacity = '0.5';
			}
	
			var obj = document.createElement('div');
			with (obj.style){
				position = 'relative';
				zIndex = 100;
			}
			obj.id = 'loadingbar';
			obj.innerHTML = \"<img src='/admin/images/indicator.gif' border=0>\";
			document.getElementById('save_loading').appendChild(obj);
	
			document.getElementById('save_loading').style.display = 'block';
			document.getElementById('save_btn').focus();
	
		//alert(iView.document.body.innerHTML);
		//frm.content.value = iView.document.body.innerHTML;
		//document.write (frm.content.value);
		//document.forms['info_input'].page_contents.value = document.forms['info_input'].page_contents.value.replace('&lt;/textarea&gt;','</textarea>');
		//document.forms['info_input'].page_contents.value = document.forms['info_input'].page_contents.value.replace('&lt;textarea','<textarea');
		return true;
	}
	
	function CheckChangeMemo(){
		var frm = document.info_input;
	
		if(frm.page_change_memo.value == '소스 수정내용을 입력해주세요'){
			frm.page_change_memo.value = '';
		}
	
	}
	
	function PageLoad(){
		var xmlHttp = new XMLHttp();
		ret = xmlHttp.request('get', '".$templet_file_path."', false, null);
		if(ret.status == 200){
			document.forms['info_input'].page_contents.value = ret.responseText;
		}else{
			alert('Error!!');
		}
	}
	
	function pview(contents, windowName, picTitle){
	
		var winHandle = window.open('' ,windowName,',left=0,top=0,width=1000,height='+screen.height+',scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=no');
		if(winHandle != null){
	//	alert(contents);
	//	contents = contents.replace('&lt;textarea','<textarea');
	//	contents = contents.replace('&lt;/textarea&gt;','</textarea>');
	
	
		//alert(contents);
	
			var htmlString = '<html>\\n';
	
			htmlString += '<LINK REL=stylesheet HREF={templet.src}/css/mallstory.css TYPE=text/css>\\n';
			//htmlString += '<LINK REL=stylesheet HREF=../include/admin.css TYPE=text/css>';
			htmlString += '<head>\\n';
			htmlString += '<title>' + picTitle + '</title>\\n';
			htmlString += '</head>\\n';
			htmlString += '<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0  noreize>\\n';
			htmlString += ''+contents;
			htmlString += '</body>\\n</html>';
	
			htmlString = htmlString.replace(/{templet.src}/gi,'$templet_path');
	
			winHandle.document.open();
			winHandle.document.write(htmlString);
			winHandle.document.close();
		}
	}
	
	
	
	
	function pageHistoryDelete(page_ix){
		if(confirm(language_data['design.mod.php']['A'][language] )){//페이지 수정내용과 백업 소스를 정말로 삭제하시겠습니까?
			//document.frames['act'].location.href='design.act.php?design_act=delete&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_name."'//kbk
			document.getElementById('act').src='design.act.php?design_act=delete&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_name."'
		}
	}
	
	function pageHistoryRecovery(page_ix){
		if(confirm(language_data['design.mod.php']['B'][language])){//해당내용으로 복구하시겠습니까? 1차적으로 화면에만 복구되게 되며 완전한 복구를 원하실때는 화면 복구후 저장버튼을 눌러주시기 바랍니다. 
			//document.frames['act'].location.href='design.act.php?design_act=recovery&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=$mod/".$page_name."'//kbk
			document.getElementById('act').src='design.act.php?design_act=recovery&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=$mod/".$page_name."'
			//document.location.href='design.mod.php?SubID=$SubID&mod=$mod&design_act=recovery&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_name."'
		}
	}
	
	
	function setCategory(cname,cid,depth,category_display_type){
		if(category_display_type == 'F'){
			alert(language_data['design.mod.php']['C'][language]);// 분류정보입니다.
		}else{
			document.location.href='design.php?pcode='+cid+'&depth='+depth+'&SubID=SM114641Sub'
		}
	}
	
	function ctrlSave() {
		if(event.ctrlKey == true && event.keyCode == 83 ){
			var pc_obj = document.getElementById('page_contents');
			pc_obj.readOnly = true;
			//alert(document.getElementById('parent_save_loading').style.zIndex);
			document.getElementById('parent_save_loading').style.zIndex = '1';
			with (document.getElementById('save_loading').style){
	
				width = '100%';
				height = '300px';
				backgroundColor = '#ffffff';
				filter = 'Alpha(Opacity=50)';
				opacity = '0.5';
			}
	
			var obj = document.createElement('div');
			with (obj.style){
				position = 'relative';
				zIndex = 100;
			}
			obj.id = 'loadingbar';
			obj.innerHTML = \"<img src='/admin/images/indicator.gif' border=0 >\";
			document.getElementById('save_loading').appendChild(obj);
	
			document.getElementById('save_loading').style.display = 'block';
			document.getElementById('save_btn').focus();
			//alert(event.keyCode);
			event.returnValue = false;
	
			pc_obj.form.submit();
		}
	
	}
	
	
	function unloading(){
		var obj = parent.document.getElementById('page_contents');
		obj.readOnly = false;
		obj.style.border = '1px solid silver';
		parent.document.getElementById('parent_save_loading').style.zIndex = -1;
		parent.document.getElementById('loadingbar').innerHTML ='';
		parent.document.getElementById('save_loading').innerHTML ='';
	
	
		parent.document.getElementById('save_loading').style.display = 'none';
		parent.document.getElementById('page_contents').focus();
	}
	
	//document.onkeypress = ctrlSave;
	document.onkeydown = ctrlSave;
	
	</Script>";
	
	
	
	$help_text = "
		<TABLE height=20 cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
			
			<TR>
				<TD  colspan=3 align=left style='padding:5px 0 5px 10px;line-height:120%'>
				<!--table cellpadding=1 cellspacing=0  class='small'>
					<col width=8>
					<col width=*>
					<tr><td><img src='/admin/image/icon_list.gif' border='0' align='absmiddle' /></td><td class='small' style='line-height:120%' >레이아웃 디자인이란 ? 페이지를 구성하기위한 부분 부분의 디자인을 하는 페이지 입니다 . 좌측 메뉴에 구성되어 있는 <b>상단, 좌측메뉴, 우측메뉴, 하단 등등</b>을 코딩하여 저장하신후 <u>페이지 상세 디자인</u>에서 구성하시면 디자인이 완료됩니다.</td></tr>
					<tr><td><img src='/admin/image/icon_list.gif' border='0' align='absmiddle' /></td><td class='small' >소스를 수정하신후에는 아래 수정내용 기입후 저장버튼을 눌러주시면, 추후에 수정내용을 참고하여 복원하실수 있습니다</td></tr>
					<tr><td><img src='/admin/image/icon_list.gif' border='0' align='absmiddle' /></td><td class='small' >소스 수정시 undo 와 redo 기능을 이용해 수정된 정보를 되돌릴수 있습니다</td></tr>
					<tr><td><img src='/admin/image/icon_list.gif' border='0' align='absmiddle' /></td><td class='small' >좌측메뉴 상단의 메뉴 숨기기 버튼을 클릭해서 보다 넓게 코딩 작업을 할 수 있습니다</td></tr>
					<tr><td><img src='/admin/image/icon_list.gif' border='0' align='absmiddle' /></td><td class='small' >새로운 레이아웃을 만드실경우에는 위와 같이 <b>치환코드</b>를 삽입해주셔야 합니다</td></tr>
					
				</table-->
				 ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."  ";
			
	if($mod=="layout"){
	$help_text .= "
					
				<table width='100%' cellpadding=5 bgcolor=silver cellspacing=1>
				<tr bgcolor=#efefef align=center>
					<td width=150>위치</td>
					<td width=150>치환코드</td>
					<td width=*>설명</td>
				</tr>
				<tr bgcolor=#ffffff align=center>
					<td><b>상단메뉴 1</b> : </td>
					<td>{header_top} </td>
					<td rowspan=2 class=small align=left><!--상단메뉴는 다양한 디자인 레이아웃을 구성하기 위해서 두부분으로 나누어 구성했습니다-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</td>
				</tr>
				<tr bgcolor=#ffffff align=center>
					<td><b>상단메뉴2</b> : </td>
					<td>{header_menu}</td>
				</tr>
				<tr bgcolor=#ffffff align=center>
					<td><b>좌측메뉴</b> : </td><td>{center_leftmenu}</td>
					<td class=small align=left><!--좌측메뉴가 위치하는 부분입니다.-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</td>
				</tr>
				<tr bgcolor=#ffffff align=center>
					<td><b>컨텐츠</b> : </td><td>{center_contents}</td>
					<td class=small align=left><!--컨텐츠 부분은 페이지 상세 디자인에서 레이아웃 구성후 html 을 작성하실수 있습니다.-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</td>
				</tr>
				<tr bgcolor=#ffffff align=center>
					<td><b>우측메뉴</b> : </td><td>{center_rightmenu}</td>
					<td class=small align=left><!--우측메뉴 또는 슬라이딩 메뉴가 들어가는 부분입니다.-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."</td>
				</tr>
				<tr bgcolor=#ffffff align=center>
					<td><b>하단1</b> : </td><td>{footer_menu}</td>
					<td rowspan=2 class=small align=left><!--하단메뉴는 또한  다양한 디자인 레이아웃을 구성하기 위해서 두부분으로 나누어 구성했습니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')."</td>
				</tr>
				<tr bgcolor=#ffffff align=center>
					<td><b>하단2</b> : </td><td>{footer_desc}</td>
	
				</tr>
				</table>
				";
	}
	$help_text .= "
				</TD>
			</TR>
		</TABLE>
		";
	
	
		$help_text = HelpBox("<table cellpadding='0' cellspacing='0' border='0'><tr><td nowrap align='top'>레이아웃 디자인</td><td style='position:relative;'><a onClick=\"PoPWindow('/admin/_manual/manual.php?config=".urlencode("몰스토리동영상메뉴얼_레이아웃디자인(090322)_config.xml")."',800,517,'manual_view')\" ><img src='../image/movie_manual.gif'  style='position:absolute;top:-2px;'></a></td></tr></table>", $help_text,171);
	
	$Contents ="
	<table width='100%' border='0' cellspacing='0' cellpadding='0' >
	<form name=info_input action='design.act.php' method='post' onsubmit='return SubmitX(this);' target=act>
	<input type='hidden' name=design_act value='update'>
	<input type='hidden' name=mod value='$mod'>
	<input type='hidden' name=page_name value='$page_name'>
	<input type='hidden' name=pcode value='$pcode'>
	<input type='hidden' name=mall_ix value='".$admininfo[mall_ix]."'>
		<col width='50%' />
		<col width='50%' />";
	if($mmode != "pop"){
	$Contents .="
		<tr>
			<td colspan=2>
			".SelectDesignSkin()."
			</td>
		</tr>";
	
	$Contents .="
		";
	}
	$Contents .="
		<tr height=40>
		    <td align='left' colspan=2> ".GetTitleNavigation("레이아웃 디자인", "디자인관리 > 레이아웃 디자인 <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=".urlencode("몰스토리동영상메뉴얼_레이아웃디자인(090322)_config.xml")."',800,517,'manual_view')\" ><img src='../image/movie_manual.gif'  style='margin:0 0 0px 0'></a>")."</td>
		</tr>
		<tr height=40><td colspan=2 align=right style='padding-bottom:10px;'>
		".colorCirCleBox("#efefef","100%","<div style='padding:3px 5px 3px 15px;'><img src='../image/title_head.gif' ><b> 선택된 페이지 : <a href=\"JavaScript:pview(document.getElementById('page_contents').value, 'aaa', 'bbb');\">".$selected_skin."/$mod/".$page_name."</a> <a href=\"JavaScript:pview(document.getElementById('page_contents').value, 'aaa', 'bbb');\"><img src='../images/".$admininfo["language"]."/btn_page_design_view.gif' border=0 align=absmiddle></a>	</b></div>")."</td></tr>
		<tr height=100>
			<td colspan=2>
				$help_text<br>
			</td>
		</tr>
		<tr height=40>
			<td>".SelectFileList2($DOCUMENT_ROOT.$templet_path."/".$mod)." ";
	
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
				$Contents .="<a href=\"javascript:PoPWindow('./addfile.pop.php?pcode=$mod&page_path=$mod',420,210,'fileadd_pop')\"><img src='../images/".$admininfo["language"]."/btn_add_file.gif' border=0 align=absmiddle></a> ";
			}else{
				$Contents .= "<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_add_file.gif' border=0 align=absmiddle ></a> ";
			}
	
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .="<a href=\"javascript:open_window('./code_executor.php');\"><img src='../images/".$admininfo["language"]."/btn_quick_design.gif' border=0 align=absmiddle></a> ";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_quick_design.gif' border=0 align=absmiddle ></a> ";
			}
	
			$Contents .="
			</td>
			<td align=right ><a onclick=\"return ToolBarCmd('Undo');\" style='cursor:pointer;'><img src='../images/".$admininfo["language"]."/btn_undo.gif' border=0></a> <a onclick=\"return ToolBarCmd('Redo');\" style='cursor:pointer;'><img src='../images/".$admininfo["language"]."/btn_redo.gif' border=0></a> </td>
		</tr>
		</table>
		<table width='100%' border='0' cellspacing='0' cellpadding='0' class='list_table_box'>
		<tr height=30 >
			<td colspan=2 class='list_box_td list_bg_gray' style='text-align:left;'>
				 <div style='width:98%;' >수정후 CTRL+S 를 누르면 저장됩니다</div>
			</td>
		</tr>
		<tr>
			<td colspan=2 bgcolor='#ffffff' width='100%'>
			<div style='z-index:-1;position:absolute;width:100%;' id='parent_save_loading'>
			<div style='width:100%;height:300px;display:block;position:relative;z-index:10px;text-align:center;padding-top:150px;' id='save_loading'></div>
			</div>
			<div style='display:none;' id='page_contents_convert' ></div>
	<textarea onkeydown=\"textarea_useTab( this, event );\" style='border:0px;overflow:auto;width:98%;height:300px;font-size:11px;font-family:돋움;' wrap='off' class='tline' name='page_contents' id='page_contents' >
	$thisfile
	</textarea>
			</td>
		</tr>
		</table>
		<table width='100%' border='0' cellspacing='0' cellpadding='0' >
		<tr>
			<td colspan=2 bgcolor='#ffffff' height='30' style='padding:10px 0 0 0'><textarea style='overflow:auto;width:98%;height:50px;font-size:11px;font-family:돋움;' wrap='off' class='tline' onfocus='CheckChangeMemo()' name='page_change_memo' >소스 수정내용을 입력해주세요</textarea></td>
		</tr>
	
		<tr>
			<td colspan=2 align=center style='padding:10px;'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
		$Contents .="
			<input type='checkbox' id='design_backup' name='design_backup' value='1'><label for='design_backup'>디자인 백업하기</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=image id='save_btn' src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle>";
			}else{
			$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle title=\" ' ".$db->dt[pname]." '  에 대한 정보를 수정합니다.\"></a>";
			}
	$Contents .="
			</td>
		</tr>
		</form>
		<tr><td colspan=2 align=center style='padding:0px;' id='design_history_area'>".PrintEditPageHistory($unique_file_path)."</td></tr>
	</table>
	
	
	                  ";
	
	
	$category_str ="<div class=box id=img3  style='width:155px;height:375px;overflow:auto;'>".Category()."</div>";
	
	
	
	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		switch($mod) {
			case ("layout") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 쇼핑몰 레이아웃";
				$P->title = "쇼핑몰 레이아웃";
			break;
			case ("layout/header") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 상단(header)";
				$P->title = "상단(header)";
			break;
			case ("layout/leftmenu") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 좌측메뉴(leftmenu)";
				$P->title = "좌측메뉴(leftmenu)";
			break;
			case ("layout/contents_add") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 추가컨텐츠";
				$P->title = "추가컨텐츠";
			break;
			case ("layout/rightmenu") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 오른쪽메뉴(rightmenu)";
				$P->title = "오른쪽메뉴(rightmenu)";
			break;
			case ("layout/footer") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 하단설명(footer)";
				$P->title = "하단설명(footer)";
			break;
			case ("css") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 스타일시트(css)";
				$P->title = "스타일시트(css)";
			break;
			case ("js") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 자바스크립트(js)";
				$P->title = "자바스크립트(js)";
			break;
			case ("etc") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 레이아웃기타";
				$P->title = "레이아웃기타";
			break;
			default :
				$P->Navigation = "디자인관리 > 레이아웃 디자인";
				$P->title = "레이아웃 디자인";
			break;
		}
		
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else if($mmode == "innerview"){
		echo "<body><div id='design_history_area'>".PrintEditPageHistory($unique_file_path)."</div></body>\n";
		echo "<script>parent.document.getElementById('design_history_area').innerHTML = document.getElementById('design_history_area').innerHTML </script>";
		exit;
	}else{
		$P = new LayOut;
		$P->addScript = "$Script";
		//$LO->OnloadFunction = "PageLoad();";//showSubMenuLayer('storeleft');
		switch($mod) {
			case ("layout") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 쇼핑몰 레이아웃";
				$P->title = "쇼핑몰 레이아웃";
			break;
			case ("layout/header") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 상단(header)";
				$P->title = "상단(header)";
			break;
			case ("layout/leftmenu") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 좌측메뉴(leftmenu)";
				$P->title = "좌측메뉴(leftmenu)";
			break;
			case ("layout/contents_add") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 추가컨텐츠";
				$P->title = "추가컨텐츠";
			break;
			case ("layout/rightmenu") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 오른쪽메뉴(rightmenu)";
				$P->title = "오른쪽메뉴(rightmenu)";
			break;
			case ("layout/footer") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 하단설명(footer)";
				$P->title = "하단설명(footer)";
			break;
			case ("css") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 스타일시트(css)";
				$P->title = "스타일시트(css)";
			break;
			case ("js") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 자바스크립트(js)";
				$P->title = "자바스크립트(js)";
			break;
			case ("etc") :
				$P->Navigation = "디자인관리 > 레이아웃 디자인 > 레이아웃기타";
				$P->title = "레이아웃기타";
			break;
			default :
				$P->Navigation = "디자인관리 > 레이아웃 디자인";
				$P->title = "레이아웃 디자인";
			break;
		}
		$P->strLeftMenu = design_menu("/admin",$category_str);
		$P->strContents = $Contents;
		$P->PrintLayOut();
	
	}
	
	
	
	function PrintEditPageHistory($page_name){
		global $admininfo, $page, $nset, $QUERY_STRING;
		global $auth_update_msg, $auth_delete_msg;
		$mdb = new Database;
		//echo $page_name;
	
		$sql = "select count(*) as total from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix ='".$admininfo[mall_ix]."'   ";
		$mdb->query($sql);
		$mdb->fetch();
		$total = $mdb->dt[total];
	
	
		$sql = "select * from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix ='".$admininfo[mall_ix]."'  order by regdate desc  ";
		//echo $sql;
		$mdb->query($sql);
	
		$max = 10;
	
		if ($page == ''){
			$start = 0;
			$page  = 1;
		}else{
			$start = ($page - 1) * $max;
		}
	
	
		$mString = "<table cellpadding=4 cellspacing=0 width=100% class='list_table_box'>";
		$mString = $mString."<tr align=center bgcolor=#efefef height=30><td class=s_td width='25%'>수정일자</td><td class=m_td width='50%'>수정내용</td><td class=e_td width='25%'>관리</td></tr>";
		if ($mdb->total == 0){
			$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=3 align=center>페이지 수정목록이 존재 하지 않습니다..</td></tr>";
		}else{
	
			//$mdb->query("select * from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' order by regdate desc   limit $start , $max");
	
			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
	
				//$no = $no + 1;
				if($mdb->dt[page_change_memo] == ""){
					$page_change_memo = "수정내용이 입력되지 않았습니다";
				}else{
					$page_change_memo = $mdb->dt[page_change_memo];
				}
	
				$mString = $mString."<tr height=45 bgcolor=#ffffff align=center>
				<td class='list_box_td list_bg_gray'>".$mdb->dt[regdate]."</td>
				<td class='list_box_td ' style='padding-left:20px;'>".$page_change_memo."</td>
				<td class='list_box_td list_bg_gray'' align=center >";
	
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$mString .= "<a href=JavaScript:pageHistoryRecovery('".$mdb->dt[page_ix]."')><img  src='../images/".$admininfo["language"]."/btn_recovery.gif' border=0 align=absmiddle></a> ";
				}else{
					$mString .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_recovery.gif' border=0 align=absmiddle ></a> ";
				}
	
				//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$mString .= "<a href=JavaScript:pageHistoryDelete('".$mdb->dt[page_ix]."')><img  src='../images/".$admininfo["language"]."/btn_delete.gif' border=0 align=absmiddle></a> ";
				}else{
					$mString .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btn_delete.gif' border=0 align=absmiddle ></a> ";
				}
	
				$mString .= "
					</td>
				</tr>
				";
			}
	
		}
	
		$query_string = str_replace("nset=$nset&page=$page&","",$QUERY_STRING) ;
		//echo $query_string;
		$mString = $mString."<tr height=50 bgcolor=#ffffff><td colspan=3 align=left>".page_bar($total, $page, $max,"&".$query_string,"")."</td></tr>
						</table>";
	
		return $mString;
	}
	
	
	
	function FileList2 ( $path , $maxdepth = -1 , $mode = "FULL" , $d = 0 ){
	global $page_name;
	   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }
	   $dirlist = array () ;
	   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
	   if(!is_dir($path)){return false;};
	   if ( $handle = opendir ( $path ) )
	   {
	
	       while ( false !== ( $file = readdir ( $handle ) ) )
	       {
	           if ( $file != '.' && $file != '..' )
	           {
	               $only_file = $file;
	               $file = $path . $file ;
	               if ( ! is_dir ( $file ) || $mode == "FULL"){
	               		if(is_dir ( $file )){
	               			//$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
	               		}else{
	               			if($page_name == $only_file){
	               				$mstring .=  "<option value='".$only_file ."' selected>".$only_file ."</option>";
	               			}else{
	               				$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
	               			}
	               		}
	
	               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
	                   $mstring .= FileList2 ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;
	                  $mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
	               }
	           }
	       }
	       closedir ( $handle ) ;
	   }
	   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
	   return ( $mstring ) ;
	}
	
	function SelectFileList2($path){
		global $DOCUMENT_ROOT, $mod, $SubID, $mmode;
		if($path == ""){
			$path = $_SERVER["DOCUMENT_ROOT"]."/data/sample/templet/basic";
		}
	
		$mstring =  "<select name='page_name' onchange=\"document.location.href='design.mod.php?SubID=$SubID&mod=$mod&page_name='+this.value+'&mmode=$mmode'\">";
		$mstring .= "<option value=''>파일을 선택해주세요</option>";
		if(FileList2($path, 0, "FULL")){
			$mstring .= FileList2($path, 0, "FULL");
		}else{
			$mstring .= "<option>파일이 존재하지않습니다.</option>";
		}
		$mstring .=  "</select>";
	
		return $mstring;
	}
	




?>
