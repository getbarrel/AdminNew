<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");


$db = new Database;


$popup_file_size = $_FILES['popup_file']['size'];
$popup_file_name = $_FILES['popup_file']['name'];
$popup_file_tmp = $_FILES['popup_file']['tmp_name'];

$act = $_POST[act];
$popup_ix = $_POST[popup_ix];
$charger_ix = $_POST[charger_ix];
$seller_id = $_POST[seller_id];
$popup_ip = $_POST[popup_ip];
$popup_confirm = $_POST[popup_confirm];


if($act == 'confirm_yn'){

	$db->query("select * from seller_official_popup_result where charger_ix='".$charger_ix."' and popup_ix='".$popup_ix."'");
	$db->total;
	
	if($db->total){

		$sql = "update seller_official_popup_result set
					popup_confirm='$popup_confirm',
					popup_ip='$popup_ip',
					popup_confirm_date=NOW()
				where charger_ix='$charger_ix' and popup_ix='$popup_ix'";
			$db->query($sql);

	} else{

			$sql="insert into
					seller_official_popup_result
						(of_pop_ix, popup_ix, charger_ix, seller_id, popup_confirm, popup_ip, popup_confirm_date)
					values 
						('', '".$popup_ix."', '".$charger_ix."', '".$seller_id."', '".$popup_confirm."', '".$popup_ip."', NOW())";
			$db->query($sql);
	}

	echo "<script>parent.self.close();</script>";
	exit;
}





if(!empty($popup_file_name)){
	$db->query("select popup_file from seller_official_popup where popup_file='".$popup_file_name."'");
	$db->fetch();
	$file_name = $db->dt[popup_file];

	if($db->total){
		echo "<script type='text/javascript'>alert('같은 파일명 존재 - 파일명을 변경해주세요.');</script>";
		echo "<script type='text/javascript'>self.close();</script>";
		exit;
	}
}


if ($act == "update"){

	/*
	$popup_text = $content;
	$popup_use_sdate = $popup_use_sdate." ".$popup_use_sdate_h.":".$popup_use_sdate_i.":".$popup_use_sdate_s;
	$popup_use_edate = $popup_use_edate." ".$popup_use_edate_h.":".$popup_use_edate_i.":".$popup_use_edate_s;
	if($popup_today == 1){
		$popup_height = $popup_height + 30;
	}else{
		$popup_height = $popup_height;
	}
	*/

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/of_pop/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/of_pop/$popup_ix/";

	//if(substr_count($data_text,"<IMG") > 0){
		if(!is_dir($path)){
			mkdir($path, 0777);
			//chmod($path,0777)
		}
	//}

if($popup_status_end == '0'){

	$popup_status = '0';

} else{
	if($popup_use_sdate > date('Y-m-d')){
		$popup_status = '1';
	} elseif($popup_use_sdate <= date('Y-m-d') && $popup_use_edate >= date('Y-m-d')){
		$popup_status = '2';
	} elseif($popup_use_edate < date('Y-m-d')){
		$popup_status = '0';
	}

}


	if ($popup_file_size > 0){

		$ext=end(explode(".",$popup_file_name));
		move_uploaded_file($popup_file_tmp, $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/images/of_pop/$popup_ix/".$popup_ix.".".$ext);
		$sql="UPDATE seller_official_popup SET popup_file='".$popup_file_name."' WHERE popup_ix='".$popup_ix."' ";
		$db->query($sql);
	}


	$sql = "update seller_official_popup set
			popup_title='$popup_title',
			popup_text='$popup_text',
			popup_use_sdate='$popup_use_sdate',
			popup_use_edate='$popup_use_edate',
			popup_width='$popup_width',
			popup_height='$popup_height',
			popup_top='$popup_top',
			popup_left='$popup_left',
			popup_today='$popup_today',
			popup_div='$popup_div',
			popup_status='$popup_status',
			popup_type = '$popup_type'
			where popup_ix='$popup_ix'";
//echo nl2br($sql);
//exit;

	$db->query($sql);

	$data_text = $popup_text;
	$data_text_convert = $popup_text;
	$data_text_convert = str_replace("\\","",$data_text_convert);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);



//print_r($_FILES);
//print_r($_POST);
//exit;
	//$db->debug = true;
//	exit;

	if($_FILES['display_title_img'][size] > 0){
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/".$popup_ix."/";
		copy($_FILES['display_title_img'][tmp_name], $path."pop_dp_title.jpg");
		//$update_str .= ", bd_file_s = '".$_FILES['display_title_img'][name][$key][s]."' ";
	}
	//print_r($_POST);
	
	//$db->debug = true;

	$data_text = str_replace("http://$HTTP_HOST","",$data_text);

	$db->query("UPDATE seller_official_popup SET popup_text = '$data_text' WHERE popup_ix='$popup_ix'");




	//echo("<script>top.location.href = 'popup.write.php?popup_ix=$popup_ix';</script>");
	if($mmode == "pop"){
		echo("<script>parent.opener.document.location.reload();parent.self.close();</script>");
	}else{
		echo("<script>top.location.href = 'popup.list.php';</script>");
	}
}

