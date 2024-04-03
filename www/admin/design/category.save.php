<?

// 	print_r($_POST);
// 	exit;
	include("../class/layout.class");
	
	include('./design.common.php');
	include "../class/LayoutXml/LayoutXml.class";

	$layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/_layout/";

	switch ($_SESSION["admin_config"]["mall_page_type"]){
		case "P":
			$layoutXmlPath .= $admin_config["selected_templete"] . ".xml";
			break;
		case "MI":
			$layoutXmlPath .= $admin_config["selected_templete_minishop"] . ".xml";
			break;
		case "M":
			//echo($admin_config["mall_page_type"]);
			//echo("<br />");
			$layoutXmlPath .= $admin_config["selected_templete_mobile"] . ".xml";
			break;
	}
	//echo $layoutXmlPath;
	$layoutXml = new LayoutXml($layoutXmlPath);

	////////////////// 테이블을 xml로 바꾼다. xml 기반으로 돌아가기 시작하면 이 자체가 필요없을듯..
	// updateCategoryXML();
	
	// $db = new MySQL;
	if($mode == "infoupdate"){
	// 	if($admin_config[mall_page_type] == "MI"){
	// 		$sql = "SELECT * FROM ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where cid = '$cid' ";
	// 	}else{
	// 		$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where cid = '$cid'";
	// 	}
		$resultFindByPcode = $layoutXml->search("layouts", array("cid"), array($cid));
		
	// 	$db->query($sql);
		//echo $sql;
		if(count($resultFindByPcode)){
			if($resultFindByPcode[0]->category_use == 1){
				$category_use = "true";
			}else{
				$category_use = "false";
			}
			
			if($resultFindByPcode[0]->is_layout_apply == 'Y'){
				$is_layout_apply = "true";
			}else{
				$is_layout_apply = "false";
			}

			$url = explode("/",$resultFindByPcode[0]->basic_link);
            $fileName = end($url);
            if(empty($fileName)){
                $fileName = 'index';
			}
			
			$mstring ="
			<html>
			<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
			<body>
			<div id='category_top_view_area'>
			".$resultFindByPcode[0]->category_top_view."
			</div>
			</body>
			</html>
			<script>
			var frm = parent.document.forms['thisCategoryform'];
			parent.document.forms['thisCategoryform'].category_use.checked = $category_use;
			parent.document.forms['thisCategoryform'].is_layout_apply.checked = $is_layout_apply;
			parent.document.forms['thisCategoryform'].path.value = '".$resultFindByPcode[0]->path."';
			parent.document.forms['thisCategoryform'].basic_link.value = '".$resultFindByPcode[0]->basic_link."';
			parent.document.getElementById('down').innerHTML = '<a href=\"download.php?path=".$fileName."&cid=$cid\">파일다운로드</a>';
			
			
			frm.bbs_name[0].selected = true;
			for(i=0;i<frm.bbs_name.length;i++){
			if(frm.bbs_name[i].value == '".$db->dt[bbs_name]."'){
			frm.bbs_name[i].selected = true;
			}
			}
			//parent.iView.document.body.innerHTML = document.getElementById('category_top_view_area').innerHTML;
			</script>";
			
 			echo $mstring;
		}
	}
	
	
	if ($mode == "modify"){
		if(!$is_layout_apply){
			$is_layout_apply = "N";
		}
		
		//path 정보 변경전 경로 가져오기
		$page_path = getDesignTempletPath($cid, $this_depth);	
		if($admin_config[mall_page_type] == "P"){		
			$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete]."/".trim($page_path);			
		}else if($admin_config[mall_page_type] == "M"){
			$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/mobile_templet/".$admin_config[selected_templete]."/".trim($page_path);
		}else if($admin_config[mall_page_type] == "MI"){
			$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/minishop_templet/".$admin_config[selected_templete]."/".trim($page_path);
		}
		
		$results = $layoutXml->search("layouts", array("cid"), array($cid));
	
		foreach ($results as $result) {
				$layoutXml->layouts[$result->layout_index]->cname 			= $this_category;
				$layoutXml->layouts[$result->layout_index]->path	 		= $path;
				$layoutXml->layouts[$result->layout_index]->basic_link 		= $basic_link;
				
				$layoutXml->layouts[$result->layout_index]->category_top_view = $category_top_view;
				$layoutXml->layouts[$result->layout_index]->category_use 	= $category_use;
				$layoutXml->layouts[$result->layout_index]->category_display_type 		= $category_display_type;
				$layoutXml->layouts[$result->layout_index]->is_layout_apply 		= $is_layout_apply;
				$layoutXml->layouts[$result->layout_index]->bbs_name 		= $bbs_name;
				
				if ($category_img_size > 0){
					copy($category_img, "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/category/".trim($category_img_name));
					$layoutXml->layouts[$result->layout_index]->catimg		= $category_img_name; 				
				}
				
				if ($leftcategory_img_size > 0){
					copy($leftcategory_img, "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/category/".trim($leftcategory_img_name));
					$layoutXml->layouts[$result->layout_index]->leftcatimg		= $leftcategory_img_name;
				}
				
		}
		
		$layoutXml->SaveXml($layoutXmlPath);
		
		$change_page_path = getDesignTempletPath($cid, $this_depth);	
		
		if($admin_config[mall_page_type] == "P"){		
			//$file_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete]."/".($mod != "" ? $mod:$page_path)."/".$page_name;		
			$change_dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete]."/$change_page_path";
		}else if($admin_config[mall_page_type] == "M"){
			//$file_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/mobile_templet/".$admin_config[selected_templete]."/".($mod != "" ? $mod:$page_path)."/".$page_name;	
			$change_dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/mobile_templet/".$admin_config[selected_templete]."/$change_page_path";
		}else if($admin_config[mall_page_type] == "MI"){
			//$file_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/minishop_templet/".$admin_config[selected_templete]."/".($mod != "" ? $mod:$page_path)."/".$page_name;	
			$change_dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/minishop_templet/".$admin_config[selected_templete]."/$change_page_path";
		}

		if(is_dir($dir_path)){		
			rename($dir_path, $change_dir_path);
		}else{	
				mkdir($dir_path, 0777);
				chmod($dir_path,0777);	
		}
		
	// 	updateLayoutXML($admininfo[mall_ix]);
		echo "<Script Language='JavaScript'>parent.document.location.href='category.php?cid=$cid';</Script>";	
		//Header("Location: category.php");
		
	}
	
	if ($mode == "del"){
		//echo $cid."::::".$this_depth."<br>";
		//echo "CheckSubCategory".CheckSubCategory($cid,$this_depth);
		//exit;
		if (CheckSubCategory($cid,$this_depth)){
			if($sub_cartegory_delete == "1"){
				$page_path = getDesignTempletPath($cid, $this_depth);	
				//echo $page_path ;
				//exit;
				//$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete]."/".trim($page_path);
				if($admin_config[mall_page_type] == "P"){		
					$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete]."/".trim($page_path);			
				}else if($admin_config[mall_page_type] == "M"){
					$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/mobile_templet/".$admin_config[selected_templete]."/".trim($page_path);
				}else if($admin_config[mall_page_type] == "MI"){
					$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/minishop_templet/".$admin_config[selected_templete]."/".trim($page_path);
				}
	
				//echo $cid.":::".$this_depth."<br>";
				//echo $dir_path ." != ".$DOCUMENT_ROOT.$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete];
				//exit;
				if(is_dir($dir_path) && $admin_config[selected_templete] && trim($page_path)){		
					deltree($dir_path);
				}
				//echo substr($cid,0,($this_depth+1)*3);
				//echo $dir_path;
				//echo "/layouts/layout[substring(@cid,1," . ($this_depth+1)*3  . ") = '" . substr($cid,0,($this_depth+1)*3) . "']";
				//$layouts = $layoutXml->simpleXml->xpath("/layouts/layout[substring(@cid,1," . ($this_depth+1)*3  . ") = '" . substr($cid,0,($this_depth+1)*3) . "']");
 				//print_r($layouts);
				//exit;
// 				echo("확인중!!!!!!!!!!!!!!!!!!!!!!!!");
				$layoutToUnsetIndex = -1;
				foreach($layoutXml->layouts as $layout){
					if (substr($layout->cid,0,($this_depth+1)*3) == substr($cid,0,($this_depth+1)*3)) {
						$layoutToUnsetIndex = $layout->layout_index;
						unset($layoutXml->layouts[$layoutToUnsetIndex]);
					}
				}
				$layoutXml->SaveXml($layoutXmlPath);
				
				//echo $layoutToUnsetIndex;
				//exit;
				
	// 			if($admin_config[mall_page_type] == "MI"){
	// 				$sql = "delete from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
	// 			}else{
	// 				$sql = "delete from ".TBL_SHOP_LAYOUT_INFO." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
	// 			}
	// 			$db->query($sql);
				//2011.09.04 관계없는 쿼리인거 같아서 주석처리
				//$db->query("delete from ".TBL_SHOP_PRODUCT_RELATION." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ");	
				
	// 			updateLayoutXML($admininfo[mall_ix]);
				echo "<Script Language='JavaScript'>alert(\"삭제 되었습니다.\");</Script>";	
				echo "<Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";	
			}else{
				echo "<Script Language='JavaScript'>alert('하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요');</Script>";	
			}
		}else{
			
// 			echo("begin design");
			$page_path = getDesignTempletPath($cid, $this_depth);
// 			echo("end design");
// 			$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete]."/".trim($page_path);
// 			exit;
			
			if($admin_config[mall_page_type] == "P"){		
				$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete]."/".trim($page_path);			
			}else if($admin_config[mall_page_type] == "M"){
				$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/mobile_templet/".$admin_config[selected_templete]."/".trim($page_path);
			}else if($admin_config[mall_page_type] == "MI"){
				$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/minishop_templet/".$admin_config[selected_templete]."/".trim($page_path);
			}
			
			
			if($page_path != "/" && $page_path != ""){
				
				if(is_dir($dir_path) && $admin_config[selected_templete]){		
					//echo "안돼";
					//exit;
					deltree($dir_path);
				}
			}
			//echo $dir_path;
			//exit;
			
			if($admin_config[mall_page_type] == "MI"){
				$layoutToUnsetIndex = -1;
				foreach($layoutXml->layouts as $layout){
					if ($layout->cid == $cid) {
						$layoutToUnsetIndex = $layout->layout_index;
					}
				}
				if($layoutToUnsetIndex != -1)
				{
					unset($layoutXml->layouts[$layoutToUnsetIndex]);
					$layoutXml->SaveXml($layoutXmlPath);
					echo("unset : " . $layoutToUnsetIndex);
				}
// 				$sql = "delete from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where cid = '$cid'";
			}else{
				$layoutToUnsetIndex = -1;
				foreach($layoutXml->layouts as $layout){
					if ($layout->cid == $cid) {
						$layoutToUnsetIndex = $layout->layout_index;
					}
				}
				if($layoutToUnsetIndex != -1)
				{
// 					echo("before count : " . count($layoutXml->layouts));
					unset($layoutXml->layouts[$layoutToUnsetIndex]);
					print_r($layoutXml->layouts);
					$layoutXml->SaveXml($layoutXmlPath);
// 					echo("after count : " . count($layoutXml->layouts));
// 					echo("unset : " . $layoutToUnsetIndex);
				}
// 				exit;
// 				$layouts = $layoutXml->simpleXml->xpath("/layouts/layout[@cid='" . $cid . "']");
// 				print_r($layouts);
				
// 				$sql = "delete from ".TBL_SHOP_LAYOUT_INFO." where cid = '$cid'";
			}
			
