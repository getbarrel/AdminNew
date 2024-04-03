<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;
	
	$idx			= $_GET[idx];
	$s_type			= $_GET[s_type];
	$company_number = $_GET[company_number];
	$form			= $_GET[from];

	if($idx != "")
	{
		$SQL = "SELECT * FROM tax_company_info WHERE idx = '$idx'";
		$db->query($SQL);
		$db->fetch();

		$company_number = $db->dt[company_number];
		$email = explode("@",$db->dt[email]);
		$tel = explode("-",$db->dt[tel]);
		$mobile = explode("-",$db->dt[mobile]);
		$fax = explode("-",$db->dt[fax]);
	}

	if($s_type == "1")	$pop_h = "650";
	else				$pop_h = "550";
?>
<html>
<title>신규거래처<?=$title?></title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='./webedit/webedit.js'></script>	
<script language='JavaScript' src='./js/admin.js'></Script>
<script language='JavaScript' src='/admin/js/jquery-1.4.js'></Script>
<script language='JavaScript' src='/admin/member/member.js'></Script>
<script language='JavaScript' >
$(document).ready(function(){
	$("#frm").submit(function(){
		if($("#c_name").val() == "")
		{
			alert ("회사명을 입력해주세요.");
			$("#c_name").focus();
			return false;
		}
		if($("#c_ceo").val() == "")
		{
			alert ("대표자를 입력해주세요.");
			$("#c_ceo").focus();
			return false;
		}
		if($("#c_personin").val() == "")
		{
			alert ("담당자를 입력해주세요.");
			$("#c_personin").focus();
			return false;
		}
	});

	$("#frm2").submit(function(){
		if($("#c_name").val() == "")
		{
			alert ("성명을 입력해주세요.");
			$("#c_name").focus();
			return false;
		}
	});

	$('#email_select').change(function(){
		$('#email_com').val($('#email_select').val());
	});

});

