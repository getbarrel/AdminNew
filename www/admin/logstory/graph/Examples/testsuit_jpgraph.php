<?php
// ==========================================================================
// File: 			TESTSUIT_JPGRAPH.PHP
// Created:			2001-02-24
// Last updated: 	13/09/01 01:16 by johanp@aditus.nu
// Description: 	Generate a page with all individual test graphs suitable
//						for visual inspection. Note: This script must be run from
//						the same directory as where all the individual test graphs
//						are.
//						NOTE: Apache/PHP must have write permission to this directory
//						otherwise the source file can't be created.
//	Ver: 				1.1
//
// Notes:	This script can be called with a parameter type=1 or type=2
// 			which controls wheter the images should be in situ on the page (2)
//				or just as a link (1). If not specified defaults to (1).
// ==========================================================================

// Default to 1 if not explicetly specified
if( !isset($type) )
	$type=1;

function GetArrayOfTestGraphs($dp) {
	if( !chdir($dp) )
		die("Can't change to directory: $dir");	
	$d = dir($dp);
	while($entry=$d->read()) {
		if( !strstr($entry,".phps") &&  strstr($entry,".php") && strstr($entry,"x"))
   		$a[] = $entry;
   }
   $d->Close();
   if( empty($a) ) 
   	die("JpGraph Tetsuit Error: Apache/PHP does not have enough permission to read".
   		"the testfiles in directory: $dp");
	return $a;
}

// Copy the real PHP file to a copy with extension PHPS to
// have PHP do color syntax highlightning. We don't use a 
// symlink since that wont work on Windows system
function MakeSourceFiles($flist) {
	foreach($flist as $f) {
		$t=substr($f,0,strlen($f)-3)."phps";
		if( file_exists($t) ) 
			if( !(@unlink($t)) )
				return false;
		if( !@copy($f,$t) ) 
			return false;
		/*
			die("JpGraph Testsuit Error: Failed to copy: $f. Most likely Apache/PHP does not have
			write permission to this directory.");
		*/
	}
	return true;
}	

$tf=GetArrayOfTestGraphs(getcwd());
sort($tf);
$tst=MakeSourceFiles($tf);

echo "<h2>Visual test suit for JpGraph</h2><p>";
echo "Number of tests: ".count($tf)."<p>";
if( $tst )
	echo "<strong>Note:</strong> The script for each graph is visible by clicking on the graph<p>";
else {
	echo "<strong>Note:</strong> Due to isufficient permission the source files can't be automatically";
	echo " created.<p>";
}
	
echo "<ol>";

for($i=0; $i<count($tf); ++$i) {
	switch( $type ) {
		case 1:
			echo "<li><a href=\"".$tf[$i]."\">".substr($tf[$i],0,strlen($tf[$i])-4)."</a>";
			if( isset($showdate) )
				echo "[".date("Y-m-d H:i",filemtime($tf[$i]))."]";
			echo "\n";
			break;
		case 2:
			if( $tst ) {
				echo "<li><a href=\"".substr($tf[$i],0,strlen($tf[$i])-3)."phps\">
					<img src=\"".$tf[$i]."\" border=0 align=top></a>
					<br><strong>Filename:</strong> <i>".substr($tf[$i],0,strlen($tf[$i])-4)."</i><br>&nbsp;";
			}
			else {
				echo "<img src=\"".$tf[$i]."\" border=0 align=top>
				<br><strong>Filename #$i:</strong> <i>".substr($tf[$i],0,strlen($tf[$i])-4)."</i><br>&nbsp;";
			}
			
			if( isset($showdate) )
				echo " [".date("Y-m-d H:i",filemtime($tf[$i]))."]";
			echo "\n<p>";
			break;	
	}		
}
echo "</ol>";
echo "<p>Test suit done.";

/* EOF */
?>