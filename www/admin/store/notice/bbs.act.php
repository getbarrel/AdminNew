<?
ini_set("upload_max_filesize", 5000);    // 세션 가비지 컬렉션 : 초(1일) 
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/dir.manage.php");
include("bbs.config.php");
//phpinfo();
session_start();


$db = new Database();

if($act == "insert"){
	$db->query("select IFNULL(max(bbs_ix),0) as bbs_ix from ". $bbs_table_name);
	if($db->total){
		$db->fetch();
		$bbs_ix = $db->dt[bbs_ix] + 1;
	}else{
		$bbs_ix = 0;
	}
	
	if ($bbs_parent_ix == ""){
		$bbs_parent_ix = 0;
	}	
	if ($bbs_ix_level == ""){
		$bbs_ix_level = 0;
	}
	
	
	$sql = "select IFNULL(max(bbs_ix_step )+1,1) as bbs_ix_step from ". $bbs_table_name ." where bbs_top_ix =  ". $bbs_parent_ix;
	$db->query($sql);
	if($db->total){
		$db->fetch();
		$bbs_ix_step = $db->dt[bbs_ix_step];
	}else{
		$bbs_ix_step = 0;
	}
	
	$sql = "select IFNULL(max(bbs_ix_level )+1,0) as bbs_ix_level from ". $bbs_table_name ." where bbs_top_ix = ". $bbs_parent_ix ." and bbs_ix_level = ".$bbs_ix_level;
	$db->query($sql);
	if($db->total){
		$db->fetch();
		$bbs_ix_level = $db->dt[bbs_ix_level];
	}else{
		$bbs_ix_level = 0;
	}

	if(!$is_notice){
		$is_notice = "N";		
	}
	
	
	$sql = "insert into ".$bbs_table_name." (bbs_ix,bbs_div,mem_ix,bbs_subject,bbs_name,bbs_pass,bbs_email,bbs_contents,bbs_top_ix, bbs_ix_level, bbs_ix_step, bbs_hidden, bbs_file_1,bbs_file_2,bbs_file_3, bbs_etc1,bbs_etc2,bbs_etc3,bbs_etc4,bbs_etc5, is_notice, regdate) 
			values 
			('$bbs_ix','$bbs_div','".$user[mem_ix]."','$bbs_subject','$bbs_name','$bbs_pass','$bbs_email','$bbs_contents','$bbs_ix','$bbs_ix_level','$bbs_ix_step','$bbs_hidden','$bbs_file_1_name','$bbs_file_2_name','$bbs_file_3_name','$bbs_etc1','$bbs_etc2','$bbs_etc3','$bbs_etc4','$bbs_etc5','$is_notice',NOW())";
			
//	$sql = "insert into ".$bbs_table_name." (bbs_ix,bbs_div,mem_ix,bbs_subject,bbs_name,bbs_pass,bbs_email,bbs_contents,bbs_hidden, bbs_file_1,bbs_file_2,bbs_file_3, bbs_etc1,bbs_etc2,bbs_etc3,bbs_etc4,bbs_etc5, regdate) 
//			values 
//			('','$bbs_div','".$user[mem_ix]."','$bbs_subject','$bbs_name','$bbs_pass','$bbs_email','$bbs_contents','$bbs_hidden','$bbs_file_1_name','$bbs_file_2_name','$bbs_file_3_name','$bbs_etc1','$bbs_etc2','$bbs_etc3','$bbs_etc4','$bbs_etc5',NOW())";
	
	$db->query($sql);	
	//$db->query("Select bbs_ix from ".$bbs_table_name." where bbs_ix = LAST_INSERT_ID()");	
	$db->query("Select bbs_ix from ".$bbs_table_name." where bbs_ix = '$bbs_ix' ");	
	
	//echo "Select bbs_ix from ".$bbs_table_name." where bbs_ix = $bbs_ix ";
	
	$db->fetch(0);
	$bbs_ix = $db->dt[bbs_ix];
	$path = $bbs_data_dir;
	
	if(!is_dir($path."/".$bbs_table_name)){
		
		if(is_writable($path)){
			mkdir($path."/".$bbs_table_name, 0777);
			chmod($path."/".$bbs_table_name, 0777);	
		}
	}
	
	$path = $bbs_data_dir."/".$bbs_table_name;
	
	
	if(!is_dir($path."/".$bbs_ix)){
		
		if(is_writable($path)){
			
			mkdir($path."/$bbs_ix", 0777);
			chmod($path."/$bbs_ix", 0777);	
		}
	}
	
	//$path = $bbs_data_dir."/".$bbs_table_name."/0012";
	$path = $bbs_data_dir."/".$bbs_table_name."/$bbs_ix";
//	echo $path;
//	exit;
	if(is_dir($path)){
		
		if ($bbs_file_1_size > 0){
			copy($bbs_file_1, $path."/".$bbs_file_1_name);
		}
	
		if ($bbs_file_2_size > 0){
			copy($bbs_file_2, $path."/".$bbs_file_2_name);
		}
		
		if ($bbs_file_3_size > 0){
			copy($bbs_file_3, $path."/".$bbs_file_3_name);
		}
	}

	//exit;
	echo "<Script>document.location.href='bbs_list.php?bbs_div=$bbs_div';</Script>"; 
}


