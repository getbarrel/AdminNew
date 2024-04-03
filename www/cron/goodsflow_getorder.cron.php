<?php
/**
 * Created by PhpStorm.
 * User: FB_sc
 * Date: 2019-09-24
 * Time: 오후 8:19
 */

include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/inventory/inventory.lib.php");

getOrderDelivery('goodsflow');