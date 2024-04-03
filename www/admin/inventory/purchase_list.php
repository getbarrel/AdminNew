<?
include("../class/layout.class");
include("inventory.lib.php");

$db = new Database;


if($info_type == 'purchase_ready'){
	$fix_status = array('OR','ORC');//OR:발주예정,ORC:발주예정취소
}else if($info_type == 'purchase_apply_complete'){
	$fix_status = array('AC','ACC');//AC:청구확정,ACC:청구확정취소
}else if($info_type == 'purchase_complete'){
	$fix_status = array('OC','OCC');//OC:발주확정,OCC:발주확정취소
}


if(empty($status)){
	$status = $fix_status;
}

if($max==""){
	$max = 15; //페이지당 갯수
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if ($startDate == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-15, date("Y"));
	$startDate = date("Y-m-d", $before10day);
	$endDate = date("Y-m-d");
}


$Script = "
<script Language='JavaScript' type='text/javascript' src='placesection.js'></script>
<script language='javascript'>

$(document).ready(function(){
	ChangeOrderDate(document.search_frm);
});

function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#startDate').addClass('point_color');
		$('#endDate').addClass('point_color');
	}else{
		$('#startDate').removeClass('point_color');
		$('#endDate').removeClass('point_color');
	}
}

function clearAll(frm){
	$('.select_ioid').each(function(){
		$(this).attr('checked',false);
	});
}

function checkAll(frm){
	$('.select_ioid').each(function(){
		$(this).attr('checked','checked');
	});
}

function fixAll(frm){
	if (!frm.all_fix.checked){
		clearAll(frm);
		frm.all_fix.checked = false;
	}else{
		checkAll(frm);
		frm.all_fix.checked = true;
	}
}

function purchaseSubmit(frm) {

	if(frm.update_type.value == 1){
		if(parseInt(frm.search_searialize_value.value.length) <= 58){
			alert(language_data['product_list.js']['K'][language]);//'검색상품 전체에 대한 적용은 검색후 가능합니다.'
			//select_update_unloading();
			return false;
		}
		
		if(confirm(language_data['product_list.js']['G'][language])){//'검색상품 전체에 정보변경을 하시겠습니까?'
			return true;
		}else{
			//select_update_unloading();
			return false;
		}
	}else if(frm.update_type.value == 2){

		var ioid_checked_bool = false;

		$('input[name^=ioid]').each(function (){
			var checked = $(this).is(':checked');
			if(checked == true){
				ioid_checked_bool = true;
			}
		})

		if(!ioid_checked_bool){
			alert(language_data['product_list.js']['H'][language]);//'선택된 제품이 없습니다. 변경하시고자 하는 상품을 선택하신 후 저장 버튼을 클릭해주세요'
			//select_update_unloading();
			return false;
			
		}
	}

	if(!CheckFormValue(frm)){
		return false;
	}

}

</script>";


