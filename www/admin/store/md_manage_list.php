<?
include("../class/layout.class");
include_once("md.lib.php");

//print_r($admininfo);

if(!$admininfo[company_id]){
	echo("<script>alert('업체가 선택되지 않았습니다 확인후 다시 시도해주세요');history.back();</script>");
	exit;
}

$cdb = new Database;
$db = new Database;

$sql = "SELECT 
			*
		FROM 
			common_user as cu
			inner join common_member_detail as cmd on (cu.code = cmd.code)
			inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
			left join common_branch as cb on (cmd.branch = cb.cb_ix)
		WHERE ccd.company_id = '".$admininfo["company_id"]."'";
$cdb->query($sql);
$cdb->fetch();
$company_name = $cdb->dt[com_name];

if($code){
	$where = " and cu.code = '".$code."' ";
}
if($cdb->dbms_type == "oracle"){
	$sql = "SELECT cu.id,cu.mem_div, cu.mem_type, cmd.code,cmd.mem_level, AES_DECRYPT(name,'".$db->ase_encrypt_key."') as name,
			AES_DECRYPT(mail,'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(tel,'".$db->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(pcs,'".$db->ase_encrypt_key."') as pcs ,
			cb_ix, rg_ix, branch, team, language, cu.auth, cu.authorized
			FROM
				common_user as cu
				inner join common_member_detail as cmd on (cu.code = cmd.code)
				inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
				left join common_branch as cb on (cmd.branch = cb.cb_ix)
			WHERE 
				ccd.company_id = '".$admininfo["company_id"]."'
				$where ";
}else{
	$sql = "SELECT cu.id,cu.mem_div, cu.mem_type, cmd.code,cmd.mem_level, birthday,birthday_div,AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
			AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
			AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs ,
			cb_ix, rg_ix, branch, team, language, cu.auth, cu.authorized
			FROM
				common_user as cu
				inner join common_member_detail as cmd on (cu.code = cmd.code)
				inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
				left join common_branch as cb on (cmd.branch = cb.cb_ix)
			WHERE
				ccd.company_id = '".$admininfo["company_id"]."' 
				$where ";
}

$db->query($sql);

if($db->total){
	$act = "user_update";
	$db->fetch();
//print_r($db->dt);
	$tel = $db->dt[tel];
	$pcs = $db->dt[pcs];
	$cb_ix = $db->dt[cb_ix];
	$rg_ix = $db->dt[rg_ix];
	$branch = $db->dt[branch];
	$team = $db->dt[team];
	$code = $db->dt[code];
	//$mem_level = $db->dt[mem_level];

	$cdb->query("SELECT depth, parent_rg_ix FROM common_region WHERE rg_ix  = '".$db->dt[rg_ix]."' ");
	$cdb->fetch();
	if($cdb->dt[depth] == 1){
		$sql = "SELECT parent_rg_ix FROM common_region WHERE rg_ix  = '".$cdb->dt[parent_rg_ix]."' ";
		//echo $sql;
		$cdb->query($sql);
		$cdb->fetch(0);
		$parent_rg_ix = $cdb->dt[rg_ix];
		//echo "parent_rg_ix : ".$parent_rg_ix;
	}else{
		$parent_rg_ix = $cdb->dt[parent_rg_ix];
	}

}else{
	$act = "user_insert";
}

$Contents03 .= "<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	  <col width=25%>
	  <col width=*>
	  <col width=10%>
	  <col width=20%>
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("MD 수정/등록", "상점관리 > MD 수정/등록 ")."</td>
	  </tr>
	   <tr>
			<td align='left' colspan=4 style='padding-bottom:20px;'>
				".md_tab("list")."
			</td>
		</tr>
	  <tr>
	    <td align='left' colspan=4 style='pdding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b> [".$company_name."] MD 목록</b></div>")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px'>
	    ".get_company_user_list($admininfo[company_id])."
	    </td>
	  </tr>
	  </table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0'>
		<tr>
			<td align=right>
				<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle valign='top' >
				<a href='?company_id=".$_GET["company_id"]."'><img src='../images/".$admininfo["language"]."/btn_add_new.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle valign='top'></a>
			</td>
		</tr>
	</table>
";
}

