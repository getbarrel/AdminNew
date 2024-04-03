<?

include("../class/layout.cms.class");

$db = new Database;



$Script="
<script src='../js/prototype.js' type='text/javascript'></script>
<script src='../js/scriptaculous.js' type='text/javascript'></script>
<script>
function setCategory(mode,cname,cid,depth,pid)
{
	//alert(1);
	//outTip(img3);

	parent.getRelationProduct(mode,1, 1,cid,depth);
	//parent.document.frames['act'].location.href='./relationAjax.category.act.php?mode='+mode+'&cid='+cid+'&page='+page; 
}

function CheckOnload(){
	$('loading_zone').style.display = 'none';
	$('category_zone').style.display = 'block';
}

</Script>";

echo"
<html><head>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<META content='MSHTML 6.00.2800.1498' name=GENERATOR></HEAD>
	<title></title>
</head>
<LINK REL='stylesheet' HREF='../include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='../css/design.css' TYPE='text/css'>
".$Script."
<body onload='CheckOnload();'>
<div id='loading_zone' style='width:100%;text-align:center;padding-top:50px;'><img src='/admin/images/indicator.gif'></div>
<div id='category_zone' style='display:none;'>".Category()."</div>

</body>
</html>";



$Script = "<script language='JavaScript' src='../webedit/webedit.js'></script>\n<script Language='JavaScript' src='../include/zoom.js'></script>\n$Script";




function search_select($pid){
$listdb = new Database;

$listdb->query("SELECT m.id,m.myshopping_desc,s.disp  FROM myshopping m left outer join search_relation s on m.id = s.search_id and s.pid = '$pid'");



	$mstr = "	<select name=search_id[] style='height:130px;width:630px;'multiple>";
				
	
	for($i=0;$i < $listdb->total;$i++){
	$listdb->fetch($i);
		if($listdb->dt[disp] == 1){	
			$mstr = $mstr."		<option value='".$listdb->dt[id]."' selected>".($i+1).". ".$listdb->dt[myshopping_desc]." ".$listdb->dt[disp]."</option>";
		}else{
			$mstr = $mstr."		<option value='".$listdb->dt[id]."'>".($i+1).". ".$listdb->dt[myshopping_desc]." ".$listdb->dt[disp]."</option>";
		}
	}		
	
	
	$mstr = $mstr."  </select>";
	
	return $mstr;
	
}



function PrintRootNode($cname){
	
	$cname = str_replace("\"","&quot;",$cname);
	
	$vPrintRootNode = "var rootnode = new TreeNode('$cname', '../resources/ServerMag_Etc_Root.gif','../resources/ServerMag_Etc_Root.gif');/n
			rootnode.expanded = true;\n\n";
	
	return $vPrintRootNode;
}

function PrintNode($cname,$cid,$depth)
{
	global $id;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$cname = str_replace("\"","&quot;",$cname);
	
	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('list','$cname','$cid',$depth,'$id')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($cname,$cid,$parent_group_ix, $depth)
{
	global $id;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$Parentdepth = $depth - 1;
	
	if ($depth+1 == 1){
		$cid1 = "000";
	}else if($depth+1 == 2){
		$cid2 = "000";
	}else if($depth+1 == 3){
		$cid3 = "000";
	}else if($depth+1 == 4){
		$cid4 = "000";
	}else if($depth+1 == 3){
		$cid5 = "000";
	}
	
	$parent_cid = $parent_group_ix;
	
	if ($depth ==2){
		$ParentNodeCode = "node$parent_cid";
	}else if($depth ==3){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==4){
		$ParentNodeCode = "groupnode$parent_cid";
	}

	$cname = str_replace("\"","&quot;",$cname);


	return "		var groupnode$cid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$cid.tooltip = '$cname';
		groupnode$cid.id ='nodeid$cid';
		groupnode$cid.action = \"setCategory('list','$cname','$cid',$depth,'$id')\";
		$ParentNodeCode.addNode(groupnode$cid);\n\n";
}

function Category()
{
	global $id, $admininfo;
	global $db;
	
$cate = "
<script language=\"JavaScript\" src=\"../include/manager.js\"></script>
<script language=\"JavaScript\" src=\"../include/Tree.js\"></script>
<script>

/*	 Create Tree		*/
	var tree = new Tree();
	tree.color = \"black\";
	tree.bgColor = \"white\";
	tree.borderWidth = 0;


/*	Create Root node	*/
	var rootnode = new TreeNode(\"이미지분류\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('이미지분류','000000000000000',-1,0,'".$id."')\";
	rootnode.expanded = true;";
	


//$db->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");
$sql = "select dig.* , case when group_depth = 1 then group_ix  else parent_group_ix end as group_order 
		from deepzoom_image_group dig where company_id ='".$admininfo["company_id"]."' 
		order by  group_order asc , group_ix asc,  vieworder asc";
//echo $sql;
$db->query($sql);

$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{

	$db->fetch($i);
	
	if ($db->dt["group_depth"] == 1){
		$cate = $cate. PrintNode($db->dt["group_name"],$db->dt["group_ix"],$db->dt["group_depth"]);
	}else if($db->dt["group_depth"] == 2){
		$cate = $cate. PrintGroupNode($db->dt["group_name"],$db->dt["group_ix"],$db->dt["parent_group_ix"],$db->dt["group_depth"]);
	}else if($db->dt["group_depth"] == 3){
		$cate = $cate. PrintGroupNode($db->dt["group_name"],$db->dt["group_ix"],$db->dt["parent_group_ix"],$db->dt["group_depth"]);
	}else if($db->dt["group_depth"] == 4){
		$cate = $cate. PrintGroupNode($db->dt["group_name"],$db->dt["group_ix"],$db->dt["parent_group_ix"],$db->dt["group_depth"]);
	}else if($db->dt["group_depth"] == 5){
		$cate = $cate. PrintGroupNode($db->dt["group_name"],$db->dt["group_ix"],$db->dt["parent_group_ix"],$db->dt["group_depth"]);
	}
}

$cate = $cate."	tree.addNode(rootnode);";
$cate = $cate."	
</script>
<form>
<div id=TREE_BAR style=\"margin:5;\">
<script>		
tree.draw();
tree.nodes[0].select();
</script>
</div>
</form>";

return $cate;
}



?>