function zipcode_tax(type) {
	var zip = window.open('/admin/member/zipcode.php?type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}

function num_chk(num)
{
	if(isNaN(num.value))
	{
		alert('숫자만 입력하세요');
		num.value = "";
		num.focus();
		return;
	}
} 
window.resizeTo("550","<?=$pop_h?>");
</Script>

<body style="margin:0px 0px 0px 0px">
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	<TR>		
		<td align=center colspan=2>
		<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
			<tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'> 
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr> 
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap><!--border-bottom:2px solid #ff9b00;-->
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 신규 거래처 <?=$title?>
							</td>
							<td width='90%' align='right' valign='top' ><!--style='border-bottom:2px solid #efefef;'-->
								&nbsp;
							</td>
						</tr>
						<!--tr height=10><td colspan=2></td></tr-->
					</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
			
			<?if($s_type == "1"){?>
			<form name='frm' id='frm' action='./company_write_step2_act.php' method='post' target="PROC">
			<input type="hidden" name="idx" id="idx" value="<?=$idx?>">
			<input type="hidden" name="s_type" id="s_type" value="<?=$s_type?>">
			<input type="hidden" name="company_number" id="company_number" value="<?=$company_number?>">
			<input type="hidden" name="from" id="from" value="<?=$from?>">
			<table width="100%">
				<tr>
					<td height="30">* 표시는 필수 입력 항목입니다.</td>
				</tr>
				<tr>
					<td>
						<table width='100%' cellSpacing=1 cellPadding=0 border='0' bgcolor='#CCCCCC'>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>사업자번호</td>
								<td><?=$company_number?></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>* 회사명</td>
								<td><input type="text" name="c_name" id="c_name" size="30" value="<?=$db->dt[company_name]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>* 대표자</td>
								<td><input type="text" name="c_ceo" id="c_ceo" size="30" value="<?=$db->dt[ceo]?>"></td>
							</tr>
							<tr height='50' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>주소</td>
								<td>
									<input type="text" name="zip1" id="zip1" size="5" readonly value="<?=$db->dt[zip1]?>" onclick="zipcode_tax('1');">-<input type="text" name="zip2" id="zip2" size="5" readonly  value="<?=$db->dt[zip2]?>" onclick="zipcode_tax('1');"> <img src="../image/member_join_adress.gif" onclick="zipcode_tax('1');" style="cursor:pointer;" align="absbottom"><br>
									<input type="text" name="addr1" id="addr1" size="30" readonly value="<?=$db->dt[addr1]?>"> <input type="text" name="addr2" id="addr2" size="30" value="<?=$db->dt[addr2]?>">
								</td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>업태</td>
								<td><input type="text" name="c_status" id="c_status" size="30" value="<?=$db->dt[company_status]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>종목</td>
								<td><input type="text" name="c_items" id="c_items" size="30" value="<?=$db->dt[company_items]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>* 담당자</td>
								<td><input type="text" name="c_personin" id="c_personin" size="30" value="<?=$db->dt[personin]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>Email</td>
								<td><input type="text" name="email_id" id="email_id" size="15" value="<?=$email[0]?>">@<input type="text" name="email_com" id="email_com" size="15" value="<?=$email[1]?>">
									<select name='email_select' id='email_select' class='sb'>
										<option value='&nbsp;'>직접입력</option>
										<option value='chol.com'>chol.com</option>
										<option value='dreamwiz.com'>dreamwiz.com</option>
										<option value='empal.com'>empal.com</option>
										<option value='freechal.com'>freechal.com</option>
										<option value='gmail.com'>gmail.com</option>
										<option value='hanafos.com'>hanafos.com</option>
										<option value='hanmail.net'>hanmail.net</option>
										<option value='hanmir.com'>hanmir.com</option>
										<option value='hitel.net'>hitel.net</option>
										<option value='hotmail.com'>hotmail.com</option>
										<option value='korea.com'>korea.com</option>
										<option value='kornet.net'>kornet.net</option>
										<option value='lycos.co.kr'>lycos.co.kr</option>
										<option value='nate.com'>nate.com</option>
										<option value='naver.com'>naver.com</option>
										<option value='netian.com'>netian.com</option>
										<option value='nownuri.net'>nownuri.net</option>
										<option value='paran.com'>paran.com</option>
										<option value='unitel.co.kr'>unitel.co.kr</option>
										<option value='yahoo.com'>yahoo.com</option>
										<option value='yahoo.co.kr'>yahoo.co.kr</option>
									</select>
								</td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>연락처</td>
								<td><input type="text" name="c_tel1" id="c_tel1" size="10" onkeyup="num_chk(this)" value="<?=$tel[0]?>">-<input type="text" name="c_tel2" id="c_tel2" size="10" onkeyup="num_chk(this)" value="<?=$tel[1]?>">-<input type="text" name="c_tel3" id="c_tel3" size="10" onkeyup="num_chk(this)" value="<?=$tel[2]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>핸드폰</td>
								<td><input type="text" name="c_mobile1" id="c_mobile1" size="10" onkeyup="num_chk(this)" value="<?=$mobile[0]?>">-<input type="text" name="c_mobile2" id="c_mobile2" size="10" onkeyup="num_chk(this)" value="<?=$mobile[1]?>">-<input type="text" name="c_mobile3" id="c_mobile3" size="10" onkeyup="num_chk(this)" value="<?=$mobile[2]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>팩스</td>
								<td><input type="text" name="c_fax1" id="c_fax1" size="10" onkeyup="num_chk(this)" value="<?=$fax[0]?>">-<input type="text" name="c_fax2" id="c_fax2" size="10" onkeyup="num_chk(this)" value="<?=$fax[1]?>">-<input type="text" name="c_fax3" id="c_fax3" size="10" onkeyup="num_chk(this)" value="<?=$fax[2]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>직책</td>
								<td><input type="text" name="c_position" id="c_position" size="30"  value="<?=$db->dt[c_position]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>메모</td>
								<td>
									<textarea name="c_memo" id="c_memo" style="width:420px;height:60px"><?=$db->dt[memo]?></textarea> <br><br>
									* 메모는 한글 100자 까지 작성하실수 있습니다.<br><br>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="50" align="center"><input type="image" src='/admin/image/b_save_.gif'> <img src='/admin/image/b_cancel.gif' value="닫기" onclick='window.close();'></td>
				</tr>
			</table>
			</form>
			<?}?>

			<?if($s_type == "2"){?>
			<form name='frm2' id='frm2' action='./company_write_step2_act.php' method='post' target="PROC">
			<input type="hidden" name="idx" id="idx" value="<?=$idx?>">
			<input type="hidden" name="s_type" id="s_type" value="<?=$s_type?>">
			<input type="hidden" name="company_number" id="company_number" value="<?=$company_number?>">
			<table width="100%">
				<tr>
					<td height="30">* 표시는 필수 입력 항목입니다.</td>
				</tr>
				<tr>
					<td>
						<table width='100%' cellSpacing=1 cellPadding=0 border='0' bgcolor='#CCCCCC'>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>주민등록번호</td>
								<td><?=$company_number?></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>* 성명</td>
								<td><input type="text" name="c_name" id="c_name" size="30" value="<?=$db->dt[company_name]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>Email</td>
								<td><input type="text" name="email_id" id="email_id" size="15" value="<?=$email[0]?>">@<input type="text" name="email_com" id="email_com" size="15" value="<?=$email[1]?>">
									<select name='email_select' id='email_select' class='sb'>
										<option value='&nbsp;'>직접입력</option>
										<option value='chol.com'>chol.com</option>
										<option value='dreamwiz.com'>dreamwiz.com</option>
										<option value='empal.com'>empal.com</option>
										<option value='freechal.com'>freechal.com</option>
										<option value='gmail.com'>gmail.com</option>
										<option value='hanafos.com'>hanafos.com</option>
										<option value='hanmail.net'>hanmail.net</option>
										<option value='hanmir.com'>hanmir.com</option>
										<option value='hitel.net'>hitel.net</option>
										<option value='hotmail.com'>hotmail.com</option>
										<option value='korea.com'>korea.com</option>
										<option value='kornet.net'>kornet.net</option>
										<option value='lycos.co.kr'>lycos.co.kr</option>
										<option value='nate.com'>nate.com</option>
										<option value='naver.com'>naver.com</option>
										<option value='netian.com'>netian.com</option>
										<option value='nownuri.net'>nownuri.net</option>
										<option value='paran.com'>paran.com</option>
										<option value='unitel.co.kr'>unitel.co.kr</option>
										<option value='yahoo.com'>yahoo.com</option>
										<option value='yahoo.co.kr'>yahoo.co.kr</option>
									</select>
								</td>
							</tr>
							<tr height='50' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>주소</td>
								<td>
									<input type="text" name="zip1" id="zip1" size="5" readonly value="<?=$db->dt[zip1]?>" onclick="zipcode_tax('1');">-<input type="text" name="zip2" id="zip2" size="5" readonly  value="<?=$db->dt[zip2]?>" onclick="zipcode_tax('1');"> <img src="../image/member_join_adress.gif" onclick="zipcode_tax('1');" style="cursor:pointer;" align="absbottom"><br>
									<input type="text" name="addr1" id="addr1" size="30" readonly value="<?=$db->dt[addr1]?>"> <input type="text" name="addr2" id="addr2" size="30" value="<?=$db->dt[addr2]?>">
								</td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>연락처</td>
								<td><input type="text" name="c_tel1" id="c_tel1" size="10" onkeyup="num_chk(this)" value="<?=$tel[0]?>">-<input type="text" name="c_tel2" id="c_tel2" size="10" onkeyup="num_chk(this)" value="<?=$tel[1]?>">-<input type="text" name="c_tel3" id="c_tel3" size="10" onkeyup="num_chk(this)" value="<?=$tel[2]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>핸드폰</td>
								<td><input type="text" name="c_mobile1" id="c_mobile1" size="10" onkeyup="num_chk(this)" value="<?=$mobile[0]?>">-<input type="text" name="c_mobile2" id="c_mobile2" size="10" onkeyup="num_chk(this)" value="<?=$mobile[1]?>">-<input type="text" name="c_mobile3" id="c_mobile3" size="10" onkeyup="num_chk(this)" value="<?=$mobile[2]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>팩스</td>
								<td><input type="text" name="c_fax1" id="c_fax1" size="10" onkeyup="num_chk(this)" value="<?=$fax[0]?>">-<input type="text" name="c_fax2" id="c_fax2" size="10" onkeyup="num_chk(this)" value="<?=$fax[1]?>">-<input type="text" name="c_fax3" id="c_fax3" size="10" onkeyup="num_chk(this)" value="<?=$fax[2]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>직책</td>
								<td><input type="text" name="c_position" id="c_position" size="30"  value="<?=$db->dt[c_position]?>"></td>
							</tr>
							<tr height='25' bgcolor='#FFFFFF'>
								<td bgcolor='#F2F2F2' align='center' width='100'>메모</td>
								<td>
									<textarea name="c_memo" id="c_memo" style="width:420px;height:60px"><?=$db->dt[memo]?></textarea> <br><br>
									* 메모는 한글 100자 까지 작성하실수 있습니다.<br><br>
								</td>
							</tr>

						</table>
					</td>
				</tr>
				<tr>
					<td height="50" align="center">
						<input type="image" src='/admin/image/b_save_.gif'> 
						<img src='/admin/image/b_cancel.gif' value="닫기" onclick='window.close();'>
					</td>
				</tr>
			</table>
			</form>
			<?}?>


		</td>
	</tR>
</table>

<iframe name="PROC" width="0"></iframe>
</body>