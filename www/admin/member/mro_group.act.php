<?
include("../../class/database.class");

$db = new Database;

if ($act == "insert"){

	$sql = "insert into shop_mro_groupinfo
				(mgp_ix,mall_ix,gp_name,sale_rate,gp_level,use_coupon_yn,use_reserve_yn,disp,editdate,regdate) 
				values
				('','$mall_ix','$gp_name','$sale_rate','$gp_level','$use_coupon_yn','$use_reserve_yn','$disp',NOW(),NOW())";


	$db->query($sql);

	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('고정단가 그룹이 정상적으로 등록되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}


if ($act == "update"){

	if($disp == NULL || $disp == ""){
		$disp = 1;
	}

	$sql = "update shop_mro_groupinfo set  
					mall_ix='$mall_ix',
					gp_name='$gp_name',
					sale_rate='$sale_rate',
					gp_level='$gp_level',
					use_coupon_yn='$use_coupon_yn',
					use_reserve_yn='$use_reserve_yn',
					disp='$disp',
					editdate=NOW()
					where
					mgp_ix='".$mgp_ix."' ";

	$db->query($sql);
 

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('그룹이 정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
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
		$sql = "delete from shop_mro_groupinfo where mgp_ix='".$mgp_ix."' ";
		$db->query($sql);


		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('고정단가 그룹이 정상적으로 삭제되었습니다.');</script>");
		echo("<script>parent.document.location.reload();</script>");
		exit;
	} else {
		echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['group.act.php']['A'][language]);parent.document.location.reload();</script>");
		//'해당 그룹으로 지정된 회원이 있으므로 삭제할 수 없습니다.'
		exit;
	}
}

 