<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");
include("../../class/database.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');

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
	$sql = "select max(substring(cid,$sPos,3))+1 as maxid from ".TBL_SNS_CATEGORY_INFO." where cid LIKE '".substr($cid,0,$sPos-1)."%'";

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


?>
<?//=getNextCid($cid,$depth)?>
<Script Language="JavaScript">
parent.document.forms["subCategoryform"].sub_cid.value = "<?=getNextCid($cid,$depth)?>";
parent.document.getElementById('selected_category').innerHTML ="<?=getCategoryPathByAdmin($cid, $depth)?>";
</Script>