<?
include("../class/layout.class");


$db1 = new Database;
$db2 = new Database;
$db3 = new Database;



$ctgr ="orders";

$Contents = "

<table width='100%'>
<tr>
    <td align='left'> ".GetTitleNavigation("주문정보수정", "매출관리 > 주문정보수정 ")."</td>
</tr>
</table>  ";

		$sql = "SELECT status FROM ".TBL_SHOP_ORDER_DETAIL." od WHERE od.oid = '".$oid."' ";
		$db2->query($sql);
		//$db2->fetchall();
		$_detail_status = $db2->getrows();
		$detail_status = $_detail_status[0];
		//print_r($detail_status);

		if($admininfo[admin_level] == 9 || $admininfo[admin_level] == 8){
		
				/*$sql = "SELECT oid, uid, btel,bmobile,rmobile,bname, mem_group, bmail, rname, rtel, rmail, zip, addr, msg, return_message,return_date,
								UNIX_TIMESTAMP(date) AS date, method, bank, tid, authcode, gift,bank_input_name,bank_input_date,
								status, quick, deliverycode, total_price, use_reserve_price, payment_price,order_cancel_price, order_return_price, delivery_price,receipt_y,vb_info,use_cupon_price,taxsheet_yn,(select sum(delivery_price) from shop_order_delivery where o.oid = oid and delivery_pay_type in ('1','3') group by oid) as delivery_price1,(select sum(delivery_price) from shop_order_delivery where o.oid = oid and delivery_pay_type = 2 group by oid) as delivery_price2
								FROM ".TBL_SHOP_ORDER." o
								WHERE o.oid = '".$oid."' ";*/
				
				/*
				$sql = "SELECT cu.id,o.*,UNIX_TIMESTAMP(o.date) AS date,(select sum(delivery_price) from shop_order_delivery where o.oid = oid and delivery_pay_type in ('1','3') group by oid) as delivery_price1,(select sum(delivery_price) from shop_order_delivery where o.oid = oid and delivery_pay_type = 2 group by oid) as delivery_price2
								FROM ".TBL_SHOP_ORDER." o left join ".TBL_COMMON_USER." cu on o.uid=cu.code , ".TBL_COMMON_MEMBER_DETAIL." cmd
								WHERE o.oid = '".$oid."' and cu.code = cmd.code ";
				*/
				// 회원이나 상품 테이블을 조인하면 안됨... 2011.09.13
				$sql = "SELECT o.*,UNIX_TIMESTAMP(o.date) AS date,(select sum(delivery_price) from shop_order_delivery where o.oid = oid and delivery_pay_type in ('1','3') group by oid) as delivery_price1,(select sum(delivery_price) from shop_order_delivery where o.oid = oid and delivery_pay_type = 2 group by oid) as delivery_price2
								FROM ".TBL_SHOP_ORDER." o 
								WHERE o.oid = '".$oid."' ";
		}

		//echo $sql;
		$db2->query($sql);
		$db2->fetch();

		

		$sattle_method = $db2->dt[method];
		$ucode = $db2->dt[uid];
		$status = getOrderStatus($db2->dt[status],$sattle_method);

		if ($sattle_method == ORDER_METHOD_CARD)
		{
			if($db2->dt[bank] == ""){
				$method = "카드결제";
			}else{
				$method = $db2->dt[bank];
			}
		}elseif($sattle_method == ORDER_METHOD_BANK){
			$method = "무통장입금 (입금자명 : ".$db2->dt[bank_input_name].")";
		}elseif($sattle_method == ORDER_METHOD_PHONE){
			$method = "전화결제";
		}elseif($sattle_method == ORDER_METHOD_AFTER){
			$method = "후불결제";
		}elseif($sattle_method == ORDER_METHOD_VBANK){
			$method = "가상계좌 (".$db2->dt[vb_info].")";
		}elseif($sattle_method == ORDER_METHOD_ICHE){
			$method = "실시간 계좌이체 (".$db2->dt[vb_info].")";
		}elseif($sattle_method == ORDER_METHOD_ASCROW){
			$method = "가상계좌[에스크로]";
		}

		if($db2->dt[receipt_y] == "Y"){
			$receipt_y = "신청";
		}else{
			$receipt_y = "미신청";
		}

		if($db2->dt[taxsheet_yn] == 1){
			$taxsheet_yn = "신청";
		}else{
			$taxsheet_yn = "미신청";
		}


		$psum = number_format($db1->dt[total_price]);

		$Obj = str_replace("-","",$oid);

		if ($db2->dt[status] ==ORDER_STATUS_DELIVERY_COMPLETE)
		{
			$delete = "[<a href=\"javascript:alert('[처리완료] 기록은 삭제할 수 없습니다.');\">삭제</a>]";
		}
		elseif ($db2->dt[status] != ORDER_STATUS_CANCEL_COMPLETE && $sattle_method == "1")
		{
			$delete = "[<a href=\"javascript:alert('[카드결제]는 [승인취소]와 [주문취소] 처리를 먼저한 후 삭제해주세요.');\">삭제</a>]";
		}
		else
		{
			$delete = "[<a href=\"javascript:act('delete','$Obj');\">삭제</a>]";
		}


	if ($sattle_method == "0")
	{
		$authinfo = "결제은행";
		$authdata = $db2->dt[bank];
	}
	else
	{
		$authinfo = "승인번호";
		$authdata = $db2->dt[authcode]."&nbsp;[<a href=\"javascript:PoPWindow2('/shop/inicis/securepay_confirm.php?mid=hongilte00&tid=".$db1->dt[tid]."&merchantreserved=승인확인테스트','400', '80','confirmwindow');\">승인확인</a>]";

		if ($db2->dt[status] == ORDER_STATUS_DELIVERY_COMPLETE )
		{
			$authcancel = $db2->dt[authcode]. "&nbsp;[<a href=\"javascript:alert('[처리완료] 기록은 승인취소할 수 없습니다.');\">승인취소</a>]";
		}
		else
		{
			//$authcancel = $db2->dt[authcode]."&nbsp;[<a href=\"javascript:PoPWindow2('card_auth_cancel.php?tid=".$db2->dt[tid]."','400', '80','cancelwindow');\">승인취소</a>]";
			$authcancel = $db2->dt[authcode]."&nbsp;";

		}
	}

