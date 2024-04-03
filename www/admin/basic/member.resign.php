<?
include("../class/layout.class");
include("./company.lib.php");
//auth(9);

$db = new Database;
$mdb = new Database;
//echo "<pre>";
//print_r ($_REQUEST);
//EXIT;
//$db->debug = true;//페이지에서 오류가 없는데 쿼리를 찍기에 주석처리함 kbk 13/02/04
$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");

if( $admininfo[mall_use_multishop] && $admininfo[mall_div]){
	$menu_name = "퇴사관리자 관리";
}else{
	$menu_name = "퇴사관리자 관리";
}

$info_type = "member_resign";
$work_devision = 'O'; //퇴사사원 리스트 

$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

include "member_query.php";

$Script = "<script language='javascript' src='../include/DateSelect.js'></script>\n
<script language='javascript' src='../basic/company.add.js'></script>\n";
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

if($regdate != "2"){
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

<table width='100%' border='0' align='center'>
<tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("전체사원리스트", "기초정보관리 > 본사관리 ")."</td>
</tr>
	<tr>
	<td align='left' colspan=4 style='padding-bottom:20px;'> 
		<div class='tab'>
		<table class='s_org_tab'>
		<tr>
			<td class='tab'>
				<table id='tab_01' >
				<tr>
					<th class='box_01'></th>
					<td class='box_02' onclick=\"document.location.href='member.list.php'\">전체사원 리스트</td>
					<th class='box_03'></th>
				</tr>
				</table>
				<table id='tab_02' class='on' >
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
					<td class='box_02' onclick=\"document.location.href='member.lump.php'\">일괄등록하기</td>
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
	<td>";
$Contents .= "
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
						<td class='search_box_title' ><label for='regdate'>기간 퇴사일</label><input type='checkbox' name='regdate' id='regdate' value='2' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("2",$regdate,"checked")."></td>
						<td class='search_box_item'  colspan=3 >
							".search_date('sdate','edate',$sdate,$edate)."
						</td>
					</tr>
					<tr height=27>
						<td class='search_box_title' >검색 </td>
						<td class='search_box_item' colspan=3>
							<table cellpadding=0 cellspacing=0 width=100%>
								<col width='80'>
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
									<td>&nbsp;
										<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:300px;font-size:12px;padding:1px;' >
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
		</table>";

$Contents .= "
    </td>
  </tr>
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
		<a href=\"javascript:PoPWindow3('member.report.php?mmode=report&info_type=".$info_type."&".$QUERY_STRING."',970,800,'stock_report')\"> <img src='../images/".$admininfo["language"]."/btn_report_print.gif'></a>
		<a href='?mmode=pop'> </a> ";
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?".$QUERY_STRING."&info_type=".$info_type."&excel_type=member_resign' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= " <a href='member.resign.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
		$Contents .= "
				</td>
			</tr>
		</table>";

$Contents .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
<tr height='28' bgcolor='#ffffff'>
	<td width='3%' align='center' class=s_td rowspan='2'><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
	<td width='3%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>순번</b></font></td>
	<td width='5%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>사원코드</b></font></td>
	<td width='5%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>입사일</font></td>
	<td width='6%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>퇴사일<br>(근무년수)</b></font></td>
	<td width='6%' align='center' class=m_td rowspan='2'><font color='#000000'><b>이름</b></font></td>
	<td width='10%' align='center' class=m_td rowspan='2'><font color='#000000'><b>근무사업장</b></font></td>
	<td width='13%' align='center' class=m_td colspan='4'><font color='#000000'><b>부서및직책</b></font></td>
	<!--<td width='7%' align='center' class=m_td rowspan='2'><font color='#000000'><b>연락처</b></font></td>-->
	<!--<td width='10%' align='center' class=m_td rowspan='2'><font color='#000000'><b>이메일</b></font></td>-->
	<!--<td width='6%' align='center' class=m_td rowspan='2'><font color='#000000'><b>메신저</b></font></td>-->
	<td width='9%' align='center' class=e_td rowspan='2'><font color='#000000'><b>관리</b></font></td>
</tr>";
$Contents .= "
	<tr height='28' bgcolor='#ffffff'>
<!--
	<td width='5%' align='center' class=m_td ><font color='#000000'><b>1DEPTH</b></font></td>
	<td width='5%' align='center' class=m_td><font color='#000000'><b>2DEPTH</b></font></td>
	<td width='5%' align='center' class=m_td><font color='#000000'><b>3DEPTH</b></font></td>
	<td width='5%' align='center' class=m_td><font color='#000000'><b>4DEPTH</b></font></td>-->

	<td width='6%' align='center' class=m_td><font color='#000000'><b>부서그룹</b></font></td>
	<td width='6%' align='center' class=m_td><font color='#000000'><b>부서</b></font></td>
	<td width='6%' align='center' class=m_td><font color='#000000'><b>직위</b></font></td>
	<td width='6%' align='center' class=m_td><font color='#000000'><b>직책</b></font></td>
	
	</tr>";

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;
	/*
		if ($db->dt[mem_level] == "E")	{ $perm = "탈퇴회원"; }
		if ($db->dt[mem_level] == "M")	{ $perm = "일반회원"; }
		if ($db->dt[mem_level] == "B")	{ $perm = "입점업체"; }
		if ($db->dt[mem_level] == "C")	{ $perm = "특별회원"; }
	*/
		
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

	//$resign_date = explode(" ",$db->dt[resign_date]);

	if($db->dt[join_date]){
		$join_array = explode(" ",$db->dt[join_date]);
		$join_date = $join_array[0];
	}else{
		$join_date = " - ";
	}

	if($db->dt[resign_date]){
		$resign_array = explode(" ",$db->dt[resign_date]);
		$resign_date = $resign_array[0];
	}else{
		$resign_date = " - ";
	}

	$resign_array = explode("-",$resign_date);

//	$now = date('Y-m');
	$now = $resign_array[0]."-".$resign_array[1];
	$now_array = explode("-",$now);
	$join_array = explode("-",$join_date);

	$resign_year = $now_array[0] - $join_array[0];
	$resign_month = $now_array[1] - $join_array[1];

	if($resign_year > 0){
		$year = $resign_year." 년 ";
	}

	if($year > 0){
		$month = 12 -$join_array[1] + $now_array[1]." 개월 ";
	}else{
		if($resign_month > 0){
			$month = $resign_month." 개월 ";
		}
	}

	$Contents = $Contents."
	<tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
		<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
		<td class='list_box_td' >".$no."</td>
		<td class='list_box_td' style='padding:0px 5px;'>".$db->dt[mem_code]."</td>
		<td class='list_box_td' nowrap>".$join_date."</td>
		 <td class='list_box_td' nowrap>".$resign_date."<br>".$year.$month."</td>
		<td class='list_box_td' ><a href='javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')' style='cursor:pointer' >".Black_list_check($db->dt[code],$db->dt[name])."</td>
		
		<td class='list_box_td point' nowrap>".$db->dt[com_name]."</td>
		<!--<td class='list_box_td' >".getCompanyname($db->dt[relation_code],9)."</td>
		<td class='list_box_td' >".getCompanyname($db->dt[relation_code],13)."</td>
		<td class='list_box_td' >".getCompanyname($db->dt[relation_code],17)."</td>-->


		<td class='list_box_td ' nowrap>".getGroupname('group',$db->dt[com_group])."</td>
		<td class='list_box_td point' >".getGroupname('department',$db->dt[department])."</td>
		<td class='list_box_td' >".getGroupname('position',$db->dt[position])."</td>
		<td class='list_box_td point' >".getGroupname('duty',$db->dt[duty])."</td>

		<!--<td class='list_box_td' >".$db->dt[pcs]."</td>-->
		<!--<td class='list_box_td' >".$db->dt[mail]."</td>-->

		<!--<td class='list_box_td ctr point' >".$db->dt[mail]."</a></td>-->
		<td class='list_box_td ctr'  style='padding:5px;' nowrap>";

		if($update_auth){
			$Contents .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle style='cursor:pointer;' onClick=\"PopSWindow('member.add.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\" /> ";
		}else{
			$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
		}

		//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
		if($delete_auth){
			$Contents .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle style='cursor:pointer;'  onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" /> ";
		}else{
			//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
		}
		if($create_auth){
			 $Contents .= "
			 <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle style='cursor:pointer;'  onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\">
			 <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle style='cursor:pointer;'  onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\">
			 ";
		}else{
			$Contents .= "
			 <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle style='cursor:pointer;' ></a>
			 <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle style='cursor:pointer;' ></a>
			 ";
		}
		$Contents .= "
		</td>
	</tr>";

	}

if (!$db->total){

$Contents = $Contents."
	<tr height=50>
		<td colspan='14' align='center'>등록된 회원 데이타가 없습니다.</td>
	</tr>";
}


$Contents .= "
</table>
</form>
<table width=100% align='right'>
<tr hegiht=30><td colspan=8 align=right style='padding:10px 0px;'>".$str_page_bar."</td></tr>
</table>";

/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원에게 SMS 또는 메일을 보내실려면 보내고자 하는 회원을 선택하신후 '선택회원 SMS 보내기' 버튼을 클릭하신후 메일을 보내실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원정보를 백업하기 위해서는 회원정보 검색후 엑셀로 저장 버튼을 클릭하시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >검색기능을 통해서 조건에 맞는 회원을 빠르게 검색하실수 있습니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("사원관리", $help_text,'70');


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = basic_menu();
$P->Navigation = "기초정보관리 > 본사관리 > $menu_name";
$P->title = "퇴사사원리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>



