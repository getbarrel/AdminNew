<?
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

/////////////////////////////////////////////////////////////////////////////////////////////
$refererParts = explode('/', $_SERVER['HTTP_REFERER']);

if($refererParts[3] != 'admin'){
    echo "<script>
			 alert('비정상적인 접근입니다.');
		  </script>";
    exit;
}

valCsrf();

/////////////////////////////////////////////////////////////////////////////////////////////

/*
print_r($_POST);
exit;
*/
/*
쿠폰 발급할 때

	if($db->dt[use_date_type] == 1){
		if($db->dt[publish_date_type] == 1){
			$publish_year = date("Y") + $db->dt[publish_date_differ];
		}else{
			$publish_year = date("Y");
		}
		if($db->dt[publish_date_type] == 2){
			$publish_month = date("m") + $db->dt[publish_date_differ];
		}else{
			$publish_month = date("m");
		}
		if($db->dt[publish_date_type] == 3){
			$publish_day = date("d") + $db->dt[publish_date_differ];
		}else{
			$publish_day = date("d");
		}
		$use_sdate = time();
		$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);

		//$event_date = mktime(0,0,0,$event_meonth,$event_day+$order[end_date_differ],$evet_year);

	}
---> 이것을
	$p_year=substr($db->dt["use_sdate"],0,4);
	$p_month=substr($db->dt["use_sdate"],4,2);
	$p_day=substr($db->dt["use_sdate"],6,2);

	if($db->dt[use_date_type] == 1){
		if($db->dt[publish_date_type] == 1){
			$publish_year = $p_year + $db->dt[publish_date_differ];
		}else{
			$publish_year = $p_year;
		}
		if($db->dt[publish_date_type] == 2){
			$publish_month = $p_month + $db->dt[publish_date_differ];
		}else{
			$publish_month = $p_month;
		}
		if($db->dt[publish_date_type] == 3){
			$publish_day = $p_day + $db->dt[publish_date_differ];
		}else{
			$publish_day = $p_day;
		}
		$use_sdate = time();
		$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);

		//$event_date = mktime(0,0,0,$event_meonth,$event_day+$order[end_date_differ],$evet_year);

	}
로 변경함 kbk 12/02/16

*/
$db = new Database();

if(!function_exists("GetCuponNo")){ 
	function GetCuponNo(){
		$mdb = new Database();
		$cupon_no =  "MSCP".date("ymd").rand(1000000,9999999);

		$mdb->query("select cupon_no from ".TBL_SHOP_CUPON_PUBLISH." where cupon_no = '$cupon_no'");

		If($mdb->total){
			GetCuponNo();
		}else{
			return $cupon_no;
		}
	}
}


if($act == "get_coupon"){
	$sql = "Select c.*
				from ".TBL_SHOP_CUPON." c
				where cupon_div = '".$cupon_div."'
				order by regdate desc  ";
	//echo nl2br($sql);
	$db->query($sql);
	$coupons = $db->fetchall();

	echo json_encode($coupons);
	exit;
}

