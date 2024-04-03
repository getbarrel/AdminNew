<?
include("../class/layout.class");


if($admininfo[admin_level] == 8){
	$company_id = $admininfo[company_id];
}

if(!$company_id){
	echo("<script>alert('".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')."');history.back();</script>");
	//exit;
}

if($admininfo[mall_use_multishop]){
	$title = "입점사 관리자설정";
}else{
	$title = "거래처관리";
}

$cdb = new Database;
$db = new Database;

$cdb->query("SELECT ccd.com_name,csd.charge_code FROM common_company_detail ccd, common_seller_detail csd WHERE ccd.company_id=csd.company_id and ccd.company_id = '".$company_id."'");
$cdb->fetch();

$company_name = $cdb->dt[com_name];
$charge_code = $cdb->dt[charge_code];

$search_str = " and ccd.company_id = '".$company_id."' and cu.code ='".$code."'";

$sql = "select
          ccd.company_id,
          AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
          AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
          AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
          AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel,
          AES_DECRYPT(UNHEX(cmd.zip),'".$db->ase_encrypt_key."') as zip,
          AES_DECRYPT(UNHEX(cmd.addr1),'".$db->ase_encrypt_key."') as addr1,
          AES_DECRYPT(UNHEX(cmd.addr2),'".$db->ase_encrypt_key."') as addr2,
          AES_DECRYPT(UNHEX(cmd.mem_card),'".$db->ase_encrypt_key."') as mem_card,
          cu.id,
          cu.code,
          cu.auth,
          cu.language,
          cu.authorized,
          cmd.gp_ix,
          cu.mem_type,
          cmd.level_ix,
          cmd.level_msg,
          cmd.sex_div,
          cmd.birthday,
          cmd.birthday_div,
          cmd.sms,
          cmd.info,
          cu.date
        from
            common_user as cu 
            inner join common_member_detail as cmd on (cu.code = cmd.code)
            inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
        where
            1
            and cu.mem_div = 'S'
            and ccd.com_type = 'S' 
            $search_str
        ";


$db->query($sql);


