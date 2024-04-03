<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

if ($act == "vieworder_update")
{
	

	//$db->query("update MALLSTORY_RECOMMEND_PRODUCT_RELATION set vieworder=1 ");
	//$_erpid = str_replace("\\\"","\"",$_POST["erpid"]);
	//echo $_erpid;
	//echo "bbb:".count(unserialize($_POST["erpid"]))."<br>";
	//print_r(unserialize($_POST["erpid"]));
	//$_erpid = unserialize(urldecode($_erpid));
	//$erpid = unserialize(urldecode($erpid));
	//echo count($_erpid);
	for($i=0;$i < count($sortlist);$i++){
		$sql = "update shop_recommend_product_relation set 
			vieworder='".($i+1)."'
			where md_ix='$md_ix' and pid='".$sortlist[$i]."' ";//md_ix='$md_ix' and 

		//echo $sql;
		$db->query($sql);
	}
	
}

if ($act == "update")
{
	
	$md_use_sdate = $FromYY.$FromMM.$FromDD;
	$md_use_edate = $ToYY.$ToMM.$ToDD;

	

	$sql = "update shop_recommend set 
			md_title='$md_title',md_use_sdate='$md_use_sdate',md_use_edate='$md_use_edate',disp='$disp',div_ix='$div_ix'
			where md_ix='$md_ix'";

	
	$db->query($sql);
	
	
	$db->query("update shop_recommend_product_relation set insert_yn = 'N' where md_ix='".$md_ix."'  ");	
	
	for($j=0;$j < count($rpid[1]);$j++){			
			$db->query("Select md_ix from shop_recommend_product_relation where md_ix='".$md_ix."' and pid = '".$rpid[1][$j]."' ");
		
			if(!$db->total){			
					$sql = "insert into shop_recommend_product_relation (rpr_ix,pid,md_ix, vieworder, insert_yn, regdate) values ('','".$rpid[1][$j]."','".$md_ix."','".($j+1)."','Y', NOW())";	
					$db->query($sql);		
			}else{
				$sql = "update shop_recommend_product_relation set insert_yn = 'Y',vieworder='".($j+1)."' where md_ix='".$md_ix."' and pid = '".$rpid[1][$j]."' ";
				$db->query($sql);	
			}	
		}
		
		$db->query("delete from shop_recommend_product_relation where md_ix='".$md_ix."' and insert_yn = 'N' ");	
		

	
	
	echo("<script>top.location.href = 'hot.write.php?md_ix=$md_ix';</script>");
}

if ($act == "insert"){
	
	$md_use_sdate = $FromYY.$FromMM.$FromDD;
	$md_use_edate = $ToYY.$ToMM.$ToDD;
	$db->query("insert into shop_recommend(div_ix,md_title,md_use_sdate,md_use_edate,disp,regdate) values('$div_ix','$md_title','$md_use_sdate','$md_use_edate','$disp',NOW())");
	$db->query("select md_ix from shop_recommend where md_ix = LAST_INSERT_ID()");
	$db->fetch();
	$md_ix = $db->dt[0];
	for($j=0;$j < count($rpid[1]);$j++){			
		$sql = "insert into shop_recommend_product_relation (rpr_ix,pid,md_ix, vieworder, insert_yn, regdate) values ('','".$rpid[1][$j]."','".$md_ix."','".($j+1)."','N', NOW())";	
		$db->query($sql);		
		
	}

	echo("<script>top.location.href = 'hot_stuff.php';</script>");
}

if ($act == "delete")
{
	
	
	$db->query("DELETE FROM shop_recommend WHERE md_ix='$md_ix'");
	echo("<script>top.location.href = 'hot_stuff.php';</script>");
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
