<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/class/admin.page.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/product/category.lib.php");
include("../class/layout.class");
include("inventory.lib.php");
//auth(8);
if ($vToYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-10, date("Y"));
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", time()-84600*(date("d")-1));
	$eDate = date("Y/m/d", time());

	$startDate = date("Ymd", time()-84600*(date("d")-1));
	$endDate = date("Ymd", time());
}else{
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());

	if($view_type == "summary"){
		$sDate = date("Y/m/d", time()-84600*(date("d")-1));
		$eDate = date("Y/m/d", time());
		$startDate = date("Ymd", time()-84600*(date("d")-1));
		$endDate = date("Ymd", time());
	}else{
		$sDate = $vFromYY."/".$vFromMM."/".$vFromDD;
		$eDate = $vToYY."/".$vToMM."/".$vToDD;
		$startDate = $vFromYY.$vFromMM.$vFromDD;
		$endDate = $vToYY.$vToMM.$vToDD;
	}
}

if($max == ""){
	$max = 10; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;
$db2 = new Database;
$cdb = new Database;

$where = "where  psi_ix  is not null ";

if($search_text != ""){
	$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
}
if ($vFromYY != "")	$where .= " and date_format(ips.regdate, '%Y%m%d') between $startDate and $endDate ";

if($ci_ix != ""){
	$where .= "and ips.ci_ix = '".$ci_ix."' ";
}

if($pi_ix != ""){
	$where .= "and ips.pi_ix = '".$pi_ix."' ";
}
/*	
	$sql = "select count(*) as total
						from inventory_customer_info ici ,
						common_member_detail cmd ,
						inventory_output_history_detail iohd  
						left join inventory_place_info ipi on ipi.pi_ix = iohd.pi_ix						
						$where  ";
*/
/*
$sql = "select count(*) as total
				from inventory_product_stockinfo ips  
				left join inventory_place_info ipi on ipi.pi_ix = ips.pi_ix				
				left join inventory_input_history_detail ihd on ihd.pi_ix = ips.pi_ix				
				left join inventory_customer_info ici on ici.ci_ix = ips.ci_ix and ici.customer_type = 'E'
				$where  $orderbyString 
				";//LIMIT $start, $max
*/

$sql = "select count(*) as total
				FROM `inventory_product_stockinfo` ps
				left join inventory_product_stockinfo_bydate psb on ps.pid = psb.pid and ps.opnd_ix = psb.opnd_ix and ps.pi_ix = psb.pi_ix
				, (select pid,  sum(input_cnt) as input_cnt from inventory_input_history_detail where date_format(regdate,'%Y%m%d') between $startDate and $endDate group by pid )  ihd 
				, (select pid,  sum(delivery_cnt) as delivery_cnt from inventory_output_history_detail where date_format(regdate,'%Y%m%d') between $startDate and $endDate group by pid) iohd 
				WHERE ps.pid=ihd.pid and ps.pid=iohd.pid
				group by ps.pid
				";//LIMIT $start, $max

//echo nl2br($sql);

	$db2->query($sql);
	$db2->fetch();
	$total = $db2->dt[total];

//echo $total;

	$page_title = "품목별이익현황";



if($mode == "search"){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&mode=$mode&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&vFromYY=$vFromYY&vFromMM=$vFromMM&vFromDD=$vFromDD&vToYY=$vToYY&vToMM=$vToMM&vToDD=$vToDD","");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype","");
	//echo $total.":::".$page."::::".$max."<br>";
}

