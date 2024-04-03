<?
include("../class/layout.class");
//auth(9);

$db = new Database;
$mdb = new Database;
//$db->debug = true;//페이지에서 오류가 없는데 쿼리를 찍기에 주석처리함 kbk 13/02/04
$update_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U");
$delete_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D");
$create_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C");
$excel_auth = checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E");

$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));



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

if($mode == "search"){
    include "member_query.php";
}

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
		$('input[name=cmd_sdate]').attr('disabled',true);
		$('input[name=cmd_edate]').attr('disabled',true);
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


function ChangeBirDate(frm){
	if(frm.bir.checked){
		frm.birYY.disabled = false;
		frm.birMM.disabled = false;
		frm.birDD.disabled = false;
	}else{
		frm.birYY.disabled = true;
		frm.birMM.disabled = true;
		frm.birDD.disabled = true;
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

function SleepSubmit(frm){
	
	if(frm.update_type.value == 1){
		
		if(!confirm('검색회원 휴면전환 하시겠습니까?')){return false;}//
		
		//alert(frm.update_kind.value);
	}else if(frm.update_type.value == 2){
		var code_checked_bool = false;
		for(i=0;i < frm.code.length;i++){
			if(frm.code[i].checked){
				code_checked_bool = true;
			}
			//	frm.code[i].checked = false;
		}
		if(!code_checked_bool){
			alert('선택된 회원이 없습니다. 휴면 변경하고자 하는 회원을 선택 후 진행해 주세요.');//
			return false;
		}
	}else{
	        alert('전환 방식을 선택 해 주세요');//
			return false;
	}
}
</script>";

$Contents = "


<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("전체회원관리", "개인정보관리 > 휴면회원관리 ")."</td>
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
					<table id='tab_01' ".(($info_type == "member" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?info_type=member'>일반회원</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "sleep_member" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents .= "<a href='sleep_member.php?info_type=sleep_member'>휴면회원</a>";

						$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' >
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents .= "<a href='sleep_member_log.php'>휴면이력</a>";

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
            <input type='hidden' name=mc_ix value='".$mc_ix." '>
			<input type='hidden' name='info_type' value='".$info_type."' />
			<input type='hidden' name=mode value='search'>
		    <col width='12%'>
			<col width='*'>";

if($_SESSION["admin_config"][front_multiview] == "Y"){
    $Contents .= "
					<tr>
						<td class='search_box_title' > 글로벌 회원 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." </td>
					</tr>";
}
$Contents .= "
				<tr height=27>
				  <td class='search_box_title' >조건검색 </td>
				  <td class='search_box_item'>
						<table cellpadding=0 cellspacing=0 width=100%>
							<col width='80'>
							<col width='*'>
							<tr>
								<td>
								  <select name=search_type>
										<option value='name' ".CompareReturnValue("name",$search_type,"selected").">고객명</option>
										<option value='id' ".CompareReturnValue("id",$search_type,"selected").">아이디</option>
										
										<option value='tel' ".CompareReturnValue("tel",$search_type,"selected").">전화번호</option>
										<option value='pcs' ".CompareReturnValue("pcs",$search_type,"selected").">휴대전화</option>
										<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">회사명</option>
										<option value='com_phone' ".CompareReturnValue("com_phone",$search_type,"selected").">회사전화</option>
										<option value='com_fax' ".CompareReturnValue("com_fax",$search_type,"selected").">회사팩스</option>
										<option value='mail' ".CompareReturnValue("mail",$search_type,"selected").">이메일</option>
										<option value='addr1' ".CompareReturnValue("addr1",$search_type,"selected").">주소</option>
								  </select>
								</td>
								<td>
									<input type=text name='search_text' class='textbox' value='".$search_text."' style='width:70%;font-size:12px;padding:1px;' >
								</td>
							</tr>
						</table>
				  </td>
		      <td class='search_box_title' >회원그룹 </td>
		      <td class='search_box_item' >
		      ".makeGroupSelectBox($mdb,"gp_ix",$gp_ix)."
		      </td>
				</tr>
				<tr height=27>
				<!--
		      <td class='search_box_title' >국내/해외 </td>
		      <td class='search_box_item' >
				<input type=radio name='nationality' value=''  id='nationality_'  ".CompareReturnValue("",$nationality,"checked")." checked><label for='nationality_'>전체</label>
				<input type=radio name='nationality' value='I' id='nationality_I' ".CompareReturnValue("I",$nationality,"checked")."><label for=''nationality_I'>국내회원</label>
				<input type=radio name='nationality' value='O' id='nationality_O' ".CompareReturnValue("O",$nationality,"checked")."><label for='nationality_O'>해외회원</label>
				<input type=radio name='nationality' value='D' id='nationality_D' ".CompareReturnValue("D",$nationality,"checked")."><label for='nationality_D'>기타회원</label>
		      </td>
		      -->
		      <td class='search_box_title' >회원구분 </td>
		      <td class='search_box_item' colspan='3' >
				<input type=radio name='mem_type' value=''  id='mem_type_'  ".CompareReturnValue("",$mem_type,"checked")." checked><label for='mem_type_'>전체</label>
				<input type=radio name='mem_type' value='M' id='mem_type_m' ".CompareReturnValue("M",$mem_type,"checked")."><label for='mem_type_m'>일반회원</label>
				<input type=radio name='mem_type' value='C' id='mem_type_c' ".CompareReturnValue("C",$mem_type,"checked")."><label for='mem_type_c'>사업자회원</label>
				<input type=radio name='mem_type' value='A' id='mem_type_s' ".CompareReturnValue("S",$mem_type,"checked")."><label for='mem_type_s'>직원(관리자)</label>
		      </td>
		    </tr>
			<tr  height=27>
		      <td class='search_box_title'  >지역선택</td>
		      <td class='search_box_item'>
		      <select name='region' style='width:140px;font-size:12px;'>
                        <option value=''>-- 선택 --</option>
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
			  <td class='search_box_title' >회원타입 </td>
				<td class='search_box_item' >
				  <input type=radio name='mem_div' value='' id='sex_all'  ".CompareReturnValue("",$mem_div,"checked")." checked><label for='sex_all'>전체</label>
				  <input type=radio name='mem_div' value='S' id='sex_man'  ".CompareReturnValue("S",$mem_div,"checked")."><label for='sex_man'>셀러</label>
				  <input type=radio name='mem_div' value='MD' id='sex_women' ".CompareReturnValue("MD",$mem_div,"checked")."><label for='sex_women'>MD담당자</label>
				  <input type=radio name='mem_div' value='D' id='sex_women' ".CompareReturnValue("D",$mem_div,"checked")."><label for='sex_women'>기타</label>
				</td>
		    </tr>
		    <!--tr height=27>
			<td class='search_box_title' >연령</td>
		      <td class='search_box_item' >
		      <select name='age' >
                        <option value=''> -- 선택 -- </option>
                        <option value='10' ".CompareReturnValue("10",$age,"selected").">10대</option>
                        <option value='20' ".CompareReturnValue("20",$age,"selected").">20대</option>
                        <option value='30' ".CompareReturnValue("30",$age,"selected").">30대</option>
                        <option value='40' ".CompareReturnValue("40",$age,"selected").">40대</option>
                        <option value='50' ".CompareReturnValue("50",$age,"selected").">50대</option>
                        <option value='60' ".CompareReturnValue("60",$age,"selected").">60대</option>
                        </select>
		      </td>
				<td class='search_box_title' >
					<label for='bir'>생일</label><input type='checkbox' name='bir' id='bir' value='1' onclick='ChangeBirDate(document.searchmember);' ".CompareReturnValue("1",$bir,"checked").">
				</td>
				<td class='search_box_item' >
				 <SELECT onchange=javascript:onChangeDate(this.form.birYY,this.form.birMM,this.form.birDD) name=birYY ></SELECT> 년
				 <SELECT onchange=javascript:onChangeDate(this.form.birYY,this.form.birMM,this.form.birDD) name=birMM></SELECT> 월
				 <SELECT name=birDD></SELECT> 일
				</td>
		    </tr-->
		    
		    <tr height=27>
		      <td class='search_box_title' >이메일 발송여부 </td>
		      <td class='search_box_item'  >
			   <input type=radio name='mailsend_yn' value='A' id='mailsend_a'  ".CompareReturnValue("A",$mailsend_yn,"checked")." checked><label for='mailsend_a'>전체</label>
		       <input type=radio name='mailsend_yn' value='1' id='mailsend_y'  ".CompareReturnValue("1",$mailsend_yn,"checked")."><label for='mailsend_y'>수신회원만</label><input type=radio name='mailsend_yn' value='0' id='mailsend_n' ".CompareReturnValue("0",$mailsend_yn,"checked")."><label for='mailsend_n'>수신거부회원포함</label>
		      </td>
		      <td class='search_box_title' >SMS 발송여부 </td>
		      <td class='search_box_item'  >
				  <input type=radio name='smssend_yn' value='A' id='smssend_a'  ".CompareReturnValue("A",$smssend_yn,"checked")." checked><label for='smssend_a'>전체</label>
				  <input type=radio name='smssend_yn' value='1' id='smssend_y'  ".CompareReturnValue("1",$smssend_yn,"checked")."><label for='smssend_y'>수신회원만</label>
				  <input type=radio name='smssend_yn' value='0' id='smssend_n' ".CompareReturnValue("0",$smssend_yn,"checked")."><label for='smssend_n'>수신거부회원포함</label>
		      </td>
		    </tr>
		    ";

$vdate = date("Y-m-d", time());
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

 $Contents .= "
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
		      <td class='search_box_title' >마일리지(M) 보유</td>
		      <td class='search_box_item' colspan='3' >
			   <input type=radio name='mileage' value='' id='reserve_'  ".CompareReturnValue("",$mileage,"checked")." checked><label for='reserve_'>전체</label>
		       <input type=radio name='mileage' value='1' id='reserve_y'  ".CompareReturnValue("1",$mileage,"checked")."><label for='reserve_y'>보유</label>
			   <input type=radio name='mileage' value='2' id='reserve_n' ".CompareReturnValue("2",$mileage,"checked")."><label for='reserve_n'>미보유</label>
		      </td>
		      <!--
		      <td class='search_box_title' >포인트(P) 보유 </td>
		      <td class='search_box_item'  >
				  <input type=radio name='point' value='' id='point_'  ".CompareReturnValue("",$point,"checked")." checked><label for='point_'>전체</label>
				  <input type=radio name='point' value='1' id='point_y'  ".CompareReturnValue("1",$point,"checked")."><label for='point_y'>보유</label>
				  <input type=radio name='point' value='2' id='point_n' ".CompareReturnValue("2",$point,"checked")."><label for='point_n'>미보유</label>
		      </td>
		      -->
		    </tr>
			<tr height=27>
				<td class='search_box_title' >가입경로</td>
				<td class='search_box_item'  colspan=3>
					<input type=radio name='agent_type' value='' id='agent_type_'  ".CompareReturnValue("",$agent_type,"checked")." checked><label for='agent_type_'>전체</label>
					<input type=radio name='agent_type' value='W' id='agent_type_W'  ".CompareReturnValue("W",$agent_type,"checked")."><label for='agent_type_W'>PC(web)</label>
					<input type=radio name='agent_type' value='M' id='agent_type_M' ".CompareReturnValue("M",$agent_type,"checked")."><label for='agent_type_M'>모바일</label>
				</td>
				 
			</tr>
			<tr height=27>
				<td class='search_box_title' >휴면기간검색</td>
				<td class='search_box_item' colspan='3' >
					<span>최근방문일 </span>
					<input type=text name='sleep_in_date' class='textbox' value='".$sleep_in_date."' size='5' style='font-size:12px;padding:1px;' >
					<span>일 부터</span>
					<input type=text name='sleep_out_date' class='textbox' value='".$sleep_out_date."' size='5' style='font-size:12px;padding:1px;' >
					<span>일 까지</span>
					<span style='color:red'>(* 0 입력 시 제한 없이 검색)</span>
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
</table><br></form>

<form name='list_frm'  method='POST' onsubmit='return SleepSubmit(this);' action='sleep_member.act.php'  target='act'>
";
if($info_type == 'sleep_member'){
$Contents .= "
<input type= 'hidden' name='act' value='move_member' />";
}else{
$Contents .= "
<input type= 'hidden' name='act' value='move_sleep' />";
}
$Contents .= "
<input type='hidden' name='code[]' id='code'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
  <tr height=30 >
  	<td colspan=5>";

    if($create_auth){
        $Contents .= "
        <a href='javascript:listAction(document.list_frm);'><img src='../images/".$admininfo["language"]."/btn_selected_sms.gif' align=absmiddle  ></a>";
    }else{
        $Contents .= "
        <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_selected_sms.gif' align=absmiddle ></a>";
    }
    $Contents .= "
    </td>
  	<td colspan=5 align=right>";
	if($excel_auth){
	//$Contents .= "<a href='sleep_member_excel.php?".$QUERY_STRING."&mode=excel'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	$Contents .= "<a href='javascript:ig_excel_dn_chk(\"sleep_member_excel.php?".$QUERY_STRING."&mode=excel\");'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
	}
	$Contents .= "
	</td>
  </tr>
</table>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='27' bgcolor='#ffffff'>
    ";
	if($info_type =='sleep_member'){
	$Contents .= "
	<td width='10' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='3%' align='center' class='m_td'><font color='#000000'><b>순번</b></font></td>
    <td width='8%' align='center' class='m_td'><font color='#000000'><b>그룹</b></font></td>
	<td width='5%' align='center' class='m_td' nowrap><font color='#000000'><b>국내/해외</b></font></td>
    <td width='8%' align='center' class='m_td' nowrap><font color='#000000'><b>회원구분</b></font></td>
	<td width='10%' align='center' class='m_td' nowrap><font color='#000000'><b>회원타입</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>이름</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>아이디</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>미이용일</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>상태변경</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>변경일</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>관리자</b></font></td>
	<td width='10%' align='center' class=e_td><font color='#000000'><b>최근이용일</b></font></td>";
	}else{
	$Contents .= "
	<td width='10' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='3%' align='center' class='m_td'><font color='#000000'><b>순번</b></font></td>
    <td width='8%' align='center' class='m_td'><font color='#000000'><b>그룹</b></font></td>
	<td width='5%' align='center' class='m_td' nowrap><font color='#000000'><b>국내/해외</b></font></td>
    <td width='8%' align='center' class='m_td' nowrap><font color='#000000'><b>회원구분</b></font></td>
	<td width='6%' align='center' class='m_td' nowrap><font color='#000000'><b>회원타입</b></font></td>
   <!-- <td width='6%' align='center' class=m_td nowrap><font color='#000000'><b>승인여부</b></font></td>-->
    <td width='13%' align='center' class=m_td><font color='#000000'><b>이름</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>아이디</b></font></td>
    <td width='20%' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>최근방문일(미이용)</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>로긴수</b></font></td>
	<!--td width='6%' align='center' class=m_td><font color='#000000'><b>계정상태</b></font></td-->";
	if($admininfo[mall_type] != "H"){
	$Contents .= "
    <td width='6%' align='center' class=m_td><font color='#000000'><b>마일리지</b></font></td>
	<!--<td width='6%' align='center' class=e_td><font color='#000000'><b>포인트</b></font></td>-->";
	}
	$Contents .= "
    <!--td width='16%' align='center' class=e_td><font color='#000000'><b>관리</b></font></td-->";
	}
	$Contents .= "
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

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}else{
			$mdb->query("SELECT gp_name FROM ".TBL_SHOP_GROUPINFO." WHERE gp_ix = '".$db->dt[gp_ix]."'  ");
		}
		$mdb->fetch(0);
		$gp_name = $mdb->dt[gp_name];


/*		// 회원 마일리지 포인트 값은 common_user 에 mileage , point 에서 가져오면 됩니다. 새로운 구조로 변경  2013-06-20 이학봉
		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid_ = '".$db->dt[code]."' and state in ('1','2','5','6','7')");
		}else{
			$sql = "SELECT IFNULL(sum(reserve),'0') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in ('1','2','5','6','7')";
			//echo $sql;
			$mdb->query($sql);
		}
		$mdb->fetch(0);
		$reserve_sum = number_format($mdb->dt[reserve_sum]);
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
		/*
		switch($db->dt[nationality]){
			case "I":
            $nationality = "국내";
            break;
			case "O":
            $nationality = "해외";
            break;
			case "D":
            $nationality = "기타";
            break;
		}
		*/
        $nationality = GetDisplayDivision($db->dt['mall_ix'], "text");
		$visit_delay = intval((strtotime(date('Ymd', strtotime("+1 day")))-strtotime($db->dt[last])) / 86400); //date('d',strtotime(date('Ymd'))-$db->dt[last]);
					
        $Contents = $Contents."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">";
            
			if($info_type =='sleep_member'){

			switch($db->dt[status]){
				case "A":
				$sleep_status = "관리자 일관변경";
				break;
				case "S":
				$sleep_status = "시스템 자동";
				break;
			}
			$Contents .= "
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
            <td class='list_box_td' >".$no."</td>
            <td class='list_box_td' style='padding:0px 5px;' nowrap><span title='".$db->dt[organization_name]."'>".$gp_name."</span></td>
			<td class='list_box_td' nowrap>".$nationality."</td>
            <td class='list_box_td' nowrap>".$mem_type."</td>
			<td class='list_box_td' nowrap>".$mem_div."</td>
            <td class='list_box_td point' nowrap>".wel_masking_seLen(Black_list_check($db->dt[code],$db->dt[name]), 1, 1)."</td>
            <td class='list_box_td' >".wel_masking("I",$db->dt[id])."</a></td>
            <td class='list_box_td' ><span style='color:red;'>(".$visit_delay.")</span></td>
            <td class='list_box_td' >".$sleep_status."</span> </td>
            <td class='list_box_td' >".$db->dt[sleep_date]."</td>";
			if($db->dt[charger_ix]){
				$Contents .= "
			<td class='list_box_td' >".getChargerinfo($db->dt[charger_ix],'name')."<br> ( ".getChargerinfo($db->dt[charger_ix],'id')." ) </td>";
			}else{
				$Contents .= "
			<td class='list_box_td' >-</td>";
			}
			$Contents .= "
			<td class='list_box_td' >".date('Y.m.d',strtotime($db->dt[last]))."</td>";
			}else{
			$Contents .= "
			<td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
            <td class='list_box_td' >".$no."</td>
            <td class='list_box_td' style='padding:0px 5px;' nowrap><span title='".$db->dt[organization_name]."'>".$gp_name."</span></td>
			<td class='list_box_td' nowrap>".$nationality."</td>
            <td class='list_box_td' nowrap>".$mem_type."</td>
			<td class='list_box_td' nowrap>".$mem_div."</td>
            <!--<td class='list_box_td' >".$authorized."</td>-->
            <td class='list_box_td point' nowrap><a href=\"javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\" style='cursor:pointer' >".wel_masking_seLen(Black_list_check($db->dt[code],$db->dt[name]), 1, 1)."</td>
            <td class='list_box_td' ><a href=\"javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')\" style='cursor:pointer' >".wel_masking("I",$db->dt[id])."</a></td>
            <td class='list_box_td' >				
				".($_SESSION[admininfo][personal_information] == '0' ? "".getTextMarking($db->dt[mail])."" : "".wel_masking("E", $db->dt[mail])."" )."
			<font color=red> ".$is_id_auth."</font></td>
            <td class='list_box_td' >".date('Y.m.d',strtotime($db->dt[last]))." <span style='color:red;'>(".$visit_delay.")</span> </td>
            <td class='list_box_td' >".$db->dt[visit]."</td>
			<!--td class='list_box_td' >".$sleep_status."</td-->";
			
			if($admininfo[mall_type] != "H"){
			$Contents .= "
            <td class='list_box_td ctr point' ><a href=\"javascript:PoPWindow('reserve.pop.php?code=".$db->dt[code]."',650,700,'reserve_pop')\">".$db->dt[mileage]."</a></td>
			<!--<td class='list_box_td ctr point' ><a href=\"javascript:PoPWindow('point_new.pop.php?code=".$db->dt[code]."',650,700,'point_new_pop')\">".$db->dt[point]."</a></td>-->";
			}
			}
			$Contents .= "
            <!--td class='list_box_td ctr'  style='padding:5px;' nowrap>";
			if($update_auth){
				$Contents .= "<img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_view.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_view')\" style='cursor:pointer;' alt='고객상담' title='고객상담'/> ";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btn_crm.gif' border=0 align=absmiddle alt='고객상담' title='고객상담' ></a> ";
			}

			if($update_auth && $_SESSION[admininfo][personal_information] != '0'){ //personal_information 개인정보 관리 권한 값
				$Contents .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\" style='cursor:pointer;' alt='수정' title='수정'/> ";
			}else{
				$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle alt='수정' title='수정' ></a> ";
			}

			//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
			if($delete_auth && $_SESSION[admininfo][personal_information] != '0'){
				$Contents .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" style='cursor:pointer;' alt='삭제' title='삭제'/> ";
			}else{
				$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle ></a> ";
			}
            if($create_auth){
			     $Contents .= "
        	     <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\" style='cursor:pointer;' alt='문자발송' title='문자발송'>
        	     <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\" style='cursor:pointer;' alt='이메일발송' title='이메일발송'>
                 ";
            }else{
                $Contents .= "
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle alt='문자발송' title='문자발송'></a>
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle alt='이메일발송' title='이메일발송'></a>
                 ";
            }
            $Contents .= "
			 </td-->
  </tr>";

	}

