<?
///////////////////////////////////////////////////////////////////
//
// 제목 : 전시관리 등록 처리 - 이현우(2013-05-08)
//
///////////////////////////////////////////////////////////////////
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("../include/admin.util.php");

$db = new Database;

if ($act == "vieworder_update"){
	//$db->query("update ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." set vieworder=1 ");
	//$_erpid = str_replace("\\\"","\"",$_POST["erpid"]);
	//echo $_erpid;
	//echo "bbb:".count(unserialize($_POST["erpid"]))."<br>";
	//print_r(unserialize($_POST["erpid"]));
	//$_erpid = unserialize(urldecode($_erpid));
	//$erpid = unserialize(urldecode($erpid));
	//echo count($_erpid);
	for($i=0;$i < count($sortlist);$i++){
		$sql = "update ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." set
			vieworder='".($i+1)."'
			where  pid='".$sortlist[$i]."' ";//main_ix='$main_ix' and

		//echo $sql;
		$db->query($sql);
	}

}

if ($act == "insert"){
	// 전시등록
	$FromHH = $_POST["FromHH"];
	$FromMI = $_POST["FromMI"];
	$ToHH = $_POST["ToHH"];
	$ToMI = $_POST["ToMI"];
	$FromHH = addZeroByDate($FromHH);
	$FromMI = addZeroByDate($FromMI);
	$ToHH = addZeroByDate($ToHH);
	$ToMI = addZeroByDate($ToMI);
	$goal_amount = $_POST["goal_amount"];
	$sdate.= $FromHH.$FromMI;
	$edate.=$ToHH.$ToMI;
	if (!$div_ix && $srch_div) $div_ix = $srch_div; // 1차분류만 있으면 1차분류값 지정


	$sql = "INSERT INTO ".TBL_SHOP_DISPLAY." (display_ix, display_div, disp_title,  image_width, image_height, sdate, edate, disp, div_ix, cid, md_id, goal_amount, regdate) VALUES (
				'', '$display_div','$disp_title','$image_width','$image_height','$sdate','$edate','$disp','$div_ix','$cid2', '$md_id', '$goal_amount', now())	";
	$db->query($sql);
	$display_ix = $db->insert_id();

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display/";
	if(!is_dir($path)){
		mkdir($path);
		chmod($path, 0777);
	}

	$img_key = $__arr_display_div_code[$display_div];

	for($i=0;$i < count($group_name);$i++){
		// 전시상품 등록		
		$sql = "insert into ".TBL_SHOP_DISPLAY_PRODUCT_GROUP." (cmpg_ix,div_ix,display_ix,group_name,group_code,group_link,display_type,product_cnt, goods_display_type, display_auto_type, insert_yn, use_yn, regdate, product_cnt, md_id, goal_amount) values('','".$div_ix."','".$display_ix."','".$group_name[$i+1]."','".($i+1)."','".$group_link[$i+1]."','".$display_type[$i+1]."','".$product_cnt[$i+1]."','".$goods_display_type[$i+1]."','".$display_auto_type[$i+1]."','Y','".$use_yn[$i+1]."',NOW()";
		$sql.=", '".$product_cnt[$i+1]."', '".$arr_md_id[$i+1]."', '".$arr_goal_amount[$i+1]."' ";
		$db->sequences = "SHOP_CT_MAIN_GOODS_GROUP_SEQ";
		$db->query($sql);
	
		if ($group_img_del[$i+1] == "Y")
		{
			//unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_".($i+1).".gif");
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display/".$img_key."_".$display_ix."_".($i+1).".gif");
		}

		//echo "size:".$group_img[size][$i+1];
		//print_r($_FILES);
		//exit;

		if ($_FILES["group_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display/".$img_key."_".$display_ix."_".($i+1).".gif");
		}

		for($j=0;$j < count($rpid[$i+1]);$j++){
			$db->query("Select cmpr_ix from ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." where group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' and display_ix = '".$display_ix."' ");

			if(!$db->total){
				$sql = "insert into ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." (cmpr_ix,display_ix,pid,group_code, vieworder, insert_yn, regdate) values ('','".$display_ix."','".$rpid[$i+1][$j]."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_CT_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." where group_code = '".($i+1)."' and insert_yn = 'N' and display_ix = '".$display_ix."' ");
	}
	echo("<script>top.location.href = 'display_list.php?display_ix=$display_ix&display_div=$display_div';</script>");
}
if ($act == "update"){
	if (!$div_ix && $srch_div) $div_ix = $srch_div; // 1차분류만 있으면 1차분류값 지정
	$sql = "SELECT * FROM ".TBL_SHOP_DISPLAY." where display_ix ='$display_ix' ";
 
	$db->query($sql); //AND cid='$cid'

	$FromHH = addZeroByDate($FromHH);
	$FromMI = addZeroByDate($FromMI);
	$ToHH = addZeroByDate($ToHH);
	$ToMI = addZeroByDate($ToMI);

	$sdate.= $FromHH.$FromMI;
	$edate.=$ToHH.$ToMI;

	 

	if($db->total){
		$sql = "update ".TBL_SHOP_DISPLAY." set
				disp_title='$disp_title',
				image_width='$image_width',
				image_height='$image_height',
				sdate='$sdate',
				edate='$edate',
				disp='$disp',
				div_ix='$div_ix',
				cid='$cid3_1',
				md_id='$md_id',
				goal_amount='$goal_amount',
				moddate = now()
				where display_ix='$display_ix'";
 
				
		$db->query($sql);		
	}else{
		$sql = "SELECT display_ix, sdate , edate FROM ".TBL_SHOP_DISPLAY." where div_ix ='$div_ix' order by edate desc limit 0,1";
		//echo $sql;
		$db->query($sql); //AND cid='$cid'
		if($db->total){
			$db->fetch();

			if($db->dt[edate] > $sdate){

				$set_use_sdate = mktime(0,0,0,substr($db->dt[edate],4,2),substr($db->dt[edate],6,2)+1,substr($db->dt[edate],0,4));
				$set_use_edate = mktime(0,0,0,substr($db->dt[edate],4,2),substr($db->dt[edate],6,2)+10,substr($db->dt[edate],0,4));

				echo("<script>alert('프로모션 상품 노출 시작일자가 ".ChangeDate($db->dt[edate],"Y-m-d")." 일 이후여야 합니다. 시작일자를 ".date("Y-m-d",$set_use_sdate)." 로 설정합니다.');parent.select_date('".date("Ymd",$set_use_sdate)."','".date("Ymd",$set_use_edate)."',1);	</script>");
				exit;
			}else{
				$display_ix = $db->dt[display_ix];

				$sql = "update ".TBL_SHOP_DISPLAY." set
				disp_title='$disp_title',
				image_width='$image_width',
				image_height='$image_height',
				sdate='$sdate',
				edate='$edate',
				disp='$disp',
				div_ix='$div_ix',
				cid='$cid2',
				moddate = now()
				where display_ix='$display_ix'";


				$db->query($sql);
			}
		}else{
			$sql = "insert into ".TBL_SHOP_DISPLAY."
					(display_ix,div_ix,cid,disp_title,image_width, image_height,sdate,edate,disp,regdate, display_div)
					values
					('','$div_ix','$cid2','$disp_title', '$image_width','$image_height','$sdate','$edate','$disp',NOW(), '$display_div')";
			$db->sequences = "SHOP_CT_MAIN_GOODS_SEQ";
			$db->query($sql);

			if($db->dbms_type == "oracle"){
				$display_ix = $db->last_insert_id;
			}else{
				$db->query("SELECT display_ix FROM ".TBL_SHOP_DISPLAY." WHERE display_ix=LAST_INSERT_ID()");
				$db->fetch();
				$display_ix = $db->dt[display_ix];
			}
		}
	}


	$sql = "update ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." set insert_yn='N' where display_ix = '".$display_ix."' ";
	$db->query($sql);

	$sql = "update ".TBL_SHOP_DISPLAY_PRODUCT_GROUP." set insert_yn='N' where display_ix = '".$display_ix."' ";
	$db->query($sql);

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display/";
	if(!is_dir($path)){
		mkdir($path);
		chmod($path, 0777);
	}

	for($i=0;$i < count($group_name);$i++){
		$db->query("Select cmpg_ix from ".TBL_SHOP_DISPLAY_PRODUCT_GROUP." where group_code = '".($i+1)."' and display_ix = '".$display_ix."' ");

		if($db->total){
			$db->fetch();
			$sql = "update ".TBL_SHOP_DISPLAY_PRODUCT_GROUP." set
							div_ix='".$div_ix."',
							display_ix='".$display_ix."',
							group_name='".$group_name[$i+1]."',
							display_type='".$display_type[$i+1]."',
							insert_yn='Y', use_yn='".$use_yn[$i+1]."',
							group_link='".$group_link[$i+1]."',
							product_cnt='".$product_cnt[$i+1]."',
							goods_display_type='".$goods_display_type[$i+1]."',
							display_auto_type='".$display_auto_type[$i+1]."'	,
							product_cnt='".$product_cnt[$i+1]."',
							md_id = '".$arr_md_id[$i+1]."',
							goal_amount = '".$arr_goal_amount[$i+1]."'
							where cmpg_ix='".$db->dt[cmpg_ix]."' and group_code = '".($i+1)."' and display_ix = '".$display_ix."' ";
						 
			$db->query($sql);
		}else{
			$sql = "insert into ".TBL_SHOP_DISPLAY_PRODUCT_GROUP." (cmpg_ix,div_ix,display_ix,group_name,group_code,group_link,display_type,product_cnt, goods_display_type, display_auto_type, insert_yn, use_yn, regdate, product_cnt, md_id, goal_amount) values('','".$div_ix."','".$display_ix."','".$group_name[$i+1]."','".($i+1)."','".$group_link[$i+1]."','".$display_type[$i+1]."','".$product_cnt[$i+1]."','".$goods_display_type[$i+1]."','".$display_auto_type[$i+1]."','Y','".$use_yn[$i+1]."',NOW()";
			$sql.=", '".$product_cnt[$i+1]."', '".$arr_md_id[$i+1]."', '".$arr_goal_amount[$i+1]."' ";
			$db->sequences = "SHOP_CT_MAIN_GOODS_GROUP_SEQ";
			$db->query($sql);
		}

		if ($group_img_del[$i+1] == "Y")
		{
			//unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_".($i+1).".gif");
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display/".$img_key."_".$display_ix."_".($i+1).".gif");
		}

		//echo "size:".$group_img[size][$i+1];
		//print_r($_FILES);
		//exit;

		if ($_FILES["group_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display/".$img_key."_".$display_ix."_".($i+1).".gif");
		}
		//$db->query("SELECT cmpg_ix FROM ".tbl_shop_main_product_group." WHERE cmpg_ix=LAST_INSERT_ID()");
		//$db->fetch();
		//$cmpg_ix = $db->dt[0];

		//$db->query("update ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." set insert_yn = 'N' where main_ix='".$main_ix."' and group_code = '".($i+1)."' ");

		for($j=0;$j < count($rpid[$i+1]);$j++){
			$db->query("Select cmpr_ix from ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." where group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' and display_ix = '".$display_ix."' ");

			if(!$db->total){
				$sql = "insert into ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." (cmpr_ix, cmg_ix, pid,group_code, vieworder, insert_yn, regdate) values ('','".$display_ix."','".$rpid[$i+1][$j]."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_CT_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." where group_code = '".($i+1)."' and insert_yn = 'N' and display_ix = '".$display_ix."' ");
		$db->query("update ".TBL_SHOP_DISPLAY_CATEGORY_RELATION." set insert_yn = 'N' where group_code = '".($i+1)."' ");

		// 자동화 부분에서 노출 카테고리 저장 하는 부분

		for($j=0;$j < count($category[$i+1]);$j++){
			$db->query("Select cmcr_ix from  ".TBL_SHOP_DISPLAY_CATEGORY_RELATION."  where group_code = '".($i+1)."'   and cid = '".$category[$i+1][$j]."' ");

			if(!$db->total){
				$sql = "insert into  ".TBL_SHOP_DISPLAY_CATEGORY_RELATION."  (cmcr_ix, cid,group_code, vieworder, insert_yn, regdate) values ('', '".$category[$i+1][$j]."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_CT_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update  ".TBL_SHOP_DISPLAY_CATEGORY_RELATION."  set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' ";
				$db->query($sql);
			}
		}
		$db->query("delete from  ".TBL_SHOP_DISPLAY_CATEGORY_RELATION."  where group_code = '".($i+1)."' and insert_yn = 'N' ");


	}


	$db->query("delete from ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." where insert_yn = 'N' and display_ix = '".$display_ix."' ");
	$db->query("delete from ".TBL_SHOP_DISPLAY_PRODUCT_GROUP." where insert_yn = 'N' and display_ix = '".$display_ix."' ");

	if($delete_cache == "Y"){
		include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php");
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cache/";

		$tpl->clearCache('000000000000000');
	}
	echo("<script>top.location.href = 'display_write.php?display_div=$display_div&display_ix=$display_ix';</script>");

} else if ($act == "delete"){
	$sql="SELECT group_code FROM ".TBL_SHOP_DISPLAY_PRODUCT_GROUP." WHERE display_ix='".$display_ix."' ";
	$db->query($sql);
	$group_cnt=$db->total;
	if($group_cnt>0) {
		$group_fetch=$db->fetchall();
		for($i=0;$i<$group_cnt;$i++) {
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display/".$img_key."_".$display_ix."_".$group_fetch[$i]["group_code"].".gif")) {
				chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display/".$img_key."_".$display_ix."_".$group_fetch[$i]["group_code"].".gif", 0777);
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/display/".$img_key."_".$display_ix."_".$group_fetch[$i]["group_code"].".gif");
			}
		}
	}
	$db->query("DELETE FROM ".TBL_SHOP_DISPLAY." WHERE display_ix='".$display_ix."' ");
	$db->query("delete from ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." where display_ix = '".$display_ix."' ");
	$db->query("delete from ".TBL_SHOP_DISPLAY_PRODUCT_GROUP." where display_ix = '".$display_ix."' ");

	echo("<script>top.location.href = 'display_list.php?display_div=$display_div';</script>");
}

?>