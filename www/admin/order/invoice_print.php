<?
include_once("../class/layout.class");

$db = new Database;
$ddb = new Database;

$company_id_array = split(",",$company_id);
//print_r($_order_info);

for($o_i=0;$o_i < 1;$o_i++){

if($mmode == "print"){
	$width_str = "style='width:350px;'";
}else{
	$width_str = "style='width:600px;'";
}


$sql = "SELECT o.*, od.* , ccd.com_addr1, ccd.com_addr2 
				FROM  ".TBL_SHOP_ORDER." o , ".TBL_SHOP_ORDER_DETAIL." od left join common_company_detail ccd on od.company_id = ccd.company_id
				where o.oid = od.oid and od.oid = '".$_order_info[$o_i][oid]."'  ";


//echo $sql;
$db->query($sql);
$db->fetch();
$order_infos = $db->dt;

/*
$sql = "SELECT code as TERMINAL_CODE FROM NEW_E2A_DAEHANTERMINAL_INFO WHERE ZIP =TRIM('".str_replace('-','',$order_infos[zip])."') AND ROWNUM <= 1 ";
//echo $sql;
$db->query($sql);
$db->fetch();
*/
$terminal_code = $db->dt[terminal_code];

$split_terminal_code = explode("-",$terminal_code);


$Contents = "<div id='print_area' ".$width_str.">
		<table cellpadding='0' cellspacing='0' border='0' width=100%>
			<col width='65%'>
			<col width='35%'>
			<tr>
				<td height='25'>
					<b class='middle_title'>From</b>
				</td>
				<td height='25' align='right'>
					<b class='middle_title'>Date</b>
				</td>
			</tr>
			<tr>
				<td height='25'>
					".$order_infos[company_name]."<br>
					".$order_infos[com_addr1]."<br>
					".$order_infos[com_addr2]."<br>
					".$order_infos[company_id]."
				</td>
				<td height='25' align='right' nowrap>
					".date("Y-m-d")."<br>
					<b>Order</b> : ".$order_infos[oid]."
				</td>
			</tr>
			<tr>
				<td align=right colspan='2'>
					<b class='' style='font-size:14px;'>NjoyNY(엔조이뉴욕)<br/>
					www.njoyny.com</b>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<b class='middle_title'>To :</b> ".$order_infos[rname]."
				</td>
			</tr>
			<tr>
				<td style='padding-bottom:3px;' colspan='2'>
					".$order_infos[addr]."
				</td>
			</tr>
		</table>
		<table border='0' width='100%' cellspacing='0' cellpadding='0'>
			<col width='10%'>
			<col width='*'>
			<col width='70%'>
			<tr>
				<td ><b class='middle_title'>ITEM : </b></td>
				<td colspan='2'> ".$order_infos[pname]." </td>
			</tr>
			<tr>
				<td height='25' colspan='2'>
					<b class='middle_title'>Pieces</b>
				</td>
				<td height='25' >
					<b class='middle_title'>Weight</b>
				</td>
			</tr>
			<tr>
				<td height='25' align=left colspan='2'>
					<b class='middle_title'>&nbsp;&nbsp;".$order_infos[pcnt]."</b>
				</td>
				<td height='25' align=left >
					<b class='middle_title'>&nbsp;&nbsp;1</b>
				</td>
			</tr>
		</table>
		<table cellpadding='0' cellspacing='0' border='0' width='100%'>
			<col width='*'>
			<col width='50%'>

			<tr>
				<td height='25'>
					<b class='middle_title'>Origin</b>
				</td>
				<td height='25'>
					<b class='middle_title'>Dest</b>
				</td>
			</tr>
			<tr>
				<td height='25' align=center>
					<b class='middle_title' style='font-size:17px;font-family:import'>".$order_infos[airport]."</b>
				</td>
				<td height='25' align=center>
					<b class='middle_title' style='font-size:17px;'>ICN</b>
				</td>
			</tr>
			<tr>
				<td height='25'>
					<b class='middle_title'>Code of Departure</b>
				</td>
				<td height='25' align=center>
					<b class='middle_title'>Code of Arrival</b>
				</td>
			</tr>
			<tr>
				<td height='25' nowrap>
					<b class='middle_title' style='font-size:17px;'>4W29-1</b> <b style='font-size:25px;'>".substr($terminal_code,0,1)."</b> <b  style='font-size:28px;'>".substr($terminal_code,1,4)."</b> <b style='font-size:25px;'>".substr($terminal_code,5,1)."</b>
				</td>
				<td height='25'  align='lfet'>
					<b class='middle_title'><img src='/include/barcode/make_barcode.php?barcode=".$split_terminal_code[0]."' title='".$split_terminal_code[0]."' style='' ></b>
				</td>
			</tr>
			<tr>
				<td height='25'>
					<b class='middle_title'>Bill No.</b> <span class='middle_title'>".$order_infos[invoice_no]."</span>
				</td>
			</tr>
			<tr>
				<td height='25' colspan=2 align='center'>
					 <img src='/include/barcode/make_barcode.php?barcode=".$order_infos[invoice_no]."' title='".$order_infos[invoice_no]."'style='margin:10px 0px 0px 0px;'>
				</td>
			</tr>
		</table>
		<br>";
if($mmode == "print"){
$Contents .= "
		<div style='width:350px;line-height:110%;font-size:11px;'>
		<b>Shipper's Agreement</b><br>
		Unless ottherwise agreed in writing,l/we agree that KE'S Terms and coditions of carriage are the terms of the contract between me/use and KE, This shipment does not contain cash or dangerous goods.
		</div>
		<br>";
}
$Contents .= "
<div style='padding:7px 0px;text-align:center;'>
<b class='middle_title'>RECEIPT</b>
</div>
<table border='0' width='100%' cellspacing='0' cellpadding='0'>
			<col width='30%'>
			<col width='35%'>
			<col width='35%'>
			<tr>
				<td height='25'><b>NAME : </b></td>
				<td > ".$order_infos[rname]." </td>
				<td > ".$order_infos[rmobile]." </td>
			</tr>
			<tr>
				<td height='25'><b>Address : </b></td>
				<td colspan=2> ".$order_infos[addr]." </td>
			</tr>
			<tr>
				<td height='25' style='vertical-align:top;' ><b>BL : </b>".$order_infos[invoice_no]."</td>
				<td colspan=2> <img src='/include/barcode/make_barcode.php?barcode=".$order_infos[invoice_no]."' title='".$order_infos[invoice_no]."' style=''> </td>
			</tr>
		</table>

						<table border='0' width='100%'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0'>
										<tr height='25' align=center>
											<td width='*' align='left'><b>ITEM</b></td>
											<td width='30%' align='left'><b>Description</b></td>
											<td width='15%' ><b>Qty</b></td>
											<td width='15%'  ><b>Unit Price</b></td>
											<td width='15%' ><b>Amount</b></td>
										</tr>";



	if($admininfo[admin_level] == 9){
			if($admininfo[mem_type] == "MD"){
				$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}

			if(is_array($type)){

				if($type_str != ""){
					$addWhere = "and od.status in ($type_str) ";
				}
			}else{
				if($type){
					$addWhere = "and od.status = '$type' ";
				}
			}
			$sql = "SELECT o.oid, od.od_ix,od.pname, od.option_text, od.regdate, od.psprice, od.option_price, od.coprice, od.pcnt, od.ptprice,od.commission, uid,company_id,od.delivery_price,
o.delivery_price as pay_delivery_price,  com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,
tid, od.status, total_price, receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and od.oid = '".$_order_info[$o_i][oid]."'  and od.product_type NOT IN (".implode(',',$sns_product_type).")
						$addWhere

						 "; //ORDER BY company_id DESC  //ORDER BY company_id DESC, od.status DESC


		}else if($admininfo[admin_level] == 8){
			if(is_array($type)){
				if($type_str != ""){
					$addWhere = "and od.status in ($type_str) ";
				}
			}else{
				if($type){
					$addWhere = "and od.status = '$type' ";
				}
			}

			$sql = "SELECT o.oid, od.od_ix,od.pname, od.option_text, od.regdate, od.psprice, od.option_price, od.coprice, od.pcnt, od.ptprice,od.commission, uid,company_id,od.delivery_price,
						o.delivery_price as pay_delivery_price, com_name,od.pid, rname, bname, mem_group,o.use_reserve_price,
						tid, od.status,  total_price, receipt_y, od.company_name, od.company_id,od.quick, od.invoice_no
						FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od
						where o.oid = od.oid and od.oid = '".$_order_info[$o_i][oid]."'  and od.company_id ='".$admininfo[company_id]."' and od.product_type NOT IN (".implode(',',$sns_product_type).")
						$addWhere
						 ";//ORDER BY company_id DESC, od.status DESC
		}
		//echo nl2br($sql);
		$ddb->query($sql);
	$num = 1;
	for($j = 0; $j < $ddb->total; $j++)
	{
		$ddb->fetch($j);



		$pname = $ddb->dt[pname];
		$pcode = $ddb->dt[pcode];
		$count = $ddb->dt[pcnt];
		$option_div = $ddb->dt[option_text];
		$option_etc1 = $ddb->dt[option_etc];
		$option_price = $ddb->dt[option_price];
		$price = $ddb->dt[coprice]+$ddb->dt[option_price];
		$coprice = $ddb->dt[coprice];
		$sumptprice = $sumptprice + $ddb->dt[ptprice];


		$reserve = $ddb->dt[reserve];
		$ptotal = $price * $count;
		$sum += $ptotal;
		$arr_sns_ptype=array("4","5","6");
		$Contents .= "
										<tr height='30' align='center'>
											<td style='text-align:left' colspan=2>
											".$pname."<br/>
											".$option_div."".($option_price != '' ? " + ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($option_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."":"")."
											</td>
											<td >".$count." </td>

											";
		$Contents .= "				<td ><div align='center'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</div></td>
											<td ><div align='center'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ptotal)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</div></td>";


$Contents .= "
										</tr>
										<!--tr><td colspan=11 class=dot-x></td></tr-->";

		$num++;
	}
