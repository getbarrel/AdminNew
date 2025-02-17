<?

include("../class/layout.class");

$Script = "
<script language='javascript'>

</script>";

$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$db = new Database;

if($pre_type=="ready"){
	$sub_title=getOrderStatus(ORDER_STATUS_ACCOUNT_COMPLETE)."내역";
	$where = " WHERE ar.status='".ORDER_STATUS_ACCOUNT_COMPLETE."' ";
}else{
	$sub_title=getOrderStatus(ORDER_STATUS_ACCOUNT_PAYMENT)."내역";
	$where = " WHERE ar.status='".ORDER_STATUS_ACCOUNT_PAYMENT."' ";
}

if($admininfo[admin_level] == 9){
	if($company_id != "") $where .= " and ar.company_id='$company_id' ";

	if($admininfo[mem_type] == "MD"){
		$where .= " and ar.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
	}

}else if($admininfo[admin_level] == 8){
	$where .= " and ar.company_id = '".$admininfo[company_id]."' ";
}


if($check_search_date){
	$where .= " and  date_format(ar.regdate,'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
}

//다중검색으로 추가
if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
	//다중검색 시작 2014-04-10 이학봉

	//조인상태땜에 어쩔수 없이 셀러명조인시 변수갑을 바꿧음 2014-08-19 이학봉

	if($search_type == 'c.com_name'){

		if($search_text != ""){
			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$search_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$search_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$search_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$search_where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n
	
				$search_array = explode("\n",trim($search_text));
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$search_where .= "and ( ";

				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$search_where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$search_where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$search_where .= ")";
			}else{
				$search_where .= " and ".$search_type." = '".trim($search_text)."'";
			}
		}

	}else{

		if($search_text != ""){
			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n
				$search_array = explode("\n",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
				$where .= "and ( ";

				for($i=0;$i<count($search_array);$i++){
					$search_array[$i] = trim($search_array[$i]);
					if($search_array[$i]){
						if($i == count($search_array) - 1){
							$where .= $search_type." = '".trim($search_array[$i])."'";
						}else{
							$where .= $search_type." = '".trim($search_array[$i])."' or ";
						}
					}
				}
				$where .= ")";
			}else{
				$where .= " and ".$search_type." = '".trim($search_text)."'";
			}
		}
	
	}

}else{	//검색어 단일검색
	if($search_text != ""){
		if(substr_count($search_text,",")){
			if($search_type == 'c.com_name'){
				
				$search_where .= " and ".$search_type." in ('".str_replace(",","','",str_replace(" ","",$search_text))."') ";
			}else{
				$where .= " and ".$search_type." in ('".str_replace(",","','",str_replace(" ","",$search_text))."') ";
			}
		}else{
			if($search_type == 'c.com_name'){

				$search_where .= " and ".$search_type." LIKE '%".trim($search_text)."%' ";
			}else{
				$where .= " and ".$search_type." LIKE '%".trim($search_text)."%' ";
			}
		}
	}
}


$sql = "select
					ar.*
				from
					shop_accounts_remittance ar
					left join ".TBL_COMMON_COMPANY_DETAIL." c on ar.company_id = c.company_id

				$where
				$search_where";
$db->query($sql);
$total = $db->total;




/*
$sql = "select
				ar.* , c.com_name, c.com_number, c.basic_bank, c.holder_name, c.bank_num
			from
			(
				select
					* 
				from
					shop_accounts_remittance ar
				$where
				order by ar_ix desc";
			if($act != "accounts_excel"){
				$sql .= "
				limit $start,$max";
			}
			$sql .= "
			) ar
			left join ".TBL_COMMON_COMPANY_DETAIL." c on ar.company_id = c.company_id
			
		where
			1
			$search_where" ;
*/

$sql = "select
	ar.* , c.com_name, c.com_number, c.basic_bank, c.holder_name, c.bank_num
from
	shop_accounts_remittance ar
	left join ".TBL_COMMON_COMPANY_DETAIL." c on ar.company_id = c.company_id

$where
$search_where";

if($act != "accounts_excel"){
	$sql .=" limit $start,$max";
}

$db->query($sql);


