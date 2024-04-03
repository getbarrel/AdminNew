<?
include("../class/layout.class");

$db = new Database;

//print_r($_POST);
//exit;

if($act == "getEventInfo"){

	$sql = 'SELECT 
					event_ix, event_title, agent_type, manage_title 
				FROM 
					'.TBL_SHOP_EVENT.' 
				WHERE 
					disp = 1 
					AND event_use_sdate <= "'.time().'" AND event_use_edate >= "'.time().'" 
				ORDER BY event_use_edate DESC';
	$db->query($sql);
	$plan_event_info = $db->fetchall();
	echo json_encode($plan_event_info);
	exit;
}

if($act == "getEventGroupInfo"){

	$sql = "select *,
		(select group_concat(pid SEPARATOR ',') from shop_event_product_relation epr where epg.event_ix = epr.event_ix and epg.group_code = epr.group_code) as r_ix
	from shop_event_product_group epg where event_ix='".$event_ix."' and epg.use_yn = 'Y' order by group_code asc";
	$db->query($sql);
	$plan_event_info = $db->fetchall();
	
	echo json_encode($plan_event_info);
	exit;
}

if ($act == "change_winner"){
	$sql = "update shop_event_applicant set is_winner = '".$is_winner."' where event_ix='$event_ix' and ea_ix='".$ea_ix."'";
	$db->query($sql);
	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('당첨정보 변경이 정상적으로 처리되었습니다.');parent.location.reload()</script>");
	exit;
}

if ($act == "select_winner"){

	$sql = "update shop_event_applicant set is_winner = '0' where event_ix='".$event_ix."'  ";
	$db->query($sql);

	$ea_ix_str = implode("','",$ea_ix);
	$ea_ix_str = "'".$ea_ix_str."'";

	$sql = "update shop_event_applicant set is_winner = '1' where event_ix='".$event_ix."' and ea_ix in (".$ea_ix_str.")  ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('선택한 회원을 정상적으로 당첨처리가 완료되었습니다 .');parent.location.reload()</script>");
}

if ($act == "random_winner"){

	$sql = "update shop_event_applicant set is_winner = '0' where event_ix='".$event_ix."'  ";
	$db->query($sql);
 

	if(!$lottery_amount){
		$lottery_amount = 0;
	}
	$sql = "select ea_ix from shop_event_applicant where is_winner = '0' order by rand() limit ".$lottery_amount."";
	$db->query($sql);
	$random_winners = $db->fetchall();

	for($i=0;$i < count($random_winners) ;$i++){
		$sql = "update shop_event_applicant set is_winner = '1' where event_ix='".$event_ix."' and ea_ix ='".$random_winners[$i]["ea_ix"]."' ";
		$db->query($sql);
	}


	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('선택한 회원을 정상적으로 당첨처리가 완료되었습니다 .');parent.location.reload()</script>");

}

if ($act == "select_comment_disp"){

	$sql = "update shop_event_comment set disp = '".$change_disp."' where event_ix='".$event_ix."' and ec_ix in ('".implode("','",$ec_ix)."') ";
	$db->query($sql);

    echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('정상적으로 처리 완료되었습니다 .');parent.location.reload()</script>");

}

if ($act == "vieworder_update"){
	//$db->query("update ".TBL_SHOP_EVENT_PRODUCT_RELATION." set vieworder=1 ");
	//$_erpid = str_replace("\\\"","\"",$_POST["erpid"]);
	//echo $_erpid;
	//echo "bbb:".count(unserialize($_POST["erpid"]))."<br>";
	//print_r(unserialize($_POST["erpid"]));
	//$_erpid = unserialize(urldecode($_erpid));
	//$erpid = unserialize(urldecode($erpid));
	//echo count($_erpid);
	for($i=0;$i < count($sortlist);$i++){
		$sql = "update ".TBL_SHOP_EVENT_PRODUCT_RELATION." set
			vieworder='".($i+1)."'
			where event_ix='$event_ix' and pid='".$sortlist[$i]."' ";//event_ix='$event_ix' and

		//echo $sql;
		$db->query($sql);
	}

}

