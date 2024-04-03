<?
include("../../class/database.class");

session_start();

$db = new Database;
if ($act == "insert")
{

	$sql = "INSERT INTO ".TBL_SHOP_SHOPINFO." ";
	$sql = $sql."(mall_domain_id,mall_domain,mall_domain_key)";
	$sql = $sql." VALUES('$mall_domain_id','$mall_domain','$mall_domain_key')";
	$db->query($sql);

	if($barobill_key){	//바로빌 정보 입력 2013-05-03 이학봉
		$db->query("insert into  shop_payment_config (mall_ix, pg_code, config_name, config_value) values('$mall_ix', 'barobill', 'barobill_key', '$barobill_key')");

	}else if($barobill_id){
			$db->query("insert into  shop_payment_config (mall_ix, pg_code, config_name, config_value) values('$mall_ix', 'barobill', 'barobill_id', '$barobill_id')");
	}else if($barobill_pw){
		$db->query("insert into  shop_payment_config (mall_ix, pg_code, config_name, config_value) values('$mall_ix', 'barobill', 'barobill_pw', '$barobill_pw')");
	}

	echo("<script>alert('정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='mallinfo.php';</script>");
}
/**
 *  로고 삭제하기 12.05.22 bgh 
 * 
 *  # 모바일로고는 기존 삭제방식 수정안함.
 */
if($del != "" || $del !=NULL){
	if($del == "admin_logo"){
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/admin_logo.gif");
	}

	if($del == "shop_logo"){
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/shop_logo.gif");
	}

	if($del == "shop_logo_over"){
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/shop_logo_over.gif");
	}
	
	if($del == "mobile_logo"){
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/mobile_logo.gif");
	}

	if($del == "mail_logo"){
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/mail_logo.gif");
	}

    if($del == "favicon"){
        @unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/favicon.ico");
    }
    if($del == "time_sale"){
        @unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/time_sale.png");
    }
    if($del == "time_sale_mobile"){
        @unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/time_sale_mobile.png");
    }

	if($del == "watermark"){
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/watermark/watermark.png");
	}
}

