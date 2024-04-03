<?php
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");

$OAL = new OpenAPI('goodsflow');

$partnerCode = $OAL->lib->getPartnerCode($company_id);
$otp=$OAL->lib->getOTP($partnerCode);
$url= $OAL->lib->getServiceInsertUrl();
?>

<form id="regist_form" name="regist_form" action="<?=$url?>" method="post">
    <input type="hidden" id="OTP" name="OTP" value="<?=$otp?>">
    <input type="hidden" id="responseURL" name="responseURL" value="">
    <input type="hidden" id="bizNo" name="bizNo" value="">
    <input type="hidden" id="mallId" name="mallId" value="">
    <input type="hidden" id="mallName" name="mallName" value="">
    <input type="hidden" id="mallUserName" name="mallUserName" value="">
    <input type="hidden" id="mallUserTel1" name="mallUserTel1" value="">
    <input type="hidden" id="mallUserEmail" name="mallUserEmail" value="">
    <input type="hidden" id="centerCode" name="centerCode" value="<?=$partnerCode?>"> <!-- 발송지코드 있으면 기본값 표시, 고정 (셀러별로 발송지 하나라고 가정하여 처리)-->
    <input type="hidden" id="centerName" name="centerName" value="">
    <input type="hidden" id="centerZipCode" name="centerZipCode" value="">
    <input type="hidden" id="centerAddr1" name="centerAddr1" value="">
    <input type="hidden" id="centerAddr2" name="centerAddr2" value="">
    <input type="hidden" id="centerTel1" name="centerTel1" value="">
    <input type="hidden" id="centerTel2" name="centerTel2" value="">
</form>
<script>document.regist_form.submit();</script>