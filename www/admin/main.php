<?
header("Location:/admin/admin.php");
exit;
include("./class/layout.class");

function showdate2($date)
{
	$date = date("Y년 n월 j일 a g시 i분", $date);
	$date = str_replace("am","오전",$date);
	$date = str_replace("pm","오후",$date);
	$date = ereg_replace("0([0-9]분)","\\1", $date);

	return $date;
}

$db1 = new Database;
$db2 = new Database;
$db3 = new Database;



$ctgr ="orders";

$Contents = "

<br>

<script language='javascript' src='"."tglib_orders.php?page=$page&ctgr=$ctgr&qstr=$qstr"."'></script>


<table width='660' border='0' cellpadding='0' cellspacing='0' align='center'>
  <tr height='20' bgcolor='#CCCCCC'>
    <td width='110' align='center'><font color='#000000'><b>주문번호</b></font></td>
    <td width='70' align='center'><font color='#000000'><b>이름</b></font></td>
    <td width='65' align='center'><font color='#000000'><b>결제방법</b></font></td>
	<td width='180' align='center'><font color='#000000'><b>주문일자</b></font></td>
    <td width='90' align='right'><font color='#000000'><b>주문총액</b></font></td>
    <td width='65' align='center'><font color='#000000'><b>처리상태</b></font></td>    
  </tr>";



	$max = 15; //페이지당 갯수

	if ($page == '')
	{
		$start = 0;
		$page  = 1;
	}
	else
	{
		$start = ($page - 1) * $max;
	}
	
	function page_bar2($total, $page, $max)
	{
		global $ctgr, $qstr;

		if ($total % $max > 0)
		{
			$total_page = floor($total / $max) + 1;
		}
		else
		{
			$total_page = floor($total / $max);
		}

		$next = $page + 1;
		$prev = $page - 1;

		if ($total)
		{
			$prev_mark = ($prev > 0) ? "<a href=orders.php?page=$prev&ctgr=$ctgr&qstr=$qstr&admincode=$admincode>◀</a> " : "◁ ";
			$next_mark = ($next <= $total_page) ? " <a href=orders.php?page=$next&ctgr=$ctgr&qstr=$qstr&admincode=$admincode>▶</a>" : " ▷";
		}

		$mstring = $mstring.$prev_mark;

		for ($i = $page - 5; $i <= $page + 5; $i++)
		{
			if ($i > 0)
			{
				if ($i <= $total_page)
				{
					if ($i != $page)
					{
						$mstring = $mstring." <a href=orders.php?page=$i&ctgr=$ctgr&qstr=$qstr&admincode=$admincode>$i</a> ";
					}
					else
					{
						$mstring = $mstring."<font color=#FF0000>$i</font>";
					}
				}
			}
		}

		$mstring = $mstring.$next_mark;
		
		return $mstring;
	}
	if($admininfo[admin_level] == 9){
		if($admincode == ""){
			if ($type == "o")	$where = "WHERE stats='1'";
			if ($type == "x")	$where = "WHERE stats='0'";
			if ($type == "oc")	$where = "WHERE stats='2'";
			if ($type == "ob")	$where = "WHERE stats='3'";
			if ($type == "r1")	$where = "WHERE stats='6'";
			if ($type == "r2")	$where = "WHERE stats='7'";
			if ($type == "xx")	$where = "WHERE stats='9'";
		}else{
			if ($type == "o")	$where = "WHERE o.pid = p.id and stats='1' and p.admin = '".$admincode."'";
			if ($type == "x")	$where = "WHERE o.pid = p.id and stats='0' and p.admin = '".$admincode."'";
			if ($type == "oc")	$where = "WHERE o.pid = p.id and stats='2' and p.admin = '".$admincode."'";
			if ($type == "ob")	$where = "WHERE o.pid = p.id and stats='3' and p.admin = '".$admincode."'";
			if ($type == "r1")	$where = "WHERE o.pid = p.id and stats='6' and p.admin = '".$admincode."'";
			if ($type == "r2")	$where = "WHERE o.pid = p.id and stats='7' and p.admin = '".$admincode."'";
			if ($type == "xx")	$where = "WHERE o.pid = p.id and stats='9' and p.admin = '".$admincode."'";
		}
	}else if($admininfo[admin_level] == 8){		
			if ($type == "o")	$where = "WHERE o.pid = p.id and stats='1' and p.admin = '".$admininfo[company_id]."'";
			if ($type == "x")	$where = "WHERE o.pid = p.id and stats='0' and p.admin = '".$admininfo[company_id]."'";
			if ($type == "oc")	$where = "WHERE o.pid = p.id and stats='2' and p.admin = '".$admininfo[company_id]."'";
			if ($type == "ob")	$where = "WHERE o.pid = p.id and stats='3' and p.admin = '".$admininfo[company_id]."'";
			if ($type == "r1")	$where = "WHERE o.pid = p.id and stats='6' and p.admin = '".$admininfo[company_id]."'";
			if ($type == "r2")	$where = "WHERE o.pid = p.id and stats='7' and p.admin = '".$admininfo[company_id]."'";
			if ($type == "xx")	$where = "WHERE o.pid = p.id and stats='9' and p.admin = '".$admininfo[company_id]."'";
	}
	//$admininfo[company_id]
	
	if ($qstr != ""){
		unset($where);
		if($admininfo[admin_level] == 9){
			if($admincode == ""){
				$where = "WHERE $ctgr LIKE '%$qstr%'";
			}else{
				$where = "WHERE o.pid = p.id and $ctgr LIKE '%$qstr%' and p.admin = '".$admincode."'";
			}			
		}else if($admininfo[admin_level] == 8){
			$where = "WHERE o.pid = p.id and $ctgr LIKE '%$qstr%' and p.admin = '".$admininfo[company_id]."' and stats <> '0' and stats <> '9'";
		}
	}else{
		if($admininfo[admin_level] == 9){
			if($admincode != ""){
				$where = "WHERE o.pid = p.id and p.admin = '".$admincode."'";
			}
		}else if($admininfo[admin_level] == 8){
			if($type == ""){
				$where = "WHERE o.pid = p.id and p.admin = '".$admininfo[company_id]."' and stats <> '0' and stats <> '9'";
			}
		}
	}

	if($admininfo[admin_level] == 9){
		if($admincode == ""){
			$db1->query("SELECT * FROM orders $where GROUP BY oid ORDER BY date DESC");
			//echo("SELECT * FROM orders $where GROUP BY oid ORDER BY date DESC");
			
			$total = $db1->total;	
			$db1->query("SELECT oid, uid, tid, stats, method FROM orders $where GROUP BY oid ORDER BY date DESC LIMIT $start, $max");
			//echo("SELECT oid, uid, tid, stats, method FROM orders $where GROUP BY oid ORDER BY date DESC LIMIT $start, $max");
		}else{
			$db1->query("SELECT * FROM orders o, product p $where GROUP BY oid ORDER BY date DESC");			
		//	echo("SELECT * FROM orders o, product p $where GROUP BY oid ORDER BY date DESC");
			$total = $db1->total;	
			$db1->query("SELECT oid, uid, tid, stats, method FROM orders o, product p $where GROUP BY oid ORDER BY date DESC LIMIT $start, $max");
		}
	}else if($admininfo[admin_level] == 8){
		$db1->query("SELECT * FROM orders o, product p $where GROUP BY oid ORDER BY date DESC");
	//	echo("SELECT * FROM orders o, product p $where GROUP BY oid ORDER BY date DESC");
		$total = $db1->total;	
		$db1->query("SELECT oid, uid, tid, stats, method FROM orders o, product p $where GROUP BY oid ORDER BY date DESC LIMIT $start, $max");
	}
	
	
	
	for ($i = 0; $i < $db1->total; $i++)
	{
		$db1->fetch($i);
		if($admininfo[admin_level] == 9){
			
			if($admincode == ""){
				$db2->query("SELECT oid, uid, btel,bmobile,rmobile,bname, bmail, rname, rtel, rmail, zip, addr, msg, return_message,return_date, UNIX_TIMESTAMP(date) AS date, method, bank, tid, authcode, stats, quick, deliverycode, p.coprice, (sum(o.psprice*o.pcnt) - sum(o.ptprice)) as reserve, c.company_name as company_name FROM orders o, product p, companyinfo c WHERE o.pid = p.id and o.oid = '".$db1->dt[oid]."' and c.company_id = p.admin GROUP by oid");
				
				//echo $db1->dt[oid]."<br>";
				if($i == 0){
				//	echo("SELECT oid, uid, btel,bmobile,rmobile,bname, bmail, rname, rtel, rmail, zip, addr, msg, return_message,return_date, UNIX_TIMESTAMP(date) AS date, method, bank, tid, authcode, stats, quick, deliverycode, p.coprice, (sum(o.psprice*o.pcnt) - sum(o.ptprice)) as reserve, c.company_name as company_name FROM orders o, product p, companyinfo c WHERE o.pid = p.id and o.oid = '".$db1->dt[oid]."' and c.company_id = p.admin GROUP by oid<br>");
				}
				
			}else{
				$db2->query("SELECT oid, uid, btel,bmobile,rmobile, bname, bmail, rname, rtel, rmail, zip, addr, msg, return_message,return_date, UNIX_TIMESTAMP(date) AS date, method, bank, tid, authcode, stats, quick, deliverycode, p.coprice, (sum(o.psprice*o.pcnt) - sum(o.ptprice)) as reserve, c.company_name as company_name   FROM orders o, product p, companyinfo c WHERE o.pid = p.id and oid = '".$db1->dt[oid]."' and p.admin = '".$admincode."' and c.company_id = p.admin GROUP by oid");
			}
		}else{
			$db2->query("SELECT oid, uid, btel,bmobile,rmobile, bname, bmail, rname, rtel, rmail, zip, addr, msg, return_message,return_date, UNIX_TIMESTAMP(date) AS date, method, bank, tid, authcode, stats, quick, deliverycode, p.coprice, (sum(o.psprice*o.pcnt )- sum(o.ptprice)) as reserve , c.company_name as company_name FROM orders o, product p, companyinfo c WHERE o.pid = p.id and oid = '".$db1->dt[oid]."' and p.admin = '".$admininfo[company_id]."' and c.company_id = p.admin and stats <> '0' and stats <> '9' GROUP by oid");
		}

		$db2->fetch();


		if ($db2->dt[stats] == "0")	$stats = "<font color=#FF0000>처리대기</font>";
		if ($db2->dt[stats] == "1") 	$stats = "<font color=#0000FF>결제확인</font>";
		if ($db2->dt[stats] == "2")	$stats = "<font color=blue>발송완료</font>";
		if ($db2->dt[stats] == "3")	$stats = "<font color=red>배송완료</font>";
		if ($db2->dt[stats] == "6")	$stats = "<font color=#993333>반품요청</font>";
		if ($db2->dt[stats] == "7")	$stats = "<font color=#993333>반품완료</font>";
		if ($db2->dt[stats] == "9")	$stats = "<font color=#993333>주문취소</font>";
		
		
/*		if ($db2->dt[stats] == "0")	$stats = "<font color=#FF0000>처리대기</font>";
		if ($db2->dt[stats] == "1") $stats = "<font color=#0000FF>처리완료</font>";
		if ($db2->dt[stats] == "2")	$stats = "<font color=#993333>주문취소</font>";
*/		

		if ($db2->dt[method] == "1")
		{
			if($db2->dt[bank] == ""){
				$method = "카드결제";
			}else{
				$method = $db2->dt[bank];
			}
		}elseif($db2->dt[method] == "0"){
			$method = "계좌입금";
		}elseif($db2->dt[method] == "2"){
			$method = "전화결제";			
		}

		$db3->query("SELECT SUM(ptprice) FROM orders WHERE oid = '".$db1->dt[oid]."'");
		$db3->fetch();

		$psum = number_format($db3->dt[0]);

		$Obj = str_replace("-","",$db1->dt[oid]);

		if ($db1->dt[stats] == "3")
		{
			$delete = "[<a href=\"javascript:alert('[처리완료] 기록은 삭제할 수 없습니다.');\">삭제</a>]";
		}
		elseif ($db1->dt[stats] != "9" && $db1->dt[method] == "1")
		{
			$delete = "[<a href=\"javascript:alert('[카드결제]는 [승인취소]와 [주문취소] 처리를 먼저한 후 삭제해주세요.');\">삭제</a>]";
		}
		else
		{
			$delete = "[<a href=\"javascript:act('delete','$Obj');\">삭제</a>]";
		}
$Contents = $Contents."
  <tr height='20' onMouseOver=\"this.style.backgroundColor='#E8ECF1'; this.style.cursor='hand'\" onMouseOut=\"this.style.backgroundColor=''\">
    <td align='center' onClick=\"swapObj('TG_VIEW_".$Obj."')\">".$db2->dt[oid]."</td>
    <td align='center' onClick=\"swapObj('TG_VIEW_".$Obj."')\">".$db2->dt[bname]."</td>
    <td align='center' onClick=\"swapObj('TG_VIEW_".$Obj."')\" nowrap>".$method."</td>
	<td align='center' onClick=\"swapObj('TG_VIEW_".$Obj."')\">".showdate2($db2->dt[date])."</td>
    <td align='right' onClick=\"swapObj('TG_VIEW_".$Obj."')\">".$psum."원</td>
    <td align='center' onClick=\"swapObj('TG_VIEW_".$Obj."')\">".$stats."</td>
  </tr>

  <tr>
    <td colspan='7' align='center'>

      <div id='TG_VIEW_".$Obj."' style='position: relative; display: none;'>
      <table border='0' width='658' cellspacing='1' cellpadding='0'>
        <tr>
          <td bgcolor='#6783A8'>
            <table border='0' cellspacing='1' cellpadding='15' width='100%'>
              <tr>
                <td bgcolor='#F8F9FA'>";




	if ($db2->dt[method] == "0")
	{
		$authinfo = "결제은행";
		$authdata = $db2->dt[bank];
	}
	else
	{
		$authinfo = "승인번호";
		$authdata = $db2->dt[authcode]."&nbsp;[<a href=\"javascript:PoPWindow2('/shop/inicis/securepay_confirm.php?mid=hongilte00&tid=".$db1->dt[tid]."&merchantreserved=승인확인테스트','400', '80','confirmwindow');\">승인확인</a>]";

		if ($db1->dt[stats] == "3")
		{
			$authcancel = $db2->dt[authcode]. "&nbsp;[<a href=\"javascript:alert('[처리완료] 기록은 승인취소할 수 없습니다.');\">승인취소</a>]";
		}
		else
		{
			$authcancel = $db2->dt[authcode]."&nbsp;[<a href=\"javascript:PoPWindow2('card_auth_cancel.php?tid=".$db1->dt[tid]."','400', '80','cancelwindow');\">승인취소</a>]";
		}
	}

$Contents = $Contents."

<table width='100%' border='0' cellpadding='0' cellspacing='1'>
	<tr>
		<td bgcolor='#6783A8'>
			<table border='0' width='100%' cellspacing='1' cellpadding='2'>
				<tr>
					<td bgcolor='#F8F9FA'>
						<table border='0' width='100%'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0'>
										<tr height='22'>
											<td width='30'><b>번호</b></td>
											<td width='388' align='center'><b>제품명</b></td>
											<td width='90' align='right'><b>수량</b></td>
											<td width='75' align='right'><b>단가</b></td>
											<td width='75' align='right'><b>공급가</b></td>
											<td width='75' align='right'><b>적립금</b></td>
											<td width='75' align='right'><b>합계</b></td>
										</tr>";

	$db3->query("SELECT o.pid, o.pname, pcnt, psprice, ptprice, p.reserve, p.coprice FROM orders o, product p WHERE o.pid = p.id and oid = '".$db1->dt[oid]."'");

	$num = $db3->total;

	$sum = 0;

	for($j = 0; $j < $db3->total; $j++)
	{
		$db3->fetch($j);

		$pname = $db3->dt[pname];
		$count = $db3->dt[pcnt];
		$price = $db3->dt[psprice];
		$coprice = $db3->dt[coprice];
		$sumptprice = $sumptprice + $db3->dt[ptprice];
		
		
		$reserve = $db3->dt[reserve];
		$ptotal = $price * $count;
		$sum += $ptotal;

$Contents = $Contents."
										<tr height='22'>
											<td width='30'><div align='center'>".$num."</div></td>
											<td width='355'><div align='center'><a href=\"javascript:PoPWindow('pinfo.php?id=".$db3->dt[pid]."','800','700','preview')\">".$pname."</a></div></td>
											<td width='90'><div align='right'>".$count." 개</div></td>
											<td width='80'><div align='right'>".number_format($price)."</div></td>
											<td width='80'><div align='right'>".number_format($coprice)."</div></td>
											<td width='80'><div align='right'>".number_format($reserve)."</div></td>
											<td width='80'><div align='right'>".number_format($ptotal)."</div></td>
										</tr>";

		$num--;	
	}
$Contents = $Contents."
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<br>
<table width='100%' border='0' cellpadding='0' cellspacing='1'>
	<tr>
		<td>
		<img src='/image/aas.gif' width='11' height='11' valign='absmiddle'>
		<font color='#993333'>주문 총액은 <b>".number_format($sumptprice)."</b>원 입니다.</font>
		</td>";
if ($db2->dt[stats] == "6"){
	$Contents .= "	<td align=right><button onclick=\"ReturnOK('".$db1->dt[oid]."')\">반품확인</button></td>";
}else if ($db2->dt[stats] == "7"){
	$Contents .= "";
}else{
	$Contents .= "	<td align=right><button onclick=\"PoPWindow('product_return.php?oid=".$db1->dt[oid]."','400','150','return')\">반품신청</button></td>";
}

unset($sumptprice);

$Contents .= "
		<!--td align=right><button onclick=\"modalwin('product_return.php?oid=".$db1->dt[oid]."','400','150')\">반품신청</button></td-->
	</tr>
</table>
";


	if (strlen($db2->dt[msg]))
	{

$Contents = $Contents."
<br><br>
<img src='/image/aas.gif' width='11' height='11' valign='absmiddle'>
<b>전달사항</b>

<table width='100%' border='0' cellpadding='0' cellspacing='1'>
	<tr>
		<td bgcolor='#6783A8'>
			<table border='0' width='100%' cellspacing='1' cellpadding='2'>
				<tr>
					<td bgcolor='#F8F9FA'>
						<table border='0' width='100%'>
							<tr>
								<td>
								".nl2br($db2->dt[msg])."
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>";

	}

$Contents = $Contents."
<!-- 내용 마침 -->
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      </div>

    

    </td>
  </tr>";


	}

$Contents = $Contents."  
</table>
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";



$P = new LayOut();
$P->strLeftMenu = order_menu();
$P->addScript = "<script language='javascript' src='orders.js'></script>";
$P->strContents = $Contents;


echo $P->PrintLayOut();


function SelectQuickLink($QuickCode, $deliverycode){
	$divname = array ("#",
	"http://www.ilogen.com/customer/reserve_03-1_ok.asp?f_slipno=",
	"http://www.doortodoor.co.kr/jsp/cmn/Tracking.jsp?QueryType=3&pTdNo=",
	"http://samsunghth.com/homepage/searchTraceGoods/SearchTraceDtdShtno.jhtml?dtdShtno=",
	"#",
	"#",
	"http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=",
	"http://www.kgbls.co.kr/tracing.asp?number=",
	"http://www.yellowcap.co.kr/branch/chase/listbody.html?a_gb=branch&a_cd=5&a_item=0&f_slipno=",
	"#");
	
	
	return "<a href='".$divname[$QuickCode]."$deliverycode' target=_blank>$deliverycode</a>";
	
}

?>