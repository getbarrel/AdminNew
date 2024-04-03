<?

/////////////////////////////////////////////////////////////
/*

입점배송방식  : 위탁 (통합) - 배송비 정산하지 않는다		delivery_type = 1
                입점 (개별발송) - 배송비 정산			delivery_type = 2

입점정산방식 : 중개 (수수료률) 정산기준금액 (최종판매가)	account_type= 1	ptprice
				    매입  정산기준금액 (공급가)			account_type = 2	coprice
					  선매입(미정산) : 정산에 반영하지 않는다 		account_type = 3

과세구분 : 과세	surtax_yorn = N
			  면세 surtax_yorn = Y
			  영세.(정산에는 영향이 미치지 않느다. )	surtax_yorn = P


정산 수수료 : 상품별 개별수수료 잇을경우 우선 순위 사용
              상품별 개별수수료 없을경우 셀러관리 수수료률 사용   현재 프로세스가 적용되어 있음
*/
/////////////////////////////////////////////////////////////


include("../class/layout.class");
//include ("./accounts.lib.php");


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

$where .=" WHERE status='".ORDER_STATUS_ACCOUNT_READY."' ";

//ac_info 정산 설정1 : 기간별 2:상품별
if($pre_type=="product"){
	$where .= " and ac_info='2' ";
}else{
	$where .= " and ac_info='1' ";
}

if($admininfo[admin_level] == 9){
	if($company_id != "") $where .= " and ac.company_id='$company_id' ";

	if($admininfo[mem_type] == "MD"){
		$where .= " and ac.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
	}

}else if($admininfo[admin_level] == 8){
	$where .= " and ac.company_id = '".$admininfo[company_id]."' ";
}

