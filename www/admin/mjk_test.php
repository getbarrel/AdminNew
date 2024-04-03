<?php
include $_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class";

$master_db = new database;

$sql = "insert into db_test (text) values ('111') ";
$master_db ->query($sql);

$master_db->query("SELECT ix FROM db_test WHERE ix=LAST_INSERT_ID()");
								$master_db->fetch();
								$ix = $master_db->dt[ix];
								
 //$add_type_ix = mysql_insert_id(); 
//$add_type_ix = $master_db->query("select LAST_INSERT_ID() from db_test");
$sql = "insert into db_test (text) values ('".$ix."') ";
$master_db ->query($sql);

?>