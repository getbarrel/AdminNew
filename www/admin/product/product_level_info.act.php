<?
include("../../class/layout.class");
include_once("../logstory/class/sharedmemory.class");

$db = new Database;

 
  
//셀러 등급설정 관련 시작 2014-06-13 이학봉
if($act == 'product_level_update'){	//셀러등급관리

	if(!$pl_ix){
		return false;
	}
	
	$sql = "update shop_product_level set
				group_name = '".$group_name."',
				level = '".$level."',
				is_use_yn = '".$is_use_yn."',
				font_color = '".$font_color."',
				edit_date = NOW()
			where
				pl_ix = '".$pl_ix."'";
	$db->query($sql);

	
	
	// 이미지 저장 시작 
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/product_level";
	if(!is_dir($path)){
		exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
	}
	if ($sellergruop_img_size > 0){
		copy($sellergruop_img,$path."/"."product_level_".$pl_ix.".gif");
	}
	// 이미지 저장 끝

	echo("<script>alert('상품레벨 정보가 정상적으로 저장 되었습니다.');parent.document.location.href = 'product_level_info.php?info_type=".$info_type."';</script>");

}

if($act == 'product_level_setup'){	//셀러등급관리
	
	if(is_array($status)){
		$status_value = serialize($status);
	}

	$sql = "update shop_product_level set
				is_auto_yn = '".$is_auto_yn."',
				period = '".$period."',
				keep_period = '".$keep_period."',
				group_type = '".$group_type."',
				status = '".$status_value."',
				edit_date = NOW()";
	$db->query($sql);

	echo("<script>alert('상품레벨 정보가 정상적으로 저장 되었습니다.');parent.document.location.href = 'product_level_info.php?info_type=".$info_type."';</script>");

}

if ($act == "product_level_price"){	//셀러등급 매출금액 점수 변경

	foreach($data as $pl_ix => $detail){
		
		$sql = "update shop_product_level set
					st_price = '".$detail[st_price]."',
					ed_price = '".$detail[ed_price]."',
					st_point = '".$detail[st_point]."',
					ed_point = '".$detail[ed_point]."'
				where
					pl_ix = '".$pl_ix."'";
		$db->query($sql);
	}

	echo("<script>alert('상품레벨 정보가 정상적으로 저장 되었습니다.');parent.document.location.href = 'product_level_info.php?info_type=".$info_type."';</script>");
}

if ($act == "product_level_setting"){	//셀러 패널티 설정

	//$data = urlencode(serialize($_POST));
	$path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path,0777);
	}else{
		chmod($path,0777);
	}
	//print_r($_POST);
	//exit;
	setSharedInfo("product_level_setting", $_POST);
	
	/*
	$shmop = new Shared("product_level_setting");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$shmop->setObjectForKey($data,"product_level_setting");
	*/

	echo("<script>alert('상품레벨 정보가 정상적으로 저장 되었습니다.');parent.document.location.href = 'product_level_info.php?info_type=".$info_type."';</script>");
}

//셀러 등급설정 관련 끝 2014-06-13 이학봉


?>