if($act == "publish"){
	$cupon_no =  GetCuponNo();

	if($use_date_type == 1){ //발행일로부터(등록하기 떄문에아래 프로세스 맞음)
		if($publish_date_type == 1){
			$publish_year = date("Y") + $publish_date_differ;
		}else{
			$publish_year = date("Y");
		}
		if($publish_date_type == 2){
			$publish_month = date("m") + $publish_date_differ;
		}else{
			$publish_month = date("m");
		}
		if($publish_date_type == 3){
			$publish_day = date("d") + $publish_date_differ;
		}else{
			$publish_day = date("d");
		}

		//$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);
		$use_sdate=date("Ymd");
		$use_edate = date("Ymd",mktime(0,0,0,$publish_month,$publish_day,$publish_year));
        if($db->dbms_type == "oracle"){
        //TO_DATE('10-04-2010 20:37:50','MM-DD-YYYY HH24:MI:SS')
            $use_sdate = date("m-d-Y H:i:s");
            $use_edate = date("m-d-Y H:i:s",mktime(0,0,0,$publish_month,$publish_day,$publish_year));
        }
		//$event_date = mktime(0,0,0,$event_meonth,$event_day+$order[end_date_differ],$evet_year);

	}else if($use_date_type == 2){ //발급일로부터
		if($regist_date_type == 1){
			$regist_year = date("Y") + $regist_date_differ;
		}else{
			$regist_year = date("Y");
		}
		if($regist_date_type == 2){
			$regist_month = date("m") + $regist_date_differ;
		}else{
			$regist_month = date("m");
		}
		if($regist_date_type == 3){
			$regist_day = date("d") + $regist_date_differ;
		}else{
			$regist_day = date("d");
		}

		//$use_date_limit = mktime(0,0,0,$regist_month,$regist_day,$regist_year);
		$use_sdate=date("Ymd");
		$use_edate = date("Ymd",mktime(0,0,0,$regist_month,$regist_day,$regist_year));
        if($db->dbms_type == "oracle"){
        //TO_DATE('10-04-2010 20:37:50','MM-DD-YYYY HH24:MI:SS')
            $use_sdate = date("m-d-Y H:i:s");
            $use_edate = date("m-d-Y H:i:s",mktime(0,0,0,$publish_month,$publish_day,$publish_year));
        }
	}else if($use_date_type == 3){ //사용기간 지정
		//$use_sdate = $FromYY.$FromMM.$FromDD;
		//$use_edate = $ToYY.$ToMM.$ToDD;
        if($db->dbms_type == "oracle"){
            $use_sdate_time = $use_sdate." ".$use_sdate_h.":".$use_sdate_i.":".$use_sdate_s;
            $use_edate_time = $use_edate." ".$use_edate_h.":".$use_edate_i.":".$use_edate_s;
        }else{
			$use_sdate_time = $use_sdate." ".$use_sdate_h.":".$use_sdate_i.":".$use_sdate_s;
            $use_edate_time = $use_edate." ".$use_edate_h.":".$use_edate_i.":".$use_edate_s;
		}
		
		$use_sdate=str_replace("-","",$use_sdate);
		$use_edate=str_replace("-","",$use_edate);

	}else { //기간 사용하지 않음 -> 현재날짜 넣음.
		$use_sdate=date("Ymd");
        if($db->dbms_type == "oracle"){
        //TO_DATE('10-04-2010 20:37:50','MM-DD-YYYY HH24:MI:SS')
           $use_sdate = date("m-d-Y H:i:s");
        }
	}

	$cupon_use_sdate = mktime($_POST["cupon_use_sdate_h"], $_POST["cupon_use_sdate_i"], $_POST["cupon_use_sdate_s"], date("m",strtotime($_POST["cupon_use_sdate"])), date("d",strtotime($_POST["cupon_use_sdate"])), date("Y",strtotime($_POST["cupon_use_sdate"])));
	$cupon_use_edate = mktime($_POST["cupon_use_edate_h"], $_POST["cupon_use_edate_i"], $_POST["cupon_use_edate_s"], date("m",strtotime($_POST["cupon_use_edate"])), date("d",strtotime($_POST["cupon_use_edate"])), date("Y",strtotime($_POST["cupon_use_edate"])));

	/*
	$sql = "update shop_cupon_publish_tmp set
							cupon_ix='$cupon_ix',use_date_type='$use_date_type',use_sdate='$use_sdate_time',use_edate='$use_edate_time',use_product_type='$use_product_type', is_include='$is_include',  publish_date_differ='$publish_date_differ',publish_date_type='$publish_date_type',regist_date_differ='$regist_date_differ',regist_date_type='$regist_date_type',publish_condition_price='$publish_condition_price',publish_limit_price='$publish_limit_price',publish_type='$publish_type',mem_ix='$mem_ix', publish_name='$publish_name',disp='$disp',is_use='".$is_use."', cupon_use_sdate = '".$cupon_use_sdate."', cupon_use_edate = '".$cupon_use_edate."'
							where publish_tmp_ix='$publish_tmp_ix' ";
	*/
	if($view_type != "mem_group"){
		if($db->dbms_type == "oracle"){
			$sql = "insert into ".TBL_SHOP_CUPON_PUBLISH."
			   (publish_ix, mall_ix, cupon_div, cupon_ix,cupon_no, publish_name,use_date_type,use_sdate, use_edate, use_product_type , is_include, publish_date_differ,publish_date_type,regist_date_differ,regist_date_type,publish_condition_price,publish_limit_price,publish_type,mem_ix, disp, issue_type, buy_point, is_cs, is_use, cupon_use_sdate, cupon_use_edate, regdate)
			   values
			   ('','$mall_ix','$cupon_div','$cupon_ix','$cupon_no','$publish_name','$use_date_type',TO_DATE('$use_sdate_time','MM-DD-YYYY HH24:MI:SS'),TO_DATE('$use_edate_time','MM-DD-YYYY HH24:MI:SS'),'$use_product_type', '$is_include', '$publish_date_differ','$publish_date_type','$regist_date_differ','$regist_date_type','$publish_condition_price','$publish_limit_price','$publish_type','$mem_ix','$disp','$issue_type','$buy_point','$is_cs','$is_use','$cupon_use_sdate','$cupon_use_edate',NOW())";
		}else{
		   $sql = "insert into ".TBL_SHOP_CUPON_PUBLISH."
			   (publish_ix,mall_ix, cupon_div, cupon_ix,cupon_no, publish_name,use_date_type,use_sdate, use_edate, use_product_type, is_include , publish_date_differ,publish_date_type,regist_date_differ,regist_date_type,publish_condition_price,publish_limit_price,publish_type,mem_ix, disp, issue_type, buy_point, is_cs, is_use, cupon_use_sdate, cupon_use_edate, regdate)
			   values
			   ('','$mall_ix','$cupon_div','$cupon_ix','$cupon_no','$publish_name','$use_date_type','$use_sdate_time','$use_edate_time','$use_product_type', '$is_include',  '$publish_date_differ','$publish_date_type','$regist_date_differ','$regist_date_type','$publish_condition_price','$publish_limit_price','$publish_type','$mem_ix','$disp','$issue_type','$buy_point','$is_cs','$is_use','$cupon_use_sdate','$cupon_use_edate',NOW())";
		}
	}else{
		if($db->dbms_type == "oracle"){
			$sql = "insert into shop_cupon_publish_tmp
			   (publish_tmp_ix,mall_ix, cupon_div, cupon_ix, publish_name,use_date_type,use_sdate, use_edate, use_product_type, is_include , publish_date_differ,publish_date_type,regist_date_differ,regist_date_type,publish_condition_price,publish_limit_price,publish_type,mem_ix,disp, issue_type, is_cs, is_use, cupon_use_sdate, cupon_use_edate, regdate)
			   values
			   ('','$mall_ix','$cupon_div','$cupon_ix','$publish_name','$use_date_type',TO_DATE('$use_sdate_time','MM-DD-YYYY HH24:MI:SS'),TO_DATE('$use_edate_time','MM-DD-YYYY HH24:MI:SS'),'$use_product_type', '$is_include',  '$publish_date_differ','$publish_date_type','$regist_date_differ','$regist_date_type','$publish_condition_price','$publish_limit_price','$publish_type','$mem_ix','$disp','$issue_type','$is_cs','$is_use','$cupon_use_sdate','$cupon_use_edate',NOW())";
		}else{
		   $sql = "insert into shop_cupon_publish_tmp
			   (publish_tmp_ix,mall_ix, cupon_div, cupon_ix, publish_name,use_date_type,use_sdate, use_edate, use_product_type, is_include , publish_date_differ,publish_date_type,regist_date_differ,regist_date_type,publish_condition_price,publish_limit_price,publish_type,mem_ix,disp,issue_type, is_cs, is_use, cupon_use_sdate, cupon_use_edate, regdate)
			   values
			   ('','$mall_ix','$cupon_div','$cupon_ix','$publish_name','$use_date_type','$use_sdate_time','$use_edate_time','$use_product_type', '$is_include',  '$publish_date_differ','$publish_date_type','$regist_date_differ','$regist_date_type','$publish_condition_price','$publish_limit_price','$publish_type','$mem_ix','$disp','$issue_type','$is_cs','$is_use','$cupon_use_sdate','$cupon_use_edate',NOW())";
		}
	}
	$db->sequences = "SHOP_CUPON_PUBLISH_SEQ";

	$db->query($sql);
	
	if($view_type != "mem_group"){
		if($db->dbms_type == "oracle"){
			$publish_ix = $db->last_insert_id;
		}else{
			$db->query("Select publish_ix from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix = LAST_INSERT_ID()");
			$db->fetch();
			$publish_ix = $db->dt[publish_ix];
		}
	}else{
		if($db->dbms_type == "oracle"){
			$publish_tmp_ix = $db->last_insert_id;
		}else{
			$db->query("Select publish_tmp_ix from shop_cupon_publish_tmp where publish_tmp_ix = LAST_INSERT_ID()");
			$db->fetch();
			$publish_tmp_ix = $db->dt[publish_tmp_ix];
		}
	}

		if($use_product_type == "3"){
			if($db->dbms_type != "oracle"){
				if(!$db->mysql_table_exists("shop_cupon_relation_product")){
					$sql = "CREATE TABLE `shop_cupon_relation_product` (
					  `cpr_ix` int(8)  default NULL auto_increment,
					  `publish_ix` int(4) NOT NULL,
					  `pid` int(6) NOT NULL,
					  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
					  PRIMARY KEY  (`cpr_ix`)
					) TYPE=MyISAM COMMENT='쿠폰관련상품'  ;";

					$db->query($sql);
				}
			}
			//$db->query("delete from shop_cupon_relation_product where publish_ix='$publish_ix'");
			if($view_type != "mem_group"){
				for($i=0;$i < count($rpid[1]);$i++){
					$db->query("Select publish_ix from shop_cupon_relation_product where pid = '".$rpid[1][$i]."' and publish_ix = '".$publish_ix."' ");
					$db->fetch();
					if(!$db->total){
						$sql = "insert into shop_cupon_relation_product (cpr_ix,publish_ix,pid, regdate)
							values ('','".$publish_ix."','".$rpid[1][$i]."',NOW())";
						$db->sequences = "SHOP_CUPON_LINK_GOODS_SEQ";
						$db->query($sql);
					}
				}
				$db->query("delete from shop_cupon_relation_category where  publish_ix = '".$publish_ix."' ");
			}else{
				for($i=0;$i < count($rpid[1]);$i++){
					$db->query("Select publish_tmp_ix from shop_cupon_relation_product where pid = '".$rpid[1][$i]."' and publish_tmp_ix = '".$publish_tmp_ix."' ");
					$db->fetch();
					if(!$db->total){
						$sql = "insert into shop_cupon_relation_product (cpr_ix,publish_tmp_ix,pid, regdate)
							values ('','".$publish_tmp_ix."','".$rpid[1][$i]."',NOW())";
						$db->sequences = "SHOP_CUPON_LINK_GOODS_SEQ";
						$db->query($sql);
					}
				}
				$db->query("delete from shop_cupon_relation_category where  publish_tmp_ix = '".$publish_tmp_ix."' ");
			}
		}else if($use_product_type == "2"){
			if($db->dbms_type != "oracle"){
				if(!$db->mysql_table_exists("shop_cupon_relation_category")){
					$sql = "CREATE TABLE `shop_cupon_relation_category` (
					  `cpc_ix` int(8)  default NULL auto_increment,
					  `publish_ix` int(4) NOT NULL,
					  `cid` int(6) NOT NULL,
					  `depth` int(1) NOT NULL,
					  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
					  PRIMARY KEY  (`cpc_ix`)
						) TYPE=MyISAM COMMENT='쿠폰관련카테고리'  ;";

					$db->query($sql);
				}
			}
			if($view_type != "mem_group"){
				for($i=0;$i < count($category);$i++){
					$sql = "Select publish_ix from shop_cupon_relation_category where cid = '".$category[$i]."' and publish_ix = '".$publish_ix."' ";
					//echo $sql;
					$db->query($sql);
					$db->fetch();
					if(!$db->total){
						$sql = "insert into shop_cupon_relation_category (cpc_ix,publish_ix,cid, depth, regdate)
										values ('','".$publish_ix."','".$category[$i]."','".$depth[$i]."',NOW())";
						$db->sequences = "SHOP_CUPON_LINK_CT_SEQ";
					//	echo $sql;
						$db->query($sql);
					}
				}
				$db->query("delete from shop_cupon_relation_product where  publish_ix = '".$publish_ix."' ");
			}else{
				for($i=0;$i < count($category);$i++){
					$sql = "Select publish_tmp_ix from shop_cupon_relation_category where cid = '".$category[$i]."' and publish_tmp_ix = '".$publish_tmp_ix."' ";
					//echo $sql;
					$db->query($sql);
					$db->fetch();
					if(!$db->total){
						$sql = "insert into shop_cupon_relation_category (cpc_ix,publish_tmp_ix,cid, depth, regdate)
										values ('','".$publish_tmp_ix."','".$category[$i]."','".$depth[$i]."',NOW())";
						$db->sequences = "SHOP_CUPON_LINK_CT_SEQ";
					//	echo $sql;
						$db->query($sql);
					}
				}
				$db->query("delete from shop_cupon_relation_product where  publish_tmp_ix = '".$publish_tmp_ix."' ");
			}
		}else if($use_product_type == "4"){
			if($view_type != "mem_group"){
				for($i=0;$i < count($brand);$i++){
					$sql = "insert into shop_cupon_relation_brand (crb_ix,publish_ix,b_ix, regdate)
									values ('','".$publish_ix."','".$brand[$i]."',NOW())";
					$db->sequences = "SHOP_CUPON_LINK_BRAND_SEQ";
					$db->query($sql);
				}
			}else{
				for($i=0;$i < count($brand);$i++){
					$sql = "insert into shop_cupon_relation_brand (crb_ix,publish_tmp_ix,b_ix, regdate)
									values ('','".$publish_tmp_ix."','".$brand[$i]."',NOW())";
					$db->sequences = "SHOP_CUPON_LINK_BRAND_SEQ";
					$db->query($sql);
				}
			}
		}else if($use_product_type == "5"){
			if($view_type != "mem_group"){
				$db->query("update shop_cupon_relation_seller set insert_yn = 'N'  where publish_ix = '".$publish_ix."'  ");
			}else{
				$db->query("update shop_cupon_relation_seller set insert_yn = 'N'  where publish_tmp_ix = '".$publish_tmp_ix."'  ");
			}
		
			for($j=0;$j < count($seller);$j++){
				if($view_type != "mem_group"){
					$db->query("select crs_ix from shop_cupon_relation_seller where publish_ix = '".$publish_ix."'  and company_id = '".$seller[$j]."' ");
				}else{
					$db->query("select crs_ix from shop_cupon_relation_seller where publish_tmp_ix = '".$publish_tmp_ix."'  and company_id = '".$seller[$j]."' ");
				}
				
				if(!$db->total){
					if($view_type != "mem_group"){
						$sql = "insert into shop_cupon_relation_seller (crs_ix,company_id,publish_ix, vieworder, insert_yn, regdate) values ('','".$seller[$j]."','".$publish_ix."','".($j+1)."','Y', NOW())";
						$db->sequences = "SHOP_POPUP_SELLER_SEQ";
						$db->query($sql);
					}else{
						$sql = "insert into shop_cupon_relation_seller (crs_ix,company_id,publish_tmp_ix, vieworder, insert_yn, regdate) values ('','".$seller[$j]."','".$publish_tmp_ix."','".($j+1)."','Y', NOW())";
						$db->sequences = "SHOP_POPUP_SELLER_SEQ";
						$db->query($sql);
					}
				}else{
					$db->fetch();
					$crs_ix = $db->dt[crs_ix];
					if($view_type != "mem_group"){
						$sql = "update shop_cupon_relation_seller set insert_yn = 'Y',vieworder='".($j+1)."' where crs_ix = '".$crs_ix."' and  publish_ix = '".$publish_ix."'  and company_id = '".$seller[$j]."' ";
						$db->query($sql);
					}else{
						$sql = "update shop_cupon_relation_seller set insert_yn = 'Y',vieworder='".($j+1)."' where crs_ix = '".$crs_ix."' and  publish_tmp_ix = '".$publish_tmp_ix."'  and company_id = '".$seller[$j]."' ";
						$db->query($sql);
					}
				}
			}

			if($view_type != "mem_group"){
				$db->query("delete from shop_cupon_relation_seller where publish_ix = '".$publish_ix."'  and insert_yn = 'N' ");
			}else{
				$db->query("delete from shop_cupon_relation_seller where publish_tmp_ix = '".$publish_tmp_ix."'  and insert_yn = 'N' ");
			}

		}


		if($publish_type == "1" || $publish_type == "4"){
			//$pdr_div = "T";
			if($publish_type == 1){
				$publish_type_text = "member";
			}else if($publish_type == 4){
				$publish_type_text = "group";
			}
			//$db->debug = true;

			for($j=0;$j < count($selected_result[$publish_type_text]);$j++){
				$sql = "insert into shop_cupon_publish_config 
							(cpc_ix, publish_ix ,r_ix,publish_type, vieworder, insert_yn, regdate) 
							values 
							('','".$publish_ix."','".$selected_result[$publish_type_text][$j]."','".$publish_type."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_CUPON_PUBLISH_LINK_SEQ";
				$db->query($sql);
			}
		}
		
		if($publish_type == "4"){// 회원그룹 발행일경우
			for($i=0;$i < count($select_gp_list);$i++){
				$sql = "insert into shop_cupon_relation_group (crg_ix,publish_tmp_ix,gp_ix,regdate)
						values
						('','".$publish_tmp_ix."','".$select_gp_list[$i]."',NOW())";
				$db->query($sql);
				
			}

		}

		if($issue_type == "1"){

			if($publish_type=="1"){
				//고객지정 발행
				$use_sdate = date("Ymd",$use_sdate);

				if($use_date_type == '9'){	//무제한일경우 use_date_limit 컬럼에 지난 날짜가 들어가면서 프론토에서 쿠폰 노출이 안됨 2014-10-22 이학봉 
					$use_edate = "99991231";
				}else{
					$use_date_limit = date("Ymd",$use_date_limit);
				}

				$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST."  select '' as regist_ix , '".$publish_ix."' as publish_ix, cu.code,1,0,
								'$use_sdate','$use_edate',null,null, NOW() , null, null
								from ".TBL_COMMON_USER." cu WHERE cu.code in ('".implode("','",$selected_result["member"])."')
								 ";
								
				$db->sequences = "SHOP_CUPON_REGIST_SEQ";
				$db->query($sql2);

			}
		}
		
	if($mmode== "pop"){
		echo "<Script>parent.document.location.href='cupon_publish_list.php?mmode=$mmode'</Script>";
	}else{
		if($view_type != "mem_group"){
			echo "<Script>parent.document.location.href='cupon_publish_list.php'</Script>";
		}else{
			echo "<Script>parent.document.location.href='group_cupon_publish_list.php'</Script>";
		}
	}
	//echo "<Script>parent.document.location.reload();</Script>";
	exit;
}


