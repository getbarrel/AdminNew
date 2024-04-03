<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;
?>
<STYLE>
#men {
	BORDER-RIGHT: 2px outset; BORDER-TOP: 2px outset; Z-INDEX: 1; LEFT: 0px; VISIBILITY: hidden; BORDER-LEFT: 2px outset; BORDER-BOTTOM: 2px outset; POSITION: absolute; TOP: 0px
}
#men A {
	PADDING-RIGHT: 1px; PADDING-LEFT: 1px; PADDING-BOTTOM: 4px; MARGIN: 1px 1px 1px 16px; FONT: 11px sans-serif; WIDTH: 100%; PADDING-TOP: 3px; HEIGHT: 100%; TEXT-DECORATION: none
}
.ico {
	BORDER-RIGHT: medium none; BORDER-TOP: medium none; FLOAT: left; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none
}

#ibox_w {
	PADDING-RIGHT: 0px; PADDING-LEFT: 0px; Z-INDEX: 100; FILTER: alpha(opacity=0); LEFT: 0px; PADDING-BOTTOM: 0px; MARGIN: 0px; WIDTH: 100%; PADDING-TOP: 0px; POSITION: absolute; TOP: 0px; HEIGHT: 100%; BACKGROUND-COLOR: #000; -moz-opacity: 0.0; opacity: 0.0
}

#ibox_progress {
	PADDING-RIGHT: 0px; PADDING-LEFT: 0px; Z-INDEX: 105; PADDING-BOTTOM: 0px; MARGIN: 0px; PADDING-TOP: 0px; POSITION: absolute
}
#ibox_wrapper {
	Z-INDEX: 100; LEFT: 0px; WIDTH: 100%; LINE-HEIGHT: 0; POSITION: absolute; TEXT-ALIGN: center;
}
#ibox_content {
	MARGIN: 0px auto;  POSITION: relative; WIDTH: 250px;HEIGHT: 250px; BACKGROUND-COLOR: #fff
}

