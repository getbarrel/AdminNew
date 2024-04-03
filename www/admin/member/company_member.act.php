<?
include($_SERVER["DOCUMENT_ROOT"]."/class/mysql.class");
include_once($_SERVER["DOCUMENT_ROOT"]."/class/mysql.class");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");

$db = new MySQL;
$db2 = new MySQL;

if($act == "change"){

	$sql="SELECT company_id FROM ".TBL_COMMON_USER." WHERE code='".$code."' ";
	$db2->query($sql);
	$db2->fetch();
	$company_id=$db2->dt["company_id"];

	$sql = "update ".TBL_COMMON_COMPANY_DETAIL." set seller_auth = 'Y' where company_id = '".$company_id."'";
	$db2->query($sql);

	$sql = "update ".TBL_COMMON_USER." set authorized = 'Y' where code='".$code."' ";
	$db2->query($sql);

	if($mode=="pop"){
		echo "<script>top.opener.document.location.reload();top.window.close();</script>";
		exit;
	}else{
		echo "<script>top.document.location.reload();location.href='about:blank';</script>";
		exit;
	}

}else if($act == "company_change"){

	$sql="SELECT company_id,request_info,request_yn FROM ".TBL_COMMON_USER." WHERE code='".$code."' ";
	$db2->query($sql);
	$db2->fetch();
	$company_id = $db2->dt["company_id"];
	$request_info = $db2->dt["request_info"];
	
	if($request_info == "C"){
		$sql = "update ".TBL_COMMON_USER." set mem_type ='C',mem_div = 'D',request_yn = 'Y',auth='0' where code='".$code."' ";
		$db2->query($sql);
	}else if ($request_info == "S"){
		$sql = "update ".TBL_COMMON_USER." set mem_type ='C',mem_div = 'S',request_yn = 'Y' where code='".$code."' ";
		$db2->query($sql);
	}

	$sql="UPDATE ".TBL_COMMON_MEMBER_DETAIL." set gp_ix='3' WHERE code='".$code."' ";//회원 그룹을 사업자회원으로 전환
	$db2->query($sql);

	if($mode=="pop"){
		echo "<script>top.opener.document.location.reload();top.window.close();</script>";
		exit;
	}else{
		echo "<script>top.document.location.reload();location.href='about:blank';</script>";
		exit;
	}

}else if($act == "seller_change"){

	include("../econtract/contract.lib.php");
	include("../logstory/class/sharedmemory.class");
	
	$shmop = new Shared("basic_seller_setup");
	$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
	$shmop->SetFilePath();
	$reserve_data = $shmop->getObjectForKey("basic_seller_setup");
	$seller_setup = unserialize(urldecode($reserve_data));

	$et_ix = $seller_setup[et_ix];
	$econtract_commission = $seller_setup[electron_contract_commission];

	$sql= "select company_id from  ".TBL_COMMON_USER." where code = '".$code."'";
	$db2->query($sql);
	$db2->fetch();
	$company_id = $db2->dt[company_id];	//입점업체키

	$sql = "update common_seller_delivery set et_ix = '".$et_ix."', econtract_commission = '".$econtract_commission."' where company_id = '".$company_id."'";
	$db2->query($sql);

	//전자계약서 인증 함수
	//regContract($db,$_SESSION['admininfo']['company_id'], $company_id, $et_ix);

	$sql = "update ".TBL_COMMON_USER." set auth = '4' where code='".$code."' ";
	$db2->query($sql);
	
	//셀러승인일자
	$sql = "update ".TBL_COMMON_SELLER_DETAIL." set authorized_date = NOW() where company_id = '".$company_id."' ";
	$db2->query($sql);

	
	/*셀러 승인 시 popbll 가입 되어지도록*/
	
	getPopbillJoinMember($code);
	
	
	if($mode=="pop"){
		echo "<script>top.opener.document.location.reload();top.window.close();</script>";
		exit;
	}else{
		echo "<script>top.document.location.reload();location.href='about:blank';</script>";
		exit;
	}

}else if($act == "seller_change_cancel"){


	if($code){	//셀러승인거부시 2014-08-06 이학봉
		$sql = "update common_user set request_info = 'C' where code = '".$code."'";
		$db->query($sql);
	}

	if($mode=="pop"){
		echo "<script>top.opener.document.location.reload();top.window.close();</script>";
		exit;
	}else{
		echo "<script>top.document.location.reload();location.href='about:blank';</script>";
		exit;
	}

} else if($act == "delete") {
	//$db->query("delete from shop_apply_company_member where cm_ix='".$cm_ix."' ");

	if($mode=="pop") {
		echo "<script>alert('정상적으로 삭제 되었습니다.');top.opener.document.location.reload();top.window.close();</script>";
		exit;
	} else {
		echo "<script>alert('정상적으로 삭제 되었습니다.');top.opener.document.location.reload();location.href='about:blank';</script>";
		exit;
	}
}else if($act == "all_update"){

	if($search_searialize_value){
		$unserialize_search_value = unserialize(urldecode($search_searialize_value));
		extract($unserialize_search_value);
	}

	if($authorized != ""){
		$where .= " and cu.authorized = '$authorized' ";
	}

	if($search_text != ""){
		if($search_type=="cmd.name"){
			$where.=" and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
			$where .= " and $search_type LIKE '%$search_text%' ";
		}
	}

	$startDate = $sdate;
	$endDate = $edate;

	if($regdate == '1'){	//신청일
		if($startDate != "" && $endDate != ""){
			if($db->dbms_type == "oracle"){
				$where .= " and  to_char(cu.request_date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
				$count_where .= " and  to_char(cu.request_date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
			}else{
				$where .= " and date_format(cu.request_date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
				$count_where .= " and date_format(cu.request_date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
			}
		}
	}

	if($update_type == '1'){

		$sql = "SELECT
					cu.*
				from
					".TBL_COMMON_USER." as  cu 
					inner join ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code 
				where
					cu.mem_type = 'M'
					and cu.request_info = 'M'
					$where ";

		$db->query($sql);
		$user_infos = $db->fetchall("object");

		for($i=0;$i<count($user_infos);$i++){
			$code[$i] = $user_infos[$i][code];
		}

	}else if($update_type == '2'){	//선택한 회원
		$code = $code;
	}

	foreach($code as $key => $value){

		$sql="UPDATE ".TBL_COMMON_USER." SET authorized='".$use_disp."' WHERE code='".$value."' ";//회원을 사업자 회원으로 바꿈 kbk
		$db2->query($sql);

		$sql="SELECT company_id FROM ".TBL_COMMON_USER." WHERE code='".$value."' ";
		$db2->query($sql);
		$db2->fetch();
		$company_id=$db2->dt["company_id"];

		$sql = "update ".TBL_COMMON_COMPANY_DETAIL." set seller_auth = 'Y' where company_id = '".$company_id."'";
		$db2->query($sql);
			
	}
	
	if($mode=="pop") {
		echo "<script>alert('정상적으로 변경 되었습니다.');top.opener.document.location.reload();top.window.close();</script>";
		exit;
	} else {
		echo "<script>alert('정상적으로 변경 되었습니다..');top.document.location.reload();location.href='about:blank';</script>";
		exit;
	}
	
}else if($act == "company_all_update"){		//회원승인대기 관련 2013-11-05 이학봉 수정 (현재 승인구조가 바꼇음)
	
	if($search_searialize_value){
		$unserialize_search_value = unserialize(urldecode($search_searialize_value));
		extract($unserialize_search_value);
	}

	if($request_yn != ""){
		$where .= " and cu.request_yn = '$request_yn' ";
	}

	if($search_text != ""){
		if($search_type=="cmd.name"){
			$where.=" and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
			$where .= " and $search_type LIKE '%$search_text%' ";
		}
	}

	$startDate = $sdate;
	$endDate = $edate;

	if($regdate == '1'){	//신청일
		if($startDate != "" && $endDate != ""){
			if($db->dbms_type == "oracle"){
				$where .= " and  to_char(cu.request_date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
			}else{
				$where .= " and date_format(cu.request_date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
			}
		}
	}

	if($update_type == '1'){

		$sql = "SELECT 
					cu.*
				from 
					".TBL_COMMON_USER." as cu 
					inner join ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code 
					inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
				where 
					cu.request_info in ('C','S')
					$where order by cu.date desc";
		$db->query($sql);
		$user_infos = $db->fetchall();

		for($i=0;$i<count($user_infos);$i++){
			$code[$i] = $user_infos[$i][code];
		}

	}else if($update_type == '2'){	//선택한 회원
		$code = $code;
	}

	foreach($code as $key => $value){

		$sql = "select request_info,company_id from ".TBL_COMMON_USER." where code='".$value."' ";
		$db->query($sql);
		$db->fetch();
		$request_info = $db->dt[request_info];
		$company_id=$db->dt["company_id"];

		if($use_disp == "Y"){	//승인

			if($request_info == "C"){
				$sql="UPDATE ".TBL_COMMON_USER." SET mem_type='C',mem_div = 'D',request_yn='".$use_disp."' WHERE code='".$value."' ";//사업자 회원으로 승인
				$db2->query($sql);
			}else if($request_info == "S"){
				$sql="UPDATE ".TBL_COMMON_USER." SET mem_type='C',mem_div = 'S',request_yn='".$use_disp."' WHERE code='".$value."' ";//셀러회원으로 승인
				$db2->query($sql);
			}

			$sql="UPDATE ".TBL_COMMON_MEMBER_DETAIL." set gp_ix='3' WHERE code='".$value."' ";//회원 그룹을 사업자회원으로 전환
			$db2->query($sql);

			$sql = "update ".TBL_COMMON_COMPANY_DETAIL." set seller_auth = 'Y',seller_type='1|2' where company_id = '".$company_id."'";	//사업장 승인과 국내매출,매입으롲 전환
			$db2->query($sql);

		}else if($use_disp == "W"){			//승인대기

			$sql="UPDATE ".TBL_COMMON_USER." SET mem_type='M',mem_div = 'D',request_yn='".$use_disp."' WHERE code='".$value."' ";// 승인대기로 전환시 회원은 일반회원으로 전환
			$db2->query($sql);
			
			$sql="UPDATE ".TBL_COMMON_MEMBER_DETAIL." set gp_ix='9' WHERE code='".$value."' ";// 승인대기로 전환시 회원은 일반회원그룹으로 전환
			$db2->query($sql);

		}else if($use_disp == "X"){

			$sql="UPDATE ".TBL_COMMON_USER." SET mem_type='M',mem_div = 'D', request_yn='".$use_disp."' WHERE code='".$value."' ";//승인대기로 전환시 회원은 일반회원으로 전환
			$db2->query($sql);
		
		}else if($use_disp == "N"){

			$sql="UPDATE ".TBL_COMMON_USER." SET mem_type='M',mem_div = 'D', request_yn='".$use_disp."' WHERE code='".$value."' ";//승인대기로 전환시 회원은 일반회원으로 전환
			$db2->query($sql);
		
		}
	}

	if($mode=="pop"){
		echo "<script>alert('정상적으로 변경 되었습니다.');top.opener.document.location.reload();top.window.close();</script>";
		exit;
	} else {
		echo "<script>alert('정상적으로 변경 되었습니다..');top.document.location.reload();location.href='about:blank';</script>";
		exit;
	}

}else if($act == "all_seller_update"){

	if($search_searialize_value){
		$unserialize_search_value = unserialize(urldecode($search_searialize_value));
		extract($unserialize_search_value);
	}

	if($authorized != ""){
		$where .= " and cu.authorized = '$authorized' ";
	}

	if($auth != ""){
		$where .= " and cu.auth = '$auth' ";
	}

	if($search_text != ""){
		if($search_type=="cmd.name"){
			$where.=" and AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' ";
		}else{
			$where .= " and $search_type LIKE '%$search_text%' ";
		}
	}

	$startDate = $sdate;
	$endDate = $edate;
	if($regdate == '1'){	//신청일
		if($startDate != "" && $endDate != ""){
			if($db->dbms_type == "oracle"){
				$where .= " and  to_char(cu.request_date_ , 'YYYY-MM-DD') between '".$startDate."' and '".$endDate."' ";
			}else{
				$where .= " and date_format(cu.request_date,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
			}
		}
	}

	if($update_type == '1'){

		$sql = "SELECT 
					cu.*
				from 
					".TBL_COMMON_USER." as cu 
					inner join ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code 
					inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
					inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
					left join shop_category_info as ci on (ci.cid = csd.seller_cid)
				where 
					cu.request_info = 'S'
					$where order by cu.date desc";
		$db->query($sql);
		$user_infos = $db->fetchall();

		for($i=0;$i<count($user_infos);$i++){
			$code[$i] = $user_infos[$i][code];
		}

	}else if($update_type == '2'){	//선택한 회원
		$code = $code;
	}

	foreach($code as $key => $value){

		$sql="UPDATE ".TBL_COMMON_USER." SET auth='".$use_disp."' WHERE code='".$value."' ";//회원을 사업자 회원으로 바꿈 kbk
		$db2->query($sql);
			
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