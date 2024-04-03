<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
$db = new Database;

//session_start();


if ($disp != 1){
	$disp = 0;
}

if ($mode == 'insert')
{
	$sql = "insert into shop_buying_company (bc_ix,cid,bc_name,building,floor,bc_no, bc_phone,disp) values('$bc_ix','$cid','$bc_name','$building','$floor','$bc_no','$bc_phone','$disp') ";

	$db->query($sql);
	$db->query("SELECT bc_ix FROM shop_buying_company WHERE bc_ix=LAST_INSERT_ID()");
	$db->fetch();
	
	header("Location:buying_company.php?mmode=$mmode");
}

if ($mode == "change")
{
//	echo("SELECT c_ix FROM ".TBL_SHOP_COMPANY." WHERE c_ix=$c_ix");
	$db->query("SELECT * FROM shop_buying_company WHERE bc_ix=$bc_ix");
	$db->fetch();

	if ($db->dt[disp] == 1){
		$checkString = "true";
	}else{
		$checkString = "false";
	}

	if ($db->dt[search_disp] == 1){
		$SearchCheckString = "true";
	}else{
		$SearchCheckString = "false";
	}
	echo "
	<Script Language='Javascript'>
		parent.document.forms['buying_company_frm'].bc_name.value = '".$db->dt[bc_name]."';
		parent.document.forms['buying_company_frm'].building.value = '".$db->dt[building]."';
		parent.document.forms['buying_company_frm'].floor.value = '".$db->dt[floor]."';
		parent.document.forms['buying_company_frm'].bc_no.value = '".$db->dt[bc_no]."';
		parent.document.forms['buying_company_frm'].bc_phone.value = '".$db->dt[bc_phone]."';
		parent.document.forms['buying_company_frm'].bc_ix.value = '".$db->dt[bc_ix]."';
		parent.document.forms['buying_company_frm'].disp.checked = $checkString;
		//parent.document.forms['buying_company_frm'].search_disp.checked = $SearchCheckString;
		parent.document.getElementById('modify').style.display = 'block';
		parent.document.getElementById('delete').style.display = 'block';
		parent.document.getElementById('ok').style.display = 'none';
		var obj = parent.document.forms['buying_company_frm'].cid;
		for(i=0;i<obj.length;i++){
			if(obj[i].value == '".$db->dt[cid]."'){
				obj[i].selected = true;
			}
		}
		";

		/*if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/company/company_".$db->dt[0].".gif")){
			echo "parent.document.getElementById('companyimgarea').innerHTML = \"<img src='".$admin_config[mall_data_root]."/images/company/company_".$db->dt[0].".gif'>\";";
		}else{
			echo "parent.document.getElementById('companyimgarea').innerHTML = \"제조사 이미지가 입력되지 않았습니다. \";";
		}*/
	echo "</Script>";

}

if ($mode == "update")
{
	// 사용하지 않기에 주석처리 함 2011-04-08 kbk
	/*if ($companyimg != "none")
	{
		copy($companyimg, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/company/company_$c_ix.gif");
	}*/
	//echo $mmode;
	$sql = "update shop_buying_company set 
			cid='$cid',bc_name='$bc_name',building='$building',floor='$floor',bc_phone='$bc_phone',bc_no='$bc_no',disp='$disp' 
			where bc_ix='$bc_ix' ";

	$db->query("$sql");

	if($mmode == "pop"){
	echo "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='pragma' content='no-cache'>
		<body>
		<div id='company_select_area'>
		".printBuyingCompany($bc_ix, $cid)."
		</div>
		</body>
		</html>
		<Script Language='Javascript'>
		parent.opener.document.getElementById('company_select_area').innerHTML = document.getElementById('company_select_area').innerHTML;
		parent.document.location.href='company.php?mmode=$mmode'
		</Script>";
	}else{
		header("Location:company.php?mmode=$mmode");
	}


}

if ($mode == "delete")
{
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/company/company_$c_ix.gif"))
	{
		unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/company/company_$c_ix.gif");
	}


	$db->query("DELETE FROM shop_buying_company WHERE bc_ix='$bc_ix'");

	if($mmode == "pop"){
	echo "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='pragma' content='no-cache'>
		<body>
		<div id='company_select_area'>
		".printBuyingCompany($bc_ix, $cid)."
		</div>
		</body>
		</html>
		<Script Language='Javascript'>
		parent.opener.document.getElementById('company_select_area').innerHTML = document.getElementById('company_select_area').innerHTML;
		parent.document.location.href='company.php?mmode=$mmode'
		</Script>";
	}else{
		header("Location:company.php?mmode=$mmode");
	}
}


function printBuyingCompany($bc_name, $cid, $return_type ="")
{
//global $db;

	$mdb = new Database;

	if($cid){
		$mdb->query("SELECT * FROM shop_buying_company where disp=1 and cid = '$cid'");
	}else{
		$mdb->query("SELECT * FROM shop_buying_company where disp=1 ");
	}

	$bl = "<Select name='buying_company' class=small>";
	if ($mdb->total == 0)	{
		$bl = $bl."<Option>등록된 사입업체가 없습니다.</Option>";
	}else{
		if($return_type == ""){
			$bl = $bl."<Option value=''>사입업체 선택</Option>";
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($bc_name == $mdb->dt[bc_name]){
					$strSelected = "Selected";
				}else{
					$strSelected = "";
				}

				$bl = $bl."<Option value='".$mdb->dt[bc_name]."' $strSelected>".$mdb->dt[bc_name]."</Option>";

			}
		}else{
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($brand == $mdb->dt[bc_ix]){
					return $mdb->dt[bc_name];
				}
			}
		}
	}

	$bl = $bl."</Select>";

	return $bl;
}