body{margin:0px;}
</STYLE>
<?

	if($publish_type == "") $publish_type = 2;	// 1.매출 2.매입 3.위수탁
	if($tax_type == "")		$tax_type = 1;		// 1.세금계산서 2.계산서

	$tab01 = $tab02 = "";
	if($tax_type == "1")
	{
		$title = "전자세금계산서";
		$tab01 = "class='on'";
	}
	elseif($tax_type == "2")
	{
		$title = "전자계산서";
		$tab02 = "class='on'";
	}

	if($publish_type == "1")		$line_color = "#e66464";
	elseif($publish_type == "2")	$line_color = "#0033ff";
	elseif($publish_type == "3")	$line_color = "#e66464";

	$line_color = "#0033ff";

	
	if($idx == "")
	{
		$db->query("SELECT * FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE company_id = '".$admininfo[company_id]."'");
			$db->fetch();

		$r_company_name		= $db->dt[com_name];					// 회사명
		$r_company_number	= $db->dt[com_number];					// 사업자번호
		$r_state			= $db->dt[com_business_status];					// 업태
		$r_name				= $db->dt[com_ceo];								// 성명
		$r_items			= $db->dt[com_business_category];					// 업종
		$r_address			= $db->dt[com_addr1]." ".$db->dt[com_addr2] ;					// 주소
		$r_personin			= $db->dt[com_ceo];							// 담당자
		$r_email			= explode("@",$db->dt[com_email]);		// 담당자 이메일
		$r_tel				= $db->dt[com_phone];							// 연락처

		if($send_type == "") $checked1 = "checked";
	}
	else
	{
		$db->query("SELECT * FROM tax_sales WHERE idx = '$idx'");
		$db->fetch();

		$numbering_k		= $db->dt[numbering_k];						// 권
		$numbering_h		= $db->dt[numbering_h];						// 호
		$numbering			= $db->dt[numbering];						// 일련번호
		
		$s_company_number	= $db->dt[s_company_number];				// 등록번호
		$s_company_j		= $db->dt[s_company_j];						// 종사업장
		$s_company_name		= $db->dt[s_company_name];					// 상호(업체명)
		$s_name				= $db->dt[s_name];							// 성명
		$s_address			= $db->dt[s_address];						// 사업장주소
		$s_state			= $db->dt[s_state];							// 업태
		$s_items			= $db->dt[s_items];							// 종목
		$s_personin			= $db->dt[s_personin];						// 담당자
		$s_tel				= $db->dt[s_tel];							// 연락처
		$s_email			= explode("@",$db->dt[s_email]);							// 이메일

		$r_company_number	= $db->dt[r_company_number];				// 등록번호
		$r_company_j		= $db->dt[r_company_j];						// 종사업장
		$r_company_name		= $db->dt[r_company_name];					// 상호(업체명)
		$r_name				= $db->dt[r_name];							// 성명
		$r_address			= $db->dt[r_address];						// 사업장주소
		$r_state			= $db->dt[r_state];							// 업태
		$r_items			= $db->dt[r_items];							// 종목
		$r_personin			= $db->dt[r_personin];						// 담당자
		$r_tel				= $db->dt[r_tel];							// 연락처
		$r_email			= explode("@",$db->dt[r_email]);							// 이메일

		$tax_per			= $db->dt[tax_per];							// 과세형태
		$marking			= $db->dt[marking];							// 비고
		$supply_price		= $db->dt[supply_price];					// 공급가액
		$tax_price			= $db->dt[tax_price];						// 세액
		$total_price		= $db->dt[total_price];						// 합계금액
		$cash				= $db->dt[cash];							// 현금
		$cheque				= $db->dt[cheque];							// 수표
		$pro_note			= $db->dt[pro_note];						// 어음
		$outstanding		= $db->dt[outstanding];						// 외상미수금
		$claim_kind			= $db->dt[claim_kind];						// 영수/청구 구분
		$signdate			= $db->dt[signdate];						// 등록일
		$re_signdate		= $db->dt[re_signdate];						// 수정일
		
		$send_type			= $db->dt[send_type];
		$sms_chk			= $db->dt[sms_chk];
		$sms_number			= explode("-",$db->dt[sms_number]);
		$fax_chk			= $db->dt[fax_chk];
		$fax_number			= explode("-",$db->dt[fax_number]);
		
		$file1				= $db->dt[file1];
		$file1_rename		= $db->dt[file1_rename];
		$file2				= $db->dt[file2];
		$file2_rename		= $db->dt[file2_rename];
		$file3				= $db->dt[file3];
		$file3_rename		= $db->dt[file4_rename];

		$memo				= nl2br($db->dt[memo]);
		
		if($send_type == 1) $checked1 = "checked";
		else				$checked1 = "";
		if($send_type == 2) $checked2 = "checked";
		else				$checked2 = "";
		if($send_type == 3) $checked3 = "checked";
		else				$checked3 = "";
		if($send_type == "") $checked1 = "checked";

		if($sms_chk == "y") $sms_checked = "checked";
		if($fax_chk == "y") $fax_checked = "checked";

	}


	/* script */
	$Contents .= "
	<script src='tax.js'></script>
	<script>
	$('#tab1_view').slideDown();
	</script>
	";

	$Contents .= "
	<LINK REL='stylesheet' HREF='./css/btn.css' TYPE='text/css'>
	<style>
	.BLine_T {
	BORDER-BOTTOM: $line_color 1px solid; COLOR: $line_color
	}
	.RBLine_T {
		BORDER-BOTTOM: $line_color 1px solid; COLOR: $line_color; BORDER-RIGHT: $line_color 1px solid
	}
	.NoLine_T {
		COLOR: $line_color
	}
	.RLine {
		PADDING-BOTTOM: 0px; PADDING-LEFT: 2px; PADDING-RIGHT: 6px; COLOR: #444444; BORDER-RIGHT: $line_color 1px solid; PADDING-TOP: 0px
	}
	.BLine {
		BORDER-BOTTOM: $line_color 1px solid; PADDING-BOTTOM: 0px; PADDING-LEFT: 2px; PADDING-RIGHT: 6px; COLOR: #444444; PADDING-TOP: 0px
	}
	.RBLine {
		BORDER-BOTTOM: $line_color 1px solid; PADDING-BOTTOM: 0px; PADDING-LEFT: 2px; PADDING-RIGHT: 6px; COLOR: #444444; BORDER-RIGHT: $line_color 1px solid; PADDING-TOP: 0px
	}
	.NoLine {
		PADDING-BOTTOM: 0px; PADDING-LEFT: 3px; PADDING-RIGHT: 5px; COLOR: #444444; PADDING-TOP: 0px
	}
	.hideline {
		BORDER-BOTTOM: #ffffff 1px solid; BORDER-LEFT: #ffffff 1px solid; BORDER-TOP: #ffffff 1px solid; BORDER-RIGHT: #ffffff 1px solid
	}
	.NoBorder {
		BORDER-BOTTOM: 0px; BORDER-LEFT: 0px; BORDER-TOP: 0px; BORDER-RIGHT: 0px
	}
	#INVOICEWRITE A {
		COLOR: #444444
	}
	#INVOICEWRITE A:hover {
		COLOR: #444444
	}
	.tb {
	BORDER-BOTTOM: #c3c3c3 1px solid; BORDER-LEFT: #c3c3c3 1px solid; BACKGROUND-COLOR: #ffffff; MARGIN: 0px; PADDING-RIGHT: 2px; FONT-FAMILY: 돋움; HEIGHT: 15px; COLOR: #333333; FONT-SIZE: 9pt; BORDER-TOP: #c3c3c3 1px solid; BORDER-RIGHT: #c3c3c3 1px solid; PADDING-TOP: 2px
	}
	.tb_readonly {
		BORDER-BOTTOM: #b8b8b8 1px solid; BORDER-LEFT: #b8b8b8 1px solid; BACKGROUND-COLOR: #f0f0f0; MARGIN: 0px; PADDING-RIGHT: 2px; FONT-FAMILY: 돋움; HEIGHT: 15px; COLOR: #444444; FONT-SIZE: 9pt; BORDER-TOP: #b8b8b8 1px solid; BORDER-RIGHT: #b8b8b8 1px solid; PADDING-TOP: 2px
	}
	</style>

	<table cellpadding=0 cellspacing=0 border=0 width='100%'>
		<tr >
			<td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("매입작성(역발행)", "세금계산서발행 > 매입작성(역발행) ")."</td>
		</tr>
	</table>
	
	<div class='tab' style='margin:0 0 5px 0'>
		<table class='s_org_tab'>
			<tr>
				<td width='730'></td>
				<td class='tab' align='right'>
					<table id='tab_01' $tab01>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='$PHP_SELF?idx=$idx&tax_type=1'\">세금계산서</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_02' $tab02>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='$PHP_SELF?idx=$idx&tax_type=2'\" >계산서</td>
							<th class='box_03'></th>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	
	<form name='frm' id='frm' action='./sales_write_act.php' method='POST' target='PROC' enctype='multipart/form-data'>
	
	<input type='hidden' name='publish_type' value='2'><!-- 1.매출 2.매입 3.위수탁 -->
	<input type='hidden' name='tax_type' value='$tax_type'><!-- 1.세금계산서 2.계산서 -->
	<input type='hidden' name='idx' value='$idx'><!-- idx -->
	<input type='hidden' name='status' id='status' value='1'><!-- 1.발행 2.임시저장 3.국세청전송-->

	<table border='0' cellpadding='0' cellspacing='0' width='100%' style='table-layout:fixed;border:3px double $line_color;'>
	<tr>
		<td width='21' height='1'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='21'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='22'></td>
		<td width='20'></td>
	</tr>
	<tr height='25' bgcolor='white'>
		<td colspan='16' rowspan='2' class='BLine_T' align='center' style='font-size:24px;'><b><span id='ModifyTitle'></span>$title</b></td>
		<td colspan='4' rowspan='2' class='BLine_T' align='center'style='line-height:20px;'>공 급 자<br>(보 관 용)</td>
		<td colspan='2' rowspan='2' class='BLine'>&nbsp;</td>
		<td colspan='4' align='right' class='NoLine_T'>책번호 :</td>
		<td colspan='3' align='right' class='NoLine'><input type='text' class='tb' name='numbering_k' style='width:56px;IME-MODE:disabled;text-align:right;' valtype='NUM' maxlength='4' value='$numbering_k'></td>
		<td colspan='1' align='center' class='NoLine'>권</td>
		<td colspan='3' align='right' class='NoLine'><input type='text' class='tb' name='numbering_h' style='width:56px;IME-MODE:disabled;text-align:right;' valtype='NUM' maxlength='4' value='$numbering_h'></td>
		<td colspan='1' align='center' class='NoLine'>호</td>
	</tr>
	<tr height='25'>
		<td colspan='4' align='right' class='BLine_T'>일련번호 :</td>
		<td colspan='8' class='BLine' align='right' style='padding:0px 8px 0px 3px;'><input type='text' class='tb' name='SerialNum' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' maxlength='27' value=''></td>
	</tr>
	<tr height='25'>
		<td colspan='1' rowspan='6' align='center' class='RBLine_T' style='line-height:22px;'><b>공<br>급<br>자</b></td>
		<td colspan='3' align='center' class='RBLine_T'>등록번호</td>
		<td colspan='8' align='center' class='RBLine'>
			<input type='text' class='tb NoBorder' name='s_company_number' id='s_company_number' value='$s_company_number' maxlength='14' style='width:55%;text-align:center;' Essential='1'>
			<img src='./img/btn_searchagency.gif' align='absmiddle' onclick='search_company(2)'>
		</td>
		<td colspan='3' align='center' class='RBLine_T'>종사업장</td>
		<td colspan='2' align='center' class='RBLine'>
			<input type='text' class='tb' name='s_company_j' id='s_company_j' valtype='NUM' value='' maxlength='4' style='width:95%;IME-MODE:disabled;'>
		</td>
		
		<td colspan='1' rowspan='6' align='center' class='RBLine_T' style='line-height:22px;'><b>공<br>급<br>받<br>는<br>자</b></td>
		
		<td colspan='3' align='center' class='RBLine_T'>등록번호</td>
		
		<td colspan='8' align='center' class='RBLine'>
			<input type='text' class='tb'  name='r_company_number' id='r_company_number' value='$r_company_number' maxlength='14' style='width:95%;text-align:center;' Essential='1' BlurEvent='1'>
		</td>
		<td colspan='3' align='center' class='RBLine_T'>종사업장</td>
		<td colspan='2' align='center' class='BLine'>
			<input type='text' class='tb' name='r_company_j' id='r_company_j' valtype='NUM' value='' maxlength='4' style='width:95%;IME-MODE:disabled;'>
		</td>
		
	</tr>
	<tr height='35'>
		<td colspan='3' align='center' class='RBLine_T' style='line-height:16px;'>상호<br>(업체명)</td>
		<td colspan='8' align='left' class='RBLine'>
			<textarea class='readonly' name='s_company_name' id='s_company_name' style='width:90%;height:26px;overflow:hidden;' Essential='1' maxlength='70'>$s_company_name</textarea>
		</td>
		<td colspan='1' align='center' class='RBLine_T' style='line-height:16px;'>성<br>명</td>
		<td colspan='4' align='left' class='RBLine'>
			<textarea class='tb' name='s_name' id='s_name' style='width:90%;height:26px;overflow:hidden;' Essential='1' maxlength='30'>$s_name</textarea>
		</td>
		<td colspan='3' align='center' class='RBLine_T' style='line-height:16px;'>상호<br>(업체명)</td>
		<td colspan='8' align='left' class='RBLine'>
			<textarea class='tb_readonly' name='r_company_name' id='r_company_name' style='width:95%;height:26px;overflow:hidden;' Essential='1' readonly maxlength='70'>$r_company_name</textarea>
		</td>
		<td colspan='1' align='center' class='RBLine_T' style='line-height:16px;'>성<br>명</td>
		<td colspan='4' align='left' class='BLine'>
			<textarea class='tb' name='r_name' id='r_name' style='width:91%;height:26px;overflow:hidden;' Essential='1' maxlength='30'>$r_name</textarea>
		</td>
	</tr>
	<tr height='35'>
		<td colspan='3' align='center' class='RBLine_T' style='line-height:16px;'>사업장<br>주소</td>
		<td colspan='13' align='left' class='RBLine'>
			<textarea class='tb' name='s_address' id='s_address' style='width:97%;height:26px;overflow:hidden;' maxlength='150'>$s_address</textarea>
		</td>
		<td colspan='3' align='center' class='RBLine_T' style='line-height:16px;'>사업장<br>주소</td>
		<td colspan='13' align='left' class='BLine'>
			<textarea class='tb_readonly'  name='r_address' id='r_address' style='width:97%;height:26px;overflow:hidden;' readonly maxlength='150'>$r_address</textarea>
		</td>
	</tr>
	<tr height='25'>
		<td colspan='3' align='center' class='RBLine_T'>업태</td>
		<td colspan='6' align='left' class='RBLine'>
			<input type='text' class='tb' name='s_state' id='s_state' value='$s_state' style='width:100%;' maxlength='40'>
		</td>
		<td colspan='2' align='center' class='RBLine_T'>종목</td>
		<td colspan='5' align='left' class='RBLine'>
			<input type='text' class='tb' name='s_item' id='s_item' value='$s_item' style='width:100%;' maxlength='40'>
		</td>
		<td colspan='3' align='center' class='RBLine_T'>업태</td>
		<td colspan='6' align='left' class='RBLine'>
			<input type='text' class='tb' name='r_state' id='r_state' value='$r_state' style='width:100%;' maxlength='40'>
		</td>
		<td colspan='2' align='center' class='RBLine_T'>종목</td>
		<td colspan='5' align='left' class='BLine'>
			<input type='text' class='tb' name='r_item' id='r_item' value='$r_item' style='width:100%;' maxlength='40'>
		</td>
	</tr>
	<tr height='25'>
		<td colspan='3' align='center' class='RBLine_T'>담당자</td>
		<td colspan='4' align='left' class='BLine'>
			<input type='text' class='tb' name='s_personin' id='s_personin' value='$s_personin' style='width:100%;' Essential='1' maxlength='30'>
		</td>
		<td colspan='2' align='left' class='RBLine'>
			<img src='./img/btn_searchcontact.gif' id='Select_Contact' class='Buttons' width='40' height='19'></td>
		<td colspan='2' align='center' class='RBLine_T' style='padding:0px;'>연락처</td>
		<td colspan='5' align='left' class='RBLine'>
			<input type='text' class='tb' name='s_tel' id='s_tel' value='$s_tel' style='width:100%;' maxlength='20'>
		</td>
		<td colspan='3' align='center' class='RBLine_T'>담당자</td>
		<td colspan='6' align='left' class='RBLine'>
			<input type='text' class='tb' name='r_personin' id='r_personin' value='$r_personin' style='width:100%;' Essential='1' maxlength='30' BlurEvent='1' >
		</td>
		<td colspan='2' align='center' class='RBLine_T' style='padding:0px;'>연락처</td>
		<td colspan='5' align='left' class='BLine'>
			<input type='text' class='tb' name='r_tel' id='r_tel' value='$r_tel' style='width:100%;' maxlength='20'>
		</td>
	</tr>
	<tr height='25'>
		<td colspan='3' align='center' class='RBLine_T'>이메일</td>
		<td colspan='13' align='left' class='RBLine'>
			<input type='text' class='tb' name='s_email1' id='s_email1' value='$s_email[0]' style='width:70px;' Essential='1'>@<input type='text' class='tb' name='s_email2' id='s_email2' value='$s_email[1]' style='width:70px;' Essential='1'>
			<select name='email_com' id='email_com' class='sb'>
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
		<td colspan='3' align='center' class='RBLine_T'>이메일</td>
		<td colspan='13' align='left' class='BLine'>
			<input type='text' class='tb' name='r_email1' id='r_email1' value='$r_email[0]' style='width:70px;'>@<input type='text' class='tb'  name='r_email2' id='r_email2' value='$r_email[1]' style='width:70px;'>
			<select name='email_com2' id='email_com2' class='sb'>
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
	";

	if($tax_type == 1)
	{
	$Contents .= "
	<tr height='1'><td colspan='34'></td></tr>
	<tr height='1'><td colspan='34' bgcolor='$line_color'></td></tr>
	<tr height='25'>
		<td colspan='4' align='center' class='RBLine_T' bgcolor='#F7F5F4'><b>과세형태</b></td>
		<td colspan='5' align='left' class='BLine' bgcolor='#F7F5F4'>
			<input type='hidden' name='TaxCalcType' value='2'>
			<a id='RadioText'><input type='radio' name='tax_per' id='tax_per' value='1' checked onclick='calculator_ing()'>&nbsp;과세(10%)</a>
		</td>
		
		<td colspan='25' align='left' class='BLine' bgcolor='#F7F5F4'>
			<a id='RadioText'><input type='radio' name='tax_per' id='tax_per' value='2' onclick='calculator_ing()'>&nbsp;영세(0%)</a>
		</td>
		
	</tr>
	";
	}
	else
	{
		$Contents .= "
		<input type='hidden' name='tax_per' id='tax_per' value='2'>
		";
	}

	$Contents .= "
	<tr height='25'>
		<td colspan='4' align='center' class='RBLine_T' bgcolor='#F7F5F4'><b>작성방법</b></td>
		<td colspan='5' align='left' class='BLine' bgcolor='#F7F5F4'>
			<a id='RadioText'><input type='radio' name='WriteType' id='WriteType' value='1' checked onclick='writeType_click()'>&nbsp;직접입력</a>
		</td>
		<td colspan='5' align='left' class='BLine' bgcolor='#F7F5F4'>
			<a id='RadioText'><input type='radio' name='WriteType' id='WriteType' value='2' onclick='writeType_click()'>&nbsp;수량/단가</a>
		</td>
		<td colspan='5' align='left' class='BLine' bgcolor='#F7F5F4'>
			<a id='RadioText'><input type='radio' name='WriteType' id='WriteType' value='3' onclick='writeType_click()'>&nbsp;공급가액</a>
		</td>
		<td colspan='4' align='left' class='BLine' bgcolor='#F7F5F4'>
			<a id='RadioText'><input type='radio' name='WriteType' id='WriteType' value='4' onclick='writeType_click()'>&nbsp;합계금액</a>
		</td>
		<td colspan='11' align='left' class='BLine' bgcolor='#F7F5F4'>
			<input type='text' class='tb_readonly' name='input_total' id='input_total' style='width:100px;IME-MODE:disabled;text-align:right;' valtype='NUM'>
			<input type='button' id='total_input_btn' value='입력' onclick='calculator_ing()'>
		</td>
	</tr>
	<tr height='1'><td colspan='34'></td></tr>
	<tr height='1'><td colspan='34' bgcolor='$line_color'></td></tr>
	<tr height='25'>
		<td colspan='4' align='center' class='RBLine_T'><b><img id='WriteDtIcon' src='./img/dtp_icon.gif' align='absmiddle' class='Buttons'> 작성</b></td>
		<td colspan='16' align='center' class='RBLine_T'><b>공급가액</b></td>
		<td colspan='14' align='center' class='BLine_T'><b>세액</b></td>		
	</tr>
	<tr height='25'>
		<td colspan='4' class='RBLine' style='padding:0px 4px 0px 2px;'><input type='text' name='signdate' id='signdate' value='".date('Y-m-d')."' class='baroCal' style='width:80px;' IconYN='0'></td>
		<td colspan='16' class='RBLine'><input type='text' class='tb_readonly' name='supply_price' id='supply_price' maxlength='18' style='width:95%;IME-MODE:disabled;text-align:right;' value='$supply_price' readonly></td>
		<td colspan='14' class='BLine'><input type='text' class='tb_readonly' name='tax_price' id='tax_price' maxlength='18' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='$tax_price' readonly></td>
	</tr>
	<tr height='1'><td colspan='34'></td></tr>
	<tr height='1'><td colspan='34' bgcolor='$line_color'></td></tr>
	<a id='RemarkBox'>
	<tr height='25'>
		<td colspan='4' align='center' class='RBLine_T'><b>비고1</b></td>
		<td colspan='29' class='RBLine'><input type='text' class='tb' name='marking' id='marking' maxlength='150' style='width:95%;' value=''></td>
		<td colspan='1' class='BLine'></td>
	</tr>
	
	</a>
	<tr height='1'><td colspan='34'></td></tr>
	<tr height='1'><td colspan='34' bgcolor='$line_color'><input type='hidden' name='DetailXML' value=''></td></tr>
	<tr height='25'>
		<td colspan='1' align='center' class='RBLine_T'><b>월</b></td>
		<td colspan='1' align='center' class='RBLine_T'><b>일</b></td>
		<td colspan='7' align='center' class='RBLine_T'><b>품목</b></td>
		<td colspan='3' align='center' class='RBLine_T'><b>규격</b></td>
		<td colspan='3' align='center' class='RBLine_T'><b>수량</b></td>
		<td colspan='3' align='center' class='RBLine_T'><b>단가</b></td>
		<td colspan='5' align='center' class='RBLine_T'><b>공급가액</b></td>
		<td colspan='4' align='center' class='RBLine_T'><b>세액</b></td>
		<td colspan='6' align='center' class='RBLine_T'><b>비고</b></td>
		<td colspan='1' align='center' class='BLine_T'><img src='./img/add.gif' id='add_btn'></td>
	</tr>
	<span id='DetailBox'>