$Contents = $Contents."

      <div id='TG_order_edit' style='position: relative;width:100%;'>
		<form name='order_info_edit' method='post' onSubmit='return CheckFormValue(this)'  action='orders.act.php' target='act'>
		<input type=hidden name=oid value='$oid'>
		<input type=hidden name=act value='orderinfo_update'>
		<input type=hidden name=bstatus value='".$db2->dt[status]."'>
        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td >
              <table border='0' cellspacing='1' cellpadding='15' width='100%'>
                <tr>
                  <td bgcolor='#F8F9FA'>";

$zipcode = split("-",$db2->dt[zip]);

					$Contents = $Contents."

					<div style='padding:5px'><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'> <b>주문정보</b></div>

					<table border='0' width='100%' cellspacing='1' cellpadding='0'>
						<tr>
							<td >
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='line_color' style='width:100%;'>
								<col width='15%' />
								<col width='35%' />
								<col width='15%' />
								<col width='35%' />
									<tr height=25 bgcolor='#ffffff' >
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 주문번호</td>
										<td>&nbsp;".$db2->dt[oid]."</td>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 주문일자</td>
										<td>&nbsp;".showdate($db2->dt[date])."</td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class=leftmenu align='left' style='padding-left:10px;' ><img src='../image/title_head.gif' align=absmiddle> 주문자이름</td>
										<td >&nbsp;<input type='text' size=25 name='bname' class='textbox' value='".$db2->dt[bname]."' validation='true' title='주문자이름' >
										<input type='text' size=15 name='mem_group' class='textbox' value='".$db2->dt[mem_group]."' validation='true' title='회원그룹'></td>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 주문자아이디</td>
										<td >&nbsp;".$db2->dt[buserid]."</td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 주문자메일</td>
										<td >&nbsp;<input type='text' size=25 name='bmail' class='textbox' value='".$db2->dt[bmail]."' validation='true' title='주문자메일'></td>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 수취인이름</td>
										<td >&nbsp;<input type='text' size=25 name='rname' class='textbox' value='".$db2->dt[rname]."' validation='false' title='수취인이름'></td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 주문자전화</td>
										<td >&nbsp;<input type='text' size=25 name='btel' class='textbox' value='".$db2->dt[btel]."' validation='false' title='주문자전화'></td>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 수취인전화</td>
										<td >&nbsp;<input type='text' size=25 name='rtel' class='textbox' value='".$db2->dt[rtel]."' validation='false' title='수취인전화'></td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 주문자핸드폰</td>
										<td >&nbsp;<input type='text' size=25 name='bmobile' class='textbox' value='".$db2->dt[bmobile]."' validation='true' title='주문자핸드폰'></td>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 수취인핸드폰</td>
										<td >&nbsp;<input type='text' size=25 name='rmobile' class='textbox' value='".$db2->dt[rmobile]."' validation='true' title='수취인핸드폰'></td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 우편번호</td>
										<td colspan='3' >&nbsp;<input type='text' name='zipcode1' id='zipcode1' size='3' maxlength='3' class='textbox' readonly value='".$zipcode[0]."'> - <input type='text' name='zipcode2' id='zipcode2' size='3' maxlength='3' class='textbox' readonly value='".$zipcode[1]."'>&nbsp;<img src='../images/".$admininfo["language"]."/btn_search_address.gif' align=absmiddle style='cursor:pointer;' onClick=\"zipcode('2')\"></td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 배달주소</td>
										<td colspan='3' >&nbsp;<input type='text' size='60' name='addr' id='addr' class='textbox' value='".$db2->dt[addr]."' validation='true' title='배달주소'></td>
									</tr>";
					if($admininfo[admin_level] == 9){
					$Contents .= "
									<tr>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> <b>총 상품금액</b></td>
										<td bgcolor='#ffffff'>&nbsp;".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[total_price])." ".$currency_display[$admin_config["currency_unit"]]["bacl"]."</td>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> <b>결제금액</b></td>
										<td bgcolor='#ffffff'>&nbsp; ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[payment_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
									</tr>
									<tr>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> <b>적립금 사용금액</b></td>
										<td bgcolor='#ffffff'>&nbsp; ".number_format($db2->dt[use_reserve_price])." P</td>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> <b>쿠폰사용금액</b></td>
										<td bgcolor='#ffffff'>&nbsp; ".number_format($db2->dt[use_cupon_price])."</td>
									</tr>
									<tr>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> <b>회원할인금액</b></td>
										<td bgcolor='#ffffff'>&nbsp;".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[use_member_sale_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
										<td class=leftmenu align='left' style='padding-left:10px;'></td>
										<td bgcolor='#ffffff'>&nbsp;</td>
									</tr>
									<tr>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> <b>주문취소금액</b></td>
										<td bgcolor='#ffffff'>&nbsp; ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[order_cancel_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> <b>반품금액</b></td>
										<td bgcolor='#ffffff'>&nbsp;".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[order_return_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
									</tr>
									<tr>
										<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 입금확인일자</td>
										<td colspan=3 bgcolor='#ffffff'>&nbsp;<input type='text' size='12' maxlength=8 name='bank_input_date' class='textbox' value='".$db2->dt[bank_input_date]."' onkeyup='onlyEditableNumber(this)' style='ime-mode:disabled;' validation='false' title='입금확인일자'> 예) 20090806</td>
									</tr>
									<tr height=40>
										<td class=leftmenu2 align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 배송비</td>
										<td bgcolor='#ffffff'>&nbsp;선불 배송비 : ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[delivery_price1])." ".$currency_display[$admin_config["currency_unit"]]["back"]."<br>&nbsp;착불 배송비 : ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[delivery_price2])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
										<td class=leftmenu2 align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 결제수단</td>
										<td bgcolor='#ffffff' style='padding:5px 0 5px 10px;line-height:140%'>$method <span class=small>".($sattle_method == '0' ? "<br>".$db2->dt[bank]:"")."</span>
										</td>
									</tr>";
					}
					$Contents .= "
									<tr >
										<td class='leftmenu3' align='left'><img src='../image/title_head.gif'> 주문상태 변경내역</td>
										<td bgcolor='#ffffff' colspan='3' style='padding:10px 0 10px 10px'>";
										if($admininfo[admin_level] == 9){
											$sql = "select os.regdate, os.status, os.status_message, os.pid, od.company_name , od.pname,os.quick,os.invoice_no
															from ".TBL_SHOP_ORDER_STATUS." os
															left join ".TBL_SHOP_ORDER_DETAIL." od on os.pid = od.pid and od.oid ='$oid'
															where os.oid ='$oid'   order by os.regdate asc"; //and ((od.status in ('IR','IC') and od.pid = '') or (od.status not in ('IR','IC') and od.pid != ''))
											//echo $sql;
											$db3->query($sql);
										}else if($admininfo[admin_level] == 8){
											$sql = "select os.regdate, os.status, os.status_message, os.pid, od.pname
															from ".TBL_SHOP_ORDER_STATUS." os, ".TBL_SHOP_ORDER_DETAIL." od
															where  os.oid ='$oid' and ((os.pid = od.pid and od.company_id ='".$admininfo[company_id]."') )
															union
															select os.regdate, os.status, os.status_message, os.pid, od.pname
															from ".TBL_SHOP_ORDER_STATUS." os, ".TBL_SHOP_ORDER_DETAIL." od
															where  os.oid ='$oid' and os.oid = od.oid and os.pid is null and od.company_id ='".$admininfo[company_id]."'
															order by regdate asc"; //and ((od.status in ('IR','IC') and od.pid = '') or (od.status not in ('IR','IC') and od.pid != ''))
											//echo $sql;

											$db3->query($sql);
										}
										for($j = 0; $j < $db3->total; $j++)
										{
											$db3->fetch($j);
											$Contents .= "<span class=small>".$db3->dt[regdate]." ".getOrderStatus($db3->dt[status],$sattle_method)."  ".($db3->dt[pid] ? "(상품코드:".$db3->dt[pid]." - ".Cut_Str($db3->dt[pname],20,"...").")":"")." <span style='color:blue'>".($db3->dt[invoice_no].":" ? codeName($db3->dt[quick]).":":"")." ".($db3->dt[invoice_no] ? $db3->dt[invoice_no]:"")."</span> ".($db3->dt[company_name] ? "- 수정업체:".$db3->dt[company_name]."":"")." - <b>".$db3->dt[status_message]."</b></span><br>";
										}


					$Contents .= "
										</td>
									</tr>
									<tr height=60 bgcolor='white'>
										<td class=leftmenu3 align='left' style='padding-left:10px; '><img src='../image/title_head.gif'> 전달사항</td>
										<td bgcolor='#ffffff' colspan=3 style='padding-left:5px; '>".nl2br($db2->dt[msg])."</td>
									</tr>
								</table>
							</td>
						</tr>";


					if($admininfo[mall_use_multishop] && $admininfo[admin_level] ==  9){
					$Contents .= "
						<tr height=30>
							<td class='small' style='padding:3px;line-height:150%'>
							<table width=100%>
								<tr>
									<td>
										<!-- - 입점업체 상품 모두가 상태변경이 되었을때 상태변경을 하시면 됩니다.-->
										".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."
									</td>
									<td align=right>
									<a href='javascript:history.back();'><img src='../images/".$admininfo["language"]."/btn_back.gif' border='0' align=absmiddle></a> ";
									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
									$Contents .= "<input type=image src='../images/".$admininfo["language"]."/bts_modify.gif' border=0 style='cursor:pointer;' align=absmiddle> ";
									}
									if($admininfo[admin_level] == 9 && $admininfo["language"] == 'korea' ){
										if($admininfo[sattle_module] == "inicis"){
											$Contents .= " <a href='https://iniweb.inicis.com/' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_inisis.gif' align=absmiddle border=0  ></a>";
										}else if($admininfo[sattle_module] == "allthegate"){
											$Contents .= " <a href='https://www.allthegate.com/login/r_login.jsp' target='_blank'><img src='../images//btn_pg_admin.gif' align=absmiddle border=0  ></a>";
										}else if($admininfo[sattle_module] == "lgdacom"){
											$Contents .= " <a href='http://pgweb.lgdacom.net' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_lgdacom.gif' align=absmiddle border=0  ></a>";
										}else if($admininfo[sattle_module] == "kcp"){
											$Contents .= " <a href='https://admin.kcp.co.kr' target='_blank'><img src='../images/".$admininfo["language"]."/btn_pg_kcp.gif' align=absmiddle border=0  ></a>";
										}
									}
								$Contents .= "
									</td>
								</tr>
							</table>
							</td>
						</tr>";

					}
					$Contents .= "
					</table>

				</td>
			</tr>
		</table>
		</form>
		</div>
	</td>