$Contents .="
									</table>
								</td>
							</tr>
						</table>";

	if($o_i != count($_order_info)-1){
		$Contents .= "<P CLASS=\"breakhere\"></P>";
	}
}

$Contents .= "</div><!--print_area 끝-->
<object id='factory' style='display:none' viewastext classid='clsid:1663ed61-23eb-11d2-b92f-008048fdd814'
codebase='http://".$_SERVER["HTTP_HOST"]."/admin/order/scriptx/smsx.cab#Version=7,1,0,60'>
</object>";

$Script = "<style type = \"text/css\">
	body	{ margin: 0px; padding: 0px; width:350px;}
	P.breakhere {page-break-before: always}

</style>
<script language='javascript'>

	var initBody ;

	function beforePrint() {
		initBody = document.body.innerHTML; document.body.innerHTML = document.getElementById('print_area').innerHTML;
		//alert(document.body.innerHTML);
	}

	function afterPrint() {
		document.body.innerHTML = initBody;
	}

	function printArea() {";

	if($mmode == "print"){
		$Script .= "	window.focus(); window.print();";
	}else{
		$Script .= "	window.print();";
	}
	$Script .= "
	}

	window.onbeforeprint = beforePrint;
	window.onafterprint = afterPrint;";

	if($mmode == "print"){
	$Script .= "
	$(document).ready(function() {
		//printArea();
		printPage();
	});";
	}

	$Script .= "

	function printPage() {
		//alert(1);

		factory.printing.header = ''; // Header에 들어갈 문장
		factory.printing.footer = ''; // Footer에 들어갈 문장
		factory.printing.portrait = true // true 면 세로인쇄, false 면 가로인쇄
		factory.printing.leftMargin = 0.2 // 왼쪽 여백 사이즈
		factory.printing.topMargin = 0.2 // 위 여백 사이즈
		factory.printing.rightMargin = 0.2 // 오른쪽 여백 사이즈
		factory.printing.bottomMargin = 0.2 // 아래 여백 사이즈
		factory.printing.Print(false,window) // 출력하기

	}

</script>
";

if($mmode == "print"){
	$P = new ManagePopLayOut();
	$P->strLeftMenu = order_menu();
	$P->addScript = $Script."\n<script language='javascript' src='orders.js'></script>";
	$P->Navigation = "HOME > 주문관리 > 주문내역확인";
	$P->NaviTitle = "주문내역확인";
	$P->strContents = $Contents;
	$P->layout_display = false;

	echo $P->PrintLayOut();
}else if($mmode == "pop"){

	$P = new ManagePopLayOut();
	$P->strLeftMenu = order_menu();
	$P->addScript = $Script."\n<script language='javascript' src='orders.js'></script>";
	$P->Navigation = "HOME > 주문관리 > 주문내역확인";
	$P->NaviTitle = "주문내역확인";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
}else{

	$P = new LayOut();
	$P->strLeftMenu = order_menu();
	$P->addScript = $Script."\n<script language='javascript' src='orders.js'></script>";
	$P->Navigation = "HOME > 주문관리 > 주문내역확인";
	$P->title = "주문내역확인";
	$P->strContents = $Contents;


	echo $P->PrintLayOut();
}

?>