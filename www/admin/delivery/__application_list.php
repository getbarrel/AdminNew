<?
include("../class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
//auth(9);

$db = new Database;
$mdb = new Database;
//$sms_design = new SMS;

if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	
//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");
	
	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
	$birDate = $birYY.$birMM.$birDD;
}


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

	$where = " where code != '' and mm.gp_ix = mg.gp_ix and gp_level != 0 ";
	if($region != ""){
		$where .= " and addr1 LIKE  '%".$region."%' ";
	}
	
	if($gp_level != ""){
		$where .= " and mg.gp_level = '".$gp_level."' ";
	}
	
	if($gp_ix != ""){
		$where .= " and mg.gp_ix = '".$gp_ix."' ";
	}	
	

	

	
	if($sex == "M" || $sex == "W"){	
		$where .= " and sex_div =  '$sex' ";
	}
	
	if($mailsend_yn == "Y"){	
		$where .= " and info =  1 ";
	}
	
	if($smssend_yn == "Y"){	
		$where .= " and sms =  1 ";
	}
	
	
	if($search_type != "" && $search_text != ""){	

		$where .= " and $search_type LIKE  '%$search_text%' ";

	}
	
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
		
	if($startDate != "" && $endDate != ""){	
		$where .= " and  MID(replace(date,'-',''),1,8) between  $startDate and $endDate ";
	}
	
	$vstartDate = $vFromYY.$vFromMM.$vFromDD;
	$vendDate = $vToYY.$vToMM.$vToDD;
		
	if($vstartDate != "" && $vendDate != ""){	
		$where .= " and  MID(replace(last,'-',''),1,8) between  $vstartDate and $vendDate ";
	}

	// 전체 갯수 불러오는 부분
	$db->query("SELECT count(*) as total FROM ".TBL_MALLSTORY_MEMBER." mm, ".TBL_MALLSTORY_GROUPINFO." mg  $where ");	
	$db->fetch();
	$total = $db->dt[total];
	$str_page_bar = page_bar($total, $page,$max, "&max=$max&search_type=$search_type&search_text=$search_text&region=$region&gp_level=$gp_level&age=$age&birthday_yn=$birthday_yn&sex=$sex&mailsend_yn=$mailsend_yn&smssend_yn=$smssend_yn&FromYY=$FromYY&FromMM=$FromMM&FromDD=$FromDD&ToYY=$ToYY&ToMM=$ToMM&ToDD=$ToDD&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","view");
/*	
	$sql = "SELECT mm.*, date_format(mm.date,'%Y.%m.%d') as regdate, UNIX_TIMESTAMP(last) AS last, sum(case when mr.state in (1,2,5,6,7) then reserve else 0 end) as reserve 
	FROM  ".TBL_MALLSTORY_MEMBER." mm left outer join ".TBL_MALLSTORY_RESERVE_INFO." mr on mm.code = mr.uid 
	$where   
	group by com_name, com_number, com_phone, com_fax, com_business_status, com_business_category, code, name, mail, visit, perm,gp_level, last 
	ORDER BY date DESC LIMIT $start, $max";
*/	
	$sql = "select mm.code, mm.id, mm.name, mg.gp_level, mm.mail, mm.visit, date_format(mm.date,'%Y.%m.%d') as regdate, 
					date_format(mm.last,'%Y.%m.%d') as last, mg.gp_name,  mm.info
					from ".TBL_MALLSTORY_MEMBER." mm , ".TBL_MALLSTORY_GROUPINFO." mg $where  ORDER BY mm.date DESC LIMIT $start, $max";
	
	//echo $sql;
	$db->query($sql);

$Script = "
<script language='javascript'>

function view_go()
{
	var sort = document.view.sort[document.view.sort.selectedIndex].value;

	location.href = 'member.php?view='+sort;
}


function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
	}else{
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;		
	}
}

