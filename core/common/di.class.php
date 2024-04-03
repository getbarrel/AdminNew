<?php

/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-12
 * Time: 오후 5:27
 */
class di
{
    public $util;
    public $protocol;
    public $log;
    public $cookie;
    public $aes128;
    public $shardMemory;
    public $file;

    /**
     * util constructor.
     * 변수로 이용하여 class 선언 하려고 하였으나 phpstom 에서 autocomplete 기능이 안되서 코딩함
     * require_once ($_SERVER ['DOCUMENT_ROOT'] . "/class/model/core/util/" . $className . ".class");
     * $this->$className = new $className();
     */
    public function __construct()
    {
        $argList = func_get_args();
        $utilPath = constant('CORE_ROOT') . "/common/util";
        foreach ($argList as $className) {
            if ($className == "util") {
                require_once($utilPath . "/util.class.php");
                $this->util = new util();
            } else if ($className == "protocol") {
                require_once($utilPath . "/protocol.class.php");
                $this->protocol = new protocol();
            } else if ($className == "log") {
                require_once($utilPath . "/log.class.php");
                $this->log = new log();
            } else if ($className == "cookie") {
                require_once($utilPath . "/cookie.class.php");
                $this->cookie = new cookie();
            } else if ($className == "aes128") {
                require_once($utilPath . "/aes128.class.php");
                $this->aes128 = new aes128();
            } else if ($className == "shardMemory") {
                require_once($utilPath . "/shardMemory.class.php");
                $this->shardMemory = new shardMemory();
            } else if ($className == "file") {
                require_once($utilPath . "/file.class.php");
                $this->file = new file();
            }
        }
    }
}