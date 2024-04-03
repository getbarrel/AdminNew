<?
include("../class/layout.class");


$db = new Database;

if($act == 'update'){
	$sql = "update log_service_error set status = '$status',worker_name = '$worker_name' where log_idx = '$log_idx' ";
	$db->query($sql);
	
	if($mode == 'pop'){
		echo("<script>alert('정상적으로 수정되었습니다.');opener.document.location.reload();self.close();</script>");
	}else{
		echo("<script>alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
	}
	exit;
}

if($act == 'delete'){
	
	$sql = "delete from log_service_error where log_idx = '$log_idx'";
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
				log_service_error_comment
			SET 
				error_ix	= '$error_ix' ,
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
				log_service_error_comment
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
					log_service_error_comment
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