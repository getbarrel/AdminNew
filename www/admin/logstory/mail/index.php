<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/

error_reporting (E_ALL ^E_NOTICE);
include("./config.php");
include("./commom_functions.php");

//$sid = "{".uniqid("")."-".uniqid("")."-".time()."}";
$tcontent = load_template($login_template,Array("login"));

$jssource = "
<script language=javascript>
function select_language(lid) {
	location = 'index.php?lid='+lid+'&f_user='+escape(document.forms[0].f_user.value)
}
</script>
";

$tcontent     = eregi_replace("<!--%UM_JS%-->",$jssource,$tcontent);

//$tcontent     = eregi_replace("<!--%UM_SID%-->",strtoupper($sid),$tcontent);

$tcontent     = eregi_replace("<!--%UM_F_USER%-->",htmlspecialchars($f_user),$tcontent);

$startvar = strpos($tcontent,"<!--%UM_VARIABLE_SERVER_BEGIN%-->");
$endvar = strpos($tcontent,"<!--%UM_VARIABLE_SERVER_END%-->")+31;

$startstat = strpos($tcontent,"<!--%UM_STATIC_SERVER_BEGIN%-->");
$endstat = strpos($tcontent,"<!--%UM_STATIC_SERVER_END%-->")+29;

$aval_servers = count($pop3_servers);

if($aval_servers != 0) {
	// fixed server
	$correct = substr($tcontent,$startstat+31,$endstat-$startstat-60);
	if ($aval_servers == 1) {
		$tmp = "@".$pop3_servers[0]["domain"]." <input type=hidden name=six value=0>";
		$correct = eregi_replace("<!--%UM_SERVERS%-->",$tmp,$correct);
	} else {
		$tmp = "<select name=six>\r";
		for($i=0;$i<$aval_servers;$i++)
			$tmp .= "<option value=$i>@".$pop3_servers[$i]["domain"]."\r";
		$tmp .= "</select>\r";
		$correct = eregi_replace("<!--%UM_SERVERS%-->",$tmp,$correct);
	}
} else {
	$correct = substr($tcontent,$startvar+33,$endvar-$startvar-64);
	//variable server
}
$tcontent = substr($tcontent,0,$startvar).$correct.substr($tcontent,$endstat);


$avallangs = count($themes);
if($avallangs == 0) die("You must provide at least one language");


$tmp   = get_tags("<!--%UM_LANGUAGE_BEGIN%-->","<!--%UM_LANGUAGE_END%-->",$tcontent);
if(!empty($allow_user_change)) {
	$cleantext = $tmp["re-content"];
	$def_lng = (is_numeric($lid))?$lid:$default_language;
	$langsel = "<select name=lng onChange=\"select_language(this.options[this.selectedIndex].value)\">\r";
	for($i=0;$i<$avallangs;$i++) {
		$selected = ($lid == $i)?" selected":"";
		$langsel .= "<option value=$i$selected>".$themes[$i]["name"]."\r";
	}
	$langsel .= "</select>\r";
	$cleantext = eregi_replace("<!--%UM_LANGUAGES%-->",$langsel,$cleantext);
	$tcontent = substr($tcontent,0,$tmp["ab-begin"]).$cleantext.substr($tcontent,$tmp["ab-end"]);
} else 
	$tcontent = substr($tcontent,0,$tmp["ab-begin"]).substr($tcontent,$tmp["ab-end"]);
unset($tmp);

echo($tcontent);
