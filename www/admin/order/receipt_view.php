<?
include("../class/layout.class");


$db = new Database;

$cominfo = getcominfo();

$sql="SELECT bname, bmail, date_format(order_date,'%Y.%m.%d') as order_date FROM shop_order where oid = '".$oid."'";
$db->query($sql);
$order = $db->fetch("object");
list($order[year],$order[month],$order[day]) = explode(".",$order[order_date]);

$sql="select
	sum(case when payment_status='F' then -product_price else product_price end) as product_price,
	sum(case when payment_status='F' then -delivery_price else delivery_price end) as delivery_price
 from shop_order_price where oid='".$oid."'";
$db->query($sql);
$order_price = $db->fetch("object");

$order_price[total_price]=$order_price[product_price]+$order_price[delivery_price];


$sql="SELECT pt_dcprice as ptprice, pname, option_text, pcnt, dcprice as price FROM shop_order_detail where oid = '".$oid."' and ifnull(refund_status,'')!='".ORDER_STATUS_REFUND_COMPLETE."'";
if($order_price[delivery_price] > 0){
	$sql.=" UNION ALL SELECT '".$order_price[delivery_price]."' as ptprice, '배송비' as pname, '' as option_text, '1' as pcnt, '".$order_price[delivery_price]."' as price  ";
}

$db->query($sql);
$order_dt = $db->fetchall("object");

if($view_type!="transaction"){
	$view_type="receipt";
}

