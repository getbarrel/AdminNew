<?
include("../class/layout.class");


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
<Script Language='JavaScript'>
function SubmitX(frm){
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}

	//frm.content.value = iView.document.body.innerHTML;	
	return true;
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
	var frm = document.smssend;
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

function searchMember(frm){
	var xmlHttp = new XMLHttp();
	var birthday = null;
	var region = null;
	var sex = '';
	var age = '';
	var smssend_yn = null;
	var vlevel = '';
	
	//alert(frm.birthday_yn.length);
	for(i=0;i<frm.birthday_yn.length;i++){
		if(frm.birthday_yn[i].checked){
			birthday = frm.birthday_yn[i].value;			
		}
	}
	
	for(i=0;i<frm.region.length;i++){
		if(frm.region[i].selected){
			region = frm.region[i].value;			
		}
	}
	
	for(i=0;i<frm.age.length;i++){
		if(frm.age[i].selected){
			age = frm.age[i].value;			
		}
	}
	
	for(i=0;i<frm.sex.length;i++){
		if(frm.sex[i].checked){
			sex = frm.sex[i].value;			
		}
	}
	
	
	for(i=0;i<frm.smssend_yn.length;i++){
		if(frm.smssend_yn[i].checked){
			smssend_yn = frm.smssend_yn[i].value;			
		}
	}
	
	if(frm.regdate.checked){
		var startDate = frm.FromYY.value+''+frm.FromMM.value+''+frm.FromDD.value;
		var endDate = frm.ToYY.value+''+frm.ToMM.value+''+frm.ToDD.value;
	}else{
		var startDate = '';
		var endDate = '';
	}
	
	if(frm.visitdate.checked){
		var vstartDate = frm.vFromYY.value+''+frm.vFromMM.value+''+frm.vFromDD.value;
		var vendDate = frm.vToYY.value+''+frm.vToMM.value+''+frm.vToDD.value;
	}else{
		var vstartDate = '';
		var vendDate = '';
	}
	search_type = frm.search_type.value;
	search_text = frm.search_text.value;
	
	var sURL = '../member/searchMember.xml.php?region='+region+'&birthday_yn='+birthday+'&age='+age+'&sex='+sex+'&vlevel='+vlevel+'&smssend_yn='+smssend_yn+'&startDate='+startDate+'&endDate='+endDate+'&vstartDate='+vstartDate+'&vendDate='+vendDate+'&search_type='+search_type+'&search_text='+search_text;
	//alert(sURL);
	//member/searchMember.xml.php?birthday_yn=&region=서울&age=&sex=&vlevel=&smssend_yn=Y&startDate=&endDate=&vstartDate=&vendDate=&search_type=name&search_text=
	ret = xmlHttp.request('get', sURL, false, null);
	
	var xmlDoc = new ActiveXObject('Msxml2.DOMDocument');
	xmlDoc.async = false;
	xmlDoc.loadXML(xmlHttp.responseText);
	//alert(xmlHttp.responseText);
	var err = xmlDoc.parseError;

	if (err.errorCode != 0)
		throw new Error('XML 문서 해석 실패 - ' + err.reason);	
	
	var xsl = new ActiveXObject('Microsoft.XMLDOM');
	xsl.async = false;
	xsl.load('../member/search_member_sms.xsl');
	
	
	document.getElementById('member_count').innerHTML = xmlDoc.getElementsByTagName('total')[0].firstChild.data
	document.getElementById('searchMemberArea').innerHTML = xmlDoc.transformNode(xsl);
	
	var err = xmlDoc.parseError;
	if (err.errorCode != 0)
		throw new Error('XSL 문서 해석 실패 - ' + err.reason);
}

function Content_Input(){	
	document.smssend.content.value = document.smssend.mall_companyinfo.value;		
}

</Script>
";
$db = new Database;
$db->query("SELECT * FROM ".TBL_SHOP_MAILSEND_CONFIG." where mc_ix= '$mc_ix'");
$db->fetch();

if($db->total){
	$mc_ix = $db->dt[mc_ix];
	$mc_title = $db->dt[mc_title];
	$mc_mail_title = $db->dt[mc_mail_title];
	$mc_mail_text = $db->dt[mc_mail_text];
	$mc_code = $db->dt[mc_code];
	$mc_mail_adminsend_yn = $db->dt[mc_mail_adminsend_yn];
	$mc_mail_usersend_yn = $db->dt[mc_mail_usersend_yn];
	$act = "update";
}else{
	$act = "insert";	
	$mc_mail_adminsend_yn = "Y";
	$mc_mail_usersend_yn = "Y";
	$birthday_yn = "N";
	$sex = "";
	$smssend_yn = "Y";
}

