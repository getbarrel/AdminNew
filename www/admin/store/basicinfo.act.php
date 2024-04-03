<?
include("../../class/database.class");

session_start();

$db = new Database;

if ($act == "update")
{

	$com_phone  = trim("$com_phone1-$com_phone2-$com_phone3");
	$com_fax  = trim("$com_fax1-$com_fax2-$com_fax3");
	$homepage  = trim($homepage);
	$shipping_company  = trim($shipping_company);

	if($info_type == "basic" || $info_type == ""){

		$com_name  = trim($com_name);
		$com_number  = trim($com_number);
		$business_kind  = trim($business_kind);
		$com_ceo  = trim($com_ceo);
		$business_item  = trim($business_item);
		$com_zip  = trim($com_zip1."-".$com_zip2);

		$sql = "UPDATE ".TBL_COMMON_COMPANY_DETAIL." SET
					com_name='$com_name',
					com_div='$com_div', 
					com_type='S',
					com_ceo='$com_ceo', 
					com_business_status='$com_business_status',
					com_business_category='$com_business_category',
					com_number='$com_number', 
					online_business_number='$online_business_number',
					com_phone='$com_phone',
					com_fax='$com_fax',
					com_zip='$com_zip', 
					com_addr1='$com_addr1', 
					com_addr2='$com_addr2',
					seller_auth='$seller_auth'
				WHERE 
					company_id='".$admininfo[company_id]."'"; // 이름에 대한 수정을 없앰 kbk
				// 회원과 회사 정보는 1:다 관계 이므로 code 값을 company_id 로 변경
		$db->query($sql);

		if ($company_stamp_size > 0){
			copy($company_stamp, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/stamps/company_stamp_".$admininfo[company_id].".gif");
			$company_stamp_str = $admin_config[mall_data_root]."/images/stamps/company_stamp_".$admininfo[company_id].".gif";
		}


		$sql = "select * from common_seller_delivery where company_id ='$company_id'";
		$db->query($sql);
		if(!$db->total){
			$sql = "insert into common_seller_detail set
						company_id='$company_id',
						shop_name='$shop_name',
						shop_desc='$shop_desc',
						homepage='$homepage',
						bank_owner='$bank_owner',
						bank_name='$bank_name',
						bank_number='$bank_number'";
			$db->query($sql);

		}else{

			$sql = "update common_seller_detail set
						shop_name='$shop_name',
						shop_desc='$shop_desc',
						homepage='$homepage',
						bank_owner='$bank_owner',
						bank_name='$bank_name',
						bank_number='$bank_number'
					where 
						company_id='$company_id' ";
			$db->query($sql);

		}

		if ($shop_img_size > 0){
			copy($shop_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/shopimg/shop_".$admininfo[company_id].".gif");
			$shop_img_str = $admin_config[mall_data_root]."/images/shopimg/shop_".$admininfo[company_id].".gif";

		}

	}else if($info_type == "delivery_info"){
		$sql = "select * from common_seller_delivery where company_id ='$company_id'";
		$db->query($sql);
		if(!$db->total){
			$sql = "insert into common_seller_delivery set
						company_id='$company_id',
						commission='$commission',
						delivery_policy='$delivery_policy',
						delivery_basic_policy='$delivery_basic_policy',
						delivery_price='$delivery_price',
						delivery_freeprice='$delivery_freeprice',
						delivery_free_policy='$delivery_free_policy',
						delivery_product_policy='$delivery_product_policy'";
			$db->query($sql);

		}else{
			$sql = "update common_seller_delivery set
						commission='$commission',
						delivery_policy='$delivery_policy',
						delivery_basic_policy='$delivery_basic_policy',
						delivery_price='$delivery_price',
						delivery_freeprice='$delivery_freeprice',
						delivery_free_policy='$delivery_free_policy',
						delivery_product_policy='$delivery_product_policy'
					where 
						company_id='$company_id' ";
			$db->query($sql);

		}

	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'company.add.php?company_id=".$admininfo[company_id]."&info_type=".$info_type."';</script>");

}

?>
