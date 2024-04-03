<?php 
/**
 * 계좌정보 페이지
 * @date 2013.09.23
 * @author bgh
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/admin/class/layout.class';
//require $_SERVER['DOCUMENT_ROOT'].'/admin/order/bankda/src/bankda.php';
require $_SERVER['DOCUMENT_ROOT'].'/admin/order/bankda/src/bankda_new.php';

$bankda = new bankda();

$postValue = $_GET;

/* 계좌정보 */
$tokenData = $bankda->getToken();

if($tokenData['code'] != 200){
    echo "<script> alert('".$tokenData['msg']."') </script>";
    exit;
}

$accountList = $bankda->getAccountList();

/* 공통 메뉴 */
require "tabMenu.php"; 

$Contents .= "
<table width='100%' cellpadding='0' cellspacing='0' border=0>";
/* 계좌 목록 */
$Contents .= "
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../../images/dot_org.gif' align=absmiddle> <b class=blk>계좌목록</b></td>
				</tr>
			</table>
		</td>
	</tr>			
	";
$Contents.="
</table>";	

//리스트											
$Contents .= "
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
	<tr height='25' >
		<td width='5%'	align='center' class='m_td' nowrap><b>번호</b></td>
		<td width='13%' align='center' class='m_td' nowrap><b>은행명</b></td>
		<td width='13%' align='center' class='m_td' nowrap><b>계좌번호</b></td>
		<td width='13%' align='center' class='m_td' nowrap><b>계좌상태</b></td>
		<td width='*'	align='center' class='m_td' nowrap><b>최종조회</b></td>
		<td width='17%' align='center' class='m_td' nowrap><b>관리</b></td>
	</tr>";
if(!empty($accountList)){
	foreach($accountList as $key => $al):
		$Contents .="
		<tr height=28 >
			<td class='list_box_td' align='center' nowrap>".($key + 1)."</td>
			<td class='list_box_td' align='center' nowrap>".$al['Bkname']."</td>
			<td class='list_box_td' align='center' nowrap>".$al['Bkacctno']."</td>
			<td class='list_box_td' align='center' nowrap>".$al['acttag']."(".$al['act_status'].")</td>
			<td class='list_box_td' align='center' nowrap>".$al['last_scraping_dtm']."</td>
			<td class='list_box_td' align='center' nowrap>
				<a href=\"javascript:PoPWindow('popup.account.php?mode=mod&acctNo=".$al['Bkacctno']."',800,300,'modAccount');\">
					<img src='../../images/korean/btc_modify.gif' align='absmiddle'></a>&nbsp; 
				<a href=\"javascript:deleteAccount('".$al['Bkacctno']."');\">
					<img src='../../images/korean/btc_del.gif' align='absmiddle'></a>
			</td>
		</tr>";
	endforeach;
}else{
		$Contents .="
		<tr height=128 >
			<td class='list_box_td' align='center' colspan=6 nowrap>등록된 계좌정보가 없습니다. 계좌추가 버튼을 통해 등록해주세요</td>
		</tr>";

}
$Contents.="
</table>
	<div style='float:right;padding:10px;'>
		<a href=\"javascript:PoPWindow('popup.account.php?mode=add',800,300,'addAccount');\">
			<img src='./btn/btn_addAccount.gif'>
		</a>
	</div>";
$help_text ="
<table width='100%' border='0' cellpadding='3' cellspacing='0' align='left'  style=''>
	<tr height='120' >
		<td width='5%' align='left' style='margin-top:10px;'>
			<b style='margin-left:10px;'>
				[필독] 계좌등록하기 전 주의 사항!!
			</b>
			<p style='padding-left:10px;'>
				> 법인계좌 조회는 간편조회 서비스.개인계좌의 조회는 기본적으로 인터넷뱅킹ID,PW를 통해 조회합니다.</br>
				&nbsp;&nbsp;단. 일부은행의 경우. 부득이하게 다른정보를 요구할 수 있습니다.</br>
				> 계좌번호 및 은행을 변경하시려면 계좌 삭제후 다시 등록해주시기 바랍니다.</br>
				> 계좌 신규/수정시 DB에 반영되는 시간은 은행별로 상이하나 평균 5분 정도 기다려 주시기 바랍니다.
			</p>
		</td>
	</tr>
</table>
";

$Contents .= HelpBox("무통장 입금확인 서비스", $help_text);

$Script = "
<script language='javascript' src='../../include/DateSelect.js'></script>
<script language='javascript' src='bankda.js'></script>
<script>
function deleteAccount(bkacctno){
	
	alert('계좌 삭제는 시스템 관리자에 문의 바랍니다.');
	
//	계좌 삭제 불가 처리 JK180809
//    var select= confirm(bkacctno + ' : 해당 계좌를 정말로 삭제하시겠습니까?');
//	if(select){
//		eventDetail=window.open('','act','');
//    	eventDetail.location='/admin/order/bankda/bankda.act.php?act=deleteAccount&bkacctno=' + bkacctno;
//	}
}
</script>
";
$P = new LayOut();
$P->addScript = $Script;
$P->OnloadFunction = "";
$P->Navigation = "주문관리 > 무통장 자동입금확인";
$P->title = "계좌정보";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();