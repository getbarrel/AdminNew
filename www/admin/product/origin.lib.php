<?

function origin_tab($selected = "list"){

	$mstring = " <div class='tab'>
		<table class='s_org_tab'>
			<tr>
				<td class='tab'>

					<table id='tab_01'  ".($selected == "list" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='origin_list.php?mmode=".$_GET["mmode"]."'>원산지 리스트</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($selected == "reg" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='origin.php?mmode=".$_GET["mmode"]."'>원산지 등록</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_04' ".($selected == "div" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='origin_div.php?mmode=".$_GET["mmode"]."'>원산지분류</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_05' ".($selected == "batch" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='origin_batch.php'>원산지 일괄등록</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
			</tr>
			</table>
			</div>";

	return $mstring;
}


function getOriginDivSelect($obj_id, $obj_txt, $parent_od_ix, $od_ix, $depth=1, $property=""){
	$db = new Database;
	$mdb = new Database;
	if($depth == 1){
		$sql = 	"SELECT bd.*
				FROM common_origin_div bd
				where depth = '$depth'
				order by vieworder asc
				";

	}else if($depth == 2){
		$sql = 	"SELECT bd.*
				FROM common_origin_div bd
				where depth = '$depth' and parent_od_ix = '$parent_od_ix'
				order by vieworder asc 
				";
	}

	$mdb->query($sql);

	$mstring = "<select name='$obj_id' id='$obj_id' $property>";
	$mstring .= "<option value=''>$obj_txt</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			if($depth == 1){
				$sql = "select
						if(parent_od_ix > 0,parent_od_ix,od_ix) as od_ix
						from	
							common_origin_div
						where
							od_ix = '".$od_ix."'";
				$db->query($sql);
				$db->fetch();
				$od_ix = $db->dt[od_ix];
			}

			if($mdb->dt[od_ix] == $od_ix){
				$mstring .= "<option value='".$mdb->dt[od_ix]."' selected>".$mdb->dt[div_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[od_ix]."'>".$mdb->dt[div_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}


function getFirstDIV($selected=""){
	global $admininfo;
	$mdb = new Database;
	
	$sql = 	"SELECT cr.*
			FROM common_origin_div cr
			where depth = 1
			order by vieworder asc ";

	$mdb->query($sql);

	$mstring = "<select name='parent_od_ix' id='parent_od_ix' disabled>";
	$mstring .= "<option value=''>1차 분류</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[od_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[od_ix]."' selected>".$mdb->dt[div_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[od_ix]."'>".$mdb->dt[div_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}

/*
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
*/
?>