$Contents ="
<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
	<tr>
		<td align='left'> ".GetTitleNavigation($PAGE_INFO["title"], "재고관리 > 발주관리 > ".$PAGE_INFO['title'])."</td>
	</tr>
	<tr>
		<td>
			<form name='search_frm'>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
				<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  border=0 cellpadding='0' cellspacing='0'>
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
											<td class='box_05' valign=top>
												<TABLE cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
												<TR>
													<TD bgColor=#ffffff style='padding:0 0 0 0;'>
													<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
														<col width='15%'>
														<col width='35%'>
														<col width='15%'>
														<col width='35%'>
														<tr height=27>
															<td class='search_box_title' bgcolor='#efefef' align=center>
																<label for='orderdate'>
																	<b>";
																		if($PAGE_INFO["type"]=="apply_complete"){
																			$Contents .="청구확정일";
																		}elseif($PAGE_INFO["type"]=="stocked"){
																			$Contents .="발주확정일";
																		}elseif($PAGE_INFO["type"]=="statement"){
																			$Contents .="입고완료일";
																		}else{
																			$Contents .="발주요청일";
																		}
																	$Contents .="
																	</b>
																</label>
																<input type='checkbox' name='orderdate' id='orderdate' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue('1',$orderdate,' checked').">
																<input type='hidden' name='date_type' id='date_type' value='".$PAGE_INFO["date"]."'>
															</td>
															<td class='search_box_item' colspan=3>
																".search_date('startDate','endDate',$startDate,$endDate)."
															</td>
														</tr>
														<tr height='27'>
															<th class='search_box_title' align=center>사업장 선택</th>
															<td class='search_box_item'>
																".SelectEstablishment($company_id,"company_id","select","false","onChange=\"loadPlace(this,'pi_ix')\" ")."
																".SelectInventoryInfo($company_id,"",'pi_ix','select','false', "title='창고' ")."
															</td>
															<th class='search_box_title'align=center>매입처</th>
															<td class='search_box_item'>
																".SelectSupplyCompany($ci_ix,"ci_ix","select", "false", "1")."
															</td>
														</tr>
														<tr height='27'>
															<th class='search_box_title' align=center>처리상태</th>
															<td class='search_box_item'>";
															
															if(count($fix_status) > 0){
																foreach($fix_status as $fs){
																	$Contents .="
																	<input type='checkbox' name='status[]' id='status_".$fs."' value='".$fs."' ".CompareReturnValue($fs,$status,' checked')." ><label for='status_".$fs."'>".$inventory_order_status[$fs]."</label>";
																}
															}
															$Contents .="
															</td>
															<th class='search_box_title' align=center>조건검색</th>
															<td class='search_box_item'>
																<table cellpadding='3' cellspacing='0' border='0' width='100%'>
																<tr>
																	<td width='80px'>
																	<select name='search_type' style='font-size:12px;'>
																		<option value='ioid' ".CompareReturnValue('ioid',$search_type,' selected').">발주번호</option>
																		<option value='charger' ".CompareReturnValue('charger',$search_type,' selected').">담당자</option>
																	</select>
																	</td>
																	<td width='*'><input type='text' class=textbox name='search_text' size='30' value='".$search_text."' style='' ></td>
																</tr>
																</table>
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
							<tr>
								<td colspan=3 align=center  style='padding:10px 0 20px 0'>
									<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>";

	$where=" where ioid !='' ";
	
	if(!$date_type){
		$date_type = $PAGE_INFO["date"];
	}
	
	if($orderdate){
		$where .= " and date_format(".$date_type.",'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
	}
	
	if($company_id){
		$where .= " and company_id = '".$company_id."' ";
	}
	
	if($pi_ix){
		$where .= " and pi_ix = '".$pi_ix."' ";
	}

	if($ci_ix){
		$where .= " and ci_ix = '".$ci_ix."' ";
	}
	
	if(is_array($status)){
		$where .= " and status in ('".implode("','",$status)."') ";
	}elseif($status){
		$where .= " and status = '".$status."' ";
	}
	
	if($search_type && $search_text){
		$where .= "and $search_type LIKE '%".trim($search_text)."%' ";
	}

	$sql = "select count(*) as total from inventory_order $where";
	$db->query($sql);
	$db->fetch();
	$total = $db->dt[total];

	$Contents .= "
	<tr>
		<td>
			<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
				<tr height=30>
					<td>
						<b class=blk>전체 : ".$total." 건</b>
					 </td>
					<td align=right >";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	$Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
					<a href='excel_config.php?".$QUERY_STRING."&info_type=$info_type&excel_type=purchase_list' rel='facebox' >
					<img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
}else{
	$Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
}
	$Contents .= "&nbsp;";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){		//$fix_status
	$Contents .= "<a href='purchase_list.php?mode=excel&info_type=$info_type&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}else{
	$Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}

