<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/http.class");


$db = new Database();


$sql = "select count(ncnt) as total from logstory_PageViewTime  where  vdate='".date("Ymd")."' limit 1; ";
$db->query($sql);
	


if($db->total){
	$db->fetch();
	echo $db->dt[total];
}else{
	echo 0;
}

	

?>