<?
@include_once("../web.config");
include_once("../../class/database.class");
include_once("../lib/imageResize.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
include_once("../inventory/inventory.lib.php");

session_start();
$db = new Database;
$db2 = new Database;
//$db->debug = true;
//$db2->debug = true;

$inventory_cid_info = array ('cid0_1','cid1_1','cid2_1','cid3_1');
$goods_setup_info = getBasicSellerSetup($admininfo[company_id]."_goods_multi_price_setup");

if($admininfo[company_id] == ""){
	echo "<script language='JavaScript' src='../_language/language.php'></Script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}

if ($act == "update" || $act == "tmp_update"){

	if(!file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory/")){
		mkdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory/");
		chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory/",0777);
	}

	if($admin_type=="A"){
		$company_id = $HEAD_OFFICE_CODE;
	}

	foreach($inventory_cid_info as $cid_name){
		//echo $cid_name;
		if($$cid_name){
			$cid=$$cid_name;
		}
	}

	foreach($standard as $s_data){
		
		$sql = "select * from inventory_goods where gid='".$s_data[gid]."' ";
		$db->query($sql);

		if(!$db->total){
			$sql="insert into inventory_goods(gid,gname,gcode,basic_unit,order_basic_unit,sell_basic_unit,cid,admin,admin_type,glevel,item_account,standard,available_priod,model,og_ix,origin,c_ix,company,b_ix,brand,ci_ix,pi_ix,ps_ix,bs_goods_url,search_keyword,etc,surtax_div,is_use,status,material,hc_code,kc_mark,leadtime,available_amountperday,valuation,lotno,bimg,mimg,msimg,simg,cimg,editdate,regdate) values('".$s_data[gid]."','$gname','$gcode','$basic_unit','$order_basic_unit','$sell_basic_unit','$cid','$company_id','$admin_type','$glevel','$item_account','".$s_data[standard]."','$available_priod','$model','$og_ix','$origin','$c_ix','$company','$b_ix','$brand_name','$ci_ix','$pi_ix','$ps_ix','$bs_goods_url','$search_keyword','".$s_data[etc]."','$surtax_div','$is_use','$status','$material','$hc_code','$kc_mark','$leadtime','$available_amountperday','$valuation','$lotno','$bimg_text','$mimg','$msimg','$simg','$cimg',NOW(),NOW())";
			//$db->sequences = "INVENTORY_GOODS_SEQ";
		}else{
			$sql="update inventory_goods set
					gname='".$gname."',
					gcode='".$gcode."',
					basic_unit='".$basic_unit."',
					order_basic_unit='".$order_basic_unit."',
					sell_basic_unit='".$sell_basic_unit."',
					cid='".$cid."',
					admin='".$company_id."',
					admin_type='".$admin_type."',
					glevel='".$glevel."',
					item_account='".$item_account."',
					standard='".$s_data[standard]."',
					available_priod='".$available_priod."',
					model='".$model."',
					og_ix='".$og_ix."',
					origin='".$origin."',
					c_ix='".$c_ix."',
					company='".$company."',
					b_ix='".$b_ix."',
					brand='".$brand_name."',
					ci_ix='".$ci_ix."',
					pi_ix='".$pi_ix."',
					ps_ix='".$ps_ix."',
					bs_goods_url='".$bs_goods_url."',
					search_keyword='".$search_keyword."',
					etc='".$s_data[etc]."',
					surtax_div='".$surtax_div."',
					is_use='".$is_use."',
					status='".$status."',
					material='".$material."',
					hc_code='".$hc_code."',
					kc_mark='".$kc_mark."',
					leadtime='".$leadtime."',
					available_amountperday='".$available_amountperday."',
					valuation='".$valuation."',
					lotno='".$lotno."',
					bimg='".$bimg_text."',
					mimg='".$mimg."',
					msimg='".$msimg."',
					simg='".$simg."',
					cimg='".$cimg."',
					editdate=NOW()
				where
					gid='".$s_data[gid]."' ";
		}

		$db->query($sql);

		foreach($goods_unit as $gu_data){
			if($gu_data[unit]){

				$sql = "select * from inventory_goods_unit where gid='".$s_data[gid]."' and unit = '".$gu_data[unit]."' ";
				$db->query($sql);
				
				if(!$db->total){
					$sql="insert into inventory_goods_unit(gu_ix,gid,unit,change_amount,buying_price,wholesale_price,sellprice,barcode,weight,width_length,depth_length,height_length,add_status,is_pos_link,editdate,regdate) values('','".$s_data[gid]."','".$gu_data[unit]."','".$gu_data[change_amount]."','".$gu_data[buying_price]."','".$gu_data[wholesale_price]."','".$gu_data[sellprice]."','".$s_data[barcode][$gu_data[unit]]."','".$gu_data[weight]."','".$gu_data[width_length]."','".$gu_data[depth_length]."','".$gu_data[height_length]."','I','N',NOW(),NOW())";
					$db->sequences = "INVENTORY_GOODS_ITEM_SEQ";
					$db->query($sql);
					$gu_ix = $db->insert_id();	//품목다중가격 추가시 사용
				}else{
					//공급가, 기본가 변경시 다중품목 금액 변경 시작
					$db->fetch();
					$gu_ix = $db->dt[gu_ix];

					update_product_coprice($gu_data,$gu_ix);	//단위 공급가 품목과 연결되 상품의 금액도 변경 2014-05-09 이학봉 항상 업데이트 위에 잇어야함
					update_product_listprice($gu_data,$gu_ix);	//단위기본가 변경시 해당 품목과 연결되 상품의 금액도 변경 2014-05-09 이학봉 항상 업데이트 위에 잇어야함
					//공급가, 기본가 변경시 다중품목 금액 변경 끝 

					$sql="update inventory_goods_unit set
							change_amount='".$gu_data[change_amount]."',
							buying_price='".$gu_data[buying_price]."',
							wholesale_price='".$gu_data[wholesale_price]."',
							sellprice='".$gu_data[sellprice]."',
							barcode='".$s_data[barcode][$gu_data[unit]]."',
							weight='".$gu_data[weight]."',
							width_length='".$gu_data[width_length]."',
							depth_length='".$gu_data[depth_length]."',
							height_length='".$gu_data[height_length]."',
							add_status='U',
							is_pos_link='N',
							editdate=NOW()
						where
							gid='".$s_data[gid]."' and unit='".$gu_data[unit]."'
						";
					$db->query($sql);
				}
			}

			insert_multi_price($goods_setup_info,$gu_data,$s_data[gid],$gu_ix);		//품목다중가격테이블 입력 
		}
	}

	foreach($standard as $s_data){

		$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory", $s_data[gid], 'Y');
		//$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory", $s_data[gid], 'Y');

		if($allimg_size != 0 || ($bimg_text && $img_url_copy)){

			if($bimg_text && $img_url_copy){
				//echo str_replace("$","\$",$bimg_text);
				copy(str_replace("$","\$",$bimg_text), $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif");
				$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif";
				chmod($basic_img_src,0777);
				$image_info = getimagesize ($basic_img_src);
				//print_r($image_info);
				$image_type = substr($image_info['mime'],-3);
			}else{
				$image_info = getimagesize ($allimg);
				$image_type = substr($image_info['mime'],-3);
				$image_width = $image_info[0];

				copy($allimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif");

				$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif";
				chmod($basic_img_src,0777);
			}

			if($image_info[0] > $image_info[1]){
				$image_resize_type = "W";
			}else{
				$image_resize_type = "H";
			}

			$chk_cimg=1;

			if($image_type == "gif"){

				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif");
				}

				MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif",$inventory_image_info[0][width],$inventory_image_info[0][height],$image_resize_type);


				if($chk_cimg == 1){
					if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif")){
						unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif");
					}
					MirrorGif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif", MIRROR_NONE);
					resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif",$inventory_image_info[1][width],$inventory_image_info[1][height],$image_resize_type);
				}

			}else if($image_type == "png"){

				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif");
				}

				MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif", MIRROR_NONE);
				resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif",$inventory_image_info[0][width],$inventory_image_info[0][height],$image_resize_type);

				if($chk_cimg == 1){
					if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif")){
						unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif");
					}
					MirrorPNG($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif", MIRROR_NONE);
					resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif",$inventory_image_info[1][width],$inventory_image_info[1][height],$image_resize_type);
				}

			}else{

				if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif");
				}

				Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif",$inventory_image_info[0][width],$inventory_image_info[0][height],$image_resize_type);

				if($chk_cimg == 1){
					if (file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif")){
						unlink($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif");
					}
					Mirror($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif", MIRROR_NONE);
					resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif",$inventory_image_info[1][width],$inventory_image_info[1][height],$image_resize_type);
				}
			}
		}

		if ($bimg_size > 0){
			copy($bimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif");
		}

		if ($cimg_size > 0)
		{
			copy($cimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif");
		}
	}

}

?>