if($act == "regist_search_update"){
	
		$sql = "Select publish_ix,use_date_type,publish_date_differ,publish_type,publish_date_type , regist_date_type, regist_date_differ,date_format(use_sdate,'%Y%m%d') as use_sdate, date_format(use_edate,'%Y%m%d') as use_edate,date_format(regdate,'%Y%m%d') as regdate
					from ".TBL_SHOP_CUPON_PUBLISH."
					where publish_ix = '".$publish_ix."'";
		//$db->debug = true;
		$db->query($sql);
		$db->fetch();
		$publish_ix = $db->dt[publish_ix];
		
		/* 20130902 Hong 발행일 부터는 SHOP_CUPON_PUBLISH.regdate 로 변경
		$p_year=substr($db->dt["use_sdate"],0,4);
		$p_month=substr($db->dt["use_sdate"],4,2);
		$p_day=substr($db->dt["use_sdate"],6,2);
		*/
		
		 
		$p_year = date("Y",strtotime($db->dt["regdate"]));//substr($db->dt["regdate"],0,4);
		$p_month = date("m",strtotime($db->dt["regdate"]));//substr($db->dt["regdate"],4,2);
		$p_day= date("d",strtotime($db->dt["regdate"])); //substr($db->dt["regdate"],6,2);

		
		if($db->dt[use_date_type] == 1){
			if($db->dt[publish_date_type] == 1){
				$publish_year = $p_year + $db->dt[publish_date_differ];
			}else{
				$publish_year = $p_year;
			}
			if($db->dt[publish_date_type] == 2){
				$publish_month = $p_month + $db->dt[publish_date_differ];
			}else{
				$publish_month = $p_month;
			}
			if($db->dt[publish_date_type] == 3){
				$publish_day = $p_day + $db->dt[publish_date_differ];
			}else{
				$publish_day = $p_day;
			}
			$use_sdate=mktime(0,0,0,$p_month,$p_day,$p_year);
			$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);

			//$event_date = mktime(0,0,0,$event_meonth,$event_day+$order[end_date_differ],$evet_year);

		}else if($db->dt[use_date_type] == 2){
			if($db->dt[regist_date_type] == 1){
				$regist_year = date("Y") + $db->dt[regist_date_differ];
			}else{
				$regist_year = date("Y");
			}
			if($db->dt[regist_date_type] == 2){
				$regist_month = date("m") + $db->dt[regist_date_differ];
			}else{
				$regist_month = date("m");
			}
			if($db->dt[regist_date_type] == 3){
				$regist_day = date("d") + $db->dt[regist_date_differ];
			}else{
				$regist_day = date("d");
			}
			$use_sdate = time();
			$use_date_limit = mktime(0,0,0,$regist_month,$regist_day,$regist_year);
		}else if($db->dt[use_date_type] == 3){
			$use_sdate = mktime(0,0,0,substr($db->dt[use_sdate],4,2),substr($db->dt[use_sdate],6,2),substr($db->dt[use_sdate],0,4));
			$use_date_limit = mktime(0,0,0,substr($db->dt[use_edate],4,2),substr($db->dt[use_edate],6,2),substr($db->dt[use_edate],0,4));
		}

		$where = " where cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0 ";

        // 2012-10-09 홍진영
        if($db->dbms_type == "oracle"){
            if($search_type != "" && $search_text != ""){
                if($search_type=="name" || $search_type=="mail"  || $search_type == "pcs" || $search_type == "tel" ) {
                    $where .= " and AES_DECRYPT(".$search_type.") LIKE '%".$search_text."%' ";
                } else {
                    $where .= " and ".$search_type." LIKE '%$search_text%' ";
                }
            }
        }else{
            if($multi_search == 1){
                $search_array = explode("\r\n",$search_texts);
                if($search_type=="name" || $search_type=="mail"  || $search_type == "pcs" || $search_type == "tel" ) {
                    //$where .= " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') LIKE '%".$search_text."%' ";
                    $where .= " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') in ('".implode("','",$search_array)."') ";
                } else {
                    $where .= " and ".$search_type." in ('".implode("','",$search_array)."') ";
                }


            }else{
                if($search_type != "" && $search_text != ""){
                    if($search_type=="name" || $search_type=="mail"  || $search_type == "pcs" || $search_type == "tel" ) {
                        $where .= " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') LIKE '%".$search_text."%' ";
                    }else if($search_type=="id") {
                        $where .= " and ".$search_type." = '$search_text' ";
                    } else {
                        $where .= " and ".$search_type." LIKE '%$search_text%' ";
                    }
                }
            }
        }

		if($gp_ix != ""){
			$where .= " and cmd.gp_ix = '".$gp_ix."' ";
		}

        if($startDate != "" && $endDate != ""){
            if($publish_div == "2"){
                $where .= " and  cu.date between  '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
            }else if($publish_div == "3"){
                $where .= " and  cmd.recent_order_date between  '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
            }else if($publish_div == "4"){
                $where .= " and  cu.last between  '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
            }
        }

		$use_sdate = date("Ymd",$use_sdate);
		$use_date_limit = date("Ymd",$use_date_limit);
		//$db->debug = true;
		if($dupe_check){
			$sql = "insert into ".TBL_SHOP_CUPON_REGIST."  select '' as regist_ix , '".$publish_ix."' as publish_ix, cu.code,1,0,
							'$use_sdate','$use_date_limit',null,null, NOW() , null, null
							from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg
							$where ";
			$db->sequences = "SHOP_CUPON_REGIST_SEQ";
			$db->query($sql);
		}else{
			$sql = "select cu.code,name,mail, pcs  from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg
							$where ";
			//echo $sql."<br/>";
			$db->query($sql);
			$meminfos = $db->fetchall();


			for($i=0;$i< count($meminfos) ;$i++){


				$db->query("Select publish_ix from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix' and mem_ix = '".$meminfos[$i][code]."' ");
				//echo "Select publish_ix from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix' and mem_ix = '".$meminfos[$i][code]."' ";
				if(!$db->total){
					$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST." (regist_ix,publish_ix,mem_ix,open_yn,use_yn,use_sdate,use_date_limit, regdate)
							values
							('','".$publish_ix."','".$meminfos[$i][code]."','1','0','$use_sdate','$use_date_limit',NOW())";
					$db->sequences = "SHOP_CUPON_REGIST_SEQ";
					//echo $sql2;
					$db->query($sql2);
				}
			}
		}

		if($mmode != "cron"){
			echo "<script language='javascript' src='../js/message.js.php'></script>
			<script language='javascript' >show_alert('정상적으로 등록 되었습니다.');parent.location.href='cupon_register_user.php?publish_ix=".$publish_ix."&mode=result'</script>";
		}
}



