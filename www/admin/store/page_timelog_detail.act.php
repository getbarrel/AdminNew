<?
include("../class/layout.class");


$db = new Database;

if($act == 'update'){
	$sql = "update logstory_admin_page_log set status = '$status',worker_ip = '".$_SERVER['REMOTE_ADDR']."',worker_name = '$worker_name', update_date=NOW() where apl_ix = '$apl_ix' ";
	$db->query($sql);
	
	if($mode == 'pop'){
		echo("<script>alert('정상적으로 수정되었습니다.');opener.document.location.reload();self.close();</script>");
	}else{
		echo("<script>alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
	}

	exit;
}

if($act == 'delete'){
	
	$sql = "delete from logstory_admin_page_log where apl_ix = '$apl_ix'";
	$db->query($sql);
	
	if($mode == 'pop'){
		echo("<script>alert('정상적으로 삭제되었습니다.');opener.document.location.reload();self.close();</script>");
	}else{
		echo("<script>alert('정상적으로 삭제되었습니다.');parent.document.location.reload();</script>");
	}
	exit;

}


if($act == 'comment_insert'){

	$code = $_SESSION['admininfo']['charger_ix'];
	$name = $_SESSION['admininfo']['charger'];

	$sql = "INSERT INTO 
				logstory_admin_page_log_comment
			SET 
				apl_ix	= '$apl_ix' ,
				mem_ix		= '$code' ,
				charger_name= '$name' ,
				comment		= '$comment' ,
				regdate		=	now()
			";
	
	$db->query($sql);

	echo("<script>alert('댓글이 등록되었습니다.');parent.document.location.reload();</script>");
	exit;

}

if($act == 'comment_delete'){

	$sql = "SELECT 
				mem_ix 
			FROM
				logstory_admin_page_log_comment
			WHERE 
				ec_ix = '".$ec_ix."'
			";
	
	$db->query($sql);
	$db->fetch();

	$mem_ix = $db->dt['mem_ix'];
	$mem_code = $_SESSION['admininfo']['charger_ix'];
	if($mem_ix == $mem_code){
		
		$sql = "DELETE 
				FROM
					logstory_admin_page_log_comment
				WHERE 
					ec_ix = '".$ec_ix."'
				";
		$db->query($sql);
		
		echo("<script>alert('정상적으로 삭제되었습니다.');history.go(-1);</script>");
		exit;

	}else{

		echo("<script>alert('본인이 작성한 댓글만 삭제가능합니다.');history.go(-1);</script>");
		exit;

	}

}

?>