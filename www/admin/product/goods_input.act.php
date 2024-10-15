<?php
@include_once("../web.config");
include_once("../../class/database.class");
include_once("../lib/imageResize.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/admin/class/layout.class");
include_once("goods.options.lib.php");
@include_once($_SERVER['DOCUMENT_ROOT'] . '/include/sns.config.php');
require $_SERVER["DOCUMENT_ROOT"] . '/class/sphinxfb.class';
include_once("goods_input.lib.php");

session_start();
$db = new Database;
$db2 = new Database;

if ($admininfo[company_id] == "") {
    echo "<script language='JavaScript' src='../_language/language.php'></Script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
    //'관리자 로그인후 사용하실수 있습니다.'
    exit;
}

// 진하게, 기울기, 밑줄, 색상코드 작업
if($b_preface == 'on'){
	$b_preface = 'Y';
}else{
	$b_preface = 'N';
}

if($i_preface == 'on'){
	$i_preface = 'Y';
}else{
	$i_preface = 'N';
}

if($u_preface == 'on'){
	$u_preface = 'Y';
}else{
	$u_preface = 'N';
}

if ($act == "get_lazada_options") {
    $sql = "select 
				i.size_variation
			from 
				sellertool_itemtype_linked_relation r, sellertool_received_itemtype i
			where 
				r.site_code='lazada'
				and i.site_code='lazada'
				and r.target_cid = i.disp_no
				and r.origin_cid='" . $cid . "'";
    $db->query($sql);

    if ($db->total) {
        $db->fetch();
        echo $db->dt['size_variation'];
    } else {
        echo null;
    }
    exit;
}

if ($act == "get_options") {
    $sql = "select pod.*
				from " . TBL_SHOP_PRODUCT_OPTIONS . " po, " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " pod
				where po.opn_ix = pod.opn_ix
				and  po.pid='" . $pid . "' and option_kind = 'b'
				order by id asc ";

    $db->query($sql);
    //$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." WHERE opnt_ix='".$opnt_ix."'");
    if ($db->dbms_type == "oracle") {
        $options = $db->fetchall("object");
    } else {
        $options = $db->fetchall2("object");
    }
    $options = str_replace("\"true\"", "true", json_encode($options));
    $options = str_replace("\"false\"", "false", $options);
    echo $options;
    exit;
    //header("Location:../product_list.php");
}

if ($act == "get_basicinfo") {

    for ($i = 0; $i < count($id); $i++) {
        $db->query("select basicinfo from " . TBL_SHOP_PRODUCT . " where id='" . $id[$i] . "' ");
        $db->fetch();
        $basicinfo .= $db->dt[basicinfo];
    }

    $basicinfo = str_replace("\"true\"", "true", json_encode($basicinfo));
    $basicinfo = str_replace("\"false\"", "false", $basicinfo);
    echo $basicinfo;
    //header("Location:../product_list.php");
}

if ($act == "templet_insert") {
    $thisfile = load_template($DOCUMENT_ROOT . $admin_config[mall_data_root] . "/productreg_templet/$page_name");
    echo "
	<html>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<body>
	$thisfile
	</body>
	</html>";
    echo "<script>parent.document.frames['iView'].document.body.innerHTML = document.body.innerHTML;</script>";
}

if ($act == "vieworder_update") {
    for ($i = 0; $i < count($sortlist); $i++) {
        $sql = "update " . TBL_SHOP_RELATION_PRODUCT . " set
			vieworder='" . ($i + 1) . "'
			where pid='$pid' and rp_pid='" . $sortlist[$i] . "'";//

        //echo $sql;
        $db->query($sql);
    }
}


$style = (count($style) > 0) ? json_encode($_REQUEST['style']) : '';
$tag = (count($tag) > 0) ? json_encode($_REQUEST['tag']) : '';