</tr>
<tr>
	<td style='padding:10px;'>";

$Contents .= "<div style='height:20px;'></div>


<div style='padding:5px'><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'> <b>주문상품정보</b></div>
<form name='order_edit' method='post' onSubmit=\"return orderStatusUpdate(this)\"  action='orders.act.php' target='act'>
<input type=hidden name=oid value='$oid'>
<input type=hidden name=act value='update'>
<input type=hidden name=bstatus value='".$db2->dt[status]."'>
<table width='100%' border='0' cellpadding='0' cellspacing='1' bgcolor=silver>
		<tr bgcolor='#ffffff' height=40>
		<td style='padding:0 0 0 10px'>
		<table border=0><tr><td>".$db2->dt[status];

			if ($db2->dt[status] == ORDER_STATUS_RETURN_APPLY || $db2->dt[status] == ORDER_STATUS_RETURN_COMPLETE )
			{
				$Contents = $Contents."<input type=hidden name=status value='".$db2->dt[status]."'>&nbsp;$status";
			}elseif ($db2->dt[status] == ORDER_STATUS_INCOM_READY){
				if($admininfo[admin_level] == 9){
					$Contents .= "
					<select name='status' onchange='ViewdeliveryCodeInputBox(this.value,document.order_edit)'>
						<option value='".ORDER_STATUS_INCOM_READY."' ".ReturnStringAfterCompare(ORDER_STATUS_INCOM_READY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option>
						<option value='".ORDER_STATUS_INCOM_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_INCOM_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
						<option value='".ORDER_STATUS_WAREHOUSING_STANDYBY."' ".ReturnStringAfterCompare(ORDER_STATUS_WAREHOUSING_STANDYBY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_WAREHOUSING_STANDYBY)."</option>
						<option value='".ORDER_STATUS_DELIVERY_READY."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_READY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option>
						<option value='".ORDER_STATUS_DELIVERY_ING."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_ING,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>
						<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
						<option value='".ORDER_STATUS_CANCEL_APPLY."' ".ReturnStringAfterCompare(ORDER_STATUS_CANCEL_APPLY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</option>
						<option value='".ORDER_STATUS_CANCEL_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_CANCEL_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>
						<option value='".ORDER_STATUS_SOLDOUT_CANCEL."' ".ReturnStringAfterCompare(ORDER_STATUS_SOLDOUT_CANCEL,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_SOLDOUT_CANCEL)."</option>";
					if(in_array(ORDER_STATUS_EXCHANGE_APPLY,$detail_status) || in_array(ORDER_STATUS_EXCHANGE_ING,$detail_status) || in_array(ORDER_STATUS_EXCHANGE_DELIVERY,$detail_status) || in_array(ORDER_STATUS_EXCHANGE_COMPLETE,$detail_status)){
						$Contents .= "<option value='".ORDER_STATUS_EXCHANGE_APPLY."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_APPLY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_ING."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_ING,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_DELIVERY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</option>";
					}
					$Contents .= "</select>";
				}else if($admininfo[admin_level] == 8){
					$Contents = $Contents."
					<select name='status' onchange='ViewdeliveryCodeInputBox(this.value,document.order_edit)'>
						<option value='".ORDER_STATUS_INCOM_READY."' ".ReturnStringAfterCompare(ORDER_STATUS_INCOM_READY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option>
						<option value='".ORDER_STATUS_WAREHOUSING_STANDYBY."' ".ReturnStringAfterCompare(ORDER_STATUS_WAREHOUSING_STANDYBY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_WAREHOUSING_STANDYBY)."</option>
						<option value='".ORDER_STATUS_DELIVERY_READY."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_READY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option>
						<option value='".ORDER_STATUS_DELIVERY_ING."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_ING,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>
						<option value='".ORDER_STATUS_SOLDOUT_CANCEL."' ".ReturnStringAfterCompare(ORDER_STATUS_SOLDOUT_CANCEL,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_SOLDOUT_CANCEL)."</option>
					</select>
					";
				}
			}elseif ($db2->dt[status] == ORDER_STATUS_INCOM_COMPLETE || $db2->dt[status] == ORDER_STATUS_DELIVERY_ING){
				if($admininfo[admin_level] == 9){
					$Contents = $Contents."
					<select name='status' onchange='ViewdeliveryCodeInputBox(this.value,document.order_edit)'>
						<option value='".ORDER_STATUS_INCOM_READY."' ".ReturnStringAfterCompare(ORDER_STATUS_INCOM_READY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option>
						<option value='".ORDER_STATUS_INCOM_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_INCOM_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
						<option value='".ORDER_STATUS_WAREHOUSING_STANDYBY."' ".ReturnStringAfterCompare(ORDER_STATUS_WAREHOUSING_STANDYBY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_WAREHOUSING_STANDYBY)."</option>
						<option value='".ORDER_STATUS_DELIVERY_READY."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_READY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option>
						<option value='".ORDER_STATUS_DELIVERY_ING."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_ING,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>
						<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
						<option value='".ORDER_STATUS_RETURN_APPLY."' ".ReturnStringAfterCompare(ORDER_STATUS_RETURN_APPLY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</option>
						<option value='".ORDER_STATUS_RETURN_ING."' ".ReturnStringAfterCompare(ORDER_STATUS_RETURN_ING,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_RETURN_ING)."</option>
						<option value='".ORDER_STATUS_RETURN_DELIVERY."' ".ReturnStringAfterCompare(ORDER_STATUS_RETURN_DELIVERY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_RETURN_DELIVERY)."</option>
						<option value='".ORDER_STATUS_RETURN_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_RETURN_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_APPLY."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_APPLY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_ING."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_ING,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_DELIVERY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</option>
						<option value='".ORDER_STATUS_REFUND_APPLY."' ".ReturnStringAfterCompare(ORDER_STATUS_REFUND_APPLY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_REFUND_APPLY)."</option>
						<option value='".ORDER_STATUS_REFUND_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_REFUND_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_REFUND_COMPLETE)."</option>
						<option value='".ORDER_STATUS_CANCEL_APPLY."' ".ReturnStringAfterCompare(ORDER_STATUS_CANCEL_APPLY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</option>
						<option value='".ORDER_STATUS_CANCEL_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_CANCEL_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>
						<option value='".ORDER_STATUS_SOLDOUT_CANCEL."' ".ReturnStringAfterCompare(ORDER_STATUS_SOLDOUT_CANCEL,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_SOLDOUT_CANCEL)."</option>
					</select>";
				}else if($admininfo[admin_level] == 8){
					$Contents = $Contents."
					<select name='status' onchange='ViewdeliveryCodeInputBox(this.value,document.order_edit)'>
						<option value='".ORDER_STATUS_INCOM_READY."' ".ReturnStringAfterCompare(ORDER_STATUS_INCOM_READY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option>
						<option value='".ORDER_STATUS_WAREHOUSING_STANDYBY."' ".ReturnStringAfterCompare(ORDER_STATUS_WAREHOUSING_STANDYBY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_WAREHOUSING_STANDYBY)."</option>
						<option value='".ORDER_STATUS_DELIVERY_READY."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_READY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option>
						<option value='".ORDER_STATUS_DELIVERY_ING."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_ING,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>		<option value='".ORDER_STATUS_RETURN_APPLY."' ".ReturnStringAfterCompare(ORDER_STATUS_RETURN_APPLY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_RETURN_APPLY)."</option>
						<option value='".ORDER_STATUS_RETURN_ING."' ".ReturnStringAfterCompare(ORDER_STATUS_RETURN_ING,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_RETURN_ING)."</option>
						<option value='".ORDER_STATUS_RETURN_DELIVERY."' ".ReturnStringAfterCompare(ORDER_STATUS_RETURN_DELIVERY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_RETURN_DELIVERY)."</option>
						<option value='".ORDER_STATUS_RETURN_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_RETURN_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_RETURN_COMPLETE)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_APPLY."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_APPLY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_APPLY)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_ING."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_ING,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_ING)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_DELIVERY."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_DELIVERY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_DELIVERY)."</option>
						<option value='".ORDER_STATUS_EXCHANGE_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_EXCHANGE_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_EXCHANGE_COMPLETE)."</option>
						<option value='".ORDER_STATUS_REFUND_APPLY."' ".ReturnStringAfterCompare(ORDER_STATUS_REFUND_APPLY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_REFUND_APPLY)."</option>
						<option value='".ORDER_STATUS_SOLDOUT_CANCEL."' ".ReturnStringAfterCompare(ORDER_STATUS_SOLDOUT_CANCEL,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_SOLDOUT_CANCEL)."</option>
					</select>
					";
				}
			}else if($db2->dt[status] == ORDER_STATUS_DELIVERY_READY || $db2->dt[status] == ORDER_STATUS_DELIVERY_COMPLETE){
				$Contents = $Contents."
				<select name='status' onchange='ViewdeliveryCodeInputBox(this.value,document.order_edit)'>
					<option value='".ORDER_STATUS_INCOM_READY."' ".ReturnStringAfterCompare(ORDER_STATUS_INCOM_READY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option>
					<option value='".ORDER_STATUS_INCOM_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_INCOM_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
					<option value='".ORDER_STATUS_DELIVERY_READY."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_READY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_READY)."</option>
					<option value='".ORDER_STATUS_DELIVERY_ING."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_ING,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>
					<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_DELIVERY_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
					<option value='".ORDER_STATUS_CANCEL_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_CANCEL_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>
				</select>
				";
			}else if($db2->dt[status] == ORDER_STATUS_CANCEL_APPLY){
				$Contents = $Contents."
				<select name='status' onchange='ViewdeliveryCodeInputBox(this.value,document.order_edit)'>
					<option value='".ORDER_STATUS_CANCEL_APPLY."' ".ReturnStringAfterCompare(ORDER_STATUS_CANCEL_APPLY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</option>
					<option value='".ORDER_STATUS_CANCEL_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_CANCEL_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>
				</select>
				";
			}else if($db2->dt[status] == ORDER_STATUS_SETTLE_READY){
				$Contents = $Contents."
				<select name='status' onchange='ViewdeliveryCodeInputBox(this.value,document.order_edit)'>
					<option value='".ORDER_STATUS_SETTLE_READY."' ".ReturnStringAfterCompare(ORDER_STATUS_SETTLE_READY,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_SETTLE_READY,$sattle_method)."</option>
					<option value='".ORDER_STATUS_CANCEL_COMPLETE."' ".ReturnStringAfterCompare(ORDER_STATUS_CANCEL_COMPLETE,$db2->dt[status],"selected").">".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>
				</select>
				";
			}else{
				$Contents = $Contents."<input type=hidden name=status value='".$db2->dt[status]."'>&nbsp;$status";
			}



