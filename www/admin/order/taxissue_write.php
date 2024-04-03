<? 
include("../class/layout.class");

$db = new Database;
$mdb = new Database;
$db2 = new Database;
$help_text = "	-  세금계산서 작성 페이지입니다. <br>
		";
if ($FromYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	
//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", $before10day);
	$eDate = date("Y/m/d");
	
	$startDate = date("Ymd", $before10day);
	$endDate = date("Ymd");
}else{
	$sDate = $FromYY."/".$FromMM."/".$FromDD;
	$eDate = $ToYY."/".$ToMM."/".$ToDD;
	$startDate = $FromYY.$FromMM.$FromDD;
	$endDate = $ToYY.$ToMM.$ToDD;
	$birDate = $birYY.$birMM.$birDD;
}
$mstring ="
<style>
table.provider td {color:red;background-color:#ffffff}
table.provider td.t {border-top:1px red solid}
table.provider td.b {border-bottom:1px red solid}
table.provider td.l {border-left:1px red solid}
table.provider td.r {border-right:1px red solid}
input.estimate {width:100%;}

table.receiver td {color:blue;background-color:#ffffff}
table.receiver td.t {border-top:1px blue solid}
table.receiver td.b {border-bottom:1px blue solid}
table.receiver td.l {border-left:1px blue solid}
table.receiver td.r {border-right:1px blue solid}
INPUT.provider {color:red; border:0px;text-align:center}
INPUT.receiver {color:blue; border:0px;text-align:center}

</style>
<script src='http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js' type='text/javascript'></script>

		
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left' colspan=6 > ".GetTitleNavigation("세금계산서 발행내역", "주문관리 > 세금계산서 발행내역")."</td>
		</tr>
		<form name='taxbill_form' method='post' onsubmit='return CheckFormValue(this);' action='taxbill.act.php' target='act'>
		<input type='hidden' id='oid'>
		<input type='hidden' name='act' value='input'>
		<tr>
			<td>
			".PrintTaxList()."
			</td>
		</tr>
		</form>";
$mstring .= "<tr><td style='padding-bottom:10px;' colspan=7>".HelpBox("세금계산서 관리", $help_text)."</td></tr>";
$mstring .="</table>";

$Contents = $mstring;


$P = new LayOut();
$P->addScript = "<script language='javascript' src='../include/DateSelect.js'></script>\n".$Script;
$P->Navigation = "HOME > 주문관리 > 세금계산서 발행내역";
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function PrintTaxList(){
	global $db, $mdb, $db2,$admininfo,$page,$nset,$tax_yn,$FromYY,$FromMM,$FromDD,$ToYY,$ToMM,$ToDD,$orderby,$ordertype,$search_type,$search_text,$client;
	
			$sql = "select * from ".TBL_COMMON_COMPANY_DETAIL." WHERE company_id = '".$admininfo[company_id]."' ";
			//exit($sql);
			$db->query($sql);
			$db->fetch();

$mString = "<table width='640' border='0' cellspacing='0' cellpadding='0'>
<tr>
	<td>
		<table class='provider' width='650' border='0' cellpadding='3' cellspacing='0' bgcolor='red'>
			<col width=25>
			<col width=65>
			<col width=100>
			<col width=25>
			<col width=100>
			<col width=25>
			<col width=65>
			<col width=100>
			<col width=25>
			<col width=100>
			<tr align='center' bgcolor='#FFFFFF'> 
				<td height='60' colspan='6' class='t b l'><strong><font style='font-size:24px'>세금계산서</font> 작성</strong></td>
				<td colspan='4' class='t b' style='padding:0px;padding-left:40px;'>
					<table width='100%' height=100% border='0' cellspacing='0' cellpadding='0'>
						<tr> 							
							<td width=33% align='center' bgcolor='#FFFFFF' class='b r l'>책&nbsp;&nbsp;번&nbsp;&nbsp;호</td>
							<td width=33% align='center' bgcolor='#FFFFFF' class='b r'><input type=text name='Kwon' size='2'>권</td>
							<td width=33% align='center' bgcolor='#FFFFFF' class='b r'><input type=text name='Ho' size='2'>호</td>
						</tr>
						<tr> 
							<td align='center' bgcolor='#FFFFFF' class='l r'>일 련 번 호</td>
							<td align='center' colspan=2 bgcolor='#FFFFFF' class='r'><input type=text name='SerialNum'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr> 
				<td width=25 height='120' align='center' class='b l r' rowspan=4>공<br>급<br>자</td>
				<td align='center' class='b r'>등록번호</td>
				<td colspan=3 align='center' class='b r'>".$db->dt[com_number]."</td>
				<td width='25' rowspan='4' class='b r' align='center'>
					공<br>급<br>자<br>받<br>는<br>자
				</td>
				<td align='center' class='b r'>등록번호</td>
				<td colspan=3 align='center' class='b r'><input type=text name='com_number' size=40 validation='true' title='등록번호'></td>	
			</tr>						
			<tr> 
				<td align='center' bgcolor='#FFFFFF' class='b r'>상&nbsp;&nbsp;&nbsp;&nbsp;호<br>(법인명)</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'>".$db->dt[com_name]."</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'>성<br>명</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'>".$db->dt[com_ceo]."</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'>상&nbsp;&nbsp;&nbsp;&nbsp;호<br>(법인명)</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'><input type=text name='com_name' size=10 validation='true' title='상호'></td>
				<td align='center' bgcolor='#FFFFFF' class='b r'>성<br>명</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'><input type=text name='com_ceo' size=10 validation='true' title='성명'></td>	
			</tr>
			<tr>
				<td align='center' class='b r'>사업장주소</td>
				<td colspan=3 align='center' class='b r'>".$db->dt[com_addr1]." ".$db->dt[com_addr2]." &nbsp;</td>
				<td align='center' class='b r'>사업장주소</td>
				<td colspan=3 align='center' class='b r'><input type=text name='com_addr' size=38></td>	
			</tr>
			<tr>
				<td align='center' bgcolor='#FFFFFF' class='b r'>업&nbsp;&nbsp;&nbsp;&nbsp;태</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'>".$db->dt[com_business_status]."&nbsp;</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'>종<br>목</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'>".$db->dt[com_business_category]."&nbsp;</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'>업&nbsp;&nbsp;&nbsp;&nbsp;태</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'><input type=text name='com_business_status' size=10 validation='true' title='업태'></td>
				<td align='center' bgcolor='#FFFFFF' class='b r'>종<br>목</td>
				<td align='center' bgcolor='#FFFFFF' class='b r'><input type=text name='com_business_category' size=10 validation='true' title='종목'></td>
			</tr>
			<tr bgcolor='#FFFFFF'> 
				<td colspan='2' align='center' class='b r l'>작&nbsp;&nbsp;&nbsp;성</td>
				<td colspan='4'  align='center' class='b r'>공&nbsp;&nbsp;급&nbsp;&nbsp;가&nbsp;&nbsp;액</td>
				<td colspan='2' align='center' class='b r'>세 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;액</td>
				<td colspan='2' align='center' class='b r'>비&nbsp;&nbsp;&nbsp;&nbsp;고</td>
			</tr>
			<tr bgcolor='#FFFFFF' height=60> 
				<td colspan='2' align='center' class='b r l' style='padding:0px;'>
					<table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0'>
						<tr height='50%'>
							<td align='center' bgcolor='#FFFFFF' class='b r'>년</td>
							<td align='center' bgcolor='#FFFFFF' class='b r'>월</td>
							<td align='center' bgcolor='#FFFFFF' class='b '>일</td>
						</tr>
						<tr height='50%'>
							<td align='center' bgcolor='#FFFFFF' class='r'><input type=text name='year' value='".date("Y")."' style='width:40px;' validation='true' title='년'></td>
							<td align='center' bgcolor='#FFFFFF' class='r'><input type=text name='month' value='".date("m")."' style='width:25px;' validation='true' id='month' title='월'></td>
							<td align='center' bgcolor='#FFFFFF' class=''><input type=text name='day' value='".date("d")."' style='width:25px;' validation='true' id='day' title='일'></td>
						</tr>
					</table>
				</td>
				<td colspan='4'  align='center' class='b r' style='padding:0px;'>
					<input type=text name='tax_price' id='tax_price' readonly onfocus='this.blur();'>
				</td>
				<td colspan='2' align='center' class='b r' style='padding:0px;'>
					<input type=text name='tax_prce_tax' id='tax_prce_tax' readonly onfocus='this.blur();'>
				</td>
				<td colspan='2' align='center' bgcolor='#FFFFFF' class='b r'><input type=text name='tax_note'></td>
			</tr>
		</table>
	</td>
</tr>
<SCRIPT type=text/javascript>
 jQuery.noConflict();
</SCRIPT>
<script type='text/javascript'>
	// 폼 체크
	function calculateSum(a, b){
		var sum = 0;
		var aclass = a;
		var bclass = b;
		jQuery('.'+aclass).each(function(){
			if(!isNaN(this.value) && this.value.length!=0){
				sum += parseFloat(this.value);
			}
		});
		jQuery('#'+bclass).val(sum);
	}
	
	function calculation(num){
		jQuery('#ptprice_'+num).val(jQuery('#pcnt_'+num).val()*jQuery('#td_cost_'+num).val());
		jQuery('#td_tax_'+num).val(jQuery('#ptprice_'+num).val()*0.1);
		calculateSum('ptprice', 'tax_price');
		calculateSum('td_tax', 'tax_prce_tax');
		jQuery('#totalprice').val(parseFloat(jQuery('#tax_price').val())+parseFloat(jQuery('#tax_prce_tax').val()));
	}
	jQuery(document).ready(function(){

		jQuery('.billstr_1').click(function(){
			jQuery('#conmonth_1').val(jQuery('#month').val())
			jQuery('#cinday_1').val(jQuery('#day').val())
		});
		jQuery('.billstr_2').click(function(){
			jQuery('#conmonth_2').val(jQuery('#month').val())
			jQuery('#cinday_2').val(jQuery('#day').val())
		});
		jQuery('.billstr_3').click(function(){
			jQuery('#conmonth_3').val(jQuery('#month').val())
			jQuery('#cinday_3').val(jQuery('#day').val())
		});
		
	});
</script>
<tr>
	<td>
		<table class='provider'  width='650' border='0' cellpadding='3' cellspacing='0' >
			<tr bgcolor='#FFFFFF'> 
				<td width=2% align='center' class='b l r' >월</td>
				<td width=2% align='center' class='b r' >일</td>
				<td width=40% colspan='2' align='center' class='b  r'>품목 및 규격</td>								
				<td width=10% align='center' class='b r'>수량</td>
				<td width=10% align='center' class='b r'>단가</td>
				<td width=10% align='center' class='b r'>공급가액</td>
				<td width=10% align='center' class='b r'>세&nbsp;&nbsp;&nbsp;&nbsp;액</td>
				<td width=15% align='center' class='b r'>비고</td>
			</tr>
			<tr bgcolor='#FFFFFF'> 
				<td align='center' class='b l r'><input type=text name='conmonth[]' id='conmonth_1' size=5 validation='true' title='월' class='billstr_1'></td>
				<td align='center' class='b r'><input type=text name='cinday[]' id='cinday_1' size=5 validation='true' title='일' class='billstr_1'></td>
				<td colspan='2' align='left' class='b r'><input type=text name='pname[]' id='pname_1' size=25 validation='true' title='품목 및 규격'></td>
				<td align='center' class='b r'><input type=text name='pcnt[]' id='pcnt_1' class='pcnt' size=5 validation='true' title='수량' onblur=\"calculation('1')\"></td>
				<td align='center' class='b r'><input type=text name='td_cost[]' id='td_cost_1' class='td_cost' size=5 validation='true' title='단가' onblur=\"calculation('1')\"></td>
				<td align='center' class='b r'><input type=text name='ptprice[]' id='ptprice_1' class='ptprice' size=5 validation='true' title='공급가액' readonly onfocus='this.blur();'></td>
				<td align='center' class='b r'><input type=text name='td_tax[]' id='td_tax_1' class='td_tax' size=5 validation='true' title='세액' readonly onfocus='this.blur();'></td>
				<td align='center' class='b r'><input type=text name='td_note[]' id='td_note_1' size=5></td>
			</tr>
			<tr bgcolor='#FFFFFF'> 
				<td align='center' class='b l r'><input type=text name='conmonth[]' id='conmonth_2' size=5 class='billstr_2'></td>
				<td align='center' class='b r'><input type=text name='cinday[]' id='cinday_2' size=5 class='billstr_2'></td>
				<td colspan='2' align='left' class='b r'><input type=text name='pname[]' id='pname_2' size=25></td>
				<td align='center' class='b r'><input type=text name='pcnt[]' id='pcnt_2' class='pcnt' size=5 onblur=\"calculation('2')\"></td>
				<td align='center' class='b r'><input type=text name='td_cost[]' id='td_cost_2' class='td_cost' size=5 onblur=\"calculation('2')\"></td>
				<td align='center' class='b r'><input type=text name='ptprice[]' id='ptprice_2' class='ptprice' size=5 readonly onfocus='this.blur();'></td>
				<td align='center' class='b r'><input type=text name='td_tax[]' id='td_tax_2' class='td_tax' size=5 readonly onfocus='this.blur();'></td>
				<td align='center' class='b r'><input type=text name='td_note[]' id='td_note_2' size=5></td>
			</tr>
			<tr bgcolor='#FFFFFF'> 
				<td align='center' class='b l r'><input type=text name='conmonth[]' id='conmonth_3' size=5 class='billstr_3'></td>
				<td align='center' class='b r'><input type=text name='cinday[]' id='cinday_3' size=5 class='billstr_3'></td>
				<td colspan='2' align='left' class='b r'><input type=text name='pname[]' id='pname_3' size=25></td>
				<td align='center' class='b r'><input type=text name='pcnt[]' id='pcnt_3' class='pcnt' size=5 onblur=\"calculation('3')\"></td>
				<td align='center' class='b r'><input type=text name='td_cost[]' id='td_cost_3' class='td_cost' size=5 onblur=\"calculation('3')\"></td>
				<td align='center' class='b r'><input type=text name='ptprice[]' id='ptprice_3' class='ptprice' size=5 readonly onfocus='this.blur();'></td>
				<td align='center' class='b r'><input type=text name='td_tax[]' id='td_tax_3' class='td_tax' size=5 readonly onfocus='this.blur();'></td>
				<td align='center' class='b r'><input type=text name='td_note[]' id='td_note_3' size=5></td>
			</tr>
			<tr bgcolor='#FFFFFF' height=60 > 
				<td colspan='9' align='center' class='b l r' style='padding:0px;'>
					<table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0'>
						<tr height='50%'>
							<td align='center' bgcolor='#FFFFFF' class='b r'>합계금액</td>
							<td align='center' bgcolor='#FFFFFF' class='b r'>현금</td>
							<td align='center' bgcolor='#FFFFFF' class='b r'>수표</td>
							<td align='center' bgcolor='#FFFFFF' class='b r'>어음</td>
							<td align='center' bgcolor='#FFFFFF' class='b r'>외상미수금</td>
							<td align='center' bgcolor='#FFFFFF' class='' width=130 rowspan=2>이 금액을 
							<select name='PurposeType'>
								<option value='1'>입금</option>
								<option value='0'>청구</option>
							</select>
							함</td>
						</tr>
						<tr height='50%'>
							<td align='center' bgcolor='#FFFFFF' class='r'><input type=text name='totalprice' id='totalprice' size=5 validation='true' onfocus='this.blur();' title='합계금액'></td>
							<td align='center' bgcolor='#FFFFFF' class='r'><input type=text name='Cash' size=5></td>
							<td align='center' bgcolor='#FFFFFF' class='r'><input type=text name='ChkBill' size=5></td>
							<td align='center' bgcolor='#FFFFFF' class='r'><input type=text name='Note' size=5></td>
							<td align='center' bgcolor='#FFFFFF' class='r'><input type=text name='Credit' size=5></td>
						</tr>
					</table>
				</td>
			</tr>			
			<tr bgcolor='#FFFFFF' height=60 > 
				<td colspan='9' align='center' class='b l r' style='padding:0px;'>
					<table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0'>
						<tr height='50%'>
							<td align='center' bgcolor='#FFFFFF' class='b r'>담당자</td>
							<td align='center' bgcolor='#FFFFFF' class='b r'><input type=text name='tax_charge_name' size=35 validation='true' title='담당자'></td>
							<td align='center' bgcolor='#FFFFFF' class='b r'>이메일</td>
							<td align='center' bgcolor='#FFFFFF' class='b'><input type=text name='tax_charge_email' size=35 validation='true' email='true' title='이메일'></td>
						</tr>
						<tr height='50%'>
							<td align='center' bgcolor='#FFFFFF' class='r'>전화</td>
							<td align='center' bgcolor='#FFFFFF' class='r'><input type=text name='TEL' size=35></td>
							<td align='center' bgcolor='#FFFFFF' class='r'>핸드폰</td>
							<td align='center' bgcolor='#FFFFFF' class=''><input type=text name='HP' size=35></td>
						</tr>
					</table>
				</td>
			</tr>			
			<tr bgcolor='#FFFFFF' height=60 > 
				<td colspan='9' align='center' style='padding:0px;'>
				<input type=image src='../image/b_save.gif' border=0 align=absmiddle>
				</td>
			</tr>			
		</table>	
	</td>
</tr>
</table>	";
	
	return $mString;
}

?>
