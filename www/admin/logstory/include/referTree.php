<?php

function GetTreeNode(){
    $mdb = new forbizDatabase;
    $mstring = "
<script>
/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;


/*	Create Root node	*/
	var rootnode = new TreeNode(\"레퍼러분류\", \"../commonimg/ServerMag_Etc_Root.gif\",\"../commonimg/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('레퍼러분류','000000000000000',-1,'','')\";
	rootnode.expanded = true;
";


    $mdb->query("SELECT * FROM ".TBL_LOGSTORY_REFERER_CATEGORYINFO." where depth in(0,1,2,3,4) order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

    $total = $mdb->total;
    for ($i = 0; $i < $mdb->total; $i++)
    {

        $mdb->fetch($i);

        if ($mdb->dt["depth"] == 0){
            $mstring = $mstring.PrintNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$mdb->dt["vkeyword"],$mdb->dt["vparameter"],$mdb->dt["catimg"]);
        }else if($mdb->dt["depth"] == 1){
            $mstring = $mstring.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$mdb->dt["vkeyword"],$mdb->dt["vparameter"],$mdb->dt["catimg"]);
        }else if($mdb->dt["depth"] == 2){
            $mstring = $mstring.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$mdb->dt["vkeyword"],$mdb->dt["vparameter"],$mdb->dt["catimg"]);
        }else if($mdb->dt["depth"] == 3){
            $mstring = $mstring.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$mdb->dt["vkeyword"],$mdb->dt["vparameter"],$mdb->dt["catimg"]);
        }else if($mdb->dt["depth"] == 4){
            $mstring = $mstring.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$mdb->dt["vkeyword"],$mdb->dt["vparameter"],$mdb->dt["catimg"]);
        }else if($mdb->dt["depth"] == 5){
            $mstring = $mstring.PrintGroupNode($mdb->dt["cname"],$mdb->dt["cid"],$mdb->dt["depth"],$mdb->dt["vreferer_url"],$mdb->dt["vkeyword"],$mdb->dt["vparameter"],$mdb->dt["catimg"]);
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

function PrintNode($cname,$cid,$depth,$vreferer='',$vkeyword='',$vparameter='',$catimg='')
{
    $cname = str_replace("\"","&quot;",$cname);
    $cname = str_replace("'","&#39;",$cname);

    $cid1 = substr($cid,0,3);
    $cid2 = substr($cid,3,3);
    $cid3 = substr($cid,6,3);
    $cid4 = substr($cid,9,3);
    $cid5 = substr($cid,12,3);

    return "	var node$cid = new TreeNode('$cname', '../commonimg/Common_TreeNode_CodeManage.gif', '../commonimg/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('$cname','$cid',$depth,'$vreferer','$vkeyword','$vparameter','$catimg')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($cname,$mcid,$depth,$vreferer='',$vkeyword='',$vparameter='',$catimg='')
{
    global $cid;

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

    $expandstring = "true";


    return "		var groupnode$mcid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$mcid.tooltip = '$cname';
		groupnode$mcid.id ='nodeid$mcid';
		groupnode$mcid.expanded = $expandstring;
		groupnode$mcid.action = \"setCategory('$cname','$mcid',$depth,'$vreferer','$vkeyword','$vparameter','$catimg')\";
		$ParentNodeCode.addNode(groupnode$mcid);\n\n";
}
?>
