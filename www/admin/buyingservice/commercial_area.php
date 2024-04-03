<?
include("../class/layout.class");
include("buying.lib.php");

$db = new Database;

if($ca_ix){
	$sql = "select 
			ca.*,
			cu.id,AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name,
			ccd.com_ceo,ccd.com_number,ccd.com_phone,ccd.com_fax,ccd.com_zip,ccd.com_addr1,ccd.com_addr2,
			ccd.customer_name,ccd.customer_position,ccd.customer_phone,ccd.customer_mobile,customer_mail
		from 
		buyingservice_commercial_area ca
		left join common_user cu on (ca.ca_charger_ix = cu.code)
		left join common_member_detail cmd on (cu.code = cmd.code)
		left join common_company_detail ccd on (cu.company_id = ccd.company_id)
		where ca.ca_ix='$ca_ix' ";
	$db->query($sql);
	$db->fetch();
	$ca=$db->dt;
	$_act="update";
}else{
	$_act="insert";
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <col width='20%'>
	  <col width='*'>
	  <tr >
		<td align='left' colspan=6> ".GetTitleNavigation("상권관리", "상권등록 ")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 10px;'><img src='../image/title_head.gif' align=absmiddle> <b class=blk><u>$board_name</u> 상권 등록/수정</b></div>")."</td>
	  </tr>
	  </table>
	  <table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' class='input_table_box'>
	  <col width='15%'>
	  <col width='35%'>
	  <col width='15%'>
	  <col width='35%'>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상권 등록/수정일 </td>
	    <td class='input_box_item'>
	    	".($_act=="update" ? $ca[regdate]." / ".$ca[editdate] : "")."
	    </td>	 
	    <td class='input_box_title'> 상권국가 <img src='".$required3_path."'></td>
		<td class='input_box_item'>
			".getCommercialCountry($ca[ca_country])."
		</td>
	  </tr>
	   <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사이트 URL <img src='".$required3_path."'></td>
	    <td class='input_box_item' colspan='3'>
	    	<input type=text class='textbox' name='ca_sub_domain' value='".$ca[ca_sub_domain]."' size='10' validation='true' title='서브도메인' > .ddm3.co.kr 가능한/불가능한 서브도메인입니다. 링크 URL 설정 
			<input type=text class='textbox' name='ca_url' value='".$ca[ca_url]."' size='30' validation='true' title='링크 URL' >
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상권명(한/중/영) <img src='".$required3_path."'></td>
	    <td class='input_box_item' colspan='3'>
	    	한국어 <input type=text class='textbox' name='ca_name_korea' value='".$ca[ca_name_korea]."' size='20' validation='true' title='한국어' > &nbsp;&nbsp; 중국어 <input type=text class='textbox' name='ca_name_chinese' value='".$ca[ca_name_chinese]."' size='20' validation='true' title='중국어' > &nbsp;&nbsp; 영어
			<input type=text class='textbox' name='ca_name_english' value='".$ca[ca_name_english]."' size='20' validation='true' title='영어' >
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상권코드 <img src='".$required3_path."'></td>
	    <td class='input_box_item'>
	    	<input type=text class='textbox' name='ca_code' value='".$ca[ca_code]."' validation='true' title='상권코드' size='3' ".( $_act=="update" ?"style='background-color:#F2F2F2;' readonly" : "")." > * 2자리
	    </td>
	    <td class='input_box_title'> 상권 권한자 <img src='".$required3_path."'></td>
		<td class='input_box_item'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td><input type=hidden name='ca_charger_ix' id='mem_ix' value='".$ca[ca_charger_ix]."' validation=true title='상권 권한자' style='width:100px;'></td>
					<td><input type=text class='ca_change_code textbox' id='buying_mem_name' value='".$ca[name]."' style='width:100px;' onclick=\"PoPWindow('./member_search.php?mode=getcominfo',600,380,'member_search')\"  readonly></td>
					<td style='padding-left:5px;'><img src='../v3/images/".$admininfo["language"]."/btn_member_search.gif' align=absmiddle onclick=\"PoPWindow('./member_search.php?mode=getcominfo',600,380,'member_search')\" class='ca_change_code'  style='cursor:pointer;'></td>
				</tr>
			</table>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 상권 권한 일 <img src='".$required3_path."'></td>
		<td class='input_box_item'>
			<input type='text' name='ca_start_date' class='textbox' value='".$ca[ca_start_date]."' style='height:20px;width:70px;text-align:center;' id='start_datepicker' validation='true' title='상권 권한 일'> ~ <input type='text' name='ca_end_date' class='textbox' value='".$ca[ca_end_date]."' style='height:20px;width:70px;text-align:center;' id='end_datepicker' validation='true' title='상권 권한 일'> ".($_act=="update" ? "&nbsp;&nbsp; 잔여일 ".round((mktime(0,0,0,substr($ca[ca_end_date],5,2),substr($ca[ca_end_date],8,2),substr($ca[ca_end_date],0,4))-time())/86400)."일":"")."
		</td>
	    <td class='input_box_title'> 인센티브 </td>
	    <td class='input_box_item'>
	    	<input type=text class='textbox number' name='ca_incentive' value='".$ca[ca_incentive]."' validation='false' title='인센티브' size='3'> % &nbsp;&nbsp; / 수익 * 인센티브
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 수수료지급일 </td>
		<td class='input_box_item'>
			매월 <input type=text class='textbox number' name='ca_give_day' value='".$ca[ca_give_day]."' validation='false' title='수수료지급일' size='3'> 일 &nbsp;&nbsp; * 전월 말일까지의 정산된 금액만 정산처리합니다.
		</td>
	    <td class='input_box_title'> 사용유무 <img src='".$required3_path."'></td>
	    <td class='input_box_item'>
	    	<input type=radio name='disp' id='disp_1' value='1' ".($ca[disp]=="1" || $ca[disp]=="" ? "checked" :"")."><label for='disp_1'>사용</label>
	    	<input type=radio name='disp' id='disp_0' value='0' ".($ca[disp]=="0" ? "checked" :"")."><label for='disp_0'>미사용</label>
	    </td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 비고(상세내역) </td>
		<td class='input_box_item' colspan='3'>
			<input type=text class='textbox' name='ca_msg' value='".$ca[ca_msg]."' validation='false' title='비고' style='width:90%' >
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사업자관련 </td>
		<td class='input_box_item' colspan='3'>
			대표자명 <input type=text class='textbox' id='com_ceo' value='".$ca[com_ceo]."' size='10' style='background-color:#F2F2F2;' readonly> &nbsp;
			사업자번호 <input type=text class='textbox' id='com_number' value='".$ca[com_number]."' size='12' style='background-color:#F2F2F2;' readonly> &nbsp;
			대표전화번호 <input type=text class='textbox' id='com_phone' value='".$ca[com_phone]."' size='12' style='background-color:#F2F2F2;' readonly> &nbsp;
			팩스 <input type=text class='textbox' id='com_fax' value='".$ca[com_fax]."' size='12' style='background-color:#F2F2F2;' readonly>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 사업장주소 </td>
		<td class='input_box_item' colspan='3'>
			우편번호 <input type=text class='textbox' id='com_zip' value='".$ca[com_zip]."' size='10' style='background-color:#F2F2F2;' readonly> &nbsp;
			주소 <input type=text class='textbox' id='com_addr' value='".$ca[com_addr1]." ".$ca[com_addr2]."' size='50' style='background-color:#F2F2F2;' readonly>
		</td>
	  </tr>
	  <tr bgcolor=#ffffff >
	    <td class='input_box_title'> 담당자 </td>
		<td class='input_box_item' colspan='3'>
			성명 <input type=text class='textbox' id='customer_name' value='".$ca[customer_name]."' size='6' style='background-color:#F2F2F2;' readonly> &nbsp;
			직책 <input type=text class='textbox' id='customer_position' value='".$ca[customer_position]."' size='7' style='background-color:#F2F2F2;' readonly> &nbsp;
			연락처 <input type=text class='textbox' id='customer_phone' value='".$ca[customer_phone]."' size='12' style='background-color:#F2F2F2;' readonly> &nbsp;
			핸드폰 <input type=text class='textbox' id='customer_mobile' value='".$ca[customer_mobile]."' size='12' style='background-color:#F2F2F2;' readonly> &nbsp;
			이메일 <input type=text class='textbox' id='customer_mail' value='".$ca[customer_mail]."' size='20' style='background-color:#F2F2F2;' readonly>
		</td>
	  </tr>
	  </table>";

