<?
$_buyingservice_status["BI"] = "사입서작성중";
$_buyingservice_status["IR"] = "입금예정";
$_buyingservice_status["IC"] = "입금확인";
$_buyingservice_status["CC"] = "취소확인";

$_buyingservice_detail_status["BI"] = "사입중";
$_buyingservice_detail_status["BC"] = "사입완료";
$_buyingservice_detail_status["CC"] = "취소완료";
$_buyingservice_detail_status["WR"] = "입고대기";
$_buyingservice_detail_status["SC"] = "품절취소";
$_buyingservice_detail_status["DI"] = "배송중";
$_buyingservice_detail_status["DC"] = "배송완료";


$_division[BA] = "사입요청";
$_division[EA] = "교환요청";
$_division[RA] = "반품요청";
$_division[SR] = "샘플반납";

$db = new Database;

$db->query("SELECT * FROM buyingservice_floorline_area WHERE type='F' and disp='1' order by name asc ");

for($i=0;$i < $db->total;$i++){

	$db->fetch($i);
	$_floor_info[$db->dt[code]]= $db->dt[name];

}

$db->query("SELECT * FROM buyingservice_floorline_area WHERE type='L' and language_type='E' and disp='1' order by name asc ");

for($i=0;$i < $db->total;$i++){

	$db->fetch($i);
	$_line_info_english[$db->dt[code]]= $db->dt[name];

}

$db->query("SELECT * FROM buyingservice_floorline_area WHERE type='L' and language_type='K' and disp='1' order by name asc ");

for($i=0;$i < $db->total;$i++){

	$db->fetch($i);
	$_line_info_korea[$db->dt[code]]= $db->dt[name];

}


function getBuyingServiceSupplierInfo($i=0, $selected="", $type="input"){
	global $admininfo;
	$mdb = new Database;

	//echo $seelcted;

	$sql = 	"SELECT * FROM buyingservice_wholesaler
				where disp = 1 order by regdate desc ";

	$mdb->query($sql);

	$mstring = "<select name=\"buying_infos[".$i."][ws_ix]\" id='ws_ix' title='도매처정보' ".($type == "input" ? "validation='true'":"validation='false'").">";
	$mstring .= "<option value=''>도매처정보</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[ws_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[ws_ix]."' selected>".$mdb->dt[ws_name]." </option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[ws_ix]."'>".$mdb->dt[ws_name]." </option>";
			}
		}

	}
	$mstring .= "</select>";


	return $mstring;
}

function getBuyingServiceDivisionInfo($i=0, $selected="", $type="input"){

	$mstring = "<select name=\"buying_infos[".$i."][division]\" id='division' title='상품구분' ".($type == "input" ? "validation='false'":"validation='false'").">";
	//$mstring .= "<option value=''>상품구분</option>";
	$mstring .= "<option value='BA' ".($selected == 'BA' ? 'selected': '').">사입요청</option>";
	$mstring .= "<option value='EA' ".($selected == 'EA' ? 'selected': '').">교환요청</option>";
	$mstring .= "<option value='RA' ".($selected == 'RA' ? 'selected': '' ).">반품요청</option>";
	$mstring .= "<option value='SR' ".($selected == 'SR' ? 'selected':'' ).">샘플반납</option>";
	$mstring .= "</select>";

	return $mstring;
}


