<?
include("../class/layout.class");


if($admininfo[admin_level] == 8){
	$company_id = $admininfo[company_id];
}

if(!$company_id){
	echo("<script>alert('".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')."');history.back();</script>");
	exit;
}

if($admininfo[mall_use_multishop]){
	$title = "입점사 관리자설정";
}else{
	$title = "거래처관리";
}

$cdb = new Database;
$db = new Database;

$cdb->query("SELECT com_name FROM common_company_detail ccd WHERE  ccd.company_id = '".$company_id."'");
$cdb->fetch();

$company_name = $cdb->dt[com_name];
$sql = "SELECT cmd.code,birthday,birthday_div,AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
		AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
		AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
		info,sms,nick_name,job,cmd.date,cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*  
		FROM common_user cu , common_member_detail cmd, common_company_detail ccd WHERE  cu.code = cmd.code and cu.company_id = ccd.company_id and ccd.company_id = '".$company_id."' and cu.code ='".$code."' ";
$db->query($sql);


if($db->total){
	$act = "user_update";
	$db->fetch();
//print_r($db->dt);
	//$tel = explode("-",$db->dt[tel]);
	//$pcs = explode("-",$db->dt[pcs]);
	$code = $db->dt[code];

}else{
	$db->fetch();
	$act = "user_insert";

}



$Contents03 .= "<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	  <col width=20%>
	  <col width=20%>
	  <col width=40%>
	  <col width=20%>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:10px;'> ".GetTitleNavigation("입점사 관리자설정", "입점업체 관리 > 입점사 관리자설정 <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=".urlencode("몰스토리동영상메뉴얼_관리자설정(090322)_config.xml")."',800,517,'manual_view')\"  title='관리자설정 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle style='margin:0 0 2 0'></a>")."</td>
	  </tr>
	  
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b> [".$company_name."] 관리자 목록</b></div>")."</td>
	  </tr>
	  
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px'>
	    ".get_company_user_list($company_id)."
	    </td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> <a name='user_add'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b> [".$company_name."] 관리자 ".($db->dt[code] ? "수정":"추가")."</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=0 cellspacing=0 border='0' class='input_table_box'>
	  <col width=20%>
	  <col width=*>
	  <tr bgcolor=#ffffff height=30>
	    <td class='input_box_title'> <b>이름 <img src='".$required2_path."'></b></td>
	    <td class='input_box_item'><input type=text name='name' value='".$db->dt[name]."' class='textbox'  style='width:200px;' validation=true title='이름'></td>
	   </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용자 권한 <img src='".$required2_path."'></b></td>
	    <td class='input_box_item'>
			".getAuthTemplet($db->dt[auth], 8)." <div style='display:inline;padding-left:10px;'><span class='small'><!--* 사용자 권한에 맞는 권한 템플릿 선택--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span></div>
		</td>
	   </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용자 언어 <img src='".$required2_path."'></b></td>
	    <td class='input_box_item'>
			<table>
				<tr>
					<td>".getLanguage($db->dt[language])."</td>
					<td> <span class='small'><!--* 사용자 권한에 맞는 권한 템플릿 선택--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span></td>
				</tr>
			</table>
		</td>
	   </tr>";
