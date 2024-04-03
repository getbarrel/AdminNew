<?
include("../class/layout.class");


$db = new Database;

$db->query("select * from ".TBL_SHOP_DESIGN." where mall_ix = '".$admininfo[mall_ix]."' and pcode ='$pcode' ");
$db->fetch();

if($db->total){	
	$page_name = $db->dt[contents];
	$contents_add = $db->dt[contents_add];
	$page_title = $db->dt[page_title];
	$page_link = $db->dt[page_link];
	$page_desc = $db->dt[page_desc];
	$page_keyword = $db->dt[page_keyword];
	$page_type = $db->dt[page_type];	
	$layout_act = "update";
}else{
//	if($page_name == ""){
//		$page_name = "ms_index.htm";
//	}
	$page_type = "A";
	$layout_act = "insert";
}
$templet_path = $admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];
$templet_file_path = $templet_path."/_".$pcode."/".$page_name;
$thisfile = load_template($DOCUMENT_ROOT.$templet_file_path);
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

if($pcode != ""){
	$page_path = $admin_config[mall_use_templete]."/_$pcode".$page_name;
}

$Script = "
<Script Language='JavaScript' src='../js/XMLHttp.js'></Script>
<Script Language='JavaScript' src='design.js'></Script>
<Script Language='JavaScript'>
function SubmitX(frm){
	//alert(iView.document.body.innerHTML);
	//frm.content.value = iView.document.body.innerHTML;
	//document.write (frm.content.value);
	//document.forms['info_input'].page_contents.value = document.forms['info_input'].page_contents.value.replace('&lt;/textarea&gt;','</textarea>');
	//document.forms['info_input'].page_contents.value = document.forms['info_input'].page_contents.value.replace('&lt;textarea','<textarea');
	if(frm.page_add_script.basci_message == 'true'){
		frm.page_add_script.value = '';
	}
	
	if(frm.page_body_property.basci_message == 'true'){
		frm.page_add_script.value = '';
	}
	
	if(frm.page_change_memo.basci_message == 'true'){
		frm.page_change_memo.value = '';
	}
	
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
		document.frames['act'].location.href='design.act.php?design_act=delete&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_name."'
	}
}

function pageHistoryRecovery(page_ix){
	if(confirm('해당내용으로 복구하시겠습니까? 1차적으로 화면에만 복구되게 되며 완전한 복구를 원하실때는 화면 복구후 저장버튼을 눌러주시기 바랍니다. ')){		
		document.frames['act'].location.href='design.act.php?design_act=recovery&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_name."'
	}
}


function showTabContents(vid, tab_id){
	var area = new Array('design_structure','design_contents','design_help');
	var tab = new Array('tab_01','tab_02','tab_03');
	
	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';			
			document.getElementById(tab_id).className = 'on';
		}else{			
			document.getElementById(area[i]).style.display = 'none';
			document.getElementById(tab[i]).className = '';
		}
	}
	
	
		
}


</Script>";



