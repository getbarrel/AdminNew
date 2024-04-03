<?php

/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-12
 * Time: 오후 5:48
 */
class cookie
{
    /**
     * 생성
     * @param $key
     * @param $value
     * @param $expire
     * @param bool $secure
     */
    public function set($key, $value, $expire, $secure = false)
    {
        setcookie($key, $value, time() + $expire, "/", $_SERVER['HTTP_HOST'], $secure, true);
    }

    /**
     * 삭제
     * @param $key
     * @param bool $secure
     */
    public function delete($key, $secure = false)
    {
        setcookie($key, '', time() - 3600, "/", $_SERVER['HTTP_HOST'], $secure, true);
    }
}