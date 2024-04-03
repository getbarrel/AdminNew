<?


$filepath = $_SERVER["DOCUMENT_ROOT"]."/admin/design/makefile.php";
//$filepath = addslashes($filepath);
$original = $path.".view.php";
//echo $filepath;
if ($original && file_exists($filepath)) {

    if(eregi("msie", $_SERVER[HTTP_USER_AGENT]) && eregi("5\.5", $_SERVER[HTTP_USER_AGENT])) {
        header("content-type: doesn/matter");
        header("content-length: ".filesize("$filepath"));
        header("content-disposition: attachment; filename=$original");
        header("content-transfer-encoding: binary");
    } else {
        header("content-type: file/unknown");
        header("content-length: ".filesize("$filepath"));
        header("content-disposition: attachment; filename=$original");
        header("content-description: php generated data");
    }
    header("pragma: no-cache");
    header("expires: 0");

    if (is_file("$filepath")) {
	    $fp = fopen("$filepath", "rb");
	    
	    while (!feof($fp)) {
	            echo str_replace("{cid}",$cid,fread($fp, 3048));
	    }
    } else {
        echo "<Script>alert('해당 파일이나 경로가 존재하지 않습니다.')</Script>";
    }

} else {
    echo "<Script>alert('파일을 찾을 수 없습니다. $filepath ') ; history.back();</Script>";
}
?>
