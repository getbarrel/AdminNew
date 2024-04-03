<?
include("../../class/layout.class");

//print_r($_POST);
//exit;
$db = new Database;


if ($act == "insert")
{


	$com_phone  = trim($com_phone);
	$com_fax  = trim($com_fax);
	$homepage  = trim($homepage);
	$shipping_company  = trim($shipping_company);
	$company_id  = md5(uniqid(rand()));

	if($info_type == "basic"){

		$com_name  = trim($com_name);
		$com_number  = trim($com_number);
		$business_kind  = trim($business_kind);
		$com_ceo  = trim($com_ceo);
		$business_item  = trim($business_item);
		$com_zip  = trim($com_zip);



		$sql = "insert into ".TBL_COMMON_COMPANY_DETAIL." SET
				company_id='$company_id',
				com_name='$com_name',
				com_div='$com_div',
				com_type='CS',
				com_ceo='$com_ceo',
				com_business_status='$com_business_status',
				com_business_category='$com_business_category',
				com_number='$com_number',
				online_business_number='$online_business_number',
				com_phone='$com_phone',
				com_fax='$com_fax',
				com_email='$com_email',
				com_zip='$com_zip',
				com_addr1='$com_addr1',
				com_addr2='$com_addr2',
				seller_auth='$seller_auth'
				"; // 이름에 대한 수정을 없앰 kbk
				// 회원과 회사 정보는 1:다 관계 이므로 code 값을 company_id 로 변경

		$db->query($sql);

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/stamps/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}
		if ($company_stamp_size > 0){
			copy($company_stamp, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif");
			$company_stamp_str = $admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif";
		}


		$sql = "insert into common_seller_detail set 
				company_id='$company_id',
				shop_name='$shop_name',
				shop_desc='$shop_desc',
				homepage='$homepage',
				minishop_templet='$minishop_templet' ";

		
		$db->query($sql);
		


		$delivery_company = 1;
		$delivery_policy = 1;
		$delivery_basic_policy = "2";
		$delivery_price = 3000;
		$delivery_freeprice = 30000;
		$delivery_free_policy = 1;
		$delivery_product_policy = 1;

		$sql = "insert into common_seller_delivery set
			company_id='$company_id',
			commission='$commission',
			delivery_company='$delivery_company',
			delivery_policy='$delivery_policy',
			delivery_basic_policy='$delivery_basic_policy',
			delivery_price='$delivery_price',
			delivery_freeprice='$delivery_freeprice',
			delivery_free_policy='$delivery_free_policy',
			delivery_product_policy='$delivery_product_policy',
			delivery_region_use='$delivery_region_use',
			delivery_policy_text='$delivery_policy_text'
			 ";

		$db->query($sql);
		



	}else if($info_type == "seller_info"){
		$sql = "insert into  common_seller_detail set
				company_id='$company_id',
				shop_name='$shop_name',
				shop_desc='$shop_desc',
				homepage='$homepage',
				bank_owner='$bank_owner',
				bank_name='$bank_name',
				bank_number='$bank_number',
				md_code='$md_code',
				team='$team',
				edit_date = NOW()
				 ";

		//echo $sql;
		$db->query($sql);
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}
		if ($shop_logo_img_size > 0){
			copy($shop_logo_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif");
			$shop_logo_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif";

		}

		if ($shop_img_size > 0){
			copy($shop_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif");
			$shop_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif";

		}

		if ($shop_img_thum_size > 0){
			copy($shop_img_thum, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif");
			$shop_img_thum_str = $admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif";
		}

	}else if($info_type == "delivery_info"){

		$sql = "insert into common_seller_delivery set
				company_id='$company_id',
				commission='$commission',
				delivery_company='$delivery_company',
				delivery_policy='$delivery_policy',
				delivery_basic_policy='$delivery_basic_policy',
				delivery_price='$delivery_price',
				delivery_freeprice='$delivery_freeprice',
				delivery_free_policy='$delivery_free_policy',
				delivery_product_policy='$delivery_product_policy',
				delivery_region_use='$delivery_region_use',
				delivery_policy_text='$delivery_policy_text'
				 ";

		$db->query($sql);
		//echo $sql;


	}

	if($admininfo[admin_level] == 9){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'chainstore.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
	}else if($admininfo[admin_level] == 8){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력 되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'chainstore.add.php?mmode=".$mmode."&company_id=".$admininfo[company_id]."';</script>");
	}


}


if ($act == "update")
{


	$com_phone  = trim($com_phone);
	$com_fax  = trim($com_fax);
	$homepage  = trim($homepage);
	$shipping_company  = trim($shipping_company);


	if($info_type == "basic"){

		$com_name  = trim($com_name);
		$com_number  = trim($com_number);
		$business_kind  = trim($business_kind);
		$com_ceo  = trim($com_ceo);
		$business_item  = trim($business_item);


		$com_zip  = trim($com_zip);



		$sql = "UPDATE ".TBL_COMMON_COMPANY_DETAIL." SET
				com_name='$com_name', com_div='$com_div', com_type='CS', com_ceo='$com_ceo', com_business_status='$com_business_status', com_business_category='$com_business_category',
				com_number='$com_number', online_business_number='$online_business_number', com_phone='$com_phone', com_fax='$com_fax', com_email='$com_email', com_email='$com_email', com_zip='$com_zip',  com_addr1='$com_addr1',  com_addr2='$com_addr2', seller_auth='$seller_auth'
				WHERE company_id='$company_id'"; // 이름에 대한 수정을 없앰 kbk
				// 회원과 회사 정보는 1:다 관계 이므로 code 값을 company_id 로 변경

		$db->query($sql);
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/stamps/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}

		if ($company_stamp_size > 0){
			copy($company_stamp, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif");
			$company_stamp_str = $admin_config[mall_data_root]."/images/stamps/stamp_".$company_id.".gif";
		}



	}else if($info_type == "seller_info"){
		$sql = "select * from common_seller_delivery where company_id ='$company_id'";
		$db->query($sql);
		if(!$db->total){
			$sql = "insert into  common_seller_detail set
					company_id='$company_id',
					shop_name='$shop_name',
					shop_desc='$shop_desc',
					homepage='$homepage',
					minishop_templet='$minishop_templet',
					bank_owner='$bank_owner',
					bank_name='$bank_name',
					bank_number='$bank_number',
					md_code='$md_code',
					team='$team', 
					edit_date = NOW()
					 ";

			//echo $sql;
			$db->query($sql);
		}else{

			$sql = "update common_seller_detail set										
					shop_name='$shop_name',shop_desc='$shop_desc',homepage='$homepage',minishop_templet='$minishop_templet', bank_owner='$bank_owner',bank_name='$bank_name',bank_number='$bank_number',
					md_code='$md_code',team='$team', edit_date = NOW()
					where company_id='$company_id' ";


			//echo $sql;
			$db->query($sql);
		}
		//print_r($_FILES);
		//echo $shop_logo_img_size;
		//exit;
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}
		if ($shop_logo_img_size > 0){
			copy($shop_logo_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif");
			$shop_logo_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_logo_".$company_id.".gif";

		}

		if ($shop_img_size > 0){
			copy($shop_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif");
			$shop_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_".$company_id.".gif";

		}

		if ($shop_img_thum_size > 0){
			copy($shop_img_thum, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif");
			$shop_img_thum_str = $admin_config[mall_data_root]."/images/shopimg/shop_thum_".$company_id.".gif";

		}

	}else if($info_type == "delivery_info"){
		$delivery_policy_text = $content;
		$sql = "select * from common_seller_delivery where company_id ='$company_id'";
		$db->query($sql);
		if(!$db->total){
			$sql = "insert into common_seller_delivery set
					company_id='$company_id',
					commission='$commission',
					delivery_company='$delivery_company',
					delivery_policy='$delivery_policy',
					delivery_basic_policy='$delivery_basic_policy',
					delivery_price='$delivery_price',
					delivery_freeprice='$delivery_freeprice',
					delivery_free_policy='$delivery_free_policy',
					delivery_product_policy='$delivery_product_policy',
					delivery_region_use='$delivery_region_use',
					delivery_policy_text='$delivery_policy_text'
					 ";

			$db->query($sql);
		}else{
			$sql = "update common_seller_delivery set
					commission='$commission',
					delivery_company='$delivery_company',
					delivery_policy='$delivery_policy',
					delivery_basic_policy='$delivery_basic_policy',
					delivery_price='$delivery_price',
					delivery_freeprice='$delivery_freeprice',
					delivery_free_policy='$delivery_free_policy',
					delivery_product_policy='$delivery_product_policy',
					delivery_region_use='$delivery_region_use',
					delivery_policy_text='$delivery_policy_text'
					where company_id='$company_id' ";

			$db->query($sql);
		}
		//echo $sql;

		if($region_delivery_type == 1){	
			$sql = "update shop_region_delivery set insert_yn = 'N' where company_id='".$company_id."' ";
			$db->query($sql);

			for($i=0;$i<count($region_name_text);$i++){			
				
				if($region_name_text[$i]){
					$sql = "select * from shop_region_delivery where rd_ix='".$rd_ix[$i]."' ";
					$db->query($sql);

					if($db->total && $rd_ix[$i]){
						$sql = " update shop_region_delivery set				
								region_name_text='".$region_name_text[$i]."',
								region_name_price='".$region_name_price[$i]."',insert_yn='Y',regdate=NOW() where rd_ix='".$rd_ix[$i]."' ";

						$db->query($sql);
					}else{
						$sql = "insert into shop_region_delivery (rd_ix,company_id, region_delivery_type,region_name_text, region_name_price,insert_yn,regdate) 
								values 
								('','".$company_id."','$region_delivery_type','".$region_name_text[$i]."','".$region_name_price[$i]."','Y',NOW())";
						$db->query($sql);
					}
				}
			}

			$sql = "delete from shop_region_delivery where insert_yn = 'N' and company_id='".$company_id."' ";
			$db->query($sql);
		}


	}

	if($admininfo[admin_level] == 9){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'chainstore.add.php?mmode=".$mmode."&company_id=".$company_id."&info_type=".$info_type."';</script>");
	}else if($admininfo[admin_level] == 8){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'chainstore.add.php?mmode=".$mmode."&company_id=".$admininfo[company_id]."';</script>");
	}


}

if ($act == "recommend")
{
	$sql = "UPDATE ".TBL_COMMON_COMPANY_DETAIL." SET
			recommend='$recomm'
			WHERE company_id='$company_id'"; // 이름에 대한 수정을 없앰 kbk
			// 회원과 회사 정보는 1:다 관계 이므로 code 값을 company_id 로 변경

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
	echo("<script>top.document.location.reload();</script>");
}



if($act == "delete"){
	$sql = "delete from ".TBL_COMMON_COMPANY_DETAIL." where company_id ='$company_id'";
	//echo $sql;
	$db->query($sql);

	$sql = "select code from ".TBL_COMMON_USER." where company_id ='$company_id'";
	$db->query($sql);
	$total = $db->total;
	$users = $db->fetchall();

	for($i=0;$i < count($users);$i++){
		$db->fetch($i);
		$code = $users[$i][code];

		$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL."  where code = '".$code."' ";
		//echo $sql;
		$db->query($sql);

		$sql = "delete from ".TBL_COMMON_USER."  where company_id ='$company_id' and code = '".$code."' ";
		//echo $sql;
		$db->query($sql);
		

	}
	
	

	


	$sql = "select id from ".TBL_SHOP_PRODUCT." where admin = '$company_id'";
	$db->query($sql);
	$total = $db->total;
	for($i=0;$i < $total;$i++){
		$db->fetch($i);
		$id = $db->dt[id];

		$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product", $id, 'Y');
		$adduploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/addimg", $id, 'Y');

		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/b_$id.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/b_$id.gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/".$uploaddir."m_$id.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/m_$id.gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_$id.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/ms_$id.gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/s_$id.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/s_$id.gif");
		}
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/c_$id.gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product".$uploaddir."/c_$id.gif");
		}

		if($id && is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id")){
			rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product_detail/$id");
		}

		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." WHERE id='$id'");
		$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." WHERE pid='$id'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." WHERE pid='$id'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_RELATION." WHERE pid='$id'");
		$db->query("DELETE FROM ".TBL_SHOP_RELATION_PRODUCT." WHERE pid = '$id'");
		$db->query("DELETE FROM ".TBL_SHOP_PRODUCT."_auction WHERE pid = '$id'");

	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');</script>");
	echo("<script>location.href = 'company_list.php?mmode=".$mmode."&';</script>");
	echo $delivery_price;

}


if($act == "user_insert"){

	//$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE company_id = '$company_id' and id = '$id' ");
	$db->query("SELECT * FROM ".TBL_COMMON_USER." WHERE id = '$id' ");//입점업체별로 동일 아이디가 생성될 수 있으므로 company_id를 조건절에서 뺌(by 김수현대리) kbk 12/02/08

	if ($db->total)
	{
		echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$id] ' + language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 사용자 입니다.
		//echo("<script>history.back();</script>");
		exit;
	}

	$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id = '$id' ");

	if ($db->total)
	{
		echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$id] '+ language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 아이디 입니다.
		//echo("<script>history.back();</script>");
		exit;
	}

	$id    = trim($id);
	$pw  = trim($pw);
	$name  = trim($name);
	$nick_name  = trim($nick_name);
	//$mail  = trim($mail1."@".$mail2);
	$addr1 = trim($addr1);
	$addr2 = trim($addr2);
	$comp  = trim($comp);
	$class = trim($class);
	$birthday=$birthday1."-".$birthday2."-".$birthday3;
	$zip   = "$zipcode1-$zipcode2";
	$tel = trim($tel);
	$pcs = trim($pcs);
	//$tel   = "$tel1-$tel2-$tel3";
	//$pcs   = "$pcs1-$pcs2-$pcs3";
	$code  = md5(uniqid(rand()));




	if(!isset($department)){
		$department = "0";
	}

	if(!isset($position)){
		$position = "0";
	}

	$gp_ix = "1";
	$sql = "INSERT INTO ".TBL_COMMON_USER."
					(code, id, pw, mem_type, language, company_id, date, visit, last, ip, auth,authorized)
					VALUES
					('$code','$id','".hash("sha256", $pw)."','S','".$language_type."','".$company_id."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."', '".$auth."','".$authorized."')";

	$db->query($sql);

	$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
					(code, name, mail, tel, pcs, date, recom_id, gp_ix)
					VALUES
					('$code',HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),NOW(),'".$admininfo[charger_id]."','$gp_ix')";

	$db->query($sql);

	admin_log("C",$id,$company_id);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('셀러가 정상적으로 등록되었습니다.');parent.document.location.reload();</script>");

}

