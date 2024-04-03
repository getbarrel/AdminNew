<?php
/**
 * Created by PhpStorm.
 * User: FB_sc
 * Date: 2019-06-18
 * Time: 오후 6:50
 */

require 'sabangnet.class.php';

$sbn = new Sabangnet();
$sbn->setPath('/barrel_data');
$data = array();
$result = $sbn->execute('insertProduct', $data);

print_r($result);