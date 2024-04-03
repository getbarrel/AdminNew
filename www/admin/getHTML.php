<?php
/**
 * ajax 등으로 불러올 데이터
 *
 * @author		Caesar <ddong0927@naver.com>
 * @copyright	2007-2011 ForBiz
 * @version		1.0
 * @package
 */
require_once($_SERVER['DOCUMENT_ROOT'].'/admin/class/layout.class');
$db = new Database;

switch($act)	{
	case 'companyList':
		$company_id		= (empty($_POST['company_id']))	?	((empty($_GET['company_id']))	?	$_GET['company_id']:''):$_POST['company_id'];
		$dispaly_type	= (empty($_POST['dispaly_type']))	?	((empty($_GET['dispaly_type']))	?	$_GET['dispaly_type']:''):$_POST['dispaly_type'];
		$onchange		= (empty($_POST['onchange']))	?	((empty($_GET['onchange']))	?	$_GET['onchange']:''):$_POST['onchange'];

		if(!$admin_config['mall_use_multishop'])	exit;
		if($admininfo['admin_level'] != 9)	exit;
		if($onchange == 'basic')	{
			$onchange = " onchange=\"document.location.href = '{$_SERVER['HTTP_URL']}?company_id='+this.value\"";
		}
		/*
		$db->query("SELECT company_id, com_name FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE com_type = 'A' ORDER BY com_name ");
		$list = $db->fetchall();
		echo '<select name="company_id" id="company_id" style="'.$display_type.' height:20px;width:102px;font-size:11px;"'.$onchange.'>';
		if($admininfo['language'] == "korea"){
			echo '<option value="">전체보기</option>';
		}else if($admininfo['language'] == "english"){
			echo '<option value="">All</option>';
		}else if($admininfo['language'] == "indonesian"){
			echo '<option value="">Kendali</option>';
		}
		foreach($list as $_key=>$_val)	{
			$sel = ($company_id == $_val['company_id'])	?	' selected':'';
			echo '<option value="'.$_val['company_id'].'"'.$sel.'>'.$_val['com_name'].'</option>';
		}
		echo '</select>';
		*/
		echo CompanyList2($company_id);
	break;


	case 'mallixList':

		if($admininfo['admin_level'] != 9)	exit;
		echo GetDisplayDivision($mall_ix, "select");
	break;
}

?>
