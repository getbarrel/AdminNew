<?
include("../class/layout.work.class");
include("work.lib.php");
if(!$admininfo[company_id]){
	echo("<script>alert('업체가 선택되지 않았습니다 확인후 다시 시도해주세요');history.back();</script>");
	exit;
}
//print_r($admininfo);

$cdb = new Database;
$db = new Database;

$cdb->query("SELECT com_name FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE company_id = '".$admininfo[company_id]."' ");
$cdb->fetch();

$com_name = $cdb->dt[com_name];

if($admininfo[master] != "Y"){
	$sql = "SELECT cmd.code,AES_DECRYPT(UNHEX(jumin),'".$db->ase_encrypt_key."') as jumin,birthday,birthday_div,sex_div,AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
		AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
		AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
		info,sms,nick_name,job,cmd.date,cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*
		FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd 
		WHERE cu.code = cmd.code and cu.company_id = '".$admininfo[company_id]."' and cu.code ='".$admininfo[charger_ix]."'   ";
	//echo $sql;
	$db->query($sql);
}else if($admininfo[master] == "Y" && $charger_ix){
	$sql = "SELECT cmd.code,AES_DECRYPT(UNHEX(jumin),'".$db->ase_encrypt_key."') as jumin,birthday,birthday_div,sex_div,AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
		AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail, AES_DECRYPT(UNHEX(zip),'".$db->ase_encrypt_key."') as zip,AES_DECRYPT(UNHEX(addr1),'".$db->ase_encrypt_key."') as addr1,
		AES_DECRYPT(UNHEX(addr2),'".$db->ase_encrypt_key."') as addr2,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,tel_div,AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,
		info,sms,nick_name,job,cmd.date,cmd.file,recent_order_date,recom_id,gp_ix,sex_div,mem_level,branch,team,department,position,add_etc1,add_etc2,add_etc3,add_etc4,add_etc5,add_etc6, cu.*
		FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd 
		WHERE cu.code = cmd.code and cu.company_id = '".$admininfo[company_id]."' and cu.code ='$charger_ix'  ";
	//echo $sql;
	$db->query($sql);
}
//echo nl2br($sql);

if($db->total){
	$act = "user_update";
	$db->fetch();
	$charger_ix = $db->dt[code];
	$tel = explode("-",$db->dt[tel]);
	$pcs = explode("-",$db->dt[pcs]);


	//print_r($pcs);
	$tmp_permit_text = $db->dt[permit];
	//echo $tmp_permit;

	$sql = "SELECT * FROM work_userinfo WHERE charger_ix = '".$charger_ix."'  ";
	//echo $sql;
	$cdb->query($sql);

	if($cdb->total){
		$cdb->fetch();

		$master = $cdb->dt[master];
		$google_mail = $cdb->dt[google_mail];
		$google_pass = $cdb->dt[google_pass];
		$google_sync_yn = $cdb->dt[google_sync_yn];
		$google_sync_time = $cdb->dt[google_sync_time];
	}


}else{
	$act = "user_insert";

	$tmp_permit_text = "";//01-00:01-11:01-12:03-00:03-02:03-04:04-00:04-01:04-11:04-12:";
}



$Contents03 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	  <col width=25%>
	  <col width=20%>
	  <col width=30%>
	  <col width=30%>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:10px;'> ".GetTitleNavigation("사용자 설정", "상점관리 > 사용자 설정 ")."</td>
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
						<td class='box_02'  ><a href='user.php'>사용자목록</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='department.php'>부서관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02' ><a href='position.php'>직급관리</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='width:450px;text-align:right;vertical-align:bottom;padding:0 0 10 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>

	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20'>
	    ".get_company_user_list($admininfo[company_id])."
	    </td>
	  </tr>


	  </table>";
//print_r($admininfo);

$Contents = "<table width='100%' border=0>";
$Contents = $Contents."

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
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table >
<div id='user_edit_box' style='display:none;vertical-align:top;text-align: center;backgorund-color:#ffffff'>

</div>
<div id='user_input_box' style='display:none;vertical-align:top;text-align: center;backgorund-color:#ffffff'>";

$innerview = "
<table class='layer_box' border=0 cellpadding=0 cellspacing=0 style='width:790px;height:0px;' >
	<col width='11px'>
	<col width='*'>
	<col width='11px'>
	<tr>
		<th class='box_01'></th>
		<td class='box_02' ></td>
		<th class='box_03'></th>
	</tr>

	<tr>
		<th class='box_04' style='vertical-align:top'></th>
		<td class='box_05' rowspan=2 valign=top style='padding:5px 15px 5px 5px;font-weight:bold;font-size:12px;line-height:150%;text-align:left;' >";
