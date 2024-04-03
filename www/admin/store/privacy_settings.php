<?
include("../class/layout.class");

if($admininfo[admin_level] < 9){
	header("Location:../admin.php");
}

$db = new Database;

$sql = "SHOW TABLES LIKE 'shop_mall_privacy_setting'";
$db->query($sql);
if(!$db->total){
	$sql="CREATE TABLE `shop_mall_privacy_setting` (
		  `mall_ix` varchar(32) NOT NULL COMMENT '쇼핑몰키',
		  `config_name` varchar(100) NOT NULL DEFAULT '' COMMENT '변수이름',
		  `config_value` varchar(255) DEFAULT NULL COMMENT '변수값',
		  PRIMARY KEY (`mall_ix`,`config_name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='쇼핑몰 개인정보 설정 정보';
	";
	$db->query($sql);
}

$db->query("SELECT * FROM shop_mall_privacy_setting where mall_ix = '".$admininfo[mall_ix]."'   ");
if($db->total){
	for($i=0; $i < $db->total;$i++){
	$db->fetch($i);
	$privacy_config[$db->dt[config_name]] = $db->dt[config_value];
	}
}

//디폴드값 설정
if(empty($privacy_config[sleep_user_yn]))	$privacy_config[sleep_user_yn] = "Y";
if(empty($privacy_config[sleep_user_mailing]))	$privacy_config[sleep_user_mailing] = "Y";
if(empty($privacy_config[change_pw_info]))	$privacy_config[change_pw_info] = "Y";

$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
	<col width='200px'>
	<col width='*'>
	<col width='*'>
		<tr>
			<td align='left' colspan='3'> ".GetTitleNavigation("개인정보 관리설정", "상점관리 > 개인정보 관리설정  ")."</td>
		</tr>
</table>

<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:20px;' >
<col width='200px'>
<col width='*' />
	<tr>
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>휴면회원 관리</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:0px;' class='input_table_box'>
<col width='200px' />
<col width='*' />
<col width='200px' />
<col width='*' />
	<tr bgcolor=#ffffff height=40>
		<td class='input_box_title'> <b>휴면계정 사용여부 설정</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='sleep_user_yn' id='sleep_user_yn_y' value='Y' ".CompareReturnValue("Y",$privacy_config[sleep_user_yn],"checked")."><label for='sleep_user_yn_y'>사용함</label>
		   <input type=radio name='sleep_user_yn' id='sleep_user_yn_n' value='N' ".CompareReturnValue("N",$privacy_config[sleep_user_yn],"checked")."><label for='sleep_user_yn_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'></span>
		   <input type='hidden' name='sleep_user_release' value='/member/sleepAccountRelease'/>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 class='sleep_setting' ".($privacy_config[sleep_user_yn] == 'Y' ? "" : "style='display:none'").">
		<td class='input_box_title'> <b>휴면상태 적용 날짜 </b></td>
		<td class='input_box_item' colspan=3>
			<input type=text class='textbox' name='sleep_date' value='".$privacy_config[sleep_date]."' style='width:5%;'>
			<span style='margin-left:10px;' class='small blu'>년 단위로 입력 가능</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 class='sleep_setting' ".($privacy_config[sleep_user_yn] == 'Y' ? "" : "style='display:none'").">
		<td class='input_box_title'> <b>휴면 알림 메일 설정</b></td>
		<td class='input_box_item' colspan=3>
		   <input type=radio name='sleep_user_mailing' id='sleep_user_mailing_y' value='Y' ".CompareReturnValue("Y",$privacy_config[sleep_user_mailing],"checked")."><label for='sleep_user_mailing_y'>사용함</label>
		   <input type=radio name='sleep_user_mailing' id='sleep_user_mailing_n' value='N' ".CompareReturnValue("N",$privacy_config[sleep_user_mailing],"checked")."><label for='sleep_user_mailing_n'>사용안함</label> <span style='margin-left:10px;' class='small blu'>휴면전환 예정 및 휴면 확정 시 메일이 발송됩니다.</span>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=40 class='sleep_mail_setting' ".($privacy_config[sleep_user_mailing] == 'Y' && $privacy_config[sleep_user_yn] =='Y' ? "" : "style='display:none'").">
		<td class='input_box_title'> <b>휴면전환 안내메일 발송일 지정</b></td>
		<td class='input_box_item' colspan=3>
		    <select name='sleep_user_mailing_day'>";
                $sleep_user_mailing_days = array(5,10,15,20,25,30,60);
                foreach($sleep_user_mailing_days as $day){
                    $Contents01 .= "<option value='".$day."' ".($privacy_config[sleep_user_mailing_day]==$day ? "selected" : "")." >".$day."</option>";
                }
$Contents01 .= "
            </select> 일전 발송 <span style='margin-left:10px;' class='small blu'>휴면회원으로 변경되기 전 알림 메일</span>
		</td>
	</tr>
	";

$Contents01 .= "
</table>";
$Contents02 ="
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:20px;' >
<col width='200px'>
<col width='*' />
	<tr>
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>관리자접근허용설정</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:0px;' class='input_table_box'>
<col width='200px' />
<col width='*' />
<col width='200px' />
<col width='*' />
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>관리자접근허용설정</b></td>
		<td class='input_box_item' style='padding:10px;' colspan='3'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td>
						<input type=radio name='admin_access_yn' id='admin_access_y' value='Y' ".CompareReturnValue("Y",$privacy_config[admin_access_yn],"checked")."><label for='admin_access_y'>사용함</label>
						<input type=radio name='admin_access_yn' id='admin_access_n' value='N' ".CompareReturnValue("N",$privacy_config[admin_access_yn],"checked")."><label for='admin_access_n'>사용안함</label><span style='margin-left:10px;' class='small blu'>등록되지 않은 IP 대역으로 관리자 페이지 접근 시도 할 경우 관리자의 휴대폰 인증을 진행 해야 함</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff class='admin_access_input' ".($privacy_config[admin_access_yn] == 'Y' ? "" : "style='display:none;'").">
		<td class='input_box_title'> <b>관리자접근허용IP입력</b></td>
		<td class='input_box_item' style='padding:10px;' colspan='3'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td class='input_box_item'>
						<textarea type=text class='textbox' name='admin_access_ip' style='width:98%;height:85px;padding:2px;'>".$privacy_config[admin_access_ip]."</textarea>
					</td>
				</tr>
				<tr><td>ex) 221.151.188.11,XXX.XXX.XXX.XXX (공유기IP 대역이 아닌 통신사로 부여받은 고유 IP) IP확인 방법 <a href='http://www.ipconfig.co.kr' target=_blank ><span class='blu'>IP확인</span></a> 접속 후 확인</td></tr>
			</table>
		</td>
	</tr>
</table>
";

$Contents03 ="
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:20px;' >
<col width='200px'>
<col width='*' />
	<tr>
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>비밀번호 관리설정</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:0px;' class='input_table_box'>
<col width='200px' />
<col width='*' />
<col width='200px' />
<col width='*' />
	<tr bgcolor=#ffffff >
		<td class='input_box_title'>
		<b>비밀번호 변경안내 설정 </b>
		</td>
		<td class='input_box_item' style='padding:5px;' colspan='3'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap>
						<input type=radio name='change_pw_info' id='change_pw_info_y' value='Y' ".CompareReturnValue("Y",$privacy_config[change_pw_info],"checked")."><label for='change_pw_info_y'>사용함</label>
						<input type=radio name='change_pw_info' id='change_pw_info_n' value='N' ".CompareReturnValue("N",$privacy_config[change_pw_info],"checked")."><label for='change_pw_info_n'>사용안함</label><span style='margin-left:10px;' class='small blu'>비밀번호 변경안내 설정 시 설정된 기간이 지날경우 비밀번호 변경안내 화면 노출</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff class='change_pw_info' ".($privacy_config[change_pw_info] == 'Y' ? "" : "style='display:none;'").">
		<td class='input_box_title'>
		<b>비밀번호 변경기간 </b>
		</td>
		<td class='input_box_item' style='padding:5px;' colspan='3'>
			<table cellpadding=0 cellspacing=0> 
				<tr>
					<td nowrap><input type=text class='textbox' name='change_pw_day'  value='".$privacy_config[change_pw_day]."' size=5 title='비밀번호 변경 제한일자'> 접속기록이 해당 일 이상 지난 회원에 대한 설정.</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff class='change_pw_info' ".($privacy_config[change_pw_info] == 'Y' ? "" : "style='display:none;'").">
		<td class='input_box_title'>
		<b>비밀번호 변경(연장)기간</b>
		</td>
		<td class='input_box_item' style='padding:5px;' colspan='3'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap><input type=text class='textbox' name='change_pw_continue_day'  value='".$privacy_config[change_pw_continue_day]."' size=5 title='비밀번호 변경(연장)기간'> 비밀번호를 변경하지 않고 연장 시 허용하는 일자</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff class='change_pw_info' ".($privacy_config[change_pw_info] == 'Y' ? "" : "style='display:none;'").">
		<td class='input_box_title'>
		<b>비밀번호 변경기간 [관리자] </b>
		</td>
		<td class='input_box_item' style='padding:5px;' colspan='3'>
			<table cellpadding=0 cellspacing=0> 
				<tr>
					<td nowrap><input type=text class='textbox' name='change_admin_pw_day'  value='".$privacy_config[change_admin_pw_day]."' size=5 title='비밀번호 변경 제한일자'> 접속기록이 해당 일 이상 지난 셀러 및 관리자에 대한 설정. </td>
				</tr>
			</table>
		</td>
	</tr>
</table>
";

$Contents05 ="
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-top:20px;' >
<col width='200px'>
<col width='*' />
	<tr>
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align=absmiddle> <b class='blk'>개인 정보 파기 설정</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=1 border='0' align='left' style='table-layout:fixed;margin-bottom:0px;' class='input_table_box'>
<col width='200px' />
<col width='*' />
<col width='200px' />
<col width='*' />
	<tr bgcolor=#ffffff >
		<td class='input_box_title'>
		<b>탈퇴시 주문 개인정보 처리 설정</b>
		</td>
		<td class='input_box_item' style='padding:5px;' colspan='3'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap>
						<input type=radio name='secede_member_order_destruction_yn' id='secede_member_order_destruction_y' value='Y' ".CompareReturnValue("Y",$privacy_config[secede_member_order_destruction_yn],"checked")."><label for='secede_member_order_destruction_y'>사용함</label>
						<input type=radio name='secede_member_order_destruction_yn' id='secede_member_order_destruction_n' value='N' ".CompareReturnValue("N",$privacy_config[secede_member_order_destruction_yn],"checked")."><label for='secede_member_order_destruction_n'>사용안함</label><span style='margin-left:10px;' class='small blu'>고객이 탈퇴시 주문의 개인 정보(주문자, 배송지주소)는 별도로 관리되고 관리자에서 개인 정보는 확인할 수가 없습니다.</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'>
		<b>목적 달성된 개인정보에 대한 파기 정책-주문 상담 내역</b>
		</td>
		<td class='input_box_item' style='padding:5px;' colspan='3'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap>
						<input type=radio name='achievement_purpose_destruction_order_memo_yn' id='achievement_purpose_destruction_order_memo_y' value='Y' ".CompareReturnValue("Y",$privacy_config[achievement_purpose_destruction_order_memo_yn],"checked")."><label for='achievement_purpose_destruction_order_memo_y'>사용함</label>
						<input type=radio name='achievement_purpose_destruction_order_memo_yn' id='achievement_purpose_destruction_order_memo_n' value='N' ".CompareReturnValue("N",$privacy_config[achievement_purpose_destruction_order_memo_yn],"checked")."><label for='achievement_purpose_destruction_order_memo_n'>사용안함</label>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff class='achievement_purpose_destruction_order_memo' ".($privacy_config[achievement_purpose_destruction_order_memo_yn] == 'Y' ? "" : "style='display:none;'").">
		<td class='input_box_title'>
		<b>주문 상담 내역 파기 기간</b>
		</td>
		<td class='input_box_item' style='padding:5px;' colspan='3'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap><input type=text class='textbox' name='achievement_purpose_destruction_order_memo_year'  value='".$privacy_config[achievement_purpose_destruction_order_memo_year]."' size=5 title='주문 상담 내역 유지기간'>년 이상 지난 경우 파기</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'>
		<b>목적 달성된 개인정보에 대한 파기 정책-주문 내역</b>
		</td>
		<td class='input_box_item' style='padding:5px;' colspan='3'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap>
						<input type=radio name='achievement_purpose_destruction_order_yn' id='achievement_purpose_destruction_order_y' value='Y' ".CompareReturnValue("Y",$privacy_config[achievement_purpose_destruction_order_yn],"checked")."><label for='achievement_purpose_destruction_order_y'>사용함</label>
						<input type=radio name='achievement_purpose_destruction_order_yn' id='achievement_purpose_destruction_order_n' value='N' ".CompareReturnValue("N",$privacy_config[achievement_purpose_destruction_order_yn],"checked")."><label for='achievement_purpose_destruction_order_n'>사용안함</label>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff class='achievement_purpose_destruction_order' ".($privacy_config[achievement_purpose_destruction_order_yn] == 'Y' ? "" : "style='display:none;'").">
		<td class='input_box_title'>
		<b>주문 내역 파기 기간</b>
		</td>
		<td class='input_box_item' style='padding:5px;' colspan='3'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap><input type=text class='textbox' name='achievement_purpose_destruction_order_year'  value='".$privacy_config[achievement_purpose_destruction_order_year]."' size=5 title='주문 내역 유지기간'>년 이상 지난 경우 파기</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff class='achievement_purpose_destruction_order'>
		<td class='input_box_title'>
		<b>1:1 문의 내역 파기 기간</b>
		</td>
		<td class='input_box_item' style='padding:5px;' colspan='3'>
			<table cellpadding=0 cellspacing=0>
				<tr>
					<td nowrap><input type=text class='textbox' name='achievement_purpose_destruction_order_year_oneone'  value='".$privacy_config[achievement_purpose_destruction_order_year_oneone]."' size=5 title='주문 내역 유지기간'>년 이상 지난 경우 파기</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff height=70><td align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}


$Contents = "<table width='100%' height='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='privacy_settings.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>
		<input name='act' type='hidden' value='update'><input name='mall_ix' type='hidden' value='".$admininfo[mall_ix]."'>
		<input name='mall_div' type='hidden' value='".$db->dt[mall_div]."'>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr style='display:none;'><td>".$Contents02."</td></tr>";
$Contents = $Contents."<tr><td>".$Contents03."<br></td></tr>";
$Contents = $Contents."<tr><td>".$Contents05."<br></td></tr>";
$Contents = $Contents."<tr ><td height=10></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";

$Script = "<script language='javascript' src='basicinfo.js'></script>
<script language='javascript'>
$(function(){
	
    $('#sleep_user_mailing_y').click(function(){
		$('.sleep_mail_setting').show();
	})
	
    $('#sleep_user_mailing_n').click(function(){
		$('.sleep_mail_setting').hide();
	})
    
	$('#sleep_user_yn_y').click(function(){
		$('.sleep_setting').show();
		$('input[name=sleep_user_mailing]:checked').trigger('click');
	});
	
    $('#sleep_user_yn_n').click(function(){
		$('.sleep_setting').hide();
		$('.sleep_mail_setting').hide();
	});

	$('#admin_access_y').click(function(){
		$('.admin_access_input').show();
	})
	
    $('#admin_access_n').click(function(){
		$('.admin_access_input').hide();
	})
	
	$('#change_pw_info_y').click(function(){
		$('.change_pw_info').show();
	})
	
    $('#change_pw_info_n').click(function(){
		$('.change_pw_info').hide();
	})

	$('#temp_pw_info_y').click(function(){
		$('.temp_pw_info').show();
	})
	
    $('#temp_pw_info_n').click(function(){
		$('.temp_pw_info').hide();
	})
	
	$('#achievement_purpose_destruction_order_memo_y').click(function(){
		$('.achievement_purpose_destruction_order_memo').show();
	})
	
	$('#achievement_purpose_destruction_order_memo_n').click(function(){
		$('.achievement_purpose_destruction_order_memo').hide();
	})
	
	$('#achievement_purpose_destruction_order_y').click(function(){
		$('.achievement_purpose_destruction_order').show();
	})
	
	$('#achievement_purpose_destruction_order_n').click(function(){
		$('.achievement_purpose_destruction_order').hide();
	})
	
});
</script>

";

if($admininfo[mall_type] == "H"){
	$Contents = str_replace("쇼핑몰","사이트",$Contents);
}

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 개인정보 관리설정";
$P->title = "개인정보 관리설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>
