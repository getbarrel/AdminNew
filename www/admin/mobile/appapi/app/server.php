<?php

/*************************************************
 * # HISTORY #
 *
 * # create - 2013/11/04
 *************************************************/
//set_include_path($_SERVER["DOCUMENT_ROOT"]."/include/pear/");

include("./server.class");
include('SOAP/Server.php');
include($_SERVER["DOCUMENT_ROOT"] . "/include/xmlWriter.php");
@include("./lib/product.lib.php");
@include("./lib/encryption.lib.php");
include("../pushService/push.ini.php");
include("../pushService/pushService.class");

include("./class/app.class");
/*
ini_set("user_agent","Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
ini_set("max_execution_time", 0);
ini_set("memory_limit", "10000M");
*/

/*************************************************
 * # 공통 파라미터(common parameter)
 * act => 처리명령어 [필수]
 * result_data_type => 결과데이터타입 [필수아님]
 * -> json <기본>
 * -> xml
 * #디버그 모드
 * debug  => 화면에 결과값을 뿌려준다
 * -> 1
 *************************************************/


//appapi/app/class/App.class
$App = new App;
$headers = apache_request_headers();
$type = $headers['osType'];
if(empty($type)) $type = $headers['Ostype'];

$result_data_type = '';
$debug ='';
$act = '';
$os_type = strtoupper($type);

if(!empty($_REQUEST['debug'])) $debug = $_REQUEST['debug'];
if(!empty($_REQUEST['act'])) $act = $_REQUEST['act'];
if(!empty($_REQUEST['result_data_type'])) $result_data_type = $_REQUEST['result_data_type'];

//syslog(1,"server - ".print_r($_POST,true));
//디버그 모드
if ($debug == "1") {

    if ($result_data_type == "xml") {
        $App->ResultDataType = "xml";
        header("Content-type: text/xml");
    } else {
        echo "<pre>";
    }

    if ($os_type == "I") {
        $App->RequestAppType = "ios";
    }

    $_act = $act;

} else {

    header("Content-type: application/json");

    if ($result_data_type == "xml") {
        $App->ResultDataType = "xml";
    }

    if ($os_type == "I") {
        $App->RequestAppType = "ios";
    }

    $_act = $act;

    $headers = apache_request_headers();

    foreach ($headers as $header => $value) {
        if ($header == "client_lang") {
            $App->Language = $value;
        } else if ($header == "android_id") {
            $App->android_id = $value;
        } else if ($header == "app_name") {
            $App->app_name = $value;
        } else if ($header == "receive_key") {
            $App->receive_key = $value;
        } else if ($header == "app_version") {
            $App->app_version = $value;
        }
    }
}

define_syslog_variables();
openlog("phplog", LOG_PID, LOG_LOCAL0);


