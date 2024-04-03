<?php

/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-02-26
 * Time: 오후 5:25
 */
class forbizController
{
    private $_responseResult = "success";
    private $_responseData;

    /**
     * get response result
     * @return string
     */
    public function getResponseResult()
    {
        return $this->_responseResult;
    }

    /**
     * get response data
     * @return mixed
     */
    public function getResponseData()
    {
        return $this->_responseData;
    }

    /**
     * 필터 (trim)
     * @param $requestData
     * @return array|string
     */
    public function requestFilter($requestData)
    {
        return $this->_trim($requestData);
    }

    /**
     * controller 에서 응답 결과를 set
     * @param $result
     */
    protected function _setResponseResult($result)
    {
        $this->_responseResult = $result;
    }

    /**
     * controller 에서 응답 결과 data 를 set
     * @param $data
     */
    protected function _setResponseData($data)
    {
        $this->_responseData = $data;
    }

    /**
     * 문자열 압뒤 공백 제거
     * @param $data
     * @return array|string
     */
    private function _trim($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = $this->_trim($value);
                } else {
                    $data[$key] = trim($value);
                }
            }
        } else {
            $data = trim($data);
        }
        return $data;
    }
}
