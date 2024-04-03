<?
include ("../class/layout.class");
include ("../class/LayoutXml/LayoutXml.class");
include ("design.common.php");

switch($admin_config [mall_page_type]){
	case "MI" : $templet_path_div = "minishop_templet";
	break;
}

$templet_path = $admin_config ["mall_data_root"] . "/".$templet_path_div."/" . $admin_config ["selected_templete_minishop"];
$templet_file_path = $templet_path . "/" . $page_path . "/" . $page_name;
if ($pcode != "") {
	$page_path_string = $admin_config ["selected_templete_minishop"] . "/$page_path/" . $page_name;
}
$unique_file_path = $templet_path_div."/" . $admin_config ["selected_templete_minishop"] . "/" . $page_path . "/" . $page_name;
$layoutXmlPath = $_SERVER ["DOCUMENT_ROOT"] . $admininfo ["mall_data_root"] . "/".$templet_path_div."/" . $admin_config ["selected_templete_minishop"] . "/layout2.xml";
$layoutXml = new LayoutXml ( $layoutXmlPath );

if ($design_act == "update") {
	
	// $page_contents = $content;
	
	$db->query ( "select page_contents from " . TBL_SHOP_PAGEINFO . " where page_name ='$page_name' and mall_ix='$mall_ix' " );
	
	if($design_backup){
		$sql = "insert into " . TBL_SHOP_PAGEINFO . " (page_ix, mall_ix, page_name, page_change_memo, page_contents, regdate) values ";
		$sql .= " ('', '$mall_ix', '" . $unique_file_path . "', '$page_change_memo', '$page_contents', NOW()) ";
		$db->query ( $sql );
		
		// 입력후 페이지에 쓰기 위해서 다시 불러온다.
		// $db->query("select page_contents, page_ix from ".TBL_SHOP_PAGEINFO." where page_name ='".$mod."/".$page_name."' and mall_ix ='$mall_ix' order by regdate desc limit 1");
		$db->query ( "select page_contents, page_ix from " . TBL_SHOP_PAGEINFO . " where page_name ='" . $unique_file_path . "' and mall_ix ='$mall_ix' order by regdate desc limit 1" );
		$db->fetch ();
		$page_contents = $db->dt [page_contents];
	}else{
		$sql = "insert into " . TBL_SHOP_TMP . " (mall_ix, design_tmp) values ";
		$sql .= " ( '$mall_ix', '$page_contents') ";
		$db->query ( $sql );
		
		$db->query ( "select design_tmp as page_contents from " . TBL_SHOP_TMP . " where mall_ix ='$mall_ix' " );
		$db->fetch ();
		$page_contents = $db->dt [page_contents];
		$db->query ( "delete from " . TBL_SHOP_TMP . " where mall_ix ='$mall_ix' " );
	}
	
	$file_path = $_SERVER ["DOCUMENT_ROOT"] . "" . $admin_config [mall_data_root] . "/minishop_templet/" . $admin_config [selected_templete_minishop] . "/" . ($mod != "" ? $mod : $page_path) . "/" . $page_name;
	
	$tab_no = $_POST ["tab_no"];
	session_register ( "tab_no" );
	if (is_writeable ( $file_path )) {
		$fp = fopen ( $file_path, "w" );
		fwrite ( $fp, $page_contents );
		
		if ($design_backup) {
			echo ("<script>parent.document.location.reload();</script>");
			echo ("<script>setTimeout('reloading()',1000);</script>");
		} else {
			echo "<script>setTimeout('parent.unloading()',1000);</script>";
		}
	} else {
		echo ("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['design.act.php']['A'][language]);</script>"); // 해당파일에 대한 쓰기권한이 없습니다.
		echo ("<script>parent.document.location.reload();</script>");
	}
}

if ($layout_act == "update") {

	$results = $layoutXml->search ( "layouts", array ("pcode", "skin_type", "templet_name"), array ($pcode, $admin_config["mall_page_type"], $admin_config ["selected_templete_minishop"]));

	foreach ( $results as $result ) {
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

	$layoutXml->SaveXml ( $layoutXmlPath );

	if ($basic_design) {
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
		
		if (count ( $searchResults->layouts ) == 0) {
			
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
			$layoutXml->SaveXml ( $layoutXmlPath );
		} else {
			foreach ( $searchResults as $result ) {
				$layoutXml->layouts [$result->layout_index]->pcode = "basic";
				// $layoutXml->layouts[$result->layout_index]->mall_ix = $mall_ix;
				// $layoutXml->layouts[$result->layout_index]->skin_type = $admin_config[mall_page_type];
				// $layoutXml->layouts[$result->layout_index]->templet_name= $admin_config[selected_templete];
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
			}
			$layoutXml->SaveXml ( $layoutXmlPath );
		}
	}

	$tpl = new Template_ ();
	$tpl->caching = true;
	$tpl->cache_dir = $DOCUMENT_ROOT . $admin_config [mall_data_root] . "/_cache/";

	$tpl->clearCache ( $pcode );

	$tab_no = $_POST ["tab_no"];
	session_register ( "tab_no" );
	echo ("<script language='javascript' src='../js/message.js.php'></script><script>show_alert(\"정상적으로 수정되었습니다 \");</script>");
	echo ("<script>parent.document.location.reload();</script>");

}

?>