switch ($_act) {
    case "test":
        $_RESULT_ = $App->getMainContents();
        break;
    case "getmainsellerlist":
        /*************************************************
         * # 메인인기상점리스트#
         *
         * # 입력 파라미터(input parameter)
         *
         * # 출력 파라미터(output parameter)
         * Array
         * (
         * code => "00" : 성공 , "99" : 오류
         * msg => ""
         * data => Array
         * (
         * store_name => 상점이름
         * store_img => 이미지경로
         * follower_cnt => 관심등록갯수
         * )
         * :
         * :
         * :
         * )
         *************************************************/
        $_RESULT_ = $App->getMainSellerList($_POST);
        break;

    case "getsellerlist":
        /*************************************************
         * # 상점리스트 #
         *
         * # 입력 파라미터(input parameter)
         * start => 리스트 시작 인덱스 [필수아님]
         * -> 0 (기본)
         * max => 리스트 가지고올 갯수 [필수아님]
         * -> 30 (기본)
         * wh_recommend => 인기상점 여부
         * -> Y (기본)
         * orderby => 리스트 노출순서 [필수아님]
         * -> random (기본)
         *
         * # 출력 파라미터(output parameter)
         * Array
         * (
         * code => "00" : 성공 , "99" : 오류
         * msg => ""
         * data => Array
         * (
         * store_name => 상점이름
         * store_img => 이미지경로
         * follower_cnt => 관심등록갯수
         * )
         * :
         * :
         * :
         * )
         *************************************************/
        $_RESULT_ = $App->getSellerList($_POST);
        break;

    case "getbanner":
        /*************************************************
         * # 메인상품리스트 (기존 상품리스트와 같지만 따로 프로모션만 불러올수 있기 때문에 따로 처리) #
         *
         * # 입력 파라미터(input parameter)
         * div => 구분값 [필수아님]
         * -> app_main (메인배너)
         *
         * # 출력 파라미터(output parameter)
         * Array
         * (
         * code => "00" : 성공 , "99" : 오류
         * msg => ""
         * data => Array
         * (
         * img => 이미지경로
         * )
         * :
         * :
         * :
         * )
         *************************************************/
        $_RESULT_ = $App->getBanner($_POST);
        break;

    case "getmaingoods":
        /*************************************************
         * # 메인상품리스트 (기존 상품리스트와 같지만 따로 프로모션만 불러올수 있기 때문에 따로 처리) #
         *
         * # 입력 파라미터(input parameter)
         * div => 구분값 [필수아님]
         * -> popularity or null (인기상품)
         * -> recommend (추천상품)
         * wh_date => 검색조건(날짜) [필수아님] !! 이슈 있음
         * -> today 오늘의인기
         *
         * # 출력 파라미터(output parameter)
         * Array
         * (
         * code => "00" : 성공 , "99" : 오류
         * msg => ""
         * data => Array
         * (
         * pid => 상품시스템코드
         * pname => 상품명
         * sellprice => 판매가
         * img => 이미지경로
         * )
         * :
         * :
         * :
         * )
         *************************************************/
        $_RESULT_ = $App->getMainGoods($_POST);
        break;

    case "getMainContents":
        /*************************************************
         *
         * # 입력 파라미터(input parameter)
         *
         *
         * # 출력 파라미터(output parameter)
         * Array
         * (
         * code => "00" : 성공 , "99" : 오류
         * msg => ""
         * data => Array
         * (
         * [0] => Array
         * (
         * [div_name] => BEST10
         * [banners] => Array
         * (
         * [0] => Array
         * (
         * [name] => test
         * [img] => http://cowell-aws.forbiz.co.kr/data/cowell_data/images/banner/31/4.png
         * [link] => http://cowell-aws.forbiz.co.krhttp://www.naver.com
         * )
         * )
         * )
         * [1] => Array
         * (
         * [div_name] => 맨즈특가
         * [banners] => Array
         * (
         * [0] => Array
         * (
         * [name] => test
         * [img] => http://cowell-aws.forbiz.co.kr/data/cowell_data/images/banner/32/2.png
         * [link] => http://cowell-aws.forbiz.co.kr
         * )
         * )
         * )
         * )
         * :
         * :
         * :
         * )
         *************************************************/
        $_RESULT_ = $App->getMainContents();
        break;

    case "getmaincategorys":
        /*************************************************
         * # 메인 카테고리 리스트에 뿌려지는 카테고리 리스트 (1,2뎁스리스트만 리턴) #
         *
         * # 입력 파라미터(input parameter)
         * 없음
         *
         * # 출력 파라미터(output parameter)
         * Array
         * (
         * code => "00" : 성공 , "99" : 오류
         * msg => ""
         * data => Array
         * (
         * cid => 카테고리아이디
         * cname => 카테고리명
         * depth => 카테고리 깊이
         * vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 => 깊이 정렬값
         * child_category => array(
         * 부모카테고리와 파라미터명 같음
         * ) ;
         * :
         * :
         * :
         * )
         * :
         * :
         * :
         * )
         *
         * -> 예시
         * Array
         * (
         * [code] = "99"
         * [msg] => ""
         * [data] => Array
         * [0] => Array
         * (
         * [cid] => 001000000000000
         * [cname] => TOP&BLOUSE
         * [depth] => 0
         * [vlevel1] => 1
         * [vlevel2] => 0
         * [vlevel3] => 0
         * [vlevel4] => 0
         * [vlevel5] => 0
         * [child_category] => Array
         * (
         * [0] => Array
         * (
         * [cid] => 001001000000000
         * [cname] => 여성상의
         * [depth] => 1
         * [vlevel1] => 1
         * [vlevel2] => 1
         * [vlevel3] => 0
         * [vlevel4] => 0
         * [vlevel5] => 0
         * )
         * :
         * :
         * :
         *************************************************/
        $_RESULT_ = $App->getMainCategorys();
        break;

    case "insertgood":
        /*************************************************
         * # 상품등록 #
         *
         * # 입력 파라미터(input parameter)
         * ?????
         *
         * 그쪽에서 로그인시 가지고 있어야 하는것들
         * code , id , name, company_id
         * $company_id = "ec33f737d1b7255f63aee35a65cd14f1";
         *
         * # 출력 파라미터(output parameter)
         * pid => 상품아이디
         *************************************************/
        $_RESULT_ = $App->insertGood($_POST);
        break;

    case "setPushService":
        /*************************************************
         * # 알림설정 #
         *
         * # 입력 파라미터(input parameter)
         * settype => 입력 삭제 여부
         * ->insert
         * ->delete
         * receive_key => 레지스트 아이디
         * device_id => 단말기 고유값
         * os_type => os 타입( 맨 위에서 처리해준다.)
         * code => 회원코드
         * is_allowable => 마케팅 수신동의 여부
         *
         * # 출력 파라미터(output parameter)
         * {
         * "code":    "00" : 성공
         * "95" : 키값이 없습니다.
         * "msg":"성공적으로 변경되었습니다."
         * }
         *************************************************/

        $push = new pushService($ios_pem, $android_apikey);

        if ($App->RequestAppType == "ios") {
            $con = $push->ios;
        } else {
            $con = $push->android;
        }

        if (!empty($_REQUEST["pushKey"])) {

            $infos["app_div"] = $App->app_name;
            if (empty($infos["app_div"])) {
                $infos["app_div"] = 'webapp';
            }
            $infos["receive_key"] = $_REQUEST["pushKey"];
            $infos["device_id"] = $_REQUEST["deviceId"];
            $infos["user_code"] = $_REQUEST["userCode"];
            $infos["is_allowable"] = $_REQUEST["isAllowed"];

            //APP 최초 설치 쿠폰
            if(!empty($infos["user_code"])){
                if($App->isFirstAppLogin($infos["device_id"])){
                    preferredConditionGiveCoupon('4', $infos["user_code"]);
                }
            }

            if ($_REQUEST['writeType'] == 'insert') {
                $result_data = $con->setRegistId($infos);
            } elseif ($_REQUEST['writeType'] == 'delete') {
                $result_data = $con->deleteRegistId($infos);
            }

            $RESULT["code"] = "00";
            $RESULT["msg"] = "설정이 정상적으로 변경되었습니다.";
        } else {
            $RESULT["code"] = "95";
            $RESULT["msg"] = "키값이 없습니다.";
        }

        $_RESULT_ = $RESULT;
        break;

    case "getPushData":
        $_RESULT_ = $App->getPushData($_REQUEST);
        break;

    case "getAppVer":
        /*************************************************
         *
         * # 앱 버전 체크 API #
         *************************************************/
        $_RESULT_ = $App->getAppVer($os_type);
        break;

    case "setPushYNService":
        /*************************************************
        push_yn => 'Y / N'
        device_id => 단말기 고유값
         *************************************************/

        $_RESULT_ = $App->pushYNSet($_REQUEST);
        break;

    case "login":
        /*************************************************
         * # 로그인 #
         *
         * # 입력 파라미터(input parameter)
         *      id => 아이디
         *      pw => 비밀번호
         *      receive_key => 키값
         *
         * # 출력 파라미터(output parameter)
         *      {
         *          "code":"00", => 01:아이디또는 비밀번호 불일치,02:계정승인 대기중,03:승인 거절
         *          "msg":"",
         *          "data":{
         *              "code":"ea0e17366276c14eb075b486b376227a", => 회원코드
         *              "company_id":"cb06306007b32db862ce70d0487b6ca0", => 회원업체코드
         *              "id":"hong861114", =>아이디
         *              "name":"\ud64d\uc9c4\uc601", => 회원명
         *              "pcs":"010-0000-0000", => 폰번호
         *              "mail":"test@nate.com", => 이메일
         *              "gp_level":"1", => 그룹레벨
         *              "gp_name":"\uc9c1\uc6d0", => 그룹명
         *              "gp_ix":"9", => 그룹코드
         *              "is_delivery_price":"1", => 배송비부과여부
         *              "is_buying_price":"1", => 사입비부과여부
         *              "mem_type":"M", => 회원타입 ('M':일반,'F':외국인,'P':해외거주자,'S':셀러,'A':관리자,'MD':엠디)
         *              "price_view":"1", => 구매가격타입 (0:권장소비자가,1:도매가)
         *              "payment":"S", => 결제가능방법(P:예치금,B:무통장,S:예치금)
         *              "commission_rate":"0", => 상품구매시 구매액의 수수료
         *              "sale_rate":"0" => 회원할인율
         *          }
         *      }
         *************************************************/

        $_RESULT_ = $App->login($_POST);

        //성공일시 회원코드 업데이트
        if ($_RESULT_["code"] == "00") {
            $push = new pushService($push_senderId, $push_apikey);

            if ($App->RequestAppType == "ios") {
                $con = $push->iso;
            } else {
                $con = $push->android;
            }
            
            $infos["app_div"] = $App->app_name;
            $infos["receive_key"] = $_POST["pushKey"];
            $infos["code"] = $_RESULT_["data"]["code"];
            if ($App->RequestAppType != "ios") {
                $con->updateRegistUserCode($infos);
            }
        }
        break;

    case "getsellergoodslist":
        /*************************************************
         * # 상품리스트 #
         *
         * # 입력 파라미터(input parameter)
         *      start => 몇번째번째 부터 상품가지고올 인덱스 0번서부터
         *      max => 가지고올 상품갯수
         *      mem_type => 로그인한 회원 타입
         *      code=> 로그인한 회원코드
         *      com_search_type => 업체 검색 종류 (T:업체명입력,S:업체선택)
         *      com_search_text => 업체 검색어
         *      company_id => 셀러아이디
         *      cid => 카테고리 코드
         *      depth => 카테고리 깊이
         *      search_text => 검색어
         *      sub_search_text => 결과내검색어
         *      lowprice => 낮은가격
         *      highprice => 높은가격
         *      orderby => 리스트정렬 방식
         *          -> regdate [최신순]
         *          -> popularity [인기순]
         *          -> lowprice [저가순]
         *          -> highprice [고가순]
         *
         * # 출력 파라미터(output parameter)
         *      {
         *          "code":"00",
         *          "msg":"",
         *          "total_cnt":"56309",
         *          "data":[
         *              {
         *                  "idx_":1,
         *                  "pid":"0000254390",
         *                  "admin":"9a32b3bfef3bc18f8152a0c9609b9dc2",
         *                  "pname":"[S\/S \uc2e0\uc0c1] \ubb34\ud30c\uc9c4",
         *                  "sellprice":"51000",
         *                  "img":"http:\/\/isoda.co.kr\/data\/isoda_data\/images\/product\/00\/00\/25\/43\/90\/s_0000254390.gif",
         *                  "regdate":"2014-03-21 14:03:33",
         *                  "delivery_policy":"1",
         *                  "wish_cnt":"1",
         *                  "comment_cnt":"0",
         *                  "addr":"APM LUX (\ub7ed\uc2a4)[4 \uce35\/\uc5c6\uc74c\/414]"
         *                  },
         *                  {
         *                  "idx_":2,
         *                  "pid":"0000254386",
         *                  "admin":"5932de7512760cd5204758e8af311688",
         *                  "pname":"[S\/S \uc2e0\uc0c1]\ud558\ud2b8 \ucea1",
         *                  "sellprice":"73100",
         *                  "img":"http:\/\/isoda.co.kr\/data\/isoda_data\/images\/product\/00\/00\/25\/43\/86\/s_0000254386.gif",
         *                  "regdate":"2014-03-21 13:52:56",
         *                  "delivery_policy":"1",
         *                  "wish_cnt":"0",
         *                  "comment_cnt":"0",
         *                  "addr":"\uc720\uc5b4\uc2a4[2 \uce35\/\uc5c6\uc74c\/20]"
         *              }
         *          ]
         *      }
         *************************************************/
        $_POST["seller_goods_list"] = "Y";
        $_RESULT_ = $App->getGoodsList($_POST);
        break;

    case "getgoods":
        /*************************************************
         * # 상품상세내용 #
         *
         * # 입력 파라미터(input parameter)
         *      pid => 상품아이디
         *
         * # 출력 파라미터(output parameter)
         *      {
         *          "code":"00",
         *          "msg":"",
         *          "data":{
         *              "product":{
         *                  "id":"0000099412",
         *                  "pid":"0000099412",
         *                  "pname":"\ubbf8\ub2c8\ub2e8\uac00\ub77c Y",
         *                  "sellprice":"20000",
         *                  "stock":"999999",
         *                  "stock_use_yn":"N",
         *                  "cname":"Cardigan",
         *                  "company_id":"5411d1fef50185acbbbdd8b21257fdf3",
         *                  "search_keyword":"",
         *                  "addr":"\ub514\uc624\ud2b8[1 \uce35\/A\/18]",
         *                  "sns_facebook":"Y",
         *                  "sns_twitter":"Y",
         *                  "delivery_price":0,
         *                  "img":[
         *                      "http:\/\/images.isoda.co.kr\/\/data\/isoda_data\/images\/product\/00\/00\/09\/94\/12\/m_0000099412.gif"
         *                  ],
         *                  "options":[
         *                      {
         *                          "option_name":"size",
         *                          "option_kind":"s",
         *                          "option_detail":[
         *                              {
         *                                  "option_id":"102",
         *                                  "option_div":"FREE"
         *                              }
         *                          ]
         *                      },
         *                      {
         *                          "option_name":"color",
         *                          "option_kind":"s",
         *                          "option_detail":[
         *                              {
         *                                  "option_id":"106",
         *                                  "option_div":"BROWN"
         *                              },
         *                              {
         *                                  "option_id":"103",
         *                                  "option_div":"ORANGE"
         *                              },
         *                              {
         *                                  "option_id":"105",
         *                                  "option_div":"PINK"
         *                              },
         *                              {
         *                                  "option_id":"104",
         *                                  "option_div":"WINE"
         *                              }
         *                          ]
         *                      }
         *                  ],
         *                  "link_url":"http:\/\/isoda.co.kr\/shop\/goods_view.php?id=0000099412",
         *                  "basicinfo":"http:\/\/isoda.co.kr\/admin\/mobile\/iframe_detail.php?id=0000099412",
         *                  "wish_cnt":3
         *              },
         *              "seller":{
         *                  "company_id":"5411d1fef50185acbbbdd8b21257fdf3",
         *                  "store_name":"H2(H2)",
         *                  "store_img":"http:\/\/isoda.co.kr\/data\/isoda_data\/images\/shopimg\/shop_5411d1fef50185acbbbdd8b21257fdf3.gif",
         *                  "follower_cnt":"130",
         *                  "store_desc":"",
         *                  "avg_score":"4.00000",
         *                  "com_email":"",
         *                  "com_phone":"02-2117-4607",
         *                  "kakaotalk_id":"",
         *                  "regdate":"2012.10.25",
         *                  "comment":{
         *                      "code":"96",
         *                      "msg":"\ub4f1\ub85d\ub41c \ub9ac\uc2a4\ud2b8\uac00 \uc5c6\uc2b5\ub2c8\ub2e4.",
         *                      "data":""
         *                  },
         *                  "product_cnt":30
         *              }
         *          }
         *      }
         *************************************************/
        $_RESULT_ = $App->getGoods($_POST);
        break;

    case "orderhistorydetail":
        /*************************************************
         * # 주문상세 #
         *
         * # 입력 파라미터(input parameter)
         *
         *      oid => 주문번호
         *      mem_type => 로그인한 회원타입
         *      company_id => 로그인한 회원 업체 코드
         *
         * # 출력 파라미터(output parameter)
         * {
         *      "code":"00",
         *      "msg":"",
         *      "data":{
         *          "oid":"201402051415-4106", => 주문번호
         *          "regdate":"2014-02-05 14:16:26", => 주문일자
         *          "total_product_price":"79200", => 상품금액합계
         *          "total_delivery_price":"3000", => 배송비합계
         *          "total_buying_price":"4400", => 사입금액합계
         *          "total_pt_price":"86600", => 총주문합계
         *          "rname":"\uc870\uc740\ubcc4", => 받는사람명
         *          "raddr":"[501-090] \uad11\uc8fc \ub3d9\uad6c \uc0b0\uc218\ub3d9537-18\ubc88\uc9c0 406\ud638", => 받는사람주소
         *          "rmobile":"010-2456-3690", => 받는사람연락처
         *          "method":"\uac00\uc0c1\uacc4\uc88c", => 결제수단
         *          "bank_input_name":"\uc870\uc740\ubcc4",
         *          "bank_input_date":"",
         *          "product":[
         *              {
         *                  "idx_":1,
         *                  "img":"http:\/\/isoda.co.kr\/data\/isoda_data\/images\/product\/00\/00\/18\/85\/25\/s_0000188525.gif",
         *                  "pname":"[F\/W \uc2e0\uc0c1]\uc138\ube10 \uc6d0 \ub370\ub2d8 \uc2a4\ucee4\ud2b8\/\uba74",
         *                  "option_text":"option : BLUE_S ",
         *                  "pcnt":"1",
         *                  "psprice":"17600",
         *                  "delivery_msg":""
         *              },
         *              {
         *                  "idx_":2,
         *                  "img":"http:\/\/isoda.co.kr\/data\/isoda_data\/images\/product\/00\/00\/21\/95\/50\/s_0000219550.gif",
         *                  "pname":"[S\/S \uc2e0\uc0c1]\ube48\ud2f0\uc9c0 \uc6cc\uc2f1 \ub370\ub2d8 \ud32c\uce20",
         *                  "option_text":"option : BLUE_S ",
         *                  "pcnt":"1",
         *                  "psprice":"26400",
         *                  "delivery_msg":""
         *              },
         *              {
         *                  "idx_":3,
         *                  "img":"http:\/\/isoda.co.kr\/data\/isoda_data\/images\/product\/00\/00\/22\/63\/46\/s_0000226346.gif",
         *                  "pname":"[S\/S \uc2e0\uc0c1]\ubbf8\ub2c8 \uccb4\ud06c \uc154\uce20",
         *                  "option_text":"option : NAVY_FREE ",
         *                  "pcnt":"1",
         *                  "psprice":"22000",
         *                  "delivery_msg":""
         *              },
         *              {
         *                  "idx_":4,
         *                  "img":"http:\/\/isoda.co.kr\/data\/isoda_data\/images\/product\/00\/00\/22\/63\/25\/s_0000226325.gif",
         *                  "pname":"[S\/S \uc2e0\uc0c1]\uace8 \uc6d0\ud53c\uc2a4",
         *                  "option_text":"option : YELLOW_FREE ",
         *                  "pcnt":"1",
         *                  "psprice":"13200",
         *                  "delivery_msg":""
         *              }
         *          ]
         *      }
         * }
         *************************************************/
        $_RESULT_ = $App->orderHistoryDetail($_POST);
        break;

    case "orderhistory":
        /*************************************************
         * # 거래현황 #
         *
         * # 입력 파라미터(input parameter)
         *
         *      mem_type => 로그인한 회원타입
         *      code    => 로그인한 회원코드
         *      compamy_id => 로그인한 회원 업체 코드
         *      period => 기간 (전체:null,1개월:1,3개월:3~~)
         *
         * # 출력 파라미터(output parameter)
         * {
         *      "code":"00",
         *      "msg":"",
         *      "data":[
         *          {
         *              "idx_":1,
         *              "oid":"201209051933-9783",
         *              "regdate":"2012-09-05",
         *              "pnam        e":"1907_5174 nay sk PT편안한 밴딩웨이스트의 치마바지스판감있는 소재로 편안함의 극대화",
         *              "payment_price":"21340"
         *          },
         *          {
         *              "idx_":2,
         *              "oid":"201209061854-7966",
         *              "regdate":"2012-09-06",
         *              "pname":"호피 SK 외 2건",
         *              "payment_price":"9900"
         *          }
         *      ]
         * }
         *************************************************/
        $_RESULT_ = $App->orderHistory($_POST);
        break;

    case "getcategorys":
        /*************************************************
         * # 부모 카테고리에서 하위 카테고리 리스트 불러오는 함수 #
         *
         * # 입력 파라미터(input parameter)
         * parent_cid => 부모카테고리코드 [필수아님]
         *
         * # 출력 파라미터(output parameter)
         * Array
         * (
         *      code => "00" : 성공 , "99" : 오류
         *      msg => ""
         *      data => Array
         *                  (
         *                      cid => 카테고리아이디
         *                      cname => 카테고리명
         *                      depth => 카테고리 깊이
         *                      vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 => 깊이 정렬값
         *                  )
         *                                  :
         *                                  :
         *                                  :
         * )
         *************************************************/
        $_RESULT_ = $App->getCategorys($parent_cid, $child_cate_yn);
        break;

    case "insertgoods":
        /*************************************************
         * # 상품등록 #
         *
         * # 입력 파라미터(input parameter)
         *  act                 [필수] insertgoods (고정값)
         *  user_data           [필수] 로그인한회원정보 json 파싱
         *  cate1               [필수] 카테고리1
         *  cate2               [필수]카테고리2
         *  cate3               [필수]카테고리3
         *  basicimgfile        [필수]기본이미지
         *  imgfile1 ~ 6        상품이미지
         *  goods_name          [필수] 상품명
         *  coprice             [필수] 원가
         *  wholesaleprice      [필수] 도매가
         *  listprice           [필수] 소매가
         *  delivery_price      배송비
         *  delivery_policy     [필수]상품개별배송사용 (Y or null)
         *  basicinfo           상품상세
         *  option              [필수]옵션 (몰카와 똑같이 보내주시면 됩니다.)
         *
         * # 출력 파라미터(output parameter)
         *  pid => 상품아이디
         *************************************************/

        $_RESULT_ = $App->insertGoods($_POST);
        break;

    case "getsellerprofile":
        /*************************************************
         * # 프로필 정보 [셀러]#
         *
         * # 입력 파라미터(input parameter)
         *
         * code => 회원코드
         *
         * # 출력 파라미터(output parameter)
         * {
         *      "code":"00",
         *      "msg":"",
         *      "data":{
         *          "code":"a0bc5edb2f2d36ace64dcd515ac6697f",
         *          "company_id":"5932de7512760cd5204758e8af311688",
         *          "profile_img":"http://isoda.co.kr/data/isoda_data/images/shopimg/shop_5932de7512760cd5204758e8af311688.gif",
         *          "store_name":"더마지",
         *          "pcs":"01037731354",
         *          "mail":"kbk@forbiz.co.kr",
         *          "kakaotalk_id":"",
         *          "shop_desc":"최신 트랜드를 앞서나는 브랜드!",
         *          "com_name":"더마지(Themarzy)",
         *          "com_ceo":"진정미",
         *          "com_zip":"100-450",
         *          "com_addr1":"서울 중구 신당동 ",
         *          "com_addr2":"251-7 ",
         *          "com_phone":"02-6270-3082",
         *          "age_gruop":"20대 초반,20대 중반,20대 후반",
         *          "shopping_addr":"유어스[2 층/없음/20]",
         *          "com_number":"201-11-15209"
         *      }
         * }
         *************************************************/
        $_RESULT_ = $App->getProfile($_POST, "S");
        break;

    case "getbbslist":
        /*************************************************
         * # 일반게시판 #
         *
         * # 입력 파라미터(input parameter)
         * bbs_name => 게시판테이블명
         * # 출력 파라미터(output parameter)
         * {
         *      "code":"00"
         *      "msg":""
         *      "data":{
         *      "contants":[
         *          {
         *              "idx_":1,
         *              "bbs_ix":"1",
         *              "bbs_subject":"교환 신청 및 교환 상품을 어떻게 해서 보내야 하나요?",
         *              "bbs_contents":"일반적으로 반품접수 후 1~2일(주말,공휴일제외) 또는 3~4일이내에(주말,공휴일제외) 회수 방문 합니다. 상품의 최초 인수 상태 그대로 포장하여 택배 기사로부터 반송장을 받은 후 상품을 건네주시면 됩니다."
         *          },
         *          {
         *              "idx_":2,
         *              "bbs_ix":"2",
         *              "bbs_subject":"배송되지 않아 취소접수를 했는데 배송이 왔어요.",
         *              "bbs_contents":"취소 내역을 확인하지 못하고 출고되는 경우가 간혹 있습니다. 이미 취소하신 상품이므로 택배 기사님 연락 시 인수하지 마시고 수취거부 하시면 됩니다. 만일, 상품을 인수하셨다면 [고객센터 ☎ 070-4322-6522]로 문의주시면 신속하게 회수 처리 해 드립니다. "
         *          }
         *      ]
         *      }
         * }
         *************************************************/
        $_RESULT_ = $App->getBBSList($_POST);
        break;

    default:
        $_RESULT_["code"] = "99";
        $_RESULT_["msg"] = "파라미터 act : " . $_POST["act"] . " 에 대한 처리 내용이 없습니다.";
}

