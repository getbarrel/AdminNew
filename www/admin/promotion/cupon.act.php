<?
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

/////////////////////////////////////////////////////////////////////////////////////////////
/*
$refererParts = explode('/', $_SERVER['HTTP_REFERER']);

if($refererParts[3] != 'admin'){
    echo "<script>
			 alert('비정상적인 접근입니다.');
		  </script>";
    exit;
}

valCsrf();
*/
/////////////////////////////////////////////////////////////////////////////////////////////

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


// 신규 등록
if($act == "insert"){


    //쿠폰 수정 모드 (복제 후 기존 쿠폰 삭제) 기존 쿠폰을 발급받은 회원이 있을 경우 프로세스 중지 처리

    if($_POST['act'] == 'insert' && $_POST['sub_mode'] == 'modify' && $_POST['publish_ix'] != ''){
        $sql = "select 				
				*
			from 				
				shop_cupon_regist cr 
			where 
				cr.publish_ix = '".$_POST['publish_ix']."'
				";
        $db->query($sql);
        if($db->total > 0){
            echo "<script>alert('회원에게 발급된 쿠폰으로 전체 수정 불가능 합니다.');history.back();</script>";
            exit;
        }else{
            $db->query("select publish_ix, cupon_ix from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix='$publish_ix'");
            $db->fetch();
            $cupon_ix = $db->dt[cupon_ix];

            $db->query("delete from ".TBL_SHOP_CUPON." where cupon_ix='$cupon_ix'");
            $db->query("delete from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix='$publish_ix'");
            $db->query("delete from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix'");
            $db->query("delete from shop_cupon_relation_product where  publish_ix = '".$publish_ix."' ");
            $db->query("delete from shop_cupon_relation_category where  publish_ix = '".$publish_ix."' ");
            $db->query("delete from shop_cupon_relation_product where publish_ix='$publish_ix'");
        }
    }

	if($cupon_acnt == 1){
		$haddoffice_rate = $cupon_sale_value;
	}

	$sql = "insert into ".TBL_SHOP_CUPON." 
				(mall_ix, cupon_kind,cupon_div,cupon_sale_value, cupon_sale_type, cupon_use_div, haddoffice_rate, seller_rate,round_position,round_type,  is_use, disp, regdate, cupon_acnt) 
				values 
				('$mall_ix','$publish_name','$cupon_div','$cupon_sale_value', '$cupon_sale_type', '$cupon_use_div', '$haddoffice_rate', '$seller_rate',  '$round_position','$round_type', '$is_use', '$disp', NOW(), '$cupon_acnt')";
	$db->sequences = "SHOP_CUPON_SEQ";
	$db->query($sql);

	$db->query("SELECT cupon_ix FROM ".TBL_SHOP_CUPON." WHERE cupon_ix=LAST_INSERT_ID()");
	$db->fetch();
	$cupon_ix = $db->dt[cupon_ix];
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

		$use_sdate_time=date("Y-m-d H:i:s");
		$use_edate_time = date("Y-m-d H:i:s",mktime(0,0,0,$publish_month,$publish_day,$publish_year));

		$use_sdate=str_replace("-","",$use_sdate.date('H').date('i').date('s'));
		$use_edate=date("YmdHis",mktime(0,0,0,$publish_month,$publish_day,$publish_year));

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

		$use_sdate = date("YmdHis");
		$use_edate = date("YmdHis",mktime(0,0,0,$regist_month,$regist_day,$regist_year));

	}else if($use_date_type == 3){ //사용기간 지정

		$use_sdate_time = $use_sdate." ".$use_sdate_h.":".$use_sdate_i.":".$use_sdate_s;
		$use_edate_time = $use_edate." ".$use_edate_h.":".$use_edate_i.":".$use_edate_s;
		$use_sdate = date("YmdHis", strtotime($use_sdate_time));
		$use_edate = date("YmdHis", strtotime($use_edate_time));

	}else { //기간 사용하지 않음 -> 현재날짜 넣음.
		$use_sdate='99991231';
	}

	$cupon_use_sdate = mktime($_POST["cupon_use_sdate_h"], $_POST["cupon_use_sdate_i"], $_POST["cupon_use_sdate_s"], date("m",strtotime($_POST["cupon_use_sdate"])), date("d",strtotime($_POST["cupon_use_sdate"])), date("Y",strtotime($_POST["cupon_use_sdate"])));
	$cupon_use_edate = mktime($_POST["cupon_use_edate_h"], $_POST["cupon_use_edate_i"], $_POST["cupon_use_edate_s"], date("m",strtotime($_POST["cupon_use_edate"])), date("d",strtotime($_POST["cupon_use_edate"])), date("Y",strtotime($_POST["cupon_use_edate"])));

	if($issue_type != '4' && $issue_type != '2'){
		$issue_type_detail = '';
	}else if($issue_type == 2){
		$issue_type_detail = $issue_type_detail2;
	}

    if (is_array($payment_method) && !empty($payment_method)) {
        $payment_method = implode('|',$payment_method);
    } else {
        $payment_method = '';
    }

   $sql = "insert into ".TBL_SHOP_CUPON_PUBLISH."
	   (mall_ix, cupon_div, cupon_ix,cupon_no, publish_name,use_date_type,use_sdate, use_edate, use_product_type, is_include , publish_date_differ,publish_date_type,regist_date_differ,regist_date_type,publish_condition_price,publish_limit_price,publish_max_price,publish_type,mem_ix, disp, issue_type, buy_point, is_cs, is_use, cupon_use_sdate, cupon_use_edate, regdate, publish_min, publish_max, publish_max_product, publish_desc, issue_type_detail,discount_use_yn, is_except, overlap_use_yn, regist_count, payment_method)
	   values
	   ('$mall_ix','$cupon_div','$cupon_ix','$cupon_no','$publish_name','$use_date_type','$use_sdate_time','$use_edate_time','$use_product_type', '$is_include',  '$publish_date_differ','$publish_date_type','$regist_date_differ','$regist_date_type','$publish_condition_price','$publish_limit_price','$publish_max_price','$publish_type','$mem_ix','$disp','$issue_type','$buy_point','$is_cs','$is_use','$cupon_use_sdate','$cupon_use_edate',NOW(), '$publish_min', '$publish_max', '$publish_max_product', '$publish_desc', '$issue_type_detail','$discount_use_yn','$is_except','$overlap_use_yn', '$regist_count','$payment_method')";

	$db->sequences = "SHOP_CUPON_PUBLISH_SEQ";
	$db->query($sql);

	$db->query("Select publish_ix from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix = LAST_INSERT_ID()");
	$db->fetch();
	$publish_ix = $db->dt[publish_ix];

		if($use_product_type == "3" || $use_product_type == "6" || $is_except == "1"){
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
		}

		if($use_product_type == "2"){
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

			for($i=0;$i < count($category);$i++){
				$sql = "Select publish_ix from shop_cupon_relation_category where cid = '".$category[$i]."' and publish_ix = '".$publish_ix."' ";
				$db->query($sql);
				$db->fetch();
				if(!$db->total){
					$sql = "insert into shop_cupon_relation_category (cpc_ix,publish_ix,cid, depth, regdate)
									values ('','".$publish_ix."','".$category[$i]."','".$depth[$i]."',NOW())";
					$db->sequences = "SHOP_CUPON_LINK_CT_SEQ";
					$db->query($sql);
				}
			}
		}else if($use_product_type == "4"){

			for($i=0;$i < count($brand);$i++){
				$sql = "insert into shop_cupon_relation_brand (crb_ix,publish_ix,b_ix, regdate)
								values ('','".$publish_ix."','".$brand[$i]."',NOW())";
				$db->sequences = "SHOP_CUPON_LINK_BRAND_SEQ";
				$db->query($sql);
			}

		}else if($use_product_type == "5"){
			$db->query("update shop_cupon_relation_seller set insert_yn = 'N'  where publish_ix = '".$publish_ix."'  ");

			for($j=0;$j < count($seller);$j++){
				$db->query("select crs_ix from shop_cupon_relation_seller where publish_ix = '".$publish_ix."'  and company_id = '".$seller[$j]."' ");

				if(!$db->total){
					$sql = "insert into shop_cupon_relation_seller (crs_ix,company_id,publish_ix, vieworder, insert_yn, regdate) values ('','".$seller[$j]."','".$publish_ix."','".($j+1)."','Y', NOW())";
					$db->sequences = "SHOP_POPUP_SELLER_SEQ";
					$db->query($sql);
				}else{
					$db->fetch();
					$crs_ix = $db->dt[crs_ix];
					$sql = "update shop_cupon_relation_seller set insert_yn = 'Y',vieworder='".($j+1)."' where crs_ix = '".$crs_ix."' and  publish_ix = '".$publish_ix."'  and company_id = '".$seller[$j]."' ";
					$db->query($sql);
				}
			}

			$db->query("delete from shop_cupon_relation_seller where publish_ix = '".$publish_ix."'  and insert_yn = 'N' ");
		}


		if($publish_type == "1" || $publish_type == "4"){
			//$pdr_div = "T";
			if($publish_type == 1){
				$publish_type_text = "member";
			}else if($publish_type == 4){
				$publish_type_text = "group";
			}

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
				$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST."  select '' as regist_ix , '".$publish_ix."' as publish_ix, cu.code,1,0,
								'$use_sdate','$use_edate',null,null, NOW() , null, null
								from ".TBL_COMMON_USER." cu WHERE cu.code in ('".implode("','",$selected_result["member"])."')
								 ";
				$db->sequences = "SHOP_CUPON_REGIST_SEQ";
                for ($rc = 0; $rc < $regist_count; $rc++) {
                    $db->query($sql2);
                }
			}
		}

	if($mmode== "pop"){
		echo "<Script>parent.document.location.href='cupon_publish_list.php?mmode=$mmode'</Script>";
	}else{
		echo "<Script>top.document.location.href='cupon_publish_list.php'</Script>";
	}
	exit;
}

