<?
//print_r($_POST);
//exit;
include("../../class/database.class");
$db = new Database;
if($act == "insert"){
	
	$sql = "select * from buyingservice_shopping_center where sc_code ='$sc_code' ";
	$db->query($sql);

	if($db->total){
		echo "<script type='text/javascript'>
		<!--
			alert('[".$sc_code."] 는 이미 사용하고 있습니다. 다른코드를 발급해주시기 바랍니다.');
			parent.$.unblockUI();
		//-->
		</script>";
		exit;
	}

	$sql = "insert into buyingservice_shopping_center
	(sc_ix,sc_code,sc_name_korea,sc_name_english,sc_name_chinese,sc_sub_domain,sc_url,sc_charger_ix,sc_start_date,sc_end_date,sc_incentive,sc_give_day,sc_msg,disp,editdate,regdate,ca_ix) values
	('','$sc_code','$sc_name_korea','$sc_name_english','$sc_name_chinese','$sc_sub_domain','$sc_url','$sc_charger_ix','$sc_start_date','$sc_end_date','$sc_incentive','$sc_give_day','$sc_msg','$disp',NOW(),NOW(),'$ca_ix')";

	$db->query($sql);
	$sc_ix = mysql_insert_id();


	if($mmode == "pop"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상가정보 관리 등록이 완료 되었습니다.');parent.document.location.href='shopping_center.add.php?sc_ix=".$sc_ix."';</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상가정보 관리 등록이 완료 되었습니다.');parent.document.location.href='shopping_center.add.php?sc_ix=".$sc_ix."';</script>";
	}
}

/*
if($act == "delete"){
	$db->query("delete from buyingservice_shopping_center where sc_ix = '".$sc_ix."'");
	$db->query("delete from buyingservice_shopping_center_floor_info where sc_ix = '".$sc_ix."'");
	$db->query("delete from buyingservice_shopping_center_line_info where sc_ix = '".$sc_ix."'");
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상가정보 관리가 정상적으로 삭제 되었습니다.');parent.document.location.href='shopping_center_list.php'</script>";
}
*/