if($db->total){
	$act = "user_update";
	$db->fetch();

    $birthday = explode("-",$db->dt[birthday]);
	$tel = explode("-",$db->dt[tel]);
	$pcs = explode("-",$db->dt[pcs]);
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
	    <td align='left' colspan=4 style='padding:3px 0px;'> <a name='user_add'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b> [".$company_name."] 관리자 ".($act == "user_update" ? "수정":"추가")."</b></div>")."</td>
	  </tr>
	  </table>";

$Contents03 .= "
		<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
		<colgroup>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
			<col width='15%' />
			<col width='35%' style='padding:0px 0px 0px 10px'/>
		</colgroup>";

$Contents03 .= "
			<tr bgcolor=#ffffff>
				<td class='input_box_title'>이름  <img src='".$required3_path."'></td>
				<td class='input_box_item'>
                <input type='text' class='textbox' id='name' name='name' validation='true' title='이름' value='".$db->dt[name]."' style='width:100px;'>
				</td>
				<td class='input_box_title'>대표 담당자 지정 </td>
				<td class='input_box_item'>
					<input type='checkbox' name='charge_check' id='charge_check' value='Y' ".CompareReturnValue($db->dt[code],$charge_code,"checked")." /> <label for='charge_check'>대표로 지정</label> (* 주문 완료 메일 및 SMS 수신)
				</td>
			</tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title'><b>아이디 <img src='".$required3_path."'></b></td>
				<td class='input_box_item'>
					<input type='text' class='textbox' id='user_id' name='id' validation='true' title='아이디'  value='".$db->dt[id]."'  style='width:100px;ime-mode:disabled;' ".($act == "user_insert" ? "" : "readonly").">
					<span id='idCheckText'></span>
				</td>
				<td class='input_box_title'><b>사용자 권한 <img src='".$required3_path."'></b></td>
				<td class='input_box_item'>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td style='padding-top:5px;'>".getAuthTemplet($db->dt[auth],8)."</td>
							<td colspan=2 style='padding-left:10px;;'>
							</td>
						</tr>
						<tr>
							<td style='padding-top:5px;'>
								<input type='hidden' name='mem_div' id='mem_div_S' value='S'>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr bgcolor=#ffffff>
            <td class='input_box_title'> 패스워드 ".($act=="user_insert" ? "<img src='".$required3_path."'>" : "")."</td>
            <td class='input_box_item' nowrap>
                <table cellpadding=0 cellspacing=0>
                    <tr>
                        <td><input type=password name='pw' value='' size=12 style='width:200' class='textbox' ".($act=="user_insert" ? "validation=true title='패스워드'" : "")." ></td>
                        ".($act == "user_update" ? "<td style='padding-left:10px;'> <input type=checkbox name=change_pass value=1></td><td> 비밀번호수정</td>":"")."
                    </tr>
                </table>
            </td>
            <td class='input_box_title'> 패스워드 확인 ".($act=="user_insert" ? "<img src='".$required3_path."'>" : "")."</td>
            <td class='input_box_item' nowrap><input type=password name='pw_confirm' value='' size=12 class='textbox'  style='width:200px' ".($act=="user_insert" ? "validation=true title='패스워드 확인'" : "")." ></td>
          </tr>
			<tr bgcolor=#ffffff >
				<td class='input_box_title'><b>사용자 언어 <img src='".$required3_path."'></b></td>
				<td class='input_box_item' >
				<table cellpadding=0 cellspacing=0>
					<tr>
						<td>".getLanguage($db->dt[language]," validation=true title='사용자 언어' ")."</td>
						<td style='padding-left:10px;;'>
						  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."
						</td>
					</tr>
				</table>
				</td>
				<td class='input_box_title'><b>사용자 승인</b></td>
				<td class='input_box_item'>
					<select name='authorized' id='authorized' style='width:100px;font-size:12px;'>
					<option value='N' ".CompareReturnValue("N",$db->dt[authorized],"selected").">승인대기</option>
					<option value='Y' ".($act == "user_insert" ? "selected" : CompareReturnValue("Y",$db->dt[authorized],"selected")).">승인</option>
					<option value='X' ".CompareReturnValue("X",$db->dt[authorized],"selected").">승인거부</option>
					 &nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'>입점업체 로그인은 관리자 승인후에만 가능합니다 </span>
				</td>
			</tr>";
if($act == "user_update"){
    $Contents03 .= "
			<tr>
				<td class='input_box_title' nowrap> 사용자그룹</td>
				<td class='input_box_item'>".makeGroupSelectBox($mdb,"gp_ix",$db->dt[gp_ix],"validation=true title='사용자 그룹'")."</td>
				<td class='input_box_title' nowrap> 회원구분</td>
				<td class='input_box_item'>
					<select name='mem_type'>
						<option value='C' ".($db->dt[mem_type] == "C" ? "selected":"").">기업회원</option>
					</select>
				</td>
			</tr>";
}else{
    $Contents03 .= "
			<tr>
			    <td class='input_box_title' nowrap> 사용자그룹 <img src='".$required3_path."'></td>
				<td class='input_box_item'>".makeGroupSelectBox($mdb,"gp_ix",$db->dt[gp_ix],"validation=true title='사용자 그룹'")."</td>
				<td class='input_box_title' nowrap> 회원구분 <img src='".$required3_path."'></td>
				<td class='input_box_item'>
					<select name='mem_type' title='회원구분'>
						<option value='C' ".($db->dt[mem_type] == "C" || $db->dt[mem_type] == "" ? "selected":"").">사업자회원</option>
					</select>
				</td>
			</tr>";
}

$Contents03 .= "
			<tr>
				<td class='input_box_title' nowrap>
					<label for='black_list'>회원레벨</label>
				</td>
				<td class='input_box_item' colspan='3'>
				".getMemberLevel($db->dt[level_ix],'false')."
					<input type='text' class='textbox' name='level_msg' id='level_msg' size='66' value='".$db->dt[level_msg]."' readonly>
				</td>
			</tr>
			<tr>
              <td class='input_box_title' nowrap> 성별</td>
				<td class='input_box_item'>
					<input type='radio' name='sex_div' id='sex_div_m' style='border:0px;' value='M' ".($db->dt[sex_div] == "M" || $db->dt[sex_div] == "" ? "checked":"")."><label for='sex_div_m'> 남성 </label>
					<input type='radio' name='sex_div' id='sex_div_w' style='border:0px;' value='W' ".($db->dt[sex_div] == "W" ? "checked":"")."><label for='sex_div_w'> 여성 </label>
					<input type='radio' name='sex_div' id='sex_div_d' style='border:0px;' value='D' ".($db->dt[sex_div] == "D" ? "checked":"")."><label for='sex_div_d'> 기타 </label>
				</td>
				<td class='input_box_title' nowrap> 생년월일</td>
				<td class='input_box_item' nowrap>
					<input type='text' class='textbox' name='birthday_yyyy' size='4' maxlength='4' value='".$birthday[0]."' style='width:30px' ".($act == "user_insert" ? "" : "readonly")."> -
					<input type='text' class='textbox' name='birthday_mm' size='2' maxlength='2' value='".$birthday[1]."' style='width:25px' ".($act == "user_insert" ? "" : "readonly")."> -
					<input type='text' class='textbox' name='birthday_dd' size='2' maxlength='2' value='".$birthday[2]."' style='width:25px' ".($act == "user_insert" ? "" : "readonly").">
					<input type='radio' name='birthday_div' id='birthday_div_1' style='border:0px;' value='1' ".($db->dt[birthday_div] == "1" || $db->dt[birthday_div] == "" ? "checked":"")."><label for='birthday_div_1'>양력</label>
					<input type='radio' name='birthday_div' id='birthday_div_0' style='border:0px;' value='0' ".($db->dt[birthday_div] == "0" ? "checked":"")."><label for='birthday_div_0'>음력</label>
				</td>
			</tr>
			<tr>
				<td class='input_box_title' nowrap> 휴대폰 <img src='".$required3_path."'></td>
				<td class='input_box_item'>
					<input type='text' class='textbox' name='pcs1' validation='true' title='휴대폰' size='3' maxlength='3' value='".$pcs[0]."' style='width:30px'> -
					<input type='text' class='textbox' name='pcs2' validation='true' title='휴대폰' size='4' maxlength='4' value='".$pcs[1]."' style='width:30px'> -
					<input type='text' class='textbox' name='pcs3' validation='true' title='휴대폰' size='4' maxlength='4' value='".$pcs[2]."' style='width:30px'>
				</td>
				<td class='input_box_title' nowrap> SMS 수신여부 <img src='".$required3_path."'></td>
				<td class='input_box_item'>
					<input type='radio' name='sms' value='1' id='sms_1' ".($db->dt[sms] == "1" || $db->dt[sms] == "" ? "checked":"")." style='border:0px;'><label for='sms_1'>수신함</label>
					<input type='radio' name='sms' value='0' id='sms_0' ".($db->dt[sms] == "0" ? "checked":"")." style='border:0px;'><label for='sms_0'>수신안함</label>
				</td>
			</tr>
			<tr>
				<td class='input_box_title' nowrap> 이메일 <img src='".$required3_path."'></td>
				<td class='input_box_item'>
				<input type='text' class='textbox' name='mail' id='mail' validation='true' title='이메일'  value='".$db->dt[mail]."' style='width:170px'>
				";
if($db->dt[is_id_auth] == 'N'){
    $Contents03 .="<input type='checkbox' name='mail_auth' id='mail_auth' value='Y' style='border:0px;'><label for='mail_auth'>강제인증</label>";
}else{
    $Contents03 .="<input type='hidden' name='mail_auth' value ='Y'>";
}
$Contents03 .= "</td>
				<td class='input_box_title' nowrap> 정보수신 <img src='".$required3_path."'></td>
				<td class='input_box_item'>
					<input type='radio' name='info' value='1' id='info_1' ".($db->dt[info] == "1" || $db->dt[info] == "" ? "checked":"")." style='border:0px;'><label for='info_1'>수신함</label>
					<input type='radio' name='info' value='0' id='info_0' ".($db->dt[info] == "0" ? "checked":"")."  ".$info_n." style='border:0px;'><label for='info_0'>수신안함</label>
				</td>
			</tr>
			<tr>
				<td class='input_box_title' nowrap> 우편번호</td>
				<td class='input_box_item' >
					<table border='0' cellpadding='0' cellspacing='0' >";
$Contents03 .= "<tr>
						<td style='border:0px;'>
							<input type='text' class='textbox' name='zip1' id='zipcode1' size='7' maxlength='7' value='".$db->dt[zip]."' style='width:60px' readonly>
						</td>
						<td style='border:0px;padding:0px 0 0 5px;'>
							<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('1');\" style='cursor:pointer;' align=absmiddle>
						</td>
					</tr>";
$Contents03 .= "</table>
				</td>
				<td class='input_box_title' nowrap> 집전화</td>
				<td class='input_box_item'>
					<input type='text' class='textbox' name='tel1' size='3' maxlength='3' value='".$tel[0]."' style='width:30px'> -
					<input type='text' class='textbox' name='tel2' size='4' maxlength='4' value='".$tel[1]."' style='width:30px'> -
					<input type='text' class='textbox' name='tel3' size='4' maxlength='4' value='".$tel[2]."' style='width:30px'>
				</td>
			</tr>
			<tr height=50>
				<td class='input_box_title'2 nowrap> 주소</td>
				<td bgcolor='#ffffff' colspan=3 style='padding:5px 0px 5px 5px;'>
				<input type='text' class='textbox' name='addr1' id='addr1' size='66' maxlength='80' value='".$db->dt[addr1]."' style='margin:2px 0px' readonly><br>
				<input type='text' class='textbox' name='addr2' id='addr2' size='66' maxlength='80' value='".$db->dt[addr2]."' style='margin:2px 0px'> 세부주소
				</td>
			</tr>
		</table><br>";


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
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' align=absmiddle > <a href='./company_user.php?company_id=".$_GET["company_id"]."'><img src='../images/".$admininfo["language"]."/btn_add_new.gif' align=absmiddle></a></td></tr>
</table>
";
}


