<?
@include_once("../web.config");
include_once("../../class/database.class");
include_once("../lib/imageResize.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
//include_once("goods.options.lib.php");
@include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

session_start();
$db = new Database;
$db2 = new Database;

if($admininfo[company_id] == ""){
	echo "<script language='JavaScript' src='../_language/language.php'></Script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}

if($act == "update" || $act == "tmp_update"){	//상품 업데이트

	if(!file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product/")){
		mkdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product/");
		chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product/",0777);
	}

	//예외처리
	/*
	$basicinfo = str_replace("''","",$basicinfo);
	$basicinfo = str_replace("'","&#39;",$basicinfo);
	$basicinfo = str_replace('"',"&quot;",$basicinfo);
	$basicinfo = strip_tags($basicinfo,"<img><table><th><col><colgroup><tbody><tr><td><div><a><ul><li><dl><dt><p><h><font><strong><br><span>");	//2014-07-27 edit 태그 예외처리 이학봉

	$m_basicinfo = str_replace("''","",$m_basicinfo);
	$m_basicinfo = str_replace("'","&#39;",$m_basicinfo);
	$m_basicinfo = str_replace('"',"&quot;",$m_basicinfo);
	$m_basicinfo = strip_tags($m_basicinfo,"<img><table><th><col><colgroup><tbody><tr><td><div><a><ul><li><dl><dt><p><h><font><strong><br><span>");	//2014-07-27 edit 태그 예외처리 이학봉
	*/

	$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $id, 'Y');
	$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $id, 'Y');

	//상품 수정 히스토리 쌓기 2014-04-09 이학봉
	product_edit_history($_POST,$_FILES,$id);
	//상품 수정 히스토리 쌓기 2014-04-09 이학봉

	//account_type 정산방식 1:수수료 2:매입(공급가로 정산) 3:미정산
	//wholesale_commission 도매수수료
	//추가 합니다. 2013-10-29 이학봉
	if($admininfo[admin_level] == 9){
		
		if($check_infos[update_check_one_commission] == '1'){
		$commision_str = "	,one_commission='$one_commission',  account_type= '$account_type' ";
		}

		if($check_infos[update_check_commission] == '1'){
		$commision_str .= " ,commission='$commission' , wholesale_commission = '$wholesale_commission' ";
		}

		if($check_infos[update_check_account_type] == '1'){
		$commision_str .= ",  account_type= '$account_type' ";
		}
		if($admin != ""){
			$company_id = $admin;
		}else if($company_id != ""){
			$company_id = $company_id;
		}
	}

	if($b_ix !=""){
		$brand = $b_ix;
	}

	if(!$brand_name){
		$sql = "select brand_name from shop_brand where b_ix = '$brand'";
		$db->query($sql);
		$db->fetch();
		$brand_name = $db->dt[brand_name];
		$brand_name = str_replace("'","\\'",$brand_name);
	}

	if($admininfo[admin_level] == 9){

		for($i=0;$i<count($icon_check);$i++){
			if($i < count($icon_check)-1){
				$icons .= $icon_check[$i].";";
			}else{
				$icons .= $icon_check[$i];
			}
		}
		$icons_str = ", icons='$icons' ";
	}

	$sns_btn = serialize($sns_btn);//

	if($delivery_policy == ""){
		$delivery_policy = "1";
	}

	$mandatory_type = $mandatory_type."|1";	// 상품고시 제대로 substr 되지 않아서 | 구분값으로 분리 시킴 2013-06-05 이학봉
    $mandatory_type_global = $mandatory_type_global."|1";
	/*
	if($mandatory_type_1 !=""){
		$mandatory_type = $mandatory_type_1."|".$mandatory_type_2;	// 상품고시 제대로 substr 되지 않아서 | 구분값으로 분리 시킴 2013-06-05 이학봉
	}else{
		$mandatory_type ="";
	}
	*/

	if($product_type=="99"){
		$coupon_use_yn="N";//세트 상품은 쿠폰 사용 못하도록 고정. goods_input.php 에서 사용안함으로 체크하나 disabled 속성으로 처리하기에 값이 넘어오지 않음. N 으로 고정시킴 kbk 13/07/17
	}

	$delivery_company = "MI"; // 2013년 08월 09일 신훈식 현재사용하지 않아 고정값으로 픽스


	if($check_infos[update_check_state] == '1'){
		/*판매상태가 일시품절일경우 입고예정일과 자동판매중 상태전환일/ 상태변경에 따른 변경사유 시작 2014-02-14 이학봉*/
		if($state == "0"){
			if($is_auto_change == "1"){
				$input_date = $input_date." ".$input_stime.":".$input_smin.":00";
				$auto_change_state = $auto_change_state." ".$auto_change_stime.":".$auto_change_smin.":00";
				$where_state_div = ", input_date = '".$input_date."', is_auto_change = '".$is_auto_change."', auto_change_state='".$auto_change_state."'";
			}else{
				$input_date = $input_date." ".$input_stime.":".$input_smin.":00";
				$where_state_div = ", input_date = '".$input_date."', is_auto_change = '".$is_auto_change."'";
			}
		}else if($state == "2" || $state == "8" || $state == "9" || $state == "7"){

			$sql = "insert into shop_product_state_history set
						pid = '".$id."',
						state = '".$state."',
						state_div = '".$state_div."',
						state_msg = '".$state_msg."',
						charger = '".$admininfo[charger]."',
						charger_ix = '".$admininfo[charger_ix]."',
						regdate = NOW()";
			$db->query($sql);
		}
		/*판매상태가 일시품절일경우 입고예정일과 자동판매중 상태전환일/ 상태변경에 따른 변경사유 시작 2014-02-14 이학봉*/
	}
	
	if($admininfo[admin_level] != '9'){		//셀러가 엑셀에서 수정했을경우 승인대기로 처리함 2014-08-10 
		//$state = "6";
		$where_state = " , state = '6'";
	}else{
		if($check_infos[update_check_state] == '1'){
			//$state = $state;	//본사일경우에는 데이타 그대로 수정됨 2014-08-10 
			$where_state = " , state = '".$state."'";
		}
	}

	$sell_priod_sdate = $sell_priod_sdate." ".$sell_priod_sdate_h.":".$sell_priod_sdate_i.":".$sell_priod_sdate_s;
	$sell_priod_edate = $sell_priod_edate." ".$sell_priod_edate_h.":".$sell_priod_edate_i.":".$sell_priod_edate_s;

	if($check_infos[update_check_basicinfo] == '1'){	//상세이미지
		if($basicinfo != "#"){
			$set_update = " , basicinfo='$basicinfo' ";
		}
	}
	
	if($check_infos[update_check_m_basicinfo] == '1'){	//모바일 상세이미지
		if($m_basicinfo != "#"){
			$set_update .= " , m_basicinfo='$m_basicinfo' ";
		}
	}

	$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET
				is_pos_link ='N',
				add_status = 'U',
				editdate = NOW()";
				//$sql .= ($check_infos[update_check_gid] == '1'?", pcode='$pcode' ":"");
				$sql .= ($check_infos[update_check_pcode] == '1'?", pcode='$pcode' ":"");
				$sql .= ($check_infos[update_check_md_id] == '1'?", md_code='$md_code' ":"");
				$sql .= ($check_infos[update_check_disp] == '1'?", disp='$disp' ":"");
				$sql .= ($check_infos[update_check_pname] == '1'?", pname='".strip_tags(trim($pname))."' ":"");
				$sql .= ($check_infos[update_check_add_info] == '1'?", add_info = '$add_info' ":"");
				$sql .= ($check_infos[update_check_laundry_cid] == '1'?", laundry_cid = '$laundry_cid' ":"");
				$sql .= ($check_infos[update_check_admin_memo] == '1'?", admin_memo = '$admin_memo' ":"");
				$sql .= ($check_infos[update_check_preface] == '1'?", preface = '$preface' ":"");
				$sql .= ($check_infos[update_check_brand] == '1'?", brand = '$brand' ":"");
				$sql .= ($check_infos[update_check_brand] == '1'?", brand_name = '$brand_name' ":"");
				$sql .= ($check_infos[update_check_company] == '1'?", company='$company' ":"");
				$sql .= ($check_infos[update_check_company] == '1'?", c_ix='$c_ix' ":"");
				$sql .= ($check_infos[update_check_paper_pname] == '1'?", paper_pname='$paper_pname' ":"");
				$sql .= ($check_infos[update_check_shotinfo] == '1'?", shotinfo='$shotinfo' ":"");
				$sql .= ($check_infos[update_check_wholesale_price] == '1'?", wholesale_price='$wholesale_price' ":"");
				$sql .= ($check_infos[update_check_wholesale_sellprice] == '1'?", wholesale_sellprice='$wholesale_sellprice' ":"");
				$sql .= ($check_infos[update_check_listprice] == '1'?", listprice='$listprice' ":"");
				$sql .= ($check_infos[update_check_sellprice] == '1'?", sellprice='$sellprice' ":"");
				$sql .= ($check_infos[update_check_premiumprice] == '1'?", premiumprice='$premiumprice' ":"");
				$sql .= ($check_infos[update_check_coprice] == '1'?", coprice='$coprice' ":"");
				$sql .= ($check_infos[update_check_mandatory_info] == '1'?", mandatory_type='$mandatory_type' ":"");
				$sql .= ($check_infos[update_check_mandatory_info_global] == '1'?", mandatory_type_global='$mandatory_type_global' ":"");
				$sql .= ($check_infos[update_check_movie] == '1'?", movie ='$movie' ":"");
				$sql .= ($check_infos[update_check_barcode] == '1'?", barcode ='$barcode' ":"");
				$sql .= ($check_infos[update_check_trade_admin] == '1'?", trade_admin ='$trade_admin' ":"");
				$sql .= ($check_infos[update_check_delivery_coupon_yn] == '1'?", delivery_coupon_yn ='$delivery_coupon_yn' ":"");
				$sql .= ($check_infos[update_check_coupon_use_yn] == '1'?", coupon_use_yn ='$coupon_use_yn' ":"");
				$sql .= ($check_infos[update_check_delivery_type] == '1'?", delivery_type ='$delivery_type' ":"");
				$sql .= ($check_infos[update_check_safestock] == '1'?", safestock ='$safestock' ":"");
				$sql .= ($check_infos[update_check_stock] == '1'?", stock='$stock'":"");
				$sql .= ($check_infos[update_check_available_stock] == '1'?", available_stock='$available_stock'":"");
				$sql .= ($check_infos[update_check_search_keyword] == '1'?", search_keyword ='$search_keyword' ":"");
				$sql .= ($check_infos[update_check_surtax_yorn] == '1'?", surtax_yorn ='$surtax_yorn' ":"");
				$sql .= ($check_infos[update_check_product_type] == '1'?", product_type ='$product_type' ":"");
				$sql .= ($check_infos[update_check_sell_priod_date] == '1'?", sell_priod_sdate='$sell_priod_sdate' ":"");
				$sql .= ($check_infos[update_check_sell_priod_date] == '1'?", sell_priod_edate='$sell_priod_edate' ":"");
				$sql .= ($check_infos[update_check_allow_order_type] == '1'?", allow_order_type='$allow_order_type' ":"");
				$sql .= ($check_infos[update_check_allow_order_cnt_byonesell] == '1'?", allow_order_cnt_byonesell ='$allow_order_cnt_byonesell' ":"");
				$sql .= ($check_infos[update_check_og_ix] == '1'?", origin = '$origin'":"");
				$sql .= ($check_infos[update_check_make_date] == '1'?", make_date = '$make_date'":"");
				$sql .= ($check_infos[update_check_expiry_date] == '1'?", expiry_date = '$expiry_date'":"");
				$sql .= ($check_infos[update_check_stock_use_yn] == '1'?", stock_use_yn = '$stock_use_yn'":"");

				$sql .= ($check_infos[update_check_is_adult] == '1'?", is_adult = '$is_adult'":"");
				$sql .= ($check_infos[update_check_is_sell_date] == '1'?", is_sell_date = '$is_sell_date'":"");
				$sql .= ($check_infos[update_check_allow_max_cnt] == '1'?", wholesale_allow_max_cnt = '$wholesale_allow_max_cnt'":"");
				$sql .= ($check_infos[update_check_allow_max_cnt] == '1'?", allow_max_cnt = '$allow_max_cnt'":"");
				
				$sql .= ($check_infos[update_check_allow_basic_cnt] == '1'?", wholesale_allow_basic_cnt = '$wholesale_allow_basic_cnt'":"");
				$sql .= ($check_infos[update_check_allow_basic_cnt] == '1'?", allow_basic_cnt = '$allow_basic_cnt'":"");
				
				$sql .= ($check_infos[update_check_product_weight] == '1'?", product_weight = '$product_weight'":"");

				$sql .= ($check_infos[update_check_wear_info] == '1'?", wear_info = '$wear_info'":"");
				$sql .= ($check_infos[update_check_listNum] == '1'?", listNum = '$listNum'":"");
				$sql .= ($check_infos[update_check_overNum] == '1'?", overNum = '$overNum'":"");
				$sql .= ($check_infos[update_check_slistNum] == '1'?", slistNum = '$slistNum'":"");
				$sql .= ($check_infos[update_check_nailNum] == '1'?", nailNum = '$nailNum'":"");
				$sql .= ($check_infos[update_check_pattNum] == '1'?", pattNum = '$pattNum'":"");
				$sql .= ($check_infos[update_check_c_preface] == '1'?", c_preface = '$c_preface'":"");
					
				$sql .= ($check_infos[update_check_wholesale_allow_byoneperson_cnt] == '1'?", allow_byoneperson_cnt = '$allow_byoneperson_cnt'":"");
				$sql .= ($check_infos[update_check_wholesale_allow_byoneperson_cnt] == '1'?", wholesale_allow_byoneperson_cnt = '$wholesale_allow_byoneperson_cnt'":"");
	
				$sql .= $where_state_div;
				$sql .= $commision_str;
				$sql .= $set_update;
				$sql .= $where_state;
		$sql .= "	Where 
				id = '$id' ";
	$db->query($sql);


	//글로벌

	if($check_infos[update_check_english_basicinfo] == '1'){	//상세이미지
		if($english_basicinfo != "#"){
			$english_set_update = " , basicinfo='$english_basicinfo' ";
		}
	}

	if($check_infos[update_check_english_m_basicinfo] == '1'){	//모바일 상세이미지
		if($english_m_basicinfo != "#"){
			$english_set_update .= " , m_basicinfo='$english_m_basicinfo' ";
		}
	}

	$sql = "UPDATE shop_product_global SET
				is_pos_link ='N',
				add_status = 'U',
				editdate = NOW()";
				//$sql .= ($check_infos[update_check_gid] == '1'?", pcode='$pcode' ":"");
				$sql .= ($check_infos[update_check_pcode] == '1'?", pcode='$pcode' ":"");
				$sql .= ($check_infos[update_check_md_id] == '1'?", md_code='$md_code' ":"");
				$sql .= ($check_infos[update_check_disp] == '1'?", disp='$disp' ":"");
				$sql .= ($check_infos[update_check_english_pname] == '1'?", pname='".strip_tags(trim($english_pname))."' ":"");
				$sql .= ($check_infos[update_check_english_add_info] == '1'?", add_info = '$english_add_info' ":"");
				$sql .= ($check_infos[update_check_laundry_cid] == '1'?", laundry_cid = '$laundry_cid' ":"");
				$sql .= ($check_infos[update_check_english_preface] == '1'?", preface = '$english_preface' ":"");
				$sql .= ($check_infos[update_check_brand] == '1'?", brand = '$brand' ":"");
				$sql .= ($check_infos[update_check_brand] == '1'?", brand_name = '$brand_name' ":"");
				$sql .= ($check_infos[update_check_company] == '1'?", company='$company' ":"");
				$sql .= ($check_infos[update_check_company] == '1'?", c_ix='$c_ix' ":"");
				$sql .= ($check_infos[update_check_paper_pname] == '1'?", paper_pname='$paper_pname' ":"");
				$sql .= ($check_infos[update_check_english_shotinfo] == '1'?", shotinfo='$english_shotinfo' ":"");
				$sql .= ($check_infos[update_check_wholesale_price] == '1'?", wholesale_price='$wholesale_price' ":"");
				$sql .= ($check_infos[update_check_wholesale_sellprice] == '1'?", wholesale_sellprice='$wholesale_sellprice' ":"");
				$sql .= ($check_infos[update_check_english_listprice] == '1'?", listprice='$english_listprice' ":"");
				$sql .= ($check_infos[update_check_english_sellprice] == '1'?", sellprice='$english_sellprice' ":"");
				$sql .= ($check_infos[update_check_premiumprice] == '1'?", premiumprice='$premiumprice' ":"");
				$sql .= ($check_infos[update_check_english_coprice] == '1'?", coprice='$english_coprice' ":"");
				$sql .= ($check_infos[update_check_mandatory_info] == '1'?", mandatory_type='$mandatory_type' ":"");
				$sql .= ($check_infos[update_check_mandatory_info_global] == '1'?", mandatory_type_global='$mandatory_type_global' ":"");
				$sql .= ($check_infos[update_check_movie] == '1'?", movie ='$movie' ":"");
				$sql .= ($check_infos[update_check_barcode] == '1'?", barcode ='$barcode' ":"");
				$sql .= ($check_infos[update_check_trade_admin] == '1'?", trade_admin ='$trade_admin' ":"");
				$sql .= ($check_infos[update_check_delivery_coupon_yn] == '1'?", delivery_coupon_yn ='$delivery_coupon_yn' ":"");
				$sql .= ($check_infos[update_check_coupon_use_yn] == '1'?", coupon_use_yn ='$coupon_use_yn' ":"");
				$sql .= ($check_infos[update_check_delivery_type] == '1'?", delivery_type ='$delivery_type' ":"");
				$sql .= ($check_infos[update_check_safestock] == '1'?", safestock ='$safestock' ":"");
				$sql .= ($check_infos[update_check_stock] == '1'?", stock='$stock'":"");
				$sql .= ($check_infos[update_check_available_stock] == '1'?", available_stock='$available_stock'":"");
				$sql .= ($check_infos[update_check_english_search_keyword] == '1'?", search_keyword ='$english_search_keyword' ":"");
				$sql .= ($check_infos[update_check_surtax_yorn] == '1'?", surtax_yorn ='$surtax_yorn' ":"");
				$sql .= ($check_infos[update_check_product_type] == '1'?", product_type ='$product_type' ":"");
				$sql .= ($check_infos[update_check_sell_priod_date] == '1'?", sell_priod_sdate='$sell_priod_sdate' ":"");
				$sql .= ($check_infos[update_check_sell_priod_date] == '1'?", sell_priod_edate='$sell_priod_edate' ":"");
				$sql .= ($check_infos[update_check_allow_order_type] == '1'?", allow_order_type='$allow_order_type' ":"");
				$sql .= ($check_infos[update_check_allow_order_cnt_byonesell] == '1'?", allow_order_cnt_byonesell ='$allow_order_cnt_byonesell' ":"");
				$sql .= ($check_infos[update_check_og_ix] == '1'?", origin = '$origin'":"");
				$sql .= ($check_infos[update_check_make_date] == '1'?", make_date = '$make_date'":"");
				$sql .= ($check_infos[update_check_expiry_date] == '1'?", expiry_date = '$expiry_date'":"");
				$sql .= ($check_infos[update_check_stock_use_yn] == '1'?", stock_use_yn = '$stock_use_yn'":"");

				$sql .= ($check_infos[update_check_is_adult] == '1'?", is_adult = '$is_adult'":"");
				$sql .= ($check_infos[update_check_is_sell_date] == '1'?", is_sell_date = '$is_sell_date'":"");
				$sql .= ($check_infos[update_check_allow_max_cnt] == '1'?", wholesale_allow_max_cnt = '$wholesale_allow_max_cnt'":"");
				$sql .= ($check_infos[update_check_allow_max_cnt] == '1'?", allow_max_cnt = '$allow_max_cnt'":"");

				$sql .= ($check_infos[update_check_allow_basic_cnt] == '1'?", wholesale_allow_basic_cnt = '$wholesale_allow_basic_cnt'":"");
				$sql .= ($check_infos[update_check_allow_basic_cnt] == '1'?", allow_basic_cnt = '$allow_basic_cnt'":"");

				$sql .= ($check_infos[update_check_product_weight] == '1'?", product_weight = '$product_weight'":"");

				$sql .= ($check_infos[update_check_wholesale_allow_byoneperson_cnt] == '1'?", allow_byoneperson_cnt = '$allow_byoneperson_cnt'":"");
				$sql .= ($check_infos[update_check_wholesale_allow_byoneperson_cnt] == '1'?", wholesale_allow_byoneperson_cnt = '$wholesale_allow_byoneperson_cnt'":"");

				$sql .= $where_state_div;
				$sql .= $commision_str;
				$sql .= $english_set_update;
				$sql .= $where_state;
		$sql .= "	Where 
				id = '$id' ";
	$db->query($sql);

	//배송템플릿 저장 시작 2014-05-13 이학봉
	//Insert_product_delivery($dt_ix,$company_id,$id,$delivery_policy);
	//배송템플릿 저장 끝 2014-05-13 이학봉

	//상품기본 배송비 추가 2014-07-31 이학봉 (네이버 , 다음 연동시 기본배송비 노출)
	//$product_basic_delivery_price = PorudctBasicDeliveryPrice($id);	//상품기본 배송비 구하기 
	//$sql = "update shop_product set delivery_price = '".$product_basic_delivery_price."' where id = '".$id."'";
	//$db->query($sql);
	//상품기본 배송비 추가 2014-07-31 이학봉

	if(in_array($product_type,$sns_product_type)){

		if($check_infos[update_check_category] == '1' ){
			$db->query("update ".TBL_SNS_PRODUCT_RELATION." set insert_yn = 'N' where pid = '$id'");
			for($i=0;$i<count($display_category);$i++){
				if($display_category[$i] == $basic){
					$category_basic = 1;
				}else{
					$category_basic = 0;
				}
				$sql = "select rid from ".TBL_SNS_PRODUCT_RELATION." where pid = '$id' and cid = '".$display_category[$i]."' ";
				$db->query($sql);
				$db->fetch();
				if($db->total){
					$db->query("update ".TBL_SNS_PRODUCT_RELATION." set insert_yn = 'Y' , basic='$category_basic' where rid = '".$db->dt[rid]."'");
				}else{
					$db->query("insert into ".TBL_SNS_PRODUCT_RELATION." (rid, cid, pid, disp, basic,insert_yn, regdate ) values ('','".$display_category[$i]."','".$id."','1','".$category_basic."','Y',NOW())");
				}
			}
			$db->query("delete from ".TBL_SNS_PRODUCT_RELATION." where pid = '$id' and insert_yn = 'N'");
			$db->query("select count(*) as total from ".TBL_SNS_PRODUCT_RELATION." where pid = '$id' ");
			$db->fetch();

			if($db->dt[total] > 0){
				$db->query("update ".TBL_SNS_PRODUCT." set reg_category = 'Y' where id = '$id' ");
			}else{
				$db->query("update ".TBL_SNS_PRODUCT." set reg_category = 'N' where id = '$id' ");
			}
		}

	}else{
		
		if($check_infos[update_check_category] == '1'){
			$db->query("update ".TBL_SHOP_PRODUCT_RELATION." set insert_yn = 'N' where pid = '$id'");
			for($i=0;$i<count($display_category);$i++){
				if($display_category[$i] == $basic){
					$category_basic = 1;
				}else{
					$category_basic = 0;
				}
				$sql = "select rid from ".TBL_SHOP_PRODUCT_RELATION." where pid = '$id' and cid = '".$display_category[$i]."' ";
				$db->query($sql);
				$db->fetch();
				if($db->total){
					$db->query("update ".TBL_SHOP_PRODUCT_RELATION." set insert_yn = 'Y' , basic='$category_basic' where rid = '".$db->dt[rid]."'");
				}else{
					if(strlen($display_category[$i]) == '15'){
						$db->sequences = "SHOP_GOODS_LINK_SEQ";
						$db->query("insert into ".TBL_SHOP_PRODUCT_RELATION." (rid, cid, pid, disp, basic,insert_yn, regdate ) values ('','".$display_category[$i]."','".$id."','1','".$category_basic."','Y',NOW())");
					}
				}
			}
			$db->query("delete from ".TBL_SHOP_PRODUCT_RELATION." where pid = '$id' and insert_yn = 'N'");
			$db->query("select count(*) as total from ".TBL_SHOP_PRODUCT_RELATION." where pid = '$id' ");
			$db->fetch();

			if($db->dt[total] > 0){
				$db->query("update ".TBL_SHOP_PRODUCT." set reg_category = 'Y' where id = '$id' ");
			}else{
				$db->query("update ".TBL_SHOP_PRODUCT." set reg_category = 'N' where id = '$id' ");
			}
		}

	}

	//상품 가격정보 수정 로그 테이블
	if($sellprice != $bsellprice || $coprice != $bcoprice ||  $reserve != $breserve ||  $listprice != $blistprice || $wholesale_sellprice != $bwholesale_sellprice || $wholesale_price != $bwholesale_price){
		$sql = "INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, listprice, sellprice, coprice, reserve,  company_id, charger_info,regdate,wholesale_price,wholesale_sellprice) ";
		$sql = $sql." values('', '$id','$listprice','$sellprice', '$coprice', '$reserve',  '".$admininfo[company_id]."','[".$admininfo[company_name]."] ".$admininfo[charger]."(".$admininfo[charger_id].")',NOW(),'".$wholesale_price."','".$wholesale_sellprice."') ";
		$db->sequences = "SHOP_PRICEINFO_SEQ";
		$db->query($sql);
	}
	
	$pid = $id;
	
	if($check_infos[update_check_mandatory_info] == '1'){
		//상품필수고시 - START
		$sql = "update shop_product_mandatory_info set insert_yn='N' where pid = '".$pid."' ";
		$db->query($sql);

		if(is_array($mandatory_info)){
			foreach ($mandatory_info as $m_info){
				if($m_info[pmi_ix] != "" && false){
					if($m_info[pmi_title] !="" || $m_info[pmi_code] !="" ){

						$sql = "update shop_product_mandatory_info set pmi_code='".$m_info[pmi_code]."', pmi_title='".$m_info[pmi_title]."',pmi_desc='".$m_info[pmi_desc]."',insert_yn='Y' where pmi_ix = '".$m_info[pmi_ix]."' ";
						$db->query($sql);
					}
				}else{
					if($m_info[pmi_title] !="" || $m_info[pmi_desc] !="" ){
						$sql = "insert into shop_product_mandatory_info(pmi_ix,pid,pmi_code,pmi_title,pmi_desc,insert_yn,regdate) values('','".$pid."','".$m_info[pmi_code]."','".$m_info[pmi_title]."','".$m_info[pmi_desc]."','Y',NOW())";
						//$db->sequences = "SHOP_GOODS_MANDATORY_INFO_SEQ";
						$db->query($sql);
					}
				}
			}
		}

		$sql = "delete from shop_product_mandatory_info where pid = '".$pid."' and insert_yn='N' ";
		$db->query($sql);
		//상품필수고시 - END
	}


    if($check_infos[update_check_mandatory_info_global] == '1'){
        //상품필수고시(영문) - START
        $sql = "update shop_product_mandatory_info_global set insert_yn='N' where pid = '".$pid."' ";
        $db->query($sql);

        if(is_array($mandatory_info_global)){
            foreach ($mandatory_info_global as $m_info){
                if($m_info[pmi_ix] != "" && false){
                    if($m_info[pmi_title] !="" || $m_info[pmi_code] !="" ){

                        $sql = "update shop_product_mandatory_info_global set pmi_code='".$m_info[pmi_code]."', pmi_title='".$m_info[pmi_title]."',pmi_desc='".$m_info[pmi_desc]."',insert_yn='Y' where pmi_ix = '".$m_info[pmi_ix]."' ";
                        $db->query($sql);
                    }
                }else{
                    if($m_info[pmi_title] !="" || $m_info[pmi_desc] !="" ){
                        $sql = "insert into shop_product_mandatory_info_global (pmi_ix,pid,pmi_code,pmi_title,pmi_desc,insert_yn,regdate) values('','".$pid."','".$m_info[pmi_code]."','".$m_info[pmi_title]."','".$m_info[pmi_desc]."','Y',NOW())";
                        //$db->sequences = "SHOP_GOODS_MANDATORY_INFO_SEQ";
                        $db->query($sql);
                    }
                }

            }
        }

        $sql = "delete from shop_product_mandatory_info_global where pid = '".$pid."' and insert_yn='N' ";
        $db->query($sql);
        //상품필수고시(영문) - END
    }

	if($check_infos[update_check_option_name] == '1'){		//가격재고관리 옵션명으로 체크 함 

		batch_OptionUpdate($db, $pid, $stock_options[0],"b");		//가격재고관리 옵션 수정
	}


	if($check_infos[update_check_basic_option_name] == '1'){
	//상품기본옵션 2014-08-09 이학봉
	if($option_all_use == "Y"){	
		$sql = "select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ";
		$db->query($sql);

		if($db->total){
			$del_options = $db->fetchall();

			for($i=0;$i < count($del_options);$i++){
				$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$del_options[$i][opn_ix]."' and pid = '$pid' ");
			}
		}
		$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ");

	}else{

		$db->query("update  ".TBL_SHOP_PRODUCT_OPTIONS." set insert_yn = 'N'  where pid = '$pid' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ");

		if(is_array($options)){
			foreach($options as $ops_key=>$ops_value) {

				if($options[$ops_key]["option_name"]){

					$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and opn_ix = '".$options[$ops_key]["opn_ix"]."' and option_kind in ('c1','c2','i1','i2','p','s','r','g')  ");

					if($options[$ops_key]["option_use"]){
						$options_use = $options[$ops_key]["option_use"];
					}else{
						$options_use = 0;
					}

					if($db->total){
						$db->fetch();
						$opn_ix = $db->dt[opn_ix];
						$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
									option_name='".trim($options[$ops_key]["option_name"])."',
									option_kind='".$options[$ops_key]["option_kind"]."',
									option_type='".$options[$ops_key]["option_type"]."',
									option_use='".$options_use."',
									insert_yn='Y'
								where
									opn_ix = '".$opn_ix."' ";//
						$db->query($sql);

					}else{
						$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate,insert_yn)
										VALUES
										('','$pid','".$options[$ops_key]["option_name"]."','".$options[$ops_key]["option_kind"]."','".$options[$ops_key]["option_type"]."','".$options_use."',NOW(), 'Y')";
						$db->sequences = "SHOP_GOODS_OPTIONS_SEQ";
						$db->query($sql);

						if($db->dbms_type == "oracle"){
							$opn_ix = $db->last_insert_id;
						}else{
							$opn_ix = $db->insert_id();
						}
					}
					

					$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N' where pid='".$pid."' and opn_ix='".$opn_ix."' ";
					$db->query($sql);

					foreach($options[$ops_key]["details"] as $od_key=>$od_value){
						if($options[$ops_key][details][$od_key][option_div]){
								$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($options[$ops_key][details][$od_key][option_div])."' and opn_ix = '".$opn_ix."' ");

								if($db->total){
									$db->fetch();
									$opd_ix = $db->dt[id];
									$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
											option_div='".$options[$ops_key][details][$od_key][option_div]."',
											option_code='".$options[$ops_key][details][$od_key][code]."',
											option_coprice='".$options[$ops_key][details][$od_key][coprice]."',
											option_price='".$options[$ops_key][details][$od_key][price]."',
											option_soldout='".$options[$ops_key][details][$od_key][option_soldout]."',
											option_etc1='".$options[$ops_key][details][$od_key][etc1]."',
											option_stock='0', option_safestock='0' ,
											insert_yn='Y'
											where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";
											//
									//option_useprice='".$options[$ops_key][details][$od_key][price]."',  2012-11-06 홍진영(char 1 이기 때문에 오라클에서 에러남)
								}else{
									$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div, option_code,option_coprice,option_price, option_stock, option_safestock, option_soldout, option_etc1, insert_yn) ";
									$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$ops_key][details][$od_key][option_div])."','".$options[$ops_key][details][$od_key][code]."','".$options[$ops_key][details][$od_key][coprice]."','".$options[$ops_key][details][$od_key][price]."','0','0','".$options[$ops_key][details][$od_key][option_soldout]."','".$options[$ops_key][details][$od_key][etc1]."', 'Y') ";
								}
								$db->sequences = "SHOP_GOODS_OPTIONS_DT_SEQ";
								$db->query($sql);
						}
					}
					
				
						$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ");
					

				}

				/**
				* 2013.06.09 신훈식
				* loop 안에 밖에 있는 부분을 loop 안으로 이동
				* select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and insert_yn = 'N'  쿼리 부분에 opn_ix 값 추가
				**/
				$sql = "select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and opn_ix = '".$options[$ops_key]["opn_ix"]."'  and insert_yn = 'N' and option_kind in ('c1','c2','i1','i2','p','s','r','g')  ";

				$db->query($sql);
				if($db->total){
					$del_options = $db->fetchall();
					for($i=0;$i < count($del_options);$i++){
						$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$del_options[$i][opn_ix]."' and pid = '$pid'  ");
					}
				}
				$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and opn_ix = '".$options[$ops_key]["opn_ix"]."'  and insert_yn = 'N' and option_kind in ('c1','c2','i1','i2','p','s','r','g')  ");
			}
		}

		// 옵션 처리후 삭제되야 되는 옵션 정리
			$sql = "select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and insert_yn = 'N' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ";
			//echo $sql."<br><br>";
			$db->query($sql);
			if($db->total){
				$del_options = $db->fetchall();
				//print_r($del_options);
				for($i=0;$i < count($del_options);$i++){
					$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$del_options[$i][opn_ix]."' and pid = '$pid' ");
				}
			}
			$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and insert_yn = 'N' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ");
		
		
	}// option_all_use 있는지 여부

	}


	if($check_infos[update_check_display_option] == '1'){
		if($display_options){	//디스플레이 옵션
			$sql = "update ".TBL_SHOP_PRODUCT_DISPLAYINFO." set insert_yn='N'	where pid = '$pid' ";
			$db->query($sql);
			//for($i=0;$i < count($_POST["display_options"]);$i++){
			foreach($display_options as $do_key=>$do_value) {
				if($display_options[$do_key]["dp_title"] && $display_options[$do_key]["dp_desc"]){

					if($display_options[$do_key]["dp_use"]){
						$dp_use = $display_options[$do_key]["dp_use"];
					}else{
						$dp_use = "0";
					}

					$dp_ix=$display_options[$do_key]["dp_ix"];//디스플레이 옵션 수정 kbk 12/06/19
					if($dp_ix!="0" && $dp_ix!="") {//디스플레이 옵션 수정 kbk 12/06/19

						$sql = "update ".TBL_SHOP_PRODUCT_DISPLAYINFO." set
										dp_title = '".$display_options[$do_key]["dp_title"]."',
										dp_desc = '".$display_options[$do_key]["dp_desc"]."',
										dp_etc_desc = '".$display_options[$do_key]["dp_etc_desc"]."',
										insert_yn = 'Y' ,dp_use = '".$dp_use."'
										where dp_ix = '$dp_ix'";
					}else{
						$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." (dp_ix,pid,dp_title,dp_desc,dp_etc_desc,dp_use, regdate) values('','$pid','".$display_options[$do_key]["dp_title"]."','".$display_options[$do_key]["dp_desc"]."','".$display_options[$do_key]["dp_etc_desc"]."','".$dp_use."',NOW()) ";
					}
					$db->sequences = "SHOP_GOODS_DISPLAYINFO_SEQ";
					$db->query($sql);
				}
			}
			$db->query("delete from ".TBL_SHOP_PRODUCT_DISPLAYINFO." where pid = '$pid' and insert_yn = 'N' ");
		}
	}


	if($check_infos[update_check_virals] == '1'){
		if($virals){	//바이럴
			//$db->debug = true;
			$sql = "update shop_product_viralinfo set insert_yn='N'	where pid = '$pid' ";
			$db->query($sql);
			//for($i=0;$i < count($_POST["virals"]);$i++){
			foreach($virals as $do_key=>$do_value) {
				if($virals[$do_key]["viral_name"] && $virals[$do_key]["viral_url"]){
					if($virals[$do_key]["vi_use"]){
						$vi_use = $virals[$do_key]["vi_use"];
					}else{
						$vi_use = "0";
					}

					$vi_ix=$virals[$do_key]["vi_ix"];//디스플레이 옵션 수정 kbk 12/06/19
					if($vi_ix!="0" && $vi_ix!="") {//디스플레이 옵션 수정 kbk 12/06/19

						$sql = "update shop_product_viralinfo set
										viral_name = '".$virals[$do_key]["viral_name"]."',viral_url = '".$virals[$do_key]["viral_url"]."',
										viral_desc = '".$virals[$do_key]["viral_desc"]."', insert_yn = 'Y' ,vi_use = '".$vi_use."'
										where vi_ix = '$vi_ix'";
					}else{
						$sql = "insert into shop_product_viralinfo (vi_ix,pid,viral_name,viral_url,viral_desc, vi_use, regdate) values('','$pid','".$virals[$do_key]["viral_name"]."','".$virals[$do_key]["viral_url"]."','".$virals[$do_key]["viral_desc"]."','".$vi_use."',NOW()) ";
						//echo $sql;

					}
					$db->sequences = "SHOP_GOODS_VIRALINFO_SEQ";
					$db->query($sql);
				}
			}
			$db->query("delete from shop_product_viralinfo where pid = '$pid' and insert_yn = 'N' ");
		}
	}


	//기본 정책이 무료인정책 뽑아내기
	$wholesale_free_delivery_yn=0;
	$free_delivery_yn=0;
	$sql="select 
			pd.is_wholesale
		from 
			shop_product_delivery as pd 
			inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
		where
			pd.pid='".$pid."' and dt.delivery_policy = '1'
		group by pd.is_wholesale ";
	$db->query($sql);

	if($db->total){
		$db->fetch(0);
		if($db->dt[is_wholesale]=="W"){
			$wholesale_free_delivery_yn=1;
		}
		$db->fetch(1);
		if($db->dt[is_wholesale]=="R"){
			$free_delivery_yn=1;
		}
	}

	//추가 정보 입력하기!!
	$sql = "select
				GROUP_CONCAT(pod.option_div SEPARATOR '|') as option_div_text
			from 
				shop_product_options po 
				left join shop_product_options_detail pod on (po.pid = pod.pid and po.opn_ix = pod.opn_ix)
			where 
				po.pid='".$pid."' and po.option_use = '1'";
	$db->query($sql);
	$db->fetch(0);
	$option_div_text = $db->dt[option_div_text];


	$sql="update shop_product_addinfo set option_div_text='".$option_div_text."', wholesale_free_delivery_yn='".$wholesale_free_delivery_yn."', free_delivery_yn='".$free_delivery_yn."' where pid='".$pid."' ";
	$db->query($sql);
	
}


