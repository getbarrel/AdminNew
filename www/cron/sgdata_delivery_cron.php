<?php
/**
 * Created by PhpStorm.
 * User: FB_sc
 * Date: 2019-10-08
 * Time: 오후 4:09
 */
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/sgdata/sgdata.class.php");

$erp = new SgERP('');
$erp->deliveryProcess();