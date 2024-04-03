<?
include("../class/layout.class");
//auth(9);

$db = new Database;
$mdb = new Database;


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


function init(){

	var frm = document.searchmember;		
	onLoad('$sDate','$eDate');
	
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;
	
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;		
}

</script>";

$Contents = "


<script language='javascript' src='member.js?page=$page&ctgr=$ctgr&view=$view&qstr=".rawurlencode($qstr)."'></script>

<table width='100%' border='0' align='center'>
  <tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("회원정보백업", "회원관리 > 회원정보백업 ")."</td>
  </tr>
  <!--tr><td colspan=3 align=right style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 회원정보백업 </b></div>")."</td></tr-->
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
		<td class='box_05'  valign=top style='padding:5 5 0 5'>	
 		<table width='100%' border='0' cellspacing='0' cellpadding='0' height='25'>
		<form name=searchmember method='get' ><!--SubmitX(this);'-->
                    <input type='hidden' name=act value='".$act."'>
                    <input type='hidden' name=mc_ix value='".$mc_ix." '>
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
		      <td bgcolor='#efefef' align=center>오늘 생일 </td>
		      <td align=left style='padding-left:5px;'>
		      <input type=radio name='birthday_yn' value='Y' id='birthday_y'  ".CompareReturnValue("Y",$birthday_yn,"checked")."><label for='birthday_y'>예</label><input type=radio name='birthday_yn' value='N' id='birthday_n' ".CompareReturnValue("N",$birthday_yn,"checked")."><label for='birthday_n'>아니오(전체)</label> 
		      </td>			      				    
		      <td bgcolor='#efefef' align=center>성별검색 </td>
		      <td align=left style='padding-left:5px;'>
		      <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("",$sex,"checked")."><label for='sex_all'>모두</label>
		      <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
		      <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label> 
		    </td>
		    </tr>		
		    <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center>회원등급 </td>
		      <td align=left style='padding-left:5px;'>
		      <select name=mem_level>
		      	<option value=''>전체회원</option>
		      	<option value='M' ".CompareReturnValue("M",$mem_level,"selected").">일반회원</option>
			<option value='D' ".CompareReturnValue("D",$mem_level,"selected").">딜러회원</option>
		        <option value='A' ".CompareReturnValue("A",$mem_level,"selected").">대리점</option>
		    	</select>
		      </td>
		      <td bgcolor='#efefef' align=center>조건검색 </td>
		      <td align=left style='padding-left:5px;'>
		      <select name=search_type>
			<option value='name' ".CompareReturnValue("name",$search_type,"selected").">고객명</option>
			<option value='id' ".CompareReturnValue("id",$search_type,"selected").">아이디</option>
			<option value='jumin' ".CompareReturnValue("jumin",$search_type,"selected").">주민번호</option>
			<option value='tel' ".CompareReturnValue("tel",$search_type,"selected").">전화번화</option>
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
		    <tr height=27>		      
		      <td bgcolor='#efefef' align=center>조건검색 </td>
		      <td align=left style='padding-left:5px;' >
		      <select name=search_type>
			<option value='name' ".CompareReturnValue("name",$search_type,"selected").">고객명</option>
			<option value='id' ".CompareReturnValue("id",$search_type,"selected").">아이디</option>
			<option value='jumin' ".CompareReturnValue("jumin",$search_type,"selected").">주민번호</option>
			<option value='tel' ".CompareReturnValue("tel",$search_type,"selected").">전화번화</option>
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
		    <tr hegiht=1><td colspan=4 class='dot-x'></td></tr>";

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
		      <td bgcolor='#efefef' align=center><label for='regdate'>가입일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.searchmember);'></td>
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
		      <td bgcolor='#efefef' align=center><label for='visitdate'>최근방문일</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.searchmember);'></td>
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
		    <tr height=50>		    	
		    	<td style='padding:0 20 0 20' colspan=4 align=center><input type=image src='../image/bt_search.gif' align=absmiddle  ></td>		    	
		    </tr>
		    </table>
		</td></form>
		<th class='box_06'></th>
	</tr>
	<tr>
		<th class='box_07'></th>
		<td class='box_08'></td>
		<th class='box_09'></th>
	</tr>
</table>			";
 
$Contents .= "          
      
      </select>
    </td>
    
  </tr>
