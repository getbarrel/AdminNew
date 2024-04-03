<?
include("../class/layout.class");
include("./company.lib.php");
//auth(9);

$db = new Database;
$ig_db = new Database;
$mdb = new Database;

$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");

$menu_name = "관리자관리";

$info_type = "member_list";

$max = 15; //페이지당 갯수

if ($page == '')
{
	$start = 0;
	$page  = 1;
}
else
{
	$start = ($page - 1) * $max;
}

include "member_query.php";

$Script = "<script language='javascript' src='../include/DateSelect.js'></script>\n
<script language='javascript' src='./company.add.js'></script>\n";
$Script .= "
<script language='javascript'>
function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.sdate.disabled = false;
		frm.edate.disabled = false;
	}else{
		frm.sdate.disabled = true;
		frm.edate.disabled = true;
	}
}
function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
			
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
	//input_check_num();
}

function checkAll(frm){
	for(i=0;i < frm.code.length;i++){
			frm.code[i].checked = true;
	}
}

function clearAll(frm){
	for(i=0;i < frm.code.length;i++){
			frm.code[i].checked = false;
	}
}

function init(){

	var frm = document.searchmember;
//	onLoad('$sDate','$eDate');";

if($regdate != "1"){ 
	$Script .= "

	frm.sdate.disabled = true;
	frm.edate.disabled = true;";
}

$Script .= "
}

";
$Script .= "

function deleteMemberInfo(act, code){
	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
	}
}

</script>";

$Contents = "
<!--<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>-->
<table width='100%' border='0' align='center'>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("관리자관리", "기초정보관리 > 본사관리 ")."</td>
</tr>
<tr>
	<td align='left' colspan=4 style='padding-bottom:10px;'> 
	<div class='tab'>
	<table class='s_org_tab'>
	<tr>
		<td class='tab'>
			<table id='tab_01' class='on' >
			<tr>
				<th class='box_01'></th>
				<td class='box_02' onclick=\"document.location.href='member.list.php'\">전체사원 리스트</td>
				<th class='box_03'></th>
			</tr>
			</table>
			<table id='tab_02'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02' onclick=\"document.location.href='member.resign.php'\">퇴사관리자 관리</td>
				<th class='box_03'></th>
			</tr>
			</table>
			<table id='tab_03'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02' onclick=\"document.location.href='member.sleep.php'\">잠금관리자 관리</td>
				<th class='box_03'></th>
			</tr>
			</table>
			<!--<table id='tab_03' >
			<tr>
				<th class='box_01'></th>
				<td class='box_02' onclick=\"document.location.href='member.lump.php'\">**일괄등록하기</td>
				<th class='box_03'></th>
			</tr>
			</table>
			<table id='tab_04' >
			<tr>
				<th class='box_01'></th>
				<td class='box_02' >탭 메뉴 4</td>
				<th class='box_03'></th>
			</tr>
			</table>
			<table id='tab_05' >
			<tr>
				<th class='box_01'></th>
				<td class='box_02' >탭 메뉴 5</td>
				<th class='box_03'></th>
			</tr>
			</table>
			<table id='tab_06' >
			<tr>
				<th class='box_01'></th>
				<td class='box_02' >탭 메뉴 6</td>
				<th class='box_03'></th>
			</tr>
			</table-->
		</td>
		<td class='btn'>

		</td>
	</tr>
	</table>	
	</div>
	</td>
