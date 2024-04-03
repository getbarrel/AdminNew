<?php
require $_SERVER ['DOCUMENT_ROOT'] . '/class/database.class';
$site_code = $_POST ['site_code'];
$act = $_POST ['act'];
$db = new MySQL ();

$sql = "SELECT
			* 
		FROM 
			sellertool_site_info
		WHERE
			site_code = '" . $site_code . "'";
			
$db->query ( $sql );
$info = $db->fetch ();
$api_key = $info['api_key'];
$_POST ['site_code'] = $info ['site_code'];
$_POST ['site_name'] = $info ['site_name'];

switch ($act) {
	/* 등록 */
	case 'insert':
		/* 값 등록 */
		$sql = "INSERT INTO
					sellertool_add_info
				SET
					site_name = '". $info ['site_name'] . "',
					api_key = '" . $api_key . "',
					add_info_name = '" . $_POST ['add_info_name'] . "',
					disp = '" . $_POST ['disp'] . "',
					reg_date = NOW()			
				";
		$db->query($sql);
		
		$sql = "SELECT add_info_id 
				FROM sellertool_add_info 
				WHERE add_info_id=LAST_INSERT_ID()
				";
		$db->query($sql);
		$db->fetch();
		$add_info_id = $db->dt['add_info_id'];
		
		foreach ( $_POST as $key => $val ) :
			if ($key != 'act' && $key != 'x' && $key != 'y' && ! empty ( $val )) {
				$sql = "INSERT INTO
			                sellertool_add_info_meta
			            SET
							add_info_id = '" . $add_info_id . "',
			            	meta_key = '" . $key . "',
			            	meta_value = '" . $val . "'
			            ";
				echo $sql;
				
				$db->query ( $sql );
			}
		endforeach
		;
		
		echo ("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력되었습니다.');parent.document.location.reload();</script>");
		break;
	
	/* 업데이트 */
	case 'update' :
		$sql = "UPDATE
					sellertool_add_info
				SET
					site_name = '". $info ['site_name'] . "',
					api_key = '" . $api_key . "',
					add_info_name = '" . $_POST ['add_info_name'] . "',
					disp = '" . $_POST ['disp'] . "',
					update_date = NOW()
				WHERE
					add_info_id = '".$_POST['add_info_id']."'
				";
		$db->query($sql);
		$sql = "DELETE FROM sellertool_add_info_meta WHERE add_info_id = '".$_POST['add_info_id']."'";
		$db->query($sql);
		foreach ( $_POST as $key => $val ) :
			if ($key != 'act' && $key != 'x' && $key != 'y' && ! empty ( $val )) {
				$sql = "INSERT INTO
			                sellertool_add_info_meta
			            SET
			            	add_info_id = '" . $add_info_id . "',
			            	meta_key = '" . $key . "',
			            	meta_value = '" . $val . "'
			            ";
				echo $sql;
				$db->query ( $sql );
			}
		endforeach
		;
		
		echo ("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
		break;
	
	/* 삭제 */
	case 'delete' :
		$add_info_id = $_POST ['add_info_id'];
		$sql = "DELETE
				FROM sellertool_add_info
				WHERE add_info_id = '" . $add_info_id . "'";
		$db->query ( $sql );
		$sql = "DELETE
				FROM sellertool_add_info_meta
				WHERE add_info_id = '" . $add_info_id . "'";
		$db->query ( $sql );
		echo 'SUCCESS';
		break;
}