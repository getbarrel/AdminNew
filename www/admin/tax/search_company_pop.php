<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include("../class/layout.class");
	$db = new Database;
	$db2 = new Database;

	$kind = $_REQUEST[kind];
	$search_type = $_REQUEST[s_type];
	$search_text = $_REQUEST[sch_txt];
	
	if($search_type == "total" || $search_type == "")		$checked1 = "checked";
	if($search_type == "com_number")				$checked2 = "checked";
	if($search_type == "com_name")				$checked3 = "checked";
	if($search_type == "personin")					$checked4 = "checked";
?>
<html>
<title>거래처검색</title>
<meta http-equiv='Content-Type' content='text/html; charset=euc-kr'>
<LINK REL='stylesheet' HREF='/admin/include/admin.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/css/design.css' TYPE='text/css'>
<LINK REL='stylesheet' HREF='/admin/common/css/design.css' TYPE='text/css'>
<script language='JavaScript' src='./webedit/webedit.js'></script>	
<script language='JavaScript' src='./js/admin.js'></Script>
<script language='JavaScript' src='/admin/js/jquery-1.4.js'></Script>
<script language='JavaScript' src='/admin/member/member.js'></Script>
<script language='JavaScript' src='/admin/tax/tax.js'></Script>


<body style="margin:0px 0px 0px 0px" onload="resize()">
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	
	<tr>
		<td>