if ($act == "insert"){

/*
	$popup_text = $content;
	$popup_use_sdate = $popup_use_sdate." ".$popup_use_sdate_h.":".$popup_use_sdate_i.":".$popup_use_sdate_s;
	$popup_use_edate = $popup_use_edate." ".$popup_use_edate_h.":".$popup_use_edate_i.":".$popup_use_edate_s;
	if($popup_today == 1){
		$popup_height = $popup_height + 30;
	}else{
		$popup_height = $popup_height;
	}
*/
	
	if($popup_use_sdate > date('Y-m-d')){
		$popup_status = '1';
	} elseif($popup_use_sdate <= date('Y-m-d') && $popup_use_edate >= date('Y-m-d')){
		$popup_status = '2';
	} elseif($popup_use_edate < date('Y-m-d')){
		$popup_status = '0';
	}
	

	//$db->sequences = "SHOP_POPUP_SEQ";
	$db->query("insert into seller_official_popup
							(popup_ix, popup_div, popup_title,popup_text, popup_width, popup_height, popup_top, popup_left, popup_today, popup_type, 
							popup_status, popup_use_sdate,popup_use_edate, regdate)			
					values('','$popup_div', '$popup_title','$popup_text', '$popup_width','$popup_height','$popup_top','$popup_left','$popup_today','$popup_type', 
							'$popup_status', '$popup_use_sdate','$popup_use_edate', NOW())");

	if($db->dbms_type == "oracle"){
		$popup_ix = $db->last_insert_id;
	}else{
		$db->query("SELECT popup_ix FROM seller_official_popup WHERE popup_ix=LAST_INSERT_ID()");
		$db->fetch();
		$popup_ix = $db->dt[0];
	}

	$data_text = $popup_text;
	$data_text_convert = $popup_text;
	$data_text_convert = str_replace("\\","",$data_text_convert);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);


	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/of_pop/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/of_pop/$popup_ix/";

	//if(substr_count($data_text,"<IMG") > 0){
		if(!is_dir($path)){
			mkdir($path, 0777);
			//chmod($path,0777)
		}
	//}


	if ($popup_file_size > 0){

		$ext=end(explode(".",$popup_file_name));
		move_uploaded_file($popup_file_tmp, $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/images/of_pop/$popup_ix/".$popup_ix.".".$ext);
		$sql="UPDATE seller_official_popup SET popup_file='".$popup_file_name."' WHERE popup_ix='".$popup_ix."' ";
		$db->query($sql);
	}


	$data_text = str_replace("http://$HTTP_HOST","",$data_text);
	$db->query("UPDATE seller_official_popup SET popup_text = '$data_text' WHERE popup_ix='$popup_ix'");
	if($mmode == "pop"){
		echo("<script>parent.opener.document.location.reload();parent.self.close();</script>");
	}else{
		echo("<script>top.location.href = 'popup.list.php';</script>");
	}
}

if ($act == "delete")
{
	if(is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/$popup_ix/") && $popup_ix){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/$popup_ix/");
	}

	$db->query("DELETE FROM seller_official_popup WHERE popup_ix='$popup_ix'");
	echo("<script>top.location.href = 'popup.list.php';</script>");
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

