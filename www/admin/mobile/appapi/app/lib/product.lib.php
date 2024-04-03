<?php

	include_once ($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	include_once ($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
	include_once ($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
	include_once ($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");
    include_once ($_SERVER["DOCUMENT_ROOT"]."/admin/lib/imageResize.lib.php");
	@include_once ($_SERVER["DOCUMENT_ROOT"]."/admin/product/goods.options.lib.php");

    $db = new Database;

    $sql = "select mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
    $db->query($sql);
    $db->fetch();

    $admin_config[mall_data_root] = $db->dt[mall_data_root];
    $admin_config[admin_level] = 9;
    $admin_config[language] = 'korea';
    $admin_config[mall_type] = $db->dt[mall_type];

    //상품등록
    function insertproduct($itemdata,$userdata){

        global $db,$admin_config, $app_name;

        //echo "app_name : ".$app_name;
        //exit;

        if(!file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product/")){
            mkdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product/");
            chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product/",0777);
        }

        if(!file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/")){
            mkdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/");
            chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/",0777);
        }

        //DEFAULT

        $pname = urldecode($itemdata->pname);

        if($userdata->mem_type=="MD" || $userdata->mem_type=="A" ){
            $coprice = $itemdata->coprice;
            $wholesale_price = $itemdata->wholesale_price;
            $wholesale_sellprice = $wholesale_price;
            $listprice = round($wholesale_price*1.7,-2);
            $sellprice = $listprice;
        }else{
            if($app_name == "discountmarket"){
                $coprice = $itemdata->wholesale_price; // 당분간 마진이 없으므로 판매가를 공급가로 넣어줌
                $wholesale_price = $itemdata->coprice; // 원가 자체가 도매가를 의미함;
                $wholesale_sellprice = $itemdata->wholesale_price;//$wholesale_price;
                $listprice = round($wholesale_price*1.7,-2);
                $sellprice = $listprice;
            }else{
                $coprice = $itemdata->coprice;
                $wholesale_price = $coprice+2000;
                $wholesale_sellprice = $wholesale_price;
                $listprice = round($wholesale_price*1.7,-2);
                $sellprice = $listprice;
            }
        }

        $origin = urldecode($itemdata->origin);
        //$material = urldecode($itemdata->material);

        if(!$stock)		$stock="9999";

        if($itemdata->delivery_policy=='Y'){//상품개별배송사용이면 Y
            $delivery_policy = "2";  //상품개별정책 2:사용
            if($itemdata->delivery_price > 0){
                $delivery_product_policy="1";// 1:유료배송
                $delivery_price=$itemdata->delivery_price;
                $delivery_package="Y";
            }else{
                $delivery_product_policy="3";// 1:무료배송
                $delivery_price=0;
                $delivery_package="";
            }
        }else{
            $delivery_policy = "1"; //상품개별정책 1:사용안함
            $delivery_product_policy="3";//3:무료배송
            $delivery_price=0;
            $delivery_package="";
        }

        //$search_keyword = urldecode($itemdata->search_keyword);

        /*
        if($itemdata->sns_facebook=="Y")		$sns_btn[btn_use1]="facebook";
        if($itemdata->sns_twitter=="Y")			$sns_btn[btn_use2]="twitter";

        if($itemdata->sns_facebook=="Y"||$itemdata->sns_twitter=="Y")			$sns_btn_yn="Y";
        else																							$sns_btn_yn="";

        $sns_btn = serialize($sns_btn);
        */

        $product_type="0";
        $state = "1";
        if($itemdata->disp=="1"){
            $disp = "1";
        }else{
            $disp = "0";
        }
        $style = $itemdata->style;

        $reg_category = "Y";
        $wholesale_reserve_yn = "N";// NEWDEV 에는 없음
        $reserve_yn = "N";
        $surtax_yorn="N";
        $delivery_company = "MI";
        $one_commission ="N";
        $cupon_use_yn = "Y";
        $stock_use_yn = "Q";
        $free_delivery_yn ="N";
        //$sample_type = 3;

        $db->query("SELECT max(vieworder)+1 as max_vieworder FROM ".TBL_SHOP_PRODUCT." ");
        $db->fetch();
        $vieworder = $db->dt[max_vieworder];

        if($pname){

            if($_SERVER["HTTP_HOST"]=="dev.forbiz.co.kr"){

                $sql = "INSERT INTO ".TBL_SHOP_PRODUCT."
                            (id,  pname,pcode, style, company,  shotinfo,  buyingservice_coprice, wholesale_price, wholesale_sellprice, listprice,sellprice,  coprice,wholesale_reserve_yn, reserve_yn,  state, disp,product_type,  vieworder, admin, stock, safestock, search_keyword,reg_category,  surtax_yorn,delivery_company,one_commission,cupon_use_yn,stock_use_yn, delivery_policy,free_delivery_yn,
                            origin, regdate,reg_charger_ix,reg_charger_name,delivery_product_policy,delivery_price,delivery_package,sns_btn_yn,sns_btn, sample_type)
                            values('', '".strip_tags(trim($pname))."','$pcode','$style', '$company', '$shotinfo', '$buyingservice_coprice','$wholesale_price','$wholesale_sellprice','$listprice','$sellprice', '$coprice','$wholesale_reserve_yn', '$reserve_yn', $state, '$disp','$product_type', '$vieworder', '".$userdata->company_id."','$stock','10','$search_keyword','$reg_category','$surtax_yorn','$delivery_company','$one_commission','$cupon_use_yn','$stock_use_yn','$delivery_policy','$free_delivery_yn',
                            '$origin',NOW(),'".$userdata->code."','".$userdata->name."(".$userdata->id.")','$delivery_product_policy','$delivery_price','$delivery_package','$sns_btn_yn','$sns_btn','$sample_type') ";
            }else{

                //NEWDEV 전용 쿼리
                /*
                $sql = "INSERT INTO ".TBL_SHOP_PRODUCT."
                            (id,  pname,pcode, style, company, paper_pname,  shotinfo,  buyingservice_coprice, wholesale_price, wholesale_sellprice, listprice,sellprice,  coprice, reserve_yn,  state, disp,product_type,  vieworder, admin, stock, safestock, search_keyword,reg_category,  surtax_yorn,delivery_company,one_commission,stock_use_yn, delivery_policy,free_delivery_yn,
                            origin, material, regdate,delivery_product_policy,delivery_price,delivery_package,sns_btn_yn,sns_btn, sample_type)
                            values('', '".strip_tags(trim($pname))."','$pcode','$style', '$company', '".strip_tags(trim($pname))."', '$shotinfo', '$buyingservice_coprice','$wholesale_price','$wholesale_sellprice','$listprice','$sellprice', '$coprice', '$reserve_yn', $state, '$disp','$product_type', '$vieworder', '".$userdata->company_id."','$stock','10','$search_keyword','$reg_category','$surtax_yorn','$delivery_company','$one_commission','$stock_use_yn','$delivery_policy','$free_delivery_yn',
                            '$origin','$material',NOW(),'$delivery_product_policy','$delivery_price','$delivery_package','$sns_btn_yn','$sns_btn','$sample_type') ";
                */
                $sql = "INSERT INTO ".TBL_SHOP_PRODUCT."
                            (id,  pname,pcode, style, company, paper_pname,  shotinfo,  buyingservice_coprice, wholesale_price, wholesale_sellprice, listprice,sellprice,  coprice, reserve_yn,  state, disp,product_type,  vieworder, admin, stock, safestock, search_keyword,reg_category,  surtax_yorn,delivery_company,one_commission,stock_use_yn, delivery_policy,free_delivery_yn,
                            origin, regdate,delivery_product_policy,delivery_price,delivery_package,sns_btn_yn,sns_btn)
                            values('', '".strip_tags(trim($pname))."','$pcode','$style', '$company', '".strip_tags(trim($pname))."', '$shotinfo', '$buyingservice_coprice','$wholesale_price','$wholesale_sellprice','$listprice','$sellprice', '$coprice', '$reserve_yn', $state, '$disp','$product_type', '$vieworder', '".$userdata->company_id."','$stock','10','$search_keyword','$reg_category','$surtax_yorn','$delivery_company','$one_commission','$stock_use_yn','$delivery_policy','$free_delivery_yn',
                            '$origin',NOW(),'$delivery_product_policy','$delivery_price','$delivery_package','$sns_btn_yn','$sns_btn') ";
            }

            $db->sequences = "SHOP_GOODS_SEQ";
            $db->query($sql);

            if($db->dbms_type == "oracle"){
                $pid = $db->last_insert_id;
            }else{
                $db->query("SELECT id FROM ".TBL_SHOP_PRODUCT." WHERE id=LAST_INSERT_ID()");
                $db->fetch();
                $pid = $db->dt[id];
            }

            if(empty($pcode)){
                $sql = "UPDATE ".TBL_SHOP_PRODUCT." SET pcode = '".$pid."' WHERE id='".$pid."'";
                //echo $sql;
                $db->query($sql);
            }

            /*
            $sql = "INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, listprice, sellprice, coprice, wholesale_price, wholesale_sellprice, reserve,  admin,regdate) ";
            $sql = $sql." values('', '$pid','$listprice','$sellprice', '$coprice', '$wholesale_price', '$wholesale_sellprice', '$reserve',  '".$admininfo[company_id]."',NOW()) ";
            */
            $sql = "INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, listprice, sellprice, coprice, wholesale_price, wholesale_sellprice, reserve,regdate) ";
            $sql = $sql." values('', '$pid','$listprice','$sellprice', '$coprice', '$wholesale_price', '$wholesale_sellprice', '$reserve',NOW()) ";

            $db->query($sql);

            //오라클에서 unix_timestamp는 FUNCTION으로 만듬 FUNCTION은 맨아래에 주석처리 해놓음 2013-03-22 홍진영
            $db->query("UPDATE ".TBL_SHOP_PRODUCT." SET regdate_desc = unix_timestamp(regdate)*-1 WHERE id='$pid'");

            return $pid;
        }else{
            return false;
        }
    }

    function insertproductoption($itemdata,$userdata,$pid){
        global $db;

        //$options =  json_decode('[{"opt_name":"벽걸이","opt_count":"1"},{"opt_name":"확장리모콘","opt_count":"2"}]', true);
        $options =  json_decode(str_replace("\\","",$itemdata->option),true);

        //옵션 구조 만들어주기
        $stock_options["option_name"] = $itemdata->option_name;//"색상/사이즈";
        $stock_options["option_kind"] = $itemdata->option_kind;//"b";
        $stock_options["option_use"] = "1";
        $stock_options["option_type"] = "9";


        if($itemdata->option_kind == "b"){
            if($userdata->mem_type=="MD"){
                $coprice = $itemdata->coprice;
                $wholesale_price = $itemdata->wholesale_price;
                $wholesale_listprice = $wholesale_price;
                $listprice = round($wholesale_price*1.7,-2);
                $sellprice = $listprice;
            }else{
                $coprice = $itemdata->coprice;
                $wholesale_price = $coprice+2000;
                $wholesale_listprice = $wholesale_price;
                $listprice = round($wholesale_price*1.7,-2);
                $sellprice = $listprice;
            }
        }else{
            $coprice = 0;
            $wholesale_0;
            $wholesale_listprice = 0;
            $listprice = 0;
            $sellprice = 0;
        }




        for($i=0;$i<count($options);$i++){
            if(urldecode($options[$i]["opt_name"]) !=""){
                $stock_options["details"][$i]["option_div"] = urldecode($options[$i]["opt_name"]);
                $stock_options["details"][$i]["stock"] = $options[$i]["opt_count"];
                $stock_options["details"][$i]["coprice"] = $coprice;
                $stock_options["details"][$i]["listprice"] = $listprice;
                $stock_options["details"][$i]["sellprice"] = $sellprice;
                $stock_options["details"][$i]["wholesale_listprice"] = $wholesale_listprice;
                $stock_options["details"][$i]["wholesale_price"] = $wholesale_price;
            }
        }

        //dev 에는 있지만 newdev(임대형)에게는 없어서 처리


        OptionUpdate($db, $pid, $stock_options,$itemdata->option_kind);

    }

    if (!function_exists('OptionUpdate')) {
        function OptionUpdate($mdb, $pid, $_options, $option_kind="x"){

            if($_options["option_name"]){
                if($_options["opn_ix"]){
                    $mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and opn_ix = '".trim($_options["opn_ix"])."' and option_kind = '".$_options["option_kind"]."' ");
                }else{
                    $mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($_options["option_name"])."' and option_kind = '".$_options["option_kind"]."' ");
                }

                if($_options["option_use"]){
                    $_options_use = $_options["option_use"];
                }else{
                    $_options_use = 0;
                }

                if($mdb->total){
                    $mdb->fetch();
                    $opn_ix = $mdb->dt[opn_ix];

                    //,box_total='".$_options["box_total"]."'
                    $sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
                                                option_name='".trim($_options["option_name"])."', 
                                                option_kind='".$_options["option_kind"]."', 
                                                option_type='".$_options["option_type"]."',
                                                option_use='".($_options["option_use"] == "" ? "0":"1")."'
                                                where opn_ix = '".$opn_ix."' ";
                    $mdb->query($sql);
                }else{
                    /*
                    $sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, box_total, regdate)
                                    VALUES
                                    ('','$pid','".$_options["option_name"]."','".$_options["option_kind"]."','".$_options["option_type"]."','".($_options["option_use"] == "" ? "0":"1")."','".$_options["box_total"]."',NOW())";
                    */
                    $sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate)
                                                VALUES
                                                ('','$pid','".$_options["option_name"]."','".$_options["option_kind"]."','".$_options["option_type"]."','".($_options["option_use"] == "" ? "0":"1")."',NOW())";
                    $mdb->sequences = "SHOP_GOODS_OPTIONS_SEQ";

                    $mdb->query($sql);


                    if($mdb->dbms_type == "oracle"){
                        $opn_ix = $mdb->last_insert_id;
                    }else{
                        $mdb->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
                        $mdb->fetch();
                        $opn_ix = $mdb->dt[opn_ix];
                    }
                }

                $sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N' where opn_ix='".$opn_ix."' ";

                $mdb->query($sql);
                $option_stock_yn = "";
                for($i=0;$i < count($_options["details"]);$i++){
                    $_option_detail = $_options["details"][$i];

                    if($_option_detail[option_div]){

                        $mdb->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($_option_detail[option_div])."' and opn_ix = '".$opn_ix."' ");

                        if($mdb->total){
                            $mdb->fetch();
                            $opd_ix = $mdb->dt[id];
                            /*
                                option_listprice='".$_option_detail[listprice]."',
                                option_wholesale_listprice='".$_option_detail[wholesale_listprice]."',
                                option_wholesale_price='".$_option_detail[wholesale_price]."',
                                option_soldout='".$_option_detail[soldout]."' ,
                                option_barcode='".$_option_detail[barcode]."' ,
                            */
                            $sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
                                                        option_div='".$_option_detail[option_div]."',
                                                        option_code='".$_option_detail[code]."',
                                                        option_coprice='".$_option_detail[coprice]."',
                                                        option_price='".$_option_detail[sellprice]."',
                                                        option_wsprice='".$_option_detail[wholesale_price]."',
                                                        option_stock='".$_option_detail[stock]."',
                                                        option_safestock='".$_option_detail[safestock]."' ,
                                                        insert_yn='Y'
                                                        where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";

                        }else{
                            /*
                            $sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL."
                                        (id, pid, opn_ix, option_div, option_code,option_coprice,option_listprice, option_price, option_wholesale_listprice,  option_wholesale_price, option_stock, option_safestock, option_soldout, option_barcode, insert_yn, regdate)
                                        values
                                        ('','$pid','$opn_ix','".$_option_detail[option_div]."','".$_option_detail[code]."','".$_option_detail[coprice]."','".$_option_detail[listprice]."','".$_option_detail[sellprice]."','".$_option_detail[wholesale_listprice]."','".$_option_detail[wholesale_price]."','".$_option_detail[stock]."','".$_option_detail[safestock]."','".$_option_detail[soldout]."', '".$_option_detail[barcode]."' ,'Y', NOW()) ";
                            */
                            $sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." 
                                                        (id, pid, opn_ix, option_div, option_code,option_coprice, option_price, option_wsprice, option_stock, option_safestock, is_bundle, bundle_amount, insert_yn) 
                                                        values
                                                        ('','$pid','$opn_ix','".$_option_detail[option_div]."','".$_option_detail[code]."','".$_option_detail[coprice]."','".$_option_detail[sellprice]."','".$_option_detail[wholesale_price]."','".$_option_detail[stock]."','".$_option_detail[safestock]."','S',1,'Y') ";

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
                }

                $sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
                //echo $sql;
                $mdb->query($sql);
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

            }else{

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
    }


    function insertproductcategory($itemdata,$pid){
        global $db;

        $cid = $itemdata->cid;
        $sql = "insert into shop_product_relation (rid, cid, pid, disp, basic,insert_yn, regdate ) values ('','".$cid."','".$pid."','1','1','Y',NOW())";
        $db->sequences = "SHOP_GOODS_LINK_SEQ";
        $db->query($sql);

    }

    function copyproductimage($itemdata,$pid,$app_version,$RequestAppType){
        global $db,$admin_config;

        $db->query("select * from shop_image_resizeinfo order by idx");
        $image_info2 = $db->fetchall();

        $uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $pid, 'Y');
        $adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $pid, 'Y');

        $uploadpath = $_SERVER["DOCUMENT_ROOT"].'/admin/mobile/appapi/app/tmp/';

        for($i = 0; $i < 7; $i++){
            if($i == 0){
                $file = "basicimgfile";
            }else{
                $file = "imgfile" . $i;
            }

            //app_version ex> 1.0.30

            /*
            if($RequestAppType == "android"){
                if( ! empty($_FILES['basicimgfile'])){
                    $file="basicimgfile";
                }else{
                    $file="imgfile1";
                }
            }else{
                $file="basicimgfile";
            }
            */

            $afile = $_FILES[$file];

            $filename = $afile['name'];
            $imgfile = $uploadpath . $filename;

            /*
            syslog(1, "================= START IN Image Main ====================");
            syslog(1, "file = " . $file);
            syslog(1, "afile = " . print_r($afile,true));
            syslog(1, print_r($_FILES[$file],true));
            syslog(1, "================= END IN Image Main ====================");
            */

            if( $afile['size'] > 0 ) {

                if(file_exists($imgfile)) {
                    unlink($imgfile);
                }

                copy($afile['tmp_name'], $imgfile);
                //exec('chmod -R 777 ' . $imgfile);

                if($file == 'basicimgfile'){ //기본이미지일때
                //if(true){ //기본이미지일때

                    $image_info = getimagesize ($imgfile);
                    $image_type = substr($image_info['mime'],-3);

                    if($image_info[0] > $image_info[1]){
                        $image_resize_type = "W";
                    }else{
                        $image_resize_type = "H";
                    }

                    $basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif";
                    copy($imgfile, $basic_img_src); // 원본 이미지를 만든다.
                    //chmod($basic_img_src,0777);

                    if($image_type == "gif" || $image_type == "GIF"){
                        MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif", MIRROR_NONE);
                        resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif",$image_info2[0][width],$image_info2[0][height],$image_resize_type);

                        MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif", MIRROR_NONE);
                        resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif",$image_info2[1][width],$image_info2[1][height],$image_resize_type);

                        MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif", MIRROR_NONE);
                        resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif",$image_info2[2][width],$image_info2[2][height],$image_resize_type);

                        MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif", MIRROR_NONE);
                        resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif",$image_info2[3][width],$image_info2[3][height],$image_resize_type);

                        MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif", MIRROR_NONE);
                        resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif",$image_info2[4][width],$image_info2[4][height],$image_resize_type);

                    }else if($image_type == "png" || $image_type == "PNG"){

                        MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif", MIRROR_NONE);
                        resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif",$image_info2[0][width],$image_info2[0][height],$image_resize_type);

                        MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif", MIRROR_NONE);
                        resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif",$image_info2[1][width],$image_info2[1][height],$image_resize_type);

                        MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif", MIRROR_NONE);
                        resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif",$image_info2[2][width],$image_info2[2][height],$image_resize_type);

                        MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif", MIRROR_NONE);
                        resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif",$image_info2[3][width],$image_info2[3][height],$image_resize_type);

                        MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif", MIRROR_NONE);
                        resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif",$image_info2[4][width],$image_info2[4][height],$image_resize_type);

                    }else{

                        if (file_exists($basic_img_src)){

                            Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif", MIRROR_NONE);
                            resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$pid.".gif",$image_info2[0][width],$image_info2[0][height],$image_resize_type);

                            Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif", MIRROR_NONE);
                            resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$pid.".gif",$image_info2[1][width],$image_info2[1][height],$image_resize_type);

                            Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif", MIRROR_NONE);
                            resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$pid.".gif",$image_info2[2][width],$image_info2[2][height],$image_resize_type);

                            Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif", MIRROR_NONE);
                            resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$pid.".gif",$image_info2[3][width],$image_info2[3][height],$image_resize_type);

                            Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif", MIRROR_NONE);
                            resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$pid.".gif",$image_info2[4][width],$image_info2[4][height],$image_resize_type);

                        }
                    }


                }else{//추가이미지

                    $sql = "INSERT INTO ".TBL_SHOP_ADDIMAGE." (id, pid, deepzoom, regdate) values('', '$pid','0',  NOW()) ";
                    $db->sequences = "SHOP_ADDIMAGE_SEQ";
                    $db->query($sql);

                    if($db->dbms_type == "oracle"){
                        $ad_ix = $db->last_insert_id;
                    }else{
                        $db->query("SELECT id FROM ".TBL_SHOP_ADDIMAGE." WHERE id=LAST_INSERT_ID()");
                        $db->fetch();
                        $ad_ix = $db->dt[id];
                    }

                    $image_info = getimagesize ($imgfile);
                    $image_type = substr($image_info['mime'],-3);

                    if($image_info[0] > $image_info[1]){
                        $image_resize_type = "W";
                    }else{
                        $image_resize_type = "H";
                    }

                    if($image_type == "gif" || $image_type == "GIF"){
                        copy($imgfile, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif");
                        MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", MIRROR_NONE);
                        resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height],$image_resize_type);

                        MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif", MIRROR_NONE);
                        resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height],$image_resize_type);

                        MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif", MIRROR_NONE);
                        resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height],$image_resize_type);

                    }else if($image_type == "png" || $image_type == "PNG"){

                        copy($imgfile, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif");
                        MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", MIRROR_NONE);
                        resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height],$image_resize_type);

                        MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif", MIRROR_NONE);
                        resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height],$image_resize_type);

                        MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif", MIRROR_NONE);
                        resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height],$image_resize_type);

                    }else{
                        copy($imgfile, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif");
                        Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", MIRROR_NONE);
                        resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height],$image_resize_type);

                        Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif", MIRROR_NONE);
                        resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height],$image_resize_type);

                        Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif", MIRROR_NONE);
                        resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height],$image_resize_type);
                    }
                }

                unlink($imgfile);
            }
        }
    }

    function copyproductbasicinfo($itemdata,$pid,$app_version,$RequestAppType){
        global $db,$admin_config;

        //예외처리
        $basicinfo = urldecode($itemdata->basicinfo);
        $basicinfo = str_replace("''","",$basicinfo);
        $basicinfo = str_replace("'","&#39;",$basicinfo);
        $basicinfo = str_replace('"',"&quot;",$basicinfo);
        $basicinfo = "<p>".$basicinfo."</p>";

        //상세 이미지 등록및 처리
        $uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $pid, 'Y');
        $detail_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/";

        if(!is_dir($detail_img_src)){
            mkdir($detail_img_src, 0777);
            //chmod($detail_img_src,0777);
        }else{
            //chmod($detail_img_src,0777);
        }

        $uploadpath = $_SERVER["DOCUMENT_ROOT"].'/admin/mobile/appapi/app/tmp/';


        //app_version ex> 1.0.30
        if($RequestAppType == "android"){
            if( ! empty($_FILES['basicimgfile'])){
                $start_i = 0;
                $last_i = 7;
            }else{
                $start_i = 1;
                $last_i = 8;
            }
        }else{
            $start_i = 0;
            $last_i = 7;
        }

        for($i=$start_i;$i < $last_i;$i++){

            //$file="detailimgfile".$i;
            if($i == 0){
                $file="basicimgfile";
            }else{
                $file="imgfile".$i;
            }

            $afile = $_FILES[$file];

            /**
             *  SM_HEO*/
            /*
            syslog(1, "================= START IN Image Detail ====================");
            syslog(1, "RequestAppType = " . $RequestAppType);
            syslog(1, "file = " . $file);
            syslog(1, "afile = " . print_r($afile,true));
            syslog(1, print_r($_FILES[$file],true));
            syslog(1, "================= END IN Image Detail ====================");
            */
            /*
            */

            $filename = $afile['name'].$i;
            $_image_filename[$i] = $afile['name'].$i;
            $imgfile = $uploadpath . $filename;

            if( $afile['size'] > 0 ) {

                if(file_exists($imgfile)) {
                    unlink($imgfile);
                }

                copy($afile['tmp_name'], $imgfile);
                //exec('chmod -R 777 ' . $imgfile);

                $image_info = getimagesize ($imgfile);
                $image_type = substr($image_info['mime'],-3);

                /*
                if($image_info[0] > $image_info[1]){
                    $image_resize_type = "W";
                }else{
                    $image_resize_type = "H";
                }
                */
                //syslog(LOG_NOTICE, json_encode($image_info[0]));
                $image_resize_type = "W";

                if($image_info[0] > 800){
                    if($image_type == "gif" || $image_type == "GIF"){

                        MirrorGif($imgfile, $detail_img_src.$filename, MIRROR_NONE);
                        resize_gif($detail_img_src.$filename,800,0,$image_resize_type);

                    }else if($image_type == "png" || $image_type == "PNG"){

                        MirrorPNG($imgfile, $detail_img_src.$filename, MIRROR_NONE);
                        resize_png($detail_img_src.$filename,800,0,$image_resize_type);

                    }else{

                        Mirror($imgfile, $detail_img_src.$filename, MIRROR_NONE);
                        resize_jpg($detail_img_src.$filename,800,0,$image_resize_type);

                    }
                }else{
                    copy($imgfile, $detail_img_src.$filename);
                }


                //$basicinfo .= "<br/><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$filename."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/>";

                unlink($afile['tmp_name']);
                unlink($imgfile);
            }



        }

        $num1 = $start_i;
        $num2 = $start_i+1;
        $num3 = $start_i+2;
        $num4 = $start_i+3;
        $num5 = $start_i+4;
        $num6 = $start_i+5;
        $num7 = $start_i+6;

        $basicinfo .= "<table width=\"1000px\" border=0 style=\"table-layout:fixed\">";
        $basicinfo .= "<col width=50%><col width=50%>";
        $basicinfo .= "<tr><td width=50%></td><td width=50%></td></tr>";
        $basicinfo .= "<tr><td colspan=2><img src=\"".$admin_config[mall_data_root]."/images/basicinfo_01.jpg\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 50px 0px\"/></td></tr>";
        /*
        $basicinfo .= "<tr>";
        $basicinfo .= "<td align=center><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num1]."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td>";
        $basicinfo .= "<td align=center><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num2]."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td>";
        $basicinfo .= "</tr>";
        */

        $basicinfo .= "<tr><td colspan=2 align=center><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num1]."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td></tr>";
        $basicinfo .= "<tr><td colspan=2 align=center><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num2]."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td></tr>";

        $basicinfo .= "<tr><td colspan=2 align=center><img src=\"".$admin_config[mall_data_root]."/images/basicinfo_02.jpg\" class=\"basicinfo_image\" border=0 style=\"margin:0px 0px 0px 0px\"/></td></tr>";

        if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num3]) && !empty($_image_filename[$num3])){
            $basicinfo .= "<tr><td colspan=2 align=center><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num3]."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td></tr>";
        }
        //$basicinfo .= "<tr><td colspan=2 align=center><img src=\"".$admin_config[mall_data_root]."/images/basicinfo_03.jpg\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 50px 0px\"/></td></tr>";

        if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num4]) && !empty($_image_filename[$num4])){
            /*
            $basicinfo .= "<tr>";
            $basicinfo .= "<td align=right><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num4]."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td>";

            $basicinfo .= "<td align=center><img src=\"".$admin_config[mall_data_root]."/images/basicinfo_05.jpg\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td>";
            $basicinfo .= "</tr>";
            */

            $basicinfo .= "<tr><td colspan=2 align=center><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num4]."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td></tr>";
        }

        if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num5]) && !empty($_image_filename[$num5])){
            /*
            $basicinfo .= "<tr>";
            $basicinfo .= "<td align=center> </td>";
            $basicinfo .= "<td align=center><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num5]."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td>";
            $basicinfo .= "</tr>";
            */

            $basicinfo .= "<tr><td colspan=2 align=center><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num5]."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td></tr>";
        }

        if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num6]) && !empty($_image_filename[$num6])){
            $basicinfo .= "<tr><td colspan=2 align=center><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num6]."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td></tr>";
        }

        if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num7]) && !empty($_image_filename[$num7])){
            $basicinfo .= "<tr><td colspan=2 align=center><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/".$_image_filename[$num7]."\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td></tr>";
        }

        //$basicinfo .= "<tr><td align=center colspan=2><img src=\"".$admin_config[mall_data_root]."/images/basicinfo_06.jpg\" class=\"basicinfo_image\" border=0 style=\"margin:30px 0px 30px 0px\"/></td></tr>";
        $basicinfo .= "</table>";

        $db->query("UPDATE ".TBL_SHOP_PRODUCT." SET basicinfo = '".$basicinfo."' WHERE id='$pid'");
    }

?>