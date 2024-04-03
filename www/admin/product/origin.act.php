<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include('../../include/xmlWriter.php');
$db = new Database;
$db2 = new Database;
session_start();

/*
if ($disp != 1){
	$disp = 0;common_origin
}
*/
//print_r($_POST);
//exit;

if($act == "checkOrginCode"){
	if($origin_code == ""){
		$result[bool] = false;
		$result[message] = "원산지 코드를 입력해주세요 ";
		echo json_encode($result);
		exit;
	}

	$sql = "select * from common_origin where origin_code = '".$origin_code."' ";
	$db->query($sql);

	if($db->total){
		$result[bool] = false;
		$result[message] = "'".$origin_code."'는 이미 사용중인  코드입니다.";
	}else{
		$result[bool] = true;
		$result[message] = "사용하실수 있는 코드입니다.";
	}

	echo json_encode($result);
	exit;
}

if($update_kind == "bd_category"){	//브랜드분류 변경
	if($update_type == "2"){//선택한 회원
		if($od_ix){
			if(count($cpid) > 0){
				for($i=0;$i<count($cpid);$i++){
					$sql = "update common_origin  set od_ix = '".$od_ix."' where og_ix = '".$cpid[$i]."'";
					$db->query($sql);
				}	
			}
		}

		echo "
		<Script Language='Javascript'>
		parent.document.location.reload();
		</Script>";
	}
}


if ($mode == 'insert')
{
	if($od_ix == ""){
		$od_ix = $parent_od_ix;
	}else{
		$od_ix = $od_ix;
	}

	//[S] 원산지 파일 업로드 추가	
	if ($_FILES["origin_file"][size] > 0) {
		$allowExt = array("jpeg","jpg","gif","png");
		$fileType = pathinfo($_FILES["origin_file"]["name"],PATHINFO_EXTENSION);
		if (!in_array(strtolower($fileType), $allowExt)) {
			echo "<script>alert('허용되지 않은 파일 확장자입니다.');</script>";
			exit;
		}
	}
	 
	//[E] 원산지 파일 업로드 추가

	$sql = "INSERT INTO common_origin 
				(og_ix,  od_ix, origin_code,origin_name,origin_name_division, disp, search_disp, shotinfo, regdate) 
				values
				('','$od_ix','$origin_code', '$origin','$origin_name_division', '$disp', '$search_disp','$shotinfo',now()) ";
	
	$db->sequences = "SHOP_BRAND_SEQ";
	$db->query($sql);

	if($db->dbms_type=='oracle'){
		$LAST_ID = $db->last_insert_id;
	}else{
		$db->query("SELECT og_ix FROM common_origin WHERE og_ix=LAST_INSERT_ID()");
		$db->fetch();
		$LAST_ID = $db->dt[0];
	}
	
	updateOriginsXML();


	//[S] 원산지 파일 업로드 추가
	if ($_FILES["origin_file"][size] > 0) {
		$file_name = UploadFile($LAST_ID, $_FILES["origin_file"],"origin", "");
		if($file_name == ""){
			echo "<script>alert('이미지가 정상적으로 저장되지 않았습니다.');</script>";
		}
	}
	//[E] 원산지 파일 업로드 추가
	

	if($mmode == "pop"){
	echo "<Script Language='Javascript'>
		parent.document.location.reload();
		</Script>";
	}else{
		echo "
		<Script Language='Javascript'>
		parent.document.location.href='./origin_list.php?mmode=$mmode';
		</Script>";
		//header("Location:origin.php?mmode=$mmode");
	}
}


if ($mode == "update"){
	
	if($od_ix == ""){
		$od_ix = $parent_od_ix;
	}else{
		$od_ix = $od_ix;
	}
	
	$sql = "UPDATE common_origin SET
			od_ix = '$od_ix', 
			origin_name = '$origin', 
			origin_code = '$origin_code', 
			origin_name_division = '$origin_name_division', 
			disp = '$disp', 					
			search_disp = '$search_disp', 
			shotinfo ='$shotinfo'
			WHERE og_ix='$og_ix'";
	
	$db->query($sql);

	updateOriginsXML();

	//[S] 원산지 파일 업로드 추가	
	if ($_FILES["origin_file"]) {
		$allowExt = array("jpeg","jpg","gif","png");
		$fileType = pathinfo($_FILES["origin_file"]["name"],PATHINFO_EXTENSION);
		if (!in_array(strtolower($fileType), $allowExt)) {
			echo "<script>alert('허용되지 않은 파일 확장자입니다.');</script>";
			exit;
		}
		$file_name = UploadFile($og_ix, $_FILES["origin_file"],"origin", "");
		if($file_name == ""){
			echo "<script>alert('이미지가 정상적으로 저장되지 않았습니다.');</script>";
		}
	}
	//[E] 원산지 파일 업로드 추가

	if($mmode == "pop"){
	echo "<Script Language='Javascript'>		
		parent.document.location.reload();
		</Script>";
	}else{
		echo "
		<script language='javascript' src='../js/message.js.php'></script><Script Language='Javascript'>
		show_alert('정상적으로 수정되었습니다.');
		parent.document.location.reload();
		</Script>";
		//header("Location:origin.php?mmode=$mmode");
	}
}

