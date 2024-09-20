<?php
include("../class/layout.class");
////////////////////
//  2013.05.07 신훈식
//  수정 : 인클루드 패스 오류
//
/////////////////////
//include("../class/database.class");
include_once('../../include/xmlWriter.php');
session_start();

$db = new Database;
$db2 = new Database;

// 컨텐츠 등록
if($mode == "Ins"){
    if(empty($company_id)){
        $company_id = $admininfo[company_id];
    }

    if($content_type == 1){
        if($b_title == 'on'){
            $b_title = 'Y';
        }else{
            $b_title = 'N';
        }

        if($i_title == 'on'){
            $i_title = 'Y';
        }else{
            $i_title = 'N';
        }

        if($u_title == 'on'){
            $u_title = 'Y';
        }else{
            $u_title = 'N';
        }

        if($b_preface == 'on'){
            $b_preface = 'Y';
        }else{
            $b_preface = 'N';
        }

        if($i_preface == 'on'){
            $i_preface = 'Y';
        }else{
            $i_preface = 'N';
        }

        if($u_preface == 'on'){
            $u_preface = 'Y';
        }else{
            $u_preface = 'N';
        }

        if($b_explanation == 'on'){
            $b_explanation = 'Y';
        }else{
            $b_explanation = 'N';
        }

        if($i_explanation == 'on'){
            $i_explanation = 'Y';
        }else{
            $i_explanation = 'N';
        }

        if($u_explanation == 'on'){
            $u_explanation = 'Y';
        }else{
            $u_explanation = 'N';
        }

        if($b_category == 'on'){
            $b_category = 'Y';
        }else{
            $b_category = 'N';
        }

        if($i_category == 'on'){
            $i_category = 'Y';
        }else{
            $i_category = 'N';
        }

        if($u_category == 'on'){
            $u_category = 'Y';
        }else{
            $u_category = 'N';
        }

        if($e_category == 'on'){
            $e_category = 'Y';
        }else{
            $e_category = 'N';
        }

        $unix_timestamp_display_sdate = mktime($display_start_h,$display_start_i,$display_start_s,substr($display_start,5,2),substr($display_start,8,2),substr($display_start,0,4));
        $unix_timestamp_display_edate = mktime($display_end_h,$display_end_i,$display_end_s,substr($display_end,5,2),substr($display_end,8,2),substr($display_end,0,4));

        $sql = "INSERT INTO shop_content
                (cid, depth, mall_ix, company_id, title, title_en, s_title, b_title, i_title, u_title, c_title,
                 preface, preface_en, b_preface, i_preface, u_preface, c_preface, explanation, explanation_en, b_explanation, i_explanation, u_explanation, c_explanation,
                 category_use, b_category, i_category, u_category, c_category, r_category, e_category, ba_category, bo_category, display_gubun, display_use, display_date_use, 
                 display_state, display_start, display_end, comment_board_ix, content_text_pc, content_text_mo, worker_ix, sort, regdate, upddate
                 )
                VALUES
                ('$cid', '$this_depth', '$mall_ix', '".$company_id."', '".$title."', '".$title_en."', '".$s_title."', '".$b_title."', '".$i_title."', '".$u_title."', '".$c_title."',
                '".$preface."', '".$preface_en."', '".$b_preface."', '".$i_preface."', '".$u_preface."', '".$c_preface."', '".$explanation."', '".$explanation_en."', '".$b_explanation."', '".$i_explanation."', '".$u_explanation."', '".$c_explanation."',
                '".$category_use."', '".$b_category."', '".$i_category."', '".$u_category."', '".$c_category."', '".$r_category."', '".$e_category."', '".$ba_category."', '".$bo_category."', '".$display_gubun."', '".$display_use."', '".$display_date_use."',
                '".$display_state."', '$unix_timestamp_display_sdate', '$unix_timestamp_display_edate', '".$comment_board_ix."', '".$content_text_pc."', '".$content_text_mo."', '".$_SESSION["admininfo"]["charger_ix"]."', '".($sort == "" ? "999" : $sort )."',NOW(),NOW()
                )
        ";

        $db->sequences = "SHOP_CONTENT_SEQ";
        $db->query($sql);

        $db->query("SELECT con_ix FROM shop_content WHERE con_ix=LAST_INSERT_ID()");
        $db->fetch();
        $con_LAST_ID = $db->dt[con_ix];

        if($_FILES['list_img']['name']){
            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_LAST_ID;

            $listImgName	= "listImg_".date('YmdHis', time())."_".$con_LAST_ID.".gif";
            $listImgTmpName	= $_FILES['list_img']['tmp_name'];

            if(!is_dir($listUpDir)){
                mkdir($listUpDir);
                chmod($listUpDir,0777);
            }

            copy($listImgTmpName, $listUpDir."/".$listImgName);

            $sql = "UPDATE shop_content SET list_img = '".$listImgName."' WHERE con_ix = '".$con_LAST_ID."' ";
            $db->query($sql);
        }

        if($_FILES['recommend_img']['name']){
            $recomUpDir	        = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_LAST_ID;

            $recomImgName	    = "recomImg_".date('YmdHis', time())."_".$con_LAST_ID.".gif";
            $recomImgTmpName	= $_FILES['recommend_img']['tmp_name'];

            if(!is_dir($recomUpDir)){
                mkdir($recomUpDir);
                chmod($recomUpDir,0777);
            }

            copy($recomImgTmpName, $recomUpDir."/".$recomImgName);

            $sql = "UPDATE shop_content SET recommend_img = '".$recomImgName."' WHERE con_ix = '".$con_LAST_ID."' ";
            $db->query($sql);
        }

        if($_POST['category']){
            foreach($_POST['category'] as $key => $val){
                $sql = "INSERT INTO shop_content_relation (con_ix, cid, worker_ix, regdate, upddate) VALUES ('$con_LAST_ID', '".$val."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_RELATION_SEQ";
                $db->query($sql);
            }
        }

        if($_POST['rpid'][9999]){
            foreach($_POST['rpid'][9999] as $key => $val){
                $sql = "INSERT INTO shop_content_product_relation (con_ix, pid, sort, worker_ix, regdate, upddate) VALUES ('$con_LAST_ID', '".$val."', '".$key."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_PRODUCT_RELATION_SEQ";
                $db->query($sql);
            }
        }

        foreach($_POST['group_order'] as $key => $val){
            if($_POST['b_group_title'][$val] == 'on'){
                $b_group_title = 'Y';
            }else{
                $b_group_title = 'N';
            }

            if($_POST['i_group_title'][$val] == 'on'){
                $i_group_title = 'Y';
            }else{
                $i_group_title = 'N';
            }

            if($_POST['u_group_title'][$val] == 'on'){
                $u_group_title = 'Y';
            }else{
                $u_group_title = 'N';
            }

            $c_group_title = $_POST['c_group_title'][$val];
            if($_POST['c_group_title'][$val] == ''){
                $c_group_title = '#000000';
            }

            if($_POST['b_group_preface'][$val] == 'on'){
                $b_group_preface = 'Y';
            }else{
                $b_group_preface = 'N';
            }

            if($_POST['i_group_preface'][$val] == 'on'){
                $i_group_preface = 'Y';
            }else{
                $i_group_preface = 'N';
            }

            if($_POST['u_group_preface'][$val] == 'on'){
                $u_group_preface = 'Y';
            }else{
                $u_group_preface = 'N';
            }

            $c_group_preface = $_POST['c_group_preface'][$val];
            if($_POST['c_group_preface'][$val] == ''){
                $c_group_preface = '#000000';
            }

            if($_POST['b_group_explanation'][$val] == 'on'){
                $b_group_explanation = 'Y';
            }else{
                $b_group_explanation = 'N';
            }

            if($_POST['i_group_explanation'][$val] == 'on'){
                $i_group_explanation = 'Y';
            }else{
                $i_group_explanation = 'N';
            }

            if($_POST['u_group_explanation'][$val] == 'on'){
                $u_group_explanation = 'Y';
            }else{
                $u_group_explanation = 'N';
            }

            $c_group_explanation = $_POST['c_group_explanation'][$val];
            if($_POST['c_group_explanation'][$val] == ''){
                $c_group_explanation = '#000000';
            }

            $unix_timestamp_group_display_sdate = mktime($_POST['group_display_start_h'][$val],$_POST['group_display_start_i'][$val],$_POST['group_display_start_s'][$val],substr($_POST['group_display_start'][$val],5,2),substr($_POST['group_display_start'][$val],8,2),substr($_POST['group_display_start'][$val],0,4));
            $unix_timestamp_group_display_edate = mktime($_POST['group_display_end_h'][$val],$_POST['group_display_end_i'][$val],$_POST['group_display_end_s'][$val],substr($_POST['group_display_end'][$val],5,2),substr($_POST['group_display_end'][$val],8,2),substr($_POST['group_display_end'][$val],0,4));

            $sql = "INSERT INTO shop_content_group_relation
                    (con_ix, group_code, group_title, group_title_en, s_group_title, b_group_title, i_group_title, u_group_title, c_group_title,
                     group_preface, group_preface_en, b_group_preface, i_group_preface, u_group_preface, c_group_preface,
                     group_explanation, group_explanation_en, b_group_explanation, i_group_explanation, u_group_explanation, c_group_explanation,
                     group_text_pc, group_text_mo, group_use, group_display_start, group_display_end,
                     worker_ix, regdate, upddate)
                    VALUES
                    ('$con_LAST_ID', '".$val."', '".$_POST['group_title'][$val]."', '".$_POST['group_title_en'][$val]."', '".$_POST['s_group_title'][$val]."', '".$b_group_title."', '".$i_group_title."', '".$u_group_title."', '".$c_group_title."',
                    '".$_POST['group_preface'][$val]."', '".$_POST['group_preface_en'][$val]."', '".$b_group_preface."', '".$i_group_preface."', '".$u_group_preface."', '".$c_group_preface."',
                    '".$_POST['group_explanation'][$val]."', '".$_POST['group_explanation_en'][$val]."', '".$b_group_explanation."', '".$i_group_explanation."', '".$u_group_explanation."', '".$c_group_explanation."',
                    '".$_POST['group_text_pc'][$val]."', '".$_POST['group_text_mo'][$val]."', '".$_POST['group_use'][$val]."', '".$unix_timestamp_group_display_sdate."', '".$unix_timestamp_group_display_edate."',
                    '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())
            ";
            $db->sequences = "SHOP_CONTENT_GROUP_RELATION_SEQ";
            $db->query($sql);

            $db->query("SELECT cgr_ix FROM shop_content_group_relation WHERE cgr_ix=LAST_INSERT_ID()");
            $db->fetch();
            $cgr_LAST_ID = $db->dt[cgr_ix];

            foreach($_POST['rpid'][$val] as $key1 => $val1){
                $sql = "INSERT INTO shop_content_group_product_relation (con_ix, cgr_ix, pid, sort, worker_ix, regdate, upddate) VALUES('$con_LAST_ID', '$cgr_LAST_ID', '".$val1."', '".$key1."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
                $db->query($sql);
            }
        }
    }else if($content_type == 2){
        if($b_title == 'on'){
            $b_title = 'Y';
        }else{
            $b_title = 'N';
        }

        if($i_title == 'on'){
            $i_title = 'Y';
        }else{
            $i_title = 'N';
        }

        if($u_title == 'on'){
            $u_title = 'Y';
        }else{
            $u_title = 'N';
        }

        if($b_preface == 'on'){
            $b_preface = 'Y';
        }else{
            $b_preface = 'N';
        }

        if($i_preface == 'on'){
            $i_preface = 'Y';
        }else{
            $i_preface = 'N';
        }

        if($u_preface == 'on'){
            $u_preface = 'Y';
        }else{
            $u_preface = 'N';
        }

        if($b_explanation == 'on'){
            $b_explanation = 'Y';
        }else{
            $b_explanation = 'N';
        }

        if($i_explanation == 'on'){
            $i_explanation = 'Y';
        }else{
            $i_explanation = 'N';
        }

        if($u_explanation == 'on'){
            $u_explanation = 'Y';
        }else{
            $u_explanation = 'N';
        }

        $unix_timestamp_display_sdate = mktime($display_start_h,$display_start_i,$display_start_s,substr($display_start,5,2),substr($display_start,8,2),substr($display_start,0,4));
        $unix_timestamp_display_edate = mktime($display_end_h,$display_end_i,$display_end_s,substr($display_end,5,2),substr($display_end,8,2),substr($display_end,0,4));

        $sql = "INSERT INTO shop_content
                (cid, depth, mall_ix, company_id, title, title_en, s_title, b_title, i_title, u_title, c_title,
                 preface, preface_en, b_preface, i_preface, u_preface, c_preface, explanation, explanation_en, b_explanation, i_explanation, u_explanation, c_explanation,
                 display_use, display_date_use, display_state, display_start, display_end, worker_ix, sort, regdate, upddate
                 )
                VALUES
                ('$cid', '$this_depth', '$mall_ix', '".$company_id."', '".$title."', '".$title_en."', '".$s_title."', '".$b_title."', '".$i_title."', '".$u_title."', '".$c_title."',
                '".$preface."', '".$preface_en."', '".$b_preface."', '".$i_preface."', '".$u_preface."', '".$c_preface."', '".$explanation."', '".$explanation_en."', '".$b_explanation."', '".$i_explanation."', '".$u_explanation."', '".$c_explanation."',
                '".$display_use."', '".$display_date_use."', '".$display_state."', '$unix_timestamp_display_sdate', '$unix_timestamp_display_edate', '".$_SESSION["admininfo"]["charger_ix"]."', '".($sort == "" ? "999" : $sort )."',NOW(),NOW()
                )
        ";

        $db->sequences = "SHOP_CONTENT_SEQ";
        $db->query($sql);

        $db->query("SELECT con_ix FROM shop_content WHERE con_ix=LAST_INSERT_ID()");
        $db->fetch();
        $con_LAST_ID = $db->dt[con_ix];

        if($_FILES['list_img']['name']){
            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_LAST_ID;

            $listImgName	= "listImg_".date('YmdHis', time())."_".$con_LAST_ID.".gif";
            $listImgTmpName	= $_FILES['list_img']['tmp_name'];

            if(!is_dir($listUpDir)){
                mkdir($listUpDir);
                chmod($listUpDir,0777);
            }

            copy($listImgTmpName, $listUpDir."/".$listImgName);

            $sql = "UPDATE shop_content SET list_img = '".$listImgName."' WHERE con_ix = '".$con_LAST_ID."' ";
            $db->query($sql);
        }

        if($_FILES['list_img_m']['name']){
            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_LAST_ID;

            $listImgMName	= "listImg_m_".date('YmdHis', time())."_".$con_LAST_ID.".gif";
            $listImgMTmpName	= $_FILES['list_img_m']['tmp_name'];

            if(!is_dir($listUpDir)){
                mkdir($listUpDir);
                chmod($listUpDir,0777);
            }

            copy($listImgMTmpName, $listUpDir."/".$listImgMName);

            $sql = "UPDATE shop_content SET list_img_m = '".$listImgMName."' WHERE con_ix = '".$con_LAST_ID."' ";
            $db->query($sql);
        }

        if($_POST['imgInsYN']){
            CopyImage($con_LAST_ID, "","style");
        }

        foreach($_POST['group_order'] as $key => $val){
            if($_POST['b_group_title'][$val] == 'on'){
                $b_group_title = 'Y';
            }else{
                $b_group_title = 'N';
            }

            if($_POST['i_group_title'][$val] == 'on'){
                $i_group_title = 'Y';
            }else{
                $i_group_title = 'N';
            }

            if($_POST['u_group_title'][$val] == 'on'){
                $u_group_title = 'Y';
            }else{
                $u_group_title = 'N';
            }

            $c_group_title = $_POST['c_group_title'][$val];
            if($_POST['c_group_title'][$val] == ''){
                $c_group_title = '#000000';
            }

            $unix_timestamp_group_display_sdate = mktime($_POST['group_display_start_h'][$val],$_POST['group_display_start_i'][$val],$_POST['group_display_start_s'][$val],substr($_POST['group_display_start'][$val],5,2),substr($_POST['group_display_start'][$val],8,2),substr($_POST['group_display_start'][$val],0,4));
            $unix_timestamp_group_display_edate = mktime($_POST['group_display_end_h'][$val],$_POST['group_display_end_i'][$val],$_POST['group_display_end_s'][$val],substr($_POST['group_display_end'][$val],5,2),substr($_POST['group_display_end'][$val],8,2),substr($_POST['group_display_end'][$val],0,4));

            $sql = "INSERT INTO shop_content_group_relation
                    (con_ix, group_code, group_title, group_title_en, s_group_title, b_group_title, i_group_title, u_group_title, c_group_title,
                     group_use, group_display_start, group_display_end,
                     worker_ix, regdate, upddate)
                    VALUES
                    ('$con_LAST_ID', '".$val."', '".$_POST['group_title'][$val]."', '".$_POST['group_title_en'][$val]."', '".$_POST['s_group_title'][$val]."', '".$b_group_title."', '".$i_group_title."', '".$u_group_title."', '".$c_group_title."',
                    '".$_POST['group_use'][$val]."', '".$unix_timestamp_group_display_sdate."', '".$unix_timestamp_group_display_edate."',
                    '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())
            ";
            $db->sequences = "SHOP_CONTENT_GROUP_RELATION_SEQ";
            $db->query($sql);

            $db->query("SELECT cgr_ix FROM shop_content_group_relation WHERE cgr_ix=LAST_INSERT_ID()");
            $db->fetch();
            $cgr_LAST_ID = $db->dt[cgr_ix];

            foreach($_POST['rpid'][$val] as $key1 => $val1){
                $sql = "INSERT INTO shop_content_group_product_relation (con_ix, cgr_ix, pid, sort, worker_ix, regdate, upddate) VALUES ('$con_LAST_ID', '$cgr_LAST_ID', '".$val1."', '".$key1."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
                $db->query($sql);
            }
        }
    }else if($content_type == 3){
        if($b_title == 'on'){
            $b_title = 'Y';
        }else{
            $b_title = 'N';
        }

        if($i_title == 'on'){
            $i_title = 'Y';
        }else{
            $i_title = 'N';
        }

        if($u_title == 'on'){
            $u_title = 'Y';
        }else{
            $u_title = 'N';
        }

        if($b_title_b == 'on'){
            $b_title_b = 'Y';
        }else{
            $b_title_b = 'N';
        }

        if($i_title_b == 'on'){
            $i_title_b = 'Y';
        }else{
            $i_title_b = 'N';
        }

        if($u_title_b == 'on'){
            $u_title_b = 'Y';
        }else{
            $u_title_b = 'N';
        }

        if($b_preface == 'on'){
            $b_preface = 'Y';
        }else{
            $b_preface = 'N';
        }

        if($i_preface == 'on'){
            $i_preface = 'Y';
        }else{
            $i_preface = 'N';
        }

        if($u_preface == 'on'){
            $u_preface = 'Y';
        }else{
            $u_preface = 'N';
        }

        if($b_explanation == 'on'){
            $b_explanation = 'Y';
        }else{
            $b_explanation = 'N';
        }

        if($i_explanation == 'on'){
            $i_explanation = 'Y';
        }else{
            $i_explanation = 'N';
        }

        if($u_explanation == 'on'){
            $u_explanation = 'Y';
        }else{
            $u_explanation = 'N';
        }

        if($b_profile == 'on'){
            $b_profile = 'Y';
        }else{
            $b_profile = 'N';
        }

        if($i_profile == 'on'){
            $i_profile = 'Y';
        }else{
            $i_profile = 'N';
        }

        if($u_profile == 'on'){
            $u_profile = 'Y';
        }else{
            $u_profile = 'N';
        }

        if($b_comment == 'on'){
            $b_comment = 'Y';
        }else{
            $b_comment = 'N';
        }

        if($i_comment == 'on'){
            $i_comment = 'Y';
        }else{
            $i_comment = 'N';
        }

        if($u_comment == 'on'){
            $u_comment = 'Y';
        }else{
            $u_comment = 'N';
        }

        if($player_subject_surf == 'on'){
            $player_subject['surf'] = 'Y';
        }else{
            $player_subject['surf'] = 'N';
        }

        if($player_subject_swim == 'on'){
            $player_subject['swim'] = 'Y';
        }else{
            $player_subject['swim'] = 'N';
        }

        if($player_subject_free == 'on'){
            $player_subject['free'] = 'Y';
        }else{
            $player_subject['free'] = 'N';
        }

        if($player_subject_scuba == 'on'){
            $player_subject['scuba'] = 'Y';
        }else{
            $player_subject['scuba'] = 'N';
        }

        if($player_subject_yoga == 'on'){
            $player_subject['yoga'] = 'Y';
        }else{
            $player_subject['yoga'] = 'N';
        }

        if($player_subject_pila == 'on'){
            $player_subject['pila'] = 'Y';
        }else{
            $player_subject['pila'] = 'N';
        }

        $unix_timestamp_display_sdate = mktime($display_start_h,$display_start_i,$display_start_s,substr($display_start,5,2),substr($display_start,8,2),substr($display_start,0,4));
        $unix_timestamp_display_edate = mktime($display_end_h,$display_end_i,$display_end_s,substr($display_end,5,2),substr($display_end,8,2),substr($display_end,0,4));

        $sql = "INSERT INTO shop_content
                (cid, depth, mall_ix, company_id, title, title_en, s_title, b_title, i_title, u_title, c_title, b_title_b, i_title_b, u_title_b, c_title_b,
                 preface, preface_en, b_preface, i_preface, u_preface, c_preface, explanation, explanation_en, b_explanation, i_explanation, u_explanation, c_explanation,
                 player_profile, player_profile_en, b_profile, i_profile, u_profile, c_profile, player_comment, player_comment_en, b_comment, i_comment, u_comment, c_comment,
                 player_subject, player_instar, player_youtube, display_use, display_date_use,  
                 display_state, display_start, display_end, worker_ix, sort, regdate, upddate
                 )
                VALUES
                ('$cid', '$this_depth', '$mall_ix', '".$company_id."', '".$title."', '".$title_en."', '".$s_title."', '".$b_title."', '".$i_title."', '".$u_title."', '".$c_title."', '".$b_title_b."', '".$i_title_b."', '".$u_title_b."', '".$c_title_b."',
                '".$preface."', '".$preface_en."', '".$b_preface."', '".$i_preface."', '".$u_preface."', '".$c_preface."', '".$explanation."', '".$explanation_en."', '".$b_explanation."', '".$i_explanation."', '".$u_explanation."', '".$c_explanation."',
                '".$player_profile."', '".$player_profile_en."', '".$b_profile."', '".$i_profile."', '".$u_profile."', '".$c_profile."', '".$player_comment."', '".$player_comment_en."', '".$b_comment."', '".$i_comment."', '".$u_comment."', '".$c_comment."',
                '".json_encode($player_subject)."', '".json_encode($player_instar)."', '".json_encode($player_youtube)."', '".$display_use."', '".$display_date_use."',
                '".$display_state."', '$unix_timestamp_display_sdate', '$unix_timestamp_display_edate', '".$_SESSION["admininfo"]["charger_ix"]."', '".($sort == "" ? "999" : $sort )."',NOW(),NOW()
                )
        ";

        $db->sequences = "SHOP_CONTENT_SEQ";
        $db->query($sql);

        $db->query("SELECT con_ix FROM shop_content WHERE con_ix=LAST_INSERT_ID()");
        $db->fetch();
        $con_LAST_ID = $db->dt[con_ix];

        if($_FILES['list_img']['name']){
            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_LAST_ID;

            $listImgName	= "listImg_".date('YmdHis', time())."_".$con_LAST_ID.".gif";
            $listImgTmpName	= $_FILES['list_img']['tmp_name'];

            if(!is_dir($listUpDir)){
                mkdir($listUpDir);
                chmod($listUpDir,0777);
            }

            copy($listImgTmpName, $listUpDir."/".$listImgName);

            $sql = "UPDATE shop_content SET list_img = '".$listImgName."' WHERE con_ix = '".$con_LAST_ID."' ";
            $db->query($sql);
        }

        if($_FILES['recommend_img']['name']){
            $recomUpDir	        = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_LAST_ID;

            $recomImgName	    = "recomImg_".date('YmdHis', time())."_".$con_LAST_ID.".gif";
            $recomImgTmpName	= $_FILES['recommend_img']['tmp_name'];

            if(!is_dir($recomUpDir)){
                mkdir($recomUpDir);
                chmod($recomUpDir,0777);
            }

            copy($recomImgTmpName, $recomUpDir."/".$recomImgName);

            $sql = "UPDATE shop_content SET recommend_img = '".$recomImgName."' WHERE con_ix = '".$con_LAST_ID."' ";
            $db->query($sql);
        }

        if($_POST['category']){
            foreach($_POST['category'] as $key => $val){
                $sql = "INSERT INTO shop_content_relation (con_ix, cid, worker_ix, regdate, upddate) VALUES ('$con_LAST_ID', '".$val."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_RELATION_SEQ";
                $db->query($sql);
            }
        }

        if($_POST['rpid'][9999]){
            foreach($_POST['rpid'][9999] as $key => $val){
                $sql = "INSERT INTO shop_content_product_relation (con_ix, pid, sort, worker_ix, regdate, upddate) VALUES ('$con_LAST_ID', '".$val."', '".$key."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_PRODUCT_RELATION_SEQ";
                $db->query($sql);
            }
        }

        if($_POST['imgInsYN']){
            CopyImage($con_LAST_ID, "","player");
        }

        foreach($_POST['group_order'] as $key => $val){
            if($_POST['b_group_title'][$val] == 'on'){
                $b_group_title = 'Y';
            }else{
                $b_group_title = 'N';
            }

            if($_POST['i_group_title'][$val] == 'on'){
                $i_group_title = 'Y';
            }else{
                $i_group_title = 'N';
            }

            if($_POST['u_group_title'][$val] == 'on'){
                $u_group_title = 'Y';
            }else{
                $u_group_title = 'N';
            }

            $c_group_title = $_POST['c_group_title'][$val];
            if($_POST['c_group_title'][$val] == ''){
                $c_group_title = '#000000';
            }

            if($_POST['b_group_explanation'][$val] == 'on'){
                $b_group_explanation = 'Y';
            }else{
                $b_group_explanation = 'N';
            }

            if($_POST['i_group_explanation'][$val] == 'on'){
                $i_group_explanation = 'Y';
            }else{
                $i_group_explanation = 'N';
            }

            if($_POST['u_group_explanation'][$val] == 'on'){
                $u_group_explanation = 'Y';
            }else{
                $u_group_explanation = 'N';
            }

            $c_group_explanation = $_POST['c_group_explanation'][$val];
            if($_POST['c_group_explanation'][$val] == ''){
                $c_group_explanation = '#000000';
            }

            $unix_timestamp_group_display_sdate = mktime($_POST['group_display_start_h'][$val],$_POST['group_display_start_i'][$val],$_POST['group_display_start_s'][$val],substr($_POST['group_display_start'][$val],5,2),substr($_POST['group_display_start'][$val],8,2),substr($_POST['group_display_start'][$val],0,4));
            $unix_timestamp_group_display_edate = mktime($_POST['group_display_end_h'][$val],$_POST['group_display_end_i'][$val],$_POST['group_display_end_s'][$val],substr($_POST['group_display_end'][$val],5,2),substr($_POST['group_display_end'][$val],8,2),substr($_POST['group_display_end'][$val],0,4));

            $sql = "INSERT INTO shop_content_group_relation
                    (con_ix, group_code, group_title, group_title_en, s_group_title, b_group_title, i_group_title, u_group_title, c_group_title,
                     group_explanation, group_explanation_en, b_group_explanation, i_group_explanation, u_group_explanation, c_group_explanation,
                     group_use, group_display_start, group_display_end,
                     worker_ix, regdate, upddate)
                    VALUES
                    ('$con_LAST_ID', '".$val."', '".$_POST['group_title'][$val]."', '".$_POST['group_title_en'][$val]."', '".$_POST['s_group_title'][$val]."', '".$b_group_title."', '".$i_group_title."', '".$u_group_title."', '".$c_group_title."',
                    '".$_POST['group_explanation'][$val]."', '".$_POST['group_explanation_en'][$val]."', '".$b_group_explanation."', '".$i_group_explanation."', '".$u_group_explanation."', '".$c_group_explanation."',
                    '".$_POST['group_use'][$val]."', '".$unix_timestamp_group_display_sdate."', '".$unix_timestamp_group_display_edate."',
                    '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())
            ";
            $db->sequences = "SHOP_CONTENT_GROUP_RELATION_SEQ";
            $db->query($sql);

            $db->query("SELECT cgr_ix FROM shop_content_group_relation WHERE cgr_ix=LAST_INSERT_ID()");
            $db->fetch();
            $cgr_LAST_ID = $db->dt[cgr_ix];

            if ($_POST['group_con_ix'][$val] != '') {
                foreach ($_POST['group_con_ix'][$val] as $key1 => $val1) {
                    $sql = "INSERT INTO shop_content_group_relation_content (cgr_ix, con_ix, sort, worker_ix, regdate, upddate) VALUES ('$cgr_LAST_ID', '" . $_POST['group_con_ix'][$val][$key1] . "', '" .$key1. "', '" . $_SESSION["admininfo"]["charger_ix"] . "', NOW(), NOW())";
                    $db->sequences = "SHOP_CONTENT_GROUP_RELATION_CONTENT_SEQ";
                    $db->query($sql);
                }
            }

            foreach($_POST['rpid'][$val] as $key1 => $val1){
                $sql = "INSERT INTO shop_content_group_product_relation (con_ix, cgr_ix, pid, sort, worker_ix, regdate, upddate) VALUES ('$con_LAST_ID', '$cgr_LAST_ID', '".$val1."', '".$key1."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
                $db->query($sql);
            }
        }
    }else if($content_type == 4){
        $unix_timestamp_display_sdate = mktime($display_start_h,$display_start_i,$display_start_s,substr($display_start,5,2),substr($display_start,8,2),substr($display_start,0,4));
        $unix_timestamp_display_edate = mktime($display_end_h,$display_end_i,$display_end_s,substr($display_end,5,2),substr($display_end,8,2),substr($display_end,0,4));

        $sql = "INSERT INTO shop_content
                (cid, depth, mall_ix, company_id, title, explanation, display_use, display_date_use, 
                 display_state, display_start, display_end, content_text_pc, content_text_mo, worker_ix, sort, regdate, upddate
                 )
                VALUES
                ('$cid', '$this_depth', '$mall_ix', '".$company_id."', '".$title."', '".$explanation."', '".$display_use."', '".$display_date_use."',
                '".$display_state."', '$unix_timestamp_display_sdate', '$unix_timestamp_display_edate', '".$content_text_pc."', '".$content_text_mo."', '".$_SESSION["admininfo"]["charger_ix"]."', '".($sort == "" ? "999" : $sort )."',NOW(),NOW()
                )
        ";

        $db->sequences = "SHOP_CONTENT_SEQ";
        $db->query($sql);
    }

    echo "<Script Language='JavaScript'>alert('등록되었습니다.');parent.document.location.href='content_edit.php?cid=".$cid."&content_type=".$content_type."&depth=".$this_depth."&mode=".$mode."';</Script>";
}

if($mode == "Upd"){
    if($content_type == 1){
        if($b_title == 'on'){
            $b_title = 'Y';
        }else{
            $b_title = 'N';
        }

        if($i_title == 'on'){
            $i_title = 'Y';
        }else{
            $i_title = 'N';
        }

        if($u_title == 'on'){
            $u_title = 'Y';
        }else{
            $u_title = 'N';
        }

        if($b_preface == 'on'){
            $b_preface = 'Y';
        }else{
            $b_preface = 'N';
        }

        if($i_preface == 'on'){
            $i_preface = 'Y';
        }else{
            $i_preface = 'N';
        }

        if($u_preface == 'on'){
            $u_preface = 'Y';
        }else{
            $u_preface = 'N';
        }

        if($b_explanation == 'on'){
            $b_explanation = 'Y';
        }else{
            $b_explanation = 'N';
        }

        if($i_explanation == 'on'){
            $i_explanation = 'Y';
        }else{
            $i_explanation = 'N';
        }

        if($u_explanation == 'on'){
            $u_explanation = 'Y';
        }else{
            $u_explanation = 'N';
        }

        if($b_category == 'on'){
            $b_category = 'Y';
        }else{
            $b_category = 'N';
        }

        if($i_category == 'on'){
            $i_category = 'Y';
        }else{
            $i_category = 'N';
        }

        if($u_category == 'on'){
            $u_category = 'Y';
        }else{
            $u_category = 'N';
        }

        if($e_category == 'on'){
            $e_category = 'Y';
        }else{
            $e_category = 'N';
        }

        $unix_timestamp_display_sdate = mktime($display_start_h,$display_start_i,$display_start_s,substr($display_start,5,2),substr($display_start,8,2),substr($display_start,0,4));
        $unix_timestamp_display_edate = mktime($display_end_h,$display_end_i,$display_end_s,substr($display_end,5,2),substr($display_end,8,2),substr($display_end,0,4));

        $sql = "UPDATE shop_content SET
                    mall_ix = '$mall_ix', title = '".$title."', title_en = '".$title_en."', s_title = '".$s_title."', b_title = '".$b_title."', i_title = '".$i_title."', u_title = '".$u_title."', c_title = '".$c_title."',
                    preface = '".$preface."', preface_en = '".$preface_en."', b_preface = '".$b_preface."', i_preface = '".$i_preface."', u_preface = '".$u_preface."', c_preface = '".$c_preface."',
                    explanation = '".$explanation."', explanation_en = '".$explanation_en."', b_explanation = '".$b_explanation."', i_explanation = '".$i_explanation."', u_explanation = '".$u_explanation."', c_explanation = '".$c_explanation."',
					category_use = '".$category_use."', b_category = '".$b_category."', i_category = '".$i_category."', u_category = '".$u_category."', c_category = '".$c_category."', r_category = '".$r_category."', e_category = '".$e_category."',
					ba_category = '".$ba_category."', bo_category = '".$bo_category."', display_gubun = '".$display_gubun."', display_use = '".$display_use."', display_date_use = '".$display_date_use."',
					display_state = '".$display_state."', display_start = '$unix_timestamp_display_sdate', display_end = '$unix_timestamp_display_edate',
					comment_board_ix = '".$comment_board_ix."', content_text_pc = '".$content_text_pc."', content_text_mo = '".$content_text_mo."', upddate = NOW()	
				WHERE
					con_ix = '$con_ix'
         ";
        $db->query($sql);

        if($list_img_del == 'on' || $_FILES['list_img']['name']){

            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $db->query("select list_img from shop_content where con_ix = '".$con_ix."' ");
            $db->fetch();

            $listImgName = $db->dt[list_img];

            //$listImgName	= "listImg_".$con_ix.".gif";

            if (file_exists($listUpDir . "/" .$listImgName)) {
                unlink($listUpDir . "/" .$listImgName);
            }

            $sql = "UPDATE shop_content SET list_img = '' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        if($_FILES['list_img']['name']){
            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $listImgName	= "listImg_".date('YmdHis', time())."_".$con_ix.".gif";
            $listImgTmpName	= $_FILES['list_img']['tmp_name'];

            if(!is_dir($listUpDir)){
                mkdir($listUpDir);
                chmod($listUpDir,0777);
            }

            copy($listImgTmpName, $listUpDir."/".$listImgName);

            $sql = "UPDATE shop_content SET list_img = '".$listImgName."' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        if($recommend_img_del == 'on' || $_FILES['recommend_img']['name']){
            $recomUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $db->query("select recommend_img from shop_content where con_ix = '".$con_ix."' ");
            $db->fetch();

            $recomImgName = $db->dt[recommend_img];

            //$recomImgName	= "recomImg_".$con_ix.".gif";

            if (file_exists($recomUpDir . "/" .$recomImgName)) {
                unlink($recomUpDir . "/" .$recomImgName);
            }

            $sql = "UPDATE shop_content SET recommend_img = '' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        if($_FILES['recommend_img']['name']){
            $recomUpDir	        = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $recomImgName	    = "recomImg_".date('YmdHis', time())."_".$con_ix.".gif";
            $recomImgTmpName	= $_FILES['recommend_img']['tmp_name'];

            if(!is_dir($recomUpDir)){
                mkdir($recomUpDir);
                chmod($recomUpDir,0777);
            }

            copy($recomImgTmpName, $recomUpDir."/".$recomImgName);

            $sql = "UPDATE shop_content SET recommend_img = '".$recomImgName."' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        //if($_POST['category']){
            $sql = "DELETE FROM shop_content_relation WHERE con_ix = '$con_ix' ";
            $db->query($sql);

            foreach($_POST['category'] as $key => $val){
                $sql = "INSERT INTO shop_content_relation (con_ix, cid, worker_ix, regdate, upddate) VALUES ('$con_ix', '".$val."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_RELATION_SEQ";
                $db->query($sql);
            }
        //}

        //if($_POST['rpid'][9999]){
            $sql = "DELETE FROM shop_content_product_relation WHERE con_ix = '$con_ix' ";
            $db->query($sql);

            foreach($_POST['rpid'][9999] as $key => $val){
                $sql = "INSERT INTO shop_content_product_relation (con_ix, pid, sort, worker_ix, regdate, upddate) VALUES ('$con_ix', '".$val."', '".$key."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_PRODUCT_RELATION_SEQ";
                $db->query($sql);
            }
        //}

        $sql = "DELETE FROM shop_content_group_relation WHERE con_ix = '$con_ix'";
        $db->query($sql);

        $sql = "DELETE FROM shop_content_group_product_relation WHERE con_ix = '$con_ix'";
        $db->query($sql);

        foreach($_POST['group_order'] as $key => $val){
            //$cgr_ix = $_POST['cgr_ix'][$val];

            if($_POST['b_group_title'][$val] == 'on'){
                $b_group_title = 'Y';
            }else{
                $b_group_title = 'N';
            }

            if($_POST['i_group_title'][$val] == 'on'){
                $i_group_title = 'Y';
            }else{
                $i_group_title = 'N';
            }

            if($_POST['u_group_title'][$val] == 'on'){
                $u_group_title = 'Y';
            }else{
                $u_group_title = 'N';
            }

            $c_group_title = $_POST['c_group_title'][$val];
            if($_POST['c_group_title'][$val] == ''){
                $c_group_title = '#000000';
            }

            if($_POST['b_group_preface'][$val] == 'on'){
                $b_group_preface = 'Y';
            }else{
                $b_group_preface = 'N';
            }

            if($_POST['i_group_preface'][$val] == 'on'){
                $i_group_preface = 'Y';
            }else{
                $i_group_preface = 'N';
            }

            if($_POST['u_group_preface'][$val] == 'on'){
                $u_group_preface = 'Y';
            }else{
                $u_group_preface = 'N';
            }

            $c_group_preface = $_POST['c_group_preface'][$val];
            if($_POST['c_group_preface'][$val] == ''){
                $c_group_preface = '#000000';
            }

            if($_POST['b_group_explanation'][$val] == 'on'){
                $b_group_explanation = 'Y';
            }else{
                $b_group_explanation = 'N';
            }

            if($_POST['i_group_explanation'][$val] == 'on'){
                $i_group_explanation = 'Y';
            }else{
                $i_group_explanation = 'N';
            }

            if($_POST['u_group_explanation'][$val] == 'on'){
                $u_group_explanation = 'Y';
            }else{
                $u_group_explanation = 'N';
            }

            $c_group_explanation = $_POST['c_group_explanation'][$val];
            if($_POST['c_group_explanation'][$val] == ''){
                $c_group_explanation = '#000000';
            }

            $unix_timestamp_group_display_sdate = mktime($_POST['group_display_start_h'][$val],$_POST['group_display_start_i'][$val],$_POST['group_display_start_s'][$val],substr($_POST['group_display_start'][$val],5,2),substr($_POST['group_display_start'][$val],8,2),substr($_POST['group_display_start'][$val],0,4));
            $unix_timestamp_group_display_edate = mktime($_POST['group_display_end_h'][$val],$_POST['group_display_end_i'][$val],$_POST['group_display_end_s'][$val],substr($_POST['group_display_end'][$val],5,2),substr($_POST['group_display_end'][$val],8,2),substr($_POST['group_display_end'][$val],0,4));

            $sql = "INSERT INTO shop_content_group_relation
                    (con_ix, group_code, group_title, group_title_en, s_group_title, b_group_title, i_group_title, u_group_title, c_group_title,
                     group_preface, group_preface_en, b_group_preface, i_group_preface, u_group_preface, c_group_preface,
                     group_explanation, group_explanation_en, b_group_explanation, i_group_explanation, u_group_explanation, c_group_explanation,
                     group_text_pc, group_text_mo, group_use, group_display_start, group_display_end,
                     worker_ix, regdate, upddate)
                    VALUES
                    ('$con_ix', '".$val."', '".$_POST['group_title'][$val]."', '".$_POST['group_title_en'][$val]."', '".$_POST['s_group_title'][$val]."', '".$b_group_title."', '".$i_group_title."', '".$u_group_title."', '".$c_group_title."',
                    '".$_POST['group_preface'][$val]."', '".$_POST['group_preface_en'][$val]."', '".$b_group_preface."', '".$i_group_preface."', '".$u_group_preface."', '".$c_group_preface."',
                    '".$_POST['group_explanation'][$val]."', '".$_POST['group_explanation_en'][$val]."', '".$b_group_explanation."', '".$i_group_explanation."', '".$u_group_explanation."', '".$c_group_explanation."',
                    '".$_POST['group_text_pc'][$val]."', '".$_POST['group_text_mo'][$val]."', '".$_POST['group_use'][$val]."', '".$unix_timestamp_group_display_sdate."', '".$unix_timestamp_group_display_edate."',
                    '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())
            ";
            $db->sequences = "SHOP_CONTENT_GROUP_RELATION_SEQ";
            $db->query($sql);

            $db->query("SELECT cgr_ix FROM shop_content_group_relation WHERE cgr_ix=LAST_INSERT_ID()");
            $db->fetch();
            $cgr_LAST_ID = $db->dt[cgr_ix];

            foreach($_POST['rpid'][$val] as $key1 => $val1){
                $sql = "INSERT INTO shop_content_group_product_relation (con_ix, cgr_ix, pid, sort, worker_ix, regdate, upddate) VALUES('$con_ix', '$cgr_LAST_ID', '".$val1."', '".$key1."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
                $db->query($sql);
            }

            /*$db->query("select count(*) as total from shop_content_group_relation where cgr_ix = '$cgr_ix' AND con_ix = '$con_ix' ");
            $db->fetch();

            if ($db->dt[total] > 0) {
                $sql = "UPDATE shop_content_group_relation SET
                    group_title = '".$_POST['group_title'][$val]."', group_title_en = '".$_POST['group_title_en'][$val]."', s_group_title = '".$_POST['s_group_title'][$val]."', b_group_title = '".$b_group_title."', i_group_title = '".$i_group_title."', u_group_title = '".$u_group_title."', c_group_title = '".$c_group_title."',
                    group_preface = '".$_POST['group_preface'][$val]."', group_preface_en = '".$_POST['group_preface_en'][$val]."', b_group_preface = '".$b_group_preface."', i_group_preface = '".$i_group_preface."', u_group_preface = '".$u_group_preface."', c_group_preface = '".$c_group_preface."',
                    group_explanation = '".$_POST['group_explanation'][$val]."', group_explanation_en = '".$_POST['group_explanation_en'][$val]."', b_group_explanation = '".$b_group_explanation."', i_group_explanation = '".$i_group_explanation."', u_group_explanation = '".$u_group_explanation."', c_group_explanation = '".$c_group_explanation."',
                    group_text_pc = '".$_POST['group_text_pc'][$val]."', group_text_mo = '".$_POST['group_text_mo'][$val]."', group_use = '".$_POST['group_use'][$val]."', 
                    group_display_start = '$unix_timestamp_group_display_sdate', group_display_end = '$unix_timestamp_group_display_edate', worker_ix = '".$_SESSION["admininfo"]["charger_ix"]."', upddate = NOW()
                WHERE
                    cgr_ix = '$cgr_ix' AND con_ix = '$con_ix'
                ";
            } else {
                $sql = "INSERT INTO shop_content_group_relation
                    (con_ix, group_code, group_title, group_title_en, s_group_title, b_group_title, i_group_title, u_group_title, c_group_title,
                     group_preface, group_preface_en, b_group_preface, i_group_preface, u_group_preface, c_group_preface,
                     group_explanation, group_explanation_en, b_group_explanation, i_group_explanation, u_group_explanation, c_group_explanation,
                     group_text_pc, group_text_mo, group_use, group_display_start, group_display_end,
                     worker_ix, regdate, upddate)
                    VALUES
                    ('$con_ix', '".$val."', '".$_POST['group_title'][$val]."', '".$_POST['group_title_en'][$val]."', '".$_POST['s_group_title'][$val]."', '".$b_group_title."', '".$i_group_title."', '".$u_group_title."', '".$c_group_title."',
                    '".$_POST['group_preface'][$val]."', '".$_POST['group_preface_en'][$val]."', '".$b_group_preface."', '".$i_group_preface."', '".$u_group_preface."', '".$c_group_preface."',
                    '".$_POST['group_explanation'][$val]."', '".$_POST['group_explanation_en'][$val]."', '".$b_group_explanation."', '".$i_group_explanation."', '".$u_group_explanation."', '".$c_group_explanation."',
                    '".$_POST['group_text_pc'][$val]."', '".$_POST['group_text_mo'][$val]."', '".$_POST['group_use'][$val]."', '".$unix_timestamp_group_display_sdate."', '".$unix_timestamp_group_display_edate."',
                    '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())
                ";
            }

            $db->query($sql);

            if($_POST['rpid'][$val]){
                $sql = "DELETE FROM shop_content_group_product_relation WHERE con_ix = '$con_ix' AND cgr_ix = '$cgr_ix'";
                $db->query($sql);

                foreach($_POST['rpid'][$val] as $key1 => $val1){
                    $sql = "INSERT INTO shop_content_group_product_relation (con_ix, cgr_ix, pid, sort, worker_ix, regdate, upddate) VALUES('$con_ix', '$cgr_ix', '".$val1."', '".$key1."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                    $db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
                    $db->query($sql);
                }
            }*/
        }
    }else if($content_type == 2){
        if($b_title == 'on'){
            $b_title = 'Y';
        }else{
            $b_title = 'N';
        }

        if($i_title == 'on'){
            $i_title = 'Y';
        }else{
            $i_title = 'N';
        }

        if($u_title == 'on'){
            $u_title = 'Y';
        }else{
            $u_title = 'N';
        }

        if($b_preface == 'on'){
            $b_preface = 'Y';
        }else{
            $b_preface = 'N';
        }

        if($i_preface == 'on'){
            $i_preface = 'Y';
        }else{
            $i_preface = 'N';
        }

        if($u_preface == 'on'){
            $u_preface = 'Y';
        }else{
            $u_preface = 'N';
        }

        if($b_explanation == 'on'){
            $b_explanation = 'Y';
        }else{
            $b_explanation = 'N';
        }

        if($i_explanation == 'on'){
            $i_explanation = 'Y';
        }else{
            $i_explanation = 'N';
        }

        if($u_explanation == 'on'){
            $u_explanation = 'Y';
        }else{
            $u_explanation = 'N';
        }

        $unix_timestamp_display_sdate = mktime($display_start_h,$display_start_i,$display_start_s,substr($display_start,5,2),substr($display_start,8,2),substr($display_start,0,4));
        $unix_timestamp_display_edate = mktime($display_end_h,$display_end_i,$display_end_s,substr($display_end,5,2),substr($display_end,8,2),substr($display_end,0,4));

        $sql = "UPDATE shop_content SET
                    mall_ix = '$mall_ix', title = '".$title."', title_en = '".$title_en."', s_title = '".$s_title."', b_title = '".$b_title."', i_title = '".$i_title."', u_title = '".$u_title."', c_title = '".$c_title."',
                    preface = '".$preface."', preface_en = '".$preface_en."', b_preface = '".$b_preface."', i_preface = '".$i_preface."', u_preface = '".$u_preface."', c_preface = '".$c_preface."',
                    explanation = '".$explanation."', explanation_en = '".$explanation_en."', b_explanation = '".$b_explanation."', i_explanation = '".$i_explanation."', u_explanation = '".$u_explanation."', c_explanation = '".$c_explanation."',
					display_use = '".$display_use."', display_date_use = '".$display_date_use."', display_state = '".$display_state."', display_start = '$unix_timestamp_display_sdate', display_end = '$unix_timestamp_display_edate',
					upddate = NOW()
				WHERE
					con_ix = '$con_ix'
         ";
        $db->query($sql);

        if($list_img_del == 'on' || $_FILES['list_img']['name']){
            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $db->query("select list_img from shop_content where con_ix = '".$con_ix."' ");
            $db->fetch();

            $listImgName = $db->dt[list_img];

            //$listImgName	= "listImg_".$con_ix.".gif";

            if (file_exists($listUpDir . "/" .$listImgName)) {
                unlink($listUpDir . "/" .$listImgName);
            }

            $sql = "UPDATE shop_content SET list_img = '' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        if($_FILES['list_img']['name']){
            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $listImgName	= "listImg_".date('YmdHis', time())."_".$con_ix.".gif";
            $listImgTmpName	= $_FILES['list_img']['tmp_name'];

            if(!is_dir($listUpDir)){
                mkdir($listUpDir);
                chmod($listUpDir,0777);
            }

            copy($listImgTmpName, $listUpDir."/".$listImgName);

            $sql = "UPDATE shop_content SET list_img = '".$listImgName."' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        if($list_img_m_del == 'on' || $_FILES['list_img_m']['name']){
            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $db->query("select list_img from shop_content where con_ix = '".$con_ix."' ");
            $db->fetch();

            $listImgMName = $db->dt[list_img_m];

            //$listImgName	= "listImg_".$con_ix.".gif";

            if (file_exists($listUpDir . "/" .$listImgMName)) {
                unlink($listUpDir . "/" .$listImgMName);
            }

            $sql = "UPDATE shop_content SET list_img_m = '' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        if($_FILES['list_img_m']['name']){
            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $listImgMName	= "listImg_m_".date('YmdHis', time())."_".$con_ix.".gif";
            $listImgMTmpName	= $_FILES['list_img_m']['tmp_name'];

            if(!is_dir($listUpDir)){
                mkdir($listUpDir);
                chmod($listUpDir,0777);
            }

            copy($listImgMTmpName, $listUpDir."/".$listImgMName);

            $sql = "UPDATE shop_content SET list_img_m = '".$listImgMName."' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        if($_POST['imgInsYN']){
            CopyImage($con_ix, "","style");
        }

        $sql = "DELETE FROM shop_content_group_relation WHERE con_ix = '$con_ix'";
        $db->query($sql);

        $sql = "DELETE FROM shop_content_group_product_relation WHERE con_ix = '$con_ix'";
        $db->query($sql);

        foreach($_POST['group_order'] as $key => $val){
            //$cgr_ix = $_POST['cgr_ix'][$val];

            if($_POST['b_group_title'][$val] == 'on'){
                $b_group_title = 'Y';
            }else{
                $b_group_title = 'N';
            }

            if($_POST['i_group_title'][$val] == 'on'){
                $i_group_title = 'Y';
            }else{
                $i_group_title = 'N';
            }

            if($_POST['u_group_title'][$val] == 'on'){
                $u_group_title = 'Y';
            }else{
                $u_group_title = 'N';
            }

            $c_group_title = $_POST['c_group_title'][$val];
            if($_POST['c_group_title'][$val] == ''){
                $c_group_title = '#000000';
            }

            $unix_timestamp_group_display_sdate = mktime($_POST['group_display_start_h'][$val],$_POST['group_display_start_i'][$val],$_POST['group_display_start_s'][$val],substr($_POST['group_display_start'][$val],5,2),substr($_POST['group_display_start'][$val],8,2),substr($_POST['group_display_start'][$val],0,4));
            $unix_timestamp_group_display_edate = mktime($_POST['group_display_end_h'][$val],$_POST['group_display_end_i'][$val],$_POST['group_display_end_s'][$val],substr($_POST['group_display_end'][$val],5,2),substr($_POST['group_display_end'][$val],8,2),substr($_POST['group_display_end'][$val],0,4));

            $sql = "INSERT INTO shop_content_group_relation
                    (con_ix, group_code, group_title, group_title_en, s_group_title, b_group_title, i_group_title, u_group_title, c_group_title,
                     group_use, group_display_start, group_display_end,
                     worker_ix, regdate, upddate)
                    VALUES
                    ('$con_ix', '".$val."', '".$_POST['group_title'][$val]."', '".$_POST['group_title_en'][$val]."', '".$_POST['s_group_title'][$val]."', '".$b_group_title."', '".$i_group_title."', '".$u_group_title."', '".$c_group_title."',
                    '".$_POST['group_use'][$val]."', '".$unix_timestamp_group_display_sdate."', '".$unix_timestamp_group_display_edate."',
                    '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())
            ";
            $db->sequences = "SHOP_CONTENT_GROUP_RELATION_SEQ";
            $db->query($sql);

            $db->query("SELECT cgr_ix FROM shop_content_group_relation WHERE cgr_ix=LAST_INSERT_ID()");
            $db->fetch();
            $cgr_LAST_ID = $db->dt[cgr_ix];

            foreach($_POST['rpid'][$val] as $key1 => $val1){
                $sql = "INSERT INTO shop_content_group_product_relation (con_ix, cgr_ix, pid, sort, worker_ix, regdate, upddate) VALUES ('$con_ix', '$cgr_LAST_ID', '".$val1."', '".$key1."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
                $db->query($sql);
            }

            /*$db->query("select count(*) as total from shop_content_group_relation where cgr_ix = '$cgr_ix' AND con_ix = '$con_ix' ");
            $db->fetch();

            if ($db->dt[total] > 0) {
                $sql = "UPDATE shop_content_group_relation SET
                            group_title = '".$_POST['group_title'][$val]."', group_title_en = '".$_POST['group_title_en'][$val]."', s_group_title = '".$_POST['s_group_title'][$val]."', b_group_title = '".$b_group_title."', i_group_title = '".$i_group_title."', u_group_title = '".$u_group_title."', c_group_title = '".$c_group_title."',
                            group_use = '".$_POST['group_use'][$val]."', group_display_start = '$unix_timestamp_group_display_sdate', group_display_end = '$unix_timestamp_group_display_edate', worker_ix = '".$_SESSION["admininfo"]["charger_ix"]."', upddate = NOW()
                        WHERE
                            cgr_ix = '$cgr_ix' AND con_ix = '$con_ix'
				";
            } else {
                $sql = "INSERT INTO shop_content_group_relation
                    (con_ix, group_code, group_title, group_title_en, s_group_title, b_group_title, i_group_title, u_group_title, c_group_title,
                     group_use, group_display_start, group_display_end,
                     worker_ix, regdate, upddate)
                    VALUES
                    ('$con_ix', '".$val."', '".$_POST['group_title'][$val]."', '".$_POST['group_title_en'][$val]."', '".$_POST['s_group_title'][$val]."', '".$b_group_title."', '".$i_group_title."', '".$u_group_title."', '".$c_group_title."',
                    '".$_POST['group_use'][$val]."', '".$unix_timestamp_group_display_sdate."', '".$unix_timestamp_group_display_edate."',
                    '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())
                ";
            }

            $db->query($sql);

            if($_POST['rpid'][$val]){
                $sql = "DELETE FROM shop_content_group_product_relation WHERE con_ix = '$con_ix' AND cgr_ix = '$cgr_ix' ";
                $db->query($sql);

                foreach($_POST['rpid'][$val] as $key1 => $val1){
                    $sql = "INSERT INTO shop_content_group_product_relation (con_ix, cgr_ix, pid, sort, worker_ix, regdate, upddate) VALUES('$con_ix', '$cgr_ix', '".$val1."', '".$key1."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                    $db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
                    $db->query($sql);
                }
            }*/
        }
    }else if($content_type == 3){
        if($b_title == 'on'){
            $b_title = 'Y';
        }else{
            $b_title = 'N';
        }

        if($i_title == 'on'){
            $i_title = 'Y';
        }else{
            $i_title = 'N';
        }

        if($u_title == 'on'){
            $u_title = 'Y';
        }else{
            $u_title = 'N';
        }

        if($b_title_b == 'on'){
            $b_title_b = 'Y';
        }else{
            $b_title_b = 'N';
        }

        if($i_title_b == 'on'){
            $i_title_b = 'Y';
        }else{
            $i_title_b = 'N';
        }

        if($u_title_b == 'on'){
            $u_title_b = 'Y';
        }else{
            $u_title_b = 'N';
        }

        if($b_preface == 'on'){
            $b_preface = 'Y';
        }else{
            $b_preface = 'N';
        }

        if($i_preface == 'on'){
            $i_preface = 'Y';
        }else{
            $i_preface = 'N';
        }

        if($u_preface == 'on'){
            $u_preface = 'Y';
        }else{
            $u_preface = 'N';
        }

        if($b_explanation == 'on'){
            $b_explanation = 'Y';
        }else{
            $b_explanation = 'N';
        }

        if($i_explanation == 'on'){
            $i_explanation = 'Y';
        }else{
            $i_explanation = 'N';
        }

        if($u_explanation == 'on'){
            $u_explanation = 'Y';
        }else{
            $u_explanation = 'N';
        }

        if($b_profile == 'on'){
            $b_profile = 'Y';
        }else{
            $b_profile = 'N';
        }

        if($i_profile == 'on'){
            $i_profile = 'Y';
        }else{
            $i_profile = 'N';
        }

        if($u_profile == 'on'){
            $u_profile = 'Y';
        }else{
            $u_profile = 'N';
        }

        if($b_comment == 'on'){
            $b_comment = 'Y';
        }else{
            $b_comment = 'N';
        }

        if($i_comment == 'on'){
            $i_comment = 'Y';
        }else{
            $i_comment = 'N';
        }

        if($u_comment == 'on'){
            $u_comment = 'Y';
        }else{
            $u_comment = 'N';
        }

        foreach($player_subject as $key => $val){
            $player_subject[$key] = 'Y';
        }

        /*if($player_subject_surf == 'on'){
            $player_subject['surf'] = 'Y';
        }else{
            $player_subject['surf'] = 'N';
        }

        if($player_subject_swim == 'on'){
            $player_subject['swim'] = 'Y';
        }else{
            $player_subject['swim'] = 'N';
        }

        if($player_subject_free == 'on'){
            $player_subject['free'] = 'Y';
        }else{
            $player_subject['free'] = 'N';
        }

        if($player_subject_scuba == 'on'){
            $player_subject['scuba'] = 'Y';
        }else{
            $player_subject['scuba'] = 'N';
        }

        if($player_subject_yoga == 'on'){
            $player_subject['yoga'] = 'Y';
        }else{
            $player_subject['yoga'] = 'N';
        }

        if($player_subject_pila == 'on'){
            $player_subject['pila'] = 'Y';
        }else{
            $player_subject['pila'] = 'N';
        }*/

        $unix_timestamp_display_sdate = mktime($display_start_h,$display_start_i,$display_start_s,substr($display_start,5,2),substr($display_start,8,2),substr($display_start,0,4));
        $unix_timestamp_display_edate = mktime($display_end_h,$display_end_i,$display_end_s,substr($display_end,5,2),substr($display_end,8,2),substr($display_end,0,4));

        $sql = "UPDATE shop_content SET
                    mall_ix = '$mall_ix', title = '".$title."', title_en = '".$title_en."', s_title = '".$s_title."', b_title = '".$b_title."', i_title = '".$i_title."', u_title = '".$u_title."', c_title = '".$c_title."',
                    b_title_b = '".$b_title_b."', i_title_b = '".$i_title_b."', u_title_b = '".$u_title_b."', c_title_b = '".$c_title_b."',
                    preface = '".$preface."', preface_en = '".$preface_en."', b_preface = '".$b_preface."', i_preface = '".$i_preface."', u_preface = '".$u_preface."', c_preface = '".$c_preface."',
                    explanation = '".$explanation."', explanation_en = '".$explanation_en."', b_explanation = '".$b_explanation."', i_explanation = '".$i_explanation."', u_explanation = '".$u_explanation."', c_explanation = '".$c_explanation."',
                    player_profile = '".$player_profile."', player_profile_en = '".$player_profile_en."', b_profile = '".$b_profile."', i_profile = '".$i_profile."', u_profile = '".$u_profile."', c_profile = '".$c_profile."',
                    player_comment = '".$player_comment."', player_comment_en = '".$player_comment_en."', b_comment = '".$b_comment."', i_comment = '".$i_comment."', u_comment = '".$u_comment."', c_comment = '".$c_comment."',
                    player_subject = '".json_encode($player_subject)."', player_instar = '".json_encode($player_instar)."', player_youtube = '".json_encode($player_youtube)."',
                    display_use = '".$display_use."', display_date_use = '".$display_date_use."', display_state = '".$display_state."', display_start = '$unix_timestamp_display_sdate', display_end = '$unix_timestamp_display_edate',
					upddate = NOW()
				WHERE
					con_ix = '$con_ix'
         ";
        $db->query($sql);

        if($list_img_del == 'on' || $_FILES['list_img']['name']){
            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $db->query("select list_img from shop_content where con_ix = '".$con_ix."' ");
            $db->fetch();

            $listImgName = $db->dt[list_img];

            //$listImgName	= "listImg_".$con_ix.".gif";

            if (file_exists($listUpDir . "/" .$listImgName)) {
                unlink($listUpDir . "/" .$listImgName);
            }

            $sql = "UPDATE shop_content SET list_img = '' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        if($_FILES['list_img']['name']){
            $listUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $listImgName	= "listImg_".date('YmdHis', time())."_".$con_ix.".gif";
            $listImgTmpName	= $_FILES['list_img']['tmp_name'];

            if(!is_dir($listUpDir)){
                mkdir($listUpDir);
                chmod($listUpDir,0777);
            }

            copy($listImgTmpName, $listUpDir."/".$listImgName);

            $sql = "UPDATE shop_content SET list_img = '".$listImgName."' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        if($recommend_img_del == 'on' || $_FILES['recommend_img']['name']){
            $recomUpDir	    = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $db->query("select recommend_img from shop_content where con_ix = '".$con_ix."' ");
            $db->fetch();

            $listImgName = $db->dt[recommend_img];

            //$recomImgName	= "recomImg_".$con_ix.".gif";

            if (file_exists($recomUpDir . "/" .$recomImgName)) {
                unlink($recomUpDir . "/" .$recomImgName);
            }

            $sql = "UPDATE shop_content SET recommend_img = '' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        if($_FILES['recommend_img']['name']){
            $recomUpDir	        = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

            $recomImgName	    = "recomImg_".date('YmdHis', time())."_".$con_ix.".gif";
            $recomImgTmpName	= $_FILES['recommend_img']['tmp_name'];

            if(!is_dir($recomUpDir)){
                mkdir($recomUpDir);
                chmod($recomUpDir,0777);
            }

            copy($recomImgTmpName, $recomUpDir."/".$recomImgName);

            $sql = "UPDATE shop_content SET recommend_img = '".$recomImgName."' WHERE con_ix = '".$con_ix."' ";
            $db->query($sql);
        }

        //if($_POST['category']){
            $sql = "DELETE FROM shop_content_relation WHERE con_ix = '$con_ix' ";
            $db->query($sql);

            foreach($_POST['category'] as $key => $val){
                $sql = "INSERT INTO shop_content_relation (con_ix, cid, worker_ix, regdate, upddate) VALUES ('$con_ix', '".$val."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_RELATION_SEQ";
                $db->query($sql);
            }
        //}

        //if($_POST['rpid'][9999]){
            $sql = "DELETE FROM shop_content_product_relation WHERE con_ix = '$con_ix' ";
            $db->query($sql);

            foreach($_POST['rpid'][9999] as $key => $val){
                $sql = "INSERT INTO shop_content_product_relation (con_ix, pid, sort, worker_ix, regdate, upddate) VALUES ('$con_ix', '".$val."', '".$key."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_PRODUCT_RELATION_SEQ";
                $db->query($sql);
            }
        //}

        if($_POST['imgInsYN']){
            CopyImage($con_ix, "","player");
        }

        $sql = "DELETE FROM shop_content_group_relation WHERE con_ix = '$con_ix'";
        $db->query($sql);

        $sql = "DELETE FROM shop_content_group_product_relation WHERE con_ix = '$con_ix'";
        $db->query($sql);

        foreach($_POST['group_order'] as $key => $val){
            //$cgr_ix = $_POST['cgr_ix'][$val];

            if($_POST['b_group_title'][$val] == 'on'){
                $b_group_title = 'Y';
            }else{
                $b_group_title = 'N';
            }

            if($_POST['i_group_title'][$val] == 'on'){
                $i_group_title = 'Y';
            }else{
                $i_group_title = 'N';
            }

            if($_POST['u_group_title'][$val] == 'on'){
                $u_group_title = 'Y';
            }else{
                $u_group_title = 'N';
            }

            $c_group_title = $_POST['c_group_title'][$val];
            if($_POST['c_group_title'][$val] == ''){
                $c_group_title = '#000000';
            }

            if($_POST['b_group_explanation'][$val] == 'on'){
                $b_group_explanation = 'Y';
            }else{
                $b_group_explanation = 'N';
            }

            if($_POST['i_group_explanation'][$val] == 'on'){
                $i_group_explanation = 'Y';
            }else{
                $i_group_explanation = 'N';
            }

            if($_POST['u_group_explanation'][$val] == 'on'){
                $u_group_explanation = 'Y';
            }else{
                $u_group_explanation = 'N';
            }

            $c_group_explanation = $_POST['c_group_explanation'][$val];
            if($_POST['c_group_explanation'][$val] == ''){
                $c_group_explanation = '#000000';
            }

            $unix_timestamp_group_display_sdate = mktime($_POST['group_display_start_h'][$val],$_POST['group_display_start_i'][$val],$_POST['group_display_start_s'][$val],substr($_POST['group_display_start'][$val],5,2),substr($_POST['group_display_start'][$val],8,2),substr($_POST['group_display_start'][$val],0,4));
            $unix_timestamp_group_display_edate = mktime($_POST['group_display_end_h'][$val],$_POST['group_display_end_i'][$val],$_POST['group_display_end_s'][$val],substr($_POST['group_display_end'][$val],5,2),substr($_POST['group_display_end'][$val],8,2),substr($_POST['group_display_end'][$val],0,4));

            $sql = "INSERT INTO shop_content_group_relation
                    (con_ix, group_code, group_title, group_title_en, s_group_title, b_group_title, i_group_title, u_group_title, c_group_title,
                     group_explanation, group_explanation_en, b_group_explanation, i_group_explanation, u_group_explanation, c_group_explanation,
                     group_use, group_display_start, group_display_end,
                     worker_ix, regdate, upddate)
                    VALUES
                    ('$con_ix', '".$val."', '".$_POST['group_title'][$val]."', '".$_POST['group_title_en'][$val]."', '".$_POST['s_group_title'][$val]."', '".$b_group_title."', '".$i_group_title."', '".$u_group_title."', '".$c_group_title."',
                    '".$_POST['group_explanation'][$val]."', '".$_POST['group_explanation_en'][$val]."', '".$b_group_explanation."', '".$i_group_explanation."', '".$u_group_explanation."', '".$c_group_explanation."',
                    '".$_POST['group_use'][$val]."', '".$unix_timestamp_group_display_sdate."', '".$unix_timestamp_group_display_edate."',
                    '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())
            ";
            $db->sequences = "SHOP_CONTENT_GROUP_RELATION_SEQ";
            $db->query($sql);

            $db->query("SELECT cgr_ix FROM shop_content_group_relation WHERE cgr_ix=LAST_INSERT_ID()");
            $db->fetch();
            $cgr_LAST_ID = $db->dt[cgr_ix];

            if ($_POST['group_con_ix'][$val] != '') {
                foreach ($_POST['group_con_ix'][$val] as $key1 => $val1) {
                    $sql = "INSERT INTO shop_content_group_relation_content (cgr_ix, con_ix, sort, worker_ix, regdate, upddate) VALUES ('$cgr_LAST_ID', '" . $_POST['group_con_ix'][$val][$key1] . "', '" .$key1. "', '" . $_SESSION["admininfo"]["charger_ix"] . "', NOW(), NOW())";
                    $db->sequences = "SHOP_CONTENT_GROUP_RELATION_CONTENT_SEQ";
                    $db->query($sql);
                }
            }

            foreach($_POST['rpid'][$val] as $key1 => $val1){
                $sql = "INSERT INTO shop_content_group_product_relation (con_ix, cgr_ix, pid, sort, worker_ix, regdate, upddate) VALUES ('$con_ix', '$cgr_LAST_ID', '".$val1."', '".$key1."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                $db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
                $db->query($sql);
            }

            /*$db->query("select count(*) as total from shop_content_group_relation where cgr_ix = '$cgr_ix' AND con_ix = '$con_ix' ");
            $db->fetch();

            if ($db->dt[total] > 0) {
                $sql = "UPDATE shop_content_group_relation SET
                        group_title = '".$_POST['group_title'][$val]."', group_title_en = '".$_POST['group_title_en'][$val]."', s_group_title = '".$_POST['s_group_title'][$val]."', b_group_title = '".$b_group_title."', i_group_title = '".$i_group_title."', u_group_title = '".$u_group_title."', c_group_title = '".$c_group_title."',
                        group_explanation = '".$_POST['group_explanation'][$val]."', group_explanation_en = '".$_POST['group_explanation_en'][$val]."', b_group_explanation = '".$b_group_explanation."', i_group_explanation = '".$i_group_explanation."', u_group_explanation = '".$u_group_explanation."', c_group_explanation = '".$c_group_explanation."',
                        group_use = '".$_POST['group_use'][$val]."', group_display_start = '$unix_timestamp_group_display_sdate', group_display_end = '$unix_timestamp_group_display_edate', worker_ix = '".$_SESSION["admininfo"]["charger_ix"]."', upddate = NOW()
                    WHERE
                        cgr_ix = '$cgr_ix' AND con_ix = '$con_ix'
                ";
            } else {
                $sql = "INSERT INTO shop_content_group_relation
                    (con_ix, group_code, group_title, group_title_en, s_group_title, b_group_title, i_group_title, u_group_title, c_group_title,
                     group_explanation, group_explanation_en, b_group_explanation, i_group_explanation, u_group_explanation, c_group_explanation,
                     group_use, group_display_start, group_display_end,
                     worker_ix, regdate, upddate)
                    VALUES
                    ('$con_ix', '".$val."', '".$_POST['group_title'][$val]."', '".$_POST['group_title_en'][$val]."', '".$_POST['s_group_title'][$val]."', '".$b_group_title."', '".$i_group_title."', '".$u_group_title."', '".$c_group_title."',
                    '".$_POST['group_explanation'][$val]."', '".$_POST['group_explanation_en'][$val]."', '".$b_group_explanation."', '".$i_group_explanation."', '".$u_group_explanation."', '".$c_group_explanation."',
                    '".$_POST['group_use'][$val]."', '".$unix_timestamp_group_display_sdate."', '".$unix_timestamp_group_display_edate."',
                    '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())
                ";
            }

            $db->query($sql);

            if ($_POST['group_con_ix'][$val] != '') {
                $sql = "DELETE FROM shop_content_group_relation_content WHERE con_ix = '" . $_POST['group_con_ix'][$val][$key1] . "' AND cgr_ix = '$cgr_ix' ";
                $db->query($sql);

                foreach ($_POST['group_con_ix'][$val] as $key1 => $val1) {
                    $sql = "INSERT INTO shop_content_group_relation_content (cgr_ix, con_ix, sort, worker_ix, regdate, upddate) VALUES ('$cgr_ix', '" . $_POST['group_con_ix'][$val][$key1] . "', '" .$key1. "', '" . $_SESSION["admininfo"]["charger_ix"] . "', NOW(), NOW())";
                    $db->sequences = "SHOP_CONTENT_GROUP_RELATION_CONTENT_SEQ";
                    $db->query($sql);
                }
            }

            if($_POST['rpid'][$val]){
                $sql = "DELETE FROM shop_content_group_product_relation WHERE con_ix = '$con_ix' AND cgr_ix = '$cgr_ix'  ";
                $db->query($sql);

                foreach($_POST['rpid'][$val] as $key1 => $val1){
                    $sql = "INSERT INTO shop_content_group_product_relation (con_ix, cgr_ix, pid, sort, worker_ix, regdate, upddate) VALUES('$con_ix', '$cgr_ix', '".$val1."', '".$key1."', '".$_SESSION["admininfo"]["charger_ix"]."', NOW(), NOW())";
                    $db->sequences = "SHOP_CONTENT_GROUP_PRODUCT_RELATION_SEQ";
                    $db->query($sql);
                }
            }*/
        }
    }else if($content_type == 4){
        $unix_timestamp_display_sdate = mktime($display_start_h,$display_start_i,$display_start_s,substr($display_start,5,2),substr($display_start,8,2),substr($display_start,0,4));
        $unix_timestamp_display_edate = mktime($display_end_h,$display_end_i,$display_end_s,substr($display_end,5,2),substr($display_end,8,2),substr($display_end,0,4));

        $sql = "UPDATE shop_content SET
                    mall_ix = '$mall_ix', 
					title = '".$title."', 
					explanation = '".$explanation."', 
					display_use = '".$display_use."', 
					display_date_use = '".$display_date_use."',
					display_state = '".$display_state."', 
					display_start = '$unix_timestamp_display_sdate', 
					display_end = '$unix_timestamp_display_edate', 
					content_text_pc = '".$content_text_pc."', 
					content_text_mo = '".$content_text_mo."', 
					upddate = NOW()
				WHERE 
					con_ix = '$con_ix'
         ";
        $db->query($sql);
    }

    if($delete_cache == "Y"){
        include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php");
        $tpl = new Template_();
        $tpl->caching = true;
        $tpl->cache_dir = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cache/";

        $tpl->clearCache('000000000000000');
    }


    echo "<Script Language='JavaScript'>alert('수정 되었습니다.');parent.document.location.href='content_edit.php?con_ix=".$con_ix."&cid=".$cid."&content_type=".$content_type."&depth=".$this_depth."&mode=".$mode."';</Script>";
}

if($mode == "Del"){
    if($content_type == 1){
        $sql = "DELETE FROM shop_content_group_product_relation WHERE con_ix = '$con_ix' ";
        $db->query($sql);

        $sql = "DELETE FROM shop_content_group_relation WHERE con_ix = '$con_ix' ";
        $db->query($sql);

        $sql = "DELETE FROM shop_content_product_relation WHERE con_ix = '$con_ix' ";
        $db->query($sql);

        $sql = "DELETE FROM shop_content_relation WHERE con_ix = '$con_ix' ";
        $db->query($sql);

        $contentUpDir	= $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

        $listImgName	= "listImg_".$con_ix.".gif";

        if (file_exists($contentUpDir . "/" .$listImgName)) {
            unlink($contentUpDir . "/" .$listImgName);
        }

        $recomImgName	= "recomImg_".$con_ix.".gif";

        if (file_exists($contentUpDir . "/" .$recomImgName)) {
            unlink($contentUpDir . "/" .$recomImgName);
        }
    }else if($content_type == 2){
        $sql = "DELETE FROM shop_content_group_product_relation WHERE con_ix = '$con_ix' ";
        $db->query($sql);

        $sql = "DELETE FROM shop_content_group_relation WHERE con_ix = '$con_ix' ";
        $db->query($sql);

        $contentUpDir	= $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

        $listImgName	= "listImg_".$con_ix.".gif";

        if (file_exists($contentUpDir . "/" .$listImgName)) {
            unlink($contentUpDir . "/" .$listImgName);
        }

        CopyImageDel($con_ix, "","style");
    }else if($content_type == 3){
        $sql = "DELETE FROM shop_content_group_product_relation WHERE con_ix = '$con_ix' ";
        $db->query($sql);

        $sql = "DELETE FROM shop_content_group_relation_content WHERE con_ix = '$con_ix' ";
        $db->query($sql);

        $sql = "DELETE FROM shop_content_group_relation WHERE con_ix = '$con_ix' ";
        $db->query($sql);

        $sql = "DELETE FROM shop_content_product_relation WHERE con_ix = '$con_ix' ";
        $db->query($sql);

        $sql = "DELETE FROM shop_content_relation WHERE con_ix = '$con_ix' ";
        $db->query($sql);

        $contentUpDir	= $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$con_ix;

        $listImgName	= "listImg_".$con_ix.".gif";

        if (file_exists($contentUpDir . "/" .$listImgName)) {
            unlink($contentUpDir . "/" .$listImgName);
        }

        $recomImgName	= "recomImg_".$con_ix.".gif";

        if (file_exists($contentUpDir . "/" .$recomImgName)) {
            unlink($contentUpDir . "/" .$recomImgName);
        }

        CopyImageDel($con_ix, "","player");
    }

    $sql = "DELETE FROM shop_content WHERE con_ix = '$con_ix' ";
    $db->query($sql);


    echo "<Script Language='JavaScript'>alert('삭제 되었습니다.');parent.document.location.href='content_list.php';</Script>";
}

function CopyImageDel($pid, $type = "", $gubun)
{
    global $admin_config, $image_info2;

    $basicDir		= $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$gubun."/".$pid;

    if (file_exists($basicDir)) {
        unlink($basicDir);
    }

}

function CopyImage($pid, $type = "", $gubun)
{
    global $admin_config, $image_info2;

    $basicDir		= $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$gubun."/".$pid;
    $backUpBasicDir = $_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$gubun."/".$_SESSION['admininfo']['charger_id'];

    if(!is_dir($backUpBasicDir)){
        mkdir($backUpBasicDir);
        chmod($backUpBasicDir,0777);
    }

    //if($_SESSION['admininfo']['charger_id'] == "hmpartner1"){
        foreach($_POST['imgName'] as $key => $val){
            $image_info = getimagesize($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key]);

            // 원본이미지
            copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpBasicDir."/basic_".$pid."_".date('YmdHis', time())."_".$key.".gif");

            if (file_exists($_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$gubun."/".$_SESSION['admininfo']['charger_id']."/".$_POST['imgName'][$key])) {
                unlink($_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$gubun."/".$_SESSION['admininfo']['charger_id']."/".$_POST['imgName'][$key]);
            }
        }

        $handle  = opendir($basicDir); // 디렉토리 open

        $eleCount = 0;

        // 디렉토리의 파일을 전체 삭제.
        while (false !== ($filename = readdir($handle))) {
            // 파일인 경우만 목록에 추가한다.
            if(is_file($basicDir . "/" . $filename)){
                unlink($basicDir . "/" . $filename);
            }
        }

        closedir($handle); // 디렉토리 close

        // 등록된 이미지를 디렉토리로 복사. 등록된 이미지는 삭제.
        $backUpBasicHandle = opendir($backUpBasicDir);
        @mkdir($basicDir);
        while(false !== ($basicFile = readdir($backUpBasicHandle))){
            if(is_file($backUpBasicDir . "/" . $basicFile)){
                copy($backUpBasicDir . "/" . $basicFile, $basicDir . "/" . $basicFile);
                unlink($backUpBasicDir . "/" . $basicFile);
            }
        }
        closedir($backUpBasicHandle);
    /*}else{
        $postCount = 0;

        foreach($_POST['imgName'] as $key => $val){

            $image_info = getimagesize($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key]);

            // 원본이미지
            copy($_SERVER["DOCUMENT_ROOT"].$_POST['imgTemp'][$key]."/".$_POST['imgName'][$key], $backUpBasicDir."/basic_".$pid."_".$key.".gif");

            if (isset($_POST['imgName'][$key]) && strpos($_POST['imgName'][$key], 'basic_') !== false) {

            } else {
                if (file_exists($_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$gubun."/".$_SESSION['admininfo']['charger_id']."/".$_POST['imgName'][$key])) {
                    unlink($_SESSION["admin_config"]["mall_data_root"] . "/images/content/".$gubun."/".$_SESSION['admininfo']['charger_id']."/".$_POST['imgName'][$key]);
                }
            }

            $postCount++;
        }
        $backUpBasicHandle = opendir($backUpBasicDir);
        @mkdir($basicDir);
        while(false !== ($basicFile = readdir($backUpBasicHandle))){
            if(is_file($backUpBasicDir . "/" . $basicFile)){
                copy($backUpBasicDir . "/" . $basicFile, $basicDir . "/" . $basicFile);
                unlink($backUpBasicDir . "/" . $basicFile);
            }
        }
        closedir($backUpBasicHandle);

        $handle  = opendir($basicDir); // 디렉토리 open

        $eleCount = 0;

        // 디렉토리의 파일을 저장
        while (false !== ($filename = readdir($handle))) {
            // 파일인 경우만 목록에 추가한다.
            if(is_file($basicDir . "/" . $filename)){
                $eleCount++;
            }
        }

        closedir($handle); // 디렉토리 close

        if($postCount < $eleCount){
            for($delImgNum = $postCount;$delImgNum < $eleCount;$delImgNum++){

                if (file_exists($basicDir."/basic_".$pid."_".$delImgNum.".gif")) {
                    unlink($basicDir."/basic_".$pid."_".$delImgNum.".gif");
                }
            }
        }
    }*/
}
// 컨텐츠분류관리 수정
if($mode == "modify"){
	if ($sub_mode == "edit_content"){	//분류수정
		if($b_preface == 'on'){
			$b_preface = 'Y';
		}else{
			$b_preface = 'N';
		}

		if($i_preface == 'on'){
			$i_preface = 'Y';
		}else{
			$i_preface = 'N';
		}

		if($u_preface == 'on'){
			$u_preface = 'Y';
		}else{
			$u_preface = 'N';
		}

		if($content_link_yn == 'on'){
			$content_link_yn = 'Y';
		}else{
			$content_link_yn = 'N';
		}

        if($n_preface == 'on'){
            $c_preface = '';
        }

		$sql = "UPDATE shop_content_class SET
					cname = '$cname', 
					global_cname = '$global_cname', 
					b_preface = '$b_preface', 
					i_preface = '$i_preface', 
					u_preface = '$u_preface', 
					c_preface = '$c_preface', 
					content_link = '$content_link', 
					content_link_yn = '$content_link_yn', 
					content_use = '$content_use',
                    content_list_use = '$content_list_use',
					content_view = '$content_view', 
					content_type = '$content_type'
				WHERE 
					cid = '$cid'";

		$db->query($sql);

		if($content_use != "1" && $cid != "000000000000000"){
			$sql = "UPDATE shop_content_class SET content_use ='$content_use' WHERE cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ";
			$db->query($sql);

		}else{

			ParentContentUseUpdate($db, $cid, $this_depth);

			if($this_depth+1 > 0){
				$sql = "UPDATE shop_content_class SET content_use ='$content_use' WHERE cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ";
				$db->query($sql);
			}

		}

	}

	echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert('카테고리 정보가 정상적으로 수정되었습니다.');parent.document.location.href='content_class.php?cid=".$cid."&depth=".$this_depth."';</Script>";
}

function ParentContentUseUpdate($mdb, $cid, $this_depth){
	$where = "";
	for($i=0;$i <= $this_depth;$i++){
		if(!$where){
			$where .= " WHERE (cid LIKE '".substr($cid,0,($i+1)*3)."%' AND depth = '".$i."' ) ";
		}else{
			$where .= " OR (cid LIKE '".substr($cid,0,($i+1)*3)."%' AND depth = '".$i."' ) ";
		}
	}

	$sql = "SELECT cid, cname,  depth, content_use FROM shop_content_class   $where ";

	$mdb->query($sql);

	$parent_content = $mdb->fetchall();
	for($i=0;$i < count($parent_content);$i++){
		$sql = "UPDATE shop_content_class SET content_use ='1' WHERE cid = '".$parent_content[$i][cid]."' ";
		$mdb->query($sql);
	}
}

// 컨텐츠분류관리 삭제
if ($mode == "delete"){
	$udb = new Database;

	if (CheckSubContent($cid,$this_depth)){
		if($sub_content_delete == "1"){
			$sql = "SELECT * FROM shop_content_class WHERE cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);
			$db->fetch();

			$sql = "DELETE FROM shop_content_class WHERE cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);

			echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert(\"삭제 되었습니다.\");</Script>";
			echo "<Script Language='JavaScript'>parent.document.location.href='content_class.php';</Script>";
		}else{
			echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert('하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요');</Script>";
			//'하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요'
		}
	}else{
			$sql = "SELECT * FROM shop_content_class WHERE cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);
			$db->fetch();

			$sql = "DELETE FROM shop_content_class WHERE cid = '$cid'";
			$db->query($sql);

			echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert('삭제되었습니다.');</Script>";
			//'삭제되었습니다.'
			echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>parent.document.location.href='content_class.php?cid=$cid';</Script>";
	}
}

// 컨텐츠분류관리 등록
if ($mode == "insert"){

	if(trim($sub_cid) != "" && trim($cid) != "") {// 카테고리 정보가 제대로 안넘어 올 경우를 검사 kbk 12/03/22
		$sql = "SELECT * FROM shop_content_class WHERE cid = '$cid'";
		$db->query($sql);
		$db->fetch(0);

		$level1 = $db->dt["vlevel1"];
		$level2 = $db->dt["vlevel2"];
		$level3 = $db->dt["vlevel3"];
		$level4 = $db->dt["vlevel4"];
		$level5 = $db->dt["vlevel5"];

		if ($sub_depth+1 == 1){
			$level1 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==2){
			$level2 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==3){
			$level3 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==4){
			$level4 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==5){
			$level5 = getMaxlevel($cid,$sub_depth);
		}

		if($b_preface == 'on'){
			$b_preface = 'Y';
		}else{
			$b_preface = 'N';
		}

		if($i_preface == 'on'){
			$i_preface = 'Y';
		}else{
			$i_preface = 'N';
		}

		if($u_preface == 'on'){
			$u_preface = 'Y';
		}else{
			$u_preface = 'N';
		}

		if($content_link_yn == 'on'){
			$content_link_yn = 'Y';
		}else{
			$content_link_yn = 'N';
		}

        if($n_preface == 'on'){
            $c_preface = '';
        }

		$sql = "insert into shop_content_class
				(cid, depth, vlevel1, vlevel2, vlevel3, vlevel4, vlevel5, cname, global_cname, b_preface, i_preface, u_preface, c_preface, content_link, content_link_yn, content_use, content_list_use, content_view, content_type, regdate)
				values
				('$sub_cid', '$sub_depth','$level1','$level2','$level3','$level4','$level5', '$cname', '$global_cname', '$b_preface', '$i_preface', '$u_preface', '$c_preface', '$content_link', '$content_link_yn', '$content_use', '$content_list_use', '$content_view', '$content_type', NOW())
		";
		$db->query($sql);

		echo "<Script Language='JavaScript'>parent.document.location.href='content_class.php?cid=$cid';</Script>";
	} else {
		echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert(language_data['content.save.php']['D'][language]);</Script>";
		//카테고리 정보가 정확하지 않습니다. 상위 카테고리를 선택해 주세요.
	}

}

function getMaxlevel($cid,$depth)
{
	global $db;

	$strdepth = $depth + 1;

	$sPos = $depth*3;
	$sql = "SELECT max(vlevel$strdepth)+1 AS maxlevel FROM shop_content_class WHERE cid LIKE '".substr($cid,0,$sPos)."%'";

	$db->query($sql);
	$db->fetch(0);

	return $db->dt["maxlevel"];

}

function CheckSubContent($cid,$depth){
	global $db;

	$endpos = $depth*3+3;
	$this_depth = $depth;
	$sql = "SELECT * FROM shop_content_class WHERE depth > $this_depth AND cid LIKE '".substr($cid,0,$endpos)."%'";
	$db->query($sql);

	if ($db->total > 0){
		return true;
	}else{
		return false;
	}

}
?>