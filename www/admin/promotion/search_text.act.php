<?
include($_SERVER["DOCUMENT_ROOT"]."/class/mysql.class");


$db = new MySQL;


if ($act == "update"){
	
	if($st_type == "1")			$st_url=$text_st_url;//1 : 텍스트
	else									$st_url=$img_st_url;//2 : 이미지

	$unix_timestamp_sdate = mktime($st_sdate_h,$st_sdate_i,$st_sdate_s,substr($st_sdate,5,2),substr($st_sdate,8,2),substr($st_sdate,0,4));
	$unix_timestamp_edate = mktime($st_edate_h,$st_edate_i,$st_edate_s,substr($st_edate,5,2),substr($st_edate,8,2),substr($st_edate,0,4));

	$sql = "update shop_search_text set
				mall_ix='".$mall_ix."',
				st_text='".$st_text."',
				st_sdate='".$unix_timestamp_sdate."',
				st_edate='".$unix_timestamp_edate."',
				st_type='".$st_type."',
				st_title='".$st_title."',
				st_url='".$st_url."',
				disp='".$disp."',
				display_position='".$display_position."'				
				where st_ix='".$st_ix."' ";
	$db->query($sql);
	
	//print_r($_POST);
	//$db->debug = true;
	$db->query("update shop_search_category_relation set insert_yn = 'N'  where st_ix = '".$st_ix."'  ");
	
	for($j=0;$j < count($selected_result['category']);$j++){
		$db->query("select scr_ix from shop_search_category_relation where st_ix = '".$st_ix."'  and cid = '".$selected_result['category'][$j]."' ");

		if(!$db->total){
			$sql = "insert into shop_search_category_relation (scr_ix,cid,st_ix, vieworder, insert_yn, regdate) values ('','".$selected_result['category'][$j]."','".$st_ix."','".($j+1)."','Y', NOW())";
			$db->sequences = "SHOP_POPUP_BRAND_SEQ";
			$db->query($sql);
		}else{
			$db->fetch();
			$scr_ix = $db->dt[scr_ix];
			$sql = "update shop_search_category_relation set insert_yn = 'Y',vieworder='".($j+1)."' where scr_ix = '".$scr_ix."' and st_ix = '".$st_ix."'  and cid = '".$selected_result['category'][$j]."' ";
			$db->query($sql);
		}
	}

	$db->query("delete from shop_search_category_relation where st_ix = '".$st_ix."'  and insert_yn = 'N' ");

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/search/";

	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/search/$st_ix/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		//chmod($path,0777)
	}


	if(is_dir($path)){
		if($st_img_size > 0){
			move_uploaded_file($st_img, $path."/".$st_ix.".gif");
		}
	}
	//exit;
	echo("<script>alert('정상적으로 수정 되었습니다.');top.location.reload();</script>");
}



if ($act == "insert"){
	
	if($st_type == "1")			$st_url=$text_st_url;//1 : 텍스트
	else									$st_url=$img_st_url;//2 : 이미지

	$unix_timestamp_sdate = mktime($st_sdate_h,$st_sdate_i,$st_sdate_s,substr($st_sdate,5,2),substr($st_sdate,8,2),substr($st_sdate,0,4));
	$unix_timestamp_edate = mktime($st_edate_h,$st_edate_i,$st_edate_s,substr($st_edate,5,2),substr($st_edate,8,2),substr($st_edate,0,4));


	$sql = "insert into shop_search_text (st_ix,mall_ix,st_text,st_sdate,st_edate,st_type,st_title,st_url,disp,display_position, regdate) values ('','$mall_ix','$st_text','$unix_timestamp_sdate','$unix_timestamp_edate','$st_type','$st_title','$st_url','$disp','$display_position',NOW())";
	$db->sequences = "SHOP_SEARCH_TEXT_SEQ";
	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$st_ix = $db->last_insert_id;
	}else{
		$db->query("SELECT st_ix FROM shop_search_text WHERE st_ix=LAST_INSERT_ID()");
		$db->fetch();
		$st_ix = $db->dt[st_ix];
	}

	$db->query("update shop_search_category_relation set insert_yn = 'N'  where st_ix = '".$st_ix."'  ");

	for($j=0;$j < count($selected_result['category']);$j++){
		$db->query("select scr_ix from shop_search_category_relation where st_ix = '".$st_ix."'  and cid = '".$selected_result['category'][$j]."' ");

		if(!$db->total){
			$sql = "insert into shop_search_category_relation (scr_ix,cid,st_ix, vieworder, insert_yn, regdate) values ('','".$selected_result['category'][$j]."','".$st_ix."','".($j+1)."','Y', NOW())";
			$db->sequences = "SHOP_POPUP_BRAND_SEQ";
			$db->query($sql);
		}else{
			$db->fetch();
			$scr_ix = $db->dt[scr_ix];
			$sql = "update shop_search_category_relation set insert_yn = 'Y',vieworder='".($j+1)."' where scr_ix = '".$scr_ix."' and st_ix = '".$st_ix."'  and cid = '".$selected_result['category'][$j]."' ";
			$db->query($sql);
		}
	}

	$db->query("delete from shop_search_category_relation where st_ix = '".$st_ix."'  and insert_yn = 'N' ");

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/search/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/search/$st_ix/";

	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	if(is_dir($path)){
		if($st_img_size > 0){
			move_uploaded_file($st_img, $path."/".$st_ix.".gif");
		}
	}

	echo("<script>alert('정상적으로 등록 되었습니다.');top.location.href='./search_text_list.php';</script>");
}


if ($act == "delete")
{
	if(is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/search/$st_ix/")){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/search/$st_ix/");
	}

	$db->query("DELETE FROM shop_search_text WHERE st_ix='$st_ix'");

	echo("<script>alert('정상적으로 삭제 되었습니다.');top.location.reload();</script>");
	exit;
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



function GetDirContents($dir){
   ini_set("max_execution_time",10);
   if (!is_dir($dir)){die ("Fehler in Funktion GetDirContents: kein g?s Verzeichnis: $dir!");}
   if ($root=@opendir($dir)){
       while ($file=readdir($root)){
           if($file=="." || $file==".."){continue;}
           if(is_dir($dir."/".$file)){
               $files=array_merge($files,GetDirContents($dir."/".$file));
           }else{
           $files[]=$dir."/".$file;
           }
       }
   }
   return $files;
}


function ClearText($str){
	return str_replace(">","",$str);
}

function returnFileName($filestr){
	$strfile = split("/",$filestr);

	return str_replace("%20","",$strfile[count($strfile)-1]);
	//return count($strfile);

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
?>
