<?
include("../class/layout.class");
include_once("service.lib.php");

$db1 = new Database;
$db2 = new Database;
$db3 = new Database;



$ctgr ="orders";

$Contents = "

<table width='100%'>
<tr>
    <td align='left'> ".GetTitleNavigation("서비스주문정보수정", "서비스관리 > 서비스주문정보수정 ")."</td>
</tr>
</table>  ";

		$sql = "SELECT status FROM service_order_detail od WHERE od.oid = '".$oid."' ";
		$db2->query($sql);
		//$db2->fetchall();
		$_detail_status = $db2->getrows();
		$detail_status = $_detail_status[0];
		//print_r($detail_status);

		if($admininfo[admin_level] == 9 || $admininfo[admin_level] == 8){
		
				/*$sql = "SELECT oid, uid, btel,bmobile,rmobile,bname, mem_group, bmail, rname, rtel, rmail, zip, addr, msg, return_message,return_date,
								UNIX_TIMESTAMP(date) AS date, method, bank, tid, authcode, gift,bank_input_name,bank_input_date,
								status, quick, deliverycode, total_price, use_reserve_price, payment_price,order_cancel_price, order_return_price, delivery_price,receipt_y,vb_info,use_cupon_price,taxsheet_yn,(select sum(delivery_price) from shop_order_delivery where o.oid = oid and delivery_pay_type in ('1','3') group by oid) as delivery_price1,(select sum(delivery_price) from shop_order_delivery where o.oid = oid and delivery_pay_type = 2 group by oid) as delivery_price2
								FROM service_order o
								WHERE o.oid = '".$oid."' ";*/
				
				/*
				$sql = "SELECT cu.id,o.*,UNIX_TIMESTAMP(o.date) AS date,(select sum(delivery_price) from shop_order_delivery where o.oid = oid and delivery_pay_type in ('1','3') group by oid) as delivery_price1,(select sum(delivery_price) from shop_order_delivery where o.oid = oid and delivery_pay_type = 2 group by oid) as delivery_price2
								FROM service_order o left join ".TBL_COMMON_USER." cu on o.uid=cu.code , ".TBL_COMMON_MEMBER_DETAIL." cmd
								WHERE o.oid = '".$oid."' and cu.code = cmd.code ";
				*/
				// 회원이나 상품 테이블을 조인하면 안됨... 2011.09.13
				$sql = "SELECT o.*,id,UNIX_TIMESTAMP(o.date) AS date, AES_DECRYPT(UNHEX(cd.mail),'".$db->ase_encrypt_key."') as mail,AES_DECRYPT(UNHEX(tel),'".$db->ase_encrypt_key."') as tel,AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs FROM service_order o left join ".TBL_COMMON_USER." c on c.code = o.code LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cd ON c.code=cd.code WHERE o.oid = '".$oid."' ";
		}

		//echo $sql;
		$db2->query($sql);
		$db2->fetch();

		

		$sattle_method = $db2->dt[method];
		$ucode = $db2->dt[code];
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
		}elseif($sattle_method == ORDER_METHOD_SAVEPRICE){
			$method = "예치금결제";
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
			$delete = "[<a href=\"javascript:alert(language_data['orders.edit.php']['B'][language]);\">삭제</a>]";//[처리완료] 기록은 삭제할 수 없습니다.
		}
		elseif ($db2->dt[status] != ORDER_STATUS_CANCEL_COMPLETE && $sattle_method == "1")
		{
			$delete = "[<a href=\"javascript:alert(language_data['orders.edit.php']['C'][language]);\">삭제</a>]";//[카드결제]는 [승인취소]와 [주문취소] 처리를 먼저한 후 삭제해주세요.
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
			$authcancel = $db2->dt[authcode]. "&nbsp;[<a href=\"javascript:alert(language_data['orders.edit.php']['D'][language]);\">승인취소</a>]";//[처리완료] 기록은 승인취소할 수 없습니다.
		}
		else
		{
			//$authcancel = $db2->dt[authcode]."&nbsp;[<a href=\"javascript:PoPWindow2('card_auth_cancel.php?tid=".$db2->dt[tid]."','400', '80','cancelwindow');\">승인취소</a>]";
			$authcancel = $db2->dt[authcode]."&nbsp;";

		}
	}