$Contents = "<form name='md_frm' action='md_manage.act.php' method='post' onsubmit='return CheckFormUserValue(this)' target='act'>
<input name='act' type='hidden' value='$act'>
<input name='company_id' type='hidden' value='".$admininfo[company_id]."'>
<input type=hidden name='code' value='".$code."' >";
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
$Contents = $Contents."</table></form>";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>사용자 추가</b>를 원하시면 사용자 정보를 입력하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>사용자 정보 수정</u> 수정을 원하시는 사용자 리스트의 수정버튼을 클릭하신후 해당 사용자의 장보를 수정 후 저장버튼을 클릭하시면 됩니다</td></tr>

</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

//$help_text = HelpBox("게시판 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<b>MD 설정</b> ", $help_text,105)."</div>";

$Contents = $Contents.$help_text;

$Script = "
<script language='javascript'>
function loadRegion(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = 'region.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadBranch(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = 'branch.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}

function loadTeam(sel,target) {

	var trigger = sel.options[sel.selectedIndex].value;
	//alert(sel.form.name);
	var form = sel.form.name;

	var depth = sel.getAttribute('depth');

	window.frames['act'].location.href = 'team.load.php?form=' + form + '&trigger=' + trigger + '&target=' + target +'&depth=2';

}


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
	document.location.href = '?company_id='+company_id+'&code='+code+'&#user_add'
	//document.frames['act'].location.href='company.act.php?act=admin_log&company_id='+company_id+'&code='+code+'&#user_add';
}