$Contents = $Contents."
		</td>";
if ($db2->dt[status] == ORDER_STATUS_DELIVERY_COMPLETE || $db2->dt[status] == ORDER_STATUS_DELIVERY_ING){
$Contents = $Contents."
		<td>
		".deliveryCompanyList($db2->dt[quick],"select")."
		</td>
		<td   nowrap><div id='deliverycode' style='display:inline'>송장번호 : <input type='text' name='deliverycode'   size=15 value='".$db2->dt[deliverycode]."'></div></td>
		<td> </td>";
}else{
$Contents = $Contents."
		<td>
		".deliveryCompanyList($db2->dt[quick],"select","style='display:none'")."
		</td>
		<td><div id='deliverycode' style='display:none'>송장번호 : <input type='text' name='deliverycode' size=15></div></td>
		<!--td id='exchangeChoice' style='display:none'><a href=\"javascript:PoPWindow('exchange_product.php?oid=".$db2->dt[oid]."',520,600,'sendsms')\">교환상품선택</a></td-->";

}

$Contents = $Contents."	<td>";
if($admininfo[mall_use_multishop]){
if($admininfo[admin_level] ==  9){
//$Contents .= " <input type=checkbox name='product_status_change' id='product_status_change' value='1' ".($admininfo[admin_level] == 9 ? "checked":"")."><label for='product_status_change'><b>주문상품상태변경</b></label>&nbsp;<span class=small>( 체크시 입점업체 상품에 대한 상태변경이 이 됩니다.)</span>";
$Contents .= " <input type=hidden name='product_status_change' id='product_status_change' value='1'>&nbsp;";
	if($db2->dt[status]!=ORDER_STATUS_CANCEL_COMPLETE) {
		$Contents .= "<span class=small><!--( 수정하기 버튼을 누르면 체크된 상품에 대한 상태가 변경됩니다.)--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')."</span>";
	} else {
		$Contents .= "";
	}
}else if($admininfo[admin_level] ==  8){
	$Contents .= " <input type=hidden name='product_status_change' id='product_status_change' value=''>";
}
}else{
$Contents .= " <input type=hidden name='product_status_change' id='product_status_change' value='1'>";
}
$Contents = $Contents."
		</td>
		<td align='right'>";
			if($db2->dt[status]!=ORDER_STATUS_CANCEL_COMPLETE) {
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents .= "<input type=image src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 style='cursor:pointer;'>";
				}else{
					$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 style='cursor:pointer;'></a>";
				}
			} else {
				$Contents .= "";
			}
	$Contents .= "
		</td>";
