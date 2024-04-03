<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
 /*
$vdate = date("Ymd", time());
$today = date("Y/m/d", time());
$vyesterday = date("Y/m/d", time()-84600);
$voneweekago = date("Y/m/d", time()-84600*7);
$vtwoweekago = date("Y/m/d", time()-84600*14);
$vfourweekago = date("Y/m/d", time()-84600*28);
$vyesterday = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Y/m/d",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));
*/
if($mgp_ix){
	$db = new Database();

	$sql = "select mgi.* from shop_mro_groupinfo mgi
					where  mgp_ix ='$mgp_ix' ";
	//echo $sql;
	$db->query($sql);
	$db->fetch();
//print_r($db->dt);

	$mgp_ix = $db->dt[mgp_ix];
	$gp_name = $db->dt[gp_name]."  "; 


}

/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>사용자별 쿠폰 발행</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='pragma' content='no-cache'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
*/
$Script = "
<script language='javascript' src='../include/DateSelect.js'></script>
<script language='javascript'>
function allCheck(frm){
	if(frm.all.checked == true){
		for(var i=0;i<frm.code.length;i++){
			if(!frm.code[i].disabled){
				frm.code[i].checked = true;
			}
		}
	}else{
		for(var i=0;i<frm.code.length;i++){
			frm.code[i].checked = false;
		}
	}
}

function registedAllCheck(frm){
	if(frm.all.checked == true){
		for(var i=0;i<frm.regist_ix.length;i++){
			if(!frm.regist_ix[i].disabled){
				frm.regist_ix[i].checked = true;
			}
		}
	}else{
		for(var i=0;i<frm.regist_ix.length;i++){
			frm.regist_ix[i].checked = false;
		}
	}
}
 
function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		$('#startDate').attr('disabled',false);
		$('#endDate').attr('disabled',false);
	}else{
		$('#startDate').attr('disabled','disabled');
		$('#endDate').attr('disabled','disabled');
	}
}
 
function selectedCouponReg(form_id){
	//alert($('#'+form_id).html());
	var total_count = parseInt($('#'+form_id).find('input#code[type=checkbox]:checked').length);
	if(total_count == 0){
		alert('발급 대상을 선택해주세요.');
		return ;
	}
	var first_person = '';
	$('#'+form_id).find('input#code[type=checkbox]:checked').each(function(index){
		if(index == 0){
			first_person = $(this).attr('user_name');
			//alert($(this).val());
		}
	});
	if(total_count-1 > 0){
		var confirm_text = first_person+'님 외 '+(total_count-1)+' 명에게 '+$('#coupon_kind').html()+' 쿠폰을 발급 하시겠습니까?';
	}else{
		var confirm_text = first_person+'님 에게 '+$('#coupon_kind').html()+' 쿠폰을 발급 하시겠습니까?';
	}
	if(confirm(confirm_text)){
		$('#'+form_id).submit();
	}
}

function searchCouponReg(form_id){
	//member_search
	var search_issue_total = $('#search_issue_total').val();
	if(search_issue_total > 0){
		if(confirm('  '+$('#coupon_kind').html()+' 이 발급된 회원이 '+search_issue_total+' 명 있습니다. 기 발급된 회원에게도 발급하시겠습니까? ')){
			$('#dupe_check').attr('checked',true);
			$('#member_search').attr('action','mrogroup.act.php');
			$('#member_search').attr('target','act')
			$('#member_search').submit();
		}
	}else{
		var total_count = $('#search_total').val();
		var first_person = '';
		//alert(form_id);
		$('#cupon_pop_frm').find('input#code[name^=code]').each(function(index){
			//alert($(this).attr('user_name'));
			if(index == 0){
				//alert($(this).attr('user_name'));
				first_person = $(this).attr('user_name');
				//alert($(this).val());
			}
		});

		if(total_count-1 > 0){
			var confirm_text = first_person+'님 외 '+(total_count-1)+' 명에게 '+$('#coupon_kind').html()+' 쿠폰을 발급 하시겠습니까?';
		}else{
			var confirm_text = first_person+'님 에게 '+$('#coupon_kind').html()+' 쿠폰을 발급 하시겠습니까?';
		}
		if(confirm(confirm_text)){
			$('#member_search').attr('action','mrogroup.act.php');
			$('#member_search').attr('target','act')
			$('#member_search').submit();
		}
	}
}

function canceledCoupon(form_id){
	//alert(form_id);
	//alert($('#'+form_id).html());
	var total_count = parseInt($('#'+form_id).find('input#regist_ix[type=checkbox]:checked').length);
	if(total_count == 0){
		alert('발급취소할 대상을 선택해주세요.');
		return ;
	}
	var first_person = '';
	$('#'+form_id).find('input#regist_ix[type=checkbox]:checked').each(function(index){
		if(index == 0){
			first_person = $(this).attr('user_name');
			//alert($(this).val());
		}
	});
	if(total_count-1 > 0){
		var confirm_text = first_person+'외 '+(total_count-1)+' 명에게 발급된 \''+$('#coupon_kind').html()+'\' 쿠폰을 발급 취소 하시겠습니까?';
	}else{
		var confirm_text = first_person+'님 에게 발급된 \''+$('#coupon_kind').html()+'\' 쿠폰을 발급 취소 하시겠습니까?';
	}

	if(confirm(confirm_text)){
		$('#'+form_id).submit();
	}
}


";

if($mode != 'result'){//kbk
$Script .= "
	$(document).ready(function() {
		//onLoad('".$sDate."','".$eDate."', document.member_search);
	});";
}
$Script .= "
</script>
";

//$db = new Database;
 
