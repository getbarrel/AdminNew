<?
include("../class/layout.class");
$db = new Database;
$mdb = new Database;

include "member_query.php";
if($info_type == ""){
$info_type = 'basic';
}
$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
		<col width='15%' />
		<col width='30%' />
		<col width='*' />
	  <tr >
		<td align='left' colspan=3> ".GetTitleNavigation("기초부서관리", "기초관리 > 기초부서관리 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=3 style='padding-bottom:12px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "basic" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?info_type=basic&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;은행 관리&nbsp;&nbsp; </a> <span style='padding-left:2px' class='helpcloud' help_width='410' help_height='70' help_html='사업자 정보를 작성하여 등록한 후에 해당 사업자에 관리자화면 로그인 정보를 발급해 줄 수 있습니다.<br />사업자 정보 추가 후 [목록관리] - [사용자 추가]를 통해 관리자에 계정을 발급해 주시기 바랍니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_02' ".($info_type == "department_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($company_id == ""){
							$Contents01 .= "<a href='?info_type=department_info&company_id=".$company_id."&mmode=$mmode'>  &nbsp;&nbsp;카드 관리&nbsp;&nbsp;  </a>";
						}else{
							$Contents01 .= "<a href='?info_type=department_info&company_id=".$company_id."&mmode=$mmode'>  &nbsp;&nbsp;카드 관리&nbsp;&nbsp;   </a>";
						}
						$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_03' ".($info_type == "post_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($company_id == ""){
							$Contents01 .= "<a href='?info_type=post_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;현금 관리&nbsp;&nbsp; </a>";
						}else{
							$Contents01 .= "<a href='?info_type=post_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;현금 관리&nbsp;&nbsp; </a>";
						}
						$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_04' ".($info_type == "position_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						if($company_id == ""){
							$Contents01 .= "<a href='?info_type=position_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;어음 관리&nbsp;&nbsp; </a>";
						}else{
							$Contents01 .= "<a href='?info_type=position_info&company_id=".$company_id."&mmode=$mmode'>&nbsp;&nbsp;어음 관리&nbsp;&nbsp; </a>";
						}
						$Contents01 .= "

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
				<td style='width:600px;text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>";

if($info_type == "basic" || $info_type == ""){
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>은행 설정</b></div>")."</td>
	  </tr>
	 </table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
		<tr>
			<td class='input_box_title'> <b>은행코드 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='true' title='은행코드' com_numeric=true>
			</td>
			<td class='input_box_title'> <b>은행선택 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
			<select name='age' >
					<option value=''> -- 부서그룹 선택 -- </option>
					<option value='10' ".CompareReturnValue("10",$age,"selected").">10대</option>
					<option value='20' ".CompareReturnValue("20",$age,"selected").">20대</option>
					<option value='30' ".CompareReturnValue("30",$age,"selected").">30대</option>
					<option value='40' ".CompareReturnValue("40",$age,"selected").">40대</option>
					<option value='50' ".CompareReturnValue("50",$age,"selected").">50대</option>
					<option value='60' ".CompareReturnValue("60",$age,"selected").">60대</option>
				</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>통장명 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='true' title='통장명' com_numeric=true>
			</td>
			<td class='input_box_title'> <b>예금주 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_fax' value='".$db->dt[com_fax]."'  style='width:200px'>
			</td>
		</tr>
		<tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>계좌번호 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan='3'><input type=text class='textbox' name='dp_level' value='".$db->dt[dp_level]."' style='width:200px;' validation='true' title='계좌번호'>
		
	    </td>
	  </tr>
	   <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>기초잔액  <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan='3'><input type=text class='textbox' name='dp_level' value='".$db->dt[dp_level]."' style='width:60px;' validation='true' title='기초잔액'>
		&nbsp;&nbsp;현재 은행잔액을 바탕으로 정산 처리 됩니다.
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>노출순서  <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan='3'><input type=text class='textbox' name='dp_level' value='".$db->dt[dp_level]."' style='width:60px;' validation='true' title='노출순서'>
		&nbsp;&nbsp;노출순서에 의해서 셀렉트 박스의 및 리스트 노출 순서가 정해집니다.
	    </td>
	  </tr>

	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용유무  <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan='3'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>미사용</label>
	    </td>
	  </tr>
	  <tr>
		  <td class='input_box_title'> <b>기타사항</b></td>
			<td class='input_box_item' colspan='3'>
			<textarea name='edit_data' value='".$db->dt[com_email]."'  style='width:90%' validation='false' title='기타사항' email=true></textarea>
			</td>
		  </tr>
</table>";
}



