<?php
/**
 * Created by PhpStorm.
 * User: FB_sc
 * Date: 2019-10-08
 * Time: 오전 11:28
 */

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/sgdata/sgdata.class.php");

$erp = new SgERP('D');
$erp->execute();