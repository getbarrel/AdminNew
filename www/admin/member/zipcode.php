<?php
/**
 * Created by PhpStorm.
 * User: moon
 * Date: 2019-03-07
 * Time: 오전 11:06
 */
include $_SERVER["DOCUMENT_ROOT"]."/class/layout.class";


$Html = "";
if(!empty($_SERVER["HTTPS"])){
    $Html .= '<script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>';
}else{
    $Html .= '<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>';
}
$Html .="
<script src='/admin/js/jquery-1.3.2.js'></script>
<script src='/admin/js/daum_zipcode.js'></script>
<div id=\"wrap\" style=\"border:1px solid;width:100%;height:800px;margin:5px 0;\"></div>
<script>
    $(document).ready(function(){
        zipcode_daum_layer('".$_GET['zip_type']."','".$_GET['obj_id']."');
    });
</script>
";
echo $Html;
?>