if($act == "update"){
	
	

	$path = $bbs_data_dir."/".$bbs_table_name."/";
	if(!is_dir($path)){
		if(is_writable($bbs_data_dir)){
			mkdir($path, 0777);
			chmod($path, 0777);	
		}
	}
	
	$path = $bbs_data_dir."/".$bbs_table_name."/".$bbs_ix."/";
	if(!is_dir($path)){
		if(is_writable($bbs_data_dir."/".$bbs_table_name)){
			mkdir($path, 0777);
			chmod($path, 0777);	
		}
	}
			
	if ($bbs_file_1_size > 0){
		if($db->dt[bbs_file_1] != ""){
			unlink($path.$db->dt[bbs_file_1]);
		}
		copy($bbs_file_1, $path.$bbs_file_1_name);
		$file_string = ", bbs_file_1 ='".$bbs_file_1_name."' ";
	}

	if ($bbs_file_2_size > 0){
		if($db->dt[bbs_file_2] != ""){
			unlink($path.$db->dt[bbs_file_2]);
		}
		copy($bbs_file_2, $path.$bbs_file_2_name);
		$file_string .= ", bbs_file_2 ='".$bbs_file_2_name."' ";
	}
	
	if ($bbs_file_3_size > 0){
		if($db->dt[bbs_file_3] != ""){
			unlink($path.$db->dt[bbs_file_3]);
		}
		copy($bbs_file_3, $path.$bbs_file_3_name);
		$file_string .= ", bbs_file_3 ='".$bbs_file_3_name."' ";
	}	
	

	if($regdate){
		$regdate_str = ",regdate='$regdate' ";	
	}

	if(!$is_notice){
		$is_notice = "N";		
	}
	

	
	
	$sql ="	update ".$bbs_table_name." set 
			bbs_subject='".trim($bbs_subject)."',bbs_div='$bbs_div',bbs_name='$bbs_name',bbs_pass='$bbs_pass',bbs_email='$bbs_email',bbs_contents='$bbs_contents', bbs_hidden ='$bbs_hidden', 
			bbs_etc1 ='$bbs_etc1', bbs_etc2 ='$bbs_etc2', bbs_etc3 ='$bbs_etc3', bbs_etc4 ='$bbs_etc4', bbs_etc5 ='$bbs_etc5' , is_notice = '$is_notice' $regdate_str $file_string
			where bbs_ix='$bbs_ix' ";
	
	//echo $sql;		
	//exit;
	$db->query($sql);	
	echo "<Script>document.location.href='bbs_read.php?article_no=$article_no&bbs_ix=$bbs_ix&bbs_div=$bbs_div';</Script>";
	
}