$Contents =	"<table cellpadding=0 cellspacing=0 width='100%'>
			 <tr>
			    <td align='left' colspan=4 style='padding-bottom:10px;'> ".GetTitleNavigation("$page_title", "재고관리 > $page_title")."</td>
			</tr>

			 <form name='search_frm' method='get' action='".$HTTP_URL."' onsubmit='return CheckFormValue(this);' ><!--target='act'><input type='hidden' name='view' value='innerview'-->
			 <input type='hidden' name='mode' value='search'>
			 <input type='hidden' name='cid' value='$cid'>
			 <input type='hidden' name='depth' value='$depth'>
				<td colspan=2>
					<table class='box_shadow' style='width:100%;height:20px' cellpadding='0' cellspacing='0'><!---mbox04-->
						<tr>
							<th class='box_01'></th>
							<td class='box_02'></td>
							<th class='box_03'></th>
						</tr>
						<tr>
							<th class='box_04'></th>
							<td class='box_05 align=center' style='padding:10'>
								<table cellpadding=0 cellspacing=0 border=0 width=100% class='input_table_box' >";


$Contents .=	"				<col width=20%>
									<col width=30%>
									<col width=20%>
									<col width=30%>
									<tr>
										<td class='input_box_title'> <label for='regdate'>출고일자 </label> </td>
										<td class='input_box_item' colspan=3>
											<table border=0 cellpadding=0 cellspacing=0>
												<TD nowrap>
												<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromYY></SELECT> 년
												<SELECT onchange=javascript:onChangeDate(this.form.vFromYY,this.form.vFromMM,this.form.vFromDD) name=vFromMM></SELECT> 월 <SELECT name=vFromDD></SELECT> 일 </TD>
												<TD width=10 align=center> ~ </TD>
												<TD nowrap>
												<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToYY></SELECT> 년
												<SELECT onchange=javascript:onChangeDate(this.form.vToYY,this.form.vToMM,this.form.vToDD) name=vToMM></SELECT> 월
												<SELECT name=vToDD></SELECT> 일</TD>
											</table>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' > 출고처</td>
										<td class='input_box_item' colspan=3>
										".SelectBoxSellCustomer("ci_ix", $ci_ix, "false")."

										</td>
										
									</tr>
									<tr>
										<td class='input_box_title'> 보관장소</td>
										<td class='input_box_item'>
										 ".makeSelectBoxTargetPlace($pi_ix, 'pi_ix',"","false")."
										</td>
										<td class='input_box_title'> </td>
										<td class='input_box_item'>
										
										</td>
										
									</tr>
									<tr>
										<td class='input_box_title'> 검색어  </td>
										<td class='input_box_item'  colspan=3 align=left  style='padding-right:5px;padding-top:2px;'>
											<table cellpadding=0 cellspacing=0>
											<tr>
												<td><select name='search_type' id='search_type'  style=\"font-size:12px;height:22px;\"><option value='pname'>상품명</option><option value='pcode'>상품코드</option></select></td>
												<td style='padding-left:3px;'>
												<INPUT id=search_texts onkeyup='findNames();' class='textbox' value='' onclick='findNames();'  clickbool='false' style=' FONT-SIZE: 12px; WIDTH: 260px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text autocomplete='off' validation=false  title='검색어'><!--onFocusOut='clearNames()'--><br>

												<DIV id=popup style='display: none; width: 268px; POSITION: absolute; height: 150px; backgorund-color: #fffafa' ><!--onmouseover=focusOutBool=false;  onmouseout=focusOutBool=true;-->
												<table cellSpacing=0 cellPadding=0 border=0 width=100% height=100% bgColor=#efefef style='width: 268px;'>
													<tr height=24>
														<td width=100%  style='padding:0 0 0 5px'>
														<table width=100% cellpadding=0 cellspacing=0 border=0>
														<tr>
															<td class='p11 ls1'>검색어 자동완성</td>
															<td class='p11 ls1' onclick='focusOutBool=false;clearNames()' style='cursor:hand;padding:0 10px 0 0' align=right>닫기</td>
														</tr>
														</table>
														</td>
													</tr>
													<tr height=100% >
														<td valign=top bgColor=#efefef style='' colspan=2>
															<table cellpadding=0 cellspacing=0 width=100% height=100% style='margin:0 0px 5px 0px;height: 120px;' bgcolor=#ffffff>
																<tr>
																	<td valign=top >
																	<div style='POSITION: absolute; overflow-y:auto;height: 120px;' id='search_data_area'>
																		<TABLE id=search_table style='table-layout:fixed;width:100%;' cellSpacing=0 cellPadding=0 bgColor=#ffffff border=0>
																		<TBODY id=search_table_body></TBODY>
																		</TABLE>
																	<div>
																	</td>
																</tr>
															</table>

														</td>
													</tr>
												</table>
												</DIV>
												</td>
												<td colspan=2 style='padding-left:5px;'><span class='p11 ls1'>* 상품명의 일부를 입력하시면 자동검색됩니다. 2자 이상 입력해주세요</span></td>
											</tr>
											</table>
										</td>
									</tr>
									<!--tr hegiht=1><td colspan=4  class='td_underline'></td></tr-->

								</table>
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
			<tr ><td  colspan=2 align=center style='padding-top:20px;'><input type=image src='../images/".$admininfo["language"]."/bt_search.gif' border=0 align=absmiddle> <!--btn_inquiry.gif--></td></td>
			</form>
			<tr>
			<td valign=top >";