if($act == "accounts_excel"){

	include '../include/phpexcel/Classes/PHPExcel.php';

	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$accounts_excel = new PHPExcel();

	// 속성 정의
	$accounts_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts List")
								 ->setSubject("accounts List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts List");

	$accounts_excel->getActiveSheet(0)->mergeCells('A1:A2');
	$accounts_excel->getActiveSheet(0)->mergeCells('B1:B2');
	$accounts_excel->getActiveSheet(0)->mergeCells('C1:C2');
	$accounts_excel->getActiveSheet(0)->mergeCells('D1:D2');
	$accounts_excel->getActiveSheet(0)->mergeCells('E1:G1');
	$accounts_excel->getActiveSheet(0)->mergeCells('H1:J1');
	$accounts_excel->getActiveSheet(0)->mergeCells('M1:M2');
	$accounts_excel->getActiveSheet(0)->mergeCells('N1:N2');
	$accounts_excel->getActiveSheet(0)->mergeCells('O1:O2');
	$accounts_excel->getActiveSheet(0)->mergeCells('P1:R1');

	$accounts_excel->getActiveSheet(0)->setCellValue('A' . 1, "순번");
	$accounts_excel->getActiveSheet(0)->setCellValue('B' . 1, "송금대기등록일자");
	$accounts_excel->getActiveSheet(0)->setCellValue('C' . 1, "셀러명");
	$accounts_excel->getActiveSheet(0)->setCellValue('D' . 1, "사업자등록번호");
	$accounts_excel->getActiveSheet(0)->setCellValue('E' . 1, "과세상품");
	$accounts_excel->getActiveSheet(0)->setCellValue('E' . 2, "공급가");
	$accounts_excel->getActiveSheet(0)->setCellValue('F' . 2, "세액");
	$accounts_excel->getActiveSheet(0)->setCellValue('G' . 2, "합계");
	$accounts_excel->getActiveSheet(0)->setCellValue('H' . 1, "과세배송비");
	$accounts_excel->getActiveSheet(0)->setCellValue('H' . 2, "공급가");
	$accounts_excel->getActiveSheet(0)->setCellValue('I' . 2, "세액");
	$accounts_excel->getActiveSheet(0)->setCellValue('J' . 2, "합계");
	$accounts_excel->getActiveSheet(0)->setCellValue('K' . 1, "면세상품");
	$accounts_excel->getActiveSheet(0)->setCellValue('K' . 2, "공급가");
	$accounts_excel->getActiveSheet(0)->setCellValue('L' . 1, "면세배송비");
	$accounts_excel->getActiveSheet(0)->setCellValue('L' . 2, "공급가");
	$accounts_excel->getActiveSheet(0)->setCellValue('M' . 1, "총정산합계");
	$accounts_excel->getActiveSheet(0)->setCellValue('N' . 1, "정산상태");
	$accounts_excel->getActiveSheet(0)->setCellValue('O' . 1, "정산지급방식");
	$accounts_excel->getActiveSheet(0)->setCellValue('P' . 1, "정산계좌");
	$accounts_excel->getActiveSheet(0)->setCellValue('P' . 2, "은행");
	$accounts_excel->getActiveSheet(0)->setCellValue('Q' . 2, "예금주");
	$accounts_excel->getActiveSheet(0)->setCellValue('R' . 2, "계좌번호");


	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
		
		$no = $total - $i;
				
		$basic_bank = $arr_banks_name[$db->dt[basic_bank]];

		$accounts_excel->getActiveSheet()->setCellValue('A' . ($i + 3), $no);
		$accounts_excel->getActiveSheet()->setCellValue('B' . ($i + 3), substr($db->dt[regdate],0,10));
		$accounts_excel->getActiveSheet()->setCellValue('C' . ($i + 3), $db->dt[com_name]);		
		$accounts_excel->getActiveSheet()->setCellValue('D' . ($i + 3), $db->dt[com_number]);
		$accounts_excel->getActiveSheet()->setCellValue('E' . ($i + 3), $db->dt[p_tax_coprice]);
		$accounts_excel->getActiveSheet()->setCellValue('F' . ($i + 3), $db->dt[p_tax_price]);
		$accounts_excel->getActiveSheet()->setCellValue('G' . ($i + 3), $db->dt[p_tax_total_price]);
		$accounts_excel->getActiveSheet()->setCellValue('H' . ($i + 3), $db->dt[d_tax_coprice]);
		$accounts_excel->getActiveSheet()->setCellValue('I' . ($i + 3), $db->dt[d_tax_price]);
		$accounts_excel->getActiveSheet()->setCellValue('J' . ($i + 3), $db->dt[d_tax_total_price]);
		$accounts_excel->getActiveSheet()->setCellValue('K' . ($i + 3), $db->dt[p_tax_free_price]);
		$accounts_excel->getActiveSheet()->setCellValue('L' . ($i + 3), $db->dt[d_tax_free_price]);
		$accounts_excel->getActiveSheet()->setCellValue('M' . ($i + 3), $db->dt[total_price]);
		$accounts_excel->getActiveSheet()->setCellValue('N' . ($i + 3), getOrderStatus($db->dt[status]));
		$accounts_excel->getActiveSheet()->setCellValue('O' . ($i + 3), getMethodStatus($db->dt[account_method]));
		$accounts_excel->getActiveSheet()->setCellValue('P' . ($i + 3), $basic_bank);
		$accounts_excel->getActiveSheet()->setCellValue('Q' . ($i + 3), $db->dt[holder_name]);
		$accounts_excel->getActiveSheet()->setCellValue('R' . ($i + 3), $db->dt[bank_num]);
	}

	// 첫번째 시트 선택
	$accounts_excel->setActiveSheetIndex(0);

	$accounts_excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$accounts_excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$accounts_excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$accounts_excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
	$accounts_excel->getActiveSheet()->getColumnDimension('R')->setWidth(10);

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","업체별송금내역.xls").'"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($accounts_excel, 'Excel5');
	$objWriter->save('php://output');

	exit;
}

