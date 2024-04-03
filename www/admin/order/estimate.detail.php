<?
include("../class/layout.class");


$db1 = new Database;
$db2 = new Database;

$Contents = "

<!--span style='width:50px;'></span>".(($admininfo[admin_level] == 9) ? selectadmin($admincode) : "")."<br><br-->
<table width='100%'>
<tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("견적서상세보기", "주문관리 > 견적서상세보기 ")."</td>
</tr>

</table>  ";

$sql = "select e.*,m.id ,
case when est_status = 0 then '견적대기'  
when est_status = 2 then '진행중' 
when est_status = 4 then '1차상담' 
when est_status = 6 then '2차상담' 
when est_status = 8 then '견적완료' 
when est_status = 9 then '납품/설치완료'
end as est_status_str
from 
mallstory_estimates e left join ".TBL_MALLSTORY_MEMBER." m on e.ucode = m.code where est_ix = '".$est_ix."' ";
$db1->query($sql);
$db1->fetch();

if($db1->dt[est_type] == "c"){
	$est_type_str = "맞춤견적";
}else if($db1->dt[est_type] == "q"){
	$est_type_str = "빠른견적";
}else if($db1->dt[est_type] == "s"){
	$est_type_str = "시스템견적";
}else if($db1->dt[est_type] == "i"){
	$est_type_str = "내부견적";
}   

if($db1->dt[est_receive_method] == "1"){
	$est_receive_str = "이메일";
}else if($db1->dt[est_receive_method] == "2"){
	$est_receive_str = "FAX";
}else{
	$est_receive_str = "방문상담/기타";
}

if($db1->dt[est_selltype] == "1"){
	$est_selltype_1 = "selected";
}else if($db1->dt[est_selltype] == "2" || $db1->dt[est_selltype] == ""){
	$est_selltype_2 = "selected";
}

$est_zip = explode("-",$db1->dt[est_zip]);
$aweeknext  = mktime (0,0,0,date("m")  , date("d")+7, date("Y"));
$Contents = $Contents."<div id='print_area'>
<table border='0' cellspacing='1' cellpadding='15' width='100%'>
                <tr>
                  <td bgcolor='#F8F9FA'>
