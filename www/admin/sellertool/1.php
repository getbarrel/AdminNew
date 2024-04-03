<?
include_once("sellertool.lib.php");
include_once("../openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");

$key = 500;
$return[$key]["co_oid"]='11';//주문번호
$return[$key]["addr1"]='12';//수취인 주소1
$return[$key]["addr2"]='13';//수취인 주소2
$return[$key]["zip"]='14';//수취인 우편번호
$return[$key]["rname"]='15';//수취인
$return[$key]["rtel"]='16';//수취인 전화번호
$return[$key]["rmobile"]='17';//수취인 핸드폰번호
$return[$key]["msg"]='18';//배송 메모
$return[$key]["btel"]='19';//주문자 전화번호
$return[$key]["bname"]='110';//주문자명
$return[$key]["bmobile"]='111';//주문자 핸드폰번호
$return[$key]["regdate"]='112';//주문번호생성일
$return[$key]["ic_date"]='113';//주문결제완료일
$return[$key]["co_od_ix"]='114';//주문 순번
$return[$key]["pid"]='0000000051';//상품코드
$return[$key]["option_id"]='116';//옵션코드
$return[$key]["option_text"]='117';//옵션명
$return[$key]["f_option_text"]='118';//옵션명
$return[$key]["b_option_text"]='119';//옵션명
$return[$key]["pcnt"]='210';//수량
$return[$key]["psprice"]='121';//상품 판매가(단품)
$return[$key]["pt_dcprice"]='122';//상품 총판매가
$return[$key]["delivery_dcprice"]='213';//배송비


insertOrderInfo('tmall', $return);

exit;


?>