</tr>
<tr>
	<td>
		<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
			<tr>
				<th class='box_01'></th>
				<td class='box_02'></td>
				<th class='box_03'></th>
			</tr>
			<tr>
				<th class='box_04'></th>
				<td class='box_05'  valign=top style='padding:5px 0px 5px 0px'>
				<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25' class='search_table_box'>
				<form name=searchmember method='get'><!--SubmitX(this);'-->
					<input type='hidden' name=mc_ix value='".$mc_ix."'>
					<input type='hidden' name='cid2' value='$cid2'>
					<input type='hidden' name='depth' value='$depth'>
					<col width='15%'>
					<col width='*'>
					<tr>
						<td class='search_box_title'>근무 사업장</td>
						<td class='search_box_item' colspan='3'>
							<table border=0 cellpadding=0 cellspacing=0>
								<tr>
									<td style='padding-right:5px;'>
									".getCompanyList("본사", "cid0_1", "onChange=\"loadCategory('cid0_1','cid1_1','member')\" title='선택' ", '5', $cid2,'member')."</td>
									<td style='padding-right:5px;'>
									".getCompanyList("선택", "cid1_1", "onChange=\"loadCategory('cid1_1','cid2_1','member')\" title='선택'", '15', $cid2,'member')."</td>
									<td style='padding-right:5px;'>
									".getCompanyList("선택", "cid2_1", "onChange=\"loadCategory('cid2_1','cid3_1','member')\" title='선택'", '25', $cid2,'member')."</td>
									<td>".getCompanyList("선택", "cid3_1", "onChange=\"loadCategory('cid3_1','','member')\" title='선택'", '35', $cid2,'member')."</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class='search_box_title'>부서설정</td>
						<td class='search_box_item' colspan='3'>
							<table border=0 cellpadding=0 cellspacing=0>
							<tr>
								<td style='padding-right:5px;'>
								".getgroup1($com_group, "onChange=\"loadDepartment('com_group','department')\" title='본부선택' ",'true')."</td>
								<td style='padding-right:5px;'>
								".getdepartment($department,'','true')."</td>
								<td style='padding-right:5px;'>
								".getposition($position,'','true')."</td>
								<td>".getduty($duty,'','true')."</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' >재직구분 </td>
						<td class='search_box_item'  colspan='3'>
							<input type=checkbox name='work_devision[]' value='R' id='mailsend_a'  ".CompareReturnValue("R",$work_devision,"checked")."><label for='mailsend_a'>정사원</label>
							<input type=checkbox name='work_devision[]' value='I' id='mailsend_y'  ".CompareReturnValue("I",$work_devision,"checked")."><label for='mailsend_y'>인턴사원</label>
							<input type=checkbox name='work_devision[]' value='C' id='mailsend_c' ".CompareReturnValue("C",$work_devision,"checked")."><label for='mailsend_c'>계약직</label>
							<input type=checkbox name='work_devision[]' value='D' id='mailsend_d' ".CompareReturnValue("D",$work_devision,"checked")."><label for='mailsend_d'>일용직</label>
							<input type=checkbox name='work_devision[]' value='S' id='mailsend_s' ".CompareReturnValue("S",$work_devision,"checked")."><label for='mailsend_s'>용역</label>
							<input type=checkbox name='work_devision[]' value='O' id='mailsend_o' ".CompareReturnValue("O",$work_devision,"checked")."><label for='mailsend_o'>퇴사사원</label>
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' ><label for='regdate'>회사입사일</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
						<td class='search_box_item'  colspan=3 >
							".search_date('sdate','edate',$sdate,$edate)."
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' >조건검색 </td>
						<td class='search_box_item' colspan=3>
							<table cellpadding=0 cellspacing=0 width=100%>
							<col width='110'>
							<col width='*'>
							<tr>
								<td>
									<select name=search_type style='width:100px;'>
											<option value='cmd.name' ".CompareReturnValue('cmd.name',$search_type,"selected").">사원명</option>
											<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">아이디</option>
											<option value='cmd.tel' ".CompareReturnValue("cmd.tel",$search_type,"selected").">전화번호</option>
											<option value='cmd.pcs' ".CompareReturnValue("cmd.pcs",$search_type,"selected").">휴대전화</option>
											<option value='cmd.mail' ".CompareReturnValue("cmd.mail",$search_type,"selected").">이메일</option>
											<option value='cmd.addr1' ".CompareReturnValue("cmd.addr1",$search_type,"selected").">주소</option>
											<option value='ccd.com_name' ".CompareReturnValue("ccd.com_name",$search_type,"selected").">회사명</option>
											<option value='cmd.com_tel' ".CompareReturnValue("cmd.com_tel",$search_type,"selected").">회사전화</option>
											<option value='ccd.com_fax' ".CompareReturnValue("ccd.com_fax",$search_type,"selected").">회사팩스</option>
									</select>
								</td>
								<td>
									<input type=text name='search_text' class='textbox point_color' value='".$search_text."' style='width:200px;font-size:12px;padding:0px;' >
								</td>
							</tr>
							</table>
						</td>
					</tr>
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
	</td>
