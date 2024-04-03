<?
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

session_start();
$db = new Database;
$db2 = new Database;


if($act == 'insert' || $act == "tmp_insert"){	//상품추가

	$db->query("SELECT max(vieworder)+1 as max_vieworder FROM shop_product_temp ");
	$db->fetch();
	$vieworder = $db->dt[max_vieworder];

	$data_text_convert = $basicinfo;
	$m_data_text_convert = $m_basicinfo;

	if($one_commission == ""){	//셀러업체일경우 빈값:  개별수수료는 사용안함으로 되어야함 2014-08-11 이학봉
		$one_commission = "N";
	}else{
		$one_commission = $one_commission;
	}

	if($admininfo[admin_level] == 9){
		if($state == ""){
			$state = "1";
		}
		if($disp == ""){
			$disp = "1";
		}
		if($mode == "copy"){
			if($admin!=""){
				$company_id = $admin;
			}
		}else{
			if($admin == ""){
				if($company_id ==""){
					$company_id = $admininfo[company_id];
				}
			}else{
				if($company_id ==""){
					$company_id = $admin;
				}
			}
		}
	}else{
		$state = "6";
		$disp = "1";
		$company_id = $admininfo[company_id];
	}

	if($b_ix !=""){
		$brand = $b_ix;
	}
	if($brand_name != ""){
		$brand_name = $brand_name;
	}else{
		$sql = "select brand_name from shop_brand where b_ix = '$brand'";
		$db->query($sql);
		$db->fetch();
		$brand_name = $db->dt[brand_name];
	}

	$brand_name = str_replace("'","\\'",$brand_name);

	if(is_array($display_category)){	//상품미분류 여부 
		$reg_category = "Y";	
	}else{
		$reg_category = "N";
	}
	if($reserve_yn == ""){
		$reserve_yn = "N";
	}

	if($rate_type == ""){
		$rate_type = "N";
	}

	for($i=0;$i<count($icon_check);$i++){
		if($i < count($icon_check)-1){
			$icons .= $icon_check[$i].";";
		}else{
			$icons .= $icon_check[$i];
		}
	}
	$sns_btn = serialize($sns_btn);

	if($delivery_policy == ""){	//배송타입이 없을경우 입점업체배송으로 체크 2014-07-21 이학봉
		$delivery_policy = "1";
	}

	if($sns_btn_yn == ""){	//sns 사용값이 빈값일경우 미사용으로 체크 
		$sns_btn_yn = "N";
	}
	
	if($surtax_yorn == ""){	//면세여부 빈값으로 넘어올경우 과세로체크 2014-07-21 이학봉
		$surtax_yorn = "N";
	}

	if($delivery_type == ""){	//배송타입이 2일경우 입점업체 배송으로 체크 2014-07-21 이학봉
		$delivery_type = "2";
	}

	if($is_sell_date == ""){	//판매기간이 빈값일경우 미설정으로 체크 2014-07-21 이학봉
		$is_sell_date = "0";
	}

	if($allow_order_type == ""){	//한정판매수량여부 값이 빈값이 경우 미적용으로 처리 2014-07-21 이학봉
		$allow_order_type = "0";
	}

	if($relation_display_type == ""){	//관련상품 노출타입 빈값일 경우 수동으로 체크 2014-07-21 이학봉
		$relation_display_type = "M";
	}

	// 상품등록일자를 왜 이렇게 처리했는지 확인필요
	if($regdate == ""  || $regdate == "NOW()" || $regdate == "0000-00-00 00:00:00"){
		$regdate = "NOW()";
	}else{
		$regdate = "'".$regdate."'";
	}

	if($editdate == "" || $editdate == "0000-00-00 00:00:00"){
		if($db->dbms_type == "oracle"){
			$editdate = "sysdate";
		}else{
			//$editdate = "0000-00-00 00:00:00";
			$editdate = "NOW()";
		}
	}else{
		$editdate = "'".$editdate."'";
	}

	if($mandatory_type_1 !=""){
		$mandatory_type = $mandatory_type_1."|".$mandatory_type_2;	// 상품고시 제대로 substr 되지 않아서 | 구분값으로 분리 시킴 2013-06-05 이학봉
	}else{
		$mandatory_type ="";
	}

	if($product_type=="99") $coupon_use_yn="N";//세트 상품은 쿠폰 사용 못하도록 고정. goods_input.php 에서 사용안함으로 체크하나 disabled 속성으로 처리하기에 값이 넘어오지 않음. N 으로 고정시킴 kbk 13/07/17
	
	$delivery_company = "MI"; // 2013년 08월 09일 신훈식 현재사용하지 않아 고정값으로 픽스

	if($admininfo[admin_level] != '9'){	//셀러일경우 웹/모바일상품 구분은 기본으로 전체로 선택함 2014-08-19 이학봉
		$is_mobile_use = 'A';	
	}
	
	if($is_mobile_use == ''){	//다른경로로 들어온 상품중에 빈값이면 강제로 A로 지정
		$is_mobile_use = 'A';
	}


	$sql = "INSERT INTO shop_product_temp
					(id,  pname,pcode, brand,brand_name, company, buying_company, paper_pname,  shotinfo,  buyingservice_coprice, wholesale_price, wholesale_sellprice, listprice,sellprice,  coprice,wholesale_reserve_yn, wholesale_reserve,wholesale_reserve_rate,wholesale_rate_type, reserve_yn, reserve,reserve_rate,rate_type, sns_btn_yn, sns_btn,  bimg, basicinfo,m_basicinfo,  icons,state, disp,product_type, movie, vieworder, admin,stock,safestock,available_stock, search_keyword,reg_category,  surtax_yorn,delivery_company,one_commission,commission,cupon_use_yn,stock_use_yn,supply_company, inventory_info,delivery_policy,delivery_product_policy,delivery_package,delivery_price,free_delivery_yn,free_delivery_count,
					sell_priod_sdate,sell_priod_edate,allow_order_type,allow_order_cnt_byonesell,allow_order_cnt_byoneperson,allow_order_minimum_cnt,origin,make_date,expiry_date,mandatory_type, relation_product_cnt, relation_display_order_type, relation_display_type, 
					etc2, etc10, download_img, download_desc, bs_goods_url, bs_site, currency_ix, hotcon_event_id, hotcon_pcode, co_goods, co_pid, co_company_id, editdate, regdate,wholesale_yn,offline_yn,is_pos_link, add_status,trade_admin,md_code,barcode,delivery_type,delivery_coupon_yn,coupon_use_yn,account_type,wholesale_commission,reg_charger_ix,reg_charger_name,is_adult,remain_stock,is_sell_date,wholesale_allow_max_cnt,allow_max_cnt,wholesale_allow_basic_cnt,allow_basic_cnt,md_one_commission,md_discount_name,md_sell_date_use,md_sell_priod_sdate,md_sell_priod_edate,whole_head_company_sale_rate,whole_seller_company_sale_rate,head_company_sale_rate,seller_company_sale_rate,c_ix,relation_display_order_date,product_weight,allow_byoneperson_cnt,wholesale_allow_byoneperson_cnt,is_mobile_use)

					values
					
					('', '".strip_tags(trim($pname))."','$pcode', '$brand','$brand_name','$company','$buying_company', '$paper_pname', '$shotinfo', '$buyingservice_coprice','$wholesale_price','$wholesale_sellprice','$listprice','$sellprice', '$coprice','$wholesale_reserve_yn', '$wholesale_reserve','$wholesale_rate1','$wholesale_rate_type','$reserve_yn', '$reserve','$rate1','$rate_type','$sns_btn_yn','$sns_btn',  '$bimg_text','$basicinfo','$m_basicinfo','$icons', $state, '$disp','$product_type', '$movie', '$vieworder', '$company_id','$stock','$safestock','$available_stock','$search_keyword','$reg_category','$surtax_yorn','$delivery_company','$one_commission','$commission','$cupon_use_yn','$stock_use_yn','$supply_company','$inventory_info','$delivery_policy','$delivery_product_policy','$delivery_package','$delivery_price','$free_delivery_yn','$free_delivery_count',
					'$sell_priod_sdate','$sell_priod_edate','$allow_order_type','$allow_order_cnt_byonesell','$allow_order_cnt_byoneperson','$allow_order_minimum_cnt','$origin','$make_date','$expiry_date','$mandatory_type','$relation_product_cnt','$relation_display_order_type','$relation_display_type',
					'$etc2','$etc10','$download_img','$download_desc','$bs_goods_url','$bs_site','$currency_ix','$hotcon_event_id', '$hotcon_pcode','$co_goods','$co_pid','$co_company_id', '".$editdate."',".$regdate.",'".$wholesale_yn."','".$offline_yn."','N','I','$trade_admin','$md_code','$barcode','$delivery_type','$delivery_coupon_yn','$coupon_use_yn','$account_type','$wholesale_commission','".$_SESSION["admininfo"]["charger_ix"]."','".$_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")','$is_adult','$remain_stock','$is_sell_date','$wholesale_allow_max_cnt','$allow_max_cnt','$wholesale_allow_basic_cnt','$allow_basic_cnt','$md_one_commission','$md_discount_name','$md_sell_date_use','$md_sell_priod_sdate','$md_sell_priod_edate','$whole_head_company_sale_rate','$whole_seller_company_sale_rate','$head_company_sale_rate','$seller_company_sale_rate','$c_ix','$relation_display_order_date','$product_weight','$allow_byoneperson_cnt','$wholesale_allow_byoneperson_cnt','$is_mobile_use') ";

	$db->query($sql);
	

	$db->query("SELECT id FROM shop_product_temp WHERE id=LAST_INSERT_ID()");
	$db->fetch();
	$pid = $db->dt[0];

//코디옵션 추가 작업 시작 2014-01-09 이학봉
if($product_type=="99"){
	if($use_option_type == "box_option"){
		unset($stock_options);
		unset($set2options);
		unset($codi_options);
	}else if($use_option_type == "set_option"){
		unset($box_options);
		unset($set2options);
		unset($stock_options);
		unset($codi_options);
	}else if($use_option_type == "set2_option"){
		unset($box_options);
		unset($stock_options);
		unset($codi_options);
	}else if($use_option_type == "codi_option"){	//코디옵션
		unset($box_options);
		unset($set2options);
		unset($stock_options);
	}else{
		unset($stock_options);
		unset($set2options);
		unset($box_options);
		unset($codi_options);
	}
}else if($product_type=="4" || $product_type=="21"){
	unset($box_options);
}else if($product_type=="31"){
	unset($box_options);
	unset($set2options);
}else if($product_type=="77"){	//사은품 상품은 세트(묶음상품)옵션만(set2options) 사용 2014-04-09 이학봉
	unset($box_options);		//박스 옵션
	unset($stock_options);		//가격 + 재고관리 옵션
	unset($codi_options);		//코디 옵션 
}else{
	unset($box_options);
	unset($set2options);
	//unset($stock_options); 2013027 Hong SNS 상품등록시 옵션등록 안되서 주석처리
}
	//상품등록시 옵션추가 부분 체크 xuefeng 코디옵션추가 
	OptionUpdate($db, $pid, $stock_options[0],"b");
	OptionUpdate($db, $pid, $box_options[0],"x");
	for($i=0;$i < count($addoptions);$i++){
		//print_r($addoptions[$i]);
		OptionUpdate($db, $pid, $addoptions[$i],"a");
	}
	CodiOptionUpdate($db, $pid, $codi_options);	//코디옵션
	SetOptionUpdate($db, $pid, $set2options);

//코디옵션 추가 작업 시작 2014-01-09 이학봉

	if($option_all_use == "Y"){
		$sql = "select opn_ix from shop_product_options_temp where pid = '$pid' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ";
		$db->query($sql);
		if($db->total){
			$del_options = $db->fetchall();
			for($i=0;$i < count($del_options);$i++){
				$db->query("delete from shop_product_options_detail where opn_ix='".$del_options[$i][opn_ix]."' and pid = '$pid' ");
			}
		}
		$db->query("delete from shop_product_options_temp where pid = '$pid' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ");
	}else{
	
		if(is_array($options)){
			foreach($options as $ops_key=>$ops_value) {

				if($options[$ops_key]["option_name"]){

					if($options[$ops_key]["option_use"]){
						$options_use = $options[$ops_key]["option_use"];
					}else{
						$options_use = 0;
					}

					$sql = "INSERT INTO shop_product_options_temp (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate)
									VALUES
									('','$pid','".$options[$ops_key]["option_name"]."','".$options[$ops_key]["option_kind"]."','".$options[$ops_key]["option_type"]."','".$options_use."',NOW())";
					$db->sequences = "SHOP_GOODS_OPTIONS_SEQ";
					$db->query($sql);

					if($db->dbms_type == "oracle"){
						$opn_ix = $db->last_insert_id;
					}else{
						$db->query("SELECT opn_ix FROM shop_product_options_temp WHERE opn_ix=LAST_INSERT_ID()");
						$db->fetch();
						$opn_ix = $db->dt[opn_ix];
					}

					$sql = "update shop_product_options_detail_temp set insert_yn='N'	where opn_ix='".$opn_ix."' ";
					$db->query($sql);
					//for($j=0;$j < count($options[$i]["details"]);$j++){
					foreach($options[$ops_key]["details"] as $od_key=>$od_value) {
						if($options[$ops_key][details][$od_key][option_div]){

							$sql = "INSERT INTO shop_product_options_detail_temp (id, pid, opn_ix, option_div, option_code,option_coprice,option_price, option_stock, option_safestock, option_soldout, option_etc1) ";
							$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$ops_key][details][$od_key][option_div])."','".$options[$ops_key][details][$od_key][code]."','".$options[$ops_key][details][$od_key][coprice]."','".$options[$ops_key][details][$od_key][price]."','0','0','".$options[$ops_key][details][$od_key][option_soldout]."','".$options[$ops_key][details][$od_key][etc1]."') ";
		
							$db->sequences = "SHOP_GOODS_OPTIONS_DT_SEQ";
							$db->query($sql);
						}
					}
				}
			}
		}

	}// option_all_use 있는지 여부

	echo "$pid";

}