<?
$Contents = "
	<script>
	function company_info_pop(idx, c_type)
	{
		window.open('./company_write_step2.php?idx='+idx+'&s_type='+c_type,'company','width=550,height=300');
	}

	function company_choice(no, kind)
	{
		var c_number = $('input[id=\'c_number['+no+']\']').val();
		var c_name = $('input[id=\'c_name['+no+']\']').val();
		var c_ceo = $('input[id=\'c_ceo['+no+']\']').val();
		var addr = $('input[id=\'addr1['+no+']\']').val() + $('input[id=\'addr2['+no+']\']').val();
		var c_status = $('input[id=\'c_status['+no+']\']').val();
		var c_items = $('input[id=\'c_items['+no+']\']').val();
		var personin = $('input[id=\'personin['+no+']\']').val();
		var email1 = $('input[id=\'email1['+no+']\']').val();
		var email2 = $('input[id=\'email2['+no+']\']').val();
		var tel = $('input[id=\'tel['+no+']\']').val();
		
		if(kind == 2)
		{
			opener.$('#s_company_number').val(c_number);
			opener.$('#s_company_name').val(c_name);
			opener.$('#s_name').val(c_ceo);
			opener.$('#s_address').val(addr);
			opener.$('#s_state').val(c_status);
			opener.$('#s_item').val(c_items);
			opener.$('#s_personin').val(personin);
			opener.$('#s_email1').val(email1);
			opener.$('#s_email2').val(email2);
			opener.$('#s_tel').val(tel);
		}
		else
		{
			opener.$('#r_company_number').val(c_number);
			opener.$('#r_company_name').val(c_name);
			opener.$('#r_name').val(c_ceo);
			opener.$('#r_address').val(addr);
			opener.$('#r_state').val(c_status);
			opener.$('#r_item').val(c_items);
			opener.$('#r_personin').val(personin);
			opener.$('#r_email1').val(email1);
			opener.$('#r_email2').val(email2);
			opener.$('#r_tel').val(tel);
		}

		window.close();
	}

	$(document).ready(function(){
		$('#tax_tab1').click(function(){
			$('#tab1_view').slideDown();
		});

		$('#company_write').click(function(){
			opener.parent.location.href = '../basic/seller.add.php';
			window.close();
		});
	
		$('#sch_frm').submit(function(){
			if($('#search_txt').val() == '')
			{
				alert ('검색어를 입력해주세요.');
				$('#search_txt').focus();
				return false;
			}
			
			$('#sch_frm').action = '$PHP_SELF';
			$('#sch_frm').method = 'POST';
		});
	});
	</script>
	";
	$Contents .= "

	<form name='sch_frm' id='sch_frm'>
	<input type='hidden' name='kind' value='$kind'>
	<!--table width='100%' class='input_table_box' cellpadding='0' cellspacing='1' border='0' bgcolor='#CCCCCC' style='margin:10px 0 0 0'>
	  <tr height='30'>
		<td bgcolor='#F2F2F2' align='center' width='60'>검색</td>
		<td bgcolor='#FFFFFF' style='padding:5px 10px 5px 10px'>
		  <input type='radio' name='s_type' id='s_type1' value='total' $checked1><label for='s_type1'>전체&nbsp;</label>
		  <input type='radio' name='s_type' id='s_type2' value='com_number' $checked2><label for='s_type2'>사업자번호(주민번호)&nbsp;</label>
		  <input type='radio' name='s_type' id='s_type3' value='ccd.com_name' $checked3><label for='s_type3'>회사명&nbsp;</label>
		  <input type='radio' name='s_type' id='s_type4' value='personin' $checked4><label for='s_type4'>담당자&nbsp;</label>
		  <input type='text' name='sch_txt' id='sch_txt' size='13'>
		  <input type='image' src='/admin/image/search01.gif' value='검색' id='frm_btn' align='absbottom'>
		</td>
	  </tr>
	</table-->
	<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box'>
		<col width='170'>
		<col width='*'>
		<tr height='40' valign='middle'>
			<td align='center'  colspan=2 class='input_box_title'><b>셀러검색</b>
				<select name='s_type'>
					<option value='ccd.com_name' ".($s_type == 'com_name' || $s_type == ''?'selected':'')."> 상호명</option>
					<option value='customer_name' ".($s_type == 'customer_name'?'selected':'')."> 셀러명 </option>
					<option value='com_ceo' ".($s_type == 'com_ceo'?'selected':'')."> 대표자 </option>
					<option value='com_phone' ".($s_type == 'com_phone'?'selected':'')."> 전화번호 </option>
				</select>
			</td>
			<td class='input_box_item' style='padding-left:15px;'>
				<input type='text' class='textbox' name='sch_txt' id='sch_txt' size='30' value='".$search_text."'>
				<input type='image' src='../images/".$admininfo['language']."/btn_search.gif'  id='frm_btn' align=absmiddle>
			</td>
		</tr>
	</table>
	</form>

	<table width='100%' class='input_table_box' cellpadding='0' cellspacing='1' border='0' bgcolor='#CCCCCC' style='margin:10px 0 0 0'>
	  <tr height='30' align='center' bgcolor='#F2F2F2'>
		<!--td>선택</td-->
		<td>회사명</td>
		<td>대표자</td>
		<td>셀러명</td>
		<td>셀러ID</td>
		<td>전화번호</td>
	  </tr>
	 ";
	
	/*if($sch_txt != "")
	{
		if($s_type != "total")	$WHERE = " WHERE $s_type like '%$sch_txt%'";
		else					$WHERE = " WHERE company_number like '%$sch_txt%' OR company_name like '%$sch_txt%' OR ceo like '%$sch_txt%' OR personin like '%$chk_txt%'";
	}
*/
	// 리스트 셋
	$CPage = (!$CPage || $CPage < 1) ? 1  : $CPage;		// 현재 페이지 1
	$LNum  = (!$LNum || $LNum < 1)   ? 15 : $LNum;		// 리스트 수 15
	$PNum  = (!$PNum || $PNum < 1)   ? 10 : $PNum;		// 페이지 수 10

	//전체 갯수
	//$TQuery = "SELECT * FROM tax_company_info ".$WHERE;
	//$db->query($TQuery);
	include "../basic/seller_query.php";
	$TOTAL_CNT = $db->total;

	$TPage = ceil($TOTAL_CNT/$LNum);

	//리스트 번호 출력
	$ListNo = $TOTAL_CNT - $LNum*($CPage-1);



	// 페이지 클래스
	include_once $DOCUMENT_ROOT."/admin/class/class.PageDivide.php";
	$pageDivide = new PageDivide($TPage,$PNum,$CPage," | ");
	$PAGES = $pageDivide->Page_Divide("","&sch_txt=$sch_txt&s_type=$s_type");

	$LinkPagePrev	= $PAGES[PagePrev];
	$LinkPageList	= $PAGES[PageList];
	$LinkPageNext	= $PAGES[PageNext];
	$LinkListPrev	= $PAGES[ListPrev];
	$LinkListNext	= $PAGES[ListNext];

	//$SQL = "SELECT * FROM tax_company_info ".$WHERE." ORDER BY idx DESC LIMIT ".($LNum*($CPage-1)).", $LNum";
