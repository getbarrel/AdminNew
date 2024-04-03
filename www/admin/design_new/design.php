<?
	include("../class/layout.class");
	//include "../class/LayoutXml/LayoutXml.class";
	
	include("category.lib.php");
	include("design.common.php");
	include("./designlib/ConvertLayout2Lib.php");
	
	if($admin_config["selected_templete"] == "") {
		$admin_config["selected_templete"] = $admin_config["mall_use_templete"];
	}
// 	 echo($admin_config["selected_templete"]);
// 	 echo("aaaaa");
// 	 echo($admin_config["mall_use_templete"]);

	if (file_exists
			(
					$_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout2.xml"
			)
		) 
	{} else {
		ConvertLayout2();
	}
		
	
	
	
//	print_r($_SESSION);	
	
 	$db = new Database;
	if($pcode == ""){
		$pcode="000000000000000";
		$depth="0";
		$category_display_type="P";
		$SubID="SM114641Sub";
	}
	//print_r($admin_config);
	//echo("<br />");
	
	$page_path = getDesignTempletPath($pcode, $depth);
	
// 	echo($layoutXmlPath);
// 	echo("<br />");
// 	echo($admininfo["mall_data_root"]);
//  	exit;

	switch ($admin_config["mall_page_type"]){
		case "P":
			$layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout2.xml";
			break;
		case "M":
			//echo($admin_config["mall_page_type"]);
			//echo("<br />");
			$layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/mobile_templet/" . $admin_config["selected_templete_mobile"] . "/layout2.xml";
			break;
	}
	
	$layoutXml = new LayoutXml($layoutXmlPath);
	
	$db->query("select cname from ".TBL_SHOP_LAYOUT_INFO." where  cid ='$pcode' ");
 	$db->fetch();

	$resultFindByPcode = $layoutXml->search("layouts", array("pcode"), array($pcode));
	$page_title = $resultFindByPcode[0]->cname;
	
	
	if(file_exists($layoutXmlPath))
	{
	} else {
		echo("레이아웃 파일 없음" . $layoutXmlPath);
		exit("파일없음");
	}
	
	//echo $sql;
	//$db->query($sql);
	//$db->fetch();