$Contents = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<!--col width='25%' />
	<col width='25%' />
	<col width='25%' />
	<col width='25%' /-->
	<tr>
		<td align='left' colspan=4>".GetTitleNavigation($sub_title, "셀러정산 > ".$sub_title)."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding:10px 0px 4px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:3px;'><img src='../image/title_head.gif' align=absmiddle><b> 정산검색</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
</table>
<form name='search_frm' method='get' >
<input type='hidden' name='pre_type' value='$pre_type'>
<input type='hidden' name='mode' value='search'>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	<col width='18%' />
	<col width='32%' />
	<col width='18%' />
	<col width='32%' />
		<tr height=30>
		  <td class='search_box_title'><label for='check_search_date'>송금등록일자</label><input type='checkbox' name='check_search_date' id='check_search_date' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue("1",$check_search_date,"checked")."></td>
		  <td class='search_box_item' colspan=3 >
			".search_date('startDate','endDate',$startDate,$endDate)."
		  </td>
		</tr>";
		if($admininfo[admin_level] == 9){
			$Contents .= "
			<tr height=30>
				<td class='search_box_title'>셀러명  </td>
				<td class='search_box_item' colspan='3'>".CompanyList($company_id,"","")."</td>
			</tr>
			
			<tr>
				<td class='search_box_title'>  검색어
				<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
				
				<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
				<label for='mult_search_use'>(다중검색 체크)</label>
				</td>
				<td class='search_box_item' colspan='3'>
					<table cellpadding=0 cellspacing=0 border='0'>
					<tr>
						<td valign='top'>
							<div style='padding-top:5px;'>
							<select name='search_type' id='search_type' style=\"font-size:12px;\">
								<option value='c.com_name' ".CompareReturnValue("c.com_name",$search_type).">셀러명</option>
							</select>
							</div>
						</td>
						<td style='padding:5px;'>
							<div id='search_text_input_div'>
								<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
							</div>
							<div id='search_text_area_div' style='display:none;'>
								<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
							</div>
						</td>
						<td>
							<div>
								<span class='small blu' > * 다중 검색은 다중 아이디로 검색 지원이 가능합니다. 구분값은 ',' 혹은 'Enter'로 사용 가능합니다. </span>
							</div>
						</td>
					</tr>
					</table>
				</td>
			</tr>

			
			";
		}
	$Contents .= "
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' >
	<tr bgcolor=#ffffff height='100'>
		<td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='cursor:pointer;border:0px;' ></td>
	</tr>
	<tr>
		<td align='right' colspan=4><a href='../seller_accounts/remittance_ready.php?act=accounts_excel&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a--></td>
	</tr>
