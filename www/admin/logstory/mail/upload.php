<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/

include("./session_management.php");

echo($nocache);
if (isset($rem) && !empty($rem)) {

	$attchs = $sess["attachments"];
	@unlink($attchs[$rem]["localname"]);
	unset($attchs[$rem]);
	$c = 0;
	$newlist = Array();
	while(list($key,$value) =  each($attchs)) {
		$newlist[$c] = $value; $c++;
	}
	$sess["attachments"] = $newlist;
	save_session($sess);
	echo("
	<script language=javascript>\n
		if(window.opener) window.opener.doupload();\n
		setTimeout('self.close()',500);\n
	</script>\n
	");

} elseif (is_uploaded_file($userfile)) {

	if(!is_array($sess["attachments"])) $ind = 0;
	else $ind = count($sess["attachments"]);
	$filename = $userfolder."_attachments/".md5(uniqid("")).$userfile_name;
    move_uploaded_file($userfile, $filename);

	$sess["attachments"][$ind]["localname"] = $filename;
	$sess["attachments"][$ind]["name"] = $userfile_name;
	$sess["attachments"][$ind]["type"] = $userfile_type;
	$sess["attachments"][$ind]["size"] = $userfile_size;

	save_session($sess);

	echo("
	<script language=javascript>\n
		if(window.opener) window.opener.doupload();\n
		setTimeout('self.close()',500);\n
	</script>\n
	");

} else {

	$tcontent = load_template($attach_window_template,Array("upload"));
	$tcontent = eregi_replace("<!--%UM_SID%-->",$sid,$tcontent);
	echo($tcontent);

}
?>