if ($act == "update")
{
	if ($mobile_logo_del == "Y"){
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/mobile_logo.gif");
	}
	if ($admin_logo_size > 0){
		copy($admin_logo, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/admin_logo.gif");
	}

	if ($shop_logo_size > 0){
		copy($shop_logo, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/shop_logo.gif");
	}

	if ($shop_logo_over_size > 0){
		copy($shop_logo_over, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/shop_logo_over.gif");
	}
	
	if ($mobile_logo_size > 0){
		copy($mobile_logo, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/mobile_logo.gif");
	}

	if ($mail_logo_size > 0){
		copy($mail_logo, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/mail_logo.gif");
	}

    if ($favicon_size > 0){
        move_uploaded_file($favicon, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/favicon.ico");
    }

    if ($time_sale_size > 0){
        move_uploaded_file($time_sale, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/time_sale.png");
    }
    if ($time_sale_mobile_size > 0){
        move_uploaded_file($time_sale_mobile, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/time_sale_mobile.png");
    }

	if ($watermark_size > 0){
		move_uploaded_file($watermark, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/watermark/watermark.png");
	}


	if($mall_use_identificationUse != 'Y')	$mall_use_identification = '';

	if($admininfo[mall_type] != "R" && $admininfo[mall_type] != "B"){
		$sql = "update ".TBL_SHOP_SHOPINFO." set  mall_domain_id = '$mall_domain_id',mall_domain = '$mall_domain',mall_domain_key='$mall_domain_key' where mall_ix in ('$mall_ix','20bd04dac38084b2bafdd6d78cd596b1','20bd04dac38084b2bafdd6d78cd596b2') and mall_div = '$mall_div' ";
		$db->query($sql);
	}

	if($admininfo[mall_type] != "H"){
		$add_query=" , sattle_module = '$sattle_module',compound_tax='$compound_tax', naver_checkout='$naver_checkout' , contents_selling='$contents_selling' , mall_use_templete = '$mall_use_templete',mall_use_mobile_templete = '$mall_use_mobile_templete' ";
		//$add_query.=" , new_promotion = '$new_promotion' ";
	}//mallinfo.php 에서 mall_type 이 H 일 경우 위의 정보들을 숨김 kbk 13/01/30

	$sql = "update ".TBL_SHOP_SHOPINFO." set  
				mall_title='$mall_title',
				mall_keyword='$mall_keyword', 
				mall_use_inventory = '$mall_use_inventory',
				mall_dc_interval = '$mall_dc_interval',
				mall_cc_interval = '$mall_cc_interval',
				translator='$translator' ,
				currency_view='$currency_view' ,
				unit_conversion='$unit_conversion' 
				".$add_query."
				where mall_ix in ('$mall_ix','20bd04dac38084b2bafdd6d78cd596b1','20bd04dac38084b2bafdd6d78cd596b2')";
	//mall_use_identification = '$mall_use_identification',mall_open_yn = '$mall_open_yn',

	$db->query($sql);

	foreach ($_POST as $key => $val) {
		if($key == 'sns'){
            if(is_array($val)){	//거래처 유형
                $val_sub = "";
                foreach($val as $sub_key=>$value){
                    if($val_sub == ''){
                        $val_sub = $value;
                    }else{
                        $val_sub .= "|".$value;
                    }
                }
            }
            $val = $val_sub;
		}
		if($key != "act" && $key != "mall_ix" && $key != "x" && $key != "y"){ 
			if($db->dbms_type=='oracle'){
				
				$sql = "delete shop_mall_config where 
						mall_ix = '".$_POST[mall_ix]."' and
						config_name ='".$key."' and
						config_value ='".$val."'  ";
				$db->query($sql);
				$sql ="INSERT INTO shop_mall_config (mall_ix,config_name,config_value) values ('".$_POST[mall_ix]."','".$key."','".$val."')";
				
			}else{
				$sql = "REPLACE INTO `shop_mall_config` set 
						mall_ix = '".$_POST[mall_ix]."',
						config_name ='".$key."',
						config_value ='".$val."'  ";
                $db->query($sql);

                $sql = "REPLACE INTO `shop_mall_config` set 
						mall_ix = '20bd04dac38084b2bafdd6d78cd596b2',
						config_name ='".$key."',
						config_value ='".$val."'  ";
			}
			$db->query($sql);
		}
	}

	if($barobill_key){//바로빌 정보 입력 2013-05-03 이학봉
		$sql = "select count(*) as cnt from shop_payment_config where mall_ix = '".$mall_ix."' and config_name = 'barobill_key' ";
		$db->query($sql);
		$db->fetch();
		if($db->dt[cnt] > 0){
			$sql = " update shop_payment_config set config_value = '".$barobill_key."' where mall_ix = '".$mall_ix."' and config_name = 'barobill_key' ";
		$db->query($sql);
		}else{
			$db->query("insert into  shop_payment_config (mall_ix, pg_code, config_name, config_value) values('$mall_ix', 'barobill', 'barobill_key', '$barobill_key')");
		}
	}
	
	if($barobill_id){//바로빌 정보 입력 2013-05-03 이학봉
		$sql = "select count(*) as cnt from shop_payment_config where mall_ix = '".$mall_ix."' and config_name = 'barobill_id' ";
		$db->query($sql);
		$db->fetch();
		if($db->dt[cnt] > 0){
			$sql = " update shop_payment_config set config_value = '".$barobill_id."' where mall_ix = '".$mall_ix."' and config_name = 'barobill_id' ";
		$db->query($sql);
		}else{
			$db->query("insert into  shop_payment_config (mall_ix, pg_code, config_name, config_value) values('$mall_ix', 'barobill', 'barobill_id', '$barobill_id')");
		}
	}

	if($barobill_pw){//바로빌 정보 입력 2013-05-03 이학봉
		$sql = "select count(*) as cnt from shop_payment_config where mall_ix = '".$mall_ix."' and config_name = 'barobill_pw' ";
		$db->query($sql);
		$db->fetch();
		if($db->dt[cnt] > 0){
			$sql = " update shop_payment_config set config_value = '".$barobill_pw."' where mall_ix = '".$mall_ix."' and config_name = 'barobill_pw' ";
		$db->query($sql);
		}else{
			$db->query("insert into  shop_payment_config (mall_ix, pg_code, config_name, config_value) values('$mall_ix', 'barobill', 'barobill_pw', '$barobill_pw')");
		}
	}

	if($bmall_use_templete != $mall_use_templete){
		unset($_SESSION["shopcfg"]);
		unset($_SESSION["layout_config"]);
		unset($_SESSION["admin_config"]["selected_templete"]);
		unset($_SESSION["admin_config"]["selected_templete_general"]);
	}
	session_start();
	$admininfo[sattle_module]  = $sattle_module;
	$admin_config[currency_unit] = $currency_unit;
	session_register("admininfo");
	if($mall_use_inventory == "Y"){
		$admin_config[mall_use_inventory] = "Y";
		
		//$is_table = $db->mysql_table_exists("inventory_input");
		//if(!$is_table){
		//}
	} else {
		$admin_config[mall_use_inventory] = "N";
	}
	
	
	//echo("<script>alert('정상적으로 수정되었습니다.');</script>");
	//echo("<script>parent.document.location.reload();</script>");
	//echo("<script>parent.completeLoading('정상적으로 수정되었습니다.');</script>");
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}

?>
