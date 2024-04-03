<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

$shmop = new Shared("member_reg_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$member_reg_rule = $shmop->getObjectForKey("member_reg_rule");

$member_reg_rule = unserialize(urldecode($member_reg_rule));
//print_r($member_reg_rule);
$db = new Database;

$display_yn_hidden = 'display:none;'; //강제로 히든처리

$sql = "SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ";

//echo $sql;
$db->query($sql);

$db->fetch();

if($join_type == ""){
	$join_type = "B";
}

$Contents01 = "

	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' >
	  <col width=150>
	  <col width=250>
	  <col width=*>
	  <tr >
			<td align='left' colspan=3> ".GetTitleNavigation("회원가입설정", "상점관리 > 쇼핑몰 환경설정 > 회원가입설정 ")."</td>
	  </tr>
	  <tr>
		<td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b>회원전용 사용여부</b></div>")."</td>
	</tr>
   </table>
   <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	  <col width=15%>
	  <col width=35%>
	  <col width=15%>
	  <col width=35%>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>사용권한 설정</b></td>
		<td class='input_box_item' colspan='3'>
			<input type=radio name='mall_open_yn' id='mall_open_y' value='Y' ".CompareReturnValue("Y",$member_reg_rule[mall_open_yn],"checked")."><label for='mall_open_y'>회원전용</label>&nbsp;
			<input type=radio name='mall_open_yn' id='mall_open_n' value='N' ".CompareReturnValue("N",$member_reg_rule[mall_open_yn],"checked")."><label for='mall_open_n'>전체</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>일반회원 승인 설정</b></td>
		<td class='input_box_item'>
			<input type=radio name='auth_type' id='auth_type_a' value='A' ".($member_reg_rule[auth_type] == "A" || $member_reg_rule[auth_type] == "" ? "checked":"")."><label for='auth_type_a'>자동승인</label>&nbsp;
			<input type=radio name='auth_type' id='auth_type_m' value='M' ".CompareReturnValue("M",$member_reg_rule[auth_type],"checked")."><label for='auth_type_m'>관리자 승인</label>&nbsp;
		</td>
		<td class='input_box_title'> <b>사업자회원 승인 설정</b></td>
		<td class='input_box_item'>
			<input type=radio name='b2b_auth_type' id='b2b_auth_type_a' value='A' ".($member_reg_rule[b2b_auth_type] == "A" || $member_reg_rule[b2b_auth_type] == "" ? "checked":"")."><label for='b2b_auth_type_a'>자동승인</label>&nbsp;
			<input type=radio name='b2b_auth_type' id='b2b_auth_type_m' value='M' ".CompareReturnValue("M",$member_reg_rule[b2b_auth_type],"checked")."><label for='b2b_auth_type_m'>관리자 승인</label>&nbsp;
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>회원형태 선택(복수선택 가능)</b></td>
		<td class='input_box_item' colspan='3'>
			<input type='checkbox' name='join_type_b' id='join_type_b' value='B' ".(substr_count($member_reg_rule[join_type],'B')>0 || $member_reg_rule[join_type]=="" ? "checked":"")."><label for='join_type_b'>일반회원</label>&nbsp;
			<input type='checkbox' name='join_type_c' id='join_type_c' value='C' ".(substr_count($member_reg_rule[join_type],'C')>0 ? "checked":"")."><label for='join_type_c'>사업자회원</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff style='".$display_yn_hidden."'>
		<td class='input_box_title'> <b>인증방식</b></td>
		<td class='input_box_item'>
			<input type=radio name='auth_method' id='auth_method_a' value='J' ".($member_reg_rule[auth_method] == "J" || $member_reg_rule[auth_method] == "" ? "checked":"")." onClick='ch_auth_form(this)'><label for='auth_method_a'> 인증</label>&nbsp;
			<input type=radio name='auth_method' id='auth_method_n' value='N' ".CompareReturnValue("N",$member_reg_rule[auth_method],"checked")." onClick='ch_auth_form(this)'><label for='auth_method_n'> 비인증</label>
		</td>
		<td class='input_box_title'> <b>가입 후 이메일 인증</b></td>
		<td class='input_box_item'>
			<input type=checkbox name='email_auth' id='email_auth' value='Y' ".CompareReturnValue("Y",$member_reg_rule[email_auth],"checked")."><label for='email_auth'>이메일 인증 사용함</label>
		</td>
	</tr>

	<!--tr bgcolor=#ffffff id='use_identity' ".CompareReturnValue("N",$member_reg_rule[auth_method],"style='display:none;'").">
		<td class='input_box_title'> <b>실명인증 사용여부</b></td>
		<td class='input_box_item' colspan=3>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td style='padding:0px 10px 0px 0px'>
					<input type='checkbox' id='mall_use_identificationUse' name='mall_use_identificationUse' value='Y'".(($member_reg_rule[mall_use_identificationUse] == "Y")	?	' checked':'')." onclick=\"document.getElementById('mall_use_identification').style.display = (this.checked)	?	'':'none';\" style='vertical-align:middle;'' /> <label for='mall_use_identificationUse' style='vertical-align:middle;'>ID 입력</label>
					<input type='text' id='mall_use_identification' name='mall_use_identification' class='textbox' value='".$member_reg_rule[mall_use_identification]."' style='display:".(($member_reg_rule[mall_use_identification])	?	'':'none').";'  />
					</td>
					<td align=left><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
				</tr>
			</table>
		</td>
	</tr-->

	<!-- <tr bgcolor=#ffffff id='use_ipin' ".CompareReturnValue("N",$member_reg_rule[auth_method],"style='display: none;'")."> -->
	<tr bgcolor=#ffffff id='use_ipin' style='".$display_yn_hidden."'>	
		<td class='input_box_title'> <b>아이핀 사용여부</b></td>
		<td class='input_box_item' style='padding:7px 5px 7px 5px;' colspan=3>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td style='padding:0px 10px 0px 0px'>
					<input type='checkbox' id='mall_use_ipin' name='mall_use_ipin' value='Y'".(($member_reg_rule[mall_use_ipin] == "Y")	?	' checked':'')." onclick=\"document.getElementById('ipin_info').style.display = (this.checked)	?	'':'none';\" style='vertical-align:middle;'' /> <label for='mall_use_ipin' style='vertical-align:middle;'>ID 입력</label>
					</td>
					<td align=left><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
				</tr>
				<tr>
					<td colspan='3'>
						 <table id='ipin_info' style='display:".(($member_reg_rule[mall_use_ipin] == "Y")	?	'':'none').";'>
							<tr>
								<td>회원사아이디</td>
								<td><input type='text' id='mall_ipin_code' name='mall_ipin_code' class='textbox' value='".$member_reg_rule[mall_ipin_code]."' /></td>
							</tr>
							<tr>
								<td>사이트식별번호</td>
								<td><input type='text' id='mall_ipin_pw' name='mall_ipin_pw' class='textbox' value='".$member_reg_rule[mall_ipin_pw]."' /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
    <!-- <tr bgcolor=#ffffff id='use_nice' ".CompareReturnValue("N",$member_reg_rule[auth_method],"style='display: none;'")."> -->
    <tr bgcolor=#ffffff id='use_nice' style='".$display_yn_hidden."'>
		<td class='input_box_title'> <b>본인인증 사용여부</b></td>
		<td class='input_box_item' colspan=3 style='padding:7px 5px 7px 5px;' >
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td style='padding:0px 10px 0px 0px'>
					<input type='checkbox' id='mall_use_certify' name='mall_use_certify' value='Y'".(($member_reg_rule[mall_use_certify] == "Y")	?	' checked':'')." onclick=\"document.getElementById('certify_info').style.display = (this.checked)	?	'':'none';\" style='vertical-align:middle;'' /> <label for='mall_use_certify' style='vertical-align:middle;'>CODE 입력</label>
					</td>
					<td align=left><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
				</tr>
				<tr>
					<td colspan='3'>
						 <table id='certify_info' style='display:".(($member_reg_rule[mall_use_certify] == "Y")	?	'':'none').";'>
							<tr>
								<td>사이트코드</td>
								<td><input type='text' id='mall_certify_code' name='mall_certify_code' class='textbox' value='".$member_reg_rule[mall_certify_code]."' /></td>
							</tr>
							<tr>
								<td>사이트비밀번호</td>
								<td><input type='text' id='mall_certify_pw' name='mall_certify_pw' class='textbox' value='".$member_reg_rule[mall_certify_pw]."' /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
<!--
	<tr bgcolor=#ffffff id='use_com_number' ".CompareReturnValue("N",$member_reg_rule[auth_method],"style='display: none;'").">
		<td class='input_box_title'> <b>사업자 인증 사용여부</b></td>
		<td class='input_box_item' colspan=3 style='padding:7px 5px 7px 5px;' >
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td style='padding:0px 10px 0px 0px'>
					<input type='checkbox' id='mall_use_com_number' name='mall_use_com_number' value='Y'".(($member_reg_rule[mall_use_com_number] == "Y")	?	' checked':'')." onclick=\"document.getElementById('com_number_info').style.display = (this.checked)	?	'':'none';\" style='vertical-align:middle;'' /> <label for='mall_use_com_number' style='vertical-align:middle;'>CODE 입력</label>
					</td>
					<td align=left><img src='../image/emo_3_15.gif' align=absmiddle > <span class=small> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span> </td>
				</tr>
				<tr>
					<td colspan='3'>
						 <table id='com_number_info' style='display:".(($member_reg_rule[mall_use_com_number] == "Y")	?	'':'none').";'>
							<tr>
								<td>회원사 아이디</td>
								<td><input type='text' id='mall_com_number_id' name='mall_com_number_id' class='textbox' value='".$member_reg_rule[mall_com_number_id]."' /></td>
							</tr>
							<tr>
								<td>사이트 식별코드</td>
								<td><input type='text' id='mall_com_number_pw' name='mall_com_number_pw' class='textbox' value='".$member_reg_rule[mall_com_number_pw]."' /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	-->
	<!--tr bgcolor=#ffffff >
		<td ><img src='../image/ico_dot2.gif' align=absmiddle> 인트로사용</td>
		<td>
			<input type=radio name='mall_intro_use' id='mall_intro_use_y' value='Y' ".CompareReturnValue("Y",$member_reg_rule[mall_intro_use],"checked")."><label for='mall_intro_use_y'>사용</label>
			<input type=radio name='mall_intro_use' id='mall_intro_use_n' value='N' ".CompareReturnValue("N",$member_reg_rule[mall_intro_use],"checked")."><label for='mall_intro_use_n'>사용 하지않음</label>
			<span class=small style='color:gray'>디자인 관리에서 인트로 화면에 대한 디자인 작업을 하셔야 합니다.</span>
		</td>
		<td align=left></td>
	</tr-->
	<tr bgcolor=#ffffff height=110>
	    <td class='input_box_title'><b>가입불가ID</b></td>
	    <td class='input_box_item' colspan=3><textarea type=text class='textbox' name='mall_deny_id' style='width:98%;height:85px;padding:2px;'>".$member_reg_rule[mall_deny_id]."</textarea></td>
	</tr>
</table>
<ul class='paging_area' >
	<li class='front'><img src='../image/emo_3_15.gif' align=absmiddle > <span  style='line-height:120%'> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span></li>
	<li class='back'></li>
</ul>";

$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$ButtonString .= "<table border=0 cellpadding=0 cellspacing=0 width='100%'><tr height=50 bgcolor=#ffffff><td colspan=8 align=center><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle></td></tr></table>";
}

