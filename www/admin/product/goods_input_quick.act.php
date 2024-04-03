<?
include_once("../web.config");
include_once("../../class/database.class");
include_once("../lib/imageResize.lib.php");
include_once("../class/layout.class");

//print_r($_FILES);
//print_r($_POST);


session_start();
$db = new Database;
$db2 = new Database;
//$db->debug = true;
//$db2->debug = true;

if($admininfo[company_id] == ""){
	echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='/admin/admin.php'</script>";
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

		//echo $sql;
		$db->query($sql);
	}

}


//echo "bs_act:".$bs_act;

if ($act == 'insert'){



	//$db->query("SELECT max(vieworder)+1 as max_vieworder FROM ".TBL_SHOP_PRODUCT." ");
	//$db->query("update ".TBL_SHOP_PRODUCT." set vieworder = vieworder + 1 , bestorder = bestorder + 1 , mainorder = mainorder + 1,wmainorder = wmainorder +1");
	$db->query("update ".TBL_SHOP_PRODUCT." set vieworder = vieworder + 1 ");
	//$db->fetch();
	//$vieworder = $db->dt[max_vieworder];
	$vieworder = 1;
	/*$bestorder = 1;
	$mainorder = 1;
	$wmainorder = 1;*/
	$data_text_convert = $basicinfo;
	/*if($one_commission == ""){
		$one_commission = "N";
	}else{
		$one_commission = $one_commission;
	}*/
	if($admininfo[admin_level] == 9){
		$state = "1";
		$disp = "0";
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
	if($product_type == "2"){
		$startdate = $FromYY."-".$FromMM."-".$FromDD." ".$FromHH.":".$FromII.":00";
		$enddate = $ToYY."-".$ToMM."-".$ToDD." ".$ToHH.":".$ToII.":00";
	}

	if(is_array($category)){
		$reg_category = "Y";
	}else{
		$reg_category = "N";
	}

	$sql = "INSERT INTO ".TBL_SHOP_PRODUCT."
					(id,  pname,pcode, brand,brand_name, company,  shotinfo,  buyingservice_coprice, listprice,sellprice,  coprice,reserve_yn, reserve,reserve_rate,  bimg, basicinfo,  icons, state, disp,product_type,startdate,enddate,plusdate,startprice,plus_count, movie, vieworder, admin,stock,safestock,search_keyword,reg_category, surtax_yorn,delivery_company,sell_type,one_commission,commission,free_delivery_yn,free_delivery_count,etc2, bs_goods_url, bs_site, regdate)
					values('', '".strip_tags($pname)."','$pcode', '$brand','$brand_name','$company',  '$shotinfo', '$buyingservice_coprice','$listprice','$sellprice', '$coprice','$reserve_yn', '$reserve','$rate1',  '$bimg_text','$basicinfo',  '$icons', $state, 0,'$product_type','$startdate','$enddate','$enddate','$startprice','$plus_count', '$movie', '$vieworder', '$company_id','$stock','$safestock','$search_keyword','$reg_category','$surtax_yorn','$delivery_company','$sell_type','$one_commission','$commission','$free_delivery_yn','$free_delivery_count','$etc2','$bs_goods_url','$bs_site',NOW()) ";

	//echo($sql);
	//exit;
	$db->query($sql);
	$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT." WHERE id=LAST_INSERT_ID()");
	$db->fetch();
	$INSERT_PRODUCT_ID = $db->dt[0];
	//카테고리 정보 입력
	for($i=0;$i<count($category);$i++){
		if($category[$i] == $basic){
			$basic = 1;
		}else{
			$basic = 0;
		}
		$db->query("insert into shop_product_relation (rid, cid, pid, disp, basic,insert_yn, regdate ) values ('','".$category[$i]."','".$INSERT_PRODUCT_ID."','1','".$basic."','N',NOW())");
	}

	//$image_info = getimagesize ($allimg);
	//$image_type = substr($image_info['mime'],-3);
	$image_db = new Database;
	$image_db->query("select * from shop_image_resizeinfo order by idx");
	$image_info2 = $image_db->fetchall();
	//if ($allimg != "none")
	if ($_FILES['thumb_'. $pu_input_name]['size'][0] > 0 || $allimg_size > 0 || $mode == "copy" || ($bimg_text && $img_url_copy)){
		//워터마크 적용
		if(false) {
			require_once "../lib/class.upload.php";

			$s_water_handle = new Upload($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$INSERT_PRODUCT_ID.".gif");
			$s_water_result = WaterMarkProcess2($s_water_handle, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/");


			@copy($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/watermark/bagic_".$INSERT_PRODUCT_ID.".gif",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$INSERT_PRODUCT_ID.".gif");
			@chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$INSERT_PRODUCT_ID.".gif", 0777);


			$image_type = "gif"; // 워터마크 처리후 마임타입이 gif로 바뀐다.
		}
		//echo "mode : ".$mode;
		//exit;
		if($mode == "copy"){// 상품 복사모드일때는 기존 상품의 이미지를 복사해서 나머지 이미지가 생성됩니다
			$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$bpid.".gif";

			copy($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif");


			$chk_mimg = 1;
			$chk_msimg = 1;
			$chk_simg = 1;
			$chk_cimg = 1;

			$image_info = getimagesize ($basic_img_src);
			$image_type = substr($image_info['mime'],-3);
			$image_width = $image_info[0];

		}else if($_FILES['thumb_'. $pu_input_name]['tmp_name'][0]){
			copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][0], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");
			$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$id.".gif";
			$image_info = getimagesize ($basic_img_src);
			$image_type = substr($image_info['mime'],-3);
		}else{
			//echo "img_url_copy :".$img_url_copy;
		//	exit;
			if($img_url_copy){
				copy($bimg_text, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$INSERT_PRODUCT_ID.".gif");
				$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$INSERT_PRODUCT_ID.".gif";
				//echo $basic_img_src;
				$image_info = getimagesize ($basic_img_src);
				$image_type = substr($image_info['mime'],-3);

				$chk_mimg = 1;
				$chk_msimg = 1;
				$chk_simg = 1;
				$chk_cimg = 1;
			}else{
				$image_info = getimagesize ($allimg);
				$image_type = substr($image_info['mime'],-3);

				$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$INSERT_PRODUCT_ID.".gif";
				copy($allimg, $basic_img_src); // 원본 이미지를 만든다.
			}
			if($image_type == "gif" || $image_type == "GIF"){
				$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$INSERT_PRODUCT_ID.".gif";
				copy($allimg, $basic_img_src); // 원본 이미지를 만든다.

				//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif");
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif",$image_info2[0][width],$image_info2[0][height]);
			}else{
				$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$INSERT_PRODUCT_ID.".gif";
				copy($allimg, $basic_img_src); // 원본 이미지를 만든다.

				//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif");
				Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif",$image_info2[0][width],$image_info2[0][height]);
			}
		}

		if($image_type == "gif" || $image_type == "GIF"){

			if($chk_mimg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$INSERT_PRODUCT_ID.".gif",$image_info2[1][width],$image_info2[1][height]);
			}

			if($chk_msimg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$INSERT_PRODUCT_ID.".gif",$image_info2[2][width],$image_info2[2][height]);
			}

			if($chk_simg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$INSERT_PRODUCT_ID.".gif",$image_info2[3][width],$image_info2[3][height]);
			}

			if($chk_cimg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$INSERT_PRODUCT_ID.".gif",$image_info2[4][width],$image_info2[4][height]);
			}
		}else{
			//copy($allimg, $basic_img_src);


			//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif");
			Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
			resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif",$image_info2[0][width],$image_info2[0][height]);

			if($chk_mimg == 1){
				Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$INSERT_PRODUCT_ID.".gif",$image_info2[1][width],$image_info2[1][height]);
			}

			if($chk_msimg == 1){
				Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$INSERT_PRODUCT_ID.".gif",$image_info2[2][width],$image_info2[2][height]);
			}

			if($chk_simg == 1){
				Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$INSERT_PRODUCT_ID.".gif",$image_info2[3][width],$image_info2[3][height]);
			}

			if($chk_cimg == 1){
				Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$INSERT_PRODUCT_ID.".gif",$image_info2[4][width],$image_info2[4][height]);
			}
		}
	}



	if ($bimg_size > 0){
		copy($bimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif");
	}else if($_FILES['thumb_'. $pu_input_name]['size'][1]){
		copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif");
	}

	//if ($mimg != "none")
	if ($mimg_size > 0){
		copy($mimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$INSERT_PRODUCT_ID.".gif");
	}else if($_FILES['thumb_'. $pu_input_name]['size'][2]){
		copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][2], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$INSERT_PRODUCT_ID.".gif");
	}

	//if ($msimg != "none")
	if ($msimg_size > 0){
		copy($msimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$INSERT_PRODUCT_ID.".gif");
	}else if($_FILES['thumb_'. $pu_input_name]['size'][3]){
		copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][3], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$INSERT_PRODUCT_ID.".gif");
	}

	//if ($simg != "none")
	if ($simg_size > 0){
		copy($simg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$INSERT_PRODUCT_ID.".gif");
	}else if($_FILES['thumb_'. $pu_input_name]['size'][4]){
		copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][4], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$INSERT_PRODUCT_ID.".gif");
	}

	//if ($cimg != "none")
	if ($cimg_size > 0){
		copy($cimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$INSERT_PRODUCT_ID.".gif");
	}else if($_FILES['thumb_'. $pu_input_name]['size'][5]){
		copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][5], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$INSERT_PRODUCT_ID.".gif");
	}

	$sql = "INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, sellprice, coprice, reserve,  admin,regdate) ";
	$sql .= " values('', '".$INSERT_PRODUCT_ID."','$sellprice', '$coprice', '$reserve',  '".$admininfo[company_id]."',NOW()) ";

	$db2->query($sql);

	$sql = "insert into shop_product_buyingservice_priceinfo(bsp_ix,pid,orgin_price,exchange_rate,air_wt,air_shipping,duty,clearance_fee,clearance_type,bs_fee_rate,bs_fee,regdate)
					values('$bsp_ix','".$INSERT_PRODUCT_ID."','$orgin_price','$exchange_rate','$air_wt','$air_shipping','$duty','$clearance_fee','$clearance_type','$bs_fee_rate','$bs_fee',NOW()) ";
	//echo $sql;
	$db2->query($sql);


	$data_text_convert = str_replace("\\","",$data_text_convert);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$INSERT_PRODUCT_ID."/";

	//if(count($out)>2){
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
							$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

							@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img));
							if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
								unlink($local_img_path);
							}

							$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
						}else{
							if(substr_count($img,$DOCUMENT_ROOT)){
								//$img = $DOCUMENT_ROOT.$img;
								if(@copy($DOCUMENT_ROOT.$img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($DOCUMENT_ROOT.$img))){
									$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
								}
							}else{
								if(@copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img))){
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
	$pid = $INSERT_PRODUCT_ID;

	if($options_price_stock["option_name"]){

		if($options_price_stock["use"]){
			$options_price_stock_use = $options_price_stock["use"];
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

					//$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1) ";
					$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price, option_stock, option_safestock, option_etc1) ";
					//$sql = $sql." values('','$pid','$opn_ix','".$options_price_stock[option_div][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[stock][$j]."','".$options_price_stock[safestock][$j]."','".$options_price_stock[etc1][$j]."') ";
					$sql = $sql." values('','$pid','$opn_ix','".$options_price_stock[option_div][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[stock][$j]."','".$options_price_stock[safestock][$j]."','".$options_price_stock[etc1][$j]."') ";

					$db->query($sql);

					if($options_price_stock[stock][$j] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
						$option_stock_yn = "N";
					}

					if($options_price_stock[stock][$j] < $options_price_stock[safestock][$j] && $option_stock_yn == ""){
						$option_stock_yn = "R";
					}
			}
		}

		$db->query("update ".TBL_SHOP_PRODUCT." set option_stock_yn = '$option_stock_yn' where id ='$pid'");

	}
	//}

	if($option_all_use == "Y"){
		$sql = "select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('s','p') ";
		//echo $sql."<br><br>";
		$db->query($sql);
		if($db->total){
			$del_options = $db->fetchall();
			//print_r($del_options);
			for($i=0;$i < count($del_options);$i++){
				$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$del_options[$i][opn_ix]."' and pid = '$pid' ");
			}
		}
		$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('s','p') ");
	}else{
		//print_r($options);
		//exit;
		for($i=0;$i < count($_POST["options"]);$i++){

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

							//$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1) ";
							$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price, option_stock, option_safestock, option_etc1) ";
							//$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$i][details][$j][option_div])."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','0','0','".$options[$i][details][$j][etc1]."') ";
							$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$i][details][$j][option_div])."','".$options[$i][details][$j][price]."','0','0','".$options[$i][details][$j][etc1]."') ";

							$db->query($sql);
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
	//$db->debug = true;
	//print_r($addimages);
	for($i=0;$i < count($addimages);$i++){
			$image_info = getimagesize ($_FILES[addimages][tmp_name][$i][addbimg]);

			if($_FILES[addimages][name][$i][addbimg]){
				$sql = "INSERT INTO ".TBL_SHOP_ADDIMAGE." (id, pid, regdate) values('', '$pid', NOW()) ";

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
					copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif");
					resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height]);

					if($addimages[$i][add_chk_mimg] == 1){
						MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height]);
					}

					if($addimages[$i][add_chk_cimg] == 1){
						MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height]);
					}

				}else{
					copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif");

					//Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$id."_add.gif", $_SERVER["DOCUMENT_ROOT"]."/shop/images/addimg/c_".$id."_add.gif", MIRROR_NONE);
					resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height]);

					if($addimages[$i][add_chk_mimg] == 1){
						Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height]);
					}

					if($addimages[$i][add_chk_cimg] == 1){
						Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height]);
					}
				}
			}
			//$db->debug = false;
			if ($_FILES[addimages][size][$i][addmimg] > 0)
			{
				move_uploaded_file($_FILES[addimages][tmp_name][$i][addmimg], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$ad_ix."_add.gif");
			}

			if ($_FILES[addimages][size][$i][addcimg] > 0)
			{
				move_uploaded_file($_FILES[addimages][tmp_name][$i][addcimg], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$ad_ix."_add.gif");
			}
	}
	//exit;

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
					$db2->query("INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." SELECT '' as id, '$INSERT_PRODUCT_ID' as pid, '$opn_ix' as opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1, option_useprice FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$bpid' and opn_ix = '".$db->dt[opn_ix]."' ");
				}
			}
		}


		////////////////////////////////////////// 디스플레이 정보 복사 루틴 //////////////////////////////////////////////////////////

		$db->query("SELECT pid FROM ".TBL_SHOP_PRODUCT_DISPLAYINFO." WHERE pid='$bpid' ");

		if($db->total){
			$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." SELECT '' as dp_ix,'$INSERT_PRODUCT_ID' as pid,dp_title,dp_desc,NOW() as regdate FROM ".TBL_SHOP_PRODUCT_DISPLAYINFO." where pid='$bpid' ";
			$db->query($sql);
		}
