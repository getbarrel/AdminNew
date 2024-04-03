<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/



// load configs
include("./config.php");
include("./commom_functions.php");
error_reporting (E_ALL ^ E_NOTICE); 

// load template
$tcontent = load_template($error_template,Array("error"));
// replace the vars in template
$tcontent = eregi_replace("<!--%UM_SID%-->",$sid,$tcontent);
$tcontent = eregi_replace("<!--%UM_LID%-->",$lid,$tcontent);
$tcontent = eregi_replace("<!--%UM_ERROR%-->",$msg,$tcontent);
// show result
echo($tcontent);
?>