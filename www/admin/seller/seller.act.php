<?
include("../../class/layout.class");
include_once("../logstory/class/sharedmemory.class");

$db = new Database;

//셀러 기본설정 관련 시작 2014-06-13 이학봉
if($act == "config_update"){

	//셀러기본설정 수정 히스토리 쌓기 시작 
	common_config_edit_history($_POST,$_FILES,$company_id);
	//셀러기본설정 수정 히스토리 쌓기 끝 

	$data = urlencode(serialize($_POST));
	$path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path,0777);
	}else{
		chmod($path,0777);
	}

	$shmop = new Shared("basic_seller_setup");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$shmop->setObjectForKey($data,"basic_seller_setup");

	echo("<script>alert('저장 되었습니다.');parent.document.location.href = 'seller_config.php';</script>");
}

// 상품 수정 히스토리 쌓기 함수 2014-04-09 이학봉
function seller_edit_history_insert($company_id,$column_name,$column_text,$b_data,$after_data,$chager_ix,$chager_name){	//회원정보 수정 히스토리 쌓기 2013-11-28 이학봉
	
	if(!$company_id){
		return false;
	}
	$db = new Database;

	$sql = "insert into common_seller_config_edit_history set 
				company_id = '".$company_id."',
				b_data = '".$b_data."',
				after_data = '".$after_data."',
				column_name = '".$column_name."',
				column_text = '".$column_text."',
				chager_ix = '".$chager_ix."',
				chager_name = '".$chager_name."',
				regdate = NOW()";
	$db->query($sql);
}

// 상품 수정 히스토리 쌓기 함수 2014-04-09 이학봉
function common_config_edit_history($_POST,$_FILES,$company_id){
	
	global $_SESSION;
	
	if(!$company_id){
		return false;
	}

	$db = new Database;

	$compare_value[0] = array("input_name"=>"seller_use_info", "column_name"=>"seller_use_info", "name_text"=>"사이트설정");
	$compare_value[1] = array("input_name"=>"seller_join_type", "column_name"=>"seller_join_type", "name_text"=>"셀러 사용여부");
	$compare_value[2] = array("input_name"=>"seller_minishop_use", "column_name"=>"seller_minishop_use", "name_text"=>"셀러 미니샵 사용여부");

	$compare_value[3] = array("input_name"=>"account_type", "column_name"=>"account_type", "name_text"=>"정산방식");
	$compare_value[4] = array("input_name"=>"account_info", "column_name"=>"account_info", "name_text"=>"정산 상품 기간설정");

	$compare_value[5] = array("input_name"=>"ac_delivery_type", "column_name"=>"ac_delivery_type", "name_text"=>"배송처리상태(정산상품기간설정)");
	$compare_value[6] = array("input_name"=>"ac_expect_date", "column_name"=>"ac_expect_date", "name_text"=>"정산처리예정일자");

	$compare_value[7] = array("input_name"=>"ac_term_div", "column_name"=>"ac_term_div", "name_text"=>"정산확정일(월횟수)");
	$compare_value[8] = array("input_name"=>"ac_term_date1", "column_name"=>"ac_term_date1", "name_text"=>"정산처리일(1)");
	$compare_value[9] = array("input_name"=>"ac_term_date2", "column_name"=>"ac_term_date2", "name_text"=>"정산처리일(2)");

	$compare_value[10] = array("input_name"=>"account_method", "column_name"=>"account_method", "name_text"=>"정산 지급방식");

	$compare_value[11] = array("input_name"=>"account_div", "column_name"=>"account_div", "name_text"=>"정산유형");	

	$compare_value[12] = array("input_name"=>"wholesale_commission", "column_name"=>"wholesale_commission", "name_text"=>"도매수수료");
	$compare_value[13] = array("input_name"=>"commission", "column_name"=>"commission", "name_text"=>"소매수수료");

	$compare_value[14] = array("input_name"=>"electron_contract_category", "column_name"=>"electron_contract_category", "name_text"=>"전자계약서분류");

	$compare_value[15] = array("input_name"=>"electron_contract", "column_name"=>"electron_contract", "name_text"=>"계약서종류");
	$compare_value[16] = array("input_name"=>"electron_contract_commission", "column_name"=>"electron_contract_commission", "name_text"=>"계약서내 수수료율");

	$db_value = getBasicSellerSetup('basic_seller_setup');	//셀러기본설정 기존 데이타 불러오기
	
	for($i=0;$i<count($compare_value);$i++){

		if($compare_value[$i][input_name] == 'mandatory_type_1'){
			if($_POST[mandatory_type_1]."|".$_POST[mandatory_type_2] != $db_value[$compare_value[$i][column_name]]){
				seller_edit_history_insert($company_id,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[mandatory_type_1]."|".$_POST[mandatory_type_2],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}
		}else{
			echo $_POST[$compare_value[$i][input_name]]." != ".$db_value[$compare_value[$i][column_name]]."<BR>"; 
			if($_POST[$compare_value[$i][input_name]] != $db_value[$compare_value[$i][column_name]]){
				seller_edit_history_insert($company_id,$compare_value[$i][column_name],$compare_value[$i][name_text],$db_value[$compare_value[$i][column_name]],$_POST[$compare_value[$i][input_name]],$_SESSION[admininfo][charger_ix],$_SESSION[admininfo][charger]);
			}
		}
	}

}
// 상품 수정 히스토리 쌓기 함수 2014-04-09 이학봉

//셀러 기본설정 관련 끝 2014-06-13 이학봉.



//셀러 등급설정 관련 시작 2014-06-13 이학봉
if($act == 'sellergroup_update'){	//셀러등급관리

	if(!$sg_ix){
		return false;
	}
	
	$sql = "update common_seller_group set
				group_name = '".$group_name."',
				level = '".$level."',
				is_use_yn = '".$is_use_yn."',
				font_color = '".$font_color."',
				edit_date = NOW()
			where
				sg_ix = '".$sg_ix."'";
	$db->query($sql);

	
	
	// 이미지 저장 시작 
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/sellergroup";
	if(!is_dir($path)){
		exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
	}
	if ($sellergruop_img_size > 0){
		copy($sellergruop_img,$path."/"."sellergroup_".$sg_ix.".gif");
	}
	// 이미지 저장 끝

	echo("<script>alert('저장 되었습니다.');parent.document.location.href = 'seller_level.php?info_type=".$info_type."';</script>");

}

if($act == 'sellergroup_setup'){	//셀러등급관리
	
	if(is_array($status)){
		$status_value = serialize($status);
	}

	$sql = "update common_seller_group set
				is_auto_yn = '".$is_auto_yn."',
				period = '".$period."',
				keep_period = '".$keep_period."',
				group_type = '".$group_type."',
				status = '".$status_value."',
				edit_date = NOW()";
	$db->query($sql);

	echo("<script>alert('저장 되었습니다.');parent.document.location.href = 'seller_level.php?info_type=".$info_type."';</script>");

}

if ($act == "sellergroup_price"){	//셀러등급 매출금액 점수 변경

	foreach($data as $sg_ix => $detail){
		
		$sql = "update common_seller_group set
					st_price = '".$detail[st_price]."',
					ed_price = '".$detail[ed_price]."',
					st_point = '".$detail[st_point]."',
					ed_point = '".$detail[ed_point]."'
				where
					sg_ix = '".$sg_ix."'";
		$db->query($sql);
	}

	echo("<script>alert('저장 되었습니다.');parent.document.location.href = 'seller_level.php?info_type=".$info_type."';</script>");
}

if ($act == "sellergroup_penalty"){	//셀러 판매신용점수 설정

	//$data = urlencode(serialize($_POST));
	$path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		chmod($path,0777);
	}else{
		chmod($path,0777);
	}
	setSharedInfo("sellergroup_penalty", $_POST);
	/*
	$shmop = new Shared("sellergroup_penalty");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$shmop->setObjectForKey($data,"sellergroup_penalty");
	*/

	echo("<script>alert('저장 되었습니다.');parent.document.location.href = 'seller_level.php?info_type=".$info_type."';</script>");
}

