<?
include_once("../../class/database.class");
include_once("../lib/imageResize.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
include_once("../product/goods.options.lib.php");
@include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

session_start();
$db = new Database;

if($admininfo[company_id] == ""){
	echo "<script language='JavaScript' src='../_language/language.php'></Script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}

if($act == 'disp_update'){
	$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET disp='".$change_disp."' Where id = ".$pid." ";
	$db->query($sql);
}

if ($act == 'insert'){

	if(!file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product/")){
		mkdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product/");
		chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product/",0777);
	}

	if(!file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/")){
		mkdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/");
		chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product_detail/",0777);
	}

	//예외처리
	$basicinfo = str_replace("''","",$basicinfo);
	$basicinfo = str_replace("'","&#39;",$basicinfo);
	$basicinfo = str_replace('"',"&quot;",$basicinfo);
	$basicinfo = "<p>".$basicinfo."</p>";

	//DEFAULT
	$product_type="0";
	$state = "1";
	$disp = "0";
	$company_id = $_SESSION["admininfo"]["company_id"];
	$reg_category = "Y";
	$wholesale_reserve_yn = "N";
	$reserve_yn = "N";
	$delivery_policy = "1"; //상품개별정책 사용 여부
	$wholesale_sellprice = $wholesale_price;
	$sellprice = $listprice;
	$surtax_yorn="Y";
	$delivery_company = "MI";
	$one_commission ="N";
	$cupon_use_yn = "Y";
	$stock_use_yn = "Q";
	$free_delivery_yn ="N";
	$db->query("SELECT max(vieworder)+1 as max_vieworder FROM ".TBL_SHOP_PRODUCT." ");
	$db->fetch();
	$vieworder = $db->dt[max_vieworder];

	
	$sql = "INSERT INTO ".TBL_SHOP_PRODUCT."
				(id,  pname,pcode, company,  shotinfo,  buyingservice_coprice, wholesale_price, wholesale_sellprice, listprice,sellprice,  coprice,wholesale_reserve_yn, reserve_yn,  basicinfo,  state, disp,product_type,  vieworder, admin, search_keyword,reg_category,  surtax_yorn,delivery_company,one_commission,cupon_use_yn,stock_use_yn, delivery_policy,free_delivery_yn,
				origin, regdate,reg_charger_ix,reg_charger_name)
				values('', '".strip_tags(trim($pname))."','$pcode', '$company', '$shotinfo', '$buyingservice_coprice','$wholesale_price','$wholesale_sellprice','$listprice','$sellprice', '$coprice','$wholesale_reserve_yn', '$reserve_yn', '$basicinfo', $state, '$disp','$product_type', '$vieworder', '$company_id','$search_keyword','$reg_category','$surtax_yorn','$delivery_company','$one_commission','$cupon_use_yn','$stock_use_yn','$delivery_policy','$free_delivery_yn',
				'$origin',NOW(),'".$_SESSION["admininfo"]["charger_ix"]."','".$_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")') ";

	$db->sequences = "SHOP_GOODS_SEQ";
	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$pid = $db->last_insert_id;
	}else{
		$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT." WHERE id=LAST_INSERT_ID()");
		$db->fetch();
		$pid = $db->dt[id];
	}
	
	//오라클에서 unix_timestamp는 FUNCTION으로 만듬 FUNCTION은 맨아래에 주석처리 해놓음 2013-03-22 홍진영
	$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET regdate_desc = unix_timestamp(regdate)*-1 WHERE id='$pid'");

	$sql = "insert into shop_product_relation (rid, cid, pid, disp, basic,insert_yn, regdate ) values ('','".$cid2."','".$pid."','1','1','Y',NOW())";
	$db->sequences = "SHOP_GOODS_LINK_SEQ";
	$db->query($sql);


	//옵션 구조 만들어주기
	$stock_options["option_name"] = "색상/사이즈";
	$stock_options["option_kind"] = "b";
	$stock_options["option_use"] = "1";
	$stock_options["option_type"] = "9";

	for($i=0;$i<count($option_div);$i++){
		if($option_div[$i] !=""){
			$stock_options["details"][$i]["option_div"] = $option_div[$i];
			$stock_options["details"][$i]["stock"] = $option_stock[$i];
		}
	}

	OptionUpdate($db, $pid, $stock_options,"b");

	
	$sql = "INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, listprice, sellprice, coprice, reserve,  company_id, charger_info, regdate) ";
	$sql .= " values('', '".$pid."','$listprice','$sellprice', '$coprice', '$reserve',  '".$admininfo[company_id]."','[".$admininfo[company_name]."] ".$admininfo[charger]."(".$admininfo[charger_id].")', NOW()) ";
	$db->sequences = "SHOP_PRICEINFO_SEQ";
	$db->query($sql);

	//이미지 등록
	$db->query("select * from shop_image_resizeinfo order by idx");
	$image_info2 = $db->fetchall();

	$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $pid, 'Y');
	$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $pid, 'Y');
		
	//exit;
	for($i=0;$i < 6;$i++){
		
		if($_FILES["img_list"]["size"][$i]){
			//echo "<br/>img_list : ".$i;
			
			if($i == $basic_img){ //기본이미지일때
				$image_info = getimagesize ($_FILES["img_list"]["tmp_name"][$i]);
				$image_type = substr($image_info['mime'],-3);

				if($image_info[0] > $image_info[1]){
					$image_resize_type = "W";
				}else{
					$image_resize_type = "H";
				}

				$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/basic_".$pid.".gif";
				copy($_FILES["img_list"]["tmp_name"][$i], $basic_img_src); // 원본 이미지를 만든다.
				chmod($basic_img_src,0777);
				
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

				$image_info = getimagesize ($_FILES["img_list"]["tmp_name"][$i]);
				$image_type = substr($image_info['mime'],-3);

				if($image_info[0] > $image_info[1]){
					$image_resize_type = "W";
				}else{
					$image_resize_type = "H";
				}

				if($image_type == "gif"){
					copy($_FILES["img_list"]["tmp_name"][$i], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif");
					MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", MIRROR_NONE);
					resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height],$image_resize_type);

					MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif", MIRROR_NONE);
					resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height],$image_resize_type);

					MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif", MIRROR_NONE);
					resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height],$image_resize_type);

				}else{
					copy($_FILES["img_list"]["tmp_name"][$i], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif");
					Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/basic_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", MIRROR_NONE);
					resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif",$image_info2[0][width],$image_info2[0][height],$image_resize_type);

					Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif", MIRROR_NONE);
					resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/m_".$ad_ix."_add.gif",$image_info2[1][width],$image_info2[1][height],$image_resize_type);

					Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/b_".$ad_ix."_add.gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif", MIRROR_NONE);
					resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg".$adduploaddir."/c_".$ad_ix."_add.gif",$image_info2[4][width],$image_info2[4][height],$image_resize_type);
				}
			}
		}
	}

	//상세 이미지 등록및 처리
	$detail_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/";

	if(!is_dir($detail_img_src)){
		mkdir($detail_img_src, 0777);
		chmod($detail_img_src,0777);
	}else{
		chmod($detail_img_src,0777);
	}
	
	for($i=0;$i < 6;$i++){
		
		if($_FILES["detail_img_list"]["size"][$i]){
			copy($_FILES["detail_img_list"]["tmp_name"][$i], $detail_img_src."detail_".$i.".gif"); // 원본 이미지를 만든다.
			$basicinfo .= "<br/><img src=\"".$admin_config[mall_data_root]."/images/product".$uploaddir."/product_detail/detail_".$i.".gif\"  />";
		}
	}
	
	$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET basicinfo = '".$basicinfo."' WHERE id='$pid'");

	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품등록이 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";
}

?>
