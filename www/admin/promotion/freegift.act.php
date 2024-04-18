<?
/*
메인프로모션 분류 추가로 인해 이미지명이 중복되어 저장되므로 분류 코드를 이미지명 앞에 붙여서 중복 저장을 막음(기존에 쓰던 방식은 그대로 유지) kbk 13/05/09
*/
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");

$db = new Database;

if ($act == "vieworder_update"){
	//$db->query("update shop_freegift_product_relation set vieworder=1 ");
	//$_erpid = str_replace("\\\"","\"",$_POST["erpid"]);
	//echo $_erpid;
	//echo "bbb:".count(unserialize($_POST["erpid"]))."<br>";
	//print_r(unserialize($_POST["erpid"]));
	//$_erpid = unserialize(urldecode($_erpid));
	//$erpid = unserialize(urldecode($erpid));
	//echo count($_erpid);
	for($i=0;$i < count($sortlist);$i++){
		$sql = "update shop_freegift_product_relation set
			vieworder='".($i+1)."'
			where  pid='".$sortlist[$i]."' ";//main_ix='$main_ix' and

		//echo $sql;
		$db->query($sql);
	}

}

if ($_POST["act"] == "update" || $_POST["act"] == "insert"){
//print_r($_POST);
//exit;
 
	$sql = "SELECT fg_use_sdate , fg_use_edate FROM shop_freegift where fg_ix ='".$_POST["fg_ix"]."' ";

	$db->query($sql); //AND cid='$cid'

	$fg_use_sdate = mktime($_POST["fg_use_sdate_h"],$_POST["fg_use_sdate_i"],$_POST["fg_use_sdate_s"],substr($_POST["fg_use_sdate"],5,2),substr($_POST["fg_use_sdate"],8,2),substr($_POST["fg_use_sdate"],0,4));
	$fg_use_edate = mktime($_POST["fg_use_edate_h"],$_POST["fg_use_edate_i"],$_POST["fg_use_edate_s"],substr($_POST["fg_use_edate"],5,2),substr($_POST["fg_use_edate"],8,2),substr($_POST["fg_use_edate"],0,4));
		//echo $fg_use_edate_h.":".$fg_use_edate_i.":".$fg_use_edate_s;
		//exit;
	//print_r($_POST); 
	//$db->debug = true;
	if($db->total){

		

		$sql = "update shop_freegift set
					mall_ix='".$_POST["mall_ix"]."',
					freegift_event_title='".$_POST["freegift_event_title"]."',
					fg_use_sdate='".$fg_use_sdate."',
					fg_use_edate='".$fg_use_edate."',
					freegift_condition='".$_POST["freegift_condition"]."',
					member_target='".$_POST["member_target"]."',
					md_mem_ix='".$_POST["md_code"]."',
					disp='".$_POST["disp"]."',
					editdate=NOW()
					where fg_ix='".$_POST["fg_ix"]."' ";


		$db->query($sql);

	}else{
		  
			
			$sql = "insert into shop_freegift
						(fg_ix,mall_ix,freegift_event_title,fg_use_sdate,fg_use_edate,freegift_condition,member_target,md_mem_ix,disp,editdate,regdate) 
						values
						('','".$_POST["mall_ix"]."','".$_POST["freegift_event_title"]."','".$fg_use_sdate."','".$fg_use_edate."','".$_POST["freegift_condition"]."','".$_POST["member_target"]."','".$_POST["md_code"]."','".$_POST["disp"]."',NOW(),NOW())";

			$db->sequences = "SHOP_CT_MAIN_GOODS_SEQ";
			$db->query($sql);

			if($db->dbms_type == "oracle"){
				$fg_ix = $db->last_insert_id;
			}else{
				$db->query("SELECT fg_ix FROM shop_freegift WHERE fg_ix=LAST_INSERT_ID() ");
				$db->fetch();
				$fg_ix = $db->dt[fg_ix];
			}
		
	}


	

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	if ($mg_title_img_del == "Y")
	{
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/main_title_img_".$fg_ix.".jpg");
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/main_title_img_".$fg_ix.".jpg");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	}

	if ($_FILES["mg_title_img"]["size"] > 0)
	{
		copy($_FILES["mg_title_img"][tmp_name], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/main_title_img_".$fg_ix.".jpg");		
	}
	$sql = "update shop_freegift_product_relation set insert_yn='N' where fg_ix = '".$fg_ix."'  ";
	$db->query($sql);

	$sql = "update shop_freegift_select_product_relation set insert_yn='N' where fg_ix = '".$fg_ix."'  ";
	$db->query($sql);
/*
	$sql = "update shop_freegift_select_product_except_relation set insert_yn='N' where fg_ix = '".$fg_ix."'  ";
	$db->query($sql);
 */
	$sql = "update shop_freegift_product_group set insert_yn='N' where fg_ix = '".$fg_ix."'  ";
	$db->query($sql);
	
	for($i=0;$i < count($_POST["freegift_group"]);$i++){
		$freegift_group_info = $_POST["freegift_group"][($i+1)];
		//print_r($freegift_group_info);

		$db->query("Select fpg_ix from shop_freegift_product_group where fg_ix = '".$fg_ix."' and group_code = '".($i+1)."' ");

		if($db->total){
			$db->fetch();
			$fpg_ix = $db->dt[fpg_ix];
		 		
			$sql = "update shop_freegift_product_group set						
						group_name='".$freegift_group_info["group_name"]."',
						group_code='".$freegift_group_info["group_code"]."',
						gift_cnt='".$freegift_group_info["gift_cnt"]."',
						sale_condition_s='".$freegift_group_info["sale_condition_s"]."',
						sale_condition_e='".$freegift_group_info["sale_condition_e"]."',
						event_amount_type='".$freegift_group_info["event_amount_type"]."',
						event_amount='".$freegift_group_info["event_amount"]."',
						insert_yn='Y',
						is_display='".$freegift_group_info["is_display"]."',
						editdate=NOW()
						where fpg_ix='".$fpg_ix."'  ";

			$db->query($sql);

			

		}else{ 
			$sql = "insert into shop_freegift_product_group
						(fpg_ix,fg_ix, group_name,group_code,gift_cnt,sale_condition_s,sale_condition_e,event_amount_type,event_amount,insert_yn,is_display,editdate, regdate)
						values
						('','".$fg_ix."','".$freegift_group_info["group_name"]."','".($i+1)."','".$freegift_group_info["gift_cnt"]."','".$freegift_group_info["sale_condition_s"]."','".$freegift_group_info["sale_condition_e"]."','".$freegift_group_info["event_amount_type"]."','".$freegift_group_info["event_amount"]."','Y','".$freegift_group_info["is_display"]."',NOW(),NOW()) ";

			$db->sequences = "SHOP_FREEGIFT_PRODUCT_GROUP_SEQ";
			$db->query($sql);

			$db->query("SELECT fpg_ix FROM  shop_freegift_product_group  WHERE fpg_ix=LAST_INSERT_ID()");
			$db->fetch();
			$fpg_ix = $db->dt[fpg_ix];
		}

		//$db->debug = true;
		//print_r($_POST);
		//exit;
		 
		//$db->debug = false;
		//exit;

		if ($group_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/freegift_group".($i+1).".gif");
			@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/".$fg_ix."_freegift_group".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}



		//echo "size:".$_FILES["group_img"]["size"][$i+1]."<br />";
		//print_r($_FILES);
		//exit;

		if ($_FILES["group_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/freegift_group".($i+1).".gif");
			@copy($_FILES["group_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/".$fg_ix."_freegift_group".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		if ($group_banner_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/freegift_groupbanner_".($i+1).".gif");
			@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/".$fg_ix."_freegift_groupbanner_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		if ($_FILES["group_banner_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_banner_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/freegift_groupbanner_".($i+1).".gif");
			@copy($_FILES["group_banner_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/".$fg_ix."_freegift_groupbanner_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		if($member_target == "G" || $member_target == "M"){
				if($member_target == "G"){
					$member_target_str = "group";	
				}else if($member_target == "M"){
					$member_target_str = "member";
				}


				$db->query("update shop_freegift_display_relation set insert_yn = 'N'  where member_target = '".$member_target."' and fg_ix = '".$fg_ix."'  ");

				for($j=0;$j < count($selected_result[$member_target_str]);$j++){
					$db->query("select fdr_ix from shop_freegift_display_relation where member_target = '".$member_target."' and r_ix = '".$selected_result[$member_target_str][$j]."' and fg_ix = '".$fg_ix."'  ");

					if(!$db->total){
						$sql = "insert into shop_freegift_display_relation 
									(fdr_ix,fg_ix , r_ix,member_target,  vieworder, insert_yn, regdate) 
									values 
									('','".$fg_ix."','".$selected_result[$member_target_str][$j]."','".$member_target."','".($j+1)."','Y', NOW())";
						$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
						$db->query($sql);
					}else{
						$sql = "update shop_freegift_display_relation set insert_yn = 'Y',vieworder='".($j+1)."', member_target = '".$member_target."' 
									where member_target = '".$member_target."' and r_ix = '".$selected_result[$member_target_str][$j]."' and fg_ix = '".$fg_ix."'  ";
						$db->query($sql);
					}
				}

				$db->query("delete from shop_freegift_display_relation where member_target = '".$member_target."' and insert_yn = 'N' and fg_ix = '".$fg_ix."'  ");
		}

		//$db->query("SELECT fpg_ix FROM ".tbl_shop_freegift_product_group." WHERE fpg_ix=LAST_INSERT_ID()");
		//$db->fetch();
		//$fpg_ix = $db->dt[0];

		//$db->query("update shop_freegift_product_relation set insert_yn = 'N' where main_ix='".$main_ix."' and group_code = '".($i+1)."' ");

		for($j=0;$j < count($fpid[$i+1]);$j++){
			$db->query("select fpr_ix from shop_freegift_product_relation where fg_ix = '".$fg_ix."' and group_code = '".($i+1)."' and pid = '".$fpid[$i+1][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_freegift_product_relation (fpr_ix,pid,fg_ix, group_code, vieworder, insert_yn, regdate) values ('','".$fpid[$i+1][$j]."','".$fg_ix."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_freegift_product_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where fg_ix = '".$fg_ix."' and group_code = '".($i+1)."' and pid = '".$fpid[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_freegift_product_relation where fg_ix = '".$fg_ix."' and group_code = '".($i+1)."' and insert_yn = 'N' ");


		for($j=0;$j < count($rpid[$i+2]);$j++){
			$db->query("select fpr_ix from shop_freegift_select_product_relation where fg_ix = '".$fg_ix."' and group_code = '".($i+2)."' and pid = '".$rpid[$i+2][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_freegift_select_product_relation (fpr_ix,pid,fg_ix, group_code, vieworder, insert_yn, regdate) values ('','".$rpid[$i+2][$j]."','".$fg_ix."','".($i+2)."','".($j+1)."','Y', NOW())";

				$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_freegift_select_product_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+2)."' where fg_ix = '".$fg_ix."' and group_code = '".($i+2)."' and pid = '".$rpid[$i+2][$j]."' ";

				$db->query($sql);
			}
		}

		$db->query("delete from shop_freegift_select_product_relation where fg_ix = '".$fg_ix."' and group_code = '".($i+2)."' and insert_yn = 'N' ");


		for($j=0;$j < count($rpid[$i+3]);$j++){
			$db->query("select fpr_ix from shop_freegift_select_product_relation where fg_ix = '".$fg_ix."' and group_code = '".($i+3)."' and pid = '".$rpid[$i+3][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_freegift_select_product_relation (fpr_ix,pid,fg_ix, group_code, vieworder, insert_yn, regdate) values ('','".$rpid[$i+3][$j]."','".$fg_ix."','".($i+3)."','".($j+1)."','Y', NOW())";

				$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_freegift_select_product_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+3)."' where fg_ix = '".$fg_ix."' and group_code = '".($i+3)."' and pid = '".$rpid[$i+3][$j]."' ";

				$db->query($sql);
			}
		}

		$db->query("delete from shop_freegift_select_product_relation where fg_ix = '".$fg_ix."' and group_code = '".($i+3)."' and insert_yn = 'N' ");


		$db->query("update shop_freegift_category_relation set insert_yn = 'N'  where fg_ix = '".$fg_ix."' ");

		for($j=0;$j < count($selected_result[category]);$j++){
			$db->query("select idx from shop_freegift_category_relation where fg_ix = '".$fg_ix."' and cid = '".$selected_result[category][$j]."'  ");

			if(!$db->total){
				$sql = "insert into shop_freegift_category_relation 
							(idx,fg_ix , cid, insert_yn, regdate) 
							values 
							('','".$fg_ix."','".$selected_result[category][$j]."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_freegift_category_relation set insert_yn = 'Y' 
							where fg_ix = '".$fg_ix."' and cid = '".$selected_result[category][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_freegift_category_relation where fg_ix = '".$fg_ix."' and insert_yn = 'N'  ");

		//	exit;
	}
 
	$db->query("delete from shop_freegift_product_relation where fg_ix = '".$fg_ix."' and insert_yn = 'N' ");
	$db->query("delete from shop_freegift_product_group where fg_ix = '".$fg_ix."' and insert_yn = 'N' ");

	if($delete_cache == "Y"){
		include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php");
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cache/";

		$tpl->clearCache('000000000000000');
	}
//exit;
	echo("<script>top.location.href = 'freegift.php?fg_ix=$fg_ix';</script>");

} else if ($act == "delete"){//삭제 추가 kbk 13/11/21
	$sql="SELECT group_code FROM shop_freegift_product_group WHERE fg_ix='".$fg_ix."' ";
	$db->query($sql);
	$group_cnt=$db->total;
	if($group_cnt>0) {
		$group_fetch=$db->fetchall();
		for($i=0;$i<$group_cnt;$i++) {
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/freegift_group".($i+1).".gif")) {
				chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/freegift_group".($i+1).".gif", 0777);
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/freegift/freegift_group".($i+1).".gif");
			}
		}
	}
	$db->query("delete from shop_freegift where fg_ix='".$fg_ix."' ");
	$db->query("delete from shop_freegift_product_relation where fg_ix = '".$fg_ix."' ");
	$db->query("delete from shop_freegift_product_group where fg_ix = '".$fg_ix."' ");
	$db->query("delete from shop_freegift_display_relation where fg_ix = '".$fg_ix."' ");



	echo("<script>top.location.href = 'freegift_list.php';</script>");
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