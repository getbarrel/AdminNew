<?
include_once("../web.config");
include_once("../../class/database.class");
include_once("../lib/imageResize.lib.php");
include_once("../class/layout.class");

session_start();
$db = new Database;
$db2 = new Database;
//$db->debug = true;
//$db2->debug = true;

if($admininfo[company_id] == ""){
	echo "<script language='JavaScript' src='../_language/language.php'></Script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}


if ($act == "templet_insert"){
	$thisfile = load_template($DOCUMENT_ROOT.$admin_config[mall_data_root]."/productreg_templet/$page_name");
	echo "
	<html>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<body>
	$thisfile
	</body>
	</html>";
	echo "<script>parent.document.frames['iView'].document.body.innerHTML = document.body.innerHTML;</script>";
}


if ($act == "vieworder_update")
{
	for($i=0;$i < count($sortlist);$i++){
		$sql = "update ".TBL_SHOP_RELATION_PRODUCT." set
			vieworder='".($i+1)."'
			where pid='$pid' and rp_pid='".$sortlist[$i]."'";//
		$db->query($sql);
	}
}






if ($act == 'insert' || $act == "tmp_insert"){
	
	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/",0777);
	}

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/",0777);
	}

	$db->query("update ".TBL_SHOP_PRODUCT." set vieworder = vieworder + 1 ");
	$vieworder = 1;
	$data_text_convert = $basicinfo;

	if($admininfo[admin_level] == 9){
		$state = "1";
		if($disp == ""){
			$disp = "1";	
		};
		if($mode == "copy"){
			$company_id = $admin;
		}else{
			if($admin == ""){
				$company_id = $admininfo[company_id];
			}else{
				$company_id = $admin;
			}
		}
	}else{
		$state = "6";
		$disp = "0";
		$company_id = $admininfo[company_id];
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


	if(is_array($category)){
		$reg_category = "Y";
	}else{
		$reg_category = "N";
	}
	if($reserve_yn == ""){
		$reserve_yn = "N";
	}
	for($i=0;$i<count($icon_check);$i++){
		if($i < count($icon_check)-1){
			$icons .= $icon_check[$i].";";
		}else{
			$icons .= $icon_check[$i];
		}
	}
	$sns_btn = serialize($sns_btn);

	if($delivery_policy == ""){
		$delivery_policy = "1";
	}

	if($regdate == ""){
		$regdate = "NOW()";
	}else{
		$regdate = "'".$regdate."'";
	}

	if($editdate == ""){
		$editdate = "'0000-00-00 00:00:00'";
	}else{
		$editdate = "'".$editdate."'";
	}
	
	$sql = "INSERT INTO ".TBL_SHOP_PRODUCT."
					(id,  pname,pcode, brand,brand_name, company, buying_company, paper_pname,  shotinfo,  buyingservice_coprice, listprice,sellprice,  coprice,reserve_yn, 
					reserve,reserve_rate, sns_btn_yn, sns_btn,  bimg, basicinfo,  icons,state, disp,product_type, movie, vieworder, admin,stock,safestock,search_keyword,reg_category,  
					surtax_yorn,delivery_company,one_commission,commission,stock_use_yn,supply_company, inventory_info,delivery_policy,delivery_product_policy,delivery_package,delivery_price,
					free_delivery_yn,free_delivery_count,
					sell_priod_sdate,sell_priod_edate,allow_order_type,allow_order_cnt_byonesell,allow_order_cnt_byoneperson,orgin,make_date,expiry_date,
					etc2, etc10, download_img, download_desc, bs_goods_url, bs_site, currency_ix, hotcon_event_id, hotcon_pcode, co_goods, co_pid, editdate, regdate,
					exchangeable_yn, returnable_yn, admin_memo)
					values('', '".strip_tags(trim($pname))."','$pcode', '$brand','$brand_name','$company','$buying_company', '$paper_pname', '$shotinfo', '$buyingservice_coprice',
					'$listprice','$sellprice', '$coprice','$reserve_yn', '$reserve','$rate1','$sns_btn_yn','$sns_btn',  '$bimg_text','$basicinfo','$icons', $state, '$disp','$product_type', 
					'$movie', '$vieworder', '$company_id','$stock','$safestock','$search_keyword','$reg_category','$surtax_yorn','$delivery_company','$one_commission','$commission',
					'$stock_use_yn','$supply_company','$inventory_info','$delivery_policy','$delivery_product_policy','$delivery_package','$delivery_price','$free_delivery_yn','$free_delivery_count',
					'$sell_priod_sdate','$sell_priod_edate','$allow_order_type','$allow_order_cnt_byonesell','$allow_order_cnt_byoneperson','$orgin','$make_date','$expiry_date',
					'$etc2','$etc10','$download_img','$download_desc','$bs_goods_url','$bs_site','$currency_ix','$hotcon_event_id', '$hotcon_pcode','$co_goods','$co_pid', ".$editdate.", ".$regdate.",
					'$exchangeable_yn', '$returnable_yn', '$admin_memo'
					) ";

	//echo($sql);
	//exit;
	$db->query($sql);
	$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT." WHERE id=LAST_INSERT_ID()");
	$db->fetch();
	$INSERT_PRODUCT_ID = $db->dt[0];

	if($round_type && $round_precision){		
		$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET round_precision = '$round_precision', round_type = '$round_type' WHERE id='".$INSERT_PRODUCT_ID."'";
		$db->query($sql);
	}

	//카테고리 정보 입력
	for($i=0;$i<count($category);$i++){
		if($category[$i] == $basic){
			$basic = 1;
		}else{
			$basic = 0;
		}
		
		$db->query("insert into shop_product_relation (rid, cid, pid, disp, basic,insert_yn, regdate ) values ('','".$category[$i]."','".$INSERT_PRODUCT_ID."','1','".$basic."','N',NOW())");
	}

	
	if($product_type == "2"){//경매상품일때 경매테이블로 입력
		$startdate = $FromYY."-".$FromMM."-".$FromDD." ".$FromHH.":".$FromII.":00";
		$enddate = $ToYY."-".$ToMM."-".$ToDD." ".$ToHH.":".$ToII.":00";
		$db->query("insert into shop_product_auction (ix, pid,startdate, enddate, plusdate, startprice, plus_count, plus_use_count, regdate) values ('','$INSERT_PRODUCT_ID','$startdate','$enddate','$enddate','$startprice','$plus_count',0,NOW())");

	}else if($product_type == "7"){//스페셜카테고리 자동차 상품일때
		$vintage = $vintage_year."-".$vintage_month;
		$sql = "insert into shop_product_car(pid,vechile_div,mf_ix,md_ix,gr_ix,vt_ix,vintage,mileage,displacement,transmission,color,fuel,license_plate,car_condition,regdate) values('$INSERT_PRODUCT_ID','$vechile_div','$mf_ix','$md_ix','$gr_ix','$vt_ix','$vintage','$mileage','$displacement','$transmission','$color','$fuel','$license_plate','$car_condition',NOW())";
		$db->query($sql);

	}else if($product_type == "8"){//스페셜카테고리 부동산 상품일때
		$sql = "insert into shop_product_property(pid,rg_ix,dimensions,deal_type,property_type,loans,maintenance_cost,heating_fuel,posisbile_date,regdate) 
				values
				('$INSERT_PRODUCT_ID','$rg_ix','$dimensions','$deal_type','$property_type','$loans','$maintenance_cost','$heating_fuel','$posisbile_date',NOW())";
		$db->query($sql);

	}else if($product_type == "9"){//스페셜카테고리 부동산 상품일때
		$sql = "insert into shop_product_hotel
					(pid,rg_ix,hotel_level,room_level,regdate) 
					values
					('$INSERT_PRODUCT_ID','$hotel_rg_ix','$hotel_level','$room_level',NOW()) ";
			$db->query($sql);

	}else if($product_type == "10"){//스페셜카테고리 여행 상품일때
		$sql = "insert into shop_product_sightseeing
					(pid,rg_ix,regdate) 
					values
					('$INSERT_PRODUCT_ID','$sightseeing_rg_ix',NOW()) ";
			$db->query($sql);
	}

	$image_db = new Database;
	$image_db->query("select * from shop_image_resizeinfo order by idx");
	$image_info2 = $image_db->fetchall();
	$before_uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $bpid, 'Y');
	$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $INSERT_PRODUCT_ID, 'Y');
	$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $INSERT_PRODUCT_ID, 'Y');

	if ($allimg_size > 0 || $mode == "copy" || ($bimg_text && $img_url_copy)){
		//워터마크 적용

		if($mode == "copy" && $bimg_text == "" && $allimg == ""){// 상품 복사모드일때는 기존 상품의 이미지를 복사해서 나머지 이미지가 생성됩니다
			$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$before_uploaddir."/basic_".$bpid.".gif";
		
			if (file_exists($basic_img_src)){
				copy($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$INSERT_PRODUCT_ID.".gif");
				chmod($basic_img_src,0777);
				$chk_mimg = 1;
				$chk_msimg = 1;
				$chk_simg = 1;
				$chk_cimg = 1;

				$image_info = getimagesize ($basic_img_src);
				$image_type = substr($image_info['mime'],-3);
				$image_width = $image_info[0];
			}

		}else{

			if($img_url_copy){
				copy($bimg_text, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$INSERT_PRODUCT_ID.".gif");
				$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$INSERT_PRODUCT_ID.".gif";
				$allimg = $basic_img_src;
				chmod($basic_img_src,0777);
				$image_info = getimagesize ($basic_img_src);
				$image_type = substr($image_info['mime'],-3);
				$chk_mimg = 1;
				$chk_msimg = 1;
				$chk_simg = 1;
				$chk_cimg = 1;

			}else{
				$image_info = getimagesize ($allimg);
				$image_type = substr($image_info['mime'],-3);
				$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$INSERT_PRODUCT_ID.".gif";
				copy($allimg, $basic_img_src); // 원본 이미지를 만든다.
				chmod($basic_img_src,0777);
			}
			
		}

		if($watermark) {
			require_once "../lib/class.upload.php";

			$s_water_handle = new Upload($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$INSERT_PRODUCT_ID.".gif");
			$s_water_result = WaterMarkProcess2($s_water_handle, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/");

			$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_watermark_".$INSERT_PRODUCT_ID.".gif";
			copy($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/watermark/basic_".$INSERT_PRODUCT_ID.".gif",$basic_img_src);
			chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$INSERT_PRODUCT_ID.".gif", 0777);
			$image_type = "gif"; // 워터마크 처리후 마임타입이 gif로 바뀐다.
		}


		if($image_type == "gif" || $image_type == "GIF"){
			MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
			resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$INSERT_PRODUCT_ID.".gif",$image_info2[0][width],$image_info2[0][height],'W');

			if($chk_mimg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$INSERT_PRODUCT_ID.".gif",$image_info2[1][width],$image_info2[1][height],'W');
			}

			if($chk_msimg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$INSERT_PRODUCT_ID.".gif",$image_info2[2][width],$image_info2[2][height],'W');
			}

			if($chk_simg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$INSERT_PRODUCT_ID.".gif",$image_info2[3][width],$image_info2[3][height],'W');
			}

			if($chk_cimg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$INSERT_PRODUCT_ID.".gif",$image_info2[4][width],$image_info2[4][height],'W');
			}

		}else if($image_type == "png" || $image_type == "PNG"){
			MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
			resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$INSERT_PRODUCT_ID.".gif",$image_info2[0][width],$image_info2[0][height],'W');

			if($chk_mimg == 1){
				MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$INSERT_PRODUCT_ID.".gif",$image_info2[1][width],$image_info2[1][height],'W');
			}

			if($chk_msimg == 1){
				MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$INSERT_PRODUCT_ID.".gif",$image_info2[2][width],$image_info2[2][height],'W');
			}

			if($chk_simg == 1){
				MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$INSERT_PRODUCT_ID.".gif",$image_info2[3][width],$image_info2[3][height],'W');
			}

			if($chk_cimg == 1){
				MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$INSERT_PRODUCT_ID.".gif",$image_info2[4][width],$image_info2[4][height],'W');
			}
			
		}else{

			if (file_exists($basic_img_src)){
					Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
					resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$INSERT_PRODUCT_ID.".gif",$image_info2[0][width],$image_info2[0][height],'W');

					if($chk_mimg == 1){
						Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$INSERT_PRODUCT_ID.".gif",$image_info2[1][width],$image_info2[1][height],'W');
					}

					if($chk_msimg == 1){
						Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$INSERT_PRODUCT_ID.".gif",$image_info2[2][width],$image_info2[2][height],'W');
					}

					if($chk_simg == 1){
						Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$INSERT_PRODUCT_ID.".gif",$image_info2[3][width],$image_info2[3][height],'W');
					}

					if($chk_cimg == 1){
						Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$INSERT_PRODUCT_ID.".gif",$image_info2[4][width],$image_info2[4][height],'W');
					}
			}
		}
	}


	if ($bimg_size > 0){
		copy($bimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$INSERT_PRODUCT_ID.".gif");
	}

	if ($mimg_size > 0){
		copy($mimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$INSERT_PRODUCT_ID.".gif");
	}

	if ($msimg_size > 0){
		copy($msimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$INSERT_PRODUCT_ID.".gif");
	}

	if ($simg_size > 0){
		copy($simg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$INSERT_PRODUCT_ID.".gif");
	}

	if ($cimg_size > 0){
		copy($cimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$INSERT_PRODUCT_ID.".gif");
	}

	if ($download_image_size > 0 || $download_desc_size > 0){
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/download/";
		if(!is_dir($path)){		
			mkdir($path, 0777);
			chmod($path,0777);	
		}

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/download/".md5("FORBIZ".$INSERT_PRODUCT_ID)."/";
		if(!is_dir($path)){		
			mkdir($path, 0777);
			chmod($path,0777);	
		}
	}
			
	if ($download_image_size > 0)
	{
		move_uploaded_file($download_image, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/download/".md5("FORBIZ".$INSERT_PRODUCT_ID)."/".iconv("cp949","utf-8",$download_image_name));
		$sql="UPDATE ".TBL_SHOP_PRODUCT." SET download_img='".$download_image_name."' WHERE id='".$INSERT_PRODUCT_ID."' ";
		$db->query($sql);
	}

	if ($download_desc_size > 0)
	{
		move_uploaded_file($download_desc, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/download/".md5("FORBIZ".$INSERT_PRODUCT_ID)."/".iconv("cp949","utf-8",$download_desc_name));
		$sql="UPDATE ".TBL_SHOP_PRODUCT." SET download_desc='".$download_desc_name."' WHERE id='".$INSERT_PRODUCT_ID."' ";
		$db->query($sql);
	}

	if($chk_deepzoom == 1){
		$client = new SoapClient("http://".$_SERVER["HTTP_HOST"]."/VESAPI/VESAPIWS.asmx?wsdl=0");
		$params = new stdClass();
		$params->inputPhysicalPathString = $basic_img_src;
		$params->outputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/deepzoom/".$INSERT_PRODUCT_ID;

		$response = $client->TilingWithPhysicalPath($params);
	}

	$sql = "INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, sellprice, coprice, reserve,  admin,regdate) ";
	$sql .= " values('', '".$INSERT_PRODUCT_ID."','$sellprice', '$coprice', '$reserve',  '".$admininfo[company_id]."',NOW()) ";

	$db2->query($sql);

	$db2->query("update shop_product_buyingservice_priceinfo set bs_use_yn = '0' where pid ='".$INSERT_PRODUCT_ID."'");
	$sql = "insert into shop_product_buyingservice_priceinfo(bsp_ix,pid,orgin_price,exchange_rate,air_wt,air_shipping,duty,clearance_fee,clearance_type,bs_fee_rate,bs_fee,bs_use_yn,regdate)
					values('$bsp_ix','".$INSERT_PRODUCT_ID."','$orgin_price','$exchange_rate','$air_wt','$air_shipping','$duty','$clearance_fee','$clearance_type','$bs_fee_rate','$bs_fee','1',NOW()) ";
	$db2->query($sql);

	if($goods_desc_copy){

			$data_text_convert = str_replace("\\","",$data_text_convert);
			preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);
			$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/".$INSERT_PRODUCT_ID."/";

			if(substr_count($data_text_convert,"<IMG") > 0){
				if(!is_dir($path)){
					mkdir($path, 0777);
					chmod($path,0777);
				}else{
					chmod($path,0777);
				}
			}



			for($i=0;$i < count($out);$i++){
				for($j=0;$j < count($out[$i]);$j++){

					$img = returnImagePath($out[$i][$j]);
					$img = ClearText($img);
					try{
						if($img){
							if(substr_count($img,$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
								if(substr_count($img,"$HTTP_HOST")>0){
									$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"],$img);

									@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img));
									if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
										unlink($local_img_path);
									}

									$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
								}else{
									if(substr_count($img,$DOCUMENT_ROOT)){
										//$img = $DOCUMENT_ROOT.$img;
										if(@copy($DOCUMENT_ROOT.$img,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($DOCUMENT_ROOT.$img))){
											$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
										}
									}else{
										if(copy($img,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img))){
											$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
										}
									}
								//	echo ":::".$img."<br>";
								}
							}
						}

					}catch(Exception $e){
					    // 에러처리 구문
					    //exit($e->getMessage());
					}

				}
			}

			$basicinfo = str_replace("http://$HTTP_HOST","",$basicinfo);


			$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET basicinfo = '$basicinfo' WHERE id='$INSERT_PRODUCT_ID'");
	}

	$pid = $INSERT_PRODUCT_ID;

	if($options_price_stock["option_name"]){

		if($options_price_stock["option_use"]){
			$options_price_stock_use = $options_price_stock["option_use"];
		}else{
			$options_price_stock_use = 0;
		}


			$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate)
							VALUES
							('','$pid','".$options_price_stock["option_name"]."','".$options_price_stock["option_kind"]."','".$options_price_stock["type"]."','".$options_price_stock_use."',NOW())";

			$db->query($sql);
			$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
			$db->fetch();
			$opn_ix = $db->dt[0];

		for($j=0;$j < count($options_price_stock["option_div"]);$j++){
			if($options_price_stock[option_div][$j]){

					$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_code ) ";
					$sql = $sql." values('','$pid','$opn_ix','".$options_price_stock[option_div][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[stock][$j]."','".$options_price_stock[safestock][$j]."','".$options_price_stock[code][$j]."') ";

					$db->query($sql);

					if($options_price_stock[stock][$j] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
						$option_stock_yn = "N";
					}

					if($options_price_stock[stock][$j] < $options_price_stock[safestock][$j] && $option_stock_yn == ""){
						$option_stock_yn = "R";
					}
			}
		}
		$db->query("SELECT sum(option_stock) as option_stock,sum(option_safestock) as option_safestock  FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE opn_ix='$opn_ix'");
		$db->fetch();
		$option_stock = $db->dt[option_stock];
		$option_safestock = $db->dt[option_safestock];
		if($sell_ing_cnt == ""){
			$sell_ing_cnt = 0;
		}
		$db->query("update ".TBL_SHOP_PRODUCT." set stock = ".$option_stock." ,safestock = $option_safestock, option_stock_yn = '$option_stock_yn' where id ='$pid'");

	}

	if($option_all_use == "Y"){
		$sql = "select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('s','p','r') ";
		$db->query($sql);
		if($db->total){
			$del_options = $db->fetchall();
			for($i=0;$i < count($del_options);$i++){
				$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$del_options[$i][opn_ix]."' and pid = '$pid' ");
			}
		}
		$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('s','p','r') ");
	}else{

		for($i=0;$i < count($options);$i++){

			if($options[$i]["option_name"]){

					$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate)
									VALUES
									('','$pid','".$options[$i]["option_name"]."','".$options[$i]["option_kind"]."','".$options[$i]["option_type"]."','".$options[$i]["option_use"]."',NOW())";
					$db->query($sql);
					$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
					$db->fetch();
					$opn_ix = $db->dt[0];

				for($j=0;$j < count($options[$i]["details"]);$j++){
					if($options[$i][details][$j][option_div]){

							$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_code, option_coprice,option_price,option_stock, option_safestock, option_etc1) ";
							$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$i][details][$j][option_div])."','".$options[$i][details][$j][code]."','".$options[$i][details][$j][coprice]."','".$options[$i][details][$j][price]."','0','0','".$options[$i][details][$j][etc1]."') ";
							//echo $sql;
							$db->query($sql);
							$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE id=LAST_INSERT_ID()");
							$db->fetch();
							$opnd_ix = $db->dt[0];
							
							if($options[$i]["details"][$j][thumb_images] || $options[$i]["details"][$j][goods_images]){
								$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $pid, 'Y');
								if(!file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/options/")){
									mkdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/options/");
									chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/options/",0777);
								}
								
								if($options[$i]["details"][$j][thumb_images]){
									copy($options[$i]["details"][$j][thumb_images], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_s.gif");
								}
								if($options[$i]["details"][$j][goods_images]){
									copy($options[$i]["details"][$j][goods_images], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/options/options_detail_".$opnd_ix."_b.gif");
								}
							}

					}
				}

			}
		}

	}// option_all_use 있는지 여부

	if($display_options){
		for($i=0;$i < count($_POST["display_options"]);$i++){
			if($display_options[$i]["dp_title"] && $display_options[$i]["dp_desc"]){
				if($display_options[$i]["dp_use"]){
					$dp_use = $display_options[$i]["dp_use"];
				}else{
					$dp_use = "0";
				}

				$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." (dp_ix,pid,dp_title,dp_desc,dp_use, regdate) values('','$pid','".$display_options[$i]["dp_title"]."','".$display_options[$i]["dp_desc"]."','".$dp_use."',NOW()) ";

				$db->query($sql);
			}
		}
	}

	//관련상품등록
	if($rpid){
		for($i=0;$i<count($rpid[1]);$i++){
			$sql = "insert into ".TBL_SHOP_RELATION_PRODUCT." (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','".$rpid[1][$i]."','".$i."','N',NOW())";
			$db->query($sql);
		}
	}

	for($i=0;$i < count($addimages);$i++){
		if ($_FILES[addimages][size][$i][addbimg] > 0){
				$image_info = getimagesize ($_FILES[addimages][tmp_name][$i][addbimg]);
			
				if($_FILES[addimages][name][$i][addbimg]){
					$sql = "INSERT INTO ".TBL_SHOP_ADDIMAGE." (id, pid, deepzoom, regdate) values('', '$pid','".$addimages[$i][add_copy_deepzoomimg]."',  NOW()) ";
					$db->query($sql);
					$db->query("SELECT id FROM ".TBL_SHOP_ADDIMAGE." WHERE id=LAST_INSERT_ID()");
					$db->fetch();
					$ad_ix = $db->dt[id];
				}

				$image_info = getimagesize ($_FILES[addimages][tmp_name][$i][addbimg]);
				$image_type = substr($image_info['mime'],-3);

				if ($_FILES[addimages][size][$i][addbimg] > 0)
				{
					if($image_type == "gif"){
						copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif");
						MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height],'W');

						if($addimages[$i][add_chk_mimg] == 1){
							MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif", MIRROR_NONE);
							resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height],'W');
						}

						if($addimages[$i][add_chk_cimg] == 1){
							MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif", MIRROR_NONE);
							resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height],'W');
						}

					}else{
						copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif");
						Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height],'W');

						if($addimages[$i][add_chk_mimg] == 1){
							Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif", MIRROR_NONE);
							resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height],'W');
						}

						if($addimages[$i][add_chk_cimg] == 1){
							Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif", MIRROR_NONE);
							resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height],'W');
						}
					}

					if($addimages[$i][add_copy_deepzoomimg] == 1){
						$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg/deepzoom";

						if(!is_dir($path)){
							mkdir($path, 0777);
							chmod($path,0777);
						}else{
							chmod($path,0777);
						}

						$client = new SoapClient("http://".$_SERVER["HTTP_HOST"]."/VESAPI/VESAPIWS.asmx?wsdl=0");
						$params = new stdClass();
						$params->inputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif";
						$params->outputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg/deepzoom/".$ad_ix;

						$response = $client->TilingWithPhysicalPath($params);
					}
				}

				if ($_FILES[addimages][size][$i][addmimg] > 0)
				{
					move_uploaded_file($_FILES[addimages][tmp_name][$i][addmimg], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif");
				}

				if ($_FILES[addimages][size][$i][addcimg] > 0)
				{
					move_uploaded_file($_FILES[addimages][tmp_name][$i][addcimg], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif");
				}
		}
	}


	if($mode == "copy"){
		////////////////////////////////////////////// 옵션 정보 복사 루틴 //////////////////////////////////////////////////////////
/*
		$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid='$bpid' ");
		if($db->total){// 옵션이 있으면
			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);
				// 옵션 정보가 있으면 복사해서 넣는다
				$sql="INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." SELECT '' as opn_ix,'$INSERT_PRODUCT_ID' as pid,option_name,option_kind,option_type,option_use,NOW() as regdate FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid='$bpid' and opn_ix ='".$db->dt[opn_ix]."' ";
				$db2->query($sql);
				// 복사해 넣은 옵션정보 키값을 가져와 옵션 디테일 정보를 입력한다
				$db2->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix= LAST_INSERT_ID() ");
				$db2->fetch();
				$opn_ix = $db2->dt[opn_ix];

				//해당 옵션에 옵션 디테일 정보가 있는지 체크하기
				$db2->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$bpid' and opn_ix = '".$db->dt[opn_ix]."' ");

				if($db2->total){// 해당 옵션의 디테일 정보가 있을경우  정보를 복사한다
					$db2->query("INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." SELECT '' as id, '$INSERT_PRODUCT_ID' as pid, '$opn_ix' as opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_code, option_useprice FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$bpid' and opn_ix = '".$db->dt[opn_ix]."' ");
				}
			}
		}


		////////////////////////////////////////// 디스플레이 정보 복사 루틴 //////////////////////////////////////////////////////////

		$db->query("SELECT pid FROM ".TBL_SHOP_PRODUCT_DISPLAYINFO." WHERE pid='$bpid' ");

		if($db->total){
			$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." SELECT '' as dp_ix,'$INSERT_PRODUCT_ID' as pid,dp_title,dp_desc,NOW() as regdate FROM ".TBL_SHOP_PRODUCT_DISPLAYINFO." where pid='$bpid' ";
			$db->query($sql);
		}


		////////////////////////////////////////// 관련 상품  복사 루틴 //////////////////////////////////////////////////////////

		$db->query("SELECT pid FROM ".TBL_SHOP_RELATION_PRODUCT." WHERE pid='$bpid' ");

		if($db->total){
			$sql = "insert into ".TBL_SHOP_RELATION_PRODUCT." (rp_ix, pid, rp_pid, vieworder, regdate) SELECT '' as rp_ix, '$INSERT_PRODUCT_ID' as pid,rp_pid,vieworder,NOW() as regdate FROM ".TBL_SHOP_RELATION_PRODUCT." where pid='$bpid' ";
			$db->query($sql);
		}
		*/
	}



	if(!$bs_act){
		if($act == "tmp_insert"){
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/goods_input.php?id=".$pid."';</script>";
		}else{
			if($mmode == "pop"){
				echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품등록이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
			}else{
				echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/product_list.php';</script>";
			}
		}
	}
}

