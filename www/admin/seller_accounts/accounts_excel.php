<?
include("../class/layout.class");
include ("./accounts.lib.php");

$db = new Database;
$page_title = "정산자료 엑셀 다운로드";
$page_navigation = "판매자정산관리 > 정산통계 > ".$page_title;



if(date("d") < "16"){
	$startDate = date("Y-m-01",strtotime('-1 month'));
	$endDate = date("Y-m-t",strtotime('-1 month'));
}else{
	$startDate = date("Y-m-01");
	$endDate = date("Y-m-15");
}

$Contents .="
			<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-top:10px;'>
			<tr>
				<td align='left' colspan=6 >".GetTitleNavigation($page_title, $page_navigation)."</td>
			</tr>
			<tr>
				<td>
				<form name='accounts_excel' method='post' action='accounts_excel2003.php' target='act'>
				<input type='hidden' name='mode' value='search' />
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  border=0 cellpadding='0' cellspacing='0'>
							<tr>
								<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
										<TABLE cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
										<TR>
											<TD bgColor=#ffffff style='padding:0 0 0 0;'>
											<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
												 <tr height=27>
													<td class='search_box_title'>엑셀양식</td>
													<td class='search_box_item'>
														<select style='width:10%;border:1;' name='oet_ix' id='oet_ix' title='엑셀양식'>
															<option value=''>선택해주세요</option>
															<option value='1'>주문건별</option>
															<option value='2'>주문별</option>
															<option value='3'>MD</option>
														</select>
													</td>
												</tr>
											 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>기간</th>
													<td class='search_box_item'>
														".search_date('startDate','endDate',$startDate,$endDate)."
													</td>
												 </tr>
											</table>
											</TD>
										</TR>
										</TABLE>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr >
					<td colspan=3 align=center style='padding:30px 0 20px 0'>
						<img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0 align='absmiddle' style='cursor:pointer' onclick=\"if(jQuery('#oet_ix').val().length > 0){location.href='./accounts_excel2003.php?oet_ix='+jQuery('#oet_ix').val()+'&startDate='+jQuery('#startDate').val()+'&endDate='+jQuery('#endDate').val()}else{alert('엑셀양식을선택해주세요.');}\" >
					</td>
				</tr>
				</table>
				</form>
				</td>
			</tr>
			</table>";



$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = seller_accounts_menu();
$P->Navigation = $page_navigation;
$P->title = $page_title;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>