if (!$db->total){

$Contents = $Contents."
  <tr height=50>
    <td colspan='15' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>";
}

$Contents .= "
</table>

<table width=100%>
	<tr>
		<td><div style='width:100%;text-align:right;padding:5px 0px;'>".$str_page_bar."</div></td>
		<td align=right>
		<!--img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\" /-->
		<!--div style='cursor:pointer' onClick=\"PopSWindow('member_info.php?act=insert',900,710,'member_info_insert')\">회원수동등록</div-->
	</td>
</tr>
</table>";

$help_text = "

<table cellpadding=0 cellspacing=0 class='small' width=100% >
	<col width=8>
	<col width=*>
	<tr>
	<td colspan='2'>
	<select name='update_type' onChange='view_member_num(this,\"$total\")'>		
		<option value=''>전환방식 선택</option>
		<option value='2'>선택한회원 전체에게</option>
		<option value='1'>검색한 회원 전체에게</option>
	</select>
	</td>
	</tr>";
	if($info_type == 'sleep_member'){
	$help_text .= "
	<tr>
		<td colspan='2' style='padding-top:10px;'>
			<table width='50%' border='0' cellpadding='0' cellspacing='0' align='' class='list_table_box'>
				<col width=20%>
				<col width=*>
				<tr>
					<td class='list_box_td point' >
						일반회원 변경 사유
					</td>
					<td>
						<textarea rows='3' cols='10' name='message' style='margin:0 10px; width:95%'></textarea>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	";
	}else{
	$help_text .= "
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >일반 회원을 접속 이용기간에 따라 휴면 회원으로 변경 할 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >휴면회원으로 전환시 '정보통신망 이용조치 및 정보보호 등에 관한 법률 시행령 제 16조' 에 의하여 회원정보를 이용 할 수 없습니다.(회원관리,CRM)</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원이 이용 또는 고객의 요청에 따라 일반회원으로 전환되어야 회원정보의 이용이 가능합니다.</td></tr>";
	}
	$help_text .= "
	<tr>
		<td align=left colspan='2' style='padding-top:10px;'>
	";
	if($info_type=='sleep_member'){
	$help_text .= "
	<input type='submit' value='일반 회원전환' />";
	}else{
	$help_text .= "
	<input type='submit' value='휴면 회원전환' />";
	}
	$help_text .= "
		</td>
	</tr>
</table>
";
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("휴면회원관리", $help_text,'70')."</form>";


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "";//init();
$P->strLeftMenu = member_menu();
$P->Navigation = "개인정보관리 > 휴면회원관리";
$P->title = "휴면회원관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function getChargerinfo($code,$type){
	$db = new MySQL;
	
	$sql = "select cu.id, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code where cu.code = '".$code."'";
	$db->query($sql);
	$db->fetch();

	if($type == 'name'){
		$data = $db->dt[name];
	}else if ($type == 'id'){
		$data = $db->dt[id];
	}
	return $data;
}


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