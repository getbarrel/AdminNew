<?

/**
* 작  성  자 : 신훈식
* 작성 일자 :  2014년 03월 19일
* 내       용 : 메인전그룹 정보 리턴함수
*                  [2014-06-22] main_group name 과 id 값을 div_ix 로 변경 - SHS
*/
function getMainGroupInfo($selected="", $return_type = "selectbox"){
	global $agent_type;
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM shop_main_div
			where disp=1 and agent_type = '".$agent_type."' ORDER BY div_ix ASC ";

	$mdb->query($sql);
	if($return_type == "selectbox"){
			$mstring = "<select name='div_ix' id='div_ix' onchange=\"loadCategory(this,'mp_ix')\">";
			$mstring .= "<option value=''>메인전시그룹 </option>";
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


/**
* 작  성  자 : 신훈식
* 작성 일자 :  2014년 03월 19일
* 내       용 : 메인전그룹 위치 정보 리턴함수
*/

function getMainGroupPosition($div_ix, $mp_ix, $return_type = "selectbox"){
	global $admininfo;
	global $agent_type;

	$mdb = new Database;
	$sql = "SELECT * FROM shop_main_position where disp=1 and div_ix = '$div_ix' and agent_type = '".$agent_type."' ";

	$mdb->query($sql);

	if($return_type == "selectbox"){
			$mstring = "<select name='mp_ix' id='mp_ix'  $property>";

			if($mdb->total){
				$mstring .= "<option value=''>메인전시그룹 위치 선택</option>";
				for($i=0;$i < $mdb->total;$i++){
					$mdb->fetch($i);
					$mstring .= "<option value='".$mdb->dt[mp_ix]."' ".($mdb->dt[mp_ix] == $mp_ix ? "selected":"").">".$mdb->dt[mp_name]."</option>";
				}
			}else{
				$mstring .= "<option value=''>메인전시그룹 위치 선택</option>";
			}
			$mstring .= "</select>";
	}else{
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			//return $mp_ix;
			if($mdb->dt[mp_ix] == $mp_ix){
				$mstring .= "<input type=hidden name='mp_ix' value='".$mdb->dt[mp_ix]."' >".$mdb->dt[mp_name];

			}
		}
	}

	return $mstring;
}


function getMainDiv($selected="", $return_type = "selectbox"){
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM shop_main_div
			where disp=1 ";

	$mdb->query($sql);

	if($return_type == "selectbox"){
		$mstring = "<select name='div_ix' id='div_ix' validation=true title='카테고리메인 분류'>";
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

?>