function OptionUpdate($mdb, $pid, $_options, $option_kind="x"){
	//$mdb->debug = true;
	//print_r($_options);
		if($_options["option_name"]){
			if($_options["opn_ix"]){
				$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE pid = '$pid' and opn_ix = '".trim($_options["opn_ix"])."' and option_kind = '".$_options["option_kind"]."' ");
			}else{
				$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE pid = '$pid' and option_name = '".trim($_options["option_name"])."' and option_kind = '".$_options["option_kind"]."' ");
			}

			if($_options["option_use"]){
				$_options_use = $_options["option_use"];
			}else{
				$_options_use = 0;
			}

			if($mdb->total){
				$mdb->fetch();
				$opn_ix = $mdb->dt[opn_ix];

				$sql = "update  shop_product_options_temp set
								option_name='".trim($_options["option_name"])."', 
								option_kind='".$_options["option_kind"]."', 
								option_type='".$_options["option_type"]."',
								option_use='".($_options["option_use"] == "" ? "0":"1")."',
								box_total='".$_options["box_total"]."',
								text_option_use = '".($_options["text_option_use"] == "" ? "0":"1")."',
								file_option_use = '".($_options["file_option_use"] == "" ? "0":"1")."'
								where opn_ix = '".$opn_ix."' ";
								//echo nl2br($sql)."<br>";
				$mdb->query($sql);
			}else{
				$sql = "INSERT INTO shop_product_options_temp (opn_ix, pid, option_name, option_kind, option_type, option_use, box_total, regdate)
								VALUES
								('','$pid','".$_options["option_name"]."','".$_options["option_kind"]."','".$_options["option_type"]."','".($_options["option_use"] == "" ? "0":"1")."','".$_options["box_total"]."',NOW())";
				$mdb->sequences = "SHOP_GOODS_OPTIONS_SEQ";

				$mdb->query($sql);


				if($mdb->dbms_type == "oracle"){
					$opn_ix = $mdb->last_insert_id;
				}else{
					$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE opn_ix=LAST_INSERT_ID()");
					$mdb->fetch();
					$opn_ix = $mdb->dt[0];
				}

			}
			//echo $sql."<br>";
			//exit;

			$sql = "update shop_product_options_detail_temp set insert_yn='N' where opn_ix='".$opn_ix."' ";
			//echo $sql."<br><br>";
			$mdb->query($sql);
			$option_stock_yn = "";
			//for($i=0;$i < count($_options["details"]);$i++){

			foreach($_options["details"] as $key => $details){	//옵션 키값이 일정하지 않음으로 for문을 쓰면안됨 2014-06-14 이학봉
				$_option_detail = $details;

				//for($j=0;$j < count($_option_details);$j++){
				//	$_option_detail = $_option_details[$j];
					
				//foreach($_option_detail as $opsd_key=>$opsd_value) {
					if($_option_detail[option_div]){
						/*
						if($_option_detail[code] != "" ){
							$sql = "select item_stock, item_safestock from inventory_goods_item where gi_ix='".$_option_detail[code]."' ";
							$mdb->query($dsql);
							if($mdb->total){
								$mdb->fetch();
								$_option_detail[stock] = $mdb->dt[item_stock];
								if($_option_detail[safestock] == "") $_option_detail[safestock] = $mdb->dt[item_safestock];
							}
						}
						*/

						$mdb->query("SELECT id FROM shop_product_options_detail_temp WHERE option_div = '".trim($_option_detail[option_div])."' and opn_ix = '".$opn_ix."' and option_div = '".trim($_option_detail[code])."' ");

						if($mdb->total){
							$mdb->fetch();
							$opd_ix = $mdb->dt[id];

							$sql = "update shop_product_options_detail_temp set
										option_div='".$_option_detail[option_div]."',
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
										insert_yn='Y'
										where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";

						}else{
							$sql = "INSERT INTO shop_product_options_detail_temp 
										(id, pid, opn_ix, option_div, option_code,option_coprice,option_listprice, option_price, option_wholesale_listprice,  option_wholesale_price, option_stock, option_safestock, option_soldout, option_barcode, insert_yn, regdate,option_surtax_div, option_gid) 
										values
										('','$pid','$opn_ix','".$_option_detail[option_div]."','".$_option_detail[code]."','".$_option_detail[coprice]."','".$_option_detail[listprice]."','".$_option_detail[sellprice]."','".$_option_detail[wholesale_listprice]."','".$_option_detail[wholesale_price]."','".$_option_detail[stock]."','".$_option_detail[safestock]."','".$_option_detail[soldout]."', '".$_option_detail[barcode]."' ,'Y', NOW(),'".$_option_detail[option_surtax_div]."','".$_option_detail[gid]."') ";
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
			}
			$sql = "delete from shop_product_options_detail_temp where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
			//echo $sql;
			$mdb->query($sql);
			if($_options["option_kind"] == "b"){
				$mdb->query("SELECT sum(option_stock) as option_stock,sum(option_safestock) as option_safestock  FROM shop_product_options_detail_temp WHERE opn_ix='$opn_ix'");
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

		}else{

			$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE pid = '".$pid."' and option_kind = '".$option_kind."'");

			if($mdb->total){
				$mdb->fetch();
				$opn_ix = $mdb->dt[0];
				$sql = "delete from shop_product_options_detail_temp where opn_ix='".$opn_ix."'  ";
				$mdb->query($sql);
			}
			$sql = "delete from shop_product_options_temp where pid = '".$pid."' and option_kind = '".$option_kind."' ";
			$mdb->query($sql);

			$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'N' where id ='$pid'");
		}
		$mdb->debug = false;
}


function SetOptionUpdate($mdb, $pid, $_options){
	//$mdb->debug = true;
	//print_r($_options);
	if(is_array($_options)){
		$sql = "update  shop_product_options_temp set
					insert_yn = 'N'
					where pid = '$pid'  and option_kind in ('s2','x2') ";
		$mdb->query($sql);

	//for($x=0;$x < count($_options);$x++){
	$x = 0;
	foreach($_options as $key => $option_info){

		if($option_info["option_name"] && $x == 0){
			if($option_info["opn_ix"]){
				$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE pid = '$pid' and opn_ix = '".trim($option_info["opn_ix"])."' and option_kind = '".$option_info["option_kind"]."' ");
			}else{
				$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE pid = '$pid' and option_name = '".trim($option_info["option_name"])."' and option_kind = '".$option_info["option_kind"]."' ");
			}

			if($option_info["option_use"]){
				$_options_use = $option_info["option_use"];
			}else{
				$_options_use = 0;
			}

			if($mdb->total){
				$mdb->fetch();
				$opn_ix = $mdb->dt[opn_ix];

				$sql = "update  shop_product_options_temp set
								option_name='".trim($option_info["option_name"])."', 
								option_kind='".$option_info["option_kind"]."', 
								option_type='".$option_info["option_type"]."',
								option_use='".($option_info["option_use"] == "" ? "0":"1")."',
								box_total='".$option_info["box_total"]."',
								insert_yn = 'Y'
								where opn_ix = '".$opn_ix."' ";
				$mdb->query($sql);
			}else{
				$sql = "INSERT INTO shop_product_options_temp (opn_ix, pid, option_name, option_kind, option_type, option_use, box_total, insert_yn, regdate)
								VALUES
								('','$pid','".$option_info["option_name"]."','".$option_info["option_kind"]."','".$option_info["option_type"]."','".($option_info["option_use"] == "" ? "0":"1")."','".$option_info["box_total"]."','Y',NOW())";
				$mdb->sequences = "SHOP_GOODS_OPTIONS_SEQ";

				$mdb->query($sql);


				if($mdb->dbms_type == "oracle"){
					$opn_ix = $mdb->last_insert_id;
				}else{
					$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE opn_ix=LAST_INSERT_ID()");
					$mdb->fetch();
					$opn_ix = $mdb->dt[0];
				}

			}
			
			$sql = "update shop_product_options_detail_temp set insert_yn='N' where opn_ix='".$opn_ix."' ";
			$mdb->query($sql);
			//$set_group = $_options[$x]["option_kind"];
			$option_stock_yn = "";
		}
			//for($i=0;$i < count($_options[$x]["details"]);$i++){
			if(is_array($option_info["details"])){
				$i = 0;
				//print_r($option_info["details"]);
				foreach($option_info["details"] as $key => $details) {	//옵션 키값이 일정하지 않음으로 for문을 쓰면안됨 2014-06-14 이학봉
					$_option_detail = $details;

					//for($j=0;$j < count($_option_details);$j++){
					//	$_option_detail = $_option_details[$j];
						
					//foreach($_option_detail as $opsd_key=>$opsd_value) {
						if($_option_detail[option_div]){
							/*
							if($_option_detail[code] != "" ){
								$sql = "select item_stock, item_safestock from inventory_goods_item where gi_ix='".$_option_detail[code]."' ";
								$mdb->query($sql);
								if($mdb->total){
									$mdb->fetch();
									$_option_detail[stock] = $mdb->dt[item_stock];
									if($_option_detail[safestock] == "") $_option_detail[safestock] = $mdb->dt[item_safestock];
								}
							}
							*/

							$mdb->query("SELECT id FROM shop_product_options_detail_temp WHERE option_div = '".trim($_option_detail[option_div])."' and opn_ix = '".$opn_ix."' and set_group = '".$x."'");

							if($mdb->total){
								$mdb->fetch();
								$opd_ix = $mdb->dt[id];

								$sql = "update shop_product_options_detail_temp set
											set_group='".$x."',
											set_group_seq = '".$i."',
											option_div='".$_option_detail[option_div]."',
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
											option_etc1='".$_option_detail[set_cnt]."' ,
											insert_yn='Y'
											where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";

							}else{
								$sql = "INSERT INTO shop_product_options_detail_temp 
											(id, pid, opn_ix, set_group, set_group_seq, option_div, option_code,option_coprice,option_listprice, option_price, option_wholesale_listprice,  option_wholesale_price, option_stock, option_safestock, option_soldout, option_barcode,option_etc1, insert_yn, regdate, option_gid) 
											values
											('','$pid','$opn_ix','".$x."','".$i."','".$_option_detail[option_div]."','".$_option_detail[code]."','".$_option_detail[coprice]."','".$_option_detail[listprice]."','".$_option_detail[sellprice]."','".$_option_detail[wholesale_listprice]."','".$_option_detail[wholesale_price]."','".$_option_detail[stock]."','".$_option_detail[safestock]."','".$_option_detail[soldout]."', '".$_option_detail[barcode]."' ,'".$_option_detail[set_cnt]."' ,'Y', NOW(),'".$_option_detail[gid]."') ";
							}
							$mdb->sequences = "SHOP_GOODS_OPTIONS_DT_SEQ";
							//echo $sql."<br><br>";
							$mdb->query($sql);

							if($_option_detail[stock] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
								$option_stock_yn = "N";
							}

							if($_option_detail[stock] < $_option_detail[safestock] && $option_stock_yn == ""){
								$option_stock_yn = "R";
							}
						//}
					}
					$i++;
				}
			}
			$sql = "delete from shop_product_options_detail_temp where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
			//echo $sql;
			$mdb->query($sql);
			
			$sql = "SELECT max(option_stock) as option_stock,max(option_safestock) as option_safestock 
						FROM shop_product_options_detail_temp 
						WHERE opn_ix='".$opn_ix."'";

			$mdb->query($sql);
			$mdb->fetch();
			$option_stock = $mdb->dt[option_stock];
			$option_safestock = $mdb->dt[option_safestock];
			if($sell_ing_cnt == ""){
				$sell_ing_cnt = 0;
			}
			$mdb->query("update ".TBL_SHOP_PRODUCT." set stock = '".$option_stock."' ,safestock = '".$option_safestock."' where id ='".$pid."'");
			if($option_stock_yn){
				$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = '$option_stock_yn' where id ='".$pid."' ");
			}
			$x++;
		
	}

		$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE pid = '$pid' and option_kind in ('s2','x2') and insert_yn = 'N' ");

		if($mdb->total){
			$mdb->fetch();
			$opn_ix = $mdb->dt[0];
			$sql = "delete from shop_product_options_detail_temp where opn_ix='".$opn_ix."'  ";
			$mdb->query($sql);
		}
		$sql = "delete from shop_product_options_temp where pid = '$pid' and option_kind in ('s2','x2') and insert_yn = 'N' ";
		$mdb->query($sql);

		/*
		
		$mdb->debug = false;
		*/
	}else{
		$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE pid = '$pid' and option_kind in ('s2','x2') ");

		if($mdb->total){
			$mdb->fetch();
			$opn_ix = $mdb->dt[0];
			$sql = "delete from shop_product_options_detail_temp where opn_ix='".$opn_ix."'  ";
			$mdb->query($sql);
		}
		$sql = "delete from shop_product_options_temp where pid = '$pid' and option_kind in ('s2','x2') ";
		$mdb->query($sql);

		//$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'N' where id ='$pid'");
	}
}

function CodiOptionUpdate($mdb, $pid, $_options){	//코디옵션추가 함수 2014-01-09 이학봉
	//$mdb->debug = true;
	//print_r($_options);
	if(is_array($_options)){	//기존 옵션 존재시 사용안함으로 변경
		$sql = "update  shop_product_options_temp set
					insert_yn = 'N'
					where pid = '$pid'  and option_kind in ('c') ";
		$mdb->query($sql);	

	//for($x=0;$x < count($_options);$x++){
	$x = 0;
	foreach($_options as $key => $option_info){
		//echo "option_name:".$option_info["option_name"];
		if($option_info["option_name"]){
				if($option_info["opn_ix"]){
					$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE pid = '$pid' and opn_ix = '".trim($option_info["opn_ix"])."' and option_kind = '".$option_info["option_kind"]."' ");
				}else{
					$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE pid = '$pid' and option_name = '".trim($option_info["option_name"])."' and option_kind = '".$option_info["option_kind"]."' ");
				}

				if($option_info["option_use"]){
					$_options_use = $option_info["option_use"];
				}else{
					$_options_use = 0;
				}

				if($mdb->total){	//기존 옵션 opn_ix 가 존재하면 새로운정보로 update
					$mdb->fetch();
					$opn_ix = $mdb->dt[opn_ix];

					$sql = "update  shop_product_options_temp set
									option_name='".trim($option_info["option_name"])."', 
									option_kind='".$option_info["option_kind"]."', 
									option_type='".$option_info["option_type"]."',
									option_use='".($option_info["option_use"] == "" ? "0":"1")."',
									box_total='".$option_info["box_total"]."',
									insert_yn = 'Y'
									where opn_ix = '".$opn_ix."' ";
									
					$mdb->query($sql);
				}else{	// 없으면 입력
					$sql = "INSERT INTO shop_product_options_temp (opn_ix, pid, option_name, option_kind, option_type, option_use, box_total, insert_yn, regdate)
									VALUES
									('','$pid','".$option_info["option_name"]."','".$option_info["option_kind"]."','".$option_info["option_type"]."','".($option_info["option_use"] == "" ? "0":"1")."','".$option_info["box_total"]."','Y',NOW())";

					$mdb->sequences = "SHOP_GOODS_OPTIONS_SEQ";

					$mdb->query($sql);


					if($mdb->dbms_type == "oracle"){
						$opn_ix = $mdb->last_insert_id;
					}else{
						$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE opn_ix=LAST_INSERT_ID()");
						$mdb->fetch();
						$opn_ix = $mdb->dt[0];
					}

				}

				$sql = "update shop_product_options_detail_temp set insert_yn='N' where opn_ix='".$opn_ix."' ";		//해당 옵션에 포함된 상품도 사용안함으로 설정
				$mdb->query($sql);
			
				
				$option_stock_yn = "";
				//for($i=0;$i < count($option_info["details"]);$i++){
				$i=0;
				foreach($option_info["details"] as $key => $option_detail){

					//foreach($_option_detail as $opsd_key=>$opsd_value) {
						if($option_detail[option_div]){

							$mdb->query("SELECT id FROM shop_product_options_detail_temp WHERE option_div = '".trim($option_detail[option_div])."' and opn_ix = '".$opn_ix."' and set_group = '".$x."'");

							if($mdb->total){
								$mdb->fetch();
								$opd_ix = $mdb->dt[id];

								$sql = "update shop_product_options_detail_temp set
											set_group='".$x."',
											set_group_seq = '".$i."',
											option_div='".$option_detail[option_div]."',
											option_code='".$option_detail[code]."',
											option_gid='".$option_detail[gid]."',
											option_coprice='".$option_detail[coprice]."',
											option_listprice='".$option_detail[listprice]."',
											option_price='".$option_detail[sellprice]."',
											option_wholesale_listprice='".$option_detail[wholesale_listprice]."',
											option_wholesale_price='".$option_detail[wholesale_price]."',
											option_stock='".$option_detail[stock]."',
											option_safestock='".$option_detail[safestock]."' ,
											option_soldout='".$option_detail[soldout]."' ,
											option_barcode='".$option_detail[barcode]."' ,
											option_etc1='".$option_detail[set_cnt]."' ,
											insert_yn='Y'
											where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";


							}else{
								$sql = "INSERT INTO shop_product_options_detail_temp 
											(id, pid, opn_ix, set_group, set_group_seq, option_div, option_code,option_coprice,option_listprice, option_price, option_wholesale_listprice,  option_wholesale_price, option_stock, option_safestock, option_soldout, option_barcode,option_etc1, insert_yn, regdate, option_gid) 
											values
											('','$pid','$opn_ix','".$x."','".$i."','".$option_detail[option_div]."','".$option_detail[code]."','".$option_detail[coprice]."','".$option_detail[listprice]."','".$option_detail[sellprice]."','".$option_detail[wholesale_listprice]."','".$option_detail[wholesale_price]."','".$option_detail[stock]."','".$option_detail[safestock]."','".$option_detail[soldout]."', '".$option_detail[barcode]."' ,'".$option_detail[set_cnt]."' ,'Y', NOW(),'".$option_detail[gid]."') ";
							}

							$mdb->sequences = "SHOP_GOODS_OPTIONS_DT_SEQ";
							//echo $sql."<br><br>";
							$mdb->query($sql);

							if($option_detail[stock] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
								$option_stock_yn = "N";
							}

							if($option_detail[stock] < $option_detail[safestock] && $option_stock_yn == ""){
								$option_stock_yn = "R";
							}
						//}
					}
					$i++;
				}
			
				$sql = "delete from shop_product_options_detail_temp where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
				//echo $sql;
				$mdb->query($sql);		//옵션에 상품을 넣거나 수정후 나머지 N 설정값들을 삭제
				
				$sql = "SELECT max(option_stock) as option_stock,max(option_safestock) as option_safestock 
							FROM shop_product_options_detail_temp 
							WHERE opn_ix='".$opn_ix."'";

				$mdb->query($sql);
				$mdb->fetch();
				$option_stock = $mdb->dt[option_stock];
				$option_safestock = $mdb->dt[option_safestock];
				if($sell_ing_cnt == ""){
					$sell_ing_cnt = 0;
				}
				$mdb->query("update ".TBL_SHOP_PRODUCT." set stock = '".$option_stock."' ,safestock = '".$option_safestock."' where id ='".$pid."'");
				if($option_stock_yn){
					$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = '$option_stock_yn' where id ='".$pid."' ");
				}
				$x++;
			}
		
			$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE pid = '$pid' and option_kind in ('c') and insert_yn = 'N' ");

			if($mdb->total){
				$mdb->fetch();
				$opn_ix = $mdb->dt[0];
				$sql = "delete from shop_product_options_detail_temp where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
				$mdb->query($sql);
			}
			$sql = "delete from shop_product_options_temp where pid = '$pid' and option_kind in ('c') and insert_yn = 'N' ";

			$mdb->query($sql);
		}
		/*
		
		$mdb->debug = false;
		*/
	}else{
		$mdb->query("SELECT opn_ix FROM shop_product_options_temp WHERE pid = '$pid' and option_kind in ('c') ");

		if($mdb->total){
			$mdb->fetch();
			$opn_ix = $mdb->dt[0];
			$sql = "delete from shop_product_options_detail_temp where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
			$mdb->query($sql);
		}
		$sql = "delete from shop_product_options_temp where pid = '$pid' and option_kind in ('c') ";

		$mdb->query($sql);

		//$mdb->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = 'N' where id ='$pid'");
	}

}

?>