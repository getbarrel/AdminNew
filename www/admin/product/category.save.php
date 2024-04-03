<?php
include("../class/layout.class");
////////////////////
//  2013.05.07 신훈식
//  수정 : 인클루드 패스 오류
//
/////////////////////
//include("../class/database.class");
include_once('../../include/xmlWriter.php');
session_start();

$db = new Database;
$db2 = new Database;

if($mode == "add_field"){
	for($i=1;$i<=10;$i++){
		$f_name = $_POST['etc'.$i];
		$f_ename = $_POST['etc'.$i.'_ename'];
		$f_type = $_POST['etc'.$i.'_type'];
		$f_search = $_POST['etc'.$i.'_search'];
		$f_value = $_POST['etc'.$i.'_value'];
		//echo $f_name."aa<br>";


		if($f_name != ""){
			$db->query("select * from shop_category_addfield where cid = '$cid' and f_code ='etc".$i."' ");
			if($db->total){
				$db->fetch();
				$db->query("update shop_category_addfield set  f_search = '$f_search',f_name = '$f_name', f_ename = '$f_ename', f_type = '$f_type',f_value = '$f_value'  where f_ix ='".$db->dt[f_ix]."' ");
			}else{
				$db->query("insert into shop_category_addfield (f_ix, cid, f_code, f_search, f_ename, f_name, f_type, f_value,regdate) values ('','$cid','etc".$i."','$f_search','$f_ename','$f_name','$f_type','$f_value',NOW() ) ");
			}
		}else{
			$db->query("delete from shop_category_addfield where cid = '$cid' and f_code ='etc".$i."' ");
		}
	}

	echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";
}



if($mode == "infoupdate"){
	$language_list = getTranslationType("","","array");

	$sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where cid = '$cid'";
	$db->query($sql);
	//echo $sql;
	if($db->total){
		$db->fetch();

		//echo $db->dt[category_use] ;

		if($db->dt[category_use] == 1){
			$category_use = "true";
		}else{
			$category_use = "false";
		}

		if($db->dt[category_type] == 'C'){
			$category_type_index = "0";
		}else{
			$category_type_index = "1";
		}

		$mstring ="
		<html>
		<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
		<body>
		<div id='category_top_view_area'>
		".$db->dt[category_top_view]."
		</div>
		</body>
		</html>
		<script language='JavaScript' src='/admin/_language/language.php'></Script>
		<script language='JavaScript' src='/admin/js/jquery-1.4.js'></Script>
		<script>
		var category_type = '".$db->dt[category_type]."';

		parent.document.forms['thisCategoryform'].category_use.checked = $category_use;
		parent.document.forms['thisCategoryform'].category_type[".$category_type_index."].checked = true;

		
		parent.document.forms['thisCategoryform'].hscode.value = '".$db->dt[hscode]."';
 
		//parent.document.forms['thisCategoryform'].basic_weight.value = '".$db->dt[basic_weight]."';
		$('form[name=thisCategoryform]', parent.document).find('input[name=basic_weight]').val('".$db->dt[basic_weight]."');
 
		if(category_type == 'C'){
			$('#category_display_link', parent.document).show();
			$('#category_link_box', parent.document).hide();
			$('#category_link', parent.document).val('".$db->dt[category_link]."');
		}else{
			$('#category_display_link', parent.document).hide();
			$('#category_link_box', parent.document).show();
			$('#category_link', parent.document).val('".$db->dt[category_link]."');
		}";


		$global_cinfo = json_decode($db->dt[global_cinfo],true);
		
		if(count($global_cinfo['cname']) > 0){
			foreach($global_cinfo['cname'] as $key => $li){

				$_global_cname = urldecode($li);

				$mstring .="$('#global_this_category_".$key."', parent.document).val('".$_global_cname."');";
			}
		}else{
			foreach($language_list as $key => $li){
				$mstring .="$('#global_this_category_".$li[language_code]."', parent.document).val('');";
			}
		}


		if($db->dt[catimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg])){
			$mstring .="parent.document.getElementById('category_img_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg]."' border=0>\";";
		}else{
			$mstring .="parent.document.getElementById('category_img_area').innerHTML = \"\";";
		}

		if($db->dt[catimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg_on])){
			$mstring .="parent.document.getElementById('category_img_on_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg_on]."' border=0>\";";
		}else{
			$mstring .="parent.document.getElementById('category_img_on_area').innerHTML = \"\";";
		}

		if($db->dt[leftcatimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg])){
			$mstring .="parent.document.getElementById('leftcategory_img_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg]."' border=0>\";";
		}else{
			$mstring .="parent.document.getElementById('leftcategory_img_area').innerHTML = \"\";";
		}

		if($db->dt[leftcatimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg_on])){
			$mstring .="parent.document.getElementById('leftcategory_img_on_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg_on]."' border=0>\";";
		}else{
			$mstring .="parent.document.getElementById('leftcategory_img_on_area').innerHTML = \"\";";
		}

//우측 메뉴 이미지 추가 시작 2013-11-26 이학봉
		if($db->dt[rightcatimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg])){
			$mstring .="parent.document.getElementById('rightcategory_img_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg]."' border=0>\";";
		}else{
			$mstring .="parent.document.getElementById('rightcategory_img_area').innerHTML = \"\";";
		}

		if($db->dt[rightcatimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg_on])){
			$mstring .="parent.document.getElementById('rightcategory_img_on_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg_on]."' border=0>\";";
		}else{
			$mstring .="parent.document.getElementById('rightcategory_img_on_area').innerHTML = \"\";";
		}