$inner_inner_view = "
			<h1 id=\"check_title\" onclick='CheckFormUserValue(document.edit_form)'>".GetTitleNavigation("사용자 설정", "업무관리 관리 > 사용자 설정 ", false)."</h1>
			<form name='edit_form' action='user.act.php' method='post' enctype='multipart/form-data'  onsubmit='return CheckFormUserValue(this)' target='act'><!--target='act'-->
			<input name='act' type='hidden' value='$act'>
			<input name='company_id' type='hidden' value='".$admininfo[company_id]."'>
			<input type=hidden name='charger_level' value='9' >
			<input type=hidden name='charger_ix' value='".$charger_ix."' >
			<table width='100%' cellpadding=4 cellspacing=0 border='0' class='input_table_box'>
			  <col width='15%'>
			  <col width='35%'>
			  <col width='15%'>
			  <col width='35%'>
			  <tr bgcolor=#ffffff >
				<td class='input_box_title'> <b>이름 <img src='".$required3_path."'></b></td>
				<td class='input_box_item' colspan=2>
					<table cellpadding=0 cellspacing=0>
						<tr>
							<td><input type=text name='name' value='".$db->dt[name]."' class='textbox'  style='width:120px' validation=true title='이름'></td>
							<td>".($admininfo[master] == "Y" ? "<input type=checkbox name=master value=Y ".($master == "Y" ? "checked":"")."> 마스터 관리자":"")." ".$db->dt[master]."</td></td>
							<td style='padding-left:10px;'>
								<input type=radio name=sex_div id='sex_m' value=M ".($db->dt[sex_div] == "M" ? "checked":"")." ><label for='sex_m'>남성</label>
								<input type=radio name=sex_div id='sex_w' value=W  ".($db->dt[sex_div] == "W" ? "checked":"")."  ><label for='sex_w'>여성</label>
							</td>
						</tr>
					</table>
				</td>
				<td rowspan=5  align=center><div style='border:1px solid silver;padding:10px;margin:4px;'>";

				//echo $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/profile/profile_".$charger_ix.".jpg";
				if(file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/profile/profile_".$charger_ix.".jpg")){
					$inner_inner_view .= "<img src='".$admin_config[mall_data_root]."/work/profile/profile_".$charger_ix.".jpg' width=90 height=90>";
				}else{
					$inner_inner_view .= "<img src='../images/".($db->dt[sex_div] == "M" ? "man.jpg":"women.jpg")."' width=90 height=90>";
				}
