<?
include("../../class/database.class");
include('../../include/xmlWriter.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');



$db = new Database;

if($mode == "add_field"){
	for($i=1;$i<=10;$i++){
		$f_name = $_POST['etc'.$i];
		$f_type = $_POST['etc'.$i.'_type'];
		$f_search = $_POST['etc'.$i.'_search'];
		$f_value = $_POST['etc'.$i.'_value'];
		//echo $f_name."aa<br>";


		if($f_name != ""){
			$db->query("select * from ".TBL_SNS_CATEGORY_ADDFIELD." where cid = '$cid' and f_code ='etc".$i."' ");
			if($db->total){
				$db->fetch();
				$db->query("update ".TBL_SNS_CATEGORY_ADDFIELD." set  f_search = '$f_search',f_name = '$f_name',f_type = '$f_type',f_value = '$f_value'  where f_ix ='".$db->dt[f_ix]."' ");
			}else{
				$db->query("insert into ".TBL_SNS_CATEGORY_ADDFIELD." (f_ix, cid, f_code, f_search, f_name, f_type, f_value,regdate) values ('','$cid','etc".$i."','$f_search','$f_name','$f_type','$f_value',NOW() ) ");
			}
		}else{
			$db->query("delete from ".TBL_SNS_CATEGORY_ADDFIELD." where cid = '$cid' and f_code ='etc".$i."' ");
		}
	}

	echo "<Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";
}



if($mode == "infoupdate"){
	$sql = "select * from ".TBL_SNS_CATEGORY_INFO." where cid = '$cid'";
	$db->query($sql);
	//echo $sql;
	if($db->total){
		$db->fetch();

		//echo $db->dt[category_use] ;

		if($db->dt[category_use] == 1){
			$category_use = "true";
		}else{
			$category_use = "false";
		}
		$mstring ="
		<html>
		<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<body>
		<div id='category_top_view_area'>
		".$db->dt[category_top_view]."
		</div>
		</body>
		</html>
		<script>
		parent.document.forms['thisCategoryform'].category_use.checked = $category_use;
		parent.document.getElementById('iView').contentWindow.document.body.innerHTML = document.getElementById('category_top_view_area').innerHTML;
		</script>";

		echo $mstring;
	}


}


if ($mode == "modify"){
	if ($category_img_size > 0){
		copy($category_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category/".$category_img_name);
		$setString = ", catimg = '$category_img_name'";
	}

	if ($leftcategory_img_size > 0){
		copy($leftcategory_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category/".$leftcategory_img_name);
		$setString .= ", leftcatimg = '$leftcategory_img_name'";
	}

	if ($sub_img_size > 0){
		copy($sub_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category/".$sub_img_name);
	echo 	$setString .= ", subimg = '$sub_img_name'";
	}

	$sql = "update ".TBL_SNS_CATEGORY_INFO." set cname = '$this_category', category_top_view = '$category_top_view', category_use ='$category_use', category_display_type ='$category_display_type' $setString where cid = '$cid'";
	$db->query($sql);

	updateCategoryXML();
	echo "<Script Language='JavaScript'>parent.document.location.href='category.php?cid=$cid';</Script>";
	//Header("Location: category.php");

}


if ($mode == "del"){
	$udb = new Database;

	if (CheckSubCategory($cid,$this_depth)){
		if($sub_cartegory_delete == "1"){
			$sql = "select pid from ".TBL_SNS_PRODUCT_RELATION." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'  ";
			$db->query($sql);

			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);

				$sql = "select pid from ".TBL_SNS_PRODUCT_RELATION." where pid  = '".$db->dt[pid]."'  ";
				$udb->query($sql);
				if($udb->total <= 1){
					$sql = "update shop_product set reg_category ='N' where id = '".$db->dt[pid]."'  ";
					$udb->query($sql);
				}
			}

			$sql = "delete from ".TBL_SNS_CATEGORY_INFO." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);
			$db->query("delete from ".TBL_SNS_PRODUCT_RELATION." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ");
			echo "<Script Language='JavaScript'>alert(language_data['common']['F'][language]);</Script>";//\"삭제 되었습니다.\"
			echo "<Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";
		}else{
			echo "<Script Language='JavaScript'>alert(language_data['sns_category.save.php']['A'][language]);</Script>";//'하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요'
		}
	}else{



			$sql = "select pid from ".TBL_SNS_PRODUCT_RELATION." where cid = '$cid'  ";
			$db->query($sql);

			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);

				$sql = "select pid from ".TBL_SNS_PRODUCT_RELATION." where pid  = '".$db->dt[pid]."'  ";
				$udb->query($sql);
				if($udb->total <= 1){
					$sql = "update shop_product set reg_category ='N' where id = '".$db->dt[pid]."'  ";
					$udb->query($sql);
				}
			}

			$sql = "delete from ".TBL_SNS_CATEGORY_INFO." where cid = '$cid'";
			$db->query($sql);

			$db->query("delete from ".TBL_SNS_PRODUCT_RELATION." where cid ='$cid' ");
			echo "<Script Language='JavaScript'>alert(language_data['common']['F'][language]);</Script>";//'삭제되었습니다.'
			echo "<Script Language='JavaScript'>parent.document.location.href='category.php?cid=$cid';</Script>";
	}
	updateCategoryXML();