$Contents = $Contents."		</tr></table>
		".printStatusInfo($oid)."
		</td>
	</tr>
	<tr>
		<td bgcolor='silver'>
			<table border='0' width='100%' cellspacing='0' cellpadding='2'>
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0'>
										<tr height='25' bgcolor='#efefef' align=center>
											<td width='3%' class='s_td'><input type=checkbox  name='all_fix' onclick='fixAll(document.order_edit)' checked></td>
											<!--td width='10%' class='m_td class'><b>상품코드</b></td-->
											<td width='*' colspan=2 class='m_td small'><b>상품명</b></td>
											<td width='5%' class='m_td small'><b>수량</b></td>
											<td width='15%' class='m_td small'><b>옵션</b></td>
											<td width='5%' class='m_td small'><b>전달사항</b></td>
											<td width='10%' class='m_td small'><b>쿠폰사용</b></td>";
if($admininfo[mall_use_multishop]){
$Contents .=	"				<td width='7%' class='m_td small'><b>공급가</b></td>";
$Contents .=	"				<td width='11%' class='m_td small'><b>상태</b></td>";
$Contents .=	"				<td width='5%'  class='m_td small'><b>단가</b></td>
											<td width='5%' class='m_td small'><b>적립금</b></td>";
}else{
$Contents .=	"				<td width='10%'  class='m_td small'><b>단가</b></td>
											<td width='10%' class='m_td small'><b>적립금</b></td>";
}

