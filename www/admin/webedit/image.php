<?
include("../class/layout.class");
if(empty($upfile)){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>:: 사진 넣기 ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<script language="javascript">
<!--
function imgPreview(){
   if(document.imgForm.upfile.value != ""){
      document.preview.src = document.imgForm.upfile.value;
   }
}
-->
</script>
</head>

<body bgcolor="F9F8F8" leftmargin="2" topmargin="2">
<table width="400" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#999999"><table width="400" border="0" cellspacing="1" cellpadding="2">
        <tr>
          <td bgcolor="#FFFFFF">
            <table width="396" border="0" cellpadding="0" cellspacing="1" class="fon2">
            <form name="imgForm" method="post" enctype="multipart/form-data">
              <tr>
                <td width="83" height="28" bgcolor="#D9D9D9"><div align="center">파일찾기</div></td>
                <td width="310" bgcolor="E1E0E0"> &nbsp;
                  <input type="file" name="upfile" class="input">
                  <!--input type="button" value="미리보기" onClick="imgPreview();" class="input"--></td>
              </tr>
              <!--tr align="center" valign="middle">
                <td height="250" colspan="2"><img src="image/preview.gif" name="preview" width="211" height="153"></td>
              </tr-->
              <tr valign="middle">
                <td height="40" colspan="2"><table width="98" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="45"><input type="image" src="image/bt_save.gif" width="45" height="22"></td>
                      <td width="6">&nbsp;</td>
                      <td><a href="javascript:self.close();"><img src="image/bt_close.gif" width="45" height="22" border="0"></a></td>
                    </tr>
                  </table></td>
              </tr>
            </form>
            </table>
          </td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>
<script type="text/javascript">
window.onload=function() {
	window.resizeTo(415,200); // 미리보기를 주석처리 하였기에 창 크기를 조절하게 함 에디터를 사용하는 페이지마다 수정하는 수고를 덜기 위하여 kbk
}
</script>
<?
}else{
//	print_r($_POST);
//	print_r($_FILES);
//	exit;
   if($upfile_size >0){
   		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/";
      	if(!is_dir($path)){
      		mkdir($path,0777);
     	// exec("chmod 705 $path");
      	}

     // $upfile_ext = strtolower(substr($upfile_name,-3));
      //$upfile_name = date('ymdhis').rand(10,99).".".$upfile_ext;
	 // $upfile_name = $upfile_filename;
      //exec("cp $upfile $path$upfile_name");
      //copy($upfile,$path.iconv("utf-8","CP949",$upfile_name));
      copy($upfile,$path.$upfile_name);
      //exec("chmod 606 $path$upfile_name");
?>
<script language=javascript>
<!--
/*opener.iView.focus();
opener.iView.document.execCommand('insertimage', false, 'http://<?=$HTTP_HOST?><?=$admin_config[mall_data_root]?>/images/upfile/<?=$upfile_name?>');
//opener.iView.document.execCommand('insertimage', false, 'http://<?//=$HTTP_HOST?><?=$admin_config[mall_data_root]?>/images/upfile/<?//=$upfile_name?>');
opener.iView.focus() ;*/
// 호환성 2011-04-06 kbk
opener.document.getElementById("iView").contentWindow.focus();
opener.document.getElementById("iView").contentWindow.document.execCommand('insertimage', false, 'http://<?=$HTTP_HOST?><?=$admin_config[mall_data_root]?>/images/upfile/<?=$upfile_name?>');
//opener.iView.document.execCommand('insertimage', false, 'http://<?//=$HTTP_HOST?><?=$admin_config[mall_data_root]?>/images/upfile/<?//=$upfile_name?>');
opener.document.getElementById("iView").contentWindow.focus() ;
self.close();
-->
</script>
<?
		}
}

?>
