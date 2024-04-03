<?php
/**
 * Created by PhpStorm.
 * User: FB_sc
 * Date: 2019-11-06
 * Time: 오후 3:49
 */
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/sgdata/sgdata.class.php");

$erp = new SgERP('');
$erp->invoiceProcess();