*/

		////////////////////////////////////////// 관련 상품  복사 루틴 //////////////////////////////////////////////////////////

		$db->query("SELECT pid FROM ".TBL_SHOP_RELATION_PRODUCT." WHERE pid='$bpid' ");

		if($db->total){
			$sql = "insert into ".TBL_SHOP_RELATION_PRODUCT." SELECT '' as rp_ix, '$INSERT_PRODUCT_ID' as pid,rp_pid,vieworder,NOW() as regdate FROM ".TBL_SHOP_RELATION_PRODUCT." where pid='$bpid' ";
			$db->query($sql);
		}
	}

	$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET basicinfo = '$basicinfo' WHERE id='$INSERT_PRODUCT_ID'");

	if(!$bs_act){
	//header("Location:./goods_input_quick.php?id=$INSERT_PRODUCT_ID");
		echo "<script language='javascript'>document.location.href='goods_input_quick.php?id=".$INSERT_PRODUCT_ID."';</script>";
	}
}

if ($act == "delete")
{
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_$id.gif");
	}

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_$id.gif");
	}
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_$id.gif")){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_$id.gif");
	}

	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='$id'");
	$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='$id'");
	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$id'");
	$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='$id'");
	$db->query("DELETE FROM ".TBL_SHOP_RELATION_PRODUCT." WHERE pid = '$id'");
	//$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE pid='$id'");




	if($id && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id")){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id");
	}

	echo "<script language='javascript'>document.location.href='product_list.php?".$QUERY_STRING."';</script>";

	//header("Location:../product_list.php");
}

