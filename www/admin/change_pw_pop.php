<?
include("./class/layout.class");
//print_r($_SESSION);
if($act == ""){
	//include("$DOCUMENT_ROOT/class/mysql.class");
	//$db = new MySQL;
	
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
.title_box_m02 p{padding:5px 0;}
.title_box_m02 ul li{float:left; padding:3px 0;}
.title_box_m02 ul {clear:both;}
.title_box_m02 ul div{text-align:center; padding:10px 0;}
</style>
</head>

<body>
<div class="modal_pop">
	<h2 class="title01">비밀번호 변경</h2>
	<div class="modal_pop_line">
		<div class="title_box_m01">
			<img src="/admin/images/popup_01_01.png" alt="" /><br />
			<strong>
				관리자 계정(<?= $_SESSION["admininfo"][charger_id]?>)의 비밀번호 유효기간이 <?= $_SESSION['privacy_config']['change_admin_pw_day']?> 일 이 지나 <br/> 비밀번호 변경 이후 서비스 이용 가능합니다.
			</strong>
		</div>
		<form name="change_pw_frm" method="post" action="../change_pw_pop.php" target='act'>
		<input type="hidden" name="charger_ix" id="charger_ix" value="<?= $charger_ix ?>" />
		<input type="hidden" name="act" value="update" />
		<div class="title_box_m02">
			<h3 class="title02"><img src="/admin/images/popup_01_02.png" alt="" /> 비밀번호 변경 </h3>
			<ul>
				<li style="width:130px;">
					<span>현재 비밀번호 : </span>
				</li>
				<li>	
					<input type='password' class='textbox' name='admin_pw' id='admin_pw' value=''  />
				</li>
			</ul>
			<ul>
				<li style="width:130px;">
					<span>새로운 비밀번호 : </span>
				</li>
				<li style="background:color:red;">	
					<input type='password' class='textbox' name='pw' id='compare_a' title="새로운 비밀번호" pwtype='true' value=''  /> 
				</li>
				<li id="pwconcheck_text" style="padding-left:130px;"></li>
			</ul>
			<ul>
				<li style="width:130px;">
					<span>새로운 비밀번호 확인 : </span>
				</li>
				<li>	
					<input type='password' class='textbox' name='pw_confirm' compare='true' title="새로운 비밀번호 확인" id='compare_b' pwtype='true' value=''  /> 
				</li>
				<li id="pwconcheck_textRe" style="padding-left:130px;"></li>
			</ul>
			<input type="hidden" name="pw_check_value" id="pw_check_value" value="N" />
			
			<ul>
				<div>
					<img src='../images/korea/bts_ok.gif' style='vertical-align:middle; cursor:pointer;' onclick="change_password(document.change_pw_frm)">
					<img src='../images/korea/btn_close.gif' style='vertical-align:middle; cursor:pointer;' onclick="agree_cancel()">
				</div>
			</ul>
		</div>
		</form>
	</div>
</div>


</body>
</html>

<script language='javascript'>
$(document).ready(function(){
	var edit_form = document.change_pw_frm;
	$('#compare_a').keyup(function(){

		//var PT_pwtype = /^(?=([a-zA-Z]+[0-9]+[a-zA-Z0-9]*|[0-9]+[a-zA-Z]+[a-zA-Z0-9]*)$).{10,20}/;
		var PT_pwtype = /^(?=.*[a-zA-Z])(?=.*[!@#$%^*+=-])(?=.*[0-9]).{8,25}$/;
		if(!PT_pwtype.test(edit_form.pw.value)) {
			$('#pwconcheck_text').css('color','#FF5A00').html('비밀번호는 10 자리 이상의 영문과 숫자, 특수문자 조합이어야만 합니다.');
			$('#pw_check_value').val('N');
			return false;
		} else {
			if(edit_form.pw.value.length < 10 ){
				$('#pwconcheck_text').css('color','#FF5A00').html('비밀번호는 10 자리 이상의 영문과 숫자, 특수문자 조합이어야만 합니다.');
				$('#pw_check_value').val('N');
				return false;
			}
			$('#pwconcheck_text').css('color','#00B050').html('이 비밀번호는 사용할 수 있습니다.');
		}	
		
		compare_b_check();
	});

	$('#compare_b').keyup(function(){
	

		if(edit_form.pw.value.length>0 && edit_form.pw_confirm.value.length>0) {
			if(edit_form.pw.value != edit_form.pw_confirm.value){
				$('#pwconcheck_textRe').css('color','#FF5A00').html('비밀번호가 일치하지 않습니다.');
				$('#pw_check_value').val('N');
				return false;
			}
			$('#pwconcheck_textRe').css('color','#00B050').html('비밀번호가 일치합니다.');
			$('#pw_check_value').val('Y');
		} else {
			$('#pwconcheck_textRe').html('');
			$('#pw_check_value').val('N');
			return false;
		}
	});
});

function compare_b_check(){
	var edit_form=document.change_pw_frm;

	if(edit_form.pw.value.length>0 && edit_form.pw_confirm.value.length>0) {
		if(edit_form.pw.value != edit_form.pw_confirm.value){
			$('#pwconcheck_textRe').css('color','#FF5A00').html('비밀번호가 일치하지 않습니다.');
			$('#pw_check_value').val('N');
			return false;
		}
		$('#pwconcheck_textRe').css('color','#00B050').html('비밀번호가 일치합니다.');
		$('#pw_check_value').val('Y');
	} else {
		$('#pwconcheck_textRe').html('');
		$('#pw_check_value').val('N');
		return false;
	}
}	
function change_password(frm){

	if(!frm.admin_pw.value){
		alert('현재 비밀번호를 입력해주세요');
		return false;
	}
	if(!frm.pw.value){
		alert('새로운 비밀번호를 입력해주세요');
		return false;
	}
	if(!frm.pw_confirm.value){
		alert('새로운 비밀번호 확인을 입력해주세요');
		return false;
	}

	if(frm.admin_pw.value == frm.pw.value){
		alert('현재 비밀번호와 새로운 비밀번호는 동일할 수 없습니다.');
		return false;
	}

	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}

	frm.submit();
	//alert(frm.admin_pw.value);
	/*
	if($('#access_area').css('display') == 'none'){
		alert('SMS발송을 통해 인증번호를 부여 받으시기 바랍니다.');
	}else{
		var access_code_input = $('#access_code_input').val();
		
		if(access_code_input.length != 6){
			alert('인증번호를 정확히 입력해 주세요.');
		}else{
			document.change_pw_frm.submit();
		}
	}*/
}


function agree_cancel(){
	parent.location.href='../admin.php?act=logout';
}
</script>
<?
}

if($act == 'update'){
	
	//include("$DOCUMENT_ROOT/class/mysql.class");
	//$db = new MySQL;
	
	$admin_pw = trim($_POST['admin_pw']);
	$pass = trim($_POST['pw']);		//비밀번호
	$charger_ix = $_POST['charger_ix'];

	$sql = "SELECT * FROM ".TBL_COMMON_USER." where code = '".$charger_ix."' and pw = '".hash("sha256", md5($admin_pw))."'";
	$db->query($sql);
	
	if($db->total){




        //  ig 최종 사용한 패스워드 재사용 금지(최근2건)
            $ig_change_pw_history_2chk_SQL = "
                    SELECT
                        *
                    FROM
                        ig_change_pw_history
                    WHERE
                        code = '".$charger_ix."'
                        AND ch_type = '0'
                    ORDER BY
                        h_pw_ix DESC
                    LIMIT 0, 2
                ";
            $db->query($ig_change_pw_history_2chk_SQL);


            for ($ig = 0; $ig < $db->total; $ig++)
            {
                $db->fetch($ig);

					
				if(trim($db->dt[pw_data]) == trim(hash("sha256", md5($pass)))) {
					echo "<script>alert('최근 사용한 비밀번호 입니다. 다른 비밀번호를 사용해주세요.')</script>";
					exit;
				}

            }

        //  //ig 최종 사용한 패스워드 재사용 금지(최근2건)




		$sql = "update ".TBL_COMMON_USER." set  pw = '".hash("sha256", md5($pass))."',change_pw_date = NOW()  where code = '".$charger_ix."' ";	
		$db->query($sql);
		
		$_SESSION["admininfo"][change_access_pw] = false;


					$ig_change_pw_history_SQL = "
						INSERT INTO
							ig_change_pw_history
						SET
							code = '".$charger_ix."',
							pw_data = '".hash("sha256", md5($pass))."',
							ch_type = '0',
							regDt = '".date("Y-m-d H:i:s")."'
						";
					$db->query($ig_change_pw_history_SQL);

					$_SESSION["admininfo"][ch_type] = "0";

		echo "<script>alert('비밀번호 변경이 완료 되었습니다. 서비스 이용 가능합니다.');top.window.location.reload();</script>";
		exit;
	}else{
		echo "<script>alert('현재 비밀번호가 틀립니다. 다시 확인 후 변경 바랍니다.')</script>";
		exit;
	}
	
	//echo $certify_number;

}

?>