$max = 10;

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if(!$_GET[mode]){
	$mode = "search";
}


		if(!$orderby){
			if($db->dbms_type == "oracle"){
				$orderby = "cmd.name"; //date_
			}else{
				$orderby = "cmd.name";//cu.date
			}
			$ordertype = "asc";
		}
		if ($admininfo[mall_type] == "O"){
			if($db->dbms_type == "oracle"){
				$where = " where cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0  ";// and cu.mem_type in ('M','C','F','S')
			}else{
				$where = " where cu.code != '' and cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0   ";//and cu.mem_type in ('M','C','F','S')
			}
		}else{
			if($db->dbms_type == "oracle"){
				$where = " where cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0  "; //and cu.mem_type in ('M','C','F') 
			}else{
				$where = " where cu.code != '' and cu.code = cmd.code and cmd.gp_ix = mg.gp_ix and gp_level != 0  ";//and cu.mem_type in ('M','C','F') 
			}
		}

		if($db->dbms_type == "oracle"){
			if($search_type != "" && $search_text != ""){
				if($search_type == "jumin"){
					$search_text = substr($search_text,0,6)."-".md5(substr($search_text,6,7));
					$where .= " and jumin = '$search_text' ";
				}else if($search_type=="name" || $search_type=="mail"  || $search_type == "pcs" || $search_type == "tel" ) {
					$where .= " and AES_DECRYPT(".$search_type.") LIKE '%".$search_text."%' ";
				} else {
					$where .= " and ".$search_type." LIKE '%$search_text%' ";
				}
			}
		}else{
			if($multi_search == 1){				
					$search_array = explode("\r\n",$search_texts);
					if($search_type == "jumin"){
						$search_text = substr($search_text,0,6)."-".md5(substr($search_text,6,7));
						$where .= " and jumin = '$search_text' ";
					}else if($search_type=="name" || $search_type=="mail"  || $search_type == "pcs" || $search_type == "tel" ) {					 
						//$where .= " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') LIKE '%".$search_text."%' ";
						$where .= " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') in ('".implode("','",$search_array)."') ";
					} else {
						$where .= " and ".$search_type." in ('".implode("','",$search_array)."') ";
					}

				
			}else{
				if($search_type != "" && $search_text != ""){
					if($search_type == "jumin"){
						$search_text = substr($search_text,0,6)."-".md5(substr($search_text,6,7));
						$where .= " and jumin = '$search_text' ";
					}else if($search_type=="name" || $search_type=="mail"  || $search_type == "pcs" || $search_type == "tel" ) {
						$where .= " and AES_DECRYPT(UNHEX(".$search_type."),'".$db->ase_encrypt_key."') LIKE '%".$search_text."%' ";
					}else if($search_type=="id") {
						$where .= " and ".$search_type." = '$search_text' ";
					} else {
						$where .= " and ".$search_type." LIKE '%$search_text%' ";
					}
				}
			}		 
		}

		if($gp_ix != ""){
			$where .= " and cmd.gp_ix = '".$gp_ix."' ";
		}

		if($mmode == "personalization"){
			$where .= " and cmd.code = '".$mem_ix."' ";
		}

		//$startDate = $FromYY.$FromMM.$FromDD;
		//$endDate = $ToYY.$ToMM.$ToDD;

		if($startDate != "" && $endDate != ""){
			if($publish_div == "2"){
				$where .= " and  cu.date between  '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
			}else if($publish_div == "3"){
				$where .= " and  cmd.recent_order_date between  '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
			}else if($publish_div == "4"){
				$where .= " and  cu.last between  '".$startDate." 00:00:00' and '".$endDate." 23:59:59' ";
			}
		}

/* date_format 변환
		if($startDate != "" && $endDate != ""){
			if($publish_div == "2"){
				$where .= " and  date_format(cu.date,'%Y-%m-%d') between  '$startDate' and '$endDate' ";
			}else if($publish_div == "3"){
				$where .= " and  date_format(cmd.recent_order_date,'%Y-%m-%d') between  '$startDate' and '$endDate' ";
			}else if($publish_div == "4"){
				$where .= " and  date_format(cu.last,'%Y-%m-%d') between  '$startDate' and '$endDate' ";
			}
		}
*/

		
		
		$sql = "select count(*) as total from shop_cupon_regist cr, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd
					where cr.publish_ix = '".$publish_ix."' and cr.mem_ix = cu.code and cu.code = cmd.code  ";
		//echo nl2br($sql);
		$db->query($sql);
		$db->fetch();
		$pre_issue_total = $db->dt[total];
		//echo "pre_issue_total:".$pre_issue_total;
	
