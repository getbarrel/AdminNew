<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='pragma' content='no-cache'>

	<title></title>
</head>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='/admin/js/jquery-1.4.js'></Script>

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
	if($idx != "")
	{
		$db->query("SELECT * FROM tax_sales WHERE idx = '$idx'");
		$db->fetch();
		
		$publish_type		= $db->dt[publish_type];					// 발행타입 (1.매출 2.매입 3.위수탁)
		$tax_type			= $db->dt[tax_type];						// 계산서타입 (1.세금계산서 2.계산서)

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
		$s_email			= $db->dt[s_email];							// 이메일

		$r_company_number	= $db->dt[r_company_number];				// 등록번호
		$r_company_j		= $db->dt[r_company_j];						// 종사업장
		$r_company_name		= $db->dt[r_company_name];					// 상호(업체명)
		$r_name				= $db->dt[r_name];							// 성명
		$r_address			= $db->dt[r_address];						// 사업장주소
		$r_state			= $db->dt[r_state];							// 업태
		$r_items			= $db->dt[r_items];							// 종목
		$r_personin			= $db->dt[r_personin];						// 담당자
		$r_tel				= $db->dt[r_tel];							// 연락처
		$r_email			= $db->dt[r_email];							// 이메일

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

		$national_tax_no	= $db->dt[national_tax_no];

		if($claim_kind == "1") $claim_show = "영수";
		if($claim_kind == "2") $claim_show = "청구";
		
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

	if($publish_type == "") $publish_type = 1;	// 1.매출 2.매입 3.위수탁
	if($tax_type == "")		$tax_type = 1;		// 1.세금계산서 2.계산서


	if($publish_type == "1")
	{
		$line_color = "#e66464";
		$menu_title1 = "매출/매입 문서조회";
		$menu_title2 = "매출 문서조회";
	}
	elseif($publish_type == "2")
	{
		$line_color = "#0033ff";
		$menu_title1 = "매출/매입 문서조회";
		$menu_title2 = "매입 문서조회";
	}
	elseif($publish_type == "3")
	{
		$line_color = "#e66464";
		$menu_title1 = "매출/매입 문서조회";
		$menu_title2 = "위수탁 문서조회";
	}

	if($tax_type == "1")
	{
		$title = "전자세금계산서";
	}
	elseif($tax_type == "2")
	{
		$title = "전자계산서";
	}

	//$line_color = "#e66464";


	/* script */
	$Contents .= "
	<script src='tax.js'></script>
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
	
	<body style='margin:0 0 0 0' onload='resize();print();'>
	";
	
	if($national_tax_no != "")
	{
		$Contents .= "
		<table width='100%'>
			<tr>
				<td align='right'><font color='$line_color'>국세청승인번호</font> : ".$national_tax_no."</td>
			</tr>
		</table>
		";
	}
	
	$Contents .= "
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
		<td colspan='3' align='right' class='NoLine'>$numbering_k</td>
		<td colspan='1' align='center' class='NoLine'>권</td>
		<td colspan='3' align='right' class='NoLine'>$numbering_h</td>
		<td colspan='1' align='center' class='NoLine'>호</td>
	</tr>
	<tr height='25'>
		<td colspan='4' align='right' class='BLine_T'>일련번호 :</td>
		<td colspan='8' class='BLine' align='right' style='padding:0px 8px 0px 3px;'>$numbering</td>
	</tr>
	<tr height='25'>
		<td colspan='1' rowspan='4' align='center' class='RBLine_T' style='line-height:22px;'><b>공<br>급<br>자</b></td>
		<td colspan='3' align='center' class='RBLine_T'>등록번호</td>
		<td colspan='8' align='center' class='RBLine'>
			$s_company_number
		</td>
		<td colspan='3' align='center' class='RBLine_T'>종사업장</td>
		<td colspan='2' align='center' class='RBLine'>
			$s_company_j
		</td>
		<td colspan='1' rowspan='4' align='center' class='RBLine_T' style='line-height:22px;'><b>공<br>급<br>받<br>는<br>자</b></td>
		<td colspan='3' align='center' class='RBLine_T'>등록번호</td>
		
		<td colspan='8' align='center' class='RBLine'>
			$r_company_number
		</td>
		<td colspan='3' align='center' class='RBLine_T'>종사업장</td>
		<td colspan='2' align='center' class='BLine'>
			$r_company_j
		</td>
		
	</tr>
	<tr height='35'>
		<td colspan='3' align='center' class='RBLine_T' style='line-height:16px;'>상호<br>(업체명)</td>
		<td colspan='8' align='left' class='RBLine'>
			$s_company_name
		</td>
		<td colspan='1' align='center' class='RBLine_T' style='line-height:16px;'>성<br>명</td>
		<td colspan='4' align='left' class='RBLine'>
			$s_name
		</td>
		<td colspan='3' align='center' class='RBLine_T' style='line-height:16px;'>상호<br>(업체명)</td>
		<td colspan='8' align='left' class='RBLine'>
			$r_company_name
		</td>
		<td colspan='1' align='center' class='RBLine_T' style='line-height:16px;'>성<br>명</td>
		<td colspan='4' align='left' class='BLine'>
			$r_name
		</td>
	</tr>
	<tr height='35'>
		<td colspan='3' align='center' class='RBLine_T' style='line-height:16px;'>사업장<br>주소</td>
		<td colspan='13' align='left' class='RBLine'>
			$s_address
		</td>
		<td colspan='3' align='center' class='RBLine_T' style='line-height:16px;'>사업장<br>주소</td>
		<td colspan='13' align='left' class='BLine'>
			$r_address
		</td>
	</tr>
	<tr height='25'>
		<td colspan='3' align='center' class='RBLine_T'>업태</td>
		<td colspan='6' align='left' class='RBLine'>
			$s_state
		</td>
		<td colspan='2' align='center' class='RBLine_T'>종목</td>
		<td colspan='5' align='left' class='RBLine'>
			$s_item
		</td>
		<td colspan='3' align='center' class='RBLine_T'>업태</td>
		<td colspan='6' align='left' class='RBLine'>
			$r_state
		</td>
		<td colspan='2' align='center' class='RBLine_T'>종목</td>
		<td colspan='5' align='left' class='BLine'>
			$r_item
		</td>
	</tr>
	
	";

	if($publish_type == "3")
	{
		$Contents .= "
		<tr height='1'><td colspan='34'></td></tr>
		<tr height='1'><td colspan='34' bgcolor='$line_color'></td></tr>
		<tr height='25'>
			<td colspan='4' align='center' class='RBLine_T' bgcolor='#F7F5F4'><b>수탁자</b></td>
			<td colspan='30' align='left' class='BLine' bgcolor='#F7F5F4'>
				포비즈(214-10-09837, 종사업장:$company_j), 안수진, 서울 서초구 양재동 16-3 윤화빌딩 6층, 서비스, 소프트웨어개발및공급
			</td>
		</tr>
		";
	}

	$Contents .= "
	<tr height='1'><td colspan='34'></td></tr>
	<tr height='1'><td colspan='34' bgcolor='$line_color'></td></tr>
	<tr height='25'>
		<td colspan='4' align='center' class='RBLine_T'>작성일자</td>
		<td colspan='16' align='center' class='RBLine_T'><b>공급가액</b></td>
		<td colspan='14' align='center' class='BLine_T'><b>세액</b></td>		
	</tr>
	<tr height='25'>
		<td colspan='4' class='RBLine' style='padding:0px 4px 0px 2px;' align='center'>$signdate</td>
		<td colspan='16' class='RBLine' align='right'>".number_format($supply_price)."</td>
		<td colspan='14' class='BLine' align='right'>".number_format($tax_price)."</td>
	</tr>
	<tr height='1'><td colspan='34'></td></tr>
	<tr height='1'><td colspan='34' bgcolor='$line_color'></td></tr>
	<a id='RemarkBox'>
	<tr height='25'>
		<td colspan='4' align='center' class='RBLine_T'><b>비고</b></td>
		<td colspan='30' class='BLine'>$marking</td>
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
		<td colspan='7' align='center' class='BLine_T'><b>비고</b></td>
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
			<input type='hidden' name='idx2[]' value='$idx2'>
			".$t_mon."</td>
			<td colspan='1' align='center' class='RBLine'>".$t_day."</td>
			<td colspan='7' align='center' class='RBLine' style='padding-right:0px;'>".$product."</td>
			<td colspan='3' align='center' class='RBLine'>".$p_size."</td>
			<td colspan='3' align='center' class='RBLine'>".$cnt."</td>
			<td colspan='3' align='center' class='RBLine'>".number_format($price)."</td>
			<td colspan='5' align='center' class='RBLine'>".number_format($p_price)."</td>
			<td colspan='4' align='center' class='RBLine'>".number_format($tax)."</td>
			<td colspan='7' align='center' class='BLine'>".$comment."</td>
		</tr>
		";
	}
	$Contents .= "<script>var add_cnt = ".$k.";</script>";
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
			이 금액을 [$claim_show] 함
		</td>
	</tr>
	<tr height='25'>
		<td colspan='9' align='right' class='RLine'>".number_format($total_price)."</td>
		<td colspan='4' align='right' class='RLine'>".number_format($cash)."</td>
		<td colspan='4' align='right' class='RLine'>".number_format($cheque)."</td>
		<td colspan='4' align='right' class='RLine'>".number_format($pro_note)."</td>
		<td colspan='4' align='right' class='RLine'>".number_format($outstanding)."</td>
	</tr>
</table>
</body>
";

echo $Contents;
?>
