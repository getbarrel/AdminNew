<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/




// load the configurations
include("./config.php");
include("./commom_functions.php");
error_reporting (E_ALL ^ E_NOTICE); 
// load the specified file
$tcontent = load_template($bad_login_template,Array("bad-login"));
$tcontent = ereg_replace("<!--%UM_LID%-->",$lid,$tcontent);
// show it :)
echo($tcontent);
?>