<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

$db = new Database;
$db->query("SELECT return_reason,return_reason_detail,status FROM shop_order_detail  where od_ix = '$od_ix'");	
$db->fetch();
	
$Script = "";

$Contents = "
		<table border='0' width='100%' cellpadding='0' cellspacing='1' align='center'>	
			
			<tr>				
				<td align=center style='padding: 0 10 0 10'>
				<table border='0' width='100%' cellspacing='0' cellpadding='5' >
					<tr>
						<td >	
						<table border='0' width='100%' cellspacing='1' cellpadding='0' style='border:5px solid #F8F9FA'>
							<tr>
								<td >
									<table border='0' width='100%' cellspacing='1' cellpadding='4' class='input_table_box' >
										<tr>
											<td class='input_box_title' width=90 align='left' >  사유</td>
											<td class='input_box_item'>&nbsp;";
											
											if($db->dt[return_reason] == "1"){
												$Contents .=  "불량";
											}else if($db->dt[return_reason] == "2"){
												$Contents .=  "오배송";
											}else if($db->dt[return_reason] == "3"){
												$Contents .=  "기타";
											}else if($db->dt[return_reason] == "4"){
												$Contents .=  "고객변심";
											}else if($db->dt[return_reason] == "5"){
												$Contents .=  "품절";
											}else if($db->dt[return_reason] == "6"){
												$Contents .=  "배송지연";
											}else if($db->dt[return_reason] == "7"){
												$Contents .=  "이중주문";
											}else if($db->dt[return_reason] == "8"){
												$Contents .=  "시스템오류";
											}else if($db->dt[return_reason] == "9"){
												$Contents .=  "누락";
											}else if($db->dt[return_reason] == "10"){
												$Contents .=  "택배분실";
											}else if($db->dt[return_reason] == "11"){
												$Contents .=  "상품불량";
											}else{
												$Contents .=  "불량";
											}
$Contents .=  "
											</td>
										</tr>
										<tr>
											<td class='input_box_title' width=90 align='left' >  자세한내용</td>
											<td class='input_box_item'>&nbsp;".$db->dt[return_reason_detail]."</td>
										</tr>
									</table>
								</td>
							</tr>
						</table><br>
			</td>
			
  		</tr>
  		</table>
		
				
		</td>
	</tr>
	
</TABLE>";



$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "주문관리 > 반품 및 교환사유";
$P->NaviTitle = "반품 및 교환사유";
$P->strContents = $Contents;
echo $P->PrintLayOut();




