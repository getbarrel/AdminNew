<?
include("../../class/database.class");

$db = new Database;

if($act == "img_delete"){
    $sql = "select organization_img from ".TBL_SHOP_GROUPINFO." where gp_ix='$gp_ix' ";
    $db->query($sql);

    if($db->total){
        $db->fetch();
        if ($db->dt[organization_img] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img])){
            unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img]);
        }
    }
    echo("<script>top.document.location.reload();</script>");
    exit;
}

if ($act == "insert"){

	$sql = "insert into shop_groupinfo
			(gp_ix,mall_ix,gp_name,organization_img,gp_level,disp,all_disp,basic,mem_type,font_color,wholesale_dc,retail_dc,shipping_dc_yn,shipping_dc_price,regdate,use_discount_type,round_depth,round_type,whole_wms_discount_type,retail_wms_discount_type,selling_type,dc_standard_price,use_coupon_yn,use_reserve_yn,app_dc_yn,app_dc_rate,shipping_free_yn,use_discount_category_yn,use_discount_category_mileage_yn)
	values
			('','$mall_ix','$gp_name','$organization_img','$gp_level','$disp','$all_disp','$basic','$mem_type','$font_color','$wholesale_dc','$retail_dc','$shipping_dc_yn','$shipping_dc_price',NOW(),'$use_discount_type','$round_depth','$round_type','$whole_wms_discount_type','$retail_wms_discount_type', '$selling_type','$dc_standard_price','$use_coupon_yn','$use_reserve_yn','$app_dc_yn','$app_dc_rate','$shipping_free_yn','$use_discount_category_yn','$use_discount_category_mileage_yn')";

	$db->sequences = "SHOP_GROUPINFO_SEQ";
	$db->query($sql);
    $db->query("SELECT gp_ix FROM shop_groupinfo WHERE gp_ix=LAST_INSERT_ID()");
    $db->fetch();
    $gp_ix = $db->dt[gp_ix];

    if($use_discount_category_yn == 'Y'){
    	inputDiscountCategoryInfo($gp_ix,$_POST['cid']);
	}

	if(!file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/")){
		mkdir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/");
		chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/",0644);
	}

	if ($organization_img_size > 0){
		copy($organization_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/".$organization_img);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('그룹이 정상적으로 등록되었습니다.','top_reload');</script>");
	echo("<script>document.location.href='group.php';</script>");
}


if ($act == "update"){

	if($disp == NULL || $disp == ""){
		$disp = 1;
	}

	if($basic == "Y" && false){

		$sql = "update ".TBL_SHOP_GROUPINFO." set
						gp_name='".$gp_name."',
						mall_ix='".$mall_ix."',
						sale_rate='".$sale_rate."',
						period='".$period."',
						keep_period='".$keep_period."',
						order_price='".$order_price."',
						keep_order_cnt='".$keep_order_cnt."',
						give_coupon='".$give_coupon."',
						disp='".$disp."',
						all_disp='".$all_disp."',
						mem_type = '".$mem_type."',
						selling_type = '".$selling_type."',
						use_discount_type = '".$use_discount_type."',
						round_depth = '".$round_depth."',
						round_type = '".$round_type."',
						whole_wms_discount_type = '".$whole_wms_discount_type."',
						retail_wms_discount_type = '".$retail_wms_discount_type."',
						regdate=NOW()
					where
						gp_ix='".$gp_ix."'";
	}else{

		$sql = "update ".TBL_SHOP_GROUPINFO." set
						gp_name='".$gp_name."',
						mall_ix='".$mall_ix."',
						mem_type='".$mem_type."',
						selling_type = '".$selling_type."',
						font_color='".$font_color."',
						wholesale_dc='".$wholesale_dc."',
						retail_dc='".$retail_dc."',
						gp_level='".$gp_level."',
						shipping_dc_yn='".$shipping_dc_yn."',
						shipping_dc_price='".$shipping_dc_price."',
						disp='".$disp."',
						all_disp='".$all_disp."',
						use_discount_type = '".$use_discount_type."',
						round_depth = '".$round_depth."',
						round_type = '".$round_type."',
						whole_wms_discount_type = '".$whole_wms_discount_type."',
						retail_wms_discount_type = '".$retail_wms_discount_type."',
						dc_standard_price = '".$dc_standard_price."',
						use_coupon_yn = '".$use_coupon_yn."',
						use_reserve_yn = '".$use_reserve_yn."',
						app_dc_yn = '".$app_dc_yn."',
						app_dc_rate = '".$app_dc_rate."',
						shipping_free_yn = '".$shipping_free_yn."',
						use_discount_category_yn = '".$use_discount_category_yn."',
						use_discount_category_mileage_yn = '".$use_discount_category_mileage_yn."',
						editdate=NOW()
					where
						gp_ix='".$gp_ix."'";
	}

	$db->query($sql);

    if($use_discount_category_yn == 'Y'){
        inputDiscountCategoryInfo($gp_ix,$_POST['cid']);
    }
	if ($organization_img_size > 0){

		$sql = "select organization_img from ".TBL_SHOP_GROUPINFO." where gp_ix='$gp_ix' ";
		$db->query($sql);

		if($db->total){
			$db->fetch();
			if ($db->dt[organization_img] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img]);
			}
		}
		copy($organization_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/".$organization_img_name);

		$db->query("update ".TBL_SHOP_GROUPINFO." set organization_img='".$organization_img_name."' where gp_ix = '".$gp_ix."'");
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('그룹이 정상적으로 수정되었습니다.','top_reload');</script>");
	echo("<script>location.href = 'group.php';</script>");
}

if ($act == "delete"){
	$sql="select count(user.code) AS cnt from ".TBL_COMMON_USER." user LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." md ON user.code=md.code WHERE md.gp_ix='$gp_ix' ";
	$db->query($sql);
	$db->fetch();
	$mtotal=$db->dt["cnt"];
	//echo $sql;
	//echo("<script>alert('$mtotal');</script>");
	//exit;
	if($mtotal==0) {
		$sql = "select organization_img from ".TBL_SHOP_GROUPINFO." where gp_ix='$gp_ix' ";
		$db->query($sql);

		if($db->total){
			$db->fetch();
			if ($db->dt[organization_img] && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/member_group/".$db->dt[organization_img]);
			}
		}
		$sql = "delete from ".TBL_SHOP_GROUPINFO." where gp_ix='$gp_ix'";
		$db->query($sql);


        $sql = "delete from shop_group_discount_category where gp_ix='$gp_ix'";
        $db->query($sql);


		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('그룹이 정상적으로 삭제되었습니다.','top_reload');</script>");
		echo("<script>document.location.href='group.php';</script>");
		exit;
	} else {
		echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['group.act.php']['A'][language]);document.location.href='group.php';</script>");
		//'해당 그룹으로 지정된 회원이 있으므로 삭제할 수 없습니다.'
		exit;
	}
}