if($info_type == "department_info" || $info_type == ""){
$Contents01 .= "
	 <tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>은행 설정</b></div>")."</td>
	  </tr>
	 </table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
		<tr>
			<td class='input_box_title'> <b>카드코드<img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='true' title='카드코드' com_numeric=true>
			</td>
			<td class='input_box_title'> <b>카드사선택  <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
			<select name='age' >
					<option value=''> -- 부서그룹 선택 -- </option>
					<option value='10' ".CompareReturnValue("10",$age,"selected").">10대</option>
					<option value='20' ".CompareReturnValue("20",$age,"selected").">20대</option>
					<option value='30' ".CompareReturnValue("30",$age,"selected").">30대</option>
					<option value='40' ".CompareReturnValue("40",$age,"selected").">40대</option>
					<option value='50' ".CompareReturnValue("50",$age,"selected").">50대</option>
					<option value='60' ".CompareReturnValue("60",$age,"selected").">60대</option>
				</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>카드구분 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type='radio'  name='card_type' id='card_type_1' value='".$db->dt[com_fax]."'> <label for='card_type_1'>신용카드</label>
				<input type='radio'  name='card_type' id='card_type_2' value='".$db->dt[com_fax]."'> <label for='card_type_2'>직불카드</label>
			</td>
			<td class='input_box_title'> <b>카드명 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='true' title='통장명' com_numeric=true>
			</td>
		</tr>
		<tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>카드번호 <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='dp_level' value='".$db->dt[dp_level]."' style='width:200px;' validation='true' title='카드번호'>
		<td class='input_box_title'> <b>카드만료일  <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'><input type=text class='textbox' name='dp_level' value='".$db->dt[dp_level]."' style='width:200px;' validation='true' title='카드만료일'>
	    </td>
	  </tr>
	   <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>기초잔액  <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item'  colspan='3'>
			<select name='age' >
					<option value=''> -- 은행 -- </option>
					<option value='10' ".CompareReturnValue("10",$age,"selected").">10대</option>
					<option value='20' ".CompareReturnValue("20",$age,"selected").">20대</option>
					<option value='30' ".CompareReturnValue("30",$age,"selected").">30대</option>
					<option value='40' ".CompareReturnValue("40",$age,"selected").">40대</option>
					<option value='50' ".CompareReturnValue("50",$age,"selected").">50대</option>
					<option value='60' ".CompareReturnValue("60",$age,"selected").">60대</option>
				</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<select name='age' >
					<option value=''> -- 계좌 선택 -- </option>
					<option value='10' ".CompareReturnValue("10",$age,"selected").">10대</option>
					<option value='20' ".CompareReturnValue("20",$age,"selected").">20대</option>
					<option value='30' ".CompareReturnValue("30",$age,"selected").">30대</option>
					<option value='40' ".CompareReturnValue("40",$age,"selected").">40대</option>
					<option value='50' ".CompareReturnValue("50",$age,"selected").">50대</option>
					<option value='60' ".CompareReturnValue("60",$age,"selected").">60대</option>
				</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				출금예정일&nbsp;&nbsp;
			<input type=text class='textbox' name='dp_level' value='".$db->dt[dp_level]."' style='width:200px;' validation='true' title='출금예정일'>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>노출순서  <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan='3'><input type=text class='textbox' name='dp_level' value='".$db->dt[dp_level]."' style='width:60px;' validation='true' title='노출순서'>
		&nbsp;&nbsp;노출순서에 의해서 셀렉트 박스의 및 리스트 노출 순서가 정해집니다.
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> <b>사용유무  <img src='".$required3_path."'></b> </td>
	    <td class='input_box_item' colspan='3'>
	    	<input type=radio name='disp' id='disp_1' value='1' checked><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ><label for='disp_0'>미사용</label>
	    </td>
	  </tr>
	  <tr>
		  <td class='input_box_title'> <b>기타사항</b></td>
			<td class='input_box_item' colspan='3'>
			<textarea name='edit_data' value='".$db->dt[com_email]."'  style='width:90%' validation='false' title='기타사항' email=true></textarea>
			</td>
		  </tr>
</table>";
}

