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
	
	frm.content.value = iView.document.body.innerHTML;	
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
	var frm = document.mailsend;
	Content_Input();
	Init(frm);
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
	
	frm.birYY.disabled = true;
	frm.birMM.disabled = true;
	frm.birDD.disabled = true;	
}

function searchMember(frm){
	var xmlHttp = new XMLHttp();
	var birthday = null;
	var region = null;
	var sex = '';
	var age = '';
	var mailsend_yn = null;
	
	//alert(frm.birthday_yn.length);
	/*for(i=0;i<frm.birthday_yn.length;i++){
		if(frm.birthday_yn[i].checked){
			birthday = frm.birthday_yn[i].value;			
		}
	}*/

	if(frm.bir.checked){
		//var startDate = frm.FromYY.value+''+frm.FromMM.value+''+frm.FromDD.value;
		var birthday = frm.birMM.value+''+frm.birDD.value;
	}else{
		var birthday = '';
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
	
	for(i=0;i<frm.mailsend_yn.length;i++){
		if(frm.mailsend_yn[i].checked){
			mailsend_yn = frm.mailsend_yn[i].value;			
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
	
	var sURL = '../member/searchMember.xml.php?birthday_yn='+birthday+'&region='+region+'&age='+age+'&sex='+sex+'&mailsend_yn='+mailsend_yn+'&startDate='+startDate+'&endDate='+endDate+'&vstartDate='+vstartDate+'&vendDate='+vendDate+'&search_type='+search_type+'&search_text='+search_text;
	//document.write(sURL);
	ret = xmlHttp.request('get', sURL, false, null);
	
	var xmlDoc = new ActiveXObject('Msxml2.DOMDocument');
	xmlDoc.async = false;
	xmlDoc.loadXML(xmlHttp.responseText);
	
	var err = xmlDoc.parseError;

	if (err.errorCode != 0)
		throw new Error('XML 문서 해석 실패 - ' + err.reason);	
	
	var xsl = new ActiveXObject('Microsoft.XMLDOM');
	xsl.async = false;
	xsl.load('../member/search_member.xsl');
	
	
	document.getElementById('member_count').innerHTML = xmlDoc.getElementsByTagName('total')[0].firstChild.data
	document.getElementById('searchMemberArea').innerHTML = xmlDoc.transformNode(xsl);
	
	var err = xmlDoc.parseError;
	if (err.errorCode != 0)
		throw new Error('XSL 문서 해석 실패 - ' + err.reason);
}

function Content_Input(){	
	document.mailsend.content.value = document.mailsend.mall_companyinfo.value;		
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
	$mailsend_yn = "Y";
}

if($mc_code != ""){
	$thisfile = load_template($_SERVER["DOCUMENT_ROOT"]."/shop_templete/".SiteUseTemplete($HTTP_HOST)."/ms_mail_".$mc_code.".htm");
}

$Contents ="
		<table width='100%' border='0' cellspacing='0' cellpadding='5' height='25'>
		<form name=mailsend action='mail.send.php' method='post' onsubmit='return SubmitX(this);'>
                    <input type='hidden' name=act value='".$act."'>
                    <input type='hidden' name=mc_ix value='".$mc_ix." '>
		    <tr>
		    	<td align='left' colspan=6 > ".GetTitleNavigation("메일발송", "회원관리 > 메일발송 ")."</td>
		    </tr>		   
                    <tr height=10><td colspan=4 align=right style='padding:10px;'><!--input type=image src='../image/b_save.gif' border=0> <a href='mail.manage.list.php'><img src='../image/b_cancel.gif' border=0></a--></td></tr>
		    	
		    <tr height=1>
		    	<td colspan=2 width='40%' class='dot-x'></td>
		    	<td rowspan=18 style='padding:0 5 0 5' valign=top>
		    		<div style='z-index:2;position:absolute;'><div style='z-index:10;position:relative;left:-150px;top:20px;padding:5px;background-color:#ffffff'><img src='../image/bt_search.gif' align=absmiddle onclick='searchMember(document.mailsend)' style='cursor:hand;'></div></div>
		    	</td>
		    	<td rowspan=18 width='60%' valign=top>
		    		<table cellpadding=0 cellspacing=0 width='100%' height='100%'>
		    			<tr height=27><td class='s_td ' align=center>검색된 회원 목록 </td><td id='member_count' class='e_td' style='padding-right:10px;'>&nbsp;</td></tr>
		    			<tr>
		    				<td width='100%' height='100%' colspan=2><div id='searchMemberArea' style='overflow:auto;height:100%;width:100%;'></div></td>
		    			</tr>
		    		</table>
		    		
		    	</td>
		    </tr>
		    <tr  height=27 >
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
		      <td bgcolor='#efefef' align=center><label for='bir'>생일</label><input type='checkbox' name='bir' id='bir' value='1' onclick='ChangeBirDate(document.mailsend);'> </td>
		      <td align=left style='padding-left:5px;'>
				 <SELECT onchange=javascript:onChangeDate(this.form.birYY,this.form.birMM,this.form.birDD) name=birYY ></SELECT> 년 <SELECT onchange=javascript:onChangeDate(this.form.birYY,this.form.birMM,this.form.birDD) name=birMM></SELECT> 월 <SELECT name=birDD></SELECT> 일
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
		      <td bgcolor='#efefef' align=center>메일발송여부 </td>
		      <td align=left >
		      <input type=radio name='mailsend_yn' value='Y' id='mailsend_y'  ".CompareReturnValue("Y",$mailsend_yn,"checked")."><label for='mailsend_y'>수신회원만</label><input type=radio name='mailsend_yn' value='N' id='mailsend_n' ".CompareReturnValue("N",$mailsend_yn,"checked")."><label for='mailsend_n'>수신거부회원포함</label> 
		      </td>			
		    </tr>
		    <tr hegiht=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center><label for='regdate'>가입일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.mailsend);'></td>
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
		      <td bgcolor='#efefef' align=center><label for='visitdate'>최근방문일</label><input type='checkbox' name='visitdate' id='visitdate' value='1' onclick='ChangeVisitDate(document.mailsend);'></td>
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
		      <td bgcolor='#efefef' align=center>조건검색 </td>
		      <td align=left >
		      <select name=search_type>
		      <option value='name'>고객명</option>
		      <option value='id'>아이디</option>
		      <option value='jumin'>주민번호</option>
		      <option value='tel'>전화번화</option>
		      <option value='pcs'>휴대전화</option>
		      </select>
		      <input type=text name='search_text' value='".$search_text."' style='width:50%' >
		      </td>			
		    </tr>
		    <tr hegiht=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=27>
		      <td bgcolor='#efefef' align=center><b>메일제목 <img src='".$required3_path."'></b></td>
		      <td align=left width='50%' nowrap>
		      <input type=text name='mail_title' value='".$mail_title."' style='width:50%' validation='true' title='메일제목'> &nbsp;<!--input type='radio' name='write_mode' id='write_mode_new' value=1 checked><label for='write_mode_new'>새로작성</label> &nbsp;<input type='radio' name='write_mode' id='write_mode_select' value=2><label for='write_mode_select'>메일선택</label-->&nbsp;
		      </td>
		    </tr>		
		    <tr height=1><td colspan=2 class='dot-x'></td></tr>
		    <tr height=30><td colspan=4 align=right class=small> ※  검색된 회원이 100 명이상이면 미리보기 리스트에는 100명 까지만 표시됩니다.</td></tr>
		    <tr height=2><td colspan=4 ></td></tr>
                    <tr>
                      <td height='30' colspan='4'>						      
						      <table id='tblCtrls' width='100%' border='0' cellspacing='1' cellpadding='0' align='center'>
						        <tr> 
						          <td bgcolor='F5F6F5'>
									 <table width='100%' border='0' cellspacing='0' cellpadding='0'>
						              <tr>
						                <td width='18%' height='56'>
											 	<table width='100%' height='56' border='0' align='center' cellpadding='0' cellspacing='0'>
						                    <tr align='center' valign='bottom'> 
						                      <td height='26'><a href='javascript:doBold();' onMouseOver=\"MM_swapImage('editImage1','','../webedit/image/wtool1_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool1.gif' name='editImage1' width='19' height='18' border='0' id='editImage1'></a></td>
						                      <td><a href='javascript:doItalic();' onMouseOver=\"MM_swapImage('editImage2','','../webedit/image/wtool2_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool2.gif' name='editImage2' width='19' height='18' border='0' id='editImage2'></a></td>
						                      <td><a href='javascript:doUnderline();' onMouseOver=\"MM_swapImage('editImage3','','../webedit/image/wtool3_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool3.gif' name='editImage3' width='19' height='18' border='0' id='editImage3'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='3' colspan='3'></td>
						                    </tr>
						                    <tr align='center' valign='top'> 
						                      <td height='27'><a href='javascript:doLeft();' onMouseOver=\"MM_swapImage('editImage8','','../webedit/image/wtool8_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool8.gif' name='editImage8' width='19' height='18' border='0' id='editImage8'></a></td>
						                      <td><a href='javascript:doCenter();' onMouseOver=\"MM_swapImage('editImage9','','../webedit/image/wtool9_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool9.gif' name='editImage9' width='19' height='18' border='0' id='editImage9'></a></td>
						                      <td><a href='javascript:doRight();' onMouseOver=\"MM_swapImage('editImage10','','../webedit/image/wtool10_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool10.gif' name='editImage10' width='19' height='18' border='0' id='editImage10'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='19%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td width='100%' height='27' align='center' valign='bottom'><a href='javascript:doFont();' onMouseOver=\"MM_swapImage('editImage4','','../webedit/image/wtool4_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool4.gif' name='editImage4' width='84' height='22' border='0' id='editImage4'></a></td>
						                    </tr>
						                    <tr>
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doSize();' onMouseOver=\"MM_swapImage('editImage11','','../webedit/image/wtool11_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool11.gif' name='editImage11' width='84' height='22' border='0' id='editImage11'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='20%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doForcol();' onMouseOver=\"MM_swapImage('editImage5','','../webedit/image/wtool5_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool5.gif' name='editImage5' width='95' height='22' border='0' id='editImage5'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doBgcol();' onMouseOver=\"MM_swapImage('editImage12','','../webedit/image/wtool12_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool12.gif' name='editImage12' width='95' height='22' border='0' id='editImage12'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='18%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doImage();' onMouseOver=\"MM_swapImage('editImage6','','../webedit/image/wtool6_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool6.gif' name='editImage6' width='73' height='22' border='0' id='editImage6'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doTable();' onMouseOver=\"MM_swapImage('editImage13','','../webedit/image/wtool13_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool13.gif' name='editImage13' width='73' height='22' border='0' id='editImage13'></a></td>
						                    </tr>
						                  </table>
											 </td>
						                <td width='2'><img src='../webedit/image/bar.gif' width='2' height='39' align='absmiddle'></td>
						                <td width='25%'>
											 	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
						                    <tr> 
						                      <td height='27' align='center' valign='bottom'><a href='javascript:doLink();' onMouseOver=\"MM_swapImage('editImage7','','../webedit/image/wtool7_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool7.gif' name='editImage7' width='74' height='22' border='0' id='editImage7'></a></td>
						                    </tr>
						                    <tr> 
						                      <td height='2'></td>
						                    </tr>
						                    <tr> 
						                      <td height='27' align='center' valign='top'><a href='javascript:doMultilink();' onMouseOver=\"MM_swapImage('editImage14','','../webedit/image/wtool14_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/wtool14.gif' name='editImage14' width='111' height='22' border='0' id='editImage14'></a></td>
						                    </tr>
						                  </table>
											 </td>
						              </tr>
						            </table>
									 </td>
						        </tr>
						      </table>
						      <input type='hidden' name='content' value=''>
						      <iframe align='right' id='iView' style='width: 100%; height:410;' scrolling='YES' hspace='0' vspace='0'></iframe>
						      <!-- html편집기 메뉴 종료 -->						      
                      </td>
                    </tr>
                    <tr style='display:block;'>
          	          <td width='120' height='25' align='center' bgcolor='#F0F0F0'></td>
          		       <td colspan='4' align='right'>&nbsp; 
						      <a href='javascript:doToggleText();' onMouseOver=\"MM_swapImage('editImage15','','../webedit/image/bt_html_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_html.gif' name='editImage15' width='93' height='23' border='0' id='editImage15'></a>
          			      <a href='javascript:doToggleHtml();' onMouseOver=\"MM_swapImage('editImage16','','../webedit/image/bt_source_1.gif',1)\" onMouseOut='MM_swapImgRestore()'><img src='../webedit/image/bt_source.gif' name='editImage16' width='93' height='23' border='0' id='editImage16'></a>
                      </td>
                    </tr>
                    <tr> 
                      <td bgcolor='D0D0D0' height='1' colspan='4'></td>
                    </tr>
                    <tr><td colspan=4 align=right style='padding:10px;'><input type=image src='../image/btn_send.gif' border=0> <a href='mail.manage.list.php'><img src='../image/b_cancel.gif' border=0></a></td></tr>
                    <textarea name='mall_companyinfo'  style='display:none'>".$mc_mail_text."</textarea></form>
                  </table>";
                  
$LO = new LayOut;
$LO->addScript = "<script language='JavaScript' src='mail.write.js'></script><script language='JavaScript' src='/js/XMLHttp.js'></script><script language='JavaScript' src='../webedit/webedit.js'></script><script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
$LO->OnloadFunction = "init();MM_preloadImages('../webedit/image/wtool1_1.gif','../webedit/image/wtool2_1.gif','../webedit/image/wtool3_1.gif','../webedit/image/wtool4_1.gif','../webedit/image/wtool5_1.gif','../webedit/image/wtool6_1.gif','../webedit/image/wtool7_1.gif','../webedit/image/wtool8_1.gif','../webedit/image/wtool9_1.gif','../webedit/image/wtool11_1.gif','../webedit/image/wtool13_1.gif','../webedit/image/wtool10_1.gif','../webedit/image/wtool12_1.gif','../webedit/image/wtool14_1.gif','../webedit/image/bt_html_1.gif','../webedit/image/bt_source_1.gif')";//showSubMenuLayer('storeleft');
$LO->strLeftMenu = member_menu();
$LO->Navigation = "HOME > 회원관리 > 메일발송";
$LO->strContents = $Contents;
$LO->PrintLayOut();

?>