$Contents .=	"
			</td>
			<td valign=top style='padding:0px;padding-top:0px;' id=product_list>
			<!--form ><input type=hidden name='mode' value='search'>

			<table width='100%'>
				<tr height=25 align=center>
					<td>상품코드</td>
					<td>제품명</td>
					<td>가격</td>
					<td>검색</td>
				</tr>
				<tr height=25 align=center>
					<td><input type=text name=pid value='$pid' size=8></td>
					<td><input type=text name=pname value='$pname' size=30></td>
					<td nowrap><input type=text name=from_price value='$from_price' size=10> ~ <input type=text name=to_price value='$to_price' size=10></td>
					<td><input type=submit value='search'></td>
					<td><input type=button value='전체보기' onclick=\"document.location.href='/admin/product_list.php'\"></td>
				</tr>
			</table>";

$Contents .=	"
			</form-->";
$innerview = "
			<table width='100%' cellpadding=0 cellspacing=0 border=0>
			<tr height=30>
				<td align=left>
				<b>상품명</b>
				<a href='?cid=$cid&depth=0&orderby=pname&ordertype=asc'><img src='../image/orderby_desc.gif' border=0 align=top alt='가나다순' title='가나다순'></a>
				<a href='?cid=$cid&depth=0&orderby=pname&ordertype=desc'><img src='../image/orderby_asc.gif' border=0 align=top alt='가나다역순' title='가나다역순'></a>
				<b>출고날짜</b>
				<a href='?cid=$cid&depth=0&orderby=date&ordertype=desc'><img src='../image/orderby_desc.gif' border=0 align=top alt='최근등록순' title='최근등록순'></a>
				<a href='?cid=$cid&depth=0&orderby=date&ordertype=asc'><img src='../image/orderby_asc.gif' border=0 align=top alt='등록순' title='등록순'></a>

				</td>
				</tr></table>
			<table cellpadding=2 cellspacing=0 bgcolor=gray border=0 width=100% class='list_table_box'>
			<tr bgcolor='#cccccc' height=30 align=center>
			  <td width='7%' class=m_td class=m_td  rowspan=2>상품코드</td>
			  <td width='15%' class=m_td rowspan=2>이미지/상품명</td>
			  <td width='10%' class=m_td rowspan=2>옵션명</td>
			  <td width='10%' class=m_td rowspan=2>기초재고</td>
			  <td width='10%' class=m_td colspan=3>입고</td>
			  <td width='10%' class=m_td colspan=3>출고</td>
			  <td width='10%' class=m_td colspan=3>재고</td>
			  <td width='*' class=e_td >대한통운재고</td>
			</tr>
			<tr>
			  <td width='5%' class=m_td height=25px>수량</td>
			  <td width='5%' class=m_td>단가</td>
			  <td width='5%' class=m_td>금액</td>
			  <td width='5%' class=m_td>수량</td>
			  <td width='5%' class=m_td>단가</td>
			  <td width='5%' class=m_td>금액</td>
			  <td width='5%' class=m_td>수량</td>
			  <td width='5%' class=m_td>단가</td>
			  <td width='5%' class=m_td>금액</td>
			  <td width='5%' class=m_td>수량</td>
		  </tr>
			
			";



