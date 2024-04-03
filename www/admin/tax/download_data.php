<? 
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;

	$idx = $_GET[idx];
	$SQL = "SELECT * FROM tax_datafile WHERE idx = '$idx'";
	$db->query($SQL);
	$db->fetch();

	$my_file_name = $db->dt[file_rename];
	$realname = $db->dt["file"];
	$my_real_name = rawurldecode($realname);
	$my_file_name = rawurldecode($my_file_name); 
	$Path="/home/dev/www/admin/tax/file/$my_file_name"; 

	if (is_file($Path)) { 
		Header("Content-type:application/octet-stream"); 
		Header("Content-Length:".filesize($Path));    
		Header("Content-Disposition:attachment;filename=".iconv('utf-8','euc-kr',$my_real_name)); 
		Header("Content-type:file/unknown"); 
		Header("Content-Description:PHP3 Generated Data"); 
		Header("Pragma: no-cache"); 
		Header("Expires: 0"); 

		$fp = fopen($Path, "rb");    
		if (!fpassthru($fp)) fclose($fp); 
		clearstatcache(); 
	} 
?>