// 			$db->query($sql);
// 			echo("ccccccccccid : " . $cid);
// 			print_r($layouts[0]);
// 			exit;
// 			echo("iiiiiiiiiiiiiiiindex : " . $layouts[0]->layout_index);
			
//			unset($layoutXml->layouts[$layouts[0]->layout_index]);
// 			$layoutXml->SaveXml($layoutXmlPath);
			//2011.09.04 관계없는 쿼리인거 같아서 주석처리
			//$db->query("delete from ".TBL_SHOP_PRODUCT_RELATION." where cid ='$cid' ");	
				
// 			updateLayoutXML($admininfo[mall_ix]);
			echo "<Script Language='JavaScript'>alert('삭제되었습니다.');</Script>";	
			echo "<Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";	
		}
		
	//	Header("Location: category.php");	
	}
	
	if ($mode == "insert"){	
		
		$sPos = $sub_depth*3 + 1;
		/*
		$sPos = $sub_depth*3 + 1;
		if($admin_config[mall_page_type] == "MI"){
			$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where cid LIKE '".substr($cid,0,$sPos-1)."%' and cname = '".$sub_category."' and depth = '".$sub_depth."' ";
		}else{
			$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where cid LIKE '".substr($cid,0,$sPos-1)."%' and cname = '".$sub_category."' and depth = '".$sub_depth."' ";
		}
		//exit;
		$db->query($sql);
		$db->fetch(0);
		if($db->total){		
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert(' 이미 등록된 카테고리명 입니다. ');</script>";
		}
		*/
	
// 		$results = $layoutXml->search("layouts", array("cid", "skin_type"), array($cid, $admin_config[mall_page_type]));
		$xpathString = sprintf("/layouts/layout[substring(@cid, 1, %s)='%s' and cname='%s' and depth='%s']"
							  , ($sPos - 1), substr($cid, 0 ,$sPos - 1), $sub_category, $sub_depth);

// 		echo("\n");
		$results = $layoutXml->simpleXml->xpath($xpathString);
		if(count($results) != 0){
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert(' 이미 등록된 카테고리명 입니다. ');</script>";
		}
		
// 		print_r($results);
// 		exit;
	// 	if($admin_config[mall_page_type] == "MI"){
	// 		$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where cid = '$cid' ";
	// 	}else{
	// 		$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where cid = '$cid' ";
	// 	}
	// 	$db->query($sql);
	// 	$db->fetch(0);
	
		$results = $layoutXml->search("layouts", array("cid", "skin_type"), array($cid, $admin_config[mall_page_type]));
// 		print_r(array($cid, $admin_config[mall_page_type]));
// 		echo("\n");
// 		echo("xxxxxxxxxxxxxxxxxxxxxxxxxx");
//  		print_r($results);
//  		exit;
 		
 		
 		//////////////// 도대체 아래 코드는 왜 존재하는것일까
		$level1 = $results[0]->vlevel1;
		$level2 = $results[0]->vlevel2;
		$level3 = $results[0]->vlevel3;
		$level4 = $results[0]->vlevel4;
		$level5 = $results[0]->vlevel5;
		
// 		print_r($results[0]);
		
	// 	$level1 = $db->dt["vlevel1"];
	// 	$level2 = $db->dt["vlevel2"];
	// 	$level3 = $db->dt["vlevel3"];
	// 	$level4 = $db->dt["vlevel4"];
	// 	$level5 = $db->dt["vlevel5"];
// 		echo($sub_depth . "  sss   " . $cid . "  sss " . $sub_depth);
// 		exit;

		
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
		
		if ($category_img_size > 0){
			copy($category_img, "../../image/category/".trim($category_img_name));
		}
		
		if ($leftcategory_img_size > 0){		
			copy($leftcategory_img, "../../image/category/".trim($leftcategory_img_name));		
		}
		
// 		exit;
	// 	if($admin_config[mall_page_type] == "MI"){
	// 			$sql = "insert into ".TBL_SHOP_MINISHOP_LAYOUT_INFO." (cid, depth,vlevel1, vlevel2, vlevel3,vlevel4,vlevel5,cname,path, catimg,leftcatimg, category_display_type, bbs_name, category_use,regdate) 
	// 			values 
	// 			('$sub_cid', '$sub_depth','$level1','$level2','$level3','$level4','$level5', '$sub_category','$path','$category_img_name','$leftcategory_img_name','$category_display_type','$bbs_name','$category_use',NOW());";
	// 	}else{
	// 		// layout2.xml에 아래 내용 추가
	// 			$sql = "insert into ".TBL_SHOP_LAYOUT_INFO." (cid, depth,vlevel1, vlevel2, vlevel3,vlevel4,vlevel5,cname,path, catimg,leftcatimg, category_display_type, bbs_name, category_use,regdate) 
	// 			values 
	// 			('$sub_cid', '$sub_depth','$level1','$level2','$level3','$level4','$level5', '$sub_category','$path','$category_img_name','$leftcategory_img_name','$category_display_type','$bbs_name','$category_use',NOW());";
	// 	}
	// 	$db->query($sql);
		
		$count = count($layoutXml->layouts);
		
		$layoutXml->layouts[$count]->layout_index = $count;
		$layoutXml->layouts[$count]->mall_ix = $_SESSION["admininfo"]["mall_ix"];
		
// 		echo("ggggggggggggggggggggggggggg");
// 		echo("<br />");
// 		echo($_SESSION["admininfo"]["mall_ix"]);
// 		print_r($_SESSION);
// 		exit;
	
		$layoutXml->layouts[$count]->templet_name = $_SESSION["admin_config"]["selected_templete"];
		$layoutXml->layouts[$count]->skin_type = $results[0]->skin_type;
		
		$layoutXml->layouts[$count]->basic_link = "";
		$layoutXml->layouts[$count]->depth = $sub_depth;
		$layoutXml->layouts[$count]->vlevel1 = $level1;
		$layoutXml->layouts[$count]->vlevel2 = $level2;
		$layoutXml->layouts[$count]->vlevel3 = $level3;
		$layoutXml->layouts[$count]->vlevel4 = $level4;
		$layoutXml->layouts[$count]->vlevel5 = $level5;
		$layoutXml->layouts[$count]->vlevelf = sprintf("%03d%03d%03d%03d%03d"
	                                                        ,$level1
	                                                        ,$level2
	                                                        ,$level3
	                                                        ,$level4
	                                                        ,$level5
	                                                   );
		
// 		echo("aaa" . $sub_cid);
// 		exit;
		
		$layoutXml->layouts[$count]->pcode = $sub_cid;//$layoutXml->layouts[$count]->vlevelf;
// 		$layoutXml->layouts[$count]->cid = $layoutXml->layouts[$count]->vlevelf;
		$layoutXml->layouts[$count]->cid = $sub_cid;
		//echo($sub_cid);
		//exit;
// 		echo("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
// 		print_r($layoutXml->layouts[$count]);
// 		exit;
		$page_path = getDesignTempletPath($sub_cid, $sub_depth);	
		if(!$page_path){
			$page_path = $path;
		}else{
			$page_path .= "/".$path; 
		}
		
		$layoutXml->layouts[$count]->cname = $sub_category;
		$layoutXml->layouts[$count]->path = $path;
		$layoutXml->layouts[$count]->layout = "";
		$layoutXml->layouts[$count]->page_navi = "";
		$layoutXml->layouts[$count]->header1 = "";
		$layoutXml->layouts[$count]->header2 = "";
		$layoutXml->layouts[$count]->leftmenu = "";
		$layoutXml->layouts[$count]->contents = "";
		$layoutXml->layouts[$count]->contents_add = "";
		$layoutXml->layouts[$count]->rightmenu = "";
		$layoutXml->layouts[$count]->footer1 = "";
		$layoutXml->layouts[$count]->footer2 = "";
		
		$layoutXml->layouts[$count]->catimg = $category_img_name;
		$layoutXml->layouts[$count]->leftcatimg = $leftcategory_img_name;
		$layoutXml->layouts[$count]->subimg = "";
		$layoutXml->layouts[$count]->category_top_view = "";
		$layoutXml->layouts[$count]->category_display_type = $category_display_type;
		$layoutXml->layouts[$count]->bbs_name = $bbs_name;
		$layoutXml->layouts[$count]->category_use = $category_use;
		$layoutXml->layouts[$count]->is_layout_apply = "";
		
		$layoutXml->layouts[$count]->page_type = "";
		$layoutXml->layouts[$count]->page_path = $page_path;
		$layoutXml->layouts[$count]->page_link = "";
		$layoutXml->layouts[$count]->page_title = "";
		$layoutXml->layouts[$count]->page_help = "";
		$layoutXml->layouts[$count]->page_addscript = "";
		$layoutXml->layouts[$count]->page_body = "";
		$layoutXml->layouts[$count]->page_desc = "";
		$layoutXml->layouts[$count]->caching = "";
		$layoutXml->layouts[$count]->caching_time = "";
		$layoutXml->layouts[$count]->regedit = date("Y-m-d H:i:s",time());
		$layoutXml->SaveXml($layoutXmlPath);
		
	
		//$page_path = getDesignTempletPath($sub_cid, $sub_depth);	
		
		if($admin_config[mall_page_type] == "P"){		
			$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete]."/".trim($page_path);			
		}else if($admin_config[mall_page_type] == "M"){
			$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/mobile_templet/".$admin_config[selected_templete]."/".trim($page_path);
		}else if($admin_config[mall_page_type] == "MI"){
			$dir_path = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/minishop_templet/".$admin_config[selected_templete]."/".trim($page_path);
		}
		//echo $admin_config[mall_page_type]." ".$dir_path;
		//exit;
		$file_path = $dir_path."/".$path.".htm";
		
		
		echo $page_path;
		echo "<br />";
 		echo $dir_path;
		echo "<br />";
 		echo $file_path;