$Contents .=	"

											<td width='8%' class='e_td small'><b>합계</b></td>

										</tr>";

	if($admininfo[admin_level] == 9){//quick, ptprice, od.option_text, po.option_etc1,invoice_no, od.status , od.coprice,od.delivery_type, od.ac_date, od.ac_ix, od.dc_date
		if($admininfo[mem_type] == "MD"){
			$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}

		$sql = "SELECT od.od_ix,od.pid,od.product_type, od.pname, od.reserve, pcnt, psprice, ptprice, od.option_text, od.option_etc, od.status , od.use_coupon, od.use_coupon_code,
						od.coprice, od.invoice_no, od.quick, od.ac_date, od.ac_ix, od.dc_date,od.option_price, od.company_name, od.company_id,od.ra_date,ea_date,return_reason,return_invoice_no, odd.order_type
						FROM ".TBL_SHOP_ORDER_DETAIL." od LEFT JOIN shop_order_detail_deliveryinfo odd ON od.od_ix=odd.od_ix
						WHERE od.oid = '".$oid."' $addWhere ";
	}else if($admininfo[admin_level] == 8){
		$sql = "SELECT od.od_ix,od.pid,od.product_type, od.pname, od.reserve, pcnt, psprice, ptprice, od.option_text, od.option_etc, od.status , od.use_coupon, od.use_coupon_code,
						od.coprice, od.invoice_no, od.quick, od.ac_date, od.ac_ix, od.dc_date,od.option_price, od.company_name, od.company_id,od.ra_date,ea_date,return_reason,return_invoice_no, odd.order_type
						FROM ".TBL_SHOP_ORDER_DETAIL." od LEFT JOIN shop_order_detail_deliveryinfo odd ON od.od_ix=odd.od_ix
						WHERE od.oid = '".$oid."' and od.company_id = '".$admininfo[company_id]."' ";
	}

	$db3->query($sql);


	$num = 1;

	$sum = 0;
	$arr_sns_ptype=array(4,5,6);
	for($j = 0; $j < $db3->total; $j++)
	{
		$db3->fetch($j);

		$pname = $db3->dt[pname];
		$pcode = $db3->dt[pcode];
		$product_type = $db3->dt[product_type];
		$count = $db3->dt[pcnt];
		$option_div = $db3->dt[option_text];
		$option_etc1 = $db3->dt[option_etc];
		$msgbyproduct = $db3->dt[msgbyproduct];
		$option_price = $db3->dt[option_price];
		$price = $db3->dt[psprice]+$db3->dt[option_price];
		$coprice = $db3->dt[coprice];
		$sumptprice = $sumptprice + $db3->dt[ptprice];


		$reserve = $db3->dt[reserve];
		$ptotal = $price * $count;
		$sum += $ptotal;

$Contents .= "
										<tr height='30' align='center'>
											<td nowrap><input type=checkbox name='od_ix[]' id='oid' value='".$db3->dt[od_ix]."' checked></td>
											<td style='padding:3px 0px;'><img src='".PrintImage($admin_config[mall_data_root]."/images/product", $db3->dt[pid], "c")."'  onerror=\"this.src='".$admin_config[mall_data_root]."/images/noimg_52.gif'\" width=50></td>
											<td align='left' style='padding:5px 0 5px 0;line-height:130%'>";
										if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
											$Contents .= "<a href=\"javascript:PoPWindow('../store/company.add.php?company_id=".$db3->dt[company_id]."&mmode=pop',960,600,'brand')\"><b>".($db3->dt[company_name] ? $db3->dt[company_name]:"-")."</b></a><br>";
										}
										if(in_array($product_type,$arr_sns_ptype)){
$Contents .= "						<a href=\"/sns/shop/goods_view.php?id=".$db3->dt[pid]."\" target=_blank>".$pname."</a>";
										} else {
$Contents .= "						<a href=\"/shop/goods_view.php?id=".$db3->dt[pid]."\" target=_blank>".$pname."</a>";
										}