</table>
</form>";


$Contents .= "
	<form name=listform method=post action='accounts.act.php' onsubmit=\"return account(this)\" target='act'>
	<input type=hidden id='ar_ix' value=''>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' >
	<tr>
		<td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b>송금 내역</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=right>
			<div style='width:100%;height:350px;overflow-y:scroll;overflow-x:scroll;position:relative;' id='scroll_div'>
			<table width='200%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' style='position:absolute;top:0px;margin-top:0px;' id='scroll_title'>
				<col width='30px'>
				<col width='3%'>
				<col width='4%'>
				<col width='*'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='4%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<tr height='25' >
					<td class='s_td' align='center'  rowspan='2'><input type=checkbox  name='all_fix3' onclick='fixAll3(document.listform)'></td>
					<td align='center' class='m_td' rowspan='2'><b>순번</b></td>
					<td align='center' class='m_td' rowspan='2'><b>송금대기<br/>등록일자</b></td>
					<td align='center' class='m_td' rowspan='2'><b>셀러명</b></td>
					<td align='center' class='m_td' rowspan='2'><b>사업자등록번호</b></td>
					<td align='center' class='m_td' colspan='3'><b>과세상품</b></td>
					<td align='center' class='m_td' colspan='3'><b>과세배송비</b></td>
					<td align='center' class='m_td'><b>면세상품</b></td>
					<td align='center' class='m_td'><b>면세배송비</b></td>
					<td align='center' class='m_td' rowspan='2'><b>총정산합계</b></td>
					<td align='center' class='m_td' rowspan='2'><b>정산상태</b></td>
					<td align='center' class='m_td' rowspan='2'><b>정산지급방식</b></td>
					<td align='center' class='m_td' colspan='3'><b>정산계좌</b></td>
					<td align='center' class='m_td' rowspan='2'><b>세금계산서<br/>발행번호</b></td>
					<td align='center' class='m_td' rowspan='2'><b>계산서<br/>발행번호</b></td>
				</tr>
				<tr height='25' >
					<td align='center' class='m_td' ><b>공급가</b></td>
					<td align='center' class='m_td' ><b>세액</b></td>
					<td align='center' class='m_td' ><b>합계</b></td>
					<td align='center' class='m_td' ><b>공급가</b></td>
					<td align='center' class='m_td' ><b>세액</b></td>
					<td align='center' class='m_td' ><b>합계</b></td>
					<td align='center' class='m_td' ><b>공급가</b></td>
					<td align='center' class='m_td' ><b>공급가</b></td>
					<td align='center' class='m_td' ><b>은행</b></td>
					<td align='center' class='m_td' ><b>예금주</b></td>
					<td align='center' class='m_td' ><b>계좌번호</b></td>
				</tr>
			</table>
			<table width='200%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' id='scroll_list'>
				<col width='30px'>
				<col width='3%'>
				<col width='4%'>
				<col width='*'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='4%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>";

	if($db->total){
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);
				
				$no = $total - ($page - 1) * $max - $i;
				
				$basic_bank = $arr_banks_name[$db->dt[basic_bank]];

				$Contents .= "
				<tr onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\" height=30>
					<td class='list_box_td'  align='center'><input type=checkbox name='ar_ix[]' id='ar_ix' value='".$db->dt[ar_ix]."'></td>
					<td class='list_box_td list_bg_gray'  align='center'>".($no)."</td>
					<td class='list_box_td'  align='center'>".substr($db->dt[regdate],0,10)."</td>
					<td class='list_box_td list_bg_gray' style='text-align:left;padding-left:5px;line-height:150%;'>
						".$db->dt[com_name]." ";

						$Contents .= "
						<br/><input type='button' value='추가정산내역' onclick=\"PoPWindow('./accounts_add.pop.php?ar_ix=".$db->dt[ar_ix]."',1000,300,'ac_add')\" /> ";

						$Contents .= "
						<input type='button' value='상세보기' onclick=\"PoPWindow('./accounts_detail.php?ar_ix=".$db->dt[ar_ix]."',1100,300,'ac_detail')\" /> 
					</td>
					<td class='list_box_td '  align='center' >".$db->dt[com_number]."</td>
					<td class='list_box_td list_bg_gray'  align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_tax_coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td' align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_tax_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td list_bg_gray'  align='center'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_tax_total_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td' align='center'  >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[d_tax_coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td list_bg_gray'  align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[d_tax_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>
					<td class='list_box_td'  align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[d_tax_total_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>
					<td class='list_box_td list_bg_gray' align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_tax_free_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td'  align='center'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[d_tax_free_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td list_bg_gray'  align='center'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[total_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td' align='center'>".getOrderStatus($db->dt[status])."</td>
					<td class='list_box_td list_bg_gray' align='center'>".getMethodStatus($db->dt[account_method])."</td>
					<td class='list_box_td'  align='center' >".$basic_bank."</td>
					<td class='list_box_td list_bg_gray'  align='center' >".$db->dt[holder_name]."</td>
					<td class='list_box_td'  align='center' >".$db->dt[bank_num]."</td>
					<td class='list_box_td' align='center'>".$db->dt[tax_no]."</td>
					<td class='list_box_td' align='center'>".$db->dt[bill_no]."</td>
				</tr>";
		}
	}else{
		$Contents .= "<tr height=50><td colspan='21' align=center>".$sub_title."이 없습니다</td></tr>";
	}

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents .= "</table>
			</div>
			<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
			  <tr height=40>
				<td align='right'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
			  </tr>
			</table>
	  	</td>
	  </tr>";


if($admininfo[admin_level] == 9 && $pre_type=="ready"){

	$help_title = "
		<nobr>
			<select name='update_type'>
				<!--option value='1'>검색한주문 전체에게</option-->
				<option value='2'>선택한정산 전체에게</option>
			</select>
			<input type='radio' name='update_kind' id='update_kind' value='' onclick=\"\" checked><label for='update_kind'>송금 처리</label>
		</nobr>";

		$help_text = "
		<script type='text/javascript'>
		<!--

		//-->
		</script>

		<div id='' style='margin-top:15px;'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
			<col width=170>
			<col width=*>
			<tr id='ht_level0_status'>
				<td class='input_box_title'> <b>처리상태</b></td>
				<td class='input_box_item'>
					<input type='radio' name='act' id='account_payment' value='account_payment' onclick=\"\" checked><label for='account_payment' >송금완료</label>
				</td>
			</tr>
		</table>
		</div>
		<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
			<tr height=50>
				<td colspan=4 align=center>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$help_text .= "
					<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
				}else{
					$help_text .= "
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
				}
				$help_text .= "
				</td>
			</tr>
		</table>";

		$Contents .= "<tr><td colspan=4> ".HelpBox($help_title, $help_text,250)."</td></tr>";

}

