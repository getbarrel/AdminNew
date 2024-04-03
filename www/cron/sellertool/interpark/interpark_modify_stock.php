<?
set_time_limit(9999999);

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");

//30분마다!


$OAL = new OpenAPI('interpark_api');
//$pid = '0000101502';

$resulte = $OAL->lib->modifyStock($pid);


?>