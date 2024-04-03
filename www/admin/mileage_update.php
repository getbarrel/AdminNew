<?php
include $_SERVER["DOCUMENT_ROOT"]."/class/database.class";

$db = new database;



$sql = "select mileage,code from common_user where mem_type != 'M' ";

$db->query($sql);
$users = $db->fetchall();

if(is_array($users) && count($users) > 0){
	foreach($users as $key=>$val){
		$sql = "select total_mileage from shop_mileage_log where uid = '".$val['code']."' order by ml_ix desc limit 1 ";
		//echo $sql;
		$db->query($sql);
		
		$db->fetch();
		
		$total_mileage = $db->dt['total_mileage'];
		if(!$total_mileage){
			$total_mileage = 0;
		}
		
		if($total_mileage != $val['mileage']){
			echo $sql;
				$sql = "update common_user set mileage = '".$total_mileage."'  where code = '".$val['code']."'  ";
				echo $sql."<br>";
				$db->query($sql);
		}
	}
}