function batch_OptionUpdate($mdb, $pid, $_options, $option_kind="b"){
	//$mdb->debug = true;
	//print_r($_options);
		if($_options["option_name"]){
			
			$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set insert_yn = 'N' where pid = '".$pid."'  and option_kind = '".$_options["option_kind"]."' ";
			$mdb->query($sql);
	
			if($_options["opn_ix"]){
				$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '".$pid."' and opn_ix = '".trim($_options["opn_ix"])."' and option_kind = '".$_options["option_kind"]."' ");
			}else{
				$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '".$pid."' and option_name = '".trim($_options["option_name"])."' and option_kind = '".$_options["option_kind"]."' ");
			}

			if($_options["option_use"]){
				$_options_use = $_options["option_use"];
			}else{
				$_options_use = 0;
			}

			if($mdb->total){
				$mdb->fetch();
				$opn_ix = $mdb->dt[opn_ix];

				$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
								option_name='".trim($_options["option_name"])."', 
								option_kind='".$_options["option_kind"]."', 
								option_type='".$_options["option_type"]."',
								option_use='".($_options["option_use"] == "" ? "0":"1")."',
								box_total='".$_options["box_total"]."',
								text_option_use = '".($_options["text_option_use"] == "" ? "0":"1")."',
								file_option_use = '".($_options["file_option_use"] == "" ? "0":"1")."',
								insert_yn = 'Y'
						where 
								pid = '".$pid."'
								and opn_ix = '".$opn_ix."' 
								and option_kind = '".$_options["option_kind"]."'";
								//echo nl2br($sql)."<br>";
				$mdb->query($sql);
			}else{
				$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, box_total, regdate,insert_yn)
								VALUES
								('','$pid','".$_options["option_name"]."','".$_options["option_kind"]."','".$_options["option_type"]."','".($_options["option_use"] == "" ? "0":"1")."','".$_options["box_total"]."',NOW(),'Y')";
				$mdb->sequences = "SHOP_GOODS_OPTIONS_SEQ";

				$mdb->query($sql);


				if($mdb->dbms_type == "oracle"){
					$opn_ix = $mdb->last_insert_id;
				}else{
					$opn_ix = $mdb->insert_id();
				}

			}
			
			$sql = "select * from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '".$pid."' and option_kind = '".$option_kind."'";
			$mdb->query($sql);
			$options_info = $mdb->fetchall();

			for($k=0;$k<count($options_info);$k++){
				$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N' where pid = '".$pid."' and opn_ix='".$options_info[$k][opn_ix]."' ";
				$mdb->query($sql);
			}
			$option_stock_yn = "";
			
			$jj = 0;
			foreach($_options["details"] as $key => $details){	//옵션 키값이 일정하지 않음으로 for문을 쓰면안됨 2014-06-14 이학봉
				$_option_detail = $details;

					if($_option_detail[option_div]){

						$custom_option_div = customOptionDivDivision($_option_detail[option_div]);
						$_option_detail[option_color] = $custom_option_div['color'];
						$_option_detail[option_size] = $custom_option_div['size'];

						$mdb->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($_option_detail[option_div])."' and opn_ix = '".$opn_ix."' and option_code = '".trim($_option_detail[code])."' ");

						if($mdb->total){
							$mdb->fetch();
							$opd_ix = $mdb->dt[id];

							$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
										option_div='".$_option_detail[option_div]."',
										set_group_seq = '".$jj."',
										option_code='".$_option_detail[code]."',
										option_gid='".$_option_detail[gid]."',
										option_coprice='".$_option_detail[coprice]."',
										option_listprice='".$_option_detail[listprice]."',
										option_price='".$_option_detail[sellprice]."',
										option_wholesale_listprice='".$_option_detail[wholesale_listprice]."',
										option_wholesale_price='".$_option_detail[wholesale_price]."',
										option_stock='".$_option_detail[stock]."',
										option_safestock='".$_option_detail[safestock]."' ,
										option_soldout='".$_option_detail[soldout]."' ,
										option_barcode='".$_option_detail[barcode]."' ,
										option_surtax_div = '".$_option_detail[option_surtax_div]."',
										option_color = '".$_option_detail[option_color]."',
										option_size = '".$_option_detail[option_size]."',
										insert_yn='Y'
										where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";
						}else{
							$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." 
										(id, pid, opn_ix, option_div, option_code,option_coprice,option_listprice, option_price, option_wholesale_listprice,  option_wholesale_price, option_stock, option_safestock, option_soldout, option_barcode, insert_yn, regdate,option_surtax_div, option_gid, set_group_seq, option_color, option_size) 
										values
										('','$pid','$opn_ix','".$_option_detail[option_div]."','".$_option_detail[code]."','".$_option_detail[coprice]."','".$_option_detail[listprice]."','".$_option_detail[sellprice]."','".$_option_detail[wholesale_listprice]."','".$_option_detail[wholesale_price]."','".$_option_detail[stock]."','".$_option_detail[safestock]."','".$_option_detail[soldout]."', '".$_option_detail[barcode]."' ,'Y', NOW(),'".$_option_detail[option_surtax_div]."','".$_option_detail[gid]."','".$jj."','".$_option_detail[option_color]."','".$_option_detail[option_size]."') ";
						}
						$mdb->sequences = "SHOP_GOODS_OPTIONS_DT_SEQ";
						$mdb->query($sql);

						if($_option_detail[stock] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
							$option_stock_yn = "N";
						}

						if($_option_detail[stock] < $_option_detail[safestock] && $option_stock_yn == ""){
							$option_stock_yn = "R";
						}
					//}
				}

				$jj++;
			}

			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '".$pid."' and option_kind = '".$_options["option_kind"]."' and insert_yn = 'N' ";
			$mdb->query($sql);
			
			for($k=0;$k<count($options_info);$k++){
				$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where pid = '".$pid."' and opn_ix='".$options_info[$k][opn_ix]."' and insert_yn = 'N' ";
				$mdb->query($sql);
			}

			if($_options["option_kind"] == "b"){
				$mdb->query("SELECT sum(option_stock) as option_stock,sum(option_safestock) as option_safestock  FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE opn_ix='$opn_ix'");
				$mdb->fetch();
				$option_stock = $mdb->dt[option_stock];
				$option_safestock = $mdb->dt[option_safestock];
				if($sell_ing_cnt == ""){
					$sell_ing_cnt = 0;
				}
				$mdb->query("update ".TBL_SHOP_PRODUCT." set stock = '".$option_stock."' ,safestock = '$option_safestock' where id ='$pid'");
				if($option_stock_yn){
					$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = '$option_stock_yn' where id ='$pid'");
				}
			}

		}else{	//옵션명을 삭제햇을경우 해당 상품의 가격재고 옵션 전부 삭제됨 2014-08-10 이학봉 확인

			$mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '".$pid."' and option_kind = '".$option_kind."'");

			if($mdb->total){
				$mdb->fetch();
				$opn_ix = $mdb->dt[0];
				$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."'  ";
				$mdb->query($sql);
			}
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '".$pid."' and option_kind = '".$option_kind."' ";
			$mdb->query($sql);

			$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'N' where id ='$pid'");
		}
		$mdb->debug = false;
}

?>
