<? 
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/poplayout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/lib/control.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/include/design.tmp.php");

$db = new Database;


$Contents01 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' >
	  <tr>
	    <td align='left' colspan=6 style='padding-bottom:10px;'> ".GetTitleNavigation("입점업체 회원가입", "상점관리 > 입점업체 회원가입")."</td>
	</tr>
	  <tr>
	    <td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='./image/title_head.gif' align=absmiddle><b> 사업자 정보</b></div>")."</td>
	  </tr>
	  <!--tr>
	    <td align='left' colspan=4 > ".LineBox("<div style='padding:0px;'><img src='../image/title_head.gif' align=absmiddle><b> 사업자 정보</b></div>","silver","#efefef")."</td>
	  </tr-->	  
	  <tr bgcolor=#ffffff>
	    <td width='20%'><img src='./image/ico_dot.gif' align=absmiddle> 사업자번호    </td>
	    <td colspan=3><input type=text name='business_number' value='".$db->dt[business_number]."' class='textbox'  style='width:50%'></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff>
	    <td width='20%' ><img src='./image/ico_dot.gif' align=absmiddle> <b>사업자명</b></td>
	    <td width='30%' ><input type=text name='company_name' value='".$db->dt[company_name]."' class='textbox'  style='width:100%' validation=true title='사업자명'></td>
	    <td width='20%' align=left style='padding:0 0 0 30'><img src='./image/ico_dot.gif' align=absmiddle> 업태    </td>
	    <td width='30%' ><input type=text name='business_kind' value='".$db->dt[business_kind]."' class='textbox'  style='width:100%'></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff>
	    <td><img src='./image/ico_dot.gif' align=absmiddle> <b>대표자명</b>    </td>
	    <td><input type=text name='ceo' value='".$db->dt[ceo]."' class='textbox'  style='width:100%' validation=true title='대표자명'></td>
	    <td width='20%' align=left style='padding:0 0 0 30'><img src='./image/ico_dot.gif' align=absmiddle> 종목    </td>
	    <td><input type=text name='business_item' value='".$db->dt[business_item]."' class='textbox'  style='width:100%'></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff>
	    <td><img src='./image/ico_dot.gif' align=absmiddle> 주소    </td>
	    <td colspan=3>
	    	<table border='0' cellpadding='0' cellspacing='0'>
				<tr>
					<td>
						<input type='text' class='input' name='zip1' size='3' maxlength='3' value='' readonly> -
						<input type='text' class='input' name='zip2' size='3' maxlength='3' value='' readonly>  
					</td>
					<td style='padding:5 0 0 5;'>
						<img src='./image/member_join_adress.gif' onclick='zipcode();' style='cursor:hand;'>
					</td>
				</tr>
				</table>
	    	<input type=text name='addr1' value='' size=50 class='textbox'  style='width:70%'><br>
	    	<input type=text name='addr2' value='' size=70 class='textbox'  style='width:100%'></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff>
	    <td width='20%'><img src='./image/ico_dot.gif' align=absmiddle> <b>입점업체 이름</b>    </td>
	    <td colspan=3><input type=text name='shop_name' value='' class='textbox'  style='width:100%' validation=true title='입점업체 이름'></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff>
	    <td width='20%'><img src='./image/ico_dot.gif' align=absmiddle> 입점업체 설명   </td>
	    <td colspan=3><textarea name='shop_desc'  style='width:100%;height:70px;' validation=false title='입점업체 설명'></textarea></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff>
	    <td><img src='./image/ico_dot.gif' align=absmiddle> 입점업체 이미지   </td>
	    <td colspan=3><input type=file name='shop_img' size=70 class='textbox'  style='width:300px'> 권장 사이트 88 * 67</td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  </table>";
	  
$ContentsDesc01 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;' class=small>
		<img src='./image/emo_3_15.gif' align=absmiddle>  세금 계산서 발급및 견적서 작성시 정보로 이용됩니다.
	</td>
</tr>
</table>
";