$Contents .= "
					</td>
				</tr>
			</table>
			<form  name='input_frm' method='post' onsubmit='return purchaseSubmit(this)' action='./purchase.act.php' target='act'>
			<input type='hidden' name='search_searialize_value' value='".urlencode(serialize($_GET))."'>
			<input type='hidden' name='page_type' value='".$PAGE_INFO["type"]."'>
			<table cellpadding=0 cellspacing=0 width=100% bgcolor=silver border='0' style='padding:0px;margin:0px;' class='list_table_box'>
				<tr bgcolor=#efefef style='font-weight:600;'>
					<td class=s_td width='3%' height=27 align='center' nowrap><input type=checkbox class=nonborder name='all_fix' onclick='fixAll(document.input_frm)'></td>
					<td class=m_td width='9%' align='center'>발주번호<br/>".($PAGE_INFO["type"]=="stocked" ? "발주확정일" : "청구확정일")."</td>
					<td class=m_td width='*' align='center'>매입처</td>
					<td class=m_td width='7%' align='center'>요청총수량</td>";

					if($PAGE_INFO["type"]!="complete" && $PAGE_INFO["type"]!="stocked"){
						$Contents .= "
						<td class=m_td width='8%' align='center'>품목금액합계</td>
						<td class=m_td width='8%' align='center'>배송비</td>";
					}
					
					if($PAGE_INFO["type"]=="stocked"){
						$Contents .= "
						<td class='m_td' width='8%' align='center'>총합계</td>
						<td class='m_td' width='5%' align='center'>완료수량</td>
						<td class='m_td' width='5%' align='center'>취소수량</td>
						<td class='m_td' width='5%' align='center'>잔여수량</td>
						<td class='m_td' width='15%' align='center'>납품처명</td>
						<td class='m_td' width='8%' align='center'>납품완료일</td>";
					}elseif($PAGE_INFO["type"]=="statement"){
						$Contents .= "
						<td class='m_td' width='5%' align='center'>입고수량</td>
						<td class='m_td' width='15%' align='center'>실공급가/세액/매입가</td>
						<td class='m_td' width='5%' align='center'>실배송비</td>
						<td class='m_td' width='8%' align='center'>납품처명</td>";
					}else{
						$Contents .= "
						<td class='m_td' width='9%' align='center'>총합계</td>
						<td class='m_td' width='13%' align='center'>납품처명</td>";
					}

					$Contents .= "
					<td class='m_td' width='6%' align='center'>처리상태</td>";

					if($PAGE_INFO["type"]=="complete"){
						$Contents .= "
						<td class='m_td' width='8%' align='center'>이메일발송</td>
						<td class='m_td' width='8%' align='center'>문자발송</td>";
					}else{
						$Contents .= "
						<td class='m_td' width='7%' align='center'>업체담당</td>";
					}
					$Contents .= "
					<td class=e_td width='8%' align='center'>관리</td>
				</tr>";