//셀러 등급설정 관련 끝 2014-06-13 이학봉



//셀러상품상세페이지 공지사항 추가 2014-08-26 이학봉
if($act == 'seller_promotion_notice_update'){	//셀로 상품상세페이지 공지사항

	$sql = "select * from common_seller_promotion_notice where pn_ix = '".$pn_ix."'";
	$db->query($sql);
	$db->fetch();

	if($is_use == '1'){
		$sql = "update common_seller_promotion_notice set is_use = '0' where company_id = '".$company_id."' and is_use = '1'";
	
		$db->query($sql);
	}

	if($db->total > 0){
		$sql = "update common_seller_promotion_notice set
					company_id = '".$company_id."',
					com_name = '".$com_name."',
					notice_title = '".$notice_title."',
					is_use = '".$is_use."',
					edit_date = NOW()
				where
					pn_ix = '".$pn_ix."'";
		$db->query($sql);
	}else{
		$sql = "insert into common_seller_promotion_notice set
					company_id = '".$company_id."',
					com_name = '".$com_name."',
					notice_title = '".$notice_title."',
					is_use = '".$is_use."',
					regdate = NOW()
				";
			
		$db->query($sql);
		$pn_ix = $db->insert_id();
	}

	
	// 이미지 저장 시작 
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/sellergroup";
	if(!is_dir($path)){
		exec("mkdir -m 755 ".$path);	//이미지 폴더 생성
	}
	if ($seller_notice_img_size > 0){
		copy($seller_notice_img,$path."/"."seller_promotion_".$pn_ix.".jpg");
		//exec("mkdir -m 755 ".$path."/"."seller_promotion_".$pn_ix.".jpg");	//이미지 폴더 생성
		chmod($path."/"."seller_promotion_".$pn_ix.".jpg",0777);
	}
	// 이미지 저장 끝

	echo("<script>alert('저장 되었습니다.');parent.document.location.href = 'promotion_product_notice.php';</script>");

}

if($act == "seller_promotion_pn_ix"){

	if($pn_ix){
		$sql = "delete from common_seller_promotion_notice where pn_ix = '".$pn_ix."'";
		$db->query($sql);

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/sellergroup";

		$del = "seller_promotion_".$pn_ix.".jpg";

		@unlink($path."/".$del);
		
		@$name = explode("_",$del);
		if($name[0] == "sheet"){
			@unlink($path."/".$del);
		}

	}

}

if($act == "seller_promotion_image_del"){
	
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/sellergroup";

	$del = "seller_promotion_".$pn_ix.".jpg";

	@unlink($path."/".$del);
	
	@$name = explode("_",$del);
	if($name[0] == "sheet"){
		@unlink($path."/".$del);
	}

}


?>