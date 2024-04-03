<?

///////////////// CREATE EXCEL FILE METHOD /////////////////
function xlsBOF() {
echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
return;
}
function xlsEOF() {
echo pack("ss", 0x0A, 0x00);
return;
}
function xlsWriteNumber($Row, $Col, $Value) {
echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
echo pack("d", $Value);
return;
}
function xlsWriteLabel($Row, $Col, $Value, $lang='' ) {
$lang = ($lang)? $lang:'euc-kr';
$Value = mb_convert_encoding($Value,$lang,"utf-8");
$L = strlen($Value);
echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
echo $Value;
return;
}
///////////////////// END //////////////////////////
include("../class/layout.class");
include("../order/excel_out_columsinfo.php");

header('Pragma: public');
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
header('Content-Type: text/plain; charset=UTF-8');
header('Content-Transfer-Encoding: binary');
header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
header("Content-type: application/x-msexcel");                    // This should work for the rest
header('Content-Disposition: attachment; filename='.iconv("utf-8","CP949","주문리스트").'_'.date("Ymd").'.xls');

/*
include("../class/layout.class");
include("../order/excel_out_columsinfo.php");

header( "Content-type: application/vnd.ms-excel" );
header( "Content-Disposition: attachment; filename=order_list.xls" );
header( "Content-Description: Generated Data" );
*/
$db1 = new Database;
$odb = new Database;

$sql = "select order_excel_info1, order_excel_info2, order_excel_checked from ".TBL_COMMON_SELLER_DETAIL."	where company_id = '".$admininfo[company_id]."'";


$db1->query($sql);
$db1->fetch();
//echo $sql;
$check_colums = unserialize(stripslashes($db1->dt[order_excel_checked]));
$columsinfo = $colums;

//print_r($columsinfo);
//exit;
$str_colums = implode(",", $check_colums);
//echo $str_colums;
//exit;

	$where = "WHERE od.status <> '' ";

	if($search_type != "" && $search_text != ""){
		if($search_type == "combi_name"){
			$where .= "and (bname LIKE '%".trim($search_text)."%'  or rname LIKE '%".trim($search_text)."%' or bank_input_name LIKE '%".trim($search_text)."%') ";
		}else{
			$where .= "and $search_type like '%$search_text%'";
		}
	}

	if ($vFromYY != "")	{
		$startDate = $vFromYY.$vFromMM.$vFromDD;
		$endDate = $vToYY.$vToMM.$vToDD;

		$where .= "and date_format(date,'%Y%m%d') between $startDate and $endDate ";
	}

	if(is_array($type)){ //

		for($i=0;$i < count($type);$i++){

			if($type_str == ""){
				$type_str .= "'".$type[$i]."'";
			}else{
				$type_str .= ",'".$type[$i]."'";
			}

		}

		if($type_str){
			$where .= "and od.status in ($type_str) ";
		}
	}else{
		//$status = getOrderStatus($type)
		if($type){
			$where .= "and od.status = '$type'";
		}

	}

	if($admininfo[admin_level] == 9){
		if($company_id != ""){
			$where .= " and o.oid = od.oid and od.company_id = '".$company_id."'";//od.pid = p.id and
		}else{
			$where .= " and o.oid = od.oid  "; //and  od.pid = p.id
		}
	}else if($admininfo[admin_level] == 8){
		$where .= " and o.oid = od.oid and od.company_id = '".$admininfo[company_id]."'"; // od.pid = p.id and
	}
	//$admininfo[company_id]

//echo ("SELECT * FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where  ORDER BY date DESC");
$db1->query("select oid from (SELECT od.oid, od.pid, od.regdate FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od $where ) ood left join ".TBL_SHOP_PRODUCT." p on ood.pid = p.id WHERE p.product_type NOT IN ('4','5','6') ");		//,

$total = $db1->total;
//echo ("SELECT o.oid, uid, o.bname, o.rname, tid, o.status, method, total_price, payment_price, date, p.pname, addr, zip, msg, rtel, rmobile FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od $where GROUP BY o.oid ORDER BY date DESC ");
/*
$sql = "select  p.pcode, ood.*  from
				(SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, o.rname, tid,  method,rmail,bmail, total_price, payment_price, date, od.pname, addr, zip, msg, rtel, rmobile,
				od.pid, od.company_name, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,  od.quick, od.invoice_no as invoiceno , od.dc_date,(select delivery_price from shop_order_delivery where o.oid = oid and od.company_id = company_id) as deliveryprice,
				(select delivery_pay_type from shop_order_delivery where o.oid = oid and od.company_id = company_id) as deliverypaytype
				FROM ".TBL_SHOP_ORDER." o, ".TBL_SHOP_ORDER_DETAIL." od $where ) ood left join ".TBL_SHOP_PRODUCT." p on ood.pid = p.id AND p.product_type NOT IN ('4','5','6') ORDER BY date DESC "; //
*/
$sql = "SELECT o.oid, uid, o.bname,o.btel, o.bmobile,od.surtax_yorn, o.rname, tid,  method,rmail,bmail, total_price, payment_price, date, od.pcode, od.pname,
			o.addr, o.zip, o.msg, o.rtel, o.rmobile, od.pid, od.company_name, od.option_text as optiontext,od.coprice, od.psprice,od.pcnt,od.ptprice,od.status,
			od.quick, od.invoice_no as invoiceno , od.dc_date, odd.delivery_price as deliveryprice, odd.delivery_pay_type as deliverypaytype
			FROM ".TBL_MALLSTORY_ORDER." o
			left join ".TBL_MALLSTORY_ORDER_DETAIL." od on o.oid=od.oid
			left join mallstory_order_delivery odd on o.oid = odd.oid and od.company_id = odd.company_id
			$where  ORDER BY date DESC ";