$Contents .= "				</td>
											<td >".$count." 개</td>
											<td align=left style='padding:7px 5px;'>".$option_div."".($option_price != '' ? " + ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($option_price)."".$currency_display[$admin_config["currency_unit"]]["back"]."":"")."</td>
											<td align=center>".$msgbyproduct."</td>
											<td align=center>".number_format($db3->dt[use_coupon])." ".($db3->dt[use_coupon] > 0 ? "<br><a href=\"javascript:PopSWindow('../display/cupon_publish.php?mmode=pop&regist_ix=".$db3->dt[use_coupon_code]."',900,700,'cupon_detail_pop');\" class=blue>쿠폰확인</a>":"")."</td>";
if($admininfo[mall_use_multishop]){
$Contents .= "				<td align=center> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db3->dt[coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";
$Contents .= "				<td align=center style='line-height:130%'>";
if($db3->dt[status]==ORDER_STATUS_EXCHANGE_DELIVERY && $db3->dt[order_type]>0) $Contents .= "<a href=\"javascript:PopSWindow('/admin/order/orders.view_address.php?oid=".$oid."&od_ix=".$db3->dt[od_ix]."&type=".$db3->dt[status]."',500,400,'orders_view_address')\">";
$Contents .=getOrderStatus($db3->dt[status],$sattle_method);
if($db3->dt[status]==ORDER_STATUS_EXCHANGE_DELIVERY && $db3->dt[order_type]>0) $Contents .= "</a>";
if($db3->dt[dc_date] && $db3->dt[status] == "DC"){
	$Contents .= "<br />(".substr($db3->dt[dc_date],0,10).")";
}
if($db3->dt[ac_date]){
	$Contents .= "<br /><a href=\"/admin/order/accounts_detail.php?ac_ix=".$db3->dt[ac_ix]."\" target=_blank>(".substr($db3->dt[ac_date],0,4)."-".substr($db3->dt[ac_date],4,2)."-".substr($db3->dt[ac_date],6,2).")</a> ";
}
if($db3->dt[invoice_no] && !($db3->dt[status] == "ED" || $db3->dt[status] == "RD")){
	$Contents .= "<br><a href=\"javascript:searchGoodsFlow('".$db3->dt[quick]."', '".str_replace("-","",$db3->dt[invoice_no])."')\">".codeName($db3->dt[quick])." </a> ";
}

if($db3->dt[ra_date] && ($db3->dt[status] == "RA")&& $db3->dt[return_reason] != ""){
	$Contents .= "(".substr($db3->dt[ra_date],0,10).")<br>";

	$Contents .= "<a href='javascript:void(0)' onclick=\"return_pop(".$db3->dt[od_ix].")\"><img src='../image/btn_return_reason.gif'></a>";

}

if($db3->dt[ea_date] && ($db3->dt[status] == "EA")&& $db3->dt[return_reason] != ""){
	$Contents .= "(".substr($db3->dt[ra_date],0,10).")<br>";

	$Contents .= "<a href='javascript:void(0)' onclick=\"return_pop(".$db3->dt[od_ix].")\"><img src='../image/btn_exchange_reason.gif'></a>";

}

if($db3->dt[status] == "ED" || $db3->dt[status] == "RD"){
	$Contents .= "<br><a href=\"javascript:searchGoodsFlow('".$db3->dt[quick]."', '".str_replace("-","",$db3->dt[return_invoice_no])."')\">".codeName($db3->dt[quick])." </a> ";
}
$Contents .="					</td>";
}
$Contents .= "				<td > ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td >".number_format($reserve)." P</td>
											<td >".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($ptotal)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
										</tr>
										<tr><td colspan=12 class=dot-x></td></tr>";

		$num++;
	}
