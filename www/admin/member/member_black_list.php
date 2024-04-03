<?
include("../class/layout.class");
//auth(9);

$db = new Database;
$mdb = new Database;

if($vip_yn ==""){
	$where .= " and cmd.level_ix in ('7','8','9')  ";
	$count_where .= " and cmd.level_ix in ('7','8','9')  ";
}else{
	$where .= " and cmd.level_ix = '".$_REQUEST[vip_yn]."'  ";
	$count_where .= " and cmd.level_ix = '".$_REQUEST[vip_yn]."'  ";
}
//$where .= " and cmd.black_list='Y' ";
//$count_where .= " and cmd.black_list='Y' ";

include "member_query.php";

$Script = "
<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;

	location.href = 'member.php?view='+sort;
}
function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		$('input[name=cmd_sdate]').attr('disabled',false);
		$('input[name=cmd_edate]').attr('disabled',false);

	}else{
		$('#cmd_sdate').attr('disabled',true);
		$('#cmd_edate').attr('disabled',true);
	}
}

function ChangeVisitDate(frm){
	if(frm.visitdate.checked){
		$('input[name=slast]').attr('disabled',false);
		$('input[name=elast]').attr('disabled',false);
	}else{
		$('input[name=slast]').attr('disabled',true);
		$('input[name=elast]').attr('disabled',true);
	}
}

function init(){

	var frm = document.searchmember;
	onLoad('$sDate','$eDate','$sDate2','$eDate2','$sDate3');";

$Script .= "
}


function deleteMemberInfo(act, code){
 	if(confirm('해당회원 정보를 정말로 삭제하시겠습니까? \\n 삭제시 관련된 모든 정보가 삭제됩니다.')){
		window.frames['iframe_act'].location.href= 'member.act.php?act='+act+'&code='+code;
 	}
}

function member_black_list_n(code){
 	if(confirm('해당회원을 불량고객에서 해제 하시겠습니까?')){
		window.frames['iframe_act'].location.href= 'member.act.php?act=member_black_list_n&code='+code;
 	}
}

function select_member_black_list_n(){
	var checked_bool = false;
	var code_obj=document.getElementsByName('code[]');
	for(i=0;i < code.length;i++){
		if(code[i].checked){
			checked_bool = true;
		}
	}
	if(!checked_bool){
		alert('한명이상 회원을 선택하셔야 합니다.');//'삭제하실 제품을 한개이상 선택하셔야 합니다'
		return;
	}else{
		if(confirm('선택하신회원을 불량고겍에서 해제 하시겠습니까?')){
			document.list_frm.submit();
		}
	}
}

</script>";

$Contents = "


<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("불량회원관리", "회원관리 > 불량회원관리 ")."</td>
  </tr>

  <tr>
  	<td>";
$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "list" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?info_type=list'>블랙회원리스트</a> <span style='padding-left:2px' class='helpcloud' help_width='410' help_height='70' help_html='사업자 정보를 작성하여 등록한 후에 해당 사업자에 관리자화면 로그인 정보를 발급해 줄 수 있습니다.<br />사업자 정보 추가 후 [목록관리] - [사용자 추가]를 통해 관리자에 계정을 발급해 주시기 바랍니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "add" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents .= "<a href='black_add.php?info_type=add'>블랙회원수동관리</a>";

						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	  </tr>
	  </table>
";

$sql = "select
			count(u.code) as member_total,
			sum(if(md.level_ix in ('7','8','9'),1,0)) as member_cnt,
			sum(if(md.level_ix = '7',1,0)) as vip_cnt,
			sum(if(md.level_ix = '8',1,0)) as vvip_cnt,
			sum(if(md.level_ix = '9',1,0)) as vvvip_cnt
		from
			common_user as u 
			inner join common_member_detail as md on (u.code = md.code)
		where
			1
		";

$mdb->query($sql);
$mdb->fetch();

$member_total = $mdb->dt[member_total];
$member_cnt = $mdb->dt[member_cnt];
$vip_cnt = $mdb->dt[vip_cnt];
$vvip_cnt = $mdb->dt[vvip_cnt];
$vvvip_cnt = $mdb->dt[vvvip_cnt];