function ChangeVisitDate(frm){
	if(frm.visitdate.checked){
		frm.vFromYY.disabled = false;
		frm.vFromMM.disabled = false;
		frm.vFromDD.disabled = false;
		frm.vToYY.disabled = false;
		frm.vToMM.disabled = false;
		frm.vToDD.disabled = false;
	}else{
		frm.vFromYY.disabled = true;
		frm.vFromMM.disabled = true;
		frm.vFromDD.disabled = true;
		frm.vToYY.disabled = true;
		frm.vToMM.disabled = true;
		frm.vToDD.disabled = true;		
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
	onLoad('$sDate','$eDate');";

if($regdate != "1"){
$Script .= "
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;";
}
if($visitdate != "1"){
$Script .= "	
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;	";
}
if($bir != "1"){
$Script .= "	
	frm.birYY.disabled = true;
	frm.birMM.disabled = true;
	frm.birDD.disabled = true;";
}
$Script .= "			
}

function BatchSubmit(frm){
	
	if(frm.update_kind[3].checked){
		if(frm.sms_text.value.length < 1){
			alert('SMS 발송 내역을 입력하신후 보내기 버튼을 클릭해주세요');
			frm.sms_text.focus();
			return false;
		}
	}
}

function ChangeUpdateForm(selected_id){
	var area = new Array('batch_update_reserve','batch_update_group','batch_update_sms');
	
	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			document.getElementById(selected_id).style.display = 'block';			
		}else{			
			document.getElementById(area[i]).style.display = 'none';
		}
	}
	
	
		
}
</script>";

if($before_update_kind){
	$update_kind = $before_update_kind;
}

$Contents = "


<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("메일링/SMS 주소록 관리", "회원관리 > 메일링/SMS 주소록 관리 ")."</td>
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
								<td class='box_02' onclick=\"document.location.href='addressbook_group.php'\" >메일링/SMS 주소록 그룹관리</td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' class='on'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='addressbook.php'\">메일링/SMS 관리</td>
								<th class='box_03'></th>
							</tr>
							</table>
							
						</td>
						<td style='width:600px;text-align:right;vertical-align:bottom;padding:0 0 10 0'>						
							총건수 :&nbsp;<b>".$total."</b>
						</td>
					</tr>
					</table>	
				</div>
	    </td>
	</tr>	
  <tr>
  	<td>";