if ($act == "update"){
	//$event_text = $content;
	//$event_use_sdate = $FromYY.$FromMM.$FromDD;
	//$event_use_edate = $ToYY.$ToMM.$ToDD;

	$unix_timestamp_sdate = mktime($event_use_sdate_h,$event_use_sdate_i,$event_use_sdate_s,substr($event_use_sdate,5,2),substr($event_use_sdate,8,2),substr($event_use_sdate,0,4));
	$unix_timestamp_edate = mktime($event_use_edate_h,$event_use_edate_i,$event_use_edate_s,substr($event_use_edate,5,2),substr($event_use_edate,8,2),substr($event_use_edate,0,4));

	//	print_r($_POST);
	//	exit;

	if(empty($company_id)){
		$company_id=$admininfo[company_id];
	}

	$send_duration = ($send_duration_H * 60 + $send_duration_M) * 60000;
	$wait_duration = ($wait_duration_H * 60 + $wait_duration_M) * 60000;

	$sql = "update ".TBL_SHOP_EVENT." set
			mall_ix='$mall_ix', agent_type = '$agent_type', 
			company_id='$company_id',
			b_ix='$b_ix',
			md_code='$md_code',
			manage_title='$manage_title',
			event_title='$event_title',			
			event_keyword='$event_keyword',
			event_text='$event_text',
			event_text2='$event_text2',
			b_img_text='$b_img_text',
			b_img_text2='$b_img_text2',
			er_ix='$er_ix',kind='$kind',
			event_use_sdate='$unix_timestamp_sdate',
			event_use_edate='$unix_timestamp_edate',
			send_cond='$send_cond',
			wait='$wait',
			send_duration='$send_duration',
			wait_duration='$wait_duration',
			disp='$disp',
			cid='$category_choice',
			full='$full',
			sort='".($sort =="" ? "999" : $sort )."',
			editdate = NOW()
			where event_ix='$event_ix'";
	
	//place_ix='$place_ix', 2016-05-16 Hong 삭제
	//echo nl2br($sql);
	//exit;
	$db->query($sql);

	$data_text = $event_text;
	$data_text_convert = $event_text;
	$data_text_convert = str_replace("\\","",$data_text_convert);
	preg_match_all("|<img .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);

	$data_text4 = $event_text2;
	$data_text_convert4 = $event_text2;
	$data_text_convert4 = str_replace("\\","",$data_text_convert4);
	preg_match_all("|<img .*src=\"(.*)\".*>|U",$data_text_convert4,$out4, PREG_PATTERN_ORDER);

	$data_text2 = $b_img_text;
	$data_text_convert2 = $b_img_text;
	$data_text_convert2 = str_replace("\\","",$data_text_convert2);
	preg_match_all("|<img .*src=\"(.*)\".*>|U",$data_text_convert2,$out2, PREG_PATTERN_ORDER);

	$data_text3 = $b_img_text2;
	$data_text_convert3 = $b_img_text2;
	$data_text_convert3 = str_replace("\\","",$data_text_convert3);
	preg_match_all("|<img .*src=\"(.*)\".*>|U",$data_text_convert3,$out3, PREG_PATTERN_ORDER);

	$LAST_ID = $event_ix;
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/";

	//if(substr_count($data_text,"<IMG") > 0){
		if(!is_dir($path)){
			mkdir($path, 0777);
			//chmod($path,0777)
		}
	//}

	if($img_del){
		unlink($path."/event_banner_".$LAST_ID.".gif");
	}
	if($b_img_del){
		unlink($path."/b_event_banner_".$LAST_ID.".gif");
	}

	if(is_dir($path)){
		if($s_img_size > 0){
			move_uploaded_file($s_img, $path."/event_banner_".$LAST_ID.".gif");
		}
	}
	if(is_dir($path)){
		if($b_img_size > 0){
			move_uploaded_file($b_img, $path."/b_event_banner_".$LAST_ID.".gif");
		}
	}



	for($i=0;$i < count($out);$i++){
		for($j=0;$j < count($out[$i]);$j++){

			$img = returnImagePath($out[$i][$j]);
			$img = ClearText($img);

			if($img){
				if(substr_count($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
					if(substr_count($img,"$HTTP_HOST")>0){
						$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);
						

						@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img));
						if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
							@unlink($local_img_path);
						}

						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}else{
						//echo substr_count($img,$_SERVER["DOCUMENT_ROOT"]);
						//if(!(substr_count(" ".$img,$_SERVER["DOCUMENT_ROOT"]) > 0)){
						//	$img = $_SERVER["DOCUMENT_ROOT"].$img;
						//}
						//echo $img;
						//exit;
						if(copy($_SERVER["DOCUMENT_ROOT"].$img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img))){
							//echo $img;
							//exit;
							$data_text = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
							//echo $data_text ;
							//exit;
						}
					}
				}
			}
		}
	}
	
	for($i=0;$i < count($out2);$i++){
		for($j=0;$j < count($out2[$i]);$j++){

			$img = returnImagePath($out2[$i][$j]);
			$img = ClearText($img);

			if($img){
				if(substr_count($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
					if(substr_count($img,"$HTTP_HOST")>0){
						$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);
						

						@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img));
						if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
							@unlink($local_img_path);
						}

						$data_text2 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text2);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}else{
						//echo substr_count($img,$_SERVER["DOCUMENT_ROOT"]);
						//if(!(substr_count(" ".$img,$_SERVER["DOCUMENT_ROOT"]) > 0)){
						//	$img = $_SERVER["DOCUMENT_ROOT"].$img;
						//}
						//echo $img;
						//exit;
						if(copy($_SERVER["DOCUMENT_ROOT"].$img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img))){
							//echo $img;
							//exit;
							$data_text2 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text2);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
							//echo $data_text ;
							//exit;
						}
					}
				}
			}
		}
	}

	for($i=0;$i < count($out3);$i++){
		for($j=0;$j < count($out3[$i]);$j++){

			$img = returnImagePath($out3[$i][$j]);
			$img = ClearText($img);

			if($img){
				if(substr_count($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
					if(substr_count($img,"$HTTP_HOST")>0){
						$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);
						

						@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img));
						if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
							@unlink($local_img_path);
						}

						$data_text3 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text3);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}else{
						if(copy($_SERVER["DOCUMENT_ROOT"].$img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img))){
							// 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
							$data_text3 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text3);	 
						}
					}
				}
			}
		}
	}

	for($i=0;$i < count($out4);$i++){
		for($j=0;$j < count($out4[$i]);$j++){

			$img = returnImagePath($out4[$i][$j]);
			$img = ClearText($img);

			if($img){
				if(substr_count($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
					if(substr_count($img,"$HTTP_HOST")>0){
						$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);
						

						@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img));
						if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
							@unlink($local_img_path);
						}

						// 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
						$data_text4 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text4);
					}else{
						if(copy($_SERVER["DOCUMENT_ROOT"].$img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img))){
							// 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
							$data_text4 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text4);
						}
					}
				}
			}
		}
	}

	//exit;
	$data_text = str_replace("http://$HTTP_HOST","",$data_text);
	$db->query("UPDATE ".TBL_SHOP_EVENT." SET event_text = '$data_text' WHERE event_ix='$LAST_ID'");
	
	$data_text = str_replace("http://$HTTP_HOST","",$data_text4);
	$db->query("UPDATE ".TBL_SHOP_EVENT." SET event_text2 = '$data_text4' WHERE event_ix='$LAST_ID'");

	$data_text2 = str_replace("http://$HTTP_HOST","",$data_text2);
	$db->query("UPDATE ".TBL_SHOP_EVENT." SET b_img_text = '$data_text2' WHERE event_ix='$LAST_ID'");
	
	$data_text3 = str_replace("http://$HTTP_HOST","",$data_text3);
	$db->query("UPDATE ".TBL_SHOP_EVENT." SET b_img_text2 = '$data_text3' WHERE event_ix='$LAST_ID'");

	$event_ix = $LAST_ID;
	
	$sql = "update shop_event_place_relation set insert_yn='N'
					where event_ix='".$event_ix."' ";
	$db->query($sql);

	if( count($place_ix) > 0 ){
		foreach($place_ix as $p_ix){
			$db->query("Select epr_ix from shop_event_place_relation where event_ix='".$event_ix."' and place_ix = '".$p_ix."' ");
			if($db->total){
				$db->fetch();
				$epr_ix = $db->dt[epr_ix]; 
				$sql = "update shop_event_place_relation set insert_yn='Y' where epr_ix = '".$epr_ix."' ";
				$db->query($sql);
			}else{
				$sql = "insert into shop_event_place_relation (epr_ix,event_ix,place_ix, insert_yn, regdate) values('','".$event_ix."','".$p_ix."','Y',NOW())";
				$db->sequences = "SHOP_EVENT_GOODS_GROUP_SEQ";
				$db->query($sql);
			}
		}
	}

	$db->query("delete from shop_event_place_relation where event_ix = '".$event_ix."' and insert_yn = 'N' ");


	$sql = "update ".TBL_SHOP_EVENT_PRODUCT_RELATION." set insert_yn='N'
					where event_ix='".$event_ix."' ";
	$db->query($sql);

	$sql = "update shop_event_product_group set insert_yn='N'
					where event_ix='".$event_ix."' ";
	$db->query($sql);
	//$db->debug = true;
	if($before_group_code != $group_order[$before_group_code]){
		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/".$event_ix."/tmp/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}
		recurse_copy($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/".$event_ix."/", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/".$event_ix."/tmp/");
	}
	
	$epg_ix_array = $epg_ix;
	$epg_ix="";
	for($i=0;$i < count($group_name);$i++){
		$before_group_code = $i+1;

		$db->query("Select epg_ix from shop_event_product_group where event_ix='".$event_ix."' and epg_ix = '".$epg_ix_array[$before_group_code]."' ");//group_code = '".($before_group_code)."' 

		if($db->total){
			$db->fetch();
			$epg_ix = $db->dt[epg_ix]; 
			$sql = "update shop_event_product_group set 
							group_name='".$group_name[$before_group_code]."', group_code='".$group_order[$before_group_code]."', event_name='".$event_name[$before_group_code]."', product_cnt='".$product_cnt[$before_group_code]."',insert_yn='Y', use_yn='".$use_yn[$before_group_code]."'
							where epg_ix = '".$epg_ix."' ";//display_type='".$display_type[$before_group_code]."',   //event_ix='".$event_ix."' and group_code = '".($before_group_code)."' 
			$db->query($sql);
		}else{
			$sql = "insert into shop_event_product_group (epg_ix,event_ix,group_name, event_name, group_code,display_type, product_cnt, insert_yn, use_yn, regdate) values('','".$event_ix."','".$group_name[$before_group_code]."','".$event_name[$before_group_code]."','".$group_order[$before_group_code]."','','".$product_cnt[$before_group_code]."','Y','".$use_yn[$before_group_code]."',NOW())";//".$display_type[$before_group_code]."
			$db->sequences = "SHOP_EVENT_GOODS_GROUP_SEQ";
			$db->query($sql);

			$db->query("SELECT epg_ix FROM  shop_event_product_group WHERE epg_ix=LAST_INSERT_ID()");
			$db->fetch();
			$epg_ix = $db->dt[epg_ix];
		}

		$db->query("update shop_event_group_display set insert_yn = 'N' where epg_ix = '".$epg_ix."'   ");

		for($j=0;$j < count($display_type[$before_group_code][type]);$j++){
			$db->query("select egd_ix from shop_event_group_display where egd_ix = '".$display_type[$before_group_code][egd_ix][$j]."'   ");

			if(!$db->total){
				$sql = "insert into shop_event_group_display (egd_ix,epg_ix, display_type, set_cnt, vieworder, insert_yn, regdate) values ('','".$epg_ix."','".$display_type[$before_group_code][type][$j]."','".$display_type[$before_group_code][set_cnt][$j]."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_event_group_display set insert_yn = 'Y',vieworder='".($j+1)."', display_type = '".$display_type[$before_group_code][type][$j]."',set_cnt = '".$display_type[$before_group_code][set_cnt][$j]."' 
							where egd_ix = '".$display_type[$before_group_code][egd_ix][$j]."'  ";
				$db->query($sql);
			}
		}
		$db->query("delete from shop_event_group_display where epg_ix = '".$epg_ix."' and insert_yn = 'N' ");

		if ($group_img_chk[$before_group_code] == 'y'){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/event_group_".($before_group_code).".gif");
		}
		
		if ($group_img_m_chk[$before_group_code] == 'y'){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/m_event_group_".($before_group_code).".gif");
		}

		if ($group_img[$before_group_code] != "")
		{
			copy($group_img[$before_group_code], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/event_group_".($before_group_code).".gif");
		}
		if ($group_img_m[$before_group_code] != "")
		{
			copy($group_img_m[$before_group_code], $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/m_event_group_".($before_group_code).".gif");
		}
		if($before_group_code != $group_order[$before_group_code]){
			if(!is_dir($path)){
			mkdir($path, 0777);

			}
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/tmp/event_group_".($before_group_code).".gif")){ 
				copy($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/tmp/event_group_".($before_group_code).".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/".$event_ix."/event_group_".($group_order[$before_group_code]).".gif");
			}

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/tmp/m_event_group_".($before_group_code).".gif")){ 
				copy($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/tmp/m_event_group_".($before_group_code).".gif", $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/".$event_ix."/m_event_group_".($group_order[$before_group_code]).".gif");
			}
		}
		//$db->query("SELECT epg_ix FROM shop_event_product_group WHERE epg_ix=LAST_INSERT_ID()");
		//$db->fetch();
		//$epg_ix = $db->dt[0];
		//$db->debug = true;
		//$db->query("update ".TBL_SHOP_EVENT_PRODUCT_RELATION." set insert_yn = 'N' where event_ix='".$event_ix."' and group_code = '".($before_group_code)."' ");
		if($before_group_code != $group_order[$before_group_code]){
			//$db->query("update ".TBL_SHOP_EVENT_PRODUCT_RELATION." set group_code = '".$group_order[$before_group_code]."' where event_ix='".$event_ix."' and group_code = '".$before_group_code."' ");
			// 그룹이 변경이 된경우 상품관련 group_code를 일괄로 변경
		}
		//echo "<pre>";print_r($rpid[$before_group_code]);
		for($j=0;$j < count($rpid[$before_group_code]);$j++){
			$db->query("Select event_ix from ".TBL_SHOP_EVENT_PRODUCT_RELATION." where event_ix='".$event_ix."' and group_code = '".$before_group_code."' and pid = '".$rpid[$before_group_code][$j]."' ");

			if(!$db->total){
					$sql = "insert into ".TBL_SHOP_EVENT_PRODUCT_RELATION." (erp_ix,pid,event_ix, group_code, vieworder, insert_yn, regdate) values ('','".$rpid[$before_group_code][$j]."','".$event_ix."','".$group_order[$before_group_code]."','".($j+1)."','Y', NOW())";
					$db->sequences = "SHOP_EVENT_GOODS_LINK_SEQ";
					$db->query($sql);
			}else{
				$sql = "update ".TBL_SHOP_EVENT_PRODUCT_RELATION." set insert_yn = 'Y',vieworder='".($j+1)."', group_code = '".$group_order[$before_group_code]."' where event_ix='".$event_ix."' and group_code = '".$before_group_code."' and pid = '".$rpid[$before_group_code][$j]."' ";
				$db->query($sql);
			}
		}

		$db->query("delete from ".TBL_SHOP_EVENT_PRODUCT_RELATION." where event_ix='".$event_ix."' and group_code = '".$before_group_code."' and insert_yn = 'N' ");
		
	}

	$db->query("delete from ".TBL_SHOP_EVENT_PRODUCT_RELATION." where event_ix='".$event_ix."' and insert_yn = 'N' ");

	//exit;
	$db->query("select *  from shop_event_config where event_ix='".$event_ix."' ");
	if($db->total){
		$sql = "update shop_event_config set					 
					 event_type='".$_POST[event_type]."',					 
					 use_comment='".$_POST[use_comment]."',
					 use_comment_category='".$_POST[use_comment_category]."',
					 lottery_type='".$_POST[lottery_type]."',
					 lottery_probability='".$_POST[lottery_probability]."',					 
					 lottery_amount='".$_POST[lottery_amount]."',
					 lottery_method='".$_POST[lottery_method]."',
					 lottery_date='".$_POST[lottery_date]."',
					 participation_able_times='".$_POST[participation_able_times]."',
					 participation_method='".$_POST[participation_method]."',
					 participation_use_point='".$_POST[participation_use_point]."',
					 participation_saving_point='".$_POST[participation_saving_point]."',
					 lottery_announce='".$_POST[lottery_announce]."'	,
					 exposure_rate='".$_POST[exposure_rate]."'	
					 where event_ix='".$_POST[event_ix]."' ";
					 //ALTER TABLE `shop_event_config`  ADD `lottery_announce` MEDIUMTEXT NOT NULL COMMENT '당첨안내' AFTER `participation_saving_point`

	}else{
		$sql = "insert into shop_event_config
					(event_ix,event_type,lottery_type,lottery_probability, lottery_amount,lottery_method,lottery_date,participation_able_times,participation_method,participation_use_point,participation_saving_point, lottery_announce,exposure_rate, regdate) 
					values
					('".$event_ix."','".$_POST[event_type]."','".$_POST[lottery_type]."','".$_POST[lottery_probability]."','".$_POST[lottery_amount]."','".$_POST[lottery_method]."','".$_POST[lottery_date]."','".$_POST[participation_able_times]."','".$_POST[participation_method]."','".$_POST[participation_use_point]."','".$_POST[participation_saving_point]."','".$_POST[lottery_announce]."','".$_POST[exposure_rate]."',NOW()) ";
	}
	
	$db->query($sql);

	

	/*
	$db->query("Select group_code from shop_event_product_group where event_ix='".$event_ix."' and insert_yn = 'N' ");


	if($db->total){
		$ddb = new Database;
		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);
			$ddb->query("delete from ".TBL_SHOP_EVENT_PRODUCT_RELATION." where event_ix='".$event_ix."' and group_code = '".$db->dt[group_code]."' ");
		}
	}
	*/
	$db->query("delete from ".TBL_SHOP_EVENT_PRODUCT_RELATION." where event_ix='".$event_ix."' and insert_yn = 'N' ");
	$db->query("delete from shop_event_product_group where event_ix='".$event_ix."' and insert_yn = 'N' ");
	/*
	$db->query("update ".TBL_SHOP_EVENT_PRODUCT_RELATION." set insert_yn = 'N' where event_ix='$LAST_ID' ");

	for($i=0;$i < count($rpid);$i++){
		$db->query("Select event_ix from ".TBL_SHOP_EVENT_PRODUCT_RELATION." where event_ix='$LAST_ID' and pid = '".$rpid[$i]."' ");

		if(!$db->total){
			$sql = "insert into ".TBL_SHOP_EVENT_PRODUCT_RELATION." (erp_ix,event_ix,pid, vieworder, insert_yn, regdate) values ('','".$LAST_ID."','".$rpid[$i]."','".($before_group_code)."','Y', NOW())";
			$db->query($sql);
		}else{
			$sql = "update ".TBL_SHOP_EVENT_PRODUCT_RELATION." set insert_yn = 'Y',vieworder='".($before_group_code)."' where event_ix='$LAST_ID' and pid = '".$rpid[$i]."' ";
			$db->query($sql);
		}
		//echo $sql;
	}
	//exit;
	$db->query("delete from ".TBL_SHOP_EVENT_PRODUCT_RELATION." where event_ix='$LAST_ID' and insert_yn = 'N' ");
	*/
	//exit;
	//$db->debug = true;
	//print_r($_FILES);
	//print_r($_POST);
	//exit;

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event";
	//$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/".$event_ix."/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/".$event_ix."/puzzle/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}
	if(is_array($event_picturepuzzles)){//$_FILES['ep_file']['size']
		//foreach($_FILES['ep_file']['size'] as $_key=>$_val)	{
		//	$db->debug = true;
		for($i=0; $i < count($event_picturepuzzles);$i++){
			//echo $_key;
			//exit;
			/*
			if ($_FILES[event_picturepuzzles][size][$i][ep_file] > 0 && $event_picturepuzzles[$i][nondelete] == 1){
			//if($event_picturepuzzles[$i][ep_ix] != "" && $event_picturepuzzles[$i][nondelete] == 1){
				$sql = "update shop_event_picturepuzzle set tmp_update='1' where ep_ix='".$event_picturepuzzles[$i][ep_ix]."' ";
				$db->query($sql);
			}
			*/
			//echo $_FILES[event_picturepuzzles][name][$i][ep_file];
			//exit;
			//echo "size:".$_FILES[event_picturepuzzles][size][$i][ep_file]."<br><br>" ;
			if($_FILES[event_picturepuzzles][size][$i][ep_file] > 0)	{//$event_picturepuzzles[$i]['ep_file']
				copy($_FILES["event_picturepuzzles"]["tmp_name"][$i]["ep_file"], $path."/".$_FILES[event_picturepuzzles][name][$i][ep_file]);
			}

			if($event_picturepuzzles[$i][ep_ix] == ""){ 
				$sql = "insert into shop_event_picturepuzzle
							(ep_ix,event_ix,ep_title,ep_link,ep_file,regdate,tmp_update) values
							('','$event_ix','".$event_picturepuzzles[$i][ep_title]."','".$event_picturepuzzles[$i][ep_link]."','".$_FILES[event_picturepuzzles][name][$i][ep_file]."', NOW(),'1')";

				$db->sequences = "SHOP_BANNERINFO_DETAIL_SEQ";
				$db->query($sql);
			} else {
				if($_FILES[event_picturepuzzles][name][$i][ep_file] == ""){
					$sql = "update shop_event_picturepuzzle set 
								ep_link='".$event_picturepuzzles[$i][ep_link]."',ep_title='".$event_picturepuzzles[$i][ep_title]."', tmp_update='1' 
								where ep_ix='".$event_picturepuzzles[$i][ep_ix]."' ";

					$db->query($sql);

				}else{
					$sql = "update shop_event_picturepuzzle set 
								ep_file='".$_FILES[event_picturepuzzles][name][$i][ep_file]."',ep_link='".$event_picturepuzzles[$i][ep_link]."',ep_title='".$event_picturepuzzles[$i][ep_title]."', tmp_update='1' 
								where ep_ix='".$event_picturepuzzles[$i][ep_ix]."' ";

					$db->query($sql);
				}
			}
		}
	}
	$db->query("delete from shop_event_picturepuzzle where event_ix='".$_POST[event_ix]."' and tmp_update = '0' ");
//exit;

	$db->query("update shop_event_gift set insert_yn = 'N' where event_ix='".$_POST[event_ix]."' ");
	
	for($i=0;$i < count($event_gift);$i++){
		$db->query("Select event_ix from shop_event_gift where event_ix='$event_ix' and eg_ix = '".$event_gift[$i][eg_ix]."' ");

		if(!$db->total && $event_gift[$i][gift_name] != ""){
			$sql = "insert into shop_event_gift
				(eg_ix,event_ix,ranking, gift_name,gift_type,gift_code,gift_amount,use_point,insert_yn,regdate) values
				('".$event_gift[$i][eg_ix]."','".$event_ix."','".$event_gift[$i][ranking]."','".$event_gift[$i][gift_name]."','".$event_gift[$i][gift_type]."','".$event_gift[$i][gift_code]."','".$event_gift[$i][gift_amount]."','".$event_gift[$i][use_point]."','Y',NOW())";
			$db->query($sql);

		}else{
			//$sql = "update shop_event_gift set insert_yn = 'Y' where event_ix='$event_ix' and eg_ix = '".$event_gift[$i][eg_ix]."' ";
			$sql = "update shop_event_gift set			 
			 ranking='".$event_gift[$i][ranking]."',
			 gift_name='".$event_gift[$i][gift_name]."',
			 gift_type='".$event_gift[$i][gift_type]."',
			 gift_code='".$event_gift[$i][gift_code]."',
			 gift_amount='".$event_gift[$i][gift_amount]."',
			 use_point='".$event_gift[$i][use_point]."',
			 insert_yn='Y'
			 where eg_ix='".$event_gift[$i][eg_ix]."' and event_ix='".$event_ix."' ";
			$db->query($sql);
		}
	}
	
	$db->query("delete from shop_event_gift where event_ix='".$event_ix."' and insert_yn = 'N' ");

	//exit;
	if($delete_cache == "Y"){
		include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php");
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cache/";

		$tpl->clearCache('goods_event_'.$event_ix);
	}
    $sql = "update ".TBL_SHOP_EVENT." set worker_ix = '' where event_ix= '$event_ix' ";
    $db->query($sql);
//	o2oSendPush('E',$event_ix,"U");


    if($agent_type == "M"){
        echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('이벤트 작업이 정상적으로 수정 되었습니다.');parent.location.href='../mShop/event.list.php'</script>");
        //echo("<script>top.location.href = '/admin/mShop/category_main.list.php?mmode=$mmode';</script>");
    }else{
        echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('이벤트 작업이 정상적으로 수정 되었습니다.');parent.location.href='../display/event.list.php'</script>");
        //echo("<script>top.location.href = '/admin/display/category_main.list.php?mmode=$mmode';</script>");
    }
	//echo("<script>top.location.href = 'event.write.php?event_ix=$event_ix';</script>");
	//echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('이벤트 작업이 정상적으로 수정 되었습니다.');top.location.href='../display/event.list.php'</script>");
}

