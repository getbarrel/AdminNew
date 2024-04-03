<?
include("../../class/database.class");
include("../lib/imageResize.lib.php");

session_start();

//Excel upload Memory Limit
set_time_limit(9999999999);
ini_set('memory_limit',-1);
//

if($admininfo[company_id] == ""){
	echo "<script language='javascript'>alert(language_data['common']['C'][language]);location.href='../'</script>";
	exit;	
}

$db = new Database;
$db2 = new Database;




if ($act == "excel_input"){
	require_once 'Excel/reader.php';
	
	if ($excel_file_size > 0){	
		copy($excel_file, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
	}
	
	// ExcelFile($filename, $encoding);
	$data = new Spreadsheet_Excel_Reader();
	
	
	// Set output Encoding.
	$data->setOutputEncoding('CP949');
	$data->read($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
	
	error_reporting(E_ALL ^ E_NOTICE);
	$shift_num = 0;
	
//	echo $data->sheets[0]['numRows'];
	//exit;
	for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {		
		//print_r($data->sheets[0]['cells'][$i]);
		//exit;
			$pcode = $data->sheets[0]['cells'][$i][1+$shift_num];
			$admin = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][2+$shift_num]);
			$pname = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][3+$shift_num]);
			$paper_name = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][4+$shift_num]);
			$brand_name = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][5+$shift_num]);
			$brand = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][6+$shift_num]);
			//$pimage = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][6+$shift_num]);
			$company = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][7+$shift_num]);
			$make_country = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][8+$shift_num]);
			$shotinfo = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][9+$shift_num]);
			$search_keyword = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][10+$shift_num]);
			$category_str = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][11+$shift_num]);
			$basicinfo = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][12+$shift_num]);			
			$delivery_company = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][13+$shift_num]);			
			$delivery_price1 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][14+$shift_num]);			
			$delivery_price2 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][15+$shift_num]);			
			$delivery_product_policy = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][16+$shift_num]);			
			$option_name1 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][17+$shift_num]);
			$option_item1 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][18+$shift_num]);
			$option_name2 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][19+$shift_num]);
			$option_item2 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][20+$shift_num]);
			$option_name3 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][21+$shift_num]);
			$option_item3 = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][22+$shift_num]);
			
			if($admininfo[mall_type] == "BW"){
				$coprice = $data->sheets[0]['cells'][$i][23+$shift_num];
				$wholesale_price = $data->sheets[0]['cells'][$i][24+$shift_num];		
				$listprice = $data->sheets[0]['cells'][$i][25+$shift_num];	
				$sellprice = $data->sheets[0]['cells'][$i][26+$shift_num];		
				
				$stock = $data->sheets[0]['cells'][$i][27+$shift_num];
				$safestock = $data->sheets[0]['cells'][$i][28+$shift_num];			
				$surtax_yorn = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][29+$shift_num]);	
				$reserve = $data->sheets[0]['cells'][$i][30+$shift_num]*$sellprice;
				$reserve_rate = $data->sheets[0]['cells'][$i][31+$shift_num];

			}else{
				$coprice = $data->sheets[0]['cells'][$i][23+$shift_num];
				$listprice = $data->sheets[0]['cells'][$i][24+$shift_num];			
				$sellprice = $data->sheets[0]['cells'][$i][25+$shift_num];		
				
				$stock = $data->sheets[0]['cells'][$i][26+$shift_num];
				$safestock = $data->sheets[0]['cells'][$i][27+$shift_num];			
				$surtax_yorn = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][28+$shift_num]);	
				$reserve = $data->sheets[0]['cells'][$i][29+$shift_num]*$sellprice;
				$reserve_rate = $data->sheets[0]['cells'][$i][30+$shift_num];
			}
			
			
			//$delivery_company = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][27+$shift_num]);
			//$gift = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][28+$shift_num]);
			//$main_material = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][29+$shift_num]);
			//$material_country = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][30+$shift_num]);
			
			//$period_of_circulation = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][30+$shift_num]);
			//$prod_branch = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][31+$shift_num]);
			//$piece = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][32+$shift_num]);
			//$max_buy_number = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][33+$shift_num]);			
			
			//$delivery_method = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][35+$shift_num]);
			//$pack_method = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][36+$shift_num]);

			$pcode =trim($pcode);
			$admin = trim($admin);
			$pname = trim($pname);
			$paper_name = trim($paper_name);
			$brand_name = trim($brand_name);
			$brand = trim($brand);
			//$pimage = iconv('CP949','UTF-8',$data->sheets[0]['cells'][$i][6+$shift_num]);
			$company = trim($company);
			$make_country = trim($make_country);
			$shotinfo = trim($shotinfo);
			$search_keyword = trim($search_keyword);
			$category_str = trim($category_str);
			$basicinfo = trim($basicinfo);			
			$delivery_company = trim($delivery_company);			
			$delivery_price1 = trim($delivery_price1);			
			$delivery_price2 = trim($delivery_price2);			
			$delivery_product_policy = trim($delivery_product_policy);			
			$option_name1 = trim($option_name1);
			$option_item1 = trim($option_item1);
			$option_name2 = trim($option_name2);
			$option_item2 = trim($option_item2);
			$option_name3 = trim($option_name3);
			$option_item3 = trim($option_item3);
			
			
			$coprice = trim($coprice);
			$wholesale_price = trim($wholesale_price);			
			$listprice = trim($listprice);		
			$sellprice = trim($sellprice);		
			
			$stock = trim($stock);
			$safestock = trim($safestock);			
			$surtax_yorn = trim($surtax_yorn);	
			$reserve = trim($reserve);
			$reserve_rate = trim($reserve_rate);
			
			
		if($pname){			
			/*
			if($surtax_yorn == "면세"){
				$surtax_yorn = "Y";
			}else if($surtax_yorn == "과세" || $surtax_yorn == "면세아님"){
				$surtax_yorn = "N";
			}else{
				$surtax_yorn = "";
			}
			
			
			
			if($delivery_company == "본사배송"){
				$delivery_company = "WE";
			}else if($delivery_company == "업체배송"){
				$delivery_company = "MI";
			}else{
				$delivery_company = "WE";
			}
			if($delivery_price1 || $delivery_price2){ 
				if($delivery_product_policy == "무료배송"){
					$delivery_product_policy = "3";
					$delivery_price = $delivery_price2;
				}else if($delivery_product_policy == "유료배송"){
					$delivery_product_policy = "1";
					$delivery_price = $delivery_price1;
				}
				$delivery_policy = "2";
			} else {
				$delivery_policy = "1";
			}
			
			
			if($delivery_method == "택배"){
				$delivery_method = "1";
			}else if($delivery_method == "소포/등기"){
				$delivery_method = "2";
			}else if($delivery_method == "퀵서비스"){
				$delivery_method = "3";
			}else if($delivery_method == "화물배달"){
				$delivery_method = "4";
			}else if($delivery_method == "일반우편"){
				$delivery_method = "5";
			}else if($delivery_method == "방문수령/직접배송"){
				$delivery_method = "6";
			}else{
				$delivery_method = "";
			}
			
			if($pack_method == "일반포장"){
				$pack_method = "A";
			}else if($pack_method == "냉장포장"){
				$pack_method = "B";
			}else if($pack_method == "냉동포장"){
				$pack_method = "C";			
			}else{
				$pack_method = "";
			}*/
			
			$db->query("update ".TBL_SHOP_PRODUCT." set vieworder = vieworder + 1  ");
			$db->fetch();
			//$vieworder = $db->dt[max_vieworder];
			$vieworder = 1;
			
			
			if($admininfo[admin_level] == 9){
				$state = "1";
				$disp = "0";
				if($admin != ""){
					$company_id = $admin;
				}else{
					$company_id = $admininfo[company_id];
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
			
			if($cid){
				$reg_category = "Y";
			}else{
				$reg_category = "N";
			}
			
			if(!$stock)  $stock = "9999";
			if(!$safestock)  $safestock = "5";

		if(trim($admin) != ""){
			
		}else{
			$admin = "3444fde7c7d641abc19d5a26f35a12cc";
		}

			$sql = "INSERT INTO ".TBL_SHOP_PRODUCT." 
					(id,  pname, paper_name, pcode, brand,brand_name, company, shotinfo,   listprice,sellprice,  coprice,wholesale_price,
					reserve_yn, reserve,reserve_rate,  basicinfo,   state, disp, movie, 
					vieworder, admin,stock,safestock,search_keyword,reg_category,
					surtax_yorn, delivery_policy, delivery_product_policy, delivery_price,
					one_commission,commission,etc2, regdate)
					values('', '".strip_tags($pname)."','".strip_tags($paper_name)."', '$pcode', '$brand','$brand_name','$company', '$shotinfo', '$listprice','$sellprice', '$coprice','$wholesale_price','$reserve_yn', '$reserve','$rate1',  '$basicinfo', 
					  $state, 0, '$movie', '$vieworder', '$admin',
					'$stock','$safestock','$search_keyword','$reg_category',
					'$surtax_yorn', '$delivery_policy', '$delivery_product_policy', '$delivery_price',
					'$one_commission','$commission','$etc2',NOW()) ";
	
			
			$db->query($sql);
		
			
			$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT." WHERE id=LAST_INSERT_ID()");
			$db->fetch();
			$pid = $db->dt[id];
			
			if($pimage != ""){
				ExcelImageCopy($pimage, $pid);
			}
            //상품 상세이미지 복사 안하도록 주석처리(문대리님 요청) 12.07.03 bgh 
			//$basicinfo = basicinfoCopy($basicinfo, $pid);

			$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET basicinfo = '$basicinfo' WHERE id='$pid'");
				
			if($option_name1 != ""){		
				$db->query("insert into ".TBL_SHOP_PRODUCT_OPTIONS."(opn_ix,pid,option_name,option_kind,option_use,regdate) values('','$pid','$option_name1','s','1',NOW())	  ");
				$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
				$db->fetch();
				$opn_ix = $db->dt[opn_ix];
				
				$option_item1 = str_replace("'","",$option_item1);
				$option_item1s = explode(",",$option_item1);
			
				for($j=0;$j<count($option_item1s);$j++){
					$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_etc1) ";
					$sql = $sql." values ('','$pid','$opn_ix','".trim($option_item1s[$j])."','','') ";
						
					//echo $sql;
					$db->query($sql);
				}
			}
			
			if($option_name2 != ""){		
				$db->query("insert into ".TBL_SHOP_PRODUCT_OPTIONS."(opn_ix,pid,option_name,option_kind,option_use,regdate) values('','$pid','$option_name2','1','1',NOW())	  ");
				$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
				$db->fetch();
				$opn_ix = $db->dt[opn_ix];
				
				$$option_item2 = str_replace("'","",$option_item2);
				$option_item2s = explode(",",$option_item2);
			
				for($j=0;$j<count($option_item2s);$j++){
					$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_etc1) ";
					$sql = $sql." values ('','$pid','$opn_ix','".trim($option_item2s[$j])."','','') ";
						
					//echo $sql;
					$db->query($sql);
				}
			}
			
			if($option_name3 != ""){		
				$db->query("insert into ".TBL_SHOP_PRODUCT_OPTIONS."(opn_ix,pid,option_name,option_kind,option_use,regdate) values('','$pid','$option_name3','1','1',NOW())	  ");
				$db->query("SELECT opn_ix FROM ".TBL_SHOP_PRODUCT_OPTIONS." WHERE opn_ix=LAST_INSERT_ID()");
				$db->fetch();
				$opn_ix = $db->dt[opn_ix];
				
				$$option_item3 = str_replace("'","",$option_item3);
				$option_item3s = explode(",",$option_item3);
			
				for($j=0;$j<count($option_item3s);$j++){
					$sql = "INSERT INTO ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." (id, pid, opn_ix, option_div,option_price,option_etc1) ";
					$sql = $sql." values ('','$pid','$opn_ix','".trim($option_item3s[$j])."','','') ";
						
					//echo $sql;
					$db->query($sql);
				}
			}
			
			$category = split(">",$category_str);
			if($category[0]){
				$sql = "SELECT cname, cid FROM ".TBL_SHOP_CATEGORY_INFO." WHERE cname = '".trim($category[0])."' ";
				$db->query($sql);
				$db->fetch();

				$cid = $db->dt[cid];
				$parent_cid = $db->dt[cid];
				$reg_category = "Y";
				if($category[1]){
					$sql = "SELECT cname, cid FROM ".TBL_SHOP_CATEGORY_INFO." WHERE cname = '".trim($category[1])."' and cid LIKE '".substr($parent_cid,0,3)."%' ";
					//echo $sql;
					$db->query($sql);					
					$db->fetch();
					$cid = $db->dt[cid];
					$parent_cid = $db->dt[cid];
					
					if($category[2]){
						$sql = "SELECT cname, cid FROM ".TBL_SHOP_CATEGORY_INFO." WHERE cname = '".trim($category[2])."' and cid LIKE '".substr($parent_cid,0,6)."%' ";
						//echo $sql;
						$db->query($sql);
						$db->fetch();
						$cid = $db->dt[cid];
					}
				}
			}else{
				$cid = $_POST["cid"];
			}

			if($cid != ""){
				$db->query("insert into ".TBL_SHOP_PRODUCT_RELATION." (rid, cid, pid ,disp ,basic, insert_yn, regdate) values ('','$cid', '$pid','1','1','Y',NOW());");
			}
			
		}
			
		}		
		
	
	
	
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name)){
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/".$excel_file_name);
	}
	
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert(language_data['product_input_excel.act_ing.php']['A'][language]);document.location.href='./product_input_excel.php?cid=$cid&depth=$depth&view=innerview'</script>";

}



