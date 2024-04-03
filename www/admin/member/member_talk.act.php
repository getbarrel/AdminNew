<?
include("$DOCUMENT_ROOT/class/mysql.class");

$db = new MySQL;
$db2 = new MySQL;

if($act == "change"){

	$sql="UPDATE shop_member_talk_history SET status='Y' WHERE ta_ix='".$ta_ix."' ";//상담내역을 처리 완료로 바꾼다
	$db2->query($sql);

	if($mode=="pop") {
		echo "<script>alert('처리완료로 변경 되었습니다.');top.opener.document.location.reload();top.window.close();</script>";
		exit;
	} else {
		echo "<script>alert('처리완료로 변경 되었습니다.');top.document.location.reload();location.href='about:blank';</script>";
		exit;
	}

} else if($act == "delete") {
	$sql = "delete from shop_member_talk_history where ta_ix='".$ta_ix."' ";
	$db->query($sql);
	if($mode=="pop") {
		echo "<script>alert('정상적으로 삭제 되었습니다.');top.opener.document.location.reload();top.window.close();</script>";
		exit;
	} else {
		echo "<script>alert('정상적으로 삭제 되었습니다.');top.document.location.reload();location.href='about:blank';</script>";
		exit;
	}

}else if($act == "all_update"){

	if($page_name == "member_talk"){
		foreach($ta_ix as $key => $value){
			$sql="UPDATE shop_member_talk_history SET status='".$status."' WHERE ta_ix='".$value."' ";//회원을 사업자 회원으로 바꿈 kbk
			$db2->query($sql);
		}
	
	}
	if($mode=="pop") {
		echo "<script>alert('정상적으로 변경 되었습니다.');top.opener.document.location.reload();top.window.close();</script>";
		exit;
	} else {
		echo "<script>alert('정상적으로 변경 되었습니다..');top.document.location.reload();location.href='about:blank';</script>";
		exit;
	}
	
	
}

?>