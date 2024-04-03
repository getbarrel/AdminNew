<?
include_once("../class/layout.class");
//include_once("../lib/barobill.lib.php");

if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));

	$startDate = date("Y-m-d", $before10day);
	$endDate = date("Y-m-d");
}

$db = new Database;

if($mode!="search"){
	$orderdate=0;
}

//directly/inversely 정/역
if($pre_type=="inversely_apply"){
	$title_str  = "계산서 신청";
}elseif($pre_type=="inversely_complete"){
	$title_str  = "계산서 발급";
}


$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

$Contents = "

<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation($title_str, "판매자정산관리 > $title_str ")."</td>
	</tr>
</table>
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
		<form name='search_frm' method='get' action=''>
		<input type='hidden' name='pre_type' value='".$pre_type."' />
		<input type='hidden' name='mode' value='search' />
			<table width=100%  border=0>
				<tr height=25>";
					if($pre_type == 'inversely_apply'){

						$Contents .= "<td align='left'  style='border-bottom:2px solid #efefef'>
													<img src='../images/dot_org.gif' align=absmiddle /> <b class=blk>세금계산서 검색하기</b>
												</td>
												<td align='right' style='padding-bottom:5px;'>";

													if($_SESSION["admininfo"]["admin_level"] < 9){
														try {
															
															$sql = "select com_number FROM common_company_detail where company_id='".$_SESSION["admininfo"]["company_id"]."' ";
															$db->query($sql);
															$db->fetch();

															$com_number = str_replace("-","",$db->dt[com_number]);

															$Contents .= GetCertificateExpireDate($com_number)." ";

															include($_SERVER["DOCUMENT_ROOT"]."/admin/tax/popbill/common.php");
															$popbillurl = $TaxinvoiceService->GetPopbillURL($com_number,$_SESSION["admininfo"]["charger_id"],'CERT');

															if($result){
																$Contents .= "
																<a href=\"javascript:PopSWindow('".$result."',820,560,'popbill_cert');\" >
																	<img src='../images/".$admininfo["language"]."/btn_electric_sign.gif' align=absmiddle />
																</a>";
															}else{
																$Contents .= "
																<a href=\"javascript:alert('POPBILL 에 등록되지 않은 사용자입니다.');\" >
																	<img src='../images/".$admininfo["language"]."/btn_electric_sign.gif' align=absmiddle />
																</a>";
															}

														}catch(PopbillException $pe) {
															$Contents .= $pe->getMessage();
														}
													}

													$Contents .= "
													<input type='button' value='팝빌 가입하기' onclick=\"getPopbillJoinMember();\" />
												</td>";
					}else{
						$Contents .= "<td align='left' colspan='2' style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle /> <b class=blk>세금계산서 검색하기</b></td>";
					}
				$Contents .= "
				</tr>
				<tr>
					<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
						<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05'>
									<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0 >
									<TR>
										<TD bgColor=#ffffff style='padding:0 0 3px 0;'>
										<table cellpadding=0 cellspacing=0 width='100%' border='0' class='search_table_box'>
										<col width=15%>
										<col width=35%>
										<col width=15%>
										<col width=35%>";

										$Contents .= "
											<tr height=33>
												<th class='search_box_title'>
												송금일자
												<input type='checkbox' name='orderdate' id='visitdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked')."></th>
												<td class='search_box_item'  colspan='3'>
													".search_date('startDate','endDate',$startDate,$endDate)."
												</td>
											</tr>
											
											<tr>
												<th class='search_box_title'>
													발급상태
												</th>
												<td class='search_box_item' colspan='3'>
													<input type='radio' name='bill_status' value='' id='bill_status_' ".($bill_status == "" ? "checked":"")."><label for='bill_status_'>전체</label> 
													<input type='radio' name='bill_status' value='0' id='bill_status_0' ".($bill_status == "0" ? "checked":"")."><label for='bill_status_0'>미발행</label> 
													<input type='radio' name='bill_status' value='1' id='bill_status_1' ".($bill_status == "1" ? "checked":"")."><label for='bill_status_1'>발행</label>
												</td>
											</tr>
											";
										if($_SESSION["admininfo"]["admin_level"]==9){
											$Contents .= "
											<tr height=30>
												<td class='search_box_title'>셀러명 </td>
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
															<select name='search_type' id='search_type'  style=\"font-size:12px;\">
																<option value='c.com_name' ".CompareReturnValue("ccd.com_name",$search_type).">셀러명</option>
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
										</TD>
									</TR>
									</TABLE>
								</td>
								<th class='box_06'></th>
							</tr>
							<tr>
								<th class='box_07'></th>
								<td class='box_08'></td>
								<th class='box_09'></th>
							</tr>
							</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr>
</table>
</form>

<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
 <tr>";


$where = "WHERE ar.status in ('".ORDER_STATUS_ACCOUNT_COMPLETE."','".ORDER_STATUS_ACCOUNT_PAYMENT."') and (p_tax_free_price != 0 OR d_tax_free_price != 0) ";

if($pre_type=="inversely_apply"){//계산서 신청
	$where .= " and ar.bill_ix='0' ";
}elseif($pre_type=="inversely_complete"){//계산서 발급
	$where .= " and ar.bill_ix!='0' ";
}else{
	echo "잘못된 접근입니다.";
	exit;
}

if($_SESSION["admininfo"]["admin_level"] < 9){
	$where .= " and ar.company_id='".$_SESSION["admininfo"]["company_id"]."' ";
}

if($orderdate){
	$where .= "and date_format(ar.ap_date,'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
}

if($bill_status!=""){
	$where .= " and ar.bill_status='".$bill_status."' ";
}

if($company_id != "") $where .= " and ar.company_id='$company_id' ";

if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
	//다중검색 시작 2014-04-10 이학봉
	if($search_text != ""){
		if(strpos($search_text,",") !== false){
			$search_array = explode(",",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			$count_where .= "and ( ";
			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
			$count_where .= ")";
		}else if(strpos($search_text,"\n") !== false){//\n
			$search_array = explode("\n",$search_text);
			$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
			$where .= "and ( ";
			$count_where .= "and ( ";

			for($i=0;$i<count($search_array);$i++){
				$search_array[$i] = trim($search_array[$i]);
				if($search_array[$i]){
					if($i == count($search_array) - 1){
						$where .= $search_type." = '".trim($search_array[$i])."'";
						$count_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$where .= $search_type." = '".trim($search_array[$i])."' or ";
						$count_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
			}
			$where .= ")";
			$count_where .= ")";
		}else{
			$where .= " and ".$search_type." = '".trim($search_text)."'";
			$count_where .= " and ".$search_type." = '".trim($search_text)."'";
		}
	}

}else{	//검색어 단일검색
	if($search_text != ""){
		if(substr_count($search_text,",")){
			$where .= " and ".$search_type." in ('".str_replace(",","','",str_replace(" ","",$search_text))."') ";
		}else{
			$where .= " and ".$search_type." LIKE '%".trim($search_text)."%' ";
		}
	}
}

$sql = "select * FROM shop_accounts_remittance ar left join common_company_detail c on (ar.company_id=c.company_id) $where ";
$db->query($sql);
$db->fetch();
$total = $db->total;

$sql = "select 
			ar.*,c.com_name,c.com_number
		FROM shop_accounts_remittance ar left join common_company_detail c on (ar.company_id=c.company_id) $where
		ORDER BY ar.ap_date DESC LIMIT $start, $max";
$db->query($sql);

$Contents .= "<td colspan=3 align=left><b class=blk>전체 건수 : $total 건</b></td>
	<td colspan=10 align=right>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	//$Contents .= "<a href='orders_excel2003.php?".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></span>";
}else{
	//$Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}

$Contents .= "
	</td>
  </tr>
  </table>";

	$Contents .= "
	<form name=listform method=post action='bill.act.php' onsubmit='return CheckStatusUpdate(this)' target='act'>
	<input type=hidden id='ar_ix' value='' >
	<input type=hidden name = 'publish_type' id='publish_type' value='2'>
	  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='25' >
			<td width='3%' align='center' class='s_td' rowspan='2'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
			<td width='8%' align='center' class='m_td' rowspan='2'><b>송금완료일자</b></td>
			<td width='10%' align='center' class='m_td' rowspan='2' ><b>셀러명</b></td>
			<td width='8%' align='center' class='m_td' rowspan='2' ><b>사업자번호</b></td>
			<td width='5%' align='center' class='m_td' rowspan='2' ><b>과세여부</b></td>
			<td width='18%' align='center' class='m_td' colspan='3'><b>상품대금</b></td>
			<td width='18%' align='center' class='m_td' colspan='3'><b>배송비</b></td>
			<td width='7%' align='center' class='m_td' rowspan='2' ><b>총합계</b></td>";
			if($pre_type!="inversely_apply"){
				$Contents .= "
				<td width='5%' align='center' class='m_td' rowspan='2' ><b>발급상태</b></td>
				<td width='8%' align='center' class='m_td'  rowspan='2'><b>발급번호</b></td>
				<td width='5%' align='center' class='m_td' rowspan='2' ><b>발급일</b></td>";
			}
			$Contents .= "
			<td width='5%' align='center' class='e_td'  rowspan='2'><b>관리</b></td>
		</tr>
		<tr>
			<td width='6%' align='center' class='m_td' ><b>공급가</b></td>
			<td width='6%' align='center' class='m_td' ><b>세액</b></td>
			<td width='6%' align='center' class='m_td' ><b>합계</b></td>
			<td width='6%' align='center' class='m_td' ><b>공급가</b></td>
			<td width='6%' align='center' class='m_td' ><b>세액</b></td>
			<td width='6%' align='center' class='m_td' ><b>합계</b></td>
		</tr>";
		
	if($db->total){
		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);

			if($db->dt[bill_status]=="0"){
				$bill_status="미발행";
			}elseif($db->dt[bill_status]=="1"){
				$bill_status="발행";
			}

			$Contents .= "
			<tr height=28 >
				<td class='list_box_td' align='center'><input type=checkbox name='ar_ix[]' id='ar_ix' value='".$db->dt[ar_ix]."' ></td>
				<td class='list_box_td' style='line-height:140%' align=center>".substr($db->dt[ap_date],0,10)."</td>
				<td class='list_box_td point' style='line-height:140%;' align=center>".$db->dt[com_name]."</td>
				<td class='list_box_td' style='line-height:140%' align=center >".$db->dt[com_number]."</td>
				<td class='list_box_td' align='center' nowrap>면세</b></td>
				<td class='list_box_td' align='center' nowrap>".number_format($db->dt[p_tax_free_price])."</td>
				<td class='list_box_td' align='center' nowrap>0</td>
				<td class='list_box_td' align='center' nowrap>".number_format($db->dt[p_tax_free_price])."</td>
				<td class='list_box_td' align='center' nowrap>".number_format($db->dt[d_tax_free_price])."</td>
				<td class='list_box_td' align='center' nowrap>0</td>
				<td class='list_box_td' align='center' nowrap>".number_format($db->dt[d_tax_free_price])."</td>
				<td class='list_box_td' align='center' nowrap>".number_format($db->dt[p_tax_free_price]+$db->dt[d_tax_free_price])."</td>";
				if($pre_type!="inversely_apply"){
					$Contents .= "
					<td class='list_box_td' align='center' nowrap>".$bill_status."</td>
					<td class='list_box_td' align='center' nowrap>".$db->dt[bill_no]."</td>
					<td class='list_box_td' align='center' nowrap>".substr($db->dt[bill_date],0,10)."</td>
					";
				}

				$Contents .= "
				<td class='list_box_td' align='center' nowrap>
					<input type='button' value='정산내역' onclick=\"PoPWindow('./accounts_detail.php?ar_ix=".$db->dt[ar_ix]."',1100,300,'ac_detail');\" /><br/>";

					if($pre_type=="inversely_complete"){
						if($db->dt[bill_status]=='0'){
							$Contents .= "<input type='button' value='발행' onclick=\"bill_issue('".str_replace("-","",$db->dt[com_number])."','".$db->dt[bill_ix]."','".$db->dt[ar_ix]."');\" /> ";
						}else{
							//$Contents .= GetTaxInvoicePopUpURL($_SESSION[admininfo][mall_ename].$db->dt[idx]);
						}
					}

					if($db->dt[bill_ix]!='0'){
						$Contents .="<br><input type='button' value='상세보기' onclick=\"PopSWindow('../tax/sales_view.php?idx=".$db->dt[bill_ix]."&mmode=pop',1300,660,'sales_view');\" />";
					}
				$Contents .="
				</td>
			</tr>";
		}

	}else{
		$Contents .= "<tr height=50><td colspan='".($pre_type=="inversely_apply" ? "13" : "16" )."' align=center>조회된 결과가 없습니다.</td></tr>";
	}
	$Contents .= "
	  </table>";

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents = str_replace("{total_sum}",$total_sum,$Contents) ;

$Contents .= "
	</tabel>
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
  <tr height=40>
    <td colspan='13' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string."#list_top","")."&nbsp;</td>
  </tr>