if ($act == "insert"){

	//	$event_text = $content;
	//$event_use_sdate = $FromYY.$FromMM.$FromDD;
	//$event_use_edate = $ToYY.$ToMM.$ToDD;

	$unix_timestamp_sdate = mktime($event_use_sdate_h,$event_use_sdate_i,$event_use_sdate_s,substr($event_use_sdate,5,2),substr($event_use_sdate,8,2),substr($event_use_sdate,0,4));
	$unix_timestamp_edate = mktime($event_use_edate_h,$event_use_edate_i,$event_use_edate_s,substr($event_use_edate,5,2),substr($event_use_edate,8,2),substr($event_use_edate,0,4));

	if(empty($company_id)){
		$company_id=$admininfo[company_id];
	}

	$send_duration = ($send_duration_H * 60 + $send_duration_M) * 60000;
	$wait_duration = ($wait_duration_H * 60 + $wait_duration_M) * 60000;

	$sql = "insert into ".TBL_SHOP_EVENT."
			(event_ix,mall_ix, agent_type, company_id, b_ix, md_code, send_cond, wait, send_duration, wait_duration, cid, er_ix, kind, manage_title, event_title,event_keyword, event_text, event_text2,b_img_text, b_img_text2,event_use_sdate,event_use_edate,disp,full, place_ix, sort, editdate, regdate)
			values
			('','$mall_ix','$agent_type','".$company_id."','".$b_ix."','$md_code','$send_cond','$wait','$send_duration','$wait_duration','$category_choice','$er_ix','$kind','$manage_title','$event_title','$event_keyword','$event_text', '$event_text2','$b_img_text','$b_img_text2','$unix_timestamp_sdate','$unix_timestamp_edate','$disp','$full','$place_ix','".($sort =="" ? "999" : $sort )."',NOW(),NOW())";

	$db->sequences = "SHOP_EVENT_SEQ";
	$db->query($sql);

	if($db->dbms_type == "oracle"){
		$LAST_ID = $db->last_insert_id;
	}else{
		$db->query("SELECT event_ix FROM ".TBL_SHOP_EVENT." WHERE event_ix=LAST_INSERT_ID()");
		$db->fetch();
		$LAST_ID = $db->dt[event_ix];
	}

	$data_text = $event_text;
	$data_text_convert = $event_text;
	$data_text_convert = str_replace("\\","",$data_text_convert);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert,$out, PREG_PATTERN_ORDER);

	$data_text4 = $event_text2;
	$data_text_convert4 = $event_text2;
	$data_text_convert4 = str_replace("\\","",$data_text_convert4);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert4,$out4, PREG_PATTERN_ORDER);

	$data_text2 = $b_img_text;
	$data_text_convert2 = $b_img_text;
	$data_text_convert2 = str_replace("\\","",$data_text_convert2);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert2,$out2, PREG_PATTERN_ORDER);

	$data_text3 = $b_img_text2;
	$data_text_convert3 = $b_img_text2;
	$data_text_convert3 = str_replace("\\","",$data_text_convert3);
	preg_match_all("|<IMG .*src=\"(.*)\".*>|U",$data_text_convert3,$out3, PREG_PATTERN_ORDER);

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		//chmod($path,0777)
	}

	if(is_dir($path)){
		if($s_img_size > 0){
			move_uploaded_file($s_img, $path."/event_banner_".$LAST_ID.".gif");
		}

	}
	if(is_dir($path)){
		if($b_img_size > 0){
			move_uploaded_file($b_img, $path."/b_event_banner_".$LAST_ID.".gif");
		}

	}

	for($i=0;$i < count($out);$i++){
		for($j=0;$j < count($out[$i]);$j++){

			$img = returnImagePath($out[$i][$j]);
			$img = ClearText($img);


			if(substr_count($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
				if(substr_count($img,"$HTTP_HOST")>0){
					$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

					@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img));
					if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
						@unlink($local_img_path);
					}

					$data_text = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
				}else{
					if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img))){
						$data_text = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}
				}
			}
		}
	}

	for($i=0;$i < count($out2);$i++){
		for($j=0;$j < count($out2[$i]);$j++){

			$img = returnImagePath($out2[$i][$j]);
			$img = ClearText($img);


			if(substr_count($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
				if(substr_count($img,"$HTTP_HOST")>0){
					$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

					@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img));
					if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
						@unlink($local_img_path);
					}

					$data_text2 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text2);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
				}else{
					if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img))){
						$data_text2 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text2);	 // 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					}
				}
			}
		}
	}

	for($i=0;$i < count($out3);$i++){
		for($j=0;$j < count($out3[$i]);$j++){

			$img = returnImagePath($out3[$i][$j]);
			$img = ClearText($img);


			if(substr_count($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
				if(substr_count($img,"$HTTP_HOST")>0){
					$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

					@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img));
					if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
						@unlink($local_img_path);
					}

					// 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					$data_text3 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text3);	 
				}else{
					if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img))){
						// 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
						$data_text3 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text3);	 
					}
				}
			}
		}
	}

	for($i=0;$i < count($out4);$i++){
		for($j=0;$j < count($out4[$i]);$j++){

			$img = returnImagePath($out4[$i][$j]);
			$img = ClearText($img);


			if(substr_count($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/") == 0){// 이미지 URL 이 이벤트 관련 폴더에 있지 않으면 ...
				if(substr_count($img,"$HTTP_HOST")>0){
					$local_img_path = str_replace("http://$HTTP_HOST",$_SERVER["DOCUMENT_ROOT"]."",$img);

					@copy($local_img_path,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img));
					if(substr_count($img,$admin_config[mall_data_root]."/images/upfile/") > 0){
						@unlink($local_img_path);
					}

					// 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
					$data_text4 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text4);	 
				}else{
					if(copy($img,$_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img))){
						// 업로드된 파일들이 URL 에 관계 없이 보일수 있도록 URL 을  / 로 치환
						$data_text4 = str_replace($img,$admin_config[mall_data_root]."/images/event/$LAST_ID/".returnFileName($img),$data_text4);	 
					}
				}
			}
		}
	}

	$data_text = str_replace("http://$HTTP_HOST","",$data_text);
	$db->query("UPDATE ".TBL_SHOP_EVENT." SET event_text = '$data_text' WHERE event_ix='$LAST_ID'");
	
	$data_text4 = str_replace("http://$HTTP_HOST","",$data_text4);
	$db->query("UPDATE ".TBL_SHOP_EVENT." SET event_text2 = '$data_text4' WHERE event_ix='$LAST_ID'");

	$data_text2 = str_replace("http://$HTTP_HOST","",$data_text2);
	$db->query("UPDATE ".TBL_SHOP_EVENT." SET b_img_text = '$data_text2' WHERE event_ix='$LAST_ID'");
	
	$data_text3 = str_replace("http://$HTTP_HOST","",$data_text3);
	$db->query("UPDATE ".TBL_SHOP_EVENT." SET b_img_text2 = '$data_text3' WHERE event_ix='$LAST_ID'");

	$event_ix = $LAST_ID;


	if( count($place_ix) > 0 ){
		foreach($place_ix as $p_ix){
			$sql = "insert into shop_event_place_relation (epr_ix,event_ix,place_ix, insert_yn, regdate) values('','".$event_ix."','".$p_ix."','Y',NOW())";
			$db->sequences = "SHOP_EVENT_GOODS_GROUP_SEQ";
			$db->query($sql);
		}
	}


	$sql = "insert into shop_event_config
					(event_ix,event_type,use_comment,use_comment_category, lottery_type,lottery_amount,lottery_method,lottery_date,participation_able_times,participation_method,participation_use_point,lottery_announce, regdate) 
					values
					('".$event_ix."','".$_POST[event_type]."','".$_POST[use_comment]."','".$_POST[use_comment_category]."','".$_POST[lottery_type]."','".$_POST[lottery_amount]."','".$_POST[lottery_method]."','".$_POST[lottery_date]."','".$_POST[participation_able_times]."','".$_POST[participation_method]."','".$_POST[participation_use_point]."', '".$_POST[lottery_announce]."', NOW()) ";
	
	
	$db->query($sql);


	
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event";
	//$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/".$event_ix."/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}
	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/".$event_ix."/puzzle/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}
	if(is_array($event_picturepuzzles)){//$_FILES['ep_file']['size']
		//foreach($_FILES['ep_file']['size'] as $_key=>$_val)	{
		//	$db->debug = true;
		for($i=0; $i < count($event_picturepuzzles);$i++){
			//echo $_key;
			//exit;
			/*
			if ($_FILES[event_picturepuzzles][size][$i][ep_file] > 0 && $event_picturepuzzles[$i][nondelete] == 1){
			//if($event_picturepuzzles[$i][ep_ix] != "" && $event_picturepuzzles[$i][nondelete] == 1){
				$sql = "update shop_event_picturepuzzle set tmp_update='1' where ep_ix='".$event_picturepuzzles[$i][ep_ix]."' ";
				$db->query($sql);
			}
			*/
			//echo $_FILES[event_picturepuzzles][name][$i][ep_file];
			//exit;
			//echo "size:".$_FILES[event_picturepuzzles][size][$i][ep_file]."<br><br>" ;
			if($_FILES[event_picturepuzzles][size][$i][ep_file] > 0)	{//$event_picturepuzzles[$i]['ep_file']
				copy($_FILES["event_picturepuzzles"]["tmp_name"][$i]["ep_file"], $path."/".$_FILES[event_picturepuzzles][name][$i][ep_file]);
			}

			if($event_picturepuzzles[$i][ep_ix] == ""){ 
				$sql = "insert into shop_event_picturepuzzle
							(ep_ix,event_ix,ep_title,ep_link,ep_file,regdate,tmp_update) values
							('','$event_ix','".$event_picturepuzzles[$i][ep_title]."','".$event_picturepuzzles[$i][ep_link]."','".$_FILES[event_picturepuzzles][name][$i][ep_file]."', NOW(),'1')";

				$db->sequences = "SHOP_BANNERINFO_DETAIL_SEQ";
				$db->query($sql);
			} else {
				if($_FILES[event_picturepuzzles][name][$i][ep_file] == ""){
					$sql = "update shop_event_picturepuzzle set 
								ep_link='".$event_picturepuzzles[$i][ep_link]."',ep_title='".$event_picturepuzzles[$i][ep_title]."', tmp_update='1' 
								where ep_ix='".$event_picturepuzzles[$i][ep_ix]."' ";

					$db->query($sql);

				}else{
					$sql = "update shop_event_picturepuzzle set 
								ep_file='".$_FILES[event_picturepuzzles][name][$i][ep_file]."',ep_link='".$event_picturepuzzles[$i][ep_link]."',ep_title='".$event_picturepuzzles[$i][ep_title]."', tmp_update='1' 
								where ep_ix='".$event_picturepuzzles[$i][ep_ix]."' ";

					$db->query($sql);
				}
			}
		}
	} 
	
	for($i=0;$i < count($event_gift);$i++){
		$db->query("Select event_ix from shop_event_gift where event_ix='$event_ix' and eg_ix = '".$event_gift[$i][eg_ix]."' ");

		if(!$db->total && $event_gift[$i][gift_name] != ""){
			$sql = "insert into shop_event_gift
				(eg_ix,event_ix,ranking, gift_name,gift_type,gift_code,gift_amount,use_point,insert_yn,regdate) values
				('".$event_gift[$i][eg_ix]."','".$event_ix."','".$event_gift[$i][ranking]."','".$event_gift[$i][gift_name]."','".$event_gift[$i][gift_type]."','".$event_gift[$i][gift_code]."','".$event_gift[$i][gift_amount]."','".$event_gift[$i][use_point]."','Y',NOW())";
			$db->query($sql);

		}else{
			//$sql = "update shop_event_gift set insert_yn = 'Y' where event_ix='$event_ix' and eg_ix = '".$event_gift[$i][eg_ix]."' ";
			$sql = "update shop_event_gift set			 
			 ranking='".$event_gift[$i][ranking]."',
			 gift_name='".$event_gift[$i][gift_name]."',
			 gift_type='".$event_gift[$i][gift_type]."',
			 gift_code='".$event_gift[$i][gift_code]."',
			 gift_amount='".$event_gift[$i][gift_amount]."',
			 use_point='".$event_gift[$i][use_point]."',
			 insert_yn='Y'
			 where eg_ix='".$event_gift[$i][eg_ix]."' and event_ix='".$event_ix."' ";
			$db->query($sql);
		}
	} 


	for($i=0;$i < count($group_name);$i++){
		//$sql = "insert into shop_event_product_group(epg_ix,event_ix,group_name,group_code,display_type,use_yn, regdate) values('','".$event_ix."','".$group_name[$i+1]."',".$display_type[$i+1].",'".($i+1)."','".$use_yn[$i+1]."',NOW())";
		$sql = "insert into shop_event_product_group(epg_ix,event_ix,group_name,event_name, group_code,display_type,product_cnt, use_yn, regdate) values('','".$event_ix."','".$group_name[$i+1]."','".$event_name[$i+1]."','".($i+1)."','','".$product_cnt[$i+1]."','".$use_yn[$i+1]."',NOW())";
		//수현대리 수정 kbk 12/03/13 //".$display_type[$i+1]."
		$db->sequences = "SHOP_EVENT_GOODS_GROUP_SEQ";
		$db->query($sql);

		if($db->dbms_type == "oracle"){
			$epg_ix = $db->last_insert_id;
		}else{
			$db->query("SELECT epg_ix FROM shop_event_product_group WHERE epg_ix=LAST_INSERT_ID()");
			$db->fetch();
			$epg_ix = $db->dt[epg_ix];
		}

		$db->query("update shop_event_group_display set insert_yn = 'N' where epg_ix = '".$epg_ix."'   ");

		for($j=0;$j < count($display_type[$i+1][type]);$j++){
			$db->query("select egd_ix from shop_event_group_display where egd_ix = '".$display_type[$i+1][egd_ix][$j]."'   ");

			if(!$db->total){
				$sql = "insert into shop_event_group_display (egd_ix,epg_ix, display_type, set_cnt, vieworder, insert_yn, regdate) values ('','".$epg_ix."','".$display_type[$i+1][type][$j]."','".$display_type[$i+1][set_cnt][$j]."','".($j+1)."','Y', NOW())";
				$db->sequences = "SHOP_MAIN_GOODS_LINK_SEQ";
				$db->query($sql);
			}else{
				$sql = "update shop_event_group_display set insert_yn = 'Y',vieworder='".($j+1)."', display_type = '".$display_type[$i+1][type][$j]."',set_cnt = '".$display_type[$i+1][set_cnt][$j]."' 
							where egd_ix = '".$display_type[$i+1][egd_ix][$j]."'  ";
				$db->query($sql);
			}
		}
		$db->query("delete from shop_event_group_display where epg_ix = '".$epg_ix."' and insert_yn = 'N' ");

		for($j=0;$j < count($rpid[$i+1]);$j++){
				$sql = "insert into ".TBL_SHOP_EVENT_PRODUCT_RELATION." (erp_ix,event_ix,group_code, pid, vieworder, insert_yn, regdate) values ('','".$event_ix."','".($i+1)."','".$rpid[$i+1][$j]."','".($i+1)."','Y', NOW())";
				$db->sequences = "SHOP_EVENT_GOODS_LINK_SEQ";
				$db->query($sql);
		}
	}

	if($delete_cache == "Y"){
		include_once($_SERVER["DOCUMENT_ROOT"]."/class/Template_.class.php");
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/_cache/";

		$tpl->clearCache('promotion_list');
	}
	
	$sql = "update ".TBL_SHOP_EVENT." set worker_ix = '' where event_ix= '$event_ix' ";
	$db->query($sql);
	
