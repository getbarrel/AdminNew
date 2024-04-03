<?
$vdate = date("Ymd", time());
$today = date("Y-m-d", time());
$vyesterday = date("Y-m-d", time()+84600);
$voneweeklater = date("Y-m-d", time()+84600*7);
$vtwoweeklater = date("Y-m-d", time()+84600*14);
$vfourweeklater = date("Y-m-d", time()+84600*28);
$vyesterday = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24);
//$voneweeklater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*7);
$v15later = date("Y-m-d", time()+84600*15);;//date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*15);
//$vfourweeklater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*28);
$vonemonthlater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)+1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthlater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)+2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthlater = date("Y-m-d",mktime(0,0,0,substr($vdate,4,2)+3,substr($vdate,6,2)+1,substr($vdate,0,4)));

function getMainDiv($selected="", $return_type = "selectbox"){
	global $agent_type;
	$mdb = new Database;

	$sql = 	"SELECT *
			FROM shop_promotion_div 
			where disp=1 and agent_type = '".$agent_type."'";

	$mdb->query($sql);

	if($return_type == "selectbox"){
		$mstring = "<select name='div_ix' id='div_ix' validation=true title='프로모션 분류'>";
		$mstring .= "<option value=''>프로모션 분류</option>";
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