if($act == "delete"){
	
	if($user[code] != "" || $admininfo[company_id] != ""){
		
		if($admininfo[company_id] != ""){
			$db->query("Select * from ".$bbs_table_name." where  bbs_ix='$bbs_ix' ");
		}else{
			$db->query("Select * from ".$bbs_table_name." where mem_ix = '".$user[code]."' and bbs_ix='$bbs_ix' ");
		}
		
		if($db->total){
			$path = $bbs_data_dir."/".$bbs_table_name."/".$bbs_ix."/";
			
			if(is_dir($path)){
				rmdirr($path);
			}
			
			if($admininfo[company_id] != ""){
				$sql ="	delete from ".$bbs_table_name." where bbs_ix='$bbs_ix' ";	
			}else{
				$sql ="	delete from ".$bbs_table_name." where bbs_ix='$bbs_ix'  and mem_ix = '".$user[code]."' ";		
			}
		
			
			$db->query($sql);
			
			$sql ="delete from ".$bbs_table_name."_comment where bbs_ix='$bbs_ix'";
			$db->query($sql);
			
			echo "<Script>document.location.href='bbs_list.php?bbs_div=$bbs_div';</Script>";
		}else{
			echo "<Script>alert('해당긁은 회원님의 글이 아닙니다. 확인후 다시 삭제해 주시기 바랍니다.');history.back();</Script>";
		}
	}else{
		$db->query("Select * from ".$bbs_table_name." where bbs_pass = '$bbs_pass' and bbs_ix='$bbs_ix' ");
		
		if($db->total){
			$path = $bbs_data_dir."/".$bbs_table_name."/".$bbs_ix."/";
			
			if(is_dir($path)){
				rmdirr($path);
			}
			
			$sql ="	delete from ".$bbs_table_name." where bbs_ix='$bbs_ix'  ";		
			$db->query($sql);
			
			$sql ="delete from ".$bbs_table_name."_comment where bbs_ix='$bbs_ix'";
			$db->query($sql);
			
			echo "<Script>document.location.href='bbs_list.php?bbs_div=$bbs_div';</Script>";
		}else{
			echo "<Script>alert('비밀번호가 올바르지 않습니다.');history.back();</Script>";
		}
	}
	
}


if($act == "comment_insert"){
	$sql = "insert into ".$bbs_table_name."_comment
			(cmt_ix,bbs_ix,mem_ix,cmt_name,cmt_pass,cmt_email,cmt_contents,regdate) 
			values
			('','$bbs_ix','$mem_ix','$cmt_name','$cmt_pass','$cmt_email','$cmt_contents',NOW())";
	
	//echo $sql;		
	$db->query($sql);	
	
	$db->query("update ".$bbs_table_name." set bbs_re_cnt = bbs_re_cnt + 1 where bbs_ix ='$bbs_ix'");
	echo "<Script>document.location.href='bbs_read.php?article_no=$article_no&bbs_ix=$bbs_ix&bbs_div=$bbs_div';</Script>";
}



if($act == "response"){
	
	$db->query("select IFNULL(max(bbs_ix),0) as bbs_ix from ". $bbs_table_name);
	if($db->total){
		$db->fetch();
		$bbs_ix = $bbs_ix + 1;
	}else{
		$bbs_ix = 0;
	}
	
	if ($bbs_parent_ix == ""){
		$bbs_parent_ix = 0;
	}	
	if ($bbs_ix_level == ""){
		$bbs_ix_level = 0;
	}
	
	
	$sql = "select IFNULL(max(bbs_ix_step )+1,0) as bbs_ix_step from ". $bbs_table_name ." where bbs_top_ix =  ". $bbs_top_ix;
	$db->query($sql);
	if($db->total){
		$db->fetch();
		$bbs_ix_step = $db->dt[bbs_ix_step];
	}else{
		$bbs_ix_step = 0;
	}
	
	$sql = "select IFNULL(max(bbs_ix_level )+1,0) as bbs_ix_level from ". $bbs_table_name ." where bbs_top_ix = ". $bbs_parent_ix ." and bbs_ix_level = ".$bbs_ix_level;
	$db->query($sql);
	if($db->total){
		$db->fetch();
		$bbs_ix_level = $db->dt[bbs_ix_level];
	}else{
		$bbs_ix_level = 0;
	}

	
	
	$sql = "insert into ".$bbs_table_name." (bbs_ix,bbs_div,mem_ix,bbs_subject,bbs_name,bbs_pass,bbs_email,bbs_contents,bbs_top_ix, bbs_ix_level, bbs_ix_step, bbs_hidden, bbs_file_1,bbs_file_2,bbs_file_3, bbs_etc1,bbs_etc2,bbs_etc3,bbs_etc4,bbs_etc5, regdate) 
			values 
			('$bbs_ix','$bbs_div','".$user[mem_ix]."','$bbs_subject','$bbs_name','$bbs_pass','$bbs_email','$bbs_contents','$bbs_parent_ix','$bbs_ix_level','$bbs_ix_step','$bbs_hidden','$bbs_file_1_name','$bbs_file_2_name','$bbs_file_3_name','$bbs_etc1','$bbs_etc2','$bbs_etc3','$bbs_etc4','$bbs_etc5',NOW())";
	
	$db->query($sql);	
	$db->query("Select bbs_ix from ".$bbs_table_name." where bbs_ix = LAST_INSERT_ID()");	
	$db->fetch();
	
	$path = $bbs_data_dir;
	
	if(!is_dir($path."/".$bbs_table_name)){
		
		if(is_writable($path)){
			mkdir($path."/".$bbs_table_name, 0777);
			chmod($path."/".$bbs_table_name, 0777);	
		}
	}
	
	$path = $bbs_data_dir."/".$bbs_table_name;
	if(!is_dir($path."/".$bbs_ix)){
		
		if(is_writable($path)){
			//echo $path."/".$bbs_ix;
			mkdir($path."/".$bbs_ix, 0777);
			chmod($path."/".$bbs_ix, 0777);	
		}
	}
	
	//$path = $bbs_data_dir."/".$bbs_table_name."/0012";
	$path = $bbs_data_dir."/".$bbs_table_name."/".$bbs_ix;
	//echo $path;
	if(is_dir($path)){
		
		if ($bbs_file_1_size > 0){
			copy($bbs_file_1, $path."/".$bbs_file_1_name);
		}
	
		if ($bbs_file_2_size > 0){
			copy($bbs_file_2, $path."/".$bbs_file_2_name);
		}
		
		if ($bbs_file_3_size > 0){
			copy($bbs_file_3, $path."/".$bbs_file_3_name);
		}
	}

	//exit;
	echo "<Script>document.location.href='bbs_list.php?bbs_div=$bbs_div';</Script>"; 
}