if ($act == "setup_update"){

	$data_array = $_REQUEST[data];

	foreach($data_array as $name =>$value){

			$sql = "update ".TBL_SHOP_GROUPINFO." set
						order_price='".$value[order_price]."',
						ed_order_price='".$value[ed_order_price]."',
						st_reserve='".$value[st_reserve]."',
						ed_reserve='".$value[ed_reserve]."',
						period='".$value[period]."',
						keep_period='".$value[keep_period]."',
						editdate=NOW()
						where gp_ix='".$value[gp_ix]."'";
			$db->query($sql);

	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('그룹설정이 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'group_detail.php';</script>");
}

if ($act == "all_update"){

	$gp_st_date = $_REQUEST[gp_st_date];
	$gp_type = $_REQUEST[gp_type];
	$all_disp = $_REQUEST[all_disp];

	$sql = "update ".TBL_SHOP_GROUPINFO." set
				gp_st_date='".$gp_st_date."',
				gp_type='".$gp_type."'
				";

	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('그룹설정이 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'group_detail.php';</script>");
}


//수동회원그룹변경
if ($act == "membergroupchange"){

	$sql="select cmd.code,cmd.gp_change_date,
		(
			select gp_level
			from shop_groupinfo sg
			where cmd.gp_ix=sg.gp_ix
		) as gp_level,
		(
			select sum(sod.ptprice)
			from shop_order_detail sod
			left join shop_order so using(oid)
			where so.uid=cu.code
			and sod.status in ('DC','AR','AC')
			and dc_date between DATE_ADD(CURDATE(), INTERVAL '-3' MONTH) and now()
		) as m3_total_price,
		(
			select sum(sod.ptprice)
			from shop_order_detail sod
			left join shop_order so using(oid)
			where so.uid=cu.code
			and sod.status in ('DC','AR','AC')
			and dc_date between DATE_ADD(CURDATE(), INTERVAL '-6' MONTH) and now()
		) as m6_total_price
		from common_user cu
		inner join common_member_detail cmd using (code)
		where cu.mem_type in ('M','C')
		";

	$db->query($sql);
	$members = $db->fetchall();

	if($db->total){

		$db->query("select * from shop_groupinfo where disp='1' order by gp_level desc");
		$groupinfos = $db->fetchall();


		foreach ($members as $member){

			foreach ($groupinfos as $groupinfo) {

				if(compare_date($member[gp_change_date],$groupinfo[keep_priod])) {//유지기간 이내일때

					if($member[gp_level] < $groupinfo[gp_level]) { //등업만( 맴버 level 이 비교하는 그룹level이 낮을때만

						if( compare_price($groupinfo[priod],$groupinfo[order_price],$member[m3_total_price],$member[m6_total_price])){// 등업 선정기준에 맞을때

							$db->query("update common_member_detail set gp_ix='".$groupinfo[gp_ix]."', gp_change_date=NOW() where code='".$member[code]."'");
							give_cupon($groupinfo[mem_group_levelup_coupon],$member[code]);

							$text="회원코드 [".$member[code]."] , 전 그룹레벨 [".$member[gp_level]."] , 변경된 그룹레벨 [".$groupinfo[gp_level]."] , 쿠폰지급 O ";


						}else{
							continue;
						}
					}else{
						continue;
					}

				}else{// 유지기간 초과
					if(compare_price($groupinfo[priod],$groupinfo[order_price],$member[m3_total_price],$member[m6_total_price])){ //그룹 level의 회원 level보다 높던 낮던 상관없이 조건에 맞는 등급체인지
						$db->query("update common_member_detail set gp_ix='".$groupinfo[gp_ix]."', gp_change_date=NOW() where code='".$member[code]."'");

						if($member[gp_level] < $groupinfo[gp_level]) {//등업시에만 쿠폰 지급
							give_cupon($groupinfo[mem_group_levelup_coupon],$member[code]);
						}

						$text="회원코드 [".$member[code]."] , 전 그룹레벨 [".$member[gp_level]."] , 변경된 그룹레벨 [".$groupinfo[gp_level]."] , 쿠폰지급 X ";


						continue;

					}else{
						continue;
					}
				}
			}
		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('회원등급이 정상적으로 수정되었습니다.');</script>");
	echo("<script>document.location.href='group.php';</script>");
	exit;
}



function compare_date($date,$month){
	if($date =='0000-00-00'){
		return true;
		exit;
	}
	$today=date('Y-m-d');
	$sub_date = (strtotime("$today") - strtotime("$date"))/3600/24/30;

	if ($sub_date < $month){
		return true;//유지기간 이내일때
	 }else{
		return false;
	}
}

function compare_price($priod,$order_price,$m3_total_price,$m6_total_price){
	switch ($priod){
	case "1":
		return true; //골드 등급은 무조건 트루
	  break;

	case "3":
		if($order_price < $m3_total_price){
			return true;
		}else{
			return false;
		}
	  break;

	case "6":
		if($order_price < $m6_total_price){
			return true;
		}else{
			return false;
		}
	  break;
	}
}

function give_cupon($publish_ix,$code){
global $db;

	$sql = "Select publish_ix,use_date_type,publish_date_differ,publish_type,publish_date_type ,regist_date_type, regist_date_differ, use_sdate, use_edate
			from ".TBL_SHOP_CUPON_PUBLISH." where publish_ix = '".$publish_ix."'";
	$db->query($sql);
	$db->fetch();
	$publish_ix = $db->dt[publish_ix];

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

	if($db->dt[publish_type] == "1" || $db->dt[publish_type] == "2"){
		$use_sdate = date("Ymd",$use_sdate);
		$use_date_limit = date("Ymd",$use_date_limit);

		$db->query("Select publish_ix from ".TBL_SHOP_CUPON_REGIST." where publish_ix='$publish_ix' and mem_ix = '".$code."' ");// 등급이 떨어젔다가 다시 후에 다시 쿠폰발급 받을수 있지 않나요? //누군가 주석 해놓은 것을 다시 풀어놓음. 중복 등록되는 현상이 발생해서.. kbk 13/03/21

		if(!$db->total){//누군가 주석 해놓은 것을 다시 풀어놓음. 중복 등록되는 현상이 발생해서.. kbk 13/03/21
			$sql2 = "insert into ".TBL_SHOP_CUPON_REGIST." (regist_ix,publish_ix,mem_ix,open_yn,use_yn,use_sdate, use_date_limit, regdate)
					values ('','".$publish_ix."','".$code."','1','0','$use_sdate','$use_date_limit',NOW())";

			$db->query($sql2);
		}//누군가 주석 해놓은 것을 다시 풀어놓음. 중복 등록되는 현상이 발생해서.. kbk 13/03/21
	}
}

function inputDiscountCategoryInfo($gp_ix,$cid){
	global $db;
    $sql = "update shop_group_discount_category set update_yn = 'N' where gp_ix = '".$gp_ix."'  ";
    $db->query($sql);
    if(is_array($cid)){
        foreach($cid as $val){
            $sql = "select * from shop_group_discount_category where gp_ix = '".$gp_ix."' and cid = '".$val."' ";
            $db->query($sql);
            if($db->total){
                $sql = "update shop_group_discount_category set update_yn = 'Y', editdate = NOW() where gp_ix = '".$gp_ix."' and cid = '".$val."'";
                $db->query($sql);
            }else{
                $sql = "insert into shop_group_discount_category (gp_ix,cid,update_yn,editdate,regdate) values ('".$gp_ix."','".$val."','Y',NOW(),NOW())";
                $db->query($sql);
            }
        }
    }
    $sql = "delete from shop_group_discount_category where gp_ix = '".$gp_ix."' and update_yn = 'N' ";
    $db->query($sql);
}
?>
