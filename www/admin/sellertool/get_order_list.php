<?
include("../openapi/openapi.lib.php");
include("sellertool.lib.php");

//크론으로 돌려야 할 페이지 한 5분마다 돌려서 가져오면 될듯.
//사이트 코드는 등록된 제휴사를 가져오려했으나 굿스 때문에 일단 수동으로 작업.
$site_code = $_GET['site_code'];

//제휴사쪽 결제완료 목록을 받아서 결제테이블에 넣는 작업만 필요한것. 있는건지 아닌지는 해당 제휴사코드 + 주문번호를 유니크값으로 쳐서 확인
//여기선 날짜계산해서 함수 호출만 하면 될듯?

$startDate = date('YmdHi',strtotime("-7 day"));
$endDate = date('YmdHi');

$result = getOrderList($site_code,$startDate,$endDate);
print_r($result);

if(!empty($result)){
    define_syslog_variables();
    openlog("phplog", LOG_PID , LOG_LOCAL0);
    //syslog(LOG_INFO, "제휴사 주문정보 가져오기 -> ".$result);
    closelog();
}
?>