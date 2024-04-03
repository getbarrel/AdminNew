<?
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

$db = new Database;

if($act == 'detail_insert'){
	$mobile   = $mobile_01."-".$mobile_02."-".$mobile_03;
	$tel = $tel_01."-".$tel_02."-".$tel_03;

	$sql = "insert into common_seller_support SET
				type = '".$type."',
				title = '".$title."',
				name='".$name."',
				id='".$id."',
				code = '".$code."',
				company_id = '".$company_id."',
				md_code = '".$md_code."',
				md_name = '".$md_name."',
				status = 'W',
				tel = '".$tel."',
				mobile = '".$mobile."',
				mail = '".$mail."',
				contents = '".$contents."',
				regdate = NOW()";
	$db->query($sql);

	echo "<script>alert('문의요청 하였습니다.');parent.opener.location.reload();self.close();</script>";
	exit;

}

if($act == 'detail_update'){
	
	$mobile   = $mobile_01."-".$mobile_02."-".$mobile_03;
	$tel = $tel_01."-".$tel_02."-".$tel_03;

	if($status == 'C'){
		$set .= ", complete_date = NOW() ";
	}

	if($type == 'C'){	//관리자 긴급사항시 셀러가 답변달수 있음 2014-07-06 이학봉
		if($admininfo[admin_level] == '8'){
			$set .= ", reply = '".$reply."',status = '".$status."',check_yn = 'N'  ";
		}else{
			if($status == 'W'){	//셀러는 문의요청중일때만 수정할수 있음
				$set .= ", 
							title = '".$title."',
							name = '".$name."',
							md_code = '".$md_code."',
							md_name = '".$md_name."',
							tel = '".$tel."',
							mobile = '".$mobile."',
							mail = '".$mail."',
							contents = '".$contents."' ";
			}else{
				echo "<script>
					alert('처리중인 문의는 수정할수 없습니다.');
					parent.location.reload();
					self.close();
				</script>";
				exit;
			}
		}
	
	}else{
		if($admininfo[admin_level] == '9'){
			$set .= ", reply = '".$reply."',status = '".$status."',check_yn = 'N'  ";
		}else{
			
			if($status == 'W'){	//셀러는 문의요청중일때만 수정할수 있음
				$set .= ", 
							title = '".$title."',
							name = '".$name."',
							md_code = '".$md_code."',
							md_name = '".$md_name."',
							tel = '".$tel."',
							mobile = '".$mobile."',
							mail = '".$mail."',
							contents = '".$contents."' ";
			}else{
				echo "<script>
					alert('처리중인 문의는 수정할수 없습니다.');
					parent.location.reload();
					self.close();
				</script>";
				exit;
			}
		}
	}

	$sql = "UPDATE common_seller_support SET
				edit_date = NOW()
				$set
			WHERE
				fs_ix = '".$fs_ix."'";
	
	$db->query($sql);

	echo "<script>alert('수정 되었습니다.');parent.opener.location.reload();self.close();</script>";
	exit;

}


if($act == 'detail_delete'){

	if($fs_ix == ""){
		echo "<script>alert('삭제 항목이 없습니다.');</script>";
		exit;
	}

	if($admininfo[admin_level] != '9'){
		$where = " and status = 'W' and company_id = '".$admininfo[company_id]."'";
	}

	$sql = "select * from common_seller_support where fs_ix = '".$fs_ix."' $where ";
	$db->query($sql);
	$db->fetch();
	if($db->dt[status] != 'W'){
		echo "<script>alert('문의요청중인 정보만 삭제 가능합니다.');</script>";
		exit;
	}

	$sql= "delete FROM common_seller_support where fs_ix = '".$fs_ix."' $where  ";
	$db->query($sql);

	echo "<script>alert('삭제 되었습니다.');parent.location.reload();</script>";
	exit;
}

if($act == 'info_delete'){

	if(count($fs_ix) < 1){
		echo "<script>alert('삭제 항목이 없습니다.');</script>";
		exit;
	}
	
	if($admininfo[admin_level] != '9'){
		$where = " and status = 'W' and company_id = '".$admininfo[company_id]."'";
	}

	for($i=0;$i<count($fs_ix);$i++){
		$sql= "delete FROM common_seller_support where fs_ix = '".$fs_ix[$i]."' $where  ";
	}

	echo "<script>alert('삭제 되었습니다.');parent.location.reload();</script>";

}
?>