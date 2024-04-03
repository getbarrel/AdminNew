<?php
/**
 * Created by PhpStorm.
 * User: FB_sc
 * Date: 2019-07-04
 * Time: 오후 15:54
 * Desc: SG Data Order 생성
 */
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/sgdata/sgdata.class.php");

$erp = new SgERP('');
$erp->execute();
