<?
include("../class/layout.class");
include("category.lib.php");
include("stree.lib.php");

//ini_set('error_reporting', E_ALL|E_STRICT); ini_set('display_errors', 'Off'); 

$db = new Database;


$db->query("select photoskin_type from shop_shopinfo where mall_ix = '".$admininfo[mall_ix]."'  ");
$db->fetch();
$photoskin_type = $db->dt[photoskin_type];
if($photoskin_type == 1){
	$photoskin_type_name = "기본 매직스킨";
}else if($photoskin_type == 2){
	$photoskin_type_name = "시간대별 매직스킨";
}else if($photoskin_type == 3){
	$photoskin_type_name = "날씨별 매직스킨";
}else {
	$photoskin_type_name = "선택되지 않음";
}

//echo $photoskin_type;

$db->query("select * from shop_design_skin  ");
$db->fetch();

for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
	$skininfo[$db->dt[photoskin_type]][$db->dt[photoskin_name]] = $db->dt[photoskin];
}

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
	var area = new Array('design_structure','photoskin_type_1_area','photoskin_type_2_area','photoskin_type_3_area','design_help');
	var tab = new Array('tab_01','tab_02','tab_03','tab_04','tab_05');
	
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

function showPotoSkinSelect(vid, tab_id){
	var area = new Array();
	var tab = new Array('photoskin_type_1','photoskin_type_2','photoskin_type_3');
	
	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';			
			//document.getElementById(tab_id).className = 'on';
		}else{			
			document.getElementById(area[i]).style.display = 'none';
			//document.getElementById(tab[i]).className = '';
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
	    <td align='left' colspan=3 > ".GetTitleNavigation("매직스킨관리", "디자인관리 > 매직스킨관리 ")."</td>
	</tr>
	<tr height=30><td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><b> 선택된 매직스킨 : <b style='color:red;'>".$photoskin_type_name."</b>
	</b></div>")."</td></tr>	
	<tr>
	    <td align='left' colspan=3  style='vertical-align:top'>
		<form name='skin_frm' method='POST' action='design_photoskin.act.php' target='act'><input type='hidden' name='act' value='photoskin_update' >
	   			 <div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_01' ".($photoskin_type == "" ? "class='on'":"")." >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('design_structure','tab_01')\">매직스킨목록</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($photoskin_type == "1" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('photoskin_type_1_area','tab_02');$('#photoskin_type_1').attr('checked','true');\" style='margin-padding:20px;'>
									<table cellpadding=0 cellspacing=0 >
										<tr>
											<td style='display:none;'><input type='radio' name='photoskin_type' id='photoskin_type_1' value='1' ".($photoskin_type == "1" ? "checked":"")."></td>
											<td style='vertical-align:middle'><label for='photoskin_type_1' >기본매직스킨</label></td>
										</tr>
									</table>
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_03' ".($photoskin_type == "2" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('photoskin_type_2_area','tab_03');$('#photoskin_type_2').attr('checked','true');\">
										<table cellpadding=0 cellspacing=0 >
										<tr>
											<td style='display:none;'><input type='radio' name='photoskin_type' id='photoskin_type_2' value='2' ".($photoskin_type == "2" ? "checked":"")."></td>
											<td style='vertical-align:middle'><label for='photoskin_type_2' >시간대별 매직스킨</label></td>
										</tr>
										</table>
										
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_04' ".($photoskin_type == "3" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('photoskin_type_3_area','tab_04');$('#photoskin_type_3').attr('checked','true');\">
								<table cellpadding=0 cellspacing=0 >
										<tr>
											<td style='display:none;'><input type='radio' name='photoskin_type' id='photoskin_type_3' value='3' ".($photoskin_type == "3" ? "checked":"")."></td>
											<td style='vertical-align:middle'><label for='photoskin_type_3' >날씨별 매직스킨</label></td>
										</tr>
									</table>
								</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_05' >
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"showTabContents('design_help','tab_05')\">도움말</td>
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
						<table id='design_structure' border=0 style='".($photoskin_type == "" ? "display:block":"display:none")."'>
						<tr>
							<td colspan=3>
								".SelectPhotoSkinList("header2",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/","")."
							</td>
						</tr>
						</table>
						<div id='photoskin_type_1_area' style='".($photoskin_type == "1" ? "display:block":"display:none")."'> 
							<div style='padding:8px 0px'><!--* 선택된 스킨에 따라서 배경화면이 고정 적용됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</div>
							<input type=hidden name='skininfo[1][basic_skin]' id='basic_skin' value='".$skininfo[1][basic_skin]."'>
							<table>
								<tr><td>".SelectPhotoSkinSelect("basic_skin",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/","", $skininfo[1][basic_skin])."</td></tr>
								<tr><td align=center style='padding:10px;'>";
									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
										$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle>";
									}else{
										$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a>";

									}

								$Contents .= "	</td></tr>
							</table>
						</div>
						<div id='photoskin_type_2_area' style='".($photoskin_type == "2" ? "display:block":"display:none")."'>
							<div style='padding:8px 0px'><!--* 시간대별 매직스킨은 시간에 따라서 매직스킨이 바뀌어집니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</div>
							<input type=hidden name='skininfo[2][morning]' id='time_skin_morning' value='".$skininfo[2][morning]."'>
							<input type=hidden name='skininfo[2][afternoon]' id='time_skin_afternoon' value='".$skininfo[2][afternoon]."'>
							<input type=hidden name='skininfo[2][evening]' id='time_skin_evening' value='".$skininfo[2][evening]."'>
							<input type=hidden name='skininfo[2][night]' id='time_skin_night' value='".$skininfo[2][night]."'>
							<table cellpadding=0 cellspacing=0 width=100%>
								<tr><td bgcolor=gray style='color:#ffffff;height:20px;padding:5px 10px;'>아침 (06:00~10:00) : </td></tr>
								<tr><td style='padding:10px 0px'>".SelectPhotoSkinSelect("time_skin_morning",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/","", $skininfo[2][morning])."</td></tr>
								<tr><td bgcolor=gray style='color:#ffffff;height:20px;padding:5px 10px;'>점심(낮) (10:00~17:00) : </td></tr>
								<tr><td style='padding:10px 0px'>".SelectPhotoSkinSelect("time_skin_afternoon",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/","", $skininfo[2][afternoon])."</td></tr>
								<tr><td bgcolor=gray style='color:#ffffff;height:20px;padding:5px 10px;'>저녁 (17:00~21:00) : </td></tr>
								<tr><td style='padding:10px 0px'>".SelectPhotoSkinSelect("time_skin_evening",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/","", $skininfo[2][evening])."</td></tr>
								<tr><td bgcolor=gray style='color:#ffffff;height:20px;padding:5px 10px;'>밤 (21:00~06:00) : </td></tr>
								<tr><td style='padding:10px 0px'>".SelectPhotoSkinSelect("time_skin_night",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/","", $skininfo[2][night])."</td></tr>
								<tr>
									<td align=center style='padding:10px;'>";
									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
										$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle>";
									}else{
										$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a>";

									}

								$Contents .= "	
									</td>
								</tr>
							</table>
						</div>
						<div id='photoskin_type_3_area' style='".($photoskin_type == "3" ? "display:block":"display:none")."'>
							<div style='padding:8px 0px'> <!--* 날씨별 매직스킨의 날씨의 변화에 따라서 자동으로 선택하신 스킨이 변경이 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." </div>
							<input type=hidden name='skininfo[3][clean]' id='wether_skin_clean' value='".$skininfo[3][clean]."'>
							<input type=hidden name='skininfo[3][cloudy]' id='wether_skin_cloudy' value='".$skininfo[3][cloudy]."'>
							<input type=hidden name='skininfo[3][rain]' id='wether_skin_rain' value='".$skininfo[3][rain]."'>
							<input type=hidden name='skininfo[3][snow]' id='wether_skin_snow' value='".$skininfo[3][snow]."'>

							<table cellpadding=0 cellspacing=0 width=100%>
								<tr><td bgcolor=gray style='color:#ffffff;height:20px;padding:5px 10px;'>맑음 : </td></tr>
								<tr><td style='padding:10px 0px'>".SelectPhotoSkinSelect("wether_skin_clean",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/","", $skininfo[3][clean])."</td></tr>
								<tr><td bgcolor=gray style='color:#ffffff;height:20px;padding:5px 10px;'>흐림 : </td></tr>
								<tr><td style='padding:10px 0px'>".SelectPhotoSkinSelect("wether_skin_cloudy",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/","", $skininfo[3][cloudy])."</td></tr>
								<tr><td bgcolor=gray style='color:#ffffff;height:20px;padding:5px 10px;'>비 </td></tr>
								<tr><td style='padding:10px 0px'>".SelectPhotoSkinSelect("wether_skin_rain",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/","", $skininfo[3][rain])."</td></tr>
								<tr><td bgcolor=gray style='color:#ffffff;height:20px;padding:5px 10px;'>눈 </td></tr>
								<tr><td style='padding:10px 0px'>".SelectPhotoSkinSelect("wether_skin_snow",$DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/","", $skininfo[3][snow])."</td></tr>
								<tr><td align=center style='padding:10px;'>";
									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
										$Contents .= "<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle>";
									}else{
										$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a>";

									}

								$Contents .= "	</td></tr>
							</table>
						</div>

						
						<div id='design_help' style='display:none;'>".nl2br($page_help)."</div>
					</div>
					<!-- my_movie end -->
				</div>		
			</form>
	    </td>
	</tr>
 </table> ";
                  