$Contents .= "  
<table class='box_shadow' style='width:100%;height:100%;' >
	<tr>
		<th class='box_01'></th>
		<td class='box_02'></td>
		<th class='box_03'></th>
	</tr>
	<tr>
		<th class='box_04'></th>
		<td class='box_05'  valign=top style='padding:5 5 5 5'>	
 		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
		<form name=searchmember method='get' style='display:inline;'><!--SubmitX(this);'-->
    <input type='hidden' name=act value='".$act."'>
    <input type='hidden' name=before_update_kind value='".$update_kind."'>
    
		    <tr  height=27>
		      <td width='15%' bgcolor='#efefef' align=center >지역선택</td>
		      <td align=left style='padding-left:5px;'>
		      <select name='region' >
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
		      <td bgcolor='#efefef' align=center>연령</td>
		      <td align=left  style='padding-left:5px;'>
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
		      
		    </tr>				    		
		    <tr height=1><td colspan=4 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center><label for='bir'>생일</label><input type='checkbox' name='bir' id='bir' value='1' onclick='ChangeBirDate(document.searchmember);' ".CompareReturnValue("1",$bir,"checked")."> </td>
		      <td align=left style='padding-left:5px;'>
				 <SELECT onchange=javascript:onChangeDate(this.form.birYY,this.form.birMM,this.form.birDD) name=birYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.birYY,this.form.birMM,this.form.birDD) name=birMM></SELECT> 월 <SELECT name=birDD></SELECT> 일
		      </td>			      				    
		      <td bgcolor='#efefef' align=center>성별검색 </td>
		      <td align=left style='padding-left:5px;'>
		      <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("0",$sex,"checked")."><label for='sex_all'>모두</label>
		      <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
		      <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label> 
		    </td>
		    </tr>		
		    <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center>회원그룹 </td>
		      <td align=left style='padding-left:5px;'>
		      ".makeGroupSelectBox($mdb,"gp_ix",$gp_ix)."
		      ".makeGroupLevelSelectBox($mdb,"gp_level",$gp_level)."
		      
		      </td>
		      <td bgcolor='#efefef' align=center>조건검색 </td>
		      <td align=left style='padding-left:5px;'>
		      <select name=search_type>
						<option value='name' ".CompareReturnValue("name",$search_type,"selected").">고객명</option>
						<option value='pcs' ".CompareReturnValue("pcs",$search_type,"selected").">휴대전화</option>
						<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">회사명</option>
						<option value='com_phone' ".CompareReturnValue("com_phone",$search_type,"selected").">회사전화</option>
						<option value='com_fax' ".CompareReturnValue("com_fax",$search_type,"selected").">회사팩스</option>					
						<option value='mail' ".CompareReturnValue("mail",$search_type,"selected").">이메일</option>					
						<option value='addr1' ".CompareReturnValue("addr1",$search_type,"selected").">주소</option>
		      </select>
		      <input type=text name='search_text' value='".$search_text."' style='width:50%' >
		      </td>			
		    </tr>
		    <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center>발송여부 </td>
		      <td align=left >
		      <input type=radio name='mailsend_yn' value='Y' id='mailsend_y'  ".CompareReturnValue("Y",$mailsend_yn,"checked")."><label for='mailsend_y'>수신회원만</label><input type=radio name='mailsend_yn' value='N' id='mailsend_n' ".CompareReturnValue("N",$mailsend_yn,"checked")."><label for='mailsend_n'>수신거부회원포함</label> 
		      </td>
		       <td bgcolor='#efefef' align=center>SMS 발송여부 </td>
		      <td align=left >
		      <input type=radio name='smssend_yn' value='Y' id='smssend_y'  ".CompareReturnValue("Y",$smssend_yn,"checked")."><label for='smssend_y'>수신회원만</label><input type=radio name='smssend_yn' value='N' id='smssend_n' ".CompareReturnValue("N",$smssend_yn,"checked")."><label for='smssend_n'>수신거부회원포함</label> 
		      </td>			
		    </tr>
		    <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>
		    ";

$vdate = date("Ymd", time());
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
		      <td bgcolor='#efefef' align=center><label for='regdate'>가입일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);' ".CompareReturnValue("1",$regdate,"checked")."></td>
		      <td align=left colspan=3 style='padding-left:5px;'>
		      	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>		
				<tr>					
					<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
					<TD width=20 align=center> ~ </TD>
					<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>
					<TD>
						<a href=\"javascript:select_date('$voneweekago','$today',1);\"><img src='../image/b_btn_s_1week01.gif'></a>
						<a href=\"javascript:select_date('$v15ago','$today',1);\"><img src='../image/b_btn_s_15day01.gif'></a>
						<a href=\"javascript:select_date('$vonemonthago','$today',1);\"><img src='../image/b_btn_s_1month01.gif'></a>
						<a href=\"javascript:select_date('$v2monthago','$today',1);\"><img src='../image/b_btn_s_2month01.gif'></a>
						<a href=\"javascript:select_date('$v3monthago','$today',1);\"><img src='../image/b_btn_s_3month01.gif'></a>
					</TD>
				</tr>		
			</table>	
		      </td>			
		    </tr>		    
		    <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center><label for='visitdate'>최근방문일</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);' ".CompareReturnValue("1",$visitdate,"checked")."></td>
		      <td align=left colspan=3  style='padding-left:5px;'>
		      	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>		
				<tr>
					<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
					<TD width=20 align=center> ~ </TD>
					<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>					
					<TD>
						<a href=\"javascript:select_date('$voneweekago','$today',2);\"><img src='../image/b_btn_s_1week01.gif'></a>
						<a href=\"javascript:select_date('$v15ago','$today',2);\"><img src='../image/b_btn_s_15day01.gif'></a>
						<a href=\"javascript:select_date('$vonemonthago','$today',2);\"><img src='../image/b_btn_s_1month01.gif'></a>
						<a href=\"javascript:select_date('$v2monthago','$today',2);\"><img src='../image/b_btn_s_2month01.gif'></a>
						<a href=\"javascript:select_date('$v3monthago','$today',2);\"><img src='../image/b_btn_s_3month01.gif'></a>
					</TD>
				</tr>		
			</table>	
		      </td>			
		    </tr>		    
		    <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>
		    
		    </table>
		</td>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>			";
 
