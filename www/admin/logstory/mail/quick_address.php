<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/

include("./session_management.php");

$filename = $userfolder."_infos/addressbook.ucf";
$myfile = read_file($filename);
if(!empty($myfile)) 
	$addressbook = unserialize(~$myfile);
array_qsort2($addressbook,"name");
$listbox = "<select name=contacts size=10 onDblClick=\"Add('to')\">\r\n";
for($i=0;$i<count($addressbook);$i++) {
	$line = $addressbook[$i];
	$name = htmlspecialchars(trim($line["name"]));;
	$email = htmlspecialchars(trim($line["email"]));
	$listbox .= "<option value=\"&quot;$name&quot; &lt;$email&gt;\"> &quot;$name&quot; &lt;$email&gt;";
}
$listbox .= "</select>";


$tcontent = load_template($quick_address_template,Array("quick-address"));

$tcontent = eregi_replace("<!--%UM_CONTACTS%-->",$listbox,$tcontent);
echo($tcontent);

?>