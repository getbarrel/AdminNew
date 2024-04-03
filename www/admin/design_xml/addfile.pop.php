<?
include("../class/layout.class");
include("design.common.php");

function mk_dir($path, $rights = 0777)
{
  $folder_path = array(
    strstr($path, '.') ? dirname($path) : $path);

  while(!@is_dir(dirname(end($folder_path)))
          && dirname(end($folder_path)) != '/'
          && dirname(end($folder_path)) != '.'
          && dirname(end($folder_path)) != '')
    array_push($folder_path, dirname(end($folder_path)));

  while($parent_folder_path = array_pop($folder_path))
    if(!@mkdir($parent_folder_path, $rights))
      user_error("Can't create folder \"$parent_folder_path\".");
}

if(!$act){
	$act = "addfile";
}

if($act == "addfile" && $filename){


	$db = new Database;
	if($admin_config[mall_page_type] == "P"){
		$dir_path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/$page_path";
	}else if($admin_config[mall_page_type] == "M"){
		$dir_path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/mobile_templet/".$admin_config[mall_use_mobile_templete]."/$page_path";
	}else if($admin_config[mall_page_type] == "MI"){
		$dir_path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/minishop_templet/".$admin_config[minishop_templete]."/$page_path";
	}
	
	$file_path = $dir_path."/".$filename;
	//echo $file_path;

	if(!is_dir($dir_path)){
		mk_dir($dir_path, 0777);
		chmod($dir_path,0777);
	}else{
		//chmod($dir_path,0777);
	}

	if(!(is_file($file_path))){
		$fp = fopen("$file_path","w");
		fclose($fp);
		chmod($file_path,0777);
		echo "<script>alert('파일이 정상적으로 생성 되었습니다.');opener.document.location.reload();self.close();</script>";
	}else{
		echo "<script>alert('이미존재 하는 파일입니다.');self.close();</script>";
		exit;
	}

}else if($act == "addModule" && $filename){


	$db = new Database;

	$dir_path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/module/".$page_path."_templet/";
	$file_path = $dir_path."/".$filename;
	//echo $file_path;

	if(!is_dir($dir_path)){
		mk_dir($dir_path, 0777);
		chmod($dir_path,0777);
	}else{
		//chmod($dir_path,0777);
	}

	if(!(is_file($file_path))){
		$fp = fopen("$file_path","w");
		fclose($fp);
		chmod($file_path,0777);
		echo "<script>alert('파일이 정상적으로 생성 되었습니다.');opener.document.location.reload();self.close();</script>";
	}else{
		echo "<script>alert('이미존재 하는 파일입니다.');self.close();</script>";
		exit;
	}

}


$Contents = "
<table border=0 cellpadding=0 cellspacing=0 width=100%>
	<!--tr height=35 bgcolor=#efefef>
		<td  style='padding:0 0 0 0;'>
			<table width='100%' border='0' cellspacing='0' cellpadding='0' >
				<tr>
					<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap>
						<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 파일생성하기
					</td>
					<td width='90%' align='right' valign='top' >
						&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr-->
	<tr >
	    <td align='left' colspan=2> ".GetTitleNavigation("파일생성하기", "디자인관리 > 파일생성하기", false)."</td>
	</tr>
	<tr>
		<td style='padding:10px;vertical-align: top; '>
			<form name='file_form' onsubmit='return CheckFormValue(this)'><input type=hidden name=act value='".$act."'><input type=hidden name=pcode value='".$pcode."'><input type=hidden name=page_path value='".$page_path."'>
			<table border=0 width=380 cellpadding=0 class='input_table_box'>
				<tr >
					<td class='input_box_title'><b>파일경로</b></td>
					<td class='input_box_item'>".$page_path."</td>
				</tr>
				<tr >
					<td class='input_box_title'><b>파일이름 <img src='".$required3_path."'></b></td>
					<td class='input_box_item'><input type=text class='textbox' name='filename' size=20 validation='true' title='파일이름'></td>
				</tr>				
			</table>
			<table border=0 width=380 cellpadding=0>
				<tr>
					<td align=right colspan=2>
					<input type='image' src='../images/".$admininfo["language"]."/btn_s_ok.gif' style='border:0px;' align='absmiddle'>
					<a href='javascript:self.close();'><img src='../images/".$admininfo["language"]."/btn_s_cancle.gif' align='absmiddle' border='0'></a>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
	<tr>
		<td align=center>

		</td>
	</tr>
</table>";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "HOME > 디자인관리 > 레이아웃 디자인";
$P->NaviTitle = "레이아웃 디자인";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>