$ContentsDesc01 = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');


$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";



$Contents = "<form name='manufacturer_form' action='commercial_area.act.php' method='post' onsubmit='return CheckFormValue(this)' target=act>
<input name='mmode' type='hidden' value='$mmode'>
<input name='act' type='hidden' value='$_act'>
<input name='ca_ix' type='hidden' value='$ca_ix'>";
$Contents = $Contents."<table width='100%' border=0 cellpadding='0' cellspacing='0'>";
$Contents = $Contents."<tr><td width='100%'>";
$Contents = $Contents.$Contents01;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>".$ButtonString."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table>";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

$help_text = HelpBox("<div style='padding-top:6px;'>상권등록</div>", $help_text);
$Contents = $Contents.$help_text;

 $Script = "
 <script language='javascript'>

$(document).ready(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

});

function get_com_info (company_id){
	
	$.ajax({ 
		type: 'POST', 
		data: {'act': 'com_json', 'company_id':company_id},
		url: './commercial_area.act.php',  
		dataType: 'json', 
		async: false, 
		beforeSend: function(){ 

		},  
		success: function(com_data){ 

			$('#com_ceo').val(com_data[0].com_ceo);
			$('#com_number').val(com_data[0].com_number);
			$('#com_phone').val(com_data[0].com_phone);
			$('#com_fax').val(com_data[0].com_fax);
			$('#com_zip').val(com_data[0].com_zip);
			$('#com_addr').val(com_data[0].com_addr1 + ' ' + com_data[0].com_addr2);
			$('#customer_name').val(com_data[0].customer_name);
			$('#customer_position').val(com_data[0].customer_position);
			$('#customer_phone').val(com_data[0].customer_phone);
			$('#customer_mobile').val(com_data[0].customer_mobile);
			$('#customer_mail').val(com_data[0].customer_mail);

		}
	}); 

}

 </script>
 ";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = buyingservice_menu();
	$P->Navigation = "상권관리 > 상권등록";
	$P->title = "상권등록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = buyingservice_menu();
	$P->Navigation = "상권관리 > 상권등록";
	$P->title = "상권등록";
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}


/*

CREATE TABLE `shop_address_group` (
  `ca_ix` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `parent_ca_ix` int(4) unsigned DEFAULT NULL,
  `ca_name` varchar(20) DEFAULT NULL,
  `depth` int(2) unsigned DEFAULT '1',
  `disp` char(1) DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ca_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
*/
?>