if($act == "regist_update"){
	$sql = "Select publish_ix,use_date_type,publish_date_differ,publish_type,publish_date_type ,regist_date_type, regist_date_differ, date_format(use_sdate,'%Y%m%d') as use_sdate, 
				date_format(use_edate,'%Y%m%d') as use_edate,date_format(regdate,'%Y%m%d') as regdate
				from ".TBL_SHOP_CUPON_PUBLISH."
				where publish_ix = '".$publish_ix."'";
	$db->query($sql);
	$db->fetch();
	$publish_ix = $db->dt[publish_ix];
	//print_r($_POST);
	//echo $db->dt[use_date_type].$db->dt[publish_date_type];
	//exit;

	/* 20130902 Hong 발행일 부터는 SHOP_CUPON_PUBLISH.regdate 로 변경
	$p_year=substr($db->dt["use_sdate"],0,4);
	$p_month=substr($db->dt["use_sdate"],4,2);
	$p_day=substr($db->dt["use_sdate"],6,2);
	*/
	
 
	$p_year = date("Y",strtotime($db->dt["regdate"]));//substr($db->dt["regdate"],0,4);
	$p_month = date("m",strtotime($db->dt["regdate"]));//substr($db->dt["regdate"],4,2);
	$p_day= date("d",strtotime($db->dt["regdate"])); //substr($db->dt["regdate"],6,2);

	if($db->dt[use_date_type] == 1){
		if($db->dt[publish_date_type] == 1){
			$publish_year = $p_year + $db->dt[publish_date_differ];
		}else{
			$publish_year = $p_year;
		}
		if($db->dt[publish_date_type] == 2){
			$publish_month = $p_month + $db->dt[publish_date_differ];
		}else{
			$publish_month = $p_month;
		}
		if($db->dt[publish_date_type] == 3){
			$publish_day = $p_day + $db->dt[publish_date_differ];
		}else{
			$publish_day = $p_day;
		}
		$use_sdate=mktime(0,0,0,$p_month,$p_day,$p_year);
		$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);

		//$event_date = mktime(0,0,0,$event_meonth,$event_day+$order[end_date_differ],$evet_year);

	}else if($db->dt[use_date_type] == 2){
		if($db->dt[regist_date_type] == 1){
			$regist_year = date("Y") + $db->dt[regist_date_differ];
		}else{
			$regist_year = date("Y");
		}
		if($db->dt[regist_date_type] == 2){
			$regist_month = date("m") + $db->dt[regist_date_differ];
		}else{
			$regist_month = date("m");
		}
		if($db->dt[regist_date_type] == 3){
			$regist_day = date("d") + $db->dt[regist_date_differ];
		}else{
			$regist_day = date("d");
		}
		$use_sdate = time();
		$use_date_limit = mktime(0,0,0,$regist_month,$regist_day,$regist_year);
	}else if($db->dt[use_date_type] == 3){
		$use_sdate = mktime(0,0,0,substr($db->dt[use_sdate],4,2),substr($db->dt[use_sdate],6,2),substr($db->dt[use_sdate],0,4));
		$use_date_limit = mktime(0,0,0,substr($db->dt[use_edate],4,2),substr($db->dt[use_edate],6,2),substr($db->dt[use_edate],0,4));

	}

	//if($db->dt[publish_type] == "1" || $db->dt[publish_type] == "2"){
		$use_sdate = date("Ymd",$use_sdate);
		if($db->dt[use_date_type] == 9){
			$use_date_limit = "99991231";
		}else{
			$use_date_limit = date("Ymd",$use_date_limit);
		}
			

		if($dupe_check){
			for($i=0;$i<count($code);$i++){
				$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST." (regist_ix,publish_ix,mem_ix,open_yn,use_yn,use_sdate, use_date_limit, regdate)
						values
						('','".$publish_ix."','".$code[$i]."','1','0','$use_sdate','$use_date_limit',NOW())";

				//echo $sql2;
				$db->sequences = "SHOP_CUPON_REGIST_SEQ";
				$db->query($sql2);
			}
		}else{
			for($i=0;$i<count($code);$i++){
				$db->query("Select publish_ix from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix' and mem_ix = '".$code[$i]."' ");

				if(!$db->total){
					$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST." (regist_ix,publish_ix,mem_ix,open_yn,use_yn,use_sdate, use_date_limit, regdate)
							values
							('','".$publish_ix."','".$code[$i]."','1','0','$use_sdate','$use_date_limit',NOW())";

					//echo $sql2;
					$db->sequences = "SHOP_CUPON_REGIST_SEQ";
					$db->query($sql2);
				}
			}
		}
	//}
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 등록 되었습니다.');parent.document.location.href='cupon_register_user.php?publish_ix=".$publish_ix."&mode=result'</script>";
	exit;
}

