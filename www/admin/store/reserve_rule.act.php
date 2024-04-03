<?
include("../../class/database.class");
include("../logstory/class/sharedmemory.class");


$data = urlencode(serialize($_POST));
//print_r($_POST);
//echo $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop = new Shared("reserve_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$shmop->setObjectForKey($data,"reserve_rule");

echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');location.href='/admin/store/reserve_rule.php';</script>";
?>