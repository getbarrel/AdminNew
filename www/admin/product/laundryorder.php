<?
include("../../class/database.class");

$db = new Database;
$depth = $this_depth;
$this_cid = $cid;

$select_level = getlevel($this_cid,$depth);

if ($mode == "up"){
	$target_cid_length = strlen(getTargetCid($depth,$this_cid,$select_level,-1));
}else{
	$target_cid_length = strlen(getTargetCid($depth,$this_cid,$select_level,+1));
}

if ($target_cid_length == 15){
	if ($mode == "up"){
		Pluslevel($this_cid,$depth,$select_level,getTargetCid($depth,$this_cid,$select_level,-1));
	}else{
		Minuslevel($this_cid,$depth,$select_level,getTargetCid($depth,$this_cid,$select_level,+1));
	}
}else{
	echo "<Script Language='JavaScript'>alert(더이상 진행할 방향이 없습니다.)</Script>";		
}

echo "<Script Language='JavaScript'>parent.document.location.href='laundry.php?cid=".$this_cid."&view_depth=$depth';</Script>";	

function getlevel($cid,$depth){
	global $db;
	
	$levelnum = $depth+1;
	$sql = "select vlevel$levelnum as level from shop_laundry_info where depth = $depth and cid = '$cid'";
	$db->query($sql);
	$db->fetch(0);

	return $db->dt['level'];
}

function getTargetCid($depth,$cid,$select_level,$num){
	global $db;

	$sPos = ($depth)*3;
	$levelnum = $depth+1;
	
	if($select_level == 0 && $num < 0){
		return "";
	}

	$sql = "select cid from shop_laundry_info where depth = $depth and vlevel$levelnum = '".($select_level+$num)."' and cid LIKE '".substr($cid,0,$sPos)."%'";
	$db->query($sql);
	if ($db->total){
		$db->fetch(0);
		return $db->dt[cid];
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
		$sql = "UPDATE shop_laundry_info SET vlevel$levelnum = '".($selectlevel)."' where vlevel$levelnum = '".($selectlevel-1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
		$db->query($sql);
	}else{
		$sql = "UPDATE shop_laundry_info SET vlevel$levelnum = '".($selectlevel)."' where vlevel$levelnum = '".($selectlevel-1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
		$db->query($sql);
	}

	$sql = "UPDATE shop_laundry_info SET vlevel$levelnum = '".($selectlevel - 1)."' where vlevel$levelnum = '$selectlevel'  and cid LIKE '".substr($cid,0,$sPos)."%'";

	$db->query($sql);

}

function Minuslevel($cid,$depth,$selectlevel,$target_cid)
{
	global $db;
	
	$sPos = ($depth+1)*3;
	$levelnum = $depth+1;
	
	if ($depth == 0){
		$sql = "UPDATE shop_laundry_info SET vlevel$levelnum = '$selectlevel' where vlevel$levelnum = '".($selectlevel+1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
		$db->query($sql);
	}else{
		$sql = "UPDATE shop_laundry_info SET vlevel$levelnum = '$selectlevel' where vlevel$levelnum = '".($selectlevel+1)."' and cid LIKE '".substr($target_cid,0,$sPos)."%'";
		$db->query($sql);
	}
	
	$sql = "UPDATE shop_laundry_info SET vlevel$levelnum = '".($selectlevel + 1)."' where vlevel$levelnum = '$selectlevel' and cid LIKE '".substr($cid,0,$sPos)."%'";
	$db->query($sql);
}
?>