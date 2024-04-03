<?
include("../../class/database.class");
include("../../include/global_util.php");

$db = new Database;

if ($act == "insert")
{

	if($company_id == ""){
		$company_id = $admininfo[company_id];
	}

    if($db->dbms_type == "oracle"){
        $_use_sdate = $use_sdate." ".$use_sdate_h.":".$use_sdate_i.":".$use_sdate_s;
        $_use_edate = $use_edate." ".$use_edate_h.":".$use_edate_i.":".$use_edate_s;
        $sql = "insert into shop_bannerinfo
			(banner_ix,mall_ix,agent_type,company_id, banner_kind, banner_text_reversal, change_effect,shot_title, banner_name,banner_page,banner_position, display_cid,banner_link,banner_target,banner_desc,banner_img, banner_img_on, banner_btn_position, banner_width,banner_height,banner_html,md_mem_ix, goal_cnt, disp,use_sdate, use_edate, regdate) values ('','".$mall_ix."','".$agent_type."','".$company_id."','$banner_kind', '$banner_text_reversal', '$change_effect','$shot_title','$banner_name','$banner_page','$banner_position','$display_cid','$banner_link','$banner_target','$banner_desc','".$banner_img_name."','".$banner_img_on_name."','".$banner_btn_position."','$banner_width','$banner_height','$banner_html','$md_code','$goal_cnt','$disp',TO_DATE('".$_use_sdate."','MM-DD-YYYY HH24:MI:SS'),TO_DATE('".$_use_edate."','MM-DD-YYYY HH24:MI:SS'),NOW())";
        $db->sequences = "SHOP_BANNERINFO_SEQ";
    }else{
		$_use_sdate = $use_sdate." ".$use_sdate_h.":".$use_sdate_i.":".$use_sdate_s;
        $_use_edate = $use_edate." ".$use_edate_h.":".$use_edate_i.":".$use_edate_s;

		if($b_name == 'on'){
			$b_name = 'Y';
		}else{
			$b_name = 'N';
		}

		if($i_name == 'on'){
			$i_name = 'Y';
		}else{
			$i_name = 'N';
		}

		if($u_name == 'on'){
			$u_name = 'Y';
		}else{
			$u_name = 'N';
		}

		if($b_title == 'on'){
			$b_title = 'Y';
		}else{
			$b_title = 'N';
		}

		if($i_title == 'on'){
			$i_title = 'Y';
		}else{
			$i_title = 'N';
		}

		if($u_title == 'on'){
			$u_title = 'Y';
		}else{
			$u_title = 'N';
		}

		if($b_desc == 'on'){
			$b_desc = 'Y';
		}else{
			$b_desc = 'N';
		}

		if($i_desc == 'on'){
			$i_desc = 'Y';
		}else{
			$i_desc = 'N';
		}

		if($u_desc == 'on'){
			$u_desc = 'Y';
		}else{
			$u_desc = 'N';
		}

		if($b_name_m == 'on'){
			$b_name_m = 'Y';
		}else{
			$b_name_m = 'N';
		}

		if($i_name_m == 'on'){
			$i_name_m = 'Y';
		}else{
			$i_name_m = 'N';
		}

		if($u_name_m == 'on'){
			$u_name_m = 'Y';
		}else{
			$u_name_m = 'N';
		}

		if($b_title_m == 'on'){
			$b_title_m = 'Y';
		}else{
			$b_title_m = 'N';
		}

		if($i_title_m == 'on'){
			$i_title_m = 'Y';
		}else{
			$i_title_m = 'N';
		}

		if($u_title_m == 'on'){
			$u_title_m = 'Y';
		}else{
			$u_title_m = 'N';
		}

		if($b_desc_m == 'on'){
			$b_desc_m = 'Y';
		}else{
			$b_desc_m = 'N';
		}

		if($i_desc_m == 'on'){
			$i_desc_m = 'Y';
		}else{
			$i_desc_m = 'N';
		}

		if($u_desc_m == 'on'){
			$u_desc_m = 'Y';
		}else{
			$u_desc_m = 'N';
		}

        $sql = "insert into shop_bannerinfo
			(banner_ix,mall_ix,agent_type,company_id, banner_kind, banner_text_reversal,change_effect,shot_title, banner_name,banner_page,banner_position, display_cid, banner_link,banner_target,banner_desc,banner_img,banner_img_on,banner_btn_position, banner_width,banner_height,banner_html,banner_html_m,md_mem_ix, goal_cnt, disp, b_name, i_name, u_name, c_name, s_name, b_title, i_title, u_title, c_title, s_title, b_desc, i_desc, u_desc, c_desc, s_desc, banner_loc, banner_desc_m, banner_name_m, shot_title_m, b_name_m, i_name_m, u_name_m, c_name_m, s_name_m, b_title_m, i_title_m, u_title_m, c_title_m, s_title_m, b_desc_m, i_desc_m, u_desc_m, c_desc_m, s_desc_m, use_sdate, use_edate, regdate) values ('','".$mall_ix."','".$agent_type."','".$company_id."','$banner_kind', '$banner_text_reversal','$change_effect','$shot_title','$banner_name','$banner_page','$banner_position','$display_cid','$banner_link','$banner_target','$banner_desc','".$banner_img_name."','".$banner_img_on_name."','".$banner_btn_position."','$banner_width','$banner_height','$banner_html','$banner_html_m','$md_code','$goal_cnt', '$disp', '$b_name', '$i_name', '$u_name', '$c_name', '$s_name', '$b_title', '$i_title', '$u_title', '$c_title', '$s_title', '$b_desc', '$i_desc', '$u_desc', '$c_desc', '$s_desc', '$banner_loc', '$banner_desc_m', '$banner_name_m', '$shot_title_m', '$b_name_m', '$i_name_m', '$u_name_m', '$c_name_m', '$s_name_m', '$b_title_m', '$i_title_m', '$u_title_m', '$c_title_m', '$s_title_m', '$b_desc_m', '$i_desc_m', '$u_desc_m', '$c_desc_m', '$s_desc_m', '".$_use_sdate."', '".$_use_edate."', NOW())";
    }

	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$banner_ix = $db->last_insert_id;
	}else{
		$db->query("SELECT banner_ix FROM shop_bannerinfo WHERE banner_ix=LAST_INSERT_ID()");
		$db->fetch();
		$banner_ix = $db->dt[banner_ix];
	}

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/",0777);
	}

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/",0777);
	}
	
	//아카마이 ftp 파일 업로드
	$akamaiFtpUploadFiles = array();

	if ($banner_img)
	{
		copy($banner_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/".$banner_img_name);
		$akamaiFtpUploadFiles[] = $banner_img_name;
	}

	if ($banner_img_on)
	{
		copy($banner_img_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/".$banner_img_on_name);
		$akamaiFtpUploadFiles[] = $banner_img_on_name;
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner";

	if ($banner_btn_left)
	{
		copy($banner_btn_left, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_left.png");
		$akamaiFtpUploadFiles[] = "banner_btn_left.png";
	}
	if ($banner_btn_left_on)
	{
		copy($banner_btn_left_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_left_on.png");
		$akamaiFtpUploadFiles[] = "banner_btn_left_on.png";
	}

	if ($banner_btn_right)
	{
		copy($banner_btn_right, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_right.png");
		$akamaiFtpUploadFiles[] = "banner_btn_right.png";
	}

	if ($banner_btn_right_on)
	{
		copy($banner_btn_right_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_right_on.png");
		$akamaiFtpUploadFiles[] = "banner_btn_right_on.png";
	}

    if($mode == 'copy'){
        foreach($bd_ix as $_key => $_val){
            $file_upload_type = '';

            if($bd_ix[$_key] != ""){
                if($_FILES['bd_file']['size'][$_key] == 0){
                    //이미지 복사 그대로
                    $file_upload_type = 'copy';
                }else{
                    //이미지 바꿔서 올렸을 때
                    $file_upload_type = 'upload';
                }
            }else{
                //이미지 추가
                $file_upload_type = 'upload';
            }

            if($file_upload_type == 'copy'){
                $sql = "select * from shop_bannerinfo_detail where bd_ix='".$bd_ix[$_key]."' ";
                $db->query($sql);
                $db->fetch();
                $bd_file = $db->dt[bd_file];

                copy( $path."/".$b_banner_ix."/".$bd_file, $path."/".$banner_ix."/".$bd_file);
                $akamaiFtpUploadFiles[] = $bd_file;

                $sql = "insert into shop_bannerinfo_detail 
							(bd_ix,banner_ix,bd_title,bd_link,bd_file,regdate,tmp_update,vieworder) 
						values
							('','$banner_ix','".$bd_title[$_key]."','".$bd_link[$_key]."','".$bd_file."', NOW(),'1','".$bd_vieworder[$_key]."')";
                $db->sequences = "SHOP_BANNERINFO_DETAIL_SEQ";
                $db->query($sql);
            }else if($file_upload_type == 'upload'){
                if($_FILES['bd_file']['size'][$_key]){
                    copy($_FILES['bd_file']['tmp_name'][$_key], $path."/".$banner_ix."/".$_FILES['bd_file']['name'][$_key]);
                    $akamaiFtpUploadFiles[] = $_FILES['bd_file']['name'][$_key];
                    $sql = "insert into shop_bannerinfo_detail 
								(bd_ix,banner_ix,bd_title,bd_link,bd_file,etc_code, regdate,tmp_update,vieworder) 
							values 
								('','$banner_ix','".$bd_title[$_key]."','".$bd_link[$_key]."','".$_FILES['bd_file']['name'][$_key]."','".$company_id[$_key]."', NOW(),'1','".$bd_vieworder[$_key]."')";
                    $db->sequences = "SHOP_BANNERINFO_DETAIL_SEQ";
                    $db->query($sql);
                }
            }else{
                //예외
            }
        }

        unset($file_upload_type);
    }else{
        if(is_array($_FILES['bd_file']['size'])){
            foreach($_FILES['bd_file']['size'] as $_key=>$_val)	{
                if($_val > 0)	{
                    copy($_FILES['bd_file']['tmp_name'][$_key], $path."/".$banner_ix."/".$_FILES['bd_file']['name'][$_key]);
                    $akamaiFtpUploadFiles[] = $_FILES['bd_file']['name'][$_key];
                    //$sql = "insert into shop_bannerinfo_detail set banner_ix = '$banner_ix', bd_file='".$_FILES['bd_file']['name'][$_key]."',bd_link='".$bd_link[$_key]."',bd_title='".$bd_title[$_key]."', tmp_update='1', regdate = NOW() ";
                    $sql = "insert into shop_bannerinfo_detail 
								(bd_ix,banner_ix,bd_title,bd_link,bd_file,etc_code, regdate,tmp_update,vieworder) 
								values
								('','$banner_ix','".$bd_title[$_key]."','".$bd_link[$_key]."','".$_FILES['bd_file']['name'][$_key]."','".$company_id[$_key]."', NOW(),'1','".$bd_vieworder[$_key]."')";
                    $db->sequences = "SHOP_BANNERINFO_DETAIL_SEQ";
                    $db->query($sql);
                }
            }
        }
    }

	akamaiFtpUpload($admin_config[mall_data_root]."/images/banner/".$banner_ix,$akamaiFtpUploadFiles);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너가  정상적으로 등록되었습니다.');</script>");
	if($mmode == "pop"){
		echo("<script>parent.self.close();</script>");
	}else{
		if($agent_type == "M"){
			echo("<script>parent.document.location.href='../mShop/banner.php?SubID=$SubID&banner_page=$banner_page';</script>");
		}else{
			echo("<script>parent.document.location.href='banner.php?SubID=$SubID&banner_page=$banner_page';</script>");
		}
	}
	
}


if ($act == "update"){

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/",0777);
	}
	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/",0777);
	}

	//아카마이 ftp 파일 업로드
	$akamaiFtpUploadFiles = array();

	if ($banner_img)
	{
		copy($banner_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/".$banner_img_name);
		$akamaiFtpUploadFiles[] = $banner_img_name;
	}
	if($banner_img){
		$banner_img_str = ",banner_img='$banner_img_name'";
	}

	if ($banner_img_on)
	{
		copy($banner_img_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/".$banner_img_on_name);
		$akamaiFtpUploadFiles[] = $banner_img_on_name;
	}

	if (!$use_banner_btn_left)
	{
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/banner_btn_left.png")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/banner_btn_left.png");
		}
	}
	if ($banner_btn_left)
	{
		copy($banner_btn_left, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_left.png");
		$akamaiFtpUploadFiles[] = "banner_btn_left.png";
	}
	if (!$use_banner_btn_left_on)
	{
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/banner_btn_left_on.png")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/banner_btn_left_on.png");
		}
	}
	if ($banner_btn_left_on)
	{
		copy($banner_btn_left_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_left_on.png");
		$akamaiFtpUploadFiles[] = "banner_btn_left_on.png";
	}


	if (!$use_banner_btn_right)
	{
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/banner_btn_right.png")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/banner_btn_right.png");
		}
	}
	if ($banner_btn_right)
	{
		copy($banner_btn_right, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_right.png");
		$akamaiFtpUploadFiles[] = "banner_btn_right.png";
	}

	if (!$use_banner_btn_right_on)
	{
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/banner_btn_right_on.png")){	
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/banner_btn_right_on.png");
		}
	}
	if ($banner_btn_right_on)
	{
		copy($banner_btn_right_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/".$banner_ix."/banner_btn_right_on.png");
		$akamaiFtpUploadFiles[] = "banner_btn_right_on.png";
	}

	
	if($banner_img_on){
		$banner_img_on_str = ",banner_img_on='$banner_img_on_name'";
	}


	if($admininfo["admin_level"] == 9){
		if($company_id != ""){
			$company_id_str = ", company_id = '".$company_id."' ";
		}
	}
   // $use_sdate = $FromYY.$FromMM.$FromDD;
	//$use_edate = $ToYY.$ToMM.$ToDD;
    if($db->dbms_type == "oracle"){
        $_use_sdate = $use_sdate." ".$use_sdate_h.":".$use_sdate_i.":".$use_sdate_s;
        $_use_edate = $use_edate." ".$use_edate_h.":".$use_edate_i.":".$use_edate_s;
        $sql = "update shop_bannerinfo set
					mall_ix='$mall_ix',
					banner_kind='$banner_kind',
					banner_text_reversal='$banner_text_reversal',
					change_effect='$change_effect',					
					banner_name='$banner_name',
					shot_title = '$shot_title',
					banner_link='$banner_link',
					banner_target='$banner_target',
					banner_desc='$banner_desc',
					banner_page='$banner_page',
					banner_position='$banner_position',
					display_cid='$display_cid',
					banner_btn_position='$banner_btn_position',					
					banner_width = '$banner_width',
					banner_height = '$banner_height',
					banner_html = '$banner_html',					
					md_mem_ix = '$md_code',
					goal_cnt = '$goal_cnt',
					disp = '$disp' ,
					use_sdate = TO_DATE('".$_use_sdate."','MM-DD-YYYY HH24:MI:SS'),
					use_edate = TO_DATE('".$_use_edate."','MM-DD-YYYY HH24:MI:SS')
					$banner_img_str 
					$banner_img_on_str
					$company_id_str
					where banner_ix='$banner_ix' ";
    }else{
		$_use_sdate = $use_sdate." ".$use_sdate_h.":".$use_sdate_i.":".$use_sdate_s;
        $_use_edate = $use_edate." ".$use_edate_h.":".$use_edate_i.":".$use_edate_s;

		if($b_name == 'on'){
			$b_name = 'Y';
		}else{
			$b_name = 'N';
		}

		if($i_name == 'on'){
			$i_name = 'Y';
		}else{
			$i_name = 'N';
		}

		if($u_name == 'on'){
			$u_name = 'Y';
		}else{
			$u_name = 'N';
		}

		if($b_title == 'on'){
			$b_title = 'Y';
		}else{
			$b_title = 'N';
		}

		if($i_title == 'on'){
			$i_title = 'Y';
		}else{
			$i_title = 'N';
		}

		if($u_title == 'on'){
			$u_title = 'Y';
		}else{
			$u_title = 'N';
		}

		if($b_desc == 'on'){
			$b_desc = 'Y';
		}else{
			$b_desc = 'N';
		}

		if($i_desc == 'on'){
			$i_desc = 'Y';
		}else{
			$i_desc = 'N';
		}

		if($u_desc == 'on'){
			$u_desc = 'Y';
		}else{
			$u_desc = 'N';
		}

		if($b_name_m == 'on'){
			$b_name_m = 'Y';
		}else{
			$b_name_m = 'N';
		}

		if($i_name_m == 'on'){
			$i_name_m = 'Y';
		}else{
			$i_name_m = 'N';
		}

		if($u_name_m == 'on'){
			$u_name_m = 'Y';
		}else{
			$u_name_m = 'N';
		}

		if($b_title_m == 'on'){
			$b_title_m = 'Y';
		}else{
			$b_title_m = 'N';
		}

		if($i_title_m == 'on'){
			$i_title_m = 'Y';
		}else{
			$i_title_m = 'N';
		}

		if($u_title_m == 'on'){
			$u_title_m = 'Y';
		}else{
			$u_title_m = 'N';
		}

		if($b_desc_m == 'on'){
			$b_desc_m = 'Y';
		}else{
			$b_desc_m = 'N';
		}

		if($i_desc_m == 'on'){
			$i_desc_m = 'Y';
		}else{
			$i_desc_m = 'N';
		}

		if($u_desc_m == 'on'){
			$u_desc_m = 'Y';
		}else{
			$u_desc_m = 'N';
		}

        $sql = "update shop_bannerinfo set
					mall_ix='$mall_ix',
					banner_kind='$banner_kind',
					banner_text_reversal='$banner_text_reversal',
					change_effect='$change_effect',
					banner_name='$banner_name',
					shot_title='$shot_title',
					banner_link='$banner_link',
					banner_target='$banner_target',
					banner_desc='$banner_desc',
					banner_page='$banner_page',
					banner_position='$banner_position',
					display_cid='$display_cid',
					banner_btn_position='$banner_btn_position',
					banner_width = '$banner_width',
					banner_height = '$banner_height',
					banner_html = '$banner_html',
                    banner_html_m = '$banner_html_m',
					md_mem_ix = '$md_code',
					goal_cnt = '$goal_cnt',
					disp = '$disp' ,
                    b_name = '$b_name',
				 	i_name = '$i_name',
				 	u_name = '$u_name',
				 	c_name = '$c_name',
				 	s_name = '$s_name',
                    b_title = '$b_title',
				 	i_title = '$i_title',
				 	u_title = '$u_title',
				 	c_title = '$c_title',
				 	s_title = '$s_title',
                    b_desc = '$b_desc',
				 	i_desc = '$i_desc',
				 	u_desc = '$u_desc',
				 	c_desc = '$c_desc',
				 	s_desc = '$s_desc',
                    banner_loc = '$banner_loc', 
					banner_desc_m = '$banner_desc_m', 
					banner_name_m = '$banner_name_m', 
					shot_title_m = '$shot_title_m', 
					b_name_m = '$b_name_m', 
					i_name_m = '$i_name_m', 
					u_name_m = '$u_name_m', 
					c_name_m = '$c_name_m', 
					s_name_m = '$s_name_m', 
					b_title_m = '$b_title_m', 
					i_title_m = '$i_title_m', 
					u_title_m = '$u_title_m', 
					c_title_m = '$c_title_m', 
					s_title_m = '$s_title_m', 
					b_desc_m = '$b_desc_m', 
					i_desc_m = '$i_desc_m', 
					u_desc_m = '$u_desc_m', 
					c_desc_m = '$c_desc_m', 
					s_desc_m = '$s_desc_m',
					use_sdate = '$_use_sdate',
					use_edate = '$_use_edate'
					$banner_img_str 
					$banner_img_on_str
					$company_id_str
					where banner_ix='$banner_ix' ";
	}
	//echo nl2br($sql);
	//exit;

	$db->query($sql);

	$sql = "update shop_bannerinfo_detail set tmp_update='0' where banner_ix='".$banner_ix."' ";
	$db->query($sql);

//echo "<pre>";
//	print_r($_POST);
//	print_r($_FILES);
//exit;	

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner";

	if(is_array($_FILES['bd_file']['size'])){
		foreach($_FILES['bd_file']['size'] as $_key=>$_val)	{
			//echo $_key;
			//exit;
			if($bd_ix[$_key] != "" && $nondelete[$bd_ix[$_key]] == 1){
				$sql = "update shop_bannerinfo_detail set tmp_update='1' where bd_ix='".$bd_ix[$_key]."' ";
				//echo nl2br($sql);
				$db->query($sql);
			}
			if($_val > 0)	{
				copy($_FILES['bd_file']['tmp_name'][$_key], $path."/".$banner_ix."/".$_FILES['bd_file']['name'][$_key]);
				$akamaiFtpUploadFiles[] = $_FILES['bd_file']['name'][$_key];

				if($bd_ix[$_key] == ""){
					//$sql = "insert into shop_bannerinfo_detail set banner_ix = '$banner_ix', bd_file='".$_FILES['bd_file']['name'][$_key]."',bd_link='".$bd_link[$_key]."',bd_title='".$bd_title[$_key]."', tmp_update='1', regdate = NOW() ";
					$sql = "insert into shop_bannerinfo_detail(bd_ix,banner_ix,bd_title,bd_link,bd_file,bd_btn_out, bd_btn_over, etc_code, regdate,tmp_update,vieworder) values('','$banner_ix','".$bd_title[$_key]."','".$bd_link[$_key]."','".$_FILES['bd_file']['name'][$_key]."','".$_FILES['bd_btn_out']['name'][$_key]."','".$_FILES['bd_btn_over']['name'][$_key]."', '".$company_id[$_key]."', NOW(),'1','".$bd_vieworder[$_key]."')";
					$db->sequences = "SHOP_BANNERINFO_DETAIL_SEQ";
					$db->query($sql);
					$db->query("SELECT bd_ix FROM shop_bannerinfo_detail WHERE bd_ix=LAST_INSERT_ID()");
					$db->fetch();
					$this_bd_ix = $db->dt[bd_ix];

				} else {
					$sql = "update shop_bannerinfo_detail set 
								bd_file='".$_FILES['bd_file']['name'][$_key]."',
								bd_link='".$bd_link[$_key]."',
								bd_title='".$bd_title[$_key]."', 
								vieworder='".$bd_vieworder[$_key]."', 
								etc_code='".$company_id[$_key]."', 								
								tmp_update='1' 
								where bd_ix='".$bd_ix[$_key]."' ";
					
					$db->query($sql);
				}
				//echo nl2br($sql);
			}
		}
	}
 
	if(is_array($_FILES['bd_btn_out']['size'])){
		foreach($_FILES['bd_btn_out']['size'] as $_key=>$_val)	{
			//echo $_key;
			//exit;
			if($bd_ix[$_key] != "" && $nondelete_out[$bd_ix[$_key]] != 1){
				$sql = "update shop_bannerinfo_detail set bd_btn_out='' where bd_ix='".$bd_ix[$_key]."' ";
				$db->query($sql);
			}
			if($_val > 0)	{
				copy($_FILES['bd_btn_out']['tmp_name'][$_key], $path."/".$banner_ix."/".$_FILES['bd_btn_out']['name'][$_key]);
				$akamaiFtpUploadFiles[] = $_FILES['bd_btn_out']['name'][$_key];
				if($this_bd_ix){
					$sql = "select * from shop_bannerinfo_detail where bd_ix = '".$this_bd_ix."' ";
				}else{
					$sql = "select * from shop_bannerinfo_detail where bd_ix = '".$bd_ix[$_key]."' ";
				}
				//echo $sql;
				//exit;
				$db->query($sql);

				if($db->total == 0){
					//$sql = "insert into shop_bannerinfo_detail set banner_ix = '$banner_ix', bd_btn_out='".$_FILES['bd_btn_out']['name'][$_key]."',bd_link='".$bd_link[$_key]."',bd_title='".$bd_title[$_key]."', tmp_update='1', regdate = NOW() ";
					$sql = "insert into shop_bannerinfo_detail(bd_ix,banner_ix,bd_title,bd_link,bd_btn_out,regdate,tmp_update,vieworder) values('','$banner_ix','".$bd_title[$_key]."','".$bd_link[$_key]."','".$_FILES['bd_btn_out']['name'][$_key]."', NOW(),'1','".$bd_vieworder[$_key]."')";
					$db->sequences = "SHOP_BANNERINFO_DETAIL_SEQ";
					$db->query($sql);
					$db->query("SELECT bd_ix FROM shop_bannerinfo_detail WHERE bd_ix=LAST_INSERT_ID()");
					$db->fetch();
					$this_bd_ix = $db->dt[bd_ix];
				} else {
					$sql = "update shop_bannerinfo_detail set bd_btn_out='".$_FILES['bd_btn_out']['name'][$_key]."',bd_link='".$bd_link[$_key]."',bd_title='".$bd_title[$_key]."', vieworder='".$bd_vieworder[$_key]."', tmp_update='1' where
					bd_ix='".$bd_ix[$_key]."' ";
					$db->query($sql);
				}
			}
		}
	}

	if(is_array($_FILES['bd_btn_over']['size'])){
		foreach($_FILES['bd_btn_over']['size'] as $_key=>$_val)	{
			//echo $_key;
			//exit;
			if($bd_ix[$_key] != "" && $nondelete_over[$bd_ix[$_key]] != 1){
				$sql = "update shop_bannerinfo_detail set bd_btn_over='' where bd_ix='".$bd_ix[$_key]."' ";
				$db->query($sql);
			}
			if($_val > 0)	{
				copy($_FILES['bd_btn_over']['tmp_name'][$_key], $path."/".$banner_ix."/".$_FILES['bd_btn_over']['name'][$_key]);
				$akamaiFtpUploadFiles[] = $_FILES['bd_btn_over']['name'][$_key];

				if($this_bd_ix){
					$sql = "select * from shop_bannerinfo_detail where bd_ix = '".$this_bd_ix."' ";
				}else{
					$sql = "select * from shop_bannerinfo_detail where bd_ix = '".$bd_ix[$_key]."' ";
				}
				$db->query($sql);

				if($db->total == 0){
					//$sql = "insert into shop_bannerinfo_detail set banner_ix = '$banner_ix', bd_btn_over='".$_FILES['bd_btn_over']['name'][$_key]."',bd_link='".$bd_link[$_key]."',bd_title='".$bd_title[$_key]."', tmp_update='1', regdate = NOW() ";
					$sql = "insert into shop_bannerinfo_detail(bd_ix,banner_ix,bd_title,bd_link,bd_btn_over,regdate,tmp_update,vieworder) values('','$banner_ix','".$bd_title[$_key]."','".$bd_link[$_key]."','".$_FILES['bd_btn_over']['name'][$_key]."', NOW(),'1','".$bd_vieworder[$_key]."')";
					$db->sequences = "SHOP_BANNERINFO_DETAIL_SEQ";
					$db->query($sql);
					$db->query("SELECT bd_ix FROM shop_bannerinfo_detail WHERE bd_ix=LAST_INSERT_ID()");
					$db->fetch();
					$this_bd_ix = $db->dt[bd_ix];
				} else {
					$sql = "update shop_bannerinfo_detail set bd_btn_over='".$_FILES['bd_btn_over']['name'][$_key]."',bd_link='".$bd_link[$_key]."',bd_title='".$bd_title[$_key]."', vieworder='".$bd_vieworder[$_key]."', tmp_update='1' where
					bd_ix='".$bd_ix[$_key]."' ";
					$db->query($sql);

				}
			}
		}
	}

	if(is_array($bd_ix)){
		foreach($bd_ix as $_key=>$_val)	{
			if($bd_ix[$_key] != ""){
				$sql = "update shop_bannerinfo_detail set 
							bd_link='".$bd_link[$_key]."',
							bd_title='".$bd_title[$_key]."', 
							vieworder='".$bd_vieworder[$_key]."', 
							etc_code = '".$company_id[$_key]."',
							tmp_update='1' 
							where bd_ix='".$bd_ix[$_key]."' ";
				//echo nl2br($sql);
				$db->query($sql);
			}
			/*
			if($bd_ix[$_key] != "" && $nondelete_out[$bd_ix[$_key]] != 1){
				$sql = "update shop_bannerinfo_detail set bd_btn_out='' where bd_ix='".$bd_ix[$_key]."' ";
				$db->query($sql);
			}

			if($bd_ix[$_key] != "" && $nondelete_over[$bd_ix[$_key]] != 1){
				$sql = "update shop_bannerinfo_detail set bd_btn_over='' where bd_ix='".$bd_ix[$_key]."' ";
				$db->query($sql);
			}
			*/
		}
	}
 
	akamaiFtpUpload($admin_config[mall_data_root]."/images/banner/".$banner_ix,$akamaiFtpUploadFiles);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너가  정상적으로 수정되었습니다.');</script>");
	if($mmode == "pop"){
		echo("<script>parent.location.reload();//parent.self.close();</script>");
	}else{
		if($agent_type == "M"){
			echo("<script>parent.document.location.href='../mShop/banner.php?SubID=$SubID&banner_page=$banner_page';</script>");
		}else{
			echo("<script>parent.document.location.href = 'banner.php?SubID=$SubID&banner_page=$banner_page';</script>");
		}
	}
}

if ($act == "delete"){

	if($banner_ix && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix")){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix");
	}
	$sql = "select banner_page from shop_bannerinfo where banner_ix='$banner_ix' ";
	$db->query($sql);
	$db->fetch();
	$banner_page = $db->dt[banner_page];

	$sql = "delete from shop_bannerinfo where banner_ix='$banner_ix' ";
	$db->query($sql);

	$sql = "delete from shop_bannerinfo_detail where banner_ix='".$banner_ix."' ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('배너가 정상적으로 삭제되었습니다.','parent_reload');</script>");
//	if($agent_type == "M"){
//		echo("<script>parent.document.location.href='../mShop/banner.php?SubID=$SubID&banner_page=$banner_page';</script>");
//	}else{
//		echo("<script>parent.document.location.href='banner.php?SubID=$SubID&banner_page=$banner_page';</script>");
//	}
	exit;
}

if($act=="del_detail_img") {
	$sql="SELECT bd_file FROM shop_bannerinfo_detail WHERE banner_ix='".$banner_ix."' AND bd_ix='".$bd_ix."' ";
	$db->query($sql);
	if($db->total) {
		$db->fetch();
		$bd_file=$db->dt["bd_file"];
		if($banner_ix && is_file($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/$bd_file")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/banner/$banner_ix/$bd_file");
		}
		$sql="DELETE FROM shop_bannerinfo_detail WHERE banner_ix='".$banner_ix."' AND bd_ix='".$bd_ix."' ";
		$db->query($sql);

		echo "Y";
	} else {
		echo "N";
	}
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
?>
