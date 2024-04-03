<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-20
 * Time: 오후 4:21
 */
//구조를 전부다 바꿀수 없기 때문에 중복 선언 될수 있음
if (!class_exists('Shared')) {
    require_once constant('CORE_ROOT') . "/common/library/extension/sharedmemory.class.php";
}

class shardMemory
{
    private $filepath;

    public function __construct()
    {
        @include($_SERVER["DOCUMENT_ROOT"] . "/mysql_user/" . str_replace("www.", "", $_SERVER["HTTP_HOST"]) . ".php");
        $this->filepath = constant('MALL_ROOT') . "/data/" . $mall_id . "_data/_shared/";
    }

    /**
     * 데이터 가지고 오기
     * @param $div
     * @return mixed
     */
    public function getData($div, $key = '')
    {
        $shared = new Shared($div);
        $shared->filepath = $this->filepath;
        $shared->SetFilePath();
        if (empty($key)) {
            $key = $div;
        }
        $result = $shared->getObjectForKey($key);
        return unserialize(urldecode($result));
    }
}