$category_str ="<div class=box id=img3  style='width:155px;height:375px;overflow:auto;'>".Category()."</div>";
           
$P = new LayOut;
$P->addScript = "$Script";
//$P->OnloadFunction = "PageLoad();";//showSubMenuLayer('storeleft');
$P->title = "";
$P->strLeftMenu = design_menu("/admin",$category_str);      
$P->strContents = $Contents;
$P->Navigation = "디자인관리 > 기타 디자인관리 > 매직스킨관리";
$P->title = "매직스킨관리";
$P->PrintLayOut();

function SelectPhotoSkinList($objname, $path, $select_file){
	global $DOCUMENT_ROOT, $admin_config;
	if($path == ""){
		$path = $DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/";	
	}
	
	//echo $path;
	if(photoSkinList($path, 0, "FULL")){
		$mstring .= photoSkinList($path, $select_file, 0, "FULL");	
	}
	
	
	return $mstring;
}



function photoSkinList ( $path , $select_file="", $maxdepth = -1 , $mode = "FULL" , $d = 0 ){
	global $DOCUMENT_ROOT;
   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }      
   $dirlist = array () ;
   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
   if ( $handle = @opendir ( $path ) )
   {
   //	var_dump($handle);
       while ( false !== ( $file = readdir ( $handle ) ) )
       {
		  
           if ( $file != '.' && $file != '..' )
           {
			 
               $only_file = $file;
               $file = $path . $file ;
               if ( ! is_dir ( $file ) || $mode == "FULL"){    
				   //echo $only_file."<br>";
               		if(substr($only_file,0,2) == "s_"){
               			$mstring .=  "<table style='float:left;width:200px;margin:0px 0px 10px 3px;' border='0' cellpadding=0 cellspacing=0>
								<tr height=20><td align=center bgcolor=#efefef><b>".str_replace("s_","",$only_file)."</b></td></tr>
               					<tr><td width='270' height='140'>
               					<table cellpadding=0 cellspacing=0 width=100%>
               					<tr height='140'>
               						<td align=center>";
						
							$mstring .=	"<img src='".str_replace($DOCUMENT_ROOT,"",$file)."' width=200 style='border:1px solid #efefef'>";
						
						$mstring .= "
									</td>
								</tr>
								<tr><td align=center style='padding:3px 0px'>마지막 수정일자:".date ("Y-m-d H:i:s", filemtime($file))."</td></tr>
								</table>	
									</td>
								</tr>
								</table>";
               		}
               		
               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
                   $mstring .= photoSkinList ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;                  
                  //$mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
               }
           }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $mstring ) ;
}