function deleteUserInfo(company_id, code){

	if(confirm('사용자 정보를 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href='company.act.php?act=user_delete&company_id='+company_id+'&code='+code
	}
}

</script>";
if($mmode == "pop"){
	$Script = "<script language='JavaScript' src='origin.js'></script>";
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->Navigation = "상점관리 > MD 관리 > MD 등록/수정";
	$P->NaviTitle = "MD목록";
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "상점관리 > MD 관리 > MD 등록/수정";
$P->title = "MD 등록/수정";
echo $P->PrintLayOut();
}

function get_company_user_list($company_id){
	global $admininfo;

	$mstirng = "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box' style='margin-top:3px;'>
			<col style='width:150px;'>
			<col style='width:150px;'>
			<col style='width:150px;'>
			<col style='width:70px;'>
			<col style='width:150px;'>
		    <col style='width:150px;'>
		    <col style='width:150px;'>
		    <col style='width:150px;'>
		    <col style='width:150px;'>
		  <tr height=30 bgcolor=#efefef align=center>
		    <td class='s_td'> 지역</td>
			<td class='m_td'> 지사</td>
			<td class='s_td'> 팀</td>
			<td class='m_td'> 역할</td>
			<td class='m_td'> 사용자명</td>
		    <td class='m_td'> 아이디</td>
		    <td class='m_td'> 이메일</td>
		    <td class='m_td'> 핸드폰</td>
		    <td class='e_td'> 사용관리</td>
		  </tr>";
	$mdb = new Database;
//cmd.code,birthday,birthday_div,AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
//AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
//AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs
	if($admininfo[admin_level] == 9){
		if($mdb->dbms_type == "oracle"){
			$sql = "SELECT 
						AES_DECRYPT(cmd.name) as name, cmd.mem_level, cu.id, AES_DECRYPT(cmd.mail) as mail, AES_DECRYPT(cmd.pcs) as pcs, cmd.date_, cu.code , cb.rg_ix , cb.branch_name, '' as team_name 
					FROM 
						".TBL_COMMON_USER." as cu
						inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
						left join common_branch as cb on (cmd.branch = cb.cb_ix)
					WHERE 
						cu.company_id = '".$admininfo[company_id]."'
						and cu.mem_div = 'MD' 
						
						union
					SELECT
						AES_DECRYPT(cmd.name) as name, cmd.mem_level, cu.id, AES_DECRYPT(cmd.mail) as mail, AES_DECRYPT(cmd.pcs) as pcs, cmd.date_, cu.code , cb.rg_ix , cb.branch_name, ct.team_name 
					FROM 
						".TBL_COMMON_USER." as cu 
						inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
						left join common_branch as cb on (cmd.branch = cb.cb_ix) 
						left join common_team as ct on (cmd.team = ct.ct_ix)
					WHERE
						cu.company_id = '".$admininfo[company_id]."'
						and cu.mem_div = 'MD' 
						
						";
		}else{
			$sql = "SELECT 
						AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cmd.mem_level, cu.id, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
						AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs, cmd.date, cu.code , cb.rg_ix , cb.branch_name, '' as team_name 
					FROM 
						".TBL_COMMON_USER." cu
						inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
						left join common_branch as cb on (cmd.branch = cb.cb_ix)
					WHERE 
						 cu.company_id = '".$admininfo[company_id]."'
						and cu.mem_type = 'MD' 
					
					union
						SELECT 
							AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, cmd.mem_level,
							cu.id, AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
							AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
							cmd.date, cu.code , cb.rg_ix , cb.branch_name, ct.team_name 
					FROM 
						".TBL_COMMON_USER." as cu
						inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
						left join common_branch as cb on (cmd.branch = cb.cb_ix) 
						left join common_team as ct on (cmd.team = ct.ct_ix)
					WHERE 
						cu.company_id = '".$admininfo[company_id]."'
						and cu.mem_div = 'MD' 
						
						";
		}

		$mdb->query($sql);
	}else{
		if(!$company_id){
			$sql = "SELECT
						* 
					FROM 
						".TBL_COMMON_USER." as cu
						inner join ".TBL_COMMON_MEMBER_DETAIL." as cmd on (cu.code = cmd.code)
						left join common_branch as cb on (cmd.branch = cb.cb_ix) 
						left join common_team as ct on (cmd.team = ct.ct_ix)
					WHERE
						cu.company_id = '".$admininfo[company_id]."'
						and cu.mem_div = 'MD'
						 ";

			$mdb->query($sql);

		}
	}

	$md_users = $mdb->fetchall();

	if(count($md_users) > 0){
		for($i=0;$i < count($md_users);$i++){
		$sql = "SELECT region_name, depth, parent_rg_ix FROM common_region WHERE rg_ix  = '".$md_users[$i][rg_ix]."' ";

		$mdb->query($sql);
		$mdb->fetch();
		$region_name = $mdb->dt[region_name];
		if($mdb->dt[depth] == 2){
			$sql = "SELECT region_name FROM common_region WHERE rg_ix  = '".$mdb->dt[parent_rg_ix]."' ";
			$mdb->query($sql);
			//echo $sql;
			$mdb->fetch(0);
			$region_name = $mdb->dt[region_name]." > ".$region_name;

		}

		if($md_users[$i][mem_level] == "11"){
			$mem_level_str = "지사장";
		}else if($md_users[$i][mem_level] == "12"){
			$mem_level_str = "MD팀장";
		}else if($md_users[$i][mem_level] == "13"){
			$mem_level_str = "MD";
		}else{
			$mem_level_str = "-";
		}
		$mstirng .= "
			  <tr bgcolor=#ffffff height=30 align=center>
			    <td class='list_box_td list_bg_gray' nowrap>".$region_name."</td>
				<td class='list_box_td' nowrap>".$md_users[$i][branch_name]."</td>
				<td class='list_box_td list_bg_gray' nowrap>".$md_users[$i][team_name]."</td>
				<td class='list_box_td' nowrap>".$mem_level_str."</td>
				<td class='list_box_td point' nowrap>".$md_users[$i][name]."</td>
			    <td class='list_box_td'>".$md_users[$i][id]."</td>
				<td class='list_box_td list_bg_gray'>".$md_users[$i][mail]."</td>
			    <td class='list_box_td' nowrap>".$md_users[$i][pcs]."</td>
			    <td class='list_box_td list_bg_gray' nowrap>";
			    	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){//admin_manage.php?code=f51d9fb92e7f6ca818c7db7b192991d9
						//$mstirng .= "<a href=\"javascript:updateUserInfo('$company_id','".$md_users[$i][code]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0> </a>";
						$mstirng .= "<a href='./md_manage.php?code=".$md_users[$i][code]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0> </a>";
					}else{
						$mstirng .= "<a href=\"javascript:alert('수정 권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0> </a>";
					}
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
						$mstirng .= "<a href=\"javascript:deleteUserInfo('$company_id','".$md_users[$i][code]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
					}else{
						$mstirng .= "<a href=\"javascript:alert('삭제 권한이 없습니다.');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0> </a>";
					}
					$mstirng .= "
			    </td>
			  </tr> ";
		}
	}else{
		$mstirng .= "
			  <tr bgcolor=#ffffff height=50>
			    <td align=center colspan=10>등록된 MD가 없습니다. </td>
			  </tr>
			  ";
	}
	$mstirng .= "</table>";

	return $mstirng;

}

?>