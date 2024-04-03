<?
/*
메인프로모션 분류 추가로 인해 이미지명이 중복되어 저장되므로 분류 코드를 이미지명 앞에 붙여서 중복 저장을 막음(기존에 쓰던 방식은 그대로 유지) kbk 13/05/09
*/
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");

$db = new Database;

if ($act == "insert"){
	if($b_special == 'on'){
		$b_special = 'Y';
	}else{
		$b_special = 'N';
	}

	if($i_special == 'on'){
		$i_special = 'Y';
	}else{
		$i_special = 'N';
	}

	if($u_special == 'on'){
		$u_special = 'Y';
	}else{
		$u_special = 'N';
	}

	if($b_special_e == 'on'){
		$b_special_e = 'Y';
	}else{
		$b_special_e = 'N';
	}

	if($i_special_e == 'on'){
		$i_special_e = 'Y';
	}else{
		$i_special_e = 'N';
	}

	if($u_special_e == 'on'){
		$u_special_e = 'Y';
	}else{
		$u_special_e = 'N';
	}

	if($b_best == 'on'){
		$b_best = 'Y';
	}else{
		$b_best = 'N';
	}

	if($i_best == 'on'){
		$i_best = 'Y';
	}else{
		$i_best = 'N';
	}

	if($u_best == 'on'){
		$u_best = 'Y';
	}else{
		$u_best = 'N';
	}

	if($b_best_e == 'on'){
		$b_best_e = 'Y';
	}else{
		$b_best_e = 'N';
	}

	if($i_best_e == 'on'){
		$i_best_e = 'Y';
	}else{
		$i_best_e = 'N';
	}

	if($u_best_e == 'on'){
		$u_best_e = 'Y';
	}else{
		$u_best_e = 'N';
	}

	if($b_style == 'on'){
		$b_style = 'Y';
	}else{
		$b_style = 'N';
	}

	if($i_style == 'on'){
		$i_style = 'Y';
	}else{
		$i_style = 'N';
	}

	if($u_style == 'on'){
		$u_style = 'Y';
	}else{
		$u_style = 'N';
	}

	if($b_style_e == 'on'){
		$b_style_e = 'Y';
	}else{
		$b_style_e = 'N';
	}

	if($i_style_e == 'on'){
		$i_style_e = 'Y';
	}else{
		$i_style_e = 'N';
	}

	if($u_style_e == 'on'){
		$u_style_e = 'Y';
	}else{
		$u_style_e = 'N';
	}

	if($b_journal == 'on'){
		$b_journal = 'Y';
	}else{
		$b_journal = 'N';
	}

	if($i_journal == 'on'){
		$i_journal = 'Y';
	}else{
		$i_journal = 'N';
	}

	if($u_journal == 'on'){
		$u_journal = 'Y';
	}else{
		$u_journal = 'N';
	}

	if($b_journal_e == 'on'){
		$b_journal_e = 'Y';
	}else{
		$b_journal_e = 'N';
	}

	if($i_journal_e == 'on'){
		$i_journal_e = 'Y';
	}else{
		$i_journal_e = 'N';
	}

	if($u_journal_e == 'on'){
		$u_journal_e = 'Y';
	}else{
		$u_journal_e = 'N';
	}

	if($b_content == 'on'){
		$b_content = 'Y';
	}else{
		$b_content = 'N';
	}

	if($i_content == 'on'){
		$i_content = 'Y';
	}else{
		$i_content = 'N';
	}

	if($u_content == 'on'){
		$u_content = 'Y';
	}else{
		$u_content = 'N';
	}

	if($b_content_e == 'on'){
		$b_content_e = 'Y';
	}else{
		$b_content_e = 'N';
	}

	if($i_content_e == 'on'){
		$i_content_e = 'Y';
	}else{
		$i_content_e = 'N';
	}

	if($u_content_e == 'on'){
		$u_content_e = 'Y';
	}else{
		$u_content_e = 'N';
	}

	if($cid3 == ''){
		if($cid2 == ''){
			if($cid1 == ''){
				$bast_cate = $cid0;
			}else{
				$bast_cate = $cid1;
			}
		}else{
			$bast_cate = $cid2;
		}
	}else{
		$bast_cate = $cid3;
	}

	$unix_timestamp_main_sdate = mktime($main_start_h,$main_start_i,$main_start_s,substr($main_start,5,2),substr($main_start,8,2),substr($main_start,0,4));
	$unix_timestamp_main_edate = mktime($main_end_h,$main_end_i,$main_end_s,substr($main_end,5,2),substr($main_end,8,2),substr($main_end,0,4));

	$sql = "INSERT INTO shop_content_main
			(
			 mall_ix, subject, explanation, special_use, 
			 special_title, special_title_en, b_special, i_special, u_special, c_special, s_special, special_e, special_e_en, b_special_e, i_special_e, u_special_e, c_special_e, 
			 best_use, best_title, best_title_en, b_best, i_best, u_best, c_best, s_best, best_e, best_e_en, b_best_e, i_best_e, u_best_e, c_best_e, bast_cate,
			 style_use, style_title, style_title_en, b_style, i_style, u_style, c_style, s_style, style_e, style_e_en, b_style_e, i_style_e, u_style_e, c_style_e, 
			 journal_use, journal_title, journal_title_en, b_journal, i_journal, u_journal, c_journal, s_journal, journal_e, journal_e_en, b_journal_e, i_journal_e, u_journal_e, c_journal_e, 
			 content_use, content_title, content_title_en, b_content, i_content, u_content, c_content, s_content, content_e, content_e_en, b_content_e, i_content_e, u_content_e, c_content_e, 
			 main_use, main_default, 
			 main_start, main_end, worker_ix, regdate, upddate 
			)
			VALUES
    		(
    		 '".$mall_ix."', '".$subject."', '".$explanation."', '".$special_use."', 
			 '".$special_title."', '".$special_title_en."', '".$b_special."', '".$i_special."', '".$u_special."', '".$c_special."', '".$s_special."', '".$special_e."', '".$special_e_en."', '".$b_special_e."', '".$i_special_e."', '".$u_special_e."', '".$c_special_e."', 
			 '".$best_use."', '".$best_title."', '".$best_title_en."', '".$b_best."', '".$i_best."', '".$u_best."', '".$c_best."', '".$s_best."', '".$best_e."', '".$best_e_en."', '".$b_best_e."', '".$i_best_e."', '".$u_best_e."', '".$c_best_e."', '".$bast_cate."',
			 '".$style_use."', '".$style_title."', '".$style_title_en."', '".$b_style."', '".$i_style."', '".$u_style."', '".$c_style."', '".$s_style."', '".$style_e."', '".$style_e_en."', '".$b_style_e."', '".$i_style_e."', '".$u_style_e."', '".$c_style_e."',
			 '".$journal_use."', '".$journal_title."', '".$journal_title_en."', '".$b_journal."', '".$i_journal."', '".$u_journal."', '".$c_journal."', '".$s_journal."', '".$journal_e."', '".$journal_e_en."', '".$b_journal_e."', '".$i_journal_e."', '".$u_journal_e."', '".$c_journal_e."',
			 '".$content_use."', '".$content_title."', '".$content_title_en."', '".$b_content."', '".$i_content."', '".$u_content."', '".$c_content."', '".$s_content."', '".$content_e."', '".$content_e_en."', '".$b_content_e."', '".$i_content_e."', '".$u_content_e."', '".$c_content_e."',
			 '".$main_use."', '".$main_default."', 
			 '".$unix_timestamp_main_sdate."', '".$unix_timestamp_main_edate."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW()
			)
	";

	$db->sequences = "SHOP_CONTENT_MAIN_SEQ";
	$db->query($sql);

	$db->query("SELECT conm_ix FROM shop_content_main WHERE conm_ix=LAST_INSERT_ID()");
	$db->fetch();
	$conm_LAST_ID = $db->dt[conm_ix];

	if($_FILES['journal_frame_img']['name']){
		$frameUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/frame/".$conm_LAST_ID;

		$frameImgName	= "frameImg_".$conm_LAST_ID.".gif";
		$frameImgTmpName= $_FILES['journal_frame_img']['tmp_name'];

		if(!is_dir($frameUpDir)){
			mkdir($frameUpDir);
			chmod($frameUpDir,0777);
		}

		copy($frameImgTmpName, $frameUpDir."/".$frameImgName);

		$sql = "UPDATE shop_content_main SET journal_frame_img = '".$frameImgName."' WHERE conm_ix = '".$conm_LAST_ID."' ";
		$db->query($sql);
	}

	if($con_ix_1){
		foreach($con_ix_1 as $key => $val){
			$sql = "INSERT INTO shop_content_main_content (conm_ix, con_ix, content_gubun, sort, worker_ix, regdate, upddate) VALUES ('$conm_LAST_ID', '".$val."', 'E', '".$key."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
			$db->sequences = "SHOP_CONTENT_MAIN_RELATION_SEQ";
			$db->query($sql);
		}
	}

	if($con_ix_2){
		foreach($con_ix_2 as $key => $val){
			$sql = "INSERT INTO shop_content_main_content (conm_ix, con_ix, content_gubun, sort, worker_ix, regdate, upddate) VALUES ('$conm_LAST_ID', '".$val."', 'S', '".$key."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
			$db->sequences = "SHOP_CONTENT_MAIN_RELATION_SEQ";
			$db->query($sql);
		}
	}

	if($con_ix_3){
		foreach($con_ix_3 as $key => $val){
			$sql = "INSERT INTO shop_content_main_content (conm_ix, con_ix, content_gubun, sort, worker_ix, regdate, upddate) VALUES ('$conm_LAST_ID', '".$val."', 'C', '".$key."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
			$db->sequences = "SHOP_CONTENT_MAIN_RELATION_SEQ";
			$db->query($sql);
		}
	}

	foreach($_POST['group_order'] as $key => $val) {
		if ($_POST['b_group_title'][$val] == 'on') {
			$b_group_title = 'Y';
		} else {
			$b_group_title = 'N';
		}

		if ($_POST['i_group_title'][$val] == 'on') {
			$i_group_title = 'Y';
		} else {
			$i_group_title = 'N';
		}

		if ($_POST['u_group_title'][$val] == 'on') {
			$u_group_title = 'Y';
		} else {
			$u_group_title = 'N';
		}

		$c_group_title = $_POST['c_group_title'][$val];
		if ($_POST['c_group_title'][$val] == '') {
			$c_group_title = '#000000';
		}

		if ($_POST['b_group_explanation'][$val] == 'on') {
			$b_group_explanation = 'Y';
		} else {
			$b_group_explanation = 'N';
		}

		if ($_POST['i_group_explanation'][$val] == 'on') {
			$i_group_explanation = 'Y';
		} else {
			$i_group_explanation = 'N';
		}

		if ($_POST['u_group_explanation'][$val] == 'on') {
			$u_group_explanation = 'Y';
		} else {
			$u_group_explanation = 'N';
		}

		$c_group_explanation = $_POST['c_group_explanation'][$val];
		if ($_POST['c_group_explanation'][$val] == '') {
			$c_group_explanation = '#000000';
		}

		$unix_timestamp_group_display_sdate = mktime($_POST['group_display_start_h'][$val], $_POST['group_display_start_i'][$val], $_POST['group_display_start_s'][$val], substr($_POST['group_display_start'][$val], 5, 2), substr($_POST['group_display_start'][$val], 8, 2), substr($_POST['group_display_start'][$val], 0, 4));
		$unix_timestamp_group_display_edate = mktime($_POST['group_display_end_h'][$val], $_POST['group_display_end_i'][$val], $_POST['group_display_end_s'][$val], substr($_POST['group_display_end'][$val], 5, 2), substr($_POST['group_display_end'][$val], 8, 2), substr($_POST['group_display_end'][$val], 0, 4));

		$sql = "INSERT INTO shop_content_main_group_relation
				(
				 conm_ix, main_group_code, main_group_title, main_group_title_en, s_main_group_title, b_main_group_title, i_main_group_title, u_main_group_title, c_main_group_title,
				 main_group_explanation, main_group_explanation_en, b_main_group_explanation, i_main_group_explanation, u_main_group_explanation, c_main_group_explanation,
				 main_group_use, main_group_display_start, main_group_display_end,
				 worker_ix, regdate, upddate
				)
				VALUES
				('$conm_LAST_ID', '" . $val . "', '" . $_POST['group_title'][$val] . "', '" . $_POST['group_title_en'][$val] . "', '" . $_POST['s_group_title'][$val] . "', '" . $b_group_title . "', '" . $i_group_title . "', '" . $u_group_title . "', '" . $c_group_title . "',
				'" . $_POST['group_explanation'][$val] . "', '" . $_POST['group_explanation_en'][$val] . "', '" . $b_group_explanation . "', '" . $i_group_explanation . "', '" . $u_group_explanation . "', '" . $c_group_explanation . "',
				'" . $_POST['group_use'][$val] . "', '" . $unix_timestamp_group_display_sdate . "', '" . $unix_timestamp_group_display_edate . "',
				'" . $_SESSION["admininfo"]["charger_ix"] . "', NOW(), NOW())
		";
		$db->sequences = "SHOP_CONTENT_MAIN_GROUP_RELATION_SEQ";
		$db->query($sql);

		$db->query("SELECT cmgr_ix FROM shop_content_main_group_relation WHERE cmgr_ix=LAST_INSERT_ID()");
		$db->fetch();
		$cmgr_LAST_ID = $db->dt[cmgr_ix];

		if ($_POST['group_con_ix'][$val] != '') {
			foreach ($_POST['group_con_ix'][$val] as $key1 => $val1) {
				$sql = "INSERT INTO shop_content_main_group_content_relation (conm_ix, cmgr_ix, con_ix, group_con_gubun, sort, worker_ix, regdate, upddate) VALUES ('$conm_LAST_ID', '$cmgr_LAST_ID', '" . $_POST['group_con_ix'][$val][$key1] . "', '" . $_POST['group_con_gubun'][$val][$key1] . "', '" .$key1. "', '" . $_SESSION["admininfo"]["charger_ix"] . "', NOW(), NOW())";
				$db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
				$db->query($sql);
			}
		}
	}

	echo "<Script Language='JavaScript'>alert('등록되었습니다.');parent.document.location.href='main_goods.php';</Script>";
}

