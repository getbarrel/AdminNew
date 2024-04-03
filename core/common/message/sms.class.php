<?php

/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-04-02
 * Time: 오전 10:43
 */
class sms
{
    var $license;
    var $dest_phone;
    var $dest_name;
    var $send_phone;
    var $send_name;
    var $msg_body;
    var $send_result;
    var $send_host;
    var $send_type;
    var $send_date;
    var $send_time;
    var $mms_file;

    function __construct($license)
    {
        if (is_array($license)) {
            $license = implode(",", $license);
        }
        $this->license = $license;
        $this->send_phone = "";
        $this->send_name = "";
        $this->send_type = 0;
        $this->send_date = 0;
        $this->send_time = 0;
        $this->mms_file = "";
        $this->sms_send_type = "";
    }

    /**
     * sms 발송
     * @return bool
     */
    function send()
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/send.php";
        $http->setURL($Requrl); //요청 url

        if ($this->msg_body == "" || $this->dest_phone == "" || $this->send_phone == "") {
            return false;
        }

        if ($this->getSMSAbleCount() > 0) {

            /*_GET 파라미터 값을 global_util 에서 < > 문자열을 변경시키는 구문이 있어서 SMS 발송일 경우에는 해당 값을 다시 변경되어 보내지도록 변경 jk140918*/
            $this->msg_body = str_replace("&gt;", ">", $this->msg_body);
            $this->msg_body = str_replace("&lt;", "<", $this->msg_body);

            $http->setParam("license", $this->license);
            $http->setParam("send_host", $_SERVER['HTTP_HOST']);
            $http->setParam("dest_phone", $this->dest_phone);
            $http->setParam("dest_name", iconv("utf-8", "CP949", $this->dest_name));
            $http->setParam("send_phone", $this->send_phone);
            $http->setParam("msg_body", iconv("utf-8", "CP949", $this->msg_body));
            $http->setParam("send_type", $this->send_type);
            $http->setParam("send_date", $this->send_date);
            $http->setParam("send_time", $this->send_time);
            $http->setParam("msg_code", $this->msg_code);
            $http->setParam("dest_code", $this->dest_code);
            $http->setParam("send_title", $this->send_title);
            for ($i = 0; $i < count($this->mms_file); $i++) {
                $http->setParam("Files[]", "" . $this->mms_file[$i] . "");
            }
            if ($this->sms_send_type) {
                $http->setParam("sms_send_type", $this->sms_send_type);
                $http->setParam("send_name", iconv("utf-8", "CP949", $this->send_name));
            } else {
                $http->setParam("sms_send_type", "A");
            }
            $this->send_result = $http->send("POST");

            return true;
        } else {
            return false;
        }
    }

    /**
     * sms 발송 할 수 있는 갯수
     * @return string
     */
    function getSMSAbleCount()
    {
        global $HTTP_HOST;

        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.count.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS 발송 리스트
    function getSMSProductLogListsUTF8($search_info, $regdate, $code = "")
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.list_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("code", $code);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS 발송 상세 리스트
    function getSMSProductLogDetailUTF8($search_info, $regdate)
    {
        echo "<hr>";
        echo "L : <br>";
        print_r($search_info);
        echo "=-=--";
        print_r($regdate);
        exit;
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.detail_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS/LMS 시간대별 분석
    function getSMSProductLogTimeUTF8($search_info, $regdate)
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.time_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS/LMS 일별 분석
    function getSMSProductLogDayUTF8($search_info, $regdate)
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.day_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS/LMS 월별 분석
    function getSMSProductLogMonthUTF8($search_info, $regdate)
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.month_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS/LMS 기간별별 분석
    function getSMSProductLogPeriodUTF8($search_info, $regdate)
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.period_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS/LMS 종합 분석
    function getSMSProductLogTotalUTF8($search_info, $regdate)
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.total_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS/LMS 자동발송 분석
    function getSMSProductLogAutoUTF8($search_info, $regdate)
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.auto_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS/LMS 자동발송 비용 분석
    function getSMSProductLogAutoPriceUTF8($search_info, $regdate)
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.autoprice_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS/LMS 자동발송 비용 분석
    function getSMSProductLogAutoCodeUTF8($search_info, $regdate)
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.autocode_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS/LMS 총 비용 분석
    function getSMSProductLogChargeUTF8($search_info, $regdate)
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.charge_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS/LMS 건별상세 분석
    function getSMSProductLogCaseUTF8($search_info, $regdate)
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log.case_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS상품 리스트
    function getSMSProductListUTF8()
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.list_utf8.php";
        $http->setURL($Requrl);                              //요청 url

        $http->setParam("license", $this->license);

        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS 보유 현황
    function getSMSStatsUTF8()
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.stats_utf8.php";
        $http->setURL($Requrl);                              //요청 url

        $http->setParam("license", $this->license);

        $send_list_result = $http->send("POST");

        return $send_list_result;
    }

    //SMS충전 리스트
    function getSMSProductRegistListUTF8()
    {
        global $HTTP_HOST;

        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms_point.list_utf8.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);

        return $http->send("POST");
    }

    //SMS리스트 엑셀출력
    function getSMSProductLogListExcelUTF8($search_info, $regdate)
    {
        $http = new Http;
        $Requrl = "http://sms.mallstory.com/sms.log_excel.php";
        $http->setURL($Requrl); //요청 url

        $http->setParam("license", $this->license);
        $http->setParam("regdate", $regdate);
        $http->setParam("search_info", urlencode(serialize($search_info)));
        $http->setParam("page", $page);
        $send_list_result = $http->send("POST");

        return $send_list_result;
    }
}