$inner_inner_view .= "
				</div></td>
			   </tr>
			  <tr bgcolor=#ffffff>
				<td class='input_box_title' nowrap> 프로파일 이미지  : </td>
				<td class='input_box_item'  style='padding:5px;' colspan=2>
					<table>
					<tr>
					<td><input type='file' class='textbox' name='profile_img' validation='false' title='프로파일 이미지' align=absmiddle></td>
					</tr>
					</table>
					<div class='small' style='text-align:left;'>* 프로파일 사진은 100*100 으로 등록해주세요.</div>
				</td>
			  </tr>
			 ";
		if($admininfo[mall_use_multishop] == '1' && $admininfo[admin_level] == 9 || true){
		$inner_inner_view .= "
			  <tr bgcolor=#ffffff >
				<td class='input_box_title'> <b>부서/직급</b></td>
				<td class='input_box_item' colspan=2>
				".makeDepartmentSelectBox($cdb,"department",$db->dt[department],"select","부서")."
				".makePositionSelectBox($cdb,"position",$db->dt[position],"직급")."
				</td>
			   </tr>";



		}
		$inner_inner_view .= "
			  <tr bgcolor=#ffffff  >
				<td class='input_box_title'> <b>아이디 <img src='".$required3_path."'></b>   </td>
				<td class='input_box_item' colspan=2>
				<input type=hidden name='b_id' value='".$db->dt[id]."' >
				<input type=text name='id' value='".$db->dt[id]."' class='textbox'  style='width:180px;ime-mode:disabled;' validation=true title='아이디'></td>
			  </tr>
			  <tr bgcolor=#ffffff  >
				<td class='input_box_title'> <b>이메일 <img src='".$required3_path."'></b></td>
				<td class='input_box_item' colspan=2><input type=text name='mail' value='".$db->dt[mail]."' class='textbox'  style='width:300px;' title='이메일' validation='true' email='true'></td>
			  </tr>
			  <tr bgcolor=#ffffff  >
				<td class='input_box_title'> <b>핸드폰 <img src='".$required3_path."'></b></td>
				<td class='input_box_item' colspan=3>
					<input type=text name='pcs1' value='".$pcs[0]."' maxlength=3 size=3 class='textbox' validation='true' title='핸드폰' numeric='true'> -
					<input type=text name='pcs2' value='".$pcs[1]."' maxlength=4 size=5 class='textbox' validation='true' title='핸드폰' numeric='true'> -
					<input type=text name='pcs3' value='".$pcs[2]."' maxlength=4 size=5 class='textbox' validation='true' title='핸드폰' numeric='true'>
					</td>
			  </tr>
			  <tr bgcolor=#ffffff  >
				<td class='input_box_title'> 패스워드</td>
				<td class='input_box_item' style='padding:5px;' align=left nowrap>
					<table cellpadding=0 cellspacing=0>
					<tr>
						<td><input type=password name='pw' value='' size=12 style='width:150px' class='textbox' ></td>
						<td>".($act == "user_update" ? "<input type=checkbox name=change_pass id=change_pass value=1><label for='change_pass'>비밀번호수정</label>":"")."</td>
					</tr>
					<tr>
						<td colspan=2><div style='display:block;padding:5px;' class=small>* 비밀번호의 첫문자는 영문으로 시작해야 합니다.</div> </td>
					</tr>
					</table>
				</td>
				<td class='input_box_title'> 패스워드 확인 </td>
				<td class='input_box_item' nowrap>
					<input type=password name='pw_confirm' value='' size=12 class='textbox'  style='width:150px' >

				</td>
			  </tr>
			  <tr bgcolor=#ffffff  >
				<td class='input_box_title'> 구글계정 </td>
				<td class='input_box_item' style='padding:5px;'>
				<input type=text name='google_mail' value='".$google_mail."' class='textbox'  style='width:180px' title='구글 이메일' validation='false' email='true'><br>
				<div style='display:block;padding:5px;' class=small>* 구글 계정을 입력하시면 <b>구글 캘린더</b>와 일정이 연동됩니다.</div>
				</td>
				<td class='input_box_title'> 구글 동기화 주기    </td>
				<td class='input_box_item' >
				<select name='google_sync_time' style='width:100px;font-size:12px;'>
					<option value='' ".CompareReturnValue("",$google_sync_time,"selected").">동기화주기</option>
					<option value='5' ".CompareReturnValue("5",$google_sync_time,"selected").">5</option>
					<option value='10'  ".CompareReturnValue("10",$google_sync_time,"selected").">10</option>
					<option value='20' ".CompareReturnValue("20",$google_sync_time,"selected").">20</option>
					 &nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' id='google_sync_yn' name='google_sync_yn' value='1' ".($google_sync_yn == "1" ? "checked":"")."><label for='google_sync_yn'>동기화여부</label>
				</td>
			  </tr>
			  <tr bgcolor=#ffffff  >
				<td class='input_box_title'> 구글 패스워드</td>
				<td class='input_box_item' style='padding:5px;' nowrap>
					<input type=password name='google_pass' value='$google_pass' size=12 style='width:180px' class='textbox' ><br> 
					<div style='display:block;padding:5px;margin:3px 0px;' class=small>* 비밀번호의 첫문자는 영문으로 시작해야 합니다.</div> 
				</td>
			  <!--/tr>
			  <tr bgcolor=#ffffff  -->
				<td class='input_box_title' nowrap> 구글 패스워드 확인 </td>
				<td class='input_box_item' nowrap>
					<input type=password name='google_pass_confirm' value='$google_pass' size=12 class='textbox'  style='width:180px' >
				</td>
			  </tr>
			  <tr bgcolor=#ffffff >
				<td class='input_box_title' > 사용자승인    </td>
				<td class='input_box_item' colspan=3 >
				<select name='authorized' style='width:100px;font-size:12px;'>
					<option value='N' ".CompareReturnValue("N",$db->dt[authorized],"selected").">승인대기</option>
					<option value='Y'  ".CompareReturnValue("Y",$db->dt[authorized],"selected").">승인</option>
					<option value='X' ".CompareReturnValue("X",$db->dt[authorized],"selected").">승인거부</option>
					 &nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'>입점업체 로그인은 사용자 승인후에만 가능합니다 </span>
				</td>
			  </tr>
			  </table>
			  <table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
				<tr bgcolor=#ffffff >
					<td colspan=3 align=center style='padding:20px 0px 20px 0px'>
					<input type='image' src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' align=absmiddle>
					<a id=\"btnCheck_cancel\" href=\"javascript:LayerClose()\"><img src='../image/b_cancel.gif' border=0 align=absmiddle></a>
					</td>

				</tr>
			</table>
			  </form>";
