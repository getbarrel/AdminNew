<?php

/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-12
 * Time: 오후 6:24
 */
class log
{
    private $path;
    private $div;
    //private $fh; //file handle
    private $txt;

    /**
     * log constructor.
     * file 컨트롤 -> error_log 함수로 변경 추후 개선해야함
     */
    public function __construct()
    {
        //include_once($_SERVER["DOCUMENT_ROOT"]."/mysql_user/".str_replace("www.","",$_SERVER["HTTP_HOST"]).".php");
        //$mall_id;
        // 이슈 ㅜㅜ 보안 떄문에 경로 수정해야 하나??
        //$mall_id = "omnichannel";
        //$this->path = $_SERVER ['DOCUMENT_ROOT'] . "/data/".$mall_id."_data/_logs";
        $this->path = str_replace("/www", "/_logs", constant('CORE_ROOT'));
        $this->div = "";
        //$this->fh = "";
        $this->txt = "";
        $this->_mkdir($this->path);
    }

    /**
     * 열기
     * 구분지어진 폴더 안으로 날짜별로 생성
     * div = test, test/20160301.log(현재일자) 로 생성
     * @param $div 구분
     */
    public function open($div)
    {
        $this->div = $div;
        $this->_mkdir($this->path . "/" . $this->div);
        $this->txt = "---------- start[" . getmypid() . "] " . date('Y-m-d H:i:s') . " ----------" . "\r\n";
    }

    /**
     * 쓰기
     * @param $txt
     */
    public function write($txt)
    {
        if (is_array($txt)) {
            $this->txt .= print_r($array, true) . "\r\n";
        } else {
            $this->txt .= $txt . "\r\n";
        }
    }

    /**
     * 닫기
     * write 에서 담은 txt 내용을 error_log 함수를 이용하여 기록 남김
     */
    public function close()
    {
        //error_log($this->txt, 3, "/var/log/".$this->div."/".date('Ymd'));
        error_log($this->txt, 3, $this->path . "/" . $this->div . "/" . date('Ymd') . ".log");
        $this->txt = "";

        /*
        $log_dir = "/home/www/data/log";
        $log_file = fopen($log_dir . "/log.txt", "a");
        fwrite($log_file, $log_txt . "\r\n");
        fclose($log_file);
        */
    }

    /**
     * directory 체크해서 생성
     * @param $path
     */
    private function _mkdir($path)
    {
        if (!is_dir($path)) {
            mkdir($path);
        }
    }
}