$Contents = "<form name='edit_form' action='company.act.php' method='post' onsubmit='return CheckFormUserValue(this)' target='act'>
<input name='act' type='hidden' value='$act'>
<input name='company_id' type='hidden' value='".$company_id."'>
<input type=hidden name='code' value='".$code."' >
";
$Contents = $Contents."<table width='100%' border=0>";
$Contents = $Contents."<tr><td>";
$Contents .= $Contents03;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc03."</td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table ></form>";

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
function zipcode(type) {
    var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}
function CheckFormUserValue(frm){


	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	if(frm.act.value == 'user_insert' || (frm.act.value == 'user_update' && frm.change_pass.checked)){
		if(frm.pw.value.length < 1){
			alert(language_data['company_user.php']['B'][language]);//비밀번호가 입력되지 않았습니다.
			frm.pw.focus();
			return false;
		}

		if(frm.pw_confirm.value.length < 1){
				alert(language_data['company_user.php']['C'][language]);//비밀번호가 확인 정보가 입력되지 않았습니다.
			frm.pw_confirm.focus();
			return false;
		}

		if(frm.pw.value != frm.pw_confirm.value){
			alert(language_data['company_user.php']['D'][language]);//비밀번호가 정확하지 않습니다 확인후 다시 입력해주세요
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

	if(confirm(language_data['company_user.php']['A'][language])){//사용자 정보를 정말로 삭제하시겠습니까?
		window.frames['act'].location.href='company.act.php?act=user_delete&company_id='+company_id+'&code='+code
	}
}

</script>";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = seller_menu();
$P->strContents = $Contents;
$P->Navigation = "셀러관리 > 입점업체 관리 > 사용자 관리";
$P->title = "사용자 관리";
echo $P->PrintLayOut();



function get_company_user_list($company_id){
	global $admininfo, $auth_update_msg, $auth_delete_msg;
	//print_r($admininfo);
	$mstirng = "
		<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='list_table_box'>
		  <tr height=30 align=center>
		    <td style='width:100px;' class='s_td'> 대표담당자</td>
		    <td style='width:150px;' class='m_td'> 사용자명</td>
		    <td style='width:150px;' class='m_td'> 아이디</td>
		    <td style='width:150px;' class='m_td'> 이메일</td>
		    <td style='width:150px;' class='m_td'> 핸드폰</td>
		    <td style='width:150px;' class='m_td'> 등록일자</td>
		    <td style='width:150px;' class='e_td'> 사용관리</td>
		  </tr>";
	$mdb = new Database;

    if( $admininfo[admin_level] == 9 ) {
        $search_str = " and ccd.company_id = '" . $company_id . "' ";
    }else{
        $search_str = " and ccd.company_id = '" . $admininfo[company_id] . "' ";
    }

    $sql = "select
            ccd.*,
            AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
            AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
            AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
            cu.id,
            cu.code,
            cu.auth,
            csd.charge_code,
            cu.date,
            (case when csd.charge_code=cu.code then 'O' else 'X' end) as charge_str
        from
            common_user as cu 
            inner join common_member_detail as cmd on (cu.code = cmd.code)
            inner join common_company_detail as ccd on (cu.company_id = ccd.company_id)
            inner join common_seller_detail as csd on (ccd.company_id = csd.company_id)
        where
            1
            and cu.mem_div = 'S'
            and ccd.com_type = 'S' 
            $search_str
            order by cu.date desc
        ";
    $mdb->query($sql);

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
			    <td class='list_box_td'>".$mdb->dt[charge_str]."</td>
			    <td class='list_box_td list_bg_gray'>".$mdb->dt[name]."</td>
			    <td class='list_box_td'>".$mdb->dt[id]."</td>
				<td class='list_box_td list_bg_gray'>".$mdb->dt[mail]."</td>
			    <td class='list_box_td'>".$mdb->dt[pcs]."</td>
			    <td class='list_box_td list_bg_gray'>".$date."</td>
			    <td class='list_box_td'>";
			    	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
						$mstirng .= "<a href=\"javascript:updateUserInfo('$company_id','".$mdb->dt[code]."')\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0> </a>";
					}else{
						$mstirng .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0> </a>";
					}
					/*
					if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
						$mstirng .= "<a href=\"javascript:deleteUserInfo('$company_id','".$mdb->dt[code]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
					}else{
						$mstirng .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0> </a>";
					}
					*/
					$mstirng .= "
			    </td>
			  </tr>";
		}
	}else{
		$mstirng .= "
			  <tr bgcolor=#ffffff height=50>
			    <td align=center colspan=7>등록된 관리자가 없습니다. </td>
			  </tr>";
	}
	$mstirng .= "</table>";

	return $mstirng;

}
?>