if ($act == "update"){
	if($b_special == 'on'){
		$b_special = 'Y';
	}else{
		$b_special = 'N';
	}

	if($i_special == 'on'){
		$i_special = 'Y';
	}else{
		$i_special = 'N';
	}

	if($u_special == 'on'){
		$u_special = 'Y';
	}else{
		$u_special = 'N';
	}

	if($b_special_e == 'on'){
		$b_special_e = 'Y';
	}else{
		$b_special_e = 'N';
	}

	if($i_special_e == 'on'){
		$i_special_e = 'Y';
	}else{
		$i_special_e = 'N';
	}

	if($u_special_e == 'on'){
		$u_special_e = 'Y';
	}else{
		$u_special_e = 'N';
	}

	if($b_best == 'on'){
		$b_best = 'Y';
	}else{
		$b_best = 'N';
	}

	if($i_best == 'on'){
		$i_best = 'Y';
	}else{
		$i_best = 'N';
	}

	if($u_best == 'on'){
		$u_best = 'Y';
	}else{
		$u_best = 'N';
	}

	if($b_best_e == 'on'){
		$b_best_e = 'Y';
	}else{
		$b_best_e = 'N';
	}

	if($i_best_e == 'on'){
		$i_best_e = 'Y';
	}else{
		$i_best_e = 'N';
	}

	if($u_best_e == 'on'){
		$u_best_e = 'Y';
	}else{
		$u_best_e = 'N';
	}

	if($b_style == 'on'){
		$b_style = 'Y';
	}else{
		$b_style = 'N';
	}

	if($i_style == 'on'){
		$i_style = 'Y';
	}else{
		$i_style = 'N';
	}

	if($u_style == 'on'){
		$u_style = 'Y';
	}else{
		$u_style = 'N';
	}

	if($b_style_e == 'on'){
		$b_style_e = 'Y';
	}else{
		$b_style_e = 'N';
	}

	if($i_style_e == 'on'){
		$i_style_e = 'Y';
	}else{
		$i_style_e = 'N';
	}

	if($u_style_e == 'on'){
		$u_style_e = 'Y';
	}else{
		$u_style_e = 'N';
	}

	if($b_journal == 'on'){
		$b_journal = 'Y';
	}else{
		$b_journal = 'N';
	}

	if($i_journal == 'on'){
		$i_journal = 'Y';
	}else{
		$i_journal = 'N';
	}

	if($u_journal == 'on'){
		$u_journal = 'Y';
	}else{
		$u_journal = 'N';
	}

	if($b_journal_e == 'on'){
		$b_journal_e = 'Y';
	}else{
		$b_journal_e = 'N';
	}

	if($i_journal_e == 'on'){
		$i_journal_e = 'Y';
	}else{
		$i_journal_e = 'N';
	}

	if($u_journal_e == 'on'){
		$u_journal_e = 'Y';
	}else{
		$u_journal_e = 'N';
	}

	if($b_content == 'on'){
		$b_content = 'Y';
	}else{
		$b_content = 'N';
	}

	if($i_content == 'on'){
		$i_content = 'Y';
	}else{
		$i_content = 'N';
	}

	if($u_content == 'on'){
		$u_content = 'Y';
	}else{
		$u_content = 'N';
	}

	if($b_content_e == 'on'){
		$b_content_e = 'Y';
	}else{
		$b_content_e = 'N';
	}

	if($i_content_e == 'on'){
		$i_content_e = 'Y';
	}else{
		$i_content_e = 'N';
	}

	if($u_content_e == 'on'){
		$u_content_e = 'Y';
	}else{
		$u_content_e = 'N';
	}

	if($cid3 == ''){
		if($cid2 == ''){
			if($cid1 == ''){
				$bast_cate = $cid0;
			}else{
				$bast_cate = $cid1;
			}
		}else{
			$bast_cate = $cid2;
		}
	}else{
		$bast_cate = $cid3;
	}

	$unix_timestamp_main_sdate = mktime($main_start_h,$main_start_i,$main_start_s,substr($main_start,5,2),substr($main_start,8,2),substr($main_start,0,4));
	$unix_timestamp_main_edate = mktime($main_end_h,$main_end_i,$main_end_s,substr($main_end,5,2),substr($main_end,8,2),substr($main_end,0,4));

	$sql = "UPDATE shop_content_main SET
			mall_ix = '$mall_ix', subject = '".$subject."', explanation = '".$explanation."', special_use = '".$special_use."',
            special_title = '".$special_title."', special_title_en = '".$special_title_en."', s_special = '".$s_special."', b_special = '".$b_special."', i_special = '".$i_special."', u_special = '".$u_special."', c_special = '".$c_special."',
			special_e = '".$special_e."', special_e_en = '".$special_e_en."', b_special_e = '".$b_special_e."', i_special_e = '".$i_special_e."', u_special_e = '".$u_special_e."', c_special_e = '".$c_special_e."',
			best_use = '".$best_use."', best_title = '".$best_title."', best_title_en = '".$best_title_en."', b_best = '".$b_best."', i_best = '".$i_best."', u_best = '".$u_best."', c_best = '".$c_best."', s_best = '".$s_best."', 
			best_e = '".$best_e."', best_e_en = '".$best_e_en."', b_best_e = '".$b_best_e."', i_best_e = '".$i_best_e."', u_best_e = '".$u_best_e."', c_best_e = '".$c_best_e."', bast_cate = '".$bast_cate."',
			style_use = '".$style_use."', style_title = '".$style_title."', style_title_en = '".$style_title_en."', b_style = '".$b_style."', i_style = '".$i_style."', u_style = '".$u_style."', c_style = '".$c_style."', s_style = '".$s_style."',
			style_e = '".$style_e."', style_e_en = '".$style_e_en."', b_style_e = '".$b_style_e."', i_style_e = '".$i_style_e."', u_style_e = '".$u_style_e."', c_style_e = '".$c_style_e."',
			journal_use = '".$journal_use."', journal_title = '".$journal_title."', journal_title_en = '".$journal_title_en."', b_journal = '".$b_journal."', i_journal = '".$i_journal."', u_journal = '".$u_journal."', c_journal = '".$c_journal."', s_journal = '".$s_journal."',
			journal_e = '".$journal_e."', journal_e_en = '".$journal_e_en."', b_journal_e = '".$b_journal_e."', i_journal_e = '".$i_journal_e."', u_journal_e = '".$u_journal_e."', c_journal_e = '".$c_journal_e."',
			content_use = '".$content_use."', content_title = '".$content_title."', content_title_en = '".$content_title_en."', b_content = '".$b_content."', i_content = '".$i_content."', u_content = '".$u_content."', c_content = '".$c_content."', s_content = '".$s_content."',
			content_e = '".$content_e."', content_e_en = '".$content_e_en."', b_content_e = '".$b_content_e."', i_content_e = '".$i_content_e."', u_content_e = '".$u_content_e."', c_content_e = '".$c_content_e."',
			main_use = '".$main_use."', main_default = '".$main_default."', main_start = '".$unix_timestamp_main_sdate."', main_end = '".$unix_timestamp_main_edate."', upddate = NOW()
		WHERE
			conm_ix = '$conm_ix'
 	";

	$db->query($sql);

	if($journal_frame_img_del == 'on' || $_FILES['journal_frame_img']['name']){
		$frameUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/frame/".$conm_ix;

		$frameImgName	= "frameImg_".$conm_ix.".gif";

		if (file_exists($frameUpDir . "/" .$frameImgName)) {
			unlink($frameUpDir . "/" .$frameImgName);
		}

		$sql = "UPDATE shop_content_main SET journal_frame_img = '' WHERE conm_ix = '".$conm_ix."' ";
		$db->query($sql);
	}

	if($_FILES['journal_frame_img']['name']){
		$frameUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/frame/".$conm_ix;

		$frameImgName	= "frameImg_".$conm_ix.".gif";
		$frameImgTmpName= $_FILES['journal_frame_img']['tmp_name'];

		if(!is_dir($frameUpDir)){
			mkdir($frameUpDir);
			chmod($frameUpDir,0777);
		}

		copy($frameImgTmpName, $frameUpDir."/".$frameImgName);

		$sql = "UPDATE shop_content_main SET journal_frame_img = '".$frameImgName."' WHERE conm_ix = '".$conm_ix."' ";
		$db->query($sql);
	}

	if($con_ix_1){
		$sql = "DELETE FROM shop_content_main_content WHERE conm_ix = '$conm_ix' AND content_gubun = 'E'";
		$db->query($sql);

		foreach($con_ix_1 as $key => $val){
			$sql = "INSERT INTO shop_content_main_content (conm_ix, con_ix, content_gubun, sort, worker_ix, regdate, upddate) VALUES ('$conm_ix', '".$val."', 'E', '".$key."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
			$db->sequences = "SHOP_CONTENT_MAIN_RELATION_SEQ";
			$db->query($sql);
		}
	}

	if($con_ix_2){
		$sql = "DELETE FROM shop_content_main_content WHERE conm_ix = '$conm_ix' AND content_gubun = 'S'";
		$db->query($sql);

		foreach($con_ix_2 as $key => $val){
			$sql = "INSERT INTO shop_content_main_content (conm_ix, con_ix, content_gubun, sort, worker_ix, regdate, upddate) VALUES ('$conm_ix', '".$val."', 'S', '".$key."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
			$db->sequences = "SHOP_CONTENT_MAIN_RELATION_SEQ";
			$db->query($sql);
		}
	}

	if($con_ix_3){
		$sql = "DELETE FROM shop_content_main_content WHERE conm_ix = '$conm_ix' AND content_gubun = 'C'";
		$db->query($sql);

		foreach($con_ix_3 as $key => $val){
			$sql = "INSERT INTO shop_content_main_content (conm_ix, con_ix, content_gubun, sort, worker_ix, regdate, upddate) VALUES ('$conm_ix', '".$val."', 'C', '".$key."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
			$db->sequences = "SHOP_CONTENT_MAIN_RELATION_SEQ";
			$db->query($sql);
		}
	}

	$sql = "DELETE FROM shop_content_main_group_relation WHERE conm_ix = '$conm_ix'";
	$db->query($sql);

	$sql = "DELETE FROM shop_content_main_group_content_relation WHERE conm_ix = '$conm_ix'";
	$db->query($sql);

	foreach($_POST['group_order'] as $key => $val) {
		//$cmgr_ix = $_POST['cmgr_ix'][$val];

		if ($_POST['b_group_title'][$val] == 'on') {
			$b_group_title = 'Y';
		} else {
			$b_group_title = 'N';
		}

		if ($_POST['i_group_title'][$val] == 'on') {
			$i_group_title = 'Y';
		} else {
			$i_group_title = 'N';
		}

		if ($_POST['u_group_title'][$val] == 'on') {
			$u_group_title = 'Y';
		} else {
			$u_group_title = 'N';
		}

		$c_group_title = $_POST['c_group_title'][$val];
		if ($_POST['c_group_title'][$val] == '') {
			$c_group_title = '#000000';
		}

		if ($_POST['b_group_explanation'][$val] == 'on') {
			$b_group_explanation = 'Y';
		} else {
			$b_group_explanation = 'N';
		}

		if ($_POST['i_group_explanation'][$val] == 'on') {
			$i_group_explanation = 'Y';
		} else {
			$i_group_explanation = 'N';
		}

		if ($_POST['u_group_explanation'][$val] == 'on') {
			$u_group_explanation = 'Y';
		} else {
			$u_group_explanation = 'N';
		}

		$c_group_explanation = $_POST['c_group_explanation'][$val];
		if ($_POST['c_group_explanation'][$val] == '') {
			$c_group_explanation = '#000000';
		}

		$unix_timestamp_group_display_sdate = mktime($_POST['group_display_start_h'][$val], $_POST['group_display_start_i'][$val], $_POST['group_display_start_s'][$val], substr($_POST['group_display_start'][$val], 5, 2), substr($_POST['group_display_start'][$val], 8, 2), substr($_POST['group_display_start'][$val], 0, 4));
		$unix_timestamp_group_display_edate = mktime($_POST['group_display_end_h'][$val], $_POST['group_display_end_i'][$val], $_POST['group_display_end_s'][$val], substr($_POST['group_display_end'][$val], 5, 2), substr($_POST['group_display_end'][$val], 8, 2), substr($_POST['group_display_end'][$val], 0, 4));

		$sql = "INSERT INTO shop_content_main_group_relation
				(
				 conm_ix, main_group_code, main_group_title, main_group_title_en, s_main_group_title, b_main_group_title, i_main_group_title, u_main_group_title, c_main_group_title,
				 main_group_explanation, main_group_explanation_en, b_main_group_explanation, i_main_group_explanation, u_main_group_explanation, c_main_group_explanation,
				 main_group_use, main_group_display_start, main_group_display_end,
				 worker_ix, regdate, upddate
				)
				VALUES
				('$conm_ix', '" . $val . "', '" . $_POST['group_title'][$val] . "', '" . $_POST['group_title_en'][$val] . "', '" . $_POST['s_group_title'][$val] . "', '" . $b_group_title . "', '" . $i_group_title . "', '" . $u_group_title . "', '" . $c_group_title . "',
				'" . $_POST['group_explanation'][$val] . "', '" . $_POST['group_explanation_en'][$val] . "', '" . $b_group_explanation . "', '" . $i_group_explanation . "', '" . $u_group_explanation . "', '" . $c_group_explanation . "',
				'" . $_POST['group_use'][$val] . "', '" . $unix_timestamp_group_display_sdate . "', '" . $unix_timestamp_group_display_edate . "',
				'" . $_SESSION["admininfo"]["charger_ix"] . "', NOW(), NOW())
		";
		$db->sequences = "SHOP_CONTENT_MAIN_GROUP_RELATION_SEQ";
		$db->query($sql);

		$db->query("SELECT cmgr_ix FROM shop_content_main_group_relation WHERE cmgr_ix=LAST_INSERT_ID()");
		$db->fetch();
		$cmgr_LAST_ID = $db->dt[cmgr_ix];

		if ($_POST['group_con_ix'][$val] != '') {
			foreach ($_POST['group_con_ix'][$val] as $key1 => $val1) {
				$sql = "INSERT INTO shop_content_main_group_content_relation (conm_ix, cmgr_ix, con_ix, group_con_gubun, sort, worker_ix, regdate, upddate) VALUES ('$conm_ix', '$cmgr_LAST_ID', '" . $_POST['group_con_ix'][$val][$key1] . "', '" . $_POST['group_con_gubun'][$val][$key1] . "', '" .$key1. "', '" . $_SESSION["admininfo"]["charger_ix"] . "', NOW(), NOW())";
				$db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
				$db->query($sql);
			}
		}

		/*$db->query("select count(*) as total from shop_content_main_group_relation where cmgr_ix = '$cmgr_ix' AND conm_ix = '$conm_ix' ");
		$db->fetch();

		if ($db->dt[total] > 0) {
			$sql = "UPDATE shop_content_main_group_relation SET
                    main_group_title = '".$_POST['group_title'][$val]."', main_group_title_en = '".$_POST['group_title_en'][$val]."', s_main_group_title = '".$_POST['s_group_title'][$val]."', b_main_group_title = '".$b_group_title."', i_main_group_title = '".$i_group_title."', u_main_group_title = '".$u_group_title."', c_main_group_title = '".$c_group_title."',
                    main_group_explanation = '".$_POST['group_explanation'][$val]."', main_group_explanation_en = '".$_POST['group_explanation_en'][$val]."', b_main_group_explanation = '".$b_group_explanation."', i_main_group_explanation = '".$i_group_explanation."', u_main_group_explanation = '".$u_group_explanation."', c_main_group_explanation = '".$c_group_explanation."',
					main_group_use = '".$_POST['group_use'][$val]."', 
					main_group_display_start = '$unix_timestamp_group_display_sdate', main_group_display_end = '$unix_timestamp_group_display_edate', worker_ix = '".$_SESSION["admininfo"]["charger_ix"]."', regdate = NOW()
				WHERE
					cmgr_ix = '$cmgr_ix' AND conm_ix = '$conm_ix'
            ";
		}else{
			$sql = "INSERT INTO shop_content_main_group_relation
				(
				 conm_ix, main_group_code, main_group_title, main_group_title_en, s_main_group_title, b_main_group_title, i_main_group_title, u_main_group_title, c_main_group_title,
				 main_group_explanation, main_group_explanation_en, b_main_group_explanation, i_main_group_explanation, u_main_group_explanation, c_main_group_explanation,
				 main_group_use, main_group_display_start, main_group_display_end,
				 worker_ix, regdate, upddate
				)
				VALUES
				('$conm_ix', '" . $val . "', '" . $_POST['group_title'][$val] . "', '" . $_POST['group_title_en'][$val] . "', '" . $_POST['s_group_title'][$val] . "', '" . $b_group_title . "', '" . $i_group_title . "', '" . $u_group_title . "', '" . $c_group_title . "',
				'" . $_POST['group_explanation'][$val] . "', '" . $_POST['group_explanation_en'][$val] . "', '" . $b_group_explanation . "', '" . $i_group_explanation . "', '" . $u_group_explanation . "', '" . $c_group_explanation . "',
				'" . $_POST['group_use'][$val] . "', '" . $unix_timestamp_group_display_sdate . "', '" . $unix_timestamp_group_display_edate . "',
				'" . $_SESSION["admininfo"]["charger_ix"] . "', NOW(), NOW())
			";
		}

		$db->query($sql);

		if ($_POST['group_con_ix'][$val] != '') {
			$sql = "DELETE FROM shop_content_main_group_content_relation WHERE cmgr_ix = '$cmgr_ix' AND conm_ix = '$conm_ix'";
			$db->query($sql);

			foreach ($_POST['group_con_ix'][$val] as $key1 => $val1) {
				$sql = "INSERT INTO shop_content_main_group_content_relation (conm_ix, cmgr_ix, con_ix, group_con_gubun, sort, worker_ix, regdate, upddate) VALUES ('$conm_ix', '$cmgr_ix', '" . $_POST['group_con_ix'][$val][$key1] . "', '" . $_POST['group_con_gubun'][$val][$key1] . "', '" .$key1. "', '" . $_SESSION["admininfo"]["charger_ix"] . "', NOW(), NOW())";
				$db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
				$db->query($sql);
			}
		}*/
	}

	echo "<Script Language='JavaScript'>alert('수정 되었습니다.');parent.document.location.href='main_goods.php?conm_ix=".$conm_ix."&act=".$act."';</Script>";
}