$innerview .= $inner_inner_view."
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>

";

$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>사용자 추가</b>를 원하시면 사용자 정보를 입력하신후 저장버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><u>사용자 정보 수정</u> 수정을 원하시는 사용자 리스트의 수정버튼을 클릭하신후 해당 사용자의 장보를 수정 후 저장버튼을 클릭하시면 됩니다</td></tr>

</table>
";


//$help_text = HelpBox("게시판 관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:0px;'><table><tr><td valign=middle><b>사용자 설정</b></td><td></td></tr></table></div>", $help_text,150)."</div>";

$Contents = $Contents.$innerview."</div>".$help_text;

$Script = "
<script language='javascript' src='work.js'></script>
<script language='javascript'>
function CheckFormUserValue(frm){


	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	if(frm.act.value == 'user_insert' || (frm.act.value == 'user_update' && frm.change_pass.checked)){
		if(frm.charger_pass.value.length < 1){
			alert('비밀번호가 입력되지 않았습니다. ');
			frm.charger_pass.focus();
			return false;
		}

		if(frm.charger_pass_confirm.value.length < 1){
				alert('비밀번호가 확인 정보가 입력되지 않았습니다. ');
			frm.charger_pass_confirm.focus();
			return false;
		}

		if(frm.charger_pass.value != frm.charger_pass_confirm.value){
			alert('비밀번호가 정확하지 않습니다 확인후 다시 입력해주세요');
			return false;
		}
	}
	return true;
}