$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' style='vertical-align:top'>
	<tr height=30>
	    <td align='left' colspan=3 > ".GetTitleNavigation("페이지추가하기", "디자인관리 > 페이지 상세 디자인 ")."</td>
	</tr>
	<tr height=30><td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 15;'><b> 선택된 페이지 :<a href=\"JavaScript:pview(document.forms['info_input'].page_contents.value, 'aaa', 'bbb');\">$page_path</a></b></div>")."</td></tr>	
	<tr>
	    <td align='left' colspan=3  style='vertical-align:top'>
	   			 <div class='tab'>
					<table class='sadow_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' class='on' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('design_structure','tab_01')\">페이지 구성 설정하기</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02'>
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
						<table id='design_structure' border=0>
						<tr>
							<td colspan=3>
								<table class='box_shadow' style='width:100%;height:60px' >
									<tr>
										<th class='box_01'></th>
										<td class='box_02'></td>
										<th class='box_03'></th>
									</tr>
									<tr>
										<th class='box_04'></th>
										<td class='box_05' style='padding:10 0 0 0'>	
											<TABLE height=20 cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>		
												<TR>
													<TD width=100 align=center> <b style='color:red'>치환코드</b> </TD>
													<TD bgColor=#ffffff>
													<b>상단1</b> : {header.top} 
													<b>상단2</b> : {header.menu}
													<b>좌측</b> : {center.leftmenu}
													<b>컨텐츠</b> : {center.contents}
													<b>우측</b> : {center.rightmenu}
													<b>하단1</b> : {footer.menu}
													<b>하단2</b> : {footer.desc}
													
													</TD>
												</TR>
												<TR height=50>
													<TD  colspan=3 align=left style='padding:20 0 10 30;line-height:120%'> 
													<li> 아래 원하는 레이아웃을 각각의 위치에 해당되는 파일을 선택해주세요 
													<li> 새로운 레이아웃을 만드실경우에는 위와 같이 치환코드를 사입해주셔야 합니다.
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
						<form name=layout_info action='design.act.php' method='post' onsubmit='return SubmitX(this);' target='act'>
						<input type='hidden' name=layout_act value='$layout_act'>
						<input type='hidden' name=page_type value='$page_type'>						
						<!--input type='hidden' name=pcode value='$pcode'-->
						<input type='hidden' name=mall_ix value='".$admininfo[mall_ix]."'>			
						<tr>
							<td colspan=2>
								<table id='design_add' style='width:100%'>
								<tr>
									<td><img src='../image/ico_dot.gif'> 페이지 코드 :</td>
									<td>
									<input type='text' name=pcode value='$pcode'> <span class=small style='color:gray'>추가하시고자 하는 페이지의 관리코드를 입력해주세요 <b>예) add_0001</b></span>
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
									<textarea  onkeydown=\"textarea_useTab( this, event );\"  wrap='off'  style='height:100px;' class='tline'  name='page_keyword' >$page_keyword</textarea>
									</td>
								</tr>								
								</table>
							</td>
							<td valign=top>
								".displayEtcPage()."
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<table cellpadding=3>
								<tr align=center>
									<td><b style='color:gray'>layout.htm</b></td>
									<td><b style='color:gray'>layout2.htm</b></td>
									<td><b style='color:gray'>layout3.htm</b></td>
								</tr>
								<tr>
									<td>			
										<table cellpadding=5 cellspacing=1 border=0 bgcolor=silver width=200 height=200 align=left style='border:5px solid #efefef;'>
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{header.top}'>상단 영역1</b></td></tr>
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{header.menu}'>상단 영역2</b></td></tr>
											<tr bgcolor=#ffffff height=130 align=center>
												<td width=50><b title=' {center.leftmenu}'>좌측 영역</b></td>
												<td width=150><b title='{center.contents}'>컨텐츠 영역</b></td>
												<td width=50><b title='{center.rightmenu}'>우측 영역</b></td></tr>
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{footer.menu}'>하단 영역1</b></td></tr>
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{footer.desc}'>하단 영역2</b></td></tr>
										</table>
									</td>
									<td>
										<table cellpadding=5 cellspacing=1 border=0 bgcolor=silver width=200 height=200 align=left  style='border:5px solid #efefef;'>
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{header.top}'>상단 영역1</b></td></tr>
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{header.menu}'>상단 영역2</b></td></tr>
											<tr bgcolor=#ffffff height=130 align=center>						
												<td width=200 colspan=2><b title='{center.contents}'>컨텐츠 영역</b></td>
												<td width=50><b title='{center.rightmenu}'>우측 영역</b></td>
											</tr>
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{footer.menu}'>하단 영역1</b></td></tr>
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{footer.desc}'>하단 영역2</b></td></tr>
										</table>
									</td>
									<td>
										<table cellpadding=5 cellspacing=1 border=0 bgcolor=silver width=200 height=200 style='border:5px solid #efefef;'>
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{header.top}'>상단 영역1</b></td></tr>
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{header.menu}'>상단 영역2</b></td></tr>
											<tr bgcolor=#ffffff height=130 align=center>						
												<td width=200 colspan=3><b title='{center.contents}'>컨텐츠 영역</b></td>							
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{footer.menu}'>하단 영역1</b></td></tr>
											<tr bgcolor=#ffffff align=center><td colspan=3><b title='{footer.desc}'>하단 영역2</b></td></tr>
										</table>
									</td>
								</tr>					
								</table>
					                </td>
					                <td colspan=2 align=left style='padding:10px;'>
					                	<table cellpadding=5>
					                		<tr><td nowrap><img src='../image/icon_dot3.gif' align=absmiddle> 레이아웃</td><td>".SelectFileList("layout",$DOCUMENT_ROOT.$templet_path."/layout",$db->dt[layout])."</td></tr>
					                		<tr><td nowrap><img src='../image/icon_dot3.gif' align=absmiddle> 상단1</td><td>".SelectFileList("header1",$DOCUMENT_ROOT.$templet_path."/header",$db->dt[header1])."</td></tr>
					                		<tr><td><img src='../image/icon_dot3.gif' align=absmiddle> 상단 2</td><td>".SelectFileList("header2",$DOCUMENT_ROOT.$templet_path."/header",$db->dt[header2])."</td></tr>
					                		<tr><td><img src='../image/icon_dot3.gif' align=absmiddle> 좌측</td><td>".SelectFileList("leftmenu",$DOCUMENT_ROOT.$templet_path."/leftmenu",$db->dt[leftmenu])."</td></tr>
					                		<tr><td style='font-weight:bold' nowrap><img src='../image/icon_dot3.gif' align=absmiddle> 컨텐츠</td><td nowrap>".SelectFileList("contents",$DOCUMENT_ROOT.$templet_path."/_".$pcode,$page_name)." <a href=\"javascript:PoPWindow('./addfile.pop.php?mod=_".$pcode."',420,150,'fileadd_pop')\"><img src='../image/add_file.gif' border=0 align=absmiddle></a></td></tr>
					                		<tr><td style='font-weight:bold' nowrap><img src='../image/icon_dot3.gif' align=absmiddle> 컨텐츠</td><td nowrap>".SelectFileList("contents_add",$DOCUMENT_ROOT.$templet_path."/_".$pcode,$contents_add)." <a href=\"javascript:PoPWindow('./addfile.pop.php?mod=_".$pcode."',420,150,'fileadd_pop')\"><img src='../image/add_file.gif' border=0 align=absmiddle></a></td></tr>
					                		<tr><td><img src='../image/icon_dot3.gif' align=absmiddle> 우측</td><td>".SelectFileList("rightmenu",$DOCUMENT_ROOT.$templet_path."/rightmenu",$db->dt[rightmenu])."</td></tr>
					                		<tr><td><img src='../image/icon_dot3.gif' align=absmiddle> 하단1</td><td>".SelectFileList("footer1",$DOCUMENT_ROOT.$templet_path."/footer",$db->dt[footer1])."</td></tr>
					                		<tr><td><img src='../image/icon_dot3.gif' align=absmiddle> 하단2</td><td>".SelectFileList("footer2",$DOCUMENT_ROOT.$templet_path."/footer",$db->dt[footer2])."</td></tr>
					                	</table>
					                </td>
					        </tr>                    
					            <tr>
					                <td colspan=3 align=center style='padding:10 10 30 10;'><input type=image src='../image/b_save.gif' border=0></td>
					        </tr></form>
						</table>
						
						<table id='design_contents' style='display:none;'>
						<tr>
						<form name=info_input action='design.act.php' method='post' onsubmit='return SubmitX(this);' target='act'>
							<input type='hidden' name=design_act value='update'>
							<input type='hidden' name=pcode value='$pcode'>							
							<input type='hidden' name=mall_ix value='".$admininfo[mall_ix]."'>
							<td colspan=3>
								<table class='box_shadow' style='width:100%;height:60px' >
									<tr>
										<th class='box_01'></th>
										<td class='box_02'></td>
										<th class='box_03'></th>
									</tr>
									<tr>
										<th class='box_04'></th>
										<td class='box_05' style='padding:10 0 0 0'>	
											<TABLE height=20 cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>									
												<TR height=50>
													<TD  colspan=3 align=left style='padding:0 0 10 30;line-height:150%'> 
													<!--li> 해당페이지에 대한 페이지별 스크립트, body property 및 해당페이지의 소스를 수정하실수 있습니다.-->
													<li> 소스를 수정하신후에는 아래 수정내용 기입후 저장버튼을 눌러주시면, 추후에 수정내용을 참고하여 복원하실수 있습니다.
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
						
					        <!--tr> 
						<td bgcolor='#ffffff' height='100' colspan='4' style='padding:10 0 0 0' >
							
