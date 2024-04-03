<? 
include("../class/layout.class");

$db = new Database;


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



$Script = "	<script language='javascript'>
		
		
		function showObj(id)
		{
			obj = eval(id+'.style');
			obj.display = 'block';
	
			document.lyrstat.opend.value = id;
		}
	
		function hideObj(id)
		{
			obj = eval(id+'.style');
			obj.display = 'none';
	
			document.lyrstat.opend.value = '';
		}
	
		function swapObj(id)
		{
			
			obj = eval(id+'.style');
			stats = obj.display;
	
			if (stats == 'none')
			{
				if (document.lyrstat.opend.value)
					hideObj(document.lyrstat.opend.value);
	
				showObj(id);
			}
			else
			{
				hideObj(id);
			}
		}
		
		function BoardDelete(bm_ix){		
			if(confirm('해당 게시판을 정말로 삭제하시겠습니까? 게시판을 삭제 하시면 관련 데이타 모두가 삭제 됩니다.')){
				document.location.href='board.manage.act.php?act=delete&bm_ix='+bm_ix
			}
		}
	
		</script>";
/*		
<tr>
	    <td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 사업자 정보</b></div>")."</td>
	  </tr>
*/	  
$db->query("SELECT * FROM ".TBL_SHOP_MAIL_TAGET." ");

$mstring ="
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=4 style='padding-bottom:10px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 타겟군 설정하기</b></div>")."</td>
		</tr>
		<tr>
			<td colspan=4 align=center>
			".SelectTarget()."
			</td>
		</tr>
		<tr><form name=poll action='board.manage.act.php'><input type=hidden name=act value=insert>
			<td align='left' colspan=4 style='padding-bottom:10px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle><b> 타겟군 리스트</b></div>")."</td>
		</tr>
		<tr>
			<td>
			<table cellpadding=3 cellspacing=0 width='100%'>
				<tr height=27 align=center>
					<td width='4%' class='s_td'> NO.</td>
					<td width='20%' class='m_td'>타겟군 이름</td>
					<td width='10%' class='m_td'>성별</td>
					<td width='10%' class='m_td'>결혼여부 </td>
					<td width='10%' class='m_td'>지역</td>
					<td width='10%' class='m_td'>학력</td>
					<td width='10%' class='m_td'>직업</td>
					<td width='10%' class='m_td'>나이</td>
					<td width='20%' class='m_td'>가입날짜</td>
					<td width='10%' class='e_td' nowrap>관리 </td>
				</tr>		
				";
if($db->total){
for($i=0;$i < $db->total;$i++){
	$db->fetch($i);
$mstring .="		<tr height=27 align=center>
					<td align=center bgcolor=#efefef>
						".($i+1)."
					</td>
					<td align=left style='padding-left;20px;'>
						<a href='mail.manage.php?mc_ix=".$db->dt[mc_ix]."'>".$db->dt[mc_title]."</a>
					</td>
					<td bgcolor=#efefef align=left style='padding-left:5px;'>
					".$db->dt[mt_name]."
					</td>
					<td  >
					".$db->dt[mt_sex]."
					</td>
					<td bgcolor=#efefef>
					".$db->dt[mc_marriage_yn]."
					</td>
					<td  >
					".$db->dt[mt_age]."
					</td>
					<td bgcolor=#efefef>
					".$db->dt[mc_region]."
					</td>
					<td  >
					".$db->dt[mt_scholarship]."
					</td>
					<td bgcolor=#efefef>
					".$db->dt[mc_job]."
					</td>
					<td bgcolor=#efefef>
					".$db->dt[mc_regdate_s]." ~ ".$db->dt[mc_regdate_e]."
					</td>
					<td  align=left style='padding-left:10px;' nowrap>
					<a href='mail.manage.php?mc_ix=".$db->dt[mc_ix]."'><img src='../image/bt_modify.gif' border=0></a> ";


$mstring .="			<a href=\"JavaScript:BoardDelete('".$db->dt[mc_ix]."')\"><img src='../image/bt_del.gif' border=0></a>";



$mstring .="			</td>
				</tr>		
				<tr hegiht=1><td colspan=8 background='../image/dot.gif'></td></tr>
				<tr >";
}
}else{
$mstring .="		<tr height=50 align=center >
					<td align=center colspan=11 bgcolor=#ffffff>
						게시판이 존재 하지 않습니다.
					</td>
				</tr>
				<tr hegiht=1><td colspan=11 background='../image/dot.gif'></td></tr>
				";
}

$mstring .="					
				</tr>				
			</table>		
			</td>
		</tr>
		</form>";