function updateUserInfo(company_id, charger_ix)
{
	//document.location.href = '?company_id='+company_id+'&charger_id='+charger_id;
	//document.frames['act'].location.href='user.act.php?act=admin_log&company_id='+company_id+'&charger_id='+charger_id
	$.ajax({
		type: 'GET',
		data: {'mmode': 'layer','company_id': company_id,'charger_ix': charger_ix, 'rand':Math.random()},
		url: './user.php',
		dataType: 'html',
		async: true,
		cache:false,
		beforeSend: function(){
			//$('#loading').show();
			$.blockUI.defaults.css = {top:'10px' };
			//$.blockUI({ message: $('#loading'), css: {backgroundColor:'transparent',  top:'10px' ,width: '100px' , height: '100px' ,padding:  '10px'} });

		},
		success: function(datas){
				//alert(document.cookie);
				//alert(datas);
				$('#user_edit_box').html(\"<div style='display:none;'>\"+Math.random()+\"</div>\"+datas);

				//$('#loading').hide();


		}
	});

	LayerShow('user_edit_box');
}

function deleteUserInfo(company_id, charger_ix){

	if(confirm('입점업체 사용자 정보를 정말로 삭제하시겠습니까?')){
		window.frames['act'].location.href='user.act.php?act=user_delete&company_id='+company_id+'&charger_ix='+charger_ix
	}
}

</script>";

if($mmode == "layer"){
	echo $innerview;
}else{
	if($admininfo[master] == "Y" || true){
		$P = new LayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = work_menu();
		$P->strContents = $Contents;

		$P->Navigation = "업무관리 > 사용자 관리";
		$P->title = "사용자 관리";

		$P->footer_menu = footMenu()."".footAddContents();
		echo $P->PrintLayOut();
	}else{

		$P = new LayOut();
		$P->addScript = $Script;
		$P->strLeftMenu = work_menu();
		$P->strContents = $inner_inner_view;

		$P->Navigation = "업무관리 > 사용자 관리";
		$P->title = "사용자 관리";

		$P->footer_menu = footMenu()."".footAddContents();
		echo $P->PrintLayOut();
	}
}

function get_company_user_list($company_id){
	global $admininfo, $admin_config;
//echo $company_id;
	$mstirng = "
		<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
		  <tr height=30 bgcolor=#efefef align=center style='font-weight:bold'>
		    <td style='width:40px;' class='s_td'> 번호</td>
			<td style='width:180px;' class='m_td'> 이름</td>
		    <td style='width:100px;' class='m_td'> 직책</td>
		    <td style='width:100px;' class='m_td'> 아이디</td>
		    <td style='width:120px;' class='m_td'> 핸드폰</td>
			<td style='width:120px;' class='m_td'> 이메일</td>
		    <td style='width:150px;' class='m_td'> 등록일자</td>
		    <td style='width:130px;' class='e_td'> 관리</td>
		  </tr>";
	$mdb = new Database;

	if($admininfo[admin_level] == 9){
			$sql = "SELECT cui.*, ui.master , AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail,
					AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel , cmd.sex_div ,
					scp.ps_name
					FROM ".TBL_COMMON_USER." cui
					right join ".TBL_COMMON_MEMBER_DETAIL." cmd on cui.code = cmd.code
					right join service_ing si on si.mem_ix = cmd.code
					and service_div = 'APP' and solution_div = 'WORK' and si_status = 'SI'
					left join work_userinfo ui on cui.code = ui.charger_ix
					left join shop_company_position scp on cmd.position = scp.ps_ix
					WHERE  cui.company_id = '".$admininfo[company_id]."' order by cui.date asc ";
//echo $sql;
			$mdb->query($sql);
	}else{
		if(!$company_id){
			$sql = "SELECT cui.*, ui.master , AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail,
					AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel, cmd.sex_div, scp.ps_name
					FROM ".TBL_COMMON_USER."  cui
					right join ".TBL_COMMON_MEMBER_DETAIL." cmd on cui.code = cmd.code
					right join service_ing si on si.mem_ix = cmd.code and service_div = 'APP' and solution_div = 'WORK' and si_status = 'SI'
					left join work_userinfo ui on cui.code = ui.charger_ix left join shop_company_position scp on cmd.position = scp.ps_ix
					WHERE cui.code = cmd.code and cui.company_id = '".$admininfo[company_id]."'  order by cui.date asc ";
			//echo $sql;
			$mdb->query($sql);
		}else{
			$sql = "SELECT cui.*, ui.master , AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
					AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail,
					AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel, cmd.sex_div, 
					scp.ps_name
					FROM ".TBL_COMMON_USER."  cui
					right join ".TBL_COMMON_MEMBER_DETAIL." cmd on cui.code = cmd.code
					right join service_ing si on si.mem_ix = cmd.code and service_div = 'APP' and solution_div = 'WORK' and si_status = 'SI'
					left join work_userinfo ui on cui.code = ui.charger_ix and cui.company_id = '".$company_id."'
					left join shop_company_position scp on cmd.position = scp.ps_ix
					WHERE cui.code = cmd.code and cui.company_id = '".$company_id."'  order by cui.date asc ";


			//
			$mdb->query($sql);
		}
	}

//echo nl2br($sql);

	if($mdb->total){
		for($i=0;$i < $mdb->total;$i++){
		$mdb->fetch($i);
		$mstirng .= "
			  <tr bgcolor=#ffffff height=30 align=center>
			    <td class='list_box_td list_bg_gray'>".($i+1)."</td>
				<td class='list_box_td point' style='padding:5px;'>
					<table>
						<tr>
							<td>";

				if(file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/work/profile/profile_".$mdb->dt[code].".jpg")){
					$mstirng .= "<img src='".$admin_config[mall_data_root]."/work/profile/profile_".$mdb->dt[code].".jpg' width=50 height=50>";
				}else{
					$mstirng .= "<img src='../images/".($mdb->dt[sex_div] == "M" ? "man.jpg":"women.jpg")."' width=50 height=50>";
				}
		$mstirng .= "
							</td>
							<td>".($mdb->dt[master] == "Y" ? "<b>[M]".$mdb->dt[name]."</b>":$mdb->dt[name])."</td>
						</tr>
					</table>
				</td>
			    <td class='list_box_td'>".$mdb->dt[ps_name]."</td>
			    <td class='list_box_td list_bg_gray'>".$mdb->dt[id]."</td>
			    <td class='list_box_td'>".$mdb->dt[pcs]."</td>
				<td class='list_box_td list_bg_gray'>".$mdb->dt[mail]."</td>
			    <td class='list_box_td'>".$mdb->dt[date]."</td>
			    <td class='list_box_td list_bg_gray'>
			    	<!-- <a href=\"?company_id=$company_id&charger_id=".$mdb->dt[charger_ix]."\"><img src='../image/btc_modify.gif' border=0></a> -->
					<a href=\"javascript:updateUserInfo('$company_id','".$mdb->dt[code]."')\"><img src='../image/btc_modify.gif' border=0></a>
		    		<a href=\"javascript:deleteUserInfo('$company_id','".$mdb->dt[code]."')\"><img src='../image/btc_del.gif' border=0></a>
			    </td>
			  </tr>";
		}
	}else{
		$mstirng .= "
			  <tr bgcolor=#ffffff height=50>
			    <td align=center colspan=9>등록된 사용자가 없습니다. </td>
			  </tr>";
	}
	$mstirng .= "</table>
				<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
					<tr height=1><td colspan=9 style='padding:10px 0px;' align=right><a href=\"javascript:LayerShow('user_input_box')\"><img src='../image/btn_admin_add.gif'></a></td></tr>
				</table>";

	return $mstirng;

}

?>