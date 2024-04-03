<?
include("../class/layout.class");

//print_r($admininfo);
if(!$admininfo[company_id]){
	echo("<script>alert('업체가 선택되지 않았습니다 확인후 다시 시도해주세요');history.back();</script>");
	exit;
}

$cdb = new Database;
$db = new Database;

$cdb->query("SELECT com_name FROM common_user cu , common_member_detail cmd, common_company_detail ccd WHERE cu.code = cmd.code and cu.company_id = ccd.company_id and ccd.company_id = '".$admininfo["company_id"]."'");
$cdb->fetch();

$company_name = $cdb->dt[com_name];

if($db->dbms_type == "oracle"){
	$sql = "SELECT cmd.code,AES_DECRYPT(jumin) as jumin,birthday,birthday_div,AES_DECRYPT(name) as name,
	AES_DECRYPT(mail) as mail, AES_DECRYPT(zip) as zip,AES_DECRYPT(addr1) as addr1,
	AES_DECRYPT(addr2) as addr2,AES_DECRYPT(tel) as tel,tel_div,AES_DECRYPT(pcs) as pcs,
	info,sms,nick_name,job,cmd.date_,cmd.file_,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*
			FROM common_user cu
			left join common_member_detail cmd on cu.code = cmd.code
			left join common_company_detail ccd on cu.company_id = ccd.company_id
			WHERE   ccd.company_id = '".$admininfo["company_id"]."'
			and cu.code ='$code' ";
}else{
	$sql = "SELECT cmd.code,birthday,birthday_div,AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
	AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
	AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
	info,sms,nick_name,job,cmd.date,cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*
			FROM common_user cu
			left join common_member_detail cmd on cu.code = cmd.code
			left join common_company_detail ccd on cu.company_id = ccd.company_id
			WHERE   ccd.company_id = '".$admininfo["company_id"]."'
			and cu.code ='$code' ";
}

$db->query($sql);


if($db->total){
	$act = "user_update";
	$db->fetch();
//print_r($db->dt);
	$tel = explode("-",$db->dt[tel]);
	$pcs = explode("-",$db->dt[pcs]);
	$code = $db->dt[code];
}else{
	$act = "user_insert";

}

$Contents03 .= "<table width='100%' cellpadding=0 cellspacing=0 border='0'  >
				<col width=20%>
				<col width=*>
				<col width=30%>
				<col width=20%>
				  <tr>
					<td align='left' colspan=4 > ".GetTitleNavigation("관리자 수정/등록", "상점관리 > 관리자 수정/등록 <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=".urlencode("몰스토리동영상메뉴얼_관리자설정(090322)_config.xml")."',800,517,'manual_view')\"  title='관리자설정 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle style='margin:0 0 2 0'></a>")."</td>
				  </tr>
				   <tr>
					<td align='left' colspan=4 style='padding-bottom:15px;'>
					<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>
								<table id='tab_01' class='on' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02'  ><a href='admin_manage_list.php'>관리자목록</a></td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_02' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02'  ><a href='admin_manage.php'>관리자등록</a></td>
									<th class='box_03'></th>
								</tr>
								</table>
								<!--
								<table id='tab_03' ".(($info_type == "basic" ) ? "class='on' ":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02'  ><a href='./department.add.php?info_type=basic&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;본부/부서&nbsp;&nbsp; </a></td>
									<th class='box_03'></th>
								</tr>
								</table>
						
								<table id='tab_04' ".($info_type == "post_info" ? "class='on' ":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' >";
									if($company_id == ""){
										$Contents03 .= "<a href='./department.add.php?info_type=post_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직위&nbsp;&nbsp; </a>";
									}else{
										$Contents03 .= "<a href='./department.add.php?info_type=post_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직위&nbsp;&nbsp; </a>";
									}
									$Contents03 .= "
									</td>
									<th class='box_03'></th>
								</tr>
								</table>
								<table id='tab_05' ".($info_type == "position_info" ? "class='on' ":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' >";
									if($company_id == ""){
										$Contents03 .= "<a href='./department.add.php?info_type=position_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직책&nbsp;&nbsp; </a>";
									}else{
										$Contents03 .= "<a href='./department.add.php?info_type=position_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;직책&nbsp;&nbsp; </a>";
									}
									$Contents03 .= "

									</td>
									<th class='box_03'></th>
								</tr>
								</table>-->";
								/*
								if(checkMenuAuth(md5("/admin/store/department.php"),"R") ){
									$Contents03 .= "
								<table id='tab_03' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' ><a href='department.php'>부서관리</a></td>
									<th class='box_03'></th>
								</tr>
								</table>";
								}
								if(checkMenuAuth(md5("/admin/store/position.php"),"R") ){
								$Contents03 .= "
								<table id='tab_04' >
								<tr>
									<th class='box_01'></th>
									<td class='box_02' ><a href='position.php'>직급관리</a></td>
									<th class='box_03'></th>
								</tr>
								</table>";
								}*/
								$Contents03 .= "
							</td>
							<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

							</td>
						</tr>
						</table>
					</div>
					</td>
				</tr>

				<tr>
					<td align='left' colspan=4 style='padding-bottom:20px'>
					".get_company_user_list($admininfo[company_id])."
					</td>
				</tr>
				</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString1 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='verticol-align:top;'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=right>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle  valign='top' style='cursor:pointer; border:0px;' >
			<a href='?company_id=".$_GET["company_id"]."'><img src='../images/".$admininfo["language"]."/btn_add_new.gif' border=0 align=absmiddle  valign='top' style='cursor:pointer; border:0px;'></a>
		</td>
	</tr>