//	$db->query($SQL);
	//echo $sql;
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
//print_R($db->dt);
		$email = explode("@",$db->dt[tax_mail]);
		$com_zip = explode("-",$db->dt[com_zip]);
		
		$sql = "select id from ".TBL_COMMON_USER." where company_id = '".$db->dt[company_id]."'";
		$db2->query($sql);
		$db2->fetch();
		//echo $sql;
		$Contents .= "
		  <input type='hidden' id='c_number[$i]' value='".$db->dt[com_number]."'>
		  <input type='hidden' id='c_name[$i]' value='".$db->dt[com_name]."'>
		  <input type='hidden' id='c_ceo[$i]' value='".$db->dt[com_ceo]."'>
		  <input type='hidden' id='zip1[$i]' value='".$db->dt[com_zip][0]."'>
		  <input type='hidden' id='zip2[$i]' value='".$db->dt[com_zip][1]."'>
		  <input type='hidden' id='addr1[$i]' value='".$db->dt[com_addr1]."'>
		  <input type='hidden' id='addr2[$i]' value='".$db->dt[com_addr2]."'>
		  <input type='hidden' id='c_status[$i]' value='".$db->dt[com_business_category]."'>
		  <input type='hidden' id='c_items[$i]' value='".$db->dt[com_business_status]."'>
		  <input type='hidden' id='personin[$i]' value='".$db->dt[tax_person_name]."'>
		  <input type='hidden' id='email1[$i]' value='".$email[0]."'>
		  <input type='hidden' id='email2[$i]' value='".$email[1]."'>
		  <input type='hidden' id='tel[$i]' value='".$db->dt[tax_person_phone]."'>

		  <tr height='30' align='center' bgcolor='#FFFFFF'>
			<!--td><input type='radio' name='choice' id='choice' onclick='company_choice(".$i.",".$kind.")'></td-->
			<td  onclick='company_choice(".$i.",".$kind.")'>".$db->dt[com_name]."</td>
			<td>".$db->dt[com_ceo]."</td>
			<td>".$db->dt[customer_name]."</td>
			<td>".$db2->dt[id]."</td>
			<td>".$db->dt[com_phone]."</td>
		  </tr>
		  ";
	}
	if($db->total < 1)
	{
		$Contents .= "
		 <tr height='30' align='center' bgcolor='#FFFFFF'>
			<td colspan='8'> 검색결과가 존재하지 않습니다.</td>
		  </tr>
		";
	}

	$Contents .= "
	</table>

	<table width='100%'>
	  <tr>
		<td align='left' width='25%'></td>
		<!--td align='center'>$LinkPagePrev << </a> [ $LinkPageList ] $LinkPageNext >></a></td-->
		<td align='center'>".$str_page_bar."</td>
		<td align='right' width='25%'></td>
	  </tr>
	  <tr height='50'>
		<td colspan='3' align='center'><img src='./img/new_info.gif' id='company_write' value='신규 거래처 등록' style='cursor:hand'></td>
	  </tr>
	</table>
	";

	//echo $Contents;
	$P = new ManagePopLayOut();
    $P->addScript = "<script language='javascript' src='../basic/company.add.js'></script>".$Script;
    $P->Navigation = "문서작성 > 거래처검색";
    $P->NaviTitle = "거래처검색";
    $P->title = "거래처검색";
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
?>
		</td>
	</tr>
</table>

</body>