if ($mode == "image_delete")
{
	if ($imagetype == "originimg"){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/origin/$og_ix/origin_".$og_ix.".gif");
	}

	if ($imagetype == "origin_banner_img"){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/origin/$og_ix/b_origin_".$og_ix.".gif");
	}

	echo "
		<script language='javascript' src='../js/message.js.php'></script><Script Language='Javascript'>
		show_alert('이미지가 정상적으로 삭제되었습니다.');
		parent.document.location.reload();
		</Script>";
}
if ($mode == "delete"){

	if ($og_ix != "" && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/origin/$og_ix/"))
	{
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/origin/$og_ix/");
	}

	$db->query("DELETE FROM common_origin WHERE og_ix='$og_ix'");
	updateOriginsXML();

	DeleteFile($og_ix,"origin","");

	if($mmode == "pop"){
	echo "<Script Language='Javascript'>		
		parent.document.location.reload();
		</Script>";
	}else{
		echo "
		<Script Language='Javascript'>
		parent.document.location.reload();
		</Script>";
	}
}


if($mode == "select_depth2"){
	if($od_ix){
		$sql = "select * from common_origin_div where od_ix = '".$od_ix."' ";
		$db2->query($sql);
		$db2->fetch();
		$div1_name =  $db2->dt[div_name];

		$sql = "select * from common_origin_div where parent_od_ix='".$od_ix."'";
		$db->query($sql);
		$data_array = $db->fetchall();

		for($i=0;$i<count($data_array);$i++){
			$brand_info[$data_array[$i][od_ix]] = $div1_name." > ".$data_array[$i][div_name];
		}

		$datas = $brand_info;
		$datas = json_encode($datas);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;

	}

}




function OriginListSelect($origin, $cid, $return_type ="")
{
//global $db;

	$mdb = new Database;

	$mdb->query("SELECT * FROM common_origin where disp=1");
	
	$bl = "<Select name='origin' class=small>";
	if ($mdb->total == 0)	{
		$bl = $bl."<Option>등록된 브랜드가 없습니다.</Option>";
	}else{
		if($return_type == ""){
			$bl = $bl."<Option value=''>브랜드 선택</Option>";
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($origin == $mdb->dt[og_ix])
				{
					$strSelected = "Selected";
				}else{
					$strSelected = "";
				}

				$bl = $bl."<Option value='".$mdb->dt[og_ix]."' $strSelected>".$mdb->dt[origin_name]."</Option>";

			}
		}else{
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($origin == $mdb->dt[og_ix]){
					return $mdb->dt[origin_name];
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


function updateOriginsXML(){

	global $DOCUMENT_ROOT, $admin_config;

	$xml = new XmlWriter_();
	$mdb = new Database;

	$mdb->query("select * from common_origin where disp=1 ");
	$origins = $mdb->fetchall();

	$xml->push('origins');


	foreach ($origins as $origin) {
		//$xml->push('shop', array('species' => $animal[0]));
		$xml->push('origin', array('cid' => $origin[cid], 'top_cid' => substr($origin[cid],0,3)));
		$xml->element('og_ix', $origin[og_ix]);
		$xml->element('origin_name', $origin[origin_name]);
		$xml->element('origin_link', "/event/goods_origin.php?og_ix=".$origin[og_ix]."&cid=".$origin[cid]);
		$xml->pop();
	}

	$xml->pop();
	//print $xml->getXml();

	$dirname = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];

	$fp = fopen($dirname."/origins.xml","w");
	fputs($fp, $xml->getXml());
	fclose($fp);
}


function UploadFile($code, $file, $prefix = "", $suffix="")
{	
	global $admin_config;
	if(!file_exists($_SERVER['DOCUMENT_ROOT'].$admin_config["mall_data_root"]."/images/origin/")){
		mkdir($_SERVER['DOCUMENT_ROOT'].$admin_config["mall_data_root"]."/images/origin/");
		chmod($_SERVER['DOCUMENT_ROOT'].$admin_config["mall_data_root"]."/images/origin/",0777);
	}

	$fileType = pathinfo($file[name],PATHINFO_EXTENSION);

	$target_dir = $_SERVER["DOCUMENT_ROOT"].$admin_config["mall_data_root"]."/images/origin/";
	$filename = ($prefix ? "{$prefix}_" : "") . $code . ($suffix ? "_{$suffix}" : "") . "." ."gif";
	$target_file = $target_dir .$filename;

	if (move_uploaded_file($file["tmp_name"], $target_file)) {
		return $filename;
	} else {
		return '';
	}
	
}

function DeleteFile($code,$prefix = "", $suffix="")
{
	global $admin_config;

	if(file_exists( $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/origin/".($prefix ? "{$prefix}_" : "") . $code . ($suffix ? "_{$suffix}" : "").".gif" )){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/origin/".($prefix ? "{$prefix}_" : "") . $code . ($suffix ? "_{$suffix}" : "").".gif");
	}
}

?>