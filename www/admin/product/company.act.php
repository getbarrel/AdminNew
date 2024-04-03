<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
$db = new Database;
$db2 = new Database;

session_start();

if ($disp != 1){
	$disp = 0;
}

if($act == "checkcompanyCode"){
	if($cp_code == ""){
		$result[bool] = false;
		$result[message] = "제조사 코드를 입력해주세요.";
		echo json_encode($result);
		exit;
	}

	$sql = "select * from shop_company where cp_code = '".$cp_code."' ";
	$db->query($sql);

	if($db->total){
		$result[bool] = false;
		$result[message] = "'".$cp_code."'는 이미 사용중인  코드입니다.";
	}else{
		$result[bool] = true;
		$result[message] = "사용하실수 있는 코드입니다.";
	}

	echo json_encode($result);
	exit;
}

if($update_kind == "category"){	//분류카테고리 일괄변경

	if($update_type == "2"){//선택한 회원
		if($cid2){
			if($update_category_type == "add"){	//카테고리추가
				if(count($cpid) > 0){
		
					for($i=0;$i<count($cpid);$i++){
						$sql = "insert into shop_company_relation set 
									cid = '".$cid2."',
									c_ix = '".$cpid[$i]."',
									disp='1',
									basic='0',
									insert_yn = 'Y',
									regdate = NOW();";
						$db->query($sql);
					}
				}
			}else if($update_category_type == "basic_add"){	//기본카테고리 변경
				if(count($cpid) > 0){
					for($i=0;$i<count($cpid);$i++){
						$sql = "select * from shop_company_relation where c_ix = '".$cpid[$i]."' and cid='".$cid2."'";
						$db->query($sql);
						$db->fetch();
						$brid = $db->dt[crid];

						$sql = "update shop_company_relation set basic = '0' where c_ix = '".$cpid[$i]."'";
						$db2->query($sql);

						if($db->total > 0){	//요청한 카테고리가 이미 있을경우 기존에 카테고리를 전부 0으로 수정한후 해당 카테고리만 1로 수정
							$sql = "update shop_company_relation set basic='1' where c_ix = '".$cpid[$i]."' and cid = '".$cid2."'";
							$db2->query($sql);
						}else{
							$sql = "insert into shop_company_relation set 
									cid = '".$cid2."',
									c_ix = '".$cpid[$i]."',
									disp='1',
									basic='1',
									insert_yn = 'Y',
									regdate = NOW();";
							$db2->query($sql);
						}
					
					}
				}
			}
		}else{
			if($update_category_type == "basic_del"){	//기본카테고리외 삭제
				if(count($cpid) > 0){
					for($i=0;$i<count($cpid);$i++){
						$sql = "delete from shop_company_relation where c_ix = '".$cpid[$i]."' and basic ='0'";
						$db->query($sql);
					}
				}
			}
		}

		echo "
		<Script Language='Javascript'>
		parent.document.location.reload();
		</Script>";
	}
}

if($update_kind == "bd_category"){	//브랜드분류 변경
	if($update_type == "2"){//선택한 회원
		if($bd_ix2){
			if(count($cpid) > 0){
				for($i=0;$i<count($cpid);$i++){
					$sql = "update shop_company set cd_ix = '".$bd_ix2."' where c_ix = '".$cpid[$i]."'";
					$db->query($sql);
				}	
			}
		echo "
		<Script Language='Javascript'>
		parent.document.location.reload();
		</Script>";
		}

	}
}

if ($mode == 'insert')
{
	
	if($_SESSION["admininfo"]["admin_level"] ==9){
		$status = "1";
	}else{
		$status = "2";
	}

	if($cd_ix=="") {//하위 카테고리 값이 없다면 상위 값을 입력 kbk 13/07/01
		$cd_ix=$parent_cd_ix;
	}

	//$zipcode = $zipcode1."-".$zipcode2;
	
	if($zipcode2 != "" || $zipcode2 != NULL){
		$zipcode = "$zipcode1-$zipcode2";
	}else{
		$zipcode = $zipcode1;
	}

	$sql = "INSERT INTO shop_company 
				(c_ix, cid, cd_ix,cp_code, company_name, disp, search_disp, company_id,cp_shotinfo,status,zipcode,addr1,addr2, regdate) 
				values
				('', '$cid', '$cd_ix', '$cp_code', '$company_name','$disp','$search_disp', '".$admininfo[company_id]."','$cp_shotinfo','$status','$zipcode','$addr1','$addr2',now()) ";//$bd_ix 추가 kbk 13/07/01

	$db->sequences = "SHOP_COMPANY_SEQ";
	$db->query($sql);

	if($db->dbms_type=='oracle'){
		$c_ix = $db->last_insert_id;
	}else{
		$c_ix = $db->insert_id();
	}

	for($i=0;$i<count($category);$i++){
		if($category[$i] == $basic){
			$category_basic = 1;
		}else{
			$category_basic = 0;
		}

		$db->sequences = "SHOP_GOODS_LINK_SEQ";
		$db->query("insert into shop_company_relation (crid, cid, c_ix, disp, basic,insert_yn, regdate ) values ('','".$category[$i]."','".$c_ix."','1','".$category_basic."','Y',NOW())");

	}

	if($mmode == "pop"){
	echo "
		<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='pragma' content='no-cache'>

		<body>
		<div id='brand_select_area'>
		".BrandListSelect($c_ix, $cid)."
		</div>
		</body>
		</html>
		<Script Language='Javascript'>
		parent.opener.document.getElementById('brand_select_area').innerHTML = document.getElementById('brand_select_area').innerHTML;
		parent.document.location.reload();
		</Script>";
	}else{
		echo "
		<Script Language='Javascript'>
		parent.document.location.reload();
		</Script>";
		//header("Location:brand.php?mmode=$mmode");
	}
}


