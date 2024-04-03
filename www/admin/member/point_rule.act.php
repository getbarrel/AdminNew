<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");
$data = urlencode(serialize($_POST));

//print_r($admininfo);
$path = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";

//echo $path;
if(!is_dir($path)){
	mkdir($path, 0777);
	chmod($path,0777);
}else{
	chmod($path,0777);
}

if($act == "b2c_point"){
$shmop = new Shared("b2c_point_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey($data,"b2c_point_rule");
}else if($act == "b2b_point"){
$shmop = new Shared("b2b_point_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey($data,"b2b_point_rule");
}

echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";

?>