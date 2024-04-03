<?php
	include("../../class/database.class");
	include('./design.common.php');
	include_once ("../class/LayoutXml/LayoutXml.class");

	define_syslog_variables();
	openlog("phplog", LOG_PID , LOG_LOCAL0);
	
	$db = new Database;
	
	
	//syslog(LOG_INFO, "design_layout BEGIN");



if ($layout_act == "update") {
	
	// "mall_ix",
	// $mall_ix,
	
if ($admin_config [mall_page_type] == "P") {
	$layoutXmlPath = $_SERVER ["DOCUMENT_ROOT"] . $admininfo ["mall_data_root"] . "/templet/" . $admin_config ["selected_templete"] . "/layout2.xml";
} else if ($admin_config [mall_page_type] == "M") {
	$layoutXmlPath = $_SERVER ["DOCUMENT_ROOT"] . $admininfo ["mall_data_root"] . "/mobile_templet/" . $admin_config ["selected_templete"] . "/layout2.xml";
} else if ($admin_config [mall_page_type] == "MI") {
	$layoutXmlPath = $_SERVER ["DOCUMENT_ROOT"] . $admininfo ["mall_data_root"] . "/templet/" . $admin_config ["selected_templete"] . "/layout2.xml";
}
//echo $layoutXmlPath;
$layoutXml = new LayoutXml ( $layoutXmlPath );
//print_r($layoutXml);
	for($i=0; $i < count($_POST["pcode"]);$i++){ 
		$results = $layoutXml->search ( "layouts", array (
				"pcode",
				"skin_type",
				"templet_name" 
		), array (
				str_replace("_","",$_POST["pcode"][$i]),
				$admin_config [mall_page_type],
				$admin_config [selected_templete] 
		) );
		//echo str_replace("_","",$_POST["pcode"][$i]);
		 //echo $pcode."::".$mall_ix."::".$admin_config[mall_page_type]."::".$admin_config[selected_templete];
		// exit;
		// print_r($_POST);
		//echo $admin_config [selected_templete];
		// echo $admin_config[mall_page_type];
		//print_r($results);
		//exit;
		foreach ( $results as $result ) {
			
			// echo $result->layout_index;
			// exit;
			$layoutXml->layouts [$result->layout_index]->layout = $_POST ["layout"];
			$layoutXml->layouts [$result->layout_index]->contents = $_POST ["contents"];
			$layoutXml->layouts [$result->layout_index]->contents_add = $_POST ["contents_add"];
			$layoutXml->layouts [$result->layout_index]->header1 = $_POST ["header1"];
			$layoutXml->layouts [$result->layout_index]->header2 = $_POST ["header2"];
			$layoutXml->layouts [$result->layout_index]->leftmenu = $_POST ["leftmenu"];
			$layoutXml->layouts [$result->layout_index]->rightmenu = $_POST ["rightmenu"];
			$layoutXml->layouts [$result->layout_index]->footer1 = $_POST ["footer1"];
			$layoutXml->layouts [$result->layout_index]->footer2 = $_POST ["footer2"];
			$layoutXml->layouts [$result->layout_index]->page_path = $_POST ["page_path"];
			$layoutXml->layouts [$result->layout_index]->caching = $_POST ["caching"];
			$layoutXml->layouts [$result->layout_index]->caching_time = $_POST ["caching_time"];
		}
	}
	
	// exit;
	$layoutXml->SaveXml ( $layoutXmlPath );
	 
	
	if ($basic_design) {

		//echo "aaaaaa";

		$searchResults = $layoutXml->search ( "layouts", array (
				"pcode",
				"mall_ix",
				"skin_type",
				"templet_name" 
		), array (
				"basic",
				$mall_ix,
				$admin_config [mall_page_type],
				$admin_config [selected_templete] 
		) );
		//print_r($searchResults);
	//	echo count($searchResults);
	//	echo $pcode."::".$mall_ix."::".$admin_config[mall_page_type]."::".$admin_config[selected_templete];
	// exit;
	//	echo "<br>count : ";		
	//	echo count ( $searchResults->layouts )."<br>";

	//	if (count ( $searchResults->layouts ) == 0) {
		if (count($searchResults) == 0) {
			//echo "신규추가<br>";
			$layoutitem = new LayoutItem ();
			$layoutitem->pcode = "basic";
			$layoutitem->mall_ix = $mall_ix;
			$layoutitem->skin_type = $admin_config [mall_page_type];
			$layoutitem->page_type = "B";
			$layoutitem->layout = $layout;
			$layoutitem->header1 = $header1;
			$layoutitem->header2 = $header2;
			$layoutitem->leftmenu = $leftmenu;
			$layoutitem->contents = $contents;
			$layoutitem->rightmenu = $rightmenu;
			$layoutitem->footer1 = $footer1;
			$layoutitem->footer2 = $footer2;
			$layoutitem->caching = $caching;
			$layoutitem->caching_time = $caching_time;
			$layoutitem->regdate = date ( "Y-m-d H:i:s" );
			$layoutXml->layouts[count ( $layoutXml->layouts )] = $layoutitem;
			$layoutXml->SaveXml ( $layoutXmlPath );
			//echo $layoutXmlPath;
			//print_r($aaa);
		} else {
			//echo 111;
			
			foreach ( $searchResults as $result ) {
				//echo $result->layout_index;
				$layoutXml->layouts[$result->layout_index]->pcode = "basic";
				// $layoutXml->layouts[$result->layout_index]->mall_ix = $mall_ix;
				// $layoutXml->layouts[$result->layout_index]->skin_type = $admin_config[mall_page_type];
				// $layoutXml->layouts[$result->layout_index]->templet_name= $admin_config[selected_templete];
				$layoutXml->layouts[$result->layout_index]->layout= $layout;
				$layoutXml->layouts[$result->layout_index]->mall_ix = $mall_ix;
				$layoutXml->layouts[$result->layout_index]->skin_type = $admin_config [mall_page_type];
				$layoutXml->layouts[$result->layout_index]->page_type = "B";
				$layoutXml->layouts[$result->layout_index]->layout = $layout;
				$layoutXml->layouts[$result->layout_index]->header1 = $header1;
				$layoutXml->layouts[$result->layout_index]->header2 = $header2;
				$layoutXml->layouts[$result->layout_index]->leftmenu = $leftmenu;
				$layoutXml->layouts[$result->layout_index]->contents = $contents;
				$layoutXml->layouts[$result->layout_index]->rightmenu = $rightmenu;
				$layoutXml->layouts[$result->layout_index]->footer1 = $footer1;
				$layoutXml->layouts[$result->layout_index]->footer2 = $footer2;
				$layoutXml->layouts[$result->layout_index]->caching = $caching;
				$layoutXml->layouts[$result->layout_index]->caching_time = $caching_time;
				$layoutXml->layouts[$result->layout_index]->regdate = date ( "Y-m-d H:i:s" );
			}
			//print_r($layoutitem);
			//echo $layoutXmlPath;
			$layoutXml->SaveXml ( $layoutXmlPath );
		}
		//exit;
		 
	}
	
	/*
	$tpl = new Template_ ();
	$tpl->caching = true;
	$tpl->cache_dir = $DOCUMENT_ROOT . $admin_config [mall_data_root] . "/_cache/";
	
	$tpl->clearCache ( $pcode );
	
 */
	$tab_no = $_POST ["tab_no"];
	session_register ( "tab_no" );
	echo ("<script language='javascript' src='../js/message.js.php'></script><script>show_alert(\"정상적으로 수정되었습니다 \");</script>");
	echo ("<script>parent.document.location.reload();</script>");
}