function SelectPhotoSkinSelect($objname, $path, $select_file, $selected){
	global $DOCUMENT_ROOT, $admin_config;
	if($path == ""){
		$path = $DOCUMENT_ROOT.$admin_config[mall_data_root]."/photoskin/";	
	}
	
	//echo $path;
	if(photoSkinSelect($path, 0, "-1", 'FULL', 0, $objname)){
		$mstring .= photoSkinSelect($path, $select_file, 0, "FULL",0, $objname, $selected);	
	}
	
	
	return $mstring;
}


function photoSkinSelect ( $path , $select_file="", $maxdepth = -1 , $mode = "FULL" , $d = 0 , $objname = '', $selected=''){
	global $DOCUMENT_ROOT;
   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }      
   $dirlist = array () ;
   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
   if ( $handle = @opendir ( $path ) )
   {
   //	var_dump($handle);
       while ( false !== ( $file = readdir ( $handle ) ) )
       {
		  
           if ( $file != '.' && $file != '..' )
           {
			 
               $only_file = $file;
               $file = $path . $file ;
               if ( ! is_dir ( $file ) || $mode == "FULL"){    
				   //echo $only_file."<br>";
               		if(substr($only_file,0,2) == "s_"){
               			$mstring .=  "<table style='table-layout:fixed;float:left;width:205px;margin:0px 0px 10px 3px;' border='0' cellpadding=0 cellspacing=0 >
								<tr height=20><td align=center bgcolor=#efefef><b>".str_replace("s_","",$only_file)."</b></td></tr>
               					<tr><td width='204' height='150'>
               					<table cellpadding=0 cellspacing=0 width=100% style='table-layout:fixed;' >
               					<tr height='150'>
               						<td align=center>";
						
							$mstring .=	"<img src='".str_replace($DOCUMENT_ROOT,"",$file)."' width=198 height=136 style='".(str_replace("s_","",$only_file) == $selected ? "border:3px solid red":"border:1px solid #efefef")."' class='".$objname."' onclick=\"\$('.".$objname."').each(function(index){\$(this).css('border','0px');});\$(this).css('border','3px solid red');$('#".$objname."').val('".str_replace("s_","",$only_file)."');\"></a>";
						
						$mstring .= "
									</td>
								</tr>
								<tr><td align=center style='padding:3px 0px'>마지막 수정일자:".date ("Y-m-d H:i:s", filemtime($file))."</td></tr>
								</table>	
									</td>
								</tr>
								</table>";
               		}
               		
               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
                   $mstring .= photoSkinList ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;                  
                  //$mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
               }
           }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $mstring ) ;
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