$Contents02 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='./image/title_head.gif' align=absmiddle><b> 거래은행 / 거래일</b></div>")."</td>
	  </tr>	  
	  <tr bgcolor=#ffffff>
	    <td width='20%' align=left><img src='./image/ico_dot.gif' align=absmiddle> 예금주    </td><td width='30%' ><input type=text name='bank_owner' value='".$db->dt[bank_owner]."' class='textbox'  style='width:100%'></td>
	    <td width='20%' align=left style='padding:0 0 0 30'><img src='./image/ico_dot.gif' align=absmiddle> 거래은행    </td><td width='30%' ><input type=text name='bank_name' value='".$db->dt[bank_name]."' class='textbox'  style='width:100%'></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff>
	    <td align=left><img src='./image/ico_dot.gif' align=absmiddle> 계좌번호    </td><td><input type=text name='bank_number' value='".$db->dt[bank_number]."' class='textbox'  style='width:100%'></td>
	    <td align=left style='padding:0 0 0 30'><img src='./image/ico_dot.gif' align=absmiddle> 거래계약일    </td><td><input type=text name='business_day' value='".$db->dt[business_day]."' class='textbox'  style='width:100%'></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  </table>";
	  
$ContentsDesc02 = "
<table width='660' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td align=left style='padding:10px;' class=small>
		<img src='./image/emo_3_15.gif' align=absmiddle> 해당거래처의 기본 계좌 정보를 입력합니다.
	</td>
</tr>
</table>
";

$Contents03 = "
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
	  <tr>
	    <td align='left' colspan=4> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='./image/title_head.gif' align=absmiddle><b> 영업정보</b></div>")."</td>
	  </tr>";
	  
if($admininfo[admin_level] == 9){
$Contents03 .= "
	  <tr bgcolor=#ffffff >
	    <td width='20%' ><img src='./image/ico_dot.gif' align=absmiddle> 거래처 형태    </td>
	    <td width='30%' colspan=3 >
	    	<input type=radio name='company_type' id='company_type_1' value='1'  ".CompareReturnValue("1",$db->dt[company_type],"checked")."><label for='company_type_1'>일반거래처</label>
	    	<input type=radio name='company_type' id='company_type_2' value='2' ".CompareReturnValue("2",$db->dt[company_type],"checked")."><label for='company_type_2'>입점업체</label>
	    	 &nbsp;&nbsp;&nbsp;&nbsp;<span class=small style='color:gray'>입점업체의 경우만 아이디와 패스워드를 입력하시면 됩니다. </span>
	    </td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>";
}
$Contents03 .= "
	  <tr bgcolor=#ffffff  >
	    <td width='20%' ><img src='./image/ico_dot.gif' align=absmiddle> <b>업체아이디</b>    </td>
	    <td width='30%'  ><input type=text name='admin_id' value='".$db->dt[admin_id]."' class='textbox'  style='width:100px' validation=true title='업체아이디' duplicate=true dup_check=false>
		<img src='./image/duplicate.gif' width='59' height='18' border='0' align='absmiddle' onClick='idCheck(document.edit_form)' style='cursor:hand;'>
		</td>
	    <td align=left style='padding:0 0 0 30'><img src='./image/ico_dot.gif' align=absmiddle> <b>패스워드</b></td>
	    <td align=left width='30%'  nowrap><input type=password name='pass1' value='' size=12 style='width:40%' class='textbox'  validation=true title='패스워드'> <input type=password name='pass2' value='' size=12 class='textbox'  style='width:40%' validation=true title='패스워드'></td>
	  </tr>
	  <tr hegiht=1 ><td colspan=4 background='./image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff >
	    <td><img src='./image/ico_dot.gif' align=absmiddle> <b>대표전화</b></td>
	    <td>
	    <input type=text name='phone1' value='".$phone[0]."' maxlength=3 size=3  class='textbox' validation=true title='대표전화'> - 
	    <input type=text name='phone2' value='".$phone[1]."' maxlength=4 size=5 class='textbox' validation=true title='대표전화'> - 
	    <input type=text name='phone3' value='".$phone[2]."' maxlength=4 size=5 class='textbox' validation=true title='대표전화'></td>
	    <td align=left style='padding:0 0 0 30' ><img src='./image/ico_dot.gif' align=absmiddle> 대표팩스</td>
	    <td><input type=text name='fax1' value='".$fax[0]."' maxlength=3 size=3 class='textbox' > - <input type=text name='fax2' value='".$fax[1]."' maxlength=4 size=5 class='textbox' > - <input type=text name='fax3' value='".$fax[2]."' maxlength=4 size=5 class='textbox' ></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff >
	    <td><img src='./image/ico_dot.gif' align=absmiddle> <b>담당자명</b></td><td><input type=text name='charger' value='".$db->dt[charger]."' class='textbox'  style='width:100%' validation=true title='담당자명'></td>
	    <td align=left style='padding:0 0 0 30'><img src='./image/ico_dot.gif' align=absmiddle> 이메일</td>
	    <td><input type=text name='charger_email' value='".$db->dt[charger_email]."' class='textbox'  style='width:100%'></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff >
	    <td><img src='./image/ico_dot.gif' align=absmiddle> 홈페이지</td><td><input type=text name='homepage' value='".$db->dt[homepage]."' class='textbox'  style='width:100%'></td>	    
	    <td align=left style='padding:0 0 0 30'><img src='./image/ico_dot.gif' align=absmiddle> 제품문의 메일</td>
	    <td><input type=text name='pqmail' value='".$db->dt[pqmail]."' class='textbox'  style='width:100%'></td>
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  <tr bgcolor=#ffffff >
	    <td><img src='./image/ico_dot.gif' align=absmiddle> AS 전화</td><td><input type=text name='asphone' value='".$db->dt[asphone]."' class='textbox'  style='width:100%'></td>
	    
	  </tr>
	  <tr hegiht=1><td colspan=4 background='./image/dot.gif'></td></tr>
	  </table>";
	  