$Contents = "<form name='login_form' action='mall_manage.act.php' method='post' onsubmit='return CheckFormValue(this)' target='act'>
<input type=hidden name='join_type' id='join_type' value='".$join_type."'>
<table width='100%' height='100%'  border=0>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";

//$Contents = "<div style=height:1000px;'></div>";


$Script = "<script language='javascript' src='mall_manage.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 쇼핑몰 환경설정 > 회원가입설정 ";
$P->title = "회원가입설정 ";
$P->strContents = $Contents;
echo $P->PrintLayOut();


/*

사업자 회원 기본 필드값 추가 
INSERT INTO `shop_join_info` (`join_type`, `field`, `field_name`, `field_type`, `field_value`, `disp`, `validation_yn`, `vieworder`, `modify_yn`, `regdate`) VALUES
('C', 'id', '아이디', 'text', '', 'Y', 'Y', 4, 'N', '2010-02-27 19:37:23'),
('C', 'pw', '패스워드', 'password', '', 'Y', 'Y', 5, 'N', '2010-02-27 19:38:19'),
('C', 'name', '이름', 'text', '', 'Y', 'Y', 2, 'N', '2010-02-27 19:40:35'),
('C', 'address', '주소', 'text', '', 'Y', 'Y', 7, 'N', '2010-02-27 19:42:41'),
('C', 'tel', '유선전화', 'text', '', 'Y', 'N', 9, 'N', '2010-02-27 19:43:18'),
('C', 'pcs', '휴대전화', 'text', '', 'Y', 'Y', 8, 'N', '2010-02-27 19:43:28'),
('C', 'mail', '이메일', 'select', '', 'Y', 'Y', 6, 'N', '2010-02-27 19:43:47'),
('C', 'jumin', '주민등록번호', 'text', '', 'N', 'N', 3, 'N', '2010-02-27 19:44:05'),
('C', 'birthday', '생년월일', 'text', '', 'Y', 'N', 10, 'N', '2010-02-27 19:44:24'),
('C', 'nick_name', '별명', 'text', '', 'Y', 'N', 11, 'N', '2010-02-27 19:44:46'),
('C', 'job', '직업', 'text', '', 'N', 'N', 12, 'N', '2010-02-27 19:44:55'),
('C', 'add_etc1', '회원구분', 'checkbox', '사서|교사|기간교사(어린이집/유치원)|일반회원|입점사', 'Y', 'N', 13, 'Y', '2010-02-27 20:05:10'),
('C', 'add_etc2', '추천인ID', 'text', '', 'N', 'N', 14, 'Y', '2010-02-27 20:05:13'),
('C', 'add_etc3', '좋아하는색 예제', 'select', 'black|yellow|white', 'N', 'N', 15, 'Y', '2010-02-27 20:05:17'),
('C', 'add_etc4', '중복선택 예제', 'checkbox', '1|2|3|4|', 'N', 'N', 16, 'Y', '2010-02-27 20:05:20'),
('C', 'add_etc5', ' ', '', '123|1233|ㅁㅇㄹ', 'N', 'N', 17, 'Y', '2010-02-27 20:05:22'),
('C', 'add_etc6', ' ', '', '', 'N', 'N', 18, 'Y', '2010-02-27 20:05:37');


*/

?>