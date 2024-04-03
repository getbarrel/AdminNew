<?php
/**
 * Created by PhpStorm.
 * User: FB_sc
 * Date: 2019-06-20
 * Time: 오후 1:44
 */
require 'sgdata.class.php';
$type = '';
$erp = new SgERP($type);

$result = $erp->execute();