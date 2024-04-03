<?
//include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include("../class/layout.class");

$db = new Database;
$db2 = new Database;

if($act == "search_brand"){
	
	//if($search_text){

		$sql = "select b_ix as value, brand_name as text from shop_brand where disp = '1' and apply_status = '1' and (brand_name LIKE '%".$search_text."%'  or brand_name_division LIKE '%".$search_text."%') ";
		$db->query($sql);
		//echo $sql;
		$datas = $db->fetchall();

		//$datas = $data;
		$datas = json_encode($datas);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
	//}

}

if($act == "search_seller"){
	
	//if($search_text){

		$sql = "select company_id as value, com_name as text from common_company_detail where com_name LIKE '%".$search_text."%'  and com_type = 'S' ";
		$db->query($sql);
		//echo $sql;
		$datas = $db->fetchall();
  

		//$datas = $data;
		$datas = json_encode($datas);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
	//}

}



if($act == "search_group"){
	
	//if($search_text){

		$sql = "select gp_ix as value, gp_name as text from shop_groupinfo where gp_name LIKE '%".$search_text."%'  and disp = '1' ";
		$db->query($sql);
		//echo $sql;
		$datas = $db->fetchall();
  

		//$datas = $data;
		$datas = json_encode($datas);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
	//}

}



if($act == "search_member"){
	
	//if($search_text){

		$sql = "select cmd.code as value, concat(AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."'),' (', cu.id ,')')  as text 
					from common_user cu, common_member_detail cmd 
					where cu.code = cmd.code and (AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') LIKE '%".$search_text."%' or id LIKE '%".$search_text."%') ";
		$db->query($sql);
		//echo $sql;
		$datas = $db->fetchall();
  

		//$datas = $data;
		$datas = json_encode($datas);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
	//}

}



if($act == 'search_category'){
	if($search_text != ""){
		if(strpos($search_text,',') === false){
			$where = " and cname like '%".$search_text."%'";
		}else{
			$search_text_array = explode(",",$search_text);
			$where .=" and (";
			for($i=0;$i<count($search_text_array);$i++){
				if($i == count($search_text_array) -1){
					$where .= " cname like '%".$search_text_array[$i]."%' ";
				}else{
					$where .= " cname like '%".$search_text_array[$i]."%' or ";
				}
			}
			$where .= ")";
		}
	}

	$sql = "select depth, cid,	cname from
				shop_category_info
				where 1 $where
				order by cid  ASC";
				//echo nl2br($sql);
	$db->query($sql);
	$_datas = $db->fetchall();

	for($i=0;$i < count($_datas);$i++){
		$datas[$i][text] = GetParentCategory_2($_datas[$i][cid], $_datas[$i][depth]);
		$datas[$i][value] = $_datas[$i][cid];
	}

	$datas = json_encode($datas);
	$datas = str_replace("\"true\"","true",$datas);
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;

}

?>