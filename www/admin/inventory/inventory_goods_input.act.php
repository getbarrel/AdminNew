<?
@include_once("../web.config");
include_once("../../class/database.class");
include_once("../lib/imageResize.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
include_once("../inventory/inventory.lib.php");


//	print_r($goods_items);
//	exit;

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

if ($act == "value_check_jquery"){
	$strLen = strlen($value);
	if($strLen < '6' || $strLen > '16') {
		echo "130"; //자리수가 지정된만큼 충족이 안된다면 A문자를 출력하고 끝냄
		exit;
	}

	$db->query("SELECT * FROM inventory_goods WHERE gid='$value' ");
	
	if ($db->total)
	{
		echo "N"; //등록이 되어있는 [아이디]입니다.등록불가입니다.
	}
	else
	{
		echo "300";
	}
	exit;
}


if ($act == "get_goods_unit")
{
	$db->query("select *,
		(select brand_name from shop_brand where b_ix=g.b_ix ) as brand_name,
		(select com_name from common_company_detail where company_id=g.ci_ix ) as com_name
	from inventory_goods g right join inventory_goods_unit gu on g.gid =gu.gid where gu_ix='".$gu_ix."' ");
	//$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." WHERE opnt_ix='".$opnt_ix."'");
	if($db->dbms_type == "oracle"){
		$unit = $db->fetch("object");
	}else{
		$unit = $db->fetch("object");
	}

	$unit["unit_text"]=$ITEM_UNIT[$unit["unit"]];
	$unit = str_replace("\"true\"","true",json_encode($unit));
	$unit = str_replace("\"false\"","false",$unit);
	echo $unit;
	exit;
}


if ($act == "get_barcode")
{
	if($code_div == "item_code"){
		$db->query("select gi_ix from inventory_goods_item order by gi_ix desc limit 1 ");
		

		if($db->total){
			$db->fetch();
			$gi_ix = $db->dt[gi_ix];
			echo $gi_ix;
		}else{
			echo rand(1000000000,9999999999);
		}
		exit;
	}else if($code_div == "item_barcode"){
		echo rand(1000000000,1000999999);
	}else if($code_div == "barcode"){
		echo rand(1000000000,1000999999);
	}
}


if ($act == 'insert' || $act == "tmp_insert"){
	

	foreach($standard as $s_data){
		
		$sql="select * from inventory_goods where gid='".$s_data[gid]."'";
		$db->query($sql);
		
		if($db->total){
			echo "<script language='javascript'>alert('`".$s_data[gid]."` 품목코드번호가 이미 등록되어 등록이 취소 되었습니다.');</script>";
			exit;
		}
	}

	if(!file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory/")){
		mkdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory/");
		chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory/",0777);
	}

	$gname = strip_tags(trim($gname));
	
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
		$sql="insert into inventory_goods(gid,gname,gcode,basic_unit,order_basic_unit,sell_basic_unit,cid,admin,admin_type,glevel,item_account,standard,available_priod,model,og_ix,origin,c_ix,company,b_ix,brand,ci_ix,pi_ix,ps_ix,bs_goods_url,search_keyword,etc,surtax_div,is_use,status,material,hc_code,kc_mark,leadtime,available_amountperday,valuation,lotno,bimg,mimg,msimg,simg,cimg,editdate,regdate) values('".$s_data[gid]."','$gname','$gcode','$basic_unit','$order_basic_unit','$sell_basic_unit','$cid','$company_id','$admin_type','$glevel','$item_account','".$s_data[standard]."','$available_priod','$model','$og_ix','$origin','$c_ix','$company','$b_ix','$brand_name','$ci_ix','$pi_ix','$ps_ix','$bs_goods_url','$search_keyword','".$s_data[etc]."','$surtax_div','$is_use','$status','$material','$hc_code','$kc_mark','$leadtime','$available_amountperday','$valuation','$lotno','$bimg_text','$mimg','$msimg','$simg','$cimg',NOW(),NOW())";
		//$db->sequences = "INVENTORY_GOODS_SEQ";
		$db->query($sql);
		

		$index = 0; // 바코드 순번을 가져오기 위해 추가 바코드는 0 1 2 순서를 가지고 있는데 $s_data[barcode][$gu_data[unit]] 기존방식으로 할때 unit 값이 변화되기 때문에 맞지 않음 JK150529
		foreach($goods_unit as $gu_data){
			if($gu_data[unit]){
				$sql="insert into inventory_goods_unit(gu_ix,gid,unit,change_amount,buying_price,wholesale_price,sellprice,barcode,weight,width_length,depth_length,height_length,add_status,is_pos_link,editdate,regdate) values('','".$s_data[gid]."','".$gu_data[unit]."','".$gu_data[change_amount]."','".$gu_data[buying_price]."','".$gu_data[wholesale_price]."','".$gu_data[sellprice]."','".$s_data[barcode][$gu_data[unit]]."','".$gu_data[weight]."','".$gu_data[width_length]."','".$gu_data[depth_length]."','".$gu_data[height_length]."','I','N',NOW(),NOW())";
				$db->sequences = "INVENTORY_GOODS_ITEM_SEQ";
				$db->query($sql);
				
				//품목 다중가격 테이블에 없을시 매입가로 일괄 적용 시작 2014-05-07 이학봉
				$gu_ix = $db->insert_id();
				
				insert_multi_price($goods_setup_info,$gu_data,$gid,$gu_ix);		//품목다중가격테이블 입력 
				//품목 다중가격 테이블에 없을시 매입가로 일괄 적용 시작 2014-05-07 이학봉

				$index ++;
			}
		}

		$gid = $s_data[gid];
	}


	$before_uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory", $bgid, 'Y');

	foreach($standard as $s_data){

		$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory", $s_data[gid], 'Y');

		if ($allimg_size > 0 || $mode == "copy" || ($bimg_text && $img_url_copy)){
			//워터마크 적용
			if($mode == "copy" && $bimg_text == "" && $allimg == ""){// 상품 복사모드일때는 기존 상품의 이미지를 복사해서 나머지 이미지가 생성됩니다
				$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$before_uploaddir."/basic_".$bgid.".gif";

				if (file_exists($basic_img_src)){
					copy($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif");

					chmod($basic_img_src,0777);

					$image_info = getimagesize ($basic_img_src);
					$image_type = substr($image_info['mime'],-3);
					$image_width = $image_info[0];
				}
			}else{
				//echo "img_url_copy :".$img_url_copy;
				//	exit;

				if($img_url_copy){

					if(@copy($bimg_text, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif")){
						$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif";
					}else{
						@copy($bimg_text, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif");
						$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif";
					}

					$allimg = $basic_img_src;
					//echo $basic_img_src;
					@chmod($basic_img_src,0777);
					$image_info = @getimagesize ($basic_img_src);
					$image_type = substr($image_info['mime'],-3);

				}else{

					if($allimg){
						$image_info = getimagesize ($allimg);
						$image_type = substr($image_info['mime'],-3);

						$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif";
						copy($allimg, $basic_img_src); // 원본 이미지를 만든다.
						chmod($basic_img_src,0777);

					}
				}
			}

			if($image_info[0] > $image_info[1]){
				$image_resize_type = "W";
			}else{
				$image_resize_type = "H";
			}

			$chk_cimg = 1;

			/*if($watermark) {
				require_once "../lib/class.upload.php";

				$s_water_handle = new Upload($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif");
				$s_water_result = WaterMarkProcess2($s_water_handle, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/", $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/");


				$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_watermark_".$s_data[gid].".gif";
				copy($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/watermark/basic_".$s_data[gid].".gif",$basic_img_src);
				chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/basic_".$s_data[gid].".gif", 0777);


				$image_type = "gif"; // 워터마크 처리후 마임타입이 gif로 바뀐다.
				//$image_info = getimagesize ($basic_img_src);
				//$image_type = substr($image_info['mime'],-3);
				//echo $image_type;
			}*/


			if($image_type == "gif" || $image_type == "GIF"){

				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif",$inventory_image_info[0][width],$inventory_image_info[0][height],$image_resize_type);

				if($chk_cimg == 1){
					MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif", MIRROR_NONE);
					resize_gif($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif",$inventory_image_info[1][width],$inventory_image_info[1][height],$image_resize_type);
				}

			}else if($image_type == "png" || $image_type == "PNG"){

				MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif", MIRROR_NONE);
				resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif",$inventory_image_info[0][width],$inventory_image_info[0][height],$image_resize_type);

				if($chk_cimg == 1){
					MirrorPNG($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif", MIRROR_NONE);
					resize_png($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif",$inventory_image_info[1][width],$inventory_image_info[1][height],$image_resize_type);
				}

			}else{
				//copy($allimg, $basic_img_src);
				//exit;

				//copy($allimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product".$uploaddir."/b_".$s_data[gid].".gif");
				if (file_exists($basic_img_src)){
					Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif", MIRROR_NONE);
					resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif",$inventory_image_info[0][width],$inventory_image_info[0][height],$image_resize_type);

					if($chk_cimg == 1){
						Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif", MIRROR_NONE);
						resize_jpg($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif",$inventory_image_info[1][width],$inventory_image_info[1][height],$image_resize_type);
					}
				}
			}
		}

		if ($bimg_size > 0){
			copy($bimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/b_".$s_data[gid].".gif");
		}

		if ($cimg_size > 0){
			copy($cimg, $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/c_".$s_data[gid].".gif");
		}

	}

	if(!$bs_act){
//		if($act == "tmp_insert"){
//			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('재고상품등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../inventory/inventory_goods_input.php?gid=".$gid."';</script>";
//		}else{
//			if($mmode == "pop"){
//				echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('재고상품등록이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
//			}else{
//				echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('재고상품등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../inventory/inventory_goods_input.php?gid=".$gid."';</script>";
//			}
//		}
	}
}

if ($act == "ajax_delete_check"){
	
	$delete_bool=true;

	$db->query("select gu_ix from inventory_goods_unit where gid='$gid' and unit='$unit' ");
	$db->fetch();
	$gu_ix = $db->dt[gu_ix];
	
	$db->query("select * from ".TBL_SHOP_PRODUCT." where is_delete='0' and stock_use_yn='Y' and pcode='".$gu_ix."'");

	if($db->total > 0){
		$delete_bool = false;
	}
	
	if($delete_bool){
		$db->query("select od.id as opnd_ix ,pid from ".TBL_SHOP_PRODUCT." p inner join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od on (p.id=od.pid) where p.is_delete='0' and p.stock_use_yn='Y' and option_code = '".$gu_ix."'");

		if($db->total > 0){
			$delete_bool = false;
		}
	}

	if($delete_bool){
		echo "Y";
	}else{
		echo "N";
	}
	exit;
}

if ($act == "delete" || $act == "ajax_delete"){
	
	$delete_bool=true;
	$db->query("select gu_ix from inventory_goods_unit where gid='$gid'");
	$gu_ix_list = $db->fetchall("object");

	
	for($i=0;$i < count($gu_ix_list);$i++){
		$gu_ix = $gu_ix_list[$i][gu_ix];

		$db->query("select * from ".TBL_SHOP_PRODUCT." where is_delete='0' and stock_use_yn='Y' and pcode='".$gu_ix."'");
		
		if($db->total > 0){
			$delete_bool = false;
			break;
		}
		
		if($delete_bool){
			$db->query("select od.id as opnd_ix ,pid from ".TBL_SHOP_PRODUCT." p inner join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od on (p.id=od.pid) where p.is_delete='0' and p.stock_use_yn='Y' and option_code = '".$gu_ix."'");

			if($db->total > 0){
				$delete_bool = false;
				break;
			}
		}
	}
	
	if($delete_bool){
		
		$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory", $gid, 'Y');

		if ($uploaddir && file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/")){
			rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/inventory".$uploaddir."/");
		}

		$db->query("DELETE FROM inventory_goods WHERE gid='$gid' ");
		//$db->query("DELETE FROM inventory_goods_item WHERE gid='$gid'");
		$db->query("DELETE FROM inventory_goods_unit WHERE gid='$gid'");
		
		if($act == "ajax_delete"){
			echo "Y";
		}else{
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('재고상품삭제가 정상적으로 처리 되었습니다.');parent.document.location.href='../inventory/inventory_goods_list.php';</script>";
		}
	}else{
		if($act == "ajax_delete"){
			echo "N";
		}else{
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품 또는 옵션에 재고가 맵핑되어있어 삭제하실수 없습니다. 상품및 옵션을 먼저 삭제후 진행해주세요.');</script>";
		}
	}
}



if ($act == "update" || $act == "tmp_update")
{
	$gu_ix_array=array();

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

				$gu_ix_array[]= $gu_ix;
				insert_multi_price($goods_setup_info,$gu_data,$s_data[gid],$gu_ix);		//품목다중가격테이블 입력 
			}
		}

		$sql="delete from inventory_goods_unit where gid='".$s_data[gid]."' and gu_ix not in ('".implode("','",$gu_ix_array)."') ";
		$db->query($sql);
	}

	/*
	for($j=0;$j < count($goods_unit);$j++){	//도매할인가,소매할인가 추가 2013-12-16 이학봉

		if($goods_unit[$j][unit]){

			$sql = "select * from inventory_goods_unit where gid='".$gid."' and unit = '".$goods_unit[$j][b_unit]."' ";
			$db->query($sql);
			
			if($db->total){
					$sql = "update inventory_goods_unit set
								
								unit='".$goods_unit[$j][unit]."',
								change_amount='".$goods_unit[$j][change_amount]."',
								buying_price='".$goods_unit[$j][buying_price]."',
								offline_wholesale_price='".$goods_unit[$j][offline_wholesale_price]."',
								wholesale_price='".$goods_unit[$j][wholesale_price]."',
								sellprice='".$goods_unit[$j][sellprice]."',
								safestock='".$goods_unit[$j][safestock]."',
								barcode='".$goods_unit[$j][barcode]."',
								sell_ing_cnt='".$goods_unit[$j][sell_ing_cnt]."',
								available_stock='".$goods_unit[$j][available_stock]."',
								wholesale_sellprice = '".$goods_unit[$j][wholesale_sellprice]."',
								discount_price = '".$goods_unit[$j][discount_price]."',
								add_status = 'U',
								is_pos_link = 'N',
								editdate=NOW(),
								regdate=NOW() 
								where gid='".$goods_unit[$j][gid]."' and  unit = '".$goods_unit[$j][unit]."'";
					$db->query($sql);

			}else{
				$sql = "insert into inventory_goods_unit
							(gid,unit,change_amount,buying_price,offline_wholesale_price,wholesale_price,sellprice,safestock,barcode,editdate,regdate,add_status,is_pos_link,sell_ing_cnt,available_stock,wholesale_sellprice,discount_price) values
							('".$gid."','".$goods_unit[$j][unit]."','".$goods_unit[$j][change_amount]."','".$goods_unit[$j][buying_price]."','".$goods_unit[$j][offline_wholesale_price]."','".$goods_unit[$j][wholesale_price]."','".$goods_unit[$j][sellprice]."','".$goods_unit[$j][safestock]."','".$goods_unit[$j][barcode]."',NOW(),NOW(),'I','N','".$goods_unit[$j][sell_ing_cnt]."','".$goods_unit[$j][available_stock]."','".$goods_unit[$j][wholesale_sellprice]."','".$goods_unit[$j][discount_price]."') ";

				//$db->sequences = "INVENTORY_GOODS_ITEM_SEQ";
				$db->query($sql);
			}

			//shop_product 금액 적용 2013-12-16 이학봉
			$sql = "select p.id from shop_product as p inner join inventory_goods_unit as gu on (gu.gu_ix = p.pcode) where gu.gid = '".$goods_unit[$j][gid]."' and gu.unit = '".$goods_unit[$j][unit]."' and p.product_type = '0'";
			$db2->query($sql);
			$product_array = $db2->fetchall();
			if(count($product_array) > 0){
				for($k=0;$k<count($product_array);$k++){
					$pid = $product_array[$k][id];
					$usql = "update shop_product set
								wholesale_price = '".$goods_unit[$j][wholesale_price]."',
								wholesale_sellprice = '".$goods_unit[$j][wholesale_sellprice]."',
								listprice = '".$goods_unit[$j][sellprice]."',
								sellprice = '".$goods_unit[$j][discount_price]."',
								coprice = '".$goods_unit[$j][buying_price]."'
							where
								id = '".$pid."'";
					$db2->query($usql);
				}
			}
			//shop_product 금액 적용 2013-12-16 이학봉

			//shop_product_options_detail 금액 적용 2013-12-16 이학봉
			$osql = "select 
						pod.id as options_id
					from
						shop_product as p 
						inner join shop_product_options_detail as pod on (p.id = pod.pid and p.product_type = '0')
						inner join inventory_goods_unit as gu on (pod.option_code = gu.gu_ix)
					where
						gu.gid = '".$goods_unit[$j][gid]."'
						and gu.unit = '".$goods_unit[$j][unit]."'
						and p.product_type = '0'";
			$db2->query($osql);
			$product_options_array = $db2->fetchall();

			if(count($product_options_array) > 0){
				for($k=0;$k<count($product_options_array);$k++){
					$op_id = $product_options_array[$k][options_id];
					$update = "update shop_product_options_detail set
									option_wholesale_listprice = '".$goods_unit[$j][wholesale_price]."',
									option_wholesale_price = '".$goods_unit[$j][wholesale_sellprice]."',
									option_listprice = '".$goods_unit[$j][sellprice]."',
									option_price = '".$goods_unit[$j][discount_price]."',
									option_coprice = '".$goods_unit[$j][buying_price]."'
								where
									id ='".$op_id."'";
					$db2->query($update);
				}
			}
			//shop_product_options_detail 금액 적용 2013-12-16 이학봉
		}
	}
	*/

	
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

	if($act == "tmp_update"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('재고상품수정이 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";
	}else{
		if($mmode == "pop"){
			//echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('재고상품수정이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
		}else{
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('재고상품수정이 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";
		}
	}
}


if ($act == "initialization"){
	
	if($_SESSION["admininfo"]["charger_id"]=="forbiz" || date("Ymd")=="20141122" || date("Ymd")=="20141123"){

		$db->query("TRUNCATE TABLE inventory_warehouse_move ");
		$db->query("TRUNCATE TABLE inventory_warehouse_move_detail ");
		$db->query("TRUNCATE TABLE inventory_history ");
		$db->query("TRUNCATE TABLE inventory_history_detail ");
		$db->query("TRUNCATE TABLE inventory_product_stockinfo ");
		$db->query("TRUNCATE TABLE inventory_product_stockinfo_bydate ");

		echo "<script>alert('재고가 정상적으로 초기화 되었습니다.');parent.document.location.reload();</script>";
	}else{
		echo "<script>alert('초기화실패 하였습니다.');parent.document.location.reload();</script>";
	}
	exit;
}

?>
