<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("../include/admin.util.php");

$db = new Database;

if ($act == "vieworder_update"){
	//$db->query("update shop_category_main_product_relation set vieworder=1 ");
	//$_erpid = str_replace("\\\"","\"",$_POST["erpid"]);
	//echo $_erpid;
	//echo "bbb:".count(unserialize($_POST["erpid"]))."<br>";
	//print_r(unserialize($_POST["erpid"]));
	//$_erpid = unserialize(urldecode($_erpid));
	//$erpid = unserialize(urldecode($erpid));
	//echo count($_erpid);
	for($i=0;$i < count($sortlist);$i++){
		$sql = "update shop_category_main_product_relation set
			vieworder='".($i+1)."'
			where  pid='".$sortlist[$i]."' ";//main_ix='$main_ix' and

		//echo $sql;
		$db->query($sql);
	}

}

if ($act == "update" || $act == "insert"){

	//print_r($_POST);
	//exit;
	//$cmg_use_sdate = $cmg_use_sdate." ".$cmg_use_stime.":".$cmg_use_smin.":00";
	//$cmg_use_edate = $cmg_use_edate." ".$cmg_use_etime.":".$cmg_use_emin.":00";

	$unix_timestamp_sdate = mktime($cmg_use_stime,$cmg_use_smin,0,substr($cmg_use_sdate,5,2),substr($cmg_use_sdate,8,2),substr($cmg_use_sdate,0,4));
	$unix_timestamp_edate = mktime($cmg_use_etime,$cmg_use_emin,0,substr($cmg_use_edate,5,2),substr($cmg_use_edate,8,2),substr($cmg_use_edate,0,4));


	$sql = "SELECT cmg_use_sdate , cmg_use_edate FROM shop_category_main_goods where cmg_ix ='$cmg_ix' ";

	$db->query($sql); //AND cid='$cid2'

	if($db->total){
		$sql = "update shop_category_main_goods set
				cmg_title='$cmg_title',
				cmg_link='$cmg_link',
				goods_max='$goods_max',
				image_width='$image_width',
				image_height='$image_height',
				cmg_use_sdate='$unix_timestamp_sdate',
				cmg_use_edate='$unix_timestamp_edate',
				md_mem_ix='$md_code',
				sales_target='$sales_target',
				disp='$disp',
				div_ix='$div_ix',
				display_cid='$display_cid',
				display_position='$display_position'
				where cmg_ix='$cmg_ix'";

		//echo $sql;
		//exit;
		$db->query($sql);

	}else{
		$sql = "SELECT cmg_ix, cmg_use_sdate , cmg_use_edate 
					FROM shop_category_main_goods 
					where div_ix ='$div_ix' and display_position = '$display_position' and display_cid = '".$display_cid."' 
					order by cmg_use_edate desc limit 0,1";
		//echo $sql;
		//exit;
		$db->query($sql); //AND cid='$cid2'
		if($db->total){
			$db->fetch();
			
			$unix_timestamp_sdate = mktime($cmg_use_sdate_h,$cmg_use_sdate_i,0,substr($cmg_use_sdate,5,2),substr($cmg_use_sdate,8,2),substr($cmg_use_sdate,0,4));
			$unix_timestamp_edate = mktime($cmg_use_edate_h,$cmg_use_edate_i,0,substr($cmg_use_edate,5,2),substr($cmg_use_edate,8,2),substr($cmg_use_edate,0,4));


			if($db->dt[cmg_use_edate] > $unix_timestamp_sdate){

				$set_use_sdate = mktime(0,0,0,date('m',$db->dt[cmg_use_edate]),date('d',$db->dt[cmg_use_edate])+1,date('Y',$db->dt[cmg_use_edate]));
				$set_use_edate = mktime(0,0,0,date('m',$db->dt[cmg_use_edate]),date('d',$db->dt[cmg_use_edate])+10,date('Y',$db->dt[cmg_use_edate]));

				echo("<script>alert('프로모션 상품 노출 시작일자가 ".date("Y-m-d",$db->dt[cmg_use_edate])." 일 이후여야 합니다. 시작일자를 ".date("Y-m-d",$set_use_sdate)." 로 설정합니다.');parent.select_date('".date("Y-m-d",$set_use_sdate)."','".date("Y-m-d",$set_use_edate)."',1);	</script>");
				exit;
			}else{
				$cmg_ix = $db->dt[cmg_ix];

				$sql = "update shop_category_main_goods set
				cmg_title='$cmg_title',
				cmg_link='$cmg_link',
				goods_max='$goods_max',
				image_width='$image_width',
				image_height='$image_height',
				cmg_use_sdate='$unix_timestamp_sdate',
				cmg_use_edate='$unix_timestamp_edate',
				md_mem_ix='$md_code',
				sales_target='$sales_target',
				disp='$disp',
				div_ix='$div_ix',
				display_cid='$display_cid',
				display_position='$display_position'
				where cmg_ix='$cmg_ix'";


				$db->query($sql);
			}
		}else{
			$sql = "insert into shop_category_main_goods
					(cmg_ix,div_ix,display_cid,display_position,cmg_title,cmg_link,goods_max,image_width, image_height,cmg_use_sdate,cmg_use_edate,md_mem_ix,sales_target, disp,regdate)
					values
					('','$div_ix','$display_cid','$display_position','$cmg_title','$cmg_link','$goods_max','$image_width','$image_height','$unix_timestamp_sdate','$unix_timestamp_edate','$md_mem_ix','$sales_target','$disp',NOW())";

			//echo nl2br($sql);
			//exit;
			$db->sequences = "SHOP_CT_MAIN_GOODS_SEQ";
			$db->query($sql);

			if($db->dbms_type == "oracle"){
				$cmg_ix = $db->last_insert_id;
			}else{
				$db->query("SELECT cmg_ix FROM shop_category_main_goods WHERE cmg_ix=LAST_INSERT_ID()");
				$db->fetch();
				$cmg_ix = $db->dt[cmg_ix];
			}
		}
	}


	$sql = "update shop_category_main_product_relation set insert_yn='N' where cmg_ix = '".$cmg_ix."' ";
	$db->query($sql);

	$sql = "update shop_category_main_product_group set insert_yn='N' where cmg_ix = '".$cmg_ix."' ";
	$db->query($sql);

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/";
	if(!is_dir($path)){
		mkdir($path);
		chmod($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/";
	if(!is_dir($path)){
		mkdir($path);
		chmod($path, 0777);
	}

	if ($cmg_title_img_del == "Y")
	{
	
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/title_img.jpg");
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/title_img.jpg");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	}	

	if ($_FILES["cmg_title_img"]["size"] > 0)
	{
		copy($_FILES["cmg_title_img"][tmp_name], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/title_img.jpg");		
	}

	for($i=0;$i < count($group_name);$i++){
		$db->query("Select cmpg_ix from shop_category_main_product_group where group_code = '".($i+1)."' and cmg_ix = '".$cmg_ix."' ");

		if($db->total){
			$db->fetch();
			$cmpg_ix = $db->dt[cmpg_ix];
			$sql = "update shop_category_main_product_group set
							div_ix='".$div_ix."',
							cmg_ix='".$cmg_ix."',
							group_name='".$group_name[$i+1]."',
							display_type='".$display_type[$i+1]."',
							insert_yn='Y', 
							use_yn='".$use_yn[$i+1]."',
							basic_display_yn ='".$basic_display_yn[$i+1]."',
							group_link='".$group_link[$i+1]."',
							product_cnt='".$product_cnt[$i+1]."',
							display_info_type='".$display_info_type[$i+1]."',							
							goods_display_type='".$goods_display_type[$i+1]."',
							goods_display_sub_type='".$goods_display_sub_type[$i+1]."',
							display_auto_type='".$display_auto_type[$i+1]."',
							display_auto_priod='".$display_auto_priod[$i+1]."',
							md_mem_ix='".$md_mem_ix[$i+1]."',
							group_sales_target='".$group_sales_target[$i+1]."'
							
							
							where cmpg_ix='".$db->dt[cmpg_ix]."' and group_code = '".($i+1)."' and cmg_ix = '".$cmg_ix."' ";
			$db->query($sql);
		}else{
			$sql = "insert into shop_category_main_product_group 
						(cmpg_ix,div_ix,cmg_ix,group_name,group_code,group_link,display_type,product_cnt,display_info_type, goods_display_type, goods_display_sub_type, display_auto_type,display_auto_priod,md_mem_ix, group_sales_target, insert_yn, use_yn, basic_display_yn, regdate) 
						values
						('','".$div_ix."','".$cmg_ix."','".$group_name[$i+1]."','".($i+1)."','".$group_link[$i+1]."','".$display_type[$i+1]."','".$product_cnt[$i+1]."','".$display_info_type[$i+1]."','".$goods_display_type[$i+1]."','".$goods_display_sub_type[$i+1]."','".$display_auto_type[$i+1]."','".$display_auto_priod[$i+1]."','".$md_mem_ix[$i+1]."','".$group_sales_target[$i+1]."','Y','".$use_yn[$i+1]."','".$basic_display_yn[$i+1]."', NOW())";
			$db->sequences = "SHOP_CT_MAIN_GOODS_GROUP_SEQ";
			$db->query($sql);

			$db->query("SELECT cmpg_ix FROM  shop_category_main_product_group WHERE cmpg_ix=LAST_INSERT_ID()");
			$db->fetch();
			$cmpg_ix = $db->dt[cmpg_ix];
		}


		$db->query("update shop_category_main_group_display set insert_yn = 'N' where cmpg_ix = '".$cmpg_ix."'   ");

		for($j=0;$j < count($display_type[$i+1][type]);$j++){
			$db->query("select cmgd_ix from shop_category_main_group_display where cmgd_ix = '".$display_type[$i+1][cmgd_ix][$j]."'   ");

			if(!$db->total){
				$sql = "insert into shop_category_main_group_display (cmgd_ix,cmpg_ix, display_type, set_cnt, vieworder, insert_yn, regdate) values ('','".$cmpg_ix."','".$display_type[$i+1][type][$j]."','".$display_type[$i+1][set_cnt][$j]."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_category_main_group_display set insert_yn = 'Y',vieworder='".($j+1)."', display_type = '".$display_type[$i+1][type][$j]."',set_cnt = '".$display_type[$i+1][set_cnt][$j]."' 
							where cmgd_ix = '".$display_type[$i+1][cmgd_ix][$j]."'  ";
				$db->query($sql);
			}
		}
		$db->query("delete from shop_category_main_group_display where cmpg_ix = '".$cmpg_ix."' and insert_yn = 'N' ");


		if ($group_img_del[$i+1] == "Y")
		{
			//unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_".($i+1).".gif");
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/cate_mg_".($i+1).".jpg");
		}

		if ($group_over_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/cate_mg_over_".($i+1).".jpg");
			@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/cate_mg_over_".($i+1).".jpg");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		//echo "size:".$group_img[size][$i+1];
		//print_r($_FILES);
		//exit;

		if ($_FILES["group_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/cate_mg_".($i+1).".jpg");
		}

		if ($_FILES["group_over_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_over_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/cate_mg_over_".($i+1).".jpg");		 
		}

		if ($group_banner_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/main_group_banner_".($i+1).".jpg");
		}

		if ($_FILES["group_banner_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_banner_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/".$cmg_ix."/main_group_banner_".($i+1).".jpg");
		}


		//$db->query("SELECT cmpg_ix FROM ".tbl_shop_main_product_group." WHERE cmpg_ix=LAST_INSERT_ID()");
		//$db->fetch();
		//$cmpg_ix = $db->dt[0];

		//$db->query("update shop_category_main_product_relation set insert_yn = 'N' where main_ix='".$main_ix."' and group_code = '".($i+1)."' ");

		for($j=0;$j < count($rpid[$i+1]);$j++){
			$db->query("Select cmpr_ix from shop_category_main_product_relation where group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' and cmg_ix = '".$cmg_ix."' ");

			if(!$db->total){
				$sql = "insert into shop_category_main_product_relation (cmpr_ix,cmg_ix,pid,group_code, vieworder, insert_yn, regdate) values ('','".$cmg_ix."','".$rpid[$i+1][$j]."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_CT_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_category_main_product_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' ";
				$db->query($sql);
			}
		}
		$db->query("delete from shop_category_main_product_relation where group_code = '".($i+1)."' and insert_yn = 'N' and cmg_ix = '".$cmg_ix."' ");

 
	
		$db->query("update shop_category_main_brand_relation set insert_yn = 'N'  where cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' and relation_type = 'M'  ");
		for($j=0;$j < count($rbrand[$i+1]);$j++){
			$db->query("select cmbr_ix from shop_category_main_brand_relation where cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' and b_ix = '".$rbrand[$i+1][$j]."' and relation_type = 'M'   ");

			if(!$db->total){
				$sql = "insert into shop_category_main_brand_relation (cmbr_ix,relation_type, b_ix,cmg_ix, group_code, vieworder, insert_yn, regdate) values ('','M','".$rbrand[$i+1][$j]."','".$cmg_ix."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_CT_MAIN_BRAND_SEQ";
				$db->query($sql);
			}else{
				$db->fetch();
				$cmbr_ix = $db->dt[cmbr_ix];
				$sql = "update shop_category_main_brand_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where cmbr_ix = '".$cmbr_ix."' and cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' and b_ix = '".$rbrand[$i+1][$j]."' and relation_type = 'M'   ";
				$db->query($sql);
			}
		}	
		$db->query("delete from shop_category_main_brand_relation where cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' and insert_yn = 'N' and relation_type = 'M'   ");

		

		
//$db->debug = true;
		$db->query("update shop_category_main_category_relation set insert_yn = 'N' where group_code = '".($i+1)."'  and cmg_ix = '".$cmg_ix."' ");
		// 자동화 부분에서 노출 카테고리 저장 하는 부분
		for($j=0;$j < count($category[$i+1]);$j++){
			$db->query("Select cmcr_ix from shop_category_main_category_relation where group_code = '".($i+1)."' and cmg_ix = '".$cmg_ix."'  and cid = '".$category[$i+1][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_category_main_category_relation (cmcr_ix,cmg_ix,cid,group_code, vieworder, insert_yn, regdate) values ('','".$cmg_ix."','".$category[$i+1][$j]."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_CT_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_category_main_category_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_category_main_category_relation where group_code = '".($i+1)."' and insert_yn = 'N' and cmg_ix = '".$cmg_ix."' ");


		/**
		* 노출 브랜드 관련
		* 담당자 : shs 
		* 작업일시 : 2014년 03월 30일
		*/
		//$db->debug = true;
		$db->query("update shop_category_main_brand_relation set insert_yn = 'N'  where cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' and relation_type = 'A'  ");

		for($j=0;$j < count($selected_result[$i+1]['brand']);$j++){
			$db->query("select cmbr_ix from shop_category_main_brand_relation where cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' and b_ix = '".$selected_result[$i+1]['brand'][$j]."' and relation_type = 'A'   ");

			if(!$db->total){
				$sql = "insert into shop_category_main_brand_relation (cmbr_ix,relation_type, b_ix,cmg_ix, group_code, vieworder, insert_yn, regdate) values ('','A','".$selected_result[$i+1]['brand'][$j]."','".$cmg_ix."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_CT_MAIN_BRAND_SEQ";
				$db->query($sql);
			}else{
				$db->fetch();
				$cmbr_ix = $db->dt[cmbr_ix];
				$sql = "update shop_category_main_brand_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where cmbr_ix = '".$cmbr_ix."' and cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' and b_ix = '".$selected_result[$i+1]['brand'][$j]."' and relation_type = 'A'   ";
				$db->query($sql);
			}
		}
	
		$db->query("delete from shop_category_main_brand_relation where cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' and insert_yn = 'N' and relation_type = 'A'   ");
	//exit;
		/**
		* 노출 셀러 관련
		* 담 당  자 : shs 
		* 작업일시 : 2014년 03월 30일
		*/
		//$db->debug = true;
		$db->query("update shop_category_main_seller_relation set insert_yn = 'N'  where cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' ");
	
		for($j=0;$j < count($selected_result[$i+1]['seller']);$j++){
			$db->query("select cmsr_ix from shop_category_main_seller_relation where cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' and company_id = '".$selected_result[$i+1]['seller'][$j]."' ");
			
			if(!$db->total){
				$sql = "insert into shop_category_main_seller_relation (cmsr_ix,company_id,cmg_ix, group_code, vieworder, insert_yn, regdate) values ('','".$selected_result[$i+1]['seller'][$j]."','".$cmg_ix."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_CT_MAIN_SELLER_SEQ";
				$db->query($sql);
			}else{
				$db->fetch();
				$cmsr_ix = $db->dt[cmsr_ix];
				$sql = "update shop_category_main_seller_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where cmsr_ix = '".$cmsr_ix."' and  cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' and company_id = '".$selected_result[$i+1]['seller'][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_category_main_seller_relation where cmg_ix = '".$cmg_ix."' and group_code = '".($i+1)."' and insert_yn = 'N' ");

	}


	$db->query("delete from shop_category_main_product_relation where insert_yn = 'N' and cmg_ix = '".$cmg_ix."' ");
	$db->query("delete from shop_category_main_product_group where insert_yn = 'N' and cmg_ix = '".$cmg_ix."' ");

	if($delete_cache == "Y" && $display_cid){
		include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php");
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cache/";

		$tpl->clearCache($display_cid);
	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('분류별 메인 정시상품이 정상적으로 처리 되었습니다.');parent.document.location.reload();;</script>";
	//echo("<script>top.location.href = 'category_main_goods.php??mmode=$mmode&cmg_ix=$cmg_ix';</script>");

} else if ($act == "delete"){
	$sql="SELECT group_code FROM shop_category_main_product_group WHERE cmg_ix='".$cmg_ix."' ";
	$db->query($sql);
	$group_cnt=$db->total;
	if($group_cnt>0) {
		$group_fetch=$db->fetchall();
		for($i=0;$i<$group_cnt;$i++) {
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/cate_mg_".$cmg_ix."_".$group_fetch[$i]["group_code"].".gif")) {
				chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/cate_mg_".$cmg_ix."_".$group_fetch[$i]["group_code"].".gif", 0777);
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category_main/cate_mg_".$cmg_ix."_".$group_fetch[$i]["group_code"].".gif");
			}
		}
	}
	$db->query("DELETE FROM shop_category_main_goods WHERE cmg_ix='".$cmg_ix."' ");
	$db->query("delete from shop_category_main_product_relation where cmg_ix = '".$cmg_ix."' ");
	$db->query("delete from shop_category_main_product_group where cmg_ix = '".$cmg_ix."' ");
	
	if($agent_type == "M"){
		echo("<script>top.location.href = '/admin/mShop/category_main.list.php?mmode=$mmode';</script>");
	}else{
		echo("<script>top.location.href = '/admin/display/category_main.list.php?mmode=$mmode';</script>");
	}
}

?>