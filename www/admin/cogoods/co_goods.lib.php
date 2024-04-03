<?php

$db = new Database;
if($chs_ix){
	$sql = "SELECT * FROM co_client_hostservers where chs_ix = '".$chs_ix."'  ";
}else{
	$sql = "SELECT * FROM co_client_hostservers where basic = '1' order by regdate desc limit 1  ";
}
$db->query($sql); //where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'

if($db->total){
	$db->fetch();
	$chs_ix = $db->dt[chs_ix];
	$hostserver = $db->dt[server_url];
	$server_name = $db->dt[server_name];
}else{
	//echo "<script language='javascript'>alert('호스트 서버 설정후 판매사이트 설정이 가능합니다.');location.href='/admin/cogoods/hostserver.php';</script>";
	//exit;
}


function getHostServer($selected="", $property="onchange=\"location.href='?chs_ix='+this.value\""){
	global $admininfo;
	$mdb = new Database;

	$sql = 	"SELECT ch.*
			FROM co_client_hostservers ch
			where disp = 1 order by server_name asc ";

	$mdb->query($sql);

	$mstring = "<select name='chs_ix' id='chs_ix' $property >";
	$mstring .= "<option value=''>호스트 서버</option>";
	if($mdb->total){


		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[chs_ix] == $selected){
				$mstring .= "<option value='".$mdb->dt[chs_ix]."' selected>".$mdb->dt[server_name]."</option>";
			}else if($mdb->dt[basic] == '1'){
				$mstring .= "<option value='".$mdb->dt[chs_ix]."' selected>".$mdb->dt[server_name]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[chs_ix]."'>".$mdb->dt[server_name]."</option>";
			}
		}

	}
	$mstring .= "</select>";

	return $mstring;
}
