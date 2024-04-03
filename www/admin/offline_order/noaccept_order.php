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
			<td align='left' colspan=6 > ".GetTitleNavigation("미수금(외상)리스트", "여신관리 > 미수금(외상)리스트 ")."</td>
		</tr>
		<tr>
			<td>
				<form name='search' >
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  cellpadding='0' cellspacing='0'  border=0>
							<!--tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>적립금 검색하기</b></td></tr-->
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
																		<option value='ni.oid' ".CompareReturnValue("ni.oid",$search_type,"selected").">주문번호</option>
																		<option value='cd.com_name' ".CompareReturnValue("cd.com_name",$search_type,"selected").">매출처</option>
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
														  <td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>잔여미수금보유</b></label></td>
														  <td class='search_box_item' align=left style='padding-left:5px;'>
																<input type='radio' name='have'  id='have_' value='' ".ReturnStringAfterCompare($have, "", " checked")."><label for='have_'>전체</label>
																<input type='radio' name='have'  id='have_1' value='1' ".ReturnStringAfterCompare($have, "1", " checked")."><label for='have_1'>보유</label>
																<input type='radio' name='have'  id='have_2' value='2' ".ReturnStringAfterCompare($have, "2", " checked")."><label for='have_2'>미보유</label>
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
	
	$where = " where ni.status='1' ";

	if($have=="1"){
		$where .= " and cancel_update_yn in ('N') ";
	}elseif($have=="2"){
		$where .= " and cancel_update_yn in ('Y') ";
	}else{
		$where .= " and cancel_update_yn in ('N','Y') ";
	}

	if($search_text!="" && $search_type!=""){
		$where .= " and ".$search_type." LIKE '%".$search_text."%' ";
	}

/*
16 02 17
매출처 검색 오류로 인해 아래 쿼리로 변경
	$sql="select *  from 
				shop_noaccept_info ni
			$where";

*/

	$sql="select ni.company_id, ni.oid, ni.price, ni.cancel_price , cd.com_name  from 
					shop_noaccept_info ni left join common_company_detail cd on (ni.company_id=cd.company_id)
				$where";
	$mdb->query($sql);
	$total = $mdb->total;
	

	$mstring .= "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>
						<tr align=center bgcolor=#efefef height=30 style='font-weight:600;'>
							<td class=s_td width='5%' rowspan='3'>순번</td>
							<td class=m_td width='10%' rowspan='3'>주문일/주문번호</td>
							<td class=m_td width='8%' rowspan='3'>매출처</td>
							<td class=m_td width='5%' rowspan='3'>결제상태</td>
							<td class=m_td width='*' colspan='7'>매출</td>
							<td class=m_td width='8%' rowspan='3'>미수금</td>
							<td class=m_td width='8%' rowspan='3'>미수금결제</td>
							<td class=m_td width='8%' rowspan='3'>잔여미수금</td>
							<td class=e_td width='5%' rowspan='3'>관리</td>
						</tr>
						<tr align=center bgcolor=#efefef height=30 style='font-weight:600;'>
							<td class=m_td colspan='2'>전체매출액</td>
							<td class=m_td colspan='2'>입금전취소매출</td>
							<td class=m_td colspan='2'>반품매출</td>
							<td class=m_td width='5%' rowspan='2'>배송비(원)</td>
						</tr>
						<tr align=center bgcolor=#efefef height=30 style='font-weight:600;'>
							<td class=m_td width='6%' >수량(개)</td>
							<td class=m_td width='6%' >주문액(원)</td>
							<td class=m_td width='6%' >수량(개)</td>
							<td class=m_td width='6%' >주문액(원)</td>
							<td class=m_td width='6%' >수량(개)</td>
							<td class=m_td width='6%' >주문액(원)</td>
						</tr>
						";

	if ($total == 0){
		$mstring .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=15 align=center>내역이 없습니다.</td></tr>";
	}else{
		
		$sql="select * from 
			(
				select ni.company_id, ni.oid, ni.price, ni.cancel_price , cd.com_name  from 
					shop_noaccept_info ni left join common_company_detail cd on (ni.company_id=cd.company_id)
				$where order by ni.regdate desc limit $start , $max
			) cm
			left join 
			(
				select o.oid,o.status,
				sum(od.pt_dcprice) as t_p,
				sum(od.pcnt) as t_c,
				sum(case when od.status='IB' then od.pt_dcprice else '0' end) as b_p,
				sum(case when od.status='IB' then od.pcnt else '0' end) as b_c,
				sum(case when od.status='RC' then od.pt_dcprice else '0' end) as r_p,
				sum(case when od.status='RC' then od.pcnt else '0' end) as r_c,
				op.expect_delivery_price
				from 
					shop_order o,
					shop_order_detail od,
					shop_order_price op
				where o.status = 'DP' and o.oid=od.oid and od.oid=op.oid and op.payment_status='G'
				group by o.oid
			) o
			on (cm.oid=o.oid)
		";
		// o.status = 'DP'  16 02 17 속도를 위해 추가
		$mdb->query($sql);

		for($j=0;$j < $mdb->total;$j++){
			$mdb->fetch($j);
			
			$no = $total - ($page - 1) * $max - $j;

			$mstring .= "<tr height=30 bgcolor=#ffffff align=center>
				<td class='list_box_td list_bg_gray' >".$no."</td>
				<td class='list_box_td'>".$mdb->dt[date]."<br/>".$mdb->dt[oid]."</td>
				<td class='list_box_td list_bg_gray' >".$mdb->dt[com_name]."</td>
				<td class='list_box_td '>".getOrderStatus($mdb->dt[status])."</td>
				<td class='list_box_td list_bg_gray' >".$mdb->dt[t_c]."개</td>
				<td class='list_box_td '>".number_format($mdb->dt[t_p])."원</td>
				<td class='list_box_td list_bg_gray' >".$mdb->dt[b_c]."개</td>
				<td class='list_box_td '>".number_format($mdb->dt[b_p])."원</td>
				<td class='list_box_td list_bg_gray' >".$mdb->dt[r_c]."개</td>
				<td class='list_box_td '>".number_format($mdb->dt[r_p])."원</td>
				<td class='list_box_td list_bg_gray' >".number_format($mdb->dt[expect_delivery_price])." 원</td>
				<td class='list_box_td ' >".number_format($mdb->dt[price])." 원</td>
				<td class='list_box_td list_bg_gray' >".number_format($mdb->dt[cancel_price])." 원</td>
				<td class='list_box_td point' >".number_format($mdb->dt[price]-$mdb->dt[cancel_price])." 원</td>
				<td class='list_box_td'>
					<input type='button' value='미수금관리' onclick=\"ShowModalWindow('./noaccept.pop.php?company_id=".$mdb->dt[company_id]."&oid=".$mdb->dt[oid]."',1200,500,'noaccept_company');\" />
				</td>
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

$mstring .= HelpBox("미수금(외상)리스트", $help_text);

$Contents = $mstring;

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = offline_order_menu();
$P->Navigation = "여신관리 > 미수금(외상)리스트";
$P->title = "미수금(외상)리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
