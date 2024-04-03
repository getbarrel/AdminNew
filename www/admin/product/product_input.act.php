<?
include("../web.config");
include("../../class/database.class");
include("../lib/imageResize.lib.php");



session_start();

if($admininfo[company_id] == ""){
	echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='/admin/admin.php'</script>";
	exit;	
}

$db = new Database;
$db2 = new Database;
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


if ($act == 'insert'){
	
	
	
	//$db->query("SELECT max(vieworder)+1 as max_vieworder FROM ".TBL_SHOP_PRODUCT." ");
	$db->query("update ".TBL_SHOP_PRODUCT." set vieworder = vieworder + 1  ");
	$db->fetch();
	//$vieworder = $db->dt[max_vieworder];
	$vieworder = 1;
	
	$data_text_convert = $basicinfo;
	
	if($brand_name != ""){
		$brand_name = $brand_name;
	}else{
		$sql = "select brand_name from shop_brand where b_ix = '$brand'";
		$db->query($sql);
		$db->fetch();
		$brand_name = $db->dt[brand_name];
	}

	if($company_name != ""){
		$company = $company_name;
	}else{
		$company = $company;
	}
	
	$sql = $sql."INSERT INTO ".TBL_SHOP_PRODUCT." (id, pcode, pname, brand,brand_name, company,  shotinfo,  listprice,sellprice,  coprice, reserve,reserve_rate,   basicinfo, ,   state, disp, movie, vieworder,  admin,stock,safestock,search_keyword,reg_category, regdate) ";
	$sql = $sql." values('', '$pcode', '$pname', '$brand','$brand_name','$company',  '$shotinfo','$listprice','$sellprice', '$coprice', '$reserve','$rate1',  '$basicinfo',  '$state', '$disp', '$movie', '$vieworder', '$admininfo[company_id]','$stock','$safestock','$search_keyword','N', NOW()) ";
	
	//echo($sql);
	$db->query($sql);
	$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT." WHERE id=LAST_INSERT_ID()");
	$db->fetch();
	$INSERT_PRODUCT_ID = $db->dt[0];
	
	$image_info = getimagesize ($allimg);	
	$image_type = substr($image_info['mime'],-3);
	$image_width = $image_info[0];