</tr>
</table>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
<tr height=50>
	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
</tr>
</table><br>
</form>";

$Contents .= "
<form name='list_frm'>
<input type='hidden' name='code[]' id='code'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr>
		<td colspan=1>
		</td>
		<td align='right' colspan=3 style='padding:5px 0 5px 0;display:none;'>
		<a href=\"javascript:PoPWindow3('member.report.php?mmode=pop&info_type=".$info_type."&".$QUERY_STRING."',970,800,'stock_report')\"> <img src='../images/".$admininfo["language"]."/btn_report_print.gif'></a>
		<a href='?mmode=pop'> </a> ";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?".$QUERY_STRING."&info_type=member_list&excel_type=member_list' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$Contents .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= " <a href='member.list.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
		$Contents .= "
		</td>
	</tr>
</table>";

$Contents .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
	<col width=2%>	체크박스
	<col width=4%>	순번
	<col width=7%>	사원코드	
	<col width=12%>	이름
	<col width=12%>	아이디
	<col width=12%>	근무사업장
	

	<col width=12%>	부서그룹
	<col width=12%>	부서

	<col width=15%>	이메일
	<col width=12%>	관리

	<tr height='28' bgcolor='#ffffff'>
		<td class=s_td rowspan='2'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
		<td class='m_td' rowspan='2'><font color='#000000'><b>순번</b></font></td>
		<td class='m_td' rowspan='2'><font color='#000000'><b>사원코드</b></font></td>
		<td class=m_td rowspan='2'><font color='#000000'><b>이름</b></font></td>
		<td class=m_td rowspan='2'><font color='#000000'><b>아이디</b></font></td>
		<td class=m_td rowspan='2'><font color='#000000'><b>근무사업장</b></font></td>
		<td class=m_td colspan='2'><font color='#000000'><b>부서및직책</b></font></td>
		<td class=m_td rowspan='2'><font color='#000000'><b>이메일</b></font></td>
		<td width='100' align='center' class=e_td rowspan='2'><font color='#000000'><b>관리</b></font></td>
	</tr>
	<tr height='28' bgcolor='#ffffff'>
		<td class=m_td><font color='#000000'><b>부서그룹</b></font></td>
		<td class=m_td><font color='#000000'><b>부서</b></font></td>
	</tr>";