if ($act == "delete" || $act == "delete_excel"){
	
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
	
	
	
	
	if(is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id") && $id){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id");
	}
	
	
	
	//echo "<script language='javascript'>document.location.href='product_input_excel.php?view=innerview&cid=$cid&depth=$depth';</script>";
	echo "<script language='javascript'>if(parent.document.frames['act'].location == 'about:blank'){parent.document.location.reload();}else{parent.document.frames['act'].location.reload();}</script>";

	//header("Location:../product_list.php");
}


if ($act == "select_delete"){
	
	for($i=0;$i<count($cpid);$i++){

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$cpid[$i].".gif");			
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$cpid[$i].".gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$cpid[$i].".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$cpid[$i].".gif");
		}
	
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='".$cpid[$i]."'");
		$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='".$cpid[$i]."'");		
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='".$cpid[$i]."'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='".$cpid[$i]."'");
	
	
		
		if(is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$cpid[$i]) && $cpid[$i]){
			rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/".$cpid[$i]);
		}
	
	}
	
	echo "<script language='javascript'>parent.document.location.reload();</script>";
	//echo "<script language='javascript'>document.location.href='product_input_excel.php?view=innerview&cid=$cid&depth=$depth';</script>";
	//echo "<script language='javascript'>if(parent.document.frames['act'].location == 'about:blank'){alert('1');parent.document.location.reload();}else{alert('3');parent.document.frames['act'].location.reload();}</script>";
	//header("Location:../product_list.php");
}

