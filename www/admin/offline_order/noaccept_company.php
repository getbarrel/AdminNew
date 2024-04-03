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

if($have ==""){
	$have="1";
}


$mstring ="
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("잔여여신리스트", "여신관리 > 잔여여신리스트 ")."</td>
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
															<th class='search_box_title' bgcolor='#efefef' width='150' align=center>매출처명 </th>
															<td class='search_box_item'>
															<table cellpadding=0 cellspacing=0 width=100%>
																<col width=110>
																<col width=*>
																<tr>
																	<!--td>
																	<select name=search_type style='width:100px;'>
																		<option value='ci_name' ".CompareReturnValue("ci_name",$search_type,"selected").">매출처</option>
																		<option value='com_name' ".CompareReturnValue("com_name",$search_type,"selected").">납품처명</option>
																	</select>
																	</td-->
																	<td>
																	<input type=text name='search_text' class=textbox value='".$search_text."' style='width:15%' >
																	</td>
																</tr>
															</table>
															</td>
														</tr>
														<tr height=27>
														  <td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>미수금보유</b></label></td>
														  <td class='search_box_item' align=left style='padding-left:5px;'>
																<input type='radio' name='have'  id='have_1' value='1' ".ReturnStringAfterCompare($have, "1", " checked")."><label for='have_1'>보유</label>
																<input type='radio' name='have'  id='have_2' value='2' ".ReturnStringAfterCompare($have, "2", " checked")."><label for='have_2'>미보유</label>
																<input type='radio' name='have'  id='have_3' value='3' ".ReturnStringAfterCompare($have, "3", " checked")."><label for='have_3'>전체</label>
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

	if($have=="1"){
		$where .= " and ccd.noaccept_price > 0 ";
	}elseif($have=="2"){
		$where .= " and ccd.noaccept_price = 0 ";
	}

	if($search_text){
		$where .= " and ccd.com_name LIKE '%".$search_text."%' ";
	}


	$sql="select ccd.company_id
		from common_company_detail ccd
		where ccd.com_type !='A' and (ccd.seller_type like '%1%' or ccd.seller_type like '%3%')  $where ";

	$mdb->query($sql);
	$total = $mdb->total;
	

	$mstring .= "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>
						<tr align=center bgcolor=#efefef height=30 style='font-weight:600;'>
							<td class=s_td width='10%'>순번</td>
							<td class=m_td width='*'>매출처코드</td>
							<td class=m_td width='15%'>매출처</td>
							<td class=m_td width='15%'>여신한도</td>
							<td class=m_td width='15%'>잔여여신한도</td>
							<td class=m_td width='15%'>총 미수금</td>
							<td class=e_td width='15%'>관리</td>
						</tr>";

	if ($total == 0){
		$mstring .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=7 align=center>검색된 매출처가 없습니다.</td></tr>";
	}else{
		
		$sql="select ccd.company_id,ccd.com_name,ccd.loan_price, ccd.noaccept_price
			from common_company_detail ccd
			where ccd.com_type !='A' and (ccd.seller_type like '%1%' or ccd.seller_type like '%3%') $where
			limit $start , $max ";

		$mdb->query($sql);

		for($j=0;$j < $mdb->total;$j++){
			$mdb->fetch($j);
			
			$no = $total - ($page - 1) * $max - $j;

			$mstring .= "<tr height=30 bgcolor=#ffffff align=center>
				<td class='list_box_td list_bg_gray' >".$no."</td>
				<td class='list_box_td'>".$mdb->dt[company_id]."</td>
				<td class='list_box_td ' >".$mdb->dt[com_name]."</td>
				<td class='list_box_td'>".number_format($mdb->dt[loan_price])." 원</td>
				<td class='list_box_td point' >".number_format($mdb->dt[loan_price]-$mdb->dt[noaccept_price])." 원</td>
				<td class='list_box_td' >".number_format($mdb->dt[noaccept_price])." 원</td>
				<td class='list_box_td'>
					<img src='../images/".$admininfo["language"]."/btn_noaccept_modify.gif' border=0 align=absmiddle onclick=\"window.open('/admin/basic/seller.add.php?company_id=".$mdb->dt[company_id]."&info_type=basic');\" style='cursor:pointer' /> 
					<img src='../images/".$admininfo["language"]."/btn_noaccept_manage.gif' border=0 align=absmiddle onclick=\"ShowModalWindow('./noaccept.pop.php?company_id=".$mdb->dt[company_id]."',1200,500,'noaccept_company');\" style='cursor:pointer' /> 
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

$mstring .= HelpBox("잔여여신리스트", $help_text);

$Contents = $mstring;

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = offline_order_menu();
$P->Navigation = "여신관리 > 잔여여신리스트";
$P->title = "잔여여신리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();


?>
