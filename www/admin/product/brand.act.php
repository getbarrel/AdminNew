<?php
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	include('../../include/xmlWriter.php');
	$db = new Database;
	$db2 = new Database;

	session_start();

	/*
	if ($disp != 1){
		$disp = 0;shop_brand
	}
	*/
	//print_r($_POST);
	//exit;

	if($act == "checkBrandCode"){
		if($brand_code == ""){
			$result[bool] = false;
			$result[message] = "브랜드 코드를 입력해주세요 ";
			echo json_encode($result);
			exit;
		}

		$sql = "select * from shop_brand where brand_code = '".$brand_code."' ";
		$db->query($sql);

		if($db->total){
			$result[bool] = false;
			$result[message] = "'".$brand_code."'는 이미 사용중인  코드입니다.";
		}else{
			$result[bool] = true;
			$result[message] = "사용하실수 있는 코드입니다.";
		}

		echo json_encode($result);
		exit;
	}

	if($update_kind == "category"){	//분류카테고리 일괄변경

		if($update_type == "2"){//선택한 회원
			if($cid2){
				if($update_category_type == "add"){	//카테고리추가
					if(count($cpid) > 0){
			
						for($i=0;$i<count($cpid);$i++){
							$sql = "insert into shop_brand_relation set 
										cid = '".$cid2."',
										b_ix = '".$cpid[$i]."',
										disp='1',
										basic='0',
										insert_yn = 'Y',
										regdate = NOW();";
							$db->query($sql);
						}
					}
				}else if($update_category_type == "basic_add"){	//기본카테고리 변경
					if(count($cpid) > 0){
						for($i=0;$i<count($cpid);$i++){
							$sql = "select * from shop_brand_relation where b_ix = '".$cpid[$i]."' and cid='".$cid2."'";
							$db->query($sql);
							$db->fetch();
							$brid = $db->dt[brid];

							$sql = "update shop_brand_relation set basic = '0' where b_ix = '".$cpid[$i]."'";
							$db2->query($sql);

							if($db->total > 0){	//요청한 카테고리가 이미 있을경우 기존에 카테고리를 전부 0으로 수정한후 해당 카테고리만 1로 수정
								$sql = "update shop_brand_relation set basic='1' where b_ix = '".$cpid[$i]."' and cid = '".$cid2."'";
								$db2->query($sql);
							}else{
								$sql = "insert into shop_brand_relation set 
										cid = '".$cid2."',
										b_ix = '".$cpid[$i]."',
										disp='1',
										basic='1',
										insert_yn = 'Y',
										regdate = NOW();";
								$db2->query($sql);
							}
						
						}
					}
				}
			}else{
				if($update_category_type == "basic_del"){	//기본카테고리외 삭제
					if(count($cpid) > 0){
						for($i=0;$i<count($cpid);$i++){
							$sql = "delete from shop_brand_relation where b_ix = '".$cpid[$i]."' and basic ='0'";
							$db->query($sql);
						}
					}
				}
			}
			echo "
			<Script Language='Javascript'>
			parent.document.location.reload();
			</Script>";
		}
	}

	if($update_kind == "bd_category"){	//브랜드분류 변경
		if($update_type == "2"){//선택한 회원
			if($bd_ix2){
				if(count($cpid) > 0){
					for($i=0;$i<count($cpid);$i++){
						$sql = "update shop_brand set bd_ix = '".$bd_ix2."' where b_ix = '".$cpid[$i]."'";
						$db->query($sql);
					}	
				}
			echo "
			<Script Language='Javascript'>
			parent.document.location.reload();
			</Script>";
			}
		}
	}


	if ($mode == "insert")
	{
		
		if($_SESSION["admininfo"]["admin_level"] ==9){
			$apply_status = "1";
		}else{
			$apply_status = "2";
		}

		if($bd_ix=="") {//하위 카테고리 값이 없다면 상위 값을 입력 kbk 13/07/01
			$bd_ix=$parent_bd_ix;
		}

		if(count($global_binfo) > 0){
			foreach($global_binfo as $colum => $li){
				foreach($li as $ln => $val){
					$global_binfo[$colum][$ln] = urlencode($val);
				}
			}
		}

		$global_binfo = json_encode($global_binfo);

		$sql = "INSERT INTO shop_brand 
					(b_ix, cid, bd_ix,brand_code, brand_name,brand_name_division, global_binfo, disp, search_disp, top_design, company_id,shotinfo,apply_status, brand_html,vieworder, regdate) 
					values
					('', '$cid', '$bd_ix', '$brand_code', '$brand','$brand_name_division', '$global_binfo', '$disp', '$search_disp','$top_design','".$admininfo[company_id]."','$shotinfo','$apply_status','$brand_html','$vieworder',now()) ";//$bd_ix 추가 kbk 13/07/01

		$db->sequences = "SHOP_BRAND_SEQ";
		$db->query($sql);

		if($db->dbms_type=='oracle'){
			$b_ix = $db->last_insert_id;
		}else{
			$db->query("SELECT b_ix FROM shop_brand WHERE b_ix=LAST_INSERT_ID()");
			$db->fetch();
			$b_ix = $db->dt[0];
		}

		for($i=0;$i<count($category);$i++){
			if($category[$i] == $basic){
				$category_basic = 1;
			}else{
				$category_basic = 0;
			}

			$db->sequences = "SHOP_GOODS_LINK_SEQ";
			$db->query("insert into shop_brand_relation (brid, cid, b_ix, disp, basic,insert_yn, regdate ) values ('','".$category[$i]."','".$b_ix."','1','".$category_basic."','Y',NOW())");

		}

		
		/*
		if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/")){
			mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/");
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/",0777);
		}
		*/
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/";

		if(substr_count($data_text,"<IMG") > 0){
			if(!is_dir($path)){
				mkdir($path, 0777);
				//chmod($path,0777)
			}
		}

		if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/")){//폴더가 생성되지 않아서 수정 2012-05-25 홍진영
			mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/");
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/",0777);
		}

		if ($_FILES["brandimg"]["size"] >0)
		{
			copy($_FILES["brandimg"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$db->dt[0].".gif");
		}

		if ($_FILES["brandbgimg"]["size"] >0)
		{
			copy($_FILES["brandbgimg"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brandbg_".$db->dt[0].".gif");
		}

		if ($_FILES["brand_banner_img"]["size"] >0)
		{
			copy($_FILES["brand_banner_img"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/b_brand_".$db->dt[0].".gif");
		}

		$pcImageCount = count($_FILES['bannerPCImage']['size']);

		for($i = 0; $i < $pcImageCount; $i++){
			if($_FILES['bannerPCImage']['size'][$i] > 0){
				move_uploaded_file($_FILES['bannerPCImage']['tmp_name'][$i], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_banner_" . $i . "_" . $b_ix . ".gif");

				$db = new Database;
				$sql = sprintf("INSERT INTO shop_brand_promotion_banner
				                 (b_ix, file_name, link, display_order)
								     VALUES 
								 (%d, '%s', '%s', %d)"
								 , $b_ix
							     , "brand_banner_" . $i . "_" . $b_ix . ".gif"
								 , $linkPC[$i]
							     , $displayOrderPC[$i]);
				$db->query($sql);
			}
		}

		$mImageCount = count($_FILES['bannerMImage']['size']);

		for($i = 0; $i < $mImageCount; $i++){
			if($_FILES['bannerMImage']['size'][$i] > 0){
				move_uploaded_file($_FILES['bannerMImage']['tmp_name'][$i], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/m_brand_banner_" . $i . "_" . $b_ix . ".gif");

				$db = new Database;
				$sql = sprintf("INSERT INTO shop_brand_promotion_banner
				                 (b_ix, file_name, link, display_order, type)
								     VALUES 
								 (%d, '%s', '%s', %d, 'M')"
								 , $b_ix
							     , "m_brand_banner_" . $i . "_" . $b_ix . ".gif"
								 , $linkM[$i]
							     , $displayOrderM[$i]);
				$db->query($sql);
			}
		}

		/*
		if ($brandimg_on != "none")
		{
			copy($brandimg_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$db->dt[0]."_on.gif");
		}*/

		$data_text = $top_design;
		$data_text_convert = $top_design;
		$data_text_convert = str_replace("\\","",$data_text_convert);
		preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);


		

	//print_r ($out);
	//exit;
		for($i=0;$i < count($out);$i++){
			for($j=0;$j < count($out[$i]);$j++){

				$img = returnImagePath($out[$i][$j]);
				$img = ClearText($img);


				if(substr_count($img,$admin_config[mall_data_root]."/images/brand/$b_ix/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
					if(substr_count($img,"$HTTP_HOST")>0){
						$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

						@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img));
						if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
							unlink($local_img_path);
						}

						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}else{
						if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img))){
							$data_text = str_replace($img,$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
						}
					}
				}



			}
		}


		$db->query("UPDATE shop_brand SET top_design = '$data_text' WHERE b_ix='$b_ix'");
		updateBrandsXML();

		if($mmode == "pop"){
		echo "
			<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
			<html xmlns='http://www.w3.org/1999/xhtml'>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
			<meta http-equiv='cache-control' content='no-cache'>
			<meta http-equiv='pragma' content='no-cache'>

			<body>
			<div id='brand_select_area'>
			".BrandListSelect($b_ix, $cid)."
			</div>
			</body>
			</html>
			<Script Language='Javascript'>
			parent.opener.document.getElementById('brand_select_area').innerHTML = document.getElementById('brand_select_area').innerHTML;
			parent.document.location.reload();
			</Script>";
		}else{
			echo "
			<Script Language='Javascript'>
			parent.document.location.reload();
			</Script>";
			//header("Location:brand.php?mmode=$mmode");
		}
	}

	if ($mode == "change")
	{
	//	echo("SELECT b_ix FROM shop_brand WHERE b_ix=$b_ix");
		$db->query("SELECT * FROM shop_brand WHERE b_ix=$b_ix");
		$db->fetch();

		$disp = $db->dt[disp];
		if ($db->dt[disp] == 1){
			$checkString = "true";
		}else{
			$checkString = "false";
		}

		if ($db->dt[search_disp] == 1){
			$SearchCheckString = "true";
		}else{
			$SearchCheckString = "false";
		}


		echo "
			<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
			<html xmlns='http://www.w3.org/1999/xhtml'>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
			<meta http-equiv='cache-control' content='no-cache'>
			<meta http-equiv='pragma' content='no-cache'>
			<body>
			<div id='top_design'>
			".$db->dt[top_design]."
			</div>
			<div id='shotinfo'>".$db->dt[shotinfo]."</div>
			</body>
			</html>
			<Script Language='Javascript'>
			parent.document.forms['brandform'].brand.value = \"".$db->dt[brand_name]."\";
			parent.document.forms['brandform'].b_ix.value = '".$db->dt[b_ix]."';
			parent.document.forms['brandform'].disp[$disp].checked = true;
			parent.document.forms['brandform'].search_disp.checked = $SearchCheckString;
			parent.document.forms['brandform'].shotinfo.innerHTML = document.getElementById('shotinfo').innerHTML;
			parent.document.getElementById('modify').style.display = 'block';
			parent.document.getElementById('delete').style.display = 'block';
			parent.document.getElementById('ok').style.display = 'none';";
		if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "O"){// 입점형
		echo "
			var obj = parent.document.forms['brandform'].cid;
			for(i=0;i<obj.length;i++){
				if(obj[i].value == '".$db->dt[cid]."'){
					obj[i].selected = true;
				}
			}";
		}
		echo "
			parent.document.getElementById('iView').contentWindow.document.body.innerHTML = document.getElementById('top_design').innerHTML;
			";

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$b_ix.".gif")){
				echo "parent.document.getElementById('brandimgarea').innerHTML = \"<img src='".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$db->dt[b_ix].".gif'>\";";
			}else{
				echo "parent.document.getElementById('brandimgarea').innerHTML = \"브랜드 이미지가 입력되지 않았습니다. \";";
			}

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/b_brand_".$b_ix.".gif")){
				echo "parent.document.getElementById('brand_banner_imgarea').innerHTML = \"<img src='".$admin_config[mall_data_root]."/images/brand/$b_ix/b_brand_".$db->dt[b_ix].".gif'>\";";
			}else{
				echo "parent.document.getElementById('brand_banner_imgarea').innerHTML = \"브랜드 이미지가 입력되지 않았습니다. \";";
			}
			/*
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$db->dt[0]."_on.gif")){
				echo "parent.document.getElementById('brandimgarea_on').innerHTML = \"<img src='".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$db->dt[0]."_on.gif'>\";";
			}else{
				echo "parent.document.getElementById('brandimgarea_on').innerHTML = \"브랜드 이미지(마우스on)가 입력되지 않았습니다. \";";
			}
			*/
		$global_binfo = json_decode($db->dt[global_binfo],true);
		$globalInfo = getGlobalInfo();

		if(count($global_binfo) > 0 && $globalInfo['global_use']=='Y'){
			foreach($global_binfo as $colum => $li){
				foreach($li as $ln => $val){
					if($colum == 'brand_name'){
						$_global_brand_name = urldecode($val);
						echo "parent.document.getElementById('global_brand_name_".$ln."').value = '".$_global_brand_name."';";
					}
				}
			}
		}
		echo "</Script>";

	}



	if ($mode == "update")
	{
		$b_ix = $_POST['b_ix'];
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/";

		if(substr_count($data_text,"<IMG") > 0){
			if(!is_dir($path)){
				mkdir($path, 0777);
				//chmod($path,0777)
			}
		}

		if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/")){
			mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/");
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/",0777);
		}

		if ($brandimg_size > 0 )
		{
			copy($brandimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$b_ix.".gif");
		}

		if ($brandbgimg_size > 0 )
		{
			copy($brandbgimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brandbg_".$b_ix.".gif");
		}

		if ($brand_banner_img_size > 0)
		{
			copy($brand_banner_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/b_brand_".$b_ix.".gif");
		}


	/*
		if ($brandimg_on != "none")
		{
			copy($brandimg_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$b_ix."_on.gif");
		}
	*/
		if($bd_ix=="") {//하위 카테고리 값이 없다면 상위 값을 입력 kbk 13/07/01
			$bd_ix=$parent_bd_ix;
		}
		
		//브랜드순서(vieworder) 정렬을 위해서
		/* 중간에 비어있으면 계속 +1 되는 현상이있어서 주석.
		$sql = "SELECT vieworder FROM shop_brand WHERE b_ix='$b_ix'";
		$db->query($sql);
		$db->fetch();

		//기존 정보랑 같으면 작업할 필요가 없다.
		if($db->dt[vieworder] != $vieworder)
		{
			if($db->dt[vieworder] > $vieworder)
			{
				$sql = "UPDATE shop_brand set vieworder = vieworder + 1 WHERE vieworder BETWEEN " . $vieworder . " AND " . ($db->dt[vieworder]-1) . "";
			}
			else
			{
				$sql = "UPDATE shop_brand set vieworder = vieworder + 1 WHERE vieworder >= " . $vieworder . "";
			}
			$db->query($sql);
		}
		*/
		if(count($global_binfo) > 0){
			foreach($global_binfo as $colum => $li){
				foreach($li as $ln => $val){
					$global_binfo[$colum][$ln] = urlencode($val);
				}
			}
		}

		$global_binfo = json_encode($global_binfo);

		$sql = "UPDATE shop_brand SET
				cid = '$cid',
				bd_ix = '$bd_ix', 
				brand_name = '$brand', 
				brand_code = '$brand_code', 
				brand_name_division = '$brand_name_division', 
				global_binfo = '$global_binfo',
				disp = '$disp', 					
				search_disp = '$search_disp', 
				shotinfo ='$shotinfo' ,
				top_design ='$top_design',
				apply_status = '$apply_status', 	
				brand_html = '$brand_html',
				vieworder = '$vieworder'
				WHERE b_ix='$b_ix'";
		//echo $sql;
		//exit;
		$db->query($sql);

		$data_text = $top_design;
		$data_text_convert = $top_design;
		$data_text_convert = str_replace("\\","",$data_text_convert);
		preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);


		

	//print_r ($out);
	//exit;
		for($i=0;$i < count($out);$i++){
			for($j=0;$j < count($out[$i]);$j++){

				$img = returnImagePath($out[$i][$j]);
				$img = ClearText($img);


				if(substr_count($img,$admin_config[mall_data_root]."/images/brand/$b_ix/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
					if(substr_count($img,"$HTTP_HOST")>0){
						$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

						@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img));
						if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
							unlink($local_img_path);
						}

						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}else{
						if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img))){
							$data_text = str_replace($img,$admin_config[mall_data_root]."/images/brand/$b_ix/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
						}
					}
				}
			}
		}


		$db->query("UPDATE shop_brand SET top_design = '$data_text' WHERE b_ix='$b_ix'");

		$db->query("update shop_brand_relation set insert_yn = 'N' where b_ix = '$b_ix'");
		for($i=0;$i<count($category);$i++){
			if($category[$i] == $basic){
				$category_basic = 1;
			}else{
				$category_basic = 0;
			}
			$sql = "select brid from shop_brand_relation where b_ix = '$b_ix' and cid = '".$category[$i]."' ";
			$db->query($sql);
			$db->fetch();
			if($db->total){
				$db->query("update shop_brand_relation set insert_yn = 'Y' , basic='$category_basic' where brid = '".$db->dt[brid]."'");
			}else{
				$db->sequences = "SHOP_GOODS_LINK_SEQ";
				$db->query("insert into shop_brand_relation (brid, cid, b_ix, disp, basic,insert_yn, regdate ) values ('','".$category[$i]."','".$b_ix."','1','".$category_basic."','Y',NOW())");
			}
		}
		$db->query("delete from shop_brand_relation where b_ix = '$b_ix' and insert_yn = 'N'");

		//echo("<script>top.location.href = 'brand.php?b_ix=$b_ix';</script>");
		updateBrandsXML();

		if(is_array($modiBannerPC)){
			for($m=0; $m < count($modiBannerPC); $m++){
				if(! empty($modiBannerPC[$m])){
					$sql = "select * from shop_brand_promotion_banner where bpb_ix = '".$modiBannerPC[$m]."'";
					$db->query($sql);

					if($db->total > 0){
						$sql = "update shop_brand_promotion_banner set link='".$linkPC[$m]."', display_order='".$displayOrderPC[$m]."' where bpb_ix = '".$modiBannerPC[$m]."'";
						$db->query($sql);
					}
				}
			}
		}

		if(is_array($modiBannerM)){
			for($m2=0; $m2 < count($modiBannerM); $m2++){
				if(! empty($modiBannerM[$m2])){
					$sql = "select * from shop_brand_promotion_banner where bpb_ix = '".$modiBannerM[$m2]."'";
					$db->query($sql);

					if($db->total > 0){
						$sql = "update shop_brand_promotion_banner set link='".$linkM[$m2]."', display_order='".$displayOrderM[$m2]."' where bpb_ix = '".$modiBannerM[$m2]."'";
						$db->query($sql);
					}
				}
			}
		}