//	o2oSendPush('E',$event_ix,"I");

	//echo("<script>top.location.href = 'event.list.php';</script>");
	if($agent_type == "M"){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('이벤트 작업이 정상적으로 입력 되었습니다.');parent.location.href='../mShop/event.list.php'</script>");
		//echo("<script>top.location.href = '/admin/mShop/category_main.list.php?mmode=$mmode';</script>");
	}else{
		echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('이벤트 작업이 정상적으로 입력 되었습니다.');parent.location.href='../display/event.list.php'</script>");
		//echo("<script>top.location.href = '/admin/display/category_main.list.php?mmode=$mmode';</script>");
	}
	
}

if ($act == "delete")
{
	if(is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/")){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$event_ix/");
	}
	
//	o2oSendPush('E',$event_ix,"D");

	$db->query("DELETE FROM ".TBL_SHOP_EVENT." WHERE event_ix='$event_ix'");
	echo("<script>top.location.href = 'event.list.php';</script>");
	exit;
}


if ($act == "event_product_delete"){
	$db->query("DELETE FROM shop_event_product_relation WHERE event_ix='".$event_ix."' and erp_ix = '".$erp_ix."' ");
	echo("<script>parent.document.location.reload();</script>");
	exit;
}


if ($act == "initialize")
{
	$sql = "update ".TBL_SHOP_EVENT." set worker_ix = '' where event_ix= '$event_ix' ";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('이벤트 작업이 정상적으로 강제종료 처리 되었습니다.');parent.window.location.reload(true); </script>");
	exit;
}

