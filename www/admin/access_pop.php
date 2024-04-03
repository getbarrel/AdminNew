<?
include("./class/layout.class");
//print_r($_SESSION);
if($act == ""){
	//include("$DOCUMENT_ROOT/class/mysql.class");
	//$db = new MySQL;

	$sql = "select  AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
					AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail,
					AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs
			from ".TBL_COMMON_MEMBER_DETAIL." where code = '$charger_ix'";
	$db->query($sql);
	$db->fetch();
	$pcs = explode('-',$db->dt[pcs]);

	if(empty($db->dt[pcs])){
		$not_mobile = "Y";
	}else{
		$not_mobile = "N";
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
<head>
<title> title </title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<style type="text/css">
/* MASTER */
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,input,button,textarea,select,option
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
margin: 0;
padding: 0;
border: 0;
}
h1,h2,h3,h4,h5,h6{font-size:12px; text-align:left;}
body,input,button,textarea,select,option{font-size:12px; font-family:"돋움",Dotum,Arial,sans-serif; color:#202020;}
label{font-size:12px; vertical-align:middle;cursor:pointer;}
a{color:#202020;text-decoration:none}
a:hover{color:#000;text-decoration:none}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section {
display: block;
}
body {
line-height: 1;
}
ol, ul {
list-style: none;
}
blockquote, q {
quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
content: '';
content: none;
}
table {
border-collapse: collapse;
border-spacing: 0;
}
/* Common */
.input_text,textarea{*margin:-1px 0;padding-right:1px}
.input_check,.input_radio{width:13px;height:13px}
.blind,legend{display:block;overflow:hidden;position:absolute;top:-1000em;left:0}
.hidden{visibility:hidden;width:0;font-size:0;line-height:0;}
hr{display:none}
legend{*width:0}
:root legend{margin-top:-1px;font-size:0;line-height:0}
.dummy{*zoom:1;}
.table_fixed {table-layout:fixed;}

.modal_pop {width:500px; background:#fff; }
.modal_pop h2.title01 {width:100%; height:36px; line-height:36px; font-size:18px; font-weight:bold; color:#fff; background:#ee4949; text-align:center;}
.modal_pop .modal_pop_line {border:2px solid #ee4949;  border-top:0;}
.modal_pop .modal_pop_line .title_box_m01 {background:#f3f3f3; padding:20px 0 12px 0; text-align:center; line-height:250%;}
.modal_pop .modal_pop_line .title_box_m01 strong {font-size:14px; display:block; padding-top:15px; }

.modal_pop .modal_pop_line .title_box_m02 {padding:0 20px;}
.modal_pop .modal_pop_line h3.title02 {font-size:14px; color:#393c3f; padding:19px 0 10px 0;}
.modal_pop .bottom_title {background:#000; text-align:right; padding:5px 0; color:#fff;}
.modal_pop .modal_pop_line .title_box_m02 .bottom_line_box {background:url(admin/images/popup_01_04.png) repeat-x; width:100%; font-size:14px; line-height:160%; padding:20px 0; color:#e22a2a; letter-spacing:-1px;}
</style>
</head>

<body>
<div class="modal_pop">
	<h2 class="title01">허용된 접근 IP가 아닙니다.</h2>
	<div class="modal_pop_line">
		<div class="title_box_m01">
			<img src="/admin/images/popup_01_01.png" alt="" /><br />
			<strong>
				관리자 휴대폰 인증을 통해 서비스 이용가능합니다.
			</strong>
		</div>
		<form name="access_frm" method="post" action="../access_pop.php" target='act'>
		<input type="hidden" name="charger_ix" id="charger_ix" value="<?= $charger_ix ?>" />
		<input type="hidden" name="act" value="check_update" />
		<input type="hidden" id="mobile_check" value="<?= $not_mobile ?>" />
		<div class="title_box_m02">
			<h3 class="title02"><img src="/admin/images/popup_01_02.png" alt="" /> 휴대폰 인증 </h3>
			<p style="line-height:160%;">
				접속자명 : &nbsp;&nbsp;<input type='text' class='textbox' name='user_name' id='user_name' value='<?=$db->dt[name]?>' readonly />
			</p>
			<p style="line-height:300%;">
				휴대폰인증 : <input type='text' class='textbox' name='pcs1' value='<?=$pcs[0]?>' style='width:5%;' readonly />-
							<input type='text' class='textbox' name='pcs2' value='<?=$pcs[1]?>' style='width:7%;' readonly />-
							<input type='text' class='textbox' name='pcs3' value='<?=$pcs[2]?>' style='width:7%;' readonly />
							<input type='hidden' id='pcs' value='<?=$db->dt[pcs]?>' />
							<img src='/admin/images/send_sms.png' style='vertical-align:middle; cursor:pointer;' onclick="open_access_code('input_code')">
			</p>
			<p style="line-height:300%; display:none;" id='access_area'>
				인증번호 : &nbsp;&nbsp;<input type='text' class='textbox' name='access_code_input' id='access_code_input' value='' /> <span>입력시간</span>: <span class="countTimeMinute">0</span>분<span class="countTimeSecond">0</span>초
				
			</p>
			
			
			<p style="padding:20px 0 20px 0; text-align:center;">
			<img src='../images/korean/bts_ok.gif' style='vertical-align:middle; cursor:pointer;' onclick="check_access()">
			<img src='../images/korean/btn_close.gif' style='vertical-align:middle; cursor:pointer;' onclick="agree_cancel()">
			</p>
		</div>
		</form>
	</div>
</div>


</body>
</html>

<script language='javascript'>

function open_access_code(){
	if($('#mobile_check').val() == "Y" ){
		alert('전송가능한 휴대전화 번호가 존재하지 않습니다.');
		return false;
	}
	$.ajax({
		url : '../access_pop.php',
		type : 'POST',
		data : {
		act:'send_sms',
		pcs:$("#pcs").val(),
		charger_ix:$("#charger_ix").val(),
		user_name:$("#user_name").val(),
		},
		dataType: 'html',
		cache:true,
		error: function(data,error){// 실패시 실행함수 
			alert(error);
			//console.log(data)
		},
		success: function(transport){
			//alert(transport)
			console.log(transport)
			var access_ok = transport;
			if(transport == 'N'){
				alert('관리자에 등록된 휴대폰 번호와 인증받고자 하는 번호가 다릅니다.');
				return false;
			}else if(transport == 'Y'){
				$('#access_area').css('display','');
				//초기값
				var hour = 0;
				var minute = 3;
				var second = 0;
				
				// 초기화
				$(".countTimeHour").html(hour);
				$(".countTimeMinute").html(minute);
				$(".countTimeSecond").html(second);
				
				var timer = setInterval(function () {
						// 설정
						$(".countTimeHour").html(hour);
						$(".countTimeMinute").html(minute);
						$(".countTimeSecond").html(second);
						
						if(second == 0 && minute == 0 && hour == 0){
							$.ajax({
								url : '../access_pop.php',
								type : 'POST',
								data : {
								act:'return_code'
								},
								dataType: 'html',
								cache:true,
								error: function(data,error){// 실패시 실행함수 
									alert(error);
									//console.log(data)
								},
								success: function(transport){
									console.log(transport)
								}	
								
							});	
							alert('입력시간이 초과 되었습니다.');
							clearInterval(timer); /* 타이머 종료 */
							
						}else{
							second--;
							
							// 분처리
							if(second < 0){
								minute--;
								second = 59;
							}
							
						/*	//시간처리
							if(minute < 0){
								if(hour > 0){
									hour--;
									minute = 59;
								}
								
							}*/
						}
					}, 1000); /* millisecond 단위의 인터벌 */
			}else{
				alert('인증이 실패되었습니다. 다시 시도 바랍니다.');
				return false;
			}
			
		}	
		
	});	
	
	

}
function check_access(){
	
	if($('#access_area').css('display') == 'none'){
		alert('SMS발송을 통해 인증번호를 부여 받으시기 바랍니다.');
	}else{
		var access_code_input = $('#access_code_input').val();
		
		if(access_code_input.length != 6){
			alert('인증번호를 정확히 입력해 주세요.');
		}else{
			document.access_frm.submit();
		}
	}
}


function agree_cancel(){
	parent.location.href='../admin.php?act=logout';
}
</script>
<?
}
if($act == "check_update"){
	//session_start();
	//echo "<script type='text/javascript'>alert('".$access_code_input." 완료".$_SESSION['access_code']." 되었습니다.') </script>";
	if($access_code_input == $_SESSION['access_code']){
		$_SESSION['admininfo']['admin_access'] = false;
		$_SESSION['access_code'] = "";
		echo "<script type='text/javascript'>alert('휴대폰 인증이 완료 되었습니다.') </script>";
		echo "<script type='text/javascript'>parent.location.reload(true); </script>";
		exit;
	}else{
		echo "<script type='text/javascript'>alert('휴대폰 인증이 실패 되었습니다.') </script>";
		echo "<script type='text/javascript'>parent.location.reload(true); </script>";
		exit;
	}

	
	
	
}

if($act == 'send_sms'){
	
	//include("$DOCUMENT_ROOT/class/mysql.class");
	//$db = new MySQL;

	$sql = "select  AES_DECRYPT(UNHEX(name),'".$db->ase_encrypt_key."') as name,
					AES_DECRYPT(UNHEX(mail),'".$db->ase_encrypt_key."') as mail,
					AES_DECRYPT(UNHEX(pcs),'".$db->ase_encrypt_key."') as pcs
			from ".TBL_COMMON_MEMBER_DETAIL." where code = '".$charger_ix."'";
	$db->query($sql);
	$db->fetch();
	$real_pcs = str_replace('-','',$db->dt[pcs]);
	$pcs = str_replace('-','',$pcs);
	if($real_pcs != $pcs){
		echo "N";
		exit;
	}


	include_once($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
	$cominfo = getcominfo();
	
	$sdb = new MySQL;
	$s = new SMS();
	$s->send_phone = $cominfo[com_phone];
	$s->send_name = $cominfo[com_name];
	
	$s->dest_phone = str_replace('-','',$pcs);
	$s->dest_name = $user_name;
	// 6자리의 인증번호를 생성
	$certify_number = rand(100000, 999999);
	
	$sms_contents="$user_name 님 관리자 접속 인증번호 ".$certify_number." 입니다.";
	$s->msg_body =$sms_contents;
	
	$s->sendbyone($cominfo);

	// 생성된 인증번호를 세션에 저장함 
	// form 에서 넘어온 인증번호와 비교하여 같으면 글쓰기 허용함 
	$_SESSION['access_code'] = $certify_number;
	
	echo "Y";
	exit;
	//echo $certify_number;

}

if($act == 'return_code'){
	$_SESSION['access_code'] = "";
	//echo $_SESSION[access_code];
}
?>