$Contents .= "
	  </table>
	</form>";

if($admininfo[admin_level] == 9){
$Script .= "
<script type='text/javascript'>
<!--
		$(document).ready(function (){

		//다중검색어 시작 2014-04-10 이학봉

			$('input[name=mult_search_use]').click(function (){
				var value = $(this).attr('checked');

				if(value == 'checked'){
					$('#search_text_input_div').css('display','none');
					$('#search_text_area_div').css('display','');
					
					$('#search_text_area').attr('disabled',false);
					$('#search_texts').attr('disabled',true);
				}else{
					$('#search_text_input_div').css('display','');
					$('#search_text_area_div').css('display','none');

					$('#search_text_area').attr('disabled',true);
					$('#search_texts').attr('disabled',false);
				}
			});

			var mult_search_use = $('input[name=mult_search_use]:checked').val();
				
			if(mult_search_use == '1'){
				$('#search_text_input_div').css('display','none');
				$('#search_text_area_div').css('display','');

				$('#search_text_area').attr('disabled',false);
				$('#search_texts').attr('disabled',true);
			}else{
				$('#search_text_input_div').css('display','');
				$('#search_text_area_div').css('display','none');

				$('#search_text_area').attr('disabled',true);
				$('#search_texts').attr('disabled',false);
			}

		//다중검색어 끝 2014-04-10 이학봉

		});


//-->
</script>
";
}


$P = new LayOut();
$P->addScript = "<script language='javascript' src='accounts.js'></script>\n".$Script;
$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";
$P->strLeftMenu = seller_accounts_menu();
$P->Navigation = "판매자정산관리 > ".$sub_title;
$P->title = $sub_title;
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>