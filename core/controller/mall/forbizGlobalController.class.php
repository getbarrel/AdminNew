<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-15
 * Time: 오후 8:32
 */

require_once constant("CORE_ROOT") . "/controller/forbizController.class.php";

class forbizGlobalController extends forbizController
{
    /**
     * js 사용 언어 수집
     */
    public function jsLanguageCollection()
    {
        require_once constant("MALL_ROOT") . "/model/global/language.class.php";
        $languageClass = new language();

        $storage = $this->requestFilter($_POST['storage']);

        $languageClass->jsLanguageCollection($storage);
    }
}