if($_GET[mode] == "" || $_GET[mode] == "search"){
	

	if($sub_mode=="search"){
		
		$sql = "select count(*) as total 
					from shop_cupon_regist cr, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg
				".$where." and cr.publish_ix = '".$publish_ix."' and cr.mem_ix = cu.code and cu.code = cmd.code  ";

		$db->query($sql);
		$db->fetch();
		$search_issue_total = $db->dt[total];

		
		$sql = "select count(*) as total from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg ".$where."  ";

		//echo $sql;
		$db->query($sql);
		$db->fetch();
		$total = $db->dt[total];

		

		if($db->dbms_type == "oracle"){
			$sql = "select cu.code, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, cu.id, AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(cmd.pcs,'".$db->ase_encrypt_key."') as pcs , cmd.gp_ix, cu.last as last, cu.date_ as regdate2,
						cmd.recent_order_date as recent_order_date,  mg.gp_name
						from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg
						".$where."  order by ".$orderby." ".$ordertype."  limit $start,$max ";
		}else{
			$sql = "select cu.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id, 
						AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, 
						AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs , 
						cmd.gp_ix, cu.last as last, cu.date as regdate2,
						cmd.recent_order_date as recent_order_date,  mg.gp_name
						from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg
						".$where."  order by ".$orderby." ".$ordertype."  limit $start,$max ";
		}

/* date_format 변환
		if($db->dbms_type == "oracle"){
			$sql = "select cu.code, AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, cu.id, AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(cmd.pcs,'".$db->ase_encrypt_key."') as pcs , cmd.gp_ix, date_format(cu.last,'%Y-%m-%d') as last, date_format(cu.date_,'%Y-%m-%d') as regdate2,
						date_format(cmd.recent_order_date,'%Y-%m-%d') as recent_order_date,  mg.gp_name
						from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg
						".$where."  order by ".$orderby." ".$ordertype."  limit $start,$max ";
		}else{
			$sql = "select cu.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id, 
						AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, 
						AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs , 
						cmd.gp_ix, date_format(cu.last,'%Y-%m-%d') as last, date_format(cu.date,'%Y-%m-%d') as regdate2,
						date_format(cmd.recent_order_date,'%Y-%m-%d') as recent_order_date,  mg.gp_name
						from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd , ".TBL_SHOP_GROUPINFO." mg
						".$where."  order by ".$orderby." ".$ordertype."  limit $start,$max ";
		}
*/
		/*
		$sql = "select code,name,id,mail, pcs ,perm , mm.gp_ix, date_format(mm.last,'%Y-%m-%d') as last, date_format(mm.date,'%Y-%m-%d') as date,
						date_format(mm.recent_order_date,'%Y-%m-%d') as recent_order_date, cr.regist_ix , mg.organization_name, mg.gp_name
						from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd left join ".TBL_SHOP_CUPON_REGIST." cr on mm.code = cr.mem_ix and cr.publish_ix = '$publish_ix'
						, ".TBL_SHOP_GROUPINFO." mg
						".$where." order by ".$orderby." ".$ordertype."  limit $start,$max ";
		*/
		$db->query($sql);
		$members = $db->fetchall();
		//echo nl2br($sql);
	}
	
}else if($_GET[mode] == "result"){


	$sql = "select count(*) as total from shop_cupon_regist cr, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg
					".$where." and cr.publish_ix = '".$publish_ix."' and cr.mem_ix = cu.code    ";
							//	echo $sql;
	//and mem_type in ('M','C','F','S')
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];
	//$pre_issue_total = $total;
	//echo $max;

	if($db->dbms_type == "oracle"){
		//and mem_type in ('M','C','F','S')
		$sql = "select cr.regist_ix,  cp.use_date_type,cr.mem_ix,AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, cu.id, cr.regdate, AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail, cr.use_yn ,cr.use_sdate, cr.use_date_limit,
			case when (cr.use_sdate <= ".date("Ymd")." and ".date("Ymd")." <= cr.use_date_limit) then 1 else 0 end use_priod_yn
			from ".TBL_SHOP_CUPON_PUBLISH." cp, shop_cupon_regist cr, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg
			".$where." and cp.publish_ix = '".$publish_ix."' AND cp.publish_ix = cr.publish_ix and cr.mem_ix = cu.code 
			limit $start,$max ";
			//echo $sql ;
	}else{
		//and mem_type in ('M','C','F','S')
		$sql = "select cr.regist_ix, cp.use_date_type,cr.mem_ix,AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id, cr.regdate, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cr.use_yn ,cr.use_sdate, cr.use_date_limit,
			case when (cr.use_sdate <= ".date("Ymd")." and ".date("Ymd")." <= cr.use_date_limit) then 1 else 0 end use_priod_yn
			from ".TBL_SHOP_CUPON_PUBLISH." cp, shop_cupon_regist cr, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg
			".$where." and cp.publish_ix = '".$publish_ix."' AND cp.publish_ix = cr.publish_ix and cr.mem_ix = cu.code  
			order by name
			limit $start,$max ";
	}
	//echo nl2br($sql);
	//echo "<br>".$where;

	$db->query($sql);
	$issued_members = $db->fetchall();


}else if($_GET[mode] == "statistics"){
	$sql = "select count(*) as total from shop_cupon_regist cr
					where cr.publish_ix = '".$publish_ix."' and cr.use_yn != '0'    ";
							//	echo $sql;
	//and mem_type in ('M','C','F','S')
	$db->query($sql);
	$db->fetch();
	$user_total = $db->dt[total];
	
	$sql = "select sum(od.dcprice) as total_price, sum(odd.dc_price) as use_coupon_price from shop_cupon_regist cr
					left join ".TBL_SHOP_ORDER." o on cr.mem_ix = o.user_code
					left join shop_order_detail_discount odd on o.oid = odd.oid
					left join ".TBL_SHOP_ORDER_DETAIL." od on odd.od_ix = od.od_ix

					where cr.publish_ix = '".$publish_ix."' and cr.use_yn != '0' and odd.dc_type = 'CP' and od.status not in('SR') ";
	$db->query($sql);
	$db->fetch();
	
	$ross_per = 0;
	if($db->dt[total_price]){
		$ross_per = round(($db->dt[total_price]/$db->dt[use_coupon_price])*100,1);
	}

	$use_coupon_price = $db->dt[use_coupon_price];
	

	
	$sql = "select count(*) as total from shop_cupon_regist cr, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg
					".$where." and cr.publish_ix = '".$publish_ix."' and cr.mem_ix = cu.code and cr.use_yn != '0'    ";
							//	echo $sql;
	//and mem_type in ('M','C','F','S')
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];
		
	if($db->dbms_type == "oracle"){
		//and mem_type in ('M','C','F','S')
		$sql = "select cr.regist_ix,  cp.use_date_type,cr.mem_ix,AES_DECRYPT(cmd.name,'".$db->ase_encrypt_key."') as name, cu.id, cr.regdate, AES_DECRYPT(cmd.mail,'".$db->ase_encrypt_key."') as mail, cr.use_yn ,cr.use_sdate, cr.use_date_limit, 
			case when (cr.use_sdate <= ".date("Ymd")." and ".date("Ymd")." <= cr.use_date_limit) then 1 else 0 end use_priod_yn
			from ".TBL_SHOP_CUPON_PUBLISH." cp, shop_cupon_regist cr, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg
			".$where." and cp.publish_ix = '".$publish_ix."' AND cp.publish_ix = cr.publish_ix and cr.mem_ix = cu.code and cr.use_yn != '0' 
			limit $start,$max ";
			//echo $sql ;
	}else{
		//and mem_type in ('M','C','F','S')
		$sql = "select cr.regist_ix, cp.use_date_type,cr.mem_ix,AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cu.id, cr.regdate, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, cr.use_yn ,cr.use_sdate, cr.use_date_limit, cr.use_oid, cr.use_pid,
			case when (cr.use_sdate <= ".date("Ymd")." and ".date("Ymd")." <= cr.use_date_limit) then 1 else 0 end use_priod_yn
			from ".TBL_SHOP_CUPON_PUBLISH." cp, shop_cupon_regist cr, ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_SHOP_GROUPINFO." mg
			".$where." and cp.publish_ix = '".$publish_ix."' AND cp.publish_ix = cr.publish_ix and cr.mem_ix = cu.code  and cr.use_yn != '0' 
			order by name
			limit $start,$max ";
	}
	//echo nl2br($sql);
	//echo "<br>".$where;

	$db->query($sql);
	$issued_members = $db->fetchall();
}

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center >
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=3 style='padding-bottom:10px;'> ".GetTitleNavigation("고정단가 회원그룹 등록 및 발급현황", "전시관리 > 고정단가 회원그룹 등록 및 발급현황 ",false)."</td>
			</tr>
			<tr>
				<td style='padding:0px 10px 0 10px' >
					<table width=100% border=0 cellpadding=0 cellspacing=0>

						<tr>
					    <td align='left' colspan=4 style='padding-bottom:20px;'>
					    	<div class='tab'>
									<table class='s_org_tab'>
									<tr>
										<td class='tab'>
											<table id='tab_01' ".($_GET[mode] == "" || $_GET[sub_mode] == "search" ? "class='on'":"")." >
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?publish_ix=".$publish_ix."&mmode=".$mmode."&mem_ix=".$mem_ix."'\">고정단가 회원그룹 등록 하기</td>
												<th class='box_03'></th>
											</tr>
											</table>
											<table id='tab_02' ".($_GET[mode] == "result" ? "class='on'":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?mode=result&publish_ix=".$publish_ix."&mmode=".$mmode."&mem_ix=".$mem_ix."'\">고정단가 회원그룹 등록현황(".$pre_issue_total.")</td>
												<th class='box_03'></th>
											</tr>
											</table>
											<table id='tab_03' ".($_GET[mode] == "statistics" ? "class='on'":"").">
											<tr>
												<th class='box_01'></th>
												<td class='box_02' onclick=\"document.location.href='?mode=statistics&publish_ix=".$publish_ix."&mmode=".$mmode."&mem_ix=".$mem_ix."'\">고정단가 회원그룹  통계</td>
												<th class='box_03'></th>
											</tr>
											</table>
										
										</td>
										<td class='btn'>

										</td>
									</tr>
									</table>
								</div>
					    </td>
						</tr>";
