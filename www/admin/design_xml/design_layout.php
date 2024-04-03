<?php
	include("../class/layout.class");
//	include "../class/LayoutXml/LayoutXml.class";
	include("category.lib.php");
	include("stree.lib.php");
	

	
	define_syslog_variables();
	openlog("phplog", LOG_PID , LOG_LOCAL0);
	
	
	
// 	$db = new Database;
	
	
 	$page_path = getDesignTempletPath($pcode, $depth);
	
	syslog(LOG_INFO, "page_path : " . $page_path);
	$layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout2.xml";
	$layoutXml = new LayoutXml($layoutXmlPath);
	
	$result = $layoutXml->search("layouts"
			                    , array('mall_ix', 'pcode')
			                    , array($admininfo[mall_ix], 'basic'));
	//print_r($result);
// 	echo($admininfo[mall_ix]);
// 	echo("<br />");
// 	print_r($result);
	if(count($result->layouts) > 0){
		$page_name = $result->layouts[0]->contents;
		$page_title = $result->layouts[0]->page_title;
		$templet_name = $result->layouts[0]->templet_name;
		$page_path = $result->layouts[0]->page_path;
		$page_link = $result->layouts[0]->page_link;
		$page_desc = $result->layouts[0]->page_desc;
		$page_help = $result->layouts[0]->page_help;
		$page_type = $result->layouts[0]->page_type;
		$layout_act = "update";
	}
	
 	$templet_path = $admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];
	
	$templet_file_path = $_SERVER["DOCUMENT_ROOT"] . $templet_path."/".$page_path."/".$page_name;
	
	
	if (count($result) != 0){
		foreach ($result as $item) {
			$page_name = $item->contents;
			$contents_add = $item->contents_add;
			$templet_name = $item->templet_name;
			$page_link = $item->page_link;
			$page_desc = $item->page_desc;
			$page_help = $item->page_help;
			$page_type = $item->page_type;
			$page_navi = $item->page_navi;
			
			$templet_file_path = $_SERVER["DOCUMENT_ROOT"] . $templet_path."/".$page_path."/".$page_name;
			
			$layout->layout 		= $item->layout;
			$layout->contents	 	= $item->contents;
			$layout->contents_add 	= $item->contents_add;
			$layout->header1 		= $item->header1;
			$layout->header2 		= $item->header2;
			$layout->leftmenu 		= $item->leftmenu;
			$layout->contents 		= $item->contents;
// 			$layout->contents_add 	= $item->contents_add;
			$layout->rightmenu 		= $item->rightmenu;
			$layout->footer1 		= $item->footer1;
			$layout->footer2 		= $item->footer2;
			$layout->page_path 		= $item->page_path;
			$layout->caching 		= $item->caching;
			$layout->caching_time 	= $item->caching_time;
			
			//syslog(LOG_INFO, "layout : " . print_r($layout, true));
		}
	}
	
	$thisfile = load_template($layoutXmlPath);
	syslog(LOG_INFO, "thisfile 1 : " . $thisfile);
	
	syslog(LOG_INFO, "templet_file_path " . $templet_file_path);
	syslog(LOG_INFO, "templet_path " . $templet_path);
	
	
	$thisfile_convert = eregi_replace("{{MALLSTORY_TEMPLET_PATH}}",$templet_path, $thisfile);
	$thisfile_convert = eregi_replace("@_templet_path",$templet_path, $thisfile_convert);
	$thisfile_convert = str_replace("</textarea>","&lt;/textarea&gt;", $thisfile_convert);
	$thisfile_convert = str_replace("<textarea","&lt;textarea", $thisfile_convert);
	$thisfile_convert = str_replace("</form>","&lt;/form&gt;", $thisfile_convert);
	$thisfile_convert = str_replace("<form","&lt;form", $thisfile_convert);
	
	$thisfile = str_replace("</textarea>","&lt;/textarea&gt;", $thisfile);
	$thisfile = str_replace("<textarea","&lt;textarea", $thisfile);
	$thisfile = str_replace("</FORM>","&lt;/FORM&gt;", $thisfile);
	$thisfile = str_replace("<FORM","&lt;FORM", $thisfile);
	
	
	syslog(LOG_INFO, "thisfile : " . $thisfile);
	
	if($pcode != ""){
		$page_path_string = $admin_config[mall_use_templete]."/$page_path/".$page_name;
	}
	
	$Script = "
		<Script Language='JavaScript' src='../js/XMLHttp.js'></Script>
		<Script Language='JavaScript' src='design.js'></Script>
		<Script Language='JavaScript'>
		function SubmitX(frm){
		
			return true;
		}
		
		
		function CheckFocus(obj){
			if(obj.basci_message == 'true'){
				obj.value = '';
				obj.basci_message = 'false'
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
		
		
		function pageHistoryDelete(page_ix){
			if(confirm('페이지 수정내용과 백업 소스를 정말로 삭제하시겠습니까?')){
				//document.frames['act'].location.href='design.act.php?design_act=delete&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_name."'//kbk
				document.getElementById('act').src='design.act.php?design_act=delete&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_name."'
			}
		}
		
		function pageHistoryRecovery(page_ix){
			if(confirm('해당내용으로 복구하시겠습니까? 1차적으로 화면에만 복구되게 되며 완전한 복구를 원하실때는 화면 복구후 저장버튼을 눌러주시기 바랍니다. ')){
				//document.frames['act'].location.href='design.act.php?design_act=recovery&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_name."'//kbk
				document.getElementById('act').src='design.act.php?design_act=recovery&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_name."'
			}
		}
		
		
		function showTabContents(vid, tab_id){
			var area = new Array('design_structure','design_contents','design_help');
			var tab = new Array('tab_01','tab_02','tab_03');
		
			for(var i=0; i<area.length; ++i){
				if(area[i]==vid){
					document.getElementById(vid).style.display = '';
					if(window.addEventListener) document.getElementById(tab_id).setAttribute('class','on');
					else document.getElementById(tab_id).className = 'on';
				}else{
					document.getElementById(area[i]).style.display = 'none';
					if(window.addEventListener) document.getElementById(tab[i]).setAttribute('class','');
					else document.getElementById(tab[i]).className = '';
				}
			}
		
		
		
		}
		
		function setCategory(cname,cid,depth,category_display_type){
			if(category_display_type == 'F'){
				alert('분류정보입니다.');
			}else{
				document.location.href='design.php?pcode='+cid+'&depth='+depth+'&category_display_type='+category_display_type+'&SubID=SM114641Sub'
			}
		}
		
		function empty(){
		
		}
		</Script>";
// 	print_r($layout);
// 	exit;

	$Contents ="
	<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' style='vertical-align:top'>
		<tr>
		    <td align='left' colspan=3 > ".GetTitleNavigation("레이아웃 일괄관리 ", "디자인관리 > 레이아웃 일괄관리  ")."</td>
		</tr>
		<tr height=10><td colspan=3 align=right style='padding-bottom:10px;'></td></tr>
		<tr>
		    <td align='left' colspan=3  style='vertical-align:top'>
		   			 <div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>
								<table id='tab_01' class='on' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"showTabContents('design_structure','tab_01')\">전체 페이지 구성 설정하기</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_02' style='display:none;'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"showTabContents('design_contents','tab_02')\">페이지 내용 수정하기</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_03' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"showTabContents('design_help','tab_03')\">도움말</td>
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
							<table id='design_structure' border=0 style='width:100%;'>
							<tr>
								<td colspan=3>
									<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
										<tr>
											<th class='box_01'></th>
											<td class='box_02'></td>
											<th class='box_03'></th>
										</tr>
										<tr>
											<th class='box_04'></th>
											<td class='box_05' style='padding:10px'>
												<TABLE cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
													<TR>
														<TD width=100 align=center> <b style='color:red'>공통치환코드</b> </TD>
														<TD bgColor=#ffffff style='line-height:120%'>
														<b>상단1</b> : {header_top}
														<b>상단2</b> : {header_menu}
														<b>좌측</b> : {center_leftmenu}
														<b>컨텐츠</b> : {center_contents}
														<b>우측</b> : {center_rightmenu}<br>
														<b>하단1</b> : {footer_menu}
														<b>하단2</b> : {footer_desc}
	
														</TD>
													</TR>
													<TR>
														<TD  colspan=3 align=left style='padding:20px 0 0px 30px;line-height:120%'>
															<ul>
																<!--li> 아래 원하는 레이아웃을 각각의 위치에 해당되는 파일을 선택해주세요.</li>
																<li> 새로운 레이아웃을 만드실경우에는 위와 같이 치환코드를 삽입해주셔야 합니다.</li-->
																".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."
															</ul>
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
							<form name=layout_info action='design_layout.act.php' method='post' onsubmit='return SubmitX(this);' target=act >
							<input type='hidden' name=layout_act value='update'>
							<input type='hidden' name=mall_ix value='".$admininfo[mall_ix]."'>
	
							<tr>
								<td colspan=2 valign=top>
								<table cellpadding=0 cellspacing=0 border=0 bgcolor=#e2e2e2 style='border:3px solid #d8d8d8' width=100%>
								<col width=250>
								<col width=*>
								<tr height=10>
									<td colspan=2></td>
								</tr>
								<tr>
									<td width=250 height=406 valign=top style='padding:0 10px 10px 10px;'>
									<div id=TREE_BAR style=\"width:250px;height:400px;padding:5px;overflow:auto;background-color:#ffffff\" >
									".sTree()."
									</div>
	
									</td>
									<td valign=top style='overflow:auto;padding:0 10px 10px 10px;'>
									<table cellpadding=13 cellspacing=4 width=100% bgcolor=#ffffff >
						                		<tr bgcolor=#efefef>
						                			<td nowrap width=130><img src='../image/title_head.gif' align=absmiddle><b> 레이아웃 디자인</b></td>
						                			<td>".SelectLayoutFileList("layout",$DOCUMENT_ROOT.$templet_path."/layout",$layout->layout)."</td>
						                			<td align=center><img src='../images/".$admininfo["language"]."/btn_page_edit.gif' border=0 align=absmiddle onclick=\"PopSWindow('./design.mod.php?mmode=pop&mod=layout&page_name='+document.forms['layout_info'].layout.value,900,800,'sendsms')\" style='cursor:pointer;'></td>
						                		</tr>
						                		<tr bgcolor=#efefef>
						                			<td nowrap><img src='../image/title_head.gif' align=absmiddle> <b>상단1 디자인</b></td>
						                			<td>".SelectLayoutFileList("header1",$DOCUMENT_ROOT.$templet_path."/layout/header",$layout->header1)."</td>
						                			<td align=center><img src='../images/".$admininfo["language"]."/btn_page_edit.gif' border=0 align=absmiddle onclick=\"PopSWindow('./design.mod.php?mmode=pop&mod=layout/header&page_name='+document.forms['layout_info'].header1.value,850,800,'sendsms')\" style='cursor:pointer;'></td>
						                		</tr>
						                		<tr bgcolor=#efefef>
						                			<td><img src='../image/title_head.gif' align=absmiddle> <b>상단 2 디자인</b></td>
						                			<td>".SelectLayoutFileList("header2",$DOCUMENT_ROOT.$templet_path."/layout/header",$layout->header2)."</td>
						                			<td align=center><img src='../images/".$admininfo["language"]."/btn_page_edit.gif' border=0 align=absmiddle onclick=\"PopSWindow('./design.mod.php?mmode=pop&mod=layout/header&page_name='+document.forms['layout_info'].header2.value,850,800,'sendsms')\" style='cursor:pointer;'></td>
						                		</tr>
						                		<tr bgcolor=#efefef>
						                			<td><img src='../image/title_head.gif' align=absmiddle> <b>좌측메뉴 디자인</b></td>
						                			<td>".SelectLayoutFileList("leftmenu",$DOCUMENT_ROOT.$templet_path."/layout/leftmenu", $layout->leftmenu) . "</td>
						                			<td align=center><img src='../images/".$admininfo["language"]."/btn_page_edit.gif' border=0 align=absmiddle onclick=\"PopSWindow('./design.mod.php?mmode=pop&mod=layout/leftmenu&page_name='+document.forms['layout_info'].leftmenu.value,850,800,'sendsms')\" style='cursor:pointer;'></td>
						                		</tr>";

	
	
	/////////////// exit; 
	if($category_display_type == "B"){
					//$Contents .="			<tr><td style='font-weight:bold' nowrap><img src='../image/title_head.gif' align=absmiddle> 컨텐츠</td><td nowrap>".SelectLayoutFileList("contents",$DOCUMENT_ROOT.$templet_path."/".$page_path,$page_name)." <a href=\"javascript:PoPWindow('./addfile.pop.php?pcode=".$pcode."&page_path=$page_path',420,150,'fileadd_pop')\"><img src='../image/add_file.gif' border=0 align=absmiddle></a></td></tr>";
					$Contents .="			<tr bgcolor=#efefef><td style='font-weight:bold' nowrap><img src='../image/title_head.gif' align=absmiddle> 게시판 템플릿</td><td nowrap>".SelectDirList("templet_name",$_SERVER["DOCUMENT_ROOT"]."/bbs_templet",$templet_name)." <a href=\"javascript:PoPWindow('./addfile.pop.php?pcode=".$pcode."&page_path=$page_path',420,150,'fileadd_pop')\">템플릿 미리보기<!--img src='../image/add_file.gif' border=0 align=absmiddle--></a></td></tr>";
					}else{
						
					$Contents .="			<tr bgcolor=#efefef>
											<td style='font-weight:bold' nowrap><img src='../image/title_head.gif' align=absmiddle> 컨텐츠</td>
											<td nowrap colspan=2> <!--span class='small'> 컨텐츠 내용은 <b><u>페이지 상세 디자인</u></b>에서 선택해주세요 </span--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</td>
										</tr>";
					}
	
	
					$Contents .="
						                		<tr bgcolor=#efefef>
						                			<td><img src='../image/title_head.gif' align=absmiddle> <b>우측 디자인</b></td>
						                			<td>".SelectLayoutFileList("rightmenu",$DOCUMENT_ROOT.$templet_path."/layout/rightmenu",$layout->rightmenu)."</td>
						                			<td align=center><img src='../images/".$admininfo["language"]."/btn_page_edit.gif' border=0 align=absmiddle onclick=\"PopSWindow('./design.mod.php?mmode=pop&mod=layout/rightmenu&page_name='+document.forms['layout_info'].rightmenu.value,850,800,'sendsms')\" style='cursor:pointer;'></td>
						                		</tr>
						                		<tr bgcolor=#efefef>
						                			<td><img src='../image/title_head.gif' align=absmiddle> <b>하단1 디자인 </b></td>
						                			<td>".SelectLayoutFileList("footer1",$DOCUMENT_ROOT.$templet_path."/layout/footer",$layout->footer1)."</td>
						                			<td align=center><img src='../images/".$admininfo["language"]."/btn_page_edit.gif' border=0 align=absmiddle onclick=\"PopSWindow('./design.mod.php?mmode=pop&mod=layout/footer&page_name='+document.forms['layout_info'].footer1.value,850,800,'sendsms')\" style='cursor:pointer;'></td>
						                		</tr>
						                		<tr bgcolor=#efefef>
						                			<td><img src='../image/title_head.gif' align=absmiddle> <b>하단2 디자인</b></td>
						                			<td>".SelectLayoutFileList("footer2",$DOCUMENT_ROOT.$templet_path."/layout/footer",$layout->footer2)."</td>
						                			<td align=center><img src='../images/".$admininfo["language"]."/btn_page_edit.gif' border=0 align=absmiddle onclick=\"PopSWindow('./design.mod.php?mmode=pop&mod=layout/footer&page_name='+document.forms['layout_info'].footer2.value,850,800,'sendsms')\" style='cursor:pointer;'></td>
						                		</tr>
						                	</table>
									</td>
								</tr>
								</table>
	
						                </td>
						                <td colspan=2 align=left style='padding:0 10px 10px 0;' valign=top>
	
						                </td>
						        </tr>
						            <tr>
						                <td colspan=3 align=center style='padding:10px 10px 30px 10px;'>";
										if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
											$Contents .="<table>
						                	<tr>
						                		<td><input type='checkbox' id='basic_design' name='basic_design' value='1'><label for='basic_design'>기본레이아웃 지정</label></td>
						                		<td style='padding:0 0 0 20px'><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0></td>
						                	</tr>
						                	</table>";
										}else{
											$Contents .= "<input type='checkbox' id='basic_design' name='basic_design' value='1'><label for='basic_design'>기본레이아웃 지정</label>  <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a>";
										}
	
						                $Contents .="
						                </td>
						        </tr></form>
							</table>
	
							</form>
							<div id='design_help' style='display:none;'>".nl2br($page_help)."</div>
						</div>
						<!-- my_movie end -->
					</div>
		    </td>
		</tr>
	
	                  </table>
	
	
	                  ";

	$category_str ="<div class=box id=img3  style='width:155px;height:375px;overflow:auto;'>".Category()."</div>";
	
	$P = new LayOut;
	
	$P->addScript = "$Script";
	//$P->OnloadFunction = "document.write(document.getElementById('TREE_BAR').innerHTML);";//PageLoad();showSubMenuLayer('storeleft');
	$P->title = "";
	
	$P->strLeftMenu = design_menu("/admin",$category_str);
	
	$P->strContents = $Contents;
	$P->Navigation = "디자인관리 > 기타 디자인관리 > 레이아웃 일괄관리";
	$P->title = "레이아웃 일괄관리";
	$P->PrintLayOut();
	
	
	
	function SelectLayoutFileList($objname, $path, $select_file){
		global $DOCUMENT_ROOT, $mod, $SubID;
		if($path == ""){
			$path = $_SERVER["DOCUMENT_ROOT"]."/data/sample/templet/basic";
		}
	
		$mstring =  "<select name='$objname' style='font-size:12px;width:200px;'><!--onchange=\"document.location.href='design.php?SubID=$SubID&mod=$mod&page_name='+this.value\"-->";
		$mstring .= "<option value=''>파일을 선택해주세요</option>";
		if(FileList($path, 0, "FULL")){
			$mstring .= FileList($path, $select_file, 0, "FULL");
		}else{
			$mstring .= "<option>파일이 존재하지않습니다.</option>";
		}
		$mstring .=  "</select>";
	
		return $mstring;
	}
	
	
	function displayEtcPage(){
		global $admininfo, $layoutXml;
	
// 		$mdb = new Database;
	
// 		$sql = "select cid, cname from ".TBL_SHOP_LAYOUT_INFO."  ";
// 		$mdb->query($sql);
	
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
		
		foreach ($layoutXml->layouts as $layout) {
			$mString .= "<tr height=30>
					<td ></td>
					<td >".$layout->cid."</td>
					<td >".$layout->cname."</td>
					<td ></td>
				</tr>";
			
		}
		
// 		for($i=0;$i < $mdb->total;$i++){
// 			$mdb->fetch($i);
		
// 			$mString .= "<tr height=30>
// 					<td ></td>
// 					<td >".$mdb->dt[cid]."</td>
// 					<td >".$mdb->dt[cname]."</td>
// 					<td ></td>
// 				</tr>";
// 		}
		$mString .= "<tr><td class='dot' colspan=5></td></tr>";
	
		$mString .= "</table>";
	
		return $mString;
	}

	
	closelog();