if ($mode == "change")
{
//	echo("SELECT c_ix FROM ".TBL_SHOP_COMPANY." WHERE c_ix=$c_ix");
	$db->query("SELECT * FROM ".TBL_SHOP_COMPANY." WHERE c_ix=$c_ix");
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
		/*var obj = parent.document.forms['companyform'].cid;
		for(i=0;i<obj.length;i++){
			if(obj[i].value == '".$db->dt[cid]."'){
				obj[i].selected = true;
			}
		}*///카테고리 사용안함 kbk 12/01/19
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

	if($cd_ix=="") {//하위 카테고리 값이 없다면 상위 값을 입력 kbk 13/07/01
		$cd_ix=$parent_cd_ix;
	}
	//$zipcode = $zipcode1."-".$zipcode2;
	
	if($zipcode2 != "" || $zipcode2 != NULL){
		$zipcode = "$zipcode1-$zipcode2";
	}else{
		$zipcode = $zipcode1;
	}

	$sql = "UPDATE shop_company SET
			cid = '$cid',
			cd_ix = '$cd_ix', 
			company_name = '$company_name', 
			cp_code = '$cp_code', 
			disp = '$disp',
			search_disp = '$search_disp', 
			cp_shotinfo ='$cp_shotinfo',
			status = '$status',
			zipcode = '$zipcode',
			addr1 = '$addr1',
			addr2 = '$addr2'
			WHERE c_ix='$c_ix'";
		
	$db->query($sql);

	$db->query("update shop_company_relation set insert_yn = 'N' where c_ix = '$c_ix'");
	for($i=0;$i<count($category);$i++){
		if($category[$i] == $basic){
			$category_basic = 1;
		}else{
			$category_basic = 0;
		}
		$sql = "select crid from shop_company_relation where c_ix = '$c_ix' and cid = '".$category[$i]."' ";
		$db->query($sql);
		$db->fetch();
		if($db->total){
			$db->query("update shop_company_relation set insert_yn = 'Y' , basic='$category_basic' where crid = '".$db->dt[crid]."'");
		}else{
			$db->sequences = "SHOP_GOODS_LINK_SEQ";
			$db->query("insert into shop_company_relation (crid, cid, c_ix, disp, basic,insert_yn, regdate ) values ('','".$category[$i]."','".$c_ix."','1','".$category_basic."','Y',NOW())");
		}
	}

	$db->query("delete from shop_company_relation where c_ix = '$c_ix' and insert_yn = 'N'");

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

	$db->query("DELETE FROM ".TBL_SHOP_COMPANY." WHERE c_ix='$c_ix'");

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
		echo "
		<Script Language='Javascript'>
		parent.document.location.reload();
		</Script>";
	}
}


if($mode == "select_depth2"){
	if($cd_ix){
		$sql = "select * from shop_company_div where cd_ix = '".$cd_ix."' ";
		$db2->query($sql);
		$db2->fetch();
		$div1_name =  $db2->dt[div_name];

		$sql = "select * from shop_company_div where parent_cd_ix='".$cd_ix."'";
		$db->query($sql);
		$data_array = $db->fetchall();

		for($i=0;$i<count($data_array);$i++){
			$brand_info[$data_array[$i][cd_ix]] = $div1_name." > ".$data_array[$i][div_name];
		}

		$datas = $brand_info;
		$datas = json_encode($datas);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;

	}

}


function MakerList($company, $cid, $return_type ="")
{
//global $db;

	$mdb = new Database;

	if($cid){
		$mdb->query("SELECT * FROM ".TBL_SHOP_COMPANY." where disp=1 and cid = '$cid'");
	}else{
		$mdb->query("SELECT * FROM ".TBL_SHOP_COMPANY." where disp=1");
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