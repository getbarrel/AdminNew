<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
if($di_ix){
	//$localfilepath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/cms/".$di_ix."/".iconv('UTF-8','EUC-KR',$data_file);
	$localfilepath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/cms/".$di_ix."/".$data_file;
}else if($di_ix){
	$localfilepath = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/cms/".$di_ix."/".iconv('UTF-8','EUC-KR',$data_file);
}
//echo $localfilepath;
//echo file_exists($localfilepath);
//exit;
$localfilepath = addslashes($localfilepath);
$vfile = split("/",$data_file);
$original = $vfile[count($vfile)-1];

if ($data_file && file_exists($localfilepath)) {
    if(eregi("msie", $_SERVER[HTTP_USER_AGENT]) && eregi("5\.5", $_SERVER[HTTP_USER_AGENT])) {
        header("content-type: doesn/matter");
        header("content-length: ".filesize("$localfilepath"));
        header("content-disposition: attachment; filename=".iconv('UTF-8','EUC-KR',$original));
        header("content-transfer-encoding: binary");
    } else {
        header("content-type: file/unknown");
        header("content-length: ".filesize("$localfilepath"));
        header("content-disposition: attachment; filename=".iconv('UTF-8','EUC-KR',$original));
        header("content-description: php generated data");
    }
    header("pragma: no-cache");
    header("expires: 0");

    if (is_file("$localfilepath")) {
        $fp = fopen("$localfilepath", "rb");
        // 서버부하를 줄이려면 print 나 echo 또는 while 문을 이용한 방법보다는 이방법이...
        if (!fpassthru($fp)) {
            fclose($fp);
        }

    } else {
        echo "<Script>alert('해당 파일이나 경로가 존재하지 않습니다.')</Script>";
    }

} else {
    echo "<Script>alert('파일을 찾을 수 없습니다. $localfilepath ')</Script>";
}
?>
