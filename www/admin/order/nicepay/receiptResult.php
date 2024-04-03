<?php

/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/
	require($_SERVER["DOCUMENT_ROOT"]."/shop/nicepay/lib/NicepayLite.php");
	
	/***************************************
	 * 2. NicepayLite 클래스의 인스턴스 생성 *
	 ***************************************/
	$nicepay = new NicepayLite;
	$nicepay->m_ActionType = "PYO";

	// 로그를 저장할 디렉토리를 설정하십시요. 설정한 디렉토리의 하위 log폴더에 생성됩니다.
	$nicepay->m_NicepayHome = $_SERVER["DOCUMENT_ROOT"]."/admin/order/nicepay";
	
	$nicepay->m_PayMethod = "RECEIPT";
	$nicepay->m_GoodsName = iconv("UTF-8","CP949",$GoodsName);
	$nicepay->m_BuyerName = iconv("UTF-8","CP949",$BuyerName);
	$nicepay->m_MID = $MID;
	
	$nicepay->m_ReceiptAmt = $ReceiptAmt;
	$nicepay->m_ReceiptSupplyAmt = $ReceiptSupplyAmt;
	$nicepay->m_ReceiptVAT = $ReceiptVAT;
	$nicepay->m_ReceiptServiceAmt = $ReceiptServiceAmt;
	$nicepay->m_Amt = $ReceiptAmt;
	
	$nicepay->m_ReceiptType = $ReceiptType;
	$nicepay->m_ReceiptTypeNo = $ReceiptTypeNo;
	
	$nicepay->m_CancelPwd = "123456";
	$nicepay->m_ssl = "true";
	// 상점키를 설정합니다.

	$nicepay->m_LicenseKey = $MERCHANTKEY;

	// PG에 접속하여 취소 처리를 진행.
	$nicepay->startAction();
	
	$AuthDate=str_replace("-","",$nicepay->m_ResultData["AuthDate"]);
	$AuthDate=str_replace(":","",$AuthDate);
	$AuthDate=str_replace(" ","",$AuthDate);
	$m_pgAuthDate=substr($AuthDate,0,8);
	$m_pgAuthTime=substr($AuthDate,8,6);

	if($nicepay->m_ResultData["ResultCode"]==7001) {
		include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
		
		/*
		$RcptType=$nicepay->m_ResultData["RcptType"];// 현금영수증 타입 0:발행안함,1:소득공제,2:지출증빙
		$m_tid=$nicepay->m_ResultData["RcptTID"];//현금영수증 TID
		$cash_authno=$nicepay->m_ResultData["RcptAuthCode"];//현금영수증 승인번호
		*/

		if($ReceiptType==1 || $ReceiptType==2) {
			if($ReceiptType==1) $cash_tr_code=0;
			else $cash_tr_code=1;
		}

		/*$db = new Database;
		$db->query("insert into receipt_result (oid,m_resultMsg, m_rcash_noappl, m_pgAuthDate, m_pgAuthTime,m_tid,m_rcr_price, m_rsup_price, m_rtax,m_rsrvc_price, m_ruseopt, regdate) values ('".$ordr_idxx."','00','','".$m_pgAuthDate."','".$m_pgAuthTime."','".$nicepay->m_ResultData["TID"]."','".$ReceiptAmt."','".$ReceiptSupplyAmt."','".$ReceiptVAT."','','".$ReceiptType."',NOW())");

		$db->query("update ".TBL_MALLSTORY_RECEIPT." set receipt_yn = 'Y' where order_no = '".$ordr_idxx."' ");*/
		$db = new Database;
		$sql = "insert into receipt_result (oid,m_resultMsg, m_rcash_noappl,m_pgAuthDate, m_pgAuthTime ,m_tid,m_payment_price,m_rcr_price, m_rsup_price, m_rtax,m_rsrvc_price,m_ruseopt,regdate) values ('".$ordr_idxx."','".iconv("CP949","UTF-8",$nicepay->m_ResultData["ResultMsg"])."','".$cash_authno."','".$m_pgAuthDate."','".$m_pgAuthTime."','".$nicepay->m_ResultData["TID"]."','".$ReceiptAmt."','".$ReceiptAmt."','".$ReceiptSupplyAmt."','".$ReceiptVAT."','','".$cash_tr_code."',NOW())";
		$db->query($sql);
		$sql = "update receipt set receipt_yn = 'C' where order_no = '".$ordr_idxx."' ";
		$db->query($sql);

		$sql = "update ".TBL_SHOP_ORDER." set tax_affairs_yn = 'Y' where oid = '".$ordr_idxx."' ";
		$db->query($sql);
	}