//http://gst.forbiz.co.kr/admin/member/mrogroup_register_user.php?dupe_check=&sub_mode=search&mgp_ix=1&orderby=cmd.name&ordertype=asc&search_issue_total=&mode=search&act=regist_search_update&search_type=name&search_text=&search_texts=&gp_ix=&publish_div=1&startDate=&endDate=&x=33&y=20
if($_GET[mode] == "" || $_GET["mode"] == "search"){
	$Contents .= "			<tr><td style='padding-left:0px;text-align:left;'><img src='../images/dot_org.gif' align=absmiddle> <b class='blue' id='coupon_kind'>".$gp_name."</b> 에 대한 고정단가 회원그룹을 발급합니다.</td></tr>";
}else if($_GET[mode] == "result"){
	$Contents .= "			<tr><td style='padding-left:0px;text-align:left;'><img src='../images/dot_org.gif' align=absmiddle> <b class='blue' id='coupon_kind'>".$gp_name."</b> 등록한 사용자 목록입니다</td></tr>";
}else if($_GET[mode] == "statistics"){
	$Contents .= "			<tr><td style='padding-left:0px;text-align:left;'><img src='../images/dot_org.gif' align=absmiddle> <b class='blue' id='coupon_kind'>".$gp_name."</b> 등록한 통계 목록입니다</td></tr>";
}

