<?php
if(strstr($_SERVER['HTTP_HOST'], '.forbiz.co.kr')) {
    defined('FRONT_WEB_URL') OR define('FRONT_WEB_URL', 'http://barrelfrontdev.forbiz.co.kr');
} else {
    defined('FRONT_WEB_URL') OR define('FRONT_WEB_URL', 'http://barrelfrontdev.devs');
}
