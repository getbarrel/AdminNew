<?
///////////////////////////////////////////////////////////////////
//
// 제목 : 전시관리 > 배너 등록  : 이현우(2013-05-20)
//
///////////////////////////////////////////////////////////////////
include("../../class/database.class");
$db = new Database;

$banner_div = $_POST["banner_div"];

$FromHH = $_POST["FromHH"];
$FromMI = $_POST["FromMI"];
$ToHH = $_POST["ToHH"];
$ToMI = $_POST["ToMI"];
$FromHH = addZeroByDate($FromHH);
$FromMI = addZeroByDate($FromMI);
$ToHH = addZeroByDate($ToHH);
$ToMI = addZeroByDate($ToMI);
$sdate.= $FromHH.$FromMI;
$edate.=$ToHH.$ToMI;
 
if ($act == "insert")
{
	if (!$div_ix && $srch_div) $div_ix = $srch_div; // 1차분류만 있으면 1차분류값 지정
	if($company_id == ""){
		$company_id = $admininfo[company_id];
	}
	
    if($db->dbms_type == "oracle"){
        $sql = "insert into shop_bannerinfo
			(banner_ix,company_id, banner_name,banner_page,banner_position, banner_link,banner_target,banner_desc,banner_img,banner_width,banner_height,disp,use_sdate, use_edate, regdate, banner_img_on, banner_on_use) values ('','".$company_id."','$banner_name','$banner_page','$banner_position','$banner_link','$banner_target','$banner_desc','".$banner_img_name."','$banner_width','$banner_height','$disp',TO_DATE('".$use_sdate."','MM-DD-YYYY HH24:MI:SS'),TO_DATE('".$use_edate."','MM-DD-YYYY HH24:MI:SS'),NOW(), '".$banner_img_on_name."', '".$banner_on_use."')";
        $db->sequences = "SHOP_BANNERINFO_SEQ";
    }else{        
        $sql = "insert into shop_bannerinfo
			(banner_ix,company_id, banner_name,banner_page,banner_link,banner_target,banner_desc,banner_img,banner_width,banner_height,disp,use_sdate,use_edate,regdate, banner_img_on, banner_on_use) values ('','".$company_id."','$banner_name','$banner_page','$banner_link','$banner_target','$banner_desc','".$banner_img_name."','$banner_width','$banner_height','$disp','".$use_sdate."','".$use_edate."',NOW(), '".$banner_img_on_name."', '".$banner_on_use."')";
    }
	
	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$banner_ix = $db->last_insert_id;
	}else{
		$db->query("SELECT banner_ix FROM shop_bannerinfo WHERE banner_ix=LAST_INSERT_ID()");
		$db->fetch();
		$banner_ix = $db->dt[0];
	}

	// 배너정보 연계테이블 저장
	$sql = "INSERT INTO shop_display_banner (banner_ix, banner_div, div_ix, cid, md_id, goal_cnt, sdate, edate, regdate) VALUES ";
	$sql.= " ($banner_ix, '$banner_div', '$div_ix', '$cid2', '$md_id', '$goal_cnt', '$sdate', '$edate', now())";
	$db->query($sql);

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/",0777);
	}

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/",0777);
	}


	if ($banner_img)
	{
		copy($banner_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/".$banner_img_name);
	}
	if ($banner_img_on)
	{
		copy($banner_img_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/".$banner_img_on_name);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너가  정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='display_banner_list.php?banner_div=".$banner_div."&SubID=$SubID';</script>");
}


if ($act == "update"){

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/",0777);
	}
	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/",0777);
	}

	if ($banner_img)
	{
		copy($banner_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/".$banner_img_name);
	}
	if ($banner_img_on)
	{
		copy($banner_img_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/".$banner_img_on_name);		
	}

	if($banner_img){
		$banner_img_str = ",banner_img='$banner_img_name'";
	}
	if($banner_img_on){
		$banner_img_on_str = ",banner_img_on='$banner_img_on_name'";
	}
	if($admininfo["admin_level"] == 9){
		if($company_id != ""){
			$company_id_str = ", company_id = '".$company_id."' ";
		}
	}	
	if (!$div_ix && $srch_div) $div_ix = $srch_div; // 1차분류만 있으면 1차분류값 지정
    if($db->dbms_type == "oracle"){
        $sql = "update shop_bannerinfo set
					banner_name='$banner_name',
					banner_link='$banner_link',
					banner_target='$banner_target',
					banner_desc='$banner_desc',
					banner_page='$banner_page',
					banner_position='$banner_position',
					banner_width = '$banner_width',
					banner_height = '$banner_height',
					banner_on_use = '$banner_on_use',
					disp = '$disp' 					
					$banner_img_str 
					$banner_img_on_str
					$company_id_str
					where banner_ix='$banner_ix' ";
    }else{
        $sql = "update shop_bannerinfo set
					banner_name='$banner_name',
					banner_link='$banner_link',
					banner_target='$banner_target',
					banner_desc='$banner_desc',
					banner_page='$banner_page',
					banner_position='$banner_position',
					banner_width = '$banner_width',
					banner_height = '$banner_height',
					banner_on_use = '$banner_on_use',
					disp = '$disp' 					 
					$banner_img_str 
					$banner_img_on_str
					$company_id_str
					where banner_ix='$banner_ix' ";
	} 
	 
	$db->query($sql);

	// 배너정보 연계테이블 저장
	$sql = "update shop_display_banner set					
					div_ix = '$div_ix',
					cid = '$cid2',
					md_id='$md_id',
					goal_cnt = '$goal_cnt',
					sdate = '$sdate',
					edate = '$edate',
					mod_date = now()
					where banner_ix='$banner_ix' AND banner_div = '$banner_div' ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너가  정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'display_banner_list.php?banner_div=".$banner_div."&SubID=$SubID';</script>");
}

if ($act == "delete"){

	if($banner_ix && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix")){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix");
	}

	$sql = "delete from shop_bannerinfo where banner_ix='$banner_ix' ";
	$db->query($sql);

	// 배너연계 테이블
	$sql = "delete from shop_display_banner where banner_ix='$banner_ix' AND banner_div='$banner_div'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>top.location.href='display_banner_list.php?banner_div=".$banner_div."';</script>");
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
