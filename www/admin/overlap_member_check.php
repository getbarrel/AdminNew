<?php
include $_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class";

//중복 회원 체크 하여 제거 처리 


#중복된 회원을 검색

$mall_ix = "20bd04dac38084b2bafdd6d78cd596b1";

$sql = "select id from (select count(*) cnt , id, max(date) from common_user where mall_ix = '".$mall_ix."' group by id HAVING cnt > 1  ) a";
$db->query($sql);
$overlapUsers = $db->fetchall();
exit;

if(is_array($overlapUsers)){
	foreach($overlapUsers as $key=>$val){
			$sql = "select count(id) id_cnt, count(pw) pw_cnt from common_user where id = '".$val['id']."'  group by pw";
			$db->query($sql);
			
			
			if($db->total > 1){
				#비밀번호가 중복된 계정이 다른경우 최근 변경된 비밀번호 계정을 남겨야 함
			
						continue;
					exit;
				$sql = "select code from common_user where id = '".$val['id']."'  ";
				$db->query($sql);
				$users = $db->fetchall();
				
				$orderBool = false;
				$orderUserCode = array();
				if(is_array($users)){
					foreach($users as $k=>$v){
						$sql = "select * from shop_order where user_code = '".$v['code']."' and status not in ('SR')  group by user_code ";
						
						$db->query($sql);				
						if($db->total > 0){			
							$db->fetch();
							$user_code = $db->dt['user_code'];
							$orderUserCode[] = $user_code;
							$orderBool = true;
						}
					}
				}
				
				if($orderBool == true){
					#주문이 포함된 계정
					
					$sql = "select * from common_user where id = '".$val['id']."' and code not in ('".implode("','",$orderUserCode)."') ";
					echo $sql;
					continue;
					exit;
					$db->query($sql);
					$del_user = $db->fetchall();
					if(is_array($del_user) && count($del_user) > 0){
						foreach($del_user as $dkey =>$dval){

							$code = $dval['code'];							

							deleteUser($code);
							
						}
					}
					
				}else{
					continue;
					exit;
					$sql = "select code from common_user where id = '".$val['id']."'  order by last desc limit 1 ";
					$db->query($sql);
					$db->fetch();
					$code = $db->dt['code'];
					
					$sql = "select code from common_user where id = '".$val['id']."' and code !='".$code."'  ";
				
					$db->query($sql);
					$del_user = $db->fetchall();
					
					if(is_array($del_user)){
						foreach($del_user as $dkey =>$dval){

							$code = $dval['code'];

							deleteUser($code);
							
						}
					}
				}
				
				
			}else{
				#비밀번호 동일한 계정 
				#중복된 계정 중 주문된 계정이 포함되어 있는지 찾기
				continue;
					exit;
				$sql = "select code from common_user where id = '".$val['id']."'  ";
				$db->query($sql);
				$users = $db->fetchall();
				
				$orderBool = false;
				$orderUserCode = array();
				if(is_array($users)){
					foreach($users as $k=>$v){
						$sql = "select * from shop_order where user_code = '".$v['code']."' and status not in ('SR')  group by user_code ";
						
						$db->query($sql);				
						if($db->total > 0){			
							$db->fetch();
							$user_code = $db->dt['user_code'];
							$orderUserCode[] = $user_code;
							$orderBool = true;
						}
					}
				}
				
				if($orderBool == true){
					#주문이 포함된 계정
					
					$sql = "select * from common_user where id = '".$val['id']."' and code not in ('".implode("','",$orderUserCode)."') ";
					
					$db->query($sql);
					$del_user = $db->fetchall();
					if(is_array($del_user) && count($del_user) > 0){
						foreach($del_user as $dkey =>$dval){

							$code = $dval['code'];							

							deleteUser($code);
							
						}
					}
					
				}else{
					continue;
					exit;
					$sql = "select code from common_user where id = '".$val['id']."'  order by last desc limit 1 ";
					$db->query($sql);
					$db->fetch();
					$code = $db->dt['code'];
					
					$sql = "select code from common_user where id = '".$val['id']."' and code !='".$code."'  ";
					$db->query($sql);
					$del_user = $db->fetchall();
					
					if(is_array($del_user)){
						foreach($del_user as $dkey =>$dval){

							$code = $dval['code'];

							deleteUser($code);
							
						}
					}
				}
				
			}
		
	}	
}

function deleteUser($code){
	
	$db = new database;
	
	$db->query("select company_id,mileage,id from ".TBL_COMMON_USER."  WHERE code='$code'");
	$db->fetch();
	$company_id = $db->dt[company_id];
	$mileage = $db->dt[mileage];
	$id = $db->dt[id];
	

	$db->query("DELETE FROM ".TBL_COMMON_MEMBER_DETAIL."  WHERE code='$code'");
	$db->query("DELETE FROM ".TBL_COMMON_USER."  WHERE code='$code'");

	if($mileage > 0){
		$mileage_data[uid] = $code;
		$mileage_data[type] = 6;
		$mileage_data[mileage] = abs($mileage);
		$mileage_data[message] = "중복가입 통합으로 인한 마일리지 소멸";
		$mileage_data[state_type] = "use";
		$mileage_data[save_type] = 'mileage';
		print_r($mileage_data);
		InsertMileageInfo($mileage_data);
	}
}

?>