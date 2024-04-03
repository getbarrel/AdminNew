<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

if ($act == "update")
{
	$popup_text = $content;
	$popup_use_sdate = $popup_use_sdate." ".$popup_use_sdate_h.":".$popup_use_sdate_i.":".$popup_use_sdate_s;
	$popup_use_edate = $popup_use_edate." ".$popup_use_edate_h.":".$popup_use_edate_i.":".$popup_use_edate_s;
	if($popup_today == 1){
		$popup_height = $popup_height + 30;
	}else{
		$popup_height = $popup_height;
	}
	$sql = "update ".TBL_SHOP_POPUP." set
			mall_ix='$mall_ix',
			popup_position='$popup_position',
			popup_title='$popup_title',popup_text='$popup_text',popup_use_sdate='$popup_use_sdate',popup_use_edate='$popup_use_edate',popup_width='$popup_width',popup_height='$popup_height',popup_top='$popup_top',popup_left='$popup_left',popup_today='$popup_today',disp='$disp',popup_type = '$popup_type' ,popup_display_type = '$popup_display_type' , display_position = '$display_position', 
			display_url = '$display_url', 
			display_target = '$display_target', 
			display_sub_target = '$display_sub_target', 
			display_title_type = '$display_title_type', 
			display_title_img = '".$_FILES['display_title_img'][name]."', 
			display_title_text = '$display_title_text', 			
			product_cnt = '$product_cnt',
			goods_display_type = '$goods_display_type',
			display_auto_sub_type = '$display_auto_sub_type',
			display_order_type = '$display_order_type',
			recent_priod = '$recent_priod',
			is_use_templet = '$is_use_templet'	,
			mobile_type = '".serialize($mobile_type)."'
			where popup_ix='$popup_ix'";
//echo nl2br($sql);
//exit;

	$db->query($sql);

	$data_text = $popup_text;
	$data_text_convert = $popup_text;
	$data_text_convert = str_replace("\\","",$data_text_convert);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}


	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/$popup_ix/";

	//if(substr_count($data_text,"<IMG") > 0){
		if(!is_dir($path)){
			mkdir($path, 0777);
			//chmod($path,0777)
		}
	//}

	if($display_position == "C"){
		$pdr_div = "P"; /// Position 
		$pdr_sub_div = "C";

		$db->query("update shop_popup_display_relation set insert_yn = 'N'  where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and popup_ix = '".$popup_ix."'  ");

		for($j=0;$j < count($selected_result[category]);$j++){
			$db->query("select pdr_ix from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[category][$j]."' and popup_ix = '".$popup_ix."'  ");

			if(!$db->total){
				$sql = "insert into shop_popup_display_relation 
							(pdr_ix,popup_ix , r_ix,pdr_div, pdr_sub_div,  vieworder, insert_yn, regdate) 
							values 
							('','".$popup_ix."','".$selected_result[category][$j]."','".$pdr_div."','".$pdr_sub_div."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_popup_display_relation set insert_yn = 'Y',vieworder='".($j+1)."', pdr_sub_div = '".$pdr_sub_div."' 
							where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[category][$j]."' and popup_ix = '".$popup_ix."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and insert_yn = 'N' and popup_ix = '".$popup_ix."'  ");
	}

	if($display_target == "T" && $display_sub_target == "G"){
		$pdr_div = "T";
		$pdr_sub_div = "G";

		$db->query("update shop_popup_display_relation set insert_yn = 'N'  where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and popup_ix = '".$popup_ix."'  ");

		for($j=0;$j < count($selected_result[group]);$j++){
			$db->query("select pdr_ix from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[group][$j]."' and popup_ix = '".$popup_ix."'  ");

			if(!$db->total){
				$sql = "insert into shop_popup_display_relation 
							(pdr_ix,popup_ix , r_ix,pdr_div, pdr_sub_div,  vieworder, insert_yn, regdate) 
							values 
							('','".$popup_ix."','".$selected_result[group][$j]."','".$pdr_div."','".$pdr_sub_div."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_popup_display_relation set insert_yn = 'Y',vieworder='".($j+1)."', pdr_sub_div = '".$pdr_sub_div."' 
							where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[group][$j]."' and popup_ix = '".$popup_ix."'  ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and insert_yn = 'N' and popup_ix = '".$popup_ix."'  ");
	}

	if($display_target == "T" && $display_sub_target == "M"){
		$pdr_div = "T";
		$pdr_sub_div = "M";

		$db->query("update shop_popup_display_relation set insert_yn = 'N'  where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and popup_ix = '".$popup_ix."'  ");

		for($j=0;$j < count($selected_result[member]);$j++){
			$db->query("select pdr_ix from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[member][$j]."' and popup_ix = '".$popup_ix."'  ");

			if(!$db->total){
				$sql = "insert into shop_popup_display_relation 
							(pdr_ix, popup_ix ,r_ix,pdr_div, pdr_sub_div,  vieworder, insert_yn, regdate) 
							values 
							('','".$popup_ix."','".$selected_result[member][$j]."','".$pdr_div."','".$pdr_sub_div."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_popup_display_relation set insert_yn = 'Y',vieworder='".($j+1)."', pdr_sub_div = '".$pdr_sub_div."' 
							where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[member][$j]."' and popup_ix = '".$popup_ix."'  ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and insert_yn = 'N' and popup_ix = '".$popup_ix."'  ");
	}

//print_r($_FILES);
//print_r($_POST);
//exit;
	//$db->debug = true;
		if(is_array($_POST['popup_banner'])){
			$db->query("update shop_popup_bannerinfo_detail set insert_yn = 'N'  where  popup_ix = '".$popup_ix."'  ");

			foreach($_POST["popup_banner"] as $key=>$value) {

				//print_r($_POST['popup_banner'][0]);
				//echo "key:".$key."::".$_FILES['popup_banner'][name][$key][b]."<br>";
				
				$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/".$popup_ix."/";
				$update_str = "";
				if($_FILES['popup_banner'][size][$key][b] > 0){
					copy($_FILES['popup_banner'][tmp_name][$key][b], $path.$_FILES['popup_banner'][name][$key][b]);
					$update_str .= ", bd_file_b = '".$_FILES['popup_banner'][name][$key][b]."' ";
				}
				if($_FILES['popup_banner'][size][$key][s] > 0){
					copy($_FILES['popup_banner'][tmp_name][$key][s], $path.$_FILES['popup_banner'][name][$key][s]);
					$update_str .= ", bd_file_s = '".$_FILES['popup_banner'][name][$key][s]."' ";
				}
				
				if($popup_banner[$key][pbd_ix]){
					$sql = "update shop_popup_bannerinfo_detail set 
								bd_title = '".$popup_banner[$key][title]."'
								,bd_link = '".$popup_banner[$key][link]."'
								,insert_yn = 'Y'
								".$update_str."
								where pbd_ix = '".$popup_banner[$key][pbd_ix]."' and popup_ix = '".$popup_ix."'
								";
				}else{
					$sql = "insert into shop_popup_bannerinfo_detail 
								(pbd_ix,popup_ix,bd_title,bd_link,bd_file_b, bd_file_s,insert_yn, regdate,tmp_update) 
								values
								('','$popup_ix','".$popup_banner[$key][title]."','".$popup_banner[$key][link]."','".$_FILES['popup_banner'][name][$key][b]."', '".$_FILES['popup_banner'][name][$key][s]."', 'Y', NOW(),'1')";
				}
				//echo nl2br($sql);
				//exit;
				$db->sequences = "SHOP_POPUP_DETAIL_SEQ";
				$db->query($sql);
 
			}
			$db->query("delete from shop_popup_bannerinfo_detail where  insert_yn = 'N' and popup_ix = '".$popup_ix."'  ");
	}
//	exit;

	if($_FILES['display_title_img'][size] > 0){
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/".$popup_ix."/";
		copy($_FILES['display_title_img'][tmp_name], $path."pop_dp_title.jpg");
		//$update_str .= ", bd_file_s = '".$_FILES['display_title_img'][name][$key][s]."' ";
	}
	//print_r($_POST);
	
	//$db->debug = true;
	$db->query("update shop_popup_display set insert_yn = 'N' where popup_ix = '".$popup_ix."'   ");

	for($j=0;$j < count($display_type[1][type]);$j++){
		$db->query("select pd_ix from shop_popup_display where pd_ix = '".$display_type[$i+1][pd_ix][$j]."'   ");

		if(!$db->total){
			$sql = "insert into shop_popup_display (pd_ix,popup_ix, display_type, set_cnt, vieworder, insert_yn, regdate) values ('','".$popup_ix."','".$display_type[$i+1][type][$j]."','".$display_type[$i+1][set_cnt][$j]."','".($j+1)."','Y', NOW())";
			$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
			$db->query($sql);
		}else{
			$sql = "update shop_popup_display set insert_yn = 'Y',vieworder='".($j+1)."', display_type = '".$display_type[$i+1][type][$j]."',set_cnt = '".$display_type[$i+1][set_cnt][$j]."' 
						where pd_ix = '".$display_type[$i+1][pd_ix][$j]."'  ";
			$db->query($sql);
		}
	}
	$db->query("delete from shop_popup_display where popup_ix = '".$popup_ix."' and insert_yn = 'N' ");

	/**
		* 노출카테고리 관련
		* 담당자 : shs 
		*/
		
		$db->query("update shop_popup_category_relation set insert_yn = 'N'  where popup_ix = '".$popup_ix."'  ");

		for($j=0;$j < count($category[$i+1]);$j++){
			$db->query("select pcr_ix from shop_popup_category_relation where popup_ix = '".$popup_ix."'  and cid = '".$category[$i+1][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_popup_category_relation (pcr_ix,cid,popup_ix, vieworder, insert_yn, regdate) values ('','".$category[$i+1][$j]."','".$popup_ix."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_POPUP_CT_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_popup_category_relation set insert_yn = 'Y',vieworder='".($j+1)."' where popup_ix = '".$popup_ix."'  and cid = '".$category[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_category_relation where popup_ix = '".$popup_ix."'  and insert_yn = 'N' ");

		/**
		* 노출 브랜드 관련
		* 담당자 : shs 
		* 작업일시 : 2014년 03월 30일
		*/
		//$db->debug = true;
		$db->query("update shop_popup_brand_relation set insert_yn = 'N'  where popup_ix = '".$popup_ix."'  ");

		for($j=0;$j < count($selected_result[$i+1]['brand']);$j++){
			$db->query("select pbr_ix from shop_popup_brand_relation where popup_ix = '".$popup_ix."'  and b_ix = '".$selected_result[$i+1]['brand'][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_popup_brand_relation (pbr_ix,b_ix,popup_ix, vieworder, insert_yn, regdate) values ('','".$selected_result[$i+1]['brand'][$j]."','".$popup_ix."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_POPUP_BRAND_SEQ";
				$db->query($sql);
			}else{
				$db->fetch();
				$pbr_ix = $db->dt[pbr_ix];
				$sql = "update shop_popup_brand_relation set insert_yn = 'Y',vieworder='".($j+1)."' where pbr_ix = '".$pbr_ix."' and popup_ix = '".$popup_ix."'  and b_ix = '".$selected_result[$i+1]['brand'][$j]."' ";
				$db->query($sql);
			}
		}
	
		$db->query("delete from shop_popup_brand_relation where popup_ix = '".$popup_ix."'  and insert_yn = 'N' ");
	//exit;
		/**
		* 노출 셀러 관련
		* 담 당  자 : shs 
		* 작업일시 : 2014년 03월 30일
		*/
		//$db->debug = true;
		$db->query("update shop_popup_seller_relation set insert_yn = 'N'  where popup_ix = '".$popup_ix."'  ");
	
		for($j=0;$j < count($selected_result[$i+1]['seller']);$j++){
			$db->query("select psr_ix from shop_popup_seller_relation where popup_ix = '".$popup_ix."'  and company_id = '".$selected_result[$i+1]['seller'][$j]."' ");
			
			if(!$db->total){
				$sql = "insert into shop_popup_seller_relation (psr_ix,company_id,popup_ix, vieworder, insert_yn, regdate) values ('','".$selected_result[$i+1]['seller'][$j]."','".$popup_ix."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_POPUP_SELLER_SEQ";
				$db->query($sql);
			}else{
				$db->fetch();
				$psr_ix = $db->dt[psr_ix];
				$sql = "update shop_popup_seller_relation set insert_yn = 'Y',vieworder='".($j+1)."' where psr_ix = '".$psr_ix."' and  popup_ix = '".$popup_ix."'  and company_id = '".$selected_result[$i+1]['seller'][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_seller_relation where popup_ix = '".$popup_ix."'  and insert_yn = 'N' ");

		$db->query("update shop_popup_product_relation set insert_yn = 'N' where popup_ix='".$popup_ix."'  ");

		for($j=0;$j < count($rpid[$i+1]);$j++){
			$db->query("select ppr_ix from shop_popup_product_relation where popup_ix = '".$popup_ix."'  and pid = '".$rpid[$i+1][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_popup_product_relation (ppr_ix,pid,popup_ix, vieworder, insert_yn, regdate) values ('','".$rpid[$i+1][$j]."','".$popup_ix."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_popup_product_relation set insert_yn = 'Y',vieworder='".($j+1)."' where popup_ix = '".$popup_ix."'  and pid = '".$rpid[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_product_relation where popup_ix = '".$popup_ix."'  and insert_yn = 'N' ");

	for($i=0;$i < count($out);$i++){
		for($j=0;$j < count($out[$i]);$j++){

			$img = returnImagePath($out[$i][$j]);
			$img = ClearText($img);


			if(substr_count($img,$admin_config[mall_data_root]."/images/popup/$popup_ix/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
				if(substr_count($img,"$HTTP_HOST")>0){
					$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

					@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/$popup_ix/".returnFileName($img));
					if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
						unlink($local_img_path);
					}

					$data_text = str_replace($img,$admin_config[mall_data_root]."/images/popup/$popup_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
				}else{
					if(@copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/$popup_ix/".returnFileName($img))){
						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/popup/$popup_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}
				}
			}



		}
	}
	$data_text = str_replace("http://$HTTP_HOST","",$data_text);

	$db->query("UPDATE ".TBL_SHOP_POPUP." SET popup_text = '$data_text' WHERE popup_ix='$popup_ix'");

	//echo("<script>top.location.href = 'popup.write.php?popup_ix=$popup_ix';</script>");
	if($mmode == "pop"){
		echo("<script>parent.opener.document.location.reload();parent.self.close();</script>");
	}else{
		echo("<script>top.location.href = 'popup.list.php';</script>");
	}
}

if ($act == "insert"){

	$popup_text = $content;
	$popup_use_sdate = $popup_use_sdate." ".$popup_use_sdate_h.":".$popup_use_sdate_i.":".$popup_use_sdate_s;
	$popup_use_edate = $popup_use_edate." ".$popup_use_edate_h.":".$popup_use_edate_i.":".$popup_use_edate_s;
	if($popup_today == 1){
		$popup_height = $popup_height + 30;
	}else{
		$popup_height = $popup_height;
	}

	$db->sequences = "SHOP_POPUP_SEQ";
	$db->query("insert into ".TBL_SHOP_POPUP."(popup_ix, mall_ix, popup_position, popup_title,popup_text,popup_use_sdate,popup_use_edate,popup_width,popup_height,popup_top,popup_left,popup_today,popup_type,is_use_templet, popup_display_type, display_position, display_url, display_target, display_sub_target, display_title_type, display_title_img, display_title_text, product_cnt, goods_display_type, display_auto_sub_type, display_order_type,recent_priod, disp,mobile_type,regdate) values('','$mall_ix','$popup_position','$popup_title','$popup_text','$popup_use_sdate','$popup_use_edate','$popup_width','$popup_height','$popup_top','$popup_left','$popup_today','$popup_type','$is_use_templet','$popup_display_type','$display_position','$display_url','$display_target','$display_sub_target','$display_title_type','$display_title_img','$display_title_text','$product_cnt','$goods_display_type','$display_auto_sub_type', '$display_order_type','$recent_priod', '$disp','".serialize($mobile_type)."', NOW())");

	if($db->dbms_type == "oracle"){
		$popup_ix = $db->last_insert_id;
	}else{
		$db->query("SELECT popup_ix FROM ".TBL_SHOP_POPUP." WHERE popup_ix=LAST_INSERT_ID()");
		$db->fetch();
		$popup_ix = $db->dt[0];
	}

	$data_text = $popup_text;
	$data_text_convert = $popup_text;
	$data_text_convert = str_replace("\\","",$data_text_convert);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);


	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/$popup_ix/";

	//if(substr_count($data_text,"<IMG") > 0){
		if(!is_dir($path)){
			mkdir($path, 0777);
			//chmod($path,0777)
		}
	//}

	if($display_position == "C"){
		$pdr_div = "P"; /// Position 
		$pdr_sub_div = "C";

		$db->query("update shop_popup_display_relation set insert_yn = 'N'  where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and popup_ix = '".$popup_ix."'  ");

		for($j=0;$j < count($selected_result[category]);$j++){
			$db->query("select pdr_ix from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[category][$j]."' and popup_ix = '".$popup_ix."'  ");

			if(!$db->total){
				$sql = "insert into shop_popup_display_relation 
							(pdr_ix,popup_ix , r_ix,pdr_div, pdr_sub_div,  vieworder, insert_yn, regdate) 
							values 
							('','".$popup_ix."','".$selected_result[category][$j]."','".$pdr_div."','".$pdr_sub_div."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_popup_display_relation set insert_yn = 'Y',vieworder='".($j+1)."', pdr_sub_div = '".$pdr_sub_div."' 
							where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[category][$j]."' and popup_ix = '".$popup_ix."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and insert_yn = 'N' and popup_ix = '".$popup_ix."'  ");
	}

	if($display_target == "T" && $display_sub_target == "G"){
		$pdr_div = "T";
		$pdr_sub_div = "G";

		$db->query("update shop_popup_display_relation set insert_yn = 'N'  where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and popup_ix = '".$popup_ix."'  ");

		for($j=0;$j < count($selected_result[group]);$j++){
			$db->query("select pdr_ix from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[group][$j]."' and popup_ix = '".$popup_ix."'  ");

			if(!$db->total){
				$sql = "insert into shop_popup_display_relation 
							(pdr_ix,popup_ix , r_ix,pdr_div, pdr_sub_div,  vieworder, insert_yn, regdate) 
							values 
							('','".$popup_ix."','".$selected_result[group][$j]."','".$pdr_div."','".$pdr_sub_div."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_popup_display_relation set insert_yn = 'Y',vieworder='".($j+1)."', pdr_sub_div = '".$pdr_sub_div."' 
							where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[group][$j]."' and popup_ix = '".$popup_ix."'  ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and insert_yn = 'N' and popup_ix = '".$popup_ix."'  ");
	}

	if($display_target == "T" && $display_sub_target == "M"){
		$pdr_div = "T";
		$pdr_sub_div = "M";

		$db->query("update shop_popup_display_relation set insert_yn = 'N'  where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and popup_ix = '".$popup_ix."'  ");

		for($j=0;$j < count($selected_result[member]);$j++){
			$db->query("select pdr_ix from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[member][$j]."' and popup_ix = '".$popup_ix."'  ");

			if(!$db->total){
				$sql = "insert into shop_popup_display_relation 
							(pdr_ix, popup_ix ,r_ix,pdr_div, pdr_sub_div,  vieworder, insert_yn, regdate) 
							values 
							('','".$popup_ix."','".$selected_result[member][$j]."','".$pdr_div."','".$pdr_sub_div."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_popup_display_relation set insert_yn = 'Y',vieworder='".($j+1)."', pdr_sub_div = '".$pdr_sub_div."' 
							where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and r_ix = '".$selected_result[member][$j]."' and popup_ix = '".$popup_ix."'  ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_display_relation where pdr_div = '".$pdr_div."' and pdr_sub_div = '".$pdr_sub_div."' and insert_yn = 'N' and popup_ix = '".$popup_ix."'  ");
	}

//print_r($_FILES);
//print_r($_POST);
		if(is_array($_POST['popup_banner'])){
			//$db->debug = true;
			foreach($_POST["popup_banner"] as $key=>$value) {
				//print_r($_POST['popup_banner'][0]);
				//echo "key:".$key."::".$_FILES['popup_banner'][name][$key][b]."<br>";
				
				$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/".$popup_ix."/";
				$update_str = "";
				if($_FILES['popup_banner'][size][$key][b] > 0){
					copy($_FILES['popup_banner'][tmp_name][$key][b], $path.$_FILES['popup_banner'][name][$key][b]);
					$update_str .= ", bd_file_b = '".$_FILES['popup_banner'][name][$key][b]."' ";
				}
				if($_FILES['popup_banner'][size][$key][s] > 0){
					copy($_FILES['popup_banner'][tmp_name][$key][s], $path.$_FILES['popup_banner'][name][$key][s]);
					$update_str .= ", bd_file_s = '".$_FILES['popup_banner'][name][$key][s]."' ";
				}
				
				if($popup_banner[$key][pbd_ix]){
					$sql = "update shop_popup_bannerinfo_detail set 
								bd_title = '".$popup_banner[$key][title]."'
								,bd_link = '".$popup_banner[$key][link]."'
								".$update_str."
								where pbd_ix = '".$pbd_ix."' and popup_ix = '".$popup_ix."'
								";
				}else{
					$sql = "insert into shop_popup_bannerinfo_detail 
								(pbd_ix,popup_ix,bd_title,bd_link,bd_file_b, bd_file_s,regdate,tmp_update) 
								values
								('','$popup_ix','".$popup_banner[$key][title]."','".$popup_banner[$key][link]."','".$_FILES['popup_banner'][name][$key][b]."', '".$_FILES['popup_banner'][name][$key][s]."', NOW(),'1')";
				}
				//echo nl2br($sql);
				//exit;
				$db->sequences = "SHOP_POPUP_DETAIL_SEQ";
				$db->query($sql);
 
			}
	}
	//exit;

	if($_FILES['display_title_img'][size] > 0){
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/".$popup_ix."/";
		copy($_FILES['display_title_img'][tmp_name], $path."pop_dp_title.jpg");
		//$update_str .= ", bd_file_s = '".$_FILES['display_title_img'][name][$key][s]."' ";
	}
	//print_r($_POST);
	//exit;
	//$db->debug = true;
	$db->query("update shop_popup_display set insert_yn = 'N' where popup_ix = '".$popup_ix."'   ");

	for($j=0;$j < count($display_type[1][type]);$j++){
		$db->query("select pd_ix from shop_popup_display where pd_ix = '".$display_type[$i+1][pd_ix][$j]."'   ");

		if(!$db->total){
			$sql = "insert into shop_popup_display (pd_ix,popup_ix, display_type, set_cnt, vieworder, insert_yn, regdate) values ('','".$popup_ix."','".$display_type[$i+1][type][$j]."','".$display_type[$i+1][set_cnt][$j]."','".($j+1)."','Y', NOW())";
			$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
			$db->query($sql);
		}else{
			$sql = "update shop_popup_display set insert_yn = 'Y',vieworder='".($j+1)."', display_type = '".$display_type[$i+1][type][$j]."',set_cnt = '".$display_type[$i+1][set_cnt][$j]."' 
						where pd_ix = '".$display_type[$i+1][pd_ix][$j]."'  ";
			$db->query($sql);
		}
	}
	$db->query("delete from shop_popup_display where popup_ix = '".$popup_ix."' and insert_yn = 'N' ");

	/**
		* 노출카테고리 관련
		* 담당자 : shs 
		*/
		
		$db->query("update shop_popup_category_relation set insert_yn = 'N'  where popup_ix = '".$popup_ix."'  ");

		for($j=0;$j < count($category[$i+1]);$j++){
			$db->query("select pcr_ix from shop_popup_category_relation where popup_ix = '".$popup_ix."'  and cid = '".$category[$i+1][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_popup_category_relation (pcr_ix,cid,popup_ix, vieworder, insert_yn, regdate) values ('','".$category[$i+1][$j]."','".$popup_ix."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_POPUP_CT_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_popup_category_relation set insert_yn = 'Y',vieworder='".($j+1)."' where popup_ix = '".$popup_ix."'  and cid = '".$category[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_category_relation where popup_ix = '".$popup_ix."'  and insert_yn = 'N' ");

		/**
		* 노출 브랜드 관련
		* 담당자 : shs 
		* 작업일시 : 2014년 03월 30일
		*/
		//$db->debug = true;
		$db->query("update shop_popup_brand_relation set insert_yn = 'N'  where popup_ix = '".$popup_ix."'  ");

		for($j=0;$j < count($selected_result[$i+1]['brand']);$j++){
			$db->query("select pbr_ix from shop_popup_brand_relation where popup_ix = '".$popup_ix."'  and b_ix = '".$selected_result[$i+1]['brand'][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_popup_brand_relation (pbr_ix,b_ix,popup_ix, vieworder, insert_yn, regdate) values ('','".$selected_result[$i+1]['brand'][$j]."','".$popup_ix."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_POPUP_BRAND_SEQ";
				$db->query($sql);
			}else{
				$db->fetch();
				$pbr_ix = $db->dt[pbr_ix];
				$sql = "update shop_popup_brand_relation set insert_yn = 'Y',vieworder='".($j+1)."' where pbr_ix = '".$pbr_ix."' and popup_ix = '".$popup_ix."'  and b_ix = '".$selected_result[$i+1]['brand'][$j]."' ";
				$db->query($sql);
			}
		}
	
		$db->query("delete from shop_popup_brand_relation where popup_ix = '".$popup_ix."'  and insert_yn = 'N' ");
	//exit;
		/**
		* 노출 셀러 관련
		* 담 당  자 : shs 
		* 작업일시 : 2014년 03월 30일
		*/
		//$db->debug = true;
		$db->query("update shop_popup_seller_relation set insert_yn = 'N'  where popup_ix = '".$popup_ix."'  ");
	
		for($j=0;$j < count($selected_result[$i+1]['seller']);$j++){
			$db->query("select psr_ix from shop_popup_seller_relation where popup_ix = '".$popup_ix."'  and company_id = '".$selected_result[$i+1]['seller'][$j]."' ");
			
			if(!$db->total){
				$sql = "insert into shop_popup_seller_relation (psr_ix,company_id,popup_ix, vieworder, insert_yn, regdate) values ('','".$selected_result[$i+1]['seller'][$j]."','".$popup_ix."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_POPUP_SELLER_SEQ";
				$db->query($sql);
			}else{
				$db->fetch();
				$psr_ix = $db->dt[psr_ix];
				$sql = "update shop_popup_seller_relation set insert_yn = 'Y',vieworder='".($j+1)."' where psr_ix = '".$psr_ix."' and  popup_ix = '".$popup_ix."'  and company_id = '".$selected_result[$i+1]['seller'][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_seller_relation where popup_ix = '".$popup_ix."'  and insert_yn = 'N' ");

		$db->query("update shop_popup_product_relation set insert_yn = 'N' where popup_ix='".$popup_ix."'  ");

		for($j=0;$j < count($rpid[$i+1]);$j++){
			$db->query("select ppr_ix from shop_popup_product_relation where popup_ix = '".$popup_ix."'  and pid = '".$rpid[$i+1][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_popup_product_relation (ppr_ix,pid,popup_ix, vieworder, insert_yn, regdate) values ('','".$rpid[$i+1][$j]."','".$popup_ix."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_popup_product_relation set insert_yn = 'Y',vieworder='".($j+1)."' where popup_ix = '".$popup_ix."'  and pid = '".$rpid[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_popup_product_relation where popup_ix = '".$popup_ix."'  and insert_yn = 'N' ");


	for($i=0;$i < count($out);$i++){
		for($j=0;$j < count($out[$i]);$j++){

			$img = returnImagePath($out[$i][$j]);
			$img = ClearText($img);


			if(substr_count($img,$admin_config[mall_data_root]."/images/popup/$popup_ix/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
				if(substr_count($img,"$HTTP_HOST")>0){// 현제 서버에 존재 하는 이미지 인지 판단 , 현재 서버에 있으면 localpath 로 변환해서 이미지 복사후 image/upfile 밑에 있는 파일만 삭제
					$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

					@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/$popup_ix/".returnFileName($img));
					if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
						unlink($local_img_path);
					}

					$data_text = str_replace($img,$admin_config[mall_data_root]."/images/popup/$popup_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
				}else{//현재 서버에 있지 않은 파일일 경우 , image URL copy 를 한다. 해당 서버에서 허용 하는 경우에만
					if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/$popup_ix/".returnFileName($img))){ // 이미지 복사가 성공할 경우에만 image URL을 치환
						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/popup/$popup_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}
				}
			}


		}
	}

	$data_text = str_replace("http://$HTTP_HOST","",$data_text);
	$db->query("UPDATE ".TBL_SHOP_POPUP." SET popup_text = '$data_text' WHERE popup_ix='$popup_ix'");
	if($mmode == "pop"){
		echo("<script>parent.opener.document.location.reload();parent.self.close();</script>");
	}else{
		echo("<script>top.location.href = 'popup.list.php';</script>");
	}
}

if ($act == "delete")
{
	if(is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/$popup_ix/") && $popup_ix){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/popup/$popup_ix/");
	}

	$db->query("DELETE FROM ".TBL_SHOP_POPUP." WHERE popup_ix='$popup_ix'");
	//echo("<script>top.location.href = 'popup.list.php';</script>");
	echo("<script>history.back(-1);</script>");
	exit;
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



function GetDirContents($dir){
   ini_set("max_execution_time",10);
   if (!is_dir($dir)){die ("Fehler in Funktion GetDirContents: kein g?s Verzeichnis: $dir!");}
   if ($root=@opendir($dir)){
       while ($file=readdir($root)){
           if($file=="." || $file==".."){continue;}
           if(is_dir($dir."/".$file)){
               $files=array_merge($files,GetDirContents($dir."/".$file));
           }else{
           $files[]=$dir."/".$file;
           }
       }
   }
   return $files;
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
?>

