<?php
	include("$DOCUMENT_ROOT/class/mysql.class");


	$db = new MySQL;

	if ($act == "update"){
		
		//print_r($_POST);
		//exit;
		$db_use_sdate = $db_use_sdate." ".$db_use_stime.":".$db_use_smin.":00";
		$db_use_edate = $db_use_edate." ".$db_use_etime.":".$db_use_emin.":00";

		$sql = "SELECT db_use_sdate , db_use_edate FROM shop_display_brand where db_ix ='$db_ix' ";

		$db->query($sql); //AND cid='$cid2'

		if($db->total){
			$sql = "update shop_display_brand set
					display_type='$display_type',
					div_ix='$div_ix',
					db_title='$db_title',
					brand_max='$brand_max',
					db_use_sdate='$db_use_sdate',
					db_use_edate='$db_use_edate',
					md_mem_ix='$md_code',
					sales_target='$sales_target',
					disp='$disp',				
					cid='$cid2'
					where db_ix='$db_ix'";

			//echo $sql;
			//exit;
			$db->query($sql);

		}else{
			$sql = "SELECT db_ix, db_use_sdate , db_use_edate FROM shop_display_brand where div_ix ='$div_ix' order by db_use_edate desc limit 0,1";
			//echo $sql;
			$db->query($sql); //AND cid='$cid2'
			if($db->total && false){
				$db->fetch();

				if($db->dt[db_use_edate] > $db_use_sdate){

					$set_use_sdate = mktime(0,0,0,substr($db->dt[db_use_edate],4,2),substr($db->dt[db_use_edate],6,2)+1,substr($db->dt[db_use_edate],0,4));
					$set_use_edate = mktime(0,0,0,substr($db->dt[db_use_edate],4,2),substr($db->dt[db_use_edate],6,2)+10,substr($db->dt[db_use_edate],0,4));

					echo("<script>alert('프로모션 상품 노출 시작일자가 ".$db_use_sdate." 일 이후여야 합니다. 시작일자를 ".date("Y-m-d",$set_use_sdate)." 로 설정합니다.');parent.select_date('".date("Y-m-d",$set_use_sdate)."','".date("Y-m-d",$set_use_edate)."',1);	</script>");
					exit;
				}else{
					$db_ix = $db->dt[db_ix];

					$sql = "update shop_display_brand set
					display_type='$display_type',
					div_ix='$div_ix',
					db_title='$db_title',
					brand_max='$brand_max',
					db_use_sdate='$db_use_sdate',
					db_use_edate='$db_use_edate',
					md_mem_ix='$md_code',
					sales_target='$sales_target',
					disp='$disp',
					cid='$cid2'
					where db_ix='$db_ix'";


					$db->query($sql);
				}
			}else{
				$sql = "insert into shop_display_brand
						(db_ix,display_type,div_ix,cid,db_title,brand_max,db_use_sdate,db_use_edate,md_mem_ix,sales_target, disp,regdate)
						values
						('','$display_type','$div_ix','$cid2','$db_title','$brand_max','$db_use_sdate','$db_use_edate','$md_mem_ix','$sales_target','$disp',NOW())";

				$db->sequences = "SHOP_CT_MAIN_GOODS_SEQ";
				$db->query($sql);

				if($db->dbms_type == "oracle"){
					$db_ix = $db->last_insert_id;
				}else{
					$db->query("SELECT db_ix FROM shop_display_brand WHERE db_ix=LAST_INSERT_ID()");
					$db->fetch();
					$db_ix = $db->dt[db_ix];
				}
			}
		}



		$sql = "update shop_display_brand_relation set insert_yn='N' WHERE db_ix='".$db_ix."' ";
		$db->query($sql);

		$sql = "update shop_display_brand_group set insert_yn='N' WHERE db_ix='".$db_ix."' ";
		$db->query($sql);

		$path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/display_banner/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}

		for($i=0;$i < count($group_name);$i++){
			$db->query("Select bg_ix from shop_display_brand_group where group_code = '".($i+1)."' AND db_ix='".$db_ix."' ");

			if($db->total){
				$db->fetch();
				$sql = "update shop_display_brand_group set
								group_name='".$group_name[$i+1]."',
								display_type='".$display_type[$i+1]."',
								insert_yn='Y', use_yn='".$use_yn[$i+1]."',
								group_link='".$group_link[$i+1]."',
								product_cnt='".$product_cnt[$i+1]."',
								goods_display_type='".$goods_display_type[$i+1]."',							
								display_auto_type='".$display_auto_type[$i+1]."'
								where bg_ix='".$db->dt[bg_ix]."' and group_code = '".($i+1)."' AND db_ix='".$db_ix."' ";
				$db->query($sql);
			}else{
					

				$sql = "insert into shop_display_brand_group (bg_ix,group_name,group_code,group_link,display_type,product_cnt, goods_display_type, display_auto_type, insert_yn, use_yn, db_ix, regdate) values('','".$group_name[$i+1]."','".($i+1)."','".$group_link[$i+1]."','".$display_type[$i+1]."','".$product_cnt[$i+1]."','".$goods_display_type[$i+1]."','".$display_auto_type[$i+1]."','Y','".$use_yn[$i+1]."','".$db_ix."',NOW())";
				$db->query($sql);
			}
			if($page_type=="M") $img_pre_text="";
			else $img_pre_text=$page_type."_";
			if ($group_img_del[$i+1] == "Y")
			{
				unlink("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/display_banner/".$db_ix."/display_banner_group_".($i+1).".gif");
			}

			//echo "size:".$_FILES["group_img"]["size"][$i+1]."<br />";
			//print_r($_FILES);
			//exit;
			$path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/display_banner/".$db_ix;
			if(!is_dir($path)){
				mkdir($path, 0777);
				chmod($path, 0777);
			}

			
			if ($_FILES["group_img"]["size"][$i+1] > 0)
			{
				copy($_FILES["group_img"][tmp_name][$i+1], "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/display_banner/".$db_ix."/display_banner_group_".($i+1).".gif");
			}

			if ($group_banner_img_del[$i+1] == "Y")
			{
				unlink("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/display_banner/".$db_ix."/display_banner_group_".($i+1).".gif");
			}
			/*
			if ($_FILES["group_banner_img"]["size"][$i+1] > 0)
			{
				copy($_FILES["group_banner_img"][tmp_name][$i+1], "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/display_banner/display_banner_group_banner_".($i+1).".gif");
			}
			*/

			//$db->query("SELECT mpg_ix FROM shop_display_brand_group WHERE mpg_ix=LAST_INSERT_ID()");
			//$db->fetch();
			//$mpg_ix = $db->dt[0];

			//$db->query("update shop_display_brand_relation set insert_yn = 'N' where main_ix='".$main_ix."' and group_code = '".($i+1)."' ");
			//$db->debug = true;
			$rpid = $rbrand;
			for($j=0;$j < count($rpid[$i+1]);$j++){
				$db->query("Select br_ix from shop_display_brand_relation where group_code = '".($i+1)."' and b_ix = '".$rpid[$i+1][$j]."' AND db_ix='".$db_ix."' ");

				if(!$db->total){
					$sql = "insert into shop_display_brand_relation (br_ix,b_ix,group_code, vieworder, insert_yn, db_ix, regdate) values ('','".$rpid[$i+1][$j]."','".($i+1)."','".($j+1)."','Y', '".$db_ix."', NOW())";
					$db->query($sql);
				}else{
					$sql = "update shop_display_brand_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where group_code = '".($i+1)."' and b_ix = '".$rpid[$i+1][$j]."' AND db_ix='".$db_ix."' ";
					$db->query($sql);
				}
			}

			$db->query("delete from shop_display_brand_relation where group_code = '".($i+1)."' and insert_yn = 'N' AND db_ix='".$db_ix."' ");

			/*
			$db->query("update shop_display_brand_relation set insert_yn = 'N'  where group_code = '".($i+1)."' AND db_ix='".$db_ix."' ");

			for($j=0;$j < count($category[$i+1]);$j++){
				$db->query("Select mcr_ix from shop_display_brand_relation where group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' AND db_ix='".$db_ix."' ");

				if(!$db->total){
					$sql = "insert into shop_display_brand_relation (mcr_ix,cid,group_code, vieworder, insert_yn, db_ix, regdate) values ('','".$category[$i+1][$j]."','".($i+1)."','".($j+1)."','Y', '".$db_ix."', NOW())";
					$db->query($sql);
				}else{
					$sql = "update shop_display_brand_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' AND db_ix='".$db_ix."' ";
					$db->query($sql);
				}
			}

			$db->query("delete from shop_display_brand_relation where group_code = '".($i+1)."' and insert_yn = 'N' AND db_ix='".$db_ix."' ");
			*/

		}


		$db->query("delete from shop_display_brand_relation where insert_yn = 'N' AND db_ix='".$db_ix."' ");
		$db->query("delete from shop_display_brand_group where insert_yn = 'N' AND db_ix='".$db_ix."' ");

		if($delete_cache == "Y"){
			include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php");
			$tpl = new Template_();
			$tpl->caching = true;
			$tpl->cache_dir = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cache/"; 
			
			$tpl->clearCache('000000000000000'); 
		}
		//exit;
		echo("<script>alert('브랜드 전시 정보가 정상적으로 수정되었습니다.');top.location.href = 'brand_display.php?db_ix=".$db_ix."';</script>");

	}

	if ($act == "delete"){
		/*
		$sql="SELECT group_code FROM shop_display_brand_group WHERE db_ix='".$db_ix."' ";
		$db->query($sql);
		$group_cnt=$db->total;
		if($group_cnt>0) {
			$group_fetch=$db->fetchall();
			for($i=0;$i<$group_cnt;$i++) {
				if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display_banner/".$db_ix."/display_banner_group_".($i+1).".gif")) {
					chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display_banner/".$db_ix."/display_banner_group_".($i+1).".gif", 0777);
					rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display_banner/".$db_ix."/display_banner_group_".($i+1).".gif");
				}
			}
		}
		*/
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display_banner/".$db_ix) && $db_ix) {
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display_banner/".$db_ix."/", 0777);
			rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display_banner/".$db_ix."/");
		}

		$db->query("DELETE FROM shop_display_brand WHERE db_ix='".$db_ix."' ");
		$db->query("delete from shop_display_brand_relation where db_ix='".$db_ix."' ");
		$db->query("delete from shop_display_brand_group where db_ix='".$db_ix."' ");

		echo("<script>top.location.href = '/admin/display/brand_display_list.php';</script>");
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
