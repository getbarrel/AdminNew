<?
include("../class/layout.class");
include("category.lib.php");

if($page_name == ""){
	$page_name = "bbs_list.htm";
}

//$templet_path = $admin_config[mall_data_root]."/bbs_templet/".$templet;
$templet_path = "/bbs_templet/".$templet;
$templet_file_path = $templet_path."/".$mod."/".$page_name;


if($design_act == "recovery"){
	$db = new Database;
	$db->query("select page_contents from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix='$mall_ix' and page_ix = '$page_ix' ");
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
		document.frames['act'].location.href='design.act.php?design_act=delete&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=bbs_templet/".$templet."/".$page_name."'
	}
}

function pageHistoryRecovery(page_ix){
	if(confirm('해당내용으로 복구하시겠습니까?\\n1차적으로 화면에만 복구되게 되며 완전한 복구를 원하실때는 화면 복구후 저장버튼을 눌러주시기 바랍니다. ')){
		document.frames['act'].location.href='design.act.php?design_act=recovery&page_ix='+page_ix+'&mall_ix=".$admininfo[mall_ix]."&page_name=bbs_templet/".$templet."/".$page_name."'
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

//document.onkeypress = ctrlSave;
document.onkeydown = ctrlSave;



</Script>";




$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' >
<form name=info_input action='design.act.php' method='post' onsubmit='return SubmitX(this);' target=act>
<input type='hidden' name=design_act value='bbs_update'>
<input type='hidden' name=templet value='$templet'>
<input type='hidden' name=page_name value='$page_name'>
<input type='hidden' name=pcode value='$pcode'>
<input type='hidden' name=mall_ix value='".$admininfo[mall_ix]."'>
	<tr height=40>
	    <td align='left' colspan=6 > ".GetTitleNavigation("게시판 상세디자인", "디자인관리 > 게시판 상세디자인 ")."</td>
	</tr>
	<tr height=40><td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><b> 선택된 페이지 : <a href=\"JavaScript:pview(document.getElementById('page_contents').value, 'aaa', 'bbb');\">bbs_templet/$templet/".$page_name."</a></b></div>")."</td></tr>
	<tr height=100>
		<td colspan=3>
			<table class='box_shadow' style='width:100%;height:60px' >
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05' style='padding:0 0 0 0'>
						<TABLE height=20 cellSpacing=0 cellPadding=0 style='width:100%;' align=center border=0>
							<TR height=50>
								<TD  colspan=3 align=left style='padding:5 0 5 10;line-height:120%'>
								<li> 페이지 추가시에는 해당페이지에서 사용되는 <b style='color:red;'>치환변수</b>를 확인하여 입력해주세요
								<li> 소스를 수정하신후에는 아래 수정내용 기입후 저장버튼을 눌러주시면, 추후에 수정내용을 참고하여 복원하실수 있습니다.
								<li> 소스 수정시 undo 와 redo 기능을 이용해 수정된 정보를 되돌릴수 있다.
								<li> 좌측메뉴 상단의 메뉴 숨기기 버튼을 클릭해서 보다 넓게 코딩 작업을 할수 있다.
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
	<tr height=40>
		<td>".SelectFileList2($DOCUMENT_ROOT.$templet_path)." <!--a href=\"javascript:PoPWindow('./addfile.pop.php?pcode=$mod&page_path=$mod',420,170,'fileadd_pop')\"><img src='../images/btn_add_file.gif' border=0 align=absmiddle></a--> <a href=\"javascript:open_window('./code_executor.php');\" align=absmiddle><img src='../images/".$admininfo["language"]."/btn_quick_design.gif' border=0 align=absmiddle vspace=3></a> </td>
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
		<td bgcolor='#ffffff' height='100%' width='100%' >
		<div style='width:100%;z-index:2;position:absolute;'>
		<div style='width:75%;height:170px;display:block;position:relative;z-index:10;text-align:center;padding-top:150px;border:0px solid red' id='save_loading'></div>
		</div>
<textarea onkeydown=\"textarea_useTab( this, event );\" style='overflow:auto;width:97%;height:300px;margin:5px;' wrap='off' name='page_contents' id='page_contents' >
$thisfile
</textarea>
		<div style='display:none;' id='page_contents_convert' ></div>
		</td>
	</tr>
	</table>
	<table width='100%' border='0' cellspacing='0' cellpadding='0' >
	<tr>
		<td bgcolor='#ffffff' height='30' colspan='4' style='padding:10px 0 0 0'><textarea style='overflow:auto;width:98%;height:50px;' wrap='off' onfocus='CheckChangeMemo()' name='page_change_memo' >소스 수정내용을 입력해주세요</textarea></td>
	</tr>
	<tr>
		<td colspan=3 align=center style='padding:10px;'>";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Contents .="<input type='checkbox' id='design_backup' name='design_backup' value='1'><label for='design_backup'>디자인 백업하기</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type=image src='../images/".$admininfo["language"]."/b_save.gif' id='save_btn' border=0 align=absmiddle>";
		}else{
		$Contents .= "<input type='checkbox' id='design_backup' name='design_backup' value='1'><label for='design_backup'>디자인 백업하기</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a>";
		}