if($total == 0){
	$Contents .= "<tr bgcolor=#ffffff height=50><td class='list_box_td' colspan=13 align=center>내역이 존재 하지 않습니다.</td></tr>";
}else{
	
	$sql = "select * from inventory_order $where order by regdate desc limit $start, $max";
	$db->query($sql);


if($mode == "excel"){

	$datas = $db->fetchall();	//총발주수량

	include("excel_out_columsinfo.php");
	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='inventory_excel_".$info_type."' ";

	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='inventory_excel_checked_".$info_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel_checked = $db->dt[conf_val];

	$check_colums = unserialize(stripslashes($stock_report_excel_checked));
	$columsinfo = $colums;

	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$inventory_excel = new PHPExcel();

	// 속성 정의
	$inventory_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");
	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
		$col++;
	}

	$before_pid = "";

	if($info_type == "warehouse" || $info_type == "category"){
		for ($i = 0; $i < count($goods_infos); $i++)
		{
			$stock_assets_sum += $goods_infos[$i][stock_assets];
			$stock_sum += $goods_infos[$i][stock];
			$stock_assets_total += $goods_infos[$i][stock_assets];
			$order_cnt_sum += $goods_infos[$i][order_cnt];
		}
	}
	

for($k=0;$k<count($datas);$k++){

	$ioid = $datas[$k][ioid];

	$sql = "select 
				*
				from 
					inventory_order as o
					inner join inventory_order_detail as od on (o.ioid = od.ioid)
				where
					o.ioid = '".$ioid."'
					";
	$db->query($sql);
	$goods_infos = $db->fetchall();


	for ($i = 0; $i < count($goods_infos); $i++)
	{
		
		$sql = "select
						g.*,
						gu.*,
						ifnull(sum(ips.stock),0) as stock
					from
							inventory_goods as g 
							inner join inventory_goods_unit as gu on (g.gid = gu.gid and g.basic_unit = gu.unit)
							right join inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
					where	
						gu.gu_ix = '".$goods_infos[$i][gu_ix]."'
					";
		$db->query($sql);
		$goods_data = $db->fetch();

		$j="A";
		foreach($check_colums as $key => $value){
			
			if($key == "gocde"){
				$value_str =  $goods_data[gocde];	//대표코드
			}else if($key == "standard"){
				$value_str =  $goods_data[standard];	//규격
			}else if($key == "item_account"){
				$value_str = $ITEM_ACCOUNT[$goods_infos[$i][item_account]];
			}else if($key == "basic_unit"){
				switch($goods_data[basic_unit]){
					case '1':
						$value_str = 'EA';
					break;
					case '2':
						$value_str = 'Kg';
					break;
					case '3':
						$value_str = 'm2';
					break;
					case '4':
						$value_str = 'Roll';
					break;
					case '5':
						$value_str = 'BOX';
					break;
					case '6':
						$value_str = 'Pack';
					break;
					case '7':
						$value_str = '생산단위';
					break;
					case '8':
					$value_str = '식';
					break;
				}
			}else if($key == "unit"){
				switch($goods_data[unit]){
					case '1':
						$value_str = 'EA';
					break;
					case '2':
						$value_str = 'Kg';
					break;
					case '3':
						$value_str = 'm2';
					break;
					case '4':
						$value_str = 'Roll';
					break;
					case '5':
						$value_str = 'BOX';
					break;
					case '6':
						$value_str = 'Pack';
					break;
					case '7':
						$value_str = '생산단위';
					break;
					case '8':
					$value_str = '식';
					break;
				}
				//$value_str = $goods_data[basic_unit];	//기본단위
			}else if($key == "order_basic_unit"){
				switch($goods_data[order_basic_unit]){
					case '1':
						$value_str = 'EA';
					break;
					case '2':
						$value_str = 'Kg';
					break;
					case '3':
						$value_str = 'm2';
					break;
					case '4':
						$value_str = 'Roll';
					break;
					case '5':
						$value_str = 'BOX';
					break;
					case '6':
						$value_str = 'Pack';
					break;
					case '7':
						$value_str = '생산단위';
					break;
					case '8':
					$value_str = '식';
					break;
				}
			}else if($key == "change_amount"){
				$value_str = $goods_data[change_amount];	//환산수량
			}else if($key == "stock"){
				$value_str = $goods_data[stock];	//현누적재고
			}else if($key == "status"){
				$value_str = $inventory_order_status[$goods_infos[$i][status]];	//처리상태
			}else{
				$value_str = $goods_infos[$i][$value];//$db1->dt[$value];
			}

			$inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
			$j++;
		}
		$z++;

	}

}



	// 첫번째 시트 선택
	$inventory_excel->setActiveSheetIndex(0);

	// 너비조정
	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="stock_report_'.$info_type.'.xls"');
	header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'CSV');
	$objWriter->setUseBOM(true);
	$objWriter->save('php://output');

	exit;
}
//print_r($_SERVER);


	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);

		$Contents .= "
		<tr bgcolor=#ffffff align=center height='30'>
			<td class='list_box_td' bgcolor='#efefef'><input type=checkbox class='nonborder select_ioid' id='ioid' name='ioid[]' value='".$db->dt[ioid]."' ".(in_array($db->dt[status],array("ACC","ORC","OCC")) ? "disabled" : "")."></td>
			<td class='list_box_td' bgcolor='#ffffff' nowrap><b class='blue'>".$db->dt[ioid]."</b><br/>".($PAGE_INFO["type"]=="stocked" ? $db->dt[oc_date] : $db->dt[regdate])."</td>
			<td bgcolor='#ffffff' align=left style='padding-left:3px;'>".$db->dt[ci_name]."</td>
			<td bgcolor='#ffffff'>".number_format($db->dt[goods_cnt])."</td>";
			if($PAGE_INFO["type"]!="complete" && $PAGE_INFO["type"]!="stocked"){
				$Contents .= "
				<td bgcolor='#ffffff'>".number_format($db->dt[goods_price])."</td>
				<td bgcolor='#ffffff'>".number_format($db->dt[delivery_price])."</td>";
			}
		
			if($PAGE_INFO["type"]=="stocked"){
				$Contents .= "
				<td  class='list_box_td point'>".number_format($db->dt[total_price])."</td>
				<td bgcolor='#ffffff'>".number_format($db->dt[real_goods_cnt])."</td>
				<td bgcolor='#ffffff'>".number_format($db->dt[cancel_goods_cnt])."</td>
				<td bgcolor='#ffffff'>".number_format($db->dt[goods_cnt]-$db->dt[real_goods_cnt]-$db->dt[cancel_goods_cnt])."</td>
				<td bgcolor='#ffffff' align=left style='padding-left:3px;'>".$db->dt[delivery_name]."</td>
				<td class='list_box_td' bgcolor='#ffffff' nowrap> ".str_replace(" ","<br/>",$db->dt[wc_date])."</td>";
			}elseif($PAGE_INFO["type"]=="statement"){
				
				$real_goods_price=$db->dt[real_goods_price];
				$real_goods_coprice_price=round($real_goods_price/1.1);
				$real_goods_tax_price= $real_goods_price-$real_goods_coprice_price;

				$Contents .= "
				<td  class='list_box_td point'>".number_format($db->dt[real_goods_cnt])."</td>
				<td bgcolor='#ffffff'>".number_format($real_goods_coprice_price)." / ".number_format($real_goods_tax_price)." / ".number_format($real_goods_price)."</td>
				<td bgcolor='#ffffff'>".number_format($db->dt[real_delivery_price])."</td>
				<td bgcolor='#ffffff' align=left style='padding-left:3px;'>".$db->dt[delivery_name]."</td>";
			}else{
				$Contents .= "
				<td  class='list_box_td point'>".number_format($db->dt[total_price])."</td>
				<td bgcolor='#ffffff' align=left style='padding-left:3px;'>".$db->dt[delivery_name]."</td>
				";
			}

			$Contents .= "<td class='list_box_td' bgcolor='#ffffff' nowrap> ".$inventory_order_status[$db->dt[status]]."</td>";

			if($PAGE_INFO["type"]=="complete"){

				if($db->dt[email_send]=="Y")		$email_send="전송<br/>".str_replace(" ","<br/>",$db->dt[email_date]);
				else										$email_send="미전송";

				if($db->dt[sms_send]=="Y")		$sms_send="전송<br/>".str_replace(" ","<br/>",$db->dt[sms_date]);
				else										$sms_send="미전송";

				$Contents .= "
				<td class='list_box_td' bgcolor='#efefef'>".$email_send." </td>
				<td class='list_box_td' bgcolor='#efefef'>".$sms_send." </td>";
			}else{
				$Contents .= "
				<td class='list_box_td' bgcolor='#efefef'>".$db->dt[charger]." </td>";
			}

			$Contents .= "
			<td class='list_box_td' bgcolor='#ffffff'>";
				
				if(in_array($db->dt[status],array("ACC","ORC","OCC","WC","GA"))){
					$onclick="PoPWindow3('../inventory/purchase.pop.php?ioid=".$db->dt[ioid]."',950,700,'purchase_detail');";
					$Contents .= "<input type='button' value='상세보기' onclick=\"".$onclick."\" />";
				}else{
					
					if($PAGE_INFO["type"]=="apply_complete"){
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U"))		$onclick="PoPWindow3('../inventory/purchase.pop.php?ioid=".$db->dt[ioid]."&view_type=update',950,700,'purchase_pop');";
						else																		$onclick=$auth_update_msg;

						$Contents .= "<input type='button' value='발주예정' onclick=\"".$onclick."\" />";
					}elseif($PAGE_INFO["type"]=="ready"){
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U"))		$onclick="PoPWindow3('../inventory/purchase_detail.php?ioid=".$db->dt[ioid]."&view_type=update',950,700,'purchase_detail');";
						else																		$onclick=$auth_update_msg;

						$Contents .= "<input type='button' value='발주확정' onclick=\"".$onclick."\" />";
					}elseif($PAGE_INFO["type"]=="complete"){
						$onclick="PoPWindow3('../inventory/purchase_detail.php?ioid=".$db->dt[ioid]."',950,700,'purchase_detail');";
						$Contents .= "<input type='button' value='상세보기' onclick=\"".$onclick."\" />";
					}elseif($PAGE_INFO["type"]=="stocked"){
						if($db->dt[status]!="WC"){
							if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U"))		$onclick="PoPWindow3('../inventory/purchase.pop.php?ioid=".$db->dt[ioid]."&view_type=stocked',950,700,'purchase_pop');";
							else																		$onclick=$auth_update_msg;

							$Contents .= "<input type='button' value='부분입고' onclick=\"".$onclick."\" />";
						}
					}
				}

			$Contents .= "
			</td>
		</tr>";
	}
}

