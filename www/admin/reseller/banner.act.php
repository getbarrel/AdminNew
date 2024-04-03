<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert")
{



	$sql = "insert into reseller_banner(rsl_banner_ix,banner_name,banner_link,banner_target,banner_desc,banner_img,banner_width,banner_height,regdate)
					values('$rsl_banner_ix','$banner_name','$banner_link','$banner_target','$banner_desc','".$banner_img_name."','$banner_width','$banner_height',NOW())";
	$db->query($sql);

	$db->query("SELECT rsl_banner_ix FROM reseller_banner WHERE rsl_banner_ix=LAST_INSERT_ID()");
	$db->fetch();
	$rsl_banner_ix = $db->dt[0];

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/",0777);
	}

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$rsl_banner_ix/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$rsl_banner_ix/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$rsl_banner_ix/",0777);
	}

	
	if ($banner_img)
	{
		copy($banner_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$rsl_banner_ix/".$banner_img_name);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너가  정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='banner.php?SubID=$SubID';</script>");
}


if ($act == "update"){

		if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/")){
			mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/");
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/",0777);
		}
		if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$rsl_banner_ix/")){
			mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$rsl_banner_ix/");
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$rsl_banner_ix/",0777);
		}

		if ($banner_img)
		{
			copy($banner_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$rsl_banner_ix/".$banner_img_name);
		}
		if($banner_img){
			$banner_img_str = ",banner_img='$banner_img_name'";
		}

		$sql = "update reseller_banner set
						banner_name='$banner_name',banner_link='$banner_link',banner_target='$banner_target',banner_desc='$banner_desc',banner_width = '$banner_width',banner_height = '$banner_height'  $banner_img_str
						where rsl_banner_ix='$rsl_banner_ix' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너가  정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'banner.php?SubID=$SubID';</script>");
}

if ($act == "delete"){

	if($rsl_banner_ix && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$rsl_banner_ix")){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$rsl_banner_ix");
	}

	$sql = "delete from reseller_banner where rsl_banner_ix='$rsl_banner_ix' ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>document.location.href='banner.php?SubID=$SubID';</script>");
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