if($act == "publish_update"){

	/*
	$use_sdate = $FromYY.$FromMM.$FromDD;
	$use_edate = $ToYY.$ToMM.$ToDD;
	if($db->dbms_type == "oracle"){
		$use_sdate = $FromMM."-".$FromDD."-".$FromYY." ".$FromHH.":".$FromII.":".$FromSS;
        $use_edate = $ToMM."-".$ToDD."-".$ToYY." ".$ToHH.":".$ToII.":".$ToSS;
	}*/
	$db->query("Select cupon_use_sdate, cupon_use_edate  from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix = '".$publish_ix."' ");
	$db->fetch();
	$before_cupon_use_sdate = $db->dt[cupon_use_sdate];
	$before_cupon_use_edate = $db->dt[cupon_use_edate];

	if($use_date_type == 1){ //발행일로부터
		if($publish_date_type == 1){
			$publish_year = date("Y") + $publish_date_differ;
		}else{
			$publish_year = date("Y");
		}
		if($publish_date_type == 2){
			$publish_month = date("m") + $publish_date_differ;
		}else{
			$publish_month = date("m");
		}
		if($publish_date_type == 3){
			$publish_day = date("d") + $publish_date_differ;
		}else{
			$publish_day = date("d");
		}

		//$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);
		$use_sdate=date("Ymd");
		$use_edate = date("Ymd",mktime(0,0,0,$publish_month,$publish_day,$publish_year));
        if($db->dbms_type == "oracle"){
        //TO_DATE('10-04-2010 20:37:50','MM-DD-YYYY HH24:MI:SS')
            $use_sdate = date("m-d-Y H:i:s");
            $use_edate = date("m-d-Y H:i:s",mktime(0,0,0,$publish_month,$publish_day,$publish_year));
        }
		//$event_date = mktime(0,0,0,$event_meonth,$event_day+$order[end_date_differ],$evet_year);

	}else if($use_date_type == 2){ //발급일로부터
		if($regist_date_type == 1){
			$regist_year = date("Y") + $regist_date_differ;
		}else{
			$regist_year = date("Y");
		}
		if($regist_date_type == 2){
			$regist_month = date("m") + $regist_date_differ;
		}else{
			$regist_month = date("m");
		}
		if($regist_date_type == 3){
			$regist_day = date("d") + $regist_date_differ;
		}else{
			$regist_day = date("d");
		}

		//$use_date_limit = mktime(0,0,0,$regist_month,$regist_day,$regist_year);
		$use_sdate=date("Ymd");
		$use_edate = date("Ymd",mktime(0,0,0,$regist_month,$regist_day,$regist_year));
        if($db->dbms_type == "oracle"){
        //TO_DATE('10-04-2010 20:37:50','MM-DD-YYYY HH24:MI:SS')
            $use_sdate = date("m-d-Y H:i:s");
            $use_edate = date("m-d-Y H:i:s",mktime(0,0,0,$publish_month,$publish_day,$publish_year));
        }
	}else if($use_date_type == 3){ //사용기간 지정
		//$use_sdate = $FromYY.$FromMM.$FromDD;
		//$use_edate = $ToYY.$ToMM.$ToDD;
        if($db->dbms_type == "oracle"){
            $use_sdate_time = $use_sdate." ".$use_sdate_h.":".$use_sdate_i.":".$use_sdate_s;
            $use_edate_time = $use_edate." ".$use_edate_h.":".$use_edate_i.":".$use_edate_s;
        }else{
			$use_sdate_time = $use_sdate." ".$use_sdate_h.":".$use_sdate_i.":".$use_sdate_s;
            $use_edate_time = $use_edate." ".$use_edate_h.":".$use_edate_i.":".$use_edate_s;
		}

	}else { //기간 사용하지 않음 -> 현재날짜 넣음.
		$use_sdate=date("Ymd");
        if($db->dbms_type == "oracle"){
        //TO_DATE('10-04-2010 20:37:50','MM-DD-YYYY HH24:MI:SS')
           $use_sdate = date("m-d-Y H:i:s");
        }
	}

	if($use_date_type == 1){
		if($publish_date_type == 1){
			$publish_year = date("Y") + $publish_date_differ;
		}else{
			$publish_year = date("Y");
		}
		if($publish_date_type == 2){
			$publish_month = date("m") + $publish_date_differ;
		}else{
			$publish_month = date("m");
		}
		if($publish_date_type == 3){
			$publish_day = date("d") + $publish_date_differ;
		}else{
			$publish_day = date("d");
		}

		$use_date_limit = mktime(0,0,0,$publish_month,$publish_day,$publish_year);

		//$event_date = mktime(0,0,0,$event_meonth,$event_day+$order[end_date_differ],$evet_year);

	}else if($use_date_type == 2){ 
		if($regist_date_type == 1){
			$regist_year = date("Y") + $regist_date_differ;
		}else{
			$regist_year = date("Y");
		}
		if($regist_date_type == 2){
			$regist_month = date("m") + $regist_date_differ;
		}else{
			$regist_month = date("m");
		}
		if($regist_date_type == 3){
			$regist_day = date("d") + $regist_date_differ;
		}else{
			$regist_day = date("d");
		}

		$use_date_limit = mktime(0,0,0,$regist_month,$regist_day,$regist_year);
	}else if($use_date_type == 3){ //사용기간 지정
		//$use_sdate = $FromYY.$FromMM.$FromDD;
		//$use_edate = $ToYY.$ToMM.$ToDD;
		if($db->dbms_type == "oracle"){
			$use_sdate_time = $use_sdate." ".$use_sdate_h.":".$use_sdate_i.":".$use_sdate_s;
            $use_edate_time = $use_edate." ".$use_edate_h.":".$use_edate_i.":".$use_edate_s;
		}else{
			$use_sdate_time = $use_sdate." ".$use_sdate_h.":".$use_sdate_i.":".$use_sdate_s;
            $use_edate_time = $use_edate." ".$use_edate_h.":".$use_edate_i.":".$use_edate_s;
		}

	}else { //기간 사용하지 않음 -> 현재날짜 넣음.
		$use_sdate=date("Ymd");
        if($db->dbms_type == "oracle"){
        //TO_DATE('10-04-2010 20:37:50','MM-DD-YYYY HH24:MI:SS')
           $use_sdate = date("m-d-Y H:i:s");
        }
	}

	//print_r($_POST);
	//exit;
	$cupon_use_sdate = mktime($_POST["cupon_use_sdate_h"], $_POST["cupon_use_sdate_i"], $_POST["cupon_use_sdate_s"], date("m",strtotime($_POST["cupon_use_sdate"])), date("d",strtotime($_POST["cupon_use_sdate"])), date("Y",strtotime($_POST["cupon_use_sdate"])));
	$cupon_use_edate = mktime($_POST["cupon_use_edate_h"], $_POST["cupon_use_edate_i"], $_POST["cupon_use_edate_s"], date("m",strtotime($_POST["cupon_use_edate"])), date("d",strtotime($_POST["cupon_use_edate"])), date("Y",strtotime($_POST["cupon_use_edate"])));

//	print_r($_POST);
		if($view_type != "mem_group"){
			if($db->dbms_type == "oracle"){
				$sql = "update shop_cupon_publish set
							mall_ix= '".$mall_ix."' , cupon_div= '".$cupon_div."' , cupon_ix='$cupon_ix',use_date_type='$use_date_type',use_sdate=TO_DATE('$use_sdate_time','MM-DD-YYYY HH24:MI:SS'),use_edate=TO_DATE('$use_edate_time','MM-DD-YYYY HH24:MI:SS'), use_product_type='$use_product_type', is_include='$is_include', publish_date_differ='$publish_date_differ',publish_date_type='$publish_date_type',regist_date_differ='$regist_date_differ',regist_date_type='$regist_date_type',publish_condition_price='$publish_condition_price',publish_limit_price='$publish_limit_price',publish_type='$publish_type',mem_ix='$mem_ix',publish_name='$publish_name' ,disp='$disp' ,issue_type='$issue_type' ,buy_point='$buy_point' ,is_use='".$is_use."', cupon_use_sdate = '".$cupon_use_sdate."', cupon_use_edate = '".$cupon_use_edate."', editdate = NOW() 
							where publish_ix='$publish_ix' ";
			}else{
				$sql = "update shop_cupon_publish set
							mall_ix= '".$mall_ix."' , cupon_div= '".$cupon_div."' , cupon_ix='$cupon_ix',use_date_type='$use_date_type',use_sdate='$use_sdate_time',use_edate='$use_edate_time',use_product_type='$use_product_type', is_include='$is_include',  publish_date_differ='$publish_date_differ',publish_date_type='$publish_date_type',regist_date_differ='$regist_date_differ',regist_date_type='$regist_date_type',publish_condition_price='$publish_condition_price',publish_limit_price='$publish_limit_price',publish_type='$publish_type',mem_ix='$mem_ix', publish_name='$publish_name',disp='$disp' ,issue_type='$issue_type' ,buy_point='$buy_point' ,is_use='".$is_use."', cupon_use_sdate = '".$cupon_use_sdate."', cupon_use_edate = '".$cupon_use_edate."', editdate = NOW()
							where publish_ix='$publish_ix' ";
			}
		}else{
			if($db->dbms_type == "oracle"){
				$sql = "update shop_cupon_publish_tmp set
							mall_ix= '".$mall_ix."' , cupon_div= '".$cupon_div."' , cupon_ix='$cupon_ix',use_date_type='$use_date_type',use_sdate=TO_DATE('$use_sdate_time','MM-DD-YYYY HH24:MI:SS'),use_edate=TO_DATE('$use_edate_time','MM-DD-YYYY HH24:MI:SS'),use_product_type='$use_product_type', is_include='$is_include',  publish_date_differ='$publish_date_differ',publish_date_type='$publish_date_type',regist_date_differ='$regist_date_differ',regist_date_type='$regist_date_type',publish_condition_price='$publish_condition_price',publish_limit_price='$publish_limit_price',publish_type='$publish_type',mem_ix='$mem_ix',publish_name='$publish_name',disp='$disp',issue_type='$issue_type' ,is_use='".$is_use."', cupon_use_sdate = '".$cupon_use_sdate."', cupon_use_edate = '".$cupon_use_edate."', editdate = NOW()
							where publish_tmp_ix='$publish_tmp_ix' ";
			}else{
				$sql = "update shop_cupon_publish_tmp set
							mall_ix= '".$mall_ix."' , cupon_div= '".$cupon_div."' , cupon_ix='$cupon_ix',use_date_type='$use_date_type',use_sdate='$use_sdate_time',use_edate='$use_edate_time',use_product_type='$use_product_type', is_include='$is_include',  publish_date_differ='$publish_date_differ',publish_date_type='$publish_date_type',regist_date_differ='$regist_date_differ',regist_date_type='$regist_date_type',publish_condition_price='$publish_condition_price',publish_limit_price='$publish_limit_price',publish_type='$publish_type',mem_ix='$mem_ix', publish_name='$publish_name',disp='$disp',issue_type='$issue_type' ,is_use='".$is_use."', cupon_use_sdate = '".$cupon_use_sdate."', cupon_use_edate = '".$cupon_use_edate."', editdate = NOW()
							where publish_tmp_ix='$publish_tmp_ix' ";
			}
		}
		//echo nl2br($sql);
		//exit;
		$db->query($sql);
		//$db->debug = true;
		if($use_product_type == "3"){// 특정상품에 발행
			//echo $view_type;
			if($view_type != "mem_group"){ // 회원그룹발행이 아닐때
				$sql = "update shop_cupon_relation_product set insert_yn = 'N' where publish_ix='$publish_ix' ";
				
				$db->query($sql);
// print_r($rpid);
				for($i=0;$i < count($rpid[1]);$i++){
					$db->query("Select publish_ix from shop_cupon_relation_product where publish_ix='$publish_ix' and pid = '".$rpid[1][$i]."' ");
					
					$db->fetch();
					if(!$db->total){
						$sql = "insert into shop_cupon_relation_product (cpr_ix,publish_ix,pid, vieworder, insert_yn, regdate)
										values ('','".$publish_ix."','".$rpid[1][$i]."','".($i+1)."','Y', NOW())";
						$db->sequences = "SHOP_CUPON_LINK_GOODS_SEQ";
						$db->query($sql);
						//echo $sql;
						$db->query("update shop_product_addinfo set use_coupon_sdate = '".$cupon_use_sdate."', use_coupon_edate = '".$cupon_use_edate."' , use_coupon_yn = '1' where pid = '".$rpid[1][$i]."' ");
						

					}else{
						
						$db->query("update shop_cupon_relation_product set vieworder='".($i+1)."', insert_yn = 'Y' where publish_ix='$publish_ix' and pid = '".$rpid[1][$i]."' ");

						//if($before_cupon_use_sdate != $cupon_use_sdate || $before_cupon_use_edate != $cupon_use_edate){
						$db->query("Select * from shop_product_addinfo where pid = '".$rpid[1][$i]."' ");
						if($db->total){
							$sql = "update shop_product_addinfo set use_coupon_sdate = '".$cupon_use_sdate."', use_coupon_edate = '".$cupon_use_edate."' , use_coupon_yn = '1' where pid = '".$rpid[1][$i]."' ";
							//echo $sql;
							$db->query($sql);
						}else{
							$sql = "update shop_product_addinfo set use_coupon_sdate = '".$cupon_use_sdate."', use_coupon_edate = '".$cupon_use_edate."' , use_coupon_yn = '1' where pid = '".$rpid[1][$i]."' ";
							//echo $sql;
							$db->query($sql);
						}
						//}
					}
				}

				$db->query("select pid from shop_cupon_relation_product where publish_ix='".$publish_ix."' and insert_yn = 'N'  ");
				$change_addinfo = $db->fetchall();
				for($j=0;$j < count($change_addinfo);$j++){						 
					$db->query("update shop_product_addinfo set discount_sdate = '0', discount_edate = '0' , discount_yn = '0' where pid = '".$change_addinfo[$j][pid]."' ");					 
				}

				$db->query("delete from shop_cupon_relation_product where publish_ix='".$publish_ix."' and insert_yn = 'N' ");
				$db->query("delete from shop_cupon_relation_category where  publish_ix = '".$publish_ix."' ");
			}else{
				$db->query("update shop_cupon_relation_product set insert_yn = 'N' where publish_tmp_ix='$publish_tmp_ix' ");

				for($i=0;$i < count($rpid[1]);$i++){
					$db->query("Select publish_tmp_ix from shop_cupon_relation_product where publish_tmp_ix='$publish_tmp_ix' and pid = '".$rpid[1][$i]."' ");
					$db->fetch();
					if(!$db->total){
						$sql = "insert into shop_cupon_relation_product (cpr_ix,publish_tmp_ix,pid, vieworder, insert_yn, regdate)
										values ('','".$publish_tmp_ix."','".$rpid[1][$i]."','".($i+1)."','Y', NOW())";
						$db->sequences = "SHOP_CUPON_LINK_GOODS_SEQ";
						$db->query($sql);

						//$db->query("update shop_product_addinfo set use_coupon_sdate = '".$cupon_use_sdate."', use_coupon_edate = '".$cupon_use_edate."' , use_coupon_yn = '1' where pid = '".$rpid[1][$i]."' ");
						//echo $sql;
					}else{
						$db->query("update shop_cupon_relation_product set vieworder='".($i+1)."', insert_yn = 'Y' where publish_tmp_ix='$publish_tmp_ix' and pid = '".$rpid[1][$i]."' ");

						//if($before_cupon_use_sdate != $cupon_use_sdate || $before_cupon_use_edate != $cupon_use_edate){
						//	$db->query("update shop_product_addinfo set use_coupon_sdate = '".$cupon_use_sdate."', use_coupon_edate = '".$cupon_use_edate."' , use_coupon_yn = '1' where pid = '".$rpid[1][$i]."' ");
						//}
					}
				}

				$db->query("delete from shop_cupon_relation_product where publish_tmp_ix='".$publish_tmp_ix."' and insert_yn = 'N' ");
				$db->query("delete from shop_cupon_relation_category where  publish_tmp_ix = '".$publish_tmp_ix."' ");
			}

		}else if($use_product_type == "2"){ //카테고리 에 속한 상품에 발행

			if($view_type != "mem_group"){
				$db->query("update shop_cupon_relation_category set insert_yn = 'N' where publish_ix='$publish_ix' ");
				for($i=0;$i < count($category);$i++){
					$sql = "Select publish_ix from shop_cupon_relation_category where cid = '".$category[$i]."' and publish_ix = '".$publish_ix."' ";
					//echo $sql;
					$db->query($sql);
					$db->fetch();
					if(!$db->total){
						$sql = "insert into shop_cupon_relation_category (cpc_ix,publish_ix,cid, depth, insert_yn, regdate)
										values ('','".$publish_ix."','".$category[$i]."','".$depth[$i]."','Y',NOW())";
					//	echo $sql;
						$db->sequences = "SHOP_CUPON_LINK_CT_SEQ";
						$db->query($sql);

						$sql = "update shop_product_addinfo pa inner join shop_product_relation pr on pa.pid = pr.pid set 
									pa.use_coupon_sdate = '".$cupon_use_sdate."', pa.use_coupon_edate = '".$cupon_use_edate."' , use_coupon_yn = '1' 
									where pr.cid = '".$category[$i]."' ";
						$db->query($sql);

					}else{
						$db->query("update shop_cupon_relation_category set insert_yn = 'Y' where publish_ix='$publish_ix' and cid='".$category[$i]."' ");
						if($before_cupon_use_sdate != $cupon_use_sdate || $before_cupon_use_edate != $cupon_use_edate){							
							$sql = "update shop_product_addinfo pa inner join shop_product_relation pr on pa.pid = pr.pid set 
									pa.use_coupon_sdate = '".$cupon_use_sdate."', pa.use_coupon_edate = '".$cupon_use_edate."' , use_coupon_yn = '1' 
									where pr.cid = '".$category[$i]."' ";

							$db->query($sql);
						}
					}
				}

				$db->query("select pid from shop_cupon_relation_category crc inner join shop_product_relation pr on crc.cid = pr.cid where publish_ix='".$publish_ix."' and crc.insert_yn = 'N'  ");
				$change_addinfo = $db->fetchall();
				for($j=0;$j < count($change_addinfo);$j++){						 
					$db->query("update shop_product_addinfo set discount_sdate = '0', discount_edate = '0' , discount_yn = '0' where pid = '".$change_addinfo[$j][pid]."' ");					 
				}

				$db->query("delete from shop_cupon_relation_category where publish_ix='".$publish_ix."' and insert_yn = 'N' ");
				$db->query("delete from shop_cupon_relation_product where  publish_ix = '".$publish_ix."' ");
			}else{
				$db->query("update shop_cupon_relation_category set insert_yn = 'N' where publish_tmp_ix='$publish_tmp_ix' ");
				for($i=0;$i < count($category);$i++){
					$sql = "Select publish_tmp_ix from shop_cupon_relation_category where cid = '".$category[$i]."' and publish_tmp_ix = '".$publish_tmp_ix."' ";
					//echo $sql;
					$db->query($sql);
					$db->fetch();
					if(!$db->total){
						$sql = "insert into shop_cupon_relation_category (cpc_ix,publish_tmp_ix,cid, depth, insert_yn, regdate)
										values ('','".$publish_tmp_ix."','".$category[$i]."','".$depth[$i]."','Y',NOW())";
					//	echo $sql;
						$db->sequences = "SHOP_CUPON_LINK_CT_SEQ";
						$db->query($sql);
					}else{
						$db->query("update shop_cupon_relation_category set insert_yn = 'Y' where publish_tmp_ix='$publish_tmp_ix' and cid='".$category[$i]."' ");
					}
				}
				$db->query("delete from shop_cupon_relation_category where publish_tmp_ix='".$publish_tmp_ix."' and insert_yn = 'N' ");
				$db->query("delete from shop_cupon_relation_product where  publish_tmp_ix = '".$publish_tmp_ix."' ");
			}
		}else if($use_product_type == "4"){// 특정 브랜드에 속한 상품
			if($view_type != "mem_group"){
				$sql = "delete from shop_cupon_relation_brand where publish_ix = '".$publish_ix."' ";
				$db->query($sql);

				for($i=0;$i < count($brand);$i++){
					$sql = "insert into shop_cupon_relation_brand (crb_ix,publish_ix,b_ix, regdate)
									values ('','".$publish_ix."','".$brand[$i]."',NOW())";
					$db->sequences = "SHOP_CUPON_LINK_BRAND_SEQ";
					$db->query($sql);

					$sql = "update shop_product_addinfo pa inner join shop_product p on pa.pid = p.id set 
								pa.use_coupon_sdate = '".$cupon_use_sdate."', pa.use_coupon_edate = '".$cupon_use_edate."' , pa.use_coupon_yn = '1' 
								where p.brand = '".$brand[$i]."' ";
					$db->query($sql);
				}
			}else{

				$sql = "delete from shop_cupon_relation_brand where publish_tmp_ix = '".$publish_tmp_ix."' ";
				$db->query($sql);

				for($i=0;$i < count($brand);$i++){
					$sql = "insert into shop_cupon_relation_brand (crb_ix,publish_tmp_ix,b_ix, regdate)
									values ('','".$publish_tmp_ix."','".$brand[$i]."',NOW())";
					$db->sequences = "SHOP_CUPON_LINK_BRAND_SEQ";
					$db->query($sql);

					if($before_cupon_use_sdate != $cupon_use_sdate || $before_cupon_use_edate != $cupon_use_edate){
						$sql = "update shop_product_addinfo pa inner join shop_product p on pa.pid = p.id set 
								pa.use_coupon_sdate = '".$cupon_use_sdate."', pa.use_coupon_edate = '".$cupon_use_edate."' , pa.use_coupon_yn = '1' 
								where p.brand = '".$brand[$i]."' ";
						$db->query($sql);
					}
				}
			}
		}else if($use_product_type == "5"){ // 특정셀러에 속한 상품에 발행
			//$db->debug = true;
			//print_r($seller);
			if($view_type != "mem_group"){
				$db->query("update shop_cupon_relation_seller set insert_yn = 'N'  where publish_ix = '".$publish_ix."'  ");
			}else{
				$db->query("update shop_cupon_relation_seller set insert_yn = 'N'  where publish_tmp_ix = '".$publish_tmp_ix."'  ");
			}
		
			for($j=0;$j < count($seller);$j++){
				if($view_type != "mem_group"){
					$db->query("select crs_ix from shop_cupon_relation_seller where publish_ix = '".$publish_ix."'  and company_id = '".$seller[$j]."' ");
				}else{
					$db->query("select crs_ix from shop_cupon_relation_seller where publish_tmp_ix = '".$publish_tmp_ix."'  and company_id = '".$seller[$j]."' ");
				}
				
				if(!$db->total){
					if($view_type != "mem_group"){
						$sql = "insert into shop_cupon_relation_seller (crs_ix,company_id,publish_ix, vieworder, insert_yn, regdate) values ('','".$seller[$j]."','".$publish_ix."','".($j+1)."','Y', NOW())";
						$db->sequences = "SHOP_POPUP_SELLER_SEQ";
						$db->query($sql);
					}else{
						$sql = "insert into shop_cupon_relation_seller (crs_ix,company_id,publish_tmp_ix, vieworder, insert_yn, regdate) values ('','".$seller[$j]."','".$publish_tmp_ix."','".($j+1)."','Y', NOW())";
						$db->sequences = "SHOP_POPUP_SELLER_SEQ";
						$db->query($sql);
					}
				}else{
					$db->fetch();
					$crs_ix = $db->dt[crs_ix];
					if($view_type != "mem_group"){
						$sql = "update shop_cupon_relation_seller set insert_yn = 'Y',vieworder='".($j+1)."' where crs_ix = '".$crs_ix."' and  publish_ix = '".$publish_ix."'  and company_id = '".$seller[$j]."' ";
						$db->query($sql);
					}else{
						$sql = "update shop_cupon_relation_seller set insert_yn = 'Y',vieworder='".($j+1)."' where crs_ix = '".$crs_ix."' and  publish_tmp_ix = '".$publish_tmp_ix."'  and company_id = '".$seller[$j]."' ";
						$db->query($sql);
					}
				}
			}
			if($view_type != "mem_group"){
				$db->query("delete from shop_cupon_relation_seller where publish_ix = '".$publish_ix."'  and insert_yn = 'N' ");
			}else{
				$db->query("delete from shop_cupon_relation_seller where publish_tmp_ix = '".$publish_tmp_ix."'  and insert_yn = 'N' ");
			}
		}
		// 2015.01.29. 신훈식  shop_cupon_publish_config 파일로 저장하는 프로세스로 변경
		// 테스트 완료후 삭제 예정
		if($publish_type == "4"){

			$sql = "delete from shop_cupon_relation_group where publish_tmp_ix = '".$publish_ix."' ";
			$db->query($sql);
			//count($select_gp_list) selected_result[group] 
			//echo count($selected_result[group]);
			//exit;
			for($i=0;$i < count($selected_result[group]);$i++){
				$sql = "insert into shop_cupon_relation_group (crg_ix,publish_tmp_ix,gp_ix,regdate)
						values
						('','".$publish_ix."','".$selected_result[group][$i]."',NOW())";
				$db->query($sql);
			}
			/* 기존소스 ###
			for($i=0;$i < count($select_gp_list);$i++){
				$sql = "insert into shop_cupon_relation_group (crg_ix,publish_tmp_ix,gp_ix,regdate)
						values
						('','".$publish_tmp_ix."','".$select_gp_list[$i]."',NOW())";
				$db->query($sql);
			}*/
		}

		if($publish_type == "1" || $publish_type == "4"){
			//$pdr_div = "T";
			if($publish_type == 1){
				$publish_type_text = "member";
			}else if($publish_type == 4){
				$publish_type_text = "group";
			}
			//$db->debug = true;

			$db->query("update shop_cupon_publish_config set insert_yn = 'N'  where publish_type = '".$publish_type."' and publish_ix = '".$publish_ix."'  ");

			for($j=0;$j < count($selected_result[$publish_type_text]);$j++){
				$db->query("select cpc_ix from shop_cupon_publish_config where publish_type = '".$publish_type."' and r_ix = '".$selected_result[$publish_type_text][$j]."' and publish_ix = '".$publish_ix."'  ");

				if(!$db->total){
					$sql = "insert into shop_cupon_publish_config 
								(cpc_ix, publish_ix ,r_ix,publish_type, vieworder, insert_yn, regdate) 
								values 
								('','".$publish_ix."','".$selected_result[$publish_type_text][$j]."','".$publish_type."','".($j+1)."','Y', NOW())";
					$db->sequences = "SHOP_CUPON_PUBLISH_LINK_SEQ";
					$db->query($sql);
				}else{
					$sql = "update shop_cupon_publish_config set insert_yn = 'Y',vieworder='".($j+1)."'
								where publish_type = '".$publish_type."' and r_ix = '".$selected_result[$publish_type_text][$j]."' and publish_ix = '".$publish_ix."'  ";
					$db->query($sql);
				}
			}

			$db->query("delete from shop_cupon_publish_config where publish_type = '".$publish_type."' and insert_yn = 'N' and publish_ix = '".$publish_ix."'  ");
		}
//echo $use_product_type ;

		if($mmode== "pop"){
			echo "<Script>parent.document.location.href='cupon_publish_list.php?mmode=$mmode'</Script>";
		}else{
			if($view_type != "mem_group"){
				echo "<Script>parent.document.location.href='cupon_publish_list.php'</Script>";
			}else{
				echo "<Script>parent.document.location.href='group_cupon_publish_list.php'</Script>";
			}
		}
		//echo "<Script>document.location.href='cupon_publish_list.php'</Script>";
		//echo "<Script>parent.document.location.reload();</Script>";
		exit;
}

