<?php
/**
 * Created by PhpStorm.
 * User: FB_sc
 * Date: 2019-07-15
 * Time: 오후 14:09
 * Desc: SG Data Refund Order 생성
 */
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/sgdata/sgdata.class.php");

$erp = new SgERP('R');
$erp->execute();