if($act == "update"){
	if($cupon_acnt == 1){
		$haddoffice_rate = $cupon_sale_value;
	}

	$sql = "update ".TBL_SHOP_CUPON." set 
				cupon_kind='$publish_name',
				is_use='$is_use',
				disp='$disp'
				where cupon_ix='$cupon_ix'";
	$db->query($sql);

	$cupon_use_sdate = mktime($_POST["cupon_use_sdate_h"], $_POST["cupon_use_sdate_i"], $_POST["cupon_use_sdate_s"], date("m",strtotime($_POST["cupon_use_sdate"])), date("d",strtotime($_POST["cupon_use_sdate"])), date("Y",strtotime($_POST["cupon_use_sdate"])));
	$cupon_use_edate = mktime($_POST["cupon_use_edate_h"], $_POST["cupon_use_edate_i"], $_POST["cupon_use_edate_s"], date("m",strtotime($_POST["cupon_use_edate"])), date("d",strtotime($_POST["cupon_use_edate"])), date("Y",strtotime($_POST["cupon_use_edate"])));

    if (is_array($payment_method) && !empty($payment_method)) {
        $payment_method = implode('|',$payment_method);
    } else {
        $payment_method = '';
    }

	$sql = "update shop_cupon_publish set
				publish_name='$publish_name',disp='$disp' ,is_use='".$is_use."',use_product_type='".$use_product_type."', cupon_use_sdate = '".$cupon_use_sdate."', cupon_use_edate = '".$cupon_use_edate."', editdate = NOW(), publish_desc = '".$publish_desc."', discount_use_yn = '".$discount_use_yn."' , is_except = '".$is_except."', overlap_use_yn = '".$overlap_use_yn."' , regist_count = '".$regist_count."' , payment_method = '".$payment_method."' 
				where publish_ix='$publish_ix' ";

	$db->query($sql);











    if($use_product_type == "3" || $use_product_type == "6" || $is_except == "1"){
        $db->query("delete from shop_cupon_relation_product where publish_ix = '".$publish_ix."' ");
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
    }

    if($use_product_type == "2"){
        $db->query("delete from shop_cupon_relation_category where publish_ix = '".$publish_ix."' ");
        for($i=0;$i < count($category);$i++){
            $sql = "Select publish_ix from shop_cupon_relation_category where cid = '".$category[$i]."' and publish_ix = '".$publish_ix."' ";
            $db->query($sql);
            $db->fetch();
            if(!$db->total){
                $sql = "insert into shop_cupon_relation_category (cpc_ix,publish_ix,cid, depth, regdate)
									values ('','".$publish_ix."','".$category[$i]."','".$depth[$i]."',NOW())";
                $db->sequences = "SHOP_CUPON_LINK_CT_SEQ";
                $db->query($sql);
            }
        }
    }else if($use_product_type == "4"){

        $db->query("delete from shop_cupon_relation_brand where publish_ix = '".$publish_ix."' ");
        for($i=0;$i < count($brand);$i++){
            $sql = "insert into shop_cupon_relation_brand (crb_ix,publish_ix,b_ix, regdate)
								values ('','".$publish_ix."','".$brand[$i]."',NOW())";
            $db->sequences = "SHOP_CUPON_LINK_BRAND_SEQ";
            $db->query($sql);
        }

    }else if($use_product_type == "5"){
        $db->query("update shop_cupon_relation_seller set insert_yn = 'N'  where publish_ix = '".$publish_ix."'  ");

        $db->query("delete from shop_cupon_relation_seller where publish_ix = '".$publish_ix."' ");
        for($j=0;$j < count($seller);$j++){
            $db->query("select crs_ix from shop_cupon_relation_seller where publish_ix = '".$publish_ix."'  and company_id = '".$seller[$j]."' ");

            if(!$db->total){
                $sql = "insert into shop_cupon_relation_seller (crs_ix,company_id,publish_ix, vieworder, insert_yn, regdate) values ('','".$seller[$j]."','".$publish_ix."','".($j+1)."','Y', NOW())";
                $db->sequences = "SHOP_POPUP_SELLER_SEQ";
                $db->query($sql);
            }else{
                $db->fetch();
                $crs_ix = $db->dt[crs_ix];
                $sql = "update shop_cupon_relation_seller set insert_yn = 'Y',vieworder='".($j+1)."' where crs_ix = '".$crs_ix."' and  publish_ix = '".$publish_ix."'  and company_id = '".$seller[$j]."' ";
                $db->query($sql);
            }
        }

        $db->query("delete from shop_cupon_relation_product where  publish_ix = '".$publish_ix."' ");
        $db->query("delete from shop_cupon_relation_seller where publish_ix = '".$publish_ix."'  and insert_yn = 'N' ");
    }

    if($is_use == 3){
        $sql = "update shop_cupon_regist set  use_yn = 1 WHERE publish_ix = '".$publish_ix."'";
        //$db->query($sql);
    }

	if($mmode== "pop"){
		echo "<Script>parent.document.location.href='coupon_regist.php?publish_ix=".$publish_ix."&mmode=$mmode'</Script>";
	}else{
		echo "<Script>alert('정상적으로 수정되었습니다');top.document.location.href='coupon_regist.php?publish_ix=".$publish_ix."'</Script>";
	}
	exit;
}