$mstring .="</table>";
/*		
//colorCirCleBox("#efefef",660,"<div style='padding:5px;'><img src='../image/title_head.gif' align=absmiddle> 영업정보</div>")
$mstring .="<tr align=center bgcolor=#ffffff><td width=400><input type=text name=title size=60></td><td width=100>".SelectFieldNumber($selectfield)."</td><td width=100><input type=submit value='save'></td></tr>";
$mstring .="<tr height=40><td align=left colspan=3>";
$mstring .= "<img src='../image/emo_3_15.gif' align=absmiddle> 설문항목과 문항수를 입력해주세요";
$mstring .="</td></tr></form>";
$mstring .="</table>";
*/
//$mstring = ShadowBox($mstring);

$Contents = $mstring;

//$Script = "<script language='javascript' src='basicinfo.js'></script>";
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = marketting_menu();
$P->addScript = "<script language='javascript' src='mail.target.js'></script><script language='javascript' src='../include/DateSelect.js'></script>";
$P->OnloadFunction = "onLoad('$sDate','$eDate');";
$P->strContents = $Contents;
echo $P->PrintLayOut();


function FieldInsert($pollnumber, $fieldnumber, $disp){
$dbm = new Database;
$dbm->query("SELECT * FROM ".TBL_SHOP_POLL_FIELD." where number = '$pollnumber' order by fieldnumber");	
if($dbm->total > 0){
	$actstring = "fieldupdate";	
	$submitstring = "수정하기";
}else{
	$actstring = "fieldinsert";
	$submitstring = "저장하기";
}

	$mstring = "<div id='TG_VIEW_".$pollnumber."' style='position: relative; display: none;'>";
	$mstring .="<form name='field$pollnumber' action='poll.act.php'><input type=hidden name=pollnumber value=$pollnumber><input type=hidden name=act value=$actstring><input type=hidden name=fieldsize value='$fieldnumber'>";
	$mstring .= "<table cellapdding=0 cellspaicng=0>";
	for($i=0;$i<$fieldnumber;$i++){
		$dbm->fetch($i);
		if($i==0){
			$mstring .= "<tr><td>".($i+1)."</td><td><input type=text name=fielddesc".($i)." size=40 value='".$dbm->dt[fielddesc]."'></td><td  valign=top style='padding-left:10px;' rowspan=10>표시 : <input type='checkbox' name='disp' style='border:1px solid #ffffff' value=1 ".($disp==1 ? "checked":"")."> &nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value='$submitstring'></td></tr>";
		}else{
			$mstring .= "<tr><td>".($i+1)."</td><td><input type=text name=fielddesc".($i)." size=40 value='".$dbm->dt[fielddesc]."'></td></tr>";
		}
	}
	$mstring .= "</table></form></div>";
	
	return $mstring;
	
}


function SelectFieldNumber($selectfield)
{
	$divname = array ("1","2","3","4","5","6","7","8","9");
	
	$pos = 0;
	$strDiv = "<Select name='fieldnum'>\n";
	$strDiv = $strDiv."<option value=0>항목수</option>\n";
	while(hasMoreElements(&$divname))
	{
	       	if( $pos == $selectdiv )
	       	{
	        	$strDiv = $strDiv."<option value='".($pos+1)."' Selected>".$divname[$pos]."</option>\n";
	       	}else{
	       		$strDiv = $strDiv."<option value='".($pos+1)."'>".$divname[$pos]."</option>\n";
		}	       
	       	$pos++;
	}	

	$strDiv = $strDiv."</Select>\n";
	
	return $strDiv;

}



/*

create table companyinfo (
company_id varchar(32) not null ,
company_name varchar(50) null default null,
business_number varchar(40) null default null,
business_kind varchar(40) null default null,
ceo varchar(20) null default null,
business_item varchar(50) null default null,
company_address varchar(200) null default null,
bank_owner varchar(20) null default null,
bank_name varchar(20) null default null,
bank_number varchar(30) null default null,
business_day datetime null default null,
admin_id varchar(20) null default null,
admin_pass varchar(32) null default null,
phone varchar(20) null default null,
fax varchar(20) null default null,
charger varchar(20) null default null,
charger_email varchar(20) null default null,
homepage varchar(50) null default null,
shipping_company varchar(30) null default null,
primary key(company_id));
*/


