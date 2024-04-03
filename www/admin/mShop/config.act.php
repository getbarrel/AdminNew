<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");

$rule_data = urlencode(serialize($_POST));
$shmop = new Shared("mobile_config");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey($rule_data,"mobile_config");

echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.location.reload();</script>";
?>