//우측 메뉴 이미지 추가 끝 2013-11-26 이학봉

		if($db->dt[subimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg])){
			$mstring .="parent.document.getElementById('sub_img_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg]."' width=400 border=0>\";";
		}else if($db->dt[subimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category/".$db->dt[subimg])){
			$mstring .="parent.document.getElementById('sub_img_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/images/category/".$db->dt[subimg]."' width=400 border=0>\";";
		}else{
			$mstring .="parent.document.getElementById('sub_img_area').innerHTML = \"\";";
		}

		if($db->dt[subimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg_on])){
			$mstring .="parent.document.getElementById('sub_img_on_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg_on]."' width=400 border=0>\";";
		}else if($db->dt[subimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category/".$db->dt[subimg_on])){
			$mstring .="parent.document.getElementById('sub_img_on_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/images/category/".$db->dt[subimg_on]."' width=400 border=0>\";";
		}else{
			$mstring .="parent.document.getElementById('sub_img_on_area').innerHTML = \"\";";
		}

//아이콘 메뉴 이미지 추가 끝 2014-03-27 이학봉
		if($db->dt[subimg_icon] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg_icon])){
			$mstring .="parent.document.getElementById('sub_img_icon_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg_icon]."' border=0>\";";
		}else if($db->dt[subimg_icon] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/category/".$db->dt[subimg_icon])){
			$mstring .="parent.document.getElementById('sub_img_icon_area').innerHTML = \"<img src='".$admin_config[mall_data_root]."/images/category/".$db->dt[subimg_icon]."' border=0>\";";
		}else{
			$mstring .="parent.document.getElementById('sub_img_icon_area').innerHTML = \"\";";
		}

		//$mstring .="parent.document.getElementById('iView').contentWindow.document.body.innerHTML = document.getElementById('category_top_view_area').innerHTML; ";

		$mstring .="$('#cke_category_top_view iframe', parent.document).contents().find('body').html(document.getElementById('category_top_view_area').innerHTML);
		</script>";

		echo $mstring;
	}


}

if($mode == "category_access"){
	
	if($cid != "" && $depth != ""){

		$sql = "select * from shop_category_auth where cid = '".$cid."' and category_access='".$access_type."'";

		$db->query($sql);
		$auth_array = $db->fetchall();

		for($i=0;$i<count($auth_array);$i++){
			if($auth_array[$i][category_access] == 'G'){
				$sql = "select * from shop_groupinfo where gp_ix = '".$auth_array[$i][access_user]."'";
				$db->query($sql);
				$db->fetch();
				$gp_name = $db->dt[gp_name];
				$access_data[$auth_array[$i][access_user]] = $gp_name;
			}else{
				$sql = "select * from common_member_detail where code = '".$auth_array[$i][access_user]."'";
				$db->query($sql);
				$db->fetch();
				$name = $db->dt[name];
			}
		}

		if(count($access_data) > 0){

			$datas = $access_data;
			$datas = json_encode($datas);
			$datas = str_replace("\"true\"","true",$datas);
			$datas = str_replace("\"false\"","false",$datas);
			echo $datas;

		}else{

			for($i=$depth;$i>=0;$i--){

				$org_cid = substr($cid,0,3+(3*$i));
				$org_cid =$org_cid."000000000000";
				$for_cid = substr($org_cid,0,15);

				$sql = "select * from shop_category_auth where cid = '".$for_cid."'  and category_access='".$access_type."'";
				
				$db->query($sql);
				$auth_array = $db->fetchall();
				
				for($j=0;$j<count($auth_array);$j++){
					$sql = "select * from shop_groupinfo where gp_ix = '".$auth_array[$j][access_user]."'";
					$db->query($sql);
					$db->fetch();
					$gp_name = $db->dt[gp_name];
					$access_data[$auth_array[$j][access_user]] = $gp_name;
				}

				if(count($access_data) > 0){
					
					$datas = $access_data;
					$datas = json_encode($datas);
					$datas = str_replace("\"true\"","true",$datas);
					$datas = str_replace("\"false\"","false",$datas);
					echo $datas;
					exit;
				}

			}
		}
	}
}