$Contents .= "    
    </td>
    
  </tr>
  <tr height=50>		    	
    	<td style='padding:10 20 0 20' colspan=4 align=center><input type=image src='../image/bt_search.gif' align=absmiddle  ></td>		    	
    </tr>
</table><br></form>
<form name='list_frm' method='POST' onsubmit='return BatchSubmit(this);' action='member_batch.act.php' target='act'>
<input type='hidden' name='code[]' id='code'>
<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
<div id='result_area'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='5%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='5%' align='center' class='m_td'><font color='#000000'><b>번호</b></font></td>
    <td width='8%' align='center' class='m_td'><font color='#000000'><b>그룹</b></font></td>
    <td width='5%' align='center' class=m_td><font color='#000000'><b>이름</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>아이디</b></font></td>
    <td width='15%' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>등록일</b></font></td>
    <td width='7%' align='center' class=m_td><font color='#000000'><b>로긴수</b></font></td>
    <td width='7%' align='center' class=m_td><font color='#000000'><b>적립금</b></font></td>
		<td width='10%' align='center' class=m_td><font color='#000000'><b>최종로그인</b></font></td>
    <td width='10%' align='center' class=e_td><font color='#000000'><b>메일링</b></font></td>
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
		
		
		$mdb->query("SELECT IFNULL(sum(reserve),'-') as reserve_sum FROM ".TBL_MALLSTORY_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in (1,2,5,6,7)");
		$mdb->fetch(0);
		$reserve_sum = number_format($mdb->dt[0]);
		
		
$Contents = $Contents."
  <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
    <td align='center' ><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
    <td align='center' >".$no."</td>
    <td align='center'><span title='".$db->dt[organization_name]."'>".$db->dt[gp_name]."</span><br><span class='small'>".$mem_div."</span></td>
    <td align='center' ><a href=\"javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',950,500,'member_info')\">".$db->dt[name]."</td>
    <td align='center' >".$db->dt[id]."</td>
    <td align='center' >".$db->dt[mail]."</td>
    <td align='center' >".$db->dt[regdate]."</td>
    <td align='center' >".$db->dt[visit]."</td>
    <td align='center' ><a href=\"javascript:PoPWindow('reserve.pop.php?code=".$db->dt[code]."',650,700,'reserve_pop')\">".$reserve_sum."</a></td>
    <td align='center' >".$db->dt[last]."</td>
    <td align='center' >".($db->dt[info] == "1" ? "수신":"비수신")."</td>
  </tr>
<tr hegiht=1><td colspan=13 class='dot-x'></td></tr> ";

	}

if (!$db->total){
		
$Contents = $Contents."		
  <tr height=50>
    <td colspan='13' align='center'>등록된 데이타가 없습니다.</td>
  </tr>";
  
}
	

	
$Contents .= "
  <tr height='40'>
    <td colspan=5 align=left> 
				
    </td>
    <td  colspan='8' align='right' >&nbsp;".$str_page_bar."&nbsp;</td>
  </tr>
</table>
</div>";

$help_text = " 

<div id='batch_update_reserve' ".($update_kind == "reserve" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4 0 4 0'><img src='../images/dot_org.gif'> <b>적립금 일괄변경</b> <span class=small style='color:gray'>적립금 금액 및 내용을 입력후 저장 버튼을 클릭해주세요</span></div>
<table cellpadding=3 cellspacing=0 width=100% style='border:1px solid #e2e2e2;'>
	<col width=170>
	<col width=*>
	<tr><td bgcolor='#efefef'><img src='../image/ico_dot.gif' border=0  > <b>적립금 지급액 / 차감액</b></td><td > <input type=text name='reserve'  class=textbox value='' onkeydown='onlyNumber(this)' onkeyup='onlyNumber(this)'  style='width:150' > <span class='small blu'>사용의 경우 마니너스 금액으로 입력하세요 예) -1000</span></td></tr>
	<tr height=1><td colspan=4 class='dot-x'></td></tr>
	<tr><td bgcolor='#efefef'><img src='../image/ico_dot.gif' border=0  > <b>적립금 적립내용</b></td><td > <input type=text name='etc'  class=textbox value='' style='width:250' ></td></tr>
	<tr height=1><td colspan=4 class='dot-x'></td></tr>
	<tr>
		<td bgcolor='#efefef'><img src='../image/ico_dot.gif' border=0  > <b>적립금 상태</b></td>
		<td><select name='state'>
					<option value=0>적립대기</option>
					<option value=1>적립완료</option>
					<option value=2>사용내역</option>
					<option value=5>반품</option>												
					<option value=9>주문취소</option>
				</select> 
		</td>
	</tr>
