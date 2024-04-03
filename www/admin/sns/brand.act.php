<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include('../../include/xmlWriter.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');
$db = new Database;


/*
if ($disp != 1){
	$disp = 0;
}
*/
if ($mode == 'insert')
{
	$sql = $sql."INSERT INTO ".TBL_SNS_BRAND." (b_ix, cid, brand_name, disp, search_disp, top_design, company_id,shotinfo,regdate) values('', '$cid', '$brand', '$disp', '$search_disp','$top_design','".$admininfo[company_id]."','$shotinfo',now()) ";

	$db->query($sql);
	$db->query("SELECT b_ix FROM ".TBL_SNS_BRAND." WHERE b_ix=LAST_INSERT_ID()");
	$db->fetch();
	$LAST_ID = $db->dt[0];

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$LAST_ID/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$LAST_ID/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$LAST_ID/",0777);
	}

	if ($brandimg != "none")
	{
		copy($brandimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$LAST_ID/brand_".$db->dt[0].".gif");
	}
	/*
	if ($brandimg_on != "none")
	{
		copy($brandimg_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$LAST_ID/brand_".$db->dt[0]."_on.gif");
	}*/

	$data_text = $top_design;
	$data_text_convert = $top_design;
	$data_text_convert = str_replace("\\","",$data_text_convert);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);


	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$LAST_ID/";

	if(substr_count($data_text,"<IMG") > 0){
		if(!is_dir($path)){
			mkdir($path, 0777);
			//chmod($path,0777)
		}
	}

//print_r ($out);
//exit;
	for($i=0;$i < count($out);$i++){
		for($j=0;$j < count($out[$i]);$j++){

			$img = returnImagePath($out[$i][$j]);
			$img = ClearText($img);


			if(substr_count($img,$admin_config[mall_data_root]."/images/brand/$LAST_ID/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
				if(substr_count($img,"$HTTP_HOST")>0){
					$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

					@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$LAST_ID/".returnFileName($img));
					if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
						unlink($local_img_path);
					}

					$data_text = str_replace($img,$admin_config[mall_data_root]."/images/brand/$LAST_ID/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
				}else{
					if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$LAST_ID/".returnFileName($img))){
						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/brand/$LAST_ID/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}
				}
			}



		}
	}


	$db->query("UPDATE ".TBL_SNS_BRAND." SET top_design = '$data_text' WHERE b_ix='$LAST_ID'");
	updateBrandsXML();

	if($mmode == "pop"){
	echo "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='pragma' content='no-cache'>

		<body>
		<div id='brand_select_area'>
		".BrandListSelect($b_ix, $cid)."
		</div>
		</body>
		</html>
		<Script Language='Javascript'>
		parent.opener.document.getElementById('brand_select_area').innerHTML = document.getElementById('brand_select_area').innerHTML;
		parent.document.location.reload();
		</Script>";
	}else{
		echo "
		<Script Language='Javascript'>
		parent.document.location.reload();
		</Script>";
		//header("Location:brand.php?mmode=$mmode");
	}
}