if($mode == "search"){
	if($check_search_date){
		$where .= " and  date_format(ac.ac_date,'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
	}
	
	if(is_array($ac_type)){
		$where .= " and ac.ac_type in ('".implode("','",$ac_type)."') ";
	}else{
		if($ac_type!=""){
			$where .= " and ac.ac_type= '".$ac_type."' ";
		}
	}

	
	if(is_array($surtax_yorn)){
		$where .= " and ac.surtax_yorn in ('".implode("','",$surtax_yorn)."') ";
	}else{
		if($surtax_yorn!=""){
			$where .= " and ac.surtax_yorn= '".$surtax_yorn."' ";
		}
	}

	if(is_array($account_method)){
		$where .= " and ac.account_method in ('".implode("','",$account_method)."') ";
	}else{
		if($account_method!=""){
			$where .= " and ac.account_method= '".$account_method."' ";
		}
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

}


$sql = "select
					*
				from
					".TBL_SHOP_ACCOUNTS." ac
					left join ".TBL_COMMON_COMPANY_DETAIL." as c on (ac.company_id = c.company_id)
				$where
				$search_where";
$db->query($sql);
$total = $db->total;
$accounts=$db->fetchall("object");

for($i=0;$i < count($accounts);$i++){
	if($ac_ix_param == ""){
		$ac_ix_param = "ac_ix_text=".$accounts[$i][ac_ix];
	}else{
		$ac_ix_param .= "|".$accounts[$i][ac_ix];
	}
}

$sql = "select
	ac.* , c.com_name
from
	".TBL_SHOP_ACCOUNTS." ac
	left join ".TBL_COMMON_COMPANY_DETAIL." as c on (ac.company_id = c.company_id)
$where
$search_where";

/*
$sql = "select
				ac.* , c.com_name
			from
			(
				select
					* 
				from
					".TBL_SHOP_ACCOUNTS." ac
				$where
				order by ac_ix desc";
			if($act != "accounts_excel"){
				$sql .= "
				limit $start,$max";
			}
			$sql .= "
			) ac
			left join ".TBL_COMMON_COMPANY_DETAIL." c on ac.company_id = c.company_id
			
		where
			1
			$search_where" ;
*/

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
	$accounts_excel->getActiveSheet(0)->mergeCells('E1:E2');
	$accounts_excel->getActiveSheet(0)->mergeCells('F1:J1');
	$accounts_excel->getActiveSheet(0)->mergeCells('K1:N1');
	$accounts_excel->getActiveSheet(0)->mergeCells('O1:O2');
	$accounts_excel->getActiveSheet(0)->mergeCells('P1:P2');
	$accounts_excel->getActiveSheet(0)->mergeCells('Q1:Q2');


	$accounts_excel->getActiveSheet(0)->setCellValue('A' . 1, "정산번호");
	$accounts_excel->getActiveSheet(0)->setCellValue('B' . 1, "정산일자");
	$accounts_excel->getActiveSheet(0)->setCellValue('C' . 1, "셀러명");
	$accounts_excel->getActiveSheet(0)->setCellValue('D' . 1, "정산방식");
	$accounts_excel->getActiveSheet(0)->setCellValue('E' . 1, "과세여부");
	$accounts_excel->getActiveSheet(0)->setCellValue('F' . 1, "상품 주문금액");
	$accounts_excel->getActiveSheet(0)->setCellValue('K' . 1, "배송비");
	$accounts_excel->getActiveSheet(0)->setCellValue('F' . 2, "정산예정금액(+)");
	$accounts_excel->getActiveSheet(0)->setCellValue('G' . 2, "할인부담금액(-)");
	$accounts_excel->getActiveSheet(0)->setCellValue('H' . 2, "수수료(-)");
	$accounts_excel->getActiveSheet(0)->setCellValue('I' . 2, "추가정산금액");
	$accounts_excel->getActiveSheet(0)->setCellValue('J' . 2, "실정산금액");
	$accounts_excel->getActiveSheet(0)->setCellValue('K' . 2, "배송비(+)");
	$accounts_excel->getActiveSheet(0)->setCellValue('L' . 2, "할인부담금액(-)");
	$accounts_excel->getActiveSheet(0)->setCellValue('M' . 2, "추가정산금액");
	$accounts_excel->getActiveSheet(0)->setCellValue('N' . 2, "실정산금액");
	$accounts_excel->getActiveSheet(0)->setCellValue('O' . 1, "실정산금액");
	$accounts_excel->getActiveSheet(0)->setCellValue('P' . 1, "정산상태");
	$accounts_excel->getActiveSheet(0)->setCellValue('Q' . 1, "정산지급방식");
	
	$col = 'A';

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
		
		if($db->dt[ac_type]==1){
			$ac_type="수수료";
		}elseif($db->dt[ac_type]==2){
			$ac_type="매입";
		}else{
			$ac_type="-";
		}

		if($db->dt[surtax_yorn]=='N'){
			$surtax_yorn="과세";
		}elseif($db->dt[surtax_yorn]=='Y'){
			$surtax_yorn="면세";
		}elseif($db->dt[surtax_yorn]=='P'){
			$surtax_yorn="영세";
		}else{
			$surtax_yorn="-";
		}

		$accounts_excel->getActiveSheet()->setCellValue('A' . ($i + 3), $db->dt[ac_ix]);
		$accounts_excel->getActiveSheet()->setCellValue('B' . ($i + 3), $db->dt[ac_date]);
		$accounts_excel->getActiveSheet()->setCellValue('C' . ($i + 3), $db->dt[com_name]);		
		$accounts_excel->getActiveSheet()->setCellValue('D' . ($i + 3), $ac_type);
		$accounts_excel->getActiveSheet()->setCellValue('E' . ($i + 3), $surtax_yorn);
		$accounts_excel->getActiveSheet()->setCellValue('F' . ($i + 3), $db->dt[p_expect_price]);
		$accounts_excel->getActiveSheet()->setCellValue('G' . ($i + 3), $db->dt[p_dc_allotment_price]);
		$accounts_excel->getActiveSheet()->setCellValue('H' . ($i + 3), $db->dt[p_fee_price]);
		$accounts_excel->getActiveSheet()->setCellValue('I' . ($i + 3), $db->dt[p_add_price]);
		$accounts_excel->getActiveSheet()->setCellValue('J' . ($i + 3), $db->dt[p_ac_price]);
		$accounts_excel->getActiveSheet()->setCellValue('K' . ($i + 3), $db->dt[d_expect_price]);
		$accounts_excel->getActiveSheet()->setCellValue('L' . ($i + 3), $db->dt[d_dc_allotment_price]);
		$accounts_excel->getActiveSheet()->setCellValue('M' . ($i + 3), $db->dt[d_add_price]);
		$accounts_excel->getActiveSheet()->setCellValue('N' . ($i + 3), $db->dt[d_ac_price]);
		$accounts_excel->getActiveSheet()->setCellValue('O' . ($i + 3), $db->dt[ac_price]);
		$accounts_excel->getActiveSheet()->setCellValue('P' . ($i + 3), getOrderStatus($db->dt[status]));
		$accounts_excel->getActiveSheet()->setCellValue('Q' . ($i + 3), getMethodStatus($db->dt[account_method]));
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

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","업체별정산현황.xls").'"');
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
		<td align='left' colspan=4>".GetTitleNavigation("정산확정내역", "셀러정산 > 정산확정내역")."</td>
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
		  <td class='search_box_title'><label for='check_search_date'>정산일자</label><input type='checkbox' name='check_search_date' id='check_search_date' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue("1",$check_search_date,"checked")."></td>
		  <td class='search_box_item' colspan=3 >
			".search_date('startDate','endDate',$startDate,$endDate)."
		  </td>
		</tr>
		<tr height=30>
			<td class='search_box_title'>정산방식  </td>
			<td class='search_box_item'>
				<input type='checkbox' name='ac_type[]' id='ac_type_1' value='1' ".CompareReturnValue("1",$ac_type,' checked')." ><label for='ac_type_1'>수수료</label>&nbsp;
				<!--<input type='checkbox' name='ac_type[]' id='ac_type_2' value='2' ".CompareReturnValue("2",$ac_type,' checked')." ><label for='ac_type_2'>매입(공급가정산)</label>-->
			</td>
			<td class='search_box_title'>과세여부  </td>
			<td class='search_box_item'>
				<input type='checkbox' name='surtax_yorn[]' id='surtax_yorn_n' value='N' ".CompareReturnValue("N",$surtax_yorn,' checked')." ><label for='surtax_yorn_n'>과세</label>&nbsp;
				<input type='checkbox' name='surtax_yorn[]' id='surtax_yorn_y' value='Y' ".CompareReturnValue("Y",$surtax_yorn,' checked')." ><label for='surtax_yorn_y'>면세</label>&nbsp;
				<!--input type='checkbox' name='surtax_yorn[]' id='surtax_yorn_p' value='P' ".CompareReturnValue("P",$surtax_yorn,' checked')." ><label for='surtax_yorn_p'>영세</label-->
			</td>
		</tr>
		<tr height=30>
			<td class='search_box_title'>지급방식  </td>
			<td class='search_box_item' ".($admininfo[admin_level] != 9 ? "colspan='3'" : "").">
				<input type='checkbox' name='account_method[]' id='account_method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue("1",$account_method,' checked')."  ><label for='account_method_".ORDER_METHOD_CASH."'>".getMethodStatus(ORDER_METHOD_CASH)."</label>&nbsp;
				<!--<input type='checkbox' name='account_method[]' id='account_method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue("2",$account_method,' checked')." ><label for='account_method_".ORDER_METHOD_SAVEPRICE."'>".getMethodStatus(ORDER_METHOD_SAVEPRICE)."</label>-->
			</td>";

			if($admininfo[admin_level] == 9){
				$Contents .= "
				<td class='search_box_title'>셀러명  </td>
				<td class='search_box_item'>".CompanyList($company_id,"","")."</td>";
			}

		$Contents .= "
		</tr>";
		
	if($admininfo[admin_level] == 9){
		$Contents .= "
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
							<option value='ac.ac_ix' ".CompareReturnValue("ac.ac_ix",$search_type).">정산번호</option>
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
		<td align='right' colspan=4>
			<a href='../seller_accounts/accounts_list.php?act=accounts_excel&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle'></a> ";
			if($admininfo[admin_level] == 9){
				$Contents .= "<input type='button' value='검색된 정산상세보기' onclick=\"PoPWindow('./accounts_detail.php?".$ac_ix_param."',1100,300,'ac_detail')\" />";
			}
		$Contents .= "
		</td>
	</tr>
</table>
</form>";


$Contents .= "
	<form name=listform method=post action='accounts.act.php' onsubmit=\"return account(this)\" target='act'>
	<input type=hidden id='ac_ix' value=''>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' >
	<tr>
		<td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b>정산 확정내역</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=right>
			<div style='width:100%;height:350px;overflow-y:scroll;overflow-x:scroll;position:relative;' id='scroll_div'>
			<table width='180%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' style='position:absolute;top:0px;margin-top:0px;' id='scroll_title'>
				<col width='30px'>
				<col width='3%'>
				<col width='4%'>
				<col width='*'>
				<col width='3%'>
				<col width='3%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='4%'>
				<col width='5%'>
				<tr height='25' >
					<td class='s_td' align='center'  rowspan='2'><input type=checkbox  name='all_fix2' onclick='fixAll2(document.listform)'></td>
					<td align='center' class='m_td' rowspan='2'><b>정산번호</b></td>
					<td align='center' class='m_td' rowspan='2'><b>정산일</b></td>
					<td align='center' class='m_td' rowspan='2'><b>셀러명</b></td>
					<td align='center' class='m_td' rowspan='2'><b>정산방식</b></td>
					<td align='center' class='m_td' rowspan='2'><b>과세여부</b></td>
					<td align='center' class='m_td' colspan='5'><b>상품 주문금액</b></td>
					<td align='center' class='m_td' colspan='4'><b>배송비</b></td>
					<td align='center' class='m_td' rowspan='2'><b>실정산합계</b></td>
					<td align='center' class='m_td' rowspan='2'><b>정산상태</b></td>
					<td align='center' class='m_td' rowspan='2'><b>정산지급방식</b></td>
					<td align='center' class='m_td' rowspan='2'><b>관리</b></td>
				</tr>
				<tr height='25' >
					<td align='center' class='m_td' ><b>정산예정금액(+)</b></td>
					<td align='center' class='m_td' ><b>할인부담금액(-)</b></td>
					<td align='center' class='m_td' ><b>수수료(-)</b></td>
					<td align='center' class='m_td' ><b>추가정산금액</b></td>
					<td align='center' class='m_td' ><b>실정산금액</b></td>
					<td align='center' class='m_td' ><b>배송비(+)</b></td>
					<td align='center' class='m_td' ><b>할인부담금액(-)</b></td>
					<td align='center' class='m_td' ><b>추가정산금액</b></td>
					<td align='center' class='m_td' ><b>실정산금액</b></td>
				</tr>
			</table>
			<table width='180%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' id='scroll_list'>
				<col width='30px'>
				<col width='3%'>
				<col width='4%'>
				<col width='*'>
				<col width='3%'>
				<col width='3%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='6%'>
				<col width='4%'>
				<col width='5%'>";

	if($db->total){
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

				$Contents .= "
				<tr onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\" height=30>
					<td class='list_box_td'  align='center'><input type=checkbox name='ac_ix[]' id='ac_ix' value='".$db->dt[ac_ix]."'></td>
					<td class='list_box_td list_bg_gray'  align='center'><b class='blue'>".$db->dt[ac_ix]."</b></td>
					<td class='list_box_td'  align='center'>".$db->dt[ac_date]."</td>
					<td class='list_box_td list_bg_gray' style='text-align:left;padding-left:5px;'>
						<input type='button' value='상세보기' onclick=\"PoPWindow('./accounts_detail.php?ac_ix=".$db->dt[ac_ix]."',1100,300,'ac_detail')\" /> ".$db->dt[com_name]."
					</td>";

					if($db->dt[ac_type]==1){
						$ac_type="수수료";
					}elseif($db->dt[ac_type]==2){
						$ac_type="매입";
					}else{
						$ac_type="-";
					}

					$Contents .= "
					<td class='list_box_td '  align='center' >".$ac_type."</td>";

					if($db->dt[surtax_yorn]=='N'){
						$surtax_yorn="과세";
					}elseif($db->dt[surtax_yorn]=='Y'){
						$surtax_yorn="면세";
					}elseif($db->dt[surtax_yorn]=='P'){
						$surtax_yorn="영세";
					}else{
						$surtax_yorn="-";
					}

					$Contents .= "
					<td class='list_box_td list_bg_gray'  align='center' >".$surtax_yorn."</td>
					<td class='list_box_td' align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_expect_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td list_bg_gray' align='center'  >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_dc_allotment_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td '  align='center'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_fee_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td list_bg_gray'  align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_add_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>
					<td class='list_box_td'  align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_ac_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>
					<td class='list_box_td list_bg_gray' align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[d_expect_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td'  align='center'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[d_dc_allotment_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td list_bg_gray'  align='center'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[d_add_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td'  align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[d_ac_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td point'  align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[ac_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>
					<td class='list_box_td' align='center'>".getOrderStatus($db->dt[status])."</td>
					<td class='list_box_td list_bg_gray' align='center'>".getMethodStatus($db->dt[account_method])."</td>
					<td class='list_box_td' align='center'>";
						if($_SESSION["admininfo"]["admin_level"] == 9){
							$Contents .="<input type='button' value='추가정산' onclick=\"PoPWindow('./accounts_add.pop.php?page_type=add&ac_ix=".$db->dt[ac_ix]."',1000,300,'ac_add')\" />";
						}else{
							$Contents .="&nbsp;";
						}
					$Contents .= "
					</td>
				</tr>";
		}
	}else{
		$Contents .= "<tr height=50><td colspan='17' align=center>정산 예정내역이 없습니다</td></tr>";
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


if($admininfo[admin_level] == 9){

	$help_title = "
		<nobr>
			<select name='update_type'>
				<!--option value='1'>검색한주문 전체에게</option-->
				<option value='2'>선택한정산 전체에게</option>
			</select>
			<input type='radio' name='update_kind' id='update_kind' value='' onclick=\"\" checked><label for='update_kind'>정산 처리</label>
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
					<input type='radio' name='act' id='account_complete' value='account_complete' onclick=\"\" checked><label for='account_complete' >송금대기</label>
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
$P->Navigation = "판매자정산관리 > 정산확정내역";
$P->title = "정산확정내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>