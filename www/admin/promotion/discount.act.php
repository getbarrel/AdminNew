<?
/*
메인프로모션 분류 추가로 인해 이미지명이 중복되어 저장되므로 분류 코드를 이미지명 앞에 붙여서 중복 저장을 막음(기존에 쓰던 방식은 그대로 유지) kbk 13/05/09
*/
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");

$db = new Database;

if ($act == "vieworder_update"){
	//$db->query("update shop_discount_product_relation set vieworder=1 ");
	//$_erpid = str_replace("\\\"","\"",$_POST["erpid"]);
	//echo $_erpid;
	//echo "bbb:".count(unserialize($_POST["erpid"]))."<br>";
	//print_r(unserialize($_POST["erpid"]));
	//$_erpid = unserialize(urldecode($_erpid));
	//$erpid = unserialize(urldecode($erpid));
	//echo count($_erpid);
	for($i=0;$i < count($sortlist);$i++){
		$sql = "update shop_discount_product_relation set
			vieworder='".($i+1)."'
			where  pid='".$sortlist[$i]."' ";//main_ix='$main_ix' and

		//echo $sql;
		$db->query($sql);
	}

}

if($act == "getDiscountInfo"){

			$sql = "select d.dc_ix, d.discount_sale_title
			from shop_discount d  
			where   d.week_no_".date("N")." = '1' and d.is_use = '1'
			and ".time()." between discount_use_sdate and discount_use_edate
			and ((use_time = 1 and ".date("h")." between start_time and end_time and ".date("i")." between start_min and end_min) or use_time = 0 ) ";
 

	//echo nl2br($sql);
	$db->query($sql);
	$plan_discount_info = $db->fetchall();
	echo json_encode($plan_discount_info);
	exit;
}

