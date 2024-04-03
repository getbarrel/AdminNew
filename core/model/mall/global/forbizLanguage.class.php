<?php
/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-13
 * Time: 오후 6:20
 */

require_once constant("CORE_ROOT") . "/model/mall/global/forbizGlobal.class.php";

class forbizLanguage extends forbizGlobal
{
    protected $callKeyList = array();
    protected $datas;
    protected $language;

    public function __construct()
    {
        parent::__construct();
        $this->datas = $this->_getDatas();
    }

    /**
     * key가 없을 경우 basicStr를 key로 이용하여 언어 세팅, key 에 해당하는 언어 없을경우 basicStr로 return
     * @param $basicStr
     * @param string $key
     * @param array $changeStr
     * @return mixed
     */
    public function trans($basicStr, $key = '')
    {
        if (empty($key)) {
            $key = $this->_getKey($basicStr);
        }
        if (constant('ENVIRONMENT') == 'development') {
            $this->_updateCallCount($key);
        }
        $transData = $this->_getTransData($key);
        $returnStr = (!empty($transData) ? $transData : $basicStr);

        $searchList = $this->_getSearchData($returnStr);
        if (is_array($searchList) && count($searchList) > 0) {
            $assignList = $this->_getReplaceData();
            foreach ($searchList as $search) {
                $replaceKey = str_replace(array('[', ']'), '', $search);
                if (array_key_exists($replaceKey, $assignList)) {
                    $returnStr = str_replace($search, $assignList[$replaceKey], $returnStr);
                }
            }
        }
        return $returnStr;
    }

    /**
     * js에 사용하는 언어 수집
     * storage[common.validation.required.text][key]=bdaeb176b9b69ac1c24395d13d47aba0
     * storage[common.validation.required.text][text]=[title]을/를 입력해 주세요.
     * @param $storage
     */
    public function jsLanguageCollection($storage)
    {
        if (is_array($storage)) {
            foreach ($storage as $tag => $language) {
                $this->_updateCallCount($language['key']);
            }
        }
    }

    /**
     * string -> md5
     * @param $str
     * @return string
     */
    protected function _getKey($str)
    {
        return md5($str);
    }

    /**
     *
     * @param $key
     * @return mixed
     */
    protected function _getTransData($key)
    {
        return $this->datas[$key];
    }

//    protected function getUseLanguage()
//    {
//        $language = $_SESSION["layout_config"]["front_language"];
//        if(empty($_SESSION["layout_config"]["front_language"])){
//            $language = 'korean';
//        }
//        return $language;
//    }

    /**
     * @return mixed
     */
    protected function _getDatas()
    {
        global $_LANGUAGE;
        return $_LANGUAGE;
    }

    /**
     * 언어 호출시 call_cnt +1
     * @param $key
     */
    protected function _updateCallCount($key)
    {
        if (!in_array($key, $this->callKeyList)) {
            $this->callKeyList[] = $key;
            $this->db->query("update global_translation set call_cnt = ifnull(call_cnt,0) + 1 where trans_key = '" . $key . "' ");
        }
    }

    /**
     * 치환해야할 [key] 패턴 찾기
     * @param $basicStr
     * @return mixed
     */
    protected function _getSearchData($basicStr)
    {
        preg_match_all("/\[[^\]]*\]/i", $basicStr, $matches);
        return $matches[0];
    }

    /**
     * tpl_ 에 assign 되어 있는 데이터 get
     * @param $basicStr
     * @return mixed
     */
    protected function _getReplaceData()
    {
        return $GLOBALS['tpl']->var_[$GLOBALS['tpl']->_current_scope];
    }
}