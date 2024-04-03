<?


function getCategoryMainPositionSelectBox($select_name,$cmp_ix='', $property=''){
	global $admininfo;

	$mdb = new Database;

	$sql = "SELECT * FROM shop_category_main_position where disp=1  ";
	
	$mdb->query($sql);

	$mstring = "<select name='$select_name' id='$select_name'  $property>";
	$mstring .= "<option value=''>전시위치</option>";
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[cmp_ix]."' ".($mdb->dt[cmp_ix] == $cmp_ix ? "selected":"").">".$mdb->dt[cmp_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>".$display_name."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}


function getCategoryMainDiv($selected="", $return_type = "selectbox" ,$etc=""){
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM shop_category_main_div
			where disp=1 ";

	$mdb->query($sql);

	if($return_type == "selectbox"){
		$mstring = "<select name='div_ix' id='div_ix' validation=true title='카테고리메인 분류' ".$etc.">";
		$mstring .= "<option value=''>카테고리메인 분류</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[div_ix] == $selected){
					$mstring .= "<option value='".$mdb->dt[div_ix]."' selected>".$mdb->dt[div_name]."</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[div_ix]."'>".$mdb->dt[div_name]."</option>";
				}
			}

		}
		$mstring .= "</select>";
	}else{
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[div_ix] == $selected){
				$mstring .= "<input type=hidden name='div_ix' value='".$mdb->dt[div_ix]."' >".$mdb->dt[div_name];

			}
		}
	}

	return $mstring;
}


function getCategoryMainPosition($div_ix,$selected="", $return_type = "selectbox" , $name="display_position" ,$etc=""){
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM shop_category_main_position
			where disp=1 and div_ix='".$div_ix."' ";

	$mdb->query($sql);

	if($return_type == "selectbox"){
		$mstring = "<select name='".$name."' id='".$name."' validation=true title='전시위치' ".$etc.">";
		$mstring .= "<option value=''>전시위치</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[cmp_ix] == $selected){
					$mstring .= "<option value='".$mdb->dt[cmp_ix]."' selected>".$mdb->dt[cmp_name]."</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[cmp_ix]."'>".$mdb->dt[cmp_name]."</option>";
				}
			}

		}
		$mstring .= "</select>";
	}else{
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[cmp_ix] == $selected){
				$mstring .= "<input type=hidden name='".$name."' value='".$mdb->dt[cmp_ix]."' >".$mdb->dt[cmp_name];

			}
		}
	}

	return $mstring;
}

?>