$Contents = $Contents."

      <div id='TG_order_edit' style='position: relative;width:100%;'>
		<form name='order_info_edit' method='post' onSubmit='return CheckFormValue(this)'  action='service_orders.act.php' target='act'>
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

					<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>주문정보</b></div>

					<table border='0' width='100%' cellspacing='1' cellpadding='0'>
						<tr>
							<td >
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='input_table_box' style='width:100%;'>
								<col width='15%' />
								<col width='35%' />
								<col width='15%' />
								<col width='35%' />
									<tr height=25 bgcolor='#ffffff' >
										<td class='input_box_title'>주문번호</td>
										<td class='input_box_item'>&nbsp;".$db2->dt[oid]."</td>
										<td class='input_box_title'>주문일자</td>
										<td class='input_box_item'>&nbsp;".showdate($db2->dt[date])."</td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class='input_box_title' >주문자이름</td>
										<td class='input_box_item'>&nbsp;<input type='text' size=25 name='bname' class='textbox' value='".$db2->dt[bname]."' validation='true' title='주문자이름' ></td>
										<td class='input_box_title'>주문자아이디</td>
										<td class='input_box_item'>&nbsp;".$db2->dt[id]."</td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class='input_box_title'>주문자메일</td>
										<td class='input_box_item' colspan='3'>&nbsp;".$db2->dt[mail]."</td>
									</tr>
									<tr bgcolor='#ffffff' >
										<td class='input_box_title'>주문자전화</td>
										<td  class='input_box_item'>&nbsp;".$db2->dt[tel]."</td>
										<td class='input_box_title'>주문자핸드폰</td>
										<td  class='input_box_item'>&nbsp;".$db2->dt[tel]."</td>
									</tr>
									<tr>
										<td class='input_box_title'><b>적립금 사용금액</b></td>
										<td class='input_box_item' colspan='3'>&nbsp; ".number_format($db2->dt[use_reserve_price])." P</td>
										<!--td class='input_box_title'><b>쿠폰사용금액</b></td>
										<td class='input_box_item'>&nbsp; ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[use_cupon_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td-->
									</tr>
									<tr>
										<td class='input_box_title'><b>총 상품금액</b></td>
										<td class='input_box_item'>&nbsp;".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[total_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
										<td class='input_box_title'><b>결제금액</b></td>
										<td class='input_box_item'>&nbsp; ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db2->dt[payment_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
									</tr>
									<tr>
										<td class='input_box_title'>입금확인일자</td>
										<td class='input_box_item'>&nbsp;<input type='text' size='12' maxlength=8 name='bank_input_date' class='textbox' value='".$db2->dt[bank_input_date]."' onkeyup='onlyEditableNumber(this)' style='ime-mode:disabled;' validation='false' title='입금확인일자'> 예) 20090806</td>
										<td class='input_box_title'>결제수단</td>
										<td  class='input_box_item' style='line-height:140%'>&nbsp; $method <span class=small>".($sattle_method == '0' ? "<br>".$db2->dt[bank]:"")."</span></td>
									</tr>
									<tr >
										<td class='input_box_title'>주문상태 변경내역</td>
										<td class='input_box_item' colspan='3' style='padding:10px 0 10px 10px'>
										<div style='width:100%;height:200px;overflow:auto;'>";
										if($admininfo[admin_level] == 9){
											$sql = "select os.regdate, os.status, os.status_message, od.pname
															from service_order_status os
															left join service_order_detail od on os.pid = od.pid and od.oid ='$oid'
															where os.oid ='$oid'  and os.status not in ('SR')
															order by os.regdate asc"; //and ((od.status in ('IR','IC') and od.pid = '') or (od.status not in ('IR','IC') and od.pid != ''))
											//echo nl2br($sql);
											$db3->query($sql);
										}else if($admininfo[admin_level] == 8){
											$sql = "select os.regdate, os.status, os.status_message, od.pname
															from service_order_status os, service_order_detail od
															where  os.oid ='$oid' and os.status not in ('SR')
															union
															select os.regdate, os.status, os.status_message, od.pname
															from service_order_status os, service_order_detail od
															where  os.oid ='$oid' and os.pid = od.pid and os.status not in ('SR')
															order by regdate asc"; //and ((od.status in ('IR','IC') and od.pid = '') or (od.status not in ('IR','IC') and od.pid != ''))
											//echo $sql;

											$db3->query($sql);
										}
										for($j = 0; $j < $db3->total; $j++)
										{
											$db3->fetch($j);
											$Contents .= "<span class=small>".$db3->dt[regdate]." ".getOrderStatus($db3->dt[status],$sattle_method)."  ".($db3->dt[pid] ? "(상품코드:".$db3->dt[pid]." - ".Cut_Str($db3->dt[pname],20,"...").")":"")." <span style='color:blue'>".($db3->dt[invoice_no].":" ? codeName($db3->dt[quick]).":":"")." ".($db3->dt[invoice_no] ? $db3->dt[invoice_no]:"")."</span> ".($db3->dt[company_name] ? "- 수정업체:".$db3->dt[company_name]."":"")." - <b>".$db3->dt[status_message]."</b></span><br>";
										}


					$Contents .= "		</div>
										</td>
									</tr>
									<tr height=60 bgcolor='white'>
										<td class='input_box_title'>전달사항</td>
										<td class='input_box_item' colspan=3 style='padding-left:5px; '>".nl2br($db2->dt[msg])."</td>
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

$Contents .= "

".OrderGoodsList($db2, $oid, "general")."
".OrderGoodsList($db2, $oid, "incom_after_cancel")."
<div style='height:20px;'></div>
		<table border='0' cellspacing='1' cellpadding='15' width='100%' bgcolor='#F8F9FA' bordercolor='#black'>
			<tr>
			  <td  style='padding:10px;'>
				<form name=order_memo_frm method=post action='service_orders_memo.act.php'  target='iframe_act'>
					<input type='hidden' name='act' value='memo_insert'>
					<input type='hidden' name='oid' value='$oid'>
					<input type='hidden' name='ucode' value='$ucode'>

					<table width=100%>
					 <tr>
						<td><img src='../images/dot_org.gif'  align='absmiddle'> <b class='middle_title'>주문 상담내역</b></td>
					</tr>
					<tr>
						<td bgcolor='#F8F9FA' style='padding:10px'><textarea style='height:50px;width:97%;' wrap='off'  basci_message=true name='memo' ></textarea></td>
					</tr>
					<tr>
						<td bgcolor='D0D0D0' height='1'></td>
					</tr>
					<tr><td align=right style='padding:10px;'> <input type=image src='../images/".$admininfo["language"]."/btn_counsel_save.gif' id='save_btn' border=0 align=absmiddle></td></tr>
					</table>
					</form>
					<table width=100%>
					<tr>
						<td align=right style='padding-top:10px;' id='design_history_area'>
						".PrintOrderMemo($oid)."
						</td>
					</tr>
					</table>

			</tr>
		</table>
      </div>

	</td>
  </tr>
</table>

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
	if(confirm(language_data['service_orders.edit.php']['A'][language])){//해당 상담내역을 정말로 삭제 하시겠습니까?
		window.frames['iframe_act'].location.href='service_orders_memo.act.php?act=memo_delete&oid='+oid+'&om_ix='+om_ix;
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
$(document).ready(function() {
	MessageBoxView();
});
function MessageBoxView(){
	var offsetX = 20;
	var offsetY = 10;
	
	/*$('a.messagebox').hover(function(e){
		//mouse on
		var msgbox = $(this).attr('messagebox_id');
		//alert($(msgbox).parent().html());
		$('#'+msgbox).css('display','block');
		$('#'+msgbox).css('top', e.pageY + offsetY).css('left', e.pageX + offsetX).appendTo('body');
	}, function(){
		//mouse off
		//$('.messagebox_contents').remove();
		$('.messagebox_contents').css('display','none');
	});
	
	$('a.messagebox').mousemove(function(e){
		$('.messagebox_contents').css('top', e.pageY + offsetY).css('left', e.pageX + offsetX);
	});*/
	$('a.messagebox').click(function(e){
		var msgbox = $(this).attr('messagebox_id');
		//alert($(msgbox).parent().html());
		$('#'+msgbox).css('display','block');
		$('#'+msgbox).css('top', e.pageY + offsetY).css('left', e.pageX + offsetX).appendTo('body');
		$('#'+msgbox+' .messagebox_x').click(function() {
			$('#'+msgbox).css('display','none');
		});
	});
		
}
</script>
<style type='text/css'>
a img {
	border: none;
}

.messagebox_contents {
	position: absolute;
	padding: .5em;
	background: #e3e3e3;
	border: 1px solid;
}
</style>
";


$P = new LayOut();
$P->strLeftMenu = service_menu();
$P->addScript = $Script."<script language='javascript' src='service_orders.js'></script>";
$P->Navigation = "서비스관리 > 서비스주문정보수정";
$P->title = "서비스주문정보수정";
$P->strContents = $Contents;


echo $P->PrintLayOut();

function OrderGoodsList($db2, $oid, $list_type = "general"){
	global $admininfo , $admin_config,$db1, $db3, $currency_display;
	global $auth_write_msg, $auth_update_msg, $auth_delete_msg;
	//return "";

	$addWhere = " and od.status in ('".implode("','",getStatusByType($list_type))."') ";
	
	if($admininfo[admin_level] == 9){//quick, ptprice, od.option_text, po.option_etc1,invoice_no, od.status , od.coprice,od.delivery_type, od.ac_date, od.ac_ix, od.dc_date
		/*if($admininfo[mem_type] == "MD"){
			$addWhere .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}*/

		$sql = "SELECT od.od_ix,od.pid, od.pname,  psprice, ptprice, od.status ,od.coprice FROM service_order_detail od WHERE od.oid = '".$oid."' $addWhere ";
	}else if($admininfo[admin_level] == 8){
		/*
		$sql = "SELECT od.od_ix,od.pid,od.product_type, od.pname, od.reserve, pcnt, psprice, ptprice, od.option_text, od.option_etc, od.status , od.use_coupon, od.use_coupon_code,
						od.coprice, od.invoice_no, od.quick, od.ac_date, od.ac_ix, od.dc_date,od.option_price, od.company_name, od.company_id,od.ra_date,ea_date,return_reason,return_invoice_no, odd.order_type
						FROM service_order_detail od LEFT JOIN shop_order_detail_deliveryinfo odd ON od.od_ix=odd.od_ix
						WHERE od.oid = '".$oid."' and od.company_id = '".$admininfo[company_id]."' ";
		*/
		//$addWhere .= "  and od.company_id = '".$admininfo[company_id]."'";

		$sql = "SELECT od.od_ix,od.pid,od.pname, psprice, ptprice, od.status , od.coprice FROM service_order_detail od WHERE od.oid = '".$oid."' $addWhere ";

	}
	//echo nl2br($sql);
	//exit;
	$db3->query($sql);

	if($db3->total == 0){
		return ;
		//exit;
	}


$Contents = "<div style='height:20px;'></div>";

if($list_type == "general"){
	$Contents .= "<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>주문상품정보(정상)</b></div>";
}else if($list_type == "incom_after_cancel"){
	$Contents .= "<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>주문상품정보(취소)</b></div>";
}else if($list_type == "return"){
	$Contents .= "<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>주문상품정보(반품/환불)</b></div>";
}else if($list_type == "exchange"){
	$Contents .= "<div style='padding:5px'><img src='../images/dot_org.gif' align='absmiddle'> <b class='middle_title'>주문상품정보(교환)</b></div>";
}

$Contents .= "
<form name='order_edit_".$list_type."' method='post' onSubmit=\"return orderStatusUpdate(this)\"  action='service_orders.act.php' target='act'><!--target='act'-->
<input type=hidden name=oid value='$oid'>
<input type=hidden name=act value='update'>
<input type=hidden name=bstatus value='".$db2->dt[status]."'>
<table width='100%' border='0' cellpadding='0' cellspacing='1' bgcolor=silver>
		<tr bgcolor='#ffffff' height=40>
		<td style='padding:0 0 0 10px'>
		<table border=0><tr><td>";

if($list_type == "general"){
	$Contents .= "
					<select name='status' onchange='ViewdeliveryCodeInputBox(this.value,document.order_edit)'>
						<option value='' >상태변경</option>
						<option value='".ORDER_STATUS_INCOM_READY."' >".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option>
						<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
						<option value='".ORDER_STATUS_CANCEL_APPLY."' >".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</option>
						<option value='".ORDER_STATUS_CANCEL_COMPLETE."' >".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>";
					
					$Contents .= "</select>";
}else if($list_type == "incom_after_cancel"){
	$Contents .= "
					<select name='status' onchange='ViewdeliveryCodeInputBox(this.value,document.order_edit)'>
						<option value='' >상태변경</option>
						<option value='".ORDER_STATUS_INCOM_READY."' >".getOrderStatus(ORDER_STATUS_INCOM_READY)."</option>
						<option value='".ORDER_STATUS_INCOM_COMPLETE."' >".getOrderStatus(ORDER_STATUS_INCOM_COMPLETE)."</option>
						<option value='".ORDER_STATUS_CANCEL_APPLY."' >".getOrderStatus(ORDER_STATUS_CANCEL_APPLY)."</option>
						<option value='".ORDER_STATUS_CANCEL_COMPLETE."' >".getOrderStatus(ORDER_STATUS_CANCEL_COMPLETE)."</option>";
					
					$Contents .= "</select>";
}else if($list_type == "return"){
	$Contents .= "
					<select name='status' onchange='ViewdeliveryCodeInputBox(this.value,document.order_edit)'>
						<option value='' >상태변경</option>
					</select>";
}else if($list_type == "exchange"){
	$Contents .= "
					<select name='status' onchange='ViewdeliveryCodeInputBox(this.value,document.order_edit)'>
						<option value='' >상태변경</option>
					</select>";
}


$Contents = $Contents."
		</td>";
if ($db2->dt[status] == ORDER_STATUS_DELIVERY_COMPLETE || $db2->dt[status] == ORDER_STATUS_DELIVERY_ING){
$Contents = $Contents."
		<td>
		".deliveryCompanyList($db2->dt[quick],"select")."
		</td>
		<td   nowrap><div id='deliverycode' style='display:inline'>송장번호 : <input type='text' name='deliverycode' class='textbox'  size=15 value='".$db2->dt[deliverycode]."'></div></td>
		<td> </td>";
}else{
$Contents = $Contents."
		<td>
		".deliveryCompanyList($db2->dt[quick],"select","style='display:none'")."
		</td>
		<td><!--div id='deliverycode' style='display:none'>송장번호 : <input type='text' name='deliverycode' class=textbox size=15></div--></td>
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
			//if($db2->dt[status]!=ORDER_STATUS_CANCEL_COMPLETE) {
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents .= " <input type=image src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 style='cursor:pointer;'>";
				}else{
					$Contents .= " <a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 style='cursor:pointer;'></a>";
				}
			//} else {
			//	$Contents .= "";
			//}
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
											<td width='3%' class='s_td'><input type=checkbox  name='all_fix' onclick='fixAll(document.order_edit_".$list_type.")' checked></td>
											<!--td width='10%' class='m_td class'><b>상품코드</b></td-->
											<td width='*' colspan=2 class='m_td small'><b>상품명</b></td>
											<!--td width='5%' class='m_td small'><b>수량</b></td>
											<td width='15%' class='m_td small'><b>옵션</b></td>
											<td width='5%' class='m_td small'><b>전달사항</b></td-->
											<td width='10%' class='m_td small'><b>쿠폰사용</b></td>";
if($admininfo[mall_use_multishop]){
$Contents .=	"							<td width='7%' class='m_td small'><b>공급가</b></td>";
$Contents .=	"							<td width='11%' class='m_td small'><b>상태</b></td>";
$Contents .=	"							<td width='5%'  class='m_td small'><b>단가</b></td>
											<td width='5%' class='m_td small'><b>적립금</b></td>";
}else{
$Contents .=	"							<td width='10%'  class='m_td small'><b>단가</b></td>
											<td width='10%' class='m_td small'><b>적립금</b></td>";
}

$Contents .=	"

											<td width='8%' class='e_td small'><b>합계</b></td>

										</tr>";

	


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
											<td nowrap><input type=checkbox name='od_ix[]' id='oid' class='od_ix' od_status='".$db3->dt[status]."' value='".$db3->dt[od_ix]."' checked></td>
											<td style='padding:3px 0px;'><img src='".PrintImage($admin_config[mall_data_root]."/images/service_product", $db3->dt[pid], "c")."' width=50></td>
											<td align='left' style='padding:5px 0 5px 0;line-height:130%'>";
										/*if($admininfo[admin_level] == 9 && $admininfo[mall_use_multishop]){
											$Contents .= "<a href=\"javascript:PoPWindow('../seller/company.add.php?company_id=".$db3->dt[company_id]."&mmode=pop',960,600,'brand')\"><b>".($db3->dt[company_name] ? $db3->dt[company_name]:"-")."</b></a><br>";
										}*/
										if(in_array($product_type,$arr_sns_ptype)){
$Contents .= "								<a href=\"/service/service_goods_view.php?id=".$db3->dt[pid]."\" target=_blank>".$pname."</a>";
										} else {
$Contents .= "								<a href=\"/service/service_goods_view.php?id=".$db3->dt[pid]."\" target=_blank>".$pname."</a>";
										}

$Contents .= "								</td>
											<!--td >".$count." 개</td>
											<td align=left style='padding:7px 5px;'>".$option_div."".($option_price != '' ? " + ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($option_price)."".$currency_display[$admin_config["currency_unit"]]["back"]."":"")."</td>
											<td align=center>".$msgbyproduct."</td-->
											<td align=center>".number_format($db3->dt[use_coupon])." ".($db3->dt[use_coupon] > 0 ? "<br><a href=\"javascript:PopSWindow('../display/cupon_publish.php?mmode=pop&regist_ix=".$db3->dt[use_coupon_code]."',900,700,'cupon_detail_pop');\" class=blue>쿠폰확인</a>":"")."</td>";
if($admininfo[mall_use_multishop]){
$Contents .= "				<td align=center> ".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db3->dt[coprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";
$Contents .= "				<td align=center style='line-height:130%'>";
if($db3->dt[status]==ORDER_STATUS_EXCHANGE_DELIVERY && $db3->dt[order_type]>0) $Contents .= "<a href=\"javascript:PopSWindow('/admin/order/orders.view_address.php?oid=".$oid."&od_ix=".$db3->dt[od_ix]."&type=".$db3->dt[status]."',500,400,'orders_view_address')\">";
$Contents .=getOrderStatus($db3->dt[status],$sattle_method);
if($db3->dt[status]==ORDER_STATUS_EXCHANGE_DELIVERY && $db3->dt[order_type]>0) $Contents .= "</a>";
if($db3->dt[ra_date] && $db3->dt[status] == "RA"){
	$Contents .= "<br />(".substr($db3->dt[ra_date],0,10).")";
}
if($db3->dt[ea_date] && $db3->dt[status] == "EA"){
	$Contents .= "<br />(".substr($db3->dt[ea_date],0,10).")";

}
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

	$Contents .= "<br /><a href='javascript:void(0)' onclick=\"return_pop(".$db3->dt[od_ix].")\"><img src='../image/btn_return_reason.gif'></a>";

}

if($db3->dt[ea_date] && ($db3->dt[status] == "EA")&& $db3->dt[return_reason] != ""){

	$Contents .= "<br /><a href='javascript:void(0)' onclick=\"return_pop(".$db3->dt[od_ix].")\"><img src='../images/".$admininfo["language"]."/btn_exchange_reason.gif'></a> <a class='messagebox' messagebox_id='messagebox_".$db3->dt[od_ix]."'><img src='../images/".$admininfo["language"]."/btn_exchange_addr.gif'></a>";
	$sql="SELECT * FROM shop_order_detail_deliveryinfo WHERE od_ix='".$db3->dt[od_ix]."' ";
	$db1->query($sql);
	$db1->fetch();
	$Contents .= "<table id='messagebox_".$db3->dt[od_ix]."' style='display:none;' class='messagebox_contents'>
					<tr>
						<td>받는사람 : </td>
						<td>".$db1->dt[rname]."</td>
					</tr>
					<tr>
						<td>받는 전화번호 : </td>
						<td>".$db1->dt[rtel]."</td>
					</tr>
					<tr>
						<td>받는 핸드폰 : </td>
						<td>".$db1->dt[rmobile]."</td>
					</tr>
					<tr>
						<td>받는 주소 : </td>
						<td>[".$db1->dt[zip]."] ".$db1->dt[addr1]."  ".$db1->dt[addr2]."</td>
					</tr>
					<tr>
						<td colspan='2' align='right' style='font-size:13px;font-family:Arial;font-weight:600;color:#A72525;'><span style='cursor:pointer;' class='messagebox_x'>X</span></td>
					</tr>
				</table>";

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
            
        </form>";

	return $Contents;
}

function PrintOrderMemo($oid){
	global $admininfo, $page, $nset, $QUERY_STRING;
	$mdb = new Database;

	$sql = "select count(*) as total from service_order_memo where oid ='$oid'    ";
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


	$mString = "<table cellpadding=4 cellspacing=0 width=100% bgcolor=silver class='list_table_box'>";
	$mString = $mString."
				<form name=listform method=post action='service_orders.act.php' onsubmit='return CheckDelete(this)' target='iframe_act'>
				<input type='hidden' name='act' value='memo_insert'>
				<input type='hidden' name='oid' value='$oid'>
				<col width='15%'>
				<col width='10%'>
				<col width='*'>
				<col width='10%'>
				<tr align=center bgcolor=#efefef height=30>
					<!--td class=s_td width='5%'><input type=checkbox class=nonborder id='all_fix' name='all_fix' onclick='fixAll(document.listform)'>&nbsp;</td-->
					<td class=s_td>상담일자</td>
					<td class=m_td>상담자</td>
					<td class=m_td>상담내용</td>
					<td class=e_td>관리</td>
				</tr>";
	if ($total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=4 align=center><!--주문 상담내역이  존재 하지 않습니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</td></tr>";
	}else{

		$mdb->query("select * from service_order_memo where oid ='$oid' order by regdate desc   limit $start , $max");

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);

			$mString = $mString."<tr height=45 bgcolor=#ffffff align=center>
			<!--td bgcolor='#ffffff'><input type=checkbox class=nonborder id='om_ix' name='om_ix[]' value='".$mdb->dt[om_ix]."'></td-->
			<td class='list_box_td list_bg_gray'>".$mdb->dt[regdate]."</td>
			<td class='list_box_td'>".$mdb->dt[counselor]."</td>
			<td class='list_box_td point' align=left style='text-align:left;padding:10px;' style='word-break:break-all'>".nl2br($mdb->dt[memo])."</td>
			<td class='list_box_td' align=center nowrap>

				<a href=JavaScript:memoDelete('".$mdb->dt[oid]."','".$mdb->dt[om_ix]."')><img  src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
			</td>
			</tr>
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

	$mdb->query("select * from service_order_status where oid = '$oid' ");

	for($i=0;$i < $db->total;$i++){
		$mdb->fetch($i);
		$mstring .= $mdb->dt[regdate] ." - ". getOrderStatus($mdb->dt[status])."<br>";
	}

	return $mstring;
}



?>