<img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'>
<b>견적정보</b>
<form name='estimate_edit_form' action='estimate.act.php' method='post'>
<table border='0' width='100%' cellspacing='1' cellpadding='0'>
	<tr>
		<td >
			<table border='0' width='100%' cellspacing='1' cellpadding='4' bgcolor=#c0c0c0>
				<tr>
					<td class=leftmenu align='left' style='padding-left:10px;' width='15%'><img src='../image/title_head.gif'> 제목</td>
					<td bgcolor='#ffffff' width='35%'>&nbsp;<input type='text' name='est_title' value='".$db1->dt[est_title]."' style='width:95%'></td>
					<td class=leftmenu align='left' style='padding-left:10px;' width='15%'><img src='../image/title_head.gif'> 견적번호</td>
					<td bgcolor='#ffffff' width='35%'>&nbsp;".$db1->dt[est_id]."</td>
				</tr>
				<tr>
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 상호/업체명</td>
					<td bgcolor='#ffffff'>&nbsp;<input type='text' name='est_company' value='".$db1->dt[est_company]."' style='width:95%'></td>
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 국가</td>
					<td bgcolor='#ffffff'>&nbsp;<input type='text' name='est_nation' value='".$db1->dt[est_nation]."' style='width:95%'></td>
				</tr>
				<tr>
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 아이디</td>
					<td bgcolor='#ffffff'>&nbsp;".$db1->dt[id]."</td>
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 담당자</td>
					<td bgcolor='#ffffff'>&nbsp;<input type='text' name='est_charger' value='".$db1->dt[est_charger]."' style='width:95%'></td>
				</tr>
				<tr>
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 휴대전화</td>
					<td bgcolor='#ffffff'>&nbsp;<input type='text' name='est_mobile' value='".$db1->dt[est_mobile]."' style='width:95%'></td>
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 이메일</td>
					<td bgcolor='#ffffff'>&nbsp;<input type='text' name='est_email' value='".$db1->dt[est_email]."' style='width:95%'></td>
				</tr>
				<tr>
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 납품희망일</td>
					<td bgcolor='#ffffff'>&nbsp;".substr($db1->dt[est_plan_date],0,4)."년 ".substr($db1->dt[est_plan_date],4,2)."월 ".substr($db1->dt[est_plan_date],6,2)."일 </td>
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 유선전화</td>
					<td bgcolor='#ffffff'>&nbsp;<input type='text' name='est_tel' value='".$db1->dt[est_tel]."' style='width:95%'></td>
				</tr>
				<tr>
					<!--td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 견적서수령방법</td>
					<td bgcolor='#ffffff'>&nbsp;<input type='radio' value='1' name='est_receive_method' ".($db1->dt[est_receive_method] == "1" ? "checked":"").">이메일 <input type='radio' value='2' name='est_receive_method' ".($db1->dt[est_receive_method] == "2" ? "checked":"").">FAX <input type='radio' value='3' name='est_receive_method' ".($db1->dt[est_receive_method] == "3" ? "checked":"").">방문수령/기타</td-->
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 견적타입</td>
					<td bgcolor='#ffffff'>&nbsp;".$est_type_str."</td>
				</tr>
				
				<tr bgcolor='#ffffff' >
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> 우편번호</td>
					<td colspan='3' >&nbsp;<input type='text' name='est_zip1' size='3' maxlength='3' class='input' readonly value='".$est_zip[0]."'> - <input type='text' name='est_zip2' size='3' maxlength='3' class='input' readonly value='".$est_zip[1]."'>&nbsp;<input type=button value='주소검색' class='button' onClick=\"zipcode('5')\"></td>
				</tr>
				<tr bgcolor='#ffffff' >
					<td class=leftmenu align='left' style='padding-left:10px;' rowspan=2><img src='../image/title_head.gif' align=absmiddle> 배달주소</td>
					<td colspan='3' >&nbsp;<input type='text' size='60' name='est_addr1' class='input' value='".$db1->dt[est_addr1]."' validation='true' title='배달주소'></td>
				</tr>
				<tr bgcolor='#ffffff' >
					
					<td colspan='3' >&nbsp;<input type='text' size='60' name='est_addr2' class='input' value='".$db1->dt[est_addr2]."' validation='true' title='배달주소'></td>
				</tr>
				<tr>
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 추가사항</td>
					<td colspan='3' bgcolor='#ffffff'>&nbsp;".$db1->dt[est_etc]."</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<br><br>
<img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'>
<b>견적제품정보</b>

