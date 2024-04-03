<?php


//error_reporting(-1);
//ini_set('display_errors', 1);

define('DOC', $_SERVER['DOCUMENT_ROOT']);
include(DOC.'/admin/dd/function.php');

include(DOC.'/admin/dd/IN.class.php');
include(DOC.'/admin/dd/form.php');
include(DOC.'/admin/dd/Mysql_adt.class.php');



function getTpl($name, $array=array(), $return=false)
{

$path = sprintf('%s.php', $name,$ext);

extract($array);


ob_start();

if ( ! file_exists( $path )) {
	die("$path file not found!!");
}

include $path;

if ( true === $return ) {

	$buffer = ob_get_contents();
	@ob_end_clean();
	return $buffer;

}

return $tpl;

}

?>