";

if($idx != "")
{
	$SQL_S = "SELECT * FROM tax_sales_detail WHERE p_idx = '$idx'";
	$db->query($SQL_S);
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
		$k = $i + 1;
		
		$idx2 = $db->dt[idx];
		$t_mon = $db->dt[t_mon];
		$t_day = $db->dt[t_day];
		$product = $db->dt[product];
		$p_size = $db->dt[p_size];
		$cnt = $db->dt[cnt];
		$price = $db->dt[price];
		$p_price = $db->dt[p_price];
		$tax = $db->dt[tax];
		$comment = $db->dt[comment];

		$Contents .= "
		<tr height='25' id='Detail1'>
			<td colspan='1' align='center' class='RBLine'>
			<input type='hidden' name='idx2[]' value='$idx2'>$idx2
			<input type='text' class='tb' name='t_mon[$k]' id='t_mon[$k]' style='width:95%;IME-MODE:disabled;text-align:center;' maxlength='2' valtype='NUM' value='$t_mon' onkeyup='num_chk(this)'></td>
			<td colspan='1' align='center' class='RBLine'><input type='text' class='tb' name='t_day[$k]' id='t_day[$k]' style='width:95%;IME-MODE:disabled;text-align:center;' maxlength='2' valtype='NUM' value='$t_day' onkeyup='num_chk(this)'></td>
			<td colspan='7' align='center' class='RBLine' style='padding-right:0px;'><input type='text' class='tb' name='product[$k]' id='product[$k]' maxlength='100' style='width:95%;' value='$product'></td>
			<td colspan='3' align='center' class='RBLine'><input type='text' class='tb' name='p_size[$k]' id='p_size[$k]' maxlength='60' style='width:95%;text-align:right;' value=''></td>
			<td colspan='3' align='center' class='RBLine'><input type='text' class='tb' name='cnt[$k]' id='cnt[$k]' maxlength='12' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='$cnt' onkeyup='calculator_ing();num_chk(this);'></td>
			<td colspan='3' align='center' class='RBLine'><input type='text' class='tb' name='price[$k]' id='price[$k]' maxlength='18' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='$price' onkeyup='calculator_ing();num_chk(this);'></td>
			<td colspan='5' align='center' class='RBLine'><input type='text' class='tb' name='p_price[$k]' id='p_price[$k]' maxlength='18' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='$p_price' onkeyup='calculator_ing();num_chk(this);'></td>
			<td colspan='4' align='center' class='RBLine'><input type='text' class='tb' name='tax[$k]' id='tax[$k]' maxlength='18' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='$tax' onkeyup='calculator_ing();num_chk(this);'></td>
			<td colspan='6' align='center' class='RBLine'><input type='text' class='tb' name='comment[$k]' id='comment[$k]' maxlength='100' style='width:95%;' value='$comment'></td>
			<td colspan='1' align='center' class='RBLine'><img src='./img/close.gif' id='DelDetail' class='Buttons' onclick='del_row(1)'></td>
		</tr>
		";
	}
	$Contents .= "<script>var add_cnt = ".$k.";</script>";
}
else
{
	for($k=1; $k<=4; $k++){
	$Contents .= "
		<tr height='25' id='Detail".$k."'>
			<td colspan='1' align='center' class='RBLine'><input type='text' class='tb' name='t_mon[$k]' id='t_mon[$k]' style='width:95%;IME-MODE:disabled;text-align:center;' maxlength='2' valtype='NUM' value='' onkeyup='num_chk(this)'></td>
			<td colspan='1' align='center' class='RBLine'><input type='text' class='tb' name='t_day[$k]' id='t_day[$k]' style='width:95%;IME-MODE:disabled;text-align:center;' maxlength='2' valtype='NUM' value='' onkeyup='num_chk(this)'></td>
			<td colspan='7' align='center' class='RBLine' style='padding-right:0px;'><input type='text' class='tb' name='product[$k]' id='product[$k]' maxlength='100' style='width:95%;' value=''></td>
			<td colspan='3' align='center' class='RBLine'><input type='text' class='tb' name='p_size[$k]' id='p_size[$k]' maxlength='60' style='width:95%;text-align:right;' value=''></td>
			<td colspan='3' align='center' class='RBLine'><input type='text' class='tb' name='cnt[$k]' id='cnt[$k]' maxlength='12' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='' onkeyup='calculator_ing();num_chk(this);'></td>
			<td colspan='3' align='center' class='RBLine'><input type='text' class='tb' name='price[$k]' id='price[$k]' maxlength='18' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='' onkeyup='calculator_ing();num_chk(this);'></td>
			<td colspan='5' align='center' class='RBLine'><input type='text' class='tb' name='p_price[$k]' id='p_price[$k]' maxlength='18' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='' onkeyup='calculator_ing();num_chk(this);'></td>
			<td colspan='4' align='center' class='RBLine'><input type='text' class='tb' name='tax[$k]' id='tax[$k]' maxlength='18' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='' onkeyup='calculator_ing();num_chk(this);'></td>
			<td colspan='6' align='center' class='RBLine'><input type='text' class='tb' name='comment[$k]' id='comment[$k]' maxlength='100' style='width:95%;' value=''></td>
			<td colspan='1' align='center' class='RBLine'><img src='./img/close.gif' id='DelDetail' class='Buttons' onclick='del_row(1)'></td>
		</tr>
	";
	}
}
	