if($info_type == "post_info" || $info_type == ""){
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>카드 설정</b></div>")."</td>
	  </tr>
	 </table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
		<tr>
			<td class='input_box_title'> <b>현금코드 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='true' title='현금코드' com_numeric=true>
			</td>
			<td class='input_box_title'> <b>현금잔액  <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_fax' value='".$db->dt[com_fax]."'  style='width:200px'  title='현금잔액'>
			</td>
		</tr>
</table>";
}

if($info_type == "position_info" || $info_type == ""){
$Contents01 .= "
	  <tr>
	    <td align='left' colspan=3 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle> <b>직책 설정</b></div>")."</td>
	  </tr>
	 </table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
		<tr>
			<td class='input_box_title'> <b>어음번호 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='true' title='어음번호' com_numeric=true>
			</td>
			<td class='input_box_title'> <b>어음구분  <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
			<select name='age' >
					<option value=''> -- 어음 구분 -- </option>
					<option value='10' ".CompareReturnValue("10",$age,"selected").">10대</option>
					<option value='20' ".CompareReturnValue("20",$age,"selected").">20대</option>
					<option value='30' ".CompareReturnValue("30",$age,"selected").">30대</option>
					<option value='40' ".CompareReturnValue("40",$age,"selected").">40대</option>
					<option value='50' ".CompareReturnValue("50",$age,"selected").">50대</option>
					<option value='60' ".CompareReturnValue("60",$age,"selected").">60대</option>
				</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>수취/발행일 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='true' title='수취/발행일' com_numeric=true>
			</td>
			<td class='input_box_title'> <b>지급일/만기일  <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='true' title='수취/발행일' com_numeric=true>
			</td>
		</tr>
		<tr>
			<td class='input_box_title'> <b>수취/지급 거래처 <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='true' title='수취/지급 거래처' com_numeric=true>
			</td>
			<td class='input_box_title'> <b>액면가  <img src='".$required3_path."'></b></td>
			<td class='input_box_item'>
				<input type=text class='textbox' name='com_email' value='".$db->dt[com_email]."'  style='width:200px' validation='true' title='액면가' com_numeric=true>
			</td>
		</tr>

</table>";
}

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
    $ButtonString = "
    <table width='100%' cellpadding=0 cellspacing=0 border='0'>
        <tr bgcolor=#ffffff ><td align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
    </table>
";
}

$Contents01 .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr>
		<td height='30'></td></tr>
	</table>
";
if($info_type == "basic" || $info_type == ""){
//$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
$ContentsDesc01 .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='3%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='3%' align='center' class='m_td'><font color='#000000'><b>등록일</b></font></td>
    <td width='6%' align='center' class='m_td'><font color='#000000'><b>은행코드</b></font></td>
    <td width='6%' align='center' class='m_td'><font color='#000000'><b>은행명</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>통장명</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>예금주</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>계좌번호</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>기초잔액</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>노출순위</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>사용유무</b></font></td>
    <td width='20%' align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
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

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid_ = '".$db->dt[code]."' and state in ('1','2','5','6','7')");
		}else{
			$sql = "SELECT IFNULL(sum(reserve),'-') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in ('1','2','5','6','7')";
			//echo $sql;
			$mdb->query($sql);
		}
		$mdb->fetch(0);
		$reserve_sum = number_format($mdb->dt[reserve_sum]);

        $ContentsDesc01 = $ContentsDesc01."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
            <td class='list_box_td' >".$no."</td>
            <td class='list_box_td' style='padding:0px 5px;'><span title='".$db->dt[organization_name]."'>".$gp_name."</span></td>
            <td class='list_box_td' ><a href='javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')' style='cursor:pointer' >".Black_list_check($db->dt[code],$db->dt[name])."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[mail]."</td>
            <td class='list_box_td ctr point' >".$db->dt[mail]."</a></td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";

			if($update_auth){
				$ContentsDesc01 .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\" /> ";
			}else{
				$ContentsDesc01 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
			}

			//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
			if($delete_auth){
				$ContentsDesc01 .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" /> ";
			}else{
				//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
			}
            if($create_auth){
			     $ContentsDesc01 .= "
        	     <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\">
        	     <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\">
                 ";
            }else{
                $ContentsDesc01 .= "
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle></a>
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle></a>
                 ";
            }
            $ContentsDesc01 .= "
    </td>
  </tr>";

	}