for($i = 0; $i < $db->total; $i++){

	$db->fetch($i);

	$no = $total - ($page - 1) * $max - $i;

	if($db->dt[is_id_auth] != "Y"){
		$is_id_auth = "미인증";
	}else{
		$is_id_auth = "";
	}

	switch($db->dt[authorized]){

	case "Y":
		$authorized = "승인";
		break;
	case "N":
		$authorized = "승인대기";
		break;
	case "X":
		$authorized = "승인거부";
		break;
	default:
		$authorized = "알수없음";
		break;
	}

	switch($db->dt[mem_type]){

	case "M":
		$mem_type = "일반";
		break;
	case "C":
		$mem_type = "기업".($db->dt[com_name] != "" ? "(".$db->dt[com_name].")":"");
		break;
	case "F":
		$mem_type = "외국인";
		break;
	case "S":
		$mem_type = "셀러";
		break;
	case "A":
		$mem_type = "관리자";
		break;
	case "MD":
		$mem_type = "MD";
		break;
	default:
		$mem_type = "일반";
		break;
	}

	if($db->dt[join_date]){
		$join_array = explode(" ",$db->dt[join_date]);
		$join_date = $join_array[0];
	}else{
		$join_date = " - ";
	}

	$now = date('Y-m');
	$now_array = explode("-",$now);
	$join_array = explode("-",$join_date);

	$join_year = $now_array[0] - $join_array[0];
	$join_month = $now_array[1] - $join_array[1];
	if($join_year > 0){
		$year = $join_year." 년 ";
	}
	if($year > 0){
		$month = 12 -$join_array[1] + $now_array[1]." 개월 ";
	}else{
		if($join_month > 0){
			$month = $join_month." 개월 ";
		}
	}

		//	임시잠금 해제
			if($db->dt[fail_step] > "0") {
				$ig_add_view1 = "<br><a href=\"javascript:ig_admin_loginChk_up('".$db->dt[id]."')\" style='color:#FF0000;'>[임시잠금회원]</a>";
			} else {
				$ig_add_view1 = "";
			}
		//	//임시잠금 해제


$Contents .= "
	<tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
		<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
		<td class='list_box_td' >".$no."</td>
		<td class='list_box_td' style='padding:0px 5px;'>".$db->dt[mem_code]."</td>
		<td class='list_box_td' >".$db->dt[name]."</td>
		<td class='list_box_td' >".$db->dt[id].$ig_add_view1."</td>

		<td class='list_box_td point' nowrap>".getCompanyname($db->dt[relation_code])."</td>
		<!--<td class='list_box_td' >".getCompanyname($db->dt[relation_code],9)."</td>
		<td class='list_box_td' >".getCompanyname($db->dt[relation_code],13)."</td>
		<td class='list_box_td' >".getCompanyname($db->dt[relation_code],17)."</td>-->

		<td class='list_box_td ' nowrap>".getGroupname('group',$db->dt[com_group])."</td>
		<td class='list_box_td point' >".getGroupname('department',$db->dt[department])."</td>
		<td class='list_box_td' >".$db->dt[mail]."</td>

		<!--<td class='list_box_td ctr point' >".$db->dt[mail]."</a></td>-->
		<td class='list_box_td ctr'  style='padding:5px;' nowrap>";

		if($update_auth){
			$Contents .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member.add.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',1000,710,'member_info')\" style='cursor:pointer;' /> ";
		}else{
			$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
		}
		//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
		if($delete_auth){
			$Contents .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" style='cursor:pointer;' /> ";
		}else{
			//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
		}
		if($create_auth){
			$Contents .= "
			<img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\" style='cursor:pointer;' />
			<img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\" style='cursor:pointer;' />";
		}else{
			$Contents .= "
			<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle></a>
			<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle></a> ";
		}
$Contents .= "
	</td>
</tr>";
	unset($year,$month);

}

if (!$db->total){

$Contents .="
	<tr height=50>
		<td colspan='17' align='center'>등록된 회원 데이타가 없습니다.</td>
	</tr>";
}

$Contents .= "
</table>
</form>
<table width=100% align='right'>";

if( $admininfo[mall_use_multishop] && $admininfo[admin_level] == 9){
	$Contents .= "<tr hegiht=30><td colspan=8 align=right style='padding:10px 0px;'>".$str_page_bar."</td></tr>";
	
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		if($admininfo["mall_type"] == "B" || $admininfo["mall_type"] == "F" || $admininfo["mall_type"] == "R"   || $admininfo["mall_type"] == "BW" ){
				$Contents .= "<tr hegiht=30><td colspan=8 align=right style='padding-top:10px;'><a href='member.add.php'><img src='../images/".$admininfo["language"]."/basic_member_add.gif' border=0></a></td></tr>";
		}else{
			$Contents .= "<tr hegiht=30><td colspan=8 align=right style='padding-top:10px;'><a href='member.add.php'><img src='../images/".$admininfo["language"]."/basic_member_add.gif' border=0></a></td></tr>";
		}
	}
}else{
	$Contents .= "<tr hegiht=30><td colspan=8 align=right style='padding-top:10px 0px;'>".$str_page_bar."</td></tr>";
}
$Contents .= "
</table>";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사원에게 SMS 또는 메일을 보내실려면 보내고자 하는 사원을 선택하신후 '선택사원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사원정보를 백업하기 위해서는 사원정보 검색후 엑셀로 저장 버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 사원을 빠르게 검색하실수 있습니다</td></tr>
</table>
";

