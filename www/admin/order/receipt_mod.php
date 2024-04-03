<!--
    /* ============================================================================== */
    /* =   PAGE : 변경 요청 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://testpay.kcp.co.kr/pgsample/FAQ/search_error.jsp       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2007   KCP Inc.   All Rights Reserved.                    = */
    /* ============================================================================== */
-->

<?
/*
lgdacom 추가 kbk 13/06/05
*/
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");

$db = new Database;
$db3 = new Database;

$db->query("select *,date_format(regdate,'%Y%m%d%H%i%s') as receipt_date from receipt_result where oid ='$oid'");
$db->fetch();

$sql="SELECT inipay_mid,lgdacom_id,lgdacom_key,lgdacom_type,kcp_id FROM ".TBL_SHOP_SHOPINFO." WHERE mall_domain = '".str_replace("www.","",$HTTP_HOST)."'";
$db3->query($sql);
$db3->fetch();
$inipay_mid=$db3->dt["inipay_mid"];
$lgdacom_id=$db3->dt["lgdacom_id"];
$lgdacom_key=$db3->dt["lgdacom_key"];
$lgdacom_type=$db3->dt["lgdacom_type"];
$kcp_id=$db3->dt["kcp_id"];

$sql="SELECT method FROM ".TBL_SHOP_ORDER." WHERE oid='".$oid."' ";
$db3->query($sql);
$db3->fetch();
$method=$db3->dt["method"];

if($_SESSION["admininfo"]["sattle_module"]=="kcp") {//결제모듈에 따라서 form 의 action 경로를 지정해줌 kbk 13/06/05
	$form_action="./kcp/sample/cash/pp_cli_hub.php";
} else if($_SESSION["admininfo"]["sattle_module"]=="lgdacom") {
	$form_action="./lgdacom/receiptResult.php";
	switch($method) {
		case "0" : $method_type="SC0100";//무통장
		break;
		case "4" : $method_type="SC0040";//가상계좌
		break;
		case "5" : $method_type="SC0030";//계좌이체
		break;
	}
}
?>
<html>
<head>
<title>현금영수증 취소</title>
<link href="css/sample.css" rel="stylesheet" type="text/css">
<script language="javascript">

    // 현금영수증 MAIN FUNC
    function  jsf__mod_cash( form )
    {
        jsf__show_progress(true);

        if ( jsf__chk_cash( form ) == false )
        {
            jsf__show_progress(false);
            return;
        }

        form.submit();
    }

    // 진행 바
    function  jsf__show_progress( show )
    {
        if ( show == true )
        {
            window.show_pay_btn.style.display  = "none";
            window.show_progress.style.display = "inline";
        }
        else
        {
            window.show_pay_btn.style.display  = "inline";
            window.show_progress.style.display = "none";
        }
    }


    function  jsf__chk_cash( form )
    {
    	len_mod_value = form.mod_value.value.length;
    	len_trad_time = form.trad_time.value.length;



        if ( len_trad_time != 14 )
        {
            alert("원 거래 시각을 정확히 입력해 주시기 바랍니다.");
            form.trad_time.select();
            form.trad_time.focus();
            return false;
        }

        return true;
    }

    function  jsf__chk_mod_gubn( form )
    {
        var span_mod_value_0 = document.getElementById( "span_mod_value_0" );
        var span_mod_value_1 = document.getElementById( "span_mod_value_1" );
        var span_mod_value_2 = document.getElementById( "span_mod_value_2" );
        var span_mod_value_3 = document.getElementById( "span_mod_value_3" );

        if ( form.mod_gubn[0].checked )
        {
            span_mod_value_0.style.display = "block";
            span_mod_value_1.style.display = "none";
            span_mod_value_2.style.display = "none";
            span_mod_value_3.style.display = "none";
        }
        else if (form.mod_gubn[1].checked )
        {
            span_mod_value_0.style.display = "none";
            span_mod_value_1.style.display = "block";
            span_mod_value_2.style.display = "none";
            span_mod_value_3.style.display = "none";
        }
        else if ( form.mod_gubn[2].checked )
        {
            span_mod_value_0.style.display = "none";
            span_mod_value_1.style.display = "none";
            span_mod_value_2.style.display = "block";
            span_mod_value_3.style.display = "none";
        }
        else if (form.mod_gubn[3].checked )
        {
            span_mod_value_0.style.display = "none";
            span_mod_value_1.style.display = "none";
            span_mod_value_2.style.display = "none";
            span_mod_value_3.style.display = "block";
        }
    }

    function  jsf__chk_mod_type( form )
    {
    	var div_division_cancel = document.getElementById( "div_division_cancel" );

        if ( form.mod_type[0].checked )
        {
            div_division_cancel.style.display = "none";
        }
        else if (form.mod_type[1].checked )
        {
            div_division_cancel.style.display = "block";
        }
        else if ( form.mod_type[2].checked )
        {
            div_division_cancel.style.display = "none";
        }
    }

