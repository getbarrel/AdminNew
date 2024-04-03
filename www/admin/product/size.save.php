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

		$title_img		= $title_img_old;
		$contents_pc	= $contents_pc_old;
		$contents_mo	= $contents_mo_old;

		if($title_img_del == 'y'){
			$title_img_del = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root]. "/images/size/" .$title_img_old;
			unlink($title_img_del);

			$title_img		= '';
		}

		if($contents_pc_del == 'y'){
			$contents_pc_del = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root]. "/images/size/" .$contents_pc_old;
			unlink($contents_pc_del);

			$contents_pc	= '';
		}

		if($contents_mo_del == 'y'){
			$contents_mo_del = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root]. "/images/size/" .$contents_mo_old;
			unlink($contents_mo_del);

			$contents_mo	= '';
		}

		if($_FILES['title_img']['size'] > 0){
			if($_FILES['title_img']['type'] != 'image/jpeg'){
				echo "<Script Language='JavaScript'>alert('타이틀 이모티콘 파일의 형식이 잘못되었습니다.');parent.document.location.href='size.php?cid=".$cid."&depth=".$this_depth."';</Script>";
			}

			$title_img = $_FILES['title_img']['name'];
			$file_path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root]. "/images/size/" .$title_img;

			move_uploaded_file($_FILES['title_img']['tmp_name'], $file_path);
		}

		if($_FILES['contents_pc']['size'] > 0){
			if($_FILES['contents_pc']['type'] != 'image/jpeg'){
				echo "<Script Language='JavaScript'>alert('PC 이미지 파일의 형식이 잘못되었습니다.');parent.document.location.href='size.php?cid=".$cid."&depth=".$this_depth."';</Script>";
			}

			$contents_pc = $_FILES['contents_pc']['name'];
			$file_path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root]. "/images/size/" .$contents_pc;

			move_uploaded_file($_FILES['contents_pc']['tmp_name'], $file_path);
		}

		if($_FILES['contents_mo']['size'] > 0){
			if($_FILES['contents_mo']['type'] != 'image/jpeg'){
				echo "<Script Language='JavaScript'>alert('Mobile 이미지 파일의 형식이 잘못되었습니다.');parent.document.location.href='size.php?cid=".$cid."&depth=".$this_depth."';</Script>";
			}

			$contents_mo = $_FILES['contents_mo']['name'];
			$file_path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root]. "/images/size/" .$contents_mo;

			move_uploaded_file($_FILES['contents_mo']['tmp_name'], $file_path);
		}

		$sql = "update shop_goods_size_info set
					title = '$title', 
					size_use = '$size_use',
					title_img = '$title_img',
					contents_pc = '$contents_pc',
					contents_mo = '$contents_mo'
				where 
					cid = '$cid'";

		$db->query($sql);
	}
	echo "<Script Language='JavaScript'>alert('상품사이즈 정보가 정상적으로 수정되었습니다.');parent.document.location.href='size.php?cid=".$cid."&depth=".$this_depth."';</Script>";
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
	$sql = "select cid, title, depth, laundry_use from shop_goods_size_info   $where ";

	$mdb->query($sql);

	$parent_category = $mdb->fetchall();
	for($i=0;$i < count($parent_category);$i++){
		$sql = "update shop_goods_size_info set laundry_use ='1' where cid = '".$parent_category[$i][cid]."' ";
		//echo $sql;
		$mdb->query($sql);
	}
}

if ($mode == "del"){
	$udb = new Database;

	if (CheckSubCategory($cid,$this_depth)){
		if($sub_cartegory_delete == "1"){

			$sql = "select * from shop_goods_size_info where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);
			$db->fetch();
			$category_info = $db->dt;

			$sql = "delete from shop_goods_size_info where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);

			echo "<Script Language='JavaScript'>alert(\"삭제 되었습니다.\");</Script>";
			echo "<Script Language='JavaScript'>parent.document.location.href='size.php';</Script>";
		}else{
			echo "<Script Language='JavaScript'>alert('하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요');</Script>";
		}
	}else{
			$sql = "select * from shop_goods_size_info where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);
			$db->fetch();
			$category_info = $db->dt;

			$sql = "delete from shop_goods_size_info where cid = '$cid'";
			$db->query($sql);

			echo "<Script Language='JavaScript'>alert('삭제되었습니다.');</Script>";
			//'삭제되었습니다.'
			echo "<Script Language='JavaScript'>parent.document.location.href='size.php';</Script>";
	}
}

if ($mode == "insert"){

	if(trim($sub_cid) != "" && trim($cid) != "") {// 카테고리 정보가 제대로 안넘어 올 경우를 검사 kbk 12/03/22
		$sql = "select * from shop_goods_size_info where cid = '$cid'";
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

		$sql = "insert into shop_goods_size_info
				(cid, depth, vlevel1, vlevel2, vlevel3, vlevel4, vlevel5, title, size_use, regdate)
				values
				('$sub_cid', '$sub_depth','$level1','$level2','$level3','$level4','$level5', '$title', '$size_use', NOW())";

		$db->query($sql);

		echo "<Script Language='JavaScript'>parent.document.location.href='size.php';</Script>";
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
	$sql = "select max(vlevel$strdepth)+1 as maxlevel from shop_goods_size_info where cid LIKE '".substr($cid,0,$sPos)."%'";

	$db->query($sql);
	$db->fetch(0);

	return $db->dt["maxlevel"];

}

function CheckSubCategory($cid,$depth){
	global $db;

	$endpos = $depth*3+3;
	$this_depth = $depth;
	$sql = "select * from shop_goods_size_info where depth > $this_depth and cid LIKE '".substr($cid,0,$endpos)."%'";
	$db->query($sql);

	if ($db->total > 0){
		return true;
	}else{
		return false;
	}

}



?>