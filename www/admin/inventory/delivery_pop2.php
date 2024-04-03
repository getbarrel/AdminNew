<?
include("../../class/database.class");
	$sdb = new Database;
	$sdb->query("SELECT * FROM inventory_info where inventory_code != '".$inventory_code."' ");
	
	$mstring = "<select name='inventory_info2' class=small style='width:150px;'>";
	
		if($sdb->total){
			for($i=0;$i < $sdb->total;$i++){
				$sdb->fetch($i);
				$mstring .= "<option value='".$sdb->dt[inventory_code]."'>".$sdb->dt[inventory_name]."</option>";
			}
		}else{
			$mstring .= "<option value=''>".$msg."</option>";
		}
		$mstring .= "</select>";
	
	
	echo $mstring;

?>