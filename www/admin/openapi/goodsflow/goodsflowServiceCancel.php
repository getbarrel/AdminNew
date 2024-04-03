<?php
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");

$OAL = new OpenAPI('goodsflow');
if($OAL->lib->getServiceCancel($requestKey)){
    $sql = "update shop_goodsflow_info set use_yn = 'N' where company_id='".$company_id."' and requestKey='".$requestKey."'";
    $db->query($sql);
    echo  "<script>top.document.location.reload();</script>";
}else{
    echo  "<script>alert('서비스 취소 신청이 정상적으로 처리가 안되어 있습니다. 계속해서 발생시 관리자에게 문의 바랍니다.');</script>";
}
exit;