</table>";

if($pre_type=="inversely_apply" && $_SESSION["admininfo"]["admin_level"]==9){
$help_title = "
	<nobr>
		<select name='update_type'>
			<!--option value='1'>검색한주문 전체에게</option-->
			<option value='2'>선택한주문 전체에게</option>
		</select>
		<input type='radio' name='update_kind' id='update_kind' value='' onclick=\"\" checked><label for='update_kind'>계산서 발급</label>
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
			<td class='input_box_item'>";
			if($pre_type=="inversely_apply"){
				$help_text .= "<input type='radio' name='act' id='inversely_bill' value='inversely_bill' onclick=\"\" checked><label for='inversely_bill' >계산서 발급</label>";
			}
		$help_text .= "
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

	$Contents .= HelpBox($help_title, $help_text,300);
}

$Contents .= "
</form>
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>
";

$Script="
<script type='text/javascript'>
<!--

	function bill_issue(com_num,bill_ix,ar_ix){
		if(confirm('해당 계산서를 발행 하시겠습니까?')){
			window.frames['act'].location.href='./bill.act.php?act=bill_issue&com_num='+com_num+'&bill_ix='+bill_ix+'&ar_ix='+ar_ix;
		}
	}

	function again_bill(idx){
		if(confirm('해당 계산서를 재발행 하시겠습니까?')){
			window.frames['act'].location.href='./bill.act.php?act=again_bill&idx='+idx;
		}
	}

	function getPopbillJoinMember(){
		if(confirm('팝빌에 가입 하시겠습니까?')){
			window.frames['act'].location.href='./taxbill.act.php?act=getPopbillJoinMember';
		}
	}

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


$P = new LayOut();
$P->strLeftMenu = seller_accounts_menu();
$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";//MenuHidden(false);
$P->addScript = "<script language='javascript' src='./bill.js'></script>\n".$Script;
$P->Navigation = "판매자정산관리 > $title_str";
$P->title = $title_str;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>