if($act == "change"){
	if(! empty($search_searialize_value) && $update_type == 1){
		$unserialize_search_value = unserialize(urldecode($search_searialize_value));
		extract($unserialize_search_value);
	}

	switch($act_detail){
		case 'use0' :
				$update_str = "is_use='0'";
			break;
		case 'use1' :
				$update_str = "is_use='1'";
			break;
		case 'disp0' :
				$update_str = "disp='0'";
			break;
		case 'disp1' :
				$update_str = "disp='1'";
			break;
	}

	if($update_type == 1){
		$where = " where c.cupon_ix is not null ";

		if($search_text != ""){
			if($search_type != ""){
				$where .= " and $search_type LIKE '%$search_text%' ";
			}else{
				$where .= " and (publish_name LIKE '%$search_text%' or cupon_no LIKE '%$search_text%' or cp.publish_ix LIKE '%$search_text%') ";
			}
		}

		if($publlish_date) {
			if($cupon_publish_sdate != "" && $cupon_publish_edate != ""){
				$where .= " and  cp.regdate between  '$cupon_publish_sdate 00:00:00' and '$cupon_publish_edate 23:59:59' ";
			}
		}

		if($use_product_type){
			$where .= " AND use_product_type = '".$use_product_type."' ";
		}

		if($cupon_sale_type){
			$where .= " AND cupon_sale_type = '".$cupon_sale_type."' ";
		}

		if($publish_type){
			$where .= " AND publish_type = '".$publish_type."' ";
		}

		if($cupon_div){
			$where .= " AND cp.cupon_div = '".$cupon_div."' ";
		}

		if($cupon_use_div){
			$where .= " AND c.cupon_use_div = '".$cupon_use_div."' ";
		}

		if($issue_type){
			$where .= " AND cp.issue_type = '".$issue_type."' ";
		}

		if($_GET["is_cs"] != ""){
			$where .= " and cp.is_cs =  '".$_GET["is_cs"]."' ";
		}

		if($is_use != ""){
			$where .= " AND cp.is_use = '".$is_use."' ";
		}

		if($disp != ""){
			$where .= " AND cp.disp = '".$disp."' ";
		}

		if($_GET["mall_ix"] != ""){
			$where .= " and cp.mall_ix =  '".$_GET["mall_ix"]."' ";
		}

		$sql = "select  c.cupon_ix, cp.publish_ix 
				from ".TBL_SHOP_CUPON."  c
					inner join ".TBL_SHOP_CUPON_PUBLISH." cp on c.cupon_ix = cp.cupon_ix
				$where";
		$db->query($sql);
		$ixs = $db->fetchall("object");

		foreach($ixs as $k => $v){
			if($act_detail == "delete"){
				$db->query("delete from ".TBL_SHOP_CUPON." where cupon_ix='".$v[cupon_ix]."'");
				$db->query("delete from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix='".$v[publish_ix]."'");
				$db->query("delete from ".TBL_SHOP_CUPON_REGIST." where publish_ix='".$v[publish_ix]."'");
				$db->query("delete from shop_cupon_relation_product where  publish_ix = '".$v[publish_ix]."' ");
				$db->query("delete from shop_cupon_relation_category where  publish_ix = '".$v[publish_ix]."' ");
				$db->query("delete from shop_cupon_relation_product where publish_ix='".$v[publish_ix]."'");
			}else{
				$sql = "update ".TBL_SHOP_CUPON." set ".$update_str." where cupon_ix = '".$v[cupon_ix]."'";
				$db->query($sql);
				$sql = "update ".TBL_SHOP_CUPON_PUBLISH." set ".$update_str." where cupon_ix = '".$v[cupon_ix]."'";
				$db->query($sql);
			}
		}

	}else if($update_type == 2){
		$sql = "select cupon_ix, publish_ix from ".TBL_SHOP_CUPON_PUBLISH." where cupon_ix in ('".implode("','",$cupon_ix)."')";
		$db->query($sql);
		$ixs = $db->fetchall("object");

		foreach($ixs as $k => $v){
			if($act_detail == "delete"){
				$db->query("delete from ".TBL_SHOP_CUPON." where cupon_ix='".$v[cupon_ix]."'");
				$db->query("delete from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix='".$v[publish_ix]."'");
				$db->query("delete from ".TBL_SHOP_CUPON_REGIST." where publish_ix='".$v[publish_ix]."'");
				$db->query("delete from shop_cupon_relation_product where  publish_ix = '".$v[publish_ix]."' ");
				$db->query("delete from shop_cupon_relation_category where  publish_ix = '".$v[publish_ix]."' ");
				$db->query("delete from shop_cupon_relation_product where publish_ix='".$v[publish_ix]."'");
			}else{
				$sql = "update ".TBL_SHOP_CUPON." set ".$update_str." where cupon_ix = '".$v[cupon_ix]."'";
				$db->query($sql);
				$sql = "update ".TBL_SHOP_CUPON_PUBLISH." set ".$update_str." where cupon_ix = '".$v[cupon_ix]."'";
				$db->query($sql);
			}
		}
	}

	echo "<Script>alert('수정이 완료되었습니다.');top.location.href='cupon_publish_list.php'</Script>";
	exit;
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

if($act == "regist_search_update"){

		$sql = "Select publish_ix,use_date_type,publish_date_differ,publish_type,publish_date_type , regist_date_type, regist_date_differ,date_format(use_sdate,'%Y%m%d') as use_sdate, date_format(use_edate,'%Y%m%d') as use_edate,date_format(regdate,'%Y%m%d') as regdate, regist_count
					from ".TBL_SHOP_CUPON_PUBLISH."
					where publish_ix = '".$publish_ix."'";
		//$db->debug = true;
		$db->query($sql);
		$db->fetch();
		$publish_ix = $db->dt[publish_ix];
        $regist_count = ($db->dt[regist_count] > 0 ? $db->dt[regist_count] : 1);

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
            for ($rc = 0; $rc < $regist_count; $rc++) {
                $db->query($sql);
            }
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
                    for ($rc = 0; $rc < $regist_count; $rc++) {
                        $db->query($sql2);
                    }
				}
			}
		}

		if($mmode != "cron"){
			echo "<script language='javascript' src='../js/message.js.php'></script>
			<script language='javascript' >show_alert('정상적으로 등록 되었습니다.');parent.location.href='cupon_register_user.php?publish_ix=".$publish_ix."&mode=result&cp_state=".$cp_state."'</script>";
		}
}