<textarea style=\"background-image: url('../image/text_bg1.gif'); height:140;\"   wrap='off' class='tline' onfocus='CheckFocus(this)' basci_message=true name='page_addscript' >
각페이지에 추가될 스크립트를 입력해주세요
예) 
<script lanaugae='javascript' src='{templet.src}/script_name.js'></script>
<script lanaugae='javascript'>
function fname(){ // 함수를 정의 합니다.
//함수 내용을 입력합니다.
}
</scfript>
</textarea>
							</td>
						</tr-->
						<!--tr> 
							<td bgcolor='#ffffff' height='30' colspan='4' style='padding:10 0 0 0'><textarea style=\"background-image: url('../image/text_bg2.gif'); height:50;\" wrap='off' onfocus='CheckFocus(this)' basci_message=true name='page_body' >BODY 태그에 입력될 이벤트 및 property를 정의해주세요 onload='functionname();' oncontextmenu='return false'</textarea></td>
						</tr-->
						<tr> 
							<td bgcolor='#ffffff' height='400' width='100%' colspan='4'><br>
<textarea onkeydown=\"textarea_useTab( this, event );\" style='height:90%;' wrap='off' id='page_contents_convert' name='page_contents' >
$thisfile
</textarea>
						<div style='display:none;' id='page_contents_convert' ></div>
							</td>
						</tr>
						<tr>
							<td bgcolor='#ffffff' height='30' colspan='4' style='padding:10 0 0 0'><textarea style='height:50;' wrap='off' onfocus='CheckFocus(this)' basci_message=true name='page_change_memo' >소스 수정내용을 입력해주세요. 수정된 내용을 확인하시고 추후에 복원하실수 있습니다.</textarea></td>
						</tr>
						<tr> 
							<td bgcolor='D0D0D0' height='1' colspan='4'></td>
						</tr>
						<tr><td colspan=3 align=right style='padding:10px;'><input type=image src='../image/b_save.gif' border=0></td></tr>
						<tr>
							<td colspan=3 align=right style='padding:10px;'>
							".PrintEditPageHistory($page_name)."
							</td>
						</tr>
						</table>
						</form>
						<div id='design_help' style='display:none;'>도움말</div>
					</div>
					<!-- my_movie end -->
				</div>		
	    </td>
	</tr>
        
                  </table>
		
		
                  ";
                  