if ($act == 'insert' || $act == "tmp_insert") {    //상품추가

    /*
    $check_pname = trim($_POST['pname']);
    $sql = "select id from shop_product where pname = '" . $check_pname . "' ";

    $db->query($sql);
    if ($db->total) {
        echo "<script>alert('중복된 상품명이 존재 합니다.');top.clickable=true;</script>";
        exit;
    }
    */

    $db->query("select * from shop_image_resizeinfo order by idx");
    $image_info2 = $db->fetchall();

    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product/")) {
        mkdir($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product/");
        chmod($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product/", 0777);
    }

    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product_detail/")) {
        mkdir($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product_detail/");
        chmod($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product_detail/", 0777);
    }

    $db->query("SELECT max(vieworder)+1 as max_vieworder FROM " . TBL_SHOP_PRODUCT . " ");
    //$db->query("update ".TBL_SHOP_PRODUCT." set vieworder = vieworder + 1 , bestorder = bestorder + 1 , mainorder = mainorder + 1,wmainorder = wmainorder +1");

    //vieworder 를 최근등록 상품일수록 큰 값을 셋팅되게 수정 2012.07.15 신훈식
    //if(!substr_count($_SERVER["HTTP_HOST"],"selina")){
    //$db->query("update ".TBL_SHOP_PRODUCT." set vieworder = vieworder + 1 ");
    //}
    $db->fetch();
    $vieworder = $db->dt[max_vieworder];
    //$vieworder = 1;
    /*$bestorder = 1;
	$mainorder = 1;
	$wmainorder = 1;*/

    $pname = str_replace("'", "&#39;", $pname);

    //예외처리
    $basicinfo = str_replace("''", "", $basicinfo);
    $basicinfo = str_replace("'", "&#39;", $basicinfo);
    $basicinfo = str_replace('"', "&quot;", $basicinfo);
    //$basicinfo = strip_tags($basicinfo, "<img><table><th><col><colgroup><tbody><tr><td><div><a><ul><li><dl><dt><p><h><font><strong><br><span><iframe>");    //2014-07-27 edit 태그 예외처리 이학봉

    $m_basicinfo = str_replace("''", "", $m_basicinfo);
    $m_basicinfo = str_replace("'", "&#39;", $m_basicinfo);
    $m_basicinfo = str_replace('"', "&quot;", $m_basicinfo);
    //$m_basicinfo = strip_tags($m_basicinfo, "<img><table><th><col><colgroup><tbody><tr><td><div><a><ul><li><dl><dt><p><h><font><strong><br><span><iframe>");    //2014-07-27 edit 태그 예외처리 이학봉

    $data_text_convert = $basicinfo;
    //$m_data_text_convert = $m_basicinfo;
    $m_data_text_convert = $basicinfo; //PC와 모바일 둘다 같은거로 처리. PC 기준

    //print_r($basicinfo);

    if ($one_commission == "") {    //셀러업체일경우 빈값:  개별수수료는 사용안함으로 되어야함 2014-08-11 이학봉
        $one_commission = "N";
    } else {
        $one_commission = $one_commission;
    }

    if ($admininfo[admin_level] == 9) {
        if ($state == "") {
            $state = "1";
        }
        if ($disp == "") {
            $disp = "1";
        }
        if ($mode == "copy") {
            if ($admin != "") {
                $company_id = $admin;
            }
        } else {
            if ($admin == "") {
                if ($company_id == "") {
                    $company_id = $admininfo[company_id];
                }
            } else {
                if ($company_id == "") {
                    $company_id = $admin;
                }
            }
        }
    } else {
        $state = "6";
        $disp = "1";
        $company_id = $admininfo[company_id];
    }

    if ($b_ix != "") {
        $brand = $b_ix;
    }
    if ($brand_name != "") {
        $brand_name = $brand_name;
    } else {
        $sql = "select brand_name from shop_brand where b_ix = '$brand'";
        $db->query($sql);
        $db->fetch();
        $brand_name = $db->dt[brand_name];
    }

    $brand_name = str_replace("'", "\\'", $brand_name);

    if (is_array($display_category)) {    //상품미분류 여부
        $reg_category = "Y";
    } else {
        $reg_category = "N";
    }
    if ($reserve_yn == "") {
        $reserve_yn = "N";
    }

    if ($rate_type == "") {
        $rate_type = "N";
    }

    for ($i = 0; $i < count($icon_check); $i++) {
        if ($i < count($icon_check) - 1) {
            $icons .= $icon_check[$i] . ";";
        } else {
            $icons .= $icon_check[$i];
        }
    }
    $sns_btn = serialize($sns_btn);

    if ($delivery_policy == "") {    //배송타입이 없을경우 입점업체배송으로 체크 2014-07-21 이학봉
        $delivery_policy = "1";
    }

    if ($sns_btn_yn == "") {    //sns 사용값이 빈값일경우 미사용으로 체크
        $sns_btn_yn = "N";
    }

    if ($surtax_yorn == "") {    //면세여부 빈값으로 넘어올경우 과세로체크 2014-07-21 이학봉
        $surtax_yorn = "N";
    }

    if ($auto_sync_wms == "") {    //재고자동연동 빈값으로 넘어올경우 연동으로체크
        $auto_sync_wms = "Y";
    }

    if ($delivery_type == "") {    //배송타입이 2일경우 입점업체 배송으로 체크 2014-07-21 이학봉
        $delivery_type = "1";  //df
    }

    if ($is_sell_date == "") {    //판매기간이 빈값일경우 미설정으로 체크 2014-07-21 이학봉
        $is_sell_date = "0";
    }

    if ($allow_order_type == "") {    //한정판매수량여부 값이 빈값이 경우 미적용으로 처리 2014-07-21 이학봉
        $allow_order_type = "0";
    }

    if ($relation_display_type == "") {    //관련상품 노출타입 빈값일 경우 수동으로 체크 2014-07-21 이학봉
        $relation_display_type = "M";
    }

    // 상품등록일자를 왜 이렇게 처리했는지 확인필요
    if ($regdate == "" || $regdate == "NOW()" || $regdate == "0000-00-00 00:00:00") {
        $regdate = "NOW()";
    } else {
        $regdate = "'" . $regdate . "'";
    }

    if ($editdate == "" || $editdate == "0000-00-00 00:00:00") {
        if ($db->dbms_type == "oracle") {
            $editdate = "sysdate";
        } else {
            //$editdate = "0000-00-00 00:00:00";
            $editdate = "NOW()";
        }
    } else {
        $editdate = "'" . $editdate . "'";
    }

    if ($mandatory_type_1 != "") {
        $mandatory_type = $mandatory_type_1 . "|" . $mandatory_type_2;    // 상품고시 제대로 substr 되지 않아서 | 구분값으로 분리 시킴 2013-06-05 이학봉
    } else {
        $mandatory_type = "";
    }

    if ($mandatory_type_1_global != "") {
        $mandatory_type_global = $mandatory_type_1_global . "|" . $mandatory_type_2_global;    // 상품고시 제대로 substr 되지 않아서 | 구분값으로 분리 시킴 2013-06-05 이학봉
    } else {
        $mandatory_type_global = "";
    }

    if ($sell_priod_sdate == '' || $sell_priod_sdate == '0000-00-00') {
        $sell_priod_sdate = "1000-01-01 00:00:00";
    } else {
        $sell_priod_sdate = $sell_priod_sdate . " " . $sell_priod_sdate_h . ":" . $sell_priod_sdate_i . ":" . $sell_priod_sdate_s;
    }

    if ($sell_priod_edate == '' || $sell_priod_edate == '0000-00-00') {
        $sell_priod_edate = "1000-01-01 00:00:00";
    } else {
        $sell_priod_edate = $sell_priod_edate . " " . $sell_priod_edate_h . ":" . $sell_priod_edate_i . ":" . $sell_priod_edate_s;
    }

    if ($product_type == "99") $coupon_use_yn = "N";//세트 상품은 쿠폰 사용 못하도록 고정. goods_input.php 에서 사용안함으로 체크하나 disabled 속성으로 처리하기에 값이 넘어오지 않음. N 으로 고정시킴 kbk 13/07/17
    if (empty($coupon_use_yn)) $coupon_use_yn = "Y";
    if (empty($delivery_coupon_yn)) $delivery_coupon_yn = "Y";

    $delivery_company = "MI"; // 2013년 08월 09일 신훈식 현재사용하지 않아 고정값으로 픽스

    // account_type 정산방식 1:수수료 2:매입(공급가로 정산) 3:미정산
    //wholesale_commission 도매수수료
    //추가 합니다. 2013-10-29 이학봉
    //product_weight : 무게KG 추가 2014-03/31 이학봉	//m_basicinfo

    if ($admininfo[admin_level] != '9') {    //셀러일경우 웹/모바일상품 구분은 기본으로 전체로 선택함 2014-08-19 이학봉
        $is_mobile_use = 'A';
    }

    if ($is_mobile_use == '') {    //다른경로로 들어온 상품중에 빈값이면 강제로 A로 지정
        $is_mobile_use = 'A';
    }

    $global_pinfo_json = "";
    if (count($global_pinfo) > 0 && is_array($global_pinfo)) {
        foreach ($global_pinfo as $colum => $li) {
            foreach ($li as $ln => $val) {
                $_global_pinfo[$colum][$ln] = urlencode($val);
            }
        }

        if (is_array($global_pinfo[pname])) {
            $global_search_keyword = implode(' ', $global_pinfo[pname]);
        }

        $global_pinfo_json = json_encode($_global_pinfo);
    }

    //20160426
    $etc1 = trim($etc1);
    $etc2 = trim($etc2);
    $etc3 = trim($etc3);
    $etc4 = trim($etc4);
    $etc5 = trim($etc5);
    $etc6 = trim($etc6);
    $etc7 = trim($etc7);
    $etc8 = trim($etc8);
    $etc9 = trim($etc9);
    $etc10 = trim($etc10);

    if (count($category_add_infomations) > 0) {
        foreach ($category_add_infomations as $colum => $li) {
            foreach ($li as $ln => $val) {
                if (is_array($val)) {
                    foreach ($val as $key => $value) {
                        //echo $colum.":::".$ln."<br>";
                        $_category_add_infomations[$colum][$ln][] = urlencode($value);
                    }
                } else {
                    $_category_add_infomations[$colum][$ln] = urlencode($val);
                }
            }
        }
//print_r($_category_add_infomations);
//exit;
        $category_add_infomations_json = json_encode($_category_add_infomations);
    }

    if ($soho == "") {
        $soho = "0";
    }
    if ($designer == "") {
        $designer = "0";
    }
    if ($mirrorpick == "") {
        $mirrorpick = "0";
    }

	if($laundry_cid == ""){
		$laundry_cid = substr($laundry_one_depth,0,3)."".substr($laundry_two_depth,3,3);
	}

    $sql = "INSERT INTO " . TBL_SHOP_PRODUCT . "
					(id,  mall_ix, pname, preface, product_color_chip, pcode, style, soho, designer, mirrorpick, brand,brand_name, company, buying_company, paper_pname,  shotinfo,  buyingservice_coprice, wholesale_price, wholesale_sellprice, 
					listprice,sellprice, premiumprice, coprice,wholesale_reserve_yn, wholesale_reserve,wholesale_reserve_rate,wholesale_rate_type, reserve_yn, reserve,reserve_rate,rate_type, sns_btn_yn, sns_btn,  bimg, basicinfo,m_basicinfo,  
					icons,state, disp,product_type, movie, movie_thumbnail,movie_now,vieworder, admin,stock,safestock,available_stock, search_keyword,add_info,reg_category, surtax_yorn,delivery_company,one_commission,commission,cupon_use_yn,stock_use_yn,
					supply_company, inventory_info,delivery_policy,delivery_product_policy,delivery_package,delivery_price,free_delivery_yn,free_delivery_count,
					sell_priod_sdate,sell_priod_edate,allow_order_type,allow_order_cnt_byonesell,allow_order_cnt_byoneperson,allow_order_minimum_cnt,origin,make_date,expiry_date,mandatory_type,mandatory_type_global, relation_product_cnt, relation_text1, 
					relation_display_type, 
					etc1,etc2,etc3,etc4,etc5,etc6,etc7,etc8,etc9,etc10, download_img, download_desc, bs_goods_url, substitude_yn, substitude_total, substitude_seller, substitude_rate, bs_site, currency_ix, hotcon_event_id, hotcon_pcode, co_goods, 
					co_pid, co_company_id, editdate, regdate,wholesale_yn,offline_yn,is_pos_link, add_status,trade_admin,md_code,barcode,delivery_type,delivery_coupon_yn,coupon_use_yn,account_type,wholesale_commission,reg_charger_ix,
					reg_charger_name,is_adult,remain_stock,is_sell_date,wholesale_allow_max_cnt,allow_max_cnt,wholesale_allow_basic_cnt,allow_basic_cnt,md_one_commission,md_discount_name,md_sell_date_use,md_sell_priod_sdate,md_sell_priod_edate,
					whole_head_company_sale_rate,whole_seller_company_sale_rate,head_company_sale_rate,seller_company_sale_rate,c_ix,relation_text2,product_weight,allow_byoneperson_cnt,wholesale_allow_byoneperson_cnt,is_mobile_use,
					gift_sprice,gift_eprice,global_pinfo, category_add_infos, mandatory_use, mandatory_use_global, auto_sync_wms, gift_qty, gift_selectbox_cnt, gift_selectbox_nooption_yn, exchangeable_yn, returnable_yn, admin_memo, laundry_cid, wear_info, b_preface, i_preface, u_preface, c_preface, listNum, overNum, slistNum, nailNum, pattNum, marker_left_dn, marker_right_dn)

					VALUES
					
					('$migration_id', '$mall_ix','" . strip_tags(trim($pname)) . "', '$preface', '$product_color_chip','$pcode', '$style',  '$soho',  '$designer',  '$mirrorpick', '$brand','$brand_name','$company','$buying_company', '$paper_pname', 
					'$shotinfo', '$buyingservice_coprice','$wholesale_price','$wholesale_sellprice','$listprice','$sellprice', '$premiumprice', '$coprice','$wholesale_reserve_yn', '$wholesale_reserve','$wholesale_rate1','$wholesale_rate_type',
					'$reserve_yn', '$reserve','$rate1','$rate_type','$sns_btn_yn','$sns_btn',  '$bimg_text','$basicinfo','$m_basicinfo','$icons', $state, '$disp','$product_type', '$movie', '$movie_thumbnail','$movie_now', '$vieworder', '$company_id',
					'$stock','$safestock','$available_stock','$search_keyword','$add_info','$reg_category','$surtax_yorn','$delivery_company','$one_commission','$commission','$cupon_use_yn','$stock_use_yn','$supply_company','$inventory_info',
					'$delivery_policy','$delivery_product_policy','$delivery_package','$delivery_price','$free_delivery_yn','$free_delivery_count',
					'$sell_priod_sdate','$sell_priod_edate','$allow_order_type','$allow_order_cnt_byonesell','$allow_order_cnt_byoneperson','$allow_order_minimum_cnt','$origin','$make_date','$expiry_date','$mandatory_type','$mandatory_type_global',
					'$relation_product_cnt','$relation_text1','$relation_display_type',
					'$etc1','$etc2','$etc3','$etc4','$etc5','$etc6','$etc7','$etc8','$etc9','$etc10','$download_img','$download_desc','$bs_goods_url','$substitude_yn','$substitude_total','$substitude_seller','$substitude_rate',
					'$bs_site','$currency_ix','$hotcon_event_id', '$hotcon_pcode','$co_goods','$co_pid','$co_company_id', NOW(),NOW(),'" . $wholesale_yn . "','" . $offline_yn . "','N','I','$trade_admin','$md_code','$barcode',
					'$delivery_type','$delivery_coupon_yn','$coupon_use_yn','$account_type','$wholesale_commission','" . $_SESSION["admininfo"]["charger_ix"] . "','" . $_SESSION["admininfo"]["charger"] . "(" . $_SESSION["admininfo"]["charger_id"] . ")',
					'$is_adult','$remain_stock','$is_sell_date','$wholesale_allow_max_cnt','$allow_max_cnt','$wholesale_allow_basic_cnt','$allow_basic_cnt','$md_one_commission','$md_discount_name','$md_sell_date_use','$md_sell_priod_sdate',
					'$md_sell_priod_edate','$whole_head_company_sale_rate','$whole_seller_company_sale_rate','$head_company_sale_rate','$seller_company_sale_rate','$c_ix','$relation_text2','$product_weight','$allow_byoneperson_cnt',
					'$wholesale_allow_byoneperson_cnt','$is_mobile_use','$gift_sprice','$gift_eprice','$global_pinfo_json','$category_add_infomations_json', '$mandatory_use', '$mandatory_use_global', '$auto_sync_wms', '$gift_qty', '$gift_selectbox_cnt', '$gift_selectbox_nooption_yn',
					'$exchangeable_yn', '$returnable_yn', '$admin_memo', '$laundry_cid', '$wear_info', '$b_preface', '$i_preface', '$u_preface', '$c_preface', '$listNum', '$overNum', '$slistNum', '$nailNum', '$pattNum', '$marker_left_dn', '$marker_right_dn') ";

    //$db->debug = true;

    $db->sequences = "SHOP_GOODS_SEQ";
    $db->query($sql);

    if ($db->dbms_type == "oracle") {
        $INSERT_PRODUCT_ID = $db->last_insert_id;
        //echo $INSERT_PRODUCT_ID;
        //exit;
    } else {
        //마이그레이션시 기존id가 있을때
        if($migration_id){
            $db->query("SELECT id FROM " . TBL_SHOP_PRODUCT . " WHERE id= '".$migration_id."' ");
            $db->fetch();
            $INSERT_PRODUCT_ID = $db->dt[id];
        }else{
            $db->query("SELECT id FROM " . TBL_SHOP_PRODUCT . " WHERE id=LAST_INSERT_ID()");
            $db->fetch();
            $INSERT_PRODUCT_ID = $db->dt[id];
        }
    }


    //글로벌 데이터 처리
    //array('pname', 'add_info', 'search_keyword', 'basicinfo', 'm_basicinfo', 'coprice', 'listprice', 'sellprice', 'preface');
    if (empty($english_pname)) {
        $english_pname = $pname;
    }

    if (empty($english_basicinfo)) {
        $english_basicinfo = $basicinfo;
    }

    if (empty($english_m_basicinfo)) {
        $english_m_basicinfo = $m_basicinfo;
    }

    if (empty($english_coprice)) {
        $english_coprice = getExchangeNationPrice($coprice);
    }

    if (empty($english_listprice)) {
        $english_listprice = getExchangeNationPrice($listprice);
    }

    if (empty($english_sellprice)) {
        $english_sellprice = getExchangeNationPrice($sellprice);
    }

    $sql = "INSERT INTO shop_product_global
					(id,  mall_ix, pname, preface, product_color_chip, pcode, style, soho, designer, mirrorpick, brand,brand_name, company, buying_company, paper_pname,  shotinfo,  buyingservice_coprice, wholesale_price, wholesale_sellprice, listprice,sellprice, premiumprice, coprice,wholesale_reserve_yn, wholesale_reserve,wholesale_reserve_rate,wholesale_rate_type, reserve_yn, reserve,reserve_rate,rate_type, sns_btn_yn, sns_btn,  bimg, basicinfo,m_basicinfo,  icons,state, disp,product_type, movie, movie_thumbnail,movie_now,vieworder, admin,stock,safestock,available_stock, search_keyword,add_info,reg_category, surtax_yorn,delivery_company,one_commission,commission,cupon_use_yn,stock_use_yn,supply_company, inventory_info,delivery_policy,delivery_product_policy,delivery_package,delivery_price,free_delivery_yn,free_delivery_count,
					sell_priod_sdate,sell_priod_edate,allow_order_type,allow_order_cnt_byonesell,allow_order_cnt_byoneperson,allow_order_minimum_cnt,origin,make_date,expiry_date,mandatory_type,mandatory_type_global, relation_product_cnt, relation_text1, relation_display_type, 
					etc1,etc2,etc3,etc4,etc5,etc6,etc7,etc8,etc9,etc10, download_img, download_desc, bs_goods_url, substitude_yn, substitude_total, substitude_seller, substitude_rate, bs_site, currency_ix, hotcon_event_id, hotcon_pcode, co_goods, co_pid, co_company_id, editdate, regdate,wholesale_yn,offline_yn,is_pos_link, add_status,trade_admin,md_code,barcode,delivery_type,delivery_coupon_yn,coupon_use_yn,account_type,wholesale_commission,reg_charger_ix,reg_charger_name,is_adult,remain_stock,is_sell_date,wholesale_allow_max_cnt,allow_max_cnt,wholesale_allow_basic_cnt,allow_basic_cnt,md_one_commission,md_discount_name,md_sell_date_use,md_sell_priod_sdate,md_sell_priod_edate,whole_head_company_sale_rate,whole_seller_company_sale_rate,head_company_sale_rate,seller_company_sale_rate,c_ix,relation_text2,product_weight,allow_byoneperson_cnt,wholesale_allow_byoneperson_cnt,is_mobile_use,gift_sprice,gift_eprice,global_pinfo, category_add_infos, mandatory_use, mandatory_use_global, auto_sync_wms, gift_qty, gift_selectbox_cnt,  laundry_cid, listNum, overNum, slistNum, nailNum, pattNum)

					values
					
					('$INSERT_PRODUCT_ID', '$mall_ix','" . strip_tags(trim($english_pname)) . "', '$english_preface', '$product_color_chip','$pcode', '$style',  '$soho',  '$designer',  '$mirrorpick', '$brand','$brand_name','$company','$buying_company', '$paper_pname', '$shotinfo', '$buyingservice_coprice','$wholesale_price','$wholesale_sellprice','$english_listprice','$english_sellprice', '$premiumprice', '$english_coprice','$wholesale_reserve_yn', '$wholesale_reserve','$wholesale_rate1','$wholesale_rate_type','$reserve_yn', '$reserve','$rate1','$rate_type','$sns_btn_yn','$sns_btn',  '$bimg_text','$english_basicinfo','$english_m_basicinfo','$icons', $state, '$disp','$product_type', '$movie', '$movie_thumbnail','$movie_now', '$vieworder', '$company_id','$stock','$safestock','$available_stock','$english_search_keyword','$english_add_info','$reg_category','$surtax_yorn','$delivery_company','$one_commission','$commission','$cupon_use_yn','$stock_use_yn','$supply_company','$inventory_info','$delivery_policy','$delivery_product_policy','$delivery_package','$delivery_price','$free_delivery_yn','$free_delivery_count',
					'$sell_priod_sdate','$sell_priod_edate','$allow_order_type','$allow_order_cnt_byonesell','$allow_order_cnt_byoneperson','$allow_order_minimum_cnt','$origin','$make_date','$expiry_date','$mandatory_type','$mandatory_type_global','$relation_product_cnt','$english_relation_text1','$relation_display_type',
					'$etc1','$etc2','$etc3','$etc4','$etc5','$etc6','$etc7','$etc8','$etc9','$etc10','$download_img','$download_desc','$bs_goods_url','$substitude_yn','$substitude_total','$substitude_seller','$substitude_rate','$bs_site','$currency_ix','$hotcon_event_id', '$hotcon_pcode','$co_goods','$co_pid','$co_company_id', NOW(),NOW(),'" . $wholesale_yn . "','" . $offline_yn . "','N','I','$trade_admin','$md_code','$barcode','$delivery_type','$delivery_coupon_yn','$coupon_use_yn','$account_type','$wholesale_commission','" . $_SESSION["admininfo"]["charger_ix"] . "','" . $_SESSION["admininfo"]["charger"] . "(" . $_SESSION["admininfo"]["charger_id"] . ")','$is_adult','$remain_stock','$is_sell_date','$wholesale_allow_max_cnt','$allow_max_cnt','$wholesale_allow_basic_cnt','$allow_basic_cnt','$md_one_commission','$md_discount_name','$md_sell_date_use','$md_sell_priod_sdate','$md_sell_priod_edate','$whole_head_company_sale_rate','$whole_seller_company_sale_rate','$head_company_sale_rate','$seller_company_sale_rate','$c_ix','$english_relation_text2','$product_weight','$allow_byoneperson_cnt','$wholesale_allow_byoneperson_cnt','$is_mobile_use','$gift_sprice','$gift_eprice','$global_pinfo_json','$category_add_infomations_json', '$mandatory_use', '$mandatory_use_global', '$auto_sync_wms', '$gift_qty', '$gift_selectbox_cnt', '$laundry_cid', '$listNum', '$overNum', '$slistNum', '$nailNum', '$pattNum') ";

    $db->query($sql);

    if ($round_type && $round_precision) {
        $sql = "UPDATE " . TBL_SHOP_PRODUCT . " SET round_precision = '$round_precision', round_type = '$round_type' WHERE id='" . $INSERT_PRODUCT_ID . "'";
        //echo $sql;
        $db->query($sql);
    }

    if ($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S') {
        $sfb = new sphinxfb(); // mysql 데이터베이스
        $sfb->rebuild_index(" id ='" . $INSERT_PRODUCT_ID . "' ");
    }

    //배송템플릿 저장 시작 2014-05-13 이학봉
    if ($delivery_type == '1') {    //통합배송일경우 본사 정책
        $sql = "select company_id from common_company_detail where com_type = 'A' limit 0,1";
        $db->query($sql);
        $db->fetch();
        $pd_company_id = $db->dt[company_id];
    } else {
        $pd_company_id = $company_id;
    }
    Insert_product_delivery($dt_ix, $pd_company_id, $INSERT_PRODUCT_ID, $delivery_policy);
    //배송템플릿 저장 끝 2014-05-13 이학봉

    input_filter_info($product_filter, $INSERT_PRODUCT_ID, 'insert');

    //상품기본 배송비 추가 2014-07-31 이학봉 (네이버 , 다음 연동시 기본배송비 노출)
    $delivery_basic_policy = PorudctDeliveryPayMethod($INSERT_PRODUCT_ID);
    $product_basic_delivery_price = PorudctBasicDeliveryPrice($INSERT_PRODUCT_ID);    //상품기본 배송비 구하기
    $sql = "update shop_product set delivery_price = '" . $product_basic_delivery_price . "', delivery_product_policy = '" . $delivery_basic_policy . "' where id = '" . $INSERT_PRODUCT_ID . "'";
    $db->query($sql);
    //상품기본 배송비 추가 2014-07-31 이학봉

    /*판매상태가 일시품절일경우 입고예정일과 자동판매중 상태전환일/ 상태변경에 따른 변경사유 시작 2014-02-14 이학봉*/
    if ($state == "0") {

        if ($is_auto_change == "1") {
            $input_date = $input_date . " " . $input_stime . ":" . $input_smin . ":00";
            $auto_change_state = $auto_change_state . " " . $auto_change_stime . ":" . $auto_change_smin . ":00";
            $where_state_div = "  input_date = '" . $input_date . "', is_auto_change = '" . $is_auto_change . "', auto_change_state='" . $auto_change_state . "'";
        } else {
            $input_date = $input_date . " " . $input_stime . ":" . $input_smin . ":00";
            $where_state_div = "  input_date = '" . $input_date . "', is_auto_change = '" . $is_auto_change . "'";
        }
        $sql = "update " . TBL_SHOP_PRODUCT . " set
					$where_state_div
				where
					id = '" . $INSERT_PRODUCT_ID . "'";
        $db->query($sql);

    } else if ($state == "2" || $state == "8" || $state == "9" || $state == "7") {

        $sql = "insert into shop_product_state_history set
					pid = '" . $INSERT_PRODUCT_ID . "',
					state = '" . $state . "',
					state_div = '" . $state_div . "',
					state_msg = '" . $state_msg . "',
					charger = '" . $admininfo[charger] . "',
					charger_ix = '" . $admininfo[charger_ix] . "',
					regdate = NOW()";
        $db->query($sql);
    }
    /*판매상태가 일시품절일경우 입고예정일과 자동판매중 상태전환일/ 상태변경에 따른 변경사유 시작 2014-02-14 이학봉*/

    //복수구매할인 추가 시작 2014-01-27 이학봉
    if (is_array($wholesale_rate)) {
        for ($i = 0; $i < count($wholesale_rate); $i++) {
            foreach ($wholesale_rate[$i] as $key => $value) {

                if ($key == "whole") {    //is_wholesale = W	도매
                    $is_wholesale = "W";
                } else if ($key == "retail") {
                    $is_wholesale = "R";
                }

                if ($value[is_use] == "") {
                    $is_use = "2";
                } else {
                    $is_use = $value[is_use];
                }

                if ($value[mr_id]) {
                    $sql = "update shop_product_mult_rate set
								is_use = '" . $is_use . "',
								sell_mult_cnt = '" . $value[sell_mult_cnt] . "',
								rate_div = '" . $value[rate_div] . "',
								rate_price = '" . $value[rate_price] . "',
								round_cnt = '" . $value[round_cnt] . "',
								round_type = '" . $value[round_type] . "',
								regdate = NOW()
							where
								pid = '" . $INSERT_PRODUCT_ID . "'
								and mr_id = '" . $value[mr_id] . "'";
                    $db->query($sql);

                } else {

                    if ($value[sell_mult_cnt] == "" && $value[rate_price] == "") {
                        continue;
                    }
                    $sql = "insert into shop_product_mult_rate set
								pid = '" . $INSERT_PRODUCT_ID . "',
								is_wholesale = '" . $is_wholesale . "',
								is_use = '" . $is_use . "',
								sell_mult_cnt = '" . $value[sell_mult_cnt] . "',
								rate_div = '" . $value[rate_div] . "',
								rate_price = '" . $value[rate_price] . "',
								round_cnt = '" . $value[round_cnt] . "',
								round_type = '" . $value[round_type] . "',
								regdate = NOW()";
                    $db->query($sql);
                }

            }
        }
    }

    //복수구매할인 추가 끝 2014-01-27 이학봉

    // SNS 추가정보 입력
    if (in_array($product_type, $sns_product_type)) {
        $querys = array();
        $spei_sDateYMD = preg_replace("[^0-9]", "", $spei_sDateYMD);    // 진행 시작일
        $spei_eDateYMD = preg_replace("[^0-9]", "", $spei_eDateYMD);    // 진행 종료일
        $querys[] = 'pid = "' . $INSERT_PRODUCT_ID . '"';                    // 제품 고유번호
        $querys[] = 'spei_couponInfo = "' . $spei_couponInfo . '"';            // 쿠폰 관리번호
        $querys[] = 'spei_couponSDate = "' . mktime(0, 0, 0, substr($spei_couponSDate, 4, 2), substr($spei_couponSDate, 6, 2), substr($spei_couponSDate, 0, 4)) . '"';            // 유효기간 시작일
        $querys[] = 'spei_couponEDate = "' . mktime(23, 59, 0, substr($spei_couponEDate, 4, 2), substr($spei_couponEDate, 6, 2), substr($spei_couponEDate, 0, 4)) . '"';            // 유효기간 종료일
        $querys[] = 'spei_sDate = "' . mktime($spei_sDateH, $spei_sDateM, 0, substr($spei_sDateYMD, 4, 2), substr($spei_sDateYMD, 6, 2), substr($spei_sDateYMD, 0, 4)) . '"';    // 진행시작일
        $querys[] = 'spei_eDate = "' . mktime($spei_eDateH, $spei_eDateM, 0, substr($spei_eDateYMD, 4, 2), substr($spei_eDateYMD, 6, 2), substr($spei_eDateYMD, 0, 4)) . '"';    // 진행종료일
        $querys[] = 'spei_dispDate = "' . $spei_dispDate . '"';                    // 남은시간 노출여부
        $querys[] = 'spei_dispStock = "' . $spei_dispStock . '"';                // 재고량 노출여부
        $querys[] = 'spei_discountRate = "' . $spei_discountRate . '"';            // 할인율(%)
        $querys[] = 'spei_dispDiscountRate = "' . $spei_dispDiscountRate . '"';    // 할인율 노출여부
        $querys[] = 'spei_dispathPoint = "' . $spei_dispathPoint . '"';            // 발송시점
        $querys[] = 'spei_targetNumber = "' . $spei_targetNumber . '"';            // 구매달성인원
        $querys[] = 'spei_addSaleCount = "' . $spei_addSaleCount . '"';            // 판매수량 노출설정
        $querys[] = 'spei_addSaleMaxCount = "' . $spei_addSaleMaxCount . '"';            // 최대판매수량 설정
        $querys[] = 'spei_buyLimitMin = "' . $spei_buyLimitMin . '"';            // 최소구매수량
        $querys[] = 'spei_buyLimitMax = "' . $spei_buyLimitMax . '"';            // 최대구매수량
        $querys[] = 'spei_smsMessage = "' . $spei_smsMessage . '"';                // SMS 메세지 내용
        $querys[] = 'min_local_type = "' . $min_local_type . '"';                // SMS 메세지 내용

        $db->query('INSERT INTO ' . TBL_SNS_PRODUCT_ETCINFO . ' SET ' . implode(',', $querys));

        if (is_array($subsciptions)) {
            foreach ($subsciptions as $subsciption) {
                if ($subsciption["due_date"]) {
                    $sql = "select pss_ix from shop_product_subs_senddetail where pid = '" . $INSERT_PRODUCT_ID . "' and pss_ix = '" . $subsciption["pss_ix"] . "' ";
                    $db->query($sql);
                    $db->fetch();
                    if (!$db->total) {

                        $sql = "insert into shop_product_subs_senddetail (pss_ix,pid,due_date,add_sale_rate,insert_yn,regdate) 
									values
									('" . $subsciption[pss_ix] . "','" . $INSERT_PRODUCT_ID . "','" . $subsciption[due_date] . "','" . $subsciption[add_sale_rate] . "','" . $subsciption[insert_yn] . "',NOW())";
                        //$db->sequences = "SHOP_GOODS_DISPLAYINFO_SEQ";
                        $db->query($sql);
                    } else {
                        $sql = "update shop_product_subs_senddetail set							 
								 pid='" . $INSERT_PRODUCT_ID . "',
								 due_date='" . $subsciption[due_date] . "',
								 add_sale_rate='" . $subsciption[add_sale_rate] . "',
								 insert_yn='" . $subsciption[insert_yn] . "'
								 where pss_ix='" . $subsciption[pss_ix] . "' 
								";
                        $db->query($sql);
                    }
                }
            }
        }

        if (is_array($local_deliverys)) {
            foreach ($local_deliverys as $local_delivery) {
                if ($local_delivery["due_date"]) {
                    $sql = "select pld_ix from shop_product_localdelivery_detail where pid = '$id' and pld_ix = '" . $local_delivery["pld_ix"] . "' ";
                    $db->query($sql);
                    $db->fetch();
                    if (!$db->total) {

                        $sql = "insert into shop_product_localdelivery_detail(pld_ix,pid,sido,sigugun,dong,due_date, regdate)			
									values
									('" . $local_delivery[pld_ix] . "','" . $INSERT_PRODUCT_ID . "','" . $local_delivery[sido] . "','" . $local_delivery[sigugun] . "','" . $local_delivery[dong] . "','" . $local_delivery[due_date] . "', NOW()) ";
                        //$db->sequences = "SHOP_GOODS_DISPLAYINFO_SEQ";
                        $db->query($sql);
                    } else {
                        $sql = "update shop_product_localdelivery_detail set							 
								 pid='" . $INSERT_PRODUCT_ID . "',
								 sido='" . $local_delivery[sido] . "',
								 sigugun='" . $local_delivery[sigugun] . "',
								 dong='" . $local_delivery[dong] . "',
								 due_date='" . $local_delivery[due_date] . "'
								 where pld_ix='" . $local_delivery[pld_ix] . "' 
								";
                        $db->query($sql);
                    }
                }
            }
        }

        for ($i = 0; $i < count($display_category); $i++) {
            if ($display_category[$i] == $basic) {
                $basic = 1;
            } else {
                $basic = 0;
            }
            if (strlen($display_category[$i]) == '15') {    //대량상품 등록시 카테고리가 0이나 빈값으로 들어오는 경우를 대비해 해당 조건 추가 2014-07-11 이학봉
                $sql = "insert into " . TBL_SNS_PRODUCT_RELATION . " (rid, cid, pid, disp, basic,insert_yn, regdate ) values ('','" . $display_category[$i] . "','" . $INSERT_PRODUCT_ID . "','1','" . $basic . "','N',NOW())";
                $db->query($sql);
            }
        }
    } else {
        //카테고리 정보 입력
        for ($i = 0; $i < count($display_category); $i++) {
            if ($display_category[$i] == $basic) {
                $category_basic = 1;
            } else {
                $category_basic = 0;
            }

            if (strlen($display_category[$i]) == '15') {    //대량상품 등록시 카테고리가 0이나 빈값으로 들어오는 경우를 대비해 해당 조건 추가 2014-07-11 이학봉
                $db->sequences = "SHOP_GOODS_LINK_SEQ";
                $sql = "insert into shop_product_relation (rid, cid, pid, disp, basic,insert_yn, regdate ) values ('','" . $display_category[$i] . "','" . $INSERT_PRODUCT_ID . "','1','" . $category_basic . "','Y',NOW())";
                $db->query($sql);
            }

        }

        //미니샵 카테고리 정보 입력
        for ($i = 0; $i < count($minishop_display_category); $i++) {
            if ($minishop_display_category[$i] == $minishop_basic) {
                $category_basic = 1;
            } else {
                $category_basic = 0;
            }

            if (strlen($minishop_display_category[$i]) == '15') {    //대량상품 등록시 카테고리가 0이나 빈값으로 들어오는 경우를 대비해 해당 조건 추가 2014-07-11 이학봉
                $db->sequences = "SHOP_GOODS_LINK_SEQ";
                $sql = "insert into shop_minishop_relation_product (company_id, rid, cid, pid, disp, basic,insert_yn, regdate ) values ('" . $_SESSION["admininfo"]['company_id'] . "', '','" . $minishop_display_category[$i] . "','" . $INSERT_PRODUCT_ID . "','1','" . $category_basic . "','Y',NOW())";
                $db->query($sql);
            }

        }

        if (count($display_standard_category) > 0) {
            for ($i = 0; $i < count($display_standard_category); $i++) {
                if ($display_standard_category[$i] == $standard_basic) {
                    $category_basic = 1;
                } else {
                    $category_basic = 0;
                }
                $sql = "select psr_ix from shop_product_standard_relation where pid = '" . $INSERT_PRODUCT_ID . "' and cid = '" . $display_standard_category[$i] . "' ";
                $db->query($sql);
                $db->fetch();
                if ($db->total) {
                    $db->query("update shop_product_standard_relation set insert_yn = 'Y' , basic='$category_basic' where psr_ix = '" . $db->dt[psr_ix] . "'");
                } else {
                    if (strlen($display_standard_category[$i]) == '15') {    //카테고리 코드가 15자리가 아닐경우 처리 안함 빈값이나 0으로 입력되는 경우가 있음 2014-07-11 이학봉
                        $db->sequences = "SHOP_GOODS_LINK_SEQ";
                        $db->query("insert into shop_product_standard_relation (psr_ix, cid, pid, disp, basic,insert_yn, regdate ) values ('','" . $display_standard_category[$i] . "','" . $INSERT_PRODUCT_ID . "','1','" . $category_basic . "','Y',NOW())");
                    }
                }
            }

            $db->query("select count(*) as total from shop_product_standard_relation where pid = '" . $INSERT_PRODUCT_ID . "' ");
            $db->fetch();
            if ($db->dt[total] > 0) {
                $db->query("update " . TBL_SHOP_PRODUCT . " set reg_standard_category = 'Y' where id = '" . $INSERT_PRODUCT_ID . "' ");
            } else {
                $db->query("update " . TBL_SHOP_PRODUCT . " set reg_standard_category = 'N' where id = '" . $INSERT_PRODUCT_ID . "' ");
            }
        }
    }

    if ($product_type == "2") {//경매상품일때 경매테이블로 입력
        $startdate = $FromYY . "-" . $FromMM . "-" . $FromDD . " " . $FromHH . ":" . $FromII . ":00";
        $enddate = $ToYY . "-" . $ToMM . "-" . $ToDD . " " . $ToHH . ":" . $ToII . ":00";
        $db->query("insert into shop_product_auction (ix, pid,startdate, enddate, plusdate, startprice, plus_count, plus_use_count, regdate) values ('','$INSERT_PRODUCT_ID','$startdate','$enddate','$enddate','$startprice','$plus_count',0,NOW())");
    } else if ($product_type == "7") {//스페셜카테고리 자동차 상품일때
        $vintage = $vintage_year . "-" . $vintage_month;
        $sql = "insert into shop_product_car(pid,vechile_div,mf_ix,md_ix,gr_ix,vt_ix,vintage,mileage,displacement,transmission,color,fuel,license_plate,car_condition,regdate) values('$INSERT_PRODUCT_ID','$vechile_div','$mf_ix','$md_ix','$gr_ix','$vt_ix','$vintage','$mileage','$displacement','$transmission','$color','$fuel','$license_plate','$car_condition',NOW())";

        $db->query($sql);
    } else if ($product_type == "8") {//스페셜카테고리 부동산 상품일때
        $sql = "insert into shop_product_property(pid,rg_ix,dimensions,deal_type,property_type,loans,maintenance_cost,heating_fuel,posisbile_date,regdate)
				values
				('$INSERT_PRODUCT_ID','$rg_ix','$dimensions','$deal_type','$property_type','$loans','$maintenance_cost','$heating_fuel','$posisbile_date',NOW())";

        $db->query($sql);
    } else if ($product_type == "9") {//스페셜카테고리 부동산 상품일때
        $sql = "insert into shop_product_hotel
					(pid,rg_ix,hotel_level,room_level,regdate)
					values
					('$INSERT_PRODUCT_ID','$hotel_rg_ix','$hotel_level','$room_level',NOW()) ";

        $db->query($sql);
    } else if ($product_type == "10") {//스페셜카테고리 여행 상품일때
        $sql = "insert into shop_product_sightseeing
					(pid,rg_ix,regdate)
					values
					('$INSERT_PRODUCT_ID','$sightseeing_rg_ix',NOW()) ";

        $db->query($sql);
    }

    //CopyImage($INSERT_PRODUCT_ID, "");
	if($_POST['imgInsYN']){
		CopyImage3($INSERT_PRODUCT_ID, "");
	}
    //CopyImage($INSERT_PRODUCT_ID, "_rectangular");

    $uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product", $INSERT_PRODUCT_ID, 'Y');

    if ($download_image_size > 0 || $download_desc_size > 0) {
        $path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/download/";
        if (!is_dir($path)) {
            mkdir($path, 0777);
            chmod($path, 0777);
        }

        $path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/download/" . md5("FORBIZ" . $INSERT_PRODUCT_ID) . "/";
        if (!is_dir($path)) {
            mkdir($path, 0777);
            chmod($path, 0777);
        }
    }

    if ($download_image_size > 0) {
        move_uploaded_file($download_image, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/download/" . md5("FORBIZ" . $INSERT_PRODUCT_ID) . "/" . iconv("cp949", "utf-8", $download_image_name));
        $sql = "UPDATE " . TBL_SHOP_PRODUCT . " SET download_img='" . $download_image_name . "' WHERE id='" . $INSERT_PRODUCT_ID . "' ";
        $db->query($sql);
    }

    if ($download_desc_size > 0) {
        move_uploaded_file($download_desc, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/download/" . md5("FORBIZ" . $INSERT_PRODUCT_ID) . "/" . iconv("cp949", "utf-8", $download_desc_name));
        $sql = "UPDATE " . TBL_SHOP_PRODUCT . " SET download_desc='" . $download_desc_name . "' WHERE id='" . $INSERT_PRODUCT_ID . "' ";
        $db->query($sql);
    }

    if ($chk_deepzoom == 1) {
        $client = new SoapClient("http://" . $_SERVER["HTTP_HOST"] . "/VESAPI/VESAPIWS.asmx?wsdl=0");
        //print_r($client);
        $params = new stdClass();
        $params->inputPhysicalPathString = $basic_img_src;
        $params->outputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/deepzoom/" . $INSERT_PRODUCT_ID;

        $response = $client->TilingWithPhysicalPath($params);
    }

    $sql = "INSERT INTO " . TBL_SHOP_PRICEINFO . " (id, pid, listprice, sellprice, coprice, reserve,  company_id, charger_info, regdate,wholesale_price,wholesale_sellprice) ";
    $sql .= " values('', '" . $INSERT_PRODUCT_ID . "','$listprice','$sellprice', '$coprice', '$reserve',  '" . $admininfo[company_id] . "','[" . $admininfo[company_name] . "] " . $admininfo[charger] . "(" . $admininfo[charger_id] . ")', NOW(),'" . $wholesale_price . "','" . $wholesale_sellprice . "') ";
    $db2->sequences = "SHOP_PRICEINFO_SEQ";
    $db2->query($sql);

    $db2->query("update shop_product_buyingservice_priceinfo set bs_use_yn = '0' where pid ='" . $INSERT_PRODUCT_ID . "'");
    $sql = "insert into shop_product_buyingservice_priceinfo(bsp_ix,pid,orgin_price,exchange_rate,air_wt,air_shipping,duty,clearance_fee,clearance_type,bs_fee_rate,bs_fee,bs_use_yn,regdate)
					values('','" . $INSERT_PRODUCT_ID . "','$orgin_price','$exchange_rate','$air_wt','$air_shipping','$duty','$clearance_fee','$clearance_type','$bs_fee_rate','$bs_fee','1',NOW()) ";
    //echo $sql;
    $db2->sequences = "SHOP_GOODS_BS_PRICEINFO_SEQ";
    $db2->query($sql);


    //아카마이 ftp 파일 업로드
    $akamaiFtpUploadFiles = array();

    if ($goods_desc_copy) {

        $data_text_convert = str_replace("\\", "", $data_text_convert);
        $data_text_convert = str_replace("&#39;", '"', $data_text_convert);
        $data_text_convert = str_replace("&quot;", '"', $data_text_convert);

        preg_match_all("|<img .*src=\"(.*)\".*>|U", $data_text_convert, $out, PREG_PATTERN_ORDER);

        $path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/";

        //if(count($out)>2){
        if (substr_count($data_text_convert, "<img") > 0) {
            if (!is_dir($path)) {
                mkdir($path, 0777);
                chmod($path, 0777);
            } else {
                chmod($path, 0777);
            }
        }

        for ($i = 0; $i < count($out); $i++) {
            for ($j = 0; $j < count($out[$i]); $j++) {

                $img = returnImagePath($out[$i][$j]);
                $img = ClearText($img);
                try {
                    if ($img) {
                        if (substr_count($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/") == 0) {// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
                            if (substr_count($img, $_SERVER["HTTP_HOST"]) > 0) {

                                $local_img_path = str_replace("http://" . $_SERVER["HTTP_HOST"], $_SERVER["DOCUMENT_ROOT"], $img);

                                @copy($local_img_path, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img));

                                $akamaiFtpUploadFiles[] = returnFileName($img);

                                if (substr_count($img, $admin_config[mall_data_root] . "/images/upfile/") > 0) {
                                    unlink($local_img_path);
                                }

                                $basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
                            } else {
                                if (substr_count($img, $DOCUMENT_ROOT)) {
                                    //$img = $DOCUMENT_ROOT.$img;
                                    if (@copy($img, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($DOCUMENT_ROOT . $img))) {
                                        $basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환

                                        $akamaiFtpUploadFiles[] = returnFileName($img);
                                    }
                                } else {
                                    if (@copy($_SERVER["DOCUMENT_ROOT"] . $img, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img))) {
                                        $basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환

                                        $akamaiFtpUploadFiles[] = returnFileName($img);
                                    }
                                }
                                //echo ":::".$img."<br>";
                            }
                        }
                    }

                } catch (Exception $e) {
                    // 에러처리 구문
                    //exit($e->getMessage());
                }

            }
        }

        $basicinfo = str_replace("http://" . $_SERVER["HTTP_HOST"], "", $basicinfo);
        $db->query("UPDATE " . TBL_SHOP_PRODUCT . " SET basicinfo = '$basicinfo' , etc8= '$etc8', etc9= '$etc9' WHERE id='$INSERT_PRODUCT_ID'");
    }

    if ($m_goods_desc_copy) {

        $m_data_text_convert = str_replace("\\", "", $m_data_text_convert);
        $m_data_text_convert = str_replace("&#39;", '"', $m_data_text_convert);
        $m_data_text_convert = str_replace("&quot;", '"', $m_data_text_convert);

        preg_match_all("|<img .*src=\"(.*)\".*>|U", $m_data_text_convert, $out, PREG_PATTERN_ORDER);

        $path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/";

        //if(count($out)>2){
        if (substr_count($m_data_text_convert, "<img") > 0) {
            if (!is_dir($path)) {
                mkdir($path, 0777);
                chmod($path, 0777);
            } else {
                chmod($path, 0777);
            }
        }

        for ($i = 0; $i < count($out); $i++) {
            for ($j = 0; $j < count($out[$i]); $j++) {

                $img = returnImagePath($out[$i][$j]);
                $img = ClearText($img);
                try {
                    if ($img) {
                        if (substr_count($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/") == 0) {// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
                            if (substr_count($img, $_SERVER["HTTP_HOST"]) > 0) {

                                $local_img_path = str_replace("http://" . $_SERVER["HTTP_HOST"], $_SERVER["DOCUMENT_ROOT"], $img);

                                @copy($local_img_path, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img));
                                if (substr_count($img, $admin_config[mall_data_root] . "/images/upfile/") > 0) {
                                    unlink($local_img_path);
                                }

                                $akamaiFtpUploadFiles[] = returnFileName($img);

                                $m_basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $m_basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
                            } else {
                                if (substr_count($img, $DOCUMENT_ROOT)) {
                                    //$img = $DOCUMENT_ROOT.$img;
                                    if (@copy($img, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($DOCUMENT_ROOT . $img))) {
                                        $m_basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $m_basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환

                                        $akamaiFtpUploadFiles[] = returnFileName($img);
                                    }
                                } else {
                                    if (copy($_SERVER["DOCUMENT_ROOT"] . $img, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img))) {
                                        $m_basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $m_basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환

                                        $akamaiFtpUploadFiles[] = returnFileName($img);
                                    }
                                }
                                //echo ":::".$img."<br>";
                            }
                        }
                    }

                } catch (Exception $e) {
                    // 에러처리 구문
                    //exit($e->getMessage());
                }

            }
        }

        $m_basicinfo = str_replace("http://" . $_SERVER["HTTP_HOST"], "", $m_basicinfo);
        $db->query("UPDATE " . TBL_SHOP_PRODUCT . " SET m_basicinfo = '$m_basicinfo' WHERE id='$INSERT_PRODUCT_ID'");
    }

    akamaiFtpUpload($admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail", $akamaiFtpUploadFiles);


    //오라클에서 unix_timestamp는 FUNCTION으로 만듬 FUNCTION은 맨아래에 주석처리 해놓음 2013-03-22 홍진영
    $db->query("UPDATE " . TBL_SHOP_PRODUCT . " SET regdate_desc = unix_timestamp(regdate)*-1 WHERE id='$INSERT_PRODUCT_ID'");

    //상품상세 이미지 복사 를 클릭하지 않으면 이 부분에 대한 업데이트를 안함. 관리자 상품 리스트엔 무조건 이 쿼리가 실행되어야 리스트 쿼리 조건에 충족하는 상태가 됨 그래서 밖으로 뺌 kbk 13/02/27

    $pid = $INSERT_PRODUCT_ID;

    //상품필수고시 - START
    if (is_array($mandatory_info)) {
        foreach ($mandatory_info as $m_info) {
            if ($m_info[pmi_title] != "" || $m_info[pmi_code] != "") {
                $sql = "insert into shop_product_mandatory_info(pmi_ix,pid,pmi_code,pmi_title,pmi_desc,insert_yn,regdate) values('','" . $pid . "','" . $m_info[pmi_code] . "','" . $m_info[pmi_title] . "','" . $m_info[pmi_desc] . "','Y',NOW())";
                $db->sequences = "SHOP_GOODS_MANDATORY_INFO_SEQ";
                $db->query($sql);
            }
        }
    }
    //상품필수고시 - END


    //상품필수고시_글로벌 - START
    if (is_array($mandatory_info_global)) {
        foreach ($mandatory_info_global as $m_info) {
            if ($m_info[pmi_title] != "" || $m_info[pmi_code] != "") {
                $sql = "insert into shop_product_mandatory_info_global (pmi_ix,pid,pmi_code,pmi_title,pmi_desc,insert_yn,regdate) values('','" . $pid . "','" . $m_info[pmi_code] . "','" . $m_info[pmi_title] . "','" . $m_info[pmi_desc] . "','Y',NOW())";
                $db->sequences = "SHOP_GOODS_MANDATORY_INFO_SEQ";
                $db->query($sql);
            }
        }
    }
    //상품필수고시 - END

    //코디옵션 추가 작업 시작 2014-01-09 이학봉
    if ($product_type == "99") {
        if ($use_option_type == "box_option") {
            unset($stock_options);
            unset($set2options);
            unset($codi_options);
        } else if ($use_option_type == "set_option") {
            unset($box_options);
            unset($set2options);
            unset($stock_options);
            unset($codi_options);
        } else if ($use_option_type == "set2_option") {
            unset($box_options);
            unset($stock_options);
            unset($codi_options);
        } else if ($use_option_type == "codi_option") {    //코디옵션
            unset($box_options);
            unset($set2options);
            unset($stock_options);
        } else {
            unset($stock_options);
            unset($set2options);
            unset($box_options);
            unset($codi_options);
        }
    } else if ($product_type == "4" || $product_type == "21") {
        unset($box_options);
    } else if ($product_type == "31") {
        unset($box_options);
        unset($set2options);
    } else if ($product_type == "77") {    //사은품 상품은 세트(묶음상품)옵션만(set2options) 사용 2014-04-09 이학봉 unset($set2options); 사용안하게 바꿈 사은품에서 사용할 필요 없슴 JK 151112
        unset($box_options);        //박스 옵션
        unset($stock_options);        //가격 + 재고관리 옵션
        unset($codi_options);        //코디 옵션
        unset($set2options);
    } else {
        unset($box_options);
        unset($set2options);

        if ($stock_use_yn == "N") {
            unset($stock_options);
        }
    }
    //상품등록시 옵션추가 부분 체크 xuefeng 코디옵션추가
    OptionUpdate($db, $pid, $stock_options[0], "b");
    OptionUpdate($db, $pid, $box_options[0], "x");
    for ($i = 0; $i < count($addoptions); $i++) {
        //print_r($addoptions[$i]);
        OptionUpdate($db, $pid, $addoptions[$i], "a");
    }
    CodiOptionUpdate($db, $pid, $codi_options);    //코디옵션
    SetOptionUpdate($db, $pid, $set2options);

    //코디옵션 추가 작업 시작 2014-01-09 이학봉

    if ($option_all_use == "Y") {
        $sql = "select opn_ix from " . TBL_SHOP_PRODUCT_OPTIONS . " where pid = '$pid' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ";
        //echo $sql."<br><br>";
        $db->query($sql);
        if ($db->total) {
            $del_options = $db->fetchall();
            //print_r($del_options);
            for ($i = 0; $i < count($del_options); $i++) {
                $db->query("delete from " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " where opn_ix='" . $del_options[$i][opn_ix] . "' and pid = '$pid' ");
            }
        }
        $db->query("delete from " . TBL_SHOP_PRODUCT_OPTIONS . " where pid = '$pid' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ");
    } else {
        if ($stock_use_yn == "N" && $product_type == "0") {
            if (is_array($options)) {
                $option_vieworder = 1;
                foreach ($options as $ops_key => $ops_value) {

                    if ($options[$ops_key]["option_name"]) {

                        if ($options[$ops_key]["option_use"]) {
                            $options_use = $options[$ops_key]["option_use"];
                        } else {
                            $options_use = 0;
                        }

                        if (count($options[$i]["global_oinfo"]) > 0) {
                            foreach ($options[$i]["global_oinfo"] as $colum => $li) {
                                foreach ($li as $ln => $val) {
                                    $options[$i]["global_oinfo"][$colum][$ln] = urlencode($val);
                                }
                            }
                        }

                        $options[$i]["global_oinfo"] = json_encode($options[$i]["global_oinfo"]);

                        $sql = "INSERT INTO " . TBL_SHOP_PRODUCT_OPTIONS . " (opn_ix, pid, global_oinfo, option_name, option_kind, option_type, option_use, option_vieworder, regdate)
										VALUES
										('','$pid','" . $options[$i]["global_oinfo"] . "','" . $options[$ops_key]["option_name"] . "','" . strtolower($options[$ops_key]["option_kind"]) . "','" . $options[$ops_key]["option_type"] . "','" . $options_use . "','" . $option_vieworder . "',NOW())";
                        $db->sequences = "SHOP_GOODS_OPTIONS_SEQ";
                        $db->query($sql);

                        if ($db->dbms_type == "oracle") {
                            $opn_ix = $db->last_insert_id;
                        } else {
                            $db->query("SELECT opn_ix FROM " . TBL_SHOP_PRODUCT_OPTIONS . " WHERE opn_ix=LAST_INSERT_ID()");
                            $db->fetch();
                            $opn_ix = $db->dt[opn_ix];
                        }

                        $sql = "update " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " set insert_yn='N'	where opn_ix='" . $opn_ix . "' and pid = '$pid' ";
                        $db->query($sql);
                        $jj = 0;
                        foreach ($options[$ops_key]["details"] as $od_key => $od_value) {
                            if ($options[$ops_key][details][$od_key][option_div]) {

                                if (count($options[$i][details][$j]["global_odinfo"]) > 0) {
                                    foreach ($options[$i][details][$j]["global_odinfo"] as $colum => $li) {
                                        foreach ($li as $ln => $val) {
                                            $options[$i][details][$j]["global_odinfo"][$colum][$ln] = urlencode($val);
                                        }
                                    }
                                }

                                $options[$i][details][$j]["global_odinfo"] = json_encode($options[$i][details][$j]["global_odinfo"]);

                                $sql = "INSERT INTO " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " (id, pid, opn_ix, global_odinfo, set_group_seq, option_div, option_code,option_coprice,option_price, option_stock, option_safestock, option_soldout, option_etc1) ";
                                $sql = $sql . " values('','$pid','" . $opn_ix . "','" . $options[$i][details][$j]["global_odinfo"] . "','" . $jj . "','" . trim($options[$ops_key][details][$od_key][option_div]) . "','" . $options[$ops_key][details][$od_key][code] . "','" . $options[$ops_key][details][$od_key][coprice] . "','" . $options[$ops_key][details][$od_key][price] . "','0','0','" . $options[$ops_key][details][$od_key][option_soldout] . "','" . $options[$ops_key][details][$od_key][etc1] . "') ";

                                $db->sequences = "SHOP_GOODS_OPTIONS_DT_SEQ";
                                $db->query($sql);

                                $jj++;
                            }
                        }
                    }
                    $option_vieworder++;
                }
            }
        } else {
            $db->query("SELECT opn_ix FROM " . TBL_SHOP_PRODUCT_OPTIONS . " WHERE pid = '" . $pid . "' and option_kind in ('c1','c2','i1','i2','p','s','r','g')");

            if ($db->total) {
                $basic_options = $db->fetchall("object");
                foreach ($basic_options as $bok => $bov) {
                    $sql = "delete from " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " where opn_ix='" . $bov[opn_ix] . "'  ";
                    $db->query($sql);
                }
            }
            $sql = "delete from " . TBL_SHOP_PRODUCT_OPTIONS . " where pid = '" . $pid . "' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ";
            $db->query($sql);
        }
    }// option_all_use 있는지 여부

    if (is_array($display_options)) {

        //2012-07-30 홍진영
        foreach ($display_options as $display_option) {
            if ($display_option["dp_title"] && $display_option["dp_desc"]) {
                if ($display_option["dp_use"]) {
                    $dp_use = $display_option["dp_use"];
                } else {
                    $dp_use = "0";
                }

                $sql = "insert into " . TBL_SHOP_PRODUCT_DISPLAYINFO . " (dp_ix,pid,dp_title,dp_desc,dp_etc_desc,dp_use, regdate) values('','$pid','" . $display_option["dp_title"] . "','" . $display_option["dp_desc"] . "','" . $display_option["dp_etc_desc"] . "','" . $dp_use . "',NOW()) ";
                $db->sequences = "SHOP_GOODS_DISPLAYINFO_SEQ";
                $db->query($sql);
            }
        }
    }

    /**
     * 2013.06.09 신훈식
     * 바이럴 정보 등록프로세스
     *
     **/
    if (is_array($virals)) {
        foreach ($virals as $do_key => $do_value) {
            if ($virals[$do_key]["viral_name"] && $virals[$do_key]["viral_url"]) {

                if ($virals[$do_key]["vi_use"]) {
                    $vi_use = $virals[$do_key]["vi_use"];
                } else {
                    $vi_use = "0";
                }
                $sql = "insert into shop_product_viralinfo (vi_ix,pid,viral_name,viral_url,viral_desc, vi_use, regdate) values('','$pid','" . $virals[$do_key]["viral_name"] . "','" . $virals[$do_key]["viral_url"] . "','" . $virals[$do_key]["viral_desc"] . "','" . $vi_use . "',NOW()) ";
                //echo $sql;


                $db->sequences = "SHOP_GOODS_VIRALINFO_SEQ";
                $db->query($sql);
            }
        }
    }

    // 관련상품등록
    if (is_array($rpid)) {
        if ($rpid[1]) {
            for ($i = 0; $i < count($rpid[1]); $i++) {
                $sql = "insert into " . TBL_SHOP_RELATION_PRODUCT . " (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','" . $rpid[1][$i] . "','" . $i . "','N',NOW())";
                $db->sequences = "SHOP_RELATION_PRODUCT_SEQ";
                $db->query($sql);
            }
        }

        if ($rpid[2]) {
            for ($i = 0; $i < count($rpid[2]); $i++) {
                $sql = "insert into shop_relation_product2 (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','" . $rpid[2][$i] . "','" . $i . "','N',NOW())";
                $db->query($sql);
            }
        }

        if ($rpid[3]) {
            for ($i = 0; $i < count($rpid[3]); $i++) {
                $sql = "insert into shop_relation_add_product (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','" . $rpid[3][$i] . "','" . $i . "','N',NOW())";
                $db->query($sql);
            }
        }

		if ($rpid[4]) {
            for ($i = 0; $i < count($rpid[4]); $i++) {
                $sql = "insert into shop_relation_product3 (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','" . $rpid[4][$i] . "','" . $i . "','N',NOW())";
                $db->query($sql);
            }
        }
    }

    //사은품등록
    if (is_array($fpid)) {
        if ($fpid[1]) {
            for ($i = 0; $i < count($fpid[1]); $i++) {
                $sql = "insert into shop_product_gift (pid,gift_pid,vieworder,insert_yn,regdate) values ('$pid','" . $fpid[1][$i] . "','" . $i . "','Y',NOW())";
                $db->sequences = "SHOP_GIFT_PRODUCT_SEQ";
                $db->query($sql);
            }
        }
    }

    // 관련상품 카테고리
    if (is_array($category)) {
        if ($category[0]) {
            for ($j = 0; $j < count($category[0]); $j++) {
                $sql = "insert into shop_relation_category (rc_ix,cid,depth,pid, vieworder, insert_yn, regdate) values ('','" . $category[0][$j] . "','" . $depth[0][$j] . "','" . $pid . "','" . $j . "','Y', NOW())";//depth 컬럼 추가 kbk 13/07/01
                $db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
                $db->query($sql);
            }
        }
    }

    // 세트상품
    if ($rpid[2]) {
        for ($i = 0; $i < count($rpid[2]); $i++) {
            $sql = "insert into shop_product_set_relation (psr_ix,pid,set_pid,vieworder,insert_yn,regdate) values ('','$pid','" . $rpid[2][$i] . "','" . $i . "','N',NOW())";
            $db->sequences = "SHOP_GOODS_SET_LINK_SEQ";
            $db->query($sql);
        }
    }

    //2012-07-30 홍진영
    /*$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/addimg", $pid, 'Y');

    if (is_array($addimages)) {
        $addimages_keys = array_keys($addimages);
    }

    for ($j = 0; $j < count($addimages); $j++) {

        $i = $addimages_keys[$j];

        if ($_FILES[addimages][size][$i][addbimg] > 0) {
            $image_info = getimagesize($_FILES[addimages][tmp_name][$i][addbimg]);

            if ($_FILES[addimages][name][$i][addbimg]) {
                if ($_SESSION["admininfo"]["mall_ix"] == "cb04d740160969f940ae3aaa3fae5ee0") {//lub2b.co.kr 에서만 강제적으로 딥줌 생성되도록 함 kbk 12/12/26
                    $sql = "INSERT INTO " . TBL_SHOP_ADDIMAGE . " (id, pid, deepzoom, regdate) values('', '$pid','1',  NOW()) ";
                } else {
                    $sql = "INSERT INTO " . TBL_SHOP_ADDIMAGE . " (id, pid, deepzoom, regdate) values('', '$pid','" . $addimages[$i][add_copy_deepzoomimg] . "',  NOW()) ";
                }
                $db->sequences = "SHOP_ADDIMAGE_SEQ";
                $db->query($sql);

                if ($db->dbms_type == "oracle") {
                    $ad_ix = $db->last_insert_id;
                } else {
                    $db->query("SELECT id FROM " . TBL_SHOP_ADDIMAGE . " WHERE id=LAST_INSERT_ID()");
                    $db->fetch();
                    $ad_ix = $db->dt[id];
                }
            }

            $image_info = getimagesize($_FILES[addimages][tmp_name][$i][addbimg]);
            $image_type = substr($image_info['mime'], -3);

            if ($_FILES[addimages][size][$i][addbimg] > 0) {
                if ($image_type == "gif") {
                    copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif");
                    MirrorGif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", MIRROR_NONE);
                    resize_gif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $image_info2[0][width], $image_info2[0][height], $image_resize_type);

                    if ($addimages[$i][add_chk_mimg] == 1) {
                        MirrorGif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif", MIRROR_NONE);
                        resize_gif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);
                    }

                    if ($addimages[$i][add_chk_cimg] == 1) {
                        MirrorGif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif", MIRROR_NONE);
                        resize_gif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);
                    }

                } else {
                    copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif");
                    Mirror($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", MIRROR_NONE);
                    resize_jpg($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $image_info2[0][width], $image_info2[0][height], $image_resize_type);

                    if ($addimages[$i][add_chk_mimg] == 1) {
                        Mirror($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif", MIRROR_NONE);
                        resize_jpg($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);
                    }

                    if ($addimages[$i][add_chk_cimg] == 1) {
                        Mirror($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif", MIRROR_NONE);
                        resize_jpg($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);
                    }
                }

                //if($addimages[$i][add_copy_deepzoomimg] == 1){
                if ($_SESSION["admininfo"]["mall_ix"] == "cb04d740160969f940ae3aaa3fae5ee0") {//lub2b.co.kr 에서만 강제적으로 딥줌 생성되도록 함 kbk 12/12/26
                    $path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg/deepzoom";

                    //if(count($out)>2){

                    if (!is_dir($path)) {
                        mkdir($path, 0777);
                        chmod($path, 0777);
                    } else {
                        chmod($path, 0777);
                    }

                    $client = new SoapClient("http://" . $_SERVER["HTTP_HOST"] . "/VESAPI/VESAPIWS.asmx?wsdl=0");
                    //print_r($client);
                    $params = new stdClass();
                    $params->inputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif";
                    $params->outputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg/deepzoom/" . $ad_ix;

                    $response = $client->TilingWithPhysicalPath($params);
                }
            }
            //$db->debug = false;
            if ($_FILES[addimages][size][$i][addmimg] > 0) {
                move_uploaded_file($_FILES[addimages][tmp_name][$i][addmimg], $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif");
            }

            if ($_FILES[addimages][size][$i][addcimg] > 0) {
                move_uploaded_file($_FILES[addimages][tmp_name][$i][addcimg], $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif");
            }

            //아카마이 ftp 파일 업로드
            $akamaiFtpUploadFiles = array();
            $akamaiFtpUploadFiles[] = "basic_" . $ad_ix . "_add.gif";
            $akamaiFtpUploadFiles[] = "b_" . $ad_ix . "_add.gif";
            $akamaiFtpUploadFiles[] = "m_" . $ad_ix . "_add.gif";
            $akamaiFtpUploadFiles[] = "c_" . $ad_ix . "_add.gif";
            akamaiFtpUpload($admin_config[mall_data_root] . "/images/addimg" . $adduploaddir, $akamaiFtpUploadFiles);
        }
    }*/

    //기본 정책이 무료인정책 뽑아내기
    $wholesale_free_delivery_yn = 0;
    $free_delivery_yn = 0;
    $sql = "select 
			pd.is_wholesale
		from 
			shop_product_delivery as pd 
			inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
		where
			pd.pid='" . $pid . "' and dt.delivery_policy = '1'
		group by pd.is_wholesale ";
    $db->query($sql);

    if ($db->total) {
        $db->fetch(0);
        if ($db->dt[is_wholesale] == "W") {
            $wholesale_free_delivery_yn = 1;
        }
        if ($db->dt[is_wholesale] == "R") {
            $free_delivery_yn = 1;
        }
        $db->fetch(1);
        if ($db->dt[is_wholesale] == "W") {
            $wholesale_free_delivery_yn = 1;
        }
        if ($db->dt[is_wholesale] == "R") {
            $free_delivery_yn = 1;
        }
    }


    $sql = "select
				GROUP_CONCAT(pod.option_div SEPARATOR '|') as option_div_text, GROUP_CONCAT(pod.option_gid SEPARATOR '|') as gid_text
			from 
				shop_product_options po 
				left join shop_product_options_detail pod on (po.pid = pod.pid and po.opn_ix = pod.opn_ix)
			where 
				po.pid='" . $pid . "' and po.option_use = '1'
			group by po.pid ";
    $db->query($sql);
    $db->fetch();
    $option_div_text = $db->dt[option_div_text];
    $gid_text = $db->dt[gid_text];

    if ($gid) {
        $gid_text = $gid;
    }

    //추가 정보 입력하기!!
    $sql = "insert into shop_product_addinfo (pid,option_div_text,gid_text,wholesale_free_delivery_yn,free_delivery_yn) values ('" . $pid . "','" . $option_div_text . "','" . $gid_text . "','" . $wholesale_free_delivery_yn . "','" . $free_delivery_yn . "')";
    $db->query($sql);


    if ($mode == "copy") {
        ////////////////////////////////////////////// 옵션 정보 복사 루틴 //////////////////////////////////////////////////////////

    }

    if (is_array($partner_prd_reg) && count($partner_prd_reg) > 0) {
        foreach ($partner_prd_reg as $p_reg) {
            $sql = "insert into sellertool_get_product (pid,site_code,state) values ('" . $pid . "','" . $p_reg . "','1')";
            $db->query($sql);
        }
    } else {
        $sql = "select * from sellertool_site_info where api_yn = 'Y' and site_code not in (select site_code from sellertool_not_company where state= '1' and company_id = '" . $company_id . "')";
        $db->query($sql);
        $site_infos = $db->fetchall("object");
        if (count($site_infos) > 0) {
            foreach ($site_infos as $si) {
                $sql = "insert into sellertool_get_product (pid,site_code,state) values ('" . $pid . "','" . $si['site_code'] . "','1')";
                $db->query($sql);
            }
        }
    }

    $sql = "insert into shop_product_history (ph_ix, history_date, id, product_type, pname, product_color_chip, pcode, brand, brand_name, c_ix, company, paper_pname, buying_company, shotinfo, buyingservice_coprice, wholesale_price, wholesale_sellprice, listprice, sellprice, coprice, wholesale_yn, offline_yn, wholesale_reserve_yn, wholesale_reserve, wholesale_reserve_rate, wholesale_rate_type, reserve_yn, reserve, reserve_rate, rate_type, sns_btn_yn, sns_btn, delivery_coupon_yn, coupon_use_yn, bimg, mimg, msimg, simg, cimg, basicinfo, m_basicinfo, icons, state, product_weight, is_adult, disp, movie,movie_thumbnail,movie_now, vieworder, admin, trade_admin, md_code, sell_ing_cnt, stock, safestock, available_stock, remain_stock, view_cnt, order_cnt, recommend_cnt, wish_cnt, after_score, after_cnt, product_point, product_level, search_keyword, reg_category, option_stock_yn, supply_company, inventory_info, surtax_yorn, delivery_company, one_commission, commission, wholesale_commission, account_type, cupon_use_yn, stock_use_yn, delivery_policy, delivery_product_policy, delivery_package, delivery_freeprice, delivery_price, delivery_type, free_delivery_yn, free_delivery_count, is_sell_date, sell_priod_sdate, sell_priod_edate, allow_max_cnt, wholesale_allow_max_cnt, allow_basic_cnt, wholesale_allow_basic_cnt, allow_order_type, allow_order_cnt_byonesell, allow_order_cnt_byoneperson, allow_order_minimum_cnt, allow_byoneperson_cnt, wholesale_allow_byoneperson_cnt, md_one_commission, md_discount_name, md_sell_date_use, md_sell_priod_sdate, md_sell_priod_edate, whole_head_company_sale_rate, whole_seller_company_sale_rate, head_company_sale_rate, seller_company_sale_rate, origin, make_date, expiry_date, mandatory_type, mandatory_type_global, relation_product_cnt, relation_text1, relation_text2, relation_display_type, barcode, input_date, is_auto_change, auto_change_state, etc1, etc2, etc3, etc4, etc5, etc6, etc7, etc8, etc9, etc10, download_img, download_desc, hotcon_event_id, hotcon_pcode, co_goods, co_pid, co_company_id, bs_goods_url, bs_site, price_policy, currency_ix, round_precision, round_type, editdate, naver_update_date, disp_naver, disp_daum, add_index_date, is_pos_link, is_erp_link, add_status, regdate, regdate_desc, reg_charger_ix, reg_charger_name, is_delete, auto_sync_wms)
	(select '' as ph_ix, NOW(), id, product_type, pname, product_color_chip, pcode, brand, brand_name, c_ix, company, paper_pname, buying_company, shotinfo, buyingservice_coprice, wholesale_price, wholesale_sellprice, listprice, sellprice, coprice, wholesale_yn, offline_yn, wholesale_reserve_yn, wholesale_reserve, wholesale_reserve_rate, wholesale_rate_type, reserve_yn, reserve, reserve_rate, rate_type, sns_btn_yn, sns_btn, delivery_coupon_yn, coupon_use_yn, bimg, mimg, msimg, simg, cimg, basicinfo, m_basicinfo, icons, state, product_weight, is_adult, disp, movie,movie_thumbnail,movie_now, vieworder, admin, trade_admin, md_code, sell_ing_cnt, stock, safestock, available_stock, remain_stock, view_cnt, order_cnt, recommend_cnt, wish_cnt, after_score, after_cnt, product_point, product_level, search_keyword, reg_category, option_stock_yn, supply_company, inventory_info, surtax_yorn, delivery_company, one_commission, commission, wholesale_commission, account_type, cupon_use_yn, stock_use_yn, delivery_policy, delivery_product_policy, delivery_package, delivery_freeprice, delivery_price, delivery_type, free_delivery_yn, free_delivery_count, is_sell_date, sell_priod_sdate, sell_priod_edate, allow_max_cnt, wholesale_allow_max_cnt, allow_basic_cnt, wholesale_allow_basic_cnt, allow_order_type, allow_order_cnt_byonesell, allow_order_cnt_byoneperson, allow_order_minimum_cnt, allow_byoneperson_cnt, wholesale_allow_byoneperson_cnt, md_one_commission, md_discount_name, md_sell_date_use, md_sell_priod_sdate, md_sell_priod_edate, whole_head_company_sale_rate, whole_seller_company_sale_rate, head_company_sale_rate, seller_company_sale_rate, origin, make_date, expiry_date, mandatory_type,mandatory_type_global, relation_product_cnt, relation_text1, relation_text2, relation_display_type, barcode, input_date, is_auto_change, auto_change_state, etc1, etc2, etc3, etc4, etc5, etc6, etc7, etc8, etc9, etc10, download_img, download_desc, hotcon_event_id, hotcon_pcode, co_goods, co_pid, co_company_id, bs_goods_url, bs_site, price_policy, currency_ix, round_precision, round_type, editdate, naver_update_date, disp_naver, disp_daum, add_index_date, is_pos_link, is_erp_link, add_status, regdate, regdate_desc, reg_charger_ix, reg_charger_name, is_delete, auto_sync_wms from shop_product sp where id = '" . $pid . "')";
    $db->query($sql);

    if (!$bs_act) {

        if ($act == "tmp_insert") {
            if (in_array($product_type, $sns_product_type)) {
                echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../sns/goods_input.php?id=" . $pid . "';</script>";
            } else {
                echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/goods_input.php?id=" . $pid . "';</script>";
            }
        } else {
            if ($mmode == "pop") {
                echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품등록이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
            } else {
                if (in_array($product_type, $sns_product_type)) {
                    echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../sns/product_list.php';</script>";
                } else {
                    echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/product_list.php';</script>";
                }
            }
        }
    }
}

if ($act == "delete") {    //상품삭제

    $db->query("update " . TBL_SHOP_PRODUCT . " p SET  editdate = NOW(), is_delete='1' , state='2' , disp='0'  WHERE id='" . $id . "'");
    if ($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S') {
        $sfb = new sphinxfb(); // mysql 데이터베이스
        $sfb->remove(" id ='" . (int)$id . "' ");
    }

    input_filter_info($product_filter, $id, 'delete');

    if (in_array($product_type, $sns_product_type)) {
        echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../sns/product_list.php';</script>";
    } else {
        echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품삭제가 정상적으로 처리 되었습니다.');parent.document.location.href='../product/product_list.php';</script>";
    }
}

if ($act == "update" || $act == "tmp_update") {    //상품 업데이트
    //상품 업데이트 시 가격정보에 입력하지 않은 데이터가 들어간다하여 전달 받은 정보를 기록하여 확인하기 위한 로그 기록 추가 JK 20200129
    $sql = "insert into shop_product_update_in_log (pid,data,regdate) values ('".$id."','".urlencode(json_encode($_POST))."',NOW()) ";
    $db->query($sql);
    //끝

    $db->query("select * from shop_image_resizeinfo order by idx");
    $image_info2 = $db->fetchall();

    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product/")) {
        mkdir($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product/");
        chmod($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product/", 0777);
    }

    $pname = str_replace("'", "&#39;", $pname);

    //예외처리
    $basicinfo = str_replace("''", "", $basicinfo);
    $basicinfo = str_replace("'", "&#39;", $basicinfo);
    $basicinfo = str_replace('"', "&quot;", $basicinfo);
    //$basicinfo = strip_tags($basicinfo, "<img><table><th><col><colgroup><tbody><tr><td><div><a><ul><li><dl><dt><p><h><font><strong><br><span><iframe>");    //2014-07-27 edit 태그 예외처리 이학봉

    $m_basicinfo = str_replace("''", "", $m_basicinfo);
    $m_basicinfo = str_replace("'", "&#39;", $m_basicinfo);
    $m_basicinfo = str_replace('"', "&quot;", $m_basicinfo);
    //$m_basicinfo = strip_tags($m_basicinfo, "<img><table><th><col><colgroup><tbody><tr><td><div><a><ul><li><dl><dt><p><h><font><strong><br><span><iframe>");    //2014-07-27 edit 태그 예외처리 이학봉

    $uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product", $id, 'Y');
    $adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg", $id, 'Y');

    //상품 수정 히스토리 쌓기 2014-04-09 이학봉
    product_edit_history($_POST, $_FILES, $id);
    //상품 수정 히스토리 쌓기 2014-04-09 이학봉
    //CopyImage($id, "");
	if($_POST['imgInsYN']){
		CopyImage3($id, "");
	}
    //CopyImage($id, "_rectangular");

    if ($download_image_size > 0 || $download_desc_size > 0) {
        $path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/download/";
        if (!is_dir($path)) {
            mkdir($path, 0777);
            chmod($path, 0777);
        }

        $path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/download/" . md5("FORBIZ" . $id) . "/";
        if (!is_dir($path)) {
            mkdir($path, 0777);
            chmod($path, 0777);
        }
    }

    if ($download_image_size > 0) {
        move_uploaded_file($download_image, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/download/" . md5("FORBIZ" . $id) . "/" . $download_image_name);
        $download_img_update = " , download_img = '" . $download_image_name . "' ";
    }

    if ($download_desc_size > 0) {
        move_uploaded_file($download_desc, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/download/" . md5("FORBIZ" . $id) . "/" . $download_desc_name);
        $download_desc_update = " , download_desc = '" . $download_desc_name . "' ";
    }


    if ($chk_deepzoom == 1) {
        if ($id) {
            rmdirr($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/deepzoom/" . $id);
        }

        $client = new SoapClient("http://" . $_SERVER["HTTP_HOST"] . "/VESAPI/VESAPIWS.asmx?wsdl=0");
        $params = new stdClass();
        $params->inputPhysicalPathString = $basic_img_src;
        $params->outputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/deepzoom/" . $id;

        $response = $client->TilingWithPhysicalPath($params);
    }


    //아카마이 ftp 파일 업로드
    $akamaiFtpUploadFiles = array();
    if ($goods_desc_copy) {

        $data_text_convert = $basicinfo;
        $data_text_convert = str_replace("\\", "", $data_text_convert);
        $data_text_convert = str_replace("&#39;", '"', $data_text_convert);
        $data_text_convert = str_replace("&quot;", '"', $data_text_convert);
        preg_match_all("|<img .*src=\"(.*)\".*>|U", $data_text_convert, $out, PREG_PATTERN_ORDER);

        $path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/";

        $INSERT_PRODUCT_ID = $id;

        //if(count($out)>2){
        if (substr_count($data_text_convert, "<img") > 0) {
            if (!is_dir($path)) {

                mkdir($path, 0777);
                chmod($path, 0777);
            } else {
                //chmod($path,0777);
            }
        }

        for ($i = 0; $i < count($out); $i++) {
            for ($j = 0; $j < count($out[$i]); $j++) {

                $img = returnImagePath($out[$i][$j]);
                $img = ClearText($img);

                try {
                    if ($img) {

                        if (substr_count($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/") == 0) {// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...

                            if (substr_count($img, "$HTTP_HOST") > 0) {

                                $local_img_path = str_replace("http://$HTTP_HOST", $_SERVER["DOCUMENT_ROOT"], $img);

                                @copy($local_img_path, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img));
                                if (substr_count($img, $admin_config[mall_data_root] . "/images/upfile/") > 0) {
                                    unlink($local_img_path);
                                }

                                $basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
                                if ($watermark_desc) {
                                    $handle_img_src = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img);
                                    $target_directory = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/";
                                    WaterMarkPrint2($handle_img_src, $target_directory);
                                }

                                $akamaiFtpUploadFiles[] = returnFileName($img);
                            } else {

                                if (substr_count($img, $DOCUMENT_ROOT)) {
                                    //$img = $DOCUMENT_ROOT.$img;\

                                    if (@copy($img, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($DOCUMENT_ROOT . $img))) {
                                        $basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
                                    }
                                } else {

                                    if (@copy($_SERVER["DOCUMENT_ROOT"] . $img, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img))) {

                                        $basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
                                    }
                                }

                                if ($watermark_desc) {
                                    $handle_img_src = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"]["mall_data_root"] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img);
                                    $target_directory = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"]["mall_data_root"] . "/images/product" . $uploaddir . "/product_detail/";
                                    WaterMarkPrint2($handle_img_src, $target_directory);
                                }

                                $akamaiFtpUploadFiles[] = returnFileName($img);
                            }
                        } else {

                            if ($watermark_desc) {

                                $handle_img_src = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"]["mall_data_root"] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img);
                                $target_directory = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"]["mall_data_root"] . "/images/product" . $uploaddir . "/product_detail/";
                                //echo $target_directory."<br>";
                                WaterMarkPrint2($handle_img_src, $target_directory);
                            }

                            $akamaiFtpUploadFiles[] = returnFileName($img);
                        }
                    }

                } catch (Exception $e) {
                    // 에러처리 구문
                    //exit($e->getMessage());
                }
            }
        }
        $basicinfo = str_replace("http://$HTTP_HOST", "", $basicinfo);
    }

    if ($m_goods_desc_copy) {        //모바일상세정보 2014-04-15 이학봉

        $data_text_convert = $m_basicinfo;
        $data_text_convert = str_replace("\\", "", $data_text_convert);
        $data_text_convert = str_replace("&#39;", '"', $data_text_convert);
        $data_text_convert = str_replace("&quot;", '"', $data_text_convert);
        preg_match_all("|<img .*src=\"(.*)\".*>|U", $data_text_convert, $out, PREG_PATTERN_ORDER);

        $path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/";

        $INSERT_PRODUCT_ID = $id;

        //if(count($out)>2){
        if (substr_count($data_text_convert, "<img") > 0) {
            if (!is_dir($path)) {

                mkdir($path, 0777);
                chmod($path, 0777);
            } else {
                //chmod($path,0777);
            }
        }

        for ($i = 0; $i < count($out); $i++) {
            for ($j = 0; $j < count($out[$i]); $j++) {

                $img = returnImagePath($out[$i][$j]);
                $img = ClearText($img);

                try {
                    if ($img) {
                        if (substr_count($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/") == 0) {// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
                            if (substr_count($img, "$HTTP_HOST") > 0) {

                                $local_img_path = str_replace("http://$HTTP_HOST", $_SERVER["DOCUMENT_ROOT"], $img);

                                @copy($local_img_path, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img));
                                if (substr_count($img, $admin_config[mall_data_root] . "/images/upfile/") > 0) {
                                    unlink($local_img_path);
                                }

                                $m_basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $m_basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
                                if ($watermark_desc) {
                                    $handle_img_src = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img);
                                    $target_directory = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/";
                                    WaterMarkPrint2($handle_img_src, $target_directory);
                                }

                                $akamaiFtpUploadFiles[] = returnFileName($img);
                            } else {

                                if (substr_count($img, $DOCUMENT_ROOT)) {
                                    //$img = $DOCUMENT_ROOT.$img;\

                                    if (@copy($img, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($DOCUMENT_ROOT . $img))) {
                                        $m_basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $m_basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
                                    }
                                } else {

                                    if (@copy($_SERVER["DOCUMENT_ROOT"] . $img, $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img))) {

                                        $m_basicinfo = str_replace($img, $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img), $m_basicinfo);     // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
                                    }
                                }

                                if ($watermark_desc) {
                                    $handle_img_src = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"]["mall_data_root"] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img);
                                    $target_directory = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"]["mall_data_root"] . "/images/product" . $uploaddir . "/product_detail/";
                                    WaterMarkPrint2($handle_img_src, $target_directory);
                                }

                                $akamaiFtpUploadFiles[] = returnFileName($img);

                            }
                        } else {

                            if ($watermark_desc) {

                                $handle_img_src = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"]["mall_data_root"] . "/images/product" . $uploaddir . "/product_detail/" . returnFileName($img);
                                $target_directory = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"]["mall_data_root"] . "/images/product" . $uploaddir . "/product_detail/";
                                //echo $target_directory."<br>";
                                WaterMarkPrint2($handle_img_src, $target_directory);
                            }

                            $akamaiFtpUploadFiles[] = returnFileName($img);
                        }
                    }
                } catch (Exception $e) {
                    // 에러처리 구문
                    //exit($e->getMessage());
                }
            }
        }
        $m_basicinfo = str_replace("http://$HTTP_HOST", "", $m_basicinfo);
    }

    akamaiFtpUpload($admin_config[mall_data_root] . "/images/product" . $uploaddir . "/product_detail", $akamaiFtpUploadFiles);

    if ($admininfo[admin_level] == 8) {
        //$state = "6"; // 입점업체 변경시 승인신청중으로 변경 되는 부분
    }

    // account_type 정산방식 1:수수료 2:매입(공급가로 정산) 3:미정산
    //wholesale_commission 도매수수료
    //추가 합니다. 2013-10-29 이학봉

    if ($admininfo[admin_level] == 9) {
        $commision_str = ",etc2='$etc2',one_commission='$one_commission',commission='$commission' ,  reserve='$reserve',reserve_rate='$rate1',
					 state='$state', disp='$disp', account_type= '$account_type', wholesale_commission= '$wholesale_commission'  ";
        //$disp_str = " ,disp='$disp'";

        if ($admin != "") {
            $company_id = $admin;
        } else if ($company_id != "") {
            $company_id = $company_id;
        }

    } else {
        if ($state != "") {
            $commision_str = ", state='$state' ";
        }
    }

    if ($b_ix != "") {    //브랜드
        $brand = $b_ix;
    }

    if (!$brand_name) {
        $sql = "select brand_name from shop_brand where b_ix = '$brand'";
        $db->query($sql);
        $db->fetch();
        $brand_name = $db->dt[brand_name];
        $brand_name = str_replace("'", "\\'", $brand_name);
    }

    if ($product_type == "11") {
        $startdate = $FromYY . "-" . $FromMM . "-" . $FromDD . " " . $FromHH . ":" . $FromII . ":00";
        $enddate = $ToYY . "-" . $ToMM . "-" . $ToDD . " " . $ToHH . ":" . $ToII . ":00";
        $sql = "select ix from shop_product_auction where pid = '$id' ";
        $db->query($sql);
        if ($db->total) {
            $db->query("update shop_product_auction set startdate='$startdate',enddate='$enddate',plusdate='$enddate',startprice='$startprice',plus_count='$plus_count' where pid = '$id' ");
        } else {
            $db->query("insert into shop_product_auction (ix, pid,startdate, enddate, plusdate, startprice, plus_count, plus_use_count, regdate) values ('','$id','$startdate','$enddate','$enddate','$startprice','$plus_count',0,NOW())");
        }
    } else if ($product_type == "7") {//스페셜카테고리 자동차 상품일때
        $sql = "select pid from shop_product_car where pid = '$id'";
        $db->query($sql);
        if ($db->total) {
            $vintage = $vintage_year . "-" . $vintage_month;
            $sql = "update shop_product_car set
					mf_ix='$mf_ix',md_ix='$md_ix',gr_ix='$gr_ix',vt_ix='$vt_ix',
					vintage='$vintage',mileage='$mileage',displacement='$displacement',
					transmission='$transmission',color='$color',fuel='$fuel',
					license_plate='$license_plate',car_condition='$car_condition'
					where pid='$id' ";

            $db->query($sql);
        } else {
            $vintage = $vintage_year . "-" . $vintage_month;
            $sql = "insert into shop_product_car
					(pid,vechile_div,mf_ix,md_ix,gr_ix,vt_ix,vintage,mileage,displacement,transmission,color,fuel,license_plate,car_condition,regdate)
					values
					('$id','$vechile_div','$mf_ix','$md_ix','$gr_ix','$vt_ix','$vintage','$mileage','$displacement','$transmission','$color','$fuel','$license_plate','$car_condition',NOW())";

            $db->query($sql);
        }
    } else if ($product_type == "8") {
        $sql = "select pid from shop_product_property where pid = '$id'";
        $db->query($sql);
        if ($db->total) {
            $sql = "update shop_product_property set
					rg_ix='$rg_ix',dimensions='$dimensions',deal_type='$deal_type',property_type='$property_type',loans='$loans',maintenance_cost='$maintenance_cost',
					heating_fuel='$heating_fuel',posisbile_date='$posisbile_date'
					where pid='$id' ";

            $db->query($sql);
        } else {
            $sql = "insert into shop_product_property
					(pid,rg_ix,dimensions,deal_type,property_type,loans,maintenance_cost,heating_fuel,posisbile_date,regdate)
					values
					('$id','$rg_ix','$dimensions','$deal_type','$property_type','$loans','$maintenance_cost','$heating_fuel','$posisbile_date',NOW())";

            $db->query($sql);

        }
    } else if ($product_type == "9") {
        $sql = "select pid from shop_product_hotel where pid = '$id'";
        $db->query($sql);
        if ($db->total) {
            $sql = "update shop_product_hotel set
					rg_ix='$hotel_rg_ix',hotel_level='$hotel_level',room_level='$room_level'
					where pid='$id' ";

            $db->query($sql);
        } else {
            $sql = "insert into shop_product_hotel
					(pid,rg_ix,hotel_level,room_level,regdate)
					values
					('$id','$hotel_rg_ix','$hotel_level','$room_level',NOW()) ";

            $db->query($sql);

        }
    } else if ($product_type == "10") {
        $sql = "select pid from shop_product_sightseeing where pid = '$id'";
        $db->query($sql);
        if ($db->total) {
            $sql = "update shop_product_sightseeing set
					rg_ix='$sightseeing_rg_ix'
					where pid='$id' ";

            $db->query($sql);
        } else {
            $sql = "insert into shop_product_sightseeing
					(pid,rg_ix,regdate)
					values
					('$id','$sightseeing_rg_ix',NOW()) ";

            $db->query($sql);
        }
    }

    if ($admininfo[admin_level] == 9) {

        for ($i = 0; $i < count($icon_check); $i++) {
            if ($i < count($icon_check) - 1) {
                $icons .= $icon_check[$i] . ";";
            } else {
                $icons .= $icon_check[$i];
            }
        }
        $icons_str = ", icons='$icons' ";
    }
    $sns_btn = serialize($sns_btn);//

    if ($supply_company != "") {
        $supply_company_str = ", supply_company='$supply_company' ";
    }

    if ($inventory_info != "") {
        $inventory_info_str = ", inventory_info='$inventory_info' ";
    }

    if ($co_company_id != "") {
        $co_company_id_str = ", co_company_id='$co_company_id' ";
    }

    if ($delivery_policy == "") {
        $delivery_policy = "1";
    }

    if ($rate_type == "") {
        $rate_type = "N";
    }

    if ($mandatory_type_1 != "") {
        $mandatory_type = $mandatory_type_1 . "|" . $mandatory_type_2;    // 상품고시 제대로 substr 되지 않아서 | 구분값으로 분리 시킴 2013-06-05 이학봉
    } else {
        $mandatory_type = "";
    }
    if ($mandatory_type_1_global != "") {
        $mandatory_type_global = $mandatory_type_1_global . "|" . $mandatory_type_2_global;    // 상품고시 제대로 substr 되지 않아서 | 구분값으로 분리 시킴 2013-06-05 이학봉
    } else {
        $mandatory_type_global = "";
    }

    if ($product_type == "99") $coupon_use_yn = "N";//세트 상품은 쿠폰 사용 못하도록 고정. goods_input.php 에서 사용안함으로 체크하나 disabled 속성으로 처리하기에 값이 넘어오지 않음. N 으로 고정시킴 kbk 13/07/17
    if (empty($coupon_use_yn)) $coupon_use_yn = "Y";
    if (empty($delivery_coupon_yn)) $delivery_coupon_yn = "Y";

    $delivery_company = "MI"; // 2013년 08월 09일 신훈식 현재사용하지 않아 고정값으로 픽스

    if ($md_one_commission == "Y") {
        $update_add = ",
				md_discount_name = '$md_discount_name',
				md_sell_date_use = '$md_sell_date_use',
				md_sell_priod_sdate = '$md_sell_priod_sdate',
				md_sell_priod_edate = '$md_sell_priod_edate',
				whole_head_company_sale_rate = '$whole_head_company_sale_rate',
				whole_seller_company_sale_rate = '$whole_seller_company_sale_rate',
				head_company_sale_rate = '$head_company_sale_rate',
				seller_company_sale_rate = '$seller_company_sale_rate'";
    }

    /*판매상태가 일시품절일경우 입고예정일과 자동판매중 상태전환일/ 상태변경에 따른 변경사유 시작 2014-02-14 이학봉*/
    if ($state == "0") {
        if ($is_auto_change == "1") {
            $input_date = $input_date . " " . $input_stime . ":" . $input_smin . ":00";
            $auto_change_state = $auto_change_state . " " . $auto_change_stime . ":" . $auto_change_smin . ":00";
            $where_state_div = ", input_date = '" . $input_date . "', is_auto_change = '" . $is_auto_change . "', auto_change_state='" . $auto_change_state . "'";
        } else {
            $input_date = $input_date . " " . $input_stime . ":" . $input_smin . ":00";
            $where_state_div = ", input_date = '" . $input_date . "', is_auto_change = '" . $is_auto_change . "'";
        }
    } else if ($state == "2" || $state == "8" || $state == "9" || $state == "7") {

        $sql = "insert into shop_product_state_history set
					pid = '" . $id . "',
					state = '" . $state . "',
					state_div = '" . $state_div . "',
					state_msg = '" . $state_msg . "',
					charger = '" . $admininfo[charger] . "',
					charger_ix = '" . $admininfo[charger_ix] . "',
					regdate = NOW()";
        $db->query($sql);
    }
    /*판매상태가 일시품절일경우 입고예정일과 자동판매중 상태전환일/ 상태변경에 따른 변경사유 시작 2014-02-14 이학봉*/

    if ($admininfo[admin_level] == '9') {
        $set_sns = " , sns_btn_yn='$sns_btn_yn',
					 sns_btn='$sns_btn' ";
    }
    $sell_priod_sdate = $sell_priod_sdate . " " . $sell_priod_sdate_h . ":" . $sell_priod_sdate_i . ":" . $sell_priod_sdate_s;
    $sell_priod_edate = $sell_priod_edate . " " . $sell_priod_edate_h . ":" . $sell_priod_edate_i . ":" . $sell_priod_edate_s;

    $global_pinfo_json = "";
    if (count($global_pinfo) > 0) {
        foreach ($global_pinfo as $colum => $li) {
            foreach ($li as $ln => $val) {
                $global_pinfo[$colum][$ln] = urlencode($val);
            }
        }

        $global_pinfo_json = json_encode($global_pinfo);
    }

    if ($admininfo[admin_level] != '9') {    //셀러일경우 웹/모바일상품 구분은 기본으로 전체로 선택함 2014-08-19 이학봉
        $is_mobile_use = 'A';
    }

    if ($is_mobile_use == '') {    //다른경로로 들어온 상품중에 빈값이면 강제로 A로 지정
        $is_mobile_use = 'A';
    }

    $etc1 = trim($etc1);
    $etc2 = trim($etc2);
    $etc3 = trim($etc3);
    $etc4 = trim($etc4);
    $etc5 = trim($etc5);
    $etc6 = trim($etc6);
    $etc7 = trim($etc7);
    $etc8 = trim($etc8);
    $etc9 = trim($etc9);
    $etc10 = trim($etc10);

    if (count($category_add_infomations) > 0) {
        foreach ($category_add_infomations as $colum => $li) {
            foreach ($li as $ln => $val) {
                if (is_array($val)) {
                    foreach ($val as $key => $value) {
                        //echo $colum.":::".$ln."<br>";
                        $_category_add_infomations[$colum][$ln][] = urlencode($value);
                    }
                } else {
                    $_category_add_infomations[$colum][$ln] = urlencode($val);
                }
            }
        }
//print_r($_category_add_infomations);
//exit;
        $category_add_infomations_json = json_encode($_category_add_infomations);
    }

    if ($soho == "") {
        $soho = "0";
    }
    if ($designer == "") {
        $designer = "0";
    }
    if ($mirrorpick == "") {
        $mirrorpick = "0";
    }

	if($laundry_cid == ""){
		$laundry_cid = substr($laundry_one_depth,0,3)."".substr($laundry_two_depth,3,3);
	}

    $sql = "UPDATE " . TBL_SHOP_PRODUCT . " SET
			mall_ix='$mall_ix', 
			pcode='$pcode', 
			pname='" . trim($pname) . "',
			preface='" . trim($preface) . "',
			b_preface='" . $b_preface . "',
			i_preface='" . $i_preface . "',
			u_preface='" . $u_preface . "',
			c_preface='" . trim($c_preface) . "',
			listNum='" . $listNum . "',
			overNum='" . $overNum . "',
			slistNum='" . $slistNum . "',
			nailNum='" . $nailNum . "',
			pattNum='" . $pattNum . "',
			
			marker_left_dn='" . $marker_left_dn . "',
			marker_right_dn='" . $marker_right_dn . "',

			product_color_chip='" . $product_color_chip . "',
			" . (!empty($global_pinfo_json) ? "global_pinfo='" . $global_pinfo_json . "'," : "") . "
			brand = '$brand',
			laundry_cid = '$laundry_cid',
			style = '$style',	
			soho = '$soho',	
			designer = '$designer',	
			mirrorpick = '$mirrorpick',	
			category_add_infos = '$category_add_infomations_json',
			brand_name = '$brand_name',
			company='$company',
			c_ix = '$c_ix',
			buying_company='$buying_company',
			paper_pname='$paper_pname',
			shotinfo='$shotinfo',
			buyingservice_coprice='$buyingservice_coprice',
			wholesale_price='$wholesale_price',
			wholesale_sellprice='$wholesale_sellprice',
			listprice='$listprice',
			sellprice='$sellprice',
			premiumprice='$premiumprice',
			coprice='$coprice',
			cupon_use_yn='$cupon_use_yn',
			bimg='$bimg_text',
			mandatory_type='$mandatory_type',
			mandatory_type_global='$mandatory_type_global',
			relation_product_cnt='$relation_product_cnt',
			relation_text1='$relation_text1',
			relation_text2 = '$relation_text2',
			relation_display_type='$relation_display_type',
			movie='$movie',
			movie_thumbnail = '$movie_thumbnail',
			movie_now = '$movie_now',
			stock='$stock',
			md_code='$md_code',
			barcode='$barcode',
			trade_admin='$trade_admin',
			delivery_coupon_yn='$delivery_coupon_yn',
			coupon_use_yn='$coupon_use_yn',
			is_pos_link ='N',
			add_status = 'U',
			delivery_type='$delivery_type',
			wholesale_yn = '$wholesale_yn',
			offline_yn = '$offline_yn',
			safestock='$safestock',
			available_stock='$available_stock',
			search_keyword = '$search_keyword',
			add_info = '$add_info',
			editdate = NOW(),
			wholesale_reserve_yn='$wholesale_reserve_yn',
			wholesale_reserve='$wholesale_reserve',
			wholesale_reserve_rate='$wholesale_rate1',
			wholesale_rate_type='$wholesale_rate_type',
			reserve_yn='$reserve_yn',
			reserve='$reserve',
			reserve_rate='$rate1',
			rate_type='$rate_type',
			gift_sprice = '$gift_sprice',
			gift_eprice = '$gift_eprice',
			gift_qty = '$gift_qty',
			
			surtax_yorn='$surtax_yorn' " . $supply_company_str . "  " . $inventory_info_str . " " . $icons_str . "
			,delivery_company='$delivery_company',
			product_type = '$product_type',free_delivery_yn='$free_delivery_yn',free_delivery_count='$free_delivery_count',
			sell_priod_sdate='$sell_priod_sdate',sell_priod_edate='$sell_priod_edate',allow_order_type='$allow_order_type',
			allow_order_cnt_byonesell='$allow_order_cnt_byonesell',allow_order_cnt_byoneperson='$allow_order_cnt_byoneperson',allow_order_minimum_cnt='$allow_order_minimum_cnt',
			origin='$origin',make_date='$make_date',expiry_date='$expiry_date',substitude_yn ='$substitude_yn',substitude_total ='$substitude_total',substitude_seller ='$substitude_seller',substitude_rate='$substitude_rate',
			bs_goods_url = '$bs_goods_url', bs_site = '$bs_site',price_policy = '$price_policy', stock_use_yn = '$stock_use_yn',delivery_policy='$delivery_policy',
			delivery_package='$delivery_package',

			auto_sync_wms='$auto_sync_wms',
			currency_ix='$currency_ix',
			hotcon_event_id='$hotcon_event_id', 
			hotcon_pcode='$hotcon_pcode',
			basicinfo='$basicinfo',
			m_basicinfo='$m_basicinfo',
			is_adult = '$is_adult',
			remain_stock = '$remain_stock',
			is_sell_date = '$is_sell_date',
			wholesale_allow_max_cnt = '$wholesale_allow_max_cnt',
			allow_max_cnt = '$allow_max_cnt',
			wholesale_allow_basic_cnt = '$wholesale_allow_basic_cnt',
			allow_basic_cnt = '$allow_basic_cnt',
			product_weight = '$product_weight',
			md_one_commission = '$md_one_commission',
			allow_byoneperson_cnt = '$allow_byoneperson_cnt',
			wholesale_allow_byoneperson_cnt = '$wholesale_allow_byoneperson_cnt',
			is_mobile_use = '$is_mobile_use',
			etc1 = '$etc1',
			etc2 = '$etc2',
			etc3 = '$etc3',
			etc4 = '$etc4',
			etc5 = '$etc5',
			etc6 = '$etc6',
			etc7 = '$etc7',
			etc8 = '$etc8',
			etc9 = '$etc9',
			etc10 = '$etc10',
			admin='$company_id',
			exchangeable_yn = '$exchangeable_yn',
			returnable_yn = '$returnable_yn',
			admin_memo = '$admin_memo',
			gift_selectbox_cnt='$gift_selectbox_cnt',
			gift_selectbox_nooption_yn='$gift_selectbox_nooption_yn',
			wear_info='$wear_info',
			mandatory_use='$mandatory_use',
			mandatory_use_global='$mandatory_use_global'
			" . $where_state_div . "
			" . $update_add . "
			" . $commision_str . " " . $download_img_update . " " . $download_desc_update . "
			" . $supply_company_str . " " . $inventory_info_str . " " . $co_company_id_str . " " . $set_sns . "
			Where id = '$id' ";


    $db->query($sql);

    //글로벌 데이터 처리
    //array('pname', 'preface', 'add_info', 'search_keyword', 'basicinfo', 'm_basicinfo', 'coprice', 'listprice', 'sellprice');
    $english_coprice = getExchangeNationPrice($coprice);
    $english_listprice = getExchangeNationPrice($listprice);
    $english_sellprice = getExchangeNationPrice($sellprice);

    $english_pname = str_replace("'", "&#39;", $english_pname);
    $english_preface = str_replace("'", "&#39;", $english_preface);

    $sql = "UPDATE shop_product_global SET
			mall_ix='$mall_ix', 
			pcode='$pcode', 
			pname='" . strip_tags(trim($english_pname)) . "',
			preface='" . strip_tags(trim($english_preface)) . "',
			listNum='" . $listNum . "',
			overNum='" . $overNum . "',
			slistNum='" . $slistNum . "',
			nailNum='" . $nailNum . "',
			pattNum='" . $pattNum . "',
			product_color_chip='" . $product_color_chip . "',
			" . (!empty($global_pinfo_json) ? "global_pinfo='" . $global_pinfo_json . "'," : "") . "
			brand = '$brand',
			laundry_cid = '$laundry_cid',
			style = '$style',	
			soho = '$soho',	
			designer = '$designer',	
			mirrorpick = '$mirrorpick',	
			category_add_infos = '$category_add_infomations_json',
			brand_name = '$brand_name',
			company='$company',
			c_ix = '$c_ix',
			buying_company='$buying_company',
			paper_pname='$paper_pname',
			shotinfo='$shotinfo',
			buyingservice_coprice='$buyingservice_coprice',
			wholesale_price='$wholesale_price',
			wholesale_sellprice='$wholesale_sellprice',
			listprice='$english_listprice',
			sellprice='$english_sellprice',
			premiumprice='$premiumprice',
			coprice='$english_coprice',
			cupon_use_yn='$cupon_use_yn',
			bimg='$bimg_text',
			mandatory_type='$mandatory_type',
			mandatory_type_global='$mandatory_type_global',
			relation_product_cnt='$relation_product_cnt',
			relation_text1='$english_relation_text1',
			relation_text2 = '$english_relation_text2',
			relation_display_type='$relation_display_type',
			movie='$movie',
			movie_thumbnail = '$movie_thumbnail',
			movie_now = '$movie_now',
			stock='$stock',
			md_code='$md_code',
			barcode='$barcode',
			trade_admin='$trade_admin',
			delivery_coupon_yn='$delivery_coupon_yn',
			coupon_use_yn='$coupon_use_yn',
			is_pos_link ='N',
			add_status = 'U',
			delivery_type='$delivery_type',
			wholesale_yn = '$wholesale_yn',
			offline_yn = '$offline_yn',
			safestock='$safestock',
			available_stock='$available_stock',
			search_keyword = '$english_search_keyword',
			add_info = '$english_add_info',
			editdate = NOW(),
			wholesale_reserve_yn='$wholesale_reserve_yn',
			wholesale_reserve='$wholesale_reserve',
			wholesale_reserve_rate='$wholesale_rate1',
			wholesale_rate_type='$wholesale_rate_type',
			reserve_yn='$reserve_yn',
			reserve='$reserve',
			reserve_rate='$rate1',
			rate_type='$rate_type',
			gift_sprice = '$gift_sprice',
			gift_eprice = '$gift_eprice',
			gift_qty = '$gift_qty',
			
			surtax_yorn='$surtax_yorn' " . $supply_company_str . "  " . $inventory_info_str . " " . $icons_str . "
			,delivery_company='$delivery_company',
			product_type = '$product_type',free_delivery_yn='$free_delivery_yn',free_delivery_count='$free_delivery_count',
			sell_priod_sdate='$sell_priod_sdate',sell_priod_edate='$sell_priod_edate',allow_order_type='$allow_order_type',
			allow_order_cnt_byonesell='$allow_order_cnt_byonesell',allow_order_cnt_byoneperson='$allow_order_cnt_byoneperson',allow_order_minimum_cnt='$allow_order_minimum_cnt',
			origin='$origin',make_date='$make_date',expiry_date='$expiry_date',substitude_yn ='$substitude_yn',substitude_total ='$substitude_total',substitude_seller ='$substitude_seller',substitude_rate='$substitude_rate',
			bs_goods_url = '$bs_goods_url', bs_site = '$bs_site',price_policy = '$price_policy', stock_use_yn = '$stock_use_yn',delivery_policy='$delivery_policy',
			delivery_package='$delivery_package',

			auto_sync_wms='$auto_sync_wms',
			currency_ix='$currency_ix',
			hotcon_event_id='$hotcon_event_id', 
			hotcon_pcode='$hotcon_pcode',
			basicinfo='$english_basicinfo',
			m_basicinfo='$english_m_basicinfo',
			is_adult = '$is_adult',
			remain_stock = '$remain_stock',
			is_sell_date = '$is_sell_date',
			wholesale_allow_max_cnt = '$wholesale_allow_max_cnt',
			allow_max_cnt = '$allow_max_cnt',
			wholesale_allow_basic_cnt = '$wholesale_allow_basic_cnt',
			allow_basic_cnt = '$allow_basic_cnt',
			product_weight = '$product_weight',
			md_one_commission = '$md_one_commission',
			allow_byoneperson_cnt = '$allow_byoneperson_cnt',
			wholesale_allow_byoneperson_cnt = '$wholesale_allow_byoneperson_cnt',
			is_mobile_use = '$is_mobile_use',
			etc1 = '$etc1',
			etc2 = '$etc2',
			etc3 = '$etc3',
			etc4 = '$etc4',
			etc5 = '$etc5',
			etc6 = '$etc6',
			etc7 = '$etc7',
			etc8 = '$etc8',
			etc9 = '$etc9',
			etc10 = '$etc10',
			admin='$company_id',
			exchangeable_yn = '$exchangeable_yn',
			returnable_yn = '$returnable_yn',
			admin_memo = '$admin_memo',
			gift_selectbox_cnt='$gift_selectbox_cnt',
			mandatory_use='$mandatory_use',
			mandatory_use_global='$mandatory_use_global'
			" . $where_state_div . "
			" . $update_add . "
			" . $commision_str . " " . $download_img_update . " " . $download_desc_update . "
			" . $supply_company_str . " " . $inventory_info_str . " " . $co_company_id_str . " " . $set_sns . "
			Where id = '$id' ";

    $db->query($sql);

    if ($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S') {
        $sfb = new sphinxfb(); // mysql 데이터베이스
        $sfb->rebuild_index(" id ='" . $id . "' ");
    }
    /*
	$sphinx_db = new Database("127.0.0.1:9306:mysql41", "","","","9306");

	$sphinx_sql = "replace into goods_rt (id, pname  ,brand_name ,shotinfo ,search_keyword ,shop_name  ,product_type ,brand  ,disp ,state  ,vieworder  ,vieworder_by_state ,order_cnt  ,view_cnt ,recommend_cnt  ,cid_ix ,cid_1d ,cid_2d ,cid_3d ,cid_4d ,uf_valuation ,after_cnt  ,stock  ,coprice  ,wholesale_listprice  ,wholesale_sellprice  ,listprice  ,sellprice  ,premiumprice ,reserve  ,reserve_rate ,regdate , editdate ,sell_priod_sdate ,sell_priod_edate ,pid  ,pcode ,company_id ,stock_use_yn ,cid  ,is_sell_date ,global_pinfo) values ('$id','$pname','$brand_name','$shotinfo','$search_keyword','$shop_name','$product_type','$brand','$disp','$state','$vieworder','$vieworder_by_state','$order_cnt','$view_cnt','$recommend_cnt','$cid_ix','$cid_1d','$cid_2d','$cid_3d','$cid_4d','$uf_valuation','$after_cnt','$stock','$coprice','$wholesale_listprice','$wholesale_sellprice','$listprice','$sellprice','$premiumprice','$reserve','$reserve_rate','$regdate','$sell_priod_sdate','$sell_priod_edate','$pid','$pcode','$company_id','$stock_use_yn','$cid','$is_sell_date','$global_pinfo')";

	//$sphinx_sql = "replace into goods_rt (id, pname ,brand_name ,shotinfo ,search_keyword ,shop_name ,product_type ,brand ,disp ,state ,vieworder ,vieworder_by_state ,order_cnt ,view_cnt ,recommend_cnt ,cid_ix ,cid_1d ,cid_2d ,cid_3d ,cid_4d ,uf_valuation ,after_cnt ,stock ,coprice ,wholesale_listprice ,wholesale_sellprice ,listprice ,sellprice ,premiumprice ,reserve ,reserve_rate ,regdate ,sell_priod_sdate ,sell_priod_edate ,pid ,pcode ,company_id ,stock_use_yn ,cid ,is_sell_date ,global_pinfo) values ('0000000271','2222','','','','(주)라이프스타일','0','0','1','1','270','270','0','19','','0000000028','002','000','000','000','0','','0','6','5','4','3','2','1','0','0','1464848781','','','','','362ed8ee1cba4cc34f80aa5529d2fbcd','N','002000000000000','0','null') ";
	//echo($sphinx_sql);
	//exit;
	//$sphinx_sql = "select * from goods_rt";
	$sphinx_db->query($sphinx_sql);
	$sphinx_db->close();
	*/
//	exit;
//배송템플릿 저장 시작 2014-05-13 이학봉
    if ($delivery_type == '1') {    //통합배송일경우 본사 정책
        $sql = "select company_id from common_company_detail where com_type = 'A' limit 0,1";
        $db->query($sql);
        $db->fetch();
        $pd_company_id = $db->dt[company_id];
    } else {
        $pd_company_id = $company_id;
    }
    Insert_product_delivery($dt_ix, $pd_company_id, $id, $delivery_policy);
//배송템플릿 저장 끝 2014-05-13 이학봉

    input_filter_info($product_filter, $id, 'update');

//상품기본 배송비 추가 2014-07-31 이학봉 (네이버 , 다음 연동시 기본배송비 노출)
    $delivery_basic_policy = PorudctDeliveryPayMethod($id);
    $product_basic_delivery_price = PorudctBasicDeliveryPrice($id);    //상품기본 배송비 구하기
    $sql = "update shop_product set delivery_price = '" . $product_basic_delivery_price . "', delivery_product_policy = '" . $delivery_basic_policy . "' where id = '" . $id . "'";
    $db->query($sql);
//상품기본 배송비 추가 2014-07-31 이학봉

//복수구매할인 추가 2014-01-27 이학봉
    if (is_array($wholesale_rate)) {

        $sql = "delete from shop_product_mult_rate where pid = '" . $id . "' ";
        $db->query($sql);

        foreach ($wholesale_rate as $i => $values) {
            //for($i=0;$i<count($wholesale_rate);$i++){
            foreach ($values as $key => $value) {

                if ($key == "whole") {    //is_wholesale = W	도매
                    $is_wholesale = "W";
                } else if ($key == "retail") {
                    $is_wholesale = "R";
                }

                if ($value[is_use] == "") {
                    $is_use = "2";
                } else {
                    $is_use = $value[is_use];
                }

                if ($value[sell_mult_cnt] == "" && $value[rate_price] == "") {
                    continue;
                }

                if ($value[is_use]) {
                    $sql = "insert into shop_product_mult_rate set
								pid = '" . $id . "',
								is_wholesale = '" . $is_wholesale . "',
								is_use = '" . $is_use . "',
								sell_mult_cnt = '" . $value[sell_mult_cnt] . "',
								rate_div = '" . $value[rate_div] . "',
								rate_price = '" . $value[rate_price] . "',
								round_cnt = '" . $value[round_cnt] . "',
								round_type = '" . $value[round_type] . "',
								regdate = NOW()";
                    $db->query($sql);
                }
                /*
				if($value[mr_id]){

					if($value[is_use]){
						$sql = "update shop_product_mult_rate set
									is_use = '".$is_use."',
									sell_mult_cnt = '".$value[sell_mult_cnt]."',
									rate_div = '".$value[rate_div]."',
									rate_price = '".$value[rate_price]."',
									round_cnt = '".$value[round_cnt]."',
									round_type = '".$value[round_type]."',
									regdate = NOW()
								where
									pid = '".$id."'
									and mr_id = '".$value[mr_id]."'";
						$db->query($sql);
					}else{
						$sql = "delete from shop_product_mult_rate where mr_id = '".$value[mr_id]."'";
						$db->query($sql);
					}

				}else{
					if($value[sell_mult_cnt] == "" && $value[rate_price] == ""){
						continue;
					}
					$sql = "insert into shop_product_mult_rate set
								pid = '".$id."',
								is_wholesale = '".$is_wholesale."',
								is_use = '".$is_use."',
								sell_mult_cnt = '".$value[sell_mult_cnt]."',
								rate_div = '".$value[rate_div]."',
								rate_price = '".$value[rate_price]."',
								round_cnt = '".$value[round_cnt]."',
								round_type = '".$value[round_type]."',
								regdate = NOW()";
					$db->query($sql);
				}
*/
            }
        }
    }

//복수구매할인 추가 2014-01-27 이학봉

    if (in_array($product_type, $sns_product_type)) {
        $querys = array();
        $spei_sDateYMD = preg_replace("[^0-9]", "", $spei_sDateYMD);    // 진행 시작일
        $spei_eDateYMD = preg_replace("[^0-9]", "", $spei_eDateYMD);    // 진행 종료일
        $querys[] = 'spei_couponInfo = "' . $spei_couponInfo . '"';            // 쿠폰 관리번호
        $querys[] = 'spei_couponSDate = "' . mktime(0, 0, 0, substr($spei_couponSDate, 4, 2), substr($spei_couponSDate, 6, 2), substr($spei_couponSDate, 0, 4)) . '"';            // 유효기간 시작일
        $querys[] = 'spei_couponEDate = "' . mktime(0, 0, 0, substr($spei_couponEDate, 4, 2), substr($spei_couponEDate, 6, 2), substr($spei_couponEDate, 0, 4)) . '"';            // 유효기간 종료일
        $querys[] = 'spei_sDate = "' . mktime($spei_sDateH, $spei_sDateM, 0, substr($spei_sDateYMD, 4, 2), substr($spei_sDateYMD, 6, 2), substr($spei_sDateYMD, 0, 4)) . '"';    // 진행시작일
        $querys[] = 'spei_eDate = "' . mktime($spei_eDateH, $spei_eDateM, 0, substr($spei_eDateYMD, 4, 2), substr($spei_eDateYMD, 6, 2), substr($spei_eDateYMD, 0, 4)) . '"';    // 진행종료일
        $querys[] = 'spei_dispDate = "' . $spei_dispDate . '"';                    // 남은시간 노출여부
        $querys[] = 'spei_dispStock = "' . $spei_dispStock . '"';                // 재고량 노출여부
        $querys[] = 'spei_discountRate = "' . $spei_discountRate . '"';            // 할인율(%)
        $querys[] = 'spei_dispDiscountRate = "' . $spei_dispDiscountRate . '"';    // 할인율 노출여부
        $querys[] = 'spei_dispathPoint = "' . $spei_dispathPoint . '"';            // 발송시점
        $querys[] = 'spei_targetNumber = "' . $spei_targetNumber . '"';            // 구매달성인원
        $querys[] = 'spei_addSaleCount = "' . $spei_addSaleCount . '"';            // 판매수량 노출설정
        $querys[] = 'spei_addSaleMaxCount = "' . $spei_addSaleMaxCount . '"';            // 최대판매수량 설정
        $querys[] = 'spei_buyLimitMin = "' . $spei_buyLimitMin . '"';            // 최소구매수량
        $querys[] = 'spei_buyLimitMax = "' . $spei_buyLimitMax . '"';            // 최대구매수량
        $querys[] = 'spei_smsMessage = "' . $spei_smsMessage . '"';                // SMS 메세지 내용
        $querys[] = 'min_local_type = "' . $min_local_type . '"';                // SMS 메세지 내용

        $db->query('UPDATE ' . TBL_SNS_PRODUCT_ETCINFO . ' SET ' . implode(',', $querys) . ' WHERE pid = ' . $id);

        foreach ($subsciptions as $subsciption) {
            if ($subsciption["due_date"]) {
                $sql = "select pss_ix from shop_product_subs_senddetail where pid = '$id' and pss_ix = '" . $subsciption["pss_ix"] . "' ";
                $db->query($sql);
                $db->fetch();
                if (!$db->total) {

                    $sql = "insert into shop_product_subs_senddetail (pss_ix,pid,due_date,add_sale_rate,insert_yn,regdate) 
								values
								('" . $subsciption[pss_ix] . "','" . $id . "','" . $subsciption[due_date] . "','" . $subsciption[add_sale_rate] . "','" . $subsciption[insert_yn] . "',NOW())";
                    $db->query($sql);
                } else {
                    $sql = "update shop_product_subs_senddetail set							 
							 pid='" . $id . "',
							 due_date='" . $subsciption[due_date] . "',
							 add_sale_rate='" . $subsciption[add_sale_rate] . "',
							 insert_yn='" . $subsciption[insert_yn] . "'
							 where pss_ix='" . $subsciption[pss_ix] . "' 
							";
                    $db->query($sql);
                }
            }
        }

        foreach ($local_deliverys as $local_delivery) {
            if ($local_delivery["sido"]) {
                $sql = "select pld_ix from shop_product_localdelivery_detail where pid = '$id' and pld_ix = '" . $local_delivery["pld_ix"] . "' ";
                $db->query($sql);
                $db->fetch();
                if (!$db->total) {

                    $sql = "insert into shop_product_localdelivery_detail(pld_ix,pid,sido,sigugun,dong, due_date, regdate)			
								values
								('" . $local_delivery[pld_ix] . "','" . $id . "','" . $local_delivery[sido] . "','" . $local_delivery[sigugun] . "','" . $local_delivery[dong] . "','" . $local_delivery[due_date] . "', NOW()) ";
                    //$db->sequences = "SHOP_GOODS_DISPLAYINFO_SEQ";
                    $db->query($sql);
                } else {
                    $sql = "update shop_product_localdelivery_detail set							 
							 pid='" . $id . "',
							 sido='" . $local_delivery[sido] . "',
							 sigugun='" . $local_delivery[sigugun] . "',
							 dong='" . $local_delivery[dong] . "',
							 due_date='" . $local_delivery[due_date] . "'
							 where pld_ix='" . $local_delivery[pld_ix] . "' 
							";
                    $db->query($sql);
                }
            }
        }

        $db->query("update " . TBL_SNS_PRODUCT_RELATION . " set insert_yn = 'N' where pid = '$id'");
        for ($i = 0; $i < count($display_category); $i++) {
            if ($display_category[$i] == $basic) {
                $category_basic = 1;
            } else {
                $category_basic = 0;
            }
            $sql = "select rid from " . TBL_SNS_PRODUCT_RELATION . " where pid = '$id' and cid = '" . $display_category[$i] . "' ";
            $db->query($sql);
            $db->fetch();
            if ($db->total) {
                $db->query("update " . TBL_SNS_PRODUCT_RELATION . " set insert_yn = 'Y' , basic='$category_basic' where rid = '" . $db->dt[rid] . "'");
            } else {
                if (strlen($display_category[$i]) == '15') {    //카테고리 코드가 15자리가 아닐경우 처리 안함 빈값이나 0으로 입력되는 경우가 있음 2014-07-11 이학봉
                    $db->query("insert into " . TBL_SNS_PRODUCT_RELATION . " (rid, cid, pid, disp, basic,insert_yn, regdate ) values ('','" . $display_category[$i] . "','" . $id . "','1','" . $category_basic . "','Y',NOW())");
                }
            }
        }
        $db->query("delete from " . TBL_SNS_PRODUCT_RELATION . " where pid = '$id' and insert_yn = 'N'");
        $db->query("select count(*) as total from " . TBL_SNS_PRODUCT_RELATION . " where pid = '$id' ");
        $db->fetch();

        if ($db->dt[total] > 0) {
            $db->query("update " . TBL_SNS_PRODUCT . " set reg_category = 'Y' where id = '$id' ");
        } else {
            $db->query("update " . TBL_SNS_PRODUCT . " set reg_category = 'N' where id = '$id' ");
        }


    } else {

        if (count($display_standard_category) > 0) {
            //$db->debug = true;
            $db->query("update shop_product_standard_relation set insert_yn = 'N' where pid = '$id'");
            for ($i = 0; $i < count($display_standard_category); $i++) {
                if ($display_standard_category[$i] == $standard_basic) {
                    $category_basic = 1;
                } else {
                    $category_basic = 0;
                }
                $sql = "select psr_ix from shop_product_standard_relation where pid = '$id' and cid = '" . $display_standard_category[$i] . "' ";
                $db->query($sql);
                $db->fetch();
                if ($db->total) {
                    $db->query("update shop_product_standard_relation set insert_yn = 'Y' , basic='$category_basic' where psr_ix = '" . $db->dt[psr_ix] . "'");
                } else {
                    if (strlen($display_standard_category[$i]) == '15') {    //카테고리 코드가 15자리가 아닐경우 처리 안함 빈값이나 0으로 입력되는 경우가 있음 2014-07-11 이학봉
                        $db->sequences = "SHOP_GOODS_LINK_SEQ";
                        $db->query("insert into shop_product_standard_relation (psr_ix, cid, pid, disp, basic,insert_yn, regdate ) values ('','" . $display_standard_category[$i] . "','" . $id . "','1','" . $category_basic . "','Y',NOW())");
                    }
                }
            }
            //$db->debug = false;
            $db->query("delete from shop_product_standard_relation where pid = '$id' and insert_yn = 'N'");
            $db->query("select count(*) as total from shop_product_standard_relation where pid = '$id' ");
            $db->fetch();
            if ($db->dt[total] > 0) {
                $db->query("update " . TBL_SHOP_PRODUCT . " set reg_standard_category = 'Y' where id = '$id' ");
            } else {
                $db->query("update " . TBL_SHOP_PRODUCT . " set reg_standard_category = 'N' where id = '$id' ");
            }
        }

        $db->query("update " . TBL_SHOP_PRODUCT_RELATION . " set insert_yn = 'N' where pid = '$id'");
        for ($i = 0; $i < count($display_category); $i++) {
            if ($display_category[$i] == $basic) {
                $category_basic = 1;
            } else {
                $category_basic = 0;
            }
            $sql = "select rid from " . TBL_SHOP_PRODUCT_RELATION . " where pid = '$id' and cid = '" . $display_category[$i] . "' ";
            $db->query($sql);
            $db->fetch();
            if ($db->total) {
                $db->query("update " . TBL_SHOP_PRODUCT_RELATION . " set insert_yn = 'Y' , basic='$category_basic' where rid = '" . $db->dt[rid] . "'");
            } else {
                if (strlen($display_category[$i]) == '15') {    //카테고리 코드가 15자리가 아닐경우 처리 안함 빈값이나 0으로 입력되는 경우가 있음 2014-07-11 이학봉
                    $db->sequences = "SHOP_GOODS_LINK_SEQ";
                    $db->query("insert into " . TBL_SHOP_PRODUCT_RELATION . " (rid, cid, pid, disp, basic,insert_yn, regdate ) values ('','" . $display_category[$i] . "','" . $id . "','1','" . $category_basic . "','Y',NOW())");
                }
            }
        }
        $db->query("delete from " . TBL_SHOP_PRODUCT_RELATION . " where pid = '$id' and insert_yn = 'N'");
        $db->query("select count(*) as total from " . TBL_SHOP_PRODUCT_RELATION . " where pid = '$id' ");
        $db->fetch();

        if ($db->dt[total] > 0) {
            $db->query("update " . TBL_SHOP_PRODUCT . " set reg_category = 'Y' where id = '$id' ");
        } else {
            $db->query("update " . TBL_SHOP_PRODUCT . " set reg_category = 'N' where id = '$id' ");
        }

        //[S] 미니샵 카테고리 등록 및 수정
        $db->query("update shop_minishop_relation_product set insert_yn = 'N' where pid = '$id' AND company_id = '" . $_SESSION["admininfo"]['company_id'] . "' ");
        for ($i = 0; $i < count($minishop_display_category); $i++) {
            if ($minishop_display_category[$i] == $minishop_basic) {
                $category_basic = 1;
            } else {
                $category_basic = 0;
            }
            $sql = "select rid from shop_minishop_relation_product where pid = '$id' and cid = '" . $minishop_display_category[$i] . "' AND company_id = '" . $_SESSION["admininfo"]['company_id'] . "' ";
            $db->query($sql);
            $db->fetch();
            if ($db->total) {
                $db->query("update shop_minishop_relation_product set insert_yn = 'Y' , basic='$category_basic' where rid = '" . $db->dt[rid] . "' ");
            } else {
                if (strlen($minishop_display_category[$i]) == '15') {    //카테고리 코드가 15자리가 아닐경우 처리 안함 빈값이나 0으로 입력되는 경우가 있음 2014-07-11 이학봉
                    $db->sequences = "SHOP_GOODS_LINK_SEQ";
                    $db->query("insert into shop_minishop_relation_product (company_id, rid, cid, pid, disp, basic,insert_yn, regdate ) values ('" . $_SESSION["admininfo"]['company_id'] . "','','" . $minishop_display_category[$i] . "','" . $id . "','1','" . $category_basic . "','Y',NOW())");
                }
            }
        }
        $db->query("delete from shop_minishop_relation_product where pid = '$id' and insert_yn = 'N' AND company_id = '" . $_SESSION["admininfo"]['company_id'] . "' ");
        //[E] 미니샵 카테고리 등록 및 수정

    }

    //카테고리 정보 수정

    if ($sellprice != $bsellprice || $coprice != $bcoprice || $reserve != $breserve || $listprice != $blistprice || $wholesale_sellprice != $bwholesale_sellprice || $wholesale_price != $bwholesale_price) {
        $sql = "INSERT INTO " . TBL_SHOP_PRICEINFO . " (id, pid, listprice, sellprice, coprice, reserve,  company_id, charger_info,regdate,wholesale_price,wholesale_sellprice) ";
        $sql = $sql . " values('', '$id','$listprice','$sellprice', '$coprice', '$reserve',  '" . $admininfo[company_id] . "','[" . $admininfo[company_name] . "] " . $admininfo[charger] . "(" . $admininfo[charger_id] . ")',NOW(),'" . $wholesale_price . "','" . $wholesale_sellprice . "') ";
        $db->sequences = "SHOP_PRICEINFO_SEQ";
        $db->query($sql);
    }


    if ($orgin_price != $b_orgin_price || $exchange_rate != $b_exchange_rate || $air_wt != $b_air_wt || $air_shipping != $b_air_shipping || $duty != $b_duty || $clearance_fee != $b_clearance_fee || $bs_fee_rate != $b_bs_fee_rate || $bs_fee != $b_bs_fee) {
        $db->query("update shop_product_buyingservice_priceinfo set bs_use_yn = '0' where pid ='" . $id . "'");

        $sql = "insert into shop_product_buyingservice_priceinfo(bsp_ix,pid,orgin_price,exchange_rate,air_wt,air_shipping,duty,clearance_fee,clearance_type, bs_fee_rate,bs_fee,bs_use_yn, regdate)
						values('','$id','$orgin_price','$exchange_rate','$air_wt','$air_shipping','$duty','$clearance_fee','$clearance_type','$bs_fee_rate','$bs_fee','1',NOW()) ";
        $db->sequences = "SHOP_GOODS_BS_PRICEINFO_SEQ";
        $db->query($sql);
    }

    $pid = $id;

    //상품필수고시 - START
    $sql = "update shop_product_mandatory_info set insert_yn='N' where pid = '" . $pid . "' ";
    $db->query($sql);

    if (is_array($mandatory_info)) {
        foreach ($mandatory_info as $m_info) {
            if ($m_info[pmi_ix] != "") {
                if ($m_info[pmi_title] != "" || $m_info[pmi_code] != "") {

                    $sql = "update shop_product_mandatory_info set pmi_code='" . $m_info[pmi_code] . "', pmi_title='" . $m_info[pmi_title] . "',pmi_desc='" . $m_info[pmi_desc] . "',insert_yn='Y' where pmi_ix = '" . $m_info[pmi_ix] . "' ";
                    $db->query($sql);
                }
            } else {
                if ($m_info[pmi_title] != "" || $m_info[pmi_desc] != "") {
                    $sql = "insert into shop_product_mandatory_info(pmi_ix,pid,pmi_code,pmi_title,pmi_desc,insert_yn,regdate) values('','" . $pid . "','" . $m_info[pmi_code] . "','" . $m_info[pmi_title] . "','" . $m_info[pmi_desc] . "','Y',NOW())";
                    $db->sequences = "SHOP_GOODS_MANDATORY_INFO_SEQ";
                    $db->query($sql);
                }
            }
        }
    }

    $sql = "delete from shop_product_mandatory_info where pid = '" . $pid . "' and insert_yn='N' ";
    $db->query($sql);
    //상품필수고시 - END


    //상품필수고시_글로벌 - START
    $sql = "update shop_product_mandatory_info_global set insert_yn='N' where pid = '" . $pid . "' ";
    $db->query($sql);

    if (is_array($mandatory_info_global)) {
        foreach ($mandatory_info_global as $m_info) {
            if ($m_info[pmi_ix] != "") {
                if ($m_info[pmi_title] != "" || $m_info[pmi_code] != "") {

                    $sql = "update shop_product_mandatory_info_global set pmi_code='" . $m_info[pmi_code] . "', pmi_title='" . $m_info[pmi_title] . "',pmi_desc='" . $m_info[pmi_desc] . "',insert_yn='Y' where pmi_ix = '" . $m_info[pmi_ix] . "' ";
                    $db->query($sql);
                }
            } else {
                if ($m_info[pmi_title] != "" || $m_info[pmi_desc] != "") {
                    $sql = "insert into shop_product_mandatory_info_global (pmi_ix,pid,pmi_code,pmi_title,pmi_desc,insert_yn,regdate) values('','" . $pid . "','" . $m_info[pmi_code] . "','" . $m_info[pmi_title] . "','" . $m_info[pmi_desc] . "','Y',NOW())";
                    $db->sequences = "SHOP_GOODS_MANDATORY_INFO_SEQ";
                    $db->query($sql);
                }
            }
        }
    }

    $sql = "delete from shop_product_mandatory_info_global where pid = '" . $pid . "' and insert_yn='N' ";
    $db->query($sql);
    //상품필수고시_글로벌 - END

//코디옵션 추가 시작 2014-01-09 이학봉
    if ($product_type == "99") {
        if ($use_option_type == "box_option") {
            unset($stock_options);
            unset($set2options);
            unset($codi_options);
        } else if ($use_option_type == "set_option") {
            unset($box_options);
            unset($set2options);
            unset($stock_options);
            unset($codi_options);
        } else if ($use_option_type == "set2_option") {
            unset($box_options);
            unset($stock_options);
            unset($codi_options);
        } else if ($use_option_type == "codi_option") {    //코디옵션
            unset($box_options);
            unset($set2options);
            unset($stock_options);
        } else {
            unset($stock_options);
            unset($set2options);
            unset($box_options);
            unset($codi_options);
        }
    } else if ($product_type == "4" || $product_type == "21") {
        unset($box_options);
    } else if ($product_type == "31") {
        unset($box_options);
        unset($set2options);
    } else if ($product_type == "77") {    //사은품 상품은 세트(묶음상품)옵션만(set2options) 사용 2014-04-09 이학봉 unset($set2options); 사용안하게 바꿈 사은품에서 사용할 필요 없슴 JK 151112
        unset($box_options);        //박스 옵션
        unset($stock_options);        //가격 + 재고관리 옵션
        unset($codi_options);        //코디 옵션
        unset($set2options);
    } else {
        unset($box_options);
        unset($set2options);

        if ($stock_use_yn == "N") {
            unset($stock_options);
        }
    }

    OptionUpdate($db, $pid, $stock_options[0], "b");
    OptionUpdate($db, $pid, $box_options[0], "x");
    for ($i = 0; $i < count($addoptions); $i++) {
        OptionUpdate($db, $pid, $addoptions[$i], "a");
    }

    CodiOptionUpdate($db, $pid, $codi_options);    //코디옵션
    SetOptionUpdate($db, $pid, $set2options);

    //코디옵션 추가 시작 2014-01-09 이학봉
    //echo 'go3';	exit;


    if ($option_all_use == "Y") {
        $sql = "select opn_ix from " . TBL_SHOP_PRODUCT_OPTIONS . " where pid = '$pid' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ";
        $db->query($sql);
        if ($db->total) {
            $del_options = $db->fetchall();
            for ($i = 0; $i < count($del_options); $i++) {
                $db->query("delete from " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " where opn_ix='" . $del_options[$i][opn_ix] . "' and pid = '$pid' ");
            }
        }
        $db->query("delete from " . TBL_SHOP_PRODUCT_OPTIONS . " where pid = '$pid' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ");
    } else {
        /**
         * 2013.06.09 신훈식
         * loop 안에 있어서 밖에 옵션은 삭제 해도 될듯..
         **/

        if ($stock_use_yn == "Y" && $product_type == "0") {
            $db->query("update  " . TBL_SHOP_PRODUCT_OPTIONS . " set insert_yn = 'N'  where pid = '$pid' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ");
            if (is_array($options)) {
                $option_vieworder = 1;
                foreach ($options as $ops_key => $ops_value) {

                    if ($options[$ops_key]["option_name"]) {

                        $db->query("SELECT opn_ix FROM " . TBL_SHOP_PRODUCT_OPTIONS . " WHERE pid = '$pid' and opn_ix = '" . $options[$ops_key]["opn_ix"] . "' and option_kind in ('c1','c2','i1','i2','p','s','r','g')  ");

                        if ($options[$ops_key]["option_use"]) {
                            $options_use = $options[$ops_key]["option_use"];
                        } else {
                            $options_use = 0;
                        }

                        if (count($options[$ops_key]["global_oinfo"]) > 0) {
                            foreach ($options[$ops_key]["global_oinfo"] as $colum => $li) {
                                foreach ($li as $ln => $val) {
                                    $options[$ops_key]["global_oinfo"][$colum][$ln] = urlencode($val);
                                }
                            }
                        }

                        $options[$ops_key]["global_oinfo"] = json_encode($options[$ops_key]["global_oinfo"]);

                        if ($db->total) {
                            $db->fetch();
                            $opn_ix = $db->dt[opn_ix];
                            $sql = "update  " . TBL_SHOP_PRODUCT_OPTIONS . " set
										global_oinfo='" . $options[$ops_key]["global_oinfo"] . "',
										option_name='" . trim($options[$ops_key]["option_name"]) . "', option_kind='" . strtolower($options[$ops_key]["option_kind"]) . "',
										option_type='" . $options[$ops_key]["option_type"] . "', option_use='" . $options_use . "', option_vieworder='" . $option_vieworder . "', insert_yn='Y'
										where opn_ix = '" . $opn_ix . "' ";//$

                            $db->query($sql);

                        } else {
                            $sql = "INSERT INTO " . TBL_SHOP_PRODUCT_OPTIONS . " (opn_ix, pid, global_oinfo, option_name, option_kind, option_type, option_use, option_vieworder, regdate)
											VALUES
											('','$pid','" . $options[$ops_key]["global_oinfo"] . "','" . $options[$ops_key]["option_name"] . "','" . strtolower($options[$ops_key]["option_kind"]) . "','" . $options[$ops_key]["option_type"] . "','" . $options_use . "','" . $option_vieworder . "',NOW())";
                            $db->sequences = "SHOP_GOODS_OPTIONS_SEQ";
                            $db->query($sql);

                            if ($db->dbms_type == "oracle") {
                                $opn_ix = $db->last_insert_id;
                            } else {
                                $db->query("SELECT opn_ix FROM " . TBL_SHOP_PRODUCT_OPTIONS . " WHERE opn_ix=LAST_INSERT_ID()");
                                $db->fetch();
                                $opn_ix = $db->dt[opn_ix];
                            }
                        }

                        $sql = "update " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " set insert_yn='N'	where opn_ix='" . $opn_ix . "'  and pid = '$pid' ";
                        $db->query($sql);
                        $jj = 0;

                        foreach ($options[$ops_key]["details"] as $od_key => $od_value) {
                            if ($options[$ops_key][details][$od_key][option_div]) {

                                if (count($options[$ops_key][details][$od_key]["global_odinfo"]) > 0) {
                                    foreach ($options[$ops_key][details][$od_key]["global_odinfo"] as $colum => $li) {
                                        foreach ($li as $ln => $val) {
                                            $options[$ops_key][details][$od_key]["global_odinfo"][$colum][$ln] = urlencode($val);
                                        }
                                    }
                                }

                                $options[$ops_key][details][$od_key]["global_odinfo"] = json_encode($options[$ops_key][details][$od_key]["global_odinfo"]);

                                $db->query("SELECT id FROM " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " WHERE option_div = '" . trim($options[$ops_key][details][$od_key][option_div]) . "' and opn_ix = '" . $opn_ix . "' ");

                                if ($db->total) {
                                    $db->fetch();
                                    $opd_ix = $db->dt[id];
                                    $sql = "update " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " set
											global_odinfo='" . $options[$ops_key][details][$od_key]["global_odinfo"] . "',
											set_group_seq='" . $jj . "',
											option_div='" . $options[$ops_key][details][$od_key][option_div] . "',
											option_code='" . $options[$ops_key][details][$od_key][code] . "',
											option_coprice='" . $options[$ops_key][details][$od_key][coprice] . "',
											option_price='" . $options[$ops_key][details][$od_key][price] . "',
											option_soldout='" . $options[$ops_key][details][$od_key][option_soldout] . "',
											option_etc1='" . $options[$ops_key][details][$od_key][etc1] . "',
											option_stock='0', option_safestock='0' ,
											insert_yn='Y'
											, option_size = '" . trim($options[$ops_key][details][$od_key][option_size]) . "'
											, option_color = '" . trim($options[$ops_key][details][$od_key][option_color]) . "'
											where id ='" . $opd_ix . "' and opn_ix = '" . $opn_ix . "'";
                                    //
                                    //option_useprice='".$options[$ops_key][details][$od_key][price]."',  2012-11-06 홍진영(char 1 이기 때문에 오라클에서 에러남)
                                } else {
                                    $sql = "INSERT INTO " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " (id, pid, opn_ix, global_odinfo, set_group_seq, option_div, option_code,option_coprice,option_price, option_stock, option_safestock, option_soldout, option_etc1) ";
                                    $sql = $sql . " values('','$pid','" . $opn_ix . "','" . $options[$ops_key][details][$od_key]["global_odinfo"] . "','" . $jj . "','" . trim($options[$ops_key][details][$od_key][option_div]) . "','" . $options[$ops_key][details][$od_key][code] . "','" . $options[$ops_key][details][$od_key][coprice] . "','" . $options[$ops_key][details][$od_key][price] . "','0','0','" . $options[$ops_key][details][$od_key][option_soldout] . "','" . $options[$ops_key][details][$od_key][etc1] . "') ";
                                }
                                $db->sequences = "SHOP_GOODS_OPTIONS_DT_SEQ";
                                $db->query($sql);
                                $jj++;
                            }
                        }
                        $db->query("delete from " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " where opn_ix='" . $opn_ix . "' and insert_yn = 'N'  and pid = '$pid' ");
                    }

                    /**
                     * 2013.06.09 신훈식
                     * loop 안에 밖에 있는 부분을 loop 안으로 이동
                     * select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and insert_yn = 'N'  쿼리 부분에 opn_ix 값 추가
                     **/
                    $sql = "select opn_ix from " . TBL_SHOP_PRODUCT_OPTIONS . " where pid = '$pid' and opn_ix = '" . $options[$ops_key]["opn_ix"] . "'  and insert_yn = 'N' and option_kind in ('c1','c2','i1','i2','p','s','r','g')  ";

                    $db->query($sql);
                    if ($db->total) {
                        $del_options = $db->fetchall();
                        for ($i = 0; $i < count($del_options); $i++) {
                            $db->query("delete from " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " where opn_ix='" . $del_options[$i][opn_ix] . "' and pid = '$pid'  ");
                        }
                    }
                    $db->query("delete from " . TBL_SHOP_PRODUCT_OPTIONS . " where pid = '$pid' and opn_ix = '" . $options[$ops_key]["opn_ix"] . "'  and insert_yn = 'N' and option_kind in ('c1','c2','i1','i2','p','s','r','g')  ");
                    $option_vieworder++;
                }
            }

            // 옵션 처리후 삭제되야 되는 옵션 정리
            $sql = "select opn_ix from " . TBL_SHOP_PRODUCT_OPTIONS . " where pid = '$pid' and insert_yn = 'N' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ";
            //echo $sql."<br><br>";
            $db->query($sql);
            if ($db->total) {
                $del_options = $db->fetchall();
                //print_r($del_options);
                for ($i = 0; $i < count($del_options); $i++) {
                    $db->query("delete from " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " where opn_ix='" . $del_options[$i][opn_ix] . "' and pid = '$pid' ");
                }
            }
            $db->query("delete from " . TBL_SHOP_PRODUCT_OPTIONS . " where pid = '$pid' and insert_yn = 'N' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ");
        } else {
            $db->query("SELECT opn_ix FROM " . TBL_SHOP_PRODUCT_OPTIONS . " WHERE pid = '" . $pid . "' and option_kind in ('c1','c2','i1','i2','p','s','r','g')");

            if ($db->total) {
                $basic_options = $db->fetchall("object");
                foreach ($basic_options as $bok => $bov) {
                    $sql = "delete from " . TBL_SHOP_PRODUCT_OPTIONS_DETAIL . " where opn_ix='" . $bov[opn_ix] . "'  ";
                    $db->query($sql);
                }
            }
            $sql = "delete from " . TBL_SHOP_PRODUCT_OPTIONS . " where pid = '" . $pid . "' and option_kind in ('c1','c2','i1','i2','p','s','r','g') ";
            $db->query($sql);
        }
    }// option_all_use 있는지 여부

    //관련상품업데이트
    if ($rpid[1]) {

        $db->query("update " . TBL_SHOP_RELATION_PRODUCT . " set insert_yn = 'N' where pid = '" . $pid . "'");
        for ($i = 0; $i < count($rpid[1]); $i++) {
            $sql = "select rp_ix from " . TBL_SHOP_RELATION_PRODUCT . " where pid = '$pid' and rp_pid = '" . $rpid[1][$i] . "' ";
            $db->query($sql);
            $db->fetch();
            if ($db->total) {
                $sql = "update " . TBL_SHOP_RELATION_PRODUCT . " set insert_yn = 'Y' , vieworder = '" . ($i + 1) . "' where rp_ix ='" . $db->dt[rp_ix] . "'";
                $db->query($sql);
            } else {
                $sql = "insert into " . TBL_SHOP_RELATION_PRODUCT . " (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','" . $rpid[1][$i] . "','" . ($i + 1) . "','Y',NOW())";
                $db->sequences = "SHOP_RELATION_PRODUCT_SEQ";
                $db->query($sql);
            }
        }
        $db->query("delete from " . TBL_SHOP_RELATION_PRODUCT . " where insert_yn = 'N' and pid ='" . $pid . "' ");

    } else {
        $db->query("delete from " . TBL_SHOP_RELATION_PRODUCT . " where pid ='" . $pid . "' ");
    }

    if ($rpid[2]) {

        $db->query("update shop_relation_product2 set insert_yn = 'N' where pid = '" . $pid . "'");
        for ($i = 0; $i < count($rpid[2]); $i++) {
            $sql = "select rp_ix from shop_relation_product2 where pid = '$pid' and rp_pid = '" . $rpid[2][$i] . "' ";
            $db->query($sql);
            $db->fetch();
            if ($db->total) {
                $sql = "update shop_relation_product2 set insert_yn = 'Y' , vieworder = '" . ($i + 1) . "' where rp_ix ='" . $db->dt[rp_ix] . "'";
                $db->query($sql);
            } else {
                $sql = "insert into shop_relation_product2 (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','" . $rpid[2][$i] . "','" . ($i + 1) . "','Y',NOW())";
                $db->query($sql);
            }
        }
        $db->query("delete from shop_relation_product2 where insert_yn = 'N' and pid ='" . $pid . "' ");

    } else {
        $db->query("delete from shop_relation_product2 where pid ='" . $pid . "' ");
    }

    if ($rpid[3]) {

        $db->query("update shop_relation_add_product set insert_yn = 'N' where pid = '" . $pid . "'");
        for ($i = 0; $i < count($rpid[3]); $i++) {
            $sql = "select rp_ix from shop_relation_add_product where pid = '$pid' and rp_pid = '" . $rpid[3][$i] . "' ";
            $db->query($sql);
            $db->fetch();
            if ($db->total) {
                $sql = "update shop_relation_add_product set insert_yn = 'Y' , vieworder = '" . ($i + 1) . "' where rp_ix ='" . $db->dt[rp_ix] . "'";
                $db->query($sql);
            } else {
                $sql = "insert into shop_relation_add_product (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','" . $rpid[3][$i] . "','" . ($i + 1) . "','Y',NOW())";
                $db->query($sql);
            }
        }
        $db->query("delete from shop_relation_add_product where insert_yn = 'N' and pid ='" . $pid . "' ");

    } else {
        $db->query("delete from shop_relation_add_product where pid ='" . $pid . "' ");
    }

	if ($rpid[4]) {

        $db->query("update shop_relation_product3 set insert_yn = 'N' where pid = '" . $pid . "'");
        for ($i = 0; $i < count($rpid[4]); $i++) {
            $sql = "select rp_ix from shop_relation_product3 where pid = '$pid' and rp_pid = '" . $rpid[4][$i] . "' ";
            $db->query($sql);
            $db->fetch();
            if ($db->total) {
                $sql = "update shop_relation_product3 set insert_yn = 'Y' , vieworder = '" . ($i + 1) . "' where rp_ix ='" . $db->dt[rp_ix] . "'";
                $db->query($sql);
            } else {
                $sql = "insert into shop_relation_product3 (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','" . $rpid[4][$i] . "','" . ($i + 1) . "','Y',NOW())";
                $db->query($sql);
            }
        }
        $db->query("delete from shop_relation_product3 where insert_yn = 'N' and pid ='" . $pid . "' ");

    } else {
        $db->query("delete from shop_relation_product3 where pid ='" . $pid . "' ");
    }

    //관련 사은품 업데이트
    if ($fpid[1]) {
        $db->query("update shop_product_gift set insert_yn = 'N' where pid = '" . $pid . "'");
        for ($i = 0; $i < count($fpid[1]); $i++) {
            $sql = "select gift_ix from shop_product_gift where pid = '$pid' and gift_pid = '" . $fpid[1][$i] . "' ";
            $db->query($sql);
            $db->fetch();
            if ($db->total) {
                $sql = "update shop_product_gift set insert_yn = 'Y' , vieworder = '" . ($i + 1) . "' where gift_ix ='" . $db->dt[gift_ix] . "'";
                $db->query($sql);
            } else {
                $sql = "insert into shop_product_gift (pid,gift_pid,vieworder,insert_yn,regdate) values ('$pid','" . $fpid[1][$i] . "','" . ($i + 1) . "','Y',NOW())";
                $db->sequences = "SHOP_GIFT_PRODUCT_SEQ";
                $db->query($sql);
            }
        }
        $db->query("delete from shop_product_gift where insert_yn = 'N' and pid ='" . $pid . "' ");
    } else {
        $db->query("delete from shop_product_gift where pid ='" . $pid . "' ");
    }

    /**
     * 2013.06.08 신훈식
     * 제목 : 관련 상품 카테고리 수정
     **/

    if ($category[0]) {
        $db->debug = true;
        $db->query("update shop_relation_category set insert_yn = 'N'  where pid = '" . $pid . "' ");

        for ($j = 0; $j < count($category[0]); $j++) {
            $db->query("select rc_ix from shop_relation_category where pid = '" . $pid . "' and cid = '" . $category[0][$j] . "' ");

            if (!$db->total) {
                $sql = "insert into shop_relation_category (rc_ix,cid,depth,pid, vieworder, insert_yn, regdate) values ('','" . $category[0][$j] . "','" . $depth[0][$j] . "','" . $pid . "','" . ($j + 1) . "','Y', NOW())";//depth 컬럼 추가 kbk 13/07/01
                $db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
                $db->query($sql);
            } else {
                $sql = "update shop_relation_category set insert_yn = 'Y',vieworder='" . ($j + 1) . "' where pid = '" . $pid . "' and cid = '" . $category[0][$j] . "' ";
                $db->query($sql);
            }
        }

        $db->query("delete from shop_relation_category where pid = '" . $pid . "' and insert_yn = 'N' ");
    }


    //세트상품업데이트
    if ($rpid[2]) {
        //$db->debug = true;
        $db->query("update shop_product_set_relation set insert_yn = 'N' where pid = '" . $pid . "'");
        for ($i = 0; $i < count($rpid[2]); $i++) {
            $sql = "select psr_ix from shop_product_set_relation where pid = '$pid' and set_pid = '" . $rpid[2][$i] . "' ";
            $db->query($sql);
            $db->fetch();
            if ($db->total) {
                $sql = "update shop_product_set_relation set insert_yn = 'Y' , vieworder = '" . ($i + 1) . "' where psr_ix ='" . $db->dt[psr_ix] . "'";
                $db->query($sql);
            } else {
                $sql = "insert into shop_product_set_relation (psr_ix,pid,set_pid,vieworder,insert_yn,regdate) values ('','$pid','" . $rpid[2][$i] . "','" . ($i + 1) . "','Y',NOW())";
                $db->sequences = "SHOP_GOODS_SET_LINK_SEQ";
                $db->query($sql);
            }
        }
        $db->query("delete from shop_product_set_relation where insert_yn = 'N' and pid ='" . $pid . "' ");
        //$db->debug = false;
    } else {
        $db->query("delete from shop_product_set_relation where pid ='" . $pid . "' ");
    }


    if ($display_options) {
        $sql = "update " . TBL_SHOP_PRODUCT_DISPLAYINFO . " set insert_yn='N'	where pid = '$pid' ";
        $db->query($sql);
        //for($i=0;$i < count($_POST["display_options"]);$i++){
        foreach ($display_options as $do_key => $do_value) {
            if ($display_options[$do_key]["dp_title"] && $display_options[$do_key]["dp_desc"]) {

                if ($display_options[$do_key]["dp_use"]) {
                    $dp_use = $display_options[$do_key]["dp_use"];
                } else {
                    $dp_use = "0";
                }

                $dp_ix = $display_options[$do_key]["dp_ix"];//디스플레이 옵션 수정 kbk 12/06/19
                if ($dp_ix != "0" && $dp_ix != "") {//디스플레이 옵션 수정 kbk 12/06/19

                    $sql = "update " . TBL_SHOP_PRODUCT_DISPLAYINFO . " set
									dp_title = '" . $display_options[$do_key]["dp_title"] . "',
									dp_desc = '" . $display_options[$do_key]["dp_desc"] . "',
									dp_etc_desc = '" . $display_options[$do_key]["dp_etc_desc"] . "',
									insert_yn = 'Y' ,dp_use = '" . $dp_use . "'
									where dp_ix = '$dp_ix'";
                } else {
                    $sql = "insert into " . TBL_SHOP_PRODUCT_DISPLAYINFO . " (dp_ix,pid,dp_title,dp_desc,dp_etc_desc,dp_use, regdate) values('','$pid','" . $display_options[$do_key]["dp_title"] . "','" . $display_options[$do_key]["dp_desc"] . "','" . $display_options[$do_key]["dp_etc_desc"] . "','" . $dp_use . "',NOW()) ";
                }
                $db->sequences = "SHOP_GOODS_DISPLAYINFO_SEQ";
                $db->query($sql);
            }
        }
        $db->query("delete from " . TBL_SHOP_PRODUCT_DISPLAYINFO . " where pid = '$pid' and insert_yn = 'N' ");
    }

    /**
     * 2013.06.09 신훈식
     * 바이럴 정보 수정프로세스
     *
     **/
    if ($virals) {
        //$db->debug = true;
        $sql = "update shop_product_viralinfo set insert_yn='N'	where pid = '$pid' ";
        $db->query($sql);
        //for($i=0;$i < count($_POST["virals"]);$i++){
        foreach ($virals as $do_key => $do_value) {
            if ($virals[$do_key]["viral_name"] && $virals[$do_key]["viral_url"]) {
                if ($virals[$do_key]["vi_use"]) {
                    $vi_use = $virals[$do_key]["vi_use"];
                } else {
                    $vi_use = "0";
                }

                $vi_ix = $virals[$do_key]["vi_ix"];//디스플레이 옵션 수정 kbk 12/06/19
                if ($vi_ix != "0" && $vi_ix != "") {//디스플레이 옵션 수정 kbk 12/06/19

                    $sql = "update shop_product_viralinfo set
									viral_name = '" . $virals[$do_key]["viral_name"] . "',viral_url = '" . $virals[$do_key]["viral_url"] . "',
									viral_desc = '" . $virals[$do_key]["viral_desc"] . "', insert_yn = 'Y' ,vi_use = '" . $vi_use . "'
									where vi_ix = '$vi_ix'";
                } else {
                    $sql = "insert into shop_product_viralinfo (vi_ix,pid,viral_name,viral_url,viral_desc, vi_use, regdate) values('','$pid','" . $virals[$do_key]["viral_name"] . "','" . $virals[$do_key]["viral_url"] . "','" . $virals[$do_key]["viral_desc"] . "','" . $vi_use . "',NOW()) ";
                    //echo $sql;

                }
                $db->sequences = "SHOP_GOODS_VIRALINFO_SEQ";
                $db->query($sql);
            }
        }
        $db->query("delete from shop_product_viralinfo where pid = '$pid' and insert_yn = 'N' ");
    }

    //$db->query("update " . TBL_SHOP_ADDIMAGE . " set insert_yn = 'N' WHERE pid = '$pid' ");

    //2012-07-30 홍진영
    /*$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/addimg", $pid, 'Y');
    $cnt_addimages = count($addimages);

    if (is_array($addimages)) {
        $addimages_keys = array_keys($addimages);
    }

    for ($j = 0; $j < $cnt_addimages; $j++) {

        //아카마이 ftp 파일 업로드
        $akamaiFtpUploadFiles = array();

        $i = $addimages_keys[$j];

        $db->query("SELECT id FROM " . TBL_SHOP_ADDIMAGE . " WHERE id = '" . $addimages[$i][ad_ix] . "' ");

        if (!$db->total) {
            if ($_FILES[addimages][name][$i][addbimg]) {
                if ($_SESSION["admininfo"]["mall_ix"] == "cb04d740160969f940ae3aaa3fae5ee0") {//lub2b.co.kr 에서만 강제적으로 딥줌 생성되도록 함 kbk 12/12/26
                    $sql = "INSERT INTO " . TBL_SHOP_ADDIMAGE . " (id, pid, deepzoom, regdate) values('', '$pid', '1',NOW()) ";
                } else {
                    $sql = "INSERT INTO " . TBL_SHOP_ADDIMAGE . " (id, pid, deepzoom, regdate) values('', '$pid', '" . $addimages[$i][add_copy_deepzoomimg] . "',NOW()) ";
                }
                $db->sequences = "SHOP_ADDIMAGE_SEQ";
                $db->query($sql);

                if ($db->dbms_type == "oracle") {
                    $ad_ix = $db->last_insert_id;
                } else {
                    $db->query("SELECT id FROM " . TBL_SHOP_ADDIMAGE . " WHERE id=LAST_INSERT_ID()");
                    $db->fetch();
                    $ad_ix = $db->dt[id];
                }
            }
        } else {
            //if($addimages[$i][add_copy_deepzoomimg] == 1){
            if ($_SESSION["admininfo"]["mall_ix"] == "cb04d740160969f940ae3aaa3fae5ee0") {//lub2b.co.kr 에서만 강제적으로 딥줌 생성되도록 함 kbk 12/12/26
                $addimg_update_str = ", deepzoom = '1'";
            } else {
                $addimg_update_str = "";
            }

            $db->query("update " . TBL_SHOP_ADDIMAGE . " set insert_yn = 'Y' $addimg_update_str WHERE pid = '$pid' and id = '" . $addimages[$i][ad_ix] . "' ");

            $ad_ix = $addimages[$i][ad_ix];
        }

        if ($_FILES[addimages][size][$i][addbimg] > 0 || $addimages[$i][add_copy_deepzoomimg] == 1) {
            $image_info = getimagesize($_FILES[addimages][tmp_name][$i][addbimg]);
            $image_type = substr($image_info['mime'], -3);

            $akamaiFtpUploadFiles[] = "basic_" . $ad_ix . "_add.gif";
            $akamaiFtpUploadFiles[] = "b_" . $ad_ix . "_add.gif";

            if ($image_type == "gif") {
                if ($_FILES[addimages][size][$i][addbimg] > 0) {
                    copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif");
                }
                MirrorGif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", MIRROR_NONE);
                resize_gif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $image_info2[0][width], $image_info2[0][height], $image_resize_type);

                if ($addimages[$i][add_chk_mimg] == 1) {
                    MirrorGif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif", MIRROR_NONE);
                    resize_gif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);

                    $akamaiFtpUploadFiles[] = "m_" . $ad_ix . "_add.gif";
                }

                if ($addimages[$i][add_chk_cimg] == 1) {
                    MirrorGif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif", MIRROR_NONE);
                    resize_gif($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);

                    $akamaiFtpUploadFiles[] = "c_" . $ad_ix . "_add.gif";
                }
            } else if ($image_type == "png") {
                if ($_FILES[addimages][size][$i][addbimg] > 0) {
                    copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif");
                }
                MirrorPNG($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", MIRROR_NONE);
                resize_png($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $image_info2[0][width], $image_info2[0][height], $image_resize_type);

                if ($addimages[$i][add_chk_mimg] == 1) {
                    MirrorPNG($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif", MIRROR_NONE);
                    resize_png($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);

                    $akamaiFtpUploadFiles[] = "m_" . $ad_ix . "_add.gif";
                }

                if ($addimages[$i][add_chk_cimg] == 1) {
                    MirrorPNG($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif", MIRROR_NONE);
                    resize_png($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);

                    $akamaiFtpUploadFiles[] = "c_" . $ad_ix . "_add.gif";
                }

            } else {
                if ($_FILES[addimages][size][$i][addbimg] > 0) {
                    copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif");
                }

                Mirror($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", MIRROR_NONE);
                resize_jpg($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $image_info2[0][width], $image_info2[0][height], $image_resize_type);

                if ($addimages[$i][add_chk_mimg] == 1) {
                    Mirror($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif", MIRROR_NONE);
                    resize_jpg($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);

                    $akamaiFtpUploadFiles[] = "m_" . $ad_ix . "_add.gif";
                }

                if ($addimages[$i][add_chk_cimg] == 1) {
                    Mirror($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $ad_ix . "_add.gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif", MIRROR_NONE);
                    resize_jpg($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);

                    $akamaiFtpUploadFiles[] = "c_" . $ad_ix . "_add.gif";
                }
            }

            //if($addimages[$i][add_copy_deepzoomimg] == 1){
            if ($_SESSION["admininfo"]["mall_ix"] == "cb04d740160969f940ae3aaa3fae5ee0") {//lub2b.co.kr 에서만 강제적으로 딥줌 생성되도록 함 kbk 12/12/26
                if ($ad_ix) {
                    rmdirr($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg/deepzoom/" . $ad_ix);
                }

                $path = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg/deepzoom";

                if (!is_dir($path)) {
                    mkdir($path, 0777);
                    chmod($path, 0777);
                } else {
                    chmod($path, 0777);
                };

                $client = new SoapClient("http://" . $_SERVER["HTTP_HOST"] . "/VESAPI/VESAPIWS.asmx?wsdl=0");
                $params = new stdClass();
                $params->inputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/basic_" . $ad_ix . "_add.gif";
                $params->outputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg/deepzoom/" . $ad_ix;
                $response = $client->TilingWithPhysicalPath($params);

            }
        }
        //$db->debug = false;
        if ($_FILES[addimages][size][$i][addmimg] > 0) {
            move_uploaded_file($_FILES[addimages][tmp_name][$i][addmimg], $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $ad_ix . "_add.gif");
            $akamaiFtpUploadFiles[] = "m_" . $ad_ix . "_add.gif";
        }

        if ($_FILES[addimages][size][$i][addcimg] > 0) {
            move_uploaded_file($_FILES[addimages][tmp_name][$i][addcimg], $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $ad_ix . "_add.gif");
            $akamaiFtpUploadFiles[] = "c_" . $ad_ix . "_add.gif";
        }
        //kbk }

        akamaiFtpUpload($admin_config[mall_data_root] . "/images/addimg" . $adduploaddir, $akamaiFtpUploadFiles);
    }*/

    /*$db->query("SELECT id FROM " . TBL_SHOP_ADDIMAGE . " WHERE insert_yn = 'N' and pid = '$pid' ");

    for ($i = 0; $i < $db->total; $i++) {
        $db->fetch($i);

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $db->dt[ad_ix] . "_add.gif")) {
            unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/b_" . $db->dt[ad_ix] . "_add.gif");
        }

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $db->dt[ad_ix] . "_add.gif")) {
            unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/m_" . $db->dt[ad_ix] . "_add.gif");
        }

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $db->dt[ad_ix] . "_add.gif")) {
            unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/addimg" . $adduploaddir . "/c_" . $db->dt[ad_ix] . "_add.gif");
        }

    }

    $db->query("delete from  " . TBL_SHOP_ADDIMAGE . " WHERE insert_yn = 'N' and pid = '$pid' ");*/


    //기본 정책이 무료인정책 뽑아내기
    $wholesale_free_delivery_yn = 0;
    $free_delivery_yn = 0;
    $sql = "select 
			pd.is_wholesale
		from 
			shop_product_delivery as pd 
			inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
		where
			pd.pid='" . $pid . "' and dt.delivery_policy = '1'
		group by pd.is_wholesale ";
    $db->query($sql);

    if ($db->total) {
        $db->fetch(0);
        if ($db->dt[is_wholesale] == "W") {
            $wholesale_free_delivery_yn = 1;
        }
        if ($db->dt[is_wholesale] == "R") {
            $free_delivery_yn = 1;
        }
        $db->fetch(1);
        if ($db->dt[is_wholesale] == "W") {
            $wholesale_free_delivery_yn = 1;
        }
        if ($db->dt[is_wholesale] == "R") {
            $free_delivery_yn = 1;
        }
    }

    //추가 정보 입력하기!!
    $sql = "select
				GROUP_CONCAT(pod.option_div SEPARATOR '|') as option_div_text, GROUP_CONCAT(pod.option_gid SEPARATOR '|') as gid_text
			from 
				shop_product_options po 
				left join shop_product_options_detail pod on (po.pid = pod.pid and po.opn_ix = pod.opn_ix)
			where 
				po.pid='" . $pid . "' and po.option_use = '1'";
    $db->query($sql);
    $db->fetch(0);
    $option_div_text = $db->dt[option_div_text];
    $gid_text = $db->dt[gid_text];

    if ($gid) {
        $gid_text = $gid;
    }

    $sql = "update shop_product_addinfo set option_div_text='" . $option_div_text . "', gid_text='" . $gid_text . "', wholesale_free_delivery_yn='" . $wholesale_free_delivery_yn . "', free_delivery_yn='" . $free_delivery_yn . "' where pid='" . $pid . "' ";
    $db->query($sql);


    $sql = "insert into shop_product_history (ph_ix, history_date, id, product_type, pname, product_color_chip, pcode, brand, brand_name, c_ix, company, paper_pname, buying_company, shotinfo, buyingservice_coprice, wholesale_price, wholesale_sellprice, listprice, sellprice, coprice, wholesale_yn, offline_yn, wholesale_reserve_yn, wholesale_reserve, wholesale_reserve_rate, wholesale_rate_type, reserve_yn, reserve, reserve_rate, rate_type, sns_btn_yn, sns_btn, delivery_coupon_yn, coupon_use_yn, bimg, mimg, msimg, simg, cimg, basicinfo, m_basicinfo, icons, state, product_weight, is_adult, disp, movie,movie_thumbnail,movie_now, vieworder, admin, trade_admin, md_code, sell_ing_cnt, stock, safestock, available_stock, remain_stock, view_cnt, order_cnt, recommend_cnt, wish_cnt, after_score, after_cnt, product_point, product_level, search_keyword, reg_category, option_stock_yn, supply_company, inventory_info, surtax_yorn, delivery_company, one_commission, commission, wholesale_commission, account_type, cupon_use_yn, stock_use_yn, delivery_policy, delivery_product_policy, delivery_package, delivery_freeprice, delivery_price, delivery_type, free_delivery_yn, free_delivery_count, is_sell_date, sell_priod_sdate, sell_priod_edate, allow_max_cnt, wholesale_allow_max_cnt, allow_basic_cnt, wholesale_allow_basic_cnt, allow_order_type, allow_order_cnt_byonesell, allow_order_cnt_byoneperson, allow_order_minimum_cnt, allow_byoneperson_cnt, wholesale_allow_byoneperson_cnt, md_one_commission, md_discount_name, md_sell_date_use, md_sell_priod_sdate, md_sell_priod_edate, whole_head_company_sale_rate, whole_seller_company_sale_rate, head_company_sale_rate, seller_company_sale_rate, origin, make_date, expiry_date, mandatory_type,mandatory_type_global, relation_product_cnt, relation_text1, relation_text2, relation_display_type, barcode, input_date, is_auto_change, auto_change_state, etc1, etc2, etc3, etc4, etc5, etc6, etc7, etc8, etc9, etc10, download_img, download_desc, hotcon_event_id, hotcon_pcode, co_goods, co_pid, co_company_id, bs_goods_url, bs_site, price_policy, currency_ix, round_precision, round_type, editdate, naver_update_date, disp_naver, disp_daum, add_index_date, is_pos_link, is_erp_link, add_status, regdate, regdate_desc, reg_charger_ix, reg_charger_name, is_delete, auto_sync_wms)
	(select '' as ph_ix, NOW(), id, product_type, pname, product_color_chip, pcode, brand, brand_name, c_ix, company, paper_pname, buying_company, shotinfo, buyingservice_coprice, wholesale_price, wholesale_sellprice, listprice, sellprice, coprice, wholesale_yn, offline_yn, wholesale_reserve_yn, wholesale_reserve, wholesale_reserve_rate, wholesale_rate_type, reserve_yn, reserve, reserve_rate, rate_type, sns_btn_yn, sns_btn, delivery_coupon_yn, coupon_use_yn, bimg, mimg, msimg, simg, cimg, basicinfo, m_basicinfo, icons, state, product_weight, is_adult, disp, movie,movie_thumbnail,movie_now, vieworder, admin, trade_admin, md_code, sell_ing_cnt, stock, safestock, available_stock, remain_stock, view_cnt, order_cnt, recommend_cnt, wish_cnt, after_score, after_cnt, product_point, product_level, search_keyword, reg_category, option_stock_yn, supply_company, inventory_info, surtax_yorn, delivery_company, one_commission, commission, wholesale_commission, account_type, cupon_use_yn, stock_use_yn, delivery_policy, delivery_product_policy, delivery_package, delivery_freeprice, delivery_price, delivery_type, free_delivery_yn, free_delivery_count, is_sell_date, sell_priod_sdate, sell_priod_edate, allow_max_cnt, wholesale_allow_max_cnt, allow_basic_cnt, wholesale_allow_basic_cnt, allow_order_type, allow_order_cnt_byonesell, allow_order_cnt_byoneperson, allow_order_minimum_cnt, allow_byoneperson_cnt, wholesale_allow_byoneperson_cnt, md_one_commission, md_discount_name, md_sell_date_use, md_sell_priod_sdate, md_sell_priod_edate, whole_head_company_sale_rate, whole_seller_company_sale_rate, head_company_sale_rate, seller_company_sale_rate, origin, make_date, expiry_date, mandatory_type,mandatory_type_global, relation_product_cnt, relation_text1, relation_text2, relation_display_type, barcode, input_date, is_auto_change, auto_change_state, etc1, etc2, etc3, etc4, etc5, etc6, etc7, etc8, etc9, etc10, download_img, download_desc, hotcon_event_id, hotcon_pcode, co_goods, co_pid, co_company_id, bs_goods_url, bs_site, price_policy, currency_ix, round_precision, round_type, editdate, naver_update_date, disp_naver, disp_daum, add_index_date, is_pos_link, is_erp_link, add_status, regdate, regdate_desc, reg_charger_ix, reg_charger_name, is_delete, auto_sync_wms from shop_product sp where id = '" . $pid . "')";
    $db->query($sql);

    if (!$bs_act) {//도매상품 업데이트 시 구분 값 kbk 13/04/22
        if ($act == "tmp_update") {
            echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품수정이 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";
        } else {
            if ($mmode == "pop") {
                echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품수정이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
            } else {

                if (in_array($product_type, $sns_product_type)) {
                    echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../sns/product_list.php';</script>";
                } else {
                    echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품수정이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/goods_input.php?id=" . $id . "';</script>";
                }
            }
        }
    }


    /*제휴사 연동 여부 체크 (JK160218)*/
    if ($state != '6' && $state != '7' && $state != '8') {
        $sellertool_single_update_bool = true;
    } else {
        $sellertool_single_update_bool = false;
    }

    if (is_array($partner_prd_reg_before)) {
        foreach ($partner_prd_reg_before as $be) {

            if (count($partner_prd_reg) > 0) {
                if (!in_array($be, $partner_prd_reg)) {
                    $sql = "select * from sellertool_get_product where site_code = '" . $be . "' and pid = '" . $id . "'";
                    $db->query($sql);
                    if ($db->total) {
                        $db->fetch();
                        $sgp_ix = $db->dt[sgp_ix];
                        $sql = "update sellertool_get_product set state = '0' where sgp_ix = '" . $sgp_ix . "' and state = '1'";
                        $db->query($sql);
                    }
                    if (function_exists('sellertool_single_update') && $sellertool_single_update_bool) {
                        sellertool_single_update($be, str_pad($pid, "10", "0", STR_PAD_LEFT), 'sold_out');
                    }
                    //API 연동 설정하였다가 다시 취소한 케이스 이기 때문에 실제로 제휴사에 연동된 상품이 존재하는지 확인 후 존재한다면 해당 상품에 대한 품절 처리를 하기 위해 추가

                }
            } else {
                //기존에 선택된 제휴사 연동 정보가 존재하지만 모두 체크 해제 되어 이전 제휴사 연동 상태를 모두 변경해야 할경우 처리
                $sql = "select * from sellertool_get_product where site_code = '" . $be . "' and pid = '" . $id . "'";
                $db->query($sql);
                if ($db->total) {
                    $db->fetch();
                    $sgp_ix = $db->dt[sgp_ix];
                    $sql = "update sellertool_get_product set state = '0' where sgp_ix = '" . $sgp_ix . "' and state = '1'";
                    $db->query($sql);
                }
                if (function_exists('sellertool_single_update') && $sellertool_single_update_bool) {
                    sellertool_single_update($be, str_pad($pid, "10", "0", STR_PAD_LEFT), 'sold_out');
                }
            }
        }
    }


    if (is_array($partner_prd_reg) && count($partner_prd_reg) > 0) {

        foreach ($partner_prd_reg as $p_reg) {
            $sql = "select * from sellertool_get_product where site_code = '" . $p_reg . "' and pid = '" . $id . "'";
            $db->query($sql);
            if ($db->total) {
                $db->fetch();
                $sgp_ix = $db->dt[sgp_ix];
                $sql = "update sellertool_get_product set state = '1' where sgp_ix = '" . $sgp_ix . "' and state = '0'";
                $db->query($sql);
            } else {
                $sql = "insert into sellertool_get_product (pid,site_code,state) values ('" . $id . "','" . $p_reg . "','1')";
                $db->query($sql);
            }
            if (function_exists('sellertool_single_update') && $sellertool_single_update_bool) {
                sellertool_single_update($p_reg, str_pad($pid, "10", "0", STR_PAD_LEFT), 'regist');
            }
        }
    }

    /*종료*/


    //echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품수정이 정상적으로 처리 되었습니다.');document.location.href='goods_input.php?mmode=$mmode&id=$pid'</script>";
}


if ($act == "search_multcategory") {    //다중카테고리 검색

    if (strpos($search_text, ",") !== false) {
        $search_array = explode(",", $search_text);
        $search_array = array_filter($search_array, create_function('$a', 'return preg_match("#\S#", $a);'));    //빈공간 업애줌
        $where = "and ( ";
        for ($i = 0; $i < count($search_array); $i++) {

            if ($i == count($search_array) - 1) {
                $where .= "cname like '%" . trim($search_array[$i]) . "%'";
            } else {
                $where .= "cname like '%" . trim($search_array[$i]) . "%' or ";
            }
        }
        $where .= ")";
    } else if (strpos($search_text, "\n") !== false) {//\n

        $search_array = explode("\n", $search_text);
        $search_array = array_filter($search_array, create_function('$a', 'return preg_match("#\S#", $a);'));
        $where = "and ( ";
        for ($i = 0; $i < count($search_array); $i++) {

            if ($i == count($search_array) - 1) {
                $where .= "cname like '%" . trim($search_array[$i]) . "%'";
            } else {
                $where .= "cname like '%" . trim($search_array[$i]) . "%' or ";
            }
        }
        $where .= ")";
    } else {
        $where .= "and cname like '%" . trim($search_text) . "%'";
    }

    $sql = "select
				depth,
				cid,
				cname
			from
				shop_category_info
			where
				1
				$where
				order by cid  ASC";
    //echo nl2br($sql);
    $db->query($sql);
    $category_array = $db->fetchall();

    for ($i = 0; $i < count($category_array); $i++) {
        $category_info[$category_array[$i][cid]] = GetParentCategory_2($category_array[$i][cid], $category_array[$i][depth]);
    }

    $datas = json_encode($category_info);
    $datas = str_replace("\"true\"", "true", $datas);
    $datas = str_replace("\"false\"", "false", $datas);
    echo $datas;

}


if ($act == "search_standard_category") {    //다중카테고리 검색

    //http://dev2.forbiz.co.kr/admin/product/goods_input.act.php?act=search_standard_category&search_text=%EC%BB%A4%ED%94%BC,%ED%99%8D%EC%B0%A8,%EC%9A%A9%ED%92%88

    if (strpos($search_text, ",") !== false) {
        $search_array = explode(",", $search_text);
        $search_array = array_filter($search_array, create_function('$a', 'return preg_match("#\S#", $a);'));    //빈공간 업애줌
        $where = "and ( ";
        for ($i = 0; $i < count($search_array); $i++) {

            if ($i == count($search_array) - 1) {
                $where .= "cname like '%" . trim($search_array[$i]) . "%'";
            } else {
                $where .= "cname like '%" . trim($search_array[$i]) . "%' or ";
            }
        }
        $where .= ")";
    } else if (strpos($search_text, "\n") !== false) {//\n

        $search_array = explode("\n", $search_text);
        $search_array = array_filter($search_array, create_function('$a', 'return preg_match("#\S#", $a);'));
        $where = "and ( ";
        for ($i = 0; $i < count($search_array); $i++) {

            if ($i == count($search_array) - 1) {
                $where .= "cname like '%" . trim($search_array[$i]) . "%'";
            } else {
                $where .= "cname like '%" . trim($search_array[$i]) . "%' or ";
            }
        }
        $where .= ")";
    } else {
        $where .= "and cname like '%" . trim($search_text) . "%'";
    }

    $sql = "select
				depth,
				cid,
				cname
			from
				standard_category_info
			where
				1
				$where
				order by cid  ASC";
    //echo nl2br($sql);
    $db->query($sql);
    $category_array = $db->fetchall();

    for ($i = 0; $i < count($category_array); $i++) {
        $category_info[$category_array[$i][cid]] = GetParentStandardCategory2($category_array[$i][cid], $category_array[$i][depth]);
    }

    $datas = json_encode($category_info);
    $datas = str_replace("\"true\"", "true", $datas);
    $datas = str_replace("\"false\"", "false", $datas);
    echo $datas;

}


if ($act == 'get_seller_setup') {    //개별상품 등록, 수정시 수수료 정보 불러오기 ajax 2014-04-07 이학봉

    if ($one_commission_use == 'N') {
        $sql = "select * from common_seller_delivery where company_id = '" . $company_id . "'";
    } else {
        $sql = "select * from shop_product where id = '" . $pid . "'";
    }
    $db->query($sql);
    $data_array = $db->fetchall();

    for ($i = 0; $i < count($i); $i++) {

        $seller_setup[$i][account_type] = $data_array[$i][account_type];
        $seller_setup[$i][commission] = $data_array[$i][commission];
        $seller_setup[$i][wholesale_commission] = $data_array[$i][wholesale_commission];
    }

    $datas = $seller_setup;
    $datas = json_encode($datas);
    $datas = str_replace("\"true\"", "true", $datas);
    $datas = str_replace("\"false\"", "false", $datas);
    echo $datas;
    exit;
}

if ($act == 'get_category_addinfo') {
    include_once("goods_input.lib.php");
    echo displayCategoryAddInfomation($cid);
    exit;
    //http://omnichannel.forbiz.co.kr/admin/product/goods_input.act.php?cid=000001001000000&act=get_category_addinfo
}

if ($act == 'get_standarad_category_addinfo') {
    include_once("goods_input.lib.php");
    //$sites = "partner_prd_reg%5B%5D=auction&partner_prd_reg%5B%5D=11st&partner_prd_reg%5B%5D=lazada&partner_prd_reg%5B%5D=qoo10";
    //$standard_cid = "000001001000000";
    parse_str(urldecode($sites));
    $sellertools = $partner_prd_reg;
    //print_r($sellertools);
    if (is_array($sellertools)) {
        foreach ($sellertools as $key => $sellertool) {
            $str .= displayStandardCategoryAddInfomation($sellertool, $standard_cid);
        }
    }
    echo $str;
    exit;
    //http://omnichannel.forbiz.co.kr/admin/product/goods_input.act.php?cid=000001001000000&act=get_category_addinfo
}


function CopyImage($pid, $type = "")
{
    global $admin_config, $image_info2;

    //if ($allimg != "none")
    $before_uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"]["mall_data_root"] . "/images/product", $_POST["bpid"], 'Y');
    $uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product", $pid, 'Y');

    if ($_FILES["allimg" . $type]["size"] > 0 || $_POST["mode"] == "copy" || ($_POST["bimg_text" . $type] && $_POST["img_url_copy" . $type])) {
        //워터마크 적용
        /*
		if($watermark) {
			require_once "../lib/class.upload.php";

			$s_water_handle = new Upload($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/product".$uploaddir."/basic_".$pid."".$type.".gif");
			$s_water_result = WaterMarkProcess2($s_water_handle, $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/");

			@copy($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/watermark".$uploaddir."/basic_".$pid."".$type.".gif",$_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/product".$uploaddir."/basic_".$pid."".$type.".gif");
			@chmod($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/product".$uploaddir."/basic_".$pid."".$type.".gif", 0777);
			$image_type = "gif"; // 워터마크 처리후 마임타입이 gif로 바뀐다.
		}
		*/
        if ($_POST["mode"] == "copy" && $_POST["bimg_text" . $type] == "" && $_FILES["allimg" . $type]["name"] == "") {// 상품 복사모드일때는 기존 상품의 이미지를 복사해서 나머지 이미지가 생성됩니다
            $basic_img_src = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $before_uploaddir . "/basic_" . $_POST["bpid"] . "" . $type . ".gif";
//echo "basic_img_src:".$basic_img_src;
            if (file_exists($basic_img_src)) {
                copy($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . "" . $type . ".gif");

                chmod($basic_img_src, 0777);
                $chk_allimg = 1;
                $chk_mimg = 1;
                $chk_msimg = 1;
                $chk_simg = 1;
                $chk_cimg = 1;

                $image_info = getimagesize($basic_img_src);
                $image_type = substr($image_info['mime'], -3);

                $image_width = $image_info[0];
                $image_height = $image_info[1];

                if ($image_width == $image_height) {
                    $etc9 = 1; // 정사각형
                } else {
                    $etc9 = 2; // 직사각형
                }
                $etc8 = $image_width . "*" . $image_height;
            }
        } else {

            if ($_POST["img_url_copy" . $type]) {

                if (copy($_POST["bimg_text" . $type], $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . "" . $type . ".gif")) {
                    $basic_img_src = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . "" . $type . ".gif";
                } else {
                    copy($_POST["bimg_text" . $type], $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . "" . $type . ".gif");
                    $basic_img_src = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . "" . $type . ".gif";
                }

                $allimg = $basic_img_src;
                //echo $basic_img_src;
                chmod($basic_img_src, 0777);
                $image_info = getimagesize($basic_img_src);
                $image_type = substr($image_info['mime'], -3);
                $image_width = $image_info[0];
                $image_height = $image_info[1];

                if ($image_width == $image_height) {
                    $etc9 = 1; // 정사각형
                } else {
                    $etc9 = 2; // 직사각형
                }
                $etc8 = $image_width . "*" . $image_height;

                $chk_allimg = 1;
                $chk_mimg = 1;
                $chk_msimg = 1;
                $chk_simg = 1;
                $chk_cimg = 1;
            } else {

                if ($_FILES["allimg" . $type]["name"]) {
                    $image_info = getimagesize($_FILES["allimg" . $type]["tmp_name"]);
                    $image_type = substr($image_info['mime'], -3);
                    $image_width = $image_info[0];
                    $image_height = $image_info[1];

                    if ($image_width == $image_height) {
                        $etc9 = 1; // 정사각형
                    } else {
                        $etc9 = 2; // 직사각형
                    }
                    $etc8 = $image_width . "*" . $image_height;

                    $basic_img_src = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . "" . $type . ".gif";
                    //echo $basic_img_src;
                    copy($_FILES["allimg" . $type]["tmp_name"], $basic_img_src); // 원본 이미지를 만든다.
                    @chmod($basic_img_src, 0777);
                }
                $chk_allimg = $_POST["chk_allimg" . $type];
                $chk_mimg = $_POST["chk_mimg" . $type];
                $chk_msimg = $_POST["chk_msimg" . $type];
                $chk_simg = $_POST["chk_simg" . $type];
                $chk_cimg = $_POST["chk_cimg" . $type];
            }

        }

        if ($image_info[0] > $image_info[1]) {
            $image_resize_type = "W";
        } else {
            $image_resize_type = "H";
        }

        if ($_POST["watermark"]) {
            require_once "../lib/class.upload.php";

            $s_water_handle = new Upload($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . "" . $type . ".gif");
            $s_water_result = WaterMarkProcess2($s_water_handle, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/", $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/");


            $basic_img_src = $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/basic_watermark_" . $pid . "" . $type . ".gif";
            copy($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/watermark/basic_" . $pid . "" . $type . ".gif", $basic_img_src);
            chmod($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . "" . $type . ".gif", 0777);
            //rmdirr($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/product".$uploaddir."/watermark/");

            $image_type = "gif"; // 워터마크 처리후 마임타입이 gif로 바뀐다.
            //$image_info = getimagesize ($basic_img_src);
            //$image_type = substr($image_info['mime'],-3);
            //echo $image_type;
        }

        $akamaiFtpUploadFiles['basic'] = "basic_" . $pid . ".gif";
        $akamaiFtpUploadFiles['b'] = "b_" . $pid . ".gif";

        if ($image_type == "gif" || $image_type == "GIF") {
            //$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/product".$uploaddir."/basic_".$pid."".$type.".gif";
            //copy($allimg, $basic_img_src); // 원본 이미지를 만든다.
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . ".gif") && $pid) {
                unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . ".gif");
            }

            //copy($allimg, $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/product".$uploaddir."/b_".$pid."".$type.".gif");
            MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . "" . $type . ".gif", MIRROR_NONE);
            if($chk_allimg == 1){
                resize_gif($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . "" . $type . ".gif", $image_info2[0][width], $image_info2[0][height], $image_resize_type);
            }


            if ($chk_mimg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . ".gif") && $pid) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . ".gif");
                }
                MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                resize_gif($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . "" . $type . ".gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);

                $akamaiFtpUploadFiles['m'] = "m_" . $pid . ".gif";
            }

            if ($chk_msimg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . ".gif") && $pid) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . ".gif");
                }
                MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                resize_gif($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . "" . $type . ".gif", $image_info2[2][width], $image_info2[2][height], $image_resize_type);

                $akamaiFtpUploadFiles['ms'] = "ms_" . $pid . ".gif";
            }

            if ($chk_simg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . ".gif") && $pid) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/ss_" . $pid . ".gif");
                }
                MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                resize_gif($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . "" . $type . ".gif", $image_info2[3][width], $image_info2[3][height], $image_resize_type);
                $akamaiFtpUploadFiles['s'] = "s_" . $pid . ".gif";
            }

            if ($chk_cimg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . ".gif") && $pid) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . ".gif");
                }
                MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                resize_gif($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . "" . $type . ".gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);
                $akamaiFtpUploadFiles['c'] = "c_" . $pid . ".gif";
            }
        } else if ($image_type == "png" || $image_type == "PNG") {
            //$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/product".$uploaddir."/basic_".$pid."".$type.".gif";
            //copy($allimg, $basic_img_src); // 원본 이미지를 만든다.
            if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . ".gif") && $pid) {
                unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . ".gif");
            }
            //copy($allimg, $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/product".$uploaddir."/b_".$pid."".$type.".gif");
            MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . "" . $type . ".gif", MIRROR_NONE);
            if($chk_allimg == 1) {
                resize_png($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . "" . $type . ".gif",
                    $image_info2[0][width], $image_info2[0][height], $image_resize_type);
            }
            if ($chk_mimg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . ".gif") && $pid) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . ".gif");
                }
                MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                resize_png($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . "" . $type . ".gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);
                $akamaiFtpUploadFiles['m'] = "m_" . $pid . ".gif";
            }

            if ($chk_msimg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . ".gif") && $pid) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . ".gif");
                }
                MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                resize_png($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . "" . $type . ".gif", $image_info2[2][width], $image_info2[2][height], $image_resize_type);
                $akamaiFtpUploadFiles['ms'] = "ms_" . $pid . ".gif";
            }

            if ($chk_simg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . ".gif") && $pid) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . ".gif");
                }
                MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                resize_png($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . "" . $type . ".gif", $image_info2[3][width], $image_info2[3][height], $image_resize_type);
                $akamaiFtpUploadFiles['s'] = "s_" . $pid . ".gif";
            }

            if ($chk_cimg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . ".gif") && $pid) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . ".gif");
                }
                MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                resize_png($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . "" . $type . ".gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);
                $akamaiFtpUploadFiles['c'] = "c_" . $pid . ".gif";
            }
        } else if ($image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {

            if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . ".gif")) {
                unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . ".gif");
            }

            Mirror($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . ".gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . ".gif", MIRROR_NONE);
            if($chk_allimg == 1) {
                resize_jpg($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . ".gif",
                    $image_info2[0][width], $image_info2[0][height], $image_resize_type);
            }
            if ($chk_mimg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . ".gif")) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . ".gif");
                }
                Mirror($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . ".gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . ".gif", MIRROR_NONE);
                resize_jpg($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . ".gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);

                $akamaiFtpUploadFiles['m'] = "m_" . $pid . ".gif";
            }

            if ($chk_msimg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . ".gif")) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . ".gif");
                }
                Mirror($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . ".gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . ".gif", MIRROR_NONE);
                resize_jpg($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . ".gif", $image_info2[2][width], $image_info2[2][height], $image_resize_type);

                $akamaiFtpUploadFiles['ms'] = "ms_" . $pid . ".gif";
            }

            if ($chk_simg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . ".gif")) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . ".gif");
                }
                Mirror($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . ".gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . ".gif", MIRROR_NONE);
                resize_jpg($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . ".gif", $image_info2[3][width], $image_info2[3][height], $image_resize_type);

                $akamaiFtpUploadFiles['s'] = "s_" . $pid . ".gif";
            }

            if ($chk_cimg == 1) {
                if (file_exists($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . ".gif")) {
                    unlink($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . ".gif");
                }
                Mirror($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/basic_" . $pid . ".gif", $_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . ".gif", MIRROR_NONE);
                resize_jpg($_SERVER["DOCUMENT_ROOT"] . $admin_config[mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . ".gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);

                $akamaiFtpUploadFiles['c'] = "c_" . $pid . ".gif";
            }
        } else {
            //copy($allimg, $basic_img_src);
            //exit;

            //copy($allimg, $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"][mall_data_root]."/images/product".$uploaddir."/b_".$pid."".$type.".gif");
            if (file_exists($basic_img_src)) {
                Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                if($chk_allimg == 1) {
                    resize_jpg($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . "" . $type . ".gif",
                        $image_info2[0][width], $image_info2[0][height], $image_resize_type);
                }
                if ($chk_mimg == 1) {
                    Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                    resize_jpg($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . "" . $type . ".gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);
                }

                if ($chk_msimg == 1) {
                    Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                    resize_jpg($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . "" . $type . ".gif", $image_info2[2][width], $image_info2[2][height], $image_resize_type);
                }

                if ($chk_simg == 1) {
                    Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                    resize_jpg($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . "" . $type . ".gif", $image_info2[3][width], $image_info2[3][height], $image_resize_type);
                }

                if ($chk_cimg == 1) {
                    Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . "" . $type . ".gif", MIRROR_NONE);
                    resize_jpg($_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . "" . $type . ".gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);
                }
            }
        }

    }

    //if ($bimg != "none")
    if ($_FILES["bimg" . $type]["size"] > 0) {
        copy($_FILES["bimg" . $type]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/b_" . $pid . "" . $type . ".gif");
        $akamaiFtpUploadFiles['b'] = "b_" . $pid . ".gif";
    }

    //if ($mimg != "none")
    if ($_FILES["mimg" . $type]["size"] > 0) {
        copy($_FILES["mimg" . $type]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/m_" . $pid . "" . $type . ".gif");
        $akamaiFtpUploadFiles['m'] = "m_" . $pid . ".gif";
    }

    //if ($msimg != "none")
    if ($_FILES["msimg" . $type]["size"] > 0) {
        copy($_FILES["msimg" . $type]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/ms_" . $pid . "" . $type . ".gif");
        $akamaiFtpUploadFiles['ms'] = "ms_" . $pid . ".gif";
    }

    //if ($simg != "none")
    if ($_FILES["simg" . $type]["size"] > 0) {
        copy($_FILES["simg" . $type]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/s_" . $pid . "" . $type . ".gif");
        $akamaiFtpUploadFiles['s'] = "s_" . $pid . ".gif";
    }

    //if ($cimg != "none")
    if ($_FILES["cimg" . $type]["size"] > 0) {
        copy($_FILES["cimg" . $type]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/c_" . $pid . "" . $type . ".gif");
        $akamaiFtpUploadFiles['c'] = "c_" . $pid . ".gif";
    }

    if ($_FILES["appimg" . $type]["size"] > 0) {
        copy($_FILES["appimg" . $type]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/app_" . $pid . "" . $type . ".gif");
        $akamaiFtpUploadFiles['app'] = "app_" . $pid . ".gif";
    }
    if ($_FILES["filter" . $type]["size"] > 0) {
        copy($_FILES["filter" . $type]['tmp_name'], $_SERVER["DOCUMENT_ROOT"] . $_SESSION["admin_config"][mall_data_root] . "/images/product" . $uploaddir . "/filter_" . $pid . "" . $type . ".gif");
        $akamaiFtpUploadFiles['filter'] = "filter_" . $pid . ".gif";
    }

    /*
	$akamaiFtpUploadFiles = array();
	$akamaiFtpUploadFiles[] = "basic_".$pid.".gif";
	$akamaiFtpUploadFiles[] = "b_".$pid.".gif";
	$akamaiFtpUploadFiles[] = "m_".$pid.".gif";
	$akamaiFtpUploadFiles[] = "ms_".$pid.".gif";
	$akamaiFtpUploadFiles[] = "s_".$pid.".gif";
	$akamaiFtpUploadFiles[] = "c_".$pid.".gif";
	$akamaiFtpUploadFiles[] = "app_".$pid.".gif";
	*/
    akamaiFtpUpload($_SESSION["admin_config"]["mall_data_root"] . "/images/product" . $uploaddir, $akamaiFtpUploadFiles);

}

function CopyImage2($pid, $type = "")
{
	global $admin_config, $image_info2, $db;

    $uploaddir		= UploadDirText($_SESSION["admin_config"]["mall_data_root"] . "/images/productNew", $pid, 'Y');
	$adduploaddir	= UploadDirText($_SESSION["admin_config"]["mall_data_root"] . "/images/addimgNew", $pid, 'Y');

	$basicDir		= $_SESSION["admin_config"]["mall_data_root"] . "/images/productNew".$uploaddir;
	$backUpBasicDir = $_SESSION["admin_config"]["mall_data_root"] . "/images/productNew/".$_SESSION['admininfo']['charger_id']."/productNew";

	$addDir			= $_SESSION["admin_config"]["mall_data_root"] . "/images/addimgNew".$adduploaddir;
	$backUpAddDir	= $_SESSION["admin_config"]["mall_data_root"] . "/images/productNew/".$_SESSION['admininfo']['charger_id']."/addimgNew";

	if(!is_dir($backUpBasicDir)){
		mkdir($backUpBasicDir);
		chmod($backUpBasicDir,0777);
	}

	if(!is_dir($backUpAddDir)){
		mkdir($backUpAddDir);
		chmod($backUpAddDir,0777);
	}

	$postCount = 0;

    $time = date('YmdHis', time());

    if($_POST['imgName']){
        foreach($_POST['imgName'] as $key => $val){
            $image_type = substr($_POST['imgName'][$key], -3);
            $image_info = getimagesize($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key]);

            if ($image_info[0] > $image_info[1]) {
                $image_resize_type = "W";
            } else {
                $image_resize_type = "H";
            }

            /*if ($image_type == "gif" || $image_type == "GIF") {
                // 원본이미지
                MirrorGif($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpBasicDir."/basic_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);

                // 리스트이미지
                MirrorGif($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/list_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_gif($backUpAddDir."/list_".$pid."_".$time."_".$key.".gif", $image_info2[0][width], $image_info2[0][height], $image_resize_type);

                // 오버이미지
                MirrorGif($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/over_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_gif($backUpAddDir."/over_".$pid."_".$time."_".$key.".gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);

                // 이미지작은
                MirrorGif($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/slist_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_gif($backUpAddDir."/slist_".$pid."_".$time."_".$key.".gif", $image_info2[2][width], $image_info2[2][height], $image_resize_type);

                // 썸네일이미지
                MirrorGif($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/nail_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_gif($backUpAddDir."/nail_".$pid."_".$time."_".$key.".gif", $image_info2[3][width], $image_info2[3][height], $image_resize_type);

                // 패턴이미지
                MirrorGif($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/patt_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_gif($backUpAddDir."/patt_".$pid."_".$time."_".$key.".gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);

            } else */

            /*if ($image_type == "png" || $image_type == "PNG" || $image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {
                // 원본이미지
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpBasicDir."/basic_".$pid."_".$time."_".$key.".".$image_type, MIRROR_NONE);

                // 리스트이미지
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/list_".$pid."_".$time."_".$key.".".$image_type, MIRROR_NONE);
                resize_png($backUpAddDir."/list_".$pid."_".$time."_".$key.".".$image_type, $image_info2[0][width], $image_info2[0][height], $image_resize_type);

                if (file_exists($addDir."/list_".$_POST['imgComName']."_".$key.".".$image_type)) {
                    unlink($addDir."/list_".$_POST['imgComName']."_".$key.".".$image_type);
                }

                // 오버이미지
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/over_".$pid."_".$time."_".$key.".".$image_type, MIRROR_NONE);
                resize_png($backUpAddDir."/over_".$pid."_".$time."_".$key.".".$image_type, $image_info2[1][width], $image_info2[1][height], $image_resize_type);

                if (file_exists($addDir."/over_".$_POST['imgComName']."_".$key.".".$image_type)) {
                    unlink($addDir."/over_".$_POST['imgComName']."_".$key.".".$image_type);
                }

                // 이미지작은
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/slist_".$pid."_".$time."_".$key.".".$image_type, MIRROR_NONE);
                resize_png($backUpAddDir."/slist_".$pid."_".$time."_".$key.".".$image_type, $image_info2[2][width], $image_info2[2][height], $image_resize_type);

                if (file_exists($addDir."/slist_".$_POST['imgComName']."_".$key.".".$image_type)) {
                    unlink($addDir."/slist_".$_POST['imgComName']."_".$key.".".$image_type);
                }

                // 썸네일이미지
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/nail_".$pid."_".$time."_".$key.".".$image_type, MIRROR_NONE);
                resize_png($backUpAddDir."/nail_".$pid."_".$time."_".$key.".".$image_type, $image_info2[3][width], $image_info2[3][height], $image_resize_type);

                if (file_exists($addDir."/nail_".$_POST['imgComName']."_".$key.".".$image_type)) {
                    unlink($addDir."/nail_".$_POST['imgComName']."_".$key.".".$image_type);
                }

                // 패턴이미지
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/patt_".$pid."_".$time."_".$key.".".$image_type, MIRROR_NONE);
                resize_png($backUpAddDir."/patt_".$pid."_".$time."_".$key.".".$image_type, $image_info2[4][width], $image_info2[4][height], $image_resize_type);

                if (file_exists($addDir."/patt_".$_POST['imgComName']."_".$key.".".$image_type)) {
                    unlink($addDir."/patt_".$_POST['imgComName']."_".$key.".".$image_type);
                }
            } else {
                // 원본이미지
                Mirror($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpBasicDir."/basic_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);

                // 리스트이미지
                Mirror($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/list_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_jpg($backUpAddDir."/list_".$pid."_".$time."_".$key.".gif", $image_info2[0][width], $image_info2[0][height], $image_resize_type);

                if (file_exists($addDir."/list_".$_POST['imgComName']."_".$key.".gif")) {
                    unlink($addDir."/list_".$_POST['imgComName']."_".$key.".gif");
                }

                // 오버이미지
                Mirror($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/over_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_jpg($backUpAddDir."/over_".$pid."_".$time."_".$key.".gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);

                if (file_exists($addDir."/over_".$_POST['imgComName']."_".$key.".gif")) {
                    unlink($addDir."/over_".$_POST['imgComName']."_".$key.".gif");
                }

                // 이미지작은
                Mirror($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/slist_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_jpg($backUpAddDir."/slist_".$pid."_".$time."_".$key.".gif", $image_info2[2][width], $image_info2[2][height], $image_resize_type);

                if (file_exists($addDir."/slist_".$_POST['imgComName']."_".$key.".gif")) {
                    unlink($addDir."/slist_".$_POST['imgComName']."_".$key.".gif");
                }

                // 썸네일이미지
                Mirror($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/nail_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_jpg($backUpAddDir."/nail_".$pid."_".$time."_".$key.".gif", $image_info2[3][width], $image_info2[3][height], $image_resize_type);

                if (file_exists($addDir."/nail_".$_POST['imgComName']."_".$key.".gif")) {
                    unlink($addDir."/nail_".$_POST['imgComName']."_".$key.".gif");
                }

                // 패턴이미지
                Mirror($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/patt_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_jpg($backUpAddDir."/patt_".$pid."_".$time."_".$key.".gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);

                if (file_exists($addDir."/patt_".$_POST['imgComName']."_".$key.".gif")) {
                    unlink($addDir."/patt_".$_POST['imgComName']."_".$key.".gif");
                }
            }*/
            // 원본이미지
            copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpBasicDir."/basic_".$pid."_".$key.".gif");

            //$akamaiFtpUploadFiles['basic'] = "basic_".$pid."_".$key.".gif";

            // 리스트이미지
            if ($image_type == "png" || $image_type == "PNG" || $image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/list_".$pid."_".$key.".gif", MIRROR_NONE);
                resize_png($backUpAddDir."/list_".$pid."_".$key.".".$image_type, $image_info2[0][width], $image_info2[0][height], $image_resize_type);
            }else{
                copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/list_".$pid."_".$key.".gif");
                resize_jpg($backUpAddDir."/list_".$pid."_".$key.".gif", $image_info2[0][width], $image_info2[0][height], $image_resize_type);
            }

            //$akamaiFtpUploadFilesAdd['list'] = "list_".$pid."_".$key.".gif";

            // 오버이미지
            if ($image_type == "png" || $image_type == "PNG" || $image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/over_".$pid."_".$key.".gif", MIRROR_NONE);
                resize_png($backUpAddDir."/over_".$pid."_".$key.".".$image_type, $image_info2[1][width], $image_info2[1][height], $image_resize_type);
            }else{
                copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/over_".$pid."_".$key.".gif");
                resize_jpg($backUpAddDir."/over_".$pid."_".$key.".gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);
            }

            //$akamaiFtpUploadFilesAdd['over'] = "over_".$pid."_".$key.".gif";

            // 이미지작은
            if ($image_type == "png" || $image_type == "PNG" || $image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/slist_".$pid."_".$key.".gif", MIRROR_NONE);
                resize_png($backUpAddDir."/slist_".$pid."_".$key.".".$image_type, $image_info2[2][width], $image_info2[2][height], $image_resize_type);
            }else{
                copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/slist_".$pid."_".$key.".gif");
                resize_jpg($backUpAddDir."/slist_".$pid."_".$key.".gif", $image_info2[2][width], $image_info2[2][height], $image_resize_type);
            }

            //$akamaiFtpUploadFilesAdd['slist'] = "slist_".$pid."_".$key.".gif";

            // 썸네일이미지
            if ($image_type == "png" || $image_type == "PNG" || $image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/nail_".$pid."_".$key.".gif", MIRROR_NONE);
                resize_png($backUpAddDir."/nail_".$pid."_".$key.".".$image_type, $image_info2[3][width], $image_info2[3][height], $image_resize_type);
            }else{
                copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key],	$backUpAddDir."/nail_".$pid."_".$key.".gif");
                resize_jpg($backUpAddDir."/nail_".$pid."_".$key.".gif", $image_info2[3][width], $image_info2[3][height], $image_resize_type);
            }

            //$akamaiFtpUploadFilesAdd['nail'] = "nail_".$pid."_".$key.".gif";

            // 패턴이미지
            if ($image_type == "png" || $image_type == "PNG" || $image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/patt_".$pid."_".$key.".gif", MIRROR_NONE);
                resize_png($backUpAddDir."/patt_".$pid."_".$key.".".$image_type, $image_info2[4][width], $image_info2[4][height], $image_resize_type);
            }else{
                copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/patt_".$pid."_".$key.".gif");
                resize_jpg($backUpAddDir."/patt_".$pid."_".$key.".gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);
            }

            //$akamaiFtpUploadFilesAdd['patt'] = "patt_".$pid."_".$key.".gif";

            /*if (file_exists($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key])) {
                unlink($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key]);
            }*/

            if (file_exists($_SESSION["admin_config"]["mall_data_root"] . "/images/productNew/".$_SESSION['admininfo']['charger_id']."/".$_POST['imgName'][$key])) {
                unlink($_SESSION["admin_config"]["mall_data_root"] . "/images/productNew/".$_SESSION['admininfo']['charger_id']."/".$_POST['imgName'][$key]);
            }
            $postCount++;
        }
    }

	$backUpBasicHandle = opendir($backUpBasicDir);
	@mkdir($basicDir);
	while(false !== ($basicFile = readdir($backUpBasicHandle))){
		if(is_file($backUpBasicDir . "/" . $basicFile)){
			copy($backUpBasicDir . "/" . $basicFile, $basicDir . "/" . $basicFile);
			unlink($backUpBasicDir . "/" . $basicFile);
		}
	}
	closedir($backUpBasicHandle);

	$backUpAddHandle = opendir($backUpAddDir);
	@mkdir($addDir);
	while(false !== ($addFile = readdir($backUpAddHandle))){
		if(is_file($backUpAddDir . "/" . $addFile)){
			copy($backUpAddDir . "/" . $addFile, $addDir . "/" . $addFile);
			unlink($backUpAddDir . "/" . $addFile);
		}
	}
	closedir($backUpAddHandle);

	//akamaiFtpUpload($_SESSION["admin_config"]["mall_data_root"]."/images/productNew".$uploaddir, $akamaiFtpUploadFiles);
	//akamaiFtpUpload($_SESSION["admin_config"]["mall_data_root"]."/images/addimgNew".$adduploaddir, $akamaiFtpUploadFilesAdd);

	$handle  = opendir($basicDir); // 디렉토리 open

	$eleCount = 0;

	// 디렉토리의 파일을 저장
	while (false !== ($filename = readdir($handle))) {
		// 파일인 경우만 목록에 추가한다.
		if(is_file($basicDir . "/" . $filename)){
			$eleCount++;
		}
	}

	closedir($handle); // 디렉토리 close

	if($postCount < $eleCount){
		for($delImgNum = $postCount;$delImgNum < $eleCount;$delImgNum++){

			if (file_exists($basicDir."/basic_".$pid."_".$delImgNum.".gif")) {
				unlink($basicDir."/basic_".$pid."_".$delImgNum.".gif");
			}

            /*if (file_exists($basicDir."/basic_".$_POST['imgComName']."_".$delImgNum.".jpe")) {
                unlink($basicDir."/basic_".$_POST['imgComName']."_".$delImgNum.".jpe");
            }

            if (file_exists($basicDir."/basic_".$_POST['imgComName']."_".$delImgNum.".png")) {
                unlink($basicDir."/basic_".$_POST['imgComName']."_".$delImgNum.".png");
            }*/

			if (file_exists($addDir."/list_".$pid."_".$delImgNum.".gif")) {
				unlink($addDir."/list_".$pid."_".$delImgNum.".gif");
			}

            /*if (file_exists($basicDir."/list_".$_POST['imgComName']."_".$delImgNum.".jpe")) {
                unlink($basicDir."/list_".$_POST['imgComName']."_".$delImgNum.".jpe");
            }

            if (file_exists($basicDir."/list_".$_POST['imgComName']."_".$delImgNum.".png")) {
                unlink($basicDir."/list_".$_POST['imgComName']."_".$delImgNum.".png");
            }*/

			if (file_exists($addDir."/over_".$pid."_".$delImgNum.".gif")) {
				unlink($addDir."/over_".$pid."_".$delImgNum.".gif");
			}

            /*if (file_exists($basicDir."/over_".$_POST['imgComName']."_".$delImgNum.".jpe")) {
                unlink($basicDir."/over_".$_POST['imgComName']."_".$delImgNum.".jpe");
            }

            if (file_exists($basicDir."/over_".$_POST['imgComName']."_".$delImgNum.".png")) {
                unlink($basicDir."/over_".$_POST['imgComName']."_".$delImgNum.".png");
            }*/

			if (file_exists($addDir."/slist_".$pid."_".$delImgNum.".gif")) {
				unlink($addDir."/slist_".$pid."_".$delImgNum.".gif");
			}

            /*if (file_exists($basicDir."/slist_".$_POST['imgComName']."_".$delImgNum.".jpe")) {
                unlink($basicDir."/slist_".$_POST['imgComName']."_".$delImgNum.".jpe");
            }

            if (file_exists($basicDir."/slist_".$_POST['imgComName']."_".$delImgNum.".png")) {
                unlink($basicDir."/slist_".$_POST['imgComName']."_".$delImgNum.".png");
            }*/

			if (file_exists($addDir."/nail_".$pid."_".$delImgNum.".gif")) {
				unlink($addDir."/nail_".$pid."_".$delImgNum.".gif");
			}

            /*if (file_exists($basicDir."/nail_".$_POST['imgComName']."_".$delImgNum.".jpe")) {
                unlink($basicDir."/nail_".$_POST['imgComName']."_".$delImgNum.".jpe");
            }

            if (file_exists($basicDir."/nail_".$_POST['imgComName']."_".$delImgNum.".png")) {
                unlink($basicDir."/nail_".$_POST['imgComName']."_".$delImgNum.".png");
            }*/

			if (file_exists($addDir."/patt_".$pid."_".$delImgNum.".gif")) {
				unlink($addDir."/patt_".$pid."_".$delImgNum.".gif");
			}

            /*if (file_exists($basicDir."/patt_".$_POST['imgComName']."_".$delImgNum.".jpe")) {
                unlink($basicDir."/patt_".$_POST['imgComName']."_".$delImgNum.".jpe");
            }

            if (file_exists($basicDir."/patt_".$_POST['imgComName']."_".$delImgNum.".png")) {
                unlink($basicDir."/patt_".$_POST['imgComName']."_".$delImgNum.".png");
            }*/
		}
	}
}

function CopyImage3($pid, $type = "")
{
    global $admin_config, $image_info2, $db;

    $addQaDir = "";
    if($admin_config['mall_domain'] == "0925admintest.barrelmade.co.kr"){
        $addQaDir = "/QA";
    }

    $uploaddir		= UploadDirText($_SESSION["admin_config"]["mall_data_root"] . "/images/productNew" . $addQaDir, $pid, 'Y');
    $adduploaddir	= UploadDirText($_SESSION["admin_config"]["mall_data_root"] . "/images/addimgNew" . $addQaDir, $pid, 'Y');

    $basicDir		= $_SESSION["admin_config"]["mall_data_root"] . "/images/productNew".$addQaDir.$uploaddir;
    $backUpBasicDir = $_SESSION["admin_config"]["mall_data_root"] . "/images/productNew/".$_SESSION['admininfo']['charger_id']."/productNew";

    $addDir			= $_SESSION["admin_config"]["mall_data_root"] . "/images/addimgNew".$addQaDir.$adduploaddir;
    $backUpAddDir	= $_SESSION["admin_config"]["mall_data_root"] . "/images/productNew/".$_SESSION['admininfo']['charger_id']."/addimgNew";

    if(!is_dir($backUpBasicDir)){
        mkdir($backUpBasicDir);
        chmod($backUpBasicDir,0777);
    }

    if(!is_dir($backUpAddDir)){
        mkdir($backUpAddDir);
        chmod($backUpAddDir,0777);
    }

    $postCount = 0;

    $time = date('YmdHis', time());

    if($_POST['imgName']){
        foreach($_POST['imgName'] as $key => $val){
            $image_type = substr($_POST['imgName'][$key], -3);
            $image_info = getimagesize($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key]);

            if ($image_info[0] > $image_info[1]) {
                $image_resize_type = "W";
            } else {
                $image_resize_type = "H";
            }

            // 원본이미지
            copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpBasicDir."/basic_".$pid."_".$time."_".$key.".gif");

            // 리스트이미지
            if ($image_type == "png" || $image_type == "PNG" || $image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/list_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_png($backUpAddDir."/list_".$pid."_".$time."_".$key.".".$image_type, $image_info2[0][width], $image_info2[0][height], $image_resize_type);
            }else{
                copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/list_".$pid."_".$time."_".$key.".gif");
                resize_jpg($backUpAddDir."/list_".$pid."_".$time."_".$key.".gif", $image_info2[0][width], $image_info2[0][height], $image_resize_type);
            }

            // 오버이미지
            if ($image_type == "png" || $image_type == "PNG" || $image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/over_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_png($backUpAddDir."/over_".$pid."_".$time."_".$key.".".$image_type, $image_info2[1][width], $image_info2[1][height], $image_resize_type);
            }else{
                copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/over_".$pid."_".$time."_".$key.".gif");
                resize_jpg($backUpAddDir."/over_".$pid."_".$time."_".$key.".gif", $image_info2[1][width], $image_info2[1][height], $image_resize_type);
            }

            // 이미지작은
            if ($image_type == "png" || $image_type == "PNG" || $image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/slist_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_png($backUpAddDir."/slist_".$pid."_".$time."_".$key.".".$image_type, $image_info2[2][width], $image_info2[2][height], $image_resize_type);
            }else{
                copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/slist_".$pid."_".$time."_".$key.".gif");
                resize_jpg($backUpAddDir."/slist_".$pid."_".$time."_".$key.".gif", $image_info2[2][width], $image_info2[2][height], $image_resize_type);
            }

            // 썸네일이미지
            if ($image_type == "png" || $image_type == "PNG" || $image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/nail_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_png($backUpAddDir."/nail_".$pid."_".$time."_".$key.".".$image_type, $image_info2[3][width], $image_info2[3][height], $image_resize_type);
            }else{
                copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key],	$backUpAddDir."/nail_".$pid."_".$time."_".$key.".gif");
                resize_jpg($backUpAddDir."/nail_".$pid."_".$time."_".$key.".gif", $image_info2[3][width], $image_info2[3][height], $image_resize_type);
            }

            // 패턴이미지
            if ($image_type == "png" || $image_type == "PNG" || $image_type == "jpg" || $image_type == "JPG" || $image_type == "jpeg" || $image_type == "JPEG") {
                MirrorPNG($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/patt_".$pid."_".$time."_".$key.".gif", MIRROR_NONE);
                resize_png($backUpAddDir."/patt_".$pid."_".$time."_".$key.".".$image_type, $image_info2[4][width], $image_info2[4][height], $image_resize_type);
            }else{
                copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpAddDir."/patt_".$pid."_".$time."_".$key.".gif");
                resize_jpg($backUpAddDir."/patt_".$pid."_".$time."_".$key.".gif", $image_info2[4][width], $image_info2[4][height], $image_resize_type);
            }

            // 신규 등록된 백업 이미지는 삭제한다.
            if (file_exists($_SESSION["admin_config"]["mall_data_root"] . "/images/productNew/".$_SESSION['admininfo']['charger_id']."/".$_POST['imgName'][$key])) {
                unlink($_SESSION["admin_config"]["mall_data_root"] . "/images/productNew/".$_SESSION['admininfo']['charger_id']."/".$_POST['imgName'][$key]);
            }
            $postCount++;
        }
    }

    // 기존 상품기본 이미지 폴더의 이미지를 일괄 삭제.(productNew 폴더)
    $handle  = opendir($basicDir); // 디렉토리 open

    // 디렉토리의 파일을 전체 삭제.
    while (false !== ($filename = readdir($handle))) {
        // 파일인 경우만 목록에 추가한다.
        if(is_file($basicDir . "/" . $filename)){
            unlink($basicDir . "/" . $filename);
        }
    }

    closedir($handle); // 디렉토리 close
    // // 기존 상품기본 이미지 폴더의 이미지를 일괄 삭제.(productNew 폴더)

    //복사한 상품기본 이미지를 설정된 이미지 폴더로 복사. 복사된 이미지는 삭제.
    $backUpBasicHandle = opendir($backUpBasicDir);
    @mkdir($basicDir);
    while(false !== ($basicFile = readdir($backUpBasicHandle))){
        if(is_file($backUpBasicDir . "/" . $basicFile)){
            copy($backUpBasicDir . "/" . $basicFile, $basicDir . "/" . $basicFile);
            unlink($backUpBasicDir . "/" . $basicFile);
        }
    }
    closedir($backUpBasicHandle);
    // //복사한 상품기본 이미지를 설정된 이미지 폴더로 복사. 복사된 이미지는 삭제.


    // 기존 상품 리사이징이미지 폴더의 이미지를 일괄 삭제.(addimgNew 폴더)
    $addHandle  = opendir($addDir); // 디렉토리 open

    // 디렉토리의 파일을 전체 삭제.
    while (false !== ($filename = readdir($addHandle))) {
        // 파일인 경우만 목록에 추가한다.
        if(is_file($addDir . "/" . $filename)){
            unlink($addDir . "/" . $filename);
        }
    }

    closedir($addHandle); // 디렉토리 close
    // // 기존 상품 리사이징이미지 폴더의 이미지를 일괄 삭제.(addimgNew 폴더)

    //복사한 리사이징이미지 이미지를 설정된 리사이징이미지 이미지 폴더로 복사. 복사된 이미지는 삭제.
    $backUpAddHandle = opendir($backUpAddDir);
    @mkdir($addDir);
    while(false !== ($basicFile = readdir($backUpAddHandle))){
        if(is_file($backUpAddDir . "/" . $basicFile)){
            copy($backUpAddDir . "/" . $basicFile, $addDir . "/" . $basicFile);
            unlink($backUpAddDir . "/" . $basicFile);
        }
    }
    closedir($backUpAddHandle);
    // //복사한 리사이징이미지 이미지를 설정된 리사이징이미지 이미지 폴더로 복사. 복사된 이미지는 삭제.
}

?>