if ($act == "cate_insert")
{
	if($use_yn != "Y") $use_yn = "N";

	if($db->dbms_type == "oracle"){
		$db->sequences = "SHOP_EVENT_LINK_SEQ";
		$db->query("insert into shop_event_relation(er_ix,agent_type,title,use_yn,regdate) values('','$agent_type','$title','$use_yn',now())");
		$LAST_ID = $db->last_insert_id;
	}else{
		$sql = "INSERT INTO shop_event_relation SET agent_type='$agent_type', title = '$title', use_yn = '$use_yn', regdate = now() ";

		$db->query($sql);
		$db->query("SELECT er_ix FROM shop_event_relation WHERE er_ix=LAST_INSERT_ID()");
		$db->fetch();
		$LAST_ID = $db->dt[er_ix];
	}


	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/cate/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/cate/$LAST_ID/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		//chmod($path,0777)
	}

	if(is_dir($path)){
		if($file_size > 0){
			$fileName = "event_category_".$LAST_ID.".gif";
			move_uploaded_file($file, $path."/".$fileName);
			$db->query("UPDATE shop_event_relation SET file = '$fileName' WHERE er_ix = '$LAST_ID' ");
		}

	}

	//echo("<script>top.location.href = 'event_category.php?mmode=pop';</script>");
	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('이벤트 작업이 정상적으로 수정 되었습니다.');parent.location.reload()</script>");
	exit;
}

