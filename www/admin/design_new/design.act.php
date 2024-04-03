<?php

define_syslog_variables();
openlog("phplog", LOG_PID , LOG_LOCAL0);


	include("../class/layout.class");
	include('./design.common.php');
	include_once("../class/LayoutXml/LayoutXml.class");
	
	//syslog(LOG_INFO, 'design.act BEGIN');
	//syslog(LOG_INFO, $layout_act);
	$db = new Database;
	
	$layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout2.xml";
	$layoutXml = new LayoutXml($layoutXmlPath);
	

// 	echo("aaaaaaaaaaaaaaaaa");
// 	print_r($layoutXml->photoSkins);
	
	
	if ($design_act == "bbs_update"){
		
		//$page_contents = $content;
		
		$db->query("select page_contents from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix='$mall_ix' ");
		
		if($design_backup){		
				$sql = "insert into ".TBL_SHOP_PAGEINFO." (page_ix, mall_ix, page_name, page_change_memo, page_contents, regdate) values ";
				$sql .= " ('', '$mall_ix', 'bbs_templet/".$templet."/".$page_name."', '$page_change_memo', '$page_contents', NOW()) ";
				$db->query($sql);
				
				// 입력후 페이지에 쓰기 위해서 다시 불러온다.
				$db->query("select page_contents, page_ix from ".TBL_SHOP_PAGEINFO." where page_name ='bbs_templet/".$templet."/".$page_name."' and mall_ix ='$mall_ix' order by regdate desc limit 1");
				$db->fetch();
				$page_contents = $db->dt[page_contents];
		}else{
			$sql = "insert into ".TBL_SHOP_TMP." (mall_ix, design_tmp) values ";
			$sql .= " ( '$mall_ix', '$page_contents') ";
			$db->query($sql);
			
			$db->query("select design_tmp as page_contents from ".TBL_SHOP_TMP." where mall_ix ='$mall_ix' ");
			$db->fetch();
			$page_contents = $db->dt[page_contents];
			$db->query("delete from ".TBL_SHOP_TMP." where mall_ix ='$mall_ix' ");
		}
		
		
		//$file_path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/bbs_templet/".$templet."/".$page_name;	
		$file_path = $_SERVER["DOCUMENT_ROOT"]."/bbs_templet/".$templet."/".$page_name;	
		
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
			//echo("<script>alert('\"$page_name\" 해당파일에 대한 쓰기권한이 없습니다.');</script>");
			echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['design.act.php']['A'][language]);</script>"); 
			//echo("<script>parent.document.location.reload();</script>");
			//chmod($file_path,0666);
			//echo("<script>location.href = './design.php?SubID=SM22464243Sub&templet=$templet&page_name=$page_name';</script>");
		}
	
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = $DOCUMENT_ROOT.$admin_config[mall_data_root]."/_cache/"; 
		
		$tpl->clearCache('000000000000000'); 
	}
	
	
	
	if ($design_act == "update" || $design_act == "pop_update"){
		
		//$page_contents = $content;
		
		$db->query("select page_contents from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix='$mall_ix' ");
	
		
		
		if($mod == ""){
			$results = $layoutXml->search("layouts", array("cid", "mall_ix")
											 	   , array($pcode , $mall_ix));
			
			foreach($results as $result){
				$layoutXml->layouts[$result->layout_index]->page_link	= $page_link;
				$layoutXml->layouts[$result->layout_index]->page_title	= $page_title;
				$layoutXml->layouts[$result->layout_index]->page_help	= $page_help;
				$layoutXml->layouts[$result->layout_index]->page_addscript	= $page_addscript;
				$layoutXml->layouts[$result->layout_index]->page_body	= $page_body;
				$layoutXml->layouts[$result->layout_index]->page_desc	= $page_desc;
				$layoutXml->layouts[$result->layout_index]->page_navi	= $page_navi;
			}
			$layoutXml->SaveXml($layoutXmlPath);
			
// 			$sql = "update ".TBL_SHOP_DESIGN." set 
// 					page_link='$page_link',page_title='$page_title',page_help='$page_help',page_addscript='$page_addscript',page_body='$page_body',page_desc='$page_desc',page_navi='$page_navi'
// 					where pcode='$pcode' and mall_ix='$mall_ix'	";
					
// 			$db->query($sql);
		}
		
		if($design_backup){
			
			
			if($mod == ""){
				$sql = "insert into ".TBL_SHOP_PAGEINFO." (page_ix, mall_ix, page_name, page_change_memo, page_contents, regdate) values ";
				$sql .= " ('', '$mall_ix', '".$page_path."/".$page_name."', '$page_change_memo', '$page_contents', NOW()) ";
				$db->query($sql);
				
				// 입력후 페이지에 쓰기 위해서 다시 불러온다.
				$db->query("select page_contents, page_ix from ".TBL_SHOP_PAGEINFO." where page_name ='".$page_path."/".$page_name."' and mall_ix ='$mall_ix' order by regdate desc limit 1");
				$db->fetch();
				$page_contents = $db->dt[page_contents];
			}else{
				$sql = "insert into ".TBL_SHOP_PAGEINFO." (page_ix, mall_ix, page_name, page_change_memo, page_contents, regdate) values ";
				$sql .= " ('', '$mall_ix', '".$mod."/".$page_name."', '$page_change_memo', '$page_contents', NOW()) ";
				$db->query($sql);
				
				// 입력후 페이지에 쓰기 위해서 다시 불러온다.
				$db->query("select page_contents, page_ix from ".TBL_SHOP_PAGEINFO." where page_name ='".$mod."/".$page_name."' and mall_ix ='$mall_ix' order by regdate desc limit 1");
				$db->fetch();
				$page_contents = $db->dt[page_contents];
			}
			
			
			
		}else{
			$sql = "insert into ".TBL_SHOP_TMP." (mall_ix, design_tmp) values ";
			$sql .= " ( '$mall_ix', '$page_contents') ";
			$db->query($sql);
			
			$db->query("select design_tmp as page_contents from ".TBL_SHOP_TMP." where mall_ix ='$mall_ix' ");
			$db->fetch();
			$page_contents = $db->dt[page_contents];
			$db->query("delete from ".TBL_SHOP_TMP." where mall_ix ='$mall_ix' ");
		}
		
		
		if($admin_config[mall_page_type] == "P"){		
			$file_path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[selected_templete]."/".($mod != "" ? $mod:$page_path)."/".$page_name;			
		}else if($admin_config[mall_page_type] == "M"){
			$file_path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/mobile_templet/".$admin_config[selected_templete]."/".($mod != "" ? $mod:$page_path)."/".$page_name;	
		}else if($admin_config[mall_page_type] == "MI"){
			$file_path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/minishop_templet/".$admin_config[selected_templete]."/".($mod != "" ? $mod:$page_path)."/".$page_name;	
		}
	
		
		
		$tab_no = $_POST["tab_no"];
		session_register("tab_no");
		if(is_writeable($file_path)){
			$fp = fopen($file_path,"w");
			fwrite($fp,$page_contents);	
			
			if($design_act == "pop_update"){
				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
				echo("<script>self.close();</script>");
			}else{
				//echo("<script>parent.document.location.reload();</script>");
				if($design_backup){
					echo "<script>
								function reloading(){								
									parent.unloading();
									document.location.href= parent.document.URL+'&mmode=innerview';
								}
								</script>";
					echo("<script>setTimeout('reloading()',1000);</script>");							
				}else{
					echo "<script>setTimeout('parent.unloading()',1000);</script>";
				}
			}
		}else{
			echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['design.act.php']['A'][language]);</script>"); //해당파일에 대한 쓰기권한이 없습니다.
			if($design_act == "pop_update"){
				echo("<script>self.close();</script>");
			}else{
				echo("<script>parent.document.location.reload();</script>");
			}
		}
	}
	
	if ($design_act == "delete"){
		//echo ("delete from ".TBL_SHOP_PAGEINFO." where mall_ix='$mall_ix' and page_name ='$page_name' and page_ix = '$page_ix'");
		$db->query("delete from ".TBL_SHOP_PAGEINFO." where mall_ix='$mall_ix'  and page_ix = '$page_ix'");
		
		//echo("<script>parent.document.location.reload();</script>");
		echo("<script>document.location.href= parent.document.URL+'&mmode=innerview';</script>");			
	}
	
	if ($design_act == "select_delete"){
		//print_r($_POST);
		for($i=0;$i < count($page_ix);$i++){
			echo ("delete from ".TBL_SHOP_PAGEINFO." where mall_ix='$mall_ix'  and page_ix = '".$page_ix[$i]."'");
			if($page_ix[$i]){
				
				$db->query("delete from ".TBL_SHOP_PAGEINFO." where mall_ix='$mall_ix'  and page_ix = '".$page_ix[$i]."'");
			}
		}
		
		echo("<script>document.location.href= parent.document.URL+'&mmode=innerview';</script>");			
	}
	
	
	if ($design_act == "recovery"){	
		$db->query("select page_contents from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix='$mall_ix' and page_ix = '$page_ix' ");
		//echo ("select page_contents from ".TBL_SHOP_PAGEINFO." where page_name ='$page_name' and mall_ix='$mall_ix' and page_ix = '$page_ix' ");
		$db->fetch();
		$page_contents = $db->dt[page_contents];
		
		echo "<html><meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<body >
		<form name='recovery'>
		<textarea  style='height:90%;' wrap='off' name='recovery_page_contents' >
		".$page_contents."
		</textarea>
		</form>
		</body>
		</html>";	
		
		echo("<script language='javascript' src='../_language/language.php'></script><script language='javascript'>
		//alert(document.forms['recovery'].recovery_page_contents.value);
		parent.document.forms['info_input'].page_contents.value = document.forms['recovery'].recovery_page_contents.value;
		parent.document.forms['info_input'].page_contents.style.backgroundColor = '#efefef';
		parent.document.forms['info_input'].page_contents.style.color = '#000000';
		alert(language_data['design.act.php']['B'][language]);//정상적으로  화면복구 되었습니다.
		</script>");
	}
	
	if ($layout_act == "update"){
		//"mall_ix", 
		//$mall_ix, 
		$results = $layoutXml->search("layouts", array("pcode", "skin_type", "templet_name")
				, array($pcode , $admin_config[mall_page_type], $admin_config[selected_templete]));
		
		foreach($results as $result){
			$layoutXml->layouts[$result->layout_index]->layout 			= $_POST["layout"];
			$layoutXml->layouts[$result->layout_index]->contents	 	= $_POST["contents"];
			$layoutXml->layouts[$result->layout_index]->contents_add 	= $_POST["contents_add"];
			$layoutXml->layouts[$result->layout_index]->header1 		= $_POST["header1"];
			$layoutXml->layouts[$result->layout_index]->header2 		= $_POST["header2"];
			$layoutXml->layouts[$result->layout_index]->leftmenu 		= $_POST["leftmenu"];
			$layoutXml->layouts[$result->layout_index]->rightmenu 		= $_POST["rightmenu"];
			$layoutXml->layouts[$result->layout_index]->footer1 		= $_POST["footer1"];
			$layoutXml->layouts[$result->layout_index]->footer2 		= $_POST["footer2"];
			$layoutXml->layouts[$result->layout_index]->page_path 		= $_POST["page_path"];
			$layoutXml->layouts[$result->layout_index]->caching 		= $_POST["caching"];
			$layoutXml->layouts[$result->layout_index]->caching_time 	= $_POST["caching_time"];
			
					
		}
//   		print_r($_POST);
//   		exit;
		
 		$layoutXml->SaveXml($layoutXmlPath);
		
		
		
		//echo($layoutXmlPath);
		//echo($layoutXml->asXML());
		
// 		<layout>layout_sub_left.htm</layout>
// 		<page_navi></page_navi>
// 		<header1>header_top.htm</header1>
// 		<header2></header2>
// 		<leftmenu>category_left.htm</leftmenu>
// 		<contents>index.htm</contents>
// 		<contents_add></contents_add>
// 		<rightmenu>today_history.htm</rightmenu>
// 		<footer1></footer1>
// 		<footer2>footer_desc.htm</footer2>
// 		<page_path>main</page_path>
// 		<caching>0</caching>
// 		<caching_time>0</caching_time>
		
		
// 		$sql = "update ".TBL_SHOP_DESIGN." set 
// 				page_link='$page_link',page_title='$page_title',page_path='$page_path',page_help='$page_help',page_addscript='$page_addscript',page_body='$page_body',page_desc='$page_desc',layout='$layout',header1='$header1',header2='$header2',leftmenu='$leftmenu',contents='$contents',contents_add='$contents_add',rightmenu='$rightmenu',footer1='$footer1',footer2='$footer2',caching='$caching',caching_time='$caching_time',templet_name = '".$admin_config[selected_templete]."'
// 				where pcode='$pcode' and mall_ix='$mall_ix' and	skin_type = '".$admin_config[mall_page_type]."' and templet_name = '".$admin_config[selected_templete]."'";
	
// 		//echo $sql;		
// 		$db->query($sql);
		
		
		
		
		if($basic_design){
			$searchResults = $layoutXml->search("layouts", array("pcode", "mall_ix", "skin_type", "templet_name")
												   , array("basic" ,$mall_ix , $admin_config[mall_page_type], $admin_config[selected_templete]));

			if(count($searchResults->layouts) == 0){
				$layoutitem = new LayoutItem();
				$layoutitem->pcode		= "basic";
				$layoutitem->mall_ix	= $mall_ix;
				$layoutitem->skin_type	= $admin_config[mall_page_type];
				$layoutitem->page_type	= "B";
				$layoutitem->layout		= $layout;
				$layoutitem->header1	= $header1;
				$layoutitem->header2	= $header2;
				$layoutitem->leftmenu	= $leftmenu;
				$layoutitem->contents	= $contents;
				$layoutitem->rightmenu	= $rightmenu;
				$layoutitem->footer1	= $footer1;
				$layoutitem->footer2	= $footer2;
				$layoutitem->caching	= $caching;
				$layoutitem->caching_time	=$caching_time;
				$layoutitem->regdate	= date("Y-m-d H:i:s");
				$layoutXml->asXML($layoutXmlPath);
			} else {
				
				foreach($searchResults as $result){
					$layoutXml->layouts[$result->layout_index]->pcode	= "basic";
					$layoutXml->layouts[$result->layout_index]->mall_ix	= $mall_ix;
					$layoutXml->layouts[$result->layout_index]->skin_type	= $admin_config[mall_page_type];
					$layoutXml->layouts[$result->layout_index]->templet_name= $admin_config[selected_templete];
				}
				$layoutXml->asXML($layoutXmlPath);
			}
// 			$db->query("select * from ".TBL_SHOP_DESIGN." where pcode='basic' and mall_ix='$mall_ix' and skin_type = '".$admin_config[mall_page_type]."' and templet_name = '".$admin_config[selected_templete]."'");
			
// 			if($db->total){
// 				$sql = "update ".TBL_SHOP_DESIGN." set 
// 						layout='$layout',header1='$header1',header2='$header2',leftmenu='$leftmenu',rightmenu='$rightmenu',footer1='$footer1',footer2='$footer2',caching='$caching',caching_time='$caching_time' 
// 						where pcode='basic' and mall_ix='$mall_ix'	and	skin_type = '".$admin_config[mall_page_type]."' and templet_name = '".$admin_config[selected_templete]."' ";
				
// 				$db->query($sql);
// 			}else{
// 				$sql = "insert into ".TBL_SHOP_DESIGN."(pcode,mall_ix,skin_type, page_type,layout,header1,header2,leftmenu,contents,rightmenu,footer1,footer2,caching, caching_time,templet_name,regdate) 
// 				values('basic','$mall_ix','".$admin_config[mall_page_type]."','B','$layout','$header1','$header2','$leftmenu','$contents','$rightmenu','$footer1','$footer2','$caching','$caching_time','".$admin_config[selected_templete]."',NOW())";
				
// 				$db->query($sql);
// 			}
		}
		
	
		$tpl = new Template_();
		$tpl->caching = true;
		$tpl->cache_dir = $DOCUMENT_ROOT.$admin_config[mall_data_root]."/_cache/"; 
		
		$tpl->clearCache($pcode); 
		
		
// 		if($admin_config[mall_page_type] == "P"){
// 			updateLayoutXML($mall_ix);
// 		}else{
// 			updateLayoutXML($mall_ix, $admin_config[mall_page_type]);
// 		}
	
		$tab_no = $_POST["tab_no"];
		session_register("tab_no");
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert(\"정상적으로 수정되었습니다 \");</script>");
		echo("<script>parent.document.location.reload();</script>");
		
	}

closelog();

?>
