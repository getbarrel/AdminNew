<?
include("../../class/database.class");

session_start();

$db = new Database;



$sql = "select * from common_member_detail  ";
$db->query($sql);
$datas = $db->fetchall();

for($i=0;$i < count($datas);$i++){
	$sql = "select charger_code, position, department from common_member_detail where charger_code = '".$datas[$i][code]."' and charger_ix != '0' ";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		$department = $db->dt[department];
		$position = $db->dt[position];
		$sql = "update common_member_detail set department = '".$department."',  position = '".$position."' where code = '".$datas[$i][code]."'  ";
		$db->query($sql);
		echo $sql."<br>";
	}
}

exit;

?>
