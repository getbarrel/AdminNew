<?php

/* INIreceipt.php
 *
 * 현금결제(실시간 은행계좌이체, 무통장입금)에 대한 현금결제 영수증 발행 요청한다.
 *
 *
 * http://www.inicis.com
 * http://support.inicis.com
 * Copyright (C) 2006 Inicis, Co. All rights reserved.
 */


	/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/
	//require("./libs/INILib.php");

	require($_SERVER["DOCUMENT_ROOT"]."/shop/inicis/libs/INILib.php");


	/***************************************
	 * 2. INIpay41 클래스의 인스턴스 생성 *
	 ***************************************/
	$inipay = new INIpay50;

	/*********************
	 * 3. 발급 정보 설정 *
	 *********************/
	$inipay->SetField("inipayhome"    ,$_SERVER["DOCUMENT_ROOT"]."/shop/inicis/");	// 이니페이 홈디렉터리
	$inipay->SetField("type"          ,"receipt"); 					// 고정
	$inipay->SetField("pgid"          ,"INIphpRECP"); 			// 고정
	$inipay->SetField("paymethod"     ,"CASH");					    // 고정 (요청분류)
	$inipay->SetField("currency"      ,$currency);				  // 화폐단위 (고정)
    /**************************************************************************************************
     * admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
     * 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
     * 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
     * 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
     **************************************************************************************************/
	$inipay->SetField("admin"         ,"1111"); 					  // 키패스워드(상점아이디에 따라 변경)
	$inipay->SetField("debug"         ,"true"); 					  // 로그모드("true"로 설정하면 상세로그가 생성됨.)
	$inipay->SetField("mid"           ,$mid); 						  // 상점아이디
	$inipay->SetField("goodname"      ,iconv("utf-8","CP949",$goodname));				// 상품명
	$inipay->SetField("cr_price"      ,$cr_price);				// 총 현금결제 금액
	$inipay->SetField("sup_price"     ,$sup_price);				// 공급가액
	$inipay->SetField("tax"           ,$tax);						  // 부가세
	$inipay->SetField("srvc_price"    ,$srvc_price);			// 봉사료
	$inipay->SetField("buyername"     ,iconv("utf-8","CP949",$buyername));				// 구매자 성명
	$inipay->SetField("buyeremail"    ,$buyeremail);			// 구매자 이메일 주소
	$inipay->SetField("buyertel"      ,$buyertel);				// 구매자 전화번호
	$inipay->SetField("reg_num"       ,$reg_num);					// 현금결제자 주민등록번호
	$inipay->SetField("useopt"        ,$useopt);					// 현금영수증 발행용도 ("0" - 소비자 소득공제용, "1" - 사업자 지출증빙용)
	$inipay->SetField("companynumber" ,$companynumber);


	/****************
	 * 4. 발급 요청 *
	 ****************/
	$inipay->startAction();


	/********************************************************************************
	 * 5. 발급 결과                           	                 		*
	 *                                              	         		*
	 * 결과코드 : $inipay->GetResult('ResultCode') ("00" 이면 발행 성공)	 		*
	 * 승인번호 : $inipay->GetResult('ApplNum') (현금영수증 발행 승인번호) 		*
	 * 승인날짜 : $inipay->GetResult('ApplDate') (YYYYMMDD)              	 		*
	 * 승인시각 : $inipay->GetResult('ApplTime') (HHMMSS)                	 		*
	 * 거래번호 : $inipay->GetResult('TID')				    	 		*
	 * 총현금결제 금액 : $inipay->GetResult('CSHR_ApplPrice')			    	 		*
	 * 공급가액 : $inipay->GetResult('CSHR_SupplyPrice')		    	    	 		*
	 * 부가세 : $inipay->GetResult('CSHR_Tax')				    	 		*
	 * 봉사료 : $inipay->GetResult('CSHR_ServicePrice')			    	 		*
	 * 사용구분 : $inipay->GetResult('CSHR_Type')                              	 		*
	 ********************************************************************************/
	if($inipay->GetResult('ResultCode') == "00"){
		include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

		$db = new Database;
		$db->query("insert into receipt_result (oid,m_resultMsg, m_rcash_noappl, m_pgAuthDate, m_pgAuthTime,m_tid,m_payment_price,m_rcr_price, m_rsup_price, m_rtax,m_rsrvc_price, m_ruseopt, regdate) values ('".$ordr_idxx."','".$inipay->GetResult('ResultCode')."','".$inipay->GetResult('ApplNum')."','".$inipay->GetResult('ApplDate')."','".$inipay->GetResult('ApplTime')."','".$inipay->GetResult('TID')."','".$cr_price."','".$inipay->GetResult('CSHR_ApplPrice')."','".$inipay->GetResult('CSHR_SupplyPrice')."','".$inipay->GetResult('CSHR_Tax')."','".$inipay->GetResult('CSHR_ServicePrice')."','".$inipay->GetResult('CSHR_Type')."',NOW())");

		$db->query("update receipt set receipt_yn = 'C' where order_no = '".$ordr_idxx."' ");
	}
?>

<html>
<head>
<title>INIpay50 현금영수증 발행</title>
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


function showreceipt() // 현금 영수증 출력
{
	var showreceiptUrl = "https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/Cash_mCmReceipt.jsp?noTid=<?php echo($inipay->GetResult('TID')); ?>" + "&clpaymethod=22";
	window.open(showreceiptUrl,"showreceipt","width=380,height=540, scrollbars=no,resizable=no");
}



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
											<td bgcolor='#ffffff'>&nbsp;<?=$db->dt[order_no]?></td>
										</tr>
										<tr>
											<td class=leftmenu width=90 align='left' ><img src='../../image/title_head.gif' > 결과코드</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$inipay->GetResult('ResultCode')?></td>
										</tr>
										<tr>
											<td class=leftmenu width=90 align='left' ><img src='../../image/title_head.gif' > 결과내용</td>
											<td bgcolor='#ffffff'>&nbsp;<?=iconv("CP949","UTF-8",$inipay->GetResult('ResultMsg'))?></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../../image/title_head.gif' > 거래번호</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$inipay->GetResult('TID')?></td>

										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../../image/title_head.gif' > 승인날짜</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$inipay->GetResult('ApplNum')?>
											</td>

										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../../image/title_head.gif' > 승인시간</td>
											<td bgcolor='#ffffff' >&nbsp;<?=$inipay->GetResult('ApplTime')?>
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