/*
Array
(
    [0] => 67
    [1] => 20
    [2] => 1
    [3] => width="67" height="20"
    [bits] => 5
    [channels] => 3
    [mime] => image/gif
)
*/
	//if ($allimg != "none")
	if ($allimg_size > 0 || $mode == "copy"){
		//워터마크 적용
		if(false) {
			require_once "../lib/class.upload.php";		

			$s_water_handle = new Upload($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$INSERT_PRODUCT_ID.".gif");
			$s_water_result = WaterMarkProcess2($s_water_handle, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/");


			@copy($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/watermark/bagic_".$INSERT_PRODUCT_ID.".gif",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$INSERT_PRODUCT_ID.".gif");
			@chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$INSERT_PRODUCT_ID.".gif", 0777);

			
			$image_type = "gif"; // 워터마크 처리후 마임타입이 gif로 바뀐다.
		}
		
		if($mode == "copy"){// 상품 복사모드일때는 기존 상품의 이미지를 복사해서 나머지 이미지가 생성됩니다
			$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$bpid.".gif";
			
			copy($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif");
			
			
			$chk_mimg = 1;
			$chk_msimg = 1;
			$chk_simg = 1;
			$chk_cimg = 1;
			
			$image_info = getimagesize ($basic_img_src);	
			$image_type = substr($image_info['mime'],-3);
	
		}else{
			$image_db = new Database;
			$image_db->query("select * from shop_image_resizeinfo order by idx");
			$image_info = $image_db->fetchall();
			if($image_type == "gif" || $image_type == "GIF"){
				$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$INSERT_PRODUCT_ID.".gif";
				copy($allimg, $basic_img_src); // 원본 이미지를 만든다.
							
				//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif");
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif",$image_info[0][width],$image_info[0][height]);
			}else{
				$basic_img_src = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$INSERT_PRODUCT_ID.".gif";
				copy($allimg, $basic_img_src); // 원본 이미지를 만든다.
							
				//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif");
				Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif",$image_info[0][width],$image_info[0][height]);
			}
		}
		
		if($image_type == "gif" || $image_type == "GIF"){
			
			if($chk_mimg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$INSERT_PRODUCT_ID.".gif",$image_info[1][width],$image_info[1][height]);
			}
			
			if($chk_msimg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$INSERT_PRODUCT_ID.".gif",$image_info[2][width],$image_info[2][height]);
			}
			
			if($chk_simg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$INSERT_PRODUCT_ID.".gif",$image_info[3][width],$image_info[3][height]);
			}
			
			if($chk_cimg == 1){
				MirrorGif($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$INSERT_PRODUCT_ID.".gif",$image_info[4][width],$image_info[4][height]);
			}
		}else{
			//copy($allimg, $basic_img_src);
			
			
			//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif");
			Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
			resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif",$image_info[0][width],$image_info[0][height]);
			
			if($chk_mimg == 1){
				Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$INSERT_PRODUCT_ID.".gif",$image_info[1][width],$image_info[1][height]);
			}
			
			if($chk_msimg == 1){
				Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$INSERT_PRODUCT_ID.".gif",$image_info[2][width],$image_info[2][height]);
			}
			
			if($chk_simg == 1){
				Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$INSERT_PRODUCT_ID.".gif",$image_info[3][width],$image_info[3][height]);
			}
			
			if($chk_cimg == 1){
				Mirror($basic_img_src, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$INSERT_PRODUCT_ID.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$INSERT_PRODUCT_ID.".gif",$image_info[4][width],$image_info[4][height]);
			}
		}
	}
	
	

	//if ($bimg != "none")
	if ($bimg_size > 0){
		copy($bimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$INSERT_PRODUCT_ID.".gif");
	}
	
	//if ($mimg != "none")
	if ($mimg_size > 0){
		copy($mimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$INSERT_PRODUCT_ID.".gif");
	}
	
	//if ($msimg != "none")
	if ($msimg_size > 0){
		copy($msimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$INSERT_PRODUCT_ID.".gif");
	}

	//if ($simg != "none")
	if ($simg_size > 0){
		copy($simg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$INSERT_PRODUCT_ID.".gif");
	}
	
	//if ($cimg != "none")
	if ($cimg_size > 0){
		copy($cimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$INSERT_PRODUCT_ID.".gif");
	}
	
	$sql = "INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, listprice, sellprice, coprice, reserve,  admin,regdate) ";
	$sql = $sql." values('', '".$INSERT_PRODUCT_ID."','$listprice','$sellprice', '$coprice', '$reserve',  '$admininfo[company_id]',NOW()) ";
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
								@unlink($local_img_path);	
							}
							
							$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
						}else{
							if(!substr_count($img,$DOCUMENT_ROOT)){	
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
	
	
	if($mode == "copy"){
		
		////////////////////////////////////////////// 옵션 정보 복사 루틴 //////////////////////////////////////////////////////////
		
		$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid='$bpid' ");		
		if($db->total){// 옵션이 있으면		
			for($i=0;$i < $db->total;$i++){
				$db->fetch($i);
				// 옵션 정보가 있으면 복사해서 넣는다
				$db2->query("INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS." SELECT '' as opn_ix,'$INSERT_PRODUCT_ID' as pid,option_name,option_kind,option_use,NOW() as regdate FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE pid='$bpid' and opn_ix ='".$db->dt[opn_ix]."' ");
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


		////////////////////////////////////////// 관련 상품  복사 루틴 //////////////////////////////////////////////////////////
		
		$db->query("SELECT pid FROM ".TBL_SHOP_RELATION_PRODUCT." WHERE pid='$bpid' ");		
		
		if($db->total){
			$sql = "insert into ".TBL_SHOP_RELATION_PRODUCT." SELECT '' as rp_ix, '$INSERT_PRODUCT_ID' as pid,rp_pid,vieworder,NOW() as regdate FROM ".TBL_SHOP_RELATION_PRODUCT." where pid='$bpid' ";	
			$db->query($sql);
		}		
	}
	
	$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET basicinfo = '$basicinfo' WHERE id='$INSERT_PRODUCT_ID'");
	
	header("Location:./product_input.php?id=".$INSERT_PRODUCT_ID);
}

if ($act == "delete")
{
    //상품 삭제되면 안됨 만약 해당 부분에 진입되는 상황이 있다면 상품 삭제가 안니 is_delete 업데이트 처리로 변경 필요
    exit;
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
	//$db->query("DELETE FROM ".TBL_SHOP_ORDER." WHERE pid='$id'");
	
	
	
	
	if($id && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id")){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id");
	}
	
	echo "<script language='javascript'>document.location.href='product_list.php';</script>";

	//header("Location:../product_list.php");
}

if ($act == "update")
{
	
	
	$image_info = getimagesize ($allimg);
	
	$image_type = substr($image_info['mime'],-3);
	
	
	if($allimg_size != 0){
		
		copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");
		
		
		//워터마크 적용
		if(false) {
			require_once "../lib/class.upload.php";		

			$s_water_handle = new Upload($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");
			$s_water_result = WaterMarkProcess2($s_water_handle, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/");


			@copy($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/watermark/bagic_".$id.".gif",$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");
			@chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", 0777);

			
			$image_type = "gif"; // 워터마크 처리후 마임타입이 gif로 바뀐다.
		}
		$image_db = new Database;
		$image_db->query("select * from shop_image_resizeinfo order by idx");
		$image_info = $image_db->fetchall();
		if($image_type == "gif"){		
						
		//if(substr($allimg_name, -3) == "gif"){	
			//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");
			
			
			
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif");
			}
			
			//copy($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif");
			MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif", MIRROR_NONE);
			resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif",$image_info[0][width],$image_info[0][height]);
			
			if($chk_mimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif",$image_info[1][width],$image_info[1][height]);
			}
			
			if($chk_msimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif",$image_info[2][width],$image_info[2][height]);
			}
			
			if($chk_simg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif",$image_info[3][width],$image_info[3][height]);
			}
			
			if($chk_cimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif");
				}
				MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif", MIRROR_NONE);
				resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif",$image_info[4][width],$image_info[4][height]);
			}
		
		}else{
			//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif");
			
			
			
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif")){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif");
			}
			
			
			//copy($allimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif");
			Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif", MIRROR_NONE);
			resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif",$image_info[0][width],$image_info[0][height]);
			
			if($chk_mimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif",$image_info[1][width],$image_info[1][height]);
			}
			
			if($chk_msimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif",$image_info[2][width],$image_info[2][height]);
			}
			
			if($chk_simg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif",$image_info[3][width],$image_info[3][height]);
			}
			
			if($chk_cimg == 1){
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif")){
					unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif");
				}
				Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/bagic_".$id.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif", MIRROR_NONE);
				resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif",$image_info[4][width],$image_info[4][height]);
			}
		}
	}
	
	
	
	
	if ($bimg_size > 0){	
		copy($bimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$id.".gif");
	}
	
	if ($mimg_size > 0)
	{
		copy($mimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$id.".gif");
	}
	
	if ($msimg_size > 0)
	{
		copy($msimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$id.".gif");
	}

	if ($simg_size > 0)
	{
		copy($simg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$id.".gif");
	}
	
	if ($cimg_size > 0)
	{
		copy($cimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$id.".gif");
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
								@unlink($local_img_path);	
							}
							
							$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/".$id."/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
						}else{
							if(!substr_count($img,$DOCUMENT_ROOT)){	
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
		$state = "6";
	}
		
	$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET  pcode='$pcode', pname='$pname', brand = '$brand', company='$company',  shotinfo='$shotinfo', listprice='$listprice',sellprice='$sellprice', coprice='$coprice', reserve='$reserve',reserve_rate='$rate1', basicinfo='$basicinfo',  state='$state', disp='$disp', movie='$movie',  stock='$stock', safestock='$safestock', search_keyword = '$search_keyword',   editdate = NOW() Where id = $id ");

	
	
	if($sellprice != $bsellprice || $coprice != $bcoprice ||  $reserve != $breserve){	
		$sql = $sql."INSERT INTO ".TBL_SHOP_PRICEINFO." (id, pid, listprice, sellprice, coprice, reserve,  admin,regdate) ";
		$sql = $sql." values('', '$id','$listprice','$sellprice', '$coprice', '$reserve',  '$admininfo[company_id]',NOW()) ";
		$db->query($sql);
	}
	
	header("Location:./product_input.php?id=$id");
}


