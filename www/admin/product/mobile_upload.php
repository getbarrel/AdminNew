<?
include("../class/layout.class");


//ini_set('error_reporting', E_ALL|E_STRICT); ini_set('display_errors', 'Off'); 

$db = new Database;


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
	
	<tr height=30><td colspan=3 align=left style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><b> 모바일 업로드 사진목록 <b style='color:red;'></b>
	</b></div>")."</td></tr>	
	<tr>
	    <td align='left' colspan=3  style='vertical-align:top'>
					<!-- my_movie start -->
					<div class='my_box' style='padding:10px;'>
						<table id='design_structure' border=0 style='".($photoskin_type == "" ? "display:block":"display:none")."'>
						<tr>
							<td colspan=3>
								".SelectMobileUploadImages($DOCUMENT_ROOT.$admin_config[mall_data_root]."/BatchUploadImages/","")."
							</td>
						</tr>
						</table>
						
					</div>
					<!-- my_movie end -->
	    </td>
	</tr>
 </table> ";
                     
$P = new ManagePopLayOut;
$P->addScript = "$Script";
//$P->OnloadFunction = "PageLoad();";//showSubMenuLayer('storeleft');
$P->title = "";
$P->strLeftMenu = design_menu("/admin",$category_str);      
$P->strContents = $Contents;
$P->Navigation = "상품관리 > 모바일 업로드 사진";
$P->title = "모바일 업로드 사진";
$P->NaviTitle = "모바일 업로드 사진";
$P->PrintLayOut();

function SelectMobileUploadImages($path, $select_file){
	global $DOCUMENT_ROOT, $admin_config;
	if($path == ""){
		$path = $DOCUMENT_ROOT.$admin_config[mall_data_root]."/BatchUploadImages/";	
	}
	
	//echo $path;
	if(MobileUploadImages($path, 0, "FULL")){
		$mstring .= MobileUploadImages($path, $select_file, 0, "file");	
	}
	
	
	return $mstring;
}



function MobileUploadImages ( $path , $select_file="", $maxdepth = -1 , $mode = "FULL" , $d = 0 ){
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
               		if(substr($only_file,0,6) == "thumb_"){
               			$mstring .=  "<table style='float:left;width:200px;margin:0px 0px 10px 3px;' border='0' cellpadding=0 cellspacing=0>
								<tr height=20><td align=center bgcolor=#efefef><b>".str_replace("s_","",$only_file)."</b></td></tr>
               					<tr><td width='270' height='140'>
               					<table cellpadding=0 cellspacing=0 width=100%>
               					<tr height='140'>
               						<td align=center>";
						
							$mstring .=	"<img src='".str_replace($DOCUMENT_ROOT,"",$file)."' width=200 style='border:1px solid #efefef' onclick=\"opener.document.getElementById('bimg_text').value=this.src;opener.ChnageImg(this.src);self.close();\">";
						
						$mstring .= "
									</td>
								</tr>
								<tr><td align=center style='padding:3px 0px'>등록일자:".date ("Y-m-d H:i:s", filemtime($file))."</td></tr>
								</table>	
									</td>
								</tr>
								</table>";
               		}
               		
               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
                   $mstring .= MobileUploadImages ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;                  
                  //$mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
               }
           }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $mstring ) ;
}



?>