if ($_GET["debug"] == "1") {
    if ($App->ResultDataType == "xml") {
        echo $_RESULT_;
    } else {

        /*
        $_RESULT_ = str_replace("\"true\"","true",json_encode($_RESULT_));
        $_RESULT_ = str_replace("\"false\"","false",$_RESULT_);
        echo json_encode_align($_RESULT_);
        */
        //print_r($_RESULT_);
        $_RESULT_ = str_replace("\"true\"", "true", json_encode($_RESULT_));
        $_RESULT_ = str_replace("\"false\"", "false", $_RESULT_);

        echo $_RESULT_;
    }
} else {

    syslog(LOG_NOTICE, json_encode($_POST));

    $_RESULT_ = str_replace("\"true\"", "true", json_encode($_RESULT_));
    $_RESULT_ = str_replace("\"false\"", "false", $_RESULT_);

    echo $_RESULT_;
}


function json_encode_align($data)
{

    $tab_no = 1;
    $data_array = explode('"', $data);

    foreach ($data_array as $data) {
        if ($data == "{") {
            echo "{<br/>" . for_tab_return($tab_no);
        } elseif (substr_count($data, ',') > 0) {
            if ($data == "},{") {
                echo "<br/>" . for_tab_return($tab_no - 1) . "}<br/><br/>";
                echo for_tab_return($tab_no - 1) . "{<br/>" . for_tab_return($tab_no);
            } else {
                echo $data . "<br/>" . for_tab_return($tab_no);
            }
        } elseif (substr_count($data, '[') > 0) {
            echo ":[<br/>";
            $tab_no++;
            echo for_tab_return($tab_no) . "{<br/>";
            $tab_no++;
            echo for_tab_return($tab_no);
        } elseif (substr_count($data, ']') > 0) {
            $tab_no--;
            echo "<br/>" . for_tab_return($tab_no) . "}";
            $tab_no--;
            echo "<br/>" . for_tab_return($tab_no) . "]";
            $tab_no--;
            echo "<br/>" . for_tab_return($tab_no) . "}";
        } elseif (substr_count($data, '}') > 0) {
            echo str_replace("}", "", $data);
            $tab_no--;
            echo "<br/>" . for_tab_return($tab_no) . "}";
        } else {
            if ($data == ":") {
                echo "" . $data . "";
            } else {
                echo "'" . $data . "'";
            }
        }
    }
}

function for_tab_return($no)
{

    $result = "";

    for ($i = 0; $i < $no; $i++) {
        $result .= "	";
    }

    return $result;
}


closelog();
?>
