<?
include("$DOCUMENT_ROOT/class/layout.class");

if($act=="vip_update") {

$vip_delete_array = $_REQUEST[vip_delete];
$vvip_delete_array = $_REQUEST[vvip_delete];
$vvvip_delete_array = $_REQUEST[vvvip_delete];

if(count($vip_delete_array) > 0){
	for($i=0;$i<count($vip_delete_array); $i++){
		$sql = "update common_member_detail set level_ix = '1' ,level_change_date = NOW() where code = '".$vip_delete_array[$i]."'";
		$db->query($sql);
	}
}

if(count($vvip_delete_array) > 0){
	for($i=0;$i<count($vvip_delete_array); $i++){
		$sql = "update common_member_detail set level_ix = '1',level_change_date = NOW() where code = '".$vvip_delete_array[$i]."'";
		$db->query($sql);
	}
}

if(count($vvvip_delete_array) > 0){
	for($i=0;$i<count($vvvip_delete_array); $i++){
		$sql = "update common_member_detail set level_ix = '1',level_change_date = NOW() where code = '".$vvvip_delete_array[$i]."'";
		$db->query($sql);
	}
}

$vip_add_array = $_REQUEST[vip_list];
$vvip_add_array = $_REQUEST[vvip_list];
$vvvip_add_array = $_REQUEST[vvvip_list];

if(count($vip_add_array) > 0){
	for($i=0;$i<count($vip_add_array); $i++){
		$sql = "update common_member_detail set level_ix = '4',level_change_date = NOW() where code = '".$vip_add_array[$i]."'";
		$db->query($sql);
	}
}

if(count($vvip_add_array) > 0){
	for($i=0;$i<count($vvip_add_array); $i++){
		$sql = "update common_member_detail set level_ix = '5',level_change_date = NOW() where code = '".$vvip_add_array[$i]."'";
		$db->query($sql);
	}
}

if(count($vvvip_add_array) > 0){
	for($i=0;$i<count($vvvip_add_array); $i++){
		$sql = "update common_member_detail set level_ix = '6',level_change_date = NOW() where code = '".$vvvip_add_array[$i]."'";
		$db->query($sql);
	}
}

	echo("<script language='javascript'>alert('회원레벨이 정상적으로 처리되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'vip_add.php?info_type=add';</script>");
	//echo("<script language='javascript'>parent.self.close();</script>");
}


if($act == "black_update"){

$black1_delete_array = $_REQUEST[black_delete];
$black2_delete_array = $_REQUEST[black1_delete];
$black3_delete_array = $_REQUEST[black2_delete];

if(count($black1_delete_array) > 0){
	for($i=0;$i<count($black1_delete_array); $i++){
		$sql = "update common_member_detail set level_ix = '1' ,level_change_date = NOW() where code = '".$black1_delete_array[$i]."'";
		$db->query($sql);
	}
}

if(count($black2_delete_array) > 0){
	for($i=0;$i<count($black2_delete_array); $i++){
		$sql = "update common_member_detail set level_ix = '1',level_change_date = NOW() where code = '".$black2_delete_array[$i]."'";
		$db->query($sql);
	}
}

if(count($black3_delete_array) > 0){
	for($i=0;$i<count($black3_delete_array); $i++){
		$sql = "update common_member_detail set level_ix = '1',level_change_date = NOW() where code = '".$black3_delete_array[$i]."'";
		$db->query($sql);
	}
}


$black_add_array = $_REQUEST[black_list];
$black1_add_array = $_REQUEST[black1_list];
$black2_add_array = $_REQUEST[black2_list];

if(count($black_add_array) > 0){
	for($i=0;$i<count($black_add_array); $i++){
		$sql = "update common_member_detail set level_ix = '7',level_change_date = NOW() where code = '".$black_add_array[$i]."'";

		echo "$sql";
		$db->query($sql);
	}
}

if(count($black1_add_array) > 0){
	for($i=0;$i<count($black1_add_array); $i++){
		$sql = "update common_member_detail set level_ix = '8',level_change_date = NOW() where code = '".$black1_add_array[$i]."'";
		$db->query($sql);
	}
}

if(count($black2_add_array) > 0){
	for($i=0;$i<count($black2_add_array); $i++){
		$sql = "update common_member_detail set level_ix = '9',level_change_date = NOW() where code = '".$black2_add_array[$i]."'";
		$db->query($sql);
	}
}

	echo("<script language='javascript'>alert('회원레벨이 정상적으로 처리되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'black_add.php?info_type=add';</script>");
	//echo("<script language='javascript'>parent.self.close();</script>");


}

if($act=="get_participatnt") {
	echo json_encode(get_participation($pid, $status));
}

function get_participation($pid, $status = ""){

	$mdb = new Database;
	if($status == "complete"){
		$sql="select 
				distinct uid, bmobile  
			from 
				".TBL_SHOP_ORDER." o 
			left join 
				".TBL_SHOP_ORDER_DETAIL." od 
			on 
				o.oid = od.oid 
			left join 
				".TBL_COMMON_USER." c 
			on 
				c.code = o.uid	
			where 
				od.status NOT IN ('SR','IR','FA','FC','CA','CC') 
			and 
				pid = '".$pid."' 
			order by 
				o.date desc  ";
	}else if($status == "ready"){
		$sql="select 
			distinct uid, bmobile  
			from 
				".TBL_SHOP_ORDER." o	
			left join 
				".TBL_SHOP_ORDER_DETAIL." od 
			on 
				o.oid = od.oid 
			left join 
				".TBL_COMMON_USER." c 
			on 
				c.code = o.uid 	
			where 
				od.status IN ('IR') 
			and 
				pid = '".$pid."' 
			order by 
				o.date desc  ";
	}else{
		$sql="select 
			distinct uid, bmobile  
			from 
				".TBL_SHOP_ORDER." o 
			left join 
				".TBL_SHOP_ORDER_DETAIL." od 
			on o.oid = od.oid 
			left join 
				".TBL_COMMON_USER." c 
			on c.code = o.uid	
			where 
				od.status NOT IN ('FA','FC','CA','CC','SR') 
			and 
				pid = '".$pid."' 
			order by 
				o.date desc  ";
	}
	//echo $sql;
	$mdb->query($sql);
	if($mdb->total){
		$participation = $mdb->fetchall();
		if(is_array($participation)){
		foreach($participation as $key => $value):
			$name_id = getNameIdByCode($value["uid"]);
			if(!empty($name_id)){
				$add_data["name"] = $name_id["name"];
				$add_data["id"] = $name_id["id"];
				
			}
			$add_data["mobile"] = $value["bmobile"];
			$participation[$key] = $participation[$key] + $add_data;
		endforeach;
		}          
	}else{
		$participation = null;
	}
	return $participation;
}

?>

