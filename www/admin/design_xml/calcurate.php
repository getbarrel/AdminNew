<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");
//include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("../class/layout.class");
include('./design.common.php');
include "../class/LayoutXml/LayoutXml.class";

// $db = new Database;

function getNextCid($cid,$depth)
{
// 	global $db, $admin_config;
	global $admin_config, $admininfo;
	
	$layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout2.xml";
	$layoutXml = new LayoutXml($layoutXmlPath);
	
	
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

// 	$sPosA = $depth*3;
	$sPos = $depth*3 + 1;
	
	
// 	$xpathString = sprintf("/layouts/layout[substring(@cid, 1, %s)='%s']"
// 			, ($sPos - 1), substr($cid, 0 ,$sPos - 1));

	$xpathString = sprintf("/layouts/layout[substring(@cid, 1, %s)='%s']"
			, ($sPos), substr($cid, 0 ,$sPos));
		
	$results = $layoutXml->simpleXml->xpath($xpathString);
	
	$cids = array();
	$i = 0;
// 	echo("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
// 	echo("\n");
// 	echo($xpathString);
// 	echo("\n");
// 	echo("\n");
	
//   	print_r($results);
	foreach ($results as $layout) {
		$cids[$i] = substr($layout->attributes()->cid, $sPos - 1, 3);
// 		echo($cids[$i]);
// 		echo("<br />");
		$i++;
	}
	$maxCid = max($cids) + 1;
// 	if($admin_config[mall_page_type] == "MI"){
// 		$sql = "select max(substring(cid,$sPos,3))+1 as maxid from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where cid LIKE '".substr($cid,0,$sPos-1)."%'";
// 	}else{
// 		$sql = "select max(substring(cid,$sPos,3))+1 as maxid from ".TBL_SHOP_LAYOUT_INFO." where cid LIKE '".substr($cid,0,$sPos-1)."%'";
// 	}
// 	$db->query($sql);
// 	$db->fetch(0);

	
/*	
	echo $sql."<br>";
	echo $db->dt["maxid"]."<br>";
*/	
	if ($depth + 1 == 1){
		$cid1 = setFourChar($maxCid);	
	}else if ($depth + 1 == 2){
		$cid2 = setFourChar($maxCid);
	}else if ($depth + 1 == 3){
		$cid3 = setFourChar($maxCid);
	}else if ($depth + 1 == 4){
		$cid4 = setFourChar($maxCid);
	}else if ($depth + 1 == 5){
		$cid5 = setFourChar($maxCid);
	}
	
	
// 	echo("xxxxxxxxxxxxx");
// 	exit;
	return "$cid1$cid2$cid3$cid4$cid5";

}


function setFourChar($cid_part)
{
	$chrlen = strlen($cid_part);
	
	$strCid = "$cid_part";
	for($i=0; $i < 3 - $chrlen ; $i++){
		$strCid = "0".$strCid;
	}
	
	return $strCid;
}




function getLayoutPathByAdmin($cid, $depth='-1'){
// 	global $user, $admin_config;
	global $user, $admin_config, $admininfo;

	
	$layoutXmlPath = $_SERVER["DOCUMENT_ROOT"] . $admininfo["mall_data_root"] . "/templet/" . $admin_config["selected_templete"] . "/layout2.xml";
	
	$layoutXml = new LayoutXml($layoutXmlPath);
	
	if($cid == ""){
		return "전체";	
	}

	$mdb = new Database;
// 	if($admin_config[mall_page_type] == "MI"){		
// 		if($depth == '0'){
// 			$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
// 		}else if($depth == '1'){
// 			$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
// 		}else if($depth == '2'){
// 			$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
// 		}else if($depth == '3'){
// 			$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
// 		}else if($depth == '4'){
// 			$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
// 		}else{
// 			$sql = "select * from ".TBL_SHOP_MINISHOP_LAYOUT_INFO." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
// 			//return "";
// 		}
// 	}else{

		switch($depth)
		{
			case '0' :
				$xpathString = sprintf("/layouts/layout[@depth=0 and substring(@pcode, 1, 3) = '%s']", substr($cid,0,3));
				break;
			case '1' :
				$xpathString = sprintf("/layouts/layout[@depth<=1 and ((@depth=0 and substring(@pcode, 1, 3) = '%s') or (@depth=1 and substring(@pcode, 1, 6) = '%s') )]", substr($cid,0,3), substr($cid, 0, 6));
				
				break;
			case '2' :
				$xpathString = sprintf("/layouts/layout[@depth<=1 and ((@depth=0 and substring(@pcode, 1, 3) = '%s') or (@depth=1 and substring(@pcode, 1, 6) = '%s') or (@depth=2 and substring(@pcode, 1, 9) = '%s') )]", substr($cid,0,3), substr($cid, 0, 6), substr($cid, 0, 9));
				break;
			case '3' :
				$xpathString = sprintf("/layouts/layout[@depth<=1 and ((@depth=0 and substring(@pcode, 1, 3) = '%s') or (@depth=1 and substring(@pcode, 1, 6) = '%s') or (@depth=2 and substring(@pcode, 1, 9) = '%s') or (@depth=3 and substring(@pcode, 1, 12) = '%s') )]", substr($cid,0,3), substr($cid, 0, 6), substr($cid, 0, 9), substr($cid, 0, 12));
				break;
			case '4' :
				$xpathString = sprintf("/layouts/layout[@depth<=1 and ((@depth=0 and substring(@pcode, 1, 3) = '%s') or (@depth=1 and substring(@pcode, 1, 6) = '%s') or (@depth=2 and substring(@pcode, 1, 9) = '%s') or (@depth=3 and substring(@pcode, 1, 12) = '%s') or (@depth=4 and substring(@pcode, 1, 15) = '%s') )]", substr($cid,0,3), substr($cid, 0, 6), substr($cid, 0, 9), substr($cid, 0, 12), substr($cid, 0, 15));
				break;
			default :
				return "";
				//$xpathString = "/layouts/layout";
				//break;
		
		
// 		}
// 		if($depth == '0'){
// 			$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
// 		}else if($depth == '1'){
// 			$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
// 		}else if($depth == '2'){
// 			$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
// 		}else if($depth == '3'){
// 			$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
// 		}else if($depth == '4'){
// 			$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
// 		}else{
// 			$sql = "select * from ".TBL_SHOP_LAYOUT_INFO." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
// 			//return "";
// 		}
//echo $xpathString;
		
	}
	$result = $layoutXml->simpleXml->xpath($xpathString);
	
	for($i = 0; $i < count($result); $i++)
	{
		if($i == 0){
			$mstring = $result[$i]->cname;
		} else {
			$mstring .= ">" . $result[$i]->cname;
		}
	}
	
	//echo $sql;
// 	$mdb->query($sql);
	
// 	for($i=0;$i < $mdb->total;$i++){
// 		$mdb->fetch($i);
		
// 		if($i == 0){
// 			$mstring .= $mdb->dt[cname];
// 		}else{
// 			$mstring .= " > ".$mdb->dt[cname];
// 		}	
// 	}
	return $mstring;
}


?>
<?//=getLayoutPathByAdmin($cid,$depth)?>
<Script Language="JavaScript">
parent.document.forms["subCategoryform"].sub_cid.value = "<?=getNextCid($cid,$depth)?>";
parent.document.getElementById('selected_category').innerHTML ="<?=getLayoutPathByAdmin($cid, $depth)?>";
</Script>