if($_SERVER[QUERY_STRING] == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$_SERVER[QUERY_STRING]) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$_SERVER[QUERY_STRING]) ;
}

$Contents .= "
			</table>
			<table cellpadding=0 cellspacing=0 width=100%>
				<tr height=30 bgcolor=#ffffff>
					<td colspan=6 align=center style='padding:10px 0 0 0'>".page_bar($total, $page, $max,  $query_string, "")."</td>
				</tr>
			</table>";

$select = "
	<select name='update_type' >
		<option value='2' selected>선택한 상품 전체에</option>
		<option value='1'>검색한 상품 전체에</option>
	</select>";
	
	if($PAGE_INFO["type"]=="complete"){
		$HelpBoxWidth=350;
		$select .= "
		<input type='radio' name='act' id='send_email' value='send_email' checked><label for='send_email'> 이메일 발송</label>
		<input type='radio' name='act' id='send_sms' value='send_sms' ><label for='send_sms'> 문자 발송</label>";
	}elseif($PAGE_INFO["type"]=="stocked"){
		$HelpBoxWidth=280;
		$select .= "
		<input type='radio' name='act' id='all_stocked' value='all_stocked' checked><label for='all_stocked'> 발주입고완료 </label>";
	}else{
		$HelpBoxWidth=280;
		$select .= "
		<input type='radio' name='act' id='status_update' value='status_update' checked><label for='status_update'> 처리상태 변경 </label>";
	}
	
	if($PAGE_INFO["type"]=="complete"){
		$help_text = "
		<div>
			<div style='padding:4px 0 4px 0'>
				<img src='../images/dot_org.gif'> <b>E-Mail 및 SMS 보내기</b> <span class=small style='color:gray'> E-Mail 및 SMS 을 보내고 싶은 발주건을 선택한후 저장 버튼을 눌러 주세요.</span>
			</div>
		</div>";
	}elseif($PAGE_INFO["type"]=="stocked"){
		$help_text = "
		<div>
			<div style='padding:4px 0 4px 0'>
				<img src='../images/dot_org.gif'> <b>발주입고완료</b> <span class=small style='color:gray'> 발주입고완료 상태로 바꾸실 발주건을 선택한후 저장 버튼을 눌러 주세요.</span>
			</div>
			<div style='padding:4px 0 4px 0'>
				<img src='../images/dot_org.gif'> <span class=small style='color:gray'> 발주입고완료 변경시 잔여수량이 모두 완료 수량으로 변경됩니다.</span>
			</div>
		</div>";
	}else{
		$help_text = "
		<div>
			<div style='padding:4px 0 4px 0'>
				<img src='../images/dot_org.gif'> <b>처리상태 변경</b> <span class=small style='color:gray'> 처리상태를 변경하고자 발주건을 선택한후 저장 버튼을 눌러 주세요.</span>
			</div>
			<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
			<col width='15%'>
			<col width='*'>
			<col width='15%'>
			<col width='35%'>
			<tr>
				<td class='input_box_title'> 처리상태 <img src='".$required3_path."'></td>
				<td class='input_box_item' colspan='3'>";
				if($PAGE_INFO["type"]=="apply_complete"){
					$help_text .= "
					<input type='radio' name='u_status' id='update_status_OR' value='OR' checked><label for='update_status_OR'>".$inventory_order_status["OR"]."</label>
					<input type='radio' name='u_status' id='update_status_ACC' value='ACC' ><label for='update_status_ACC'>".$inventory_order_status["ACC"]."</label>";
				}elseif($PAGE_INFO["type"]=="ready"){
					$help_text .= "
					<input type='radio' name='u_status' id='update_status_OC' value='OC' checked><label for='update_status_OC'>".$inventory_order_status["OC"]."</label>
					<input type='radio' name='u_status' id='update_status_ORC' value='ORC' ><label for='update_status_ORC'>".$inventory_order_status["ORC"]."</label>";
				}elseif($PAGE_INFO["type"]=="statement"){
					$help_text .= "
					<input type='radio' name='u_status' id='update_status_GA' value='GA' checked><label for='update_status_GA'>".$inventory_order_status["GA"]."</label>";
				}
				$help_text .= "
				</td>
			</tr>
			</table>
		</div>";
	}

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$help_text .= "
	<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td colspan=4 align=center><img src='../images/".$admininfo["language"]."/b_save.gif' onclick=\"Submit_bool=true;$(this).closest('form').submit();\" border=0 align=absmiddle style='cursor:pointer;'></td></tr>
	</table>";
}else{
	$help_text .= "<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
		<tr height=50><td align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle ></a></td></tr>
	</table>";
}


$Contents .= HelpBox($select, $help_text,$HelpBoxWidth);
$Contents .= "</form>";

$Contents .= "
		</td>
	</tr>
</table>";




$P = new LayOut();
$P->addScript = $Script;
$P->OnloadFunction = "";
$P->strLeftMenu = inventory_menu();
$P->Navigation = "재고관리 > 발주관리 > ".$PAGE_INFO["title"];
$P->title = $PAGE_INFO['title'];
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