function getCommercialAreaInfo($ca_country="",$selected="", $type="input"){
	global $admininfo;
	$mdb = new Database;

	//echo $seelcted;
	if($type == "array"){
		$sql = 	"SELECT * FROM buyingservice_commercial_area
				where disp = 1 and ca_country = '$ca_country' order by regdate desc ";

		$mdb->query($sql);
		$currencys = $mdb->fetchall("object");
		for($i=0;$i < count($currencys);$i++){
			if($mdb->dt[ca_ix] == $selected){
			$__currencys[$currencys[$i]["ca_ix"]]["ca_name_".$_SESSION["admininfo"]["language"]] = $currencys[$i]["ca_name_".$_SESSION["admininfo"]["language"]];
			$__currencys[$currencys[$i]["ca_ix"]]["basic_currency"] = $currencys[$i]["basic_currency"];
			}
		}

		return $__currencys;
	}else{
		$sql = 	"SELECT * FROM buyingservice_commercial_area
					where disp = 1 and ca_country = '$ca_country' order by regdate desc ";

		$mdb->query($sql);

		$mstring = "<select name=\"ca_ix\" id='ca_ix' title='상권정보' ".($type == "input" ? "validation='true'":"validation='false'").">";
		$mstring .= "<option value=''>상권정보</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[ca_ix] == $selected){
					$mstring .= "<option value='".$mdb->dt[ca_ix]."' selected>".$mdb->dt["ca_name_".$_SESSION["admininfo"]["language"]]." </option>";
				}else if(($mdb->dt[is_basic] == "Y" && $selected == "" && $type == "input")){
					$mstring .= "<option value='".$mdb->dt[ca_ix]."' selected>".$mdb->dt["ca_name_".$_SESSION["admininfo"]["language"]]." </option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[ca_ix]."'>".$mdb->dt["ca_name_".$_SESSION["admininfo"]["language"]]." </option>";
				}
			}

		}
		$mstring .= "</select>";
	}

	return $mstring;
}

function getSoapCommercialAreaInfo($ca_country="",$selected="", $property="", $type="input"){
	global $admininfo;
	$mdb = new Database;

	//echo $seelcted;
	if($type == "array"){
		$sql = 	"SELECT * FROM buyingservice_commercial_area
				where disp = 1 and ca_country = '$ca_country' order by regdate desc ";

		$mdb->query($sql);
		$currencys = $mdb->fetchall("object");
		for($i=0;$i < count($currencys);$i++){
			if($mdb->dt[ca_code] == $selected){
			$__currencys[$currencys[$i]["ca_code"]]["ca_name_".$_SESSION["admininfo"]["language"]] = $currencys[$i]["ca_name_".$_SESSION["admininfo"]["language"]];
			$__currencys[$currencys[$i]["ca_code"]]["basic_currency"] = $currencys[$i]["basic_currency"];
			}
		}

		return $__currencys;
	}else{
		$sql = 	"SELECT * FROM buyingservice_commercial_area
					where disp = 1 and ca_country = '$ca_country' order by regdate desc ";

		$mdb->query($sql);

		$mstring = "<select name=\"ca_code\" id='ca_code' title='상권정보' ".($type == "input" ? "validation='true'":"validation='false'")." $property >";
		$mstring .= "<option value=''>상권정보</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[ca_code] == $selected){
					$mstring .= "<option value='".$mdb->dt[ca_code]."' selected>".$mdb->dt["ca_name_".$_SESSION["admininfo"]["language"]]." </option>";
				}else if(($mdb->dt[is_basic] == "Y" && $selected == "" && $type == "input")){
					$mstring .= "<option value='".$mdb->dt[ca_code]."' selected>".$mdb->dt["ca_name_".$_SESSION["admininfo"]["language"]]." </option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[ca_code]."'>".$mdb->dt["ca_name_".$_SESSION["admininfo"]["language"]]." </option>";
				}
			}

		}
		$mstring .= "</select>";
	}

	return $mstring;
}


function getShoppingCenter($selected="", $type="select",$property=""){
	global $admininfo;
	$mdb = new Database;
	$sql = "SELECT * FROM buyingservice_shopping_center 
			  where disp = 1 order by regdate desc ";

		$mdb->query($sql);
	//echo $seelcted;
	if($type == "array"){		
		$wholsaler = $mdb->fetchall("object");
		for($i=0;$i < count($wholsaler);$i++){
			if($mdb->dt[sc_ix] == $selected){
				$wholsaler[$i]["sc_name"];			
			}
		}

		return $wholsaler;
	}else{	
		$mstring = "<select name=\"sc_ix\" id='sc_ix' title='상가' $property >";
		$mstring .= "<option value=''>상가 선택</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[sc_ix] == $selected){
					$mstring .= "<option value='".$mdb->dt[sc_ix]."' selected>".$mdb->dt[sc_name]." </option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[sc_ix]."'>".$mdb->dt[sc_name]." </option>";
				}
			}

		}
		$mstring .= "</select>";
	}

	return $mstring;
}