/**
 * HTTP 소켓 클래스
 */
class Http
{
    var $host;
    var $port;
    var $path;
    var $cookie;
    var $variable;
    var $referer;
    var $_header;
    var $auth;
    var $debug;
    var $query;

    # constructor
    function Http($url = "")
    {
        $this->port = 80;
        if ($url) $this->setURL($url);
    }

    /**
     * URL 지정함수
     *
     * @param string $url : URL
     * @return boolean
     */
    function setURL($url)
    {
        if (!$m = parse_url($url)) return $this->setError("파싱이 불가능한 URL입니다.");
        if ($m['scheme'] != "http") return $this->setError("HTTP URL이 아닙니다.");

        $this->host = $m['host'];
        $this->port = ($m['port']) ? $m['port'] : 80;
        $this->path = ($m['path']) ? $m['path'] : "/";
        if ($m['query']) {
            $arr1 = explode("&", $m['query']);
            foreach ($arr1 as $value) {
                $arr2 = explode("=", $value);
                $this->setParam($arr2[0], $arr2[1]);
            }
        }
        if ($m['user'] && $m['pass']) $this->setAuth($m['user'], $m['pass']);
        return true;
    }

    /**
     * 변수값을 지정한다.
     *
     * @param string $key : 변수명, 배열로도 넣을수 있다.
     * @param string $value : 변수값
     */
    function setParam($key, $value = "")
    {
        if (is_array($key)) foreach ($key as $k => $v) $this->variable[$k] = $v;
        else $this->variable[$key] = $value;
    }

    /**
     * Referer를 지정한다.
     *
     * @param string $referer : Referer
     */
    function setReferer($referer)
    {
        $this->referer = $referer;
    }

    /**
     * 쿠키를 지정한다.
     *
     * @param string $key : 쿠키변수명, 배열로도 넣을수 있다.
     * @param string $value : 쿠키변수값
     */
    function setCookie($key, $value = "")
    {
        if (is_array($key)) foreach ($key as $k => $v) $this->cookie .= "; $k=$v";
        else $this->cookie .= "; $key=$value";
        if (substr($this->cookie, 0, 1) == ";") $this->cookie = substr($this->cookie, 2);
    }

