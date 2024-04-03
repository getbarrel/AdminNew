<?php

/**
 * Created by PhpStorm.
 * User: Hong
 * Date: 2018-03-12
 * Time: 오후 6:36
 */
class protocol
{
    protected $protocol;

    /**
     * @return 'https' or 'http'
     */
    public function getProtocol()
    {
        if (empty($this->protocol)) {
            $this->_setProtocol();
        }
        return $this->protocol;
    }

    /**
     * set protocol
     */
    protected function _setProtocol()
    {
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] == 443
        ) {
            $this->protocol = 'https';
        } else {
            $this->protocol = 'http';
        }
    }
}