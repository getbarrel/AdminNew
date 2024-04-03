<?
include("../../class/database.class");

$db = new Database;
$depth = $this_depth;
$this_cid = $cid;
/*	
	$sPos = $depth*3;
	
	if ($depth == 0){
		$sql = "select * from ".TBL_SHOP_ESTIMATE_CATEGORY." where depth = $depth and cid LIKE '".substr($cid,0,$sPos)."%'";
	}else{
		$sql = "select * from ".TBL_SHOP_ESTIMATE_CATEGORY." where depth = $depth and cid LIKE '".substr($cid,0,$sPos)."%'";
	}
		
	echo($sql);
*/	
	$select_level = getlevel($this_cid,$depth);
//	echo"level:".$select_level."<br><br>";
	echo "depth:".($depth)."<br><br>";
	echo "this_cid:".($this_cid)."<br><br>";
	
	if ($mode == "up"){
		$target_cid_length = strlen(getTargetCid($depth,$this_cid,$select_level,-1));
	}else{
		$target_cid_length = strlen(getTargetCid($depth,$this_cid,$select_level,+1));
	}
	
	echo $target_cid_length;
	
	if ($target_cid_length == 15){
		if ($mode == "up"){
//			echo "strlen:".strlen(getTargetCid($depth,$this_cid,$select_level,-1))."<br><br>";
//			echo "tarcagetcid:".getTargetCid($depth,$this_cid,$select_level,-1)."<br>";
			Pluslevel($this_cid,$depth,$select_level,getTargetCid($depth,$this_cid,$select_level,-1));
		}else{
//			echo "strlen:".strlen(getTargetCid($depth,$this_cid,$select_level,+1))."<br><br>";
//			echo "tarcagetcid:".getTargetCid($depth,$this_cid,$select_level,+1)."<br>";
			Minuslevel($this_cid,$depth,$select_level,getTargetCid($depth,$this_cid,$select_level,+1));
		}
	}else{
		echo "<Script Language='JavaScript'>alert('더이상 진행할 방향이 없습니다.')</Script>";			
	}

echo "<Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";	

function getlevel($cid,$depth){
	global $db;
	
	$levelnum = $depth+1;
	$sql = "select vlevel$levelnum from ".TBL_SHOP_ESTIMATE_CATEGORY." where depth = $depth and cid = '$cid'";
//	echo($sql)."<br>";
	$db->query($sql);
	$db->fetch(0);	
	
	return $db->dt[0];
}

function getTargetCid($depth,$cid,$select_level,$num){
	global $db;
	
	$sPos = ($depth)*3;
	$levelnum = $depth+1;
	
	if($select_level == 0 && $num < 0){
		return "";	
	}
	
	$sql = "select cid from ".TBL_SHOP_ESTIMATE_CATEGORY." where depth = $depth and vlevel$levelnum = '".($select_level+$num)."' and cid LIKE '".substr($cid,0,$sPos)."%'";	
//	echo($sql)."<br>";
	$db->query($sql);
//	echo $db->total;
	if ($db->total){
		
		$db->fetch(0);			
		//echo "오나여?".$db->dt[0];
		return $db->dt[0];		
	}else{
		if($num < 0){
			return getTargetCid($depth,$cid,$select_level,$num-1);
		}else if($num > 0){
			return getTargetCid($depth,$cid,$select_level,$num+1);
		}
	}
}

function Pluslevel($cid,$depth,$selectlevel,$target_cid)
{
	global $db;
	
	$sPos = ($depth+1)*3;
	$levelnum = $depth+1;
	
	if ($depth == 0){
		$sql = "UPDATE ".TBL_SHOP_ESTIMATE_CATEGORY." SET vlevel$levelnum = '".($selectlevel)."' where vlevel$levelnum = '".($selectlevel-1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
		$db->query($sql);
	}else{
		//$sql = "UPDATE ".TBL_SHOP_ESTIMATE_CATEGORY." SET vlevel$levelnum = '".($selectlevel)."' where vlevel$levelnum = '".($selectlevel-1)."' and depth = $depth and cid LIKE '".substr($target_cid,0,$sPos)."%'";
		$sql = "UPDATE ".TBL_SHOP_ESTIMATE_CATEGORY." SET vlevel$levelnum = '".($selectlevel)."' where vlevel$levelnum = '".($selectlevel-1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
		$db->query($sql);
	}
//	echo($sql)."<br>";
	
	$sql = "UPDATE ".TBL_SHOP_ESTIMATE_CATEGORY." SET vlevel$levelnum = '".($selectlevel - 1)."' where vlevel$levelnum = '$selectlevel'  and cid LIKE '".substr($cid,0,$sPos)."%'";
	$db->query($sql);
//	echo($sql)."<br>";
}

function Minuslevel($cid,$depth,$selectlevel,$target_cid)
{
	global $db;
	
	$sPos = ($depth+1)*3;
	$levelnum = $depth+1;
	
	if ($depth == 0){
		$sql = "UPDATE ".TBL_SHOP_ESTIMATE_CATEGORY." SET vlevel$levelnum = '$selectlevel' where vlevel$levelnum = '".($selectlevel+1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
		$db->query($sql);
	}else{
		//$sql = "UPDATE ".TBL_SHOP_ESTIMATE_CATEGORY." SET vlevel$levelnum = '$selectlevel' where vlevel$levelnum = '".($selectlevel+1)."' and depth = $depth and cid LIKE '".substr($target_cid,0,$sPos)."%'";
		$sql = "UPDATE ".TBL_SHOP_ESTIMATE_CATEGORY." SET vlevel$levelnum = '$selectlevel' where vlevel$levelnum = '".($selectlevel+1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
		$db->query($sql);
	}
//	echo($sql)."<br>";
	
	$sql = "UPDATE ".TBL_SHOP_ESTIMATE_CATEGORY." SET vlevel$levelnum = '".($selectlevel + 1)."' where vlevel$levelnum = '$selectlevel' and cid LIKE '".substr($cid,0,$sPos)."%'";
	$db->query($sql);
//	echo($sql)."<br>";
}
?>