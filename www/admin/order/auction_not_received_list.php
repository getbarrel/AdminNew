<?
include_once("../class/layout.class");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");

if ($startDate == ""){
	//$before30day = mktime(0, 0, 0, date("m")  , date("d")-30, date("Y"));
	$startDate = date("Y-m-d");
	$endDate = date("Y-m-d");
}

$OAL = new OpenAPI('auction');
$result = $OAL->lib->getCancelNotReceivedList($startDate,$endDate);

$Contents = "
<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("옥션미수령신고 내역조회", "제휴 > 옥션미수령신고 내역조회")."</td>
	</tr>
</table>
<form name='search_frm' method='get' action=''>
<input type='hidden' name='mode' value='search' />
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>주문정보 검색하기</b></td>
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
									<TABLE cellSpacing=0 cellPadding=3 style='width:100%;' align=center border=0 class='search_table_box'>
											<col width=15%>
											<col width=35%>
											<col width=15%>
											<col width=35%>
											<tr height=33>
												<th class='search_box_title'>
													미수령신고 일자
												</th>
												<td class='search_box_item' colspan=3>
													".search_date('startDate','endDate',$startDate,$endDate)."
												</td>
											</tr>
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

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
 <tr height=30>";



 $Contents .= "<td colspan=3 align=left></td>
			<td colspan=9 align=right >
				<!--a href='../order/cafe24_order_list.php?act=excel&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a-->
			</td>
		</tr>
  </table>";


$Contents .= "
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='25' >
		<td width='10%' align='center'  class='m_td' nowrap><b>미수령신고일자</b></td>
		<td width='*' align='center' class='m_td'><b>주문번호</b></td>
		<td width='18%' align='center'  class='m_td' nowrap><b>미수령 신고사유</b></td>
		<td width='22%' align='center' class='m_td' nowrap><b>미수령 신고 철회</b></td>
		<td width='20%' align='center' class='m_td' nowrap><b>최초 배송정보</b></td>
		<td width='18%' align='center' class='m_td' nowrap><b>재발송 배송정보</b></td>
	</tr>";

if(count($result)>0){
	for ($i = 0; $i < count($result); $i++)
	{

		$Contents .= "<tr height=28 >";
			$Contents .= "<td  class='list_box_td ' style='line-height:140%' align=center>".$result[$i]['date']."</td>";
			$Contents .= "<td class='list_box_td point' style='line-height:140%' align=center><spanstyle='color:#007DB7;font-weight:bold;'>".$result[$i]['order_no']."</span></td>";
			$Contents .= "<td style='line-height:140%' align=center class='list_box_td'>[".$result[$i]["reason"]."] ".$result[$i]["detail_reason"]."</td>";
			$Contents .= "<td class='list_box_td' style='line-height:140%;padding:3px;' align='left' nowrap>
				요청 일자 : ".$result[$i]["draw_date"]."<br/>
				이의 제기 여부 :  ".$result[$i]["draw_exception_yn"]."<br/>
				이의 제기 일자 :  ".$result[$i]["draw_exception_date"]."
			</td>";
			$Contents .= "<td class='list_box_td' style='line-height:140%;padding:3px;' align='left' nowrap>
				[".$result[$i]['delivery_name']."] ".$result[$i]['invoice_no']." <br/>
				배송중 일자 : ".$result[$i]['delivery_send_date']." <br/>
				배송완료일자 : ".$result[$i]['delivery_finish_date']."
			</td>";
			$Contents .= "<td class='list_box_td' style='line-height:140%;padding:3px;' align='left' nowrap>
				[".$result[$i]['re_delivery_name']."] ".$result[$i]['re_invoice_no']." <br/>
				판매자 매세지 : ".$result[$i]['seller_message']."
			</td>";
		$Contents .= "</tr>";
	}

}else{
	$Contents .= "<tr height=50><td colspan='6' align=center>조회된 결과가 없습니다.</td></tr>
			";
}
$Contents .= "
  </table>";


$P = new LayOut();

$P->strLeftMenu = order_menu();
$P->OnloadFunction = "";
$P->addScript = "";
$P->Navigation = "제휴 > 옥션미수령신고 내역조회";
$P->title = "옥션미수령신고 내역조회";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>