</table>	
<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
	<tr height=50><td colspan=4 align=center><input type=image src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>	
</table>
</div>
<div id='batch_update_group' ".($update_kind == "group" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4 0 4 0'><img src='../images/dot_org.gif'> <b>메일링/SMS 그룹 일괄변경</b> <span class=small style='color:gray'>변경하시고자 하는 메일링/SMS그룹을 선택후 저장 버튼을 클릭해주세요</span></div>
<table cellpadding=3 cellspacing=0 width=100% style='border:1px solid #e2e2e2;'>
	<col width=170>
	<col width=*>
	
	<tr>
		<td bgcolor='#efefef'>
			<img src='../image/ico_dot.gif' border=0  > <b>캡페인 그룹</b>
			<input type='checkbox' name='mem_group_use' id='bir' value='1' onclick=\"if(this.checked){\$('update_gp_ix').disabled = false;}else{\$('update_gp_ix').disabled = true;}\">
		</td>
		<td >".makeGroupSelectBox($mdb,"update_gp_ix",$update_gp_ix, " disabled")." <span class=small style='color:gray'>회원그룹 변경에 따라 회원등급이 자동 변경됩니다.</span></td></tr>
	<!--tr height=1><td colspan=4 class='dot-x'></td></tr>
	<tr>
		<td bgcolor='#efefef'><img src='../image/ico_dot.gif' border=0  > <b>회원등급</b><input type='checkbox' name='mem_level_use' id='bir' value='1' onclick=\"if(this.checked){\$('update_gp_level').disabled = false;}else{\$('update_gp_level').disabled = true;}\"></td>
		<td>
		".makeGroupLevelSelectBox($mdb,"update_gp_level",$update_gp_level, " disabled")."
		</td>
	</tr-->
</table>	
<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
	<tr height=30><td colspan=4 align=left><span class=small style='color:gray'>회원그룹 변경시 회원 등급이 자동으로 변경됩니다.</span></td></tr>	
	<tr height=50><td colspan=4 align=center><input type=image src='../image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
</div>
<div id='batch_update_sms' ".($update_kind == "sms" ? "style='display:block'":"style='display:none'")." >
<div style='padding:4 0 4 0'><img src='../images/dot_org.gif'> <b>sms 일괄발송</b> <span class=small style='color:gray'>검색/선택된 회원에게 SMS 를 발송합니다</span></div>
<table cellpadding=0 cellspacing=0>
	<tr>
		<td>
			<table class='box_shadow' style='width:132;height:120;' >
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05'  valign=top style='padding:5 7 5 7'>	
						<table cellpadding=0 cellspacing=0><!--CheckSpecialChar(this);-->
						<tr><td align=left>mallstory sms </td></tr>	
						<tr><td><textarea style='width:106;height:100;background-color:#efefef;border:1px solid #e6e6e6;padding:2px;overflow:hidden;' name='sms_text' onkeyup=\"fc_chk_byte(this,80, this.form.sms_text_count);\" ></textarea></td></tr>
						<tr><td height=20 align=right><input type=text name='sms_text_count' style='display:inline;border:0px;text-align:right' size=3 value=0> / 80 byte </td></tr>
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
	<!--/tr>
	<tr-->";
$cominfo = getcominfo();
		
