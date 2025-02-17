<?
include("../include/admin.util.php");
include("../../class/database.class");

$db = new Database;

function getNextCid($cid,$depth)
{
	global $db;
	$cid1 = substr($cid,0,3);
	$cid2 = substr($cid,3,3);
	$cid3 = substr($cid,6,3);
	$cid4 = substr($cid,9,3);
	$cid5 = substr($cid,12,3);

	$sPosA = $depth*3;
	$sPos = $depth*3 + 1;
	$sql = "select max(substring(cid,$sPos,3))+1 as maxid from ".TBL_SHOP_ESTIMATE_CATEGORY." where cid LIKE '".substr($cid,0,$sPos-1)."%'";
	
	$db->query($sql);
	$db->fetch(0);

/*	
	echo $sql."<br>";
	echo $db->dt["maxid"]."<br>";
*/	
	if ($depth + 1 == 1){
		$cid1 = setFourChar($db->dt[0]);	
	}else if ($depth + 1 == 2){
		$cid2 = setFourChar($db->dt[0]);
	}else if ($depth + 1 == 3){
		$cid3 = setFourChar($db->dt[0]);
	}else if ($depth + 1 == 4){
		$cid4 = setFourChar($db->dt[0]);
	}else if ($depth + 1 == 5){
		$cid5 = setFourChar($db->dt[0]);
	}
	
	
	
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



function getCategoryPathByAdminEstimate($cid, $depth='-1'){
	global $user;
	if($cid == ""){
		return "전체";	
	}
	$mdb = new Database;
	
	if($depth == '0'){
		$sql = "select * from ".TBL_SHOP_ESTIMATE_CATEGORY." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
	}else if($depth == '1'){
		$sql = "select * from ".TBL_SHOP_ESTIMATE_CATEGORY." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
	}else if($depth == '2'){
		$sql = "select * from ".TBL_SHOP_ESTIMATE_CATEGORY." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
	}else if($depth == '3'){
		$sql = "select * from ".TBL_SHOP_ESTIMATE_CATEGORY." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
	}else if($depth == '4'){
		$sql = "select * from ".TBL_SHOP_ESTIMATE_CATEGORY." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
	}else{
		$sql = "select * from ".TBL_SHOP_ESTIMATE_CATEGORY." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
		//return "";
	}
	//echo $depth;
	$mdb->query($sql);
	
	for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);
		
		if($i == 0){
			$mstring .= $mdb->dt[cname];
		}else{
			$mstring .= " > ".$mdb->dt[cname];
		}	
	}
	return $mstring;
}


?>
<?=getNextCid($cid,$depth)?>
<Script Language="JavaScript">
parent.document.forms["subCategoryform"].sub_cid.value = "<?=getNextCid($cid,$depth)?>";
parent.document.getElementById('selected_category').innerHTML ="<?=getCategoryPathByAdminEstimate($cid, $depth)?>";
</Script>