if($act == "getDiscountGroupInfo"){
	$sql = "select d.dc_ix, d.discount_sale_title, d.discount_type, dpg.group_code, dpg.dpg_ix, dpg.commission, dpg.group_name, dpg.headoffice_rate, dpg.seller_rate, dpg.sale_rate, dpg.goods_display_type, dpg.display_auto_sub_type, 
			case when dpg.goods_display_type = 'A'  then
				group_concat(ddr.r_ix SEPARATOR ',') 
			when dpg.goods_display_type = 'M'  then
				(select group_concat(pid SEPARATOR ',') from shop_discount_product_relation dpr where dpr.dc_ix = d.dc_ix and dpr.group_code = dpg.group_code)
			else '' end r_ix
			from shop_discount d 
			right join shop_discount_product_group dpg on d.dc_ix = dpg.dc_ix  
			left join shop_discount_display_relation ddr on dpg.dc_ix = ddr.dc_ix and ddr.relation_type = dpg.display_auto_sub_type
			where dpg.is_display = 'Y'  
			and d.week_no_".date("N")." = '1' and d.is_use = '1'
			and ".time()." between discount_use_sdate and discount_use_edate
			and ((use_time = 1 and ".date("h")." between start_time and end_time and ".date("i")." between start_min and end_min) or use_time = 0 )
			and d.dc_ix = '".$dc_ix."'
			group by dpg.dpg_ix";

	$db->query($sql);
	$plan_discount_info = $db->fetchall();
	/*
	if(is_array($plan_discount_info)){
		$sql = "";
		
		for($i=0;$i < count($plan_discount_info);$i++){
			if($plan_discount_info[$i][goods_display_type] == "M"){
			// 상품전시 타입이 수동일때 
			$detail_sql = "select '".$plan_discount_info[$i][discount_type]."' as discount_type, dpg.dpg_ix, p.id as pid , p.sellprice ,  dpg.commission, dpg.headoffice_rate, dpg.seller_rate, dpg.sale_rate, dpg.discount_sale_type, dpg.round_position, dpg.round_type
						from shop_discount_product_group dpg , shop_discount_product_relation dpr ,   shop_product p
						where dpg.dc_ix = dpr.dc_ix and dpg.group_code = dpr.group_code and dpr.pid = p.id
						and dpg.dpg_ix = '".$plan_discount_info[$i][dpg_ix]."'
						 ";
			}else if($plan_discount_info[$i][goods_display_type] == "A" && $plan_discount_info[$i][display_auto_sub_type] == "C"){
			// 상품전시 타입이 자동일때 자동타입이 카테고리 일때 
			//$category_sql = "select cid, depth from shop_category_info ci , shop_discount_display_relation ddr where ci.cid = ddr.r_ix and ddr.relation_type = 'C' and dc_ix = '".$plan_discount_info[$i][dc_ix]."' and ddr.group_code = '".$plan_discount_info[$i][group_code]."' ";
			$category_sql = "select cid, depth from shop_category_info ci  where cid = '".$plan_discount_info[$i][r_ix]."' ";

			$db->query($category_sql);
			$db->fetch();
			$cid = $db->dt[cid];
			$depth = $db->dt[depth];
			//echo $category_sql;
			$detail_sql = "select '".$plan_discount_info[$i][discount_type]."' as discount_type,dpg.dpg_ix, p.id as pid , p.sellprice ,  dpg.commission, dpg.headoffice_rate, dpg.seller_rate, dpg.sale_rate, dpg.discount_sale_type, dpg.round_position, dpg.round_type
						from shop_discount_product_group dpg , shop_discount_display_relation ddr , shop_product_relation pr,   shop_product p
						where dpg.dc_ix = ddr.dc_ix 
						and relation_type = 'C' and substr(ddr.r_ix,0,".(($depth+1)*3).") = substr(pr.cid,0,".(($depth+1)*3).") and pr.pid = p.id 
						and dpg.dpg_ix = '".$plan_discount_info[$i][dpg_ix]."' and pr.cid LIKE '".(substr($cid,0,($depth+1)*3))."%'
						 ";
			}else if($plan_discount_info[$i][goods_display_type] == "A" && $plan_discount_info[$i][display_auto_sub_type] == "S"){
			// 상품전시 타입이 자동일때 자동타입이 셀러 일때 
			$detail_sql = "select '".$plan_discount_info[$i][discount_type]."' as discount_type,dpg.dpg_ix, p.id as pid , p.sellprice ,  dpg.commission, dpg.headoffice_rate, dpg.seller_rate, dpg.sale_rate, dpg.discount_sale_type, dpg.round_position, dpg.round_type
						from shop_discount_product_group dpg , shop_discount_display_relation ddr ,   shop_product p
						where dpg.dc_ix = ddr.dc_ix 
						and relation_type = 'S' and ddr.r_ix = p.admin
						and dpg.dpg_ix = '".$plan_discount_info[$i][dpg_ix]."'
						 ";
			}else if($plan_discount_info[$i][goods_display_type] == "A" && $plan_discount_info[$i][display_auto_sub_type] == "B"){
			// 상품전시 타입이 자동일때 자동타입이 브랜드 일때 
			$detail_sql = "select '".$plan_discount_info[$i][discount_type]."' as discount_type, dpg.dpg_ix, p.id as pid , p.sellprice ,  dpg.commission, dpg.headoffice_rate, dpg.seller_rate, dpg.sale_rate, dpg.discount_sale_type, dpg.round_position, dpg.round_type
						from shop_discount_product_group dpg , shop_discount_display_relation ddr ,   shop_product p
						where dpg.dc_ix = ddr.dc_ix 
						and relation_type = 'B' and ddr.r_ix = p.brand
						and dpg.dpg_ix = '".$plan_discount_info[$i][dpg_ix]."'
						 ";
			}
			 
			
			//echo "<br><br>";
		}
	}
	*/
	echo json_encode($plan_discount_info);
	exit;
}