if($orderby != "" && $ordertype != ""){
	$orderbyString = " order by $orderby $ordertype ";
}else{
	$orderbyString = " order by psi_ix desc ";
}
/*
$where = "where   iohd.charger_ix = cmd.code  and ici.ci_ix = iohd.ci_ix ";


if($search_text != ""){
	$where .= "and ".$search_type." LIKE '%".$search_text."%' ";
}
if ($startDate != "")	$where .= " and date_format(iohd.regdate, '%Y%m%d') between $startDate and $endDate ";
*/	


	/*	
$sql = "select iohd.*,  iohd.charger_ix, ipi.place_name, ici.customer_name, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name
			from inventory_customer_info left join common_member_detail cmd ,
			inventory_output_history_detail iohd  
			left join inventory_place_info ipi on ipi.pi_ix = iohd.pi_ix						
			$where $orderbyString 
			LIMIT $start, $max";
*/


	$sql =	"SELECT ps.pid,ps.opnd_ix, sum(ps.stock) as now_stock, sum(psb.stock) as basic_stock, ihd.input_cnt, iohd.delivery_cnt, ihd.input_price, iohd.delivery_price
			FROM `inventory_product_stockinfo` ps
			left join inventory_product_stockinfo_bydate psb on ps.pid = psb.pid and ps.opnd_ix = psb.opnd_ix and ps.pi_ix = psb.pi_ix
			, (select pid, opnd_ix, sum(input_cnt) as input_cnt, sum(input_price*input_cnt)/sum(input_cnt) as input_price from inventory_input_history_detail where date_format(regdate,'%Y%m%d') between $startDate and $endDate group by pid )  ihd 
			, (select pid, opnd_ix, sum(delivery_cnt) as delivery_cnt , sum(delivery_price*delivery_cnt)/sum(delivery_cnt) as delivery_price from inventory_output_history_detail where date_format(regdate,'%Y%m%d') between $startDate and $endDate group by pid) iohd 
			WHERE ps.pid=ihd.pid and ps.pid=iohd.pid
			group by ps.pid	";
			
			/*
	$sql = "select ips.psi_ix, ips.pi_ix, ips.stock, ihd.pid, ihd.opn_ix, ihd.opnd_ix, ihd.pname, sum(iohd.delivery_cnt)as delivery_cnt, sum(iohd.delivery_price)/count(iohd.iohd_ix) as delivery_price, iohd.charger_ix, ipi.place_name, ici.customer_name,
			(SELECT ROUND(SUM(input_cnt*input_price)/SUM(input_cnt),0) FROM inventory_input_history_detail WHERE pid=iohd.pid AND opn_ix=iohd.opn_ix AND opnd_ix=iohd.opnd_ix) AS cast_price
				from inventory_product_stockinfo ips  
				left join inventory_place_info ipi on ipi.pi_ix = ips.pi_ix				
				left join inventory_input_history_detail ihd on ihd.pi_ix = ips.pi_ix				
				left join inventory_output_history_detail iohd on iohd.pi_ix = ips.pi_ix				
				left join inventory_customer_info ici on ici.ci_ix = ips.ci_ix and ici.customer_type = 'E'
				$where $orderbyString 
				
				";//LIMIT $start, $max*/
//echo nl2br($sql);
$db->query($sql);

//echo $sql;