$db1->query($sql);

if($db1->total){

xlsBOF();
	$j=0;

	foreach($check_colums as $key => $value){
		xlsWriteLabel(0,$j,$columsinfo[$value][title]);
		$j++;
		/*
		if(!$mstring_line){
			$mstring_line = $columsinfo[$value][title];

		}else{
			$mstring_line .= "\t".$columsinfo[$value][title];
		}*/
	}
	//$mstring .= $mstring_line."\n";

	//$mstring_line = "주문번호\t사업자명\t상품코드\t상품명\t과세/면세\t옵션\t주문일\t회원그룹\t주문자명\t연락처1\t연락처2\t받는자\t우편번호\t수취인주소\t연락처1\t연락처2\t판매가\t공급가\t수량\t배송료\t포장비\t상태\t증빙서\t배송완료일\t택배사명\t송장번호\t메모\n";
	for ($i=0,$z=0; $i < $db1->total; $i++)
	{
		$db1->fetch($i);

		for($x=0;$x < $db1->dt[pcnt];$x++,$z++){
			$status = getOrderStatus($db1->dt[status]);

			if ($db1->dt[method] == "1")
			{
				if($db1->dt[bank] == ""){
					$method = "카드결제";
				}else{
					$method = $db1->dt[bank];
				}
			}elseif($db1->dt[method] == "0"){
				$method = "계좌입금";
			}elseif($db1->dt[method] == "2"){
				$method = "전화결제";
			}
			if($db1->dt[surtax_yorn] == "Y"){
				$surtax_yorn = "면세";
			}else{
				$surtax_yorn = "과세";
			}

			$psum = number_format($db1->dt[total_price]);



			if($db1->dt[receipt_y] == "Y"){
				$receipt_y = "발행";
			}else{
				$receipt_y = "미발행";
			}


			$j=0;
			foreach($check_colums as $key => $value){
				//echo $value;
				if($value == "status"){
					$value_str = strip_tags(getOrderStatus($db1->dt[$value]));
				}else if($value == "quick"){
					$value_str = deliveryCompanyList($db1->dt[$value],"excel_text");
				}else if($value == "method"){
					if ($db1->dt[$value] == "1")
					{
						if($db1->dt[$value] == ""){
							$value_str = "카드결제";
						}
					}elseif($db1->dt[$value] == "0"){
						$value_str = "계좌입금";
					}elseif($db1->dt[$value] == "2"){
						$value_str = "전화결제";
					}
					//$value_str = deliveryCompanyList($db1->dt[$value],"excel_text");
				}else if($value == "deliverypaytype"){
					if($db1->dt[$value] == "1"){
						$value_str = "선불";
					}elseif($db1->dt[$value] == "2"){
						$value_str = "착불";
					}else{
						$value_str = "무료";
					}
				}else if($value == "deliverypayuse"){
					if($db1->dt[deliverypaytype] == "1" || $db1->dt[deliverypaytype] == "2"){
						$value_str = "구매자";
					}else{
						$value_str = "판매자";
					}
				}else{
					if($value == "pcnt"){
						$pcnt = $db1->dt[$value];
						$value_str = 1;
					}else{
						if($value == "optiontext"){
							$value_str = str_replace(array("color :","COLOR :"),"",$db1->dt[$value]);
							$value_str = str_replace(array("size :","SIZE :"),"",$value_str);
							$value_str = strip_tags($value_str);
						}else{
							$value_str = $db1->dt[$value];
						}
					}
				}
				if(is_numeric($value_str) && $value != "invoiceno"){
					xlsWriteNumber(($z+1),$j,$value_str);
				}else{
					xlsWriteLabel(($z+1),$j,$value_str);
				}
				/*
				if($j==0){
					$mstring_line = $value_str;
				}else{

					$mstring_line .= "\t".$value_str;
				}
				*/
				$j++;
			}

		}
		//echo $pcnt;
		/*
		for($x=0;$x < $pcnt;$x++){
		$mstring .= $mstring_line."\n";
		}
		*/

		//$mstring .=  $db1->dt[oid]."\t".$db1->dt[company_name]."\t".$db1->dt[pid]."\t".$db1->dt[pname]."\t".$surtax_yorn."\t".strip_tags($db1->dt[option_text])."\t".$db1->dt[date]."\t".$gp_name."\t".$db1->dt[bname]."\t".$db1->dt[btel]."\t".$db1->dt[bmobile]."\t".$db1->dt[rname]."\t".$db1->dt[zip]."\t".$db1->dt[addr]."\t".$db1->dt[rtel]."\t".$db1->dt[rmobile]."\t".$db1->dt[psprice]."\t".$db1->dt[coprice]."\t".$db1->dt[pcnt]."\t".$db1->dt[delivery_price]."\t".$pack_method."\t".strip_tags($status)."\t".$receipt_y."\t".$db1->dt[dc_date]."\t".codeName($db1->dt[quick])."\t".$db1->dt[invoice_no]."\t".$db1->dt[msg]."\n";
	}
}
xlsEOF();
//echo iconv("utf-8","CP949",$mstring);

?>