if($member_cnt > 0){
	$member_rate = $member_cnt / $member_total * 100;
	$vip_rate = $vip_cnt / $member_cnt * 100 ;
	$vvip_rate = $vvip_cnt / $member_cnt * 100 ;
	$vvvip_rate = $vvvip_cnt / $member_cnt * 100 ;
}else{
	$vip_rate = '0' ;
	$vvip_rate = '0' ;
	$vvvip_rate = '0' ;
}

$Contents .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
<tr>
	<td colspan=8>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 블랙 회원 인원</b> ".($company_id != "" ? "[ ".$db->dt[com_name]." : ".$company_id." ]" : "")." </div>")."</td>
		  </tr>
		</table>
	</td>
</tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
<tr height='28' bgcolor='#ffffff'>
    <td width='25%' align='center' class=s_td>전체인원</td>
    <td width='25%' align='center' class='m_td'><font color='#000000'><b>블랙1</b></font></td>
    <td width='25%' align='center' class='m_td'><font color='#000000'><b>블랙2</b></font></td>
	<td width='25%' align='center' class='m_td' nowrap><font color='#000000'><b>블랙3</b></font></td>
</tr>
<tr height='28'>
	<td class='list_box_td' >".$member_cnt."(".round($member_rate,2)."%)</td>
	<td class='list_box_td'>".$vip_cnt."(".round($vip_rate,2)."%)</td>
	<td class='list_box_td'>".$vvip_cnt."(".round($vvip_rate,2)."%)</td>
	<td class='list_box_td'>".$vvvip_cnt."(".round($vvvip_rate,2)."%)</td>
</tr>
</table>
<br><br>";

$Contents .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
<tr>
	<td colspan=8>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:5px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' ><b> 블랙회원 검색</b> ".($company_id != "" ? "[ ".$db->dt[com_name]." : ".$company_id." ]" : "")." </div>")."</td>
		  </tr>
		</table>
	</td>
</tr>
</table>
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
			<input type='hidden' name=mc_ix value='".$mc_ix." '>
			<col width='18%'>
			<col width='32%'>
			<col width='18%'>
			<col width='32%'>";

