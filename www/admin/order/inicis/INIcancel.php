<?php
/* INIcancel.php
 *
 * 이미 승인된 지불을 취소한다.
 * 은행계좌 이체 , 무통장입금은 이 모듈을 통해 취소 불가능.
 *  [은행계좌이체는 상점정산 조회페이지 (https://iniweb.inicis.com)를 통해 취소 환불 가능하며, 무통장입금은 취소 기능이 없습니다.]  
 *  
 * Date : 2007/09
 * Author : ts@inicis.com
 * Project : INIpay V5.0 for PHP
 * 
 * http://www.inicis.com
 * Copyright (C) 2007 Inicis, Co. All rights reserved.
 */


	/**************************
	 * 1. 라이브러리 인클루드 *
	 **************************/
	require("./libs/INILib.php");
	
	/***************************************
	 * 2. INIpay41 클래스의 인스턴스 생성 *
	 ***************************************/
	$inipay = new INIpay50;
	
	/*********************
	 * 3. 취소 정보 설정 *
	 *********************/
  	$inipay->SetField("inipayhome", "/home/sigongweb/INIpay50"); // 이니페이 홈디렉터리(상점수정 필요)
  	$inipay->SetField("type", "cancel");                            // 고정 (절대 수정 불가)
  	$inipay->SetField("debug", "true");                             // 로그모드("true"로 설정하면 상세로그가 생성됨.)
	$inipay->SetField("mid", $mid);                                 // 상점아이디
    /**************************************************************************************************
     * admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
     * 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
     * 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
     * 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
     **************************************************************************************************/
  	$inipay->SetField("admin", "1111");                             //비대칭 사용키 키패스워드
	$inipay->SetField("tid", $tid);                                 // 취소할 거래의 거래아이디
	$inipay->SetField("cancelmsg", $msg);                           // 취소사유

	/****************
	 * 4. 취소 요청 *
	 ****************/
	$inipay->startAction();
	
	
	/****************************************************************
	 * 5. 취소 결과                                           	*
	 *                                                        	*
	 * 결과코드 : $inipay->getResult('ResultCode') ("00"이면 취소 성공)  	*
	 * 결과내용 : $inipay->getResult('ResultMsg') (취소결과에 대한 설명) 	*
	 * 취소날짜 : $inipay->getResult('CancelDate') (YYYYMMDD)          	*
	 * 취소시각 : $inipay->getResult('CancelTime') (HHMMSS)            	*
	 * 현금영수증 취소 승인번호 : $inipay->getResult('CSHR_CancelNum')    *
	 * (현금영수증 발급 취소시에만 리턴됨)                          * 
	 ****************************************************************/
	if( $inipay->getResult('ResultCode') == "00"){
		include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

		$db = new Database;

		$db->query("update receipt set receipt_yn = 'E' where order_no = '".$ordr_idxx."' ");
		
		$db->query("select * from receipt_result where oid = '".$ordr_idxx."' ");
		$db->fetch();
		$rprice = $db->dt[m_payment_price];

		$db->query("insert into receipt_modify_history (mh_ix,tid,modify_type,company_id,admin_id,rprice,price,regdate) values('','$tid','','".$_SESSION["admininfo"]["company_id"]."','".$_SESSION["admininfo"]["charger"]."(".$_SESSION["admininfo"]["charger_id"].")','$rprice','0',NOW())");
	}
?>

<html>
<head>
<title>INIpayTX50 취소요청 페이지 샘플</title>
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

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
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
											<td bgcolor='#ffffff'>&nbsp;<?=$inipay->GetResult('ResultCode')?></td>
										</tr>
										<tr>
											<td class=leftmenu width=90 align='left' ><img src='../../image/title_head.gif' > 결과내용</td>
											<td bgcolor='#ffffff'>&nbsp;<?=iconv("CP949","UTF-8",$inipay->GetResult('ResultMsg'))?></td>
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../../image/title_head.gif' > 거래번호</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$tid?></td>
											
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../../image/title_head.gif' > 취소날짜</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$inipay->GetResult('CancelDate')?>
											</td>
											
										</tr>
										<tr>
											<td class=leftmenu align='left' ><img src='../../image/title_head.gif' > 취소시간</td>
											<td bgcolor='#ffffff' >&nbsp;<?=$inipay->GetResult('CancelTime')?>
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