//  		exit;
		
		
		if(!is_dir($dir_path)){		
			mkdir($dir_path, 0777);
			chmod($dir_path,0777);	
		}
		
		if($category_display_type == "P" || $category_display_type == "B"){
			if(!(is_file($file_path))){
				$fp = fopen("$file_path","w");
				
				if($category_display_type == "B"){
					if($bbs_name){					
						fwrite($fp,"{=print_bbs(\"$bbs_name\",_GET[\"mode\"], _GET[\"act\"],\"$sub_cid\") // 게시판을 불러오는 치환함수} ");	
					}else{
						fwrite($fp,"{=print_bbs(_GET[\"board\"],_GET[\"mode\"], _GET[\"act\"],\"$sub_cid\")}<!--{*print_bbs(게시판코드, 게시판모드(list, write, modify, response ...), 게시판 액션(insert, update, delete ...), 페이지 코드)*}-->");	
					}
					//fwrite($fp,"test");	
				}else{
					fwrite($fp,"$sub_category");	
				}
				fclose($fp);
				chmod($file_path,0777);
				//echo "<script>alert('파일이 정상적으로 생성 되었습니다.');opener.document.location.reload();self.close();</script>";	
			}
			
			
			//$db->query("select * from ".TBL_SHOP_DESIGN." where pcode='$sub_cid' and mall_ix='".$admininfo[mall_ix]."' and templet_name = '".$admin_config[selected_templete]."' ");
			
			$layoutXml = simplexml_load_file($layoutXmlPath);
			
	// 		pcode <- 있음
	// 		mall_ix <- 없음.
	// 		templet_name <- 이건없음.
			
			$layouts = $layoutXml->xpath("//layout[@pcode='" . $sub_cid . "']");
			
			if(count($layouts) == 0){
				$layoutsBasic = $layoutXml->xpath("//layout[@pcode='basic']"); // 반드시 있다고 가정
				foreach($layoutsBasic as $layoutBasic){
					$layout = $layoutXml->addChild("layout");
					$layout->addAttribute("pcode", $layoutBasic["pcode"]);
					$layout->addAttribute("basic_link", $layoutBasic["basic_link"]);
					$layout->addAttribute("depth", $layoutBasic["depth"]);
					$layout->addChild("page_navi", $layoutBasic["page_navi"]);
					$layout->addChild("header1", $layoutBasic["header1"]);
					$layout->addChild("header2", $layoutBasic["header2"]);
					$layout->addChild("leftmenu", $layoutBasic["leftmenu"]);
					$layout->addChild("contents", $layoutBasic["contents"]);
					$layout->addChild("contents_add", $layoutBasic["contents_add"]);
					$layout->addChild("rightmenu", $layoutBasic["rightmenu"]);
					$layout->addChild("footer1", $layoutBasic["footer1"]);
					$layout->addChild("footer2", $layoutBasic["footer2"]);
					$layout->addChild("page_path", $layoutBasic["page_path"]);
					$layout->addChild("caching", $layoutBasic["caching"]);
					$layout->addChild("caching_time", $layoutBasic["caching_time"]);
				}
	
				$layoutXml->asXML($layoutXmlPath);
			}
		}
	
	// 	updateLayoutXML($admininfo[mall_ix]);
	echo "<Script Language='JavaScript'>parent.document.location.href='category.php?cid=$cid';</Script>";	
	//	Header("Location: category.php");	
	
		
	}
	
	function deltree($f) {
	  if (is_dir($f)) {
	    foreach(glob($f.'/*') as $sf) {
	      if (is_dir($sf) && !is_link($sf)) {
	        deltree($sf);
	      } else {
	        unlink($sf);
	      }  
	    }  
	  }
	  rmdir($f);
	}
	
	
	function getMaxlevel($cid,$depth)
	{
		global $db, $admin_config, $layoutXml;
			
		$strdepth = $depth + 1;
		
		$sPos = $depth*3;
// 		if($admin_config[mall_page_type] == "MI"){
// 			$sql = "select max(vlevel$strdepth)+1 as maxlevel from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where cid LIKE '".substr($cid,0,$sPos)."%' ";
// 		}else{
// 			$sql = "select max(vlevel$strdepth)+1 as maxlevel from ".TBL_SHOP_LAYOUT_INFO." where cid LIKE '".substr($cid,0,$sPos)."%'";
// 		}
		//////////////////////////////////// 꼭 확인해봐야할것 /////////////////////////
// 		echo("꼭확인해봐야할부분");
// 		echo("<br />");
// 		print_r($layoutXml);
// 		print_r($layoutXml->simpleXml);
// 		exit;

// 		echo("/layouts/layout[substring(@cid,1," . $sPos . ") = '" . substr($cid,0,$sPos) . "']");

// 		$layouts = $layoutXml->simpleXml->xpath("/layouts/layout[substring(@cid,1," . $sPos . ") = '" . substr($cid,0,$sPos) . "' and skin_type='P']");
		$layouts = $layoutXml->simpleXml->xpath("/layouts/layout[substring(@cid,1," . $sPos . ") = '" . substr($cid,0,$sPos) . "']");
		
		//찾으려는 값으로만 이루어진 array를 만든다.
		
		$vlevels = array();
		$i = 0;
		
// 		$vlevel = "vlevel" . $strdepth;
// 		print_r($layouts);
// 		exit;
// 		echo("strdepth : " . $strdepth . "\n");
// 		exit;
		foreach ($layouts as $layout) {
			switch ($strdepth) {
				case 1:
					$vlevels[$i] = (int) $layout->attributes()->vlevel1;
					break;
				case 2:
					$vlevels[$i] = (int) $layout->attributes()->vlevel2;
// 					echo($vlevels[$i]);
// 					echo("<br />");
					break;
				case 3:
					$vlevels[$i] = (int) $layout->attributes()->vlevel3;
					break;
				case 4:
					$vlevels[$i] = (int) $layout->attributes()->vlevel4;
					break;
				case 5:
					$vlevels[$i] = (int) $layout->attributes()->vlevel5;
					break;
				default:
					break;
			}
// 			$vlevels[$i] = $layout->$$vlevel;
			$i++;
		}
// 		exit;
		$maxvalue = max($vlevels);
		
//  		print_r($vlevels);
//  		echo("<br />");
//  		echo($maxvalue);
		
// 		$db->query($sql);
// 		$db->fetch(0);
	
		
	//	echo $sql."<br>";
	//	echo $db->dt["maxlevel"]."<br>";
// 		return $db->dt["maxlevel"];
// 		print_r($vlevels);
// 		exit;
		return $maxvalue + 1;
	}
	
	function CheckSubCategory($cid,$depth){
		global $db, $admin_config, $layoutXml;
		
		$endpos = $depth*3+3;
		$this_depth = $depth;
		
// 		if($admin_config[mall_page_type] == "MI"){
// 			$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth > $this_depth and cid LIKE '".substr($cid,0,$endpos)."%' ";
// 		}else{
// 			$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth > $this_depth and cid LIKE '".substr($cid,0,$endpos)."%'";
// 		}
// 		$db->query($sql);
		
// 		$layoutXml
		
		
		$layouts = $layoutXml->simpleXml->xpath("/layouts/layout[substring(@cid,1," . $endpos . ") = '" . substr($cid, 0, $endpos) . "' and @depth > " . $this_depth . "]");
		
// 		echo "$sql<br>";
		$count = count($layouts);
		
		if ($count > 0){
			return true;
		}else{
			return false;
		}
		
	}