function cancel_receipt(fm) {
	if(confirm("현금영수증을 취소하시겠습니까?")) {
		fm.submit();
	}
}
</script>
</head>
<body>
<form name="cash_form" action="<?=$form_action?>" method="post">
<table border="0" cellpadding="0" cellspacing="1" width="500" align="center">

    <tr>
        <td colspan="2">
            <table width="90%" align="center">
                <tr>
                    <td bgcolor="CFCFCF" height="2"></td>
                </tr>
                <tr>
                    <td align="center"><B>변경 정보</B></td>
                </tr>
                <tr>
                    <td bgcolor="CFCFCF" height="2"></td>
                </tr>
            </table>
            <table width="90%" align="center" border="0">
				<? if($_SESSION["admininfo"]["sattle_module"]=="kcp") { ?>
                <tr>
                    <td>변경 타입</td>
                    <td>
                    	<input type="radio" name="mod_type" onClick="jsf__chk_mod_type( this.form )" value="STSC" checked>취소요청&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    	<input type="radio" name="mod_type" onClick="jsf__chk_mod_type( this.form )" value="STPC">부분취소요청&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    	<input type="radio" name="mod_type" onClick="jsf__chk_mod_type( this.form )" value="STSQ">조회요청&nbsp;
                   	</td>
                </tr>
				<input type='hidden' name='mod_gubn' value='MG02'>
                <tr><td colspan="2"><IMG SRC="./kcp/sample/cash/img/dot_line.gif" width="100%"></td></tr>
                <tr>
                    <td>
                        <span id="span_mod_value_0" style="display:none;">현금영수증 거래번호</span>
                        <span id="span_mod_value_1" style="display:block;">현금영수증 승인번호</span>
                        <span id="span_mod_value_2" style="display:none;">신분확인 ID</span>
                        <span id="span_mod_value_3" style="display:none;">PG 결제 거래번호</span>
                    </td>
                    <td><input type="text" name="mod_value" size="20" maxlength="20" value="<?=$db->dt[m_rcash_noappl]?>"></td>
                </tr>
                <tr><td colspan="2"><IMG SRC="./kcp/sample/cash/img/dot_line.gif" width="100%"></td></tr>
                <tr>
                    <td>원거래 시각</td>
                    <td><input type="text" name="trad_time" size="20" maxlength="14" value="<?=$db->dt[m_pgAuthDate]?>"></td>
                </tr>
                <tr>
	                <td colspan="2" >
		                <div id = div_division_cancel style="display:none;">
			                <table width="100%" border="0"  cellpadding="1" cellspacing="0">
				                <tr><td colspan="2"><IMG SRC="./kcp/sample/cash/img/dot_line.gif" width="100%"></td></tr>
							    <tr>
							        <td>취소금액</td>
							        <td><input type="text" name="mod_mny" size="20" maxlength="12" value=""></td>
							    </tr>
							    <tr><td colspan="2"><IMG SRC="./kcp/sample/cash/img/dot_line.gif" width="100%"></td></tr>
							    <tr>
							        <td>원금액</td>
							        <td><input type="text" name="rem_mny" size="20" maxlength="12" value=""></td>
							    </tr>
						    </table>
					    </div>
				    </td>
			    </tr>

                <tr><td colspan="2"><IMG SRC="./kcp/sample/cash/img/dot_line.gif" width="100%"></td></tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <span id="show_pay_btn">
                            <input type="button" value="변경 요청" onclick="jsf__mod_cash( this.form )" class="box">
                        </span>
                        <span id="show_progress" style="display:none">
                            <b>변경 진행중입니다. 잠시만 기다려주십시오</b>
                        </span>
                    </td>
                </tr>
				<? } else if($_SESSION["admininfo"]["sattle_module"]=="lgdacom") { ?>
				<input type="hidden" name="CST_MID" value="<?=$lgdacom_id?>"/>
				<input type="hidden" name="CST_PLATFORM" value="<?=$lgdacom_type?>"/>
				<input type="hidden" name="LGD_TID" value="<?=$db->dt[m_tid]?>"/>
				<input type="hidden" name="LGD_PAYTYPE" value="<?=$method_type?>"/>
				<input type="hidden" name="LGD_OID" value="<?=$db->dt[oid]?>"/>
				<input type='hidden' name='LGD_METHOD' value='CANCEL' />
				 <tr>
                    <td>변경 타입</td>
                    <td>
                    	취소요청
                   	</td>
                </tr>
				<tr><td colspan="2"><IMG SRC="./kcp/sample/cash/img/dot_line.gif" width="100%"></td></tr>
                <tr>
					 <td>상점아이디</td>
                    <td>
                    	<?=$lgdacom_id?>
                   	</td>
                </tr>
				<tr><td colspan="2"><IMG SRC="./kcp/sample/cash/img/dot_line.gif" width="100%"></td></tr>
                <tr>
					 <td>LG 텔레콤 승인번호</td>
                    <td>
                    	<?=$db->dt[m_rcash_noappl]?>
                   	</td>
                </tr>
				<tr><td colspan="2"><IMG SRC="./kcp/sample/cash/img/dot_line.gif" width="100%"></td></tr>
                <tr>
					 <td>LG 텔레콤 승인날짜</td>
                    <td>
                    	<?=$db->dt[receipt_date]?>
                   	</td>
                </tr>
				<tr><td colspan="2"><IMG SRC="./kcp/sample/cash/img/dot_line.gif" width="100%"></td></tr>
                <tr>
					 <td>LG 텔레콤 거래번호</td>
                    <td>
                    	<?=$db->dt[m_tid]?>
                   	</td>
                </tr>
				 <tr>
                    <td colspan="2" align="center">
                       <input type="button" value="취소 요청" onclick="cancel_receipt(this.form)" class="box" style="cursor:pointer;">
                    </td>
                </tr>
				<? } ?>
                <!--tr><td colspan="2"><IMG SRC="./kcp/sample/cash/img/dot_line.gif" width="100%"></td></tr>
                <tr>
                    <td>변경 요청 거래번호 구분</td>
                    <td>
                    	<input type="radio" name="mod_gubn" value="MG01" onClick="jsf__chk_mod_gubn( this.form )" checked>영수증 거래번호&nbsp;
                    	<input type="radio" name="mod_gubn" value="MG02" onClick="jsf__chk_mod_gubn( this.form )">영수증 승인번호&nbsp;<br>
                    	<input type="radio" name="mod_gubn" value="MG03" onClick="jsf__chk_mod_gubn( this.form )">신분확인 ID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    	<input type="radio" name="mod_gubn" value="MG04" onClick="jsf__chk_mod_gubn( this.form )">결제 거래번호&nbsp;
                   	</td>
                </tr-->
				
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="CFCFCF" height="3" colspan="2"></td>
    </tr>

</table>
<!-- 요청종류 승인(pay)/변경(mod) 요청시 사용 -->
<input type="hidden" name="req_tx" value="mod">
</form>
</body>
</html>