$Contents .= "	
	</span>
	<tr height='25'>
		<td colspan='9' align='center' class='RBLine_T'><b>합계금액</b></td>
		<td colspan='4' align='center' class='RBLine_T'>현금</td>
		<td colspan='4' align='center' class='RBLine_T'>수표</td>
		<td colspan='4' align='center' class='RBLine_T'>어음</td>
		<td colspan='4' align='center' class='RBLine_T'>외상미수금</td>
		<td colspan='9' rowspan='2' align='center' class='NoLine'>
			<table border='0' cellpadding='0' cellspacing='0' width='100%' style='table-layout:fixed;'>
				<tr>
					<td height='23'>이 금액을 </td>
				</tr>
				<tr>
					<td height='23'>
						<a id='RadioText'><input type='radio' name='claim_kind' id='claim_kind' value='1' >&nbsp;영수</a>
						<a id='RadioText'><input type='radio' name='claim_kind' id='claim_kind' value='2' checked>&nbsp;청구</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr height='25'>
		<td colspan='9' align='right' class='RLine'><input type='text' class='tb_readonly' name='total_price' id='total_price' maxlength='18' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='$total_price' readonly></td>
		<td colspan='4' align='right' class='RLine'><input type='text' class='tb' name='cash' id='cash' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='$cash'></td>
		<td colspan='4' align='right' class='RLine'><input type='text' class='tb' name='cheque' id='cheque' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='$cheque'></td>
		<td colspan='4' align='right' class='RLine'><input type='text' class='tb' name='pro_note' id='pro_note' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='$pro_note'></td>
		<td colspan='4' align='right' class='RLine'><input type='text' class='tb' name='outstanding' id='outstanding' style='width:95%;IME-MODE:disabled;text-align:right;' valtype='NUM' value='$outstanding'></td>
	</tr>
</table>


";

	echo $Contents;
?>