if($mode == "member_search"){
	if($search_text){
		$sql = "select
					cu.code,
					AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
					ccd.com_name
				from
					common_user as cu 
					inner join common_member_detail as cmd on (cu.code = cmd.code)
					left join common_company_detail as ccd on (cu.company_id = ccd.company_id)
				where
					1
					and (cu.id = '".$search_text."' or AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') LIKE  '%$search_text%')";
				
		$db->query($sql);
		$member_list = $db->fetchall();
		
		for($i=0;$i<count($member_list);$i++){
			$data_array[$member_list[$i][code]]=$member_list[$i][name].($member_list[$i][com_name] ?" (".$member_list[$i][com_name].")":'');
		}

		$datas = json_encode($data_array);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;

	}else{
		echo "";
	}

}

if($mode == "product_cnt"){
	if($cid){
		$sql = "select
					count(pr.pid) as cnt
				from
				    shop_product as p 					
				left join 
				    shop_product_relation as pr on p.id = pr.pid
				where
					pr.cid = '".$cid."'
				and 
					p.is_delete = '0'
					";
		$db->query($sql);
		$db->fetch();
		
		if($db->dt[cnt]){
			$product_cnt = $db->dt[cnt];
		}else{
			$product_cnt = '0';
		}
	
		$sql = "select
					count(pr.pid) as cnt
				from
					shop_product as p 					
				left join 
				    shop_product_relation as pr on p.id = pr.pid
				where
					pr.cid like '".substr($cid,0,3+(3*$depth))."%' 
				and p.is_delete = '0'	
					";
		$db->query($sql);
		$db->fetch();
		
		if($db->dt[cnt]){
			$product_total_cnt = $db->dt[cnt];
		}else{
			$product_total_cnt = '0';
		}

		$data_array[product_cnt] = $product_cnt;
		$data_array[product_total_cnt] = $product_total_cnt;

		$datas = json_encode($data_array);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;

	}

}

if($mode == "select_department"){	//분류설정 부서불러오기 ajax
	if($group_ix){

		$sql = "select
					cd.dp_ix,
					cd.dp_name,
					cg.group_name
				from
					shop_company_group as cg 
					inner join shop_company_department as cd on (cg.group_ix = cd.group_ix)
				where
					cg.disp = '1'
					and cd.disp='1'
					and cd.group_ix = '".$group_ix."'
					order by cd.seq  ASC";

		$db->query($sql);
		$data_array = $db->fetchall();

		for($i=0;$i<count($data_array);$i++){
			$items[$data_array[$i][dp_ix]] = $data_array[$i][group_name]." > ".$data_array[$i][dp_name];
		}

		$datas = json_encode($items);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;

	}else{
		echo "";
	}

}

if($mode == "select_department_md"){	//분류설정 부서 ajax
	if($group_ix){

		$sql = "select
					cd.dp_ix,
					cd.dp_name,
					cg.group_name
				from
					shop_company_group as cg 
					inner join shop_company_department as cd on (cg.group_ix = cd.group_ix)
				where
					cg.disp = '1'
					and cd.disp='1'
					and cd.group_ix = '".$group_ix."'
					order by cd.seq  ASC";

		$db->query($sql);
		$data_array = $db->fetchall();

		for($i=0;$i<count($data_array);$i++){
			$items[$data_array[$i][dp_ix]] = $data_array[$i][dp_name];
		}

		$datas = json_encode($items);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;

	}else{
		echo "";
	}

}

if($mode == "select_position_md"){	//분류설정 부서불러오기 ajax
	if($dp_ix){

		$sql = "select
					cmd.code,
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
					cp.ps_name,
					cd.duty_name
				from
					common_member_detail as cmd 
					left join shop_company_position as cp on (cmd.position = cp.ps_ix)
					left join shop_company_duty as cd on (cmd.duty = cd.cu_ix)
				where
					cmd.department = '".$dp_ix."'";

		$db->query($sql);
		$data_array = $db->fetchall();

		for($i=0;$i<count($data_array);$i++){
			$items[$data_array[$i][code]] = $data_array[$i][name]." [".$data_array[$i][ps_name]."] "."[".$data_array[$i][duty_name]."]";
		}

		$datas = json_encode($items);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;

	}else{
		echo "";
	}

}

if($mode == 'category_design'){	//분류디자인 불러오기 ajax
	if($cid){
		$sql = "select * from shop_category_design where cid = '".$cid."'";
		$db->query($sql);
		$data_array = $db->fetchall();
		
		if(count($data_array) > 0){
			for($i=0;$i<count($data_array);$i++){

				$design_info[cname_style] = unserialize($data_array[$i][cname_style]);
				$design_info[cname_on_style] = unserialize($data_array[$i][cname_on_style]);

				$design_info[product_border] = unserialize($data_array[$i][product_border]);
				$design_info[pname_style] = unserialize($data_array[$i][pname_style]);
				$design_info[product_info] = unserialize($data_array[$i][product_info]);
				$design_info[product_listprice] = unserialize($data_array[$i][product_listprice]);
				$design_info[product_sellprice] = unserialize($data_array[$i][product_sellprice]);

				$design_info['use'][product_sellprice_use] = $data_array[$i][product_sellprice_use];
				$design_info['use'][product_border_use] = $data_array[$i][product_border_use];
				$design_info['use'][pname_style_use] = $data_array[$i][pname_style_use];
				$design_info['use'][product_info_use] = $data_array[$i][product_info_use];
				$design_info['use'][product_listprice_use] = $data_array[$i][product_listprice_use];

				$design_info['basic'][order_type] = $data_array[$i][order_type];
				$design_info['basic'][order_type_date] = $data_array[$i][order_type_date];
				$design_info['basic'][display_type] = $data_array[$i][display_type];
				$design_info['basic'][goods_max] = $data_array[$i][goods_max];

			}
			
			$datas = json_encode($design_info);
			$datas = str_replace("\"true\"","true",$datas);
			$datas = str_replace("\"false\"","false",$datas);
			echo $datas;

		}else{

			$design_info[cname_style] = array('this_category_color'=>'#000000','family_cname_style'=>'0','size_cname_style'=>'0','weight_cname_style'=>'0','decoration_cname_style'=>'0');
			$design_info[cname_on_style] = array('this_category_on_color'=>'#000000','family_cname_on_style'=>'0','size_cname_on_style'=>'0','weight_cname_on_style'=>'0','decoration_cname_on_style'=>'0');

			$design_info[product_border] = array('product_border_color'=>'#000000','product_style_line'=>'0');
			$design_info[pname_style] = array('pname_color'=>'#000000','family_pname_style'=>'0','size_pname_style'=>'0','weight_pname_style'=>'0','decoration_pname_style'=>'0');
			$design_info[product_info] = array('product_info_color'=>'#000000','family_product_info_style'=>'0','size_product_info_style'=>'0','weight_product_info_style'=>'0','decoration_product_info_style'=>'0');
			$design_info[product_listprice] = array('product_listprice_color'=>'#000000','family_product_listprice_style'=>'0','size_product_listprice_style'=>'0','weight_product_listprice_style'=>'0','decoration_product_listprice_style'=>'0');
			$design_info[product_sellprice] = array('product_sellprice_color'=>'#000000','family_product_sellprice_style'=>'0','size_product_sellprice_style'=>'0','weight_product_sellprice_style'=>'0','decoration_product_sellprice_style'=>'0');

			$design_info['use'][product_sellprice_use] = '0';
			$design_info['use'][product_border_use] = '0';
			$design_info['use'][pname_style_use] = '0';
			$design_info['use'][product_info_use] = '0';
			$design_info['use'][product_listprice_use] = '0';

			$design_info['basic'][order_type] = '0';
			$design_info['basic'][order_type_date] = '0';
			$design_info['basic'][display_type] = '0';
			$design_info['basic'][goods_max] = '0';

			$datas = json_encode($design_info);
			$datas = str_replace("\"true\"","true",$datas);
			$datas = str_replace("\"false\"","false",$datas);
			echo $datas;
		}
	}
}

if($mode == 'add_person'){	//분류설정 MD 관련 ajax
	$sql = "select * from shop_category_auth where category_access in ('MD') and cid = '".$cid."'";
	$db->query($sql);
	$md_array = $db->fetchall();
	
	if(count($md_array) > 0){
		for($i=0;$i<count($md_array);$i++){
	
			$sql = "select 
						AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
						cg.group_name,
						cd.dp_name,
						cdu.duty_name,
						cp.ps_name
					from
						common_member_detail as cmd 
						
						left join shop_company_department as cd on (cmd.department = cd.dp_ix)
						inner join shop_company_group as cg on (cd.group_ix = cg.group_ix)
						left join shop_company_duty as cdu on (cmd.duty = cdu.cu_ix)
						left join shop_company_position as cp on (cmd.position = cp.ps_ix)
					where
						cmd.code = '".$md_array[$i][access_user]."'";
			$db->query($sql);
			$db->fetch();
			
			$person_info[MD][$md_array[$i][access_user]] = $db->dt[group_name]." > ".$db->dt[dp_name]." > ".$db->dt[name]." [".$db->dt[ps_name]."]"." [".$db->dt[duty_name]."]";
	
		}

	}

	$sql = "select * from shop_category_auth where category_access in ('DE') and cid = '".$cid."'";
	$db->query($sql);
	$department_array = $db->fetchall();

	if(count($department_array) > 0){

		for($i=0;$i<count($department_array);$i++){

			$sql = "select 
						cg.group_name,
						cd.dp_name
					from
						shop_company_group as cg
						inner join shop_company_department as cd on (cg.group_ix = cd.group_ix)
					where
						cd.dp_ix = '".$department_array[$i][access_user]."'";
			$db->query($sql);
			$db->fetch();

			$person_info[DE][$department_array[$i][access_user]] = $db->dt[group_name]." > ".$db->dt[dp_name];
	
		}

	}
	
	$sql = "select * from shop_category_auth where category_access in ('MD','DE') and cid = '".$cid."' limit 0,1";
	$db->query($sql);
	$db->fetch();
	
	if($db->dt[auth_use] == '1'){
		$person_info[auth][] = '1';
	}else{
		$person_info[auth][] = '0';
	}

	$datas = json_encode($person_info);
	$datas = str_replace("\"true\"","true",$datas);
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;

}


if($mode == 'get_add_fields'){


	$db->query("select * from shop_category_addfield where cid = '$cid' ");
	if($db->total){
		$add_fields = $db->fetchall();
	}

	$datas = json_encode($add_fields);
	$datas = str_replace("\"true\"","true",$datas);
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
}


if($mode == "select_category_info"){	//브랜드리스트 분류검색 ajax

	if($cid){
		$sql = "select depth from shop_category_info where cid = '".$cid."'";
		$db->query($sql);
		$db->fetch();
		$depth = $db->dt[depth];

		$like_cid = substr($cid, 0,(3+$depth*3));
		$for_depth = $depth + 1;
		$sql = "select 
				*
				from
					shop_category_info
				where
					cid like '".$like_cid."%'
					and cid != '".$like_cid."'
					and depth = '".$for_depth."'
					order by cid ASC";
		$db->query($sql);
		$category_array = $db->fetchall();

		for($i=0;$i<count($category_array);$i++){
			$category_info[$category_array[$i][cid]] = $category_array[$i][cname];
		}

		$datas = json_encode($category_info);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
	
	}
}

if($mode == "get_category_name"){
	if($cid){
		$sql = "select * from shop_category_info where cid = '".$cid."'";
		$db->query($sql);
		$db->fetch();
		$depth = $db->dt[depth];
	
		for($i=0;$i<=$depth;$i++){
			$this_cid = substr(substr($cid, 0,($i*3+3)).'000000000000',0,15);
			//echo "$i"."<br>";
			$sql = "select * from shop_category_info where cid = '".$this_cid."'";
			//echo nl2br($sql)."<br>";
			$db2->query($sql);
			$db2->fetch();
			$cname = $db2->dt[cname];
			
			if($i == $depth){
				$relation_cname .= $cname;
			}else{
				$relation_cname .= $cname." > ";
			}
			
		}
		$category_info[$cid] = $relation_cname;
	
		$datas = json_encode($category_info);
		$datas = str_replace("\"true\"","true",$datas);
		$datas = str_replace("\"false\"","false",$datas);
		echo $datas;
	}

}

if ($mode == "modify"){

	if($sub_mode == "design_subcategory"){	// 분류별 디자인설정 

		$sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where cid = '$cid'";
		$db->query($sql);
		$db->fetch();

		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/";
		if(!is_dir($path)){
			mkdir($path, 0777);
		}
		if ($category_img_size > 0){
			if($db->dt[catimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg]);
			}

			copy($category_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_img_name);
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_img_name,0777);
			$setString .= ", catimg = '$category_img_name'";
		}

		if ($category_img_on_size > 0){
			if($db->dt[catimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg_on])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg_on]);
			}

			copy($category_img_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_img_on_name);
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_img_on_name,0777);
			$setString .= ", catimg_on = '$category_img_on_name'";
		}

		if ($leftcategory_img_size > 0){
			if($db->dt[leftcatimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg]);
			}
			copy($leftcategory_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$leftcategory_img_name);
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$leftcategory_img_name,0777);
			$setString .= ", leftcatimg = '$leftcategory_img_name'";
		}

		if ($leftcategory_img_on_size > 0){
			if($db->dt[leftcatimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg_on])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg_on]);
			}
			copy($leftcategory_img_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$leftcategory_img_on_name);
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$leftcategory_img_on_name,0777);
			$setString .= ", leftcatimg_on = '$leftcategory_img_on_name'";
		}

		//우측 메뉴 이미지 추가 시작 2013-11-26 이학봉
		if ($rightcategory_img_size > 0){
			if($db->dt[rightcatimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg]);
			}
			copy($rightcategory_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$rightcategory_img_name);
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$rightcategory_img_name,0777);
			$setString .= ", rightcatimg = '$rightcategory_img_name'";
		}

		if ($rightcategory_img_on_size > 0){
			if($db->dt[rightcatimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg_on])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg_on]);
			}
			copy($rightcategory_img_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$rightcategory_img_on_name);
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$rightcategory_img_on_name,0777);
			$setString .= ", rightcatimg_on = '$rightcategory_img_on_name'";
		}
		//우측 메뉴 이미지 추가 끝 2013-11-26 이학봉

		if ($sub_img_size > 0){
			if($db->dt[subimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg]);
			}
			copy($sub_img, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$sub_img_name);
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$sub_img_name,0777);
			$setString .= ", subimg = '$sub_img_name'";
		}

		if ($sub_img_on_size > 0){
			if($db->dt[subimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg_on])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg_on]);
			}
			copy($sub_img_on, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$sub_img_on_name);
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$sub_img_on_name,0777);
			$setString .= ", subimg_on = '$sub_img_on_name'";
		}

		//아이콘 메뉴 이미지 추가 끝 2014-03-27 이학봉
		if ($sub_img_icon_size > 0){
			if($db->dt[subimg_icon] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg_icon])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg_icon]);
			}
			copy($sub_img_icon, $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$sub_img_icon_name);
			chmod($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$sub_img_icon_name,0777);
			$setString .= ", subimg_icon = '$sub_img_icon_name'";
		}

		if(count($global_this_category) > 0){
			foreach($global_this_category as $key => $gp){
				$global_this_category[$key] = urlencode($gp);
			}
		}
		/*
		if(count($global_this_category_on) > 0){
			foreach($global_this_category_on as $key => $gp){
				$global_this_category_on[$key] = urlencode($gp);
			}
		}
		*/

		$global_cinfo['cname'] = $global_this_category;
		//$global_cinfo['cname_on'] = $global_this_category_on;
		$global_cinfo = json_encode($global_cinfo);

		$sql = "update ".TBL_SHOP_CATEGORY_INFO." set
					global_cinfo = '$global_cinfo',
					cname = '$this_category',
					cname_color = '$this_category_color', 
					cname_on = '$this_category_on',
					cname_on_color = '$this_category_on_color',
					category_display_type ='$category_display_type'
					$setString
				where 
					cid = '$cid'";
		//echo $sql;
		//exit;
		$db->query($sql);
		$del_num=0;
		$del_query="";


		if($ch_category_img=="Y") {
			if($db->dt[catimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg]);
			}

			if($del_num==0) $del_query.=" catimg='' ";
			else $del_query.=" ,catimg='' ";
			$del_num++;
		}

		if($ch_category_img_on=="Y") {
			if($db->dt[catimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg_on])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[catimg_on]);
			}
			if($del_num==0) $del_query.=" catimg_on='' ";
			else $del_query.=" ,catimg_on='' ";
			$del_num++;
		}

		if($ch_leftcategory_img=="Y") {
			if($db->dt[leftcatimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg]);
			}
			if($del_num==0) $del_query.=" leftcatimg='' ";
			else $del_query.=" ,leftcatimg='' ";
			$del_num++;
		}

		if($ch_leftcategory_img_on=="Y") {
			if($db->dt[leftcatimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg_on])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[leftcatimg_on]);
			}
			if($del_num==0) $del_query.=" leftcatimg_on='' ";
			else $del_query.=" ,leftcatimg_on='' ";
			$del_num++;
		}

		//우측 메뉴 이미지추가 시작 2013-11-26 이학봉
		if($ch_rightcategory_img=="Y") {
			if($db->dt[rightcatimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg]);
			}
			if($del_num==0) $del_query.=" rightcatimg='' ";
			else $del_query.=" ,rightcatimg='' ";
			$del_num++;
		}

		if($ch_rightcategory_img_on=="Y") {
			if($db->dt[rightcatimg_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg_on])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[rightcatimg_on]);
			}
			if($del_num==0) $del_query.=" rightcatimg_on='' ";
			else $del_query.=" ,rightcatimg_on='' ";
			$del_num++;
		}
		//우측 메뉴 이미지추가 끝 2013-11-26 이학봉


		if($ch_sub_img=="Y") {
			if($db->dt[subimg] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg]);
			}
			if($del_num==0) $del_query.=" subimg='' ";
			else $del_query.=" ,subimg='' ";
			$del_num++;
		}

		if($ch_sub_img_on=="Y") {
			if($db->dt[sub_img_on] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[sub_img_on])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[sub_img_on]);
			}
			if($del_num==0) $del_query.=" subimg_on='' ";
			else $del_query.=" ,subimg_on='' ";
			$del_num++;
		}

		//아이콘 이미지 삭제 2014-03-27 이학봉
		if($ch_sub_img_icon=="Y") {
			if($db->dt[subimg_icon] != "" && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg_icon])){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$db->dt[subimg_icon]);
			}
			if($del_num==0) $del_query.=" subimg_icon='' ";
			else $del_query.=" ,subimg_icon='' ";
			$del_num++;
		}

		if($del_num>0) {
			$sql="UPDATE ".TBL_SHOP_CATEGORY_INFO." SET ".$del_query." WHERE cid = '$cid'";
			$db->query($sql);
		}

		//각 색상 사이즈 폰트 설정값 추가 시작 2013-12-09 이학봉
		$cname_style = serialize(array('this_category_color'=>$this_category_color,'family_cname_style'=>$family_cname_style,'size_cname_style'=>$size_cname_style,'weight_cname_style'=>$weight_cname_style,'decoration_cname_style'=>$decoration_cname_style));

		$cname_on_style = serialize(array('this_category_on_color'=>$this_category_on_color,'family_cname_on_style'=>$family_cname_on_style,'size_cname_on_style'=>$size_cname_on_style,'weight_cname_on_style'=>$weight_cname_on_style,'decoration_cname_on_style'=>$decoration_cname_on_style));

		if($product_border_use == '1'){
			$product_border = serialize(array('product_border_color'=>$product_border_color,'product_style_line'=>$product_style_line));
			$update_set = "product_border = '".$product_border."',";
		}
		if($pname_style_use == '1'){
			$pname_style = serialize(array('pname_color'=>$pname_color,'family_pname_style'=>$family_pname_style,'size_pname_style'=>$size_pname_style,'weight_pname_style'=>$weight_pname_style,'decoration_pname_style'=>$decoration_pname_style));
			$update_set .= "pname_style = '".$pname_style."',";
		}
		
		if($product_info_use == '1'){
			$product_info = serialize(array('product_info_color'=>$product_info_color,'family_product_info_style'=>$family_product_info_style,'size_product_info_style'=>$size_product_info_style,'weight_product_info_style'=>$weight_product_info_style,'decoration_product_info_style'=>$decoration_product_info_style));
			$update_set .= "product_info = '".$product_info."',";
		}
		
		if($product_listprice_use == '1'){
			$product_listprice = serialize(array('product_listprice_color'=>$product_listprice_color,'family_product_listprice_style'=>$family_product_listprice_style,'size_product_listprice_style'=>$size_product_listprice_style,'weight_product_listprice_style'=>$weight_product_listprice_style,'decoration_product_listprice_style'=>$decoration_product_listprice_style));
			$update_set .= "product_listprice = '".$product_listprice."',";
		}

		if($product_sellprice_use == '1'){
			$product_sellprice = serialize(array('product_sellprice_color'=>$product_sellprice_color,'family_product_sellprice_style'=>$family_product_sellprice_style,'size_product_sellprice_style'=>$size_product_sellprice_style,'weight_product_sellprice_style'=>$weight_product_sellprice_style,'decoration_product_sellprice_style'=>$decoration_product_sellprice_style));
			$update_set .= "product_sellprice = '".$product_sellprice."',";
		}

		$sql = "select
				count(cd_ix) as total
				from
					shop_category_design
				where
					cid = '".$cid."'";
		$db->query($sql);
		$db->fetch();

		if($db->dt[total] > 0){		//분류별 디자인설정 테이블 추가 2013-12-09 이학봉
			// 분류디자인설정 테이블에 값이 있을 경우 update
			$sql = "update shop_category_design set
						cname_style = '".$cname_style."',
						cname_on_style = '".$cname_on_style."',
						order_type = '".$order_type."',
						order_type_date = '".$order_type_date."',
						display_type = '".$display_type."',
						product_border_use = '".$product_border_use."',
						pname_style_use = '".$pname_style_use."',
						product_info_use = '".$product_info_use."',
						product_listprice_use = '".$product_listprice_use."',
						product_sellprice_use = '".$product_sellprice_use."',
						goods_max = '$goods_max',
						$update_set
						regdate = NOW()
					where
						cid = '".$cid."'";
		}else{
			//없을경우 insert
			$sql = "insert into shop_category_design set
						cid = '".$cid."',
						cname_style = '".$cname_style."',
						cname_on_style = '".$cname_on_style."',
						order_type = '".$order_type."',
						order_type_date = '".$order_type_date."',
						display_type = '".$display_type."',
						product_border = '".$product_border."',
						product_border_use = '".$product_border_use."',
						pname_style_use = '".$pname_style_use."',
						product_info = '".$product_info."',
						product_info_use = '".$product_info_use."',
						product_listprice = '".$product_listprice."',
						product_listprice_use = '".$product_listprice_use."',
						product_sellprice = '".$product_sellprice."',
						product_sellprice_use = '".$product_sellprice_use."',
						goods_max = '$goods_max',
						regdate = NOW()";
		}

		$db->query($sql);
		//각 색상 사이즈 폰트 설정값 추가 끝 2013-12-09 이학봉


		//edit내용저장 2014-06-17 수정
		$sql = "update ".TBL_SHOP_CATEGORY_INFO." set
			category_top_view = '$category_top_view'
		where 
			cid = '$cid'";
		$db->query($sql);

		//category_top_view
	}else if ($sub_mode == "edit_category"){	//분류수정
		
		//카테고리 회원접근권한 시작 2013-11-27 이학봉
		$sql = "select count(ca_ix) as total from shop_category_auth where cid = '".$cid."' and category_access not in ('MD','DE')";
		$db->query($sql);
		$db->fetch();
		if($db->dt[total] > 0){
			$db->query("delete from shop_category_auth where cid ='".$cid."' and category_access not in ('MD','DE')");
		}
		if($category_access == "G"){
			if(is_array($group_list)){
				foreach($group_list as $key => $value){
					$sql = "insert into shop_category_auth (cid,category_access,access_user,regdate)
							values('".$cid."','".$category_access."','".$value."',NOW())";
					$db->query($sql);
				}
			}
		}else if ($category_access == "U"){

			if(is_array($md_list)){
				foreach($md_list as $key => $value){
					$sql = "insert into shop_category_auth (cid,category_access,access_user,regdate)
							values('".$cid."','".$category_access."','".$value."',NOW())";
					$db->query($sql);
				}
			}
		}else{
			$sql = "insert into shop_category_auth (cid,category_access,access_user,regdate)
							values('".$cid."','".$category_access."','',NOW())";
					$db->query($sql);
		}
		//카테고리 회원접근권한 끝 2013-11-27 이학봉
		
		if(count($global_this_category) > 0){
			foreach($global_this_category as $key => $gp){
				$global_this_category[$key] = urlencode($gp);
			}
		}
		/*
		if(count($global_this_category_on) > 0){
			foreach($global_this_category_on as $key => $gp){
				$global_this_category_on[$key] = urlencode($gp);
			}
		}
		*/

		$global_cinfo['cname'] = $global_this_category;
		//$global_cinfo['cname_on'] = $global_this_category_on;
		$global_cinfo = json_encode($global_cinfo);

		$sql = "update ".TBL_SHOP_CATEGORY_INFO." set
					global_cinfo = '$global_cinfo',
					cname = '$this_category',
					hscode = '$hscode',
					category_top_view = '$category_top_view',
					category_type ='$category_type',
					category_link ='$category_link',
					category_use = '$category_use',
					category_code = '$category_code',
					category_sort = '$category_sort',
					is_adult = '$is_adult',
					is_layout_apply = '$is_layout_apply',
					is_layout_emphasis = '$is_layout_emphasis'
				where 
					cid = '$cid'";

		$db->query($sql);

		if($category_use != "1" && $cid != "000000000000000"){
			$sql = "update ".TBL_SHOP_CATEGORY_INFO." set category_use ='$category_use' where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ";
			$db->query($sql);

		}else{

			ParentCategoryUseUpdate($db, $cid, $this_depth);


			if($this_depth+1 > 0){
				$sql = "update ".TBL_SHOP_CATEGORY_INFO." set category_use ='$category_use' where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ";
				$db->query($sql);
			}

		}

	}else if ($sub_mode == "add_person"){	//분류MD설정
		//카테고리 MD설정 시작 2013-12-09 이학봉
		if($md_use == '1'){	//MD 사용시 해당값에 대해 처리
			$sql = "select count(ca_ix) as total from shop_category_auth where cid = '".$cid."' and category_access in ('MD','DE')";
			$db->query($sql);
			$db->fetch();
			if($db->dt[total] > 0){
				$db->query("delete from shop_category_auth where cid ='".$cid."' and category_access in ('MD','DE')");
			}

			if(count($department_list) > '0'){
				for($i=0;$i<count($department_list);$i++){
					$sql = "insert into shop_category_auth (cid,auth_use,category_access,access_user,regdate)
					values('".$cid."','".$md_use."','DE','".$department_list[$i]."',NOW())";

					$db->query($sql);
				}
			}
			if(count($md_code) > '0'){
				for($i=0;$i<count($md_code);$i++){
					$sql = "insert into shop_category_auth (cid,auth_use,category_access,access_user,regdate)
					values('".$cid."','".$md_use."','MD','".$md_code[$i]."',NOW())";

					$db->query($sql);
				}
			
			}
			
			
		}
		//카테고리 MD설정 끝 2013-12-09 이학봉
	}

	//exit;
	updateCategoryXML();
	echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert('카테고리 정보가 정상적으로 수정되었습니다.');parent.document.location.href='category.php?cid=".$cid."&depth=".$this_depth."';</Script>";
	//Header("Location: category.php");
	//'카테고리 정보가 정상적으로 수정되었습니다.'
}