if($act == "insert"){

	$sql = "insert into ".TBL_SHOP_CUPON." 
				(cupon_ix,mall_ix, cupon_kind,cupon_div,cupon_sale_value, cupon_sale_type, cupon_use_div, haddoffice_rate, seller_rate,round_position,round_type,  is_use, disp, regdate) 
				values 
				('','$mall_ix','$cupon_kind','$cupon_div','$cupon_sale_value', '$cupon_sale_type', '$cupon_use_div', '$haddoffice_rate', '$seller_rate',  '$round_position','$round_type', '$is_use', '$disp', NOW())";
	$db->sequences = "SHOP_CUPON_SEQ";
	$db->query($sql);

	if ($cupon_img_size > 0){
		//$cupon_imgname = "bgm_cl_".$db->dt[0].".".substr($cupon_img_name,-3);
		//copy($cupon_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/cupon/".$cupon_imgname);

		if($db->dbms_type == "oracle"){
			$cupon_ix = $db->last_insert_id;
		}else{
			$db->query("SELECT cupon_ix FROM ".TBL_SHOP_CUPON." WHERE cupon_ix=LAST_INSERT_ID()");
			$db->fetch();
			$cupon_ix = $db->dt[0];
		}
		//$db->query("update cardstory_card_backmusic set cupon_img = '$cupon_imgname' WHERE bgm_ix='".$db->dt[0]."'");

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/cupon/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}

		copy($cupon_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/cupon/cupon_".$cupon_ix.".gif");
	}

	echo "<Script>document.location.href='cupon_list.php'</Script>";
	exit;
}

