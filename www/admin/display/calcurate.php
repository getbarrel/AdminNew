<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");
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
	$sql = "select max(substr(cid,$sPos,3))+1 as maxid from shop_content_class where cid LIKE '".substr($cid,0,$sPos-1)."%'";
	
	$db->query($sql);
	$db->fetch(0);

/*	
	echo $sql."<br>";
	echo $db->dt["maxid"]."<br>";
*/	
	if ($depth + 1 == 1){
		$cid1 = setFourChar($db->dt[maxid]);	
	}else if ($depth + 1 == 2){
		$cid2 = setFourChar($db->dt[maxid]);
	}else if ($depth + 1 == 3){
		$cid3 = setFourChar($db->dt[maxid]);
	}else if ($depth + 1 == 4){
		$cid4 = setFourChar($db->dt[maxid]);
	}else if ($depth + 1 == 5){
		$cid5 = setFourChar($db->dt[maxid]);
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

	if('<?=$mode?>' == ''){
		parent.document.forms["subContentform"].sub_cid.value = "<?=getNextCid($cid,$depth)?>";
	}

	if('<?=$depth?>' == '0'){
		parent.document.getElementById('selected_content_1').innerHTML = "";
		if('<?=$mode?>' == ''){
			parent.document.getElementById('selected_content_2').innerHTML = "";
		}
	}else{
		parent.document.getElementById('selected_content_1').innerHTML = "<?=getContentPathByAdmin($cid, $depth)?>";
		if('<?=$mode?>' == ''){
			parent.document.getElementById('selected_content_2').innerHTML = "<?=getContentPathByAdmin($cid, $depth)?>";
		}
	}

</Script>