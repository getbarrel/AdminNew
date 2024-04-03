<?
include("../../class/database.class");

$db = new Database;
if($mode == "infoupdate"){
	$sql = "select * from ".TBL_SHOP_ESTIMATE_CATEGORY." where cid = '$cid'";
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

		//parent.iView.document.body.innerHTML = document.getElementById('category_top_view_area').innerHTML;
		</script>";
		
		echo $mstring;
	}
	
	
}


if ($mode == "modify"){
	if ($category_img_size > 0){		
		copy($category_img, "../../image/category/".$category_img_name);
		$setString = ", catimg = '$category_img_name'";
	}
	
	if ($leftcategory_img_size > 0){		
		copy($leftcategory_img, "../../image/category/".$leftcategory_img_name);
		$setString = ", leftcatimg = '$leftcategory_img_name'";
	}
	
	$sql = "update ".TBL_SHOP_ESTIMATE_CATEGORY." set cname = '$this_category', category_top_view = '$category_top_view', category_use ='$category_use' $setString where cid = '$cid'";
	$db->query($sql);
	
	
	echo "<Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";	
	//Header("Location: category.php");
	
}

if ($mode == "del"){
	if (CheckSubCategory($cid,$this_depth)){
		if($sub_cartegory_delete == "1"){
			$sql = "delete from ".TBL_SHOP_ESTIMATE_CATEGORY." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);
			$db->query("delete from ".TBL_SHOP_PRODUCT_RELATION." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ");	
			echo "<Script Language='JavaScript'>alert(\"삭제 되었습니다.\");</Script>";	
			echo "<Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";	
		}else{
			echo "<Script Language='JavaScript'>alert('하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요');</Script>";	
		}
	}else{
		
			$sql = "delete from ".TBL_SHOP_ESTIMATE_CATEGORY." where cid = '$cid'";
			$db->query($sql);
			$db->query("delete from ".TBL_SHOP_PRODUCT_RELATION." where cid ='$cid' ");	
			echo "<Script Language='JavaScript'>alert('삭제되었습니다.');</Script>";	
			echo "<Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";	
	}
	
//	Header("Location: category.php");	
}

if ($mode == "insert"){	
	
	$sql = "select * from ".TBL_SHOP_ESTIMATE_CATEGORY." where cid = '$cid'";
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
	
	$sql = "insert into ".TBL_SHOP_ESTIMATE_CATEGORY." (cid, depth,vlevel1, vlevel2, vlevel3,vlevel4,vlevel5,cname,catimg,leftcatimg, category_use,regdate) values ('$sub_cid', '$sub_depth','$level1','$level2','$level3','$level4','$level5', '$sub_category','$category_img_name','$leftcategory_img_name','$category_use',NOW());";
	$db->query($sql);
//	echo $sql ;

	echo "<Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";	
//	Header("Location: category.php");	
}

function getMaxlevel($cid,$depth)
{
	global $db;
		
	$strdepth = $depth + 1;
	
	$sPos = $depth*3;
	$sql = "select max(vlevel$strdepth)+1 as maxlevel from ".TBL_SHOP_ESTIMATE_CATEGORY." where cid LIKE '".substr($cid,0,$sPos)."%'";
	
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
	$sql = "select * from ".TBL_SHOP_ESTIMATE_CATEGORY." where depth > $this_depth and cid LIKE '".substr($cid,0,$endpos)."%'";
	$db->query($sql);
	echo "$sql<br>";
	
	if ($db->total > 0){
		return true;
	}else{
		return false;
	}
	
}


//echo "<br>maxlevel:".getMaxlevel($cid,$sub_depth);
?>