if($act == "update"){
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/cupon/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}
	if ($cupon_img_size > 0){
		copy($cupon_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/cupon/cupon_".$cupon_ix.".gif");
	}
/*
$sql = "insert into ".TBL_SHOP_CUPON." 
				(cupon_ix,mall_ix, cupon_kind,cupon_div,cupon_sale_value, cupon_sale_type, cupon_use_div, haddoffice_rate, seller_rate, round_position,round_type, is_use, disp, regdate) 
				values 
				('','$mall_ix','$cupon_kind','$cupon_div','$cupon_sale_value', '$cupon_sale_type', '$cupon_use_div', '$haddoffice_rate', '$seller_rate', '$round_position','$round_type','$is_use', '$disp', NOW())";
*/
	$sql = "update ".TBL_SHOP_CUPON." set 
				cupon_kind='$cupon_kind', 
				cupon_div='$cupon_div', 
				cupon_sale_value = '$cupon_sale_value', 
				cupon_sale_type = '$cupon_sale_type',
				cupon_use_div='$cupon_use_div', 
				haddoffice_rate='$haddoffice_rate',
				seller_rate='$seller_rate',
				round_position = '$round_position',
				round_type = '$round_type',
				is_use='$is_use',
				disp='$disp'
				where cupon_ix='$cupon_ix'";
	//echo nl2br($sql);
	//exit;
	$db->query($sql);

	echo "<Script>document.location.href='cupon_list.php'</Script>";
	exit;
}

