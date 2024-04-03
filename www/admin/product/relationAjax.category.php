<?

include($_SERVER["DOCUMENT_ROOT"]."/shop/common/util.php");
include("../class/layout.class");



//echo exif_imagetype("/home/simpleline/www/data/simpleline/images/product/basic_1705.gif");

$db = new Database;


function PrintOptionName($pid, $select_opn_ix)
{
	$mdb = new Database;
	$mdb->query("SELECT * FROM ".TBL_SHOP_PRODUCT_OPTIONS." where pid ='$pid'");

	$SelectString = "<Select name='opn_ix' onchange=\"ChangeOptionName('$pid', this);\">";

	if ($mdb->total){
			$SelectString = $SelectString."<option value=''>옵션이름 선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			$SelectString = $SelectString."<option value='".$mdb->dt[opn_ix]."' option_kind='".$mdb->dt[option_kind]."'>".$mdb->dt[option_name]."</option>";
		}
	}else{
	$SelectString = $SelectString."<option value=''> 옵션이 없습니다.</option>";
	}

	$SelectString = $SelectString."</Select>";

	return $SelectString;
}



$Script="
<script src='../js/prototype.js' type='text/javascript'></script>
<script src='../js/scriptaculous.js' type='text/javascript'></script>
<script>
function setCategory(mode,cname,cid,depth,pid)
{ 
	parent.ms_productSearch._getProductList(mode,1, 1,cid,depth,'','".$search_type."');
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



$Script = "<script language='JavaScript' src='../webedit/webedit.js'></script>\n<!--script Language='JavaScript' src='../include/zoom.js'></script-->\n$Script";





function PrintRootNode($cname){

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","\'",$cname);

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
	$cname = str_replace("'","\'",$cname);

	return "	var node$cid = new TreeNode('$cname', '../resources/Common_TreeNode_CodeManage.gif', '../resources/Common_TreeNode_CodeManage.gif');
	node$cid.expanded = true;
	node$cid.action = \"setCategory('list','$cname','$cid',$depth,'$id')\";
	rootnode.addNode(node$cid);\n\n";
}

function PrintGroupNode($cname,$cid,$depth)
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

	$parent_cid = "$cid1$cid2$cid3$cid4$cid5";

	if ($depth ==1){
		$ParentNodeCode = "node$parent_cid";
	}else if($depth ==2){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==3){
		$ParentNodeCode = "groupnode$parent_cid";
	}else if($depth ==4){
		$ParentNodeCode = "groupnode$parent_cid";
	}

	$cname = str_replace("\"","&quot;",$cname);
	$cname = str_replace("'","\'",$cname);


	return "		var groupnode$cid = new TreeNode('$cname',TREE_FOLDER_CLOSED_IMG,TREE_FOLDER_OPEN_IMG);
		groupnode$cid.tooltip = '$cname';
		groupnode$cid.id ='nodeid$cid';
		groupnode$cid.action = \"setCategory('list','$cname','$cid',$depth,'$id')\";
		$ParentNodeCode.addNode(groupnode$cid);\n\n";
}

function Category()
{
	global $id;
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
	var rootnode = new TreeNode(\"상품카테고리\", \"../resources/ServerMag_Etc_Root.gif\",\"../resources/ServerMag_Etc_Root.gif\");
	rootnode.action = \"setCategory('상품카테고리','000000000000000',-1,0,'".$id."')\";
	rootnode.expanded = true;";



$db->query("SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");

$total = $db->total;
for ($i = 0; $i < $db->total; $i++)
{

	$db->fetch($i);

	if ($db->dt["depth"] == 0){
		$cate = $cate. PrintNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 1){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 2){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 3){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
	}else if($db->dt["depth"] == 4){
		$cate = $cate. PrintGroupNode($db->dt["cname"],$db->dt["cid"],$db->dt["depth"]);
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

function PrintRelation($pid){
	global $db;

	$sql = "select c.cid,c.cname,c.depth, r.rid  from ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." c where pid = '$pid' and c.cid = r.cid ";


	$db->query($sql);

	$mString = "<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver style='font-size:10px;'>";
	$mString .= "<tr align=center bgcolor=gray height=2><td colspan=5></td></tr>";
	$mString .= "<tr align=center bgcolor=#efefef height=25><td class='table_td small'>번호</td><td class='table_td small'>카테고리 ID</td><td class='table_td small'>카테고리</td><td class='table_td small'>삭제</td></tr>";
	if ($db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=21><td colspan=4 align=center>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr height=25 bgcolor=#ffffff><td class='table_td_white small' align=center>".($i+1)."</td><td class='table_td_white small'>".$db->dt[cid]."</td><td class='table_td_white small'>".($parent_cname != "" ? $parent_cname.">":"").$db->dt[cname]."</td><td class=table_td_white align=center><a href=\"JavaScript:deleteCategory('delete','".$db->dt[rid]."','$pid')\"><img src='../image/btc_del.gif' border=0></a></td></tr>";
			$mString .= "<tr height=1><td colspan=5 background='../image/dot.gif'></td></tr>";
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";

	return $mString;
}


?>