if($db->total == 0){
	$innerview .= "<tr bgcolor=#ffffff height=50><td colspan=11 align=center> 내역이 없습니다.</td></tr>";
}else{
	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
		$no = $total - ($page - 1) * $max - $i;

		if($db->dt[option_text] == ""){
			$option_text = "FREE";
		}else{
			$option_text = $db->dt[option_text];
		}
		if($db->dt[output_type] == "1"){
			$output_text = "일반판매";
		}else if($db->dt[output_type] == "2"){
			$output_text = "직원판매";
		}else if($db->dt[output_type] == "3"){
			$output_text = "기타판매";
		}else if($db->dt[output_type] == "9"){
			$output_text = "손/방실";
		}
		/*if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product/c_".$db->dt[pid].".gif")){
			$img_str = $admin_config[mall_data_root]."/images/product/c_".$db->dt[pid].".gif";
		}else{
			$img_str = "../image/no_img.gif";
		}*/
		if(file_exists(PrintImage($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/product", $db->dt[pid], "s"))){
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "s");
		}else{
			$img_str = "../image/no_img.gif";
		}
/*
		$sum_delivery_cnt += $db->dt[delivery_cnt];
		$total_delivery_price = ($db->dt[delivery_cnt])*($db->dt[delivery_price]);
		$total_cast_price = ($db->dt[delivery_cnt]*$db->dt[cast_price]);
		$profit = ($db->dt[delivery_price]-$db->dt[cast_price]);
		$profit_price = ($profit*$db->dt[delivery_cnt]);
		$profit_margin = round(($profit_price/$total_delivery_price)*100, 1);
		
		
		$sum_cnt += $db->dt[delivery_cnt];
		$sum_delivery_price += $total_delivery_price;
		$sum_cast_price += $total_cast_price;
		$sum_profit_price += $profit_price;
		$sum_profit_margin = round(($sum_profit_price/$sum_delivery_price)*100, 1);
		*/
		$total_input_price = $db->dt[input_cnt]*$db->dt[input_price];
		$total_delivery_price = $db->dt[delivery_cnt]*$db->dt[delivery_price];
		$stock = $db->dt[basic_stock] + $db->dt[input_cnt] - $db->dt[delivery_cnt];
		$stock_price = $stock * $db->dt[input_price];
		
		$sum_basic_stock += $db->dt[basic_stock];
		$sum_input_cnt += $db->dt[input_cnt];
		$sum_input_price += $db->dt[input_price];
		$sum_input_total_price += $db->dt[input_cnt]*$db->dt[input_price];
		$sum_delivery_cnt += $db->dt[delivery_cnt];
		$sum_delivery_price += $db->dt[delivery_price];
		$sum_delivery_total_price += $db->dt[delivery_cnt]*$db->dt[delivery_price];
		$sum_stock += $stock;
		
	$innerview .= "<tr height=70>
					<td class='list_box_td list_bg_gray'>".$db->dt[pid]."</td>
					<td class='list_box_td point' style='padding:5px 5px;' nowrap>
					<table>
						<tr>
							<td width='50' align=center style='padding:0px 10px;'><img src='".$img_str."' width=50 height=50 style='border:1px solid #eaeaea' align=absmiddle></td>
							<td  class='list_box_td'style='text-align:left; padding-left:10px;line-height:150%;'>
							<span class='small'>".getCategoryPathByAdmin($db->dt[cid], 4)."</span><br>
							<!--a href=\"javascript:PoPWindow3('stock_output_detail.php?idx=".$db->dt[o_ix]."&pid=".$db->dt[pid]."&company_code=".$db->dt[pi_ix]."',820,800,'input_detail_pop')\"--><b>".$db->dt[pname]."</b><!--/a-->
							</td>
						</tr>
					</table>
					</td>
					<td class='list_box_td list_bg_gray'> ".$db->dt[option_div]."</td>
					<td class='list_box_td'>".$db->dt[basic_stock]."</td>
					<td class='list_box_td list_bg_gray'>".number_format($db->dt[input_cnt])."</td>
					<td class='list_box_td' style='padding:0px 5px;' nowrap>".number_format($db->dt[input_price])."</td>
					<td class='list_box_td list_bg_gray'>".number_format($total_input_price)."</td>
					<td class='list_box_td'>".number_format($db->dt[delivery_cnt])."</td>
					<td class='list_box_td list_bg_gray '>".number_format($db->dt[delivery_price])."</td>
					<td class='list_box_td '>".number_format($total_delivery_price)."</td>
					<td class='list_box_td list_bg_gray'>".number_format($stock)."</td>
					<td class='list_box_td '>".number_format($db->dt[input_price])."</td>
					<td class='list_box_td list_bg_gray'>".number_format($stock_price)."</td>
					<td class='list_box_td '>".$profit_margin."</td>
					
				
";

	}
	$innerview .= "<tr height=30>
					<td class='list_box_td list_bg_gray' colspan=3><b>합계</b></td>					
					<td class='list_box_td ' >".$sum_basic_stock."</td>
					<td class='list_box_td list_bg_gray'>".$sum_input_cnt."</td>
					<td class='list_box_td ' >".number_format($sum_input_price)."</td>
					<td class='list_box_td list_bg_gray' >".number_format($sum_input_total_price)."</td>
					<td class='list_box_td  ' >".$sum_delivery_cnt."</td>
					<td class='list_box_td list_bg_gray' >".number_format($sum_delivery_price)."</td>
					<td class='list_box_td  ' >".number_format($sum_delivery_total_price)."</td>
					<td class='list_box_td list_bg_gray ' >".number_format($sum_stock)."</td>
					<td class='list_box_td  ' >".number_format($sum_input_price)."</td>
					<td class='list_box_td list_bg_gray ' >".number_format($sum_input_total_price)."</td>
					<td class='list_box_td  ' >".number_format($sum_profit_margin)."</td>
					
					</tr>
					";

	

}
	$innerview .= "</table>
				

				";

