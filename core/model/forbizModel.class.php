<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-02-26
 * Time: 오후 5:18
 */

require_once constant("CORE_ROOT") . "/common/common.class.php";
require_once constant("CORE_ROOT") . "/common/library/database/database.class.php";

class forbizModel extends common
{
    private static $database;
    protected $db;

    public function __construct()
    {
        if (empty(self::$database)) {
            self::$database = new database;
        }
        $this->db = self::$database;
    }
}