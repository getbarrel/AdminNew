<?
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

header("Content-type: text/html; charset=utf-8");
//include("bbs.config.php");
session_start();
$file = urldecode($file_name);
$ext=end(explode(".",$file));
//exit;
$filepath = $_SERVER["DOCUMENT_ROOT"].$_SESSION["layout_config"]["mall_data_root"]."/images/of_pop/$popup_ix/".$popup_ix.".".$ext;

$filepath = addslashes($filepath);
$original = $file;
//echo $filepath;

if ($file && file_exists($filepath)) {
/*$db = new MySQL();
$db->query("update ".$bbs_table_name." set bbs_down_cnt = bbs_down_cnt+1 where bbs_ix = '$bbs_ix' ");*/

		
    if(eregi("msie", $_SERVER[HTTP_USER_AGENT]) && eregi("5\.5", $_SERVER[HTTP_USER_AGENT])) {
        header("content-type: doesn/matter");
        header("content-length: ".filesize("$filepath"));
        //header("content-disposition: attachment; filename=".iconv("utf-8","CP949",$original)."");
		header("content-disposition: attachment; filename=".iconv("utf-8","CP949",$original)."");
        header("content-transfer-encoding: binary");
    } else {
        header("content-type: file/unknown");
        header("content-length: ".filesize("$filepath"));
        header("content-disposition: attachment; filename=".iconv("utf-8","CP949",$original)."");
        header("content-description: php generated data");
    }
    header("pragma: no-cache");
    header("expires: 0");
    header("Cache-control: private"); 

    if (is_file("$filepath")) {
    $fp = fopen("$filepath", "rb");
    // 서버부하를 줄이려면 print 나 echo 또는 while 문을 이용한 방법보다는 이방법이...
    if (!fpassthru($fp)) {
        fclose($fp);
    }

    } else {
        echo "<Script>alert('해당 파일이나 경로가 존재하지 않습니다.'); history.back();</Script>";
    }

} else {
	echo "<Script>alert('파일을 찾을 수 없습니다. $filepath ') ; history.back();</Script>";
}
?>
