<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');
$db = new Database;


if ($disp != 1){
	$disp = 0;
}

if ($mode == 'insert')
{
	$sql = $sql."INSERT INTO ".TBL_SNS_COMPANY." (c_ix, cid, company_name, disp,search_disp) values('', '$cid', '$company', '$disp', '$search_disp') ";

	$db->query($sql);
	$db->query("SELECT c_ix FROM ".TBL_SNS_COMPANY." WHERE c_ix=LAST_INSERT_ID()");
	$db->fetch();
	header("Location:company.php?mmode=$mmode");
}

if ($mode == "change")
{
//	echo("SELECT c_ix FROM ".TBL_SNS_COMPANY." WHERE c_ix=$c_ix");
	$db->query("SELECT * FROM ".TBL_SNS_COMPANY." WHERE c_ix=$c_ix");
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
		parent.document.forms['companyform'].company.value = '".$db->dt[company_name]."';
		parent.document.forms['companyform'].c_ix.value = '".$db->dt[c_ix]."';
		parent.document.forms['companyform'].disp.checked = $checkString;
		//parent.document.forms['companyform'].search_disp.checked = $SearchCheckString;
		parent.document.getElementById('modify').style.display = 'block';
		parent.document.getElementById('delete').style.display = 'block';
		parent.document.getElementById('ok').style.display = 'none';
		var obj = parent.document.forms['companyform'].cid;
		for(i=0;i<obj.length;i++){
			if(obj[i].value == '".$db->dt[cid]."'){
				obj[i].selected = true;
			}
		}
		";
	echo "</Script>";

}

if ($mode == "update")
{
	$db->query("UPDATE ".TBL_SNS_COMPANY." SET cid = '$cid',company_name = '$company', disp = '$disp',search_disp = '$search_disp' WHERE c_ix='$c_ix'");
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
		".MakerList($c_ix, $cid)."
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


	$db->query("DELETE FROM ".TBL_SNS_COMPANY." WHERE c_ix='$c_ix'");

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
		".MakerList($c_ix, $cid)."
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


function MakerList($company, $cid, $return_type ="")
{
//global $db;

	$mdb = new Database;

	if($cid){
		$mdb->query("SELECT * FROM ".TBL_SNS_COMPANY." where disp=1 and cid = '$cid'");
	}else{
		$mdb->query("SELECT * FROM ".TBL_SNS_COMPANY." where disp=1");
	}

	$bl = "<Select name='company' class=small>";
	if ($mdb->total == 0)	{
		$bl = $bl."<Option>등록된 제조사가 없습니다.</Option>";
	}else{
		if($return_type == ""){
			$bl = $bl."<Option value=''>제조사 선택</Option>";
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($company == $mdb->dt[company_name]){
					$strSelected = "Selected";
				}else{
					$strSelected = "";
				}

				$bl = $bl."<Option value='".$mdb->dt[company_name]."' $strSelected>".$mdb->dt[company_name]."</Option>";

			}
		}else{
			for($i=0 ; $i <$mdb->total ; $i++)
			{
				$mdb->fetch($i);
				if ($brand == $mdb->dt[c_ix]){
					return $mdb->dt[comapny_name];
				}
			}
		}
	}

	$bl = $bl."</Select>";

	return $bl;
}