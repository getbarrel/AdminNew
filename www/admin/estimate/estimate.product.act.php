<?
include("../../class/database.class");

session_start();

$db = new Database;

if ($act == "relation"){
	
	$sql = "insert into ".TBL_SHOP_ESTIMATE_RELATION." (erid, ecid, pid, disp, regdate) values ";
	$sql .= " ('', '$ecid',  '$pid', 1, NOW()) ";
	$db->query($sql);
	
	//echo $sql;

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력되었습니다.');</script>");
	//echo("<script>location.href = './design.php?page_name=$page_name';</script>");
}


if ($act == "relations"){
	
	for($i=0;$i<count($pid);$i++){
		$sql = "insert into ".TBL_SHOP_ESTIMATE_RELATION." (erid, ecid, pid, disp, regdate) values ";
		$sql .= " ('', '$ecid',  '".$pid[$i]."', 1, NOW()) ";
		
		//echo $sql;
		$db->query($sql);	
	}
	
	
	
	//echo $sql;

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력되었습니다.');</script>");
	//echo("<script>location.href = './design.php?page_name=$page_name';</script>");
}

if ($act == "delete"){
	
	$db->query("DELETE FROM ".TBL_SHOP_ESTIMATE_RELATION." WHERE erid = '".$erid."'");
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 삭제 되었습니다.');</script>");
	echo("<script>parent.setCategory('','$cid','$depth','')</script>");

}

?>