if ($mode == "change")
{
//	echo("SELECT b_ix FROM ".TBL_SNS_BRAND." WHERE b_ix=$b_ix");
	$db->query("SELECT * FROM ".TBL_SNS_BRAND." WHERE b_ix=$b_ix");
	$db->fetch();

	$disp = $db->dt[disp];
	if ($db->dt[disp] == 1){
		$checkString = "true";
	}else{
		$checkString = "false";
	}

	if ($db->dt[search_disp] == 1){
		$SearchCheckString = "true";
	}else{
		$SearchCheckString = "false";
	}


	echo "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='pragma' content='no-cache'>
		<body>
		<div id='top_design'>
		".$db->dt[top_design]."
		</div>
		</body>
		</html>
		<Script Language='Javascript'>
		parent.document.forms['brandform'].brand.value = '".$db->dt[brand_name]."';
		parent.document.forms['brandform'].b_ix.value = '".$db->dt[b_ix]."';
		parent.document.forms['brandform'].disp[$disp].checked = true;
		parent.document.forms['brandform'].search_disp.checked = $SearchCheckString;
		parent.document.forms['brandform'].shotinfo.value = '".$db->dt[shotinfo]."';
		parent.document.getElementById('modify').style.display = 'block';
		parent.document.getElementById('delete').style.display = 'block';
		parent.document.getElementById('ok').style.display = 'none';
		var obj = parent.document.forms['brandform'].cid;
		for(i=0;i<obj.length;i++){
			if(obj[i].value == '".$db->dt[cid]."'){
				obj[i].selected = true;
			}
		}
		parent.document.getElementById('iView').contentWindow.document.body.innerHTML = document.getElementById('top_design').innerHTML;
		";

		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$b_ix.".gif")){
			echo "parent.document.getElementById('brandimgarea').innerHTML = \"<img src='".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$db->dt[0].".gif'>\";";
		}else{
			echo "parent.document.getElementById('brandimgarea').innerHTML = \"브랜드 이미지가 입력되지 않았습니다. \";";
		}
		/*
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$db->dt[0]."_on.gif")){
			echo "parent.document.getElementById('brandimgarea_on').innerHTML = \"<img src='".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$db->dt[0]."_on.gif'>\";";
		}else{
			echo "parent.document.getElementById('brandimgarea_on').innerHTML = \"브랜드 이미지(마우스on)가 입력되지 않았습니다. \";";
		}
		*/

	echo "</Script>";

}

if ($mode == "update")
{

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/",0777);
	}

	if ($brandimg != "none")
	{
		copy($brandimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$b_ix.".gif");
	}

	if ($brandimg_on != "none")
	{
		copy($brandimg_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$b_ix."_on.gif");
	}

	$sql = "UPDATE ".TBL_SNS_BRAND." SET
			cid = '$cid',brand_name = '$brand', disp = '$disp', search_disp = '$search_disp', shotinfo ='$shotinfo' ,top_design ='$top_design'
			WHERE b_ix='$b_ix'";
	$db->query($sql);

	$data_text = $top_design;
	$data_text_convert = $top_design;
	$data_text_convert = str_replace("\\","",$data_text_convert);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);


	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/";

	if(substr_count($data_text,"<IMG") > 0){
		if(!is_dir($path)){
			mkdir($path, 0777);
			//chmod($path,0777)
		}
	}

//print_r ($out);
//exit;
	for($i=0;$i < count($out);$i++){
		for($j=0;$j < count($out[$i]);$j++){

			$img = returnImagePath($out[$i][$j]);
			$img = ClearText($img);


			if(substr_count($img,$admin_config[mall_data_root]."/images/brand/$b_ix/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
				if(substr_count($img,"$HTTP_HOST")>0){
					$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

					@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img));
					if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
						unlink($local_img_path);
					}

					$data_text = str_replace($img,$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
				}else{
					if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img))){
						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}
				}
			}



		}
	}


	$db->query("UPDATE ".TBL_SNS_BRAND." SET top_design = '$data_text' WHERE b_ix='$b_ix'");
	//echo("<script>top.location.href = 'brand.php?b_ix=$b_ix';</script>");
	updateBrandsXML();

	if($mmode == "pop"){
	echo "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='pragma' content='no-cache'>
		<body>
		<div id='brand_select_area'>
		".BrandListSelect($b_ix, $cid)."
		</div>
		</body>
		</html>
		<Script Language='Javascript'>
		parent.opener.document.getElementById('brand_select_area').innerHTML = document.getElementById('brand_select_area').innerHTML;
		parent.document.location.reload();
		</Script>";
	}else{
		echo "
		<script language='javascript' src='../js/message.js.php'></script><Script Language='Javascript'>
		show_alert('정상적으로 수정되었습니다.');
		parent.document.location.reload();
		</Script>";
		//header("Location:brand.php?mmode=$mmode");
	}
}