$Contents = $Contents.$innerview ."
			</td>
			</tr>
		</table>
		<iframe name='act' src='' width=0 height=0></iframe>
			";

$help_text = "
<table cellpadding=0 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >리스트에서 기본적인 정보를 수정하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >개별정보를 수정후 <img src='../image/btc_modify.gif' align=absmiddle> 버튼를 클릭하시면 해당 제품만을 수정하실수 있습니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >리스트의 여러제품을 수정후 <img src='../image/bt_all_modify.gif' align=absmiddle> 버튼를 클릭하시면 해당 리스트에 보여지는 전체 제품을 수정하실수 있습니다</td></tr>
</table>
";

//$Contents .= HelpBox("상품 리스트", $help_text);


$category_str ="<div class=box id=img3  style='width:155px;height:250px;overflow:auto;'>".Category()."</div>";


	$Script = "<Script Language='JavaScript' src='/js/ajax2.js'></Script>\n
	<script Language='JavaScript' src='../include/zoom.js'></script>
	<!-- 스크립트 에러 발생으로 주석처리함 kbk -->
	<script src='stock_output.list.js' type='text/javascript'></script>
	<script type='text/javascript'>	
function onLoad(FromDate, ToDate) {
	var frm = document.search_frm;
	
	
	LoadValues(frm.vFromYY, frm.vFromMM, frm.vFromDD, FromDate);
	LoadValues(frm.vToYY, frm.vToMM, frm.vToDD, ToDate);";
	
/*
if($regdate != "1"){
$Script .= "
	frm.vFromYY.disabled = true;
	frm.vFromMM.disabled = true;
	frm.vFromDD.disabled = true;
	frm.vToYY.disabled = true;
	frm.vToMM.disabled = true;
	frm.vToDD.disabled = true;	";
}*/
$Script .= "	
	init_date(FromDate,ToDate);
	
}
</script>
";
	$P = new LayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 집계표 > $page_title";
	$P->title = "$page_title";
	$P->strContents = $Contents;
	$P->OnloadFunction = "onLoad('$sDate','$eDate');";//"ChangeOrderDate(document.search_frm);";
	$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n$Script";
	$P->PrintLayOut();