//거래명세표
$transaction="
<div id='idPrint'>
	<table cellspacing='0' cellpadding='0' border='0' width='640' style='border:3px solid {receipt_color};'>
		<!--거래명세표 상단-->
		<tr >
			<td style='border-bottom:1px solid {receipt_color};'>
				<table cellspacing='0' cellpadding='0' border='0' width='100%'>
					<colgroup>
					<col width='50%'>
					<col width='*'>
					</colgroup>
					<tr align='center' bgcolor='#FFFFFF'>
						<td height='' style='border-right:1px solid {receipt_color};'>
							<strong style=' color:{receipt_color};'>
								<font style='font-size:24px;'>거래명세서</font> ({receipt_title} 보관용)
							</strong>
						</td>
						<td  style='padding:0px;'>
							<table width='100%' height='' border='0' cellspacing='0' cellpadding='0' style=''>
									<tbody>
										<tr>
											<td style='border-bottom:1px solid {receipt_color}; padding:0;' >
												<table cellspacing='0' cellpadding='0' border='0' width='100%' height='30'>
													<tr>
														<td width='80' align='center' bgcolor='#FFFFFF'  style='border-right:1px solid {receipt_color};font-size:12px;color:{receipt_color};'>책&nbsp;번&nbsp;호</td>
														<td width='' colspan='3' align='center' bgcolor='#FFFFFF'  style='color:{receipt_color}; font-size:12px;'><span   style=''></span><span  >권</span></td>
														<td width='' colspan='3' align='center' bgcolor='#FFFFFF'  style='color:{receipt_color}; font-size:12px;'><span   style=''></span><span  >호</span></td>
													</tr>
												</table>
											</td>
											
										</tr>
										<tr>
											<td style='' style='padding:0;'>
												<table cellspacing='0' cellpadding='0' border='0' width='100%' height='30'>
													<tr>
														<td width='80' align='center' bgcolor='#FFFFFF'  style='border-right:1px solid {receipt_color}; font-size:12px;color:{receipt_color};'>일련번호</td>
														<td align='center' bgcolor='#FFFFFF'  style='border-right:1px solid {receipt_color};'></td>
														<td align='center' bgcolor='#FFFFFF'  style='border-right:1px solid {receipt_color};'></td>
														<td align='center' bgcolor='#FFFFFF'  style='border-right:1px solid {receipt_color};'></td>
														<td align='center' bgcolor='#FFFFFF'  style='border-right:1px solid {receipt_color};'></td>
														<td align='center' bgcolor='#FFFFFF'  style='border-right:1px solid {receipt_color};'></td>
														<td align='center' bgcolor='#FFFFFF' ></td>
													</tr>
												</table>
											</td>
										</tr>
									</tbody>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<!--합계금액 및 공급자-->
		<tr >
			<td style='border-bottom:1px solid {receipt_color};'>
				<table cellspacing='0' cellpadding='0' border='0' width='100%'>
					<col width='50%'>
					<col width='50%'>
					<tr>
						<td style='border-right:1px solid {receipt_color};'>
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr >
									<td width='35%'>
										<table cellspacing='0' cellpadding='0' border='0' width='100%'>
											<tr>
												<td  style='font-size:12px; color:000;padding-left:10px;'>".$order[year]."</td>
												<td  style='color:{receipt_color};'>년</td>
												<td  style='font-size:12px; color:000;'>".$order[month]."</td>
												<td  style='color:{receipt_color};'>월</td>
												<td  style='font-size:12px; color:000;'>".$order[day]."</td>
												<td  style='color:{receipt_color};'>일</td>
											</tr>
										</table>
									</td>
									<td width='65%'>
									</td>
								</tr>
								<tr >
									<td style=''></td>
									<td style='border-bottom:1px solid {receipt_color};'>
										<table cellspacing='0' cellpadding='0' border='0' width='100%'>
											<tr>
												<td style='width:70%; text-align:right;'>".$order[bname]."</td>
												<td style='width:30%; text-align:center; color:{receipt_color}; font-size:20px; font-weight:bold;'>귀하</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td style='color:{receipt_color}; font-weight:bold;'>합계금액</td>
									<td style=''>
										<table cellspacing='0' cellpadding='0' border='0' width='100%'>
											<tr>
												<td style='width:70%; text-align:right;'>".number_format($order_price[total_price])."</td>
												<td style='width:30%; text-align:center; color:{receipt_color}; font-size:20px; font-weight:bold;'>원정</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
						<td rowspan='2'>
							<table cellspacing='0' cellpadding='0' border='0' width='100%'>
								<tr>
									<td rowspan='4' width='20' style='border-right:1px solid {receipt_color}; line-height:140%; font-size:12px; text-align:center;color:{receipt_color};'>공<br/>급<br/>자</td>
									<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};text-align:center;font-size:12px;color:{receipt_color}; '>등록번호</td>
									<td style='border-bottom:1px solid {receipt_color};'colspan='3' style='font-size:11px;'>".$cominfo[com_number]."</td>
								</tr>
								<tr>
									<td style='border-right:1px solid {receipt_color};border-bottom:1px solid {receipt_color}; text-align:center;font-size:12px;color:{receipt_color};'>상호<br/>(법인명)</td>
									<td style='border-right:1px solid {receipt_color};border-bottom:1px solid {receipt_color};font-size:11px; '>".$cominfo[com_name]."</td>
									<td style='border-right:1px solid {receipt_color};border-bottom:1px solid {receipt_color}; text-align:center; font-size:12px;color:{receipt_color};'>성명</td>
									<td style='border-bottom:1px solid {receipt_color};font-size:11px;position:relative;'>
										".$cominfo[com_ceo]."
										<img src='http://".$_SERVER['HTTP_HOST']."/admin/images/company-stamp.png' alt='직인' style='width:50px;position:absolute;top:-10px;right:0px;' />
									</td>
								</tr>
								<tr>
									<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color}; text-align:center;font-size:12px;color:{receipt_color};'>사업장<br/>소재지</td>
									<td style='border-bottom:1px solid {receipt_color};font-size:11px;'colspan='3'>".$cominfo[com_addr]."</td>
								</tr>
								<tr>
									<td style='border-right:1px solid {receipt_color}; text-align:center;font-size:12px;color:{receipt_color};'>업태</td>
									<td style='border-right:1px solid {receipt_color};font-size:11px;'>".$cominfo[com_business_status]."</td>
									<td style='border-right:1px solid {receipt_color}; text-align:center;font-size:12px;color:{receipt_color};'>종목</td>
									<td style='font-size:11px;'>".$cominfo[com_business_category]."</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<!--목록-->
		<tr>
			<td>
				<table cellspacing='0' cellpadding='0' border='0' width='100%'>
					<tr>
						<td style='text-align:center; border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:{receipt_color}; width:30px; height:30px;font-size:12px;'>
							월
						</td>
						<td style='text-align:center; border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:{receipt_color}; width:30px;font-size:12px;'>
							일
						</td>
						<td style='text-align:center; border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:{receipt_color}; width:300px;font-size:12px;'>
							품목/규격
						</td>
						<td style='text-align:center; border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:{receipt_color};font-size:12px;'>
							단위
						</td>
						<td style='text-align:center; border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:{receipt_color};font-size:12px;'>
							수량
						</td>
						<td style='text-align:center; border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:{receipt_color};font-size:12px;'>
							단가
						</td>
						<td style='text-align:center; border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:{receipt_color};font-size:12px;'>
							공급가액
						</td>
						<td style='text-align:center;border-bottom:1px solid {receipt_color};color:{receipt_color};font-size:12px;'>
							세액
						</td>
					</tr>";

					if(count($order_dt)>0){
						$sum_coprice=0;
						$sum_tax=0;
						foreach($order_dt as $od){

							$coprice = $od[ptprice]/1.1;
							$tax = $od[ptprice]-$coprice;

							$sum_coprice += $coprice;
							$sum_tax += $tax;

							$transaction.="
							<tr>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;text-align:right; text-indent:-10px; font-size:11px;height:30px;'>
									".$order[month]."
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;text-align:right;text-indent:-10px;font-size:11px;'>
									".$order[day]."
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;text-indent:15px;font-size:11px;'>
									".strip_tags($od[pname])." ".(strip_tags($od[option_text]) ? "<br/>옵션 - ".strip_tags($od[option_text]) : "")."
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;font-size:11px;'>
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;font-size:11px;text-align:center;'>
									".$od[pcnt]."
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;font-size:11px;text-align:right;'>
									".number_format($od[price])."
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;font-size:11px;text-align:right;'>
									".number_format($coprice)."
								</td>
								<td style='border-bottom:1px solid {receipt_color};color:000;font-size:11px;text-align:right;'>
									".number_format($tax)."
								</td>
							</tr>";
						}
					}
				
					for($i=0;$i<(10-count($order_dt));$i++){
						if(count($order_dt) < 9 && $i==0){
							$transaction.="
							<tr>
								<td colspan='8' style='border-bottom:1px solid {receipt_color};color:000;text-align:center; text-indent:-10px; height:30px;font-size:12px;'>
									*** 이하여백 ***
								</td>
							</tr>";
						}else{
							$transaction.="
							<tr>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;text-align:right; text-indent:-10px;font-size:11px;height:30px;'>
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;text-align:right;text-indent:-10px;font-size:11px;'>
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;text-indent:15px;font-size:11px;'>
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;font-size:11px;'>
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;font-size:11px;'>
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;font-size:11px;'>
								</td>
								<td style='border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;font-size:11px;'>
								</td>
								<td style='border-bottom:1px solid {receipt_color};color:000;font-size:11px;'>
								</td>
							</tr>";
						}
					}

					$transaction.="
					<tr>
						<td colspan='6' style='border-bottom:1px solid {receipt_color};color:000;text-align:center; text-indent:-10px;height:30px;'>
							소계
						</td>
						<td style='border-right:1px solid {receipt_color};border-left:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};color:000;font-size:11px;text-align:right;'>
							".number_format($sum_coprice)."
						</td>
						<td style='border-bottom:1px solid {receipt_color};color:000;font-size:11px;text-align:right;'>
							".number_format($sum_tax)."
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<!--합계-->
		<tr>
			<td>
				<table cellspacing='0' cellpadding='0' border='0'width='100%'>
					<col width='7%'>
					<col width=''>
					<col width='7%'>
					<col width=''>
					<col width='7%'>
					<col width='15%'>
					<tr>
						<td style='border-right:1px solid {receipt_color}; color:{receipt_color};text-align:center;height:30px;font-size:12px;'>미수금</td>
						<td style='border-right:1px solid {receipt_color}; text-indent:10px;text-align:center;'> - </td>
						<td style='border-right:1px solid {receipt_color}; color:{receipt_color};text-align:center;font-size:12px;'>합계</td>
						<td style='border-right:1px solid {receipt_color}; text-indent:10px; text-align:center;'>".number_format($order_price[total_price])."</td>
						<td style='color:{receipt_color}; text-align:center; font-size:12px;'>인수자</td>
						<td style='text-align:right;'>(인)</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>";