<input type='hidden' name='act' value='update'>
<input type='hidden' name='est_ix' value='".$db1->dt[est_ix]."'>
<table width='100%' border='0' cellpadding='0' cellspacing='1'>
	<tr>
		<td bgcolor='silver'>
			<table border='0' width='100%' cellspacing='1' cellpadding='2'>
				<tr>
					<td bgcolor='#ffffff'>
						<table border='0' width='100%'>
							<tr>
								<td>
									<table width='100%' border='0' cellpadding='0' cellspacing='0'>
										<tr height='25' bgcolor='#efefef' align=center>
											<td width='5%' class='s_td'><b>번호</b></td>
											<td width='20%'  class='m_td' colspan='2'><b>제품명</b></td>
											<td width='10%' class='m_td'><b>상품코드</b></td>
											<td width='7%' class='m_td'><b>수량</b></td>
											<td width='8%' class='m_td'><b>도매가</b></td>
											<td width='8%'  class='m_td'><b>견적가</b></td>
											<!--td width='8%'  class='m_td'><b>부가세</b></td-->
											<td width='10%' class='e_td'><b>합계</b></td>
										</tr>";

	
		$sql = "SELECT ed.*,p.coprice from mallstory_estimates_detail ed, ".TBL_MALLSTORY_PRODUCT." p WHERE est_ix = '".$est_ix."' and ed.pid = p.id";
	
		
	$db2->query($sql);
	

	$num = 1;

	$sum = 0;

	for($j = 0; $j < $db2->total; $j++)
	{
		$mdb = new MySql;
		$mdb->query("SELECT add_etc5 FROM ".TBL_MALLSTORY_MEMBER."  where id = '".$db1->dt[id]."' ");	
		$mdb->fetch();
		
		$db2->fetch($j);
		
		$percent = $mdb->dt[add_etc5];
		$pname = $db2->dt[pname];
		$pcode = $db2->dt[pcode];
		$price = $db2->dt[coprice];
		$options = $db2->dt[options];
		$expectprice = $db2->dt[expectprice];
		//$sumptprice = $sumptprice + $db3->dt[totalprice];
		
		$perprice = $price * ($percent / 100 + 1);
		if(!$expectprice) $expectprice = $perprice;
		$ptotal = $expectprice * $db2->dt[pcount];
		$sum += $ptotal;

$Contents .= "
										<input type='hidden' name='estd_ix[]' value='".$db2->dt[estd_ix]."'>
										<tr height='30' align='center'>
											<td align=center>".$num."</td>
											<td ><a href='/shop/goods_view.php?id=".$db2->dt[pid]."' target='_blank'><img src=\"".$admin_config[mall_data_root]."/images/product/c_".$db2->dt[pid].".gif\" style='margin:5 5;border:1px solid silver'  width=50 align=absmiddle></a></td>
											<td><a href='/shop/goods_view.php?id=".$db2->dt[pid]."' target='_blank'>".$pname."<br>".$options."</a></td>
											<td align=center><a href='/shop/goods_view.php?id=".$db2->dt[pid]."' target='_blank'>".$pcode."</a></td>
											<td align=center><input type='text' class='estimate' onkeyup=\"changeEstimateinfo2('".$db2->dt[estd_ix]."');\"  name='pcount_".$db2->dt[estd_ix]."' value='".$db2->dt[pcount]."' size=4>개</td>
											<td align='center'>".number_format($price)."</td>
											<td align='center'><input type='text' name='expectprice_".$db2->dt[estd_ix]."' value='".$expectprice."' style='width:80px;' onkeyup=\"changeEstimateinfo2('".$db2->dt[estd_ix]."');\"></td>
											<!--td align='center'><input type='text'  name='tax_".$db2->dt[estd_ix]."'  style='border:0px;' value='".$db2->dt[expectprice]*0.1."' readonly size=10></td-->
											<td align='center'>".number_format($ptotal)."</td>
										</tr>
										<tr><td colspan=11 class=dot-x></td></tr>";

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
		</td>
	</tr>
</table><br>";

$Contents = $Contents."
<br><br>
<img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'>
<b>견적상태변경</b>
<table border='0' width='100%' cellspacing='1' cellpadding='0'>
	<tr>
		<td >
			<table border='0' width='100%' cellspacing='1' cellpadding='4' bgcolor=#c0c0c0>
				<tr>
					<td class=leftmenu align='left' style='padding-left:10px;' width='15%'><img src='../image/title_head.gif'> 견적금액</td>
					<td bgcolor='#ffffff' width='35%'>&nbsp;".number_format($sum)."</td>
					<td class=leftmenu align='left' style='padding-left:10px;' width='15%'><img src='../image/title_head.gif'> 세액처리</td>
					<td bgcolor='#ffffff' width='35%'>&nbsp;
						<select name='est_selltype'>
							<option value='1' ".$est_selltype_1.">VAT 포함</option>
							<option value='2' ".$est_selltype_2.">VAT 미포함</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 견적서 유효일자</td>
					<td bgcolor='#ffffff'>&nbsp;".date("Y년 m월 d일", $aweeknext)."</td>
					<td class=leftmenu align='left' style='padding-left:10px;'><img src='../image/title_head.gif'> 상태변경</td>
					<td bgcolor='#ffffff' colspan=3>&nbsp;
					<select name='est_status'>				
						<option value=0 ".CompareReturnValue(0,$db1->dt[est_status]).">견적대기</option>
						<option value=1 ".CompareReturnValue(1,$db1->dt[est_status]).">검토중</option>
						<option value=2 ".CompareReturnValue(2,$db1->dt[est_status]).">1차상담</option>
						<option value=3 ".CompareReturnValue(3,$db1->dt[est_status]).">2차상담</option>
						<option value=4 ".CompareReturnValue(4,$db1->dt[est_status]).">견적완료</option>
						<option value=5 ".CompareReturnValue(5,$db1->dt[est_status]).">입금확인</option>
						<option value=6 ".CompareReturnValue(6,$db1->dt[est_status]).">사입중</option>
						<option value=7 ".CompareReturnValue(7,$db1->dt[est_status]).">발송완료</option>
					</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>";
$Contents = $Contents."
<table border=0 cellpadding=0 cellspacing=0 width='100%'>
	<tr>
		".($db1->dt[est_status] >= "4" ? "<td align=left>
			<table border=0 cellpadding=0 cellspacing=4>
				<tr>
					<td><a href='./estimate.detail.excel.php?est_ix=".$db1->dt[est_ix]."&est_id=".$db1->dt[est_id]."'><img src='../image/btn_orderlist.gif'></a></td>
					<td><a href='#'><img src='../image/btn_ordershop.gif'></a> </td>
				</tr>
			</table>
		</td>":"")."
		<td align=right>
			<table border=0 cellpadding=0 cellspacing=4>
				<tr>
					<td><input type=image src='../image/btc_modify.gif' border=0 style='cursor:hand;'></td>
					<td align=right><a href='estimate.list.php'><img src='../image/btn_e_list.gif'></a> </td>
					<td align=right><a href=\"javascript:PrintWindow('./estimate.view.php?est_ix=".$db1->dt[est_ix]."',740,600,'estimate_view')\"><img src='../image/btn_e_send.gif'></a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form>
</td>
	</tr>
</table>
";

$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >세금계산서를 발급받기 위해서는 세금계산서 관련 정보를 입력하셔야 합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >반품신청을 원하시면 반품신청 버튼을 클릭하신후 반품 사유를 입력하시고 반품 확인 버튼을 누르시면 됩니다</td></tr>
</table>
";


$help_text = HelpBox("견적리스트", $help_text);	
$Contents .= $help_text;

$Contents = $Contents."  
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";


$Script = "
<script language='javascript'>
	
var initBody ;
function beforePrint() { 
	initBody = document.body.innerHTML; document.body.innerHTML = document.getElementById('print_area').innerHTML; 
} 
function afterPrint() {  
	document.body.innerHTML = initBody; 
} 
function printArea() { 
	window.print(); 
} 
window.onbeforeprint = beforePrint; window.onafterprint = afterPrint; 
</script>
<script type='text/javascript'>
function changeEstimateinfo2(id){
	var frm = document.estimate_edit_form;
	var pcount = eval('frm.pcount_'+id+'.value');
	if(pcount != '')
		pcount = parseInt(pcount);	
	
	var expectprice = eval('frm.expectprice_'+id+'.value');
	//alert(expectprice);
	if(expectprice != '')
		expectprice = parseInt(expectprice);	
		
	var totalprice = eval('frm.expectprice_'+id);
	//var tax = eval('frm.tax_'+id);
	
	
	//totalprice.value =expertprice * pcount;
	
	//tax.value =parseInt(expectprice * pcount *0.1);
}

function zipcode(id)
{
	var zip = window.open('../member/zipcode.php?type='+id,'','width=440,height=350,scrollbars=yes,status=no');
}
</script>
";

if($mmode == "pop"){	
	
	$P = new ManagePopLayOut();
	$P->strLeftMenu = order_menu();
	$P->addScript = $Script;
	$P->Navigation = "HOME > 주문관리 > 견적리스트";
	$P->NaviTitle = "견적리스트";
	$P->strContents = $Contents;

	echo $P->PrintLayOut();
}else{
	
	$P = new LayOut();
	$P->strLeftMenu = order_menu();
	$P->addScript = $Script;
	$P->Navigation = "HOME > 주문관리 > 견적리스트";
	$P->strContents = $Contents;
	
	
	echo $P->PrintLayOut();
}


?>