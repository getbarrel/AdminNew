<?
print_r($_POST);
print_r($_GET);
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include_once("../lib/imageResize.lib.php");
include("cms.lib.php");

$db = new Database;

if ($act == "insert"){
	//echo $_FILES[data_file][name]."::".strrchr($_FILES[data_file][name],".");
	

	if(!$group_ix){
		$group_ix = $parent_group_ix;
	}

	if(!$chk_data){
		$chk_data = '0';
	}

	$db->query("insert into cms_data (di_ix,group_ix,data_name,data_file, chk_data, charger_ix, regdate) values('','$group_ix','$data_name','".$_FILES[data_file][name]."','".$chk_data."','".$admininfo[charger_ix]."',NOW()) ");
	$db->query("SELECT di_ix FROM cms_data WHERE di_ix=LAST_INSERT_ID()");
	$db->fetch();
	$di_ix = $db->dt[di_ix];


	if ($_FILES["data_file"][size] > 0){
		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/cms/";
		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}

		/*
		$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/cms/$di_ix/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}
		*/		


		$path .= $di_ix."/";

		if(!is_dir($path)){
			mkdir($path, 0777);
			chmod($path,0777);
		}else{
			chmod($path,0777);
		}
	}
	
	if ($_FILES["data_file"][size] > 0){
		$ext = str_replace(".","",strrchr($_FILES[data_file][name],"."));
		
		if(in_array($ext,$support_file["image"])){
			$image_info = getimagesize ($_FILES[data_file][tmp_name]);	
			//print_r($image_info);
			//exit;
			$image_type = substr($image_info['mime'],-3);	
			//echo $image_type;
			//exit;
	//		$target_path = $path."/".iconv('UTF-8','EUC-KR',$_FILES[data_file][name]);
	//		$target_tumb_path = $path."/s_".iconv('UTF-8','EUC-KR',$_FILES[data_file][name]);

			$target_path = $path."/".$_FILES[data_file][name];
			$target_tumb_path = $path."/s_".$_FILES[data_file][name];


			move_uploaded_file($_FILES[data_file][tmp_name], $target_path);
			chmod($target_path,0777);

			
			if($image_type == "gif"){	
				MirrorGif($target_path, $target_tumb_path, MIRROR_NONE);
				resize_gif($target_tumb_path,100,100);
			}else{
				Mirror($target_path, $target_tumb_path, MIRROR_NONE);
				resize_jpg($target_tumb_path,100,100);

				//echo "chk_data:".$chk_data;
				if($chk_data == 1){
					$client = new SoapClient("http://".$_SERVER["HTTP_HOST"]."/VESAPI/VESAPIWS.asmx?wsdl=0");
					//print_r($client);
					//echo "http://".$_SERVER["HTTP_HOST"]."/VESAPI/VESAPIWS.asmx?wsdl=0";
					$params = new stdClass();
					//echo $target_path;
					$params->inputPhysicalPathString = $target_path;
					$params->outputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/cms/".$di_ix;
					
				//	echo $params->inputPhysicalPathString."<br>";
				//	echo $params->outputPhysicalPathString;

					$response = $client->TilingWithPhysicalPath($params);
					//echo $response;
				}
			}
		}else{
			$target_path = $path."/".$_FILES[data_file][name];
			$target_tumb_path = $path."/s_".$_FILES[data_file][name];


			move_uploaded_file($_FILES[data_file][tmp_name], $target_path);
			chmod($target_path,0777);
		}

		
	}
	

	echo("<script>parent.document.location.reload();</script>");
}

if ($act == "delete"){
	//echo "delete from work_tmp where  wt_ix ='$wt_ix' ";
	if($di_ix && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/cms/".$di_ix)){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/cms/".$di_ix);
	}

	$db->query("delete from cms_data where  di_ix ='$di_ix' ");
	//echo("<script>alert('정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>");

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


?>