if($_GET[mode] == "statistics"){
$Contents .= "
						<tr height=30px>
							<td style='padding-top:10px' colspan=2>
								<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>

								<tr style='padding-bottom:30px;'>
									<th class='box_04'></th>
									<td class='box_05' align=center >
									<table border='0' cellspacing='0' cellpadding='3' width='100%' class='table_box'>
										<col width='20%'>
										<col width='30%'>
										<col width='20%'>
										<col width='30%'>
										<tr height='30' valign='middle'>
											<td class='search_box_title'> <b>발행구분</b></td>
											";
											if ($publish_type == 1){
											$Contents .= "
											<td class='search_box_item'>고객지정발행</td>";
											}else{
											$Contents .= "
											<td class='search_box_item'>".$_PUBLISH_TYPE[$publish_type]["text"]."</td>";
											}
											$Contents .= "
											<td class='search_box_title'> <b>등록수</b></td>
											<td class='search_box_item'>".$pre_issue_total."</td>
										</tr>
										<tr height='30' valign='middle'>
											<td class='search_box_title'> <b>사용수/할인총액</b></td>
											<td class='search_box_item'>".$user_total." / ".number_format($use_coupon_price)."</td>

											<td class='search_box_title'> <b>ROAS</b></td>
											<td class='search_box_item'>".$ross_per."%</td>
										</tr>
									</table>
									</td>
								</tr>
								</table>
							</td>
						</tr>";

}
//echo $sql;
$Contents .= "			<tr height=30px>
							<td style='padding-top:10px' colspan=2>
								<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
								<tr>
									<th class='box_01'></th>
									<td class='box_02'></td>
									<th class='box_03'></th>
								</tr>
								<tr>
									<th class='box_04'></th>
									<td class='box_05' align=center >
										<form name='member_search' id='member_search' method='get' >
										<input type='hidden' name='dupe_check' value=''>
										<input type='hidden' name='sub_mode' value='search'>
										<input type='hidden' name='mgp_ix' value=".$mgp_ix.">
										<input type='hidden' name='orderby' value='".$orderby."'>
										<input type='hidden' name='ordertype' value='".$ordertype."'>
										<input type='hidden' name='search_issue_total' id='search_issue_total' value='".$search_issue_total."'>
										
										<input type='hidden' name='mode' value='".$mode."'>
										<input type='hidden' name='act' value='regist_search_update'>
										<table border='0' cellspacing='0' cellpadding='3' width='100%' class='search_table_box'>
											<col width='100'>
											<col width='*'>
											<col width='100'>
											<col width='250'>
											<tr height='30' valign='middle'>
												<td class='search_box_title'> <b>회원검색</b></td>
												<td class='search_box_item'>
													<table cellpadding=0 cellspacing=0>
														<tr>
															<td>
															<select name='search_type' >
																<option value='name' ".($search_type == "name" ? "selected":"")."> 이름 </option>
																<option value='id' ".($search_type == "id" ? "selected":"")."> 아이디</option>																
																<option value='pcs' ".($search_type == "pcs" ? "selected":"")."> 핸드폰 </option>
																<option value='mail' ".($search_type == "mail" ? "selected":"")."> 이메일 </option>
															</select>
															</td>
															<td style='padding:5px 3px'>
																<input type='text' name='search_text' id='search_text_single' class='textbox' style='width:200px;".($multi_search == 1 ? "display:none;":"" )."' size='20' value='".$search_text."'>
																<textarea class='textbox' name='search_texts' id='search_text_multi'  style='width:200px;height:100px;".($multi_search == 1 ? "display:block;":"display:none;" )."' >".$search_texts."</textarea>
															</td>
															<td><input type=checkbox name='multi_search' id='multi_search' value=1 onclick=\"$('#search_text_single').toggle();$('#search_text_multi').toggle();\" ".($multi_search == 1 ? "checked":"" )."><label for='multi_search'>다중검색</label></td>
														</tr>
													</table>
												</td>
												<td class='search_box_title'> <b>회원그룹</b></td>
												<td class='search_box_item'>
													".makeGroupSelectBox($db,"gp_ix",$gp_ix)."
												</td>
											</tr>
											<!--tr height='30' valign='middle'>
												<td class='search_box_title' ><b>등록조건</b></td>
												<td class='search_box_item' colspan=3 >
													
												</td>
											</tr-->
											<tr height=10>
												  <td class='search_box_title'  ><label for='regdate'><b>일자</b></label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.member_search);'></td>
												  <td class='search_box_item' colspan=3 >
													<div style='padding:5px;'>
													<input type='radio' name='publish_div' id='publish_div_1' onFocus='this.blur();' align='middle' value='1' style='border:0px;' ".($publish_div == '1' || $publish_div == '' ?' checked':'')."><label for='publish_div_1'>모두보기</label>&nbsp;
													  <input type='radio' name='publish_div' id='publish_div_2' onFocus='this.blur();' align='middle' value='2' style='border:0px;' ".CompareReturnValue($publish_div,'2',' checked')."><label for='publish_div_2'>신규회원가입</label>&nbsp;
													  <input type='radio' name='publish_div' id='publish_div_3' onFocus='this.blur();' align='middle' value='3' style='border:0px;' ".CompareReturnValue($publish_div,'3',' checked')."><label for='publish_div_3'>최근구매회원</label>
													  <input type='radio' name='publish_div' id='publish_div_4' onFocus='this.blur();' align='middle' value='4' style='border:0px;' ".CompareReturnValue($publish_div,'4',' checked')."><label for='publish_div_4'>최근방문회원</label>
													 </div>
												  ".search_date('startDate','endDate',$startDate,$endDate,'N','D')."
													  <!--table cellpadding=0 cellspacing=1 border=0 bgcolor=#ffffff>
														  <tr>
															  <td nowrap><SELECT onchange='javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD)' name=FromYY ></SELECT> 년 <SELECT onchange='javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD)' name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
															  <td  align=center> ~ </TD>
															  <td  nowrap><SELECT onchange='javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD)' name=ToYY></SELECT> 년 <SELECT onchange='javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD)' name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
															  <td>

															  </td>
														  </tr>
													  </table-->
												  </td>
											  </tr>
											  <!--tr height='10' valign='middle'>
												<td class='search_box_item' style='padding-left:5px;' colspan=3>
												  <a href=\"javascript:select_date('".$voneweekago."','".$today."',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
												  <a href=\"javascript:select_date('".$v15ago."','".$today."',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
												  <a href=\"javascript:select_date('".$vonemonthago."','".$today."',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
												  <a href=\"javascript:select_date('".$v2monthago."','".$today."',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
												  <a href=\"javascript:select_date('".$v3monthago."','".$today."',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
												</td>
											</tr-->
										</table>

									</td>
									<th class='box_06'></th>
								</tr>
								<tr>
									<th class='box_07'></th>
									<td class='box_08'></td>
									<th class='box_09'></th>
								</tr>
								</table>
						<!--
								검색 :
								<select name='search_type'>
									<option value='id'> 아이디</option>
									<option value='name'> 이름</option>
								</select> <input type='text' name='search_text'> <img src='../images/btn/ok.gif' style='vertical-align:middle' onclick=\"searchGo('".$publish_ix."')\">
								-->
							</td>
						</tr>
						<tr >
                <td colspan=5 align=center  style='padding:10px 0 0 0'>
                    <input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='border:0px;'>
                </td>
            </tr>
            </form>";