if($act == "update"){
	
	if($view_type=="detail"){
		
		foreach($_FILES as $key => $val){

			if($key=="sc_transport"){

				$msg="상가 교통수단";
				$file_div="shopping_center_transport";
				$file_name=$val["name"];
				$file_size=$val["size"];
				$file_type=$val["type"];
				$file_tmp_name=$val["tmp_name"];
				
				if($file_size>0){
					$bin_data = addslashes(fread(fopen($file_tmp_name, "r"), $file_size));

					$sql = "select * from file_binary_data where file_div_ix='".$sc_ix."' and file_div='".$file_div."' ";
					$db->query($sql);
					if(!$db->total){
						$sql = "insert into file_binary_data (id,file_div_ix,file_div,file_name,file_size,file_type,bin_data,msg,regdate) 
						values ('','".$sc_ix."','".$file_div."','".$file_name."','".$file_size."','".$file_type."','".$bin_data."','".$msg."',NOW())";
						$db->query($sql);
					}else{
						$db->fetch();
						$file_binary_data_id=$db->dt["id"];
						$sql = "update  file_binary_data set 
							file_name='".$file_name."' ,
							file_size='".$file_size."' ,
							file_type='".$file_type."' ,
							bin_data='".$bin_data."' ,
							msg='".$msg."'
							where id='".$file_binary_data_id."'
							";
						$db->query($sql);
					}
				}

			}else{

				$msg="상가 이미지";
				$file_div="shopping_center_shopimg";
				
				$sql = "delete from file_binary_data where id not in ('".implode("','",$sc_shopimg_id)."') and file_div_ix='".$sc_ix."' and file_div='".$file_div."' ";
				$db->query($sql);

				foreach($sc_shopimg_id as $_key => $id){
					if($val["size"][$_key]>0){
						$file_name=$val["name"][$_key];
						$file_size=$val["size"][$_key];
						$file_type=$val["type"][$_key];
						$file_tmp_name=$val["tmp_name"][$_key];
						
						if($file_size>0){
							$bin_data = addslashes(fread(fopen($file_tmp_name, "r"), $file_size));
							
							if($id!=""){
								$sql = "select * from file_binary_data where id='".$id."'";
								$db->query($sql);
								if(!$db->total){
									$sql = "insert into file_binary_data (id,file_div_ix,file_div,file_name,file_size,file_type,bin_data,msg,regdate) 
									values ('','".$sc_ix."','".$file_div."','".$file_name."','".$file_size."','".$file_type."','".$bin_data."','".$msg."',NOW())";
									$db->query($sql);
								}else{
									$db->fetch();
									$file_binary_data_id=$db->dt["id"];
									$sql = "update  file_binary_data set 
										file_name='".$file_name."' ,
										file_size='".$file_size."' ,
										file_type='".$file_type."' ,
										bin_data='".$bin_data."' ,
										msg='".$msg."'
										where id='".$file_binary_data_id."'
										";
									$db->query($sql);
								}
							}else{
								$sql = "insert into file_binary_data (id,file_div_ix,file_div,file_name,file_size,file_type,bin_data,msg,regdate) 
									values ('','".$sc_ix."','".$file_div."','".$file_name."','".$file_size."','".$file_type."','".$bin_data."','".$msg."',NOW())";
								$db->query($sql);
							}
						}
					}
				}
			}

		}

		$sql = "update buyingservice_shopping_center set 
					sc_mg_charger_ix = '$sc_mg_charger_ix',
					sc_mg_time_type = '$sc_mg_time_type',
					sc_start_mg_time = '$sc_start_mg_time',
					sc_end_mg_time = '$sc_end_mg_time',
					start_no = '$start_no',
					end_no = '$end_no',
					editdate=NOW()
					where sc_ix = '".$sc_ix."'";
		$db->query($sql);

		$sql = "update buyingservice_shopping_center_floor_info set 
					insert_yn='N'					
					where sc_ix = '".$sc_ix."' ";
		$db->query($sql);

		for($i=0;$i < count($floor);$i++){
			$sql = "select sc_ix from buyingservice_shopping_center_floor_info where sc_ix = '".$sc_ix."' and floor='".$floor[$i]."' ";
			$db->query($sql);
			if($db->total){
				$sql = "update buyingservice_shopping_center_floor_info set 
							floor_memo='".$floor_memo[$floor[$i]]."',
							floor_start_time='".$floor_start_time[$floor[$i]]."',
							floor_end_time='".$floor_end_time[$floor[$i]]."',
							insert_yn='Y'
							
							where sc_ix = '$sc_ix' and floor='".$floor[$i]."' ";
				$db->query($sql);
			}else{
				$sql = "insert into buyingservice_shopping_center_floor_info set 
							sc_ix='".$sc_ix."',
							floor='".$floor[$i]."',
							floor_memo='".$floor_memo[$floor[$i]]."',
							floor_start_time='".$floor_start_time[$floor[$i]]."',
							floor_end_time='".$floor_end_time[$floor[$i]]."',
							insert_yn='Y',	
							regdate=NOW() ";
				$db->query($sql);
			}
		}

		$sql = "delete from buyingservice_shopping_center_floor_info 
					where sc_ix = '".$sc_ix."' and insert_yn='N'		 ";
		$db->query($sql);


		$sql = "update buyingservice_shopping_center_line_info set 
					insert_yn='N'
					where sc_ix = '".$sc_ix."' ";
		$db->query($sql);

		for($i=0;$i < count($line);$i++){
			$sql = "select sc_ix from buyingservice_shopping_center_line_info where sc_ix = '".$sc_ix."' and line='".$line[$i]."' ";
			$db->query($sql);
			if($db->total){
				$sql = "update buyingservice_shopping_center_line_info set 
							insert_yn='Y'					
							where sc_ix = '$sc_ix' and line='".$line[$i]."' ";
				$db->query($sql);
			}else{
				$sql = "insert into buyingservice_shopping_center_line_info set 
							sc_ix='".$sc_ix."',
							line='".$line[$i]."',
							insert_yn='Y',	
							regdate=NOW() ";
				$db->query($sql);
			}
		}

		$sql = "delete from buyingservice_shopping_center_line_info 
					where sc_ix = '".$sc_ix."' and insert_yn='N'		 ";
		$db->query($sql);

	}else{

		$sql = "update buyingservice_shopping_center set
					sc_name_korea='".$sc_name_korea."',
					sc_name_english='".$sc_name_english."',
					sc_name_chinese='".$sc_name_chinese."',
					sc_sub_domain='".$sc_sub_domain."',
					sc_url='".$sc_url."',
					sc_charger_ix='".$sc_charger_ix."',
					sc_start_date='".$sc_start_date."',
					sc_end_date='".$sc_end_date."',
					sc_incentive='".$sc_incentive."',
					sc_give_day='".$sc_give_day."',
					sc_msg='".$sc_msg."',
					disp='".$disp."',
					editdate=NOW(),
					ca_ix='".$ca_ix."'
					where sc_ix = '".$sc_ix."'";
		$db->query($sql);

	}

	if($mmode == "pop"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상가정보 관리 등록이 완료 되었습니다.');parent.document.location.reload();</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상가정보 관리 수정이 완료 되었습니다.');parent.document.location.reload();</script>";
	}
}
?>