// 	echo($layoutXmlPath);
// 	echo("<br />");

	$layoutFindbyPcode = $layoutXml->search("layouts", array("pcode")
								   , array($pcode));
	$contents_add = "";
	
	//print_r($layouts);
	if (count($layoutFindbyPcode) != 0){
		foreach ($layoutFindbyPcode as $layout) {
			$page_name = $layout->contents;
			$contents_add = $layout->contents_add;
			$templet_name = $layout->templet_name;
			$page_link = $layout->page_link;
			$page_desc = $layout->page_desc;
			$page_help = $layout->page_help;
			$page_type = $layout->page_type;
			$page_navi = $layout->page_navi;
		}
		$layout_act = "update";
	} else {
		$page_type = "A";
		$layout_act = "insert";
	}

	$results = $layoutXml->search("layouts", array("mall_ix", "skin_type", "pcode", "templet_name")
										   , array($admininfo[mall_ix] 
										   , $admin_config[mall_page_type]
										   , $pcode
										   , $admin_config[selected_templete]));

	
	if(count($results) == 0){
		
		//print_r($layoutXml->layouts);
// 		$xmlDataToCopy = $layoutXml->search("layouts"
// 										   , array("mall_ix", "skin_type", "pcode", "templet_name")
// 										   , array(trim($admininfo[mall_ix]), trim($admin_config[mall_page_type]), trim($pcode), 'stylestory'));
		
// 		print_r(array($admininfo[mall_ix], $admin_config[mall_page_type], $pcode, 'stylestory'));
// 		$xmlDataToCopy = $layoutXml->search("layouts"
// 										  , array("mall_ix", "skin_type", "pcode", "templet_name")
//          				                  , array("d02b37324dd0b08f6bc0f3847673e7d5" ,"P", "002003000000000", "stylestory"));

// 		echo($layoutXmlPath);
// 		$xmlDataToCopy = $layoutXml->search("layouts", array("mall_ix", "skin_type", "pcode", "templet_name")
// 													 , array("d02b37324dd0b08f6bc0f3847673e7d5" ,"P", "002003000000000", "stylestory"));
		
// 		mall_ix="d02b37324dd0b08f6bc0f3847673e7d5" templet_name="stylestory" skin_type="P" pcode="002003000000000"
		
		
// 		print_r($xmlDataToCopy);
// 		exit;
// 		foreach($xmlDataToCopy as $xmlItemToCopy){
		   //$layoutitem = new LayoutItem();
// 		   $layoutitem = $xmlDataToCopy;
		    
		    
// 		   $layoutXml->layouts[count($layoutXml->layouts)] = $layoutitem;
// 		}
		
// 		if($admin_config[mall_page_type] == "P"){
// 		$sql = "insert into shop_design
// 				select pcode,mall_ix, '".$admin_config[mall_page_type]."' as skin_type, page_type, page_path, page_link, page_title, page_help ,page_addscript,
// 				page_body, page_desc, page_navi, layout, header1, header2, leftmenu, contents, contents_add, rightmenu,footer1,footer2,caching,caching_time, '".$admin_config[selected_templete]."' as templet_name,NOW()
// 				from ".TBL_SHOP_DESIGN." where mall_ix = '".$admininfo[mall_ix]."' and skin_type = '".$admin_config[mall_page_type]."' and templet_name = 'photoskin' ";
// 		}else if($admin_config[mall_page_type] == "M"){
// 		$sql = "insert into shop_design
// 				select pcode,mall_ix, '".$admin_config[mall_page_type]."' as skin_type, page_type, page_path, page_link, page_title, page_help ,page_addscript,
// 				page_body, page_desc, page_navi, layout, header1, header2, leftmenu, contents, contents_add, rightmenu,footer1,footer2,caching,caching_time, '".$admin_config[selected_templete]."' as templet_name,NOW()
// 				from ".TBL_SHOP_DESIGN." where mall_ix = '".$admininfo[mall_ix]."' and skin_type = '".$admin_config[mall_page_type]."' and templet_name = 'mobile' ";
// 		}else if($admin_config[mall_page_type] == "MI"){
// 		$sql = "insert into shop_design
// 				select pcode,mall_ix, '".$admin_config[mall_page_type]."' as skin_type, page_type, page_path, page_link, page_title, page_help ,page_addscript,
// 				page_body, page_desc, page_navi, layout, header1, header2, leftmenu, contents, contents_add, rightmenu,footer1,footer2,caching,caching_time, '".$admin_config[selected_templete]."' as templet_name,NOW()
// 				from ".TBL_SHOP_DESIGN." where mall_ix = '".$admininfo[mall_ix]."' and skin_type = '".$admin_config[mall_page_type]."' and templet_name = 'basic' ";
// 		}
	}
	
	$templet_path = $admin_config[mall_data_root]."/templet/".$admin_config[selected_templete_general];
	
 	if($admin_config[mall_page_type] == "P"){
 		$templet_path = $admin_config[mall_data_root]."/templet/".$admin_config[selected_templete_general];
 		$templet_file_path = $templet_path."/".$page_path."/".$page_name;
 		if($pcode != ""){
 			$page_path_string = $admin_config[selected_templete_general]."/$page_path/".$page_name;
 		}
 	}else if($admin_config[mall_page_type] == "M"){
 		$templet_path = $admin_config[mall_data_root]."/mobile_templet/".$admin_config[selected_templete_mobile];
 		$templet_file_path = $templet_path."/".$page_path."/".$page_name;
 		if($pcode != ""){
 			$page_path_string = $admin_config[selected_templete_mobile]."/$page_path/".$page_name;
 		}
 	}else if($admin_config[mall_page_type] == "MI"){
 		//exit;
 		$templet_path = $admin_config[mall_data_root]."/minishop_templet/".$admin_config[selected_templete_minishop];
 		//echo $templet_path;
 		$templet_file_path = $templet_path."/".$page_path."/".$page_name;
 		if($pcode != ""){
 			$page_path_string = $admin_config[selected_templete_minishop]."/$page_path/".$page_name;
 		}
 	}


	$thisfile = load_template($DOCUMENT_ROOT.$templet_file_path);
	$thisfile_convert = eregi_replace("{templet_src}",$templet_path, $thisfile);
	$thisfile_convert = eregi_replace("@_templet_path",$templet_path, $thisfile_convert);
	$thisfile_convert = str_replace("</textarea>","&lt;/textarea&gt;", $thisfile_convert);
	$thisfile_convert = str_replace("<textarea","&lt;textarea", $thisfile_convert);
	$thisfile_convert = str_replace("</form>","&lt;/form&gt;", $thisfile_convert);
	$thisfile_convert = str_replace("<form","&lt;form", $thisfile_convert);
	
	$thisfile = str_replace("</textarea>","&lt;/textarea&gt;", $thisfile);
	$thisfile = str_replace("<textarea","&lt;textarea", $thisfile);
	$thisfile = str_replace("</FORM>","&lt;/FORM&gt;", $thisfile);
	$thisfile = str_replace("<FORM","&lt;FORM", $thisfile);
	
	
	
	$Script = "
	<Script Language='JavaScript' src='../js/XMLHttp.js'></Script>
	<Script Language='JavaScript' src='design.js'></Script>
	<Script Language='JavaScript'>
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
			//document.getElementById('save_btn').focus();
	
		//alert(iView.document.body.innerHTML);
		//frm.content.value = iView.document.body.innerHTML;
		//document.write (frm.content.value);
		//document.forms['info_input'].page_contents.value = document.forms['info_input'].page_contents.value.replace('&lt;/textarea&gt;','</textarea>');
		//document.forms['info_input'].page_contents.value = document.forms['info_input'].page_contents.value.replace('&lt;textarea','<textarea');
	
		/*
		if(frm.page_add_script.basic_message == 'true'){
			frm.page_add_script.value = '';
		}
	
		if(frm.page_body_property.basic_message == 'true'){
			frm.page_body_property.value = '';
		}
		*/
		if(frm.page_navi_property.basic_message == 'true'){
			frm.page_navi.value = '';
		}
	
		if(frm.page_change_memo.basic_message == 'true'){
			frm.page_change_memo.value = '';
		}
	
		return true;
	}
	
	
	function CheckFocus(obj){
		if(obj.basic_message == 'true'){
			obj.value = '';
			obj.basic_message = 'false'
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
	
	//	contents = contents.replace('&lt;textarea','<textarea');
	//	contents = contents.replace('&lt;/textarea&gt;','</textarea>');
		contents = contents.replace(/{templet.src}/gi,'$templet_path');
	
		//alert(contents);
	
			var htmlString = '<html>';
	
			htmlString += '<LINK REL=stylesheet HREF=../include/admin.css TYPE=text/css>';
			htmlString += '<head>';
			htmlString += '<title>' + picTitle + '</title>';
			htmlString += '</head>';
			htmlString += '<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0  noreize>';
			htmlString += ''+contents;
			htmlString += '</body></html>';
			winHandle.document.open();
			winHandle.document.write(htmlString);
			winHandle.document.close();
		}
	}
	
	function pview_pop(location, windowName, picTitle){
	
		var winHandle = window.open(location ,windowName,',left=0,top=0,width=1000,height='+screen.height+',scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=no');
		
	}
	
	
	function pageHistoryDelete(page_ix){
		if(confirm(language_data['design.php']['A'][language])){//페이지 수정내용과 백업 소스를 정말로 삭제하시겠습니까?
			//document.frames['act'].location.href='design.act.php?design_act=delete&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_path."/".$page_name."'//kbk
			document.getElementById('act').src='design.act.php?design_act=delete&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_path."/".$page_name."'
		}
	}
	
	function pageHistoryRecovery(page_ix){
		if(confirm('해당내용으로 복구하시겠습니까? 1차적으로 화면에만 복구되게 되며 완전한 복구를 원하실때는 화면 복구후 저장버튼을 눌러주시기 바랍니다. ')){
			//document.frames['act'].location.href='design.act.php?design_act=recovery&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_path."/".$page_name."'//kbk
			document.getElementById('act').src='design.act.php?design_act=recovery&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_path."/".$page_name."'
		}
	}
	
	
	function showTabContents(vid, tab_id){
		var area = new Array('design_structure','design_contents','design_help');
		var tab = new Array('tab_01','tab_02','tab_03');
	
		for(var i=0; i<area.length;i++){
			if(area[i]==vid){
				document.getElementById(vid).style.display = '';
				if(window.addEventListener) document.getElementById(tab_id).setAttribute('class','on');//kbk
				else document.getElementById(tab_id).className = 'on';
			}else{
				document.getElementById(area[i]).style.display = 'none';
				if(window.addEventListener) document.getElementById(tab[i]).setAttribute('class','');//kbk
				else document.getElementById(tab[i]).className = '';
			}
			if(vid=='design_help') { // 익스의 경우 동영상이 탭을 바꿔도 계속 재생되므로 추가함 2011-04-12 kbk
				document.getElementById('design_help_movie').innerHTML=viewMenual2(\"몰스토리동영상메뉴얼_페이지상세디자인(090322)_config.xml\", 800, 517);
			} else {
				document.getElementById('design_help_movie').innerHTML='';
			}
		}
	
	
	
	}
	
	function setCategory(cname,cid,depth,category_display_type){
		if(category_display_type == 'F'){
			alert('분류정보는 페이지 구성을 하실수 없으며 하부에 페이지를 생성 하실수 있습니다. 아래 디자인구성을 클릭하셔서 구성정보를 수정하실수 잇습니다.');
		}else{
			document.location.href='design.php?pcode='+cid+'&depth='+depth+'&category_display_type='+category_display_type+'&SubID=SM114641Sub'
		}
	}
	
	function ctrlSave() {
	
		if(event.ctrlKey == true && event.keyCode == 83 ){
			var pc_obj = document.getElementById('page_contents');
			//alert(1);
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
			//alert(event.keyCode);
			event.returnValue = false;
	
			pc_obj.form.submit();
		}
	
	}
	
	function unloading(){
		var obj = parent.document.getElementById('page_contents');
		obj.readOnly = false;
		obj.style.border = '1px solid silver';
		parent.document.getElementById('parent_save_loading').style.zIndex = '-1';
		parent.document.getElementById('loadingbar').innerHTML ='';
		parent.document.getElementById('save_loading').innerHTML ='';
	
	
		parent.document.getElementById('save_loading').style.display = 'none';
		parent.document.getElementById('page_contents').focus();
	}
	
	
	function clearAll(frm){
			for(i=0;i < frm.page_ix.length;i++){
					frm.page_ix[i].checked = false;
			}
	}
	function checkAll(frm){
	       	for(i=0;i < frm.page_ix.length;i++){
					frm.page_ix[i].checked = true;
			}
	}
	function fixAll(frm){
		if (!frm.all_fix.checked){
			clearAll(frm);
			frm.all_fix.checked = false;
	
		}else{
			checkAll(frm);
			frm.all_fix.checked = true;
		}
	}
	
	
	function CheckDelete(frm){
		if(confirm('선택하신 디자인 백업을 정말로 삭제하시겠습니까? 삭제하신 디자인 백업 복원되지 않습니다')){
			for(i=0;i < frm.page_ix.length;i++){
				if(frm.page_ix[i].checked){
					return true	;
				}
			}
			alert('삭제하실 목록을 한개이상 선택하셔야 합니다.');
		}
		return false;
	
	}
	
	function SelectDelete(frm){
		frm.design_act.value = 'select_delete';
		if(CheckDelete(frm)){
			frm.submit();
		}
	
	}
	
	//document.onkeypress = ctrlSave;
	";
	
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Script .= "document.onkeydown = ctrlSave;";
	}
	$Script .= "
	</Script>";
	
	$help_text = "
		<TABLE cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
			<TR>
				<TD width=100 align=left valign=top class=small > <img src='/admin/image/icon_list.gif' align=absmiddle><b style='color:darkorange'>공통치환코드</b> </TD>
				<TD bgColor=#ffffff class=small style='line-height:150%'>
				<b>상단1</b> : {header_top}
				<b>상단2</b> : {header_menu}
				<b>좌측</b> : {center_leftmenu}
				<b>컨텐츠</b> : {center_contents}
				<b>우측</b> : {center_rightmenu}
				<b>하단1</b> : {footer_menu}<br>
				<b>하단2</b> : {footer_desc}
	
				</TD>
			</TR>
			<TR height=40>
				<TD  colspan=2 valign=top align=left style='padding:5px 0 0 0;line-height:120%' class=small>
				<img src='/admin/image/icon_list.gif' align=absmiddle style='margin:2px 0 5px 0'><!--아래 원하는 레이아웃을 각각의 위치에 해당되는 파일을 선택해주세요.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." <br>
				<img src='/admin/image/icon_list.gif' align=absmiddle style='margin:2px 0 5px 0'><!--새로운 레이아웃을 만드실경우에는 위와 같이 치환코드를 삽입해주셔야 합니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."<br>";
	if($category_display_type == "B"){
		$help_text .="<img src='/admin/image/icon_list.gif' align=absmiddle style='margin:2px 0 5px 0'> <!--게시판 디자인은 게시판 <u><b>생성, 수정시 템플릿</b></u>을 선택하시면 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."<br>
		<img src='/admin/image/icon_list.gif' align=absmiddle style='margin:2px 0 5px 0'><!--게시판 페이지 디자인후 게시판이 삽입될 곳에 <b>{bbs_area}</b> 를 넣어주세요.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')."<br>";
	}else{
		/*$help_text .="<img src='/admin/image/icon_list.gif' align=absmiddle style='margin:2px 0 5px 0'><b>캐쉬설정</b> 기능을 이용하여 자주 갱신되지 않으며 많이 노출되는 웹페이지를 캐쉬하여 전체적인 웹사이트의 성능을 향상 시킬수 있습니다. 디자인 구성정보및 디자인 페이지 내용이 갱신되면 자동으로 캐쉬가 갱신됩니다<br>
		<img src='/admin/image/icon_list.gif' align=absmiddle style='margin:2px 0 5px 0'>게시판 , 마이페이지 등은 캐쉬 설정을 하지 않는것을 권장합니다.<br>";*/
		$help_text .= getTransDiscription(md5($_SERVER["PHP_SELF"]),'G');
	}
	$help_text .="
				</TD>
			</TR>
		</TABLE>
		";
	
	
		$help_text = HelpBox("레이아웃 디자인", $help_text,171);
	
	$Contents ="
	<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' style='vertical-align:top'>";
	if($mmode != "pop"){
	$Contents .="
		<tr>
			<td colspan=2>
			".SelectDesignSkin()."
			</td>
		</tr>";
	}
	

	$Contents .="
		<tr height=30>
		    <td align='left'> ".GetTitleNavigation("페이지 상세 디자인", "디자인관리 > 페이지 상세 디자인 <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_페이지상세디자인(090322)_config.xml',800,517,'manual_view')\" ><img src='../image/movie_manual.gif' align=absmiddle style='margin:0 0 5px 0'></a>")."</td>
		</tr>
		<tr height=30><td align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;text-align:right;'><img src='../image/title_head.gif' ><b> 선택된 페이지 : [$page_title] <a href=\"JavaScript:pview_pop('/main/page.php?pgid=".$_GET["pcode"]."', 'aaa', 'bbb');\">$page_path_string</a> <a href=\"JavaScript:pview_pop('/main/page.php?pgid=".$_GET["pcode"]."', 'aaa', 'bbb');\"><img src='../images/".$admininfo["language"]."/btn_page_design_view.gif' border=0 align=absmiddle></a></b></div>")."</td></tr>
		<tr>
		    <td align='left' style='vertical-align:top'>
		   			 <div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>
								<table id='tab_01' ".($tab_no == "01" || !$tab_no ?  "class='on'":"") ."  >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"showTabContents('design_structure','tab_01')\">페이지 구성 설정하기</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_02' ".($tab_no == "02" ? "class='on'":"") ." >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"showTabContents('design_contents','tab_02')\">페이지 내용 수정하기</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_03' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"showTabContents('design_help','tab_03')\" style='position:relative;width:70px;'><img src='/admin/image/movie_manual.gif' border=0 align=absmiddle style='position:relative;top:-5px;'> 도움말 </td>
									<th class='box_03'></th>
								</tr>
								</table>
								<!--table id='tab_04' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' >탭 메뉴 4</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_05' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' >탭 메뉴 5</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_06' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' >탭 메뉴 6</td>
									<th class='box_03'></th>
								</tr>
								</table-->
							</td>
							<td class='btn'>
	
							</td>
						</tr>
						</table>
					</div>
					<div class='mallstory t_no'>
						<!-- my_movie start -->
	
						<div class='my_box' style='padding:10px;'>
							<table id='design_structure' ".($tab_no == "01" || !$tab_no ? " ":"style='display:none;'") ." width=100% border=0>
							<col width='400px;'>
							<col width='*'>
							<tr>
								<td colspan=2>
									".$help_text."<br>
								</td>
							</tr>
							<form name=layout_info action='design.act.php' method='post' onsubmit='return CheckFormValue(this);' target='act'>
							<input type='hidden' name=layout_act value='$layout_act'>
							<input type='hidden' name=page_type value='$page_type'>
							<input type='hidden' name=page_path value='$page_path'>
							<input type='hidden' name=tab_no value='01'>
							<!--input type='hidden' name=pcode value='$pcode'-->
							<input type='hidden' name=mall_ix value='".$admininfo[mall_ix]."'>
							<tr style='display:none;'>
								<td colspan=2>
									<table id='design_add' style='width:100%'>
									<tr>
										<td><img src='../image/ico_dot.gif'> 페이지 코드 :</td>
										<td>
										<input type='text' name=pcode value='$pcode' readonly> <span class=small style='color:gray'>기본페이지의 페이지 코드는 변경하실수 없습니다.</b></span>
										</td>
									</tr>
									<tr>
										<td><img src='../image/ico_dot.gif'> 페이지명 :</td>
										<td>
										<input type='text' name=page_title value='$page_title' > <span class=small style='color:gray'>해당페이지의 이름을 입력해주세요 <b>예) 메인페이지</b></span>
										</td>
									</tr>
									<tr>
										<td><img src='../image/ico_dot.gif'> 페이지 설명 :</td>
										<td>
										<input type='text' name=page_desc value='$page_desc' style='width:100%'>
										</td>
									</tr>
									<tr>
										<td><img src='../image/ico_dot.gif'> 페이지 링크 :</td>
										<td>
										<input type='text' name=page_link value='$page_link' style='width:100%'>
										</td>
									</tr>
									<tr>
										<td><img src='../image/ico_dot.gif'> 페이지 도움말 :</td>
										<td>
										<textarea  onkeydown=\"textarea_useTab( this, event );\"  wrap='off' class='tline' style='height:100px;'  name='page_help' > $page_help </textarea>
										</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td valign=top>
									<table cellpadding=3 cellspacing=0 width=100%>
										<tr align=center>
											<td><b style='color:gray'>layout.htm</b></td>
											<td><b style='color:gray'>layout2.htm</b></td>
											<td><b style='color:gray'>layout3.htm</b></td>
										</tr>
										<tr>
											<td>
												<table cellpadding=5 cellspacing=1 border=0 bgcolor=silver width=180 height=220 align=left style='border:5px solid #efefef;'>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#9eeafb><b title='{header.top}'>상단1</b></td></tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#9eeafb><b title='{header.menu}'>상단2</b></td></tr>
													<tr bgcolor=#ffffff height=130 align=center>
														<td width=45 bgcolor=#9efbc3><b title=' {center.leftmenu}'>좌측</b></td>
														<td width=140 bgcolor=#c6fb9e><b title='{center.contents}'>컨텐츠</b></td>
														<td width=45 bgcolor=#9efbc3><b title='{center.rightmenu}'>우측</b></td></tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#fbdca8><b title='{footer.menu}'>컨텐츠추가</b></td></tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#fbdca8><b title='{footer.menu}'>하단1</b></td></tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#fbdca8><b title='{footer.desc}'>하단2</b></td></tr>
												</table>
											</td>
											<td>
												<table cellpadding=5 cellspacing=1 border=0 bgcolor=silver width=180 height=220 align=left  style='border:5px solid #efefef;'>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#9eeafb><b title='{header.top}'>상단1</b></td></tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#9eeafb><b title='{header.menu}'>상단2</b></td></tr>
													<tr bgcolor=#ffffff height=130 align=center>
														<td width=45 bgcolor=#9efbc3><b title=' {center.leftmenu}'>좌측</b></td>
														<td width=180 bgcolor=#c6fb9e><b title='{center.contents}'>컨텐츠</b></td>
													</tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#fbdca8><b title='{footer.menu}'>컨텐츠추가</b></td></tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#fbdca8><b title='{footer.menu}'>하단1</b></td></tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#fbdca8><b title='{footer.desc}'>하단2</b></td></tr>
												</table>
											</td>
											<td>
												<table cellpadding=5 cellspacing=1 border=0 bgcolor=silver width=180 height=220 style='border:5px solid #efefef;'>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#9eeafb><b title='{header.top}'>상단1</b></td></tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#9eeafb><b title='{header.menu}'>상단2</b></td></tr>
													<tr bgcolor=#ffffff height=130 align=center>
														<td width=190 colspan=3 bgcolor=#c6fb9e><b title='{center.contents}'>컨텐츠</b></td>
													</tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#fbdca8><b title='{footer.menu}'>컨텐츠추가</b></td></tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#fbdca8><b title='{footer.menu}'>하단1</b></td></tr>
													<tr bgcolor=#ffffff align=center><td colspan=3 bgcolor=#fbdca8><b title='{footer.desc}'>하단2</b></td></tr>
												</table>
											</td>
										</tr>
									</table>
						         </td>
						         <td align=left style='padding:10px;'>
						                	<table cellpadding=0 cellspacing=0 border=0 width=100%>
						                		<tr>
													<td colspan=3 align=left style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:2px 5px 2px 15px;'><b> 선택된 페이지 : [$page_title]  <a href=\"JavaScript:pview(document.getElementById('page_contents').value, 'aaa', 'bbb');\"><img src='../images/".$admininfo["language"]."/btn_page_design_view.gif' border=0 align=absmiddle></a></b></div>")." 
													</td>
												</tr>
											</table>
											<table cellpadding=0 cellspacing=0 border=0 width=100% class='input_table_box'>
						                		<col width='100px'>
												<col width='*'>
												<tr>
													<td class='input_box_title' nowrap>레이아웃</td>
													<td class='input_box_item'>
														<table cellpadding=0 cellspacing=0 >
															<tr>
																<td>" . SelectFileList("layout",$DOCUMENT_ROOT.$templet_path."/layout",$layout->layout)."</td>
																<td style='padding:3px;'>
																<a href=\"javascript:PopSWindow('design.mod.php?mod=layout&page_name=".$layout->layout."&mmode=pop',980,600,'design')\"'><img src='/admin/images/".$admininfo["language"]."/btn_page_edit.gif'></a>
																</td>
															</tr>
														</table>
													</td>
												</tr>
						                		<tr>
													<td class='input_box_title' nowrap>상단1</td>
													<td class='input_box_item'>
														<table cellpadding=0 cellspacing=0 >
															<tr>
																<td>".SelectFileList("header1",$DOCUMENT_ROOT.$templet_path."/layout/header",$layout->header1)."</td>
																<td style='padding:3px;'>
																<a href=\"javascript:PopSWindow('design.mod.php?mod=layout/header&page_name=".$layout->header1."&mmode=pop',980,600,'design')\"'><img src='/admin/images/".$admininfo["language"]."/btn_page_edit.gif'></a>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td class='input_box_title' nowrap>상단2</td>
													<td class='input_box_item'>
														<table cellpadding=0 cellspacing=0 >
															<tr>
																<td>".SelectFileList("header2",$DOCUMENT_ROOT.$templet_path."/layout/header",$layout->header2)."</td>
																<td style='padding:3px;'>
																<a href=\"javascript:PopSWindow('design.mod.php?mod=layout/header&page_name=".$layout->header2."&mmode=pop',980,600,'design')\"'><img src='/admin/images/".$admininfo["language"]."/btn_page_edit.gif'></a>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td class='input_box_title' nowrap>좌측</td>
													<td class='input_box_item'>
														<table cellpadding=0 cellspacing=0 >
															<tr>
																<td>".SelectFileList("leftmenu",$DOCUMENT_ROOT.$templet_path."/layout/leftmenu",$layout->leftmenu)."</td>
																<td style='padding:3px;'>
																<a href=\"javascript:PopSWindow('design.mod.php?mod=layout/leftmenu&page_name=".$layout->leftmenu."&mmode=pop',980,600,'design')\"'><img src='/admin/images/".$admininfo["language"]."/btn_page_edit.gif'></a>
																</td>
															</tr>
														</table>
													</td>
												</tr>
													";
		
					if($category_display_type == "B"){
					$Contents .="			<tr>
													<td class='input_box_title' nowrap>컨텐츠</td>
													<td class='input_box_item point'>
														<table cellpadding=0 cellspacing=0 >
															<tr>
																<td>".SelectFileList("contents",$DOCUMENT_ROOT.$templet_path."/".$page_path,$page_name)." </td>
																<td style='padding:3px;'>
																<a href=\"javascript:PopSWindow('./addfile.pop.php?pcode=".$pcode."&page_path=$page_path',420,170,'fileadd_pop')\"><img src='../images/".$admininfo["language"]."/btn_page_add.gif' border=0 align=absmiddle></a>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td class='input_box_title' nowrap>컨텐츠 추가</td>
													<td class='input_box_item'>
														<table cellpadding=0 cellspacing=0 >
															<tr>
																<td>".SelectFileList("contents_add",$DOCUMENT_ROOT.$templet_path."/layout/contents_add",$layout->contents_add)."</td>
																<td style='padding:3px;'>
																<a href=\"javascript:PopSWindow('./addfile.pop.php?pcode=".$pcode."&page_path=layout/contents_add',420,170,'fileadd_pop')\"><img src='../images/".$admininfo["language"]."/btn_page_add.gif' border=0 align=absmiddle></a>
																</td>
															</tr>
														</table>
													</td>
												</tr>";
					}else{
						
					$Contents .="			<tr>
													<td class='input_box_title' nowrap>컨텐츠</td>
													<td class='input_box_item point'>
														<table cellpadding=0 cellspacing=0 >
															<tr>
																<td>".SelectFileList("contents",$DOCUMENT_ROOT.$templet_path."/".$page_path,$page_name)."</td>
																<td style='padding:3px;'>
																<a href=\"javascript:PopSWindow('./addfile.pop.php?pcode=".$pcode."&page_path=$page_path',420,170,'fileadd_pop')\"><img src='../images/".$admininfo["language"]."/btn_page_add.gif' border=0 align=absmiddle></a>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td class='input_box_title' nowrap>컨텐츠 추가</td>
													<td class='input_box_item'>
														<table cellpadding=0 cellspacing=0 >
															<tr>
																<td>".SelectFileList("contents_add",$DOCUMENT_ROOT.$templet_path."/layout/contents_add",$layout->contents_add)."</td>
																<td style='padding:3px;'>
																<a href=\"javascript:PopSWindow('./addfile.pop.php?pcode=".$pcode."&page_path=layout/contents_add',420,170,'fileadd_pop')\"><img src='../images/".$admininfo["language"]."/btn_page_add.gif' border=0 align=absmiddle></a>
																</td>
															</tr>
														</table>
													</td>
												</tr>";
					}
	
					
					$Contents .="			<tr>
													<td class='input_box_title' nowrap>우측</td>
													<td class='input_box_item'>
														<table cellpadding=0 cellspacing=0 >
															<tr>
																<td>".SelectFileList("rightmenu",$DOCUMENT_ROOT.$templet_path."/layout/rightmenu",$layout->rightmenu)."</td>
																<td style='padding:3px;'>
																<a href=\"javascript:PopSWindow('design.mod.php?mod=layout/rightmenu&page_name=".$layout->rightmenu."&mmode=pop',980,600,'design')\"'><img src='/admin/images/".$admininfo["language"]."/btn_page_edit.gif'></a>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td class='input_box_title' nowrap>하단1</td>
													<td class='input_box_item'>
														<table cellpadding=0 cellspacing=0 >
															<tr>
																<td>".SelectFileList("footer1",$DOCUMENT_ROOT.$templet_path."/layout/footer",$layout->footer1)."</td>
																<td style='padding:3px;'>
																<a href=\"javascript:PopSWindow('design.mod.php?mod=layout/footer&page_name=".$layout->footer1."&mmode=pop',980,600,'design')\"'><img src='/admin/images/".$admininfo["language"]."/btn_page_edit.gif'></a>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td class='input_box_title' nowrap>하단2</td>
													<td class='input_box_item'>
														<table cellpadding=0 cellspacing=0 >
															<tr>
																<td>".SelectFileList("footer2",$DOCUMENT_ROOT.$templet_path."/layout/footer",$layout->footer2)."</td>
																<td style='padding:3px;'>
																<a href=\"javascript:PopSWindow('design.mod.php?mod=layout/footer&page_name=".$layout->footer2."&mmode=pop',980,600,'design')\"'><img src='/admin/images/".$admininfo["language"]."/btn_page_edit.gif'></a>
																</td>
															</tr>
														</table>
													</td>
												</tr>
	
						                		<tr>
						                				<td class='input_box_title'>캐쉬설정</td>
						                				<td class='input_box_item'>
						                				<select name='caching' style='font-size:12px;'>
						                					<option value='0' ".($layout->caching == "0" ? "selected":"").">캐쉬사용안함</option>
						                					<option value='1' ".($layout->caching == "1" ? "selected":"").">캐쉬사용</option>
						                				</select>
						                				</td>
						                		</tr>
						                		<tr>
						                			<td class='input_box_title'>캐쉬타임</td>
						                			<td class='input_box_item'>
						                				<select name='caching_time' style='font-size:12px;'>
						                					<option value='0' ".($layout->caching_time == "0" ? "selected":"").">무제한</option>
						                					<option value='600' ".($layout->caching_time == "600" ? "selected":"").">10분</option>
						                					<option value='1200' ".($layout->caching_time == "1200" ? "selected":"").">20분</option>
						                					<option value='1800' ".($layout->caching_time == "1800" ? "selected":"").">30분</option>
						                					<option value='2400' ".($layout->caching_time == "2400" ? "selected":"").">40분</option>
						                					<option value='3000' ".($layout->caching_time == "3000" ? "selected":"").">50분</option>
						                					<option value='3600' ".($layout->caching_time == "3600" ? "selected":"").">60분 (1시간)</option>
						                					<option value='7200' ".($layout->caching_time == "7200" ? "selected":"").">120분 (2시간)</option>
						                					<option value='10800' ".($layout->caching_time == "10800" ? "selected":"").">180분 (3시간)</option>
						                					<option value='14400' ".($layout->caching_time == "14400" ? "selected":"").">240분 (4시간)</option>
						                					<option value='18000' ".($layout->caching_time == "18000" ? "selected":"").">300분 (5시간)</option>
						                					<option value='21600' ".($layout->caching_time == "21600" ? "selected":"").">3600분 (6시간)</option>
						                					<option value='43200' ".($layout->caching_time == "43200" ? "selected":"").">7200분 (12시간)</option>
						                					<option value='86400' ".($layout->caching_time == "86400" ? "selected":"").">14400분 (24시간)</option>
						                					<option value='604800' ".($layout->caching_time == "604800" ? "selected":"").">1주일</option>
						                					<option value='1209600' ".($layout->caching_time == "1209600" ? "selected":"").">2주일</option>
						                					<option value='1814400' ".($layout->caching_time == "1814400" ? "selected":"").">3주일</option>
						                					<option value='2419200' ".($layout->caching_time == "2419200" ? "selected":"").">4주일</option>
						                					<option value='2592000' ".($layout->caching_time == "2592000" ? "selected":"").">1개월</option>
						                				</select>
						                			</td>
						                		</tr>
						                	</table>
						                </td>
									</tr>";
									//if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					
						$Contents .="<tr>
						                <td colspan='3' align=center style='padding:10px 10px 30px 10px;'>";
	
										if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
											$Contents .="<input type='checkbox' id='basic_design' name='basic_design' value='1'><label for='basic_design'>기본레이아웃 지정</label> <input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle>";
										}else{
											$Contents .= "<input type='checkbox' id='basic_design' name='basic_design' value='1'><label for='basic_design'>기본레이아웃 지정</label>  <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a>";
										}
							
										$Contents .= "
										</td>
									</tr>";
									//}

					$Contents .="</form>
							</table>
							<div  id='design_contents'  ".($tab_no == "02" ? " ":"style='display:none;'") ." >
							<table cellpadding=0 cellspacing=0width='100%'>
							<tr>
							<form name=info_input action='design.act.php' method='post' onsubmit='return SubmitX(this);' target='act'>
								<input type='hidden' name=design_act value='update'>
								<input type='hidden' name=pcode value='$pcode'>
								<input type='hidden' name=page_path value='$page_path'>
								<input type='hidden' name=page_name value='$page_name'>
								<input type='hidden' name=tab_no value='02'>
								<input type='hidden' name=mall_ix value='".$admininfo[mall_ix]."'>
								<td colspan=3>
									<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02'></td>
											<th class='box_03'></th>
										</tr>
										<tr>
											<th class='box_04'></th>
											<td class='box_05' style='padding:0px 0 0 0' >
												<TABLE height=20 cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
													<TR height=50>
														<TD  colspan=3 align=left style='padding:5px;line-height:140%'>
														<!--소스를 수정하신후에는 아래 수정내용 기입후 디자인 백업하기를 체크 하신다음 저장버튼을 눌러주시면, 추후에 수정내용을 참고하여 복원하실수 있습니다.-->
															".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."
														</TD>
													</TR>
												</TABLE>
											</td>
											<th class='box_06'></th>
										</tr>
										<tr>
											<th class='box_07'></th>
											<td class='box_08'></td>
											<th class='box_09'></th>
										</tr>
									</table><br>
								</td>
							</tr>
	
	
							<tr height=30 >
								<td colspan=3 style='padding:0 0 0 0;'>
									 <a href=\"javascript:open_window('./code_executor.php?pcode=$pcode&depth=$depth');\"><img src='../images/".$admininfo["language"]."/btn_quick_design.gif' border=0 align=absmiddle vspace=3></a>
								</td>
							</tr>
							</table>
							<table width='100%' border='0' cellspacing='0' cellpadding='0' class='list_table_box'>
							<tr>
								<td class='list_box_td' height='30'  style='padding:0px;'>
								<textarea style=\"width:97%; height:50px;margin:5px;\" wrap='off' class='tline' onfocus='CheckFocus(this)' ".($page_navi == "" ? "basic_message=true":"basic_message=false")." name='page_navi' >".($page_navi == "" ? "페이지 네비게이션정보를 입력해주세요":$page_navi)."</textarea>
								</td>
							</tr>
							<tr height=30 bgcolor=#efefef>
								<td class='list_box_td list_bg_gray' style='text-align:left;padding:0 0 0 10px;'>
									 <span >수정후 CTRL+S 를 누르면 저장됩니다</span>
								</td>
							</tr>
							<tr>
								<td class='list_box_td' bgcolor='#ffffff' height='300' width='100%' style='padding:0px;' >
								<div style='z-index:-1;position:absolute;width:100%;' id='parent_save_loading'>
			<div style='width:100%;height:300px;display:block;position:relative;z-index:10px;text-align:center;padding-top:150px;' id='save_loading'></div>
			</div>
								";
	
	$Contents .="
	<textarea onkeydown=\"textarea_useTab( this, event );\" style='height:300px;width:97%;margin:5px;' wrap='off' class='tline' id='page_contents' name='page_contents' >
	$thisfile
	</textarea>";
	
		
	$Contents .="
							<div style='display:none;' id='page_contents_convert' ></div>
								</td>
							</tr>
							<tr>
								<td class='list_box_td' bgcolor='#ffffff' height='30' style='padding:0px'>
								<textarea style='height:50px;width:97%;margin:5px;' wrap='off' onfocus='CheckFocus(this)' class='tline' basic_message=true name='page_change_memo' >".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</textarea> 
								 </td>
							</tr>
							</table>
							<table width='100%' border='0' cellspacing='0' cellpadding='0' >
							<tr>
								<td colspan=3 align=center style='padding:10px;'> ";
							if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
								$Contents .="<input type='checkbox' id='design_backup' name='design_backup' value='1'><label for='design_backup'>디자인 백업하기</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=image src='../images/".$admininfo["language"]."/b_save.gif' id='save_btn' border=0 align=absmiddle>";
							}else{
								$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../image/b_save.gif' border=0 align=absmiddle ></a>";
							}
							$Contents .="
								</td>
							</tr>
							</form>
	
							<tr>
								<td colspan=3 align=right style='padding:10px;' id='design_history_area'>
								".PrintEditPageHistory($page_path."/".$page_name)."
								</td>
							</tr>
							</table>
							</div>
							<div id='design_help' style='display:none;text-align:center;padding:30px 0 30px 0;height:700px;'>
							<!--script language='javascript' id='design_help_script'>viewMenual('몰스토리동영상메뉴얼_페이지상세디자인(090322)_config.xml', 800, 517)</script-->
								<div style='width:100%;' id='design_help_movie'></div>
							<div align=left style='padding:30px;'>
							".nl2br($page_help)."
							</div>
							</div>
						</div>
						<!-- my_movie end -->
					</div>
		    </td>
		</tr>
	</table>
	
	
	                  ";

	$category_str ="<div class=box id=img3  style='width:155px;height:375px;overflow:auto;'>".Category()."</div>";
	
	if($mmode == "innerview"){
		
		echo "<body><div id='design_history_area'>".PrintEditPageHistory($page_path."/".$page_name)."</div></body>\n";
		echo "<script>parent.document.getElementById('design_history_area').innerHTML = document.getElementById('design_history_area').innerHTML </script>";
		exit;
	}else{
		
		$P = new LayOut;
		$P->addScript = "$Script";
		//$P->OnloadFunction = "PageLoad();";//showSubMenuLayer('storeleft');
		$P->title = "";
		$P->strLeftMenu = design_menu("/admin",$category_str);
		$P->strContents = $Contents;
		$P->Navigation = "디자인관리 > 페이지 상세디자인";
		$P->title = "페이지 상세디자인";
		$P->PrintLayOut();
	}
	
	function displayEtcPage(){
		global $admininfo;
	
		$mdb = new Database;
	
		$sql = "select pcode, page_title from ".TBL_SHOP_DESIGN." where page_type ='A' and mall_ix ='".$admininfo[mall_ix]."'   ";
		$mdb->query($sql);
	
		$max = 10;
	
		if ($page == ''){
			$start = 0;
			$page  = 1;
		}else{
			$start = ($page - 1) * $max;
		}
	
		$mString = "<table class='bar01' style='width:100%;'>
			<tr>
				<td class='bar_01'></td>
				<td class='bar_02'>페이지코드</td>
				<td class='bar_02'>페이지제목</td>
				<td class='bar_03'></td>
			</tr>";
		for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);
	
		$mString .= "<tr height=30>
				<td ></td>
				<td >".$mdb->dt[pcode]."</td>
				<td >".$mdb->dt[page_title]."</td>
				<td ></td>
			</tr>";
		}
		$mString .= "<tr><td class='dot' colspan=5></td></tr>";
	
		$mString .= "</table>";
	
		return $mString;
	}
	
	function PrintEditPageHistory($page_name){
		global $admininfo, $page, $nset, $QUERY_STRING, $auth_delete_msg;
		$mdb = new Database;
	
		$sql = "select count(*) as total from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix ='".$admininfo[mall_ix]."'   ";
		$mdb->query($sql);
		$mdb->fetch();
		$total = $mdb->dt[total];
	
		$sql = "select * from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix ='".$admininfo[mall_ix]."'  order by regdate desc  ";
	
		$mdb->query($sql);
	
		$max = 10;
	
		if ($page == ''){
			$start = 0;
			$page  = 1;
		}else{
			$start = ($page - 1) * $max;
		}
	
	
		$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>";
		$mString = $mString."
					<form name=listform method=post action='design.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
					<input type='hidden' name='design_act' value='select_delete'>
					<input type='hidden' name='page_ix' value=''>
					<input type='hidden' name='mall_ix' value='".$admininfo[mall_ix]."'>
					<tr align=center bgcolor=#efefef height=25>
						<td class=s_td width='5%'><input type=checkbox class=nonborder id='all_fix' name='all_fix' onclick='fixAll(document.listform)'></td>
						<td class=m_td width='20%'>수정일자</td>
						<td class=m_td width='50%'>수정내용</td>
						<td class=e_td width='25%'>관리</td>
					</tr>";
		if ($mdb->total == 0){
			$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=4 align=center>페이지 수정목록이 존재 하지 않습니다.</td></tr>";
		}else{
	
			$mdb->query("select * from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' order by regdate desc   limit $start , $max");
	
			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
	
				//$no = $no + 1;
				if($mdb->dt[page_change_memo] == ""){
					$page_change_memo = "수정내용이 입력되지 않았습니다";
				}else{
					$page_change_memo = $mdb->dt[page_change_memo];
				}
	
				$mString = $mString."<tr height=45 bgcolor=#ffffff align=center>
				<td bgcolor='#ffffff'><input type=checkbox class=nonborder id='page_ix' name='page_ix[]' value='".$mdb->dt[page_ix]."'></td>
				<td bgcolor='#fbfbfb'>".$mdb->dt[regdate]."</td>
				<td align=left style='padding-left:20px;'>".$page_change_memo."</td>
				<td bgcolor='#fbfbfb' align=center nowrap>";
	
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
			
	
			$query_string = str_replace("nset=$nset&page=$page&","",$QUERY_STRING) ;
	
		}
		$mString .= "</table>";
		$mString .= "<table cellpadding=0 cellspacing=0 border=0 width=100%>
							<col width=20%>
							<col width=80%>
							<tr height=30>
							<td style='text-align:left;'>
							<a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a>
							</td>
							<td style='text-align:right;' align=right>".page_bar($total, $page, $max,"&".$query_string,"")."</td>
						  </tr>
						  </table>";
		
		//echo $query_string;
		$mString .= "</form>";
		return $mString;
	}


?>