if($act == "user_update"){

	admin_log("U",$id,$company_id);


	$tel = trim($tel);
	$pcs = trim($pcs);

	if($change_pass){
		$update_pass_str = ", pw= '".hash("sha256", $pw)."'";
	}

	if(trim($charger_id) != trim($bcharger_id)){
		$db->query("select * from ".TBL_COMMON_USER."  where company_id='".trim($company_id)."' and id='".trim($id)."' ");

		if($db->total){
			//echo "<script language='javascript'>alert('$charger_id 아이디는 이미 사용중입니다.');</script>";
			echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$charger_id] ' + language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 사용자 입니다.
			exit;
		}

		$db->query("SELECT * FROM ".TBL_COMMON_DROPMEMBER." WHERE id = '$id' ");

		if ($db->total)
		{
			//echo("<script>alert('[$id] 이미 등록된 아이디 입니다. ');</script>");
			echo("<script language='javascript' src='../_language/language.php'></script><script>alert('[$id] ' + language_data['company.act.php']['A'][language]);</script>"); //는 이미 등록된 아이디 입니다.
			//echo("<script>history.back();</script>");
			exit;
		}
	}


	if(!isset($department)){
		$department = "0";
	}

	if(!isset($position)){
		$position = "0";
	}
	$sql = "UPDATE ".TBL_COMMON_USER." SET
			id='$id' , language = '$language_type',authorized = '$authorized', auth = '$auth' $update_pass_str
			WHERE code='$code'";

	$db->query($sql);


	$sql = "UPDATE ".TBL_COMMON_MEMBER_DETAIL." SET
			mail= HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')), tel=HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),pcs=HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')), name = HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')) , department = '$department' , position = '$position'
			WHERE code='$code'";



	//echo $sql;
	//exit	;
	$db->query($sql);

	//변경정보와 로그인 아이디가 같으면 랭귀지 변경정보를 세션에 반영한다.
	if($admininfo[charger_ix] == $code){
		$admininfo["language"] = $language_type;
	}


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('관리자가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}

if($act == "user_delete"){

	admin_log("D",$id,$company_id);

	$db->query("SELECT code, company_id FROM ".TBL_COMMON_USER." WHERE company_id = '$company_id' and code = '$code' ");
	$db->fetch();
	$code = $db->dt[code];

	$sql = "delete from ".TBL_COMMON_USER." where company_id ='$company_id' and code = '$code'";

	$db->query($sql);

	$sql = "delete from ".TBL_COMMON_MEMBER_DETAIL." where  code = '$code'";

	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('입점업체 사용자 정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
	//echo("<script>location.href = 'company_list.php';</script>");
}

if($act == "admin_log")
{
	admin_log("R",$charger_id,$company_id);
}

function admin_log($crud_div,$id,$company_id)
{
	global $admininfo;

	$mdb = new Database;

	$mdb->query("select ccd.com_name, cmd.name from common_user cu, common_member_detail cmd ,  common_company_detail ccd  where cu.code = cmd.code and cu.company_id = ccd.company_id and cu.company_id = '$company_id' and cu.id = '$id'");

	$mdb->fetch();


	$sql = "insert into admin_log(accept_com_name,accept_m_name,admin_id,admin_name,crud_div,ip,regdate) values('".$mdb->dt[com_name]."','".$mdb->dt[name]."','".$admininfo['charger_id']."','".$admininfo['charger']."','$crud_div','".$_SERVER["REMOTE_ADDR"]."',NOW())";

	$mdb->query($sql);


}

/*
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
*/
?>
