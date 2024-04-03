<?php
include $_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class";


$Script = "
<script language='JavaScript' >
function CheckSMS(frm){

	if(frm.sms_contents.value.length < 1){
		alert('SMS 내용을 입력해주세요');
		return false;
	}

	if(frm.mobiles.value.length < 1){
		alert('SMS 보낼 대상이 한명이상이어야 합니다.');
		return false;
	}

	return true;
}

</Script>";


$Contents = "
<form name='send_mail' method='post' action='sms.act.php' onsubmit='return CheckSMS(this);' target='act'>
<input type=hidden name='act' value='send_sms_manual' >
    <TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
        <TR>
            <td align=center colspan=2>
                <table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>			
                    <tr >
                        <td align='left' colspan=2> ".GetTitleNavigation("SMS 보내기", "회원관리 > SMS 보내기", false)."</td>
                    </tr>			
                </table>
            </td>
        </tr>
        <tr height=30>
            <td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> - 메세지 내용을 입력해주세요.</td>
            <td class='p11 ls1' style='padding:0 0 0 5px;' nowrap> - 메세지를 보낼 <b>휴대폰 번호</b>입니다. <br> - 구분값은 ',' 혹은 'Enter'로 사용 가능합니다.</td>
        </tr>
        <tr>    
            <td style='vertical-align:top;padding: 0 5px 0 5px' width=50%>
                <div style='width:225px;height:146px;border:1px solid silver;'>
                    <textarea name='sms_contents' class=textbox cols=20 rows=20 style='border:0px;width:200px;height:116px;'>".$msg."</textarea>
                </div>
            </td>
            <td style='padding: 0 5px 0 5px' width=50% valign=top>
                <div style='width:225px;height:146px;border:1px solid silver;'>
                    <textarea name='sms_phone_area' class=textbox cols=20 rows=20 style='border:0px;width:200px;height:116px;'></textarea>
                </div>
            </td>
        </tr>
        <tr>
            <td align=center style='padding:0 10px 0 10px' colspan=2>
            </td>
        </tr>
        <tr>
            <td align=center style='padding:10px 0 0 0' colspan=2>
                <input type=image src='./images/".$admininfo["language"]."/btn_send_sms_01.png' border=0 align=absmiddle> <a href='javascript:self.close();'><img src='./images/".$admininfo["language"]."/btn_close.gif' border=0 align=absmiddle></a>
            </td>
        </tr>
    </TABLE>
</form>
";




$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "회원관리 > SMS 보내기";
$P->NaviTitle = "SMS 보내기";
$P->strContents = $Contents;
echo $P->PrintLayOut();