if($admininfo[mall_use_multishop] == '1' && $admininfo[admin_level] == 9 && false){
$Contents03 .= "
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>부서/직급</b></td>
	    <td class='input_box_item'>
			<table>
				<tr>
					<td>
						".makeDepartmentSelectBox($cdb,"department",$db->dt[department],"select","부서")."
						".makePositionSelectBox($cdb,"position",$db->dt[position],"직급")."
					</td>
					<td> <span class='small'><!-- 업무관리를 사용할 사용자만 선택--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')."</span></td>
				</tr>
			</table>
		
		</td>
	   </tr>";



}
$Contents03 .= "
	  <tr bgcolor=#ffffff  >
	    <td class='input_box_title'> <b>아이디 <img src='".$required2_path."'></b>   </td>
	    <td class='input_box_item' >
			<input type=hidden name='b_id' value='".$db->dt[id]."' >
			<input type=text name='id' value='".$db->dt[id]."' class='textbox'  style='width:200px;ime-mode:disabled;' validation=true title='아이디'>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff  height=30>
	    <td class='input_box_title'> <b>이메일 <img src='".$required2_path."'></b></td>
	    <td class='input_box_item'><input type=text name='mail' value='".$db->dt[mail]."' class='textbox'  style='width:300px' title='이메일' validation='true' email='true'></td>
	  </tr>
	  <tr bgcolor=#ffffff  height=30>
	    <td class='input_box_title'> <b>담당자 핸드폰 <img src='".$required2_path."'></b></td>
	    <td class='input_box_item'>
			<input type=text name='pcs' value='".$db->dt[pcs]."'  style='width:200px;' class='textbox' validation='true' title='담당자 핸드폰' >
		</td>
	  </tr>
	  <tr bgcolor=#ffffff  height=30>
	    <td class='input_box_title'> 패스워드</td>
	    <td class='input_box_item' nowrap>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td><input type=password name='pw' value='' size=12 style='width:200' class='textbox' ></td>
					".($act == "user_update" ? "<td style='padding-left:10px;'> <input type=checkbox name=change_pass value=1></td><td> 비밀번호수정</td>":"")."
				</tr>
			</table>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff  height=30>
	    <td class='input_box_title'> 패스워드 확인 </td>
	    <td class='input_box_item' nowrap><input type=password name='pw_confirm' value='' size=12 class='textbox'  style='width:200px' ></td>
	  </tr>
	  <tr bgcolor=#ffffff height=30>
	    <td class='input_box_title'> 담당자전화</td>
	    <td class='input_box_item'>
			<input type=text name='tel' value='".$db->dt[tel]."'  style='width:200px;'  class='textbox' >
		</td>
	   </tr>
	  <tr bgcolor=#ffffff height=30>
	    <td class='input_box_title'> 사용자승인    </td>
	    <td class='input_box_item'>
	    <select name='authorized' style='width:100px;font-size:12px;'>
	    	<option value='N' ".CompareReturnValue("N",$db->dt[authorized],"selected").">승인대기</option>
	    	<option value='Y'  ".CompareReturnValue("Y",$db->dt[authorized],"selected").">승인</option>
	    	<option value='X' ".CompareReturnValue("X",$db->dt[authorized],"selected").">승인거부</option>
	    	 &nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'>입점업체 로그인은 관리자 승인후에만 가능합니다 </span>
	    </td>
	  </tr>
	  </table>";

/*
$ContentsDesc03 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td width='17'><img src='../image/emo_3_15.gif' align=absmiddle></td>
	<td width='*' align=left style='padding:10px;' class=small>
		 해당업체의 서브 관리자를 생성 하실 수 있습니다.
	</td>
</tr>
</table>
";*/
$ContentsDesc03 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');


if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle > <a href='./chainstore_user.php?company_id=".$_GET["company_id"]."'><img src='../images/".$admininfo["language"]."/btn_add_new.gif' align=absmiddle></a></td></tr>
</table>
";
}

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='edit_form' action='chainstore.act.php' method='post' onsubmit='return CheckFormUserValue(this)' target='act'>
<input name='act' type='hidden' value='$act'>
<input name='company_id' type='hidden' value='".$company_id."'>
<input type=hidden name='code' value='".$code."' >
";
//$Contents = $Contents."<tr ><img src='../funny/image/title_basicinfo.gif'><td></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents .= $Contents03;
//$Contents = $Contents.$ContentsDesc03;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc03."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr></form>";
$Contents = $Contents."</table >";

/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>사용자 추가</b>를 원하시면 사용자 정보를 입력하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>사용자 정보 수정</u> 수정을 원하시는 사용자 리스트의 수정버튼을 클릭하신후 해당 사용자의 장보를 수정 후 저장버튼을 클릭하시면 됩니다</td></tr>

</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'E');

//$help_text = HelpBox("게시판 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<b>입점사 관리자설정</b> <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_관리자설정(090322)_config.xml',800,517,'manual_view')\"  title='관리자설정 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' style='vertical-align:middle;'></a>", $help_text,155)."</div>";

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
	if(frm.act.value == 'user_insert' || (frm.act.value == 'user_update' && frm.change_pass.checked)){
		if(frm.pw.value.length < 1){
			alert(language_data['chainstore_user.php']['B'][language]);//비밀번호가 입력되지 않았습니다. 
			frm.pw.focus();
			return false;
		}

		if(frm.pw_confirm.value.length < 1){
				alert(language_data['chainstore_user.php']['C'][language]);//비밀번호가 확인 정보가 입력되지 않았습니다. 
			frm.pw_confirm.focus();
			return false;
		}

		if(frm.pw.value != frm.pw_confirm.value){
			alert(language_data['chainstore_user.php']['D'][language]);//비밀번호가 정확하지 않습니다 확인후 다시 입력해주세요
			return false;
		}
	}
	return true;
}