if (!$db->total){

$ContentsDesc01 = $ContentsDesc01."
  <tr height=50>
    <td colspan='11' align='center'>등록된 데이타가 없습니다.</td>
  </tr>
  ";
}
$ContentsDesc01 = $ContentsDesc01."</table>";
}

if($info_type == "department_info" || $info_type == ""){
//$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
$ContentsDesc01 .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='3%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='3%' align='center' class='m_td'><font color='#000000'><b>등록일</b></font></td>
    <td width='6%' align='center' class='m_td'><font color='#000000'><b>카드코드</b></font></td>
    <td width='6%' align='center' class='m_td'><font color='#000000'><b>카드사</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>카드구분</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>카드번호</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>카드만료일</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>노출순위</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>사용유무</b></font></td>
    <td width='20%' align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
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

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid_ = '".$db->dt[code]."' and state in ('1','2','5','6','7')");
		}else{
			$sql = "SELECT IFNULL(sum(reserve),'-') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in ('1','2','5','6','7')";
			//echo $sql;
			$mdb->query($sql);
		}
		$mdb->fetch(0);
		$reserve_sum = number_format($mdb->dt[reserve_sum]);

        $ContentsDesc01 = $ContentsDesc01."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
            <td class='list_box_td' >".$no."</td>
            <td class='list_box_td' style='padding:0px 5px;'><span title='".$db->dt[organization_name]."'>".$gp_name."</span></td>
            <td class='list_box_td' ><a href='javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')' style='cursor:pointer' >".Black_list_check($db->dt[code],$db->dt[name])."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[mail]."</td>
            <td class='list_box_td ctr point' >".$db->dt[mail]."</a></td>
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";

			if($update_auth){
				$ContentsDesc01 .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\" /> ";
			}else{
				$ContentsDesc01 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
			}

			//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
			if($delete_auth){
				$ContentsDesc01 .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" /> ";
			}else{
				//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
			}
            if($create_auth){
			     $ContentsDesc01 .= "
        	     <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\">
        	     <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\">
                 ";
            }else{
                $ContentsDesc01 .= "
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle></a>
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle></a>
                 ";
            }
            $ContentsDesc01 .= "
    </td>
  </tr>";

	}

if (!$db->total){

$ContentsDesc01 = $ContentsDesc01."
  <tr height=50>
    <td colspan='12' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>
  ";
}
$ContentsDesc01 = $ContentsDesc01."</table>";
}

if($info_type == "post_info" || $info_type == ""){
//$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
$ContentsDesc01 .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='3%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='3%' align='center' class='m_td'><font color='#000000'><b>등록일</b></font></td>
    <td width='6%' align='center' class='m_td'><font color='#000000'><b>카드코드</b></font></td>
    <td width='6%' align='center' class='m_td'><font color='#000000'><b>카드사</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>카드구분</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>카드번호</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>카드만료일</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>노출순위</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>사용유무</b></font></td>
    <td width='20%' align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
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

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid_ = '".$db->dt[code]."' and state in ('1','2','5','6','7')");
		}else{
			$sql = "SELECT IFNULL(sum(reserve),'-') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in ('1','2','5','6','7')";
			//echo $sql;
			$mdb->query($sql);
		}
		$mdb->fetch(0);
		$reserve_sum = number_format($mdb->dt[reserve_sum]);

        $ContentsDesc01 = $ContentsDesc01."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
            <td class='list_box_td' >".$no."</td>
            <td class='list_box_td' style='padding:0px 5px;'><span title='".$db->dt[organization_name]."'>".$gp_name."</span></td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[mail]."</td>
            <td class='list_box_td ctr point' >".$db->dt[mail]."</a></td>
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";

			if($update_auth){
				$ContentsDesc01 .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\" /> ";
			}else{
				$ContentsDesc01 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
			}

			//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
			if($delete_auth){
				$ContentsDesc01 .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" /> ";
			}else{
				//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
			}
            if($create_auth){
			     $ContentsDesc01 .= "
        	     <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\">
        	     <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\">
                 ";
            }else{
                $ContentsDesc01 .= "
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle></a>
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle></a>
                 ";
            }
            $ContentsDesc01 .= "
    </td>
  </tr>";

	}

