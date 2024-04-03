<?
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

$shmop = new Shared("coupon_rule");
$shmop->filepath = $path;
$shmop->SetFilePath();
$shmop->setObjectForKey($data,"coupon_rule");

echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
?>