function SelectTarget(){

$mstring ="
<form name='target_form' action='mail.target.act.asp' method=get>
<table width=750 border=0 cellpadding=5 cellspacing=0>
			<tr >
				<td width=200><li>성별 : </td>
				<td colspan=3>
					<input type='radio' name='sex_sel' value='0' id='sex_sel0' checked /> <label for='sex_sel0'>모두</label> 
					<input type='radio' name='sex_sel' value='1' id='sex_sel1' /> <label for='sex_sel1'>남성</label> 
					<input type='radio' name='sex_sel' value='2' id='sex_sel2' /> <label for='sex_sel2'>여성</label>
				</td>
			</tr>
			<tr >
				<td ><li>결혼여후 : </td>
				<td colspan=2>
					<input type='radio' name='marriage_yn' value='0' id='marriage_yn_all' checked /> <label for='marriage_yn_all'>모두</label> 
					<input type='radio' name='marriage_yn' value='1' id='marriage_yn_y' /> <label for='marriage_yn_y'>결혼</label> 
					<input type='radio' name='marriage_yn' value='2' id='marriage_yn_n' /> <label for='marriage_yn_n'>미혼</label>
				</td>
			</tr>
			<tr>
				<TD ><li>가입일자</TD>												
				<TD width=210 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
				<TD width=20 align=center> ~ </TD>
				<TD width=210 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>				
			</tr>
			<tr>
				<TD ><li>최근방문일자</TD>												
				<TD width=210 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY2,this.form.FromMM2,this.form.FromDD2) name=FromYY2></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY2,this.form.FromMM2,this.form.FromDD2) name=FromMM2></SELECT> 월 <SELECT name=FromDD2></SELECT> 일 </TD>
				<TD width=20 align=center> ~ </TD>
				<TD width=210 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY2,this.form.ToMM2,this.form.ToDD2) name=ToYY2></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY2,this.form.ToMM2,this.form.ToDD2) name=ToMM2></SELECT> 월 <SELECT name=ToDD2></SELECT> 일</TD>				
			</tr>
			<!--tr height=40>
				<td colspan=4>
					<table height=30 width=650>
					<tr>
						<td width=30%><li>나이 : </td>
					<td colspan=2>
						<SELECT name=age_from>
						<option value=0 selected>0</option></SELECT>세부터 <SELECT name=age_to><option value=0 selected>0</option></SELECT> 세까지 <span style='width:20'></span><FONT color=#234e8d>※ 모두 0인 경우 나이 제한없음</FONT> 					
					</td>
					</tr>
					</table>
				</td>				
			</tr-->
			<tr height=190 valign=bottom>
				<td colspan=5>
				<table cellpadding=0 cellspacing=0>
				<tr>
				<td width=30% >
					<table cellpadding=0 cellspacing=0 border=0>
						<tr><td><li>지역</td></tr>
						<tr>
							<td align='center'><input type='button' name='a_button' value='전체지역선택' style='width:100%'  onclick=\"for(i=0;i<document.forms['target_form'].a_code.options.length;i++) document.forms['target_form'].a_code.options[i].selected=true;\" /><br />
							<select name='a_code' size='10' multiple style='width:120px;'>
							<option value='0'>전체</option>
							<option value='1'>서울특별시</option>
							<option value='2'>부산광역시</option>
							<option value='3'>인천광역시</option>
							<option value='4'>대전광역시</option>
							<option value='5'>대구광역시</option>
							<option value='6'>광주광역시</option>
							<option value='7'>울산광역시</option>
							<option value='8'>경기도</option>
							<option value='9'>강원도</option>
							<option value='10'>충청북도</option>
							<option value='11'>충청남도</option>
							<option value='12'>경상북도</option>
							<option value='13'>경상남도</option>
							<option value='14'>전라북도</option>
							<option value='15'>전라남도</option>
							<option value='16'>제주도</option>
							<option value='17'>해외</option>
							</select>
							</td>
						</tr>
					</table>
				</td>
				<td width=30%>
					<table cellpadding=0 cellspacing=0 border=0>
						<tr><td><li>학력</td></tr>
						<tr>
							<td align='center'>
							<input type='button' name='h_button' value='전체학력선택' style='width:100%' onclick=\"for(i=0;i<document.forms['target_form'].h_code.options.length;i++)document.forms['target_form'].h_code.options[i].selected=true;\" /><br />
							<select name='h_code' size='10' multiple style='width:120px;'>
							<option value='0'>전체</option>
							<option value='1'>초등학교재학</option>
							<option value='2'>초등학교졸업</option>
							<option value='3'>중학교재학</option>
							<option value='4'>중학교졸업</option>
							<option value='5'>고등학교재학</option>
							<option value='6'>고등학교졸업</option>
							<option value='7'>전문대재학</option>
							<option value='8'>전문대졸업</option>
							<option value='9'>대학재학</option>
							<option value='10'>대학졸업</option>
							<option value='11'>대학원(석사)재학</option>
							<option value='12'>대학원(석사)졸업</option>
							<option value='13'>대학원(박사)재학</option>
							<option value='14'>대학원(박사)졸업</option>
							<option value='15'>기타</option></select>
							</td>
						</tr>
					</table>
				</td>
				<td width=30%>
					<table cellpadding=0 cellspacing=0 border=0>
						<tr><td><li>직업</td></tr>
						<tr>
							<td align='center'>
							<input type='button' name='j_button' value='전체직업선택' style='width:100%' onclick=\"for(i=0;i<document.forms['target_form'].j_code.options.length;i++)document.forms['target_form'].j_code.options[i].selected=true;\" /><br />
							<select name='j_code' size='10' multiple style='width:120px;'>
							<option value='0'>전체</option>
							<option value='1'>회사원</option>
							<option value='2'>자영업</option>
							<option value='3'>대학(원)생</option>
							<option value='4'>학생(초/중/고)</option>
							<option value='5'>주부</option>
							<option value='6'>공무원</option>
							<option value='7'>교직자</option>
							<option value='8'>전문직</option>
							<option value='9'>의료인</option>
							<option value='10'>법조인</option>
							<option value='11'>종교인</option>
							<option value='12'>언론</option>
							<option value='13'>농/축/수산/임업</option>
							<option value='14'>금융/증권/보험</option>
							<option value='15'>유통</option>
							<option value='16'>정보통신</option>
							<option value='17'>건설</option>
							<option value='18'>제조</option>
							<option value='19'>서비스</option>
							<option value='20'>군인</option>
							<option value='21'>방송인</option>
							<option value='22'>예술가</option>
							<option value='23'>부동산</option>
							<option value='24'>운송</option>
							<option value='25'>일용직</option>
							<option value='26'>무직</option>
							<option value='27'>기타</option>
							</select>
							</td>
						</tr>
					</table>
				</td>
				<td width=30%>
					<table cellpadding=0 cellspacing=0 border=0>
						<tr><td><li>나이</td></tr>
						<tr>
							<td align='center'>
							<input type='button' name='c_button' value='전체컨텐츠선택' style='width:100%'  onclick=\"for(i=0;i<document.forms['target_form'].age_code.options.length;i++)document.forms['target_form'].age_code.options[i].selected=true;\" /><br />
							<select name='age_code' size='10' multiple style='width:120px;'>
							<option value='1:200'>전체</option>
							<option value='1:9'>1세~9 세</option>
							<option value='10:19'>10세~19세</option>
							<option value='20:29'>20세~29세</option>
							<option value='30:39'>30세~39세</option>
							<option value='40:49'>40세~49세</option>
							<option value='50:59'>50세~59세</option>
							<option value='60:69'>60세~69세</option>
							<option value='70:79'>70세~79세</option>
							<option value='80:89'>80세~89세</option>
							<option value='90:99'>90세~99세</option>
							<option value='100:200'>100세~</option>
							</select>
							</td>
						</tr>
					</table>
				</td>
				<!--td width=30%>
					<table cellpadding=0 cellspacing=0 border=0>
						<tr><td><li>컨텐츠</td></tr>
						<tr>
							<td align='center'>
							<input type='button' name='c_button' value='전체컨텐츠선택' style='width:100%'  onclick=\"for(i=0;i<document.forms['target_form'].c_code.options.length;i++)document.forms['target_form'].c_code.options[i].selected=true;\" /><br />
							<select name='c_code' size='10' multiple style='width:120px;'>							
							<option value='1'>전체</option>
							<option value='2'>의류/잡화</option>
							<option value='3'>컴퓨터</option>
							<option value='4'>생활용품</option>
							<option value='5'>스포츠/레포츠</option>
							<option value='6'>도서</option>
							<option value='7'>주방용품</option>
							<option value='8'>사무용품</option>
							<option value='9'>광학기기</option>
							<option value='10'>유아용품</option>
							<option value='11'>성인용품</option>
							<option value='12'>출산용품</option>
							<option value='23'>기타</option>
							</select>
							</td>
						</tr>
					</table>
				</td-->
				</tr>
				</table>
				</td>
				<td valign=bottom align=right width=150><input type=image src='../image/b_save.gif' border=0></td>
			</tr>
			
			<tr height=20><td colspan=4></td></tr>
			<tr><td colspan=4 align=right></td></tr>
			<tr height=20><td colspan=4></td></tr>								
		</table></form>";
	
return $mstring;	
}
?>