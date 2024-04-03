<?
include("../../class/database.class");

$db = new Database;



if ($act == "insert"){
	if($margin_caculation_type == 9){
		$margin = "";
	}else if($margin_caculation_type == 1){
		$margin = $margin_plus;
	}else if($margin_caculation_type == 2){
		$margin = $margin_cross;
	}

	if($usable_round == ""){
		$usable_round = "N";
	}
	
	$sql = "insert into goodss_category_setting set 
				cid='".$cid2."',
				goodss_company_id='".$company_id."',
				goodss_cid='".$goodss_cid."',
				margin_caculation_type='".$margin_caculation_type."',
				margin='".$margin."',
				usable_round='".$usable_round."',
				round_precision='".$round_precision."',
				round_type='".$round_type."',
				dupe_process='".$dupe_process."',
				gcs_state='".$gsc_state."',
				gcs_disp='".$gsc_disp."',
				disp='".$disp."',
				regdate=NOW() ";
	//echo $sql;
	$db->query($sql);
	
	//echo  "<script language='javacript'>alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 등록 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>");
}

if ($act == "delete"){
	
	$sql = "delete from goodss_category_setting 	where gcs_ix = '".$gcs_ix."' ";
	//echo $sql;
	$db->query($sql);
	
	//echo  "<script language='javacript'>alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제되었습니다.');parent.document.location.reload();</script>");
}

if ($act == "update"){
	if($margin_caculation_type == 9){
		$margin = "";
	}else if($margin_caculation_type == 1){
		$margin = $margin_plus;
	}else if($margin_caculation_type == 2){
		$margin = $margin_cross;
	}

	if($usable_round == ""){
		$usable_round = "N";
	}

	


	$sql = "update goodss_category_setting set 
				cid='".$cid2."',
				goodss_company_id='".$company_id."',
				goodss_cid='".$goodss_cid."',
				margin_caculation_type='".$margin_caculation_type."',
				margin='".$margin."',
				usable_round='".$usable_round."',
				round_precision='".$round_precision."',
				round_type='".$round_type."',
				dupe_process='".$dupe_process."',
				gcs_state='".$gsc_state."',
				gcs_disp='".$gsc_disp."',
				disp='".$disp."'
				where gcs_ix='".$gcs_ix."' ";
	//echo $sql;
	//exit;
	$db->query($sql);
	
	//echo  "<script language='javacript'>alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
}


?>