function basicinfoCopy($basicinfo, $INSERT_PRODUCT_ID){
	global $admin_config, $DOCUMENT_ROOT, $HTTP_HOST;
	$data_text_convert = $basicinfo;
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

	//print_r($out);
	
	for($i=0;$i < count($out);$i++){
		for($j=0;$j < count($out[$i]);$j++){
			
			$img = returnImagePath($out[$i][$j]);
			$img = ClearText($img);
			try{
				//echo $img;
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
								//echo $img;
								if(@copy($DOCUMENT_ROOT.$img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($DOCUMENT_ROOT.$img))){
									$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환	
								}
							}else{
								
								if(@copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img))){							
									$basicinfo = str_replace($img,$admin_config[mall_data_root]."/images/product_detail/$INSERT_PRODUCT_ID/".returnFileName($img),$basicinfo);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환								
								}
								//echo $basicinfo;
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
	
	return $basicinfo;
}

function ExcelImageCopy($image_path, $pid){
	global $admin_config, $DOCUMENT_ROOT;
	$image_info = getimagesize ($image_path);	
	$image_type = substr($image_info['mime'],-3);
	
		if($image_type == "gif"){
			copy($image_path, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif");
			
			//MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$pid.".gif", MIRROR_NONE);
			copy($image_path, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$pid.".gif");
			//resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$pid.".gif",500,500);
						
			MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$pid.".gif", MIRROR_NONE);
			resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$pid.".gif",300,300);
			
			MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$pid.".gif", MIRROR_NONE);
			resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$pid.".gif",137,137);
			
			MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$pid.".gif", MIRROR_NONE);
			resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$pid.".gif",90,90);
			
			MirrorGif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$pid.".gif", MIRROR_NONE);
			resize_gif($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$pid.".gif",50,50);
			
		}else{
			copy($image_path, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif");
			
			//Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$pid.".gif", MIRROR_NONE);
			copy($image_path, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$pid.".gif");
			//resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/b_".$pid.".gif",500,500);
			
			
			Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$pid.".gif", MIRROR_NONE);
			resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/m_".$pid.".gif",300,300);
						
			Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$pid.".gif", MIRROR_NONE);
			resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/ms_".$pid.".gif",137,137);
			
			Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$pid.".gif", MIRROR_NONE);
			resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/s_".$pid.".gif",90,90);
			
			
			Mirror($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/basic_".$pid.".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$pid.".gif", MIRROR_NONE);
			resize_jpg($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$pid.".gif",50,50);
			
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