if ($act == "delete"){
	$sql = "DELETE FROM shop_content_main_group_content_relation WHERE conm_ix = '$conm_ix' ";
	$db->query($sql);

	$sql = "DELETE FROM shop_content_main_group_relation WHERE conm_ix = '$conm_ix' ";
	$db->query($sql);

	$sql = "DELETE FROM shop_content_main_content WHERE conm_ix = '$conm_ix' ";
	$db->query($sql);

	$frameUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/frame/".$conm_ix;

	$frameImgName	= "frameImg_".$conm_LAST_ID.".gif";

	if (file_exists($frameUpDir . "/" .$frameImgName)) {
		unlink($frameUpDir . "/" .$frameImgName);
	}

	$sql = "DELETE FROM shop_content_main WHERE conm_ix = '$conm_ix' ";
	$db->query($sql);

	echo "<Script Language='JavaScript'>alert('삭제 되었습니다.');parent.document.location.href='main_goods.list.php';</Script>";
}
exit;

if ($act == "vieworder_update"){
	for($i=0;$i < count($sortlist);$i++){
		$sql = "update ".TBL_SHOP_MAIN_PRODUCT_RELATION." set
			vieworder='".($i+1)."'
			where  pid='".$sortlist[$i]."' ";//main_ix='$main_ix' and

		//echo $sql;
		$db->query($sql);
	}

}