$P = new LayOut;
$P->addScript = "$Script";
//$P->OnloadFunction = "PageLoad();";//showSubMenuLayer('storeleft');
$P->title = "";
$P->Navigation = "HOME > 디자인관리 > 페이지 추가하기";
$P->strLeftMenu = design_menu();
$P->strContents = $Contents;
$P->PrintLayOut();

function displayEtcPage(){
	global $admininfo, $PHP_SELF;
	
	$mdb = new Database;
	
	$sql = "select pcode, page_title, regdate from ".TBL_SHOP_DESIGN." where page_type ='A' and mall_ix ='".$admininfo[mall_ix]."'   ";
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
			<td class='bar_02'>등록일자</td>
			<td class='bar_03'></td>
		</tr>";
	for($i=0;$i < $mdb->total;$i++){
	$mdb->fetch($i);
	
	$mString .= "<tr height=30 align=center>
			<td ></td>
			<td >".$mdb->dt[pcode]."</td>
			<td ><a href='$PHP_SELF?SubID=$SubID&pcode=".$mdb->dt[pcode]."'>".$mdb->dt[page_title]."</a></td>
			<td >".$mdb->dt[regdate]."</td>
			<td ></td>
		</tr>";		
	}
	$mString .= "<tr><td class='dot' colspan=5></td></tr>";
	
	$mString .= "</table>";
	
	return $mString;
}
								