if($_GET[mode] == "" || $_GET[mode] == "search"){
$Contents .= "
						<tr align=right>
							<td style='text-align:left;'>회원수 : ".$total." 명<inpu type=hidden name='search_total' id='search_total'  value='".$total."'></td>
							<td style='padding-top:10px' valign='bottom'> 정렬 :
							<b class=small>이름</b>
							<a href='cupon_register_user.php?publish_ix=".$publish_ix."&orderby=name&ordertype=asc'><img src='../image/orderby_desc.gif' border=0 align=top title='가나다순'></a>
							<a href='cupon_register_user.php?publish_ix=".$publish_ix."&orderby=name&ordertype=desc'><img src='../image/orderby_asc.gif' border=0 align=top title='가나다역순'></a>
							<b class=small>아이디</b>
							<a href='cupon_register_user.php?publish_ix=".$publish_ix."&orderby=id&ordertype=asc'><img src='../image/orderby_desc.gif' border=0 align=top title='abcd순'></a>
							<a href='cupon_register_user.php?publish_ix=".$publish_ix."&orderby=id&ordertype=desc'><img src='../image/orderby_asc.gif' border=0 align=top title='abcd역순'></a>
							<b class=small>등록일</b>
							<a href='cupon_register_user.php?publish_ix=".$publish_ix."&orderby=cu.date&ordertype=desc'><img src='../image/orderby_desc.gif' border=0 align=top title='최근등록순'></a>
							<a href='cupon_register_user.php?publish_ix=".$publish_ix."&orderby=cu.date&ordertype=asc'><img src='../image/orderby_asc.gif' border=0 align=top title='최근등록역순'></a>

							</td>
						</tr>
						
						<tr>
							<td style='padding:10px 0px 0px 0px;height:360px;' colspan=2 valign=top>
								<form name='cupon_pop_frm' id='cupon_pop_frm' method='post' action='mrogroup.act.php' target='act'>
								<input type='hidden' name='mgp_ix' value=".$mgp_ix.">
								<input type='hidden' name='act' value='regist_update'>
								<table width=100% border=0 cellpadding=0 cellspacing=0 class='list_table_box'>
									<col width='30'>
									<col width='80'>
									<col width='90'>
									<col width='80'>
									<col width='*'>
									<col width='100'>
									<col width='150'>
									<col width='150'> 
									
									<tr height=40 >
										<td class='s_td'><input type='checkbox' name='all' style='border:0' id='code' onclick='allCheck(document.cupon_pop_frm)'></td>
										<td class='m_td'>회원그룹</td>
										<td class='m_td'>이름</td>
										<td class='m_td'>아이디</td>
										<td class='m_td'>이메일</td>
										<td class='m_td'>핸드폰</td>
										<td class='m_td'>최근방문일<br>최근구매일</td>
										<td class='e_td'>회원가입일</td>
										
									</tr>";


if(count($members) > 0){
									for($i=0;$i < count($members);$i++){
										//$db->fetch($i);
$Contents .= "
									<tr height=40>
										<td class='list_box_td list_bg_gray' ><input type='checkbox' name='code[]' id='code' value='".$members[$i][code]."' user_name='".$members[$i][name]."' style='border:0' ".($members[$i][regist_ix] ? "title='이미 등록된 사용자 입니다.' disabled":"")."></td>
										<td class='list_box_td' ><span>".$members[$i][gp_name]."</span></td>
										<td class='list_box_td point' >".$members[$i][name]."</td>
										<td class='list_box_td list_bg_gray' >".$members[$i][id]."</td>
										<td class='list_box_td' >".$members[$i][mail]."</td>
										<td class='list_box_td list_bg_gray' >".$members[$i][pcs]."</td>
										<td class='list_box_td' >".$members[$i][last]."<br>".$members[$i][recent_order_date]."</td>
										<td class='list_box_td' >".$members[$i][regdate2]."</td>
									</tr>
									";
									}

/*

										<td class='list_box_td' >".$members[$i][last]."</td>
										<td class='list_box_td list_bg_gray' >".$members[$i][recent_order_date]."</td>
										<td class='list_box_td' >".$members[$i][regdate2]."</td>

*/

}else{
					
							if($sub_mode=="search"){
								$Contents .= "
								<tr height=50><td colspan=9 class='list_box_td'  align=center>검색된 회원이 없습니다.</td></tr>";
							}else{
								$Contents .= "
								<tr height=50><td colspan=9 class='list_box_td'  align=center>검색조건을 입력후 검색버튼을 클릭하세요.</td></tr>";
							}
}
$Contents .= "				</table>
									<table width=100% border=0 cellpadding=0 cellspacing=0 >
									<tr>
										<td colspan=4 style='padding:10px 0px' align=left>
											<a href=\"javascript:selectedCouponReg('cupon_pop_frm');\"><img src='../images/".$admininfo["language"]."/btn_selected_cupon_reg.gif' align=absmiddle  border=0></a>";

									if($mode == "search"){
									$Contents .= "
											&nbsp;&nbsp;&nbsp;
											<a href=\"javascript:searchCouponReg('member_search');\"><img src='../images/".$admininfo["language"]."/btn_searched_cupon_reg.gif' align=absmiddle border=0></a> 
											 ";
									}else{
									$Contents .= "
											&nbsp;&nbsp;&nbsp;<a href=\"javascript:alert('검색후 사용하실수 있습니다.');\"><img src='../images/".$admininfo["language"]."/btn_searched_cupon_reg.gif' align=absmiddle border=0></a>  ";
									}
								$Contents .= "
										</td>
									</tr>
									<tr>
										<td colspan=5 align=right style='padding:10px 0;letter-spacing:0'>".page_bar($total, $page, $max,"&publish_ix=".$publish_ix."&sub_mode=".$mode."&search_type=".$search_type."&search_text=".$search_text."&orderby=$orderby&ordertype=$ordertype&publish_div=$publish_div&gp_ix=$gp_ix&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD","")."</td></tr>

								</table>
								</form>
							</td>
						</tr>

						
					</table>
				</td>
				</tr>";

}else if($_GET[mode] == "result"){
$Contents .= "
				
				<tr height=30px>
					<td style='padding-top:10px;height:460px;' valign=top>
					<form name='issued_list' id='issued_list' method='POST' action='mrogroup.act.php' target='act'>
					<input type='hidden' name='mgp_ix' value='".$mgp_ix."'>
					<input type='hidden' name='act' value='regist_delete'>
					<table width=100% border=0 cellpadding=0 cellspacing=0 class='list_table_box'>
						<col width='5%'>
						<col width='13%'>
						<col width='13%'>
						<col width='*'>
						<col width='17%'>
						<col width='10%'>
						<col width='10%'>
						<tr height=30 align=center>
							<td class='s_td'><input type='checkbox' name='all' id='code'  style='border:0' onclick='registedAllCheck(document.issued_list)'></td>
							<td class='m_td'>이름</td>
							<td class='m_td'>아이디</td>
							<td class='m_td'>사용기간</td>
							<td class='m_td'>등록일자</td>
							<td class='m_td'>잔여기간</td>
							<td class='e_td'>사용여부</td>
						</tr>";

					if($total){
							for($i=0;$i<count($issued_members);$i++){
								//$db->fetch($i);

							if($issued_members[$i][use_date_type] != 9){
								if($issued_members[$i][use_yn] == 0) {
									if($issued_members[$i][use_sdate] <= date("Ymd") && date("Ymd") <= $issued_members[$i][use_date_limit]){
										$use_str =   "사용가능";
										$useable_yn = 1;
										$dday = date_diff2(date("Y-m-d"), ChangeDate($issued_members[$i][use_date_limit],"Y-m-d"));
									}else if($issued_members[$i][use_sdate] > date("Ymd")){
										$use_str =   "사용대기";
										$useable_yn = 1;
										$dday = date_diff2(date("Y-m-d"), ChangeDate($issued_members[$i][use_date_limit],"Y-m-d"));
									} else {
										$use_str =   "기간만료";
										$useable_yn = 1;
										$dday = "-";
									}
								} else {
									$use_str =   "사용완료";
									$useable_yn = 0;
									$dday = "-";
								}
							}else{
								if($issued_members[$i][use_yn] == 0) {
									$use_str =   "사용가능";
									$useable_yn = 1;
									$dday = date_diff2(date("Y-m-d"), ChangeDate($issued_members[$i][use_date_limit],"Y-m-d"));
								} else {
									$use_str =   "사용완료";
									$useable_yn = 0;
									$dday = "-";
								}
							}

$Contents .= "
						<tr height=30 align=center>
							<td class='list_box_td point'><input type='checkbox' name='regist_ix[]' style='border:0px' id='regist_ix' value='".$issued_members[$i][regist_ix]."' user_name='".$issued_members[$i][name]."' ".(($issued_members[$i][use_yn] == 0 && $useable_yn == 1) ? "":"disabled")."></td>
							<td class='list_box_td'>".$issued_members[$i][name]."</td>
							<td class='list_box_td list_bg_gray'>".$issued_members[$i][id]."</td>
							<td class='list_box_td'>".($issued_members[$i][use_date_type]!=9 ? ChangeDate($issued_members[$i][use_sdate],"Y-m-d")."~".ChangeDate($issued_members[$i][use_date_limit],"Y-m-d"):"기간제한없음")."</td>
							<td class='list_box_td list_bg_gray'>".$issued_members[$i][regdate]."</td>
							<td class='list_box_td'> ".$dday."</td>
							<td class='list_box_td list_bg_gray' title='".$issued_members[$i][use_sdate]."~".$issued_members[$i][use_date_limit]."'>";

$Contents .= 	$use_str;
$Contents .= "
							</td>
						</tr>";

							}
						}else{
$Contents .= "
						<tr height=50><td colspan=7 class='list_box_td'  align=center>등록된 사용자가 없습니다.</td></tr>";

						}

 $Contents .= "	</table>
						</form>
						<table width=100% border=0 cellpadding=0 cellspacing=0 >
						<tr>
							<td colspan=2 style='padding:5px 5px 5px 0px' align=left>
								<a href=\"javascript:canceledCoupon('issued_list');\"><img src='../images/".$admininfo["language"]."/btn_issue_cancle.gif' align=absmiddle></a>
							</td>
							<td colspan=4 align=right style='padding:10px 0px;letter-spacing:0'>".page_bar($total, $page, $max,"&publish_ix=".$publish_ix."&mode=".$mode,"")."</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' align='left'>고정단가 회원그룹 <b class='blue small'>".$group_name."</b> 에 대한 고정단가 회원그룹 등록 목록 입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small'  align='left'>삭제를 원하시는 목록을 선택하여 등록 취소 할 수 있습니다. 단 이미 사용한 고정단가 회원그룹은 삭제 하실 수 없습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small'  align='left'>같은 고정단가 회원그룹은 중복 등록할 수 없습니다.</td></tr>
</table>
";


$Contents .= HelpBox("고정단가 회원그룹 등록", $help_text,'60');
$Contents .= "
				</td>
				</tr>
				</form>";

}else if($_GET[mode] == "statistics"){
$Contents .= "
				<tr height=30px>
					<td style='padding-top:10px;'align='right'>";
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
						//$innerview .= " <a href='product_list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
						$Contents .= "<a href='cupon_statistics_excel.php?".$_SERVER["QUERY_STRING"]."'><input type='image' src='../images/".$admininfo["language"]."/btn_excel_save.gif' style='cursor:pointer;' ></a>";
					}else{
						$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
					}
					$Contents .= " 
					</td>
				</tr>
				<tr height=30px>
					<td style='padding-top:10px;height:460px;' valign=top>
					<table width=100% border=0 cellpadding=0 cellspacing=0 class='list_table_box'>
						<col width='10%'>
						<col width='18%'>
						<col width='20%'>
						<col width='*'>
						<col width='7%'>
						<col width='10%'>
						<col width='10%'>
						<col width='15%'>
						<tr height=45 align=center>
							<td class='m_td'>이름<br>(아이디)</td>
							<td class='m_td'>주문번호</td>
							<td class='m_td'>상품명<br>(상품코드)</td>
							<td class='m_td'>옵션명</td>
							<td class='m_td'>수량</td>
							<td class='m_td'>상품금액</td>
							<td class='m_td'>할인금액</td>
							<td class='e_td'>업체명</td>
						</tr>";

					if($total){
							for($i=0;$i<count($issued_members);$i++){
								//$db->fetch($i);
						
						$sql = "select od.dcprice, odd.dc_price as couponprice, od.oid,od.pname, od.option_text, od.pcode, od.company_name, od.pcnt 
						from ".TBL_SHOP_ORDER_DETAIL." od 
						LEFT JOIN shop_order_detail_discount odd on(od.oid = odd.oid and od.od_ix = odd.od_ix)
						where od.oid = '".$issued_members[$i][use_oid]."' and od.pid = '".$issued_members[$i][use_pid]."' and odd.dc_type = 'cp' ";
						$db->query($sql);
						$db->fetch();

						//
							
						if($db->dt[pcode]){
							$pcode = "(".$db->dt[pcode].")";
						}else{
							$pcode = "";
						}
$Contents .= "
						<tr height=30 align=center>
							<td class='list_box_td'>".$issued_members[$i][name]."<br>(".$issued_members[$i][id].")</td>
							<td class='list_box_td list_bg_gray'>".$issued_members[$i][use_oid]."</td>
							<td class='list_box_td'>".$db->dt[pname]."<br>".$pcode."</td>
							<td class='list_box_td list_bg_gray'>".$db->dt[option_text]."</td>
							<td class='list_box_td'> ".$db->dt[pcnt]."</td>
							<td class='list_box_td list_bg_gray'>".number_format($db->dt[dcprice])."</td>
							<td class='list_box_td list_bg_gray'>".number_format($db->dt[couponprice])."</td>
							<td class='list_box_td '>".$db->dt[company_name]."</td>";


$Contents .= 	$use_str;
$Contents .= "
							</td>
						</tr>";

							}
						}else{
$Contents .= "
						<tr height=100><td colspan=8 class='list_box_td'  align=center>사용된 고정단가 회원그룹이 없습니다.</td></tr>";
						
						}

 $Contents .= "	</table>
						<table width=100% border=0 cellpadding=0 cellspacing=0 >
						<tr>
							<td colspan=2 style='padding:5px 5px 5px 0px' align=left>
								
							</td>
							<td colspan=4 align=right style='padding:10px 0px;letter-spacing:0'>".page_bar($total, $page, $max,"&publish_ix=".$publish_ix."&mode=".$mode,"")."</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' align='left'>고정단가 회원그룹 <b class='blue small'>".$group_name."</b> 에 대한 고정단가 회원그룹 등록 목록 입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small'  align='left'>삭제를 원하시는 목록을 선택하여 등록 취소 할 수 있습니다. 단 이미 사용한 고정단가 회원그룹은 삭제 하실 수 없습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small'  align='left'>같은 고정단가 회원그룹은 중복 등록할 수 없습니다.</td></tr>
</table>
";


$Contents .= HelpBox("고정단가 회원그룹 등록", $help_text,'60');
$Contents .= "
				</td>
				</tr>
				</form>";
}
$Contents .= "

			<!--tr>
				<td style='padding-top:10px' align=center><a href='javascript:self.close();'><img src='../images/".$admininfo["language"]."/btn_close.gif' style='border:0'></a></td>
			</tr-->
		</table>
		</td>
	</tr>
</TABLE>";





$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "전시관리 > 고정단가 회원그룹 등록현황";
$P->NaviTitle = "고정단가 회원그룹 등록현황";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>