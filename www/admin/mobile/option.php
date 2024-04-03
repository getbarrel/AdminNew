<?
$script_time[start] = time();
include("../class/layout.class");
include "../class/Snoopy.class.php";
$script_time[start] = time();
//print_r($_SESSION);

$db = new Database; 

$Script = "
<script language='javascript' src='shop_main_v3_calender.js'></script>
<style type='text/css'>
.checkbox_image_tick07{width:26px;}
</style>
<script language='JavaScript'>

var ck_adminSaveAUTO;

function loadeautocookie(val){

	if(val=='Y'){
		$('#auto_login_check').attr('checked',true);
		$('#tick_img_auto_login_check').attr('src','./images/checkbox_on.png');
		ck_adminSaveAUTO = 'Y';
	}else{
		ck_adminSaveAUTO = 'N';
	}
}

function changautocookie(){
	var act_name;

	if(ck_adminSaveAUTO=='Y'){
		ck_adminSaveAUTO='N';
		act_name = 'no_auto_login';
	}else{
		ck_adminSaveAUTO='Y';
		act_name = 'yes_auto_login';
	}
";

if($_SESSION["admininfo"]["action_agent"] == "app"){
	//app은 현제 접속해 있는 쿠키를 바꾸고 리플래시 해야함
	$Script .= "
		//$('#app_mallstory').attr('src','./auto_login_cookie.php?act='+act_name);
		document.location.href='./auto_login_cookie.php?act='+act_name;
	";
}else{
	$Script .= "
		$('#app_mallstory').attr('src','http://app.mallstory.com/auto_login_cookie.php?act='+act_name);
	";
}

$Script .= "
}


$(document).ready(function(){
	$('input.select_checkbox07').imageTick({
		tick_image_path: './images/checkbox_on.png',
		no_tick_image_path: './images/checkbox.png',
		image_tick_class: 'checkbox_image_tick07',
		act_value : 'changautocookie()'
	});
});

</script>
";

$Contents01 = "
<iframe name='app_mallstory' id='app_mallstory' src='".($_SESSION["admininfo"]["action_agent"] == "app" ? "." : "http://app.mallstory.com")."/auto_login_cookie.php?act=check&domain=".$_SERVER["HTTP_HOST"]."' style='display:none;' ></iframe>
<table cellpadding='0' cellspacing='0' border='0' width='100%' class='page_option'>
	<tr>
		<td colspan='2' class='option_title'>설정</td>
	</tr>
	<tr>
		<th colspan='2'><img src='./images/bg_login.png' width='12' style='position:relative;top:2px;' />&nbsp;로그인</th>
	</tr>
	<tr>
		<td>로그인 정보</td>
		<td align='right'>".$_SESSION["admininfo"]["charger_id"]."<img src='./images/bg_lt.png' width='10' style='position:relative;top:3px;margin-left:20px;' /></td>
	</tr>
	<tr><!--tr class='other_border'-->
		<td>자동 로그인</td>
		<td align='right'><input type='checkbox' name='auto_login_check' id='auto_login_check' class='select_checkbox07' /></td>
	</tr>
	<!--tr>
		<th colspan='2'><img src='./images/bg_alarm.png' width='18'  style='position:relative;top:2px;' />&nbsp;알람설정</th>
	</tr>
	<tr>
		<td>주문결제</td>
		<td align='right'><input type='checkbox' name='option_check02' id='option_check02' class='select_checkbox07' /></td>
	</tr>
	<tr>
		<td>상품문의</td>
		<td align='right'><input type='checkbox' name='option_check03' id='option_check03' class='select_checkbox07' /></td>
	</tr>
	<tr>
		<td>1:1문의</td>
		<td align='right'><input type='checkbox' name='option_check04' id='option_check04' class='select_checkbox07' /></td>
	</tr>
	<tr>
		<td>회원가입</td>
		<td align='right'><input type='checkbox' name='option_check05' id='option_check05' class='select_checkbox07' /></td>
	</tr>
	<tr><td colspan='2'></td></tr-->
</table>

";



$Contents = $Contents01;




	$P = new MobileLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = store_menu();
	$P->strContents = $Contents;
	$P->Navigation = "상품리스트";
	$P->TitleBool = false;
	$P->ServiceInfoBool = true;
	echo $P->PrintLayOut();



$script_time[end] = time();
if($admininfo[charger_id] == "forbiz"){
	//print_r($script_time);
}

?>