if (!$db->total){

$ContentsDesc01 = $ContentsDesc01."
  <tr height=50>
    <td colspan='12' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>
  ";
}
$ContentsDesc01 = $ContentsDesc01."</table>";
}


if($info_type == "position_info" || $info_type == ""){
//$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
$ContentsDesc01 .= "
<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
  <tr height='28' bgcolor='#ffffff'>
    <td width='3%' align='center' class=s_td><input type=checkbox name='all_fix' id='all_fix' onclick='fixAll(document.list_frm)'></td>
    <td width='3%' align='center' class='m_td'><font color='#000000'><b>등록일</b></font></td>
    <td width='6%' align='center' class='m_td'><font color='#000000'><b>카드코드</b></font></td>
    <td width='6%' align='center' class='m_td'><font color='#000000'><b>카드사</b></font></td>
    <td width='6%' align='center' class=m_td><font color='#000000'><b>카드구분</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>카드번호</b></font></td>
    <td width='10%' align='center' class=m_td><font color='#000000'><b>카드만료일</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>노출순위</b></font></td>
	<td width='10%' align='center' class=m_td><font color='#000000'><b>사용유무</b></font></td>
    <td width='20%' align='center' class=e_td><font color='#000000'><b>관리</b></font></td>
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

		if($db->dbms_type == "oracle"){
			$mdb->query("SELECT sum(reserve) as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid_ = '".$db->dt[code]."' and state in ('1','2','5','6','7')");
		}else{
			$sql = "SELECT IFNULL(sum(reserve),'-') as reserve_sum FROM ".TBL_SHOP_RESERVE_INFO." WHERE uid = '".$db->dt[code]."' and state in ('1','2','5','6','7')";
			//echo $sql;
			$mdb->query($sql);
		}
		$mdb->fetch(0);
		$reserve_sum = number_format($mdb->dt[reserve_sum]);

        $ContentsDesc01 = $ContentsDesc01."
          <tr height='28' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\">
            <td class='list_box_td'><input type=checkbox name=code[] id='code' value='".$db->dt[code]."'></td>
            <td class='list_box_td' >".$no."</td>
            <td class='list_box_td' ><a href='javascript:PopSWindow('member_view.php?code=".$db->dt[code]."',985,600,'member_info')' style='cursor:pointer' >".Black_list_check($db->dt[code],$db->dt[name])."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[pcs]."</td>
			<td class='list_box_td' >".$db->dt[mail]."</td>
            <td class='list_box_td ctr point' >".$db->dt[mail]."</a></td>
            <td class='list_box_td ctr'  style='padding:5px;' nowrap>";
			if($update_auth){
				$ContentsDesc01 .= "<img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle onClick=\"PopSWindow('member_info.php?mmode=pop&code=".$db->dt[code]."&mmode=pop',900,710,'member_info')\" /> ";
			}else{
				$ContentsDesc01 .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 align=absmiddle ></a> ";
			}

			//$mString .= "<img  src='../image/btn_view_source.gif'  border=0 align=absmiddle>";
			if($delete_auth){
				$ContentsDesc01 .= "<img src='../images/".$admininfo["language"]."/btn_del.gif' border=0 align=absmiddle onClick=\"deleteMemberInfo('delete','".$db->dt[code]."')\" /> ";
			}else{
				//$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/btc_del.gif' border=0 align=absmiddle ></a> ";
			}
            if($create_auth){
			     $ContentsDesc01 .= "
        	     <img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle onclick=\"PoPWindow('../sms.pop.php?code=".$db->dt[code]."',500,380,'sendsms')\">
        	     <img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle onclick=\"PoPWindow('../mail.pop.php?code=".$db->dt[code]."',550,535,'sendmail')\">
                 ";
            }else{
                $ContentsDesc01 .= "
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_sms.gif' align=absmiddle></a>
        	     <a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/btn_email.gif' align=absmiddle></a>
                 ";
            }
            $ContentsDesc01 .= "
    </td>
  </tr>";

	}

