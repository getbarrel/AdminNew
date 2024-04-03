<?php
	if($pcode){
		$selected_cid = $pcode;
	}
	
	function sTree()
	{
		global $id, $admin_config, $admininfo;
		
		$layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout2.xml";

		$layoutXml = new LayoutXml($layoutXmlPath);
		
		$source_path = "/manage";
		$m_string = "
		
		<script language='JavaScript' src='/admin/include/sTree.js'></script>
		<script>
		
		/*	 Create Tree		*/
			var tree = new sTree();
			tree.color = 'black';
			tree.bgColor = 'white';
			tree.borderWidth = 0;
		
		
		/*	Create Root node	*/
			var rootnode = new sTreeNode('디자인카테고리', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');
			rootnode.action = \"setCategory('product category','000000000000000',-1,'".$id."')\";
			rootnode.expanded = true;";
		
// 		$mdb->query("SELECT concat(cid,'_') as cid, cname, depth,  category_display_type, is_layout_apply FROM ".TBL_SHOP_LAYOUT_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");
		
		$total = count($layoutXml->layouts);
		
		xsort($layoutXml->layouts, 'vlevelf', SORT_ASC);
		
// 		$total = $mdb->total;
// 		print_r($layoutXml->layouts);
// 		exit;
		for ($i = 0; $i < $total; $i++)
		{
			if(($layoutXml->layouts[$i]->pcode != "basic")){
				if ($layoutXml->layouts[$i]->depth == 0){
					$m_string = $m_string.sPrintNode($layoutXml->layouts[$i]);
				}else {
					$m_string = $m_string.sPrintGroupNode($layoutXml->layouts[$i]);
				}
			}
// 			$mdb->fetch($i);
		
// 			if ($mdb->dt["depth"] == 0){
// 				$m_string = $m_string.sPrintNode($mdb);
// 			}else if($mdb->dt["depth"] == 1){
// 				$m_string = $m_string.sPrintGroupNode($mdb);
// 			}else if($mdb->dt["depth"] == 2){
// 				$m_string = $m_string.sPrintGroupNode($mdb);
// 			}else if($mdb->dt["depth"] == 3){
// 				$m_string = $m_string.sPrintGroupNode($mdb);
// 			}else if($mdb->dt["depth"] == 4){
// 				$m_string = $m_string.sPrintGroupNode($mdb);
// 			}
		}
		
			$m_string = $m_string."tree.addNode(rootnode);";
		
			$m_string = $m_string."
								</script>
								
								<div id=TREE_BAR style='margin:5px;'>
								<script>
								tree.draw();
								
								tree.nodes[0].select();
								</script>
								</div>
								";
// 		echo("<br />");
// 		echo("Result!!!!!!!!!!!!!!!!!!!!!!!BEGIN ");
// 		echo($m_string);
// 		echo("Result!!!!!!!!!!!!!!!!!!!!!!!!!END ");
// 		echo("<br />");
		return $m_string;
	}
	
	
	
	
	function sPrintRootNode($cname){
	
		$cname = str_replace("\"","&quot;",$cname);
	
	
		$vPrintRootNode = "var rootnode = new sTreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
				rootnode.expanded = true;\n\n";
	
		return $vPrintRootNode;
	}
	
	
	function sPrintNode($layout){
		global $selected_cid;
// 		$cname = $mdb->dt[cname];
// 		$cid = $mdb->dt[cid];
// 		$depth = $mdb->dt[depth];
// 		$category_display_type = $mdb->dt[category_display_type];
// 		$is_layout_apply = $mdb->dt[is_layout_apply];
	
		$cname = $layout->cname;
		$cid = $layout->cid . "_";
		$depth = $layout->depth;
		$category_display_type = $layout->category_display_type;
		$is_layout_apply = $layout->is_layout_apply;
		
		$cid1 = substr($cid,0,3);
		$cid2 = substr($cid,3,3);
		$cid3 = substr($cid,6,3);
		$cid4 = substr($cid,9,3);
		$cid5 = substr($cid,12,3);
	
		$cname = str_replace("\"","&quot;",$cname);
	
		if($layout->category_display_type == "P"){
			$mstring =  "		var node$cid = new sTreeNode('$cname ', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
		}else if($layout->category_display_type == "F"){
			$mstring =  "		var node$cid = new sTreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
		}else{
			$mstring =  "		var node$cid = new sTreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');\n";
		}
	
		if(substr($selected_cid,0,($depth+1)*3) == substr($cid,0,($depth+1)*3)){
			$mstring .=  "
			node$cid.expanded = true;
			node$cid.cid = '$cid';
			node$cid.cdepth = '$depth';
			node$cid.is_layout_apply = '$is_layout_apply';
			node$cid.action = \"empty();\";
			rootnode.addNode(node$cid);\n\n";
		}else{
			$mstring .=  "
			node$cid.expanded = false;
			node$cid.cid = '$cid';
			node$cid.cdepth = '$depth';
			node$cid.is_layout_apply = '$is_layout_apply';
	
			node$cid.action = \"empty();\";
			rootnode.addNode(node$cid);\n\n";
		}
	
		return $mstring;
	}
	
	
	function sPrintGroupNode($layout)
	{
// 		print_r($layout);
// 		exit;
		$cname = $layout->cname;
		$mcid  = $layout->cid . "_";
		
// 		echo("<br />");
// 		echo("<br />");
// 		echo($layout->cid);
// 		echo("<br />");
		
		$depth = $layout->depth;
		$category_display_type = $layout->category_display_type;
		$is_layout_apply = $layout->is_layout_apply;
	
		global $cid;
		$cid1 = substr($mcid,0,3);
		$cid2 = substr($mcid,3,3);
		$cid3 = substr($mcid,6,3);
		$cid4 = substr($mcid,9,3);
		$cid5 = substr($mcid,12,3);
	
		$Parentdepth = $depth - 1;
	
		if ($depth+1 == 1){
			$cid1 = "000";
		}else if($depth+1 == 2){
			$cid2 = "000";
		}else if($depth+1 == 3){
			$cid3 = "000";
		}else if($depth+1 == 4){
			$cid4 = "000";
		}else if($depth+1 == 5){
			$cid5 = "000";
		}
	
		$parent_cid = "$cid1$cid2$cid3$cid4$cid5"."_";
// 		echo("mcid  "  . $mcid);
// 		echo("<br />");
// 		echo("depth  "  . $depth);
// 		echo("<br />");
// 		echo("parent_cid  "  . $parent_cid);
// 		echo("<br />");
		if ($depth ==1){
			$ParentNodeCode = "node$parent_cid";
		}else if($depth ==2){
			$ParentNodeCode = "groupnode$parent_cid";
		}else if($depth ==3){
			$ParentNodeCode = "groupnode$parent_cid";
		}else if($depth ==4){
			$ParentNodeCode = "groupnode$parent_cid";
		}else if($depth ==5){
			$ParentNodeCode = "groupnode$parent_cid";
		}
	
// 		echo("ParentNodeCode : " . $ParentNodeCode );
// 		echo("<br />");
// 		echo("groupnodeSmcid : " . "groupnode$mcid");
// 		echo("<br />");
	
		$cname = str_replace("\"","&quot;",$cname);
		if($layout->category_display_type == "P"){
			$mstring =  "		var groupnode$mcid = new sTreeNode('$cname', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
		}else{
			$mstring =  "		var groupnode$mcid = new sTreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
		}
	
		if ($mcid == $cid){
			$mstring .=  "	groupnode$cid.expanded = true;\n";
		//	$mstring .=  "	groupnode$cid.select = true;\n";
		}
	
	
		$mstring .=  "	groupnode$mcid.tooltip = '$cname';
			groupnode$mcid.id ='nodeid$mcid';
			groupnode$mcid.cid = '$mcid';
			groupnode$mcid.cdepth = '$depth';
			groupnode$mcid.is_layout_apply = '$is_layout_apply';
			groupnode$mcid.action = \"empty();\";
			$ParentNodeCode.addNode(groupnode$mcid);\n\n";
// 		echo("mstring : " . $mstring);
// 		echo("<br />");
		return $mstring;
	}
	
	
	
// 	function sPrintNodeX($cname,$cid,$depth)
// 	{
// 		global $id;
// 		$cid1 = substr($cid,0,3);
// 		$cid2 = substr($cid,3,3);
// 		$cid3 = substr($cid,6,3);
// 		$cid4 = substr($cid,9,3);
// 		$cid5 = substr($cid,12,3);
	
// 		$cname = str_replace("\"","&quot;",$cname);
	
// 		return "	var node$cid = new sTreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
// 		node$cid.expanded = true;
// 		node$cid.action = \"empty();\";
// 		rootnode.addNode(node$cid);\n\n";
// 	}
	
	
/////////// 설마 아래 안쓰는거?	
	
// 	function sPrintGroupNodeX($cname,$mcid,$depth)
// 	{
// 		global $id,$cid;
// 		$cid1 = substr($mcid,0,3);
// 		$cid2 = substr($mcid,3,3);
// 		$cid3 = substr($mcid,6,3);
// 		$cid4 = substr($mcid,9,3);
// 		$cid5 = substr($mcid,12,3);
	
// 		$Parentdepth = $depth - 1;
	
// 		if ($depth+1 == 1){
// 			$cid1 = "000";
// 		}else if($depth+1 == 2){
// 			$cid2 = "000";
// 		}else if($depth+1 == 3){
// 			$cid3 = "000";
// 		}else if($depth+1 == 4){
// 			$cid4 = "000";
// 		}else if($depth+1 == 5){
// 			$cid5 = "000";
// 		}
	
// 		$parent_cid = "$cid1$cid2$cid3$cid4$cid5";
	
// 		if ($depth ==1){
// 			$ParentNodeCode = "node$parent_cid";
// 		}else if($depth ==2){
// 			$ParentNodeCode = "groupnode$parent_cid";
// 		}else if($depth ==3){
// 			$ParentNodeCode = "groupnode$parent_cid";
// 		}else if($depth ==4){
// 			$ParentNodeCode = "groupnode$parent_cid";
// 		}else if($depth ==5){
// 			$ParentNodeCode = "groupnode$parent_cid";
// 		}
	
// 		if ($cid == $mcid){
// 			$expandstring = "true";
// 		}else{
// 			$expandstring = "false";
// 		}
	
// 		$cname = str_replace("\"","&quot;",$cname);
	
// 		//return "		var groupnode$mcid = new sTreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
// 		if($mdb->dt[category_display_type] == "P"){
// 			$mstring =  "		var groupnode$mcid = new sTreeNode('$cname', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
// 		}else{
// 			$mstring =  "		var groupnode$mcid = new sTreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
// 		}
	
// 		$mstring .=  "
// 			groupnode$mcid.tooltip = '$cname';
// 			groupnode$mcid.id ='nodeid$mcid';
// 			groupnode$mcid.expanded = $expandstring;
// 			groupnode$mcid.action = \"empty();\";
// 			$ParentNodeCode.addNode(groupnode$mcid);\n\n";
	
// 		return $mstring;
// 	}

?>