</table>
";
}


$Contents = "<form name='edit_form' action='company.act.php' method='post' onsubmit='return CheckFormUserValue(this)' target='act'>
<input name='act' type='hidden' value='$act'>
<input name='company_id' type='hidden' value='".$admininfo[company_id]."'>
<input type=hidden name='code' value='".$code."' >
";
$Contents = $Contents."<table width='100%' border=0>";
//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents .= $Contents03;
//$Contents = $Contents.$ContentsDesc03;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc03."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table ></form>";


$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<b>관리자 설정</b> <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_관리자설정(090322)_config.xml',800,517,'manual_view')\"  title='관리자설정 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' style='vertical-align:middle;'></a>", $help_text,105)."</div>";

$Contents = $Contents.$help_text;

$Script = "
<script language='javascript' src='basicinfo.js'></script>
<script language='javascript'>
function CheckFormUserValue(frm){


	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	if(frm.act.value == 'user_insert' || (frm.act.value == 'user_update' && frm.pw.checked)){
		if(frm.pw.value.length < 1){
			alert('비밀번호가 입력되지 않았습니다. ');
			frm.pw.focus();
			return false;
		}

		if(frm.pw_confirm.value.length < 1){
				alert('비밀번호가 확인 정보가 입력되지 않았습니다. ');
			frm.pw_confirm.focus();
			return false;
		}

		if(frm.pw.value != frm.pw_confirm.value){
			alert('비밀번호가 정확하지 않습니다 확인후 다시 입력해주세요');
			return false;
		}
	}
	return true;
}

function updateUserInfo(company_id, code)
{
	window.location.href = '?company_id='+company_id+'&code='+code+'&#user_add'
	//document.frames['act'].location.href='company.act.php?act=admin_log&company_id='+company_id+'&code='+code+'&#user_add';
}