function ParentCategoryUseUpdate($mdb, $cid, $this_depth){
	$where = "";
	for($i=0;$i <= $this_depth;$i++){
		if(!$where){
			$where .= " where (cid LIKE '".substr($cid,0,($i+1)*3)."%' and depth = '".$i."' ) ";
		}else{
			$where .= " or (cid LIKE '".substr($cid,0,($i+1)*3)."%' and depth = '".$i."' ) ";
		}
	}
	//$mdb->debug = true;
	$sql = "select cid, cname,  depth, category_use from ".TBL_SHOP_CATEGORY_INFO."   $where ";

	$mdb->query($sql);

	$parent_category = $mdb->fetchall();
	for($i=0;$i < count($parent_category);$i++){
		$sql = "update ".TBL_SHOP_CATEGORY_INFO." set category_use ='1' where cid = '".$parent_category[$i][cid]."' ";
		//echo $sql;
		$mdb->query($sql);
	}


}


if ($mode == "del"){
	$udb = new Database;

	if (CheckSubCategory($cid,$this_depth)){
		if($sub_cartegory_delete == "1"){
			$sql = "select pid, cid, basic from ".TBL_SHOP_PRODUCT_RELATION." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'  ";
			$db->query($sql);


			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);

				$sql = "select pid from shop_product_relation where pid  = '".$db->dt[pid]."'  ";
				$udb->query($sql);
				if($udb->total <= 1){
					$sql = "update shop_product set reg_category ='N' where id = '".$db->dt[pid]."'  ";
					$udb->query($sql);
				}else{
					if($db->dt[basic] == '1'){
						$sql = "update ".TBL_SHOP_PRODUCT_RELATION." set basic = '1' where cid != '".$db->dt[cid]."' and pid = '".$db->dt[pid]."' limit 1 ";
						$udb->query($sql);
					}
				}
			}


			$sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);
			$db->fetch();
			$category_info = $db->dt;

			$sql = "delete from ".TBL_SHOP_CATEGORY_INFO." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);

			$db->query("delete from ".TBL_SHOP_PRODUCT_RELATION." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ");
			$db->query("delete from shop_buyingservice_url_info where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%' ");

			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[catimg]);
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[catimg_on]);
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[leftcatimg]);
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[leftcatimg_on]);
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[rightcatimg]);
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[rightcatimg_on]);
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[subimg]);
			unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[sub_img_on]);



			echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert(\"삭제 되었습니다.\");</Script>";
			echo "<Script Language='JavaScript'>parent.document.location.href='category.php';</Script>";
		}else{
			echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert('하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요');</Script>";
			//'하부 카테고리가 존제 합니다.하부 카테고리를 먼저 삭제하신후 다시 시도해 주세요'
		}
	}else{


			//$db->debug = true;
			//$udb->debug = true;
			$sql = "select pid, cid, basic from ".TBL_SHOP_PRODUCT_RELATION." where cid = '$cid'  ";
			$db->query($sql);


			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);

				$sql = "select pid from shop_product_relation where pid  = '".$db->dt[pid]."'  ";
				$udb->query($sql);
				if($udb->total <= 1){
					$sql = "update shop_product set reg_category ='N' where id = '".$db->dt[pid]."'  ";
					$udb->query($sql);
				}else{
					if($db->dt[basic] == '1'){
						$sql = "update  ".TBL_SHOP_PRODUCT_RELATION." set basic = '1'  where cid != '".$cid."' and pid = '".$db->dt[pid]."' limit 1 ";
						$udb->query($sql);
					}
				}
			}
			$sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where cid LIKE '".substr($cid,0,($this_depth+1)*3)."%'";
			$db->query($sql);
			$db->fetch();
			$category_info = $db->dt;

			$sql = "delete from ".TBL_SHOP_PRODUCT_RELATION." where cid = '$cid'  ";
			$db->query($sql);

			$sql = "delete from ".TBL_SHOP_CATEGORY_INFO." where cid = '$cid'";
			$db->query($sql);

			$db->query("delete from shop_buyingservice_url_info where cid ='$cid' ");

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[catimg]) && $category_info[catimg]){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[catimg]);
			}
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[catimg_on]) && $category_info[catimg_on]){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[catimg_on]);
			}
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[leftcatimg]) && $category_info[leftcatimg]){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[leftcatimg]);
			}
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[leftcatimg_on]) && $category_info[leftcatimg_on]){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[leftcatimg_on]);
			}
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[rightcatimg]) && $category_info[rightcatimg]){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[rightcatimg]);
			}
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[rightcatimg_on]) && $category_info[rightcatimg_on]){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[rightcatimg_on]);
			}
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[subimg]) && $category_info[subimg]){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[subimg]);
			}
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[sub_img_on]) && $category_info[sub_img_on]){
				unlink($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete]."/images/category/".$category_info[sub_img_on]);
			}

			echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert('삭제되었습니다.');</Script>";
			//'삭제되었습니다.'
			echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>parent.document.location.href='category.php?cid=$cid';</Script>";
	}
	updateCategoryXML();
