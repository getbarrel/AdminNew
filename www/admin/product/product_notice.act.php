<?
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

$db = new Database;


if($act == 'insert'){

	if($pid == ''){
		echo "<script>alert('상품시스템코드가 없습니다.');parent.opener.location.reload();self.close();</script>";
	exit;
	}
	$sql = "insert into shop_product_notice SET
				pid = '".$pid."',
				type = '".$type."',
				title = '".$title."',
				name='".$name."',
				id='".$id."',
				code = '".$code."',
				com_name = '".$com_name."',
				company_id = '".$company_id."',
				md_code = '".$md_code."',
				md_name = '".$md_name."',
				status = 'W',
				contents = '".$contents."',
				regdate = NOW()";
	$db->query($sql);

	echo "<script>alert('문의요청 하였습니다.');parent.opener.location.reload();self.close();</script>";
	exit;

}


if($act == 'update'){
	
	

	if($status == 'C'){
		$set .= ", complete_date = NOW() ";
	}

	$set .= ", reply = '".$reply."',
				status = '".$status."',
				check_yn = 'N',
				title = '".$title."',
				contents = '".$contents."' ";

	$sql = "UPDATE shop_product_notice SET
				edit_date = NOW()
				$set
			WHERE
				pn_ix = '".$pn_ix."'";

	$db->query($sql);

	echo "<script>alert('수정 되었습니다.');parent.opener.location.reload();self.close();</script>";
	exit;

	if($status == 'W'){	//셀러는 문의요청중일때만 수정할수 있음

	}else{
		echo "<script>
			alert('처리중인 문의는 수정할수 없습니다.');
			parent.location.reload();
			self.close();
		</script>";
		exit;
	}



}


if($act == 'detail_delete'){

	if($pn_ix == ""){
		echo "<script>alert('삭제 항목이 없습니다.');</script>";
		exit;
	}

	$sql = "select * from shop_product_notice where pn_ix = '".$pn_ix."' ";
	$db->query($sql);
	$db->fetch();
	if($db->dt[status] != 'W'){
		echo "<script>alert('문의요청중인 정보만 삭제 가능합니다.');</script>";
		exit;
	}

	$sql= "delete from shop_product_notice where pn_ix = '".$pn_ix."'";
	$db->query($sql);

	echo "<script>alert('삭제 되었습니다.');parent.location.reload();</script>";
	exit;
}

if($act == 'info_delete'){

	if(count($pn_ix) < 1){
		echo "<script>alert('삭제 항목이 없습니다.');</script>";
		exit;
	}
	
	for($i=0;$i<count($pn_ix);$i++){
		$sql= "delete from shop_product_notice where pn_ix = '".$pn_ix[$i]."'";
		$db->query($sql);
	}

	echo "<script>alert('삭제 되었습니다.');parent.location.reload();</script>";

}
?>