<?php
	include("../class/LayoutXml/LayoutXml.class");

	if($pcode){
		$selected_cid = $pcode;
	}
	
	function Category()
	{
		global $admin_config, $admininfo;
// 	$mdb = new Database;
		
		global $id;
		$source_path = "/manage";	
		$m_string = "	
		<script language='JavaScript' src='/admin/include/manager.js'></script>
		<script language='JavaScript' src='/admin/include/Tree.js'></script>
		<script>
		
		/*	 Create Tree		*/
			var tree = new Tree();
			tree.color = 'black';
			tree.bgColor = 'white';
			tree.borderWidth = 0;
		
		
		/*	Create Root node	*/
			var rootnode = new TreeNode('디자인카테고리', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');
			rootnode.action = \"setCategory('product category','000000000000000',-1,'".$id."')\";
			rootnode.expanded = true;";

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
	//			echo $layoutXmlPath;
		
	$layoutXml = new LayoutXml($layoutXmlPath);
	
	$layouts = $layoutXml->simpleXml->xpath("/layouts/layout");
	$total = count($layoutXml->layouts);

	xsort($layoutXml->layouts, 'vlevelf', SORT_ASC);
	
	
	$j = 0;
	for ($i = 0; $i < $total; $i++)
	{
		
// 		echo("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx   ");
// 		echo($layoutXml->layouts[$i]->vlevelf);
// 		echo("<br />");
		
		
// 		echo("depth  :  " . $layoutXml->layouts[$i]->vlevelf . "     vlevel1 : " . $layoutXml->layouts[$i]->vlevel1);
// 		echo("vlevelf  :  " . $layoutXml->layouts[$i]->vlevelf );
// 		echo("<br />");
// 		if(($layoutXml->layouts[$i]->pcode != "basic") 
// 				&& ($layoutXml->layouts[$i]->mall_ix != "")
// 				&& ($layoutXml->layouts[$i]->skin_type != "")
// 				&& ($layoutXml->layouts[$i]->skin_type == "P")){
// 		echo("aaaaaaaaaaaaaaa " . $layoutXml->layouts[$i]->pcode);
// 		echo("<br />");

		
		
		
		
		
// 		echo($layoutXml->layouts[$i]->pcode);
// 		echo("<br />");

		
		
		
		if(($layoutXml->layouts[$i]->pcode != "basic")){
			if ($layoutXml->layouts[$i]->depth == 0){
				$m_string = $m_string.PrintNode($layoutXml->layouts[$i]);
			}else {
// 				echo($layoutXml->layouts[$i]->pcode);
// 				echo("<br />");
				
				if($j < 281 || true){
					
				$m_string = $m_string.PrintGroupNode($layoutXml->layouts[$i]);
					$j++;
				}else{
					if($j+1 == 282){
					//print_r($layoutXml->layouts[$i]);
					}
				}

			}
		} else {
			
		}
	
// 		$mdb->fetch($i);
		
// 		if ($mdb->dt["depth"] == 0){
// 			$m_string = $m_string.PrintNode($mdb);
// 		}else if($mdb->dt["depth"] == 1){
// 			$m_string = $m_string.PrintGroupNode($mdb);
// 		}else if($mdb->dt["depth"] == 2){
// 			$m_string = $m_string.PrintGroupNode($mdb);
// 		}else if($mdb->dt["depth"] == 3){
// 			$m_string = $m_string.PrintGroupNode($mdb);
// 		}else if($mdb->dt["depth"] == 4){
// 			$m_string = $m_string.PrintGroupNode($mdb);
// 		}
	}
	
		$m_string = $m_string."tree.addNode(rootnode);";
	
		$m_string = $m_string."	</script>
								<form>
								<div id=TREE_BAR style='margin:5px;'>
								<script>		
								tree.draw();
								
								tree.nodes[0].select();
								</script>
								</div>
								</form>";
		
		return $m_string;
	}
	

	
	function PrintRootNode($cname){
		
		$cname = str_replace("\"","&quot;",$cname);
		$cname = str_replace("'","\'",$cname);
		
		$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
				rootnode.expanded = true;\n\n";
		
		return $vPrintRootNode;
	}
	
	
	function PrintNode($layout){
		global $selected_cid;
		$cname = $layout->cname;
		$cid = $layout->cid;
		$depth = $layout->depth;
		$category_display_type = $layout->category_display_type;
	
		$cid1 = substr($cid,0,3);
		$cid2 = substr($cid,3,3);
		$cid3 = substr($cid,6,3);
		$cid4 = substr($cid,9,3);
		$cid5 = substr($cid,12,3);
	
		$cname = str_replace("\"","&quot;",$cname);
		$cname = str_replace("'","\'",$cname);
		
	//	if ($cid == $mcid){
	//		$expandstring = "true";	
	//	}else{
	//		$expandstring = "false";	
	//	}
		
	//	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
		
		if($layout->category_display_type == "P"){
			$mstring =  "		var node$cid = new TreeNode('$cname ', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
		}else if($layout->category_display_type == "B"){
			$mstring =  "		var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_BBS.gif', '../resources/Common_TreeNode_BBS.gif');\n";
		}else if($layout->category_display_type == "F"){
			$mstring =  "		var node$cid = new TreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
		}else{
			$mstring =  "		var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');\n";
		}
		
		$cname = str_replace("'","\'",$cname);
		if(substr($selected_cid,0,($depth+1)*3) == substr($cid,0,($depth+1)*3)){
			$mstring .=  "
			node$cid.expanded = true;
			node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type')\";
			rootnode.addNode(node$cid);\n\n";
		}else{
				$mstring .=  "
			node$cid.expanded = false;
			node$cid.action = \"setCategory('$cname','$cid',$depth, '$category_display_type')\";
			rootnode.addNode(node$cid);\n\n";
		}
		
		return $mstring;
	}
	
	
	function PrintGroupNode($layout)
	{
		$cname = $layout->cname;
		$mcid = $layout->cid;
// 		echo("cid  : " . $mcid);
// 		echo("<br />");
		$depth = $layout->depth;
		$category_display_type = $layout->category_display_type;
		
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
		
		$parent_cid = "$cid1$cid2$cid3$cid4$cid5";
		
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
		
		$cname = str_replace("\"","&quot;",$cname);
		$cname = str_replace("'","\'",$cname);
		
		if($layout->category_display_type == "P"){
			$mstring =  "		var groupnode$mcid = new TreeNode('$cname', '../resources/Common_TreeNode_Note.gif', '../resources/Common_TreeNode_Note.gif');\n";
		}else if($layout->category_display_type == "B"){
			$mstring =  "		var groupnode$mcid = new TreeNode('$cname', '../resources/Common_TreeNode_BBS.gif', '../resources/Common_TreeNode_BBS.gif');\n";
		}else{
			$mstring =  "		var groupnode$mcid = new TreeNode('$cname', TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);\n";
		}
		
		if ($mcid == $cid){
			$mstring .=  "	groupnode$cid.expanded = true;\n";
		//	$mstring .=  "	groupnode$cid.select = true;\n";
		}
		
		$cname = str_replace("'","\'",$cname);
		$mstring .=  "	groupnode$mcid.tooltip = '$cname';
			groupnode$mcid.id ='nodeid$mcid';
			groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth, '$category_display_type')\";
			$ParentNodeCode.addNode(groupnode$mcid);\n\n";
		
		return $mstring;
	}


?>