    /**
     * 인증설정함수
     *
     * @param string $id : 아이디
     * @param string $pass : 패스워드
     */
    function setAuth($id, $pass)
    {
        $this->auth = base64_encode($id . ":" . $pass);
    }

    /**
     * POST 방식의 헤더구성함수
     *
     * @return string
     */
    function postMethod()
    {
        if (is_array($this->variable)) {
            $parameter = "\r\n";
            foreach ($this->variable as $key => $val) {
                $parameter .= trim($key) . "=" . urlencode(trim($val)) . "&";
            }
            $parameter .= "\r\n";
        }
        $query .= "POST " . $this->path . " HTTP/1.0\r\n";
        $query .= "Host: " . $this->host . "\r\n";
        if ($this->auth) $query .= "Authorization: Basic " . $this->auth . "\r\n";
        if ($this->referer) $query .= "Referer: " . $this->referer . "\r\n";
        if ($this->cookie) $query .= "Cookie: " . $this->cookie . "\r\n";
        $query .= "User-agent: PHP/HTTP_CLASS\r\n";
        $query .= "Content-type: application/x-www-form-urlencoded\r\n";
        $query .= "Content-length: " . strlen($parameter) . "\r\n";
        if ($parameter) $query .= $parameter;
        $query .= "\r\n";
        return $query;
    }

    /**
     * GET 방식의 헤더구성함수
     *
     * @return string
     */
    function getMethod()
    {
        if (is_array($this->variable)) {
            $parameter = "?";
            foreach ($this->variable as $key => $val) {
                $parameter .= trim($key) . "=" . urlencode(trim($val)) . "&";
            }
            //$parameter = substr($parameter, 0, -1);
        }
        $query = "GET " . $this->path . $parameter . " HTTP/1.0\r\n";
        $query .= "Host: " . $this->host . "\r\n";
        if ($this->auth) $query .= "Authorization: Basic " . $this->auth . "\r\n";
        if ($this->referer) $query .= "Referer: " . $this->referer . "\r\n";
        if ($this->cookie) $query .= "Cookie: " . $this->cookie . "\r\n";
        $query .= "User-agent: PHP/HTTP_CLASS\r\n";
        $query .= "\r\n";
        return $query;
    }

    /**
     * 데이타 전송함수
     *
     * @param string $mode : POST, GET 중 하나를 입력한다.
     * @return string
     */
    function send($mode = "GET")
    {

        // 웹서버에 접속한다.
        $fp = fsockopen($this->host, $this->port, $errno, $errstr, 10);
        if (!$fp) return $this->setError($this->host . "로의 접속에 실패했습니다.");

        // GET, POST 방식에 따라 헤더를 다르게 구성한다.
        if (strtoupper($mode) == "POST") $this->query = $this->postMethod();
        else $this->query = $this->getMethod();

        fputs($fp, $this->query);

        // 헤더 부분을 구한다.
        $this->_header = ""; // 헤더의 내용을 초기화 한다.
        while (trim($buffer = fgets($fp, 1024)) != "") {
            $this->_header .= $buffer;
        }

        // 바디 부분을 구한다.
        while (!feof($fp)) {
            $body .= fgets($fp, 1024);
        }

        // 접속을 해제한다.
        fclose($fp);

        return $body;
    }

    /**
     * 헤더를 구하는 함수
     *
     * @return string
     */
    function getHeader()
    {
        return $this->_header;
    }

    /**
     * 쿠키값을 구하는 함수
     *
     * @param string $key : 쿠키변수
     * @return string or array
     */
    function getCookie($key = "")
    {
        if ($key) {
            $pattern = "/" . $key . "=([^;]+)/";
            if (preg_match($pattern, $this->_header, $ret)) return $ret[1];
        } else {
            preg_match_all("/Set-Cookie: [^\n]+/", $this->_header, $ret);
            return $ret[0];
        }
    }

    function setError($messge)
    {
        echo $message;
    }

}

?>