//		$sql = sprintf("SELECT COUNT(*) FROM shop_brand_promotion_banner WHERE b_ix = %d", $b_ix);
		$pcImageCount = count($_FILES['bannerPCImage']['size']);

		for($i = 0; $i < $pcImageCount; $i++){
			if($_FILES['bannerPCImage']['size'][$i] > 0){
				move_uploaded_file($_FILES['bannerPCImage']['tmp_name'][$i], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_banner_" . $i . "_" . $b_ix . ".gif");

				$db = new Database;
				$sql = sprintf("INSERT INTO shop_brand_promotion_banner
				                 (b_ix, file_name, link, display_order)
								     VALUES 
								 (%d, '%s', '%s', %d)"
								 , $b_ix
							     , "brand_banner_" . $i . "_" . $b_ix . ".gif"
								 , $linkPC[$i]
							     , $displayOrderPC[$i]);
				$db->query($sql);
			}
		}

		$mImageCount = count($_FILES['bannerMImage']['size']);

		for($i = 0; $i < $mImageCount; $i++){
			if($_FILES['bannerMImage']['size'][$i] > 0){
				move_uploaded_file($_FILES['bannerMImage']['tmp_name'][$i], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/m_brand_banner_" . $i . "_" . $b_ix . ".gif");

				$db = new Database;
				$sql = sprintf("INSERT INTO shop_brand_promotion_banner
				                 (b_ix, file_name, link, display_order, type)
								     VALUES 
								 (%d, '%s', '%s', %d, 'M')"
								 , $b_ix
							     , "m_brand_banner_" . $i . "_" . $b_ix . ".gif"
								 , $linkM[$i]
							     , $displayOrderM[$i]);
				$db->query($sql);
			}
		}

		if($mmode == "pop"){
			echo "
				<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
				<html xmlns='http://www.w3.org/1999/xhtml'>
				<head>
				<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
				<meta http-equiv='cache-control' content='no-cache'>
				<meta http-equiv='pragma' content='no-cache'>
				<body>
				<div id='brand_select_area'>
				".BrandListSelect($b_ix, $cid)."
				</div>
				</body>
				</html>
				<Script Language='Javascript'>
				parent.opener.document.getElementById('brand_select_area').innerHTML = document.getElementById('brand_select_area').innerHTML;
				parent.document.location.reload();
				</Script>";
		}else{
			echo "
			<script language='javascript' src='../js/message.js.php'></script><Script Language='Javascript'>
			show_alert('정상적으로 수정되었습니다.');
			parent.document.location.reload();
			</Script>";
			//header("Location:brand.php?mmode=$mmode");
		}


		// 삭제후 입력
		$sql = sprintf("DELETE FROM shop_display_brand_product WHERE b_ix = %d", $b_ix);
		$db->query($sql);

		if($rpid[1] != ""){
			foreach($rpid[1] as $pid){
				$sql  =       "INSERT INTO shop_display_brand_product ";
				$sql .=       " (b_ix, pid) ";
				$sql .=       "     VALUES  ";
				$sql .= sprintf(" (%d, %d) ", $b_ix, $pid);
				$db->query($sql);
			}
		}
	}



	if ($mode == "image_delete")
	{
		if ($imagetype == "brandimg"){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$b_ix.".gif");
		}

		if ($imagetype == "brand_banner_img"){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/b_brand_".$b_ix.".gif");
		}

		echo "
			<script language='javascript' src='../js/message.js.php'></script><Script Language='Javascript'>
			show_alert('이미지가 정상적으로 삭제되었습니다.');
			parent.document.location.reload();
			</Script>";
	}
	if ($mode == "delete")
	{
		/*
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/brand_$b_ix.gif"))
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/brand_$b_ix.gif");
		}
		*/

		if ($b_ix != "" && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/"))
		{
			rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/");
		}

		$db->query("DELETE FROM shop_brand WHERE b_ix='$b_ix'");
		updateBrandsXML();

		if($mmode == "pop"){
		echo "
			<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
			<html xmlns='http://www.w3.org/1999/xhtml'>
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
			<meta http-equiv='cache-control' content='no-cache'>
			<meta http-equiv='pragma' content='no-cache'>
			<body>
			<div id='brand_select_area'>
			".BrandListSelect($b_ix, $cid)."
			</div>
			</body>
			</html>
			<Script Language='Javascript'>
			parent.opener.document.getElementById('brand_select_area').innerHTML = document.getElementById('brand_select_area').innerHTML;
			parent.document.location.reload();
			</Script>";
		}else{
			echo "
			<Script Language='Javascript'>
			parent.document.location.reload();
			</Script>";
		}
	}

	if($mode == "select_depth2"){
		if($bd_ix){
			$sql = "select * from shop_brand_div where bd_ix = '".$bd_ix."' ";
			$db2->query($sql);
			$db2->fetch();
			$div1_name =  $db2->dt[div_name];

			$sql = "select * from shop_brand_div where parent_bd_ix='".$bd_ix."'";
			$db->query($sql);
			$data_array = $db->fetchall();

			for($i=0;$i<count($data_array);$i++){
				

				$brand_info[$data_array[$i][bd_ix]] = $div1_name." > ".$data_array[$i][div_name];
			}

			$datas = $brand_info;
			$datas = json_encode($datas);
			$datas = str_replace("\"true\"","true",$datas);
			$datas = str_replace("\"false\"","false",$datas);
			echo $datas;

		}

	}

	function BrandListSelect($brand, $cid, $return_type ="")
	{
	//global $db;

		$mdb = new Database;


		if($cid){
			$mdb->query("SELECT * FROM ".TBL_SHOP_BRAND." where disp=1 and cid = '$cid'");
		}else{
			$mdb->query("SELECT * FROM ".TBL_SHOP_BRAND." where disp=1");
		}

		$bl = "<Select name='brand' class=small>";
		if ($mdb->total == 0)	{
			$bl = $bl."<Option>등록된 브랜드가 없습니다.</Option>";
		}else{
			if($return_type == ""){
				$bl = $bl."<Option value=''>브랜드 선택</Option>";
				for($i=0 ; $i <$mdb->total ; $i++)
				{
					$mdb->fetch($i);
					if ($brand == $mdb->dt[b_ix])
					{
						$strSelected = "Selected";
					}else{
						$strSelected = "";
					}

					$bl = $bl."<Option value='".$mdb->dt[b_ix]."' $strSelected>".$mdb->dt[brand_name]."</Option>";

				}
			}else{
				for($i=0 ; $i <$mdb->total ; $i++)
				{
					$mdb->fetch($i);
					if ($brand == $mdb->dt[b_ix]){
						return $mdb->dt[brand_name];
					}
				}
			}
		}

		$bl = $bl."</Select>";

		return $bl;
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

	function ClearText($str){
		return str_replace(">","",$str);
	}


	function returnFileName($filestr){
		$strfile = split("/",$filestr);

		return str_replace("%20","",$strfile[count($strfile)-1]);
		//return count($strfile);

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


	function updateBrandsXML(){

		global $DOCUMENT_ROOT, $admin_config;

		$xml = new XmlWriter_();
		$mdb = new Database;

		$mdb->query("select * from ".TBL_SHOP_BRAND." where disp=1 ");
		$brands = $mdb->fetchall();

		$xml->push('brands');


		foreach ($brands as $brand) {
			//$xml->push('shop', array('species' => $animal[0]));
			$xml->push('brand', array('cid' => $brand[cid], 'top_cid' => substr($brand[cid],0,3)));
			$xml->element('top_cid', substr($brand[cid],0,3));
			$xml->element('b_ix', $brand[b_ix]);
			$xml->element('brand_name', $brand[brand_name]);
			$xml->element('brand_link', "/event/goods_brand.php?b_ix=".$brand[b_ix]."&cid=".$brand[cid]);
			$xml->pop();
		}

		$xml->pop();
		//print $xml->getXml();

		$dirname = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];

		$fp = fopen($dirname."/brands.xml","w");
		fputs($fp, $xml->getXml());
		fclose($fp);
	}
