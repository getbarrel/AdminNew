<?
function getFirstDIV($selected=""){
	global $admininfo;
	$mdb = new Database;
	return;
	$sql = 	"SELECT cr.*
			FROM shop_realestate_region cr
			where depth = 1
			order by vieworder asc ";

	$mdb->query($sql);

	$mstring = "<select name='parent_rg_ix' id='parent_rg_ix' disabled>";
	$mstring .= "<option value=''>1차지역</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[rg_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[rg_ix]."' selected>".$mdb->dt[region_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[rg_ix]."'>".$mdb->dt[region_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}

function realestate_tab($selected = "region"){

	$mstring = " <div class='tab'>
		<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<!--table id='tab_01' ".($selected == "md" ? "class='on'":"")." >
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='md_manage.php'>MD 목록</a></td>
						<th class='box_03'></th>
					</tr>
					</table-->
					<table id='tab_02' ".($selected == "region" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='region.php'>지역관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>

				</td>
			</tr>
			</table>
			</div>";

	return $mstring;
}




function getRegionInfoSelect($obj_id, $obj_txt, $parent_rg_ix, $selected, $depth=1, $property=""){
	global $admininfo;
	return;
	$mdb = new Database;
	if($depth == 1){
		$sql = 	"SELECT abg.*
				FROM shop_realestate_region abg
				where depth = '$depth'
				order by vieworder asc";
	}else if($depth == 2){
		$sql = 	"SELECT abg.*
				FROM shop_realestate_region abg
				where depth = '$depth' and parent_rg_ix = '$parent_rg_ix'
				order by vieworder asc ";
	}
	//echo $sql;
	$mdb->query($sql);

	$mstring = "<select name='$obj_id' id='$obj_id' $property>";
	$mstring .= "<option value=''>$obj_txt</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[rg_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[rg_ix]."' selected>".$mdb->dt[region_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[rg_ix]."'>".$mdb->dt[region_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}

?>