function deleteUserInfo(company_id, code){

	if(confirm('사용자 정보를 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href='company.act.php?act=user_delete&company_id='+company_id+'&code='+code
	}
}

</script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "상점관리 > 관리자 설정 > 관리자 수정/등록";
$P->title = "관리자 수정/등록";
echo $P->PrintLayOut();



function get_company_user_list($company_id){
	global $admininfo,$db;

	$mstirng = "
		<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
		  <col width=15%>
		  <col width=*>
		  <col width=15%>
		  <col width=15%>
		  <col width=15%>
		  <col width=15%>
		  <tr height=25 bgcolor=#efefef align=center style='font-weight:bold'>
		    <td class='s_td'> 사용자명</td>
		    <td class='m_td'> 아이디</td>
		    <td class='m_td'> 권한템플릿</td>
		    <td class='m_td'> 이메일</td>
		    <td class='m_td'> 핸드폰</td>
		    <td class='m_td'> 등록일자</td>
		    <td class='e_td'> 사용관리</td>
		  </tr>";
	$mdb = new Database;
	if($admininfo["charger_id"] != "forbiz"){
		$forbiz_where = " and cu.id <> 'forbiz' ";
	}

	if($admininfo[admin_level] == 9){
		if($mdb->dbms_type == "oracle"){
			$sql = "SELECT cmd.code,AES_DECRYPT(name) as name,
					AES_DECRYPT(mail) as mail,AES_DECRYPT(pcs) as pcs,cmd.date_, cu.*
					FROM ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code ".$forbiz_where."
					WHERE  cu.company_id = '".$admininfo[company_id]."' and cu.mem_type = 'A' ".$forbiz_where." ";
		}else{
			$sql = "SELECT cmd.code,birthday,birthday_div,AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
					AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
					AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
					info,sms,nick_name,job,cmd.date,cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*
					FROM ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on  cu.code = cmd.code ".$forbiz_where."
					WHERE  cu.company_id = '".$admininfo[company_id]."' and cu.mem_type = 'A' ".$forbiz_where." order by auth,id asc";
		}

		$mdb->query($sql);
	}else{
		if(!$company_id){
			if($mdb->dbms_type == "oracle"){
				$sql = "SELECT cmd.code,AES_DECRYPT(name) as name,
					AES_DECRYPT(mail) as mail,AES_DECRYPT(pcs) as pcs,cmd.date_, cu.*
					FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd
					WHERE cu.code = cmd.code and cu.company_id = '".$admininfo[company_id]."' and cu.mem_type = 'S' ";
			}else{
				$sql = "SELECT cmd.code,birthday,birthday_div,AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
					AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
					AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
					info,sms,nick_name,job,cmd.date,cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*
					FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd
					WHERE cu.code = cmd.code and cu.company_id = '".$admininfo[company_id]."' and cu.mem_type = 'S' order by auth,id asc";
			}
			$mdb->query($sql);
		}
	}


	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);

		if($mdb->dbms_type == "oracle"){
			$date = $mdb->dt[date_];
		}else{
			$date = $mdb->dt[date];
		}

		$mstirng .= "
			  <tr bgcolor=#ffffff height=30 align=center>
			    <td class='list_box_td list_bg_gray' >".$mdb->dt[name]."</td>

			    <td class='list_box_td point' >".$mdb->dt[id]."</td>
			    <td class='list_box_td ' >".getAuthTemplet_list($mdb->dt[auth])."</td>
				<td class='list_box_td list_bg_gray' >".$mdb->dt[mail]."</td>
			    <td class='list_box_td ' >".$mdb->dt[pcs]."</td>
			    <td class='list_box_td list_bg_gray' >".$date."</td>
			    <td class='list_box_td ' >";
			    	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						$mstirng .= "<a href='./admin_manage.php?code=".$mdb->dt[code]."'><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 /></a> ";
					}else{
						$mstirng .= "<a href=\"javascript:alert('수정 권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0> </a>";
					}
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
						$mstirng .= "<a href=\"javascript:deleteUserInfo('$company_id','".$mdb->dt[code]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
					}else{
						$mstirng .= "<a href=\"javascript:alert('삭제 권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0> </a>";
					}
					$mstirng .= "
			   </td>
			</tr>";
		}
	}else{
		$mstirng .= "
			  <tr bgcolor=#ffffff height=50>
			    <td align=center colspan=6>등록된 관리자가 없습니다. </td>
			  </tr>
			  <tr height=1><td colspan=6 class='dot-x'></td></tr>	  ";
	}
	$mstirng .= "</table>";

	return $mstirng;

}

?>