if($act == "regist_update"){
	$sql = "Select publish_ix,use_date_type,publish_date_differ,publish_type,publish_date_type ,regist_date_type, regist_date_differ, date_format(use_sdate,'%Y%m%d') as use_sdate, 
				date_format(use_edate,'%Y%m%d') as use_edate,date_format(regdate,'%Y%m%d') as regdate, regist_count
				from ".TBL_SHOP_CUPON_PUBLISH."
				where publish_ix = '".$publish_ix."'";
	$db->query($sql);
	$db->fetch();
	$publish_ix = $db->dt[publish_ix];
    $regist_count = ($db->dt[regist_count] > 0 ? $db->dt[regist_count] : 1);
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
                for ($rc = 0; $rc < $regist_count; $rc++) {
                    $db->query($sql2);
                }
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
                    for ($rc = 0; $rc < $regist_count; $rc++) {
                        $db->query($sql2);
                    }
				}
			}
		}
	//}
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 등록 되었습니다.');parent.document.location.href='cupon_register_user.php?publish_ix=".$publish_ix."&mode=result&cp_state=".$cp_state."'</script>";
	exit;
}

if($act == "regist_delete"){

	for($i=0;$i < count($regist_ix);$i++){
			$db->query("delete from ".TBL_SHOP_CUPON_REGIST." where publish_ix='".$publish_ix."' and regist_ix = '".$regist_ix[$i]."'");
	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 발급 취소 되었습니다.');parent.document.location.href='cupon_register_user.php?publish_ix=".$publish_ix."&mode=result&cp_state=".$cp_state."'</script>";
	exit;
}

if($act == "delete"){
	$db->query("select publish_ix, cupon_ix from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix='$publish_ix'");
	$db->fetch();
	$cupon_ix = $db->dt[cupon_ix];

	$db->query("delete from ".TBL_SHOP_CUPON." where cupon_ix='$cupon_ix'");
	$db->query("delete from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix='$publish_ix'");
	$db->query("delete from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix'");
	$db->query("delete from shop_cupon_relation_product where  publish_ix = '".$publish_ix."' ");
	$db->query("delete from shop_cupon_relation_category where  publish_ix = '".$publish_ix."' ");
	$db->query("delete from shop_cupon_relation_product where publish_ix='$publish_ix'");

	echo "<Script>top.document.location.href='cupon_publish_list.php'</Script>";
	exit;
}

if($act == "copy"){
	$db->query("select cupon_ix from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix='$publish_ix'");
	$db->fetch();
	$b_cupon_ix = $db->dt[cupon_ix];
	$b_publish_ix = $publish_ix;

	$sql = "insert into ".TBL_SHOP_CUPON." 
				(mall_ix, cupon_kind,cupon_div,cupon_sale_value, cupon_sale_type, cupon_use_div, haddoffice_rate, seller_rate,round_position,round_type,  is_use, disp, regdate, cupon_acnt) 
				SELECT mall_ix, cupon_kind,cupon_div,cupon_sale_value, cupon_sale_type, cupon_use_div, haddoffice_rate, seller_rate,round_position,round_type,  is_use, disp, NOW(), cupon_acnt FROM ".TBL_SHOP_CUPON." WHERE cupon_ix = '".$b_cupon_ix."'";
	$db->sequences = "SHOP_CUPON_SEQ";
	$db->query($sql);

	$db->query("SELECT cupon_ix FROM ".TBL_SHOP_CUPON." WHERE cupon_ix=LAST_INSERT_ID()");
	$db->fetch();
	$cupon_ix = $db->dt[cupon_ix];
	$cupon_no =  GetCuponNo();

    $sql = "insert into ".TBL_SHOP_CUPON_PUBLISH."
	   (mall_ix, cupon_div, cupon_ix,cupon_no, publish_name,use_date_type,use_sdate, use_edate, use_product_type, is_include , publish_date_differ,publish_date_type,regist_date_differ,regist_date_type,publish_condition_price,publish_limit_price,publish_max_price,publish_type,mem_ix, disp, issue_type, buy_point, is_cs, is_use, cupon_use_sdate, cupon_use_edate, regdate, publish_min, publish_max, publish_max_product, publish_desc, issue_type_detail, discount_use_yn, is_except, overlap_use_yn, regist_count, payment_method)
	   SELECT mall_ix, cupon_div, '".$cupon_ix."','".$cupon_no."', publish_name,use_date_type,use_sdate, use_edate, use_product_type, is_include , publish_date_differ,publish_date_type,regist_date_differ,regist_date_type,publish_condition_price,publish_limit_price,publish_max_price,publish_type,mem_ix, disp, issue_type, buy_point, is_cs, is_use, cupon_use_sdate, cupon_use_edate, NOW(), publish_min, publish_max, publish_max_product, publish_desc, issue_type_detail, discount_use_yn, is_except, overlap_use_yn, regist_count, payment_method FROM ".TBL_SHOP_CUPON_PUBLISH." WHERE publish_ix='".$b_publish_ix."'";

	$db->sequences = "SHOP_CUPON_PUBLISH_SEQ";
	$db->query($sql);

	$db->query("Select publish_ix, use_product_type, publish_type, issue_type, use_sdate, use_edate from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix = LAST_INSERT_ID()");
	$db->fetch();
	$publish_ix = $db->dt[publish_ix];
	$use_product_type = $db->dt[use_product_type];
	$publish_type = $db->dt[publish_type];
	$issue_type = $db->dt[issue_type];
	$use_sdate = $db->dt[use_sdate];
	$use_edate = $db->dt[use_edate];
    $regist_count = $db->dt[regist_count];


	if($use_product_type == "3" || $use_product_type == "6" ){
		$sql = "select cpr_ix from shop_cupon_relation_product where publish_ix = '".$b_publish_ix."'";
		$db->query($sql);
		$cpr_list = $db->fetchall("object");

		for($i=0;$i < count($cpr_list);$i++){
			$sql = "insert into shop_cupon_relation_product (publish_ix,pid, regdate)
				select '".$publish_ix."',pid, NOW() from shop_cupon_relation_product where cpr_ix = '".$cpr_list[$i][cpr_ix]."'";
			$db->sequences = "SHOP_CUPON_LINK_GOODS_SEQ";
			$db->query($sql);
		}




	}else if($use_product_type == "2"){
		$sql = "select cpc_ix from shop_cupon_relation_category where publish_ix = '".$b_publish_ix."'";
		$db->query($sql);
		$c_list = $db->fetchall("object");

		for($i=0;$i < count($c_list);$i++){
			$sql = "insert into shop_cupon_relation_category (publish_ix,cid, depth, regdate)
							select '".$publish_ix."',cid, depth, NOW() from shop_cupon_relation_category where cpc_ix = '".$c_list[$i][cpc_ix]."'";
			$db->sequences = "SHOP_CUPON_LINK_CT_SEQ";
			$db->query($sql);
		}
	}else if($use_product_type == "4"){
		$sql = "select crb_ix from shop_cupon_relation_brand where publish_ix = '".$b_publish_ix."'";
		$db->query($sql);
		$b_list = $db->fetchall("object");

		for($i=0;$i < count($b_list);$i++){
			$sql = "insert into shop_cupon_relation_brand (publish_ix,b_ix, regdate)
							select '".$publish_ix."',b_ix, NOW() from shop_cupon_relation_brand where crb_ix = '".$b_list[$i][crb_ix]."'";
			$db->sequences = "SHOP_CUPON_LINK_BRAND_SEQ";
			$db->query($sql);
		}
	}else if($use_product_type == "5"){
		$sql = "select crs_ix from shop_cupon_relation_seller where publish_ix = '".$b_publish_ix."'";
		$db->query($sql);
		$s_list = $db->fetchall("object");

		for($j=0;$j < count($s_list);$j++){
			$sql = "insert into shop_cupon_relation_seller (company_id,publish_ix, vieworder, insert_yn, regdate) 
					select company_id,'".$publish_ix."', vieworder, insert_yn, NOW() from shop_cupon_relation_seller where crs_ix = '".$b_list[$j][crs_ix]."'";
			$db->sequences = "SHOP_POPUP_SELLER_SEQ";
			$db->query($sql);
		}
	}


	if($publish_type == "1" || $publish_type == "4"){
		$sql = "select cpc_ix from shop_cupon_publish_config where publish_ix = '".$b_publish_ix."'";
		$db->query($sql);
		$cf_list = $db->fetchall("object");

		for($j=0;$j < count($cf_list);$j++){
			$sql = "insert into shop_cupon_publish_config 
						(publish_ix ,r_ix,publish_type, vieworder, insert_yn, regdate) 
						select '".$publish_ix."',r_ix,publish_type, vieworder, insert_yn, NOW() from shop_cupon_publish_config where cpc_ix = '".$cf_list[$j][cpc_ix]."'";
			$db->sequences = "SHOP_CUPON_PUBLISH_LINK_SEQ";
			$db->query($sql);
		}
	}

	if($publish_type == "4"){// 회원그룹 발행일경우
		$sql = "select crg_ix from shop_cupon_relation_group where publish_tmp_ix = '".$b_publish_ix."'";
		$db->query($sql);
		$g_list = $db->fetchall("object");

		for($i=0;$i < count($g_list);$i++){
			$sql = "insert into shop_cupon_relation_group (publish_tmp_ix,gp_ix,regdate)
					select '".$publish_ix."',gp_ix,regdate from shop_cupon_relation_group where crg_ix='".$g_list[$j][crg_ix]."'";
			$db->query($sql);

		}
	}

	if($issue_type == "1"){
		if($publish_type=="1"){
			$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST."  select '' as regist_ix , '".$publish_ix."' as publish_ix, mem_ix,1,0,
							use_sdate,use_date_limit,null,null, NOW() , null, null
							from (select * from ".TBL_SHOP_CUPON_REGIST." WHERE publish_ix='".$b_publish_ix."') as a
							 ";
			$db->sequences = "SHOP_CUPON_REGIST_SEQ";
            for ($rc = 0; $rc < $regist_count; $rc++) {
                $db->query($sql2);
            }
		}
	}

	echo "<Script>top.document.location.href='cupon_publish_list.php'</Script>";
	exit;
}

if($act == "publish_tmp_delete"){

	$db->query("delete from shop_cupon_publish_tmp where publish_tmp_ix='$publish_tmp_ix'");
	$db->query("delete from shop_cupon_relation_group where publish_tmp_ix='$publish_tmp_ix'");

	echo "<Script>parent.document.location.reload();</Script>";
	exit;

}
?>