function getSoapShoppingCenter($selected="", $type="select",$property=""){
	global $admininfo;
	$mdb = new Database;
	$sql = "SELECT * FROM buyingservice_shopping_center 
			  where disp = 1 order by regdate desc ";

		$mdb->query($sql);
	//echo $seelcted;
	if($type == "array"){		
		$wholsaler = $mdb->fetchall("object");
		for($i=0;$i < count($wholsaler);$i++){
			if($mdb->dt[sc_code] == $selected){
				$wholsaler[$i]["sc_name"];			
			}
		}

		return $wholsaler;
	}else{	
		$mstring = "<select name=\"sc_code\" id='sc_code' title='상가' $property >";
		$mstring .= "<option value=''>상가 선택</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[sc_code] == $selected){
					$mstring .= "<option value='".$mdb->dt[sc_code]."' selected>".$mdb->dt["sc_name_".$_SESSION["admininfo"]["language"]]." </option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[sc_code]."'>".$mdb->dt["sc_name_".$_SESSION["admininfo"]["language"]]." </option>";
				}
			}

		}
		$mstring .= "</select>";
	}

	return $mstring;
}


function getShoppingCenterFloorInfo($sc_ix, $selected="", $type="select",$property=""){
	global $admininfo;
	$mdb = new Database;
	$sql = "SELECT * FROM buyingservice_shopping_center_floor_info 
			  where sc_ix = '".$sc_ix."' order by regdate desc ";

		$mdb->query($sql);
	//echo $seelcted;
	if($type == "array"){		
		$wholsaler = $mdb->fetchall("object");
		for($i=0;$i < count($wholsaler);$i++){
			if($mdb->dt[sc_ix] == $selected){
				$wholsaler[$i]["sc_name"];			
			}
		}

		return $wholsaler;
	}else{	
		$mstring = "<select name=\"floor\" id='floor' title='층' ".$property." >";
		$mstring .= "<option value=''>층 선택</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[floor] == $selected){
					$mstring .= "<option value='".$mdb->dt[floor]."' selected>".$mdb->dt[floor]." 층</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[floor]."'>".$mdb->dt[floor]." 층</option>";
				}
			}

		}
		$mstring .= "</select>";
	}

	return $mstring;
}

function getSoapShoppingCenterFloorInfo($sc_code, $selected="", $type="select",$property=""){
	global $admininfo;
	$mdb = new Database;

	$sql = "SELECT * FROM buyingservice_shopping_center 
				where sc_code = '$sc_code'  ";
	$mdb->query($sql);
	$mdb->fetch();
	$sc_ix=$mdb->dt["sc_ix"];

	$sql = "SELECT * FROM buyingservice_shopping_center_floor_info 
			  where sc_ix = '".$sc_ix."' order by regdate desc ";

		$mdb->query($sql);
	//echo $seelcted;
	if($type == "array"){		
		$wholsaler = $mdb->fetchall("object");
		for($i=0;$i < count($wholsaler);$i++){
			if($mdb->dt[sc_ix] == $selected){
				$wholsaler[$i]["sc_name"];			
			}
		}

		return $wholsaler;
	}else{	
		$mstring = "<select name=\"floor\" id='floor' title='층' ".$property." >";
		$mstring .= "<option value=''>층 선택</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[floor] == $selected){
					$mstring .= "<option value='".$mdb->dt[floor]."' selected>".$mdb->dt[floor]." 층</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[floor]."'>".$mdb->dt[floor]." 층</option>";
				}
			}

		}
		$mstring .= "</select>";
	}

	return $mstring;
}

function getShoppingCenterLineInfo($sc_ix, $selected="", $type="select",$property=""){
	global $admininfo;
	$mdb = new Database;
	$sql = "SELECT * FROM buyingservice_shopping_center_line_info 
			  where sc_ix = '".$sc_ix."' order by line asc ";

		$mdb->query($sql);
	//echo $seelcted;
	if($type == "array"){		
		$wholsaler = $mdb->fetchall("object");
		for($i=0;$i < count($wholsaler);$i++){
			if($mdb->dt[sc_ix] == $selected){
				$wholsaler[$i]["line"];			
			}
		}

		return $wholsaler;
	}else{	
		$mstring = "<select name=\"line\" id='line' title='라인' ".$property." >";
		$mstring .= "<option value=''>라인 선택</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[line] == $selected){
					$mstring .= "<option value='".$mdb->dt[line]."' selected>".$mdb->dt[line]." 라인</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[line]."'>".$mdb->dt[line]." 라인</option>";
				}
			}

		}
		$mstring .= "</select>";
	}

	return $mstring;
}

