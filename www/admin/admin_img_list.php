<?
include("../class/layout.class");

	$results = array(); 
	
	$directory = "../admin/images/korea/";

    $handler = opendir($directory); 

    while ($file = readdir($handler)) { 

        if ($file != '.' && $file != '..' && is_dir($file) != '1') {

            $results[] = $file; 

        }

    } 

    closedir($handler); 
	

	for($i=0; $i < count($results); $i++){
		echo "[<img src = '".$directory.$results[$i]."' >]";
	}
	//print_r($results);
?>