if($act == "delete"){

	if($db->total){
		$db->fetch();
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/cupon/cupon_".$cupon_ix.".gif")){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/cupon/cupon_".$cupon_ix.".gif");
		}
	}
	$sql_regist = "select count(*) as cnt from shop_cupon_regist a,shop_cupon_publish  b
					where a.publish_ix = b.publish_ix
					and b.cupon_ix = '$cupon_ix' and a.use_date_limit > now()";

	$db->query($sql_regist);
	$db->fetch();

	if($db->dt[cnt]){
		echo "<script language='JavaScript' src='../_language/language.js'></Script><script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert(language_data['cupon.act.php']['A'][language]);</script>";//'삭제할수 없습니다.'
	}else{
		$db->query("delete from ".TBL_SHOP_CUPON." where cupon_ix ='$cupon_ix'");
		$db->query("delete from ".TBL_SHOP_CUPON_PUBLISH." where cupon_ix ='$cupon_ix'");
	}
	echo "<Script>document.location.href='cupon_list.php'</Script>";
}


if($act == "regist_delete"){

	for($i=0;$i < count($regist_ix);$i++){
			$db->query("delete from ".TBL_SHOP_CUPON_REGIST." where publish_ix='".$publish_ix."' and regist_ix = '".$regist_ix[$i]."'");
	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 발급 취소 되었습니다.');parent.document.location.href='cupon_register_user.php?publish_ix=".$publish_ix."&mode=result'</script>";
	exit;
}

if($act == "publish_delete"){
	$db->query("select publish_ix from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix' and use_yn = '0'");

	if($db->total){
		echo "<script language='JavaScript' src='../_language/language.js'></Script><script language='javascript' src='../_language/language.php'></script><Script>alert(language_data['cupon.act.php']['B'][language]);</Script>";//'사용되지 않은 쿠폰이 존재합니다. 쿠폰등록을 취소한후 삭제하시기 바랍니다.'
		exit;
	}else{
		$db->query("delete from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix='$publish_ix'");
		$db->query("delete from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix'");
		$db->query("delete from shop_cupon_relation_product where  publish_ix = '".$publish_ix."' ");
		$db->query("delete from shop_cupon_relation_category where  publish_ix = '".$publish_ix."' ");

		$db->query("delete from shop_cupon_relation_product where publish_ix='$publish_ix'");

		echo "<Script>parent.document.location.reload();</Script>";
		exit;
	}
}

if($act == "publish_tmp_delete"){

	$db->query("delete from shop_cupon_publish_tmp where publish_tmp_ix='$publish_tmp_ix'");
	$db->query("delete from shop_cupon_relation_group where publish_tmp_ix='$publish_tmp_ix'");

	echo "<Script>parent.document.location.reload();</Script>";
	exit;
	
}


?>