function PrintEditPageHistory($page_name){
	global $admininfo;
	$mdb = new Database;
	
	$sql = "select * from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix ='".$admininfo[mall_ix]."'  order by regdate desc  ";
	$mdb->query($sql);
	
	$max = 10;
	
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
		
	
	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver>";	
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=s_td width='25%'>수정일자</td><td class=m_td width='50%'>수정내용</td><td class=e_td width='25%'>관리</td></tr>";	
	if ($mdb->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=9 align=center>페이지 수정목록이 존재 하지 않습니다.</td></tr>";
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
			<td bgcolor='#efefef'>".$mdb->dt[regdate]."</td>
			<td align=left style='padding-left:20px;'>".$page_change_memo."</td>
			<td bgcolor='#efefef' align=center>	
				<a href=JavaScript:pageHistoryRecovery('".$mdb->dt[page_ix]."')>[복구하기]</a>
				[소스보기]
				<a href=JavaScript:pageHistoryDelete('".$mdb->dt[page_ix]."')>[삭제하기]</a>
				<!--a href=JavaScript:pageHistoryDelete('".$mdb->dt[page_ix]."')><img  src='../image/si_remove.gif' border=0></a-->
			</td>
			</tr>
			<tr height=1><td colspan=6 background='../image/dot.gif'></td></tr>
			";
		}
		
	}
	
	
	$mString = $mString."<tr height=50 bgcolor=#ffffff><td colspan=6 align=left>".page_bar($total, $page, $max,  "&max=$max")."</td></tr>
					</table>";
	
	return $mString;
}

function FileList ( $path , $select_file="", $maxdepth = -1 , $mode = "FULL" , $d = 0 ){

   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }      
   $dirlist = array () ;
   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
   if ( $handle = @opendir ( $path ) )
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
               			if($select_file == $only_file){
               				$mstring .=  "<option value='".$only_file ."' selected>".$only_file ."</option>";
               			}else{
               				$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               			}
               		}
               		
               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
                   $mstring .= FileList ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;                  
                  $mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
               }
           }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $mstring ) ;
}

function SelectFileList($objname, $path, $select_file){
	global $DOCUMENT_ROOT, $mod, $SubID;
	if($path == ""){
		$path = $_SERVER["DOCUMENT_ROOT"]."/data/sample/templet/basic";	
	}
	
	$mstring =  "<select name='$objname' ><!--onchange=\"document.location.href='design.php?SubID=$SubID&mod=$mod&page_name='+this.value\"-->";
	$mstring .= "<option value=''>파일을 선택해주세요</option>";
	if(FileList($path, 0, "FULL")){
		$mstring .= FileList($path, $select_file, 0, "FULL");
	}else{
		$mstring .= "<option>파일이 존재하지않습니다.</option>";
	}
	$mstring .=  "<select>";
	
	return $mstring;
}



?>