$ContentsDesc03 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr>
	<td><img src='./image/emo_3_15.gif' align=absmiddle> </td>
	<td align=left style='padding:10px;' class=small>
		 입점 업체의 경우 로그인 아이디 패스워드가 부여가 됩니다. 또한 쇼핑몰의 A/S 정보및 배송 정보등에 정보가 쓰이게 됩니다.
	</td>
</tr>
</table>
";



$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=right><input type='image' src='./image/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
	  
	  
	  
$Contents = "<table width='680' border=0 align=center >";
$Contents = $Contents."<form name='edit_form' action='store/company.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' ><input name='act' type='hidden' value='submaster_insert'><input name='company_id' type='hidden' value='".$db->dt[company_id]."'>";
$Contents = $Contents."<tr><td>";
//$Contents = $Contents.ShadowBox($Contents01);
$Contents = $Contents.$Contents01."<br>";
//$Contents = $Contents.$ContentsDesc01;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>";
//$Contents = $Contents.ShadowBox($Contents02);
$Contents = $Contents.$Contents02;
//$Contents = $Contents.$ContentsDesc02;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc02."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>";
//$Contents = $Contents.ShadowBox($Contents03);
$Contents = $Contents.$Contents03;
//$Contents = $Contents.$ContentsDesc03;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc03."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents04."</td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr></form>";
$Contents = $Contents."<tr height=20><td></td></tr>";
$Contents = $Contents."</table >";

 
	

$Script = "<script language='JavaScript' src='./js/auto.validation.js'></Script>\n
<script language='javascript'>
function zipcode() {
	var zip = window.open('./member/zipcode.php','','width=440,height=300,scrollbars=yes,status=no');
}
</script>";

$P = new popLayOut();
$P->addScript = $Script;
//$P->strLeftMenu = store_menu();
$P->Navigation = "HOME > 상점관리 > 입점업체 회원가입";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>
