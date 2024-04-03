<?
include("../../class/database.class");
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include("../class/layout.class");

$install_path = "../../include/";
include("SOAP/Client.php");
session_start();
//$admininfo[hostserver] = "b2b.mallstory.com";

$db = new Database;



if ($act == "replace"){
		//print_r($myserver_info);
		//exit;
			foreach ($_POST[myserver_info] as $key => $val) {
				//echo $key."::::".$val."<br>";
				$sql = "REPLACE INTO co_myserver_info set  server_property ='".$key."',server_value='".$val."'  ";
				$db->query($sql);

				$host_serverinfo[$key] = $val;
			}
			
		
			
			$sql = "select * from co_client_hostservers where server_url = '".$_POST["myserver_info"]["server_url"]."' ";
			//echo $sql;
			//exit;
			$db->query($sql);
			if(!$db->total){
				$sql = "update co_client_hostservers set 
					basic='0'  ";
				//echo $sql;
				$db->query($sql);

				$sql = "insert into co_client_hostservers(chs_ix,server_name,server_url,basic,disp,regdate) values('','".$_POST["myserver_info"]["server_name"]."','".$_POST["myserver_info"]["server_url"]."','1','1',NOW()) ";
				//echo $sql;
				//exit;
				$db->query($sql);
			}else{
				$db->fetch();
				$chs_ix = $db->dt[chs_ix];
				$sql = "update co_client_hostservers set 
					server_name='".$_POST["myserver_info"]["server_name"]."',server_url='".$_POST["myserver_info"]["server_name"]."', basic='1', disp='1' 
					where chs_ix='".$chs_ix."' ";
				//echo $sql;
				//exit;
				$db->query($sql);
			}
			//exit;

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>parent.document.location.reload();</script>");
}

/*
create table co_myserver_info (
server_property varchar(32) not null,
server_value varchar(32) not null
primary key(server_property))




*/
?>