/*

insert into inventory_product_stockinfo_bydate
select '' as bsib_ix ,'20110201' as vdate,ci_ix,pi_ix,pid,opn_ix,opnd_ix,stock_pcode,stock,exit_order,NOW() as regdate 
from inventory_product_stockinfo

SELECT pid,opnd_ix, sum(ps.stock) as now_stock, sum(psb.stock) as basic_stock  , ihd.input_cnt, ihd.input_price, iohd.delivery_cnt , iohd.delivery_price
FROM `inventory_product_stockinfo` ps
left join inventory_product_stockinfo_bydate psb on ps.pid = psb.pid and ps.opnd_ix = psb.opnd_ix and ps.pi_ix = psb.pi_ix
, (select ihd.pid, sum(input_cnt) as input_cnt from inventory_input_history_detail where date_format('Ymd', regdate) between '' and '')  ihd 
, (select iohd.pid, sum(delivery_cnt) as delivery_cnt from inventory_output_history_detail where date_format('ymd', regdate) between '' and '') iohd 
WHERE ps.pid = ihd.pid and ps.opnd_ix = ihd.opnd_ix and ps.pi_ix = ihd.pi_ix and ps.pid = iohd.pid and ps.opnd_ix = iohd.opnd_ix and ps.pi_ix = iohd.pi_ix
group by ps.pid, ps.opnd_ix

SELECT ps.pid,ps.opnd_ix, sum(ps.stock) as now_stock
FROM `inventory_product_stockinfo` ps
left join inventory_product_stockinfo_bydate psb on ps.pid = psb.pid and ps.opnd_ix = psb.opnd_ix and ps.pi_ix = psb.pi_ix
, (select  sum(input_cnt) as input_cnt from inventory_input_history_detail where date_format('Ymd', regdate) between '' and '')  ihd 
, (select  sum(delivery_cnt) as delivery_cnt from inventory_output_history_detail where date_format('ymd', regdate) between '' and '') iohd 
WHERE ps.pid
group by ps.pid


SELECT ps.pid, sum(ps.stock) as now_stock, sum(psb.stock) as basic_stock  
FROM `inventory_product_stockinfo` ps
left join inventory_product_stockinfo_bydate psb on ps.pid = psb.pid and ps.opnd_ix = psb.opnd_ix and ps.pi_ix = psb.pi_ix
, (select ihd.pid, sum(input_cnt) as input_cnt FROM inventory_input_history_detail where having date_format('Ymd', regdate) between '' and '') 

WHERE 1
group by ps.pid

SELECT * FROM inventory_product_stockinfo as ps 
left join inventory_product_stockinfo_bydate as psb on ps.pid = psb.pid
WHERE  ps.pid IN (SELECT pid FROM inventory_input_history_detail WHERE pid and opnd_ix ;


SELECT psb.pid, (psb.stock) as stock FROM inventory_product_stockinfo_bydate as psb
left join inventory_product_stockinfo as ps on psb.pid = ps.pid
WHERE psb.pid = ps.pid  



CREATE TABLE IF NOT EXISTS `inventory_product_stockinfo_bydate` (
  `psib_ix` int(10) NOT NULL auto_increment COMMENT '인덱스',
  `vdate` varchar(8) NOT NULL COMMENT '재고기준 일자',
  `ci_ix` int(6) unsigned NOT NULL COMMENT '입고처키',
  `pi_ix` int(6) unsigned NOT NULL COMMENT '보관장소',
  `pid` int(10) unsigned zerofill default NULL COMMENT '상품아이디',
  `opn_ix` int(6) unsigned default NULL COMMENT '상품물류 코드',
  `opnd_ix` int(10) unsigned default NULL COMMENT '상품물류 옵션코드',
  `stock_pcode` varchar(30) default NULL COMMENT '상품물류 코드',
  `stock` int(8) default NULL COMMENT '재고',
  `exit_order` int(4) default NULL COMMENT '출고우선순위',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY  (`psib_ix`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='일자별/상품별 재고 상세정보'  ;


*/



?>