if($_SESSION["admin_config"][front_multiview] == "Y"){
    $Contents .= "
			<tr>
				<td class='search_box_title' > 글로벌 회원 구분</td>
				<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
			</tr>";
}
$Contents .= "
			<tr height=27>
				<td class='search_box_title' >회원그룹 </td>
				<td class='search_box_item' colspan='3'>
					".makeGroupSelectBox($mdb,"gp_ix",$gp_ix)."
				</td>
			</tr>
			<tr height=27>
				<td class='search_box_title' >회원구분 </td>
				<td class='search_box_item' >
				<input type=radio name='mem_type' value=''  id='mem_type_'  ".CompareReturnValue("",$mem_type,"checked")." checked><label for='mem_type_'>전체</label>
				<input type=radio name='mem_type' value='M' id='mem_type_m' ".CompareReturnValue("M",$mem_type,"checked")."><label for='mem_type_m'>일반회원</label>
				<input type=radio name='mem_type' value='C' id='mem_type_c' ".CompareReturnValue("C",$mem_type,"checked")."><label for='mem_type_c'>사업자회원</label>
				<input type=radio name='mem_type' value='A' id='mem_type_s' ".CompareReturnValue("A",$mem_type,"checked")."><label for='mem_type_s'>직원(관리자)</label>
				</td>
				<td class='search_box_title' >회원타입 </td>
				<td class='search_box_item' >
					<input type=radio name='mem_div' value='' id='mem_div_'  ".CompareReturnValue("",$mem_div,"checked")." checked><label for='mem_div_'>전체</label>
					<input type=radio name='mem_div' value='S' id='mem_div_s'  ".CompareReturnValue("S",$mem_div,"checked")."><label for='mem_div_s'>셀러</label>
					<input type=radio name='mem_div' value='MD' id='mem_div_md' ".CompareReturnValue("MD",$mem_div,"checked")."><label for='mem_div_md'>MD담당자</label>
					<input type=radio name='mem_div' value='D' id='mem_div_d' ".CompareReturnValue("D",$mem_div,"checked")."><label for='mem_div_d'>기타</label>
				</td>
			</tr>
			<tr height=27>
				<td class='search_box_title'  >지역선택</td>
				<td class='search_box_item'>
				<select name='region' style='width:100px;font-size:12px;'>
					<option value=''>지역 선택</option>
					<option value='서울'  ".CompareReturnValue("서울",$region,"selected").">서울</option>
					<option value='충북'  ".CompareReturnValue("충북",$region,"selected").">충북</option>
					<option value='충남'  ".CompareReturnValue("충남",$region,"selected").">충남</option>
					<option value='전북'  ".CompareReturnValue("전북",$region,"selected").">전북</option>
					<option value='제주'  ".CompareReturnValue("제주",$region,"selected").">제주</option>
					<option value='전남'  ".CompareReturnValue("전남",$region,"selected").">전남</option>
					<option value='경북'  ".CompareReturnValue("경북",$region,"selected").">경북</option>
					<option value='경남'  ".CompareReturnValue("경남",$region,"selected").">경남</option>
					<option value='경기'  ".CompareReturnValue("경기",$region,"selected").">경기</option>
					<option value='부산'  ".CompareReturnValue("부산",$region,"selected").">부산</option>
					<option value='대구'  ".CompareReturnValue("대구",$region,"selected").">대구</option>
					<option value='인천'  ".CompareReturnValue("인천",$region,"selected").">인천</option>
					<option value='광주'  ".CompareReturnValue("광주",$region,"selected").">광주</option>
					<option value='대전'  ".CompareReturnValue("대전",$region,"selected").">대전</option>
					<option value='울산'  ".CompareReturnValue("울산",$region,"selected").">울산</option>
					<option value='강원'  ".CompareReturnValue("강원",$region,"selected").">강원</option>
				</select>
				</td>
				<td class='search_box_title' >레벨</td>
				<td class='search_box_item'>
					<input type=radio name='vip_yn' value='' id='vip_'  ".CompareReturnValue("",$vip_yn,"checked")." checked><label for='vip_'>전체</label>
					<input type=radio name='vip_yn' value='4' id='vip_1'  ".CompareReturnValue("4",$vip_yn,"checked")."><label for='vip_1'>VIP</label>
					<input type=radio name='vip_yn' value='5' id='vip_2' ".CompareReturnValue("5",$vip_yn,"checked")."><label for='vip_2'>VVIP</label>
					<input type=radio name='vip_yn' value='6' id='vip_3' ".CompareReturnValue("6",$vip_yn,"checked")."><label for='vip_3'>VVVIP</label>
				</td>
			</tr>

			<tr height=27>
				<td class='search_box_title' >이메일 발송여부 </td>
				<td class='search_box_item'  >
					<input type=radio name='mailsend_yn' value='A' id='mailsend_a'  ".CompareReturnValue("A",$mailsend_yn,"checked")." checked><label for='mailsend_a'>전체</label>
					 <input type=radio name='mailsend_yn' value='1' id='mailsend_y'  ".CompareReturnValue("1",$mailsend_yn,"checked")."><label for='mailsend_y'>수신회원만</label><input type=radio name='mailsend_yn' value='0' id='mailsend_n' ".CompareReturnValue("0",$mailsend_yn,"checked")."><label for='mailsend_n'>수신거부회원</label>
				</td>
				<td class='search_box_title' >SMS 발송여부 </td>
				<td class='search_box_item'  >
					<input type=radio name='smssend_yn' value='A' id='smssend_a'  ".CompareReturnValue("A",$smssend_yn,"checked")." checked><label for='smssend_a'>전체</label>
					<input type=radio name='smssend_yn' value='1' id='smssend_y'  ".CompareReturnValue("1",$smssend_yn,"checked")."><label for='smssend_y'>수신회원만</label>
					<input type=radio name='smssend_yn' value='0' id='smssend_n' ".CompareReturnValue("0",$smssend_yn,"checked")."><label for='smssend_n'>수신거부회원</label>
				</td>
			</tr>
			<tr height=27>
				<td class='search_box_title' >마일리지 보유</td>
				<td class='search_box_item'  >
					<input type=radio name='reserve_yn' value='' id='reserve_'  ".CompareReturnValue("",$reserve_yn,"checked")." checked><label for='reserve_'>전체</label>
					<input type=radio name='reserve_yn' value='1' id='reserve_y'  ".CompareReturnValue("1",$reserve_yn,"checked")."><label for='reserve_y'>보유</label>
					<input type=radio name='reserve_yn' value='0' id='reserve_n' ".CompareReturnValue("0",$reserve_yn,"checked")."><label for='reserve_n'>미보유</label>
				</td>
				<td class='search_box_title' >포인트 보유 </td>
				<td class='search_box_item'  >
					<input type=radio name='point_yn' value='A' id='point_'  ".CompareReturnValue("",$point_yn,"checked")." checked><label for='point_'>전체</label>
					<input type=radio name='point_yn' value='1' id='point_y'  ".CompareReturnValue("1",$point_yn,"checked")."><label for='point_y'>보유</label>
					<input type=radio name='point_yn' value='0' id='point_n' ".CompareReturnValue("0",$point_yn,"checked")."><label for='point_n'>미보유</label>
				</td>
			</tr>
			<tr height=27>
				<td class='search_box_title' ><label for='regdate'>가입일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
				<td class='search_box_item'  colspan=3 >
					".search_date('cmd_sdate','cmd_edate',$cmd_sdate,$cmd_edate)."
				</td>
			</tr>
			<tr height=27>
				<td class='search_box_title' ><label for='visitdate'>최근방문일</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);' ".CompareReturnValue("1",$visitdate,"checked")."></td>
				<td class='search_box_item'  colspan=3  >
					".search_date('slast','elast',$slast,$elast)."
				</td>
			</tr>

			<tr height=27>
				<td class='search_box_title' >조건검색 </td>
				<td class='search_box_item' colspan='3'>
					<table cellpadding=0 cellspacing=0 width=100%>
						<col width='80'>
						<col width='*'>
						<tr>
							<td>
								<select name=search_type>
									<option value='cmd.name' ".CompareReturnValue("cmd.name",$search_type,"selected").">고객명</option>
									<option value='cu.id' ".CompareReturnValue("cu.id",$search_type,"selected").">아이디</option>
									<option value='cmd.tel' ".CompareReturnValue("cmd.tel",$search_type,"selected").">전화번호</option>
									<option value='cmd.pcs' ".CompareReturnValue("cmd.pcs",$search_type,"selected").">휴대전화</option>
									<option value='ccd.com_name' ".CompareReturnValue("ccd.com_name",$search_type,"selected").">회사명</option>
									<option value='ccd.com_phone' ".CompareReturnValue("ccd.com_phone",$search_type,"selected").">회사전화</option>
									<option value='ccd.com_fax' ".CompareReturnValue("ccd.com_fax",$search_type,"selected").">회사팩스</option>
									<option value='cmd.mail' ".CompareReturnValue("cmd.mail",$search_type,"selected").">이메일</option>
									<option value='cmd.addr1' ".CompareReturnValue("cmd.addr1",$search_type,"selected").">주소</option>
								</select>
							</td>
							<td>
								<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:250px;font-size:12px;padding:1px;' >
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
<tr height=50>
	<td style='padding:10px 20px 0 20px' colspan=4 align=center><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' align=absmiddle  ></td>