$Contents .="

		</td>
	</tr>
	</form>
	<tr><td colspan=3 align=center style='padding:10px;' id='design_history_area'>".PrintEditPageHistory("bbs_templet/".$templet."/".$page_name)."</td></tr>
</table> ";


$category_str ="<div class=box id=img3  style='width:155px;height:375px;overflow:auto;'>".Category()."</div>";

if($mmode == "innerview"){
	echo "<body><div id='design_history_area'>".PrintEditPageHistory("bbs_templet/".$templet."/".$page_name)."</div></body>\n";
	echo "<script>parent.document.getElementById('design_history_area').innerHTML = document.getElementById('design_history_area').innerHTML </script>";
	exit;
}else{
	$P = new LayOut;
	$P->addScript = "$Script";
	//$P->OnloadFunction = "PageLoad();";//showSubMenuLayer('storeleft');
	$P->title = "";
	$P->Navigation = "디자인관리 > 기타 디자인관리 > 게시판 상세디자인";
	$P->title = "게시판 상세디자인";
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
	//echo $sql;
	$mdb->query($sql);

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>";
	$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=s_td width='25%'>수정일자</td><td class=m_td width='50%'>수정내용</td><td class=e_td width='25%'>관리</td></tr>";
	if ($mdb->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=3 align=center>페이지 수정목록이 존재 하지 않습니다..</td></tr>";
	}else{

		$mdb->query("select * from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix ='".$admininfo[mall_ix]."' order by regdate desc   limit $start , $max");

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
			<td class='list_box_td' align=left style='padding-left:20px;'>".$page_change_memo."</td>
			<td class='list_box_td list_bg_gray' align=center>";

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

	}

	$query_string = str_replace("nset=$nset&page=$page&","",$QUERY_STRING) ;
	//echo $query_string;
	$mString .= "</table>";
	$mString .= "<table cellpadding=0 cellspacing=0 width=100%>";
	$mString .= "<tr height=50 bgcolor=#ffffff><td colspan=3 align=left>".page_bar($total, $page, $max,"&".$query_string,"")."</td></tr>
					</table>";

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
	global $DOCUMENT_ROOT, $templet, $SubID;
	if($path == ""){
		//$path = $_SERVER["DOCUMENT_ROOT"].".$admin_config[mall_data_root]/bbs_templet/basic";
		$path = $_SERVER["DOCUMENT_ROOT"]."/bbs_templet/basic";
	}

	$mstring =  "<select name='page_name' onchange=\"document.location.href='design.bbs.php?SubID=$SubID&templet=$templet&page_name='+this.value\">";
	if(FileList2($path, 0, "FULL")){
		$mstring .= FileList2($path, 0, "FULL");
	}else{
		$mstring .= "<option>파일이 존재하지않습니다.</option>";
	}
	$mstring .=  "<select>";

	return $mstring;
}



?>