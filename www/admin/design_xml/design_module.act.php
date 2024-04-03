<?
include("../class/layout.class");



$db = new Database;


if ($act == "module_update"){
	
	//$page_contents = $content;
	
	$db->query("select page_contents from ".TBL_SHOP_PAGEINFO." where page_name ='module/".$module_type."_templet/".$page_name."' and mall_ix='$mall_ix' ");

	
	
	if($design_backup){		
			$sql = "insert into ".TBL_SHOP_PAGEINFO." (page_ix, mall_ix, page_name, page_change_memo, page_contents, regdate) values ";
			$sql .= " ('', '$mall_ix', 'module/".$module_type."_templet/".$page_name."', '$page_change_memo', '$page_contents', NOW()) ";
			$db->query($sql);
			
			// 입력후 페이지에 쓰기 위해서 다시 불러온다.
			$db->query("select page_contents, page_ix from ".TBL_SHOP_PAGEINFO." where page_name ='module/".$module_type."_templet/".$page_name."' and mall_ix ='$mall_ix' order by regdate desc limit 1");
			$db->fetch();
			$page_contents = $db->dt[page_contents];
		
		
		
	}else{
		$sql = "insert into ".TBL_SHOP_TMP." (mall_ix, design_tmp) values ( '$mall_ix', '$page_contents') ";
		$db->query($sql);
		
		$db->query("select design_tmp as page_contents from ".TBL_SHOP_TMP." where mall_ix ='$mall_ix' ");
		$db->fetch();
		$page_contents = $db->dt[page_contents];
		$db->query("delete from ".TBL_SHOP_TMP." where mall_ix ='$mall_ix' ");
	}
	
	
		$file_path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/module/".$module_type."_templet/".$page_name;	
	
	
	session_register("tab_no");
	if(is_writeable($file_path)){
		$fp = fopen($file_path,"w");
		fwrite($fp,$page_contents);	
		
	
		
		//echo("<script>alert('정상적으로 수정되었습니다.');</script>");
		//echo("<script>parent.document.location.reload();</script>");
		if($design_backup){
			echo "<script>setTimeout('parent.unloading()',100);</script>";
			echo("<script>document.location.href= parent.document.URL+'&mmode=innerview';</script>");				
		}else{
			echo "<script>setTimeout('parent.unloading()',1000);</script>";
		}
	}else{
		
		echo "
				<script>
				setTimeout('parent.unloading()',500);				
				</script>		
				";
		echo("<script>alert('\"$page_name\" 해당파일에 대한 쓰기권한이 없습니다.');</script>");
		//echo("<script>parent.document.location.reload();</script>");
		//chmod($file_path,0666);
		//echo("<script>location.href = './design.php?SubID=SM22464243Sub&templet=$templet&page_name=$page_name';</script>");
	}
}

?>
