<?
include("../class/layout.class");
include("category.lib.php");
include("stree.lib.php");

$db = new Database;


$page_path = getDesignTempletPath($pcode, $depth);

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

if($skin_type == "mobile"){
	$templet_path = $admin_config[mall_data_root]."/mobile_templet/".$admin_config[mall_use_mobile_templete];
	$act = "skin_mobile_update";
}else{
	$templet_path = $admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];
	$act = "skin_update";
}

$templet_file_path = $templet_path."/".$page_path."/".$page_name;
$thisfile = load_template($DOCUMENT_ROOT.$templet_file_path);


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



$Contents ="
<table width='100%' border='0' cellspacing='0' cellpadding='0' height='100%' style='vertical-align:top'>
	<tr height=30>
	    <td align='left' colspan=3 > ".GetTitleNavigation("쇼핑몰 스킨관리", "디자인관리 > 쇼핑몰 스킨관리 ")."</td>
	</tr>
	<tr height=30><td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5 5 5 15;'><b> 선택된 페이지 :<a href=\"JavaScript:pview(document.forms['info_input'].page_contents.value, 'aaa', 'bbb');\">$page_path_string</a></b></div>")."</td></tr>	
	<tr>
	    <td align='left' colspan=3  style='vertical-align:top'>
	   			 <div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".($skin_type != "mobile" ? "class='on'":"")."  >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('design_structure','tab_01')\">선택된 템플릿</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($skin_type == "mobile" ? "class='on'":"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='?skin_type=mobile'>선택된 모바일 템플릿</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
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
							<td colspan=3>";
							if($skin_type == "mobile"){
								$Contents .= "<img src='".$templet_path."/".$admin_config[mall_use_mobile_templete].".jpg'>";
							}else{
								$Contents .= "<img src='".$templet_path."/".$admin_config[mall_use_templete].".jpg'>";
							}

							$Contents .= "
							</td>
						</tr>
						
						</table>
						
						<div id='design_help' style='display:none;'>".nl2br($page_help)."</div>
					</div>
					<!-- my_movie end -->
				</div>		
	    </td>
	</tr>
	<tr>
	    <td align='left' colspan=3  style='vertical-align:top'>
	   			 <div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01'  >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('design_structure','tab_01')\">나의 디자인 템플릿</td>
								<th class='box_03'></th>
							</tr>
							</table>
							
						</td>
						<td class='btn'>						
							
						</td>
					</tr>
					</table>	
				</div>
				<div class='mallstory t_no'>
					<!-- my_movie start -->
					<form name='skin_frm' method='POST' action='design_skin_select.act.php' target='act'>
					<input type='hidden' name='act' value='$act' >
					<input type=hidden name='mall_use_templete' id='skin_select' value='".$mall_use_templete."'>
					<div class='my_box' style='padding:10px;'>
						<table id='design_structure' border=0>
						<tr>
							<td colspan=3>";
							if($skin_type == "mobile"){
								$Contents .= SelectTempleteList("header2",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/mobile_templet/","");
							}else{
								$Contents .= SelectTempleteList("header2",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/templet/","");
							}
							$Contents .= "
							</td>
						</tr>
						<tr><td align=center style='padding:10px;'><input type=image src='../image/b_save.gif' border=0 align=absmiddle></td></tr>
						</table>
						
					</div>
					<!-- my_movie end -->
					</form>
				</div>		
	    </td>
	</tr>
</table>
		
		
                  ";
                  
$category_str ="<div class=box id=img3  style='width:155px;height:375px;overflow:auto;'>".Category()."</div>";
           
$LO = new LayOut;
$LO->addScript = "$Script";
//$LO->OnloadFunction = "PageLoad();";//showSubMenuLayer('storeleft');
$LO->title = "";
$LO->strLeftMenu = design_menu("/admin",$category_str);      
$LO->strContents = $Contents;
$LO->Navigation = "HOME > 디자인관리 > 게시판디자인관리";
$LO->PrintLayOut();

function SelectTempleteList($objname, $path, $select_file){
	global $DOCUMENT_ROOT;
	if($path == ""){
		$path = $_SERVER["DOCUMENT_ROOT"]."/data/demo/bbs_templete/";	
	}
	
	
	if(TempleteList($path, 0, "FULL")){
		$mstring .= TempleteList($path, $select_file, 0, "FULL");	
	}
	
	
	return $mstring;
}



function TempleteList ( $path , $select_file="", $maxdepth = -1 , $mode = "FULL" , $d = 0 ){
	global $DOCUMENT_ROOT, $admin_config;
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
               			$mstring .=  "<table style='float:left;width:270px;' border='0'><tr><td align=center><b>".$only_file ."</b></td></tr>
               					<tr><td width='270' height='230'>
               					<table cellpadding=2 width=100%>
               					<tr height='230'>
               						<td align=center>";
						if(is_file($file."/".$only_file.".gif")){
							$mstring .=	"<img src='".str_replace($DOCUMENT_ROOT,"",$path)."".$only_file."/".$only_file.".gif' width=260 style='".($admin_config[mall_use_templete] == $only_file ? "border:3px solid red":"border:1px solid #efefef")."' class='skin_select' onclick=\"\$('.skin_select').each(function(index){\$(this).css('border','0px');});\$(this).css('border','3px solid red');$('#skin_select').val('".str_replace("s_","",$only_file)."');\">";
						}else if(is_file($file."/".$only_file.".jpg")){
							$mstring .=	"<img src='".str_replace($DOCUMENT_ROOT,"",$path)."".$only_file."/".$only_file.".jpg' width=260 style='".($admin_config[mall_use_templete] == $only_file ? "border:3px solid red":"border:1px solid #efefef")."' class='skin_select' onclick=\"\$('.skin_select').each(function(index){\$(this).css('border','0px');});\$(this).css('border','3px solid red');$('#skin_select').val('".str_replace("s_","",$only_file)."');\">";
						}else{
							$mstring .=	"<div style='width:260px;height:304px;align:center;padding:0 0 0 0;line-height:140%;'><b>$only_file.gif</b> 또는<br> <b>$only_file.jpg</b> 이름으로<br> 이미지를 올려주세요.</div>";
						}
						$mstring .= "
										</td>
									</tr>
									<tr><td align=center>마지막 수정일자:".date ("Y-m-d H:i:s", filemtime($file))."</td></tr>
									</table>	
										</td>
									</tr>
									
						</table>";
               		}
               		
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