/// 기존 디비를 xml 로 바꾸기
// function updateCategoryXML(){

// 	global $DOCUMENT_ROOT, $admin_config;

// 	$xml = new XmlWriter_();
// 	$mdb = new MySQL;
// 	$mdb->query("select * from ".TBL_SHOP_LAYOUT_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");
// 	$categorys = $mdb->fetchall();

// 	$xml->push('designcategories');


// 	foreach ($categorys as $category) {
// 		//$xml->push('category', array('cid' => $category[cid], 'depth' => $category[depth], 'top_cid' => substr($category[cid],0,3)));
// 		$xml->push('category', array('regdate' => $category["regdate"]));
// 		$xml->element('cid', $category["cid"]);
// 		$xml->element('depth', $category["depth"]);
// 		$xml->element('vlevel1', $category["vlevel1"]);
// 		$xml->element('vlevel2', $category["vlevel2"]);
// 		$xml->element('vlevel3', $category["vlevel3"]);
// 		$xml->element('vlevel4', $category["vlevel4"]);
// 		$xml->element('vlevel5', $category["vlevel5"]);
// 		$xml->element('cname', $category["cname"]);
// 		$xml->element('path', $category["path"]);
// 		$xml->element('basic_link', $category["basic_link"]);
// 		$xml->element('catimg', $category["catimg"]);
// 		$xml->element('leftcatimg', $category["leftcatimg"]);
// 		$xml->element('subimg', $category["subimg"]);
// 		$xml->element('category_top_view', $category["category_top_view"]);
// 		$xml->element('category_display_type', $category["category_display_type"]);
// 		$xml->element('bbs_name', $category["bbs_name"]);
// 		$xml->element('category_use', $category["category_use"]);
// 		$xml->element('is_layout_apply', $category["is_layout_apply"]);
// 		$xml->pop();
// 	}

// 	$xml->pop();
// 	//print $xml->getXml();

// 	$dirname = "$DOCUMENT_ROOT".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];
// 	/*
// 	 if(!is_dir($dirname)){
// 	if(is_writable($path)){
// 	mkdir($dirname, 0777, true);
// 	chmod($dirname, 0777);
// 	}
// 	}
// 	*/
// 	//$fileName = "main_flash.xml";
// 	$fp = fopen($dirname."/design_categorys.xml","w");
// 	fputs($fp, $xml->getXml());
// 	fclose($fp);
// }

//echo "<br>maxlevel:".getMaxlevel($cid,$sub_depth);
?>

