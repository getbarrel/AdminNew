<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
session_start();
$db = new Database;

if ($act == "vieworder_update"){
	//$db->query("update ".TBL_SHOP_MAIN_PRODUCT_RELATION." set vieworder=1 ");
	//$_erpid = str_replace("\\\"","\"",$_POST["erpid"]);
	//echo $_erpid;
	//echo "bbb:".count(unserialize($_POST["erpid"]))."<br>";
	//print_r(unserialize($_POST["erpid"]));
	//$_erpid = unserialize(urldecode($_erpid));
	//$erpid = unserialize(urldecode($erpid));
	//echo count($_erpid);
	for($i=0;$i < count($sortlist);$i++){
		$sql = "update ".TBL_SHOP_MAIN_PRODUCT_RELATION." set 
			vieworder='".($i+1)."'
			where  pid='".$sortlist[$i]."' ";//main_ix='$main_ix' and 

		//echo $sql;
		$db->query($sql);
	}
	
}


if ($act == "insert"){

	//print_r($_POST);
	
	$sql = "insert into deepzoom_gallery_info
			(dgi_ix,charger_ix, gallery_name,gallery_link,display_type,gallery_disp_cnt,insert_yn,use_yn,regdate) 
			values
			('$dgi_ix',".$admininfo[charger_ix].",'".$gallery_name[1]."','".$gallery_link[1]."','".$display_type[1]."','".$gallery_disp_cnt[1]."','".$use_yn[1]."','',NOW())";

	//$db->debug = true;
	//echo $sql;
	//exit;
	$db->query($sql);
	
	$db->query("SELECT dgi_ix FROM deepzoom_gallery_info WHERE dgi_ix=LAST_INSERT_ID()");
	$db->fetch();
	$dgi_ix = $db->dt[0];
	
			
	for($j=0;$j < count($rpid[1]);$j++){						
			//$sql = "insert into ".TBL_SHOP_EVENT_PRODUCT_RELATION." (erp_ix,event_ix,gallery_code, pid, vieworder, insert_yn, regdate) values ('','".$event_ix."','".($i+1)."','".$rpid[$i+1][$j]."','".($i+1)."','Y', NOW())";	
			$sql = "insert into deepzoom_gallery_relation(dgr_ix,di_ix,dgi_ix,vieworder,insert_yn,regdate) values('','".$rpid[$i+1][$j]."','".$dgi_ix."','".($i+1)."','Y',NOW())";
			//echo $sql;
			$db->query($sql);			
	}

	//exit;
	echo("<script>top.location.href = 'gallery.list.php';</script>");
}

if ($act == "delete")
{
	if(is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/")){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/");
	}
	
	$db->query("DELETE FROM deepzoom_gallery_info WHERE event_ix='$event_ix'");
	echo("<script>top.location.href = 'event.list.php';</script>");
	exit;
}


if ($act == "update"){
	
	//print_r($_POST);
	//exit;
	
	//$db->debug = true;
	
	$sql = "update deepzoom_gallery_info set 
					gallery_name='".$gallery_name[1]."',display_type='".$display_type[1]."',insert_yn='Y', use_yn='".$use_yn[1]."',gallery_link='".$gallery_link[1]."',gallery_disp_cnt='".$gallery_disp_cnt[1]."'
					where dgi_ix='".$dgi_ix."'  ";
	$db->query($sql);
		
	$sql = "update deepzoom_gallery_relation set insert_yn='N' where dgi_ix = '".$dgi_ix."' ";
	$db->query($sql);
	for($j=0;$j < count($rpid[1]);$j++){			
		$db->query("Select dgr_ix from deepzoom_gallery_relation where dgi_ix = '".$dgi_ix."' and di_ix = '".$rpid[1][$j]."' ");
	
		if(!$db->total){			
				$sql = "insert into deepzoom_gallery_relation (dgr_ix,di_ix,dgi_ix, vieworder, insert_yn, regdate) values ('','".$rpid[1][$j]."','".($dgi_ix)."','".($j+1)."','Y', NOW())";	
				$db->query($sql);		
		}else{
			$sql = "update deepzoom_gallery_relation set insert_yn = 'Y',vieworder='".($j+1)."' where dgi_ix = '".$dgi_ix."' and di_ix = '".$rpid[1][$j]."' ";
			$db->query($sql);	
		}	
	}
	
	$db->query("delete from deepzoom_gallery_relation where insert_yn = 'N' and dgi_ix = '".$dgi_ix."'");	
	
	
	echo("<script>top.location.href = 'gallery.php?dgi_ix=".$dgi_ix."';</script>");
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
