<?
include("../class/layout.class");
include("category.lib.php");
include("stree.lib.php");

$db = new Database;


//$page_path = getDesignTempletPath($pcode, $depth);

$db->query("select * from ".TBL_SHOP_DESIGN." where mall_ix = '".$admininfo[mall_ix]."' and pcode ='basic' ");
$db->fetch();

if($db->total){	
	$page_name = $db->dt[contents];
	$page_title = $db->dt[page_title];
	$templet_name = $db->dt[templet_name];
	//$page_path = $db->dt[page_path];
	$page_link = $db->dt[page_link];
	$page_desc = $db->dt[page_desc];
	$page_help = $db->dt[page_help];
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
$templet_file_path = $templet_path."/".$page_path."/".$page_name;
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
	$page_path_string = $admin_config[mall_use_templete]."/$page_path/".$page_name;
}

$Script = "
<Script Language='JavaScript' src='../js/XMLHttp.js'></Script>
<Script Language='JavaScript' src='design.js'></Script>
<Script Language='JavaScript'>
function SubmitX(frm){
	
	alert(1);
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

function setCategory(cname,cid,depth,category_display_type){
	if(category_display_type == 'F'){
		alert('분류정보입니다.');
	}else{
		document.location.href='design.php?pcode='+cid+'&depth='+depth+'&category_display_type='+category_display_type+'&SubID=SM114641Sub'
	}
}

</Script>";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' >적용하시길 원하는 타이틀 디자인을 선택하시면 전체 페이지에적용된 타이틀이 수정됩니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >개별 적용하고 싶은 페이지의 타이틀은 치환함수를 삭제한후 직접 HTML 로 적용하시면 됩니다</td></tr>
	<tr><td valign=top></td><td class='small' >치환함수 : <b>displayDesignModule(\"title\", array(\"title\"=>\"회원가입\", \"navigation\"=>\"홈 > 회원가입\", \"템플릿 파일경로\")</b></td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >치환함수에 템플릿 경로가 입력되지 않으면 기본정의된 타이틀 템플릿이 적용되게 됩니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' style='vertical-align:top'>
	<tr height=30>
	    <td align='left' colspan=3 > ".GetTitleNavigation("타이틀 디자인관리", "디자인관리 > 타이틀 디자인관리 ")."</td>
	</tr>
	<tr height=30>
		<td colspan=3 align=left style='padding-bottom:10px;'>
		".HelpBox("타이틀 디자인 관리", $help_text, 171)."
		</td>
	</tr>	
	<tr height=30><td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><b>적용중인 타이틀</b></div>")."</td></tr>	
	<tr height=50>
		<td colspan=3 align=right style='padding-bottom:10px;'>
			<table class='box_shadow' style='width:100%;height:50px' cellspacing=0 cellpadding=0 >
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05' style='padding:15px;'>	
						".displayDesignModule("title", array("title"=>"회원가입", "navigation"=>"홈 > 회원가입"))."
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
			</table>
			
		</td>
	</tr>	
	<tr height=30><td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><b>타이틀 템플릿</b></div>")."</td></td></tr>	
	<tr>
	    <td align='center' colspan=3  style='vertical-align:top'>
	   			
						<table id='design_structure' width=100% border=0>
						<tr>
							<td colspan=3>
								".SelectTempleteList("header2",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/module/title_templet/","")."
							</td>
						</tr>
						
						</table>
	    </td>
	</tr>
</table>";


                  
$category_str ="<div class=box id=img3  style='width:155px;height:375px;overflow:auto;'>".Category()."</div>";
           
$P = new LayOut;
$P->addScript = "$Script";
//$P->OnloadFunction = "PageLoad();";//showSubMenuLayer('storeleft');
$P->title = "";
$P->strLeftMenu = design_menu("/admin",$category_str);      
$P->strContents = $Contents;
$P->Navigation = "디자인관리 > 기타 디자인관리 > 타이틀 디자인";
$P->title = "타이틀 디자인";
$P->PrintLayOut();

function SelectTempleteList($objname, $path, $select_file){
	global $DOCUMENT_ROOT, $admin_config;
	if($path == ""){
		$path = $_SERVER["DOCUMENT_ROOT"]."/".$admin_config[mall_data_root]."/module/title_templet/";	
	}
	
	
	if(TempleteList($path, 0, "FULL")){
		$mstring .= TempleteList($path, $select_file, 0, "FULL");	
	}
	
	
	return $mstring;
}



function TempleteList ( $path , $select_file="", $maxdepth = -1 , $mode = "FULL" , $d = 0 ){
	global $DOCUMENT_ROOT, $admininfo;
   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }      
   $dirlist = array () ;
   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
   if ( $handle = @opendir ( $path ) )
   {
   	
       while ( false !== ( $file = readdir ( $handle ) ) )
       {
       		
           if ( $file != '.' && $file != '..' )
           {
           //	echo $file."<br>";
               $only_file = $file;
               $file = $path . $file ;
               
               if ( !is_dir($file) && !substr_count($file, ".xml")){    //|| $mode == "FULL"
               	//echo is_dir($file);	
               			$mstring .=  "<table style='float:left;width:100%;' border='0'>
               					<col width=10%><col width=15%><col width=*>
               					<tr>
               						<td align=left><b>".$only_file ."</b></td>
	               					<td align=left>
	               						<a href='design_module.php?SubID=SM22464243Sub&module_type=title&page_name=".$only_file."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a>
	               						<a onclick=\"javascript:document.frames['iframe_act'].location.href='design_title.act.php?act=apply&templet=".$only_file ."'\"><img src='../images/".$admininfo["language"]."/btc_apply_title.gif' border=0></a>
	               					</td>
	               					<td></td>
               					</tr>
               					<tr>
               					<td height='50' width=90% colspan=3 style='padding-bottom:40px;'>";
												if(is_file($file."/".$only_file.".gif")){
													$mstring .=	"<a href='design_module.php?SubID=SM22464243Sub&page_name=".$only_file."'><img src='".str_replace($DOCUMENT_ROOT,"",$path)."".$only_file."/".$only_file.".gif' width=260 style='border:1px solid #efefef'></a>";
												}else{
												//	$mstring .=	displayDesignModule("title", array("title"=>"회원가입", "navigation"=>"홈 > 회원가입"), $only_file);
													
													$mstring .= "
													<table class='box_shadow' style='width:100%;height:50px' cellspacing=0 cellpadding=0 >
													<tr>
														<th class='box_01'></th>
														<td class='box_02'></td>
														<th class='box_03'></th>
													</tr>
													<tr>
														<th class='box_04'></th>
														<td class='box_05' style='padding:15px;'>	
															".displayDesignModule("title", array("title"=>"회원가입", "navigation"=>"홈 > 회원가입"), $only_file)."
														</td>
														<th class='box_06'></th>
													</tr>
													<tr>
														<th class='box_07'></th>
														<td class='box_08'></td>
														<th class='box_09'></th>
													</tr>
												</table>";
												}
						$mstring .= "</td>
												</tr>
               						</table>";
               		
               		
               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
                   $mstring .= TempleteList ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;                  
                  //$mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
               }
           }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $mstring ) ;
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
			<td bgcolor='#fbfbfb'>".$mdb->dt[regdate]."</td>
			<td align=left style='padding-left:20px;'>".$page_change_memo."</td>
			<td bgcolor='#fbfbfb' align=center nowrap>
				<a href=JavaScript:pageHistoryRecovery('".$mdb->dt[page_ix]."')><img  src='../image/btn_recovery.gif' border=0></a>
				<img  src='../image/btn_view_source.gif' border=0>
				<a href=JavaScript:pageHistoryDelete('".$mdb->dt[page_ix]."')><img  src='../image/btn_delete.gif' border=0></a>
				<!--a href=JavaScript:pageHistoryDelete('".$mdb->dt[page_ix]."')><img  src='../image/si_remove.gif' border=0></a-->
			</td>
			</tr>
			<tr height=1><td colspan=6 class='dot-x'></td></tr>
			";
		}
		
	}
	
	
	$mString = $mString."<tr height=50 bgcolor=#ffffff><td colspan=6 align=left>".page_bar($total, $page, $max,  "&max=$max")."</td></tr>
					</table>";
	
	return $mString;
}



?>