/*	
	if ($layout_act == "update"){
		
		if($basic_design){
			
			$db->query("select * from ".TBL_SHOP_DESIGN." where pcode='basic' and mall_ix='$mall_ix' ");
			
			if($db->total){
				$sql = "	update ".TBL_SHOP_DESIGN." set 
						layout='$layout',header1='$header1',header2='$header2',leftmenu='$leftmenu',rightmenu='$rightmenu',footer1='$footer1',footer2='$footer2' 
						where pcode='basic' and mall_ix='$mall_ix'	";
				
				$db->query($sql);
			}else{
				$sql = "insert into shop_design(pcode,mall_ix,page_type,layout,header1,header2,leftmenu,contents,rightmenu,footer1,footer2,regdate) 
				values('basic','$mall_ix','B','$layout','$header1','$header2','$leftmenu','$contents','$rightmenu','$footer1','$footer2',NOW())";
				
				$db->query($sql);
			}
		}
	
		for($i=0;$i<count($pcode);$i++){
			$db->query("select * from ".TBL_SHOP_DESIGN." where pcode='".str_replace("_","",$pcode[$i])."' and mall_ix='$mall_ix' ");
			
			if($db->total){
				$sql = "	update ".TBL_SHOP_DESIGN." set 
						layout='$layout',header1='$header1',header2='$header2',leftmenu='$leftmenu',rightmenu='$rightmenu',footer1='$footer1',footer2='$footer2' 
						where pcode='".str_replace("_","",$pcode[$i])."' and mall_ix='$mall_ix'	";
				//page_link='$page_link',page_title='$page_title',page_path='$page_path',page_help='$page_help',page_addscript='$page_addscript',page_body='$page_body',page_desc='$page_desc',	
				//echo $sql;		
			}else{
				$sql = "insert into ".TBL_SHOP_DESIGN."(pcode,mall_ix,page_type,layout,header1,header2,leftmenu,contents,rightmenu,footer1,footer2,regdate) 
				values('".str_replace("_","",$pcode[$i])."','$mall_ix','B','$layout','$header1','$header2','$leftmenu','$contents','$rightmenu','$footer1','$footer2',NOW())";
			}
			$db->query($sql);
			
		}
		
		updateLayoutXML($mall_ix);
		
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('레이아웃이 정상적으로 수정되었습니다.');</script>");
		echo("<script>parent.document.location.reload();</script>");
	}
*/	
	//syslog(LOG_INFO, "design_layout END");
closelog();
?>
