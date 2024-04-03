<?
/*
메인프로모션 분류 추가로 인해 이미지명이 중복되어 저장되므로 분류 코드를 이미지명 앞에 붙여서 중복 저장을 막음(기존에 쓰던 방식은 그대로 유지) kbk 13/05/09
*/
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");

$db = new Database;

if ($act == "vieworder_update"){
	//$db->query("update shop_promotion_product_relation set vieworder=1 ");
	//$_erpid = str_replace("\\\"","\"",$_POST["erpid"]);
	//echo $_erpid;
	//echo "bbb:".count(unserialize($_POST["erpid"]))."<br>";
	//print_r(unserialize($_POST["erpid"]));
	//$_erpid = unserialize(urldecode($_erpid));
	//$erpid = unserialize(urldecode($erpid));
	//echo count($_erpid);
	for($i=0;$i < count($sortlist);$i++){
		$sql = "update shop_promotion_product_relation set
			vieworder='".($i+1)."'
			where  pid='".$sortlist[$i]."' ";//promotion_ix='$promotion_ix' and

		//echo $sql;
		$db->query($sql);
	}

}

if ($act == "update" || $act == "insert"){
	
	$unix_timestamp_sdate = mktime($pg_use_sdate_h,$pg_use_sdate_i,$pg_use_sdate_s,substr($pg_use_sdate,5,2),substr($pg_use_sdate,8,2),substr($pg_use_sdate,0,4));
	$unix_timestamp_edate = mktime($pg_use_edate_h,$pg_use_edate_i,$pg_use_edate_s,substr($pg_use_edate,5,2),substr($pg_use_edate,8,2),substr($pg_use_edate,0,4));

	if(!$div_code){
		$sql = "SELECT div_code FROM shop_promotion_div where div_ix ='$div_ix' ";
		$db->query($sql); //AND cid='$cid'
		$db->fetch();
		$div_code = $db->dt[div_code];
	}

	//$db->debug = true;
	
	$sql = "SELECT pg_use_sdate , pg_use_edate FROM shop_promotion_goods where pg_ix ='$pg_ix' ";
	$db->query($sql); //AND cid='$cid'
/*
	$sql = "SELECT pg_ix, pg_use_sdate , pg_use_edate 
					FROM shop_promotion_goods where div_ix ='".$div_ix."' 
					order by pg_use_edate desc 
					limit 0,1";
	//echo $sql;
	$db->query($sql); //AND cid='$cid'
	if($db->total){
		$db->fetch();
		//echo $db->dt[pg_use_edate] .">". $unix_timestamp_sdate."<br><br>";
		//exit;
		if($db->dt[pg_use_edate] > $unix_timestamp_sdate){

			$set_use_sdate = $db->dt[pg_use_sdate];//mktime(0,0,0,substr($db->dt[pg_use_edate],4,2),substr($db->dt[pg_use_edate],6,2)+1,substr($db->dt[pg_use_edate],0,4));
			$set_use_edate = $db->dt[pg_use_edate];//mktime(0,0,0,substr($db->dt[pg_use_edate],4,2),substr($db->dt[pg_use_edate],6,2)+10,substr($db->dt[pg_use_edate],0,4));
		//echo date("Y-m-d",$set_use_sdate);
			echo("<script language='javascript'>alert('프로모션 상품 노출 시작일자가 ".date("Y-m-d H:i:00", $db->dt[pg_use_edate])." 일 이후여야 합니다. 시작일자를 ".date("Y-m-d",$set_use_sdate)." 로 설정합니다.');parent.select_date('".date("Y-m-d",$set_use_sdate)."','".date("Y-m-d",$set_use_edate)."',1);	</script>");
			exit;
		}
	}
	*/

	if($db->total){
		//echo $pg_use_sdate."<br>";
		//echo substr($pg_use_sdate,5,2);
		

		$sql = "update shop_promotion_goods set
				mall_ix='$mall_ix',
				pg_title='$pg_title',
				goods_max='$goods_max',
				image_width='$image_width',
				image_height='$image_height',
				pg_use_sdate='$unix_timestamp_sdate',
				pg_use_edate='$unix_timestamp_edate',
				md_mem_ix='".$md_code."',
				goal_amount='$goal_amount',
				disp='$disp',
				div_ix='$div_ix',
				cid='$cid'
				where pg_ix='$pg_ix'";


		$db->query($sql);

	}else{
		
		/*
				$pg_ix = $db->dt[pg_ix];

				$sql = "update shop_promotion_goods set
				pg_title='$pg_title',
				goods_max='$goods_max',
				image_width='$image_width',
				image_height='$image_height',
				pg_use_sdate='$pg_use_sdate',
				pg_use_edate='$pg_use_edate',
				md_mem_ix='".$md_code."',
				goal_amount='$goal_amount',
				disp='$disp',
				div_ix='$div_ix',
				cid='$cid'
				where pg_ix='$pg_ix'";


				$db->query($sql);
			}
		
		}else{
			*/
			$unix_timestamp_sdate = mktime($pg_use_sdate_h,$pg_use_sdate_i,$pg_use_sdate_s,substr($pg_use_sdate,5,2),substr($pg_use_sdate,8,2),substr($pg_use_sdate,0,4));
			$unix_timestamp_edate = mktime($pg_use_edate_h,$pg_use_edate_i,$pg_use_edate_s,substr($pg_use_edate,5,2),substr($pg_use_edate,8,2),substr($pg_use_edate,0,4));

			$sql = "insert into shop_promotion_goods
					(pg_ix,mall_ix,agent_type, div_ix,cid,pg_title,goods_max,image_width, image_height,pg_use_sdate,pg_use_edate,md_mem_ix, goal_amount, disp, regdate)
					values
					('','$mall_ix','$agent_type','$div_ix','$cid','$pg_title','$goods_max','$image_width','$image_height','$unix_timestamp_sdate','$unix_timestamp_edate','".$md_code."','$goal_amount','$disp',NOW())";

			$db->sequences = "SHOP_CT_PROMOTION_GOODS_SEQ";
			$db->query($sql);

			if($db->dbms_type == "oracle"){
				$pg_ix = $db->last_insert_id;
			}else{
				$db->query("SELECT pg_ix FROM shop_promotion_goods WHERE pg_ix=LAST_INSERT_ID()");
				$db->fetch();
				$pg_ix = $db->dt[pg_ix];
			}
		
	}


	$sql = "update shop_promotion_product_relation set insert_yn='N' where pg_ix = '".$pg_ix."'  ";
	$db->query($sql);

	$sql = "update shop_promotion_product_group set insert_yn='N' where pg_ix = '".$pg_ix."'  ";
	$db->query($sql);

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	if ($pg_title_img_del == "Y")
	{
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/promotion_title_img_".$pg_ix.".jpg");
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/promotion_title_img_".$pg_ix.".jpg");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	}

	if ($_FILES["pg_title_img"]["size"] > 0)
	{
		copy($_FILES["pg_title_img"][tmp_name], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/promotion_title_img_".$pg_ix.".jpg");		
	}

	for($i=0;$i < count($group_name);$i++){
		$db->query("Select ppg_ix from shop_promotion_product_group where pg_ix = '".$pg_ix."' and group_code = '".($i+1)."' ");

		if($db->total){
			$db->fetch();
			$sql = "update shop_promotion_product_group set
							div_code='".$div_code."',
							group_name='".$group_name[$i+1]."',
							display_type='".$display_type[$i+1]."',
							insert_yn='Y', use_yn='".$use_yn[$i+1]."',
							group_link='".$group_link[$i+1]."',
							product_cnt='".$product_cnt[$i+1]."',
							goods_display_type='".$goods_display_type[$i+1]."',
							md_mem_ix='".$md_mem_ix[$i+1]."',
							display_auto_priod='".$display_auto_priod[$i+1]."',
							display_auto_type='".$display_auto_type[$i+1]."'
							where ppg_ix='".$db->dt[ppg_ix]."' and pg_ix = '".$pg_ix."'  and group_code = '".($i+1)."' ";


			$db->query($sql);
		}else{
			$sql = "insert into shop_promotion_product_group 
						(ppg_ix,div_code, group_name,pg_ix, group_code,group_link,display_type,product_cnt, goods_display_type, display_auto_type,display_auto_priod,md_mem_ix, insert_yn, use_yn, regdate) 
						values
						('','".$div_code."','".$group_name[$i+1]."','".$pg_ix."','".($i+1)."','".$group_link[$i+1]."','".$display_type[$i+1]."','".$product_cnt[$i+1]."','".$goods_display_type[$i+1]."','".$display_auto_type[$i+1]."','".$display_auto_priod[$i+1]."','".$md_mem_ix[$i+1]."','Y','".$use_yn[$i+1]."',NOW())";
			$db->sequences = "SHOP_PROMOTION_PRODUCT_GROUP_SEQ";
			$db->query($sql);
		}
		if ($group_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/promotion_group_".($i+1).".gif");
			@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/".$pg_ix."_promotion_group_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		if ($group_over_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/promotion_group_over_".($i+1).".gif");
			@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/".$mg_ix."_promotion_group_over_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}
		//echo "size:".$_FILES["group_img"]["size"][$i+1]."<br />";
		//print_r($_FILES);
		//exit;

		if ($_FILES["group_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/promotion_group_".($i+1).".gif");
			@copy($_FILES["group_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/".$pg_ix."_promotion_group_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		if ($_FILES["group_over_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_over_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/promotion_group_over_".($i+1).".gif");
			@copy($_FILES["group_over_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/".$mg_ix."_promotion_group_over_".($i+1).".gif");//메인분류추가에 따른 이미
		}

		if ($group_banner_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/promotion_group_banner_".($i+1).".gif");
			@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/".$pg_ix."_promotion_group_banner_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		if ($_FILES["group_banner_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_banner_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/promotion_group_banner_".($i+1).".gif");
			@copy($_FILES["group_banner_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/promotion/".$pg_ix."_promotion_group_banner_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		//$db->query("SELECT ppg_ix FROM ".tbl_shop_promotion_product_group." WHERE ppg_ix=LAST_INSERT_ID()");
		//$db->fetch();
		//$ppg_ix = $db->dt[0];

		//$db->query("update shop_promotion_product_relation set insert_yn = 'N' where promotion_ix='".$promotion_ix."' and group_code = '".($i+1)."' ");

		for($j=0;$j < count($rpid[$i+1]);$j++){
			$db->query("select ppr_ix from shop_promotion_product_relation where pg_ix = '".$pg_ix."' and group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_promotion_product_relation (ppr_ix,pid,pg_ix, div_code, group_code, vieworder, insert_yn, regdate) values ('','".$rpid[$i+1][$j]."','".$pg_ix."','".$div_code."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_PROMOTION_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_promotion_product_relation set insert_yn = 'Y',vieworder='".($j+1)."', div_code = '".$div_code."',group_code = '".($i+1)."' where pg_ix = '".$pg_ix."' and group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_promotion_product_relation where pg_ix = '".$pg_ix."' and group_code = '".($i+1)."' and insert_yn = 'N' ");

		$db->query("update shop_promotion_category_relation set insert_yn = 'N'  where pg_ix = '".$pg_ix."' and group_code = '".($i+1)."' ");

		for($j=0;$j < count($category[$i+1]);$j++){
			$db->query("select pcr_ix from shop_promotion_category_relation where pg_ix = '".$pg_ix."' and group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_promotion_category_relation (pcr_ix,cid,div_code, pg_ix, group_code, vieworder, insert_yn, regdate) values ('','".$category[$i+1][$j]."','".$div_code."','".$pg_ix."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_PROMOTION_CT_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_promotion_category_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where pg_ix = '".$pg_ix."' and group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_promotion_category_relation where pg_ix = '".$pg_ix."' and group_code = '".($i+1)."' and insert_yn = 'N' ");

	}


	$db->query("delete from shop_promotion_product_relation where pg_ix = '".$pg_ix."' and insert_yn = 'N' ");
	$db->query("delete from shop_promotion_product_group where pg_ix = '".$pg_ix."' and insert_yn = 'N' ");

	if($delete_cache == "Y"){
		include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php");
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cache/";

		$tpl->clearCache('000000000000000');
	}
	//exit;
	if($agent_type == "M"){
		echo("<script>top.location.href = '../mShop/promotion_goods.php?mmode=$mmode&pg_ix=$pg_ix';</script>");
	}else{
		echo("<script>top.location.href = 'promotion_goods.php?mmode=$mmode&pg_ix=$pg_ix';</script>");
	}

}

if($act == "delete"){
	$db->query("delete from shop_promotion_goods where pg_ix = '".$pg_ix."'");
	$db->query("delete from shop_promotion_category_relation where pg_ix = '".$pg_ix."'  ");
	$db->query("delete from shop_promotion_product_relation where pg_ix = '".$pg_ix."' ");
	$db->query("delete from shop_promotion_product_group where pg_ix = '".$pg_ix."' ");

	echo("<script>alert('프로모션 정보가 정상적으로 삭제 되었습니다.');parent.document.location.reload();</script>");
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