$Contents = $Contents."
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>





<!-- 수정마침 -->

				</td>
			  </tr>
			</table>
            </td>
          </tr>
        </table>
        </form>
		<table border='0' cellspacing='1' cellpadding='15' width='100%' bgcolor='#F8F9FA' bordercolor='#black'>
			<tr>
			  <td  style='padding:10px;'>
				<form name=order_memo_frm method=post action='orders_memo.act.php'  target='iframe_act'>
					<input type='hidden' name='act' value='memo_insert'>
					<input type='hidden' name='oid' value='$oid'>
					<input type='hidden' name='ucode' value='$ucode'>

					<table width=100%>
					 <tr>
						<td><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'><b> 주문 상담내역</b></td>
					</tr>
					<tr>
						<td bgcolor='#ffffff' style='padding:10px'><textarea style='height:50px;width:97%;' wrap='off'  basci_message=true name='memo' ></textarea></td>
					</tr>
					<tr>
						<td bgcolor='D0D0D0' height='1'></td>
					</tr>
					<tr><td align=right style='padding:10px;'> <input type=image src='../images/".$admininfo["language"]."/btn_counsel_save.gif' id='save_btn' border=0 align=absmiddle></td></tr>
					</form>
					<tr>
						<td align=right style='padding-top:10px;' id='design_history_area'>
						".PrintOrderMemo($oid)."
						</td>
					</tr>
					</table>

			</tr>
		</table>
      </div>



";
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >주문에 대한 정보를 확인 및 수정할수 있는 페이지 입니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >처리상태가 반품완료 및 취소완료시에는 상품을 변경할수 없습니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


$help_text = HelpBox("주문내역수정", $help_text);
$Contents .= $help_text;


$Contents = $Contents."
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";

$Script = "
<script language='javascript' >
function memoDelete(oid, om_ix){
	if(confirm('해당 상담내역을 정말로 삭제 하시겠습니까?')){
		document.frames['iframe_act'].location.href='orders_memo.act.php?act=memo_delete&oid='+oid+'&om_ix='+om_ix;
	}
}


//콤마표현 없는 정수만입력
function onlyEditableNumber(obj){
 var str = obj.value;
 str = new String(str);
 var Re = /[^0-9]/g;
 str = str.replace(Re,'');
 obj.value = str;
}
</script>
";


$P = new LayOut();
$P->strLeftMenu = order_menu();
$P->addScript = $Script."<script language='javascript' src='orders.js'></script>";
$P->Navigation = "HOME > 주문관리 > 주문정보수정";
$P->strContents = $Contents;


echo $P->PrintLayOut();



function PrintOrderMemo($oid){
	global $admininfo, $page, $nset, $QUERY_STRING;
	$mdb = new Database;

	$sql = "select count(*) as total from shop_order_memo where oid ='$oid'    ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[total];


	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver>";
	$mString = $mString."
				<form name=listform method=post action='orders.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
				<input type='hidden' name='act' value='memo_insert'>
				<input type='hidden' name='oid' value='$oid'>
				<col width='15%'>
				<col width='10%'>
				<col width='*'>
				<col width='10%'>
				<tr align=center bgcolor=#efefef height=25>
					<!--td class=s_td width='5%'><input type=checkbox class=nonborder id='all_fix' name='all_fix' onclick='fixAll(document.listform)'>&nbsp;</td-->
					<td class=s_td>상담일자</td>
					<td class=m_td>상담자</td>
					<td class=m_td>상담내용</td>
					<td class=e_td>관리</td>
				</tr>";
	if ($total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=4 align=center><!--주문 상담내역이  존재 하지 않습니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</td></tr>";
	}else{

		$mdb->query("select * from shop_order_memo where oid ='$oid' order by regdate desc   limit $start , $max");

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			$mString = $mString."<tr height=45 bgcolor=#ffffff align=center>
			<!--td bgcolor='#ffffff'><input type=checkbox class=nonborder id='om_ix' name='om_ix[]' value='".$mdb->dt[om_ix]."'></td-->
			<td bgcolor='#efefef'>".$mdb->dt[regdate]."</td>
			<td >".$mdb->dt[counselor]."</td>
			<td bgcolor='#efefef' align=left style='padding-left:10px;' style='word-break:break-all'>".nl2br($mdb->dt[memo])."</td>
			<td bgcolor='#ffffff' align=center nowrap>

				<a href=JavaScript:memoDelete('".$mdb->dt[oid]."','".$mdb->dt[om_ix]."')><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
			</td>
			</tr>
			<tr height=1><td colspan=6 class='dot-x'></td></tr>
			";
		}

		//$mString .= "<tr bgcolor=#ffffff height=40><td colspan=8 align=left><a href=\"JavaScript:SelectDelete(document.forms['listform']);\"><img  src='../image/bt_all_del.gif' border=0 align=absmiddle ></a></td></tr>";
	}

	//$query_string = str_replace("nset=$nset&page=$page&","",$QUERY_STRING) ;
	//echo $query_string;
	$mString .= "</form>";
	//$mString .= "<tr height=50 bgcolor=#ffffff><td colspan=6 align=left>".page_bar($total, $page, $max,"&".$query_string,"")."</td></tr>
	$mString .= "</table>";

	return $mString;
}




function printStatusInfo($oid){
	$mdb = new Database;

	$mdb->query("select * from ".TBL_SHOP_ORDER_STATUS." where oid = '$oid' ");

	for($i=0;$i < $db->total;$i++){
		$mdb->fetch($i);
		$mstring .= $mdb->dt[regdate] ." - ". getOrderStatus($mdb->dt[status])."<br>";
	}

	return $mstring;
}



?>