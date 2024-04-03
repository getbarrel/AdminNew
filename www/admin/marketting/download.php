<?
$file = urldecode($file_name);

//$file = iconv("euc-kr","utf-8",$file);
$filepath = "../../data/basic/images/cooperation/".$ix."/".iconv("utf-8","CP949",$file);

$filepath = addslashes($filepath);

$original = $file;

//echo $filepath;
if ($file && file_exists($filepath)) {
	
    if(eregi("msie", $_SERVER[HTTP_USER_AGENT]) && eregi("5\.5", $_SERVER[HTTP_USER_AGENT])) {
		
        header("content-type: doesn/matter");
        header("content-length: ".filesize("$filepath"));
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
        echo "<Script>alert('해당 파일이나 경로가 존재하지 않습니다.')</Script>";
    }

} else {
	//$filepath = "../../data/basic/images/cooperation/".$ix."/".iconv("utf-8","CP949",$file);
	$filepath = "../../data/basic/images/cooperation/".$ix."/".$file;
	$filepath = addslashes($filepath);
	
	$original = $file;
	if ($file && file_exists($filepath)) {
	
			
		if(eregi("msie", $_SERVER[HTTP_USER_AGENT]) && eregi("5\.5", $_SERVER[HTTP_USER_AGENT])) {
			header("content-type: doesn/matter");
			header("content-length: ".filesize("$filepath"));
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
			echo "<Script>alert('해당 파일이나 경로가 존재하지 않습니다.')</Script>";
		}
	}else{
		echo "<Script>alert('파일을 찾을 수 없습니다. $filepath ') ; history.back();</Script>";
	}
}
?>