</tr>
</table><br>
</form>";

$Contents .= "
<form name='list_frm' action='member.act.php' target='act'>
<input type='hidden' name='act' value='select_member_black_list_n'>
<input type='hidden' name='code' id='code'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
<tr height=30 >
	<td colspan=5>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
		$Contents .= "
		<a href='javascript:listAction(document.list_frm);'><img src='../images/".$admininfo["language"]."/btn_selected_sms.gif' align=absmiddle  ></a>";
	}else{
		$Contents .= "
		<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_selected_sms.gif' align=absmiddle ></a>";
	}
	$Contents .= "
	<td colspan=5 align=right>";
	if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	//$Contents .= "<a href='member_excel2003.php?page_type=member_black_list&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	$Contents .= "<a href='javascript:ig_excel_dn_chk(\"member_excel2003.php?page_type=member_black_list&".$QUERY_STRING."\");'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}

	$Contents .= "
	</td>
</tr>
</table>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
<tr height='28' bgcolor='#ffffff'>
	<td width='10' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
	<td width='5%' align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
	<td width='6%' align='center' class='m_td'><font color='#000000'><b>회원구분</b></font></td>
	<td width='6%' align='center' class='m_td'><font color='#000000'><b>회원그룹</b></font></td>
	<td width='6%' align='center' class='m_td'><font color='#000000'><b>회원타입</b></font></td>

	<td width='6%' align='center' class=m_td><font color='#000000'><b>이름</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>아이디</b></font></td>
	<td width='15%' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
	<td width='7%' align='center' class=m_td><font color='#000000'><b>불량설정일</b></font></td>
	<td width='5%' align='center' class=m_td><font color='#000000'><b>레벨구분</b></font></td>
	<td width='7%' align='center' class=m_td><font color='#000000'><b>최근방문일</b></font></td>";

	if($admininfo[mall_type] != "H"){
	$Contents .= "
	<td width='8%' align='center' class=m_td><font color='#000000'><b>마일리지</b></font></td>
	<td width='8%' align='center' class=m_td><font color='#000000'><b>포인트</b></font></td>";
	}
	$Contents .= "
	<td width='100' align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