$Contents .= HelpBox("사원관리", $help_text,'70');







	//	잠금 사원 기능 추가
			$help_text2 = "

			<table cellpadding=0 cellspacing=0 class='small' width=100% >
				<col width=8>
				<col width=*>
				<tr>
				<td colspan='2'>
				<select name='update_type'>
					<!--<option value=''>전환방식 선택</option>-->
					<option value='2'>선택한회원 전체에게</option>
					<!--<option value='1'>검색한 회원 전체에게</option>-->
				</select>
				</td>
				</tr>";

				$help_text2 .= "
				<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용자를 접속 이용기간에 또는 지속적인 패스워드 입력 오류에 따라 휴면 회원으로 변경 할 수 있습니다.</td></tr>
				<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >잠금사원(휴면회원)으로 전환시 '정보통신망 이용조치 및 정보보호 등에 관한 법률 시행령 제 16조' 에 의하여 회원정보를 이용 할 수 없습니다.(회원관리,CRM)</td></tr>
				<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용자가 이용 또는 요청에 따라 일반 사용자로 전환되어야 회원정보의 이용이 가능합니다.</td></tr>";

				$help_text2 .= "
				<tr>
					<td align=left colspan='2' style='padding-top:10px;'>
				";
	
				$help_text2 .= "
				<button type='button' onclick=\"ig_sendData('sleep');\">잠금사용자 전환</button>";
				$help_text2 .= "
					</td>
				</tr>
			</table>
			";

			$Contents .= HelpBox("잠금사용자관리", $help_text2,'70')."</form>";
	//	//잠금 사원 기능 추가












$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>

</form>";

if($mmode == "pop" || $mmode == "report"){

	$P = new ManagePopLayOut();
		$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
		$P->Navigation = "기초정보관리 > 본사관리 > $menu_name";
		$P->title = "관리자관리";
		$P->strContents = $Contents;
		$P->OnloadFunction = "init();";
		echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
	$P->OnloadFunction = "init();";
	$P->strLeftMenu = basic_menu();
	$P->Navigation = "기초정보관리 > 본사관리 > $menu_name";
	$P->title = "관리자관리";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}
?>




	<script type="text/javascript">
		//	계정 전환
		function ig_sendData(sendType){


			var ig_frm = document.list_frm;
			var ig_code_checked_num = 0;
			var code_List = new Array();	//	선택된 회원 리스트


			for(i=1;i < ig_frm.code.length;i++){
				if(ig_frm.code[i].checked){
					code_List.push(ig_frm.code[i].value);
					ig_code_checked_num++;
				}
			}


			if(ig_code_checked_num == "0") {
				alert("사원을 선택해 주세요.");
				return false;
			} else {
				if(confirm('선택한 사원을 잠금계정 처리 하시겠습니까?')) {

						//	sendType 타입 : sleep = 잠그기, nosleep = 풀기
						$.ajax({
							type: "post",
							url:"ig_sendData.php",
							data:{
									"sendType":sendType,
									"code_List":code_List
								},
							cache: false,	
							async: false,
							success:function(data) {

								//console.log(data);

								if(data == "sendOK") {
									alert("처리되었습니다.");
									location.reload();
									return false;
								} else {
									alert(data);
									return false;
								}
							}
						});
				} else {
					return false;
				}
			}
		}



		//	임시잠금 해제
		function ig_admin_loginChk_up(s_id){
			var code_List = new Array();	//	선택된 회원 리스트
			code_List.push(s_id);

				if(confirm(s_id+' 계정의 임시잠금 해지를 하시겠습니까?')) {

						//	sendType 타입 : sleep = 잠그기, nosleep = 풀기, 임시잠금 풀기 = ig_admin_loginChk
						$.ajax({
							type: "post",
							url:"ig_sendData.php",
							data:{
									"sendType":"ig_admin_loginChk",
									"code_List":code_List
								},
							cache: false,	
							async: false,
							success:function(data) {

								//console.log(data);

								if(data == "sendOK") {
									alert("처리되었습니다.");
									location.reload();
									return false;
								} else {
									alert(data);
									return false;
								}
							}
						});
				} else {
					return false;
				}
		}
	</script>