$help_text .= "
		<td valign=top style='padding:0 0 0 10'>
			<table cellpadding=0 cellspacing=0><input type=hidden name='sms_send_page' value='1'>
				<tr height=26><td align=left width=90 class=small>보내는사람 : </td><td><input type=text name='send_phone' class=textbox style='display:inline;' size=12 value='".$cominfo[com_phone]."'></td></tr>	
				<tr height=22><td align=left class=small>SMS 잔여건수 : </td><td>".$sms_design->getSMSAbleCount($admininfo)." 건 </td></tr>	
				<tr height=22><td align=left class=small>발송수/발송대상 : </td><td><b id='sended_sms_cnt' class=blu>0</b> 건 / <b id='remainder_sms_cnt'>$total</a> 명</td></tr>	
				<tr height=22>
						<td align=left class=small>발송수량(1회) : </td>
						<td>
						<select name=max>
							<option value='5' >5</option>
							<option value='10'  >10</option>
							<option value='20' >20</option>
							<option value='50' >50</option>
							<option value='100' selected>100</option>
							<option value='200' >200</option>
							<option value='300' >300</option>
							<option value='400' >400</option>
							<option value='500' >500</option>
							<option value='1000' >1000</option>
						</select>
						</td>
				</tr>	
				<tr height=22><td align=left class=small>일시정지 : </td><td><input type='checkbox' name='stop' ></td></tr>	
				<tr height=50><td align=center colspan=2><input type=image src='../image/btn_send.gif' border=0> </td></tr>	
			</table>
		</td>
	</tr>
</table>
</div>
";

$select = "
<select name='update_type' >
					<option value='1'>검색한 회원 전체에게</option>
					<option value='2'>선택한회원 전체에게</option>
				</select>
				
				<input type='radio' name='update_kind' id='update_kind_group' value='group' ".CompareReturnValue("group",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_group');\"><label for='update_kind_group'>메일링/SMS 그룹 일괄변경</label>
				<input type='radio' name='update_kind' id='update_kind_sms' value='sms' ".CompareReturnValue("sms",$update_kind,"checked")." onclick=\"ChangeUpdateForm('batch_update_sms');\"><label for='update_kind_sms'>SMS 일괄발송</label>";

$Contents .= "".HelpBox($select, $help_text)."</form>";


$Contents .= "
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";




$P = new LayOut();
$P->addScript = "<script language='javascript' src='member.js'></script>\n<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = campaign_menu();
$P->Navigation = "HOME > 회원관리 > 메일링/SMS 주소록 관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();


/*

CREATE TABLE `mallstory_sms_group` (
  `sg_ix` int(8) NOT NULL AUTO_INCREMENT,
  `sg_name` varchar(50) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sg_ix`)
}

CREATE TABLE `mallstory_sms_address` (
  `sa_ix` int(8) NOT NULL AUTO_INCREMENT,
  `sg_ix` int(8) DEFAULT NULL,
  `sa_name` varchar(25) NOT NULL DEFAULT '0',
  `sa_mobile` varchar(15) DEFAULT '',
  `sa_sex` enum('M','F')  DEFAULT NULL,
  `sa_etc` varchar(255) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sa_ix`),
  KEY `regdate` (`regdate`),
) ENGINE=MyISAM  DEFAULT CHARSET=utf8


CREATE TABLE `mallstory_sms_address` (
  `sa_ix` int(8) NOT NULL AUTO_INCREMENT,
  `sg_ix` int(8) DEFAULT NULL,
  `sa_name` varchar(25) NOT NULL DEFAULT '0',
  `sa_mobile` varchar(15) DEFAULT '',
  `sa_sex` enum('M','F')  DEFAULT NULL,
  `sa_etc` varchar(255) DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sa_ix`),
  KEY `regdate` (`regdate`),
) ENGINE=MyISAM  DEFAULT CHARSET=utf8

CREATE TABLE `mallstory_sms_history` (
  `sh_ix` int(8) NOT NULL AUTO_INCREMENT,
  `send_phone` varchar(50) DEFAULT NULL,
  `dest_mobile` varchar(15) DEFAULT '',
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`sg_ix`)
}
*/
?>



