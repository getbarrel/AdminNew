<?
include("../../class/database.class");

$db = new Database;

if ($act == "json_insert"){

	$sql = "insert into sellertool_etc_linked_relation 
				(etc_div,site_code,origin_code,origin_name,target_code,target_name,rel_date)
				values
				('$etc_div','$site_code','$origin_code','$origin_name','$target_code','$target_name',NOW())";
	$db->query($sql);
	
	echo "SUCCESS";
	exit;
}

if ($act == "json_update"){

	$target_name = $cname;

	$sql = "update sellertool_etc_linked_relation set
				etc_div='$etc_div',
                site_code='$site_code',
                origin_code='$origin_code',
				origin_name='$origin_name',
                target_code='$target_code',
                target_name='$target_name',
                editdate=NOW()
                where elr_ix='$elr_ix'";

	$db->query($sql);

    echo "SUCCESS";
	exit;
	//echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.opener.document.location.reload();parent.window.close();</script>");
}


if ($act == 'delete'){
	$db->query("delete from sellertool_etc_linked_relation where elr_ix = '$elr_ix'");
	echo 'SUCCESS';
	exit;
}

?>