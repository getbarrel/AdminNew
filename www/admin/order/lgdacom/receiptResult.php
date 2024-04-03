<?php
/*
lgdacom 현금영수증 발행/취소 kbk 13/06/05
*/
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
		$db = new Database;
    /*
     * [현금영수증 발급 요청 페이지]
     *
     * 파라미터 전달시 POST를 사용하세요
     */
    $CST_PLATFORM          = $HTTP_POST_VARS["CST_PLATFORM"];       		//LG텔레콤 결제 서비스 선택(test:테스트, service:서비스)
    $CST_MID                    = $HTTP_POST_VARS["CST_MID"];            		//상점아이디(LG텔레콤으로 부터 발급받으신 상점아이디를 입력하세요)
                                                                         		//테스트 아이디는 't'를 반드시 제외하고 입력하세요.
    $LGD_MID                    = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;  //상점아이디(자동생성)    
    $LGD_TID                	= $HTTP_POST_VARS["LGD_TID"];			 		//LG텔레콤으로 부터 내려받은 거래번호(LGD_TID)
    
	$LGD_METHOD   		    	= $HTTP_POST_VARS["LGD_METHOD"];                //메소드('AUTH':승인, 'CANCEL' 취소)
    $LGD_OID                	= $HTTP_POST_VARS["LGD_OID"];					//주문번호(상점정의 유니크한 주문번호를 입력하세요)
    $LGD_PAYTYPE                = $HTTP_POST_VARS["LGD_PAYTYPE"];				//결제수단 코드 (SC0030:계좌이체, SC0040:가상계좌, SC0100:무통장입금 단독)
    $LGD_AMOUNT     		    = $HTTP_POST_VARS["LGD_AMOUNT"];            	//금액("," 를 제외한 금액을 입력하세요)
    $LGD_CASHCARDNUM        	= $HTTP_POST_VARS["LGD_CASHCARDNUM"];           //발급번호(주민등록번호,현금영수증카드번호,휴대폰번호 등등)
    $LGD_CUSTOM_MERTNAME 		= $HTTP_POST_VARS["LGD_CUSTOM_MERTNAME"];    	//상점명
    $LGD_CUSTOM_BUSINESSNUM 	= $HTTP_POST_VARS["LGD_CUSTOM_BUSINESSNUM"];    //사업자등록번호
    $LGD_CUSTOM_MERTPHONE 		= $HTTP_POST_VARS["LGD_CUSTOM_MERTPHONE"];    	//상점 전화번호
    $LGD_CASHRECEIPTUSE     	= $HTTP_POST_VARS["LGD_CASHRECEIPTUSE"];		//현금영수증발급용도('1':소득공제, '2':지출증빙)
    $LGD_PRODUCTINFO        	= $HTTP_POST_VARS["LGD_PRODUCTINFO"];			//상품명
    $LGD_TID        			= $HTTP_POST_VARS["LGD_TID"];					//텔레콤 거래번호

	$configPath 				= $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]; 						 		//LG텔레콤에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.   
	// $configPath 			= "/data/baekop_data/conf"; 						 		// 안되면 이것으로.. 되는것이 있으면  tempet_src 말고 data_src 같은 임대서비스 공용변수 사용
    	
    require_once("./XPayClient.php");
    $xpay = &new XPayClient($configPath, $CST_PLATFORM);
    $xpay->Init_TX($LGD_MID);
    $xpay->Set("LGD_TXNAME", "CashReceipt");
    $xpay->Set("LGD_METHOD", $LGD_METHOD);
    $xpay->Set("LGD_PAYTYPE", $LGD_PAYTYPE);

    if ($LGD_METHOD == "AUTH"){					// 현금영수증 발급 요청
    	$xpay->Set("LGD_OID", $LGD_OID);
    	$xpay->Set("LGD_AMOUNT", $LGD_AMOUNT);
    	$xpay->Set("LGD_CASHCARDNUM", $LGD_CASHCARDNUM);
    	$xpay->Set("LGD_CUSTOM_MERTNAME", $LGD_CUSTOM_MERTNAME);
    	$xpay->Set("LGD_CUSTOM_BUSINESSNUM", $LGD_CUSTOM_BUSINESSNUM);
    	$xpay->Set("LGD_CUSTOM_MERTPHONE", $LGD_CUSTOM_MERTPHONE);
    	$xpay->Set("LGD_CASHRECEIPTUSE", $LGD_CASHRECEIPTUSE);
		$xpay->Set("LGD_ENCODING", "UTF-8");//케릭터 셋 설정

		if ($LGD_PAYTYPE == "SC0030"){				//기결제된 계좌이체건 현금영수증 발급요청시 필수 
			$xpay->Set("LGD_TID", $LGD_TID);
		}
		else if ($LGD_PAYTYPE == "SC0040"){			//기결제된 가상계좌건 현금영수증 발급요청시 필수 
			$xpay->Set("LGD_TID", $LGD_TID);
			$xpay->Set("LGD_SEQNO", "001");
		}
		else {										//무통장입금 단독건 발급요청
			$xpay->Set("LGD_PRODUCTINFO", $LGD_PRODUCTINFO);
    	}
    }else {											// 현금영수증 취소 요청 
    	$xpay->Set("LGD_TID", $LGD_TID);
 
    	if ($LGD_PAYTYPE == "SC0040"){				//가상계좌건 현금영수증 발급취소시 필수
			$xpay->Set("LGD_SEQNO", "001");
    	}
    }


    /*
     * 1. 현금영수증 발급/취소 요청 결과처리
     *
     * 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
     */
    if ($xpay->TX()) {
        //1)현금영수증 발급/취소결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)

		if($xpay->Response("LGD_RESPCODE",0)=="0000") {//성공
			if ($LGD_METHOD == "AUTH"){					// 현금영수증 발급 요청
				$AuthDate=str_replace("-","",$xpay->Response("LGD_RESPDATE",0));
				$AuthDate=str_replace(":","",$AuthDate);
				$AuthDate=str_replace(" ","",$AuthDate);
				$m_pgAuthDate=substr($AuthDate,0,8);
				$m_pgAuthTime=substr($AuthDate,8,6);
				switch($LGD_CASHRECEIPTUSE) {
					case "1" : $m_ruseopt="0";
					break;
					case "2" : $m_ruseopt="1";
					break;
				}
				$sql = "insert into receipt_result (oid,m_resultMsg, m_rcash_noappl,m_pgAuthDate,m_pgAuthTime,m_tid,m_rcr_price, m_rsup_price, m_rtax,m_rsrvc_price,m_ruseopt,regdate) values ('".$LGD_OID."','".$xpay->Response("LGD_RESPCODE",0)."','".$xpay->Response("LGD_CASHRECEIPTNUM",0)."','".$m_pgAuthDate."','".$m_pgAuthTime."','".$xpay->Response("LGD_TID",0)."','".$LGD_AMOUNT."','".$LGD_AMOUNT."','','','".$m_ruseopt."',NOW())";
				$db->query($sql);
				$sql = "update receipt set receipt_yn = 'Y' where order_no = '".$LGD_OID."' ";
				$db->query($sql);

				$method_text="신청";
			} else {					// 현금영수증 취소 요청
				$sql="DELETE FROM receipt_result WHERE m_tid='".$LGD_TID."' ";
				$db->query($sql);

				$sql="UPDATE receipt SET receipt_yn='N' WHERE order_no='".$LGD_OID."' ";
				$db->query($sql);

				$method_text="취소";
			}


			/*echo "현금영수증 발급/취소 요청처리가 완료되었습니다.  <br>";
			echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
			echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
			
			echo "결과코드 : " . $xpay->Response("LGD_RESPCODE",0) . "<br>";
			echo "결과메세지 : " . $xpay->Response("LGD_RESPMSG",0) . "<br>";
			echo "거래번호 : " . $xpay->Response("LGD_TID",0) . "<p>";
			
			$keys = $xpay->Response_Names();
			foreach($keys as $name) {
				echo $name . " = " . $xpay->Response($name, 0) . "<br>";
			}*/
		}
 
    }else {
        //2)API 요청 실패 화면처리
        /*echo "현금영수증 발급/취소 요청처리가 실패되었습니다.  <br>";
        echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
        echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";*/
    }
?>

<html>
<head>
<title>LGDACOM 현금영수증 <?=$method_text?></title>
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
								<img src='/admin/images/icon/sub_title_dot.gif' align=absmiddle> 현금영수증<?=$method_text?>완료
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
											<td bgcolor='#ffffff'>&nbsp;<?=$LGD_OID?></td>
										</tr>
										<tr>
											<td class=leftmenu width=90 align='left' ><img src='../../image/title_head.gif' > 결과코드</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$xpay->Response_Code()?></td>
										</tr>
										<tr>
											<td class=leftmenu width=90 align='left' ><img src='../../image/title_head.gif' > 결과내용</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$xpay->Response_Msg()?></td>
										</tr>
										<?
										if ($xpay->TX()) {
											if ($LGD_METHOD == "AUTH"){
										?>
										<tr>
											<td class=leftmenu align='left' ><img src='../../image/title_head.gif' > 거래번호</td>
											<td bgcolor='#ffffff'>&nbsp;<?=$xpay->Response("LGD_TID",0)?></td>
											
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
										<?
											}
										}
										?>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<span id="show_pay_btn">
										<input type="button" value="닫기"  class="box" onclick="self.close()" style="cursor:pointer;" />
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