if ($act == "delete")
{
	$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $id, 'Y');
	//$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $id, 'Y');

	/*
	if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_$id.gif");
	}

	if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_$id.gif");
	}
	*/

	if ($uploaddir && file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/")){
		rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/");
	}

	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='$id'");
	$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='$id'");
	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$id'");
	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='$id'");
	$db->query("DELETE FROM ".TBL_SHOP_RELATION_PRODUCT." WHERE pid = '$id'");
	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT."_auction WHERE pid = '$id'");
	$db->query("DELETE FROM ".TBL_SHOP_CART." WHERE id='$id'");
	//$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE pid='$id'");

	$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $id, 'Y');
	if ($id && is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/")){
		rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/");
	}

	$db->query("SELECT id FROM ".TBL_SHOP_ADDIMAGE." WHERE  pid = '$id' ");
	for($i=0;$i < $db->tota;$i++){
		$db->fetch($i);
		$ad_ix = $db->dt[id];
		//$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif"
		
		if($id && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/deepzoom/$ad_ix")){
			rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/deepzoom/$ad_ix");
		}
	}
	
	$db->query("DELETE FROM ".TBL_SHOP_ADDIMAGE." WHERE  pid = '$id'");


	if($id && is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/$id")){
		rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/$id");
	}

	if($id && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/download/".md5("FORBIZ".$id)."/")){
		rmdirr($download_image, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/download/".md5("FORBIZ".$id)."/");
	}

	//echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품삭제가 정상적으로 처리 되었습니다.');document.location.href='product_list.php?".$QUERY_STRING."';</script>";
	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품삭제가 정상적으로 처리 되었습니다.');parent.document.location.href='../product/product_list.php';</script>";

	//header("Location:../product_list.php");
}

