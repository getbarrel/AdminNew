<?php

function GetTreeNode($LinkPage,$vdate,$mode="referer", $depth = 3){


    $mstring = "
<script>
/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;\n
	";



    if($mode == "referer"){
        $mdb = new forbizDatabase;
        $mstring .="
/*	Create Root node	*/
	var rootnode = new TreeNode(\"레퍼러분류\", \"../commonimg/ServerMag_Etc_Root.gif\",\"../commonimg/ServerMag_Etc_Root.gif\");
	rootnode.action = \"alert(language_data['ReportReferTree.php']['A'][language]);\"; //ViewTreeReport('$LinkPage', '$vdate','$cname','$cid',$depth)
	rootnode.expanded = true;\n\n";//'아래 카테고리를 선택하세요'

        $mdb->query("SELECT * FROM ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where depth < $depth order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");
        //$mdb->query("SELECT * FROM ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where depth in(0,1,2,3) order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

        $total = $mdb->total;
        for ($i = 0; $i < $mdb->total; $i++)
        {

            $mdb->fetch($i);

            if ($mdb->dt["depth"] == 0){
                $mstring = $mstring.PrintNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 1){
                $mstring = $mstring.PrintGroupNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 2){
                $mstring = $mstring.PrintGroupNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 3){
                $mstring = $mstring.PrintGroupNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 4){
                $mstring = $mstring.PrintGroupNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 5){
                $mstring = $mstring.PrintGroupNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }
        }
    }else if($mode == "product"){
        $mdb = new CommerceDatabase;
        $mstring .="
/*	Create Root node	*/
	var rootnode = new TreeNode(\"상품분류\", \"../commonimg/ServerMag_Etc_Root.gif\",\"../commonimg/ServerMag_Etc_Root.gif\");	
	rootnode.action = \"ViewTreeReport('$LinkPage', '$vdate','$cname','$cid',-1)\";
	rootnode.expanded = true;\n\n";

        $mdb->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." where depth in(0,1,2,3) order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");


        $total = $mdb->total;
        for ($i = 0; $i < $mdb->total; $i++)
        {

            $mdb->fetch($i);

            if ($mdb->dt["depth"] == 0){
                $mstring = $mstring.PrintNode($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 1){
                $mstring = $mstring.PrintGroupNode($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 2){
                $mstring = $mstring.PrintGroupNode($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 3){
                $mstring = $mstring.PrintGroupNode($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 4){
                $mstring = $mstring.PrintGroupNode($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 5){
                $mstring = $mstring.PrintGroupNode($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }
        }

    }else{
        $mdb = new forbizDatabase;
        $mstring .="
/*	Create Root node	*/
	var rootnode = new TreeNode(\"검색엔진별 키워드\", \"../commonimg/ServerMag_Etc_Root.gif\",\"../commonimg/ServerMag_Etc_Root.gif\");
	rootnode.action = \"ViewTreeReport('$LinkPage', '$vdate','$cname','$cid',$depth)\";
	rootnode.expanded = true;\n\n";

        $mdb->query("SELECT * FROM ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where depth in(0, 1,2,3,4,5) and cid LIKE '000001%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");
//	$mdb->query("SELECT * FROM ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where depth in(0,1,2,3) order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

        $total = $mdb->total;
        for ($i = 0; $i < $mdb->total; $i++)
        {

            $mdb->fetch($i);

            if ($mdb->dt["depth"] == 1){
                $mstring = $mstring.PrintNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 2){
                $mstring = $mstring.PrintGroupNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 3){
                $mstring = $mstring.PrintGroupNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 4){
                $mstring = $mstring.PrintGroupNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 5){
                $mstring = $mstring.PrintGroupNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }else if($mdb->dt["depth"] == 6){
                $mstring = $mstring.PrintGroupNode2($LinkPage,$mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$vdate);
            }
        }

    }

    $mstring = $mstring."
	tree.addNode(rootnode);
</script>
<script>		
tree.draw();
tree.nodes[0].select();
</script>
";

    return $mstring;
}





function PrintRootNode($cname){

    $cname = str_replace("\"","&quot;",$cname);
    $cname = str_replace("'","&#39;",$cname);

    $vPrintRootNode = "var rootnode = new TreeNode('$cname', '../commonimg/ServerMag_Etc_Root.gif','../commonimg/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";

    return $vPrintRootNode;
}

function PrintNode($LinkPage,$cname,$cid,$depth,$vreferer='',$vdate){
    global $_GET;
    $cid1 = substr($cid,0,3);
    $cid2 = substr($cid,3,3);
    $cid3 = substr($cid,6,3);
    $cid4 = substr($cid,9,3);
    $cid5 = substr($cid,12,3);


    $cname = str_replace("\"","&quot;",$cname);
    $cname = str_replace("'","&#39;",$cname);

    return "	var node$cid = new TreeNode('$cname', '../commonimg/Common_TreeNode_CodeManage.gif', '../commonimg/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"ViewTreeReport('$LinkPage', '$vdate','$cname','$cid',$depth,'".$_GET['search_sdate']."','".$_GET['search_edate']."');\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintNode2($LinkPage,$cname,$cid,$depth,$vreferer='',$vdate){
    global $_GET;
    $cid1 = substr($cid,0,3);
    $cid2 = substr($cid,3,3);
    $cid3 = substr($cid,6,3);
    $cid4 = substr($cid,9,3);
    $cid5 = substr($cid,12,3);

    $cname = str_replace("\"","&quot;",$cname);
    $cname = str_replace("'","&#39;",$cname);

    return "	var node$cid = new TreeNode('$cname', '../commonimg/Common_TreeNode_CodeManage.gif', '../commonimg/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"ViewTreeRefererReport('$LinkPage', '$vdate','$cname','$cid',$depth,'".$_GET['search_sdate']."','".$_GET['search_edate']."');\";
	rootnode.addNode(node$cid);\n\n";
}


function PrintGroupNode($LinkPage,$cname,$mcid,$depth,$vreferer='',$vdate){
    global $_GET;
    $cname = str_replace("\"","&quot;",$cname);
    $cname = str_replace("'","&#39;",$cname);

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

    if ($cid == $mcid){
        $expandstring = "true";
    }else{
        $expandstring = "false";
    }


    return "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
	//	groupnode$cid.expanded = $expandstring;
		groupnode$mcid.action = \"ViewTreeReport('$LinkPage', '$vdate','$cname','$mcid',$depth,'".$_GET['search_sdate']."','".$_GET['search_edate']."')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
}

function PrintGroupNode2($LinkPage, $cname,$mcid,$depth,$vreferer='',$vdate=""){
    global $_GET;
    $cname = str_replace("\"","&quot;",$cname);
    $cname = str_replace("'","&#39;",$cname);

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
    }else if ($depth ==2){
        $ParentNodeCode = "groupnode$parent_cid";
    }else if($depth ==3){
        $ParentNodeCode = "groupnode$parent_cid";
    }else if($depth ==4){
        $ParentNodeCode = "groupnode$parent_cid";
    }else if($depth ==5){
        $ParentNodeCode = "groupnode$parent_cid";
    }else if($depth ==6){
        $ParentNodeCode = "groupnode$parent_cid";
    }

    if ($cid == $mcid){
        $expandstring = "true";
    }else{
        $expandstring = "false";
    }


    return "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
	//	groupnode$cid.expanded = $expandstring;
		groupnode$mcid.action = \"ViewTreeRefererReport('$LinkPage', '$vdate','$cname','$mcid',$depth,'".$_GET['search_sdate']."','".$_GET['search_edate']."')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
}
?>