<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
header("Content-type: text/html; charset=utf-8");

//include("bbs.config.php");

/*
if ($member[mb_level] < $board[bo_download_level]) { 
    $alert_msg = "다운로드 권한이 없습니다.";
    if ($member[mb_id]) {
        alert($alert_msg);
    } else {
        alert($alert_msg . "\\n\\n회원이시라면 로그인 후 이용해 보십시오.", "./?doc=bbs/mblogin.php&wr_id=$wr_id&$qstr&url=".urlencode("./?doc=bbs/gnuboard.php&bo_table=$bo_table&wr_id=$wr_id"));
    }
}
*/

//$file = iconv("CP949","utf-8",$file);
//$subject = conv_subject($write[wr_subject], 255);
//$html_title = "$group[gr_subject] > $board[bo_subject] > " . $subject . " > 다운로드";
//$file = $file_name;
//$filepath = $_SERVER["DOCUMENT_ROOT"]."/".$P->Config[mall_data_root]."/bbs/$bbs_table_name/$bbs_ix/". $file;
$file = urldecode($file_name);
//exit;
//$filepath = $_SERVER["DOCUMENT_ROOT"]."/data/basic/images/cooperation/".$ix."/";
$filepath = $_SERVER["DOCUMENT_ROOT"]."".$admininfo["mall_data_root"]."/images/cooperation/".$ix."/";
$filepath = addslashes($filepath);
$original = $file;

$filepath=$filepath.$original;
//echo $filepath;
//exit;
if ($file && file_exists($filepath)) {
/*$db = new Database();
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
        echo "
			<script language='JavaScript' src='/admin/_language/language.js'></Script>
			<script language='JavaScript' src='/admin/_language/language.php'></Script>
			<Script>alert(language_data['download.php']['C'][language]); history.back();</Script>";//'해당 파일이나 경로가 존재하지 않습니다.'
    }

} else {
	echo "
		<script language='JavaScript' src='/admin/_language/language.js'></Script>
		<script language='JavaScript' src='/admin/_language/language.php'></Script>
		<Script>alert(language_data['download.php']['C'][language]+' $filepath ') ; history.back();</Script>";//파일을 찾을 수 없습니다.
  /*$filepath = $bbs_data_dir."/".$bbs_table_name."/".$bbs_ix."/".iconv("utf-8","CP949",$file);
	$filepath = addslashes($filepath);
	$original = $file;
	if ($file && file_exists($filepath)) {
		$db = new Database();
		$db->query("update ".$bbs_table_name." set bbs_down_cnt = bbs_down_cnt+1 where bbs_ix = '$bbs_ix' ");

			
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
	}*/
}
?>