if ($mode == "delete")
{
	/*
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/brand_$b_ix.gif"))
	{
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/brand_$b_ix.gif");
	}
	*/

	if (is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/"))
	{
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/");
	}

	$db->query("DELETE FROM ".TBL_SNS_BRAND." WHERE b_ix='$b_ix'");
	updateBrandsXML();

	if($mmode == "pop"){
	echo "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='pragma' content='no-cache'>
		<body>
		<div id='brand_select_area'>
		".BrandListSelect($b_ix, $cid)."
		</div>
		</body>
		</html>
		<Script Language='Javascript'>
		parent.opener.document.getElementById('brand_select_area').innerHTML = document.getElementById('brand_select_area').innerHTML;
		parent.document.location.reload();
		</Script>";
	}else{
		echo "
		<Script Language='Javascript'>
		parent.document.location.reload();
		</Script>";
	}
}



function BrandListSelect($brand, $cid, $return_type ="")
{
//global $db;

	$mdb = new Database;


	if($cid){
		$mdb->query("SELECT * FROM ".TBL_SNS_BRAND." where disp=1 and cid = '$cid'");
	}else{
		$mdb->query("SELECT * FROM ".TBL_SNS_BRAND." where disp=1");
	}

	$bl = "<Select name='brand' class=small>";
	if ($mdb->total == 0)	{
		$bl = $bl."<Option>등록된 브랜드가 없습니다.</Option>";
	}else{
		if($return_type == ""){
			$bl = $bl."<Option value=''>브랜드 선택</Option>";
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($brand == $mdb->dt[b_ix])
				{
					$strSelected = "Selected";
				}else{
					$strSelected = "";
				}

				$bl = $bl."<Option value='".$mdb->dt[b_ix]."' $strSelected>".$mdb->dt[brand_name]."</Option>";

			}
		}else{
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($brand == $mdb->dt[b_ix]){
					return $mdb->dt[brand_name];
				}
			}
		}
	}

	$bl = $bl."</Select>";

	return $bl;
}

function returnImagePath($str){
	$IMG = split(" ",$str);

	for($i=0;$i<count($IMG);$i++){
		//echo substr_count($IMG[$i],"src");
			if(substr_count($IMG[$i],"src=") > 0){
				$mstring = str_replace("src=","",$IMG[$i]);
				return str_replace("\"","",$mstring);
			}
	}
}

function ClearText($str){
	return str_replace(">","",$str);
}


function returnFileName($filestr){
	$strfile = split("/",$filestr);

	return str_replace("%20","",$strfile[count($strfile)-1]);
	//return count($strfile);

}

function rmdirr($target,$verbose=false)
// removes a directory and everything within it
{
$exceptions=array('.','..');
if (!$sourcedir=@opendir($target))
   {
   if ($verbose)
       echo '<strong>Couldn&#146;t open '.$target."</strong><br />\n";
   return false;
   }
while(false!==($sibling=readdir($sourcedir)))
   {
   if(!in_array($sibling,$exceptions))
       {
       $object=str_replace('//','/',$target.'/'.$sibling);
       if($verbose)
           echo 'Processing: <strong>'.$object."</strong><br />\n";
       if(is_dir($object))
           rmdirr($object);
       if(is_file($object))
           {
           $result=@unlink($object);
           if ($verbose&&$result)
               echo "File has been removed<br />\n";
           if ($verbose&&(!$result))
               echo "<strong>Couldn&#146;t remove file</strong>";
           }
       }
   }
closedir($sourcedir);
if($result=@rmdir($target))
   {
   if ($verbose)
       echo "Target directory has been removed<br />\n";
   return true;
   }
if ($verbose)
   echo "<strong>Couldn&#146;t remove target directory</strong>";
return false;
}


function updateBrandsXML(){

	global $DOCUMENT_ROOT, $admin_config;

	$xml = new XmlWriter_();
	$mdb = new Database;

	$mdb->query("select * from ".TBL_SNS_BRAND." where disp=1 ");
	$brands = $mdb->fetchall();

	$xml->push('brands');


	foreach ($brands as $brand) {
		//$xml->push('shop', array('species' => $animal[0]));
		$xml->push('brand', array('cid' => $brand[cid], 'top_cid' => substr($brand[cid],0,3)));
		$xml->element('top_cid', substr($brand[cid],0,3));
		$xml->element('b_ix', $brand[b_ix]);
		$xml->element('brand_name', $brand[brand_name]);
		$xml->element('brand_link', "/event/goods_brand.php?b_ix=".$brand[b_ix]."&cid=".$brand[cid]);
		$xml->pop();
	}

	$xml->pop();
	//print $xml->getXml();

	$dirname = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];

	$fp = fopen($dirname."/brands.xml","w");
	fputs($fp, $xml->getXml());
	fclose($fp);
}

?>