$receipt="
<div id='idPrint'>
	<table cellspacing='0' cellpadding='0' border='0' width='352' style='border:2px solid {receipt_color};'>
		<tr>
			<td align='center' height='37px' colspan='2' style='table-layout:fixed;'>
				<span style='font-size:22px; color:{receipt_color}; font-weight:bold; letter-spacing:5px; position:relative; margin-left:60px;'>영수증
				</span><span style=' font-size:12px; color:{receipt_color};'>({receipt_title}용)</span>
			</td>
		</tr>
		<tr>
			<td height='20px' width='96'  style='border-top:1px solid {receipt_color}; border-right:1px solid {receipt_color};'>
				<strong style='font-size:12px; color:{receipt_color}; margin-left:5px;'>no.</strong>
			</td>
			<td height='20px' align='right'>
				<span style='font-size:12px;'>".$order[bname]."</span><span style='margin:0px 5px 0px 20px; font-weight:bold; color:{receipt_color};'>귀하</span>
			</td>
		</tr>
		<tr>
			<td width='96' style='border-top:1px solid {receipt_color}; border-right:1px solid {receipt_color};' rowspan='4' >
				<table cellspacing='0' cellpadding='0' border='0' width='96'  style='font-size:12px; font-weight:bold; color:{receipt_color}; '>
					<tr>
						<td width='27' rowspan='4' align='center' style='border-right:1px solid {receipt_color}; '>
							공<br /><br />급<br /><br />자
						</td>
						<td align='center' style='border-bottom:1px solid {receipt_color};'>
							사&nbsp;업&nbsp;자<br />등록번호
						</td>
					</tr>
					<tr>
						<td align='center'  style='border-bottom:1px solid {receipt_color};' height='27px'>상&nbsp;호</td>
					</tr>
					<tr>
						<td align='center'  style='border-bottom:1px solid {receipt_color};'>사&nbsp;업&nbsp;자<br />소&nbsp;재&nbsp;지</td>
					</tr>
					<tr>
						<td align='center' height='26px'>업태</td>
					</tr>
				</table>
			</td>
			<td style='font-size:15px; color:#202020;  border-bottom:1px solid {receipt_color}; border-top:1px solid {receipt_color};' height='28px' align='center'>
				".$cominfo[com_number]."
			</td>
		</tr>
		<tr>
			<td align='center' style='  border-bottom:1px solid {receipt_color};'>
				<table cellspacing='0' cellpadding='0' border='0' width='100%'>
					<tr>
						<td width='101' style='font-size:12px;' align='center'>".$cominfo[com_name]."</td>
						<td height='27px'  width='39' style='font-weight:bold; color:{receipt_color}; font-size:12px;  border-left:1px solid {receipt_color}; border-right:1px solid {receipt_color};' align='center'>성명</td>
						<td width='102' style='font-size:12px;position:relative;' align='center'>
							".$cominfo[com_ceo]."
							<img src='http://".$_SERVER['HTTP_HOST']."/admin/images/company-stamp.png' alt='직인' style='width:40px;position:absolute;top:-7px;right:0px;' />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style='font-size:14px; color:#202020;  border-bottom:1px solid {receipt_color};' height='28px' align='center'>
				".$cominfo[com_addr]."
			</td>
		</tr>
		<tr>
			<td  height='25px' align='center' >
				<table cellspacing='0' cellpadding='0' border='0' width='100%'>
					<tr>
						<td width='101' style='font-size:12px;' align='center'>".$cominfo[com_business_status]."</td>
						<td  height='27px'  width='39' style='font-weight:bold; color:{receipt_color}; font-size:12px; border-left:1px solid {receipt_color}; border-right:1px solid {receipt_color};' align='center'>종목</td>
						<td width='102' style='font-size:12px;' align='center'>".$cominfo[com_business_category]."</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan='2' style='border-top:1px solid {receipt_color};'>
				<table cellspacing='0' cellpadding='0' border='0' width='100%'>
					<tr>
						<td align='center' width='125' style='font-size:12px; border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color}; color:{receipt_color}; font-weight:bold;' height='17px'>
							작성년 월 일
						</td>
						<td align='center' width='150' style='border:1px solid {receipt_color}; border-bottom:0px; font-size:12px; border-bottom:1px solid {receipt_color};'  height='17px'>
							공급대가총액
						</td>
						<td  align='center' style='font-size:12px; border-left:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};'  height='17px' >
							비 &nbsp;고
						</td>
					</tr>
					<tr>
						<td align='center' width='125'  height='17px' style='font-size:12px;  border-right:1px solid {receipt_color}; border-bottom:1px solid {receipt_color}; '>
							".$order[order_date]."
						</td>
						<td align='center'  width='150'  height='17px' style='border-left:1px solid {receipt_color}; border-right:1px solid {receipt_color}; font-size:12px; border-bottom:2px solid {receipt_color};'>
							".number_format($order_price[total_price])."
						</td>
						<td align='center'  height='17px' style='border-left:1px solid {receipt_color}; border-bottom:1px solid {receipt_color};'>
						
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan='2' align='center' height='20px'style='font-size:12px; font-weight:bold; color:{receipt_color};'>
				위 금액을 정히 영수(청수)함
			</td>
		</tr>
		<tr>
			<td colspan='2' style='border-top:1px solid {receipt_color};'>
				<table cellspacing='0' cellpadding='0' border='0' width='100%' style='font-size:12px; font-weight:bold; color:{receipt_color};'>
					<tr>
						<td align='center' height='18px' width='22px' style='border-right:1px solid {receipt_color};'>
							월
						</td>
						<td align='center' height='18px'  width='22px' style='border-right:1px solid {receipt_color};'>
							일
						</td>
						<td align='center' height='18px' width='156px' style='border-right:1px solid {receipt_color};'>
							품&nbsp;목
						</td>
						<td align='center' height='18px' width='39px' style='border-right:1px solid {receipt_color};'>
							수량
						</td>
						<td align='center' height='18px'  width='39px'style='border-right:1px solid {receipt_color};'>
							단가
						</td>
						<td align='center' height='18px'>
							금액
						</td>
					</tr>";

					if(count($order_dt)>0){
						foreach($order_dt as $od){
							$receipt.="
							<tr>
								<td height='18px' width='22px' style='border-right:1px solid {receipt_color}; border-top:1px solid {receipt_color};    font-weight:normal; color:#222;' align='center'>
									".$order[month]."
								</td>
								<td height='18px' width='22px' style='border-right:1px solid {receipt_color};  border-top:1px solid {receipt_color};    font-weight:normal; color:#222;' align='center'>
									".$order[day]."
								</td>
								<td height='18px' width='150px' style='border-right:1px solid {receipt_color};  border-top:1px solid {receipt_color};   font-weight:normal; color:#222;' align='center'>
									".strip_tags($od[pname])." ".(strip_tags($od[option_text]) ? "<br/>옵션 - ".strip_tags($od[option_text]) : "")."
								</td>
								<td height='18px' width='41px' style='border-right:1px solid {receipt_color};   font-weight:normal; color:#222; border-top:1px solid {receipt_color};' align='center'>
									".$od[pcnt]."
								</td>
								<td height='18px' width='41px' style='border-right:1px solid {receipt_color};   font-weight:normal; color:#222; border-top:1px solid {receipt_color};' align='center'>
									".number_format($od[price])."
								</td>
								<td height='18px'  width='41px' style=' border-top:1px solid {receipt_color};  font-weight:normal; color:#222;' align='center'>
									".number_format($od[ptprice])."
								</td>
							</tr>";
						}
					}

				$receipt.="
				</table>
			</td>
		</tr>";

		for($i=0;$i<(10-count($order_dt));$i++){
			if(count($order_dt) < 9 && $i==0){
				$receipt.="
				<tr>
					<td colspan='2' align='center' height='20px'style='font-size:12px; color:#222; border-top:1px solid {receipt_color};'>
						***&nbsp;이&nbsp;하&nbsp;여&nbsp;백&nbsp;***
					</td>
				</tr>";
			}else{
				$receipt.="
				<tr>
					<td colspan='2' style='border-top:1px solid {receipt_color};'>
						<table cellspacing='0' cellpadding='0' border='0' width='100%'>
							<tr>
								<td height='18px' width='22px' style='border-right:1px solid {receipt_color};    font-weight:normal; color:#222;' align='center'>
								</td>
								<td height='18px' width='22px' style='border-right:1px solid {receipt_color};    font-weight:normal; color:#222;' align='center'>
								</td>
								<td height='18px' width='150px' style='border-right:1px solid {receipt_color};   font-weight:normal; color:#222;' align='center'>
								</td>
								<td height='18px' width='41px' style='border-right:1px solid {receipt_color};   font-weight:normal; color:#222;' align='center'>
								</td>
								<td height='18px' width='41px' style='border-right:1px solid {receipt_color};   font-weight:normal; color:#222;' align='center'>
								</td>
								<td height='18px'  width='41px' style='  font-weight:normal; color:#222;' align='center'>
								</td>
							</tr>
						</table>
					</td>
				</tr>";
			}
		}
		
		$receipt.="
		<tr>
			<td colspan='2' align='center' height='20px'style='font-size:11px; color:#222; border-top:1px solid {receipt_color};'>
				부가가치세법시행규칙 제25조 규정에 의한(영수정)으로 개정.
			</td>
		</tr>
	</table>