function rmdirr($target,$verbose=false)
// removes a directory and everything within it
{
$exceptions=array('.','..');
if (!$sourcedir=@opendir($target))
   {
   if ($verbose)
       echo '<strong>Couldn&#146;t open '.$target."</strong><br />\n";
   return false;
   }
while(false!==($sibling=readdir($sourcedir)))
   {
   if(!in_array($sibling,$exceptions))
       {
       $object=str_replace('//','/',$target.'/'.$sibling);
       if($verbose)
           echo 'Processing: <strong>'.$object."</strong><br />\n";
       if(is_dir($object))
           rmdirr($object);
       if(is_file($object))
           {
           $result=@unlink($object);
           if ($verbose&&$result)
               echo "File has been removed<br />\n";
           if ($verbose&&(!$result))
               echo "<strong>Couldn&#146;t remove file</strong>";
           }
       }
   }
closedir($sourcedir);
if($result=@rmdir($target))
   {
   if ($verbose)
       echo "Target directory has been removed<br />\n";
   return true;
   }
if ($verbose)
   echo "<strong>Couldn&#146;t remove target directory</strong>";
return false;
}


function ClearText($str){
	return str_replace(">","",$str);
}


function returnFileName($filestr){
	$strfile = split("/",$filestr);
	
	return str_replace("%20","",$strfile[count($strfile)-1]);
	//return count($strfile);
	
}

function returnImagePath($str){
	$IMG = split(" ",$str);
	
	for($i=0;$i<count($IMG);$i++){
		//echo substr_count($IMG[$i],"src");
			if(substr_count($IMG[$i],"src=") > 0){
				$mstring = str_replace("src=","",$IMG[$i]);
				return str_replace("\"","",$mstring);
			}
	}
}

function imageExists($image,$dir) {

    $i=1; $probeer=$image;

    while(file_exists($dir.$probeer)) {
        $punt=strrpos($image,".");
        if(substr($image,($punt-3),1)!==("[") && substr($image,($punt-1),1)!==("]")) {
            $probeer=substr($image,0,$punt)."[".$i."]".
            substr($image,($punt),strlen($image)-$punt);
        } else {
            $probeer=substr($image,0,($punt-3))."[".$i."]".
            substr($image,($punt),strlen($image)-$punt);
        }
        $i++;
    }
    return $probeer;
}


?>