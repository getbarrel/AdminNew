<?
include("../../class/database.class");

$db = new Database;

if ($mode == "insert"){
$db->query("delete from search_relation where pid ='$pid'");
//$arySearchId = explode(",",$search_id);	
$size = count($search_id);
	for($i=0;$i < $size;$i++){
		if (checkrelation($search_id[$i], $pid)){
			$sql = "insert into search_relation (search_id, pid ,disp) values ('".$search_id[$i]."','$pid','1');";
			$db->query($sql);
		}
	}
	echo "<script language='javascript' src='../js/message.js.php'></script><Script Language='JavaScript'>show_alert(language_data['search_relation.act.php']['A'][language]);</Script>";
	//'정상적으로 입력되었습니다'
}


function checkrelation($msearch_id, $mpid)
{
	global $db;
	$sql = "select * from search_relation where search_id ='$msearch_id' and pid = '$mpid'";
	$db->query($sql);
	
	if ($db->total == 0){
		return true;
	}else{
		return false;
	}
	
	
}

?>