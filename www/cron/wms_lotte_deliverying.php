<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/class/wms_lotte_interface.class.php");

$wmsLotteInterface = new WmsLotteInterface();

$wmsLotteInterface->cronDeliverying();