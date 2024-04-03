<?
include("../class/layout.class");
//include("../../include/cash_manage.lib.php");

$db = new Database;
$mdb = new Database;

$Script = "
<link type='text/css' href='../js/themes/base/ui.all.css' rel='stylesheet' />
<script type='text/javascript' src='../js/ui/ui.core.js'></script>
<script type='text/javascript' src='../js/ui/ui.datepicker.js'></script>

<script language='javascript'>


</script>";


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("미수금연동리스트", "여신관리 > 미수금연동리스트 ")."</td>
		</tr>
		<tr>
			<td>
				<form name='search' >
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  cellpadding='0' cellspacing='0'  border=0>
							<tr>
								<td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
									<table class='box_shadow' cellpadding=0 cellspacing=0 style='width:100%;' align=left>
										<tr>
											<th class='box_01'></th>
											<td class='box_02'></td>
											<th class='box_03'></th>
										</tr>
										<tr>
											<th class='box_04'></th>
											<td class='box_05' valign=top style='padding:0px;'>
												<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
												<TR>
													<TD bgColor=#ffffff style='padding:0 0 0 0;'>
													<table cellpadding=3 cellspacing=1 width='100%' class='search_table_box'>
														 <tr height=27>
															<th class='search_box_title' bgcolor='#efefef' width='150' align=center>조건검색 </th>
															<td class='search_box_item'>
															<table cellpadding=0 cellspacing=0 width=100%>
																<col width=110>
																<col width=*>
																<tr>
																	<td>
																	<select name=search_type style='width:100px;'>
																		<option value='ReceiptNo' ".CompareReturnValue("ReceiptNo",$search_type,"selected").">처리번호</option>
																		<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">업체명</option>
																	</select>
																	</td>
																	<td>
																	<input type=text name='search_text' class=textbox value='".$search_text."' style='width:15%' >
																	</td>
																</tr>
															</table>
															</td>
														</tr>
														<tr height=27>
														  <td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>연동데이터 삭제여부</b></label></td>
														  <td class='search_box_item' align=left style='padding-left:5px;'>
																<input type='radio' name='is_delete'  id='is_delete_' value='' ".ReturnStringAfterCompare($is_delete, "", " checked")."><label for='is_delete_'>전체</label>
																<input type='radio' name='is_delete'  id='is_delete_y' value='Y' ".ReturnStringAfterCompare($is_delete, "Y", " checked")."><label for='is_delete_y'>삭제</label>
																<input type='radio' name='is_delete'  id='is_delete_n' value='N' ".ReturnStringAfterCompare($is_delete, "N", " checked")."><label for='is_delete_n'>미삭제</label>
														  </td>
														</tr>
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
							<tr >
								<td colspan=3 align=center style='padding:10px 0 20px 0'>
									<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				</table>
				</form>
			</td>
		</tr>
		<tr>
			<td>";

	$max = 20;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	
	
	$where = " where 1=1 ";

	if($is_delete!=""){
		$where .= " and is_delete = '".$is_delete."' ";
	}

	if($search_text!="" && $search_type!=""){
		$where .= " and ".$search_type." LIKE '%".$search_text."%' ";
	}
	

	$sql="select *  from 
				shop_noaccept_erp as ne left join common_company_detail as ccd on (ne.CustSeq = ccd.custseq)
			$where";
	$mdb->query($sql);
	$total = $mdb->total;
	

	$mstring .= "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>
						<tr align=center bgcolor=#efefef height=30 style='font-weight:600;'>
							<td class=s_td width='15%' >처리번호</td>
							<td class=m_td width='15%' >처리일자</td>
							<td class=m_td width='*' >업체명</td>
							<td class=m_td width='20%' >금액</td>
						</tr>
						";

	if ($total == 0){
		$mstring .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=15 align=center>내역이 없습니다.</td></tr>";
	}else{
		
		$sql="select * from shop_noaccept_erp as ne left join common_company_detail as ccd on (ne.CustSeq = ccd.custseq)  $where limit $start,$max ";
		$mdb->query($sql);

		for($j=0;$j < $mdb->total;$j++){
			$mdb->fetch($j);
			
			$no = $total - ($page - 1) * $max - $j;

			$mstring .= "<tr height=30 bgcolor=#ffffff align=center >
				<td class='list_box_td list_bg_gray' ".($mdb->dt[is_delete]=="Y" ? "style='background-color:#fff7da'":"").">".$mdb->dt[ReceiptNo]."</td>
				<td class='list_box_td' ".($mdb->dt[is_delete]=="Y" ? "style='background-color:#fff7da'":"").">".substr($mdb->dt[ReceiptDate],0,4).substr($mdb->dt[ReceiptDate],4,2).substr($mdb->dt[ReceiptDate],6,2)."</td>
				<td class='list_box_td list_bg_gray' ".($mdb->dt[is_delete]=="Y" ? "style='background-color:#fff7da'":"").">".$mdb->dt[com_name]."</td>
				<td class='list_box_td ' ".($mdb->dt[is_delete]=="Y" ? "style='background-color:#fff7da'":"").">".number_format($mdb->dt[DomAmt])."</td>
			</tr>";
		}
	}

$mstring .= "
					</table>
				<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver >
					<tr height=50 bgcolor=#ffffff>
						<td align=right>
							".page_bar($total, $page, $max,"&max=$max&search_type=$search_type&search_text=$search_text&have=$have","")."
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>";



$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$mstring .= HelpBox("미수금연동리스트", $help_text);

$Contents = $mstring;

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = offline_order_menu();
$P->Navigation = "여신관리 > 미수금연동리스트";
$P->title = "미수금연동리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
