<?
include("../../class/database.class");


if($act=="update"){

	$db = new Database;

	$sql = "update common_seller_delivery set 
					seller_minishop_promotion_use='$seller_minishop_promotion_use'
				where company_id='$company_id' ";
	$db->query($sql);
	
	for($i=0;$i < count($group_name);$i++){
		$db->query("Select mpg_ix from shop_minishop_product_group where group_code = '".($i+1)."' AND company_id='".$company_id."' ");

		if($db->total){
			$db->fetch();
			$sql = "update shop_minishop_product_group set
							group_name='".$group_name[$i+1]."',
							display_type='".$display_type[$i+1]."',
							insert_yn='Y', use_yn='".$use_yn[$i+1]."',
							group_link='".$group_link[$i+1]."',
							product_cnt='".$product_cnt[$i+1]."',
							goods_display_type='".$goods_display_type[$i+1]."',							
							display_auto_type='".$display_auto_type[$i+1]."'
							where mpg_ix='".$db->dt[mpg_ix]."' and group_code = '".($i+1)."' AND company_id='".$company_id."' ";
			$db->query($sql);
		}else{
				

			$sql = "insert into shop_minishop_product_group (mpg_ix,group_name,group_code,group_link,display_type,product_cnt, goods_display_type, display_auto_type, insert_yn, use_yn, company_id, regdate) values('','".$group_name[$i+1]."','".($i+1)."','".$group_link[$i+1]."','".$display_type[$i+1]."','".$product_cnt[$i+1]."','".$goods_display_type[$i+1]."','".$display_auto_type[$i+1]."','Y','".$use_yn[$i+1]."','".$company_id."',NOW())";
			$db->query($sql);
		}
		if($page_type=="M") $img_pre_text="";
		else $img_pre_text=$page_type."_";
		if ($group_img_del[$i+1] == "Y")
		{
			unlink("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/minishop/".$img_pre_text."minishop_group_".($i+1).".gif");
		}

		//echo "size:".$_FILES["group_img"]["size"][$i+1]."<br />";
		//print_r($_FILES);
		//exit;
		
		if ($_FILES["group_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_img"][tmp_name][$i+1], "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/minishop/".$img_pre_text."minishop_group_".($i+1).".gif");
		}

		if ($group_banner_img_del[$i+1] == "Y")
		{
			unlink("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/minishop/".$img_pre_text."minishop_group_banner_".($i+1).".gif");
		}

		if ($_FILES["group_banner_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_banner_img"][tmp_name][$i+1], "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/minishop/".$img_pre_text."minishop_group_banner_".($i+1).".gif");
		}

		//$db->query("SELECT mpg_ix FROM shop_minishop_product_group WHERE mpg_ix=LAST_INSERT_ID()");
		//$db->fetch();
		//$mpg_ix = $db->dt[0];

		//$db->query("update shop_minishop_product_relation set insert_yn = 'N' where main_ix='".$main_ix."' and group_code = '".($i+1)."' ");
		$db->query("update shop_minishop_product_relation set insert_yn = 'N' where group_code = '".($i+1)."' ");
		for($j=0;$j < count($rpid[$i+1]);$j++){
			$db->query("Select mpr_ix from shop_minishop_product_relation where group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' AND company_id='".$company_id."' ");

			if(!$db->total){
				$sql = "insert into shop_minishop_product_relation (mpr_ix,pid,group_code, vieworder, insert_yn, company_id, regdate) values ('','".$rpid[$i+1][$j]."','".($i+1)."','".($j+1)."','Y', '".$company_id."', NOW())";
				$db->query($sql);
			}else{
				$sql = "update shop_minishop_product_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' AND company_id='".$company_id."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_minishop_product_relation where group_code = '".($i+1)."' and insert_yn = 'N' AND company_id='".$company_id."' ");

		$db->query("update shop_minishop_category_relation set insert_yn = 'N'  where group_code = '".($i+1)."' AND company_id='".$company_id."' ");

		for($j=0;$j < count($category[$i+1]);$j++){
			$db->query("Select mcr_ix from shop_minishop_category_relation where group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' AND company_id='".$company_id."' ");

			if(!$db->total){
				$sql = "insert into shop_minishop_category_relation (mcr_ix,cid,group_code, vieworder, insert_yn, company_id, regdate) values ('','".$category[$i+1][$j]."','".($i+1)."','".($j+1)."','Y', '".$company_id."', NOW())";
				$db->query($sql);
			}else{
				$sql = "update shop_minishop_category_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' AND company_id='".$company_id."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_minishop_category_relation where group_code = '".($i+1)."' and insert_yn = 'N' AND company_id='".$company_id."' ");

	}


	$db->query("delete from shop_minishop_product_relation where insert_yn = 'N' AND company_id='".$company_id."' ");
	$db->query("delete from shop_minishop_product_group where insert_yn = 'N' AND company_id='".$company_id."' ");
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}

?>