if($mc_code != ""){
	$thisfile = load_template($_SERVER["DOCUMENT_ROOT"]."/shop_templete/".SiteUseTemplete($HTTP_HOST)."/ms_mail_".$mc_code.".htm");
}

$Contents ="
		<table width='100%' border='0' cellspacing='0' cellpadding='5' height='25'>
		<form name=smssend action='sms.send.php' method='post' onsubmit='return SubmitX(this);'>
                    <input type='hidden' name=act value='".$act."'>
                    <input type='hidden' name=mc_ix value='".$mc_ix." '>
		    <tr>
		    	<td align='left' colspan=6 > ".GetTitleNavigation("SMS 발송", "회원관리 > SMS 발송 ")."</td>
		    </tr>		   
                    <tr height=10><td colspan=4 align=right style='padding:10px;'><!--input type=image src='../image/b_save.gif' border=0> <a href='mail.manage.list.php'><img src='../image/b_cancel.gif' border=0></a--></td></tr>
		    	
		    <tr height=1>
		    	<td colspan=2 width='40%' class='dot-x'></td>
		    	<td rowspan=18 style='padding:0 5 0 5' valign=top>
		    		<div style='z-index:2;position:absolute;'><div style='z-index:10;position:relative;left:-150px;top:20px;padding:5px;background-color:#ffffff'><img src='../image/bt_search.gif' align=absmiddle onclick='searchMember(document.smssend)' style='cursor:hand;'></div></div>
		    	</td>
		    	<td rowspan=16 width='60%' valign=top>
		    		<table cellpadding=0 cellspacing=0 width='100%' height='100%'>
		    			<tr height=27><td class='s_td ' align=center>검색된 회원 목록 </td><td id='member_count' class='e_td' style='padding-right:10px;'>&nbsp;</td></tr>
		    			<tr>
		    				<td width='100%' height='100%' colspan=2><div id='searchMemberArea' style='overflow:auto;height:100%;width:100%;'></div></td>
		    			</tr>
		    		</table>
		    		
		    	</td>
		    </tr>
		    <tr  height=25>
		      <td width='15%' bgcolor='#efefef' align=center>지역선택</td>
		      <td align=left >
		      <select name='region' >
                        <option value=''>-- 선택 --</option>
                        <option value='서울' >서울</option>
                        <option value='충북' >충북</option>
                        <option value='충남' >충남</option>
                        <option value='전북' >전북</option>
                        <option value='제주' >제주</option>
                        <option value='전남' >전남</option>
                        <option value='경북' >경북</option>
                        <option value='경남' >경남</option>
                        <option value='경기' >경기</option>
                        <option value='부산' >부산</option>
                        <option value='대구' >대구</option>
                        <option value='인천' >인천</option>
                        <option value='광주' >광주</option>
                        <option value='대전' >대전</option>
                        <option value='울산' >울산</option>
                        <option value='강원' >강원</option>
                        </select>
		      </td>
		    </tr>
		    <tr height=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center>연령</td>
		      <td align=left  >
		      <select name='age' >
                        <option value=''> -- 선택 -- </option>
                        <option value='10' >10대</option>
                        <option value='20' >20대</option>
                        <option value='30' >30대</option>
                        <option value='40' >40대</option>
                        <option value='50' >50대</option>
                        <option value='60' >60대</option>
                        </select>
		      </td>
		      
		    </tr>				    		
		    <tr height=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center>오늘 생일 </td>
		      <td align=left >
		      <input type=radio name='birthday_yn' value='Y' id='birthday_y'  ".CompareReturnValue("Y",$birthday_yn,"checked")."><label for='birthday_y'>예</label><input type=radio name='birthday_yn' value='N' id='birthday_n' ".CompareReturnValue("N",$birthday_yn,"checked")."><label for='birthday_n'>아니오(전체)</label> 
		      </td>			      		
		    </tr>
		    <tr height=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center>성별검색 </td>
		      <td align=left >
		      <input type=radio name='sex' value='' id='sex_all'  ".CompareReturnValue("",$sex,"checked")."><label for='sex_all'>모두</label>
		      <input type=radio name='sex' value='M' id='sex_man'  ".CompareReturnValue("M",$sex,"checked")."><label for='sex_man'>남자</label>
		      <input type=radio name='sex' value='W' id='sex_women' ".CompareReturnValue("W",$sex,"checked")."><label for='sex_women'>여자</label> 
		      </td>			      		
		    </tr>		
		    <tr hegiht=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center>SMS 발송여부 </td>
		      <td align=left >
		      <input type=radio name='smssend_yn' value='Y' id='smssend_y'  ".CompareReturnValue("Y",$smssend_yn,"checked")."><label for='smssend_y'>수신회원만</label><input type=radio name='smssend_yn' value='N' id='smssend_n' ".CompareReturnValue("N",$smssend_yn,"checked")." disabled><label for='smssend_n'>수신거부회원포함</label> 
		      </td>			
		    </tr>
		    <tr hegiht=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center><label for='regdate'>가입일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.smssend);'></td>
		      <td align=left >
		      	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>		
				<tr>					
					<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월 <SELECT name=FromDD></SELECT> 일 </TD>
					<TD width=20 align=center> ~ </TD>
					<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월 <SELECT name=ToDD></SELECT> 일</TD>					
				</tr>		
			</table>	
		      </td>			
		    </tr>		    
		    <tr hegiht=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center><label for='visitdate'>최근방문일</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.smssend);'></td>
		      <td align=left >
		      	<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff>		
							<tr>
								<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
								<TD width=20 align=center> ~ </TD>
								<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월 <SELECT name=vToDD></SELECT> 일</TD>					
							</tr>		
						</table>	
		      </td>			
		    </tr>		    
		    <tr hegiht=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center>발송형태</td>
		      <td align=left >
		      	<input type='radio' name='send_type' id='send_type_0' value='0' onclick=\"$('reservation_datetime').style.display='none';\" checked><label for='send_type_0'>즉시발송</label>
		      	<input type='radio' name='send_type' id='send_type_1' value='1' onclick=\"$('reservation_datetime').style.display='block';\" ><label for='send_type_1'>예약발송</label>
		      	<table cellpadding=0 cellspacing=2 border=0 width=100% bgcolor=#ffffff id='reservation_datetime' style='display:none;'>				      		
							<tr>
								<TD width=200 nowrap><SELECT onchange=javascript:onChangeDate(this.form.sFromYY,this.form.sFromMM,this.form.sFromDD) name=sFromYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.sFromYY,this.form.sFromMM,this.form.sFromDD) name=sFromMM></SELECT> 월 <SELECT name=sFromDD></SELECT> 일 </TD>
								<td>
									<input type=text name='sDateTime' value='".(date("His"))."' style='width:100px' > 
								</td>
							</tr>		
							<tr>
								<td></td>
		      			<td align=left>
		      				<span >예) 18시 30분 10초  183010 형태로 입력</span>
		      			</td>
		      		</tr>
						</table>	
		      </td>			
		    </tr>		    
		    <tr hegiht=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center>조건검색 </td>
		      <td align=left >
		      <select name=search_type>
		      <option value='name'>고객명</option>
		      <option value='id'>아이디</option>
		      <option value='tel'>전화번화</option>
		      <option value='pcs'>휴대전화</option>
		      </select>
		      <input type=text name='search_text' value='".$search_text."' style='width:50%' >
		      </td>			
		    </tr>
		    <tr hegiht=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center><b>SMS 내용 <img src='".$required3_path."'></b></td>
		      <td align=left width='50%' nowrap>
		      <table>
		      	<tr>
		      		<td><textarea  name='mc_sms_text' style='font-size:11px;width:120;height:130px;overflow:auto;' align=left validation='true' title='SMS내용'></textarea></td>
		      		<td valign=top>
				      <b>{mem_name}</b> 해당회원의 <b>이름</b>으로 치환됩니다.<br>
				      <b>{mem_id}</b> 해당회원의 <b>아이디</b>로 치환됩니다.
				</td>
			</tr>
			</table>
		      </td>
		    </tr>		
		    <tr height=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=30><td colspan=4 align=right class=small> ※  검색된 회원이 100 명이상이면 미리보기 리스트에는 100명 까지만 표시됩니다.</td></tr>
                    <tr>
                      <td height='30' colspan='4'>
                      </td>
                    </tr>
                   
                    <tr> 
                      <td bgcolor='D0D0D0' height='1' colspan='4'></td>
                    </tr>
                    <tr><td colspan=4 align=right style='padding:10px;'><input type=image src='../image/btn_send.gif' border=0> <a href='mail.manage.list.php'><img src='../image/b_cancel.gif' border=0></a></td></tr>
                    <textarea name='mall_companyinfo'  style='display:none'>".$mc_mail_text."</textarea></form>
                  </table>";
                  
$LO = new LayOut;
$LO->addScript = "<script language='JavaScript' src='sms.write.js'></script><script language='JavaScript' src='/js/XMLHttp.js'></script><script language='JavaScript' src='../webedit/webedit.js'></script><script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
$LO->OnloadFunction = "init();MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')";//showSubMenuLayer('storeleft');
$LO->strLeftMenu = member_menu();
$LO->Navigation = "HOME > 회원관리 > SMS 발송";
$LO->strContents = $Contents;
$LO->PrintLayOut();

?>