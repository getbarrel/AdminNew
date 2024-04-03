<?
	include("../class/layout.class");
	$host	=	"183.111.154.11";
	$id		=	"forbiz";
	$pw		=	"forbizlogin1q2w3e";
	$dbname	=	"tm001";
	$db = new Database($host,$id,$pw,$dbname);
	
	$sql = "
			INSERT INTO 
				customer_info
			(user_id,title,content,sender,sender_alias,receiver_alias,send_time,file_name,wasRead,wasSend,wasComplete,needRetry,regist_date)
			VALUES
			('forbiz','테스트메일','테스트입니다요','holyoneself@hotamil.com','정범기','정범기',now(),'','X','X','X','X',now())
				
	";

	$db->query($sql);
	
	$sql = "
			INSERT INTO 
				customer_data
			(id,email,first)
			VALUES
			(LAST_INSERT_ID(),'holyoneself@naver.com','정범기')
				
	";

	$db->query($sql);

?>