if ($act == "cate_update")
{
	if($use_yn != "Y") $use_yn = "N";
	$db->query("UPDATE shop_event_relation SET title = '$title', use_yn = '$use_yn' WHERE er_ix = '$er_ix' ");
	$LAST_ID = $er_ix;

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/cate/";
	if(!is_dir($path)){
		mkdir($path, 0777);
	}

	$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/cate/$LAST_ID/";

	if(!is_dir($path)){
		mkdir($path, 0777);
		//chmod($path,0777)
	}

	if(is_dir($path)){
		if($file_size > 0){
			$fileName = "event_category_".$LAST_ID.".gif";
			move_uploaded_file($file, $path."/".$fileName);
			$db->query("UPDATE shop_event_relation SET file = '$fileName' WHERE er_ix = '$LAST_ID' ");
		}

	}

	//echo("<script>top.location.href = 'event_category.php?mmode=pop';</script>");
	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('이벤트 작업이 정상적으로 수정 되었습니다.');parent.location.reload()</script>");
	exit;
}



if($act=="del_puzzle_img") {
	$sql="SELECT ep_file FROM shop_event_picturepuzzle WHERE event_ix='".$event_ix."' AND ep_ix='".$ep_ix."' ";
	$db->query($sql);
	if($db->total) {
		$db->fetch();
		$ep_file=$db->dt["ep_file"];
		if($banner_ix && is_file($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/".$event_ix."/puzzle/".$ep_file)){
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/en/".$event_ix."/puzzle/".$ep_file);
		}
		$sql="DELETE FROM shop_event_picturepuzzle WHERE event_ix='".$event_ix."' AND ep_ix='".$ep_ix."' ";
		$db->query($sql);

		echo "Y";
	} else {
		echo "N";
	}
}

function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
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

?>
