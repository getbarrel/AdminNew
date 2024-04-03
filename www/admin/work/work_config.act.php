<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include_once("work.lib.php");

session_start();

$db = new Database;


if ($act == "update"){

	$db->query("delete from work_config where charger_ix = '".$admininfo[charger_ix]."' ");
	foreach ($_POST as $key => $val) {		
		if($key != "x" && $key != "y" && $key != "act"){
			
			
			$sql = "REPLACE INTO work_config set charger_ix='".$admininfo[charger_ix]."' , conf_name='".$key."',conf_val='".serialize($val)."'  ";
			$db->query($sql);

			$work_confs[$key] = $val;
			//echo $sql."<br>";
		}

	}
	
	$admininfo["work_confs"] = $work_confs;
	session_register("admininfo");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('환경설정이 정상적으로 저장되었습니다..');parent.document.location.reload();</script>");
	
}


if ($act == "op_update"){

	
	foreach ($_POST as $key => $val) {		
		if($key != "x" && $key != "y" && $key != "act"){
			$sql = "select * from work_op_config  where company_id = '".$admininfo[company_id]."' and conf_name='".$key."' " ;
			//$sql = "REPLACE INTO work_op_config set company_id='".$admininfo[company_id]."', conf_name='".$key."',conf_val='".$val."'  ";
			$db->query($sql);
			if($db->total){
				$sql = "update work_op_config set conf_val='".$val."' where company_id = '".$admininfo[company_id]."' and conf_name='".$key."' " ;
				//$sql = "REPLACE INTO work_op_config set company_id='".$admininfo[company_id]."', conf_name='".$key."',conf_val='".$val."'  ";
				$db->query($sql);
			}else{
				$sql = "insert into work_op_config(company_id,conf_name,conf_val) values('".$admininfo[company_id]."','".$key."','".$val."')" ;
				//$sql = "REPLACE INTO work_op_config set company_id='".$admininfo[company_id]."', conf_name='".$key."',conf_val='".$val."'  ";
				$db->query($sql);
			}
			//$work_op_confs[$key] = $val;
			//echo $sql."<br>";
		}

	}
	$db->query("select conf_name, conf_val from work_op_config where company_id = '".$admininfo[company_id]."' ");
	//echo $db->total;
	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
		//echo $db->dt[conf_name];
		$work_op_confs[$db->dt[conf_name]] = $db->dt[conf_val];
	}
	//print_r($work_op_confs);
	//exit;
	$admininfo["work_op_confs"] = $work_op_confs;
	session_register("admininfo");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('운영설정이 정상적으로 저장되었습니다..');parent.document.location.reload();</script>");
	
}

/*


CREATE TABLE `work_config` (
  wcf_ix int(8) unsigned NOT NULL AUTO_INCREMENT,
  charger_ix int(8) DEFAULT NULL,
  conf_name varchar(50) DEFAULT NULL,
  conf_val varchar(255),
  regdate datetime DEFAULT NULL,
  PRIMARY KEY (wcf_ix)
) TYPE=MyISAM DEFAULT CHARSET=utf8


*/

?>