if (!$db->total){

$ContentsDesc01 = $ContentsDesc01."
  <tr height=50>
    <td colspan='12' align='center'>등록된 회원 데이타가 없습니다.</td>
  </tr>
  ";
}
$ContentsDesc01 = $ContentsDesc01."</table>";
}




$Contents = "<table width='100%' border=0>";
$Contents = $Contents."<form name='department_frm' action='department.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data'>
<input name='act' type='hidden' value='insert'>
<input name='dp_ix' type='hidden' value=''>
<input name='basic' type='hidden' value=''>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$Contents01."<br>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr height=50><td></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$Contents02."<br></td></tr>";
$Contents = $Contents."<tr height=30><td></td></tr>";

$Contents = $Contents."</table >";
/*
$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >회원 부서정보는 위와 같이 9단계의 등급으로 이루어져 있으며 '부서등급' 은 수정이 불가능하며 각 등급에 맞는 명칭을 변경해서 사용하셔야 합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용하지 않으실 부서정보는 수정버튼을 클릭하신후 사용하지 않음으로 선택하신후 저장 버튼을 누르시면 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >사용유무가 사용으로 되어 있는 부서만 사용하실수 있게 됩니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .= HelpBox("부서관리", $help_text, 60);

 $Script = "
 <script language='javascript'>
 function updateDepartmentInfo(dp_ix,sale_rate,dp_name,dp_name, dp_id,dp_img, dp_level,sale_rate,memberreg_baymoney,use_mall_yn, disp, basic){
 	var frm = document.department_frm;

 	frm.act.value = 'update';
 	frm.dp_ix.value = dp_ix;
 	//frm.sale_rate.value = sale_rate;
 	frm.dp_name.value = dp_name;
 	frm.basic.value = basic;
 	frm.dp_level.value = dp_level;
 	if(dp_img != ''){
 		document.getElementById('dp_img_area').innerHTML =\"<img src='".$admin_config[mall_data_root]."/images/department/\"+dp_img+\"' width=109>\";
 	}else{
 		document.getElementById('dp_img_area').innerHTML =\"\";
 	}
//	alert(document.getElementById('dp_img_area').innerHTML);
/*

 	for(i=0;i < frm.dp_level.length;i++){
 		if(frm.dp_level[i].value == dp_level){
 			frm.dp_level[i].selected = true;
 		}
 	}
*/
 	if(disp == '1'){
 		frm.disp[0].checked = true;
 	}else{
 		frm.disp[1].checked = true;
 	}

}

function deleteDepartmentInfo(act, dp_ix){
 	if(confirm('해당부서 정보를 정말로 삭제하시겠습니까?')){
 		var frm = document.department_frm;
 		frm.act.value = act;
 		frm.dp_ix.value = dp_ix;
 		frm.submit();
 	}
}
 </script>
 ";



$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = basic_menu();
$P->Navigation = "기초정보관리 > 기초관리 > 기초은행/카드/현금관리";
$P->title = "기초은행/카드/현금관리";
$P->strContents = $Contents;
echo $P->PrintLayOut();



/*

create table shop_company_department (
dp_ix int(4) unsigned not null auto_increment  ,
dp_name varchar(20) null default null,
dp_level int(2)  default '9' ,
sale_rate varchar(20) null default null,

disp char(1) default '1' ,
regdate datetime not null,
primary key(dp_ix));
*/
?>