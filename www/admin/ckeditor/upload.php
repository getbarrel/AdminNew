<?php
session_start();

/**
 * uuid 추가 2013-04-18 bgh
 */
function uuid($serverID=1)
{
    $t=explode(" ",microtime());
    return sprintf( '%04x-%08s-%08s-%04s-%04x%04x',
        $serverID,
        clientIPToHex(),
        substr("00000000".dechex($t[1]),-8),   // get 8HEX of unixtime
        substr("0000".dechex(round($t[0]*65536)),-4), // get 4HEX of microtime
        mt_rand(0,0xffff), mt_rand(0,0xffff));
}
function clientIPToHex($ip="") {
    $hex="";
    if($ip=="") $ip=getEnv("REMOTE_ADDR");
    $part=explode('.', $ip);
    for ($i=0; $i<=count($part)-1; $i++) {
        $hex.=substr("0".dechex($part[$i]),-2);
    }
    return $hex;
}

if(isset($_SESSION["admin_config"]["mall_data_root"])) {
    $up_url = $_SESSION["admin_config"]["mall_data_root"]."/images/upfile/";
    $up_dir = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/upfile/";
    // 업로드 DIALOG 에서 전송된 값
    $funcNum = $_GET['CKEditorFuncNum'] ;
    $CKEditor = $_GET['CKEditor'] ;
    $langCode = $_GET['langCode'] ;


    if(isset($_FILES['upload']['tmp_name']))
    {
        $file_name = $_FILES['upload']['name'];
        $ext = strtolower(substr($file_name, (strrpos($file_name, '.') + 1)));

        if ('jpg' != $ext && 'jpeg' != $ext && 'gif' != $ext && 'png' != $ext)
        {
            echo '이미지만 가능'.$up_url;
            return false;
        }
        //$file_id = md5($_FILES["upload"]["tmp_name"] + rand()*100000);
        $file_id = uuid();
        $save_dir = sprintf('%s/%s', $up_dir, $file_id.".".$ext);
        $save_url = sprintf('%s/%s', $up_url, $file_id.".".$ext);

        if (move_uploaded_file($_FILES["upload"]["tmp_name"],$save_dir))
            echo "<script>try{document.domain='njoyny.com';}catch(e){};window.parent.CKEDITOR.tools.callFunction($funcNum, '$save_url', '업로드완료');</script>";
    }
}
