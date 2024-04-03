<?php
include("../class/layout.class");
////////////////////
//  2013.05.07 신훈식
//  수정 : 인클루드 패스 오류
//
/////////////////////
//include("../class/database.class");
//include_once('../../include/xmlWriter.php');
session_start();

$db = new Database;
$db2 = new Database;

if ($mode == "modify"){

	if ($sub_mode == "edit_category"){	//분류수정

		$sql = "update shop_laundry_info set
					title = '$title', 
					title_en = '$title_en', 
					laundry_use = '$laundry_use', 
					laundry_use_en = '$laundry_use_en',
					contents = '$contents', 
					contents_en = '$contents_en'
				where 
					cid = '$cid'";

		$db->query($sql);

		if($laundry_use != "1" && $cid != "000000000000000"){
			$sql = "update shop_laundry_info set laundry_use ='$laundry_use' where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ";
			$db->query($sql);
		}else{
			ParentKoreaUseUpdate($db, $cid, $this_depth);

			if($this_depth+1 > 0){
				$sql = "update shop_laundry_info set laundry_use ='$laundry_use' where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ";
				$db->query($sql);
			}
		}

		if($laundry_use_en != "1" && $cid != "000000000000000"){
			$sql = "update shop_laundry_info set laundry_use_en ='$laundry_use_en' where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ";
			$db->query($sql);
		}else{
			ParentEnglishUseUpdate($db, $cid, $this_depth);

			if($this_depth+1 > 0){
				$sql = "update shop_laundry_info set laundry_use_en ='$laundry_use_en' where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ";
				$db->query($sql);
			}
		}

	}

	echo "<Script Language='JavaScript'>alert('세탁주의관리 정보가 정상적으로 수정되었습니다.');parent.document.location.href='laundry.php?cid=".$cid."&depth=".$this_depth."';</Script>";
}

function ParentKoreaUseUpdate($mdb, $cid, $this_depth){
	$where = "";
	for($i=0;$i <= $this_depth;$i++){
		if(!$where){
			$where .= " where (cid LIKE '".substr($cid,0,($i+1)*3)."%' and depth = '".$i."' ) ";
		}else{
			$where .= " or (cid LIKE '".substr($cid,0,($i+1)*3)."%' and depth = '".$i."' ) ";
		}
	}
	//$mdb->debug = true;
	$sql = "select cid, title, depth, laundry_use from shop_laundry_info   $where ";

	$mdb->query($sql);

	$parent_category = $mdb->fetchall();
	for($i=0;$i < count($parent_category);$i++){
		$sql = "update shop_laundry_info set laundry_use ='1' where cid = '".$parent_category[$i][cid]."' ";
		//echo $sql;
		$mdb->query($sql);
	}
}

function ParentEnglishUseUpdate($mdb, $cid, $this_depth){
	$where = "";
	for($i=0;$i <= $this_depth;$i++){
		if(!$where){
			$where .= " where (cid LIKE '".substr($cid,0,($i+1)*3)."%' and depth = '".$i."' ) ";
		}else{
			$where .= " or (cid LIKE '".substr($cid,0,($i+1)*3)."%' and depth = '".$i."' ) ";
		}
	}
	//$mdb->debug = true;
	$sql = "select cid, title_en, depth, laundry_use_en from shop_laundry_info   $where ";

	$mdb->query($sql);

	$parent_category = $mdb->fetchall();
	for($i=0;$i < count($parent_category);$i++){
		$sql = "update shop_laundry_info set laundry_use_en ='1' where cid = '".$parent_category[$i][cid]."' ";
		//echo $sql;
		$mdb->query($sql);
	}
}

if ($mode == "del"){
	$udb = new Database;

	if (CheckSubCategory($cid,$this_depth)){
		if($sub_cartegory_delete == "1"){

			$sql = "select * from shop_laundry_info where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);
			$db->fetch();
			$category_info = $db->dt;

			$sql = "delete from shop_laundry_info where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);

			echo "<Script Language='JavaScript'>alert(\"삭제 되었습니다.\");</Script>";
			echo "<Script Language='JavaScript'>parent.document.location.href='laundry.php';</Script>";
		}else{
			echo "<Script Language='JavaScript'>alert('하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요');</Script>";
		}
	}else{
			$sql = "select * from shop_laundry_info where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);
			$db->fetch();
			$category_info = $db->dt;

			$sql = "delete from shop_laundry_info where cid = '$cid'";
			$db->query($sql);

			echo "<Script Language='JavaScript'>alert('삭제되었습니다.');</Script>";
			//'삭제되었습니다.'
			echo "<Script Language='JavaScript'>parent.document.location.href='laundry.php?cid=$cid';</Script>";
	}
}

if ($mode == "insert"){

	if(trim($sub_cid) != "" && trim($cid) != "") {// 카테고리 정보가 제대로 안넘어 올 경우를 검사 kbk 12/03/22
		$sql = "select * from shop_laundry_info where cid = '$cid'";
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

		$sql = "insert into shop_laundry_info
				(cid, depth, vlevel1, vlevel2, vlevel3, vlevel4, vlevel5, title, title_en, laundry_use, laundry_use_en, regdate)
				values
				('$sub_cid', '$sub_depth','$level1','$level2','$level3','$level4','$level5', '$title', '$title_en', '$laundry_use', '$laundry_use_en', NOW())";

		$db->query($sql);

		echo "<Script Language='JavaScript'>parent.document.location.href='laundry.php';</Script>";
	//	Header("Location: category.php");

	} else {
		echo "insert error";
		//echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert(language_data['category.save.php']['D'][language]);</Script>";
		//카테고리 정보가 정확하지 않습니다. 상위 카테고리를 선택해 주세요.
	}

}

function getMaxlevel($cid,$depth)
{
	global $db;

	$strdepth = $depth + 1;

	$sPos = $depth*3;
	$sql = "select max(vlevel$strdepth)+1 as maxlevel from shop_laundry_info where cid LIKE '".substr($cid,0,$sPos)."%'";

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
	$sql = "select * from shop_laundry_info where depth > $this_depth and cid LIKE '".substr($cid,0,$endpos)."%'";
	$db->query($sql);
	//echo "$sql<br>";

	if ($db->total > 0){
		return true;
	}else{
		return false;
	}

}



?>