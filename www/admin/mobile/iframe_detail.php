<?
include("$DOCUMENT_ROOT/class/layout.class");

$db = new MySQL();
// 제품 정보 셋팅

$sql = "SELECT pname , basicinfo, movie FROM ".TBL_SHOP_PRODUCT." p where id = '$id' limit 1";
$db->query($sql);
$db->fetch();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="id" xml:lang="id" >
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<script type="text/javascript" language=javascript src="<?=$_SESSION["layout_config"]["mall_templet_webpath"]?>/js/jquery-1.5.2.min.js"></script>
<script LANGUAGE="JavaScript" >
	
//플래쉬 재생 소스
function generate_flash(file_, width_, height_){

	var mstring="";
	
	mstring = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="'+width_+'" height="'+height_+'" id="falsh_" align="middle"> \n';
	mstring += '<param name="allowScriptAccess" value="always" /> \n';
	mstring += '<param name="movie" value="'+file_+'" /> \n';
	mstring += '<param name="quality" value="high" /> \n';
	mstring += '<param name="wmode" value="Transparent" /> \n';
	mstring += '<param name="bgcolor" value="#ffffff" /> \n';
	mstring += '<embed src="'+file_+'" quality="high" bgcolor="#ffffff" width="'+width_+'" height="'+height_+'" name="flash_" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /> \n';
	mstring += '</object> \n';
	
	document.write(mstring);
}

</script>

<style>
a {border:0px;}
img {width:100%;}
</style>

<body leftmargin="0" oncontextmenu='return false' >
<div align=center width="100%" >
<?
if($db->dt[movie] != ""){	
?>
<div><script language='javascript'>generate_flash('/swf/cf3.swf?movie_path=<?=$db->dt[movie]?>',320,270)</script></div>
<?
}
?>
</div>

<div align=center width="100%" >

<?
if($db->dt[basicinfo] == "" || $db->dt[basicinfo] != "<P>&nbsp;</P>"){	
?>

<?=str_replace("&amp;","&",str_replace('&quot;','"',str_replace("&#39;","'",$db->dt[basicinfo])))?>

<?
}else{
	echo "<div style='font-size:11px;color:gray;height:100px;padding-top:50px;width:100%;text-align:center;'>상품정보가 입력되지 않았습니다</div>";
}
?>
</div>

</body>
</html>