</tr>";


	for ($i = 0; $i < $db->total; $i++){

		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid_ = '".$db->dt[code]."' and state in (1,2,5,6,7)");
		}else{
			$mdb->query("SELECT IFNULL(sum(reserve),'-') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in (1,2,5,6,7)");
		}
		$mdb->fetch(0);
		$reserve_sum = number_format($mdb->dt[reserve_sum]);

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT sum(reserve) as point_sum FROM ".TBL_SHOP_POINT_INFO." WHERE uid_ = '".$db->dt[code]."' and state in (0,1,2,9)");
		}else{
			$mdb->query("SELECT IFNULL(sum(reserve),'-') as point_sum FROM ".TBL_SHOP_POINT_INFO." WHERE uid = '".$db->dt[code]."' and state in (0,1,2,9)");
		}
		$mdb->fetch(0);
		$point_sum = number_format($mdb->dt[point_sum]);

		if($db->dt[is_id_auth] != "Y"){
			$is_id_auth = "미인증";
		}else{
			$is_id_auth = "";
		}
//적립상태구분값(0:대기,1:적립,2:사용,9:취소)

		switch($db->dt[mem_type]){

		case "M":
			$mem_type = "일반회원";
			break;
		case "C":
			$mem_type = "기업".($db->dt[com_name] != "" ? "(".$db->dt[com_name].")":"");
			break;
		case "A":
			$mem_type = "직원(관리자)";
			break;
		}

		switch($db->dt[mem_div]){
			case "MD":
			$mem_div = "MD담당자";
			break;
			case "S":
			$mem_div = "셀러";
			break;
			case "D":
			$mem_div = "기타";
			break;
		}

		switch($db->dt[level_ix]){

		case "7":
			$level_type = "블랙1";
			break;
		case "8":
			$level_type = "블랙2";
			break;
		case "9":
			$level_type = "블랙3";
			break;
		}

		if($db->dt[level_change_date]){
			$level_change_array = explode(" ",$db->dt[level_change_date]);
			$level_change_date = $level_change_array[0];
		}else{
			$level_change_date = "0000-00-00";
		}

		$Contents = $Contents."
		  <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='pointer'\" onMouseOut=\"this.style.backgroundColor=''\">
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
			<td class='list_box_td' onClick=\"PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\">".$no."</td>
			<td class='list_box_td' onClick=\"PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\" nowrap>국내</td>
			<td class='list_box_td' onClick=\"PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\"><span title='".$db->dt[organization_name]."'>".$db->dt[gp_name]."</span></td>
			
			<td class='list_box_td' onClick=\"PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\" nowrap>".$mem_type."</td>
			<td class='list_box_td point' onClick=\"PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\" nowrap>".wel_masking_seLen(Black_list_check($db->dt[code],$db->dt[name]), 1, 1)."</td>
			<td class='list_box_td' onClick=\"PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\">".$db->dt[id]."</td>
			<td class='list_box_td' onClick=\"PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\">".wel_masking("E", $db->dt[mail])."<font color=red> ".$is_id_auth."</font></td>
			<td class='list_box_td' onClick=\"PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\">".$level_change_date."</td>
			<td class='list_box_td' onClick=\"PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\">".$level_type."</td>
			<td class='list_box_td' onClick=\"PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\">".$db->dt[regdate]."</td>";
			if($admininfo[mall_type] != "H"){
			$Contents .= "
			<td class='list_box_td ctr point' ><a href=\"javascript:PoPWindow('reserve.pop.php?code=".$db->dt[code]."',650,700,'reserve_pop')\">".number_format($db->dt[mileage])."</a></td>
			<td class='list_box_td ctr point' ><a href=\"javascript:PoPWindow('point.pop.php?code=".$db->dt[code]."',650,700,'reserve_pop')\">".number_format($db->dt[point])."</a></td>";
			}
			$Contents .= "
			<td class='list_box_td ctr'  style='padding:5px;' nowrap>";
			if($update_auth){
				//$Contents .= "<img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_view.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_view')\" style='cursor:pointer;' alt='고객상담' title='고객상담'/> ";
				$Contents .= "<img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_view.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_view')\" alt='고객상담' title='고객상담'/>";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle alt='고객상담' title='고객상담' ></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .= "<img src='../images/".$admininfo["language"]."/btn_clear.gif' border=0 align=absmiddle onClick=\"member_black_list_n('".$db->dt[code]."')\" />";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_clear.gif' border=0 align=absmiddle></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
				 $Contents .= "
				 <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\" alt='문자발송' title='문자발송'>
				 <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\" alt='이메일발송' title='이메일발송'>
				 ";
			}else{
				$Contents .= "
				 <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle alt='문자발송' title='문자발송'></a>
				 <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle alt='문자발송' title='이메일발송'></a>
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

<table width=100%>
<tr>
	<td><a href=\"javascript:select_member_black_list_n();\">선택회원 불량회원해제</a><span style='padding-left:2px' class='helpcloud' help_width='300' help_height='20' help_html='불량회원으로 등록된 사용자를 해제 할 수 있습니다.'><img src='/admin/images/icon_q.gif' /></span></td>
	<td align=right>
	".$str_page_bar."
	<!--img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\" /-->
	<!--div style='cursor:pointer' onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\">회원수동등록</div-->
	</td>
</tr>
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
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

//$Contents .= HelpBox("불량고객관리", $help_text,'70');


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";
$P->strLeftMenu = member_menu();
$P->Navigation = "회원관리 > 불량고객관리";
$P->title = "불량고객관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


//	웰숲 우클릭 방지
include_once("../order/wel_drag.php");
?>



<script type="text/javascript">
	//	wel_ 새벽시간(23시~07시)이나 휴무일 등 업무시간 외 다운로드시 검수 member_excel2003
	function ig_excel_dn_chk(s_val_Data) {
		//console.log(s_val_Data);
		var ig_now = new Date();   //현재시간
		var ig_hour = ig_now.getHours();   //현재 시간 중 시간.




			//	새벽시간(23시~07시), 휴무일(일, 토)
		//if(Number(ig_hour) >= "23" || Number(ig_hour) <= "7" || Number(ig_now.getDay()) == "0" || Number(ig_now.getDay()) == "6") {
			var ig_inputString = prompt('사유를 간략하게 입력하세요.\r\n(20자 이내(띄어쓰기포함), 특수문자 제외)');

			if(ig_inputString != null && ig_inputString.trim() != "") {
				//	엑셀다운로드 진행

					var str_length = ig_inputString.length;		// 전체길이

					if(str_length > "20") {
						alert("사유가 20자 이상 입니다.");
						return false;
					} else {
						var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

							if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

								var str_PW_length = ig_inputString_PW.length;		// 전체길이

								if(str_PW_length > "15") {
									alert("비밀번호를 15자 이하로 해주세요.");
									return false;
								} else {
									location.href = s_val_Data+"&irs="+ig_inputString+"&ipw="+ig_inputString_PW;
								}

							} else {
								alert("비밀번호를 입력해 주세요.");
								return false;
							}
					}


			} else {
				alert("사유를 입력하세요");
				return false;
			}
		/*} else {
			//	일반 업무때 다운로드
			var ig_inputString_PW = prompt('파일 비밀번호를 지정해 주세요.\r\n(15자 이하, 영문/숫자만 사용, 공백사용금지)');

				if(ig_inputString_PW != null && ig_inputString_PW.trim() != "") {

					var str_PW_length = ig_inputString_PW.length;		// 전체길이

					if(str_PW_length > "15") {
						alert("비밀번호를 15자 이하로 해주세요.");
						return false;
					} else {
						location.href = s_val_Data+"&ipw="+ig_inputString_PW;
					}

				} else {
					alert("비밀번호를 입력해 주세요.");
					return false;
				}
		}*/



	}
</script>