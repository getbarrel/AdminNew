<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;
$db->query("SELECT text FROM $ctgr WHERE subj IS NOT NULL and no=$no");
$db->fetch();

echo "<Script Language='JavaScript'>parent.document.getElementById('CONTENTS_$no').innerHTML=\"".CheckStr($db->dt[text])."\"</Script>\n";
//echo "<Script Language='JavaScript'>parent.document.getElementById('modify_$no').innerHTML=\"".CheckStrModify($db->dt[text])."\"</Script>";
echo "<Script Language='JavaScript'>parent.document.EDIT_$no.modify_$no.value=\"".CheckStrModify($db->dt[text])."\"</Script>";



function CheckStr($str){
global $html;
	if ($html == 1){
		return str_replace(chr(34),'\\"',str_replace(chr(13).chr(10),' ',$str));
	}else{
		return str_replace(chr(34),'\\"',str_replace(chr(13).chr(10),'<br>',$str));
	}
	
}

function CheckStrModify($str){
global $html;	
	if ($html){
		return str_replace(chr(34),'\\"',str_replace(chr(13).chr(10),' ',$str));
	}else{
		return str_replace(chr(34),'\\"',str_replace(chr(13).chr(10),'\n',$str));
	}
	
}
?> 