</table><br>
<form name='list_frm'>
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center'>
 
  <tr height=25 ><td colspan=10 align=right><a href='member.excel.php?".$QUERY_STRING."'><img src='../image/btn_excel_save.gif' align=absmiddle  ></a></td></tr>
  <tr height='28' bgcolor='#CCCCCC'>
    <td width='5%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='5%' align='center' class=m_td><font color='#000000'><b>번호</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>아이디</b></font></td>    
    <td width='10%' align='center' class=m_td><font color='#000000'><b>이름</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>권한</b></font></td>
    <td width='15%' align='center' class=m_td><font color='#000000'><b>이메일</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>핸드폰</b></font></td>
    <td width='5%' align='center' class=m_td><font color='#000000'><b>로긴수</b></font></td>
    <td width='5%' align='center' class=m_td><font color='#000000'><b>적립금</b></font></td>
    <td width='5%' align='center' class=e_td><font color='#000000'><b>등록일</b></font></td>
  </tr>";


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

	$where = " where cu.code != '' and cu.code = cmd.code ";
	if($region != ""){
		$where .= " and addr1 LIKE  '%".$region."%' ";
	}
	
	if($mem_level != ""){
		$where .= " and mem_level = '".$mem_level."' ";
	}	
	
	if($age != ""){
		//$where .= " and year(now()) - ((19+ROUND((MID(replace(jumin,'-',''),7,1)-1)/2))*100+left(jumin,2)-1) between ".$age." and  ".($age+9)." ";
		$where .= " and year(now()) - (1900+left(jumin,2)-1) between ".$age." and  ".($age+9)." ";
	}
	
	
	if($birthday_yn == "Y"){		
		$where .= " and MID(jumin,3,4) =  '".date("md")."' ";
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
	$db->query("SELECT count(*) as total FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd $where ");	
	$db->fetch();
	$total = $db->dt[total];
	

	$sql = "select cu.code, cu.id, cmd.name, cu.mem_type, cmd.mail, cu.visit, pcs, date_format(cu.date,'%Y.%m.%d') as regdate, UNIX_TIMESTAMP(last) AS last
	from ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd $where  ORDER BY cu.date DESC LIMIT $start, $max";
	
//	echo $sql;
	$db->query($sql);

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);

		$no = $total - ($page - 1) * $max - $i;

		if ($db->dt[mem_type] == "C")	{ $perm = "기업회원"; }
		if ($db->dt[mem_type] == "M")	{ $perm = "일반회원"; }
		if ($db->dt[mem_type] == "F")	{ $perm = "외국인회원"; }
		if ($db->dt[mem_type] == "S")	{ $perm = "셀러회원"; }
		
		$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in (1,2,5,6,7)");	
		$mdb->fetch(0);
		
$Contents = $Contents."
  <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='hand'\" onMouseOut=\"this.style.backgroundColor=''\">
    <td align='center' ><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
    <td align='center' onClick=\"PoPWindow('member_view.php?code=".$db->dt[code]."',950,700,'member_info')\">".$no."</td>
    <td align='center' onClick=\"PoPWindow('member_view.php?code=".$db->dt[code]."',950,700,'member_info')\">".$db->dt[id]."</td>
    <td align='center' onClick=\"PoPWindow('member_view.php?code=".$db->dt[code]."',950,700,'member_info')\">".$db->dt[name]."</td>
    <td align='center' onClick=\"PoPWindow('member_view.php?code=".$db->dt[code]."',950,700,'member_info')\">".$perm."</td>
    <td align='center' onClick=\"PoPWindow('member_view.php?code=".$db->dt[code]."',950,700,'member_info')\">".$db->dt[mail]."</td>
    <td align='center' onClick=\"PoPWindow('member_view.php?code=".$db->dt[code]."',950,700,'member_info')\">".$db->dt[pcs]."</td>
    <td align='center' onClick=\"PoPWindow('member_view.php?code=".$db->dt[code]."',950,700,'member_info')\">".$db->dt[visit]."</td>
    <td align='center' ><a href=\"javascript:PoPWindow('reserve.pop.php?code=".$db->dt[code]."',650,550,'sendsms')\">".($mdb->dt[reserve_sum] == "" ? "-":$mdb->dt[reserve_sum])."</a></td>
    <td align='center' nowrap>    
	  ".$db->dt[regdate]."
    </td>
  </tr>
<tr hegiht=1><td colspan=10 class='dot-x'></td></tr> ";

	}

if (!$db->total){
		
$Contents = $Contents."		
  <tr height=50>
    <td colspan='8' align='center'>등록된 데이타가 없습니다.</td>
  </tr>";
  
}

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}
	
$Contents = $Contents."
	</form>
  <tr height='40'><form name='search' method='get' action='member.php'>
    <td colspan=4 align=left> 
	<!--table cellpadding=2 cellspacing=0>
       		<tr>
	       		<td><select name='ctgr' style=\"behavior: url('../js/selectbox.htc'); height: 20px; width: 100px;\" >
					<option value='com_name'>회사명</option>
					<option value='com_phone'>회사전화</option>
					<option value='com_fax'>회사팩스</option>
					<option value='name'>이름</option>
					<option value='id'>아이디</option>
					<option value='mail'>이메일</option>
					<option value='jumin'>주민번호</option>
					<option value='tel'>집전화</option>
					<option value='pcs'>휴대폰</option>
					
					<option value='addr1'>주소</option>
				</select></td>
			<td><input type='text' class='input' name='qstr' size='15' style='border:1px solid #000000;height: 20px; '>
				<input type='button' class='button' value='검색' onClick='search.submit()'>
			</td>
		</tr>
	</table-->
    </td></form>
    <td  colspan='6' align='right' >&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
  </tr>
</table>

<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = member_menu();
$P->Navigation = "HOME > 회원관리 > 전체회원";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>



