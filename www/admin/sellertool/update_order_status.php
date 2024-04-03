<?
include("../openapi/openapi.lib.php");
include("sellertool.lib.php");

$site_code = $_GET['site_code'];

$result = updateOrdStatus($site_code);
print_r($result);
if(!empty($result)){
    define_syslog_variables();
    openlog("phplog", LOG_PID , LOG_LOCAL0);
    //syslog(LOG_INFO, "제휴사 주문정보 업데이트 -> ".$result);
    closelog();
}
?>