</div>";



$Contents = "
<table cellspacing=0 cellpadding=0 width='100%' align=center border=0>
	<tr>
		<td>
			<div class='tab' style='width:100%;height:38px;margin:0px;'>
				<table class='s_org_tab' cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<td class='tab'>
						<table id='tab_01' class='on' >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"$('td.tab').find('table').removeClass('on');$('#tab_01').addClass('on');$('#seller').hide();$('#buyer').show();\" style='padding-left:20px;padding-right:20px;'>
								공급받는자
							</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_02'>
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"$('td.tab').find('table').removeClass('on');$('#tab_02').addClass('on');$('#seller').show();$('#buyer').hide();\" style='padding-left:20px;padding-right:20px;'>
								공급자
							</td>
							<th class='box_03'></th>
						</tr>
						</table>
					</td>
					<td class='btn'>
					</td>
				</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr id='buyer'>
		<td align=center>";
			
			$receipt_title = "공급받는자";
			$receipt_color = "blue";
			$buyer_mail_contents = str_replace("{receipt_title}",$receipt_title,$$view_type);
			$buyer_mail_contents = str_replace("{receipt_color}",$receipt_color,$buyer_mail_contents);
			$Contents .= $buyer_mail_contents;

		$Contents .="
		</td>
	</tr>
	<tr id='seller' style='display:none;'>
		<td align=center>";
			
			$receipt_title = "공급자";
			$receipt_color = "red";
			$seller_mail_contents = str_replace("{receipt_title}",$receipt_title,$$view_type);
			$seller_mail_contents = str_replace("{receipt_color}",$receipt_color,$seller_mail_contents);
			$Contents .= $seller_mail_contents;

		$Contents .="
		</td>
	</tr>";

	$Contents .="
	<tr>
		<td align=center height='50'>

			<input type='hidden' name='buyer' id='buyer_mail_contents' value=\"".$buyer_mail_contents."\">
			<input type='hidden' name='seller' id='seller_mail_contents' value=\"".$seller_mail_contents."\">

			<form name='receipt_mail_frm' method=post action='../order/receipt.act.php' onsubmit='return receipt_mail_submit(this)' target='act'>
				<input type='hidden' name='act' value='mail_send'>
				<input type='hidden' name='view_type' value='".$view_type."'>
				<input type='hidden' name='oid' value='".$oid."'>
				<input type='hidden' name='mail_contents' id='mail_contents' value=\"\">
				고객명 : <input type='text' name='user_name' class='textbox' value='".$order[bname]."' style='height:20px;width:60px;' > 
				이메일주소 : <input type='text' name='user_mail' class='textbox' value='".$order[bmail]."' style='height:20px;width:120px;' >
				<input type='image' src='/admin/images/".$admininfo["language"]."/btn_send_mail_01.png' border='0' align='absmiddle'>
			</form>
			<img src='../images/".$admininfo["language"]."/btn_print.gif' style='cursor:pointer' align='absmiddle' onclick=\"javascript:content_print();\"/>
		</td>
	</tr>
</table>";


$Script="
<script type='text/javascript'>
<!--
	function receipt_mail_submit(frm){
		if(confirm('메일을 보내시겠습니까?')){
			if($('#buyer').is(':visible')){
				$('#mail_contents').val($('#buyer_mail_contents').val());
			}else{
				$('#mail_contents').val($('#seller_mail_contents').val());
			}
			return true;
		}else{
			return false;
		}
	}
//-->
</script>

<script type='text/javascript'>

function content_print(){
    
	var initBody = document.body.innerHTML;
	window.onbeforeprint = function(){
		document.body.innerHTML = document.getElementById('idPrint').innerHTML;
	}
	window.onafterprint = function(){
		document.body.innerHTML = initBody;
	}
	window.print();     
}            
</script>
";


$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "주문관리 > 증빙서 > 영수증";
$P->NaviTitle = "영수증";
$P->title = "영수증";
$P->strContents = $Contents;
echo $P->PrintLayOut();


