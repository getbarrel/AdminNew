<?
include("../class/layout.class");
include("category.lib.php");



$templet_path = $admin_config[mall_data_root]."/module/".$module_type."_templet/";
$templet_file_path = $templet_path."/".$page_name;


if($design_act == "recovery"){
	$db = new Database;
	$db->query("select page_contents from ".TBL_SHOP_PAGEINFO." where page_name ='module/".$module_type."_templet/".$page_name."' and mall_ix='$mall_ix' and page_ix = '$page_ix' ");
	$db->fetch();
	$thisfile = $db->dt[page_contents];
	$thisfile_convert = eregi_replace("{{MALLSTORY_TEMPLET_PATH}}",$templet_path, $thisfile);
	$thisfile_convert = eregi_replace("@_templet_path",$templet_path, $thisfile_convert);
	$thisfile_convert = str_replace("</textarea>","&lt;/textarea&gt;", $thisfile_convert);
	$thisfile_convert = str_replace("<textarea","&lt;textarea", $thisfile_convert);
	$thisfile_convert = str_replace("</form>","&lt;/form&gt;", $thisfile_convert);
	$thisfile_convert = str_replace("<form","&lt;form", $thisfile_convert);
	$thisfile_convert = str_replace("<FORM","&lt;form", $thisfile_convert);
	$thisfile_convert = str_replace("</FORM>","&lt;/form&gt;", $thisfile_convert);
}else{
	$thisfile = load_template($DOCUMENT_ROOT.$templet_file_path);
	$thisfile_convert = eregi_replace("{{MALLSTORY_TEMPLET_PATH}}",$templet_path, $thisfile);
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
		with (document.getElementById('save_loading').style){
			
			width = '76%';
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
	if(confirm('페이지 수정내용과 백업 소스를 정말로 삭제하시겠습니까?')){		
		document.frames['act'].location.href='design.act.php?design_act=delete&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=module/".$module_type."_templet/".$page_name."'
	}
}

function pageHistoryRecovery(page_ix){
	if(confirm('해당내용으로 복구하시겠습니까?\\n1차적으로 화면에만 복구되게 되며 완전한 복구를 원하실때는 화면 복구후 저장버튼을 눌러주시기 바랍니다. ')){				
		document.frames['act'].location.href='design.act.php?design_act=recovery&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=module/".$module_type."_templet/".$page_name."'
		//document.location.href='design.mod.php?SubID=$SubID&mod=$mod&design_act=recovery&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=".$page_name."'
	}
}


function setCategory(cname,cid,depth,category_display_type){
	if(category_display_type == 'F'){
		alert('분류정보입니다.');
	}else{
		document.location.href='design.php?pcode='+cid+'&depth='+depth+'&SubID=SM114641Sub'
	}
}


function ctrlSave() {
	if(event.ctrlKey == true && event.keyCode == 83 ){
		var pc_obj = document.getElementById('page_contents');		
		pc_obj.readOnly = true;
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
document.onkeydown = ctrlSave;



</Script>";


$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' >
<form name=info_input action='design_module.act.php' method='post' onsubmit='return SubmitX(this);' target=act>
<input type='hidden' name=act value='module_update'>
<input type='hidden' name=module_type value='$module_type'>
<input type='hidden' name=page_name value='$page_name'>
<input type='hidden' name=pcode value='$pcode'>
<input type='hidden' name=mall_ix value='".$admininfo[mall_ix]."'>
	<tr height=40>
	    <td align='left' colspan=6 > ".GetTitleNavigation("디자인모듈수정", "디자인관리 > 디자인모듈수정 ")."</td>
	</tr>
	<tr height=30><td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><b>선택된 타이틀</b></div>")."</td></tr>	
	<tr height=70>
		<td colspan=3 align=right style='padding-bottom:10px;'>
			".displayDesignModule("title", array("title"=>"회원가입", "navigation"=>"홈 > 회원가입"), $page_name)."
		</td>
	</tr>	
	<tr height=40>
		<td colspan=3 align=center style='padding-bottom:10px;'>
			<b>displayDesignModule(\"title\", array(\"title\"=>\"회원가입\", \"navigation\"=>\"홈 > 회원가입\"), \"$page_name\")</b>
		</td>
	</tr>	
	<tr height=40>
		<td>".SelectFileList2($DOCUMENT_ROOT.$templet_path)." <a href=\"javascript:PoPWindow('./addfile.pop.php?act=addModule&page_path=title',420,170,'fileadd_pop')\"><img src='../images/".$admininfo["language"]."/btn_add_file.gif' border=0 align=absmiddle></a>  <!--a href=\"javascript:open_window('./code_executor.php');\"><img src='../image/btn_quick_design.gif' border=0 align=absmiddle vspace=3></a--> </td>
		<td colspan=2 align=right ><a onclick=\"return ToolBarCmd('Undo');\"><img src='../images/".$admininfo["language"]."/btn_undo.gif' border=0></a> <a onclick=\"return ToolBarCmd('Redo');\"><img src='../images/".$admininfo["language"]."/btn_redo.gif' border=0></a>
		</td>
	</tr>
	</table>
	<table width='100%' border='0' cellspacing='0' cellpadding='0' class='list_table_box'>
	<tr height=30 >
		<td class='list_box_td list_bg_gray' style='padding:0 0 0 10px;'>
			 <span class='small' >수정후 CTRL+S 를 누르면 저장됩니다</span>
		</td>
	</tr>
	<tr> 
		<td class='list_box_td'  height='100%' width='100%'>
		<div style='width:100%;z-index:2;position:absolute;'>
		<div style='width:97%;height:300px;display:block;position:relative;z-index:10px;text-align:center;padding-top:150px;' id='save_loading'></div>
		</div>
<textarea onkeydown=\"textarea_useTab( this, event );\" style='overflow:auto;width:97%;height:300px;margin:10px;' wrap='off' name='page_contents' id='page_contents' >
$thisfile
</textarea>
		<div style='display:none;' id='page_contents_convert' ></div>
		</td>
	</tr>
	</table>
	<table width='100%' border='0' cellspacing='0' cellpadding='0' >
	<tr> 
		<td  height='30' style='text-align:center;padding:10px 0 0 0'>
		<textarea style='overflow:auto;width:98%;height:50px;margin:0px;' wrap='off' onfocus='CheckChangeMemo()' name='page_change_memo' >소스 수정내용을 입력해주세요</textarea>
		</td>
	</tr>	
	<tr><td colspan=3 align=center style='padding:10px;'>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Contents .="<input type='checkbox' id='design_backup' name='design_backup' value='1'><label for='design_backup'>디자인 백업하기</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=image src='../images/".$admininfo["language"]."/b_save.gif' id='save_btn' border=0 align=absmiddle>";
		}else{
		$Contents .= "<input type='checkbox' id='design_backup' name='design_backup' value='1'><label for='design_backup'>디자인 백업하기</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a>";
		}
$Contents .="
		</td></tr>
	</form>
	<tr><td colspan=3 align=center style='padding:10px;' id='design_history_area'>".PrintEditPageHistory("module/".$module_type."_templet/".$page_name)."</td></tr>
</table> ";


$category_str ="<div class=box id=img3  style='width:155px;height:375px;overflow:auto;'>".Category()."</div>";

if($mmode == "innerview"){
	echo "<body><div id='design_history_area'>".PrintEditPageHistory("module/".$module_type."_templet/".$page_name)."</div></body>\n";
	echo "<script>parent.document.getElementById('design_history_area').innerHTML = document.getElementById('design_history_area').innerHTML </script>";
	exit;
}else{           
	$P = new LayOut;
	$P->addScript = "$Script";
	//$P->OnloadFunction = "PageLoad();";//showSubMenuLayer('storeleft');
	$P->title = "";
	$P->Navigation = "디자인관리 > 기타 디자인관리 > 디자인모듈수정";
	$P->title = "디자인모듈수정";
	$P->strLeftMenu = design_menu("/admin",$category_str); 
	$P->strContents = $Contents;
	$P->PrintLayOut();
}

function PrintEditPageHistory($page_name){
	global $admininfo, $page, $nset, $QUERY_STRING;
	global $auth_update_msg, $auth_delete_msg;
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
	$mString .= "<form name=listform method=post action='design.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
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
		$mString .= "<tr bgcolor=#ffffff height=50><td colspan=4 align=center>모듈 수정목록이 존재 하지 않습니다.</td></tr>
					</table>";
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
			<td class='list_box_td'><input type=checkbox class=nonborder id='page_ix' name='page_ix[]' value='".$mdb->dt[page_ix]."'></td>
			<td class='list_box_td list_bg_gray'>".$mdb->dt[regdate]."</td>
			<td class='list_box_td' style='padding-left:20px;'>".$page_change_memo."</td>
			<td class='list_box_td list_bg_gray' align=center nowrap>";

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

			$mString .= "</td>
			</tr>
			";
		}
		$mString .= "</table>";
		$query_string = str_replace("nset=$nset&page=$page&","",$QUERY_STRING) ;
		
		$mString .= "<table cellpadding=0 cellspacing=0 width=100%>
							<tr bgcolor=#ffffff height=40>
								<td colspan=4 align=left>
									<a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../images/".$admininfo["language"]."/bt_all_del.gif' border=0 align=absmiddle ></a>
								</td>
								<td colspan=4 align=left>".page_bar($total, $page, $max,"&".$query_string,"")."</td>
							</tr>
						</table>";
	}
	
	
	//echo $query_string;
	$mString .= "</form>";
	
	return $mString;
}




function FileList2 ( $path , $maxdepth = -1 , $mode = "FULL" , $d = 0 ){
global $page_name;
   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }      
   $dirlist = array () ;
   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
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
	global $DOCUMENT_ROOT, $module_type, $SubID;
	if($path == ""){
		$path = $_SERVER["DOCUMENT_ROOT"].".$admin_config[mall_data_root]/module/".$module_type."_templet/";	
	}
	
	$mstring =  "<select name='page_name' onchange=\"document.location.href='design_module.php?SubID=$SubID&module_type=$module_type&page_name='+this.value\">";
	if(FileList2($path, 0, "FULL")){
		$mstring .= FileList2($path, 0, "FULL");
	}else{
		$mstring .= "<option>파일이 존재하지않습니다.</option>";
	}
	$mstring .=  "<select>";
	
	return $mstring;
}



?>