if ($act == "update")
{


	//$image_info = getimagesize ($allimg);
	//$image_type = substr($image_info['mime'],-3);
	//$image_width = $image_info[0];
	$image_db = new Database;
	$image_db->query("select * from shop_image_resizeinfo order by idx");
	$image_info2 = $image_db->fetchall();

	if($_FILES['thumb_'. $pu_input_name]['size'][0] != 0 || $allimg_size != 0 ||  ($bimg_text && $img_url_copy)){

		if($bimg_text && $img_url_copy){
			copy($bimg_text, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");
			$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$id.".gif";
			$image_info = getimagesize ($basic_img_src);
			$image_type = substr($image_info['mime'],-3);
		}else if($_FILES['thumb_'. $pu_input_name]['tmp_name'][0]){
			copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][0], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");
			$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$id.".gif";
			$image_info = getimagesize ($basic_img_src);
			$image_type = substr($image_info['mime'],-3);
		}else{
			$image_info = getimagesize ($allimg);
			$image_type = substr($image_info['mime'],-3);
			$image_width = $image_info[0];
			copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");
		}

		//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");


		//워터마크 적용
		if(false) {
			require_once "../lib/class.upload.php";

			$s_water_handle = new Upload($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");
			$s_water_result = WaterMarkProcess2($s_water_handle, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/");


			@copy($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/watermark/bagic_".$id.".gif",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");
			@chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", 0777);


			$image_type = "gif"; // 워터마크 처리후 마임타입이 gif로 바뀐다.
		}

		if($image_type == "gif"){

		//if(substr($allimg_name, -3) == "gif"){
			//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");



			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif");
			}

			//copy($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif");
			MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif", MIRROR_NONE);
			resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif",$image_info2[0][width],$image_info2[0][height]);

			if($chk_mimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif",$image_info2[1][width],$image_info2[1][height]);
			}

			if($chk_msimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif",$image_info2[2][width],$image_info2[2][height]);
			}

			if($chk_simg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif",$image_info2[3][width],$image_info2[3][height]);
			}

			if($chk_cimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif",$image_info2[4][width],$image_info2[4][height]);
			}

		}else{
			//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");



			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif");
			}


			//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif");
			Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif", MIRROR_NONE);
			resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif",$image_info2[0][width],$image_info2[0][height]);

			if($chk_mimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif",$image_info2[1][width],$image_info2[1][height]);
			}

			if($chk_msimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif",$image_info2[2][width],$image_info2[2][height]);
			}

			if($chk_simg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif",$image_info2[3][width],$image_info2[3][height]);
			}

			if($chk_cimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif",$image_info2[4][width],$image_info2[4][height]);
			}
		}
	}




	if ($bimg_size > 0){
		copy($bimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif");
	}else if($_FILES['thumb_'. $pu_input_name]['size'][1]){
		copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif");
	}

	if ($mimg_size > 0)	{
		copy($mimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif");
	}else if($_FILES['thumb_'. $pu_input_name]['size'][2]){
		copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][2], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif");
	}

	if ($msimg_size > 0)	{
		copy($msimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif");
	}else if($_FILES['thumb_'. $pu_input_name]['size'][3]){
		copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][3], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif");
	}

	if ($simg_size > 0)	{
		copy($simg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif");
	}else if($_FILES['thumb_'. $pu_input_name]['size'][4]){
		copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][4], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif");
	}

	if ($cimg_size > 0)	{
		copy($cimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif");
	}else if($_FILES['thumb_'. $pu_input_name]['size'][5]){
		copy($_FILES['thumb_'. $pu_input_name]['tmp_name'][5], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif");
	}



	$data_text_convert = $basicinfo;
	$data_text_convert = str_replace("\\","",$data_text_convert);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$id."/";

	$INSERT_PRODUCT_ID = $id ;
	//echo $path;

	//if(count($out)>2){
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
							$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

							@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($img));
							if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
								unlink($local_img_path);
							}

							$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
						}else{
							if(substr_count($img,$DOCUMENT_ROOT)){
								//$img = $DOCUMENT_ROOT.$img;
								if(@copy($DOCUMENT_ROOT.$img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($DOCUMENT_ROOT.$img))){
									$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
								}
							}else{
								if(@copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($img))){
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
	if($admininfo[admin_level] == 8){
		//$state = "6"; // 입점업체 변경시 승인신청중으로 변경 되는 부분
	}

	if($admininfo[admin_level] == 9){
		$commision_str =",etc2='$etc2',one_commission='$one_commission',commission='$commission' , reserve='$reserve',reserve_rate='$rate1',
					 state='$state', disp='$disp' ";
		//$disp_str = " ,disp='$disp'";

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
	}

	if($product_type == "2"){
		$startdate = $FromYY."-".$FromMM."-".$FromDD." ".$FromHH.":".$FromII.":00";
		$enddate = $ToYY."-".$ToMM."-".$ToDD." ".$ToHH.":".$ToII.":00";

	}


	$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET
					pcode='$pcode', pname='$pname', brand = '$brand', brand_name = '$brand_name',company='$company', shotinfo='$shotinfo', buyingservice_coprice='$buyingservice_coprice',listprice='$listprice',sellprice='$sellprice', coprice='$coprice',
					bimg='$bimg_text',basicinfo='$basicinfo',  icons='$icons', movie='$movie',
					stock='$stock', safestock='$safestock', search_keyword = '$search_keyword',
					editdate = NOW(),reserve_yn='$reserve_yn',
					surtax_yorn='$surtax_yorn',
					delivery_company='$delivery_company',
					product_type = '$product_type',startdate='$startdate',enddate='$enddate',plusdate='$enddate',startprice='$startprice',free_delivery_yn='$free_delivery_yn',free_delivery_count='$free_delivery_count',plus_count='$plus_count',
					bs_goods_url = '$bs_goods_url', bs_site = '$bs_site'
					$commision_str $admin_update
					Where id = $id ";

	$db->query($sql);






//echo $cid0_2."<br>".$cid1_2."<br>".$cid2_2."<br>".$cid3_2;
	//exit;
	//echo $rid_1;
	//exit;
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
		$sql = "insert into shop_product_buyingservice_priceinfo(bsp_ix,pid,orgin_price,exchange_rate,air_wt,air_shipping,duty,clearance_fee,clearance_type, bs_fee_rate,bs_fee,regdate)
						values('$bsp_ix','$id','$orgin_price','$exchange_rate','$air_wt','$air_shipping','$duty','$clearance_fee','$clearance_type','$bs_fee_rate','$bs_fee',NOW()) ";

		$db->query($sql);
	}

	$pid = $id;


		if($options_price_stock["option_name"]){
			$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($options_price_stock["option_name"])."' and option_kind = 'b'");

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
			for($j=0;$j < count($options_price_stock["option_div"]);$j++){
				if($options_price_stock[option_div][$j]){
					$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($options_price_stock[option_div][$j])."' and opn_ix = '".$opn_ix."' ");

					if($db->total){
						$db->fetch();
						$opd_ix = $db->dt[id];
						/*
						$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
									option_div='".$options_price_stock[option_div][$j]."',option_price='".$options_price_stock[price][$j]."',option_m_price='".$options_price_stock[price][$j]."',option_d_price='".$options_price_stock[price][$j]."',
									option_a_price='".$options_price_stock[price][$j]."',option_useprice='".$options_price_stock[price][$j]."', option_stock='".$options_price_stock[stock][$j]."', option_safestock='".$options_price_stock[safestock][$j]."' ,
									option_etc1='".$options_price_stock[etc1][$j]."', insert_yn='Y'
									where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";
						*/
						$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
									option_div='".$options_price_stock[option_div][$j]."',option_price='".$options_price_stock[price][$j]."', option_stock='".$options_price_stock[stock][$j]."', option_safestock='".$options_price_stock[safestock][$j]."' ,
									option_etc1='".$options_price_stock[etc1][$j]."', insert_yn='Y'
									where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";
					}else{
						//$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1) ";
						$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price, option_stock, option_safestock, option_etc1) ";
						//$sql = $sql." values('','$pid','$opn_ix','".$options_price_stock[option_div][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[stock][$j]."','".$options_price_stock[safestock][$j]."','".$options_price_stock[etc1][$j]."') ";
						$sql = $sql." values('','$pid','$opn_ix','".$options_price_stock[option_div][$j]."','".$options_price_stock[price][$j]."','".$options_price_stock[stock][$j]."','".$options_price_stock[safestock][$j]."','".$options_price_stock[etc1][$j]."') ";
					}

					//echo $sql."<br><br>";
					$db->query($sql);

					if($options_price_stock[stock][$j] == 0 && ($option_stock_yn == "" || $option_stock_yn == "R")){
						$option_stock_yn = "N";
					}

					if($options_price_stock[stock][$j] < $options_price_stock[safestock][$j] && $option_stock_yn == ""){
						$option_stock_yn = "R";
					}
				}
			}
			$sql = "delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$opn_ix."' and insert_yn = 'N' ";
			//echo $sql;
			$db->query($sql);

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
		$sql = "select opn_ix from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('s','p') ";
		//echo $sql."<br><br>";
		$db->query($sql);
		if($db->total){
			$del_options = $db->fetchall();
			//print_r($del_options);
			for($i=0;$i < count($del_options);$i++){
				$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where opn_ix='".$del_options[$i][opn_ix]."' and pid = '$pid' ");
			}
		}
		$db->query("delete from ".TBL_SHOP_PRODUCT_OPTIONS." where pid = '$pid' and option_kind in ('s','p') ");
	}else{
		$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS." set insert_yn='N' 	where pid = '$pid' and option_kind in ('s','p') ";
		//echo $sql."<br><br>";

		$db->query($sql);
		//$db->debug = true;

		for($i=0;$i < count($_POST["options"]);$i++){
			//echo $options[$i][option_name].":::".$options[$i][opn_ix]."<br>";
			//exit;
			if($options[$i]["option_name"]){
				$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and option_name = '".trim($options[$i]["option_name"])."' and option_kind in ('s','p') ");
				//$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid = '$pid' and opn_ix = '".$options[$i]["opn_ix"]."' and option_kind in ('s','p') ");



				if($db->total){
					$db->fetch();
					$opn_ix = $db->dt[opn_ix];
					$sql = "update  ".TBL_SHOP_PRODUCT_OPTIONS." set
									option_name='".trim($options[$i]["option_name"])."', option_kind='".$options[$i]["option_kind"]."',
									option_type='".$options[$i]["option_type"]."', option_use='".$options[$i]["option_use"]."',insert_yn='Y'
									where opn_ix = '".$opn_ix."' ";

					$db->query($sql);

				}else{
					$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." (opn_ix, pid, option_name, option_kind, option_type, option_use, regdate)
									VALUES
									('','$pid','".$options[$i]["option_name"]."','".$options[$i]["option_kind"]."','".$options[$i]["option_type"]."','".$options[$i]["option_use"]."',NOW())";
					$db->query($sql);
					$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
					$db->fetch();
					$opn_ix = $db->dt[0];
				}
				//echo $sql."<br><br>";
				//


				$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set insert_yn='N'	where opn_ix='".$opn_ix."' ";
				$db->query($sql);
				for($j=0;$j < count($options[$i]["details"]);$j++){
					if($options[$i][details][$j][option_div]){
							$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE option_div = '".trim($options[$i][details][$j][option_div])."' and opn_ix = '".$opn_ix."' ");

							if($db->total){
								$db->fetch();
								$opd_ix = $db->dt[id];
								/*
								$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
										option_div='".$options[$i][details][$j][option_div]."',option_price='".$options[$i][details][$j][price]."',option_m_price='".$options[$i][details][$j][price]."',option_d_price='".$options[$i][details][$j][price]."',
										option_a_price='".$options[$i][details][$j][price]."',option_useprice='".$options[$i][details][$j][price]."', option_stock='0', option_safestock='0' ,
										option_etc1='".$options[$i][details][$j][etc1]."', insert_yn='Y'
										where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";
								*/
								$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set
										option_div='".$options[$i][details][$j][option_div]."',option_price='".$options[$i][details][$j][price]."', option_stock='0', option_safestock='0' ,
										option_etc1='".$options[$i][details][$j][etc1]."', insert_yn='Y'
										where id ='".$opd_ix."' and opn_ix = '".$opn_ix."'";
							}else{
								//$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_m_price, option_d_price, option_a_price, option_stock, option_safestock, option_etc1) ";
								$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price, option_stock, option_safestock, option_etc1) ";
								//$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$i][details][$j][option_div])."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','".$options[$i][details][$j][price]."','0','0','".$options[$i][details][$j][etc1]."') ";
								$sql = $sql." values('','$pid','".$opn_ix."','".trim($options[$i][details][$j][option_div])."','".$options[$i][details][$j][price]."','0','0','".$options[$i][details][$j][etc1]."') ";
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
	//print_r($display_options);
		//관련상품업데이트
		if($rpid){
			for($i=0;$i<count($rpid[1]);$i++){
				$sql = "select rp_ix from ".TBL_SHOP_RELATION_PRODUCT." where pid = '$pid' and rp_pid = '".$rpid[1][$i]."' ";
				$db->query($sql);
				$db->fetch();
				if($db->total){
					$sql = "update ".TBL_SHOP_RELATION_PRODUCT." set insert_yn = 'Y' and vieworder = '$i' where rp_ix ='".$db->dt[rid]."'";
					$db->query($sql);
				}else{
					$sql = "insert into ".TBL_SHOP_RELATION_PRODUCT." (rp_ix,pid,rp_pid,vieworder,insert_yn,regdate) values ('','$pid','".$rpid[1][$i]."','".$i."','Y',NOW())";
					$db->query($sql);
				}
			}
			$db->query("delete from ".TBL_SHOP_RELATION_PRODUCT." where insert_yn = 'N' and pid ='".$pid."' ");
			$db->query("update ".TBL_SHOP_RELATION_PRODUCT." set insert_yn = 'N' where pid = '".$pid."'");
		}
		if($display_options){
			$sql = "update ".TBL_SHOP_PRODUCT_DISPLAYINFO." set insert_yn='N'	where pid = '$pid' ";
			$db->query($sql);
			for($i=0;$i < count($_POST["display_options"]);$i++){
				if($display_options[$i]["dp_title"] && $display_options[$i]["dp_desc"]){
					$db->query("SELECT dp_ix FROM ".TBL_SHOP_PRODUCT_DISPLAYINFO." WHERE dp_title = '".$display_options[$i]["dp_title"]."' and pid = '$pid' ");

					if($display_options[$i]["dp_use"]){
						$dp_use = $display_options[$i]["dp_use"];
					}else{
						$dp_use = "0";
					}

					if($db->total){
						$db->fetch();
						$dp_ix = $db->dt[dp_ix];

						$sql = "update ".TBL_SHOP_PRODUCT_DISPLAYINFO." set
										dp_title = '".$display_options[$i]["dp_title"]."',dp_desc = '".$display_options[$i]["dp_desc"]."',insert_yn = 'Y' ,dp_use = '".$dp_use."'
										where dp_ix = '$dp_ix'";
					}else{
						$sql = "insert into ".TBL_SHOP_PRODUCT_DISPLAYINFO." (dp_ix,pid,dp_title,dp_desc,dp_use, regdate) values('','$pid','".$display_options[$i]["dp_title"]."','".$display_options[$i]["dp_desc"]."','".$dp_use."',NOW()) ";
						//echo $sql;
					}
					$db->query($sql);
				}
			}
			$db->query("delete from ".TBL_SHOP_PRODUCT_DISPLAYINFO." where pid = '$pid' and insert_yn = 'N' ");
		}


	$db->query("update ".TBL_SHOP_ADDIMAGE." set insert_yn = 'N' WHERE pid = '$pid' ");

	for($i=0;$i < count($addimages);$i++){

			$image_info = getimagesize ($_FILES[addimages][tmp_name][$i][addbimg]);
			//print_r($image_info);

			$db->query("SELECT id FROM ".TBL_SHOP_ADDIMAGE." WHERE id = '".$addimages[$i][ad_ix]."' ");


			if(!$db->total){
				if($_FILES[addimages][name][$i][addbimg]){
					$sql = "INSERT INTO ".TBL_SHOP_ADDIMAGE." (id, pid, regdate) values('', '$pid', NOW()) ";
					$db->query($sql);
					$db->query("SELECT id FROM ".TBL_SHOP_ADDIMAGE." WHERE id=LAST_INSERT_ID()");
					$db->fetch();
					$ad_ix = $db->dt[id];
				}
			}else{
				$db->query("update ".TBL_SHOP_ADDIMAGE." set insert_yn = 'Y' WHERE pid = '$pid' and id = '".$addimages[$i][ad_ix]."' ");

				$ad_ix = $addimages[$i][ad_ix];
			}


			$image_info = getimagesize ($_FILES[addimages][tmp_name][$i][addbimg]);
			$image_type = substr($image_info['mime'],-3);

			if ($_FILES[addimages][size][$i][addbimg] > 0)
			{
				if($image_type == "gif"){
					copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif");
					resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height]);

					if($addimages[$i][add_chk_mimg] == 1){
						MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height]);
					}

					if($addimages[$i][add_chk_cimg] == 1){
						MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height]);
					}

				}else{
					copy($_FILES[addimages][tmp_name][$i][addbimg], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif");

					//Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$id."_add.gif", $_SERVER["DOCUMENT_ROOT"]."/shop/images/addimg/c_".$id."_add.gif", MIRROR_NONE);
					resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height]);

					if($addimages[$i][add_chk_mimg] == 1){
						Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height]);
					}

					if($addimages[$i][add_chk_cimg] == 1){
						Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$ad_ix."_add.gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height]);
					}
				}
			}
			//$db->debug = false;
			if ($_FILES[addimages][size][$i][addmimg] > 0)
			{
				move_uploaded_file($_FILES[addimages][tmp_name][$i][addmimg], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$ad_ix."_add.gif");
			}

			if ($_FILES[addimages][size][$i][addcimg] > 0)
			{
				move_uploaded_file($_FILES[addimages][tmp_name][$i][addcimg], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$ad_ix."_add.gif");
			}
	}
	$db->query("SELECT id FROM ".TBL_SHOP_ADDIMAGE." WHERE insert_yn = 'N' and pid = '$pid' ");

	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[ad_ix]."_add.gif"))
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/b_".$db->dt[ad_ix]."_add.gif");
		}

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[ad_ix]."_add.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/m_".$db->dt[ad_ix]."_add.gif");
		}

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[ad_ix]."_add.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/addimg/c_".$db->dt[ad_ix]."_add.gif");
		}

	}

	$db->query("delete from  ".TBL_SHOP_ADDIMAGE." WHERE insert_yn = 'N' and pid = '$pid' ");

	//$db->debug = false;
//exit;
	//echo "<script>document.location.href='product_list.php?".$QUERY_STRING."'</script>";
	echo "<script>document.location.href='goods_input_quick.php?id=$pid'</script>";
}



?>