if($act == "comment_delete"){
	$sql ="select * from ".$bbs_table_name."_comment where cmt_ix = '$cmt_ix' and bbs_ix='$bbs_ix' and cmt_pass ='$cmt_pass' ";
	//echo $sql;
	$db->query($sql);
	
	if($db->total){
		$sql ="	delete from ".$bbs_table_name."_comment where cmt_ix = '$cmt_ix' and bbs_ix='$bbs_ix' ";
	//	echo $sql;
		$db->query($sql);
		$db->query("update ".$bbs_table_name." set bbs_re_cnt = bbs_re_cnt - 1 where bbs_ix ='$bbs_ix'");
		
		echo "<Script>document.location.href='bbs_read.php?article_no=$article_no&bbs_ix=$bbs_ix&bbs_div=$bbs_div';</Script>";
	}else{
		echo "<Script>alert('비밀번호가 올바르지 않습니다.');history.back();</Script>";
	}
	
	
	
}


if($act == "pass_check"){
	if($user[code] != ""){
		$db->query("Select * from ".$bbs_table_name." where mem_ix = '".$user[code]."' and bbs_ix='$bbs_ix' ");
	}else{
		$sql ="select * from ".$bbs_table_name." where bbs_ix='$bbs_ix' ";
	//	echo $sql;
		$db->query($sql);
		$db->fetch();
		$bbs_pass = $db->dt[bbs_pass];
	}
	
	if($db->total){
		echo "<Script>document.location.href='bbs_modify.php?bbs_ix=$bbs_ix&article_no=$article_no&page=$page&bbs_div=$bbs_div&bbs_pass=$bbs_pass';</Script>";	
	}else{
		echo "<Script>alert('비밀번호가 올바르지 않습니다.');history.back();</Script>";
	}
		
}

if($act == "faq_insert"){
	
	$sql = "insert into ".$bbs_table_name." 
			(bbs_ix,bbs_div,bbs_q,bbs_a,bbs_contents_type,regdate) 
			values
			('','$bbs_div','$bbs_q','$bbs_a','$bbs_contents_type',NOW())";
	
//	echo $sql;		
	$db->query($sql);	
	echo "<Script>document.location.href='bbs_list.php';</Script>";
}


if($act == "faq_update"){
	
	$sql ="	update ".$bbs_table_name." set 
			bbs_div='$bbs_div',bbs_q='$bbs_q',bbs_a='$bbs_a',bbs_contents_type='$bbs_contents_type'
			where bbs_ix='$bbs_ix'";
	
	//echo $sql;		
	$db->query($sql);	

		
	echo "<Script>document.location.href='bbs_list.php';</Script>";
	
}

if($act == "faq_delete"){
	
	$sql ="delete from ".$bbs_table_name." where bbs_ix='$bbs_ix'  ";		
	$db->query($sql);
	
	echo "<Script>document.location.href='bbs_list.php';</Script>";
	
}



?>