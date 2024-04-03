<?
include("../../class/database.class");

$db = new Database;
$db2 = new Database;

if($act == "insert"){

	if($mandatory_name != "" && $mi_code !=""){
		
		$sql  = "insert into shop_mandatory_info set
					mi_code = '".$mi_code."',
					mandatory_name = '".$mandatory_name."',
					is_use = '".$is_use."',
					mall_ix = '".$mall_ix."',
					regdate = NOW()";
		$db->query($sql);
		$mi_ix = $db->insert_id();

		if($mi_ix && is_array($mandatory)){
			$i = 1;
			foreach($mandatory as $key=> $val){
				foreach($val as $seq => $details){
					$insert_sql = "insert into shop_mandatory_detail set
									mi_ix = '".$mi_ix."',
									detail_code = '".$details[detail_code]."',
									seq = '".$i."',
									mid_title = '".$details[mid_title]."',
									mid_desc = '".$details[mid_desc]."',
									mid_comment = '".$details[mid_comment]."',
									mid_code = '1',
									regdate = NOW()";
					$db->query($insert_sql);
					$i++;
				}
			}
		}
		
		echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('등록이 정상적으로 처리 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>";

	}else{

		echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('등록정보가 부족합니다.');parent.opener.document.location.reload();parent.self.close();</script>";
	}

}


if($act == "update"){

	if($mi_ix){
		$sql = "update shop_mandatory_info set
					mi_code = '".$mi_code."',
					mandatory_name = '".$mandatory_name."',
					is_use = '".$is_use."',
					mall_ix = '".$mall_ix."',
					edit_date = NOW()
				where
					mi_ix = '".$mi_ix."'";
		$db->query($sql);

		if($mi_ix && is_array($mandatory)){

			$del = "delete from shop_mandatory_detail where mi_ix = '".$mi_ix."'";
			$db->query($del);

			$i = 1;
			foreach($mandatory as $key=> $val){
				foreach($val as $seq => $details){

					$insert_sql = "insert into shop_mandatory_detail set
									mi_ix = '".$mi_ix."',
									detail_code = '".$details[detail_code]."',
									seq = '".$i."',
									mid_title = '".$details[mid_title]."',
									mid_desc = '".$details[mid_desc]."',
									mid_comment = '".$details[mid_comment]."',
									mid_code = '1',
									regdate = NOW()";
					$db->query($insert_sql);
					$i++;
				}
			
			}
		
		}

		echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('수정되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>";
	
	}
	
}

if($act == 'delete'){

	if($mi_ix){
		$sql = "delete from shop_mandatory_info where mi_ix = '".$mi_ix."'";
		$db->query($sql);
		$sql = "delete from shop_mandatory_detail where mi_ix = '".$mi_ix."'";
		$db->query($sql);

		echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('삭제되되었습니다..');parent.document.location.reload();parent.self.close();</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('삭제할 고시코드가 없습니다.');parent.document.location.reload();parent.self.close();</script>";
	}
}



if($mode == "search_mi_code"){
	if($mi_code){
		$sql = "select
					*
				from
					shop_mandatory_info
				where
					mi_code = '".$mi_code."'
				";
		$db->query($sql);
		$db->fetch();
		
		if($db->total > 0){
			echo "N";		//동일한 고시코드가 있을경우
		}else{
			echo "Y";		//동일한 고시코드가 없을경우 등록 허용함
		}

	}else{
		echo "N";
	}

}

if($act == "get_mandatory_info"){
	if($parameter_1){
		$mi_code = $parameter_1;

		$sql = "select 
					*
				from
					shop_mandatory_info as mi 
					inner join shop_mandatory_detail as  md on (mi.mi_ix = md.mi_ix)
				where
					mi.mi_code = '".$mi_code."'
					order by md.seq ASC";
		$db->query($sql);
		$mandatory_array = $db->fetchall();

		for($i=0;$i<count($mandatory_array);$i++){
			$data_array[$i][code] = $mandatory_array[$i][mi_code]."|".$mandatory_array[$i][detail_code];
			$data_array[$i][title] = $mandatory_array[$i][mid_title];
			$data_array[$i][desc] = $mandatory_array[$i][mid_desc];
			$data_array[$i][comment] = $mandatory_array[$i][mid_comment];
			$data_array[$i][validation] = 'true';
		}

		if(count($data_array) > 0){
			
			$datas = $data_array;
			$datas = json_encode($datas);
			$datas = str_replace("\"true\"","true",$datas);
			$datas = str_replace("\"false\"","false",$datas);
			echo $datas;
		}
	
	}

}

if($act == "select_laundry"){

		//if(strlen($select_type) == 3){
		//	$where = " and laundry_use = 1 and laundry_use_en = 1 "; 
		//} else {
			$subCid = substr($relation_code,0,3);
			$where = " and cid like '$subCid%' and laundry_use = 1 and laundry_use_en = 1 "; 
		//}

		$sql = "select cid, title from shop_laundry_info where depth = $target_depth $where order by cid asc";

		$db->query($sql);
		$data_array = $db->fetchall();
		
		for($j=0;$j<count($data_array);$j++){
			
			$access_data[$j][cid] = $data_array[$j][cid];
			$access_data[$j][title] = $data_array[$j][title];
		}

		if(count($access_data) > 0){
			
			$datas = $access_data;
			$datas = json_encode($datas);
			$datas = str_replace("\"true\"","true",$datas);
			$datas = str_replace("\"false\"","false",$datas);
			echo $datas;
			exit;
		}

	

}


?>