if ($_POST["act"] == "update" || $_POST["act"] == "insert"){
//print_r($_POST);
//exit;
 
	$sql = "SELECT discount_use_sdate , discount_use_edate FROM shop_discount where dc_ix ='".$_POST["dc_ix"]."' ";
	$db->query($sql); //AND cid='$cid'
	$db->fetch();
	$before_discount_use_sdate = $db->dt[discount_use_sdate];
	$before_discount_use_edate = $db->dt[discount_use_edate];


	$discount_use_sdate = mktime($_POST["discount_use_sdate_h"],$_POST["discount_use_sdate_i"],$_POST["discount_use_sdate_s"],substr($_POST["discount_use_sdate"],5,2),substr($_POST["discount_use_sdate"],8,2),substr($_POST["discount_use_sdate"],0,4));
	$discount_use_edate = mktime($_POST["discount_use_edate_h"],$_POST["discount_use_edate_i"],$_POST["discount_use_edate_s"],substr($_POST["discount_use_edate"],5,2),substr($_POST["discount_use_edate"],8,2),substr($_POST["discount_use_edate"],0,4));
		//echo $discount_use_edate_h.":".$discount_use_edate_i.":".$discount_use_edate_s;
		//exit;
	//print_r($_POST); 
	//exit;
	//$db->debug = true;
	if(!$discount_type){
		$discount_type = "GP";
	}

	if(is_array($_POST["week_no"])){
		foreach($_POST["week_no"] as $key => $value){
			//eval("\$week_no_".$key." = 0;");
			eval("\$week_no_".$key." = \$value;");
		}
	}

	if($db->total){	

		$sql = "update shop_discount set
					mall_ix='".$_POST["mall_ix"]."',
					discount_sale_title='".$_POST["discount_sale_title"]."',
					discount_use_sdate='".$discount_use_sdate."',
					discount_use_edate='".$discount_use_edate."',
					week_no_1='".$week_no_1."',
					week_no_2='".$week_no_2."',
					week_no_3='".$week_no_3."',
					week_no_4='".$week_no_4."',
					week_no_5='".$week_no_5."',
					week_no_6='".$week_no_6."',
					week_no_7='".$week_no_7."',					
					use_time='".$_POST["use_time"]."',
					start_time='".$_POST["start_time"]."',
					start_min='".$_POST["start_min"]."',
					end_time='".$_POST["end_time"]."',
					end_min='".$_POST["end_min"]."',
					member_target='".$_POST["member_target"]."',
					md_mem_ix='".$_POST["md_code"]."',
					seller_ix='".$_POST["company_id"]."',
					is_use='".$_POST["is_use"]."',
					editdate=NOW()
					where dc_ix='".$_POST["dc_ix"]."' ";
		
		//echo nl2br($sql);
		//exit;
		$db->query($sql);

	}else{
		  
			
			$sql = "insert into shop_discount
						(dc_ix,mall_ix,discount_type, discount_sale_title,discount_use_sdate,discount_use_edate, week_no_1, week_no_2, week_no_3, week_no_4, week_no_5, week_no_6, week_no_7, use_time, start_time, start_min, end_time, end_min, member_target,md_mem_ix,seller_ix,is_use,editdate,regdate) 
						values
						('','".$_POST["mall_ix"]."','".$discount_type."','".$_POST["discount_sale_title"]."','".$discount_use_sdate."','".$discount_use_edate."','".$week_no_1."','".$week_no_2."','".$week_no_3."','".$week_no_4."','".$week_no_5."','".$week_no_6."','".$week_no_7."','".$_POST["use_time"]."','".$_POST["start_time"]."','".$_POST["start_min"]."','".$_POST["end_time"]."','".$_POST["end_min"]."','".$_POST["member_target"]."','".$_POST["md_code"]."','".$_POST["company_id"]."','".$_POST["is_use"]."',NOW(),NOW())";

			$db->sequences = "SHOP_CT_MAIN_GOODS_SEQ";
			$db->query($sql);

			if($db->dbms_type == "oracle"){
				$dc_ix = $db->last_insert_id;
			}else{
				$db->query("SELECT dc_ix FROM shop_discount WHERE dc_ix=LAST_INSERT_ID() ");
				$db->fetch();
				$dc_ix = $db->dt[dc_ix];
			}
		
	}


	

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	if ($mg_title_img_del == "Y")
	{
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/main_title_img_".$dc_ix.".jpg");
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/main_title_img_".$dc_ix.".jpg");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	}

	if ($_FILES["mg_title_img"]["size"] > 0)
	{
		copy($_FILES["mg_title_img"][tmp_name], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/main_title_img_".$dc_ix.".jpg");		
	}
	

	$relation_type = $member_target;
	if($relation_type == "G" || $relation_type == "M"){
			if($relation_type == "G"){
				$relation_type_str = "group";	
			}else if($relation_type == "M"){
				$relation_type_str = "member";
			}


			$db->query("update shop_discount_display_relation set insert_yn = 'N'  where relation_type = '".$relation_type."' and dc_ix = '".$dc_ix."'  ");

			for($j=0;$j < count($selected_result[$relation_type_str]);$j++){
				$db->query("select ddr_ix from shop_discount_display_relation where relation_type = '".$relation_type."' and r_ix = '".$selected_result[$relation_type_str][$j]."' and dc_ix = '".$dc_ix."'  ");

				if(!$db->total){
					$sql = "insert into shop_discount_display_relation 
								(ddr_ix,dc_ix , r_ix,relation_type,  vieworder, insert_yn, regdate) 
								values 
								('','".$dc_ix."','".$selected_result[$relation_type_str][$j]."','".$relation_type."','".($j+1)."','Y', NOW())";
					$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
					$db->query($sql);
				}else{
					$sql = "update shop_discount_display_relation set insert_yn = 'Y',vieworder='".($j+1)."', relation_type = '".$relation_type."' 
								where relation_type = '".$relation_type."' and r_ix = '".$selected_result[$relation_type_str][$j]."' and dc_ix = '".$dc_ix."'  ";
					$db->query($sql);
				}
			}

			$db->query("delete from shop_discount_display_relation where relation_type = '".$relation_type."' and insert_yn = 'N' and dc_ix = '".$dc_ix."'  ");
	}

	$db->query("update shop_discount_product_group set insert_yn = 'N'  where  dc_ix = '".$dc_ix."'  ");
	//echo count($_POST["discount_group"]);
	for($i=0;$i < count($_POST["discount_group"]);$i++){
		
		$discount_group_info = $_POST["discount_group"][($i+1)];
		//print_r($discount_group_info);

		$db->query("Select dpg_ix from shop_discount_product_group where dc_ix = '".$dc_ix."' and group_code = '".($i+1)."' ");

		if($discount_type == "SP"){
			$sale_rate = $discount_group_info["headoffice_rate"];
		}else{
			$sale_rate = $discount_group_info["headoffice_rate"];
		}

		if($goods_display_type == "M" || $goods_display_type == "ME"){
			$display_auto_sub_type = "P";
		}else{
            $discount_group_info["display_auto_sub_type"] = 'C';
			$display_auto_sub_type = $discount_group_info["display_auto_sub_type"];
		}
		

		if($db->total){
			$db->fetch();
			$dpg_ix = $db->dt[dpg_ix];
		 		
			$sql = "update shop_discount_product_group set						
						group_name='".$discount_group_info["group_name"]."',
						group_code='".($i+1)."',
						commission ='".$discount_group_info["commission"]."',
						headoffice_rate ='".$discount_group_info["headoffice_rate"]."',
						seller_rate='".$discount_group_info["seller_rate"]."',
						sale_rate='".$sale_rate."',
						goods_display_type='".$discount_group_info["goods_display_type"]."',
						display_auto_sub_type='".$display_auto_sub_type."',
						discount_sale_type='".$discount_group_info["discount_sale_type"]."',						
						round_position='".$discount_group_info["round_position"]."',
						round_type='".$discount_group_info["round_type"]."',
						insert_yn='Y',
						is_display='".$discount_group_info["is_display"]."',
						coupon_use_yn='".$discount_group_info["coupon_use_yn"]."',
						editdate=NOW()
						where dpg_ix='".$dpg_ix."'  ";
 
			$db->query($sql);

			

		}else{ 
			$sql = "insert into shop_discount_product_group
						(dpg_ix,dc_ix, group_name,group_code,commission, headoffice_rate,seller_rate,sale_rate,goods_display_type, display_auto_sub_type, discount_sale_type, round_position, round_type, insert_yn,is_display,coupon_use_yn,editdate, regdate)
						values
						('','".$dc_ix."','".$discount_group_info["group_name"]."','".($i+1)."','".$discount_group_info["commission"]."','".$discount_group_info["headoffice_rate"]."','".$discount_group_info["seller_rate"]."','".$sale_rate."','".$discount_group_info["goods_display_type"]."','".$display_auto_sub_type."','".$discount_group_info["discount_sale_type"]."','".$discount_group_info["round_position"]."','".$discount_group_info["round_type"]."', 'Y','".$discount_group_info["is_display"]."','".$discount_group_info["coupon_use_yn"]."',NOW(),NOW()) ";

			$db->sequences = "SHOP_discount_PRODUCT_GROUP_SEQ";
			$db->query($sql);

			$db->query("SELECT dpg_ix FROM  shop_discount_product_group  WHERE dpg_ix=LAST_INSERT_ID()");
			$db->fetch();
			$dpg_ix = $db->dt[dpg_ix];
		}

		//$db->debug = true;
		//print_r($_POST);
		//exit;
		 
		//$db->debug = false;
		//exit;

		if ($group_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/discount_group".($i+1).".gif");
			@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/".$dc_ix."_discount_group".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}



		//echo "size:".$_FILES["group_img"]["size"][$i+1]."<br />";
		//print_r($_FILES);
		//exit;

		if ($_FILES["group_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/discount_group".($i+1).".gif");
			@copy($_FILES["group_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/".$dc_ix."_discount_group".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		if ($group_banner_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/discount_groupbanner_".($i+1).".gif");
			@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/".$dc_ix."_discount_groupbanner_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		if ($_FILES["group_banner_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_banner_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/discount_groupbanner_".($i+1).".gif");
			@copy($_FILES["group_banner_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/".$dc_ix."_discount_groupbanner_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}


		if($discount_group_info["goods_display_type"] == "C" || $discount_group_info["goods_display_type"] == "CE"){

            $discount_group_info["display_auto_sub_type"] = 'C';
			if($discount_group_info["display_auto_sub_type"] == "C"){
				$sub_type = "category";	
			}else if($discount_group_info["display_auto_sub_type"] == "B"){
				$sub_type = "brand";
			}else if($discount_group_info["display_auto_sub_type"] == "S"){
				$sub_type = "seller";
			}
			//$db->debug = true;

			//$sql = "update shop_discount_display_relation set insert_yn = 'N'  where relation_type = '".$discount_group_info["display_auto_sub_type"]."' and dc_ix = '".$dc_ix."' and group_code = '".($i+1)."'  "; 2014-08-25 기존쿼리 

			//category code로 등록되엇다가 브랜드로 등록시 기존 카테고리 데이타가 삭제 안되는 문제점 2014-08-25 이학봉 relation_type 조건땜에 기존 C조건으로 된것은 삭제가 안됨 
			$sql = "update shop_discount_display_relation set insert_yn = 'N'  where dc_ix = '".$dc_ix."' and group_code = '".($i+1)."'  ";
			$db->query($sql);

			for($j=0;$j < count($discount_group_info[$sub_type]);$j++){
				$db->query("select ddr_ix from shop_discount_display_relation where relation_type = '".$discount_group_info["display_auto_sub_type"]."' and r_ix = '".$discount_group_info[$sub_type][$j]."' and dc_ix = '".$dc_ix."'  and group_code = '".($i+1)."'  ");

				if(!$db->total){
					$sql = "insert into shop_discount_display_relation 
								(ddr_ix,dc_ix , group_code, r_ix,relation_type,  vieworder, insert_yn, regdate) 
								values 
								('','".$dc_ix."','".($i+1)."','".$discount_group_info[$sub_type][$j]."','".$discount_group_info["display_auto_sub_type"]."','".($j+1)."','Y', NOW())";
					$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
					$db->query($sql);
				}else{
					$sql = "update shop_discount_display_relation set   insert_yn = 'Y',vieworder='".($j+1)."', relation_type = '".$discount_group_info["display_auto_sub_type"]."' 
								where relation_type = '".$discount_group_info["display_auto_sub_type"]."' and r_ix = '".$discount_group_info[$sub_type][$j]."' and dc_ix = '".$dc_ix."' and group_code = '".($i+1)."' ";
					$db->query($sql);
				}
			}
			
			$sql = "delete from shop_discount_display_relation where insert_yn = 'N' and dc_ix = '".$dc_ix."' and group_code = '".($i+1)."'  ";
			$db->query($sql);
			//$db->debug = false;
		}else{
			//수동등록으로 들어오는 그룹별 코드로 dispplay_relation 삭제 2014-08-25 이학봉
			$sql = "delete from shop_discount_display_relation where dc_ix = '".$dc_ix."' and group_code = '".($i+1)."'  ";
			$db->query($sql);
		
		}
		
		//$db->query("SELECT dpg_ix FROM ".tbl_shop_discount_product_group." WHERE dpg_ix=LAST_INSERT_ID()");
		//$db->fetch();
		//$dpg_ix = $db->dt[0];

		//$db->query("update shop_discount_product_relation set insert_yn = 'N' where main_ix='".$main_ix."' and group_code = '".($i+1)."' ");
		$sql = "update shop_discount_product_relation set insert_yn='N' where dc_ix = '".$dc_ix."' and group_code = '".($i+1)."'  ";
		$db->query($sql);

		for($j=0;$j < count($rpid[$i+1]);$j++){
			$db->query("select dpr_ix from shop_discount_product_relation where dc_ix = '".$dc_ix."' and group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' ");
			
			if($discount_group[($i+1)][goods_display_type] == 'M' || $discount_group[($i+1)][goods_display_type] == 'ME'){	//기존 수동등록 데이타를 불러와서 처리하는데 자동등록으로 바꿧을경우에도 기존 수동등록 데이타가 그대로 다시 입력됨
				//그룹별 할인상품 타입이 수동등록일경우에만 처리하도록 수정 2014-08-25 이학봉 
				if(!$db->total){
					$sql = "insert into shop_discount_product_relation (dpr_ix,pid,dc_ix, group_code, vieworder, insert_yn, regdate) values ('','".$rpid[$i+1][$j]."','".$dc_ix."','".($i+1)."','".($j+1)."','Y', NOW())";
					$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
					$db->query($sql);
					
					if($discount_type == "SP"){
						$db->query("update shop_product_addinfo set special_discount_sdate = '".$discount_use_sdate."', special_discount_edate = '".$discount_use_edate."' , special_discount_yn = '1' where pid = '".$rpid[$i+1][$j]."' ");
					}else{
						$db->query("update shop_product_addinfo set discount_sdate = '".$discount_use_sdate."', discount_edate = '".$discount_use_edate."' , discount_yn = '1' where pid = '".$rpid[$i+1][$j]."' ");
					}

				}else{
					$sql = "update shop_discount_product_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where dc_ix = '".$dc_ix."' and group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' ";
					$db->query($sql);

					if($before_discount_use_sdate != $discount_use_sdate || $before_discount_use_edate != $discount_use_edate){
						if($discount_type == "SP"){
							//if($discount_use_sdate <= time()  && time() <= $discount_use_edate){
							$db->query("update shop_product_addinfo set special_discount_sdate = '".$discount_use_sdate."', special_discount_edate = '".$discount_use_edate."' , special_discount_yn = '1' where pid = '".$rpid[$i+1][$j]."' ");
						}else{
							$db->query("update shop_product_addinfo set discount_sdate = '".$discount_use_sdate."', discount_edate = '".$discount_use_edate."' , discount_yn = '1' where pid = '".$rpid[$i+1][$j]."' ");
						}
					}
				}
			}
		}

		$db->query("select pid from shop_discount_product_relation where dc_ix = '".$dc_ix."' and group_code = '".($i+1)."' and insert_yn = 'N'  ");
		$change_addinfo = $db->fetchall();
		for($j=0;$j < count($change_addinfo);$j++){
				if($discount_type == "SP"){
					$db->query("update shop_product_addinfo set special_discount_sdate = '0', special_discount_edate = '0' , special_discount_yn = '0' where pid = '".$change_addinfo[$j][pid]."' ");
				}else{
					$db->query("update shop_product_addinfo set discount_sdate = '0', discount_edate = '0' , discount_yn = '0' where pid = '".$change_addinfo[$j][pid]."' ");
				}
		}

		$db->query("delete from shop_discount_product_relation where dc_ix = '".$dc_ix."' and group_code = '".($i+1)."' and insert_yn = 'N' ");
	//	exit;
	}

 
	$db->query("delete from shop_discount_product_relation where dc_ix = '".$dc_ix."' and insert_yn = 'N' ");
	$db->query("delete from shop_discount_product_group where dc_ix = '".$dc_ix."' and insert_yn = 'N' ");
	//print_r($_POST);
	//exit;
	

	//$data = urlencode(serialize($_POST));
	//print_r($_POST);
	//echo $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	/*
	$shmop = new Shared("category_discount_info");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$shmop->setObjectForKey($category_discount_info,"category_discount_info");
*/


	if($delete_cache == "Y"){
		include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php");
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cache/";

		$tpl->clearCache('000000000000000');
	}
//exit;
	if ($_POST["act"] == "update"){
		echo("<script>parent.location.reload();</script>");
	}else if($_POST["act"] == "insert"){
		if($discount_type == "SP"){
			echo("<script>parent.location.href = 'special_discount.php?dc_ix=$dc_ix';</script>");
		}elseif($discount_type == "M"){
			echo("<script>parent.location.href = '../mShop/mobile_discount.php?dc_ix=$dc_ix';</script>");
		}else{
			echo("<script>parent.location.href = 'discount.php?dc_ix=$dc_ix';</script>");
		}
	}
	

} else if ($act == "delete"){//삭제 추가 kbk 13/11/21

	$db->query("select discount_type from shop_discount where dc_ix='".$dc_ix."' ");
	$db->fetch();
	$discount_type = $db->dt[discount_type];
	$sql="SELECT group_code FROM shop_discount_product_group WHERE dc_ix='".$dc_ix."' ";
	$db->query($sql);
	$group_cnt=$db->total;
	if($group_cnt>0) {
		$group_fetch=$db->fetchall();
		for($i=0;$i<$group_cnt;$i++) {
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/discount_group".($i+1).".gif")) {
				chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/discount_group".($i+1).".gif", 0777);
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/discount/discount_group".($i+1).".gif");
			}
		}
	}
	$db->query("delete from shop_discount where dc_ix='".$dc_ix."' ");
	$db->query("delete from shop_discount_product_relation where dc_ix = '".$dc_ix."' ");
	$db->query("delete from shop_discount_product_group where dc_ix = '".$dc_ix."' ");
	$db->query("delete from shop_discount_display_relation where dc_ix = '".$dc_ix."' ");


	if($discount_type == "SP"){
		echo("<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('특별할인 정보가 정삭적으로 삭제 되었습니다.');parent.document.location.href = 'special_discount_list.php';</script>");
	}elseif($discount_type == "M"){
		echo("<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('모바일할인 정보가 정삭적으로 삭제 되었습니다.');parent.document.location.href = '../mShop/mobile_discount_list.php';</script>");
	}else{
		echo("<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('기획할인 정보가 정삭적으로 삭제 되었습니다.');parent.document.location.href = 'discount_list.php';</script>");
	}
	
	exit;
}else if($act == "relation_delete"){
	$db->query("delete from shop_discount_product_relation where dc_ix = '".$dc_ix."' and pid='".$pid."'");
	echo("<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('기획할인 정보가 정삭적으로 삭제 되었습니다.');parent.document.location.reload();</script>");
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