//	Header("Location: category.php");
}

if ($mode == "insert"){

	if(trim($sub_cid) != "" && trim($cid) != "") {// 카테고리 정보가 제대로 안넘어 올 경우를 검사 kbk 12/03/22
		$sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where cid = '$cid'";
		$db->query($sql);
		$db->fetch(0);

		$level1 = $db->dt["vlevel1"];
		$level2 = $db->dt["vlevel2"];
		$level3 = $db->dt["vlevel3"];
		$level4 = $db->dt["vlevel4"];
		$level5 = $db->dt["vlevel5"];

		if ($sub_depth+1 == 1){
			$level1 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==2){
			$level2 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==3){
			$level3 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==4){
			$level4 = getMaxlevel($cid,$sub_depth);
		}else if($sub_depth+1 ==5){
			$level5 = getMaxlevel($cid,$sub_depth);
		}

		$sql = "insert into ".TBL_SHOP_CATEGORY_INFO."
					(cid, depth,vlevel1, vlevel2, vlevel3,vlevel4,vlevel5,cname,cname_color,cname_on,cname_on_color,catimg,catimg_on,leftcatimg,leftcatimg_on, subimg,subimg_on, category_use,category_display_type,category_type, category_link,regdate,category_code,is_adult,is_layout_apply,is_layout_emphasis,category_sort)
					values
					('$sub_cid', '$sub_depth','$level1','$level2','$level3','$level4','$level5', '$sub_category','$sub_category_color','$sub_category_on','$sub_category_on_color','$category_img_name','$category_img_on_name','$leftcategory_img_name','$leftcategory_img_on_name','$sub_img_name','$sub_img_on_name','$category_use','$category_display_type','$category_type','$category_link',NOW(),'$category_code','$is_adult','$is_layout_apply','$is_layout_emphasis','$category_sort')";

		$db->query($sql);
	//	echo $sql ;

		echo "<Script Language='JavaScript'>parent.document.location.href='category.php?cid=$cid';</Script>";
	//	Header("Location: category.php");

		updateCategoryXML();
	} else {
		echo "<script language='JavaScript' src='/admin/_language/language.php'></Script><Script Language='JavaScript'>alert(language_data['category.save.php']['D'][language]);</Script>";
		//카테고리 정보가 정확하지 않습니다. 상위 카테고리를 선택해 주세요.
	}

}