function getSoapShoppingCenterLineInfo($sc_code, $selected="", $type="select",$property=""){
	global $admininfo;
	$mdb = new Database;

	$sql = "SELECT * FROM buyingservice_shopping_center 
				where sc_code = '$sc_code'  ";
	$mdb->query($sql);
	$mdb->fetch();
	$sc_ix=$mdb->dt["sc_ix"];

	$sql = "SELECT * FROM buyingservice_shopping_center_line_info 
			  where sc_ix = '".$sc_ix."' order by line asc ";

		$mdb->query($sql);
	//echo $seelcted;
	if($type == "array"){		
		$wholsaler = $mdb->fetchall("object");
		for($i=0;$i < count($wholsaler);$i++){
			if($mdb->dt[sc_ix] == $selected){
				$wholsaler[$i]["line"];			
			}
		}

		return $wholsaler;
	}else{	
		$mstring = "<select name=\"line\" id='line' title='라인' ".$property." >";
		$mstring .= "<option value=''>라인 선택</option>";
		if($mdb->total){


			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				if($mdb->dt[line] == $selected){
					$mstring .= "<option value='".$mdb->dt[line]."' selected>".$mdb->dt[line]." 라인</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[line]."'>".$mdb->dt[line]." 라인</option>";
				}
			}

		}
		$mstring .= "</select>";
	}

	return $mstring;
}

function getSoapShoppingCenterNoInfo($sc_code, $selected="", $type="select"){
	$mdb = new Database;

	$sql = "SELECT * FROM buyingservice_shopping_center
			  where sc_code = '".$sc_code."' order by regdate desc ";

	$mdb->query($sql);

	$mstring = "<select name=\"no\" id='no' title='호수' ".$property." >";
	$mstring .= "<option value=''>호수 선택</option>";
	if($mdb->total){

		$mdb->fetch();
		for($i=$mdb->dt[start_no];$i <= $mdb->dt[end_no];$i++){
			
			if($i == $selected){
				$mstring .= "<option value='".$i."' selected>".$i." 호 </option>";
			}else{
				$mstring .= "<option value='".$i."'>".$i." 호 </option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}

function getShoppingCenterNoInfo($sc_ix, $selected="", $type="select"){
	$mdb = new Database;
	$sql = "SELECT * FROM buyingservice_shopping_center
			  where sc_ix = '".$sc_ix."' order by regdate desc ";

	$mdb->query($sql);

	$mstring = "<select name=\"no\" id='no' title='호수' ".$property." >";
	$mstring .= "<option value=''>호수 선택</option>";
	if($mdb->total){

		$mdb->fetch();
		for($i=1;$i <= $mdb->dt[no];$i++){
			
			if($i == $selected){
				$mstring .= "<option value='".$i."' selected>".$i." 호 </option>";
			}else{
				$mstring .= "<option value='".$i."'>".$i." 호 </option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}


function getCommercialCountry($ca_country=""  ,$return_type="select", $property="" , $validation="true"){
	
	if($return_type=="select"){
		$mstring = "<select name='ca_country' id='ca_country' $property validation='$validation' title='상권국가'>
				<option value=''>국가선택</option>
				<option value='korea' ".($ca_country == "korea" ? "selected":"").">한국</option>
				<option value='english' ".($ca_country == "english" ? "selected":"").">미국</option>
				<option value='chinese' ".($ca_country == "chinese" ? "selected":"").">중국</option>
				<!--option value='indonesian' ".($ca_country == "indonesian" ? "selected":"").">인도네시아</option>
				<option value='japan' ".($ca_country == "japan" ? "selected":"").">일본</option-->
			 </select>";
	}elseif($return_type=="text"){
		if($ca_country == "korea")				return "한국";
		elseif($ca_country == "english")		return "미국";
		elseif($ca_country == "chinese")		return "중국";
	}

	return $mstring;
}

?>