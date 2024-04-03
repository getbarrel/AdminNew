<?

function car_tab($selected = "manufacturer"){

	$mstring = " <div class='tab'>
		<table class='s_org_tab'>
			<tr>
				<td class='tab'>

					<table id='tab_01'  ".($selected == "manufacturer" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='manufacturer.php'>자동차/오토바이 제조사</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($selected == "vechile_type" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='vechile_type.php'>자동차/오토바이 유형</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($selected == "model" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='model.php?recommend=0'>자동차/오토바이 모델</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_04' ".($selected == "vechile_grade" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='vechile_grade.php'>자동차/오토바이 등급</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			</div>";

	return $mstring;
}


function VechileDiv($vechile_div){
$mstring = "
<div class='tab' style='height:35px;padding-bottom:12px;'>
		<table class='s_org_tab'>
			<tr>
				<td class='tab'>

					<table id='tab_01'  ".($vechile_div == "" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?vechile_div='>전체</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($vechile_div == "C" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?vechile_div=C'>자동차</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($vechile_div == "B" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='?vechile_div=B'>오토바이</a></td>
						<th class='box_03'></th>
					</tr>
					</table>

				</td>
			</tr>
		</table>
	</div>";

	return $mstring;

}


function makeManufacturerSelectBox($vechile_div, $select_name,$mf_ix='',  $display_name = '전체보기', $property="style='width:110px;font-size:11px;'"){
	global $admininfo;
	return;
	$mdb = new Database;
	$mdb->query("SELECT * FROM shop_car_manufacturer where disp=1 and vechile_div = '".$vechile_div."'  order by vieworder asc ");

	$mstring = "<select name='$select_name' id='$select_name'  $property>";
	if($display_name != ""){
		$mstring .= "<option value=''>".$display_name."</option>";
	}
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[mf_ix]."' ".($mdb->dt[mf_ix] == $mf_ix ? "selected":"").">".$mdb->dt[manufacturer_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>".$display_name."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}

function makeVechileTypeSelectBox($vechile_div, $select_name, $vt_ix='',  $display_name = '전체보기', $property="style='width:110px;font-size:11px;'"){
	global $admininfo;
	return;
	$mdb = new Database;
	$mdb->query("SELECT * FROM shop_car_vechiletype where disp=1 and vechile_div = '".$vechile_div."'  order by vieworder asc ");

	$mstring = "<select name='$select_name' id='$select_name'  $property>";
	if($display_name != ""){
		$mstring .= "<option value=''>".$display_name."</option>";
	}
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[vt_ix]."' ".($mdb->dt[vt_ix] == $vt_ix ? "selected":"").">".$mdb->dt[vechiletype_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>".$display_name."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}


function makeModelSelectBox($vechile_div, $select_name,$md_ix='', $mf_ix='', $display_name = '전체보기', $property="style='width:110px;font-size:11px;'"){
	global $admininfo;
	return;
	$mdb = new Database;
	if($mf_ix == "all"){
		$sql = "SELECT * FROM shop_car_model where disp=1 and vechile_div = '".$vechile_div."' order by vieworder asc ";
	}else{
		$sql = "SELECT * FROM shop_car_model where disp=1 and vechile_div = '".$vechile_div."'  and mf_ix = '".$mf_ix."' order by vieworder asc ";
	}
	$mdb->query($sql);

	$mstring = "<select name='$select_name' id='$select_name'  $property>";
	if($display_name != ""){
		$mstring .= "<option value=''>".$display_name."</option>";
	}
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[md_ix]."' ".($mdb->dt[md_ix] == $md_ix ? "selected":"").">".$mdb->dt[model_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>".$display_name."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}


function makeVechileGradeSelectBox($vechile_div, $select_name,$gr_ix='', $md_ix='',  $display_name = '전체보기', $property="style='width:110px;font-size:11px;'"){
	global $admininfo;
	return;
	$mdb = new Database;
	//if($md_ix != ""){
		$sql = "SELECT * FROM shop_car_grade where disp=1 and md_ix = '".$md_ix."' and vechile_div = '".$vechile_div."' order by vieworder asc ";
	//}else{
	//	$sql = "SELECT * FROM shop_car_grade where disp=1 order by vieworder asc ";
	//}
	$mdb->query($sql);

	$mstring = "<select name='$select_name' id='$select_name'  $property>";
	if($display_name != ""){
		$mstring .= "<option value=''>".$display_name."</option>";
	}
	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			$mstring .= "<option value='".$mdb->dt[gr_ix]."' ".($mdb->dt[gr_ix] == $gr_ix ? "selected":"").">".$mdb->dt[grade_name]."</option>";
		}
	}else{
		$mstring .= "<option value=''>".$display_name."</option>";
	}
	$mstring .= "</select>";

	return $mstring;
}

?>