function getMaxlevel($cid,$depth)
{
	global $db;

	$strdepth = $depth + 1;

	$sPos = $depth*3;
	$sql = "select max(vlevel$strdepth)+1 as maxlevel from ".TBL_SHOP_CATEGORY_INFO." where cid LIKE '".substr($cid,0,$sPos)."%'";

	$db->query($sql);
	$db->fetch(0);

//	echo $sql."<br>";
//	echo $db->dt["maxlevel"]."<br>";

	return $db->dt["maxlevel"];

}

function CheckSubCategory($cid,$depth){
	global $db;

	$endpos = $depth*3+3;
	$this_depth = $depth;
	$sql = "select * from ".TBL_SHOP_CATEGORY_INFO." where depth > $this_depth and cid LIKE '".substr($cid,0,$endpos)."%'";
	$db->query($sql);
	//echo "$sql<br>";

	if ($db->total > 0){
		return true;
	}else{
		return false;
	}

}

function updateCategoryXML(){

	global $DOCUMENT_ROOT, $admin_config;

	$xml = new XmlWriter_();
	$mdb = new Database;
	$mdb->query("select * from ".TBL_SHOP_CATEGORY_INFO." where category_use = 1 order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5");
	$categorys = $mdb->fetchall();

	$xml->push('categorys');

	if(count($categorys) > 0){
		foreach ($categorys as $category) {
			//$xml->push('shop', array('species' => $animal[0]));
			$xml->push('category', array('cid' => $category[cid], 'depth' => $category[depth], 'top_cid' => substr($category[cid],0,3)));
			$xml->element('cid', $category[cid]);
			$xml->element('cname', $category[cname]);
			$xml->element('cname_on', $category[cname_on]);
			$xml->element('depth', $category[depth]);
			$xml->pop();
		}

		$xml->pop();
		//print $xml->getXml();

		$dirname = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/templet/".$admin_config[mall_use_templete];
		/*
		if(!is_dir($dirname)){
			if(is_writable($path)){
				mkdir($dirname, 0777, true);
				chmod($dirname, 0777);
			}
		}
		*/
		//$fileName = "main_flash.xml";
		$fp = fopen($dirname."/categorys.xml","w");
		fputs($fp, $xml->getXml());
		fclose($fp);
	}
}
//echo "<br>maxlevel:".getMaxlevel($cid,$sub_depth);


/*
1. 카테고리 할인율 정보를 위한 추가 2014-04-19 이학봉
*/



?>