if ($act == "update" || $act == "insert"){
	//print_r($_POST);
	//exit;
	if(!$div_code){
		$sql = "SELECT div_code FROM shop_main_div where div_ix ='$div_ix' ";
		$db->query($sql); //AND cid='$cid'
		$db->fetch();
		$div_code = $db->dt[div_code];
	}

	$sql = "SELECT mg_use_sdate , mg_use_edate FROM shop_main_goods where mg_ix ='$mg_ix' ";

	$db->query($sql); //AND cid='$cid'

	if($db->total){

		$unix_timestamp_sdate = mktime($mg_use_sdate_h,$mg_use_sdate_i,$mg_use_sdate_s,substr($mg_use_sdate,5,2),substr($mg_use_sdate,8,2),substr($mg_use_sdate,0,4));
		$unix_timestamp_edate = mktime($mg_use_edate_h,$mg_use_edate_i,$mg_use_edate_s,substr($mg_use_edate,5,2),substr($mg_use_edate,8,2),substr($mg_use_edate,0,4));
		//echo $mg_use_edate_h.":".$mg_use_edate_i.":".$mg_use_edate_s;
		//exit;
		$sql = "update shop_main_goods set
				mall_ix = '".$mall_ix."',				
				mg_title='$mg_title',
				goods_max='$goods_max',
				image_width='$image_width',
				image_height='$image_height',
				mg_use_sdate='$unix_timestamp_sdate',
				mg_use_edate='$unix_timestamp_edate',
				md_mem_ix='".$md_code."',
				goal_amount='$goal_amount',
				mg_link='$mg_link',
				disp='$disp',
				div_ix='$div_ix',
				mp_ix='$mp_ix',
				cid='$cid'
				where mg_ix='$mg_ix'";


		$db->query($sql);

	}else{
		$sql = "SELECT mg_ix, mg_use_sdate , mg_use_edate FROM shop_main_goods where div_ix ='$div_ix' order by mg_use_edate desc limit 0,1";
		//echo $sql;
		//exit;
		$db->query($sql); //AND cid='$cid'

		$unix_timestamp_sdate = mktime($mg_use_sdate_h,$mg_use_sdate_i,$mg_use_sdate_s,substr($mg_use_sdate,5,2),substr($mg_use_sdate,8,2),substr($mg_use_sdate,0,4));
		$unix_timestamp_edate = mktime($mg_use_edate_h,$mg_use_edate_i,$mg_use_edate_s,substr($mg_use_edate,5,2),substr($mg_use_edate,8,2),substr($mg_use_edate,0,4));


		if($db->total){
			$db->fetch();



			if($db->dt[mg_use_edate] > $unix_timestamp_sdate && $priod_dupe_check){

				$set_use_sdate = mktime(0,0,0,date('m',$db->dt[mg_use_edate]),date('d',$db->dt[mg_use_edate])+1,date('Y',$db->dt[mg_use_edate]));
				$set_use_edate = mktime(0,0,0,date('m',$db->dt[mg_use_edate]),date('d',$db->dt[mg_use_edate])+10,date('Y',$db->dt[mg_use_edate]));

				echo("<script language='javascript'>alert('메인상품 노출 시작일자가 ".date("Y-m-d",$db->dt[mg_use_edate])." 일 이후여야 합니다. 시작일자를 ".date("Y-m-d",$set_use_sdate)." 로 설정합니다.');parent.select_date('".date("Y-m-d",$set_use_sdate)."','".date("Y-m-d",$set_use_edate)."',1);	</script>");
				exit;
			}else{


				$sql = "insert into shop_main_goods
					(mg_ix,mall_ix, agent_type, div_ix,mp_ix, cid,mg_title,goods_max,image_width, image_height,mg_use_sdate,mg_use_edate,md_mem_ix, goal_amount, disp, regdate)
					values
					('','".$mall_ix."','".$agent_type."','$div_ix','$mp_ix','$cid','$mg_title','$goods_max','$image_width','$image_height','$unix_timestamp_sdate','$unix_timestamp_edate','".$md_code."','$goal_amount','$disp',NOW())";
				$db->sequences = "SHOP_CT_MAIN_GOODS_SEQ";
				$db->query($sql);

				if($db->dbms_type == "oracle"){
					$mg_ix = $db->last_insert_id;
				}else{
					$db->query("SELECT mg_ix FROM shop_main_goods WHERE mg_ix=LAST_INSERT_ID()");
					$db->fetch();
					$mg_ix = $db->dt[mg_ix];
				}
			}
		}else{
			$sql = "insert into shop_main_goods
					(mg_ix,mall_ix, agent_type, div_ix,mp_ix, cid,mg_title,goods_max,image_width, image_height,mg_use_sdate,mg_use_edate,md_mem_ix, goal_amount, disp, regdate)
					values
					('','".$mall_ix."','".$agent_type."','$div_ix','$mp_ix','$cid','$mg_title','$goods_max','$image_width','$image_height','$unix_timestamp_sdate','$unix_timestamp_edate','".$md_code."','$goal_amount','$disp',NOW())";
			$db->sequences = "SHOP_CT_MAIN_GOODS_SEQ";
			$db->query($sql);

			if($db->dbms_type == "oracle"){
				$mg_ix = $db->last_insert_id;
			}else{
				$db->query("SELECT mg_ix FROM shop_main_goods WHERE mg_ix=LAST_INSERT_ID()");
				$db->fetch();
				$mg_ix = $db->dt[mg_ix];
			}
		}
	}


	$sql = "update ".TBL_SHOP_MAIN_PRODUCT_RELATION." set insert_yn='N' where mg_ix = '".$mg_ix."'  ";
	$db->query($sql);

	$db->query("update shop_main_brand_relation set insert_yn = 'N'  where mg_ix = '".$mg_ix."'  ");
	$db->query("update shop_main_category_relation set insert_yn = 'N'  where mg_ix = '".$mg_ix."' ");

	$sql = "update ".TBL_SHOP_MAIN_PRODUCT_GROUP." set insert_yn='N' where mg_ix = '".$mg_ix."'  ";
	$db->query($sql);

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	if ($mg_title_img_del == "Y")
	{
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_title_img_".$mg_ix.".jpg");
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_title_img_".$mg_ix.".jpg");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	}

	if ($_FILES["mg_title_img"]["size"] > 0)
	{
		copy($_FILES["mg_title_img"][tmp_name], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_title_img_".$mg_ix.".jpg");
	}

	//	$db->debug = true;
	for($i=0;$i < count($group_name);$i++){
		$db->query("Select mpg_ix from ".TBL_SHOP_MAIN_PRODUCT_GROUP." where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' ");

		if($db->total){
			$db->fetch();
			$mpg_ix = $db->dt[mpg_ix];

			$sql = "update ".TBL_SHOP_MAIN_PRODUCT_GROUP." set
							div_code='".$div_code."',
							group_name='".$group_name[$i+1]."',
							banner_position='".$banner_position[$i+1]."',
							display_type='".$display_type[$i+1]."',
							insert_yn='Y', use_yn='".$use_yn[$i+1]."',
							group_link='".$group_link[$i+1]."',
							product_cnt='".$product_cnt[$i+1]."',
							goods_display_type='".$goods_display_type[$i+1]."',
							goods_display_sub_type='".$goods_display_sub_type[$i+1]."',
							md_mem_ix='".$md_mem_ix[$i+1]."',							
							display_auto_type='".$display_auto_type[$i+1]."',
							display_auto_priod='".$display_auto_priod[$i+1]."',
							vieworder= '".$vieworder[$i+1]."'
							where mpg_ix='".$mpg_ix."' and mg_ix = '".$mg_ix."'  and group_code = '".($i+1)."' ";


			$db->query($sql);



		}else{
			$sql = "insert into ".TBL_SHOP_MAIN_PRODUCT_GROUP." 
						(mpg_ix,div_code, group_name,banner_position,mg_ix, group_code,group_link,display_type,product_cnt, goods_display_type, goods_display_sub_type, display_auto_type,display_auto_priod,md_mem_ix, insert_yn, use_yn,vieworder, regdate) 
						values
						('','".$div_code."','".$group_name[$i+1]."','".$banner_position[$i+1]."','".$mg_ix."','".($i+1)."','".$group_link[$i+1]."','".$display_type[$i+1]."','".$product_cnt[$i+1]."','".$goods_display_type[$i+1]."','".$goods_display_sub_type[$i+1]."','".$display_auto_type[$i+1]."','".$display_auto_priod[$i+1]."','".$md_mem_ix[$i+1]."','Y','".$use_yn[$i+1]."','".$vieworder[$i+1]."',NOW())";
			$db->sequences = "SHOP_MAIN_PRODUCT_GROUP_SEQ";
			$db->query($sql);

			$db->query("SELECT mpg_ix FROM  ".TBL_SHOP_MAIN_PRODUCT_GROUP."  WHERE mpg_ix=LAST_INSERT_ID()");
			$db->fetch();
			$mpg_ix = $db->dt[mpg_ix];
		}

		//$db->debug = true;
		//print_r($_POST);
		//exit;
		$db->query("update shop_main_group_display set insert_yn = 'N' where mpg_ix = '".$mpg_ix."'   ");

		for($j=0;$j < count($display_type[$i+1][type]);$j++){
			$db->query("select mgd_ix from shop_main_group_display where mgd_ix = '".$display_type[$i+1][mgd_ix][$j]."'   ");

			if(!$db->total){
				$sql = "insert into shop_main_group_display (mgd_ix,mpg_ix, display_type, set_cnt, vieworder, insert_yn, regdate) values ('','".$mpg_ix."','".$display_type[$i+1][type][$j]."','".$display_type[$i+1][set_cnt][$j]."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_main_group_display set insert_yn = 'Y',vieworder='".($j+1)."', display_type = '".$display_type[$i+1][type][$j]."',set_cnt = '".$display_type[$i+1][set_cnt][$j]."' 
							where mgd_ix = '".$display_type[$i+1][mgd_ix][$j]."'  ";
				$db->query($sql);
			}
		}
		$db->query("delete from shop_main_group_display where mpg_ix = '".$mpg_ix."' and insert_yn = 'N' ");
		//$db->debug = false;
		//exit;

		if ($group_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_".($i+1).".gif");
			@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/".$mg_ix."_main_group_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		if ($group_over_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_over_".($i+1).".gif");
			@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/".$mg_ix."_main_group_over_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}



		//echo "size:".$_FILES["group_img"]["size"][$i+1]."<br />";
		//print_r($_FILES);
		//exit;

		if ($_FILES["group_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_".($i+1).".gif");
			@copy($_FILES["group_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/".$mg_ix."_main_group_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		if ($_FILES["group_over_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_over_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_over_".($i+1).".gif");
			@copy($_FILES["group_over_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/".$mg_ix."_main_group_over_".($i+1).".gif");//메인분류추가에 따른 이미
		}

		if ($group_banner_img_del[$i+1] == "Y")
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_banner_".($i+1).".gif");
			@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/".$mg_ix."_main_group_banner_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		if ($_FILES["group_banner_img"]["size"][$i+1] > 0)
		{
			copy($_FILES["group_banner_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_banner_".($i+1).".gif");
			@copy($_FILES["group_banner_img"][tmp_name][$i+1], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/".$mg_ix."_main_group_banner_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
		}

		//$db->query("SELECT mpg_ix FROM ".tbl_shop_main_product_group." WHERE mpg_ix=LAST_INSERT_ID()");
		//$db->fetch();
		//$mpg_ix = $db->dt[0];

		//$db->query("update ".TBL_SHOP_MAIN_PRODUCT_RELATION." set insert_yn = 'N' where main_ix='".$main_ix."' and group_code = '".($i+1)."' ");

		for($j=0;$j < count($rpid[$i+1]);$j++){
			$db->query("select mpr_ix from ".TBL_SHOP_MAIN_PRODUCT_RELATION." where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' ");

			if(!$db->total){
				$sql = "insert into ".TBL_SHOP_MAIN_PRODUCT_RELATION." (mpr_ix,pid,mg_ix, div_code, group_code, vieworder, insert_yn, regdate) values ('','".$rpid[$i+1][$j]."','".$mg_ix."','".$div_code."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update ".TBL_SHOP_MAIN_PRODUCT_RELATION." set insert_yn = 'Y',vieworder='".($j+1)."', div_code = '".$div_code."',group_code = '".($i+1)."' where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and pid = '".$rpid[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from ".TBL_SHOP_MAIN_PRODUCT_RELATION." where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and insert_yn = 'N' ");


		/**
		* 노출카테고리 관련
		* 담당자 : shs
		*/

		$db->query("update shop_main_category_relation set insert_yn = 'N'  where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' ");

		for($j=0;$j < count($category[$i+1]);$j++){
			$db->query("select mcr_ix from shop_main_category_relation where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_main_category_relation (mcr_ix,cid,mg_ix, group_code, vieworder, insert_yn, regdate) values ('','".$category[$i+1][$j]."','".$mg_ix."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_CT_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_main_category_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and cid = '".$category[$i+1][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_main_category_relation where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and insert_yn = 'N' ");

		/**
		* 노출 브랜드 관련
		* 담당자 : shs
		* 작업일시 : 2014년 03월 30일
		*/
		//$db->debug = true;
		$db->query("update shop_main_brand_relation set insert_yn = 'N'  where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' ");

		for($j=0;$j < count($selected_result[$i+1]['brand']);$j++){
			$db->query("select mbr_ix from shop_main_brand_relation where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and b_ix = '".$selected_result[$i+1]['brand'][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_main_brand_relation (mbr_ix,b_ix,mg_ix, group_code, vieworder, insert_yn, regdate) values ('','".$selected_result[$i+1]['brand'][$j]."','".$mg_ix."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_BRAND_SEQ";
				$db->query($sql);
			}else{
				$db->fetch();
				$mbr_ix = $db->dt[mbr_ix];
				$sql = "update shop_main_brand_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where mbr_ix = '".$mbr_ix."' and mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and b_ix = '".$selected_result[$i+1]['brand'][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_main_brand_relation where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and insert_yn = 'N' ");
	//exit;
		/**
		* 노출 셀러 관련
		* 담 당  자 : shs
		* 작업일시 : 2014년 03월 30일
		*/
		//$db->debug = true;
		$db->query("update shop_main_seller_relation set insert_yn = 'N'  where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' ");

		for($j=0;$j < count($selected_result[$i+1]['seller']);$j++){
			$db->query("select msr_ix from shop_main_seller_relation where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and company_id = '".$selected_result[$i+1]['seller'][$j]."' ");

			if(!$db->total){
				$sql = "insert into shop_main_seller_relation (msr_ix,company_id,mg_ix, group_code, vieworder, insert_yn, regdate) values ('','".$selected_result[$i+1]['seller'][$j]."','".$mg_ix."','".($i+1)."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_SELLER_SEQ";
				$db->query($sql);
			}else{
				$db->fetch();
				$msr_ix = $db->dt[msr_ix];
				$sql = "update shop_main_seller_relation set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".($i+1)."' where msr_ix = '".$msr_ix."' and  mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and company_id = '".$selected_result[$i+1]['seller'][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from shop_main_seller_relation where mg_ix = '".$mg_ix."' and group_code = '".($i+1)."' and insert_yn = 'N' ");
	//	exit;
	}
	//exit;
	$db->query("delete from shop_main_brand_relation where mg_ix = '".$mg_ix."' and insert_yn = 'N' ");
	$db->query("delete from shop_main_seller_relation where mg_ix = '".$mg_ix."' and insert_yn = 'N' ");

	$db->query("delete from ".TBL_SHOP_MAIN_PRODUCT_RELATION." where mg_ix = '".$mg_ix."' and insert_yn = 'N' ");
	$db->query("delete from ".TBL_SHOP_MAIN_PRODUCT_GROUP." where mg_ix = '".$mg_ix."' and insert_yn = 'N' ");

	if($delete_cache == "Y"){
		include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php");
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cache/";

		$tpl->clearCache('000000000000000');
	}
	if($agent_type == "M"){
		echo("<script>top.location.href = '../mShop/main_goods.php?mmode=$mmode&mg_ix=$mg_ix';</script>");
	}else{
		echo("<script>top.location.href = 'main_goods.php?mmode=$mmode&mg_ix=$mg_ix';</script>");
	}

} else if ($act == "delete"){//삭제 추가 kbk 13/11/21
	if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_title_img_".($mg_ix).".gif")) {
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_title_img_".$mg_ix.".jpg");
		@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_title_img_".$mg_ix.".jpg");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
	}

	$sql="SELECT group_code FROM shop_main_product_group WHERE mg_ix='".$mg_ix."' ";
	$db->query($sql);
	$group_cnt=$db->total;
	if($db->total) {
		//$group_fetch=$db->fetchall();
		for($i=0;$i<$group_cnt;$i++) {
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_".($i+1).".gif")) {
				chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_".($i+1).".gif", 0777);
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_".($i+1).".gif");
				@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/".$mg_ix."_main_group_".($i+1).".gif");
			}

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_over_".($i+1).".gif")) {
				chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_over_".($i+1).".gif", 0777);
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_over_".($i+1).".gif");
				@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/".$mg_ix."_main_group_over_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
			}

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_banner_".($i+1).".gif")) {
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/main_group_banner_".($i+1).".gif");
				@unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/main/".$mg_ix."_main_group_banner_".($i+1).".gif");//메인분류추가에 따른 이미지 명 변경 kbk 13/05/09
			}
		}
	}
	$db->query("DELETE FROM shop_main_goods WHERE mg_ix='".$mg_ix."' ");
	$db->query("delete from shop_main_product_relation where mg_ix = '".$mg_ix."' ");
	$db->query("delete from shop_main_product_group where mg_ix = '".$mg_ix."' ");

	if($agent_type == "M"){
		echo("<script>parent.location.href = '../mShop/main_goods.list.php';</script>");
	}else{
		echo("<script>parent.location.href = '../display/main_goods.list.php';</script>");
	}

	exit;
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