if ($act == "update" || $act == "tmp_update")
{
	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/",0777);
	}

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/",0777);
	}

	$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $id, 'Y');	
	$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $id, 'Y');
	//$image_info = getimagesize ($allimg);
	//$image_type = substr($image_info['mime'],-3);
	//$image_width = $image_info[0];
	$image_db = new Database;
	$image_db->query("select * from shop_image_resizeinfo order by idx");
	$image_info2 = $image_db->fetchall();

	if($allimg_size != 0 || ($bimg_text && $img_url_copy)){
		
		if($bimg_text && $img_url_copy){
			//echo str_replace("$","\$",$bimg_text);
			copy(str_replace("$","\$",$bimg_text), $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif");
			$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif";
			chmod($basic_img_src,0777);
			$image_info = getimagesize ($basic_img_src);
			//print_r($image_info);
			$image_type = substr($image_info['mime'],-3);
		}else{
			$image_info = getimagesize ($allimg);
			$image_type = substr($image_info['mime'],-3);
			$image_width = $image_info[0];
			copy($allimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif");
			$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif";
			chmod($basic_img_src,0777);
		}

		//워터마크 적용
		if($watermark) {
			require_once "../lib/class.upload.php";

			$s_water_handle = new Upload($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif");
			$s_water_result = WaterMarkProcess2($s_water_handle, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/");
			@copy($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/watermark/basic_".$id.".gif",$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif");
			@chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", 0777);
			rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/watermark/");

			$image_type = "gif"; // 워터마크 처리후 마임타입이 gif로 바뀐다.
		}

		if($image_type == "gif"){
			if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif");
			}

			//copy($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif");
			MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif", MIRROR_NONE);
			resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif",$image_info2[0][width],$image_info2[0][height],'W');

			if($chk_mimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif",$image_info2[1][width],$image_info2[1][height],'W');
			}

			if($chk_msimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif",$image_info2[2][width],$image_info2[2][height],'W');
			}

			if($chk_simg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif",$image_info2[3][width],$image_info2[3][height],'W');
			}

			if($chk_cimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif",$image_info2[4][width],$image_info2[4][height],'W');
			}
		}else if($image_type == "png"){

			if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif");
			}

			MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif", MIRROR_NONE);
			resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif",$image_info2[0][width],$image_info2[0][height],'W');

			if($chk_mimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif");
				}
				MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif", MIRROR_NONE);
				resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif",$image_info2[1][width],$image_info2[1][height],'W');
			}

			if($chk_msimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif");
				}
				MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif", MIRROR_NONE);
				resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif",$image_info2[2][width],$image_info2[2][height],'W');
			}

			if($chk_simg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif");
				}
				MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif", MIRROR_NONE);
				resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif",$image_info2[3][width],$image_info2[3][height],'W');
			}

			if($chk_cimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif");
				}
				MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif", MIRROR_NONE);
				resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif",$image_info2[4][width],$image_info2[4][height],'W');
			}

		}else{

			if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif");
			}

			Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif", MIRROR_NONE);
			resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif",$image_info2[0][width],$image_info2[0][height],'W');

			if($chk_mimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif",$image_info2[1][width],$image_info2[1][height],'W');
			}

			if($chk_msimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif",$image_info2[2][width],$image_info2[2][height],'W');
			}

			if($chk_simg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif",$image_info2[3][width],$image_info2[3][height],'W');
			}

			if($chk_cimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif",$image_info2[4][width],$image_info2[4][height],'W');
			}
		}
	}




	if ($bimg_size > 0){
		copy($bimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$id.".gif");
	}

	if ($mimg_size > 0)
	{
		copy($mimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/m_".$id.".gif");
	}

	if ($msimg_size > 0)
	{
		copy($msimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_".$id.".gif");
	}

	if ($simg_size > 0)
	{
		copy($simg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/s_".$id.".gif");
	}

	if ($cimg_size > 0)
	{
		copy($cimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/c_".$id.".gif");
	}


	if ($download_image_size > 0 || $download_desc_size > 0){
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/download/";
		if(!is_dir($path)){		
			mkdir($path, 0777);
			chmod($path,0777);	
		}

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/download/".md5("FORBIZ".$id)."/";
		if(!is_dir($path)){		
			mkdir($path, 0777);
			chmod($path,0777);	
		}
	}
	
	
	if ($download_image_size > 0)
	{
		move_uploaded_file($download_image, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/download/".md5("FORBIZ".$id)."/".$download_image_name);
		$download_img_update = " , download_img = '".$download_image_name."' ";
	}

	if ($download_desc_size > 0)
	{
		move_uploaded_file($download_desc, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/download/".md5("FORBIZ".$id)."/".$download_desc_name);
		$download_desc_update = " , download_desc = '".$download_desc_name."' ";
	}


	if($chk_deepzoom == 1){
		if($id){
			rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/deepzoom/".$id);
		}
		$client = new SoapClient("http://".$_SERVER["HTTP_HOST"]."/VESAPI/VESAPIWS.asmx?wsdl=0");
		$params = new stdClass();
		$params->inputPhysicalPathString = $basic_img_src;
		$params->outputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/deepzoom/".$id;
		$response = $client->TilingWithPhysicalPath($params);
	}

	if($goods_desc_copy){
			$data_text_convert = $basicinfo;
			$data_text_convert = str_replace("\\","",$data_text_convert);
			preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);
			$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/".$id."/";
			$INSERT_PRODUCT_ID = $id ;
			if(substr_count($data_text_convert,"<IMG") > 0){
				if(!is_dir($path)){
					mkdir($path, 0777);
					chmod($path,0777);
				}else{
					//chmod($path,0777);
				}
			}

			for($i=0;$i < count($out);$i++){
				for($j=0;$j < count($out[$i]);$j++){
					$img = returnImagePath($out[$i][$j]);
					$img = ClearText($img);
					try{
						if($img){
							if(substr_count($img,$admin_config[mall_data_root]."/images/product_detail/".$id."/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
								if(substr_count($img,"$HTTP_HOST")>0){
									$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"],$img);

									@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($img));
									if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
										unlink($local_img_path);
									}

									$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
								}else{
									if(substr_count($img,$DOCUMENT_ROOT)){
										//$img = $DOCUMENT_ROOT.$img;
										if(@copy($DOCUMENT_ROOT.$img,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($DOCUMENT_ROOT.$img))){
											$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
										}
									}else{
										if(@copy($img,$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($img))){
											$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
										}
									}

								}
							}
						}

					}catch(Exception $e){
					    // 에러처리 구문
					    //exit($e->getMessage());
					}


				}
			}
			$basicinfo = str_replace("http://$HTTP_HOST","",$basicinfo);
	}
	if($admininfo[admin_level] == 8){
		//$state = "6"; // 입점업체 변경시 승인신청중으로 변경 되는 부분
	}

	if($admininfo[admin_level] == 9){
		$commision_str =",etc2='$etc2',one_commission='$one_commission',commission='$commission' , reserve='$reserve',reserve_rate='$rate1',
					 state='$state', disp='$disp' ";
		if($admin != ""){
			$admin_update = ",admin='$admin'";
		}
	}else{
		if($state != ""){
			$commision_str =", state='$state' ";
		}
	}

	if(!$brand_name){
		$sql = "select brand_name from shop_brand where b_ix = '$brand'";
		$db->query($sql);
		$db->fetch();
		$brand_name = $db->dt[brand_name];
		$brand_name = str_replace("'","\\'",$brand_name);
	}


	if($product_type == "2"){
		$startdate = $FromYY."-".$FromMM."-".$FromDD." ".$FromHH.":".$FromII.":00";
		$enddate = $ToYY."-".$ToMM."-".$ToDD." ".$ToHH.":".$ToII.":00";
		$sql ="select ix from shop_product_auction where pid = '$id' ";
		$db->query($sql);
		if($db->total){
			$db->query("update shop_product_auction set startdate='$startdate',enddate='$enddate',plusdate='$enddate',startprice='$startprice',plus_count='$plus_count' where pid = '$id' ");
		}else{
			$db->query("insert into shop_product_auction (ix, pid,startdate, enddate, plusdate, startprice, plus_count, plus_use_count, regdate) values ('','$id','$startdate','$enddate','$enddate','$startprice','$plus_count',0,NOW())");
		}

	}else if($product_type == "7"){//스페셜카테고리 자동차 상품일때
		$sql = "select pid from shop_product_car where pid = '$id'";
		$db->query($sql);
		if($db->total){
			$vintage = $vintage_year."-".$vintage_month;
			$sql = "update shop_product_car set
					mf_ix='$mf_ix',md_ix='$md_ix',gr_ix='$gr_ix',vt_ix='$vt_ix',
					vintage='$vintage',mileage='$mileage',displacement='$displacement',
					transmission='$transmission',color='$color',fuel='$fuel',
					license_plate='$license_plate',car_condition='$car_condition' 
					where pid='$id' ";

			$db->query($sql);
		}else{
			$vintage = $vintage_year."-".$vintage_month;
			$sql = "insert into shop_product_car
					(pid,vechile_div,mf_ix,md_ix,gr_ix,vt_ix,vintage,mileage,displacement,transmission,color,fuel,license_plate,car_condition,regdate) 
					values
					('$id','$vechile_div','$mf_ix','$md_ix','$gr_ix','$vt_ix','$vintage','$mileage','$displacement','$transmission','$color','$fuel','$license_plate','$car_condition',NOW())";

			$db->query($sql);
		}

	}else if($product_type == "8"){
		$sql = "select pid from shop_product_property where pid = '$id'";
		$db->query($sql);
		if($db->total){
			$sql = "update shop_product_property set 
					rg_ix='$rg_ix',dimensions='$dimensions',deal_type='$deal_type',property_type='$property_type',loans='$loans',maintenance_cost='$maintenance_cost',
					heating_fuel='$heating_fuel',posisbile_date='$posisbile_date'
					where pid='$id' ";
			$db->query($sql);
		}else{
			$sql = "insert into shop_product_property
					(pid,rg_ix,dimensions,deal_type,property_type,loans,maintenance_cost,heating_fuel,posisbile_date,regdate) 
					values
					('$id','$rg_ix','$dimensions','$deal_type','$property_type','$loans','$maintenance_cost','$heating_fuel','$posisbile_date',NOW())";
			$db->query($sql);
		}

	}else if($product_type == "9"){
		$sql = "select pid from shop_product_hotel where pid = '$id'";
		$db->query($sql);
		if($db->total){
			$sql = "update shop_product_hotel set 
					rg_ix='$hotel_rg_ix',hotel_level='$hotel_level',room_level='$room_level'
					where pid='$id' ";
			
			$db->query($sql);
		}else{
			$sql = "insert into shop_product_hotel
					(pid,rg_ix,hotel_level,room_level,regdate) 
					values
					('$id','$hotel_rg_ix','$hotel_level','$room_level',NOW()) ";

			$db->query($sql);

		}
	}else if($product_type == "10"){
		$sql = "select pid from shop_product_sightseeing where pid = '$id'";
		$db->query($sql);
		if($db->total){
			$sql = "update shop_product_sightseeing set 
					rg_ix='$sightseeing_rg_ix'
					where pid='$id' ";
			
			$db->query($sql);
		}else{
			$sql = "insert into shop_product_sightseeing
					(pid,rg_ix,regdate) 
					values
					('$id','$sightseeing_rg_ix',NOW()) ";

			$db->query($sql);

		}

		
	}
	if($admininfo[admin_level] == 9){

		for($i=0;$i<count($icon_check);$i++){
			if($i < count($icon_check)-1){
				$icons .= $icon_check[$i].";";
			}else{
				$icons .= $icon_check[$i];
			}
		}
	
		$icons_str = " icons='$icons', ";
	}
	$sns_btn = serialize($sns_btn);//

	if($supply_company != ""){
		$supply_company_str = " supply_company='$supply_company', ";
	}

	if($inventory_info != ""){
		$inventory_info_str = " inventory_info='$inventory_info', ";
	}

	if($delivery_policy == ""){
		$delivery_policy = "1";
	}

	$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET
			pcode='$pcode', 
			pname='".trim($pname)."', 
			brand = '$brand', 
			brand_name = '$brand_name',
			company='$company',
			buying_company='$buying_company',
			paper_pname='$paper_pname',  
			shotinfo='$shotinfo', 
			buyingservice_coprice='$buyingservice_coprice',
			listprice='$listprice',
			sellprice='$sellprice', 
			coprice='$coprice',
			bimg='$bimg_text',   
			movie='$movie', 
			stock='$stock', 
			safestock='$safestock', 
			search_keyword = '$search_keyword',
			editdate = NOW(),reserve_yn='$reserve_yn',
			sns_btn_yn='$sns_btn_yn',
			sns_btn='$sns_btn',
			surtax_yorn='$surtax_yorn', ".$supply_company_str."  ".$inventory_info_str." ".$icons_str."
			delivery_company='$delivery_company',
			product_type = '$product_type',
			free_delivery_yn='$free_delivery_yn',
			free_delivery_count='$free_delivery_count',
			sell_priod_sdate='$sell_priod_sdate',
			sell_priod_edate='$sell_priod_edate',
			allow_order_type='$allow_order_type',
			allow_order_cnt_byonesell='$allow_order_cnt_byonesell',
			allow_order_cnt_byoneperson='$allow_order_cnt_byoneperson',
			orgin='$orgin',
			make_date='$make_date',
			expiry_date='$expiry_date',
			bs_goods_url = '$bs_goods_url', 
			bs_site = '$bs_site',
			price_policy = '$price_policy', 
			stock_use_yn = '$stock_use_yn',
			delivery_policy='$delivery_policy',
			delivery_product_policy='$delivery_product_policy',
			delivery_package='$delivery_package',
			delivery_price='$delivery_price',
			currency_ix='$currency_ix',
			hotcon_event_id='$hotcon_event_id', 
			hotcon_pcode='$hotcon_pcode',
			basicinfo='$basicinfo',
			exchangeable_yn='$exchangeable_yn', 
			returnable_yn ='$returnable_yn', 
			admin_memo='$admin_memo'
			$commision_str $admin_update $download_img_update $download_desc_update
			Where id = '$id' ";
//echo $sql; 
//exit;
	$db->query($sql);


	//카테고리 정보 수정
	$db->query("update ".TBL_SHOP_PRODUCT_RELATION." set insert_yn = 'N' where pid = '$id'");
	for($i=0;$i<count($category);$i++){
		if($category[$i] == $basic){
			$category_basic = 1;
		}else{
			$category_basic = 0;
		}
		$sql = "select rid from ".TBL_SHOP_PRODUCT_RELATION." where pid = '$id' and cid = '".$category[$i]."' ";
		$db->query($sql);
		$db->fetch();
		if($db->total){
			$db->query("update ".TBL_SHOP_PRODUCT_RELATION." set insert_yn = 'Y' , basic='$category_basic' where rid = '".$db->dt[rid]."'");
		}else{
			$db->query("insert into ".TBL_SHOP_PRODUCT_RELATION." (rid, cid, pid, disp, basic,insert_yn, regdate ) values ('','".$category[$i]."','".$id."','1','".$category_basic."','Y',NOW())");
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


	if($sellprice != $bsellprice || $coprice != $bcoprice ||  $reserve != $breserve){
		$sql = "INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, listprice, sellprice, coprice, reserve,  admin,regdate) ";
		$sql = $sql." values('', '$id','$listprice','$sellprice', '$coprice', '$reserve',  '$admininfo[company_id]',NOW()) ";
		$db->query($sql);
	}

	if($orgin_price != $b_orgin_price || $exchange_rate != $b_exchange_rate || $air_wt != $b_air_wt || $air_shipping != $b_air_shipping || $duty != $b_duty || $clearance_fee != $b_clearance_fee || $bs_fee_rate != $b_bs_fee_rate || $bs_fee != $b_bs_fee){
		$db->query("update shop_product_buyingservice_priceinfo set bs_use_yn = '0' where pid ='".$id."'");

		$sql = "insert into shop_product_buyingservice_priceinfo(bsp_ix,pid,orgin_price,exchange_rate,air_wt,air_shipping,duty,clearance_fee,clearance_type, bs_fee_rate,bs_fee,bs_use_yn, regdate)
						values('$bsp_ix','$id','$orgin_price','$exchange_rate','$air_wt','$air_shipping','$duty','$clearance_fee','$clearance_type','$bs_fee_rate','$bs_fee','1',NOW()) ";

		$db->query($sql);
	}

	$pid = $id;
	
		if($options_price_stock["option_name"]){
			if($options_price_stock["opn_ix"]){
				$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and opn_ix = '".trim($options_price_stock["opn_ix"])."' and option_kind = 'b'");
			}else{
				$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($options_price_stock["option_name"])."' and option_kind = 'b'");
			}

			if($db->total){
				$db->fetch();
				$opn_ix = $db->dt[opn_ix];
				$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
								option_name='".trim($options_price_stock["option_name"])."', option_kind='".$options_price_stock["option_kind"]."', option_type='".$options_price_stock["option_type"]."',
								option_use='".$options_price_stock["option_use"]."'
								where opn_ix = '".$opn_ix."' ";
				$db->query($sql);
			}else{
				$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate)
								VALUES
								('','$pid','".$options_price_stock["option_name"]."','".$options_price_stock["option_kind"]."','".$options_price_stock["type"]."','".$options_price_stock["use"]."',NOW())";

				$db->query($sql);
				$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
				$db->fetch();
				$opn_ix = $db->dt[0];
			}
			//echo $sql."<br>";
			//exit;

			
			$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N' where opn_ix='".$opn_ix."' ";
			//echo $sql."<br><br>";
			$db->query($sql);
			$option_stock_yn = "";
			//for($j=0;$j < count($options_price_stock["option_div"]);$j++){
			foreach($options_price_stock["option_div"] as $opsd_key=>$opsd_value) {
				if($options_price_stock[option_div][$opsd_key]){
					$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($options_price_stock[option_div][$opsd_key])."' and opn_ix = '".$opn_ix."' ");

					if($db->total){
						$db->fetch();
						$opd_ix = $db->dt[id];

						$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
									option_div='".$options_price_stock[option_div][$opsd_key]."',
									option_code='".$options_price_stock[code][$opsd_key]."', 
									option_coprice='".$options_price_stock[coprice][$opsd_key]."',
									option_price='".$options_price_stock[price][$opsd_key]."',
									option_useprice='".$options_price_stock[price][$opsd_key]."', 
									option_stock='".$options_price_stock[stock][$opsd_key]."', 
									option_safestock='".$options_price_stock[safestock][$opsd_key]."' ,
									insert_yn='Y'
									where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";

					}else{
						$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div, option_code,option_coprice,option_price, option_stock, option_safestock) ";
						$sql = $sql." values('','$pid','$opn_ix','".$options_price_stock[option_div][$opsd_key]."','".$options_price_stock[code][$opsd_key]."','".$options_price_stock[coprice][$opsd_key]."','".$options_price_stock[price][$opsd_key]."','".$options_price_stock[stock][$opsd_key]."','".$options_price_stock[safestock][$opsd_key]."') ";
					}

					//echo $sql."<br><br>";
					$db->query($sql);

					if($options_price_stock[stock][$opsd_key] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
						$option_stock_yn = "N";
					}

					if($options_price_stock[stock][$opsd_key] < $options_price_stock[safestock][$opsd_key] && $option_stock_yn == ""){
						$option_stock_yn = "R";
					}
				}
			}
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
			//echo $sql;
			$db->query($sql);
			$db->query("SELECT sum(option_stock) as option_stock,sum(option_safestock) as option_safestock  FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE opn_ix='$opn_ix'");
			$db->fetch();
			$option_stock = $db->dt[option_stock];
			$option_safestock = $db->dt[option_safestock];
			if($sell_ing_cnt == ""){
				$sell_ing_cnt = 0;
			}
			$db->query("update ".TBL_SHOP_PRODUCT." set stock = ".$option_stock." ,safestock = $option_safestock where id ='$pid'");
			if($option_stock_yn){
				$db->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = '$option_stock_yn' where id ='$pid'");
			}

		}else{

			$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_kind = 'b'");

			if($db->total){
				$db->fetch();
				$opn_ix = $db->dt[0];
				$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."'  ";
				$db->query($sql);
			}
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind = 'b' ";
			$db->query($sql);
		}
	//}

	if($option_all_use == "Y"){
		$sql = "select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('s','p','r') ";
		//echo $sql."<br><br>";
		$db->query($sql);
		if($db->total){
			$del_options = $db->fetchall();
			//print_r($del_options);
			for($i=0;$i < count($del_options);$i++){
				$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$del_options[$i][opn_ix]."' and pid = '$pid' ");
			}
		}
		$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('s','p','r') ");
	}else{
		//$db->debug = true;
		//print_r($_POST["options"]);
		//exit;
		$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS." set insert_yn='N' 	where pid = '$pid' and option_kind not in ('b') ";
		//echo $sql."<br><br>";

		$db->query($sql);
		
	
		//for($i=0;$i < count($_POST["options"]);$i++){
		foreach($options as $ops_key=>$ops_value) {
			//echo $options[$i][option_name].":::".$options[$i][opn_ix]."<br>";
			//exit;
			if($options[$ops_key]["option_name"]){
				//$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($options[$i]["option_name"])."' and option_kind in ('s','p','r') ");
				// 2011.10.21 shs 수정
				$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and opn_ix = '".$options[$ops_key]["opn_ix"]."' and option_kind not in ('b')  ");



				if($db->total){
					$db->fetch();
					$opn_ix = $db->dt[opn_ix];
					$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
									option_name='".trim($options[$ops_key]["option_name"])."', option_kind='".$options[$ops_key]["option_kind"]."',
									option_type='".$options[$ops_key]["option_type"]."', option_use='".$options[$ops_key]["option_use"]."',insert_yn='Y'
									where opn_ix = '".$opn_ix."' ";

					$db->query($sql);

				}else{
					$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate)
									VALUES
									('','$pid','".$options[$ops_key]["option_name"]."','".$options[$ops_key]["option_kind"]."','".$options[$ops_key]["option_type"]."','".$options[$ops_key]["option_use"]."',NOW())";
					$db->query($sql);
					$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
					$db->fetch();
					$opn_ix = $db->dt[0];
				}
				//echo $sql."<br><br>";
				//


				$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N'	where opn_ix='".$opn_ix."' ";
				$db->query($sql);
				//for($j=0;$j < count($options[$i]["details"]);$j++){
				foreach($options[$ops_key]["details"] as $od_key=>$od_value) {
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
										option_useprice='".$options[$ops_key][details][$od_key][price]."', 
										option_stock='0', option_safestock='0' ,
										insert_yn='Y'
										where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";
							}else{
								$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div, option_code,option_coprice,option_price, option_stock, option_safestock) ";
								$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$ops_key][details][$od_key][option_div])."','".$options[$ops_key][details][$od_key][code]."','".$options[$ops_key][details][$od_key][coprice]."','".$options[$ops_key][details][$od_key][price]."','0','0') ";
							}
							$db->query($sql);
							//echo $sql."<br><br>";
					}
				}
				$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ");
			}
		}
		$sql = "select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and insert_yn = 'N' ";
		//echo $sql."<br><br>";
		$db->query($sql);
		if($db->total){
			$del_options = $db->fetchall();
			//print_r($del_options);
			for($i=0;$i < count($del_options);$i++){
				$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$del_options[$i][opn_ix]."' and pid = '$pid' ");
			}
		}
		$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and insert_yn = 'N' ");
	}// option_all_use 있는지 여부
	//$db->debug = false;
	//}
	//exit;
	//print_r($rpid);
	//echo print_r($rpid[1]);
	//exit;
		//관련상품업데이트
		if($rpid){
			//$db->debug = true;
			$db->query("update ".TBL_SHOP_RELATION_PRODUCT." set insert_yn = 'N' where pid = '".$pid."'");
			for($i=0;$i<count($rpid[1]);$i++){
				$sql = "select rp_ix from ".TBL_SHOP_RELATION_PRODUCT." where pid = '$pid' and rp_pid = '".$rpid[1][$i]."' ";
				$db->query($sql);
				$db->fetch();
				if($db->total){
					$sql = "update ".TBL_SHOP_RELATION_PRODUCT." set insert_yn = 'Y' , vieworder = '".($i+1)."' where rp_ix ='".$db->dt[rp_ix]."'";
					$db->query($sql);
				}else{
					$sql = "insert into ".TBL_SHOP_RELATION_PRODUCT." (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','".$rpid[1][$i]."','".($i+1)."','Y',NOW())";
					$db->query($sql);
				}
			}
			$db->query("delete from ".TBL_SHOP_RELATION_PRODUCT." where insert_yn = 'N' and pid ='".$pid."' ");
		//$db->debug = false;
		}else{
			$db->query("delete from ".TBL_SHOP_RELATION_PRODUCT." where pid ='".$pid."' ");
		}
		//exit;
		if($display_options){
			$sql = "update ".TBL_SHOP_PRODUCT_DISPLAYINFO." set insert_yn='N'	where pid = '$pid' ";
			$db->query($sql);
			//for($i=0;$i < count($_POST["display_options"]);$i++){
			foreach($_POST["display_options"] as $do_key=>$do_value) {
				if($display_options[$do_key]["dp_title"] && $display_options[$do_key]["dp_desc"]){
					$db->query("SELECT dp_ix FROM ".TBL_SHOP_PRODUCT_DISPLAYINFO." WHERE dp_title = '".$display_options[$do_key]["dp_title"]."' and pid = '$pid' ");

					if($display_options[$do_key]["dp_use"]){
						$dp_use = $display_options[$do_key]["dp_use"];
					}else{
						$dp_use = "0";
					}

					if($db->total){
						$db->fetch();
						$dp_ix = $db->dt[dp_ix];

						$sql = "update ".TBL_SHOP_PRODUCT_DISPLAYINFO." set
										dp_title = '".$display_options[$do_key]["dp_title"]."',dp_desc = '".$display_options[$do_key]["dp_desc"]."',insert_yn = 'Y' ,dp_use = '".$dp_use."'
										where dp_ix = '$dp_ix'";
					}else{
						$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." (dp_ix,pid,dp_title,dp_desc,dp_use, regdate) values('','$pid','".$display_options[$do_key]["dp_title"]."','".$display_options[$do_key]["dp_desc"]."','".$dp_use."',NOW()) ";
						//echo $sql;
					}
					$db->query($sql);
				}
			}
			$db->query("delete from ".TBL_SHOP_PRODUCT_DISPLAYINFO." where pid = '$pid' and insert_yn = 'N' ");
		}


	$db->query("update ".TBL_SHOP_ADDIMAGE." set insert_yn = 'N' WHERE pid = '$pid' ");
	$cnt_addimages=count($addimages);
	//echo count($addimages);exit;
	for($i=0;$i < $cnt_addimages;$i++){
		//kbk if (file_exists($_FILES[addimages][tmp_name][$i][addbimg])){
			//kbk $image_info = getimagesize ($_FILES[addimages][tmp_name][$i][addbimg]);
			//print_r($image_info)."<br />";

			$db->query("SELECT id FROM ".TBL_SHOP_ADDIMAGE." WHERE id = '".$addimages[$i][ad_ix]."' ");


			if(!$db->total){
				if($_FILES[addimages][name][$i][addbimg]){
					$sql = "INSERT INTO ".TBL_SHOP_ADDIMAGE." (id, pid, deepzoom, regdate) values('', '$pid', '".$addimages[$i][add_copy_deepzoomimg]."',NOW()) ";
					$db->query($sql);
					$db->query("SELECT id FROM ".TBL_SHOP_ADDIMAGE." WHERE id=LAST_INSERT_ID()");
					$db->fetch();
					$ad_ix = $db->dt[id];
				}
			}else{
				if($addimages[$i][add_copy_deepzoomimg] == 1){
					$addimg_update_str = ", deepzoom = '1'";
				}else{
					$addimg_update_str = "";
				}

				$db->query("update ".TBL_SHOP_ADDIMAGE." set insert_yn = 'Y' $addimg_update_str WHERE pid = '$pid' and id = '".$addimages[$i][ad_ix]."' ");

				$ad_ix = $addimages[$i][ad_ix];
			}


			//kbk $image_info = getimagesize ($_FILES[addimages][tmp_name][$i][addbimg]);
			//kbk $image_type = substr($image_info['mime'],-3);

			if ($_FILES[addimages][size][$i][addbimg] > 0 || $addimages[$i][add_copy_deepzoomimg] == 1)
			{
				$image_info = getimagesize ($_FILES[addimages][tmp_name][$i][addbimg]);
				$image_type = substr($image_info['mime'],-3);
				if($image_type == "gif"){
					if ($_FILES[addimages][size][$i][addbimg] > 0){
						copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif");
					}
					MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", MIRROR_NONE);
					resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height],'W');

					if($addimages[$i][add_chk_mimg] == 1){
						MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height],'W');
					}

					if($addimages[$i][add_chk_cimg] == 1){
						MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height],'W');
					}
				}else if($image_type == "png"){
					if ($_FILES[addimages][size][$i][addbimg] > 0){
						copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif");
					}
					MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", MIRROR_NONE);
					resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height],'W');

					if($addimages[$i][add_chk_mimg] == 1){
						MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height],'W');
					}

					if($addimages[$i][add_chk_cimg] == 1){
						MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_pnt($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height],'W');
					}

				}else{
					if ($_FILES[addimages][size][$i][addbimg] > 0){
						copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif");
					}

					Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", MIRROR_NONE);
					resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height],'W');

					if($addimages[$i][add_chk_mimg] == 1){
						Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height],'W');
					}

					if($addimages[$i][add_chk_cimg] == 1){
						Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height],'W');
					}
				}

				if($addimages[$i][add_copy_deepzoomimg] == 1){
					if($ad_ix){
						rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg/deepzoom/".$ad_ix);
					}

					$path = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg/deepzoom";

					//if(count($out)>2){
					
					if(!is_dir($path)){
						mkdir($path, 0777);
						chmod($path,0777);
					}else{
						chmod($path,0777);
					}
					//echo $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif";
					//exit;

					$client = new SoapClient("http://".$_SERVER["HTTP_HOST"]."/VESAPI/VESAPIWS.asmx?wsdl=0");
					//print_r($client);
					$params = new stdClass();
					$params->inputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif";
					$params->outputPhysicalPathString = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg/deepzoom/".$ad_ix;

					$response = $client->TilingWithPhysicalPath($params);
					//print_r($response);
					//exit;
				}
			}
			//$db->debug = false;
			if ($_FILES[addimages][size][$i][addmimg] > 0)
			{
				move_uploaded_file($_FILES[addimages][tmp_name][$i][addmimg], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif");
			}

			if ($_FILES[addimages][size][$i][addcimg] > 0)
			{
				move_uploaded_file($_FILES[addimages][tmp_name][$i][addcimg], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif");
			}
		//kbk }
	}
	$db->query("SELECT id FROM ".TBL_SHOP_ADDIMAGE." WHERE insert_yn = 'N' and pid = '$pid' ");

	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);

		if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$db->dt[ad_ix]."_add.gif"))
		{
			unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$db->dt[ad_ix]."_add.gif");
		}

		if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$db->dt[ad_ix]."_add.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$db->dt[ad_ix]."_add.gif");
		}

		if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$db->dt[ad_ix]."_add.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$db->dt[ad_ix]."_add.gif");
		}

	}

	$db->query("delete from  ".TBL_SHOP_ADDIMAGE." WHERE insert_yn = 'N' and pid = '$pid' ");

	//$db->debug = false;
//exit;
	//echo "<script>document.location.href='product_list.php?".$QUERY_STRING."'</script>";
	if($act == "tmp_update"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품수정이 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";
	}else{
		if($mmode == "pop"){
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품수정이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
		}else{
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품수정이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/product_list.php';</script>";
		}
	}
	//echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품수정이 정상적으로 처리 되었습니다.');document.location.href='goods_input.php?mmode=$mmode&id=$pid'</script>";
}



?>