//	Header("Location: category.php");
}

if ($mode == "insert"){

	$sql = "select * from ".TBL_SNS_CATEGORY_INFO." where cid = '$cid'";
	$db->query($sql);
	$db->fetch(0);

	$level1 = $db->dt["vlevel1"];
	$level2 = $db->dt["vlevel2"];
	$level3 = $db->dt["vlevel3"];
	$level4 = $db->dt["vlevel4"];
	$level5 = $db->dt["vlevel5"];

	if ($sub_depth+1 == 1){
		$level1 = getMaxlevel($cid,$sub_depth);
	}else if($sub_depth+1 ==2){
		$level2 = getMaxlevel($cid,$sub_depth);
	}else if($sub_depth+1 ==3){
		$level3 = getMaxlevel($cid,$sub_depth);
	}else if($sub_depth+1 ==4){
		$level4 = getMaxlevel($cid,$sub_depth);
	}else if($sub_depth+1 ==5){
		$level5 = getMaxlevel($cid,$sub_depth);
	}

	if ($category_img_size > 0){
		copy($category_img, "../../image/category/".$category_img_name);
	}

	if ($leftcategory_img_size > 0){
		copy($leftcategory_img, "../../image/category/".$leftcategory_img_name);
	}

	$sql = "insert into ".TBL_SNS_CATEGORY_INFO." (cid, depth,vlevel1, vlevel2, vlevel3,vlevel4,vlevel5,cname,catimg,leftcatimg, category_use,regdate) values ('$sub_cid', '$sub_depth','$level1','$level2','$level3','$level4','$level5', '$sub_category','$category_img_name','$leftcategory_img_name','$category_use',NOW());";
	$db->query($sql);
//	echo $sql ;

	echo "<Script Language='JavaScript'>parent.document.location.href='category.php?cid=$cid';</Script>";
//	Header("Location: category.php");

	updateCategoryXML();
}

function getMaxlevel($cid,$depth)
{
	global $db;

	$strdepth = $depth + 1;

	$sPos = $depth*3;
	$sql = "select max(vlevel$strdepth)+1 as maxlevel from ".TBL_SNS_CATEGORY_INFO." where cid LIKE '".substr($cid,0,$sPos)."%'";

	$db->query($sql);
	$db->fetch(0);


//	echo $sql."<br>";
//	echo $db->dt["maxlevel"]."<br>";


	return $db->dt["maxlevel"];

}

function CheckSubCategory($cid,$depth){
	global $db;

	$endpos = $depth*3+3;
	$this_depth = $depth;
	$sql = "select * from ".TBL_SNS_CATEGORY_INFO." where depth > $this_depth and cid LIKE '".substr($cid,0,$endpos)."%'";
	$db->query($sql);
	echo "$sql<br>";

	if ($db->total > 0){
		return true;
	}else{
		return false;
	}

}

function updateCategoryXML(){

	global $DOCUMENT_ROOT, $admin_config;

	$xml = new XmlWriter_();
	$mdb = new Database;
	$mdb->query("select * from ".TBL_SNS_CATEGORY_INFO." where category_use = 1 order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");
	$categorys = $mdb->fetchall();

	$xml->push('categorys');


	foreach ($categorys as $category) {
		//$xml->push('shop', array('species' => $animal[0]));
		$xml->push('category', array('cid' => $category[cid], 'depth' => $category[depth], 'top_cid' => substr($category[cid],0,3)));
		$xml->element('cid', $category[cid]);
		$xml->element('cname', $category[cname]);
		$xml->element('depth', $category[depth]);
		$xml->pop();
	}

	$xml->pop();
	//print $xml->getXml();

	$dirname = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];
	/*
	if(!is_dir($dirname)){
		if(is_writable($path)){
			mkdir($dirname, 0777, true);
			chmod($dirname, 0777);
		}
	}
	*/
	//$fileName = "main_flash.xml";
	$fp = fopen($dirname."/categorys.xml","w");
	fputs($fp, $xml->getXml());
	fclose($fp);
}
//echo "<br>maxlevel:".getMaxlevel($cid,$sub_depth);
?>