function updateUserInfo(company_id, code)
{
	document.location.href = '?company_id='+company_id+'&code='+code;
	//document.frames['act'].location.href='company.act.php?act=admin_log&company_id='+company_id+'&code='+code
}

function deleteUserInfo(company_id, code){

	if(confirm(language_data['chainstore_user.php']['A'][language])){//사용자 정보를 정말로 삭제하시겠습니까?
		window.frames['act'].location.href='company.act.php?act=user_delete&company_id='+company_id+'&code='+code
	}
}

</script>";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = chainstore_menu();
$P->strContents = $Contents;
$P->Navigation = "가맹점관리 > 회원관리 > 사용자 관리";
$P->title = "사용자 관리";
echo $P->PrintLayOut();


function get_company_user_list($company_id){
	global $admininfo, $auth_update_msg, $auth_delete_msg;
	//print_r($admininfo);
	$mstirng = "
		<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
		  <tr height=30 align=center>
		    <td style='width:150px;' class='s_td'> 사용자명</td>
		    <td style='width:150px;' class='m_td'> 아이디</td>
		    <td style='width:150px;' class='m_td'> 이메일</td>
		    <td style='width:150px;' class='m_td'> 핸드폰</td>
		    <td style='width:150px;' class='m_td'> 등록일자</td>
		    <td style='width:150px;' class='e_td'> 사용관리</td>
		  </tr>";
	$mdb = new Database;

	if($admininfo[admin_level] == 9){
			$sql = "SELECT cmd.code,birthday,birthday_div,AES_DECRYPT(UNHEX(name),'".$mdb->ase_encrypt_key."') as name,
					AES_DECRYPT(UNHEX(mail),'".$mdb->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(zip),'".$mdb->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(addr1),'".$mdb->ase_encrypt_key."') as addr1,
					AES_DECRYPT(UNHEX(addr2),'".$mdb->ase_encrypt_key."') as addr2,AES_DECRYPT(UNHEX(tel),'".$mdb->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(UNHEX(pcs),'".$mdb->ase_encrypt_key."') as pcs,
					info,sms,nick_name,job,cmd.date,cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*  FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd WHERE cu.code = cmd.code and cu.company_id = '".$company_id."' and cu.mem_type = 'CS' ";
			$mdb->query($sql);
	}else{
		if($admininfo[company_id] != ""){
			$sql = "SELECT cmd.code,birthday,birthday_div,AES_DECRYPT(UNHEX(name),'".$mdb->ase_encrypt_key."') as name,
					AES_DECRYPT(UNHEX(mail),'".$mdb->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(zip),'".$mdb->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(addr1),'".$mdb->ase_encrypt_key."') as addr1,
					AES_DECRYPT(UNHEX(addr2),'".$mdb->ase_encrypt_key."') as addr2,AES_DECRYPT(UNHEX(tel),'".$mdb->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(UNHEX(pcs),'".$mdb->ase_encrypt_key."') as pcs,
					info,sms,nick_name,job,cmd.date,cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*  FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd WHERE cu.code = cmd.code and cu.company_id = '".$admininfo[company_id]."' and cu.mem_type = 'CS' ";
			//echo $sql;
			$mdb->query($sql);
		}
	}


	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);
		$mstirng .= "
			  <tr bgcolor=#ffffff height=30 align=center>
			    <td class='list_box_td list_bg_gray'>".$mdb->dt[name]."</td>
			    <td class='list_box_td'>".$mdb->dt[id]."</td>
				<td class='list_box_td list_bg_gray'>".$mdb->dt[mail]."</td>
			    <td class='list_box_td'>".$mdb->dt[pcs]."</td>
			    <td class='list_box_td list_bg_gray'>".$mdb->dt[date]."</td>
			    <td class='list_box_td'>";
			    	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						$mstirng .= "<a href=\"javascript:updateUserInfo('$company_id','".$mdb->dt[code]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0> </a>";
					}else{
						$mstirng .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0> </a>";
					}
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
						$mstirng .= "<a href=\"javascript:deleteUserInfo('$company_id','".$mdb->dt[code]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
					}else{
						$mstirng .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0> </a>";
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