?>

<html>
<head>
<title>Nicepay 현금영수증 발행</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel="stylesheet" href="css/group.css" type="text/css">
<style>
body, tr, td {font-size:9pt; font-family:굴림,verdana; color:#433F37; line-height:19px;}
table, img {border:none}

/* Padding ******/ 
.pl_01 {padding:1 10 0 10; line-height:19px;}
.pl_03 {font-size:20pt; font-family:굴림,verdana; color:#FFFFFF; line-height:29px;}

/* Link ******/ 
.a:link  {font-size:9pt; color:#333333; text-decoration:none}
.a:visited { font-size:9pt; color:#333333; text-decoration:none}
.a:hover  {font-size:9pt; color:#0174CD; text-decoration:underline}

.txt_03a:link  {font-size: 8pt;line-height:18px;color:#333333; text-decoration:none}
.txt_03a:visited {font-size: 8pt;line-height:18px;color:#333333; text-decoration:none}
.txt_03a:hover  {font-size: 8pt;line-height:18px;color:#EC5900; text-decoration:underline}
</style>
<script>
opener.document.location.reload();
</script>


</head>
<body bgcolor="#FFFFFF" text="#242424" leftmargin=0 topmargin=15 marginwidth=0 marginheight=0 bottommargin=0 rightmargin=0><center>
<TABLE cellSpacing=0 cellPadding=0 width="100%" align=center border=0>
	<TR>		
		<td align=center colspan=2>
		<table border="0" width="100%" cellpadding="0" cellspacing="1" align="center">	
			<tr><td  align=left class='top_orange'  ></td></tr>
			<tr height=35 bgcolor=#efefef>
				<td  style='padding:0 0 0 0;'> 
					<table width='100%' border='0' cellspacing='0' cellpadding='0' >
						<tr> 
							<td width='10%' height='31' valign='middle' style='font-family:굴림;font-size:16px;font-weight:bold;letter-spacing:-2px;word-spacing:0px;padding-left:10px;' nowrap><!--border-bottom:2px solid #ff9b00;-->
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 현금영수증신청완료
							</td>
							<td width='90%' align='right' valign='top' ><!--style='border-bottom:2px solid #efefef;'-->
								&nbsp;
							</td>
						</tr>
						<!--tr height=10><td colspan=2></td></tr-->
					</table>
				</td>
			</tr>
			
			<tr>				
				<td align=center style='padding: 0 10 0 10'>
				<table border='0' width='100%' cellspacing='0' cellpadding='5' >
					<tr>
						<td >	
						<table border='0' width='100%' cellspacing='1' cellpadding='0' style='border:5px solid #F8F9FA'>
							<tr>
								<td >
									<table border='0' width='100%' cellspacing='1' cellpadding='4' bgcolor='#c0c0c0'>
										<tr>
											<td class=leftmenu width=90 align='left' style='color:#cc0000'><img src='../../image/title_head.gif' > 주문번호</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$ordr_idxx?></td>
										</tr>
										<tr>
											<td class=leftmenu width=90 align='left' ><img src='../../image/title_head.gif' > 결과코드</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$nicepay->m_ResultData["ResultCode"]?></td>
										</tr>
										<tr>
											<td class=leftmenu width=90 align='left' ><img src='../../image/title_head.gif' > 결과내용</td>
											<td bgcolor='#ffffff'>&nbsp;<?=iconv("CP949","UTF-8",$nicepay->m_ResultData["ResultMsg"])?></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../../image/title_head.gif' > 거래번호</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$nicepay->m_ResultData["TID"]?></td>
											
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../../image/title_head.gif' > 승인날짜</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$m_pgAuthDate?>
											</td>
											
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../../image/title_head.gif' > 승인시간</td>
											<td bgcolor='#ffffff' >&nbsp;<?=$m_pgAuthTime?>
											</td>					
										</tr>
										
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<span id="show_pay_btn">
										<input type="button" value="닫기"  class="box" onclick="self.close()">
									</span>
									
								</td>
							</tr>
						</table><br>
			</td>
			
  		</tr>

  		</table>
		
				
		</td>
	</tr>
	
</TABLE>


</center></body>
</html>
